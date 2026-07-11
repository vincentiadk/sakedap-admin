<?php

namespace App\Jobs;

use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ComplianceV3ExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries   = 1;
    public $timeout = 600;

    public function __construct(
        protected string $jobID,
        protected array  $params
    ) {}

    public function handle(): void
    {
        Redis::hset('download:' . $this->jobID, 'status',   'processing');
        Redis::hset('download:' . $this->jobID, 'type',     'compliance-v3');
        Redis::hset('download:' . $this->jobID, 'date',     date('Y-m-d H:i:s'));
        Redis::hset('download:' . $this->jobID, 'progress', json_encode(['pct' => 5, 'label' => 'Memulai proses export...']));

        try {
            $controller = app(\App\Http\Controllers\CoachingSupervision\ComplianceV3Controller::class);
            $controller->buildExportFile($this->jobID, $this->params);
        } catch (\Throwable $e) {
            Redis::hset('download:' . $this->jobID, 'status', 'failed');
            Log::channel('report')->error('ComplianceV3ExportJob gagal: ' . $e->getMessage());
        }
    }

    public function failed(\Throwable $e): void
    {
        Redis::hset('download:' . $this->jobID, 'status', 'failed');
        Log::channel('report')->error('ComplianceV3ExportJob failed: ' . $e->getMessage());
    }
}
