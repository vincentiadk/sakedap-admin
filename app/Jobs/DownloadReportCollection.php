<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Download;
use App\Models\Location;
use Illuminate\Bus\Queueable;
use App\Exports\CollectionExport;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Imtigger\LaravelJobStatus\Trackable;
use Storage;

class DownloadReportCollection implements ShouldQueue
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
        $this->setInput(['type' => 'Download Laporan Koleksi']);
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
        $filename = 'Koleksi_' . date('H_i') . '_' . $user->username . '.xlsx';

        $this->setProgressNow(50);

        (new CollectionExport($this->data))->store('public/excel/collection/' . date('d-m-Y') . '/' . $filename, $this->location->location);
        $this->setProgressNow(75);

        Download::create([
            'user_id'     => $this->data['user_id'],
            'slug'        => 'collection',
            'link'        => 'public/excel/collection/' . date('d-m-Y') . '/' . $filename,
            'description' => json_encode([
                'param'            => $this->data['param'],
                'province_id'      => $this->data['province_id'],
                'method'           => $this->data['method'],
                'year_start'       => $this->data['year_start'],
                'year_end'         => $this->data['year_end'],
                'month_start'      => $this->data['month_start'],
                'month_end'        => $this->data['month_end'],
                'month_year_start' => $this->data['month_year_start'],
                'month_year_end'   => $this->data['month_year_end'],
                'day_start'        => $this->data['day_start'],
                'day_end'          => $this->data['day_end'],
                'type'             => $this->data['type'],
                'publisher_id'     => $this->data['publisher_id'],
                'extension'        => $this->data['extension']
            ]),
            'location_id'   => $this->location->id
        ]);

        $this->setProgressNow(100);
        $this->setOutput(['message' => 'success']);

        return;
    }
}
