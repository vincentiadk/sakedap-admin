<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\User;
use App\Models\Location;
use App\Models\Publisher;
use App\Models\CopyDelivery;
use Illuminate\Http\Request;
use App\Models\CollectionCopy;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CollectionKckraShippingController extends Controller
{
    protected $location;

    public function __construct()
    {
        $this->location = Location::where('active', 1)->first();
    }

    public function index()
    {
        $library_id = session('library_id');
        $data = [
            'title'   => 'Pengiriman KCKRA',
            'content' => 'admin.kckra.shipping'
        ];

        $data = array_merge($data, [
            'library_id' => $library_id,
        ]);

        return view('admin.layout.index', ['data' => $data]);
    }

    public function getPublisher(Request $request)
    {
        $publisher = Publisher::where('id', $request->publisher_id)->with('province', 'city')->first()->toArray();
        return response()->json($publisher);
    }

    public function checkCodeDeposit(Request $request)
    {
        $library_id = session('library_id');
        $code = $request->code;
        // $check = Collection::whereHas('collectionCopy', function ($subquery) use ($code) {
        //     $subquery->whereNull('delivery_form_id')->where('code', $code);
        // })
        //     ->whereHas('collectionCopy.lib_location', function ($subquery) use ($library_id) {
        //         $subquery->where('library_id', $library_id);
        //     })->doesntHave('collectionCopy.copy_delivery');

        $check = CollectionCopy::where('code', $code)->whereNotIn('availability', ['7', '8'])
            ->whereHas('lib_location', function ($subquery) use ($library_id) {
                $subquery->where('library_id', $library_id);
            })->doesntHave('copy_delivery')->first();
        // if ($library_id == '1') {
        //     $check = $check->where('code', $request->code);
        // } else {
        //     $check = $check->where('code', $request->code);
        // }
        // $check = $check->where('code', $request->code);
        // $check = $check->first();

        if ($check) {
            $data = $check->collection->toArray();
            if ($check->collection->parent_id != 0) {
                // dd($check->collection->parent());
                $data['parent'] = $check->collection->parent()->toArray();
                $data['title'] = $check->collection->parent()->title . ' - ' . $check->collection->edition;
                $data['publication_year'] = date('Y', strtotime($check->collection->start_publication_date)) . ' - ' . date('Y', strtotime($check->collection->end_publication_date));
            } else {
                $data['parent'] = [];
            }
            // $data['total_copy'] = $check->collection->totalCopy($library_id);
            $data['total_copy'] = 1;
            $data['code'] = $check->code;
            $data['publisher_name'] = $check->collection->publisher->name;
            $data['type_collection'] = $check->collection->depositHead->shape . ' (' . $check->collection->depositHead->code . ')';
            $response = [
                'status'  => 200,
                'message' => 'Data Koleksi Ditemukan!',
                'data'    => $data
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Tidak Ditemukan Eksemplar yang Tersedia',
                'data'    => null
            ];
        }

        return response()->json($response);
    }

    public function create(Request $request)
    {
        try {
            if ($request->ajax()) {
                $datas = json_decode($request->input('data'));
                // dd($datas, $request->input('data'));
                $library_id = session('library_id');
                if (sizeof($datas) > 0) {
                    // foreach ($datas as $key => $value) {
                    //     if (isset($groupedData[$value->collection_id])) {
                    //         $groupedData[$value->collection_id] = $groupedData[$value->collection_id] + 1;
                    //     } else {
                    //         $groupedData[$value->collection_id] = 1;
                    //     }
                    // }

                    // dd($groupedData);

                    // foreach ($groupedData as $key => $value) {
                    //     $check = CollectionCopy::select('id')->where('collection_id', $key)
                    //         ->whereNull('delivery_form_id')
                    //         ->whereHas('lib_location', function ($subquery) use ($library_id) {
                    //             $subquery->where('library_id', $library_id);
                    //         })->doesntHave('copy_delivery');

                    //     $check = $check->take($value)->get();

                    //     foreach ($check as $copy) {
                    //         CopyDelivery::create([
                    //             'delivery_internal_date' => date('Y-m-d'),
                    //             'accepted_date' => null,
                    //             'system_id' => null,
                    //             'collection_copy_id' => $copy->id,
                    //             'user_delivery_id' => session('id'),
                    //             'created_by' => session('id'),
                    //             'updated_by' => session('id'),
                    //         ]);
                    //     }

                    //     unset($check);
                    // }

                    foreach ($datas as $copy) {
                        $collection_copy = CollectionCopy::where('code', $copy->code)->first();
                        if ($collection_copy) {
                            CopyDelivery::create([
                                'delivery_internal_date' => date('Y-m-d'),
                                'accepted_date' => null,
                                'system_id' => null,
                                'collection_copy_id' => $collection_copy->id,
                                'user_delivery_id' => session('id'),
                                'created_by' => session('id'),
                                'updated_by' => session('id'),
                            ]);

                            //set the availability of that copy to "dalam pengiriman ke pengolahan"
                            $collection_copy->update(['availability' => '1']);
                        }
                    }

                    $response = [
                        'status' => 200,
                        'message' => 'Berhasil Menambahkan Data Pengiriman Internal!'
                    ];
                } else {
                    $response = [
                        'status'  => 500,
                        'message' => 'Data Kosong, Mohon tambahkan data sebelum submit!',
                    ];
                }

                return response()->json($response);
            }
        } catch (Exception $e) {
            activity('collections')
                ->causedBy(session('id'))
                ->withProperties([
                    'error' => $e->getMessage(),
                ])
                ->log('Gagal Create Manual');
            return response()->json([
                'status'  => 500,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function datatableShipping(Request $request)
    {
        $column = $request->input('columns');

        // dd($request->input);
        $start  = $request->start;
        $length = $request->length;
        $order  = $column[$request->input('order.0.column')]['data'];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');
        $period = $request->input('period');
        $publication_date = explode(' - ', $period);
        $date_start = $publication_date[0];
        $date_end = $publication_date[1];
        $user_id = $request->input('user_id');
        $library_id = session('library_id');

        // dd($request->input());

        $total_data = CopyDelivery::where('user_delivery_id', $user_id)->whereBetween('delivery_internal_date', [$date_start, $date_end])->count();

        $query_data = CopyDelivery::where('user_delivery_id', $user_id)->whereBetween('delivery_internal_date', [$date_start, $date_end])->where(function ($query) use ($search) {
            if ($search) {
                $query->whereHas('copy', function ($query) use ($search) {
                    $query->where('code', 'like', "%$search%")->orWhereHas('collection', function ($query) use ($search) {
                        $query->where('title', 'like', "%$search%")
                            ->orWhere('edition', 'like', "%$search%")
                            ->orWhere('mark_national', 'like', "%$search%")
                            ->orWhere('mark_province', 'like', "%$search%");
                    });
                });
            }
        })
            ->offset($start)
            ->limit($length)
            ->orderBy($order, $dir)
            ->get();

        $total_filtered = CopyDelivery::where('user_delivery_id', $user_id)->whereBetween('delivery_internal_date', [$date_start, $date_end])->where(function ($query) use ($search) {
            if ($search) {
                $query->whereHas('copy', function ($query) use ($search) {
                    $query->where('code', 'like', "%$search%")->orWhereHas('collection', function ($query) use ($search) {
                        $query->where('title', 'like', "%$search%")
                            ->orWhere('edition', 'like', "%$search%")
                            ->orWhere('mark_national', 'like', "%$search%")
                            ->orWhere('mark_province', 'like', "%$search%");
                    });
                });
            }
        })->count();

        $response['data'] = [];
        if ($query_data <> FALSE) {
            foreach ($query_data as $val) {
                try {
                    $response['data'][] = [
                        'trk' => ($library_id == '1') ? $val->copy->collection->mark_national : $val->copy->collection->mark_province,
                        'code' => $val->copy->code,
                        'judul' => ($val->copy->collection->parent_id != '0') ? $val->copy->collection->parent()->title . ' ' . $val->copy->collection->edition : $val->copy->collection->title,
                        'tahun_terbit' => ($val->copy->collection->parent_id != '0') ? date('Y', strtotime($val->copy->collection->start_publication_date)) . ' - ' . date('Y', strtotime($val->copy->collection->end_publication_date)) : $val->copy->collection->publication_year,
                        'eksemplar' => 1,
                        'delivery_internal_date' => $val->delivery_internal_date,
                        'hapus' => ($val->created_by == session('id')) ? ' <button type="button" onclick="destroyCopyDelivery(' . $val->id . ')" class="btn btn-danger btn-sm"><i class="la la-trash"></i> Hapus</button> ' : '',
                        'collection_id' => $val->copy->collection_id
                    ];
                } catch (Exception $e) {
                    continue;
                    // dd($val->copy->collection_id);
                }
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

    public function printPdf(Request $request)
    {
        // dd($request);
        $data['title'] = $request->input('title');
        $data['coordinator'] = $request->input('coordinator');
        $data['nip'] = $request->input('nip');
        $data['period'] = $request->input('period');
        $data['sender'] = $request->input('sender');
        $data['name_sender'] = User::find($data['sender'])->username;
        $data['library_id'] = session('library_id');
        $period = explode(' - ', $data['period']);
        $date_start = $period[0];
        $date_end = $period[1];

        $data['data'] = DB::table('copy_delivery_internals as copy_delivery')
            ->join('collection_copies as copy', 'copy_delivery.collection_copy_id', '=', 'copy.id')
            ->join('collections as collection', 'copy.collection_id', '=', 'collection.id')
            ->leftJoin('collections as parent', 'parent.id', '=', 'collection.parent_id')
            ->select(
                'collection.mark_national',
                'collection.mark_province',
                'collection.start_publication_date',
                'collection.end_publication_date',
                'collection.publication_year',
                'collection.parent_id',
                'collection.edition as edition_title',
                'collection.title as title',
                'copy.code as barcode',
                'copy_delivery.delivery_internal_date',
                'parent.title as parent_title'
            )
            ->where('user_delivery_id', $data['sender'])->whereBetween('delivery_internal_date', [$date_start, $date_end])
            ->whereNull('copy_delivery.deleted_at')
            ->whereNull('parent.deleted_at')
            ->whereNull('copy.deleted_at')
            ->whereNull('collection.deleted_at')
            ->get()->toArray();

        // dd($data);
        $pdf = Pdf::loadView('admin.kckra.shipping_pdf', $data);
        return $pdf->stream($data['title'] . '.pdf', ['Attachment' => false]);
    }

    function karantina(Request $request, $id)
    {
        $delivery = CopyDelivery::find($id);

        if (!$delivery) {
            return response()->json(['message' => 'Pengiriman Eksemplar Koleksi not found.'], 404);
        }

        $delivery->delete();

        return response()->json(['message' => 'Pengiriman Eksemplar Koleksi Sudah Dikarantina.']);
    }
}
