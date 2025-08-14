<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Collection;
use App\Models\CollectionCategory;
use App\Models\CollectionCopy;
use App\Models\CollectionContributor;
use App\Models\CollectionMedia;
use App\Models\Contributor;
use App\Models\DepositHead;
use App\Models\Library;
use App\Models\Location;
use App\Models\Publisher;
use App\Models\Category;
use App\Models\Solr;
use App\Models\User;
use App\Models\Expedition;
use App\Models\DeliveryForm;
use App\Helper\GeneralHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CollectionDeliveryFormController extends Controller
{

    protected $location;

    public function __construct()
    {
        $this->location = Location::where('active', 1)->first();
    }

    public function formDelivery(Request $request)
    {

        if ($request->ajax()) {
            return $this->createDelivery($request);
        }

        $user = User::find(session('id'));
        $getDepositHead = DepositHead::whereIn('category', ['KC', 'KRA'])->get();
        $deposit_head = [];
        $deposit_head_serial = [];
        foreach ($getDepositHead as $key => $value) {
            $deposit_head[$value->id] = $value->shape;
            if ($value->is_serial == 1) {
                $deposit_head_serial[] = $value->id;
            }
        }

        $groups = null;
        if ($user->publisher->getGroups()) {
            $groups = $user->publisher->getGroups()->groups;
        }
        if ($groups == null) {
            $publisher_groups = false;
            $publisher_user = $user->publisher;
        } else {
            $publisher_groups = $groups->where('publisher_id', '!=', $user->publisher->id)->all();
            $publisher_user = $groups->where('publisher_id', $user->publisher->id)->first();
            array_unshift($publisher_groups, $publisher_user);
        }

        $library = Library::where('province_id', $user->publisher->province_id)->first();
        $data = [
            'title' => 'Pengiriman KC dan KR Analog',
            'content' => 'publisher.collection.form_delivery',
            'publisher' => $user->publisher,
            'publisher_groups' => $publisher_groups,
            'deposit_head' => $deposit_head,
            'deposit_head_serial' => $deposit_head_serial,
            'category' => Category::where('type', 1)->get(),
            'expedition' => Expedition::get(),
            'library' => $library,
            'contributor' => Contributor::where('show', 1)->orderBy('name', 'asc')->get(),
        ];

        // dd($data);

        return view('publisher.layout.index', ['data' => $data]);
    }

    public function checkCodeIsbn(Request $request)
    {

        $user = User::find(session('id'));
        $dataDb = Collection::where(DB::raw("REPLACE(code, '-', '')"), str_replace('-', '', $request->code))
            ->where('publisher_id', $user->publisher->id)
            ->first();

        $exemplar = 2;
        if ($request->library_id <> 1) {
            $exemplar = 1;
        }

        if (!empty($dataDb)) {
            $collection = $dataDb;

            if ($request->library_id == 1) {
                $checkCopy = CollectionCopy::join('delivery_form', 'collection_copies.delivery_form_id', '=', 'delivery_form.id')
                    ->where('collection_copies.collection_id', '=', $collection['id'])
                    ->where('collection_copies.availability', '<>', '11')
                    ->where('delivery_form.library_id', '=', '1')
                    ->get()
                    ->count();

                if ($checkCopy >= 2) {
                    $response = [
                        'status' => 500,
                        'message' => 'Koleksi sudah dikirim ke Perpusnas sebelumnya!',
                        'data' => null,
                    ];

                    return response()->json($response);
                }
            } else {
                $checkCopy = CollectionCopy::join('delivery_form', 'collection_copies.delivery_form_id', '=', 'delivery_form.id')
                    ->where('collection_copies.collection_id', '=', $collection['id'])
                    ->where('collection_copies.availability', '<>', '11')
                    ->where('delivery_form.library_id', '<>', '1')
                    ->get()
                    ->count();

                if ($checkCopy >= 1) {
                    $response = [
                        'status' => 500,
                        'message' => 'Koleksi sudah dikirim ke Provinsi sebelumnya!',
                        'data' => null,
                    ];

                    return response()->json($response);
                }
            }

            $publisher = Publisher::with('province', 'city')->where('id', $collection['publisher_id'])->first();

            $log_contributor = [];
            $kepeng = [];
            if ($collection->collectionContributor->count() > 0) {
                foreach ($collection->collectionContributor as $cc) {
                    $kepeng[] = $cc->author->fullname;
                    $log_contributor[] = [
                        'id_kontributor' => $cc->contributor->id,
                        'kontributor' => $cc->contributor->name,
                        'id_author' => $cc->author->id,
                        'author' => $cc->author->fullname,
                        'author_title' => $cc->author->title,
                        'author_birth' => $cc->author->year_of_birth,
                        'author_death' => $cc->author->year_of_death,
                    ];
                }
            }

            $log_category = [];
            if ($collection->collectionCategory->count() > 0) {
                foreach ($collection->collectionCategory as $cc) {
                    $log_category[] = $cc->category->id;
                }
            }

            $collectionMedia = CollectionMedia::where('collection_id', $collection['id'])->where('type', 1)->first();

            $response = [
                'status' => 200,
                'message' => 'Data ditemukan!',
                'data' => [
                    'collection_id' => $collection['id'],
                    'deposit_head_id' => $collection['deposit_head_id'] ?? '7',
                    'code' => $collection['code'],
                    'title' => $collection['title'],
                    'tahun_terbit' => $collection['publication_year'],
                    'bulan_terbit' => $collection['publication_month'],
                    'kepeng' => !empty($kepeng) ? implode('; ', $kepeng) : '',
                    'sinopsis' => $collection['description'],
                    'edisi' => $collection['edition'],
                    'jml_hlm' => $collection->physicalDescription() ? $collection->physicalDescription()->total_page : '',
                    'subjek' => '',
                    'seri' => $collection['serial'],
                    'dimension' => $collection->physicalDescription() ? $collection->physicalDescription()->dimension : '',
                    'publisher_id' => $publisher ? $publisher->id : '',
                    'publisher_name' => $publisher ? $publisher->name : '',
                    'publisher_province_id' => $publisher ? $publisher->province_id : '',
                    'publisher_province' => $publisher ? $publisher->province->name : '',
                    'publisher_city_id' => $publisher ? $publisher->city_id : '',
                    'publisher_city' => $publisher ? $publisher->city->name : '',
                    'price' => $collection['price'],
                    'category' => $log_category,
                    'contributor' => $log_contributor,
                    'exemplar' => $exemplar,
                    'cover_url' => $collectionMedia ? asset(Storage::disk($this->location->location)->url($collectionMedia->link)) : '',
                ],
            ];
        } else {
            $dataSolr = Solr::data('isbn', 'complete', ['code' => str_replace('-', '', $request->code)]);
            if (count($dataSolr) > 0) {
                //\Log::info($dataSolr[0]['received_date']);
                //\Log::info($request->library_id);
                if ($request->library_id == 1 && $dataSolr[0]['received_date'] != null) {
                    $response = [
                        'status' => 500,
                        'message' => 'Koleksi sudah pernah dikirimkan ke Perpusnas!',
                        'data' => null,
                    ];
                    return response()->json($response);
                }

                $result = $dataSolr[0];
                $code = $result['prefix_element'] . '-' . $result['publisher_element'] . '-' . $result['item_element'] . '-' . $result['check_digit'];
                $publisher = Publisher::with('province', 'city')->where('code_system', $result['kd_penerbit'])->first();

                $log_contributor = [];
                if (isset($result['kepeng'])) {
                    $exp_kepeng = explode(', ', $result['kepeng']);
                    foreach ($exp_kepeng as $key => $value) {
                        $log_contributor[] = [
                            'id_kontributor' => null,
                            'kontributor' => null,
                            'id_author' => null,
                            'author' => $value,
                            'author_title' => null,
                            'author_birth' => null,
                            'author_death' => null,
                        ];
                    }
                }

                if (!empty($publisher)) {
                    $response = [
                        'status' => 200,
                        'message' => 'Data ditemukan!',
                        'data' => [
                            'collection_id' => null,
                            'deposit_head_id' => null,
                            'code' => $code,
                            'title' => $result['title'],
                            'tahun_terbit' => isset($result['tahun_terbit']) ? $result['tahun_terbit'] : '',
                            'bulan_terbit' => isset($result['bulan_terbit']) ? $result['bulan_terbit'] : '',
                            'kepeng' => isset($result['kepeng']) ? $result['kepeng'] : '',
                            'sinopsis' => isset($result['sinopsis']) ? $result['sinopsis'] : '',
                            'edisi' => isset($result['edisi']) ? $result['edisi'] : '',
                            'jml_hlm' => isset($result['jml_hlm']) ? $result['jml_hlm'] : '',
                            'subjek' => isset($result['subjek']) ? $result['subjek'] : '',
                            'seri' => isset($result['seri']) ? $result['seri'] : '',
                            'dimension' => isset($result['dimension']) ? $result['dimension'] : '',
                            'publisher_id' => $publisher ? $publisher->id : '',
                            'publisher_name' => $publisher ? $publisher->name : '',
                            'publisher_province_id' => $publisher ? $publisher->province_id : '',
                            'publisher_province' => $publisher ? $publisher->province->name : '',
                            'publisher_city_id' => $publisher ? $publisher->city_id : '',
                            'publisher_city' => $publisher ? $publisher->city->name : '',
                            'price' => isset($result['price']) ? $result['price'] : '',
                            'category' => null,
                            'contributor' => $log_contributor,
                            'exemplar' => $exemplar,
                            'cover_url' => null,
                        ],
                    ];
                } else {
                    $response = [
                        'status' => 500,
                        'message' => 'Penerbit tidak ditemukan! Mohon Tambahkan Publisher "' . $result['nama_penerbit'] . '" Dengan Kode Sistem Penerbit "' . $result['kd_penerbit'] . '"',
                        'data' => null,
                    ];
                }
            } else {
                $response = [
                    'status' => 500,
                    'message' => 'Data tidak ditemukan!',
                    'data' => null,
                ];
            }
        }

        return response()->json($response);
    }

    public function createDelivery(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'expedition_id' => 'required',
            'library_id' => 'required',
            'delivery_date' => 'required',
            'receipt_no' => 'required',
        ], [
            'expedition_id.required' => 'Ekpedisi wajib di isi!',
            'library_id.required' => 'Tujuan wajib di isi!',
            'delivery_date.required' => 'Tanggal kirim wajib di isi!',
            'receipt_no.required' => 'No Resi wajib di isi!',
        ]);

        if ($validation->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validation->errors()
            ];

            return response()->json($response);
        }

        $delivery = DeliveryForm::where('receipt_no', $request->receipt_no)
            ->first();
        if (!empty($delivery)) {
            if ($delivery->expedition_id <> '1' && $delivery->status == 'DELIVERED') {
                $response = [
                    'status' => 422,
                    'error'  => 'No resi sudah pernah dikirim sebelumnya'
                ];

                return response()->json($response);
            }
        }

        if (!isset($request->collection_copy)) {
            $response = [
                'status' => 422,
                'error' => ['Wajib menyertakan koleksi'],
            ];
        } else {
            //\Log::info("create deliver called");
            $publisher = User::find(session('id'))->publisher;
            try {
                $delivery = DeliveryForm::create([
                    'publisher_id' => $publisher->id,
                    'expedition_id' => $request->expedition_id,
                    'library_id' => $request->library_id,
                    'delivery_date' => $request->delivery_date,
                    'receipt_no' => $request->receipt_no,
                    'status' => 'DRAFT',
                ]);

                foreach ($request->collections as $key => $value) {


                    $physical_description = [
                        'total_page' => $value['jml_hlm'],
                        'dimension' => $value['dimension'],
                    ];

                    $create = Collection::updateOrCreate(
                        [
                            'id' => $value['collection_id']
                        ],
                        [
                            'publisher_id' => $publisher->id,
                            'title' => $value['title'],
                            'title_ori' => $value['title'],
                            'slug' => Str::slug($value['title'], '-'),
                            'deposit_head_id' => $value['deposit_head_id'] ?? '7',
                            'parent_id' => $value['parent_id'] ?? '0',
                            'type' => $value['deposit_head_id'] ?? 1,
                            // 'type_book' => $type_book,
                            // 'ddc' => $ddc,
                            // 'kepeng' => $request->author_book,
                            'series' => $value['seri'],
                            'edition' => $value['edisi'],
                            'code' => $value['code'],
                            'price' => $value['price'],
                            // 'code_type' => 1,
                            // 'code_kdt' => $code_kdt,
                            'publication_year' => $value['tahun_terbit'],
                            'publication_month' => $value['bulan_terbit'],
                            // 'preview' => $request->preview_book,
                            'description' => $value['sinopsis'],
                            // 'access' => $request->access,
                            'city_id' => $value['publisher_city_id'],
                            'physical_description' => json_encode($physical_description),
                            // 'manual' => 1,
                            'deposit' => GeneralHelper::depositCollection(),
                            'copyright' => 'Copyrights (c) ' . date('Y') . ' ' . $publisher->name,
                            'status' => 1,
                            'created_by' => session('id'),
                            'updated_by' => session('id'),
                        ]
                    );

                    if (isset($value['category'])) {
                        foreach ($value['category'] as $cc) {
                            CollectionCategory::create([
                                'collection_id' => $create->id,
                                'category_id' => $cc,
                            ]);
                        }
                    }

                    if (isset($value['contributor'])) {
                        CollectionContributor::where('collection_id', $create->id)->delete();

                        foreach ($value['contributor'] as $k => $v) {
                            $name  = $v['author'];
                            $title = $v['author_title'];
                            if (!empty($name)) {
                                $authorCheck = Author::updateOrCreate([
                                    'fullname' => $name,
                                    'title'    => $title,
                                    'slug'     => Str::slug($name, '-'),
                                ], [
                                    'year_of_birth' => $v['author_birth'],
                                    'year_of_death' => $v['author_death']
                                ]);

                                $author = Author::where('fullname', $name)
                                    ->where('title', $title)
                                    ->where('slug', Str::slug($name, '-'))
                                    ->where('year_of_birth', $v['author_birth'])
                                    ->where('year_of_death', $v['author_death'])
                                    ->first();

                                $logged = CollectionContributor::create([
                                    'collection_id'  => $create->id,
                                    'contributor_id' => $v['id_kontributor'],
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

                    if (isset($value['cover']) && $value['cover'] <> 'undefined') {
                        $cover = $value['cover'];
                        $name_cover = $create->depositHead->code;
                        $path_cover    = Storage::disk($this->location->location)->put('public/collection/' . $name_cover . '/edition/cover/' . $create->id, $cover);
                        $cover_image = '<a class="btn btn-outline-secondary" href="' . asset(Storage::disk($this->location->location)->url($path_cover)) . '" data-lightbox="' . $cover->getClientOriginalName() . '" data-title="' . $cover->getClientOriginalName() . '"><img src="' . asset(Storage::disk($this->location->location)->url($path_cover)) . '" style="max-height:30px; max-width:30px;"></a>';

                        try {
                            //delete previous cover
                            $this->deleteCollectionMedia(1, $create->id);
                            //insert new cover
                            $createCover = CollectionMedia::create([
                                'collection_id' => $create->id,
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

                    $count = 1;
                    if (isset($value['exemplar'])) {
                        $count = $value['exemplar'];
                    }

                    for ($i = 0; $i < $count; $i++) {
                        $delivery->collectionCopy()->create([
                            'collection_id' => $create->id,
                        ]);
                    }
                }

                session()->flash('success', 'Berhasil ditambahkan!');
                $response = ['status' => 200, 'id' => $delivery->id];
            } catch (\Exception $e) {
                $data[] = $e->getMessage();
                $response = [
                    'status' => 422,
                    'error' => $data,
                ];
            }
        }


        return response()->json($response);
    }

    public function editDelivery(Request $request, $id)
    {
        // dd($request);
        $validation = Validator::make($request->all(), [
            'expedition_id' => 'required',
            'library_id' => 'required',
            'delivery_date' => 'required',
            'receipt_no' => 'required',
        ], [
            'expedition_id.required' => 'Ekpedisi wajib di isi!',
            'library_id.required' => 'Tujuan wajib di isi!',
            'delivery_date.required' => 'Tanggal kirim wajib di isi!',
            'receipt_no.required' => 'No Resi wajib di isi!',
        ]);

        if ($validation->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validation->errors()
            ];

            return response()->json($response);
        }
        //\Log::info($request->all());
        $delivery = DeliveryForm::find($request->input('delivery_id'));
        //\Log::info($delivery);
        if ($delivery->expedition_id <> '1' && $delivery->status == 'DELIVERED') {
            $response = [
                'status' => 422,
                'error'  => 'No resi sudah pernah dikirim sebelumnya'
            ];

            return response()->json($response);
        }

        if (!isset($request->collection_copy)) {
            $response = [
                'status' => 422,
                'error' => ['Wajib menyertakan koleksi'],
            ];
        } else {
            //\Log::info("create deliver called");
            $publisher = User::find(session('id'))->publisher;
            try {
                // dd($request->all());
                $delivery = DeliveryForm::find($id);
                $delivery->expedition_id = $request->expedition_id;
                $delivery->receipt_no = $request->receipt_no;
                $delivery->delivery_date = $request->delivery_date;
                $delivery->library_id = $request->library_id;
                $delivery->save();

                $delivery->collectionCopy()->delete();

                foreach ($request->collections as $key => $value) {

                    $physical_description = [
                        'total_page' => $value['jml_hlm'],
                        'dimension' => $value['dimension'],
                    ];

                    $create = Collection::updateOrCreate(
                        [
                            'id' => $value['collection_id']
                        ],
                        [
                            'publisher_id' => $publisher->id,
                            'title' => $value['title'],
                            'title_ori' => $value['title'],
                            'slug' => Str::slug($value['title'], '-'),
                            'deposit_head_id' => $value['deposit_head_id'] ?? '7',
                            'parent_id' => $value['parent_id'] ?? 0,
                            'type' => $value['deposit_head_id'] ?? '7',
                            // 'type_book' => $type_book,
                            // 'ddc' => $ddc,
                            // 'kepeng' => $request->author_book,
                            'series' => $value['seri'],
                            'edition' => $value['edisi'],
                            'code' => $value['code'],
                            'price' => $value['price'],
                            // 'code_type' => 1,
                            // 'code_kdt' => $code_kdt,
                            'publication_year' => $value['tahun_terbit'],
                            'publication_month' => $value['bulan_terbit'],
                            // 'preview' => $request->preview_book,
                            'description' => $value['sinopsis'],
                            // 'access' => $request->access,
                            'city_id' => $value['publisher_city_id'],
                            'physical_description' => json_encode($physical_description),
                            // 'manual' => 1,
                            'deposit' => GeneralHelper::depositCollection(),
                            'copyright' => 'Copyrights (c) ' . date('Y') . ' ' . $publisher->name,
                            'status' => 1,
                            'created_by' => session('id'),
                            'updated_by' => session('id'),
                        ]
                    );

                    if (isset($value['category'])) {
                        foreach ($value['category'] as $cc) {
                            $createCollectionCategory = CollectionCategory::firstOrCreate(
                                [
                                    'collection_id' => $create->id,
                                    'category_id' => $cc,
                                ]
                            );
                        }
                    }

                    if (isset($value['contributor'])) {
                        CollectionContributor::where('collection_id', $create->id)->delete();

                        foreach ($value['contributor'] as $k => $v) {
                            $name  = $v['author'];
                            $title = $v['author_title'];
                            if (!empty($name)) {
                                $authorCheck = Author::updateOrCreate([
                                    'fullname' => $name,
                                    'title'    => $title,
                                    'slug'     => Str::slug($name, '-'),
                                ], [
                                    'year_of_birth' => $v['author_birth'],
                                    'year_of_death' => $v['author_death']
                                ]);

                                $author = Author::where('fullname', $name)
                                    ->where('title', $title)
                                    ->where('slug', Str::slug($name, '-'))
                                    ->where('year_of_birth', $v['author_birth'])
                                    ->where('year_of_death', $v['author_death'])
                                    ->first();

                                $logged = CollectionContributor::create([
                                    'collection_id'  => $create->id,
                                    'contributor_id' => $v['id_kontributor'],
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

                    if (isset($value['cover']) && $value['cover'] <> 'undefined') {
                        $cover = $value['cover'];
                        $name_cover = $create->depositHead->code;
                        $path_cover    = Storage::disk($this->location->location)->put('public/collection/' . $name_cover . '/edition/cover/' . $create->id, $cover);
                        $cover_image = '<a class="btn btn-outline-secondary" href="' . asset(Storage::disk($this->location->location)->url($path_cover)) . '" data-lightbox="' . $cover->getClientOriginalName() . '" data-title="' . $cover->getClientOriginalName() . '"><img src="' . asset(Storage::disk($this->location->location)->url($path_cover)) . '" style="max-height:30px; max-width:30px;"></a>';

                        try {
                            //delete previous cover
                            $this->deleteCollectionMedia(1, $create->id);
                            //insert new cover
                            $createCover = CollectionMedia::create([
                                'collection_id' => $create->id,
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

                    $count = 1;
                    if (isset($value['exemplar'])) {
                        $count = $value['exemplar'];
                    }

                    for ($i = 0; $i < $count; $i++) {
                        $delivery->collectionCopy()->create([
                            'collection_id' => $create->id,
                        ]);
                    }
                }

                session()->flash('success', 'Berhasil ditambahkan!');
                $response = ['status' => 200, 'id' => $delivery->id];
            } catch (\Exception $e) {
                dd($e);
                $data[] = $e->getMessage();
                $response = [
                    'status' => 422,
                    'error' => $data,
                ];
            }
        }


        return response()->json($response);
    }

    public function sendDelivery(Request $request, $id)
    {

        try {
            $delivery = DeliveryForm::find($id);
            $delivery->status = 'DELIVERED';
            $delivery->save();

            $delivery->collectionCopy->each(function ($copy) {
                $copy->update(['availability' => '7']);
            });

            session()->flash('success', 'Berhasil dikirim!');
            $response = ['status' => 200, 'id' => $delivery->id];
        } catch (\Exception $e) {
            $data[] = $e->getMessage();
            $response = [
                'status' => 422,
                'error' => $data,
            ];
        }

        return response()->json($response);
    }

    function deleteCollectionMedia($type, $collection_id)
    {
        CollectionMedia::where('type', $type)->where('collection_id', $collection_id)->delete();
    }
}
