<?php

namespace App\Http\Controllers\Report;

use App\Helpers\QueryAPI;
use App\Http\Controllers\Controller;
use App\Traits\OracleHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Dashboard kinerja validasi koleksi.
 *
 * Dua ukuran yang sengaja dipisah karena sering tertukar:
 *   - WAKTU KERJA  : jeda antar validasi berturut-turut oleh orang yang sama
 *                    di hari yang sama. Perkiraan lama menangani satu koleksi.
 *   - WAKTU TUNGGU : jeda kalender CREATED_AT -> VALIDATED_AT. Lama koleksi
 *                    mengantre sebelum disentuh, bukan lama dikerjakan.
 *
 * Jeda di atas AMBANG_ISTIRAHAT menit dianggap istirahat/pindah tugas, bukan
 * satu koleksi yang dikerjakan selama itu — dilaporkan terpisah, tidak dibuang.
 */
class ValidationPerformanceController extends Controller
{
    use OracleHelper;

    private const AMBANG_ISTIRAHAT = 60;   // menit
    private const MIN_SAMPEL       = 5;    // jeda minimum agar median bermakna
    private const CACHE_TTL        = 600;  // 10 menit

    // Filter validator (E_COLLECTIONS.VALIDATED_BY_NAME = username/NIP).
    private ?string $userFilter = null;

    private function userClause(): string
    {
        if (!$this->userFilter) return '';
        $safe = str_replace("'", "''", $this->userFilter);
        return " AND UPPER(C.VALIDATED_BY_NAME) = UPPER('{$safe}')";
    }

