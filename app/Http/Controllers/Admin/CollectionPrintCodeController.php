<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use App\Models\Library;
use App\Models\Category;
use App\Models\Location;
use App\Models\Publisher;
use App\Models\Collection;
use App\Models\Contributor;
use App\Models\DepositHead;
use App\Models\CopyDelivery;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CollectionCopy;
use App\Models\LibraryLocation;
use App\Models\UserPrintHistory;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Milon\Barcode\Facades\DNS1DFacade;
use Milon\Barcode\Facades\DNS2DFacade;

class CollectionPrintCodeController extends Controller
{
    protected $location;

    public function __construct()
    {
        $this->location = Location::where('active', 1)->first();
    }

    public function index($type = null)
    {
        $get_deposit_head = DepositHead::get();
        $library_id = session('library_id');
        $deposit_head = $kckra = [];
        foreach ($get_deposit_head as $key => $value) {
            $deposit_head[$value['category']][] = $value;
            if (in_array($value['category'], ['KC', 'KRA'])) {
                $kckra[$value['id']] = $value['shape'];
            }
        }
        $data = [
            'title'   => 'Cetak Label Koleksi',
            'content' => 'admin.kckra.print_label'
        ];

        $data = array_merge($data, [
            'types'    =>  DepositHead::whereIn('category', ['KC', 'KRA'])->pluck('shape', 'id'),
            'category'    => Category::where('type', $type)->get(),
            'contributor' => Contributor::where('show', 1)->orderBy('name', 'asc')->get(),
            'lib_loc' => LibraryLocation::where('library_id', $library_id)->orderBy('name', 'asc')->get(),
            'deposit_head' => $deposit_head,
            'type' => $type,
        ]);

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $types = DepositHead::whereIn('category', ['KC', 'KRA'])->pluck('shape', 'id')->toArray();
        // dd($types);
        $kckra = array_keys($types);
        $whereLike = [
            'edit',
            'id',
            'type',
            'manage_by',
            'lock',
            'deposit',
            'publisher_id',
            'title',
            'code',
            'updated_by',
            'validated_by',
            'received_at',
            'delete'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $show_availability = ['0', '1', '2', '3', '4', '5', '6', '9', '10', '11'];

        $totalData = CollectionCopy::where(function ($query) use ($show_availability) {
            $query->whereIn('availability', $show_availability);
        })->whereHas('collection', function ($query) use ($kckra) {
            $query->whereIn('type', $kckra)->where(function ($query) {
                if (session('library_id') != 1) {
                    $query->whereHas('publisher', function ($query) {
                        $query->where('province_id', session('province_id'));
                    });
                } else {
                    $query->whereNotNull('mark_national');
                }
            });
        })->count();


        if (empty($search)) {

            session()->put('filter.collection.kckra.print.title', $request->title);
            session()->put('filter.collection.kckra.print.publisher_id', $request->publisher_id);
            session()->put('filter.collection.kckra.print.province_id', $request->province_id);
            session()->put('filter.collection.kckra.print.city', $request->city);
            session()->put('filter.collection.kckra.print.publication_year', $request->publication_year);
            session()->put('filter.collection.kckra.print.code', $request->code);
            session()->put('filter.collection.kckra.print.manage', $request->manage);
            session()->put('filter.collection.kckra.print.validated', $request->validated);
            session()->put('filter.collection.kckra.print.edited', $request->edited);
            session()->put('filter.collection.kckra.print.param', $request->param);
            session()->put('filter.collection.kckra.print.type', $request->type);

            if ($request->param == 'annual') {
                session()->put('filter.collection.kckra.print.year_start', $request->year_start);
                session()->put('filter.collection.kckra.print.year_end', $request->year_end);

                session()->forget('filter.collection.kckra.print.month_start');
                session()->forget('filter.collection.kckra.print.month_year_start');
                session()->forget('filter.collection.kckra.print.month_end');
                session()->forget('filter.collection.kckra.print.month_year_end');

                session()->forget('filter.collection.kckra.print.day_start');
                session()->forget('filter.collection.kckra.print.day_end');
            } else if ($request->param == 'monthly') {
                session()->put('filter.collection.kckra.print.month_start', $request->month_start);
                session()->put('filter.collection.kckra.print.month_year_start', $request->month_year_start);
                session()->put('filter.collection.kckra.print.month_end', $request->month_end);
                session()->put('filter.collection.kckra.print.month_year_end', $request->month_year_end);

                session()->forget('filter.collection.kckra.print.year_start');
                session()->forget('filter.collection.kckra.print.year_end');

                session()->forget('filter.collection.kckra.print.day_start');
                session()->forget('filter.collection.kckra.print.day_end');
            } else if ($request->param == 'daily') {
                session()->put('filter.collection.kckra.print.day_start', $request->day_start);
                session()->put('filter.collection.kckra.print.day_end', $request->day_end);

                session()->forget('filter.collection.kckra.print.year_start');
                session()->forget('filter.collection.kckra.print.year_end');

                session()->forget('filter.collection.kckra.print.month_start');
                session()->forget('filter.collection.kckra.print.month_year_start');
                session()->forget('filter.collection.kckra.print.month_end');
                session()->forget('filter.collection.kckra.print.month_year_end');
            } else {
                session()->forget('filter.collection.kckra.print.year_start');
                session()->forget('filter.collection.kckra.print.year_end');

                session()->forget('filter.collection.kckra.print.month_start');
                session()->forget('filter.collection.kckra.print.month_year_start');
                session()->forget('filter.collection.kckra.print.month_end');
                session()->forget('filter.collection.kckra.print.month_year_end');

                session()->forget('filter.collection.kckra.print.day_start');
                session()->forget('filter.collection.kckra.print.day_end');
            }

            $queryData =
                CollectionCopy::where(function ($query) use ($show_availability) {
                    $query->whereIn('availability', $show_availability);
                })->whereHas(
                    'collection',
                    function ($query) use ($kckra, $request) {
                        $query->whereIn('type', $kckra)->where(function ($query) {
                            if (session('library_id') != 1) {
                                $query->whereHas('publisher', function ($query) {
                                    $query->where('province_id', session('province_id'));
                                });
                            } else {
                                $query->whereNotNull('mark_national');
                            }
                        })->where(function ($query) use ($request) {
                            if ($request->title) {
                                $query->where('title', 'like', "%$request->title%");
                            }

                            if ($request->publisher_id) {
                                $query->where('publisher_id', $request->publisher_id);
                            }

                            if ($request->province_id) {
                                $query->whereHas('publisher', function ($query) use ($request) {
                                    $query->where('province_id', $request->province_id);
                                });
                            }

                            if ($request->city) {
                                $query->whereHas('publisher', function ($query) use ($request) {
                                    $query->whereHas('city', function ($query) use ($request) {
                                        $query->where('name', 'like', "%$request->city%");
                                    });
                                });
                            }

                            if ($request->publication_year) {
                                $query->where('publication_year', $request->publication_year);
                            }

                            if ($request->code) {
                                $query->where('code', 'like', "%$request->code%");
                            }

                            if ($request->manage) {
                                if ($request->manage == 1) {
                                    $query->whereNotNull('manage_by');
                                } else {
                                    $query->whereNull('manage_by');
                                }
                            }

                            if ($request->validated) {
                                if ($request->validated == 1) {
                                    $query->whereNotNull('validated_by')
                                        ->whereNotNull('validated_at')
                                        ->where('lock', true);
                                } else {
                                    $query->whereNull('validated_by')
                                        ->whereNull('validated_at')
                                        ->where('lock', false);
                                }
                            }

                            if ($request->edited) {
                                if ($request->edited == 1) {
                                    $query->whereNotNull('edit_by');
                                } else {
                                    $query->whereNull('edit_by');
                                }
                            }

                            if ($request->param) {
                                if ($request->param == 'annual') {
                                    $query->whereYear('received_at', '>=', $request->year_start)
                                        ->whereYear('received_at', '<=', $request->year_end);
                                } else if ($request->param == 'monthly') {
                                    $query->whereMonth('received_at', '>=', $request->month_start)
                                        ->whereYear('received_at', '>=', $request->month_year_start)
                                        ->whereMonth('received_at', '<=', $request->month_end)
                                        ->whereYear('received_at', '<=', $request->month_year_start);
                                } else if ($request->param == 'daily') {
                                    $query->whereDate('received_at', '>=', $request->day_start)
                                        ->whereDate('received_at', '<=', $request->day_end);
                                }
                            }

                            if ($request->type) {
                                $query->where('type', $request->type);
                            }
                        });
                    }
                )->offset($start)->limit($length)->orderBy($order, $dir)->get();

            $totalFiltered =
                CollectionCopy::where(function ($query) use ($show_availability) {
                    $query->whereIn('availability', $show_availability);
                })->whereHas(
                    'collection',
                    function ($query) use ($kckra, $request) {
                        $query->whereIn('type', $kckra)->where(function ($query) {
                            if (session('library_id') != 1) {
                                $query->whereHas('publisher', function ($query) {
                                    $query->where('province_id', session('province_id'));
                                });
                            } else {
                                $query->whereNotNull('mark_national');
                            }
                        })->where(function ($query) use ($request) {
                            if ($request->title) {
                                $query->where('title', 'like', "%$request->title%");
                            }

                            if ($request->publisher_id) {
                                $query->where('publisher_id', $request->publisher_id);
                            }

                            if ($request->province_id) {
                                $query->whereHas('publisher', function ($query) use ($request) {
                                    $query->where('province_id', $request->province_id);
                                });
                            }

                            if ($request->city) {
                                $query->whereHas('publisher', function ($query) use ($request) {
                                    $query->whereHas('city', function ($query) use ($request) {
                                        $query->where('name', 'like', "%$request->city%");
                                    });
                                });
                            }

                            if ($request->publication_year) {
                                $query->where('publication_year', $request->publication_year);
                            }

                            if ($request->code) {
                                $query->where('code', 'like', "%$request->code%");
                            }

                            if ($request->manage) {
                                if ($request->manage == 1) {
                                    $query->whereNotNull('manage_by');
                                } else {
                                    $query->whereNull('manage_by');
                                }
                            }

                            if ($request->validated) {
                                if ($request->validated == 1) {
                                    $query->whereNotNull('validated_by')
                                        ->whereNotNull('validated_at')
                                        ->where('lock', true);
                                } else {
                                    $query->whereNull('validated_by')
                                        ->whereNull('validated_at')
                                        ->where('lock', false);
                                }
                            }

                            if ($request->edited) {
                                if ($request->edited == 1) {
                                    $query->whereNotNull('edit_by');
                                } else {
                                    $query->whereNull('edit_by');
                                }
                            }

                            if ($request->param) {
                                if ($request->param == 'annual') {
                                    $query->whereYear('received_at', '>=', $request->year_start)
                                        ->whereYear('received_at', '<=', $request->year_end);
                                } else if ($request->param == 'monthly') {
                                    $query->whereMonth('received_at', '>=', $request->month_start)
                                        ->whereYear('received_at', '>=', $request->month_year_start)
                                        ->whereMonth('received_at', '<=', $request->month_end)
                                        ->whereYear('received_at', '<=', $request->month_year_start);
                                } else if ($request->param == 'daily') {
                                    $query->whereDate('received_at', '>=', $request->day_start)
                                        ->whereDate('received_at', '<=', $request->day_end);
                                }
                            }

                            if ($request->type) {
                                $query->where('type', $request->type);
                            }
                        });
                    }
                )->count();
        } else {
            $queryData =
                CollectionCopy::where(function ($query) use ($show_availability) {
                    $query->whereIn('availability', $show_availability);
                })->whereHas('collection', function ($query) use ($kckra, $request) {
                    $query->whereIn('type', $kckra)->where(function ($query) {
                        if (session('library_id') != 1) {
                            $query->whereHas('publisher', function ($query) {
                                $query->where('province_id', session('province_id'));
                            });
                        } else {
                            $query->whereNotNull('mark_national');
                        }
                    })->where(function ($query) use ($request) {
                        if ($request->title) {
                            $query->where('title', 'like', "%$request->title%");
                        }

                        if ($request->publisher_id) {
                            $query->where('publisher_id', $request->publisher_id);
                        }

                        if ($request->province_id) {
                            $query->whereHas('publisher', function ($query) use ($request) {
                                $query->where('province_id', $request->province_id);
                            });
                        }

                        if ($request->city) {
                            $query->whereHas('publisher', function ($query) use ($request) {
                                $query->whereHas('city', function ($query) use ($request) {
                                    $query->where('name', 'like', "%$request->city%");
                                });
                            });
                        }

                        if ($request->publication_year) {
                            $query->where('publication_year', $request->publication_year);
                        }

                        if ($request->code) {
                            $query->where('code', 'like', "%$request->code%");
                        }

                        if ($request->manage) {
                            if ($request->manage == 1) {
                                $query->whereNotNull('manage_by');
                            } else {
                                $query->whereNull('manage_by');
                            }
                        }

                        if ($request->validated) {
                            if ($request->validated == 1) {
                                $query->whereNotNull('validated_by')
                                    ->whereNotNull('validated_at')
                                    ->where('lock', true);
                            } else {
                                $query->whereNull('validated_by')
                                    ->whereNull('validated_at')
                                    ->where('lock', false);
                            }
                        }

                        if ($request->edited) {
                            if ($request->edited == 1) {
                                $query->whereNotNull('edit_by');
                            } else {
                                $query->whereNull('edit_by');
                            }
                        }

                        if ($request->param) {
                            if ($request->param == 'annual') {
                                $query->whereYear('received_at', '>=', $request->year_start)
                                    ->whereYear('received_at', '<=', $request->year_end);
                            } else if ($request->param == 'monthly') {
                                $query->whereMonth('received_at', '>=', $request->month_start)
                                    ->whereYear('received_at', '>=', $request->month_year_start)
                                    ->whereMonth('received_at', '<=', $request->month_end)
                                    ->whereYear('received_at', '<=', $request->month_year_start);
                            } else if ($request->param == 'daily') {
                                $query->whereDate('received_at', '>=', $request->day_start)
                                    ->whereDate('received_at', '<=', $request->day_end);
                            }
                        }

                        if ($request->type) {
                            $query->where('type', $request->type);
                        }
                    });
                })->where(function ($query) use ($search) {
                    $query->whereHas('collection', function ($query) use ($search) {
                        $query->where('deposit', 'like', "%{$search}%")
                            ->orWhere('mark_national', 'like', "%{$search}%")
                            ->orWhere('mark_province', 'like', "%{$search}%")
                            ->orWhere('title', 'like', "%{$search}%")
                            ->orWhere('edition', 'like', "%{$search}%");
                    })->orWhere('code', 'like', "%{$search}%");
                })->offset($start)->limit($length)->orderBy($order, $dir)->get();


            $totalFiltered =
                CollectionCopy::where(function ($query) use ($show_availability) {
                    $query->whereIn('availability', $show_availability);
                })->whereHas('collection', function ($query) use ($kckra, $request) {
                    $query->whereIn('type', $kckra)->where(function ($query) {
                        if (session('library_id') != 1) {
                            $query->whereHas('publisher', function ($query) {
                                $query->where('province_id', session('province_id'));
                            });
                        } else {
                            $query->whereNotNull('mark_national');
                        }
                    })->where(function ($query) use ($request) {
                        if ($request->title) {
                            $query->where('title', 'like', "%$request->title%");
                        }

                        if ($request->publisher_id) {
                            $query->where('publisher_id', $request->publisher_id);
                        }

                        if ($request->province_id) {
                            $query->whereHas('publisher', function ($query) use ($request) {
                                $query->where('province_id', $request->province_id);
                            });
                        }

                        if ($request->city) {
                            $query->whereHas('publisher', function ($query) use ($request) {
                                $query->whereHas('city', function ($query) use ($request) {
                                    $query->where('name', 'like', "%$request->city%");
                                });
                            });
                        }

                        if ($request->publication_year) {
                            $query->where('publication_year', $request->publication_year);
                        }

                        if ($request->code) {
                            $query->where('code', 'like', "%$request->code%");
                        }

                        if ($request->manage) {
                            if ($request->manage == 1) {
                                $query->whereNotNull('manage_by');
                            } else {
                                $query->whereNull('manage_by');
                            }
                        }

                        if ($request->validated) {
                            if ($request->validated == 1) {
                                $query->whereNotNull('validated_by')
                                    ->whereNotNull('validated_at')
                                    ->where('lock', true);
                            } else {
                                $query->whereNull('validated_by')
                                    ->whereNull('validated_at')
                                    ->where('lock', false);
                            }
                        }

                        if ($request->edited) {
                            if ($request->edited == 1) {
                                $query->whereNotNull('edit_by');
                            } else {
                                $query->whereNull('edit_by');
                            }
                        }

                        if ($request->param) {
                            if ($request->param == 'annual') {
                                $query->whereYear('received_at', '>=', $request->year_start)
                                    ->whereYear('received_at', '<=', $request->year_end);
                            } else if ($request->param == 'monthly') {
                                $query->whereMonth('received_at', '>=', $request->month_start)
                                    ->whereYear('received_at', '>=', $request->month_year_start)
                                    ->whereMonth('received_at', '<=', $request->month_end)
                                    ->whereYear('received_at', '<=', $request->month_year_start);
                            } else if ($request->param == 'daily') {
                                $query->whereDate('received_at', '>=', $request->day_start)
                                    ->whereDate('received_at', '<=', $request->day_end);
                            }
                        }

                        if ($request->type) {
                            $query->where('type', $request->type);
                        }
                    });
                })->where(function ($query) use ($search) {
                    $query->whereHas('collection', function ($query) use ($search) {
                        $query->where('deposit', 'like', "%{$search}%")
                            ->orWhere('mark_national', 'like', "%{$search}%")
                            ->orWhere('mark_province', 'like', "%{$search}%")
                            ->orWhere('title', 'like', "%{$search}%")
                            ->orWhere('edition', 'like', "%{$search}%");
                    })->orWhere('code', 'like', "%{$search}%");
                })->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                $receivedBy    = $val->receivedBy ? $val->receivedBy->username : "";
                $updatedBy     = $val->collection->updatedBy ? $val->collection->updatedBy->username : "";
                $type          = $types[$val->collection->type];

                if ($val->edition) {
                    $title = $val->edition->collection->title . ' - ' . $val->edition->edition;
                    $isbn = $val->edition->collection->code;
                } else {
                    $title = $val->collection->title;
                    $isbn = $val->collection->code;
                }
                $response['data'][] = [
                    $val->id,
                    $nomor,
                    $type,
                    $val->code,
                    $val->collection->mark_national,
                    $val->collection->mark_province,
                    Str::limit($val->collection->publisher->name, 20),
                    $title,
                    $isbn,
                    $val->condition_text(),
                    $val->availability_text(),
                    $val->lib_location ? $val->lib_location->library->name : "",
                    $val->lib_location ? $val->lib_location->name : "",
                    $updatedBy,
                    $receivedBy,
                    date('d-m-Y', strtotime($val->received_at))
                ];
                $nomor++;
            }
        }

        // dd($getData->toSql());

        // dd($response);

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

    public function getPublisher(Request $request)
    {
        $publisher = Publisher::where('id', $request->publisher_id)->with('province', 'city')->first()->toArray();
        return response()->json($publisher);
    }

    public function checkCodeDeposit(Request $request)
    {
        $library_id = session('library_id');
        $check = Collection::whereHas('collectionCopy', function ($subquery) {
            $subquery->whereNull('delivery_from_id');
        })
            ->whereHas('collectionCopy.lib_location', function ($subquery) use ($library_id) {
                $subquery->where('library_id', $library_id);
            })->doesntHave('collectionCopy.copy_delivery');

        if ($library_id == '1') {
            $check = $check->where('mark_national', $request->code);
        } else {
            $check = $check->where('mark_province', $request->code);
        }
        $check = $check->first();

        if ($check) {
            $data = $check->toArray();
            if ($check->parent_id != 0) {
                $data['parent'] = $check->parent()->toArray();
                $data['title'] = $check->parent()->title . ' - ' . $check->edition;
                $data['publication_year'] = date('Y', strtotime($check->start_publication_date)) . ' - ' . date('Y', strtotime($check->end_publication_date));
            } else {
                $data['parent'] = [];
            }
            $data['total_copy'] = $check->totalCopy($library_id);
            $data['publisher_name'] = $check->publisher->name;
            $data['type_collection'] = $check->depositHead->shape . ' (' . $check->depositHead->code . ')';
            $response = [
                'status'  => 200,
                'message' => 'Data Koleksi Ditemukan!',
                'data'    => $data
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Tidak Ditemukan Eksemplar yang Tersedia',
                'data'    => null
            ];
        }

        return response()->json($response);
    }

    public function create(Request $request)
    {
        try {
            if ($request->ajax()) {
                $datas = json_decode($request->input('data'));
                // dd($datas, $request->input('data'));
                $library_id = session('library_id');
                if (sizeof($datas) > 0) {
                    foreach ($datas as $key => $value) {
                        if (isset($groupedData[$value->collection_id])) {
                            $groupedData[$value->collection_id] = $groupedData[$value->collection_id] + 1;
                        } else {
                            $groupedData[$value->collection_id] = 1;
                        }
                    }

                    // dd($groupedData);

                    foreach ($groupedData as $key => $value) {
                        $check = CollectionCopy::select('id')->where('collection_id', $key)
                            ->whereNull('delivery_from_id')
                            ->whereHas('lib_location', function ($subquery) use ($library_id) {
                                $subquery->where('library_id', $library_id);
                            })->doesntHave('copy_delivery');

                        $check = $check->take($value)->get();

                        foreach ($check as $copy) {
                            CopyDelivery::create([
                                'delivery_internal_date' => date('Y-m-d'),
                                'accepted_date' => null,
                                'system_id' => null,
                                'collection_copy_id' => $copy->id,
                                'user_delivery_id' => session('id'),
                                'created_by' => session('id'),
                                'updated_by' => session('id'),
                            ]);
                        }

                        unset($check);
                    }

                    $response = [
                        'status' => 200,
                        'message' => 'Berhasil Menambahkan Data Pengiriman Internal!'
                    ];
                } else {
                    $response = [
                        'status'  => 500,
                        'message' => 'Data Kosong, Mohon tambahkan data sebelum submit!',
                    ];
                }

                return response()->json($response);
            }
        } catch (Exception $e) {
            activity('collections')
                ->causedBy(session('id'))
                ->withProperties([
                    'error' => $e->getMessage(),
                ])
                ->log('Gagal Create Manual');
            return response()->json([
                'status'  => 500,
                'message' =>  $e->getMessage(),
            ]);
        }
    }

    public function printBarcode(Request $request)
    {
        $library_id = session('library_id');
        $ids = $request->input('ids');
        $arr_ids = explode(',', $ids);
        if (sizeof($arr_ids) > 0) {
            $data['data'] = CollectionCopy::whereIn('id', $arr_ids)->get();
            foreach ($data['data'] as $key => $value) {
                $barcodeData = $value->code;
                $barcodeImage = DNS1DFacade::getBarcodePNG($barcodeData, 'C39', 1, 110, array(1, 1, 1), true);
                $data['data'][$key]['barcode_image'] = $barcodeImage;
                $data['data'][$key]['mark_number'] = ($library_id == '1') ? $value->collection->mark_national : $value->collection->mark_province;
                if ($value->parent_id != 0) {
                    $data[$key]['title'] = $value->parent()->title . ' - ' . $value->edition;
                    $data[$key]['publication_year'] = date('Y', strtotime($value->start_publication_date)) . ' - ' . date('Y', strtotime($value->end_publication_date));
                }
            }

            $this->insertHistory($arr_ids);

            $data['library'] = Library::find($library_id);

            // Create PDF using Barryvdh\DomPDF
            $pdf = Pdf::loadView('admin.kckra.print_pdf_barcode', $data);

            // Set the paper size to 10cm x 5cm (283.464566929pt x 141.732283465pt)
            $pdf->setPaper([0, 0, 283.464566929, 141.732283465]);
            return $pdf->download('barcodes.pdf');
        } else {
            return redirect('admin/collection/kckra/print')->with(['failed' => 'Pastikan Keranjang Tidak Kosong!']);
        }
    }

    public function printQrcode(Request $request)
    {
        $library_id = session('library_id');
        $ids = $request->input('ids');
        $arr_ids = explode(',', $ids);
        if (sizeof($arr_ids) > 0) {
            $data['data'] = CollectionCopy::whereIn('id', $arr_ids)->get();
            foreach ($data['data'] as $key => $value) {
                $barcodeData = $value->code;
                $barcodeImage = DNS2DFacade::getBarcodePNG($barcodeData, 'QRCODE', 6.5, 6.5, array(1, 1, 1), true);
                $data['data'][$key]['barcode_image'] = $barcodeImage;
                $data['data'][$key]['mark_number'] = ($library_id == '1') ? $value->collection->mark_national : $value->collection->mark_province;
                if ($value->parent_id != 0) {
                    $data[$key]['title'] = $value->parent()->title . ' - ' . $value->edition;
                    $data[$key]['publication_year'] = date('Y', strtotime($value->start_publication_date)) . ' - ' . date('Y', strtotime($value->end_publication_date));
                }
            }

            $this->insertHistory($arr_ids);

            $data['library'] = Library::find($library_id);
            // Create PDF using Barryvdh\DomPDF
            $pdf = Pdf::loadView('admin.kckra.print_pdf_qrcode', $data);

            // Set the paper size to 10cm x 5cm (283.464566929pt x 141.732283465pt)
            $pdf->setPaper([0, 0, 283.464566929, 141.732283465]);
            return $pdf->download('qrcodes.pdf');
        } else {
            return redirect('admin/collection/kckra/print')->with(['failed' => 'Pastikan Keranjang Tidak Kosong!']);
        }
    }

    function insertHistory($data)
    {
        $user = session('id');
        $insert = [];
        foreach ($data as $key => $value) {
            $insert[$key]['collection_id'] = $value;
            $insert[$key]['created_by'] = $user;
            $insert[$key]['updated_by'] = $user;
            $insert[$key]['created_at'] = Carbon::now();
            $insert[$key]['updated_at'] = Carbon::now();
        }

        UserPrintHistory::insert($insert);
    }

    function karantina(Request $request, $id)
    {
        $delivery = CopyDelivery::find($id);

        if (!$delivery) {
            return response()->json(['message' => 'Pengiriman Eksemplar Koleksi not found.'], 404);
        }

        $delivery->delete();

        return response()->json(['message' => 'Pengiriman Eksemplar Koleksi Sudah Dikarantina.']);
    }
}
