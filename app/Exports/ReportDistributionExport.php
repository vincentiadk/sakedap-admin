<?php

namespace App\Exports;

use App\Models\DeliveryForm;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class ReportDistributionExport implements FromView
{
    use Exportable;

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
        libxml_use_internal_errors(true);
    }

    public function view(): View
    {
        ini_set('memory_limit', '-1');

        $detail = $this->data;
        $data = DeliveryForm::where(function ($query) use ($detail) {
            if ($detail['expedition_id']) {
                $query->where('expedition_id', $detail['expedition_id']);
            }

            if ($detail['publisher_id']) {
                $query->where('publisher_id', $detail['publisher_id']);
            }

            if ($detail['library_id']) {
                $query->where('library_id', $detail['library_id']);
            }

            if ($detail['delivery_date']) {
                $query->whereDate('delivery_date', $detail['delivery_date']);
            }

            if ($detail['accepted_date']) {
                $query->whereDate('accepted_date', $detail['accepted_date']);
            }

            if ($detail['status']) {
                $query->where('status', $detail['status']);
            }
        })->get();

        return view('admin.export.report_distribution', [
            'data'   => $data,
            'detail' => $detail
        ]);
    }
}
