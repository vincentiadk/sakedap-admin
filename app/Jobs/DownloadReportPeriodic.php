<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Download;
use App\Models\Location;
use Illuminate\Bus\Queueable;
use App\Exports\PeriodicExport;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Imtigger\LaravelJobStatus\Trackable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DownloadReportPeriodic implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    protected $data;
    protected $location;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->prepareStatus();
        $this->data = $data;
        $this->setInput(['type' => 'Download Laporan Periodic']);
        $this->location = Location::where('active', 1)->first();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');

        $this->setProgressMax(100);
        $this->setProgressNow(20);

        $user     = User::find($this->data['user_id']);
        $filename = 'Periodic_' . date('H_i') . '_' . $user->username . '.xlsx';

        $this->setProgressNow(50);

        (new PeriodicExport($this->data))->store('public/excel/periodic/' . date('d_m_Y') . '/' . $filename, $this->location->location);

        $this->setProgressNow(75);

        Download::create([
            'user_id'     => $this->data['user_id'],
            'slug'        => 'periodic',
            'link'        => 'public/excel/periodic/' . date('d_m_Y') . '/' . $filename,
            'location_id' => $this->location->id,
            'description' => json_encode([
                'date'   => $this->data['date'],
                'yearly' => $this->data['yearly'],
                'status' => $this->data['status']
            ]),
        ]);

        //\Log::info($this->location);
        $this->setProgressNow(100);
        $this->setOutput(['message' => 'success']);

        return;
    }
}
