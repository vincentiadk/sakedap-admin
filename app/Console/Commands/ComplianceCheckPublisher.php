<?php

namespace App\Console\Commands;

use App\Helpers\ComplianceSettings;
use App\Traits\OracleHelper;
use Illuminate\Console\Command;

/**
 * Cek apakah satu penerbit akan diblokir bila compliance:recompute-status
 * dijalankan — tanpa mengubah apa pun.
 *
 * Memakai fetchCurrentStatus() + classify() milik ComplianceRecomputeStatus
 * lewat reflection, supaya hasilnya identik dengan command aslinya (bukan
 * logika yang ditulis ulang).
 */
class ComplianceCheckPublisher extends Command
{
    use OracleHelper;

    protected $signature = 'compliance:cek-penerbit {id : ID penerbit yang mau dicek}';

    protected $description = 'Cek status blokir satu penerbit bila recompute-status dijalankan (read-only, tidak mengubah data).';

    public function handle(): int
    {
        $id = (int) $this->argument('id');

        $cmd = new ComplianceRecomputeStatus();
        $ref = new \ReflectionClass($cmd);

        $conn = $this->getOracleConnection();
        $cs   = ComplianceSettings::get();

        $rows = $ref->getMethod('fetchCurrentStatus')->invoke(
            $cmd,
            $conn,
            (int) $cs['BatasWaktuKonfirmasiTerbitKaryaCetak'],
            (int) $cs['BatasWaktuKonfirmasiTerbitDigital'],
            (int) $cs['BatasWaktuTeguranKonfirmasiTerbit'],
        );

        $row = null;
        foreach ($rows as $r) {
            if ((int) $r->ID === $id) {
                $row = $r;
                break;
            }
        }

        if (!$row) {
            $this->warn("Penerbit {$id} tidak masuk perhitungan (tidak punya tagihan/kewajiban ISBN). Otomatis TIDAK diblokir.");
            return 0;
        }

        $minPct  = (int) $cs['BatasMinimumKepatuhanKCKR'];
        $classify = $ref->getMethod('classify');

        $args = [
            (int) $row->LEWAT_TEGURAN,
            (int) $row->TERLAMBAT_KCKR,
            (float) $row->PERSENTASE_KCKR,
            $minPct,
        ];

        $skrg    = trim((string) $row->STATUS_AKHIR) ?: '(kosong)';
        $default = $classify->invoke($cmd, ...array_merge($args, [false]));
        $withKckr = $classify->invoke($cmd, ...array_merge($args, [true]));

        $this->line('');
        $this->info("  {$row->NAME} (ID {$id})");
        $this->line('  ─────────────────────────────────────────────');
        $this->line("  Lewat teguran   : " . (int) $row->LEWAT_TEGURAN);
        $this->line("  Terlambat KCKR  : " . (int) $row->TERLAMBAT_KCKR);
        $this->line("  % kepatuhan KCKR: {$row->PERSENTASE_KCKR}%  (ambang {$minPct}%)");
        $this->line('  ─────────────────────────────────────────────');
        $this->line("  Status sekarang        : {$skrg}");
        $this->line("  Hasil recompute default: {$default}");
        $this->line("  Hasil dgn --kckr-block : {$withKckr}");
        $this->line('');

        $akanBlokir = str_starts_with($default, 'Blokir') && $default !== $skrg;
        if ($akanBlokir) {
            $this->error("  >> AKAN DIBLOKIR: status berubah dari '{$skrg}' menjadi '{$default}'.");
        } elseif (str_starts_with($default, 'Blokir')) {
            $this->warn("  >> Sudah berstatus blokir; recompute tidak mengubahnya.");
        } else {
            $this->info("  >> TIDAK diblokir (mode default).");
        }
        $this->line('');

        return 0;
    }
}
