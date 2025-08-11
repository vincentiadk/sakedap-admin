<?php

namespace App\Exports;

use App\Models\Solr;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;

class DataIsrcExportOld implements FromView, WithEvents
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
                $event->sheet->getStyle('A1:H2')->applyFromArray([
                    'fill' => [
                        'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => '28A745']
                    ]
                ]);

                $event->sheet->getStyle('A4:H4')->applyFromArray([
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

            array_push($filter, ['publication_date' => "[$start TO $finish]"]);
        }

        if ($detail->title) {
            array_push($filter, ['title' => '"' . $detail->title . '"']);
        }

        if ($detail->publisher_id) {
            $publisher      = DB::connection('isrc')->table('producers')->where('id', $detail->publisher_id)->first();
            $publisher_name = $publisher ? $publisher->name : null;

            array_push($filter, ['producer_name' => '"' . $publisher_name . '"']);
        }

        if ($detail->publication_year) {
            array_push($filter, ['year' => $detail->publication_year]);
        }

        if ($detail->code) {
            array_push($filter, ['isrc_number' => '"' . $detail->code . '"']);
        }

        if ($detail->file_type) {
            array_push($filter, ['asset_type' => $detail->file_type]);
        }

        $data = Solr::data('isrc', 'assets', Arr::collapse($filter));
        return view($detail->view . '.export.data_isrc', [
            'data'   => $data,
            'detail' => $detail
        ]);
    }
}
