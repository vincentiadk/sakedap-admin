<?php

namespace App\Http\Controllers\DigitalStorageHandover;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AcceptController extends Controller
{
    private $worksheetCategory;

    public function __construct()
    {
        $this->worksheetCategory = Main::COLLECTION_DIGITAL;
    }

    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'media' => QueryAPI::get("select * from collectionmedias where (isdelete = 0 or isdelete is null) and worksheet_id in (20,142)") ?? [],
                'content' => 'digital-storage-handover.accept',
                'plugins' => [
                    'datatable',
                    'daterangepicker',
                    'select2',
                ]
            ]
        ]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'e_collections.id', null, 'e_collections.received_by_name',
            'e_collections.description', 'collectionmedias.name',
            'e_collections.code', 'e_collections.received_at',
        ];

        $draw   = intval($request->draw   ?? 0);
        $start  = intval($request->start  ?? 0);
        $length = intval($request->length ?? 10);
        $search = strtoupper(trim($request->search['value'] ?? ''));

        $wsCat = str_replace("'", "''", $this->worksheetCategory);

        $baseJoins = "
            FROM e_collections
            JOIN collectionmedias ON collectionmedias.id = e_collections.collection_media_id
                AND (collectionmedias.isdelete = 0 OR collectionmedias.isdelete IS NULL)
            JOIN worksheets ON worksheets.id = collectionmedias.worksheet_id
                AND worksheets.category = '$wsCat'
            LEFT JOIN kabupaten ON kabupaten.id = e_collections.kabupaten_id
        ";

        $whereCondition = ["e_collections.deleted_at IS NULL AND e_collections.status='2' "];

        if ($request->title) {
            $title = strtoupper(str_replace("'", "''", $request->title));
            $whereCondition[] = "upper(e_collections.title) like '%$title%'";
        }
        if ($request->province_id) $whereCondition[] = "kabupaten.propinsiid = " . (int)$request->province_id;
        if ($request->media_id)    $whereCondition[] = "e_collections.collection_media_id = " . (int)$request->media_id;
        if ($request->executor_id) $whereCondition[] = "e_collections.penerbit_id = " . (int)$request->executor_id;
        if ($request->year)        $whereCondition[] = "e_collections.publication_year = " . (int)$request->year;

        if ($request->verified) {
            if ($request->verified == 'verified') $whereCondition[] = "e_collections.is_need_verify = 0";
            else $whereCondition[] = "(e_collections.is_need_verify = 1 or e_collections.is_need_verify is null)";
        }

        if ($request->date) {
            [$d0, $d1] = explode(' - ', $request->date);
            $startDate = \Carbon\Carbon::createFromFormat('Y/m/d', trim($d0))->format('Y-m-d');
            $endDate   = \Carbon\Carbon::createFromFormat('Y/m/d', trim($d1))->format('Y-m-d');
            $whereCondition[] = "(e_collections.received_at >= to_date('$startDate','YYYY-MM-DD') and e_collections.received_at < to_date('$endDate','YYYY-MM-DD') + 1)";
        }

        if ($request->fullname) {
            $fnReq = strtoupper(str_replace("'", "''", $request->fullname));
            $whereCondition[] = "(UPPER(e_collections.received_by_name) LIKE '%$fnReq%' OR UPPER(u.fullname) LIKE '%$fnReq%')";
        }

        if ($request->code) {
            $codeReq = strtoupper(str_replace("'", "''", $request->code));
            $whereCondition[] = "UPPER(REPLACE(e_collections.code, '-', '')) LIKE '%" . str_replace('-', '', $codeReq) . "%'";
        }

        if ($request->isbn_status) {
            $isbnStatus = $request->isbn_status;
            if ($isbnStatus === 'null') {
                $whereCondition[] = "e_collections.code_type = '1' AND NOT EXISTS (SELECT 1 FROM penerbit_isbn pi WHERE REPLACE(TRIM(pi.isbn_no),'-','') = REPLACE(TRIM(e_collections.code),'-','') AND pi.isbn_deposit_status IS NOT NULL)";
            } else {
                $statusSafe = str_replace("'", "''", $isbnStatus);
                $whereCondition[] = "EXISTS (SELECT 1 FROM penerbit_isbn pi WHERE REPLACE(TRIM(pi.isbn_no),'-','') = REPLACE(TRIM(e_collections.code),'-','') AND pi.isbn_deposit_status = '$statusSafe')";
            }
        }

        if ($search) {
            $searchEsc = str_replace("'", "''", $search);
            $terms = [];
            foreach ($column as $c) {
                if ($c) $terms[] = "upper($c) like '%$searchEsc%'";
            }
            $whereCondition[] = '(' . implode(' or ', $terms) . ')';
        }

        $whereClause = " WHERE " . implode(' AND ', $whereCondition);

        // Slim joins untuk COUNT
        $needsUsers     = (bool) $request->fullname;
        $needsKabupaten = (bool) $request->province_id;

        $slimJoins = "FROM e_collections
            JOIN collectionmedias ON collectionmedias.id = e_collections.collection_media_id
                AND (collectionmedias.isdelete = 0 OR collectionmedias.isdelete IS NULL)
            JOIN worksheets ON worksheets.id = collectionmedias.worksheet_id
                AND worksheets.category = '$wsCat'";
        if ($needsKabupaten) $slimJoins .= "\n            LEFT JOIN kabupaten ON kabupaten.id = e_collections.kabupaten_id";
        if ($needsUsers)     $slimJoins .= "\n            LEFT JOIN users u ON u.username = e_collections.received_by_name";

        // --- 2. Hitung Records ---
        $totalData = QueryAPI::get("
            SELECT COUNT(*) AS total
            FROM e_collections
            JOIN collectionmedias ON collectionmedias.id = e_collections.collection_media_id
                AND (collectionmedias.isdelete = 0 OR collectionmedias.isdelete IS NULL)
            JOIN worksheets ON worksheets.id = collectionmedias.worksheet_id
                AND worksheets.category = '$wsCat'
            WHERE e_collections.deleted_at IS NULL
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            SELECT COUNT(DISTINCT e_collections.id) AS total
            $slimJoins
            $whereClause
        ", true)->TOTAL ?? 0;

        // --- 3. Query Data (Paginasi) ---
        $orderCol = $column[$request->order[0]['column'] ?? 0] ?? 'e_collections.received_at';
        $orderDir = strtoupper($request->order[0]['dir'] ?? 'DESC');
        $orderBy  = "ORDER BY $orderCol $orderDir, e_collections.id DESC";

        $sql = "
            SELECT * FROM (
                SELECT rownum as rnum, data.* FROM (
                    SELECT
                        e_collections.id as ec_id,
                        e_collections.code,
                        e_collections.title,
                        e_collections.description,
                        e_collections.received_at as received_at_e_collection,
                        e_collections.is_need_verify as inv_e_collection,
                        e_collections.received_by_name as received_username,
                        e_collections.article_title,
                        u_recv.fullname as received_fullname,
                        collectionmedias.name as name_media,
                        p.name as penerbit_name, e_collections.penerbit_id,
                        cat.id as catalog_id
                    $baseJoins
                    LEFT JOIN users u_recv ON u_recv.username = e_collections.received_by_name
                    LEFT JOIN users u ON u.username = e_collections.received_by_name
                    LEFT JOIN penerbit p ON p.id = e_collections.penerbit_id
                    LEFT JOIN catalogs cat ON cat.edeposit_col_id = e_collections.id AND nvl(cat.isdelete, 0) = 0
                    $whereClause
                    $orderBy
                ) data
                WHERE rownum <= " . ($start + $length) . "
            )
            WHERE rnum > $start
        ";

        $queryData = QueryAPI::get($sql) ?: [];
        $data = [];

        foreach ($queryData as $val) {
            $action = '<a href="' . url('digital-storage-handover/accept/detail/' . $val->EC_ID) . '" class="btn btn-primary btn-sm"><i class="ph-info me-1"></i> Detail</a>';

            $hasCatalog      = !empty($val->CATALOG_ID);
            $alreadyVerified = $hasCatalog && (int) $val->INV_E_COLLECTION === 0;

            if ($alreadyVerified) {
                $action .= ' <a href="javascript:void(0);" class="btn btn-warning btn-sm" onclick="verification(' . $val->EC_ID . ')"><i class="ph-arrows-clockwise me-1"></i> Verifikasi Ulang</a>';
            } else {
                $action .= ' <a href="javascript:void(0);" class="btn btn-danger btn-sm" onclick="verification(' . $val->EC_ID . ')"><i class="ph-warning me-1"></i> Perlu Verifikasi</a>';
            }

            $receivedBy = $val->RECEIVED_USERNAME ?? '-';
            if (!empty($val->RECEIVED_FULLNAME)) {
                $receivedBy .= ' — ' . $val->RECEIVED_FULLNAME;
            }

            $receivedAt = $val->RECEIVED_AT_E_COLLECTION
                ? \Carbon\Carbon::parse($val->RECEIVED_AT_E_COLLECTION)->isoFormat('dddd, D MMMM Y')
                : '-';

            $data[] = [
                $start + 1,
                $action,
                $val->PENERBIT_ID . ' | ' . $val->PENERBIT_NAME,
                $val->ARTICLE_TITLE ? $val->ARTICLE_TITLE : $val->TITLE,
                $val->NAME_MEDIA ?? '-',
                $val->CODE,
                $receivedAt,
                $receivedBy,
            ];
            $start++;
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    }

    public function detail($id)
    {
        $id = (int) $id;

        $collection = QueryAPI::get("
            SELECT
                c.*,
                p.name AS name_penerbit,
                k.namakab AS namakab,
                ec.id AS e_collections_id,
                pr.namapropinsi AS namapropinsi,
                ec.code_type AS code_type_e_collection,
                ec.collection_media_id AS cm_id_e_col,
                ec.serial AS serial_e_collection,
                ec.received_at AS received_at_e_collection,
                ec.price AS price_e_collection,
                ec.jilid AS jilid_e_collection,
                ec.description AS description_e_collection,
                ec.jenis_isi AS jenis_isi_e_collection,
                ec.jenis_wadah AS jenis_wadah_e_collection,
                ec.jenis_media AS jenis_media_e_collection,
                ec.currency AS currency_e_collection,
                ec.jumlah_eks AS jumlah_eks_e_collection,
                ec.physical_description AS pd_e_collection,
                ec.title AS ec_title,
                ec.title_ori AS ec_title_ori,
                ec.code AS ec_code,
                ec.kabupaten_id AS city_id,
                ec.akses,
                ec.series,
                ec.preview,
                ec.qrcbn AS ec_qrcbn,
                ec.edition AS ec_edition,
                ec.edition_date AS ec_edition_date,
                ec.publication_year AS publishyear,
                ec.publication_month AS publish_month,
                ec.publication_day AS publish_day,
                ec.album,
                ec.author,
                ec.kelas_besar_id,
                par.title AS title_parent,
                ccr.id AS id_catalogcovers,
                ccr.fileurl AS fileurl_catalogcovers,
                ccr.hash AS hash_catalogcovers,
                ccr.mime AS mime_catalogcovers,
                ccr.file_size AS file_size_catalogcovers,
                ccr.method AS method_catalogcovers,
                cfr.id AS id_catalogfiles,
                cfr.fileurl AS fileurl_catalogfiles,
                cfr.hash AS hash_catalogfiles,
                cfr.mime AS mime_catalogfiles,
                cfr.file_size AS file_size_catalogfiles,
                cfr.method AS method_catalogfiles
            FROM e_collections ec
            LEFT JOIN catalogs c ON c.edeposit_col_id = ec.id
                AND nvl(c.isdelete, 0) = 0
            LEFT JOIN e_collections par ON par.id = ec.parent_id
            LEFT JOIN penerbit p ON p.id = c.penerbit_id
            LEFT JOIN kabupaten k ON k.id = ec.kabupaten_id
            LEFT JOIN propinsi pr ON pr.id = k.propinsiid
            LEFT JOIN (
                SELECT * FROM (
                    SELECT cf.catalog_id, cf.id, cf.fileurl, cf.hash, cf.mime, cf.file_size, cf.method,
                           row_number() OVER (ORDER BY cf.id DESC) AS rn
                    FROM catalogfiles cf
                    WHERE cf.catalog_id IN (
                        SELECT id FROM catalogs WHERE edeposit_col_id = $id AND nvl(isdelete,0) = 0
                    )
                ) WHERE rn = 1
            ) cfr ON cfr.catalog_id = c.id
            LEFT JOIN (
                SELECT * FROM (
                    SELECT cc.catalog_id, cc.id, cc.fileurl, cc.hash, cc.mime, cc.file_size, cc.method,
                           row_number() OVER (ORDER BY cc.id DESC) AS rn
                    FROM catalogcovers cc
                    WHERE cc.catalog_id IN (
                        SELECT id FROM catalogs WHERE edeposit_col_id = $id AND nvl(isdelete,0) = 0
                    )
                ) WHERE rn = 1
            ) ccr ON ccr.catalog_id = c.id
            WHERE ec.deleted_at IS NULL
              AND ec.id = $id
        ", true);

        // Status ISBN dari penerbit_isbn
        $isbnDepositStatus = null;
        $isbnNo = preg_replace('/[\s\-]/', '', $collection->EC_CODE ?? '');
        if ($isbnNo && ($collection->CODE_TYPE_E_COLLECTION ?? null) == '1') {
            $penerbitIsbnRow = QueryAPI::get("
                SELECT pi.isbn_deposit_status
                FROM penerbit_isbn pi
                WHERE REPLACE(TRIM(pi.isbn_no), '-', '') = '$isbnNo'
                  AND rownum = 1
            ", true);
            $isbnDepositStatus = $penerbitIsbnRow->ISBN_DEPOSIT_STATUS ?? null;
        }

        $collectionCategory = [];
        $collectionId       = $id;

        $dataCollectionCategory = QueryAPI::get("
            select
                *
            from
                e_collection_categories
            where
                collection_id = $collectionId
        ");

        if ($dataCollectionCategory) {
            foreach ($dataCollectionCategory as $dcc) {
                $collectionCategory[] = $dcc->CATEGORY_ID;
            }
        }

        $collectionCopy = QueryAPI::get("
            select
                *
            from
                e_collections
            where
                parent_id = $collectionId and
                deleted_at is null
        ");

        return view('layouts.index', [
            'data' => [
                'worksheet' => QueryAPI::get("select * from worksheets where category is not null") ?? [],
                'media' => QueryAPI::get("select * from collectionmedias where (isdelete = 0 or isdelete is null) and worksheet_id in (20,142)") ?? [],
                'category' => QueryAPI::get("select * from e_categories where deleted_at is null") ?? [],
                'problem' => QueryAPI::get("select * from e_problems where deleted_at is null") ?? [],
                'contentType' => QueryAPI::get("select * from fieldrefs where tag = '336'") ?? [],
                'containerType' => QueryAPI::get("select * from fieldrefs where tag = '337'") ?? [],
                'mediaType' => QueryAPI::get("select * from fieldrefs where tag = '338'") ?? [],
                'bigClass' => QueryAPI::get("select * from master_kelas_besar") ?? [],
                'collection' => $collection,
                'isbnDepositStatus' => $isbnDepositStatus,
                'collectionCategory' => $collectionCategory,
                'collectionContributor' => explode(';', ($collection->AUTHOR ?? '')),
                'collectionCopy' => $collectionCopy,
                'physicalDescription' => json_decode($collection->PD_E_COLLECTION ?? ''),
                'content' => 'digital-storage-handover.accept-detail',
                'plugins' => [
                    'select2',
                    'datatable',
                    'epubjs',
                    'videojs',
                    'pdfjs',
                    'howlerjs',
                ]
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $id = (int) $id;

        $update = QueryAPI::update('e_collections', $id, [
            'jilid'          => $request->binding,
            'is_need_verify' => 1,
            'updated_by'     => session('id'),
        ], true);

        if ($update) {
            return response()->json(['code' => 200, 'message' => 'Berhasil disimpan'], 200);
        }

        return response()->json(['code' => 500, 'message' => 'Gagal menyimpan'], 500);
    }

    public function verification(Request $request)
    {
        $id = $request->id;

        $collection = QueryAPI::get("SELECT received_by_name FROM e_collections WHERE id = " . (int) $id, true);
        $acceptedBy = $collection->RECEIVED_BY_NAME ?? session('username');

        $verif = QueryAPI::verificationCollection($id, $acceptedBy);

        if ($verif) {
            $update = QueryAPI::update('e_collections', $id, [
                'is_need_verify' => 0,
                'updated_by' => session('id')
            ], true);

            if ($update) {
                return response()->json([
                    'code' => 200,
                    'message' => 'Sukses diverifikasi'
                ], 200);
            }
        }

        return response()->json([
            'code' => 404,
            'message' => 'Gagal diverifikasi'
        ], 404);
    }
}
