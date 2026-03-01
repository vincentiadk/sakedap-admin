<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    private $worksheetCategoryAnalog;
    private $worksheetCategoryDigital;
    private $worksheetCategoryPrinted;

    public function __construct()
    {
        $this->worksheetCategoryAnalog = Main::COLLECTION_ANALOG;
        $this->worksheetCategoryDigital = Main::COLLECTION_DIGITAL;
        $this->worksheetCategoryPrinted = Main::COLLECTION_PRINTED;
    }

    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'content' => 'dashboard',
                'plugins' => [
                    'daterangepicker',
                    'echart',
                ]
            ]
        ]);
    }

    public function dataCollectionStatus(Request $request)
    {
        $date = $request->date;
        $explodeDate = explode(' - ', $date);
        $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
        $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

        $response = [];
        $condition = ["(e_collections.created_at >= to_date('$startDate', 'YYYY-MM-DD') and e_collections.created_at < to_date('$endDate', 'YYYY-MM-DD') + 1)"];

        if (!Main::isSuperAdmin() && !Main::isPerpusnas()) {
            $condition[] = "penerbit.province_id = " . session('province_id');
        }

        $whereClause = !empty($condition) ? "where " . implode(' and ', $condition) : '';

        $data = QueryAPI::get("
            select
                nvl(sum(case when e_collections.status = '1' then 1 else 0 end), 0) as total_1,
                nvl(sum(case when e_collections.status = '2' then 1 else 0 end), 0) as total_2,
                nvl(sum(case when e_collections.status = '3' then 1 else 0 end), 0) as total_3,
                nvl(sum(case when e_collections.status = '5' then 1 else 0 end), 0) as total_5
            from
                e_collections
            join
                penerbit on penerbit.id = e_collections.penerbit_id
            $whereClause
        ", true);

        $response = [
            'label' => [
                'Ditinjau',
                'Diterima',
                'Bermasalah',
                'Ditolak',
            ],
            'data' => [
                $data->TOTAL_1 ?? 0,
                $data->TOTAL_2 ?? 0,
                $data->TOTAL_3 ?? 0,
                $data->TOTAL_5 ?? 0,
            ]
        ];

        return response()->json($response);
    }

    public function dataProvince(Request $request)
    {
        $date = $request->date;
        $explodeDate = explode(' - ', $date);
        $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
        $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');
        $condition = [];

        if (!Main::isSuperAdmin() && !Main::isPerpusnas()) {
            $condition[] = "propinsi.id = " . session('province_id');
        }

        $whereClause = !empty($condition) ? "where " . implode(' and ', $condition) : '';

        $response = QueryAPI::get("
            select
                propinsi.namapropinsi,
                sum(case when worksheets.category = '$this->worksheetCategoryDigital' then 1 else 0 end) as total_digital,
                sum(case when worksheets.category = '$this->worksheetCategoryAnalog' then 1 else 0 end) as total_analog,
                sum(case when worksheets.category = '$this->worksheetCategoryPrinted' then 1 else 0 end) as total_printed
            from
                propinsi
            left join
                kabupaten on kabupaten.propinsiid = propinsi.id
            left join
                e_collections on e_collections.kabupaten_id = kabupaten.id
            left join
                catalogs on catalogs.edeposit_col_id = e_collections.id and
                (catalogs.createdate >= to_date('$startDate', 'YYYY-MM-DD') and catalogs.createdate < to_date('$endDate', 'YYYY-MM-DD') + 1)
            left join
                worksheets on worksheets.id = catalogs.worksheet_id
            $whereClause
            group by
                propinsi.namapropinsi
            order by
                propinsi.namapropinsi
        ");

        return response()->json($response);
    }

    public function dataActivity()
    {
        $condition = ['rownum <= 10'];

        if (!Main::isSuperAdmin() && !Main::isPerpusnas()) {
            $usernameSession = session('username');
            $condition[] = "actionby = '$usernameSession'";
        }

        $whereClause = !empty($condition) ? "where " . implode(' and ', $condition) : '';

        $response = QueryAPI::get("
            select
                *
            from
                historydata
            $whereClause
            order by
                actiondate desc
        ");

        return response()->json($response);
    }

    public function dataDigitalWork(Request $request)
    {
        $date = $request->date;
        $explodeDate = explode(' - ', $date);
        $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
        $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

        $response = [];
        $condition = [];

        if (!Main::isSuperAdmin() && !Main::isPerpusnas()) {
            $condition[] = "branchs.province_id = " . session('province_id');
        } else {
            $condition[] = "branchs.province_id is not null";
        }

        $whereClause = !empty($condition) ? implode(' and ', $condition) : '';

        $data = QueryAPI::get("
            select
                worksheets.name,
                count(catalogs.id) as total
            from
                worksheets
            left join
                catalogs on catalogs.worksheet_id = worksheets.id and
                (catalogs.createdate >= to_date('$startDate', 'YYYY-MM-DD') and catalogs.createdate < to_date('$endDate', 'YYYY-MM-DD') + 1)
            left join
                branchs on branchs.id = catalogs.id
            left join
                propinsi on propinsi.id = branchs.province_id and
                $whereClause
            where
                worksheets.category = '$this->worksheetCategoryDigital'
            group by
                worksheets.name
            order by
                worksheets.name
        ");

        foreach (($data ?? []) as $d) {
            $response[] = [
                'name' => $d->NAME,
                'value' => $d->TOTAL
            ];
        }

        return response()->json($response);
    }

    public function dataAnalogWork(Request $request)
    {
        $date = $request->date;
        $explodeDate = explode(' - ', $date);
        $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
        $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

        $response = [];
        $condition = [];

        if (!Main::isSuperAdmin() && !Main::isPerpusnas()) {
            $condition[] = "branchs.province_id = " . session('province_id');
        } else {
            $condition[] = "branchs.province_id is not null";
        }

        $whereClause = !empty($condition) ? implode(' and ', $condition) : '';

        $data = QueryAPI::get("
            select
                worksheets.name,
                count(catalogs.id) as total
            from
                worksheets
            left join
                catalogs on catalogs.worksheet_id = worksheets.id and
                (catalogs.createdate >= to_date('$startDate', 'YYYY-MM-DD') and catalogs.createdate < to_date('$endDate', 'YYYY-MM-DD') + 1)
            left join
                branchs on branchs.id = catalogs.id
            left join
                propinsi on propinsi.id = branchs.province_id and
                $whereClause
            where
                worksheets.category = '$this->worksheetCategoryAnalog'
            group by
                worksheets.name
            order by
                worksheets.name
        ");

        foreach (($data ?? []) as $d) {
            $response[] = [
                'name' => $d->NAME,
                'value' => $d->TOTAL
            ];
        }

        return response()->json($response);
    }

    public function dataPrintedWork(Request $request)
    {
        $date = $request->date;
        $explodeDate = explode(' - ', $date);
        $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
        $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

        $response = [];
        $condition = [];

        if (!Main::isSuperAdmin() && !Main::isPerpusnas()) {
            $condition[] = "branchs.province_id = " . session('province_id');
        } else {
            $condition[] = "branchs.province_id is not null";
        }

        $whereClause = !empty($condition) ? implode(' and ', $condition) : '';

        $data = QueryAPI::get("
            select
                worksheets.name,
                count(catalogs.id) as total
            from
                worksheets
            left join
                catalogs on catalogs.worksheet_id = worksheets.id and
                (catalogs.createdate >= to_date('$startDate', 'YYYY-MM-DD') and catalogs.createdate < to_date('$endDate', 'YYYY-MM-DD') + 1)
            left join
                branchs on branchs.id = catalogs.id
            left join
                propinsi on propinsi.id = branchs.province_id and
                $whereClause
            where
                worksheets.category = '$this->worksheetCategoryPrinted'
            group by
                worksheets.name
            order by
                worksheets.name
        ");

        foreach (($data ?? []) as $d) {
            $response[] = [
                'name' => $d->NAME,
                'value' => $d->TOTAL
            ];
        }

        return response()->json($response);
    }

    public function dataCollection(Request $request)
    {
        $date = $request->date;
        $explodeDate = explode(' - ', $date);
        $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
        $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

        $response = [];
        $condition = ["(catalogs.createdate >= to_date('$startDate', 'YYYY-MM-DD') and catalogs.createdate < to_date('$endDate', 'YYYY-MM-DD') + 1)"];

        if (!Main::isSuperAdmin() && !Main::isPerpusnas()) {
            $condition[] = "branchs.province_id = " . session('province_id');
        }

        $whereClause = !empty($condition) ? "where " . implode(' and ', $condition) : '';

        $data = QueryAPI::get("
            select
                nvl(sum(case when worksheets.category = '$this->worksheetCategoryDigital' then 1 else 0 end), 0) as total_digital,
                nvl(sum(case when worksheets.category = '$this->worksheetCategoryAnalog' then 1 else 0 end), 0) as total_analog,
                nvl(sum(case when worksheets.category = '$this->worksheetCategoryPrinted' then 1 else 0 end), 0) as total_printed
            from
                catalogs
            left join
                worksheets on worksheets.id = catalogs.id
            left join
                branchs on branchs.id = catalogs.id
            left join
                propinsi on propinsi.id = branchs.province_id
            $whereClause
        ", true);

        $response = [
            'label' => [
                'Karya Digital',
                'Karya Analog',
                'Karya Cetak'
            ],
            'data' => [
                $data->TOTAL_DIGITAL ?? 0,
                $data->TOTAL_ANALOG ?? 0,
                $data->TOTAL_PRINTED ?? 0,
            ]
        ];

        return response()->json($response);
    }

    public function dataType(Request $request)
    {
        $date = $request->date;
        $explodeDate = explode(' - ', $date);
        $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
        $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

        $response = [];
        $condition = [];

        if (!Main::isSuperAdmin() && !Main::isPerpusnas()) {
            $condition[] = "branchs.province_id = " . session('province_id');
        } else {
            $condition[] = "branchs.province_id is not null";
        }

        $whereClause = !empty($condition) ? implode(' and ', $condition) : '';

        $data = QueryAPI::get("
            select
                worksheets.name,
                count(catalogs.id) as total
            from
                worksheets
            left join
                catalogs on catalogs.worksheet_id = worksheets.id and
                (catalogs.createdate >= to_date('$startDate', 'YYYY-MM-DD') and catalogs.createdate < to_date('$endDate', 'YYYY-MM-DD') + 1)
            left join
                branchs on branchs.id = catalogs.id
            left join
                propinsi on propinsi.id = branchs.province_id and
                $whereClause
            where
                worksheets.category is not null
            group by
                worksheets.name
            order by
                worksheets.name
        ");

        foreach (($data ?? []) as $d) {
            $response[] = [
                'name' => $d->NAME,
                'value' => $d->TOTAL
            ];
        }

        return response()->json($response);
    }
}
