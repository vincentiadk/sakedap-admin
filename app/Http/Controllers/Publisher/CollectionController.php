<?php

namespace App\Http\Controllers\Publisher;

use DB;
use App\Models\Solr;
use App\Models\User;
use App\Models\Author;
use App\Models\Location;
use App\Jobs\PDFToImage;
use App\Models\Publisher;
use App\Models\Collection;
use App\Models\Contributor;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helper\GeneralHelper;
use setasign\Fpdi\Tcpdf\Fpdi;
use App\Models\CollectionMedia;
use App\Models\CollectionSubject;
use App\Models\CollectionCategory;
use App\Http\Controllers\Controller;
use App\Models\CollectionContributor;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Jobs\SendMailCollectionSubmitted;
use Illuminate\Support\Facades\Validator;

class CollectionController extends Controller
{
    protected $location;

    public function __construct()
    {
        $this->location = Location::where('active', 1)->first();
    }

    public function createManual(Request $request)
    {

        if ($request->ajax()) {
            return $this->createCollection($request);
        }

        $user = User::find(session('id'));
        // $publisher = User::find(session('id'))->publisher;
        //$publisher = Publisher::find(1377);

        $groups = null;
        if ($user->publisher->getGroups()) {
            $groups = $user->publisher->getGroups()->groups;
        }
        if ($groups == null) {
            $publisher_groups = false;
            $publisher_user   = $user->publisher;
        } else {
            $publisher_groups = $groups->where('publisher_id', '!=', $user->publisher->id)->all();
            $publisher_user   = $groups->where('publisher_id', $user->publisher->id)->first();
            array_unshift($publisher_groups, $publisher_user);
        }

        $data = [
            'title'                     => 'Penyerahan Koleksi',
            'content'                   => 'publisher.collection.create',
            'publisher'                 => $user->publisher,
            'publisher_groups'          => $publisher_groups
        ];

        return view('publisher.layout.index', ['data' => $data]);
    }

