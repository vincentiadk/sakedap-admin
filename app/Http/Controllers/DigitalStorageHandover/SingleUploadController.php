<?php

namespace App\Http\Controllers\DigitalStorageHandover;

use App\Helpers\ISBN;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SingleUploadController extends Controller
{
    private $worksheetCategory;

    public function __construct()
    {
        $this->worksheetCategory = Main::COLLECTION_DIGITAL;
    }

    public function index(Request $request)
    {
        $uploadIDCover = $request->upload_id_cover;
        $uploadIDContent = $request->upload_id_content;

        return view('layouts.index', [
            'data' => [
                'worksheet' => QueryAPI::get("select * from worksheets where category = '$this->worksheetCategory'") ?? [],
                'media' => QueryAPI::get("select * from collectionmedias where (isdelete = 0 or isdelete is null) and worksheet_id in (20,142)") ?? [],
                'category' => QueryAPI::get("select * from e_categories where deleted_at is null") ?? [],
                'contentType' => QueryAPI::get("select * from fieldrefs where tag = '336'") ?? [],
                'containerType' => QueryAPI::get("select * from fieldrefs where tag = '337'") ?? [],
                'mediaType' => QueryAPI::get("select * from fieldrefs where tag = '338'") ?? [],
                'bigClass' => QueryAPI::get("select * from master_kelas_besar") ?? [],
                'uploadIDCover' => $uploadIDCover,
                'uploadIDContent' => $uploadIDContent,
                'content' => 'digital-storage-handover.single-upload',
                'plugins' => [
                    'select2',
                    'daterangepicker',
                    'fileinput',
                    'datatable',
                ]
            ]
        ]);
    }

    public function checkISBNCode(Request $request)
    {
        $code = $request->code;
        $data = ISBN::get('search', ['code' => $code], true);

        return response()->json([
            'code' => $data ? 200 : 500,
            'data' => $data
        ]);
    }

    public function catalogParent(Request $request)
    {
        $id = $request->id;
        $data = QueryAPI::get("
            select
                catalogs.*,
                penerbit.name as name_penerbit,
                kabupaten.namakab as namakab,
                propinsi.namapropinsi as namapropinsi,
                e_collections.code_type as code_type_e_collection,
                e_collections.serial as serial_e_collection,
                e_collections.currency as currency_e_collection,
                e_collections.price as price_e_collection,
                e_collections.jilid as jilid_e_collection,
                e_collections.description as description_e_collection
            from
                catalogs
            left join
                penerbit on penerbit.id = catalogs.penerbit_id
            left join
                kabupaten on kabupaten.id = catalogs.city_id
            left join
                propinsi on propinsi.id = kabupaten.propinsiid
            left join
                e_collections on e_collections.id = catalogs.edeposit_col_id
            where
                catalogs.id = $id
        ", true);

        return response()->json($data);
    }

    public function submitted(Request $request)
    {
        $response = [];

        if ($request->ajax()) {
            $validation = Validator::make($request->all(), [
                'executor_id' => 'required',
                'worksheet_id' => 'required',
                'city_id' => 'required',
                'title' => 'required',
                'collection_media_id' => 'required',
                'received_at' => 'required',
                'access' => 'required',
                'file_cover' => 'nullable|image|mimes:png,jpg,jpeg|max:' . config('system.catalog_cover_max_upload'),
                'file_content' => 'nullable|file|mimes:pdf,epub,mp3,mp4,wav|max:' . config('system.catalog_content_max_upload'),
            ], [
                'executor_id.required' => 'Pelaksana serah tidak boleh kosong',
                'worksheet_id.required' => 'Jenis bahan tidak boleh kosong',
                'city_id.required' => 'Kota tidak boleh kosong',
                'title.required' => 'Judul tidak boleh kosong',
                'collection_media_id.required' => 'Media tidak boleh kosong',
                'received_at.required' => 'Tanggal terima tidak boleh kosong',
                'access.required' => 'Akses tidak boleh kosong',
                'file_cover.image' => 'File cover tidak valid',
                'file_cover.mimes' => 'File cover harus png, jpg, jpeg',
                'file_cover.max' => 'File cover maksimal ' . Main::formatFileSize((int) config('system.catalog_cover_max_upload')),
                'file_content.file' => 'File konten tidak valid',
                'file_content.mimes' => 'File konten harus pdf, epub, mp3, mp4, wav',
                'file_content.max' => 'File konten maksimal ' . Main::formatFileSize((int) config('system.catalog_content_max_upload')),
            ]);

            if ($validation->fails()) {
                $response = [
                    'code' => 400,
                    'error' => $validation->errors()->all(),
                ];
            } else {
                try {
                    $currentTime = date('Y-m-d H:i:s');
                    $userId = session('id');
                    $publishTime = $request->publish_time ? strtotime($request->publish_time) : time();
                    $receivedTime = strtotime($request->received_at);
                    $catalogId = $request->catalog_id;
                    $catalog = null;

                    if ($catalogId) {
                        $catalog = QueryAPI::get("select edeposit_col_id from catalogs where id = $catalogId", true);
                    }

                    $uploadIDCover = $request->upload_id_cover;
                    $uploadIDContent = $request->upload_id_content;

                    $baseCollectionData = [
                        'id_old' => 0,
                        'parent_id' => $catalog->EDEPOSIT_COL_ID ?? null,
                        'publisher_id' => $request->executor_id,
                        'city_id' => $request->city_id,
                        'title_ori' => $request->title,
                        'album' => $request->album,
                        'slug' => Str::slug($request->title, '-'),
                        'series' => $request->series,
                        'serial' => $request->serial,
                        'code' => $request->code,
                        'code_type' => $request->code_type ?? 0,
                        'publication_month' => date('m', $publishTime),
                        'publication_year' => date('Y', $publishTime),
                        'publication_day' => date('d', $publishTime),
                        'preview' => $request->preview,
                        'physical_description' => json_encode($request->physical_description),
                        'sync' => 0,
                        'manual' => 1,
                        'akses' => $request->access,
                        'status' => 2,
                        'received_at' => date('Y-m-d H:i:s', $receivedTime),
                        'received_by' => $userId,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                        'validated_at' => $currentTime,
                        'validated_by' => $userId,
                        'price' => str_replace([',', '.'], '', $request->price ?? '0'),
                        'copyright' => Main::copyright($request->executor_id),
                        'worksheet_id' => $request->worksheet_id,
                        'collection_media_id' => $request->collection_media_id,
                        'penerbit_id' => $request->executor_id,
                        'kabupaten_id' => $request->city_id,
                        'title' => $request->title,
                        'author' => implode(';', ($request->author ?? [])),
                        'jilid' => $request->binding,
                        'currency' => $request->currency ?? 'IDR',
                        'jenis_isi' => $request->content_type,
                        'jenis_wadah' => $request->container_type,
                        'jenis_media' => $request->media_type,
                        'description' => $request->description,
                        'kelas_besar_id' => $request->big_class_id,
                        'edition' => $request->edition,
                        'edition_date' => $request->edition_date ? date('Y-m-d H:i:s', strtotime($request->edition_date)) : null,
                        'qrcbn' => $request->qrcbn,
                    ];

                    $createCollection = QueryAPI::create('e_collections', $baseCollectionData);

                    if (!$createCollection) {
                        throw new \Exception('Gagal membuat data koleksi');
                    }

                    if ($request->category && is_array($request->category)) {
                        $categoryData = [];

                        foreach ($request->category as $categoryId) {
                            $categoryData[] = [
                                'collection_id' => $createCollection->ID,
                                'category_id' => $categoryId
                            ];
                        }

                        foreach ($categoryData as $data) {
                            QueryAPI::create('e_collection_categories', $data);
                        }
                    }

                    if ($request->cc_edition && $request->has_edition) {
                        $filesToUpload = [];

                        foreach ($request->cc_edition as $key => $cce) {
                            $editionTitle = $request->cc_edition_title[$key] ?? null;
                            $editionDate = $request->cc_edition_date[$key] ?? null;

                            // Data artikel baru
                            $articleTitle = $request->cc_edition_article_title[$key] ?? null;
                            $articleContributor = $request->cc_edition_article_contributor[$key] ?? null;
                            $articleAbstract = $request->cc_edition_article_abstract[$key] ?? null;
                            $articleSubject = $request->cc_edition_article_subject[$key] ?? null;
                            $articleOriginalLink = $request->cc_edition_article_original_link[$key] ?? null;
                            $articlePublishDate = $request->cc_edition_article_publish_date[$key] ?? null;
                            $articleDoi = $request->cc_edition_article_doi[$key] ?? null;

                            $editionCover = null;
                            $editionContent = null;

                            if ($request->hasFile('cc_edition_cover') && isset($request->file('cc_edition_cover')[$key])) {
                                $editionCover = $request->file('cc_edition_cover')[$key];
                            }
                            if ($request->hasFile('cc_edition_content') && isset($request->file('cc_edition_content')[$key])) {
                                $editionContent = $request->file('cc_edition_content')[$key];
                            }

                            if ($editionTitle && $editionDate && $editionCover && $editionContent && $editionCover->isValid() && $editionContent->isValid()) {
                                $editionData = $baseCollectionData;
                                $editionData['parent_id'] = $createCollection->ID;
                                $editionData['edition'] = $editionTitle;
                                $editionData['edition_date'] = date('Y-m-d H:i:s', strtotime($editionDate));
                                $editionData['publication_month'] = date('m', strtotime($editionDate));
                                $editionData['publication_year'] = date('Y', strtotime($editionDate));
                                $editionData['publication_day'] = date('d', strtotime($editionDate));
                                $editionData['jilid'] = $request->binding;
                                $editionData['currency'] = $request->currency ?? 'IDR';
                                $editionData['jenis_isi'] = $request->content_type;
                                $editionData['jenis_wadah'] = $request->container_type;
                                $editionData['jenis_media'] = $request->media_type;
                                $editionData['article_title'] = $articleTitle;
                                $editionData['article_contributor'] = $articleContributor;
                                $editionData['article_abstract'] = $articleAbstract;
                                $editionData['article_subject'] = $articleSubject;
                                $editionData['article_original_link'] = $articleOriginalLink;
                                $editionData['article_publish_date'] = $articlePublishDate ? date('Y-m-d', strtotime($articlePublishDate)) : null;
                                $editionData['article_doi'] = $articleDoi;
                                $createEdition = QueryAPI::create('e_collections', $editionData);

                                if ($createEdition) {
                                    if ($request->category && is_array($request->category)) {
                                        foreach ($request->category as $categoryId) {
                                            QueryAPI::create('e_collection_categories', [
                                                'collection_id' => $createEdition->ID,
                                                'category_id' => $categoryId
                                            ]);
                                        }
                                    }

                                    $filesToUpload[] = [
                                        'collection_id' => $createEdition->ID,
                                        'slug' => $createEdition->SLUG,
                                        'cover' => $editionCover,
                                        'content' => $editionContent,
                                    ];
                                }
                            }
                        }

                        foreach ($filesToUpload as $fileData) {
                            QueryAPI::uploadFile([
                                'type' => 'cover',
                                'id' => $fileData['collection_id'],
                                'status' => 1,
                                'hash' => md5('FILE-COVER-' . $fileData['slug']),
                                'mime' => $fileData['cover']->getMimeType(),
                                'filesize' => $fileData['cover']->getSize(),
                                'method' => 4,
                                'iszip' => false,
                                'file' => $fileData['cover'],
                            ]);

                            QueryAPI::uploadFile([
                                'type' => 'konten_digital',
                                'id' => $fileData['collection_id'],
                                'status' => 1,
                                'hash' => md5('FILE-KONTEN-' . $fileData['slug']),
                                'mime' => $fileData['content']->getMimeType(),
                                'filesize' => $fileData['content']->getSize(),
                                'method' => 4,
                                'iszip' => false,
                                'file' => $fileData['content'],
                            ]);

                            QueryAPI::verificationCollection($fileData['collection_id']);
                        }
                    }

                    $fileCover = $request->file('file_cover');
                    $fileContent = $request->file('file_content');

                    if ($fileCover) {
                        QueryAPI::uploadFile([
                            'type' => 'cover',
                            'id' => $createCollection->ID,
                            'status' => 1,
                            'hash' => md5('FILE-COVER-' . $createCollection->SLUG),
                            'mime' => $fileCover->getMimeType(),
                            'filesize' => $fileCover->getSize(),
                            'method' => 4,
                            'iszip' => false,
                            'file' => $fileCover,
                        ]);
                    }

                    if ($fileContent) {
                        QueryAPI::uploadFile([
                            'type' => 'konten_digital',
                            'id' => $createCollection->ID,
                            'status' => 1,
                            'hash' => md5('FILE-KONTEN-' . $createCollection->SLUG),
                            'mime' => $fileContent->getMimeType(),
                            'filesize' => $fileContent->getSize(),
                            'method' => 4,
                            'iszip' => false,
                            'file' => $fileContent,
                        ]);
                    }

                    if ($uploadIDCover && $uploadIDContent) {
                        QueryAPI::query("update catalogcovers set e_col_id = $createCollection->ID where upload_id = $uploadIDCover");
                        QueryAPI::query("update catalogfiles set e_col_id = $createCollection->ID where upload_id = $uploadIDContent");
                    }

                    QueryAPI::verificationCollection($createCollection->ID);

                    $response = [
                        'code' => 200,
                        'message' => 'Data telah ditambahkan'
                    ];
                } catch (\Exception $e) {
                    $response = [
                        'code' => $e->getCode() ?: 500,
                        'message' => $e->getMessage()
                    ];
                }
            }
        }

        return response()->json($response);
    }
}
