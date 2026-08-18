<?php

namespace App\Http\Controllers\Report;

use App\Helpers\Main;
use App\Helpers\QueryAPI;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * Dashboard kinerja pencatatan koleksi fisik.
 *
 * Sumber data: tabel COLLECTIONS (pencatatan via INLIS).
 * Tanggal acuan: COLLECTIONS.CREATEDATE
 */
class RecordingPerformanceController extends Controller
{
    private const CACHE_TTL          = 1800; // 30 menit
    private const PERPUSNAS_BRANCH_ID = 37;

    // Filter hanya menampilkan media yang punya kode kategori di
    // WORKSHEETS.CATEGORY: KC (Karya Cetak), KRA (Karya Rekam Analog),
    // KRD (Karya Rekam Digital). Media tanpa kode (CATEGORY null) tidak muncul.
    // Dijangkau lewat collections.media_id -> collectionmedias.worksheet_id
    // -> worksheets.id
    private const KATEGORI_FISIK = "('KC','KRA','KRD')";

    // Filter petugas (username/NIP di collections.createby). Disimpan sebagai
    // properti supaya tidak perlu diteruskan ke setiap method fetch; baseWhere
    // dan fetchPerMedia membacanya langsung.
    private ?string $userFilter = null;

    private function userClause(): string
    {
        if (!$this->userFilter) return '';
        $safe = str_replace("'", "''", $this->userFilter);
        return " AND UPPER(c.createby) = UPPER('{$safe}')";
    }

    /** Urutkan daftar petugas by nama (fallback username) di PHP. */
    private function sortPetugas(array $rows): array
    {
        $get = fn($r, $k) => is_array($r) ? ($r[$k] ?? $r[strtoupper($k)] ?? '') : ($r->$k ?? $r->{strtoupper($k)} ?? '');
        usort($rows, function ($a, $b) use ($get) {
            $ka = $get($a, 'fullname') ?: $get($a, 'username');
            $kb = $get($b, 'fullname') ?: $get($b, 'username');
            return strcasecmp((string) $ka, (string) $kb);
        });
        return $rows;
    }

