<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class Barantum
{
    private static $baseUrl;
    private static $companyUuid;
    private static $chatBotId;
    private static $templateId;

    public static function initialize()
    {
        static::$baseUrl = config('barantum.base_url');
        static::$companyUuid = config('barantum.company_uuid');
        static::$chatBotId = config('barantum.chat_bot_id');
        static::$templateId = config('barantum.template_id');
    }

    public static function send($number, $name, $variables = [], $attachmentUrl = null)
    {
        static::initialize();

        try {
            $number = preg_replace('/[^0-9]/', '', $number);

            if (substr($number, 0, 1) === '0') {
                $number = '62' . substr($number, 1);
            } else if (substr($number, 0, 1) === '8') {
                $number = '62' . $number;
            }

            $formattedVariables = [];

            foreach ($variables as $index => $value) {
                $formattedVariables["{{" . ($index + 1) . "}}"] = $value;
            }

            $payload = [
                'company_uuid' => static::$companyUuid,
                'template_uuid' => static::$templateId,
                'chat_bot_uuid' => static::$chatBotId,
                'content_header' => $attachmentUrl ?? "",
                'contacts' => [
                    [
                        'user_name' => $name,
                        'number' => $number,
                        'variabel' => $formattedVariables
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post(static::$baseUrl, $payload);

            if ($response->successful()) {
                return (object) [
                    'code' => 200,
                    'message' => 'Sukses',
                    'data' => $response->object()
                ];
            }

            throw new \Exception("Barantum API Error: " . $response->body());
        } catch (\Exception $e) {
            Log::channel('barantum')->error('Barantum Custom Error: ' . $e->getMessage());

            return (object) [
                'code' => 500,
                'message' => $e->getMessage()
            ];
        }
    }
}
