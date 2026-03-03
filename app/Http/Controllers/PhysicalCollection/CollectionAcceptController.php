<?php

namespace App\Http\Controllers\PhysicalCollection;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use App\Helpers\RajaOngkir;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class CollectionAcceptController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'deliveryService' => QueryAPI::get("select * from jasa_pengiriman") ?? [],
                'content' => 'physical-collection.collection-accept',
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
            'ld.letter_detail_id',
            null,
            'ld.title',
            'p.name',
            'b.name',
            'jp.name',
            'l.receipt_no',
            'ld.qty_accept',
            'ld.jenis_media',
            'l.status',
        ];

        $draw   = intval($request->draw ?? 0);
        $start  = intval($request->start ?? 0);              // offset
        $limit  = intval($request->length ?? 10);            // page size
        $endRow = $start + $limit;                           // rn <= endRow

        $data = [];
        $search = trim($request->search['value'] ?? '');
        $search = strtoupper($search);

        // --- WHERE conditions (dipakai untuk totalFiltered dan queryData) ---
        $whereCondition = [];
        $whereCondition[] = "l.status IN ('DITERIMA PENUH','DITERIMA PARSIAL','DITERIMA')";

        if (!Main::isSuperAdmin() && !Main::isPerpusnas()) {
            $provinceId = intval(session('province_id'));
            $whereCondition[] = "b.province_id = $provinceId";
        }

        if ($request->delivery_service_id) {
            $deliveryServiceId = intval($request->delivery_service_id);
            $whereCondition[] = "l.jasa_pengiriman_id = $deliveryServiceId";
        }

        if ($request->status) {
            $status = str_replace("'", "''", $request->status);
            $whereCondition[] = "l.status = '$status'";
        }

        if ($request->title) {
            $title = strtoupper(str_replace("'", "''", $request->title));
            $whereCondition[] = "upper(ld.title) = '$title'";
        }
        if ($request->isbn) {
            $isbn = strtoupper(str_replace("'", "''", $request->isbn));
            $whereCondition[] = "replace(ld.ISBN,'-','') = '$isbn'";
        }

        if ($request->executor_id) {
            $executorId = intval($request->executor_id);
            $whereCondition[] = "l.penerbit_id = $executorId";
        }

        if ($request->date && $request->date_type) {
            $dateType = $request->date_type;

            // whitelist kolom tanggal biar gak bisa injeksi "letter.$dateType"
            $allowedDateType = ['accept_date', 'letter_date', 'createdate'];
            if (in_array($dateType, $allowedDateType, true)) {
                $explodeDate = explode(' - ', $request->date);
                $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
                $endDate   = Carbon::parse($explodeDate[1])->format('Y-m-d');

                $whereCondition[] =
                    "(l.$dateType >= to_date('$startDate','YYYY-MM-DD') AND l.$dateType < to_date('$endDate','YYYY-MM-DD') + 1)";
            }
        }

        if ($search !== '') {
            $safe = str_replace("'", "''", $search);

            $terms = [];
            // HATI-HATI: kolom numeric jangan di upper()
            $terms[] = "upper(ld.title) LIKE '%$safe%'";
            $terms[] = "upper(p.name) LIKE '%$safe%'";
            $terms[] = "upper(b.name) LIKE '%$safe%'";
            $terms[] = "upper(jp.name) LIKE '%$safe%'";
            $terms[] = "upper(l.receipt_no) LIKE '%$safe%'";
            $terms[] = "upper(l.status) LIKE '%$safe%'";
            $terms[] = "to_char(ld.letter_detail_id) LIKE '%$safe%'";
            $terms[] = "to_char(l.penerbit_id) LIKE '%$safe%'";

            $whereCondition[] = '(' . implode(' OR ', $terms) . ')';
        }

        $whereClause = 'WHERE ' . implode(' AND ', $whereCondition);

        // --- ORDER BY untuk paging ---
        $orderBy = "ld.letter_detail_id DESC"; // default

        if (!empty($request->order)) {
            $orderColumnIndex = intval($request->order[0]['column'] ?? 0);
            $orderDir = strtolower($request->order[0]['dir'] ?? 'desc') === 'asc' ? 'ASC' : 'DESC';

            $orderCol = $column[$orderColumnIndex] ?? 'ld.letter_detail_id';
            if ($orderCol) {
                $orderBy = "$orderCol $orderDir";
            }
        }

        // recordsTotal: total semua letter_detail (tanpa filter)
        $totalData = QueryAPI::get("
            SELECT COUNT(*) AS total
            FROM letter_detail
        ", true)->TOTAL ?? 0;

        // recordsFiltered: total setelah filter/search (HARUS sama whereClause yang dipakai paging)
        $totalFiltered = QueryAPI::get("
            SELECT COUNT(*) AS total
            FROM letter_detail ld
            JOIN letter l ON l.letter_id = ld.letter_id
            LEFT JOIN penerbit p ON p.id = l.penerbit_id
            LEFT JOIN jasa_pengiriman jp ON jp.id = l.jasa_pengiriman_id
            LEFT JOIN branchs b ON b.id = l.branch_id
            $whereClause
        ", true)->TOTAL ?? 0;

        // Data: 2-step (ambil ID dulu, baru join ambil detail)
        $queryData = QueryAPI::get("
            SELECT
                ld.*,
                jp.name  AS name_jasa_pengiriman,
                p.name   AS name_penerbit,
                b.name   AS name_branch,
                l.receipt_no  AS receipt_no_letter,
                l.status      AS status_letter,
                l.penerbit_id AS penerbit_id_letter
            FROM (
                SELECT letter_detail_id, letter_id
                FROM (
                    SELECT
                        ld.letter_detail_id,
                        ld.letter_id,
                        ROW_NUMBER() OVER (ORDER BY $orderBy) AS rn
                    FROM letter_detail ld
                    JOIN letter l ON l.letter_id = ld.letter_id
                    LEFT JOIN penerbit p ON p.id = l.penerbit_id
                    LEFT JOIN jasa_pengiriman jp ON jp.id = l.jasa_pengiriman_id
                    LEFT JOIN branchs b ON b.id = l.branch_id
                    $whereClause
                )
                WHERE rn > $start
                AND rn <= $endRow
            ) x
            JOIN letter_detail ld ON ld.letter_detail_id = x.letter_detail_id
            JOIN letter l         ON l.letter_id = x.letter_id
            LEFT JOIN penerbit p  ON p.id = l.penerbit_id
            LEFT JOIN jasa_pengiriman jp ON jp.id = l.jasa_pengiriman_id
            LEFT JOIN branchs b   ON b.id = l.branch_id
            ORDER BY $orderBy
        ");

        if ($queryData) {
            $no = $start + 1;
            foreach ($queryData as $val) {
                $action = '
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm" onclick="detail(' . $val->LETTER_DETAIL_ID . ')">
                        <i class="ph-info me-1"></i>
                        Detail
                    </a>
                ';

                $identifier = "";
                if (!empty($val->ISBN))  $identifier .= "<br/>ISBN : " . $val->ISBN;
                if (!empty($val->ISSN))  $identifier .= "<br/>ISSN : " . $val->ISSN;
                if (!empty($val->QRCBN)) $identifier .= "<br/>QRCBN : " . $val->QRCBN;
                if (!empty($val->ISRC))  $identifier .= "<br/>ISRC : " . $val->ISRC;

                $data[] = [
                    $no++,
                    $action,
                    $val->TITLE . $identifier,
                    $val->PENERBIT_ID_LETTER . ' | ' . $val->NAME_PENERBIT,
                    $val->NAME_BRANCH,
                    $val->NAME_JASA_PENGIRIMAN,
                    $val->RECEIPT_NO_LETTER,
                    $val->QTY_ACCEPT,
                    $val->JENIS_MEDIA,
                    $val->STATUS_LETTER,
                ];
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    }

    public function detail(Request $request)
    {
        $id = $request->id;
        $trackingDelivery = null;

        $history = QueryAPI::get("
            select
                *
            from
                historydata
            where
                lower(tablename) = 'letter_detail' and
                idref = '$id'
        ");

        $data = QueryAPI::get("
            select
                letter_detail.*,
                jasa_pengiriman.name as name_jasa_pengiriman,
                jasa_pengiriman.code as code_jasa_pengiriman,
                penerbit.id as id_penerbit,
                penerbit.name as name_penerbit,
                branchs.name as name_branch,
                letter.receipt_no as receipt_no_letter
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
            where
                letter_detail.letter_detail_id = $id
        ", true);

        $awbQueryParam = [
            'awb' => $data->RECEIPT_NO_LETTER ?? '',
            'courier' => $data->CODE_JASA_PENGIRIMAN ?? ''
        ];

        if ($awbQueryParam['awb']) {
            $awb = RajaOngkir::post('track/waybill?' . http_build_query($awbQueryParam));

            if ($awb) {
                $trackingDelivery = $awb;
            }
        }

        return response()->json([
            'history' => $history ?? [],
            'data' => $data,
            'awb' => $trackingDelivery
        ]);
    }
}
