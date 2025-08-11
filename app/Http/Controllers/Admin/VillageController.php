<?php

namespace App\Http\Controllers\Admin;

use App\Models\Village;
use App\Models\District;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class VillageController extends Controller
{
    public function index()
    {
        $data = [
            'title'    => 'Master Kelurahan',
            'district' => District::all(),
            'content'  => 'admin.master.village'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $whereLike = [
            'id',
            'district_id',
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

        $totalData = Village::count();
        if (empty($search)) {
            $queryData = Village::offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Village::count();
        } else {
            $queryData = Village::where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('latitude', 'like', "%{$search}%")
                    ->orWhere('longitude', 'like', "%{$search}%");
            })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Village::where(function ($query) use ($search) {
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
                    $val->district->name,
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
            'district_id' => 'required',
            'name'        => 'required'
        ], [
            'district_id.required' => 'Harap memilih kecamatan',
            'name.required'        => 'Nama kelurahan wajib di isi'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $create = Village::create([
                'district_id' => $request->district_id,
                'name'        => $request->name,
                'latitude'    => $request->latitude,
                'longitude'   => $request->longitude
            ]);

            if ($create) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil ditambahkan!'
                ];

                activity('villages')
                    ->performedOn(new Village())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'kecamatan' => $create->district->name,
                        'nama'      => $create->name,
                        'latitude'  => $create->latitude,
                        'longitude' => $create->longitude
                    ])
                    ->log('Menambah data kelurahan');
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
        $data = Village::find($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'district_id' => 'required',
            'name'        => 'required'
        ], [
            'district_id.required' => 'Harap memilih kecamatan',
            'name.required'        => 'Nama kelurahan wajib di isi'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $old_data = Village::find($id);
            $new_data = Village::find($id);

            $new_data->update([
                'district_id' => $request->district_id,
                'name'        => $request->name,
                'latitude'    => $request->latitude,
                'longitude'   => $request->longitude
            ]);

            if ($new_data) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil diupdate!'
                ];

                activity('villages')
                    ->performedOn(new Village())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'data_lama' => [
                            'kecamatan' => $old_data->district->name,
                            'nama'      => $old_data->name,
                            'latitude'  => $old_data->latitude,
                            'longitude' => $old_data->longitude
                        ],
                        'data_baru' => [
                            'kecamatan' => $new_data->district->name,
                            'nama'      => $new_data->name,
                            'latitude'  => $new_data->latitude,
                            'longitude' => $new_data->longitude
                        ]
                    ])
                    ->log('Mengubah data kelurahan');
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
        $destroy = Village::where('id', $id)->delete();
        $data    = Village::withTrashed()->find($id);

        if ($destroy) {
            $response = [
                'status'  => 200,
                'message' => 'Berhasil dihapus!'
            ];

            activity('villages')
                ->performedOn(new Village())
                ->causedBy(session('id'))
                ->withProperties([
                    'nama' => $data->name
                ])
                ->log('Menghapus data kelurahan');
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Gagal dihapus'
            ];
        }

        return response()->json($response);
    }
}
