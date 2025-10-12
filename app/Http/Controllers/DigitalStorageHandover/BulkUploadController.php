<?php

namespace App\Http\Controllers\DigitalStorageHandover;

use Carbon\Carbon;
use App\Jobs\BulkJob;
use App\Helpers\QueryAPI;
use App\Imports\BulkImport;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use ZanySoft\Zip\Facades\Zip;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BulkUploadController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'content' => 'digital-storage-handover.bulk-upload',
                'plugins' => [
                    'fileinput',
                    'select2',
                    'datatable',
                ]
            ]
        ]);
    }

    public function datatableBulk(Request $request)
    {
        $column = [
            'id',
            null,
            'file_upload',
            'process_start_at',
            'process_finish_at',
            'status_progress',
            'created_at',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "user_id = " . session('id');

        if ($search) {
            $terms = [];

            foreach ($column as $c) {
                if ($c) {
                    $terms[] = "upper($c) like '%$search%'";
                }
            }

            $whereCondition[] = '(' . implode(' or ', $terms) . ')';
        }

        if ($whereCondition) {
            $whereClause = "where " . implode(' and ', $whereCondition);
        }

        if ($order) {
            $orderColumnIndex = $order[0]['column'];
            $orderDir = $order[0]['dir'];
            $orderBy = "order by " . $column[$orderColumnIndex] . " $orderDir";
        }

        $totalData = QueryAPI::get("
            select
                count(*) as total
            from
                e_bulks
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                e_bulks
            $whereClause
        ", true)->TOTAL ?? 0;

        $queryData = QueryAPI::get("
            select
                *
            from (
                    select
                        rownum as rnum,
                        data.*
                    from
                        (
                            select
                                *
                            from
                                e_bulks
                            $whereClause
                            $orderBy
                        ) data
                )
            where
                rnum > $start and rnum <= $length
        ");

        if ($queryData) {
            foreach ($queryData as $val) {
                $action = '
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm" onclick="showData(' . $val->ID . ')">
                        <i class="ph-eye me-1"></i>
                        Detail
                    </a>
                ';

                $fileUpload = '
                    <a href="' . url('download/from-storage') . '?path=' . $val->FILE_UPLOAD . '" class="text-primary" target="_blank">' . $val->NAME . '</a>
                ';

                $data[] = [
                    $start + 1,
                    $action,
                    $fileUpload,
                    $val->PROCESS_START_AT ? Carbon::parse($val->PROCESS_START_AT)->isoFormat('dddd, D MMMM Y') : '',
                    $val->PROCESS_FINISH_AT ? Carbon::parse($val->PROCESS_FINISH_AT)->isoFormat('dddd, D MMMM Y') : '',
                    $val->STATUS_PROGRESS,
                    $val->CREATED_AT ? Carbon::parse($val->CREATED_AT)->isoFormat('dddd, D MMMM Y') : '',
                ];

                $start++;
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    }

    public function detailBulk(Request $request)
    {
        $id = $request->id;
        $data = QueryAPI::get("select * from e_bulk_details where bulk_id = $id");

        return response()->json($data);
    }

    public function submitted(Request $request)
    {
        if (!$request->ajax()) {
            return response()->json([
                'code' => 400,
                'message' => 'Invalid Request Type.'
            ]);
        }

        $validation = Validator::make($request->all(), [
            'type' => 'required',
            'id' => 'required',
            'file' => 'required|file|mimes:zip|max:500000',
        ], [
            'type.required' => 'Jenis tidak boleh kosong',
            'id.required' => 'Pelaksana Serah (Non Serial) / Katalog (Serial) tidak boleh kosong',
            'file.required' => 'File tidak boleh kosong',
            'file.file' => 'File tidak valid',
            'file.mimes' => 'File harus zip',
            'file.max' => 'File maksimal 500 MB',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'code' => 400,
                'error' => $validation->errors()->all(),
            ]);
        }

        $path = null;
        $storedFile = null;
        $bulk = null;

        try {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();

            $path = 'bulk/' . date('Y-m-d') . '/' . Str::random(20);
            $storedFile = $file->storeAs($path, $filename);
            $fullPath = Storage::path($storedFile);

            if (!Storage::exists($storedFile)) {
                throw new \Exception('Gagal menyimpan file ke disk.', 500);
            }

            try {
                $zip = Zip::open($fullPath);
                $listFile = $zip->listFiles() ?? [];
            } catch (\Exception $e) {
                if ($storedFile) {
                    Storage::delete($storedFile);
                }

                Log::error("Gagal membuka file ZIP: " . $e->getMessage(), [
                    'file' => $filename
                ]);

                return response()->json([
                    'code' => 500,
                    'message' => 'Gagal membuka file ZIP: ' . $e->getMessage()
                ]);
            }

            if (!in_array('data.xlsx', $listFile)) {
                $zip->close();

                Storage::delete($storedFile);

                return response()->json([
                    'code' => 404,
                    'message' => 'Metadata (data.xlsx) tidak ditemukan dalam file ZIP.'
                ]);
            }

            $targetExtractionPath = Storage::path($path);
            $zip->extract($targetExtractionPath);
            $zip->close();

            $bulk = QueryAPI::create('e_bulks', [
                'user_id' => session('id'),
                'name' => $filename,
                'file_upload' => $storedFile,
                'status_progress' => 'MENUNGGU',
            ]);

            if (!$bulk) {
                Storage::deleteDirectory($path);

                return response()->json([
                    'code' => 500,
                    'message' => 'Gagal membuat data bulk.'
                ]);
            }

            $fileExcel = $targetExtractionPath . '/data.xlsx';
            $collection = Excel::toCollection(new BulkImport, $fileExcel);
            $rowData = $collection->first();

            if ($collection->isEmpty() || $rowData->isEmpty()) {
                Storage::deleteDirectory($path);

                return response()->json([
                    'code' => 500,
                    'message' => 'File Excel kosong atau tidak terdeteksi.'
                ]);
            }

            $requestData = (object) [
                'user_id' => session('id'),
                'bulk_id' => $bulk->ID,
                'type' => $request->type,
                'id' => $request->id,
                'path' => $path,
            ];

            BulkJob::dispatch($rowData, $requestData)->onQueue('bulk');

            return response()->json([
                'code' => 200,
                'message' => 'File telah masuk dalam antrian proses'
            ]);
        } catch (\Exception $e) {
            Log::error("Proses Bulk Gagal: " . $e->getMessage(), [
                'path' => $path,
                'error_trace' => $e->getTraceAsString()
            ]);

            if ($path && Storage::exists($path)) {
                Storage::deleteDirectory($path);
            }

            return response()->json([
                'code' => 500,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ]);
        }
    }
}
