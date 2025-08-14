<?php

namespace App\Http\Controllers\Publisher;

use App\Models\Collection;
use App\Models\CollectionProblem;
use App\Models\CollectionCopy;
use App\Models\CopyRejected;
use App\Models\CopyRejectedProblem;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class CollectionMonitoringKckraController extends Controller
{

    public function index($connect = null)
    {

        $publisher = User::find(session('id'))->publisher;

        $data = [
            'title'   => 'Monitoring Koleksi KCKR Analog',
            'content' => 'publisher.collection.monitor_kckra',
            'groups'        => $publisher->getGroups()
        ];

        return view('publisher.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $whereLike = [
            'id',
            'publisher_id',
            'title',
            'code',
            'problem',
            'validate_at',
            'validated_by'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $search = $request->input('search.value');
        $order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');

        $user = User::find(session('id'));
        $publisher = $user->publisher;

        $publisher_params  = $request->input('publisher_id');

        if ($publisher_params == null) {
            if ($publisher->getGroups() == null) {
                $publisher_id[0] =  $publisher->id;
            } else {
                $publisher_id = $publisher->getGroups()->groups->pluck('publisher_id');
            }
        } else {
            $publisher_id =  $publisher_params;
        }

        $model =  CollectionCopy::join('delivery_form', 'collection_copies.delivery_form_id', '=', 'delivery_form.id')
            ->join('collections', 'collection_copies.collection_id', '=', 'collections.id')
            ->select(
                'collection_copies.*',
                DB::raw("SUM(CASE WHEN delivery_form.library_id = 1 THEN 1 ELSE 0 END) AS perpusnas_sent_count"),
                DB::raw("SUM(CASE WHEN delivery_form.library_id = 1 AND collection_copies.availability IN ('9','10') THEN 1 ELSE 0 END) AS perpusnas_accept_count"),
                DB::raw("SUM(CASE WHEN delivery_form.library_id <> 1 THEN 1 ELSE 0 END) AS province_sent_count"),
                DB::raw("SUM(CASE WHEN delivery_form.library_id <> 1 AND collection_copies.availability IN ('9','10') THEN 1 ELSE 0 END) AS province_accept_count"),
            )
            // ->whereNotNull('collection_copies.availability')
            ->whereIn('delivery_form.publisher_id', $publisher_id)
            ->where(function ($query) use ($request) {

                if ($request->title) {
                    $query->where('collections.title', 'like', "%{$request->title}%");
                }

                if ($request->code) {
                    $query->where('collections.code', $request->code);
                }

                if ($request->periode_start && $request->periode_end) {
                    $query->whereBetween('delivery_date', [$request->periode_start, $request->periode_end]);
                } else if ($request->periode_start) {
                    $query->whereDate('delivery_date', '>', $request->periode_start);
                } else if ($request->periode_end) {
                    $query->whereDate('delivery_date', '<', $request->periode_end);
                } else {
                    $query->whereNotNull('delivery_date');
                }
            })
            ->groupBy('collection_copies.collection_id');

        $totalData = CollectionCopy::join('delivery_form', 'collection_copies.delivery_form_id', '=', 'delivery_form.id')
            // ->where('collection_copies.availability', '=', 'Ditolak')
            ->groupBy('collection_copies.collection_id')
            ->get()
            ->count();

        if (empty($search)) {
            $totalFiltered = $model
                ->get()
                ->count();
            $queryData = $model->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $model->where(function ($query) use ($search) {
                $query->whereHas('collection', function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%");
                });
            })
                ->get();
            $totalFiltered = $model->count();
            $queryData = $model->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            foreach ($queryData as $val) {
                $collectionProblem = '';
                // $problem = CopyRejected::where('collection_copy_id', $val->id)->get();
                // foreach($problem  as $key => $cp) {
                //     if($key + 1 < $val->collectionProblem->count()) {
                //         $collectionProblem .= $cp->problem->name . ', ';
                //     } else {
                //         $collectionProblem .= $cp->problem->name;
                //     }
                // }

                // dd($val->collection);
                // dd($val->collection->depositHead);

                $response['data'][] = [
                    !empty($val->collection->depositHead) ? $val->collection->depositHead->shape : "",
                    '<span data-toggle="tooltip" title="' .  $val->collection->publisher->name . '">' . Str::limit($val->collection->publisher->name, 20) . '</span>',
                    '<span data-toggle="tooltip" title="' . $val->collection->title . '">' . Str::limit($val->collection->title, 20) . '</span>',
                    $val->collection->code ? $val->collection->code : '<i class="la la-times text-danger"></i>',
                    $val->perpusnas_sent_count,
                    $val->perpusnas_accept_count,
                    $val->province_sent_count,
                    $val->province_accept_count,
                    ' <button type="button" onclick="show(' . $val->collection->id . ')" class="btn btn-info btn-sm"><i class="la la-eye"></i> Detail</button>'
                ];
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

    public function show($collection_id)
    {
        $data = CollectionCopy::where('collection_copies.collection_id', $collection_id)
            ->get();
        $response = [];
        foreach ($data as $key => $value) {
            $collectionProblem = '';
            if ($value->availability == '11') {
                $copyRejected = CopyRejected::where('collection_copy_id', $value->id)->first();
                if (!empty($copyRejected)) {

                    foreach ($copyRejected->copy_rejected_problem as $k => $v) {
                        if ($k + 1 < $copyRejected->copy_rejected_problem->count()) {
                            $collectionProblem .= $v->problem->name . ', ';
                        } else {
                            $collectionProblem .= $v->problem->name;
                        }
                    }
                }
            }
            $response[] = [
                'id' => $value->id,
                'receipt_no' => $value->delivery_form->receipt_no ?? '',
                'delivery_date' => $value->delivery_form->delivery_date ?? '',
                'accepted_date' => $value->delivery_form->accepted_date ?? '',
                'status_delivery' => $value->delivery_form->status ?? '',
                'status_collection' => ucwords($value->availability_text() ?? ''),
                'problem' => $collectionProblem,
            ];
        }

        return response()->json($response);
    }
}
