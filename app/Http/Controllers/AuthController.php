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
            $login = QueryAPI::login($username, $password);

            if (($login->Status ?? '') == 'Success') {
                $userId = $login->Data->Id ?? null;
                $user = QueryAPI::get("
                    select
                        users.*,
                        branchs.province_id as province_id
                    from
                        users
                    left join
                        branchs on branchs.id = users.branch_id
                    where
                        users.id = $userId and
                        users.isdelete != 1
                ", true);

                if ($user) {
                    session([
                        'id' => $user->ID,
                        'username' => $user->USERNAME,
                        'name' => $user->FULLNAME,
                        'email' => $user->EMAILADDRESS,
                        'province_id' => $user->PROVINCE_ID,
                        'branch_id' => $user->BRANCH_ID,
                        'role_id' => $user->ROLE_ID,
                    ]);

                    return redirect()->intended('home');
                }
            }

            return redirect('/')->with(['failed' => 'Kredensial tidak ditemukan']);
        }

        return view('login');
    }

    public function logout()
    {
        session()->flush();

        return redirect('/');
    }
}
