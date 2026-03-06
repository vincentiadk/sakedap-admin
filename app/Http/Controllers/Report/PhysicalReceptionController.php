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
                'createBy' => QueryAPI::get("select distinct(create_by) from letter where create_by is not null") ?? [],
                'receivedBy' => QueryAPI::get("select distinct(received_by) from letter_detail where received_by is not null") ?? [],
                'verifiedBy' => QueryAPI::get("select distinct(verified_by) from letter_detail where verified_by is not null") ?? [],
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
            'letter.accept_date',
            'letter_detail.create_date',
            'letter_detail.title',
            'penerbit.name',
            'branchs.name',
            'jasa_pengiriman.name',
            'letter.receipt_no',
            'letter_detail.copy',
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
            'letter.create_by',
            'letter_detail.received_by',
            'letter_detail.verified_by',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 10);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "letter.status != 'DRAFT'";

        if (!Main::isSuperAdmin() && !Main::isPerpusnas()) {
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

        if ($request->create_by) {
            $whereCondition[] = "letter.create_by = '$request->create_by'";
        }

        if ($request->received_by) {
            $whereCondition[] = "letter_detail.received_by = '$request->received_by'";
        }

        if ($request->verified_by) {
            $whereCondition[] = "letter_detail.verified_by = '$request->verified_by'";
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
                                letter.accept_date as accept_date_letter,
                                letter.create_date as create_date_letter,
                                letter.penerbit_id as penerbit_id_letter,
                                letter.create_by as create_by_letter
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
                $createDateHTML = '';
                $acceptDateHTML = '';

                if ($val->ACCEPT_DATE_LETTER ?: null) {
                    $acceptDateHTML = '
                        <div>' . Carbon::parse($val->ACCEPT_DATE_LETTER)->isoFormat('D MMM Y') . '</div>
                        <small class="text-muted">Jam : ' . Carbon::parse($val->ACCEPT_DATE_LETTER)->format('H:i') . ' WIB</small>
                    ';
                }

                if ($val->CREATE_DATE_LETTER ?: null) {
                    $createDateHTML = '
                        <div>' . Carbon::parse($val->CREATE_DATE_LETTER)->isoFormat('D MMM Y') . '</div>
                        <small class="text-muted">Jam : ' . Carbon::parse($val->CREATE_DATE_LETTER)->format('H:i') . ' WIB</small>
                    ';
                }

                $data[] = [
                    $start + 1,
                    $val->TITLE,
                    $acceptDateHTML,
                    $createDateHTML,
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
                    $val->CREATE_BY_LETTER,
                    $val->RECEIVED_BY,
                    $val->VERIFIED_BY,
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
