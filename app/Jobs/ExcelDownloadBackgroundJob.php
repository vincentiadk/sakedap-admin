<?php

namespace App\Jobs;

use App\Exports\ReportLogExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExecutorExport;
use App\Exports\ReportPeriodicExport;
use Illuminate\Support\Facades\Redis;
use App\Exports\ReportPromotionExport;
use Illuminate\Queue\SerializesModels;
use App\Exports\ReportCollectionExport;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Exports\ReportPerformanceUserExport;

class ExcelDownloadBackgroundJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

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
        Redis::hset('download:' . $this->jobID, 'status', 'processing');
        Redis::hset('download:' . $this->jobID, 'type', $this->type);
        Redis::hset('download:' . $this->jobID, 'date', date('Y-m-d H:i:s'));

        try {
            $filename = 'download/' . $this->type . '-' . $this->jobID . '.xlsx';
            $disk = 'public';

            switch ($this->type) {
                case 'report-periodic':
                    Excel::store(new ReportPeriodicExport($this->request), $filename, $disk);
                    break;
                case 'report-executor':
                    Excel::store(new ReportExecutorExport($this->request), $filename, $disk);
                    break;
                case 'report-collection':
                    Excel::store(new ReportCollectionExport($this->request), $filename, $disk);
                    break;
                case 'report-performance-user':
                    Excel::store(new ReportPerformanceUserExport($this->request), $filename, $disk);
                case 'report-log':
                    Excel::store(new ReportLogExport($this->request), $filename, $disk);
                    break;
                case 'report-promotion':
                    Excel::store(new ReportPromotionExport($this->request), $filename, $disk);
                    break;
                default:
                    throw new \Exception('Jenis tidak valid.');
            }

            Redis::hset('download:' . $this->jobID, 'status', 'completed');
            Redis::hset('download:' . $this->jobID, 'filename', $filename);
        } catch (\Exception $e) {
            Redis::hset('download:' . $this->jobID, 'status', 'failed');
            Log::channel('report')->error('Gagal : ' . $e->getMessage());
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Redis::hset('download:' . $this->jobID, 'status', 'failed');
        Log::channel('report')->error('Gagal : ' . $exception->getMessage());
    }
}
