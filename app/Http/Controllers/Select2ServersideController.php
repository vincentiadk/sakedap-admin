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
            from (
                    select
                        *
                    from
                        propinsi
                    where
                        namapropinsi like '%$search%'
                    order by
                        namapropinsi asc
                )
            where
                rownum <= 20
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
                *
            from (
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
                )
            where
                rownum <= 20
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
                *
            from (
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
                )
            where
                rownum <= 20
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

    public function branch(Request $request)
    {
        $response = [];
        $search = Str::headline($request->search);

        $data = QueryAPI::get("
            select
                *
            from (
                    select
                        branchs.*,
                        propinsi.namapropinsi as namapropinsi
                    from
                        branchs
                    join
                        propinsi on propinsi.id = branchs.province_id
                    where
                        branchs.name like '%$search%' and
                        branchs.isdelete != 1
                    order by
                        branchs.name asc
                )
            where
                rownum <= 20
        ");

        if ($data) {
            foreach ($data as $d) {
                $html = '
                    <div><small class="text-muted">' . ($d->NAMAPROPINSI ?? '-') . '</small></div>
                    <div>' . $d->NAME . '</div>
                ';

                $response[] = [
                    'id' => $d->ID,
                    'text' => $d->NAME,
                    'html' => $html,
                ];
            }
        }

        return response()->json($response);
    }

    public function publisher(Request $request)
    {
        $response = [];
        $search = Str::headline($request->search);

        $data = QueryAPI::get("
            select
                *
            from (
                    select
                        penerbit.*,
                        propinsi.namapropinsi as namapropinsi
                    from
                        penerbit
                    join
                        propinsi on propinsi.id = penerbit.province_id
                    where
                        penerbit.name like '%$search%'
                    order by
                        penerbit.name asc
                )
            where
                rownum <= 20
        ");

        if ($data) {
            foreach ($data as $d) {
                $html = '
                    <div><small class="text-muted">' . ($d->NAMAPROPINSI ?? '-') . '</small></div>
                    <div>' . $d->NAME . '</div>
                ';

                $response[] = [
                    'id' => $d->ID,
                    'text' => $d->NAME,
                    'html' => $html,
                ];
            }
        }

        return response()->json($response);
    }

    public function location(Request $request)
    {
        $response = [];
        $data = [];

        $for = $request->for ?? 'village';
        $search = Str::headline($request->search);

        if ($for == 'province') {
            $data = QueryAPI::get("
                select
                    *
                from (
                        select
                            *
                        from
                            propinsi
                        where
                            namapropinsi like '%$search%'
                        order by
                            namapropinsi asc
                    )
                where
                    rownum <= 20
            ");
        } else if ($for == 'city') {
            $data = QueryAPI::get("
                select
                    *
                from (
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
                    )
                where
                    rownum <= 20
            ");
        } else if ($for == 'district') {
            $data = QueryAPI::get("
                select
                    *
                from (
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
                    )
                where
                    rownum <= 20
            ");
        } else if ($for == 'village') {
            $data = QueryAPI::get("
                select
                    *
                from (
                        select
                            kelurahan.*,
                            kecamatan.namakec as namakec,
                            kabupaten.namakab as namakab,
                            propinsi.namapropinsi as namapropinsi
                        from
                            kelurahan
                        join
                            kecamatan on kecamatan.id = kelurahan.kecamatanid
                        join
                            kabupaten on kabupaten.id = kecamatan.kabupatenid
                        join
                            propinsi on propinsi.id = kabupaten.propinsiid
                        where
                            kelurahan.namakel like '%$search%'
                        order by
                            kelurahan.namakel asc
                    )
                where
                    rownum <= 20
            ");
        }

        if ($data) {
            foreach ($data as $d) {
                if ($for == 'province') {
                    $text = $d->NAMAPROPINSI;

                    $html = '
                        <div>' . $d->NAMAPROPINSI . '</div>
                    ';
                } else if ($for == 'city') {
                    $text = $d->NAMAPROPINSI . ' -> ' . $d->NAMAKAB;

                    $html = '
                        <div><small class="text-muted">' . ($d->NAMAPROPINSI ?? '-') . '</small></div>
                        <div>' . $d->NAMAKAB . '</div>
                    ';
                } else if ($for == 'district') {
                    $text = $d->NAMAPROPINSI . ' -> ' . $d->NAMAKAB . ' -> ' . $d->NAMAKEC;

                    $html = '
                        <div><small class="text-muted">' . ($d->NAMAPROPINSI ?? '-') . '</small></div>
                        <div><small class="text-muted">' . ($d->NAMAKAB ?? '-') . '</small></div>
                        <div>' . $d->NAMAKEC . '</div>
                    ';
                } else if ($for == 'village') {
                    $text = $d->NAMAPROPINSI . ' -> ' . $d->NAMAKAB . ' -> ' . $d->NAMAKEC . ' -> ' . $d->NAMAKEL;

                    $html = '
                        <div><small class="text-muted">' . ($d->NAMAPROPINSI ?? '-') . '</small></div>
                        <div><small class="text-muted">' . ($d->NAMAKAB ?? '-') . '</small></div>
                        <div><small class="text-muted">' . ($d->NAMAKEC ?? '-') . '</small></div>
                        <div>' . $d->NAMAKEL . '</div>
                    ';
                }

                $response[] = [
                    'id' => $d->ID,
                    'text' => $text,
                    'html' => $html,
                ];
            }
        }

        return response()->json($response);
    }

    public function collectionParent(Request $request)
    {
        $response = [];
        $search = $request->search;

        $data = QueryAPI::get("
            select
                *
            from (
                    select
                        e_collections.*,
                        penerbit.name as name_penerbit
                    from
                        e_collections
                    join
                        penerbit on penerbit.id = e_collections.penerbit_id
                    where
                        e_collections.deleted_at is null
                        (
                            e_collections.parent_id is null OR
                            e_collections.parent_id = 0
                        ) AND
                        (
                            e_collections.title_ori like '%$search%' OR
                            e_collections.title like '%$search%'
                        )
                )
            where
                rownum <= 20
        ");

        if ($data) {
            foreach ($data as $d) {
                $html = '
                    <div><small class="text-muted">' . ($d->NAME_PENERBIT ?? '-') . '</small></div>
                    <div>' . ($d->TITLE ?? $d->TITLE_ORI) . '</div>
                ';

                $response[] = [
                    'id' => $d->ID,
                    'text' => $d->TITLE ?? $d->TITLE_ORI,
                    'html' => $html,
                ];
            }
        }

        return response()->json($response);
    }
}
