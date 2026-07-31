<?php

namespace App\Jobs;

use ZipArchive;
use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Redis;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;


class ProcessZipJournalJobV2 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $historyId;
    public $user;
    public $tries = 1;
    public $timeout = 600;

    public function __construct($historyId, $user)
    {
        $this->historyId = $historyId;
        $this->user = $user;
    }

    public function handle()
    {
        try {
            $this->setSummaryProgress([
                'status' => 'processing',
                'notes' => 'Sedang memproses file ZIP',
                'started_at' => date('Y-m-d H:i:s'),
                'processed_rows' => 0,
                'success_rows' => 0,
                'failed_rows' => 0,
            ]);

            $history = QueryAPI::get("
                SELECT *
                FROM e_zip_upload_history
                WHERE id = {$this->historyId}
            ", true);

            if (!$history) {
                throw new \Exception('Histori upload tidak ditemukan');
            }

            $penerbit = QueryAPI::get("
                SELECT *
                FROM penerbit
                WHERE id = {$history->PENERBIT_ID}
                ", true);
            if (!$penerbit) {
                throw new \Exception('Penerbit tidak ditemukan');
            }
            $zipPath = $history->ZIP_PATH;

            if (!file_exists($zipPath)) {
                throw new \Exception('File ZIP tidak ditemukan di server');
            }

            $extractPath = storage_path('app/tmp_zip_extract/' . $this->historyId);

            if (!is_dir($extractPath)) {
                mkdir($extractPath, 0777, true);
            }

            $zip = new ZipArchive();
            if ($zip->open($zipPath) !== true) {
                throw new \Exception('ZIP gagal dibuka');
            }

            $zip->extractTo($extractPath);
            $zip->close();

            $excelFile = $this->findExcelFile($extractPath);
            if (!$excelFile) {
                throw new \Exception('File Excel tidak ditemukan di dalam ZIP');
            }

            $rows = Excel::toArray([], $excelFile)[0] ?? [];
            $rows = array_values(array_filter($rows, function ($row) {
                foreach ($row as $cell) {
                    if (!is_null($cell) && trim((string) $cell) !== '') {
                        return true;
                    }
                }
                return false;
            }));
            if (count($rows) < 2) {
                throw new \Exception('Data Excel kosong');
            }

            $header = array_map(function ($item) {
                return $this->normalizeHeader($item);
            }, $rows[0]);

            unset($rows[0]);
            $rows = array_values($rows);

            $this->setSummaryProgress([
                'total_rows' => count($rows),
                'notes' => 'Excel ditemukan, mulai proses data',
            ]);

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;
                $title = null;
                $fileName = null;

                try {
                    $item = $this->combineRow($header, $row);

                    if (empty($item['nama_file_pdf'])) {
                        throw new \Exception('Nama File (.pdf) kosong');
                    }

                    if (empty($item['judul_artikel'])) {
                        throw new \Exception('Judul Artikel kosong');
                    }

                    if (empty($item['judul_jurnal'])) {
                        throw new \Exception('Judul Jurnal kosong');
                    }

                    if (empty($item['provinsi'])) {
                        throw new \Exception('Provinsi kosong');
                    }

                    $title = $item['judul_artikel'];
                    $fileName = $item['nama_file_pdf'];

                    $this->setRowProgressToRedis($rowNumber, [
                        'status' => 'running',
                        'title' => $title,
                        'file_name' => $fileName,
                        'message' => 'Sedang memproses row',
                    ]);

                    $pdfPath = $this->findPdfFile($extractPath, $fileName);
                    if (!$pdfPath) {
                        throw new \Exception("File PDF {$fileName} tidak ditemukan");
                    }

                    // ========================================
                    // PRIORITY 1 FIX: Upload file DULU
                    // ========================================

                    $file = new UploadedFile(
                        $pdfPath,
                        basename($pdfPath),
                        File::mimeType($pdfPath),
                        null,
                        true
                    );

                    $hash = md5_file($pdfPath);
                    if (!$hash) {
                        throw new \Exception('Gagal membuat hash file');
                    }

                    // Sanitize hash: trim whitespace, convert to lowercase for consistency
                    $hash = trim(strtolower($hash));

                    // Cek duplikasi di CATALOGFILES
                    try {
                        $existingFile = QueryAPI::get("
                            SELECT * FROM CATALOGFILES
                            WHERE LOWER(TRIM(hash)) = '" . ($hash) . "'
                        ", true);

                        if ($existingFile) {
                            throw new \Exception('File yang sama sudah pernah diupload.');
                        }
                    } catch (\Throwable $hashCheckError) {
                        // Log detail error saat cek hash
                        Log::channel('zip-upload')->error('Hash duplicate check error', [
                            'history_id' => $this->historyId,
                            'row_number' => $rowNumber,
                            'file_name' => $fileName,
                            'hash' => $hash,
                            'hash_length' => strlen($hash),
                            'error' => $hashCheckError->getMessage(),
                        ]);
                        throw $hashCheckError;
                    }

                    // UPLOAD FILE LEBIH DULU (sebelum create collection)
                    $this->setRowProgressToRedis($rowNumber, [
                        'status' => 'running',
                        'title' => $title,
                        'file_name' => $fileName,
                        'message' => 'Sedang upload file PDF',
                    ]);

                    $tempCollectionId = null;

                    // Upload file dengan hash yang sudah di-sanitize
                    $uploadResult = QueryAPI::uploadFile([
                        'type' => 'konten_digital',
                        'id' => $tempCollectionId,
                        'status' => 1,  // e_collections (status = 1)
                        'hash' => $hash,  // Already trimmed & lowercased
                        'mime' => $file->getMimeType(),
                        'filesize' => $file->getSize(),
                        'method' => 7,
                        'iszip' => false,
                        'file' => $file,
                    ]);

                    if (!$uploadResult) {
                        throw new \Exception('Upload file PDF gagal');
                    }

                    $filePath = null;
                    if (is_object($uploadResult)) {
                        $filePath = $uploadResult->path
                            ?? $uploadResult->filepath
                            ?? $uploadResult->file_path
                            ?? $uploadResult->fullpath
                            ?? null;
                    }

                    // Validasi penerbit sebelum create collection
                    if (!$penerbit || !$penerbit->ID) {
                        throw new \Exception('Data penerbit tidak ditemukan atau tidak valid');
                    }

                    if (empty($history->PENERBIT_ID)) {
                        throw new \Exception('Penerbit ID dari history upload kosong');
                    }

                    // File upload sukses, baru create collection di database
                    $payload = $this->mapExcelRowToEcollectionsPayload($item, $history, $penerbit);

                    // Log penerbit_id yang akan digunakan
                    Log::channel('zip-upload')->info('Creating collection with penerbit', [
                        'history_id' => $this->historyId,
                        'row_number' => $rowNumber,
                        'penerbit_id' => $history->PENERBIT_ID,
                        'penerbit_name' => $penerbit->NAME ?? null,
                    ]);

                    $this->setRowProgressToRedis($rowNumber, [
                        'status' => 'running',
                        'title' => $title,
                        'file_name' => $fileName,
                        'message' => 'Sedang menyimpan koleksi ke database',
                    ]);

                    try {
                        $createdCollection = QueryAPI::create('e_collections', $payload);
                    } catch (\Throwable $e) {
                        // Log detail error dari API
                        Log::channel('zip-upload')->error('QueryAPI::create e_collections failed', [
                            'history_id' => $this->historyId,
                            'row_number' => $rowNumber,
                            'error' => $e->getMessage(),
                            'error_code' => $e->getCode(),
                            'payload' => $payload,
                        ]);
                        throw new \Exception('Gagal menyimpan ke tabel e_collections: ' . $e->getMessage());
                    }

                    if (!$createdCollection || !isset($createdCollection->ID)) {
                        throw new \Exception('Gagal menyimpan ke tabel e_collections: Response tidak valid');
                    }

                    // ========================================
                    // Metadata sekarang aman, file sudah upload
                    // ========================================

                    $finalRowData = [
                        'status' => 'success',
                        'title' => $title,
                        'file_name' => $fileName,
                        'message' => 'Berhasil upload dan simpan koleksi',
                        'e_collection_id' => $createdCollection->ID,
                    ];

                    $this->setRowProgressToRedis($rowNumber, $finalRowData);
                    $this->saveFinalDetailToDatabase($rowNumber, $finalRowData);

                    $currentData = $this->incrementSummaryProgress(
                        [
                            'processed_rows' => 1,
                            'success_rows' => 1,
                        ],
                        [
                            'notes' => "Row {$rowNumber} berhasil diproses",
                            'current_row' => $rowNumber,
                            'last_status' => 'success',
                        ]
                    );

                    if (($rowNumber - 1) % 10 === 0) {
                        $this->flushSummaryToDatabase();
                    }

                    // Verifikasi collection
                    try {
                        $verifikasi = QueryAPI::verificationCollection($createdCollection->ID, $this->user['username']);
                        if (!$verifikasi) {
                            throw new \Exception('Gagal verifikasi artikel menjadi katalog - API return false');
                        }
                    } catch (\Throwable $e) {
                        Log::channel('zip-upload')->error('Verification collection failed', [
                            'history_id' => $this->historyId,
                            'row_number' => $rowNumber,
                            'collection_id' => $createdCollection->ID,
                            'title' => $title,
                            'file_name' => $fileName,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
                        throw $e;
                    }

                } catch (\Throwable $e) {
                    if (($rowNumber - 1) % 10 === 0) {
                        $this->flushSummaryToDatabase();
                    }

                    $finalRowData = [
                        'status' => 'failed',
                        'title' => $title,
                        'file_name' => $fileName,
                        'message' => $e->getMessage(),
                    ];

                    $this->setRowProgressToRedis($rowNumber, $finalRowData);
                    $this->saveFinalDetailToDatabase($rowNumber, $finalRowData);

                    $currentData = $this->incrementSummaryProgress(
                        [
                            'processed_rows' => 1,
                            'failed_rows' => 1,
                        ],
                        [
                            'notes' => "Row {$rowNumber} gagal: {$e->getMessage()}",
                            'current_row' => $rowNumber,
                            'last_status' => 'failed',
                        ]
                    );

                    Log::channel('zip-upload')->error('ZIP upload row failed', [
                        'history_id' => $this->historyId,
                        'row_number' => $rowNumber,
                        'title' => $title,
                        'file_name' => $fileName,
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }

            $lastSummary = json_decode(Redis::get("zip_upload:{$history->ID}:summary"), true);

            $data = [
                'status' => (int) $lastSummary['failed_rows'] > 0 ? 'done_with_error' : 'done',
                'notes' => 'Proses upload selesai',
                'finished_at' => date('Y-m-d H:i:s'),
                'failed_rows' => $lastSummary['failed_rows'] ?? 0,
                'success_rows' => $lastSummary['success_rows'] ?? 0,
                'processed_rows' => $lastSummary['processed_rows'] ?? 0,
                'total_rows' =>  $lastSummary['total_rows'] ?? 0
            ];
            $this->setSummaryProgress($data);

            QueryAPI::update('e_zip_upload_history',
                $this->historyId,
                $data,
                false
            );

            $this->deleteDirectory($extractPath);
        } catch (\Throwable $e) {
            $this->setSummaryProgress([
                'status' => 'failed',
                'notes' => $e->getMessage(),
                'finished_at' => date('Y-m-d H:i:s'),
            ]);

            Log::channel('zip-upload')->error('ZIP upload failed - V2', [
                'history_id' => $this->historyId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
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

    protected function mapExcelRowToEcollectionsPayload($item, $history, $penerbit, $filePath = null)
    {
        $rawDate = $item['tanggal_terbit_dd_mm_yyyy']
                ?? $item['tanggal_terbit']
                ?? null;
        $editionDate = $this->convertExcelDate($rawDate);
        $receivedDate = $this->convertExcelDate($item['tanggal_aset_dd_mm_yyyy'] ?? date('Y-m-d H:i:s'));
        $publicationYear = $this->extractYear($editionDate)  ?? $this->extractYear($receivedDate);

        return [
            'penerbit_id' => $history->PENERBIT_ID,
            'article_doi' => $item['doi'] ?? null,
            'article_original_link' => $item['link_artikel'] ?? null,
            'publication_year' => $publicationYear,
            'kabupaten_id' => $penerbit->CITY_ID ?? null,
            'city_id' => $penerbit->CITY_ID ?? null,
            'deposit' => Main::generateNumberDeposit(),
            'preview' => '1-2',
            'akses' => 1,
            'jenis_isi' => 'teks',
            'jenis_wadah' => 'komputer',
            'jenis_media' => 'sumber daya sambung jaring',
            'received_at' => $receivedDate,
            'received_by' => $this->user['id'],
            'received_by_name' => $this->user['username'],
            'collection_media_id' => 203,
            'worksheet_id' => 142,
            'title' => $item['judul_jurnal'] ?? null,
            'slug' => Str::slug($item['judul_artikel'], '-'),
            'copyright' => Main::copyright($history->PENERBIT_ID),
            'garuda_journal_id' => $item['id_jurnal_garuda'] ?? null,
            'article_subject' => $item['subjek'] ?? null,
            'article_title' => $item['judul_artikel'] ?? null,
            'article_file_link' => $item['url_file'] ?? null,
            'article_contributor' => $item['kontributor'] ?? null,
            'article_abstract' => $item['sinopsis'] ?? null,
            'volume' => $item['volume'] ?? null,
            'edition_date' => $editionDate,
            'code' => $item['issn_eissn'] ?? null,
            'code_type' => $item['issn_eissn'] ? 3 : null,
            'status' => 2,
            'created_by' => $this->user['id']
        ];
    }

    protected function extractYear($date)
    {
        if (!$date) return null;

        try {
            return Carbon::parse($date)->year;
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function getHistory()
    {
        return QueryAPI::get("
            SELECT *
            FROM e_zip_upload_history
            WHERE id = {$this->historyId}
        ", true);
    }

    protected function getHistoryDetail($rowNumber)
    {
        return QueryAPI::get("
            SELECT *
            FROM e_zip_upload_history_detail
            WHERE zip_upload_history_id = {$this->historyId}
            AND row_number_upload = {$rowNumber}
        ", true);
    }

    protected function findExcelFile($dir)
    {
        $files = collect(File::allFiles($dir));

        $excel = $files->first(function ($file) {
            $ext = strtolower($file->getExtension());
            return in_array($ext, ['xls', 'xlsx']);
        });

        return $excel ? $excel->getRealPath() : null;
    }

    protected function findPdfFile($dir, $fileName)
    {
        $target = strtolower(trim($fileName));

        if (!str_ends_with($target, '.pdf')) {
            $target .= '.pdf';
        }

        $files = collect(File::allFiles($dir));

        $pdf = $files->first(function ($file) use ($target) {
            return strtolower($file->getFilename()) === $target;
        });

        return $pdf ? $pdf->getRealPath() : null;
    }

    protected function combineRow($header, $row)
    {
        $result = [];
        foreach ($header as $index => $column) {
            $result[$column] = $row[$index] ?? null;
        }
        return $result;
    }

    protected function deleteDirectory($dir)
    {
        if (is_dir($dir)) {
            File::deleteDirectory($dir);
        }
    }

    protected function normalizeHeader($text)
    {
        $text = strtolower(trim((string) $text));

        $replace = [
            ' '  => '_',
            '/'  => '_',
            '.'  => '',
            '('  => '',
            ')'  => '',
            '-'  => '_',
        ];

        $text = strtr($text, $replace);

        $text = preg_replace('/_+/', '_', $text);
        $text = trim($text, '_');

        return $text;
    }

    protected function redisRowKey($rowNumber)
    {
        return "zip_upload:{$this->historyId}:row:{$rowNumber}";
    }

    protected function redisSummaryKey()
    {
        return "zip_upload:{$this->historyId}:summary";
    }

    protected function setSummaryProgress(array $data)
    {
        $key = "zip_upload:{$this->historyId}:summary";
        $existing = Redis::get($key);
        $existing = $existing ? json_decode($existing, true) : [];

        $payload = array_merge($existing, $data, [
            'updated_at' => now()->format('Y-m-d H:i:s'),
        ]);

        Redis::set($key, json_encode($payload, JSON_UNESCAPED_UNICODE));
        Redis::expire($key, 86400);
    }

    protected function incrementSummaryProgress(array $increments = [], array $extra = [])
    {
        $key = $this->redisSummaryKey();

        $existing = Redis::get($key);
        $existing = $existing ? json_decode($existing, true) : [];

        $payload = array_merge([
            'status' => 'processing',
            'total_rows' => 0,
            'processed_rows' => 0,
            'success_rows' => 0,
            'failed_rows' => 0,
            'notes' => null,
            'started_at' => null,
            'finished_at' => null,
        ], $existing);

        foreach ($increments as $field => $value) {
            $payload[$field] = ((int) ($payload[$field] ?? 0)) + (int) $value;
        }

        $payload = array_merge($payload, $extra, [
            'updated_at' => now()->format('Y-m-d H:i:s'),
        ]);

        Redis::set($key, json_encode($payload, JSON_UNESCAPED_UNICODE));
        Redis::expire($key, 86400);

        return $payload;
    }

    protected function getSummaryProgress()
    {
        $key = $this->redisSummaryKey();

        $summary = Redis::get($key);

        return $summary ? json_decode($summary, true) : [];
    }

    protected function flushSummaryToDatabase(array $summary = [])
    {
        if (empty($summary)) {
            $summary = $this->getSummaryProgress();
        }

        if (empty($summary)) {
            return;
        }

        QueryAPI::update('e_zip_upload_history', $this->historyId, [
            'status' => $summary['status'] ?? null,
            'total_rows' => $summary['total_rows'] ?? 0,
            'processed_rows' => $summary['processed_rows'] ?? 0,
            'success_rows' => $summary['success_rows'] ?? 0,
            'failed_rows' => $summary['failed_rows'] ?? 0,
            'notes' => $summary['notes'] ?? null,
            'started_at' => $summary['started_at'] ?? null,
            'finished_at' => $summary['finished_at'] ?? null,
        ], false);
    }

    protected function setRowProgressToRedis($rowNumber, array $data)
    {
        $key = $this->redisRowKey($rowNumber);

        $existing = Redis::get($key);
        $existing = $existing ? json_decode($existing, true) : [];

        $payload = array_merge($existing, $data, [
            'row_number_upload' => $rowNumber,
            'updated_at' => now()->format('Y-m-d H:i:s'),
        ]);

        Redis::set($key, json_encode($payload, JSON_UNESCAPED_UNICODE));
        Redis::expire($key, 60 * 60 * 24);
    }

    protected function saveFinalDetailToDatabase($rowNumber, array $data)
    {
        $existingDetail = $this->getHistoryDetail($rowNumber);

        $payload = [
            'zip_upload_history_id' => $this->historyId,
            'row_number_upload' => $rowNumber,
            'title' => $data['title'] ?? null,
            'file_name' => $data['file_name'] ?? null,
            'status' => $data['status'] ?? null,
            'message' => $data['message'] ?? null,
            'e_collection_id' => $data['e_collection_id'] ?? null,
        ];

        if ($existingDetail && isset($existingDetail->ID)) {
            QueryAPI::update('e_zip_upload_history_detail', $existingDetail->ID, $payload);
            return $existingDetail->ID;
        }

        $detail = QueryAPI::create('e_zip_upload_history_detail', $payload);

        if (!$detail || !isset($detail->ID)) {
            throw new \Exception('Gagal menyimpan detail histori final');
        }

        return $detail->ID;
    }
}
