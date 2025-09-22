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

class PostageController extends Controller
{
    public function index(Request $request)
    {
        if ($request->exported) {
            $jobID = (string) Str::uuid();
            $userId = session('id');
            $userKey = "user:$userId:download";

            $payload = [
                'is_not_center_branch' => Main::isNotCenterBranch(),
                'date' => $request->date,
                'province_id' => session('province_id')
            ];

            Redis::lpush($userKey, $jobID);
            ExcelDownloadBackgroundJob::dispatch($jobID, 'report-postage', $payload)
                ->onQueue('report');

            return redirect('report/postage')->with(['success' => 'Data laporan sedang diproses']);
        }

        return view('layouts.index', [
            'data' => [
                'content' => 'report.postage',
                'plugins' => [
                    'daterangepicker',
                ]
            ]
        ]);
    }

    public function loadData(Request $request)
    {
        $explodeDate = explode(' - ', $request->date);
        $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
        $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');
        $condition = [];
        $whereClause = '';

        if (Main::isNotCenterBranch()) {
            $condition[] = 'propinsi.id = ' . session('province_id');
        }

        if ($condition) {
            $whereClause = "where " . implode(' and ', $condition);
        }

        $response = QueryAPI::get("
            select
                propinsi.namapropinsi as province,
                to_char(nvl(sum(letter.berat) / 1000, 0), 'FM999G999G990D00', 'NLS_NUMERIC_CHARACTERS=''.,''') as weight,
                to_char(nvl(min(letter.biaya_kirim), 0), 'FM999G999G990D00', 'NLS_NUMERIC_CHARACTERS=''.,''') as postage_min,
                to_char(nvl(max(letter.biaya_kirim), 0), 'FM999G999G990D00', 'NLS_NUMERIC_CHARACTERS=''.,''') as postage_max,
                to_char(nvl(avg(letter.biaya_kirim), 0), 'FM999G999G990D00', 'NLS_NUMERIC_CHARACTERS=''.,''') as postage_avg,
                to_char(nvl(sum(letter.jumlah_paket), 0), 'FM999G999G990D00', 'NLS_NUMERIC_CHARACTERS=''.,''') as package
            from
                propinsi
            left join
                penerbit ON penerbit.province_id = propinsi.id
            left join
                letter ON letter.penerbit_id = penerbit.id and
                (
                    letter.letter_date >= date '$startDate' and
                    letter.letter_date <= date '$endDate'
                )
            $whereClause
            group by
                propinsi.namapropinsi
            order by
                propinsi.namapropinsi
        ");

        return response()->json($response);
    }
}
