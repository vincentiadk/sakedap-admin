<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class RajaOngkir
{
    private static $token;
    private static $baseUrl;
    private static $cacheTime = 60 * 60;

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
        $cacheKey = 'rajaongkir_get_' . $endpoint . '_' . md5(json_encode($payload));

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        static::initialize();

        $query = Http::baseUrl(static::$baseUrl)
            ->acceptJson()
            ->withHeaders([
                'key' => static::$token
            ])
            ->get($endpoint, $payload);

        $response = $query->object();

        if ($query->successful() && isset($response->data)) {
            Cache::put($cacheKey, $response->data, static::$cacheTime);
        }

        return $response->data ?? null;
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
        $cacheKey = 'rajaongkir_post_' . $endpoint . '_' . md5(json_encode($payload));

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        static::initialize();

        $query = Http::baseUrl(static::$baseUrl)
            ->asForm()
            ->withHeaders([
                'key' => static::$token
            ])
            ->post($endpoint, $payload);

        $response = $query->object();

        if ($query->successful() && isset($response->data)) {
            Cache::put($cacheKey, $response->data, static::$cacheTime);
        }

        return $response->data ?? null;
    }
}