    private function createCollection(Request $request)
    {

        //update publisher
        $publisher = User::find(session('id'))->publisher;
        //$publisher = Publisher::find(1377);
        $publisher->update([
            'address' => $request->publisher_address,
            'province_id' => $request->publisher_province,
            'city_id' => $request->publisher_city,
            'district_id' => $request->publisher_district,
            'village_id' => $request->publisher_village,
        ]);

        if ($request->type == "1") {
            if ($request->type_of_collection == 'isbn') {

                $validator = Validator::make($request->all(), [
                    'isbn_book' => 'required',
                    'title_book' => 'required',
                    'preview_book' => 'required|regex:/^\d+-\d+$/',
                    'publication_year_book' => 'required|date_format:Y',
                    'file_upload.cover.*.*' => 'required|max:2048|mimes:jpg,jpeg,png',
                    'file_upload.content.*.*' => 'required|max:500000|mimes:pdf,epub,mp3',
                ], [
                    'isbn_book.required' => 'Kode ISBN wajib di isi!',
                    'title_book.required' => 'Judul wajib di isi!',
                    'preview_book.required' => 'Preview wajib di isi!',
                    'preview_book.regex' => 'Format preview tidak benar, hanya gunakan angka!',
                    'publication_year_book.required' => 'Tahun terbit wajib di isi!',
                    'publication_year_book.date_format' => 'Tahun terbit harus berupa tahun!',
                    'file_upload.cover.*.*.required' => 'Cover wajib di isi!',
                    'file_upload.cover.*.*.max' => 'Cover maksimal 2MB!',
                    'file_upload.cover.*.*.mimes' => 'Cover harus bertipe jpg, jpeg, png!',
                    'file_upload.content.*.*.required' => 'File konten wajib di isi!',
                    'file_upload.content.*.*.max' => 'File konten maksimal 500MB!',
                    'file_upload.content.*.*.mimes' => 'File konten harus bertipe pdf, epub atau mp3!',
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'title_book' => 'required',
                    'preview_book' => 'required|regex:/^\d+-\d+$/',
                    'publication_year_book' => 'required|date_format:Y',
                    'file_upload.cover.*.*' => 'required|max:2048|mimes:jpg,jpeg,png',
                    'file_upload.content.*.*' => 'required|max:500000|mimes:pdf,epub,mp3',
                ], [
                    'title_book.required' => 'Judul wajib di isi!',
                    'preview_book.required' => 'Preview wajib di isi!',
                    'preview_book.regex' => 'Format preview tidak benar, hanya gunakan angka!',
                    'publication_year_book.required' => 'Tahun terbit wajib di isi!',
                    'publication_year_book.date_format' => 'Tahun terbit harus berupa tahun!',
                    'file_upload.cover.*.*.required' => 'Cover wajib di isi!',
                    'file_upload.cover.*.*.max' => 'Cover maksimal 2MB!',
                    'file_upload.cover.*.*.mimes' => 'Cover harus bertipe jpg, jpeg, png!',
                    'file_upload.content.*.*.required' => 'File konten wajib di isi!',
                    'file_upload.content.*.*.max' => 'File konten maksimal 500MB!',
                    'file_upload.content.*.*.mimes' => 'File konten harus bertipe pdf, epub atau mp3!',
                ]);
            }
        } else if ($request->type == "2") {
            $validator = Validator::make($request->all(), [
                'title_partitur' => 'required',
                'preview_partitur' => 'required',
                'publication_year_partitur' => 'required|date_format:Y',
                'file_upload.cover.*.*' => 'required|max:2048|mimes:jpg,jpeg,png',
                'file_upload.content.*.*' => 'required|max:500000|mimes:pdf',
            ], [
                'title_partitur.required' => 'Judul wajib di isi!',
                'preview_partitur.required' => 'Preview wajib di isi!',
                'publication_year_partitur.required' => 'Tahun terbit wajib di isi!',
                'publication_year_partitur.date_format' => 'Tahun terbit harus berupa tahun!',
                'file_upload.cover.*.*.required' => 'Cover wajib di isi!',
                'file_upload.cover.*.*.max' => 'Cover maksimal 2MB!',
                'file_upload.cover.*.*.mimes' => 'Cover harus bertipe jpg, jpeg, png!',
                'file_upload.content.*.*.required' => 'File konten wajib di isi!',
                'file_upload.content.*.*.max' => 'File konten maksimal 500MB!',
                'file_upload.content.*.*.mimes' => 'File konten harus bertipe pdf!',
            ]);
        } else if ($request->type == "3") {
            $validator = Validator::make($request->all(), [
                'title_map' => 'required',
                'preview_map' => 'required',
                'publication_year_map' => 'required|date_format:Y',
                'file_upload.cover.*.*' => 'required|max:2048|mimes:jpg,jpeg,png',
                'file_upload.content.*.*' => 'required|max:500000|mimes:pdf',
            ], [
                'title_map.required' => 'Judul wajib di isi!',
                'preview_map.required' => 'Preview wajib di isi!',
                'publication_year_map.required' => 'Tahun terbit wajib di isi!',
                'publication_year_map.date_format' => 'Tahun terbit harus berupa tahun!',
                'file_upload.cover.*.*.required' => 'Cover wajib di isi!',
                'file_upload.cover.*.*.max' => 'Cover maksimal 2MB!',
                'file_upload.cover.*.*.mimes' => 'Cover harus bertipe jpg, jpeg, png!',
                'file_upload.content.*.*.required' => 'File konten wajib di isi!',
                'file_upload.content.*.*.max' => 'File konten maksimal 500MB!',
                'file_upload.content.*.*.mimes' => 'File konten harus bertipe pdf!',
            ]);
        } else if ($request->type == "4") {
            $validator = Validator::make($request->all(), [
                'title_serial' => 'required',
                'publication_year_serial' => 'required|date_format:Y',
            ], [
                'title.required' => 'Judul wajib di isi!',
                'publication_year.required' => 'Tahun terbit wajib di isi!',
                'publication_year.date_format' => 'Tahun terbit harus berupa tahun!',
            ]);
        } else if ($request->type == "5") {
            $validator = Validator::make($request->all(), [
                'title_music' => 'required',
                //'preview_start_music'    => 'required|date_format:i:s',
                //'preview_end_music'      => 'required|date_format:i:s',
                'publication_year_music' => 'required|date_format:Y',
                'file_upload.cover.*.*' => 'required|max:2048|mimes:jpg,jpeg,png',
                'file_upload.content.*.*' => 'required|file|max:500000|mimes:mp3,wav',
            ], [
                'title_music.required' => 'Judul wajib di isi!',
                // 'preview_start_music.required'       => 'Preview start wajib di isi!',
                // 'preview_start_music.date_format:i:s' => 'Preview start harus berupa detik sebagai berikut 00:30!',
                // 'preview_end_music.required'         => 'Preview end wajib di isi!',
                // 'preview_end_music.date_format:i:s'      => 'Preview end harus berupa detik sebagai berikut 01:00!',
                'publication_year_music.required' => 'Tahun terbit wajib di isi!',
                'publication_year_music.date_format' => 'Tahun terbit harus berupa tahun!',
                'file_upload.cover.*.*.required' => 'Cover wajib di isi!',
                'file_upload.cover.*.*.max' => 'Cover maksimal 2MB!',
                'file_upload.cover.*.*.mimes' => 'Cover harus bertipe jpg, jpeg, png!',
                'file_upload.content.*.*.required' => 'File konten wajib di isi!',
                'file_upload.content.*.*.max' => 'File konten maksimal 500MB!',
                'file_upload.content.*.*.mimes' => 'File konten harus bertipe MP3 atau WAV!',
            ]);
        } else if ($request->type == "6") {
            $validator = Validator::make($request->all(), [
                'title_video' => 'required',
                // 'preview_start_video'    => 'required|date_format:i:s',
                // 'preview_end_video'      => 'required|date_format:i:s',
                'publication_year_video' => 'required|date_format:Y',
                'file_upload.cover.*.*' => 'required|max:2048|mimes:jpg,jpeg,png',
                'file_upload.content.*.*' => 'required|file|max:5000000|mimes:mkv,mp4,avi,mpeg,3gp',
            ], [
                'title_video.required' => 'Judul wajib di isi!',
                // 'preview_start_video.required'       => 'Preview start wajib di isi!',
                // 'preview_start_video.date_format:i:s' => 'Preview start harus berupa detik sebagai berikut 00:30!',
                // 'preview_end_video.required'         => 'Preview end wajib di isi!',
                // 'preview_end_video.date_format:i:s'      => 'Preview end harus berupa detik sebagai berikut 01:00!',
                'publication_year_video.required' => 'Tahun terbit wajib di isi!',
                'publication_year_video.date_format' => 'Tahun terbit harus berupa tahun!',
                'file_upload.cover.*.*.required' => 'Cover wajib di isi!',
                'file_upload.cover.*.*.max' => 'Cover maksimal 1MB!',
                'file_upload.cover.*.*.mimes' => 'Cover harus bertipe jpg, jpeg, png!',
                'file_upload.content.*.*.required' => 'File konten wajib di isi!',
                'file_upload.content.*.*.max' => 'File konten maksimal 500MB!',
                'file_upload.content.*.*.mimes' => 'File konten harus bertipe mkv, mp4, avi, mpeg, 3gp!',
            ]);
        }

        if (isset($validator)) {
            if ($validator->fails()) {
                $response = [
                    'status' => 422,
                    'error' => $validator->errors(),
                ];
                return response()->json($response);
            }
        }

        //validation file pdf
        if ($request->type != 4) {
            $messageEncrypted = [];
            if ($request->type == "1" || $request->type == "2" || $request->type == "3") {
                foreach ($request->file('file_upload')['content'] as $value) {
                    if ($value[0]->getClientOriginalExtension() == "pdf") { //jika file pdf
                        try {
                            $pdf = new FPDI();
                            $pdf->setSourceFile($value[0]->getPathName());
                        } catch (\setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException $e) {
                            if ($e->getMessage() == "This PDF document is encrypted and cannot be processed with FPDI.") {
                                $messageEncrypted[] = "File Konten: " . $value[0]->getClientOriginalName() . " terenkripsi.";
                            }
                        }
                    }
                }
            }

            $messageBroken = [];
            foreach ($request->file('file_upload')['content'] as $value) {
                if ($value[0]->getSize() <= 0) {
                    $messageBroken[] = "File Konten: " . $value[0]->getClientOriginalName() . " rusak.";
                }
            }

            foreach ($request->file('file_upload')['cover'] as $value) {
                if ($value[0]->getSize() <= 0) {
                    $messageBroken[] = "File Cover: " . $value[0]->getClientOriginalName() . " rusak.";
                }
            }

            if (count($messageEncrypted) > 0 || count($messageBroken) > 0) {
                $response = [
                    'status' => 422,
                    'error' => array_merge($messageEncrypted, $messageBroken),
                ];
                return response()->json($response);
            }
        }

        try {
            if ($request->type == "1") {
                return $this->createBookCollection($request);
            } else if ($request->type == "2") {
                return $this->createPartiturCollection($request);
            } else if ($request->type == "3") {
                return $this->createMapCollection($request);
            } else if ($request->type == "5") {
                return $this->createAudioCollection($request);
            } else if ($request->type == "6") {
                return $this->createVideoCollection($request);
            } else if ($request->type == "4") {
                return $this->createSerialCollection($request);
            }
        } catch (\Exception $e) {
            $data[] = $e->getMessage();
            $response = [
                'status' => 422,
                'error' => $data,
            ];
            return response()->json($response);
        }
    }

