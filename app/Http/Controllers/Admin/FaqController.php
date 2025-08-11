<?php

namespace App\Http\Controllers\Admin;

use App\Models\Faq;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class FaqController extends Controller
{
    public function index()
    {
        $data = [
            'title'   => 'FAQ',
            'content' => 'admin.master.faq'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'id',
            'sequence',
            'category',
            'question',
            'answer',
            'publish'
        ];

        // dd($request->input);
        $start  = $request->start;
        $length = $request->length;
        $order  = $column[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $total_data = Faq::where('id', '>', 0)->count();

        $query_data = Faq::where(function ($query) use ($search) {
            if ($search) {
                $query->where('question', 'like', "%$search%")->orWhere('category', 'like', "%$search%");
            }
        })
            ->offset($start)
            ->limit($length)
            ->orderBy($order, $dir)
            ->get();

        $total_filtered = Faq::where(function ($query) use ($search) {
            if ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('question', 'like', "%$search%")
                        ->orWhere('category', 'like', "%$search%");
                });
            }
        })
            ->count();

        $response['data'] = [];
        if ($query_data <> FALSE) {
            $nomor = $start + 1;
            foreach ($query_data as $val) {
                $response['data'][] = [
                    $nomor,
                    $val->sequence,
                    $val->category,
                    $val->question,
                    ($val->publish == '1') ? '<span class="badge bg-primary">Published</span>' : '<span class="badge bg-secondary">Unpublished</span>',
                    '
                        <button type="button" onclick="show(' . $val->id . ')" class="btn btn-warning btn-sm"><i class="la la-pencil"></i> Edit</button>
                        <button type="button" onclick="destroy(' . $val->id . ')" class="btn btn-danger btn-sm"><i class="la la-trash"></i> Hapus</button>
                    '
                ];
                $nomor++;
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
        $validation = Validator::make($request->all(), [
            'question' => 'required',
            'sequence' => 'required|numeric',
            'category' => 'required',
            'answer' => 'required',
            'publish' => 'required'
        ], [
            'question.required' => 'Mohon mengisi Pertanyaan.',
            'answer.required' => 'Mohon mengisi Jawaban.',
            'category.required' => 'Mohon mengisi Category.',
            'sequence.required' => 'Mohon mengisi Sequence.',
            'publish.required' => 'Mohon isi Status Publish.'
        ]);

        if ($validation->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validation->errors()
            ];
        } else {
            $query = Faq::create([
                'sequence'      => $request->sequence,
                'category'      => $request->category,
                'question'      => $request->question,
                'answer'        => $request->answer,
                'publish'       => $request->publish
            ]);

            if ($query) {
                activity('faq')
                    ->performedOn(new Faq())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'sequence'      => $request->sequence,
                        'category'      => $request->category,
                        'question'      => $request->question,
                        'answer'        => $request->answer,
                        'publish'       => $request->publish
                    ])
                    ->log('Menambah data master FAQ');

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
        $data = Faq::find($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $validation = Validator::make($request->all(), [
            'question' => 'required',
            'answer' => 'required',
            'sequence' => 'required|numeric',
            'category' => 'required',
            'publish' => 'required'
        ], [
            'question.required' => 'Mohon mengisi Pertanyaan.',
            'answer.required' => 'Mohon mengisi Jawaban.',
            'category.required' => 'Mohon mengisi Category.',
            'sequence.required' => 'Mohon mengisi Sequence.',
            'sequence.numeric' => 'Mohon isi sequence dengan angka.',
            'publish.required' => 'Mohon isi status publish.'
        ]);

        // dd($request->all(), $validation);

        if ($validation->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validation->errors()
            ];
        } else {
            $old_data = $new_data = Faq::find($id);
            $new_data->update([
                'question'  => $request->question,
                'answer'    => $request->answer,
                'sequence'    => $request->sequence,
                'category'    => $request->category,
                'publish'    => $request->publish
            ]);

            if ($new_data) {
                activity('faq')
                    ->performedOn(new Faq())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'data_lama' => [
                            'question'    => $old_data->question,
                            'answer'      => $old_data->answer,
                            'sequence'    => $old_data->sequence,
                            'category'    => $old_data->category,
                            'publish'    => $old_data->publish
                        ],
                        'data_baru' => [
                            'question'    => $request->question,
                            'answer'      => $request->answer,
                            'sequence'    => $request->sequence,
                            'category'    => $request->category,
                            'publish'    => $request->publish
                        ]
                    ])
                    ->log('Mengubah data FAQ');

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
        $data = Faq::find($id);
        $query     = Faq::find($id)->delete();

        if ($query) {
            activity('faq')
                ->performedOn(new Faq())
                ->causedBy(session('id'))
                ->withProperties([
                    'question'      => $data->question,
                ])
                ->log('Menghapus data FAQ');

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
