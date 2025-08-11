<?php

namespace App\Helper;

use Crypt;
use Imagick;
use ImagickDraw;
use App\Models\City;
use App\Models\Mark;
use App\Helper\PHPMP3;
use App\Models\Setting;
use App\Models\Location;
use App\Models\Province;
use App\Models\Publisher;
use App\Models\Collection;
use App\Models\DepositHead;
use App\Models\DeliveryForm;
use App\Models\CollectionCopy;
use setasign\Fpdi\Tcpdf\Fpdi;
use App\Models\CollectionMedia;
use falahati\PHPMP3\MpegAudio;
use App\Models\UserCertainAccess;
use Illuminate\Support\Facades\DB;
use Org_Heigl\Ghostscript\Ghostscript;
use Illuminate\Support\Facades\Storage;

class GeneralHelper
{

    public static function decodeAes($string)
    {
        $key           = '02806cc1-5b33-4170-8640-1795d77b0708';
        $decode_base64 = base64_decode($string);
        $decode_utf8   = utf8_decode($decode_base64);
        $decode_array  = json_decode($decode_utf8, true);
        $data          = $decode_array['value'];
        $iv            = base64_decode($decode_array['iv']);
        $decrypt       = openssl_decrypt($data, 'AES-256-CBC', $key, 0, $iv);
        $result        = json_decode($decrypt);

        return $result;
    }

    public static function minuteToSecond($str)
    {
        $time   = explode(':', $str);
        $second = ((int)$time[0] * 60) + (int)$time[1];

        return $second;
    }

    public static function depositCollection($param = null)
    {
        if ($param) {
            $data = Collection::select(DB::raw('MAX(SUBSTRING(deposit,12)) as unique_code'))->where('deposit', 'LIKE', 'DEP' . date('Ymd', strtotime($param)) . "%")->withTrashed()->first();
            $code = (int)$data->unique_code;
            $code = $code + 1;
            return 'DEP' . date('Ymd', strtotime($param)) . sprintf('%05s', $code);
        } else {
            $data = Collection::select(DB::raw('MAX(SUBSTRING(deposit,12)) as unique_code'))->where('deposit', 'LIKE', 'DEP' . date('Ymd') . "%")->withTrashed()->first();
            $code = (int)$data->unique_code;
            $code += 1;
            return 'DEP' . date('Ymd') . sprintf('%05s', $code);
        }
    }

    public static function codeCopyCollection($param = null)
    {
        if ($param) {
            $data = CollectionCopy::select(DB::raw('MAX(SUBSTRING(code,8)) as unique_code'))->where('code', 'LIKE', 'C' . date('ymd', strtotime($param)) . "%")->withTrashed()->first();
            $code = (int)$data->unique_code;
            $code = $code + 1;
            return 'C' . date('ymd', strtotime($param)) . sprintf('%05s', $code);
        } else {
            $data = CollectionCopy::select(DB::raw('MAX(SUBSTRING(code,8)) as unique_code'))->where('code', 'LIKE', 'C' . date('ymd') . "%")->withTrashed()->first();
            $code = (int)$data->unique_code;
            $code += 1;
            return 'C' . date('ymd') . sprintf('%05s', $code);
        }
    }

    public static function letterNoDelivery($param = null)
    {
        if ($param) {
            $data = DeliveryForm::select(DB::raw('MAX(SUBSTRING(letter_no,12)) as unique_code'))->where('letter_no', 'LIKE', 'REC' . date('Ymd', strtotime($param)) . "%")->first();
            $code = (int)$data->unique_code;
            $code = $code + 1;
            return 'REC' . date('Ymd', strtotime($param)) . sprintf('%05s', $code);
        } else {
            $data = DeliveryForm::select(DB::raw('MAX(SUBSTRING(letter_no,12)) as unique_code'))->where('letter_no', 'LIKE', 'REC' . date('Ymd') . "%")->first();
            $code = (int)$data->unique_code;
            $code += 1;
            return 'REC' . date('Ymd') . sprintf('%05s', $code);
        }
    }

