<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Province;
use App\Models\City;
use App\Models\District;
use App\Models\Village;
use App\Models\Otp;
use App\Helper\GeneralHelper;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailDaftarPublisher;
use Validator;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Support\Str;

class StepperController extends Controller
{
    public function index()
    {
        return view('stepper.index');
    }

    public function getDataIsbn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required'          => 'Username wajib di isi!',
            'password.required'          => 'Password wajib di isi!',

        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];

            return response()->json($response);
        }

        if ($request->has('_token')) {
            try {

                $usr = request('username');
                $pwd = request('password');

                $data = GeneralHelper::getDetailPublisher($usr, $pwd);
                if (!empty($data)) {
                    $response = [
                        'status'  => 200,
                        'message' => 'Berhasil',
                        'data'    => $data
                    ];
                } else {
                    $response = [
                        'status'  => 404,
                        'message' => 'User tidak terdaftar di ISBN.'
                    ];
                }
            } catch (\Exception $e) {
                $response = [
                    'status'  => 500,
                    'message' => $e->getMessage()
                ];
            }

            return response()->json($response);
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Token expired!'
            ];
            return response()->json($response);
        }
    }

    public function generateUniqueCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'        => 'required',
        ], [
            'email.required'          => 'Email wajib di isi!'
        ]);
        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => 'Gagal Generate OTP'
            ];

            return response()->json($response);
        }

        if ($request->has('_token')) {
            try {

                do {
                    $code = random_int(100000, 999999);
                } while (
                    Otp::where("otp", "=", $code)->first()
                );

                $otp = Otp::create([
                    'token' => Str::uuid()->toString(),
                    'timestap' => strtotime(date('Y-m-d H:i:s', strtotime('+1 minutes'))),
                    'otp' => $code
                ]);

                // $this->email(request('email'),$otp->otp);
                // $this->email('sa2705001@gmail.com',$otp->otp);

                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil Generate OTP',
                    'data'    => $otp
                ];
            } catch (\Exception $e) {
                $response = [
                    'status'  => 500,
                    'message' => $e->getMessage()
                ];
            }

            return response()->json($response);
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Token expired!'
            ];
            return response()->json($response);
        }
    }

    public function compareUniqueCode(Request $request)
    {
        $uuidOtp = request('uuidOtp');
        $otp = request('otp');
        $timeStampOtp = time();
        $dt = Otp::where("otp", "=", $otp)->where("token", "=", $uuidOtp)->first();
        if (empty($dt)) {
            $response = [
                'status' => 401,
                'message'  => 'OTP tidak valid.'
            ];

            return response()->json($response);
        }
        if ($dt->timestap < $timeStampOtp) {
            $response = [
                'status' => 401,
                'message'  => 'OTP expired.'
            ];

            return response()->json($response);
        }

        if ($request->has('_token')) {

            try {
                $publisher = Publisher::create([
                    'province_id'                => $request->provinsi,
                    'city_id'                    => $request->kota,
                    'district_id'                => $request->kecamatan,
                    'village_id'                 => $request->kelurahan,
                    'contact'                    => $request->phone_number,
                    'postal_code'                => $request->kode_pos,
                    'fax'                        => $request->fax,
                    'name'                       => $request->name,
                    'address'                    => $request->address,
                    'status'                     => 1,
                    'system_type'                => 'edep'
                ]);

                User::create([
                    'userable_type'   => 'publishers',
                    'userable_id'     => $publisher->id,
                    'username'        => $request->username,
                    'email'           => $request->email,
                    'password'        => \Hash::make($request->password),
                    'lang'            => 'id',
                    'enable'          => 1,
                    'role_id'         => 2,
                    'verification_at' => date('Y-m-d H:i:s')
                ]);

                $response = ['status' => 200, 'message' => 'Berhasil daftar! Mohon tunggu konfirmasi oleh admin'];
            } catch (\Exception $e) {
                session()->flash('failed', 'Gagal daftar!');
                $response = [
                    'status'  => 500,
                    'message' => $e->getMessage()
                ];
            }

            return response()->json($response);
        } else {
            session()->flash('failed', 'Gagal daftar!');
            $response = [
                'status'  => 500,
                'message' => 'Token expired!'
            ];
            return response()->json($response);
        }
    }

    public function GetDataIsrc(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'        => 'required',
            'password'        => 'required',
        ], [
            'username.required'          => 'Username wajib di isi!',
            'passowrd.required'          => 'Password wajib di isi!',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];

            return response()->json($response);
        }

        if ($request->has('_token')) {
            try {

                $response = Http::post('https://api-interoperabilitas.perpusnas.go.id/register/login-isrc', [
                    'username' => request('username'),
                    'password' => request('password'),
                ]);
                $detail_data = json_decode($response);
                if (!empty($detail_data) && $detail_data->status == 200) {
                    $response = [
                        'status'  => 200,
                        'message' => 'Berhasil',
                        'data'    => $detail_data->data
                    ];
                } else {
                    $response = [
                        'status'  => 404,
                        'message' => 'User tidak terdaftar di ISRC.'
                    ];
                }
            } catch (\Exception $e) {
                $response = [
                    'status'  => 500,
                    'message' => $e->getMessage()
                ];
            }

            return response()->json($response);
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Token expired!'
            ];
            return response()->json($response);
        }
    }
}
