<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Download;
use App\Models\Location;
use App\Exports\CollectionDeliveryExport;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Imtigger\LaravelJobStatus\Trackable;
use Storage;

class DownloadReportCollectionDelivery implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $location;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
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

        $user     = User::find($this->data['user_id']);
        $filename = 'Pengiriman_KCKRA_' . date('H_i') . '_' . $user->username . '.xlsx';

        (new CollectionDeliveryExport($this->data))->store('public/excel/collection/' . date('d_m_Y') . '/' . $filename, $this->location->location);
        Download::create([
            'user_id'     => $this->data['user_id'],
            'slug'        => 'collection_delivery',
            'link'        => 'public/excel/collection/' . date('d_m_Y') . '/' . $filename,
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
                'collection'       => $this->data['collection'],
                'publisher_id'     => $this->data['publisher_id']
            ]),
            'location_id'   => $this->location->id,
        ]);

        return;
    }
}
