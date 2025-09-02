<?php

namespace App\Http\Controllers;

use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DownloadController extends Controller
{
    public function fromPublic(Request $request)
    {
        $path = $request->path ?? '';
        $pathDownload = public_path($path);

        return response()->download($pathDownload);
    }

    public function requestFile(Request $request)
    {
        $catalogId = (int) $request->param;
        $token = $request->token;

        if ($catalogId && $token) {
            $collectionRequest = QueryAPI::get("
                select
                    *
                from
                    e_collection_requests
                where
                    catalog_id = $catalogId and
                    token_download = '$token' and
                    approved_by is not null and
                    status = 2
                order by
                    expired_at desc
            ", true);

            if ($collectionRequest) {
                $currentTime = strtotime(date('Y-m-d H:i:s'));
                $scheduleTime = strtotime(date('Y-m-d H:i:s', strtotime($collectionRequest->EXPIRED_AT)));
                $diff = $scheduleTime - $currentTime;
                $minute = floor($diff / 60);

                if ($minute < 0) {
                    echo '
                        alert("Link download telah kadaluwarsa, silahkan melakukan permintaan kembali!!");
                        document.location.href = "https://edeposit.perpusnas.go.id";
                    ';
                }

                $catalogFile = QueryAPI::get("
                    select
                        *
                    from
                        catalogfiles
                    where
                        catalog_id = $catalogId
                ", true);

                if ($catalogFile) {
                    QueryAPI::update('e_collection_requests', [
                        'count_download' => $collectionRequest->COUNT_DOWNLOAD + 1
                    ]);

                    return redirect('stream-file?type=konten_digital&id=' . $catalogId . '&filename=' . $catalogFile->FILEURL);
                }
            }
        }

        abort(404);
    }
}
