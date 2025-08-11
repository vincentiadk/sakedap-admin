<?php

namespace App\Http\Controllers\Admin;

use App\Models\CollectionMedia;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class FileController extends Controller
{
    public function get_file($id)
    {
        $file = CollectionMedia::find($id);
        if (Storage::disk($file->location->location)->exists($file->link)) {
            $getFile =  Storage::disk($file->location->location)->get($file->link);
            $mimeType = Storage::disk($file->location->location)->mimeType($file->link);
            $response = Response::make($getFile, 200);
            $response->header('Content-Type', $mimeType);
            // dd($file->location);
            // dd(\Storage::disk($file->file_location->location)->get(str_replace('/public','','/public/pdf/4224/2020/9/11/856acdbb-f4b7-409a-80af-da232bc39b3e.pdf')));
            return $response;
        } else {
            return [];
        }
    }
}
