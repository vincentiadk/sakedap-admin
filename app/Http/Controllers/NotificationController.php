<?php

namespace App\Http\Controllers;

use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function index()
    {
        if (Main::isNotCenterBranch()) {
            $provinceId = 'penerbit.province_id = ' . session('id');
        } else {
            $provinceId = 'penerbit.province_id is not null';
        }

        $dataExecutor = QueryAPI::get("
            select
                penerbit.updatedate,
                penerbit.name,
                propinsi.namapropinsi
            from
                penerbit
            left join
                propinsi on propinsi.id = penerbit.province_id
            where
                penerbit.status = '1' and
                $provinceId
            order by
                penerbit.updatedate desc
        ");

        $dataFile = QueryAPI::get("
            select
                penerbit.name,
                catalogs.title,
                e_collection_requests.created_at
            from
                e_collection_requests
            left join
                catalogs on catalogs.id = e_collection_requests.catalog_id
            left join
                penerbit on penerbit.id = catalogs.penerbit_id
            left join
                kabupaten on kabupaten.id = catalogs.city_id
            where
                e_collection_requests.status = 1 and
                $provinceId
            order by
                e_collection_requests.created_at desc
        ");

        return response()->json([
            'executor' => $dataExecutor ?? [],
            'file' => $dataFile ?? [],
        ]);
    }
}
