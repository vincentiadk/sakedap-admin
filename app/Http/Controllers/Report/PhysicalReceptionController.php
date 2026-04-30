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
            /*$orCondition[] = "(upper(u_verified.fullname) like '%$name%' or upper(letter_detail.verified_by) like '%$name%')";*/
        }

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $whereCondition[] = "(letter.$request->date_type >= to_date('$startDate', 'YYYY-MM-DD') and letter.$request->date_type < to_date('$endDate', 'YYYY-MM-DD') + 1)";
        }

        $dateType = in_array($request->date_type, ['accept_date', 'letter_date', 'createdate']) 
            ? $request->date_type 
            : 'accept_date';
        if ($dateType === 'accept_date') {
                $dateField = "NVL(letter_detail.received_date, letter.accept_date)";
        } else {
                $dateField = "letter.$dateType";
        }
        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');
            
            $whereCondition[] = "(
                $dateField >= TO_DATE('$startDate', 'YYYY-MM-DD')
                AND $dateField < TO_DATE('$endDate', 'YYYY-MM-DD') + 1
            )";
        } else {
            $whereCondition[] = "(
                $dateField >= TRUNC(SYSDATE)
                AND $dateField < TRUNC(SYSDATE) + 1
            )";
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
        $columns = [
            1 => 'nama_orang',
            2 => 'periode',
            3 => 'total_judul',
            4 => 'total_eks',
            5 => 'total_eks_ditolak'
        ];

        $draw   = intval($request->draw ?? 0);
        $start  = intval($request->start ?? 0);
        $length = intval($request->length ?? 10);

        $data = [];

        /*
        |--------------------------------------------------------------------------
        | Periode
        |--------------------------------------------------------------------------
        */
        $periodLabel = "TO_CHAR(TRUNC({$dateField}), 'YYYY-MM-DD')";

        if ($request->period === 'monthly') {
            $periodLabel = "TO_CHAR(TRUNC({$dateField}, 'MM'), 'YYYY-MM')";
        } elseif ($request->period === 'yearly') {
            $periodLabel = "TO_CHAR(TRUNC({$dateField}, 'YYYY'), 'YYYY')";
        }

        /*
        |--------------------------------------------------------------------------
        | Date field whitelist
        |--------------------------------------------------------------------------
        */
        $allowedDateFields = [
            'letter.accept_date' => "NVL(ld.received_date, l.accept_date)",
            'accept_date'        => "NVL(ld.received_date, l.accept_date)",

            'letter.letter_date' => 'l.letter_date',
            'letter_date'        => 'l.letter_date',

            'letter.createdate'  => 'l.createdate',
            'createdate'         => 'l.createdate',

            'letter.create_date' => 'l.create_date',
            'create_date'        => 'l.create_date',
        ];

        $dateField = "NVL(ld.received_date, l.accept_date)";
        if ($request->date_type && isset($allowedDateFields[$request->date_type])) {
            $dateField = $allowedDateFields[$request->date_type];
        }

        /*
        |--------------------------------------------------------------------------
        | Base where
        |--------------------------------------------------------------------------
        */
        $whereConditions = [];
        $whereConditions[] = "l.status != 'DRAFT'";
        $whereConditions[] = "{$dateField} IS NOT NULL";

        if (!Main::isSuperAdmin() && !Main::isPerpusnas()) {
            $provinceId = (int) session('province_id');
            $whereConditions[] = "b.province_id = {$provinceId}";
        }

        if ($request->delivery_service_id !== null && $request->delivery_service_id !== '') {
            $deliveryServiceId = (int) $request->delivery_service_id;
            $whereConditions[] = "l.jasa_pengiriman_id = {$deliveryServiceId}";
        }

        if ($request->status) {
            $status = str_replace("'", "''", trim($request->status));
            $whereConditions[] = "l.status = '{$status}'";
        }

        if ($request->executor_id !== null && $request->executor_id !== '') {
            $executorId = (int) $request->executor_id;
            $whereConditions[] = "l.penerbit_id = {$executorId}";
        }

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);

            if (count($explodeDate) === 2) {
                $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
                $endDate   = Carbon::parse($explodeDate[1])->format('Y-m-d');

                $whereConditions[] = "(
                    {$dateField} >= TO_DATE('{$startDate}', 'YYYY-MM-DD')
                    AND {$dateField} < TO_DATE('{$endDate}', 'YYYY-MM-DD') + 1
                )";
            } else {
                $whereConditions[] = "(
                    {$dateField} >= TRUNC(SYSDATE)
                    AND {$dateField} < TRUNC(SYSDATE) + 1
                )";
            }
        } else {
            $whereConditions[] = "(
                {$dateField} >= TRUNC(SYSDATE)
                AND {$dateField} < TRUNC(SYSDATE) + 1
            )";
        }

        $whereClause = implode(' AND ', $whereConditions);

        /*
        |--------------------------------------------------------------------------
        | Name filter per role
        |--------------------------------------------------------------------------
        */
        $nameCreateWhere   = '';
        $nameReceivedWhere = '';
        $nameVerifiedWhere = '';

        if ($request->name) {
            $name = strtoupper(trim($request->name));
            $name = str_replace("'", "''", $name);

            $nameCreateWhere = " AND (
                UPPER(NVL(u_create.fullname, '')) LIKE '%{$name}%'
                OR UPPER(NVL(l.create_by, '')) LIKE '%{$name}%'
            )";

            $nameReceivedWhere = " AND (
                UPPER(NVL(u_received.fullname, '')) LIKE '%{$name}%'
                OR UPPER(NVL(ld.received_by, '')) LIKE '%{$name}%'
            )";

            /*$nameVerifiedWhere = " AND (
                UPPER(NVL(u_verified.fullname, '')) LIKE '%{$name}%'
                OR UPPER(NVL(ld.verified_by, '')) LIKE '%{$name}%'
            )";*/
        }

        /*
        |--------------------------------------------------------------------------
        | Final summary query
        |--------------------------------------------------------------------------
        */
        $finalQuery = "SELECT
            nama_orang,
            periode,
            COUNT(*) AS total_judul,
            SUM(qty_accept) AS total_eks,
            SUM(qty_reject) AS total_eks_ditolak
        FROM (
            SELECT
                nama_orang,
                periode,
                letter_detail_id,
                MAX(qty_accept) AS qty_accept,
                MAX(qty_reject) AS qty_reject
            FROM (
                -- pembuat
                SELECT
                    u_create.fullname AS nama_orang,
                    {$periodLabel} AS periode,
                    ld.qty_accept,
                    ld.qty_reject,
                    ld.letter_detail_id
                FROM letter_detail ld
                LEFT JOIN letter l
                    ON l.letter_id = ld.letter_id
                LEFT JOIN branchs b
                    ON b.id = l.branch_id
                JOIN users u_create
                    ON u_create.username = l.create_by
                WHERE {$whereClause}
                {$nameCreateWhere}

                UNION ALL

                -- penerima
                SELECT
                    u_received.fullname AS nama_orang,
                    {$periodLabel} AS periode,
                    ld.qty_accept,
                    ld.qty_reject,
                    ld.letter_detail_id
                FROM letter_detail ld
                LEFT JOIN letter l
                    ON l.letter_id = ld.letter_id
                LEFT JOIN branchs b
                    ON b.id = l.branch_id
                JOIN users u_received
                    ON u_received.username = ld.received_by
                WHERE {$whereClause}
                {$nameReceivedWhere}

            )
            GROUP BY nama_orang, periode, letter_detail_id
        )
        GROUP BY nama_orang, periode";
        /*
        |--------------------------------------------------------------------------
        | Ordering
        |--------------------------------------------------------------------------
        */
        $orderBy = " ORDER BY periode DESC, nama_orang ASC ";
        if (!empty($request->order) && isset($request->order[0])) {
            $orderColumnIndex = (int) $request->order[0]['column'];
            $orderDir = strtolower($request->order[0]['dir'] ?? 'asc');
            $orderDir = $orderDir === 'desc' ? 'DESC' : 'ASC';

            if (isset($columns[$orderColumnIndex])) {
                $orderBy = " ORDER BY {$columns[$orderColumnIndex]} {$orderDir} ";
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Count filtered
        |--------------------------------------------------------------------------
        */
        $totalFiltered = QueryAPI::get("
            SELECT COUNT(*) AS total
            FROM (
                {$finalQuery}
            )
        ", true)->TOTAL ?? 0;

        /*
        |--------------------------------------------------------------------------
        | Paging
        |--------------------------------------------------------------------------
        */
       $endRow = $start + $length;

        $sql = "
            SELECT *
            FROM (
                SELECT
                    ROW_NUMBER() over(Order by periode desc, nama_orang asc) as rnum,
                    data.*
                FROM (
                    {$finalQuery}
                    {$orderBy}
                ) data 
            )
            WHERE rnum > {$start} and rnum <= {$endRow}
        ";
        
        $queryData = QueryAPI::get($sql);

        if ($queryData) {
            foreach ($queryData as $val) {
                $data[] = [
                    $start + 1,
                    $val->NAMA_ORANG,
                    $val->PERIODE,
                    $val->TOTAL_JUDUL,
                    $val->TOTAL_EKS,
                    $val->TOTAL_EKS_DITOLAK
                ];
                $start++;
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalFiltered,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }
}
