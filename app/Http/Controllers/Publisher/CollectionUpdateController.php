<?php

namespace App\Http\Controllers\Publisher;

use App\Models\User;
use App\Models\Author;
use App\Models\Subject;
use App\Models\Category;
use App\Models\Location;
use App\Jobs\PDFToImage;
use App\Models\Collection;
use App\Models\Contributor;
use App\Jobs\WatermarkFilm;
use Illuminate\Support\Str;
use App\Jobs\WatermarkAudio;
use Illuminate\Http\Request;
use App\Helper\GeneralHelper;
use App\Models\CollectionMedia;
use App\Models\PublisherAccess;
use App\Models\CollectionProblem;
use App\Models\CollectionSubject;
use App\Models\CollectionCategory;
use App\Http\Controllers\Controller;
use App\Models\CollectionContributor;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CollectionUpdateController extends Controller
{

    protected $location;

    public function __construct()
    {
        $this->location = Location::where('active', 1)->first();
    }
    public function update(Request $request, $id)
    {
        $collection = Collection::find($id);

        if ($request->has('_token')) {
            if ($collection->type == 1) {

                $validator = Validator::make($request->all(), [
                    'title'            => 'required',
                    'publication_year' => 'required|date_format:Y',
                    'cover'            => 'image|max:2048|mimes:jpg,jpeg,png',
                    'preview'          => 'required|regex:/^\d+-\d+$/',
                ], [
                    'title.required'                => 'Judul wajib di isi!',
                    'preview.required'              => 'Preview wajib di isi!',
                    'preview.regex'                 => 'Format preview tidak benar, hanya gunakan angka!',
                    'publication_year.required'    => 'Tahun terbit wajib di isi!',
                    'publication_year.date_format' => 'Tahun terbit harus berupa tahun!',
                    'cover.image'                  => 'Cover berupa file image!',
                    'cover.max'                    => 'Cover maksimal 1MB!',
                    'cover.mimes'                  => 'Cover harus bertipe jpg, jpeg, png!'
                ]);
            } else if ($collection->type == 2) {
                $validator = Validator::make($request->all(), [
                    'title'            => 'required',
                    'publication_year' => 'required|date_format:Y',
                    'cover'            => 'image|max:2048|mimes:jpg,jpeg,png',
                    'preview'          => 'required',
                ], [
                    'title.required'               => 'Judul wajib di isi!',
                    'preview.required'              => 'Preview wajib di isi!',
                    'publication_year.required'    => 'Tahun terbit wajib di isi!',
                    'publication_year.date_format' => 'Tahun terbit harus berupa tahun!',
                    'cover.image'                  => 'Cover berupa file image!',
                    'cover.max'                    => 'Cover maksimal 1MB!',
                    'cover.mimes'                  => 'Cover harus bertipe jpg, jpeg, png!'
                ]);
            } else if ($collection->type == 3) {
                $validator = Validator::make($request->all(), [
                    'title'            => 'required',
                    'publication_year' => 'required|date_format:Y',
                    'cover'            => 'image|max:2048|mimes:jpg,jpeg,png',
                    'preview'          => 'required',
                ], [
                    'title.required'               => 'Judul wajib di isi!',
                    'preview.required'              => 'Preview wajib di isi!',
                    'publication_year.required'    => 'Tahun terbit wajib di isi!',
                    'publication_year.date_format' => 'Tahun terbit harus berupa tahun!',
                    'cover.image'                  => 'Cover berupa file image!',
                    'cover.max'                    => 'Cover maksimal 1MB!',
                    'cover.mimes'                  => 'Cover harus bertipe jpg, jpeg, png!'
                ]);
            } else if ($collection->type == 4) {
                $validator = Validator::make($request->all(), [
                    'title'            => 'required',
                    'publication_year' => 'required|date_format:Y',
                    'cover'            => 'image|max:2048|mimes:jpg,jpeg,png',
                ], [
                    'title.required'               => 'Judul wajib di isi!',
                    'publication_year.required'    => 'Tahun terbit wajib di isi!',
                    'publication_year.date_format' => 'Tahun terbit harus berupa tahun!',
                    'cover.image'                  => 'Cover berupa file image!',
                    'cover.max'                    => 'Cover maksimal 1MB!',
                    'cover.mimes'                  => 'Cover harus bertipe jpg, jpeg, png!'
                ]);
            } else if ($collection->type == 5) {
                $validator = Validator::make($request->all(), [
                    'title'            => 'required',
                    'publication_year' => 'required|date_format:Y',
                    'cover'            => 'image|max:2048|mimes:jpg,jpeg,png',
                    'preview'          => 'required',
                ], [
                    'title.required'               => 'Judul wajib di isi!',
                    'preview.required'              => 'Preview wajib di isi!',
                    'publication_year.required'    => 'Tahun terbit wajib di isi!',
                    'publication_year.date_format' => 'Tahun terbit harus berupa tahun!',
                    'cover.image'                  => 'Cover berupa file image!',
                    'cover.max'                    => 'Cover maksimal 1MB!',
                    'cover.mimes'                  => 'Cover harus bertipe jpg, jpeg, png!'
                ]);
            } else if ($collection->type == 6) {
                $validator = Validator::make($request->all(), [
                    'title'            => 'required',
                    'publication_year' => 'required|date_format:Y',
                    'cover'            => 'image|max:2048|mimes:jpg,jpeg,png',
                    'preview'          => 'required',
                ], [
                    'title.required'               => 'Judul wajib di isi!',
                    'preview.required'              => 'Preview wajib di isi!',
                    'publication_year.required'    => 'Tahun terbit wajib di isi!',
                    'publication_year.date_format' => 'Tahun terbit harus berupa tahun!',
                    'cover.image'                  => 'Cover berupa file image!',
                    'cover.max'                    => 'Cover maksimal 1MB!',
                    'cover.mimes'                  => 'Cover harus bertipe jpg, jpeg, png!'
                ]);
            }

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator);
            } else {
                if ($collection->type == 1) {
                    $physical_description = [
                        'total_page'  => $request->total_page,
                        'dimension'   => $request->dimension,
                        'ilustration' => $request->ilustration
                    ];
                } else if ($collection->type == 2) {
                    $physical_description = [
                        'total_page'  => $request->total_page,
                        'dimension'   => $request->dimension
                    ];
                } else if ($collection->type == 3) {
                    $physical_description = [
                        'total_page' => $request->total_page,
                        'scale'      => $request->scale,
                        'dimension'  => $request->dimension
                    ];
                } else if ($collection->type == 4) {
                    $physical_description = [
                        'total_page' => $request->total_page,
                        'dimension'  => $request->dimension
                    ];
                } else if ($collection->type == 5) {
                    $physical_description = [
                        'duration' => $request->duration
                    ];
                } else if ($collection->type == 6) {
                    $physical_description = [
                        'duration' => $request->duration
                    ];
                }

                $user = User::find(session('id'));
                $publisher = $user->publisher;
                $collection = Collection::where('id', $id)
                    ->first();

                if (!$collection) {
                    return response()->json(['code' => 400], 400);
                }

                $old_data = $collection;

                $update = $collection->update([
                    'publisher_id'         => $publisher->id,
                    'city_id'              => $publisher->city_id,
                    'title'                => $request->title,
                    'physical_description' => json_encode($physical_description),
                    'album'                => $request->album,
                    'slug'                 => Str::slug($request->title, '-'),
                    'publication_year'     => $request->publication_year,
                    'publication_month'    => isset($request->publication_month) ? $request->publication_month : $collection->publication_month,
                    'edition'              => $request->edition,
                    'series'               => $request->series,
                    'serial'               => $request->serial,
                    'volume'               => $request->volume,
                    'preview'              => $request->preview,
                    'description'          => $request->description,
                    'status'               => 1,
                    'updated_by'           => session('id')
                ]);

                if ($update) {
                    CollectionCategory::where('collection_id', $id)->delete();
                    CollectionContributor::where('collection_id', $id)->delete();
                    CollectionSubject::where('collection_id', $id)->delete();

                    if ($request->has('collection_category')) {
                        foreach ($request->collection_category as $cc) {
                            CollectionCategory::create([
                                'collection_id' => $id,
                                'category_id'   => $cc
                            ]);
                        }
                    }

                    if ($request->has('contributor_contributor_id_field')) {
                        foreach ($request->contributor_contributor_id_field as $key => $ccid) {
                            $authorCheck = Author::updateOrCreate([
                                'fullname'      => $request->contributor_fullname_field[$key],
                                'title'         => $request->contributor_title_field[$key],
                                'slug'          => Str::slug($request->contributor_title_field[$key], '-')
                            ], [
                                'year_of_birth' => $request->contributor_year_of_birth_field[$key],
                                'year_of_death' => $request->contributor_year_of_death_field[$key]
                            ]);

                            $author = Author::where('fullname', $request->contributor_fullname_field[$key])
                                ->where('title', $request->contributor_title_field[$key])
                                ->where('slug', Str::slug($request->contributor_title_field[$key], '-'))
                                ->where('year_of_birth', $request->contributor_year_of_birth_field[$key])
                                ->where('year_of_death', $request->contributor_year_of_death_field[$key])
                                ->first();

                            CollectionContributor::create([
                                'collection_id'  => $id,
                                'contributor_id' => $ccid,
                                'author_id'      => $author->id
                            ]);
                        }
                    }

                    if ($request->has('collection_subject')) {
                        foreach ($request->collection_subject as $cs) {
                            $subjectCheck = Subject::updateOrCreate([
                                'slug' => Str::slug($cs, '-')
                            ], [
                                'name' => $cs
                            ]);

                            $subject = Subject::where('name', $cs)
                                ->where('slug', Str::slug($cs, '-'))
                                ->first();

                            CollectionSubject::create([
                                'collection_id' => $id,
                                'subject_id'    => $subject->id
                            ]);
                        }
                    }

                    if ($request->cover) {
                        $collectionMedia = $collection->collectionMedia->where('type', 1)->first();
                        $old_media = null;
                        if ($collectionMedia) {
                            Storage::disk($this->location->location)->delete($collectionMedia->link);
                            $media = CollectionMedia::where('id', $collectionMedia->id)->first();;
                            $old_media = $media;
                            $media->forceDelete();
                        }

                        if ($collection->type == 1) {
                            $path = 'public/collection/book/cover/';
                        } else if ($collection->type == 2) {
                            $path = 'public/collection/partitur/cover';
                        } else if ($collection->type == 3) {
                            $path = 'public/collection/map/cover';
                        } else if ($collection->type == 4) {
                            $path = 'public/collection/serial/cover';
                        } else if ($collection->type == 5) {
                            $path = 'public/collection/audio/cover';
                        } else if ($collection->type == 6) {
                            $path = 'public/collection/film/cover';
                        }
                        $file_name = Storage::disk($this->location->location)->put($path . '/' . $collection->id, $request->file('cover'));
                        $media = CollectionMedia::create([
                            'collection_id' => $id,
                            'link'          => $file_name,
                            'size'          => File::size($request->file('cover')),
                            'extension'     => $request->file('cover')->getClientOriginalExtension(),
                            'mimes'         => File::mimeType($request->file('cover')),
                            'hash'          => md5_file($request->file('cover')),
                            'type'          => 1,
                            'method'        => 3,
                            'location_id'   => $this->location->id
                        ]);

                        activity('collections')
                            ->performedOn($collection)
                            ->causedBy(session('id'))
                            ->withProperties([
                                'data_lama' => [
                                    'link'          => $media->link,
                                    'size'          => $media->size,
                                    'extension'     => $media->extension,
                                    'mimes'         => $media->mimes,
                                    'hash'          => $media->hash
                                ],
                                'data_baru' => [
                                    'link'          => $old_media != null ? $old_media->link : null,
                                    'size'          => $old_media != null ? $old_media->size : null,
                                    'extension'     => $old_media != null ? $old_media->extension : null,
                                    'mimes'         => $old_media != null ? $old_media->mimes : null,
                                    'hash'          => $old_media != null ? $old_media->hash : null
                                ]
                            ])
                            ->log('Mengubah cover koleksi (' . $collection->title . ')');
                    }

                    if ($request->original) {
                        $original = $request->original;
                        $collectionMedia = $collection->collectionMedia->where('type', 2)->first();
                        $old_original = null;
                        if ($collectionMedia) {
                            Storage::disk($this->location->location)->delete($collectionMedia->link);
                            $media = CollectionMedia::where('id', $collectionMedia->id)->first();
                            $old_original = $media;
                            $media->forceDelete();
                        }

                        if ($collection->type == 1) {
                            $type = 'book';
                        } else if ($collection->type == 2) {
                            $type = 'partitur';
                        } else if ($collection->type == 3) {
                            $type = 'map';
                        } else if ($collection->type == 4) {
                            $type = 'serial';
                        } else if ($collection->type == 5) {
                            $type = 'audio';
                        } else if ($collection->type == 6) {
                            $type = 'film';
                        }

                        $targetFolder         = 'public\collection' . DIRECTORY_SEPARATOR . $type . '\original' . DIRECTORY_SEPARATOR . $collection->id;
                        $imagesFolder         = 'public\collection' . DIRECTORY_SEPARATOR . $type . '\images' . DIRECTORY_SEPARATOR . $collection->id;
                        $watermarktargetFolder = 'public\collection' . DIRECTORY_SEPARATOR . $type . '\watermark' . DIRECTORY_SEPARATOR . $collection->id;

                        Storage::disk($this->location->location)->deleteDirectory($targetFolder);
                        Storage::disk($this->location->location)->deleteDirectory($imagesFolder);
                        Storage::disk($this->location->location)->deleteDirectory($watermarktargetFolder);

                        $dir_original = Storage::disk($this->location->location)->put('public/collection/' . $type . '/original/' . $collection->id, $original);
                        if ($type == 'book' || $type == 'partitur' || $type == 'map') {
                            $collectionMedia = $collection->collectionMedia->where('type', 3)->first();
                            if ($collectionMedia) {
                                CollectionMedia::where('id', $collectionMedia->id)->forceDelete();
                            }
                        } else if ($type == 'audio') {
                            $collectionMedia = $collection->collectionMedia->where('type', 2)->first();
                            if ($collectionMedia) {
                                CollectionMedia::where('id', $collectionMedia->id)->forceDelete();
                            }
                        } else if ($type ==  'film') {
                            $collectionMedia = $collection->collectionMedia->where('type', 2)->first();
                            if ($collectionMedia) {
                                CollectionMedia::where('id', $collectionMedia->id)->forceDelete();
                            }
                        }
                        if ($original->getClientOriginalExtension() == "pdf") {
                            $job = new PDFToImage($type, $dir_original, $collection->id);
                            dispatch(($job)->onQueue('convert_pdf'));
                        }

                        if ($type == 'audio') {
                            $originalType = 4;
                        } else if ($type == 'video') {
                            $originalType = 7;
                        } else {
                            $originalType = 2;
                        }

                        $create_media = CollectionMedia::create(
                            [
                                'collection_id' => $collection->id,
                                'link'          => $dir_original,
                                'size'          => File::size($original),
                                'extension'     => $original->getClientOriginalExtension(),
                                'mimes'         => File::mimeType($original),
                                'hash'          => md5_file($original),
                                'type'          => $originalType,
                                'method'        => 3,
                                'status'        => 1,
                                'location_id'   => $this->location->location,
                                'created_at'    => date('Y-m-d H:i:s'),
                                'updated_at'    => date('Y-m-d H:i:s'),
                                'location_id'   => $this->location->id
                            ]
                        );

                        if ($type == 'audio') {
                            $collectionMedia = $collection->collectionMedia->where('type', 3)->first();
                            if ($collectionMedia) {
                                CollectionMedia::where('id', $collectionMedia->id)->forceDelete();
                            }

                            // CollectionMedia::create([
                            //     'collection_id'         => $collection->id,
                            //     'link'                  => "",
                            //     'size'                  => "0",
                            //     'extension'             => "",
                            //     'mimes'                 => "",
                            //     'hash'                  => "",
                            //     'type'                  => 2,
                            //     'status'                => 0,
                            //     'method'                => 3,
                            //     'location_id'           => $this->location->location,
                            //     'created_at'            => date('Y-m-d H:i:s'),
                            //     'updated_at'            => date('Y-m-d H:i:s'),
                            //     'location_id'           => $this->location->id

                            // ]);

                            CollectionMedia::create([
                                'collection_id'         => $collection->id,
                                'link'                  => "",
                                'size'                  => "0",
                                'extension'             => "",
                                'mimes'                 => "",
                                'hash'                  => "",
                                'type'                  => 3,
                                'status'                => 0,
                                'method'                => 3,
                                'location_id'           => $this->location->location,
                                'created_at'            => date('Y-m-d H:i:s'),
                                'updated_at'            => date('Y-m-d H:i:s'),
                                'location_id'           => $this->location->id

                            ]);

                            dispatch(new WatermarkAudio(Storage::disk($this->location->location)->path($dir_original), $create_media))->onQueue('audio');
                        } else if ($type == 'film') {

                            $collectionMedia = $collection->collectionMedia->where('type', 3)->first();
                            if ($collectionMedia) {
                                CollectionMedia::where('id', $collectionMedia->id)->forceDelete();
                            }

                            // $preview = CollectionMedia::create([
                            //     'collection_id' => $collection->id,
                            //     'link'          => "",
                            //     'size'          => 0,
                            //     'extension'     => $original->getClientOriginalExtension(),
                            //     'mimes'         => "",
                            //     'hash'          => "",
                            //     'type'          => 8,
                            //     'method'        => 4,
                            //     'status'        => 0,
                            //     'location_id'   => $this->location->location,
                            //     'created_at'    => date('Y-m-d H:i:s'),
                            //     'updated_at'    => date('Y-m-d H:i:s'),
                            //     'location_id'   => $this->location->id
                            // ]);

                            $watermark = CollectionMedia::create([
                                'collection_id' => $collection->id,
                                'link'          => "",
                                'size'          => 0,
                                'extension'     => $original->getClientOriginalExtension(),
                                'mimes'         => "",
                                'hash'          => "",
                                'type'          => 3,
                                'method'        => 4,
                                'status'        => 0,
                                'location_id'   => $this->location->location,
                                'created_at'    => date('Y-m-d H:i:s'),
                                'updated_at'    => date('Y-m-d H:i:s'),
                                'location_id'   => $this->location->id
                            ]);

                            dispatch(new WatermarkFilm(Storage::disk($this->location->location)->path($dir_original), $create_media))->onQueue('film');
                        }

                        activity('collections')
                            ->performedOn($collection)
                            ->causedBy(session('id'))
                            ->withProperties([
                                'data_lama' => [
                                    'link'          => $create_media->link,
                                    'size'          => $create_media->size,
                                    'extension'     => $create_media->extension,
                                    'mimes'         => $create_media->mimes,
                                    'hash'          => $create_media->hash
                                ],
                                'data_baru' => [
                                    'link'          => $old_original != null ? $old_original->link : null,
                                    'size'          => $old_original != null ? $old_original->size : null,
                                    'extension'     => $old_original != null ? $old_original->extension : null,
                                    'mimes'         => $old_original != null ? $old_original->mimes : null,
                                    'hash'          => $old_original != null ? $old_original->hash : null
                                ]
                            ])
                            ->log('Mengubah file original koleksi (' . $collection->title . ')');
                    }

                    $this->updatePreviewAndAccess($request, $id);

                    CollectionProblem::where('collection_id', $collection->id)->update([
                        'solved'    => 1
                    ]);

                    activity('collections')
                        ->performedOn($collection)
                        ->causedBy(session('id'))
                        ->withProperties([
                            'data_lama' => [
                                'penerbit'         => $old_data->publisher->name,
                                'judul'            => $old_data->title,
                                'deskripsi_fisik'  => $old_data->physical_description,
                                'album'            => $old_data->album,
                                'tahun_terbit'     => $old_data->publication_year,
                                'edisi'            => $old_data->edition,
                                'seri'             => $old_data->series,
                                'serial'           => $old_data->serial,
                                'volume'           => $old_data->volume,
                                'deskripsi'        => $old_data->description,
                                'akses'            => $old_data->access(),
                                'diupdate_oleh'    => $old_data->updatedBy->username,
                            ],
                            'data_baru' => [
                                'penerbit'         => $collection->publisher->name,
                                'judul'            => $collection->title,
                                'deskripsi_fisik'  => $collection->physical_description,
                                'album'            => $collection->album,
                                'tahun_terbit'     => $collection->publication_year,
                                'edisi'            => $collection->edition,
                                'seri'             => $collection->series,
                                'serial'           => $collection->serial,
                                'volume'           => $collection->volume,
                                'deskripsi'        => $collection->description,
                                'akses'            => $collection->access(),
                                'diupdate_oleh'    => $collection->updatedBy->username,
                            ]
                        ])
                        ->log('Mengubah data koleksi (' . $collection->title . ')');

                    return redirect('publisher/collection/monitoring/')->with(['success' => 'Koleksi berhasil di update!']);
                } else {
                    return redirect()->back()->with(['failed' => 'Koleksi gagal di update!']);
                }
            }
        } else {



            $user = User::find(session('id'));
            $publisher_id = $user->publisher->id;
            try {
                $u_publisher_access = PublisherAccess::select('publisher_group_id')
                    ->where('publisher_id', $publisher_id)
                    ->first();

                $c_publisher_access = PublisherAccess::select('publisher_group_id')
                    ->where('publisher_id', $collection->publisher_id)
                    ->first();

                if ($c_publisher_access != $u_publisher_access) {
                    return abort(403, 'Unauthorized action.');
                }
            } catch (\Throwable $th) {
                print_r($th);
                exit;
            }




            if ($collection->type == 1) {
                if (count($collection->edition()->get()) > 0) {
                    $data = [
                        'title'   => 'Edit Pengelolaan Buku',
                        'content' => 'publisher.book.update_manage_jilid'
                    ];
                } else {
                    $data = [
                        'title'   => 'Edit Pengelolaan Buku',
                        'content' => 'publisher.book.update_manage'
                    ];
                }
            } else if ($collection->type == 2) {
                $data = [
                    'title'      => 'Edit Pengelolaan Partitur',
                    'content'    => 'publisher.partitur.update_manage'
                ];
            } else if ($collection->type == 3) {
                $data = [
                    'title'      => 'Edit Pengelolaan Peta',
                    'content'    => 'publisher.map.update_manage'
                ];
            } else if ($collection->type == 4) {
                $data = [
                    'title'      => 'Edit Pengelolaan Serial',
                    'content'    => 'publisher.serial.update_manage'
                ];
            } else if ($collection->type == 5) {
                $data = [
                    'title'      => 'Edit Pengelolaan Audio',
                    'content'    => 'publisher.audio.update_manage'
                ];
            } else if ($collection->type == 6) {
                $data = [
                    'title'      => 'Edit Pengelolaan Film',
                    'content'    => 'publisher.film.update_manage'
                ];
            } else {
                return redirect()->back();
            }

            $data = array_merge($data, [
                'category'    => Category::where('type', $collection->type)->get(),
                'collection'  => $collection,
                'contributor' => Contributor::where('type', $collection->type)->get(),
                'edition'     => Collection::where('parent_id', $id)->get()
            ]);

            return view('publisher.layout.index', ['data' => $data]);
        }
    }


    public function updatePreviewAndAccess(Request $request, $id)
    {
        $collection = Collection::find($id);

        $type = $collection->type;

        if ($type == 1) {
            $validator = Validator::make($request->all(), [
                'preview'               => 'regex:/^\d+-\d+$/',
            ], [
                'preview.regex'    => 'Format preview tidak benar, hanya gunakan angka!'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator);
            }
        }

        $access = isset($request->access) ? $request->access : $collection->access;
        $preview = isset($request->preview) ? $request->preview : $collection->preview;

        $update = $collection->update([
            'access'               => $access,
            'preview'              => $preview,
        ]);

        if ($type == 5) {
            $create_media = CollectionMedia::where('type', 2)->where('collection_id', $id)->first();

            if ($create_media) {
                $collectionMedia = $collection->collectionMedia->where('type', 3)->first();
                if ($collectionMedia) {
                    CollectionMedia::where('id', $collectionMedia->id)->forceDelete();
                }

                CollectionMedia::create([
                    'collection_id'         => $collection->id,
                    'link'                  => "",
                    'size'                  => "0",
                    'extension'             => "",
                    'mimes'                 => "",
                    'hash'                  => "",
                    'type'                  => 2,
                    'status'                => 0,
                    'method'                => 3,
                    'created_at'            => date('Y-m-d H:i:s'),
                    'updated_at'            => date('Y-m-d H:i:s'),

                ]);

                CollectionMedia::create([
                    'collection_id'         => $collection->id,
                    'link'                  => "",
                    'size'                  => "0",
                    'extension'             => "",
                    'mimes'                 => "",
                    'hash'                  => "",
                    'type'                  => 6,
                    'status'                => 0,
                    'method'                => 3,
                    'created_at'            => date('Y-m-d H:i:s'),
                    'updated_at'            => date('Y-m-d H:i:s'),

                ]);

                dispatch(new WatermarkAudio(Storage::disk($this->location->location)->path($create_media->link), $create_media))->onQueue('audio');
            }
        } else if ($type == 6) {
            $create_media = CollectionMedia::where('type', 2)->where('collection_id', $id)->first();

            if ($create_media) {
                $collectionMedia = $collection->collectionMedia->where('type', 3)->first();
                if ($collectionMedia) {
                    CollectionMedia::where('id', $collectionMedia->id)->forceDelete();
                }

                $preview = CollectionMedia::create([
                    'collection_id' => $collection->id,
                    'link'          => "",
                    'size'          => 0,
                    'extension'     => $collectionMedia->getClientOriginalExtension(),
                    'mimes'         => "",
                    'hash'          => "",
                    'type'          => 8,
                    'method'        => 4,
                    'status'        => 0,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                    'location_id'   => $this->location->id
                ]);

                $watermark = CollectionMedia::create([
                    'collection_id' => $collection->id,
                    'link'          => "",
                    'size'          => 0,
                    'extension'     => $collectionMedia->getClientOriginalExtension(),
                    'mimes'         => "",
                    'hash'          => "",
                    'type'          => 3,
                    'method'        => 4,
                    'status'        => 0,
                    'created_at'    => date('Y-m-d H:i:s'),
                    'updated_at'    => date('Y-m-d H:i:s'),
                    'location_id'   => $this->location->id
                ]);

                dispatch(new WatermarkFilm(Storage::disk($this->location->location)->path($create_media->link), $create_media))->onQueue('film');
            }
        }

        if ($request->access != $collection->access || $request->preview != $collection->preview) {
            activity()
                ->performedOn($collection)
                ->causedBy(User::find(session('id')))
                ->withProperties([
                    'data_lama' => [
                        'access'                => $request->access,
                        'preview'               => $request->preview,
                    ],
                    'data_baru' => [
                        'access'                => $collection->access,
                        'preview'               => $collection->preview,
                    ]
                ])
                ->log('Mengubah Hak Akses atau Preview ' . $collection->title);
        }


        return redirect()->back()->with('success', 'Berhasil mengubah koleksi!');
    }
}
