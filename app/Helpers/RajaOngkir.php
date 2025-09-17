<?php

namespace App\Helpers;

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
        static::$token = config('raja-ongkir.token');
        static::$baseUrl = config('raja-ongkir.base_url');
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

        $query = Http::baseUrl(static::$baseUrl)
            ->acceptJson()
            ->withHeaders([
                'key' => static::$token
            ])
            ->get($endpoint, $payload);

        $response = $query->object();

        return $response->data;
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

        $query = Http::baseUrl(static::$baseUrl)
            ->asForm()
            ->withHeaders([
                'key' => static::$token
            ])
            ->post($endpoint, $payload);

        $response = $query->object();

        return $response->data;
    }
}
