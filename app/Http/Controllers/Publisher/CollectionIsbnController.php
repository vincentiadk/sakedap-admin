<?php

namespace App\Http\Controllers\Publisher;

use App\Helper\GeneralHelper;
use App\Http\Controllers\Controller;
use App\Jobs\PDFToImage;
use App\Models\Author;
use App\Models\Category;
use App\Models\Collection;
use App\Models\CollectionCategory;
use App\Models\CollectionContributor;
use App\Models\CollectionMedia;
use App\Models\CollectionSubject;
use App\Models\Contributor;
use App\Models\Publisher;
use App\Models\Subject;
use App\Models\Location;
use App\Models\User;
use App\Models\Solr;
use Exception;
use Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CollectionIsbnController extends Controller
{
    protected $location;

    public function __construct()
    {
        $this->location = Location::where('active', 1)->first();
    }

    public function index()
    {

        $data = [
            'title' => 'Penyerahan Koleksi ISBN',
            'content' => 'publisher.collection.unggah_isbn',
        ];

        return view('publisher.layout.index', ['data' => $data]);
    }

    public function upload(Request $request)
    {

        try {
            $fileUpload = $request->file('file');

            if ($fileUpload) {

                $publisher = User::find(session('id'))->publisher;
                $publisher_code = $publisher->code_system;

                $extension = $fileUpload->getClientOriginalExtension();
                $fileName = basename($fileUpload->getClientOriginalName(), '.' . $fileUpload->getClientOriginalExtension());

                if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg' || $extension == 'pdf' || $extension == 'epub' || $extension == 'mp3') {
                    $getIsbn = Solr::data('isbn', 'complete', ['code' => trim(str_replace('-', '', $fileName))]);
                    $VIsbnLengkap = $getIsbn ? $getIsbn[0] : false;
                    if (!$VIsbnLengkap) {
                        $parent = Collection::create([
                            'publisher_id' => $publisher->id,
                            'title' => 'ISBN TIDAK DITEMUKAN',
                            'slug' => Str::slug('ISBN TIDAK DITEMUKAN', '-'),
                            'type' => 1,
                            'type_book' => 1,
                            'code' => $fileName,
                            'code_type' => 1,
                            'code_kdt' => 1,
                            'access' => 2,
                            'city_id' => $publisher->city_id,
                            'manual' => 1,
                            'preview' => '1-10',
                            'deposit' => GeneralHelper::depositCollection(),
                            'copyright' => 'Copyrights (c) ' . date('Y') . ' ' . $publisher->name,
                            'status' => 4,
                            'deposit_head_id' => 1,
                            'created_by' => session('id'),
                            'updated_by' => session('id'),
                        ]);
                        return response()->json('Koleksi berhasil di upload');
                    }
                    $publisherOnGroup = Publisher::checkGroupPublisher($publisher, $VIsbnLengkap["nama_penerbit"]);

                    if ($publisherOnGroup) {
                        if (!$publisherOnGroup->city_id) {
                            activity('collections')
                                ->causedBy(session('id'))
                                ->withProperties([
                                    'error' => 'File tidak bisa diunggah. Lengkapi alamat dan lokasi pada profil ' . $VIsbnLengkap["Penerbit"] . '!',
                                ])
                                ->log('Gagal Mengunggah File Koleksi / Cover dari unggah isbn');
                            return response()->json('File tidak bisa diunggah. Lengkapi alamat dan lokasi pada profil ' . $VIsbnLengkap["Penerbit"] . '!', 500);
                        }
                    } else if ($publisherOnGroup == null && !$publisher->city_id) {
                        activity('collections')
                            ->causedBy(session('id'))
                            ->withProperties([
                                'error' => 'File tidak bisa diunggah. Lengkapi alamat dan lokasi pada profil Anda!',
                            ])
                            ->log('Gagal Mengunggah File Koleksi / Cover dari unggah isbn');
                        return response()->json('File tidak bisa diunggah. Lengkapi alamat dan lokasi pada profil Anda!', 500);
                    }
                    $isbn = $VIsbnLengkap['prefix_element'] . '-' . $VIsbnLengkap['publisher_element'] . '-' . $VIsbnLengkap['item_element'] . '-' . $VIsbnLengkap['check_digit'];
                    if (strtolower($VIsbnLengkap["nama_penerbit"]) != strtolower($publisher->name)) {
                        if ($publisherOnGroup == null) {
                            $parent = Collection::create([
                                'publisher_id' => $publisher->id,
                                'title' => 'Data ISBN tidak sesuai dengan penerbit atau group penerbit',
                                'slug' => Str::slug('Data ISBN tidak sesuai dengan penerbit atau group penerbit', '-'),
                                'type' => 1,
                                'type_book' => 1,
                                'code' => $isbn,
                                'code_type' => 1,
                                'code_kdt' => 1,
                                'access' => 2,
                                'city_id' => $publisher->city_id,
                                'manual' => 1,
                                'preview' => '1-10',
                                'deposit' => GeneralHelper::depositCollection(),
                                'copyright' => 'Copyrights (c) ' . date('Y') . ' ' . $publisher->name,
                                'status' => 4,
                                'deposit_head_id' => 1,
                                'created_by' => session('id'),
                                'updated_by' => session('id'),
                            ]);

                            return response()->json('File tidak bisa diunggah. ISBN tidak sesuai dengan penerbit atau group penerbit!', 500);
                        }
                    }
                    if (str_contains(isset($VIsbnLengkap["title"]), '(BATAL TERBIT)')) {
                        $parent = Collection::create([
                            'publisher_id' => $publisher->id,
                            'title' => 'BATAL TERBIT',
                            'slug' => Str::slug('BATAL TERBIT', '-'),
                            'type' => 1,
                            'type_book' => 1,
                            'code' => $isbn,
                            'code_type' => 1,
                            'code_kdt' => 1,
                            'access' => 2,
                            'city_id' => $publisher->city_id,
                            'manual' => 1,
                            'preview' => '1-10',
                            'deposit' => GeneralHelper::depositCollection(),
                            'copyright' => 'Copyrights (c) ' . date('Y') . ' ' . $publisher->name,
                            'status' => 4,
                            'deposit_head_id' => 1,
                            'created_by' => session('id'),
                            'updated_by' => session('id'),
                        ]);

                        return response()->json('Koleksi berhasil di upload');
                    }

                    $collection = Collection::where('code', $isbn)
                        ->where('code_type', 1)
                        ->where('type', 1)
                        ->first();

                    if ($collection) {
                        if ($collection->status == 1 || $collection->status == 2 || $collection->status == 3) {

                            $parent = Collection::create([
                                'publisher_id' => $publisher->id,
                                'title' => 'ISBN SUDAH PERNAH DIUNGGAH',
                                'slug' => Str::slug('ISBN SUDAH PERNAH DIUNGGAH', '-'),
                                'type' => 1,
                                'type_book' => 1,
                                'code' => $isbn,
                                'code_type' => 1,
                                'code_kdt' => 1,
                                'access' => 2,
                                'city_id' => $publisher->city_id,
                                'manual' => 1,
                                'preview' => '1-10',
                                'deposit' => GeneralHelper::depositCollection(),
                                'copyright' => 'Copyrights (c) ' . date('Y') . ' ' . $publisher->name,
                                'status' => 4,
                                'deposit_head_id' => 1,
                                'created_by' => session('id'),
                                'updated_by' => session('id'),
                            ]);

                            return response()->json('Koleksi berhasil di upload');
                        }

                        if ($collection->status == 4) {
                            if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') {
                                $cover = $collection->collectionMedia->where('type', 1)->first();
                                if ($cover) {
                                    Storage::disk('local')->delete($cover->link);
                                    CollectionMedia::where('id', $cover->id)->forceDelete();
                                }


                                $this->uploadCover($collection, $fileUpload);
                            } else {
                                $file = $collection->collectionMedia->where('type', 2)->first();
                                if ($file) {
                                    Storage::disk('local')->delete($file->link);
                                    CollectionMedia::where('id', $file->id)->forceDelete();
                                }

                                $this->uploadOrignial($collection, $fileUpload);
                            }
                        }

                        return response()->json('Koleksi berhasil di upload');
                    } else {
                        $title = $VIsbnLengkap["title"];

                        $typeBook = null;
                        if ($VIsbnLengkap["jenis"] == 'elek') {
                            $typeBook = 1;
                        } else if ($VIsbnLengkap["jenis"] == 'cetak') {
                            $typeBook = 2;
                        }

                        $parent = Collection::create([
                            'publisher_id' => $publisher->id,
                            'title' => $title,
                            'title_ori' => $title,
                            'slug' => Str::slug($title, '-'),
                            'type' => 1,
                            'type_book' => $typeBook,
                            'code' => $isbn,
                            'publication_year' => $VIsbnLengkap["tahun_terbit"],
                            'code_type' => 1,
                            'code_kdt' => 1,
                            'access' => 2,
                            'city_id' => $publisher->city_id,
                            'description' => isset($VIsbnLengkap["sinopsis"]) ? $VIsbnLengkap["sinopsis"] : "",
                            'ddc' => isset($VIsbnLengkap["call_number"]) ? $VIsbnLengkap["call_number"] : "",
                            'manual' => 1,
                            'preview' => '1-10',
                            'deposit' => GeneralHelper::depositCollection(),
                            'copyright' => 'Copyrights (c) ' . date('Y') . ' ' . $publisher->name,
                            'status' => 4,
                            'deposit_head_id' => 1,
                            'created_by' => session('id'),
                            'updated_by' => session('id'),
                        ]);

                        $kepeng = explode(';', $VIsbnLengkap["kepeng"]);

                        CollectionContributor::where('collection_id', $parent->id)->delete();

                        foreach ($kepeng as $value) {

                            $slice = explode(',', $value);

                            $contributor = Contributor::updateOrCreate([
                                'slug' => Str::slug(trim($slice[0]), '-'),
                                'type' => 1,
                            ], [
                                'name' => trim($slice[0]),
                                'slug' => Str::slug(trim($slice[0]), '-'),
                                'type' => 1,
                            ]);

                            $authorName = isset($slice[1]) ? $slice[1] : 'penulis';

                            $author = Author::updateOrCreate([
                                'slug' => Str::slug(trim($authorName), '-'),
                            ], [
                                'title' => '-',
                                'fullname' => trim($authorName),
                                'year_of_birth' => null,
                                'year_of_death' => null,
                            ]);

                            CollectionContributor::create([
                                'collection_id' => $parent->id,
                                'contributor_id' => $contributor->id,
                                'author_id' => $author->id,
                            ]);
                        }

                        if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') {
                            $this->uploadCover($parent, $fileUpload);
                        } else if ($extension == 'pdf' || $extension == 'epub' || $extension == 'mp3') {
                            $this->uploadOrignial($parent, $fileUpload);
                        } else {
                            activity('collections')
                                ->causedBy(session('id'))
                                ->withProperties([
                                    'error' => "Gagal unggah. Extension file : " . $extension . ' tidak didukung.',
                                ])
                                ->log('Gagal Mengunggah File Koleksi / Cover dari unggah isbn');
                            return response()->json("Gagal unggah. Extension file : " . $extension . ' tidak didukung.');
                        }

                        return response()->json('Koleksi berhasil di upload');
                    }
                }
            }
            activity('collections')
                ->causedBy(session('id'))
                ->withProperties([
                    'error' => 'File tidak dapat di upload. File tidak terbaca / tidak ada.',
                ])
                ->log('Gagal Mengunggah File Koleksi / Cover dari unggah isbn');

            return response()->json('File tidak dapat di upload, ukuran file minimum adalah 500KB', 500);
        } catch (\Exception $e) {
            activity('collections')
                ->causedBy(session('id'))
                ->withProperties([
                    'error' => $e->getMessage(),
                ])
                ->log('Gagal Mengunggah File Koleksi / Cover dari unggah isbn');
            return response()->json($e->getMessage(), 500);
        }
    }

    private function uploadCover($collection, $fileUpload)
    {

        try {

            CollectionMedia::where('collection_id', $collection->id)->where('type', 1)->delete();

            $link_collection_cover = Storage::disk($this->location->location)->put('public/collection/book/cover/' . $collection->id, $fileUpload);


            CollectionMedia::insert([
                'collection_id' => $collection->id,
                'link' => $link_collection_cover,
                'size' => File::size($fileUpload),
                'extension' => $fileUpload->getClientOriginalExtension(),
                'mimes' => File::mimeType($fileUpload),
                'hash' => md5_file($fileUpload),
                'type' => 1,
                'method' => 3,
                'status' => 1,
                'location_id' => $this->location->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            activity('collections')
                ->causedBy(session('id'))
                ->withProperties([
                    'error' => $e->getMessage(),
                ])
                ->log('Gagal Mengunggah File Koleksi / Cover dari unggah isbn : ' . $collection->id);
        }
    }

    private function uploadOrignial($collection, $fileUpload)
    {
        try {

            CollectionMedia::where('collection_id', $collection->id)->where('type', 2)->delete();
            $dir_original = Storage::disk($this->location->location)->put('public/collection/book/original/' . $collection->id, $fileUpload);

            CollectionMedia::insert([
                'collection_id' => $collection->id,
                'link' => $dir_original,
                'size' => File::size($fileUpload),
                'extension' => $fileUpload->getClientOriginalExtension(),
                'mimes' => File::mimeType($fileUpload),
                'hash' => md5_file($fileUpload),
                'type' => 2,
                'method' => 3,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            activity('collections')
                ->causedBy(session('id'))
                ->withProperties([
                    'error' => $e->getMessage(),
                ])
                ->log('Gagal Mengunggah File Koleksi / Cover dari unggah isbn : ' . $collection->id);
        }
    }

    public function datatable(Request $request)
    {
        $whereLike = [
            'publisher_id',
            'title',
            'code',
            'created_at',
        ];

        $start = $request->input('start');
        $length = $request->input('length');
        $search = $request->input('search.value');

        $user = User::find(session('id'));
        $publisher_group = $user->publisher->getGroups();


        if ($publisher_group) {
            $publisher_id = $publisher_group->groups->pluck('publisher_id');
        } else {
            $publisher_id[0] = $user->publisher->id;
        }

        $model = Collection::whereIn('publisher_id', $publisher_id)
            ->where('status', 4)
            ->where('parent_id', 0)
            ->where(function ($query) use ($request) {
                if ($request->periode_start && $request->periode_end) {
                    $query->whereBetween('updated_at', [$request->periode_start, $request->periode_end]);
                } else if ($request->periode_start) {
                    $query->whereDate('updated_at', '>', $request->periode_start);
                } else if ($request->periode_end) {
                    $query->whereDate('updated_at', '<', $request->periode_end);
                } else {
                    $query->whereNotNull('updated_at');
                }
            })
            ->where(function ($query) use ($request) {
                if ($request->type) {
                    $query->whereIn('type', $request->type);
                }
            });

        $totalData = $model->count();
        if (empty($search)) {
            $totalFiltered = $model->count();
            $queryData = $model->offset($start)
                ->limit($length)
                ->oldest()
                ->get();
        } else {
            $totalFiltered = $model->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
                ->count();
            $queryData = $model->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
                ->offset($start)
                ->limit($length)
                ->oldest()
                ->get();
        }

        $response['data'] = [];
        if ($queryData != false) {
            foreach ($queryData as $val) {

                $cover = $val->collectionMedia->where('type', 1)->first();
                $file = $val->collectionMedia->where('type', 2)->first();

                if ($val->title == 'ISBN TIDAK DITEMUKAN' || $val->title == 'ISBN SUDAH PERNAH DIUNGGAH' || $val->title == 'BATAL TERBIT' || $val->title == 'Data ISBN bukan merupakan ISBN elektronik') {
                    $aksi = '
                    <button type="button" onclick="deleteCollection(' . $val->id . ')" class="btn btn-danger btn-sm"><i class="la la-trash"></i> Hapus</button>';
                } else {
                    $aksi = '<button type="button" onclick="editCollection(' . $val->id . ')" class="btn btn-warning btn-sm"><i class="la la-edit"></i> Edit</button>
                    <button type="button" onclick="deleteCollection(' . $val->id . ')" class="btn btn-danger btn-sm"><i class="la la-trash"></i> Hapus</button>';
                }

                $response['data'][] = [
                    $aksi,
                    $val->title,
                    $val->publisher->name,
                    $val->code ? $val->code : '<i class="la la-times text-danger"></i>',
                    $cover ? '<i class="la la-check text-success"></i>' : '<i class="la la-times text-danger"></i>',
                    $file ? '<i class="la la-check text-success"></i>' : '<i class="la la-times text-danger"></i>',
                    date('d M Y', strtotime($val->created_at)),
                    $val->publication_month ? '<i class="la la-check text-success"></i>' : '<i class="la la-times text-danger"></i>',
                    $val->publication_year ? '<i class="la la-check text-success"></i>' : '<i class="la la-times text-danger"></i>',
                    $val->description ? '<i class="la la-check text-success"></i>' : '<i class="la la-times text-danger"></i>',
                    $val->publisher->city_id ? '<i class="la la-check text-success"></i>' : '<i class="la la-times text-danger"></i> Lengkapi Profil Anda!',
                    $val->preview,
                    $val->access,
                ];
            }
        }

        $response['recordsTotal'] = 0;
        if ($totalData != false) {
            $response['recordsTotal'] = $totalData;
        }

        $response['recordsFiltered'] = 0;
        if ($totalFiltered != false) {
            $response['recordsFiltered'] = $totalFiltered;
        }

        return response()->json($response);
    }

    public function find($id)
    {
        $collection = Collection::find($id);
        $category = Category::where('type', $collection->type)->get();
        $contributor = Contributor::where('type', $collection->type)->get();
        $publisher = User::find(session('id'))->publisher;

        $data = [
            'title' => 'Update Koleksi',
            'collection' => $collection,
        ];

        return view('publisher.book.review_isbn', compact('data', 'collection', 'category', 'contributor', 'publisher'));
    }

    public function update(Request $request, $id)
    {

        $physical_description = [
            'total_page' => $request->total_page,
            'ilustration' => $request->ilustration,
        ];


        $collection = Collection::where('id', $id)
            ->first();

        if (!$collection) {
            return response()->json(['code' => 400], 400);
        }

        $collection->update([
            'title' => $request->title,
            'physical_description' => json_encode($physical_description),
            'album' => $request->album,
            'slug' => Str::slug($request->title, '-'),
            'access' => $request->access,
            'publication_year' => $request->publication_year,
            'publication_month' => isset($request->publication_month) ? $request->publication_month : 0,
            'edition' => $request->edition,
            'series' => $request->series,
            'serial' => $request->serial,
            'volume' => $request->volume,
            'preview' => $request->preview,
            'description' => $request->description,
            'status' => 4,
            'updated_by' => session('id'),
        ]);

        if ($collection) {
            CollectionCategory::where('collection_id', $id)->delete();
            CollectionContributor::where('collection_id', $id)->delete();
            CollectionSubject::where('collection_id', $id)->delete();

            if ($request->has('category')) {
                foreach ($request->category as $cc) {
                    CollectionCategory::create([
                        'collection_id' => $id,
                        'category_id' => $cc,
                    ]);
                }
            }

            if ($request->has('contributor_contributor_id_field')) {
                foreach ($request->contributor_contributor_id_field as $key => $ccid) {
                    $name = $request->contributor_fullname_field[$key];
                    $title = $request->contributor_title_field[$key];

                    if (!empty($name) && !empty($title)) {
                        $authorCheck = Author::updateOrCreate([
                            'fullname' => $name,
                            'title' => $title,
                            'slug' => Str::slug($name, '-'),
                        ], [
                            'year_of_birth' => $request->contributor_year_of_birth_field[$key],
                            'year_of_death' => $request->contributor_year_of_death_field[$key],
                        ]);

                        $author = Author::where('fullname', $name)
                            ->where('title', $title)
                            ->where('slug', Str::slug($name, '-'))
                            ->where('year_of_birth', $request->contributor_year_of_birth_field[$key])
                            ->where('year_of_death', $request->contributor_year_of_death_field[$key])
                            ->first();

                        CollectionContributor::create([
                            'collection_id' => $id,
                            'contributor_id' => $ccid,
                            'author_id' => $author->id,
                        ]);
                    }
                }
            }

            if ($request->has('collection_subject')) {
                foreach ($request->collection_subject as $cs) {
                    $subjectCheck = Subject::updateOrCreate([
                        'slug' => Str::slug($cs, '-'),
                    ], [
                        'name' => $cs,
                    ]);

                    $subject = Subject::where('name', $cs)
                        ->where('slug', Str::slug($cs, '-'))
                        ->first();

                    CollectionSubject::create([
                        'collection_id' => $id,
                        'subject_id' => $subject->id,
                    ]);
                }
            }
        }

        return response()->json(['message' => 'success']);
    }

    public function submit()
    {
        $totalSuccess = 0;
        $totalFailed = 0;

        $collection = Collection::where('created_by', session('id'))
            ->where('status', 4)
            ->whereNotNull('description')
            ->whereNotNull('preview')
            ->whereNotNull('access')
            ->whereNotNull('title')
            ->whereNotNull('code')
            ->whereNotNull('publication_month')
            ->whereNotNull('publication_year')
            ->where('title', '<>', 'ISBN TIDAK DITEMUKAN')
            ->where('title', '<>', 'ISBN SUDAH PERNAH DIUNGGAH')
            ->where('title', '<>', 'BATAL TERBIT')
            ->get();

        foreach ($collection as $val) {
            try {
                $cover = CollectionMedia::where('collection_id', $val->id)->where('type', 1)->first();
                $file = CollectionMedia::where('collection_id', $val->id)->where('type', 2)->first();

                $publisher = Publisher::find($val->publisher_id);

                if ($cover != null && $file != null) {
                    if ($file->extension == "pdf") {
                        $job = new PDFToImage('book', $file->link, $val->id);
                        dispatch(($job)->onQueue('convert_pdf'));
                    }
                    $update = Collection::find($val->id);
                    $update->city_id = $publisher->city_id;
                    $update->status = 1;
                    $update->save();

                    $totalSuccess += 1;

                    activity('collections')
                        ->performedOn($update)
                        ->causedBy(session('id'))
                        ->withProperties([
                            'penerbit' => $update->publisher->name,
                            'judul' => $update->title,
                            'deskripsi_fisik' => $update->physical_description,
                            'tipe' => $update->type(),
                            'album' => $update->album,
                            'tipe_buku' => $update->typeBook(),
                            'edisi' => $update->edition,
                            'kode' => $update->code,
                            'tipe_kode' => $update->codeType(),
                            'kode_kdt' => $update->code_kdt,
                            'bulan_terbit' => $update->publication_month,
                            'tahun_terbit' => $update->publication_year,
                            'seri' => $update->series,
                            'serial' => $update->serial,
                            'ddc' => $update->ddc,
                            'volume' => $update->volume,
                            'preview' => $update->preview,
                            'description' => $update->description,
                            'deposit' => $update->deposit,
                            'copyright' => $update->copyright,
                            'akses' => $update->access,
                            'status' => $update->status(),
                            'dibuat_oleh' => $update->createdBy->username,
                            'method' => "unggah ISBN",
                        ])
                        ->log('Menambah data koleksi');
                }
            } catch (\Exception $e) {
                $totalFailed += 1;
            }
        }

        return response()->json(['message' => count($collection) . ' koleksi diunggah. ' . $totalSuccess . ' berhasil ' . $totalFailed . ' gagal.']);
    }

    public function delete($id)
    {

        $collection = Collection::where('id', $id)
            ->where('status', 4)
            ->first();

        $media = $collection->collectionMedia;
        foreach ($media as $m) {
            if (Storage::disk($m->location->location)->exists($m->link)) {
                Storage::disk($m->location->location)->delete($m->link);
            }
        }
        CollectionMedia::where('collection_id', $id)->delete();

        $collection->delete();

        return response()->json(['message' => 'success']);
    }
}
