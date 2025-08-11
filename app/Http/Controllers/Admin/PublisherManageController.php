<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Library;
use App\Jobs\CheckIsbn;
use App\Models\Publisher;
use App\Models\Organization;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PublisherManageController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Penerbit Pengelolaan',
            'organization' => Organization::all(),
            'content' => 'admin.publisher.manage',
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $whereLike = [
            'id',
            'photo',
            'name',
            'email',
            'phone',
            'organization_id',
            'created_at',
        ];

        $start = $request->input('start');
        $length = $request->input('length');
        $order = $whereLike[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $totalData = Publisher::where('status', 2)->where(function ($query) use ($request) {
            $library_id = session('library_id');
            if ($library_id <> '1') {
                $library = Library::where('id', $library_id)->first();
                $query->where('province_id', $library->province_id);
            }
        })->count();
        $filtered = Publisher::where('status', 2)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('email', 'like', "%{$search}%");
                    });
            })
            ->where(function ($query) use ($request) {
                $library_id = session('library_id');
                if ($library_id <> '1') {
                    $library = Library::where('id', $library_id)->first();
                    $query->where('province_id', $library->province_id);
                }
            });
        $totalFiltered = $filtered->count();
        $queryData = $filtered->offset($start)
            ->limit($length)
            ->orderBy($order, $dir)
            ->get();

        $response['data'] = [];
        if ($queryData != false) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                $photo = '<a href="' . $val->photo() . '" data-lightbox="' . $val->name . '" data-title="' . $val->name . '"><img src="' . $val->photo() . '" style="max-height:50px; max-width:50px;"></a>';

                if ($val->organization_id && $val->organization_id != 0) {
                    $organization = $val->organization->name;
                } else {
                    $organization = 'Invalid';
                }

                $isbn = '';
                if ($val->code_system != "" && $val->system_type == "isbn") {
                    $isbn .= '<button type="button" onclick="sync(' . $val->id . ')" class="btn btn-primary btn-sm"><i class="la la-gear"></i> Sinkronisasi </button>';
                }



                $response['data'][] = [
                    $nomor,
                    $photo,
                    '<span data-toggle="tooltip" title="' . $val->name . '">' . Str::limit($val->name, 20) . '</span>',
                    $val->user ? $val->user->email : "",
                    $val->phone,
                    $organization,
                    date('d-m-Y', strtotime($val->created_at)),
                    '
                        <button type="button" onclick="show(' . $val->id . ')" class="btn btn-warning btn-sm"><i class="la la-pencil"></i> Edit</button>
                        <button type="button" onclick="destroy(' . $val->id . ')" class="btn btn-danger btn-sm"><i class="la la-trash"></i> Hapus</button>
                    '
                        . $isbn,
                    $val->warning->count(),
                ];
                $nomor++;
            }
        }

        $response['recordsTotal'] = 0;
        if ($totalData != false) {
            $response['recordsTotal'] = $totalData;
        }

        $response['recordsFiltered'] = 0;
        if ($totalFiltered != false) {
            $response['recordsFiltered'] = $totalFiltered;
        }

        return response()->json($response);
    }

    public function syncIsbn($id)
    {
        $job = new CheckIsbn($id, session('id'));
        dispatch(($job)->onQueue('check_isbn', $id));

        return response()->json([
            'status' => '200',
            'message' => 'Data penyerahan sedang di sinkronisasi dengan ISBN. Silahkan cek berkala pada menu data tagihan ISBN. Proses ini dapat memakan waktu 5 hingga 60 menit.',
        ]);
    }

    public function show($id)
    {
        $data = Publisher::find($id);
        if ($data->organization_id && $data->organization_id != 0) {
            $organization = $data->organization->id;
        } else {
            $organization = '';
        }

        return response()->json([
            'province' => $data->province_id ? $data->province->name : null,
            'province_id' => $data->province_id,
            'username' => $data->user ? $data->user->username : "",
            'city' => $data->city_id ? $data->city->name : null,
            'city_id' => $data->city_id,
            'district' => $data->district_id ? $data->district->name : null,
            'district_id' => $data->district_id,
            'village' => $data->village_id ? $data->village->name : null,
            'village_id' => $data->village_id,
            'organization' => $organization,
            'photo' => $data->photo(),
            'contact' => $data->contact,
            'fax' => $data->fax,
            'name' => $data->name,
            'name_change' => $data->name_change,
            'email' => $data->user ? $data->user->email : "",
            'phone' => $data->phone,
            'website' => $data->website,
            'address' => $data->address,
            'type' => $data->type,
            'code_system' => $data->code_system,
            'system_type' => $data->system_type,
            'created_at' => date('d-m-Y', strtotime($data->created_at)),
        ]);
    }

    public function update(Request $request, $id)
    {
        $publisher = Publisher::find($id);
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'organization_id' => 'required',
            'contact' => 'required',
            'fax' => 'required',
            'phone' => 'required',
            'type' => 'required',
        ], [
            'email.required' => 'Email wajib di isi',
            'email.email' => 'Email tidak valid',
            'organization_id.required' => 'Harap memilih organisasi',
            'contact.required' => 'Kontak wajib di isi',
            'fax.required' => 'No fax wajib di isi',
            'phone.required' => 'Telepon wajib di isi',
            'type.required' => 'Harap memilih tipe',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error' => $validator->errors(),
            ];
        } else {
            $old_data = $publisher;
            $new_data = Publisher::find($id);

            $new_data->update([
                'organization_id' => $request->organization_id,
                'contact' => $request->contact,
                'fax' => $request->fax,
                'name_change' => $request->name_change,
                'phone' => $request->phone,
                'website' => $request->website,
                'type' => $request->type,
                'province_id'       => $request->province_id,
                'city_id'           => $request->city_id,
                'district_id'       => $request->district_id,
                'village_id'        => $request->village_id,
            ]);

            if ($publisher->user) {
                User::where('userable_type', 'publishers')
                    ->where('userable_id', $id)
                    ->update(['email' => $request->email]);
            } else {
                $username = $request->username ? $request->username : Str::before($request->email, '@');
                User::create([
                    'userable_type' => 'publishers',
                    'userable_id' => $publisher->id,
                    'role_id' => null,
                    'username' => $username,
                    'email' => $request->email,
                    'password' => Hash::make($username),
                    'lang' => 'id',
                    'last_login' => date('Y-m-d H:i:s'),
                    'enable' => true,
                ]);
            }

            if ($new_data) {
                $response = [
                    'status' => 200,
                    'message' => 'Berhasil diupdate!',
                ];

                activity('publishers')
                    ->performedOn(new Publisher())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'data_lama' => [
                            'email' => $old_data->user->email,
                            'organisasi' =>  $old_data->organization ? $old_data->organization->name : '',
                            'kontak' => $old_data->contact,
                            'fax' => $old_data->fax,
                            'perubahan_nama' => $old_data->name_change,
                            'phone' => $old_data->phone,
                            'website' => $old_data->website,
                            'tipe' => $old_data->type(),
                        ],
                        'data_baru' => [
                            'email' => $new_data->user->email,
                            'organisasi' => $new_data->organization->name,
                            'kontak' => $new_data->contact,
                            'fax' => $new_data->fax,
                            'perubahan_nama' => $new_data->name_change,
                            'phone' => $new_data->phone,
                            'website' => $new_data->website,
                            'tipe' => $new_data->type(),
                        ],
                    ])
                    ->log('Mengubah data penerbit');
            } else {
                $response = [
                    'status' => 500,
                    'message' => 'Gagal diupdate',
                ];
            }
        }

        return response()->json($response);
    }

    public function destroy($id)
    {
        $destroy = Publisher::where('id', $id)->delete();
        $data = Publisher::withTrashed()->find($id);

        if ($destroy) {
            $response = [
                'status' => 200,
                'message' => 'Berhasil dihapus!',
            ];

            activity('publishers')
                ->performedOn(new Publisher())
                ->causedBy(session('id'))
                ->withProperties([
                    'nama' => $data->name,
                ])
                ->log('Menghapus data penerbit');
        } else {
            $response = [
                'status' => 500,
                'message' => 'Gagal dihapus',
            ];
        }

        return response()->json($response);
    }

    public function lockUnclock($id)
    {
        $publisher = Publisher::find($id);
        $type = request('type');
        if ($type == '1') {
            $publisher->update([
                'flag_lock' => 1,
            ]);
            if ($publisher) {
                $data = Publisher::find($id);
                $response = [
                    'status' => 200,
                    'message' => 'Berhasil diblokir!',
                ];

                activity('publishers')
                    ->performedOn(new Publisher())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'nama' => $data->name,
                    ])
                    ->log('Memblokir penerbit');
            } else {
                $response = [
                    'status' => 500,
                    'message' => 'Gagal diblokir',
                ];
            }
        } else {
            $publisher->update([
                'flag_lock' => 0,
            ]);
            if ($publisher) {
                $data = Publisher::find($id);
                $response = [
                    'status' => 200,
                    'message' => 'Berhasil membuka blokir!',
                ];

                activity('publishers')
                    ->performedOn(new Publisher())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'nama' => $data->name,
                    ])
                    ->log('Membuka blokir penerbit');
            } else {
                $response = [
                    'status' => 500,
                    'message' => 'Gagal membuka blokir',
                ];
            }
        }
    }
}
