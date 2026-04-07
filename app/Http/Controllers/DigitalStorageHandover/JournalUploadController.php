<?php

namespace App\Http\Controllers\DigitalStorageHandover;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\QueryAPI;
use App\Jobs\ProcessZipJournalJob;

class JournalUploadController extends Controller
{
   public function index()
    {
        $histories = QueryAPI::get("
            SELECT *
            FROM (
                SELECT *
                FROM zip_upload_history
                ORDER BY id DESC
            )
            WHERE ROWNUM <= 20
        ");
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
        $storedPath = $zipFile->store('tmp_zip_upload');

        $history = QueryAPI::create('e_zip_upload_history', [
            'penerbit_id' => $request->pelaksana_serah_id,
            'zip_name' => $zipFile->getClientOriginalName(),
            'zip_path' => $storedPath,
            'status' => 'queued',
            'total_rows' => 0,
            'processed_rows' => 0,
            'success_rows' => 0,
            'failed_rows' => 0,
            'notes' => 'Menunggu proses upload zip',
            'created_by' => session('username'),
        ]);

        if (!$history || !isset($history->id)) {
            return response()->json([
                'message' => 'Gagal menyimpan histori upload'
            ], 500);
        }

        ProcessZipJournalJob::dispatch($history->id);

        return response()->json([
            'message' => 'ZIP berhasil diupload dan masuk antrian proses',
            'history_id' => $history->id,
            'progress_url' => route('journal.zip.progress', $history->id),
        ]);
    }

    public function progress($id)
    {
        $history = QueryAPI::get("
            SELECT *
            FROM e_zip_upload_history
            WHERE id = {$id}
        ", true);

        if (!$history) {
            return response()->json(['message' => 'Histori tidak ditemukan'], 404);
        }

        $percent = 0;
        if ((int)$history->TOTAL_ROWS > 0) {
            $percent = round(($history->PROCESSED_ROWS / $history->TOTAL_ROWS) * 100);
        }

        return response()->json([
            'status' => $history->STATUS,
            'total_rows' => (int)$history->TOTAL_ROWS,
            'processed_rows' => (int)$history->PROCESSED_ROWS,
            'success_rows' => (int)$history->SUCCESS_ROWS,
            'failed_rows' => (int)$history->FAILED_ROWS,
            'notes' => $history->NOTES,
            'percent' => $percent,
        ]);
    }

    public function show($id)
    {
        $history = QueryAPI::get("
            SELECT *
            FROM e_zip_upload_history
            WHERE id = {$id}
        ", true);

        $details = QueryAPI::get("
            SELECT *
            FROM e_zip_upload_history_detail
            WHERE zip_upload_history_id = {$id}
            ORDER BY row_number_upload ASC
        ");

        return view('digital-storage-handover.journal-upload-show', compact('history', 'details'));
    }
}
