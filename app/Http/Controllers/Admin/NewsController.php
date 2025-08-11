<?php

namespace App\Http\Controllers\Admin;

use App\Models\News;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class NewsController extends Controller
{
    public function index()
    {
        $data = [
            'title'   => 'Artikel',
            'content' => 'admin.article'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $whereLike = [
            'id',
            'image',
            'title',
            'status',
            'created_at',
            'updated_at'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $totalData = News::count();
        if (empty($search)) {
            $queryData = News::offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = News::count();
        } else {
            $queryData = News::where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%");
            })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = News::where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%");
            })
                ->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                $image = '<a href="' . asset(Storage::disk($val->location->location)->url($val->image)) . '" data-lightbox="' . $val->title . '" data-title="' . $val->title . '"><img src="' . asset(Storage::disk($val->location->location)->url($val->image)) . '" class="height-50"></a>';

                $response['data'][] = [
                    $nomor,
                    $image,
                    '<span data-toggle="tooltip" title="' . $val->title . '">' . Str::limit($val->title, 20) . '</span>',
                    $val->status(),
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
            'image'   => 'required|max:500|mimes:jpg,jpeg,png',
            'title'   => 'required',
            'content' => 'required'
        ], [
            'image.required'   => 'Gambar wajib di isi',
            'image.max'        => 'Gambar maksimal 500KB',
            'image.mimes'      => 'Gambar harus bertipe jpg, jpeg, png',
            'title.required'   => 'Judul wajib di isi',
            'content.required' => 'Konten wajib di isi'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $image_path = Storage::disk($this->location->location)->put('public/article', $request->file('image'));
            $create = News::create([
                'image'   => $image_path,
                'title'   => $request->title,
                'slug'    => Str::slug($request->title, '-'),
                'content' => $request->content,
                'status'  => $request->status,
                'location_id' => $this->location->id
            ]);

            if ($create) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil ditambahkan!'
                ];

                activity('news')
                    ->performedOn(new News())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'gambar'  => asset(Storage::url($create->image)),
                        'judul'   => $create->title,
                        'content' => $create->content,
                        'status'  => $create->status()
                    ])
                    ->log('Menambah data artikel');
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
        $data = News::find($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'image'   => 'max:500|mimes:jpg,jpeg,png',
            'title'   => 'required',
            'content' => 'required'
        ], [
            'image.max'        => 'Gambar maksimal 500KB',
            'image.mimes'      => 'Gambar harus bertipe jpg, jpeg, png',
            'title.required'   => 'Judul wajib di isi',
            'content.required' => 'Konten wajib di isi'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $news = News::find($id);

            if ($request->has('image')) {
                Storage::disk($news->location->location)->delete($news->image);
                $image = Storage::disk($this->location->location)->put('public/article', $request->file('image'));
                $location_id = $this->location->id;
            } else {
                $image = $news->image;
                $location_id = $news->location_id;
            }

            $old_data = $news;
            $new_data = News::find($id);

            $new_data->update([
                'image'   => $image,
                'title'   => $request->title,
                'slug'    => Str::slug($request->title, '-'),
                'content' => $request->content,
                'status'  => $request->status,
                'location_id' => $location_id
            ]);

            if ($new_data) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil diupdate!'
                ];

                activity('news')
                    ->performedOn(new News())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'data_lama' => [
                            'gambar'  => asset(Storage::disk($old_data->location->location)->url($old_data->image)),
                            'judul'   => $old_data->title,
                            'content' => $old_data->content,
                            'status'  => $old_data->status(),
                            'location_id' => $old_data->location_id
                        ],
                        'data_baru' => [
                            'gambar'  => asset(Storage::disk($new_data->location->location)->url($new_data->image)),
                            'judul'   => $new_data->title,
                            'content' => $new_data->content,
                            'status'  => $new_data->status(),
                            'location_id' => $new_data->location_id
                        ]
                    ])
                    ->log('Mengubah data artikel');
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
        $destroy = News::where('id', $id)->delete();
        $data    = News::withTrashed()->find($id);

        if ($destroy) {
            $response = [
                'status'  => 200,
                'message' => 'Berhasil dihapus!'
            ];

            activity('news')
                ->performedOn(new News())
                ->causedBy(session('id'))
                ->withProperties([
                    'judul' => $data->title
                ])
                ->log('Menghapus data artikel');
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Gagal dihapus'
            ];
        }

        return response()->json($response);
    }
}
