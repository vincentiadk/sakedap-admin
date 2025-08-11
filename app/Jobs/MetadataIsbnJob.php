<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Download;
use App\Models\Location;
use Illuminate\Bus\Queueable;
use App\Exports\MetadataIsbnBulkExport;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Imtigger\LaravelJobStatus\Trackable;
use Storage;


class MetadataIsbnJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Trackable;

    protected $data;
    public $timeout = 86400;
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
        $this->setInput(['type' => 'Download Metadata ISBN']);
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

        $download = Download::create([
            'user_id'   => $this->data['user_id'],
            'slug'      => 'metadata-isbn',
            'status'    => 'Sedang Diproses',
            'location_id'   => $this->location->id
        ]);

        $filename = 'Metadata_Isbn' . date('H_i') . '.xlsx';

        $this->setProgressNow(50);

        try {
            (new MetadataIsbnBulkExport($this->data))->store('public/excel/publisher/' . date('d_m_Y') . '/' . $filename, $this->location->location);

            $this->setProgressNow(75);

            $download->update([
                'link'  => 'public/excel/publisher/' . date('d_m_Y') . '/' . $filename,
                'status' => 'Selesai',
            ]);


            $this->setProgressNow(100);
            $this->setOutput(['message' => 'success']);
        } catch (\Exception $e) {
            $download->update([
                'status'    => 'Gagal'
            ]);
            $this->setOutput(['message' => $e->getMessage()]);
        }
    }
}
