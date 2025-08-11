<?php

namespace App\Exports;

use App\Models\Collection;
use App\Models\Publisher;
use App\Helper\GeneralHelper;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class MetadataIsbnBulkExport implements FromView, ShouldAutoSize, WithEvents
{

    use Exportable;

    protected $publisher_id;
    protected $collection_not_received = [];

    public function __construct($publisher_id)
    {
        $this->publisher_id = $publisher_id;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:I1')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'e2e2e2'],
                    ],
                ]);
            },
        ];
    }

    public function view(): View
    {
        ini_set('memory_limit', '-1');

        $publisher = Publisher::where('code_system', $this->publisher_id)->first();
        if ($publisher) {
            $collectionAlready = Collection::select('code')
                ->whereNotNull('code')
                ->where('publisher_id', $publisher->id)
                ->where('type', 1)
                ->pluck('code');

            $totalData = 0;
            $pageNum = 1;
            $anyData = true;

            while ($anyData) {
                $getData = GeneralHelper::getDataElek($publisher->code_system, $pageNum, "0", "");
                $this->parseData($getData, $publisher->name);
                $totalData += count($getData);
                if (count($getData) < 10) {
                    $anyData = false;
                } else {
                    $pageNum++;
                }
                sleep(2);
            }


            foreach ($this->collection_not_received as $key => $value) {
                foreach ($collectionAlready as $col) {
                    if (isset($this->collection_not_received[$key]['isbnno']) && ($this->collection_not_received[$key]['isbnno'] == $col)) {
                        unset($this->collection_not_received[$key]);
                    }
                }
            }
            return view('publisher.export.metadata_isbn', [
                'data' => $this->collection_not_received,
            ]);
        } else {
            return view('publisher.export.metadata_isbn', [
                'data' => [],
            ]);
        }
    }
    public function parseData($dataIsbn, $publisher_name)
    {
        foreach ($dataIsbn as $d) {
            $this->collection_not_received[] = [
                "nama_penerbit" => $publisher_name,
                "title" => $d["title"],
                "isbnno" => $d["isbn"],
                "kepeng" => $d["creator"],
                "tahun_terbit" => $d["year"],
                "created_at" => $d["created_at"]
            ];
        }
    }
}
