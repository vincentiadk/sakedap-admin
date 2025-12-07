<?php

namespace App\Http\Controllers\Report;

use Carbon\Carbon;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DigitalManageController extends Controller
{
    private $tableName;

    public function __construct()
    {
        $this->tableName = "'E_COLLECTIONS','E_PROBLEMS','CATALOGS','E_CATEGORIES'";
    }

    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'action' => QueryAPI::get("select distinct(lower(action)) as name from historydata where tablename in ($this->tableName)") ?? [],
                'actionBy' => QueryAPI::get("select distinct(actionby) as name from historydata where tablename in ($this->tableName)") ?? [],
                'tableName' => QueryAPI::get("select distinct(tablename) as name from historydata where tablename in ($this->tableName)") ?? [],
                'content' => 'report.digital-manage',
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
            'id',
            'action',
            'tablename',
            'actionby',
            'actiondate',
            'actionterminal',
            'note',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "tablename in ($this->tableName)";

        if ($search) {
            $terms = [];

            foreach ($column as $c) {
                if ($c) {
                    $terms[] = "upper($c) like '%$search%'";
                }
            }

            $whereCondition[] = '(' . implode(' or ', $terms) . ')';
        }

        if ($request->action) {
            $whereCondition[] = "lower(action) = lower('$request->action')";
        }

        if ($request->action_by) {
            $whereCondition[] = "actionby = '$request->action_by'";
        }

        if ($request->table_name) {
            $whereCondition[] = "tablename = '$request->table_name'";
        }

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $whereCondition[] = "(actiondate >= to_date('$startDate', 'YYYY-MM-DD') and actiondate < to_date('$endDate', 'YYYY-MM-DD') + 1)";
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
                historydata
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                historydata
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
                                *
                            from
                                historydata
                            $whereClause
                            $orderBy
                        ) data
                )
            where
                rnum > $start and rownum <= $length
        ");

        if ($queryData) {
            foreach ($queryData as $val) {
                $data[] = [
                    $start + 1,
                    ucwords($val->ACTION),
                    $val->TABLENAME,
                    $val->ACTIONBY,
                    Carbon::parse($val->ACTIONDATE)->isoFormat('dddd, D MMMM Y'),
                    $val->ACTIONTERMINAL,
                    $val->NOTE,
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
