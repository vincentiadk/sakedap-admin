<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RajaOngkir
{
    private static $token;
    private static $baseUrl;
    private static $cacheTime = 3600;

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
            $cachedData = Cache::get($cacheKey);

            Log::channel('rajaongkir')->info("GET Hit Cache [{$endpoint}]", [
                'payload' => $payload,
                'data' => $cachedData
            ]);

            return $cachedData;
        }

        static::initialize();

        try {
            $query = Http::baseUrl(static::$baseUrl)
                ->timeout(10)
                ->acceptJson()
                ->withHeaders(['key' => static::$token])
                ->get($endpoint, $payload);

            if ($query->failed()) {
                Log::channel('rajaongkir')->error("GET Gagal [HTTP {$query->status()}] [{$endpoint}]", [
                    'payload' => $payload,
                    'response' => $query->body()
                ]);

                return null;
            }

            $response = $query->object();

            if (isset($response->data)) {
                Cache::put($cacheKey, $response->data, static::$cacheTime);

                Log::channel('rajaongkir')->info("GET Sukses [{$endpoint}]", [
                    'payload' => $payload,
                    'response' => $response
                ]);

                return $response->data;
            }

            Log::channel('rajaongkir')->warning("GET Sukses Tanpa Data Kompleks [{$endpoint}]", [
                'payload' => $payload,
                'response' => $response
            ]);

            return null;
        } catch (\Exception $e) {
            Log::channel('rajaongkir')->critical("GET Exception Error [{$endpoint}]", [
                'payload' => $payload,
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
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
            $cachedData = Cache::get($cacheKey);

            Log::channel('rajaongkir')->info("POST Hit Cache [{$endpoint}]", [
                'payload' => $payload,
                'data' => $cachedData
            ]);

            return $cachedData;
        }

        static::initialize();

        try {
            $query = Http::baseUrl(static::$baseUrl)
                ->timeout(10)
                ->asForm()
                ->withHeaders(['key' => static::$token])
                ->post($endpoint, $payload);

            if ($query->failed()) {
                Log::channel('rajaongkir')->error("POST Gagal [HTTP {$query->status()}] [{$endpoint}]", [
                    'payload' => $payload,
                    'response' => $query->body()
                ]);

                return null;
            }

            $response = $query->object();

            if (isset($response->data)) {
                Cache::put($cacheKey, $response->data, static::$cacheTime);

                Log::channel('rajaongkir')->info("POST Sukses [{$endpoint}]", [
                    'payload' => $payload,
                    'response' => $response
                ]);

                return $response->data;
            }

            Log::channel('rajaongkir')->warning("POST Sukses Tanpa Data Kompleks [{$endpoint}]", [
                'payload' => $payload,
                'response' => $response
            ]);

            return null;
        } catch (\Exception $e) {
            Log::channel('rajaongkir')->critical("POST Exception Error [{$endpoint}]", [
                'payload' => $payload,
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return null;
        }
    }
}