    private function createBookCollection(Request $request)
    {
        \Log::info("create book called");
        $publisher = User::find(session('id'))->publisher;

        $dataIsbn = null;

        if ($request->type_of_collection == 'isbn') {
            $publisher_code = $publisher->code_system;

            $dataIsbn = GeneralHelper::getDetailIsbn($request->isbn_book);

            if (! str_contains($dataIsbn[0]["Judul"], "elektronis]")) {
                $response = [
                    'status' => 422,
                    'error' => ['Data ISBN bukan merupakan ISBN elektronik'],
                ];
                return response()->json($response);
            }

            $collection = Collection::where('code', $request->isbn_book)
                ->where('type', 1)
                ->where('code_type', 1)
                ->where('publisher_id', $publisher->id)
                ->first();

            if ($collection) {
                $response = [
                    'status' => 422,
                    'error' => ['ISBN tersebut sudah diinput sebelumnya'],
                ];
                return response()->json($response);
            }

            $code = $request->isbn_book;
            $code_kdt = "";
            $code_type = 1;
            $ddc = "";
        } else {
            $code = null;
            $code_kdt = null;
            $code_type = null;
            $ddc = null;
        }

        $type_book = 1;

        $physical_description = [
            'total_page' => $request->page_book,
            'dimension' => $request->thickness_book,
        ];

        if ($dataIsbn) {
            $publisherOnGroup = Publisher::checkGroupPublisher($publisher, $dataIsbn[0]["Penerbit"]);
        } else {
            $publisherOnGroup = $publisher;
        }


        $parent = Collection::create([
            'publisher_id' => $publisherOnGroup->id,
            'title' => $request->title_book,
            'title_ori' => $request->title_ori_book,
            'slug' => Str::slug($request->title_book, '-'),
            'type' => 1,
            'type_book' => $type_book,
            'ddc' => $ddc,
            'kepeng' => $request->author_book,
            'edition' => $request->edition_book,
            'code' => $code,
            'code_type' => 1,
            'code_kdt' => $code_kdt,
            'publication_year' => $request->publication_year_book,
            'publication_month' => $request->publication_month_book,
            'preview' => $request->preview_book,
            'description' => $request->description,
            'access' => $request->access,
            'city_id' => $publisherOnGroup->city_id,
            'physical_description' => json_encode($physical_description),
            'manual' => 1,
            'deposit' => GeneralHelper::depositCollection(),
            'copyright' => 'Copyrights (c) ' . date('Y') . ' ' . $publisherOnGroup->name,
            'status' => 1,
            'created_by' => session('id'),
            'updated_by' => session('id'),
        ]);

        if ($parent) {

            try {

                if ($request->type_of_collection == 'isbn') {
                    $cover = $request->file('file_upload')['cover'][$request->isbn_book];
                    $original = $request->file('file_upload')['content'][$request->isbn_book];
                    $this->uploadCollection($parent->id, $cover[0], $original[0], 'book');
                } else {
                    $cover = $request->file('file_upload')['cover'][0];
                    $original = $request->file('file_upload')['content'][0];
                    $this->uploadCollection($parent->id, $cover[0], $original[0], 'book');
                }

                //upload media collection
                if (count($request->file('file_upload')['cover']) > 1) {

                    foreach ($request->file('file_upload')['cover'] as $key => $cover) {
                        if ($key != 0) {
                            $child = Collection::create([
                                'parent_id' => $parent->id,
                                'publisher_id' => $publisher->id,
                                'title' => $request->title_book,
                                'slug' => Str::slug($request->title_book, '-'),
                                'type' => 1,
                                'type_book' => $type_book,
                                'kepeng' => $request->author_book,
                                'edition' => $request->edition_book,
                                'code' => $key,
                                'code_type' => 1,
                                'code_kdt' => $code_kdt,
                                'publication_year' => $request->publication_year_book,
                                'publication_month' => $request->publication_month_book,
                                'preview' => $request->preview_book,
                                'description' => $request->description,
                                'access' => $request->access,
                                'city_id' => $publisher->city_id,
                                'manual' => 1,
                                'deposit' => GeneralHelper::depositCollection(),
                                'copyright' => 'Copyrights (c) ' . date('Y') . ' ' . $publisher->name,
                                'status' => 1,
                                'created_by' => session('id'),
                                'updated_by' => session('id'),
                            ]);

                            $original = $request->file('file_upload')['content'][$key];
                            $this->uploadCollection($child->id, $cover[0], $original[0], 'book');
                        }
                    }
                }

                $this->createAdditonalInfo($request, $parent->id);

                $collection = Collection::find($parent->id);
                $params = [
                    'user_id' => session('id'),
                    'publisher' => $collection->publisher->name,
                    'title' => $collection->title,
                ];

                $job = new SendMailCollectionSubmitted($params);
                dispatch(($job)->onQueue('notification'));

                activity('collections')
                    ->performedOn($collection)
                    ->causedBy(session('id'))
                    ->withProperties([
                        'penerbit' => $collection->publisher->name,
                        'judul' => $collection->title,
                        'deskripsi_fisik' => $collection->physical_description,
                        'tipe' => $collection->type(),
                        'album' => $collection->album,
                        'tipe_buku' => $collection->typeBook(),
                        'edisi' => $collection->edition,
                        'kode' => $collection->code,
                        'tipe_kode' => $collection->codeType(),
                        'kode_kdt' => $collection->code_kdt,
                        'bulan_terbit' => $collection->publication_month,
                        'tahun_terbit' => $collection->publication_year,
                        'seri' => $collection->series,
                        'serial' => $collection->serial,
                        'ddc' => $collection->ddc,
                        'volume' => $collection->volume,
                        'preview' => $collection->preview,
                        'description' => $collection->description,
                        'deposit' => $collection->deposit,
                        'copyright' => $collection->copyright,
                        'akses' => $collection->access,
                        'status' => $collection->status(),
                        'dibuat_oleh' => $collection->createdBy->username,
                        'method' => 'unggah mandiri',
                    ])
                    ->log('Menambah data koleksi.');

                session()->flash('success', 'Berhasil ditambahkan!');
                $response = ['status' => 200, 'type' => $request->type, 'id' => $parent->id];
            } catch (\Exception $e) {
                \Log::debug('error create collection: ' . $e->getMessage());
                session()->flash('failed', 'Gagal Menyimpan data, Mohon lengkapi data kembali!' . $e->getMessage());
                $response = [
                    'status' => 302,
                    'data' => $parent,
                    'message' => 'Gagal ditambahkan',
                ];
                $data[] = $e->getMessage();
            }
        } else {
            session()->flash('failed', 'Gagal ditambahkan!');
            $response = [
                'status' => 500,
                'message' => 'Gagal ditambahkan tidak bisa upload koleksi',
            ];
        }

        return response()->json($response);
    }

