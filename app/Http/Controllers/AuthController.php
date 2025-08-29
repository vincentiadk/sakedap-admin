<?php

namespace App\Http\Controllers;

use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (session('id')) {
            return redirect('home');
        }

        if ($request->_token == csrf_token()) {
            $username = $request->username;
            $password = $request->password;
            $lastLogin = date('Y-m-d H:i:s');

            $user = QueryAPI::get("
                select
                    e_users.*,
                    e_libraries.id as id_library,
                    e_libraries.province_id as province_id_library,
                    e_admins.fullname as fullname_admin,
                    e_admins.address as address_admin
                from
                    e_users
                left join
                    e_libraries on e_libraries.id = e_users.library_id
                inner join
                    e_admins on e_admins.id = e_users.userable_id
                where
                    e_users.username = '$username' and
                    e_users.userable_type = 'admins' and
                    e_users.enable = 1 and
                    e_users.verification_at is not null and
                    e_users.deleted_at is null
            ", true);

            if ($user) {
                session([
                    'id' => $user->ID,
                    'username' => $user->USERNAME,
                    'userable_type' => $user->USERABLE_TYPE,
                    'userable_id' => $user->USERABLE_ID,
                    'library_id' => $user->ID_LIBRARY,
                    'province_id' => $user->PROVINCE_ID_LIBRARY,
                    'last_login' => $lastLogin,
                    'fullname' => $user->FULLNAME_ADMIN,
                    'email' => $user->EMAIL,
                    'address' => $user->ADDRESS_ADMIN,
                ]);

                return redirect()->intended('home');
            }

            return redirect('/')->with(['failed' => 'Username dan Password tidak ditemukan']);
        }

        return view('login');
    }

    public function changePassword(Request $request)
    {
        if ($request->_token == csrf_token()) {
            $validation = Validator::make($request->all(), [
                'new_password' => 'required',
                'confirm_password' => 'required|same:new_password'
            ], [
                'new_password.required' => 'Password baru tidak boleh kosong',
                'confirm_password.required' => 'Konfirmasi password tidak boleh kosong',
                'confirm_password.same' => 'Konfirmasi password harus sama dengan password baru'
            ]);

            if ($validation->fails()) {
                return redirect()->back()->withErrors($validation);
            } else {
                try {
                    $change = QueryAPI::update('e_users', session('id'), [
                        'password' => Hash::make($request->new_password)
                    ]);

                    if ($change) {
                        $message = ['success' => 'Password berhasil diganti'];
                    } else {
                        $message = ['failed' => 'Password gagal diganti'];
                    }
                    return redirect('auth/change-password')->with($message);
                } catch (\Exception $e) {
                    return redirect()->back()->with([
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        $data = [
            'content' => 'change-password'
        ];

        return view('layouts.index', ['data' => $data]);
    }

    public function logout()
    {
        session()->flush();

        return redirect('/');
    }
}
