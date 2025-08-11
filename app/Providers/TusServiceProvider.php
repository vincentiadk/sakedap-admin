<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use TusPhp\Tus\Server as TusServer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class TusServiceProvider extends ServiceProvider
{
    // ...

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        if (App::environment('production')) {
            $this->app->singleton('tus-server', function ($app) {
                $server = new TusServer();
                $location = DB::connection('mysql')->table('locations')->where('active', 1)->first()->location;

                if (!file_exists(Storage::disk($location)->path('public/tmp'))) {
                    mkdir(Storage::disk($location)->path('public/tmp'), 0777, true);
                }

                $server->setApiPath('/tus') // tus server endpoint.
                    ->setUploadDir(Storage::disk($location)->path('public/tmp')); // uploads dir.

                return $server;
            });
        }
    }

    // ...
}
