<?php

namespace App\Providers;

use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class ConfigurationProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $ttl = now()->addMinutes(60);
        $settings = Cache::remember(Main::CACHE_NAME_CONFIG_APP, $ttl, function () {
            $configParam = array_map(function ($name) {
                return "'" . $name . "'";
            }, Main::CONFIG_PARAM);

            $settingParameterName = implode(',', $configParam);
            $settingParameter = QueryAPI::get("select * from settingparameters where name in ($settingParameterName)");
            $mail = QueryAPI::get("select * from mailserver where modul = 'EDEPOSIT'", true);
            $collectedSettings = [];

            if ($settingParameter) {
                $sp = collect($settingParameter);
                $collectedSettings['inlis.aes_key'] = $sp->firstWhere('NAME', 'EAesInlisKey')->VALUE ?? null;
                $collectedSettings['inlis.aes_iv'] = $sp->firstWhere('NAME', 'EAesInlisIV')->VALUE ?? null;

                $collectedSettings['database.redis.client'] = $sp->firstWhere('NAME', 'ERedisClient')->VALUE ?? 'phpredis';
                $collectedSettings['database.redis.default.host'] = $sp->firstWhere('NAME', 'ERedisHost')->VALUE ?? '127.0.0.1';
                $collectedSettings['database.redis.default.username'] = $sp->firstWhere('NAME', 'ERedisUsername')->VALUE ?? null;
                $collectedSettings['database.redis.default.password'] = $sp->firstWhere('NAME', 'ERedisPassword')->VALUE ?? null;
                $collectedSettings['database.redis.default.port'] = $sp->firstWhere('NAME', 'ERedisPort')->VALUE ?? '6379';

                $collectedSettings['session.driver'] = $sp->firstWhere('NAME', 'ESessionDriver')->VALUE ?? 'redis';
                $collectedSettings['session.lifetime'] = (int) ($sp->firstWhere('NAME', 'ESessionLifeTime')->VALUE ?? 120);
                $collectedSettings['session.encrypt'] = ($sp->firstWhere('NAME', 'ESessionEncrypt')->VALUE ?? null) == 1;

                $collectedSettings['captcha.secret'] = $sp->firstWhere('NAME', 'ECaptchaSecret')->VALUE ?? null;
                $collectedSettings['captcha.sitekey'] = $sp->firstWhere('NAME', 'ECaptchaSite')->VALUE ?? null;

                $collectedSettings['system.retry_login'] = (int) ($sp->firstWhere('NAME', 'EPercobaanLogin')->VALUE ?? 3);
                $collectedSettings['system.aes_key'] = $sp->firstWhere('NAME', 'EAesKey')->VALUE ?? null;
                $collectedSettings['system.aes_iv'] = $sp->firstWhere('NAME', 'EAesIV')->VALUE ?? null;
                $collectedSettings['system.iframe_domain'] = $sp->firstWhere('NAME', 'EIFrameDomain')->VALUE ?? null;
                $collectedSettings['system.catalog_cover_max_upload'] = ($sp->firstWhere('NAME', 'EKatalogCoverMaxUpload')->VALUE ?? 2) * 1024;
                $collectedSettings['system.catalog_content_max_upload'] = ($sp->firstWhere('NAME', 'EKatalogContentMaxUpload')->VALUE ?? 200) * 1024;
                $collectedSettings['system.limit_submission_kckr'] = (int) ($sp->firstWhere('NAME', 'EBatasSerahKCKR')->VALUE ?? 3);
                $collectedSettings['system.limit_grant'] = (int) ($sp->firstWhere('NAME', 'EBatasHibah')->VALUE ?? 3);
                $collectedSettings['system.limit_retur'] = (int) ($sp->firstWhere('NAME', 'EBatasPengambilan')->VALUE ?? 3);

                $collectedSettings['isbn.token'] = $sp->firstWhere('NAME', 'EAPIISBNToken')->VALUE ?? null;
                $collectedSettings['isbn.base_url'] = $sp->firstWhere('NAME', 'EAPIISBNBaseUrl')->VALUE ?? null;
                $collectedSettings['raja-ongkir.token'] = $sp->firstWhere('NAME', 'EAPIRajaOngkirToken')->VALUE ?? null;
                $collectedSettings['raja-ongkir.base_url'] = $sp->firstWhere('NAME', 'EAPIRajaOngkirBaseUrl')->VALUE ?? null;
            }

            if ($mail) {
                $collectedSettings['mail.mailers.smtp.host'] = $mail->HOST ?? '127.0.0.1';
                $collectedSettings['mail.mailers.smtp.port'] = $mail->PORT ?? 2525;
                $collectedSettings['mail.mailers.smtp.username'] = $mail->CREDENTIALMAIL ?? null;
                $collectedSettings['mail.mailers.smtp.password'] = $mail->CREDENTIALPASSWORD ?? null;
                $collectedSettings['mail.from.address'] = $mail->MAILFROM ?? 'hello@example.com';
                $collectedSettings['mail.from.name'] = $mail->MAILDISPLAYNAME ?? 'Example';
            }

            return $collectedSettings;
        });

        foreach ($settings as $key => $value) {
            Config::set($key, $value);
        }
    }
}
