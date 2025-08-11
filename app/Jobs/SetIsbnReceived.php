<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Helper\GeneralHelper;

class SetIsbnReceived implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $isbn;
    private $date;
    public $timeout = 3600; // 1 jam
    public $tries = 2;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($isbn, $date)
    {
        $this->isbn = $isbn;
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $totalWaitTime = 0;
        $startTime = microtime(true);
        $setIsbn = false;
        $log = [];
            while($setIsbn == false) {
                $setIsbn = GeneralHelper::setIsbnReceived($this->isbn, $this->date);
                sleep(1);
                $totalWaitTime ++;
                if($setIsbn) {
                    array_push($log, $this->isbn . "-> OK" );  
                }
            }
        $endTime = microtime(true);
        $totalTime = $endTime - $startTime;
        array_push($log, "Total Waiting Time : " . $totalWaitTime . " seconds" ); 
        array_push($log, "Total Execution Time : " . $totalTime . " seconds" ); 
        activity('collections')
            ->withProperties($log)
            ->log("Set Received Date : " . $this->isbn . ' -> ' . $this->date);
    }
}
