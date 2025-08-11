<?php

namespace App\Http\Controllers\Admin;

use App\Models\Library;
use App\Models\Location;
use App\Models\Publisher;
use Illuminate\Http\Request;
use App\Models\PublisherWarning;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PublisherWarningController extends Controller
{
    protected $location;

    public function __construct()
    {
        $this->location = Location::where('active', 1)->first();
    }

    public function create(Request $request)
    {
        if ($request->has('_token')) {

            $validation = Validator::make($request->all(), [
                'publisher_id' => 'required',
                'warning' => 'required',
                'warning_date' => 'required',
            ], [
                'publisher_id.required' => 'Mohon memilih penerbit.',
                'warning.required' => 'Mohon mengisi pilihan teguran.',
                'warning_date.required' => 'Mohon mengisi tanggal teguran.',
            ]);

            if ($validation->fails()) {
                $response = [
                    'status' => 422,
                    'error'  => $validation->errors()
                ];

                return response()->json($response);
            }

            $file      = $request->file('attachment');
            $link_attachment = Storage::disk($this->location->location)->put('public/publisher/warning', $file);

            $library = $request->category == 1 ? 1 : Library::where('province_id', $request->province_id)->value('id');

            PublisherWarning::create([
                "publisher_id"              => $request->publisher_id,
                "library_id"                => $library,
                "warning"                   => $request->warning,
                "warning_date"              => date('Y-m-d', strtotime($request->warning_date)),
                "attachment"                => $link_attachment,
                "reason"                    => $request->reason,
                "location_id"               => $this->location->id,
            ]);

            $response = [
                'status'  => 200,
                'message' => 'Data telah diproses.'
            ];

            return response()->json($response);
        } else {
            $data = [
                'title'   => 'KCKR - Tegur Penerbit',
                'content' => 'admin.publisher_warning.create'
            ];

            return view('admin.layout.index', ['data' => $data]);
        }
    }

    public function index(Request $request)
    {
        $data = [
            'title'   => 'Teguran Penerbit',
            'content' => 'admin.publisher_warning.index'
        ];


        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {

        $whereLike = [
            'name',
            'name',
        ];

        $start    = $request->start == null ? 0 : $request->start;
        $length   = $request->length == null ? 10 : $request->length;
        $search   = $request->input('search.value');
        $order      = $whereLike[$request->input('order.0.column')];
        $dir        = $request->input('order.0.dir');

        $params = [];
        if ($search) {
            $params['nama_penerbit'] = "*$search*";
        }


        $queryPublisherId = $request->input('publisher_id');
        $queryProvinceId = $request->input('province_id');
        $countWarning = $request->input('warning_count');

        $currentProvince = session('library_id') != 1 ? session('province_id') : null;

        $totalData =  Publisher::select('id', 'name', 'publisher_code')
            ->withCount('warning')
            ->has('warning', '>', 0)
            ->when($currentProvince != null, function ($query) use ($currentProvince) {
                $query->where('province_id', $currentProvince);
            })
            ->count();
        if (
            empty($search) && $request->input('publisher_id') == null
            && $request->input('province_id')  == null
            && $request->input('warning_count')  == null
        ) {

            $queryData =  Publisher::select('id', 'name', 'publisher_code')
                ->withCount('warning')
                ->has('warning', '>', 0)
                ->when($currentProvince != null, function ($query) use ($currentProvince) {
                    $query->where('province_id', $currentProvince);
                })
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Publisher::select('id', 'name', 'publisher_code')
                ->withCount('warning')
                ->has('warning', '>', 0)
                ->when($currentProvince != null, function ($query) use ($currentProvince) {
                    $query->where('province_id', $currentProvince);
                })
                ->count();
        } else {
            $queryData = Publisher::select('id', 'name', 'publisher_code')
                ->when($countWarning != null, function ($query) use ($countWarning) {
                    $query->withCount('warning');
                    $query->has('warning', '=', $countWarning);
                })
                ->when($countWarning == null, function ($query) {
                    $query->withCount('warning');
                    $query->has('warning', '>', 0);
                })
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->when($queryPublisherId != null, function ($query) use ($queryPublisherId) {
                    $query->whereIn('id', $queryPublisherId);
                })
                ->when($queryProvinceId != null, function ($query) use ($queryProvinceId) {
                    $query->whereIn('province_id', $queryProvinceId);
                })
                ->when($currentProvince != null, function ($query) use ($currentProvince) {
                    $query->where('province_id', $currentProvince);
                })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Publisher::select('id', 'name', 'publisher_code')
                ->when($countWarning != null, function ($query) use ($countWarning) {
                    $query->withCount('warning');
                    $query->has('warning', '=', $countWarning);
                })
                ->when($countWarning == null, function ($query) {
                    $query->withCount('warning');
                })
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->when($queryPublisherId != null, function ($query) use ($queryPublisherId) {
                    $query->whereIn('id', $queryPublisherId);
                })
                ->when($queryProvinceId != null, function ($query) use ($queryProvinceId) {
                    $query->whereIn('province_id', $queryProvinceId);
                })
                ->when($currentProvince != null, function ($query) use ($currentProvince) {
                    $query->where('province_id', $currentProvince);
                })
                ->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                $response['data'][] = [
                    $nomor,
                    $val->name,
                    $val->warning_count,
                ];
                $nomor++;
            }
        }

        $response['recordsTotal'] = $totalData;
        $response['recordsFiltered'] = $totalFiltered;

        return response()->json($response);
    }

    public function listDatatable(Request $request)
    {

        $whereLike = [
            'library_id',
            'publisher_id',
        ];

        $start    = $request->start == null ? 0 : $request->start;
        $length   = $request->length == null ? 10 : $request->length;
        $search   = $request->input('search.value');
        $order      = $whereLike[$request->input('order.0.column')];
        $dir        = $request->input('order.0.dir');

        $params = [];
        if ($search) {
            $params['nama_penerbit'] = "*$search*";
        }


        $queryPublisherId = $request->input('publisher_id');
        $queryProvinceId = $request->input('province_id');
        $queryLibraryId = Library::where('province_id', $request->input('province_id'))->value('id');
        $countWarning = $request->input('warning_count');

        $currentProvince = session('library_id') != 1 ? session('province_id') : null;

        $totalData =  PublisherWarning::when($currentProvince != null, function ($query) use ($currentProvince) {
            $query->whereHas('publisher', function ($query) use ($currentProvince) {
                $query->where('province_id', $currentProvince);
            });
        })->count();
        if (
            empty($search) && $request->input('publisher_id') == null
            && $request->input('province_id')  == null
            && $request->input('warning_count')  == null
        ) {

            $queryData =  PublisherWarning::when($currentProvince != null, function ($query) use ($currentProvince) {
                $query->whereHas('publisher', function ($query) use ($currentProvince) {
                    $query->where('province_id', $currentProvince);
                });
            })->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = PublisherWarning::when($currentProvince != null, function ($query) use ($currentProvince) {
                $query->whereHas('publisher', function ($query) use ($currentProvince) {
                    $query->where('province_id', $currentProvince);
                });
            })->count();
        } else {
            $queryData = PublisherWarning::where(function ($query) use ($search) {
                $query->where('reason', 'like', "%{$search}%");
            })
                ->when($queryPublisherId != null, function ($query) use ($queryPublisherId) {
                    $query->where('publisher_id', $queryPublisherId);
                })
                ->when($queryLibraryId != null, function ($query) use ($queryLibraryId) {
                    $query->where('library_id', $queryLibraryId);
                })
                ->when($countWarning != null, function ($query) use ($countWarning) {
                    $query->where('warning', $countWarning);
                })->when($currentProvince != null, function ($query) use ($currentProvince) {
                    $query->whereHas('publisher', function ($query) use ($currentProvince) {
                        $query->where('province_id', $currentProvince);
                    });
                })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = PublisherWarning::where(function ($query) use ($search) {
                $query->where('reason', 'like', "%{$search}%");
            })
                ->when($queryPublisherId != null, function ($query) use ($queryPublisherId) {
                    $query->where('publisher_id', $queryPublisherId);
                })
                ->when($queryLibraryId != null, function ($query) use ($queryLibraryId) {
                    $query->where('library_id', $queryLibraryId);
                })
                ->when($countWarning != null, function ($query) use ($countWarning) {
                    $query->where('warning', $countWarning);
                })->when($currentProvince != null, function ($query) use ($currentProvince) {
                    $query->whereHas('publisher', function ($query) use ($currentProvince) {
                        $query->where('province_id', $currentProvince);
                    });
                })
                ->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                $response['data'][] = [
                    $nomor,
                    $val->publisher->name,
                    date('d-m-Y', strtotime($val->warning_date)),
                    'Teguran ke-' . $val->warning,
                    $val->library->name,
                    $val->reason,
                    '
                        <button type="button" onclick="show(' . $val->id . ')" class="btn btn-warning btn-sm"><i class="la la-pencil"></i> Edit</button>
                        <button type="button" onclick="destroy(' . $val->id . ')" class="btn btn-danger btn-sm"><i class="la la-trash"></i> Hapus</button>
                    '
                ];
                $nomor++;
            }
        }

        $response['recordsTotal'] = $totalData;
        $response['recordsFiltered'] = $totalFiltered;

        return response()->json($response);
    }

    public function publisherDatatable(Request $request)
    {

        $whereLike = [
            'name',
            'name',
        ];

        $start    = $request->start == null ? 0 : $request->start;
        $length   = $request->length == null ? 10 : $request->length;
        $search   = $request->input('search.value');
        $order      = $whereLike[$request->input('order.0.column')];
        $dir        = $request->input('order.0.dir');

        $countWarning = $request->input('warning_count');

        $totalData =  Publisher::count();
        $queryData = Publisher::select('id', 'name', 'publisher_code')
            ->when($countWarning != null, function ($query) use ($countWarning) {
                $query->withCount('warning');
                $query->has('warning', '<', $countWarning);
            })
            ->when($countWarning == null, function ($query) {
                $query->withCount('warning');
            })
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->offset($start)
            ->limit($length)
            ->orderBy($order, $dir)
            ->get();
        $totalFiltered = Publisher::select('id', 'name', 'publisher_code')
            ->when($countWarning != null, function ($query) use ($countWarning) {
                $query->withCount('warning');
                $query->has('warning', '<', $countWarning);
            })
            ->when($countWarning == null, function ($query) {
                $query->withCount('warning');
            })
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->count();

        $response['data'] = [];
        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                $response['data'][] = [
                    $val->id,
                    $val->name,
                    $val->warning_count,
                ];
                $nomor++;
            }
        }

        $response['recordsTotal'] = $totalData;
        $response['recordsFiltered'] = $totalFiltered;

        return response()->json($response);
    }

    public function show($id)
    {
        $data = PublisherWarning::find($id);

        return response()->json([
            'id' => $data->id,
            'name' => $data->publisher->name,
            'warning' => $data->warning,
            'reason' => $data->reason,
            'warning_date' => date('Y-m-d', strtotime($data->warning_date)),
            'attachment_link' => asset(Storage::disk($data->location->location)->url($data->attachment)),
        ]);
    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'warning_date' => 'required',
        ], [
            'warning_date.required' => 'Mohon mengisi tanggal teguran.',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $publisherWarning = PublisherWarning::find($id);

            if ($request->has('attachment')) {
                Storage::disk($this->location->location)->delete($publisherWarning->attachment);
                $attachment = Storage::disk($this->location->location)->put('public/publisher/warning', $request->file('attachment'));
            } else {
                $attachment = $publisherWarning->attachment;
            }

            $old_data = $publisherWarning;
            $new_data = PublisherWarning::find($id);

            $new_data->update([
                'attachment'   => $attachment,
                'warning_date' => $request->warning_date,
                'reason'       => $request->reason,
                'location_id'  => $this->location->id
            ]);

            if ($new_data) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil diupdate!'
                ];

                activity('publisher_warnings')
                    ->performedOn(new PublisherWarning())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'data_lama' => [
                            'attachment'    => asset(Storage::disk($old_data->location->location)->url($old_data->attachment)),
                            'warning_date'  => $old_data->warning_date,
                            'reason'        => $old_data->reason
                        ],
                        'data_baru' => [
                            'attachment'    => asset(Storage::url($new_data->attachment)),
                            'warning_date'  => $new_data->warning_date,
                            'reason'        => $new_data->reason
                        ]
                    ])
                    ->log('mengubah data publisher warning');
            } else {
                $response = [
                    'status'  => 500,
                    'message' => 'Gagal diupdate'
                ];
            }
        }

        return response()->json($response);
    }

    public function destroy($id)
    {
        $destroy = PublisherWarning::where('id', $id)->delete();
        $data = PublisherWarning::withTrashed()->find($id);

        if ($destroy) {
            $response = [
                'status' => 200,
                'message' => 'Berhasil dihapus!',
            ];

            activity('publisher_warnings')
                ->performedOn(new PublisherWarning())
                ->causedBy(session('id'))
                ->withProperties([
                    'id' => $data->id,
                ])
                ->log('Menghapus data teguran penerbit');
        } else {
            $response = [
                'status' => 500,
                'message' => 'Gagal dihapus',
            ];
        }

        return response()->json($response);
    }

    public function countPublisherWarnings(Request $request, $publisherId)
    {
        $count = PublisherWarning::where('publisher_id', $publisherId)->count();

        $last = PublisherWarning::where('publisher_id', $publisherId)->latest('warning_date')->first();;

        return response()->json(['count' => $count, 'last' => $last]);
    }
}
