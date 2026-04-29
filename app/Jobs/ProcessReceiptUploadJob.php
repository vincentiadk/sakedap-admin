<?php

namespace App\Jobs;

use App\Helpers\QueryAPI;
use App\Helpers\ISBN;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ProcessReceiptUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $historyId;
    protected $user;

    public $timeout = 1200;

    public function __construct($historyId, array $user = [])
    {
        $this->historyId = $historyId;
        $this->user = $user;
    }

    public function handle()
    {
        $summaryKey = "receipt_upload:{$this->historyId}:summary";

        try {
            $history = QueryAPI::get("
                SELECT *, p.name, p.id as penerbit_id, p.province_id, p.city_id
                FROM e_receipt_upload_history 
                LEFT JOIN PENERBIT p on p.id = e_receipt_upload_history.penerbit_id
                WHERE e_receipt_upload_history.id = {$this->historyId}
            ", true);

            if (!$history) {
                throw new \Exception("History upload resi tidak ditemukan.");
            }

            $penerbitId = $history->PENERBIT_ID;

            QueryAPI::update('e_receipt_upload_history', [
                'status' => 'processing',
                'notes' => 'File sedang diproses.',
                'started_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ], "id = {$this->historyId}");

            if (!file_exists($history->FILE_PATH)) {
                throw new \Exception("File Excel tidak ditemukan di server.");
            }

            $rows = Excel::toArray([], $history->FILE_PATH)[0] ?? [];

            if (count($rows) <= 1) {
                throw new \Exception("File Excel kosong atau tidak memiliki data.");
            }

            $dataRows = array_slice($rows, 1);
            $totalRows = count($dataRows);

            QueryAPI::update('e_receipt_upload_history', [
                'total_rows' => $totalRows,
                'processed_rows' => 0,
                'success_rows' => 0,
                'failed_rows' => 0,
                'notes' => 'Mulai membaca data Excel.',
            ], "id = {$this->historyId}");

            Redis::setex($summaryKey, 86400, json_encode([
                'status' => 'processing',
                'total_rows' => $totalRows,
                'processed_rows' => 0,
                'success_rows' => 0,
                'failed_rows' => 0,
                'notes' => 'Mulai membaca data Excel.',
            ]));

            $codes = collect($dataRows)
                ->pluck(0)
                ->map(fn ($x) => str_replace('-', '', trim((string) $x)))
                ->filter()
                ->unique()
                ->values();

            $isbnMap = collect();

            if ($codes->isNotEmpty()) {
                $result = ISBN::get('search', [
                    'code' => $codes->implode(','),
                    'start' => 0,
                    'length' => 5000,
                ]);

                $isbnMap = collect($result->data ?? [])
                    ->keyBy(fn ($row) => str_replace('-', '', (string) ($row->code ?? $row->isbn ?? '')));
            }

            $processedRows = 0;
            $successRows = 0;
            $failedRows = 0;
            $validDetails = [];

            foreach ($dataRows as $index => $row) {
                $rowNumber = $index + 2;
                $rowKey = "receipt_upload:{$this->historyId}:row:{$rowNumber}";

                $isbn = trim((string)($row[0] ?? ''));
                $issn = trim((string)($row[1] ?? ''));
                $title = trim((string)($row[2] ?? ''));
                $jenis = (int)($row[3] ?? 0);
                $volume = trim((string)($row[4] ?? ''));
                $tahun_terbit = (int)($row[5] ?? 2026);
                $ttesAwalRaw = $row[6] ?? null;
                $ttesAkhirRaw = $row[7] ?? null;
                $nilaiAset = (int)($row[8] ?? 0);
                $receivedDateRaw = $row[9] ?? null;
                $qtyDelivery = (int)($row[10] ?? 0);

                Redis::setex($rowKey, 86400, json_encode([
                    'row_number_upload' => $rowNumber,
                    'status' => 'running',
                    'isbn' => $isbn,
                    'issn' => $issn,
                    'title' => $title,
                    'qty_delivery' => $qtyDelivery,
                    'message' => 'Sedang diproses.',
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                ]));

                try {
                    $ttesAwal = $this->convertExcelDate($ttesAwalRaw);
                    $ttesAkhir = $this->convertExcelDate($ttesAkhirRaw);
                    $receivedDate = $this->convertExcelDate($receivedDateRaw);

                    if (!$receivedDate) {
                        $receivedDate = Carbon::now()->format('Y-m-d');
                    }

                    if (!$ttesAwal && $ttesAkhir) {
                        $ttesAwal = $ttesAkhir;
                    }

                    if (!$ttesAkhir && $ttesAwal) {
                        $ttesAkhir = $ttesAwal;
                    }

                    if (!$title) {
                        throw new \Exception("Judul wajib diisi.");
                    }

                    if ($qtyDelivery <= 0) {
                        throw new \Exception("Jumlah dikirim harus lebih dari 0.");
                    }

                    if (!$isbn && !$issn) {
                        throw new \Exception("ISBN atau ISSN wajib diisi.");
                    }

                    if ($issn || in_array($jenis, [4, 5])) {
                        if (!$volume) {
                            throw new \Exception("Volume wajib untuk karya serial seperti jurnal/koran/majalah/buletin.");
                        }
                    }

                    $catalogId = null;
                    $penerbitTerbitanId = null;
                    $collectionTypeId = $jenis;
                    $price = $nilaiAset;
                    $penerbitIsbnId = null;
                    $isbnData = null;
                    $sinopsis = null;
                    $author = null;

                    if ($isbn) {
                        $cleanIsbn = str_replace('-', '', $isbn);
                        $isbnData = $isbnMap->get($cleanIsbn);

                        if (!$isbnData) {
                            throw new \Exception("ISBN {$isbn} tidak ditemukan pada database ISBN.");
                        }

                        $isbnPenerbitId = $isbnData->penerbit_id
                            ?? $isbnData->PENERBIT_ID
                            ?? $isbnData->publisher_id
                            ?? null;

                        if (!$isbnPenerbitId) {
                            throw new \Exception("Data penerbit pada ISBN {$isbn} tidak ditemukan.");
                        }

                        if ((int)$isbnPenerbitId !== (int)$penerbitId) {
                            throw new \Exception("ISBN {$isbn} tidak terdaftar pada pelaksana serah yang dipilih.");
                        }

                        $catalogId = $isbnData->catalog_id
                            ?? $isbnData->CATALOG_ID
                            ?? $isbnData->catalogId
                            ?? null;

                        $penerbitTerbitanId = $isbnData->penerbit_terbitan_id
                            ?? $isbnData->PENERBIT_TERBITAN_ID
                            ?? $isbnData->penerbitTerbitanId
                            ?? null;
                        $sinopsis = $isbnData->sinopsis ?? null;
                        $author = $isbnData-> author ?? null;
                    } 

                    $qtyShouldAccept = (($this->user['branch_id'] ?? null) === '37') ? 2 : 1;
                    $qtyAccept = min($qtyDelivery, $qtyShouldAccept);
                    $qtyReject = max($qtyDelivery - $qtyShouldAccept, 0);

                    $detail = QueryAPI::create('e_receipt_upload_detail', [
                        'receipt_upload_history_id' => $this->historyId,
                        'row_number_upload' => $rowNumber,
                        'isbn' => $isbn,
                        'title' => $title,
                        'qty_delivery' => $qtyDelivery,
                        'catalog_id' => $catalogId,
                        'penerbit_terbitan_id' => $penerbitTerbitanId,
                        'received_date' => $receivedDate,
                        'received_by' => $this->user['username'] ?? 'system',
                        'qty_should_accept' => $qtyShouldAccept,
                        'qty_accept' => $qtyAccept,
                        'qty_reject' => $qtyReject,
                        'status' => 'success',
                        'message' => $qtyReject > 0
                            ? 'Data valid, namun terdapat kelebihan jumlah pengiriman.'
                            : 'Data valid.',
                    ], true);

                    $validDetails[] = [
                        'row_number_upload' => $rowNumber,
                        'isbn' => $isbn,
                        'issn' => $issn,
                        'title' => $title,
                        'sinopsis' => $sinopsis,
                        'author' => $author,
                        'province_id' => $history->PROVINCE_ID,
                        'kab_id' => $history->CITY_ID,
                        'publish_year' => $tahun_terbit,
                        'isbn_status' => $isbnData ? 'berISBN' : 'tidak berISBN',
                        'penerbit_isbn_id' => $isbnData->id ?? null,
                        'jenis' => $jenis,
                        'volume' => $volume,
                        'ttes_awal' => $ttesAwal,
                        'ttes_akhir' => $ttesAkhir,
                        'price' => $nilaiAset,
                        'ttes_awal' => $ttesAwal,
                        'ttes_akhir' => $ttesAkhir,
                        'received_date' => $receivedDate,
                        'received_by' => $this->user['username'] ?? 'system',
                        'qty_delivery' => $qtyDelivery,
                        'qty_should_accept' => $qtyShouldAccept,
                        'qty_accept' => $qtyAccept,
                        'qty_reject' => $qtyReject,
                        'catalog_id' => $catalogId,
                        'penerbit_terbitan_id' => $penerbitTerbitanId,
                        'history_detail_id' => $detail->ID ?? null,
                        'collection_type_id' => $collectionTypeId,
                        'publisher' => $history->NAME ?? null,
                    ];

                    $successRows++;

                    Redis::setex($rowKey, 86400, json_encode([
                        'row_number_upload' => $rowNumber,
                        'status' => 'success',
                        'isbn' => $isbn,
                        'issn' => $issn,
                        'title' => $title,
                        'qty_delivery' => $qtyDelivery,
                        'qty_accept' => $qtyAccept,
                        'qty_reject' => $qtyReject,
                        'message' => $qtyReject > 0
                            ? 'Data valid, namun terdapat kelebihan jumlah pengiriman.'
                            : 'Data valid.',
                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]));
                } catch (\Throwable $e) {
                    $failedRows++;

                    QueryAPI::create('e_receipt_upload_detail', [
                        'receipt_upload_history_id' => $this->historyId,
                        'row_number_upload' => $rowNumber,
                        'isbn' => $isbn,
                        'title' => $title,
                        'qty_delivery' => $qtyDelivery,
                        'received_date' => $receivedDate ?? Carbon::now()->format('Y-m-d'),
                        'received_by' => $this->user['username'] ?? 'system',
                        'qty_should_accept' => 0,
                        'qty_accept' => 0,
                        'qty_reject' => $qtyDelivery,
                        'status' => 'failed',
                        'message' => $e->getMessage(),
                    ]);

                    Redis::setex($rowKey, 86400, json_encode([
                        'row_number_upload' => $rowNumber,
                        'status' => 'failed',
                        'isbn' => $isbn,
                        'issn' => $issn,
                        'title' => $title,
                        'qty_delivery' => $qtyDelivery,
                        'qty_accept' => 0,
                        'qty_reject' => $qtyDelivery,
                        'message' => $e->getMessage(),
                        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    ]));
                }

                $processedRows++;

                Redis::setex($summaryKey, 86400, json_encode([
                    'status' => 'processing',
                    'total_rows' => $totalRows,
                    'processed_rows' => $processedRows,
                    'success_rows' => $successRows,
                    'failed_rows' => $failedRows,
                    'notes' => "Memproses baris {$processedRows} dari {$totalRows}.",
                ]));

                QueryAPI::update('e_receipt_upload_history', [
                    'processed_rows' => $processedRows,
                    'success_rows' => $successRows,
                    'failed_rows' => $failedRows,
                    'notes' => "Memproses baris {$processedRows} dari {$totalRows}.",
                ], "id = {$this->historyId}");
            }

            $letterId = null;

            if ($successRows > 0) {
                $letterId = $this->createLetterAndDetail($history, $validDetails);
            }

            if ($successRows > 0 && $failedRows > 0) {
                $finalStatus = 'partial_success';
                $notes = 'Upload selesai. Sebagian data berhasil diproses.';
            } elseif ($successRows > 0 && $failedRows == 0) {
                $finalStatus = 'success';
                $notes = 'Upload selesai. Semua data berhasil diproses.';
            } else {
                $finalStatus = 'failed';
                $notes = 'Upload selesai, tetapi seluruh data gagal diproses.';
            }

            QueryAPI::update('e_receipt_upload_history', [
                'status' => $finalStatus,
                'letter_id' => $letterId,
                'processed_rows' => $processedRows,
                'success_rows' => $successRows,
                'failed_rows' => $failedRows,
                'notes' => $notes,
                'finished_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ], "id = {$this->historyId}");

            Redis::setex($summaryKey, 86400, json_encode([
                'status' => $finalStatus,
                'total_rows' => $totalRows,
                'processed_rows' => $processedRows,
                'success_rows' => $successRows,
                'failed_rows' => $failedRows,
                'notes' => $notes,
                'letter_id' => $letterId,
            ]));
        } catch (\Throwable $e) {
            Log::error('ProcessReceiptUploadJob failed', [
                'history_id' => $this->historyId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            QueryAPI::update('e_receipt_upload_history', [
                'status' => 'failed',
                'notes' => $e->getMessage(),
                'finished_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ], "id = {$this->historyId}");

            Redis::setex($summaryKey, 86400, json_encode([
                'status' => 'failed',
                'total_rows' => 0,
                'processed_rows' => 0,
                'success_rows' => 0,
                'failed_rows' => 0,
                'notes' => $e->getMessage(),
            ]));
        }
    }

    private function createLetterAndDetail($history, array $validDetails)
    {
        $totalReject = array_sum(array_column($validDetails, 'qty_reject'));
        $totalAccept = array_sum(array_column($validDetails, 'qty_accept'));

        if ($totalReject > 0 && $totalAccept > 0) {
            $letterStatus = 'DITERIMA PARSIAL';
        } elseif ($totalReject == 0 && $totalAccept > 0) {
            $letterStatus = 'DITERIMA PENUH';
        } else {
            $letterStatus = 'DITOLAK';
        }

        $letter = QueryAPI::create('letter', [
            'penerbit_id' => $history->PENERBIT_ID,
            'branch_id' => $this->user['branch_id'],
            'receipt_no' => $history->RECEIPT_NO,
            'type_of_delivery' => $history->TYPE_OF_DELIVERY ?? 'EKSPEDISI',
            'jasa_pengiriman_id' => $history->JASA_PENGIRIMAN_ID ?? null,
            'letter_date' => Carbon::now()->format('Y-m-d H:i:s'),
            'accept_date' => Carbon::now()->format('Y-m-d H:i:s'),
            'status' => $letterStatus,
            'note' => $history->NOTES ?? null,
            'created_by' => $this->user['username'] ?? 'system',
        ], true);

        if (!$letter || !isset($letter->ID)) {
            throw new \Exception("Gagal membuat data letter.");
        }

        foreach ($validDetails as $item) {
            $letterDetail = QueryAPI::create('letter_detail', [
                'letter_id' => $letter->ID,
                'catalog_id' => $item['catalog_id'],
                'penerbit_terbitan_id' => $item['penerbit_terbitan_id'],
                'isbn' => $item['isbn'],
                'title' => $item['title'],
                'quantity' => $item['qty_delivery'],
                'qty_accept' => $item['qty_accept'],
                'qty_reject' => $item['qty_reject'],
                'price' => $item['price'],
                'ttes_awal' => $item['ttes_awal'],
                'ttes_akhir' => $item['ttes_akhir'],
                'collection_type_id' => $item['collection_type_id'],
                'edisi_serial' => $item['volume'],
                'author' => $item['author'],
                'sinopsis' => $item['sinopsis'],
                'publish_year' => $item['publish_year'],
                'publisher' => $item['publisher'],
                'penerbit_id' => $item['penerbit_id'],
                'checked' => 1,
                'copy' => 1,
                'penerbit_isbn_id' => $item['penerbit_isbn_id'],                
                'remark' => $item['qty_reject'] > 0
                    ? 'Terdapat kelebihan jumlah pengiriman.'
                    : 'Diterima melalui upload resi.',
                'received_by' => $this->user['username'] ?? 'system',
                'received_date' => $item['received_date']
            ]);
            if ($item['isbn'] ?: null && (int) $item->qty_accept > 0) {
                QueryAPI::setReceiveDate([
                        'LetterDetailId' => $letterDetail->ID,
                        'NomorISBN' => $item['isbn'],
                        'IsPerpusnas' => (int)$this->user['branch_id'] == 37 ? 1 : 0,
                        'IsProvinsi' => (int)$this->user['branch_id'] != 37 ? 1 : 0,
                        'TanggalTerima' => $item['received_date'],
                ]);
            }
        }

        return $letter->ID;
    }
    protected function convertExcelDate($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
        }

        $value = trim((string) $value);

        $formats = ['d-m-Y', 'd/m/Y', 'Y-m-d'];

        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $value);
            if ($date && $date->format($format) === $value) {
                return $date->format('Y-m-d');
            }
        }

        return $value;
    }
}