<?php

namespace App\Http\Controllers\Admin;

use App\Models\Publisher;
use Illuminate\Http\Request;
use App\Models\PublisherGroup;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PublisherGroupController extends Controller
{
    public function index()
    {
        $data = [
            'title'   => 'Master Publisher Group',
            'content' => 'admin.master.publishergroup'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
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

        $totalData = PublisherGroup::count();
        if (empty($search)) {
            $queryData = PublisherGroup::offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = PublisherGroup::count();
        } else {
            $queryData = PublisherGroup::where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = PublisherGroup::where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
                ->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                $response['data'][] = [
                    $nomor,
                    $val->name,
                    date('d-m-Y', strtotime($val->created_at)),
                    date('d-m-Y', strtotime($val->updated_at)),
                    '
                        <a href="' . url('admin/publisher_group/' . $val->id . '/access') . '" class="btn btn-info btn-sm"><i class="la la-eye"></i> Detail</a>
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
            'name' => 'required',
        ], [
            'name.required' => 'Nama wajib di isi'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $create = PublisherGroup::create([
                'name' => $request->name
            ]);

            if ($create) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil ditambahkan!'
                ];

                activity('publishergroup')
                    ->performedOn(new PublisherGroup())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'nama' => $create->name
                    ])
                    ->log('Menambah data publisher group');
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
        $data = PublisherGroup::find($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ], [
            'name.required' => 'Nama wajib di isi'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $old_data = PublisherGroup::find($id);
            $new_data = PublisherGroup::find($id);

            $new_data->update([
                'name' => $request->name
            ]);

            if ($new_data) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil diupdate!'
                ];

                activity('publishergroup')
                    ->performedOn(new PublisherGroup())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'data_lama' => [
                            'nama' => $old_data->name
                        ],
                        'data_baru' => [
                            'nama' => $new_data->name
                        ]
                    ])
                    ->log('Mengubah data publisher group');
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
        $destroy = PublisherGroup::where('id', $id)->delete();
        $data    = PublisherGroup::withTrashed()->find($id);

        if ($destroy) {
            $response = [
                'status'  => 200,
                'message' => 'Berhasil dihapus!'
            ];

            activity('publishergroup')
                ->performedOn(new PublisherGroup())
                ->causedBy(session('id'))
                ->withProperties([
                    'nama' => $data->name
                ])
                ->log('Menghapus data publisher group');
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
