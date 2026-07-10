<?php

namespace App\Http\Controllers\CoachingSupervision;

use App\Helpers\ComplianceSettings;
use App\Http\Controllers\Controller;
use App\Traits\OracleHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Compliance V3 – Gabungan: pra-2026 + 2026+
 *
 * Pra-2026 : KCKR dari createdate, tanpa pelacakan konfirmasi terbit.
 * 2026+    : KCKR dari tanggal_terbit, pelacakan konfirmasi terbit aktif.
 * Kolom terbit (Sudah Terbit, Belum, Hutang, Lewat Teguran) hanya terisi
 * untuk records 2026+. Penerbit murni pra-2026 akan tampil "-" di kolom tsb.
 */
class ComplianceV3Controller extends Controller
{
    use OracleHelper;

    private const PER_PAGE = 25;
    private const CUTOFF   = '2026-01-01';

    private function loadSettings(): array
    {
        return ComplianceSettings::get();
    }

    private function exprDeadlineTerbit(array $s): string
    {
        $kc = (int) $s['BatasWaktuKonfirmasiTerbitKaryaCetak'];
        $kr = (int) $s['BatasWaktuKonfirmasiTerbitDigital'];
        return "CASE WHEN PT.JENIS_MEDIA = '1' THEN (PI.CREATEDATE + $kc) ELSE (PI.CREATEDATE + $kr) END";
    }

    private function exprDeadlineKckrV1(): string
    {
        return "CASE WHEN P.KATEGORI_ID = 1 THEN ADD_MONTHS(PI.CREATEDATE, 3)
                     ELSE CASE WHEN PT.JENIS_MEDIA = '1' THEN ADD_MONTHS(PI.CREATEDATE, 3)
                               ELSE ADD_MONTHS(PI.CREATEDATE, 12) END END";
    }

    private function exprDeadlineKckrV2(): string
    {
        return "CASE WHEN P.KATEGORI_ID = 1 THEN ADD_MONTHS(PI.TANGGAL_TERBIT, 3)
                     ELSE CASE WHEN PT.JENIS_MEDIA = '1' THEN ADD_MONTHS(PI.TANGGAL_TERBIT, 3)
                               ELSE ADD_MONTHS(PI.TANGGAL_TERBIT, 12) END END";
    }

    private function buildDateWhere(string $start, string $end): string
    {
        return "AND PI.CREATEDATE >= TO_DATE('$start', 'YYYY-MM-DD')
                AND PI.CREATEDATE <  TO_DATE('$end',   'YYYY-MM-DD')";
    }

    private function parsePersentaseRange(string $persentase): array
    {
        return match($persentase) {
            '0-20'   => [0, 20],
            '21-40'  => [21, 40],
            '41-60'  => [41, 60],
            '61-80'  => [61, 80],
            '81-100' => [81, 100],
            default  => [0, 100],
        };
    }

    private function buildBaseQuery(
        string $dateWhere,
        string $provinceWhere,
        ?string $kategori,
        ?string $search,
        ?string $filterHutang,
        ?string $filterTeguran,
        ?string $filterKckr,
        ?string $persentase = null,
        ?string $filterRekomendasi = null,
        string $kckrCol = 'RECEIVED_DATE_KCKR'
    ): string {
        $cutoff = self::CUTOFF;

        $kategoriWhere = !empty($kategori) ? "AND P.KATEGORI_ID = " . intval($kategori) : '';
        $searchWhere   = !empty($search)
            ? "AND UPPER(P.NAME) LIKE '%" . strtoupper(addslashes($search)) . "%'"
            : '';

        $settings = $this->loadSettings();
        $dlTerbit = $this->exprDeadlineTerbit($settings);
        $teguran  = (int) $settings['BatasWaktuTeguranKonfirmasiTerbit'];
        $minPct   = (int) $settings['BatasMinimumKepatuhanKCKR'];
        $dlKckrV1 = $this->exprDeadlineKckrV1();
        $dlKckrV2 = $this->exprDeadlineKckrV2();

        // Terbit hanya berlaku untuk records 2026+
        $is2026   = "PI.CREATEDATE >= TO_DATE('$cutoff','YYYY-MM-DD')";
        $isPre26  = "PI.CREATEDATE <  TO_DATE('$cutoff','YYYY-MM-DD')";

        $sudahTerbit = "(PI.TANGGAL_TERBIT IS NOT NULL OR PI.RECEIVED_DATE_KCKR IS NOT NULL)";
        $belumTerbit = "(PI.TANGGAL_TERBIT IS NULL AND PI.RECEIVED_DATE_KCKR IS NULL)";

        // Tagihan = masih tertagih = belum serahkan KCKR
        //   pra-2026: semua yg BELUM setor (tanpa syarat terbit)
        //   2026+   : sudah konfirmasi terbit tapi BELUM setor KCKR
        $tagihan = "SUM(CASE
            WHEN $isPre26 AND PI.RECEIVED_DATE_KCKR IS NULL THEN 1
            WHEN $is2026  AND PI.TANGGAL_TERBIT IS NOT NULL AND PI.RECEIVED_DATE_KCKR IS NULL THEN 1
            ELSE 0 END)";

        // Denominator persentase = total yang pernah wajib KCKR (sudah + masih belum)
        $sudahKckrExpr = "SUM(CASE WHEN PI.RECEIVED_DATE_KCKR IS NOT NULL THEN 1 ELSE 0 END)";
        $totalWajib    = "($sudahKckrExpr + $tagihan)";

        $innerQuery = "
            SELECT
                P.ID, P.NAME, P.ALAMAT, P.PROVINSI, P.CITY,
                P.TELP1, P.TELP2, P.EMAIL1, P.EMAIL2,
                P.KATEGORI_ID, P.PROVINCE_ID, P.CREATEDATE,
                CASE WHEN P.KATEGORI_ID = 1 THEN 'Pemerintah'
                     WHEN P.KATEGORI_ID = 2 THEN 'Swasta'
                     ELSE 'Lainnya' END as KATEGORI,

                -- Total judul dalam range (semua tahun)
                COUNT(PI.ID)                                                             as TOTAL_JUDUL,

                -- Berapa yang 2026+ (agar UI tahu apakah kolom terbit relevan)
                SUM(CASE WHEN $is2026 THEN 1 ELSE 0 END)                                as JUDUL_2026_PLUS,

                -- Status Terbit: hanya dari records 2026+
                SUM(CASE WHEN $is2026 AND $sudahTerbit THEN 1 ELSE 0 END)               as JUDUL_TERBIT,
                SUM(CASE WHEN $is2026 AND $belumTerbit THEN 1 ELSE 0 END)               as JUDUL_BELUM_TERBIT,
                SUM(CASE WHEN $is2026 AND $belumTerbit AND SYSDATE > $dlTerbit
                         THEN 1 ELSE 0 END)                                              as HUTANG_TERBIT,
                SUM(CASE WHEN $is2026 AND $belumTerbit AND SYSDATE > ($dlTerbit + $teguran)
                         THEN 1 ELSE 0 END)                                              as LEWAT_TEGURAN,

                -- KCKR: dari semua records (pra-2026 dan 2026+)
                SUM(CASE WHEN PI.RECEIVED_DATE_KCKR IS NOT NULL
                         THEN 1 ELSE 0 END)                                              as SUDAH_KCKR,
                SUM(CASE WHEN PI.RECEIVED_DATE_KCKR IS NOT NULL AND PT.JENIS_MEDIA = '1'
                         THEN 1 ELSE 0 END)                                              as SUDAH_KCKR_CETAK,
                SUM(CASE WHEN PI.RECEIVED_DATE_KCKR IS NOT NULL
                          AND (PT.JENIS_MEDIA != '1' OR PT.JENIS_MEDIA IS NULL)
                         THEN 1 ELSE 0 END)                                              as SUDAH_KCKR_REKAM,

