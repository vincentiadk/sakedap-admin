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
            'c.id',
            null,
            'c.id',
            'penerbit.id',
            'penerbit.name',
            'e.created_at',
            'cfr.method',
            'propinsi.namapropinsi',
            'kabupaten.namakab',
            'c.title',
            'collectionmedias.name',
            'c.album',
            'c.series',
            'c.edition',
            'e_collections.serial',
            'c.deweyno',
            'c.volume',
            'c.isbn',
            'e_collections.deposit',
            'c.controlnumber',
            'c.publishyear',
            'c.copyright',
            'c.preview',
            null,
            'cfr.method',
            'c.akses',
            'c.author',
            null,
            'cfr.file_size',
            'cfr.fileurl',
            'e_collections.created_at',
            'c.createdate',
            'e_collections.price',
            'u_receive.fullname'
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 10);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "
            (
                c.isdelete = 0 or
                c.isdelete is null
            ) and
            w.category = '$this->worksheetCategory' and
            c.edeposit_col_id is not null
        ";

        if ($request->title) {
            $title = strtoupper($request->title);
            $whereCondition[] = "upper(c.title) like '%$title%'";
        }

        if ($request->executor_id) {
            $whereCondition[] = "c.penerbit_id = $request->executor_id";
        }

        if ($request->province_id) {
            $whereCondition[] = "kb.propinsiid = $request->province_id";
        }

        if ($request->year) {
            $whereCondition[] = "c.publishyear = $request->year";
        }

        if ($request->media_id) {
            $whereCondition[] = "e.collection_media_id = $request->media_id";
        }

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $whereCondition[] = "(e.received_at >= to_date('$startDate', 'YYYY-MM-DD') and e.received_at < to_date('$endDate', 'YYYY-MM-DD') + 1)";
        }

        if ($request->fullname) {
            $whereCondition[] = "
                UPPER(
                    CASE
                        WHEN ea_receive.fullname IS NOT NULL THEN CAST(ea_receive.fullname AS VARCHAR2(255))
                        ELSE u_receive.fullname
                    END
                ) LIKE '%" . strtoupper($request->fullname) . "%'
            ";
        }

        if ($search) {
            $terms = [];

            foreach ($column as $c) {
                if ($c) {
                    $terms[] = "upper($c) like '%$search%'";
                }
            }

            $whereCondition[] = '(' . implode(' or ', $terms) . ')';
        }

        if ($whereCondition) {
            $whereClause = "where " . implode(' and ', $whereCondition);
        }

        if ($order) {
            $orderColumnIndex = $order[0]['column'];
            $orderDir = $order[0]['dir'];
            $orderBy = "order by " . $column[$orderColumnIndex] . " $orderDir";
        }

        $totalData = QueryAPI::get("
            select
                count(*) as total
            from
                catalogs
            left join
                worksheets on worksheets.id = catalogs.worksheet_id
            where
                (
                    catalogs.isdelete = 0 or
                    catalogs.isdelete is null
                ) and
                worksheets.category = '$this->worksheetCategory' and
                catalogs.edeposit_col_id is not null
        ", true)->TOTAL ?? 0;

        $deduplicatedInner = "
            SELECT DISTINCT c.id AS catalog_id
            FROM
                catalogs c
            LEFT JOIN penerbit p ON p.id = c.penerbit_id
            LEFT JOIN e_collections e ON e.id = c.edeposit_col_id
            LEFT JOIN kabupaten kb ON kb.id = e.kabupaten_id
            LEFT JOIN propinsi pr ON pr.id = kb.propinsiid
            LEFT JOIN collectionmedias cm ON cm.id = e.collection_media_id
            LEFT JOIN worksheets w ON w.id = c.worksheet_id
            LEFT JOIN users u_receive
                ON u_receive.id = e.received_by
            LEFT JOIN (
                SELECT
                    eu.id,
                    eu.userable_id,
                    ROW_NUMBER() OVER (PARTITION BY eu.id ORDER BY eu.id) AS rn_eu
                FROM e_users eu
                WHERE eu.userable_type = 'admins'
            ) eu_receive
                ON eu_receive.id = e.received_by
                AND eu_receive.rn_eu = 1
            LEFT JOIN e_admins ea_receive
                ON ea_receive.id = eu_receive.userable_id
            $whereClause
        ";

        $sqlFiltered = "
            SELECT count(*) AS total
            FROM ($deduplicatedInner) dedup
        ";

        $totalFiltered = QueryAPI::get($sqlFiltered, true)->TOTAL ?? 0;

        $sql = "
            SELECT
                c.*,
                e.deposit AS deposit_e_collection,
                e.created_at AS created_at_e_collection,
                e.received_at,
                e.serial AS serial_e_collection,
                e.price AS price_e_collection,
                p.id AS id_penerbit,
                p.name AS name_penerbit,
                pr.namapropinsi AS namapropinsi,
                kb.namakab AS namakab,
                cm.name AS name_media,
                cfr.fileurl AS fileurl_catalogfiles,
                cfr.file_size AS file_size_catalogfiles,
                cfr.method AS method_catalogfiles,
                CASE
                    WHEN ea_receive.fullname IS NOT NULL THEN CAST(ea_receive.fullname AS VARCHAR2(255))
                    ELSE u_receive.fullname
                END AS fullname
            FROM (
                SELECT *
                FROM (
                    SELECT
                        rownum AS rnum,
                        base.*
                    FROM (
                        SELECT DISTINCT c.id
                        FROM
                            catalogs c
                        LEFT JOIN penerbit p ON p.id = c.penerbit_id
                        LEFT JOIN e_collections e ON e.id = c.edeposit_col_id
                        LEFT JOIN kabupaten kb ON kb.id = e.kabupaten_id
                        LEFT JOIN propinsi pr ON pr.id = kb.propinsiid
                        LEFT JOIN collectionmedias cm ON cm.id = e.collection_media_id
                        LEFT JOIN worksheets w ON w.id = c.worksheet_id
                        LEFT JOIN users u_receive
                            ON u_receive.id = e.received_by
                        LEFT JOIN (
                            SELECT
                                eu.id,
                                eu.userable_id,
                                ROW_NUMBER() OVER (PARTITION BY eu.id ORDER BY eu.id) AS rn_eu
                            FROM e_users eu
                            WHERE eu.userable_type = 'admins'
                        ) eu_receive
                            ON eu_receive.id = e.received_by
                            AND eu_receive.rn_eu = 1
                        LEFT JOIN e_admins ea_receive
                            ON ea_receive.id = eu_receive.userable_id
                        $whereClause
                        $orderBy
                    ) base
                    WHERE rownum <= " . ($start + $length) . "
                )
                WHERE rnum > " . (int)$start . "
            ) paged
            JOIN catalogs c ON c.id = paged.id
            LEFT JOIN penerbit p ON p.id = c.penerbit_id
            LEFT JOIN e_collections e ON e.id = c.edeposit_col_id
            LEFT JOIN kabupaten kb ON kb.id = e.kabupaten_id
            LEFT JOIN propinsi pr ON pr.id = kb.propinsiid
            LEFT JOIN collectionmedias cm ON cm.id = e.collection_media_id
            LEFT JOIN worksheets w ON w.id = c.worksheet_id
            LEFT JOIN (
                SELECT
                    cf.catalog_id,
                    cf.id,
                    cf.fileurl,
                    cf.hash,
                    cf.mime,
                    cf.file_size,
                    cf.method
                FROM (
                    SELECT
                        cf.catalog_id,
                        cf.id,
                        cf.fileurl,
                        cf.hash,
                        cf.mime,
                        cf.file_size,
                        cf.method,
                        ROW_NUMBER() OVER (PARTITION BY cf.catalog_id ORDER BY cf.id DESC) AS rn
                    FROM catalogfiles cf
                ) cf
                WHERE cf.rn = 1
            ) cfr ON cfr.catalog_id = c.id
            LEFT JOIN users u_receive
                ON u_receive.id = e.received_by
            LEFT JOIN (
                SELECT
                    eu.id,
                    eu.userable_id,
                    ROW_NUMBER() OVER (PARTITION BY eu.id ORDER BY eu.id) AS rn_eu
                FROM e_users eu
                WHERE eu.userable_type = 'admins'
            ) eu_receive
                ON eu_receive.id = e.received_by
                AND eu_receive.rn_eu = 1
            LEFT JOIN e_admins ea_receive
                ON ea_receive.id = eu_receive.userable_id
            ORDER BY paged.rnum
        ";

        $queryData = QueryAPI::get($sql);

        if ($queryData) {
            foreach ($queryData as $val) {
                $action = '
                    <a href="' . url('report/manage/detail/' . $val->ID) . '" class="btn btn-primary btn-sm">
                        <i class="ph-info me-1"></i>
                        Detail
                    </a>
                ';

                $data[] = [
                    $start + 1,
                    $action,
                    $val->ID,
                    $val->ID_PENERBIT,
                    $val->NAME_PENERBIT,
                    Carbon::parse($val->RECEIVED_AT)->isoFormat('D MMMM Y'),
                    Main::method($val->METHOD_CATALOGFILES),
                    $val->NAMAPROPINSI,
                    $val->NAMAKAB,
                    $val->TITLE,
                    $val->NAME_MEDIA,
                    $val->ALBUM,
                    $val->SERIES,
                    $val->EDITION,
                    Main::serial($val->SERIAL_E_COLLECTION),
                    $val->DEWEYNO,
                    $val->VOLUME,
                    $val->ISBN,
                    $val->DEPOSIT_E_COLLECTION,
                    $val->CONTROLNUMBER,
                    $val->PUBLISHYEAR,
                    $val->COPYRIGHT,
                    $val->PREVIEW,
                    'Tidak',
                    $val->METHOD_CATALOGFILES == 4 ? 'Ya' : 'Tidak',
                    Main::access($val->AKSES),
                    $val->AUTHOR,
                    '',
                    Main::formatFileSize($val->FILE_SIZE_CATALOGFILES),
                    strtoupper(pathinfo($val->FILEURL_CATALOGFILES, PATHINFO_EXTENSION)),
                    Carbon::parse($val->CREATEDATE)->format('d-m-Y') . ', ' . Carbon::parse($val->CREATEDATE)->format('H:i'),
                    Carbon::parse($val->RECEIVED_AT)->format('d-m-Y') . ', ' . Carbon::parse($val->RECEIVED_AT)->format('H:i'),
                    $val->PRICE_E_COLLECTION,
                    $val->FULLNAME
                ];

                $start++;
            }
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
