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
     * @param  mixed $attachment
     * @return void
     */
    public static function send($target, $message, $attachment = null)
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
                Log::channel('fonnte')->info("Format nomor salah: $target");

                return (object) [
                    'code' => 401,
                    'message' => 'Format nomor invalid',
                    'data' => null
                ];
            }

            $postData = [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62',
            ];

            $http = Http::withHeaders([
                'Authorization' => $token,
            ]);

            $hasFile = false;
            $filePath = null;

            if (is_string($attachment) && file_exists($attachment)) {
                $hasFile = true;
                $filePath = $attachment;
            } else if (is_array($attachment) && isset($attachment['path']) && file_exists($attachment['path'])) {
                $hasFile = true;
                $filePath = $attachment['path'];
            } else if ((is_string($attachment) && filter_var($attachment, FILTER_VALIDATE_URL)) || (is_array($attachment) && isset($attachment['url']))) {
                $url = is_array($attachment) ? $attachment['url'] : $attachment;
                $postData['url'] = $url;
            }

            if ($hasFile) {
                $fileStream = fopen($filePath, 'r');
                $fileName = basename($filePath);

                $response = $http->attach('file', $fileStream, $fileName)->post($baseUrl, $postData);
            } else {
                $response = $http->post($baseUrl, $postData);
            }

            return (object) [
                'code' => 201,
                'message' => 'Pesan terkirim',
                'data' => $response->object()
            ];
        } catch (\Exception $e) {
            Log::channel('fonnte')->error('Gagal mengirim pesan', [
                'error' => $e->getMessage(),
                'target' => $target ?? null,
            ]);

            return (object) [
                'code' => 500,
                'message' => 'Gagal mengirim pesan: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
}
