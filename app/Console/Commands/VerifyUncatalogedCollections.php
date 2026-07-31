<?php

namespace App\Console\Commands;

use App\Helpers\QueryAPI;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class VerifyUncatalogedCollections extends Command
{
    protected $signature = 'app:verify-uncataloged-collections
                            {--dry-run    : Tampilkan ringkasan tanpa eksekusi}
                            {--chunk=200  : Jumlah baris per batch}
                            {--preview=20 : Jumlah baris preview saat dry-run}';

    protected $description = 'Panggil API verifikasi (verifikasikoleksiditerima) untuk e_collections berstatus diterima (status=2) yang belum punya catalog_id';

    private const FALLBACK_ACTOR = 'SISTEM';

    public function handle(): int
    {
        $isDryRun   = (bool) $this->option('dry-run');
        $chunkSize  = max(1, (int) ($this->option('chunk')   ?? 200));
        $previewMax = max(1, (int) ($this->option('preview') ?? 20));

        $tStart = microtime(true);
        $lap    = fn() => round(microtime(true) - $tStart, 2);

        $this->line('');
        $this->info('╔══════════════════════════════════════════╗');
        $this->info('║   Verifikasi Koleksi Belum Dikatalog     ║');
        $this->info('╚══════════════════════════════════════════╝');
        $this->line("  Mode : " . ($isDryRun ? 'DRY RUN — tidak ada yang akan diubah' : "EKSEKUSI (chunk {$chunkSize}/batch)"));
        $this->line('');

        try {
            $tQuery = microtime(true);
            $rows = QueryAPI::get("
                SELECT e.id, e.title, e.received_by_name
                FROM e_collections e
                WHERE e.deleted_at IS NULL
                AND e.status = '2'
                AND e.catalog_id IS NULL
                and title is not null
            ") ?? [];
            $queryDuration = round(microtime(true) - $tQuery, 2);

            $total = count($rows);
            $this->line("  Query: {$queryDuration}s — ditemukan {$total} koleksi belum dikatalog");
            $this->line('');

            if ($total === 0) {
                $this->info('✓ Tidak ada koleksi yang perlu diverifikasi. Selesai.');
                return 0;
            }

            $previewCount = min($previewMax, $total);
            $previewRows  = array_map(fn($r) => [
                $r->ID,
                mb_strimwidth($r->TITLE ?? '-', 0, 45, '…'),
                $r->RECEIVED_BY_NAME ?: '(kosong, pakai fallback SISTEM)',
            ], array_slice($rows, 0, $previewCount));

            $this->table(['ID', 'Judul', 'Diterima Oleh (actionBy)'], $previewRows);
            if ($total > $previewMax) {
                $this->line("  … dan " . ($total - $previewMax) . " baris lainnya (tidak ditampilkan).");
            }
            $this->line('');

            if ($isDryRun) {
                $this->warn("Dry-run selesai. Jalankan tanpa --dry-run untuk eksekusi.");
                $this->line("  Estimasi batch  : ~" . ceil($total / $chunkSize) . " batch × {$chunkSize} baris.");
                $this->line("  Total waktu     : {$lap()}s");
                return 0;
            }

            $done   = 0;
            $failed = 0;
            $chunks = array_chunk($rows, $chunkSize);

            $bar = $this->output->createProgressBar($total);
            $bar->setFormat(' Verifikasi: %current%/%max% [%bar%] %percent:3s%%');
            $bar->start();

            $tWrite = microtime(true);
            foreach ($chunks as $chunk) {
                foreach ($chunk as $r) {
                    $actionBy = $r->RECEIVED_BY_NAME ?: self::FALLBACK_ACTOR;

                    $verif = QueryAPI::verificationCollection($r->ID, $actionBy);

                    if ($verif) {
                        QueryAPI::update('e_collections', $r->ID, [
                            'is_need_verify' => 0,
                        ], false);
                        $done++;
                    } else {
                        $failed++;
                        Log::channel('daily')->warning('VerifyUncatalogedCollections: gagal verifikasi', [
                            'id'        => $r->ID,
                            'action_by' => $actionBy,
                        ]);
                    }
                    $bar->advance();
                }
            }
            $bar->finish();
            $writeDuration = round(microtime(true) - $tWrite, 2);
            $totalDuration = $lap();

            $this->line('');
            $this->info("✓ Selesai: {$done} berhasil diverifikasi" . ($failed > 0 ? ", {$failed} gagal" : '') . '.');
            $this->line("  Waktu query      : {$queryDuration}s");
            $this->line("  Waktu verifikasi : {$writeDuration}s ({$total} baris)");
            $this->line("  Total waktu      : {$totalDuration}s");

            Log::channel('daily')->info('VerifyUncatalogedCollections: selesai', [
                'ditemukan'      => $total,
                'sukses'         => $done,
                'gagal'          => $failed,
                'durasi_query_s' => $queryDuration,
                'durasi_tulis_s' => $writeDuration,
                'durasi_total_s' => $totalDuration,
            ]);

        } catch (\Throwable $e) {
            $this->error("Error fatal: " . $e->getMessage() . " (setelah {$lap()}s)");
            Log::channel('daily')->error('VerifyUncatalogedCollections: error fatal', [
                'error'          => $e->getMessage(),
                'trace'          => $e->getTraceAsString(),
                'durasi_total_s' => $lap(),
            ]);
            return 1;
        }

        return 0;
    }
}
