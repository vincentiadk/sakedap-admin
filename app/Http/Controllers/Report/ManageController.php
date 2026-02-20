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
            'catalogs.id',
            null,
            'penerbit.name',
            'e_collections.created_at',
            'cfr.method',
            'propinsi.namapropinsi',
            'kabupaten.namakab',
            'catalogs.title',
            'collectionmedias.name',
            'catalogs.album',
            'catalogs.series',
            'catalogs.edition',
            'e_collections.serial',
            'catalogs.deweyno',
            'catalogs.volume',
            'catalogs.isbn',
            'catalogs.controlnumber',
            'catalogs.publishyear',
            'catalogs.copyright',
            'catalogs.preview',
            null,
            'cfr.method',
            'catalogs.akses',
            'catalogs.author',
            null,
            'cfr.file_size',
            'cfr.fileurl',
            'e_collections.created_at',
            'catalogs.createdate',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 10);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "(catalogs.isdelete = 0 or catalogs.isdelete is null)";
        $whereCondition[] = "catalogs.edeposit_col_id is not null";

        if ($request->title) {
            $title = strtoupper($request->title);
            $whereCondition[] = "upper(catalogs.title) like '%$title%'";
        }

        if ($request->executor_id) {
            $whereCondition[] = "catalogs.penerbit_id = $request->executor_id";
        }

        if ($request->province_id) {
            $whereCondition[] = "kabupaten.propinsiid = $request->province_id";
        }

        if ($request->year) {
            $whereCondition[] = "catalogs.publishyear = $request->year";
        }

        if ($request->media_id) {
            $whereCondition[] = "catalogs.collectionmedia_id = $request->media_id";
        }

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $whereCondition[] = "(catalogs.createdate >= to_date('$startDate', 'YYYY-MM-DD') and catalogs.createdate < to_date('$endDate', 'YYYY-MM-DD') + 1)";
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
            where
                (
                    isdelete = 0 or
                    isdelete is null
                ) and
                edeposit_col_id is not null
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                catalogs
            left join
                penerbit on penerbit.id = catalogs.penerbit_id
            left join
                kabupaten on kabupaten.id = catalogs.city_id
            left join
                propinsi on propinsi.id = kabupaten.propinsiid
            left join
                collectionmedias on collectionmedias.id = catalogs.collectionmedia_id
            left join
                worksheets on worksheets.id = catalogs.worksheet_id
            left join
                e_collections on e_collections.id = catalogs.edeposit_col_id
            left join
                (
                    select
                        cf.catalog_id,
                        cf.id,
                        cf.fileurl,
                        cf.hash,
                        cf.mime,
                        cf.file_size,
                        cf.method
                    from (
                        select
                            cf.catalog_id,
                            cf.id,
                            cf.fileurl,
                            cf.hash,
                            cf.mime,
                            cf.file_size,
                            cf.method,
                            ROW_NUMBER() OVER (PARTITION BY cf.catalog_id ORDER BY cf.id DESC) as rn
                        from
                            catalogfiles cf
                    ) cf
                    where
                        rn = 1
                ) cfr on cfr.catalog_id = catalogs.id
            $whereClause
        ", true)->TOTAL ?? 0;

        $queryData = QueryAPI::get("
            select
                *
            from (
                    select
                        rownum as rnum,
                        data.*
                    from
                        (
                            select
                                catalogs.*,
                                e_collections.created_at as created_at_e_collection,
                                e_collections.serial as serial_e_collection,
                                penerbit.id as id_penerbit,
                                penerbit.name as name_penerbit,
                                propinsi.namapropinsi as namapropinsi,
                                kabupaten.namakab as namakab,
                                collectionmedias.name as name_media,
                                cfr.fileurl as fileurl_catalogfiles,
                                cfr.file_size as file_size_catalogfiles,
                                cfr.method as method_catalogfiles
                            from
                                catalogs
                            left join
                                penerbit on penerbit.id = catalogs.penerbit_id
                            left join
                                kabupaten on kabupaten.id = catalogs.city_id
                            left join
                                propinsi on propinsi.id = kabupaten.propinsiid
                            left join
                                collectionmedias on collectionmedias.id = catalogs.collectionmedia_id
                            left join
                                worksheets on worksheets.id = catalogs.worksheet_id
                            left join
                                e_collections on e_collections.id = catalogs.edeposit_col_id
                            left join
                                (
                                    select
                                        cf.catalog_id,
                                        cf.id,
                                        cf.fileurl,
                                        cf.hash,
                                        cf.mime,
                                        cf.file_size,
                                        cf.method
                                    from (
                                        select
                                            cf.catalog_id,
                                            cf.id,
                                            cf.fileurl,
                                            cf.hash,
                                            cf.mime,
                                            cf.file_size,
                                            cf.method,
                                            ROW_NUMBER() OVER (PARTITION BY cf.catalog_id ORDER BY cf.id DESC) as rn
                                        from
                                            catalogfiles cf
                                    ) cf
                                    where
                                        rn = 1
                                ) cfr on cfr.catalog_id = catalogs.id
                            $whereClause
                            $orderBy
                        ) data
                    where
                        rownum <= $length
                )
            where
                rnum > $start
        ");

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
                    $val->ID_PENERBIT . ' | ' . $val->NAME_PENERBIT,
                    Carbon::parse($val->CREATED_AT_E_COLLECTION)->isoFormat('D MMMM Y'),
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
                    Carbon::parse($val->CREATED_AT_E_COLLECTION)->format('d-m-Y') . ', ' . Carbon::parse($val->CREATED_AT_E_COLLECTION)->format('H:i'),
                    Carbon::parse($val->CREATEDATE)->format('d-m-Y') . ', ' . Carbon::parse($val->CREATEDATE)->format('H:i'),
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
                kabupaten k on k.id = c.city_id
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