                -- Belum KCKR:
                --   pra-2026 : semua yg belum setor KCKR (tanpa syarat terbit)
                --   2026+    : sudah konfirmasi terbit tapi belum KCKR
                SUM(CASE
                    WHEN $isPre26 AND PI.RECEIVED_DATE_KCKR IS NULL THEN 1
                    WHEN $is2026  AND PI.TANGGAL_TERBIT IS NOT NULL AND PI.RECEIVED_DATE_KCKR IS NULL THEN 1
                    ELSE 0 END)                                                          as BELUM_KCKR,
                SUM(CASE
                    WHEN $isPre26 AND PI.RECEIVED_DATE_KCKR IS NULL AND PT.JENIS_MEDIA = '1' THEN 1
                    WHEN $is2026  AND PI.TANGGAL_TERBIT IS NOT NULL AND PI.RECEIVED_DATE_KCKR IS NULL AND PT.JENIS_MEDIA = '1' THEN 1
                    ELSE 0 END)                                                          as BELUM_KCKR_CETAK,
                SUM(CASE
                    WHEN $isPre26 AND PI.RECEIVED_DATE_KCKR IS NULL AND (PT.JENIS_MEDIA != '1' OR PT.JENIS_MEDIA IS NULL) THEN 1
                    WHEN $is2026  AND PI.TANGGAL_TERBIT IS NOT NULL AND PI.RECEIVED_DATE_KCKR IS NULL AND (PT.JENIS_MEDIA != '1' OR PT.JENIS_MEDIA IS NULL) THEN 1
                    ELSE 0 END)                                                          as BELUM_KCKR_REKAM,

                -- Terlambat KCKR: formula hybrid
                SUM(CASE
                    WHEN $isPre26 AND (
                        (PI.RECEIVED_DATE_KCKR IS NOT NULL AND PI.RECEIVED_DATE_KCKR > ($dlKckrV1))
                     OR (PI.RECEIVED_DATE_KCKR IS NULL    AND SYSDATE > ($dlKckrV1))
                    ) THEN 1
                    WHEN $is2026 AND PI.TANGGAL_TERBIT IS NOT NULL AND (
                        (PI.RECEIVED_DATE_KCKR IS NOT NULL AND PI.RECEIVED_DATE_KCKR > ($dlKckrV2))
                     OR (PI.RECEIVED_DATE_KCKR IS NULL    AND SYSDATE > ($dlKckrV2))
                    ) THEN 1
                    ELSE 0
                END)                                                                     as TERLAMBAT_KCKR,

                -- % KCKR = sudah / (sudah + tagihan) — tagihan = masih belum setor
                ROUND(
                    CASE WHEN $totalWajib > 0
                        THEN $sudahKckrExpr / $totalWajib * 100
                        ELSE 0 END, 1
                )                                                                        as PERSENTASE_KCKR

            FROM PENERBIT P
            LEFT JOIN PENERBIT_ISBN PI ON P.ID = PI.PENERBIT_ID
                $dateWhere
                AND (PI.KETERANGAN IS NULL OR UPPER(PI.KETERANGAN) NOT LIKE '%LENGKAP%')
            LEFT JOIN PENERBIT_TERBITAN PT ON PI.PENERBIT_TERBITAN_ID = PT.ID
            WHERE 1=1
                $provinceWhere
                $kategoriWhere
                $searchWhere
            GROUP BY P.ID, P.NAME, P.ALAMAT, P.PROVINSI, P.CITY,
                     P.TELP1, P.TELP2, P.EMAIL1, P.EMAIL2,
                     P.KATEGORI_ID, P.PROVINCE_ID, P.CREATEDATE
            HAVING COUNT(PI.ID) > 0
        ";

        $outerWhere = '';
        if ($filterHutang === 'ya')     $outerWhere .= ' AND HUTANG_TERBIT > 0';
        if ($filterHutang === 'tidak')  $outerWhere .= ' AND HUTANG_TERBIT = 0';
        if ($filterTeguran === 'ya')    $outerWhere .= ' AND LEWAT_TEGURAN > 0';
        if ($filterTeguran === 'tidak') $outerWhere .= ' AND LEWAT_TEGURAN = 0';
        if ($filterKckr === 'sudah')    $outerWhere .= ' AND SUDAH_KCKR > 0';
        if ($filterKckr === 'belum')    $outerWhere .= ' AND BELUM_KCKR > 0';

        // Prioritas: Blokir KCKR > Blokir Terbit > Baik
        if ($filterRekomendasi === 'blokir_kckr')   $outerWhere .= " AND TERLAMBAT_KCKR > 0 AND PERSENTASE_KCKR <= $minPct";
        if ($filterRekomendasi === 'blokir_terbit') $outerWhere .= " AND LEWAT_TEGURAN > 0 AND NOT (TERLAMBAT_KCKR > 0 AND PERSENTASE_KCKR <= $minPct)";
        if ($filterRekomendasi === 'baik')           $outerWhere .= " AND LEWAT_TEGURAN = 0 AND (TERLAMBAT_KCKR = 0 OR PERSENTASE_KCKR > $minPct)";

        if (!empty($persentase)) {
            [$min, $max] = $this->parsePersentaseRange($persentase);
            $outerWhere .= " AND PERSENTASE_KCKR BETWEEN $min AND $max";
        }

        $query = $outerWhere
            ? "SELECT * FROM ($innerQuery) WHERE 1=1 $outerWhere"
            : $innerQuery;

