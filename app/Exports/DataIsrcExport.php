<?php

namespace App\Exports;

use App\Models\Solr;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;

class DataIsrcExport implements FromView, WithEvents
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

        $request = (object)$this->data;
        $search = isset($request->search) ? $request->search : '';

        $model = DB::connection('isrc')
            ->table('assets')
            ->join('producers', 'assets.producer_id', '=', 'producers.id')
            ->join('isrc_requests', 'assets.isrc_request_id', '=', 'isrc_requests.id')
            ->select('assets.*', 'producers.name as producer_name', 'isrc_requests.*')
            ->where(function ($query) use ($request) {
                if ($request->file_type) {
                    $query->where('asset_type', $request->file_type);
                }
                if ($request->title) {
                    $query->where('title', 'LIKE', "%{$request->title}%");
                }
                if ($request->publisher_id) {
                    $query->where('producer_id', $request->publisher_id);
                }
                if ($request->publication_year) {
                    $query->where('year', $request->publication_year);
                }

                if ($request->code) {
                    $query->where('isrc_number', 'like', "%{$request->code}%");
                }
                if ($request->param) {
                    if ($request->param == 'annual') {
                        $query->whereYear('isrc_requests.validation_date', '>=', $request->year_start)
                            ->whereYear('isrc_requests.validation_date', '<=', $request->year_end);
                    } else if ($request->param == 'monthly') {
                        $query->whereMonth('isrc_requests.validation_date', '>=', $request->month_start)
                            ->whereYear('isrc_requests.validation_date', '>=', $request->month_year_start)
                            ->whereMonth('isrc_requests.validation_date', '<=', $request->month_end)
                            ->whereYear('isrc_requests.validation_date', '<=', $request->month_year_start);
                    } else if ($request->param == 'daily') {
                        $query->whereDate('isrc_requests.validation_date', '>=', $request->day_start)
                            ->whereDate('isrc_requests.validation_date', '<=', $request->day_end);
                    }
                }
            })->where('isrc_requests.status', 'approved');

        if ($search) {
            $model->where(function ($query) use ($search) {
                $query->where('producers.name', 'like', "%{$search}%")
                    ->orWhere('composer_name', 'like', "%{$search}%")
                    ->orWhere('isrc_number', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%")
                    ->orWhere('asset_type', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            });
        }

        $data = $model->get();

        return view($request->view . '.export.data_isrc', [
            'data'   => $data,
            'detail' => $request
        ]);
    }
}
