<?php

namespace App\Http\Controllers;

use App\Helpers\Main;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (session('id')) {
            return redirect('home');
        }

        if ($request->_token == csrf_token()) {
            $validation = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required',
                'g-recaptcha-response' => 'required|captcha',
            ], [
                'username.required' => 'Username tidak boleh kosong',
                'password.required' => 'Password tidak boleh kosong',
                'g-recaptcha-response.required' => 'Terdeteksi robot',
                'g-recaptcha-response.captcha' => 'Captcha tidak valid',
            ]);

            if ($validation->fails()) {
                return redirect('/')->withErrors($validation);
            } else {
                $username = $request->username;
                $password = $request->password;
                $login = Main::login($username, $password);

                if ($login) {
                    return redirect()->intended('home');
                }

                return redirect('/')->with(['failed' => 'Kredensial tidak ditemukan']);
            }
        }

        return view('login');
    }

    public function logout()
    {
        session()->flush();

        return redirect('/');
    }
}
