<?php

namespace App\Console\Commands;

use App\Models\Solr;
use App\Models\Collection;
use Illuminate\Console\Command;
use Symfony\Component\Console\Output\ConsoleOutput;

class GetISBN extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getisbn:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $collection_empty_isbn = Collection::whereNull('code')
            ->where('type', 1)
            ->where('title', 'LIKE', '%sumber elektronis%')
            ->where('status', 2)
            ->get();

        $out = new ConsoleOutput();
        $out->writeln("Number of iteration : " . $collection_empty_isbn->count());
        $i = 1;

        foreach ($collection_empty_isbn as $d) {
            $isbn = Solr::data('isbn', 'complete', ['title' => $d->title]);
            if (count($isbn) > 0) {
                if (!isset($isbn[0]['received_date'])) {
                    Solr::postUpdate('isbn', 'complete', [
                        'kd_penerbit_dtl' => $isbn[0]['kd_penerbit_dtl']
                    ], [
                        'received_date' => $d->received_at
                    ]);

                    $d->update([
                        'code_kdt' => $isbn[0]['kd_penerbit_dtl'],
                        'sync'     => 1,
                        'code'     => $isbn[0]['code']
                    ]);

                    $out->writeln($i . ". ID : " . $d->id . " Judul : " . $d->title . " ISBN : " . $d->code . ' Received Date = ' . $d->received_at);
                    $i++;
                } else {
                    $d->update([
                        'status'     => 3,
                        'problem'    => 1,
                        'updated_by' => 1,
                        'problem'    => 'Koleksi ini sudah pernah di terima <SYSTEM AUTOMATE MESSAGE>'
                    ]);

                    $out->writeln($i . ". ID : " . $d->id . " Koleksi ini sudah pernah diterima");
                }
            } else {
                $out->writeln($i . ". ID : " . $d->id . " ISBN tidak ditemukan");
            }
        }
    }
}
