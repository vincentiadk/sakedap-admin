<?php

namespace App\Http\Controllers\Report;

use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PeriodicController extends Controller
{
    public function index()
    {
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

        foreach (($worksheet ?? []) as $w) {
            $data = [];

            for ($i = 1; $i <= 12; $i++) {
                $month = sprintf('%02s', $i);
                $catalog = QueryAPI::get("
                    select
                        count(*) as total
                    from
                        catalogs
                    where
                        worksheet_id = $w->ID and
                        edeposit_col_id is not null and
                        (
                            to_char(validatedate, 'YYYY') = '$year' and
                            to_char(validatedate, 'MM') = '$month'
                        )
                ", true);

                $data[] = number_format($catalog->TOTAL ?? 0);
            }

            $response[] = [
                'name' => $w->NAME,
                'data' => $data,
            ];
        }

        return response()->json($response);
    }
}
