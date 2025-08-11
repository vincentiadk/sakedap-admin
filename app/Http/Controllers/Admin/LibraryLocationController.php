<?php

namespace App\Http\Controllers\Admin;

use App\Models\Library;
use Illuminate\Http\Request;
use App\Models\LibraryLocation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LibraryLocationController extends Controller
{
    public function index()
    {
        $library = Library::where('id', session('library_id'))->orderBy('name', 'asc')->get();
        $data = [
            'title'   => 'LibraryLocation',
            'library_id'   => session('library_id'),
            'library'   => $library,
            'content' => 'admin.master.library_location'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'library_id',
            'name',
            'publish',
        ];

        // dd($request->input);
        $start  = $request->start;
        $length = $request->length;
        $order  = $column[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $total_data = LibraryLocation::where('library_id', session('library_id'))->where('id', '>', 0)->count();

        $query_data = LibraryLocation::where('library_id', session('library_id'))->where(function ($query) use ($search) {
            if ($search) {
                $query->where('name', 'like', "%$search%");
            }
        })->with('library')
            ->offset($start)
            ->limit($length)
            ->orderBy($order, $dir)
            ->get();

        $total_filtered = LibraryLocation::where('library_id', session('library_id'))->where(function ($query) use ($search) {
            if ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%$search%");
                });
            }
        })->with('library')->count();

        $response['data'] = [];
        $status_publish = ['1' => 'Published', '0' => 'Unpublished'];
        if ($query_data <> FALSE) {
            foreach ($query_data as $val) {
                $response['data'][] = [
                    $val->id,
                    $val->library->name,
                    $val->name,
                    $status_publish[$val->publish],
                    '
                        <button type="button" onclick="show(' . $val->id . ')" class="btn btn-warning btn-sm"><i class="la la-pencil"></i> Edit</button>
                        <button type="button" onclick="destroy(' . $val->id . ')" class="btn btn-danger btn-sm"><i class="la la-trash"></i> Hapus</button>
                    '
                ];
            }
        }

        $response['recordsTotal'] = 0;
        if ($total_data <> FALSE) {
            $response['recordsTotal'] = $total_data;
        }

        $response['recordsFiltered'] = 0;
        if ($total_filtered <> FALSE) {
            $response['recordsFiltered'] = $total_filtered;
        }

        return response()->json($response);
    }

    public function create(Request $request)
    {

        // dd($request->all());
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'library_id' => 'required',
            'publish' => 'required|numeric',
        ], [
            'name.required' => 'Mohon mengisi Nama Lokasi.',
            'library_id.required' => 'Mohon mengisi perpustakaan.',
            'publish.required' => 'Mohon mengisi publish.',
            'publish.numeric' => 'Mohon isi publish dengan angka.'
        ]);

        if ($validation->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validation->errors()
            ];
        } else {
            $query = LibraryLocation::create([
                'name'         => $request->name,
                'publish'       => $request->publish,
                'library_id'       => $request->library_id,
            ]);

            if ($query) {
                activity('library_location')
                    ->performedOn(new LibraryLocation())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'name'         => $request->name,
                        'publish'       => $request->publish,
                        'library_id'       => $request->library_id,
                    ])
                    ->log('Menambah data master Library Location');

                $response = [
                    'status'  => 200,
                    'message' => 'Data telah diproses.'
                ];
            } else {
                $response = [
                    'status'  => 500,
                    'message' => 'Data gagal diproses.'
                ];
            }
        }

        return response()->json($response);
    }

    public function show(Request $request, $id)
    {
        $data = LibraryLocation::find($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'library_id' => 'required',
            'publish' => 'required',
        ], [
            'name.required' => 'Mohon mengisi Nama Lokasi.',
            'library_id.required' => 'Mohon mengisi perpustakaan.',
            'publish.required' => 'Mohon mengisi status publish.',
        ]);

        if ($validation->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validation->errors()
            ];
        } else {
            $old_data = $new_data = LibraryLocation::find($id);
            $new_data->update([
                'name'         => $request->name,
                'publish'       => $request->publish,
                'library_id'       => $request->library_id,
            ]);

            // dd($new_data);

            if ($new_data) {
                activity('library_location')
                    ->performedOn(new LibraryLocation())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'data_lama' => [
                            'name'         => $old_data->name,
                            'publish'       => $old_data->publish,
                            'library_id'       => $old_data->library_id,
                        ],
                        'data_baru' => [
                            'name'         => $request->name,
                            'publish'       => $request->publish,
                            'library_id'       => $request->library_id,
                        ]
                    ])
                    ->log('Mengubah data LibraryLocation');

                $response = [
                    'status'  => 200,
                    'message' => 'Data telah diproses.'
                ];
            } else {
                $response = [
                    'status'  => 500,
                    'message' => 'Data gagal diproses.'
                ];
            }
        }

        return response()->json($response);
    }

    public function destroy(Request $request, $id)
    {
        $data = LibraryLocation::find($id);
        $query     = LibraryLocation::find($id)->delete();

        if ($query) {
            activity('library_location')
                ->performedOn(new LibraryLocation())
                ->causedBy(session('id'))
                ->withProperties([
                    'name'      => $data->title,
                ])
                ->log('Menghapus data Library Location');

            $response = [
                'status'  => 200,
                'message' => 'Data telah dihapus.'
            ];
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Data gagal dihapus.'
            ];
        }

        return response()->json($response);
    }
}
