<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Helpers\QueryAPI;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportPostageExport implements FromView, ShouldAutoSize
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
        $explodeDate = explode(' - ', $request->date);
        $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
        $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');
        $condition = [];
        $whereClause = '';

        if ($request->is_not_center_branch) {
            $condition[] = 'propinsi.id = ' . session('province_id');
        }

        if ($condition) {
            $whereClause = "where " . implode(' and ', $condition);
        }

        $data = QueryAPI::get("
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

        return view('export.report-postage', [
            'request' => $request,
            'data' => $data
        ]);
    }
}
