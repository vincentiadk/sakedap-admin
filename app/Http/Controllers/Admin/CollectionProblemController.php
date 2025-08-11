<?php

namespace App\Http\Controllers\Admin;

use App\Models\Collection;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CollectionProblem;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\DashboardController;

class CollectionProblemController extends Controller
{
    public function index($type = null)
    {
        if ($type == 1) {
            $data = [
                'title'   => 'Masalah Buku',
                'content' => 'admin.book.problem'
            ];
        } else if ($type == 2) {
            $data = [
                'title'   => 'Masalah Partitur',
                'content' => 'admin.partitur.problem'
            ];
        } else if ($type == 3) {
            $data = [
                'title'   => 'Masalah Peta',
                'content' => 'admin.map.problem'
            ];
        } else if ($type == 4) {
            $data = [
                'title'   => 'Masalah Serial',
                'content' => 'admin.serial.problem'
            ];
        } else if ($type == 5) {
            $data = [
                'title'   => 'Masalah Audio',
                'content' => 'admin.audio.problem'
            ];
        } else if ($type == 6) {
            $data = [
                'title'   => 'Masalah Film',
                'content' => 'admin.film.problem'
            ];
        } else {
            $data = [
                'title'          => 'Masalah Koleksi',
                'total_book'     => DashboardController::statistic('collection_type_status', [1, [3, 5]]),
                'total_partitur' => DashboardController::statistic('collection_type_status', [2, [3, 5]]),
                'total_map'      => DashboardController::statistic('collection_type_status', [3, [3, 5]]),
                'total_serial'   => DashboardController::statistic('collection_type_status', [4, [3, 5]]),
                'total_audio'    => DashboardController::statistic('collection_type_status', [5, [3, 5]]),
                'total_film'     => DashboardController::statistic('collection_type_status', [6, [3, 5]]),
                'content'        => 'admin.collection.problem'
            ];
        }

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request, $type)
    {
        $whereLike = [
            'id',
            'publisher_id',
            'title',
            'code',
            'status',
            'collection_problem',
            'problem',
            'validated_at',
            'validated_by'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $totalData = Collection::where(function ($query) use ($type) {
            $query->where('type', $type)
                ->whereIn('status', [3, 5])
                ->where('parent_id', 0)
                ->whereNotNull('rejected_at')
                ->whereNotNull('rejected_by');
        })
            ->where(function ($query) {
                $query->whereIn('status', ['3', '5'])
                    ->orWhereHas('collectionProblem', function ($query) {
                        $query->where('solved', 0);
                    });
            })
            ->where(function ($query) {
                if (session('library_id') != 1) {
                    $query->whereHas('city', function ($query) {
                        $query->where('province_id', session('province_id'));
                    });
                }
            })
            ->count();
        if (empty($search)) {

            session()->put('filter.collection.problem.' . $type . '.title', $request->title);
            session()->put('filter.collection.problem.' . $type . '.publisher_id', $request->publisher_id);
            session()->put('filter.collection.problem.' . $type . '.province_id', $request->province_id);
            session()->put('filter.collection.problem.' . $type . '.city', $request->city);
            session()->put('filter.collection.problem.' . $type . '.publication_year', $request->publication_year);
            session()->put('filter.collection.problem.' . $type . '.code', $request->code);
            session()->put('filter.collection.problem.' . $type . '.param', $request->param);

            if ($request->param == 'annual') {
                session()->put('filter.collection.problem.' . $type . '.year_start', $request->year_start);
                session()->put('filter.collection.problem.' . $type . '.year_end', $request->year_end);

                session()->forget('filter.collection.problem.' . $type . '.month_start');
                session()->forget('filter.collection.problem.' . $type . '.month_year_start');
                session()->forget('filter.collection.problem.' . $type . '.month_end');
                session()->forget('filter.collection.problem.' . $type . '.month_year_end');

                session()->forget('filter.collection.problem.' . $type . '.day_start');
                session()->forget('filter.collection.problem.' . $type . '.day_end');
            } else if ($request->param == 'monthly') {
                session()->put('filter.collection.problem.' . $type . '.month_start', $request->month_start);
                session()->put('filter.collection.problem.' . $type . '.month_year_start', $request->month_year_start);
                session()->put('filter.collection.problem.' . $type . '.month_end', $request->month_end);
                session()->put('filter.collection.problem.' . $type . '.month_year_end', $request->month_year_end);

                session()->forget('filter.collection.problem.' . $type . '.year_start');
                session()->forget('filter.collection.problem.' . $type . '.year_end');

                session()->forget('filter.collection.problem.' . $type . '.day_start');
                session()->forget('filter.collection.problem.' . $type . '.day_end');
            } else if ($request->param == 'daily') {
                session()->put('filter.collection.problem.' . $type . '.day_start', $request->day_start);
                session()->put('filter.collection.problem.' . $type . '.day_end', $request->day_end);

                session()->forget('filter.collection.problem.' . $type . '.year_start');
                session()->forget('filter.collection.problem.' . $type . '.year_end');

                session()->forget('filter.collection.problem.' . $type . '.month_start');
                session()->forget('filter.collection.problem.' . $type . '.month_year_start');
                session()->forget('filter.collection.problem.' . $type . '.month_end');
                session()->forget('filter.collection.problem.' . $type . '.month_year_end');
            } else {
                session()->forget('filter.collection.problem.' . $type . '.year_start');
                session()->forget('filter.collection.problem.' . $type . '.year_end');

                session()->forget('filter.collection.problem.' . $type . '.month_start');
                session()->forget('filter.collection.problem.' . $type . '.month_year_start');
                session()->forget('filter.collection.problem.' . $type . '.month_end');
                session()->forget('filter.collection.problem.' . $type . '.month_year_end');

                session()->forget('filter.collection.problem.' . $type . '.day_start');
                session()->forget('filter.collection.problem.' . $type . '.day_end');
            }

            $queryData = Collection::where(function ($query) use ($type) {
                $query->where('type', $type)
                    ->whereIn('status', [3, 5])
                    ->where('parent_id', 0)
                    ->whereNotNull('rejected_at')
                    ->whereNotNull('rejected_by');
            })
                ->where(function ($query) {
                    $query->whereIn('status', ['3', '5'])
                        ->orWhereHas('collectionProblem', function ($query) {
                            $query->where('solved', 0);
                        });
                })
                ->where(function ($query) {
                    if (session('library_id') != 1) {
                        $query->whereHas('city', function ($query) {
                            $query->where('province_id', session('province_id'));
                        });
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->title) {
                        $query->where('title', 'like', "%$request->title%");
                    }

                    if ($request->publisher_id) {
                        $query->where('publisher_id', $request->publisher_id);
                    }

                    if ($request->province_id) {
                        $query->whereHas('city', function ($query) use ($request) {
                            $query->where('province_id', $request->province_id);
                        });
                    }

                    if ($request->city) {
                        $query->whereHas('city', function ($query) use ($request) {
                            $query->where('name', 'like', "%$request->city%");
                        });
                    }

                    if ($request->publication_year) {
                        $query->where('publication_year', $request->publication_year);
                    }

                    if ($request->code) {
                        $query->where('code', 'like', "%$request->code%");
                    }

                    if ($request->param) {
                        if ($request->param == 'annual') {
                            $query->whereYear('updated_at', '>=', $request->year_start)
                                ->whereYear('updated_at', '<=', $request->year_end);
                        } else if ($request->param == 'monthly') {
                            $query->whereMonth('updated_at', '>=', $request->month_start)
                                ->whereYear('updated_at', '>=', $request->month_year_start)
                                ->whereMonth('updated_at', '<=', $request->month_end)
                                ->whereYear('updated_at', '<=', $request->month_year_start);
                        } else if ($request->param == 'daily') {
                            $query->whereDate('updated_at', '>=', $request->day_start)
                                ->whereDate('updated_at', '<=', $request->day_end);
                        }
                    }
                })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Collection::where(function ($query) use ($type) {
                $query->where('type', $type)
                    ->whereIn('status', [3, 5])
                    ->where('parent_id', 0)
                    ->whereNotNull('rejected_at')
                    ->whereNotNull('rejected_by');
            })
                ->where(function ($query) {
                    $query->whereIn('status', ['3', '5'])
                        ->orWhereHas('collectionProblem', function ($query) {
                            $query->where('solved', 0);
                        });
                })
                ->where(function ($query) {
                    if (session('library_id') != 1) {
                        $query->whereHas('city', function ($query) {
                            $query->where('province_id', session('province_id'));
                        });
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->title) {
                        $query->where('title', 'like', "%$request->title%");
                    }

                    if ($request->publisher_id) {
                        $query->where('publisher_id', $request->publisher_id);
                    }

                    if ($request->province_id) {
                        $query->whereHas('city', function ($query) use ($request) {
                            $query->where('province_id', $request->province_id);
                        });
                    }

                    if ($request->city) {
                        $query->whereHas('city', function ($query) use ($request) {
                            $query->where('name', 'like', "%$request->city%");
                        });
                    }

                    if ($request->publication_year) {
                        $query->where('publication_year', $request->publication_year);
                    }

                    if ($request->code) {
                        $query->where('code', 'like', "%$request->code%");
                    }

                    if ($request->param) {
                        if ($request->param == 'annual') {
                            $query->whereYear('updated_at', '>=', $request->year_start)
                                ->whereYear('updated_at', '<=', $request->year_end);
                        } else if ($request->param == 'monthly') {
                            $query->whereMonth('updated_at', '>=', $request->month_start)
                                ->whereYear('updated_at', '>=', $request->month_year_start)
                                ->whereMonth('updated_at', '<=', $request->month_end)
                                ->whereYear('updated_at', '<=', $request->month_year_start);
                        } else if ($request->param == 'daily') {
                            $query->whereDate('updated_at', '>=', $request->day_start)
                                ->whereDate('updated_at', '<=', $request->day_end);
                        }
                    }
                })
                ->count();
        } else {
            $queryData = Collection::where(function ($query) use ($type) {
                $query->where('type', $type)
                    ->whereIn('status', [3, 5])
                    ->where('parent_id', 0)
                    ->whereNotNull('rejected_at')
                    ->whereNotNull('rejected_by');
            })
                ->where(function ($query) {
                    $query->whereIn('status', ['3', '5'])
                        ->orWhereHas('collectionProblem', function ($query) {
                            $query->where('solved', 0);
                        });
                })
                ->where(function ($query) {
                    if (session('library_id') != 1) {
                        $query->whereHas('city', function ($query) {
                            $query->where('province_id', session('province_id'));
                        });
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->title) {
                        $query->where('title', 'like', "%$request->title%");
                    }

                    if ($request->publisher_id) {
                        $query->where('publisher_id', $request->publisher_id);
                    }

                    if ($request->province_id) {
                        $query->whereHas('city', function ($query) use ($request) {
                            $query->where('province_id', $request->province_id);
                        });
                    }

                    if ($request->city) {
                        $query->whereHas('city', function ($query) use ($request) {
                            $query->where('name', 'like', "%$request->city%");
                        });
                    }

                    if ($request->publication_year) {
                        $query->where('publication_year', $request->publication_year);
                    }

                    if ($request->code) {
                        $query->where('code', 'like', "%$request->code%");
                    }

                    if ($request->param) {
                        if ($request->param == 'annual') {
                            $query->whereYear('updated_at', '>=', $request->year_start)
                                ->whereYear('updated_at', '<=', $request->year_end);
                        } else if ($request->param == 'monthly') {
                            $query->whereMonth('updated_at', '>=', $request->month_start)
                                ->whereYear('updated_at', '>=', $request->month_year_start)
                                ->whereMonth('updated_at', '<=', $request->month_end)
                                ->whereYear('updated_at', '<=', $request->month_year_start);
                        } else if ($request->param == 'daily') {
                            $query->whereDate('updated_at', '>=', $request->day_start)
                                ->whereDate('updated_at', '<=', $request->day_end);
                        }
                    }
                })
                ->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Collection::where(function ($query) use ($type) {
                $query->where('type', $type)
                    ->whereIn('status', [3, 5])
                    ->where('parent_id', 0)
                    ->whereNotNull('rejected_at')
                    ->whereNotNull('rejected_by');
            })
                ->where(function ($query) {
                    $query->whereIn('status', ['3', '5'])
                        ->orWhereHas('collectionProblem', function ($query) {
                            $query->where('solved', 0);
                        });
                })
                ->where(function ($query) {
                    if (session('library_id') != 1) {
                        $query->whereHas('city', function ($query) {
                            $query->where('province_id', session('province_id'));
                        });
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->title) {
                        $query->where('title', 'like', "%$request->title%");
                    }

                    if ($request->publisher_id) {
                        $query->where('publisher_id', $request->publisher_id);
                    }

                    if ($request->province_id) {
                        $query->whereHas('city', function ($query) use ($request) {
                            $query->where('province_id', $request->province_id);
                        });
                    }

                    if ($request->city) {
                        $query->whereHas('city', function ($query) use ($request) {
                            $query->where('name', 'like', "%$request->city%");
                        });
                    }

                    if ($request->publication_year) {
                        $query->where('publication_year', $request->publication_year);
                    }

                    if ($request->code) {
                        $query->where('code', 'like', "%$request->code%");
                    }

                    if ($request->param) {
                        if ($request->param == 'annual') {
                            $query->whereYear('updated_at', '>=', $request->year_start)
                                ->whereYear('updated_at', '<=', $request->year_end);
                        } else if ($request->param == 'monthly') {
                            $query->whereMonth('updated_at', '>=', $request->month_start)
                                ->whereYear('updated_at', '>=', $request->month_year_start)
                                ->whereMonth('updated_at', '<=', $request->month_end)
                                ->whereYear('updated_at', '<=', $request->month_year_start);
                        } else if ($request->param == 'daily') {
                            $query->whereDate('updated_at', '>=', $request->day_start)
                                ->whereDate('updated_at', '<=', $request->day_end);
                        }
                    }
                })
                ->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                })
                ->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                $collectionProblem = '';
                $problem = CollectionProblem::where('collection_id', $val->id)->where('solved', 0)->get();
                foreach ($problem  as $key => $cp) {
                    if ($key + 1 < $val->collectionProblem->count()) {
                        $collectionProblem .= $cp->problem->name . ', ';
                    } else {
                        $collectionProblem .= $cp->problem->name;
                    }
                }

                $response['data'][] = [
                    $nomor,
                    '<span data-toggle="tooltip" title="' . $val->publisher->name . '">' . Str::limit($val->publisher->name, 20) . '</span>',
                    '<span data-toggle="tooltip" title="' . $val->title . '">' . Str::limit($val->title, 20) . '</span>',
                    $val->code ? $val->code : '<i class="la la-times text-danger"></i>',
                    $val->status(),
                    $collectionProblem,
                    $val->problem,
                    $val->updated_at ? date('d-m-Y', strtotime($val->updated_at)) : '-',
                    $val->updatedBy->username
                ];
                $nomor++;
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
