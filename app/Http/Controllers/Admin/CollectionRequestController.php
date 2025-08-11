<?php

namespace App\Http\Controllers\Admin;

use App\Models\Solr;
use App\Models\Author;
use App\Models\Setting;
use App\Models\Subject;
use App\Models\Category;
use App\Models\Director;
use App\Models\Location;
use App\Jobs\BulkUpload;
use App\Jobs\PDFToImage;
use App\Models\Publisher;
use App\Models\Collection;
use App\Models\ActivityLog;
use App\Models\Contributor;
use App\Models\DepositHead;
use App\Models\Notification;
use Illuminate\Support\Str;
use App\Jobs\WatermarkAudio;
use Illuminate\Http\Request;
use App\Helper\GeneralHelper;
use App\Models\CollectionMedia;
use App\Models\CollectionSubject;
use App\Models\CollectionCategory;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\CollectionContributor;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CollectionRequestController extends Controller
{
    protected $location;

    public function __construct()
    {
        $this->location = Location::where('active', 1)->first();
    }

    public function streamFilePdf(Request $request)
    {
        $data = Storage::disk($this->location->location)->path($request->file_stream);

        return response()->make(file_get_contents($data), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="temporary.pdf"'
        ]);
    }

    public function saveTemporary(Request $request)
    {
        $cover    = $request->file('cover_field');
        $original = $request->file('original_field');


        $path_cover    = Storage::disk($this->location->location)->put('public/collection/serial/temporary', $cover);
        $path_original = Storage::disk($this->location->location)->put('public/collection/serial/temporary', $original);

        $cover_image = '<a href="' . asset(Storage::disk($this->location->location)->url($path_cover)) . '" data-lightbox="' . $cover->getClientOriginalName() . '" data-title="' . $cover->getClientOriginalName() . '"><img src="' . asset(Storage::disk($this->location->location)->url($path_cover)) . '" style="max-height:30px; max-width:30px;"></a>';

        $original_file = '<form method="GET" action="' . url('publisher/collection/stream_file_pdf') . '" target="_blank">
            <input type="hidden" name="csrf-token" value="' . csrf_token() . '">
            <input type="hidden" name="file_stream" value="' . $path_original . '">
            <button type="submit" class="btn btn-success btn-sm">Lihat File</button>
        </form>';

        return response()->json([
            'date_field'     => date('d-m-Y', strtotime($request->date_field)),
            'cover_field'    => $cover_image,
            'original_field' => $original_file,
        ]);
    }

    public function checkCodeIsbn(Request $request)
    {
        $data = Solr::data('isbn', 'complete', ['code' => str_replace('-', '', $request->code)]);
        if (count($data) > 0) {
            $check = Collection::where('code', $request->code)->where('parent_id', 0)->first();
            if ($check) {
                if ($check->status == 1) {
                    $segment = '/collection/monitoring/review/';
                } else {
                    $segment = '/collection/manage/update/';
                }

                Collection::find($check->id)->update(['edit_by' => session('id')]);

                $response = [
                    'status'  => 201,
                    'message' => null,
                    'data'    => url('admin' . $segment . $check->id)
                ];
            } else {
                $result    = $data[0];
                $code      = $result['prefix_element'] . '-' . $result['publisher_element'] . '-' . $result['item_element'] . '-' . $result['check_digit'];
                $publisher = Publisher::where('kd_penerbit', $result['kd_penerbit'])->first();

                $response = [
                    'status'  => 200,
                    'message' => 'Data ditemukan!',
                    'data'    => [
                        'code'           => $code,
                        'title'          => $result['title'],
                        'tahun_terbit'   => isset($result['tahun_terbit']) ? $result['tahun_terbit'] : '',
                        'kepeng'         => isset($result['kepeng']) ? $result['kepeng'] : '',
                        'sinopsis'       => isset($result['sinopsis']) ? $result['sinopsis'] : '',
                        'edisi'          => isset($result['edisi']) ? $result['edisi'] : '',
                        'jml_hlm'        => isset($result['jml_hlm']) ? $result['jml_hlm'] : '',
                        'subjek'         => isset($result['subjek']) ? $result['subjek'] : '',
                        'seri'           => isset($result['seri']) ? $result['seri'] : '',
                        'publisher_id'   => $publisher ? $publisher->id : '',
                        'publisher_name' => $publisher ? $publisher->name : ''
                    ]
                ];
            }
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Data tidak ditemukan!',
                'data'    => null
            ];
        }

        return response()->json($response);
    }

    public function createManual(Request $request, $type, $connect = null)
    {
        try {
            if ($request->ajax()) {
                if ($type == 1) {
                    if ($connect == 'isbn') {
                        $validator = Validator::make($request->all(), [
                            'code'              => 'required',
                            'publisher_id'      => 'required',
                            'title'             => 'required',
                            'preview'           => 'required',
                            'publication_month' => 'required|date_format:m',
                            'publication_year'  => 'required|date_format:Y',
                            'received_at'       => 'required',
                            'cover'             => 'required|max:1024|mimes:jpg,jpeg,png',
                            'original'          => 'required|max:500000|mimes:pdf,epub,mobi'
                        ], [
                            'code.required'                 => 'Kode ISBN wajib di isi!',
                            'publisher_id.required'         => 'Harap memilih penerbit!',
                            'title.required'                => 'Judul wajib di isi!',
                            'preview.required'              => 'Preview wajib di isi!',
                            'publication_month.required'    => 'Bulan terbit wajib di isi!',
                            'publication_month.date_format' => 'Bulan terbit harus berupa bulan!',
                            'publication_year.required'     => 'Tahun terbit wajib di isi!',
                            'publication_year.date_format'  => 'Tahun terbit harus berupa tahun!',
                            'received_at.required'          => 'Tanggal terbit wajib di isi!',
                            'cover.required'                => 'Cover wajib di isi!',
                            'cover.max'                     => 'Cover maksimal 1MB!',
                            'cover.mimes'                   => 'Cover harus bertipe jpg, jpeg, png!',
                            'original.required'             => 'File konten wajib di isi!',
                            'original.max'                  => 'File konten maksimal 500MB!',
                            'original.mimes'                => 'File konten harus bertipe pdf!'
                        ]);
                    } else {
                        $validator = Validator::make($request->all(), [
                            'publisher_id'      => 'required',
                            'title'             => 'required',
                            'preview'           => 'required',
                            'publication_month' => 'required|date_format:m',
                            'publication_year'  => 'required|date_format:Y',
                            'received_at'       => 'required',
                            'cover'             => 'required|max:1024|mimes:jpg,jpeg,png',
                            'original'          => 'required|file|max:500000|mimes:pdf,epub,mobi'
                        ], [
                            'publisher_id.required'         => 'Harap memilih penerbit!',
                            'title.required'                => 'Judul wajib di isi!',
                            'preview.required'              => 'Preview wajib di isi!',
                            'publication_month.required'    => 'Bulan terbit wajib di isi!',
                            'publication_month.date_format' => 'Bulan terbit harus berupa bulan!',
                            'publication_year.required'     => 'Tahun terbit wajib di isi!',
                            'publication_year.date_format'  => 'Tahun terbit harus berupa tahun!',
                            'received_at.required'          => 'Tanggal terbit wajib di isi!',
                            'cover.required'                => 'Cover wajib di isi!',
                            'cover.max'                     => 'Cover maksimal 1MB!',
                            'cover.mimes'                   => 'Cover harus bertipe jpg, jpeg, png!',
                            'original.required'             => 'File konten wajib di isi!',
                            'original.image'                => 'File konten berupa file image!',
                            'original.max'                  => 'File konten maksimal 500MB!',
                            'original.mimes'                => 'File konten harus bertipe pdf!'
                        ]);
                    }
                } else if ($type == 2) {
                    $validator = Validator::make($request->all(), [
                        'publisher_id'      => 'required',
                        'title'             => 'required',
                        'preview'           => 'required',
                        'publication_month' => 'required|date_format:m',
                        'publication_year'  => 'required|date_format:Y',
                        'received_at'       => 'required',
                        'cover'             => 'required|max:1024|mimes:jpg,jpeg,png',
                        'original'          => 'required|file|max:500000|mimes:pdf,epub,mobi'
                    ], [
                        'publisher_id.required'         => 'Harap memilih produser!',
                        'title.required'                => 'Judul wajib di isi!',
                        'preview.required'              => 'Preview wajib di isi!',
                        'publication_month.required'    => 'Bulan terbit wajib di isi!',
                        'publication_month.date_format' => 'Bulan terbit harus berupa bulan!',
                        'publication_year.required'     => 'Tahun terbit wajib di isi!',
                        'publication_year.date_format'  => 'Tahun terbit harus berupa tahun!',
                        'received_at.required'          => 'Tanggal terbit wajib di isi!',
                        'cover.required'                => 'Cover wajib di isi!',
                        'cover.max'                     => 'Cover maksimal 1MB!',
                        'cover.mimes'                   => 'Cover harus bertipe jpg, jpeg, png!',
                        'original.required'             => 'File konten wajib di isi!',
                        'original.image'                => 'File konten berupa file image!',
                        'original.max'                  => 'File konten maksimal 500MB!',
                        'original.mimes'                => 'File konten harus bertipe pdf!'
                    ]);
                } else if ($type == 3) {
                    $validator = Validator::make($request->all(), [
                        'publisher_id'      => 'required',
                        'title'             => 'required',
                        'preview'           => 'required',
                        'publication_month' => 'required|date_format:m',
                        'publication_year'  => 'required|date_format:Y',
                        'received_at'       => 'required',
                        'cover'             => 'required|max:1024|mimes:jpg,jpeg,png',
                        'original'          => 'required|file|max:500000|mimes:pdf,epub,mobi'
                    ], [
                        'publisher_id.required'         => 'Harap memilih penerbit!',
                        'title.required'                => 'Judul wajib di isi!',
                        'preview.required'              => 'Preview wajib di isi!',
                        'publication_month.required'    => 'Bulan terbit wajib di isi!',
                        'publication_month.date_format' => 'Bulan terbit harus berupa bulan!',
                        'publication_year.required'     => 'Tahun terbit wajib di isi!',
                        'publication_year.date_format'  => 'Tahun terbit harus berupa tahun!',
                        'received_at.required'          => 'Tanggal terbit wajib di isi!',
                        'cover.required'                => 'Cover wajib di isi!',
                        'cover.max'                     => 'Cover maksimal 1MB!',
                        'cover.mimes'                   => 'Cover harus bertipe jpg, jpeg, png!',
                        'original.required'             => 'File konten wajib di isi!',
                        'original.image'                => 'File konten berupa file image!',
                        'original.max'                  => 'File konten maksimal 500MB!',
                        'original.mimes'                => 'File konten harus bertipe pdf!'
                    ]);
                } else if ($type == 4) {
                    $validator = Validator::make($request->all(), [
                        'publisher_id'      => 'required',
                        'title'             => 'required',
                        'preview'           => 'required',
                        'publication_month' => 'required|date_format:m',
                        'publication_year'  => 'required|date_format:Y',
                        'cover'             => 'required|max:1024|mimes:jpg,jpeg,png'
                    ], [
                        'publisher_id.required'         => 'Harap memilih penerbit!',
                        'title.required'                => 'Judul wajib di isi!',
                        'preview.required'              => 'Preview wajib di isi!',
                        'publication_month.required'    => 'Bulan terbit wajib di isi!',
                        'publication_month.date_format' => 'Bulan terbit harus berupa bulan!',
                        'publication_year.required'     => 'Tahun terbit wajib di isi!',
                        'publication_year.date_format'  => 'Tahun terbit harus berupa tahun!',
                        'cover.required'                => 'Cover wajib di isi!',
                        'cover.max'                     => 'Cover maksimal 1MB!',
                        'cover.mimes'                   => 'Cover harus bertipe jpg, jpeg, png!'
                    ]);
                } else if ($type == 5) {
                    $validator = Validator::make($request->all(), [
                        'publisher_id'      => 'required',
                        'title'             => 'required',
                        'preview_start'     => 'required|date_format:i:s',
                        'preview_end'       => 'required|date_format:i:s',
                        'publication_month' => 'required|date_format:m',
                        'publication_year'  => 'required|date_format:Y',
                        'received_at'       => 'required',
                        'cover'             => 'required|max:1024|mimes:jpg,jpeg,png',
                        'original'          => 'required|file|max:500000|mimes:mpeg,mpga,mp3,wav'
                    ], [
                        'publisher_id.required'         => 'Harap memilih produser!',
                        'title.required'                => 'Judul wajib di isi!',
                        'preview_start.required'        => 'Preview start wajib di isi!',
                        'preview_start.date_format'     => 'Preview start harus berupa detik sebagai berikut 00: 30!',
                        'preview_end.required'          => 'Preview end wajib di isi!',
                        'preview_end.date_format'       => 'Preview end harus berupa detik sebagai berikut 01  : 02!',
                        'publication_month.required'    => 'Bulan terbit wajib di isi!',
                        'publication_month.date_format' => 'Bulan terbit harus berupa bulan!',
                        'publication_year.required'     => 'Tahun terbit wajib di isi!',
                        'publication_year.date_format'  => 'Tahun terbit harus berupa tahun!',
                        'received_at.required'          => 'Tanggal terbit wajib di isi!',
                        'cover.required'                => 'Cover wajib di isi!',
                        'cover.max'                     => 'Cover maksimal 1MB!',
                        'cover.mimes'                   => 'Cover harus bertipe jpg, jpeg, png!',
                        'original.required'             => 'File konten wajib di isi!',
                        'original.image'                => 'File konten berupa file image!',
                        'original.max'                  => 'File konten maksimal 500MB!',
                        'original.mimes'                => 'File konten harus bertipe MP3!'
                    ]);
                } else if ($type == 6) {
                    $validator = Validator::make($request->all(), [
                        'publisher_id'      => 'required',
                        'title'             => 'required',
                        'preview_start'     => 'required|date_format:H:i:s',
                        'preview_end'       => 'required|date_format:H:i:s',
                        'publication_month' => 'required|date_format:m',
                        'publication_year'  => 'required|date_format:Y',
                        'received_at'       => 'required',
                        'cover'             => 'required|max:1024|mimes:jpg,jpeg,png',
                        'original'          => 'required|file|max:5000000|mimes:mkv,mp4,avi,mpeg,3gp'
                    ], [
                        'publisher_id.required'         => 'Harap memilih produser!',
                        'title.required'                => 'Judul wajib di isi!',
                        'preview.required'              => 'Preview wajib di isi!',
                        'preview_start.required'        => 'Preview start wajib di isi!',
                        'preview_start.date_format'     => 'Preview start harus berupa jam sebagai berikut 01: 30: 00!',
                        'preview_end.required'          => 'Preview end wajib di isi!',
                        'preview_end.date_format'       => 'Preview end harus berupa jam sebagai berikut 02  : 03: 07!',
                        'publication_month.required'    => 'Bulan terbit wajib di isi!',
                        'publication_month.date_format' => 'Bulan terbit harus berupa bulan!',
                        'publication_year.required'     => 'Tahun terbit wajib di isi!',
                        'publication_year.date_format'  => 'Tahun terbit harus berupa tahun!',
                        'received_at.required'          => 'Tanggal terbit wajib di isi!',
                        'cover.required'                => 'Cover wajib di isi!',
                        'cover.max'                     => 'Cover maksimal 1MB!',
                        'cover.mimes'                   => 'Cover harus bertipe jpg, jpeg, png!',
                        'original.required'             => 'File konten wajib di isi!',
                        'original.image'                => 'File konten berupa file image!',
                        'original.max'                  => 'File konten maksimal 5GB!',
                        'original.mimes'                => 'File konten harus bertipe mkv, mp4, avi, mpeg, 3gp!'
                    ]);
                }

                if ($validator->fails()) {
                    $response = [
                        'status' => 422,
                        'error'  => $validator->errors()
                    ];
                } else {
                    if ($type == 1) {
                        if ($connect == 'isbn') {
                            $explode = explode('-', $request->code);
                            $isbn    = Solr::data('isbn', 'complete', [
                                'prefix_element'    => $explode[0] . '-' . $explode[1],
                                'publisher_element' => $explode[2],
                                'item_element'      => $explode[3],
                                'check_digit'       => $explode[4]
                            ]);

                            $type_book        = $isbn[0]['jenis'] == 'elek' ? 1 : 2;
                            $code             = $request->code;
                            $code_kdt         = $isbn[0]['kd_penerbit_dtl'];
                        } else {
                            $type_book = null;
                            $code      = null;
                            $code_kdt  = null;
                        }

                        $code_type            = 1;
                        $physical_description = [
                            'total_page'  => $request->total_page,
                            'dimension'   => $request->dimension,
                            'ilustration' => $request->ilustration
                        ];
                    } else if ($type == 2) {
                        $code_type            = 2;
                        $type_book            = null;
                        $code                 = $request->code;
                        $code_kdt             = null;
                        $physical_description = [
                            'total_page'  => $request->total_page,
                            'dimension'   => $request->dimension
                        ];
                    } else if ($type == 3) {
                        $code_type            = 1;
                        $type_book            = null;
                        $code                 = $request->code;
                        $code_kdt             = null;
                        $physical_description = [
                            'total_page' => $request->total_page,
                            'scale'      => $request->scale,
                            'dimension'  => $request->dimension
                        ];
                    } else if ($type == 4) {
                        $code_type            = 4;
                        $type_book            = null;
                        $code                 = $request->code;
                        $code_kdt             = null;
                        $physical_description = [
                            'dimension' => $request->dimension
                        ];
                    } else if ($type == 5) {
                        $code_type            = 3;
                        $type_book            = null;
                        $code                 = $request->code;
                        $code_kdt             = null;
                        $physical_description = [
                            'duration' => $request->duration
                        ];
                    } else if ($type == 6) {
                        $code_type            = 5;
                        $type_book            = null;
                        $code                 = $request->code;
                        $code_kdt             = null;
                        $physical_description = [
                            'duration' => $request->duration
                        ];
                    }

                    $publisher = Publisher::find($request->publisher_id);
                    $preview   = $request->preview ? $request->preview : $request->preview_start . '-' . $request->preview_end;
                    $create    = Collection::create([
                        'publisher_id'         => $publisher->id,
                        'title'                => $request->title,
                        'title_ori'            => $request->title,
                        'physical_description' => json_encode($physical_description),
                        'slug'                 => Str::slug($request->title, '-'),
                        'type'                 => $type,
                        'album'                => $request->album,
                        'type_book'            => $type_book,
                        'edition'              => $request->edition,
                        'code'                 => $code,
                        'code_type'            => $code_type,
                        'code_kdt'             => $code_kdt,
                        'publication_month'    => $request->publication_month,
                        'publication_year'     => $request->publication_year,
                        'series'               => $request->series,
                        'serial'               => $request->serial,
                        'ddc'                  => $request->ddc,
                        'volume'               => $request->volume,
                        'preview'              => $preview,
                        'description'          => $request->description,
                        'manual'               => 1,
                        'deposit'              => GeneralHelper::depositCollection(),
                        'copyright'            => 'Copyrights (c) ' . date('Y') . ' ' . $publisher->name,
                        'access'               => $request->access,
                        'status'               => 2,
                        'received_by'          => session('id'),
                        'edit_by'              => session('id'),
                        'received_at'          => $request->has('received_at') ? $request->received_at : date('Y-m-d'),
                        'created_by'           => session('id'),
                        'updated_by'           => session('id'),
                        'validated_by'         => session('id'),
                        'validated_at'         => date('Y-m-d H:i:s')
                    ]);

                    if ($create) {
                        $cover    = $request->file('cover');
                        $original = $request->file('original');

                        $log_category    = [];
                        $log_contributor = [];
                        $log_subject     = [];

                        if ($request->has('collection_category')) {
                            foreach ($request->collection_category as $cc) {
                                $logged = CollectionCategory::create([
                                    'collection_id' => $create->id,
                                    'category_id'   => $cc
                                ]);

                                $log_category[] = $logged->category->name;
                            }
                        }

                        if ($request->has('contributor_contributor_id_field')) {
                            foreach ($request->contributor_contributor_id_field as $key => $ccid) {
                                $name  = $request->contributor_fullname_field[$key];
                                $title = $request->contributor_title_field[$key];

                                if (!empty($name) && !empty($title)) {
                                    $authorCheck = Author::updateOrCreate([
                                        'fullname' => $name,
                                        'title'    => $title,
                                        'slug'     => Str::slug($name, '-')
                                    ], [
                                        'year_of_birth' => $request->contributor_year_of_birth_field[$key],
                                        'year_of_death' => $request->contributor_year_of_death_field[$key]
                                    ]);

                                    $author = Author::where('fullname', $name)
                                        ->where('title', $title)
                                        ->where('slug', Str::slug($name, '-'))
                                        ->where('year_of_birth', $request->contributor_year_of_birth_field[$key])
                                        ->where('year_of_death', $request->contributor_year_of_death_field[$key])
                                        ->first();

                                    $logged = CollectionContributor::create([
                                        'collection_id'  => $create->id,
                                        'contributor_id' => $ccid,
                                        'author_id'      => $author->id
                                    ]);

                                    $log_contributor[] = [
                                        'kontributor'      => $logged->contributor->name,
                                        'nama'             => $logged->author->fullname,
                                        'gelar'            => $logged->author->title,
                                        'tanggal_lahir'    => $logged->author->year_of_birth,
                                        'tanggal_kematian' => $logged->author->year_of_death
                                    ];
                                }
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

                                $logged = CollectionSubject::create([
                                    'collection_id' => $create->id,
                                    'subject_id'    => $subject->id
                                ]);

                                $log_subject[] = $logged->subject->name;
                            }
                        }

                        if ($request->has('edition_edition_field')) {
                            foreach ($request->edition_edition_field as $key => $eef) {
                                $edition = Collection::create([
                                    'parent_id'    => $create->id,
                                    'publisher_id' => $request->publisher_id,
                                    'type'         => $type,
                                    'edition'      => $eef,
                                    'deposit'      => GeneralHelper::depositCollection(),
                                    'copyright'    => 'Copyrights (c) ' . date('Y') . ' ' . $publisher->name,
                                    'manual'       => 1,
                                    'date'         => $request->edition_date_field[$key],
                                    'status'       => 1,
                                    'received_by'  => session('id'),
                                    'received_at'  => date('Y-m-d H:i:s'),
                                    'edit_by'      => session('id'),
                                    'created_by'   => session('id'),
                                    'updated_by'   => session('id')
                                ]);

                                $cover_edition     = $request->edition_cover_field[$key];
                                $original_edition  = $request->edition_original_field[$key];
                                $path_tmp_cover    = 'public/collection/serial/temporary/' . $cover_edition;
                                $path_tmp_original = 'public/collection/serial/temporary/' . $original_edition;
                                $path_cover        = 'public/collection/serial/edition/cover/' . $edition->id . '/' . Str::random(40) . '.jpeg';
                                $path_original     = 'public/collection/serial/edition/original/' . $edition->id . '/' . Str::random(40) . '.pdf';

                                Storage::disk($this->location->location)->makeDirectory('public/collection/serial/edition/watermark/' . $edition->id);
                                CollectionMedia::insert([
                                    [
                                        'collection_id' => $edition->id,
                                        'link'          => $path_cover,
                                        'size'          => File::size(Storage::disk($this->location->location)->path($path_cover)),
                                        'extension'     => pathinfo(Storage::disk($this->location->location)->path($path_cover), PATHINFO_EXTENSION),
                                        'mimes'         => File::mimeType(Storage::disk($this->location->location)->path($path_cover)),
                                        'hash'          => md5_file(Storage::disk($this->location->location)->path($path_cover)),
                                        'type'          => 1,
                                        'method'        => 4,
                                        'created_at'    => date('Y-m-d H:i:s'),
                                        'updated_at'    => date('Y-m-d H:i:s'),
                                        'location_id'   => $this->location->id
                                    ],
                                    [
                                        'collection_id' => $edition->id,
                                        'link'          => $path_original,
                                        'size'          => File::size(Storage::disk($this->location->location)->path($path_original)),
                                        'extension'     => pathinfo(Storage::disk($this->location->location)->path($path_original), PATHINFO_EXTENSION),
                                        'mimes'         => File::mimeType(Storage::disk($this->location->location)->path($path_original)),
                                        'hash'          => md5_file(Storage::disk($this->location->location)->path($path_original)),
                                        'type'          => 2,
                                        'method'        => 4,
                                        'created_at'    => date('Y-m-d H:i:s'),
                                        'updated_at'    => date('Y-m-d H:i:s')
                                    ]
                                ]);
                            }
                        }

                        if ($type == 1) {
                            $name_cover = 'book';
                        } else if ($type == 2) {
                            $name_cover = 'partitur';
                        } else if ($type == 3) {
                            $name_cover = 'map';
                        } else if ($type == 4) {
                            $name_cover = 'serial';
                        } else if ($type == 5) {
                            $name_cover = 'audio';
                        } else if ($type == 4) {
                            $name_cover = 'film';
                        }

                        $link_collection_cover = Storage::disk($this->location->location)->put('public/collection/' . $name_cover . '/cover/' . $create->id, $cover);
                        CollectionMedia::create([
                            'collection_id' => $create->id,
                            'link'          => Storage::disk($this->location->location)->put('public/collection/' . $name_cover . '/cover/' . $create->id, $cover),
                            'size'          => File::size($cover),
                            'extension'     => $cover->getClientOriginalExtension(),
                            'mimes'         => File::mimeType($cover),
                            'hash'          => md5_file($cover),
                            'type'          => 1,
                            'method'        => 4,
                            'created_at'    => date('Y-m-d H:i:s'),
                            'updated_at'    => date('Y-m-d H:i:s'),
                            'location_id'   => $this->location->id
                        ]);

                        if ($type == 1) {
                            $dir_original = Storage::disk($this->location->location)->put('public/collection/book/original/' . $create->id, $original);
                            $job          = new PDFToImage('book', $dir_original, $create->id);
                            dispatch(($job)->onQueue('convert_pdf'));
                        } else if ($type == 2) {
                            $dir_original = Storage::disk($this->location->location)->put('public/collection/partitur/original/' . $create->id, $original);
                            $job          = new PDFToImage('partitur', $dir_original, $create->id);
                            dispatch(($job)->onQueue('convert_pdf'));
                        } else if ($type == 3) {
                            $dir_original = Storage::disk($this->location->location)->put('public/collection/map/original/' . $create->id, $original);
                            $job          = new PDFToImage('map', $dir_original, $create->id);
                            dispatch(($job)->onQueue('convert_pdf'));
                        } else if ($type == 4) {
                            $collection      = Collection::find($create->id);
                            $collectionMedia = CollectionMedia::where('id', $create->id)->first();
                            $template        = Setting::where('slug', 'template-email-collection-success')->first();

                            if ($collection->publisher->email) {
                                Mail::send([], [], function ($message) use ($collection, $template, $collectionMedia) {
                                    $header      = Setting::where('slug', 'template-email-header')->first();
                                    $footer      = Setting::where('slug', 'template-email-footer')->first();
                                    $link_header = public_path('storage/' . str_replace('public/', '', $header->content));
                                    $link_footer = public_path('storage/' . str_replace('public/', '', $footer->content));
                                    $director    = Director::where('province_id', session('province_id'))->orderByRaw('DATE(position_start) DESC')->first();

                                    if ($director) {
                                        $signature = $director->position . ', <br>' . $director->name . '<br><br><img src="' . public_path('storage/' . str_replace('public/', '', $director->signature)) . '" width="180"><br><br>NIP. ' . $director->nip;
                                    } else {
                                        $signature = '';
                                    }

                                    $data = [
                                        'header'      => '<img src="' . $message->embed($link_header) . '" style="width:100%;">',
                                        'footer'      => '<img src="' . $message->embed($link_footer) . '" style="width:100%;">',
                                        'received_at' => $collection->received_at,
                                        'code'        => $collection->code,
                                        'publisher'   => $collection->publisher->name,
                                        'title'       => $collection->title,
                                        'size'        => $collectionMedia->size,
                                        'mimes'       => $collectionMedia->mimes,
                                        'hash'        => $collectionMedia->hash,
                                        'director'    => $signature
                                    ];

                                    $message->to($collection->publisher->email, 'edeposit@perpusnas.go.id')
                                        ->subject('Koleksi Divalidasi')
                                        ->from('edeposit@perpusnas.go.id', 'Info edeposit')
                                        ->setBody($template->parse($data), 'text/html');
                                });
                            }

                            Notification::create([
                                'user_id' => session('id'),
                                'title'   => 'Koleksi Divalidasi',
                                'body'    => $template->content
                            ]);
                        } else if ($type == 5) {
                            Storage::disk($this->location->location)->makeDirectory('public/collection/audio/preview/' . $create->id);
                            Storage::disk($this->location->location)->makeDirectory('public/collection/audio/watermark/' . $create->id);

                            $dir_original = Storage::disk($this->location->location)->put('public/collection/audio/original/' . $create->id, $original);
                            $prev_start   = $request->preview_start;
                            $prev_end     = $request->preview_end;

                            $create_media = CollectionMedia::create([
                                'collection_id' => $create->id,
                                'link'          => $dir_original,
                                'size'          => File::size($original),
                                'extension'     => $original->getClientOriginalExtension(),
                                'mimes'         => File::mimeType($original),
                                'hash'          => md5_file($original),
                                'type'          => 4,
                                'method'        => 4,
                                'created_at'    => date('Y-m-d H:i:s'),
                                'updated_at'    => date('Y-m-d H:i:s'),
                                'location_id'   => $this->location->id
                            ]);

                            dispatch(new WatermarkAudio(Storage::disk($this->location->location)->path($dir_original), $create_media))->onQueue('audio');

                            $collection      = Collection::find($create->id);
                            $collectionMedia = CollectionMedia::where('id', $create->id)->first();
                            $template        = Setting::where('slug', 'template-email-collection-success')->first();

                            if ($collection->publisher->email) {
                                Mail::send([], [], function ($message) use ($collection, $template, $collectionMedia) {
                                    $header      = Setting::where('slug', 'template-email-header')->first();
                                    $footer      = Setting::where('slug', 'template-email-footer')->first();
                                    $link_header = public_path('storage/' . str_replace('public/', '', $header->content));
                                    $link_footer = public_path('storage/' . str_replace('public/', '', $footer->content));
                                    $director    = Director::where('province_id', session('province_id'))->orderByRaw('DATE(position_start) DESC')->first();

                                    if ($director) {
                                        $signature = $director->position . ', <br>' . $director->name . '<br><br><img src="' . public_path('storage/' . str_replace('public/', '', $director->signature)) . '" width="180"><br><br>NIP. ' . $director->nip;
                                    } else {
                                        $signature = '';
                                    }

                                    $data = [
                                        'header'      => '<img src="' . $message->embed($link_header) . '" style="width:100%;">',
                                        'footer'      => '<img src="' . $message->embed($link_footer) . '" style="width:100%;">',
                                        'received_at' => $collection->received_at,
                                        'code'        => $collection->code,
                                        'publisher'   => $collection->publisher->name,
                                        'title'       => $collection->title,
                                        'size'        => $collectionMedia->size,
                                        'mimes'       => $collectionMedia->mimes,
                                        'hash'        => $collectionMedia->hash,
                                        'director'    => $signature
                                    ];

                                    $message->to($collection->publisher->email, 'edeposit@perpusnas.go.id')
                                        ->subject('Koleksi Divalidasi')
                                        ->from('edeposit@perpusnas.go.id', 'Info edeposit')
                                        ->setBody($template->parse($data), 'text/html');
                                });

                                Notification::create([
                                    'user_id' => $collection->publisher->user->id,
                                    'title'   => 'Koleksi Divalidasi',
                                    'body'    => $template->content
                                ]);
                            }
                        } else if ($type == 6) {
                            Storage::disk($this->location->location)->makeDirectory('public/collection/film/preview/' . $create->id);
                            Storage::disk($this->location->location)->makeDirectory('public/collection/film/watermark/' . $create->id);

                            $preview_start = $request->preview_start;
                            $preview_end   = $request->preview_end;
                            $dir_original  = Storage::disk($this->location->location)->put('public/collection/film/original/' . $create->id, $original);

                            $filename_preview = Str::random(40) . '.mp4';
                            $path_preview     = Storage::disk($this->location->location)->path('/public/collection/film/preview/' . $create->id . '/' . $filename_preview);

                            $filename_watermark = Str::random(40) . '.mp4';
                            $path_watermark     = Storage::disk($this->location->location)->path('/public/collection/film/watermark/' . $create->id . '/' . $filename_watermark);

                            $link_collection_preview   = 'public/collection/film/preview/' . $create->id . '/' . $filename_preview;
                            $link_collection_watermark = 'public/collection/film/watermark/' . $create->id . '/' . $filename_watermark;

                            GeneralHelper::videoCut(Storage::disk($this->location->location)->path($dir_original), $path_preview, $preview_start, $preview_end);
                            GeneralHelper::videoWatermark(Storage::disk($this->location->location)->path($dir_original), $path_watermark);

                            CollectionMedia::insert([
                                [
                                    'collection_id' => $create->id,
                                    'link'          => $dir_original,
                                    'size'          => File::size($original),
                                    'extension'     => $original->getClientOriginalExtension(),
                                    'mimes'         => File::mimeType($original),
                                    'hash'          => md5_file($original),
                                    'type'          => 7,
                                    'method'        => 4,
                                    'created_at'    => date('Y-m-d H:i:s'),
                                    'updated_at'    => date('Y-m-d H:i:s')
                                ],
                                [
                                    'collection_id' => $create->id,
                                    'link'          => $link_collection_preview,
                                    'size'          => File::size($path_preview),
                                    'extension'     => $original->getClientOriginalExtension(),
                                    'mimes'         => File::mimeType($path_preview),
                                    'hash'          => md5_file($path_preview),
                                    'type'          => 8,
                                    'method'        => 4,
                                    'created_at'    => date('Y-m-d H:i:s'),
                                    'updated_at'    => date('Y-m-d H:i:s')
                                ],
                                [
                                    'collection_id' => $create->id,
                                    'link'          => $link_collection_watermark,
                                    'size'          => File::size($path_watermark),
                                    'extension'     => $original->getClientOriginalExtension(),
                                    'mimes'         => File::mimeType($path_watermark),
                                    'hash'          => md5_file($path_watermark),
                                    'type'          => 9,
                                    'method'        => 4,
                                    'created_at'    => date('Y-m-d H:i:s'),
                                    'updated_at'    => date('Y-m-d H:i:s')
                                ]
                            ]);

                            $collection      = Collection::find($create->id);
                            $collectionMedia = CollectionMedia::where('id', $create->id)->first();
                            $template        = Setting::where('slug', 'template-email-collection-success')->first();

                            if ($collection->publisher->email) {
                                Mail::send([], [], function ($message) use ($collection, $template, $collectionMedia) {
                                    $header      = Setting::where('slug', 'template-email-header')->first();
                                    $footer      = Setting::where('slug', 'template-email-footer')->first();
                                    $link_header = public_path('storage/' . str_replace('public/', '', $header->content));
                                    $link_footer = public_path('storage/' . str_replace('public/', '', $footer->content));
                                    $director    = Director::where('province_id', session('province_id'))->orderByRaw('DATE(position_start) DESC')->first();

                                    if ($director) {
                                        $signature = $director->position . ', <br>' . $director->name . '<br><br><img src="' . public_path('storage/' . str_replace('public/', '', $director->signature)) . '" width="180"><br><br>NIP. ' . $director->nip;
                                    } else {
                                        $signature = '';
                                    }

                                    $data = [
                                        'header'      => '<img src="' . $message->embed($link_header) . '" style="width:100%;">',
                                        'footer'      => '<img src="' . $message->embed($link_footer) . '" style="width:100%;">',
                                        'received_at' => $collection->received_at,
                                        'code'        => $collection->code,
                                        'publisher'   => $collection->publisher->name,
                                        'title'       => $collection->title,
                                        'size'        => $collectionMedia->size,
                                        'mimes'       => $collectionMedia->mimes,
                                        'hash'        => $collectionMedia->hash,
                                        'director'    => $signature
                                    ];

                                    $message->to($collection->publisher->email, 'edeposit@perpusnas.go.id')
                                        ->subject('Koleksi Divalidasi')
                                        ->from('edeposit@perpusnas.go.id', 'Info edeposit')
                                        ->setBody($template->parse($data), 'text/html');
                                });
                            }

                            Notification::create([
                                'user_id' => $collection->publisher->user->id,
                                'title'   => 'Koleksi Divalidasi',
                                'body'    => $template->content
                            ]);
                        }

                        CollectionMedia::insert([
                            [
                                'collection_id' => $create->id,
                                'link'          => $dir_original,
                                'size'          => File::size($original),
                                'extension'     => $original->getClientOriginalExtension(),
                                'mimes'         => File::mimeType($original),
                                'hash'          => md5_file($original),
                                'type'          => 2,
                                'method'        => 4,
                                'created_at'    => date('Y-m-d H:i:s'),
                                'updated_at'    => date('Y-m-d H:i:s')
                            ]
                        ]);

                        $collection      = Collection::find($create->id);
                        $collectionMedia = CollectionMedia::where('id', $create->id)->where('type', 2)->first();
                        $template        = Setting::where('slug', 'template-email-collection-success')->first();

                        if ($collection->publisher->email) {
                            Mail::send([], [], function ($message) use ($collection, $template, $collectionMedia) {
                                $header      = Setting::where('slug', 'template-email-header')->first();
                                $footer      = Setting::where('slug', 'template-email-footer')->first();
                                $link_header = public_path('storage/' . str_replace('public/', '', $header->content));
                                $link_footer = public_path('storage/' . str_replace('public/', '', $footer->content));
                                $director    = Director::where('province_id', session('province_id'))->orderByRaw('DATE(position_start) DESC')->first();

                                if ($director) {
                                    $signature = $director->position . ', <br>' . $director->name . '<br><br><img src="' . public_path('storage/' . str_replace('public/', '', $director->signature)) . '" width="180"><br><br>NIP. ' . $director->nip;
                                } else {
                                    $signature = '';
                                }

                                $data = [
                                    'header'      => '<img src="' . $message->embed($link_header) . '" style="width:100%;">',
                                    'footer'      => '<img src="' . $message->embed($link_footer) . '" style="width:100%;">',
                                    'received_at' => $collection->received_at,
                                    'code'        => $collection->code,
                                    'publisher'   => $collection->publisher->name,
                                    'title'       => $collection->title,
                                    'size'        => $collectionMedia->size,
                                    'mimes'       => $collectionMedia->mimes,
                                    'hash'        => $collectionMedia->hash,
                                    'director'    => $signature
                                ];

                                $message->to($collection->publisher->email, 'edeposit@perpusnas.go.id')
                                    ->subject('Koleksi Divalidasi')
                                    ->from('edeposit@perpusnas.go.id', 'Info edeposit')
                                    ->setBody($template->parse($data), 'text/html');
                            });
                        }

                        if ($type == 1 && $connect == 'isbn') {
                            DB::connection('sqlsrv')
                                ->table('mst_isbn')
                                ->where('kd_penerbit_dtl', $collection->kd_penerbit_dtl)
                                ->update([
                                    'received_date' => date('Y-m-d H:i:s')
                                ]);
                        }

                        session()->flash('success', 'Berhasil ditambahkan!');
                        $response = ['status'  => 200];

                        activity('collections')
                            ->performedOn($create)
                            ->causedBy(session('id'))
                            ->withProperties([
                                'penerbit'         => $create->publisher->name,
                                'judul'            => $create->title,
                                'deskripsi_fisik'  => $create->physical_description,
                                'tipe'             => $create->type(),
                                'album'            => $request->album,
                                'tipe_buku'        => $create->typeBook(),
                                'edisi'            => $create->edition,
                                'kode'             => $create->code,
                                'tipe_kode'        => $create->codeType(),
                                'kode_kdt'         => $create->code_kdt,
                                'bulan_terbit'     => $create->publication_month,
                                'tahun_terbit'     => $create->publication_year,
                                'seri'             => $create->series,
                                'serial'           => $create->serial,
                                'ddc'              => $create->ddc,
                                'volume'           => $create->volume,
                                'preview'          => $create->preview,
                                'description'      => $create->description,
                                'deposit'          => $create->deposit,
                                'copyright'        => $create->copyright,
                                'akses'            => $create->access,
                                'status'           => $create->status(),
                                'tanggal_terima'   => date('Y-m-d H:i:s', strtotime($create->received_at)),
                                'diedit_oleh'      => $create->editBy->username,
                                'dibuat_oleh'      => $create->createdBy->username,
                                'diupdate_oleh'    => $create->updatedBy->username,
                                'divalidasi_oleh'  => $create->validatedBy->username,
                                'tanggal_validasi' => date('Y-m-d H:i:s', strtotime($create->validated_at)),
                                'kategori'         => $log_category,
                                'kontributor'      => $log_contributor,
                                'subjek'           => $log_subject
                            ])
                            ->log('Menambah data koleksi');
                    } else {
                        session()->flash('failed', 'Gagal ditambahkan!');
                        $response = [
                            'status'  => 500,
                            'message' => 'Gagal ditambahkan'
                        ];
                    }
                }

                return response()->json($response);
            } else {
                if ($type == 1) {
                    $data = [
                        'title'   => 'Tambah Buku',
                        'content' => 'admin.book.create'
                    ];
                } else if ($type == 2) {
                    $data = [
                        'title'   => 'Tambah Partitur',
                        'content' => 'admin.partitur.create'
                    ];
                } else if ($type == 3) {
                    $data = [
                        'title'   => 'Tambah Peta',
                        'content' => 'admin.map.create'
                    ];
                } else if ($type == 4) {
                    $serial = Collection::where(function ($query) {
                        $query->where('type', 4)
                            ->where('status', 2)
                            ->where('parent_id', 0)
                            ->whereNotNull('received_at')
                            ->whereNotNull('received_by');
                    })
                        ->where(function ($query) {
                            if (session('library_id') != 1) {
                                $query->whereHas('city', function ($query) {
                                    $query->where('province_id', session('province_id'));
                                });
                            }
                        })
                        ->get();

                    $data = [
                        'title'   => 'Tambah Serial',
                        'serial'  => $serial,
                        'content' => 'admin.serial.create'
                    ];
                } else if ($type == 5) {
                    $data = [
                        'title'   => 'Tambah Audio',
                        'content' => 'admin.audio.create'
                    ];
                } else if ($type == 6) {
                    $data = [
                        'title'   => 'Tambah Film',
                        'content' => 'admin.film.create'
                    ];
                } else if ($type == 7) {
                    $data = [
                        'title'   => 'Tambah Buku Fisik',
                        'content' => 'admin.film.create'
                    ];
                } else {
                    $data = [
                        'title'   => 'Entri Koleksi',
                        'content' => 'admin.collection.create_manual'
                    ];
                }

                $get_deposit_head = DepositHead::get();
                $deposit_head = [];

                foreach ($get_deposit_head as $key => $value) {
                    $deposit_head[$value['category']][] = $value;
                }

                $data = array_merge($data, [
                    'category'    => Category::where('type', $type)->get(),
                    'contributor' => Contributor::where('show', 1)->orderBy('name', 'asc')->groupBy('slug')->get(),
                    'deposit_head' => $deposit_head,
                ]);

                return view('admin.layout.index', ['data' => $data]);
            }
        } catch (\Exception $e) {
            activity('collections')
                ->causedBy(session('id'))
                ->withProperties([
                    'error' => $e->getMessage(),
                ])
                ->log('Gagal Create Manual');
        }
    }

    public function lockable(Request $request, $id)
    {
        if ($request->has('lock')) {
            $locked = true;
            $desc   = 'Mengunci';
        } else {
            $locked = false;
            $desc   = 'Membuka kunci';
        }

        $collection      = Collection::find($id);
        $log_category    = [];
        $log_contributor = [];
        $log_subject     = [];

        if ($collection->collectionCategory->count() > 0) {
            foreach ($collection->collectionCategory as $cc) {
                $log_category[] = $cc->category->name;
            }
        }

        if ($collection->collectionContributor->count() > 0) {
            foreach ($collection->collectionContributor as $cc) {
                $log_contributor[] = [
                    'kontributor'      => $cc->contributor->name,
                    'nama'             => $cc->author->fullname,
                    'gelar'            => $cc->author->title,
                    'tanggal_lahir'    => $cc->author->year_of_birth,
                    'tanggal_kematian' => $cc->author->year_of_death
                ];
            }
        }

        if ($collection->collectionSubject->count() > 0) {
            foreach ($collection->collectionSubject as $cs) {
                $log_subject[] = $cs->subject->name;
            }
        }

        if ($collection) {
            $properties = json_encode([
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
                'kunci'            => $collection->lock == 1 ? 'Dikunci' : 'Tidak Dikunci',
                'akses'            => $collection->access(),
                'diedit_oleh'      => $collection->editBy->username,
                'diupdate_oleh'    => $collection->updatedBy->username,
                'divalidasi_oleh'  => $collection->validatedBy ? $collection->validatedBy->username : '',
                'tanggal_validasi' => date('Y-m-d H:i:s', strtotime($collection->validated_at)),
                'kategori'         => $log_category,
                'kontributor'      => $log_contributor,
                'subjek'           => $log_subject
            ]);

            ActivityLog::create([
                'log_name'     => 'collections',
                'description'  => $desc . ' koleksi',
                'subject_id'   => $collection->id,
                'subject_type' => "App\Models\Collection",
                'causer_id'    => session('id'),
                'causer_type'  => "App\Models\User",
                'properties'   => $properties
            ]);

            $collection->update([
                'lock'         => $locked,
                'manage_by'    => $collection->manage_by ? $collection->manage_by : session('id'),
                'edit_by'      => null,
                'updated_by'   => session('id'),
                'validated_by' => $locked ? session('id') : null,
                'validated_at' => $locked ? date('Y-m-d H:i:s') : null
            ]);

            return redirect('admin/collection/manage/' . $collection->type)->with(['success' => 'berhasil diupdate']);
        } else {
            return redirect('admin/collection/manage/' . $collection->type)->with(['failed' => 'gagal diupdate']);
        }
    }

    public function destroy($id)
    {
        $destroy = Collection::where('id', $id)->delete();
        if ($destroy) {
            $collection = Collection::where('parent_id', $id);
            if ($collection->count() > 0) {
                $collection->delete();
            }

            $response = [
                'status'  => 200,
                'message' => 'Berhasil dihapus!'
            ];

            $data = Collection::withTrashed()->find($id);
            activity('collections')
                ->performedOn(new Collection())
                ->causedBy(session('id'))
                ->withProperties(['judul' => $data->title])
                ->log('Menghapus data koleksi');
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Gagal dihapus'
            ];
        }

        return response()->json($response);
    }
}
