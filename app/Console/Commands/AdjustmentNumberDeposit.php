<?php

namespace App\Console\Commands;

use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Console\Command;

class AdjustmentNumberDeposit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:adjustment-number-deposit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle duplicate nomor deposit';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $duplicateGroups = QueryAPI::get("
            SELECT deposit, COUNT(*) AS total
            FROM e_collections
            WHERE deposit IS NOT NULL AND TRIM(deposit) IS NOT NULL
            GROUP BY deposit
            HAVING COUNT(*) > 1
        ") ?: [];

        $idsToRegenerate = [];

        foreach ($duplicateGroups as $group) {
            $depositEsc = str_replace("'", "''", $group->DEPOSIT);

            $rows = QueryAPI::get("
                SELECT id FROM e_collections
                WHERE deposit = '$depositEsc'
                ORDER BY id ASC
            ") ?: [];

            $ids = array_map(fn($r) => $r->ID, $rows);

            array_shift($ids);

            $idsToRegenerate = array_merge($idsToRegenerate, $ids);
        }

        $nullRows = QueryAPI::get("
            SELECT id FROM e_collections
            WHERE deposit IS NULL OR TRIM(deposit) IS NULL OR LENGTH(TRIM(deposit)) = 0
        ") ?: [];

        $idsNullOrEmpty = array_map(fn($r) => $r->ID, $nullRows);
        $allIdsToUpdate = array_unique(array_merge($idsToRegenerate, $idsNullOrEmpty));

        $this->info("Total baris yang akan di-generate ulang: " . count($allIdsToUpdate));

        foreach ($allIdsToUpdate as $id) {
            do {
                $newDeposit = Main::generateNumberDeposit() ?: '';
                $depositEsc = str_replace("'", "''", $newDeposit);
                $existsCheck = QueryAPI::get("SELECT COUNT(*) AS total FROM e_collections WHERE deposit = '$depositEsc'", true) ?: [];
                $exists = ($existsCheck->TOTAL ?? 0) > 0;
            } while ($exists);

            QueryAPI::update('e_collections', $id, [
                'deposit' => $depositEsc
            ]);
        }

        $this->info("Selesai update deposit");
    }
}
