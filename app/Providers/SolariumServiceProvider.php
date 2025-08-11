<?php

namespace App\Providers;

use Solarium\Client;
use Solarium\Core\Client\Adapter\Curl;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\EventDispatcher\EventDispatcher;

class SolariumServiceProvider extends ServiceProvider {

    protected $defer = true;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Client::class, function() {
            return new Client(new Curl(), new EventDispatcher(), config('solr'));
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        return [Client::class];
    }
}
