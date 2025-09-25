<?php

namespace App\Exports;

use App\Helpers\QueryAPI;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportPeriodicExport implements FromView, ShouldAutoSize
{
    use Exportable;

    /**
     * request
     *
     * @var mixed
     */
    protected $request;

    /**
     * __construct
     *
     * @param  mixed $request
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;

        ini_set('memory_limit', '-1');
    }

    /**
     * view
     *
     * @return View
     */
    public function view(): View
    {
        $request = (object) $this->request;
        $worksheet = QueryAPI::get("select * from worksheets where category is not null");
        $provinceId = $request->province_id;
        $year = $request->year;
        $tableData = [];

        if (empty($worksheet)) {
            return view('export.report-periodic', [
                'request' => $request,
                'data' => $tableData
            ]);
        }

        $worksheetIds = implode(',', array_column($worksheet, 'ID'));
        $conditions = [
            "c.worksheet_id in ($worksheetIds)",
            "c.edeposit_col_id is not null",
            "to_char(c.createdate, 'YYYY') = '$year'",
        ];

        if ($request->is_not_center_branch) {
            $conditions[] = 'p.province_id = ' . $provinceId;
        }

        $whereClause = "where " . implode(' and ', $conditions);

        $query = "
            select
                c.worksheet_id,
                to_char(c.createdate, 'MM') as month,
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
                to_char(c.createdate, 'MM')
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

        return view('export.report-periodic', [
            'request' => $request,
            'data' => $tableData
        ]);
    }
}
