<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Contributor;
use App\Models\CollectionRequest;
use App\Models\Setting;
use App\Models\User;
use Storage;
use Mail;

class CollectionMonitoringRequest extends Controller
{

    public function index()
    {
        $data = [
            'title'   => 'Request File Original',
            'content' => 'publisher.collection.monitor_request_file'
        ];

        return view('publisher.layout.index', ['data' => $data]);
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

        $user = User::find(session('id'));
        $publisher_id = $user->publisher->id;

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
            ->where('publishers.id', $publisher_id)
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
                ->where('publishers.id', $publisher_id)
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
                ->where('publishers.id', $publisher_id)
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
                ->where('publishers.id', $publisher_id)
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
                ->where('publishers.id', $publisher_id)
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
                if ($val->status == 2) {
                    $current_time = strtotime(date('Y-m-d H:i'));
                    $schedule_time = strtotime(date('Y-m-d H:i', strtotime($val->expired_at)));

                    $diff = $schedule_time - $current_time;
                    $minutes = floor($diff / 60);

                    if ($minutes < 0) {
                        $action = '<div class="badge badge-danger">Link Kadaluarsa</div>';
                    } else if ($val->count_download >= 2) {
                        $action = '<div class="badge badge-danger">Sudah mencapai batas maksimal unduh</div>';
                    } else $action = '<a href="' . $val->getLinkDownload() . '" type="button" class="btn btn-success btn-sm"  target="_blank" onclick="loadDatatable()">Unduh</a>';
                }

                $response['data'][] = [
                    '<a href="' . url('publisher/collection/monitoring/review/' . $val->id) . '" data-toggle="tooltip" title="' . $val->collection_title . '">' . Str::limit($val->collection_title, 20) . '</a>',
                    $val->status(),
                    $val->count_download,
                    '<a href="' .  asset(Storage::disk($val->location->location)->url($val->request_letter)) . '" target="_blank">Lihat Surat</a>',
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
            'status'         => $request->status,
            'validated_by'    => session('id'),
            'expired_at'    => date("Y-m-d H:i:s", strtotime('+24 hours'))
        ]);


        Mail::send([], [], function ($message) use ($collection) {
            $hash = \Hash::make($collection->token_download);

            $data = [
                'publisher' => $collection->collection->publisher->name,
                'title'     => $collection->title,
                'link'         => url('download/file/original?=token' . $hash)
            ];

            $template = Setting::where('slug', 'template-email-request-file-original')->first();
            $message->to($collection->collection->publisher->email, 'edeposit@perpusnas.go.id')
                ->subject('Download File Original')
                ->from('edeposit@perpusnas.go.id', 'Info edeposit')
                ->setBody($template->parse($data), 'text/html');
        });

        activity()
            ->performedOn($collection)
            ->causedBy(User::find(session('id')))
            ->log('Request File Original ' . $collection->title);


        return view('publisher.layout.index', ['data' => $data]);
    }
}
