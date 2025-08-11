<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Collection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GuestController extends Controller
{
    public function index()
    {
        $data = [
            'title'   => 'Guest',
            'category' => Category::all(),
            'content' => 'admin.guest.guest'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'id',
            'title',
            'type',
            'publisher_id',
            'contributors',
            'subjects'
        ];

        $start  = $request->start;
        $length = $request->length;
        $order  = $column[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $model = Collection::where('parent_id', 0)
            ->whereNotNull('received_at')
            ->whereNotNull('received_by')
            ->where('status', 2);
        $total_data = $model->count();
        $filtered = $model->where(function ($query) use ($request, $search) {
            if ($search) {
                $query->where('title', 'like', "%$search%");
            }

            if ($request->publisher_id) {
                $query->where('publisher_id', $request->publisher_id);
            }

            if ($request->type) {
                $query->where('type', $request->type);
            }

            if ($request->category_id) {
                $query->whereHas('collectionCategory', function ($query) use ($request, $search) {
                    $query->where('category_id', $request->category_id);
                });
            }

            if ($request->subject_id) {
                $query->whereHas('collectionSubject', function ($query) use ($request, $search) {
                    $query->whereHas('subject', function ($query) use ($request) {
                        $query->where('name', 'like', "%$request->subject_id%");
                    });
                });
            }

            if ($request->province_id) {
                $query->where('province_id', $request->province_id);
            }

            if ($request->param) {
                if ($request->param == 'annual') {
                    $query->whereYear('created_at', '>=', $request->year_start)
                        ->whereYear('created_at', '<=', $request->year_end);
                } else if ($request->param == 'monthly') {
                    $query->whereMonth('created_at', '>=', $request->month_start)
                        ->whereYear('created_at', '>=', $request->month_year_start)
                        ->whereMonth('created_at', '<=', $request->month_end)
                        ->whereYear('created_at', '<=', $request->month_year_start);
                } else if ($request->param == 'daily') {
                    $query->whereDate('created_at', '>=', $request->day_start)
                        ->whereDate('created_at', '<=', $request->day_end);
                }
            }
        });

        $total_filtered = $filtered->count();

        $query_data = $filtered->offset($start)
            ->limit($length)
            ->orderBy($order, $dir)
            ->get();


        $response['data'] = [];
        if ($query_data <> FALSE) {
            $nomor = $start + 1;
            foreach ($query_data as $val) {
                if ($request->param) {
                    if ($request->param == 'annual') {
                        $periode = date('Y', strtotime($val->created_at));
                    } else if ($request->param == 'monthly') {
                        $periode = date('F Y', strtotime($val->created_at));
                    } else if ($request->param == 'daily') {
                        $periode = date('d F Y', strtotime($val->created_at));
                    }
                } else {
                    $periode = 'Semua Periode';
                }

                $contributors = $val->collectionContributor;
                $contributors_ = "";
                foreach ($contributors as $contributor) {
                    $contributors_ .= $contributor->contributor->name . ", " . $contributor->author->fullname . "; ";
                }

                $subjects = $val->collectionSubject;
                $subjects_ = "";
                foreach ($subjects as $subject) {
                    $subjects_ .= $subject->subject->name . "<br/>";
                }

                $response['data'][] = [
                    $nomor,
                    $val->title,
                    $val->type(),
                    $val->publisher ? $val->publisher->name : "",
                    $contributors_,
                    $subjects_,
                    $periode,
                    date('d F Y', strtotime($val->received_at)),
                    '<a href="' . url('admin/guest/detail/' . $val->id) . '" class="btn btn-info btn-sm" target="_blank"><i class="la la-info-circle"></i> Detail</a>'
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

    public function show($id)
    {
        $collection = Collection::find($id);
        if ($collection->type == 1) {
            $data = [
                'title'   => 'Detail Buku',
                'content' => 'admin.guest.detail_book'
            ];
        } else if ($collection->type == 2) {
            $data = [
                'title'   => 'Detail Partitur',
                'content' => 'admin.guest.detail_partitur'
            ];
        } else if ($collection->type == 3) {
            $data = [
                'title'   => 'Detail Peta',
                'content' => 'admin.guest.detail_map'
            ];
        } else if ($collection->type == 4) {
            $data = [
                'title'   => 'Detail Serial',
                'content' => 'admin.guest.detail_serial'
            ];
        } else if ($collection->type == 5) {
            $data = [
                'title'   => 'Detail Audio',
                'content' => 'admin.guest.detail_audio'
            ];
        } else if ($collection->type == 6) {
            $data = [
                'title'   => 'Detail Film',
                'content' => 'admin.guest.detail_film'
            ];
        } else {
            return redirect()->back();
        }

        $data = array_merge($data, [
            'collection' => $collection,
            'edition'    => Collection::where('parent_id', $id)->get()
        ]);

        return view('admin.layout.index', ['data' => $data]);
    }
}
