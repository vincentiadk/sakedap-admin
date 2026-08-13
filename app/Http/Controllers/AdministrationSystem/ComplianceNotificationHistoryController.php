<?php

namespace App\Http\Controllers\AdministrationSystem;

use App\Helpers\ComplianceNotification;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use App\Http\Controllers\Controller;
use App\Traits\OracleHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Riwayat email notifikasi kepatuhan per penerbit.
 *
 * SENGAJA dicari per-penerbit (bukan list global lintas-penerbit): historydata
 * adalah tabel bersama seluruh modul (puluhan juta baris). Query di sini selalu
 * pakai (tablename, idref) sebagai filter, persis kombinasi yang sudah
 * diindeks (IDX_HISTORYDATA_TBL_REF / HISTORYDATA_TABLENAME), jadi tetap
 * cepat berapa pun besar tabelnya. Jangan tambah pencarian teks bebas lintas
 * penerbit di sini — itu bakal full-scan.
 */
class ComplianceNotificationHistoryController extends Controller
{
    use OracleHelper;

    public function __construct()
    {
        if (!Main::isSuperAdmin() && !Main::isPerpusnas()) {
            abort(403);
        }
    }

    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'content' => 'administration-system.compliance-notification-history',
            ]
        ]);
    }

    /**
     * Riwayat email kepatuhan (action=EMAIL) untuk satu penerbit, terbaru dulu.
     * Dibatasi 200 baris — cukup untuk histori satu penerbit, sekaligus jaga
     * respons tetap ringan.
     */
    public function history(Request $request)
    {
        $request->validate([
            'penerbit' => 'required|integer|min:1',
        ]);

        $penerbitId = (int) $request->penerbit;

        $rows = QueryAPI::get("
            SELECT *
            FROM (
                SELECT ACTIONDATE, ACTIONBY, ACTIONTERMINAL, NOTE
                FROM historydata
                WHERE TABLENAME = 'PENERBIT'
                  AND IDREF = {$penerbitId}
                  AND ACTION = 'EMAIL'
                ORDER BY ACTIONDATE DESC
            )
            WHERE ROWNUM <= 200
        ") ?? [];

        $data = array_map(fn($r) => [
            'waktu'    => $r->ACTIONDATE ?? null,
            'oleh'     => $r->ACTIONBY ?? null,
            'terminal' => $r->ACTIONTERMINAL ?? null,
            'catatan'  => $r->NOTE ?? null,
        ], $rows);

        return response()->json([
            'success' => true,
            'total'   => count($data),
            'data'    => $data,
        ]);
    }

    /**
     * Ringkasan jumlah penerbit yang terkirim email kepatuhan per jenis, dalam
     * rentang tanggal tertentu. Ini AGREGAT (COUNT/GROUP BY dibatasi rentang
     * ACTIONDATE, kena index IDX_HISTORYDATA_ACTIONDATE) — bukan listing baris
     * per penerbit, jadi aman dipakai lintas-penerbit tanpa terikat idref.
     * JANGAN ubah ini jadi pencarian teks bebas; itu akan full-scan.
     */
    public function summary(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date',
        ]);

        $start = $request->start_date ? Carbon::parse($request->start_date) : Carbon::today();
        $end   = $request->end_date ? Carbon::parse($request->end_date) : Carbon::today();
        if ($end->lt($start)) {
            [$start, $end] = [$end, $start];
        }
        $startSql = $start->format('Y-m-d');
        $endSql   = $end->copy()->addDay()->format('Y-m-d');

        $jenisCase = $this->jenisCaseSql();

        $sql = "
            SELECT
                {$jenisCase} AS JENIS_KEY,
                COUNT(DISTINCT IDREF) AS JUMLAH_PENERBIT,
                COUNT(*) AS JUMLAH_EMAIL,
                MIN(ACTIONDATE) AS PALING_AWAL,
                MAX(ACTIONDATE) AS PALING_AKHIR
            FROM historydata
            WHERE TABLENAME = 'PENERBIT'
              AND ACTION = 'EMAIL'
              AND ACTIONDATE >= TO_DATE('{$startSql}', 'YYYY-MM-DD')
              AND ACTIONDATE <  TO_DATE('{$endSql}', 'YYYY-MM-DD')
            GROUP BY {$jenisCase}
            ORDER BY JUMLAH_EMAIL DESC
        ";

        $rows = QueryAPI::get($sql) ?? [];

        $data = array_map(fn($r) => [
            'jenis_key'       => $r->JENIS_KEY,
            'jenis'           => ComplianceNotification::JENIS[$r->JENIS_KEY]['label'] ?? 'Lainnya',
            'jumlah_penerbit' => (int) $r->JUMLAH_PENERBIT,
            'jumlah_email'    => (int) $r->JUMLAH_EMAIL,
            'paling_awal'     => $r->PALING_AWAL ?? null,
            'paling_akhir'    => $r->PALING_AKHIR ?? null,
        ], $rows);

        // Dihitung terpisah (bukan sum per-jenis) karena satu penerbit bisa kena
        // lebih dari satu jenis email dalam rentang yang sama — sum per-jenis
        // akan menghitung penerbit itu dobel.
        $distinct = QueryAPI::get("
            SELECT COUNT(DISTINCT IDREF) AS TOTAL_PENERBIT, COUNT(*) AS TOTAL_EMAIL
            FROM historydata
            WHERE TABLENAME = 'PENERBIT'
              AND ACTION = 'EMAIL'
              AND ACTIONDATE >= TO_DATE('{$startSql}', 'YYYY-MM-DD')
              AND ACTIONDATE <  TO_DATE('{$endSql}', 'YYYY-MM-DD')
        ", true);

        return response()->json([
            'success'        => true,
            'start_date'     => $startSql,
            'end_date'       => $end->format('Y-m-d'),
            'total_penerbit' => (int) ($distinct->TOTAL_PENERBIT ?? 0),
            'total_email'    => (int) ($distinct->TOTAL_EMAIL ?? 0),
            'data'           => $data,
        ]);
    }

    /**
     * Daftar penerbit yang terkirim satu jenis email tertentu, dalam rentang
     * tanggal tertentu. Dipaginasi 50/halaman (ROWNUM, pola sama dengan
     * DataTableServersideController) — tetap dibatasi rentang ACTIONDATE
     * (index) supaya tidak full-scan biarpun tidak terikat idref.
     */
    public function detail(Request $request)
    {
        $request->validate([
            'jenis'      => 'required|string',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date',
            'offset'     => 'nullable|integer|min:0',
        ]);

        if (!ComplianceNotification::jenisValid($request->jenis)) {
            return response()->json(['success' => false, 'message' => 'Jenis notifikasi tidak dikenal.'], 422);
        }

        $key   = $request->jenis;
        $label = ComplianceNotification::JENIS[$key]['label'];

        [$startSql, $endSql] = $this->resolveDateRange($request);
        $noteFilter = $this->noteFilterSql($key);

        $offset = max(0, (int) $request->offset);
        $limit  = 50;
        $fetchTo = $offset + $limit + 1; // +1 buat deteksi "masih ada lagi"

        $sql = "
            SELECT * FROM (
                SELECT rownum AS rnum, h.*
                FROM (
                    SELECT hd.ACTIONDATE, hd.IDREF, hd.NOTE, p.NAME AS PENERBIT_NAME
                    FROM historydata hd
                    LEFT JOIN PENERBIT p ON p.ID = hd.IDREF
                    WHERE hd.TABLENAME = 'PENERBIT'
                      AND hd.ACTION = 'EMAIL'
                      AND hd.ACTIONDATE >= TO_DATE('{$startSql}', 'YYYY-MM-DD')
                      AND hd.ACTIONDATE <  TO_DATE('{$endSql}', 'YYYY-MM-DD')
                      AND {$noteFilter}
                    ORDER BY hd.ACTIONDATE DESC
                ) h
                WHERE ROWNUM <= {$fetchTo}
            )
            WHERE rnum > {$offset}
        ";

        $rows = QueryAPI::get($sql) ?? [];

        $hasMore = count($rows) > $limit;
        if ($hasMore) {
            array_pop($rows);
        }

        $data = array_map(fn($r) => [
            'penerbit_id'   => (int) $r->IDREF,
            'penerbit_nama' => $r->PENERBIT_NAME ?? '(penerbit tidak ditemukan)',
            'waktu'         => $r->ACTIONDATE ?? null,
        ], $rows);

        return response()->json([
            'success'  => true,
            'jenis'    => $label,
            'offset'   => $offset,
            'has_more' => $hasMore,
            'data'     => $data,
        ]);
    }

    /**
     * Unduh (Excel) SELURUH penerbit yang terkirim satu jenis email tertentu,
     * dalam rentang tanggal tertentu — bukan cuma 50 yang tampil di halaman.
     * Dibatasi 20.000 baris sebagai jaring pengaman; kalau kepentok, itu tandanya
     * rentang tanggalnya perlu dipersempit dulu.
     */
    public function exportDetail(Request $request)
    {
        $request->validate([
            'jenis'      => 'required|string',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date',
        ]);

        if (!ComplianceNotification::jenisValid($request->jenis)) {
            return back()->with('error', 'Jenis notifikasi tidak dikenal.');
        }

        $key   = $request->jenis;
        $label = ComplianceNotification::JENIS[$key]['label'];

        [$startSql, $endSql] = $this->resolveDateRange($request);
        $noteFilter = $this->noteFilterSql($key);

        $sql = "
            SELECT * FROM (
                SELECT hd.ACTIONDATE, hd.IDREF, hd.NOTE, p.NAME AS PENERBIT_NAME,
                       p.EMAIL1, p.EMAIL2
                FROM historydata hd
                LEFT JOIN PENERBIT p ON p.ID = hd.IDREF
                WHERE hd.TABLENAME = 'PENERBIT'
                  AND hd.ACTION = 'EMAIL'
                  AND hd.ACTIONDATE >= TO_DATE('{$startSql}', 'YYYY-MM-DD')
                  AND hd.ACTIONDATE <  TO_DATE('{$endSql}', 'YYYY-MM-DD')
                  AND {$noteFilter}
                ORDER BY hd.ACTIONDATE DESC
            )
            WHERE ROWNUM <= 20000
        ";

        $rows = QueryAPI::get($sql) ?? [];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daftar Penerbit');

        $sheet->setCellValue('A1', 'Daftar Penerbit — ' . $label);
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 13]]);

        $sheet->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($startSql)) . ' – ' . date('d/m/Y', strtotime($endSql) - 86400));
        $sheet->mergeCells('A2:E2');
        $sheet->setCellValue('A3', 'Diunduh: ' . date('d/m/Y H:i'));
        $sheet->mergeCells('A3:E3');

        $headers = ['ID Penerbit', 'Nama Penerbit', 'Email 1', 'Email 2', 'Waktu Kirim'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue("{$col}5", $h);
            $col++;
        }
        $sheet->getStyle('A5:E5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1565C0']],
        ]);

        $row = 6;
        foreach ($rows as $r) {
            $sheet->setCellValue("A{$row}", (int) $r->IDREF);
            $sheet->setCellValue("B{$row}", $r->PENERBIT_NAME ?? '(penerbit tidak ditemukan)');
            $sheet->setCellValue("C{$row}", $r->EMAIL1 ?? '');
            $sheet->setCellValue("D{$row}", $r->EMAIL2 ?? '');
            $sheet->setCellValue("E{$row}", $r->ACTIONDATE ?? '');
            $row++;
        }
        if (empty($rows)) {
            $sheet->setCellValue('A6', 'Tidak ada data pada rentang ini.');
        }

        foreach (['A' => 14, 'B' => 40, 'C' => 26, 'D' => 26, 'E' => 20] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $filename = 'Notifikasi_' . str_replace(' ', '_', $label) . '_' . date('Ymd_His') . '.xlsx';

        return $this->streamXlsx($spreadsheet, $filename, $request);
    }

    /**
     * Rentang tanggal dari request, dinormalisasi jadi [start, end) buat WHERE
     * ACTIONDATE — dipakai bersama oleh detail() dan exportDetail().
     */
    private function resolveDateRange(Request $request): array
    {
        $start = $request->start_date ? Carbon::parse($request->start_date) : Carbon::today();
        $end   = $request->end_date ? Carbon::parse($request->end_date) : Carbon::today();
        if ($end->lt($start)) {
            [$start, $end] = [$end, $start];
        }

        return [$start->format('Y-m-d'), $end->copy()->addDay()->format('Y-m-d')];
    }

    /**
     * Kondisi WHERE buat menyaring historydata.NOTE ke satu jenis tertentu —
     * dipakai bersama oleh detail() dan exportDetail(). Lihat jenisCaseSql()
     * untuk penjelasan dua lapis prefix/fallback-nya.
     */
    private function noteFilterSql(string $key): string
    {
        $labelSafe = str_replace("'", "''", ComplianceNotification::JENIS[$key]['label']);
        $keySafe   = str_replace("'", "''", $key);

        return "(NOTE LIKE '[{$keySafe}]%' OR NOTE LIKE '%\"{$labelSafe}\"%')";
    }

    /**
     * CASE WHEN yang memetakan NOTE ke kunci jenis (App\Helpers\ComplianceNotification::JENIS).
     * Dicek dua lapis: prefix "[jenis-key]" (format baru, presisi) sebagai
     * prioritas, fallback ke potongan label berkutip (kompatibel dengan baris
     * lama yang terkirim sebelum prefix ini dipasang) — diurutkan label
     * terpanjang dulu supaya "Pengingat Blokir X" tidak salah kena tangkap
     * sebagai "Blokir X".
     */
    private function jenisCaseSql(): string
    {
        $labelsByLength = collect(ComplianceNotification::JENIS)
            ->sortByDesc(fn($def) => mb_strlen($def['label']))
            ->keys();

        $caseWhen = '';
        foreach (ComplianceNotification::JENIS as $key => $def) {
            $caseWhen .= "WHEN NOTE LIKE '[{$key}]%' THEN '{$key}'\n            ";
        }
        foreach ($labelsByLength as $key) {
            $labelSafe = str_replace("'", "''", ComplianceNotification::JENIS[$key]['label']);
            $caseWhen .= "WHEN NOTE LIKE '%\"{$labelSafe}\"%' THEN '{$key}'\n            ";
        }

        return "CASE\n            {$caseWhen}ELSE 'lainnya'\n        END";
    }
}
