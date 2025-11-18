<?php

namespace App\Helpers;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class Twilio
{
    private static $sid;
    private static $authToken;
    private static $from;

    /**
     * initialize
     *
     * @return void
     */
    public static function initialize()
    {
        static::$sid = config('twilio.sid');
        static::$authToken = config('twilio.auth_token');
        static::$from = config('twilio.from');
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

        $sid = static::$sid;
        $token = static::$authToken;
        $from = 'whatsapp:' . static::$from;
        $target = preg_replace('/[^0-9]/', '', $target);

        if (substr($target, 0, 1) === '0') {
            $target = '+62' . substr($target, 1);
        } elseif (substr($target, 0, 2) === '62') {
            $target = '+' . $target;
        } else if (substr($target, 0, 3) !== '+62') {
            Log::channel('twilio')->info('Format nomor tidak valid. Gunakan 08xxxxx atau 62xxxxx', [
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

        $targetFormatted = "whatsapp:" . $target;

        try {
            $twilio = new Client($sid, $token);
            $result = $twilio->messages->create($targetFormatted, [
                'from' => $from,
                'body' => $message
            ]);

            return (object) [
                'code' => 201,
                'message' => 'Pesan berhasil dikirim',
                'data' => (object) [
                    'sid' => $result->sid ?? null,
                    'target' => $targetFormatted,
                    'body' => $message
                ]
            ];
        } catch (\Exception $e) {
            Log::channel('twilio')->error('Gagal mengirim pesan', [
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
