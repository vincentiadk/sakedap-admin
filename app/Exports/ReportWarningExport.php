<?php

namespace App\Exports;

use App\Helpers\QueryAPI;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportWarningExport implements FromView, ShouldAutoSize
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
        $year = $request->year;
        $condition[] = "extract(year from e_publisher_warnings.warning_date) = '$year'";

        if ($request->is_not_center_branch) {
            $condition[] = 'penerbit.province_id = ' . session('province_id');
        }

        $whereClause = "where " . implode(' and ', $condition);

        $data = QueryAPI::get("
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

        return view('export.report-warning', [
            'request' => $request,
            'data' => $data
        ]);
    }
}
