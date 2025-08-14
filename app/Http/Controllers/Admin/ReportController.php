<?php

namespace App\Http\Controllers\Admin;

use App\Models\Solr;
use App\Models\User;
use App\Models\Library;
use App\Models\Setting;
use App\Models\Director;
use App\Models\Download;
use App\Models\Province;
use App\Models\Publisher;
use App\Models\Collection;
use App\Models\Expedition;
use App\Helper\CustomTCPDF;
use App\Models\ActivityLog;
use App\Models\DepositHead;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\DeliveryForm;
use Illuminate\Http\Request;
use App\Helper\GeneralHelper;
use App\Jobs\DownloadDataIsrc;
use App\Models\CollectionCopy;
use App\Models\UserCertainAccess;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Jobs\DownloadReportBillIsbn;
use App\Jobs\DownloadReportPeriodic;
use App\Jobs\DownloadReportPublisher;
use App\Jobs\DownloadReportCollection;
use Illuminate\Support\Facades\Storage;
use App\Jobs\DownloadReportDistribution;
use App\Jobs\DownloadReportPublisherISBN;
use App\Jobs\DownloadReportPerformanceUser;
use App\Jobs\DownloadReportCollectionDelivery;

class ReportController extends Controller
{
    public function collection()
    {
        $arrCategoryDH = [
            'KRD' => 'Karya Rekam Digital'
        ];

        $getDepositHead = DepositHead::where('category', 'KRD')->get();
        $deposit_head = [];

        foreach ($getDepositHead as $value) {
            $deposit_head[$value->category][] = $value;
        }

        $data = [
            'title'   => 'Laporan Koleksi',
            'deposit_head' => $deposit_head,
            'category_dh' => $arrCategoryDH,
            'content' => 'admin.report.collection'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function collectionDatatableSummary(Request $request)
    {
        $whereLike = [
            'id',
            'type',
            'total_submitted',
            'total_accept',
            'total_rejected',
            'total_data'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $totalData = Collection::where(function ($query) {
            if (session('library_id') != 1) {
                $query->whereHas('city', function ($query) {
                    $query->where('province_id', session('province_id'));
                });
            }
        })->whereHas('depositHead', function ($query) {
            $query->whereIn('category', ['KRD']);
        })->where('parent_id', 0)->groupBy('type')->get()->count();

        if (empty($search)) {
            $queryData = Collection::where(function ($query) use ($request) {
                if ($request->publisher_id) {
                    $query->where('publisher_id', $request->publisher_id);
                }

                if ($request->type) {
                    $query->whereIn('type', $request->type);
                }

                if ($request->province_id) {
                    $query->whereHas('city', function ($query) use ($request) {
                        $query->where('province_id', $request->province_id);
                    });
                }

                if ($request->method) {
                    $query->whereHas('collectionMedia', function ($query) use ($request) {
                        $query->where('method', $request->method);
                    });
                }

                if ($request->status) {
                    $query->where('status', $request->status);
                }

                if ($request->param) {
                    $query->where(function ($query) use ($request) {
                        if ($request->param == 'annual') {
                            $query->whereYear($request->type_date, '>=', $request->year_start)
                                ->whereYear($request->type_date, '<=', $request->year_end);
                        } else if ($request->param == 'monthly') {
                            $query->whereMonth($request->type_date, '>=', $request->month_start)
                                ->whereYear($request->type_date, '>=', $request->month_year_start)
                                ->whereMonth($request->type_date, '<=', $request->month_end)
                                ->whereYear($request->type_date, '<=', $request->month_year_start);
                        } else if ($request->param == 'daily') {
                            $query->whereDate($request->type_date, '>=', $request->day_start)
                                ->whereDate($request->type_date, '<=', $request->day_end);
                        }
                    });
                }
            })->whereHas('depositHead', function ($query) {
                $query->whereIn('category', ['KRD']);
            })->where(function ($query) {
                if (session('library_id') != 1) {
                    $query->whereHas('city', function ($query) {
                        $query->where('province_id', session('province_id'));
                    });
                }
            })->where('parent_id', 0)->groupBy('type')->offset($start)->limit($length)->orderBy($order, $dir)->get();

            $totalFiltered = Collection::where(function ($query) use ($request) {
                if ($request->publisher_id) {
                    $query->where('publisher_id', $request->publisher_id);
                }

                if ($request->type) {
                    $query->whereIn('type', $request->type);
                }

                if ($request->province_id) {
                    $query->whereHas('city', function ($query) use ($request) {
                        $query->where('province_id', $request->province_id);
                    });
                }

                if ($request->method) {
                    $query->whereHas('collectionMedia', function ($query) use ($request) {
                        $query->where('method', $request->method);
                    });
                }

                if ($request->status) {
                    $query->where('status', $request->status);
                }

                if ($request->param) {
                    $query->where(function ($query) use ($request) {
                        if ($request->param == 'annual') {
                            $query->whereYear($request->type_date, '>=', $request->year_start)
                                ->whereYear($request->type_date, '<=', $request->year_end);
                        } else if ($request->param == 'monthly') {
                            $query->whereMonth($request->type_date, '>=', $request->month_start)
                                ->whereYear($request->type_date, '>=', $request->month_year_start)
                                ->whereMonth($request->type_date, '<=', $request->month_end)
                                ->whereYear($request->type_date, '<=', $request->month_year_start);
                        } else if ($request->param == 'daily') {
                            $query->whereDate($request->type_date, '>=', $request->day_start)
                                ->whereDate($request->type_date, '<=', $request->day_end);
                        }
                    });
                }
            })->whereHas('depositHead', function ($query) {
                $query->whereIn('category', ['KRD']);
            })->where(function ($query) {
                if (session('library_id') != 1) {
                    $query->whereHas('city', function ($query) {
                        $query->where('province_id', session('province_id'));
                    });
                }
            })->where('parent_id', 0)->groupBy('type')->get()->count();
        } else {
            $queryData = Collection::where(function ($query) use ($request) {
                if ($request->publisher_id) {
                    $query->where('publisher_id', $request->publisher_id);
                }

                if ($request->type) {
                    $query->whereIn('type', $request->type);
                }

                if ($request->province_id) {
                    $query->whereHas('city', function ($query) use ($request) {
                        $query->where('province_id', $request->province_id);
                    });
                }

                if ($request->method) {
                    $query->whereHas('collectionMedia', function ($query) use ($request) {
                        $query->where('method', $request->method);
                    });
                }

                if ($request->status) {
                    $query->where('status', $request->status);
                }

                if ($request->param) {
                    $query->where(function ($query) use ($request) {
                        if ($request->param == 'annual') {
                            $query->whereYear($request->type_date, '>=', $request->year_start)
                                ->whereYear($request->type_date, '<=', $request->year_end);
                        } else if ($request->param == 'monthly') {
                            $query->whereMonth($request->type_date, '>=', $request->month_start)
                                ->whereYear($request->type_date, '>=', $request->month_year_start)
                                ->whereMonth($request->type_date, '<=', $request->month_end)
                                ->whereYear($request->type_date, '<=', $request->month_year_start);
                        } else if ($request->param == 'daily') {
                            $query->whereDate($request->type_date, '>=', $request->day_start)
                                ->whereDate($request->type_date, '<=', $request->day_end);
                        }
                    });
                }
            })->where(function ($query) use ($search) {
                $query->where('type', 'like', "%{$search}%");
            })->where(function ($query) {
                if (session('library_id') != 1) {
                    $query->whereHas('city', function ($query) {
                        $query->where('province_id', session('province_id'));
                    });
                }
            })->whereHas('depositHead', function ($query) {
                $query->whereIn('category', ['KRD']);
            })->where('parent_id', 0)->groupBy('type')->offset($start)->limit($length)->orderBy($order, $dir)->get();

            $totalFiltered = Collection::where(function ($query) use ($request) {
                if ($request->publisher_id) {
                    $query->where('publisher_id', $request->publisher_id);
                }

                if ($request->type) {
                    $query->whereIn('type', $request->type);
                }

                if ($request->province_id) {
                    $query->whereHas('city', function ($query) use ($request) {
                        $query->where('province_id', $request->province_id);
                    });
                }

                if ($request->method) {
                    $query->whereHas('collectionMedia', function ($query) use ($request) {
                        $query->where('method', $request->method);
                    });
                }

                if ($request->status) {
                    $query->where('status', $request->status);
                }

                if ($request->param) {
                    $query->where(function ($query) use ($request) {
                        if ($request->param == 'annual') {
                            $query->whereYear($request->type_date, '>=', $request->year_start)
                                ->whereYear($request->type_date, '<=', $request->year_end);
                        } else if ($request->param == 'monthly') {
                            $query->whereMonth($request->type_date, '>=', $request->month_start)
                                ->whereYear($request->type_date, '>=', $request->month_year_start)
                                ->whereMonth($request->type_date, '<=', $request->month_end)
                                ->whereYear($request->type_date, '<=', $request->month_year_start);
                        } else if ($request->param == 'daily') {
                            $query->whereDate($request->type_date, '>=', $request->day_start)
                                ->whereDate($request->type_date, '<=', $request->day_end);
                        }
                    });
                }
            })->where(function ($query) use ($search) {
                $query->where('type', 'like', "%{$search}%");
            })->where(function ($query) {
                if (session('library_id') != 1) {
                    $query->whereHas('city', function ($query) {
                        $query->where('province_id', session('province_id'));
                    });
                }
            })->whereHas('depositHead', function ($query) {
                $query->whereIn('category', ['KRD']);
            })->where('parent_id', 0)->groupBy('type')->get()->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                $total_submitted = Collection::where('type', $val->type)
                    ->where('status', 1)
                    ->where('parent_id', 0)
                    ->where(function ($query) {
                        if (session('library_id') != 1) {
                            $query->whereHas('city', function ($query) {
                                $query->where('province_id', session('province_id'));
                            });
                        }
                    })
                    ->where(function ($query) use ($request) {
                        if ($request->publisher_id) {
                            $query->where('publisher_id', $request->publisher_id);
                        }

                        if ($request->type) {
                            $query->where('type', $request->type);
                        }

                        if ($request->province_id) {
                            $query->whereHas('city', function ($query) use ($request) {
                                $query->where('province_id', $request->province_id);
                            });
                        }

                        if ($request->method) {
                            $query->whereHas('collectionMedia', function ($query) use ($request) {
                                $query->where('method', $request->method);
                            });
                        }

                        if ($request->param) {
                            $query->where(function ($query) use ($request) {
                                if ($request->param == 'annual') {
                                    $query->whereYear($request->type_date, '>=', $request->year_start)
                                        ->whereYear($request->type_date, '<=', $request->year_end);
                                } else if ($request->param == 'monthly') {
                                    $query->whereMonth($request->type_date, '>=', $request->month_start)
                                        ->whereYear($request->type_date, '>=', $request->month_year_start)
                                        ->whereMonth($request->type_date, '<=', $request->month_end)
                                        ->whereYear($request->type_date, '<=', $request->month_year_start);
                                } else if ($request->param == 'daily') {
                                    $query->whereDate($request->type_date, '>=', $request->day_start)
                                        ->whereDate($request->type_date, '<=', $request->day_end);
                                }
                            });
                        }
                    })
                    ->whereHas('depositHead', function ($query) {
                        $query->whereIn('category', ['KRD']);
                    })
                    ->count();

                $total_accept = Collection::where('type', $val->type)
                    ->where('status', 2)
                    ->where('parent_id', 0)
                    ->whereNotNull('received_at')
                    ->whereNotNull('received_by')
                    ->where(function ($query) {
                        if (session('library_id') != 1) {
                            $query->whereHas('city', function ($query) {
                                $query->where('province_id', session('province_id'));
                            });
                        }
                    })
                    ->where(function ($query) use ($request) {
                        if ($request->publisher_id) {
                            $query->where('publisher_id', $request->publisher_id);
                        }

                        if ($request->type) {
                            $query->where('type', $request->type);
                        }

                        if ($request->province_id) {
                            $query->whereHas('city', function ($query) use ($request) {
                                $query->where('province_id', $request->province_id);
                            });
                        }

                        if ($request->method) {
                            $query->whereHas('collectionMedia', function ($query) use ($request) {
                                $query->where('method', $request->method);
                            });
                        }

                        if ($request->param) {
                            $query->where(function ($query) use ($request) {
                                if ($request->param == 'annual') {
                                    $query->whereYear($request->type_date, '>=', $request->year_start)
                                        ->whereYear($request->type_date, '<=', $request->year_end);
                                } else if ($request->param == 'monthly') {
                                    $query->whereMonth($request->type_date, '>=', $request->month_start)
                                        ->whereYear($request->type_date, '>=', $request->month_year_start)
                                        ->whereMonth($request->type_date, '<=', $request->month_end)
                                        ->whereYear($request->type_date, '<=', $request->month_year_start);
                                } else if ($request->param == 'daily') {
                                    $query->whereDate($request->type_date, '>=', $request->day_start)
                                        ->whereDate($request->type_date, '<=', $request->day_end);
                                }
                            });
                        }
                    })
                    ->whereHas('depositHead', function ($query) {
                        $query->whereIn('category', ['KRD']);
                    })
                    ->count();

                $total_rejected = Collection::where('type', $val->type)
                    ->where('status', 3)
                    ->where('parent_id', 0)
                    ->whereNotNull('rejected_at')
                    ->whereNotNull('rejected_by')
                    ->where(function ($query) {
                        if (session('library_id') != 1) {
                            $query->whereHas('city', function ($query) {
                                $query->where('province_id', session('province_id'));
                            });
                        }
                    })
                    ->where(function ($query) use ($request) {
                        if ($request->publisher_id) {
                            $query->where('publisher_id', $request->publisher_id);
                        }

                        if ($request->type) {
                            $query->where('type', $request->type);
                        }

                        if ($request->province_id) {
                            $query->whereHas('city', function ($query) use ($request) {
                                $query->where('province_id', $request->province_id);
                            });
                        }

                        if ($request->method) {
                            $query->whereHas('collectionMedia', function ($query) use ($request) {
                                $query->where('method', $request->method);
                            });
                        }

                        if ($request->param) {
                            $query->where(function ($query) use ($request) {
                                if ($request->param == 'annual') {
                                    $query->whereYear($request->type_date, '>=', $request->year_start)
                                        ->whereYear($request->type_date, '<=', $request->year_end);
                                } else if ($request->param == 'monthly') {
                                    $query->whereMonth($request->type_date, '>=', $request->month_start)
                                        ->whereYear($request->type_date, '>=', $request->month_year_start)
                                        ->whereMonth($request->type_date, '<=', $request->month_end)
                                        ->whereYear($request->type_date, '<=', $request->month_year_start);
                                } else if ($request->param == 'daily') {
                                    $query->whereDate($request->type_date, '>=', $request->day_start)
                                        ->whereDate($request->type_date, '<=', $request->day_end);
                                }
                            });
                        }
                    })
                    ->whereHas('depositHead', function ($query) {
                        $query->whereIn('category', ['KRD']);
                    })
                    ->count();

                $total_data = Collection::where('type', $val->type)
                    ->where('parent_id', 0)
                    ->where(function ($query) use ($request) {
                        if ($request->publisher_id) {
                            $query->where('publisher_id', $request->publisher_id);
                        }

                        if ($request->type) {
                            $query->where('type', $request->type);
                        }

                        if ($request->province_id) {
                            $query->whereHas('city', function ($query) use ($request) {
                                $query->where('province_id', $request->province_id);
                            });
                        }

                        if ($request->method) {
                            $query->whereHas('collectionMedia', function ($query) use ($request) {
                                $query->where('method', $request->method);
                            });
                        }

                        if ($request->param) {
                            $query->where(function ($query) use ($request) {
                                if ($request->param == 'annual') {
                                    $query->whereYear($request->type_date, '>=', $request->year_start)
                                        ->whereYear($request->type_date, '<=', $request->year_end);
                                } else if ($request->param == 'monthly') {
                                    $query->whereMonth($request->type_date, '>=', $request->month_start)
                                        ->whereYear($request->type_date, '>=', $request->month_year_start)
                                        ->whereMonth($request->type_date, '<=', $request->month_end)
                                        ->whereYear($request->type_date, '<=', $request->month_year_start);
                                } else if ($request->param == 'daily') {
                                    $query->whereDate($request->type_date, '>=', $request->day_start)
                                        ->whereDate($request->type_date, '<=', $request->day_end);
                                }
                            });
                        }

                        if ($request->type_date == 'created_at') {
                            $query->whereNotNull('created_at');
                        } else if ($request->type_date == 'received_at') {
                            $query->where('status', 2)
                                ->whereNotNull('received_by')
                                ->whereNotNull('received_at');
                        } else if ($request->type_date == 'updated_at') {
                            $query->where('status', 3)
                                ->whereNotNull('rejected_by')
                                ->whereNotNull('rejected_at');
                        } else if ($request->type_date == 'validated_at') {
                            $query->where('status', 2)
                                ->whereNotNull('validated_by')
                                ->whereNotNull('validated_at');
                        }
                    })
                    ->where(function ($query) {
                        if (session('library_id') != 1) {
                            $query->whereHas('city', function ($query) {
                                $query->where('province_id', session('province_id'));
                            });
                        }
                    })
                    ->whereHas('depositHead', function ($query) {
                        $query->whereIn('category', ['KRD']);
                    })
                    ->count();

                $response['data'][] = [
                    $nomor,
                    $val->type(),
                    $total_submitted,
                    $total_accept,
                    $total_rejected,
                    $total_data
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

    public function collectionDatatableDetail(Request $request)
    {
        $whereLike = [
            'id',
            'action',
            'publisher_id',
            'period',
            'method',
            'province_id',
            'city_id',
            'title',
            'type',
            'album',
            'series',
            'edition',
            'serial',
            'ddc',
            'volume',
            'code',
            'deposit',
            'publication_year',
            'copyright',
            'preview',
            'lock',
            'manual',
            'access',
            'size_file',
            'extension',
            'created_at',
            'received_at',
            'status',
            'receipt'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        //$order  = $whereLike[$request->input('order.0.column')];
        //$dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        //\Log::info($request);
        $model = Collection::where(function ($query) use ($request) {
            if ($request->publisher_id) {
                $query->where('publisher_id', $request->publisher_id);
            }

            if ($request->type) {
                $query->where('type', $request->type);
            }

            if ($request->province_id) {
                $query->whereHas('city', function ($query) use ($request) {
                    $query->where('province_id', $request->province_id);
                });
            }
            if ($request->extension) {
                $query->whereHas('collectionMedia', function ($query) use ($request) {
                    $query->where('extension', $request->extension);
                });
            }
            if ($request->method) {
                $query->whereHas('collectionMedia', function ($query) use ($request) {
                    $query->where('method', $request->method);
                });
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            if ($request->param) {
                $query->where(function ($query) use ($request) {
                    if ($request->param == 'annual') {
                        $query->whereYear($request->type_date, '>=', $request->year_start)
                            ->whereYear($request->type_date, '<=', $request->year_end);
                    } else if ($request->param == 'monthly') {
                        $query->whereMonth($request->type_date, '>=', $request->month_start)
                            ->whereYear($request->type_date, '>=', $request->month_year_start)
                            ->whereMonth($request->type_date, '<=', $request->month_end)
                            ->whereYear($request->type_date, '<=', $request->month_year_start);
                    } else if ($request->param == 'daily') {
                        $query->whereDate($request->type_date, '>=', $request->day_start)
                            ->whereDate($request->type_date, '<=', $request->day_end);
                    }
                });
            }

            if ($request->type_date == 'created_at') {
                $query->whereNotNull('created_at');
            } else if ($request->type_date == 'received_at') {
                $query->where('status', 2)
                    ->whereNotNull('received_by')
                    ->whereNotNull('received_at');
            } else if ($request->type_date == 'updated_at') {
                $query->where('status', 3)
                    ->whereNotNull('rejected_by')
                    ->whereNotNull('rejected_at');
            } else if ($request->type_date == 'validated_at') {
                $query->where('status', 2)
                    ->whereNotNull('validated_by')
                    ->whereNotNull('validated_at');
            }
        })->where(function ($query) {
            if (session('library_id') != 1) {
                $query->whereHas('city', function ($query) {
                    $query->where('province_id', session('province_id'));
                });
            }
        })->where('parent_id', 0)->whereHas('depositHead', function ($query) {
            $query->whereIn('category', ['KRD']);
        });

        $totalData = Collection::where('parent_id', 0)
            ->whereHas('depositHead', function ($query) {
                $query->whereIn('category', ['KRD']);
            })
            ->count();

        if (empty($search)) {
            $totalFiltered = $model->count();
            $queryData = $model->offset($start)
                ->limit($length)

                ->get();
        } else {
            $model->where(function ($query) use ($search) {
                $query->whereHas('publisher', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhereHas('province', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('city', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                })
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('album', 'like', "%{$search}%")
                    ->orWhere('series', 'like', "%{$search}%")
                    ->orWhere('edition', 'like', "%{$search}%")
                    ->orWhere('serial', 'like', "%{$search}%")
                    ->orWhere('ddc', 'like', "%{$search}%")
                    ->orWhere('volume', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('deposit', 'like', "%{$search}%")
                    ->orWhere('publication_year', 'like', "%{$search}%")
                    ->orWhere('deposit', 'like', "%{$search}%")
                    ->orWhere('copyright', 'like', "%{$search}%");
            });

            $totalFiltered = $model->count();
            $queryData = $model->offset($start)
                ->limit($length)
                ->get();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
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

                if ($val->status == 2) {
                    $receipt = '<a href="' . url('admin/report/collection/download_receipt/' . $val->id) . '" class="text-success" target="_blank"><i class="la la-download"></i> Download</a>';
                } else {
                    $receipt = '-';
                }

                if ($val->type == 5) {
                    $type_method = 4;
                } else if ($val->type == 6) {
                    $type_method = 7;
                } else {
                    $type_method = 2;
                }

                $get_method = $val->collectionMedia()->where('type', $type_method)->first();
                $method     = $get_method ? $get_method->method() : '-';
                $media = null;
                if ($val->type == 1 || $val->type == 2 || $val->type == 3 || $val->type == 4) {
                    $media = $val->collectionMedia->where('type', 2)->first();
                } else if ($val->type == 5) {
                    $media = $val->collectionMedia->where('type', 2)->first();
                } else {
                    $media = $val->collectionMedia->where('type', 2)->first();
                }

                $action = "";
                if ($val->status == 1) {
                    $action = "<a href='" . url('/admin/collection/monitoring/review/' . $val->id) . "' class='btn btn-warning btn-sm text-white'><i class='la la-pencil'></i></a>";
                } else if ($val->status == 2) {
                    $receivedBy    = $val->receivedBy ? $val->receivedBy->username : "";
                    $updatedBy     = $val->updatedBy ? $val->updatedBy->username : "";
                    if (!$val->edit_by) {
                        $action = '<a href="' . url('admin/collection/manage/update/' . $val->id) . '" class="btn btn-warning btn-sm"><i class="la la-pencil"></i></a>';
                    } else {
                        if ($val->edit_by == session('id')) {
                            $action = '<a href="' . url('admin/collection/manage/update/' . $val->id) . '" data-toggle="tooltip" title="sedang anda edit" class="btn btn-info btn-sm"><i class="la la-pencil"></i></a>';
                        } else {
                            $action = '<span class="btn btn-warning btn-sm text-white" data-toggle="tooltip" style="opacity:0.6;" title="sedang diedit oleh ' . $val->editBy->username . '" disabled><i class="la la-ban"></i></span>';
                        }
                    }
                }

                if ($val->publisher) {
                    $publisher_name = $val->publisher ? $val->publisher->name : "";
                    $province_name = $val->publisher->province ? $val->publisher->province->name : '';
                    $city_name     = $val->publisher->city ? $val->publisher->city->name : '';
                } else {
                    $province_name = '';
                    $city_name     = '';
                    $publisher_name = '';
                }

                $response['data'][] = [
                    $nomor,
                    $action,
                    '<span data-toggle="tooltip" title="' . $publisher_name . '">' . Str::limit($publisher_name, 20) . '</span>',
                    $periode,
                    $method,
                    $province_name,
                    $city_name,
                    '<span data-toggle="tooltip" title="' . $val->title . '">' . Str::limit($val->title, 20) . '</span>',
                    $val->type(),
                    $val->album,
                    '<span data-toggle="tooltip" title="' . $val->series . '">' . Str::limit($val->series, 20) . '</span>',
                    '<span data-toggle="tooltip" title="' . $val->edition . '">' . Str::limit($val->edition, 20) . '</span>',
                    $val->serial,
                    $val->ddc,
                    $val->volume,
                    $val->code,
                    $val->deposit,
                    $val->publication_year,
                    '<span data-toggle="tooltip" title="' . $val->copyright . '">' . Str::limit($val->copyright, 20) . '</span>',
                    $val->preview,
                    $val->lock ? 'Ya' : 'Tidak',
                    $val->manual ? 'Ya' : 'Tidak',
                    '<span data-toggle="tooltip" title="' . $val->access() . '">' . Str::limit($val->access(), 20) . '</span>',
                    $media ? $media->size . ' KB' : '0 KB',
                    $media ? $media->extension : '',
                    $val->created_at->format('d-m-Y, H:i'),
                    $val->received_at ? date('d-m-Y, H:i', strtotime($val->received_at)) : '-',
                    $val->status(),
                    $receipt
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

    public function publisher()
    {
        $data = [
            'title'   => 'Laporan Penerbit',
            'content' => 'admin.report.publisher'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function publisherDatatable(Request $request)
    {
        $whereLike = [
            'id',
            'name',
            'address',
            'total_book',
            'total_partitur',
            'total_map',
            'total_serial',
            'total_audio',
            'total_video'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $totalData = Publisher::whereHas('collection', function ($query) {
            $query->where('status', 2)
                ->whereNotNull('received_at')
                ->whereNotNull('received_by');
        })
            ->count();
        $model =  Publisher::whereHas('collection', function ($query) {
            $query->where('status', 2)
                ->whereNotNull('received_at')
                ->whereNotNull('received_by');
        })
            ->where(function ($query) use ($request) {
                if ($request->type) {
                    $query->where('type', $request->type);
                }

                if ($request->collection) {
                    $query->whereHas('collection', function ($query) use ($request) {
                        $query->where('type', $request->collection);
                    });
                }

                if ($request->province_id) {
                    $query->where('province_id', $request->province_id);
                }

                if ($request->param) {
                    $query->where(function ($query) use ($request) {
                        if ($request->param == 'annual') {
                            $query->whereYear($request->type_date, '>=', $request->year_start)
                                ->whereYear($request->type_date, '<=', $request->year_end);
                        } else if ($request->param == 'monthly') {
                            $query->whereMonth($request->type_date, '>=', $request->month_start)
                                ->whereYear($request->type_date, '>=', $request->month_year_start)
                                ->whereMonth($request->type_date, '<=', $request->month_end)
                                ->whereYear($request->type_date, '<=', $request->month_year_start);
                        } else if ($request->param == 'daily') {
                            $query->whereDate($request->type_date, '>=', $request->day_start)
                                ->whereDate($request->type_date, '<=', $request->day_end);
                        }
                    });
                }
            });
        if (empty($search)) {
            $totalFiltered = $model->count();
            $queryData = $model->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $model->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
            $totalFiltered = $model->count();
            $queryData = $model->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                $province = $val->province_id ? $val->province->name : '-';
                $city     = $val->publisher ? ($val->publisher->city ? $val->publisher->city->name : '-') : '-';
                $district = $val->district_id ? $val->district->name : '-';
                $village  = $val->village_id ? $val->village->name : '-';
                $address  = $province . ', ' . $city . ', ' . $district . ', ' . $village . ', ' . $val->address;

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

                if ($request->collection) {
                    $total_data_filter = $val->getTotalCollection($request->collection);
                    if ($request->collection == 1) {
                        $book     = $total_data_filter;
                        $partitur = '-';
                        $map      = '-';
                        $serial   = '-';
                        $audio    = '-';
                        $film     = '-';
                    } else if ($request->collection == 2) {
                        $book     = '-';
                        $partitur = $total_data_filter;
                        $map      = '-';
                        $serial   = '-';
                        $audio    = '-';
                        $film     = '-';
                    } else if ($request->collection == 3) {
                        $book     = '-';
                        $partitur = '-';
                        $map      = $total_data_filter;
                        $serial   = '-';
                        $audio    = '-';
                        $film     = '-';
                    } else if ($request->collection == 4) {
                        $book     = '-';
                        $partitur = '-';
                        $map      = '-';
                        $serial   = $total_data_filter;
                        $audio    = '-';
                        $film     = '-';
                    } else if ($request->collection == 5) {
                        $book     = '-';
                        $partitur = '-';
                        $map      = '-';
                        $serial   = '-';
                        $audio    = $total_data_filter;
                        $film     = '-';
                    } else if ($request->collection == 6) {
                        $book     = '-';
                        $partitur = '-';
                        $map      = '-';
                        $serial   = '-';
                        $audio    = '-';
                        $film     = $total_data_filter;
                    }
                } else {
                    $book     = $val->getTotalCollection(1) > 0 ? $val->getTotalCollection(1) : '-';
                    $partitur = $val->getTotalCollection(2) > 0 ? $val->getTotalCollection(2) : '-';
                    $map      = $val->getTotalCollection(3) > 0 ? $val->getTotalCollection(3) : '-';
                    $serial   = $val->getTotalCollection(4) > 0 ? $val->getTotalCollection(4) : '-';
                    $audio    = $val->getTotalCollection(5) > 0 ? $val->getTotalCollection(5) : '-';
                    $film     = $val->getTotalCollection(6) > 0 ? $val->getTotalCollection(6) : '-';
                }

                $response['data'][] = [
                    $nomor,
                    '<span data-toggle="tooltip" title="' . $val->name . '">' . Str::limit($val->name, 20) . '</span>',
                    $periode,
                    '<span data-toggle="tooltip" title="' . $address . '">' . Str::limit($address, 30) . '</span>',
                    $val->type(),
                    $book,
                    $partitur,
                    $map,
                    $serial,
                    $audio,
                    $film
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

    public function publisherISBN()
    {
        $data = [
            'title'   => 'Laporan Penerbit ISBN',
            'content' => 'admin.report.publisherISBN'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function publisherISBNDatatable(Request $request)
    {
        $where_like = [
            'kd_penerbit',
            'nama_penerbit',
            'percentage',
            'total_elek_diminta',
            'total_cetak_diminta',
            'total_elek_diterima',
            'total_cetak_diterima',
            'total_tagihan_elek',
            'total_tagihan_cetak',
            'total_all'
        ];

        $data   = [];
        $offset = $request->start;
        $limit  = $request->length;
        $order  = $where_like[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        if ($search) {
            array_push($data, [
                'nama_penerbit' => '"' . $search . '"',
                'kd_penerbit'   => '"' . $search . '"'
            ]);
        }

        if ($request->param) {
            if ($request->param == 'annual') {
                $start  = $request->year_start . '-01-01T00:00:00Z';
                $finish = $request->year_end . '-12-31T23:59:59Z';
            } else if ($request->param == 'monthly') {
                $start  = $request->month_year_start . '-' . $request->month_start . '-01T00:00:00Z';
                $finish = date('Y-m-t', strtotime($request->month_year_end . '-' . $request->month_end)) . 'T23:59:59Z';
            } else if ($request->param == 'daily') {
                $start  = $request->day_start . 'T00:00:00Z';
                $finish = $request->day_end . 'T23:59:59Z';
            }

            array_push($data, ['created_date' => "[$start TO $finish]"]);
        }

        if ($request->province_id) {
            $province = Province::find($request->province_id);
            array_push($data, ['provinsi' => '"' . $province->name . '"']);
        }

        if ($request->publisher_id) {
            $specific = ['kd_penerbit' => $request->publisher_id];
        } else {
            $specific = [];
        }

        $pagination = [
            'sort'   => $dir,
            'column' => $order,
            'offset' => $offset,
            'limit'  => $limit
        ];

        $datatable = Solr::datatable('isbn', 'complete', Arr::collapse($data), $pagination, $specific);
        $response['data'] = [];
        $nomor = $offset + 1;

        foreach ($datatable['result'] as $d) {
            $summary    = Solr::summaryBillIsbn('isbn', $d['kd_penerbit'], $request);
            $total_bill = number_format($summary['total_all_bill']);
            $total_rest = number_format($summary['total_all_rest']);
            $total_all  = '<span style="font-size:12px;" class="font-weight-bold text-italic">' . $total_rest . '</span> / ' . $total_bill;

            $response['data'][] = [
                $nomor,
                '<span data-toggle="tooltip" title="' . $d['nama_penerbit'] . '">' . Str::limit($d['nama_penerbit'], 20) . '</span>',
                $summary['percentage'] . '%',
                number_format($summary['total_bill_elek']),
                number_format($summary['total_bill_cetak']),
                number_format($summary['received_elek']),
                number_format($summary['received_cetak']),
                number_format($summary['request_elek']),
                number_format($summary['request_cetak']),
                $total_all
            ];

            $nomor++;
        }

        $response['recordsTotal']    = $datatable['total_all_data'];
        $response['recordsFiltered'] = $datatable['total_filter'];

        return response()->json($response);
    }

    public function logActivity()
    {
        $data = [
            'title'   => 'Laporan Log Aktivitas',
            'content' => 'admin.report.log_activity'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function logActivityDatatable(Request $request)
    {
        $whereLike = [
            'id',
            'causer_id',
            'description',
            'properties',
            'created_at'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $totalData = ActivityLog::count();
        if (empty($search)) {
            $queryData = ActivityLog::offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = ActivityLog::count();
        } else {
            $queryData = ActivityLog::where(function ($query) use ($search) {
                $query->whereHas('user', function ($query) use ($search) {
                    $query->where('username', 'like', "%{$search}%");
                })
                    ->orWhere('description', 'like', "%{$search}%");
            })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = ActivityLog::where(function ($query) use ($search) {
                $query->whereHas('user', function ($query) use ($search) {
                    $query->where('username', 'like', "%{$search}%");
                })
                    ->orWhere('description', 'like', "%{$search}%");
            })
                ->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                $property = '
                    <a href="javascript:void(0);" class="text-primary" data-toggle="modal" data-target="#modalProperty' . $val->id . '"><i class="la la-eye"></i></a>
                    <div class="modal fade text-left" id="modalProperty' . $val->id . '" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Properti</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <pre class="p-2">' . json_encode(json_decode($val->properties), JSON_PRETTY_PRINT) . '</pre>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                ';

                $response['data'][] = [
                    $nomor,
                    $val->user ? $val->user->username : '-',
                    $val->description,
                    $property,
                    date('d-m-Y H:i:s', strtotime($val->created_at))
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

    public function fileDownload()
    {
        $data = [
            'title'   => 'File Download',
            'content' => 'admin.report.file_download'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function fileDownloadDatatable(Request $request)
    {
        $whereLike = [
            'id',
            'slug',
            'date',
            'time',
            'description',
            'link'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $totalData = Download::where('user_id', session('id'))
            ->count();
        if (empty($search)) {
            $queryData = Download::where('user_id', session('id'))
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
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
                ->orderBy($order, $dir)
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
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                $response['data'][] = [
                    $nomor,
                    $val->slug(),
                    date('d F Y', strtotime($val->created_at)),
                    date('H.i', strtotime($val->created_at)),
                    '<a href="javascript:void(0)" onclick="showDescription(' . $val->id . ')" class="text-primary">Lihat</a>',
                    '<a href="' . url('admin/report/file_download/download/' . $val->id) . '" class="text-primary">Download</a>'
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

    public function fileDownloadProcessing(Request $request)
    {
        $data = [
            'param' => $request->param,
            'province_id' => session('library_id') == 1 ? $request->province_id : session('province_id'),
            'method' => $request->method,
            'year_start' => $request->year_start,
            'year_end' => $request->year_end,
            'month_start' => $request->month_start,
            'month_end' => $request->month_end,
            'month_year_start' => $request->month_year_start,
            'month_year_end' => $request->month_year_end,
            'day_start' => $request->day_start,
            'day_end' => $request->day_end,
            'type' => $request->type,
            'collection' => $request->collection,
            'type_date' => $request->type_date,
            'publisher_id' => $request->publisher_id,
            'status' => $request->status,
            'start_date' => $request->start_date,
            'finish_date' => $request->finish_date,
            'action_id' => $request->user_id,
            'extension' => $request->extension,
            'user_id' => $request->user_id ?? session('id'),
            'role_id' => $request->role_id ?? session('role_id'),
            'library_id' => $request->library_id ?? session('library_id'),
            'date' => $request->date,
            'yearly' => $request->yearly,
            'title' => $request->title,
            'code' => $request->code,
            'publication_year' => $request->publication_year,
            'file_type' => $request->file_type,
            'expedition_id' => $request->expedition_id,
            'delivery_date' => $request->delivery_date,
            'accepted_date' => $request->accepted_date,
            'causer_id' => $request->causer_id ?? null,
            'view' => 'admin'
        ];

        if ($request->slug == 'bill_isbn') {
            DownloadReportBillIsbn::dispatch($data)->onQueue('report');
        } else if ($request->slug == 'publisher') {
            DownloadReportPublisher::dispatch($data)->onQueue('report');
        } else if ($request->slug == 'collection') {
            DownloadReportCollection::dispatch($data)->onQueue('report');
        } else if ($request->slug == 'performance_user') {
            DownloadReportPerformanceUser::dispatch($data)->onQueue('report');
        } else if ($request->slug == 'periodic') {
            DownloadReportPeriodic::dispatch($data)->onQueue('report');
        } else if ($request->slug == 'data_isrc') {
            DownloadDataIsrc::dispatch($data)->onQueue('report');
        } else if ($request->slug == 'publisher_isbn') {
            DownloadReportPublisherISBN::dispatch($data)->onQueue('report');
        } else if ($request->slug == 'collection_delivery') {
            DownloadReportCollectionDelivery::dispatch($data)->onQueue('report');
        } else if ($request->slug == 'report_distribution') {
            DownloadReportDistribution::dispatch($data)->onQueue('report');
        }

        return response()->json(200);
    }

    public function fileDownloadDescription($id)
    {
        $data = Download::find($id);

        if ($data->slug == 'periodic') {
            $detail_date   = $data->description()->date;
            $detail_status = $data->description()->status;

            if ($detail_status == 1) {
                $status = 'Review';
            } else if ($detail_status == 2) {
                $status = 'Diterima';
            } else if ($detail_status == 3) {
                $status = 'Masalah';
            } else if ($detail_status == 4) {
                $status = 'Pre Proses';
            } else if ($detail_status == 5) {
                $status = 'Ditolak';
            } else {
                $status = 'Invalid';
            }

            if ($detail_date == 'rejected_at') {
                $date = 'Ditolak';
            } else if ($detail_date == 'received_at') {
                $date = 'Diterima';
            } else if ($detail_date == 'validated_at') {
                $date = 'Divalidasi';
            } else if ($detail_date == 'created_at') {
                $date = 'Dibuat';
            } else if ($detail_date == 'updated_at') {
                $date = 'Diedit';
            } else {
                $date = 'Invalid';
            }

            $response = [
                'yearly' => $data->description()->yearly,
                'status' => $status,
                'date'   => $date,
                'slug'   => $data->slug
            ];
        } else {
            if ($data->slug == 'performance_user') {
                if ($data->description()->start_date && $data->description()->finish_date) {
                    $start   = date('d-m-Y', strtotime($data->description()->start_date));
                    $finish  = date('d-m-Y', strtotime($data->description()->finish_date));
                    $periode = $start . ' s/d ' . $finish;
                } else if ($data->description()->start_date) {
                    $periode = date('d-m-Y', strtotime($data->description()->start_date));
                } else if ($data->description()->finish_date) {
                    $periode = date('d-m-Y', strtotime($data->description()->finish_date));
                } else {
                    $periode = 'Semua Periode';
                }
            } else {
                if ($data->description()->param == 'annual') {
                    $periode = date('Y', strtotime($data->description()->year_start)) . ' s/d ' . $periode = date('Y', strtotime($data->description()->year_end));
                } else if ($data->description()->param == 'monthly') {
                    $start   = $data->description()->month_year_start . '-' . $data->description()->month_start;
                    $end     = $data->description()->month_year_end . '-' . $data->description()->month_end;
                    $periode = date('F Y', strtotime($start)) . ' s/d ' . $periode = date('F Y', strtotime($end));
                } else if ($data->description()->param == 'daily') {
                    $periode = date('d-m-Y', strtotime($data->description()->day_start)) . ' s/d ' . $periode = date('Y', strtotime($data->description()->day_end));
                } else {
                    $periode = 'Semua Periode';
                }
            }

            if (isset($data->description()->province_id)) {
                $province = Province::find($data->description()->province_id)->name;
            } else {
                $province = 'Semua Provinsi';
            }

            if (isset($data->description()->method)) {
                if ($data->description()->method == 1) {
                    $method = 'API';
                } else if ($data->description()->method == 2) {
                    $method = 'SFTP';
                } else if ($data->description()->method == 3) {
                    $method = 'Mandiri';
                } else if ($data->description()->method == 4) {
                    $method = 'Manual';
                } else if ($data->description()->method == 5) {
                    $method = 'Sistem';
                } else if ($data->description()->method == 6) {
                    $method = 'Bulk Penerbit';
                } else if ($data->description()->method == 7) {
                    $method = 'Bulk Admin';
                } else {
                    $method = 'Semua Metode';
                }
            } else {
                $method = 'Semua Metode';
            }

            if ($data->description()->type) {
                if ($data->slug == 'bill_isbn') {
                    $type = ucwords($data->description()->type);
                } else if ($data->slug == 'collection') {
                    if ($data->description()->type == 1) {
                        $type = 'Buku';
                    } else if ($data->description()->type == 2) {
                        $type = 'Partitur';
                    } else if ($data->description()->type == 3) {
                        $type = 'Peta';
                    } else if ($data->description()->type == 4) {
                        $type = 'Serial';
                    } else if ($data->description()->type == 5) {
                        $type = 'Audio';
                    } else if ($data->description()->type == 6) {
                        $type = 'Film';
                    } else {
                        $type = 'Semua Tipe';
                    }
                } else if ($data->slug == 'publisher') {
                    if ($data->description()->type == 1) {
                        $type = 'Swasta';
                    } else if ($data->description()->type == 2) {
                        $type = 'Perorangan';
                    } else if ($data->description()->type == 3) {
                        $type = 'Pemerintah';
                    } else {
                        $type = 'Semua';
                    }
                } else if ($data->slug == 'performance_user') {
                    if ($data->description()->type == 1) {
                        $type = 'Tolak';
                    } else if ($data->description()->type == 2) {
                        $type = 'Terima';
                    } else if ($data->description()->type == 3) {
                        $type = 'Kelola (Before After)';
                    } else if ($data->description()->type == 4) {
                        $type = 'Validasi';
                    } else if ($data->description()->type == 5) {
                        $type = 'Masalah';
                    } else {
                        $type = 'Semua';
                    }
                }
            } else {
                $type = 'Semua Tipe';
            }

            if ($data->description()->publisher_id) {
                if ($data->slug == 'bill_isbn') {
                    $mst_penerbit = Solr::data('isbn', 'mst_penerbit', ['kd_penerbit' => $data->description()->publisher_id]);
                    $publisher    = $mst_penerbit[0]['nama_penerbit'];
                } else {
                    $publisher = Publisher::find($data->description()->publisher_id)->name;
                }
            } else {
                $publisher = 'Semua Penerbit';
            }

            if (isset($data->description()->action_id)) {
                $action = User::find($data->description()->action_id)->username;
            } else {
                $action = '-';
            }

            $response = [
                'periode'   => $periode,
                'province'  => $province,
                'method'    => $method,
                'type'      => $type,
                'publisher' => $publisher,
                'user'      => $action,
                'slug'      => $data->slug
            ];
        }

        return response()->json($response);
    }

    public function fileDownloadRun($id)
    {
        $file = Download::find($id);
        return response()->download(Storage::disk($file->location->location)->path($file->link));
    }

    public function downloadReceipt($id)
    {
        $collection = Collection::where('id', $id)
            ->where('parent_id', 0)
            ->where('status', 2)
            ->whereNotNull('received_at')
            ->whereNotNull('received_by')
            ->first();

        if ($collection) {
            $collection_media   = $collection->collectionMedia()->where('type', 1)->first();
            $template           = Setting::where('slug', 'template-email-collection-success')->first();
            $header             = Setting::where('slug', 'template-email-header')->first();
            $footer             = Setting::where('slug', 'template-email-footer')->first();
            $link_header        = public_path('storage/' . str_replace('public/', '', $header->content));
            $link_footer        = public_path('storage/' . str_replace('public/', '', $footer->content));
            $take_now           = Director::where('province_id', session('province_id'))->orderByRaw('DATE(position_start) DESC')->first();
            $received_at        = date('Y-m-m', strtotime($collection->received_at));
            $director_signature = Director::where('province_id', session('province_id'))->whereDate('position_start', '<', $received_at)->first();

            if ($director_signature) {
                $director = $director_signature;
            } else {
                $director = $take_now;
            }

            if ($director->signature) {
                $signature_image = public_path('storage/' . str_replace('public/', '', $director->signature));
            } else {
                $signature_image = '';
            }

            $signature = $director->position . '<br><br><img src="' . $signature_image . '" width="150"><br><br>' . $director->name . '<br><span style="font-weight:bold;">NIP. ' . $director->nip . '</span>';

            $data = [
                'received_at' => date('d F Y', strtotime($collection->received_at)),
                'code'        => $collection->code,
                'publisher'   => $collection->publisher->name,
                'title'       => $collection->title,
                'code'        => $collection->code,
                'depositid'   => $collection->deposit,
                'mimes'       => $collection_media ? $collection_media->mimes : '-',
                'hash'        => $collection_media ? $collection_media->hash : '-',
                'size'        => $collection_media ? $collection_media->size : '-',
                'director'    => $signature,
                'header'      => '<img src="' . $link_header . '" style="max-width:100%;">',
                'footer'      => '<img src="' . $link_footer . '" style="max-width:100%; margin-bottom:10px">',
            ];

            $html = $template->parse($data);
            $pdf  = new CustomTCPDF();
            $pdf->SetMargins(10, 5, 10, 0);
            $pdf->SetAutoPageBreak(true, 0);
            $pdf->AddPage();
            $pdf->writeHTML($html, true, false, true, false, '');

            $filename = storage_path("app/public/receipt/$collection->deposit.pdf");
            return $pdf->output($filename, 'I');
        }

        return redirect()->back();
    }

    public function performanceUser()
    {
        $data = [
            'title' => 'Kinerja User',
            'access_all_user' => UserCertainAccess::where('role_id', session('role_id'))->where('access', 3)->count(),
            'user' => User::where('userable_type', 'admins')->get(),
            'content' => 'admin.report.performance_user'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function performanceUserDatatable(Request $request)
    {
        $whereLike = [
            'id',
            'causer_id',
            'subject_id',
            'description',
            'properties',
            'created_at'
        ];

        $start = $request->input('start');
        $length = $request->input('length');
        $order = $whereLike[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $totalData = ActivityLog::has('collection')
            ->where('log_name', 'collections')
            ->whereHas('collection', function ($query) {
                if (session('library_id') != 1) {
                    $query->whereHas('city', function ($query) {
                        $query->where('province_id', session('province_id'));
                    });
                }
            })
            ->count();

        if (empty($search)) {
            $queryData = ActivityLog::has('collection')
                ->where('log_name', 'collections')
                ->whereHas('collection', function ($query) {
                    if (session('library_id') != 1) {
                        $query->whereHas('city', function ($query) {
                            $query->where('province_id', session('province_id'));
                        });
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->start_date && $request->finish_date) {
                        $query->whereDate('created_at', '>=', $request->start_date)
                            ->whereDate('created_at', '<=', $request->finish_date);
                    } else if ($request->start_date) {
                        $query->whereDate('created_at', '>=', $request->start_date);
                    } else if ($request->finish_date) {
                        $query->whereDate('created_at', '>=', $request->finish_date);
                    }

                    if ($request->causer_id) {
                        $query->where('causer_id', $request->causer_id);
                    }

                    if ($request->type) {
                        if ($request->type == 1) {
                            $query->where('description', 'like', "%menolak koleksi%");
                        } else if ($request->type == 2) {
                            $query->where('description', 'like', "%menyetujui koleksi%");
                        } else if ($request->type == 3) {
                            $query->where('description', 'like', "%mengubah data koleksi%");
                        } else if ($request->type == 4) {
                            $query->where('description', 'like', "%mengunci koleksi%")
                                ->orWhere('description', 'like', "%membuka kunci koleksi%");
                        } else if ($request->type == 5) {
                            $query->where('description', 'like', "%koleksi bermasalah%");
                        }
                    }
                })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = ActivityLog::has('collection')
                ->where('log_name', 'collections')
                ->whereHas('collection', function ($query) {
                    if (session('library_id') != 1) {
                        $query->whereHas('city', function ($query) {
                            $query->where('province_id', session('province_id'));
                        });
                    }
                })
                ->where(function ($query) use ($request) {
                    if ($request->user_id) {
                        $query->where('causer_id', $request->user_id);
                    }

                    if ($request->start_date && $request->finish_date) {
                        $query->whereDate('created_at', '>=', $request->start_date)
                            ->whereDate('created_at', '<=', $request->finish_date);
                    } else if ($request->start_date) {
                        $query->whereDate('created_at', '>=', $request->start_date);
                    } else if ($request->finish_date) {
                        $query->whereDate('created_at', '>=', $request->finish_date);
                    }

                    if ($request->type) {
                        if ($request->type == 1) {
                            $query->where('description', 'like', "%menolak koleksi%");
                        } else if ($request->type == 2) {
                            $query->where('description', 'like', "%menyetujui koleksi%");
                        } else if ($request->type == 3) {
                            $query->where('description', 'like', "%mengubah data koleksi%");
                        } else if ($request->type == 4) {
                            $query->where('description', 'like', "%mengunci koleksi%")
                                ->orWhere('description', 'like', "%membuka kunci koleksi%");
                        } else if ($request->type == 5) {
                            $query->where('description', 'like', "%koleksi bermasalah%");
                        }
                    }
                })
                ->count();
        } else {
            $queryData = ActivityLog::has('collection')
                ->where('log_name', 'collections')
                ->whereHas('collection', function ($query) {
                    if (session('library_id') != 1) {
                        $query->whereHas('city', function ($query) {
                            $query->where('province_id', session('province_id'));
                        });
                    }
                })
                ->where(function ($query) use ($search, $request) {
                    $query->whereHas('user', function ($query) use ($search) {
                        $query->where('username', 'like', "%$search%");
                    })
                        ->orWhereHas('collection', function ($query) use ($search) {
                            $query->where('title', 'like', "%$search%");
                        })
                        ->orWhere('description', 'like', "%$search%");

                    if ($request->start_date && $request->finish_date) {
                        $query->whereDate('created_at', '>=', $request->start_date)
                            ->whereDate('created_at', '<=', $request->finish_date);
                    } else if ($request->start_date) {
                        $query->whereDate('created_at', '>=', $request->start_date);
                    } else if ($request->finish_date) {
                        $query->whereDate('created_at', '>=', $request->finish_date);
                    }

                    if ($request->user_id) {
                        $query->where('causer_id', $request->user_id);
                    }

                    if ($request->type) {
                        if ($request->type == 1) {
                            $query->where('description', 'like', "%menolak koleksi%");
                        } else if ($request->type == 2) {
                            $query->where('description', 'like', "%menyetujui koleksi%");
                        } else if ($request->type == 3) {
                            $query->where('description', 'like', "%mengubah data koleksi%");
                        } else if ($request->type == 4) {
                            $query->where('description', 'like', "%mengunci koleksi%")
                                ->orWhere('description', 'like', "%membuka kunci koleksi%");
                        } else if ($request->type == 5) {
                            $query->where('description', 'like', "%koleksi bermasalah%");
                        }
                    }
                })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = ActivityLog::has('collection')
                ->where('log_name', 'collections')
                ->whereHas('collection', function ($query) {
                    if (session('library_id') != 1) {
                        $query->whereHas('city', function ($query) {
                            $query->where('province_id', session('province_id'));
                        });
                    }
                })
                ->where(function ($query) use ($search, $request) {
                    $query->whereHas('user', function ($query) use ($search) {
                        $query->where('username', 'like', "%$search%");
                    })
                        ->orWhereHas('collection', function ($query) use ($search) {
                            $query->where('title', 'like', "%$search%");
                        })
                        ->orWhere('description', 'like', "%$search%");

                    if ($request->start_date && $request->finish_date) {
                        $query->whereDate('created_at', '>=', $request->start_date)
                            ->whereDate('created_at', '<=', $request->finish_date);
                    } else if ($request->start_date) {
                        $query->whereDate('created_at', '>=', $request->start_date);
                    } else if ($request->finish_date) {
                        $query->whereDate('created_at', '>=', $request->finish_date);
                    }

                    if ($request->user_id) {
                        $query->where('causer_id', $request->user_id);
                    }

                    if ($request->type) {
                        if ($request->type == 1) {
                            $query->where('description', 'like', "%menolak koleksi%");
                        } else if ($request->type == 2) {
                            $query->where('description', 'like', "%menyetujui koleksi%");
                        } else if ($request->type == 3) {
                            $query->where('description', 'like', "%mengubah data koleksi%");
                        } else if ($request->type == 4) {
                            $query->where('description', 'like', "%mengunci koleksi%")
                                ->orWhere('description', 'like', "%membuka kunci koleksi%");
                        } else if ($request->type == 5) {
                            $query->where('description', 'like', "%koleksi bermasalah%");
                        }
                    }
                })
                ->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                $property = '
                    <a href="javascript:void(0);" class="text-primary" data-toggle="modal" data-target="#modalProperty' . $val->id . '"><i class="la la-eye"></i></a>
                    <div class="modal fade text-left" id="modalProperty' . $val->id . '" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Properti</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <pre class="p-2">' . json_encode(json_decode($val->properties), JSON_PRETTY_PRINT) . '</pre>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                ';

                $response['data'][] = [
                    $nomor,
                    $val->user ? $val->user->username : '-',
                    $val->collection ? $val->collection->title : '-',
                    $val->description,
                    $property,
                    $val->created_at->format('Y-m-d H:i:s')
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

    public function periodic(Request $request)
    {
        $data = [
            'title'   => 'Periodik',
            'content' => 'admin.report.periodic'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function loadDataPeriodic(Request $request)
    {
        $item     = [];
        $book     = 0;
        $partitur = 0;
        $map      = 0;
        $serial   = 0;
        $audio    = 0;
        $film     = 0;

        for ($i = 1; $i <= 12; $i++) {
            $item[]['data']['month'] = GeneralHelper::getMonth($i < 10 ? '0' . $i : $i);
            for ($col = 1; $col <= 6; $col++) {
                $query = Collection::where(function ($query) use ($i, $request) {
                    $query->whereMonth($request->date, $i)
                        ->whereYear($request->date, $request->yearly);
                })
                    ->where('status', $request->status)
                    ->where('type', $col)
                    ->where('parent_id', 0)
                    ->count();

                if ($col == 1) {
                    $book += $query;
                } else if ($col == 2) {
                    $partitur += $query;
                } else if ($col == 3) {
                    $map += $query;
                } else if ($col == 4) {
                    $serial += $query;
                } else if ($col == 5) {
                    $audio += $query;
                } else if ($col == 6) {
                    $film += $query;
                }

                $index = $i - 1;
                $item[$index]['data']['item'][] = $query;
            }
        }

        $response = [
            'item'  => $item,
            'total' => [$book, $partitur, $map, $serial, $audio, $film]
        ];

        return response()->json($response);
    }

    public function collectionDelivery()
    {
        $data = [
            'title'   => 'Laporan Pengiriman KCKR Analog',
            'expedition' => Expedition::get(),
            'content' => 'admin.report.collection_delivery'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function collectionDeliveryDatatable(Request $request)
    {
        $whereLike = [
            'id',
            'name',
            'address',
            'total_book',
            'total_partitur',
            'total_map',
            'total_serial',
            'total_audio',
            'total_video'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $totalData = CollectionCopy::join('delivery_form', 'collection_copies.delivery_form_id', '=', 'delivery_form.id')
            ->where('collection_copies.availability', '<>', '11')
            ->groupBy('collection_copies.collection_id')
            ->get()
            ->count();

        $model =  CollectionCopy::join('delivery_form', 'collection_copies.delivery_form_id', '=', 'delivery_form.id')
            ->select(
                'collection_copies.*',
                DB::raw('SUM(CASE WHEN delivery_form.library_id = 1 THEN 1 ELSE 0 END) AS perpusnas_count'),
                DB::raw('SUM(CASE WHEN delivery_form.library_id <> 1 THEN 1 ELSE 0 END) AS province_count'),
                DB::raw('MIN(CASE WHEN delivery_form.library_id = 1 THEN delivery_date END) AS perpusnas_delivery_date'),
                DB::raw('MIN(CASE WHEN delivery_form.library_id = 1 THEN accepted_date END) AS perpusnas_accepted_date'),
                DB::raw('MIN(CASE WHEN delivery_form.library_id <> 1 THEN delivery_date END) AS province_delivery_date'),
                DB::raw('MIN(CASE WHEN delivery_form.library_id <> 1 THEN accepted_date END) AS province_accepted_date')
            )
            ->where('collection_copies.availability', '<>', '11')
            ->where(function ($query) use ($request) {

                if ($request->library_id) {
                    $query->where('library_id', $request->library_id);
                } else {
                    if (session('library_id') <> 1) {
                        $query->where('library_id', session('library_id'));
                    }
                }

                if ($request->publisher_id) {
                    $query->where('publisher_id', $request->publisher_id);
                }

                if ($request->expedition_id) {
                    $query->where('expedition_id', $request->expedition_id);
                }

                if ($request->param) {
                    $query->where(function ($query) use ($request) {
                        if ($request->param == 'annual') {
                            $query->whereYear($request->type_date, '>=', $request->year_start)
                                ->whereYear($request->type_date, '<=', $request->year_end);
                        } else if ($request->param == 'monthly') {
                            $query->whereMonth($request->type_date, '>=', $request->month_start)
                                ->whereYear($request->type_date, '>=', $request->month_year_start)
                                ->whereMonth($request->type_date, '<=', $request->month_end)
                                ->whereYear($request->type_date, '<=', $request->month_year_start);
                        } else if ($request->param == 'daily') {
                            $query->whereDate($request->type_date, '>=', $request->day_start)
                                ->whereDate($request->type_date, '<=', $request->day_end);
                        }
                    });
                }
            })
            ->groupBy('collection_copies.collection_id');

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
            $nomor = $start + 1;
            foreach ($queryData as $val) {

                $trk_no = '';
                if (session('library_id') == 1) {
                    $trk_no = $val->collection->mark_national;
                } else {
                    $trk_no = $val->collection->mark_province;
                }

                $pengarang = '';
                if (count($val->collection->collectionContributor) > 0) {
                    $pengarang = $val->collection->collectionContributor[0]->author->fullname;
                }

                $response['data'][] = [
                    $nomor,
                    $val->collection->publisher->name,
                    $val->delivery_form->expedition->name,
                    !empty($val->collection->depositHead) ? $val->collection->depositHead->shape : "",
                    $val->collection->code,
                    $val->collection->title,
                    $pengarang,
                    $trk_no,
                    $val->collection->publicationMonth() . '/' . $val->collection->publication_year,
                    $val->perpusnas_count,
                    $val->province_count,
                    $val->perpusnas_delivery_date,
                    $val->perpusnas_accepted_date,
                    $val->province_delivery_date,
                    $val->province_accepted_date,
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

    public function distribution()
    {
        $data = [
            'title'   => 'Laporan Distribusi',
            'expedition' => Expedition::all(),
            'library' => Library::all(),
            'status' => DeliveryForm::select('status')->distinct('status')->pluck('status'),
            'content' => 'admin.report.distribution'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function distributionDatatable(Request $request)
    {
        $whereLike = [
            'id',
            'expedition_id',
            'publisher_id',
            'library_id',
            'delivery_date',
            'accepted_date',
            'status',
            'letter_no'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $totalData = DeliveryForm::count();

        $queryData = DeliveryForm::where(function ($query) use ($search, $request) {
            if ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('receipt_no', 'like', "%$search%")
                        ->orWhere('letter_no', 'like', "%$search%")
                        ->orWhereHas('publisher', function ($query) use ($search) {
                            $query->where('name', 'like', "%$search%");
                        })
                        ->orWhereHas('expedition', function ($query) use ($search) {
                            $query->where('name', 'like', "%$search%");
                        })
                        ->orWhereHas('library', function ($query) use ($search) {
                            $query->where('name', 'like', "%$search%");
                        });
                });
            }

            if ($request->expedition_id) {
                $query->where('expedition_id', $request->expedition_id);
            }

            if ($request->publisher_id) {
                $query->where('publisher_id', $request->publisher_id);
            }

            if ($request->library_id) {
                $query->where('library_id', $request->library_id);
            }

            if ($request->delivery_date) {
                $query->whereDate('delivery_date', $request->delivery_date);
            }

            if ($request->accepted_date) {
                $query->whereDate('accepted_date', $request->accepted_date);
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }
        })->offset($start)->limit($length)->orderBy($order, $dir)->get();

        $totalFiltered = DeliveryForm::where(function ($query) use ($search, $request) {
            if ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('receipt_no', 'like', "%$search%")
                        ->orWhere('letter_no', 'like', "%$search%")
                        ->orWhereHas('publisher', function ($query) use ($search) {
                            $query->where('name', 'like', "%$search%");
                        })
                        ->orWhereHas('expedition', function ($query) use ($search) {
                            $query->where('name', 'like', "%$search%");
                        })
                        ->orWhereHas('library', function ($query) use ($search) {
                            $query->where('name', 'like', "%$search%");
                        });
                });
            }

            if ($request->expedition_id) {
                $query->where('expedition_id', $request->expedition_id);
            }

            if ($request->publisher_id) {
                $query->where('publisher_id', $request->publisher_id);
            }

            if ($request->library_id) {
                $query->where('library_id', $request->library_id);
            }

            if ($request->delivery_date) {
                $query->whereDate('delivery_date', $request->delivery_date);
            }

            if ($request->accepted_date) {
                $query->whereDate('accepted_date', $request->accepted_date);
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }
        })->count();

        $response['data'] = [];
        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                $response['data'][] = [
                    $nomor,
                    $val->expedition->name ?? '',
                    $val->publisher->name ?? '',
                    $val->library->name ?? '',
                    $val->delivery_date,
                    $val->accepted_date,
                    $val->status,
                    $val->letter_no,
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