        return str_replace('RECEIVED_DATE_KCKR', $kckrCol, $query);
    }

    private function makeCacheKeyV3(Request $request, string $prefix): string
    {
        $v = ComplianceSettings::cacheVersion();
        return $this->makeCacheKey($request, "{$prefix}_{$v}", [
            'filter_type', 'filter_year', 'filter_month', 'start_date', 'end_date',
            'province_ids', 'kategori', 'search',
            'filter_hutang', 'filter_teguran', 'filter_kckr', 'persentase', 'filter_rekomendasi',
            'kckr_mode',
        ]);
    }

    // ─── Actions ─────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        try {
            $ctx         = $this->resolveProvinceContext($request->province_ids ?? [], $request->get('kckr_mode', 'perpusnas'));
            $isPerpusnas = $ctx['isPerpusnas'];
            $kckrMode    = $ctx['kckrMode'];

            $conn      = $this->getOracleConnection();
            $provinces = $isPerpusnas
                ? array_map(
                    fn($r) => (object) $r,
                    Cache::remember('compliance_v3:provinces', 3600, fn() =>
                        array_map(fn($r) => (array) $r, $this->fetchProvinces($conn))
                    )
                )
                : [];

            $userProvinceName = !$isPerpusnas ? (session('province_name') ?? '') : null;
            $provinceIds      = $ctx['provinceIds'];
            $minPct           = $this->loadSettings()['BatasMinimumKepatuhanKCKR'];

            return view('compliance_v3.index', compact('provinces', 'isPerpusnas', 'kckrMode', 'userProvinceName', 'provinceIds', 'minPct'));
        } catch (\Exception $e) {
            return view('compliance_v3.index', [
                'error' => 'Error: ' . $e->getMessage(),
                'provinces' => [],
                'isPerpusnas' => true,
                'kckrMode' => 'perpusnas',
                'userProvinceName' => null,
            ]);
        }
    }

    public function data(Request $request)
    {
        try {
            $conn              = $this->getOracleConnection();
            $page              = max(1, (int) $request->get('page', 1));
            $kategori          = $request->kategori           ?? null;
            $search            = trim($request->search        ?? '');
            $filterHutang      = $request->filter_hutang      ?? null;
            $filterTeguran     = $request->filter_teguran     ?? null;
            $filterKckr        = $request->filter_kckr        ?? null;
            $persentase        = $request->persentase         ?? null;
            $filterRekomendasi = $request->filter_rekomendasi ?? null;
            $sortCol           = $request->sort_col           ?? 'NAME';
            $sortDir           = $request->sort_dir           ?? 'ASC';

            $ctx           = $this->resolveProvinceContext($request->province_ids ?? [], $request->get('kckr_mode', 'perpusnas'));
            $provinceIds   = $ctx['provinceIds'];
            $kckrCol       = $ctx['kckrCol'];

            $dateFilter    = $this->parseDateFilter($request);
            $dateWhere     = $this->buildDateWhere($dateFilter['start'], $dateFilter['end']);
            $provinceWhere = $this->buildProvinceWhere($provinceIds);

            $cacheKey = $this->makeCacheKeyV3($request, 'compliance_v3:data')
                . ':' . $page . ':' . strtolower($sortCol) . ':' . strtolower($sortDir);

            $cached = Cache::remember($cacheKey, 3600, function() use (
                $conn, $dateWhere, $provinceWhere, $kategori, $search,
                $filterHutang, $filterTeguran, $filterKckr, $persentase, $filterRekomendasi,
                $page, $sortCol, $sortDir, $kckrCol
            ) {
                $baseQuery = $this->buildBaseQuery(
                    $dateWhere, $provinceWhere, $kategori, $search,
                    $filterHutang, $filterTeguran, $filterKckr, $persentase, $filterRekomendasi, $kckrCol
                );

                $allowed = ['NAME','TOTAL_JUDUL','JUDUL_TERBIT','HUTANG_TERBIT',
                            'LEWAT_TEGURAN','SUDAH_KCKR','BELUM_KCKR','PERSENTASE_KCKR','TERLAMBAT_KCKR'];
                $sortCol = in_array(strtoupper($sortCol), $allowed) ? strtoupper($sortCol) : 'NAME';
                $sortDir = strtoupper($sortDir) === 'DESC' ? 'DESC' : 'ASC';

                $perPage = self::PER_PAGE;
                $offset  = ($page - 1) * $perPage;
                $end     = $offset + $perPage;

                $countRes = odbc_exec($conn, "SELECT COUNT(*) as TOTAL FROM ($baseQuery)");
                $total    = (int) odbc_result($countRes, 'TOTAL');

                $aggRes = odbc_exec($conn, "
                    SELECT
                        SUM(TOTAL_JUDUL)        as SUM_JUDUL,
                        SUM(JUDUL_TERBIT)       as SUM_TERBIT,
                        SUM(JUDUL_BELUM_TERBIT) as SUM_BELUM_TERBIT,
                        SUM(HUTANG_TERBIT)      as SUM_HUTANG,
                        SUM(LEWAT_TEGURAN)      as SUM_TEGURAN,
                        SUM(SUDAH_KCKR)         as SUM_SUDAH_KCKR,
                        SUM(SUDAH_KCKR_CETAK)   as SUM_SUDAH_KCKR_CETAK,
                        SUM(SUDAH_KCKR_REKAM)   as SUM_SUDAH_KCKR_REKAM,
                        SUM(BELUM_KCKR)         as SUM_BELUM_KCKR,
                        SUM(BELUM_KCKR_CETAK)   as SUM_BELUM_KCKR_CETAK,
                        SUM(BELUM_KCKR_REKAM)   as SUM_BELUM_KCKR_REKAM,
                        ROUND(AVG(PERSENTASE_KCKR), 1) as AVG_PCT
                    FROM ($baseQuery)
                ");
                $agg = (array) odbc_fetch_object($aggRes);

                $sql = "
                    SELECT * FROM (
                        SELECT a.*, ROWNUM as RN FROM (
                            $baseQuery ORDER BY $sortCol $sortDir
                        ) a WHERE ROWNUM <= $end
                    ) WHERE RN > $offset
                ";

                $result = odbc_exec($conn, $sql);
                $data   = [];
                while ($row = odbc_fetch_object($result)) {
                    $data[] = (array) $row;
                }

                return [
                    'data'         => $data,
                    'total'        => $total,
                    'current_page' => $page,
                    'last_page'    => max(1, (int) ceil($total / $perPage)),
                    'per_page'     => $perPage,
                    'agg'          => $agg,
                ];
            });

            return response()->json([
                'data'         => array_map(fn($r) => (object) $r, $cached['data']),
                'total'        => $cached['total'],
                'current_page' => $cached['current_page'],
                'last_page'    => $cached['last_page'],
                'per_page'     => $cached['per_page'],
                'agg'          => $cached['agg'],
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function detail(Request $request, $id)
    {
        try {
            $ctx        = $this->resolveProvinceContext([], $request->get('kckr_mode', 'perpusnas'));
            $kckrCol    = $ctx['kckrCol'];
            $kckrMode   = $ctx['kckrMode'];

            $conn       = $this->getOracleConnection();
            $penerbitId = (int) $id;
            $page       = max(1, (int) $request->get('page', 1));
            $perPage    = self::PER_PAGE;
            $dateFilter = $this->parseDateFilter($request);
            $dateWhere  = $this->buildDateWhere($dateFilter['start'], $dateFilter['end']);
            $cutoff     = self::CUTOFF;

            $penerbit = (object) Cache::remember("compliance_v3:penerbit:$penerbitId", 3600, function() use ($conn, $penerbitId) {
                $r = odbc_fetch_object(odbc_exec($conn, "
                    SELECT P.ID, P.NAME, P.ALAMAT, P.PROVINSI, P.CITY, P.KODEPOS, P.KATEGORI_ID,
                           P.KONTAK1, P.TELP1, P.FAX1, P.EMAIL1,
                           P.KONTAK2, P.TELP2, P.FAX2, P.EMAIL2,
                           P.WEBSITE, P.NOSIUP
                    FROM PENERBIT P WHERE P.ID = $penerbitId
                "));
                return $r ? (array) $r : null;
            });
            if (!$penerbit || !isset($penerbit->ID)) abort(404, 'Penerbit tidak ditemukan');

            $filterStatus    = $request->filter_status    ?? '';
            $filterJenis     = $request->filter_jenis     ?? '';
            $filterHutang    = $request->filter_hutang    ?? '';
            $filterTeguran   = $request->filter_teguran   ?? '';
            $filterTerlambat = $request->filter_terlambat ?? '';
            $searchJudul     = trim($request->search_judul ?? '');
            $searchIsbn      = str_replace('-','',trim($request->search_isbn))  ?? '';

            $settings = $this->loadSettings();
            $dlTerbit = $this->exprDeadlineTerbit($settings);
            $teguran  = (int) $settings['BatasWaktuTeguranKonfirmasiTerbit'];
            $dlKckrV1 = $this->exprDeadlineKckrV1();
            $dlKckrV2 = $this->exprDeadlineKckrV2();

            $is2026  = "PI.CREATEDATE >= TO_DATE('$cutoff','YYYY-MM-DD')";
            $isPre26 = "PI.CREATEDATE <  TO_DATE('$cutoff','YYYY-MM-DD')";

            $fromJoin = "
                FROM PENERBIT P
                JOIN PENERBIT_ISBN PI ON P.ID = PI.PENERBIT_ID
                    $dateWhere
                    AND (PI.KETERANGAN IS NULL OR UPPER(PI.KETERANGAN) NOT LIKE '%LENGKAP%')
                LEFT JOIN PENERBIT_TERBITAN PT ON PI.PENERBIT_TERBITAN_ID = PT.ID
                WHERE P.ID = $penerbitId
            ";

            $searchWhere = '';
            if ($searchJudul) $searchWhere .= " AND UPPER(PT.TITLE) LIKE '%" . strtoupper(addslashes($searchJudul)) . "%'";
            if ($searchIsbn)  $searchWhere .= " AND UPPER(PI.ISBN_NO) LIKE '%" . strtoupper(addslashes($searchIsbn)) . "%'";
            if ($filterJenis === 'cetak') $searchWhere .= " AND PT.JENIS_MEDIA = '1'";
            if ($filterJenis === 'rekam') $searchWhere .= " AND (PT.JENIS_MEDIA != '1' OR PT.JENIS_MEDIA IS NULL)";

            // filter status terbit: hanya 2026+ records punya status terbit
            $kc = $kckrCol; // alias pendek untuk readability
            if ($filterStatus === 'terbit')       $searchWhere .= " AND $is2026 AND (PI.TANGGAL_TERBIT IS NOT NULL OR PI.$kc IS NOT NULL)";
            if ($filterStatus === 'belum_terbit') $searchWhere .= " AND $is2026 AND PI.TANGGAL_TERBIT IS NULL AND PI.$kc IS NULL";
            if ($filterStatus === 'sudah_kckr')   $searchWhere .= " AND PI.$kc IS NOT NULL";
            if ($filterStatus === 'belum_kckr')   $searchWhere .= " AND PI.$kc IS NULL";
            if ($filterHutang  === 'ya')    $searchWhere .= " AND $is2026 AND PI.TANGGAL_TERBIT IS NULL AND PI.$kc IS NULL AND SYSDATE > $dlTerbit";
            if ($filterHutang  === 'tidak') $searchWhere .= " AND $is2026 AND NOT (PI.TANGGAL_TERBIT IS NULL AND PI.$kc IS NULL AND SYSDATE > $dlTerbit)";
            if ($filterTeguran === 'ya')    $searchWhere .= " AND $is2026 AND PI.TANGGAL_TERBIT IS NULL AND PI.$kc IS NULL AND SYSDATE > ($dlTerbit + $teguran)";
            if ($filterTeguran === 'tidak') $searchWhere .= " AND $is2026 AND NOT (PI.TANGGAL_TERBIT IS NULL AND PI.$kc IS NULL AND SYSDATE > ($dlTerbit + $teguran))";
            if ($filterTerlambat === 'ya') {
                $searchWhere .= " AND (
                    ($isPre26 AND ((PI.$kc IS NOT NULL AND PI.$kc > ($dlKckrV1)) OR (PI.$kc IS NULL AND SYSDATE > ($dlKckrV1))))
                    OR ($is2026 AND PI.TANGGAL_TERBIT IS NOT NULL AND ((PI.$kc IS NOT NULL AND PI.$kc > ($dlKckrV2)) OR (PI.$kc IS NULL AND SYSDATE > ($dlKckrV2))))
                )";
            }
            if ($filterTerlambat === 'tidak') {
                $searchWhere .= " AND NOT (
                    ($isPre26 AND ((PI.$kc IS NOT NULL AND PI.$kc > ($dlKckrV1)) OR (PI.$kc IS NULL AND SYSDATE > ($dlKckrV1))))
                    OR ($is2026 AND PI.TANGGAL_TERBIT IS NOT NULL AND ((PI.$kc IS NOT NULL AND PI.$kc > ($dlKckrV2)) OR (PI.$kc IS NULL AND SYSDATE > ($dlKckrV2))))
                )";
            }

            // Summary: terbit metrics hanya 2026+, KCKR semua records
            $csv        = ComplianceSettings::cacheVersion();
            $summaryKey = $this->makeCacheKey($request, "compliance_v3:detail:{$penerbitId}:summary_{$csv}", [
                'filter_type', 'filter_year', 'filter_month', 'start_date', 'end_date', 'kckr_mode',
            ]);
            $summary = (object) Cache::remember($summaryKey, 3600, function() use ($conn, $fromJoin, $dlTerbit, $teguran, $dlKckrV1, $dlKckrV2, $is2026, $isPre26, $kckrCol) {
                $sudahTerbit = "(PI.TANGGAL_TERBIT IS NOT NULL OR PI.RECEIVED_DATE_KCKR IS NOT NULL)";
                $sql = "
                    SELECT
                        COUNT(*) as TOTAL,
                        COUNT(CASE WHEN $is2026 AND $sudahTerbit THEN 1 END) as SUDAH_TERBIT,
                        COUNT(CASE WHEN $is2026 AND PI.TANGGAL_TERBIT IS NULL AND PI.RECEIVED_DATE_KCKR IS NULL THEN 1 END) as BELUM_TERBIT,
                        COUNT(CASE WHEN $is2026 AND PI.TANGGAL_TERBIT IS NULL AND PI.RECEIVED_DATE_KCKR IS NULL AND SYSDATE > $dlTerbit THEN 1 END) as HUTANG_TERBIT,
                        COUNT(CASE WHEN $is2026 AND PI.TANGGAL_TERBIT IS NULL AND PI.RECEIVED_DATE_KCKR IS NULL AND SYSDATE > ($dlTerbit + $teguran) THEN 1 END) as LEWAT_TEGURAN,
                        COUNT(CASE WHEN PI.RECEIVED_DATE_KCKR IS NOT NULL THEN 1 END) as SUDAH_KCKR,
                        COUNT(CASE WHEN ($isPre26 AND PI.RECEIVED_DATE_KCKR IS NULL)
                                     OR ($is2026 AND PI.TANGGAL_TERBIT IS NOT NULL AND PI.RECEIVED_DATE_KCKR IS NULL)
                                   THEN 1 END) as BELUM_KCKR,
                        COUNT(CASE WHEN
                            ($isPre26 AND ((PI.RECEIVED_DATE_KCKR IS NOT NULL AND PI.RECEIVED_DATE_KCKR > ($dlKckrV1)) OR (PI.RECEIVED_DATE_KCKR IS NULL AND SYSDATE > ($dlKckrV1))))
                            OR ($is2026 AND PI.TANGGAL_TERBIT IS NOT NULL AND ((PI.RECEIVED_DATE_KCKR IS NOT NULL AND PI.RECEIVED_DATE_KCKR > ($dlKckrV2)) OR (PI.RECEIVED_DATE_KCKR IS NULL AND SYSDATE > ($dlKckrV2))))
                        THEN 1 END) as TERLAMBAT_KCKR,
                        ROUND(
                            CASE WHEN (
                                    COUNT(CASE WHEN PI.RECEIVED_DATE_KCKR IS NOT NULL THEN 1 END)
                                  + COUNT(CASE WHEN ($isPre26 AND PI.RECEIVED_DATE_KCKR IS NULL)
                                                 OR ($is2026 AND PI.TANGGAL_TERBIT IS NOT NULL AND PI.RECEIVED_DATE_KCKR IS NULL)
                                               THEN 1 END)
                                ) > 0
                                THEN COUNT(CASE WHEN PI.RECEIVED_DATE_KCKR IS NOT NULL THEN 1 END)
                                   / (COUNT(CASE WHEN PI.RECEIVED_DATE_KCKR IS NOT NULL THEN 1 END)
                                    + COUNT(CASE WHEN ($isPre26 AND PI.RECEIVED_DATE_KCKR IS NULL)
                                                   OR ($is2026 AND PI.TANGGAL_TERBIT IS NOT NULL AND PI.RECEIVED_DATE_KCKR IS NULL)
                                                 THEN 1 END)) * 100
                                ELSE 0 END, 1
                        ) as PERSENTASE_KCKR
                    $fromJoin
                ";
                $r = odbc_exec($conn, str_replace('RECEIVED_DATE_KCKR', $kckrCol, $sql));
                return (array) odbc_fetch_object($r);
            });

            $selectCols = "
                PI.ID, PI.ISBN_NO,
                PI.CREATEDATE      as TGL_DAFTAR,
                PI.TANGGAL_TERBIT,
                PI.RECEIVED_DATE_KCKR as TGL_KCKR,
                PI.KETERANGAN,
                PT.TITLE, PT.KEPENG, PT.JENIS_MEDIA, PT.JILID_VOLUME,
                CASE WHEN $isPre26 THEN NULL ELSE $dlTerbit END   as DEADLINE_TERBIT,
                CASE WHEN $isPre26 THEN NULL ELSE ($dlTerbit + $teguran) END as BATAS_TEGURAN,
                -- IS_PRE2026: 1 = pra-2026, 0 = 2026+
                CASE WHEN $isPre26 THEN 1 ELSE 0 END               as IS_PRE2026,
                -- Status Terbit: N/A untuk pra-2026
                CASE
                    WHEN $isPre26                                                          THEN 'N/A'
                    WHEN PI.RECEIVED_DATE_KCKR IS NOT NULL AND PI.TANGGAL_TERBIT IS NULL  THEN 'Terbit'
                    WHEN PI.TANGGAL_TERBIT IS NULL AND SYSDATE > ($dlTerbit + $teguran)         THEN 'Lewat Teguran'
                    WHEN PI.TANGGAL_TERBIT IS NULL AND SYSDATE > $dlTerbit                THEN 'Hutang Terbit'
                    WHEN PI.TANGGAL_TERBIT IS NULL                                        THEN 'Belum Terbit'
                    ELSE 'Terbit'
                END as STATUS_TERBIT,
                CASE
                    WHEN PI.RECEIVED_DATE_KCKR IS NOT NULL THEN 'Sudah'
                    WHEN $is2026 AND PI.TANGGAL_TERBIT IS NULL AND PI.RECEIVED_DATE_KCKR IS NULL THEN 'Belum Terbit'
                    ELSE 'Belum'
                END as STATUS_KCKR,
                -- Deadline KCKR: hybrid
                CASE
                    WHEN $isPre26 THEN ($dlKckrV1)
                    WHEN PI.TANGGAL_TERBIT IS NOT NULL THEN ($dlKckrV2)
                    ELSE NULL
                END as DEADLINE_KCKR,
                -- Terlambat KCKR: hybrid
                CASE
                    WHEN $isPre26 AND (
                        (PI.RECEIVED_DATE_KCKR IS NOT NULL AND PI.RECEIVED_DATE_KCKR > ($dlKckrV1))
                     OR (PI.RECEIVED_DATE_KCKR IS NULL AND SYSDATE > ($dlKckrV1))
                    ) THEN 'Ya'
                    WHEN $is2026 AND PI.TANGGAL_TERBIT IS NOT NULL AND (
                        (PI.RECEIVED_DATE_KCKR IS NOT NULL AND PI.RECEIVED_DATE_KCKR > ($dlKckrV2))
                     OR (PI.RECEIVED_DATE_KCKR IS NULL AND SYSDATE > ($dlKckrV2))
                    ) THEN 'Ya'
                    ELSE 'Tidak'
                END as TERLAMBAT_KCKR
            ";

            $pageKey = $this->makeCacheKey($request, "compliance_v3:detail:{$penerbitId}:page_{$csv}", [
                'filter_type', 'filter_year', 'filter_month', 'start_date', 'end_date',
                'filter_status', 'filter_jenis', 'filter_hutang', 'filter_teguran', 'filter_terlambat',
                'search_judul', 'search_isbn', 'kckr_mode',
            ]) . ':' . $page;

            // TTL lebih pendek untuk filter aktif agar hasil tidak stale
            $hasActiveFilter = $filterStatus || $filterJenis || $filterHutang || $filterTeguran || $filterTerlambat || $searchJudul || $searchIsbn;
            $pageTtl = $hasActiveFilter ? 120 : 3600;

            $cached = Cache::remember($pageKey, $pageTtl, function() use ($conn, $fromJoin, $searchWhere, $selectCols, $page, $perPage, $kckrCol) {
                $countSql  = str_replace('RECEIVED_DATE_KCKR', $kckrCol, "SELECT COUNT(*) as TOTAL $fromJoin $searchWhere");
                $countRes  = odbc_exec($conn, $countSql);
                if (!$countRes) throw new \RuntimeException('Count query error: ' . odbc_errormsg($conn));
                $total    = (int) (odbc_fetch_object($countRes)->TOTAL ?? 0);
                $lastPage = max(1, (int) ceil($total / $perPage));
                $page     = min($page, $lastPage);
                $offset   = ($page - 1) * $perPage;
                $end      = $offset + $perPage;

                $sql = str_replace('RECEIVED_DATE_KCKR', $kckrCol, "
                    SELECT * FROM (
                        SELECT a.*, ROWNUM as RN FROM (
                            SELECT $selectCols $fromJoin $searchWhere ORDER BY TGL_DAFTAR DESC
                        ) a WHERE ROWNUM <= $end
                    ) WHERE RN > $offset
                ");
                $result = odbc_exec($conn, $sql);
                if (!$result) throw new \RuntimeException('Page query error: ' . odbc_errormsg($conn));
                $titles = [];
                while ($row = odbc_fetch_object($result)) {
                    $titles[] = (array) $row;
                }
                return compact('titles', 'total', 'lastPage', 'page');
            });

            $titles   = array_map(fn($r) => (object) $r, $cached['titles']);
            $total    = $cached['total'];
            $lastPage = $cached['lastPage'];
            $page     = $cached['page'];

            $kategoriLabel = match((int)$penerbit->KATEGORI_ID) {
                1 => 'Pemerintah', 2 => 'Swasta', default => 'Lainnya'
            };

            $filters = compact(
                'searchJudul', 'searchIsbn', 'filterStatus',
                'filterJenis', 'filterHutang', 'filterTeguran', 'filterTerlambat'
            );

            $minPct = (int) $settings['BatasMinimumKepatuhanKCKR'];

            return view('compliance_v3.detail', compact(
                'penerbit', 'titles', 'dateFilter', 'kategoriLabel',
                'summary', 'total', 'page', 'perPage', 'lastPage', 'filters',
                'kckrMode', 'minPct', 'settings', 'teguran'
            ));

        } catch (\Exception $e) {
            abort(500, $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        try {
        $ctx           = $this->resolveProvinceContext($request->province_ids ?? [], $request->get('kckr_mode', 'perpusnas'));
        $provinceIds   = $ctx['provinceIds'];
        $kckrCol       = $ctx['kckrCol'];
        $kckrMode      = $ctx['kckrMode'];

        $conn          = $this->getOracleConnection();
        $kategori      = $request->kategori      ?? null;
        $search        = trim($request->search   ?? '');
        $filterHutang  = $request->filter_hutang  ?? null;
        $filterTeguran = $request->filter_teguran ?? null;
        $filterKckr    = $request->filter_kckr    ?? null;
        $persentase    = $request->persentase     ?? null;

        $dateFilter    = $this->parseDateFilter($request);
        $dateWhere     = $this->buildDateWhere($dateFilter['start'], $dateFilter['end']);
        $provinceWhere = $this->buildProvinceWhere($provinceIds);
        $baseQuery     = $this->buildBaseQuery(
            $dateWhere, $provinceWhere, $kategori, $search,
            $filterHutang, $filterTeguran, $filterKckr, $persentase, null, $kckrCol
        );

        $label    = $this->buildFilterLabel($request, $provinceIds);
        $periode  = str_replace(['/', ' ', '–', '-'], ['', '_', '-', '_'], $label['periode']);

        $judulExport = $kckrMode === 'provinsi'
            ? 'LAPORAN KEPATUHAN PENERBIT KCKR — DATA PROVINSI'
            : 'LAPORAN KEPATUHAN PENERBIT KCKR — DATA PERPUSNAS';
        if (!$ctx['isPerpusnas']) {
            $provName    = session('province_name') ?? 'Provinsi';
            $judulExport = "LAPORAN KEPATUHAN PENERBIT KCKR — " . strtoupper($provName);
        }

        $filename = 'ComplianceV3_' . ($kckrMode === 'provinsi' ? 'Provinsi_' : '') . $periode . '_' . date('d-m-Y') . '.xlsx';

        $exportKey = $this->makeCacheKeyV3($request, 'compliance_v3:export');

        $minPct  = (int) $this->loadSettings()['BatasMinimumKepatuhanKCKR'];
        $rows = Cache::remember($exportKey, 3600, function() use ($conn, $baseQuery, $minPct) {
            $result = odbc_exec($conn, "SELECT * FROM ($baseQuery) ORDER BY NAME ASC");
            $data   = [];
            while ($row = odbc_fetch_object($result)) {
                $lewat      = (int) $row->LEWAT_TEGURAN;
                $terlambat  = (int) $row->TERLAMBAT_KCKR;
                $pct        = (float) $row->PERSENTASE_KCKR;
                $jml2026    = (int) $row->JUDUL_2026_PLUS;

                $rekomendasi = ($terlambat > 0 && $pct <= $minPct)
                    ? 'Blokir SS KCKR'
                    : ($lewat > 0 ? 'Blokir Konfirmasi Terbit' : 'Baik');

                $data[] = [
                    $row->NAME,
                    $row->KATEGORI,
                    $row->CITY,
                    $row->PROVINSI,
                    $row->TELP1  ?? '',
                    $row->TELP2  ?? '',
                    $row->EMAIL1 ?? '',
                    $row->EMAIL2 ?? '',
                    (int) $row->TOTAL_JUDUL,
                    // Kolom terbit: kosong jika tidak ada data 2026+
                    $jml2026 > 0 ? (int) $row->JUDUL_TERBIT       : '-',
                    $jml2026 > 0 ? (int) $row->JUDUL_BELUM_TERBIT : '-',
                    $jml2026 > 0 ? (int) $row->HUTANG_TERBIT      : '-',
                    $jml2026 > 0 ? $lewat                          : '-',
                    (int) $row->SUDAH_KCKR,
                    (int) $row->SUDAH_KCKR_CETAK,
                    (int) $row->SUDAH_KCKR_REKAM,
                    (int) $row->BELUM_KCKR,
                    (int) $row->BELUM_KCKR_CETAK,
                    (int) $row->BELUM_KCKR_REKAM,
                    $terlambat,
                    $pct,
                    $rekomendasi,
                ];
            }
            return $data;
        });

        $i  = 1;
        $sp = $this->makeSpreadsheetV3(function($add) use ($rows, &$i) {
            foreach ($rows as $r) {
                $add(array_merge([$i++], $r));
            }
        }, 'Ringkasan', $judulExport, $label, 23);

        // ── Sheet 2: Daftar Judul (opsional) ──────────────────────────────
        if ($request->boolean('with_judul')) {
            $this->addJudulSheet($sp, $conn, $baseQuery, $dateWhere, $kckrCol, $judulExport, $label);
        }

        return $this->streamXlsx($sp, $filename, $request);
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }
    }

    private function addJudulSheet(
        \PhpOffice\PhpSpreadsheet\Spreadsheet $sp,
        $conn,
        string $baseQuery,
        string $dateWhere,
        string $kckrCol,
        string $mainTitle,
        array $label
    ): void {
        $cutoff   = self::CUTOFF;
        $settings = $this->loadSettings();
        $dlTerbit = $this->exprDeadlineTerbit($settings);
        $teguran  = (int) $settings['BatasWaktuTeguranKonfirmasiTerbit'];
        $dlKckrV1 = $this->exprDeadlineKckrV1();
        $dlKckrV2 = $this->exprDeadlineKckrV2();
        $is2026   = "PI.CREATEDATE >= TO_DATE('$cutoff','YYYY-MM-DD')";
        $isPre26  = "PI.CREATEDATE <  TO_DATE('$cutoff','YYYY-MM-DD')";

        $sql = str_replace('RECEIVED_DATE_KCKR', $kckrCol, "
            SELECT
                P.NAME                                   as NAMA_PENERBIT,
                P.CITY,
                P.TELP1, P.TELP2, P.EMAIL1, P.EMAIL2,
                PI.ISBN_NO,
                PT.TITLE,
                PT.KEPENG,
                PI.KETERANGAN                            as JILID_VOLUME,
                PT.JENIS_MEDIA,
                PI.CREATEDATE                            as TGL_DAFTAR,
                PI.TANGGAL_TERBIT,
                CASE WHEN $isPre26 THEN NULL ELSE $dlTerbit END as DEADLINE_TERBIT,
                CASE
                    WHEN $isPre26 THEN 'N/A (Pra-2026)'
                    WHEN PI.TANGGAL_TERBIT IS NULL AND SYSDATE > ($dlTerbit + $teguran) THEN 'Lewat Teguran'
                    WHEN PI.TANGGAL_TERBIT IS NULL AND SYSDATE > $dlTerbit        THEN 'Hutang Terbit'
                    WHEN PI.TANGGAL_TERBIT IS NULL                                THEN 'Belum Terbit'
                    ELSE 'Terbit'
                END                                      as STATUS_TERBIT,
                CASE
                    WHEN $isPre26 THEN ($dlKckrV1)
                    WHEN PI.TANGGAL_TERBIT IS NOT NULL   THEN ($dlKckrV2)
                    ELSE NULL
                END                                      as DEADLINE_KCKR,
                PI.RECEIVED_DATE_KCKR                    as TGL_KCKR,
                CASE
                    WHEN PI.RECEIVED_DATE_KCKR IS NOT NULL THEN 'Sudah'
                    WHEN $is2026 AND PI.TANGGAL_TERBIT IS NULL AND PI.RECEIVED_DATE_KCKR IS NULL THEN 'Belum Terbit'
                    ELSE 'Belum'
                END                                      as STATUS_KCKR,
                CASE
                    WHEN $isPre26 AND (
                        (PI.RECEIVED_DATE_KCKR IS NOT NULL AND PI.RECEIVED_DATE_KCKR > ($dlKckrV1))
                     OR (PI.RECEIVED_DATE_KCKR IS NULL    AND SYSDATE > ($dlKckrV1))
                    ) THEN 'Ya'
                    WHEN $is2026 AND PI.TANGGAL_TERBIT IS NOT NULL AND (
                        (PI.RECEIVED_DATE_KCKR IS NOT NULL AND PI.RECEIVED_DATE_KCKR > ($dlKckrV2))
                     OR (PI.RECEIVED_DATE_KCKR IS NULL    AND SYSDATE > ($dlKckrV2))
                    ) THEN 'Ya'
                    ELSE 'Tidak'
                END                                      as TERLAMBAT_KCKR
            FROM PENERBIT P
            JOIN PENERBIT_ISBN PI ON P.ID = PI.PENERBIT_ID
                $dateWhere
                AND (PI.KETERANGAN IS NULL OR UPPER(PI.KETERANGAN) NOT LIKE '%LENGKAP%')
            LEFT JOIN PENERBIT_TERBITAN PT ON PI.PENERBIT_TERBITAN_ID = PT.ID
            WHERE P.ID IN (SELECT ID FROM ($baseQuery))
            ORDER BY P.NAME ASC, PI.CREATEDATE DESC
        ");

        $result = odbc_exec($conn, $sql);

        $Fill   = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID;
        $AlignH = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
        $AlignV = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
        $coord  = fn(int $col, int $row) =>
            \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row;

        $sheet = $sp->createSheet();
        $sheet->setTitle('Daftar Judul');

        $headers = [
            '#', 'Nama Penerbit', 'Kota', 'No. Telp 1', 'No. Telp 2', 'Email 1', 'Email 2',
            'No. ISBN', 'Judul', 'Kepengarangan', 'Jilid/Vol', 'Jenis Media',
            'Tgl Daftar', 'Tgl Terbit', 'Status Terbit',
            'Deadline KCKR', 'Tgl KCKR', 'Status KCKR', 'Terlambat',
        ];
        $colCount = count($headers);

        $r1 = $this->writeTitleRows($sheet, $mainTitle . ' — DAFTAR JUDUL', $label, $colCount);
        $hStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => $Fill, 'startColor' => ['rgb' => '1565C0']],
            'alignment' => ['horizontal' => $AlignH, 'vertical' => $AlignV, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'AAAAAA']]],
        ];
        foreach ($headers as $i => $h) {
            $sheet->getCell($coord($i + 1, $r1))->setValue($h);
            $sheet->getStyle($coord($i + 1, $r1))->applyFromArray($hStyle);
        }
        $sheet->getRowDimension($r1)->setRowHeight(22);

        // Kumpulkan semua data dulu lalu tulis sekaligus
        $allRows  = [];
        $redRows  = [];
        $no       = 1;
        while ($row = odbc_fetch_object($result)) {
            $jenis     = ($row->JENIS_MEDIA === '1') ? 'Karya Cetak' : 'Karya Rekam';
            $terlambat = $row->TERLAMBAT_KCKR ?? 'Tidak';
            if ($terlambat === 'Ya') $redRows[] = count($allRows);
            $allRows[] = [
                $no++,
                $row->NAMA_PENERBIT ?? '',
                $row->CITY          ?? '',
                $row->TELP1         ?? '',
                $row->TELP2         ?? '',
                $row->EMAIL1        ?? '',
                $row->EMAIL2        ?? '',
                $row->ISBN_NO       ?? '',
                $row->TITLE         ?? '',
                $row->KEPENG        ?? '',
                $row->JILID_VOLUME  ?? '',
                $jenis,
                $row->TGL_DAFTAR      ? date('d/m/Y', strtotime($row->TGL_DAFTAR))      : '',
                $row->TANGGAL_TERBIT  ? date('d/m/Y', strtotime($row->TANGGAL_TERBIT))  : '',
                $row->STATUS_TERBIT   ?? '',
                $row->DEADLINE_KCKR   ? date('d/m/Y', strtotime($row->DEADLINE_KCKR))   : '',
                $row->TGL_KCKR        ? date('d/m/Y', strtotime($row->TGL_KCKR))        : '',
                $row->STATUS_KCKR     ?? '',
                $terlambat,
            ];
        }

        $dataStart = $r1 + 1;
        $rowNum    = $dataStart + count($allRows);

        if ($allRows) {
            // Tulis semua data sekaligus
            $sheet->fromArray($allRows, null, 'A' . $dataStart);

            $lastDataRow = $dataStart + count($allRows) - 1;

            // Border + alignment seluruh area (1 pemanggilan)
            $sheet->getStyle("A{$dataStart}:S{$lastDataRow}")->applyFromArray([
                'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]],
                'alignment' => ['vertical' => $AlignV],
            ]);

            // Warna merah kolom Terlambat (col 19 = 'S') per baris yang perlu
            foreach ($redRows as $idx) {
                $r = $dataStart + $idx;
                $sheet->getStyle("S{$r}")->getFont()->getColor()->setRGB('C62828');
                $sheet->getStyle("S{$r}")->getFont()->setBold(true);
            }
        }

        // Lebar kolom tetap agar tidak melampaui layar
        // #, Nama Penerbit, Kota, Telp1, Telp2, Email1, Email2,
        // ISBN, Judul, Kepengarangan, Jilid, Jenis Media,
        // Tgl Daftar, Tgl Terbit, Status Terbit, Deadline KCKR, Tgl KCKR, Status KCKR, Terlambat
        $colWidths = [5, 28, 14, 16, 16, 26, 26, 16, 38, 28, 10, 14, 12, 12, 14, 14, 12, 12, 10];
        $wrapCols  = [2, 6, 7, 9, 10]; // Nama Penerbit, Email1, Email2, Judul, Kepengarangan
        foreach ($colWidths as $i => $w) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->getColumnDimension($colLetter)->setWidth($w);
            if (in_array($i + 1, $wrapCols)) {
                $sheet->getStyle($colLetter . ($r1 + 1) . ':' . $colLetter . ($rowNum - 1))
                    ->getAlignment()->setWrapText(true);
            }
        }
        $sheet->freezePane('A' . ($r1 + 1));
    }

    public function exportDetail(Request $request, $id)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        try {
        $ctx        = $this->resolveProvinceContext([], $request->get('kckr_mode', 'perpusnas'));
        $kckrCol    = $ctx['kckrCol'];
        $kckrMode   = $ctx['kckrMode'];

        $conn       = $this->getOracleConnection();
        $penerbitId = (int) $id;
        $dateFilter = $this->parseDateFilter($request);
        $dateWhere  = $this->buildDateWhere($dateFilter['start'], $dateFilter['end']);
        $cutoff     = self::CUTOFF;

        $pResult      = odbc_exec($conn, "SELECT P.NAME, P.KATEGORI_ID FROM PENERBIT P WHERE P.ID = $penerbitId");
        $penerbit     = odbc_fetch_object($pResult);
        $penerbitName = $penerbit ? $penerbit->NAME : 'Penerbit';
        $modeLabel    = $kckrMode === 'provinsi' ? '_Provinsi' : '';
        $filename     = $this->safeName($penerbitName) . '_V3' . $modeLabel . '_' . date('d-m-Y') . '.xlsx';

        $settings = $this->loadSettings();
        $dlTerbit = $this->exprDeadlineTerbit($settings);
        $teguran  = (int) $settings['BatasWaktuTeguranKonfirmasiTerbit'];
        $dlKckrV1 = $this->exprDeadlineKckrV1();
        $dlKckrV2 = $this->exprDeadlineKckrV2();

        $is2026  = "PI.CREATEDATE >= TO_DATE('$cutoff','YYYY-MM-DD')";
        $isPre26 = "PI.CREATEDATE <  TO_DATE('$cutoff','YYYY-MM-DD')";

        $csv       = ComplianceSettings::cacheVersion();
        $exportKey = $this->makeCacheKey($request, "compliance_v3:export_detail:{$penerbitId}_{$csv}", [
            'filter_type', 'filter_year', 'filter_month', 'start_date', 'end_date', 'kckr_mode',
        ]);

        $rows = Cache::remember($exportKey, 3600, function() use ($conn, $penerbitId, $dateWhere, $dlTerbit, $teguran, $dlKckrV1, $dlKckrV2, $is2026, $isPre26, $kckrCol) {
            $sql = "
                SELECT
                    PI.ISBN_NO, PT.TITLE, PT.KEPENG, PT.JILID_VOLUME, PT.JENIS_MEDIA,
                    PI.CREATEDATE as TGL_DAFTAR,
                    PI.TANGGAL_TERBIT,
                    CASE WHEN $isPre26 THEN NULL ELSE $dlTerbit END as DEADLINE_TERBIT,
                    CASE WHEN $isPre26 THEN NULL ELSE ($dlTerbit + $teguran) END as BATAS_TEGURAN,
                    CASE
                        WHEN $isPre26 THEN 'N/A (Pra-2026)'
                        WHEN PI.TANGGAL_TERBIT IS NULL AND SYSDATE > ($dlTerbit + $teguran) THEN 'Lewat Teguran'
                        WHEN PI.TANGGAL_TERBIT IS NULL AND SYSDATE > $dlTerbit        THEN 'Hutang Terbit'
                        WHEN PI.TANGGAL_TERBIT IS NULL                                THEN 'Belum Terbit'
                        ELSE 'Terbit'
                    END as STATUS_TERBIT,
                    PI.RECEIVED_DATE_KCKR,
                    CASE
                        WHEN $isPre26 THEN ($dlKckrV1)
                        WHEN PI.TANGGAL_TERBIT IS NOT NULL THEN ($dlKckrV2)
                        ELSE NULL
                    END as DEADLINE_KCKR,
                    CASE
                        WHEN PI.RECEIVED_DATE_KCKR IS NOT NULL THEN 'Sudah'
                        WHEN $is2026 AND PI.TANGGAL_TERBIT IS NULL AND PI.RECEIVED_DATE_KCKR IS NULL THEN 'Belum Terbit'
                        ELSE 'Belum'
                    END as STATUS_KCKR,
                    CASE
                        WHEN $isPre26 AND (
                            (PI.RECEIVED_DATE_KCKR IS NOT NULL AND PI.RECEIVED_DATE_KCKR > ($dlKckrV1))
                         OR (PI.RECEIVED_DATE_KCKR IS NULL AND SYSDATE > ($dlKckrV1))
                        ) THEN 'Ya'
                        WHEN $is2026 AND PI.TANGGAL_TERBIT IS NOT NULL AND (
                            (PI.RECEIVED_DATE_KCKR IS NOT NULL AND PI.RECEIVED_DATE_KCKR > ($dlKckrV2))
                         OR (PI.RECEIVED_DATE_KCKR IS NULL AND SYSDATE > ($dlKckrV2))
                        ) THEN 'Ya'
                        ELSE 'Tidak'
                    END as TERLAMBAT_KCKR,
                    PI.KETERANGAN
                FROM PENERBIT P
                JOIN PENERBIT_ISBN PI ON P.ID = PI.PENERBIT_ID
                    $dateWhere
                    AND (PI.KETERANGAN IS NULL OR UPPER(PI.KETERANGAN) NOT LIKE '%LENGKAP%')
                LEFT JOIN PENERBIT_TERBITAN PT ON PI.PENERBIT_TERBITAN_ID = PT.ID
                WHERE P.ID = $penerbitId
                ORDER BY TGL_DAFTAR DESC
            ";
            $result = odbc_exec($conn, str_replace('RECEIVED_DATE_KCKR', $kckrCol, $sql));
            $data   = [];
            $kckrColUpper = strtoupper($kckrCol);
            while ($row = odbc_fetch_object($result)) {
                $jenis       = $row->JENIS_MEDIA === '1' ? 'Karya Cetak' : 'Karya Rekam';
                $tglKckr     = $row->$kckrColUpper ?? null;
                $data[] = [
                    $row->ISBN_NO, $row->TITLE, $row->KEPENG, $row->JILID_VOLUME, $jenis,
                    $row->TGL_DAFTAR       ? date('d/m/Y', strtotime($row->TGL_DAFTAR))      : '',
                    $row->DEADLINE_TERBIT  ? date('d/m/Y', strtotime($row->DEADLINE_TERBIT)) : '-',
                    $row->TANGGAL_TERBIT   ? date('d/m/Y', strtotime($row->TANGGAL_TERBIT))  : '',
                    $row->STATUS_TERBIT,
                    $row->BATAS_TEGURAN    ? date('d/m/Y', strtotime($row->BATAS_TEGURAN))   : '-',
                    $row->DEADLINE_KCKR    ? date('d/m/Y', strtotime($row->DEADLINE_KCKR))   : '',
                    $tglKckr               ? date('d/m/Y', strtotime($tglKckr))              : '',
                    $row->STATUS_KCKR,
                    $row->TERLAMBAT_KCKR ?? '-',
                    $row->KETERANGAN ?? '',
                ];
            }
            return $data;
        });

        $headers = [
            'ISBN','Judul','Pengarang','Jilid','Jenis Media',
            'Tgl Daftar','Deadline Terbit','Tgl Terbit','Status Terbit',
            'Batas Teguran','Deadline KCKR','Tgl KCKR','Status KCKR',
            'Terlambat KCKR','Keterangan',
        ];

        $label = $this->buildFilterLabel($request);
        $i     = 1;
        $coord = fn(int $c, int $r) => \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c) . $r;
        $sp    = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $sp->getActiveSheet();
        $sheet->setTitle('Daftar Judul');

        $hStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1565C0']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                            'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'AAAAAA']]],
        ];

        $allHeaders = array_merge(['No'], $headers);
        $modeText = $kckrMode === 'provinsi' ? ' [DATA KCKR PROVINSI]' : ' [DATA KCKR PERPUSNAS]';
        $sheet->mergeCells($coord(1, 1) . ':' . $coord(count($allHeaders), 1));
        $sheet->getCell($coord(1, 1))->setValue('DAFTAR JUDUL — ' . strtoupper($penerbitName) . $modeText);
        $sheet->getStyle($coord(1, 1) . ':' . $coord(count($allHeaders), 1))->applyFromArray(array_merge($hStyle, [
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D47A1']],
        ]));
        $sheet->getRowDimension(1)->setRowHeight(20);

        foreach ($allHeaders as $idx => $h) {
            $addr = $coord($idx + 1, 2);
            $sheet->getCell($addr)->setValue($h);
            $sheet->getStyle($addr)->applyFromArray($hStyle);
        }
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getStyle('B:B')->getNumberFormat()
              ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        $rn = 3;
        foreach ($rows as $r) {
            $sheet->getCell($coord(1, $rn))->setValue($i++);
            foreach ($r as $ci => $v) {
                $cell = $sheet->getCell($coord($ci + 2, $rn));
                if ($ci === 0) {
                    $cell->setValueExplicit($v ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                } else {
                    $cell->setValue($v ?? '');
                }
            }
            $rn++;
        }
        // No, ISBN, Judul, Pengarang, Jilid, Jenis Media, Tgl Daftar, Deadline Terbit,
        // Tgl Terbit, Status Terbit, Batas Teguran, Deadline KCKR, Tgl KCKR, Status KCKR,
        // Terlambat KCKR, Keterangan
        $detailWidths = [5, 16, 38, 28, 10, 14, 12, 14, 12, 14, 14, 14, 12, 12, 14, 22];
        $detailWrap   = [3, 4, 16]; // Judul, Pengarang, Keterangan
        foreach ($detailWidths as $i => $w) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->getColumnDimension($col)->setWidth($w);
            if (in_array($i + 1, $detailWrap) && $rn > 3) {
                $sheet->getStyle($col . '3:' . $col . ($rn - 1))->getAlignment()->setWrapText(true);
            }
        }
        $sheet->freezePane('A3');

        return $this->streamXlsx($sp, $filename, $request);
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }
    }

    // ─── Spreadsheet builder ─────────────────────────────────────────────────

    private function makeSpreadsheetV3(
        callable $rowFetcher,
        string $sheetTitle,
        string $mainTitle,
        array $label,
        int $colCount
    ): \PhpOffice\PhpSpreadsheet\Spreadsheet {
        $Fill   = \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID;
        $AlignH = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
        $AlignV = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
        $coord  = fn(int $col, int $row) =>
            \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row;

        $sp    = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $sp->getActiveSheet();
        $sheet->setTitle($sheetTitle);

        $r1 = $this->writeTitleRows($sheet, $mainTitle, $label, $colCount);
        $r2 = $r1 + 1;

        $baseStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => $Fill, 'startColor' => ['rgb' => '1565C0']],
            'alignment' => ['horizontal' => $AlignH, 'vertical' => $AlignV, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                             'color'       => ['rgb' => 'AAAAAA']]],
        ];
        $groupStyle = $baseStyle;
        $groupStyle['fill']['startColor']['rgb'] = '0D47A1';

        // Kolom span tunggal (rowspan 2)
        $singleCols = [
            1 => '#',  2 => 'Nama Penerbit', 3 => 'Kategori',
            4 => 'Kota', 5 => 'Provinsi',
            6 => 'No. Telp 1', 7 => 'No. Telp 2', 8 => 'Email 1', 9 => 'Email 2',
            10 => 'Total Judul',
            22 => '% KCKR', 23 => 'Rekomendasi',
        ];
        foreach ($singleCols as $col => $label_) {
            $sheet->getCell($coord($col, $r1))->setValue($label_);
            $sheet->mergeCells($coord($col, $r1) . ':' . $coord($col, $r2));
            $sheet->getStyle($coord($col, $r1) . ':' . $coord($col, $r2))->applyFromArray($baseStyle);
        }

        $groups = [
            [11, 12, 'Status Terbit (2026+)'],
            [13, 14, 'Keterlambatan Terbit (2026+)'],
            [15, 17, 'Sudah KCKR'],
            [18, 21, 'Belum KCKR'],
        ];
        foreach ($groups as [$from, $to, $title]) {
            $sheet->getCell($coord($from, $r1))->setValue($title);
            $sheet->mergeCells($coord($from, $r1) . ':' . $coord($to, $r1));
            $sheet->getStyle($coord($from, $r1) . ':' . $coord($to, $r1))->applyFromArray($groupStyle);
        }

        $subHeaders = [
            11 => 'Terbit',       12 => 'Belum',
            13 => 'Hutang',       14 => 'Lewat Teguran',
            15 => 'Total',        16 => 'Cetak',        17 => 'Rekam',
            18 => 'Total',        19 => 'Cetak',        20 => 'Rekam',        21 => 'Terlambat',
        ];
        foreach ($subHeaders as $col => $label_) {
            $sheet->getCell($coord($col, $r2))->setValue($label_);
            $sheet->getStyle($coord($col, $r2))->applyFromArray($baseStyle);
        }

        $sheet->getRowDimension($r1)->setRowHeight(22);
        $sheet->getRowDimension($r2)->setRowHeight(20);

        // Kumpulkan semua baris dulu, lalu tulis & style secara range — jauh lebih cepat
        $allRows     = [];
        $redRows     = []; // baris yang kolom Terlambat KCKR > 0
        $rowFetcher(function(array $rowData) use (&$allRows, &$redRows): void {
            if (!empty($rowData[20]) && $rowData[20] > 0) {
                $redRows[] = count($allRows);
            }
            $allRows[] = $rowData;
        });

        $dataStart = $r2 + 1;
        $rowNum    = $dataStart + count($allRows); // baris terakhir + 1

        // Tulis semua data sekaligus (jauh lebih cepat dari per-cell)
        $sheet->fromArray($allRows, null, 'A' . $dataStart);

        if ($allRows) {
            $lastDataRow = $dataStart + count($allRows) - 1;
            $lastCol     = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount);
            $dataRange   = "A{$dataStart}:{$lastCol}{$lastDataRow}";

            // 1. Border + alignment seluruh area data (1 pemanggilan)
            $sheet->getStyle($dataRange)->applyFromArray([
                'borders'   => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'DDDDDD']]],
                'alignment' => ['vertical' => $AlignV],
            ]);

            // 2. Warna kolom-kelompok per range kolom (per kolom: 1 pemanggilan masing-masing)
            $colRanges = [
                'K' => 'E8F5E9', 'L' => 'E8F5E9',
                'M' => 'FFFDE7', 'N' => 'FFFDE7',
                'O' => 'C8E6C9', 'P' => 'C8E6C9', 'Q' => 'C8E6C9',
                'R' => 'FFF3E0', 'S' => 'FFF3E0', 'T' => 'FFF3E0', 'U' => 'FFCCBC',
            ];
            foreach ($colRanges as $colLetter => $rgb) {
                $sheet->getStyle("{$colLetter}{$dataStart}:{$colLetter}{$lastDataRow}")
                      ->getFill()->setFillType($Fill)->getStartColor()->setRGB($rgb);
            }

            // 3. Warna merah baris Terlambat > 0 (hanya kolom 21 = 'U', per baris yang perlu saja)
            foreach ($redRows as $idx) {
                $r = $dataStart + $idx;
                $sheet->getStyle("U{$r}")->getFont()->getColor()->setRGB('C62828');
                $sheet->getStyle("U{$r}")->getFont()->setBold(true);
            }
        }

        // #, Nama Penerbit, Kategori, Kota, Provinsi, Telp1, Telp2, Email1, Email2,
        // Total Judul,
        // (Terbit: Judul Terbit, Belum Terbit, Hutang, Lewat Teguran),
        // (KCKR Sudah: Total, Cetak, Rekam),
        // (KCKR Belum: Total, Cetak, Rekam, Terlambat),
        // % KCKR, Rekomendasi
        $rekapWidths = [5, 28, 12, 14, 18, 16, 16, 26, 26, 11, 11, 11, 11, 11, 11, 11, 11, 11, 11, 11, 11, 10, 22];
        $rekapWrap   = [2, 8, 9, 23]; // Nama Penerbit, Email1, Email2, Rekomendasi
        foreach ($rekapWidths as $i => $w) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i + 1);
            $sheet->getColumnDimension($col)->setWidth($w);
            if (in_array($i + 1, $rekapWrap) && $rowNum > $r2 + 1) {
                $sheet->getStyle($col . ($r2 + 1) . ':' . $col . ($rowNum - 1))->getAlignment()->setWrapText(true);
            }
        }
        $sheet->freezePane('A' . ($r2 + 1));

        return $sp;
    }
}
