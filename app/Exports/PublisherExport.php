<?php

namespace App\Exports;

use App\Models\Publisher;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\FromCollection;

class PublisherExport implements FromView, WithEvents
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
                $event->sheet->getStyle('A1:M2')->applyFromArray([
                    'fill' => [
                        'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => '28A745']
                    ]
                ]);

                $event->sheet->getStyle('A4:M4')->applyFromArray([
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

        $detail = $this->data;
        // $data =  Publisher::where(function($query) use ($detail) {
        //     if($detail['type']) {
        //         $query->where('type', $detail['type']);
        //     }

        //     if($detail['collection']) {
        //         $query->whereHas('collection', function($query) use ($detail) {
        //             $query->where('type', $detail['collection']);
        //         });
        //     }

        //     if($detail['province_id']) {
        //         $query->where('province_id', $detail['province_id']);
        //     }

        //     if($detail['param']) {
        //         if($detail['param'] == 'annual') {
        //             $query->whereYear('created_at', '>=', $detail['year_start'])
        //                 ->whereYear('created_at', '<=', $detail['year_end']);
        //         } else if($detail['param'] == 'monthly') {
        //             $query->whereMonth('created_at', '>=', $detail['month_start'])
        //                 ->whereYear('created_at', '>=', $detail['month_year_start'])
        //                 ->whereMonth('created_at', '<=', $detail['month_end'])
        //                 ->whereYear('created_at', '<=', $detail['month_year_start']);
        //         } else if($detail['param'] == 'daily') {
        //             $query->whereDate('created_at', '>=', $detail['day_start'])
        //                 ->whereDate('created_at', '<=', $detail['day_end']);
        //         }
        //     }
        // })->get();

        // whereHas('collection', function($query) {
        //     $query->where('status', 2)
        //         ->whereNotNull('received_at')
        //         ->whereNotNull('received_by');
        // })

        $data   = Publisher::where(function ($query) use ($detail) {
            if ($detail['type']) {
                $query->where('type', $detail['type']);
            }

            if ($detail['province_id']) {
                $query->where('province_id', $detail['province_id']);
            }
            if ($detail['collection']) {
                $query->whereHas('collection', function ($query) use ($detail) {
                    $query->where('type', $detail['collection'])->where('status', 2)
                        ->whereNotNull('received_at')
                        ->whereNotNull('received_by');
                });
            }

            if ($detail['param']) {
                if ($detail['param'] == 'annual') {
                    $query->whereYear('created_at', '>=', $detail['year_start'])
                        ->whereYear('created_at', '<=', $detail['year_end']);
                } else if ($detail['param'] == 'monthly') {
                    $query->whereMonth('created_at', '>=', $detail['month_start'])
                        ->whereYear('created_at', '>=', $detail['month_year_start'])
                        ->whereMonth('created_at', '<=', $detail['month_end'])
                        ->whereYear('created_at', '<=', $detail['month_year_start']);
                } else if ($detail['param'] == 'daily') {
                    $query->whereDate('created_at', '>=', $detail['day_start'])
                        ->whereDate('created_at', '<=', $detail['day_end']);
                }
            }
        })
            ->whereHas('collection', function ($query) {
                $query->where('status', 2)
                    ->whereNotNull('received_at')
                    ->whereNotNull('received_by');
            })
            ->get();

        return view('admin.export.publisher', [
            'data'   => $data,
            'detail' => $detail
        ]);
    }
}
