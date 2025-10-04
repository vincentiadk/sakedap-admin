<?php

namespace App\Http\Controllers\Collection;

use App\Helpers\ISBN;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CreateSingleController extends Controller
{
    private $worksheetCategory;

    public function __construct()
    {
        $this->worksheetCategory = Main::COLLECTION_DIGITAL;
    }

    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'worksheet' => QueryAPI::get("select * from worksheets where category = '$this->worksheetCategory'"),
                'media' => QueryAPI::get("select * from collectionmedias where isdelete = 0 or isdelete is null"),
                'category' => QueryAPI::get("select * from e_categories where deleted_at is null"),
                'contributor' => QueryAPI::get("select * from e_contributors where show = 1 and deleted_at is null"),
                'contentType' => QueryAPI::get("select * from fieldrefs where tag = '336'"),
                'containerType' => QueryAPI::get("select * from fieldrefs where tag = '337'"),
                'mediaType' => QueryAPI::get("select * from fieldrefs where tag = '338'"),
                'content' => 'collection.create-single',
                'plugins' => [
                    'select2',
                    'daterangepicker',
                    'fileinput',
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
                'file_cover' => 'required|image|mimes:png,jpg,jpeg|max:' . config('system.catalog_cover_max_upload'),
                'file_content' => 'required|file|mimes:pdf,epub,mp3,mp4,wav|max:' . config('system.catalog_content_max_upload'),
            ], [
                'executor_id.required' => 'Pelaksana serah tidak boleh kosong',
                'worksheet_id.required' => 'Jenis bahan tidak boleh kosong',
                'city_id.required' => 'Kota tidak boleh kosong',
                'city_id.required' => 'Perpustakaan tidak boleh kosong',
                'title.required' => 'Judul tidak boleh kosong',
                'collection_media_id.required' => 'Media tidak boleh kosong',
                'received_at.required' => 'Tanggal terima tidak boleh kosong',
                'access.required' => 'Akses tidak boleh kosong',
                'file_cover.required' => 'File cover tidak boleh kosong',
                'file_cover.image' => 'File cover tidak valid',
                'file_cover.mimes' => 'File cover harus png, jpg, jpeg',
                'file_cover.max' => 'File cover maksimal ' . ((int) config('system.catalog_cover_max_upload') / 1024) . 'MB',
                'file_content.required' => 'File konten tidak boleh kosong',
                'file_content.file' => 'File konten tidak valid',
                'file_content.mimes' => 'File konten harus pdf, epub, mp3, mp4, wav',
                'file_content.max' => 'File cover maksimal ' . ((int) config('system.catalog_content_max_upload') / 1024) . 'MB',
            ]);

            if ($validation->fails()) {
                $response = [
                    'code' => 400,
                    'error' => $validation->errors()->all(),
                ];
            } else {
                try {
                    $author = [];

                    if ($request->cc_contributor) {
                        $contributorIds = array_filter($request->cc_contributor_id ?? []);

                        if (!empty($contributorIds)) {
                            $contributorsData = QueryAPI::get("
                                select
                                    id,
                                    name
                                from
                                    e_contributors
                                where
                                    id in (" . implode(',', $contributorIds) . ") and
                                    deleted_at is null
                            ");

                            $contributorsLookup = [];

                            if ($contributorsData) {
                                foreach ($contributorsData as $contributor) {
                                    $contributorsLookup[$contributor->ID] = $contributor->NAME;
                                }
                            }

                            foreach ($request->cc_contributor as $key => $ccc) {
                                $contributorId = $request->cc_contributor_id[$key] ?? null;
                                $contributorName = $request->cc_contributor_name[$key] ?? null;

                                if ($contributorId && $contributorName && isset($contributorsLookup[$contributorId])) {
                                    $author[] = '(' . $contributorsLookup[$contributorId] . ') ' . $contributorName;
                                }
                            }
                        }
                    }

                    $currentTime = date('Y-m-d H:i:s');
                    $userId = session('id');
                    $publishTime = strtotime($request->publish_time);
                    $receivedTime = strtotime($request->received_at);

                    $baseCollectionData = [
                        'id_old' => 0,
                        'publisher_id' => $request->executor_id,
                        'city_id' => $request->city_id,
                        'title_ori' => $request->title,
                        'album' => $request->album,
                        'slug' => Str::slug($request->title, '-'),
                        'series' => $request->series,
                        'serial' => $request->serial,
                        'ddc' => $request->ddc,
                        'code' => $request->code,
                        'code_type' => $request->code_type ?? 0,
                        'publication_month' => date('m', $publishTime),
                        'publication_year' => date('Y', $publishTime),
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
                        'price' => str_replace([',', '.'], '', $request->price),
                        'copyright' => Main::copyright($request->executor_id),
                        'worksheet_id' => $request->worksheet_id,
                        'collection_media_id' => $request->collection_media_id,
                        'penerbit_id' => $request->executor_id,
                        'kabupaten_id' => $request->city_id,
                        'title' => $request->title,
                        'author' => implode(';', $author),
                        'parent_id' => 0,
                        'jilid' => $request->binding,
                        'currency' => $request->currency,
                        'jenis_isi' => $request->content_type,
                        'jenis_wadah' => $request->container_type,
                        'jenis_media' => $request->media_type,
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
                            $editionCover = null;
                            $editionContent = null;

                            if ($request->hasFile('cc_edition_cover') && isset($request->file('cc_edition_cover')[$key])) {
                                $editionCover = $request->file('cc_edition_cover')[$key];
                            }
                            if ($request->hasFile('cc_edition_content') && isset($request->file('cc_edition_content')[$key])) {
                                $editionContent = $request->file('cc_edition_content')[$key];
                            }

                            if ($editionTitle && $editionDate && $editionCover && $editionContent) {
                                $editionData = $baseCollectionData;
                                $editionData['parent_id'] = $createCollection->ID;
                                $editionData['edition'] = $editionTitle;
                                $editionData['date'] = $editionDate;
                                $editionData['publication_month'] = date('m', strtotime($editionDate));
                                $editionData['publication_year'] = date('Y', strtotime($editionDate));
                                $createEdition = QueryAPI::create('e_collections', $editionData);

                                if ($createEdition) {
                                    $filesToUpload[] = [
                                        'collection_id' => $createEdition->ID,
                                        'slug' => $createEdition->SLUG,
                                        'cover' => $editionCover,
                                        'content' => $editionContent,
                                        'jilid' => $request->binding,
                                        'currency' => $request->currency,
                                        'jenis_isi' => $request->content_type,
                                        'jenis_wadah' => $request->container_type,
                                        'jenis_media' => $request->media_type,
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

                    if ($fileCover && $fileContent) {
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

                        QueryAPI::verificationCollection($createCollection->ID);
                    }

                    $response = [
                        'code' => 200,
                        'message' => 'Data telah ditambahkan'
                    ];
                } catch (\Exception $e) {
                    $response = [
                        'code' => $e->getCode(),
                        'message' => $e->getMessage()
                    ];
                }
            }
        }

        return response()->json($response);
    }
}
