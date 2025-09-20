<?php

namespace App\Http\Controllers\Report;

use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Jobs\ExcelDownloadBackgroundJob;

class PeriodicController extends Controller
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
            ExcelDownloadBackgroundJob::dispatch($jobID, 'report-periodic', $payload)
                ->onQueue('report');

            return redirect('report/periodic')->with(['success' => 'Data laporan sedang diproses']);
        }

        $data = [
            'worksheet' => QueryAPI::get("select * from worksheets where category is not null"),
            'content' => 'report.periodic'
        ];

        return view('layouts.index', ['data' => $data]);
    }

    public function loadData(Request $request)
    {
        $worksheet = QueryAPI::get("select * from worksheets where category is not null");
        $year = $request->year;
        $response = [];

        if (empty($worksheet)) {
            return response()->json($response);
        }

        $worksheetIds = implode(',', array_column($worksheet, 'ID'));
        $conditions = [
            "c.worksheet_id in ($worksheetIds)",
            "c.edeposit_col_id is not null",
            "to_char(c.validatedate, 'YYYY') = '$year'",
        ];

        if (Main::isNotCenterBranch()) {
            $conditions[] = 'p.province_id = ' . session('province_id');
        }

        $whereClause = "where " . implode(' and ', $conditions);

        $query = "
            select
                c.worksheet_id,
                to_char(c.validatedate, 'MM') as month,
                count(distinct c.id) as catalog_total,
                count(coll.id) as collection_total
            from
                catalogs c
            left join
                collections coll on coll.catalog_id = c.id
            left join
                penerbit p on p.id = c.penerbit_id
            $whereClause
            group by
                c.worksheet_id,
                to_char(c.validatedate, 'MM')
            order by
                c.worksheet_id,
                month
        ";

        $results = QueryAPI::get($query);
        $dataByWorksheet = [];

        foreach ($results ?? [] as $result) {
            $worksheetId = $result->WORKSHEET_ID;
            $month = (int) $result->MONTH;

            $dataByWorksheet[$worksheetId][$month] = [
                'catalog_total' => $result->CATALOG_TOTAL,
                'collection_total' => $result->COLLECTION_TOTAL,
            ];
        }

        foreach ($worksheet as $w) {
            $data = [];

            for ($i = 1; $i <= 12; $i++) {
                $catalogTotal = $dataByWorksheet[$w->ID][$i]['catalog_total'] ?? 0;
                $collectionTotal = $dataByWorksheet[$w->ID][$i]['collection_total'] ?? 0;
                $data[] = number_format($catalogTotal);
                $data[] = number_format($collectionTotal);
            }

            $response[] = [
                'name' => $w->NAME,
                'data' => $data,
            ];
        }

        foreach ($response as $item) {
            $rowData = [$item['name']];

            for ($i = 0; $i < count($item['data']); $i += 2) {
                $rowData[] = $item['data'][$i];
                $rowData[] = $item['data'][$i + 1];
            }

            $tableData[] = $rowData;
        }

        return response()->json($tableData);
    }
}
