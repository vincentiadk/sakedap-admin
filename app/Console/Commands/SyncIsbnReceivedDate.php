<?php

namespace App\Console\Commands;

use App\Helpers\QueryAPI;
use App\Traits\OracleHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncIsbnReceivedDate extends Command
{
    use OracleHelper;

    protected $signature = 'app:sync-isbn-received-date
                            {--dry-run   : Tampilkan ringkasan tanpa eksekusi}
                            {--chunk=500 : Jumlah baris per batch MERGE INTO}
                            {--limit=    : Batasi jumlah baris yang diproses (untuk tes)}
                            {--isbn=     : Debug satu ISBN tertentu}';

    protected $description = 'Konsinyasi received_date_kckr / received_date_prov ke penerbit_isbn berdasarkan letter_detail yang sudah diterima';

    private const ACTOR = 'Sistem SAKEDAP';

    public function handle(): int
    {
        $isDryRun  = (bool) $this->option('dry-run');
        $chunkSize = max(1, (int) ($this->option('chunk') ?? 500));
        $limit     = $this->option('limit') ? max(1, (int) $this->option('limit')) : null;
        $isbnDebug = $this->option('isbn') ? preg_replace('/[\s\-]/', '', $this->option('isbn')) : null;
        $now       = now()->timezone('Asia/Jakarta')->format('Y-m-d H:i:s');

        $this->line('');
        $this->info('╔══════════════════════════════════════════════╗');
        $this->info('║   Sync ISBN Received Date — Konsinyasi Data  ║');
        $this->info('╚══════════════════════════════════════════════╝');
        $this->line("  Waktu  : {$now}");
        if ($isbnDebug) {
            $this->line("  Mode   : DEBUG ISBN {$isbnDebug}");
        } else {
            $this->line("  Mode   : " . ($isDryRun ? 'DRY RUN — tidak ada yang akan diubah' : 'EKSEKUSI'));
            $this->line("  Chunk  : {$chunkSize} baris per batch" . ($limit ? " | Limit: {$limit} baris" : ''));
        }
        $this->line('');

        if ($isbnDebug) {
            return $this->debugIsbn($isbnDebug);
        }

        $rows = QueryAPI::get("
            SELECT
                ld.letter_detail_id,
                REPLACE(TRIM(ld.isbn), '-', '') AS isbn_clean,
                ld.qty_accept,
                l.accept_date,
                l.branch_id,
                l.receipt_no,
                l.status AS letter_status,
                b.name AS branch_name,
                pi.id AS penerbit_isbn_id,
                pi.penerbit_terbitan_id,
                pi.received_date_kckr,
                pi.received_date_prov
            FROM letter_detail ld
            JOIN letter l ON l.letter_id = ld.letter_id
            LEFT JOIN branchs b ON b.id = l.branch_id
            JOIN penerbit_isbn pi
                ON REPLACE(TRIM(pi.isbn_no), '-', '') = REPLACE(TRIM(ld.isbn), '-', '')
            WHERE ld.qty_accept > 0
              AND TRIM(ld.isbn) IS NOT NULL
              AND l.accept_date IS NOT NULL
              AND l.status IN ('DITERIMA PENUH', 'DITERIMA PARSIAL')
              AND (
                (l.branch_id = 37 AND pi.received_date_kckr IS NULL)
                OR
                (l.branch_id != 37 AND pi.received_date_prov IS NULL)
              )
        ") ?? [];

        $total = count($rows);

        if ($limit) {
            $rows  = array_slice($rows, 0, $limit);
            $total = count($rows);
        }

        if ($total === 0) {
            $this->info('✓ Tidak ada data yang perlu disinkronisasi.');
            return 0;
        }

        $this->info("  Ditemukan {$total} baris yang akan diperbarui:");
        $this->line('');

        $previewRows = array_map(fn($r) => [
            $r->PENERBIT_ISBN_ID,
            $r->ISBN_CLEAN,
            $r->BRANCH_NAME ?? '-',
            $r->BRANCH_ID == 37 ? 'received_date_kckr' : 'received_date_prov',
            $r->ACCEPT_DATE ? date('Y-m-d', strtotime($r->ACCEPT_DATE)) : '-',
        ], array_slice($rows, 0, 20));

        $this->table(['penerbit_isbn ID', 'ISBN', 'Branch', 'Field', 'Tanggal'], $previewRows);

        if ($total > 20) {
            $this->line("  … dan " . ($total - 20) . " lainnya.");
        }
        $this->line('');

        if ($isDryRun) {
            $this->warn("Dry-run selesai. Jalankan tanpa --dry-run untuk eksekusi.");
            return 0;
        }

        $conn = $this->getOracleConnection();
        $ip   = request()->ip() ?? '127.0.0.1';
        $nowSafe = str_replace("'", "''", $now);
        $actorSafe = str_replace("'", "''", self::ACTOR);
        $ipSafe = str_replace("'", "''", $ip);

        // Pisah ke dua grup: kckr (branch 37) dan prov (selain 37)
        $groupKckr = array_filter($rows, fn($r) => $r->BRANCH_ID == 37);
        $groupProv = array_filter($rows, fn($r) => $r->BRANCH_ID != 37);

        $done   = 0;
        $failed = 0;

        foreach (['received_date_kckr' => $groupKckr, 'received_date_prov' => $groupProv] as $field => $group) {
            $group = array_values($group);
            if (empty($group)) continue;

            $chunks = array_chunk($group, $chunkSize);
            $bar = $this->output->createProgressBar(count($group));
            $bar->setFormat(" %current%/%max% [%bar%] %percent:3s%% — {$field}");
            $bar->start();

            foreach ($chunks as $chunk) {
                // Build MERGE INTO — deduplikasi per ID, ambil ACCEPT_DATE terbaru
                $deduped = [];
                foreach ($chunk as $row) {
                    $id  = (int) $row->PENERBIT_ISBN_ID;
                    $tgl = strtotime($row->ACCEPT_DATE);
                    if (!isset($deduped[$id]) || $tgl > $deduped[$id]['ts']) {
                        $deduped[$id] = ['ts' => $tgl, 'tgl' => date('Y-m-d', $tgl)];
                    }
                }

                $unionRows = [];
                foreach ($deduped as $id => $val) {
                    $unionRows[] = "SELECT $id AS id, '{$val['tgl']}' AS tgl FROM DUAL";
                }

                $union = implode(" UNION ALL ", $unionRows);
                $sql   = "
                    MERGE INTO penerbit_isbn pi
                    USING ($union) src ON (pi.id = src.id)
                    WHEN MATCHED THEN UPDATE SET
                        pi.{$field}       = TO_DATE(src.tgl, 'YYYY-MM-DD'),
                        pi.updatedate     = TO_DATE('$nowSafe', 'YYYY-MM-DD HH24:MI:SS'),
                        pi.updateby       = '$actorSafe',
                        pi.updateterminal = '$ipSafe'
                ";

                $res = odbc_exec($conn, $sql);

                if (!$res) {
                    $this->warn("  MERGE gagal: " . odbc_errormsg($conn));
                }

                if ($res) {
                    odbc_free_result($res);
                    // Insert historydata per baris dalam chunk ini
                    foreach ($chunk as $row) {
                        $ptId     = (int) $row->PENERBIT_TERBITAN_ID;
                        $tgl      = date('d/m/Y', strtotime($row->ACCEPT_DATE));
                        $branchName = $row->BRANCH_NAME ?? 'Perpustakaan Nasional';
                        $label      = $field === 'received_date_kckr' ? 'Perpustakaan Nasional (KCKR)' : "Perpustakaan Daerah ({$branchName})";
                        $resi       = $row->RECEIPT_NO ?? '-';
                        $noteSafe   = str_replace("'", "''", "Tanggal terima di {$label} diisi otomatis berdasarkan data penerimaan fisik pada {$tgl} (No. Resi: {$resi})");

                        $sqlHist = "
                            INSERT INTO historydata
                                (action, tablename, actionby, actiondate, actionterminal, note, idref)
                            VALUES (
                                'UPDATE',
                                'PENERBIT_TERBITAN',
                                '$actorSafe',
                                TO_DATE('$nowSafe', 'YYYY-MM-DD HH24:MI:SS'),
                                '$ipSafe',
                                '$noteSafe',
                                $ptId
                            )
                        ";
                        $resHist = odbc_exec($conn, $sqlHist);
                        if (!$resHist) {
                            $this->warn("  historydata gagal (penerbit_terbitan_id=$ptId): " . odbc_errormsg($conn));
                        } else {
                            odbc_free_result($resHist);
                        }

                        $done++;
                        $bar->advance();
                    }
                } else {
                    $err = odbc_errormsg($conn);
                    $failed += count($chunk);
                    $bar->advance(count($chunk));
                    Log::channel('daily')->warning("SyncIsbnReceivedDate: MERGE gagal ({$field})", [
                        'error' => $err,
                        'ids'   => array_column($chunk, 'PENERBIT_ISBN_ID'),
                    ]);
                }
            }

            $bar->finish();
            $this->line('');
        }

        $this->line('');
        $this->info("✓ Selesai: {$done} baris diperbarui" . ($failed > 0 ? ", {$failed} gagal" : '') . '.');

        Log::channel('daily')->info('SyncIsbnReceivedDate: selesai', [
            'tanggal'    => $now,
            'diperbarui' => $done,
            'gagal'      => $failed,
        ]);

        return 0;
    }

    private function debugIsbn(string $isbnClean): int
    {
        $pi = QueryAPI::get("
            SELECT id, isbn_no, received_date_kckr, received_date_prov
            FROM penerbit_isbn
            WHERE REPLACE(TRIM(isbn_no), '-', '') = '$isbnClean'
        ") ?? [];

        $this->info('[ penerbit_isbn ]');
        if (empty($pi)) {
            $this->error("  Tidak ditemukan di penerbit_isbn untuk ISBN $isbnClean");
        } else {
            $this->table(
                ['ID', 'isbn_no', 'received_date_kckr', 'received_date_prov'],
                array_map(fn($r) => [$r->ID, $r->ISBN_NO, $r->RECEIVED_DATE_KCKR ?? '-', $r->RECEIVED_DATE_PROV ?? '-'], $pi)
            );
        }

        $this->line('');

        $ld = QueryAPI::get("
            SELECT
                ld.letter_detail_id,
                ld.isbn,
                ld.qty_accept,
                l.letter_id,
                l.branch_id,
                l.status AS letter_status,
                l.accept_date,
                b.name AS branch_name,
                b.province_id
            FROM letter_detail ld
            JOIN letter l ON l.letter_id = ld.letter_id
            LEFT JOIN branchs b ON b.id = l.branch_id
            WHERE REPLACE(TRIM(ld.isbn), '-', '') = '$isbnClean'
            ORDER BY l.accept_date DESC
        ") ?? [];

        $this->info('[ letter_detail ]');
        if (empty($ld)) {
            $this->error("  Tidak ditemukan di letter_detail untuk ISBN $isbnClean");
        } else {
            $this->table(
                ['ld_id', 'isbn', 'qty_accept', 'letter_id', 'status', 'accept_date', 'branch', 'branch_id'],
                array_map(fn($r) => [
                    $r->LETTER_DETAIL_ID,
                    $r->ISBN,
                    $r->QTY_ACCEPT ?? '-',
                    $r->LETTER_ID,
                    $r->LETTER_STATUS,
                    $r->ACCEPT_DATE ? date('Y-m-d', strtotime($r->ACCEPT_DATE)) : '-',
                    $r->BRANCH_NAME ?? '-',
                    $r->BRANCH_ID ?? '-',
                ], $ld)
            );
        }

        $this->line('');
        $this->comment("branch_id = 37 → received_date_kckr (Perpusnas). Selain itu → received_date_prov.");

        return 0;
    }
}