    private function createPartiturCollection(Request $request)
    {

        if ($request->type_of_collection == 'ismn') {
            $code = $request->code_partitur;
            $code_type = 2;
        } else {
            $code = null;
            $code_type = null;
        }

        // $publisher    = Publisher::find(session('id'));
        $publisher = User::find(session('id'))->publisher;
        $parent = Collection::create([
            'publisher_id' => $publisher->id,
            'city_id' => $publisher->city_id,
            'title' => $request->title_partitur,
            'slug' => Str::slug($request->title_partitur, '-'),
            'type' => 2,
            'code' => $code,
            'code_type' => $code_type,
            'publication_year' => $request->publication_year_partitur,
            'publication_month' => $request->publication_month_partitur,
            'preview' => $request->preview_partitur,
            'description' => $request->description_partitur,
            'access' => $request->access,
            'manual' => 1,
            'deposit' => GeneralHelper::depositCollection(),
            'copyright' => 'Copyrights (c) ' . date('Y') . ' ' . $publisher->name,
            'status' => 1,
            'created_by' => session('id'),
            'updated_by' => session('id'),
        ]);

        if ($parent) {

            try {
                foreach ($request->file('file_upload')['cover'] as $key => $cover) {
                    $original = $request->file('file_upload')['content'][$key];
                    $this->uploadCollection($parent->id, $cover[0], $original[0], 'partitur');
                }

                $this->createAdditonalInfo($request, $parent->id);

                $collection = Collection::find($parent->id);
                $params = [
                    'user_id' => session('id'),
                    'publisher' => $collection->publisher->name,
                    'title' => $collection->title,
                ];

                $job = new SendMailCollectionSubmitted($params);
                dispatch(($job)->onQueue('notification'));

                activity('collections')
                    ->performedOn($collection)
                    ->causedBy(session('id'))
                    ->withProperties([
                        'penerbit' => $collection->publisher->name,
                        'judul' => $collection->title,
                        'deskripsi_fisik' => $collection->physical_description,
                        'tipe' => $collection->type(),
                        'album' => $collection->album,
                        'tipe_buku' => $collection->typeBook(),
                        'edisi' => $collection->edition,
                        'kode' => $collection->code,
                        'tipe_kode' => $collection->codeType(),
                        'kode_kdt' => $collection->code_kdt,
                        'bulan_terbit' => $collection->publication_month,
                        'tahun_terbit' => $collection->publication_year,
                        'seri' => $collection->series,
                        'serial' => $collection->serial,
                        'ddc' => $collection->ddc,
                        'volume' => $collection->volume,
                        'preview' => $collection->preview,
                        'description' => $collection->description,
                        'deposit' => $collection->deposit,
                        'copyright' => $collection->copyright,
                        'akses' => $collection->access,
                        'status' => $collection->status(),
                        'dibuat_oleh' => $collection->createdBy->username,
                        'method' => 'unggah mandiri',
                    ])
                    ->log('Menambah data koleksi.');

                session()->flash('success', 'Berhasil ditambahkan!');
                $response = ['status' => 200, 'type' => $request->type, 'id' => $parent->id];
            } catch (\Exception $e) {
                session()->flash('failed', 'Mohon lengkapi data!');
                $response = [
                    'status' => 302,
                    'data' => $parent,
                    'message' => 'Gagal ditambahkan',
                ];
            }
        } else {
            session()->flash('failed', 'Gagal ditambahkan!');
            $response = [
                'status' => 500,
                'message' => 'Gagal ditambahkan',
            ];
        }

        return response()->json($response);
    }

