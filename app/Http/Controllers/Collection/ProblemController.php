<?php

namespace App\Http\Controllers\Collection;

use Carbon\Carbon;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProblemController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'worksheet' => QueryAPI::get("select * from worksheets where category is not null"),
                'content' => 'collection.problem',
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
            'e_collections.id',
            'penerbit.name',
            'e_collections.title',
            'worksheets.name',
            'e_collections.code',
            null,
            'e_collections.problem',
            'e_collections.rejected_at',
            'users.fullname',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = $request->search['value'];

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = '(e_collections.status = 3 and e_collections.deleted_at is null) and (e_collections.parent_id = 0 or e_collections.parent_id is null)';

        if ($request->title) {
            $whereCondition[] = "(e_collections.title_ori like '%$search%' or e_collections.title like '%$search%')";
        }

        if ($request->executor_id) {
            $whereCondition[] = "e_collections.penerbit_id = $request->executor_id";
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

            $whereCondition[] = "(e_collections.updated_at >= to_date('$startDate', 'YYYY-MM-DD') and e_collections.updated_at < to_date('$endDate', 'YYYY-MM-DD') + 1)";
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
            where
                (
                    parent_id = 0 or
                    parent_id is null
                ) and
                (
                    status = 3 and
                    deleted_at is null
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
                kabupaten on kabupaten.id = e_collections.kabupaten_id
            left join
                worksheets on worksheets.id = e_collections.worksheet_id
            left join
                users on users.id = e_collections.rejected_by
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
                                worksheets.name as name_worksheet,
                                users.fullname as name_user
                            from
                                e_collections
                            join
                                penerbit on penerbit.id = e_collections.penerbit_id
                            join
                                kabupaten on kabupaten.id = e_collections.kabupaten_id
                            left join
                                worksheets on worksheets.id = e_collections.worksheet_id
                            left join
                                users on users.id = e_collections.rejected_by
                            $whereClause
                            $orderBy
                        ) data
                )
            where
                rnum > $start and rnum <= $length
        ");

        if ($queryData) {
            foreach ($queryData as $val) {
                $listProblem = '';
                $dataProblem = QueryAPI::get("
                    select
                        e_problems.name as name_problem
                    from
                        e_collection_problems
                    join
                        e_problems on e_problems.id = e_collection_problems.problem_id
                    where
                        e_collection_problems.collection_id = $val->ID
                ");

                if ($dataProblem) {
                    foreach ($dataProblem as $dp) {
                        $listProblem .= '<li>' . $dp->NAME_PROBLEM . '</li>';
                    }
                }

                $listProblem = '
                    <ul class="p-0 mb-0 ps-2">
                        ' . $listProblem . '
                    </ul>
                ';

                $data[] = [
                    $start + 1,
                    $val->NAME_PENERBIT,
                    ($val->TITLE ?? $val->TITLE_ORI),
                    $val->NAME_WORKSHEET,
                    $val->CODE,
                    $listProblem,
                    $val->PROBLEM,
                    Carbon::parse($val->REJECTED_AT)->format('d/m/Y'),
                    $val->NAME_USER,
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
}
