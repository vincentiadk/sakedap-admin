<?php

namespace App\Http\Controllers\CoachingSupervision;

use App\Helpers\ComplianceSettings;
use App\Http\Controllers\Controller;
use App\Traits\OracleHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardComplianceController extends Controller
{
    use OracleHelper;

    public function index(Request $request)
    {
        try {
            $conn        = $this->getOracleConnection();
            $dateFilter  = $this->parseDateFilter($request);
            $start_date  = $dateFilter['start'];
            $end_date    = $dateFilter['end'];

            $kckrMode    = $request->get('kckr_mode', 'perpusnas');
            $ctx         = $this->resolveProvinceContext($request->province_ids ?? [], $kckrMode);
            $provinceIds = $ctx['provinceIds'];
            $kckrCol     = $ctx['kckrCol'];
            $isPerpusnas = $ctx['isPerpusnas'];
            $kckrMode    = $ctx['kckrMode'];

            $cutoff = '2026-01-01';
            $hasV1  = $start_date < $cutoff;
            $hasV2  = $end_date   > $cutoff;
            $isV2   = !$hasV1 && $hasV2;   // pure 2026+
            $isMixed = $hasV1 && $hasV2;   // range melintasi 2026

            $provinces = $isPerpusnas
                ? array_map(
                    fn($r) => (object) $r,
                    Cache::remember('dashboard:provinces', 3600, fn() =>
                        array_map(fn($r) => (array) $r, $this->fetchProvinces($conn))
                    )
                )
                : [];

            $whereProvinsi = $this->buildProvinceWhere($provinceIds);

            $cs       = ComplianceSettings::get();
            $dlKc     = (int) $cs['BatasWaktuKonfirmasiTerbitKaryaCetak'];
            $dlKr     = (int) $cs['BatasWaktuKonfirmasiTerbitDigital'];
            $teguran  = (int) $cs['BatasWaktuTeguranKonfirmasiTerbit'];
            $minPct   = (int) $cs['BatasMinimumKepatuhanKCKR'];
            $dlTerbitExpr = "CASE WHEN PT.JENIS_MEDIA = '1' THEN (PI.CREATEDATE + $dlKc) ELSE (PI.CREATEDATE + $dlKr) END";

            $csVersion     = ComplianceSettings::cacheVersion();
            $cacheKey      = $this->makeCacheKey($request, 'dashboard_' . $csVersion, [
                'filter_type', 'filter_year', 'filter_month',
                'start_date', 'end_date', 'province_ids', 'kckr_mode',
            ]);

            // ── Query universal: pre-2026 pakai CREATEDATE, 2026+ pakai TANGGAL_TERBIT ──
            $cached = Cache::remember($cacheKey, 3600, function() use ($conn, $start_date, $end_date, $cutoff, $whereProvinsi, $kckrCol, $dlTerbitExpr, $teguran, $minPct) {
                $dlTerbit = $dlTerbitExpr;
                $dlKckrV1 = "CASE WHEN P.KATEGORI_ID = 1 THEN ADD_MONTHS(PI.CREATEDATE, 3)
                                  ELSE CASE WHEN PT.JENIS_MEDIA = '1' THEN ADD_MONTHS(PI.CREATEDATE, 3)
                                            ELSE ADD_MONTHS(PI.CREATEDATE, 12) END END";
                $dlKckrV2 = "CASE WHEN P.KATEGORI_ID = 1 THEN ADD_MONTHS(PI.TANGGAL_TERBIT, 3)
                                  ELSE CASE WHEN PT.JENIS_MEDIA = '1' THEN ADD_MONTHS(PI.TANGGAL_TERBIT, 3)
                                            ELSE ADD_MONTHS(PI.TANGGAL_TERBIT, 12) END END";

                // pre-2026: semua judul wajib KCKR; 2026+: hanya yang sudah konfirmasi terbit
                $tagihanExpr = "SUM(CASE
                    WHEN PI.CREATEDATE <  TO_DATE('$cutoff','YYYY-MM-DD') AND PI.RECEIVED_DATE_KCKR IS NULL THEN 1
                    WHEN PI.CREATEDATE >= TO_DATE('$cutoff','YYYY-MM-DD') AND PI.TANGGAL_TERBIT IS NOT NULL AND PI.RECEIVED_DATE_KCKR IS NULL THEN 1
                    ELSE 0 END)";
                $sudahExpr   = "SUM(CASE WHEN PI.RECEIVED_DATE_KCKR IS NOT NULL THEN 1 ELSE 0 END)";
                $ttExpr      = "($sudahExpr + $tagihanExpr)";
                // Penerbit yang lewat teguran (blokir terbit) harus selalu masuk hitungan
                // meski ttExpr = 0 (buku 2026 belum konfirmasi terbit sama sekali)
                $lewatTeguranExpr = "SUM(CASE WHEN PI.CREATEDATE >= TO_DATE('$cutoff','YYYY-MM-DD')
                    AND PI.TANGGAL_TERBIT IS NULL AND PI.RECEIVED_DATE_KCKR IS NULL
                    AND SYSDATE > ($dlTerbit + $teguran) THEN 1 ELSE 0 END)";

                $query = "
                    SELECT
                        CASE
                            WHEN PERSENTASE_KCKR BETWEEN 0  AND 20  THEN 'Sangat Tidak Patuh'
                            WHEN PERSENTASE_KCKR BETWEEN 21 AND 40  THEN 'Tidak Patuh'
                            WHEN PERSENTASE_KCKR BETWEEN 41 AND 60  THEN 'Cukup Patuh'
                            WHEN PERSENTASE_KCKR BETWEEN 61 AND 80  THEN 'Patuh'
                            WHEN PERSENTASE_KCKR BETWEEN 81 AND 100 THEN 'Sangat Patuh'
                        END as KATEGORI_PATUH,
                        COUNT(*) as JUMLAH,
                        ROUND(AVG(PERSENTASE_KCKR), 1) as RATA_RATA_PCT,
                        SUM(TOTAL_JUDUL) as TOTAL_JUDUL,
                        SUM(SUDAH_KCKR) as TOTAL_KCKR
                    FROM (
                        SELECT
                            P.ID,
                            COUNT(DISTINCT PI.ID) as TOTAL_JUDUL,
                            SUM(CASE WHEN PI.RECEIVED_DATE_KCKR IS NOT NULL THEN 1 ELSE 0 END) as SUDAH_KCKR,
                            ROUND(
                                CASE WHEN $ttExpr > 0
                                    THEN SUM(CASE WHEN PI.RECEIVED_DATE_KCKR IS NOT NULL THEN 1 ELSE 0 END) / $ttExpr * 100
                                    ELSE 0 END, 1
                            ) as PERSENTASE_KCKR
                        FROM PENERBIT P
                        LEFT JOIN PENERBIT_ISBN PI ON P.ID = PI.PENERBIT_ID
                            AND PI.CREATEDATE >= TO_DATE('$start_date', 'YYYY-MM-DD')
                            AND PI.CREATEDATE <  TO_DATE('$end_date',   'YYYY-MM-DD')
                            AND (PI.KETERANGAN IS NULL OR UPPER(PI.KETERANGAN) NOT LIKE '%LENGKAP%')
                        LEFT JOIN PENERBIT_TERBITAN PT ON PI.PENERBIT_TERBITAN_ID = PT.ID
                        WHERE 1=1 $whereProvinsi
                        GROUP BY P.ID
                        HAVING $ttExpr > 0 OR $lewatTeguranExpr > 0
                    )
                    GROUP BY
                        CASE
                            WHEN PERSENTASE_KCKR BETWEEN 0  AND 20  THEN 'Sangat Tidak Patuh'
                            WHEN PERSENTASE_KCKR BETWEEN 21 AND 40  THEN 'Tidak Patuh'
                            WHEN PERSENTASE_KCKR BETWEEN 41 AND 60  THEN 'Cukup Patuh'
                            WHEN PERSENTASE_KCKR BETWEEN 61 AND 80  THEN 'Patuh'
                            WHEN PERSENTASE_KCKR BETWEEN 81 AND 100 THEN 'Sangat Patuh'
                        END
                    ORDER BY MIN(PERSENTASE_KCKR)
                ";
                $result     = odbc_exec($conn, str_replace('RECEIVED_DATE_KCKR', $kckrCol, $query));
                $distribusi = [];
                while ($row = odbc_fetch_object($result)) {
                    $distribusi[] = (array) $row;
                }

                $queryTotal = "
                    SELECT
                        COUNT(*) as TOTAL_PENERBIT,
                        SUM(TOTAL_JUDUL) as TOTAL_JUDUL,
                        SUM(SUDAH_KCKR) as TOTAL_KCKR,
                        SUM(BELUM_KCKR) as TOTAL_BELUM_KCKR,
                        ROUND(AVG(PERSENTASE_KCKR), 1) as RATA_RATA_KEPATUHAN,
                        SUM(JUDUL_TERBIT) as TOTAL_TERBIT,
                        SUM(JUDUL_BELUM_TERBIT) as TOTAL_BELUM_TERBIT,
                        SUM(HUTANG_TERBIT) as TOTAL_HUTANG_TERBIT,
                        SUM(LEWAT_TEGURAN) as TOTAL_LEWAT_TEGURAN,
                        COUNT(CASE WHEN LEWAT_TEGURAN > 0 AND NOT (TERLAMBAT_KCKR > 0 AND PERSENTASE_KCKR <= $minPct) THEN 1 END) as PENERBIT_BLOKIR_TERBIT,
                        COUNT(CASE WHEN TERLAMBAT_KCKR > 0 AND PERSENTASE_KCKR <= $minPct AND LEWAT_TEGURAN = 0 THEN 1 END) as PENERBIT_BLOKIR_KCKR,
                        COUNT(CASE WHEN LEWAT_TEGURAN > 0 AND TERLAMBAT_KCKR > 0 AND PERSENTASE_KCKR <= $minPct THEN 1 END) as PENERBIT_BLOKIR_KEDUANYA,
                        COUNT(CASE WHEN LEWAT_TEGURAN = 0 AND (TERLAMBAT_KCKR = 0 OR PERSENTASE_KCKR > $minPct) THEN 1 END) as PENERBIT_BAIK
                    FROM (
                        SELECT
                            P.ID,
                            COUNT(DISTINCT PI.ID) as TOTAL_JUDUL,
                            SUM(CASE WHEN PI.RECEIVED_DATE_KCKR IS NOT NULL THEN 1 ELSE 0 END) as SUDAH_KCKR,
                            SUM(CASE WHEN PI.RECEIVED_DATE_KCKR IS NULL THEN 1 ELSE 0 END) as BELUM_KCKR,
                            -- KCKR deadline: pre-2026 dari CREATEDATE, 2026+ dari TANGGAL_TERBIT
                            SUM(CASE
                                WHEN PI.CREATEDATE < TO_DATE('$cutoff','YYYY-MM-DD') AND (
                                    (PI.RECEIVED_DATE_KCKR IS NOT NULL AND PI.RECEIVED_DATE_KCKR > ($dlKckrV1))
                                 OR (PI.RECEIVED_DATE_KCKR IS NULL AND SYSDATE > ($dlKckrV1))
                                ) THEN 1
                                WHEN PI.CREATEDATE >= TO_DATE('$cutoff','YYYY-MM-DD') AND PI.TANGGAL_TERBIT IS NOT NULL AND (
                                    (PI.RECEIVED_DATE_KCKR IS NOT NULL AND PI.RECEIVED_DATE_KCKR > ($dlKckrV2))
                                 OR (PI.RECEIVED_DATE_KCKR IS NULL AND SYSDATE > ($dlKckrV2))
                                ) THEN 1
                                ELSE 0 END) as TERLAMBAT_KCKR,
                            -- Konfirmasi terbit: hanya berlaku untuk 2026+
                            COUNT(DISTINCT CASE WHEN PI.CREATEDATE >= TO_DATE('$cutoff','YYYY-MM-DD')
                                AND (PI.TANGGAL_TERBIT IS NOT NULL OR PI.RECEIVED_DATE_KCKR IS NOT NULL)
                                THEN PI.ID END) as JUDUL_TERBIT,
                            COUNT(DISTINCT CASE WHEN PI.CREATEDATE >= TO_DATE('$cutoff','YYYY-MM-DD')
                                AND PI.TANGGAL_TERBIT IS NULL AND PI.RECEIVED_DATE_KCKR IS NULL
                                THEN PI.ID END) as JUDUL_BELUM_TERBIT,
                            SUM(CASE WHEN PI.CREATEDATE >= TO_DATE('$cutoff','YYYY-MM-DD')
                                AND PI.TANGGAL_TERBIT IS NULL AND PI.RECEIVED_DATE_KCKR IS NULL
                                AND SYSDATE > $dlTerbit THEN 1 ELSE 0 END) as HUTANG_TERBIT,
                            SUM(CASE WHEN PI.CREATEDATE >= TO_DATE('$cutoff','YYYY-MM-DD')
                                AND PI.TANGGAL_TERBIT IS NULL AND PI.RECEIVED_DATE_KCKR IS NULL
                                AND SYSDATE > ($dlTerbit + $teguran) THEN 1 ELSE 0 END) as LEWAT_TEGURAN,
                            ROUND(
                                CASE WHEN $ttExpr > 0
                                    THEN SUM(CASE WHEN PI.RECEIVED_DATE_KCKR IS NOT NULL THEN 1 ELSE 0 END) / $ttExpr * 100
                                    ELSE 0 END, 1
                            ) as PERSENTASE_KCKR
                        FROM PENERBIT P
                        LEFT JOIN PENERBIT_ISBN PI ON P.ID = PI.PENERBIT_ID
                            AND PI.CREATEDATE >= TO_DATE('$start_date', 'YYYY-MM-DD')
                            AND PI.CREATEDATE <  TO_DATE('$end_date',   'YYYY-MM-DD')
                            AND (PI.KETERANGAN IS NULL OR UPPER(PI.KETERANGAN) NOT LIKE '%LENGKAP%')
                        LEFT JOIN PENERBIT_TERBITAN PT ON PI.PENERBIT_TERBITAN_ID = PT.ID
                        WHERE 1=1 $whereProvinsi
                        GROUP BY P.ID
                        HAVING $ttExpr > 0 OR $lewatTeguranExpr > 0
                    )
                ";
                $resultTotal = odbc_exec($conn, str_replace('RECEIVED_DATE_KCKR', $kckrCol, $queryTotal));
                $total       = (array) odbc_fetch_object($resultTotal);

                return compact('distribusi', 'total');
            });

            $distribusi = array_map(fn($r) => (object) $r, $cached['distribusi']);
            $total      = (object) $cached['total'];

            return view('coaching-supervision.dashboard', compact(
                'distribusi', 'total', 'start_date', 'end_date',
                'provinceIds', 'provinces', 'dateFilter', 'isV2', 'hasV2', 'isMixed', 'isPerpusnas', 'kckrMode',
                'minPct'
            ));

        } catch (\Exception $e) {
            return view('coaching-supervision.dashboard', [
                'error' => 'Error: ' . $e->getMessage(),
                'distribusi' => [], 'total' => null,
                'provinces' => [], 'provinceIds' => [],
                'isV2' => false, 'hasV2' => false, 'isMixed' => false,
            ]);
        }
    }

    public function prediksiData(Request $request)
    {
        try {
            $conn = $this->getOracleConnection();
            $ctx  = $this->resolveProvinceContext($request->province_ids ?? [], $request->get('kckr_mode', 'perpusnas'));
            $whereProvinsi = $this->buildProvinceWhere($ctx['provinceIds']);
            $kckrCol = $ctx['kckrCol'];

            $cs      = ComplianceSettings::get();
            $dlKc    = (int) $cs['BatasWaktuKonfirmasiTerbitKaryaCetak'];
            $dlKr    = (int) $cs['BatasWaktuKonfirmasiTerbitDigital'];
            $teguran = (int) $cs['BatasWaktuTeguranKonfirmasiTerbit'];
            $cutoff  = '2026-01-01';

            $cacheKey = $this->makeCacheKey($request, 'dashboard:prediksi_' . ComplianceSettings::cacheVersion(), ['province_ids', 'kckr_mode']);

            $data = Cache::remember($cacheKey, 1800, function() use ($conn, $whereProvinsi, $kckrCol, $dlKc, $dlKr, $teguran, $cutoff) {
                $daysExpr = "ROUND(CASE WHEN PT.JENIS_MEDIA = '1'
                    THEN (PI.CREATEDATE + $dlKc + $teguran) - SYSDATE
                    ELSE (PI.CREATEDATE + $dlKr + $teguran) - SYSDATE END)";

                // Bucket summary: 0-30, 31-60, 61-90 hari sebelum blokir
                $sqlBucket = "
                    SELECT
                        SUM(CASE WHEN MIN_DAYS BETWEEN 1  AND 30 THEN 1 ELSE 0 END) as D30,
                        SUM(CASE WHEN MIN_DAYS BETWEEN 31 AND 60 THEN 1 ELSE 0 END) as D60,
                        SUM(CASE WHEN MIN_DAYS BETWEEN 61 AND 90 THEN 1 ELSE 0 END) as D90
                    FROM (
                        SELECT PI.PENERBIT_ID, MIN($daysExpr) as MIN_DAYS
                        FROM PENERBIT P
                        JOIN PENERBIT_ISBN PI ON P.ID = PI.PENERBIT_ID
                        JOIN PENERBIT_TERBITAN PT ON PI.PENERBIT_TERBITAN_ID = PT.ID
                        WHERE PI.CREATEDATE >= TO_DATE('$cutoff', 'YYYY-MM-DD')
                        AND PI.TANGGAL_TERBIT IS NULL
                        AND PI.RECEIVED_DATE_KCKR IS NULL
                        AND (PI.KETERANGAN IS NULL OR UPPER(PI.KETERANGAN) NOT LIKE '%LENGKAP%')
                        $whereProvinsi
                        GROUP BY PI.PENERBIT_ID
                        HAVING MIN($daysExpr) BETWEEN 1 AND 90
                    )
                ";
                $r   = odbc_fetch_object(odbc_exec($conn, str_replace('RECEIVED_DATE_KCKR', $kckrCol, $sqlBucket)));
                $d30 = (int) ($r->D30 ?? 0);
                $d60 = (int) ($r->D60 ?? 0);
                $d90 = (int) ($r->D90 ?? 0);

                // Timeline harian untuk 30 hari ke depan (untuk chart)
                $sqlTimeline = "
                    SELECT MIN_DAYS as DAY_N, COUNT(*) as CNT
                    FROM (
                        SELECT PI.PENERBIT_ID, MIN($daysExpr) as MIN_DAYS
                        FROM PENERBIT P
                        JOIN PENERBIT_ISBN PI ON P.ID = PI.PENERBIT_ID
                        JOIN PENERBIT_TERBITAN PT ON PI.PENERBIT_TERBITAN_ID = PT.ID
                        WHERE PI.CREATEDATE >= TO_DATE('$cutoff', 'YYYY-MM-DD')
                        AND PI.TANGGAL_TERBIT IS NULL
                        AND PI.RECEIVED_DATE_KCKR IS NULL
                        AND (PI.KETERANGAN IS NULL OR UPPER(PI.KETERANGAN) NOT LIKE '%LENGKAP%')
                        $whereProvinsi
                        GROUP BY PI.PENERBIT_ID
                        HAVING MIN($daysExpr) BETWEEN 1 AND 30
                    )
                    GROUP BY MIN_DAYS
                    ORDER BY MIN_DAYS
                ";
                $res      = odbc_exec($conn, str_replace('RECEIVED_DATE_KCKR', $kckrCol, $sqlTimeline));
                $dayMap   = [];
                while ($row = odbc_fetch_object($res)) {
                    $dayMap[(int) $row->DAY_N] = (int) $row->CNT;
                }
                $timelineLabels = [];
                $timelineData   = [];
                for ($d = 1; $d <= 30; $d++) {
                    $timelineLabels[] = 'H-' . $d;
                    $timelineData[]   = $dayMap[$d] ?? 0;
                }

                return compact('d30', 'd60', 'd90', 'timelineLabels', 'timelineData');
            });

            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function provinsiData(Request $request)
    {
        try {
            $conn       = $this->getOracleConnection();
            $dateFilter = $this->parseDateFilter($request);
            $start_date = $dateFilter['start'];
            $end_date   = $dateFilter['end'];
            $ctx        = $this->resolveProvinceContext((array) ($request->province_ids ?? []), $request->get('kckr_mode', 'perpusnas'));
            $kckrCol    = $ctx['kckrCol'];
            $provinceIds = $ctx['provinceIds'];

            // 1 provinsi terpilih → breakdown per kabupaten/kota
            $mode          = count($provinceIds) === 1 ? 'kota' : 'provinsi';
            $whereProvinsi = $this->buildProvinceWhere($provinceIds);

            $cs      = ComplianceSettings::get();
            $dlKc    = (int) $cs['BatasWaktuKonfirmasiTerbitKaryaCetak'];
            $dlKr    = (int) $cs['BatasWaktuKonfirmasiTerbitDigital'];
            $teguran = (int) $cs['BatasWaktuTeguranKonfirmasiTerbit'];
            $minPct  = (int) $cs['BatasMinimumKepatuhanKCKR'];
            $cutoff  = '2026-01-01';

            $cacheKey = $this->makeCacheKey($request, 'dashboard:provinsi_' . ComplianceSettings::cacheVersion(), [
                'filter_type', 'filter_year', 'filter_month', 'start_date', 'end_date', 'kckr_mode', 'province_ids',
            ]);

            $rows = Cache::remember($cacheKey, 3600, function() use (
                $conn, $kckrCol, $start_date, $end_date, $cutoff, $dlKc, $dlKr, $teguran, $minPct, $mode, $whereProvinsi
            ) {
                $dlKckrV1 = "CASE WHEN P.KATEGORI_ID = 1 THEN ADD_MONTHS(PI.CREATEDATE, 3)
                                  ELSE CASE WHEN PT.JENIS_MEDIA = '1' THEN ADD_MONTHS(PI.CREATEDATE, 3)
                                            ELSE ADD_MONTHS(PI.CREATEDATE, 12) END END";
                $dlKckrV2 = "CASE WHEN P.KATEGORI_ID = 1 THEN ADD_MONTHS(PI.TANGGAL_TERBIT, 3)
                                  ELSE CASE WHEN PT.JENIS_MEDIA = '1' THEN ADD_MONTHS(PI.TANGGAL_TERBIT, 3)
                                            ELSE ADD_MONTHS(PI.TANGGAL_TERBIT, 12) END END";
                $dlTerbit = "CASE WHEN PT.JENIS_MEDIA = '1' THEN (PI.CREATEDATE + $dlKc) ELSE (PI.CREATEDATE + $dlKr) END";

                $sudahExpr   = "SUM(CASE WHEN PI.RECEIVED_DATE_KCKR IS NOT NULL THEN 1 ELSE 0 END)";
                $tagihanExpr = "SUM(CASE
                    WHEN PI.CREATEDATE <  TO_DATE('$cutoff','YYYY-MM-DD') AND PI.RECEIVED_DATE_KCKR IS NULL THEN 1
                    WHEN PI.CREATEDATE >= TO_DATE('$cutoff','YYYY-MM-DD') AND PI.TANGGAL_TERBIT IS NOT NULL AND PI.RECEIVED_DATE_KCKR IS NULL THEN 1
                    ELSE 0 END)";
                $ttExpr = "($sudahExpr + $tagihanExpr)";
                $lewatTeguranExpr = "SUM(CASE WHEN PI.CREATEDATE >= TO_DATE('$cutoff','YYYY-MM-DD')
                    AND PI.TANGGAL_TERBIT IS NULL AND PI.RECEIVED_DATE_KCKR IS NULL
                    AND SYSDATE > ($dlTerbit + $teguran) THEN 1 ELSE 0 END)";

                if ($mode === 'kota') {
                    // 1 provinsi → group per kabupaten/kota (P.CITY free-text, dinormalisasi)
                    $outerSelect = "NVL(INITCAP(TRIM(S.CITY)), 'Tidak Diketahui') as NAMA";
                    $outerFrom   = '';
                    $outerJoin   = '';
                    $outerGroup  = "GROUP BY NVL(INITCAP(TRIM(S.CITY)), 'Tidak Diketahui')";
                } else {
                    $outerSelect = "PR.NAMAPROPINSI as NAMA";
                    $outerFrom   = "PROPINSI PR JOIN";
                    $outerJoin   = "ON S.PROVINCE_ID = PR.ID";
                    $outerGroup  = "GROUP BY PR.ID, PR.NAMAPROPINSI";
                }

                $sql = "
                    SELECT
                        $outerSelect,
                        COUNT(*) as TOTAL_PENERBIT,
                        ROUND(AVG(S.PERSENTASE_KCKR), 1) as AVG_KCKR,
                        COUNT(CASE WHEN S.LEWAT_TEGURAN > 0 AND NOT (S.TERLAMBAT_KCKR > 0 AND S.PERSENTASE_KCKR <= $minPct) THEN 1 END) as BLOKIR_TERBIT,
                        COUNT(CASE WHEN S.TERLAMBAT_KCKR > 0 AND S.PERSENTASE_KCKR <= $minPct AND S.LEWAT_TEGURAN = 0 THEN 1 END) as BLOKIR_KCKR,
                        COUNT(CASE WHEN S.LEWAT_TEGURAN > 0 AND S.TERLAMBAT_KCKR > 0 AND S.PERSENTASE_KCKR <= $minPct THEN 1 END) as BLOKIR_KEDUANYA,
                        COUNT(CASE WHEN S.LEWAT_TEGURAN = 0 AND (S.TERLAMBAT_KCKR = 0 OR S.PERSENTASE_KCKR > $minPct) THEN 1 END) as BAIK
                    FROM $outerFrom (
                        SELECT
                            P.ID,
                            P.PROVINCE_ID,
                            P.CITY,
                            ROUND(
                                CASE WHEN $ttExpr > 0
                                    THEN SUM(CASE WHEN PI.RECEIVED_DATE_KCKR IS NOT NULL THEN 1 ELSE 0 END) / $ttExpr * 100
                                    ELSE 0 END, 1
                            ) as PERSENTASE_KCKR,
                            SUM(CASE
                                WHEN PI.CREATEDATE < TO_DATE('$cutoff','YYYY-MM-DD') AND (
                                    (PI.RECEIVED_DATE_KCKR IS NOT NULL AND PI.RECEIVED_DATE_KCKR > ($dlKckrV1))
                                 OR (PI.RECEIVED_DATE_KCKR IS NULL AND SYSDATE > ($dlKckrV1))
                                ) THEN 1
                                WHEN PI.CREATEDATE >= TO_DATE('$cutoff','YYYY-MM-DD') AND PI.TANGGAL_TERBIT IS NOT NULL AND (
                                    (PI.RECEIVED_DATE_KCKR IS NOT NULL AND PI.RECEIVED_DATE_KCKR > ($dlKckrV2))
                                 OR (PI.RECEIVED_DATE_KCKR IS NULL AND SYSDATE > ($dlKckrV2))
                                ) THEN 1
                                ELSE 0 END) as TERLAMBAT_KCKR,
                            SUM(CASE WHEN PI.CREATEDATE >= TO_DATE('$cutoff','YYYY-MM-DD')
                                AND PI.TANGGAL_TERBIT IS NULL AND PI.RECEIVED_DATE_KCKR IS NULL
                                AND SYSDATE > ($dlTerbit + $teguran) THEN 1 ELSE 0 END) as LEWAT_TEGURAN
                        FROM PENERBIT P
                        LEFT JOIN PENERBIT_ISBN PI ON P.ID = PI.PENERBIT_ID
                            AND PI.CREATEDATE >= TO_DATE('$start_date', 'YYYY-MM-DD')
                            AND PI.CREATEDATE <  TO_DATE('$end_date',   'YYYY-MM-DD')
                            AND (PI.KETERANGAN IS NULL OR UPPER(PI.KETERANGAN) NOT LIKE '%LENGKAP%')
                        LEFT JOIN PENERBIT_TERBITAN PT ON PI.PENERBIT_TERBITAN_ID = PT.ID
                        WHERE 1=1 $whereProvinsi
                        GROUP BY P.ID, P.PROVINCE_ID, P.CITY
                        HAVING $ttExpr > 0 OR $lewatTeguranExpr > 0
                    ) S $outerJoin
                    $outerGroup
                    ORDER BY AVG_KCKR ASC
                ";
                $result = odbc_exec($conn, str_replace('RECEIVED_DATE_KCKR', $kckrCol, $sql));
                $data   = [];
                while ($row = odbc_fetch_object($result)) {
                    $data[] = [
                        'nama'            => $row->NAMA,
                        'total_penerbit'  => (int) $row->TOTAL_PENERBIT,
                        'avg_kckr'        => (float) $row->AVG_KCKR,
                        'blokir_terbit'   => (int) $row->BLOKIR_TERBIT,
                        'blokir_kckr'     => (int) $row->BLOKIR_KCKR,
                        'blokir_keduanya' => (int) $row->BLOKIR_KEDUANYA,
                        'baik'            => (int) $row->BAIK,
                    ];
                }
                return $data;
            });

            return response()->json(['mode' => $mode, 'rows' => $rows]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function breakdownData(Request $request)
    {
        try {
            $conn       = $this->getOracleConnection();
            $dateFilter = $this->parseDateFilter($request);
            $start_date = $dateFilter['start'];
            $end_date   = $dateFilter['end'];
            $ctx        = $this->resolveProvinceContext($request->province_ids ?? [], $request->get('kckr_mode', 'perpusnas'));
            $whereProvinsi = $this->buildProvinceWhere($ctx['provinceIds']);
            $kckrCol    = $ctx['kckrCol'];
            $cutoff     = '2026-01-01';

            $cacheKey = $this->makeCacheKey($request, 'dashboard:breakdown2', [
                'filter_type', 'filter_year', 'filter_month', 'start_date', 'end_date', 'province_ids', 'kckr_mode',
            ]);

            $data = Cache::remember($cacheKey, 3600, function() use ($conn, $kckrCol, $start_date, $end_date, $cutoff, $whereProvinsi) {
                $sudahExpr   = "SUM(CASE WHEN PI.RECEIVED_DATE_KCKR IS NOT NULL THEN 1 ELSE 0 END)";
                $tagihanExpr = "SUM(CASE
                    WHEN PI.CREATEDATE <  TO_DATE('$cutoff','YYYY-MM-DD') AND PI.RECEIVED_DATE_KCKR IS NULL THEN 1
                    WHEN PI.CREATEDATE >= TO_DATE('$cutoff','YYYY-MM-DD') AND PI.TANGGAL_TERBIT IS NOT NULL AND PI.RECEIVED_DATE_KCKR IS NULL THEN 1
                    ELSE 0 END)";
                $fromWhere = "
                    FROM PENERBIT P
                    JOIN PENERBIT_ISBN PI ON P.ID = PI.PENERBIT_ID
                        AND PI.CREATEDATE >= TO_DATE('$start_date', 'YYYY-MM-DD')
                        AND PI.CREATEDATE <  TO_DATE('$end_date',   'YYYY-MM-DD')
                        AND (PI.KETERANGAN IS NULL OR UPPER(PI.KETERANGAN) NOT LIKE '%LENGKAP%')
                    LEFT JOIN PENERBIT_TERBITAN PT ON PI.PENERBIT_TERBITAN_ID = PT.ID
                    WHERE 1=1 $whereProvinsi
                ";

                // 1) Breakdown kategori: Pemerintah vs Swasta
                $sqlKategori = "
                    SELECT CASE WHEN P.KATEGORI_ID = 1 THEN 'Pemerintah' ELSE 'Swasta' END as GRP,
                        COUNT(DISTINCT P.ID) as PENERBIT,
                        COUNT(PI.ID)         as TOTAL_JUDUL,
                        $sudahExpr           as SUDAH,
                        $tagihanExpr         as BELUM
                    $fromWhere
                    GROUP BY CASE WHEN P.KATEGORI_ID = 1 THEN 'Pemerintah' ELSE 'Swasta' END
                ";

                // 2) Breakdown media: Karya Cetak vs Karya Rekam
                $sqlMedia = "
                    SELECT CASE WHEN PT.JENIS_MEDIA = '1' THEN 'Karya Cetak' ELSE 'Karya Rekam' END as GRP,
                        COUNT(DISTINCT P.ID) as PENERBIT,
                        COUNT(PI.ID)         as TOTAL_JUDUL,
                        $sudahExpr           as SUDAH,
                        $tagihanExpr         as BELUM
                    $fromWhere
                    GROUP BY CASE WHEN PT.JENIS_MEDIA = '1' THEN 'Karya Cetak' ELSE 'Karya Rekam' END
                ";

                // 3) Top 10 penerbit penyumbang hutang KCKR terbesar
                $sqlTop10 = "
                    SELECT * FROM (
                        SELECT P.ID, P.NAME,
                            COUNT(PI.ID)  as TOTAL_JUDUL,
                            $sudahExpr    as SUDAH,
                            $tagihanExpr  as HUTANG
                        $fromWhere
                        GROUP BY P.ID, P.NAME
                        HAVING $tagihanExpr > 0
                        ORDER BY $tagihanExpr DESC
                    ) WHERE ROWNUM <= 10
                ";

                $fetch = function($sql) use ($conn, $kckrCol) {
                    $res  = odbc_exec($conn, str_replace('RECEIVED_DATE_KCKR', $kckrCol, $sql));
                    $rows = [];
                    while ($row = odbc_fetch_object($res)) {
                        $rows[] = (array) $row;
                    }
                    return $rows;
                };

                $mapGroup = fn($rows) => array_map(function($r) {
                    $wajib = (int) $r['SUDAH'] + (int) $r['BELUM'];
                    return [
                        'grp'      => $r['GRP'],
                        'penerbit' => (int) $r['PENERBIT'],
                        'judul'    => (int) $r['TOTAL_JUDUL'],
                        'sudah'    => (int) $r['SUDAH'],
                        'belum'    => (int) $r['BELUM'],
                        'pct'      => $wajib > 0 ? round((int) $r['SUDAH'] / $wajib * 100, 1) : 0,
                    ];
                }, $rows);

                // Status setoran karya rekam digital di e-Deposit (e_collections)
                // 1 = in review, 2 = diterima, 3 = bermasalah
                $sqlEcol = "
                    SELECT TO_CHAR(E.STATUS) as STATUS, COUNT(*) as CNT
                    FROM E_COLLECTIONS E
                    JOIN PENERBIT P ON P.ID = E.PENERBIT_ID
                    WHERE E.DELETED_AT IS NULL
                    AND E.CREATED_AT >= TO_DATE('$start_date', 'YYYY-MM-DD')
                    AND E.CREATED_AT <  TO_DATE('$end_date',   'YYYY-MM-DD')
                    $whereProvinsi
                    GROUP BY TO_CHAR(E.STATUS)
                ";
                $ecolMap = ['1' => 0, '2' => 0, '3' => 0];
                foreach ($fetch($sqlEcol) as $r) {
                    if (isset($ecolMap[$r['STATUS']])) {
                        $ecolMap[$r['STATUS']] = (int) $r['CNT'];
                    }
                }

                return [
                    'kategori' => $mapGroup($fetch($sqlKategori)),
                    'media'    => $mapGroup($fetch($sqlMedia)),
                    'ecol'     => [
                        'in_review'  => $ecolMap['1'],
                        'diterima'   => $ecolMap['2'],
                        'bermasalah' => $ecolMap['3'],
                    ],
                    'top10'    => array_map(fn($r) => [
                        'id'     => (int) $r['ID'],
                        'nama'   => $r['NAME'],
                        'judul'  => (int) $r['TOTAL_JUDUL'],
                        'sudah'  => (int) $r['SUDAH'],
                        'hutang' => (int) $r['HUTANG'],
                        'pct'    => ((int) $r['SUDAH'] + (int) $r['HUTANG']) > 0
                            ? round((int) $r['SUDAH'] / ((int) $r['SUDAH'] + (int) $r['HUTANG']) * 100, 1) : 0,
                    ], $fetch($sqlTop10)),
                ];
            });

            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function insightData(Request $request)
    {
        try {
            $conn       = $this->getOracleConnection();
            $dateFilter = $this->parseDateFilter($request);
            $start_date = $dateFilter['start'];
            $end_date   = $dateFilter['end'];
            $ctx        = $this->resolveProvinceContext($request->province_ids ?? [], $request->get('kckr_mode', 'perpusnas'));
            $whereProvinsi = $this->buildProvinceWhere($ctx['provinceIds']);
            $kckrCol    = $ctx['kckrCol'];
            $cutoff     = '2026-01-01';

            // Periode sebelumnya: geser mundur sepanjang periode aktif
            $lenDays   = max(1, (int) ((strtotime($end_date) - strtotime($start_date)) / 86400));
            $prevStart = date('Y-m-d', strtotime($start_date) - $lenDays * 86400);
            $prevEnd   = $start_date;

            $cacheKey = $this->makeCacheKey($request, 'dashboard:insight', [
                'filter_type', 'filter_year', 'filter_month', 'start_date', 'end_date', 'province_ids', 'kckr_mode',
            ]);

            $data = Cache::remember($cacheKey, 3600, function() use (
                $conn, $kckrCol, $start_date, $end_date, $prevStart, $prevEnd, $cutoff, $whereProvinsi
            ) {
                $sudahExpr   = "SUM(CASE WHEN PI.RECEIVED_DATE_KCKR IS NOT NULL THEN 1 ELSE 0 END)";
                $tagihanExpr = "SUM(CASE
                    WHEN PI.CREATEDATE <  TO_DATE('$cutoff','YYYY-MM-DD') AND PI.RECEIVED_DATE_KCKR IS NULL THEN 1
                    WHEN PI.CREATEDATE >= TO_DATE('$cutoff','YYYY-MM-DD') AND PI.TANGGAL_TERBIT IS NOT NULL AND PI.RECEIVED_DATE_KCKR IS NULL THEN 1
                    ELSE 0 END)";

                // 1) Perbandingan periode: metrik ringkas periode aktif vs sebelumnya
                $periodQuery = function($sd, $ed) use ($conn, $kckrCol, $whereProvinsi, $sudahExpr, $tagihanExpr) {
                    $sql = "
                        SELECT
                            COUNT(DISTINCT P.ID) as PENERBIT,
                            COUNT(PI.ID)         as TOTAL_JUDUL,
                            $sudahExpr           as SUDAH,
                            $tagihanExpr         as BELUM
                        FROM PENERBIT P
                        JOIN PENERBIT_ISBN PI ON P.ID = PI.PENERBIT_ID
                            AND PI.CREATEDATE >= TO_DATE('$sd', 'YYYY-MM-DD')
                            AND PI.CREATEDATE <  TO_DATE('$ed', 'YYYY-MM-DD')
                            AND (PI.KETERANGAN IS NULL OR UPPER(PI.KETERANGAN) NOT LIKE '%LENGKAP%')
                        WHERE 1=1 $whereProvinsi
                    ";
                    $r = (array) odbc_fetch_object(odbc_exec($conn, str_replace('RECEIVED_DATE_KCKR', $kckrCol, $sql)));
                    $wajib = (int) ($r['SUDAH'] ?? 0) + (int) ($r['BELUM'] ?? 0);
                    return [
                        'penerbit' => (int) ($r['PENERBIT'] ?? 0),
                        'judul'    => (int) ($r['TOTAL_JUDUL'] ?? 0),
                        'sudah'    => (int) ($r['SUDAH'] ?? 0),
                        'belum'    => (int) ($r['BELUM'] ?? 0),
                        'pct'      => $wajib > 0 ? round((int) ($r['SUDAH'] ?? 0) / $wajib * 100, 1) : 0,
                    ];
                };

                $current  = $periodQuery($start_date, $end_date);
                $previous = $periodQuery($prevStart, $prevEnd);

                // 2) Aging hutang KCKR: usia kewajiban yang belum disetor
                //    pre-2026 dihitung dari CREATEDATE, 2026+ dari TANGGAL_TERBIT
                $ageExpr = "CASE
                    WHEN PI.CREATEDATE < TO_DATE('$cutoff','YYYY-MM-DD') THEN MONTHS_BETWEEN(SYSDATE, PI.CREATEDATE)
                    ELSE MONTHS_BETWEEN(SYSDATE, PI.TANGGAL_TERBIT) END";
                $sqlAging = "
                    SELECT
                        SUM(CASE WHEN $ageExpr <  3               THEN 1 ELSE 0 END) as B1,
                        SUM(CASE WHEN $ageExpr >= 3  AND $ageExpr < 6  THEN 1 ELSE 0 END) as B2,
                        SUM(CASE WHEN $ageExpr >= 6  AND $ageExpr < 12 THEN 1 ELSE 0 END) as B3,
                        SUM(CASE WHEN $ageExpr >= 12                   THEN 1 ELSE 0 END) as B4
                    FROM PENERBIT P
                    JOIN PENERBIT_ISBN PI ON P.ID = PI.PENERBIT_ID
                        AND PI.CREATEDATE >= TO_DATE('$start_date', 'YYYY-MM-DD')
                        AND PI.CREATEDATE <  TO_DATE('$end_date',   'YYYY-MM-DD')
                        AND (PI.KETERANGAN IS NULL OR UPPER(PI.KETERANGAN) NOT LIKE '%LENGKAP%')
                    WHERE PI.RECEIVED_DATE_KCKR IS NULL
                    AND (PI.CREATEDATE < TO_DATE('$cutoff','YYYY-MM-DD') OR PI.TANGGAL_TERBIT IS NOT NULL)
                    $whereProvinsi
                ";
                $ag = (array) odbc_fetch_object(odbc_exec($conn, str_replace('RECEIVED_DATE_KCKR', $kckrCol, $sqlAging)));
                $aging = [
                    ['label' => '< 3 bulan',   'count' => (int) ($ag['B1'] ?? 0)],
                    ['label' => '3–6 bulan',   'count' => (int) ($ag['B2'] ?? 0)],
                    ['label' => '6–12 bulan',  'count' => (int) ($ag['B3'] ?? 0)],
                    ['label' => '> 1 tahun',   'count' => (int) ($ag['B4'] ?? 0)],
                ];

                // 3) Rata-rata & median lama konfirmasi terbit (2026+, hari dari ISBN sampai konfirmasi)
                $sqlKonfirm = "
                    SELECT
                        ROUND(AVG(PI.TANGGAL_TERBIT - PI.CREATEDATE), 1)    as AVG_HARI,
                        ROUND(MEDIAN(PI.TANGGAL_TERBIT - PI.CREATEDATE), 1) as MEDIAN_HARI,
                        COUNT(*)                                             as N
                    FROM PENERBIT P
                    JOIN PENERBIT_ISBN PI ON P.ID = PI.PENERBIT_ID
                        AND PI.CREATEDATE >= TO_DATE('$start_date', 'YYYY-MM-DD')
                        AND PI.CREATEDATE <  TO_DATE('$end_date',   'YYYY-MM-DD')
                        AND (PI.KETERANGAN IS NULL OR UPPER(PI.KETERANGAN) NOT LIKE '%LENGKAP%')
                    WHERE PI.CREATEDATE >= TO_DATE('$cutoff','YYYY-MM-DD')
                    AND PI.TANGGAL_TERBIT IS NOT NULL
                    AND PI.TANGGAL_TERBIT >= PI.CREATEDATE
                    $whereProvinsi
                ";
                $kf = (array) odbc_fetch_object(odbc_exec($conn, str_replace('RECEIVED_DATE_KCKR', $kckrCol, $sqlKonfirm)));
                $konfirmasi = [
                    'avg'    => (float) ($kf['AVG_HARI'] ?? 0),
                    'median' => (float) ($kf['MEDIAN_HARI'] ?? 0),
                    'n'      => (int) ($kf['N'] ?? 0),
                ];

                return compact('current', 'previous', 'aging', 'konfirmasi');
            });

            $data['prev_label'] = $prevStart . ' s.d. ' . date('Y-m-d', strtotime($prevEnd) - 86400);
            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function chartData(Request $request)
    {
        try {
            $conn          = $this->getOracleConnection();
            $ctx           = $this->resolveProvinceContext($request->province_ids ?? [], $request->get('kckr_mode', 'perpusnas'));
            $provinceIds   = $ctx['provinceIds'];
            $kckrCol       = $ctx['kckrCol'];
            $whereProvinsi = $this->buildProvinceWhere($provinceIds);

            // Tentukan mode & rentang dari filter utama dashboard
            $filterType = $request->get('filter_type', 'range');

            if ($filterType === 'tahun') {
                $year      = (int) $request->get('filter_year', date('Y'));
                $startDate = "$year-01-01";
                $endDate   = "$year-12-31";
                $granularity = 'bulan';
            } elseif ($filterType === 'bulan') {
                $year  = (int) $request->get('filter_year', date('Y'));
                $month = (int) $request->get('filter_month', date('n'));
                $startDate = sprintf('%04d-%02d-01', $year, $month);
                $endDate   = date('Y-m-t', strtotime($startDate));
                $granularity = 'hari';
            } else {
                // range — auto-detect granularitas berdasarkan lebar rentang
                $startDate = $request->get('start_date', date('Y') - 4 . '-01-01');
                $endDate   = $request->get('end_date',   date('Y') . '-12-31');
                $diffDays  = (int) ((strtotime($endDate) - strtotime($startDate)) / 86400);
                if ($diffDays <= 93) {
                    $granularity = 'hari';
                } elseif ($diffDays <= 730) {
                    $granularity = 'bulan';
                } else {
                    $granularity = 'tahun';
                }
            }

            $cacheKey = $this->makeCacheKey($request, 'dashboard:chart2', [
                'filter_type', 'filter_year', 'filter_month', 'start_date', 'end_date', 'province_ids', 'kckr_mode',
            ]);

            $data = Cache::remember($cacheKey, 3600, function () use (
                $conn, $granularity, $startDate, $endDate, $whereProvinsi, $kckrCol
            ) {
                $sdSafe = preg_replace('/[^0-9\-]/', '', $startDate);
                $edSafe = preg_replace('/[^0-9\-]/', '', $endDate);

                if ($granularity === 'hari') {
                    $sql = "
                        SELECT
                            TO_CHAR(PI.CREATEDATE, 'YYYY-MM-DD') as PERIODE,
                            COUNT(PI.ID)                         as TOTAL_ISBN,
                            COUNT(PI.RECEIVED_DATE_KCKR)         as SUDAH_KCKR
                        FROM PENERBIT P
                        JOIN PENERBIT_ISBN PI ON P.ID = PI.PENERBIT_ID
                            AND PI.CREATEDATE >= TO_DATE('$sdSafe','YYYY-MM-DD')
                            AND PI.CREATEDATE <  TO_DATE('$edSafe','YYYY-MM-DD') + 1
                            AND (PI.KETERANGAN IS NULL OR UPPER(PI.KETERANGAN) NOT LIKE '%LENGKAP%')
                        WHERE 1=1 $whereProvinsi
                        GROUP BY TO_CHAR(PI.CREATEDATE, 'YYYY-MM-DD')
                        ORDER BY PERIODE
                    ";
                } elseif ($granularity === 'bulan') {
                    $sql = "
                        SELECT
                            TO_CHAR(PI.CREATEDATE, 'YYYY-MM')   as PERIODE,
                            COUNT(PI.ID)                         as TOTAL_ISBN,
                            COUNT(PI.RECEIVED_DATE_KCKR)         as SUDAH_KCKR
                        FROM PENERBIT P
                        JOIN PENERBIT_ISBN PI ON P.ID = PI.PENERBIT_ID
                            AND PI.CREATEDATE >= TO_DATE('$sdSafe','YYYY-MM-DD')
                            AND PI.CREATEDATE <  TO_DATE('$edSafe','YYYY-MM-DD') + 1
                            AND (PI.KETERANGAN IS NULL OR UPPER(PI.KETERANGAN) NOT LIKE '%LENGKAP%')
                        WHERE 1=1 $whereProvinsi
                        GROUP BY TO_CHAR(PI.CREATEDATE, 'YYYY-MM')
                        ORDER BY PERIODE
                    ";
                } else {
                    $sql = "
                        SELECT
                            TO_CHAR(PI.CREATEDATE, 'YYYY')      as PERIODE,
                            COUNT(PI.ID)                         as TOTAL_ISBN,
                            COUNT(PI.RECEIVED_DATE_KCKR)         as SUDAH_KCKR
                        FROM PENERBIT P
                        JOIN PENERBIT_ISBN PI ON P.ID = PI.PENERBIT_ID
                            AND PI.CREATEDATE >= TO_DATE('$sdSafe','YYYY-MM-DD')
                            AND PI.CREATEDATE <  TO_DATE('$edSafe','YYYY-MM-DD') + 1
                            AND (PI.KETERANGAN IS NULL OR UPPER(PI.KETERANGAN) NOT LIKE '%LENGKAP%')
                        WHERE 1=1 $whereProvinsi
                        GROUP BY TO_CHAR(PI.CREATEDATE, 'YYYY')
                        ORDER BY PERIODE
                    ";
                }

                $result = odbc_exec($conn, str_replace('RECEIVED_DATE_KCKR', $kckrCol, $sql));
                $rows   = [];
                while ($row = odbc_fetch_object($result)) {
                    $rows[] = $row;
                }
                return $rows;
            });

            // Format label
            $bulanNames = ['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei','06'=>'Jun',
                           '07'=>'Jul','08'=>'Agu','09'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'];
            $labels = [];
            $isbn   = [];
            $kckr   = [];

            foreach ($data as $row) {
                if ($granularity === 'hari') {
                    // YYYY-MM-DD → DD Mon
                    $parts = explode('-', $row->PERIODE);
                    $labels[] = (int)$parts[2] . ' ' . ($bulanNames[$parts[1]] ?? $parts[1]);
                } elseif ($granularity === 'bulan') {
                    // YYYY-MM → Mon YYYY (atau Mon saja jika satu tahun)
                    $parts    = explode('-', $row->PERIODE);
                    $labels[] = ($bulanNames[$parts[1]] ?? $parts[1]) . ' ' . $parts[0];
                } else {
                    $labels[] = $row->PERIODE;
                }
                $isbn[] = (int) $row->TOTAL_ISBN;
                $kckr[] = (int) $row->SUDAH_KCKR;
            }

            return response()->json(compact('labels', 'isbn', 'kckr', 'granularity'));

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
