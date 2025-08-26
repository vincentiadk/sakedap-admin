<?php

namespace App\Helpers;

class Main
{
    public static function locationById($id, $for)
    {
        $data = null;

        if ($for == 'province') {
            $data = QueryAPI::get("
                select
                    *
                from
                    propinsi
                where
                    id = $id
            ", true);
        } else if ($for == 'city') {
            $data = QueryAPI::get("
                select
                    kabupaten.*,
                    propinsi.namapropinsi as namapropinsi
                from
                    kabupaten
                join
                    propinsi on propinsi.id = kabupaten.propinsiid
                where
                    kabupaten.id = $id
            ", true);
        } else if ($for == 'district') {
            $data = QueryAPI::get("
                select
                    kecamatan.*,
                    kabupaten.namakab as namakab,
                    propinsi.namapropinsi as namapropinsi,
                    propinsi.id as propinsiid
                from
                    kecamatan
                join
                    kabupaten on kabupaten.id = kecamatan.kabupatenid
                join
                    propinsi on propinsi.id = kabupaten.propinsiid
                where
                    kecamatan.id = $id
            ", true);
        } else if ($for == 'village') {
            $data = QueryAPI::get("
                select
                    kelurahan.*,
                    kecamatan.namakec as namakec,
                    kabupaten.namakab as namakab,
                    kabupaten.id as kabupatenid,
                    propinsi.namapropinsi as namapropinsi,
                    propinsi.id as propinsiid
                from
                    kelurahan
                join
                    kecamatan on kecamatan.id = kelurahan.kecamatanid
                join
                    kabupaten on kabupaten.id = kecamatan.kabupatenid
                join
                    propinsi on propinsi.id = kabupaten.propinsiid
                where
                    kelurahan.id = $id
            ", true);
        }

        return $data;
    }
}
