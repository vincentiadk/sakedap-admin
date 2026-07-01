<?php

namespace App\Http\Controllers\Report;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Jobs\ExcelDownloadBackgroundJob;

class ManageController extends Controller
{
    private $worksheetCategory;

    public function __construct()
    {
        $this->worksheetCategory = Main::COLLECTION_DIGITAL;
    }

    public function index(Request $request)
    {
        if ($request->exported) {
            $jobID = (string) Str::uuid();
            $userId = session('id');
            $userKey = "user:$userId:download";

            $payload = [
                'is_not_center_branch' => !Main::isSuperAdmin(),
                'title' => $request->title,
                'executor_id' => $request->executor_id,
                'province_id' => $request->province_id,
                'year' => $request->year,
                'media_id' => $request->media_id,
                'date' => $request->date
            ];

            Redis::lpush($userKey, $jobID);
            ExcelDownloadBackgroundJob::dispatch($jobID, 'report-manage', $payload)
                ->onQueue('report');

            return redirect('report/manage')->with(['success' => 'Data laporan sedang diproses']);
        }

        return view('layouts.index', [
            'data' => [
                'media' => QueryAPI::get("select * from collectionmedias where (isdelete = 0 or isdelete is null) and worksheet_id in (20,142)") ?? [],
                'content' => 'report.manage',
                'plugins' => [
                    'datatable',
                    'select2',
                    'daterangepicker',
                ]
            ]
        ]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'c.id', null, 'c.id', 'p.id', 'p.name', 'e.received_at', 'cfr.method', 'pr.namapropinsi', 
            'kb.namakab', 'c.title', 'cm.name', 'c.album', 'c.series', 'c.edition', 'e.serial', 
            'c.deweyno', 'c.volume', 'c.isbn', 'e.deposit', 'c.controlnumber', 'c.publishyear', 
            'c.copyright', 'c.preview', null, 'cfr.method', 'c.akses', 'c.author', null, 
            'cfr.file_size', 'cfr.fileurl', 'e.created_at', 'c.createdate', 'e.price', 'fullname', 
            'c.callnumber', 'cl.noinduk_deposit',
        ];

        $searchableColumns = [
            'c.id', 'p.id', 'p.name', 'TO_CHAR(e.received_at, \'YYYY-MM-DD HH24:MI:SS\')',
            'pr.namapropinsi', 'kb.namakab', 'c.title', 'cm.name', 'c.album', 'c.series',
            'c.edition', 'e.serial', 'c.deweyno', 'c.volume', 'c.isbn', 'e.deposit',
            'c.controlnumber', 'c.publishyear', 'c.copyright', 'c.preview', 'c.akses',
            'c.author', 'TO_CHAR(e.price)', 
            "CASE WHEN ri.fullname IS NOT NULL THEN CAST(ri.fullname AS VARCHAR2(255)) ELSE u_receive.fullname END",
            'c.callnumber',
        ];

        $draw   = intval($request->draw ?? 0);
        $start  = intval($request->start ?? 0);
        $length = intval($request->length ?? 10);
        $search = strtoupper(trim($request->search['value'] ?? ''));

        // --- Build Where Clause ---
        $whereCondition = ["NVL(c.isdelete,0) = 0", "w.category = '$this->worksheetCategory'", "c.edeposit_col_id IS NOT NULL"];
        
        if ($request->title) {
            $title = strtoupper(str_replace("'", "''", $request->title));
            $whereCondition[] = "UPPER(c.title) LIKE '%$title%'";
        }
        if ($request->executor_id) $whereCondition[] = "c.penerbit_id = " . (int)$request->executor_id;
        if ($request->province_id) $whereCondition[] = "kb.propinsiid = " . (int)$request->province_id;
        if ($request->year) $whereCondition[] = "c.publishyear = " . (int)$request->year;
        if ($request->media_id) $whereCondition[] = "e.collection_media_id = " . (int)$request->media_id;
        
        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = \Carbon\Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = \Carbon\Carbon::parse($explodeDate[1])->format('Y-m-d');
            $whereCondition[] = "(e.received_at >= TO_DATE('$startDate', 'YYYY-MM-DD') AND e.received_at < TO_DATE('$endDate', 'YYYY-MM-DD') + 1)";
        }

