<?php

namespace App\Http\Controllers\Collection;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;

class LabelController extends Controller
{
    private $worksheetCategory;

    public function __construct()
    {
        $this->worksheetCategory = "('" . Main::COLLECTION_ANALOG . "','" . Main::COLLECTION_PRINTED . "')";
    }

    public function index()
    {
        $data = [
            'worksheet' => QueryAPI::get("select * from worksheets where category in $this->worksheetCategory"),
            'content' => 'collection.label'
        ];

        return view('layouts.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $column = [
            null,
            'collections.id',
            'worksheets.name',
            'collections.nomorbarcode',
            'collections.mark_national',
            'collections.mark_province',
            'penerbit.name',
            'collections.title',
            'collections.isbn',
            'location_library.name',
            'branchs.name',
            'collections.updateby',
            'collections.createby',
            'collections.acquireddate',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = $request->search['value'];

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "(collections.isdelete = 0 or collections.isdelete is null) and worksheets.category in $this->worksheetCategory";

        if ($request->title) {
            $whereCondition[] = "collections.title like '%$search%'";
        }

        if ($request->publisher_id) {
            $whereCondition[] = "collections.penerbit_id = $request->publisher_id";
        }

        if ($request->province_id) {
            $whereCondition[] = "kabupaten.propinsiid = $request->province_id";
        }

        if ($request->year) {
            $whereCondition[] = "collections.publishyear = $request->year";
        }

        if ($request->worksheet_id) {
            $whereCondition[] = "collections.worksheet_id = $request->worksheet_id";
        }

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $whereCondition[] = "(collections.acquireddate >= date '$startDate' and collections.acquireddate <= date '$endDate')";
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
                collections
            join
                e_collection_copies on e_collection_copies.id = collections.edeposit_cc_id
            left join
                worksheets on worksheets.id = collections.worksheet_id
            where
                (
                    collections.isdelete = 0 or
                    collections.isdelete is null
                ) and
                worksheets.category in $this->worksheetCategory
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                collections
            join
                e_collection_copies on e_collection_copies.id = collections.edeposit_cc_id
            left join
                penerbit on penerbit.id = collections.penerbit_id
            left join
                kabupaten on kabupaten.id = collections.city_id
            left join
                worksheets on worksheets.id = collections.worksheet_id
            left join
                location_library on location_library.id = collections.loc_lib_id
            left join
                branchs on branchs.id = location_library.branch_id
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
                                collections.id,
                                collections.title,
                                collections.isbn,
                                collections.acquireddate,
                                collections.updateby,
                                collections.createby,
                                collections.nomorbarcode,
                                collections.mark_national,
                                collections.mark_province,
                                penerbit.name as name_penerbit,
                                worksheets.name as name_worksheet,
                                location_library.name as name_location_library,
                                branchs.name as name_branch
                            from
                                collections
                            join
                                e_collection_copies on e_collection_copies.id = collections.edeposit_cc_id
                            left join
                                penerbit on penerbit.id = collections.penerbit_id
                            left join
                                kabupaten on kabupaten.id = collections.city_id
                            left join
                                worksheets on worksheets.id = collections.worksheet_id
                            left join
                                location_library on location_library.id = collections.loc_lib_id
                            left join
                                branchs on branchs.id = location_library.branch_id
                            $whereClause
                            $orderBy
                        ) data
                )
            where
                rnum > $start and rnum <= $length
        ");

        if ($queryData) {
            foreach ($queryData as $val) {
                $inputHidden = '
                    <input type="hidden" name="data" data-id="' . $val->ID . '" data-title="' . $val->TITLE . '" data-code="' . $val->NOMORBARCODE . '" data-mark-national="' . $val->MARK_NATIONAL . '" data-mark-province="' . $val->MARK_PROVINCE . '">
                ';

                $data[] = [
                    $inputHidden,
                    $start + 1,
                    $val->NAME_WORKSHEET,
                    $val->NOMORBARCODE,
                    $val->MARK_NATIONAL,
                    $val->MARK_PROVINCE,
                    $val->NAME_PENERBIT,
                    $val->TITLE,
                    $val->ISBN,
                    $val->NAME_LOCATION_LIBRARY,
                    $val->NAME_BRANCH,
                    $val->UPDATEBY,
                    $val->CREATEBY,
                    Carbon::parse($val->ACQUIREDDATE)->format('d/m/Y'),
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

    public function print(Request $request, $param)
    {
        $id = collect($request->id)->map(function ($value, $key) {
            return intval($value);
        })->all();

        $dataId = collect($id)->implode(',');
        $listCollection = QueryAPI::get("
            select
                collections.*,
                location_library.name as name_location_library
            from
                collections
            left join
                location_library on location_library.id = collections.loc_lib_id
            where
                collections.id in ($dataId)
        ");

        $pdf = Pdf::setOptions([
            'adminUsername' => session('name'),
        ])->loadView('pdf.label', [
            'title' => 'Cetak Label',
            'data' => $listCollection,
            'param' => $param
        ])->setPaper([0, 0, 283.464566929, 141.732283465])->setWarnings(false);

        return $pdf->stream('label-' . $param . '-' . date('YmdHis') . '.pdf');
    }
}
