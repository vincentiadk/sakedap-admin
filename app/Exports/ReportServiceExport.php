<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Helpers\QueryAPI;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportServiceExport implements FromView, ShouldAutoSize
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
        $response = [];
        $conditions = ['penerbit.id is not null'];

        if ($request->is_not_center_branch) {
            $conditions[] = 'propinsi.id = ' . $request->province_id;
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

        return view('export.report-service', [
            'request' => $request,
            'data' => $response
        ]);
    }
}
