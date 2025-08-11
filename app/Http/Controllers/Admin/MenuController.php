<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    public function index()
    {
        $data = [
            'title'   => 'Pengaturan Menu',
            'parent'  => Menu::where('parent_id', 0)->get(),
            'content' => 'admin.setting.menu'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $whereLike = [
            'id',
            'name',
            'icon',
            'url',
            'order',
            'parent_id'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $totalData = Menu::count();
        if (empty($search)) {
            $queryData = Menu::offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Menu::count();
        } else {
            $queryData = Menu::where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('url', 'like', "%{$search}%");
            })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Menu::where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('url', 'like', "%{$search}%");
            })
                ->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                if ($val->parent_id == 0) {
                    $url      = '<b class="font-italic">Is Parent</b>';
                    $icon     = '<i class="' . $val->icon . '"></i>';
                    $parentId = '<b class="font-italic">Is Parent</b>';
                } else {
                    $url      = url($val->url);
                    $icon     = '<b class="font-italic">Is Child</b>';
                    $parent   = Menu::find($val->parent_id);
                    $parentId = $parent ? $parent->name : '';
                }

                $response['data'][] = [
                    $nomor,
                    $val->name,
                    $icon,
                    $url,
                    $parentId,
                    $val->order,
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
            'name' => 'required'
        ], [
            'name.required' => 'Nama menu wajib di isi'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $create = Menu::create([
                'name'      => $request->name,
                'icon'      => $request->icon,
                'url'       => $request->url,
                'parent_id' => $request->parent_id,
                'order'     => $request->order
            ]);

            if ($create) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil ditambahkan!'
                ];

                activity('menus')
                    ->performedOn(new Menu())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'nama'   => $create->name,
                        'ikon'   => $create->icon,
                        'url'    => $create->url,
                        'parent' => $request->parent_id != 0 ? $create->parent()->name : '',
                        'order'  => $create->order
                    ])
                    ->log('Menambah data menu');
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
        $data = Menu::find($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ], [
            'name.required' => 'Nama menu wajib di isi'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $old_data = Menu::find($id);
            $new_data = Menu::find($id);

            $new_data->update([
                'name'      => $request->name,
                'icon'      => $request->icon,
                'url'       => $request->url,
                'parent_id' => $request->parent_id,
                'order'     => $request->order
            ]);

            if ($new_data) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil diupdate!'
                ];

                activity('menus')
                    ->performedOn(new Menu())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'data_lama' => [
                            'nama'   => $old_data->name,
                            'ikon'   => $old_data->icon,
                            'url'    => $old_data->url,
                            'parent' => $old_data->parent() ? $old_data->parent()->name : '',
                            'order'  => $old_data->order
                        ],
                        'data_baru' => [
                            'nama'   => $new_data->name,
                            'ikon'   => $new_data->icon,
                            'url'    => $new_data->url,
                            'parent' => $new_data->parent() ? $new_data->parent()->name : '',
                            'order'  => $new_data->order
                        ]
                    ])
                    ->log('Mengubah data menu');
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
        $destroy = Menu::where('id', $id)->delete();
        $data    = Menu::withTrashed()->find($id);

        if ($destroy) {
            $response = [
                'status'  => 200,
                'message' => 'Berhasil dihapus!'
            ];

            activity('menus')
                ->performedOn(new Menu())
                ->causedBy(session('id'))
                ->withProperties([
                    'nama' => $data->name
                ])
                ->log('Menghapus data menu');
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Gagal dihapus'
            ];
        }

        return response()->json($response);
    }
}