        if ($request->fullname) {
            $fnReq = strtoupper(str_replace("'", "''", $request->fullname));
            $whereCondition[] = "UPPER(CASE WHEN ri.fullname IS NOT NULL THEN CAST(ri.fullname AS VARCHAR2(255)) ELSE u_receive.fullname END) LIKE '%$fnReq%'";
        }

        if ($search) {
            $searchEsc = str_replace("'", "''", $search);
            $terms = [];
            foreach ($searchableColumns as $col) $terms[] = "UPPER($col) LIKE '%$searchEsc%'";
            $whereCondition[] = '(' . implode(' OR ', $terms) . ')';
        }

        $whereClause = " WHERE " . implode(' AND ', $whereCondition);

        // --- Joins & Base Query ---
        $filterJoins = "
            FROM catalogs c
            JOIN worksheets w ON w.id = c.worksheet_id
            LEFT JOIN penerbit p ON p.id = c.penerbit_id
            LEFT JOIN e_collections e ON e.id = c.edeposit_col_id
            LEFT JOIN kabupaten kb ON kb.id = e.kabupaten_id
            LEFT JOIN propinsi pr ON pr.id = kb.propinsiid
            LEFT JOIN collectionmedias cm ON cm.id = e.collection_media_id
            LEFT JOIN users u_receive ON u_receive.id = e.received_by
            LEFT JOIN (
                SELECT eu.id AS eu_id, MAX(ea.fullname) AS fullname
                FROM e_users eu
                JOIN e_admins ea ON ea.id = eu.userable_id
                WHERE eu.userable_type = 'admins'
                GROUP BY eu.id
            ) ri ON ri.eu_id = e.received_by
        ";

        $baseQuery = "
            SELECT c.id, e.deposit, c.title, c.createdate, c.isbn, c.controlnumber
            FROM catalogs c
            JOIN worksheets w ON w.id = c.worksheet_id
            LEFT JOIN penerbit p ON p.id = c.penerbit_id
            LEFT JOIN e_collections e ON e.id = c.edeposit_col_id
            LEFT JOIN kabupaten kb ON kb.id = e.kabupaten_id
            LEFT JOIN propinsi pr ON pr.id = kb.propinsiid
            LEFT JOIN collectionmedias cm ON cm.id = e.collection_media_id
            LEFT JOIN users u_receive ON u_receive.id = e.received_by
            LEFT JOIN (
                SELECT eu.id AS eu_id, MAX(ea.fullname) AS fullname
                FROM e_users eu
                JOIN e_admins ea ON ea.id = eu.userable_id
                WHERE eu.userable_type = 'admins'
                GROUP BY eu.id
            ) ri ON ri.eu_id = e.received_by
            $whereClause
        ";

        // 2. Query Deduplikasi (Satu-satunya sumber kebenaran)
        // Kita gunakan ID unik per deposit di sini
        $deduplicatedQuery = "
            SELECT id FROM (
                SELECT id, 
                    ROW_NUMBER() OVER (
                        PARTITION BY 
                            COALESCE(TO_CHAR(deposit), 'NULL_' || id) || '|' ||
                            COALESCE(title, 'NULL') || '|' ||
                            COALESCE(TO_CHAR(createdate, 'YYYYMMDDHH24MISS'), 'NULL') || '|' ||
                            COALESCE(isbn, 'NULL') || '|' ||
                            COALESCE(controlnumber, 'NULL')
                        ORDER BY id DESC
                    ) as rn
                FROM ($baseQuery)
            ) WHERE rn = 1
        ";

        // --- Execution ---
        $totalData = QueryAPI::get("SELECT COUNT(*) AS total FROM catalogs c JOIN worksheets w ON w.id = c.worksheet_id WHERE NVL(c.isdelete,0) = 0 AND w.category = '$this->worksheetCategory' AND c.edeposit_col_id IS NOT NULL", true)->TOTAL ?? 0;
        

