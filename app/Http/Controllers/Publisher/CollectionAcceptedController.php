<?php

namespace App\Http\Controllers\Publisher;

use App\Models\Collection;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CollectionAcceptedController extends Controller
{

    public function index($connect = null)
    {
        $publisher = User::find(session('id'))->publisher;

        $data = [
            'title'   => 'Koleksi Diterima',
            'content' => 'publisher.collection.accepted',
            'groups'        => $publisher->getGroups()
        ];

        return view('publisher.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $whereLike = [
            'publisher_id',
            'title',
            'code',
            'collection_problem',
            'problem',
            'validate_at',
            'validated_by'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $search = $request->input('search.value');

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

        $model = Collection::whereIn('publisher_id', $publisher_id)
            ->where('status', 2)
            ->where('parent_id', 0)
            ->where(function ($query) use ($request) {
                if ($request->periode_start && $request->periode_end) {
                    $query->whereBetween('created_at', [$request->periode_start, $request->periode_end]);
                } else if ($request->periode_start) {
                    $query->whereDate('created_at', '>', $request->periode_start);
                } else if ($request->periode_end) {
                    $query->whereDate('created_at', '<', $request->periode_end);
                } else {
                    $query->whereNotNull('created_at');
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
        if ($queryData <> FALSE) {
            foreach ($queryData as $val) {

                $btnAction = '
                        <a href="' . url('publisher/collection/monitoring/detail/' . $val->id) . '" class="btn btn-info btn-sm"><i class="la la-eye"></i> Detail</a>
                        <button type="button" onclick="requestFile(' . $val->id . ')" class="btn btn-success btn-sm"><i class="la la-file-archive-o"></i> Request File Original</button>
                        <a href="' . url('publisher/collection/request/receipt/' . $val->id) . '" class="btn btn-warning btn-sm" target="_blank"><i class="la la-ticket"></i> Unduh Tanda Terima </a>
                    ';

                if ($val->type == 4) {
                    $btnAction .= '<a href="' . url('publisher/collection/accepted/detail/' . $val->id) . '" class="btn btn-warning btn-sm"><i class="la la-pencil"></i> Edit</a>';
                }

                $response['data'][] = [
                    $val->type(),
                    '<span data-toggle="tooltip" title="' . $val->publisher->name . '">' . Str::limit($val->publisher->name, 20) . '</span>',
                    '<a href="' . url('publisher/collection/monitoring/detail/' . $val->id) . '" data-toggle="tooltip" title="' . $val->title . '">' . Str::limit($val->title, 20) . '</a>',
                    $val->code ? $val->code : '<i class="la la-times text-danger"></i>',
                    date('d-m-Y', strtotime($val->received_at)),
                    $btnAction
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

    public function review(Request $request, $id)
    {
        $collection = Collection::find($id);

        if ($collection->type == 4) {
            $data = [
                'title'   => 'Review Pemantauan Serial',
                'content' => 'publisher.serial.review_accepted'
            ];

            $data = array_merge($data, [
                'collection' => $collection,
                'edition'    => Collection::where('parent_id', $id)->get()
            ]);

            return view('publisher.layout.index', ['data' => $data]);
        }

        return redirect('publisher/dashboard');
    }
}
