<?php

namespace App\Jobs;

use App\Helpers\QueryAPI;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class ProcessZipJournalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $historyId;

    public function __construct($historyId)
    {
        $this->historyId = $historyId;
    }

    public function handle()
    {
        try {
            QueryAPI::update('e_zip_upload_history', $this->historyId, [
                'status' => 'processing',
                'notes' => 'Sedang memproses file ZIP',
                'started_at' => date('Y-m-d H:i:s'),
            ], false);

            $history = QueryAPI::get("
                SELECT *
                FROM e_zip_upload_history
                WHERE id = {$this->historyId}
            ", true);

            if (!$history) {
                throw new \Exception('Histori upload tidak ditemukan');
            }

            $zipPath = storage_path('app/' . $history->ZIP_PATH);

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

            if (count($rows) < 2) {
                throw new \Exception('Data Excel kosong');
            }

            $header = array_map(function ($item) {
                return $this->normalizeHeader($item);
            }, $rows[0]);

            unset($rows[0]);
            $rows = array_values($rows);

            QueryAPI::update('e_zip_upload_history', $this->historyId, [
                'total_rows' => count($rows),
                'notes' => 'Excel ditemukan, mulai proses data',
            ]);

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;

                try {
                    $historyLatest = $this->getHistory();

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

                    if (empty($item['provinsi'])) {
                        throw new \Exception('Provinsi kosong');
                    }

                    $detail = QueryAPI::create('e_zip_upload_history_detail', [
                        'e_zip_upload_history_id' => $this->historyId,
                        'row_number_upload' => $rowNumber,
                        'title' => $title,
                        'file_name' => $fileName,
                        'status' => 'pending',
                        'message' => 'Sedang memproses row',
                    ]);

                    if (!$detail || !isset($detail->id)) {
                        throw new \Exception('Gagal membuat detail histori');
                    }

                    $pdfPath = $this->findPdfFile($extractPath, $fileName);
                    if (!$pdfPath) {
                        throw new \Exception("File PDF {$fileName} tidak ditemukan");
                    }

                    $uploadResult = QueryAPI::uploadFile([
                        'file' => $pdfPath,
                        'filename' => $fileName,
                        'folder' => 'e_collections',
                        'uploadtype' => 'document',
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
                    $payload = $this->mapExcelRowToEcollectionsPayload($item, $history);
                    $createdCollection = QueryAPI::create('e_collections', $payload);

                    if (!$createdCollection || !isset($createdCollection->id)) {
                        throw new \Exception('Gagal menyimpan ke tabel e_collections');
                    }

                    QueryAPI::update('e_zip_upload_history_detail', $detail->id, [
                        'status' => 'success',
                        'message' => 'Berhasil upload dan simpan koleksi',
                        'e_collection_id' => $createdCollection->id,
                    ]);

                    QueryAPI::update('e_zip_upload_history', $this->historyId, [
                        'processed_rows' => ((int) $historyLatest->PROCESSED_ROWS) + 1,
                        'success_rows' => ((int) $historyLatest->SUCCESS_ROWS) + 1,
                        'notes' => "Row {$rowNumber} berhasil diproses",
                    ], false);

                    QueryAPI::verificationCollection($createdCollection->id);
                } catch (\Throwable $e) {
                    $historyLatest = $this->getHistory();

                    QueryAPI::create('e_zip_upload_history_detail', [
                        'e_zip_upload_history_id' => $this->historyId,
                        'row_number_upload' => $rowNumber,
                        'title' => $row[0] ?? null,
                        'file_name' => $row[1] ?? null,
                        'status' => 'failed',
                        'message' => $e->getMessage(),
                    ]);

                    QueryAPI::update('e_zip_upload_history', $this->historyId, [
                        'processed_rows' => ((int) $historyLatest->PROCESSED_ROWS) + 1,
                        'failed_rows' => ((int) $historyLatest->FAILED_ROWS) + 1,
                        'notes' => "Row {$rowNumber} gagal: {$e->getMessage()}",
                    ], false);

                    Log::error('ZIP upload row failed', [
                        'history_id' => $this->historyId,
                        'row_number' => $rowNumber,
                        'message' => $e->getMessage(),
                    ]);
                }
            }

            $finalHistory = $this->getHistory();

            QueryAPI::update('e_zip_upload_history', $this->historyId, [
                'status' => ((int) $finalHistory->FAILED_ROWS > 0) ? 'done_with_error' : 'done',
                'notes' => 'Proses upload selesai',
                'finished_at' => date('Y-m-d H:i:s'),
            ], false);

            $this->deleteDirectory($extractPath);
        } catch (\Throwable $e) {
            QueryAPI::update('e_zip_upload_history', $this->historyId, [
                'status' => 'failed',
                'notes' => $e->getMessage(),
                'finished_at' => date('Y-m-d H:i:s'),
            ], false);

            Log::error('ZIP upload failed', [
                'history_id' => $this->historyId,
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function convertExcelDate($value)
    {
        if (empty($value)) {
            return null;
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
    protected function mapExcelRowToEcollectionsPayload($item, $history, $filePath = null)
    {
        return [
            'penerbit_id' => $history->PENERBIT_ID,
            //'no_urut' => $item['no'] ?? null,
            //'url_file' => $item['url_file'] ?? null,
            'article_doi' => $item['doi'] ?? null,
            'article_original_link' => $item['link_artikel'] ?? null,
            //'file_name' => $item['nama_file_pdf'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'received_at' => $this->convertExcelDate($item['tanggal_aset_dd_mm_yyyy'] ??  date('Y-m-d H:i:s')),
            'article_title' => $item['judul_artikel'] ?? null,
            'title' => $item['judul_jurnal'] ?? null,
            'garuda_journal_id' => $item['id_jurnal_garuda'] ?? null,
            //'publisher' => $item['penerbit'] ?? null,
            //'province' => $item['provinsi'] ?? null,
            'article_subject' => $item['subjek'] ?? null,
            'volume' => $item['volume'] ?? null,
            'article_contributor' => $item['kontributor'] ?? null,
            'description' => $item['sinopsis'] ?? null,
            'edition_date' => $this->convertExcelDate($item['tanggal_terbit'] ?? null),
            'code' => $item['issn_eissn'] ?? null,
            'code_type' => $item['issn_eiisn'] ? 3 : null,
            'status' => 2,
            'create_by' => $history->CREATE_BY
        ];
    }
    protected function getHistory()
    {
        return QueryAPI::get("
            SELECT *
            FROM e_zip_upload_history
            WHERE id = {$this->historyId}
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
}