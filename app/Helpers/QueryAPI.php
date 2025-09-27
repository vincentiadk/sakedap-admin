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
        static::$baseUrl = config('inlis.base_url');
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

        $query = Http::connectTimeout(60)
            ->timeout(120)
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
        $query = Http::connectTimeout(60)
            ->timeout(120)
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
        $query = Http::connectTimeout(60)
            ->timeout(120)
            ->withQueryParameters([
                'token' => static::$token,
                'op' => 'add',
                'table' => $table,
                'issavehistory' => 1,
                'ListAddItem' => json_encode($bodyJson)
            ])
            ->post(static::$baseUrl);

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
        $query = Http::connectTimeout(60)
            ->timeout(120)
            ->withQueryParameters([
                'token' => static::$token,
                'op' => 'update',
                'table' => $table,
                'id' => $id,
                'issavehistory' => 1,
                'ListUpdateItem' => json_encode($bodyJson)
            ])
            ->post(static::$baseUrl);

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
        $query = Http::connectTimeout(60)
            ->timeout(120)
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
            'uploadby' => session('name'),
            'terminal' => request()->ip(),
        ]);

        $query = Http::connectTimeout(60)
            ->timeout(120)
            ->attach('file', file_get_contents($payload['file']), $payload['file']->getClientOriginalName())
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
            'actionby' => session('name'),
            'terminal' => request()->ip(),
        ]);

        $query = Http::connectTimeout(60)
            ->timeout(120)
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

        $query = Http::connectTimeout(60)
            ->timeout(120)
            ->withQueryParameters($param)
            ->withOptions(['stream' => true])
            ->post(static::$baseUrl);

        if ($query->status() == 200) {
            $result = $query->getBody();

            return response()->stream(function () use ($result) {
                while (!$result->eof()) {
                    echo $result->read(1024);

                    flush();
                }
            }, 200, [
                'Content-Type' => Main::contentTypeFile($payload['filename']),
                'Content-Disposition' => 'inline; filename="' . $payload['filename'] . '"',
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
        $query = Http::connectTimeout(60)
            ->timeout(120)
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
}
