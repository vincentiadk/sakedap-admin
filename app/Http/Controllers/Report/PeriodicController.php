<?php

namespace App\Http\Controllers\Report;

use App\Helpers\Main;
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
                $condition = [];
                $condition[] = "catalogs.worksheet_id = $w->ID";
                $condition[] = "catalogs.edeposit_col_id is not null";
                $condition[] = "(to_char(catalogs.validatedate, 'YYYY') = '$year' and to_char(catalogs.validatedate, 'MM') = '$month')";

                if (Main::isNotCenterBranch()) {
                    $condition[] = 'penerbit.province_id = ' . session('province_id');
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

            $response[] = [
                'name' => $w->NAME,
                'data' => $data,
            ];
        }

        return response()->json($response);
    }
}
