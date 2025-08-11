<?php

namespace App\Exports;

use App\Models\Solr;
use App\Models\Province;
use Illuminate\Support\Arr;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;

class BillIsbnExport implements FromView, WithEvents
{
    use Exportable;

    protected $data;
    protected $dataIsbn = [];

    public function __construct($data)
    {
        $this->data = $data;
        libxml_use_internal_errors(true);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:K2')->applyFromArray([
                    'fill' => [
                        'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => '28A745']
                    ]
                ]);

                $event->sheet->getStyle('A4:K4')->applyFromArray([
                    'fill' => [
                        'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => '007BFF']
                    ]
                ]);
            }
        ];
    }

    public function view(): View
    {
        ini_set('memory_limit', '-1');

        $filter = [];
        $detail = (object)$this->data;

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
                array_push($data, [$detail->type_date => "[$start TO $finish]"]);
            } else {
                array_push($data, ['created_date' => "[$start TO $finish]"]);
            }
        }

        if ($detail->province_id) {
            $province = Province::find($detail->province_id);
            array_push($data, ['provinsi' => '"' . $province->name . '"']);
        }

        if ($detail->publisher_id) {
            array_push($data, ['kd_penerbit' => $detail->publisher_id == 1 ? 'elek' : 'cetak']);
        }

        if ($detail->type) {
            array_push($data, ['jenis' => $detail->type == 1 ? 'elek' : 'cetak']);
        }
        if ($detail->title) {
            array_push($data, ['title' => '"' . $detail->title . '"']);
        }
        if ($detail->code) {
            array_push($data, ['code' => '*' . str_replace('-', '', $detail->code) . '*']);
        }
        if ($detail->kepeng) {
            array_push($data, ['kepeng' => '*' . $detail->kepeng . '*']);
        }

        if ($detail->status) {
            if ($detail->status == 1) {
                array_push($data, ['-received_date' => "[* TO *]"]);
            } else {
                array_push($data, ['received_date' => "[$start TO $finish]"]);
            }
        }

        $data = Solr::downloadData('isbn', 'complete', Arr::collapse($filter));

        return view($detail->view . '.export.bill_isbn_only', [
            'data'   => $data,
            'detail' => $detail
        ]);
    }
}
