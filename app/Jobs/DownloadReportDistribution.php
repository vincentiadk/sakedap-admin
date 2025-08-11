<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Download;
use App\Models\Location;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Exports\ReportDistributionExport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DownloadReportDistribution implements ShouldQueue
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

        $user = User::find($this->data['user_id']);
        $filename = 'Laporan_Distribusi_' . date('H_i') . '_' . $user->username . '.xlsx';

        (new ReportDistributionExport($this->data))->store('public/excel/report/distribution/' . date('d_m_Y') . '/' . $filename, $this->location->location);
        Download::create([
            'user_id' => $this->data['user_id'],
            'slug' => 'report_distribution',
            'link' => 'public/excel/report/distribution/' . date('d_m_Y') . '/' . $filename,
            'description' => json_encode([
                'expedition_id' => $this->data['expedition_id'],
                'publisher_id' => $this->data['publisher_id'],
                'library_id' => $this->data['library_id'],
                'delivery_date' => $this->data['delivery_date'],
                'accepted_date' => $this->data['accepted_date'],
                'status' => $this->data['status'],
            ]),
            'location_id'   => $this->location->id,
        ]);

        return;
    }
}
