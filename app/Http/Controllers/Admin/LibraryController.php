<?php

namespace App\Http\Controllers\Admin;

use App\Models\Library;
use App\Models\Province;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LibraryController extends Controller
{
    public function index()
    {
        $data = [
            'title'    => 'Perpustakaan',
            'province' => Province::all(),
            'content'  => 'admin.master.library'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $whereLike = [
            'id',
            'province_id',
            'name',
            'address'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $totalData = Library::count();
        if (empty($search)) {
            $queryData = Library::offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Library::count();
        } else {
            $queryData = Library::where(function ($query) use ($search) {
                $query->whereHas('province', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Library::where(function ($query) use ($search) {
                $query->whereHas('province', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
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
                    $val->province->name,
                    $val->name,
                    $val->address,
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
            'province_id' => 'required',
            'name'        => 'required'
        ], [
            'province_id.required' => 'Harap memilih provinsi',
            'name.required'        => 'Nama wajib di isi'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $create = Library::create([
                'province_id' => $request->province_id,
                'name'        => $request->name,
                'address'     => $request->address
            ]);

            if ($create) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil ditambahkan!'
                ];

                activity('libraries')
                    ->performedOn(new Library())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'provinsi' => $create->province->name,
                        'nama'     => $create->name,
                        'alamat'   => $create->address
                    ])
                    ->log('Menambah data user group');
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
        $data = Library::find($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'province_id' => 'required',
            'name'        => 'required'
        ], [
            'province_id.required' => 'Harap memilih provinsi',
            'name.required'        => 'Nama wajib di isi'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $old_data = Library::find($id);
            $new_data = Library::find($id);

            $new_data->update([
                'province_id' => $request->province_id,
                'name'        => $request->name,
                'address'     => $request->address
            ]);

            if ($new_data) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil diupdate!'
                ];

                activity('libraries')
                    ->performedOn(new Library())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'data_lama' => [
                            'provinsi' => $old_data->province->name,
                            'nama'     => $old_data->name,
                            'alamat'   => $old_data->address
                        ],
                        'data_baru' => [
                            'provinsi' => $new_data->province->name,
                            'nama'     => $new_data->name,
                            'alamat'   => $new_data->address
                        ]
                    ])
                    ->log('Mengubah data user group');
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
        $destroy = Library::where('id', $id)->where('id', '!=', 1)->delete();
        $data    = Library::withTrashed()->find($id);

        if ($destroy) {
            $response = [
                'status'  => 200,
                'message' => 'Berhasil dihapus!'
            ];

            activity('libraries')
                ->performedOn(new Library())
                ->causedBy(session('id'))
                ->withProperties([
                    'nama' => $data->name
                ])
                ->log('Menghapus data user group');
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Gagal dihapus'
            ];
        }

        return response()->json($response);
    }
}
