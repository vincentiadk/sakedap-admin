<?php

namespace App\Http\Controllers\Admin;

use App\Models\Director;
use App\Models\Location;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DirectorController extends Controller
{
    protected $location;

    public function __construct()
    {
        $this->location = Location::first();
    }

    public function index()
    {
        $data = [
            'title'   => 'Master Pimpinan / Direktur',
            'province' => Province::all(),
            'content' => 'admin.master.director'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $whereLike = [
            'id',
            'signature',
            'province_id',
            'nip',
            'name',
            'position',
            'position_time'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $totalData = Director::count();
        if (empty($search)) {
            $queryData = Director::offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Director::count();
        } else {
            $queryData = Director::where(function ($query) use ($search) {
                $query->where('nip', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%");
            })
                ->offset($start)
                ->limit($length)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Director::where(function ($query) use ($search) {
                $query->where('nip', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%");
            })
                ->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                $response['data'][] = [
                    $nomor,
                    '<a href="' . $val->signature() . '" data-lightbox="' . $val->name . '" data-title="' . $val->name . '"><img src="' . $val->signature() . '" style="max-height:50px; max-width:50px;"></a>',
                    $val->province->name ?? '',
                    $val->nip,
                    $val->name,
                    $val->position,
                    $val->positionTime(),
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
            'signature' => 'image|max:1024|mimes:png',
            'nip' => 'required|unique:mysql.directors,nip',
            'province_id' => 'required',
            'name' => 'required',
            'position' => 'required',
            'position_start' => 'required'
        ], [
            'signature.image' => 'Tanda tangan harus berupa image',
            'signature.max' => 'Tanda tangan maksimal 1MB',
            'signature.mimes' => 'Tanda tangan yang didukung hanya ekstensi .png',
            'province_id.required' => 'Provinsi wajib di isi',
            'nip.required' => 'NIP wajib di isi',
            'nip.unique' => 'NIP sudah ada',
            'name.required' => 'Nama wajib di isi',
            'position.required' => 'Jabatan wajib di isi',
            'position_start.required' => 'Waktu menjabat wajib di isi'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            if ($request->hasFile('signature')) {
                $signature = Storage::disk($this->location->location)->put('public/director', $request->file('signature'));
            } else {
                $signature = null;
            }

            $create = Director::create([
                'signature' => $signature,
                'nip' => $request->nip,
                'name' => $request->name,
                'position' => $request->position,
                'position_start' => $request->position_start,
                'position_end' => $request->position_end,
                'location_id' => $this->location->id,
                'province_id' => $request->province_id,
            ]);

            if ($create) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil ditambahkan!'
                ];

                activity('directors')
                    ->performedOn(new Director())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'tanda_tangan' => $create->signature(),
                        'provinsi' => $create->province->name ?? '',
                        'nip' => $create->nip,
                        'nama' => $create->name,
                        'jabatan' => $create->position,
                        'waktu_menjabat' => $create->position_start,
                        'jabatan_berakhir' => $create->position_end
                    ])
                    ->log('Menambah data pimpinan / direktur');
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
        $data = Director::find($id);
        return response()->json([
            'signature' => $data->signature(),
            'province_id' => $data->province_id,
            'nip' => $data->nip,
            'name' => $data->name,
            'position' => $data->position,
            'position_start' => $data->position_start,
            'position_end' => $data->position_end
        ]);
    }

    public function update(Request $request, $id)
    {
        $query     = Director::find($id);
        $validator = Validator::make($request->all(), [
            'signature' => 'image|max:1024|mimes:png',
            'nip' => ['required', Rule::unique('mysql.directors', 'nip')->ignore($id)],
            'province_id' => 'required',
            'name' => 'required',
            'position' => 'required',
            'position_start' => 'required'
        ], [
            'signature.image' => 'Tanda tangan harus berupa image',
            'signature.max' => 'Tanda tangan maksimal 1MB',
            'signature.mimes' => 'Tanda tangan yang didukung hanya ekstensi .png',
            'nip.required' => 'NIP wajib di isi',
            'nip.unique' => 'NIP sudah ada',
            'province_id.required' => 'Provinsi wajib di isi',
            'name.required' => 'Nama wajib di isi',
            'position.required' => 'Jabatan wajib di isi',
            'position_start.required' => 'Waktu menjabat wajib di isi'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            if ($request->has('signature')) {
                if (Storage::disk($query->location->location)->exists($query->signature)) {
                    Storage::disk($query->location->location)->delete($query->signature);
                }

                $signature = Storage::disk($this->location->location)->put('public/director', $request->file('signature'));
            } else {
                $signature = $query->signature;
            }

            $old_data = $query;
            $new_data = Director::find($id);

            $new_data->update([
                'signature' => $signature,
                'nip' => $request->nip,
                'name' => $request->name,
                'position' => $request->position,
                'position_start' => $request->position_start,
                'position_end' => $request->position_end,
                'location_id' => $this->location->id,
                'province_id' => $request->province_id,
            ]);

            if ($new_data) {
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil diupdate!'
                ];

                activity('directors')
                    ->performedOn(new Director())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'data_lama' => [
                            'tanda_tangan' => $old_data->signature(),
                            'provinsi' => $old_data->province->name ?? '',
                            'nip' => $old_data->nip,
                            'nama' => $old_data->name,
                            'jabatan' => $old_data->position,
                            'waktu_menjabat' => $old_data->position_start,
                            'jabatan_berakhir' => $old_data->position_end
                        ],
                        'data_baru' => [
                            'tanda_tangan' => $new_data->signature(),
                            'provinsi' => $new_data->province->name ?? '',
                            'nip' => $new_data->nip,
                            'nama' => $new_data->name,
                            'jabatan' => $new_data->position,
                            'waktu_menjabat' => $new_data->position_start,
                            'jabatan_berakhir' => $new_data->position_end
                        ]
                    ])
                    ->log('Mengubah data director');
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
        $destroy = Director::where('id', $id)->delete();
        $data    = Director::withTrashed()->find($id);

        if ($destroy) {
            $response = [
                'status'  => 200,
                'message' => 'Berhasil dihapus!'
            ];

            activity('directors')
                ->performedOn(new Director())
                ->causedBy(session('id'))
                ->withProperties([
                    'nama' => $data->name
                ])
                ->log('Menghapus data director');
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Gagal dihapus'
            ];
        }

        return response()->json($response);
    }
}
