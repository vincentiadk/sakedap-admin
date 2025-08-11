<?php

namespace App\Http\Controllers\Admin;

use App\Models\Bulk;
use ZanySoft\Zip\Zip;
use App\Models\Location;
use App\Models\Collection;
use App\Models\DepositHead;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\BatchCollection;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Exports\TemplateBulkKckraExport;
use Illuminate\Support\Facades\Validator;

class CollectionKckraBulkUploadController extends Controller
{
    protected $location;

    public function __construct()
    {
        $this->location = Location::where('active', 1)->first();
    }

    public function index()
    {
        $deposit_head = DepositHead::whereIn('category', ['KC', 'KRA'])->get();
        $library_id = session('library_id');
        $data = [
            'title'   => 'Bulk Upload KCKRA',
            'deposit_head'   => $deposit_head,
            'library_id'   => $library_id,
            'content' => 'admin.kckra.bulk_upload'
        ];
        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatableSerial(Request $request, $deposit_head)
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

        $totalData = Collection::where(function ($query) use ($deposit_head) {
            $query->where('parent_id', 0)
                ->where('type', $deposit_head);
        })
            ->where(function ($query) {
                if (session('library_id') != 1) {
                    $query->whereHas('publisher', function ($query) {
                        $query->where('province_id', session('province_id'));
                    });
                }
            })
            ->count();
        if (empty($search)) {
            $queryData = Collection::where(function ($query) use ($deposit_head) {
                $query->where('parent_id', 0)
                    ->where('type', $deposit_head)
                    ->where('status', 2);
            })
                ->where(function ($query) {
                    if (session('library_id') != 1) {
                        $query->whereHas('publisher', function ($query) {
                            $query->where('province_id', session('province_id'));
                        });
                    }
                })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Collection::where(function ($query) use ($deposit_head) {
                $query->where('parent_id', 0)
                    ->where('type', $deposit_head);
            })
                ->where(function ($query) {
                    if (session('library_id') != 1) {
                        $query->whereHas('publisher', function ($query) {
                            $query->where('province_id', session('province_id'));
                        });
                    }
                })
                ->count();
        } else {
            $queryData = Collection::where(function ($query) use ($deposit_head) {
                $query->where('parent_id', 0)
                    ->where('type', $deposit_head);
            })
                ->where(function ($query) {
                    if (session('library_id') != 1) {
                        $query->whereHas('publisher', function ($query) {
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
            $totalFiltered = Collection::where(function ($query) use ($deposit_head) {
                $query->where('parent_id', 0)
                    ->where('type', $deposit_head);
            })
                ->where(function ($query) {
                    if (session('library_id') != 1) {
                        $query->whereHas('publisher', function ($query) {
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
        // dd($request);
        if ($request->flag == 'serial') {
            $validator = Validator::make($request->all(), [
                'type_serial' => 'required',
                'collection_id' => 'required',
                'file_upload'   => 'required|mimes:zip|max:512000'
            ], [
                'type_serial.required' => 'Harap memilih tipe koleksi!',
                'collection_id.required' => 'Harap memilih koleksi!',
                'file_upload.required'   => 'File upload tidak boleh kosong!',
                'file_upload.mimes'      => 'File upload hanya boleh .zip!',
                'file_upload.max'        => 'File upload max 500MB!'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'type_non_serial' => 'required',
                'publisher_id' => 'required',
                'file_upload'  => 'required|mimes:zip|max:512000'
            ], [
                'type_non_serial.required' => 'Harap memilih tipe koleksi!',
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
            $directory = 'public/temp_bulk_kckra/' . date('Y_m_d_H_i_s');
            $save_file = Storage::disk($this->location->location)->put('public/temp_bulk_kckra/' . date('Y_m_d_H_i_s'), $file);

            $get_file  = Zip::open(Storage::disk($this->location->location)->path($save_file));
            $get_file->extract(Storage::disk($this->location->location)->path($directory));

            $bulk = Bulk::create([
                'user_id'          => session('id'),
                'name'             => $file->getClientOriginalName(),
                'file'             => Storage::disk($this->location->location)->path($save_file),
                'process_start_at' => date('Y-m-d H:i:s'),
                'status'           => 2,
                'deposit_head_id'  => !empty($request->type_serial) ? $request->type_serial : $request->type_non_serial
            ]);

            $data = (object)[
                'file_excel'    => $directory . '/' . 'data.xlsx',
                'bulk_id'       => $bulk->id,
                'user_id'       => session('id'),
                'library_id'    => session('library_id'),
                'collection_id' => $request->collection_id,
                'flag'          => $request->flag,
                'type'          => !empty($request->type_serial) ? $request->type_serial : $request->type_non_serial,
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

    function downloadTemplate(Request $request)
    {
        $type = $request->input('type');
        try {
            // Export the Excel file
            $excelFileName = 'data.xlsx';
            $excelFilePath = 'template/' . $excelFileName;

            // Optionally, you can delete the original Excel file if needed
            if (Storage::disk($this->location->location)->exists('public/' . $excelFilePath)) {
                Storage::disk($this->location->location)->delete('public/' . $excelFilePath);
            }

            // Store on a different disk with a defined writer type.
            Excel::store(new TemplateBulkKckraExport([$type]), 'public/' . $excelFilePath, $this->location->location);

            // Create a unique ZIP file name
            $zipFileName = 'template_kckra_' . $type . '_' . Str::random(10) . '.zip';
            $zipFilePath = 'template/' . $zipFileName;

            // Create the ZIP archive
            $zip = Zip::create(Storage::disk($this->location->location)->path('public/' . $zipFilePath));
            $zip->add(Storage::disk($this->location->location)->path('public/' . $excelFilePath), $excelFileName);
            $zip->add(Storage::disk($this->location->location)->path('public/template/judul1.jpg'), 'judul1.jpg');
            $zip->add(Storage::disk($this->location->location)->path('public/template/judul2.jpg'), 'judul2.jpg');
            $zip->close();


            if (Storage::disk($this->location->location)->exists('public/' . $zipFilePath)) {
                return response()->download(Storage::disk($this->location->location)->path('public/' . $zipFilePath))->deleteFileAfterSend();
            } else {
                return false;
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function progress()
    {
        $data = [
            'title'   => 'Progress Bulk Upload KCKRA',
            'content' => 'admin.kckra.bulk_upload_progress'
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

        $totalData = Bulk::where('deposit_head_id', '!=', 0)->where('user_id', session('id'))
            ->count();
        if (empty($search)) {
            $queryData = Bulk::where('deposit_head_id', '!=', 0)->where('user_id', session('id'))
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Bulk::where('deposit_head_id', '!=', 0)->where('user_id', session('id'))
                ->count();
        } else {
            $queryData = Bulk::where('deposit_head_id', '!=', 0)->where('user_id', session('id'))
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Bulk::where('deposit_head_id', '!=', 0)->where('user_id', session('id'))
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
