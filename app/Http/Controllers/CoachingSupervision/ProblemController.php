<?php

namespace App\Http\Controllers\CoachingSupervision;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProblemController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'content' => 'coaching-supervision.problem',
                'plugins' => [
                    'datatable',
                ]
            ]
        ]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'penerbit.id',
            'penerbit.id',
            'penerbit.keterangan',
            'penerbit.name',
            'penerbit.email1',
            'penerbit_kategori.name',
            'penerbit_jenis.name',
            'penerbit.telp1',
            'penerbit.createdate',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "penerbit.status = '2'";

        if (Main::isNotSuperAdmin()) {
            $whereCondition[] = 'penerbit.province_id = ' . session('province_id');
        }

        if ($search) {
            $terms = [];

            foreach ($column as $c) {
                if ($c) {
                    $terms[] = "upper($c) like '%$search%'";
                }
            }

            $whereCondition[] = '(' . implode(' or ', $terms) . ')';
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
                status = '2'
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                penerbit
            left join
                penerbit_kategori on penerbit_kategori.id = penerbit.kategori_id
            left join
                penerbit_jenis on penerbit_jenis.id = penerbit.jenis_id
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
                                penerbit.*,
                                penerbit_kategori.name as name_penerbit_kategori,
                                penerbit_jenis.name as name_penerbit_jenis
                            from
                                penerbit
                            left join
                                penerbit_kategori on penerbit_kategori.id = penerbit.kategori_id
                            left join
                                penerbit_jenis on penerbit_jenis.id = penerbit.jenis_id
                            $whereClause
                            $orderBy
                        ) data
                )
            where
                rnum > $start and rnum <= $length
        ");

        if ($queryData) {
            foreach ($queryData as $val) {
                $email = '
                    <div>Utama : ' . $val->EMAIL1 . '</div>
                    <div>Alternatif : ' . $val->EMAIL2 . '</div>
                ';

                $phone = '
                    <div>Utama : ' . $val->TELP1 . '</div>
                    <div>Alternatif : ' . $val->TELP2 . '</div>
                ';

                $data[] = [
                    $start + 1,
                    $val->KETERANGAN,
                    $val->ID,
                    $val->NAME,
                    $email,
                    $val->NAME_PENERBIT_KATEGORI,
                    $val->NAME_PENERBIT_JENIS,
                    $phone,
                    Carbon::parse($val->CREATEDATE)->isoFormat('dddd, D MMMM Y'),
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
