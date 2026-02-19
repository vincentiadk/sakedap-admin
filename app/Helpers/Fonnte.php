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
                $target = '62' . substr($target, 1);
            } else if (substr($target, 0, 3) === '+62') {
                $target = '62' . substr($target, 3);
            } else if (substr($target, 0, 2) !== '62') {
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
            $isBinary = false;
            $binaryData = null;
            $customFileName = null;

            if (is_string($attachment) && file_exists($attachment)) {
                $hasFile = true;
                $filePath = $attachment;
            } else if (is_array($attachment) && isset($attachment['path']) && file_exists($attachment['path'])) {
                $hasFile = true;
                $filePath = $attachment['path'];
            } else if (is_array($attachment) && isset($attachment['binary'])) {
                $hasFile = true;
                $isBinary = true;
                $binaryData = $attachment['binary'];
                $customFileName = $attachment['filename'] ?? 'file.pdf';
            } else if ((is_string($attachment) && filter_var($attachment, FILTER_VALIDATE_URL)) || (is_array($attachment) && isset($attachment['url']))) {
                $url = is_array($attachment) ? $attachment['url'] : $attachment;
                $postData['url'] = $url;
            }

            if ($hasFile) {
                if ($isBinary) {
                    $response = $http->attach('file', $binaryData, $customFileName)->post($baseUrl, $postData);
                } else {
                    $fileStream = fopen($filePath, 'r');
                    $fileName = basename($filePath);
                    $response = $http->attach('file', $fileStream, $fileName)->post($baseUrl, $postData);
                }
            } else {
                $response = $http->post($baseUrl, $postData);
            }

            return (object) [
                'code' => 201,
                'message' => 'Pesan terkirim',
                'data' => $response->object()
            ];
        } catch (\Exception $e) {
            Log::channel('fonnte')->error($e->getMessage() ?? 'Error tidak diketahui saat mengirim pesan');

            return (object) [
                'code' => 500,
                'message' => 'Gagal mengirim pesan: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
}
