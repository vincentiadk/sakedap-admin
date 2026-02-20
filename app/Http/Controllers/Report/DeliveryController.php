<?php

namespace App\Http\Controllers\Report;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeliveryController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'deliveryService' => QueryAPI::get("select * from jasa_pengiriman") ?? [],
                'content' => 'report.delivery',
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
            'l.letter_id',
            'l.letter_date',
            'l.letter_number',
            'l.accept_date',
            'l.create_date',
            'l.sender',
            'l.phone',
            'p.name',
            'l.letter_number_ut',
            'l.receipt_no',
            'jp.name',
            'b.name',
            null,
            null,
            null,
            null,
            null,
            null,
            'l.status',
            'l.kode_promo',
            'l.biaya_kirim',
            'l.berat',
            'l.jumlah_paket',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 10);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "l.status != 'DRAFT'";

        if (!Main::isSuperAdmin() && !Main::isPerpusnas()) {
            $whereCondition[] = 'b.province_id = ' . session('province_id');
        }

        if ($request->receipt_no) {
            $receiptNo = strtoupper($request->receipt_no);
            $whereCondition[] = "upper(l.receipt_no) like '%$receiptNo%'";
        }

        if ($request->delivery_service_id) {
            $whereCondition[] = "l.jasa_pengiriman_id = $request->delivery_service_id";
        }

        if ($request->status) {
            $whereCondition[] = "l.status = '$request->status'";
        }

        if ($request->executor_id) {
            $whereCondition[] = "l.penerbit_id = $request->executor_id";
        }

        if ($request->branch_id) {
            $whereCondition[] = "l.branch_id = $request->branch_id";
        }

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $whereCondition[] = "(l.$request->date_type >= to_date('$startDate', 'YYYY-MM-DD') and l.$request->date_type < to_date('$endDate', 'YYYY-MM-DD') + 1)";
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
                letter
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                letter l
            left join
                penerbit p on p.id = l.penerbit_id
            left join
                jasa_pengiriman jp on jp.id = l.jasa_pengiriman_id
            left join
                branchs b on b.id = l.branch_id
            $whereClause
        ", true)->TOTAL ?? 0;

        $queryData = QueryAPI::get("
            select
                *
            from
                (
                    select
                        rownum as rnum,
                        data.*
                    from
                        (
                            select
                                l.*,
                                b.name as name_branch,
                                jp.name as name_jasa_pengiriman,
                                p.name as name_penerbit,
                                nvl(td.total_eks_receipt, 0) as total_eks_receipt,
                                nvl(td.total_title_receipt, 0) as total_title_receipt,
                                case
                                    when l.status in ('TERKIRIM', 'CEK FISIK', 'DITERIMA PENUH', 'DITERIMA PARSIAL', 'RETUR')
                                    then nvl(td.total_eks_delivery, 0)
                                    else 0
                                end as total_eks_delivery,
                                case
                                    when l.status in ('TERKIRIM', 'CEK FISIK', 'DITERIMA PENUH', 'DITERIMA PARSIAL', 'RETUR')
                                    then nvl(td.total_title_delivery, 0)
                                    else 0
                                end as total_title_delivery,
                                case
                                    when l.status in ('TERKIRIM', 'CEK FISIK', 'DITERIMA PENUH', 'DITERIMA PARSIAL', 'RETUR')
                                    then nvl(td.total_eks_grant, 0)
                                    else 0
                                end as total_eks_grant,
                                case
                                    when l.status in ('TERKIRIM', 'CEK FISIK', 'DITERIMA PENUH', 'DITERIMA PARSIAL', 'RETUR')
                                    then nvl(td.total_title_grant, 0)
                                    else 0
                                end as total_title_grant
                            from
                                letter l
                            left join
                                penerbit p on p.id = l.penerbit_id
                            left join
                                jasa_pengiriman jp on jp.id = l.jasa_pengiriman_id
                            left join
                                branchs b on b.id = l.branch_id
                            left join
                                (
                                    select
                                        letter_id,
                                        sum(copy) as total_eks_delivery,
                                        sum(quantity) as total_title_delivery,
                                        sum(case when qty_accept > 0 then qty_accept else 0 end) as total_eks_receipt,
                                        sum(case when qty_accept > 0 then quantity else 0 end) as total_title_receipt,
                                        sum(case when qty_hibah > 0 then qty_hibah else 0 end) as total_eks_grant,
                                        sum(case when qty_hibah > 0 then quantity else 0 end) as total_title_grant
                                    from
                                        letter_detail
                                    group by
                                        letter_id
                                ) td on td.letter_id = l.letter_id
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
                $createDateHTML = '';
                $acceptDateHTML = '';

                if ($val->ACCEPT_DATE ?: null) {
                    $acceptDateHTML = '
                        <div>' . Carbon::parse($val->ACCEPT_DATE)->isoFormat('D MMM Y') . '</div>
                        <small class="text-muted">Jam : ' . Carbon::parse($val->ACCEPT_DATE)->format('H:i') . ' WIB</small>
                    ';
                }

                if ($val->CREATE_DATE ?: null) {
                    $createDateHTML = '
                        <div>' . Carbon::parse($val->CREATE_DATE)->isoFormat('D MMM Y') . '</div>
                        <small class="text-muted">Jam : ' . Carbon::parse($val->CREATE_DATE)->format('H:i') . ' WIB</small>
                    ';
                }

                $data[] = [
                    $start + 1,
                    $val->LETTER_DATE ? Carbon::parse($val->LETTER_DATE)->isoFormat('dddd, D MMMM Y') : '',
                    $val->LETTER_NUMBER,
                    $acceptDateHTML,
                    $createDateHTML,
                    $val->SENDER,
                    $val->PHONE,
                    $val->PENERBIT_ID . ' | ' . $val->NAME_PENERBIT,
                    $val->LETTER_NUMBER_UT,
                    $val->RECEIPT_NO,
                    $val->NAME_JASA_PENGIRIMAN,
                    $val->NAME_BRANCH,
                    $val->TOTAL_TITLE_DELIVERY,
                    $val->TOTAL_EKS_DELIVERY,
                    $val->TOTAL_TITLE_RECEIPT,
                    $val->TOTAL_EKS_RECEIPT,
                    $val->TOTAL_TITLE_GRANT,
                    $val->TOTAL_EKS_GRANT,
                    $val->STATUS,
                    $val->KODE_PROMO,
                    'Rp ' . number_format(($val->BIAYA_KIRIM ?: 0)),
                    (($val->BERAT ?: 0) > 0 ? number_format($val->BERAT / 1000, '2', ',', '.') : 0) . ' Kg',
                    $val->JUMLAH_PAKET,
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
