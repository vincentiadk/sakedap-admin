<?php

namespace App\Http\Controllers\Report;

use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Jobs\ExcelDownloadBackgroundJob;

class ManagerController extends Controller
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

    public function index(Request $request)
    {
        if ($request->exported) {
            $jobID = (string) Str::uuid();
            $userId = session('id');
            $userKey = "user:$userId:download";

            $payload = [
                'is_not_center_branch' => Main::isNotCenterBranch(),
                'type_id' => $request->type_id,
                'category_id' => $request->category_id,
                'province_id' => $request->province_id
            ];

            Redis::lpush($userKey, $jobID);
            ExcelDownloadBackgroundJob::dispatch($jobID, 'report-manager', $payload)
                ->onQueue('report');

            return redirect('report/manager')->with(['success' => 'Data laporan sedang diproses']);
        }

        $data = [
            'category' => QueryAPI::get("select * from penerbit_kategori"),
            'type' => QueryAPI::get("select * from penerbit_jenis"),
            'content' => 'report.manager'
        ];

        return view('layouts.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'penerbit.id',
            'penerbit.name',
            'penerbit_jenis.name',
            'penerbit_kategori.name',
            'propinsi.namapropinsi',
            null,
            null,
            null,
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = $request->search['value'];

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "penerbit.status = '3'";

        if ($search) {
            $terms = [];

            foreach ($column as $c) {
                if ($c) {
                    $terms[] = "$c like '%$search%'";
                }
            }

            $whereCondition[] = '(' . implode(' or ', $terms) . ')';
        }

        if ($request->type_id) {
            $whereCondition[] = "penerbit.jenis_id = $request->type_id";
        }

        if ($request->category_id) {
            $whereCondition[] = "penerbit.kategori_id = $request->category_id";
        }

        if ($request->province_id) {
            $whereCondition[] = "penerbit.province_id = $request->province_id";
        }

        if ($whereCondition) {
            $whereClause = "where " . implode(' and ', $whereCondition);
        }

        if ($order) {
            $orderColumnIndex = $order[0]['column'];
            $orderDir = $order[0]['dir'];
            $orderBy = "order by " . $column[$orderColumnIndex] . " $orderDir";
        }

        $totalData = QueryAPI::get("
            select
                count(*) as total
            from
                penerbit
            where
                penerbit.status = '3'
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                penerbit
            left join
                penerbit_jenis on penerbit_jenis.id = penerbit.jenis_id
            left join
                propinsi on propinsi.id = penerbit.province_id
            left join
                penerbit_kategori on penerbit_kategori.id = penerbit.kategori_id
            $whereClause
        ", true)->TOTAL ?? 0;

        $queryData = QueryAPI::get("
            select
                *
            from (
                    select
                        rownum as rnum,
                        data.*
                    from
                        (
                            select
                                penerbit.id,
                                penerbit.name,
                                penerbit_jenis.name AS name_penerbit_jenis,
                                penerbit_kategori.name AS name_penerbit_kategori,
                                propinsi.namapropinsi AS namapropinsi,
                                count(
                                    case
                                        when worksheets.category = '$this->worksheetCategoryAnalog'
                                        then 1
                                    end
                                ) AS total_analog,
                                count(
                                    case
                                        when worksheets.category = '$this->worksheetCategoryPrinted'
                                        then 1
                                    end
                                ) AS total_printed,
                                count(
                                    case
                                        when worksheets.category = '$this->worksheetCategoryDigital'
                                        then 1
                                    end
                                ) AS total_digital
                            from
                                penerbit
                            left join
                                penerbit_jenis on penerbit_jenis.id = penerbit.jenis_id
                            left join
                                propinsi on propinsi.id = penerbit.province_id
                            left join
                                penerbit_kategori on penerbit_kategori.id = penerbit.kategori_id
                            left join
                                catalogs on catalogs.penerbit_id = penerbit.id
                            left join
                                worksheets on worksheets.id = catalogs.worksheet_id
                            $whereClause
                            group by
                                penerbit.id,
                                penerbit_jenis.name,
                                penerbit_kategori.name,
                                propinsi.namapropinsi,
                                penerbit.name
                            $orderBy
                        ) data
                )
            where
                rnum > $start and rnum <= $length
        ");

        if ($queryData) {
            foreach ($queryData as $val) {
                $data[] = [
                    $start + 1,
                    $val->NAME,
                    $val->NAME_PENERBIT_JENIS,
                    $val->NAME_PENERBIT_KATEGORI,
                    $val->NAMAPROPINSI,
                    number_format($val->TOTAL_DIGITAL),
                    number_format($val->TOTAL_ANALOG),
                    number_format($val->TOTAL_PRINTED),
                ];

                $start++;
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    }
}
