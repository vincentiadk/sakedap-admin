<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Models\Province;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller
{
    public function index()
    {
        $data = [
            'title'    => 'Master Kota',
            'province' => Province::all(),
            'content'  => 'admin.master.city'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $whereLike = [
            'id',
            'total_collection',
            'province_id',
            'name',
            'latitude',
            'longitude',
            'created_at',
            'updated_at'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $totalData = City::count();
        if (empty($search)) {
            $queryData = City::offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = City::count();
        } else {
            $queryData = City::where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('latitude', 'like', "%{$search}%")
                    ->orWhere('longitude', 'like', "%{$search}%");
            })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = City::where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('latitude', 'like', "%{$search}%")
                    ->orWhere('longitude', 'like', "%{$search}%");
            })
                ->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                $response['data'][] = [
                    $nomor,
                    $val->collection->count(),
                    $val->province->name,
                    $val->name,
                    $val->latitude,
                    $val->longitude,
                    date('d-m-Y', strtotime($val->created_at)),
                    date('d-m-Y', strtotime($val->updated_at)),
                    '
                        <button type="button" onclick="show(' . $val->id . ')" class="btn btn-warning btn-sm"><i class="la la-pencil"></i> Edit</button>
                        <button type="button" onclick="destroy(' . $val->id . ')" class="btn btn-danger btn-sm"><i class="la la-trash"></i> Hapus</button>
                    '
                ];
                $nomor++;
            }
        }

        $response['recordsTotal'] = 0;
        if ($totalData <> FALSE) {
            $response['recordsTotal'] = $totalData;
        }

        $response['recordsFiltered'] = 0;
        if ($totalFiltered <> FALSE) {
            $response['recordsFiltered'] = $totalFiltered;
        }

        return response()->json($response);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'province_id' => 'required',
            'name'        => 'required'
        ], [
            'province_id.required' => 'Harap memilih provinsi',
            'name.required'        => 'Nama kota wajib di isi'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $create = City::create([
                'province_id' => $request->province_id,
                'name'        => $request->name,
                'latitude'    => $request->latitude,
                'longitude'   => $request->longitude
            ]);

            if ($create) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil ditambahkan!'
                ];

                activity('cities')
                    ->performedOn(new City())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'provinsi'  => $create->province->name,
                        'name'      => $create->name,
                        'latitude'  => $create->latitude,
                        'longitude' => $create->longitude
                    ])
                    ->log('Menambah data kota');
            } else {
                $response = [
                    'status'  => 500,
                    'message' => 'Gagal ditambahkan'
                ];
            }
        }

        return response()->json($response);
    }

    public function show($id)
    {
        $data = City::find($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'province_id' => 'required',
            'name'        => 'required'
        ], [
            'province_id.required' => 'Harap memilih provinsi',
            'name.required'        => 'Nama kota wajib di isi'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $old_data = City::find($id);
            $new_data = City::find($id);

            $new_data->update([
                'province_id' => $request->province_id,
                'name'        => $request->name,
                'latitude'    => $request->latitude,
                'longitude'   => $request->longitude
            ]);

            if ($new_data) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil diupdate!'
                ];

                activity('cities')
                    ->performedOn(new City())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'data_lama' => [
                            'provinsi'  => $old_data->province->name,
                            'name'      => $old_data->name,
                            'latitude'  => $old_data->latitude,
                            'longitude' => $old_data->longitude
                        ],
                        'data_baru' => [
                            'provinsi'  => $new_data->province->name,
                            'name'      => $new_data->name,
                            'latitude'  => $new_data->latitude,
                            'longitude' => $new_data->longitude
                        ]
                    ])
                    ->log('Mengubah data kota');
            } else {
                $response = [
                    'status'  => 500,
                    'message' => 'Gagal diupdate'
                ];
            }
        }

        return response()->json($response);
    }

    public function destroy($id)
    {
        $destroy = City::where('id', $id);
        if ($destroy->first()->district->count() < 1) {
            $destroy->delete();
            $data = City::withTrashed()->find($id);

            if ($destroy) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil dihapus!'
                ];

                activity('cities')
                    ->performedOn(new City())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'nama' => $data->name
                    ])
                    ->log('Menghapus data kota');
            } else {
                $response = [
                    'status'  => 500,
                    'message' => 'Gagal dihapus'
                ];
            }
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Kota masih memiliki relasi di kecamatan'
            ];
        }

        return response()->json($response);
    }
}
