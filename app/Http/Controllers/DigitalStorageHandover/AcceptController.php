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
            'catalogs.id', null, 'penerbit.name', 'catalogs.title',
            'collectionmedias.name', 'catalogs.isbn', 'catalogs.createdate',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = intval($request->length ?? 10);
        $search = strtoupper(trim($request->search['value'] ?? ''));

        // --- 1. Definisi Komponen Query (Sama untuk semua) ---
        $baseJoins = "
            FROM catalogs
            LEFT JOIN penerbit ON penerbit.id = catalogs.penerbit_id
            LEFT JOIN e_collections ON e_collections.id = catalogs.edeposit_col_id
            LEFT JOIN kabupaten ON kabupaten.id = e_collections.kabupaten_id
            LEFT JOIN worksheets ON worksheets.id = catalogs.worksheet_id
            LEFT JOIN collectionmedias ON collectionmedias.id = e_collections.collection_media_id
        ";

        $whereCondition = [
            "(catalogs.isdelete = 0 or catalogs.isdelete is null)", 
            "worksheets.category = '" . str_replace("'", "''", $this->worksheetCategory) . "'", 
            "catalogs.edeposit_col_id IS NOT NULL"
        ];

        if ($request->title) {
            $title = strtoupper(str_replace("'", "''", $request->title));
            $whereCondition[] = "upper(catalogs.title) like '%$title%'";
        }
        if ($request->executor_id) $whereCondition[] = "catalogs.penerbit_id = " . (int)$request->executor_id;
        if ($request->province_id) $whereCondition[] = "kabupaten.propinsiid = " . (int)$request->province_id;
        if ($request->year) $whereCondition[] = "catalogs.publishyear = " . (int)$request->year;
        if ($request->media_id) $whereCondition[] = "e_collections.collection_media_id = " . (int)$request->media_id;
        
        if ($request->verified) {
            if ($request->verified == 'verified') $whereCondition[] = "e_collections.is_need_verify = 0";
            else $whereCondition[] = "(e_collections.is_need_verify = 1 or e_collections.is_need_verify is null)";
        }

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = \Carbon\Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = \Carbon\Carbon::parse($explodeDate[1])->format('Y-m-d');
            $whereCondition[] = "(e_collections.received_at >= to_date('$startDate', 'YYYY-MM-DD') and e_collections.received_at < to_date('$endDate', 'YYYY-MM-DD') + 1)";
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

        // --- 2. Hitung Records (Sinkronisasi Total) ---
        $totalData = QueryAPI::get("SELECT COUNT(*) AS total FROM catalogs LEFT JOIN worksheets ON worksheets.id = catalogs.worksheet_id WHERE (catalogs.isdelete = 0 or catalogs.isdelete is null) AND worksheets.category = '" . str_replace("'", "''", $this->worksheetCategory) . "' AND catalogs.edeposit_col_id is not null", true)->TOTAL ?? 0;
        
        $totalFiltered = QueryAPI::get("
            SELECT COUNT(DISTINCT catalogs.id) AS total
            $baseJoins
            $whereClause
        ", true)->TOTAL ?? 0;

        // --- 3. Query Data (Paginasi) ---
        $orderCol = $column[$request->order[0]['column'] ?? 0] ?? 'catalogs.createdate';
        $orderDir = strtoupper($request->order[0]['dir'] ?? 'DESC');
        $orderBy = "ORDER BY $orderCol $orderDir, catalogs.id DESC";

        $sql = "
            SELECT * FROM (
                SELECT rownum as rnum, data.* FROM (
                    SELECT
                        catalogs.id, catalogs.title, catalogs.isbn, catalogs.createdate,
                        catalogs.penerbit_id, catalogs.edeposit_col_id,
                        e_collections.received_at as received_at_e_collection,
                        e_collections.is_need_verify as inv_e_collection,
                        penerbit.name as name_penerbit,
                        collectionmedias.name as name_media
                    $baseJoins
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
            $action = '<a href="' . url('digital-storage-handover/accept/detail/' . $val->ID) . '" class="btn btn-primary btn-sm"><i class="ph-info me-1"></i> Detail</a>';
            
            if ($val->INV_E_COLLECTION == 0 || $val->INV_E_COLLECTION === '0') {
                $action .= ' <a href="javascript:void(0);" class="btn btn-success btn-sm"><i class="ph-check me-1"></i> Terverifikasi</a>';
            } else {
                $action .= ' <a href="javascript:void(0);" class="btn btn-danger btn-sm" onclick="verification(' . $val->EDEPOSIT_COL_ID . ')"><i class="ph-warning me-1"></i> Verifikasi</a>';
            }

            $data[] = [
                $start + 1,
                $action,
                $val->PENERBIT_ID . ' | ' . $val->NAME_PENERBIT,
                $val->TITLE,
                $val->NAME_MEDIA,
                $val->ISBN,
                \Carbon\Carbon::parse($val->CREATEDATE)->isoFormat('dddd, D MMMM Y'),
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
        $collection = QueryAPI::get("
            select
                c.*,
                p.name as name_penerbit,
                k.namakab as namakab,
                ec.id as e_collections_id,
                pr.namapropinsi as namapropinsi,
                ec.code_type as code_type_e_collection,
                ec.collection_media_id as cm_id_e_col,
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

    public function verification(Request $request)
    {
        $id = $request->id;
        $verif = QueryAPI::verificationCollection($id, session('username'));

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
