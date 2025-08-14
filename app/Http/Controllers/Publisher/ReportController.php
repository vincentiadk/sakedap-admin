<?php

namespace App\Http\Controllers\Publisher;

use App\Models\User;
use App\Models\Download;
use App\Models\Publisher;
use App\Models\Collection;
use App\Models\ActivityLog;
use App\Models\JobStatus;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\PublisherExport;
use App\Http\Controllers\Controller;
use App\Jobs\DownloadReportBillIsbn;
use App\Jobs\DownloadReportPublisher;
use App\Jobs\DownloadReportCollection;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{

    public function collection()
    {
        $data = [
            'title'   => 'Laporan Koleksi',
            'content' => 'publisher.report.collection'
        ];

        return view('publisher.layout.index', ['data' => $data]);
    }

    public function collectionDatatableSummary(Request $request)
    {
        $whereLike = [
            'type',
            'total_submitted',
            'total_accept',
            'total_rejected',
            'total_data'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $search = $request->input('search.value');

        $publisher_id = User::find(session('id'))->publisher->id;

        $request_data = [
            'param'            => $request->param,
            'publisher_id'     => $publisher_id,
            'type'             => $request->type,
            'province_id'      => $request->province_id,
            'year_start'       => $request->year_start,
            'year_end'         => $request->year_end,
            'month_start'      => $request->month_start,
            'month_end'        => $request->month_end,
            'month_year_start' => $request->month_year_start,
            'month_year_end'   => $request->month_year_end,
            'day_start'        => $request->day_start,
            'day_end'          => $request->day_end
        ];


        $totalData = Collection::select('type')
            ->where('parent_id', 0)
            ->where(function ($query) use ($request_data) {
                if ($request_data['publisher_id']) {
                    $query->where('publisher_id', $request_data['publisher_id']);
                }

                if ($request_data['type']) {
                    $query->where('type', $request_data['type']);
                }


                if ($request_data['param']) {
                    if ($request_data['param'] == 'annual') {
                        $query->whereYear('created_at', '<=', $request_data['year_end']);
                    } else if ($request_data['param'] == 'monthly') {
                        $query->whereMonth('created_at', '>=', $request_data['month_start'])
                            ->whereYear('created_at', '>=', $request_data['month_year_start'])
                            ->whereMonth('created_at', '<=', $request_data['month_end'])
                            ->whereYear('created_at', '<=', $request_data['month_year_start']);
                    } else if ($request_data['param'] == 'daily') {
                        $query->whereDate('created_at', '>=', $request_data['day_start'])
                            ->whereDate('created_at', '<=', $request_data['day_end']);
                    }
                }
            })
            ->distinct()
            ->count();

        if (empty($search)) {
            $queryData = Collection::select('type')
                ->where('parent_id', 0)
                ->where(function ($query) use ($request_data) {
                    if ($request_data['publisher_id']) {
                        $query->where('publisher_id', $request_data['publisher_id']);
                    }

                    if ($request_data['type']) {
                        $query->where('type', $request_data['type']);
                    }


                    if ($request_data['param']) {
                        if ($request_data['param'] == 'annual') {
                            $query->whereYear('created_at', '>=', $request_data['year_start'])
                                ->whereYear('created_at', '<=', $request_data['year_end']);
                        } else if ($request_data['param'] == 'monthly') {
                            $query->whereMonth('created_at', '>=', $request_data['month_start'])
                                ->whereYear('created_at', '>=', $request_data['month_year_start'])
                                ->whereMonth('created_at', '<=', $request_data['month_end'])
                                ->whereYear('created_at', '<=', $request_data['month_year_start']);
                        } else if ($request_data['param'] == 'daily') {
                            $query->whereDate('created_at', '>=', $request_data['day_start'])
                                ->whereDate('created_at', '<=', $request_data['day_end']);
                        }
                    }
                })
                ->distinct()
                ->offset($start)
                ->limit($length)
                ->oldest()
                ->get();
            $totalFiltered = Collection::select('type')
                ->where('parent_id', 0)
                ->where(function ($query) use ($request_data) {
                    if ($request_data['publisher_id']) {
                        $query->where('publisher_id', $request_data['publisher_id']);
                    }

                    if ($request_data['type']) {
                        $query->where('type', $request_data['type']);
                    }

                    if ($request_data['param']) {
                        if ($request_data['param'] == 'annual') {
                            $query->whereYear('created_at', '>=', $request_data['year_start'])
                                ->whereYear('created_at', '<=', $request_data['year_end']);
                        } else if ($request_data['param'] == 'monthly') {
                            $query->whereMonth('created_at', '>=', $request_data['month_start'])
                                ->whereYear('created_at', '>=', $request_data['month_year_start'])
                                ->whereMonth('created_at', '<=', $request_data['month_end'])
                                ->whereYear('created_at', '<=', $request_data['month_year_start']);
                        } else if ($request_data['param'] == 'daily') {
                            $query->whereDate('created_at', '>=', $request_data['day_start'])
                                ->whereDate('created_at', '<=', $request_data['day_end']);
                        }
                    }
                })
                ->distinct()
                ->count();
        } else {
            $queryData = Collection::select('type')
                ->where('parent_id', 0)
                ->where(function ($query) use ($request_data) {
                    if ($request_data['publisher_id']) {
                        $query->where('publisher_id', $request_data['publisher_id']);
                    }

                    if ($request_data['type']) {
                        $query->where('type', $request_data['type']);
                    }

                    if ($request_data['param']) {
                        if ($request_data['param'] == 'annual') {
                            $query->whereYear('created_at', '>=', $request_data['year_start'])
                                ->whereYear('created_at', '<=', $request_data['year_end']);
                        } else if ($request_data['param'] == 'monthly') {
                            $query->whereMonth('created_at', '>=', $request_data['month_start'])
                                ->whereYear('created_at', '>=', $request_data['month_year_start'])
                                ->whereMonth('created_at', '<=', $request_data['month_end'])
                                ->whereYear('created_at', '<=', $request_data['month_year_start']);
                        } else if ($request_data['param'] == 'daily') {
                            $query->whereDate('created_at', '>=', $request_data['day_start'])
                                ->whereDate('created_at', '<=', $request_data['day_end']);
                        }
                    }
                })
                ->where(function ($query) use ($search) {
                    $query->where('type', 'like', "%{$search}%");
                })
                ->distinct()
                ->offset($start)
                ->limit($length)
                ->oldest()
                ->get();
            $totalFiltered = Collection::select('type')
                ->where('parent_id', 0)
                ->where(function ($query) use ($request_data) {
                    if ($request_data['publisher_id']) {
                        $query->where('publisher_id', $request_data['publisher_id']);
                    }

                    if ($request_data['type']) {
                        $query->where('type', $request_data['type']);
                    }


                    if ($request_data['param']) {
                        if ($request_data['param'] == 'annual') {
                            $query->whereYear('created_at', '>=', $request_data['year_start'])
                                ->whereYear('created_at', '<=', $request_data['year_end']);
                        } else if ($request_data['param'] == 'monthly') {
                            $query->whereMonth('created_at', '>=', $request_data['month_start'])
                                ->whereYear('created_at', '>=', $request_data['month_year_start'])
                                ->whereMonth('created_at', '<=', $request_data['month_end'])
                                ->whereYear('created_at', '<=', $request_data['month_year_start']);
                        } else if ($request_data['param'] == 'daily') {
                            $query->whereDate('created_at', '>=', $request_data['day_start'])
                                ->whereDate('created_at', '<=', $request_data['day_end']);
                        }
                    }
                })
                ->where(function ($query) use ($search) {
                    $query->where('type', 'like', "%{$search}%");
                })
                ->distinct()
                ->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            foreach ($queryData as $val) {
                $total_submitted = Collection::where('type', $val->type)
                    ->where('status', 1)
                    ->where('parent_id', 0)
                    ->whereNull('received_at')
                    ->where(function ($query) use ($request_data) {
                        if ($request_data['publisher_id']) {
                            $query->where('publisher_id', $request_data['publisher_id']);
                        }

                        if ($request_data['type']) {
                            $query->where('type', $request_data['type']);
                        }

                        if ($request_data['param']) {
                            if ($request_data['param'] == 'annual') {
                                $query->whereYear('created_at', '>=', $request_data['year_start'])
                                    ->whereYear('created_at', '<=', $request_data['year_end']);
                            } else if ($request_data['param'] == 'monthly') {
                                $query->whereMonth('created_at', '>=', $request_data['month_start'])
                                    ->whereYear('created_at', '>=', $request_data['month_year_start'])
                                    ->whereMonth('created_at', '<=', $request_data['month_end'])
                                    ->whereYear('created_at', '<=', $request_data['month_year_start']);
                            } else if ($request_data['param'] == 'daily') {
                                $query->whereDate('created_at', '>=', $request_data['day_start'])
                                    ->whereDate('created_at', '<=', $request_data['day_end']);
                            }
                        }
                    })
                    ->count();

                $total_accept = Collection::where('type', $val->type)
                    ->where('status', 2)
                    ->where('parent_id', 0)
                    ->whereNotNull('received_at')
                    ->where(function ($query) use ($request_data) {
                        if ($request_data['publisher_id']) {
                            $query->where('publisher_id', $request_data['publisher_id']);
                        }

                        if ($request_data['type']) {
                            $query->where('type', $request_data['type']);
                        }


                        if ($request_data['param']) {
                            if ($request_data['param'] == 'annual') {
                                $query->whereYear('created_at', '>=', $request_data['year_start'])
                                    ->whereYear('created_at', '<=', $request_data['year_end']);
                            } else if ($request_data['param'] == 'monthly') {
                                $query->whereMonth('created_at', '>=', $request_data['month_start'])
                                    ->whereYear('created_at', '>=', $request_data['month_year_start'])
                                    ->whereMonth('created_at', '<=', $request_data['month_end'])
                                    ->whereYear('created_at', '<=', $request_data['month_year_start']);
                            } else if ($request_data['param'] == 'daily') {
                                $query->whereDate('created_at', '>=', $request_data['day_start'])
                                    ->whereDate('created_at', '<=', $request_data['day_end']);
                            }
                        }
                    })
                    ->count();

                $total_rejected = Collection::where('type', $val->type)
                    ->where('status', 3)
                    ->where('parent_id', 0)
                    ->whereNull('received_at')
                    ->where(function ($query) use ($request_data) {
                        if ($request_data['publisher_id']) {
                            $query->where('publisher_id', $request_data['publisher_id']);
                        }

                        if ($request_data['type']) {
                            $query->where('type', $request_data['type']);
                        }


                        if ($request_data['param']) {
                            if ($request_data['param'] == 'annual') {
                                $query->whereYear('created_at', '>=', $request_data['year_start'])
                                    ->whereYear('created_at', '<=', $request_data['year_end']);
                            } else if ($request_data['param'] == 'monthly') {
                                $query->whereMonth('created_at', '>=', $request_data['month_start'])
                                    ->whereYear('created_at', '>=', $request_data['month_year_start'])
                                    ->whereMonth('created_at', '<=', $request_data['month_end'])
                                    ->whereYear('created_at', '<=', $request_data['month_year_start']);
                            } else if ($request_data['param'] == 'daily') {
                                $query->whereDate('created_at', '>=', $request_data['day_start'])
                                    ->whereDate('created_at', '<=', $request_data['day_end']);
                            }
                        }
                    })
                    ->count();

                $total_data = Collection::where('type', $val->type)
                    ->where('parent_id', 0)
                    ->where(function ($query) use ($request_data) {
                        if ($request_data['publisher_id']) {
                            $query->where('publisher_id', $request_data['publisher_id']);
                        }

                        if ($request_data['type']) {
                            $query->where('type', $request_data['type']);
                        }


                        if ($request_data['param']) {
                            if ($request_data['param'] == 'annual') {
                                $query->whereYear('created_at', '>=', $request_data['year_start'])
                                    ->whereYear('created_at', '<=', $request_data['year_end']);
                            } else if ($request_data['param'] == 'monthly') {
                                $query->whereMonth('created_at', '>=', $request_data['month_start'])
                                    ->whereYear('created_at', '>=', $request_data['month_year_start'])
                                    ->whereMonth('created_at', '<=', $request_data['month_end'])
                                    ->whereYear('created_at', '<=', $request_data['month_year_start']);
                            } else if ($request_data['param'] == 'daily') {
                                $query->whereDate('created_at', '>=', $request_data['day_start'])
                                    ->whereDate('created_at', '<=', $request_data['day_end']);
                            }
                        }
                    })
                    ->count();

                $response['data'][] = [
                    $val->type(),
                    $total_submitted,
                    $total_accept,
                    $total_rejected,
                    $total_data
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

    public function collectionDatatableDetail(Request $request)
    {
        $whereLike = [
            'publisher_id',
            'code',
            'code_type',
            'edition',
            'series',
            'city_id',
            'created_at',
            'received_at',
            'receipt'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $search = $request->input('search.value');

        $publisher_id = User::find(session('id'))->publisher->id;

        $request_data = [
            'param'            => $request->param,
            'publisher_id'     => $publisher_id,
            'type'             => $request->type,
            'province_id'      => $request->province_id,
            'year_start'       => $request->year_start,
            'year_end'         => $request->year_end,
            'month_start'      => $request->month_start,
            'month_end'        => $request->month_end,
            'month_year_start' => $request->month_year_start,
            'month_year_end'   => $request->month_year_end,
            'day_start'        => $request->day_start,
            'day_end'          => $request->day_end,
        ];

        $totalData = Collection::where(function ($query) use ($request_data) {
            if ($request_data['publisher_id']) {
                $query->where('publisher_id', $request_data['publisher_id']);
            }

            if ($request_data['type']) {
                $query->where('type', $request_data['type']);
            }

            if ($request_data['province_id']) {
                $query->whereHas('city', function ($query) use ($request_data) {
                    $query->where('province_id', $request_data['province_id']);
                });
            }

            if ($request_data['param']) {
                if ($request_data['param'] == 'annual') {
                    $query->whereYear('created_at', '>=', $request_data['year_start'])
                        ->whereYear('created_at', '<=', $request_data['year_end']);
                } else if ($request_data['param'] == 'monthly') {
                    $query->whereMonth('created_at', '>=', $request_data['month_start'])
                        ->whereYear('created_at', '>=', $request_data['month_year_start'])
                        ->whereMonth('created_at', '<=', $request_data['month_end'])
                        ->whereYear('created_at', '<=', $request_data['month_year_start']);
                } else if ($request_data['param'] == 'daily') {
                    $query->whereDate('created_at', '>=', $request_data['day_start'])
                        ->whereDate('created_at', '<=', $request_data['day_end']);
                }
            }
        })
            ->where('parent_id', 0)
            ->count();
        if (empty($search)) {
            $queryData = Collection::where(function ($query) use ($request_data) {
                if ($request_data['publisher_id']) {
                    $query->where('publisher_id', $request_data['publisher_id']);
                }

                if ($request_data['type']) {
                    $query->where('type', $request_data['type']);
                }

                if ($request_data['province_id']) {
                    $query->whereHas('city', function ($query) use ($request_data) {
                        $query->where('province_id', $request_data['province_id']);
                    });
                }

                if ($request_data['param']) {
                    if ($request_data['param'] == 'annual') {
                        $query->whereYear('created_at', '>=', $request_data['year_start'])
                            ->whereYear('created_at', '<=', $request_data['year_end']);
                    } else if ($request_data['param'] == 'monthly') {
                        $query->whereMonth('created_at', '>=', $request_data['month_start'])
                            ->whereYear('created_at', '>=', $request_data['month_year_start'])
                            ->whereMonth('created_at', '<=', $request_data['month_end'])
                            ->whereYear('created_at', '<=', $request_data['month_year_start']);
                    } else if ($request_data['param'] == 'daily') {
                        $query->whereDate('created_at', '>=', $request_data['day_start'])
                            ->whereDate('created_at', '<=', $request_data['day_end']);
                    }
                }
            })
                ->where('parent_id', 0)
                ->offset($start)
                ->limit($length)
                ->oldest()
                ->get();
            $totalFiltered = Collection::where(function ($query) use ($request_data) {
                if ($request_data['publisher_id']) {
                    $query->where('publisher_id', $request_data['publisher_id']);
                }

                if ($request_data['type']) {
                    $query->where('type', $request_data['type']);
                }

                if ($request_data['province_id']) {
                    $query->whereHas('city', function ($query) use ($request_data) {
                        $query->where('province_id', $request_data['province_id']);
                    });
                }

                if ($request_data['param']) {
                    if ($request_data['param'] == 'annual') {
                        $query->whereYear('created_at', '>=', $request_data['year_start'])
                            ->whereYear('created_at', '<=', $request_data['year_end']);
                    } else if ($request_data['param'] == 'monthly') {
                        $query->whereMonth('created_at', '>=', $request_data['month_start'])
                            ->whereYear('created_at', '>=', $request_data['month_year_start'])
                            ->whereMonth('created_at', '<=', $request_data['month_end'])
                            ->whereYear('created_at', '<=', $request_data['month_year_start']);
                    } else if ($request_data['param'] == 'daily') {
                        $query->whereDate('created_at', '>=', $request_data['day_start'])
                            ->whereDate('created_at', '<=', $request_data['day_end']);
                    }
                }
            })
                ->where('parent_id', 0)
                ->count();
        } else {
            $queryData = Collection::where(function ($query) use ($request_data) {
                if ($request_data['publisher_id']) {
                    $query->where('publisher_id', $request_data['publisher_id']);
                }

                if ($request_data['type']) {
                    $query->where('type', $request_data['type']);
                }

                if ($request_data['province_id']) {
                    $query->whereHas('city', function ($query) use ($request_data) {
                        $query->where('province_id', $request_data['province_id']);
                    });
                }

                if ($request_data['param']) {
                    if ($request_data['param'] == 'annual') {
                        $query->whereYear('created_at', '>=', $request_data['year_start'])
                            ->whereYear('created_at', '<=', $request_data['year_end']);
                    } else if ($request_data['param'] == 'monthly') {
                        $query->whereMonth('created_at', '>=', $request_data['month_start'])
                            ->whereYear('created_at', '>=', $request_data['month_year_start'])
                            ->whereMonth('created_at', '<=', $request_data['month_end'])
                            ->whereYear('created_at', '<=', $request_data['month_year_start']);
                    } else if ($request_data['param'] == 'daily') {
                        $query->whereDate('created_at', '>=', $request_data['day_start'])
                            ->whereDate('created_at', '<=', $request_data['day_end']);
                    }
                }
            })
                ->where('parent_id', 0)
                ->where(function ($query) use ($search) {
                    $query->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('edition', 'like', "%{$search}%")
                        ->orWhere('series', 'like', "%{$search}%");
                })
                ->offset($start)
                ->limit($length)
                ->oldest()
                ->get();
            $totalFiltered = Collection::where(function ($query) use ($request_data) {
                if ($request_data['publisher_id']) {
                    $query->where('publisher_id', $request_data['publisher_id']);
                }

                if ($request_data['type']) {
                    $query->where('type', $request_data['type']);
                }

                if ($request_data['province_id']) {
                    $query->whereHas('city', function ($query) use ($request_data) {
                        $query->where('province_id', $request_data['province_id']);
                    });
                }

                if ($request_data['param']) {
                    if ($request_data['param'] == 'annual') {
                        $query->whereYear('created_at', '>=', $request_data['year_start'])
                            ->whereYear('created_at', '<=', $request_data['year_end']);
                    } else if ($request_data['param'] == 'monthly') {
                        $query->whereMonth('created_at', '>=', $request_data['month_start'])
                            ->whereYear('created_at', '>=', $request_data['month_year_start'])
                            ->whereMonth('created_at', '<=', $request_data['month_end'])
                            ->whereYear('created_at', '<=', $request_data['month_year_start']);
                    } else if ($request_data['param'] == 'daily') {
                        $query->whereDate('created_at', '>=', $request_data['day_start'])
                            ->whereDate('created_at', '<=', $request_data['day_end']);
                    }
                }
            })
                ->where('parent_id', 0)
                ->where(function ($query) use ($search) {
                    $query->orWhere('title', 'like', "%{$search}%")
                        ->orWhere('edition', 'like', "%{$search}%")
                        ->orWhere('series', 'like', "%{$search}%");
                })
                ->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            foreach ($queryData as $val) {
                $received_at = $val->received_at ? date('d-m-Y', strtotime($val->received_at)) : '-';
                $code        = $val->code ? $val->code : '-';
                $city_id     = $val->city_id ? $val->city->name : '-';

                $edition = $val->edition ? '<span data-toggle="tooltip" title="' . $val->edition . '">' . Str::limit($val->edition, 20) . '</span>' : '-';

                $series = $val->series ? '<span data-toggle="tooltip" title="' . $val->series . '">' . Str::limit($val->series, 20) . '</span>' : '-';

                if ($request_data['param']) {
                    if ($request_data['param'] == 'annual') {
                        $periode = date('Y', strtotime($val->created_at));
                    } else if ($request_data['param'] == 'monthly') {
                        $periode = date('F Y', strtotime($val->created_at));
                    } else if ($request_data['param'] == 'daily') {
                        $periode = date('d F Y', strtotime($val->created_at));
                    }
                } else {
                    $periode = 'Semua Periode';
                }

                if ($val->status == 2) {
                    $receipt = '<a href="' . url('publisher/collection/request/receipt/' . $val->id) . '" class="text-success"><i class="la la-download"></i> Download</a>';
                } else {
                    $receipt = '-';
                }

                $response['data'][] = [
                    '<span data-toggle="tooltip" title="' . $val->publisher->name . '">' . Str::limit($val->publisher->name, 20) . '</span>',
                    $periode,
                    $code,
                    '<span data-toggle="tooltip" title="' . $val->title . '">' . Str::limit($val->title, 20) . '</span>',
                    $edition,
                    $series,
                    $city_id,
                    date('d-m-Y', strtotime($val->created_at)),
                    $received_at,
                    $receipt
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

    public function fileDownload()
    {
        $data = [
            'title'   => 'File Download',
            'content' => 'publisher.report.file_download'
        ];

        return view('publisher.layout.index', ['data' => $data]);
    }

    public function fileDownloadDatatable(Request $request)
    {
        $whereLike = [
            'slug',
            'date',
            'time',
            'link'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $search = $request->input('search.value');

        $totalData = Download::where('user_id', session('id'))
            ->count();
        if (empty($search)) {
            $queryData = Download::where('user_id', session('id'))
                ->offset($start)
                ->limit($length)
                ->latest()
                ->get();
            $totalFiltered = Download::where('user_id', session('id'))
                ->count();
        } else {
            $queryData = Download::where('user_id', session('id'))
                ->where(function ($query) use ($search) {
                    $query->where('slug', 'like', "%{$search}%")
                        ->orWhere('link', 'like', "%{$search}%");
                })
                ->offset($start)
                ->limit($length)
                ->latest()
                ->get();
            $totalFiltered = Download::where('user_id', session('id'))
                ->where(function ($query) use ($search) {
                    $query->where('slug', 'like', "%{$search}%")
                        ->orWhere('link', 'like', "%{$search}%");
                })
                ->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            foreach ($queryData as $val) {
                $response['data'][] = [
                    $val->slug(),
                    $val->status,
                    date('d F Y', strtotime($val->created_at)),
                    date('H.i', strtotime($val->created_at)),
                    '<a href="' . url('publisher/report/file_download/download/' . $val->id) . '" class="text-primary">Download</a>'
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

    public function fileDownloadProcessing(Request $request)
    {
        $publisher = User::find(session('id'))->publisher;
        $publisher_code = $publisher->code_system;

        $data = [
            'param'            => $request->param,
            'province_id'      => $request->province_id,
            'method'           => $request->method,
            'year_start'       => $request->year_start,
            'year_end'         => $request->year_end,
            'month_start'      => $request->month_start,
            'month_end'        => $request->month_end,
            'month_year_start' => $request->month_year_start,
            'month_year_end'   => $request->month_year_end,
            'day_start'        => $request->day_start,
            'day_end'          => $request->day_end,
            'type'             => $request->type,
            'type_date'        => $request->type_date,
            'status'           => $request->status,
            'publisher_id'     => $request->slug == 'collection' ? session('id') : $request->publisher_id,
            'user_id'          => session('id'),
            'action_id'        => session('id'),
            'view'             => 'publisher'
        ];

        if ($data['publisher_id'] == null) {
            $data['publisher_id'] = $publisher_code;
        }

        \Log::info($data);
        if ($request->slug == 'bill_isbn') {
            DownloadReportBillIsbn::dispatch($data)->onQueue('report');
        } else if ($request->slug == 'publisher') {
            DownloadReportPublisher::dispatch($data)->onQueue('report');
        } else if ($request->slug == 'collection') {
            DownloadReportCollection::dispatch($data)->onQueue('report');
        }

        return response()->json(200);
    }

    public function fileDownloadRun($id)
    {
        $file = Download::find($id);
        return response()->download(Storage::disk($file->location->location)->path($file->link));
    }

    public function datatableJobs(Request $request)
    {

        $start  = $request->input('start');
        $length = $request->input('length');
        $search = $request->input('search.value');

        $totalData = JobStatus::where('user_id', session('id'))->where('queue', 'report')->count();
        if (empty($search)) {
            $queryData = JobStatus::where('user_id', session('id'))
                ->where('queue', 'report')
                ->offset($start)
                ->limit($length)
                ->oldest()
                ->get();
            $totalFiltered = JobStatus::where('user_id', session('id'))->count();
        } else {
            $queryData = JobStatus::where('user_id', session('id'))
                ->where('queue', 'report')
                ->where(function ($query) use ($search) {
                    $query->where('status', 'like', "%{$search}%")
                        ->orWhere('job_id', 'like', "%{$search}%");
                })
                ->offset($start)
                ->limit($length)
                ->oldest()
                ->get();
            $totalFiltered = JobStatus::where('user_id', session('id'))
                ->where('queue', 'report')
                ->where(function ($query) use ($search) {
                    $query->where('status', 'like', "%{$search}%")
                        ->orWhere('job_id', 'like', "%{$search}%");
                })
                ->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            foreach ($queryData as $val) {

                $output = json_encode($val->output);
                $msg = "";
                if (isset($output['message'])) {
                    $msg = $output['message'];
                }

                $response['data'][] = [
                    $val->type(),
                    $val->job_id,
                    "<div class='progress'><div class='progress-bar progress-bar-striped progress-bar-animated bg-success' role='progressbar' aria-valuenow='$val->progress_now' aria-valuemin='$val->progress_now' aria-valuemax='$val->progress_max' style='width:$val->progress_now%'></div></div>",
                    $val->status(),
                    $msg,
                    date('d-m-Y H:i:s', strtotime($val->created_at)),
                    $val->started_at == null ? '' : date('d-m-Y H:i:s', strtotime($val->started_at)),
                    $val->finished_at == null ? '' : date('d-m-Y H:i:s', strtotime($val->finished_at))
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