    private function createMapCollection(Request $request)
    {

        if ($request->type_of_collection == 'isbn') {
            $code = $request->code_map;
            $code_type = 1;
        } else {
            $code = null;
            $code_type = null;
        }

        $publisher = User::find(session('id'))->publisher;
        //$publisher    = Publisher::find(1377);
        $physical_description = [
            'scale' => $request->scala_map,
        ];
        $parent = Collection::create([
            'publisher_id' => $publisher->id,
            'city_id' => $publisher->city_id,
            'title' => $request->title_map,
            'slug' => Str::slug($request->title_map, '-'),
            'type' => 3,
            'code' => $code,
            'code_type' => $code_type,
            'publication_year' => $request->publication_year_map,
            'publication_month' => $request->publication_month_map,
            'preview' => $request->preview_map,
            'description' => $request->description_map,
            'access' => $request->access,
            'physical_description' => json_encode($physical_description),
            'manual' => 1,
            'deposit' => GeneralHelper::depositCollection(),
            'copyright' => 'Copyrights (c) ' . date('Y') . ' ' . $publisher->name,
            'status' => 1,
            'created_by' => session('id'),
            'updated_by' => session('id'),
        ]);

        if ($parent) {

            try {

                foreach ($request->file('file_upload')['cover'] as $key => $cover) {
                    $original = $request->file('file_upload')['content'][$key];
                    $this->uploadCollection($parent->id, $cover[0], $original[0], 'map');
                }

                $this->createAdditonalInfo($request, $parent->id);

                $collection = Collection::find($parent->id);
                $params = [
                    'user_id' => session('id'),
                    'publisher' => $collection->publisher->name,
                    'title' => $collection->title,
                ];

                $job = new SendMailCollectionSubmitted($params);
                dispatch(($job)->onQueue('notification'));

                activity('collections')
                    ->performedOn($collection)
                    ->causedBy(session('id'))
                    ->withProperties([
                        'penerbit' => $collection->publisher->name,
                        'judul' => $collection->title,
                        'deskripsi_fisik' => $collection->physical_description,
                        'tipe' => $collection->type(),
                        'album' => $collection->album,
                        'tipe_buku' => $collection->typeBook(),
                        'edisi' => $collection->edition,
                        'kode' => $collection->code,
                        'tipe_kode' => $collection->codeType(),
                        'kode_kdt' => $collection->code_kdt,
                        'bulan_terbit' => $collection->publication_month,
                        'tahun_terbit' => $collection->publication_year,
                        'seri' => $collection->series,
                        'serial' => $collection->serial,
                        'ddc' => $collection->ddc,
                        'volume' => $collection->volume,
                        'preview' => $collection->preview,
                        'description' => $collection->description,
                        'deposit' => $collection->deposit,
                        'copyright' => $collection->copyright,
                        'akses' => $collection->access,
                        'status' => $collection->status(),
                        'dibuat_oleh' => $collection->createdBy->username,
                        'method' => 'unggah mandiri',
                    ])
                    ->log('Menambah data koleksi.');

                session()->flash('success', 'Berhasil ditambahkan!');
                $response = ['status' => 200, 'type' => $request->type, 'id' => $parent->id];
            } catch (\Exception $e) {
                session()->flash('failed', 'Mohon lengkapi data!');
                $response = [
                    'status' => 302,
                    'data' => $parent,
                    'message' => 'Gagal ditambahkan',
                ];
            }
        } else {
            session()->flash('failed', 'Gagal ditambahkan!');
            $response = [
                'status' => 500,
                'message' => 'Gagal ditambahkan',
            ];
        }

        return response()->json($response);
    }

    private function createAudioCollection(Request $request)
    {

        if ($request->type_of_collection == 'isrc') {
            $code = $request->code_music;
            $code_type = 3;
        } else {
            $code = null;
            $code_type = null;
        }

        $publisher = User::find(session('id'))->publisher;
        //$publisher    = Publisher::find(1377);
        $physical_description = [
            'duration' => $request->duration_music,
        ];

        //format slider preview
        $preview = explode(',', $request->preview_music);
        $startMins = floor($preview[0] / 60 % 60);
        $startSecs = floor($preview[0] % 60);

        $endMins = floor($preview[1] / 60 % 60);
        $endSecs = floor($preview[1] % 60);

        $preview_music = sprintf('%02d:%02d-%02d:%02d', $startMins, $startSecs, $endMins, $endSecs);

        $parent = Collection::create([
            'publisher_id' => $publisher->id,
            'city_id' => $publisher->city_id,
            'title' => $request->title_music,
            'slug' => Str::slug($request->title_music, '-'),
            'type' => 5,
            'album' => $request->album_music,
            'code' => $code,
            'code_type' => $code_type,
            'publication_year' => $request->publication_year_music,
            'publication_month' => $request->publication_month_music,
            'preview' => $preview_music,
            'description' => $request->description_music,
            'access' => $request->access,
            'manual' => 1,
            'deposit' => GeneralHelper::depositCollection(),
            'copyright' => 'Copyrights (c) ' . date('Y') . ' ' . $publisher->name,
            'status' => 1,
            'created_by' => session('id'),
            'updated_by' => session('id'),
            'physical_description' => json_encode($physical_description),

        ]);

        if ($parent) {

            try {

                foreach ($request->file('file_upload')['cover'] as $key => $cover) {
                    $original = $request->file('file_upload')['content'][$key];
                    $this->uploadCollection($parent->id, $cover[0], $original[0], 'audio');
                }

                $this->createAdditonalInfo($request, $parent->id);

                $collection = Collection::find($parent->id);
                $params = [
                    'user_id' => session('id'),
                    'publisher' => $collection->publisher->name,
                    'title' => $collection->title,
                ];

                $job = new SendMailCollectionSubmitted($params);
                dispatch(($job)->onQueue('notification'));

                activity('collections')
                    ->performedOn($collection)
                    ->causedBy(session('id'))
                    ->withProperties([
                        'penerbit' => $collection->publisher->name,
                        'judul' => $collection->title,
                        'deskripsi_fisik' => $collection->physical_description,
                        'tipe' => $collection->type(),
                        'album' => $collection->album,
                        'tipe_buku' => $collection->typeBook(),
                        'edisi' => $collection->edition,
                        'kode' => $collection->code,
                        'tipe_kode' => $collection->codeType(),
                        'kode_kdt' => $collection->code_kdt,
                        'bulan_terbit' => $collection->publication_month,
                        'tahun_terbit' => $collection->publication_year,
                        'seri' => $collection->series,
                        'serial' => $collection->serial,
                        'ddc' => $collection->ddc,
                        'volume' => $collection->volume,
                        'preview' => $collection->preview,
                        'description' => $collection->description,
                        'deposit' => $collection->deposit,
                        'copyright' => $collection->copyright,
                        'akses' => $collection->access,
                        'status' => $collection->status(),
                        'dibuat_oleh' => $collection->createdBy->username,
                        'method' => 'unggah mandiri',
                    ])
                    ->log('Menambah data koleksi.');

                session()->flash('success', 'Berhasil ditambahkan!');
                $response = ['status' => 200, 'type' => $request->type, 'id' => $parent->id];
            } catch (\Exception $e) {
                session()->flash('failed', 'Mohon lengkapi data!');
                $response = [
                    'status' => 302,
                    'data' => $parent,
                    'message' => 'Gagal ditambahkan',
                ];
            }
        } else {
            session()->flash('failed', 'Gagal ditambahkan!');
            $response = [
                'status' => 500,
                'message' => 'Gagal ditambahkan',
            ];
        }

        return response()->json($response);
    }

