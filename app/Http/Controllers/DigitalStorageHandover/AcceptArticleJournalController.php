<?php

namespace App\Http\Controllers\DigitalStorageHandover;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class AcceptArticleJournalController extends Controller
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
                'content' => 'digital-storage-handover.accept-article-journal',
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
            'e.id',
            null,
            'penerbit.name',
            'catalogs.title',
            'collectionmedias.name',
            'catalogs.isbn',
            'e.created_at',
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
                e.deleted_at is null
            ) and
            worksheets.category = '$this->worksheetCategory' 
            and article_title is not null
        ";

        if ($request->title) {
            $title = strtoupper($request->title);
            $whereCondition[] = "upper(e.article_title) like '%$title%'";
        }

        if ($request->executor_id) {
            $whereCondition[] = "e.penerbit_id = $request->executor_id";
        }

        if ($request->province_id) {
            $whereCondition[] = "kabupaten.propinsiid = $request->province_id";
        }

        if ($request->year) {
            $whereCondition[] = "e.publishyear = $request->year";
        }
        $whereCondition[] = "e.collection_media_id = 203";
        /*if ($request->media_id) {
            $whereCondition[] = "e.collection_media_id = $request->media_id";
        }*/

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $whereCondition[] = "(e.received_at >= to_date('$startDate', 'YYYY-MM-DD') and e.received_at < to_date('$endDate', 'YYYY-MM-DD') + 1)";
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
                e_collections e
            left join
                catalogs on catalogs.edeposit_col_id = e.id
            left join
                worksheets on worksheets.id = e.worksheet_id
            where
                (
                   e.deleted_at is null
                ) and
                worksheets.category = '$this->worksheetCategory'
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                e_collections e
            left join
                catalogs on catalogs.edeposit_col_id = e.id
            left join
                penerbit on penerbit.id = e.penerbit_id
            left join
                kabupaten on kabupaten.id = e.kabupaten_id
            left join
                worksheets on worksheets.id = e.worksheet_id
            left join
                collectionmedias on collectionmedias.id = e.collection_media_id
            $whereClause
        ", true)->TOTAL ?? 0;
        $sql = "
            select
                *
            from (
                    select
                        rownum as rnum,
                        data.*
                    from
                        (
                            select 
                                e.id,
                                catalogs.id as cat_id,
                                catalogs.title,
                                catalogs.isbn,
                                e.created_at,
                                e.penerbit_id, e.code,
                                penerbit.name as name_penerbit,
                                collectionmedias.name as name_media,e.edition, e.serial,
                                e.received_at as received_at_e_collection,
                                e.article_title, e.article_contributor, e.article_subject, 
                                e.article_original_link, e.article_doi, e.description,
                                e.article_publish_date, e.deposit
                            from
                                e_collections e
                            left join
                                catalogs on e.id = catalogs.edeposit_col_id
                            left join
                                penerbit on penerbit.id = e.penerbit_id
                            left join
                                kabupaten on kabupaten.id = e.kabupaten_id
                            left join
                                worksheets on worksheets.id = e.worksheet_id
                            left join
                                collectionmedias on collectionmedias.id = e.collection_media_id
                            $whereClause
                            $orderBy
                        ) data
                    where
                        rownum <= $length
                )
            where
                rnum > $start
        ";
        
        $queryData = QueryAPI::get($sql);
        
        if ($queryData) {
            foreach ($queryData as $val) {
                $action = '
                    <a href="' . url('digital-storage-handover/accept-edition/detail/' . $val->ID) . '" class="btn btn-primary btn-sm">
                        <i class="ph-info me-1"></i>
                        Detail
                    </a>
                    <a href="javascript:void(0);" class="btn btn-danger btn-sm mt-1 text-nowrap" onclick="delete(' . $val->ID . ')">
                        <i class="ph-trash-logo me-1"></i>
                        Hapus
                    </a>
                    <a href="javascript:void(0);" class="btn btn-success btn-sm mt-1 text-nowrap" onclick="verifikasi(' . $val->ID . ')">
                        <i class="ph-check me-1"></i>
                        Verifikasi
                    </a>
                ';

                $data[] = [
                    $start + 1,
                    $action,
                    $val->PENERBIT_ID . ' | ' . $val->NAME_PENERBIT,
                    $val->CAT_ID,
                    $val->ARTICLE_TITLE,
                    $val->ARTICLE_CONTRIBUTOR,
                    $val->ARTICLE_SUBJECT,
                    $val->ARTICLE_PUBLISH_DATE,
                    $val->TITLE,
                    $val->NAME_MEDIA,
                    $val->ISBN,
                    $val->EDITION,
                    Carbon::parse($val->RECEIVED_AT_E_COLLECTION)->isoFormat('dddd, D MMMM Y'),
                ];

                $start++;
            }
        }
        //Log::info($data);
        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    }

    public function detail($id)
    {
        $sql = "
            select
                c.*,
                ec.id as E_COLLECTIONS_ID,
                p.name as name_penerbit,
                k.namakab as namakab,
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
                e_collections ec
            left join
                catalogs c on ec.id = c.edeposit_col_id
            left join
                e_collections par on par.id = ec.parent_id
            left join
                penerbit p on p.id = ec.penerbit_id
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
                            cf.e_col_id,
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
                            cf.e_col_id = $id
                    ) where rn = 1
                ) cfr on cfr.e_col_id = ec.id
            left join
                (
                    select
                        *
                    from (
                        select
                            cc.e_col_id,
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
                            cc.e_col_id = $id
                    ) where rn = 1
                ) ccr on ccr.e_col_id = ec.id
            where
                nvl(c.isdelete, 0) = 0
                and ec.id = $id
        ";
        Log::info($sql);
        $collection = QueryAPI::get($sql, true);

        $collectionCategory = [];
        $collectionId = $collection->E_COLLECTIONS_ID ?? 0;

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
        $verifikasi  = QueryAPI::verificationCollection($id, session('username'));
        if($verifikasi) {
            return response()->json([
                    'code' => 200,
                    'message' => 'Sukses diverifikasi'
                ], 200);
        } else {
            return response()->json([
                'code' => 404,
                'message' => 'Gagal diverifikasi'
            ], 404);
        }
    }
}