    public static function generateMarks($deposit_head_id, $province_id, $regency_id = null, $year = null)
    {
        try {
            $unique_code = Mark::where('deposit_head_id', $deposit_head_id)->where('province_id', $province_id);
            // dd($unique_code);
            $deposit_head_code = DepositHead::where('id', $deposit_head_id)->value('code');
            $province_code = Province::where('id', $province_id)->value('code');
            $location_code = $province_code;
            if (!empty($regency_id)) {
                $unique_code = $unique_code->where('regency_id', $regency_id);
                $regency_code = City::where('id', $regency_id)->value('code');
                $location_code = $regency_code;
            } else {
                $unique_code = $unique_code->where('regency_id', 0);
                $regency_id = 0;
            }

            if (!empty($year)) {
                $unique_code = $unique_code->where('year', $year);
            } else {
                $unique_code = $unique_code->where('year', date('Y'));
                $year = date('Y');
            }

            $unique_code = $unique_code->first();
            $code = (int) (isset($unique_code->last_digit)) ? $unique_code->last_digit : 0;
            $code += 1;
            //update or insert when method called
            Mark::updateOrInsert(
                ['id' => (isset($unique_code->id)) ? $unique_code->id : null],
                ['deposit_head_id' => $deposit_head_id, 'province_id' => $province_id, 'regency_id' => $regency_id, 'year' => $year, 'last_digit' => $code]
            );

            return $deposit_head_code . '-' . $location_code . ' ' . $year . '-' . sprintf('%05s', $code);
        } catch (\Exception $e) {
            dd($e);
        }

        return $deposit_head_code . '-' . $location_code . ' ' . $year . '-' . sprintf('%05s', $code);
    }

    public static function publisherCode($param = null)
    {
        if ($param) {
            $data = Publisher::select(DB::raw('MAX(SUBSTRING(publisher_code,9)) as unique_code'))->whereDate('created_at', '=', substr($param, 0, 10))->first();
            $code = (int)$data->unique_code;
            $code++;
            return date('Ymd', strtotime($param)) . sprintf('%05s', $code);
        } else {
            $data = Publisher::select(DB::raw('MAX(SUBSTRING(publisher_code,9)) as unique_code'))->whereDate('created_at', '=', date('Y-m-d'))->first();
            $code = (int)$data->unique_code;
            $code++;
            return date('Ymd') . sprintf('%05s', $code);
        }
    }

    public static function formatSize($bytes)
    {
        $kb = 1024;
        $mb = $kb * 1024;
        $gb = $mb * 1024;
        $tb = $gb * 1024;

        if (($bytes >= 0) && ($bytes < $kb)) {
            return $bytes . ' B';
        } elseif (($bytes >= $kb) && ($bytes < $mb)) {
            return ceil($bytes / $kb) . ' KB';
        } elseif (($bytes >= $mb) && ($bytes < $gb)) {
            return ceil($bytes / $mb) . ' MB';
        } elseif (($bytes >= $gb) && ($bytes < $tb)) {
            return ceil($bytes / $gb) . ' GB';
        } elseif ($bytes >= $tb) {
            return ceil($bytes / $tb) . ' TB';
        } else {
            return $bytes . ' B';
        }
    }

    public static function getFileName($url_file)
    {
        $arrFile = explode('/', $url_file);
        return $arrFile[sizeof($arrFile) - 1];
    }

    public static function countSlug($name, $table_name)
    {
        $count = 0;
        switch ($table_name) {
            case 'collections':
                $count = \App\Models\Collection::where('slug', \Str::slug($name))->count();
                break;

            case 'authors':
                $count = \App\Models\Author::where('slug', \Str::slug($name))->count();
                break;

            case 'categories':
                $count = \App\Models\Category::where('slug', \Str::slug($name))->count();
                break;

            case 'authors':
                $count = \App\Models\Author::where('slug', \Str::slug($name))->count();
                break;

            case 'contributors':
                $count = \App\Models\Contributor::where('slug', \Str::slug($name))->count();
                break;

            default:
                # code...
                break;
        }
        return $count;
    }

