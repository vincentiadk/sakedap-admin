<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;

use Illuminate\Support\Str;

class NotificationController extends Controller
{
    public function index(Request $request)
    {

        $data = [
            'title'   => 'Notifikasi',
            'content' => 'publisher.notification.index'
        ];

        return view('publisher.layout.index', ['data' => $data]);
    }

    public function findOne(Request $request, $id)
    {

        $notification = Notification::find($id);
        $notification->update([
            'read_at'   => date('Y-m-d H:i:s')
        ]);

        $response = [
            'status'    => 200,
            'data'      => $notification
        ];

        return response()->json($response);
    }

    public function datatable(Request $request)
    {

        $start  = $request->input('start');
        $length = $request->input('length');
        $search = $request->input('search.value');

        $user = User::find(session('id'));

        $totalData = Notification::where('user_id', $user->id)
            ->where(function ($query) use ($request) {
                if ($request->periode_start && $request->periode_end) {
                    $query->whereBetween('created_at', [$request->periode_start, $request->periode_end]);
                } else if ($request->periode_start) {
                    $query->whereDate('created_at', '>', $request->periode_start);
                } else if ($request->periode_end) {
                    $query->whereDate('created_at', '<', $request->periode_end);
                }
            })
            ->count();
        if (empty($search)) {
            $queryData = Notification::where('user_id', $user->id)
                ->where(function ($query) use ($request) {
                    if ($request->periode_start && $request->periode_end) {
                        $query->whereBetween('created_at', [$request->periode_start, $request->periode_end]);
                    } else if ($request->periode_start) {
                        $query->whereDate('created_at', '>', $request->periode_start);
                    } else if ($request->periode_end) {
                        $query->whereDate('created_at', '<', $request->periode_end);
                    }
                })
                ->offset($start)
                ->limit($length)
                ->oldest()
                ->get();
            $totalFiltered = Notification::where('user_id', $user->id)
                ->where(function ($query) use ($request) {
                    if ($request->periode_start && $request->periode_end) {
                        $query->whereBetween('created_at', [$request->periode_start, $request->periode_end]);
                    } else if ($request->periode_start) {
                        $query->whereDate('created_at', '>', $request->periode_start);
                    } else if ($request->periode_end) {
                        $query->whereDate('created_at', '<', $request->periode_end);
                    }
                })
                ->count();
        } else {
            $queryData = Notification::where('user_id', $user->id)
                ->where(function ($query) use ($request) {
                    if ($request->periode_start && $request->periode_end) {
                        $query->whereBetween('created_at', [$request->periode_start, $request->periode_end]);
                    } else if ($request->periode_start) {
                        $query->whereDate('created_at', '>', $request->periode_start);
                    } else if ($request->periode_end) {
                        $query->whereDate('created_at', '<', $request->periode_end);
                    }
                })
                ->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%");
                })
                ->offset($start)
                ->limit($length)
                ->oldest()
                ->get();
            $totalFiltered = Notification::where('user_id', $user->id)
                ->where(function ($query) use ($request) {
                    if ($request->periode_start && $request->periode_end) {
                        $query->whereBetween('created_at', [$request->periode_start, $request->periode_end]);
                    } else if ($request->periode_start) {
                        $query->whereDate('created_at', '>', $request->periode_start);
                    } else if ($request->periode_end) {
                        $query->whereDate('created_at', '<', $request->periode_end);
                    }
                })
                ->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%");
                })
                ->count();
        }
        $response['data'] = [];
        if ($queryData <> FALSE) {
            foreach ($queryData as $val) {

                if ($val->read_at != null) {
                    $title = '<span data-toggle="tooltip" title="' . $val->title . '">' . Str::limit($val->title, 20) . '</span>';
                } else {
                    $title = '<strong><span data-toggle="tooltip" title="' . $val->title . '">' . Str::limit($val->title, 20) . '</span></strong><span class="float-right primary">
                      <span class="badge badge-pill badge-danger">&nbsp;</span>
                    </span>';
                }

                $response['data'][] = [
                    $title,
                    Str::limit($val->body, 50),
                    date('d-m-Y', strtotime($val->created_at)),
                    '<button class="btn btn-sm btn-info" type="button" onclick="showDetail(\'' . $val->id . '\')"><i class="la la-eye"></i>Detail</button>'
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
