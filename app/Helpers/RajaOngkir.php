<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;

class RajaOngkir
{
    private static $token;
    private static $baseUrl;

    /**
     * initialize
     *
     * @return void
     */
    public static function initialize()
    {
        static::$token = App::environment('production') ? config('raja-ongkir.prod.token') : config('raja-ongkir.dev.token');
        static::$baseUrl = App::environment('production') ? config('raja-ongkir.prod.base_url') : config('raja-ongkir.dev.base_url');
    }

    /**
     * get
     *
     * @param  mixed $endpoint
     * @param  mixed $payload
     * @return void
     */
    public static function get($endpoint, $payload = [])
    {
        static::initialize();

        $data = null;
        $query = Http::baseUrl(static::$baseUrl)
            ->acceptJson()
            ->withHeaders([
                'key' => static::$token
            ])
            ->get($endpoint, $payload);

        if ($query->status() == 200) {
            $response = $query->object();

            if ($response->meta->code == 200) {
                return $response->data;
            }
        } else {
            dd($query->object());
        }

        return $data;
    }

    /**
     * post
     *
     * @param  mixed $endpoint
     * @param  mixed $payload
     * @return void
     */
    public static function post($endpoint, $payload = [])
    {
        static::initialize();

        $data = null;
        $query = Http::baseUrl(static::$baseUrl)
            ->asForm()
            ->withHeaders([
                'key' => static::$token
            ])
            ->post($endpoint, $payload);

        if ($query->status() == 200) {
            $response = $query->object();

            if ($response->meta->code == 200) {
                return $response->data;
            }
        } else {
            dd($query->object());
        }

        return $data;
    }
}
