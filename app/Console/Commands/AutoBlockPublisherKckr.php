<?php

namespace App\Console\Commands;

use App\Helpers\ComplianceSettings;
use App\Traits\OracleHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoBlockPublisherKckr extends Command
{
    use OracleHelper;

    protected $signature = 'app:auto-block-publisher-kckr
                            {--dry-run   : Tampilkan ringkasan dan preview tanpa eksekusi}
                            {--min-pct=  : Override batas minimum kepatuhan KCKR (default dari settings)}
                            {--chunk=100 : Jumlah penerbit per batch commit}
                            {--preview=20: Jumlah baris preview saat dry-run}';

    protected $description = 'Auto blokir / buka blokir penerbit berdasarkan kepatuhan KCKR (dihitung dari 2021)';

    private const START_DATE = '2021-01-01';
    private const CUTOFF     = '2026-01-01';
    private const ACTOR      = 'SISTEM';

    public function handle(): int
    {
        $isDryRun   = (bool) $this->option('dry-run');
        $chunkSize  = max(1, (int) ($this->option('chunk')   ?? 100));
        $previewMax = max(1, (int) ($this->option('preview') ?? 20));
        $terminal   = gethostname() ?: '127.0.0.1';
        $now        = date('Y-m-d H:i:s');
        $dateNow    = date('Y-m-d');

        $this->line('');
        $this->info('╔══════════════════════════════════════════╗');
        $this->info('║   Auto Blokir / Buka Blokir KCKR         ║');
        $this->info('╚══════════════════════════════════════════╝');
        $this->line("  Periode  : " . self::START_DATE . " s.d. {$dateNow}");
        $this->line("  Cutoff   : " . self::CUTOFF . " (pra-2026 vs 2026+)");
        $this->line("  Mode     : " . ($isDryRun ? 'DRY RUN — tidak ada yang akan diubah' : "EKSEKUSI (chunk {$chunkSize}/batch)"));
        $this->line('');

        try {
            $cs = ComplianceSettings::get();

            // ── Cek apakah auto blokir diaktifkan ─────────────────────────────
            if (!$isDryRun) {
                if (($cs['AutoBlokir_Aktif'] ?? '0') != '1') {
                    $this->warn("Auto blokir tidak aktif (dinonaktifkan dari pengaturan). Proses dihentikan.");
                    return 0;
                }

                $mulaiTanggal = $cs['AutoBlokir_MulaiTanggal'] ?? null;
                if ($mulaiTanggal && $dateNow < $mulaiTanggal) {
                    $this->warn("Auto blokir belum aktif. Mulai berlaku: {$mulaiTanggal}. Proses dihentikan.");
                    return 0;
                }
            }

            $minPct = $this->option('min-pct') !== null
                ? (int) $this->option('min-pct')
                : (int) $cs['BatasMinimumKepatuhanKCKR'];

            $this->line("  Min KCKR : {$minPct}%");
            $this->line('');

            $conn = $this->getOracleConnection();

            // ── Fase 1: Blokir ────────────────────────────────────────────────
            $this->line('─── Fase 1: Blokir ───────────────────────────────────');
            $toBlock = $this->fetchCandidates(
                $conn,
                $this->buildBlockSql($minPct, self::START_DATE, $dateNow, self::CUTOFF)
            );
            $this->showSummary($toBlock, $minPct, $previewMax, 'blokir');

            // ── Fase 2: Buka Blokir ───────────────────────────────────────────
            $this->line('─── Fase 2: Buka Blokir ──────────────────────────────');
            $toUnblock = $this->fetchCandidates(
                $conn,
                $this->buildUnblockSql($minPct, self::START_DATE, $dateNow, self::CUTOFF)
            );
            $this->showSummary($toUnblock, $minPct, $previewMax, 'buka');

            if ($isDryRun) {
                $this->line('');
                $this->warn("Dry-run selesai. Jalankan tanpa --dry-run untuk eksekusi.");
                $totalEst = count($toBlock) + count($toUnblock);
                if ($totalEst > 0) {
                    $this->line("  Estimasi batch  : ~" . ceil($totalEst / $chunkSize) . " batch × {$chunkSize} penerbit.");
                }
                return 0;
            }

            if (count($toBlock) === 0 && count($toUnblock) === 0) {
                $this->info("✓ Tidak ada perubahan yang diperlukan. Selesai.");
                return 0;
            }

            // ── Konfirmasi interaktif ─────────────────────────────────────────
            if ($this->input->isInteractive()) {
                $msg = implode(' dan ', array_filter([
                    count($toBlock)   > 0 ? count($toBlock)   . ' diblokir' : null,
                    count($toUnblock) > 0 ? count($toUnblock) . ' dibuka'   : null,
                ]));
                if (!$this->confirm("Lanjutkan ({$msg})?", false)) {
                    $this->warn("Dibatalkan.");
                    return 0;
                }
            }

            // ── Eksekusi blokir ───────────────────────────────────────────────
            $this->line('');
            [$blocked, $blockFail] = $this->executeChunked(
                $conn, $toBlock, $chunkSize, $now, $terminal, $minPct, $dateNow,
                is_lock: 1,
                action:  'AUTO BLOKIR KCKR',
                reasonFn: fn($pub, $minPct, $dateNow) =>
                    "Auto blokir sistem: kepatuhan KCKR {$pub->PERSENTASE_KCKR}% "
                    . "(batas minimum {$minPct}%), "
                    . "{$pub->TERLAMBAT_KCKR} dari {$pub->TOTAL_JUDUL} judul terlambat KCKR, "
                    . "periode 2021 s.d. {$dateNow}",
                label: 'Memblokir'
            );

            // ── Eksekusi buka blokir ──────────────────────────────────────────
            $this->line('');
            [$unblocked, $unblockFail] = $this->executeChunked(
                $conn, $toUnblock, $chunkSize, $now, $terminal, $minPct, $dateNow,
                is_lock: 0,
                action:  'AUTO BUKA BLOKIR KCKR',
                reasonFn: fn($pub, $minPct, $dateNow) =>
                    "Auto buka blokir sistem: kepatuhan KCKR {$pub->PERSENTASE_KCKR}% "
                    . "sudah melebihi batas minimum {$minPct}%, "
                    . "periode 2021 s.d. {$dateNow}",
                label: 'Membuka blokir'
            );

            // ── Ringkasan akhir ───────────────────────────────────────────────
            $this->line('');
            $this->info("✓ Selesai:");
            $this->line("  Diblokir    : {$blocked}" . ($blockFail   > 0 ? " ({$blockFail} gagal)"   : ''));
            $this->line("  Dibuka      : {$unblocked}" . ($unblockFail > 0 ? " ({$unblockFail} gagal)" : ''));

            Log::channel('daily')->info('AutoBlockPublisherKckr: selesai', [
                'tanggal'        => $dateNow,
                'diblokir'       => $blocked,
                'block_gagal'    => $blockFail,
                'dibuka'         => $unblocked,
                'unblock_gagal'  => $unblockFail,
                'min_pct'        => $minPct,
            ]);

        } catch (\Throwable $e) {
            $this->error("Error fatal: " . $e->getMessage());
            Log::channel('daily')->error('AutoBlockPublisherKckr: error fatal', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return 1;
        }

        return 0;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function fetchCandidates($conn, string $sql): array
    {
        $result = odbc_exec($conn, $sql);
        if (!$result) {
            throw new \RuntimeException("Query gagal: " . odbc_errormsg($conn));
        }
        $rows = [];
        while ($row = odbc_fetch_object($result)) {
            $rows[] = $row;
        }
        return $rows;
    }

    private function showSummary(array $candidates, int $minPct, int $previewMax, string $mode): void
    {
        $total = count($candidates);
        $label = $mode === 'blokir' ? 'diblokir' : 'dibuka blokir';

        if ($total === 0) {
            $this->line("  Tidak ada penerbit yang perlu {$label}.");
            $this->line('');
            return;
        }

        $this->info("  Ditemukan {$total} penerbit yang akan {$label}:");
        $this->line('');

        $previewCount = min($previewMax, $total);
        $previewRows  = array_map(fn($r) => [
            $r->ID,
            mb_strimwidth($r->NAME, 0, 38, '…'),
            $r->TOTAL_JUDUL,
            $r->SUDAH_KCKR,
            $r->TERLAMBAT_KCKR,
            $r->PERSENTASE_KCKR . '%',
        ], array_slice($candidates, 0, $previewCount));

        $this->table(
            ['ID', 'Nama Penerbit', 'Total Judul', 'Sudah KCKR', 'Terlambat', '% KCKR'],
            $previewRows
        );

        if ($total > $previewMax) {
            $this->line("  … dan " . ($total - $previewMax) . " penerbit lainnya (tidak ditampilkan).");
        }
        $this->line('');
    }

    private function executeChunked(
        $conn,
        array $candidates,
        int $chunkSize,
        string $now,
        string $terminal,
        int $minPct,
        string $dateNow,
        int $is_lock,
        string $action,
        \Closure $reasonFn,
        string $label
    ): array {
        $total = count($candidates);
        if ($total === 0) {
            return [0, 0];
        }

        $done   = 0;
        $failed = 0;
        $chunks = array_chunk($candidates, $chunkSize);

        $bar = $this->output->createProgressBar($total);
        $bar->setFormat(" {$label}: %current%/%max% [%bar%] %percent:3s%%");
        $bar->start();

        foreach ($chunks as $chunk) {
            $ids = implode(',', array_map(fn($r) => (int) $r->ID, $chunk));

            $sqlUpdate = "UPDATE PENERBIT
                          SET    is_lock        = {$is_lock},
                                 updateby       = '" . self::ACTOR . "',
                                 updatedate     = TO_DATE('{$now}','YYYY-MM-DD HH24:MI:SS'),
                                 updateterminal = '{$terminal}'
                          WHERE  ID IN ({$ids})";

            $resUpdate = odbc_exec($conn, $sqlUpdate);

            if (!$resUpdate) {
                $errMsg = odbc_errormsg($conn);
                foreach ($chunk as $pub) {
                    Log::channel('daily')->error("AutoBlockPublisherKckr: UPDATE gagal ({$action})", [
                        'penerbit_id' => (int) $pub->ID,
                        'error'       => $errMsg,
                    ]);
                }
                $failed += count($chunk);
                $bar->advance(count($chunk));
                continue;
            }

            foreach ($chunk as $pub) {
                $id         = (int) $pub->ID;
                $reason     = $reasonFn($pub, $minPct, $dateNow);
                $reasonSafe = str_replace("'", "''", $reason);

                $sqlHist = "INSERT INTO historydata
                                (action, tablename, actionby, actiondate, actionterminal, note, idref)
                            VALUES (
                                '{$action}',
                                'PENERBIT',
                                '" . self::ACTOR . "',
                                TO_DATE('{$now}','YYYY-MM-DD HH24:MI:SS'),
                                '{$terminal}',
                                '{$reasonSafe}',
                                {$id}
                            )";

                $resHist = odbc_exec($conn, $sqlHist);
                if (!$resHist) {
                    Log::channel('daily')->warning("AutoBlockPublisherKckr: INSERT historydata gagal ({$action})", [
                        'penerbit_id' => $id,
                        'error'       => odbc_errormsg($conn),
                    ]);
                }

                $done++;
                $bar->advance();
            }
        }

        $bar->finish();
        return [$done, $failed];
    }

    // ── SQL Builders ──────────────────────────────────────────────────────────

    private function baseExpressions(string $cutoff): array
    {
        $isPre26 = "PI.CREATEDATE <  TO_DATE('{$cutoff}','YYYY-MM-DD')";
        $is2026  = "PI.CREATEDATE >= TO_DATE('{$cutoff}','YYYY-MM-DD')";

        $dlKckrV1 = "CASE WHEN P.KATEGORI_ID = 1
                          THEN ADD_MONTHS(PI.CREATEDATE, 3)
                          ELSE CASE WHEN PT.JENIS_MEDIA = '1'
                                    THEN ADD_MONTHS(PI.CREATEDATE, 3)
                                    ELSE ADD_MONTHS(PI.CREATEDATE, 12) END
                     END";

        $dlKckrV2 = "CASE WHEN P.KATEGORI_ID = 1
                          THEN ADD_MONTHS(PI.TANGGAL_TERBIT, 3)
                          ELSE CASE WHEN PT.JENIS_MEDIA = '1'
                                    THEN ADD_MONTHS(PI.TANGGAL_TERBIT, 3)
                                    ELSE ADD_MONTHS(PI.TANGGAL_TERBIT, 12) END
                     END";

        // Identik dengan ComplianceV3Controller
        $tagihan = "SUM(CASE
            WHEN {$isPre26} AND PI.RECEIVED_DATE_KCKR IS NULL THEN 1
            WHEN {$is2026}  AND PI.TANGGAL_TERBIT IS NOT NULL AND PI.RECEIVED_DATE_KCKR IS NULL THEN 1
            ELSE 0 END)";

        $sudahKckr  = "SUM(CASE WHEN PI.RECEIVED_DATE_KCKR IS NOT NULL THEN 1 ELSE 0 END)";
        $totalWajib = "({$sudahKckr} + {$tagihan})";

        $terlambat = "SUM(CASE
            WHEN {$isPre26} AND (
                (PI.RECEIVED_DATE_KCKR IS NOT NULL AND PI.RECEIVED_DATE_KCKR > ({$dlKckrV1}))
             OR (PI.RECEIVED_DATE_KCKR IS NULL    AND SYSDATE > ({$dlKckrV1}))
            ) THEN 1
            WHEN {$is2026} AND PI.TANGGAL_TERBIT IS NOT NULL AND (
                (PI.RECEIVED_DATE_KCKR IS NOT NULL AND PI.RECEIVED_DATE_KCKR > ({$dlKckrV2}))
             OR (PI.RECEIVED_DATE_KCKR IS NULL    AND SYSDATE > ({$dlKckrV2}))
            ) THEN 1
            ELSE 0 END)";

        $persentase = "ROUND(
            CASE WHEN {$totalWajib} > 0
                THEN {$sudahKckr} / {$totalWajib} * 100
                ELSE 0 END, 1
        )";

        return compact('tagihan', 'sudahKckr', 'totalWajib', 'terlambat', 'persentase');
    }

    private function buildInnerSql(string $startDate, string $endDate, string $cutoff, string $lockWhere): string
    {
        $e = $this->baseExpressions($cutoff);

        // HAVING dan COUNT(DISTINCT) identik dengan dashboard agar angka konsisten
        return "
            SELECT
                P.ID,
                P.NAME,
                COUNT(DISTINCT PI.ID) AS TOTAL_JUDUL,
                {$e['sudahKckr']}     AS SUDAH_KCKR,
                {$e['tagihan']}       AS BELUM_KCKR,
                {$e['terlambat']}     AS TERLAMBAT_KCKR,
                {$e['persentase']}    AS PERSENTASE_KCKR
            FROM PENERBIT P
            LEFT JOIN PENERBIT_ISBN PI
                   ON P.ID = PI.PENERBIT_ID
                  AND PI.CREATEDATE >= TO_DATE('{$startDate}','YYYY-MM-DD')
                  AND PI.CREATEDATE <  TO_DATE('{$endDate}','YYYY-MM-DD') + 1
                  AND (PI.KETERANGAN IS NULL OR UPPER(PI.KETERANGAN) NOT LIKE '%LENGKAP%')
            LEFT JOIN PENERBIT_TERBITAN PT ON PI.PENERBIT_TERBITAN_ID = PT.ID
            WHERE {$lockWhere}
            GROUP BY P.ID, P.NAME
            HAVING ({$e['sudahKckr']} + {$e['tagihan']}) > 0
        ";
    }

    // Kandidat blokir: belum ter-lock, TERLAMBAT > 0, % KCKR <= minPct
    private function buildBlockSql(int $minPct, string $startDate, string $endDate, string $cutoff): string
    {
        $inner = $this->buildInnerSql($startDate, $endDate, $cutoff,
            lockWhere: "(P.is_lock IS NULL OR P.is_lock != 1)"
        );
        return "SELECT ID, NAME, TOTAL_JUDUL, SUDAH_KCKR, BELUM_KCKR, TERLAMBAT_KCKR, PERSENTASE_KCKR
                FROM ({$inner})
                WHERE TERLAMBAT_KCKR > 0 AND PERSENTASE_KCKR <= {$minPct}
                ORDER BY PERSENTASE_KCKR ASC, TERLAMBAT_KCKR DESC";
    }

    // Kandidat buka blokir: sedang ter-lock, % KCKR sudah > minPct
    private function buildUnblockSql(int $minPct, string $startDate, string $endDate, string $cutoff): string
    {
        $inner = $this->buildInnerSql($startDate, $endDate, $cutoff,
            lockWhere: "P.is_lock = 1"
        );
        return "SELECT ID, NAME, TOTAL_JUDUL, SUDAH_KCKR, BELUM_KCKR, TERLAMBAT_KCKR, PERSENTASE_KCKR
                FROM ({$inner})
                WHERE PERSENTASE_KCKR > {$minPct}
                ORDER BY PERSENTASE_KCKR DESC";
    }
}
