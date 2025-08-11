<?php

namespace App\Exports;

use App\Models\Publisher;
use App\Models\CollectionCopy;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;

class CollectionDeliveryExport implements FromView, WithEvents
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
                $event->sheet->getStyle('A1:P2')->applyFromArray([
                    'fill' => [
                        'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => '28A745']
                    ]
                ]);

                $event->sheet->getStyle('A4:P4')->applyFromArray([
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

        $data   = CollectionCopy::join('delivery_form', 'collection_copies.delivery_form_id', '=', 'delivery_form.id')
            ->select(
                'collection_copies.*',
                DB::raw('SUM(CASE WHEN delivery_form.library_id = 1 THEN 1 ELSE 0 END) AS perpusnas_count'),
                DB::raw('SUM(CASE WHEN delivery_form.library_id <> 1 THEN 1 ELSE 0 END) AS province_count'),
                DB::raw('MIN(CASE WHEN delivery_form.library_id = 1 THEN delivery_date END) AS perpusnas_delivery_date'),
                DB::raw('MIN(CASE WHEN delivery_form.library_id = 1 THEN accepted_date END) AS perpusnas_accepted_date'),
                DB::raw('MIN(CASE WHEN delivery_form.library_id <> 1 THEN delivery_date END) AS province_delivery_date'),
                DB::raw('MIN(CASE WHEN delivery_form.library_id <> 1 THEN accepted_date END) AS province_accepted_date')
            )
            ->where('collection_copies.availability', '<>', '11') // Tidak ditolak
            ->where(function ($query) use ($detail) {

                if ($detail['library_id']) {
                    $query->where('library_id', $detail['library_id']);
                } else {
                    if (session('library_id') <> 1) {
                        $query->where('library_id', session('library_id'));
                    }
                }

                if ($detail['publisher_id']) {
                    $query->where('publisher_id', $detail['publisher_id']);
                }

                if ($detail['expedition_id']) {
                    $query->where('expedition_id', $detail['expedition_id']);
                }

                if ($detail['param']) {
                    if ($detail['param'] == 'annual') {
                        $query->whereYear('delivery_date', '>=', $detail['year_start'])
                            ->whereYear('delivery_date', '<=', $detail['year_end']);
                    } else if ($detail['param'] == 'monthly') {
                        $query->whereMonth('delivery_date', '>=', $detail['month_start'])
                            ->whereYear('delivery_date', '>=', $detail['month_year_start'])
                            ->whereMonth('delivery_date', '<=', $detail['month_end'])
                            ->whereYear('delivery_date', '<=', $detail['month_year_start']);
                    } else if ($detail['param'] == 'daily') {
                        $query->whereDate('delivery_date', '>=', $detail['day_start'])
                            ->whereDate('delivery_date', '<=', $detail['day_end']);
                    }
                }
            })
            ->groupBy('collection_copies.collection_id')
            ->get();

        return view('admin.export.collection_delivery', [
            'data'   => $data,
            'detail' => $detail
        ]);
    }
}
