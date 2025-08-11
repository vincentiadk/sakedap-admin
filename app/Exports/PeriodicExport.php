<?php

namespace App\Exports;

use App\Models\Collection;
use App\Helper\GeneralHelper;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;

class PeriodicExport implements FromView, WithEvents
{

    use Exportable;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
        libxml_use_internal_errors(true);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:G2')->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => '28A745']
                    ]
                ]);

                $event->sheet->getStyle('A4:G4')->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => '007BFF']
                    ]
                ]);
            }
        ];
    }

    public function view(): View
    {
        ini_set('memory_limit', '-1');

        $detail   = $this->data;
        $data     = [];
        $book     = 0;
        $partitur = 0;
        $map      = 0;
        $serial   = 0;
        $audio    = 0;
        $film     = 0;

        for ($i = 1; $i <= 12; $i++) {
            $data[]['data']['month'] = GeneralHelper::getMonth($i < 10 ? '0' . $i : $i);
            for ($col = 1; $col <= 6; $col++) {
                $query = Collection::where(function ($query) use ($i, $detail) {
                    $query->whereMonth($detail['date'], $i)
                        ->whereYear($detail['date'], $detail['yearly']);
                })
                    ->where('status', $detail['status'])
                    ->where('type', $col)
                    ->where('parent_id', 0)
                    ->count();

                if ($col == 1) {
                    $book += $query;
                } else if ($col == 2) {
                    $partitur += $query;
                } else if ($col == 3) {
                    $map += $query;
                } else if ($col == 4) {
                    $serial += $query;
                } else if ($col == 5) {
                    $audio += $query;
                } else if ($col == 6) {
                    $film += $query;
                }

                $index = $i - 1;
                $data[$index]['data']['item'][] = $query;
            }
        }

        $data[]['data'] = [
            'month' => 'TOTAL',
            'item'  => [$book, $partitur, $map, $serial, $audio, $film]
        ];

        return view('admin.export.periodic', [
            'data'   => $data,
            'detail' => $detail
        ]);
    }
}
