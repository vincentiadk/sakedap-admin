<?php

namespace App\Console\Commands;

use App\Helpers\ComplianceNotification;
use App\Helpers\ComplianceSettings;
use App\Traits\OracleHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ComplianceSendNotifications extends Command
{
    use OracleHelper;

    protected $signature = 'compliance:send-notifications
                            {--dry-run          : Tampilkan kandidat tanpa mengirim dan tanpa menulis penanda}
                            {--to=tester        : Tujuan email: tester|email1|email2|auto|both}
                            {--tester-email=    : Alamat tester (dipakai saat --to=tester, default dari MAIL_TESTER_ADDRESS)}
                            {--jenis=all        : Jenis email, pisahkan koma: pengingat-kt,blokir-kt,pengingat-kckr,blokir-kckr}
                            {--penerbit=        : Batasi ke satu ID penerbit (untuk uji coba)}
                            {--limit=0          : Batasi jumlah email yang dikirim (0 = tanpa batas)}
                            {--cooldown=7       : Jeda minimum (hari) antar email pengingat ke penerbit yang sama}
                            {--reminder-days=7  : Kirim pengingat bila tanggal blokir tinggal <= N hari lagi}
                            {--force            : Lewati konfirmasi saat mengirim ke email asli penerbit}
                            {--preview          : Uji redaksi: abaikan syarat status/cooldown. Wajib --to=tester dan --penerbit}';

    protected $description = 'Kirim email pengingat & pemberitahuan blokir kepatuhan ke penerbit. Default aman: hanya ke alamat tester dan tidak menandai PENERBIT.';

    // Definisi jenis email, redaksi, query kandidat, dan penyusunan HTML
    // tinggal di App\Helpers\ComplianceNotification supaya halaman uji
    // di Administrasi Sistem memakai sumber yang sama persis.
    private const JENIS = ComplianceNotification::JENIS;

    private const STATUS_BLOKIR_TERBIT   = ComplianceNotification::STATUS_BLOKIR_TERBIT;
    private const STATUS_BLOKIR_KCKR     = ComplianceNotification::STATUS_BLOKIR_KCKR;
    private const STATUS_BLOKIR_KEDUANYA = ComplianceNotification::STATUS_BLOKIR_KEDUANYA;

    private ComplianceNotification $notif;

    public function handle(): int
    {
        $this->notif  = new ComplianceNotification();
        $isDryRun     = (bool) $this->option('dry-run');
        $toMode       = strtolower(trim((string) $this->option('to')));
        $limit        = max(0, (int) $this->option('limit'));
        $cooldown     = max(0, (int) $this->option('cooldown'));
        $reminderDays = max(1, (int) $this->option('reminder-days'));
        $penerbitId   = $this->option('penerbit') !== null ? (int) $this->option('penerbit') : null;

        $tStart = microtime(true);
        $lap    = fn() => round(microtime(true) - $tStart, 2);

        // ── Validasi opsi ────────────────────────────────────────────────────
        $validModes = ['tester', 'email1', 'email2', 'auto', 'both'];
        if (!in_array($toMode, $validModes, true)) {
            $this->error("--to tidak dikenal: '{$toMode}'. Pilihan: " . implode('|', $validModes));
            return 1;
        }

        $testerEmail = null;
        if ($toMode === 'tester') {
            $testerEmail = trim((string) ($this->option('tester-email') ?: env('MAIL_TESTER_ADDRESS', '')));
            if ($testerEmail === '') {
                $this->error('Mode tester butuh alamat tujuan. Pakai --tester-email=nama@domain, atau set MAIL_TESTER_ADDRESS di .env.');
                return 1;
            }
            if (!filter_var($testerEmail, FILTER_VALIDATE_EMAIL)) {
                $this->error("Alamat tester tidak valid: {$testerEmail}");
                return 1;
            }
        }

        $jenisList = $this->resolveJenis((string) $this->option('jenis'));
        if ($jenisList === null) {
            return 1;
        }

        // Preview dikunci ke satu penerbit + alamat tester. Tanpa kunci ini,
        // sebuah salah ketik bisa mengirimkan surat "akun Anda diblokir" ke
        // ribuan penerbit yang statusnya baik-baik saja.
        $isPreview = (bool) $this->option('preview');
        if ($isPreview) {
            if ($toMode !== 'tester') {
                $this->error('--preview hanya boleh dengan --to=tester. Mode ini mengabaikan syarat status, jadi tidak boleh menyasar penerbit asli.');
                return 1;
            }
            if (!$penerbitId) {
                $this->error('--preview wajib disertai --penerbit=<id> supaya jelas data siapa yang dirender.');
                return 1;
            }
        }

        // ── Banner ───────────────────────────────────────────────────────────
        $this->line('');
        $this->info('╔══════════════════════════════════════════╗');
        $this->info('║   Kirim Notifikasi Kepatuhan Penerbit    ║');
        $this->info('╚══════════════════════════════════════════╝');
        $this->line('  Mode       : ' . ($isDryRun ? 'DRY RUN — tidak ada email yang dikirim' : 'KIRIM'));
        $this->line('  Tujuan     : ' . $this->describeMode($toMode, $testerEmail));
        $this->line('  Jenis      : ' . implode(', ', array_map(fn($j) => self::JENIS[$j]['label'], $jenisList)));
        $this->line("  Cooldown   : {$cooldown} hari (email pengingat)");
        $this->line("  Pengingat  : dikirim {$reminderDays} hari sebelum tanggal blokir");
        if ($penerbitId) {
            $this->line("  Penerbit   : dibatasi ke ID {$penerbitId}");
        }
        if ($isPreview) {
            $this->line('  Preview    : syarat status/cooldown DIABAIKAN — hanya untuk uji redaksi');
        }
        if ($limit > 0) {
            $this->line("  Limit      : {$limit} email");
        }
        $this->line('');

        $marksWritten = !$isDryRun && $toMode !== 'tester';
        if ($toMode === 'tester') {
            $this->warn('  Mode tester: penanda IS_NOTIF_*/TGL_NOTIF_* di PENERBIT TIDAK akan ditulis,');
            $this->warn('  supaya penerbit asli tidak tercatat "sudah dinotifikasi" padahal belum.');
            $this->line('');
        }

        // ── Konfirmasi kalau menyasar email asli ─────────────────────────────
        if (!$isDryRun && $toMode !== 'tester' && !$this->option('force')) {
            if (!$this->confirm("Email akan dikirim ke ALAMAT ASLI PENERBIT ({$toMode}). Lanjutkan?", false)) {
                $this->line('Dibatalkan.');
                return 0;
            }
        }

        try {
            $cs     = ComplianceSettings::get();
            $minPct = (int) $cs['BatasMinimumKepatuhanKCKR'];
            $dlKc   = (int) $cs['BatasWaktuKonfirmasiTerbitKaryaCetak'];
            $dlKr   = (int) $cs['BatasWaktuKonfirmasiTerbitDigital'];
            $teguran = (int) $cs['BatasWaktuTeguranKonfirmasiTerbit'];

            $redaksi = $this->notif->loadRedaksi();

            // Ini surat resmi — kalau redaksinya belum diisi admin, jenis tersebut
            // dilewati, bukan diganti teks karangan sistem.
            $jenisList = array_values(array_filter($jenisList, function ($jenis) use ($redaksi) {
                $param = self::JENIS[$jenis]['param'];
                if (isset($redaksi[$param])) {
                    return true;
                }
                $this->warn("  Dilewati — redaksi '{$param}' belum diisi di Pengaturan Kepatuhan.");
                return false;
            }));

            if (empty($jenisList)) {
                $this->error('Tidak ada jenis email dengan redaksi terisi. Isi dulu di menu Pengaturan Kepatuhan.');
                return 1;
            }

            $conn = $this->getOracleConnection();

            $tQuery = microtime(true);
            $rows   = $this->notif->fetchCandidates($conn, $dlKc, $dlKr, $teguran, $reminderDays, $penerbitId, $isPreview);
            $this->line('  Query kandidat : ' . round(microtime(true) - $tQuery, 2) . 's (' . count($rows) . ' penerbit dievaluasi)');
            $this->line('');

            // ── Susun antrian kirim ──────────────────────────────────────────
            $queue   = [];
            $skipped = ['sudah_dikirim' => 0, 'cooldown' => 0, 'email_kosong' => 0, 'imun' => 0];

            foreach ($rows as $row) {
                foreach ($jenisList as $jenis) {
                    if (!$isPreview && !$this->isEligible($jenis, $row, $minPct, $cooldown, $skipped)) {
                        continue;
                    }

                    $recipients = $this->resolveRecipients($row, $toMode, $testerEmail);
                    if (empty($recipients)) {
                        $skipped['email_kosong']++;
                        continue;
                    }

                    $queue[] = [
                        'jenis'      => $jenis,
                        'row'        => $row,
                        'recipients' => $recipients,
                    ];
                }
            }

            if ($limit > 0 && count($queue) > $limit) {
                $queue = array_slice($queue, 0, $limit);
            }

            $total = count($queue);
            $this->info("  Antrian kirim : {$total} email");
            foreach ($skipped as $reason => $count) {
                if ($count > 0) {
                    $this->line('  Dilewati (' . str_replace('_', ' ', $reason) . ") : {$count}");
                }
            }
            $this->line('');

            if ($total === 0) {
                $this->info('✓ Tidak ada email yang perlu dikirim. Selesai.');
                return 0;
            }

            // ── Preview ──────────────────────────────────────────────────────
            $previewRows = array_map(fn($q) => [
                $q['row']->ID,
                mb_strimwidth($q['row']->NAME ?? '-', 0, 30, '…'),
                self::JENIS[$q['jenis']]['label'],
                mb_strimwidth(implode(', ', $q['recipients']), 0, 38, '…'),
            ], array_slice($queue, 0, 20));

            $this->table(['ID', 'Nama Penerbit', 'Jenis Email', 'Tujuan'], $previewRows);
            if ($total > 20) {
                $this->line('  … dan ' . ($total - 20) . ' email lainnya (tidak ditampilkan).');
            }
            $this->line('');

            if ($isDryRun) {
                $this->warn('Dry-run selesai. Jalankan tanpa --dry-run untuk mengirim.');
                $this->line("  Total waktu : {$lap()}s");
                return 0;
            }

            // ── Kirim ────────────────────────────────────────────────────────
            [$sent, $failed] = $this->sendQueue($conn, $queue, $redaksi, $minPct, $marksWritten);

            $this->line('');
            $this->info("✓ Selesai: {$sent} email terkirim" . ($failed > 0 ? ", {$failed} gagal" : '') . '.');
            $this->line("  Total waktu : {$lap()}s");

            Log::channel('daily')->info('ComplianceSendNotifications: selesai', [
                'mode'          => $toMode,
                'jenis'         => $jenisList,
                'antrian'       => $total,
                'terkirim'      => $sent,
                'gagal'         => $failed,
                'dilewati'      => $skipped,
                'tulis_penanda' => $marksWritten,
                'durasi_s'      => $lap(),
            ]);

        } catch (\Throwable $e) {
            $this->error('Error fatal: ' . $e->getMessage() . " (setelah {$lap()}s)");
            Log::channel('daily')->error('ComplianceSendNotifications: error fatal', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return 1;
        }

        return 0;
    }

    // ── Opsi ─────────────────────────────────────────────────────────────────

    private function resolveJenis(string $raw): ?array
    {
        $raw = strtolower(trim($raw));
        if ($raw === '' || $raw === 'all') {
            return array_keys(self::JENIS);
        }

        $list = array_filter(array_map('trim', explode(',', $raw)));
        foreach ($list as $j) {
            if (!isset(self::JENIS[$j])) {
                $this->error("--jenis tidak dikenal: '{$j}'. Pilihan: " . implode(', ', array_keys(self::JENIS)) . ', atau all.');
                return null;
            }
        }

        return array_values($list);
    }

    private function describeMode(string $mode, ?string $testerEmail): string
    {
        return match ($mode) {
            'tester' => "TESTER — semua email diarahkan ke {$testerEmail}",
            'email1' => 'ASLI — PENERBIT.EMAIL1',
            'email2' => 'ASLI — PENERBIT.EMAIL2',
            'auto'   => 'ASLI — PENERBIT.EMAIL1, fallback ke EMAIL2 bila kosong',
            'both'   => 'ASLI — PENERBIT.EMAIL1 dan EMAIL2 sekaligus',
        };
    }

    private function resolveRecipients(object $row, string $mode, ?string $testerEmail): array
    {
        if ($mode === 'tester') {
            return [$testerEmail];
        }

        $e1 = $this->cleanEmail($row->EMAIL1 ?? null);
        $e2 = $this->cleanEmail($row->EMAIL2 ?? null);

        return array_values(array_unique(array_filter(match ($mode) {
            'email1' => [$e1],
            'email2' => [$e2],
            'auto'   => [$e1 ?: $e2],
            'both'   => [$e1, $e2],
        })));
    }

    private function cleanEmail(?string $email): ?string
    {
        $email = trim((string) $email);
        return ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) ? $email : null;
    }

    // ── Kelayakan kirim ──────────────────────────────────────────────────────

    private function isEligible(string $jenis, object $row, int $minPct, int $cooldown, array &$skipped): bool
    {
        $def    = self::JENIS[$jenis];
        $status = trim((string) ($row->STATUS_AKHIR ?? ''));
        $imun   = (int) ($row->IS_IMUN_AUTO_BLOKIR ?? 0) === 1;

        $kenaBlokirKt   = in_array($status, [self::STATUS_BLOKIR_TERBIT, self::STATUS_BLOKIR_KEDUANYA], true);
        $kenaBlokirKckr = in_array($status, [self::STATUS_BLOKIR_KCKR,   self::STATUS_BLOKIR_KEDUANYA], true);

        // Kondisi substansi: apakah penerbit ini memang sasaran jenis email tsb?
        //
        // Pengingat memakai angka mentah (LEWAT_TEGURAN / TERLAMBAT_KCKR), bukan
        // hanya STATUS_AKHIR. Alasannya: selama recompute berjalan tanpa
        // --kckr-block, STATUS_AKHIR tidak pernah menjadi 'Blokir SSKCKR', jadi
        // mengandalkan status saja akan mengirimi "akan diblokir dalam N hari"
        // kepada penerbit yang sebenarnya sudah lewat jatuh tempo.
        $match = match ($jenis) {
            'blokir-kt'      => $kenaBlokirKt,
            'blokir-kckr'    => $kenaBlokirKckr,

            'pengingat-kt'   => !$kenaBlokirKt
                                && (int) ($row->LEWAT_TEGURAN ?? 0) === 0
                                && (int) ($row->KT_MENDEKATI ?? 0) > 0,

            'pengingat-kckr' => !$kenaBlokirKckr
                                && (int) ($row->TERLAMBAT_KCKR ?? 0) === 0
                                && (int) ($row->KCKR_MENDEKATI ?? 0) > 0
                                && (float) ($row->PERSENTASE_KCKR ?? 0) <= $minPct,
        };

        if (!$match) {
            return false;
        }

        // Penerbit yang dikecualikan dari blokir otomatis tidak dikirimi email blokir.
        if ($imun && !$def['reminder']) {
            $skipped['imun']++;
            return false;
        }

        if ($def['reminder']) {
            // Pengingat: boleh berulang, tapi hormati cooldown.
            $last = trim((string) ($row->{$def['date']} ?? ''));
            if ($last !== '' && strtotime($last) !== false) {
                if (strtotime($last) > strtotime("-{$cooldown} days")) {
                    $skipped['cooldown']++;
                    return false;
                }
            }
            return true;
        }

        // Blokir: sekali per transisi. Penanda direset ke 0/NULL oleh
        // compliance:recompute-status saat penerbit kembali Aktif, sehingga
        // siklus blokir berikutnya otomatis dikirimi email lagi.
        if ((int) ($row->{$def['flag']} ?? 0) === 1) {
            $skipped['sudah_dikirim']++;
            return false;
        }

        return true;
    }

    // ── Pengiriman ───────────────────────────────────────────────────────────

    private function sendQueue($conn, array $queue, array $redaksi, int $minPct, bool $writeMarks): array
    {
        $sent   = 0;
        $failed = 0;

        $bar = $this->output->createProgressBar(count($queue));
        $bar->setFormat(' Mengirim: %current%/%max% [%bar%] %percent:3s%%');
        $bar->start();

        foreach ($queue as $item) {
            $jenis = $item['jenis'];
            $def   = self::JENIS[$jenis];
            $row   = $item['row'];

            $body = $this->notif->buildHtml($redaksi[$def['param']], $jenis, $row, $minPct);

            try {
                $this->notif->send($item['recipients'], $jenis, $body);

                $sent++;

                // Penanda hanya ditulis kalau email benar-benar terkirim ke alamat
                // asli penerbit. Gagal kirim sengaja TIDAK ditandai, supaya run
                // berikutnya mencoba ulang.
                if ($writeMarks) {
                    $this->markSent($conn, (int) $row->ID, $def);
                }

            } catch (\Throwable $e) {
                $failed++;
                Log::channel('daily')->error('ComplianceSendNotifications: gagal kirim', [
                    'penerbit_id' => (int) $row->ID,
                    'jenis'       => $jenis,
                    'tujuan'      => $item['recipients'],
                    'error'       => $e->getMessage(),
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        return [$sent, $failed];
    }

    private function markSent($conn, int $penerbitId, array $def): void
    {
        $now = date('Y-m-d H:i:s');
        $sql = "UPDATE PENERBIT
                   SET {$def['flag']} = 1,
                       {$def['date']} = TO_DATE('{$now}','YYYY-MM-DD HH24:MI:SS')
                 WHERE ID = {$penerbitId}";

        $res = odbc_exec($conn, $sql);
        if (!$res) {
            Log::channel('daily')->error('ComplianceSendNotifications: UPDATE penanda gagal', [
                'penerbit_id' => $penerbitId,
                'kolom'       => $def['flag'],
                'error'       => odbc_errormsg($conn),
            ]);
            return;
        }
        odbc_free_result($res);
    }
}
