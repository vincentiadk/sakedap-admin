<?php

namespace App\Http\Controllers\Publisher;

use Illuminate\Http\Request;
use App\Models\CollectionRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class DownloadFileOriginal extends Controller
{

    public function download(Request $request, $id)
    {
        if ($request->token == null) {
            return response()->json(['message' => 'Link download tidak valid!']);
        }

        $collection = CollectionRequest::where('collection_id', $id)
            ->where('token_download', $request->token)
            ->whereNotNull('approved_by')
            ->where('status', 2)
            ->orderBy('expired_at', 'desc')
            ->first();

        if ($collection == null) {
            return response()->json(['message' => 'Link download tidak valid!']);
        }

        $current_time = strtotime(date('Y-m-d H:i'));
        $schedule_time = strtotime(date('Y-m-d H:i', strtotime($collection->expired_at)));

        $diff = $schedule_time - $current_time;
        $minutes = floor($diff / 60);

        if ($minutes < 0) {
            return response()->json(['message' => 'Link sudah kadaluarsa!']);
        }

        if ($collection->type == 1 || $collection->type == 2 || $collection->type == 3 || $collection->type == 4) {
            $media = $collection->collectionMedia->where('type', 2)->first();
        } else if ($collection->type == 5) {
            $media = $collection->collectionMedia->where('type', 2)->first();
        } else if ($collection->type == 6) {
            $media = $collection->collectionMedia->where('type', 1)->first();
        } else {
            $media = null;
        }

        // $media = CollectionMedia::where('collection_id', $id)
        //             ->where('type', 'Original')
        //             ->first();

        if ($media == null) {
            return response()->json(['message' => 'Link download tidak valid!']);
        }


        $collection->update(['count_download' => $collection->count_download + 1]);

        return Storage::disk($media->location->location)->download($media->link, $collection->collection->slug . '.' . $media->extension);
    }
}
