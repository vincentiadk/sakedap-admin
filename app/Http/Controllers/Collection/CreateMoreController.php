<?php

namespace App\Http\Controllers\Collection;

use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CreateMoreController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'content' => 'collection.create-more',
                'plugins' => [
                    'fileinput',
                    'select2',
                    'datatable',
                    'lookup',
                ]
            ]
        ]);
    }

    public function submitted(Request $request)
    {
        $response = [];

        if ($request->ajax()) {
            $validation = Validator::make($request->all(), [
                'type' => 'required',
                'id' => 'required',
                'file' => 'required|file|mimes:zip|max:500000',
            ], [
                'type.required' => 'Jenis tidak boleh kosong',
                'id.required' => 'Pelaksana Serah (Non Serial) / Katalog (Serial) tidak boleh kosong',
                'file.required' => 'File tidak boleh kosong',
                'file.file' => 'File tidak valid',
                'file.mimes' => 'File harus zip',
                'file.max' => 'File maksimal 500 MB',
            ]);

            if ($validation->fails()) {
                $response = [
                    'code' => 400,
                    'error' => $validation->errors()->all(),
                ];
            } else {
                try {
                    QueryAPI::uploadFile([
                        'type' => $request->type,
                        'id' => $request->id,
                        'method' => 7,
                        'iszip' => 1,
                        'file' => $request->file('file')
                    ]);

                    $response = [
                        'code' => 200,
                        'message' => 'Data telah masuk proses'
                    ];
                } catch (\Exception $e) {
                    $response = [
                        'code' => $e->getCode(),
                        'message' => $e->getMessage()
                    ];
                }
            }
        }

        return response()->json($response);
    }
}
