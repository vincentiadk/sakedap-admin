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
                namapropinsi like '%$search%' OR
                code like '%$search%'
            order by
                namapropinsi asc
        ");

        if ($data) {
            foreach ($data as $d) {
                $html = '
                    <small class="text-muted">' . ($d->CODE ?? '-') . '</small>
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
}
