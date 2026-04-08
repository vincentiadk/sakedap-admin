<?php

namespace App\Http\Controllers\DigitalStorageHandover;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\QueryAPI;
use Carbon\Carbon;
use App\Jobs\ProcessZipJournalJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class JournalUploadController extends Controller
{
   public function index()
    {
        $sql = "
            SELECT *
            FROM (
                SELECT *
                FROM e_zip_upload_history
                where created_by = '" . session('username') . "'
                ORDER BY id DESC
            )
            WHERE ROWNUM <= 20
        ";
        $histories = QueryAPI::get($sql);
        //Log::info($sql);
        return view('layouts.index', [
            'data' => [
                'histories' => $histories,
                'content' => 'digital-storage-handover.journal-upload',
                'plugins' => [
                    'datatable',
                    'daterangepicker',
                    'select2',
                ]
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pelaksana_serah_id' => 'required',
            'zip_file' => 'required|file|mimes:zip',
        ]);
        
        $zipFile = $request->file('zip_file');
        if (!$zipFile || !$zipFile->isValid()) {
            return response()->json([
                'message' => 'File ZIP tidak valid atau gagal upload'
            ], 422);
        }

        $destination = storage_path('app/tmp_zip_upload');

        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }

        $filename = time() . '_' . $zipFile->getClientOriginalName();

        $zipFile->move($destination, $filename);
        $history = QueryAPI::create('e_zip_upload_history', [
            'penerbit_id' => $request->pelaksana_serah_id,
            'zip_name' => $zipFile->getClientOriginalName(),
            'zip_path' => $destination . '/' . $filename,
            'status' => 'queued',
            'total_rows' => 0,
            'processed_rows' => 0,
            'success_rows' => 0,
            'failed_rows' => 0,
            'notes' => 'Menunggu proses upload zip',
            'created_by' => session('username'),
        ], true);

        if (!$history || !isset($history->ID)) {
            return response()->json([
                'message' => 'Gagal menyimpan histori upload'
            ], 500);
        }

        ProcessZipJournalJob::dispatch($history->ID, [
            'id' => session('id'),
            'username' => session('username'),
            'fullname' => session('fullname'),
        ])->onQueue('zip');;

        return response()->json([
            'message' => 'ZIP berhasil diupload dan masuk antrian proses',
            'history_id' => $history->ID,
            'progress_url' => route('journal.zip.progress', $history->ID),
            'detail_url' => route('journal.zip.show', $history->ID)
        ]);
    }

    public function progress($id)
    {
        $key = "zip_upload:{$id}:summary";

        $summary = Redis::get($key);
        $summary = $summary ? json_decode($summary, true) : null;

        if (!$summary) {
            $history = QueryAPI::get("
                SELECT *
                FROM e_zip_upload_history
                WHERE id = {$id}
            ", true);

            return response()->json([
                'status' => $history->STATUS ?? 'unknown',
                'total_rows' => (int) ($history->TOTAL_ROWS ?? 0),
                'processed_rows' => (int) ($history->PROCESSED_ROWS ?? 0),
                'success_rows' => (int) ($history->SUCCESS_ROWS ?? 0),
                'failed_rows' => (int) ($history->FAILED_ROWS ?? 0),
                'notes' => $history->NOTES ?? null,
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
            SELECT *
            FROM e_zip_upload_history
            WHERE id = {$id}
        ", true);
        return view('layouts.index', [
            'data' => [
                'content' => 'digital-storage-handover.journal-upload-show',
                'plugins' => [
                    'datatable',
                ],
                'id' => $id,
                'history' => $history
            ],
            
        ]);
    }

    public function datatableShow(Request $request, $id)
    {
        $column = [
            'row_number_upload',
            'title',
            'file_name',
            'status',
            'message',
            'E_COLLECTION_ID',
            'created_at',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 10);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = "";
        $whereCondition[] = "";
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
            $whereClause = "where zip_upload_history_id = '{$id}' " . implode(' and ', $whereCondition);
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
                e_zip_upload_history_detail zh
            where
                zip_upload_history_id = '{$id}'
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                e_zip_upload_history_detail zh
            $whereClause
        ", true)->TOTAL ?? 0;
        $sql = "
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
                                e_zip_upload_history_detail zh
                            
                            $whereClause
                            $orderBy
                        ) data
                    where
                        rownum <= $length
                )
            where
                rnum > $start
        ";
        //Log::info($sql);
        $queryData = QueryAPI::get($sql);
        
        if ($queryData) {
            foreach ($queryData as $val) {
                $data[] = [
                    $val->ROW_NUMBER_UPLOAD,
                    $val->TITLE,
                    $val->FILE_NAME,
                    $val->STATUS,
                    $val->MESSAGE,
                    $val->E_COLLECTION_ID,
                    Carbon::parse($val->CREATED_AT)->isoFormat('dddd, D MMMM Y')
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

    public function progressRealtime($id)
    {
        $pattern = "zip_upload:{$id}:row:*";
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
                'title' => $item['title'] ?? null,
                'file_name' => $item['file_name'] ?? null,
                'message' => $item['message'] ?? null,
                'e_collection_id' => $item['e_collection_id'] ?? null,
                'updated_at' => $item['updated_at'] ?? null,
            ];
        }

        usort($rows, function ($a, $b) {
            return $a['row_number_upload'] <=> $b['row_number_upload'];
        });

        $total = count($rows);
        $running = count(array_filter($rows, fn ($r) => $r['status'] === 'running'));
        $success = count(array_filter($rows, fn ($r) => $r['status'] === 'success'));
        $failed = count(array_filter($rows, fn ($r) => $r['status'] === 'failed'));
        $pending = count(array_filter($rows, fn ($r) => $r['status'] === 'pending'));

        $processed = $success + $failed;
        $percent = $total > 0 ? round(($processed / $total) * 100, 2) : 0;

        $latestRow = null;
        if (!empty($rows)) {
            usort($rows, function ($a, $b) {
                return strcmp($b['updated_at'] ?? '', $a['updated_at'] ?? '');
            });
            $latestRow = $rows[0];
        }

        return response()->json([
            'summary' => [
                'total_rows_in_redis' => $total,
                'processed_rows' => $processed,
                'success_rows' => $success,
                'failed_rows' => $failed,
                'running_rows' => $running,
                'pending_rows' => $pending,
                'percent' => $percent,
                'latest_row' => $latestRow,
            ],
            'rows' => $rows,
        ]);
    }

}
