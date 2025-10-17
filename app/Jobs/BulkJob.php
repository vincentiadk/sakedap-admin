<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class BulkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * data
     *
     * @var mixed
     */
    protected $data;

    /**
     * request
     *
     * @var mixed
     */
    protected $request;

    /**
     * Create a new job instance.
     */
    public function __construct($data, $request)
    {
        $this->data = $data;
        $this->request = $request;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = $this->data;
        $request = $this->request;
        $bulkId = data_get($request, 'bulk_id');
        $path = data_get($request, 'path');

        try {
            QueryAPI::update('e_bulks', $bulkId, [
                'process_start_at' => now()->toDateTimeString(),
                'status_progress' => 'PROSES'
            ]);

            if (empty($data)) {
                QueryAPI::update('e_bulks', $bulkId, [
                    'process_finish_at' => now()->toDateTimeString(),
                    'status_progress' => 'SELESAI'
                ]);

                return;
            }

            $userId = data_get($request, 'user_id');
            $paramId = data_get($request, 'id');
            $paramType = data_get($request, 'type');

            $executor = null;
            $catalog = null;
            $catalogSql = '';
            $worksheetId = null;

            if ($paramType === 'bulk_non_serial') {
                $worksheetId = 20;
                $executor = QueryAPI::get("select * from penerbit where id = {$paramId}", true);
            } else if ($paramType === 'bulk_serial') {
                $worksheetId = 142;
                $catalogSql = "
                    select
                        catalogs.*,
                        e_collections.serial as serial_e_collections,
                        e_collections.code_type as code_type_e_collections
                    from
                        catalogs
                    left join
                        e_collections on e_collections.id = catalogs.edeposit_col_id
                    where
                        catalogs.id = {$paramId}
                ";

                $catalog = QueryAPI::get($catalogSql, true);
            }

            foreach (collect($data)->chunk(10) as $chunk) {
                foreach ($chunk as $d) {
                    $fileCover = $d->get('file_cover');
                    $fileContent = $d->get('file_konten');
                    $edition = $d->get('edisi');
                    $editionDate = $d->get('tanggal_edisi');
                    $title = $d->get('judul');
                    $access = $d->get('akses', 0);
                    $sinopsis = $d->get('sinopsis');
                    $preview = $d->get('preview');
                    $codeType = $d->get('jenis_kode');
                    $code = $d->get('kode');
                    $series = $d->get('seri');
                    $serial = $d->get('kala_terbit');
                    $publishTime = $d->get('waktu_terbit');
                    $currency = $d->get('mata_uang');
                    $price = $d->get('harga', 0);
                    $binding = $d->get('jilid', 0);
                    $contributor = $d->get('kontributor');

                    if (is_numeric($publishTime) && $publishTime > 1) {
                        try {
                            $dateObject = Carbon::instance(Date::excelToDateTimeObject($publishTime));
                            $finalpublishTime = $dateObject->toDateTimeString();
                        } catch (\Exception $e) {
                            $finalpublishTime = null;
                        }
                    } else if (!empty($publishTime)) {
                        try {
                            $dateObject = Carbon::parse($publishTime);
                            $finalpublishTime = $dateObject->toDateTimeString();
                        } catch (\Exception $e) {
                            $finalpublishTime = null;
                        }
                    }

                    $parentId = optional($catalog)->EDEPOSIT_COL_ID ?? 0;
                    $executorId = optional($catalog)->PENERBIT_ID ?? optional($executor)->ID ?? 0;
                    $cityId = optional($catalog)->CITY_ID ?? 0;
                    $copyrightId = optional($catalog)->PENERBIT_ID ?? optional($executor)->ID ?? 0;
                    $titleOri = optional($catalog)->TITLE ?? $title ?? '';
                    $album = optional($catalog)->ALBUM ?? '';
                    $seriesData = optional($catalog)->SERIES ?? $series ?? '';
                    $serialData = optional($catalog)->SERIAL_E_COLLECTION ?? $serial ?? '';
                    $codeData = optional($catalog)->ISBN ?? $code ?? '';
                    $codeTypeData = optional($catalog)->CODE_TYPE_E_COLLECTION ?? $codeType ?? '';
                    $publishMonth = optional($catalog)->PUBLISH_MONTH ?: ($finalpublishTime ? date('m', strtotime($finalpublishTime)) : null);
                    $publishYear = optional($catalog)->PUBLISHYEAR ?: ($finalpublishTime ? date('Y', strtotime($finalpublishTime)) : null);

                    $physicalDescription = [
                        'paging' => $d->get('paging'),
                        'ill' => $d->get('ilustrasi'),
                        'sizes' => $d->get('sizes'),
                    ];

                    if (is_numeric($editionDate) && $editionDate > 1) {
                        try {
                            $dateObject = Carbon::instance(Date::excelToDateTimeObject($editionDate));
                            $finalEditionDate = $dateObject->toDateTimeString();
                        } catch (\Exception $e) {
                            $finalEditionDate = null;
                        }
                    } else if (!empty($editionDate)) {
                        try {
                            $dateObject = Carbon::parse($editionDate);
                            $finalEditionDate = $dateObject->toDateTimeString();
                        } catch (\Exception $e) {
                            $finalEditionDate = null;
                        }
                    }

                    $baseCollectionData = [
                        'worksheet_id' => $worksheetId,
                        'parent_id' => $parentId,
                        'publisher_id' => $executorId,
                        'city_id' => $cityId,
                        'title_ori' => $titleOri,
                        'album' => $album,
                        'slug' => Str::slug($titleOri, '-'),
                        'series' => $seriesData,
                        'serial' => $serialData,
                        'code' => $codeData,
                        'code_type' => $codeTypeData,
                        'publication_month' => $publishMonth,
                        'publication_year' => $publishYear,
                        'preview' => $preview,
                        'physical_description' => json_encode($physicalDescription),
                        'sync' => 0,
                        'manual' => 1,
                        'akses' => $access,
                        'status' => 1,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                        'price' => str_replace([',', '.'], '', $price),
                        'author' => $contributor,
                        'jilid' => $binding,
                        'currency' => $currency,
                        'description' => $sinopsis,
                        'edition' => $edition,
                        'edition_date' => $finalEditionDate ?? null,
                    ];

                    $additionalData = [
                        'copyright' => Main::copyright($copyrightId),
                        'penerbit_id' => $executorId,
                        'title' => $titleOri,
                    ];

                    $collectionData = array_merge($baseCollectionData, $additionalData);
                    $createCollection = QueryAPI::create('e_collections', $collectionData);

                    if (!$createCollection) {
                        QueryAPI::create('e_bulk_details', [
                            'bulk_id' => $bulkId,
                            'title' => $edition ?? $title,
                            'description' => 'Gagal membuat data koleksi',
                            'status' => 'GAGAL'
                        ]);

                        continue;
                    }

                    $fileUploadResult = $this->handleFileUpload(
                        $createCollection,
                        $path,
                        $fileCover,
                        $fileContent,
                        $bulkId,
                        $edition ?? $title
                    );

                    QueryAPI::create('e_bulk_details', [
                        'bulk_id' => $bulkId,
                        'title' => $edition ?? $title,
                        'description' => $fileUploadResult['description'],
                        'status' => $fileUploadResult['status']
                    ]);
                }

                QueryAPI::update('e_bulks', $bulkId, [
                    'process_finish_at' => now()->toDateTimeString(),
                    'status_progress' => 'SELESAI'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Bulk ' . $bulkId . ' GAGAL: ' . $e->getMessage(), [
                'exception' => $e,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            QueryAPI::update('e_bulks', $bulkId, [
                'process_start_at' => now()->toDateTimeString(),
                'process_finish_at' => now()->toDateTimeString(),
                'status_progress' => 'GAGAL'
            ]);
        }
    }

    /**
     * handleFileUpload.
     *
     * @param object $collectionData
     * @param string $path
     * @param string|null $fileCover
     * @param string|null $fileContent
     * @param int $bulkId
     * @param string $title
     * @return array ['status', 'description']
     */
    private function handleFileUpload($collectionData, $path, $fileCover, $fileContent, $bulkId, $title)
    {
        $uploadSuccess = true;
        $description = 'Data koleksi berhasil dibuat.';
        $getFullPath = fn($fileName) => $path . '/' . $fileName;
        $fileCoverPath = $fileCover ? $getFullPath($fileCover) : null;
        $fileContentPath = $fileContent ? $getFullPath($fileContent) : null;
        $fileCoverExists = $fileCoverPath && Storage::exists($fileCoverPath);
        $fileContentExists = $fileContentPath && Storage::exists($fileContentPath);
        $filesToUpload = [];

        if ($fileCoverExists) {
            $filesToUpload[] = [
                'type' => 'cover',
                'filePath' => $fileCoverPath,
                'hash_prefix' => 'FILE-COVER-',
            ];
        } else {
            $description .= ' File cover tidak ditemukan.';
        }

        if ($fileContentExists) {
            $filesToUpload[] = [
                'type' => 'konten_digital',
                'filePath' => $fileContentPath,
                'hash_prefix' => 'FILE-KONTEN-',
            ];
        } else {
            $description .= ' File konten tidak ditemukan.';
        }

        foreach ($filesToUpload as $file) {
            $fileStream = null;

            try {
                $mimeType = Storage::mimeType($file['filePath']);
                $fileSize = Storage::size($file['filePath']);
                $fileStream = Storage::readStream($file['filePath']);

                QueryAPI::uploadFile([
                    'type' => $file['type'],
                    'id' => $collectionData->ID,
                    'status' => 1,
                    'hash' => md5($file['hash_prefix'] . $collectionData->SLUG),
                    'mime' => $mimeType,
                    'filesize' => $fileSize,
                    'method' => 7,
                    'iszip' => false,
                    'file' => $fileStream,
                    'filename' => basename($file['filePath']),
                ]);
            } catch (\Exception $e) {
                $uploadSuccess = false;
                $description = "GAGAL saat upload file **{$file['type']}**: " . $e->getMessage();

                Log::error("Bulk ID: {$bulkId}, Title: {$title}. Gagal upload file {$file['type']}: " . $e->getMessage());

                break;
            }
        }

        if (!$uploadSuccess) {
            return ['status' => 'GAGAL', 'description' => $description];
        } elseif ($fileCoverExists || $fileContentExists) {
            return ['status' => 'SUKSES', 'description' => $description];
        } else {
            return ['status' => 'SUKSES', 'description' => 'Data koleksi berhasil dibuat, file cover dan konten tidak ditemukan.'];
        }
    }
}
