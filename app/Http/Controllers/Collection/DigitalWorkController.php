<?php

namespace App\Http\Controllers\Collection;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DigitalWorkController extends Controller
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
                'worksheet' => QueryAPI::get("select * from worksheets where category = '$this->worksheetCategory'"),
                'content' => 'collection.digital-work',
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
            'catalogs.id',
            null,
            'penerbit.name',
            'catalogs.title',
            'worksheets.name',
            'catalogs.isbn',
            'catalogs.createdate',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "
            (
                catalogs.isdelete = 0 or
                catalogs.isdelete is null
            ) and
            worksheets.category = '$this->worksheetCategory' and
            catalogs.edeposit_col_id is not null
        ";

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

        if ($request->worksheet_id) {
            $whereCondition[] = "catalogs.worksheet_id = $request->worksheet_id";
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
                worksheets on worksheets.id = catalogs.worksheet_id
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
                                catalogs.id,
                                catalogs.title,
                                catalogs.isbn,
                                catalogs.createdate,
                                catalogs.penerbit_id,
                                penerbit.name as name_penerbit,
                                worksheets.name as name_worksheet
                            from
                                catalogs
                            left join
                                penerbit on penerbit.id = catalogs.penerbit_id
                            left join
                                kabupaten on kabupaten.id = catalogs.city_id
                            left join
                                worksheets on worksheets.id = catalogs.worksheet_id
                            $whereClause
                            $orderBy
                        ) data
                )
            where
                rnum > $start and rnum <= $length
        ");

        if ($queryData) {
            foreach ($queryData as $val) {
                $action = '
                    <a href="' . url('collection/digital-work/detail/' . $val->ID) . '" class="btn btn-primary btn-sm">
                        <i class="ph-info me-1"></i>
                        Detail
                    </a>
                ';

                $data[] = [
                    $start + 1,
                    $action,
                    $val->PENERBIT_ID . ' | ' . $val->NAME_PENERBIT,
                    $val->TITLE,
                    $val->NAME_WORKSHEET,
                    $val->ISBN,
                    Carbon::parse($val->CREATEDATE)->isoFormat('dddd, D MMMM Y'),
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
                catalogs.*,
                penerbit.name as name_penerbit,
                kabupaten.namakab as namakab,
                propinsi.namapropinsi as namapropinsi,
                e_collections.code_type as code_type_e_collection,
                e_collections.serial as serial_e_collection,
                e_collections.received_at as received_at_e_collection,
                e_collections.price as price_e_collection,
                e_collections.jilid as jilid_e_collection,
                e_collections.description as description_e_collection,
                e_collections.jenis_isi as jenis_isi_e_collection,
                e_collections.jenis_wadah as jenis_wadah_e_collection,
                e_collections.jenis_media as jenis_media_e_collection,
                e_collections.currency as currency_e_collection,
                e_collections.jumlah_eks as jumlah_eks_e_collection
            from
                catalogs
            left join
                e_collections on e_collections.id = catalogs.edeposit_col_id
            left join
                penerbit on penerbit.id = catalogs.penerbit_id
            left join
                kabupaten on kabupaten.id = catalogs.city_id
            left join
                propinsi on propinsi.id = kabupaten.propinsiid
            where
                (
                    catalogs.isdelete = 0 or
                    catalogs.isdelete is null
                ) and
                catalogs.id = $id
        ", true);

        $collectionCategory = [];
        $collectionId = $collection->EDEPOSIT_COL_ID ?? 0;
        $catalogId = $collection->ID ?? 0;

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

        $collectionCover = QueryAPI::get("
            select
                *
            from
                catalogcovers
            where
                catalog_id = $catalogId
        ", true);

        $collectionOriginal = QueryAPI::get("
            select
                *
            from
                catalogfiles
            where
                catalog_id = $catalogId
        ", true);

        $collectionPreview = QueryAPI::get("
            select
                *
            from
                e_file_preview
            where
                catalog_id = $catalogId
        ", true);

        $collectionWatermark = QueryAPI::get("
            select
                *
            from
                e_file_access
            where
                catalog_id = $catalogId
        ", true);

        return view('layouts.index', [
            'data' => [
                'worksheet' => QueryAPI::get("select * from worksheets where category is not null"),
                'media' => QueryAPI::get("select * from collectionmedias where isdelete = 0 or isdelete is null"),
                'category' => QueryAPI::get("select * from e_categories where deleted_at is null"),
                'problem' => QueryAPI::get("select * from e_problems where deleted_at is null"),
                'contentType' => QueryAPI::get("select * from fieldrefs where tag = '336'"),
                'containerType' => QueryAPI::get("select * from fieldrefs where tag = '337'"),
                'mediaType' => QueryAPI::get("select * from fieldrefs where tag = '338'"),
                'bigClass' => QueryAPI::get("select * from master_kelas_besar"),
                'collection' => $collection,
                'collectionCategory' => $collectionCategory,
                'collectionContributor' => explode(';', ($collection->AUTHOR ?? '')),
                'collectionCopy' => $collectionCopy,
                'collectionCover' => $collectionCover,
                'collectionOriginal' => $collectionOriginal,
                'collectionPreview' => $collectionPreview,
                'collectionWatermark' => $collectionWatermark,
                'content' => 'collection.digital-work-detail',
                'plugins' => [
                    'select2',
                    'datatable',
                ]
            ]
        ]);
    }
}
