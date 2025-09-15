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
        $search = Str::upper($request->search);

        $data = QueryAPI::get("
            select
                *
            from (
                    select
                        *
                    from
                        propinsi
                    where
                        upper(namapropinsi) like '%$search%'
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
        $search = Str::upper($request->search);

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
                        upper(kabupaten.namakab) like '%$search%'
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
        $search = Str::upper($request->search);

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
                        upper(kecamatan.namakec) like '%$search%'
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
        $condition = [];

        $whereClause = '';
        $search = Str::upper($request->search);
        $provinceId = $request->province_id;

        $condition[] = "upper(branchs.name) like '%$search%'";
        $condition[] = "(branchs.isdelete = 0 or branchs.isdelete is null)";

        if ($provinceId) {
            $condition[] = "propinsi.id = $provinceId";
        }

        if ($condition) {
            $whereClause = "where " . implode(' and ', $condition);
        }

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
                    $whereClause
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

    public function executor(Request $request)
    {
        $whereClause = '';
        $provinceId = $request->province_id ?? null;
        $search = Str::upper($request->search);

        $response = [];
        $condition = ["upper(penerbit.name) like '%$search%'"];

        if ($provinceId) {
            $condition[] = "propinsi.id = $provinceId";
        }

        if ($condition) {
            $whereClause = "where " . implode(' and ', $condition);
        }

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
                    $whereClause
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
        $condition = [];

        $for = $request->for ?? 'village';
        $provinceId = $request->province_id ?? null;
        $search = Str::upper($request->search);

        if ($for == 'province') {
            $condition[] = "upper(namapropinsi) like '%$search%'";

            if ($provinceId) {
                $condition[] = "id = $provinceId";
            }

            $whereClause = "where " . implode(' and ', $condition);

            $data = QueryAPI::get("
                select
                    *
                from (
                        select
                            *
                        from
                            propinsi
                        $whereClause
                        order by
                            namapropinsi asc
                    )
                where
                    rownum <= 20
            ");
        } else if ($for == 'city') {
            $condition[] = "upper(kabupaten.namakab) like '%$search%'";

            if ($provinceId) {
                $condition[] = "propinsi.id = $provinceId";
            }

            $whereClause = "where " . implode(' and ', $condition);

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
                        $whereClause
                        order by
                            kabupaten.namakab asc
                    )
                where
                    rownum <= 20
            ");
        } else if ($for == 'district') {
            $condition[] = "upper(kecamatan.namakec) like '%$search%'";

            if ($provinceId) {
                $condition[] = "propinsi.id = $provinceId";
            }

            $whereClause = "where " . implode(' and ', $condition);

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
                        $whereClause
                        order by
                            kecamatan.namakec asc
                    )
                where
                    rownum <= 20
            ");
        } else if ($for == 'village') {
            $condition[] = "upper(kelurahan.namakel) like '%$search%'";

            if ($provinceId) {
                $condition[] = "propinsi.id = $provinceId";
            }

            $whereClause = "where " . implode(' and ', $condition);

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
                        $whereClause
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
        $search = Str::upper($request->search);

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
                            upper(e_collections.title_ori) like '%$search%' OR
                            upper(e_collections.title) like '%$search%'
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
