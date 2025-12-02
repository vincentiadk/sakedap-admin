<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class Komship
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
        static::$token = config('komship.token');
        static::$baseUrl = config('komship.base_url');
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
        $cacheKey = 'komship_get_' . $endpoint . '_' . md5(json_encode($payload));

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        static::initialize();

        $query = Http::baseUrl(static::$baseUrl)
            ->acceptJson()
            ->withHeaders([
                'x-api-key' => static::$token
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
        static::initialize();

        $query = Http::baseUrl(static::$baseUrl)
            ->asJson()
            ->withHeaders([
                'x-api-key' => static::$token,
                'Accept' => 'application/json'
            ])
            ->post($endpoint, $payload);

        $response = $query->object();

        return $response->data ?? null;
    }
}
