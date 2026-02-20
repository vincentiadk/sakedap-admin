<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (session('id')) {
            return redirect('home');
        }

        if ($request->_token == csrf_token()) {
            $rateLimitMaxAttempt = config('system.retry_login');
            $rateLimitInterval = config('system.retry_login_interval');
            $rateLimitIntervalSecond = $rateLimitInterval * 60 * 60;
            $rateLimitKey = $request->ip();
            $retriesLeft = RateLimiter::retriesLeft($rateLimitKey, $rateLimitMaxAttempt);

            /*if (RateLimiter::tooManyAttempts($rateLimitKey, $rateLimitMaxAttempt)) {
                $seconds = RateLimiter::availableIn($rateLimitKey);
                $retryAt = Carbon::now()->addSeconds($seconds);
                $retryTime = $retryAt->diffForHumans();

                return redirect('/')->with('failed', "Terlalu banyak upaya login ($rateLimitMaxAttempt kali). Anda dapat mencoba kembali pada $retryTime");
            }*/

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
                // RateLimiter::hit($rateLimitKey, $rateLimitIntervalSecond);

                return redirect('/')->withErrors($validation);
            } else {
                $username = $request->username;
                $password = $request->password;
                $login = Main::login($username, $password);

                if ($login) {
                    // RateLimiter::clear($rateLimitKey);

                    return redirect()->intended('home');
                }

                //RateLimiter::hit($rateLimitKey, $rateLimitIntervalSecond);

                // return redirect('/')->with(['failed' => 'Kredensial tidak ditemukan, sisa percobaan login ' . $retriesLeft . 'x lagi']);
                return redirect('/')->with(['failed' => 'Kredensial tidak ditemukan']);
            }
        }

        return view('login');
    }

    public function changePassword(Request $request)
    {
        if ($request->_token == csrf_token()) {
            $validation = Validator::make($request->all(), [
                'new_password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d\s]).+$/',
                'confirm_password' => 'required|same:new_password'
            ], [
                'new_password.required' => 'Password baru tidak boleh kosong',
                'new_password.string' => 'Password baru harus text',
                'new_password.min' => 'Password baru minimal 8 karakter',
                'new_password.regex' => 'Password baru harus mengandung, 1 huruf besar, 1 huruf kecil, 1 angka, dan 1 simbol',
                'confirm_password.required' => 'Konfirmasi password tidak boleh kosong',
                'confirm_password.same' => 'Konfirmasi password harus sama dengan password baru'
            ]);

            if ($validation->fails()) {
                return redirect()->back()->withErrors($validation);
            } else {
                try {
                    $hash = QueryAPI::hashPassword($request->new_password);

                    if ($hash) {
                        $change = QueryAPI::update('users', session('id'), [
                            'password' => $hash->Output,
                            'updateby' => session('username'),
                            'updatedate' => date('Y-m-d H:i:s'),
                            'updateterminal' => $request->ip(),
                        ], false);

                        if ($change) {
                            $message = ['success' => 'Password berhasil diganti'];
                        } else {
                            $message = ['failed' => 'Password gagal diganti'];
                        }
                    } else {
                        $message = ['failed' => 'Gagal lakukan hash password'];
                    }

                    return redirect('auth/change-password')->with($message);
                } catch (\Exception $e) {
                    return redirect()->back()->with([
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        return view('layouts.index', [
            'data' => [
                'content' => 'change-password'
            ]
        ]);
    }

    public function profile(Request $request)
    {
        if ($request->_token == csrf_token()) {
            $validation = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
            ], [
                'name.required' => 'Nama tidak boleh kosong',
                'email.required' => 'Email tidak boleh kosong',
                'email.email' => 'Email tidak valid'
            ]);

            if ($validation->fails()) {
                return redirect()->back()->withErrors($validation);
            } else {
                try {
                    $change = QueryAPI::update('users', session('id'), [
                        'fullname' => $request->name,
                        'emailaddress' => $request->email,
                        'updateby' => session('username'),
                        'updatedate' => date('Y-m-d H:i:s'),
                        'updateterminal' => $request->ip(),
                    ], false);

                    if ($change) {
                        session([
                            'name' => $request->name,
                            'email' => $request->email,
                        ]);

                        $message = ['success' => 'Profil berhasil diganti'];
                    } else {
                        $message = ['failed' => 'Profil gagal diganti'];
                    }

                    return redirect('auth/profile')->with($message);
                } catch (\Exception $e) {
                    return redirect()->back()->with([
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        $data = [
            'content' => 'profile'
        ];

        return view('layouts.index', ['data' => $data]);
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
                    'expired_at' => date('Y-m-d H:i:s', strtotime('+' . config('system.limit_reset_password') . ' hours')),
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
                                $message->to($payloadEmail['email'], $payloadEmail['name'])
                                    ->subject('Permintaan Reset Password')
                                    ->from(config('mail.from.address'), config('mail.from.name'))
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
                    'new_password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d\s]).+$/',
                    'confirm_password' => 'required|same:new_password'
                ], [
                    'new_password.required' => 'Password baru tidak boleh kosong',
                    'new_password.string' => 'Password baru harus text',
                    'new_password.min' => 'Password baru minimal 8 karakter',
                    'new_password.regex' => 'Password baru harus mengandung, 1 huruf besar, 1 huruf kecil, 1 angka, dan 1 simbol',
                    'confirm_password.required' => 'Konfirmasi password tidak boleh kosong',
                    'confirm_password.same' => 'Konfirmasi password harus sama dengan password baru'
                ]);

                if ($validation->fails()) {
                    return redirect()->back()->withErrors($validation);
                } else {
                    try {
                        $hash = QueryAPI::hashPassword($request->new_password);

                        if ($hash) {
                            QueryAPI::update('users', $user->ID, [
                                'password' => $hash->Output,
                                'updateby' => $user->FULLNAME,
                                'updatedate' => date('Y-m-d H:i:s'),
                                'updateterminal' => $request->ip(),
                            ], false);

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
                                $message->to($bodyEmail['email'], $bodyEmail['name'])
                                    ->subject('Berhasil Reset Password')
                                    ->from(config('mail.from.address'), config('mail.from.name'))
                                    ->html(Main::parseTemplateEmail($bodyEmail, $templateEmailContent), 'text/html');
                            });

                            QueryAPI::update('e_password_resets', $check->ID, [
                                'expired_at' => date('Y-m-d H:i:s')
                            ], false);

                            return redirect('/')->with([
                                'success' => 'Password berhasil direset'
                            ]);
                        } else {
                            return redirect()->back()->with([
                                'failed' => 'Gagal mengelola password'
                            ]);
                        }
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
        try {
            Http::timeout(10)
                ->withOptions([
                    'verify' => false,
                ])
                ->get(config('inlis.base_url') . '/Sakedap_Monitoring/Logout.aspx');

            Http::timeout(10)
                ->withOptions([
                    'verify' => false,
                ])
                ->get(config('inlis.base_url') . '/Logout.aspx');
        } catch (\Exception $e) {
            Log::warning('External logout failed', [
                'error' => $e->getMessage(),
                'user_id' => session('id')
            ]);
        }

        session()->flush();
        session()->regenerate();

        return redirect('/')->with('success', 'Anda telah berhasil logout');
    }
}
