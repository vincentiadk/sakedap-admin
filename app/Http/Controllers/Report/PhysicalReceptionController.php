<?php

namespace App\Http\Controllers\Report;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PhysicalReceptionController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'deliveryService' => QueryAPI::get("select * from jasa_pengiriman") ?? [],
                'content' => 'report.physical-reception',
                'plugins' => [
                    'datatable',
                    'select2',
                    'daterangepicker',
                ]
            ]
        ]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'letter_detail.letter_detail_id',
            'letter_detail.title',
            'penerbit.name',
            'branchs.name',
            'jasa_pengiriman.name',
            'letter.receipt_no',
            'letter_detail.copyt',
            'letter_detail.qty_accept',
            'letter_detail.qty_reject',
            'letter_detail.qty_hibah',
            'letter_detail.qty_retur',
            'letter_detail.qty_verif',
            'letter_detail.jenis_media',
            'letter.status',
            'letter_detail.price',
            'letter_detail.isbn',
            'letter_detail.publish_year',
            'letter_detail.isbn_status',
            'letter_detail.edisi_serial',
            'letter_detail.ttes_awal',
            'letter_detail.ttes_akhir',
            'letter_detail.kala_terbit',
            'letter_detail.catalog_id',
            'letter_detail.nomorpanggiljilid',
            'letter_detail.qrcbn',
            'letter_detail.isbd',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "letter.status != 'DRAFT'";

        if (!Main::isSuperAdmin()) {
            $whereCondition[] = 'branchs.province_id = ' . session('province_id');
        }

        if ($request->delivery_service_id) {
            $whereCondition[] = "letter.jasa_pengiriman_id = $request->delivery_service_id";
        }

        if ($request->status) {
            $whereCondition[] = "letter.status = '$request->status'";
        }

        if ($request->executor_id) {
            $whereCondition[] = "letter.penerbit_id = $request->executor_id";
        }

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $whereCondition[] = "(letter.$request->date_type >= to_date('$startDate', 'YYYY-MM-DD') and letter.$request->date_type < to_date('$endDate', 'YYYY-MM-DD') + 1)";
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
                letter_detail
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                letter_detail
            left join
                letter on letter.letter_id = letter_detail.letter_id
            left join
                penerbit on penerbit.id = letter.penerbit_id
            left join
                jasa_pengiriman on jasa_pengiriman.id = letter.jasa_pengiriman_id
            left join
                branchs on branchs.id = letter.branch_id
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
                                letter_detail.*,
                                jasa_pengiriman.name as name_jasa_pengiriman,
                                penerbit.name as name_penerbit,
                                branchs.name as name_branch,
                                letter.receipt_no as receipt_no_letter,
                                letter.status as status_letter,
                                letter.penerbit_id as penerbit_id_letter
                            from
                                letter_detail
                            left join
                                letter on letter.letter_id = letter_detail.letter_id
                            left join
                                penerbit on penerbit.id = letter.penerbit_id
                            left join
                                jasa_pengiriman on jasa_pengiriman.id = letter.jasa_pengiriman_id
                            left join
                                branchs on branchs.id = letter.branch_id
                            $whereClause
                            $orderBy
                        ) data
                    where
                        rownum <= $length
                )
            where
                rnum > $start
        ");

        if ($queryData) {
            foreach ($queryData as $val) {
                $data[] = [
                    $start + 1,
                    $val->TITLE,
                    $val->PENERBIT_ID_LETTER . ' | ' . $val->NAME_PENERBIT,
                    $val->NAME_BRANCH,
                    $val->NAME_JASA_PENGIRIMAN,
                    $val->RECEIPT_NO_LETTER,
                    $val->COPY,
                    $val->QTY_ACCEPT,
                    $val->QTY_REJECT,
                    $val->QTY_HIBAH,
                    $val->QTY_RETUR,
                    $val->QTY_VERIF,
                    $val->JENIS_MEDIA,
                    $val->STATUS_LETTER,
                    'Rp ' . number_format($val->PRICE ?: 0),
                    $val->ISBN,
                    $val->PUBLISH_YEAR,
                    $val->ISBN_STATUS,
                    $val->EDISI_SERIAL,
                    $val->TTES_AWAL,
                    $val->TTES_AKHIR,
                    $val->KALA_TERBIT,
                    $val->CATALOG_ID,
                    $val->NOMORPANGGILJILID,
                    $val->QRCBN,
                    $val->ISBD,
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
