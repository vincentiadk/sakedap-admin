<?php

namespace App\Http\Controllers\Report;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

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
            'letter.accept_date',
            'letter.create_date',
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
            'collectionmedias.name',
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
        $orCondition[] = "";

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

        if ($request->name) {
            $name = strtoupper(trim($request->name));
            $orCondition[] = "(upper(u_create.fullname) like '%$name%' or upper(letter.create_by) like '%$name%')";
            $orCondition[] = "(upper(u_received.fullname) like '%$name%' or upper(letter_detail.received_by) like '%$name%')";
            $orCondition[] = "(upper(u_verified.fullname) like '%$name%' or upper(letter_detail.verified_by) like '%$name%')";
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
        if (count($whereCondition) > 0 || count($orCondition) > 0) {
            $whereClause = " where ";
        }
        if (count($whereCondition) > 0) {
            $whereClause .= implode(' and ', $whereCondition);
        }

        if (count($orCondition) > 1) {
            $conditions = array_slice($orCondition, 1);
            $whereClause .= " and (" . implode(' or ', $conditions) . ")";
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
            left join
                users u_create on letter.create_by = u_create.username
            left join
                users u_received on letter_detail.received_by = u_received.username
            left join
                users u_verified on letter_detail.verified_by = u_verified.username
            left join 
                collectionmedias on collectionmedias.id = letter_detail.collection_type_id
            $whereClause
        ", true)->TOTAL ?? 0;

        $sql = "
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
                                letter.create_by as create_by_letter,
                                u_create.fullname as fullname_create,
                                u_received.fullname as fullname_received,
                                u_verified.fullname as fullname_verified,
                                collectionmedias.name as media_name
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
                            left join
                                users u_create on letter.create_by = u_create.username
                            left join
                                users u_received on letter_detail.received_by = u_received.username
                            left join
                                users u_verified on letter_detail.verified_by = u_verified.username
                            left join 
                                collectionmedias on collectionmedias.id = letter_detail.collection_type_id
                            $whereClause
                            $orderBy
                        ) data
                    where
                        rownum <= $length
                )
            where
                rnum > $start
        ";

        $queryData = QueryAPI::get($sql);
        
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

                $createBy = '
                    <div><small>Username: ' . $val->CREATE_BY_LETTER . '</small></div>
                    <div><small>Nama: ' . $val->FULLNAME_CREATE . '</small></div>
                ';

                $receivedBy = '
                    <div><small>Username: ' . $val->RECEIVED_BY . '</small></div>
                    <div><small>Nama: ' . $val->FULLNAME_RECEIVED . '</small></div>
                ';

                $verifiedBy = '
                    <div><small>Username: ' . $val->VERIFIED_BY . '</small></div>
                    <div><small>Nama: ' . $val->FULLNAME_VERIFIED . '</small></div>
                ';

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
                    $val->MEDIA_NAME, //$val->JENIS_MEDIA,
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
                    $createBy,
                    $receivedBy,
                    $verifiedBy,
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

    public function datatableSummary(Request $request)
    {
        $column = [
            'nama_orang',
            'periode',
            'total_judul',
            'total_eks',
        ];

        $draw   = intval($request->draw ?? 0);
        $start  = intval($request->start ?? 0);
        $length = intval($request->length ?? 10);

        $data = [];
        $orderBy = '';
        $order = $request->order;
        /*
        |--------------------------------------------------------------------------
        | Periode grouping
        |--------------------------------------------------------------------------
        */
        $periodGroup = "TRUNC(l.accept_date)";
        $periodLabel = "TO_CHAR(TRUNC(l.accept_date),'YYYY-MM-DD')";

        if ($request->period == 'monthly') {
            $periodGroup = "TRUNC(l.accept_date,'MM')";
            $periodLabel = "TO_CHAR(TRUNC(l.accept_date,'MM'),'YYYY-MM')";
        }

        if ($request->period == 'yearly') {
            $periodGroup = "TRUNC(l.accept_date,'YYYY')";
            $periodLabel = "TO_CHAR(TRUNC(l.accept_date,'YYYY'),'YYYY')";
        }

        /*
        |--------------------------------------------------------------------------
        | WHERE CONDITION
        |--------------------------------------------------------------------------
        */
        $whereCondition = [];
        $whereCondition[] = "l.status != 'DRAFT'";
        $whereCondition[] = "l.accept_date IS NOT NULL";

        if (!Main::isSuperAdmin() && !Main::isPerpusnas()) {
            $whereCondition[] = "branchs.province_id = " . session('province_id');
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

        if ($request->date) {

            $explodeDate = explode(' - ', $request->date);

            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate   = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $whereCondition[] = "
                (
                    l.$request->date_type >= TO_DATE('$startDate','YYYY-MM-DD')
                    AND l.$request->date_type < TO_DATE('$endDate','YYYY-MM-DD') + 1
                )
            ";
        }

        $whereClause = implode(' AND ', $whereCondition);

        /*
        |--------------------------------------------------------------------------
        | Sub Query (semua role)
        |--------------------------------------------------------------------------
        */
       $baseQuery = "
        SELECT 
            nama_orang,
            periode,
            COUNT(letter_detail_id) AS total_judul,
            SUM(qty_accept) AS total_eks
        FROM (
            SELECT DISTINCT
                nama_orang,
                periode,
                letter_detail_id,
                qty_accept
            FROM (

                -- pembuat
                SELECT
                    NVL(u_create.fullname,l.create_by) nama_orang,
                    $periodLabel periode,
                    ld.qty_accept,
                    ld.letter_detail_id
                FROM letter_detail ld
                LEFT JOIN letter l
                    ON l.letter_id = ld.letter_id
                LEFT JOIN users u_create
                    ON u_create.username = l.create_by
                WHERE $whereClause
                AND NVL(l.create_by,'x') IS NOT NULL

                UNION ALL

                -- penerima
                SELECT
                    NVL(u_received.fullname,ld.received_by) nama_orang,
                    $periodLabel periode,
                    ld.qty_accept,
                    ld.letter_detail_id
                FROM letter_detail ld
                LEFT JOIN letter l
                    ON l.letter_id = ld.letter_id
                LEFT JOIN users u_received
                    ON u_received.username = ld.received_by
                WHERE $whereClause
                AND NVL(ld.received_by,'x') IS NOT NULL

                UNION ALL

                -- verifikator
                SELECT
                    NVL(u_verified.fullname,ld.verified_by) nama_orang,
                    $periodLabel periode,
                    ld.qty_accept,
                    ld.letter_detail_id
                FROM letter_detail ld
                LEFT JOIN letter l
                    ON l.letter_id = ld.letter_id
                LEFT JOIN users u_verified
                    ON u_verified.username = ld.verified_by
                WHERE $whereClause
                AND NVL(ld.verified_by,'x') IS NOT NULL

            )
            WHERE nama_orang IS NOT NULL
        ";
        /*
        |--------------------------------------------------------------------------
        | Filter nama
        |--------------------------------------------------------------------------
        */
        $nameFilter = '';
        if ($request->name) {
           $name = strtoupper(trim($request->name));
           $nameFilter .= " WHERE UPPER(nama_orang) LIKE '%$name%' ";
        }

        /*
        |--------------------------------------------------------------------------
        | Total Filtered
        |--------------------------------------------------------------------------
        */
        $totalFiltered = QueryAPI::get("
            SELECT COUNT(*) total
            FROM (
                $baseQuery
            )
            $nameFilter
            GROUP BY nama_orang,periode )
        ", true)->TOTAL ?? 0;

        /*
        |--------------------------------------------------------------------------
        | Pagination Oracle
        |--------------------------------------------------------------------------
        */
        $sql = "
            SELECT *
            FROM (
                SELECT
                    ROWNUM rnum,
                    data.*
                FROM (
                    $baseQuery
                    )
                    $nameFilter
                    GROUP BY nama_orang,periode
                    $orderBy
                ) data
                WHERE ROWNUM <= " . ($start + $length) . "
            )
            WHERE rnum > $start
        ";

        $queryData = QueryAPI::get($sql);
        
        /*
        |--------------------------------------------------------------------------
        | Result
        |--------------------------------------------------------------------------
        */
        if ($queryData) {

            foreach ($queryData as $val) {

                $data[] = [
                    $start + 1,
                    $val->NAMA_ORANG,
                    $val->PERIODE,
                    $val->TOTAL_JUDUL,
                    $val->TOTAL_EKS
                ];

                $start++;
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalFiltered,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    }
}
