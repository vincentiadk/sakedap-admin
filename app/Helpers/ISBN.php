<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;
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
        static::$token = App::environment('production') ? config('isbn.prod.token') : config('isbn.dev.token');
        static::$baseUrl = App::environment('production') ? config('isbn.prod.base_url') : config('isbn.dev.base_url');
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
            ->get($endpoint, $payload);

        if ($query->status() == 200) {
            $response = $query->object();

            if (count($response->data) > 0) {
                if ($single == true) {
                    $data = $response->data[0];
                } else {
                    $data = $response;
                }
            }
        } else {
            dd($query->object());
        }

        return $data;
    }
}
