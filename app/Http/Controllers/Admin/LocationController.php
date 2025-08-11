<?php

namespace App\Http\Controllers\Admin;

use App\Models\Library;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    public function index()
    {
        $data = [
            'title'    => 'Storage Location',
            //'province' => Province::all(),
            'content'  => 'admin.master.location'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $whereLike = [
            'id',
            'location',
            'server',
            'driver'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $totalData = Location::count();
        if (empty($search)) {
            $queryData = Location::offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Location::count();
        } else {
            $queryData = Location::where(function ($query) use ($search) {
                $query->where('host', 'like', "%{$search}%")
                    ->orWhere('driver', 'like', "%{$search}%")
                    ->orWhere('root', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Location::where(function ($query) use ($search) {
                $query->where('host', 'like', "%{$search}%")
                    ->orWhere('driver', 'like', "%{$search}%")
                    ->orWhere('root', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            })
                ->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                $btn_delete         = $val->id == 1 ? 'style="display:none;"' : '';
                $response['data'][] = [
                    $nomor,
                    $val->location,
                    $val->driver,
                    $val->root,
                    '
                        <button type="button" onclick="show(' . $val->id . ')" class="btn btn-warning btn-sm"><i class="la la-pencil"></i> Edit</button>
                        <button type="button" onclick="destroy(' . $val->id . ')" class="btn btn-danger btn-sm" ' . $btn_delete . '"><i class="la la-trash"></i> Hapus</button>
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
            'location' => 'required',
            'driver'        => 'required'
        ], [
            'location.required' => 'Harap mengisi location',
            'driver.required'        => 'Nama mengisi driver'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            if ($request->active == 1) {
                Location::update([
                    'active' => 0
                ]);
            }
            $create = Location::create([
                'location' => $request->location,
                'driver'   => $request->driver,
                'host'     => $request->host,
                'username' => $request->username,
                'password' => $request->password,
                'root'     => $request->root,
                'active'   => $request->active
            ]);


            if ($create) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil ditambahkan!'
                ];

                activity('locations')
                    ->performedOn(new Library())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'location' => $create->location,
                        'driver'   => $create->driver,
                        'host'     => $create->host,
                        'username' => $create->username,
                        'root'     => $create->root,
                        'active'   => $create->active
                    ])
                    ->log('Menambah data storage location');
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
        $data = Location::find($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'location' => 'required',
            'driver'        => 'required'
        ], [
            'location.required' => 'Harap mengisi location',
            'driver.required'        => 'Nama mengisi driver'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $old_data = Location::find($id);
            $new_data = Location::find($id);
            if ($request->active == 1) {
                Location::where('id', $id)->update([
                    'active' => 0
                ]);
            }
            $new_data->update([
                'location' => $request->location,
                'driver'   => $request->driver,
                'host'     => $request->host,
                'username' => $request->username,
                'password' => $request->password,
                'root'     => $request->root,
                'active'   => $request->active
            ]);

            if ($new_data) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil diupdate!'
                ];

                activity('locations')
                    ->performedOn(new Library())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'data_lama' => [
                            'location'  => $old_data->location,
                            'driver'    => $old_data->driver,
                            'host'      => $old_data->host,
                            'username'  => $old_data->username,
                            'root'      => $old_data->username,
                            'active'    => $old_data->active
                        ],
                        'data_baru' => [
                            'location'  => $new_data->location,
                            'driver'    => $new_data->driver,
                            'host'      => $new_data->host,
                            'username'  => $new_data->username,
                            'root'      => $new_data->username,
                            'active'    => $new_data->active
                        ]
                    ])
                    ->log('Mengubah data storage location');
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
        $data    = Location::find($id);
        $destroy = Location::where('id', $id)->where('id', '!=', 1)->delete();
        if ($destroy) {
            $response = [
                'status'  => 200,
                'message' => 'Berhasil dihapus!'
            ];

            activity('locations')
                ->performedOn(new Location())
                ->causedBy(session('id'))
                ->withProperties([
                    'nama' => $data->location
                ])
                ->log('Menghapus data storage location');
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Gagal dihapus'
            ];
        }

        return response()->json($response);
    }
}
