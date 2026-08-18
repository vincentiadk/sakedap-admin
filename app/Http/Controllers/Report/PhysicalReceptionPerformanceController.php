<?php

namespace App\Http\Controllers\Report;

use App\Helpers\Main;
use App\Helpers\QueryAPI;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Kinerja Penerimaan Karya Fisik
 *
 * Data bersumber dari tabel letter_detail (join letter, branchs, propinsi,
 * collectionmedias). Hanya surat berstatus DITERIMA yang dihitung.
 *
 * Panel yang tersedia:
 *   1. Kartu ringkasan (total surat, judul, copy diterima/ditolak/hibah/retur)
 *   2. Tren per periode (hari / bulan / tahun)
 *   3. Per cabang / kantor penerima
 *   4. Per petugas penerima
 */
class PhysicalReceptionPerformanceController extends Controller
{
    private const STATUS_DITERIMA = "'DITERIMA PENUH','DITERIMA PARSIAL','DITERIMA'";
    private const CACHE_TTL       = 1800; // 30 menit (query berat, 124rb+ baris)

    // Filter petugas penerima (lampiran_detail.received_by = username/NIP).
    private ?string $userFilter = null;

    private function userClause(): string
    {
        if (!$this->userFilter) return '';
        $safe = str_replace("'", "''", $this->userFilter);
        return " AND UPPER(ld.received_by) = UPPER('{$safe}')";
    }

    // ── Index ────────────────────────────────────────────────────────────────

