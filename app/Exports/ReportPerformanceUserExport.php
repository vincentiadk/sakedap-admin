<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Helpers\QueryAPI;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportPerformanceUserExport implements FromView, ShouldAutoSize
{
    use Exportable;

    /**
     * request
     *
     * @var mixed
     */
    protected $request;

    /**
     * tableName
     *
     * @var mixed
     */
    private $tableName;

    /**
     * __construct
     *
     * @param  mixed $request
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
        $this->tableName = "
            'catalogs',
            'collections',
            'e_collections'
        ";

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
        $whereCondition[] = "lower(historydata.tablename) in ($this->tableName)";

        if ($request->action) {
            $whereCondition[] = "lower(historydata.action) = lower('$request->action')";
        }

        if ($request->action_by) {
            $whereCondition[] = "historydata.actionby = '$request->action_by'";
        }

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $whereCondition[] = "(historydata.actiondate >= to_date('$startDate', 'YYYY-MM-DD') and historydata.actiondate < to_date('$endDate', 'YYYY-MM-DD') + 1)";
        }

        if ($whereCondition) {
            $whereClause = "where " . implode(' and ', $whereCondition);
        }

        $result = QueryAPI::get("
            select
                historydata.*,
                coalesce(
                    cast(catalogs.title as varchar2(4000)),
                    cast(collections.title as varchar2(4000)),
                    cast(e_collections.title as varchar2(4000))
                ) as title
            from
                historydata
            left join
                catalogs on historydata.idref = catalogs.id and lower(historydata.tablename) = 'catalogs'
            left join
                collections on historydata.idref = collections.id and lower(historydata.tablename) = 'collections'
            left join
                e_collections on historydata.idref = e_collections.id and lower(historydata.tablename) = 'e_collections'
            $whereClause
        ");

        return view('export.report-performance-user', [
            'request' => $request,
            'data' => $result ?? []
        ]);
    }
}
