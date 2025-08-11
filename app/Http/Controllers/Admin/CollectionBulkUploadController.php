<?php

namespace App\Http\Controllers\Admin;

use App\Models\Bulk;
use ZanySoft\Zip\Zip;
use App\Models\Location;
use App\Models\Collection;
use Illuminate\Http\Request;
use App\Jobs\BatchCollection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CollectionBulkUploadController extends Controller
{

    protected $location;

    public function __construct()
    {
        $this->location = Location::where('active', 1)->first();
    }

    public function index()
    {
        $data = [
            'title'   => 'Bulk Upload',
            'content' => 'admin.collection.bulk_upload'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatableSerial(Request $request)
    {
        $whereLike = [
            'id',
            'title',
            'publisher_id'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $totalData = Collection::where(function ($query) {
            $query->where('parent_id', 0)
                ->where('type', 4)
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
            $queryData = Collection::where(function ($query) {
                $query->where('parent_id', 0)
                    ->where('type', 4)
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
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Collection::where(function ($query) {
                $query->where('parent_id', 0)
                    ->where('type', 4)
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
        } else {
            $queryData = Collection::where(function ($query) {
                $query->where('parent_id', 0)
                    ->where('type', 4)
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
                ->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhereHas('publisher', function ($query) use ($search) {
                            $query->where('name', 'like',  "%{$search}%");
                        });
                })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Collection::where(function ($query) {
                $query->where('parent_id', 0)
                    ->where('type', 4)
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
                ->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhereHas('publisher', function ($query) use ($search) {
                            $query->where('name', 'like',  "%{$search}%");
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
                    $val->title,
                    $val->publisher->name,
                    '
                        <input type="radio" name="collection_id" id="collection_id" value="' . $val->id . '" required>
                    '
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

    public function actionUpload(Request $request)
    {
        if ($request->flag == 'serial') {
            $validator = Validator::make($request->all(), [
                'collection_id' => 'required',
                'file_upload'   => 'required|mimes:zip|max:512000'
            ], [
                'collection_id.required' => 'Harap memilih koleksi!',
                'file_upload.required'   => 'File upload tidak boleh kosong!',
                'file_upload.mimes'      => 'File upload hanya boleh .zip!',
                'file_upload.max'        => 'File upload max 500MB!'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'type'         => 'required',
                'publisher_id' => 'required',
                'file_upload'  => 'required|mimes:zip|max:512000'
            ], [
                'type.required'         => 'Harap memilih tipe koleksi!',
                'publisher_id.required' => 'Harap memilih penerbit!',
                'file_upload.required'  => 'File upload tidak boleh kosong!',
                'file_upload.mimes'     => 'File upload hanya boleh .zip!',
                'file_upload.max'       => 'File upload max 500MB!'
            ]);
        }

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $file      = $request->file('file_upload');
            $directory = 'public/temp_bulk/' . date('Y_m_d_H_i_s');
            $save_file = Storage::disk($this->location->location)->put('public/temp_bulk/' . date('Y_m_d_H_i_s'), $file);

            $get_file  = Zip::open(Storage::disk($this->location->location)->path($save_file));
            $get_file->extract(Storage::disk($this->location->location)->path($directory));

            $bulk = Bulk::create([
                'user_id'          => session('id'),
                'name'             => $file->getClientOriginalName(),
                'file'             => Storage::disk($this->location->location)->path($save_file),
                'process_start_at' => date('Y-m-d H:i:s'),
                'status'           => 2
            ]);

            $data = (object)[
                'file_excel'    => $directory . '/' . 'data.xlsx',
                'bulk_id'       => $bulk->id,
                'user_id'       => session('id'),
                'collection_id' => $request->collection_id,
                'flag'          => $request->flag,
                'type'          => $request->type,
                'publisher_id'  => $request->publisher_id
            ];

            $run = dispatch(new BatchCollection($data))->onQueue('bulk_upload');
            if ($run) {
                $response = [
                    'status'  => 200,
                    'message' => 'File telah diproses'
                ];
            } else {
                $response = [
                    'status'  => 500,
                    'message' => 'File gagal diproses'
                ];
            }
        }

        return response()->json($response);
    }

    public function download(Request $request)
    {
        $filename = base64_decode($request->param);
        return response()->download(public_path('main/' . $filename));
    }

    public function progress()
    {
        $data = [
            'title'   => 'Progress Bulk Upload',
            'content' => 'admin.collection.bulk_upload_progress'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatableProgress(Request $request)
    {
        $whereLike = [
            'id',
            'name',
            'process_start_at',
            'process_finish_at',
            'status'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $totalData = Bulk::where('user_id', session('id'))
            ->count();
        if (empty($search)) {
            $queryData = Bulk::where('user_id', session('id'))
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Bulk::where('user_id', session('id'))
                ->count();
        } else {
            $queryData = Bulk::where('user_id', session('id'))
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Bulk::where('user_id', session('id'))
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
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
                    $val->process_start_at,
                    $val->process_finish_at,
                    $val->status(),
                    '
                        <a href="javascript:void(0);" onclick="show(' . $val->id . ')" class="btn btn-warning btn-sm"><i class="la la-eye"></i></a>
                    '
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

    public function showProgress(Request $request)
    {
        $data        = Bulk::find($request->id);
        $bulk_detail = [];

        if ($data->bulkDetail->count() > 0) {
            foreach ($data->bulkDetail as $bd) {
                $bulk_detail[] = [
                    'title'       => $bd->title,
                    'description' => $bd->description,
                    'status'      => $bd->status()
                ];
            }
        }

        return response()->json([
            'bulk_detail' => $bulk_detail
        ]);
    }
}
