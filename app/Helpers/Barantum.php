<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class Barantum
{
    private static $token;
    private static $baseUrl;
    private static $companyUuid;
    private static $channel;

    /**
     * initialize
     *
     * @return void
     */
    public static function initialize()
    {
        static::$token = config('barantum.token');
        static::$baseUrl = config('barantum.base_url');
        static::$companyUuid = config('barantum.company_uuid');
        static::$channel = config('barantum.channel');
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
            $target = preg_replace('/[^0-9]/', '', $target);

            if (substr($target, 0, 1) === '0') {
                $target = '62' . substr($target, 1);
            }

            $payload = [
                'company_uuid' => static::$companyUuid,
                'chats_users_id' => $target,
                'channel' => static::$channel,
            ];

            if ($attachment) {
                $fileUrl = '';

                if (is_string($attachment)) {
                    $fileUrl = filter_var($attachment, FILTER_VALIDATE_URL) ? $attachment : asset(str_replace(public_path(), '', $attachment));
                } else if (is_array($attachment) && isset($attachment['url'])) {
                    $fileUrl = $attachment['url'];
                }

                if (empty($fileUrl)) {
                    throw new \Exception("URL File tidak valid atau tidak ditemukan.");
                }

                $payload['type'] = 'media';
                $payload['media'] = [
                    'caption' => $message,
                    'link' => $fileUrl,
                    'filename' => basename($fileUrl)
                ];
            } else {
                $payload['type'] = 'text';
                $payload['chats_message_text'] = $message;
            }

            $response = Http::withToken(static::$token)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post(static::$baseUrl, $payload);

            if ($response->successful()) {
                return (object) [
                    'code'    => 200,
                    'message' => 'Pesan terkirim ke Barantum',
                    'data'    => $response->json()
                ];
            }

            throw new \Exception("Barantum API Error: " . $response->status() . " - " . $response->body());
        } catch (\Exception $e) {
            Log::channel('barantum')->error('Barantum Gateway Error: ' . $e->getMessage());

            return (object) [
                'code' => 500,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }
}