    /** Label filter petugas: "Nama (NIP)" bila ketemu, kalau tidak NIP saja. */
    private function userLabel(string $kosong): string
    {
        if (!$this->userFilter) return $kosong;
        $safe = str_replace("'", "''", $this->userFilter);
        $u = QueryAPI::get("SELECT fullname FROM users WHERE UPPER(username) = UPPER('{$safe}')", true);
        $nama = $u->FULLNAME ?? $u->fullname ?? null;
        return $nama ? "{$nama} ({$this->userFilter})" : $this->userFilter;
    }

    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'content' => 'report.validation-performance',
                'medias'  => $this->fetchMedias(),
                'petugas' => $this->fetchPetugas(),
                'plugins' => ['daterangepicker'],
            ]
        ]);
    }

    /**
     * Seluruh angka dashboard dalam satu respons, supaya filter cukup sekali
     * jalan dan keempat panel selalu konsisten satu sama lain.
     */
    public function data(Request $request)
    {
        $request->validate([
            'start'    => 'required|date',
            'end'      => 'required|date|after_or_equal:start',
            'media_id' => 'nullable|integer',
            'granular' => 'nullable|in:hari,bulan,tahun',
        ]);

        [$start, $end, $mediaId, $granular] = $this->resolveFilter($request);
        $this->userFilter = $request->user ? trim((string) $request->user) : null;

        try {
            $payload = $this->buildPayload($start, $end, $mediaId, $granular);
        } catch (\Throwable $e) {
            Log::channel('daily')->error('ValidationPerformance: query gagal', [
                'error'  => $e->getMessage(),
                'filter' => compact('start', 'end', 'mediaId', 'granular'),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json(array_merge(['success' => true], $payload, [
            'filter' => [
                'start'    => $start,
                'end'      => $end,
                'media_id' => $mediaId,
                'granular' => $granular,
                'ambang'   => self::AMBANG_ISTIRAHAT,
            ],
        ]));
    }

    /**
     * Unduh seluruh panel sebagai satu workbook Excel (4 sheet).
     */
    public function export(Request $request)
    {
        $request->validate([
            'start'    => 'required|date',
            'end'      => 'required|date|after_or_equal:start',
            'media_id' => 'nullable|integer',
            'granular' => 'nullable|in:hari,bulan,tahun',
        ]);

        [$start, $end, $mediaId, $granular] = $this->resolveFilter($request);
        $this->userFilter = $request->user ? trim((string) $request->user) : null;

        try {
            $payload = $this->buildPayload($start, $end, $mediaId, $granular);
        } catch (\Throwable $e) {
            Log::channel('daily')->error('ValidationPerformance: export gagal', [
                'error'  => $e->getMessage(),
                'filter' => compact('start', 'end', 'mediaId', 'granular'),
            ]);

            return back()->with('error', 'Gagal menyiapkan unduhan: ' . $e->getMessage());
        }

        $mediaLabel = 'Semua Jenis Media';
        if ($mediaId) {
            $found = collect($this->fetchMedias())->firstWhere('id', $mediaId);
            $mediaLabel = $found['name'] ?? "Media ID {$mediaId}";
        }

        $meta = [
            'periode' => date('d/m/Y', strtotime($start)) . ' – ' . date('d/m/Y', strtotime($end)),
            'media'   => $mediaLabel,
        ];

        return $this->buildWorkbook($payload, $meta, $granular, $request);
    }

    public function pdf(Request $request)
    {
        $request->validate([
            'start'    => 'required|date',
            'end'      => 'required|date|after_or_equal:start',
            'media_id' => 'nullable|integer',
            'granular' => 'nullable|in:hari,bulan,tahun',
        ]);

        [$start, $end, $mediaId, $granular] = $this->resolveFilter($request);
        $this->userFilter = $request->user ? trim((string) $request->user) : null;

        try {
            $payload = $this->buildPayload($start, $end, $mediaId, $granular);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menyiapkan PDF: ' . $e->getMessage());
        }

        $mediaLabel = 'Semua Jenis Media';
        if ($mediaId) {
            $found = collect($this->fetchMedias())->firstWhere('id', $mediaId);
            $mediaLabel = $found['name'] ?? "Media ID {$mediaId}";
        }

        $r = $payload['ringkasan'];
        $meta = [
            'Periode'     => date('d/m/Y', strtotime($start)) . ' – ' . date('d/m/Y', strtotime($end)),
            'Jenis Media' => $mediaLabel,
            'Validator'   => $this->userLabel('Semua validator'),
        ];

        $sections = [
            ['title' => 'Ringkasan', 'type' => 'kv', 'rows' => [
                ['Total Validasi',                  number_format((int) ($r['total_validasi'] ?? 0), 0, ',', '.')],
                ['Jumlah Validator',                (int) ($r['jml_validator'] ?? 0)],
                ['Jumlah Hari Kerja',               (int) ($r['jml_hari'] ?? 0)],
                ['Waktu Kerja / Koleksi (median)',  ($r['median_menit'] ?? '-') . ' menit'],
                ['Waktu Tunggu (median, hari)',     $r['median_tunggu'] ?? '-'],
                ['Koleksi Menunggu > 90 Hari',      number_format((int) ($r['diatas_90_hari'] ?? 0), 0, ',', '.')],
            ]],
            ['title' => 'Per Validator', 'headers' => ['Validator', 'NIP', 'Koleksi', 'Hari', 'Per Hari', 'Median (mnt)'], 'num' => [2, 3, 4, 5],
                'rows' => array_map(fn($v) => [$v['validator'] ?? '-', $v['nip'] ?? '', $v['n_jeda'] ?? 0, $v['hari_kerja'] ?? 0, $v['per_hari'] ?? 0, $v['median_menit'] ?? '-'], $payload['validator'])],
            ['title' => 'Tren per ' . ucfirst($granular), 'headers' => ['Periode', 'Koleksi', 'Validator', 'Median (mnt)'], 'num' => [1, 2, 3],
                'rows' => array_map(fn($t) => [$t['periode'] ?? '-', $t['n_jeda'] ?? 0, $t['jml_validator'] ?? 0, $t['median_menit'] ?? '-'], $payload['tren'])],
            ['title' => 'Per Jenis Media', 'headers' => ['Media', 'Koleksi', 'Validator', 'Median (mnt)'], 'num' => [1, 2, 3],
                'rows' => array_map(fn($m) => [$m['media'] ?? '-', $m['n_jeda'] ?? 0, $m['jml_validator'] ?? 0, $m['median_menit'] ?? '-'], $payload['media'])],
        ];

        $pdf = Pdf::loadView('pdf.report-kinerja', [
            'title'    => 'Kinerja Validasi Karya Digital',
            'meta'     => $meta,
            'sections' => $sections,
        ])->setPaper('a4', 'portrait')->setWarnings(false);

        return $pdf->stream('kinerja-validasi-' . $start . '-' . $end . '.pdf');
    }

    // ── Pembangun data bersama ───────────────────────────────────────────────

    /**
     * @return array{0:string,1:string,2:?int,3:string} [start, end, mediaId, granular]
     */
    private function resolveFilter(Request $request): array
    {
        return [
            date('Y-m-d', strtotime($request->start)),
            date('Y-m-d', strtotime($request->end)),
            $request->media_id ? (int) $request->media_id : null,
            $request->granular ?: 'bulan',
        ];
    }

    private function buildPayload(string $start, string $end, ?int $mediaId, string $granular): array
    {
        $cacheKey = 'valperf:' . md5("$start|$end|$mediaId|$granular|{$this->userFilter}");

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($start, $end, $mediaId, $granular) {
            $conn = $this->getOracleConnection();

            return [
                'ringkasan' => $this->fetchRingkasan($conn, $start, $end, $mediaId),
                'validator' => $this->fetchPerValidator($conn, $start, $end, $mediaId),
                'tren'      => $this->fetchTren($conn, $start, $end, $mediaId, $granular),
                'media'     => $this->fetchPerMedia($conn, $start, $end),
            ];
        });
    }

    // ── Blok SQL bersama ─────────────────────────────────────────────────────

    /**
     * Subquery jeda antar validasi. PARTITION per validator per hari supaya
     * koleksi pertama tiap hari tidak dihitung sebagai jeda dari hari kemarin.
     */
    private function jedaSubquery(string $start, string $end, ?int $mediaId): string
    {
        $filterMedia = $mediaId ? "AND C.COLLECTION_MEDIA_ID = {$mediaId}" : '';

        return "
            SELECT C.VALIDATED_BY_NAME,
                   TRUNC(C.VALIDATED_AT) AS HARI,
                   C.VALIDATED_AT,
                   (C.VALIDATED_AT - LAG(C.VALIDATED_AT) OVER (
                        PARTITION BY C.VALIDATED_BY_NAME, TRUNC(C.VALIDATED_AT)
                        ORDER BY C.VALIDATED_AT)) * 1440 AS JEDA
            FROM E_COLLECTIONS C
            WHERE C.VALIDATED_AT >= TO_DATE('{$start}','YYYY-MM-DD')
              AND C.VALIDATED_AT <  TO_DATE('{$end}','YYYY-MM-DD') + 1
              AND C.VALIDATED_BY_NAME IS NOT NULL
              {$filterMedia}
              {$this->userClause()}
        ";
    }

    // ── Panel 1: kartu ringkasan ─────────────────────────────────────────────

    private function fetchRingkasan($conn, string $start, string $end, ?int $mediaId): array
    {
        $ambang = self::AMBANG_ISTIRAHAT;
        $jeda   = $this->jedaSubquery($start, $end, $mediaId);

        $sql = "
            SELECT COUNT(*)                                                        AS TOTAL_VALIDASI,
                   COUNT(DISTINCT VALIDATED_BY_NAME)                               AS JML_VALIDATOR,
                   COUNT(DISTINCT HARI)                                            AS JML_HARI,
                   ROUND(MEDIAN(CASE WHEN JEDA <= {$ambang} THEN JEDA END), 1)      AS MEDIAN_MENIT,
                   ROUND(AVG(CASE WHEN JEDA <= {$ambang} THEN JEDA END), 1)         AS RATA2_MENIT,
                   SUM(CASE WHEN JEDA > {$ambang} THEN 1 ELSE 0 END)                AS JEDA_PANJANG,
                   SUM(CASE WHEN JEDA <= 1 THEN 1 ELSE 0 END)                       AS JEDA_KILAT
            FROM ({$jeda})
        ";

        $ringkasan = $this->one($conn, $sql);

        // Waktu tunggu dihitung terpisah: basisnya seluruh koleksi tervalidasi,
        // bukan hanya yang punya jeda pendahulu.
        $filterMedia = $mediaId ? "AND C.COLLECTION_MEDIA_ID = {$mediaId}" : '';
        $tunggu = $this->one($conn, "
            SELECT ROUND(MEDIAN(TRUNC(C.VALIDATED_AT) - TRUNC(C.CREATED_AT)), 1) AS MEDIAN_TUNGGU,
                   ROUND(AVG(TRUNC(C.VALIDATED_AT) - TRUNC(C.CREATED_AT)), 1)    AS RATA2_TUNGGU,
                   SUM(CASE WHEN TRUNC(C.VALIDATED_AT) - TRUNC(C.CREATED_AT) > 90 THEN 1 ELSE 0 END) AS DIATAS_90_HARI,
                   COUNT(*) AS N_TUNGGU
            FROM E_COLLECTIONS C
            WHERE C.VALIDATED_AT >= TO_DATE('{$start}','YYYY-MM-DD')
              AND C.VALIDATED_AT <  TO_DATE('{$end}','YYYY-MM-DD') + 1
              AND C.CREATED_AT IS NOT NULL
              {$filterMedia}
        ");

        return array_merge($ringkasan, $tunggu);
    }

    // ── Panel 2: per validator ───────────────────────────────────────────────

    private function fetchPerValidator($conn, string $start, string $end, ?int $mediaId): array
    {
        $ambang = self::AMBANG_ISTIRAHAT;
        $minN   = self::MIN_SAMPEL;
        $jeda   = $this->jedaSubquery($start, $end, $mediaId);

        // Nama validator dari USERS; kalau NIP tak punya padanan, tampilkan
        // NIP-nya sendiri (jangan kosong). Join case-insensitive, konsisten
        // dengan query lain.
        // SENGAJA tidak "WHERE G.JEDA IS NOT NULL" di sini — itu akan membuang
        // validasi PERTAMA tiap validator per hari (baris itu memang tidak punya
        // jeda-sebelumnya buat dihitung), bikin COUNT(*) di sini beda dari kartu
        // "Total Validasi" di fetchRingkasan(). Statistik yang benar-benar butuh
        // jeda (median, jeda panjang/kilat) tetap aman karena sudah dibungkus
        // CASE WHEN G.JEDA ... — otomatis melewatkan baris NULL tanpa perlu WHERE.
        return $this->all($conn, "
            SELECT NVL(U.FULLNAME, G.VALIDATED_BY_NAME)                        AS VALIDATOR,
                   G.VALIDATED_BY_NAME                                         AS NIP,
                   COUNT(*)                                                    AS N_JEDA,
                   COUNT(DISTINCT G.HARI)                                      AS HARI_KERJA,
                   ROUND(COUNT(*) / COUNT(DISTINCT G.HARI), 1)                 AS PER_HARI,
                   ROUND(MEDIAN(CASE WHEN G.JEDA <= {$ambang} THEN G.JEDA END), 1) AS MEDIAN_MENIT,
                   SUM(CASE WHEN G.JEDA > {$ambang} THEN 1 ELSE 0 END)         AS JEDA_PANJANG,
                   SUM(CASE WHEN G.JEDA <= 1 THEN 1 ELSE 0 END)                AS JEDA_KILAT
            FROM ({$jeda}) G
            LEFT JOIN USERS U ON UPPER(U.USERNAME) = UPPER(G.VALIDATED_BY_NAME)
            GROUP BY U.FULLNAME, G.VALIDATED_BY_NAME
            HAVING COUNT(G.JEDA) >= {$minN}
            ORDER BY COUNT(*) DESC
        ");
    }

    // ── Panel 3: tren per periode ────────────────────────────────────────────

    private function fetchTren($conn, string $start, string $end, ?int $mediaId, string $granular): array
    {
        $ambang = self::AMBANG_ISTIRAHAT;
        $jeda   = $this->jedaSubquery($start, $end, $mediaId);

        $format = match ($granular) {
            'hari'  => 'YYYY-MM-DD',
            'tahun' => 'YYYY',
            default => 'YYYY-MM',
        };

        // Sama seperti fetchPerValidator() — tidak difilter WHERE JEDA IS NOT NULL,
        // supaya COUNT(*) konsisten dengan "Total Validasi" (lihat komentar di sana).
        return $this->all($conn, "
            SELECT TO_CHAR(VALIDATED_AT, '{$format}')                          AS PERIODE,
                   COUNT(*)                                                    AS N_JEDA,
                   COUNT(DISTINCT VALIDATED_BY_NAME)                           AS JML_VALIDATOR,
                   COUNT(DISTINCT HARI)                                        AS JML_HARI,
                   ROUND(MEDIAN(CASE WHEN JEDA <= {$ambang} THEN JEDA END), 1) AS MEDIAN_MENIT
            FROM ({$jeda})
            GROUP BY TO_CHAR(VALIDATED_AT, '{$format}')
            ORDER BY 1
        ");
    }

    // ── Panel 4: per jenis media ─────────────────────────────────────────────

    private function fetchPerMedia($conn, string $start, string $end): array
    {
        $ambang = self::AMBANG_ISTIRAHAT;

        // Sama seperti fetchPerValidator()/fetchTren() — tidak difilter
        // WHERE JEDA IS NOT NULL, supaya COUNT(*) konsisten dengan "Total Validasi".
        return $this->all($conn, "
            SELECT M.ID                                                        AS MEDIA_ID,
                   M.NAME                                                      AS MEDIA,
                   COUNT(*)                                                    AS N_JEDA,
                   COUNT(DISTINCT G.VALIDATED_BY_NAME)                         AS JML_VALIDATOR,
                   ROUND(MEDIAN(CASE WHEN G.JEDA <= {$ambang} THEN G.JEDA END), 1) AS MEDIAN_MENIT
            FROM (
                SELECT C.COLLECTION_MEDIA_ID,
                       C.VALIDATED_BY_NAME,
                       (C.VALIDATED_AT - LAG(C.VALIDATED_AT) OVER (
                            PARTITION BY C.VALIDATED_BY_NAME, C.COLLECTION_MEDIA_ID, TRUNC(C.VALIDATED_AT)
                            ORDER BY C.VALIDATED_AT)) * 1440 AS JEDA
                FROM E_COLLECTIONS C
                WHERE C.VALIDATED_AT >= TO_DATE('{$start}','YYYY-MM-DD')
                  AND C.VALIDATED_AT <  TO_DATE('{$end}','YYYY-MM-DD') + 1
                  AND C.VALIDATED_BY_NAME IS NOT NULL
                  {$this->userClause()}
            ) G
            JOIN COLLECTIONMEDIAS M ON M.ID = G.COLLECTION_MEDIA_ID
            GROUP BY M.ID, M.NAME
            ORDER BY COUNT(*) DESC
        ");
    }

    // ── Daftar media untuk dropdown ──────────────────────────────────────────

    private function fetchMedias(): array
    {
        return Cache::remember('valperf:medias', 3600, function () {
            $conn = $this->getOracleConnection();

            return $this->all($conn, "
                SELECT M.ID, M.NAME, COUNT(C.ID) AS JML
                FROM COLLECTIONMEDIAS M
                JOIN E_COLLECTIONS C ON C.COLLECTION_MEDIA_ID = M.ID AND C.VALIDATED_AT IS NOT NULL
                WHERE NVL(M.ISDELETE, 0) = 0
                GROUP BY M.ID, M.NAME
                ORDER BY COUNT(C.ID) DESC
            ");
        });
    }

    // ── Daftar validator untuk dropdown ──────────────────────────────────────

    private function fetchPetugas(): array
    {
        return Cache::remember('valperf:petugas', 3600, function () {
            $conn = $this->getOracleConnection();

            // ORDER BY NVL(MAX(FULLNAME),USERNAME) memicu ORA-00935 (group
            // function nested too deeply), jadi diurutkan di PHP.
            $rows = $this->all($conn, "
                SELECT USERNAME, MAX(FULLNAME) AS FULLNAME FROM (
                    SELECT C.VALIDATED_BY_NAME AS USERNAME, U.FULLNAME
                    FROM E_COLLECTIONS C
                    LEFT JOIN USERS U ON UPPER(U.USERNAME) = UPPER(C.VALIDATED_BY_NAME)
                    WHERE C.VALIDATED_BY_NAME IS NOT NULL
                      AND C.VALIDATED_AT >= ADD_MONTHS(TRUNC(SYSDATE), -18)
                )
                GROUP BY USERNAME
            ");

            usort($rows, function ($a, $b) {
                $ka = ($a['FULLNAME'] ?? $a['fullname'] ?? '') ?: ($a['USERNAME'] ?? $a['username'] ?? '');
                $kb = ($b['FULLNAME'] ?? $b['fullname'] ?? '') ?: ($b['USERNAME'] ?? $b['username'] ?? '');
                return strcasecmp((string) $ka, (string) $kb);
            });
            return $rows;
        });
    }

    // ── Util ODBC ────────────────────────────────────────────────────────────

    private function all($conn, string $sql): array
    {
        $result = odbc_exec($conn, $sql);
        if (!$result) {
            throw new \RuntimeException('Query gagal: ' . odbc_errormsg($conn));
        }

        $rows = [];
        while ($row = odbc_fetch_array($result)) {
            $rows[] = array_change_key_case($row, CASE_LOWER);
        }
        odbc_free_result($result);

        return $rows;
    }

    private function one($conn, string $sql): array
    {
        return $this->all($conn, $sql)[0] ?? [];
    }

    // ── Excel ────────────────────────────────────────────────────────────────

    private function buildWorkbook(array $payload, array $meta, string $granular, Request $request)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        $r = $payload['ringkasan'];

        // Sheet 1 — Ringkasan (pasangan label/nilai, bukan tabel biasa)
        $s1 = $spreadsheet->createSheet();
        $s1->setTitle('Ringkasan');
        $row = $this->tulisJudul($s1, 'Kinerja Validasi Koleksi — Ringkasan', $meta, 2);
        $ringkasanRows = [
            ['Total validasi',                  (int) ($r['total_validasi'] ?? 0)],
            ['Jumlah validator',                (int) ($r['jml_validator'] ?? 0)],
            ['Jumlah hari kerja',               (int) ($r['jml_hari'] ?? 0)],
            ['Waktu kerja / koleksi (median)',  $this->menit($r['median_menit'] ?? null)],
            ['Waktu kerja / koleksi (rata2)',   $this->menit($r['rata2_menit'] ?? null)],
            ['Waktu tunggu (median, hari)',     $r['median_tunggu'] ?? '-'],
            ['Waktu tunggu (rata2, hari)',      $r['rata2_tunggu'] ?? '-'],
            ['Koleksi menunggu > 90 hari',      (int) ($r['diatas_90_hari'] ?? 0)],
            ['Jeda > ' . self::AMBANG_ISTIRAHAT . ' menit (istirahat)', (int) ($r['jeda_panjang'] ?? 0)],
            ['Jeda < 1 menit (validasi kilat)', (int) ($r['jeda_kilat'] ?? 0)],
        ];
        foreach ($ringkasanRows as $pair) {
            $s1->setCellValue("A{$row}", $pair[0]);
            $s1->setCellValue("B{$row}", $pair[1]);
            $row++;
        }
        $s1->getColumnDimension('A')->setWidth(38);
        $s1->getColumnDimension('B')->setWidth(22);

        // Sheet 2 — Per validator
        $this->sheetTabel(
            $spreadsheet, 'Per Validator', 'Kinerja per Validator', $meta,
            ['Validator', 'NIP', 'Koleksi', 'Hari Kerja', 'Per Hari', 'Median (menit)', 'Jeda Panjang', 'Jeda < 1 mnt'],
            array_map(fn($v) => [
                $v['validator'] ?? '(tidak dikenal)',
                (string) ($v['nip'] ?? ''),
                (int) ($v['n_jeda'] ?? 0),
                (int) ($v['hari_kerja'] ?? 0),
                (float) ($v['per_hari'] ?? 0),
                $v['median_menit'] ?? '-',
                (int) ($v['jeda_panjang'] ?? 0),
                (int) ($v['jeda_kilat'] ?? 0),
            ], $payload['validator']),
            [34, 20, 12, 12, 12, 15, 14, 14]
        );

        // Sheet 3 — Tren
        $this->sheetTabel(
            $spreadsheet, 'Tren', 'Tren per ' . ucfirst($granular), $meta,
            ['Periode', 'Koleksi', 'Validator', 'Hari', 'Median (menit)'],
            array_map(fn($t) => [
                $t['periode'] ?? '',
                (int) ($t['n_jeda'] ?? 0),
                (int) ($t['jml_validator'] ?? 0),
                (int) ($t['jml_hari'] ?? 0),
                $t['median_menit'] ?? '-',
            ], $payload['tren']),
            [16, 12, 14, 10, 15]
        );

        // Sheet 4 — Per media
        $this->sheetTabel(
            $spreadsheet, 'Per Media', 'Per Jenis Media (semua media)', $meta,
            ['Media', 'Koleksi', 'Validator', 'Median (menit)'],
            array_map(fn($m) => [
                $m['media'] ?? '',
                (int) ($m['n_jeda'] ?? 0),
                (int) ($m['jml_validator'] ?? 0),
                $m['median_menit'] ?? '-',
            ], $payload['media']),
            [30, 12, 14, 15]
        );

        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'Kinerja_Validasi_' . date('Ymd_His') . '.xlsx';

        return $this->streamXlsx($spreadsheet, $filename, $request);
    }

    /**
     * Bikin satu sheet tabel: judul, header tebal, isi baris.
     */
    private function sheetTabel(
        \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet,
        string $tabTitle,
        string $mainTitle,
        array $meta,
        array $headers,
        array $rows,
        array $widths
    ): void {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle($tabTitle);

        $headerRow = $this->tulisJudul($sheet, $mainTitle, $meta, count($headers));

        $col = 1;
        foreach ($headers as $h) {
            $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue("{$letter}{$headerRow}", $h);
            $sheet->getColumnDimension($letter)->setWidth($widths[$col - 1] ?? 15);
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

    private function menit($v): string
    {
        return ($v === null || $v === '') ? '-' : $v . ' menit';
    }

    /**
     * Kop sheet: judul, baris periode + jenis media, tanggal unduh.
     * Ditulis lokal (bukan OracleHelper::writeTitleRows) karena laporan ini
     * memakai label "Jenis Media", bukan "Provinsi" yang di-hardcode helper itu.
     *
     * @return int nomor baris tempat header tabel harus mulai
     */
    private function tulisJudul(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        string $mainTitle,
        array $meta,
        int $colCount
    ): int {
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);

        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', $mainTitle);
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '0D47A1']],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(24);

        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->setCellValue('A2', 'Periode: ' . ($meta['periode'] ?? '-')
            . '     |     Jenis Media: ' . ($meta['media'] ?? '-'));
        $sheet->getStyle('A2')->applyFromArray(['font' => ['size' => 10, 'color' => ['rgb' => '444444']]]);

        $sheet->mergeCells("A3:{$lastCol}3");
        $sheet->setCellValue('A3', 'Diunduh: ' . date('d/m/Y H:i'));
        $sheet->getStyle('A3')->applyFromArray(['font' => ['size' => 10, 'color' => ['rgb' => '444444']]]);

        $sheet->getStyle("A1:{$lastCol}3")->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E3F2FD');

        $sheet->getRowDimension(4)->setRowHeight(6);

        return 5; // header tabel mulai di baris 5
    }
}
