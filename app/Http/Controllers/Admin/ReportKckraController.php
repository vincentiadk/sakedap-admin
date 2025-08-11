<?php

namespace App\Http\Controllers\Admin;

use App\Models\Solr;
use App\Models\User;
use App\Models\Download;
use App\Models\Province;
use App\Models\Publisher;
use App\Models\Collection;
use App\Models\DepositHead;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CollectionCopy;
use App\Models\LibraryLocation;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Jobs\DownloadReportCollectionKckra;

class ReportKckraController extends Controller
{
    public function collection()
    {
        $arrCategoryDH = [
            'KC' => 'Karya Cetak',
            'KRA' => 'Karya Rekam Analog',
            'KRD' => 'Karya Rekam Digital'
        ];
        $getDepositHead = DepositHead::whereIn('category', ['KC', 'KRA'])->get();
        $deposit_head = [];
        foreach ($getDepositHead as $key => $value) {
            $deposit_head[$value->category][] = $value;
        }

        $arrConditions = [
            '1' => 'Sangat Baik',
            '2' => 'Baik',
            '3' => 'Cukup',
            '4' => 'Rusak'
        ];

        $availability = [
            'tersedia',
            'dalam pengiriman ke pengelolaan',
            'sedang didayagunakan',
            'hilang',
            'rusak',
            'sedang diperbaiki',
            'sedang diolah',
            'masih di ekspedisi',
            'sedang dicek',
            'diterima pengelohan',
            'diterima tim kckr',
            'ditolak',
        ];

        $library_id = session('library_id');
        $data = [
            'title'   => 'Laporan Koleksi',
            'deposit_head' => $deposit_head,
            'category_dh' => $arrCategoryDH,
            'condition' => $arrConditions,
            'availability' => $availability,
            'lib_loc' => LibraryLocation::where('library_id', $library_id)->orderBy('name', 'asc')->get(),
            'content' => 'admin.report.collection_kckra'
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
        })
            ->where('parent_id', 0)
            ->whereHas('depositHead', function ($query) {
                $query->whereIn('category', ['KC', 'KRA']);
            })
            ->groupBy('type')
            ->get()
            ->count();

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
                }
            })
                ->where(function ($query) {
                    if (session('library_id') != 1) {
                        $query->whereHas('city', function ($query) {
                            $query->where('province_id', session('province_id'));
                        });
                    }
                })
                ->where('parent_id', 0)
                ->whereHas('depositHead', function ($query) {
                    $query->whereIn('category', ['KC', 'KRA']);
                })
                ->groupBy('type')
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
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
                }
            })
                ->where(function ($query) {
                    if (session('library_id') != 1) {
                        $query->whereHas('city', function ($query) {
                            $query->where('province_id', session('province_id'));
                        });
                    }
                })
                ->where('parent_id', 0)
                ->whereHas('depositHead', function ($query) {
                    $query->whereIn('category', ['KC', 'KRA']);
                })
                ->groupBy('type')
                ->get()
                ->count();
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
                }
            })
                ->where(function ($query) use ($search) {
                    $query->where('type', 'like', "%{$search}%");
                })
                ->where(function ($query) {
                    if (session('library_id') != 1) {
                        $query->whereHas('city', function ($query) {
                            $query->where('province_id', session('province_id'));
                        });
                    }
                })
                ->where('parent_id', 0)
                ->whereHas('depositHead', function ($query) {
                    $query->whereIn('category', ['KC', 'KRA']);
                })
                ->groupBy('type')
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
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
                }
            })
                ->where(function ($query) use ($search) {
                    $query->where('type', 'like', "%{$search}%");
                })
                ->where(function ($query) {
                    if (session('library_id') != 1) {
                        $query->whereHas('city', function ($query) {
                            $query->where('province_id', session('province_id'));
                        });
                    }
                })
                ->where('parent_id', 0)
                ->whereHas('depositHead', function ($query) {
                    $query->whereIn('category', ['KC', 'KRA']);
                })
                ->groupBy('type')
                ->get()
                ->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                $total_submitted = Collection::where('type', $val->type)
                    ->where('status', 1)
                    ->where('parent_id', 0)
                    ->whereHas('depositHead', function ($query) {
                        $query->whereIn('category', ['KC', 'KRA']);
                    })
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
                        }
                    })
                    ->count();

                $total_accept = Collection::where('type', $val->type)
                    ->where('status', 2)
                    ->where('parent_id', 0)
                    ->whereHas('depositHead', function ($query) {
                        $query->whereIn('category', ['KC', 'KRA']);
                    })
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
                        }
                    })
                    ->count();

                $total_rejected = Collection::where('type', $val->type)
                    ->where('status', 3)
                    ->where('parent_id', 0)
                    ->whereHas('depositHead', function ($query) {
                        $query->whereIn('category', ['KC', 'KRA']);
                    })
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
                        }
                    })
                    ->count();

                $total_data = Collection::where('type', $val->type)
                    ->where('parent_id', 0)
                    ->whereHas('depositHead', function ($query) {
                        $query->whereIn('category', ['KC', 'KRA']);
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
                    ->count();

                $response['data'][] = [
                    $nomor,
                    $val->depositHead->shape,
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

        $arrConditions = [
            '1' => 'Sangat Baik',
            '2' => 'Baik',
            '3' => 'Cukup',
            '4' => 'Rusak'
        ];

        $availability = [
            'tersedia',
            'dalam pengiriman ke pengelolaan',
            'sedang didayagunakan',
            'hilang',
            'rusak',
            'sedang diperbaiki',
            'sedang diolah',
            'masih di ekspedisi',
            'sedang dicek',
            'diterima pengelohan',
            'diterima tim kckr',
            'ditolak',
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        //$order  = $whereLike[$request->input('order.0.column')];
        //$dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $model = CollectionCopy::whereHas('collection', function ($query) use ($request) {
            if ($request->publisher_id) {
                $query->where('publisher_id', $request->publisher_id);
            }

            if ($request->type) {
                $query->where('type', $request->type);
            }

            if ($request->province_id) {
                $query->whereHas('publisher', function ($query) use ($request) {
                    $query->where('province_id', $request->province_id);
                });
            }

            if ($request->extension) {
                $query->whereHas('collectionMedia', function ($query) use ($request) {
                    $query->where('extension', $request->extension);
                });
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            if ($request->param) {
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

            if (session('library_id') == '1') {
                $query->whereNotNull('mark_national');
            }

            $query->whereHas('depositHead', function ($query) {
                $query->whereIn('category', ['KC', 'KRA']);
            });

            $query->where(function ($query) {
                if (session('library_id') != 1) {
                    $query->whereHas('publisher', function ($query) {
                        $query->where('province_id', session('province_id'));
                    });
                }
            })->where('parent_id', 0);
        })->whereHas('lib_location',  function ($query) use ($request) {
            $query->where('publish', 1)->where('library_id', session('library_id'));
        })->where(function ($query) use ($request) {
            if ($request->lib_loc_id) {
                $query->where('lib_loc_id', $request->lib_loc_id);
            }
            if ($request->condition) {
                $query->where('condition', $request->condition);
            }
            if ($request->availability) {
                $query->where('availability', $request->availability);
            }
        });

        // dd($model->toSql());

        $totalData = CollectionCopy::whereHas(
            'collection',
            function ($query) use ($request) {
                $query->where('parent_id', 0);
            }
        )->count();

        if (empty($search)) {
            $totalFiltered = $model->count();
            $queryData = $model->offset($start)
                ->limit($length)
                ->get();
        } else {
            $model->whereHas('collection', function ($query) use ($search) {
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
            })->orWhereHas('lib_location',  function ($query) use ($search) {
                $query->where('publish', 1)->where('library_id', session('library_id'));
                $query->where('name', 'like', "%{$search}%");
            });



            $totalFiltered = $model->count();
            $queryData = $model->offset($start)
                ->limit($length)
                ->get();
        }
        Log::info($model->toSql());
        $response['data'] = [];
        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                if ($request->param) {
                    if ($request->param == 'annual') {
                        $periode = date('Y', strtotime($val->collection->created_at));
                    } else if ($request->param == 'monthly') {
                        $periode = date('F Y', strtotime($val->collection->created_at));
                    } else if ($request->param == 'daily') {
                        $periode = date('d F Y', strtotime($val->collection->created_at));
                    }
                } else {
                    $periode = 'Semua Periode';
                }

                if ($val->collection->status == 2) {
                    $receipt = '<a href="' . url('admin/report/collection/kckra/download_receipt/' . $val->collection->id) . '" class="text-success" target="_blank"><i class="la la-download"></i> Download</a>';
                } else {
                    $receipt = '-';
                }

                $action = "";
                if ($val->collection->status == 1) {
                    $action = "<a href='" . url('admin/collection/kckra/monitoring/review/' . $val->id) . "' class='btn btn-warning btn-sm text-white'><i class='la la-pencil'></i></a>";
                } else if ($val->collection->status == 2) {
                    $receivedBy    = $val->collection->receivedBy ? $val->collection->receivedBy->username : "";
                    $updatedBy     = $val->collection->updatedBy ? $val->collection->updatedBy->username : "";
                    if (!$val->collection->edit_by) {
                        $action = '<a href="' . url('admin/collection/kckra/manage/update/' . $val->collection->id) . '" class="btn btn-warning btn-sm"><i class="la la-pencil"></i></a>';
                    } else {
                        if ($val->collection->edit_by == session('id')) {
                            $action = '<a href="' . url('admin/collection/kckra/manage/update/' . $val->collection->id) . '" data-toggle="tooltip" title="sedang anda edit" class="btn btn-info btn-sm"><i class="la la-pencil"></i></a>';
                        } else {
                            $action = '<span class="btn btn-warning btn-sm text-white" data-toggle="tooltip" style="opacity:0.6;" title="sedang diedit oleh ' . $val->collection->editBy->username . '" disabled><i class="la la-ban"></i></span>';
                        }
                    }
                }

                if ($val->collection->publisher) {
                    $publisher_name = $val->collection->publisher ? $val->collection->publisher->name : "";
                    $province_name = $val->collection->publisher->province ? $val->collection->publisher->province->name : '';
                    $city_name     = $val->collection->publisher->city ? $val->collection->publisher->city->name : '';
                } else {
                    $province_name = '';
                    $city_name     = '';
                    $publisher_name = '';
                }

                if (session('library_id') == '1') {
                    $mark = $val->collection->mark_national;
                } else {
                    $mark = $val->collection->mark_province;
                }

                $response['data'][] = [
                    $nomor,
                    $action,
                    '<span data-toggle="tooltip" title="' . $publisher_name . '">' . Str::limit($publisher_name, 20) . '</span>',
                    $periode,
                    $province_name,
                    $city_name,
                    '<span data-toggle="tooltip" title="' . $val->collection->title . '">' . Str::limit($val->collection->title, 20) . '</span>',
                    $val->collection->depositHead->shape,
                    $val->collection->album,
                    '<span data-toggle="tooltip" title="' . $val->collection->series . '">' . Str::limit($val->collection->series, 20) . '</span>',
                    '<span data-toggle="tooltip" title="' . $val->collection->edition . '">' . Str::limit($val->collection->edition, 20) . '</span>',
                    $val->collection->serial,
                    $val->collection->code,
                    $val->collection->deposit,
                    $mark,
                    $val->collection->publication_year,
                    $val->lib_location->name,
                    isset($availability[$val->availability]) ? $availability[$val->availability] : '-',
                    isset($arrConditions[$val->condition]) ? $arrConditions[$val->condition] : '-',
                    $val->collection->lock ? 'Ya' : 'Tidak',
                    $val->collection->manual ? 'Ya' : 'Tidak',
                    $val->created_at->format('d-m-Y, H:i'),
                    $val->received_at ? date('d-m-Y, H:i', strtotime($val->received_at)) : '-',
                    // $receipt,
                    ''
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
        $province_id = session('province_id');
        if ($request->has('province_id')) {
            if (!empty($request->province_id)) {
                $province_id = $request->province_id;
            }
        }

        $data = [
            'param'            => $request->param,
            'province_id'      => $province_id,
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
            'collection'       => $request->collection,
            'type_date'        => $request->type_date,
            'publisher_id'     => $request->publisher_id,
            'status'           => $request->status,
            'start_date'       => $request->start_date,
            'finish_date'      => $request->finish_date,
            'action_id'        => $request->user_id,
            'extension'        => $request->extension,
            'user_id'          => session('id'),
            'role_id'          => session('role_id'),
            'library_id'       => session('library_id'),
            'date'             => $request->date,
            'yearly'           => $request->yearly,
            'title'            => $request->title,
            'code'             => $request->code,
            'publication_year' => $request->publication_year,
            'file_type'        => $request->file_type,
            'expedition_id'    => $request->expedition_id,
            'lib_loc_id'       => $request->lib_loc_id,
            'condition'        => $request->condition,
            'availability'     => $request->availability,
            'view'             => 'admin'
        ];

        // dd($data);

        DownloadReportCollectionKckra::dispatch($data)->onQueue('report');

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
}
