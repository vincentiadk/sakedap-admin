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
                SELECT *
                FROM e_receipt_upload_history
                WHERE id = {$this->historyId}
            ", true);

            if (!$history) {
                throw new \Exception("History upload resi tidak ditemukan.");
            }

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

            /**
             * Header Excel:
             * A: ISBN
             * B: Judul
             * C: Jenis Koleksi
             * D: Tanggal Terima
             * E: Jumlah Pengiriman
             * F: Harga koleksi
             */
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

            $processedRows = 0;
            $successRows = 0;
            $failedRows = 0;
            $validDetails = [];

            foreach ($dataRows as $index => $row) {
                $rowNumber = $index + 2;

                $isbn = trim((string)($row[0] ?? ''));
                $title = trim((string)($row[1] ?? ''));
                $receivedDateRaw = $row[3] ?? null;
                $qtyDelivery = (int)($row[4] ?? 0);

                $receivedDate = $this->convertExcelDate($receivedDateRaw);
                // kalau kosong / gagal parse → pakai hari ini
                if (!$receivedDate) {
                    $receivedDate = Carbon::now()->format('Y-m-d');
                }
                $rowKey = "receipt_upload:{$this->historyId}:row:{$rowNumber}";

                Redis::setex($rowKey, 86400, json_encode([
                    'row_number_upload' => $rowNumber,
                    'status' => 'running',
                    'isbn' => $isbn,
                    'title' => $title,
                    'qty_delivery' => $qtyDelivery,
                    'message' => 'Sedang diproses.',
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
                ]));

                try {
                    if (!$isbn) {
                        throw new \Exception("ISBN kosong.");
                    }

                    if (!$title) {
                        throw new \Exception("Judul kosong.");
                    }

                    if ($qtyDelivery <= 0) {
                        throw new \Exception("Jumlah pengiriman harus lebih dari 0.");
                    }

                    $catalog = ISBN::get("
                        SELECT *
                        FROM catalog
                        WHERE REPLACE(isbn, '-', '') = REPLACE('{$isbn}', '-', '')
                           OR REPLACE(isbn13, '-', '') = REPLACE('{$isbn}', '-', '')
                    ", true);

                    if (!$catalog) {
                        throw new \Exception("ISBN tidak ditemukan pada katalog.");
                    }

                    /**
                     * Hitung jumlah seharusnya diterima.
                     * Untuk buku umumnya 2 eksemplar.
                     * Kalau nanti ada aturan khusus, bagian ini yang tinggal disesuaikan.
                     */
                    $qtyShouldAccept = 2;

                    $qtyAccept = min($qtyDelivery, $qtyShouldAccept);
                    $qtyReject = max($qtyDelivery - $qtyShouldAccept, 0);

                    $detail = QueryAPI::create('e_receipt_upload_history_detail', [
                        'receipt_upload_history_id' => $this->historyId,
                        'row_number_upload' => $rowNumber,
                        'isbn' => $isbn,
                        'title' => $title,
                        'qty_delivery' => $qtyDelivery,
                        'catalog_id' => $catalog->ID ?? null,
                        'penerbit_terbitan_id' => $catalog->PENERBIT_TERBITAN_ID ?? null,
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
                        'title' => $title,
                        'qty_delivery' => $qtyDelivery,
                        'qty_should_accept' => $qtyShouldAccept,
                        'qty_accept' => $qtyAccept,
                        'qty_reject' => $qtyReject,
                        'catalog_id' => $catalog->ID ?? null,
                        'penerbit_terbitan_id' => $catalog->PENERBIT_TERBITAN_ID ?? null,
                        'history_detail_id' => $detail->ID ?? null,
                    ];

                    $successRows++;

                    Redis::setex($rowKey, 86400, json_encode([
                        'row_number_upload' => $rowNumber,
                        'status' => 'success',
                        'isbn' => $isbn,
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

                    QueryAPI::create('e_receipt_upload_history_detail', [
                        'receipt_upload_history_id' => $this->historyId,
                        'row_number_upload' => $rowNumber,
                        'isbn' => $isbn,
                        'title' => $title,
                        'qty_delivery' => $qtyDelivery,
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
            $letterStatus = 'DITERIMA_PARSIAL';
        } elseif ($totalReject == 0 && $totalAccept > 0) {
            $letterStatus = 'DITERIMA_PENUH';
        } else {
            $letterStatus = 'DITOLAK';
        }

        $letter = QueryAPI::create('letter', [
            'penerbit_id' => $history->PENERBIT_ID,
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
            QueryAPI::create('letter_detail', [
                'letter_id' => $letter->ID,
                'catalog_id' => $item['catalog_id'],
                'penerbit_terbitan_id' => $item['penerbit_terbitan_id'],
                'isbn' => $item['isbn'],
                'title' => $item['title'],
                'quantity' => $item['qty_delivery'],
                'qty_accept' => $item['qty_accept'],
                'qty_reject' => $item['qty_reject'],
                'remark' => $item['qty_reject'] > 0
                    ? 'Terdapat kelebihan jumlah pengiriman.'
                    : 'Diterima melalui upload resi.',
                'created_by' => $this->user['username'] ?? 'system',
            ]);
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