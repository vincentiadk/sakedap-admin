<?php

namespace App\Http\Controllers\Admin;

use App\Models\Author;
use App\Models\Subject;
use App\Models\Category;
use App\Models\Location;
use App\Models\Collection;
use App\Models\Contributor;
use App\Models\DepositHead;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
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
use App\Http\Controllers\Admin\DashboardController;

class CollectionManageController extends Controller
{
    protected $location;

    public function __construct()
    {
        $this->location = Location::where('active', 1)->first();
    }

    public function index($type = null)
    {
        if ($type == 1) {
            $data = [
                'title'   => 'Pengelolaan Buku',
                'content' => 'admin.book.manage'
            ];
        } else if ($type == 2) {
            $data = [
                'title'   => 'Pengelolaan Partitur',
                'content' => 'admin.partitur.manage'
            ];
        } else if ($type == 3) {
            $data = [
                'title'   => 'Pengelolaan Peta',
                'content' => 'admin.map.manage'
            ];
        } else if ($type == 4) {
            $data = [
                'title'   => 'Pengelolaan Serial',
                'content' => 'admin.serial.manage'
            ];
        } else if ($type == 5) {
            $data = [
                'title'   => 'Pengelolaan Audio',
                'content' => 'admin.audio.manage'
            ];
        } else if ($type == 6) {
            $data = [
                'title'   => 'Pengelolaan Film',
                'content' => 'admin.film.manage'
            ];
        } else {
            $data = [
                'title'          => 'Pengelolaan Koleksi',
                'total_book'     => DashboardController::statistic('collection_type_status', [1, 2]),
                'total_partitur' => DashboardController::statistic('collection_type_status', [2, 2]),
                'total_map'      => DashboardController::statistic('collection_type_status', [3, 2]),
                'total_serial'   => DashboardController::statistic('collection_type_status', [4, 2]),
                'total_audio'    => DashboardController::statistic('collection_type_status', [5, 2]),
                'total_film'     => DashboardController::statistic('collection_type_status', [6, 2]),
                'content'        => 'admin.collection.manage'
            ];

            $get_deposit_head = DepositHead::get();
            $library_id = session('library_id');
            $deposit_head = [];
            foreach ($get_deposit_head as $key => $value) {
                $deposit_head[$value['category']][] = $value;
            }
            $data = array_merge($data, [
                'category'    => Category::where('type', $type)->get(),
                'contributor' => Contributor::where('show', 1)->orderBy('name', 'asc')->get(),
                'lib_loc' => LibraryLocation::where('library_id', $library_id)->orderBy('name', 'asc')->get(),
                'deposit_head' => $deposit_head,
            ]);
        }

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request, $type)
    {
        $whereLike = [
            'edit',
            'id',
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

        $totalData = Collection::where(function ($query) use ($type) {
            $query->where('parent_id', 0)
                ->where('type', $type)
                ->where('status', 2)
                ->whereNotNull('received_at')
                ->whereNotNull('received_by');
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

            session()->put('filter.collection.manage.' . $type . '.title', $request->title);
            session()->put('filter.collection.manage.' . $type . '.publisher_id', $request->publisher_id);
            session()->put('filter.collection.manage.' . $type . '.province_id', $request->province_id);
            session()->put('filter.collection.manage.' . $type . '.city', $request->city);
            session()->put('filter.collection.manage.' . $type . '.publication_year', $request->publication_year);
            session()->put('filter.collection.manage.' . $type . '.code', $request->code);
            session()->put('filter.collection.manage.' . $type . '.manage', $request->manage);
            session()->put('filter.collection.manage.' . $type . '.validated', $request->validated);
            session()->put('filter.collection.manage.' . $type . '.edited', $request->edited);
            session()->put('filter.collection.manage.' . $type . '.param', $request->param);

            if ($request->param == 'annual') {
                session()->put('filter.collection.manage.' . $type . '.year_start', $request->year_start);
                session()->put('filter.collection.manage.' . $type . '.year_end', $request->year_end);

                session()->forget('filter.collection.manage.' . $type . '.month_start');
                session()->forget('filter.collection.manage.' . $type . '.month_year_start');
                session()->forget('filter.collection.manage.' . $type . '.month_end');
                session()->forget('filter.collection.manage.' . $type . '.month_year_end');

                session()->forget('filter.collection.manage.' . $type . '.day_start');
                session()->forget('filter.collection.manage.' . $type . '.day_end');
            } else if ($request->param == 'monthly') {
                session()->put('filter.collection.manage.' . $type . '.month_start', $request->month_start);
                session()->put('filter.collection.manage.' . $type . '.month_year_start', $request->month_year_start);
                session()->put('filter.collection.manage.' . $type . '.month_end', $request->month_end);
                session()->put('filter.collection.manage.' . $type . '.month_year_end', $request->month_year_end);

                session()->forget('filter.collection.manage.' . $type . '.year_start');
                session()->forget('filter.collection.manage.' . $type . '.year_end');

                session()->forget('filter.collection.manage.' . $type . '.day_start');
                session()->forget('filter.collection.manage.' . $type . '.day_end');
            } else if ($request->param == 'daily') {
                session()->put('filter.collection.manage.' . $type . '.day_start', $request->day_start);
                session()->put('filter.collection.manage.' . $type . '.day_end', $request->day_end);

                session()->forget('filter.collection.manage.' . $type . '.year_start');
                session()->forget('filter.collection.manage.' . $type . '.year_end');

                session()->forget('filter.collection.manage.' . $type . '.month_start');
                session()->forget('filter.collection.manage.' . $type . '.month_year_start');
                session()->forget('filter.collection.manage.' . $type . '.month_end');
                session()->forget('filter.collection.manage.' . $type . '.month_year_end');
            } else {
                session()->forget('filter.collection.manage.' . $type . '.year_start');
                session()->forget('filter.collection.manage.' . $type . '.year_end');

                session()->forget('filter.collection.manage.' . $type . '.month_start');
                session()->forget('filter.collection.manage.' . $type . '.month_year_start');
                session()->forget('filter.collection.manage.' . $type . '.month_end');
                session()->forget('filter.collection.manage.' . $type . '.month_year_end');

                session()->forget('filter.collection.manage.' . $type . '.day_start');
                session()->forget('filter.collection.manage.' . $type . '.day_end');
            }

            $queryData = Collection::where(function ($query) use ($type) {
                $query->where('parent_id', 0)
                    ->where('type', $type)
                    ->where('status', 2)
                    ->whereNotNull('received_at')
                    ->whereNotNull('received_by');
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
                })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Collection::where(function ($query) use ($type) {
                $query->where('parent_id', 0)
                    ->where('type', $type)
                    ->where('status', 2)
                    ->whereNotNull('received_at')
                    ->whereNotNull('received_by');
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
                })
                ->count();
        } else {
            $queryData = Collection::where(function ($query) use ($type) {
                $query->where('parent_id', 0)
                    ->where('type', $type)
                    ->where('status', 2)
                    ->whereNotNull('received_at')
                    ->whereNotNull('received_by');
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
                })
                ->where(function ($query) use ($search) {
                    $query->where('deposit', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%");
                })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Collection::where(function ($query) use ($type) {
                $query->where('parent_id', 0)
                    ->where('type', $type)
                    ->where('status', 2)
                    ->whereNotNull('received_at')
                    ->whereNotNull('received_by');
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
                $access_delete = UserCertainAccess::where('role_id', session('role_id'))->where('access', 1)->count();

                if (!$val->edit_by) {
                    if ($access_delete > 0) {
                        $delete_button = '<button type="button" onclick="destroy(' . $val->id . ')" class="btn btn-danger btn-sm"><i class="la la-trash"></i></button>';
                    } else {
                        $delete_button = '<span class="btn btn-danger btn-sm text-white" data-toggle="tooltip" style="opacity:0.6;" title="Tidak Ada Akses" disabled><i class="la la-trash"></i></span>';
                    }

                    $edit = '<a href="' . url('admin/collection/manage/update/' . $val->id) . '" class="btn btn-warning btn-sm"><i class="la la-pencil"></i></a>';
                } else {
                    $delete_button = '<span class="btn btn-danger btn-sm text-white" data-toggle="tooltip" style="opacity:0.6;" title="Tidak bisa dihapus, sedang diedit oleh ' . $val->editBy->username . '" disabled><i class="la la-trash"></i></span>';

                    if ($val->edit_by == session('id')) {
                        $edit = '<a href="' . url('admin/collection/manage/update/' . $val->id) . '" data-toggle="tooltip" title="sedang anda edit" class="btn btn-info btn-sm"><i class="la la-pencil"></i></a>';
                    } else {
                        $edit = '<span class="btn btn-warning btn-sm text-white" data-toggle="tooltip" style="opacity:0.6;" title="sedang diedit oleh ' . $val->editBy->username . '" disabled><i class="la la-ban"></i></span>';
                    }
                }

                $response['data'][] = [
                    $edit,
                    $nomor,
                    $val->manageBy ? '<i class="la la-check text-success"></i>' : '<i class="la la-times text-danger"></i>',
                    $val->lock ? '<i class="la la-check text-success"></i>' : '<i class="la la-times text-danger"></i>',
                    $val->deposit,
                    '<span data-toggle="tooltip" title="' . $val->publisher->name . '">' . Str::limit($val->publisher->name, 20) . '</span>',
                    '<a href="' . url('admin/collection/manage/update/' . $val->id) . '" data-toggle="tooltip" title="' . $val->title . '">' . Str::limit($val->title, 20) . '</a>',
                    $val->code ? $val->code : '<i class="la la-times text-danger"></i>',
                    $updatedBy,
                    $receivedBy,
                    date('d-m-Y', strtotime($val->received_at)),
                    $delete_button
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
        $access_lock = UserCertainAccess::where('role_id', session('role_id'))->where('access', 2)->count();

        if ($collection->status != 2) {
            abort(404);
        }

        if ($request->cancel) {
            $collection->update(['edit_by' => null]);
            return redirect('admin/collection/manage/' . $collection->type);
        }

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
            if ($collection->type == 1) {
                $validator = Validator::make($request->all(), [
                    'publisher_id'     => 'required',
                    'title'            => 'required',
                    'publication_year' => 'required|date_format:Y',
                    'cover'            => 'image|max:2048|mimes:jpg,jpeg,png'
                ], [
                    'publisher_id.required'        => 'Harap memilih penerbit!',
                    'title.required'               => 'Judul wajib di isi!',
                    'publication_year.required'    => 'Tahun terbit wajib di isi!',
                    'publication_year.date_format' => 'Tahun terbit harus berupa tahun!',
                    'cover.image'                  => 'Cover berupa file image!',
                    'cover.max'                    => 'Cover maksimal 1MB!',
                    'cover.mimes'                  => 'Cover harus bertipe jpg, jpeg, png!'
                ]);
            } else if ($collection->type == 2) {
                $validator = Validator::make($request->all(), [
                    'publisher_id'     => 'required',
                    'title'            => 'required',
                    'publication_year' => 'required|date_format:Y',
                    'cover'            => 'image|max:2048|mimes:jpg,jpeg,png'
                ], [
                    'publisher_id.required'        => 'Harap memilih produser!',
                    'title.required'               => 'Judul wajib di isi!',
                    'publication_year.required'    => 'Tahun terbit wajib di isi!',
                    'publication_year.date_format' => 'Tahun terbit harus berupa tahun!',
                    'cover.image'                  => 'Cover berupa file image!',
                    'cover.max'                    => 'Cover maksimal 1MB!',
                    'cover.mimes'                  => 'Cover harus bertipe jpg, jpeg, png!'
                ]);
            } else if ($collection->type == 3) {
                $validator = Validator::make($request->all(), [
                    'publisher_id'     => 'required',
                    'title'            => 'required',
                    'publication_year' => 'required|date_format:Y',
                    'cover'            => 'image|max:2048|mimes:jpg,jpeg,png'
                ], [
                    'publisher_id.required'        => 'Harap memilih penerbit!',
                    'title.required'               => 'Judul wajib di isi!',
                    'publication_year.required'    => 'Tahun terbit wajib di isi!',
                    'publication_year.date_format' => 'Tahun terbit harus berupa tahun!',
                    'cover.image'                  => 'Cover berupa file image!',
                    'cover.max'                    => 'Cover maksimal 1MB!',
                    'cover.mimes'                  => 'Cover harus bertipe jpg, jpeg, png!'
                ]);
            } else if ($collection->type == 4) {
                $validator = Validator::make($request->all(), [
                    'publisher_id'     => 'required',
                    'title'            => 'required',
                    'publication_year' => 'required|date_format:Y',
                    'cover'            => 'image|max:2048|mimes:jpg,jpeg,png'
                ], [
                    'publisher_id.required'        => 'Harap memilih penerbit!',
                    'title.required'               => 'Judul wajib di isi!',
                    'publication_year.required'    => 'Tahun terbit wajib di isi!',
                    'publication_year.date_format' => 'Tahun terbit harus berupa tahun!',
                    'cover.image'                  => 'Cover berupa file image!',
                    'cover.max'                    => 'Cover maksimal 1MB!',
                    'cover.mimes'                  => 'Cover harus bertipe jpg, jpeg, png!'
                ]);
            } else if ($collection->type == 5) {
                $validator = Validator::make($request->all(), [
                    'publisher_id'     => 'required',
                    'title'            => 'required',
                    'publication_year' => 'required|date_format:Y',
                    'cover'            => 'image|max:2048|mimes:jpg,jpeg,png'
                ], [
                    'publisher_id.required'        => 'Harap memilih produser!',
                    'title.required'               => 'Judul wajib di isi!',
                    'publication_year.required'    => 'Tahun terbit wajib di isi!',
                    'publication_year.date_format' => 'Tahun terbit harus berupa tahun!',
                    'cover.image'                  => 'Cover berupa file image!',
                    'cover.max'                    => 'Cover maksimal 1MB!',
                    'cover.mimes'                  => 'Cover harus bertipe jpg, jpeg, png!'
                ]);
            } else if ($collection->type == 6) {
                $validator = Validator::make($request->all(), [
                    'publisher_id'     => 'required',
                    'title'            => 'required',
                    'publication_year' => 'required|date_format:Y',
                    'cover'            => 'image|max:2048|mimes:jpg,jpeg,png'
                ], [
                    'publisher_id.required'        => 'Harap memilih produser!',
                    'title.required'               => 'Judul wajib di isi!',
                    'publication_year.required'    => 'Tahun terbit wajib di isi!',
                    'publication_year.date_format' => 'Tahun terbit harus berupa tahun!',
                    'cover.image'                  => 'Cover berupa file image!',
                    'cover.max'                    => 'Cover maksimal 1MB!',
                    'cover.mimes'                  => 'Cover harus bertipe jpg, jpeg, png!'
                ]);
            }

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator);
            } else {
                if ($collection->type == 1) {
                    $physical_description = [
                        'total_page'  => $request->total_page,
                        'dimension'   => $request->dimension,
                        'ilustration' => $request->ilustration
                    ];
                } else if ($collection->type == 2) {
                    $physical_description = [
                        'total_page'  => $request->total_page,
                        'dimension'   => $request->dimension
                    ];
                } else if ($collection->type == 3) {
                    $physical_description = [
                        'total_page' => $request->total_page,
                        'scale'      => $request->scale,
                        'dimension'  => $request->dimension
                    ];
                } else if ($collection->type == 4) {
                    $physical_description = [
                        'total_page' => $request->total_page,
                        'dimension'  => $request->dimension
                    ];
                } else if ($collection->type == 5) {
                    $physical_description = [
                        'duration' => $request->duration
                    ];
                } else if ($collection->type == 6) {
                    $physical_description = [
                        'duration' => $request->duration
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

                    if ($request->cover) {
                        $collectionMedia = $collection->collectionMedia->where('type', 1)->first();
                        if ($collectionMedia) {
                            Storage::disk('local')->delete($collectionMedia->link);
                            CollectionMedia::where('id', $collectionMedia->id)->forceDelete();
                        }

                        if ($collection->type == 1) {
                            $path = 'public/collection/book/cover';
                        } else if ($collection->type == 2) {
                            $path = 'public/collection/partitur/cover';
                        } else if ($collection->type == 3) {
                            $path = 'public/collection/map/cover';
                        } else if ($collection->type == 4) {
                            $path = 'public/collection/serial/cover';
                        } else if ($collection->type == 5) {
                            $path = 'public/collection/audio/cover';
                        } else if ($collection->type == 6) {
                            $path = 'public/collection/film/cover';
                        }

                        CollectionMedia::create([
                            'collection_id' => $id,
                            'link'          => Storage::disk($this->location->location)->put($path, $request->file('cover')),
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
                                'subjek'           => $log_new_subject
                            ]
                        ])
                        ->log('Mengubah data koleksi (' . $collection->title . ')');

                    $collection->update([
                        'manage_by' => $collection->manage_by ? $collection->manage_by : session('id'),
                        'edit_by'   => null
                    ]);

                    return redirect('admin/collection/manage/' . $collection->type)->with(['success' => 'Koleksi berhasil di update!']);
                } else {
                    return redirect()->back()->with(['failed' => 'Koleksi gagal di update!']);
                }
            }
        } else {
            if ($collection->type == 1) {
                if (count($collection->edition()->get()) > 0) {
                    $data = [
                        'title'   => 'Edit Pengelolaan Buku',
                        'content' => 'admin.book.update_manage_jilid'
                    ];
                } else {
                    $data = [
                        'title'   => 'Edit Pengelolaan Buku',
                        'content' => 'admin.book.update_manage'
                    ];
                }
            } else if ($collection->type == 2) {
                $data = [
                    'title'      => 'Edit Pengelolaan Partitur',
                    'content'    => 'admin.partitur.update_manage'
                ];
            } else if ($collection->type == 3) {
                $data = [
                    'title'      => 'Edit Pengelolaan Peta',
                    'content'    => 'admin.map.update_manage'
                ];
            } else if ($collection->type == 4) {
                $data = [
                    'title'      => 'Edit Pengelolaan Serial',
                    'content'    => 'admin.serial.update_manage'
                ];
            } else if ($collection->type == 5) {
                $data = [
                    'title'      => 'Edit Pengelolaan Audio',
                    'content'    => 'admin.audio.update_manage'
                ];
            } else if ($collection->type == 6) {
                $data = [
                    'title'      => 'Edit Pengelolaan Film',
                    'content'    => 'admin.film.update_manage'
                ];
            } else {
                return redirect()->back();
            }

            $edition = Collection::where('parent_id', $id)
                ->where(function ($query) {
                    if (session('library_id') != 1) {
                        $query->whereHas('city', function ($query) {
                            $query->where('province_id', session('province_id'));
                        });
                    }
                })
                ->get();

            $data = array_merge($data, [
                'category'    => Category::where('type', $collection->type)->get(),
                'collection'  => $collection,
                'locked_url'  => url('admin/collection/lockable/' . $collection->id),
                'access_lock' => $access_lock,
                'contributor' => Contributor::where('type', $collection->type)->where('show', 1)->orderBy('name', 'asc')->get(),
                'edition'     => $edition
            ]);

            return view('admin.layout.index', ['data' => $data]);
        }
    }
}
