<?php

namespace App\Http\Controllers\Setting;

use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class ConfigurationController extends Controller
{
    public function index()
    {
        $configParam = array_map(function ($name) {
            return "'" . $name . "'";
        }, Main::CONFIG_PARAM);

        $settingParameterName = implode(',', $configParam);
        $settingParameter = QueryAPI::get("select * from settingparameters where name in ($settingParameterName)");
        $mail = QueryAPI::get("select * from mailserver where modul = 'EDEPOSIT'", true);
        $obedient = QueryAPI::get("select * from e_kepatuhan_kckr");

        return view('layouts.index', [
            'data' => [
                'settingParameter' => collect($settingParameter ?? []),
                'obedient' => collect($obedient ?? []),
                'mail' => $mail,
                'content' => 'setting.configuration'
            ]
        ]);
    }

    public function submitted(Request $request)
    {
        $redirectUrl = url('setting/configuration');

        try {
            $userName = session('name');
            $currentDate = date('Y-m-d H:i:s');
            $ip = $request->ip();

            $createBy = $userName;
            $createDate = $currentDate;
            $createTerminal = $ip;
            $updateBy = $userName;
            $updateDate = $currentDate;
            $updateTerminal = $ip;

            $payloadSettingParameter = [
                ['name' => 'EPercobaanLogin', 'value' => $request->system_rate_limiter],
                ['name' => 'EPercobaanLoginInterval', 'value' => $request->system_rate_limiter_interval],
                ['name' => 'EAesKey', 'value' => $request->system_aes_key],
                ['name' => 'EAesIV', 'value' => $request->system_aes_iv],
                ['name' => 'EAesInlisKey', 'value' => $request->system_aes_key_inlis],
                ['name' => 'EAesInlisIV', 'value' => $request->system_aes_iv_inlis],
                ['name' => 'EIFrameDomain', 'value' => $request->system_allow_iframe_domain],
                ['name' => 'ERedisClient', 'value' => $request->system_redis_client],
                ['name' => 'ERedisHost', 'value' => $request->system_redis_host],
                ['name' => 'ERedisUsername', 'value' => $request->system_redis_username],
                ['name' => 'ERedisPassword', 'value' => $request->system_redis_password],
                ['name' => 'ERedisPort', 'value' => $request->system_redis_port],
                ['name' => 'ESessionDriver', 'value' => $request->system_session_driver],
                ['name' => 'ESessionLifeTime', 'value' => $request->system_session_lifetime],
                ['name' => 'ESessionEncrypt', 'value' => $request->system_encryption],
                ['name' => 'EKatalogCoverMaxUpload', 'value' => $request->catalog_cover],
                ['name' => 'EKatalogContentMaxUpload', 'value' => $request->catalog_obedient],
                ['name' => 'EBatasSerahKCKR', 'value' => $request->catalog_submission_kckr],
                ['name' => 'EBatasHibah', 'value' => $request->catalog_limit_grant],
                ['name' => 'EBatasPengambilan', 'value' => $request->catalog_limit_retur],
                ['name' => 'ECaptchaSecret', 'value' => $request->captcha_secret_key],
                ['name' => 'ECaptchaSite', 'value' => $request->captcha_site_key],
                ['name' => 'EAPIISBNToken', 'value' => $request->isbn_token],
                ['name' => 'EAPIISBNBaseUrl', 'value' => $request->isbn_base_url],
                ['name' => 'EAPIRajaOngkirToken', 'value' => $request->ro_token],
                ['name' => 'EAPIRajaOngkirBaseUrl', 'value' => $request->ro_base_url],
            ];

            $payloadMail = [
                'modul' => 'EDEPOSIT',
                'host' => $request->mail_host,
                'port' => $request->mail_port,
                'credentialmail' => $request->mail_username,
                'credentialpassword' => $request->mail_password,
                'enablessl' => 1,
                'mailfrom' => $request->mail_from,
                'maildisplayname' => $request->mail_name,
                'isactive' => 1,
                'createby' => $createBy,
                'createdate' => $createDate,
                'createterminal' => $createTerminal,
                'updateby' => $updateBy,
                'updatedate' => $updateDate,
                'updateterminal' => $updateTerminal,
            ];

            $payloadObedient = [
                ['name' => 'Patuh', 'persen' => $request->catalog_obedient],
                ['name' => 'Sebagian Patuh', 'persen' => $request->catalog_some_obey],
                ['name' => 'Tidak Patuh', 'persen' => $request->catalog_not_obey],
            ];

            foreach ($payloadSettingParameter as $psp) {
                $name = $psp['name'];
                $payload = array_merge($psp, [
                    'createby' => $createBy,
                    'createdate' => $createDate,
                    'createterminal' => $createTerminal,
                    'updateby' => $updateBy,
                    'updatedate' => $updateDate,
                    'updateterminal' => $updateTerminal,
                ]);

                $check = QueryAPI::get("select * from settingparameters where name = '$name'", true);

                if ($check) {
                    unset($payload['createby'], $payload['createdate'], $payload['createterminal']);
                    QueryAPI::update('settingparameters', $check->ID, $payload, false);
                } else {
                    QueryAPI::create('settingparameters', $payload, false);
                }
            }

            $checkExistsConfigEmail = QueryAPI::get("select * from mailserver where modul = 'EDEPOSIT'", true);

            if ($checkExistsConfigEmail) {
                unset($payloadMail['createby'], $payloadMail['createdate'], $payloadMail['createterminal']);
                QueryAPI::update('mailserver', $checkExistsConfigEmail->ID, $payloadMail, false);
            } else {
                QueryAPI::create('mailserver', $payloadMail, false);
            }

            foreach ($payloadObedient as $po) {
                $name = $po['name'];
                $payload = array_merge($po, [
                    'createby' => $createBy,
                    'createdate' => $createDate,
                    'createterminal' => $createTerminal,
                    'updateby' => $updateBy,
                    'updatedate' => $updateDate,
                    'updateterminal' => $updateTerminal,
                ]);

                $check = QueryAPI::get("select * from e_kepatuhan_kckr where name = '$name'", true);

                if ($check) {
                    unset($payload['createby'], $payload['createdate'], $payload['createterminal']);
                    QueryAPI::update('e_kepatuhan_kckr', $check->ID, $payload, false);
                } else {
                    QueryAPI::create('e_kepatuhan_kckr', $payload, false);
                }
            }

            Cache::forget(Main::CACHE_NAME_CONFIG_APP);

            return redirect($redirectUrl)->with('success', 'Konfigurasi telah disimpan');
        } catch (\Exception $e) {
            return redirect($redirectUrl)->with('failed', $e->getMessage());
        }
    }

    public function testSendEmail(Request $request)
    {
        try {
            $email = $request->email ?? 'admin@gmail.com';
            $from = config('mail.from.address');
            $name = config('mail.from.name');

            Mail::send([], [], function ($message) use ($email, $from, $name) {
                $message->to($email, $name)
                    ->subject('Tes Kirim Email')
                    ->from($from, $name)
                    ->html('Email berhasil terkirim', 'text/html');
            });

            $response = [
                'code' => 200,
                'message' => 'Email berhasil dikirim',
            ];
        } catch (\Exception $e) {
            $response = [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ];
        }

        return response()->json($response);
    }
}
