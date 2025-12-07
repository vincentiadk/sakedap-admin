<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Jobs\ExcelDownloadBackgroundJob;

class LogActivityController extends Controller
{
    public function index(Request $request)
    {
        if ($request->exported) {
            $jobID = (string) Str::uuid();
            $userId = session('id');
            $userKey = "user:$userId:download";

            $payload = [
                'is_not_center_branch' => Main::isNotSuperAdmin(),
                'action' => $request->action,
                'action_by' => $request->action_by,
                'date' => $request->date
            ];

            Redis::lpush($userKey, $jobID);
            ExcelDownloadBackgroundJob::dispatch($jobID, 'report-log', $payload)
                ->onQueue('report');

            return redirect('report/log')->with(['success' => 'Data laporan sedang diproses']);
        }

        return view('layouts.index', [
            'data' => [
                'action' => QueryAPI::get("select distinct(lower(action)) as name from historydata") ?? [],
                'actionBy' => QueryAPI::get("select distinct(actionby) as name from historydata") ?? [],
                'tableName' => QueryAPI::get("select distinct(tablename) as name from historydata") ?? [],
                'content' => 'log-activity',
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
        $whereCondition = [];

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