        $totalFiltered = QueryAPI::get("
            SELECT COUNT(*) AS total 
            FROM ($deduplicatedQuery)
        ", true)->TOTAL ?? 0;

        $endRow = $start + $length;
        $sqlIds = "
            SELECT rnum, id
            FROM (
                SELECT ROWNUM AS rnum, id
                FROM (
                    SELECT id 
                    FROM ($deduplicatedQuery)
                    ORDER BY id DESC
                )
                WHERE ROWNUM <= $endRow
            )
            WHERE rnum > $start
        ";

        $pagedIdsData = QueryAPI::get($sqlIds);
        $finalData = [];

        if ($pagedIdsData && count($pagedIdsData) > 0) {
            $pagedIds = array_map(fn($item) => $item->ID, $pagedIdsData);
            $idString = implode(',', array_unique($pagedIds));

            $sqlDetail = "
                SELECT c.id, c.title, c.album, c.series, c.edition, c.deweyno, c.volume, c.isbn, c.controlnumber, 
                    c.publishyear, c.copyright, c.preview, c.akses, c.author, c.callnumber,
                    e.deposit AS deposit_e_collection, e.article_doi AS doi_e_collection, e.created_at AS created_at_e_collection, 
                    e.received_at, e.serial AS serial_e_collection, e.price AS price_e_collection, e.article_subject AS a_subject,
                    p.id AS id_penerbit, p.name AS name_penerbit, pr.namapropinsi, kb.namakab, cm.name AS name_media,
                    CASE WHEN ri.fullname IS NOT NULL THEN CAST(ri.fullname AS VARCHAR2(255)) ELSE u_receive.fullname END AS fullname,
                    (SELECT MAX(cf.fileurl) KEEP (DENSE_RANK LAST ORDER BY cf.id) FROM catalogfiles cf WHERE cf.catalog_id = c.id) AS fileurl_catalogfiles,
                    (SELECT MAX(cf.file_size) KEEP (DENSE_RANK LAST ORDER BY cf.id) FROM catalogfiles cf WHERE cf.catalog_id = c.id) AS file_size_catalogfiles,
                    (SELECT MAX(cf.method) KEEP (DENSE_RANK LAST ORDER BY cf.id) FROM catalogfiles cf WHERE cf.catalog_id = c.id) AS method_catalogfiles,
                    (SELECT MAX(cl.noinduk_deposit) KEEP (DENSE_RANK LAST ORDER BY cl.id) FROM collections cl WHERE cl.catalog_id = c.id) AS noinduk_dep_cl
                FROM catalogs c
                LEFT JOIN penerbit p ON p.id = c.penerbit_id
                LEFT JOIN e_collections e ON e.id = c.edeposit_col_id
                LEFT JOIN kabupaten kb ON kb.id = e.kabupaten_id
                LEFT JOIN propinsi pr ON pr.id = kb.propinsiid
                LEFT JOIN collectionmedias cm ON cm.id = e.collection_media_id
                LEFT JOIN users u_receive ON u_receive.id = e.received_by
                LEFT JOIN (SELECT eu.id AS eu_id, MAX(ea.fullname) AS fullname FROM e_users eu JOIN e_admins ea ON ea.id = eu.userable_id WHERE eu.userable_type = 'admins' GROUP BY eu.id) ri ON ri.eu_id = e.received_by
                WHERE c.id IN ($idString)
            ";

            $queryData = QueryAPI::get($sqlDetail) ?: [];
            $dataMap = [];
            foreach ($queryData as $row) $dataMap[$row->ID] = $row;

            $counter = $start + 1;
            foreach ($pagedIds as $id) {
                if (!isset($dataMap[$id])) continue;
                $val = $dataMap[$id];
                $action = '<a href="' . url('report/manage/detail/' . $val->ID) . '" class="btn btn-primary btn-sm"><i class="ph-info me-1"></i> Detail</a>';
                $finalData[] = [
                    $counter++, $action, $val->ID, $val->ID_PENERBIT, $val->NAME_PENERBIT,
                    $val->RECEIVED_AT ? \Carbon\Carbon::parse($val->RECEIVED_AT)->isoFormat('D MMMM Y') : '-',
                    Main::method($val->METHOD_CATALOGFILES), $val->NAMAPROPINSI, $val->NAMAKAB, $val->TITLE,
                    $val->NAME_MEDIA, $val->ALBUM, $val->SERIES, $val->EDITION, Main::serial($val->SERIAL_E_COLLECTION),
                    $val->DEWEYNO, $val->VOLUME, $val->ISBN, $val->DEPOSIT_E_COLLECTION, $val->CONTROLNUMBER,
                    $val->PUBLISHYEAR, $val->COPYRIGHT, $val->PREVIEW, 'Tidak', $val->METHOD_CATALOGFILES == 4 ? 'Ya' : 'Tidak',
                    Main::access($val->AKSES), $val->AUTHOR, $val->A_SUBJECT, Main::formatFileSize($val->FILE_SIZE_CATALOGFILES),
                    strtoupper(pathinfo($val->FILEURL_CATALOGFILES ?? '', PATHINFO_EXTENSION)),
                    $val->CREATED_AT_E_COLLECTION ? \Carbon\Carbon::parse($val->CREATED_AT_E_COLLECTION)->format('d-m-Y, H:i') : '-',
                    $val->RECEIVED_AT ? \Carbon\Carbon::parse($val->RECEIVED_AT)->format('d-m-Y, H:i') : '-',
                    $val->PRICE_E_COLLECTION, $val->FULLNAME, $val->DOI_E_COLLECTION, $val->CALLNUMBER, $val->NOINDUK_DEP_CL,
                ];
            }
        }

        return response()->json(['draw' => $draw, 'recordsTotal' => $totalData, 'recordsFiltered' => $totalFiltered, 'data' => $finalData]);
    }

