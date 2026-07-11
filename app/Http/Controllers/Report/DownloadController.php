<?php

namespace App\Http\Controllers\Report;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function index(Request $request)
    {
        if ($request->downloaded) {
            $param = $request->param;
            $status = Redis::hget('download:' . $param, 'status');
            $filename = Redis::hget('download:' . $param, 'filename');

            if ($status !== 'completed' || empty($filename)) {
                return redirect('report/download')->with('error', 'File belum siap, silakan tunggu dan coba lagi.');
            }

            $path = Redis::hget('download:' . $param, 'path');

            if ($path && Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->download($path, $filename);
            } elseif ($filename && Storage::disk('public')->exists($filename)) {
                return Storage::disk('public')->download($filename);
            } elseif ($filename && Storage::exists($filename)) {
                return Storage::download($filename);
            }

            abort(404, 'File tidak ditemukan di server.');
        }

        $userId = session('id');
        $userKey = "user:$userId:download";
        $listData = Redis::lrange($userKey, 0, -1);
        $result = [];

        foreach ($listData as $ld) {
            $status = Redis::hget('download:' . $ld, 'status');
            $filename = Redis::hget('download:' . $ld, 'filename');
            $date = Redis::hget('download:' . $ld, 'date');
            $type = Redis::hget('download:' . $ld, 'type');
            $types = $type ?? null;

            if ($types == 'report-manage') {
                $typeText = 'Laporan Pengelolaan';
            } else if ($types == 'report-promotion') {
                $typeText = 'Laporan Promosi';
            } else if ($types == 'report-service') {
                $typeText = 'Laporan Pelayanan';
            } else if ($types == 'compliance-v3') {
                $typeText = 'Kepatuhan KCKR';
            } else {
                $typeText = 'N/A';
            }

            $result[] = [
                'job_id' => $ld,
                'status' => $status ?? 'pending',
                'filename' => $filename,
                'type' => $typeText,
                'date' => Carbon::parse($date)->isoFormat('D MMMM Y'),
                'time' => Carbon::parse($date)->format('H:i') . ' WIB',
            ];
        }

        return view('layouts.index', [
            'data' => [
                'result' => $result,
                'content' => 'report.download',
                'plugins' => [
                    'datatable',
                ]
            ]
        ]);
    }
}
