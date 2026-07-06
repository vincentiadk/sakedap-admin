<?php

namespace App\Http\Controllers\Report;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

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
                                p.telp1 as penerbit_telp1,
                                p.telp2 as penerbit_telp2,
                                p.email1 as penerbit_email1,
                                p.email2 as penerbit_email2,
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
                    $val->PENERBIT_TELP1  ?? '',
                    $val->PENERBIT_TELP2  ?? '',
                    $val->PENERBIT_EMAIL1 ?? '',
                    $val->PENERBIT_EMAIL2 ?? '',
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

    public function export(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $whereCondition = ["l.status != 'DRAFT'"];

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
            $startDate   = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate     = Carbon::parse($explodeDate[1])->format('Y-m-d');
            $whereCondition[] = "(l.$request->date_type >= to_date('$startDate','YYYY-MM-DD') and l.$request->date_type < to_date('$endDate','YYYY-MM-DD') + 1)";
        }

        $whereClause = 'WHERE ' . implode(' AND ', $whereCondition);

        $rows = QueryAPI::get("
            SELECT
                l.letter_date, l.letter_number, l.accept_date, l.create_date,
                l.sender, l.phone, l.letter_number_ut, l.receipt_no,
                l.status, l.kode_promo, l.biaya_kirim, l.berat, l.jumlah_paket,
                p.name  as name_penerbit,  p.id as penerbit_id,
                p.telp1 as penerbit_telp1, p.telp2 as penerbit_telp2,
                p.email1 as penerbit_email1, p.email2 as penerbit_email2,
                jp.name as name_jasa_pengiriman,
                b.name  as name_branch,
                nvl(td.total_eks_receipt,   0) as total_eks_receipt,
                nvl(td.total_title_receipt, 0) as total_title_receipt,
                CASE WHEN l.status IN ('TERKIRIM','CEK FISIK','DITERIMA PENUH','DITERIMA PARSIAL','RETUR')
                     THEN nvl(td.total_eks_delivery,   0) ELSE 0 END as total_eks_delivery,
                CASE WHEN l.status IN ('TERKIRIM','CEK FISIK','DITERIMA PENUH','DITERIMA PARSIAL','RETUR')
                     THEN nvl(td.total_title_delivery, 0) ELSE 0 END as total_title_delivery,
                CASE WHEN l.status IN ('TERKIRIM','CEK FISIK','DITERIMA PENUH','DITERIMA PARSIAL','RETUR')
                     THEN nvl(td.total_eks_grant,   0) ELSE 0 END as total_eks_grant,
                CASE WHEN l.status IN ('TERKIRIM','CEK FISIK','DITERIMA PENUH','DITERIMA PARSIAL','RETUR')
                     THEN nvl(td.total_title_grant, 0) ELSE 0 END as total_title_grant
            FROM letter l
            LEFT JOIN penerbit p          ON p.id  = l.penerbit_id
            LEFT JOIN jasa_pengiriman jp  ON jp.id = l.jasa_pengiriman_id
            LEFT JOIN branchs b           ON b.id  = l.branch_id
            LEFT JOIN (
                SELECT letter_id,
                    SUM(copy)                                         AS total_eks_delivery,
                    SUM(quantity)                                     AS total_title_delivery,
                    SUM(CASE WHEN qty_accept > 0 THEN qty_accept  ELSE 0 END) AS total_eks_receipt,
                    SUM(CASE WHEN qty_accept > 0 THEN quantity    ELSE 0 END) AS total_title_receipt,
                    SUM(CASE WHEN qty_hibah  > 0 THEN qty_hibah   ELSE 0 END) AS total_eks_grant,
                    SUM(CASE WHEN qty_hibah  > 0 THEN quantity    ELSE 0 END) AS total_title_grant
                FROM letter_detail
                GROUP BY letter_id
            ) td ON td.letter_id = l.letter_id
            $whereClause
            ORDER BY l.letter_date DESC
        ") ?? [];

        // ── Build XLSX ──────────────────────────────────────────────────────
        $sp    = new Spreadsheet();
        $sheet = $sp->getActiveSheet();
        $sheet->setTitle('Laporan Pengiriman');

        $headers = [
            'No', 'Tgl Pengiriman', 'Nomor Pengiriman', 'Tgl Terima', 'Tgl Buat',
            'Pengirim', 'No HP', 'Pelaksana Serah (ID)', 'Pelaksana Serah',
            'Nomor UT', 'Resi', 'Jasa Kirim', 'Tujuan',
            'Pengiriman Judul', 'Pengiriman Eks',
            'Diterima Judul', 'Diterima Eks',
            'Ditolak Judul', 'Ditolak Eks',
            'Status', 'Kode Promo', 'Biaya Kirim', 'Berat', 'Jumlah Paket',
            'Telp 1 Penerbit', 'Telp 2 Penerbit', 'Email 1 Penerbit', 'Email 2 Penerbit',
        ];

        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1565C0']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'AAAAAA']]],
        ];
        $cellStyle = [
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ];

        // Title row
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->getCell('A1')->setValue('LAPORAN PENGIRIMAN SERAH SIMPAN');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '0D47A1']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(24);

        $sheet->mergeCells('A2:' . $lastCol . '2');
        $sheet->getCell('A2')->setValue('Diunduh: ' . date('d/m/Y H:i'));
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '444444']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(4);

        // Header row
        foreach ($headers as $i => $h) {
            $col = $i + 1;
            $sheet->getCell(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . '4')
                  ->setValue($h);
            $sheet->getStyle(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . '4')
                  ->applyFromArray($headerStyle);
        }
        $sheet->getRowDimension(4)->setRowHeight(22);

        $rowNum = 5;
        $no     = 1;
        foreach ($rows as $val) {
            $isEven = ($rowNum % 2 === 0);
            $bg     = $isEven ? 'F9F9F9' : 'FFFFFF';

            $rowData = [
                $no++,
                $val->LETTER_DATE   ? Carbon::parse($val->LETTER_DATE)->format('d/m/Y')   : '',
                $val->LETTER_NUMBER ?? '',
                $val->ACCEPT_DATE   ? Carbon::parse($val->ACCEPT_DATE)->format('d/m/Y H:i') : '',
                $val->CREATE_DATE   ? Carbon::parse($val->CREATE_DATE)->format('d/m/Y H:i') : '',
                $val->SENDER        ?? '',
                $val->PHONE         ?? '',
                $val->PENERBIT_ID   ?? '',
                $val->NAME_PENERBIT ?? '',
                $val->LETTER_NUMBER_UT       ?? '',
                $val->RECEIPT_NO             ?? '',
                $val->NAME_JASA_PENGIRIMAN   ?? '',
                $val->NAME_BRANCH            ?? '',
                (int) ($val->TOTAL_TITLE_DELIVERY ?? 0),
                (int) ($val->TOTAL_EKS_DELIVERY   ?? 0),
                (int) ($val->TOTAL_TITLE_RECEIPT  ?? 0),
                (int) ($val->TOTAL_EKS_RECEIPT    ?? 0),
                (int) ($val->TOTAL_TITLE_GRANT    ?? 0),
                (int) ($val->TOTAL_EKS_GRANT      ?? 0),
                $val->STATUS     ?? '',
                $val->KODE_PROMO ?? '',
                (int) ($val->BIAYA_KIRIM   ?? 0),
                (float) ($val->BERAT       ?? 0) / 1000,
                (int) ($val->JUMLAH_PAKET  ?? 0),
                $val->PENERBIT_TELP1  ?? '',
                $val->PENERBIT_TELP2  ?? '',
                $val->PENERBIT_EMAIL1 ?? '',
                $val->PENERBIT_EMAIL2 ?? '',
            ];

            foreach ($rowData as $i => $val_) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
                $sheet->getCell($colLetter . $rowNum)->setValue($val_ ?? '');
                $style = $cellStyle;
                $style['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]];
                $sheet->getStyle($colLetter . $rowNum)->applyFromArray($style);
            }
            $rowNum++;
        }

        foreach (range(1, count($headers)) as $c) {
            $sheet->getColumnDimension(
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c)
            )->setAutoSize(true);
        }
        $sheet->freezePane('A5');

        $filename = 'LaporanPengiriman_' . date('d-m-Y_His') . '.xlsx';
        $temp     = tempnam(sys_get_temp_dir(), 'xlsx_');
        (new Xlsx($sp))->save($temp);
        $content = file_get_contents($temp);
        unlink($temp);

        return response($content, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-store, no-cache',
        ]);
    }
}
