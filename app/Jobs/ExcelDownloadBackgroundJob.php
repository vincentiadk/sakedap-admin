<?php

namespace App\Jobs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportPeriodicExport;
use Illuminate\Support\Facades\Redis;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExcelDownloadBackgroundJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * jobID
     *
     * @var mixed
     */
    protected $jobID;

    /**
     * type
     *
     * @var mixed
     */
    protected $type;

    /**
     * request
     *
     * @var mixed
     */
    protected $request;

    /**
     * Create a new job instance.
     */
    public function __construct($jobID, $type, $request)
    {
        $this->jobID = $jobID;
        $this->type = $type;
        $this->request = $request;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Redis::hset('download_status:' . $this->jobID, 'status', 'processing');
        Redis::hset('download_status:' . $this->jobID, 'type', $this->type);
        Redis::hset('download_status:' . $this->jobID, 'date', date('Y-m-d H:i:s'));

        try {
            $filename = 'download/' . $this->type . '-' . $this->jobID . '.xlsx';

            switch ($this->type) {
                case 'report-periodic':
                    Excel::store(new ReportPeriodicExport($this->request), $filename, 'public');
                    break;
                default:
                    throw new \Exception('Jenis tidak valid.');
            }

            Redis::hset('download_status:' . $this->jobID, 'status', 'completed');
            Redis::hset('download_status:' . $this->jobID, 'filename', $filename);
        } catch (\Exception $e) {
            Redis::hset('download_status:' . $this->jobID, 'status', 'failed');
            Log::error('Gagal : ' . $e->getMessage());
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Redis::hset('download_status:' . $this->jobID, 'status', 'failed');
        Log::error('Gagal : ' . $exception->getMessage());
    }
}
