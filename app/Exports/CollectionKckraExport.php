<?php

namespace App\Exports;

use App\Models\Collection;
use App\Models\CollectionCopy;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\FromCollection;

class CollectionKckraExport implements FromView, WithEvents
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

        $condition = [
            '1' => 'Sangat Baik',
            '2' => 'Baik',
            '3' => 'Cukup',
            '4' => 'Rusak'
        ];

        $availability = [
            'tersedia',
            'dalam pengiriman ke pengelolaan',
            'sedang didayagunakan',
            'hilang',
            'rusak',
            'sedang diperbaiki',
            'sedang diolah',
            'masih di ekspedisi',
            'sedang dicek',
            'diterima pengelohan',
            'diterima tim kckr',
            'ditolak',
        ];

        $data = CollectionCopy::whereHas('collection', function ($query) use ($detail) {
            if ($detail['publisher_id']) {
                $query->where('publisher_id', $detail['publisher_id']);
            }

            if ($detail['type']) {
                $query->where('type', $detail['type']);
            }

            if ($detail['province_id']) {
                $query->whereHas('publisher', function ($query) use ($detail) {
                    $query->where('province_id', $detail['province_id']);
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
                    ->whereNotNull('received_by')
                    ->whereNotNull('received_at');
            } else if ($detail['type_date'] == 'updated_at') {
                $query->where('status', 3)
                    ->whereNotNull('rejected_by')
                    ->whereNotNull('rejected_at');
            } else if ($detail['type_date'] == 'validated_at') {
                $query->where('status', 2)
                    ->whereNotNull('validated_by')
                    ->whereNotNull('validated_at');
            }

            if ($detail['library_id'] == '1') {
                $query->whereNotNull('mark_national');
            }

            $query->whereHas('depositHead', function ($query) {
                $query->whereIn('category', ['KC', 'KRA']);
            });

            $query->where(function ($query) use ($detail) {
                if ($detail['library_id'] != 1) {
                    $query->whereHas('publisher', function ($query) use ($detail) {
                        $query->where('province_id', $detail['province_id']);
                    });
                }
            })->where('parent_id', 0);
        })->whereHas('lib_location',  function ($query) use ($detail) {
            $query->where('publish', 1)->where('library_id', $detail['library_id']);
        })->where(function ($query) use ($detail) {
            if (!empty($detail['lib_loc_id'])) {
                $query->where('lib_loc_id', $detail['lib_loc_id']);
            }
            if (!empty($detail['condition'])) {
                $query->where('condition', $detail['condition']);
            }
            if (!empty($detail['availability'])) {
                $query->where('availability', $detail['availability']);
            }
        });

        if ($data->count() == 0) {
            //Log::debug($data->toSql());
            //Log::debug($detail);
        }

        $data = $data->get();
        // dd($data->count());

        return view('admin.export.collection_kckra', [
            'data'   => $data,
            'detail' => $detail,
            'condition' => $condition,
            'availability' => $availability,
        ]);
    }
}
