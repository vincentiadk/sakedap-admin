<?php

namespace App\Http\Controllers\PhysicalDelivery;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class DeliveryVerificationController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'deliveryService' => QueryAPI::get("select * from jasa_pengiriman") ?? [],
                'receivedBy' => QueryAPI::get("select distinct(received_by) from letter where received_by is not null") ?? [],
                'content' => 'physical-delivery.delivery-verification',
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
            null,
            'p.name',
            'l.status',
            'l.sent_date',
            'l.check_date',
            'l.receipt_no',
            'jp.name',
            'b.name',
            null,
            null,
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "l.status in ('TERKIRIM', 'CEK FISIK')";

        if (Main::isNotSuperAdmin()) {
            $whereCondition[] = 'b.province_id = ' . session('province_id');
        }

        if ($request->received_by) {
            $whereCondition[] = "ld.received_by = '$request->received_by'";
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

        if ($request->title) {
            $filterText = strtoupper($request->title);
            $whereCondition[] = "upper(ld.title) like '%$filterText%'";
        }

        if ($request->author) {
            $filterText = strtoupper($request->author);
            $whereCondition[] = "upper(ld.author) like '%$filterText%'";
        }

        if ($request->isbn) {
            $filterText = str_replace('-', '', $request->isbn);
            $whereCondition[] = "replace(ld.isbn, '-', '') = '$filterText'";
        }

        if ($request->publish_year) {
            $whereCondition[] = "ld.publish_year = '$request->publish_year'";
        }

        if ($request->edition_serial) {
            $filterText = strtoupper($request->edition_serial);
            $whereCondition[] = "upper(ld.edisi_serial) like '%$filterText%'";
        }

        if ($request->periodicals) {
            $filterText = strtoupper($request->periodicals);
            $whereCondition[] = "upper(ld.kala_terbit) like '%$filterText%'";
        }

        if ($request->physical_description) {
            $filterText = strtoupper($request->physical_description);
            $whereCondition[] = "upper(ld.deskripsifisik) like '%$filterText%'";
        }

        if ($request->sinopsis) {
            $filterText = strtoupper($request->sinopsis);
            $whereCondition[] = "upper(ld.sinopsis) like '%$filterText%'";
        }

        if ($request->media_type) {
            $filterText = strtoupper($request->media_type);
            $whereCondition[] = "upper(ld.jenis_media) like '%$filterText%'";
        }

        if ($request->binding) {
            $filterText = strtoupper($request->binding);
            $whereCondition[] = "upper(ld.nomorpanggiljilid) like '%$filterText%'";
        }

        if ($request->qrcbn) {
            $filterText = strtoupper($request->qrcbn);
            $whereCondition[] = "ld.qrcbn = '$filterText'";
        }

        if ($request->isbd) {
            $filterText = strtoupper($request->isbd);
            $whereCondition[] = "ld.isbd = '$filterText'";
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
                count(distinct l.letter_id) as total
            from
                letter l
            left join
                penerbit p on p.id = l.penerbit_id
            left join
                jasa_pengiriman jp on jp.id = l.jasa_pengiriman_id
            left join
                branchs b on b.id = l.branch_id
            left join
                letter_detail ld on ld.letter_id = l.letter_id
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
                            select distinct
                                l.letter_id,
                                l.status,
                                l.receipt_no,
                                l.penerbit_id,
                                l.sent_date,
                                l.check_date,
                                b.name as name_branch,
                                jp.name as name_jasa_pengiriman,
                                p.name as name_penerbit,
                                case
                                    when l.status in ('TERKIRIM', 'CEK FISIK')
                                    then nvl(td.total_eks_delivery, 0)
                                    else 0
                                end as total_eks_delivery,
                                case
                                    when l.status in ('TERKIRIM', 'CEK FISIK')
                                    then nvl(td.total_title_delivery, 0)
                                    else 0
                                end as total_title_delivery
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
                                        sum(quantity) as total_title_delivery
                                    from
                                        letter_detail
                                    group by
                                        letter_id
                                ) td on td.letter_id = l.letter_id
                            left join
                                letter_detail ld on ld.letter_id = l.letter_id
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
                $statusAccept = ['DITERIMA PENUH', 'DITERIMA PARSIAL'];
                $iconClass = in_array($val->STATUS, $statusAccept) ? 'ph-info' : 'ph-check';
                $buttonText = 'Detail';
                $detailUrl = url('physical-delivery/delivery-verification/detail/' . $val->LETTER_ID);

                $action = '
                    <button type="button" class="btn btn-primary btn-sm text-nowrap" data-url="' . $detailUrl . '" onclick="window.location.href = this.getAttribute(\'data-url\');"><i class="' . $iconClass . ' me-1"></i>' . $buttonText . '</button>
                ';

                $sentDateHTML = '
                    <div>' . Carbon::parse($val->SENT_DATE)->isoFormat('D MMM Y') . '</div>
                    <small class="text-muted">Jam : ' . Carbon::parse($val->SENT_DATE)->format('H:i') . ' WIB</small>
                ';

                $sentDateDB = $val->SENT_DATE;
                $checkDateDB = null;
                $sentDate = Carbon::parse($sentDateDB);

                if ($checkDateDB) {
                    $endDate = Carbon::parse($checkDateDB);
                } else {
                    $endDate = Carbon::now();
                }

                $seconds = round($sentDate->diffInSeconds($endDate));
                $minutes = round($sentDate->diffInMinutes($endDate));
                $hours = round($sentDate->diffInHours($endDate));
                $days = round($sentDate->diffInDays($endDate));

                if ($days > 0) {
                    $aging = '
                        <div>' . $days . ' Hari</div>
                    ';
                } else if ($hours > 0) {
                    $aging = '
                        <div>' . $hours . ' Jam</div>
                    ';
                } else if ($minutes > 0) {
                    $aging = '
                        <div>' . $minutes . ' Menit</div>
                    ';
                } else if ($seconds > 0) {
                    $aging = '
                        <div>' . $seconds . ' Detik</div>
                    ';
                } else {
                    $aging = 'Tidak Ada';
                }

                $data[] = [
                    $start + 1,
                    $action,
                    $val->PENERBIT_ID . ' | ' . $val->NAME_PENERBIT,
                    $val->STATUS,
                    $sentDateHTML,
                    $aging,
                    $val->RECEIPT_NO,
                    $val->NAME_JASA_PENGIRIMAN,
                    $val->NAME_BRANCH,
                    $val->TOTAL_TITLE_DELIVERY,
                    $val->TOTAL_EKS_DELIVERY,
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

    public function detail(Request $request, $id)
    {
        if (!is_numeric($id)) {
            abort(404, 'Invalid letter ID');
        }

        try {
            $letterSql = "
                select
                    l.*,
                    jp.name as name_jasa_pengiriman,
                    p.name as name_penerbit
                from
                    letter l
                left join
                    penerbit p on p.id = l.penerbit_id
                left join
                    jasa_pengiriman jp on jp.id = l.jasa_pengiriman_id
                where
                    l.letter_id = $id
            ";

            $letter = QueryAPI::get($letterSql, true);

            if (!$letter) {
                abort(404, 'Letter not found');
            }

            $letterDetail = QueryAPI::get("
                select
                    *
                from
                    letter_detail
                where
                    letter_id = $id
            ", false);

            $currentStatus = $letter->STATUS ?? '';
            $isSuperAdmin = !Main::isNotSuperAdmin();
            $isBranchMatch = ($letter->BRANCH_ID ?? '') === session('branch_id');
            $verificatorUsername = $letter->IS_VERIFICATION_BY ?? '';
            $currentUser = session('username');
            $letterId = $letter->LETTER_ID ?? null;

            if ($isBranchMatch && $currentStatus === 'TERKIRIM') {
                QueryAPI::update('letter', $letterId, [
                    'status' => 'CEK FISIK'
                ], false);

                $letter = QueryAPI::get($letterSql, true);
                $currentStatus = $letter->STATUS ?? '';
                $verificatorUsername = $letter->IS_VERIFICATION_BY ?? '';
            }

            $isVerifiableNow = ($currentStatus === 'CEK FISIK') && $isBranchMatch;

            if (!empty($verificatorUsername) && $verificatorUsername !== $currentUser && !$isSuperAdmin) {
                echo '
                    <script>
                        alert("Sedang diverifikasi oleh ' . $verificatorUsername . '");
                        window.location.href = "' . url('physical-delivery/delivery-verification') . '";
                    </script>
                ';

                exit;
            }

            if ($request->ajax()) {
                try {
                    $param = $request->input('param');
                    $letterDetailIds = $request->input('letter_detail_id', []);
                    $quantities = $request->input('letter_detail_quantity', []);
                    $qtyAccepts = $request->input('letter_detail_qty_accept', []);
                    $qtyRejects = $request->input('letter_detail_qty_reject', []);
                    $remarks = $request->input('letter_detail_remark', []);
                    $checkeds = $request->input('letter_detail_checked', []);
                    $notes = $request->input('letter_detail_note', []);
                    $status = 'DITERIMA PENUH';

                    foreach ($letterDetailIds as $key => $detailId) {
                        $qtyAccept = $qtyAccepts[$key] ?? 0;
                        $qtyReject = $qtyRejects[$key] ?? 0;
                        $remark = $remarks[$key] ?? [];
                        $quantity = $quantities[$key] ?? 0;
                        $checked = $checkeds[$key] ?? 0;
                        $note = $notes[$key] ?? '';

                        QueryAPI::update('letter_detail', $detailId, [
                            'qty_accept' => $qtyAccept,
                            'qty_reject' => $qtyReject,
                            'remark' => is_array($remark) ? implode(';', $remark) : $remark,
                            'checked' => $checked ? 1 : null,
                            'isbn_status' => $note,
                            'received_by' => $checked ? session('username') : null,
                            'received_date' => $checked ? date('Y-m-d H:i:s') : null,
                        ], false);

                        if ($qtyAccept < $quantity) {
                            $status = 'DITERIMA PARSIAL';
                        }
                    }

                    $requestStatus = $request->input('status');
                    $letterUpdateData = [
                        'status' => ($param === 'save-verification') ? $status : $requestStatus,
                    ];

                    if ($param === 'save-verification') {
                        $letterUpdateData['accept_date'] = date('Y-m-d H:i:s');
                    }

                    $letterUpdateData['is_verification_by'] = session('username');
                    $letterUpdateData['proses_by'] = session('username');

                    if (empty($letter->CHECK_DATE ?? '') && in_array($letterUpdateData['status'], ['CEK FISIK', 'DITERIMA PENUH', 'DITERIMA PARSIAL'])) {
                        $letterUpdateData['check_date'] = date('Y-m-d H:i:s');
                    }

                    QueryAPI::update('letter', $letterId, $letterUpdateData, false);

                    return response()->json([
                        'code' => 200,
                        'message' => 'Data telah disimpan'
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error in AJAX request: ' . $e->getMessage(), [
                        'letter_id' => $letterId,
                        'param' => $request->input('param'),
                        'trace' => $e->getTraceAsString()
                    ]);

                    return response()->json([
                        'code' => 500,
                        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                    ], 500);
                }
            }

            return view('layouts.index', [
                'data' => [
                    'letter' => $letter,
                    'letterDetail' => $letterDetail,
                    'content' => 'physical-delivery.delivery-verification-detail',
                    'acceptDefault' => Main::isNotSuperAdmin() ? 1 : 2,
                    'plugins' => [
                        'select2',
                        'datatable',
                        'lightbox',
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in detail function: ' . $e->getMessage(), [
                'letter_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'code' => 500,
                    'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
                ], 500);
            }

            abort(500, 'Terjadi kesalahan sistem');
        }
    }
}