    private function mediaFisikSubquery(): string
    {
        return "c.media_id IN (
            SELECT cm.id FROM collectionmedias cm
            JOIN worksheets w ON w.id = cm.worksheet_id
            WHERE w.category IN " . self::KATEGORI_FISIK . "
        )";
    }

    // ── Entry points ─────────────────────────────────────────────────────────

    public function index()
    {
        // Hanya media pencatatan fisik (KC + KRA); media digital (KRD) tidak
        // ditampilkan supaya operator tidak bisa memilih di luar cakupan laporan.
        $medias = QueryAPI::get("
            SELECT cm.id, cm.name, w.category
            FROM collectionmedias cm
            JOIN worksheets w ON w.id = cm.worksheet_id
            WHERE w.category IN " . self::KATEGORI_FISIK . "
              AND NVL(cm.isdelete, 0) = 0
            ORDER BY w.category, cm.name
        ") ?? [];

        $provinces = QueryAPI::get("
            SELECT p.id, p.namapropinsi AS name
            FROM propinsi p
            ORDER BY p.namapropinsi
        ") ?? [];

        // Daftar petugas untuk filter: username/NIP yang pernah mencatat,
        // dengan nama lengkap dari USERS bila ada.
        // ORDER BY dengan agregat ditolak lapisan API, jadi diurutkan di PHP.
        $petugas = QueryAPI::get("
            SELECT createby AS username, MAX(fullname) AS fullname
            FROM (
                SELECT c.createby, u.fullname
                FROM collections c
                LEFT JOIN users u ON UPPER(u.username) = UPPER(c.createby)
                WHERE c.createby IS NOT NULL
                  AND c.createdate >= ADD_MONTHS(TRUNC(SYSDATE), -18)
            )
            GROUP BY createby
        ") ?? [];
        $petugas = $this->sortPetugas($petugas);

        return view('layouts.index', [
            'data' => [
                'content'   => 'report.recording-performance',
                'medias'    => $medias,
                'provinces' => $provinces,
                'petugas'   => $petugas,
                'plugins'   => ['daterangepicker'],
            ]
        ]);
    }

    public function data(Request $request)
    {
        $request->validate([
            'start'       => 'required|date',
            'end'         => 'required|date|after_or_equal:start',
            'media_id'    => 'nullable|integer',
            'granular'    => 'nullable|in:hari,bulan,tahun',
            'province_id' => 'nullable|integer',
        ]);

        [$start, $end, $mediaId, $granular, $provinceId] = $this->resolveFilter($request);
        $this->userFilter = $request->user ? trim((string) $request->user) : null;

        $cacheKey = 'recperf_v2:' . md5("$start|$end|$mediaId|$granular|$provinceId|{$this->userFilter}");

        try {
            $payload = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($start, $end, $mediaId, $granular, $provinceId) {
                return [
                    'ringkasan'   => $this->fetchRingkasan($start, $end, $mediaId, $provinceId),
                    'tren'        => $this->fetchTren($start, $end, $mediaId, $granular, $provinceId),
                    'per_media'   => $this->fetchPerMedia($start, $end, $provinceId),
                    'per_cabang'  => $this->fetchPerCabang($start, $end, $mediaId, $provinceId),
                    'per_petugas' => $this->fetchPerPetugas($start, $end, $mediaId, $provinceId),
                ];
            });
        } catch (\Throwable $e) {
            Log::channel('daily')->error('RecordingPerformance: query gagal', [
                'error'  => $e->getMessage(),
                'filter' => compact('start', 'end', 'mediaId', 'granular', 'provinceId'),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json(array_merge(['success' => true], $payload, [
            'filter' => compact('start', 'end', 'mediaId', 'granular', 'provinceId'),
        ]));
    }

    public function export(Request $request)
    {
        $request->validate([
            'start'       => 'required|date',
            'end'         => 'required|date|after_or_equal:start',
            'media_id'    => 'nullable|integer',
            'granular'    => 'nullable|in:hari,bulan,tahun',
            'province_id' => 'nullable|integer',
        ]);

        [$start, $end, $mediaId, $granular, $provinceId] = $this->resolveFilter($request);
        $this->userFilter = $request->user ? trim((string) $request->user) : null;

        try {
            $ringkasan   = $this->fetchRingkasan($start, $end, $mediaId, $provinceId);
            $tren        = $this->fetchTren($start, $end, $mediaId, $granular, $provinceId);
            $perMedia    = $this->fetchPerMedia($start, $end, $provinceId);
            $perCabang   = $this->fetchPerCabang($start, $end, $mediaId, $provinceId);
            $perPetugas  = $this->fetchPerPetugas($start, $end, $mediaId, $provinceId);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menyiapkan unduhan: ' . $e->getMessage());
        }

        $spreadsheet = new Spreadsheet();
        $sheets = ['Ringkasan', 'Tren', 'Per Petugas', 'Per Media', 'Per Cabang'];

        foreach ($sheets as $i => $name) {
            $sheet = $i === 0 ? $spreadsheet->getActiveSheet() : $spreadsheet->createSheet();
            $sheet->setTitle($name);
        }

        // Sheet 1: Ringkasan
        $s = $spreadsheet->getSheet(0);
        $r = $ringkasan;
        $s->setCellValue('A1', 'Kinerja Pencatatan Koleksi Fisik');
        $s->setCellValue('A2', 'Periode: ' . date('d/m/Y', strtotime($start)) . ' – ' . date('d/m/Y', strtotime($end)));
        $s->setCellValue('A4', 'Indikator')->setCellValue('B4', 'Nilai');
        $s->fromArray([
            ['Total Judul (katalog unik)', $r['total_judul'] ?? 0],
            ['Total Eksemplar (koleksi)',  $r['total_eks']   ?? 0],
            ['Hari Aktif',                 $r['jml_hari']    ?? 0],
            ['Rata-rata Eks / Hari',       $r['rata2_hari']  ?? 0],
        ], null, 'A5');

        // Sheet 2: Tren
        $s = $spreadsheet->getSheet(1);
        $s->fromArray(['Periode', 'Judul', 'Eksemplar', 'Cabang Aktif'], null, 'A1');
        $row = 2;
        foreach ($tren as $t) {
            $s->setCellValue("A$row", $t->PERIODE ?? '');
            $s->setCellValue("B$row", $t->TOTAL_JUDUL ?? 0);
            $s->setCellValue("C$row", $t->TOTAL_EKS ?? 0);
            $s->setCellValue("D$row", $t->JML_CABANG ?? 0);
            $row++;
        }

        // Sheet 3: Per Petugas
        $s = $spreadsheet->getSheet(2);
        $s->fromArray(['Petugas', 'Judul', 'Eksemplar', 'Hari Aktif', 'Rata-rata Eks / Hari'], null, 'A1');
        $row = 2;
        foreach ($perPetugas as $pt) {
            $s->setCellValue("A$row", $pt->PETUGAS ?? '');
            $s->setCellValue("B$row", $pt->TOTAL_JUDUL ?? 0);
            $s->setCellValue("C$row", $pt->TOTAL_EKS ?? 0);
            $s->setCellValue("D$row", $pt->JML_HARI ?? 0);
            $s->setCellValue("E$row", $pt->RATA2_HARI ?? 0);
            $row++;
        }

        // Sheet 4: Per Media
        $s = $spreadsheet->getSheet(3);
        $s->fromArray(['Jenis Media', 'Judul', 'Eksemplar', '% Eks'], null, 'A1');
        $totalEks = array_sum(array_map(fn($m) => $m->TOTAL_EKS ?? 0, $perMedia));
        $row = 2;
        foreach ($perMedia as $m) {
            $eks = $m->TOTAL_EKS ?? 0;
            $pct = $totalEks > 0 ? round($eks / $totalEks * 100, 1) : 0;
            $s->setCellValue("A$row", $m->NAMA_MEDIA ?? '');
            $s->setCellValue("B$row", $m->TOTAL_JUDUL ?? 0);
            $s->setCellValue("C$row", $eks);
            $s->setCellValue("D$row", $pct . '%');
            $row++;
        }

        // Sheet 5: Per Cabang
        $s = $spreadsheet->getSheet(4);
        $s->fromArray(['Cabang', 'Provinsi', 'Judul', 'Eksemplar'], null, 'A1');
        $row = 2;
        foreach ($perCabang as $c) {
            $s->setCellValue("A$row", $c->NAMA_CABANG ?? '');
            $s->setCellValue("B$row", $c->NAMA_PROPINSI ?? '');
            $s->setCellValue("C$row", $c->TOTAL_JUDUL ?? 0);
            $s->setCellValue("D$row", $c->TOTAL_EKS ?? 0);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'kinerja-pencatatan-' . $start . '-' . $end . '.xlsx';

        return response()->stream(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * @return array{0:string,1:string,2:?int,3:string,4:?int}
     */
    private function resolveFilter(Request $request): array
    {
        $provinceId = null;
        if (Main::isPerpusnas()) {
            $provinceId = $request->province_id ? (int) $request->province_id : null;
        } else {
            $provinceId = (int) session('province_id');
        }

        return [
            date('Y-m-d', strtotime($request->start)),
            date('Y-m-d', strtotime($request->end)),
            $request->media_id ? (int) $request->media_id : null,
            $request->granular ?: 'bulan',
            $provinceId,
        ];
    }

    /** WHERE clause bersama — index-friendly, tanpa NVL di kolom. */
    private function baseWhere(string $start, string $end, ?int $mediaId, ?int $provinceId): string
    {
        $ds = "TO_DATE('{$start}','YYYY-MM-DD')";
        $de = "TO_DATE('{$end}','YYYY-MM-DD') + 1";

        $where = "c.createdate >= {$ds} AND c.createdate < {$de}";
        $where .= " AND " . $this->mediaFisikSubquery();
        $where .= $this->userClause();

        if ($mediaId)    $where .= " AND c.media_id = {$mediaId}";
        if ($provinceId) $where .= " AND b.province_id = {$provinceId}";

        return $where;
    }

    private function fetchRingkasan(string $start, string $end, ?int $mediaId, ?int $provinceId): array
    {
        $where = $this->baseWhere($start, $end, $mediaId, $provinceId);

        // Judul = katalog unik (COUNT DISTINCT catalog_id); Eks = baris koleksi
        // (COUNT id). Satu judul bisa punya banyak eksemplar.
        $row = QueryAPI::get("
            SELECT
                COUNT(DISTINCT c.catalog_id)   AS total_judul,
                COUNT(c.id)                    AS total_eks,
                COUNT(DISTINCT TRUNC(c.createdate)) AS jml_hari,
                COUNT(DISTINCT c.branch_id)    AS jml_cabang,
                ROUND(COUNT(c.id) /
                    NULLIF(COUNT(DISTINCT TRUNC(c.createdate)), 0), 1) AS rata2_hari
            FROM collections c
            LEFT JOIN branchs b ON b.id = c.branch_id
            WHERE {$where}
        ", true);

        return [
            'total_judul' => $row->TOTAL_JUDUL  ?? 0,
            'total_eks'   => $row->TOTAL_EKS    ?? 0,
            'jml_hari'    => $row->JML_HARI     ?? 0,
            'jml_cabang'  => $row->JML_CABANG   ?? 0,
            'rata2_hari'  => $row->RATA2_HARI   ?? 0,
        ];
    }

    private function fetchTren(string $start, string $end, ?int $mediaId, string $granular, ?int $provinceId): array
    {
        $where = $this->baseWhere($start, $end, $mediaId, $provinceId);

        $grpExpr = match ($granular) {
            'hari'  => "TO_CHAR(c.createdate, 'YYYY-MM-DD')",
            'tahun' => "TO_CHAR(c.createdate, 'YYYY')",
            default => "TO_CHAR(c.createdate, 'YYYY-MM')",
        };

        $rows = QueryAPI::get("
            SELECT
                {$grpExpr}              AS periode,
                COUNT(DISTINCT c.catalog_id) AS total_judul,
                COUNT(c.id)             AS total_eks,
                COUNT(DISTINCT c.branch_id) AS jml_cabang
            FROM collections c
            LEFT JOIN branchs b ON b.id = c.branch_id
            WHERE {$where}
            GROUP BY {$grpExpr}
            ORDER BY 1
        ") ?? [];

        return is_array($rows) ? $rows : [$rows];
    }

    private function fetchPerMedia(string $start, string $end, ?int $provinceId): array
    {
        // Per media selalu semua jenis media (abaikan filter mediaId)
        $ds = "TO_DATE('{$start}','YYYY-MM-DD')";
        $de = "TO_DATE('{$end}','YYYY-MM-DD') + 1";
        $whereProvince = $provinceId ? "AND b.province_id = {$provinceId}" : '';

        $rows = QueryAPI::get("
            SELECT
                NVL(cm.name, '(Tidak ada media)') AS nama_media,
                COUNT(DISTINCT c.catalog_id)       AS total_judul,
                COUNT(c.id)                        AS total_eks
            FROM collections c
            LEFT JOIN branchs b ON b.id = c.branch_id
            LEFT JOIN collectionmedias cm ON cm.id = c.media_id
            WHERE c.createdate >= {$ds}
              AND c.createdate <  {$de}
              AND {$this->mediaFisikSubquery()}
              {$this->userClause()}
              {$whereProvince}
            GROUP BY cm.name
            ORDER BY total_eks DESC
        ") ?? [];

        return is_array($rows) ? $rows : [$rows];
    }

    private function fetchPerCabang(string $start, string $end, ?int $mediaId, ?int $provinceId): array
    {
        $where = $this->baseWhere($start, $end, $mediaId, $provinceId);

        $rows = QueryAPI::get("
            SELECT
                NVL(b.name, '(Tidak diketahui)')        AS nama_cabang,
                NVL(p.namapropinsi, '—')                 AS nama_propinsi,
                COUNT(DISTINCT c.catalog_id)             AS total_judul,
                COUNT(c.id)                              AS total_eks
            FROM collections c
            LEFT JOIN branchs b  ON b.id  = c.branch_id
            LEFT JOIN propinsi p ON p.id  = b.province_id
            WHERE {$where}
            GROUP BY b.name, p.namapropinsi
            ORDER BY total_eks DESC
        ") ?? [];

        return is_array($rows) ? $rows : [$rows];
    }

    private function fetchPerPetugas(string $start, string $end, ?int $mediaId, ?int $provinceId): array
    {
        $where = $this->baseWhere($start, $end, $mediaId, $provinceId);

        // Tampilkan nama lengkap petugas; createby berisi username/NIP, jadi
        // di-join ke USERS. Akun tanpa padanan di USERS (mis. akun non-staf)
        // fallback ke username-nya sendiri.
        $rows = QueryAPI::get("
            SELECT
                NVL(MAX(u.fullname), NVL(c.createby, '(tidak diketahui)')) AS petugas,
                NVL(c.createby, '(tidak diketahui)')     AS username,
                COUNT(DISTINCT c.catalog_id)             AS total_judul,
                COUNT(c.id)                              AS total_eks,
                COUNT(DISTINCT TRUNC(c.createdate))      AS jml_hari,
                ROUND(COUNT(c.id) /
                    NULLIF(COUNT(DISTINCT TRUNC(c.createdate)), 0), 1) AS rata2_hari
            FROM collections c
            LEFT JOIN branchs b ON b.id = c.branch_id
            LEFT JOIN users u ON UPPER(u.username) = UPPER(c.createby)
            WHERE {$where}
            GROUP BY c.createby
            ORDER BY total_eks DESC
        ") ?? [];

        return is_array($rows) ? $rows : [$rows];
    }
}
