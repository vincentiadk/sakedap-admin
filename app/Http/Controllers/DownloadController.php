<?php

namespace App\Http\Controllers;

use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

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

        if (!$catalogId || !$token) {
            return abort(404);
        }

        $requestData = QueryAPI::get("
            select
                er.id,
                er.count_download,
                er.expired_at,
                cf.fileurl
            from
                e_collection_requests er
            left join
                catalogfiles cf ON cf.catalog_id = er.catalog_id
            where
                er.catalog_id = $catalogId and
                er.token_download = '$token' and
                er.approved_by IS NOT NULL and
                er.status = 2
            order by
                er.expired_at DESC
        ", true);

        if ($requestData && time() < strtotime($requestData->EXPIRED_AT)) {
            QueryAPI::update('e_collection_requests', $requestData->ID, [
                'count_download' => $requestData->COUNT_DOWNLOAD + 1
            ]);

            if ($requestData->FILEURL) {
                return redirect('stream-file?type=konten_digital&id=' . $catalogId . '&filename=' . $requestData->FILEURL);
            }
        }

        echo '
            <script>
                alert("Link unduhan tidak valid atau telah kadaluwarsa, silakan buat permintaan baru!");
                window.close();
            </script>
        ';
    }

    public function fromStorage(Request $request)
    {
        $path = $request->path ?? '';

        if (Storage::exists($path)) {
            return Storage::download($path);
        }

        abort(404);
    }
}
