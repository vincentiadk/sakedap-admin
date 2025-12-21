<?php

namespace App\Helpers;

use App\Helpers\Main;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class QueryAPI
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
        static::$token = config('inlis.token');
        static::$baseUrl = config('inlis.api_url');
    }

    /**
     * login
     *
     * @param  mixed $username
     * @param  mixed $password
     * @return void
     */
    public static function login($username, $password)
    {
        static::initialize();

        $query = Http::connectTimeout(0)
            ->timeout(0)
            ->withQueryParameters([
                'token' => static::$token,
                'op' => 'isloginvalid',
                'UserName' => $username,
                'UserPassword' => $password,
            ])
            ->post(static::$baseUrl);

        return $query->object();
    }

    /**
     * query
     *
     * @param  mixed $sql
     * @return void
     */
    public static function query($sql)
    {
        static::initialize();

        $data = null;
        $query = Http::connectTimeout(0)
            ->timeout(0)
            ->withQueryParameters([
                'token' => static::$token,
                'op' => 'getlistraw',
                'sql' => $sql
            ])
            ->post(static::$baseUrl);

        if ($query->status() == 200) {
            $data = $query->object();
        }

        return $data;
    }


    /**
     * get
     *
     * @param  mixed $sql
     * @param  mixed $single
     * @return void
     */
    public static function get($sql, $single = false)
    {
        static::initialize();

        $data = null;
        $query = Http::connectTimeout(0)
            ->timeout(0)
            ->withQueryParameters([
                'token' => static::$token,
                'op' => 'getlistraw',
                'sql' => $sql
            ])
            ->post(static::$baseUrl);

        if ($query->status() == 200) {
            $response = $query->object();

            if ($response->Status == 'Success') {
                $result = $response->Data->Items;

                if ($result) {
                    if (count($result) > 0) {
                        if ($single == true) {
                            $data = $result[0];
                        } else {
                            $data = $result;
                        }
                    }
                }
            } else {
                Log::channel('sakedap-api')->error('Gagal kueri', [$response, $sql]);
            }
        }

        return $data;
    }

    /**
     * create
     *
     * @param  mixed $table
     * @param  mixed $payload
     * @return void
     */
    public static function create($table, $payload = [], $withTimestamp = true)
    {
        static::initialize();

        $bodyJson = [];

        if ($payload) {
            foreach ($payload as $key => $p) {
                $bodyJson[] = [
                    'Name' => $key,
                    'Value' => $p
                ];
            }
        }

        if ($withTimestamp) {
            $bodyJson[] = [
                'Name' => 'created_at',
                'Value' => date('Y-m-d H:i:s')
            ];

            $bodyJson[] = [
                'Name' => 'updated_at',
                'Value' => date('Y-m-d H:i:s')
            ];
        }

        $data = [];
        $query = Http::connectTimeout(0)
            ->timeout(0)
            ->asForm()
            ->post(static::$baseUrl, [
                'token' => static::$token,
                'op' => 'add',
                'table' => $table,
                'issavehistory' => 1,
                'ListAddItem' => json_encode($bodyJson)
            ]);

        if ($query->status() == 200) {
            $response = $query->object();

            if ($response->Status == 'Success') {
                $data = $response->Data;
            } else {
                Log::channel('sakedap-api')->error('Gagal insert', $query->json());
            }
        }

        return $data;
    }

    /**
     * update
     *
     * @param  mixed $table
     * @param  mixed $id
     * @param  mixed $payload
     * @return void
     */
    public static function update($table, $id, $payload = [], $withTimestamp = true)
    {
        static::initialize();

        $bodyJson = [];

        if ($payload) {
            foreach ($payload as $key => $p) {
                $bodyJson[] = [
                    'Name' => $key,
                    'Value' => $p
                ];
            }
        }

        if ($withTimestamp) {
            $bodyJson[] = [
                'Name' => 'updated_at',
                'Value' => date('Y-m-d H:i:s')
            ];
        }

        $data = false;
        $query = Http::connectTimeout(0)
            ->timeout(0)
            ->asForm()
            ->post(static::$baseUrl, [
                'token' => static::$token,
                'op' => 'update',
                'table' => $table,
                'id' => $id,
                'issavehistory' => 1,
                'ListUpdateItem' => json_encode($bodyJson)
            ]);

        if ($query->status() == 200) {
            $response = $query->object();

            if ($response->Status == 'Success') {
                $data = true;
            } else {
                Log::channel('sakedap-api')->error('Gagal update', $query->json());
            }
        }

        return $data;
    }

    /**
     * delete
     *
     * @param  mixed $table
     * @param  mixed $id
     * @return void
     */
    public static function delete($table, $id)
    {
        static::initialize();

        $data = false;
        $query = Http::connectTimeout(0)
            ->timeout(0)
            ->withQueryParameters([
                'token' => static::$token,
                'op' => 'delete',
                'table' => $table,
                'id' => $id,
            ])
            ->post(static::$baseUrl);

        if ($query->status() == 200) {
            $response = $query->object();

            if ($response->Status == 'Success') {
                $data = true;
            } else {
                Log::channel('sakedap-api')->error('Gagal hapus', $query->json());
            }
        }

        return $data;
    }

    /**
     * uploadFile
     *
     * @param  mixed $payload
     * @return void
     */
    public static function uploadFile($payload = [])
    {
        static::initialize();

        $data = false;
        $param = array_merge($payload, [
            'token' => static::$token,
            'op' => 'uploadfile',
            'uploadby' => session('username'),
            'terminal' => request()->ip(),
        ]);

        $fileInput = $payload['file'];
        $fileContent = null;
        $filename = 'uploaded_file';

        if (isset($payload['filename'])) {
            $filename = $payload['filename'];
        } else if (is_object($fileInput) && method_exists($fileInput, 'getClientOriginalName')) {
            $filename = $fileInput->getClientOriginalName();
        }

        if (is_resource($fileInput)) {
            $fileContent = stream_get_contents($fileInput);

            if (is_resource($fileInput)) {
                fclose($fileInput);
            }
        } else if (is_object($fileInput) && method_exists($fileInput, 'getRealPath')) {
            $path = $fileInput->getRealPath();
            $fileContent = file_get_contents($path);
        } else if (is_string($fileInput)) {
            $fileContent = file_get_contents($fileInput);
        }

        if ($fileContent === null) {
            Log::channel('sakedap-api')->error('Gagal upload: File content is NULL and cannot be processed.', $payload);

            return false;
        }

        $query = Http::connectTimeout(0)
            ->timeout(0)
            ->attach('file', $fileContent, $filename)
            ->withQueryParameters($param)
            ->post(static::$baseUrl);

        if ($query->status() == 200) {
            $response = $query->object();

            if ($response->Status == 'Success') {
                $data = isset($response->Data) ? $response->Data : true;
            } else {
                Log::channel('sakedap-api')->error('Gagal upload file', $query->json());
            }
        }

        return $data;
    }

    public static function removeFile($payload = [])
    {
        static::initialize();

        $data = false;
        $param = array_merge($payload, [
            'token' => static::$token,
            'op' => 'deletefile',
            'actionby' => session('username'),
            'terminal' => request()->ip(),
        ]);

        $query = Http::connectTimeout(0)
            ->timeout(0)
            ->withQueryParameters($param)
            ->post(static::$baseUrl);

        if ($query->status() == 200) {
            $response = $query->object();

            if ($response->Status == 'Success') {
                $data = true;
            } else {
                Log::channel('sakedap-api')->error('Gagal hapus file', $query->json());
            }
        }

        return $data;
    }

    /**
     * getFile
     *
     * @param  mixed $payload
     * @return void
     */
    public static function getFile($payload = [])
    {
        static::initialize();

        $data = false;
        $param = array_merge($payload, [
            'token' => static::$token,
            'op' => 'getfile',
        ]);

        try {
            $query = Http::connectTimeout(0)
                ->timeout(0)
                ->withQueryParameters($param)
                ->withOptions(['stream' => true])
                ->post(static::$baseUrl);

            if ($query->status() == 200) {
                $contentType = $query->header('Content-Type');

                if (str_contains($contentType, 'application/json') || str_contains($contentType, 'text/plain')) {
                    $errorBody = $query->body();

                    Log::channel('sakedap-api')->error('GetFile 200 OK but response is JSON/Text (Logic Error)', [
                        'content_type' => $contentType,
                        'response_body' => $errorBody,
                        'payload' => $payload
                    ]);
                } else if ($query->header('Content-Length') === '0') {
                    Log::channel('sakedap-api')->error('GetFile 200 OK but Content-Length is 0', [
                        'payload' => $payload
                    ]);
                } else {
                    $result = $query->toPsrResponse()->getBody();

                    return response()->stream(function () use ($result) {
                        while (!$result->eof()) {
                            echo $result->read(1024);
                            flush();
                        }
                    }, 200, [
                        'Content-Type' => Main::contentTypeFile($payload['filename'] ?? 'unknown'),
                        'Content-Disposition' => 'inline; filename="' . ($payload['filename'] ?? 'file') . '"',
                        'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                        'Pragma' => 'no-cache',
                    ]);
                }
            } else {
                Log::channel('sakedap-api')->error('GetFile API Error: Status Code bukan 200', [
                    'status' => $query->status(),
                    'url' => static::$baseUrl
                ]);
            }
        } catch (\Exception $e) {
            Log::channel('sakedap-api')->error('GetFile Exception', [
                'message' => $e->getMessage(),
                'payload' => $payload
            ]);
        }

        $filePath = public_path('assets/no-file.jpg');

        if (file_exists($filePath)) {
            return response()->file($filePath, [
                'Content-Type' => 'image/jpeg',
                'Content-Disposition' => 'inline; filename="no-file.jpg"',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
            ]);
        }

        return $data;
    }

    /**
     * verificationCollection
     *
     * @param  mixed $id
     * @return void
     */
    public static function verificationCollection($id)
    {
        static::initialize();

        $data = false;
        $query = Http::connectTimeout(0)
            ->timeout(0)
            ->withQueryParameters([
                'token' => static::$token,
                'op' => 'verifikasikoleksiditerima',
                'id' => $id
            ])
            ->post(static::$baseUrl);

        if ($query->status() == 200) {
            $response = $query->object();

            if ($response->Status == 'Success') {
                $data = true;
            } else {
                Log::channel('sakedap-api')->error('Gagal verifikasi koleksi', $query->json());
            }
        }

        return $data;
    }

    /**
     * hashPassword
     *
     * @param  mixed $string
     * @return void
     */
    public static function hashPassword($string)
    {
        static::initialize();

        $data = false;
        $query = Http::connectTimeout(0)
            ->timeout(0)
            ->withQueryParameters([
                'token' => static::$token,
                'op' => 'hashpassword',
                'Input' => $string,
            ])
            ->post(static::$baseUrl);

        if ($query->status() == 200) {
            $response = $query->object();

            if ($response->Status == 'Success') {
                $data = $response->Data;
            } else {
                Log::channel('sakedap-api')->error('Gagal hash password', $query->json());
            }
        }

        return $data;
    }
}
