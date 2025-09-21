<?php

namespace App\Http\Controllers\Report;

use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Jobs\ExcelDownloadBackgroundJob;

class WarningController extends Controller
{
    public function index(Request $request)
    {
        if ($request->exported) {
            $jobID = (string) Str::uuid();
            $userId = session('id');
            $userKey = "user:$userId:download";

            $payload = [
                'is_not_center_branch' => Main::isNotCenterBranch(),
                'year' => $request->year,
                'province_id' => session('province_id')
            ];

            Redis::lpush($userKey, $jobID);
            ExcelDownloadBackgroundJob::dispatch($jobID, 'report-warning', $payload)
                ->onQueue('report');

            return redirect('report/warning')->with(['success' => 'Data laporan sedang diproses']);
        }

        $data = [
            'content' => 'report.warning'
        ];

        return view('layouts.index', ['data' => $data]);
    }

    public function loadData(Request $request)
    {
        $year = $request->year;
        $condition[] = "extract(year from e_publisher_warnings.warning_date) = '$year'";

        if (Main::isNotCenterBranch()) {
            $condition[] = 'penerbit.province_id = ' . session('province_id');
        }

        $whereClause = "where " . implode(' and ', $condition);

        $response = QueryAPI::get("
            select
                *
            from (
                select
                    penerbit.name,
                    extract(month from e_publisher_warnings.warning_date) as month
                from
                    penerbit
                join
                    e_publisher_warnings on e_publisher_warnings.publisher_id = penerbit.id
                $whereClause
            )
            pivot (
                count(month)
                for month in (
                    1 as month_1,
                    2 as month_2,
                    3 as month_3,
                    4 as month_4,
                    5 as month_5,
                    6 as month_6,
                    7 as month_7,
                    8 as month_8,
                    9 as month_9,
                    10 as month_10,
                    11 as month_11,
                    12 as month_12
                )
            )
            order by
                name
        ");

        return response()->json($response);
    }
}
