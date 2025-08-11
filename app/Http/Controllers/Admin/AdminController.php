<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use App\Models\Admin;
use App\Models\Library;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{

    public function index()
    {
        $data = [
            'title'   => 'Pengaturan User',
            'library' => Library::all(),
            'role'    => Role::all(),
            'content' => 'admin.setting.user'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $whereLike = [
            'id',
            'fullname',
            'role_id',
            'address',
            'email',
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        //$order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $totalData = Admin::has('user')
            ->count();
        if (empty($search)) {
            $queryData = Admin::has('user')
                ->offset($start)
                ->limit($length)
                //->orderBy($order, $dir)
                ->get();
            $totalFiltered = Admin::count();
        } else {
            $queryData = Admin::has('user')
                ->where(function ($query) use ($search) {
                    $query->where('fullname', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                })
                ->offset($start)
                ->limit($length)
                //->orderBy($order, $dir)
                ->get();
            $totalFiltered = Admin::has('user')
                ->where(function ($query) use ($search) {
                    $query->where('fullname', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                })
                ->count();
        }

        $response['data'] = [];
        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                $response['data'][] = [
                    $nomor,
                    $val->fullname,
                    $val->user->role->name,
                    '<span data-toggle="tooltip" title="' . $val->address . '">' . Str::limit($val->address, 20) . '</span>',
                    $val->user ? $val->user->email : "",
                    $val->user ? $val->user->username : "",
                    date('d-m-Y H:i:s', strtotime($val->user->last_login)),
                    '
                        <button type="button" onclick="resetPassword(' . $val->id . ')" class="btn btn-success btn-sm"><i class="la la-lock"></i> Reset Password</button>
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
            'fullname'         => 'required',
            'email'            => 'required|email',
            'username'         => 'required|unique:mysql.users,username',
            'password'         => 'required',
            'password_confirm' => 'required|same:password'
        ], [
            'fullname.required'         => 'Nama wajib di isi!',
            'email.required'            => 'Email wajib di isi!',
            'email.emial'               => 'Email tidak valid!',
            'username.required'         => 'Username wajib di isi!',
            'username.unique'           => 'Username telah ada!',
            'password.required'         => 'Password wajib di isi!',
            'password_confirm.required' => 'Konfirmasi password wajib di isi!',
            'password_confirm.same'     => 'Konfirmasi password tidak sama!'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $create = Admin::create([
                'fullname' => $request->fullname,
                'address'  => $request->address
            ]);

            if ($create) {
                $user = User::create([
                    'userable_type'   => 'admins',
                    'userable_id'     => $create->id,
                    'library_id'      => $request->library_id,
                    'role_id'         => $request->role_id,
                    'username'        => $request->username,
                    'email'           => $request->email,
                    'password'        => Hash::make($request->password),
                    'enable'          => true,
                    'verification_at' => date('Y-m-d H:i:s')
                ]);

                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil ditambahkan!'
                ];

                activity('admins')
                    ->performedOn(new Admin())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'nama_lengkap' => $create->fullname,
                        'alamat'       => $create->address,
                        'perpustakaan' => $user->library->name,
                        'role'         => $user->role,
                        'username'     => $user->username,
                        'email'        => $user->email,
                        'aktif'        => $user->enable ? 'Ya' : 'Tidak',
                        'verifikasi'   => date('Y-m-d H:i:s', strtotime($user->verification_at))
                    ])
                    ->log('Menambah data admin');
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
        $data = Admin::find($id);
        return response()->json([
            'fullname'   => $data->fullname,
            'library_id' => $data->user->library_id,
            'address'    => $data->address,
            'email'      => $data->user->email,
            'username'   => $data->user->username,
            'role_id'    => $data->user->role_id
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required',
            'email'    => 'required|email'
        ], [
            'fullname.required' => 'Nama wajib di isi!',
            'email.required'    => 'Email wajib di isi!',
            'email.emial'       => 'Email tidak valid!'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $data_admin  = Admin::find($id);
            $data_user   = User::where('userable_type', 'admins')->where('userable_id', $id)->first();
            $query       = Admin::find($id);

            $query->update([
                'fullname' => $request->fullname,
                'address'  => $request->address
            ]);

            if ($query) {
                User::where('userable_type', 'admins')->where('userable_id', $id)->update([
                    'library_id' => $request->library_id,
                    'role_id'    => $request->role_id,
                    'email'      => $request->email
                ]);

                $user = User::where('userable_type', 'admins')->where('userable_id', $id)->first();
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil diupdate!'
                ];

                activity('admins')
                    ->performedOn(new Admin())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'data_lama' => [
                            'nama_lengkap' => $data_admin->fullname,
                            'alamat'       => $data_admin->address,
                            'perpustakaan' => $data_user->library->name,
                            'role'         => $data_user->role,
                            'username'     => $data_user->username,
                            'email'        => $data_user->email,
                            'aktif'        => $data_user->enable ? 'Ya' : 'Tidak',
                            'verifikasi'   => date('Y-m-d H:i:s', strtotime($data_user->verification_at))
                        ],
                        'data_baru' => [
                            'nama_lengkap' => $query->fullname,
                            'alamat'       => $query->address,
                            'perpustakaan' => $user->library->name,
                            'role'         => $user->role,
                            'username'     => $user->username,
                            'email'        => $user->email,
                            'aktif'        => $user->enable ? 'Ya' : 'Tidak',
                            'verifikasi'   => date('Y-m-d H:i:s', strtotime($user->verification_at))
                        ]
                    ])
                    ->log('Mengubah data admin');
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
        $destroy = User::where('userable_type', 'admins')->where('userable_id', $id)->delete();
        $data    = User::withTrashed()->where('userable_type', 'admins')->where('userable_id', $id)->first();

        if ($destroy) {
            $response = [
                'status'  => 200,
                'message' => 'Berhasil dihapus!'
            ];

            activity('admins')
                ->performedOn(new Admin())
                ->causedBy(session('id'))
                ->withProperties([
                    'nama_lengkap'    => $data->admin->fullname,
                    'tanggal_dihapus' => $data->deleted_at->format('Y-m-d H:i:s')
                ])
                ->log('Menghapus data admin');
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Gagal dihapus'
            ];
        }

        return response()->json($response);
    }

    public function resetPassword($id)
    {
        $reset = User::where('userable_type', 'admins')->where('userable_id', $id)->update(['password' => Hash::make('eDepositV3')]);
        $data  = User::where('userable_type', 'admins')->where('userable_id', $id)->first();

        if ($reset) {
            $response = [
                'status'  => 200,
                'message' => 'Password telah direset!'
            ];

            activity('admins')
                ->performedOn(new Admin())
                ->causedBy(session('id'))
                ->withProperties([
                    'nama_lengkap' => $data->admin->fullname
                ])
                ->log('Reset password admin');
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Password gagal direset!'
            ];
        }

        return response()->json($response);
    }
}
