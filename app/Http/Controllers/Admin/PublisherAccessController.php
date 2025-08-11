<?php

namespace App\Http\Controllers\Admin;

use App\Models\Publisher;
use Illuminate\Http\Request;
use App\Models\PublisherAccess;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PublisherAccessController extends Controller
{
    public function index($group_id)
    {
        $data = [
            'title'         => 'Master Publisher Access',
            'content'       => 'admin.master.publisheraccess',
            'publisher'     => Publisher::all(),
            'group_id'      => $group_id,
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable($group_id, Request $request)
    {
        Log::info($group_id);
        $whereLike = [
            'id',
            'name',
            'created_at',
            'updated_at'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $totalData = PublisherAccess::where('publisher_group_id', $group_id)->count();
        if (empty($search)) {
            $queryData = PublisherAccess::offset($start)
                ->where('publisher_group_id', $group_id)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = PublisherAccess::where('publisher_group_id', $group_id)->count();
        } else {
            $queryData = PublisherAccess::where(function ($query) use ($search) {
                $query->where('code_system', 'like', "%{$search}%");
                $query->where('system_type', 'like', "%{$search}%");
            })
                ->where('publisher_group_id', $group_id)
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = PublisherAccess::where(function ($query) use ($search) {
                $query->where('code_system', 'like', "%{$search}%");
                $query->where('system_type', 'like', "%{$search}%");
            })
                ->where('publisher_group_id', $group_id)
                ->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                $response['data'][] = [
                    $nomor,
                    $val->publisher->name,
                    $val->system_type,
                    $val->code_system,
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

    public function create(Request $request, $group_id)
    {
        $validator = Validator::make($request->all(), [
            'publisher_id'  => 'required',
            'code_system'   => 'required',
            'system_type'   => 'required',
            'publisher_id'  => 'required'
        ], [
            'publisher_id.required'     => 'Nama wajib di isi',
            'code_system.required'      => 'Kode Sistem wajib di isi',
            'publisher_id.required'     => 'Publisher wajib di isi',
            'system_type.required'      => 'Tipe Sistem wajib di isi'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $create = PublisherAccess::create([
                'publisher_group_id' => $group_id,
                'publisher_id'       => $request->publisher_id,
                'code_system'        => $request->code_system,
                'system_type'        => $request->system_type,
            ]);

            if ($create) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil ditambahkan!'
                ];

                activity('publisheraccess')
                    ->performedOn(new PublisherAccess())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'publisher_group_id' => $group_id,
                        'publisher_id'       => $create->publisher_id,
                        'code_system'        => $create->code_system,
                        'system_type'        => $create->system_type,
                    ])
                    ->log('Menambah data publisher access');
            } else {
                $response = [
                    'status'  => 500,
                    'message' => 'Gagal ditambahkan'
                ];
            }
        }

        return response()->json($response);
    }

    public function show($group_id, $id)
    {
        $data = PublisherAccess::where('id', $id)->with('publisher')->first();
        return response()->json($data);
    }

    public function update(Request $request, $group_id, $id)
    {
        $validator = Validator::make($request->all(), [
            'code_system'   => 'required',
            'system_type'   => 'required',
            'publisher_id'  => 'required'
        ], [
            'code_system.required'      => 'Kode Sistem wajib di isi',
            'publisher_id.required'     => 'Publisher wajib di isi',
            'system_type.required'      => 'Tipe Sistem wajib di isi'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $old_data = PublisherAccess::find($id);
            $new_data = PublisherAccess::find($id);

            $new_data->update([
                'code_system'   => $request->code_system,
                'system_type'   => $request->system_type,
            ]);

            if ($new_data) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil diupdate!'
                ];

                activity('publisheraccess')
                    ->performedOn(new PublisherAccess())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'data_lama' => [
                            'code_system'   => $old_data->code_system,
                            'system_type'   => $old_data->system_type,
                        ],
                        'data_baru' => [
                            'code_system'   => $new_data->code_system,
                            'system_type'   => $new_data->system_type,
                        ]
                    ])
                    ->log('Mengubah data publisher access');
            } else {
                $response = [
                    'status'  => 500,
                    'message' => 'Gagal diupdate'
                ];
            }
        }

        return response()->json($response);
    }

    public function destroy($group_id, $id)
    {
        $destroy = PublisherAccess::where('id', $id)->delete();
        $data    = PublisherAccess::withTrashed()->find($id);

        if ($destroy) {
            $response = [
                'status'  => 200,
                'message' => 'Berhasil dihapus!'
            ];

            activity('publisheraccess')
                ->performedOn(new PublisherAccess())
                ->causedBy(session('id'))
                ->withProperties([
                    'code_system'   => $data->code_system,
                    'system_type'   => $data->system_type,
                ])
                ->log('Menghapus data publisher access');
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Gagal dihapus'
            ];
        }

        return response()->json($response);
    }

    public function getPublisher($id)
    {
        return Publisher::findOrFail($id);
    }
}
