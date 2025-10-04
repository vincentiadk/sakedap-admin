<?php

namespace App\Providers;

use App\Helpers\Main;
use App\Helpers\QueryAPI;
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
        $configParam = array_map(function ($name) {
            return "'" . $name . "'";
        }, Main::CONFIG_PARAM);

        $settingParameterName = implode(',', $configParam);
        $settingParameter = QueryAPI::get("select * from settingparameters where name in ($settingParameterName)");
        $mail = QueryAPI::get("select * from mailserver where modul = 'EDEPOSIT'", true);

        if ($settingParameter) {
            $sp = collect($settingParameter);

            config('inlis.aes_key', $sp->firstWhere('NAME', 'EAesInlisKey')->VALUE ?? null);
            config('inlis.aes_iv', $sp->firstWhere('NAME', 'EAesInlisIV')->VALUE ?? null);

            config('database.redis.client', $sp->firstWhere('NAME', 'ERedisClient')->VALUE ?? 'phpredis');
            config('database.redis.default.host', $sp->firstWhere('NAME', 'ERedisHost')->VALUE ?? '127.0.0.1');
            config('database.redis.default.username', $sp->firstWhere('NAME', 'ERedisUsername')->VALUE ?? null);
            config('database.redis.default.password', $sp->firstWhere('NAME', 'ERedisPassword')->VALUE ?? null);
            config('database.redis.default.port', $sp->firstWhere('NAME', 'ERedisPort')->VALUE ?? '6379');

            config('session.driver', $sp->firstWhere('NAME', 'ESessionDriver')->VALUE ?? 'redis');
            config('session.lifetime', (int) ($sp->firstWhere('NAME', 'ESessionLifeTime')->VALUE ?? 120));
            config('session.encrypt', ($sp->firstWhere('NAME', 'ESessionEncrypt')->VALUE ?? null) == 1 ? true : false);

            config('captcha.secret', $sp->firstWhere('NAME', 'ECaptchaSecret')->VALUE ?? null);
            config('captcha.sitekey', $sp->firstWhere('NAME', 'ECaptchaSite')->VALUE ?? null);

            config('system.retry_login', $sp->firstWhere('NAME', 'EPercobaanLogin')->VALUE ?? 3);
            config('system.aes_key', $sp->firstWhere('NAME', 'EAesKey')->VALUE ?? null);
            config('system.aes_iv', $sp->firstWhere('NAME', 'EAesIV')->VALUE ?? null);
            config('system.iframe_domain', $sp->firstWhere('NAME', 'EIFrameDomain')->VALUE ?? null);
            config('system.catalog_cover_max_upload', ($sp->firstWhere('NAME', 'EKatalogCoverMaxUpload')->VALUE ?? 2) * 1024);
            config('system.catalog_content_max_upload', ($sp->firstWhere('NAME', 'EKatalogContentMaxUpload')->VALUE ?? 200) * 1024);
            config('system.limit_submission_kckr', $sp->firstWhere('NAME', 'EBatasSerahKCKR')->VALUE ?? 3);
            config('system.limit_grant', $sp->firstWhere('NAME', 'EBatasHibah')->VALUE ?? 3);

            config('isbn.token', $sp->firstWhere('NAME', 'EAPIISBNToken')->VALUE ?? null);
            config('isbn.base_url', $sp->firstWhere('NAME', 'EAPIISBNBaseUrl')->VALUE ?? null);

            config('raja-ongkir.token', $sp->firstWhere('NAME', 'EAPIRajaOngkirToken')->VALUE ?? null);
            config('raja-ongkir.base_url', $sp->firstWhere('NAME', 'EAPIRajaOngkirBaseUrl')->VALUE ?? null);
        }

        if ($mail) {
            config('mail.mailers.smtp.host', $mail->HOST ?? '127.0.0.1');
            config('mail.mailers.smtp.port', $mail->PORT ?? 2525);
            config('mail.mailers.smtp.username', $mail->CREDENTIALMAIL ?? null);
            config('mail.mailers.smtp.password', $mail->CREDENTIALPASSWORD ?? null);
            config('mail.from.address', $mail->MAILFROM ?? 'hello@example.com');
            config('mail.from.name', $mail->MAILDISPLAYNAME ?? 'Example');
        }
    }
}
