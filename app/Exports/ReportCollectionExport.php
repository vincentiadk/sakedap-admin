<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Helpers\QueryAPI;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportCollectionExport implements FromView, ShouldAutoSize
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
        $whereClause = '';
        $whereCondition[] = "(catalogs.isdelete = 0 or catalogs.isdelete is null)";
        $whereCondition[] = "catalogs.edeposit_col_id is not null";

        if ($request->title) {
            $whereCondition[] = "catalogs.title like '%$request->title%'";
        }

        if ($request->executor_id) {
            $whereCondition[] = "catalogs.penerbit_id = $request->executor_id";
        }

        if ($request->province_id) {
            $whereCondition[] = "kabupaten.propinsiid = $request->province_id";
        }

        if ($request->year) {
            $whereCondition[] = "catalogs.publishyear = $request->year";
        }

        if ($request->worksheet_id) {
            $whereCondition[] = "catalogs.worksheet_id = $request->worksheet_id";
        }

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $whereCondition[] = "(catalogs.validatedate >= to_date('$startDate', 'YYYY-MM-DD') and catalogs.validatedate < to_date('$endDate', 'YYYY-MM-DD') + 1)";
        }

        if ($whereCondition) {
            $whereClause = "where " . implode(' and ', $whereCondition);
        }

        $result = QueryAPI::get("
            select
                catalogs.title,
                catalogs.album,
                catalogs.series,
                catalogs.edition,
                catalogs.deweyno,
                catalogs.volume,
                catalogs.isbn,
                catalogs.publishyear,
                catalogs.preview,
                catalogs.validatedate,
                penerbit.name as name_penerbit,
                propinsi.namapropinsi as namapropinsi,
                kabupaten.namakab as namakab,
                worksheets.name as name_worksheet
            from
                catalogs
            left join
                penerbit on penerbit.id = catalogs.penerbit_id
            left join
                kabupaten on kabupaten.id = catalogs.city_id
            left join
                propinsi on propinsi.id = kabupaten.propinsiid
            left join
                worksheets on worksheets.id = catalogs.worksheet_id
            $whereClause
        ");

        return view('export.report-collection', [
            'request' => $request,
            'data' => $result ?? []
        ]);
    }
}
