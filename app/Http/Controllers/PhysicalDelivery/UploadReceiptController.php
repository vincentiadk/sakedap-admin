<?php

namespace App\Http\Controllers\PhysicalDelivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\QueryAPI;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessReceiptUploadJob;

class UploadReceiptController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'content' => 'physical-delivery.upload-receipt',
                'plugins' => [
                    'datatable',
                    'daterangepicker',
                    'select2',
                ]
            ]
        ]);
    }

    public function datatable(Request $request)
    {
        $column = [
            null,
            'ruh.id',
            'p.name',
            'ruh.receipt_no',
            'ruh.file_name',
            'ruh.status',
            'ruh.total_rows',
            'ruh.success_rows',
            'ruh.failed_rows',
            'ruh.created_at',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 10);

        $data = [];
        $search = strtoupper($request->search['value'] ?? '');

        $whereCondition = [];
        $whereCondition[] = "ruh.created_by = '" . session('username') . "'";

        if ($search) {
            $terms = [];
            foreach ($column as $c) {
                if ($c) {
                    $terms[] = "UPPER($c) LIKE '%$search%'";
                }
            }
            $whereCondition[] = '(' . implode(' OR ', $terms) . ')';
        }

        if ($request->executor_id) {
            $whereCondition[] = "ruh.penerbit_id = " . intval($request->executor_id);
        }

        if ($request->receipt_no) {
            $receiptNo = strtoupper($request->receipt_no);
            $whereCondition[] = "UPPER(ruh.receipt_no) LIKE '%$receiptNo%'";
        }

        if ($request->status) {
            $whereCondition[] = "ruh.status = '" . $request->status . "'";
        }

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $whereCondition[] = "(ruh.created_at >= TO_DATE('$startDate', 'YYYY-MM-DD') 
                AND ruh.created_at < TO_DATE('$endDate', 'YYYY-MM-DD') + 1)";
        }

        $whereClause = 'WHERE ' . implode(' AND ', $whereCondition);

        $orderBy = 'ORDER BY ruh.id DESC';
        if ($request->order) {
            $orderColumnIndex = $request->order[0]['column'];
            $orderDir = $request->order[0]['dir'];

            if (!empty($column[$orderColumnIndex])) {
                $orderBy = "ORDER BY " . $column[$orderColumnIndex] . " $orderDir";
            }
        }

        $totalData = QueryAPI::get("
            SELECT COUNT(*) AS total
            FROM e_receipt_upload_history ruh
            WHERE ruh.created_by = '" . session('username') . "'
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            SELECT COUNT(*) AS total
            FROM e_receipt_upload_history ruh
            LEFT JOIN penerbit p ON ruh.penerbit_id = p.id
            $whereClause
        ", true)->TOTAL ?? 0;

        $sql = "
            SELECT *
            FROM (
                SELECT ROWNUM AS rnum, data.*
                FROM (
                    SELECT 
                        ruh.*,
                        p.name AS penerbit_name
                    FROM e_receipt_upload_history ruh
                    LEFT JOIN penerbit p ON ruh.penerbit_id = p.id
                    $whereClause
                    $orderBy
                ) data
                WHERE ROWNUM <= $length
            )
            WHERE rnum > $start
        ";

        $queryData = QueryAPI::get($sql);

        if ($queryData) {
            foreach ($queryData as $val) {
                $action = '
                    <a href="' . url('/physical-delivery/upload-receipt/history/' . $val->ID) . '" 
                       class="btn btn-primary btn-sm" target="_blank">
                        <i class="ph-info me-1"></i> Detail
                    </a>
                ';

                $data[] = [
                    $action,
                    $val->ID,
                    $val->PENERBIT_NAME,
                    $val->RECEIPT_NO,
                    $val->FILE_NAME,
                    $val->STATUS,
                    $val->TOTAL_ROWS,
                    $val->SUCCESS_ROWS,
                    $val->FAILED_ROWS,
                    Carbon::parse($val->CREATED_AT)->isoFormat('dddd, D MMMM Y'),
                ];
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pelaksana_serah_id' => 'required',
            'receipt_no' => 'required|string|max:100',
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
            'type_of_delivery' => 'nullable|string|max:50',
            'jasa_pengiriman_id' => 'nullable',
            'note' => 'nullable|string',
        ], [
            'pelaksana_serah_id.required' => 'Pelaksana serah wajib dipilih.',
            'receipt_no.required' => 'Nomor resi wajib diisi.',
            'excel_file.required' => 'File Excel wajib diunggah.',
            'excel_file.mimes' => 'File harus berformat XLS atau XLSX.',
            'excel_file.max' => 'Ukuran file maksimal 10 MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $ps = QueryAPI::get("
            SELECT *
            FROM penerbit
            WHERE id = '" . intval($request->pelaksana_serah_id) . "'
        ", true);

        if (!$ps) {
            return response()->json([
                'message' => 'Data pelaksana serah tidak ditemukan.',
                'errors' => [
                    'pelaksana_serah_id' => ['Pelaksana serah tidak valid.']
                ]
            ], 422);
        }

        $excelFile = $request->file('excel_file');

        if (!$excelFile || !$excelFile->isValid()) {
            return response()->json([
                'message' => 'File Excel tidak valid atau gagal upload.',
                'errors' => [
                    'excel_file' => ['File Excel tidak valid atau gagal diunggah.']
                ]
            ], 422);
        }

        $destination = storage_path('app/tmp_receipt_upload');

        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }

        $filename = time() . '_' . $excelFile->getClientOriginalName();
        $excelFile->move($destination, $filename);

        $filePath = $destination . '/' . $filename;

        try {
            $history = QueryAPI::create('e_receipt_upload_history', [
                'penerbit_id' => $request->pelaksana_serah_id,
                'receipt_no' => $request->receipt_no,
                'branch_id' => session('branch_id'),
                'type_of_delivery' => $request->type_of_delivery ?? 'POS',
                'jasa_pengiriman_id' => $request->jasa_pengiriman_id,
                'file_name' => $excelFile->getClientOriginalName(),
                'file_path' => $filePath,
                'status' => 'queued',
                'total_rows' => 0,
                'processed_rows' => 0,
                'success_rows' => 0,
                'failed_rows' => 0,
                'notes' => 'Menunggu proses upload resi',
                'created_by' => session('username'),
            ], true);

            if (!$history || !isset($history->ID)) {
                return response()->json([
                    'message' => 'Gagal menyimpan histori upload resi.'
                ], 500);
            }

            ProcessReceiptUploadJob::dispatch($history->ID, [
                'id' => session('id'),
                'username' => session('username'),
                'fullname' => session('fullname'),
                'branch_id' => session('branch_id')
            ])->onQueue('receipt');

            return response()->json([
                'message' => 'Excel resi berhasil diupload dan masuk antrian proses.',
                'history_id' => $history->ID,
                'progress_url' => url('/physical-delivery/upload-receipt/progress/' . $history->ID),
                'detail_url'   => url('/physical-delivery/upload-receipt/history/' . $history->ID),
            ]);

        } catch (\Throwable $e) {
            Log::error('Gagal upload resi', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan upload resi.',
            ], 500);
        }
    }

    public function progress($id)
    {
        $key = "receipt_upload:{$id}:summary";

        $summary = Redis::get($key);
        $summary = $summary ? json_decode($summary, true) : null;

        if (!$summary) {
            $history = QueryAPI::get("
                SELECT *
                FROM e_receipt_upload_history
                WHERE id = " . intval($id) . "
            ", true);

            return response()->json([
                'status' => $history->STATUS ?? 'unknown',
                'total_rows' => (int) ($history->TOTAL_ROWS ?? 0),
                'processed_rows' => (int) ($history->PROCESSED_ROWS ?? 0),
                'success_rows' => (int) ($history->SUCCESS_ROWS ?? 0),
                'failed_rows' => (int) ($history->FAILED_ROWS ?? 0),
                'notes' => $history->NOTES ?? null,
                'letter_id' => $history->LETTER_ID ?? null,
                'started_at' => $history->STARTED_AT ?? null,
                'finished_at' => $history->FINISHED_AT ?? null,
                'percent' => 0,
            ]);
        }

        $total = (int) ($summary['total_rows'] ?? 0);
        $processed = (int) ($summary['processed_rows'] ?? 0);

        $summary['percent'] = $total > 0
            ? round(($processed / $total) * 100, 2)
            : 0;

        return response()->json($summary);
    }

    public function show($id)
    {
        $history = QueryAPI::get("
            SELECT 
                ruh.*,
                p.name AS penerbit_name
            FROM e_receipt_upload_history ruh
            LEFT JOIN penerbit p ON ruh.penerbit_id = p.id
            WHERE ruh.id = " . intval($id) . "
        ", true);

        return view('layouts.index', [
            'data' => [
                'content' => 'physical-delivery.upload-receipt-show',
                'plugins' => [
                    'datatable',
                ],
                'id' => $id,
                'history' => $history,
            ]
        ]);
    }

    public function datatableShow(Request $request, $id)
    {
        $column = [
            'row_number_upload',
            'isbn',
            'title',
            'qty_delivery',
            'qty_should_accept',
            'qty_accept',
            'qty_reject',
            'status',
            'message',
            'created_at',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 10);

        $data = [];
        $search = strtoupper($request->search['value'] ?? '');

        $whereCondition = [];
        $whereCondition[] = "receipt_upload_history_id = " . intval($id);

        if ($search) {
            $terms = [];
            foreach ($column as $c) {
                if ($c) {
                    $terms[] = "UPPER($c) LIKE '%$search%'";
                }
            }
            $whereCondition[] = '(' . implode(' OR ', $terms) . ')';
        }

        $whereClause = 'WHERE ' . implode(' AND ', $whereCondition);

        $orderBy = 'ORDER BY row_number_upload ASC';
        if ($request->order) {
            $orderColumnIndex = $request->order[0]['column'];
            $orderDir = $request->order[0]['dir'];

            if (!empty($column[$orderColumnIndex])) {
                $orderBy = "ORDER BY " . $column[$orderColumnIndex] . " $orderDir";
            }
        }

        $totalData = QueryAPI::get("
            SELECT COUNT(*) AS total
            FROM e_receipt_upload_history_detail
            WHERE receipt_upload_history_id = " . intval($id) . "
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            SELECT COUNT(*) AS total
            FROM e_receipt_upload_history_detail
            $whereClause
        ", true)->TOTAL ?? 0;

        $sql = "
            SELECT *
            FROM (
                SELECT ROWNUM AS rnum, data.*
                FROM (
                    SELECT *
                    FROM e_receipt_upload_history_detail
                    $whereClause
                    $orderBy
                ) data
                WHERE ROWNUM <= $length
            )
            WHERE rnum > $start
        ";

        $queryData = QueryAPI::get($sql);

        if ($queryData) {
            foreach ($queryData as $val) {
                $data[] = [
                    $val->ROW_NUMBER_UPLOAD,
                    $val->ISBN,
                    $val->TITLE,
                    $val->QTY_DELIVERY,
                    $val->QTY_SHOULD_ACCEPT,
                    $val->QTY_ACCEPT,
                    $val->QTY_REJECT,
                    $val->STATUS,
                    $val->MESSAGE,
                    Carbon::parse($val->CREATED_AT)->isoFormat('dddd, D MMMM Y'),
                ];
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    public function progressRealtime($id)
    {
        $pattern = "receipt_upload:{$id}:row:*";
        $keys = Redis::keys($pattern);

        $rows = [];

        foreach ($keys as $key) {
            $key = str_replace(config('database.redis.options.prefix'), '', $key);

            $json = Redis::get($key);
            if (!$json) {
                continue;
            }

            $item = json_decode($json, true);
            if (!$item) {
                continue;
            }

            $rows[] = [
                'row_number_upload' => (int) ($item['row_number_upload'] ?? 0),
                'status' => $item['status'] ?? 'unknown',
                'isbn' => $item['isbn'] ?? null,
                'title' => $item['title'] ?? null,
                'qty_delivery' => $item['qty_delivery'] ?? null,
                'qty_accept' => $item['qty_accept'] ?? null,
                'qty_reject' => $item['qty_reject'] ?? null,
                'message' => $item['message'] ?? null,
                'updated_at' => $item['updated_at'] ?? null,
            ];
        }

        usort($rows, fn ($a, $b) => $a['row_number_upload'] <=> $b['row_number_upload']);

        $total = count($rows);
        $success = count(array_filter($rows, fn ($r) => $r['status'] === 'success'));
        $failed = count(array_filter($rows, fn ($r) => $r['status'] === 'failed'));
        $pending = count(array_filter($rows, fn ($r) => $r['status'] === 'pending'));
        $running = count(array_filter($rows, fn ($r) => $r['status'] === 'running'));

        $processed = $success + $failed;
        $percent = $total > 0 ? round(($processed / $total) * 100, 2) : 0;

        return response()->json([
            'summary' => [
                'total_rows_in_redis' => $total,
                'processed_rows' => $processed,
                'success_rows' => $success,
                'failed_rows' => $failed,
                'pending_rows' => $pending,
                'running_rows' => $running,
                'percent' => $percent,
            ],
            'rows' => $rows,
        ]);
    }
}