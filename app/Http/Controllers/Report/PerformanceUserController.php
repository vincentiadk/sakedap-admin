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

    public function index(Request $request)
    {
        if ($request->exported) {
            $jobID = (string) Str::uuid();
            $userId = session('id');
            $userKey = "user:$userId:download";

            $payload = [
                'is_not_center_branch' => Main::isNotCenterBranch(),
                'action' => $request->action,
                'action_by' => $request->action_by,
                'date' => $request->date
            ];

            Redis::lpush($userKey, $jobID);
            ExcelDownloadBackgroundJob::dispatch($jobID, 'report-performance-user', $payload)
                ->onQueue('report');

            return redirect('report/performance-user')->with(['success' => 'Data laporan sedang diproses']);
        }

        return view('layouts.index', [
            'data' => [
                'action' => QueryAPI::get("select distinct(lower(action)) as name from historydata where lower(tablename) in ($this->tableName)"),
                'actionBy' => QueryAPI::get("select distinct(actionby) as name from historydata where lower(tablename) in ($this->tableName)"),
                'content' => 'report.performance-user',
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
            'historydata.id',
            'historydata.action',
            null,
            'historydata.actionby',
            'historydata.actiondate',
            'historydata.actionterminal',
            'historydata.note',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = $request->search['value'];

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "lower(historydata.tablename) in ($this->tableName)";

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
            $whereCondition[] = "lower(historydata.action) = lower('$request->action')";
        }

        if ($request->action_by) {
            $whereCondition[] = "historydata.actionby = '$request->action_by'";
        }

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $whereCondition[] = "(historydata.actiondate >= to_date('$startDate', 'YYYY-MM-DD') and historydata.actiondate < to_date('$endDate', 'YYYY-MM-DD') + 1)";
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
                                historydata.*,
                                coalesce(
                                    cast(catalogs.title as varchar2(4000)),
                                    cast(collections.title as varchar2(4000)),
                                    cast(e_collections.title as varchar2(4000))
                                ) as title
                            from
                                historydata
                            left join
                                catalogs on historydata.idref = catalogs.id and lower(historydata.tablename) = 'catalogs'
                            left join
                                collections on historydata.idref = collections.id and lower(historydata.tablename) = 'collections'
                            left join
                                e_collections on historydata.idref = e_collections.id and lower(historydata.tablename) = 'e_collections'
                            $whereClause
                            $orderBy
                        ) data
                )
            where
                rnum > $start and rnum <= $length
        ");

        if ($queryData) {
            foreach ($queryData as $val) {
                $data[] = [
                    $start + 1,
                    ucwords($val->ACTION),
                    $val->TITLE,
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
