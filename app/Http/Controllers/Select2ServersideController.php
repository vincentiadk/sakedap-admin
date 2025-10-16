<?php

namespace App\Http\Controllers;

use App\Helpers\QueryAPI;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Select2ServersideController extends Controller
{
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
                        branchs.id,
                        branchs.name,
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
                    <div>' . $d->NAME . '</div>
                    <div class="fw-light fs-12 text-muted">Provinsi : ' . ($d->NAMAPROPINSI ?? '-') . '</div>
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
        $condition = ["(upper(penerbit.name) like '%$search%' or penerbit.id like '%$search%')"];

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
                        penerbit.id,
                        penerbit.name,
                        penerbit.email1,
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
                    <div>' . $d->NAME . '</div>
                    <div class="fw-light fs-12 text-muted">ID : ' . ($d->ID ?? '-') . '</div>
                    <div class="fw-light fs-12 text-muted">Email : ' . ($d->EMAIL1 ?? '-') . '</div>
                    <div class="fw-light fs-12 text-muted">Provinsi : ' . ($d->NAMAPROPINSI ?? '-') . '</div>
                ';

                $response[] = [
                    'id' => $d->ID,
                    'text' => $d->ID . ' | ' . $d->NAME,
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
                            id,
                            namapropinsi
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
                            kabupaten.id,
                            kabupaten.namakab,
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
                            kecamatan.id,
                            kecamatan.namakec,
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
                            kelurahan.id,
                            kelurahan.namakel,
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
                        <div>' . $d->NAMAKAB . '</div>
                        <div class="fw-light fs-12 text-muted">Provinsi : ' . ($d->NAMAPROPINSI ?? '-') . '</div>
                    ';
                } else if ($for == 'district') {
                    $text = $d->NAMAPROPINSI . ' -> ' . $d->NAMAKAB . ' -> ' . $d->NAMAKEC;

                    $html = '
                        <div>' . $d->NAMAKEC . '</div>
                        <div class="fw-light fs-12 text-muted">Kota / Kabupaten : ' . ($d->NAMAKAB ?? '-') . '</div>
                        <div class="fw-light fs-12 text-muted">Provinsi : ' . ($d->NAMAPROPINSI ?? '-') . '</div>
                    ';
                } else if ($for == 'village') {
                    $text = $d->NAMAPROPINSI . ' -> ' . $d->NAMAKAB . ' -> ' . $d->NAMAKEC . ' -> ' . $d->NAMAKEL;

                    $html = '
                        <div>' . $d->NAMAKEL . '</div>
                        <div class="fw-light fs-12 text-muted">Kecamatan : ' . ($d->NAMAKEC ?? '-') . '</div>
                        <div class="fw-light fs-12 text-muted">Kota / Kabupaten : ' . ($d->NAMAKAB ?? '-') . '</div>
                        <div class="fw-light fs-12 text-muted">Provinsi : ' . ($d->NAMAPROPINSI ?? '-') . '</div>
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
                        e_collections.id,
                        e_collections.title,
                        e_collections.title_ori,
                        e_collections.code,
                        e_collections.author,
                        e_collections.publication_year,
                        penerbit.name as name_penerbit
                    from
                        e_collections
                    join
                        penerbit on penerbit.id = e_collections.penerbit_id
                    where
                        e_collections.deleted_at is null and
                        (
                            e_collections.parent_id is null or
                            e_collections.parent_id = 0
                        ) and
                        (
                            upper(e_collections.title_ori) like '%$search%' or
                            upper(e_collections.title) like '%$search%'
                        )
                )
            where
                rownum <= 20
        ");

        if ($data) {
            foreach ($data as $d) {
                $html = '
                    <div>' . ($d->TITLE ?? $d->TITLE_ORI) . '</div>
                    <div class="fw-light fs-12 text-muted">ID : ' . ($d->ID ?? '-') . '</div>
                    <div class="fw-light fs-12 text-muted">Kode : ' . ($d->CODE ?? '-') . '</div>
                    <div class="fw-light fs-12 text-muted">Tahun Terbit : ' . ($d->PUBLICATION_YEAR ?? '-') . '</div>
                    <div class="fw-light fs-12 text-muted">Pelaksana Serah : ' . ($d->NAME_PENERBIT ?? '-') . '</div>
                    <div class="fw-light fs-12 text-muted">Kepeng : ' . str_replace(';', ', ', ($d->AUTHOR ?? '-')) . '</div>
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

    public function problem(Request $request)
    {
        $response = [];
        $search = Str::upper($request->search);

        $data = QueryAPI::get("
            select
                id,
                name
            from
                e_problems
            where
                upper(name) like '%$search%'
        ");

        if ($data) {
            foreach ($data as $d) {
                $html = '
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

    public function catalog(Request $request)
    {
        $response = [];
        $search = Str::upper($request->search);
        $provinceId = $request->province_id;
        $placeholder = $request->placeholder ?? null;

        $condition[] = "(c.isdelete = 0 or c.isdelete is null)";
        $condition[] = "upper(c.title) like '%$search%'";

        if ($provinceId) {
            $condition[] = "k.propinsiid = $provinceId";
        }

        $whereClause = "where " . implode(' and ', $condition);

        $data = QueryAPI::get("
            select
                rnum,
                c.id,
                c.bibid,
                c.title,
                c.author,
                c.publishyear,
                c.isbn,
                c.callnumber,
                p.name as name_penerbit,
                (
                    select
                        count(cl.id)
                    from
                        collections cl
                    where
                        cl.catalog_id = c.id
                ) as total_collection
            from (
                select
                    rownum as rnum,
                    t.*
                from (
                    select
                        c.id,
                        c.bibid,
                        c.title,
                        c.author,
                        c.publishyear,
                        c.isbn,
                        c.callnumber,
                        c.penerbit_id,
                        c.city_id,
                        c.worksheet_id
                    from
                        catalogs c
                    left join
                        penerbit p on p.id = c.penerbit_id
                    left join
                        kabupaten k on k.id = c.city_id
                    left join
                        worksheets w on w.id = c.worksheet_id
                    $whereClause
                ) t
                where
                    rownum <= 20
            ) c
            left join
                penerbit p on p.id = c.penerbit_id
        ");

        if ($data) {
            foreach ($data as $d) {
                $DTitle = $d->TITLE ?? '-';
                $cleanedTitle = preg_replace('/[\x00-\x1F\x7F]/', '', $DTitle);
                $cleanedTitle = str_replace("\u{200B}", '', $cleanedTitle);
                $title = strip_tags($cleanedTitle);

                $html = '
                    <div>' . ($title) . '</div>
                    <div class="fw-light fs-12 text-muted">ID : ' . ($d->ID ?? '-') . '</div>
                    <div class="fw-light fs-12 text-muted">BIBID : ' . ($d->BIBID ?? '-') . '</div>
                    <div class="fw-light fs-12 text-muted">Kode : ' . ($d->ISBN ?? '-') . '</div>
                    <div class="fw-light fs-12 text-muted">Tahun Terbit : ' . ($d->PUBLISHYEAR ?? '-') . '</div>
                    <div class="fw-light fs-12 text-muted">Nomor Panggil : ' . ($d->CALLNUMBER ?? '-') . '</div>
                    <div class="fw-light fs-12 text-muted">Jumlah Koleksi : ' . ($c->TOTAL_COLLECTION ?? 0) . '</div>
                    <div class="fw-light fs-12 text-muted">Pelaksana Serah : ' . ($d->NAME_PENERBIT ?? '-') . '</div>
                    <div class="fw-light fs-12 text-muted">Kepeng : ' . str_replace(';', ', ', ($d->AUTHOR ?? '-')) . '</div>
                ';

                $response[] = [
                    'id' => $d->ID,
                    'text' => $placeholder == 'id' ? $d->ID : $title,
                    'html' => $html,
                ];
            }
        }

        return response()->json($response);
    }

    public function currency(Request $request)
    {
        $response = [];
        $search = Str::upper($request->search);

        $data = QueryAPI::get("
            select
                currency,
                description
            from
                master_currency
            where
                upper(currency) like '%$search%' or
                upper(description) like '%$search%'
            group by
                currency,
                description
        ");

        if ($data) {
            foreach ($data as $d) {
                $html = '
                    <div>' . ($d->CURRENCY ?? '-') . '</div>
                    <div class="fw-light fs-12 text-muted">Keterangan : ' . ($d->DESCRIPTION ?? '-') . '</div>
                ';

                $response[] = [
                    'id' => $d->CURRENCY,
                    'text' => $d->CURRENCY,
                    'html' => $html,
                ];
            }
        }

        return response()->json($response);
    }

    public function promotion(Request $request)
    {
        $response = [];
        $search = Str::upper($request->search);
        $provinceId = $request->province_id;
        $condition[] = "
            (
                upper(kode_promo) like '%$search%' or
                upper(judul) like '%$search%'
            )
        ";

        if ($provinceId) {
            $condition[] = "
                (
                    ',' || province_id || ',' LIKE '%," . $provinceId . ",%' or
                    province_id is null
                )
            ";
        }

        $whereClause = "where " . implode(' and ', $condition);

        $data = QueryAPI::get("
            select
                id,
                judul,
                kode_promo
            from
                e_promo
            $whereClause
        ");

        if ($data) {
            foreach ($data as $d) {
                $html = '
                    <div>' . ($d->JUDUL ?? '-') . '</div>
                    <div class="fw-light fs-12 text-muted">Kode : ' . ($d->KODE_PROMO ?? '-') . '</div>
                ';

                $response[] = [
                    'id' => $d->ID,
                    'text' => $d->JUDUL,
                    'html' => $html,
                ];
            }
        }

        return response()->json($response);
    }

    public function newsCategory(Request $request)
    {
        $response = [];
        $search = Str::upper($request->search);
        $condition[] = "(upper(name) like '%$search%')";
        $whereClause = "where " . implode(' and ', $condition);

        $data = QueryAPI::get("
            select
                id,
                parent_id,
                name,
                pages,
                level,
                rpad(' ', (level - 1) * 2) || name as tree_view,
                ltrim(sys_connect_by_path(name, ' > '), ' > ') as tree_path
            from
                e_news_kategori
            $whereClause
            start with
                parent_id is null
            connect by prior
                id = parent_id
            order siblings by
                name
        ");

        if ($data) {
            foreach ($data as $d) {
                $html = '
                    <div>' . ($d->TREE_PATH ?? '-') . '</div>
                ';

                $response[] = [
                    'id' => $d->ID,
                    'text' => $d->TREE_PATH,
                    'html' => $html,
                ];
            }
        }

        return response()->json($response);
    }

    public function news(Request $request)
    {
        $response = [];
        $search = Str::upper($request->search);
        $condition[] = "upper(title) like '%$search%'";
        $whereClause = "where " . implode(' and ', $condition);

        $data = QueryAPI::get("
            select
                id,
                title
            from
                e_news
            $whereClause
        ");

        if ($data) {
            foreach ($data as $d) {
                $html = '
                    <div>' . ($d->TITLE ?? '-') . '</div>
                ';

                $response[] = [
                    'id' => $d->ID,
                    'text' => $d->TITLE,
                    'html' => $html,
                ];
            }
        }

        return response()->json($response);
    }
}
