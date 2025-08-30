<?php

namespace App\Helpers;

class Main
{
    /**
     * locationById
     *
     * @param  mixed $id
     * @param  mixed $for
     * @return void
     */
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

    /**
     * contentTypeFile
     *
     * @param  mixed $filename
     * @return void
     */
    public static function contentTypeFile($filename)
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        if ($extension == 'pdf') {
            $content = 'application/pdf';
        } else if (in_array($extension, ['jpg', 'jpeg'])) {
            $content = 'image/jpeg';
        } else if ($extension == 'png') {
            $content = 'image/png';
        } else if ($extension == 'docx') {
            $content = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        } else if ($extension == 'doc') {
            $content = 'application/msword';
        } else if ($extension == 'xlsx') {
            $content = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        } else if ($extension == 'xls') {
            $content = 'application/vnd.ms-excel';
        } else if ($extension == 'epub') {
            $content = 'application/epub+zip';
        } else if ($extension == 'mp3') {
            $content = 'audio/mpeg';
        } else if ($extension == 'mp4') {
            $content = 'video/mp4';
        } else if ($extension == 'wav') {
            $content = 'audio/wav';
        } else if ($extension == 'zip') {
            $content = 'application/zip';
        } else if ($extension == 'rar') {
            $content = 'application/vnd.rar';
        } else {
            $content = 'application/octet-stream';
        }

        return $content;
    }
}
