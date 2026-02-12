<?php

namespace App\Http\Controllers\PhysicalDelivery;

use Carbon\Carbon;
use App\Helpers\Main;
use Milon\Barcode\DNS2D;
use App\Helpers\Barantum;
use App\Helpers\QueryAPI;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AcceptController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'deliveryService' => QueryAPI::get("select * from jasa_pengiriman") ?? [],
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
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "l.status in ('DITERIMA PENUH', 'DITERIMA PARSIAL', 'DITERIMA')";

        if (!Main::isSuperAdmin()) {
            $whereCondition[] = 'b.province_id = ' . session('province_id');
        }

        if ($request->receipt_no) {
            $receiptNo = strtoupper($request->receipt_no);
            $whereCondition[] = "upper(l.receipt_no) like '%$receiptNo%'";
        }

        if ($request->delivery_service_id) {
            $whereCondition[] = "l.jasa_pengiriman_id = $request->delivery_service_id";
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
                            select distinct
                                l.*,
                                b.name as name_branch,
                                jp.name as name_jasa_pengiriman,
                                p.name as name_penerbit,
                                nvl(td.total_eks_receipt, 0) as total_eks_receipt,
                                nvl(td.total_title_receipt, 0) as total_title_receipt,
                                case
                                    when l.status in ('DITERIMA PENUH', 'DITERIMA PARSIAL', 'DITERIMA')
                                    then nvl(td.total_eks_delivery, 0)
                                    else 0
                                end as total_eks_delivery,
                                case
                                    when l.status in ('DITERIMA PENUH', 'DITERIMA PARSIAL', 'DITERIMA')
                                    then nvl(td.total_title_delivery, 0)
                                    else 0
                                end as total_title_delivery,
                                case
                                    when l.status in ('DITERIMA PENUH', 'DITERIMA PARSIAL', 'DITERIMA')
                                    then nvl(td.total_eks_grant, 0)
                                    else 0
                                end as total_eks_grant,
                                case
                                    when l.status in ('DITERIMA PENUH', 'DITERIMA PARSIAL', 'DITERIMA')
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
                        <a href="javascript:void(0);" class="btn btn-teal btn-sm mt-1 text-nowrap" onclick="sendWhatsapp(' . $val->LETTER_ID . ')">
                            <i class="ph-whatsapp-logo me-1"></i>
                            Kirim Whatsapp
                        </a>
                    ';
                }

                $acceptDateHTML = '';

                if ($val->ACCEPT_DATE ?: null) {
                    $acceptDateHTML = '
                        <div>' . Carbon::parse($val->ACCEPT_DATE)->isoFormat('D MMM Y') . '</div>
                        <small class="text-muted">Jam : ' . Carbon::parse($val->ACCEPT_DATE)->format('H:i') . ' WIB</small>
                    ';
                }

                $data[] = [
                    $start + 1,
                    $action,
                    $acceptDateHTML,
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
                penerbit.name as name_penerbit,
                branchs.name as name_branch
            from
                letter
            left join
                penerbit on penerbit.id = letter.penerbit_id
            left join
                jasa_pengiriman on jasa_pengiriman.id = letter.jasa_pengiriman_id
            left join
                branchs on branchs.id = letter.branch_id
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

        return view('layouts.index', [
            'data' => [
                'letter' => $letter,
                'letterDetail' => $letterDetail,
                'content' => 'physical-delivery.accept-detail',
                'plugins' => [
                    'select2',
                    'datatable',
                    'lightbox',
                ]
            ]
        ]);
    }

    public function print($id)
    {
        if (empty($id)) {
            abort(404);
        }

        $pdfPath = $this->generatePDF($id);

        if (!$pdfPath || !file_exists($pdfPath)) {
            abort(404, 'File PDF tidak ditemukan.');
        }

        $stream = fopen($pdfPath, 'rb');
        $filename = basename($pdfPath);

        return response()->stream(function () use ($stream) {
            fpassthru($stream);

            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
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

        if (!$letter) {
            return response()->json([
                'code' => 404,
                'message' => 'Data surat tidak ditemukan'
            ]);
        }

        if (empty($letter->EMAIL_PENERBIT)) {
            return response()->json([
                'code' => 401,
                'message' => 'Email penerbit/pelaksana serah kosong'
            ]);
        }

        $pdfPath = $this->generatePDF($letterId);

        if (!$pdfPath || !file_exists($pdfPath)) {
            return response()->json([
                'code' => 500,
                'message' => 'Gagal membuat file PDF Resi'
            ]);
        }

        try {
            $acceptDate = $letter->ACCEPT_DATE ? Carbon::parse($letter->ACCEPT_DATE)->isoFormat('D MMMM Y') : date('d-m-Y');
            $receiptNo = $letter->RECEIPT_NO ?? '-';
            $emailSubject = "Bukti Penerimaan Serah Simpan Karya Cetak / Karya Rekam - " . $receiptNo;

            $emailBody = "
                <p>Kepada Yth. <br><b>{$letter->NAME_PENERBIT}</b></p>
                <p>Dengan hormat,</p>
                <p>Terima kasih telah melaksanakan kewajiban Serah Simpan Karya Cetak dan Karya Rekam (SSKCKR).</p>
                <p>Bersama email ini, kami sampaikan <b>Bukti Penerimaan (Resi)</b> atas koleksi fisik yang telah kami terima pada tanggal <b>{$acceptDate}</b>.</p>
                <p>Dokumen resi terlampir dalam format PDF. Silakan unduh lampiran tersebut sebagai arsip bukti serah simpan.</p>
                <br>
                <p>Atas perhatian dan kerja sama Saudara dalam melestarikan hasil karya bangsa, kami ucapkan terima kasih.</p>
                <br>
                <hr>
                <small><i>Email ini dikirim secara otomatis oleh sistem. Mohon untuk tidak membalas email ini.</i></small>
            ";

            Mail::send([], [], function ($message) use ($letter, $pdfPath, $emailSubject, $emailBody, $receiptNo) {
                $message->to($letter->EMAIL_PENERBIT, $letter->NAME_PENERBIT)
                    ->subject($emailSubject)
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->html($emailBody)
                    ->attach($pdfPath, [
                        'as' => 'Resi_Penerimaan_' . str_replace('/', '_', $receiptNo) . '.pdf',
                        'mime' => 'application/pdf',
                    ]);
            });

            if (file_exists($pdfPath)) {
                @unlink($pdfPath);
            }

            return response()->json([
                'code' => 200,
                'message' => 'Email beserta lampiran PDF berhasil dikirim'
            ]);
        } catch (\Exception $e) {
            if (isset($pdfPath) && file_exists($pdfPath)) {
                @unlink($pdfPath);
            }

            Log::error('Gagal kirim email resi: ' . $e->getMessage());

            return response()->json([
                'code' => 500,
                'message' => 'Terjadi kesalahan saat mengirim email: ' . $e->getMessage()
            ]);
        }
    }

    public function sendWhatsapp(Request $request)
    {
        $letterId = $request->id;

        $letter = QueryAPI::get("
            select
                letter.*,
                penerbit.name as name_penerbit,
                penerbit.kontak1 as kontak_penerbit
            from
                letter
            join
                penerbit on penerbit.id = letter.penerbit_id
            where
                letter.letter_id = $letterId
        ", true);

        if (!$letter) {
            return response()->json([
                'code' => 404,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        if (!$letter->KONTAK_PENERBIT) {
            return response()->json([
                'code' => 401,
                'message' => 'Telp pelaksana serah kosong'
            ], 401);
        }

        $pdfPath = $this->generatePDF($letterId);

        if (!$pdfPath) {
            return response()->json([
                'code' => 500,
                'message' => 'Gagal generate PDF'
            ], 500);
        }

        $dateNow = date('Y-m-d');
        $targetNumber = $letter->KONTAK_PENERBIT;
        $acceptDate = $letter->ACCEPT_DATE ? Carbon::parse($letter->ACCEPT_DATE)->isoFormat('D MMMM Y') : '';
        $branchId = $letter->BRANCH_ID ?? 0;

        $leader = QueryAPI::get("
            select
                *
            from
                penanggung_jawab
            where
                branch_id = $branchId and
                (
                    tanggal_awal <= to_date('$dateNow', 'YYYY-MM-DD')
                    and tanggal_akhir >= to_date('$dateNow', 'YYYY-MM-DD') + 1
                )
        ", true);

        $leaderName = $leader->NAMA ?? '';

        $bodyMessage = "📋 *BUKTI PENERIMAAN*\n";
        $bodyMessage .= "*Karya Cetak / Karya Rekam*\n\n";
        $bodyMessage .= "Kepada Yth.\n";
        $bodyMessage .= "🙋 *{$leaderName}*\n\n";
        $bodyMessage .= "Dengan hormat,\n\n";
        $bodyMessage .= "Kami informasikan bahwa telah menerima Karya Cetak/Karya Rekam yang Saudara kirimkan dengan rincian:\n\n";
        $bodyMessage .= "📅 *Tanggal Penerimaan:* {$acceptDate}\n\n";
        $bodyMessage .= "✅ Atas kerja sama dan kepatuhan Saudara dalam melaksanakan *UU No.13 Tahun 2018* tentang Serah Simpan Karya Cetak dan Karya Rekam, kami ucapkan terima kasih.\n\n";
        $bodyMessage .= "📄 *Bukti penerimaan terlampir dalam file PDF*\n\n";

        $sendWhatsapp = Barantum::send($targetNumber, $letter->NAME_PENERBIT ?? 'Penerbit', [$bodyMessage], $pdfPath);

        if (file_exists($pdfPath)) {
            @unlink($pdfPath);
        }

        return response()->json($sendWhatsapp);
    }

    private function generatePDF($letterId)
    {
        try {
            $letter = QueryAPI::get("
                select
                    letter.*,
                    penerbit.name as name_penerbit
                from
                    letter
                left join
                    penerbit on penerbit.id = letter.penerbit_id
                where
                    letter.letter_id = $letterId
            ", true);

            if (!$letter) {
                return null;
            }

            $settings = QueryAPI::get("
                select
                    *
                from
                    e_settings
                where
                    slug = 'ResiPenerimaan' or
                    (slug in ('Header','Footer') and province_id = " . session('province_id') . ")
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

            $imgHeader = '';
            $imgFooter = '';

            if ($templateEmailHeader) {
                $urlHeader = url('stream-file?type=gambar_template&id=' . ($templateEmailHeader->ID ?? '') . '&filename=' . ($templateEmailHeader->CONTENT ?? ''));
                $imgHeader = Main::base64File($urlHeader);
            }

            if ($templateEmailFooter) {
                $urlFooter = url('stream-file?type=gambar_template&id=' . ($templateEmailFooter->ID ?? '') . '&filename=' . ($templateEmailFooter->CONTENT ?? ''));
                $imgFooter = Main::base64File($urlFooter);
            }

            $branchId = session('branch_id');
            $dateNow = date('Y-m-d');
            $signatureTable = '<br><br><br>';

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
                $ttdUrl = url('stream-file') . '?type=gambar_ttd&id=' . ($leader->ID ?? '') . '&filename=' . ($leader->TTD_FILE_NAME ?? '');
                $imgTtd = Main::base64File($ttdUrl) ?: '';
                $imgTagTtd = (strlen($imgTtd) > 100) ? '<img src="' . $imgTtd . '" height="60" style="height:60px;">' : '<br><br><br>';

                $signatureTable = '
                    <table border="0" cellspacing="0" cellpadding="0" style="text-align:center; width:100%;">
                        <tr><td>' . ($leader->JABATAN ?? 'Pejabat') . '</td></tr>
                        <tr><td style="height:70px;">' . $imgTagTtd . '</td></tr>
                        <tr><td>' . ($leader->NAMA ?? '') . '</td></tr>
                        <tr><td style="font-weight:bold;">NIP. ' . ($leader->NIP ?? '-') . '</td></tr>
                    </table>
                ';
            }

            $qrGenerator = new \Milon\Barcode\DNS2D();
            $qrCodeBody = config('system.fo_url') . '/track-shipment?receipt=' . $letter->RECEIPT_NO;
            $qrBase64Raw = $qrGenerator->getBarcodePNG((string) $qrCodeBody, 'QRCODE', 4, 4);

            $dataParseTemplate = [
                'accepted_date' => Carbon::parse($letter->ACCEPT_DATE)->isoFormat('D MMMM Y'),
                'letter_no' => $letter->LETTER_NUMBER_UT ?: '-',
                'publisher_name' => $letter->NAME_PENERBIT,
                'director' => $signatureTable,
                'header' => !empty($imgHeader) ? '<div style="text-align:center;"><img src="' . $imgHeader . '" width="300" style="width:100%;"></div><br><br>' : '',
                'footer' => !empty($imgFooter) ? '<br><br><br><br><br><br><br><br><div style="text-align:center;"><img src="' . $imgFooter . '" width="550" style="width:100%;"></div>' : '',
                'qr' => '<br><br><img alt="QR" src="data:image/png;base64,' . $qrBase64Raw . '" style="height:120px; width:120px">',
            ];

            $htmlContent = Main::parseTemplateEmail($dataParseTemplate, $templateEmailContent);
            $finalHtml = '
                <style>
                    table {
                        border-collapse: collapse;
                        padding: 0;
                        margin: 0;
                    }

                    td {
                        vertical-align: top;
                    }

                    body {
                        font-family: helvetica;
                        font-size: 10pt;
                    }
                </style>
                <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tr><td>' . $htmlContent . '</td></tr>
                </table>
            ';

            $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(15, 10, 15);
            $pdf->SetAutoPageBreak(true, 15);
            $pdf->AddPage();

            $pdf->writeHTML($finalHtml, true, false, true, false, '');

            $collections = QueryAPI::get("
                select
                    ld.letter_id,
                    l.accept_date as accept_date_letter,
                    ld.title,
                    ld.jenis_media,
                    ld.isbn,
                    case when ld.collection_id LIKE '%,%' and t.lvl > 0 THEN 1 ELSE ld.qty_accept end as qty_accept,
                    c.noinduk as noinduk_collection, c.mark_province as mark_province_collection
                from
                    letter_detail ld
                left join
                    letter l on l.letter_id = ld.letter_id
                cross join
                    (select level as lvl from dual connect by level <= 1000) t
                left join
                    collections c on c.id = to_number(nvl(trim(regexp_substr(ld.collection_id,'[^,]+',1,t.lvl)),'0'))
                where
                    ld.letter_id = $letter->LETTER_ID
                    and ld.qty_accept is not null
                    and ld.qty_accept > 0
                    and T.lvl <= regexp_count(nvl(ld.collection_id, 'X'), ',') + 1
            ");

            $htmlCollections = '
                <table border="1" cellpadding="4" cellspacing="0" style="font-size:8px; border-collapse:collapse;">
                    <tr style="background-color:#f0f0f0; font-weight:bold;">
                        <th width="5%" align="center">No</th>
                        <th width="15%" align="center">Tgl Terima</th>
                        <th width="40%" align="center">Judul</th>
                        <th width="15%" align="center">Jenis</th>
                        <th width="15%" align="center">ISBN</th>
                        <th width="10%" align="center">Jml</th>
                    </tr>
            ';

            if ($collections) {
                foreach ($collections as $key => $c) {
                    $htmlCollections .= '<tr>';
                    $htmlCollections .= '<td align="center">' . ($key + 1) . '</td>';
                    $htmlCollections .= '<td align="center">' . date('d-m-Y', strtotime($c->ACCEPT_DATE_LETTER)) . '</td>';
                    $htmlCollections .= '<td>' . ($c->TITLE ?? '-') . '</td>';
                    $htmlCollections .= '<td align="center">' . ($c->JENIS_MEDIA ?? '-') . '</td>';
                    $htmlCollections .= '<td align="center">' . ($c->ISBN ?? '-') . '</td>';
                    $htmlCollections .= '<td align="center">' . ($c->QTY_ACCEPT ?? '-') . '</td>';
                    $htmlCollections .= '</tr>';
                }
            }

            $htmlCollections .= '</table>';

            $pdf->AddPage();
            $pdf->writeHTML($htmlCollections, true, false, true, false, '');

            $letterNumber = $letter->LETTER_ID ?? date('YmdHis');
            $nameExecutor = $letter->NAME_PENERBIT ?? '';
            $directory = storage_path('app/public/physical-delivery/accept/receipt');

            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            $filename = $directory . '/' . Str::slug('Pengiriman Fisik Koleksi ' . $letterNumber . ' ' . $nameExecutor, '-') . '.pdf';
            $pdf->Output($filename, 'F');

            return $filename;
        } catch (\Exception $e) {
            Log::error('Error generating PDF: ' . $e->getMessage());

            return null;
        }
    }
}
