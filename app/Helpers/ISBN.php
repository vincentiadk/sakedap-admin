<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ISBN
{
    private static $token;
    private static $baseUrl;
    private static $cacheTime = 60 * 60 * 24;

    /**
     * initialize
     *
     * @return void
     */
    public static function initialize()
    {
        static::$token = config('isbn.token');
        static::$baseUrl = config('isbn.base_url');
    }

    /**
     * get
     *
     * @param  mixed $endpoint
     * @param  mixed $payload
     * @param  mixed $single
     * @return void
     */
    public static function get($endpoint, $payload = [], $single = false)
    {
        $cacheKey = 'isbn_get_' . $endpoint . '_' . $single . '_' . md5(json_encode($payload));

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        static::initialize();

        $data = null;
        $query = Http::baseUrl(static::$baseUrl)
            ->withToken(static::$token)
            ->withoutVerifying()
            ->get($endpoint, $payload);
        if ($query->status() == 200) {
            $response = $query->object();

            if (count($response->data) > 0) {
                if ($single == true) {
                    $data = $response->data[0];
                } else {
                    $data = $response;
                }

                Cache::put($cacheKey, $data, static::$cacheTime);
            }
        } else {
            Log::channel('isbn-api')->error('Gagal get endpoint', $query->json());
        }

        return $data;
    }
}
