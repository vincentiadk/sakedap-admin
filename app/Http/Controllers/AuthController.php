<?php

namespace App\Http\Controllers;

use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
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

    public function resetPasswordRequest(Request $request)
    {
        if (session('id')) {
            return redirect('home');
        }

        if ($request->_token == csrf_token()) {
            $email = $request->email;
            $checkEmail = QueryAPI::get("select * from users where emailaddress = '$email'", true);
            $templateEmail = QueryAPI::get("select * from e_settings where slug = 'ResetPassword'", true);

            if ($checkEmail) {
                $createRequest = QueryAPI::create('e_password_resets', [
                    'email' => $email,
                    'token' => Str::random(40),
                    'created_at' => date('Y-m-d H:i:s'),
                    'expired_at' => date('Y-m-d H:i:s', strtotime('+2 hours')),
                ], false);

                if ($createRequest) {
                    try {
                        $tokenUrl = url('reset-password-action?token=' . $createRequest->TOKEN . '&email=' . urlencode($email));
                        $payloadEmail = [
                            'name' => $checkEmail->FULLNAME,
                            'email' => $email,
                            'link' => '<a href="' . $tokenUrl . '">' . $tokenUrl . '</a>',
                        ];

                        if ($templateEmail) {
                            Mail::send([], [], function ($message) use ($payloadEmail, $templateEmail) {
                                $message->to($payloadEmail['email'], 'edeposit@perpusnas.go.id')
                                    ->subject('Permintaan Reset Password')
                                    ->from('edeposit@perpusnas.go.id', 'Info edeposit')
                                    ->html(Main::parseTemplateEmail($payloadEmail, $templateEmail), 'text/html');
                            });
                        }

                        return redirect('reset-password-request')->with('success', 'Kami telah mengirim email ke ' . $email);
                    } catch (\Exception $e) {
                        return redirect('reset-password-request')->with('failed', $e->getMessage());
                    }
                }
            } else {
                return redirect('reset-password-request')->with('failed', 'Email tidak terdaftar');
            }
        }

        return view('reset-password-request');
    }

    public function resetPasswordAction(Request $request)
    {
        $email = $request->email;
        $token = $request->token;
        $check = QueryAPI::get("select * from e_password_resets where email = '$email' and token = '$token'", true);
        $user = QueryAPI::get("select * from users where emailaddress = '$email'", true);

        if ($check && $user) {
            $currentTime = strtotime(date('Y-m-d H:i:s'));
            $expiredTime = strtotime(date('Y-m-d H:i:s', strtotime($check->EXPIRED_AT)));
            $diff = $expiredTime - $currentTime;
            $minutes = floor($diff / 60);

            if ($minutes < 0) {
                abort(419);
            }

            if ($request->_token == csrf_token()) {
                $validation = Validator::make($request->all(), [
                    'new_password' => 'required',
                    'confirm_password' => 'required|same:new_password'
                ], [
                    'new_password.required' => 'password baru tidak boleh kosong',
                    'confirm_password.required' => 'konfirmasi password tidak boleh kosong',
                    'confirm_password.same' => 'konfirmasi password harus sama dengan password baru'
                ]);

                if ($validation->fails()) {
                    return redirect()->back()->withErrors($validation);
                } else {
                    try {
                        $settings = QueryAPI::get("
                            select
                                *
                            from
                                e_settings
                            where
                                slug in ('GantiPassword','Header','Footer')
                        ");

                        $templateEmailContent = null;
                        $templateEmailHeader = null;
                        $templateEmailFooter = null;

                        if ($settings) {
                            foreach ($settings as $setting) {
                                if ($setting->SLUG == 'GantiPassword') {
                                    $templateEmailContent = $setting;
                                } elseif ($setting->SLUG == 'Header') {
                                    $templateEmailHeader = $setting;
                                } elseif ($setting->SLUG == 'Footer') {
                                    $templateEmailFooter = $setting;
                                }
                            }
                        }

                        $bodyEmail = [
                            'name' => $user->FULLNAME,
                            'email' => $user->EMAILADDRESS,
                            'header' => '<img src="' . Main::base64File(url('stream-file?type=gambar_template&id=' . ($templateEmailHeader->ID ?? '') . '&filename=' . ($templateEmailHeader->CONTENT ?? ''))) . '" style="max-width:100%;">',
                            'footer' => '<img src="' . Main::base64File(url('stream-file?type=gambar_template&id=' . ($templateEmailFooter->ID ?? '') . '&filename=' . ($templateEmailFooter->CONTENT ?? ''))) . '" style="max-width:100%; margin-bottom:10px">',
                        ];

                        Mail::send([], [], function ($message) use ($bodyEmail, $templateEmailContent) {
                            $message->to($bodyEmail['email'], 'edeposit@perpusnas.go.id')
                                ->subject('Berhasil Reset Password')
                                ->from('edeposit@perpusnas.go.id', 'Info edeposit')
                                ->html(Main::parseTemplateEmail($bodyEmail, $templateEmailContent), 'text/html');
                        });

                        return redirect('/')->with([
                            'success' => 'Password berhasil direset'
                        ]);
                    } catch (\Exception $e) {
                        return redirect()->back()->with([
                            'failed' => $e->getMessage()
                        ]);
                    }
                }
            }

            return view('reset-password-action');
        } else {
            abort(404);
        }
    }

    public function logout()
    {
        session()->flush();

        return redirect('/');
    }
}