    private function createVideoCollection(Request $request)
    {

        if ($request->type_of_collection == 'isan') {
            $code = $request->code_video;
            $code_type = 5;
        } else {
            $code = null;
            $code_type = null;
        }

        $publisher = User::find(session('id'))->publisher;
        //$publisher    = Publisher::find(1377);
        $physical_description = [
            'duration' => $request->duration_video,
        ];

        $preview = explode(',', $request->preview_video);
        $startMins = floor($preview[0] / 60 % 60);
        $startSecs = floor($preview[0] % 60);

        $endMins = floor($preview[1] / 60 % 60);
        $endSecs = floor($preview[1] % 60);

        $preview_video = sprintf('%02d:%02d-%02d:%02d', $startMins, $startSecs, $endMins, $endSecs);
        $parent = Collection::create([
            'publisher_id' => $publisher->id,
            'city_id' => $publisher->city_id,
            'title' => $request->title_video,
            'slug' => Str::slug($request->title_video, '-'),
            'type' => 6,
            'album' => $request->album_video,
            'code' => $code,
            'code_type' => $code_type,
            'publication_year' => $request->publication_year_video,
            'publication_month' => $request->publication_month_video,
            'preview' => $preview_video,
            'description' => $request->description_video,
            'access' => $request->access,
            'manual' => 1,
            'deposit' => GeneralHelper::depositCollection(),
            'copyright' => 'Copyrights (c) ' . date('Y') . ' ' . $publisher->name,
            'status' => 1,
            'created_by' => session('id'),
            'updated_by' => session('id'),
        ]);

        if ($parent) {

            try {

                foreach ($request->file('file_upload')['cover'] as $key => $cover) {
                    $original = $request->file('file_upload')['content'][$key];
                    $this->uploadCollection($parent->id, $cover[0], $original[0], 'video');
                }

                $this->createAdditonalInfo($request, $parent->id);

                $collection = Collection::find($parent->id);
                $params = [
                    'user_id' => session('id'),
                    'publisher' => $collection->publisher->name,
                    'title' => $collection->title,
                ];

                $job = new SendMailCollectionSubmitted($params);
                dispatch(($job)->onQueue('notification'));

                activity('collections')
                    ->performedOn($collection)
                    ->causedBy(session('id'))
                    ->withProperties([
                        'penerbit' => $collection->publisher->name,
                        'judul' => $collection->title,
                        'deskripsi_fisik' => $collection->physical_description,
                        'tipe' => $collection->type(),
                        'album' => $collection->album,
                        'tipe_buku' => $collection->typeBook(),
                        'edisi' => $collection->edition,
                        'kode' => $collection->code,
                        'tipe_kode' => $collection->codeType(),
                        'kode_kdt' => $collection->code_kdt,
                        'bulan_terbit' => $collection->publication_month,
                        'tahun_terbit' => $collection->publication_year,
                        'seri' => $collection->series,
                        'serial' => $collection->serial,
                        'ddc' => $collection->ddc,
                        'volume' => $collection->volume,
                        'preview' => $collection->preview,
                        'description' => $collection->description,
                        'deposit' => $collection->deposit,
                        'copyright' => $collection->copyright,
                        'akses' => $collection->access,
                        'status' => $collection->status(),
                        'dibuat_oleh' => $collection->createdBy->username,
                        'method' => 'unggah mandiri',
                    ])
                    ->log('Menambah data koleksi.');

                session()->flash('success', 'Berhasil ditambahkan!');
                $response = ['status' => 200, 'type' => $request->type, 'id' => $parent->id];
            } catch (\Exception $e) {
                session()->flash('failed', 'Mohon lengkapi data!');
                $response = [
                    'status' => 302,
                    'data' => $parent,
                    'message' => 'Gagal ditambahkan',
                ];
            }
        } else {
            session()->flash('failed', 'Gagal ditambahkan!');
            $response = [
                'status' => 500,
                'message' => 'Gagal ditambahkan',
            ];
        }

        return response()->json($response);
    }

