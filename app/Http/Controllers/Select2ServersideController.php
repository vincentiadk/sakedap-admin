<?php

namespace App\Http\Controllers;

use App\Helpers\QueryAPI;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Select2ServersideController extends Controller
{
    public function province(Request $request)
    {
        $response = [];
        $search = Str::headline($request->search);

        $data = QueryAPI::get("
            select
                *
            from
                propinsi
            where
                namapropinsi like '%$search%'
            order by
                namapropinsi asc
        ");

        if ($data) {
            foreach ($data as $d) {
                $html = '
                    <div>' . $d->NAMAPROPINSI . '</div>
                ';

                $response[] = [
                    'id' => $d->ID,
                    'text' => $d->NAMAPROPINSI,
                    'html' => $html,
                ];
            }
        }

        return response()->json($response);
    }

    public function city(Request $request)
    {
        $response = [];
        $search = Str::headline($request->search);

        $data = QueryAPI::get("
            select
                kabupaten.*,
                propinsi.namapropinsi as namapropinsi
            from
                kabupaten
            join
                propinsi on propinsi.id = kabupaten.propinsiid
            where
                kabupaten.namakab like '%$search%'
            order by
                kabupaten.namakab asc
        ");

        if ($data) {
            foreach ($data as $d) {
                $html = '
                    <div><small class="text-muted">' . ($d->NAMAPROPINSI ?? '-') . '</small></div>
                    <div>' . $d->NAMAKAB . '</div>
                ';

                $response[] = [
                    'id' => $d->ID,
                    'text' => $d->NAMAKAB,
                    'html' => $html,
                ];
            }
        }

        return response()->json($response);
    }

    public function district(Request $request)
    {
        $response = [];
        $search = Str::headline($request->search);

        $data = QueryAPI::get("
            select
                kecamatan.*,
                kabupaten.namakab as namakab,
                propinsi.namapropinsi as namapropinsi
            from
                kecamatan
            join
                kabupaten on kabupaten.id = kecamatan.kabupatenid
            join
                propinsi on propinsi.id = kabupaten.propinsiid
            where
                kecamatan.namakec like '%$search%'
            order by
                kecamatan.namakec asc
        ");

        if ($data) {
            foreach ($data as $d) {
                $html = '
                    <div><small class="text-muted">' . ($d->NAMAPROPINSI ?? '-') . '</small></div>
                    <div><small class="text-muted">' . ($d->NAMAKAB ?? '-') . '</small></div>
                    <div>' . $d->NAMAKEC . '</div>
                ';

                $response[] = [
                    'id' => $d->ID,
                    'text' => $d->NAMAKEC,
                    'html' => $html,
                ];
            }
        }

        return response()->json($response);
    }
}
