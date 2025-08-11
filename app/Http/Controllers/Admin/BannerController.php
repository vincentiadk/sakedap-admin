<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use App\Models\Location;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{

    protected $location;

    public function __construct()
    {
        $this->location = Location::where('active', 1)->first();
    }

    public function index()
    {
        $data = [
            'title'   => 'Banner',
            'content' => 'admin.master.banner'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $whereLike = [
            'id',
            'image',
            'title',
            'description',
            'status'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $totalData = Banner::count();
        if (empty($search)) {
            $queryData = Banner::offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Banner::count();
        } else {
            $queryData = Banner::where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Banner::where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
                ->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                $image = '<a href="' . asset(Storage::disk($val->location->location)->url($val->image)) . '" data-lightbox="' . $val->title . '" data-title="' . $val->title . '"><img src="' . url('banner/' . $val->id) . '" class="height-50"></a>';
                $response['data'][] = [
                    $nomor,
                    $image,
                    '<span data-toggle="tooltip" title="' . $val->title . '">' . Str::limit($val->title, 20) . '</span>',
                    '<span data-toggle="tooltip" title="' . $val->description . '">' . Str::limit($val->description, 20) . '</span>',
                    $val->status(),
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
            'image'  => 'required|max:1024|mimes:jpg,jpeg,png|dimensions:max_width=1920,max-height=400',
            'status' => 'required'
        ], [
            'image.required'   => 'Gambar wajib di isi',
            'image.max'        => 'Gambar maksimal 1MB',
            'image.mimes'      => 'Gambar harus bertipe jpg, jpeg, png',
            'image.dimensions' => 'Ukuran gambar maksimal 1920x400',
            'status.required'  => 'Harap memilih status'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $path_image = Storage::disk($this->location->location)->put('public/banner', $request->file('image'));

            $create = Banner::create([
                'image'       => $path_image,
                'title'       => $request->title,
                'description' => $request->description,
                'status'      => $request->status,
                'location_id'  => $this->location->id
            ]);

            if ($create) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil ditambahkan!'
                ];

                activity('banners')
                    ->performedOn(new Banner())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'image'     => asset(Storage::url($create->image)),
                        'judul'     => $create->title,
                        'deskripsi' => $create->description,
                        'status'    => $create->status == 1 ? 'Aktif' : 'Tidak Aktif',
                        'location_id'  => $this->location->id
                    ])
                    ->log('Menambah data banner');
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
        $data = Banner::find($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'image'  => 'max:1024|mimes:jpg,jpeg,png|dimensions:max_width=1920,max-height=400',
            'status' => 'required'
        ], [
            'image.max'        => 'Gambar maksimal 1MB',
            'image.mimes'      => 'Gambar harus bertipe jpg, jpeg, png',
            'image.dimensions' => 'Ukuran gambar maksimal 1920x400',
            'status.required'  => 'Harap memilih status'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $banner = Banner::find($id);

            if ($request->has('image')) {
                Storage::disk($this->location->location)->delete($banner->image);
                $image = Storage::disk($this->location->location)->put('public/banner/', $request->file('image'));
            } else {
                $image = $banner->image;
            }

            $old_data = $banner;
            $new_data = Banner::find($id);

            $new_data->update([
                'image'       => $image,
                'title'       => $request->title,
                'description' => $request->description,
                'status'      => $request->status,
                'location_id'  => $this->location->id
            ]);

            if ($new_data) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil diupdate!'
                ];

                activity('banners')
                    ->performedOn(new Banner())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'data_lama' => [
                            'image'     => asset(Storage::disk($old_data->location->location)->url($old_data->image)),
                            'judul'     => $old_data->title,
                            'deskripsi' => $old_data->description,
                            'status'    => $old_data->status == 1 ? 'Aktif' : 'Tidak Aktif',
                            'location_id'  => $old_data->location_id
                        ],
                        'data_baru' => [
                            'image'     => asset(Storage::url($new_data->image)),
                            'judul'     => $new_data->title,
                            'deskripsi' => $new_data->description,
                            'status'    => $new_data->status == 1 ? 'Aktif' : 'Tidak Aktif',
                            'location_id'  => $new_data->location_id
                        ]
                    ])
                    ->log('mengubah data banner');
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
        $data    = Banner::find($id)->first();
        $destroy = Banner::find($id)->delete();

        if ($destroy) {
            Storage::delete($data->image);
            $response = [
                'status'  => 200,
                'message' => 'Berhasil dihapus!'
            ];

            activity('banners')
                ->performedOn(new Banner())
                ->causedBy(session('id'))
                ->withProperties([
                    'judul' => $data->title
                ])
                ->log('menghapus data banner');
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Gagal dihapus'
            ];
        }

        return response()->json($response);
    }
}
