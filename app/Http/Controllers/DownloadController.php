<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DownloadController extends Controller
{
    public function fromPublic(Request $request)
    {
        $path = $request->path ?? '';
        $pathDownload = public_path($path);

        return response()->download($pathDownload);
    }
}
