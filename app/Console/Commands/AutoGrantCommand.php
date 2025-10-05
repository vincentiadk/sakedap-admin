<?php

namespace App\Console\Commands;

use App\Helpers\QueryAPI;
use Illuminate\Console\Command;

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
        $limit = config('system.limit_grant');
        $sessionName = 'Sistem E-Deposit';
        $requestIp = request()->ip();
        $dateTimeNow = date('Y-m-d H:i:s');
        $dateNow = date('Y-m-d');
        $dateLimitSql = "trunc(l.accept_date) + $limit";

        $letterDetail = QueryAPI::get("
            select
                ld.*,
                l.accept_date
            from
                letter_detail ld
            join
                letter l on l.letter_id = ld.letter_id
            where
                (ld.qty_hibah is null or ld.qty_hibah = 0) and
                (ld.qty_reject > 0) and
                (l.status in ('DITERIMA PENUH', 'DITERIMA PARSIAL') and l.accept_date is not null) and
                $dateLimitSql <= to_date('$dateNow', 'YYYY-MM-DD')
        ");

        if ($letterDetail) {
            $collectionChunk = collect($letterDetail)->chunk(100);

            foreach ($collectionChunk as $chunk) {
                foreach ($chunk as $val) {
                    $qtyReject = $val->QTY_REJECT ?? 0;
                    $qtyRetur = $val->QTY_RETUR ?? 0;
                    $qtyGrant = max(0, $qtyReject - $qtyRetur);

                    QueryAPI::update('letter_detail', $val->LETTER_DETAIL_ID, [
                        'qty_hibah' => $qtyGrant,
                    ], false);

                    $totalNilai = (float) ($val->PRICE ?? 0) * (float) ($qtyGrant);

                    QueryAPI::create('hibah_detail', [
                        'judul' => $val->TITLE,
                        'penerbit' => $val->PUBLISHER,
                        'isbn' => $val->ISBN,
                        'tahun_terbit' => $val->PUBLISH_YEAR,
                        'jumlah_eksemplar' => $qtyGrant,
                        'harga' => $val->PRICE,
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
                        'letter_detail_id' => $val->LETTER_DETAIL_ID,
                    ], false);
                }
            }
        }
    }
}
