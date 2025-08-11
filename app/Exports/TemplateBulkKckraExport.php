<?php

namespace App\Exports;

use App\Models\LibraryLocation;
use App\Models\Location;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Files\LocalTemporaryFile;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Maatwebsite\Excel\Excel;

class TemplateBulkKckraExport implements WithEvents
{
    protected $location;
    protected $selects;
    protected $row_count;
    protected $column_count;
    protected $lib_loc;
    protected $is_serial;
    protected $template;

    public function __construct(array $array)
    {
        $this->location = Location::where('active', 1)->first();
        $lib_loc = LibraryLocation::where('library_id', session('library_id'))->where('publish', '1')->pluck('name', 'id')->toArray();

        $this->row_count = 4; //number of rows that will have the dropdown
        $this->column_count = 5; //number of columns to be auto sized
        $this->lib_loc = $lib_loc;
        $this->is_serial = ($array[0] == 'serial') ? 1 : 0;
        if ($this->is_serial) {
            $this->template = 'kckra_bulk_serial_template.xlsx';
            $selects = [  //selects should have column_name and options
                ['columns_name' => 'G', 'options' => array_values($lib_loc)],
                ['columns_name' => 'H', 'options' => ['Sangat Baik', 'Baik', 'Cukup', 'Rusak']],
            ];
        } else {
            $this->template = 'kckra_bulk_non_serial_template.xlsx';
            $selects = [  //selects should have column_name and options
                ['columns_name' => 'J', 'options' => array_values($lib_loc)],
                ['columns_name' => 'K', 'options' => ['Sangat Baik', 'Baik', 'Cukup', 'Rusak']],
            ];
        }
        $this->selects = $selects;
    }

    public function registerEvents(): array
    {
        return [
            BeforeWriting::class => function (BeforeWriting $event) {
                // dd(Storage::disk($this->location->location)->path('public/template/' . $this->template));
                $templateFile = new LocalTemporaryFile(Storage::disk($this->location->location)->path('public/template/' . $this->template));
                $event->writer->reopen($templateFile, Excel::XLSX);
                $sheet = $event->writer->getSheetByIndex(0);
                $this->populateSheet($sheet);
                $event->writer->getSheetByIndex(0)->export($event->getConcernable()); // call the export on the first sheet

                if ($this->is_serial) {
                    $sheet = $event->writer->getSheetByIndex(1);
                    $col_start = 25;
                    foreach ($this->lib_loc as $key => $value) {
                        $sheet->setCellValue('A' . $col_start, $key);
                        $sheet->setCellValue('B' . $col_start, $value);
                        $col_start++;
                    }

                    $event->writer->getSheetByIndex(1)->export($event->getConcernable()); // call the export on the second sheet
                } else {
                    $sheet = $event->writer->getSheetByIndex(1);
                    $col_start = 34;
                    foreach ($this->lib_loc as $key => $value) {
                        $sheet->setCellValue('A' . $col_start, $key);
                        $sheet->setCellValue('B' . $col_start, $value);
                        $col_start++;
                    }

                    $event->writer->getSheetByIndex(1)->export($event->getConcernable()); // call the export on the second sheet
                }


                return $event->getWriter()->getSheetByIndex(0);
            },
        ];
    }

    private function populateSheet($sheet)
    {
        foreach ($this->selects as $select) {
            $drop_column = $select['columns_name'];
            $options = $select['options'];
            // set dropdown list for first data row
            $validation = $sheet->getCell("{$drop_column}2")->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Input error');
            $validation->setError('Value is not in list.');
            $validation->setPromptTitle('Pick from list');
            $validation->setPrompt('Please pick a value from the drop-down list.');
            $validation->setFormula1(sprintf('"%s"', implode(',', $options)));

            $sheet->setCellValue($drop_column . '2', reset($options));

            // clone validation to remaining rows
            for ($i = 3; $i <= $this->row_count; $i++) {
                $sheet->getCell("{$drop_column}{$i}")->setDataValidation(clone $validation);
                $sheet->setCellValue($drop_column . $i, reset($options));
            }
        }
    }
}
