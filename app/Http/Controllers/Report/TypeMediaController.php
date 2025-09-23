<?php

namespace App\Http\Controllers\Report;

use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Jobs\ExcelDownloadBackgroundJob;

class TypeMediaController extends Controller
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
            ExcelDownloadBackgroundJob::dispatch($jobID, 'report-type-media', $payload)
                ->onQueue('report');

            return redirect('report/type-media')->with(['success' => 'Data laporan sedang diproses']);
        }

        return view('layouts.index', [
            'data' => [
                'content' => 'report.type-media',
            ]
        ]);
    }

    public function loadData(Request $request)
    {
        $media = QueryAPI::get("select * from collectionmedias where isdelete = 0 or isdelete is null");
        $year = $request->year;
        $response = [];

        if (empty($media)) {
            return response()->json($response);
        }

        $mediaIds = implode(',', array_column($media, 'ID'));
        $conditions = [
            "c.collectionmedia_id in ($mediaIds)",
            "c.edeposit_col_id is not null",
            "to_char(c.validatedate, 'YYYY') = '$year'",
        ];

        if (Main::isNotCenterBranch()) {
            $conditions[] = 'p.province_id = ' . session('province_id');
        }

        $whereClause = "where " . implode(' and ', $conditions);

        $query = "
            select
                c.collectionmedia_id,
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
                c.collectionmedia_id,
                to_char(c.validatedate, 'MM')
            order by
                c.collectionmedia_id,
                month
        ";

        $results = QueryAPI::get($query);
        $dataByMedia = [];

        foreach ($results ?? [] as $result) {
            $mediaId = $result->WORKSHEET_ID;
            $month = (int) $result->MONTH;

            $dataByMedia[$mediaId][$month] = [
                'catalog_total' => $result->CATALOG_TOTAL,
                'collection_total' => $result->COLLECTION_TOTAL,
            ];
        }

        foreach ($media as $m) {
            $data = [];

            for ($i = 1; $i <= 12; $i++) {
                $catalogTotal = $dataByMedia[$m->ID][$i]['catalog_total'] ?? 0;
                $collectionTotal = $dataByMedia[$m->ID][$i]['collection_total'] ?? 0;
                $data[] = number_format($catalogTotal);
                $data[] = number_format($collectionTotal);
            }

            $response[] = [
                'name' => $m->NAME,
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
