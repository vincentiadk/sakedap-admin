<?php

namespace App\Http\Controllers\Admin;

use App\Models\Author;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AuthorController extends Controller
{
    public function index()
    {
        $data = [
            'title'   => 'Master Author',
            'content' => 'admin.master.author'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $whereLike = [
            'id',
            'total_collection',
            'fullname',
            'title',
            'year_of_birth',
            'year_of_death'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $totalData = Author::count();
        if (empty($search)) {
            $queryData = Author::offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Author::count();
        } else {
            $queryData = Author::where(function ($query) use ($search) {
                $query->where('fullname', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('year_of_birth', 'like', "%{$search}%")
                    ->orWhere('year_of_death', 'like', "%{$search}%");
            })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Author::where(function ($query) use ($search) {
                $query->where('fullname', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('year_of_birth', 'like', "%{$search}%")
                    ->orWhere('year_of_death', 'like', "%{$search}%");
            })
                ->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                $response['data'][] = [
                    $nomor,
                    $val->collectionContributor->count(),
                    $val->fullname,
                    $val->title,
                    $val->year_of_birth,
                    $val->year_of_death,
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

    public function show($id)
    {
        $data = Author::find($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'fullname'      => 'required',
            'title'         => 'required',
            'year_of_birth' => 'required',
            'year_of_death' => 'required'
        ], [
            'fullname.required'      => 'Nama author wajib di isi',
            'title.required'         => 'Title wajib di isi',
            'year_of_birth.required' => 'Tahun kelahiran wajib di isi',
            'year_of_death.required' => 'Tahun kematian wajib di isi'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $old_data = Author::find($id);
            $new_data = Author::find($id);

            $new_data->update([
                'fullname'      => $request->fullname,
                'title'         => $request->title,
                'slug'          => Str::slug($request->slug, '-'),
                'year_of_birth' => $request->year_of_birth,
                'year_of_death' => $request->year_of_death
            ]);

            if ($new_data) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil diupdate!'
                ];

                activity('authors')
                    ->performedOn(new Author())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'data_lama' => [
                            'nama_lengkap'     => $old_data->fullname,
                            'gelar'            => $old_data->title,
                            'tanggal_lahir'    => $old_data->year_of_birth,
                            'tanggal_kematian' => $old_data->year_of_death
                        ],
                        'data_baru' => [
                            'nama_lengkap'     => $new_data->fullname,
                            'gelar'            => $new_data->title,
                            'tanggal_lahir'    => $new_data->year_of_birth,
                            'tanggal_kematian' => $new_data->year_of_death
                        ]
                    ])
                    ->log('Mengubah data author');
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
        $data    = Author::find($id);
        $destroy = Author::where('id', $id);

        if ($destroy->first()->collectionContributor->count() < 1) {
            $destroy->delete();

            if ($destroy) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil dihapus!'
                ];

                activity('authors')
                    ->performedOn(new Author())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'nama_lengkap' => $data->fullname
                    ])
                    ->log('Menghapus data author');
            } else {
                $response = [
                    'status'  => 500,
                    'message' => 'Gagal dihapus'
                ];
            }
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Author masih memiliki relasi di koleksi kontributor'
            ];
        }

        return response()->json($response);
    }
}
