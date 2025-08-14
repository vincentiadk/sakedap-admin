<?php

namespace App\Http\Controllers\Publisher;

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Problem;
use App\Models\Category;
use App\Models\Contributor;
use App\Models\Collection;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helper\GeneralHelper;
use App\Models\CollectionMedia;
use falahati\PHPMP3\MpegAudio;
use App\Models\CollectionProblem;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use LynX39\LaraPdfMerger\Facades\PdfMerger;

class CollectionMonitoringController extends Controller
{

    public function index($connect = null)
    {

        $publisher = User::find(session('id'))->publisher;

        $data = [
            'title'   => 'Pemantauan Koleksi',
            'content' => 'publisher.collection.monitoring',
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
            'created_at'
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
            ->where('status', 1)
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
                ->orderBy('created_at', 'desc')
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
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            foreach ($queryData as $val) {
                $response['data'][] = [
                    $val->type(),
                    '<span data-toggle="tooltip" title="' . $val->publisher->name . '">' . Str::limit($val->publisher->name, 20) . '</span>',
                    '<a href="' . url('publisher/collection/monitoring/detail/' . $val->id) . '" data-toggle="tooltip" title="' . $val->title . '">' . Str::limit($val->title, 20) . '</a>',
                    $val->code ? $val->code : '<i class="la la-times text-danger"></i>',
                    date('d-m-Y', strtotime($val->created_at)),
                    '
                        <a href="' . url('publisher/collection/monitoring/detail/' . $val->id) . '" class="btn btn-info btn-sm"><i class="la la-eye"></i> Detail</a>
                    '
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

        $user = User::find(session('id'));
        $publisher = $user->publisher;

        if ($publisher->getGroups() == null) {
            if ($collection->publisher_id != $publisher->id) {
                return abort(403, 'Unauthorized action.');
            }
        } else {
            if (!$publisher->checkSameGroups($collection->publisher_id)) {
                return abort(403, 'Unauthorized action.');
            }
        }



        if ($collection->status == 3) {
            return abort(403, 'Unauthorized action.');
        }

        if ($collection->type == 1) {

            if (count($collection->edition()->get()) > 0) {
                $data = [
                    'title'   => 'Review Pemantauan Buku',
                    'content' => 'publisher.book.review_monitoring_jilid'
                ];
            } else {

                if ($collection->status == 1) {
                    $data = [
                        'title'   => 'Review Pemantauan Buku',
                        'content' => 'publisher.book.review'
                    ];
                } else {
                    $data = [
                        'title'   => 'Review Pemantauan Buku',
                        'content' => 'publisher.book.review_monitoring'
                    ];
                }
            }
        } else if ($collection->type == 2) {


            if ($collection->status == 1) {
                $data = [
                    'title'   => 'Review Pemantauan Partitur',
                    'content' => 'publisher.partitur.review'
                ];
            } else {
                $data = [
                    'title'   => 'Review Pemantauan Partitur',
                    'content' => 'publisher.partitur.review_monitoring'
                ];
            }
        } else if ($collection->type == 3) {


            if ($collection->status == 1) {
                $data = [
                    'title'   => 'Review Pemantauan Peta',
                    'content' => 'publisher.map.review'
                ];
            } else {
                $data = [
                    'title'   => 'Review Pemantauan Peta',
                    'content' => 'publisher.map.review_monitoring'
                ];
            }
        } else if ($collection->type == 4) {


            if ($collection->status == 1) {
                $data = [
                    'title'   => 'Review Pemantauan Serial',
                    'content' => 'publisher.serial.review'
                ];
            } else {
                $data = [
                    'title'   => 'Review Pemantauan Serial',
                    'content' => 'publisher.serial.review_monitoring'
                ];
            }
        } else if ($collection->type == 5) {

            if ($collection->status == 1) {
                $data = [
                    'title'   => 'Review Pemantauan Audio',
                    'content' => 'publisher.audio.review'
                ];
            } else {
                $data = [
                    'title'   => 'Review Pemantauan Audio',
                    'content' => 'publisher.audio.review_monitoring'
                ];
            }
        } else if ($collection->type == 6) {


            if ($collection->status == 1) {
                $data = [
                    'title'   => 'Review Pemantauan Film',
                    'content' => 'publisher.film.review'
                ];
            } else {
                $data = [
                    'title'   => 'Review Pemantauan Film',
                    'content' => 'publisher.film.review_monitoring'
                ];
            }
        } else {
            return redirect()->back();
        }

        $data = array_merge($data, [
            'collection' => $collection,
            'problem'    => Problem::all(),
            'category'   => Category::where('type', $collection->type)->get(),
            'contributor' => Contributor::where('type', $collection->type)->get(),
            'edition'    => Collection::where('parent_id', $id)->get()
        ]);

        return view('publisher.layout.index', ['data' => $data]);
    }

    public function history(Request $request)
    {

        $history = ActivityLog::where('subject_type', 'App\Models\Collection')
            ->where('subject_id', $request->collection_id)
            ->get();

        return response()->json($history);
    }
}
