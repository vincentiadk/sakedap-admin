<?php

namespace App\Http\Controllers\Delivery;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ListController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'deliveryService' => QueryAPI::get("select * from jasa_pengiriman"),
                'content' => 'delivery.list',
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
            'letter.letter_id',
            null,
            'penerbit.name',
            'letter.receipt_no',
            'jasa_pengiriman.name',
            'branchs.name',
            null,
            null,
            null,
            null,
            null,
            null,
            'letter.status',
            'letter.proses_by',
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

        if (Main::isNotCenterBranch()) {
            $whereCondition[] = 'penerbit.province_id = ' . session('province_id');
        }

        if ($request->receipt_no) {
            $receiptNo = strtoupper($request->receipt_no);
            $whereCondition[] = "upper(letter.receipt_no) like '%$receiptNo%'";
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

        if ($request->branch_id) {
            $whereCondition[] = "letter.branch_id = $request->branch_id";
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
                letter
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                letter
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
                                letter.letter_id,
                                letter.status,
                                letter.receipt_no,
                                letter.proses_by,
                                letter.penerbit_id,
                                branchs.name as name_branch,
                                jasa_pengiriman.name as name_jasa_pengiriman,
                                penerbit.name as name_penerbit,
                                coalesce(sum(letter_detail.copy), 0) as total_eks_delivery,
                                coalesce(sum(letter_detail.quantity), 0) as total_title_delivery,
                                coalesce(sum(case when letter_detail.qty_accept > 0 then letter_detail.qty_accept else 0 end), 0) as total_eks_receipt,
                                coalesce(sum(case when letter_detail.qty_accept > 0 then letter_detail.quantity else 0 end), 0) as total_title_receipt,
                                coalesce(sum(case when letter_detail.qty_hibah > 0 then letter_detail.qty_hibah else 0 end), 0) as total_eks_grant,
                                coalesce(sum(case when letter_detail.qty_hibah > 0 then letter_detail.quantity else 0 end), 0) as total_title_grant
                            from
                                letter
                            left join
                                penerbit on penerbit.id = letter.penerbit_id
                            left join
                                jasa_pengiriman on jasa_pengiriman.id = letter.jasa_pengiriman_id
                            left join
                                letter_detail on letter_detail.letter_id = letter.letter_id
                            left join
                                branchs on branchs.id = letter.branch_id
                            $whereClause
                            group by
                                letter.letter_id,
                                letter.status,
                                letter.receipt_no,
                                letter.proses_by,
                                letter.penerbit_id,
                                branchs.name,
                                jasa_pengiriman.name,
                                penerbit.name
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
                    <a href="' . url('delivery/list/verification/' . $val->LETTER_ID) . '" class="btn btn-primary btn-sm">
                        <i class="' . (in_array($val->STATUS, $statusAccept) ? 'ph-info' : 'ph-check') . ' me-1"></i>
                        ' . (in_array($val->STATUS, $statusAccept) ? 'Detail' : 'Verifikasi') . '
                    </a>
                ';

                if (in_array($val->STATUS, $statusAccept)) {
                    $action .= '
                        <a href="' . url('delivery/list/print/' . $val->LETTER_ID) . '" class="btn btn-success btn-sm" target="_blank">
                            <i class="ph-printer me-1"></i>
                            Resi Penerimaan
                        </a>
                    ';
                }

                $data[] = [
                    $start + 1,
                    $action,
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

    public function verification(Request $request, $id)
    {
        $letter = QueryAPI::get("
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
        ", true);

        $letterDetail = QueryAPI::get("
            select
                letter_detail.*,
                worksheets.name as name_worksheet
            from
                letter_detail
            left join
                catalogs on catalogs.id = letter_detail.catalog_id
            left join
                worksheets on worksheets.id = catalogs.worksheet_id
            where
                letter_detail.letter_id = $id
        ");

        if ($request->ajax()) {
            try {
                $letterDetailIds = $request->collect('letter_detail_id');
                $quantities = $request->collect('letter_detail_quantity');
                $qtyAccepts = $request->collect('letter_detail_qty_accept');
                $qtyRejects = $request->collect('letter_detail_qty_reject');
                $remarks = $request->collect('letter_detail_remark');
                $param = $request->param;
                $status = 'DITERIMA PENUH';
                $letterDetailsToUpdate = [];

                foreach ($letterDetailIds as $key => $ldi) {
                    $qtyAccept = $qtyAccepts->get($key, 0);
                    $qtyReject = $qtyRejects->get($key, 0);
                    $remark = $remarks->get($key, []);
                    $quantity = $quantities->get($key, 0);

                    $letterDetailsToUpdate[] = [
                        'id' => $ldi,
                        'qty_accept' => $qtyAccept,
                        'qty_reject' => $qtyReject,
                        'remark' => is_array($remark) ? implode(';', $remark) : $remark
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

                QueryAPI::update('letter', $id, [
                    'status' => ($param === 'save-verification') ? $status : $request->status,
                    'accept_date' => ($param === 'save-verification') ? date('Y-m-d H:i:s') : null,
                    'proses_by' => ($param === 'save-verification') ? session('name') : null,
                ], false);

                $response = [
                    'code' => 200,
                    'message' => 'Data telah disimpan'
                ];
            } catch (\Exception $e) {
                $response = [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage()
                ];
            }

            return response()->json($response);
        }

        if (in_array(($letter->STATUS ?? ''), ['DALAM PENGIRIMAN', 'TERKIRIM', 'CEK FISIK']) && ($letter->BRANCH_ID ?? '') == session('branch_id')) {
            $disable = null;
        } else {
            $disable = 'disabled';
        }

        return view('layouts.index', [
            'data' => [
                'letter' => $letter,
                'letterDetail' => $letterDetail,
                'disabled' => $disable,
                'content' => 'delivery.list-verification',
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
            return redirect('delivery/list');
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
            return redirect('delivery/list');
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
            return redirect('delivery/list');
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
                letter_detail.letter_id,
                letter.accept_date as accept_date_letter,
                letter_detail.title,
                letter_detail.jenis_media,
                letter_detail.isbn,
                letter_detail.qty_accept
            from
                letter_detail
            left join
                letter on letter.letter_id = letter_detail.letter_id
            where
                letter.letter_id = $letter->LETTER_ID and
                letter_detail.qty_accept > 0
        ");

        $htmlCollections = '<table border="1" style="font-size:8px">';
        $htmlCollections .= '<tr>';
        $htmlCollections .= '<th style="padding:12px;text-align: center;">No</th>';
        $htmlCollections .= '<th style="padding:12px;text-align: center;">Tanggal Terima</th>';
        $htmlCollections .= '<th style="padding:12px;text-align: center;">Judul</th>';
        $htmlCollections .= '<th style="padding:12px;text-align: center;">Jenis Media</th>';
        $htmlCollections .= '<th style="padding:12px;text-align: center;">ISBN/ISSN</th>';
        $htmlCollections .= '<th style="padding:12px;text-align: center;">Jumlah (Eksemplar)</th>';
        $htmlCollections .= '</tr>';

        if ($collections) {
            foreach ($collections as $key => $c) {
                $htmlCollections .= '<tr>';
                $htmlCollections .= '<td style="padding:10px;text-align: center;">' . ($key + 1) . '</td>';
                $htmlCollections .= '<td style="padding:10px;text-align: center;">' . date('d-m-Y', strtotime($c->ACCEPT_DATE_LETTER)) . '</td>';
                $htmlCollections .= '<td style="padding:10px;text-align: center;">' . ($c->TITLE ?? '-') . '</td>';
                $htmlCollections .= '<td style="padding:10px;text-align: center;">' . ($c->JENIS_MEDIA ?? '-') . '</td>';
                $htmlCollections .= '<td style="padding:10px;text-align: center;">' . ($c->ISBN ?? '-') . '</td>';
                $htmlCollections .= '<td style="padding:10px;text-align: center;">' . ($c->QTY_ACCEPT ?? '-') . '</td>';
                $htmlCollections .= '</tr>';
            }
        }

        $htmlCollections .= '</table>';

        $pdf->AddPage();
        $pdf->writeHTML($htmlCollections, true, false, true, false, '');

        $letterNumberUT = $letter->LETTER_NUMBER_UT ?? date('YmdHis');
        $filename = storage_path("app/public/delivery/list/receipt/$letterNumberUT.pdf");

        return $pdf->output($filename, 'I');
    }
}
