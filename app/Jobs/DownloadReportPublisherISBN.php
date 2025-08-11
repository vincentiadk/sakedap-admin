<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Download;
use App\Models\Location;
use App\Models\Province;
use Illuminate\Bus\Queueable;
use App\Exports\PublisherExport;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Imtigger\LaravelJobStatus\Trackable;
use Storage;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Arr;
use App\Models\Solr;

class DownloadReportPublisherISBN implements ShouldQueue
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
        $this->setInput(['type' => 'Download Laporan Publisher ISBN']);
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
        $exl = new FastExcel;
        $this->setProgressMax(100);
        $this->setProgressNow(20);

        $user     = User::find($this->data['user_id']);
        $filename = date('d-m-Y') . '-' . 'Publisher_ISBN_' . date('H_i') . '_' . $user->username . '.xlsx';

        $this->setProgressNow(50);
        $filter = [];
        $detail = (object)$this->data;
        $filter_penerbit = [];
        if ($detail->param) {
            if ($detail->param == 'annual') {
                $start  = $detail->year_start . '-01-01T00:00:00Z';
                $finish = $detail->year_end . '-12-31T23:59:59Z';
            } else if ($detail->param == 'monthly') {
                $start  = $detail->month_year_start . '-' . $detail->month_start . '-01T00:00:00Z';
                $finish = date('Y-m-t', strtotime($detail->month_year_end . '-' . $detail->month_end)) . 'T23:59:59Z';
            } else if ($detail->param == 'daily') {
                $start  = $detail->day_start . 'T00:00:00Z';
                $finish = $detail->day_end . 'T23:59:59Z';
            }

            if (isset($detail->type_date)) {
                array_push($filter, [$detail->type_date => "[$start TO $finish]"]);
            } else {
                array_push($filter, ['created_date' => "[$start TO $finish]"]);
            }
        }

        if ($detail->publisher_id) {
            array_push($filter, ['kd_penerbit' => $detail->publisher_id]);
            array_push($filter_penerbit, ['kd_penerbit' => $detail->publisher_id]);
        }

        if ($detail->province_id) {
            $province = Province::find($detail->province_id);
            array_push($filter, ['provinsi' => '"' . $province->name . '"']);
            array_push($filter_penerbit, ['provinsi' => '"' . $province->name . '"']);
        }

        $result        = Solr::downloadData('isbn', 'mst_penerbit', Arr::collapse($filter_penerbit));
        (new FastExcel($this->generateRow($result, $this->data)))->export(Storage::disk($this->location->location)->path('public/excel/publisher_isbn/' .  $filename));
        $this->setProgressNow(75);
        $this->setProgressNow(100);
        $this->setOutput(['message' => 'success']);
        Download::create([
            'user_id'     => $this->data['user_id'],
            'slug'        => 'publisher_isbn',
            'link'        => 'public/excel/publisher_isbn/' . $filename,
            'description' => json_encode([
                'param'            => $this->data['param'],
                'province_id'      => $this->data['province_id'],
                'year_start'       => $this->data['year_start'],
                'year_end'         => $this->data['year_end'],
                'month_start'      => $this->data['month_start'],
                'month_end'        => $this->data['month_end'],
                'month_year_start' => $this->data['month_year_start'],
                'month_year_end'   => $this->data['month_year_end'],
                'day_start'        => $this->data['day_start'],
                'day_end'          => $this->data['day_end'],
                'publisher_id'     => $this->data['publisher_id'],
                'type_date'        => $this->data['type_date'],

            ]),
            'location_id'   => $this->location->id
        ]);
        return;
    }

    public function generateRow($result, $request)
    {
        $nomor = 1;
        foreach ($result as $d) {
            $summary    = Solr::summaryBillIsbnNew('isbn', $d["kd_penerbit"], $request);
            $total_bill = number_format($summary['total_all_bill']);
            $total_rest = number_format($summary['total_all_rest']);
            $total_all  =  $total_rest . ' / ' . $total_bill;
            $percentage = $summary['percentage'] == "-" ? $summary['percentage'] : $summary['percentage'] . '%';

            yield [
                'no' => $nomor,
                'nama_penerbit' => $d['nama_penerbit'] ? $d['nama_penerbit'] : '',
                'provinsi' => $d["provinsi"] ? $d["provinsi"] : '',
                'nama_kota' => $d["nama_kota"] ? $d["nama_kota"] : '',
                'admin_phone' => $d["admin_phone"] ? $d["admin_phone"] : '',
                'alamat_penerbit' => $d["alamat_penerbit"] ? $d["alamat_penerbit"] : '',
                'kode_pos' => $d["kode_pos"] ? $d["kode_pos"] : '',
                'nama_gedung' => $d["nama_gedung"] ? $d["nama_gedung"] : '',
                'prosentase' => $percentage,
                'permintaan_elek' => number_format($summary['request_elek']),
                'diterima_elek' => number_format($summary['received_elek']),
                'tagihan_elek' => number_format($summary['total_bill_elek']),
                'permintaan_cetak' => number_format($summary['request_cetak']),
                'diterima_cetak' => number_format($summary['received_cetak']),
                'tagihan_cetak' => number_format($summary['total_bill_cetak']),
                'total diterima / tagihan' => $total_all
            ];
            $nomor++;
        }
    }
}
