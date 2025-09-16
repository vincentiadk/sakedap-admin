<?php

namespace App\Helpers;

use Carbon\Carbon;

class Main
{
    const COLLECTION_DIGITAL = 'KRD';
    const COLLECTION_PRINTED = 'KC';
    const COLLECTION_ANALOG = 'KRA';
    const IS_CENTER_BRANCH = 37;

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

    /**
     * generateNumberDeposit
     *
     * @param  mixed $param
     * @return void
     */
    public static function generateNumberDeposit($worksheetId, $branchId, $year, $cityId)
    {
        $worksheet = QueryAPI::get("select depositformat_code as code from worksheet where id = $worksheetId", true);
        $city = QueryAPI::get("select code_kab as code from kabupaten where id = $cityId", true);
        $seq = 1;

        $cityCode = $branchId == static::IS_CENTER_BRANCH ? substr($city->CODE ?? '', 0, -3) : ($city->CODE ?? '');
        $worksheetCode = $worksheet->CODE ?? '';
        $yearNow = date('Y');

        $data = QueryAPI::get("
            select
                max(substr(deposit, -5)) as unique_code
            from
                e_collections
            where
                deposit is not null and
                to_char(created_at, 'YYYY') = '$yearNow'
        ", true);

        if ($data) {
            $seq = (int) $data->UNIQUE_CODE;
            $seq += 1;
            $seq = sprintf('%05d', $seq);
        }

        return "$worksheetCode-$cityCode<br>$year-$seq";
    }

    /**
     * generateNumberCopy
     *
     * @param  mixed $param
     * @return void
     */
    public static function generateNumberCopy($param = null)
    {
        $date = Carbon::parse($param ?? date('Y-m-d'))->format('Ymd');
        $seq = 1;

        $data = QueryAPI::get("
            select
                max(substr(code, 8)) as unique_code
            from
                e_collection_copies
            where
                code like '%C$date%'
        ", true);

        if ($data) {
            $seq = (int) $data->UNIQUE_CODE;
            $seq += 1;
        }

        return 'C' . $date . sprintf('%05s', $seq);
    }

    /**
     * copyright
     *
     * @param  mixed $executorId
     * @return void
     */
    public static function copyright($executorId)
    {
        $executor = QueryAPI::get("
            select
                *
            from
                penerbit
            where
                id = $executorId
        ", true);

        if ($executor) {
            return 'Copyrights (c) ' . date('Y') . ' ' . $executor->NAME;
        }

        return null;
    }

    /**
     * parseTemplateEmail
     *
     * @param  mixed $payload
     * @param  mixed $template
     * @return void
     */
    public static function parseTemplateEmail($payload, $template)
    {
        $parsed = preg_replace_callback('/{{(.*?)}}/', function ($matches) use ($payload, $template) {
            list($shortCode, $index) = $matches;
            if (isset($payload[$index])) {
                return $payload[$index];
            } else {
                throw new \Exception("Shortcode {$shortCode} not found in template id {$template->ID}", 1);
            }
        }, $template->CONTENT);

        return $parsed;
    }

    /**
     * isNotCenterBranch
     *
     * @return void
     */
    public static function isNotCenterBranch()
    {
        return (int) session('branch_id') !== static::IS_CENTER_BRANCH;
    }

    /**
     * AESCrypt
     *
     * @param  mixed $text
     * @return void
     */
    public static function AESCrypt($text, $key = null, $iv = null)
    {
        $cipher = 'aes-256-cbc';
        $key = $key ?? env('AES_KEY');
        $iv = $iv ?? env('AES_IV');
        $encrypted = openssl_encrypt($text, $cipher, $key, OPENSSL_RAW_DATA, $iv);

        return base64_encode($encrypted);
    }

    /**
     * AESDecrypt
     *
     * @param  mixed $text
     * @return void
     */
    public static function AESDecrypt($text, $key = null, $iv = null)
    {
        $cipher = 'aes-256-cbc';
        $key = $key ?? env('AES_KEY');
        $iv = $iv ?? env('AES_IV');
        $decoded = base64_decode($text);

        $decrypted = openssl_decrypt($decoded, $cipher, $key, OPENSSL_RAW_DATA, $iv);

        return $decrypted;
    }

    /**
     * login
     *
     * @param  mixed $username
     * @param  mixed $password
     * @return void
     */
    public static function login($username, $password)
    {
        $response = false;
        $login = QueryAPI::login($username, $password);

        if (($login->Status ?? '') == 'Success') {
            $userId = $login->Data->Id ?? null;
            $user = QueryAPI::get("
                select
                    users.*,
                    branchs.province_id as province_id,
                    branchs.name as name_branch,
                    propinsi.namapropinsi as namapropinsi
                from
                    users
                left join
                    branchs on branchs.id = users.branch_id
                left join
                    propinsi on propinsi.id = branchs.province_id
                where
                    users.id = $userId and
                    (
                        users.isdelete = 0 or
                        users.isdelete is null
                    )
            ", true);

            if ($user) {
                session([
                    'id' => $user->ID,
                    'username' => $user->USERNAME,
                    'name' => $user->FULLNAME,
                    'email' => $user->EMAILADDRESS,
                    'province_id' => $user->PROVINCE_ID,
                    'province_name' => $user->NAMAPROPINSI,
                    'branch_id' => $user->BRANCH_ID,
                    'branch_name' => $user->NAME_BRANCH,
                    'role_id' => $user->ROLE_ID,
                ]);

                $response = true;
            }
        }

        return $response;
    }
}
