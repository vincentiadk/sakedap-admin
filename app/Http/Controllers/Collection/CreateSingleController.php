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
    public function index()
    {
        $data = [
            'worksheet' => QueryAPI::get("select * from worksheets where category is not null"),
            'media' => QueryAPI::get("select * from collectionmedias where isdelete = 0 or isdelete is null"),
            'category' => QueryAPI::get("select * from e_categories where deleted_at is null"),
            'contributor' => QueryAPI::get("select * from e_contributors where show = 1 and deleted_at is null"),
            'content' => 'collection.create-single'
        ];

        return view('layouts.index', ['data' => $data]);
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
                'branch_id' => 'required',
                'title' => 'required',
                'collection_media_id' => 'required',
                'received_at' => 'required',
                'access' => 'required',
                'file_cover' => 'required|image|mimes:png,jpg,jpeg',
                'file_content' => 'required|file|mimes:pdf,epub,mp3,mp4,wav',
            ], [
                'executor_id.required' => 'Pelaksana serah tidak boleh kosong',
                'worksheet_id.required' => 'Jenis tidak boleh kosong',
                'city_id.required' => 'Kota tidak boleh kosong',
                'city_id.required' => 'Perpustakaan tidak boleh kosong',
                'title.required' => 'Judul tidak boleh kosong',
                'collection_media_id.required' => 'Media tidak boleh kosong',
                'received_at.required' => 'Tanggal terima tidak boleh kosong',
                'access.required' => 'Akses tidak boleh kosong',
                'file_cover.required' => 'File cover tidak boleh kosong',
                'file_cover.image' => 'File cover tidak valid',
                'file_cover.mimes' => 'File cover harus png, jpg, jpeg',
                'file_content.required' => 'File konten tidak boleh kosong',
                'file_content.file' => 'File konten tidak valid',
                'file_content.mimes' => 'File konten harus pdf, epub, mp3, mp4, wav',
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
                        foreach ($request->cc_contributor as $key => $ccc) {
                            $contributorId = isset($request->cc_contributor_id[$key]) ? $request->cc_contributor_id[$key] : null;
                            $contributorName = isset($request->cc_contributor_name[$key]) ? $request->cc_contributor_name[$key] : null;
                            $contributorData = QueryAPI::get("select * from e_contributors where id = $contributorId and deleted_at is null", true);

                            if ($contributorData && $contributorName) {
                                $author[] = '(' . $contributorData->NAME . ') ' . $contributorName;
                            }
                        }
                    }

                    $createCollection = QueryAPI::create('e_collections', [
                        'id_old' => 0,
                        'publisher_id' => $request->executor_id,
                        'city_id' => $request->city_id,
                        'parent_id' => 0,
                        'branch_id' => $request->branch_id,
                        'title_ori' => $request->title,
                        'album' => $request->album,
                        'slug' => Str::slug($request->title, '-'),
                        'series' => $request->series,
                        'serial' => $request->serial,
                        'ddc' => $request->ddc,
                        'code' => $request->code,
                        'code_type' => $request->code_type ?? 0,
                        'publication_month' => date('m', strtotime($request->publish_time)),
                        'publication_year' => date('Y', strtotime($request->publish_time)),
                        'preview' => $request->preview,
                        'description' => $request->description,
                        'sync' => 0,
                        'manual' => 1,
                        'akses' => $request->access,
                        'status' => 2,
                        'received_at' => date('Y-m-d H:i:s', strtotime($request->received_at)),
                        'received_by' => session('id'),
                        'created_by' => session('id'),
                        'updated_by' => session('id'),
                        'validated_at' => date('Y-m-d H:i:s'),
                        'validated_by' => session('id'),
                        'price' => str_replace([',', '.'], '', $request->price),
                        'copyright' => Main::copyright($request->executor_id),
                        'worksheet_id' => $request->worksheet_id,
                        'collection_media_id' => $request->collection_media_id,
                        'penerbit_id' => $request->executor_id,
                        'kabupaten_id' => $request->city_id,
                        'title' => $request->title,
                        'author' => collect($author)->implode(';'),
                    ]);

                    if ($createCollection) {
                        if ($request->category) {
                            foreach ($request->category as $c) {
                                QueryAPI::create('e_collection_categories', [
                                    'collection_id' => $createCollection->ID,
                                    'category_id' => $c
                                ]);
                            }
                        }

                        if ($request->cc_edition && $request->has_edition) {
                            foreach ($request->cc_edition as $key => $cce) {
                                $editionTitle = isset($request->cc_edition_title[$key]) ? $request->cc_edition_title[$key] : null;
                                $editionDate = isset($request->cc_edition_date[$key]) ? $request->cc_edition_date[$key] : null;
                                $editionCover = $request->hasFile('cc_edition_cover')[$key] ? $request->file('cc_edition_cover')[$key] : null;
                                $editionContent = $request->hasFile('cc_edition_content')[$key] ? $request->file('cc_edition_content')[$key] : null;

                                if ($editionTitle && $editionDate && $editionCover && $editionContent) {
                                    $createEdition = QueryAPI::create('e_collections', [
                                        'id_old' => 0,
                                        'publisher_id' => $request->executor_id,
                                        'city_id' => $request->city_id,
                                        'branch_id' => $request->branch_id,
                                        'parent_id' => $createCollection->id,
                                        'title_ori' => $request->title,
                                        'album' => $request->album,
                                        'slug' => Str::slug($request->title, '-'),
                                        'series' => $request->series,
                                        'serial' => $request->serial,
                                        'ddc' => $request->ddc,
                                        'code' => $request->code,
                                        'code_type' => $request->code_type ?? 0,
                                        'publication_month' => date('m', strtotime($editionDate)),
                                        'publication_year' => date('Y', strtotime($editionDate)),
                                        'preview' => $request->preview,
                                        'edition' => $editionTitle,
                                        'preview' => $request->preview,
                                        'sync' => 0,
                                        'manual' => 1,
                                        'akses' => $request->access,
                                        'date' => $editionDate,
                                        'status' => 2,
                                        'received_at' => date('Y-m-d H:i:s', strtotime($request->received_at)),
                                        'received_by' => session('id'),
                                        'created_by' => session('id'),
                                        'updated_by' => session('id'),
                                        'validated_at' => date('Y-m-d H:i:s'),
                                        'validated_by' => session('id'),
                                        'price' => str_replace([',', '.'], '', $request->price),
                                        'copyright' => Main::copyright($request->executor_id),
                                        'worksheet_id' => $request->worksheet_id,
                                        'collection_media_id' => $request->collection_media_id,
                                        'penerbit_id' => $request->executor_id,
                                        'kabupaten_id' => $request->city_id,
                                        'title' => $request->title,
                                        'author' => collect($author)->implode(';'),
                                    ]);

                                    if ($createEdition) {
                                        $fileCover = $editionCover;
                                        $fileContent = $editionContent;

                                        QueryAPI::uploadFile([
                                            'type' => 'cover',
                                            'id' => $createEdition->ID,
                                            'status' => 1,
                                            'hash' => md5('FILE-COVER-' . $createEdition->SLUG),
                                            'mime' => $fileCover->getMimeType(),
                                            'filesize' => $fileCover->getSize(),
                                            'method' => 4,
                                            'iszip' => false,
                                            'file' => $fileCover,
                                        ]);

                                        QueryAPI::uploadFile([
                                            'type' => 'konten_digital',
                                            'id' => $createEdition->ID,
                                            'status' => 1,
                                            'hash' => md5('FILE-KONTEN-' . $createEdition->SLUG),
                                            'mime' => $fileContent->getMimeType(),
                                            'filesize' => $fileContent->getSize(),
                                            'method' => 4,
                                            'iszip' => false,
                                            'file' => $fileContent,
                                        ]);

                                        QueryAPI::verificationCollection($createEdition->ID);
                                    }
                                }
                            }
                        }

                        $fileCover = $request->file('file_cover');
                        $fileContent = $request->file('file_content');

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
