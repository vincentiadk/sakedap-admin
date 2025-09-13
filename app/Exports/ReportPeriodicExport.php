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
        $result = [];

        foreach (($worksheet ?? []) as $w) {
            $data = [];

            for ($i = 1; $i <= 12; $i++) {
                $month = sprintf('%02s', $i);
                $condition = [];
                $condition[] = "catalogs.worksheet_id = $w->ID";
                $condition[] = "catalogs.edeposit_col_id is not null";
                $condition[] = "(to_char(catalogs.validatedate, 'YYYY') = '$year' and to_char(catalogs.validatedate, 'MM') = '$month')";

                if ($request->is_not_center_branch) {
                    $condition[] = 'penerbit.province_id = ' . $provinceId;
                }

                $whereClause = "where " . implode(' and ', $condition);

                $catalog = QueryAPI::get("
                    select
                        count(*) as total
                    from
                        catalogs
                    left join
                        penerbit on penerbit.id = catalogs.penerbit_id
                    $whereClause
                ", true);

                $data[] = number_format($catalog->TOTAL ?? 0);
            }

            $result[] = [
                'name' => $w->NAME,
                'data' => $data,
            ];
        }

        return view('export.report-periodic', [
            'request' => $request,
            'data' => $result ?? []
        ]);
    }
}
