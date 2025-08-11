<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Models\District;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DistrictController extends Controller
{
    public function index()
    {
        $data = [
            'title'   => 'Master Kecamatan',
            'city'    => City::all(),
            'content' => 'admin.master.district'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $whereLike = [
            'id',
            'city_id',
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

        $totalData = District::count();
        if (empty($search)) {
            $queryData = District::offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = District::count();
        } else {
            $queryData = District::where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('latitude', 'like', "%{$search}%")
                    ->orWhere('longitude', 'like', "%{$search}%");
            })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = District::where(function ($query) use ($search) {
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
                    $val->city->name,
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
            'city_id' => 'required',
            'name'    => 'required'
        ], [
            'city_id.required' => 'Harap memilih kota',
            'name.required'    => 'Nama kecamatan wajib di isi'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $create = District::create([
                'city_id'   => $request->city_id,
                'name'      => $request->name,
                'latitude'  => $request->latitude,
                'longitude' => $request->longitude
            ]);

            if ($create) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil ditambahkan!'
                ];

                activity('districts')
                    ->performedOn(new District())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'kota'      => $create->city->name,
                        'nama'      => $create->name,
                        'latitude'  => $create->latitude,
                        'longitude' => $create->longitude
                    ])
                    ->log('Menambah data kecamatan');
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
        $data = District::find($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'city_id' => 'required',
            'name'    => 'required'
        ], [
            'city_id.required' => 'Harap memilih kota',
            'name.required'    => 'Nama kecamatan wajib di isi'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $old_data = District::find($id);
            $new_data = District::find($id);

            $new_data->update([
                'city_id'   => $request->city_id,
                'name'      => $request->name,
                'latitude'  => $request->latitude,
                'longitude' => $request->longitude
            ]);

            if ($new_data) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil diupdate!'
                ];

                activity('districts')
                    ->performedOn(new District())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'data_lama' => [
                            'kota'      => $old_data->city->name,
                            'nama'      => $old_data->name,
                            'latitude'  => $old_data->latitude,
                            'longitude' => $old_data->longitude
                        ],
                        'data_baru' => [
                            'kota'      => $new_data->city->name,
                            'nama'      => $new_data->name,
                            'latitude'  => $new_data->latitude,
                            'longitude' => $new_data->longitude
                        ]
                    ])
                    ->log('Mengubah data kecamatan');
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
        $destroy = District::where('id', $id);
        if ($destroy->first()->village->count() < 1) {
            $destroy->delete();
            $data = District::withTrashed()->find($id);

            if ($destroy) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil dihapus!'
                ];

                activity('districts')
                    ->performedOn(new District())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'nama' => $data->name
                    ])
                    ->log('Menghapus data kecamatan');
            } else {
                $response = [
                    'status'  => 500,
                    'message' => 'Gagal dihapus'
                ];
            }
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Kecamatan masih memiliki relasi di kelurahan'
            ];
        }

        return response()->json($response);
    }
}
