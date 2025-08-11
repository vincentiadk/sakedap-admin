<?php

namespace App\Exports;

use App\Models\ActivityLog;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;

class PerformanceUserExport implements FromView, WithEvents
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
                $event->sheet->getStyle('A1:J2')->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => '28A745']
                    ]
                ]);

                $event->sheet->getStyle('A4:J4')->applyFromArray([
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

        $data = ActivityLog::has('collection')
            ->where('log_name', 'collections')
            ->whereHas('collection', function ($query) use ($detail) {
                if ($detail['library_id'] != 1) {
                    $query->whereHas('city', function ($query) use ($detail) {
                        $query->where('province_id', $detail['province_id']);
                    });
                }
            })
            ->where(function ($query) use ($detail) {
                if ($detail['start_date'] && $detail['finish_date']) {
                    $query->whereDate('created_at', '>=', $detail['start_date'])
                        ->whereDate('created_at', '<=', $detail['finish_date']);
                } else if ($detail['start_date']) {
                    $query->whereDate('created_at', '>=', $detail['start_date']);
                } else if ($detail['finish_date']) {
                    $query->whereDate('created_at', '>=', $detail['finish_date']);
                }

                if ($detail['causer_id']) {
                    $query->where('causer_id', $detail['causer_id']);
                }

                if ($detail['type']) {
                    if ($detail['type'] == 1) {
                        $query->where('description', 'like', "%menolak koleksi%");
                    } else if ($detail['type'] == 2) {
                        $query->where('description', 'like', "%menyetujui koleksi%");
                    } else if ($detail['type'] == 3) {
                        $query->where('description', 'like', "%mengubah data koleksi%");
                    } else if ($detail['type'] == 4) {
                        $query->where('description', 'like', "%mengunci koleksi%")
                            ->orWhere('description', 'like', "%membuka kunci koleksi%");
                    } else if ($detail['type'] == 5) {
                        $query->where('description', 'like', "%koleksi bermasalah%");
                    }
                }
            })
            ->get();

        return view('admin.export.performance_user', [
            'data' => $data,
            'detail' => $detail
        ]);
    }
}
