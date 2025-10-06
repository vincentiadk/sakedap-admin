<?php

namespace App\Console\Commands;

use App\Helpers\QueryAPI;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class AutoGrantCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-grant-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis menjadikan hibah koleksi yang ditolak di pengiriman';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limitGrant = Config::get('system.limit_grant');
        $limitRetur = Config::get('system.limit_retur');
        $sessionName = 'Sistem E-Deposit';
        $requestIp = request()->ip();
        $dateTimeNow = date('Y-m-d H:i:s');
        $dateNow = date('Y-m-d');

        $dateLimitGrantSql = "trunc(l.accept_date) + $limitGrant";
        $dateLimitReturSql = "trunc(l.accept_date) + $limitRetur";

        $this->info("Memulai proses limit Hibah dan Retur untuk tanggal $dateNow...");

        $queryGrant = "
            select
                ld.*,
                l.accept_date
            from
                letter_detail ld
            join
                letter l ON l.letter_id = ld.letter_id
            where
                (ld.qty_hibah is null or ld.qty_hibah = 0) and
                (ld.qty_retur is null or ld.qty_retur = 0) and
                (ld.qty_reject > 0) and
                (l.status in ('DITERIMA PENUH', 'DITERIMA PARSIAL') and l.accept_date is not null) and
                $dateLimitGrantSql <= to_date('$dateNow', 'YYYY-MM-DD')
        ";

        $this->processLetterWorkflow(
            $queryGrant,
            'QTY_REJECT',
            ['qty_hibah' => 'QTY_VALUE', 'qty_retur' => null],
            $sessionName,
            $dateTimeNow,
            $requestIp,
            'Hibah'
        );

        $queryRetur = "
            select
                ld.*,
                l.accept_date
            from
                letter_detail ld
            join
                letter l ON l.letter_id = ld.letter_id
            where
                (ld.diambil = 0 or ld.diambil = -1) and
                (ld.qty_retur > 0) and
                (l.status in ('DITERIMA PENUH', 'DITERIMA PARSIAL') and l.accept_date is not null) and
                $dateLimitReturSql <= to_date('$dateNow', 'YYYY-MM-DD')
        ";

        $this->processLetterWorkflow(
            $queryRetur,
            'QTY_REJECT',
            ['qty_hibah' => null, 'qty_retur' => 'QTY_VALUE'],
            $sessionName,
            $dateTimeNow,
            $requestIp,
            'Retur'
        );

        $this->info("Proses selesai.");
        return 0;
    }

    /**
     * processLetterWorkflow
     *
     * @param  mixed $querySql
     * @param  mixed $qtyKey
     * @param  mixed $updateFields
     * @param  mixed $sessionName
     * @param  mixed $dateTimeNow
     * @param  mixed $requestIp
     * @param  mixed $processType
     * @return void
     */
    protected function processLetterWorkflow($querySql, $qtyKey, $updateFields, $sessionName, $dateTimeNow, $requestIp, $processType)
    {
        $letterDetails = QueryAPI::get($querySql);

        if (empty($letterDetails)) {
            $this->comment("Tidak ada data $processType yang perlu diproses.");

            return;
        }

        $this->info("Ditemukan " . count($letterDetails) . " item untuk diproses sebagai $processType.");

        $collectionChunk = collect($letterDetails)->chunk(100);

        foreach ($collectionChunk as $chunk) {
            $letterDetailUpdates = [];
            $hibahDetailInserts = [];

            foreach ($chunk as $val) {
                $qty = (int) $val->{$qtyKey};
                $letterDetailId = $val->LETTER_DETAIL_ID;
                $price = (float) ($val->PRICE ?? 0);
                $totalNilai = $price * $qty;

                $letterDetailUpdates[] = [
                    'id' => $letterDetailId,
                    'qty_hibah' => $updateFields['qty_hibah'] === 'QTY_VALUE' ? $qty : $updateFields['qty_hibah'],
                    'qty_retur' => $updateFields['qty_retur'] === 'QTY_VALUE' ? $qty : $updateFields['qty_retur'],
                ];

                $hibahDetailInserts[] = [
                    'judul' => $val->TITLE,
                    'penerbit' => $val->PUBLISHER,
                    'isbn' => $val->ISBN,
                    'tahun_terbit' => $val->PUBLISH_YEAR,
                    'jumlah_eksemplar' => $qty,
                    'harga' => $price,
                    'total_nilai' => $totalNilai,
                    'createby' => $sessionName,
                    'createdate' => $dateTimeNow,
                    'createterminal' => $requestIp,
                    'updateby' => $sessionName,
                    'updatedate' => $dateTimeNow,
                    'updateterminal' => $requestIp,
                    'deskripsi_fisik' => $val->DESKRIPSIFISIK,
                    'jenis_isi' => $val->JENIS_ISI,
                    'jenis_wadah' => $val->JENIS_WADAH,
                    'jenis_media' => $val->JENIS_MEDIA,
                    'source_id' => 6,
                    'source_sub_id' => 3,
                    'ketersediaan_id' => 1,
                    'partner_id' => 9687,
                    'kala_terbit' => $val->KALA_TERBIT,
                    'letter_detail_id' => $letterDetailId,
                ];
            }

            foreach ($letterDetailUpdates as $updateItem) {
                QueryAPI::update('letter_detail', $updateItem['id'], [
                    'qty_hibah' => $updateItem['qty_hibah'],
                    'qty_retur' => $updateItem['qty_retur'],
                ], false);
            }

            foreach ($hibahDetailInserts as $insertItem) {
                QueryAPI::create('hibah_detail', $insertItem, false);
            }
        }

        $this->line("Pemrosesan $processType selesai untuk chunk.");
    }
}
