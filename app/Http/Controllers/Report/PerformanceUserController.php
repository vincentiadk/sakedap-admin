<?php

namespace App\Http\Controllers\Report;

use Carbon\Carbon;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PerformanceUserController extends Controller
{
    private $tableName;

    public function __construct()
    {
        $this->tableName = "
            'catalogs',
            'collections',
            'e_collections'
        ";
    }

    public function index()
    {
        $data = [
            'action' => QueryAPI::get("select distinct(lower(action)) as name from historydata where lower(tablename) in ($this->tableName)"),
            'actionBy' => QueryAPI::get("select distinct(actionby) as name from historydata where lower(tablename) in ($this->tableName)"),
            'content' => 'report.performance-user'
        ];

        return view('layouts.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'id',
            'action',
            null,
            'actionby',
            'actiondate',
            'actionterminal',
            'note',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = $request->search['value'];

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "lower(tablename) in ($this->tableName)";

        if ($search) {
            $terms = [];

            foreach ($column as $c) {
                if ($c) {
                    $terms[] = "$c like '%$search%'";
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

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $whereCondition[] = "(actiondate >= date '$startDate' and actiondate <= date '$endDate')";
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
            where
                lower(tablename) in ($this->tableName)
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
                rnum > $start and rnum <= $length
        ");

        if ($queryData) {
            foreach ($queryData as $val) {
                $tableName = strtolower($val->TABLENAME);
                $tableId = (int) $val->IDREF;
                $collection = QueryAPI::get("select $tableName.title from $tableName where id = $tableId", true);

                $data[] = [
                    $start + 1,
                    ucwords($val->ACTION),
                    $collection->TITLE ?? null,
                    $val->ACTIONBY,
                    Carbon::parse($val->ACTIONDATE)->format('d/m/Y'),
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
