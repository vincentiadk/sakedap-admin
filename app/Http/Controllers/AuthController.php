<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Location;
use App\Models\Publisher;
use Illuminate\Http\Request;
use App\Helper\GeneralHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $location;

    public function __construct()
    {
        $this->location = Location::where('active', 1)->first();
    }

    public function login(Request $request)
    {
        $message = null;
        $redirect = null;

        if (session('id')) {
            if (session('userable_type') == 'admins') {
                return redirect('admin/dashboard');
            } else if (session('userable_type') == 'publishers') {
                return redirect('publisher/dashboard');
            } else {
                return redirect('logout');
            }
        } else {
            // if(config('app.env') == 'production') {
            if ($request->has('_token')) {
                $username = $request->username;
                $password = $request->password;
                $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
                $query = User::where("$fieldType", $username)->first();
                $last_login = date('Y-m-d H:i:s');
                if ($query) { // username ditemukan

                    if ($query->userable_type == "admins") {
                        if (Hash::check($password, $query->password)) {

                            User::find($query->id)->update(['last_login' => $last_login]);

                            if ($query->library) {
                                $library_id = $query->library->id;
                                $province_id = $query->library->province_id;
                            } else {
                                $library_id = null;
                                $province_id = null;
                            }

                            session([
                                'id' => $query->id,
                                'username' => $query->username,
                                'userable_type' => $query->userable_type,
                                'userable_id' => $query->userable_id,
                                'library_id' => $library_id,
                                'province_id' => $province_id,
                                'role_id' => $query->role_id,
                                'last_login' => $last_login,
                                'fullname' => $query->admin->fullname,
                                'email' => $query->admin->email,
                                'address' => $query->admin->address,
                            ]);
                            return redirect('admin/dashboard');
                        } else {
                            return redirect()->back()->with([
                                'failed' => 'Password salah.',
                            ]);
                        }
                    } else if ($query->userable_type == "publishers") {
                        if ($query->publisher->system_type == "isbn") {
                            if ($this->checkIsbn($username, $password, $last_login)) {

                                if (config('app.env') === 'production') {
                                    $message = $this->checkUserAgent($request->userAgent());
                                    if ($message != null) {
                                        return redirect()->back()->with($message);
                                    }
                                }
                                return redirect('publisher/dashboard');
                            } else {
                                return redirect()->back()->with([
                                    'failed' => 'Maaf, username dan password akun ISBN Anda salah atau tidak ditemukan. Silakan hubungi administrator web ISBN.',
                                ]);
                            }
                        } else { //edeposit
                            if (Hash::check($password, $query->password)) {
                                if ($query->publisher->status == 1) {
                                    return redirect()->back()->with([
                                        'failed' => 'Maaf, user akun Anda belum diverifikasi oleh tim edeposit Perpusnas.',
                                    ]);
                                } else if ($query->publisher->status == 2) {

                                    $query->update(['last_login'  => $last_login]);

                                    session([
                                        'id' => $query->id,
                                        'username' => $query->username,
                                        'userable_type' => $query->userable_type,
                                        'userable_id' => $query->userable_id,
                                        'role_id' => $query->role_id,
                                        'fullname' => $query->publisher->name,
                                        'email' => $query->publisher->email,
                                        'address' => $query->publisher->address,
                                        'last_login' => $last_login,
                                        'user_agent_login'  => $request->userAgent()
                                    ]);
                                    if (config('app.env') === 'production') {
                                        $message = $this->checkUserAgent($request->userAgent());
                                        if ($message != null) {
                                            return redirect()->back()->with($message);
                                        }
                                    }

                                    return redirect('publisher/dashboard');
                                } else if ($query->publisher->status == 3) {
                                    return redirect()->back()->with([
                                        'failed' => 'Maaf, user akun Anda bermasalah, hubungi tim edeposit Perpusnas pada fitur chat.',
                                    ]);
                                }
                            } else {
                                return redirect()->back()->with([
                                    'failed' => 'Password salah.',
                                ]);
                            }
                        }
                    }
                } else { //username tidak ditemukan
                    if ($this->checkIsbn($username, $password, $last_login) == true) {

                        if (config('app.env') === 'production') {
                            $message = $this->checkUserAgent($request->userAgent());
                            if ($message != null) {
                                return redirect()->back()->with($message);
                            }
                        }

                        return redirect('publisher/dashboard');
                    } else if ($this->checkIsbn($username, $password, $last_login) == false) {
                        return redirect()->back()->with([
                            'failed' => 'Maaf, username atau email Anda tidak ditemukan pada data ISBN atau sistem lain.
                                Harap periksa kembali username atau email Anda.'
                        ]);
                    } else {
                        return redirect()->back()->with([
                            'failed' => 'Maaf, username Anda tidak ditemukan. Silakan coba lagi.',
                        ]);
                    }
                }
            } else {
                return view('login', [
                    'redirect'  => $redirect,
                    'message'  => $message,
                ]);
            }
            // } else {
            //     $user_id    = isset($_COOKIE['X-PZN-SESSION']) ? $_COOKIE['X-PZN-SESSION'] : null;
            //     $web_id     = '8cbf8dc5-2c63-4225-ab93-885133a1eb87';
            //     $domain_sso = 'https://api-interoperabilitas.perpusnas.go.id/';
            //     $base_url   = $domain_sso . 'sso/handle-user';
            //     $redirect   = $domain_sso . 'login?mst=' . $web_id;
            //     $last_login = date('Y-m-d H:i:s');

            //     if($user_id) {
            //         $data = Http::get($base_url, [
            //             'uuid'   => $user_id,
            //             'mstweb' => $web_id
            //         ]);

            //         if($data->status() == 200) {
            //             $detail_data = json_decode($data);
            //             if(!$detail_data->error) {
            //                 $decode_data = GeneralHelper::decodeAes($detail_data->data);
            //                 if($decode_data) {
            //                     if($decode_data->email) {
            //                         $email = collect($decode_data->email)->pluck('email')->all();
            //                     } else {
            //                         $email = [];
            //                     }

            //                     $query = User::whereIn('email', $email)->first();
            //                     if($query) {
            //                         if($query->userable_type == "admins") {
            //                             User::find($query->id)->update(['last_login' => $last_login]);

            //                             if($query->library) {
            //                                 $library_id  = $query->library->id;
            //                                 $province_id = $query->library->province_id;
            //                             } else {
            //                                 $library_id  = null;
            //                                 $province_id = null;
            //                             }

            //                             session([
            //                                 'id'            => $query->id,
            //                                 'username'      => $query->username,
            //                                 'userable_type' => $query->userable_type,
            //                                 'userable_id'   => $query->userable_id,
            //                                 'library_id'    => $library_id,
            //                                 'province_id'   => $province_id,
            //                                 'role_id'       => $query->role_id,
            //                                 'last_login'    => $last_login,
            //                                 'fullname'      => $query->admin->fullname,
            //                                 'email'         => $query->admin->email,
            //                                 'address'       => $query->admin->address
            //                             ]);

            //                             return redirect('admin/dashboard');
            //                         } else if ($query->userable_type == "publishers") {
            //                             if($query->publisher->system_type == "isbn") {
            //                                 if($this->checkIsbn($username, $password, $last_login)) {
            //                                     if(config('app.env') === 'production') {
            //                                         $message = $this->checkUserAgent($request->userAgent());
            //                                         if($message != null) {
            //                                             return redirect()->back()->with($message);
            //                                         }
            //                                     }

            //                                     return redirect('publisher/dashboard');
            //                                 } else {
            //                                     return view('login', [
            //                                         'redirect' => $redirect,
            //                                         'message'  => 'Maaf, user ISBN anda tidak ditemukan'
            //                                     ]);
            //                                 }
            //                             } else {
            //                                 if($query->publisher->status == 1) {
            //                                     return view('login', [
            //                                         'redirect' => $redirect,
            //                                         'message'  => 'Maaf, user akun anda belum diverifikasi oleh tim edeposit perpusnas'
            //                                     ]);
            //                                 } else if ($query->publisher->status == 2) {
            //                                     $query->update(['last_login'  => $last_login]);

            //                                     session([
            //                                         'id'               => $query->id,
            //                                         'username'         => $query->username,
            //                                         'userable_type'    => $query->userable_type,
            //                                         'userable_id'      => $query->userable_id,
            //                                         'role_id'          => $query->role_id,
            //                                         'fullname'         => $query->publisher->name,
            //                                         'email'            => $query->publisher->email,
            //                                         'address'          => $query->publisher->address,
            //                                         'last_login'       => $last_login,
            //                                         'user_agent_login' => $request->userAgent()
            //                                     ]);

            //                                     return redirect('publisher/dashboard');
            //                                 } else if ($query->publisher->status == 3) {
            //                                     return view('login', [
            //                                         'redirect' => $redirect,
            //                                         'message'  => 'Maaf, user akun anda bermasalah, hubungi kami pada fitur live chat'
            //                                     ]);
            //                                 }
            //                             }
            //                         } else {
            //                             return view('login', [
            //                                 'redirect' => $redirect,
            //                                 'message'  => 'Invalid user'
            //                             ]);
            //                         }
            //                     } else {
            //                         return view('login', [
            //                             'redirect' => $redirect,
            //                             'message'  => 'User eDeposit tidak ditemukan'
            //                         ]);
            //                     }
            //                 } else {
            //                     return view('login', [
            //                         'redirect' => $redirect,
            //                         'message'  => 'Data tidak dapat di ekstrak'
            //                     ]);
            //                 }
            //             } else {
            //                 return view('login', [
            //                     'redirect' => $redirect,
            //                     'message'  => 'Data tidak ada akses di eDeposit'
            //                 ]);
            //             }
            //         } else {
            //             return view('login', [
            //                 'redirect' => $redirect,
            //                 'message'  => 'Data tidak ditemukan'
            //             ]);
            //         }
            //     } else {
            //         return view('login', [
            //             'redirect' => $redirect,
            //             'message'  => null
            //         ]);
            //     }
            // }
        }
    }

    public function checkIsbn($username, $password, $last_login)
    {
        $p = GeneralHelper::getDetailPublisher($username, $password);
        if ($p) {
            $publisher = Publisher::updateOrCreate([
                'code_system' => $p["KdPenerbit"],
                'system_type' => 'isbn',
            ], [
                'contact' => $p["Contact"],
                'name' => $p["NamaPenerbit"],
                'phone' => $p["Phone"],
                'address' => $p["Address1"] != "" ? $p["Address1"] : $p["Address2"],
                'type' => null,
                'status' => 2,

            ]);

            $user = User::updateOrCreate([
                'userable_type' => 'publishers',
                'userable_id' => $publisher->id,
                'username' => $p["UserName"],
                'role_id' => 2,
            ], [
                'email' => $p["Email"],
                'last_login' => $last_login
            ]);

            session([
                'id' => $user->id,
                'username' => $p["UserName"],
                'userable_type' => $user->userable_type,
                'userable_id' => $user->userable_id,
                'role_id' => 2,
                'fullname' => $p["NamaPenerbit"],
                'email' => $p["Email"],
                'address' => $p["Address1"] != "" ? $p["Address1"] : $p["Address2"],
                'last_login' => $last_login
            ]);
            return true;
        } else {
            return false;
        }
    }

    private function checkUserAgent($user_agent)
    {

        // $user = User::find(session('id'));


        // if($user->user_agent_login != null) {
        //     if($user->user_agent_login != $user_agent) {
        //         session()->flush();
        //         return [
        //             'failed' => 'Maaf, user akun Anda sudah login di device atau browser lain. mohon untuk logout terlebih dahulu.',
        //         ];
        //     }
        // }

        // $user->update([
        //     'last_login'        => date('Y-m-d H:i:s'),
        //     'user_agent_login'  => $user_agent
        // ]);

        return null;
    }

    public function logout()
    {
        User::where('id', session('id'))->update([
            'last_login'        => date('Y-m-d H:i:s'),
            'user_agent_login'  => null
        ]);

        session()->flush();
        // if (config('app.env') == 'local') {
        //     return redirect()->away('https://api-interoperabilitas.perpusnas.go.id/sso/logout?mst=8cbf8dc5-2c63-4225-ab93-885133a1eb87');
        // } else {
        return redirect('login');
        // }
    }

    public function register(Request $request)
    {
        Validator::extend('without_spaces', function ($attr, $value) {
            return preg_match('/^\S*$/u', $value);
        });

        if ($request->has('_token')) {
            if ($request->category == 1) {
                $validator = Validator::make($request->all(), [
                    'name'         => 'required',
                    'username'     => 'required|without_spaces|unique:users,username|min:6',
                    'category'     => 'required',
                    'email'        => 'required|email|unique:users,email',
                    'password'     => 'required',
                    'address'      => 'required',
                    'phone_number' => 'required',
                    'province_id'  => 'required',
                    'city_id'      => 'required',
                    'district_id'  => 'required',
                    'village_id'   => 'required',
                    'surat'        => 'required|max:1000|mimes:pdf',
                    'akta'         => 'required|max:1000|mimes:pdf'
                ], [
                    'name.required'           => 'Nama Pelaksana wajib di isi!',
                    'username.required'       => 'Username wajib di isi!',
                    'username.unique'         => 'Username telah ada!',
                    'username.without_spaces' => 'Username tidak boleh menggunakan spasi',
                    'username.min'            => 'Username minimal 6 Karakter!',
                    'category.required'       => 'Kategori wajib di isi!',
                    'email.required'          => 'Email wajib di isi!',
                    'email.unique'            => 'Email Telah Terdaftar!',
                    'password.required'       => 'Password wajib di isi!',
                    'address.required'        => 'Alamat wajib di isi!',
                    'phone_number.required'   => 'No. Telp wajib di isi!',
                    'province_id.required'    => 'Provinsi wajib di isi!',
                    'city_id.required'        => 'Kota/Kab wajib di isi!',
                    'district_id.required'    => 'Kecamatan wajib di isi!',
                    'village_id.required'     => 'Desa wajib di isi!',
                    'surat.required'          => 'Surat Pernyataan wajib di isi!',
                    'surat.image'             => 'Surat Pernyataan berupa file image!',
                    'surat.max'               => 'Surat Pernyataan maksimal 1MB!',
                    'surat.mimes'             => 'Surat Pernyataan harus bertipe pdf!',
                    'akta.required'           => 'Akta Perusahaan wajib di isi!',
                    'akta.image'              => 'Akta Perusahaan berupa file image!',
                    'akta.max'                => 'Akta Perusahaan maksimal 1MB!',
                    'akta.mimes'              => 'Akta Perusahaan harus bertipe pdf!'
                ]);
            } else if ($request->category == 2) {
                $validator = Validator::make($request->all(), [
                    'name'         => 'required',
                    'username'     => 'required|without_spaces|unique:users,username|min:6',
                    'category'     => 'required',
                    'email'        => 'required|email|unique:users,email',
                    'password'     => 'required',
                    'address'      => 'required',
                    'phone_number' => 'required',
                    'province_id'  => 'required',
                    'city_id'      => 'required',
                    'district_id'  => 'required',
                    'village_id'   => 'required',
                    'surat'        => 'required|max:1000|mimes:pdf',
                    'akta'         => 'required|max:1000|mimes:pdf'
                ], [
                    'name.required'           => 'Nama Pelaksana wajib di isi!',
                    'username.required'       => 'Username wajib di isi!',
                    'username.unique'         => 'Username telah ada!',
                    'username.without_spaces' => 'Username tidak boleh menggunakan spasi',
                    'username.min'            => 'Username minimal 6 Karakter!',
                    'category.required'       => 'Kategori wajib di isi!',
                    'email.required'          => 'Email wajib di isi!',
                    'email.unique'            => 'Email Telah Terdaftar!',
                    'password.required'       => 'Password wajib di isi!',
                    'address.required'        => 'Alamat wajib di isi!',
                    'phone_number.required'   => 'No. Telp wajib di isi!',
                    'province_id.required'    => 'Provinsi wajib di isi!',
                    'city_id.required'        => 'Kota/Kab wajib di isi!',
                    'district_id.required'    => 'Kecamatan wajib di isi!',
                    'village_id.required'     => 'Desa wajib di isi!',
                    'surat.required'          => 'Surat Pernyataan wajib di isi!',
                    'surat.image'             => 'Surat Pernyataan berupa file image!',
                    'surat.max'               => 'Surat Pernyataan maksimal 1MB!',
                    'surat.mimes'             => 'Surat Pernyataan harus bertipe pdf!',
                    'akta.required'           => 'KTP wajib di isi!',
                    'akta.image'              => 'KTP berupa file image!',
                    'akta.max'                => 'KTP maksimal 1MB!',
                    'akta.mimes'              => 'KTP harus bertipe pdf!'
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    'name'         => 'required',
                    'username'     => 'required|without_spaces|unique:users,username|min:6',
                    'category'     => 'required',
                    'email'        => 'required|email|unique:users,email',
                    'password'     => 'required',
                    'address'      => 'required',
                    'phone_number' => 'required',
                    'province_id'  => 'required',
                    'city_id'      => 'required',
                    'district_id'  => 'required',
                    'village_id'   => 'required',
                    'surat'        => 'required|max:1000|mimes:pdf'
                ], [
                    'name.required'           => 'Nama Pelaksana wajib di isi!',
                    'username.required'       => 'Username wajib di isi!',
                    'username.unique'         => 'Username telah ada!',
                    'username.without_spaces' => 'Username tidak boleh menggunakan spasi',
                    'username.min'            => 'Username minimal 6 Karakter!',
                    'category.required'       => 'Kategori wajib di isi!',
                    'email.required'          => 'Email wajib di isi!',
                    'email.unique'            => 'Email Telah Terdaftar!',
                    'password.required'       => 'Password wajib di isi!',
                    'address.required'        => 'Alamat wajib di isi!',
                    'phone_number.required'   => 'No. Telp wajib di isi!',
                    'province_id.required'    => 'Provinsi wajib di isi!',
                    'city_id.required'        => 'Kota/Kab wajib di isi!',
                    'district_id.required'    => 'Kecamatan wajib di isi!',
                    'village_id.required'     => 'Desa wajib di isi!',
                    'surat.required'          => 'Surat Pernyataan wajib di isi!',
                    'surat.image'             => 'Surat Pernyataan berupa file image!',
                    'surat.max'               => 'Surat Pernyataan maksimal 1MB!',
                    'surat.mimes'             => 'Surat Pernyataan harus bertipe pdf!'
                ]);
            }

            if ($validator->fails()) {
                $response = [
                    'status' => 422,
                    'error'  => $validator->errors()
                ];

                return response()->json($response);
            }

            $akta       = $request->file('akta');
            $surat      = $request->file('surat');
            $path_akta  = null;
            $path_surat = null;

            if ($request->category == 1 || $request->category == 2) {
                $path_akta  = Storage::disk($this->location->location)->put('public/publisher/birth_certificate', $akta);
                $path_surat = Storage::disk($this->location->location)->put('public/publisher/statement_letter', $surat);
            }

            try {
                $publisher = Publisher::create([
                    'province_id'                => $request->province_id,
                    'city_id'                    => $request->city_id,
                    'district_id'                => $request->district_id,
                    'village_id'                 => $request->village_id,
                    'contact'                    => $request->phone_number,
                    'fax'                        => $request->fax,
                    'name'                       => $request->name,
                    'address'                    => $request->address,
                    'type'                       => $request->category,
                    'statement_letter'           => $path_surat,
                    'birth_certificate'          => $path_akta,
                    'statement_letter_location'  => $this->location->id,
                    'birth_certificate_location' => $this->location->id,
                    'status'                     => 1,
                    'system_type'                => 'edep'
                ]);

                User::create([
                    'userable_type'   => 'publishers',
                    'userable_id'     => $publisher->id,
                    'username'        => $request->username,
                    'email'           => $request->email,
                    'password'        => Hash::make($request->password),
                    'lang'            => 'id',
                    'enable'          => 1,
                    'role_id'         => 2,
                    'verification_at' => date('Y-m-d H:i:s')
                ]);

                session()->flash('success', 'Berhasil daftar! Mohon tunggu konfirmasi oleh admin');
                $response = ['status' => 200];
            } catch (\Exception $e) {
                session()->flash('failed', 'Gagal daftar!');
                $response = [
                    'status'  => 500,
                    'message' => 'Gagal daftar'
                ];
            }

            return response()->json($response);
        } else {
            return view('register');
        }
    }

    public function registerSuccess()
    {
        return view('register-success', ['data' => []]);
    }
}
