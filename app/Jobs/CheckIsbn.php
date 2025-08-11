<?php

namespace App\Jobs;

use App\Models\Publisher;
use App\Models\Collection;
use Illuminate\Bus\Queueable;
use App\Helper\GeneralHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckIsbn implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $id;
    private $user_id;

    public function __construct($id, $user_id)
    {
        $this->id = $id;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $p = Publisher::find($this->id);
        if ($p) {
            $colls = Collection::where('publisher_id', $this->id)
                ->where('status', 2)
                ->where('title', 'like', '%sumber elek%')
                ->where('type', 1)
                ->whereNotNull('code')
                ->get();

            activity('publishers')
                ->performedOn($p)
                ->causedBy($this->user_id)
                ->log("ISBN Synchronization started : " . $p->name);
            $totalWaitTime = 0;
            $startTime = microtime(true);
            $log = [];
            foreach ($colls as $coll) {
                $result = false;
                while ($result == false) {
                    $result = GeneralHelper::setIsbnReceived($coll->code, $coll->received_at);
                    sleep(1);
                    $totalWaitTime++;
                    if ($result) {
                        array_push($log, $coll->code . "-> OK");
                    }
                }
            }
            $endTime = microtime(true);
            $totalTime = $endTime - $startTime;
            array_push($log, "Total Waiting Time : " . $totalWaitTime . " seconds");
            array_push($log, "Total Execution Time : " . $totalTime . " seconds");
            activity('publishers')
                ->performedOn($p)
                ->causedBy($this->user_id)
                ->withProperties($log)
                ->log("ISBN Synchronization finish  " . $p->name . " : " . $colls->count() . " collection");
        }
    }
}
