<?php

namespace App\Console\Commands;

use App\Helpers\QueryAPI;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReleaseReviewLock extends Command
{
    protected $signature = 'app:release-review-lock
                            {--dry-run : Tampilkan ringkasan tanpa eksekusi}';

    protected $description = 'Hapus lock review_by pada e_collections yang masih terkunci (dijalankan tiap malam)';

    public function handle(): int
    {
        $isDryRun = (bool) $this->option('dry-run');
        $now      = now()->timezone('Asia/Jakarta')->format('Y-m-d H:i:s');

        $this->line('');
        $this->info('╔══════════════════════════════════════════╗');
        $this->info('║   Release Review Lock — e_collections    ║');
        $this->info('╚══════════════════════════════════════════╝');
        $this->line("  Waktu  : {$now}");
        $this->line("  Mode   : " . ($isDryRun ? 'DRY RUN — tidak ada yang akan diubah' : 'EKSEKUSI'));
        $this->line('');

        $locked = QueryAPI::get("
            select id, review_by, title
            from e_collections
            where review_by is not null
              and status = '1'
              and deleted_at is null
              and worksheet_id = 20
        ") ?? [];

        $total = count($locked);

        if ($total === 0) {
            $this->info('✓ Tidak ada lock review yang perlu dilepas.');
            return 0;
        }

        $this->info("  Ditemukan {$total} koleksi masih terkunci:");
        $this->line('');

        $previewRows = array_map(fn($r) => [
            $r->ID,
            mb_strimwidth($r->TITLE ?? '-', 0, 50, '…'),
            $r->REVIEW_BY,
        ], array_slice($locked, 0, 20));

        $this->table(['ID', 'Judul', 'Review By'], $previewRows);

        if ($total > 20) {
            $this->line("  … dan " . ($total - 20) . " lainnya.");
        }
        $this->line('');

        if ($isDryRun) {
            $this->warn("Dry-run selesai. Jalankan tanpa --dry-run untuk eksekusi.");
            return 0;
        }

        $done   = 0;
        $failed = 0;

        foreach ($locked as $row) {
            $result = QueryAPI::update('e_collections', $row->ID, [
                'review_by' => null,
            ], false);

            if ($result) {
                $done++;
            } else {
                $failed++;
                Log::channel('daily')->warning('ReleaseReviewLock: gagal melepas lock', [
                    'id'        => $row->ID,
                    'review_by' => $row->REVIEW_BY,
                ]);
            }
        }

        $this->info("✓ Selesai: {$done} lock dilepas" . ($failed > 0 ? ", {$failed} gagal" : '') . '.');

        Log::channel('daily')->info('ReleaseReviewLock: selesai', [
            'tanggal' => $now,
            'dilepas' => $done,
            'gagal'   => $failed,
        ]);

        return 0;
    }
}
