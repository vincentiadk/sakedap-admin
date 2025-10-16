<?php

namespace App\Http\Controllers\PhysicalDelivery;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeliveryVerificationController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'deliveryService' => QueryAPI::get("select * from jasa_pengiriman"),
                'prosesBy' => QueryAPI::get("select distinct(proses_by) from letter where proses_by is not null"),
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
            'l.is_verification_by',
            'p.name',
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
            'l.proses_by',
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

        if (Main::isNotCenterBranch()) {
            $whereCondition[] = 'p.province_id = ' . session('province_id');
        }

        if ($request->proses_by) {
            $whereCondition[] = "l.proses_by = '$request->proses_by'";
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
                                l.letter_id,
                                l.status,
                                l.receipt_no,
                                l.proses_by,
                                l.penerbit_id,
                                l.is_verification_by,
                                b.name as name_branch,
                                jp.name as name_jasa_pengiriman,
                                p.name as name_penerbit,
                                case
                                    when l.status in ('TERKIRIM', 'CEK FISIK', 'DITERIMA PENUH', 'DITERIMA PARSIAL', 'RETUR')
                                    then coalesce(td.total_eks_delivery, 0)
                                    else 0
                                end as total_eks_delivery,
                                case
                                    when l.status in ('TERKIRIM', 'CEK FISIK', 'DITERIMA PENUH', 'DITERIMA PARSIAL', 'RETUR')
                                    then coalesce(td.total_title_delivery, 0)
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
                            $whereClause
                            $orderBy
                        ) data
                )
            where
                rnum > $start and rnum <= $length
        ");

        if ($queryData) {
            foreach ($queryData as $val) {
                $statusAccept = ['DITERIMA PENUH', 'DITERIMA PARSIAL'];

                $action = '
                    <a href="' . url('physical-delivery/delivery-verification/detail/' . $val->LETTER_ID) . '" class="btn btn-primary btn-sm text-nowrap">
                        <i class="' . (in_array($val->STATUS, $statusAccept) ? 'ph-info' : 'ph-check') . ' me-1"></i>
                        Detail
                    </a>
                ';

                $data[] = [
                    $start + 1,
                    $action,
                    $val->IS_VERIFICATION_BY,
                    $val->PENERBIT_ID . ' | ' . $val->NAME_PENERBIT,
                    $val->RECEIPT_NO,
                    $val->NAME_JASA_PENGIRIMAN,
                    $val->NAME_BRANCH,
                    $val->TOTAL_TITLE_DELIVERY,
                    $val->TOTAL_EKS_DELIVERY,
                    $val->STATUS,
                    $val->PROSES_BY,
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
        $letterSql = "
            select
                letter.*,
                jasa_pengiriman.name as name_jasa_pengiriman,
                penerbit.name as name_penerbit
            from
                letter
            left join
                penerbit on penerbit.id = letter.penerbit_id
            left join
                jasa_pengiriman on jasa_pengiriman.id = letter.jasa_pengiriman_id
            where
                letter.letter_id = $id
        ";

        $letter = QueryAPI::get($letterSql, true);

        $letterDetail = QueryAPI::get("
            select
                *
            from
                letter_detail
            where
                letter_id = $id
        ");

        $disable = 'disabled';
        $currentStatus = $letter->STATUS ?? '';
        $isUserVerificator = ($letter->IS_VERIFICATION_BY ?? '') === session('username');
        $isBranchMatch = ($letter->BRANCH_ID ?? '') === session('branch_id');

        if ($isBranchMatch && in_array($currentStatus, ['TERKIRIM'])) {
            QueryAPI::update('letter', $id, [
                'status' => 'CEK FISIK'
            ], false);

            $letter = QueryAPI::get($letterSql, true);
            $currentStatus = $letter->STATUS ?? '';
        }

        if ($isUserVerificator) {
            $disable = null;
        } else {
            $isVerifiableNow = ($currentStatus === 'CEK FISIK') && $isBranchMatch;

            if ($isVerifiableNow && empty($letter->IS_VERIFICATION_BY)) {
                $disable = null;

                QueryAPI::update('letter', $id, [
                    'is_verification_by' => session('username'),
                    'proses_by' => session('name'),
                ], false);

                $letter = QueryAPI::get($letterSql, true);
            }
        }

        if ($request->ajax()) {
            try {
                $param = $request->param;

                if ($param == 'cancel') {
                    QueryAPI::update('letter', $id, [
                        'is_verification_by' => null,
                        'proses_by' => null,
                    ], false);

                    $response = [
                        'code' => 200,
                        'message' => 'Verifikasi telah dibatalkan'
                    ];
                } else {
                    $letterDetailIds = $request->collect('letter_detail_id');
                    $quantities = $request->collect('letter_detail_quantity');
                    $qtyAccepts = $request->collect('letter_detail_qty_accept');
                    $qtyRejects = $request->collect('letter_detail_qty_reject');
                    $remarks = $request->collect('letter_detail_remark');
                    $checkeds = $request->collect('letter_detail_checked');
                    $notes = $request->collect('letter_detail_note');
                    $status = 'DITERIMA PENUH';
                    $letterDetailsToUpdate = [];

                    foreach ($letterDetailIds as $key => $ldi) {
                        $qtyAccept = $qtyAccepts->get($key, 0);
                        $qtyReject = $qtyRejects->get($key, 0);
                        $remark = $remarks->get($key, []);
                        $quantity = $quantities->get($key, 0);
                        $checked = $checkeds->get($key, 0);
                        $note = $notes->get($key, 0);

                        $letterDetailsToUpdate[] = [
                            'id' => $ldi,
                            'qty_accept' => $qtyAccept,
                            'qty_reject' => $qtyReject,
                            'remark' => is_array($remark) ? implode(';', $remark) : $remark,
                            'checked' => $checked,
                            'isbn_status' => $note,
                        ];

                        if ($qtyAccept < $quantity) {
                            $status = 'DITERIMA PARSIAL';
                        }
                    }

                    foreach ($letterDetailsToUpdate as $updateData) {
                        $letterId = $updateData['id'];

                        unset($updateData['id']);

                        QueryAPI::update('letter_detail', $letterId, $updateData, false);
                    }

                    $requestStatus = $request->status;

                    QueryAPI::update('letter', $id, [
                        'status' => ($param === 'save-verification') ? $status : $requestStatus,
                        'accept_date' => ($param === 'save-verification') ? date('Y-m-d H:i:s') : null,
                        'proses_by' => in_array($requestStatus, ['CEK FISIK', 'DITERIMA PENUH', 'DITERIMA PARSIAL']) ? session('name') : null,
                    ], false);

                    $response = [
                        'code' => 200,
                        'message' => 'Data telah disimpan'
                    ];
                }
            } catch (\Exception $e) {
                $response = [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage()
                ];
            }

            return response()->json($response);
        }

        return view('layouts.index', [
            'data' => [
                'letter' => $letter,
                'letterDetail' => $letterDetail,
                'disabled' => $disable,
                'content' => 'physical-delivery.delivery-verification-detail',
                'acceptDefault' => Main::isNotCenterBranch() ? 1 : 2,
                'plugins' => [
                    'select2',
                    'datatable',
                ]
            ]
        ]);
    }
}
