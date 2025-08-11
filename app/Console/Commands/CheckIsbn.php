<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Collection;
use App\Helper\GeneralHelper;
use DB;
use Symfony\Component\Console\Output\ConsoleOutput;

class CheckIsbn extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkisbn:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Isbn';

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
        $collection_isbn = Collection::whereNotNull('code')
            ->where('type', 1)
            ->where('title', 'LIKE', '%sumber elekt%')
            ->where('status', 2)
            ->where('code_type', '1')
            ->whereDate('updated_at', '>=', \Carbon\Carbon::now()->subDays(1))
            ->get();

        $out = new ConsoleOutput();
        $out->writeln("Number of iteration : " . $collection_isbn->count());
        $i = 1;
        activity('collections')
            ->log("Automatic ISBN Synchronization started");
        $log = [];
        $totalWaitTime = 0;
        $startTime = microtime(true);
        foreach ($collection_isbn as $d) {
            $setIsbn = false;
            while ($setIsbn == false) {
                $setIsbn = GeneralHelper::setIsbnReceived($d->code, $d->received_at);
                sleep(1);
                $totalWaitTime++;
                if ($setIsbn) {
                    array_push($log, $d->code . "-> OK");
                    $out->writeln($i . ". " . $d->code . "-> OK");
                    $i++;
                }
            }
        }
        $endTime = microtime(true);
        $totalTime = $endTime - $startTime;
        array_push($log, "Total Waiting Time : " . $totalWaitTime . " seconds");
        array_push($log, "Total Execution Time : " . $totalTime . " seconds");
        activity('collections')
            ->withProperties($log)
            ->log("Automatic ISBN Synchronization finish : " . $collection_isbn->count() . " collection");
    }
}
