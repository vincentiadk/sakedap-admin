<?php

namespace App\Http\Controllers\Admin;

use App\Models\Author;
use App\Models\Library;
use App\Models\Subject;
use App\Models\Category;
use App\Models\Location;
use App\Models\Collection;
use App\Models\Contributor;
use App\Models\DepositHead;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helper\GeneralHelper;
use App\Models\CollectionCopy;
use App\Models\CollectionMedia;
use App\Models\LibraryLocation;
use App\Models\CollectionSubject;
use App\Models\UserCertainAccess;
use App\Models\CollectionCategory;
use App\Http\Controllers\Controller;
use App\Models\CollectionContributor;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CollectionManageKckraController extends Controller
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
            'title'   => 'Pengelolaan KCKRA',
            'content' => 'admin.kckra.manage'
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

        // $data = Collection::where(function ($query) use ($kckra) {
        //     $query->where('parent_id', 0)
        //         ->whereIn('type', $kckra);
        // })->where(function ($query) use ($show_availability) {
        //     $query->whereHas('collectionCopy', function ($query) use ($show_availability) {
        //         $query->whereIn('availability', $show_availability);
        //     })->orWhereHas('collectionEditionCopy', function ($query) use ($show_availability) {
        //         $query->whereIn('availability', $show_availability);
        //     });
        // })->get();

        // dd($data);
        // exit;

        $totalData = Collection::where(function ($query) use ($kckra) {
            $query->where('parent_id', 0)
                ->whereIn('type', $kckra);
        })->where(function ($query) {
            if (session('library_id') != 1) {
                $query->whereHas('publisher', function ($query) {
                    $query->where('province_id', session('province_id'));
                });
            } else {
                $query->whereNotNull('mark_national');
            }
        })->where(function ($query) use ($show_availability) {
            $query->whereHas('collectionCopy', function ($query) use ($show_availability) {
                $query->whereIn('availability', $show_availability);
            })->orWhereHas('collectionEditionCopy', function ($query) use ($show_availability) {
                $query->whereIn('availability', $show_availability);
            });
        })
            ->count();

        if (empty($search)) {

            session()->put('filter.collection.kckra.manage.title', $request->title);
            session()->put('filter.collection.kckra.manage.publisher_id', $request->publisher_id);
            session()->put('filter.collection.kckra.manage.province_id', $request->province_id);
            session()->put('filter.collection.kckra.manage.city', $request->city);
            session()->put('filter.collection.kckra.manage.publication_year', $request->publication_year);
            session()->put('filter.collection.kckra.manage.code', $request->code);
            session()->put('filter.collection.kckra.manage.manage', $request->manage);
            session()->put('filter.collection.kckra.manage.validated', $request->validated);
            session()->put('filter.collection.kckra.manage.edited', $request->edited);
            session()->put('filter.collection.kckra.manage.param', $request->param);
            session()->put('filter.collection.kckra.manage.type', $request->type);

            if ($request->param == 'annual') {
                session()->put('filter.collection.kckra.manage.year_start', $request->year_start);
                session()->put('filter.collection.kckra.manage.year_end', $request->year_end);

                session()->forget('filter.collection.kckra.manage.month_start');
                session()->forget('filter.collection.kckra.manage.month_year_start');
                session()->forget('filter.collection.kckra.manage.month_end');
                session()->forget('filter.collection.kckra.manage.month_year_end');

                session()->forget('filter.collection.kckra.manage.day_start');
                session()->forget('filter.collection.kckra.manage.day_end');
            } else if ($request->param == 'monthly') {
                session()->put('filter.collection.kckra.manage.month_start', $request->month_start);
                session()->put('filter.collection.kckra.manage.month_year_start', $request->month_year_start);
                session()->put('filter.collection.kckra.manage.month_end', $request->month_end);
                session()->put('filter.collection.kckra.manage.month_year_end', $request->month_year_end);

                session()->forget('filter.collection.kckra.manage.year_start');
                session()->forget('filter.collection.kckra.manage.year_end');

                session()->forget('filter.collection.kckra.manage.day_start');
                session()->forget('filter.collection.kckra.manage.day_end');
            } else if ($request->param == 'daily') {
                session()->put('filter.collection.kckra.manage.day_start', $request->day_start);
                session()->put('filter.collection.kckra.manage.day_end', $request->day_end);

                session()->forget('filter.collection.kckra.manage.year_start');
                session()->forget('filter.collection.kckra.manage.year_end');

                session()->forget('filter.collection.kckra.manage.month_start');
                session()->forget('filter.collection.kckra.manage.month_year_start');
                session()->forget('filter.collection.kckra.manage.month_end');
                session()->forget('filter.collection.kckra.manage.month_year_end');
            } else {
                session()->forget('filter.collection.kckra.manage.year_start');
                session()->forget('filter.collection.kckra.manage.year_end');

                session()->forget('filter.collection.kckra.manage.month_start');
                session()->forget('filter.collection.kckra.manage.month_year_start');
                session()->forget('filter.collection.kckra.manage.month_end');
                session()->forget('filter.collection.kckra.manage.month_year_end');

                session()->forget('filter.collection.kckra.manage.day_start');
                session()->forget('filter.collection.kckra.manage.day_end');
            }

            $queryData = Collection::where(function ($query) use ($kckra) {
                $query->where('parent_id', 0)
                    ->whereIn('type', $kckra);
            })
                ->where(function ($query) {
                    if (session('library_id') != 1) {
                        $query->whereHas('publisher', function ($query) {
                            $query->where('province_id', session('province_id'));
                        });
                    } else {
                        $query->whereNotNull('mark_national');
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

                    if ($request->type) {
                        $query->where('type', $request->type);
                    }
                })
                ->where(function ($query) use ($show_availability) {
                    $query->whereHas('collectionCopy', function ($query) use ($show_availability) {
                        $query->whereIn('availability', $show_availability);
                    })->orWhereHas('collectionEditionCopy', function ($query) use ($show_availability) {
                        $query->whereIn('availability', $show_availability);
                    });
                })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();




            $totalFiltered = Collection::where(function ($query) use ($kckra) {
                $query->where('parent_id', 0)
                    ->whereIn('type', $kckra);
            })->where(function ($query) {
                if (session('library_id') != 1) {
                    $query->whereHas('publisher', function ($query) {
                        $query->where('province_id', session('province_id'));
                    });
                } else {
                    $query->whereNotNull('mark_national');
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

                    if ($request->type) {
                        $query->where('type', $request->type);
                    }
                })->where(function ($query) use ($show_availability) {
                    $query->whereHas('collectionCopy', function ($query) use ($show_availability) {
                        $query->whereIn('availability', $show_availability);
                    })->orWhereHas('collectionEditionCopy', function ($query) use ($show_availability) {
                        $query->whereIn('availability', $show_availability);
                    });
                })
                ->count();
        } else {
            $queryData = Collection::where(function ($query) use ($kckra) {
                $query->where('parent_id', 0)
                    ->whereIn('type', $kckra);
            })->where(function ($query) {
                if (session('library_id') != 1) {
                    $query->whereHas('publisher', function ($query) {
                        $query->where('province_id', session('province_id'));
                    });
                } else {
                    $query->whereNotNull('mark_national');
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

                    if ($request->type) {
                        $query->where('type', $request->type);
                    }
                })
                ->where(function ($query) use ($show_availability) {
                    $query->whereHas('collectionCopy', function ($query) use ($show_availability) {
                        $query->whereIn('availability', $show_availability);
                    })->orWhereHas('collectionEditionCopy', function ($query) use ($show_availability) {
                        $query->whereIn('availability', $show_availability);
                    });
                })
                ->where(function ($query) use ($search) {
                    $query->where('deposit', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('mark_national', 'like', "%{$search}%")
                        ->orWhere('mark_province', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%");
                })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Collection::where(function ($query) use ($kckra) {
                $query->where('parent_id', 0)
                    ->whereIn('type', $kckra);
            })
                ->where(function ($query) {
                    if (session('library_id') != 1) {
                        $query->whereHas('publisher', function ($query) {
                            $query->where('province_id', session('province_id'));
                        });
                    } else {
                        $query->whereNotNull('mark_national');
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

                    if ($request->type) {
                        $query->where('type', $request->type);
                    }
                })
                ->where(function ($query) use ($show_availability) {
                    $query->whereHas('collectionCopy', function ($query) use ($show_availability) {
                        $query->whereIn('availability', $show_availability);
                    })->orWhereHas('collectionEditionCopy', function ($query) use ($show_availability) {
                        $query->whereIn('availability', $show_availability);
                    });
                })
                ->where(function ($query) use ($search) {
                    $query->where('deposit', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%");
                })
                ->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                $receivedBy    = $val->receivedBy ? $val->receivedBy->username : "";
                $updatedBy     = $val->updatedBy ? $val->updatedBy->username : "";
                $type          = $types[$val->type];
                $access_delete = UserCertainAccess::where('role_id', session('role_id'))->where('access', 1)->count();

                if (!$val->edit_by) {
                    if ($access_delete > 0) {
                        $delete_button = '<button type="button" onclick="destroy(' . $val->id . ')" class="btn btn-danger btn-sm"><i class="la la-trash"></i></button>';
                    } else {
                        $delete_button = '<span class="btn btn-danger btn-sm text-white" data-toggle="tooltip" style="opacity:0.6;" title="Tidak Ada Akses" disabled><i class="la la-trash"></i></span>';
                    }

                    $edit = '<a href="' . url('admin/collection/kckra/manage/update/' . $val->id) . '" class="btn btn-warning btn-sm"><i class="la la-pencil"></i></a>';
                } else {
                    $delete_button = '<span class="btn btn-danger btn-sm text-white" data-toggle="tooltip" style="opacity:0.6;" title="Tidak bisa dihapus, sedang diedit oleh ' . $val->editBy->username . '" disabled><i class="la la-trash"></i></span>';

                    if ($val->edit_by == session('id')) {
                        $edit = '<a href="' . url('admin/collection/kckra/manage/update/' . $val->id) . '" data-toggle="tooltip" title="sedang anda edit" class="btn btn-info btn-sm"><i class="la la-pencil"></i></a>';
                    } else {
                        $edit = '<span class="btn btn-warning btn-sm text-white" data-toggle="tooltip" style="opacity:0.6;" title="sedang diedit oleh ' . $val->editBy->username . '" disabled><i class="la la-ban"></i></span>';
                    }
                }

                $response['data'][] = [
                    'edit' => $edit,
                    'nomor' => $nomor,
                    'id' => $val->id,
                    'is_serial' => $val->depositHead->is_serial,
                    'type' => $type,
                    'manage_by' => $val->manageBy ? '<i class="la la-check text-success"></i>' : '<i class="la la-times text-danger"></i>',
                    'lock' => $val->lock ? '<i class="la la-check text-success"></i>' : '<i class="la la-times text-danger"></i>',
                    'mark_province' => $val->mark_province,
                    'mark_national' => $val->mark_national,
                    'publisher' => '<span data-toggle="tooltip" title="' . $val->publisher->name . '">' . Str::limit($val->publisher->name, 20) . '</span>',
                    'title' => '<a href="' . url('admin/collection/kckra/manage/update/' . $val->id) . '" data-toggle="tooltip" title="' . $val->title . '">' . Str::limit($val->title, 20) . '</a>',
                    'code' => $val->code ? $val->code : '<i class="la la-times text-danger"></i>',
                    'updated_by' => $updatedBy,
                    'validated_by' => $receivedBy,
                    'received_at' => date('d-m-Y', strtotime($val->received_at)),
                    'delete' => $delete_button
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

    public function update(Request $request, $id)
    {
        $collection  = Collection::find($id);
        // $show_availability = ['0', '1', '2', '3', '4', '5', '6', '9', '10', '11'];
        // dd($collection);
        $access_lock = UserCertainAccess::where('role_id', session('role_id'))->where('access', 2)->count();
        $getType = DepositHead::find($collection->type);

        if ($request->cancel) {
            $collection->update(['edit_by' => null]);
            return redirect('admin/collection/manage/' . $collection->type);
        }


        // $hasMatchingCopies = $collection->collectionCopy()->where(function ($query) use ($show_availability) {
        //     $query->whereIn('availability', $show_availability);
        // })->exists();

        // if ($collection->status != 2) {
        //     abort(404);
        // }

        if ($collection->edit_by) {
            if ($collection->edit_by != session('id')) {
                return '
                    <script>
                        alert("Sedang di edit oleh ' . $collection->editBy->username . '")
                        window.location.href="' . url('admin/collection/manage/' . $collection->type) . '"
                    </script>
                ';
            }
        }

        $collection->update(['edit_by' => session('id')]);

        if ($request->has('_token')) {

            $collection_fields = config("collectionfield.$collection->type");
            if (!$request->has('cover')) {
                unset($collection_fields['cover']);
            }
            $getValidations = GeneralHelper::generateValidation('validation', $collection_fields);
            $getMessages = GeneralHelper::generateValidation('messages', $collection_fields);
            $validator = Validator::make($request->all(), $getValidations, $getMessages);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator);
            } else {
                // dd($request->all());
                if ($collection->type == 7) {
                    $physical_description = [
                        'total_page'  => $request->total_page,
                        'dimension'   => $request->dimension
                    ];
                } else {
                    $physical_description = [
                        'total_page'   => $request->total_page,
                        'dimension'   => $request->dimension
                    ];
                }

                $lock     = Collection::find($id)->lock;
                $old_data = $collection;
                $new_data = Collection::find($id);

                $log_old_category    = [];
                $log_old_contributor = [];
                $log_old_subject     = [];

                if ($old_data->collectionCategory->count() > 0) {
                    foreach ($old_data->collectionCategory as $cc) {
                        $log_old_category[] = $cc->category->name;
                    }
                }

                if ($old_data->collectionContributor->count() > 0) {
                    foreach ($old_data->collectionContributor as $cc) {
                        $log_old_contributor[] = [
                            'kontributor'      => $cc->contributor->name,
                            'nama'             => $cc->author->fullname,
                            'gelar'            => $cc->author->title,
                            'tanggal_lahir'    => $cc->author->year_of_birth,
                            'tanggal_kematian' => $cc->author->year_of_death
                        ];
                    }
                }

                if ($old_data->collectionSubject->count() > 0) {
                    foreach ($old_data->collectionSubject as $cs) {
                        $log_old_subject[] = $cs->subject->name;
                    }
                }

                $new_data->update([
                    'publisher_id'         => $request->publisher_id,
                    'title'                => $request->title,
                    'physical_description' => json_encode($physical_description),
                    'album'                => $request->album,
                    'slug'                 => Str::slug($request->title, '-'),
                    'publication_year'     => $request->publication_year,
                    'edition'              => $request->edition,
                    'series'               => $request->series,
                    'serial'               => $request->serial,
                    'volume'               => $request->volume,
                    'description'          => $request->description,
                    'lock'                 => $request->has('lock') ? 1 : 0,
                    'access'               => $request->access,
                    'edit_by'              => session('id'),
                    'updated_by'           => session('id'),
                    'received_at'          => $request->received_at,
                    'validated_at'         => $request->has('lock') ? date('Y-m-d H:i:s') : null,
                    'validated_by'         => $request->has('lock') ? session('id') : null
                ]);

                if ($new_data) {
                    $log_new_category    = [];
                    $log_new_contributor = [];
                    $log_new_subject     = [];
                    $log_lib_loc         = [];

                    CollectionCategory::where('collection_id', $id)->delete();
                    CollectionContributor::where('collection_id', $id)->delete();
                    CollectionSubject::where('collection_id', $id)->delete();

                    if ($request->has('collection_category')) {
                        foreach ($request->collection_category as $cc) {
                            $create = CollectionCategory::create([
                                'collection_id' => $id,
                                'category_id'   => $cc
                            ]);

                            $log_new_category[] = $create->category->name;
                        }
                    }

                    if ($request->has('contributor_contributor_id_field')) {
                        foreach ($request->contributor_contributor_id_field as $key => $ccid) {
                            $name  = $request->contributor_fullname_field[$key];
                            $title = $request->contributor_title_field[$key];

                            if (!empty($name) && !empty($title)) {
                                $authorCheck = Author::updateOrCreate([
                                    'fullname' => $name,
                                    'title'    => $title,
                                    'slug'     => Str::slug($name, '-')
                                ], [
                                    'year_of_birth' => $request->contributor_year_of_birth_field[$key],
                                    'year_of_death' => $request->contributor_year_of_death_field[$key]
                                ]);

                                $author = Author::where('fullname', $name)
                                    ->where('title', $title)
                                    ->where('slug', Str::slug($name, '-'))
                                    ->where('year_of_birth', $request->contributor_year_of_birth_field[$key])
                                    ->where('year_of_death', $request->contributor_year_of_death_field[$key])
                                    ->first();

                                $create = CollectionContributor::create([
                                    'collection_id'  => $id,
                                    'contributor_id' => $ccid,
                                    'author_id'      => $author->id
                                ]);

                                $log_new_contributor[] = [
                                    'kontributor'      => $create->contributor->name,
                                    'nama'             => $create->author->fullname,
                                    'gelar'            => $create->author->title,
                                    'tanggal_lahir'    => $create->author->year_of_birth,
                                    'tanggal_kematian' => $create->author->year_of_death
                                ];
                            }
                        }
                    }

                    if ($request->has('collection_subject')) {
                        foreach ($request->collection_subject as $cs) {
                            $subjectCheck = Subject::updateOrCreate([
                                'slug' => Str::slug($cs, '-')
                            ], [
                                'name' => $cs
                            ]);

                            $subject = Subject::where('name', $cs)
                                ->where('slug', Str::slug($cs, '-'))
                                ->first();

                            $create = CollectionSubject::create([
                                'collection_id' => $id,
                                'subject_id'    => $subject->id
                            ]);

                            $log_new_subject[] = $create->subject->name;
                        }
                    }

                    if ($request->has('location_lib_loc_id_field')) {
                        foreach ($request->location_lib_loc_id_field as $key => $llid) {
                            $copy  = $request->location_copy_field[$key];
                            $condition = $request->location_condition_field[$key];
                            $id_loc = isset($request->location_id_field[$key]) ? $request->location_id_field[$key] : null;

                            if (!empty($copy) && !empty($condition)) {
                                $logged = CollectionCopy::updateOrCreate(
                                    [
                                        'id' => $id_loc,
                                        'collection_id' => $new_data->id,
                                    ],
                                    [
                                        'lib_loc_id' => $llid,
                                        'copy' => $copy,
                                        'condition' => $condition,
                                    ]
                                );

                                $log_lib_loc[] = [
                                    'location_id'      => $logged->lib_loc_id,
                                    'location_name'    => $logged->lib_location->name,
                                    'library'          => $logged->lib_location->library->name,
                                    'copy'             => $logged->copy,
                                    'conditon'         => $logged->conditon
                                ];
                            }
                        }
                    }

                    if ($request->has('edition_edition_field')) {
                        $arr_id_ed = [];
                        $arr_id_loc_ed = [];
                        $arr_id_med_ed = [];
                        foreach ($request->edition_edition_field as $key => $eef) {
                            $id_ed = isset($request->edition_id_field[$key]) ? $request->edition_id_field[$key] : null;
                            $edition = Collection::updateOrCreate(
                                [
                                    'parent_id'    => $new_data->id,
                                    'id'           => $id_ed
                                ],
                                [
                                    'publisher_id' => $request->publisher_id,
                                    'edition'      => $eef,
                                    'deposit'      => GeneralHelper::depositCollection(),
                                    'manual'       => 1,
                                    'date'         => $request->edition_date_field[$key],
                                    'status'       => 1,
                                    'received_by'  => session('id'),
                                    'received_at'  => date('Y-m-d H:i:s'),
                                    'edit_by'      => session('id'),
                                    'created_by'   => session('id'),
                                    'updated_by'   => session('id')
                                ]
                            );
                            $arr_id_ed[] = $edition->id;

                            if ($request->has('edition_location_field')) {
                                if (isset($request->edition_location_field['location_lib_loc_id_field'])) {
                                    foreach ($request->edition_location_field['location_lib_loc_id_field'][$key] as $k => $llid) {
                                        $id_loc_ed  = isset($request->edition_location_field['location_id_field'][$key][$k]) ? $request->edition_location_field['location_id_field'][$key][$k] : null;
                                        $copy  = $request->edition_location_field['location_copy_field'][$key][$k];
                                        $condition = $request->edition_location_field['location_condition_field'][$key][$k];
                                        if (!empty($copy) && !empty($condition)) {
                                            $edition_location = CollectionCopy::updateOrCreate(
                                                [
                                                    'id' => $id_loc_ed,
                                                    'collection_id' => $edition->id,
                                                ],
                                                [
                                                    'lib_loc_id' => $llid,
                                                    'copy' => $copy,
                                                    'condition' => $condition,
                                                ]
                                            );
                                            $arr_id_loc_ed[] = $edition_location->id;
                                        }
                                    }
                                }
                                //delete removed collection_location
                                CollectionCopy::where('collection_id', $edition->id)->whereNotIn('id', $arr_id_loc_ed)->delete();
                            } else {
                                //delete all collection_location from related collection
                                CollectionCopy::where('collection_id', $edition->id)->delete();
                            }

                            $cover_edition = $request->edition_cover_field[$key];
                            if (!empty($cover_edition) && $cover_edition != null && $cover_edition != 'null') {
                                $name_cover = $getType->code;
                                $ext_cover = pathinfo($cover_edition, PATHINFO_EXTENSION);
                                $path_tmp_cover    = 'public/collection/serial/temporary/' . $cover_edition;
                                $old_cover         = 'public/collection/' . $name_cover . '/edition/cover/' . $edition->id . '/' . $cover_edition;
                                if (!Storage::disk($this->location->location)->exists($old_cover)) {
                                    $path_cover  = 'public/collection/' . $name_cover . '/edition/cover/' . $edition->id . '/' . Str::random(40) . '.' . $ext_cover;
                                    Storage::disk($this->location->location)->move($path_tmp_cover, $path_cover);
                                } else {
                                    $path_cover = $old_cover;
                                }
                                try {
                                    $edition_cover = CollectionMedia::updateOrCreate(
                                        [
                                            'collection_id' => $edition->id,
                                            'type'          => 1
                                        ],
                                        [
                                            'link'          => $path_cover,
                                            'size'          => File::size(Storage::disk($this->location->location)->path($path_cover)),
                                            'extension'     => pathinfo(Storage::disk($this->location->location)->path($path_cover), PATHINFO_EXTENSION),
                                            'mimes'         => File::mimeType(Storage::disk($this->location->location)->path($path_cover)),
                                            'hash'          => md5_file(Storage::disk($this->location->location)->path($path_cover)),
                                            'method'        => 4,
                                            'created_at'    => date('Y-m-d H:i:s'),
                                            'updated_at'    => date('Y-m-d H:i:s'),
                                            'location_id'   => $this->location->id
                                        ]
                                    );
                                    $arr_id_med_ed[] = $edition_cover->id;
                                    //delete removed collection_media
                                    CollectionMedia::where('collection_id', $edition->id)->whereNotIn('id', $arr_id_med_ed)->delete();
                                } catch (\Exception $e) {
                                    dd($e);
                                }
                            } else {
                                CollectionMedia::where('collection_id', $edition->id)->where('type', 1)->delete();
                            }
                        }
                        //delete removed collection
                        Collection::where('parent_id', $new_data->id)->whereNotIn('id', $arr_id_ed)->delete();
                    } else {
                        //delete all collection with parent_id
                        Collection::where('parent_id', $new_data->id)->delete();
                    }

                    if ($request->cover) {
                        $collectionMedia = $collection->collectionMedia->where('type', 1)->first();
                        if ($collectionMedia) {
                            Storage::disk('local')->delete($collectionMedia->link);
                            CollectionMedia::where('id', $collectionMedia->id)->forceDelete();
                        }

                        $name_cover = $getType->code;
                        $link_collection_cover = Storage::disk($this->location->location)->put('public/collection/' . $name_cover . '/cover/' . $new_data->id, $request->file('cover'));
                        CollectionMedia::create([
                            'collection_id' => $id,
                            'link'          => $link_collection_cover,
                            'size'          => File::size($request->file('cover')),
                            'extension'     => $request->file('cover')->getClientOriginalExtension(),
                            'mimes'         => File::mimeType($request->file('cover')),
                            'hash'          => md5_file($request->file('cover')),
                            'type'          => 1,
                            'method'        => 4,
                            'location_id'   => $this->location->id
                        ]);
                    }

                    activity('collections')
                        ->performedOn($new_data)
                        ->causedBy(session('id'))
                        ->withProperties([
                            'data_lama' => [
                                'penerbit'         => $old_data->publisher->name,
                                'judul'            => $old_data->title,
                                'deskripsi_fisik'  => $old_data->physical_description,
                                'album'            => $old_data->album,
                                'tahun_terbit'     => $old_data->publication_year,
                                'edisi'            => $old_data->edition,
                                'seri'             => $old_data->series,
                                'serial'           => $old_data->serial,
                                'volume'           => $old_data->volume,
                                'deskripsi'        => $old_data->description,
                                'kunci'            => $old_data->lock == 1 ? 'Dikunci' : 'Tidak Dikunci',
                                'akses'            => $old_data->access(),
                                'diedit_oleh'      => $old_data->editBy->username,
                                'diupdate_oleh'    => $old_data->updatedBy->username,
                                'divalidasi_oleh'  => $old_data->validatedBy ? $old_data->validatedBy->username : '',
                                'tanggal_validasi' => date('Y-m-d H:i:s', strtotime($old_data->validation_at)),
                                'kategori'         => $log_old_category,
                                'kontributor'      => $log_old_contributor,
                                'subjek'           => $log_old_subject
                            ],
                            'data_baru' => [
                                'penerbit'         => $new_data->publisher->name,
                                'judul'            => $new_data->title,
                                'deskripsi_fisik'  => $new_data->physical_description,
                                'album'            => $new_data->album,
                                'tahun_terbit'     => $new_data->publication_year,
                                'edisi'            => $new_data->edition,
                                'seri'             => $new_data->series,
                                'serial'           => $new_data->serial,
                                'volume'           => $new_data->volume,
                                'deskripsi'        => $new_data->description,
                                'kunci'            => $new_data->lock == 1 ? 'Dikunci' : 'Tidak Dikunci',
                                'akses'            => $new_data->access(),
                                'diedit_oleh'      => $new_data->editBy->username,
                                'diupdate_oleh'    => $new_data->updatedBy->username,
                                'divalidasi_oleh'  => $new_data->validatedBy ? $new_data->validatedBy->username : '',
                                'tanggal_validasi' => date('Y-m-d H:i:s', strtotime($new_data->validated_at)),
                                'kategori'         => $log_new_category,
                                'kontributor'      => $log_new_contributor,
                                'lib_loc'          => $log_lib_loc,
                                'subjek'           => $log_new_subject
                            ]
                        ])
                        ->log('Mengubah data koleksi (' . $collection->title . ')');

                    $collection->update([
                        'manage_by' => $collection->manage_by ? $collection->manage_by : session('id'),
                        'edit_by'   => null
                    ]);

                    return redirect('admin/collection/kckra/manage/all')->with(['success' => 'Koleksi berhasil di update!']);
                } else {
                    return redirect()->back()->with(['failed' => 'Koleksi gagal di update!']);
                }
            }
        } else {
            // dd();

            $data = [
                'title'      => 'Edit ' . $collection->depositHead->shape,
                'content'    => 'admin.kckra.update_manage'
            ];

            $edition = Collection::where('parent_id', $id)
                ->where(function ($query) {
                    if (session('library_id') != 1) {
                        $query->whereHas('publisher', function ($query) {
                            $query->where('province_id', session('province_id'));
                        });
                    } else {
                        $query->whereNotNull('mark_national');
                    }
                })
                ->with(['collectionMedia', 'collectionCopy', 'depositHead'])
                ->get();

            // dd($edition);

            $collection_fields = config('collectionfield.' . $collection->deposit_head_id);
            $library_id = session('library_id');
            $arrConditions = [
                '1' => 'Sangat Baik',
                '2' => 'Baik',
                '3' => 'Cukup',
                '4' => 'Rusak'
            ];

            // dd(session());

            $data = array_merge($data, [
                'kategori_deposit' => $getType['category'],
                'category'    => Category::where('type', $collection->type)->get(),
                'availability' => [
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
                ],
                'collection'  => $collection,
                'shape'  => $collection->depositHead->shape,
                'type'  => $collection->deposit_head_id,
                'locked_url'  => url('admin/collection/lockable/' . $collection->id),
                'access_lock' => $access_lock,
                'contributor' => Contributor::where('show', 1)->orderBy('name', 'asc')->get(),
                'lib_loc' => LibraryLocation::where('library_id', $library_id)->orderBy('name', 'asc')->get(),
                'edition'     => $edition,
                'collection_id' => $id,
                'fields'     => $collection_fields,
                'col_conditions' => $arrConditions,
                'library' => Library::find($library_id),
            ]);

            // dd($data);

            return view('admin.layout.index', ['data' => $data]);
        }
    }

    function createEditions(Request $request, $parent_id)
    {
        try {
            // dd($request->all());
            if ($request->has('edition_field') && $request->has('publication_date_field')) {
                $parent_collection = Collection::find($parent_id);
                $createCover = $createMedia = $cover_image = $media_file = [];
                $publication_date_field = $request->publication_date_field;
                if (!empty($publication_date_field)) {
                    $publication_date = explode(' - ', $publication_date_field);
                    $start_publication_date = $publication_date[0];
                    $end_publication_date = $publication_date[1];
                }

                $edition = Collection::create([
                    'parent_id'    => $parent_id,
                    'publisher_id' => $parent_collection->publisher_id,
                    'type'         => $parent_collection->type,
                    'deposit_head_id' => $parent_collection->type,
                    'edition'      => $request->edition_field,
                    'deposit'      => GeneralHelper::depositCollection(),
                    'copyright'    => 'Copyrights (c) ' . date('Y') . ' ' . $parent_collection->publisher->name,
                    'manual'       => 1,
                    'date'         => $start_publication_date,
                    'start_publication_date' => $start_publication_date,
                    'end_publication_date' => $end_publication_date,
                    'status'       => 1,
                    'received_by'  => session('id'),
                    'received_at'  => date('Y-m-d H:i:s'),
                    'edit_by'      => session('id'),
                    'created_by'   => session('id'),
                    'updated_by'   => session('id')
                ]);

                if ($edition) {
                    if ($request->has('copy_lib_loc_id') && $request->has('copy_total')) {
                        for ($i = 0; $i < $request->copy_total; $i++) {
                            $collection_id  = $edition->id;
                            $copy_received_date  = $request->copy_received_date;
                            $availability_id  = $request->copy_availability;
                            $lib_loc_id  = $request->copy_lib_loc_id;
                            $condition_id  = $request->copy_condition;


                            $logged = CollectionCopy::create([
                                'received_at' => $copy_received_date,
                                'collection_id' => $collection_id,
                                'lib_loc_id' => $lib_loc_id,
                                'condition' => $condition_id,
                                'availability' => $availability_id,
                                'created_by' => session('id'),
                                'edit_by' => session('id'),
                            ]);
                        }
                    }

                    $name_cover = $parent_collection->depositHead->code;
                    if ($request->has('cover_field')) {
                        $cover = $request->file('cover_field');
                        $name_cover = $parent_collection->depositHead->code;
                        $path_cover    = Storage::disk($this->location->location)->put('public/collection/' . $name_cover . '/edition/cover/' . $edition->id, $cover);
                        $cover_image = '<a class="btn btn-outline-secondary" href="' . asset(Storage::disk($this->location->location)->url($path_cover)) . '" data-lightbox="' . $cover->getClientOriginalName() . '" data-title="' . $cover->getClientOriginalName() . '"><img src="' . asset(Storage::disk($this->location->location)->url($path_cover)) . '" style="max-height:30px; max-width:30px;"></a>';

                        try {
                            $createCover = CollectionMedia::create([
                                'collection_id' => $edition->id,
                                'link'          => $path_cover,
                                'size'          => File::size(Storage::disk($this->location->location)->path($path_cover)),
                                'extension'     => pathinfo(Storage::disk($this->location->location)->path($path_cover), PATHINFO_EXTENSION),
                                'mimes'         => File::mimeType(Storage::disk($this->location->location)->path($path_cover)),
                                'hash'          => md5_file(Storage::disk($this->location->location)->path($path_cover)),
                                'type'          => 1,
                                'method'        => 4,
                                'created_at'    => date('Y-m-d H:i:s'),
                                'updated_at'    => date('Y-m-d H:i:s'),
                                'location_id'   => $this->location->id
                            ]);
                        } catch (\Exception $e) {
                            dd($e);
                        }
                    }

                    if ($request->has('media_field')) {
                        $media = $request->file('media_field');
                        $path_media    = Storage::disk($this->location->location)->put('public/collection/' . $name_cover . '/edition/media/' . $edition->id, $media);
                        $media_file = '<a class="btn btn-outline-secondary" href="' . asset(Storage::disk($this->location->location)->url($path_media)) . '" data-lightbox="' . $media->getClientOriginalName() . '" data-title="' . $media->getClientOriginalName() . '"><img src="' . asset(Storage::disk($this->location->location)->url($path_media)) . '" style="max-height:30px; max-width:30px;"></a>';

                        try {
                            $createMedia = CollectionMedia::create([
                                'collection_id' => $edition->id,
                                'link'          => $path_media,
                                'size'          => File::size(Storage::disk($this->location->location)->path($path_media)),
                                'extension'     => pathinfo(Storage::disk($this->location->location)->path($path_media), PATHINFO_EXTENSION),
                                'mimes'         => File::mimeType(Storage::disk($this->location->location)->path($path_media)),
                                'hash'          => md5_file(Storage::disk($this->location->location)->path($path_media)),
                                'type'          => 2,
                                'method'        => 4,
                                'created_at'    => date('Y-m-d H:i:s'),
                                'updated_at'    => date('Y-m-d H:i:s'),
                                'location_id'   => $this->location->id
                            ]);
                        } catch (\Exception $e) {
                            dd($e);
                        }
                    }
                }


                $response = [
                    'status'  => 200,
                    'message' => 'Success Menambahkan Edisi Koleksi'
                ];
            } else {
                $response = [
                    'status' => 422,
                    'message' => 'Mohon Lengkapi Data'
                ];
            }
        } catch (\Exception $e) {
            // dd($e);
            $response = [
                'status'  => 500,
                'message' => 'Gagal Ditambahkan ' . $e->getMessage()
            ];
        }

        return response()->json($response);
    }

    function datatableEditions(Request $request, $collection_id)
    {
        $column = [
            'id',
            'edition',
            'total_copy',
            'cover_image',
            'karantina',
            'action'
        ];

        // dd($request->input);
        $start  = $request->start;
        $length = $request->length;
        $order  = $column[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');
        $param = ($request->has('param')) ? $request->input('param') : '';
        $show_availability = ['0', '1', '2', '3', '4', '5', '6', '9', '10', '11'];

        $total_data = Collection::where('parent_id', $collection_id)->where(function ($query) use ($param, $show_availability) {
            if ($param == 'index') {
                $query->where(function ($query) use ($show_availability) {
                    $query->whereHas('collectionCopy', function ($query) use ($show_availability) {
                        $query->whereIn('availability', $show_availability);
                    });
                });
            }
        })->count();

        $query_data = Collection::where('parent_id', $collection_id)->where(function ($query) use ($search, $param, $show_availability) {
            if ($search) {
                $query->where('edition', 'like', "%$search%");
            }
            if ($param == 'index') {
                $query->where(function ($query) use ($show_availability) {
                    $query->whereHas('collectionCopy', function ($query) use ($show_availability) {
                        $query->whereIn('availability', $show_availability);
                    });
                });
            }
        })
            ->offset($start)
            ->limit($length)
            ->orderBy($order, $dir)
            ->get();
        // dd($query_data);

        $total_filtered = Collection::where('parent_id', $collection_id)->where(function ($query) use ($search, $param, $show_availability) {
            if ($search) {
                $query->where('edition', 'like', "%$search%");
            }
            if ($param == 'index') {
                $query->where(function ($query) use ($show_availability) {
                    $query->whereHas('collectionCopy', function ($query) use ($show_availability) {
                        $query->whereIn('availability', $show_availability);
                    });
                });
            }
        })->count();

        $response['data'] = [];
        if ($query_data <> FALSE) {
            $nomor = $start + 1;
            foreach ($query_data as $val) {
                $cover = $val->collectionMedia->where('type', 1)->first();
                if ($cover) {
                    $path_cover = $cover->link;
                    $cover_image = '<a class="btn btn-outline-secondary" href="' . asset(Storage::disk($this->location->location)->url($path_cover)) . '" data-lightbox="' . $cover->hash . '" data-title="' . $cover->hash . '"><img src="' . asset(Storage::disk($this->location->location)->url($path_cover)) . '" style="max-height:30px; max-width:30px;"></a>';
                } else {
                    $cover_image = null;
                }

                if ($param == 'index') {
                    $response['data'][] = [
                        'edition' => $val->edition,
                        'publication_date' => $val->start_publication_date . ' - ' . $val->end_publication_date,
                        'total_copy' => $val->totalCopy(),
                        'cover' => $cover_image,
                        'mark_national' => $val->mark_national,
                        'mark_province' => $val->mark_province,
                        'id' => $val->id,
                        'karantina' => (!empty($val->deleted_at)) ? '<span class="badge bg-danger">Dikarantina</span>' : '<span class="badge bg-primary">Published</span>',
                    ];
                } else {
                    $response['data'][] = [
                        $nomor,
                        $val->edition,
                        $val->start_publication_date . ' - ' . $val->end_publication_date,
                        $val->totalCopy(),
                        $cover_image,
                        (!empty($val->deleted_at)) ? '<span class="badge bg-danger">Dikarantina</span>' : '<span class="badge bg-primary">Published</span>',
                        ($val->created_by == session('id')) ? '
                        <button type="button" onclick="showEditions(' . $val->id . ')" class="btn btn-warning btn-sm"><i class="la la-pencil"></i> Edit</button>
                        <button type="button" onclick="destroyEditions(' . $val->id . ')" class="btn btn-danger btn-sm"><i class="la la-trash"></i> Karantina</button>
                        ' : ''
                    ];
                }
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

        $total_data_valid = Collection::where('parent_id', $collection_id)->where('id', '>', 0)->where('deleted_at', null)->count();

        $response['recordsValid'] = $total_data_valid;

        return response()->json($response);
    }

    public function showEditions(Request $request, $id)
    {
        $getData = Collection::find($id);
        $data = $getData->toArray();
        $data['cover'] = !empty($getData->collectionMedia->where('type', 1)->first()) ? $getData->collectionMedia->where('type', 1)->first()->link : null;
        $data['file'] = !empty($getData->collectionMedia->where('type', 2)->first()) ? $getData->collectionMedia->where('type', 2)->first()->link : null;
        return response()->json($data);
    }

    public function updateEditions(Request $request, $id)
    {
        $arrValidations = [
            'edition_field' => 'required',
            'publication_date_field' => 'required',
        ];

        $arrMessages = [
            'edition_field.required' => 'Mohon mengisi Kondisi Koleksi.',
            'publication_date_field.required' => 'Mohon mengisi Ketersediaan.',
        ];

        // dd($request->all());

        $publication_date_field = $request->publication_date_field;
        if (!empty($publication_date_field)) {
            $publication_date = explode(' - ', $publication_date_field);
            $start_publication_date = $publication_date[0];
            $end_publication_date = $publication_date[1];
        }

        $arrUpdates = [
            'edition'  => $request->edition_field,
            'date'         => $start_publication_date,
            'start_publication_date'    => $start_publication_date,
            'end_publication_date'    => $end_publication_date,
            'edit_by' => session('id'),
        ];

        $validation = Validator::make($request->all(), $arrValidations, $arrMessages);

        if ($validation->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validation->errors()
            ];
        } else {

            $new_data = Collection::find($id);
            $new_data->update($arrUpdates);

            if ($new_data) {
                //delete cover if cover removed
                if ($request->has('temp_cover') && empty($request->temp_cover)) {
                    $this->deleteCollectionMedia(1, $new_data->id);
                }
                //delete file if file removed
                if ($request->has('temp_file') && empty($request->temp_file)) {
                    $this->deleteCollectionMedia(2, $new_data->id);
                }

                $parent_collection = Collection::find($new_data->parent_id);
                $name_cover = $parent_collection->depositHead->code;
                if ($request->has('cover_field')) {
                    if (!empty($request->cover_field)) {
                        $cover = $request->file('cover_field');
                        $name_cover = $parent_collection->depositHead->code;
                        $path_cover    = Storage::disk($this->location->location)->put('public/collection/' . $name_cover . '/edition/cover/' . $new_data->id, $cover);
                        $cover_image = '<a class="btn btn-outline-secondary" href="' . asset(Storage::disk($this->location->location)->url($path_cover)) . '" data-lightbox="' . $cover->getClientOriginalName() . '" data-title="' . $cover->getClientOriginalName() . '"><img src="' . asset(Storage::disk($this->location->location)->url($path_cover)) . '" style="max-height:30px; max-width:30px;"></a>';

                        try {
                            //delete previous cover
                            $this->deleteCollectionMedia(1, $new_data->id);
                            //insert new cover
                            $createCover = CollectionMedia::create([
                                'collection_id' => $new_data->id,
                                'link'          => $path_cover,
                                'size'          => File::size(Storage::disk($this->location->location)->path($path_cover)),
                                'extension'     => pathinfo(Storage::disk($this->location->location)->path($path_cover), PATHINFO_EXTENSION),
                                'mimes'         => File::mimeType(Storage::disk($this->location->location)->path($path_cover)),
                                'hash'          => md5_file(Storage::disk($this->location->location)->path($path_cover)),
                                'type'          => 1,
                                'method'        => 4,
                                'created_at'    => date('Y-m-d H:i:s'),
                                'updated_at'    => date('Y-m-d H:i:s'),
                                'location_id'   => $this->location->id
                            ]);
                        } catch (\Exception $e) {
                            dd($e);
                        }
                    }
                }

                if ($request->has('media_field')) {
                    if (!empty($request->media_field)) {
                        $media = $request->file('media_field');
                        $path_media    = Storage::disk($this->location->location)->put('public/collection/' . $name_cover . '/edition/media/' . $new_data->id, $media);
                        $media_file = '<a class="btn btn-outline-secondary" href="' . asset(Storage::disk($this->location->location)->url($path_media)) . '" data-lightbox="' . $media->getClientOriginalName() . '" data-title="' . $media->getClientOriginalName() . '"><img src="' . asset(Storage::disk($this->location->location)->url($path_media)) . '" style="max-height:30px; max-width:30px;"></a>';

                        try {
                            //delete previous file
                            $this->deleteCollectionMedia(2, $new_data->id);
                            //insert new file
                            $createMedia = CollectionMedia::create([
                                'collection_id' => $new_data->id,
                                'link'          => $path_media,
                                'size'          => File::size(Storage::disk($this->location->location)->path($path_media)),
                                'extension'     => pathinfo(Storage::disk($this->location->location)->path($path_media), PATHINFO_EXTENSION),
                                'mimes'         => File::mimeType(Storage::disk($this->location->location)->path($path_media)),
                                'hash'          => md5_file(Storage::disk($this->location->location)->path($path_media)),
                                'type'          => 2,
                                'method'        => 4,
                                'created_at'    => date('Y-m-d H:i:s'),
                                'updated_at'    => date('Y-m-d H:i:s'),
                                'location_id'   => $this->location->id
                            ]);
                        } catch (\Exception $e) {
                            dd($e);
                        }
                    }
                }

                $response = [
                    'status'  => 200,
                    'message' => 'Data telah diproses.'
                ];
            } else {
                $response = [
                    'status'  => 500,
                    'message' => 'Data gagal diproses.'
                ];
            }
        }

        return response()->json($response);
    }

    function deleteCollectionMedia($type, $collection_id)
    {
        CollectionMedia::where('type', $type)->where('collection_id', $collection_id)->delete();
    }

    function karantinaEditions(Request $request, $id)
    {
        $collection = Collection::find($id);

        if (!$collection) {
            return response()->json(['message' => 'Eksemplar Koleksi not found.'], 404);
        }

        $collection->delete();

        return response()->json(['message' => 'Eksemplar Koleksi Sudah Dikarantina.']);
    }

    function createCopies(Request $request, $parent_id)
    {
        if ($request->ajax()) {
            // dd($request->all());
            try {
                if ($request->has('data')) {
                    $created_datas = [];
                    foreach ($request->data as $key => $value) {
                        $collection_id  = $value['edition_id'];
                        $availability_id  = $value['availability_id'];
                        $library_id  = $value['library_id'];
                        $lib_loc_id  = $value['lib_loc_id'];
                        $condition_id  = $value['condition_id'];
                        $received_date  = $value['received_date'];

                        if (empty($collection_id)) {
                            $collection_id = $parent_id;
                        }

                        $logged = CollectionCopy::create([
                            'collection_id' => $collection_id,
                            'received_date' => $received_date,
                            'lib_loc_id' => $lib_loc_id,
                            'condition' => $condition_id,
                            'availability' => $availability_id,
                            'created_by' => session('id'),
                            'edit_by' => session('id'),
                        ]);

                        $created_datas[$logged->id] = $value;
                        $created_datas[$logged->id]['id'] = $logged->id;
                    }

                    $response = [
                        'status'  => 200,
                        'data' => $created_datas
                    ];
                }
            } catch (\Exception $e) {
                // dd($e);
                $response = [
                    'status'  => 500,
                    'message' => 'Gagal Ditambahkan ' . $e->getMessage()
                ];
            }

            return response()->json($response);
        }
    }

    function datatableCopies(Request $request, $collection_id, $type = null)
    {
        $param = ($request->has('param')) ? $request->input('param') : '';
        if ($param == 'index') {
            $column = $request->input('columns');
            $order  = $column[$request->input('order.0.column')]['data'];
        } else {
            $column = [
                'id',
                'code',
                'received_at',
                'condition',
                'price',
                'availability',
                'library',
                'location',
            ];
            $order  = $column[$request->input('order.0.column')];
        }

        $arrConditions = [
            '1' => 'Sangat Baik',
            '2' => 'Baik',
            '3' => 'Cukup',
            '4' => 'Rusak'
        ];

        $arrAvailability = [
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

        // dd($request->input);
        $start  = $request->start;
        $length = $request->length;

        // dd($order);
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');
        $show_availability = ['0', '1', '2', '3', '4', '5', '6', '9', '10', '11'];

        if ($type !== 'all') {

            $total_data = CollectionCopy::where('collection_id', $collection_id)->where(function ($query) use ($param, $show_availability) {
                if ($param == 'index') {
                    $query->whereIn('availability', $show_availability);
                }
            })->where('id', '>', 0)->count();

            $query_data = CollectionCopy::where('collection_id', $collection_id)->where(function ($query) use ($param, $show_availability) {
                if ($param == 'index') {
                    $query->whereIn('availability', $show_availability);
                }
            })->where(function ($query) use ($search) {
                if ($search) {
                    $query->whereHas('lib_location', function ($subquery) use ($search) {
                        $subquery->where('name', 'like', "%$search%");
                    })->orWhereHas('collection', function ($subquery) use ($search) {
                        $subquery->where('edition', 'like', "%$search%");
                    })->orWhere('code', 'like', "%$search%");
                }
            })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            // dd($query_data);

            $total_filtered = CollectionCopy::where('collection_id', $collection_id)->where(function ($query) use ($param, $show_availability) {
                if ($param == 'index') {
                    $query->whereIn('availability', $show_availability);
                }
            })->where(function ($query) use ($search) {
                if ($search) {
                    $query->whereHas('lib_location', function ($subquery) use ($search) {
                        $subquery->where('name', 'like', "%$search%");
                    })->orWhereHas('collection', function ($subquery) use ($search) {
                        $subquery->where('edition', 'like', "%$search%");
                    })->orWhere('code', 'like', "%$search%");
                }
            })
                ->count();

            $total_data_valid = CollectionCopy::where('id', '>', 0)->where(function ($query) use ($param, $show_availability) {
                if ($param == 'index') {
                    $query->whereIn('availability', $show_availability);
                }
            })->where('collection_id', $collection_id)->where('deleted_at', null)->count();
        } else {
            $total_data = CollectionCopy::whereHas('collection', function ($subquery) use ($collection_id) {
                $subquery->where('parent_id', $collection_id);
            })->count();

            $query_data = CollectionCopy::whereHas('collection', function ($subquery) use ($collection_id) {
                $subquery->where('parent_id', $collection_id);
            })->where(function ($query) use ($search) {
                if ($search) {
                    $query->whereHas('lib_location', function ($subquery) use ($search) {
                        $subquery->where('name', 'like', "%$search%");
                    })->orWhereHas('collection', function ($subquery) use ($search) {
                        $subquery->where('edition', 'like', "%$search%");
                    })->orWhere('code', 'like', "%$search%");
                }
            })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();

            $total_filtered = CollectionCopy::whereHas('collection', function ($subquery) use ($collection_id) {
                $subquery->where('parent_id', $collection_id);
            })->where(function ($query) use ($search) {
                if ($search) {
                    $query->whereHas('lib_location', function ($subquery) use ($search) {
                        $subquery->where('name', 'like', "%$search%");
                    })->orWhereHas('collection', function ($subquery) use ($search) {
                        $subquery->where('edition', 'like', "%$search%");
                    })->orWhere('code', 'like', "%$search%");
                }
            })
                ->count();

            $total_data_valid = CollectionCopy::whereHas('collection', function ($subquery) use ($collection_id) {
                $subquery->where('parent_id', $collection_id);
            })->where('deleted_at', null)->count();
        }

        $response['data'] = [];
        if ($query_data <> FALSE) {
            $nomor = $start + 1;
            foreach ($query_data as $val) {
                if ($param == 'index') {
                    $response['data'][] = [
                        'code' => $val->code,
                        'received_at' => $val->received_at ?? null,
                        'condition' => isset($arrConditions[$val->condition]) ? $arrConditions[$val->condition] : '-',
                        'price' => $val->collection->price,
                        'availability' => isset($arrAvailability[$val->availability]) ? $arrAvailability[$val->availability] : '-',
                        'library' => isset($val->lib_location) ? $val->lib_location->library->name : '-',
                        'location' => isset($val->lib_location) ? $val->lib_location->name : '-',
                    ];
                } else {
                    $button_action = '';
                    if ($val->created_by == session('id')) {
                        $button_action = '
                            <button type="button" onclick="showCopies(' . $val->id . ')" class="btn btn-warning btn-sm"><i class="la la-pencil"></i> Edit</button>
                            <button type="button" onclick="destroyCopies(' . $val->id . ')" class="btn btn-danger btn-sm"><i class="la la-trash"></i> Karantina</button>
                        ';
                    } else if ($val->received_by == session('id')) {
                        $button_action = '
                            <button type="button" onclick="showCopies(' . $val->id . ')" class="btn btn-warning btn-sm"><i class="la la-pencil"></i> Edit</button>
                        ';
                    }
                    if ($val->collection->depositHead->is_serial) {
                        $response['data'][] = [
                            $nomor,
                            $val->code,
                            $val->received_at ?? null,
                            isset($arrConditions[$val->condition]) ? $arrConditions[$val->condition] : '-',
                            $val->collection->edition,
                            !empty($val->collection->price) ? $val->collection->price : $val->collection->parent()->price,
                            isset($arrAvailability[$val->availability]) ? $arrAvailability[$val->availability] : '-',
                            isset($val->lib_location) ? $val->lib_location->library->name : '-',
                            isset($val->lib_location) ? $val->lib_location->name : '-',
                            (!empty($val->deleted_at)) ? '<span class="badge bg-danger">Dikarantina</span>' : '<span class="badge bg-primary">Published</span>',
                            $button_action
                        ];
                    } else {
                        $response['data'][] = [
                            $nomor,
                            $val->code,
                            $val->created_at,
                            isset($arrConditions[$val->condition]) ? $arrConditions[$val->condition] : '-',
                            $val->collection->price,
                            isset($arrAvailability[$val->availability]) ? $arrAvailability[$val->availability] : '-',
                            isset($val->lib_location) ? $val->lib_location->library->name : '-',
                            isset($val->lib_location) ? $val->lib_location->name : '-',
                            (!empty($val->deleted_at)) ? '<span class="badge bg-danger">Dikarantina</span>' : '<span class="badge bg-primary">Published</span>',
                            $button_action
                        ];
                    }
                }
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



        $response['recordsValid'] = $total_data_valid;

        return response()->json($response);
    }

    public function showCopies(Request $request, $id)
    {
        $data = CollectionCopy::find($id);
        return response()->json($data);
    }

    public function updateCopies(Request $request, $id)
    {
        $arrValidations = [
            'condition' => 'required',
            'availability' => 'required',
            'lib_loc_id' => 'required',
        ];

        $arrMessages = [
            'condition.required' => 'Mohon mengisi Kondisi Koleksi.',
            'availability.required' => 'Mohon mengisi Ketersediaan.',
            'lib_loc_id.required' => 'Mohon mengisi Lokasi Koleksi.',
        ];

        $arrUpdates = [
            'condition'  => $request->condition,
            'availability'    => $request->availability,
            'lib_loc_id'    => $request->lib_loc_id,
            'edit_by' => session('id'),
        ];

        if ($request->has('collection_id')) {
            $arrValidations['collection_id'] = 'required';
            $arrMessages['collection_id.required'] = 'Mohon isi Edisi';
            $arrUpdates['collection_id'] = $request->collection_id;
        }

        $validation = Validator::make($request->all(), $arrValidations, $arrMessages);

        if ($validation->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validation->errors()
            ];
        } else {
            $new_data = CollectionCopy::find($id);
            $new_data->update($arrUpdates);

            if ($new_data) {
                $response = [
                    'status'  => 200,
                    'message' => 'Data telah diproses.'
                ];
            } else {
                $response = [
                    'status'  => 500,
                    'message' => 'Data gagal diproses.'
                ];
            }
        }

        return response()->json($response);
    }

    function karantinaCopies(Request $request, $id)
    {
        $collectionCopy = CollectionCopy::find($id);

        if (!$collectionCopy) {
            return response()->json(['message' => 'Eksemplar Koleksi not found.'], 404);
        }

        $collectionCopy->delete();

        return response()->json(['message' => 'Eksemplar Koleksi Sudah Dikarantina.']);
    }
}
