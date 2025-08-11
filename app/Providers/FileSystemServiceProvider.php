<?php

namespace App\Providers;

use App\Models\Location;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class FileSystemServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if (App::environment('production')) {
            Location::all()->each(function (Location $myDisk) {
                if (!is_null($myDisk->location) && !is_null($myDisk->driver)) {
                    $this->app['config']["filesystems.disks.{$myDisk->location}"] =
                        [
                            'driver' => $myDisk->driver,
                            'host' => $myDisk->host,
                            'username' => $myDisk->username,
                            'password' => $myDisk->password,
                            'root' => $myDisk->root,
                        ];
                }
            });
        }
    }
}
