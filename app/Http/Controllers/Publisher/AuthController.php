<?php

namespace App\Http\Controllers\Publisher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Helper\GeneralHelper;
use App\Models\User;
use App\Models\Publisher;
use App\Models\PublisherAccess;
use App\Models\PublisherGroup;


class AuthController extends Controller
{

    /*public function login(Request $request)
    {
        if(session('id') ) {
            return redirect('admin/dashboard');
        } else {
            if($request->has('_token')) {
                $username = $request->username;
                $password = $request->password;
                $query    = User::where('username', $username);

                if($query->count() > 0) {
                    if(Hash::check($password, $query->first()->password)) {
                        session([
                            'id'       => $query->first()->id,
                            'username' => $query->first()->username,
                            'fullname' => $query->first()->admin->fullname,
                            'email'    => $query->first()->admin->email,
                            'address'  => $query->first()->admin->address,
                            'role_id'  => $query->first()->role_id
                        ]);

                        $query->update(['last_login' => date('Y-m-d H:i:s')]);
                        return redirect('admin/dashboard');
                    }
                }

                return redirect()->back()->with(['failed' => 'Maaf, kata sandi Anda salah. Harap periksa kembali kata sandi Anda.']);
            } else {
                return view('admin.auth.login');
            }
        }
    }*/

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
                $change = User::where('id', session('id'))->update([
                    'password' => Hash::make($request->password)
                ]);

                if ($change) {
                    return redirect()->back()->with(['success' => 'Password telah diganti!']);
                } else {
                    return redirect()->back()->with(['failed' => 'Password gagal diganti!']);
                }
            }
        } else {
            $data = [
                'title'   => 'Ganti Password',
                'content' => 'publisher.auth.change_password'
            ];

            return view('publisher.layout.index', ['data' => $data]);
        }
    }

    public function profile(Request $request)
    {
        if ($request->has('_token')) {
            $validator = Validator::make($request->all(), [
                'fullname'             => 'required',
                'address'            => 'required',
                'province_id'        => 'required',
                'district_id'        => 'required',
                'city_id'            => 'required',
                'village_id'        => 'required',
                // 'username'          => ['required', Rule::unique('mysql.users', 'username')->ignore(2, 'id')]
                // 'email'          => ['required', Rule::unique('mysql.users', 'email')->ignore(session('email'), 'email')]
            ], [
                'fullname.required'     => 'Nama wajib di isi!',
                // 'email.required'    	=> 'Email wajib di isi!',
                // 'email.emial'       	=> 'Email tidak valid!',
                'address.required'         => 'Alamat wajib di isi!',
                'province_id.required'     => 'Provinsi wajib di pilih!',
                'district_id.required'     => 'Kecamatan wajib di pilih!',
                'city_id.required'         => 'Kota wajib di pilih!',
                'village_id.required'     => 'Kelurahan wajib di pilih!',
                // 'email.unique'   	      => 'Email telah ada!'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator);
            } else {
                $user = User::find(session('id'));
                //$user = User::find(2);

                Publisher::where('id', $user->userable_id)->update([
                    'name'                 => $request->fullname,
                    'address'              => $request->address,
                    'province_id'          => $request->province_id,
                    'city_id'              => $request->city_id,
                    'district_id'          => $request->district_id,
                    'village_id'          => $request->village_id,
                ]);

                // User::where('id', session('id'))->update([
                //     'email'             => $request->email
                // ]);

                return redirect()->back()->with(['success' => 'Profile berhasil diupdate!']);
            }
        } else {

            //user 
            //$user = User::find(session('id'));
            //$user = User::find(2);
            $publisher = User::find(session('id'))->publisher;

            $data = [
                'title'         => 'Profile',
                'content'       => 'publisher.auth.profile',
                'publisher'     => $publisher,
                'groups'        => $publisher->getGroups()
            ];

            return view('publisher.layout.index', ['data' => $data]);
        }
    }

    public function logout()
    {
        User::where('id', session('id'))->update(['last_login' => date('Y-m-d H:i:s')]);
        //User::where('id', 2)->update(['last_login' => date('Y-m-d H:i:s')]);
        session()->flush();
        return redirect('publisher/login')->with(['success' => 'Anda telah logout.']);
    }

    public function connect()
    {
        $user = User::find(session('id'));
        $publisher = $user->publisher;
        $validator = Validator::make(request()->all(), [
            'username_isbn'             => 'required|unique:users,username,' . $user->id . ',id',
        ], [
            'username_isbn.required'     => 'Username wajib di isi!',
            'username_isbn.unique' => 'Username ' . request('username') . ' telah terdaftar!',
        ]);
        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error' => $validator->errors(),
            ];
            return response()->json($response);
        }
        try {
            $p = GeneralHelper::getDetailPublisher(request('username_isbn'), request('password_isbn'));
            if ($p) {
                if (User::where('email', $p["Email"])->count() > 1) {
                    $response = [
                        'status' => 422,
                        'error' => [
                            'Email sudah pernah terdaftar'
                        ],
                    ];
                    return response()->json($response);
                }

                $publisher->update([
                    'code_system' => $p["KdPenerbit"],
                    'system_type' => 'isbn',
                    'contact' => $p["Contact"],
                    'name' => $p["NamaPenerbit"],
                    'phone' => $p["Phone"],
                    'address' => $p["Address1"] != "" ? $p["Address1"] : $p["Address2"],
                    'type' => null,
                    'status' => 2,
                ]);

                $user = $user->update([
                    'username' => $p["UserName"],
                    'email' => $p["Email"],
                    'role_id' => 2,
                ]);
                return [
                    'status' => 200,
                    'message' => 'Sukses',
                ];
            } else {
                return [
                    'status' => 302,
                    'message' => 'Username Anda tidak ditemukan pada ISBN',
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => 302,
                'message' => 'Username Anda tidak ditemukan pada ISBN',
            ];
        }
    }
}
