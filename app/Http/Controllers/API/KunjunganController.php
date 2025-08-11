<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Kunjungan;
use App\Models\PemustakaKunjungan;
use Hash;
use Str;

class KunjunganController extends Controller
{

    public function index()
    {
        $data = Kunjungan::select('id', 'name')->get();
        return response()->json($data);
    }

    public function statistics(Request $request)
    {

        $total = 4;
        if ($request->type == 'years') {

            $data = [];

            $kunjungan = Kunjungan::select('id', 'name')->get();

            for ($i = $total; $i >= 0; $i--) {

                $list = [];

                foreach ($kunjungan as $item) {
                    $count = PemustakaKunjungan::where('kunjungan_id', $item->id)
                        ->whereYear('created_at', date('Y', strtotime("- $i year")))
                        ->count();

                    $list[] = [
                        'name'      => $item->name,
                        'total'     => $count
                    ];
                }

                $data[] = [
                    'date'  => date('Y', strtotime("- $i year")),
                    'list'  => $list
                ];
            }

            return response()->json($data);
        } else if ($request->type == 'months') {

            $data =  [];

            $kunjungan = Kunjungan::select('id', 'name')->get();

            for ($i = $total; $i >= 0; $i--) {

                $list = [];

                foreach ($kunjungan as $item) {
                    $count = PemustakaKunjungan::where('kunjungan_id', $item->id)
                        ->whereMonth('created_at', date('m', strtotime("- $i month")))
                        ->whereYear('created_at', date('Y'))
                        ->count();

                    $list[] = [
                        'name'      => $item->name,
                        'total'     => $count
                    ];
                }

                $data[] = [
                    'date'  => date('M Y', strtotime("- $i month")),
                    'list'  => $list
                ];
            }

            return response()->json($data);
        } else if ($request->type == 'days') {


            $data =  [];

            $kunjungan = Kunjungan::select('id', 'name')->get();

            for ($i = $total; $i >= 0; $i--) {

                $list = [];

                foreach ($kunjungan as $item) {
                    $count = PemustakaKunjungan::where('kunjungan_id', $item->id)
                        ->whereDate('created_at', date('Y-m-d', strtotime("- $i days")))
                        ->count();

                    $list[] = [
                        'name'      => $item->name,
                        'total'     => $count
                    ];
                }

                $data[] = [
                    'date'  => date('d M Y', strtotime("- $i days")),
                    'list'  => $list
                ];
            }

            return response()->json($data);
        }
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pemustaka_id'                          => 'required',
            'kunjungan_id'                             => 'required',
        ], [
            'pemustaka_id.required'                    => 'Id Pemustaka wajib di isi!',
            'kunjungan_id.required'                   => 'Id Kunjungan wajib di isi!',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
            return response()->json($response);
        }

        try {
            PemustakaKunjungan::create([
                'pemustaka_id'      => $request->pemustaka_id,
                'kunjungan_id'         => $request->kunjungan_id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => 'Failed Created Kunjungan. Server Error',
                'err'       => $e->getMessage(),
                'status'    => 'Failed'
            ], 500);
        }

        return response()->json([
            'message'   => 'Success Created Kunjungan.',
            'status'    => 'Success'
        ], 201);
    }
}
