<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Solr;
use App\Models\Author;
use App\Models\Library;
use App\Models\Setting;
use App\Models\Subject;
use App\Models\Category;
use App\Models\Director;
use App\Models\Location;
use App\Models\Publisher;
use App\Models\Collection;
use App\Models\ActivityLog;
use App\Models\Contributor;
use App\Models\DepositHead;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helper\GeneralHelper;
use App\Models\CollectionCopy;
use App\Models\CollectionMedia;
use App\Models\LibraryLocation;
use App\Models\CollectionSubject;
use App\Models\CollectionCategory;
use App\Http\Controllers\Controller;
use App\Models\CollectionContributor;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CollectionKcKraController extends Controller
{
    protected $location;

    public function __construct()
    {
        $this->location = Location::where('active', 1)->first();
    }

    public function streamFilePdf(Request $request)
    {
        $data = asset(Storage::disk($this->location->location)->url($request->file_stream));
        header("Content-type: application/octet-stream");
        header("Content-disposition: attachment;filename=$data");

        readfile($data);
    }

    public function saveTemporary(Request $request)
    {
        // dd($request->all());
        $locations = [];
        $hash_name = null;
        if ($request->has('cover_field')) {
            $cover    = $request->file('cover_field');
            $path_cover    = Storage::disk($this->location->location)->put('public/collection/serial/temporary', $cover);
            $hash_name = $cover->hashName();
            $cover_image = '<a class="btn btn-outline-secondary" href="' . asset(Storage::disk($this->location->location)->url($path_cover)) . '" data-lightbox="' . $cover->getClientOriginalName() . '" data-title="' . $cover->getClientOriginalName() . '"><img src="' . asset(Storage::disk($this->location->location)->url($path_cover)) . '" style="max-height:30px; max-width:30px;"></a>';
        } else {
            if ($request->has('old_data')) {
                try {
                    $jsonOldData = json_decode($request->old_data);
                    $path_cover = null;
                    $cover_image = $jsonOldData->cover_file;
                } catch (\Exception $e) {
                    $path_cover = null;
                    $cover_image = null;
                    $hash_name = null;
                }
            } else {
                $path_cover = null;
                $cover_image = null;
                $hash_name = null;
            }
        }

        if ($request->has('location_lib_loc_id_field')) {
            foreach ($request->location_lib_loc_id_field as $key => $llid) {
                $copy  = $request->location_copy_field[$key];
                $condition = $request->location_condition_field[$key];
                if (!empty($copy) && !empty($condition)) {
                    $locations[$key]['lib_loc_id'] = $llid;
                    $locations[$key]['copy'] = $copy;
                    $locations[$key]['condition'] = $condition;
                }
            }
        }
        return response()->json([
            'date_field'     => date('d-m-Y', strtotime($request->date_field)),
            'cover_field'    => $cover_image,
            'locations'      => $locations,
            'path_cover'      => $path_cover,
            'hash_name'      => $hash_name,
            'total_copy'      => $request->total_copy,
        ]);
    }

    public function getPublisher(Request $request)
    {
        $publisher = Publisher::where('id', $request->publisher_id)->with('province', 'city')->first()->toArray();
        return response()->json($publisher);
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
                $publisher = Publisher::with('province', 'city')->where('code_system', $result['kd_penerbit'])->first();

                if (!empty($publisher)) {
                    if (!isset($publisher->province->name) || !isset($publisher->city->name)) {
                        $response = [
                            'status'  => 500,
                            'message' => 'Provinsi atau Kota Penerbit Kosong! Mohon tambahkan provinsi "' . $result['provinsi'] . '" dan kota "' . $result['nama_kota'] . '" untuk penerbit "' . $result['nama_penerbit'] . '" melalui "Master Penerbit"',
                            'data'    => null
                        ];
                    } else {
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
                                'publisher_id'   => isset($publisher->id) ? $publisher->id : '',
                                'publisher_name' => isset($publisher->name) ? $publisher->name : '',
                                'publisher_province_id' => isset($publisher->province_id) ? $publisher->province_id : '',
                                'publisher_province' => isset($publisher->province->name) ? $publisher->province->name : '',
                                'publisher_city_id' => isset($publisher->city_id) ? $publisher->city_id : '',
                                'publisher_city' => isset($publisher->city->name) ? $publisher->city->name : '',
                            ]
                        ];
                    }
                } else {
                    $response = [
                        'status'  => 500,
                        'message' => 'Penerbit tidak ditemukan! Mohon Tambahkan Publisher "' . $result['nama_penerbit'] . '" Dengan Kode Sistem Penerbit "' . $result['kd_penerbit'] . '"',
                        'data'    => null
                    ];
                }
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

                // dd($request->all());
                // $copies_field = json_decode(base64_decode($request->copies_field));
                // dd($copies_field);
                //validation
                $collection_fields = config("collectionfield.$type");
                $getValidations = GeneralHelper::generateValidation('validation', $collection_fields);
                // dd($getValidations);
                $getMessages = GeneralHelper::generateValidation('messages', $collection_fields);

                //remove validation isbn if code is null
                if ($request->has('code')) {
                    if (empty($request->code)) {
                        unset($getValidations['code']);
                        unset($getMessages['code']);
                    }
                }

                // dd($type);
                $validator = Validator::make($request->all(), $getValidations, $getMessages);

                if ($validator->fails()) {
                    $response = [
                        'status' => 422,
                        'error'  => $validator->errors()
                    ];
                } else {
                    $getType = DepositHead::find($type);

                    // dd($getType);
                    if ($type == 7) {
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

                        $code_type = 1;
                        $physical_description = [
                            'total_page'  => $request->total_page,
                            'dimension'   => $request->dimension
                        ];
                    } else {
                        $code_type = null;
                        $type_book = null;
                        $code      = null;
                        $code_kdt  = null;
                        $physical_description = [
                            'dimension'   => $request->dimension
                        ];
                    }

                    $publisher = Publisher::find($request->publisher_id);
                    $create    = Collection::create([
                        'publisher_id'         => $publisher->id,
                        'title'                => $request->title,
                        'title_ori'            => $request->title,
                        'physical_description' => json_encode($physical_description),
                        'slug'                 => Str::slug($request->title, '-'),
                        'type'                 => $type,
                        'deposit_head_id'      => $type,
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
                        'description'          => $request->description,
                        'manual'               => 1,
                        'price'                => $request->price,
                        'currency'             => $request->currency,
                        'deposit'              => GeneralHelper::depositCollection(),
                        'copyright'            => 'Copyrights (c) ' . date('Y') . ' ' . $publisher->name,
                        'access'               => $request->access,
                        'status'               => 2,
                        'received_by'          => session('id'),
                        'received_at'          => $request->has('received_at') ? $request->received_at : date('Y-m-d'),
                        'created_by'           => session('id'),
                        'updated_by'           => session('id'),
                        'validated_by'         => session('id'),
                        'validated_at'         => date('Y-m-d H:i:s')
                    ]);

                    // dd($create);

                    if ($create) {
                        $cover    = $request->file('cover');

                        $log_category    = [];
                        $log_contributor = [];
                        $log_subject     = [];
                        $log_lib_loc     = [];

                        if ($request->has('collection_category')) {
                            foreach ($request->collection_category as $cc) {
                                $logged = CollectionCategory::create([
                                    'collection_id' => $create->id,
                                    'category_id'   => $cc
                                ]);

                                $log_category[] = $logged->category->name;
                            }
                        }

                        if ($request->has('copy_lib_loc_id') && $request->has('copy_total')) {
                            for ($i = 0; $i < $request->copy_total; $i++) {
                                $collection_id  = $create->id;
                                $copy_received_date  = $request->copy_received_date;
                                $availability_id  = $request->copy_availability;
                                $lib_loc_id  = $request->copy_lib_loc_id;
                                $condition_id  = $request->copy_condition;


                                $logged = CollectionCopy::create([
                                    'received_at' => $copy_received_date,
                                    'received_by' => session('id'),
                                    'collection_id' => $collection_id,
                                    'lib_loc_id' => $lib_loc_id,
                                    'condition' => $condition_id,
                                    'availability' => $availability_id,
                                    'created_by' => session('id'),
                                    'edit_by' => session('id'),
                                ]);

                                $log_lib_loc[] = [
                                    'location_id'      => $logged->lib_loc_id,
                                    'location_name'    => $logged->lib_location->name,
                                    'library'          => $logged->lib_location->library->name,
                                    'conditon'         => $logged->conditon,
                                    'created_by'       => $logged->created_by,
                                    'received_at'      => $logged->received_at,
                                    'collection_id'    => $logged->collection_id,
                                    'availability'     => $logged->availability,
                                ];
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

                        // dd(($request->all()));
                        if ($request->has('edition_edition_field')) {
                            foreach ($request->edition_edition_field as $key => $eef) {
                                $edition = Collection::create([
                                    'parent_id'    => $create->id,
                                    'publisher_id' => $request->publisher_id,
                                    'type'         => $type,
                                    'deposit_head_id' => $type,
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

                                // dd($request->edition_location_field);

                                // if ($request->has('edition_location_field')) {
                                //     if (isset($request->edition_location_field['location_lib_loc_id_field'])) {
                                //         foreach ($request->edition_location_field['location_lib_loc_id_field'][$key] as $k => $llid) {
                                //             // dd($llid);
                                //             $copy  = $request->edition_location_field['location_copy_field'][$key][$k];
                                //             $condition = $request->edition_location_field['location_condition_field'][$key][$k];
                                //             if (!empty($copy) && !empty($condition)) {
                                //                 CollectionCopy::create([
                                //                     'collection_id' => $edition->id,
                                //                     'lib_loc_id' => $llid,
                                //                     'copy' => $copy,
                                //                     'condition' => $condition,
                                //                 ]);
                                //             }
                                //         }
                                //     }
                                // }

                                // dd($create->id);

                                $cover_edition = $request->edition_cover_field[$key];
                                if (!empty($cover_edition)) {
                                    $name_cover = $getType->code;
                                    $ext_cover = pathinfo($cover_edition, PATHINFO_EXTENSION);
                                    $path_tmp_cover    = 'public/collection/serial/temporary/' . $cover_edition;
                                    $path_cover        = 'public/collection/' . $name_cover . '/edition/cover/' . $edition->id . '/' . Str::random(40) . '.' . $ext_cover;
                                    Storage::disk($this->location->location)->put($path_cover, Storage::disk($this->location->location)->path($path_tmp_cover));
                                    Storage::disk($this->location->location)->makeDirectory('public/collection/serial/edition/watermark/' . $edition->id);
                                    // dd(Storage::disk($this->location->location)->path($path_cover));
                                    try {
                                        $test = CollectionMedia::create([
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
                                        ]);
                                    } catch (Exception $e) {
                                        dd($e);
                                    }
                                }
                                // dd($test);
                            }
                        }


                        if (isset($cover)) {
                            $name_cover = $getType->code;
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

                            // dd($create->id);
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

                                    // $message->to($collection->publisher->email, 'edeposit@perpusnas.go.id')
                                    //     ->subject('Koleksi Divalidasi')
                                    //     ->from('edeposit@perpusnas.go.id', 'Info edeposit')
                                    //     ->setBody($template->parse($data), 'text/html');

                                    $message->to('aqshalfajarputra0226@gmail.com', 'edeposit@perpusnas.go.id')
                                        ->subject('Koleksi Divalidasi')
                                        ->from('edeposit@perpusnas.go.id', 'Info edeposit')
                                        ->setBody($template->parse($data), 'text/html');
                                });
                            }
                        }

                        # save to mysql server isbn
                        // if ($type == 1 && $connect == 'isbn') {
                        //     DB::connection('sqlsrv')
                        //         ->table('mst_isbn')
                        //         ->where('kd_penerbit_dtl', $collection->kd_penerbit_dtl)
                        //         ->update([
                        //             'received_date' => date('Y-m-d H:i:s')
                        //         ]);
                        // }

                        session()->flash('success', 'Berhasil ditambahkan!');
                        $response = ['status'  => 200, 'collection_id' => $create->id, 'message' => 'Berhasil ditambahkan'];

                        // dd($create->editBy()->toArray());

                        // activity('collections')
                        //     ->performedOn($create)
                        //     ->causedBy(session('id'))
                        //     ->withProperties([
                        //         'penerbit'         => $create->publisher->name,
                        //         'judul'            => $create->title,
                        //         'deskripsi_fisik'  => $create->physical_description,
                        //         'tipe'             => $create->type(),
                        //         'album'            => $request->album,
                        //         'tipe_buku'        => $create->typeBook(),
                        //         'edisi'            => $create->edition,
                        //         'kode'             => $create->code,
                        //         'tipe_kode'        => $create->codeType(),
                        //         'kode_kdt'         => $create->code_kdt,
                        //         'bulan_terbit'     => $create->publication_month,
                        //         'tahun_terbit'     => $create->publication_year,
                        //         'seri'             => $create->series,
                        //         'serial'           => $create->serial,
                        //         'ddc'              => $create->ddc,
                        //         'volume'           => $create->volume,
                        //         'preview'          => $create->preview,
                        //         'description'      => $create->description,
                        //         'deposit'          => $create->deposit,
                        //         'copyright'        => $create->copyright,
                        //         'akses'            => $create->access,
                        //         'status'           => $create->status(),
                        //         'tanggal_terima'   => date('Y-m-d H:i:s', strtotime($create->received_at)),
                        //         'diedit_oleh'      => isset($create->editBy->username) ? $create->editBy->username : null,
                        //         'dibuat_oleh'      => $create->createdBy->username,
                        //         'diupdate_oleh'    => $create->updatedBy->username,
                        //         'divalidasi_oleh'  => $create->validatedBy->username,
                        //         'tanggal_validasi' => date('Y-m-d H:i:s', strtotime($create->validated_at)),
                        //         'kategori'         => $log_category,
                        //         'lib_loc'          => $log_lib_loc,
                        //         'kontributor'      => $log_contributor,
                        //         'subjek'           => $log_subject
                        //     ])
                        //     ->log('Menambah data koleksi');
                    } else {
                        session()->flash('failed', 'Gagal ditambahkan!');
                        $response = [
                            'status'  => 500,
                            'message' => 'Gagal ditambahkan'
                        ];
                    }

                    // dd($response);
                }

                return response()->json($response);
            } else {
                // dd($type);
                $getType = DepositHead::find($type);
                $arrConditions = [
                    '1' => 'Sangat Baik',
                    '2' => 'Baik',
                    '3' => 'Cukup',
                    '4' => 'Rusak'
                ];
                // $getLibLocs = LibraryLocation::find();
                // dd($getType);
                $arrCategoryDH = [
                    'KC' => 'Karya Cetak',
                    'KRA' => 'Karya Rekam Analog'
                ];

                $collection_fields = config('collectionfield.' . $type);
                if (!empty($collection_fields)) {
                    $data = [
                        'title'   => 'Entri ' . $arrCategoryDH[$getType['category']] . ' - ' . $getType['shape'],
                        'content' => 'admin.kckra.create',
                        'fields' => $collection_fields,
                        'col_conditions' => $arrConditions,
                        'shape' => $getType['shape'],
                        'kategori_deposit' => $getType['category'],
                        'deposit_head_by_id' => $getType
                    ];
                } else {
                    $data = [
                        'title'   => 'Entri Koleksi',
                        'content' => 'admin.collection.create_manual'
                    ];
                }

                $get_deposit_head = DepositHead::get();
                $library_id = session('library_id');
                $deposit_head = [];
                foreach ($get_deposit_head as $key => $value) {
                    $deposit_head[$value['category']][] = $value;
                }
                $data = array_merge($data, [
                    'kategori_deposit' => $getType['category'],
                    'is_serial' => $getType['is_serial'],
                    'category'    => Category::where('type', $type)->get(),
                    'contributor' => Contributor::where('show', 1)->orderBy('name', 'asc')->get(),
                    'lib_loc' => LibraryLocation::where('library_id', $library_id)->orderBy('name', 'asc')->get(),
                    'library' => Library::find($library_id),
                    'deposit_head' => $deposit_head,
                    'availability' => [
                        'tersedia',
                        'dalam pengiriman ke pengelolaan',
                        'sedang didayagunakan',
                        'hilang',
                        'rusak',
                        'sedang diperbaiki',
                        'sedang diolah',
                        'masih di ekspedisi',
                        'sedang dicek',
                        'diterima pengelohan',
                        'diterima tim kckr',
                        'ditolak',
                    ],
                ]);

                // dd($data);

                return view('admin.layout.index', ['data' => $data]);
            }
        } catch (Exception $e) {
            activity('collections')
                ->causedBy(session('id'))
                ->withProperties([
                    'error' => $e->getMessage(),
                ])
                ->log('Gagal Create Manual');
            return response()->json(['message' => $e->getMessage()]);
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

            return redirect('admin/collection/kcra/manage/all')->with(['success' => 'berhasil diupdate']);
        } else {
            return redirect('admin/collection/kcra/manage/all')->with(['failed' => 'gagal diupdate']);
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

    public function approval($type = null)
    {
        $get_deposit_head = DepositHead::get();
        $library_id = session('library_id');
        $deposit_head = $kckra = [];
        foreach ($get_deposit_head as $key => $value) {
            $deposit_head[$value['category']][] = $value;
            if (in_array($value['category'], ['KC', 'KRA'])) {
                $kckra[$value['id']] = $value['shape'];
            }
        }
        $data = [
            'title'   => 'Daftar Penerimaan Koleksi Provinsi',
            'content' => 'admin.kckra.approve_list'
        ];

        $data = array_merge($data, [
            'types'    =>  DepositHead::whereIn('category', ['KC', 'KRA'])->pluck('shape', 'id'),
            'category'    => Category::where('type', $type)->get(),
            'contributor' => Contributor::where('show', 1)->orderBy('name', 'asc')->get(),
            'lib_loc' => LibraryLocation::where('library_id', $library_id)->orderBy('name', 'asc')->get(),
            'deposit_head' => $deposit_head,
            'type' => $type,
        ]);

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatableApproval(Request $request)
    {
        $types = DepositHead::whereIn('category', ['KC', 'KRA'])->pluck('shape', 'id')->toArray();
        $kckra = array_keys($types);
        $whereLike = [
            'edit',
            'id',
            'type',
            'manage_by',
            'lock',
            'deposit',
            'publisher_id',
            'title',
            'code',
            'updated_by',
            'validated_by',
            'received_at',
            'delete'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $totalData = Collection::where(function ($query) use ($kckra) {
            $query->where('parent_id', 0)
                ->where('status', 2)
                ->whereIn('type', $kckra)
                ->whereNotNull('received_at')
                ->whereNotNull('received_by');
        })
            ->where(function ($query) {
                if (session('library_id') != 1) {
                    $query->whereHas('publisher', function ($query) {
                        $query->where('province_id', session('province_id'));
                    });
                }
            })
            ->count();
        if (empty($search)) {

            session()->put('filter.collection.kckra.approval.title', $request->title);
            session()->put('filter.collection.kckra.approval.publisher_id', $request->publisher_id);
            session()->put('filter.collection.kckra.approval.province_id', $request->province_id);
            session()->put('filter.collection.kckra.approval.city', $request->city);
            session()->put('filter.collection.kckra.approval.publication_year', $request->publication_year);
            session()->put('filter.collection.kckra.approval.code', $request->code);
            session()->put('filter.collection.kckra.approval.manage', $request->manage);
            session()->put('filter.collection.kckra.approval.validated', $request->validated);
            session()->put('filter.collection.kckra.approval.edited', $request->edited);
            session()->put('filter.collection.kckra.approval.param', $request->param);
            session()->put('filter.collection.kckra.approval.type', $request->type);

            if ($request->param == 'annual') {
                session()->put('filter.collection.kckra.approval.year_start', $request->year_start);
                session()->put('filter.collection.kckra.approval.year_end', $request->year_end);

                session()->forget('filter.collection.kckra.approval.month_start');
                session()->forget('filter.collection.kckra.approval.month_year_start');
                session()->forget('filter.collection.kckra.approval.month_end');
                session()->forget('filter.collection.kckra.approval.month_year_end');

                session()->forget('filter.collection.kckra.approval.day_start');
                session()->forget('filter.collection.kckra.approval.day_end');
            } else if ($request->param == 'monthly') {
                session()->put('filter.collection.kckra.approval.month_start', $request->month_start);
                session()->put('filter.collection.kckra.approval.month_year_start', $request->month_year_start);
                session()->put('filter.collection.kckra.approval.month_end', $request->month_end);
                session()->put('filter.collection.kckra.approval.month_year_end', $request->month_year_end);

                session()->forget('filter.collection.kckra.approval.year_start');
                session()->forget('filter.collection.kckra.approval.year_end');

                session()->forget('filter.collection.kckra.approval.day_start');
                session()->forget('filter.collection.kckra.approval.day_end');
            } else if ($request->param == 'daily') {
                session()->put('filter.collection.kckra.approval.day_start', $request->day_start);
                session()->put('filter.collection.kckra.approval.day_end', $request->day_end);

                session()->forget('filter.collection.kckra.approval.year_start');
                session()->forget('filter.collection.kckra.approval.year_end');

                session()->forget('filter.collection.kckra.approval.month_start');
                session()->forget('filter.collection.kckra.approval.month_year_start');
                session()->forget('filter.collection.kckra.approval.month_end');
                session()->forget('filter.collection.kckra.approval.month_year_end');
            } else {
                session()->forget('filter.collection.kckra.approval.year_start');
                session()->forget('filter.collection.kckra.approval.year_end');

                session()->forget('filter.collection.kckra.approval.month_start');
                session()->forget('filter.collection.kckra.approval.month_year_start');
                session()->forget('filter.collection.kckra.approval.month_end');
                session()->forget('filter.collection.kckra.approval.month_year_end');

                session()->forget('filter.collection.kckra.approval.day_start');
                session()->forget('filter.collection.kckra.approval.day_end');
            }

            $queryData = Collection::where(function ($query) use ($kckra) {
                $query->where('parent_id', 0)
                    ->where('status', 2)
                    ->whereIn('type', $kckra)
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
                ->where(function ($query) use ($request) {
                    if ($request->title) {
                        $query->where('title', 'like', "%$request->title%");
                    }

                    if ($request->publisher_id) {
                        $query->where('publisher_id', $request->publisher_id);
                    }

                    if ($request->province_id) {
                        $query->whereHas('city', function ($query) use ($request) {
                            $query->where('province_id', $request->province_id);
                        });
                    }

                    if ($request->city) {
                        $query->whereHas('city', function ($query) use ($request) {
                            $query->where('name', 'like', "%$request->city%");
                        });
                    }

                    if ($request->publication_year) {
                        $query->where('publication_year', $request->publication_year);
                    }

                    if ($request->code) {
                        $query->where('code', 'like', "%$request->code%");
                    }

                    if ($request->manage) {
                        if ($request->manage == 1) {
                            $query->whereNotNull('manage_by');
                        } else {
                            $query->whereNull('manage_by');
                        }
                    }

                    if ($request->validated) {
                        if ($request->validated == 1) {
                            $query->whereNotNull('validated_by')
                                ->whereNotNull('validated_at')
                                ->where('lock', true);
                        } else {
                            $query->whereNull('validated_by')
                                ->whereNull('validated_at')
                                ->where('lock', false);
                        }
                    }

                    if ($request->edited) {
                        if ($request->edited == 1) {
                            $query->whereNotNull('edit_by');
                        } else {
                            $query->whereNull('edit_by');
                        }
                    }

                    if ($request->param) {
                        if ($request->param == 'annual') {
                            $query->whereYear('received_at', '>=', $request->year_start)
                                ->whereYear('received_at', '<=', $request->year_end);
                        } else if ($request->param == 'monthly') {
                            $query->whereMonth('received_at', '>=', $request->month_start)
                                ->whereYear('received_at', '>=', $request->month_year_start)
                                ->whereMonth('received_at', '<=', $request->month_end)
                                ->whereYear('received_at', '<=', $request->month_year_start);
                        } else if ($request->param == 'daily') {
                            $query->whereDate('received_at', '>=', $request->day_start)
                                ->whereDate('received_at', '<=', $request->day_end);
                        }
                    }

                    if ($request->type) {
                        $query->where('type', $request->type);
                    }
                })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();


            $totalFiltered = Collection::where(function ($query) use ($kckra) {
                $query->where('parent_id', 0)
                    ->where('status', 2)
                    ->whereIn('type', $kckra)
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
                ->where(function ($query) use ($request) {
                    if ($request->title) {
                        $query->where('title', 'like', "%$request->title%");
                    }

                    if ($request->publisher_id) {
                        $query->where('publisher_id', $request->publisher_id);
                    }

                    if ($request->province_id) {
                        $query->whereHas('city', function ($query) use ($request) {
                            $query->where('province_id', $request->province_id);
                        });
                    }

                    if ($request->city) {
                        $query->whereHas('city', function ($query) use ($request) {
                            $query->where('name', 'like', "%$request->city%");
                        });
                    }

                    if ($request->publication_year) {
                        $query->where('publication_year', $request->publication_year);
                    }

                    if ($request->code) {
                        $query->where('code', 'like', "%$request->code%");
                    }

                    if ($request->manage) {
                        if ($request->manage == 1) {
                            $query->whereNotNull('manage_by');
                        } else {
                            $query->whereNull('manage_by');
                        }
                    }

                    if ($request->validated) {
                        if ($request->validated == 1) {
                            $query->whereNotNull('validated_by')
                                ->whereNotNull('validated_at')
                                ->where('lock', true);
                        } else {
                            $query->whereNull('validated_by')
                                ->whereNull('validated_at')
                                ->where('lock', false);
                        }
                    }

                    if ($request->edited) {
                        if ($request->edited == 1) {
                            $query->whereNotNull('edit_by');
                        } else {
                            $query->whereNull('edit_by');
                        }
                    }

                    if ($request->param) {
                        if ($request->param == 'annual') {
                            $query->whereYear('received_at', '>=', $request->year_start)
                                ->whereYear('received_at', '<=', $request->year_end);
                        } else if ($request->param == 'monthly') {
                            $query->whereMonth('received_at', '>=', $request->month_start)
                                ->whereYear('received_at', '>=', $request->month_year_start)
                                ->whereMonth('received_at', '<=', $request->month_end)
                                ->whereYear('received_at', '<=', $request->month_year_start);
                        } else if ($request->param == 'daily') {
                            $query->whereDate('received_at', '>=', $request->day_start)
                                ->whereDate('received_at', '<=', $request->day_end);
                        }
                    }

                    if ($request->type) {
                        $query->where('type', $request->type);
                    }
                })
                ->count();
        } else {
            $queryData = Collection::where(function ($query) use ($kckra) {
                $query->where('parent_id', 0)
                    ->where('status', 2)
                    ->whereIn('type', $kckra)
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
                ->where(function ($query) use ($request) {
                    if ($request->title) {
                        $query->where('title', 'like', "%$request->title%");
                    }

                    if ($request->publisher_id) {
                        $query->where('publisher_id', $request->publisher_id);
                    }

                    if ($request->province_id) {
                        $query->whereHas('city', function ($query) use ($request) {
                            $query->where('province_id', $request->province_id);
                        });
                    }

                    if ($request->city) {
                        $query->whereHas('city', function ($query) use ($request) {
                            $query->where('name', 'like', "%$request->city%");
                        });
                    }

                    if ($request->publication_year) {
                        $query->where('publication_year', $request->publication_year);
                    }

                    if ($request->code) {
                        $query->where('code', 'like', "%$request->code%");
                    }

                    if ($request->manage) {
                        if ($request->manage == 1) {
                            $query->whereNotNull('manage_by');
                        } else {
                            $query->whereNull('manage_by');
                        }
                    }

                    if ($request->validated) {
                        if ($request->validated == 1) {
                            $query->whereNotNull('validated_by')
                                ->whereNotNull('validated_at')
                                ->where('lock', true);
                        } else {
                            $query->whereNull('validated_by')
                                ->whereNull('validated_at')
                                ->where('lock', false);
                        }
                    }

                    if ($request->edited) {
                        if ($request->edited == 1) {
                            $query->whereNotNull('edit_by');
                        } else {
                            $query->whereNull('edit_by');
                        }
                    }

                    if ($request->param) {
                        if ($request->param == 'annual') {
                            $query->whereYear('received_at', '>=', $request->year_start)
                                ->whereYear('received_at', '<=', $request->year_end);
                        } else if ($request->param == 'monthly') {
                            $query->whereMonth('received_at', '>=', $request->month_start)
                                ->whereYear('received_at', '>=', $request->month_year_start)
                                ->whereMonth('received_at', '<=', $request->month_end)
                                ->whereYear('received_at', '<=', $request->month_year_start);
                        } else if ($request->param == 'daily') {
                            $query->whereDate('received_at', '>=', $request->day_start)
                                ->whereDate('received_at', '<=', $request->day_end);
                        }
                    }

                    if ($request->type) {
                        $query->where('type', $request->type);
                    }
                })
                ->where(function ($query) use ($search) {
                    $query->where('deposit', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%");
                })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Collection::where(function ($query) use ($kckra) {
                $query->where('parent_id', 0)
                    ->where('status', 2)
                    ->whereIn('type', $kckra)
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
                ->where(function ($query) use ($request) {
                    if ($request->title) {
                        $query->where('title', 'like', "%$request->title%");
                    }

                    if ($request->publisher_id) {
                        $query->where('publisher_id', $request->publisher_id);
                    }

                    if ($request->province_id) {
                        $query->whereHas('city', function ($query) use ($request) {
                            $query->where('province_id', $request->province_id);
                        });
                    }

                    if ($request->city) {
                        $query->whereHas('city', function ($query) use ($request) {
                            $query->where('name', 'like', "%$request->city%");
                        });
                    }

                    if ($request->publication_year) {
                        $query->where('publication_year', $request->publication_year);
                    }

                    if ($request->code) {
                        $query->where('code', 'like', "%$request->code%");
                    }

                    if ($request->manage) {
                        if ($request->manage == 1) {
                            $query->whereNotNull('manage_by');
                        } else {
                            $query->whereNull('manage_by');
                        }
                    }

                    if ($request->validated) {
                        if ($request->validated == 1) {
                            $query->whereNotNull('validated_by')
                                ->whereNotNull('validated_at')
                                ->where('lock', true);
                        } else {
                            $query->whereNull('validated_by')
                                ->whereNull('validated_at')
                                ->where('lock', false);
                        }
                    }

                    if ($request->edited) {
                        if ($request->edited == 1) {
                            $query->whereNotNull('edit_by');
                        } else {
                            $query->whereNull('edit_by');
                        }
                    }

                    if ($request->param) {
                        if ($request->param == 'annual') {
                            $query->whereYear('received_at', '>=', $request->year_start)
                                ->whereYear('received_at', '<=', $request->year_end);
                        } else if ($request->param == 'monthly') {
                            $query->whereMonth('received_at', '>=', $request->month_start)
                                ->whereYear('received_at', '>=', $request->month_year_start)
                                ->whereMonth('received_at', '<=', $request->month_end)
                                ->whereYear('received_at', '<=', $request->month_year_start);
                        } else if ($request->param == 'daily') {
                            $query->whereDate('received_at', '>=', $request->day_start)
                                ->whereDate('received_at', '<=', $request->day_end);
                        }
                    }

                    if ($request->type) {
                        $query->where('type', $request->type);
                    }
                })
                ->where(function ($query) use ($search) {
                    $query->where('deposit', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%");
                })
                ->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                $type          = $types[$val->type];
                $response['data'][] = [
                    $nomor,
                    $type,
                    $val->mark_province,
                    $val->mark_national,
                    '<span data-toggle="tooltip" title="' . $val->publisher->name . '">' . Str::limit($val->publisher->name, 20) . '</span>',
                    '<a href="' . url('admin/collection/manage/update/' . $val->id) . '" data-toggle="tooltip" title="' . $val->title . '">' . Str::limit($val->title, 20) . '</a>',
                    $val->code ? $val->code : '<i class="la la-times text-danger"></i>',
                    !empty($val->mark_province) ? '<i class="la la-check text-success"></i>' : '<i class="la la-times text-danger"></i>',
                    !empty($val->mark_national) ? '<i class="la la-check text-success"></i>' : '<i class="la la-times text-danger"></i>',
                ];
                $nomor++;
            }
        }

        $response['recordsTotal'] = 0;
        if ($totalData <> FALSE) {
            $response['recordsTotal'] = $totalData;
        }

        $response['recordsFiltered'] = 0;
        if ($totalFiltered <> FALSE) {
            $response['recordsFiltered'] = $totalFiltered;
        }

        return response()->json($response);
    }

    function datatableSerialParents(Request $request)
    {
        $column = [
            'title',
        ];

        $start  = $request->start;
        $length = $request->length;
        $type = $request->input('type');
        $order  = $column[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        // $total_data = Collection::where('parent_id', $collection_id)->where('parent_id',)->count();

        $total_data = Collection::where(function ($query) use ($type) {
            $query->where('type', $type)
                ->where('status', 2)
                ->where('parent_id', 0)
                ->whereNotNull('received_at')
                ->whereNotNull('received_by');
        })
            ->where(function ($query) {
                if (session('library_id') != 1) {
                    $query->whereHas('publisher', function ($query) {
                        $query->where('province_id', session('province_id'));
                    });
                }
            })
            ->count();

        $query_data =  Collection::where(function ($query) use ($type, $search) {
            if ($search) {
                $query->where('title', 'like', "%$search%");
            }
            $query->where('type', $type)
                ->where('status', 2)
                ->where('parent_id', 0)
                ->whereNotNull('received_at')
                ->whereNotNull('received_by');
        })
            ->where(function ($query) {
                if (session('library_id') != 1) {
                    $query->whereHas('publisher', function ($query) {
                        $query->where('province_id', session('province_id'));
                    });
                }
            })
            ->offset($start)
            ->limit($length)
            ->orderBy($order, $dir)
            ->get();

        $total_filtered =  Collection::where(function ($query) use ($type, $search) {
            if ($search) {
                $query->where('title', 'like', "%$search%");
            }
            $query->where('type', $type)
                ->where('status', 2)
                ->where('parent_id', 0)
                ->whereNotNull('received_at')
                ->whereNotNull('received_by');
        })
            ->where(function ($query) {
                if (session('library_id') != 1) {
                    $query->whereHas('publisher', function ($query) {
                        $query->where('province_id', session('province_id'));
                    });
                }
            })
            ->count();

        $response['data'] = [];
        if ($query_data <> FALSE) {
            $nomor = $start + 1;
            foreach ($query_data as $val) {

                $response['data'][] = [
                    $nomor,
                    $val->title,
                    $val->collectionEdition()->count(),
                    '<input type="radio" onclick="getParentSerial(' . $val->id . ')">'
                ];
                $nomor++;
            }
        }

        $response['recordsTotal'] = 0;
        if ($total_data <> FALSE) {
            $response['recordsTotal'] = $total_data;
        }

        $response['recordsFiltered'] = 0;
        if ($total_filtered <> FALSE) {
            $response['recordsFiltered'] = $total_filtered;
        }

        return response()->json($response);
    }

    function testing(Request $request) {}
}
