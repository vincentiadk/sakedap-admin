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

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->exported) {
            $jobID = (string) Str::uuid();
            $userId = session('id');
            $userKey = "user:$userId:download";

            $payload = [
                'is_not_center_branch' => !Main::isSuperAdmin(),
                'year' => $request->year,
                'province_id' => session('province_id')
            ];

            Redis::lpush($userKey, $jobID);
            ExcelDownloadBackgroundJob::dispatch($jobID, 'report-service', $payload)
                ->onQueue('report');

            return redirect('report/service')->with(['success' => 'Data laporan sedang diproses']);
        }

        return view('layouts.index', [
            'data' => [
                'content' => 'report.service',
            ]
        ]);
    }

    public function loadData(Request $request)
    {
        $year = $request->year;
        $response = [];
        $conditions = ['penerbit.id is not null'];

        if (!Main::isSuperAdmin() && !Main::isPerpusnas()) {
            $conditions[] = 'propinsi.id = ' . session('province_id');
        }

        $whereClause = 'where ' . implode(' AND ', $conditions);

        $sqlLetter = "
            select
                extract(month from collections.createdate) as month_number,
                sum(case when letter.jasa_pengiriman_id = 1 then 1 else 0 end) as total_direct,
                count(distinct letter_detail.letter_detail_id) as total_delivery
            from
                collections
            left join
                letter on letter.letter_id = collections.letter_id
            left join
                letter_detail on letter_detail.letter_detail_id = collections.letter_detail_id
            left join
                penerbit on penerbit.id = collections.penerbit_id
            left join
                propinsi on propinsi.id = penerbit.province_id
            $whereClause and
                extract(year from collections.createdate) = $year
            group by
                extract(month from collections.createdate)
        ";

        $resultLetter = QueryAPI::get($sqlLetter);

        $sqlECollection = "
            select
                extract(month from e_collections.created_at) as month_number,
                count(e_collections.id) as total_independent
            from
                e_collections
            left join
                penerbit on penerbit.id = e_collections.publisher_id
            left join
                propinsi on propinsi.id = penerbit.province_id
            $whereClause and
                extract(year from e_collections.created_at) = $year and
                e_collections.manual = 1 and
                e_collections.received_at is not null
            group by
                extract(month from e_collections.created_at)
        ";

        $resultECollection = QueryAPI::get($sqlECollection);

        $monthlyData = array_fill(1, 12, [
            'total_direct' => 0,
            'total_independent' => 0,
            'total_delivery' => 0,
        ]);

        foreach ($resultLetter ?? [] as $row) {
            $month = $row->MONTH_NUMBER;
            $monthlyData[$month]['total_direct'] = $row->TOTAL_DIRECT;
            $monthlyData[$month]['total_delivery'] = $row->TOTAL_DELIVERY;
        }

        foreach ($resultECollection ?? [] as $row) {
            $month = $row->MONTH_NUMBER;
            $monthlyData[$month]['total_independent'] = $row->TOTAL_INDEPENDENT;
        }

        for ($i = 1; $i <= 12; $i++) {
            $month = sprintf('%02d', $i);
            $data = [
                $monthlyData[$i]['total_direct'],
                $monthlyData[$i]['total_independent'],
                $monthlyData[$i]['total_delivery'],
            ];

            $response[] = [
                'name' => Carbon::parse($year . '-' . $month)->isoFormat('MMMM'),
                'data' => $data,
            ];
        }

        return response()->json($response);
    }
}
