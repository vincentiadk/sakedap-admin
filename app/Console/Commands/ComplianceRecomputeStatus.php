<?php

namespace App\Console\Commands;

use App\Helpers\ComplianceSettings;
use App\Traits\OracleHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ComplianceRecomputeStatus extends Command
{
    use OracleHelper;

    protected $signature = 'compliance:recompute-status
                            {--dry-run    : Tampilkan ringkasan tanpa eksekusi}
                            {--chunk=200  : Jumlah penerbit per batch}
                            {--preview=20 : Jumlah baris preview saat dry-run}
                            {--kckr-block : Paksa aktifkan klasifikasi & kunci Blokir SSKCKR terlepas dari setting AutoBlokir_Aktif (override manual, buat testing). Operasional normal cukup diatur lewat Pengaturan Kepatuhan, tidak perlu flag ini.}
                            {--reset-notif : Reset penanda email blokir (IS_NOTIF_BLOKIR_*) saat blokir dicabut (default: mati, kolom notifikasi tidak disentuh)}';

    protected $description = 'Hitung ulang status kepatuhan penerbit dan simpan riwayatnya ke PENERBIT_STATUS. Klasifikasi & kunci Blokir SSKCKR mengikuti setting AutoBlokir_Aktif/AutoBlokir_MulaiTanggal di Pengaturan Kepatuhan (atau dipaksa lewat --kckr-block).';

    private const START_DATE = '2021-01-01';
    private const CUTOFF     = '2026-01-01';
    private const ACTOR      = 'SISTEM';

    private const STATUS_AKTIF          = 'Aktif';
    private const STATUS_BLOKIR_TERBIT  = 'Blokir Konfirmasi Terbit';
    private const STATUS_BLOKIR_KCKR    = 'Blokir SSKCKR';
    private const STATUS_BLOKIR_KEDUANYA = 'Blokir Konfirmasi Terbit dan SSKCKR';

    public function handle(): int
    {
        $isDryRun    = (bool) $this->option('dry-run');
        $chunkSize   = max(1, (int) ($this->option('chunk')   ?? 200));
        $previewMax  = max(1, (int) ($this->option('preview') ?? 20));
        $kckrBlockOn = (bool) $this->option('kckr-block');
        $resetNotifOn = (bool) $this->option('reset-notif');
        $terminal    = gethostname() ?: '127.0.0.1';
        $now         = date('Y-m-d H:i:s');

        $tStart = microtime(true);
        $lap    = fn() => round(microtime(true) - $tStart, 2);

        $this->line('');
        $this->info('╔══════════════════════════════════════════╗');
        $this->info('║   Recompute Status Kepatuhan Penerbit    ║');
        $this->info('╚══════════════════════════════════════════╝');
        $this->line("  Mode        : " . ($isDryRun ? 'DRY RUN — tidak ada yang akan diubah' : "EKSEKUSI (chunk {$chunkSize}/batch)"));
        $this->line("  Reset notif   : " . ($resetNotifOn ? 'AKTIF — penanda email blokir direset saat blokir dicabut' : 'NONAKTIF (default) — kolom IS_NOTIF_*/TGL_NOTIF_* tidak disentuh'));
        $this->line('');

        try {
            $cs      = ComplianceSettings::get();
            $minPct  = (int) $cs['BatasMinimumKepatuhanKCKR'];
            $dlKc    = (int) $cs['BatasWaktuKonfirmasiTerbitKaryaCetak'];
            $dlKr    = (int) $cs['BatasWaktuKonfirmasiTerbitDigital'];
            $teguran = (int) $cs['BatasWaktuTeguranKonfirmasiTerbit'];

            // Klasifikasi & kunci Blokir SSKCKR aktif kalau SALAH SATU benar:
            // (a) setting AutoBlokir_Aktif+AutoBlokir_MulaiTanggal di Pengaturan
            //     Kepatuhan mengizinkan — ini jalur operasional normal, murni
            //     dikendalikan admin lewat halaman, tanpa perlu ubah cron/kode; atau
            // (b) --kckr-block dipasang manual — override buat testing/debug,
            //     supaya bisa dicoba tanpa mengubah setting yang berlaku produksi.
            $autoBlokirActive = ComplianceSettings::isAutoBlokirActive($cs);
            $kckrActive       = $kckrBlockOn || $autoBlokirActive;

            $this->line("  Min KCKR : {$minPct}%");
            $this->line("  Blokir SSKCKR : " . ($kckrActive ? 'AKTIF' . ($kckrBlockOn && !$autoBlokirActive ? ' (dipaksa via --kckr-block)' : '') : 'NONAKTIF — hanya Blokir Konfirmasi Terbit yang jalan'));
            $this->line("  Auto Blokir (setting) : " . ($autoBlokirActive ? 'AKTIF' : 'NONAKTIF/belum mulai'));
            $this->line('');

            $conn = $this->getOracleConnection();

            $tQuery = microtime(true);
            $rows   = $this->fetchCurrentStatus($conn, $dlKc, $dlKr, $teguran);
            $queryDuration = round(microtime(true) - $tQuery, 2);
            $this->line("  Query hitung status : {$queryDuration}s (" . count($rows) . " penerbit)");
            $this->line('');

            $diffs = [];
            foreach ($rows as $row) {
                $newStatus = $this->classify(
                    (int) $row->LEWAT_TEGURAN,
                    (int) $row->TERLAMBAT_KCKR,
                    (float) $row->PERSENTASE_KCKR,
                    $minPct,
                    $kckrActive
                );
                $oldStatus = trim((string) ($row->STATUS_AKHIR ?? ''));
                $oldStatus = $oldStatus === '' ? null : $oldStatus;

                $currentLock = (int) ($row->IS_LOCK ?? 0);

                // is_lock hanya dihitung ulang dari angka mentah SSKCKR kalau klasifikasi
                // SSKCKR aktif ($kckrActive — lihat penjelasan di atas). Kalau tidak,
                // is_lock dibiarkan apa adanya (tidak diubah).
                $targetLock = $kckrActive
                    ? (((int) $row->TERLAMBAT_KCKR > 0 && (float) $row->PERSENTASE_KCKR <= $minPct) ? 1 : 0)
                    : $currentLock;

                $labelChanged = $oldStatus !== $newStatus;
                $lockChanged  = $currentLock !== $targetLock;

                if ($labelChanged || $lockChanged) {
                    $diffs[] = [
                        'id'            => (int) $row->ID,
                        'name'          => $row->NAME,
                        'old'           => $oldStatus,
                        'new'           => $newStatus,
                        'label_changed' => $labelChanged,
                        'old_lock'      => $currentLock,
                        'new_lock'      => $targetLock,
                        'lewat'         => (int) $row->LEWAT_TEGURAN,
                        'terlambat'     => (int) $row->TERLAMBAT_KCKR,
                        'pct'           => (float) $row->PERSENTASE_KCKR,
                        // Penanda email blokir yang sudah menyala; dipakai untuk
                        // memperingatkan kalau --reset-notif dimatikan padahal
                        // penerbit ini butuh direset.
                        'notif_kt'      => (int) ($row->IS_NOTIF_BLOKIR_KT_EMAIL ?? 0),
                        'notif_kckr'    => (int) ($row->IS_NOTIF_BLOKIR_KCKR_EMAIL ?? 0),
                    ];
                }
            }

            $total = count($diffs);
            $this->info("  Dievaluasi : " . count($rows) . " penerbit");
            $this->line("  Berubah    : {$total} penerbit (status dan/atau is_lock)");
            $this->line('');

            // Mematikan --reset-notif aman selama belum ada email blokir terkirim.
            // Begitu ada, penanda yang tertinggal menyala akan membuat siklus blokir
            // berikutnya lewat tanpa pemberitahuan — jadi kondisinya diteriakkan,
            // bukan didiamkan.
            if (!$resetNotifOn) {
                $perluReset = count(array_filter($diffs, fn($d) => $d['label_changed']
                    && $this->resetNotifClause($d['new']) !== ''
                    && ($d['notif_kt'] === 1 || $d['notif_kckr'] === 1)));

                if ($perluReset > 0) {
                    $this->warn("  PERHATIAN: {$perluReset} penerbit blokirnya dicabut tapi penanda email blokirnya masih menyala.");
                    $this->warn("  Tanpa --reset-notif mereka tidak akan dikirimi email lagi saat diblokir berikutnya.");
                    $this->line('');
                }
            }

            if ($total === 0) {
                $this->info('✓ Tidak ada perubahan status. Selesai.');
                return 0;
            }

            $previewCount = min($previewMax, $total);
            $previewRows  = array_map(fn($d) => [
                $d['id'],
                mb_strimwidth($d['name'] ?? '-', 0, 32, '…'),
                $d['old'] ?? '(belum ada)',
                $d['new'],
                $d['old_lock'] . ' → ' . $d['new_lock'],
                $d['pct'] . '%',
            ], array_slice($diffs, 0, $previewCount));

            $this->table(['ID', 'Nama Penerbit', 'Status Lama', 'Status Baru', 'Lock', '% KCKR'], $previewRows);
            if ($total > $previewMax) {
                $this->line("  … dan " . ($total - $previewMax) . " penerbit lainnya (tidak ditampilkan).");
            }
            $this->line('');

            if ($isDryRun) {
                $this->warn("Dry-run selesai. Jalankan tanpa --dry-run untuk eksekusi.");
                $this->line("  Estimasi batch  : ~" . ceil($total / $chunkSize) . " batch × {$chunkSize} penerbit.");
                $this->line("  Total waktu     : {$lap()}s");
                return 0;
            }

            $tWrite = microtime(true);
            [$done, $failed] = $this->executeChunked($conn, $diffs, $chunkSize, $now, $terminal, $minPct, $resetNotifOn);
            $writeDuration = round(microtime(true) - $tWrite, 2);
            $totalDuration = $lap();

            $this->line('');
            $this->info("✓ Selesai: {$done} status diperbarui" . ($failed > 0 ? ", {$failed} gagal" : '') . '.');
            $this->line("  Waktu query  : {$queryDuration}s");
            $this->line("  Waktu tulis  : {$writeDuration}s ({$total} baris)");
            $this->line("  Total waktu  : {$totalDuration}s");

            Log::channel('daily')->info('ComplianceRecomputeStatus: selesai', [
                'dievaluasi'     => count($rows),
                'berubah'        => $total,
                'sukses'         => $done,
                'gagal'          => $failed,
                'min_pct'        => $minPct,
                'durasi_query_s' => $queryDuration,
                'durasi_tulis_s' => $writeDuration,
                'durasi_total_s' => $totalDuration,
            ]);

        } catch (\Throwable $e) {
            $this->error("Error fatal: " . $e->getMessage() . " (setelah {$lap()}s)");
            Log::channel('daily')->error('ComplianceRecomputeStatus: error fatal', [
                'error'          => $e->getMessage(),
                'trace'          => $e->getTraceAsString(),
                'durasi_total_s' => $lap(),
            ]);
            return 1;
        }

        return 0;
    }

    // ── Klasifikasi ──────────────────────────────────────────────────────────
    // Identik dengan logika rekomendasi di ComplianceV3Controller/DashboardComplianceController.
    //
    // $kckrActive = false: kriteria SSKCKR (terlambat KCKR + % KCKR) sama sekali
    // tidak dipakai — hasil hanya Aktif atau Blokir Konfirmasi Terbit. Status "Blokir SSKCKR"
    // dan "Blokir Konfirmasi Terbit dan SSKCKR" tidak akan pernah muncul sampai
    // AutoBlokir_Aktif dinyalakan (atau dipaksa lewat --kckr-block).

    private function classify(int $lewatTeguran, int $terlambatKckr, float $persentaseKckr, int $minPct, bool $kckrActive = false): string
    {
        if (!$kckrActive) {
            return $lewatTeguran > 0 ? self::STATUS_BLOKIR_TERBIT : self::STATUS_AKTIF;
        }

        if ($lewatTeguran > 0 && $terlambatKckr > 0 && $persentaseKckr <= $minPct) {
            return self::STATUS_BLOKIR_KEDUANYA;
        }
        if ($lewatTeguran > 0) {
            return self::STATUS_BLOKIR_TERBIT;
        }
        if ($terlambatKckr > 0 && $persentaseKckr <= $minPct) {
            return self::STATUS_BLOKIR_KCKR;
        }
        return self::STATUS_AKTIF;
    }

    // ── Query ────────────────────────────────────────────────────────────────
    // Window kumulatif (dari awal data s.d. sekarang), tanpa filter provinsi/kategori —
    // ini merepresentasikan status kepatuhan penerbit yang sebenarnya, bukan status
    // pada periode laporan tertentu. Sumber KCKR = RECEIVED_DATE_KCKR (Perpusnas).

    private function fetchCurrentStatus($conn, int $dlKc, int $dlKr, int $teguran): array
    {
        $startDate = self::START_DATE;
        $cutoff    = self::CUTOFF;
        $endDate   = date('Y-m-d', strtotime('+1 day'));

        $isPre26 = "PI.CREATEDATE <  TO_DATE('$cutoff','YYYY-MM-DD')";
        $is2026  = "PI.CREATEDATE >= TO_DATE('$cutoff','YYYY-MM-DD')";

        $dlTerbit = "CASE WHEN PT.JENIS_MEDIA = '1' THEN (PI.CREATEDATE + $dlKc) ELSE (PI.CREATEDATE + $dlKr) END";
        $dlKckrV1 = "CASE WHEN P.KATEGORI_ID = 1 THEN ADD_MONTHS(PI.CREATEDATE, 3)
                          ELSE CASE WHEN PT.JENIS_MEDIA = '1' THEN ADD_MONTHS(PI.CREATEDATE, 3)
                                    ELSE ADD_MONTHS(PI.CREATEDATE, 12) END END";
        $dlKckrV2 = "CASE WHEN P.KATEGORI_ID = 1 THEN ADD_MONTHS(PI.TANGGAL_TERBIT, 3)
                          ELSE CASE WHEN PT.JENIS_MEDIA = '1' THEN ADD_MONTHS(PI.TANGGAL_TERBIT, 3)
                                    ELSE ADD_MONTHS(PI.TANGGAL_TERBIT, 12) END END";

        $belumTerbit = "(PI.TANGGAL_TERBIT IS NULL AND PI.RECEIVED_DATE_KCKR IS NULL)";

        $tagihan = "SUM(CASE
            WHEN $isPre26 AND PI.RECEIVED_DATE_KCKR IS NULL THEN 1
            WHEN $is2026  AND PI.TANGGAL_TERBIT IS NOT NULL AND PI.RECEIVED_DATE_KCKR IS NULL THEN 1
            ELSE 0 END)";
        $sudahKckr  = "SUM(CASE WHEN PI.RECEIVED_DATE_KCKR IS NOT NULL THEN 1 ELSE 0 END)";
        $totalWajib = "($sudahKckr + $tagihan)";

        $lewatTeguran = "SUM(CASE WHEN $is2026 AND $belumTerbit AND SYSDATE > ($dlTerbit + $teguran)
                               THEN 1 ELSE 0 END)";

        $terlambat = "SUM(CASE
            WHEN $isPre26 AND (
                (PI.RECEIVED_DATE_KCKR IS NOT NULL AND PI.RECEIVED_DATE_KCKR > ($dlKckrV1))
             OR (PI.RECEIVED_DATE_KCKR IS NULL    AND SYSDATE > ($dlKckrV1))
            ) THEN 1
            WHEN $is2026 AND PI.TANGGAL_TERBIT IS NOT NULL AND (
                (PI.RECEIVED_DATE_KCKR IS NOT NULL AND PI.RECEIVED_DATE_KCKR > ($dlKckrV2))
             OR (PI.RECEIVED_DATE_KCKR IS NULL    AND SYSDATE > ($dlKckrV2))
            ) THEN 1
            ELSE 0 END)";

        $persentase = "ROUND(CASE WHEN $totalWajib > 0 THEN $sudahKckr / $totalWajib * 100 ELSE 0 END, 1)";

        $sql = "
            SELECT
                P.ID, P.NAME, P.STATUS_AKHIR, P.IS_LOCK,
                P.IS_NOTIF_BLOKIR_KT_EMAIL, P.IS_NOTIF_BLOKIR_KCKR_EMAIL,
                $lewatTeguran AS LEWAT_TEGURAN,
                $terlambat    AS TERLAMBAT_KCKR,
                $persentase   AS PERSENTASE_KCKR
            FROM PENERBIT P
            LEFT JOIN PENERBIT_ISBN PI ON P.ID = PI.PENERBIT_ID
                AND PI.CREATEDATE >= TO_DATE('$startDate', 'YYYY-MM-DD')
                AND PI.CREATEDATE <  TO_DATE('$endDate', 'YYYY-MM-DD')
                AND (PI.KETERANGAN IS NULL OR UPPER(PI.KETERANGAN) NOT LIKE '%LENGKAP%')
            LEFT JOIN PENERBIT_TERBITAN PT ON PI.PENERBIT_TERBITAN_ID = PT.ID
            GROUP BY P.ID, P.NAME, P.STATUS_AKHIR, P.IS_LOCK,
                     P.IS_NOTIF_BLOKIR_KT_EMAIL, P.IS_NOTIF_BLOKIR_KCKR_EMAIL
            HAVING $totalWajib > 0 OR $lewatTeguran > 0
        ";

        $result = odbc_exec($conn, $sql);
        if (!$result) {
            throw new \RuntimeException("Query gagal: " . odbc_errormsg($conn));
        }

        $rows = [];
        while ($row = odbc_fetch_object($result)) {
            $rows[] = $row;
        }
        odbc_free_result($result);
        return $rows;
    }

    // ── Eksekusi ─────────────────────────────────────────────────────────────

    private function executeChunked($conn, array $diffs, int $chunkSize, string $now, string $terminal, int $minPct, bool $resetNotifOn = false): array
    {
        $total  = count($diffs);
        $done   = 0;
        $failed = 0;
        $chunks = array_chunk($diffs, $chunkSize);

        $bar = $this->output->createProgressBar($total);
        $bar->setFormat(' Memperbarui: %current%/%max% [%bar%] %percent:3s%%');
        $bar->start();

        foreach ($chunks as $chunk) {
            foreach ($chunk as $d) {
                $id         = $d['id'];
                $oldStatus  = $d['old'];
                $newStatus  = $d['new'];
                $newLock    = $d['new_lock'];
                $newSafe    = str_replace("'", "''", $newStatus);

                // Histori status hanya dicatat kalau LABEL benar-benar berubah.
                // Perubahan is_lock murni (tanpa perubahan label) tidak menulis riwayat baru.
                if ($d['label_changed']) {
                    $note        = "Lewat Teguran: {$d['lewat']} | Terlambat KCKR: {$d['terlambat']} ({$d['pct']}%) | Ambang KCKR: {$minPct}% | is_lock: {$d['old_lock']} --> {$newLock}";
                    $noteSafe    = str_replace("'", "''", $note);
                    $oldValueSql = $oldStatus === null ? 'NULL' : "'" . str_replace("'", "''", $oldStatus) . "'";

                    $sqlInsert = "INSERT INTO PENERBIT_STATUS
                                    (PENERBIT_ID, STATUSAWAL, STATUSAKHIR, NOTE, CREATEDATE, CREATEBY, CREATETERMINAL)
                                  VALUES (
                                    {$id},
                                    {$oldValueSql},
                                    '{$newSafe}',
                                    '{$noteSafe}',
                                    TO_DATE('{$now}','YYYY-MM-DD HH24:MI:SS'),
                                    '" . self::ACTOR . "',
                                    '{$terminal}'
                                  )";

                    $resInsert = odbc_exec($conn, $sqlInsert);
                    if (!$resInsert) {
                        $failed++;
                        Log::channel('daily')->error('ComplianceRecomputeStatus: INSERT PENERBIT_STATUS gagal', [
                            'penerbit_id' => $id,
                            'error'       => odbc_errormsg($conn),
                        ]);
                        $bar->advance();
                        continue;
                    }
                    odbc_free_result($resInsert);
                }

                // is_lock selalu diikutkan dalam UPDATE, terlepas dari apakah label berubah —
                // supaya kunci tetap sinkron dengan angka SSKCKR terbaru tiap run.
                //
                // Penanda notifikasi direset hanya saat LABEL berubah, dan hanya untuk
                // blokir yang benar-benar dicabut. Tanpa ini, penerbit yang pernah
                // diblokir lalu pulih tidak akan pernah dikirimi email blokir lagi
                // pada siklus tunggakan berikutnya (lihat compliance:send-notifications).
                //
                // Default mati: selama email kepatuhan belum benar-benar dikirim,
                // kolom notifikasi tidak perlu disentuh sama sekali. Nyalakan
                // --reset-notif bersamaan dengan mulai berjalannya pengiriman email.
                $resetNotif = ($resetNotifOn && $d['label_changed'])
                    ? $this->resetNotifClause($newStatus)
                    : '';

                // LOCK_DATE mencatat KAPAN kunci SSKCKR dipasang. Hanya ditulis saat
                // transisi kunci 0 -> 1; saat dilepas (1 -> 0) dikosongkan kembali.
                // Kunci yang tidak berubah tidak menyentuh LOCK_DATE.
                $lockClause = '';
                if ((int) $d['old_lock'] === 0 && (int) $newLock === 1) {
                    $lockClause = ", LOCK_DATE = TO_DATE('{$now}','YYYY-MM-DD HH24:MI:SS')";
                } elseif ((int) $d['old_lock'] === 1 && (int) $newLock === 0) {
                    $lockClause = ", LOCK_DATE = NULL";
                }

                $sqlUpdate = "UPDATE PENERBIT SET STATUS_AKHIR = '{$newSafe}', IS_LOCK = {$newLock}{$lockClause}{$resetNotif} WHERE ID = {$id}";
                $resUpdate = odbc_exec($conn, $sqlUpdate);
                if (!$resUpdate) {
                    $failed++;
                    Log::channel('daily')->error('ComplianceRecomputeStatus: UPDATE PENERBIT gagal', [
                        'penerbit_id' => $id,
                        'error'       => odbc_errormsg($conn),
                    ]);
                    $bar->advance();
                    continue;
                }
                odbc_free_result($resUpdate);

                $done++;
                $bar->advance();
            }
        }

        $bar->finish();
        return [$done, $failed];
    }

    /**
     * Potongan SET untuk mereset penanda email blokir yang sudah tidak berlaku lagi.
     *
     * Reset dilakukan per-jenis, bukan sekadar "kalau kembali Aktif" — supaya transisi
     * "Blokir Konfirmasi Terbit dan SSKCKR" -> "Blokir Konfirmasi Terbit" juga
     * membuka kembali notifikasi SSKCKR untuk siklus berikutnya.
     *
     * Kolom _WA sengaja belum disentuh karena belum ada pengirim WhatsApp. Kalau
     * kanal itu diaktifkan nanti, tambahkan resetnya di sini juga.
     */
    private function resetNotifClause(string $newStatus): string
    {
        $masihBlokirKt   = in_array($newStatus, [self::STATUS_BLOKIR_TERBIT, self::STATUS_BLOKIR_KEDUANYA], true);
        $masihBlokirKckr = in_array($newStatus, [self::STATUS_BLOKIR_KCKR,   self::STATUS_BLOKIR_KEDUANYA], true);

        $clause = '';
        if (!$masihBlokirKt) {
            $clause .= ', IS_NOTIF_BLOKIR_KT_EMAIL = 0, TGL_NOTIF_BLOKIR_KT_EMAIL = NULL';
        }
        if (!$masihBlokirKckr) {
            $clause .= ', IS_NOTIF_BLOKIR_KCKR_EMAIL = 0, TGL_NOTIF_BLOKIR_KCKR_EMAIL = NULL';
        }

        return $clause;
    }
}