    private function createSerialCollection(Request $request)
    {

        $publisher = User::find(session('id'))->publisher;

        $code_type = 4;
        $type_book = null;
        $code = $request->code_serial;
        $code_kdt = null;

        if ($request->id_serial != null) {
            $parent = Collection::find($request->id_serial);
        } else {
            $parent = Collection::create([
                'publisher_id' => $publisher->id,
                'city_id' => $publisher->city_id,
                'title' => $request->title_serial,
                'slug' => Str::slug($request->title_serial, '-'),
                'type' => 4,
                'album' => $request->album_serial,
                'type_book' => $type_book,
                'edition' => $request->edition_serial,
                'code' => $code,
                'code_type' => $code_type,
                'code_kdt' => $code_kdt,
                'publication_year' => $request->publication_year_serial,
                'publication_month' => $request->publication_month_serial,
                'series' => $request->series_serial,
                'serial' => $request->serial,
                'ddc' => $request->ddc_serial,
                'volume' => $request->volume_serial,
                'preview' => $request->preview_serial,
                'description' => $request->description_serial,
                'access' => $request->access,
                'manual' => 1,
                'deposit' => GeneralHelper::depositCollection(),
                'copyright' => 'Copyrights (c) ' . date('Y') . ' ' . $publisher->name,
                'status' => 1,
                'created_by' => session('id'),
                'updated_by' => session('id'),
            ]);
        }

        if ($parent) {

            try {

                $cover = $request->file('file_upload')['cover'][0][0];

                $file_name = Storage::disk($this->location->location)->put('public/collection/serial/cover/' . $parent->id, $cover);
                CollectionMedia::create([
                    'collection_id' => $parent->id,
                    'link' => $file_name,
                    'size' => File::size($cover),
                    'extension' => $cover->getClientOriginalExtension(),
                    'mimes' => File::mimeType($cover),
                    'hash' => md5_file($cover),
                    'type' => 1,
                    'method' => 4,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                if ($request->has('edition_edition_field')) {
                    foreach ($request->edition_edition_field as $key => $eef) {

                        $physical_description = [
                            'total_page' => $request->edition_total_page_field[$key],
                        ];

                        $edition = Collection::create([
                            'parent_id' => $parent->id,
                            'publisher_id' => $publisher->id,
                            'physical_description' => json_encode($physical_description),
                            'type' => 4,
                            'edition' => $eef,
                            'deposit' => GeneralHelper::depositCollection(),
                            'copyright' => 'Copyrights (c) ' . date('Y') . ' ' . $publisher->name,
                            'manual' => 1,
                            'date' => $request->edition_date_field[$key],
                            'preview' => $request->preview_serial,
                            'status' => 1,
                            'created_by' => session('id'),
                            'updated_by' => session('id'),
                        ]);

                        $path_tmp_cover = $request->edition_cover_field[$key];
                        $path_tmp_original = $request->edition_original_field[$key];
                        $path_cover = 'public/collection/serial/edition/cover/' . $edition->id . '/' . Str::random(40) . '.jpeg';
                        $path_original = 'public/collection/serial/edition/original/' . $edition->id . '/' . Str::random(40) . '.pdf';

                        Storage::disk($this->location->location)->makeDirectory('public/collection/serial/edition/cover/' . $edition->id);
                        Storage::disk($this->location->location)->makeDirectory('public/collection/serial/edition/original/' . $edition->id);

                        File::copy(Storage::disk($this->location->location)->path($path_tmp_cover), Storage::disk($this->location->location)->path($path_cover));
                        File::copy(Storage::disk($this->location->location)->path($path_tmp_original), Storage::disk($this->location->location)->path($path_original));

                        CollectionMedia::insert([
                            [
                                'collection_id' => $edition->id,
                                'link' => $path_cover,
                                'size' => File::size(Storage::disk($this->location->location)->path($path_cover)),
                                'extension' => pathinfo(Storage::disk($this->location->location)->path($path_cover), PATHINFO_EXTENSION),
                                'mimes' => File::mimeType(Storage::disk($this->location->location)->path($path_cover)),
                                'hash' => md5_file(Storage::disk($this->location->location)->path($path_cover)),
                                'type' => 1,
                                'method' => 3,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ],
                            [
                                'collection_id' => $edition->id,
                                'link' => $path_original,
                                'size' => File::size(Storage::disk($this->location->location)->path($path_original)),
                                'extension' => pathinfo(Storage::disk($this->location->location)->path($path_original), PATHINFO_EXTENSION),
                                'mimes' => File::mimeType(Storage::disk($this->location->location)->path($path_original)),
                                'hash' => md5_file(Storage::disk($this->location->location)->path($path_original)),
                                'type' => 2,
                                'method' => 3,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ],
                        ]);

                        $job = new PDFToImage('serial/edition', $path_original, $edition->id);
                        dispatch(($job)->onQueue('convert_pdf'));
                    }
                }

                $this->createAdditonalInfo($request, $parent->id);

                $collection = Collection::find($parent->id);
                $params = [
                    'user_id' => session('id'),
                    'publisher' => $collection->publisher->name,
                    'title' => $collection->title,
                ];

                $job = new SendMailCollectionSubmitted($params);
                dispatch(($job)->onQueue('notification'));

                session()->flash('success', 'Berhasil ditambahkan!');
                $response = ['status' => 200, 'type' => $request->type, 'id' => $parent->id];
            } catch (\Exception $e) {
                \Log::debug('error create serial: ' . $e->getMessage());
                session()->flash('failed', 'Mohon lengkapi data!');
                $response = [
                    'status' => 302,
                    'data' => $parent,
                    'message' => 'Gagal ditambahkan',
                ];
            }
        } else {
            session()->flash('failed', 'Gagal ditambahkan!');
            $response = [
                'status' => 500,
                'message' => 'Gagal ditambahkan',
            ];
        }

        return response()->json($response);
    }

    private function createAdditonalInfo(Request $request, $collectionId)
    {
        if ($request->has('category')) {
            foreach ($request->category as $cc) {
                CollectionCategory::create([
                    'collection_id' => $collectionId,
                    'category_id' => $cc,
                ]);
            }
        }

        if ($request->has('contributor_id_field')) {
            foreach ($request->contributor_id_field as $key => $ccid) {

                $author = Author::updateOrCreate([
                    'slug' => Str::slug($request->author_fullname_field[$key], '-'),
                ], [
                    'title' => $request->author_title_field[$key],
                    'fullname' => $request->author_fullname_field[$key],
                    'year_of_birth' => $request->author_year_of_birth_field[$key],
                    'year_of_death' => $request->author_year_of_death_field[$key],
                ]);

                $contributor = Contributor::updateOrCreate([
                    'slug' => Str::slug($request->contributor_name_field[$key], '-'),
                    'type' => $request->type,
                ], [
                    'name' => $request->contributor_name_field[$key],
                    'slug' => Str::slug($request->contributor_name_field[$key], '-'),
                    'type' => $request->type,
                ]);

                CollectionContributor::create([
                    'collection_id' => $collectionId,
                    'contributor_id' => $contributor->id,
                    'author_id' => $author->id,
                ]);
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
                    'collection_id' => $collectionId,
                    'subject_id' => $subject->id,
                ]);
            }
        }
    }

