<?php

namespace App\Exports;

use App\Helpers\QueryAPI;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportTypeMediaExport implements FromView, ShouldAutoSize
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
        $media = QueryAPI::get("select * from collectionmedias where isdelete = 0 or isdelete is null");
        $provinceId = $request->province_id;
        $year = $request->year;
        $tableData = [];

        if (empty($media)) {
            return view('export.report-type-media', [
                'request' => $request,
                'data' => $tableData
            ]);
        }

        $mediaIds = implode(',', array_column($media, 'ID'));
        $conditions = [
            "c.collectionmedia_id in ($mediaIds)",
            "c.edeposit_col_id is not null",
            "to_char(c.validatedate, 'YYYY') = '$year'",
        ];

        if ($request->is_not_center_branch) {
            $conditions[] = 'p.province_id = ' . $provinceId;
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

        foreach ($media as $w) {
            $data = [];

            for ($i = 1; $i <= 12; $i++) {
                $catalogTotal = $dataByMedia[$w->ID][$i]['catalog_total'] ?? 0;
                $collectionTotal = $dataByMedia[$w->ID][$i]['collection_total'] ?? 0;
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

        return view('export.report-type-media', [
            'request' => $request,
            'data' => $tableData
        ]);
    }
}
