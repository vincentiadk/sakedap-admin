<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ISBN
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
        static::initialize();

        $data = null;
        $query = Http::baseUrl(static::$baseUrl)
            ->withToken(static::$token)
            ->withoutVerifying()
            ->get($endpoint, $payload);

        $response = $query->object();

        if ($query->status() == 200) {

            if (isset($response->data)) {
                if (count($response->data) > 0) {
                    if ($single == true) {
                        $data = $response->data[0];
                    } else {
                        $data = $response;
                    }
                }
            } else {
                $data = $response;
            }
        } else {
            Log::channel('isbn-api')->error('Gagal get endpoint', [
                'payload' => $payload,
                'response' => $response,
                'message' => $query->body()
            ]);
        }

        return $data;
    }
}
