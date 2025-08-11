<?php

namespace App\Http\Controllers\Admin;

use App\Models\Subject;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    public function index()
    {
        $data = [
            'title'   => 'Master Subjek',
            'content' => 'admin.master.subject'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $whereLike = [
            'id',
            'total_collection',
            'name',
            'created_at',
            'updated_at'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $totalData = Subject::count();
        if (empty($search)) {
            $queryData = Subject::offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Subject::count();
        } else {
            $queryData = Subject::where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Subject::where(function ($query) use ($search) {
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
                    $val->collectionSubject->count(),
                    $val->name,
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
            'name' => 'required',
        ], [
            'name.required' => 'Nama subjek wajib di isi'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $create = Subject::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name, '-')
            ]);

            if ($create) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil ditambahkan!'
                ];

                activity('subjects')
                    ->performedOn(new Subject())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'name' => $create->name
                    ])
                    ->log('Menambah data subjek');
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
        $data = Subject::find($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ], [
            'name.required' => 'Nama subjek wajib di isi'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $old_data = Subject::find($id);
            $new_data = Subject::find($id);

            $new_data->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name, '-')
            ]);

            if ($new_data) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil diupdate!'
                ];

                activity('subjects')
                    ->performedOn(new Subject())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'data_lama' => [
                            'nama' => $old_data->name
                        ],
                        'data_baru' => [
                            'nama' => $new_data->name
                        ]
                    ])
                    ->log('Mengubah data subjek');
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
        $destroy = Subject::where('id', $id);
        if ($destroy->first()->collectionSubject->count() < 1) {
            $destroy->delete();
            $data = Subject::withTrashed()->find($id);

            if ($destroy) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil dihapus!'
                ];

                activity('subjects')
                    ->performedOn(new Subject())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'nama' => $data->name
                    ])
                    ->log('Menghapus data subjek');
            } else {
                $response = [
                    'status'  => 500,
                    'message' => 'Gagal dihapus'
                ];
            }
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Subjek masih memiliki relasi di koleksi subjek'
            ];
        }

        return response()->json($response);
    }
}
