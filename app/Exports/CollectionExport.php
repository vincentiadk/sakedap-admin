<?php

namespace App\Exports;

use App\Models\Collection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\FromCollection;

class CollectionExport implements FromView, WithEvents
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
                $event->sheet->getStyle('A1:AB2')->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => '28A745']
                    ]
                ]);

                $event->sheet->getStyle('A4:AB4')->applyFromArray([
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

        $detail = $this->data;
        $data   = Collection::where(function ($query) use ($detail) {
            if ($detail['type']) {
                $query->where('type', $detail['type']);
            }

            if ($detail['publisher_id']) {
                $query->where('publisher_id', $detail['publisher_id']);
            }

            if ($detail['province_id']) {
                $query->whereHas('city', function ($query) use ($detail) {
                    $query->where('province_id', $detail['province_id']);
                });
            }

            if ($detail['method']) {
                $query->whereHas('collectionMedia', function ($query) use ($detail) {
                    $query->where('method', $detail['method']);
                });
            }

            if ($detail['extension']) {
                $query->whereHas('collectionMedia', function ($query) use ($detail) {
                    $query->where('extension', $detail['extension']);
                });
            }

            if ($detail['status']) {
                $query->where('status', $detail['status']);
            }

            if ($detail['param']) {
                if ($detail['param'] == 'annual') {
                    $query->whereYear($detail['type_date'], '>=', $detail['year_start'])
                        ->whereYear($detail['type_date'], '<=', $detail['year_end']);
                } else if ($detail['param'] == 'monthly') {
                    $query->whereMonth($detail['type_date'], '>=', $detail['month_start'])
                        ->whereYear($detail['type_date'], '>=', $detail['month_year_start'])
                        ->whereMonth($detail['type_date'], '<=', $detail['month_end'])
                        ->whereYear($detail['type_date'], '<=', $detail['month_year_start']);
                } else if ($detail['param'] == 'daily') {
                    $query->whereDate($detail['type_date'], '>=', $detail['day_start'])
                        ->whereDate($detail['type_date'], '<=', $detail['day_end']);
                }
            }

            if ($detail['type_date'] == 'created_at') {
                $query->whereNotNull('created_at');
            } else if ($detail['type_date'] == 'received_at') {
                $query->where('status', 2)
                    ->whereNotNull('received_at')
                    ->whereNotNull('received_by');
            } else if ($detail['type_date'] == 'updated_at') {
                $query->where('status', 3)
                    ->whereNotNull('rejected_at')
                    ->whereNotNull('rejected_by');
            } else if ($detail['type_date'] == 'validated_at') {
                $query->where('status', 2)
                    ->whereNotNull('received_at')
                    ->whereNotNull('received_by');
            }
        })
            ->where('parent_id', 0)
            ->get();

        return view('admin.export.collection', [
            'data'   => $data,
            'detail' => $detail
        ]);
    }
}
