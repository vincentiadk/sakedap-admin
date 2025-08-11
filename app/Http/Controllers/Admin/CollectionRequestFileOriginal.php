<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CollectionRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Jobs\SendEmailRequestFileOriginal;

class CollectionRequestFileOriginal extends Controller
{
    public function index()
    {
        $data = [
            'title'   => 'Request File Original',
            'total_1_request' => CollectionRequest::where('status', 1)->count(),
            'total_2_request' => CollectionRequest::where('status', 2)->count(),
            'total_3_request' => CollectionRequest::where('status', 3)->count(),
            'content' => 'admin.collection.request_file'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {

        $whereLike = [
            'publisher_id',
            'title',
            'code',
            'created_at'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $search = $request->input('search.value');

        $totalData = CollectionRequest::select('collections.title as collection_title', 'collection_requests.*', 'publishers.name as publisher_name')
            ->where(function ($query) use ($request) {
                if ($request->publisher_id) {
                    $query->where('collections.publisher_id', $request->publisher_id);
                }

                if ($request->periode_start && $request->periode_end) {
                    $query->whereBetween('collection_requests.created_at', [$request->periode_start, $request->periode_end]);
                } else if ($request->periode_start) {
                    $query->whereDate('collection_requests.created_at', '>', $request->periode_start);
                } else if ($request->periode_end) {
                    $query->whereDate('collection_requests.created_at', '<', $request->periode_end);
                } else {
                    $query->whereNotNull('collection_requests.created_at');
                }
            })
            ->where(function ($query) use ($search) {
                $query->where('collections.title', 'like', "%{$search}%")
                    ->orWhere('collections.code', 'like', "%{$search}%");
            })
            ->leftJoin('collections', 'collection_requests.collection_id', 'collections.id')
            ->leftJoin('publishers', 'collections.publisher_id', 'publishers.id')
            ->count();

        if (empty($search)) {
            $queryData =  CollectionRequest::select('collections.title as collection_title', 'collection_requests.*', 'publishers.name as publisher_name')
                ->where(function ($query) use ($request) {
                    if ($request->publisher_id) {
                        $query->where('collections.publisher_id', $request->publisher_id);
                    }

                    if ($request->periode_start && $request->periode_end) {
                        $query->whereBetween('collection_requests.created_at', [$request->periode_start, $request->periode_end]);
                    } else if ($request->periode_start) {
                        $query->whereDate('collection_requests.created_at', '>', $request->periode_start);
                    } else if ($request->periode_end) {
                        $query->whereDate('collection_requests.created_at', '<', $request->periode_end);
                    } else {
                        $query->whereNotNull('collection_requests.created_at');
                    }
                })
                ->leftJoin('collections', 'collection_requests.collection_id', 'collections.id')
                ->leftJoin('publishers', 'collections.publisher_id', 'publishers.id')
                ->offset($start)
                ->limit($length)
                ->oldest()
                ->get();
            $totalFiltered = CollectionRequest::select('collections.title as collection_title', 'collection_requests.*', 'publishers.name as publisher_name')
                ->where(function ($query) use ($request) {
                    if ($request->publisher_id) {
                        $query->where('collections.publisher_id', $request->publisher_id);
                    }

                    if ($request->periode_start && $request->periode_end) {
                        $query->whereBetween('collection_requests.created_at', [$request->periode_start, $request->periode_end]);
                    } else if ($request->periode_start) {
                        $query->whereDate('collection_requests.created_at', '>', $request->periode_start);
                    } else if ($request->periode_end) {
                        $query->whereDate('collection_requests.created_at', '<', $request->periode_end);
                    } else {
                        $query->whereNotNull('collection_requests.created_at');
                    }
                })
                ->leftJoin('collections', 'collection_requests.collection_id', 'collections.id')
                ->leftJoin('publishers', 'collections.publisher_id', 'publishers.id')
                ->count();
        } else {
            $queryData =  CollectionRequest::select('collections.title as collection_title', 'collection_requests.*', 'publishers.name as publisher_name')
                ->where(function ($query) use ($request) {
                    if ($request->publisher_id) {
                        $query->where('collections.publisher_id', $request->publisher_id);
                    }

                    if ($request->periode_start && $request->periode_end) {
                        $query->whereBetween('collection_requests.created_at', [$request->periode_start, $request->periode_end]);
                    } else if ($request->periode_start) {
                        $query->whereDate('collection_requests.created_at', '>', $request->periode_start);
                    } else if ($request->periode_end) {
                        $query->whereDate('collection_requests.created_at', '<', $request->periode_end);
                    } else {
                        $query->whereNotNull('collection_requests.created_at');
                    }
                })
                ->where(function ($query) use ($search) {
                    $query->where('collections.title', 'like', "%{$search}%")
                        ->orWhere('collections.code', 'like', "%{$search}%");
                })
                ->leftJoin('collections', 'collection_requests.collection_id', 'collections.id')
                ->leftJoin('publishers', 'collections.publisher_id', 'publishers.id')
                ->offset($start)
                ->limit($length)
                ->oldest()
                ->get();
            $totalFiltered = CollectionRequest::select('collections.title as collection_title', 'collection_requests.*', 'publishers.name as publisher_name')
                ->where(function ($query) use ($request) {
                    if ($request->publisher_id) {
                        $query->where('collections.publisher_id', $request->publisher_id);
                    }

                    if ($request->periode_start && $request->periode_end) {
                        $query->whereBetween('collection_requests.created_at', [$request->periode_start, $request->periode_end]);
                    } else if ($request->periode_start) {
                        $query->whereDate('collection_requests.created_at', '>', $request->periode_start);
                    } else if ($request->periode_end) {
                        $query->whereDate('collection_requests.created_at', '<', $request->periode_end);
                    } else {
                        $query->whereNotNull('collection_requests.created_at');
                    }
                })
                ->where(function ($query) use ($search) {
                    $query->where('collections.title', 'like', "%{$search}%")
                        ->orWhere('collections.code', 'like', "%{$search}%");
                })
                ->leftJoin('collections', 'collection_requests.collection_id', 'collections.id')
                ->leftJoin('publishers', 'collections.publisher_id', 'publishers.id')
                ->count();
        }


        $response['data'] = [];
        if ($queryData <> FALSE) {
            foreach ($queryData as $val) {

                $action = '';
                if ($val->status == 1) {
                    $action = '<button type="button" class="btn btn-success" onclick="updateStatus(' . $val->id . ', 2)">Terima</button><button type="button" class="btn btn-danger" onclick="updateStatus(' . $val->id . ', 3)">Tolak</button>';
                }

                $response['data'][] = [
                    '<span data-toggle="tooltip" title="' . $val->publisher_name . '">' . Str::limit($val->publisher_name, 20) . '</span>',
                    '<a href="' . url('admin/collection/manage/update/' . $val->id) . '" data-toggle="tooltip" title="' . $val->collection_title . '">' . Str::limit($val->collection_title, 20) . '</a>',
                    $val->status(),
                    $val->count_download,
                    '<a href="' . asset(Storage::disk($val->location->location)->url($val->request_letter)) . '" target="_blank">Lihat Surat</a>',
                    date('d-m-Y', strtotime($val->created_at)),
                    $action,

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

    public function update(Request $request)
    {

        $collection = CollectionRequest::find($request->collection_request_id);
        $collection->update([
            'token_download'       => Hash::make("session('id')_requestfileori_" . date("Y-m-d H:i:s")),
            'status'                => $request->status,
            'approved_by'           => session('id'),
            'expired_at'           => date("Y-m-d H:i:s", strtotime('+24 hours'))
        ]);

        if ($request->status == 2) {
            $user = User::where('userable_id', $collection->collection->publisher->id)->where('userable_type', 'publishers')->first();

            $params = [
                'publisher'     => $collection->collection->publisher->name,
                'email'         => $collection->collection->publisher->email,
                'title'         => $collection->collection->title,
                'status'        => $request->status,
                'url'           => $collection->getLinkDownload(),
                'user_id'       => $user->id
            ];

            $job = new SendEmailRequestFileOriginal($params);
            dispatch(($job)->onQueue('notification'));
        }

        return response()->json(['status' => 200]);
    }
}