    private function uploadCollection($collectionId, $cover, $original, $type)
    {

        try {
            $link_collection_cover = Storage::disk($this->location->location)->put('public/collection/' . $type . '/cover/' . $collectionId, $cover);
            $dir_original   =  Storage::disk($this->location->location)->put('public/collection/' . $type . '/original/' . $collectionId, $original);

            if ($original->getClientOriginalExtension() == "pdf") {
                $job = new PDFToImage($type, $dir_original, $collectionId);
                dispatch(($job)->onQueue('convert_pdf'));
            }

            if ($type == 'audio') {
                $originalType = 4;
            } else if ($type == 'video') {
                $originalType = 7;
            } else {
                $originalType = 2;
            }

            CollectionMedia::insert([
                [
                    'collection_id' => $collectionId,
                    'link' => $link_collection_cover,
                    'size' => File::size($cover),
                    'extension' => $cover->getClientOriginalExtension(),
                    'mimes' => File::mimeType($cover),
                    'hash' => md5_file($cover),
                    'type' => 1,
                    'method' => 3,
                    'location_id' => $this->location->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'collection_id' => $collectionId,
                    'link' => $dir_original,
                    'size' => File::size($original),
                    'extension' => $original->getClientOriginalExtension(),
                    'mimes' => File::mimeType($original),
                    'hash' => md5_file($original),
                    'type' => $originalType,
                    'location_id' => $this->location->id,
                    'method' => 3,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
            ]);
        } catch (\Exception $e) {
            activity('collections')
                ->causedBy(session('id'))
                ->withProperties([
                    'error' => $e->getMessage(),
                ])
                ->log('Gagal Mengunggah File Koleksi / Cover dari unggah tunggal : ' . $collectionId);
        }
    }

    public function getIsbnByPublisher(Request $request)
    {
        $publisher      = User::find(session('id'))->publisher;
        $publisher_code = $publisher->code_system;

        $where_like = [
            'kd_penerbit',
            'code',
            'title',
            'action',
            'detail'
        ];

        $offset   = $request->start;
        $limit    = $request->length;
        $order    = $where_like[$request->input('order.0.column')];
        $dir      = $request->input('order.0.dir');
        $search   = $request->input('search.value');
        $specific = ['kd_penerbit' => $publisher_code];

        $collectionAlready = Collection::select('code')
            ->whereNotNull('code')
            ->where('publisher_id', $publisher->id)
            ->where('type', 1)
            ->pluck('code');

        $data = [
            '-code'          => str_replace('-', '', $collectionAlready),
            'jenis'          => 'elek',
            '-received_date' => '[* TO *]'
        ];

        if ($search) {
            array_push($data, ['title' => '"' . $search . '"']);
        }

        $pagination = [
            'sort'   => $dir,
            'column' => $order,
            'offset' => $offset,
            'limit'  => $limit
        ];

        $datatable        = Solr::datatable('isbn', 'complete', Arr::collapse($data), $pagination, $specific);
        $response['data'] = [];
        $nomor            = $offset + 1;

        foreach ($datatable['result'] as $d) {
            $actionDetail = "";
            if (count(Solr::data('isbn', 'complete', ['kd_penerbit_dtl' => $d['kd_penerbit_dtl']])) > 1) {
                $actionDetail    = 'Ready';
                $kd_penerbit_dtl = $d['kd_penerbit_dtl'];
                $action          = "<button type='button' class='btn btn-sm btn-danger' name='select_isbn' kd_penerbit_dtl='$kd_penerbit_dtl'>Pilih</button>";
            } else {
                $actionDetail    = '-';
                $kd_penerbit_dtl = '-';
                $action          = "<button type='button' class='btn btn-sm btn-danger' name='select_isbn' kd_penerbit_dtl=''>Pilih</button>";
            }

            $prefix_element    = $d['prefix_element'];
            $publisher_element = $d['publisher_element'];
            $item_element      = $d['item_element'];
            $check_digit       = $d['check_digit'];
            $code              = $prefix_element . '-' . $publisher_element . '-' . $item_element . '-' . $check_digit;

            $response['data'][] = [
                $actionDetail,
                $code,
                $d['title'],
                $action,
                $d
            ];
        }

        $response['recordsTotal']    = $datatable['total_all_data'];
        $response['recordsFiltered'] = $datatable['total_filter'];

        return response()->json($response);
    }

    public function getIsbnByPublisherAll(Request $request)
    {
        if ($request->publisher_id) {
            $publisher_id   = $request->publisher_id;
            $user           = User::where('userable_type', 'publishers')->where('userable_id', $publisher_id)->first();
            $publisher      = $user->publisher;
            $publisher_code = $publisher->code_system;
        } else {
            $publisher      = User::find(session('id'))->publisher;
            $publisher_code = $publisher->code_system;
            $publisher_id   = $publisher->id;
        }

        $where_like = [
            'kd_penerbit',
            'code',
            'nama_penerbit',
            'title'
        ];

        $collectionAlready = Collection::select('code')
            ->whereNotNull('code')
            ->where('publisher_id', $publisher_id)
            ->where('type', 1)
            ->get();

        $data         = [];
        $offset       = $request->start;
        $limit        = $request->length;
        $order        = $where_like[$request->input('order.0.column')];
        $dir          = $request->input('order.0.dir');
        $search       = $request->input('search.value');
        $publisher_id = $request->input('publisher_id');
        $specific     = ['kd_penerbit' => $publisher_code];

        if ($search) {
            array_push($data, ['title' => '"' . $search . '"']);
        }

        foreach ($collectionAlready as $ca) {
            array_push($data, ['-code' => str_replace('-', '', $ca['code'])]);
        }

        $pagination = [
            'sort'   => $dir,
            'column' => $order,
            'offset' => $offset,
            'limit'  => $limit
        ];

        $datatable        = Solr::datatable('isbn', 'complete', Arr::collapse($data), $pagination, $specific);
        $response['data'] = [];
        $nomor            = $offset + 1;

        foreach ($datatable['result'] as $d) {
            $prefix_element    = $d['prefix_element'];
            $publisher_element = $d['publisher_element'];
            $item_element      = $d['item_element'];
            $check_digit       = $d['check_digit'];
            $code              = $prefix_element . '-' . $publisher_element . '-' . $item_element . '-' . $check_digit;
            $kd_penerbit_dtl   = $d['kd_penerbit_dtl'];
            $selector          = 'selectIsbn' . $nomor;

            $response['data'][] = [
                $nomor,
                $code,
                $d['nama_penerbit'],
                $d['title'],
                '<button type="button" class="btn btn-sm btn-danger" name="select_isbn" kd_penerbit_dtl="' . $kd_penerbit_dtl . '" id="selectIsbn' . $nomor . '" onclick="selectisbn(' . "'$selector'" . ')">Pilih</button>'
            ];

            $nomor++;
        }

        $response['recordsTotal']    = $datatable['total_all_data'];
        $response['recordsFiltered'] = $datatable['total_filter'];

        return response()->json($response);
    }

    public function getIsbnJilid($kd_penerbit_dtl)
    {
        $data     = Solr::data('isbn', 'complete', ['kd_penerbit_dtl' => $kd_penerbit_dtl]);
        $response = [];

        if (count($data) > 0) {
            foreach ($data as $key => $d) {
                foreach ($d as $key => $val) {
                    if ($key == 'code') {
                        $prefix_element    = $d['prefix_element'];
                        $publisher_element = $d['publisher_element'];
                        $item_element      = $d['item_element'];
                        $check_digit       = $d['check_digit'];
                        $code              = $prefix_element . '-' . $publisher_element . '-' . $item_element . '-' . $check_digit;
                        $value             = $code;
                    } else {
                        $value = $val;
                    }

                    $response[$key] = $value;
                }
            }
        }

        return response()->json($response);
    }

    public function loadImagePdf(Request $request)
    {
        $data = CollectionMedia::where('collection_id', $request->collection_id)->where('type', 3)->first();
        $file = $data ? $data->jsonParse() : null;
        $total_file = 0;
        $image = '';

        if ($file) {
            $image = $file[(int) $request->key - 1];
            /*foreach($file as $key => $f) {
        $total_file += $key + 1;

        if($request->key == $key - 1) {
        $image = $f;
        }
        }*/
        }

        return response()->json([
            'image' => $image,
            'total_data' => count($file),
        ]);
    }
}
