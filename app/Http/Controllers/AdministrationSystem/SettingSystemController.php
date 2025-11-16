<?php

namespace App\Http\Controllers\AdministrationSystem;

use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class SettingSystemController extends Controller
{
    private const CACHE_TTL = 3600;

    public function index()
    {
        $settingParameter = Cache::remember('settings_params', self::CACHE_TTL, function () {
            $configParam = array_map(fn($name) => "'$name'", Main::CONFIG_PARAM);
            $settingParameterName = implode(',', $configParam);

            return QueryAPI::get("SELECT * FROM settingparameters WHERE name IN ($settingParameterName)") ?? [];
        });

        $mail = Cache::remember('mail_config_edeposit', self::CACHE_TTL, function () {
            return QueryAPI::get("SELECT * FROM mailserver WHERE modul = 'EDEPOSIT'", true);
        });

        $obedient = Cache::remember('obedient_kckr', self::CACHE_TTL, function () {
            return QueryAPI::get("SELECT * FROM e_kepatuhan_kckr") ?? [];
        });

        return view('layouts.index', [
            'data' => [
                'settingParameter' => collect($settingParameter),
                'obedient' => collect($obedient),
                'mail' => $mail,
                'content' => 'administration-system.setting-system'
            ]
        ]);
    }

    public function submitted(Request $request)
    {
        $redirectUrl = url('administration-system/setting-system');

        try {
            $auditData = $this->getAuditData($request);

            $this->upsertSettingParameters($request, $auditData);
            $this->upsertMailConfiguration($request, $auditData);
            $this->upsertObedientConfiguration($request, $auditData);
            $this->clearConfigurationCache();

            return redirect($redirectUrl)->with('success', 'Konfigurasi telah disimpan');
        } catch (\Exception $e) {
            Log::error('Configuration update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect($redirectUrl)->with('failed', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function testSendEmail(Request $request)
    {
        try {
            $email = $request->email ?? 'admin@gmail.com';
            $from = config('mail.from.address');
            $name = config('mail.from.name');

            Mail::send([], [], function ($message) use ($email, $from, $name) {
                $message->to($email)
                    ->subject('Tes Kirim Email')
                    ->from($from, $name)
                    ->html('<h2>Test Email</h2><p>Email berhasil terkirim dari sistem SAKEDAP.</p><p>Timestamp: ' . now()->format('d-m-Y H:i:s') . '</p>', 'text/html');
            });

            return response()->json([
                'code' => 200,
                'success' => true,
                'message' => 'Email berhasil dikirim ke ' . $email,
            ]);
        } catch (\Exception $e) {
            Log::error('Test email failed', [
                'email' => $request->email ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'code' => 500,
                'success' => false,
                'message' => 'Gagal mengirim email: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function getAuditData(Request $request): array
    {
        $currentDate = date('Y-m-d H:i:s');
        $userName = session('name', 'system');
        $ip = $request->ip();

        return [
            'createby' => $userName,
            'createdate' => $currentDate,
            'createterminal' => $ip,
            'updateby' => $userName,
            'updatedate' => $currentDate,
            'updateterminal' => $ip,
        ];
    }

    private function buildSettingParameters(Request $request): array
    {
        return [
            ['name' => 'EPercobaanLogin', 'value' => $request->system_rate_limiter],
            ['name' => 'EPercobaanLoginInterval', 'value' => $request->system_rate_limiter_interval],
            ['name' => 'EAesKey', 'value' => $request->system_aes_key],
            ['name' => 'EAesIV', 'value' => $request->system_aes_iv],
            ['name' => 'EAesInlisKey', 'value' => $request->system_aes_key_inlis],
            ['name' => 'EAesInlisIV', 'value' => $request->system_aes_iv_inlis],
            ['name' => 'EIFrameDomain', 'value' => $request->system_allow_iframe_domain],
            ['name' => 'EBatasResetPassword', 'value' => $request->system_limit_reset_password],
            ['name' => 'EBatasFileOriginal', 'value' => $request->system_limit_file_original],
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
            ['name' => 'EWaktuWajibKaryaCetak', 'value' => $request->printed_work],
            ['name' => 'EWaktuWajibKaryaRekam', 'value' => $request->recording_work],
            ['name' => 'ECaptchaSecret', 'value' => $request->captcha_secret_key],
            ['name' => 'ECaptchaSite', 'value' => $request->captcha_site_key],
            ['name' => 'EAPIISBNToken', 'value' => $request->isbn_token],
            ['name' => 'EAPIISBNBaseUrl', 'value' => $request->isbn_base_url],
            ['name' => 'EAPIRajaOngkirToken', 'value' => $request->ro_token],
            ['name' => 'EAPIRajaOngkirBaseUrl', 'value' => $request->ro_base_url],
        ];
    }

    private function upsertSettingParameters(Request $request, array $auditData): void
    {
        $parameters = $this->buildSettingParameters($request);
        $names = array_column($parameters, 'name');
        $namesList = implode("','", $names);
        $existingRecords = QueryAPI::get("SELECT * FROM settingparameters WHERE name IN ('$namesList')") ?? [];
        $existingMap = [];

        foreach ($existingRecords as $record) {
            $id = $record->ID ?? $record->id ?? $record->Id ?? null;
            $name = $record->NAME ?? $record->name ?? $record->Name ?? null;

            if ($id && $name) {
                $existingMap[$name] = (int) $id ?? 0;
            }
        }

        foreach ($parameters as $param) {
            $name = $param['name'];
            $value = $param['value'];

            if ($value === null || $value === '') {
                continue;
            }

            $payload = array_merge($param, $auditData);
            $existing = isset($existingMap[$name]) ? $existingMap[$name] : null;

            if ($existing) {
                unset($payload['createby'], $payload['createdate'], $payload['createterminal']);

                QueryAPI::update('settingparameters', $existingMap[$name], $payload, false);
            } else {
                QueryAPI::create('settingparameters', $payload, false);
            }
        }
    }

    private function upsertMailConfiguration(Request $request, array $auditData): void
    {
        $payload = [
            'modul' => 'EDEPOSIT',
            'host' => $request->mail_host,
            'port' => $request->mail_port,
            'credentialmail' => $request->mail_username,
            'credentialpassword' => $request->mail_password,
            'enablessl' => 1,
            'mailfrom' => $request->mail_from,
            'maildisplayname' => $request->mail_name,
            'isactive' => 1,
        ];

        $existing = QueryAPI::get("SELECT * FROM mailserver WHERE modul = 'EDEPOSIT'", true);

        if ($existing) {
            $id = $existing->ID ?? $existing->id ?? $existing->Id ?? null;

            if ($id) {
                $updatePayload = array_merge($payload, [
                    'updateby' => $auditData['updateby'],
                    'updatedate' => $auditData['updatedate'],
                    'updateterminal' => $auditData['updateterminal'],
                ]);

                QueryAPI::update('mailserver', $id, $updatePayload, false);
            }
        } else {
            QueryAPI::create('mailserver', array_merge($payload, $auditData), false);
        }
    }

    private function buildObedientParameters(Request $request): array
    {
        return [
            ['name' => 'Patuh', 'persen' => $request->catalog_obedient],
            ['name' => 'Sebagian Patuh', 'persen' => $request->catalog_some_obey],
            ['name' => 'Tidak Patuh', 'persen' => $request->catalog_not_obey],
        ];
    }

    private function upsertObedientConfiguration(Request $request, array $auditData): void
    {
        $obedients = $this->buildObedientParameters($request);
        $names = array_column($obedients, 'name');
        $namesList = implode("','", $names);
        $existingRecords = QueryAPI::get("SELECT ID, name FROM e_kepatuhan_kckr WHERE name IN ('$namesList')") ?? [];
        $existingMap = [];

        foreach ($existingRecords as $record) {
            $id = $record->ID ?? $record->id ?? $record->Id ?? null;
            $name = $record->NAME ?? $record->name ?? $record->Name ?? null;

            if ($id && $name) {
                $existingMap[$name] = (int) $id ?? 0;
            }
        }

        foreach ($obedients as $obedient) {
            $name = $obedient['name'];
            $persen = $obedient['persen'];

            if ($persen === null || $persen === '') {
                continue;
            }

            $payload = array_merge($obedient, $auditData);
            $existing = isset($existingMap[$name]) ? $existingMap[$name] : null;

            if ($existing) {
                unset($payload['createby'], $payload['createdate'], $payload['createterminal']);

                QueryAPI::update('e_kepatuhan_kckr', $existingMap[$name], $payload, false);
            } else {
                QueryAPI::create('e_kepatuhan_kckr', $payload, false);
            }
        }
    }

    private function clearConfigurationCache(): void
    {
        Cache::forget('settings_params');
        Cache::forget('mail_config_edeposit');
        Cache::forget('obedient_kckr');
        Cache::forget(Main::CACHE_NAME_CONFIG_APP);
    }
}
