<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tutorial;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TutorialController extends Controller
{
    public function index()
    {
        $data = [
            'title'   => 'Tutorial',
            'content' => 'admin.master.tutorial'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'sequence',
            'category',
            'title',
            'publish',
        ];

        // dd($request->input);
        $start  = $request->start;
        $length = $request->length;
        $order  = $column[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $total_data = Tutorial::where('id', '>', 0)->count();

        $query_data = Tutorial::where(function ($query) use ($search) {
            if ($search) {
                $query->where('title', 'like', "%$search%")->orWhere('category', 'like', "%$search%");
            }
        })
            ->offset($start)
            ->limit($length)
            ->orderBy($order, $dir)
            ->get();

        $total_filtered = Tutorial::where(function ($query) use ($search) {
            if ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%$search%")
                        ->orWhere('category', 'like', "%$search%");
                });
            }
        })
            ->count();

        $response['data'] = [];
        $status_publish = ['1' => 'Published', '0' => 'Unpublished'];
        if ($query_data <> FALSE) {
            foreach ($query_data as $val) {
                $response['data'][] = [
                    $val->sequence,
                    $val->category,
                    $val->title,
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
            'title' => 'required',
            'sequence' => 'required|numeric',
            'category' => 'required',
            'content' => 'required'
        ], [
            'title.required' => 'Mohon mengisi Pertanyaan.',
            'content.required' => 'Mohon mengisi Jawaban.',
            'category.required' => 'Mohon mengisi Category.',
            'sequence.required' => 'Mohon mengisi Sequence.',
            'sequence.numeric' => 'Mohon isi sequence dengan angka.'
        ]);

        if ($validation->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validation->errors()
            ];
        } else {
            $query = Tutorial::create([
                'sequence'      => $request->sequence,
                'category'      => $request->category,
                'title'         => $request->title,
                'publish'       => $request->publish,
                'content'       => $request->content,
                'slug'          => Str::slug($request->title)
            ]);

            if ($query) {
                activity('tutorial')
                    ->performedOn(new Tutorial())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'sequence'      => $request->sequence,
                        'category'      => $request->category,
                        'title'         => $request->title,
                        'publish'       => $request->publish,
                        'content'       => $request->content,
                        'slug'          => Str::slug($request->title)
                    ])
                    ->log('Menambah data master Tutorial');

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
        $data = Tutorial::find($id);
        $data->content = base64_decode($data->content);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'sequence' => 'required|numeric',
            'category' => 'required',
        ], [
            'title.required' => 'Mohon mengisi Pertanyaan.',
            'content.required' => 'Mohon mengisi Jawaban.',
            'category.required' => 'Mohon mengisi Category.',
            'sequence.required' => 'Mohon mengisi Sequence.',
            'sequence.numeric' => 'Mohon isi sequence dengan angka.'
        ]);

        if ($validation->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validation->errors()
            ];
        } else {
            $old_data = $new_data = Tutorial::find($id);
            $new_data->update([
                'title'  => $request->title,
                'content'    => $request->content,
                'sequence'    => $request->sequence,
                'publish'    => $request->publish,
                'category'    => $request->category,
                'slug'        => Str::slug($request->title)
            ]);

            if ($new_data) {
                activity('tutorial')
                    ->performedOn(new Tutorial())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'data_lama' => [
                            'title'    => $old_data->title,
                            'content'      => $old_data->content,
                            'sequence'    => $old_data->sequence,
                            'publish'    => $old_data->publish,
                            'category'    => $old_data->category,
                            'slug'  => $old_data->slug
                        ],
                        'data_baru' => [
                            'title'    => $request->title,
                            'content'      => $request->content,
                            'sequence'    => $request->sequence,
                            'publish'    => $request->publish,
                            'category'    => $request->category,
                            'slug'  => Str::slug($request->slug)
                        ]
                    ])
                    ->log('Mengubah data Tutorial');

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
        $data = Tutorial::find($id);
        $query     = Tutorial::find($id)->delete();

        if ($query) {
            activity('tutorial')
                ->performedOn(new Tutorial())
                ->causedBy(session('id'))
                ->withProperties([
                    'title'      => $data->title,
                ])
                ->log('Menghapus data Tutorial');

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
