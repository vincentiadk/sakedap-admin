<?php

namespace App\Helpers;

use Carbon\Carbon;

class Main
{
    const COLLECTION_DIGITAL = 'KRD';
    const COLLECTION_PRINTED = 'KC';
    const COLLECTION_ANALOG = 'KRA';
    const IS_SUPER_ADMIN = 1;
    const IS_PERPUSNAS = 37;
    const CACHE_NAME_CONFIG_APP = 'app_configuration';
    const CONFIG_PARAM = [
        'EPercobaanLogin',
        'EPercobaanLoginInterval',
        'EAesKey',
        'EAesIV',
        'EAesInlisKey',
        'EAesInlisIV',
        'EIFrameDomain',
        'EBatasResetPassword',
        'EBatasFileOriginal',
        'ETglKepatuhanPenerbit',
        'ERedisClient',
        'ERedisHost',
        'ERedisUsername',
        'ERedisPassword',
        'ERedisPort',
        'ESessionDriver',
        'ESessionLifeTime',
        'ESessionEncrypt',
        'EKatalogCoverMaxUpload',
        'EKatalogContentMaxUpload',
        'EBatasSerahKCKR',
        'EBatasHibah',
        'EBatasPengambilan',
        'EWaktuWajibKaryaCetak',
        'EWaktuWajibKaryaRekam',
        'EMaksJumlahPembinaan',
        'ECaptchaSecret',
        'ECaptchaSite',
        'EAPIISBNToken',
        'EAPIISBNBaseUrl',
        'EAPIRajaOngkirToken',
        'EAPIRajaOngkirBaseUrl',
        'EAPIKomshipToken',
        'EAPIKomshipBaseUrl',
        'EDeliveryMethod',
    ];

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
    public static function generateNumberDeposit($worksheetId, $year, $cityId)
    {
        $branchId = session('branch_id');
        $worksheet = QueryAPI::get("select depositformat_code as code from worksheet where id = $worksheetId", true);
        $city = QueryAPI::get("select code_kab as code from kabupaten where id = $cityId", true);
        $seq = 1;

        $cityCode = $branchId == 37 ? substr($city->CODE ?? '', 0, -3) : ($city->CODE ?? '');
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
    public static function copyright($executorId = null)
    {
        $text = '';

        if ($executorId) {
            $executor = QueryAPI::get("
                select
                    *
                from
                    penerbit
                where
                    id = $executorId
            ", true);

            if ($executor) {
                $text = 'Copyrights (c) ' . date('Y') . ' ' . $executor->NAME;
            }
        }

        return $text;
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
        $parsed = preg_replace_callback('/{{(.*?)}}/', function ($matches) use ($payload) {
            list($shortCode, $index) = $matches;

            if (isset($payload[$index])) {
                return $payload[$index];
            } else {
                return $shortCode;
            }
        }, $template->CONTENT);

        return (string) $parsed;
    }

    /**
     * isSuperAdmin
     *
     * @return void
     */
    public static function isSuperAdmin()
    {
        return (int) (session('role_id') == static::IS_SUPER_ADMIN);
    }

    public static function isPerpusnas()
    {
        return (int) (session('branch_id') == static::IS_PERPUSNAS);
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
        $key = $key ?? config('system.aes_key');
        $iv = $iv ?? config('system.aes_iv');
        $encrypted = @openssl_encrypt($text, $cipher, $key, OPENSSL_RAW_DATA, $iv);

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
        $key = $key ?? config('system.aes_key');
        $iv = $iv ?? config('system.aes_iv');
        $decoded = base64_decode($text);

        $decrypted = @openssl_decrypt($decoded, $cipher, $key, OPENSSL_RAW_DATA, $iv);

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
                    'province_id' => $user->PROVINCE_ID ?: 31,
                    'province_name' => $user->NAMAPROPINSI ?: 'DKI Jakarta',
                    'branch_id' => $user->BRANCH_ID ?: 37,
                    'branch_name' => $user->NAME_BRANCH ?: 'Perpustakaan Nasional',
                    'role_id' => $user->ROLE_ID ?: 1,
                ]);

                $response = true;
            }
        }

        return $response;
    }

    /**
     * credentialInlisIFrame
     *
     * @return void
     */
    public static function credentialInlisIFrame()
    {
        $userId = session('id');
        $encFrameInlis = static::AESCrypt(
            "userid=$userId;auth=1",
            base64_decode(config('inlis.aes_key')),
            base64_decode(config('inlis.aes_iv'))
        );

        return $encFrameInlis;
    }

    /**
     * base64File
     *
     * @param  mixed $url
     * @return void
     */
    public static function base64File($url)
    {
        $getContent = file_get_contents($url);
        $base64 = base64_encode($getContent);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_buffer($finfo, $getContent);
        $link = "data:$mimeType;base64," . $base64;

        return $link;
    }

    /**
     * formatFileSize
     *
     * @param  mixed $bytes
     * @param  mixed $precision
     * @return void
     */
    public static function formatFileSize($bytes, $precision = 2)
    {
        $bytes = (int) $bytes;
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

        if ($bytes == 0) {
            return '0 ' . $units[0];
        }

        $power = floor(log($bytes, 1024));

        return round($bytes / pow(1024, $power), $precision) . ' ' . $units[$power];
    }

    /**
     * method
     *
     * @param  mixed $value
     * @return void
     */
    public static function method($value)
    {
        $text = '';

        if ($value == 1) {
            $text = 'API';
        } else if ($value == 2) {
            $text = 'SFTP';
        } else if ($value == 3) {
            $text = 'Mandiri';
        } else if ($value == 4) {
            $text = 'Manual';
        } else if ($value == 5) {
            $text = 'Sistem';
        } else if ($value == 6) {
            $text = 'Bulk Penerbit';
        } else if ($value == 7) {
            $text = 'Bulk Admin';
        }

        return $text;
    }

    /**
     * serial
     *
     * @param  mixed $value
     * @return void
     */
    public static function serial($value)
    {
        $text = '';

        if ($value == 1) {
            $text = 'Harian';
        } else if ($value == 2) {
            $text = 'Mingguan';
        } else if ($value == 3) {
            $text = 'Bulanan';
        } else if ($value == 4) {
            $text = '3 Bulan Sekali';
        } else if ($value == 5) {
            $text = '4 Bulan Sekali';
        } else if ($value == 6) {
            $text = '6 Bulan Sekali';
        } else if ($value == 7) {
            $text = 'Tahunan';
        } else if ($value == 8) {
            $text = '2 Tahun Sekali';
        } else if ($value == 9) {
            $text = '3 Tahun Sekali';
        }

        return $text;
    }

    /**
     * access
     *
     * @param  mixed $value
     * @return void
     */
    public static function access($value)
    {
        $text = '';

        if ($value == 1) {
            $text = 'Akses full file berwatermak secara online';
        } else if ($value == 2) {
            $text = 'Akses hanya preview file secara online, namun tetap dapat di dayagunakan di lingkungan perpustakaan nasional RI dengan jaringan internet LAN';
        } else if ($value == 3) {
            $text = 'Akses hanya file preview secara online, dan tidak didayagunakan di lingkungan Perpustakaan Nasional RI selama 5 tahun sejak diserahkan. Setelah 5 tahun, akan didayagunakan oleh Perpustakaan Nasional RI di jaringan internet LAN';
        } else if ($value == 4) {
            $text = 'Akses hanya file preview secara online selamanya dan tidak didayagunakan di mana pun';
        }

        return $text;
    }

    /**
     * getCoverISBN
     *
     * @param  mixed $fileUrl
     * @return void
     */
    public static function getCoverISBN($fileUrl = null)
    {
        $baseUrl = config('inlis.base_url');

        if ($fileUrl) {
            $link = $baseUrl . $fileUrl;
        } else {
            $link = asset('assets/no-file.jpg');
        }

        return $link;
    }
}
