<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\CollectionMedia;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CollectionController extends Controller
{
    public function loadImagePdf(Request $request)
    {
        $data       = CollectionMedia::where('collection_id', $request->collection_id)->where('type', 3)->orderBy('created_at', 'DESC')->first();
        $file       = $data ? $data->jsonParse() : null;
        $total_file = 0;
        $image      = '';

        if ($file) {
            $image = $file[(int)$request->key - 1];
        }

        return response()->json([
            'image'      => $image,
            'total_data' => count($file)
        ]);
    }

    public function streamPdf($id)
    {
        $data = CollectionMedia::where('collection_id', $id)->where('type', 2)->first();
        if ($data) {
            header('Content-Type: application/pdf');
            header('Content-Transfer-Encoding: binary');
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');
            header('Content-Ranges: bytes');

            readfile(asset(Storage::disk($data->location->location)->url($data->link)));
        } else {
            echo '<script>alert("File tidak ditemukan")</script>';
            return redirect()->back();
        }
    }

    public function resetFilter($type, $id)
    {
        session()->forget('filter.collection.' . $type . '.' . $id);
    }
}
