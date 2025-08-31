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
        $data = [
            'worksheet' => QueryAPI::get("select * from worksheets where category = '$this->worksheetCategory'"),
            'content' => 'collection.digital-work'
        ];

        return view('layouts.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'e_collections.id',
            null,
            'e_collections.deposit',
            'penerbit.name',
            'e_collections.title',
            'worksheets.name',
            'e_collections.code',
            'e_collections.validated_date',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = $request->search['value'];

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "
            e_collections.status = 2 and
            (
                e_collections.deleted_at is null and
                worksheets.category = '$this->worksheetCategory'
            ) and
            (
                e_collections.parent_id = 0 or
                e_collections.parent_id is null
            )";

        if ($request->title) {
            $whereCondition[] = "e_collections.title like '%$search%'";
        }

        if ($request->publisher_id) {
            $whereCondition[] = "e_collections.penerbit_id = $request->publisher_id";
        }

        if ($request->province_id) {
            $whereCondition[] = "kabupaten.propinsiid = $request->province_id";
        }

        if ($request->year) {
            $whereCondition[] = "e_collections.publication_year = $request->year";
        }

        if ($request->worksheet_id) {
            $whereCondition[] = "e_collections.worksheet_id = $request->worksheet_id";
        }

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $whereCondition[] = "(e_collections.validatedate >= date '$startDate' and e_collections.validatedate <= date '$endDate')";
        }

        if ($search) {
            $terms = [];

            foreach ($column as $c) {
                if ($c) {
                    $terms[] = "$c like '%$search%'";
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
                e_collections
            left join
                worksheets on worksheets.id = e_collections.worksheet_id
            where
                e_collections.status = 2 and
                (
                    e_collections.parent_id = 0 or
                    e_collections.parent_id is null
                ) and
                (
                    e_collections.deleted_at is null and
                    worksheets.category = '$this->worksheetCategory'
                )
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                e_collections
            join
                penerbit on penerbit.id = e_collections.penerbit_id
            join
                kabupaten on kabupaten.id = e_collections.city_id
            left join
                worksheets on worksheets.id = e_collections.worksheet_id
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
                                e_collections.*,
                                penerbit.name as name_penerbit,
                                worksheets.name as name_worksheet
                            from
                                e_collections
                            join
                                penerbit on penerbit.id = e_collections.penerbit_id
                            join
                                kabupaten on kabupaten.id = e_collections.city_id
                            left join
                                worksheets on worksheets.id = e_collections.worksheet_id
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
                    $val->DEPOSIT,
                    $val->NAME_PENERBIT,
                    $val->TITLE,
                    $val->NAME_WORKSHEET,
                    $val->CODE,
                    Carbon::parse($val->VALIDATED_AT)->format('d/m/Y'),
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
                e_collections.*,
                penerbit.name as name_penerbit,
                kabupaten.namakab as namakab,
                propinsi.namapropinsi as namapropinsi
            from
                e_collections
            join
                penerbit on penerbit.id = e_collections.penerbit_id
            join
                kabupaten on kabupaten.id = e_collections.kabupaten_id
            join
                propinsi on propinsi.id = kabupaten.propinsiid
            where
                (
                    e_collections.parent_id = 0 or
                    e_collections.parent_id is null
                ) and
                e_collections.id = $id and
                e_collections.deleted_at is null and
                e_collections.status = 2
        ", true);

        $collectionCategory = [];
        $dataCollectionCategory = QueryAPI::get("
            select
                *
            from
                e_collection_categories
            where
                collection_id = $id
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
                parent_id = $id and
                deleted_at is null
        ");

        $collectionCover = QueryAPI::get("
            select
                *
            from
                catalogcovers
            where
                e_col_id = $id
        ");

        $collectionContent = QueryAPI::get("
            select
                *
            from
                catalogfiles
            where
                e_col_id = $id
        ");

        $data = [
            'worksheet' => QueryAPI::get("select * from worksheets where category is not null"),
            'media' => QueryAPI::get("select * from collectionmedias where isdelete != 1"),
            'category' => QueryAPI::get("select * from e_categories where deleted_at is null"),
            'contributor' => QueryAPI::get("select * from e_contributors where show = 1 and deleted_at is null"),
            'problem' => QueryAPI::get("select * from e_problems where deleted_at is null"),
            'collection' => $collection,
            'collectionCategory' => $collectionCategory,
            'collectionContributor' => explode(';', ($collection->DESCRIPTION ?? '')),
            'collectionCopy' => $collectionCopy,
            'collectionCover' => $collectionCover,
            'collectionContent' => $collectionContent,
            'content' => 'collection.digital-work-detail'
        ];

        return view('layouts.index', ['data' => $data]);
    }
}
