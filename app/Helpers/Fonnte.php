<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class Fonnte
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
        static::$token = config('fonnte.token');
        static::$baseUrl = config('fonnte.base_url');
    }

    /**
     * send
     *
     * @param  mixed $target
     * @param  mixed $message
     * @return void
     */
    public static function send($target, $message)
    {
        static::initialize();

        try {
            $token = static::$token;
            $baseUrl = static::$baseUrl;
            $target = preg_replace('/[^0-9]/', '', $target);

            if (substr($target, 0, 1) === '0') {
                $target = '+62' . substr($target, 1);
            } elseif (substr($target, 0, 2) === '62') {
                $target = '+' . $target;
            } else if (substr($target, 0, 3) !== '+62') {
                Log::channel('fonnte')->info('Format nomor tidak valid. Gunakan 08xxxxx atau 62xxxxx', [
                    'nomor' => $target,
                    'pesan' => $message
                ]);

                return (object) [
                    'code' => 401,
                    'message' => 'Format nomor tidak valid. Gunakan 08xxxxx atau 62xxxxx',
                    'data' => (object) [
                        'target' => $target,
                        'message' => $message
                    ]
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post($baseUrl, [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62',
            ]);

            return (object) [
                'code' => 201,
                'message' => 'Pesan berhasil dikirim',
                'data' => $response->object()
            ];
        } catch (\Exception $e) {
            Log::channel('fonnte')->error('Gagal mengirim pesan', [
                'error' => $e->getMessage(),
                'target' => $targetFormatted ?? null,
                'body' => $message
            ]);

            return (object) [
                'code' => $e->getCode() ?? 500,
                'message' => 'Gagal mengirim pesan',
                'data' => (object) [
                    'error' => $e->getMessage(),
                    'target' => $targetFormatted ?? null,
                    'body' => $message
                ]
            ];
        }
    }
}