    public static function getMonth($param = null)
    {
        $month = [
            '01' => 'Januari',
            '02' => 'Febuari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];
        if (strlen($param) == 1) {
            $param = '0' . $param;
        }

        if ($param) {
            $result = $month[$param];
        } else {
            $result = $month;
        }

        return $result;
    }

    public static function checkPermissionCertain($role, $access)
    {
        $check =  UserCertainAccess::where('role_id', $role)
            ->where('access', $access)
            ->count();

        if ($check > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function pdfToImage($type, $file, $collection_id)
    {
        $location = Location::where('active', 1)->first();
        $target = 'public/collection/' . $type . '/images/' . $collection_id;

        Storage::disk($location->location)->makeDirectory($target);

        $arr = explode('/', $file);
        $file_name = str_replace(".pdf", "", end($arr));
        $gsPath = env('GS_PATH', null);

        if ($gsPath) {
            Ghostscript::setGsPath($gsPath);
        }

        $pdf = new \Spatie\PdfToImage\Pdf(Storage::disk($location->location)->path($file));
        for ($i = 1; $i <= $pdf->getNumberOfPages(); $i++) {
            $pdf->setPage($i)->saveImage(Storage::disk($location->location)->path($target . '/' . $file_name . "-$i.jpg"));
        }

        $media = CollectionMedia::firstOrNew([
            'collection_id' => $collection_id,
            'link' => 'collection/' . $type . '/images/' . $collection_id . '/' . $file_name,
            'type' => 3,
            'method' => 4,
            'status' => 1,
        ], [
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'location_id' => $location->id
        ]);

        $media->save();
    }



    public static function convertWavtoMp3($originalpath, $savepath)
    {
        return system('ffmpeg -i ' . $originalpath . ' -f mp2 ' . $savepath);
    }


    public static function audioCut($file, $output, $prev_start, $prev_end)
    {
        return MpegAudio::fromFile($file)
            ->trim(GeneralHelper::minuteToSecond($prev_start), (GeneralHelper::minuteToSecond($prev_end) -  GeneralHelper::minuteToSecond($prev_start)))
            ->saveFile($output);
    }

    public static function audioWatermark($file, $output)
    {
        return MpegAudio::fromFile(public_path('watermark/audio_wm.mp3'))
            ->append(MpegAudio::fromFile($file))
            ->append(MpegAudio::fromFile(public_path('watermark/audio_wm.mp3')))
            ->saveFile($output);
        /*
        $mp3  = new PHPMP3($file);
        $mp3->striptags();
        $watermark = new PHPMP3(public_path('watermark/audio_wm.mp3'));
        $mp3->mergeBehind($watermark);
        $mp3->striptags();
        $mp3->setIdv3_2('01','Track Title','Artist','Album','Year','Genre','Comments','Composer','OrigArtist', 'Copyright','url','encodedBy');
        return $mp3->save($output);
        */
    }

    public static function videoCut($file, $output, $prev_start, $prev_end)
    {
        return system('ffmpeg -i ' . $file . ' -ss ' . $prev_start . ' -to ' . $prev_end . ' -c copy ' . $output);
    }

    public static function videoWatermark($file, $output)
    {
        $logo = public_path('main/logo.png');
        return system('ffmpeg -i ' . $file . ' -i ' . $logo . ' -filter_complex "overlay=10:10" ' . $output);
    }

    public static function encryptString($value)
    {
        return Crypt::encryptString($value);
    }

    public static function decryptString($value)
    {
        return Crypt::decryptString($value);
    }

    /**
     * @param int $number
     * @return string
     */
    public static function numberToRomawi($number)
    {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if ($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }
    public static function encrypt_aes256($str)
    {
        $key = Setting::where('slug', 'key')->first()->content;
        $iv = Setting::where('slug', 'iv')->first()->content;
        $encrypt_text = openssl_encrypt($str, "AES-256-CBC", $key, OPENSSL_RAW_DATA, $iv);
        $data = base64_encode($encrypt_text);
        return $data;
    }

    public static function decrypt_aes256($str)
    {
        $key = Setting::where('slug', 'key')->first()->content;
        $iv = Setting::where('slug', 'iv')->first()->content;
        $encrypt_text = base64_decode($str);
        $clear_text = openssl_decrypt($encrypt_text, "AES-256-CBC", $key, OPENSSL_RAW_DATA, $iv);
        return $clear_text;
    }

    public static function getDetailPublisher($usr, $pwd)
    {
        $user = self::encrypt_aes256($usr);
        $pass = self::encrypt_aes256($pwd);
        //\Log::info("usr = " . $user );
        //\Log::info("pass = " . $pass );
        //\Log::info("https://isbn.perpusnas.go.id/api/cari?usr=" . $user . "&pwd=" . $pass);
        try {
            //$url = "http://192.168.0.107:8020/Restful.aspx?op=cari&usr=" . $user . "&pwd=" . $pass . "&output=json";
            $url = "http://192.168.7.185:8020/Restful.aspx?op=cari&usr=" . $user . "&pwd=" . $pass . "&output=json";

            //$url = "http://192.168.0.107:88/api/cari?&usr=" . $user . "&pwd=" . $pass;
            //\Log::info($url);
            //$url = "https://isbn.perpusnas.go.id/api/cari?usr=" . $user . "&pwd=" . $pass;
            $json = self::preJsonBase($url);
            //\Log::info($json);
            if ($json) {
                return $json["CariUserModels"];
            } else {
                return $json;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function setIsbnReceived($isbn, $date)
    {
        $isbn = self::encrypt_aes256($isbn . ';' . $date);
        try {
            $url = "https://isbn.perpusnas.go.id/api/SumberElek?isbnok_date=" . $isbn;
            $arrContextOptions = array(
                "ssl" => array(
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ),
                "http" => array(
                    "method" => "PUT",
                    'header' => 'Content-Length: 0',
                    'content' => '',
                )
            );
            $result =  json_decode(@file_get_contents($url, false, stream_context_create($arrContextOptions)));
            if ($result) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function getDataElek($kd_penerbit, $pageNum, $status, $keyword)
    {
        try {
            $url = urldecode('https://isbn.perpusnas.go.id/api/sumberElek?kode=' . $kd_penerbit . '&status=' . $status . '&pageNumber=' . $pageNum . '&keyword=' . $keyword);
            $json = self::preJsonBase($url);
            if ($json) {
                return $json;
            } else {
                return [];
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function getDataCetak($kd_penerbit, $pageNum, $status, $keyword)
    {
        try {
            $url = urldecode('https://isbn.perpusnas.go.id/api/Cetak?kode=' . $kd_penerbit . '&status=' . $status . '&pageNumber=' . $pageNum . '&keyword=' . $keyword);
            $json = self::preJsonBase($url);
            if ($json) {
                return $json;
            } else {
                return [];
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function getDetailIsbn($isbn)
    {
        try {
            $url = 'https://isbn.perpusnas.go.id/api/cari?isbn=' . $isbn;
            $json = self::preJsonBase($url);
            return $json;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function preJsonBase($url)
    {
        $url = str_replace('&amp;', '&', $url);
        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );
        try {
            $result = @json_decode(file_get_contents($url, false, stream_context_create($arrContextOptions)), true);
            //\Log::info(file_get_contents($url, false, stream_context_create($arrContextOptions)));
            \Log::info($result);
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function get_http_response_code($url)
    {
        $headers = get_headers($url);
        return substr($headers[0], 9, 3);
    }

    public static function generateValidation($type, $validation_rule)
    {
        $generated = [];
        foreach ($validation_rule as $key => $value) {
            if ($value !== null && isset($value[$type])) {
                $generated = array_merge($generated, $value[$type]);
            }
        }
        return $generated;
    }

    public static function validateConfig($field, $validation)
    {
        $temp = [];
        if (isset($validation[$field])) {
            $temp['validation'][$field] = $validation[$field]['validation'];
            foreach ($validation[$field]['messages'] as $k => $v) {
                $temp['messages'][$field . '.' . $k] = $v;
            }
        }
        return $temp;
    }

    public static function toSnakeCase($input)
    {
        // Replace spaces and certain characters with underscores
        $snakeCase = preg_replace('/[\s\-]+/', '_', $input);

        // Convert the string to lowercase
        return strtolower($snakeCase);
    }
}
