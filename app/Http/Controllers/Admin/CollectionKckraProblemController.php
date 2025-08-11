<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\CopyRejected;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CollectionCopy;
use App\Http\Controllers\Controller;

class CollectionKckraProblemController extends Controller
{
    public function index()
    {
        $library_id = session('library_id');
        $data = [
            'title'   => 'Koleksi Ditolak KCKR Analog',
            'content' => 'admin.kckra.problem'
        ];

        $data = array_merge($data, [
            'library_id' => $library_id,
        ]);

        return view('admin.layout.index', ['data' => $data]);
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
        $publisher = $user->publisher ?? null;
        $publisher_params = $request->input('publisher_id') ?? [];

        if ($publisher_params == null && $publisher) {
            if ($publisher->getGroups() == null) {
                $publisher_id[0] =  $publisher->id;
            } else {
                $publisher_id = $publisher->getGroups()->groups->pluck('publisher_id')->toArray();
            }
        } else {
            $publisher_id = $publisher_params;
        }

        $model =  CollectionCopy::join('delivery_form', 'collection_copies.delivery_form_id', '=', 'delivery_form.id')
            ->join('collections', 'collection_copies.collection_id', '=', 'collections.id')
            ->join('copy_rejected', 'collection_copies.id', '=', 'copy_rejected.collection_copy_id')
            ->select('collection_copies.*')
            ->where('collection_copies.availability', '11')
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
            ->groupBy('collection_copies.id');

        $totalData = CollectionCopy::join('delivery_form', 'collection_copies.delivery_form_id', '=', 'delivery_form.id')
            ->where('collection_copies.availability', '=', '11')
            ->groupBy('collection_copies.id')
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

                $copyRejected = CopyRejected::where('collection_copy_id', $val->id)->first();
                if (!empty($copyRejected)) {
                    # code...
                    foreach ($copyRejected->copy_rejected_problem as $key => $cp) {
                        if ($key + 1 < $copyRejected->copy_rejected_problem->count()) {
                            $collectionProblem .= $cp->problem->name . ', ';
                        } else {
                            $collectionProblem .= $cp->problem->name;
                        }
                    }
                } else {
                }

                $response['data'][] = [
                    $val->collection->depositHead->shape,
                    '<span data-toggle="tooltip" title="' .  $val->collection->publisher->name . '">' . Str::limit($val->collection->publisher->name, 20) . '</span>',
                    '<span data-toggle="tooltip" title="' . $val->collection->title . '">' . Str::limit($val->collection->title, 20) . '</span>',
                    $val->collection->code ? $val->collection->code : '<i class="la la-times text-danger"></i>',
                    $collectionProblem,
                    $copyRejected->handling ? strtoupper($copyRejected->handling) : '',
                    $copyRejected->handling ? '<button type="button" onclick="handleReset(' . $copyRejected->id . ')" class="btn btn-warning btn-sm"> Reset</button>' :
                        '<button type="button" onclick="handleCopy(' . $copyRejected->id . ', 1)" class="btn btn-success btn-sm"> Donasi</button>
                    <button type="button" onclick="handleCopy(' . $copyRejected->id . ', 2)" class="btn btn-info btn-sm"> Ambil</button>'
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
}
