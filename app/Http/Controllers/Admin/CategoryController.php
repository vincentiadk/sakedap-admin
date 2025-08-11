<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\DepositHead;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $arrCategoryDH = [
            'KC' => 'Karya Cetak',
            'KRA' => 'Karya Rekam Analog',
            'KRD' => 'Karya Rekam Digital'
        ];
        $getDepositHead = DepositHead::get();
        $deposit_head = [];
        foreach ($getDepositHead as $key => $value) {
            $deposit_head[$value->category][] = $value;
        }
        $data = [
            'title'   => 'Master Kategori',
            'content' => 'admin.master.category',
            'deposit_head' => $deposit_head,
            'category_dh' => $arrCategoryDH
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $whereLike = [
            'id',
            'total_collection',
            'name',
            'type',
            'created_at',
            'updated_at'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $totalData = Category::count();
        if (empty($search)) {
            $queryData = Category::offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Category::count();
        } else {
            $queryData = Category::where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Category::where(function ($query) use ($search) {
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
                    $val->collectionCategory->count(),
                    $val->name,
                    $val->type(),
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
            'type' => 'required'
        ], [
            'name.required' => 'Nama kategori wajib di isi',
            'type.required' => 'Harap memilih tipe'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $create = Category::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name, '-'),
                'type' => $request->type
            ]);

            if ($create) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil ditambahkan!'
                ];

                activity('categories')
                    ->performedOn(new Category())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'nama' => $create->name,
                        'tipe' => $create->type()
                    ])
                    ->log('Menambah data kategori');
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
        $data = Category::find($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'type' => 'required'
        ], [
            'name.required' => 'Nama kategori wajib di isi',
            'type.required' => 'Harap memilih tipe'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $old_data = Category::find($id);
            $new_data = Category::find($id);

            $new_data->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name, '-'),
                'type' => $request->type
            ]);

            if ($new_data) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil diupdate!'
                ];

                activity('categories')
                    ->performedOn(new Category())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'data_lama' => [
                            'nama' => $old_data->name,
                            'tipe' => $old_data->type()
                        ],
                        'data_baru' => [
                            'nama' => $new_data->name,
                            'tipe' => $new_data->type()
                        ]
                    ])
                    ->log('Mengubah data kategori');
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
        $destroy = Category::find($id);
        if ($destroy->first()->collectionCategory->count() < 1) {
            $destroy->delete();
            $data = Category::withTrashed()->find($id);

            if ($destroy) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil dihapus!'
                ];

                activity('categories')
                    ->performedOn(new Category())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'nama' => $data->name
                    ])
                    ->log('Menghapus data kategori');
            } else {
                $response = [
                    'status'  => 500,
                    'message' => 'Gagal dihapus'
                ];
            }
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Kategori masih memiliki relasi di koleksi kategori'
            ];
        }

        return response()->json($response);
    }
}