    public function index()
    {
        $medias = Cache::remember('physrecperf:medias', 3600, function () {
            return QueryAPI::get("
                SELECT m.id, m.name
                FROM collectionmedias m
                WHERE NVL(m.isdelete, 0) = 0
                ORDER BY m.name
            ") ?? [];
        });

        $petugas = Cache::remember('physrecperf:petugas', 3600, function () {
            // ORDER BY dengan agregat ditolak lapisan API — diurutkan di PHP.
            $rows = QueryAPI::get("
                SELECT username, MAX(fullname) AS fullname FROM (
                    SELECT ld.received_by AS username, u.fullname
                    FROM letter_detail ld
                    LEFT JOIN users u ON UPPER(u.username) = UPPER(ld.received_by)
                    WHERE ld.received_by IS NOT NULL
                      AND ld.received_date >= ADD_MONTHS(TRUNC(SYSDATE), -18)
                )
                GROUP BY username
            ") ?? [];

            usort($rows, function ($a, $b) {
                $ka = ($a->FULLNAME ?? '') ?: ($a->USERNAME ?? '');
                $kb = ($b->FULLNAME ?? '') ?: ($b->USERNAME ?? '');
                return strcasecmp((string) $ka, (string) $kb);
            });
            return $rows;
        });

        return view('layouts.index', [
            'data' => [
                'content' => 'report.physical-reception-performance',
                'medias'  => $medias,
                'petugas' => $petugas,
                'plugins' => ['daterangepicker', 'select2'],
            ]
        ]);
    }

    // ── Data (AJAX) ──────────────────────────────────────────────────────────

    public function data(Request $request)
    {
        $request->validate([
            'start'       => 'required|date',
            'end'         => 'required|date|after_or_equal:start',
            'media_id'    => 'nullable|integer',
            'granular'    => 'nullable|in:hari,bulan,tahun',
            'province_id' => 'nullable|integer',
            'tujuan'      => 'nullable|in:perpusnas,provinsi',
        ]);

        [$start, $end, $mediaId, $granular, $provinceId, $tujuan] = $this->resolveFilter($request);
        $this->userFilter = $request->user ? trim((string) $request->user) : null;

        $cacheKey = 'physrecperf_v2:' . md5("$start|$end|$mediaId|$granular|$provinceId|$tujuan|{$this->userFilter}");

        try {
            $payload = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($start, $end, $mediaId, $granular, $provinceId, $tujuan) {
                return [
                    'ringkasan'   => $this->fetchRingkasan($start, $end, $mediaId, $provinceId, $tujuan),
                    'tren'        => $this->fetchTren($start, $end, $mediaId, $granular, $provinceId, $tujuan),
                    'per_media'   => $this->fetchPerMedia($start, $end, $provinceId, $tujuan),
                    'per_cabang'  => $this->fetchPerCabang($start, $end, $mediaId, $provinceId, $tujuan),
                    'per_petugas' => $this->fetchPerPetugas($start, $end, $mediaId, $provinceId, $tujuan),
                ];
            });
        } catch (\Throwable $e) {
            Log::channel('daily')->error('PhysicalReceptionPerformance: query gagal', [
                'error'  => $e->getMessage(),
                'filter' => compact('start', 'end', 'mediaId', 'granular', 'provinceId', 'tujuan'),
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        return response()->json(array_merge(['success' => true], $payload, [
            'filter' => compact('start', 'end', 'mediaId', 'granular', 'provinceId', 'tujuan'),
        ]));
    }

    // ── Export Excel ─────────────────────────────────────────────────────────

    public function export(Request $request)
    {
        $request->validate([
            'start'       => 'required|date',
            'end'         => 'required|date|after_or_equal:start',
            'media_id'    => 'nullable|integer',
            'granular'    => 'nullable|in:hari,bulan,tahun',
            'province_id' => 'nullable|integer',
        ]);

        [$start, $end, $mediaId, $granular, $provinceId, $tujuan] = $this->resolveFilter($request);
        $this->userFilter = $request->user ? trim((string) $request->user) : null;

        try {
            $ringkasan  = $this->fetchRingkasan($start, $end, $mediaId, $provinceId, $tujuan);
            $tren       = $this->fetchTren($start, $end, $mediaId, $granular, $provinceId, $tujuan);
            $perMedia   = $this->fetchPerMedia($start, $end, $provinceId, $tujuan);
            $perCabang  = $this->fetchPerCabang($start, $end, $mediaId, $provinceId, $tujuan);
            $perPetugas = $this->fetchPerPetugas($start, $end, $mediaId, $provinceId, $tujuan);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengambil data: ' . $e->getMessage());
        }

        $periodeLabel = date('d/m/Y', strtotime($start)) . ' – ' . date('d/m/Y', strtotime($end));
        $mediaLabel   = 'Semua Jenis Media';
        if ($mediaId) {
            $found = QueryAPI::get("SELECT name FROM collectionmedias WHERE id = $mediaId", true);
            $mediaLabel = $found->NAME ?? "Media ID $mediaId";
        }
        $meta = ['periode' => $periodeLabel, 'media' => $mediaLabel];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        // Sheet 1 – Ringkasan
        $s1  = $spreadsheet->createSheet();
        $s1->setTitle('Ringkasan');
        $row = $this->tulisJudul($s1, 'Kinerja Penerimaan Karya Fisik — Ringkasan', $meta, 2);
        $pairs = [
            ['Total Surat Masuk',    (int) ($ringkasan['total_surat']  ?? 0)],
            ['Total Judul',          (int) ($ringkasan['total_judul']  ?? 0)],
            ['Total Copy Diterima',  (int) ($ringkasan['total_accept'] ?? 0)],
            ['Total Copy Ditolak',   (int) ($ringkasan['total_reject'] ?? 0)],
            ['Total Hibah',          (int) ($ringkasan['total_hibah']  ?? 0)],
            ['Total Retur',          (int) ($ringkasan['total_retur']  ?? 0)],
        ];
        foreach ($pairs as [$label, $val]) {
            $s1->setCellValue("A{$row}", $label);
            $s1->setCellValue("B{$row}", $val);
            $row++;
        }
        $s1->getColumnDimension('A')->setWidth(32);
        $s1->getColumnDimension('B')->setWidth(18);

        // Sheet 2 – Tren
        $this->sheetTabel($spreadsheet, 'Tren per ' . ucfirst($granular), 'Tren Penerimaan per ' . ucfirst($granular), $meta,
            ['Periode', 'Surat', 'Judul', 'Diterima', 'Ditolak', 'Hibah', 'Retur'],
            array_map(fn($r) => [
                $r['periode']      ?? '',
                (int) ($r['total_surat']  ?? 0),
                (int) ($r['total_judul']  ?? 0),
                (int) ($r['total_accept'] ?? 0),
                (int) ($r['total_reject'] ?? 0),
                (int) ($r['total_hibah']  ?? 0),
                (int) ($r['total_retur']  ?? 0),
            ], $tren),
            [16, 10, 10, 12, 12, 10, 10]
        );

        // Sheet 3 – Per Media
        $this->sheetTabel($spreadsheet, 'Per Jenis Media', 'Penerimaan per Jenis Media', $meta,
            ['Jenis Media', 'Judul', 'Diterima', 'Ditolak', 'Hibah', 'Retur'],
            array_map(fn($r) => [
                $r['media']        ?? '-',
                (int) ($r['total_judul']  ?? 0),
                (int) ($r['total_accept'] ?? 0),
                (int) ($r['total_reject'] ?? 0),
                (int) ($r['total_hibah']  ?? 0),
                (int) ($r['total_retur']  ?? 0),
            ], $perMedia),
            [30, 10, 12, 12, 10, 10]
        );

        // Sheet 4 – Per Cabang
        $this->sheetTabel($spreadsheet, 'Per Cabang', 'Penerimaan per Cabang', $meta,
            ['Cabang', 'Provinsi', 'Tujuan', 'Surat', 'Judul', 'Diterima', 'Ditolak', 'Hibah', 'Retur'],
            array_map(fn($r) => [
                $r['cabang']       ?? '-',
                $r['provinsi']     ?? '-',
                $r['tujuan']       ?? '-',
                (int) ($r['total_surat']  ?? 0),
                (int) ($r['total_judul']  ?? 0),
                (int) ($r['total_accept'] ?? 0),
                (int) ($r['total_reject'] ?? 0),
                (int) ($r['total_hibah']  ?? 0),
                (int) ($r['total_retur']  ?? 0),
            ], $perCabang),
            [30, 22, 12, 10, 10, 12, 12, 10, 10]
        );

        // Sheet 4 – Per Petugas
        $this->sheetTabel($spreadsheet, 'Per Petugas', 'Penerimaan per Petugas', $meta,
            ['Petugas', 'Username', 'Judul', 'Diterima', 'Ditolak', 'Hibah', 'Retur'],
            array_map(fn($r) => [
                $r['petugas']      ?? '-',
                $r['username']     ?? '-',
                (int) ($r['total_judul']  ?? 0),
                (int) ($r['total_accept'] ?? 0),
                (int) ($r['total_reject'] ?? 0),
                (int) ($r['total_hibah']  ?? 0),
                (int) ($r['total_retur']  ?? 0),
            ], $perPetugas),
            [30, 20, 10, 12, 12, 10, 10]
        );

        $spreadsheet->setActiveSheetIndex(0);
        $filename = 'Kinerja_Penerimaan_Fisik_' . date('Ymd_His') . '.xlsx';

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return response($content, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-store, no-cache',
        ]);
    }

    // ── Query helpers ────────────────────────────────────────────────────────

    private const PERPUSNAS_BRANCH_ID = 37;

    private function resolveFilter(Request $request): array
    {
        $start      = date('Y-m-d', strtotime($request->start));
        $end        = date('Y-m-d', strtotime($request->end));
        $mediaId    = $request->media_id ? (int) $request->media_id : null;
        $granular   = in_array($request->granular, ['hari', 'bulan', 'tahun']) ? $request->granular : 'bulan';
        $provinceId = $request->province_id ? (int) $request->province_id : null;
        $tujuan     = in_array($request->tujuan, ['perpusnas', 'provinsi']) ? $request->tujuan : null;

        // Non-perpusnas hanya bisa lihat provinsi sendiri
        if (!Main::isPerpusnas()) {
            $provinceId = (int) session('province_id');
        }

        return [$start, $end, $mediaId, $granular, $provinceId, $tujuan];
    }

    private function baseWhere(string $start, string $end, ?int $mediaId, ?int $provinceId, ?string $tujuan): string
    {
        // Pisah NVL jadi dua cabang OR supaya Oracle bisa pakai index
        $ds = "TO_DATE('{$start}','YYYY-MM-DD')";
        $de = "TO_DATE('{$end}','YYYY-MM-DD') + 1";

        $where  = "l.status IN (" . self::STATUS_DITERIMA . ")";
        $where .= " AND (";
        $where .= "   (ld.received_date IS NOT NULL AND ld.received_date >= {$ds} AND ld.received_date < {$de})";
        $where .= "   OR";
        $where .= "   (ld.received_date IS NULL     AND l.accept_date    >= {$ds} AND l.accept_date    < {$de})";
        $where .= " )";

        if ($tujuan === 'perpusnas') {
            $where .= " AND l.branch_id = " . self::PERPUSNAS_BRANCH_ID;
        } elseif ($tujuan === 'provinsi') {
            $where .= " AND l.branch_id != " . self::PERPUSNAS_BRANCH_ID;
        }

        if ($mediaId) {
            $where .= " AND ld.collection_type_id = {$mediaId}";
        }
        if ($provinceId) {
            $where .= " AND b.province_id = {$provinceId}";
        }
        $where .= $this->userClause();

        return $where;
    }

    private function fetchRingkasan(string $start, string $end, ?int $mediaId, ?int $provinceId, ?string $tujuan): array
    {
        $where  = $this->baseWhere($start, $end, $mediaId, $provinceId, $tujuan);
        $result = QueryAPI::get("
            SELECT
                COUNT(DISTINCT l.letter_id)        AS total_surat,
                COUNT(ld.letter_detail_id)         AS total_judul,
                SUM(NVL(ld.qty_accept, 0))         AS total_accept,
                SUM(NVL(ld.qty_reject, 0))         AS total_reject,
                SUM(NVL(ld.qty_hibah,  0))         AS total_hibah,
                SUM(NVL(ld.qty_retur,  0))         AS total_retur
            FROM letter_detail ld
            LEFT JOIN letter l ON l.letter_id = ld.letter_id
            LEFT JOIN branchs b ON b.id = l.branch_id
            WHERE {$where}
        ", true);

        if (!$result) return [];

        return [
            'total_surat'  => (int) $result->TOTAL_SURAT,
            'total_judul'  => (int) $result->TOTAL_JUDUL,
            'total_accept' => (int) $result->TOTAL_ACCEPT,
            'total_reject' => (int) $result->TOTAL_REJECT,
            'total_hibah'  => (int) $result->TOTAL_HIBAH,
            'total_retur'  => (int) $result->TOTAL_RETUR,
        ];
    }

    private function fetchTren(string $start, string $end, ?int $mediaId, string $granular, ?int $provinceId, ?string $tujuan): array
    {
        $where  = $this->baseWhere($start, $end, $mediaId, $provinceId, $tujuan);
        $format = match ($granular) {
            'hari'  => 'YYYY-MM-DD',
            'tahun' => 'YYYY',
            default => 'YYYY-MM',
        };

        // Gunakan CASE agar NVL tidak dieksekusi sebagai fungsi di GROUP BY
        $tglExpr = "CASE WHEN ld.received_date IS NOT NULL THEN ld.received_date ELSE l.accept_date END";

        $rows = QueryAPI::get("
            SELECT
                TO_CHAR({$tglExpr}, '{$format}')     AS periode,
                COUNT(DISTINCT l.letter_id)          AS total_surat,
                COUNT(ld.letter_detail_id)           AS total_judul,
                SUM(NVL(ld.qty_accept, 0))           AS total_accept,
                SUM(NVL(ld.qty_reject, 0))           AS total_reject,
                SUM(NVL(ld.qty_hibah,  0))           AS total_hibah,
                SUM(NVL(ld.qty_retur,  0))           AS total_retur
            FROM letter_detail ld
            LEFT JOIN letter l ON l.letter_id = ld.letter_id
            LEFT JOIN branchs b ON b.id = l.branch_id
            WHERE {$where}
            GROUP BY TO_CHAR({$tglExpr}, '{$format}')
            ORDER BY 1
        ");

        if (!$rows) return [];

        return collect($rows)->map(fn($r) => [
            'periode'      => $r->PERIODE,
            'total_surat'  => (int) $r->TOTAL_SURAT,
            'total_judul'  => (int) $r->TOTAL_JUDUL,
            'total_accept' => (int) $r->TOTAL_ACCEPT,
            'total_reject' => (int) $r->TOTAL_REJECT,
            'total_hibah'  => (int) $r->TOTAL_HIBAH,
            'total_retur'  => (int) $r->TOTAL_RETUR,
        ])->toArray();
    }

    private function fetchPerMedia(string $start, string $end, ?int $provinceId, ?string $tujuan): array
    {
        // Per media selalu semua media (tidak difilter mediaId) agar overview lengkap
        $where = $this->baseWhere($start, $end, null, $provinceId, $tujuan);

        $rows = QueryAPI::get("
            SELECT
                NVL(m.name, '(Tidak Diketahui)')   AS media,
                COUNT(ld.letter_detail_id)          AS total_judul,
                SUM(NVL(ld.qty_accept, 0))          AS total_accept,
                SUM(NVL(ld.qty_reject, 0))          AS total_reject,
                SUM(NVL(ld.qty_hibah,  0))          AS total_hibah,
                SUM(NVL(ld.qty_retur,  0))          AS total_retur
            FROM letter_detail ld
            LEFT JOIN letter l ON l.letter_id = ld.letter_id
            LEFT JOIN branchs b ON b.id = l.branch_id
            LEFT JOIN collectionmedias m ON m.id = ld.collection_type_id
            WHERE {$where}
            GROUP BY m.name
            ORDER BY COUNT(ld.letter_detail_id) DESC
        ");

        if (!$rows) return [];

        return collect($rows)->map(fn($r) => [
            'media'        => $r->MEDIA,
            'total_judul'  => (int) $r->TOTAL_JUDUL,
            'total_accept' => (int) $r->TOTAL_ACCEPT,
            'total_reject' => (int) $r->TOTAL_REJECT,
            'total_hibah'  => (int) $r->TOTAL_HIBAH,
            'total_retur'  => (int) $r->TOTAL_RETUR,
        ])->toArray();
    }

    private function fetchPerCabang(string $start, string $end, ?int $mediaId, ?int $provinceId, ?string $tujuan): array
    {
        $where = $this->baseWhere($start, $end, $mediaId, $provinceId, $tujuan);

        $rows = QueryAPI::get("
            SELECT
                NVL(b.name, '(Tidak Diketahui)')   AS cabang,
                CASE WHEN l.branch_id = " . self::PERPUSNAS_BRANCH_ID . " THEN 'Perpusnas'
                     ELSE NVL(prov.namapropinsi, '-') END                   AS provinsi,
                CASE WHEN l.branch_id = " . self::PERPUSNAS_BRANCH_ID . " THEN 'Perpustakaan Nasional RI'
                     ELSE 'Provinsi' END                                    AS tujuan,
                COUNT(DISTINCT l.letter_id)         AS total_surat,
                COUNT(ld.letter_detail_id)          AS total_judul,
                SUM(NVL(ld.qty_accept, 0))          AS total_accept,
                SUM(NVL(ld.qty_reject, 0))          AS total_reject,
                SUM(NVL(ld.qty_hibah,  0))          AS total_hibah,
                SUM(NVL(ld.qty_retur,  0))          AS total_retur
            FROM letter_detail ld
            LEFT JOIN letter l ON l.letter_id = ld.letter_id
            LEFT JOIN branchs b ON b.id = l.branch_id
            LEFT JOIN propinsi prov ON prov.id = b.province_id
            WHERE {$where}
            GROUP BY b.name, prov.namapropinsi, l.branch_id
            ORDER BY COUNT(ld.letter_detail_id) DESC
        ");

        if (!$rows) return [];

        return collect($rows)->map(fn($r) => [
            'cabang'       => $r->CABANG,
            'provinsi'     => $r->PROVINSI,
            'tujuan'       => $r->TUJUAN,
            'total_surat'  => (int) $r->TOTAL_SURAT,
            'total_judul'  => (int) $r->TOTAL_JUDUL,
            'total_accept' => (int) $r->TOTAL_ACCEPT,
            'total_reject' => (int) $r->TOTAL_REJECT,
            'total_hibah'  => (int) $r->TOTAL_HIBAH,
            'total_retur'  => (int) $r->TOTAL_RETUR,
        ])->toArray();
    }

    private function fetchPerPetugas(string $start, string $end, ?int $mediaId, ?int $provinceId, ?string $tujuan): array
    {
        $where = $this->baseWhere($start, $end, $mediaId, $provinceId, $tujuan);

        $rows = QueryAPI::get("
            SELECT
                NVL(u.fullname, ld.received_by)    AS petugas,
                NVL(ld.received_by, '-')            AS username,
                COUNT(ld.letter_detail_id)          AS total_judul,
                SUM(NVL(ld.qty_accept, 0))          AS total_accept,
                SUM(NVL(ld.qty_reject, 0))          AS total_reject,
                SUM(NVL(ld.qty_hibah,  0))          AS total_hibah,
                SUM(NVL(ld.qty_retur,  0))          AS total_retur
            FROM letter_detail ld
            LEFT JOIN letter l ON l.letter_id = ld.letter_id
            LEFT JOIN branchs b ON b.id = l.branch_id
            LEFT JOIN users u ON u.username = ld.received_by
            WHERE {$where}
              AND ld.received_by IS NOT NULL
            GROUP BY u.fullname, ld.received_by
            ORDER BY COUNT(ld.letter_detail_id) DESC
        ");

        if (!$rows) return [];

        return collect($rows)->map(fn($r) => [
            'petugas'      => $r->PETUGAS,
            'username'     => $r->USERNAME,
            'total_judul'  => (int) $r->TOTAL_JUDUL,
            'total_accept' => (int) $r->TOTAL_ACCEPT,
            'total_reject' => (int) $r->TOTAL_REJECT,
            'total_hibah'  => (int) $r->TOTAL_HIBAH,
            'total_retur'  => (int) $r->TOTAL_RETUR,
        ])->toArray();
    }

    // ── Excel helpers ────────────────────────────────────────────────────────

    private function sheetTabel(
        \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet,
        string $tabTitle,
        string $mainTitle,
        array  $meta,
        array  $headers,
        array  $rows,
        array  $widths
    ): void {
        $sheet     = $spreadsheet->createSheet();
        $sheet->setTitle($tabTitle);
        $headerRow = $this->tulisJudul($sheet, $mainTitle, $meta, count($headers));

        $col = 1;
        foreach ($headers as $h) {
            $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue("{$letter}{$headerRow}", $h);
            $sheet->getColumnDimension($letter)->setWidth($widths[$col - 1] ?? 14);
            $col++;
        }
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle("A{$headerRow}:{$lastCol}{$headerRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1565C0']],
        ]);

        $dataRow = $headerRow + 1;
        foreach ($rows as $r) {
            $col = 1;
            foreach ($r as $val) {
                $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $sheet->setCellValue("{$letter}{$dataRow}", $val);
                $col++;
            }
            $dataRow++;
        }

        if (empty($rows)) {
            $sheet->setCellValue("A{$dataRow}", 'Tidak ada data pada rentang ini.');
        }
    }

    private function tulisJudul(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        string $mainTitle,
        array  $meta,
        int    $colCount
    ): int {
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);

        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', $mainTitle);
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '0D47A1']],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(24);

        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->setCellValue('A2', 'Periode: ' . ($meta['periode'] ?? '-') . '     |     Jenis Media: ' . ($meta['media'] ?? '-'));
        $sheet->getStyle('A2')->applyFromArray(['font' => ['size' => 10, 'color' => ['rgb' => '444444']]]);

        $sheet->mergeCells("A3:{$lastCol}3");
        $sheet->setCellValue('A3', 'Diunduh: ' . date('d/m/Y H:i'));
        $sheet->getStyle('A3')->applyFromArray(['font' => ['size' => 10, 'color' => ['rgb' => '444444']]]);

        $sheet->getStyle("A1:{$lastCol}3")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E3F2FD');

        $sheet->getRowDimension(4)->setRowHeight(6);

        return 5;
    }
}
