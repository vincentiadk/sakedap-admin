<?php

namespace App\Http\Controllers;

use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\LengthAwarePaginator;

class LogAWBController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 10;
        $currentPage = $request->get('page', 1);

        $totalResults = QueryAPI::get("select count(*) as total from log_web_hook_ro", true);
        $total = $totalResults->TOTAL ?? 0;

        $startRow = ($currentPage - 1) * $perPage + 1;
        $endRow = $currentPage * $perPage;

        $date = $request->month ?? date('Y-m');
        $year = date('Y', strtotime($date));
        $month = date('m', strtotime($date));

        $items = QueryAPI::get("
            select
                *
            from (
                    select
                        rownum as rnum,
                        data.*
                    from
                        (
                            select
                                *
                            from
                                log_web_hook_ro
                            where
                                to_char(date_req, 'MM') = '$month' and
                                to_char(date_req, 'YYYY') = '$year'
                        ) data
                    where
                        rownum <= $endRow
                )
            where
                rnum > $startRow
        ");


        $data = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );

        return view('layouts.index', [
            'data' => [
                'data' => $data,
                'content' => 'log-awb',
            ]
        ]);
    }
}
