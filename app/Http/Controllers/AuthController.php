<?php

namespace App\Http\Controllers;

use App\Helpers\Main;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
            $login = Main::login($username, $password);

            if ($login) {
                return redirect()->intended('home');
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
