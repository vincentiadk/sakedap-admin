<?php

namespace App\Http\Controllers\PhysicalDelivery;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class AcceptController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'deliveryService' => QueryAPI::get("select * from jasa_pengiriman") ?? [],
                'prosesBy' => QueryAPI::get("select distinct(proses_by) from letter where proses_by is not null") ?? [],
                'content' => 'physical-delivery.accept',
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
            'l.accept_date',
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
        $whereCondition[] = "l.status in ('DITERIMA PENUH', 'DITERIMA PARSIAL')";

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
                                l.accept_date,
                                b.name as name_branch,
                                jp.name as name_jasa_pengiriman,
                                p.name as name_penerbit,
                                coalesce(td.total_eks_receipt, 0) as total_eks_receipt,
                                coalesce(td.total_title_receipt, 0) as total_title_receipt,
                                case
                                    when l.status in ('TERKIRIM', 'CEK FISIK', 'DITERIMA PENUH', 'DITERIMA PARSIAL', 'RETUR')
                                    then coalesce(td.total_eks_delivery, 0)
                                    else 0
                                end as total_eks_delivery,
                                case
                                    when l.status in ('TERKIRIM', 'CEK FISIK', 'DITERIMA PENUH', 'DITERIMA PARSIAL', 'RETUR')
                                    then coalesce(td.total_title_delivery, 0)
                                    else 0
                                end as total_title_delivery,
                                case
                                    when l.status in ('TERKIRIM', 'CEK FISIK', 'DITERIMA PENUH', 'DITERIMA PARSIAL', 'RETUR')
                                    then coalesce(td.total_eks_grant, 0)
                                    else 0
                                end as total_eks_grant,
                                case
                                    when l.status in ('TERKIRIM', 'CEK FISIK', 'DITERIMA PENUH', 'DITERIMA PARSIAL', 'RETUR')
                                    then coalesce(td.total_title_grant, 0)
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
                )
            where
                rnum > $start and rnum <= $length
        ");

        if ($queryData) {
            foreach ($queryData as $val) {
                $statusAccept = ['DITERIMA PENUH', 'DITERIMA PARSIAL'];

                $action = '
                    <a href="' . url('physical-delivery/accept/detail/' . $val->LETTER_ID) . '" class="btn btn-primary btn-sm text-nowrap">
                        <i class="' . (in_array($val->STATUS, $statusAccept) ? 'ph-info' : 'ph-check') . ' me-1"></i>
                        ' . (in_array($val->STATUS, $statusAccept) ? 'Detail' : 'Verifikasi') . '
                    </a>
                ';

                if (in_array($val->STATUS, $statusAccept)) {
                    $action .= '
                        <a href="' . url('physical-delivery/accept/print/' . $val->LETTER_ID) . '" class="btn btn-success btn-sm mt-1 text-nowrap" target="_blank">
                            <i class="ph-printer me-1"></i>
                            Resi Penerimaan
                        </a>
                    ';

                    $action .= '
                        <a href="javascript:void(0);" class="btn btn-danger btn-sm mt-1 text-nowrap" onclick="sendEmail(' . $val->LETTER_ID . ')">
                            <i class="ph-envelope-open me-1"></i>
                            Kirim Email
                        </a>
                    ';
                }

                $data[] = [
                    $start + 1,
                    $action,
                    ($val->ACCEPT_DATE ?: null) ? Carbon::parse($val->ACCEPT_DATE)->isoFormat('dddd, D MMMM Y') : '',
                    $val->IS_VERIFICATION_BY,
                    $val->PENERBIT_ID . ' | ' . $val->NAME_PENERBIT,
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
                    'is_verification_by' => session('username')
                ], false);

                $letter = QueryAPI::get($letterSql, true);
            }
        }

        if ($request->ajax()) {
            try {
                $param = $request->param;

                if ($param == 'cancel') {
                    QueryAPI::update('letter', $id, [
                        'is_verification_by' => null
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
                'content' => 'physical-delivery.accept-detail',
                'acceptDefault' => Main::isNotCenterBranch() ? 1 : 2,
                'plugins' => [
                    'select2',
                    'datatable',
                ]
            ]
        ]);
    }

    public function print($id)
    {
        if (!isset($id)) {
            return redirect('physical-delivery/accept');
        }

        $letter = QueryAPI::get("
            select
                letter.*,
                penerbit.name as name_penerbit
            from
                letter
            left join
                penerbit on penerbit.id = letter.penerbit_id
            where
                letter.letter_id = $id
        ", true);

        if (!$letter) {
            return redirect('physical-delivery/accept');
        }

        $settings = QueryAPI::get("
            select
                *
            from
                e_settings
            where
                slug in ('ResiPenerimaan','Header','Footer')
        ");

        $templateEmailContent = null;
        $templateEmailHeader = null;
        $templateEmailFooter = null;

        if ($settings) {
            foreach ($settings as $setting) {
                if ($setting->SLUG == 'ResiPenerimaan') {
                    $templateEmailContent = $setting;
                } elseif ($setting->SLUG == 'Header') {
                    $templateEmailHeader = $setting;
                } elseif ($setting->SLUG == 'Footer') {
                    $templateEmailFooter = $setting;
                }
            }
        }

        if (!$templateEmailContent || !$templateEmailHeader || !$templateEmailFooter) {
            return redirect('physical-delivery/accept');
        }

        $branchId = session('branch_id');
        $dateNow = date('Y-m-d');
        $signature = '';

        $leader = QueryAPI::get("
            select
                *
            from
                penanggung_jawab
            where
                branch_id = $branchId and
                (tanggal_awal <= to_date('$dateNow', 'YYYY-MM-DD') and tanggal_akhir >= to_date('$dateNow', 'YYYY-MM-DD') + 1)
        ", true);

        if ($leader) {
            $signatureUrl = url('stream-file') . '?type=gambar_ttd&id=' . ($leader->ID ?? '') . '&filename=' . ($leader->TTD_FILE_NAME ?? '');
            $signature = $leader->JABATAN . '<br><br>' . '<img src="' . $signatureUrl . '" style="max-width:40px !important;"><br><br>' . $leader->NAMA . '<br>' . '<span style="font-weight:bold;">' . $leader->NIP . '</span>';
        }

        $dataParseTemplate = [
            'accepted_date' => date('d/m/Y', strtotime($letter->ACCEPT_DATE)),
            'letter_no' => $letter->LETTER_NUMBER_UT,
            'publisher_name' => $letter->NAME_PENERBIT,
            'director' => $signature,
            'header' => '<img src="' . url('stream-file?type=gambar_template&id=' . ($templateEmailHeader->ID ?? '') . '&filename=' . ($templateEmailHeader->CONTENT ?? '')) . '" style="max-width:100%;">',
            'footer' => '<img src="' . url('stream-file?type=gambar_template&id=' . ($templateEmailFooter->ID ?? '') . '&filename=' . ($templateEmailFooter->CONTENT ?? '')) . '" style="max-width:100%; margin-bottom:10px">',
            'qr' => 'https://image-charts.com/chart?chs=150x150&cht=qr&chl=' . $letter->LETTER_NUMBER_UT,
        ];

        $pdf = new \TCPDF();
        $pdf->SetMargins(10, 5, 10, 0);
        $pdf->SetAutoPageBreak(true, 0);
        $pdf->AddPage();
        $html = Main::parseTemplateEmail($dataParseTemplate, $templateEmailContent);
        $pdf->writeHTML($html, true, false, true, false, '');

        $collections = QueryAPI::get("
            select
                ld.letter_id,
                l.accept_date as accept_date_letter,
                ld.title,
                ld.jenis_media,
                ld.isbn,
                case when ld.collection_id LIKE '%,%' and t.lvl > 0 THEN 1 ELSE ld.qty_accept end as qty_accept,
                c.noinduk as noinduk_collection,
                c.mark_province as mark_province_collection
            from
                letter_detail ld
            left join
                letter l on l.letter_id = ld.letter_id
            cross join
                (select level as lvl from dual connect by level <= 1000) t
            left join
                collections c on c.id = to_number(
                    nvl(
                        trim(
                            regexp_substr(
                                ld.collection_id,
                                '[^,]+',
                                1,
                                t.lvl
                            )
                        ),
                        '0'
                    )
                )
            where
                ld.letter_id = $letter->LETTER_ID
                and ld.qty_accept is not null
                and ld.qty_accept > 0
                and T.lvl <= regexp_count(nvl(ld.collection_id, 'X'), ',') + 1
        ");

        $htmlCollections = '<table border="1" style="font-size:8px">';
        $htmlCollections .= '<tr>';
        $htmlCollections .= '<th style="padding:12px;text-align: center;">No</th>';
        $htmlCollections .= '<th style="padding:12px;text-align: center;">Tanggal Terima</th>';
        $htmlCollections .= '<th style="padding:12px;text-align: center;">Judul</th>';
        $htmlCollections .= '<th style="padding:12px;text-align: center;">Jenis Media</th>';
        $htmlCollections .= '<th style="padding:12px;text-align: center;">ISBN/ISSN</th>';
        $htmlCollections .= '<th style="padding:12px;text-align: center;">Jumlah (Eksemplar)</th>';
        $htmlCollections .= '<th style="padding:12px;text-align: center;">TRK</th>';
        $htmlCollections .= '</tr>';

        if ($collections) {
            foreach ($collections as $key => $c) {
                $trk = Main::isNotCenterBranch() ? $c->MARK_PROVINCE_COLLECTION : $c->NOINDUK_COLLECTION;

                $htmlCollections .= '<tr>';
                $htmlCollections .= '<td style="padding:10px;text-align: center;">' . ($key + 1) . '</td>';
                $htmlCollections .= '<td style="padding:10px;text-align: center;">' . date('d-m-Y', strtotime($c->ACCEPT_DATE_LETTER)) . '</td>';
                $htmlCollections .= '<td style="padding:10px;text-align: center;">' . ($c->TITLE ?? '-') . '</td>';
                $htmlCollections .= '<td style="padding:10px;text-align: center;">' . ($c->JENIS_MEDIA ?? '-') . '</td>';
                $htmlCollections .= '<td style="padding:10px;text-align: center;">' . ($c->ISBN ?? '-') . '</td>';
                $htmlCollections .= '<td style="padding:10px;text-align: center;">' . ($c->QTY_ACCEPT ?? '-') . '</td>';
                $htmlCollections .= '<td style="padding:10px;text-align: center;">' . ($trk ?? '-') . '</td>';
                $htmlCollections .= '</tr>';
            }
        }

        $htmlCollections .= '</table>';

        $pdf->AddPage();
        $pdf->writeHTML($htmlCollections, true, false, true, false, '');

        $letterNumberUT = $letter->LETTER_NUMBER_UT ?? date('YmdHis');
        $filename = storage_path("app/public/physical-delivery/accept/receipt/$letterNumberUT.pdf");

        return $pdf->output($filename, 'I');
    }

    public function sendEmail(Request $request)
    {
        $letterId = $request->id;

        $letter = QueryAPI::get("
            select
                letter.*,
                penerbit.name as name_penerbit,
                penerbit.email1 as email_penerbit
            from
                letter
            join
                penerbit on penerbit.id = letter.penerbit_id
            where
                letter.letter_id = $letterId
        ", true);

        if ($letter) {
            if ($letter->EMAIL_PENERBIT) {
                $settings = QueryAPI::get("
                    select
                        *
                    from
                        e_settings
                    where
                        slug in ('ResiPenerimaan','Header','Footer')
                ");

                $templateEmailContent = null;
                $templateEmailHeader = null;
                $templateEmailFooter = null;

                if ($settings) {
                    foreach ($settings as $setting) {
                        if ($setting->SLUG == 'ResiPenerimaan') $templateEmailContent = $setting;
                        elseif ($setting->SLUG == 'Header') $templateEmailHeader = $setting;
                        elseif ($setting->SLUG == 'Footer') $templateEmailFooter = $setting;
                    }
                }

                $dateNow = date('Y-m-d');
                $branchId = $letter->BRANCH_ID ?? 0;

                $leader = QueryAPI::get("
                    select
                        *
                    from
                        penanggung_jawab
                    where
                        branch_id = $branchId and
                        (tanggal_awal <= to_date('$dateNow', 'YYYY-MM-DD') and tanggal_akhir >= to_date('$dateNow', 'YYYY-MM-DD') + 1)
                ", true);

                $signature = '';

                if ($leader) {
                    $signatureUrl = url('stream-file') . '?type=gambar_ttd&id=' . ($leader->ID ?? 0) . '&filename=' . ($leader->TTD_FILE_NAME ?? 0);
                    $signature = $leader->JABATAN . '<br><br>' . '<img src="' . $signatureUrl . '" style="max-width:40px !important;"><br><br>' . $leader->NAMA . '<br>' . '<span style="font-weight:bold;">' . $leader->NIP . '</span>';
                }

                $bodyParamEmail = [
                    'accepted_date' => date('d/m/Y', strtotime($letter->ACCEPT_DATE ?? '')),
                    'letter_no' => $letter->LETTER_NUMBER_UT ?? '',
                    'publisher_name' => $letter->NAME_PENERBIT ?? '',
                    'director' => $signature,
                    'header' => '<img src="' . Main::base64File(url('stream-file?type=gambar_template&id=' . ($templateEmailHeader->ID ?? 0) . '&filename=' . ($templateEmailHeader->CONTENT ?? ''))) . '" style="max-width:100%;">',
                    'footer' => '<img src="' . Main::base64File(url('stream-file?type=gambar_template&id=' . ($templateEmailFooter->ID ?? 0) . '&filename=' . ($templateEmailFooter->CONTENT ?? ''))) . '" style="max-width:100%; margin-bottom:10px">',
                    'qr' => 'https://image-charts.com/chart?chs=150x150&cht=qr&chl=' . $letter->LETTER_NUMBER_UT,
                ];

                Mail::send([], [], function ($message) use ($bodyParamEmail, $templateEmailContent, $letter) {
                    $message->to($letter->EMAIL_PENERBIT ?? '', $bodyParamEmail['publisher_name'])
                        ->subject('Resi Penerimaan')
                        ->from(config('mail.from.address'), config('mail.from.name'))
                        ->html(Main::parseTemplateEmail($bodyParamEmail, $templateEmailContent), 'text/html');
                });

                $response = [
                    'code' => 200,
                    'message' => 'Email berhasil dikirim'
                ];
            } else {
                $response = [
                    'code' => 401,
                    'message' => 'Email pelaksana serah kosong'
                ];
            }
        } else {
            $response = [
                'code' => 404,
                'message' => 'Data tidak ditemukan'
            ];
        }

        return response()->json($response);
    }
}
