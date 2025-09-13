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
                abort(404, 'File laporan belum siap atau tidak ditemukan.');
            }

            if (!Storage::exists($filename)) {
                abort(404, 'File tidak ditemukan di server.');
            }

            return Storage::download($filename);
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

            if ($types == 'report-periodic') {
                $typeText = 'Laporan Periodik';
            } else if ($types == 'report-manager') {
                $typeText = 'Laporan Pengelola';
            } else if ($types == 'report-collection') {
                $typeText = 'Laporan Koleksi';
            } else if ($types == 'report-performance-user') {
                $typeText = 'Laporan Performa User';
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

        $data = [
            'result' => $result,
            'content' => 'report.download'
        ];

        return view('layouts.index', ['data' => $data]);
    }
}