    public function detail($id)
    {
        $collection = QueryAPI::get("
            select
                c.*,
                p.name as name_penerbit,
                k.namakab as namakab,
                pr.namapropinsi as namapropinsi,
                ec.collection_media_id as cm_id_e_col,
                ec.code_type as code_type_e_collection,
                ec.serial as serial_e_collection,
                ec.received_at as received_at_e_collection,
                ec.price as price_e_collection,
                ec.jilid as jilid_e_collection,
                ec.description as description_e_collection,
                ec.jenis_isi as jenis_isi_e_collection,
                ec.jenis_wadah as jenis_wadah_e_collection,
                ec.jenis_media as jenis_media_e_collection,
                ec.currency as currency_e_collection,
                ec.jumlah_eks as jumlah_eks_e_collection,
                ec.physical_description as pd_e_collection,
                par.title as title_parent,
                ccr.id as id_catalogcovers,
                ccr.fileurl as fileurl_catalogcovers,
                ccr.hash as hash_catalogcovers,
                ccr.mime as mime_catalogcovers,
                ccr.file_size as file_size_catalogcovers,
                ccr.method as method_catalogcovers,
                cfr.id as id_catalogfiles,
                cfr.fileurl as fileurl_catalogfiles,
                cfr.hash as hash_catalogfiles,
                cfr.mime as mime_catalogfiles,
                cfr.file_size as file_size_catalogfiles,
                cfr.method as method_catalogfiles
            from
                catalogs c
            left join
                e_collections ec on ec.id = c.edeposit_col_id
            left join
                e_collections par on par.id = ec.parent_id
            left join
                penerbit p on p.id = c.penerbit_id
            left join
                kabupaten k on k.id = ec.kabupaten_id
            left join
                propinsi pr on pr.id = k.propinsiid
            left join
                (
                    select
                        *
                    from (
                        select
                            cf.catalog_id,
                            cf.id,
                            cf.fileurl,
                            cf.hash,
                            cf.mime,
                            cf.file_size,
                            cf.method,
                            row_number() over (order by cf.id desc) as rn
                        from
                            catalogfiles cf
                        where
                            cf.catalog_id = $id
                    ) where rn = 1
                ) cfr on cfr.catalog_id = c.id
            left join
                (
                    select
                        *
                    from (
                        select
                            cc.catalog_id,
                            cc.id,
                            cc.fileurl,
                            cc.hash,
                            cc.mime,
                            cc.file_size,
                            cc.method,
                            row_number() over (order by cc.id desc) as rn
                        from
                            catalogcovers cc
                        where
                            cc.catalog_id = $id
                    ) where rn = 1
                ) ccr on ccr.catalog_id = c.id
            where
                nvl(c.isdelete, 0) = 0
                and c.id = $id
        ", true);

        $collectionCategory = [];
        $collectionId = $collection->EDEPOSIT_COL_ID ?? 0;

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
                'collectionCategory' => $collectionCategory,
                'collectionContributor' => explode(';', ($collection->AUTHOR ?? '')),
                'collectionCopy' => $collectionCopy,
                'physicalDescription' => json_decode($collection->PD_E_COLLECTION ?? ''),
                'content' => 'report.manage-detail',
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
}
