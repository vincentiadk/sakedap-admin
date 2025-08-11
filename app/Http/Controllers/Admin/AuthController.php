<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function changePassword(Request $request)
    {
        if ($request->has('_token')) {
            $validator = Validator::make($request->all(), [
                'password'         => 'required',
                'password_confirm' => 'required|same:password'
            ], [
                'password.required'         => 'Password wajib di isi!',
                'password_confirm.required' => 'Konfirmasi password wajib di isi!',
                'password_confirm.same'     => 'Konfirmasi password tidak sama!'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator);
            } else {
                $old    = User::find(session('id'));
                $change = User::find(session('id'));

                $change->update([
                    'password' => Hash::make($request->password)
                ]);

                if ($change) {
                    return redirect()->back()->with(['success' => 'Password telah diganti!']);

                    activity('admins')
                        ->performedOn(new Admin())
                        ->withProperties([
                            'nama_lengkap' => $old->admin->fullname
                        ])
                        ->log('Ganti password');
                } else {
                    return redirect()->back()->with(['failed' => 'Password gagal diganti!']);
                }
            }
        } else {
            $data = [
                'title'   => 'Ganti Password',
                'content' => 'admin.auth.change_password'
            ];

            return view('admin.layout.index', ['data' => $data]);
        }
    }

    public function profile(Request $request)
    {
        if ($request->has('_token')) {
            $validator = Validator::make($request->all(), [
                'fullname' => 'required',
                'email'    => 'required|email',
                'username' => ['required', Rule::unique('mysql.users')->ignore(session('id'))]
            ], [
                'fullname.required' => 'Nama wajib di isi!',
                'email.required'    => 'Email wajib di isi!',
                'email.emial'       => 'Email tidak valid!',
                'username.required' => 'Username wajib di isi!',
                'username.unique'   => 'Username telah ada!'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator);
            } else {
                $old_user  = User::find(session('id'));
                $old_admin = User::find(session('id'));
                $new_user  = User::find(session('id'));
                $new_admin = User::find(session('id'));

                $new_admin->update([
                    'fullname' => $request->fullname,
                    'address'  => $request->address,
                    'email'    => $request->email
                ]);

                $new_user->update([
                    'username' => $request->username
                ]);

                activity('admins')
                    ->performedOn(new Admin())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'data_lama' => [
                            'nama_lengkap' => $old_admin->fullname,
                            'alamat'       => $old_admin->address,
                            'email'        => $old_user->email,
                            'username'     => $old_user->username
                        ],
                        'data_baru' => [
                            'nama_lengkap' => $new_admin->fullname,
                            'alamat'       => $new_admin->address,
                            'email'        => $new_user->email,
                            'username'     => $new_user->username
                        ]
                    ])
                    ->log('Update profile');

                return redirect()->back()->with(['success' => 'Password telah diganti!']);
            }
        } else {
            $data = [
                'title'   => 'Profile',
                'content' => 'admin.auth.profile'
            ];

            return view('admin.layout.index', ['data' => $data]);
        }
    }
}
