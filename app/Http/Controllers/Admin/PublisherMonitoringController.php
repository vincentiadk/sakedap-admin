<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Library;
use App\Models\Setting;
use App\Models\Publisher;
use App\Models\Organization;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PublisherMonitoringController extends Controller
{
    public function index()
    {
        $data = [
            'title'        => 'Penerbit Pemantauan',
            'organization' => Organization::all(),
            'content'      => 'admin.publisher.monitoring'
        ];

        return view('admin.layout.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $whereLike = [
            'id',
            'photo',
            'name',
            'email',
            'phone',
            'organization_id',
            'created_at'
        ];

        $start  = $request->input('start');
        $length = $request->input('length');
        $order  = $whereLike[$request->input('order.0.column')];
        $dir    = $request->input('order.0.dir');
        $search = $request->input('search.value');

        $totalData = Publisher::where('status', 1)
            ->where(function ($query) use ($request) {
                $library_id = session('library_id');
                if ($library_id <> '1') {
                    $library = Library::where('id', $library_id)->first();
                    $query->where('province_id', $library->province_id);
                }
            })
            ->count();
        $filtered = Publisher::where('status', 1)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($query) use ($search) {
                        $query->where('email', 'like', "%{$search}%");
                    });
            })
            ->where(function ($query) use ($request) {
                $library_id = session('library_id');
                if ($library_id <> '1') {
                    $library = Library::where('id', $library_id)->first();
                    $query->where('province_id', $library->province_id);
                }
            });
        $totalFiltered = $filtered->count();
        $queryData = $filtered->offset($start)
            ->limit($length)
            ->orderBy($order, $dir)
            ->get();

        $response['data'] = [];
        if ($queryData <> FALSE) {
            $nomor = $start + 1;
            foreach ($queryData as $val) {
                $photo = '<a href="' . $val->photo() . '" data-lightbox="' . $val->name . '" data-title="' . $val->name . '"><img src="' . $val->photo() . '" style="max-height:50px; max-width:50px;"></a>';

                if ($val->organization_id && $val->organization_id != 0) {
                    $organization = $val->organization->name;
                } else {
                    $organization = 'Invalid';
                }

                $response['data'][] = [
                    $nomor,
                    $photo,
                    '<span data-toggle="tooltip" title="' . $val->name . '">' . Str::limit($val->name, 20) . '</span>',
                    $val->user ? $val->user->email : "TIDAK ADA EMAIL",
                    $val->phone,
                    $organization,
                    date('d-m-Y', strtotime($val->created_at)),
                    '
                        <button type="button" onclick="show(' . $val->id . ')" class="btn btn-info btn-sm"><i class="la la-info-circle"></i> Review</button>
                        <button type="button" onclick="destroy(' . $val->id . ')" class="btn btn-danger btn-sm"><i class="la la-trash"></i> Hapus</button>
                    '
                ];
                $nomor++;
            }
        }

        $response['recordsTotal'] = 0;
        if ($totalData <> FALSE) {
            $response['recordsTotal'] = $totalData;
        }

        $response['recordsFiltered'] = 0;
        if ($totalFiltered <> FALSE) {
            $response['recordsFiltered'] = $totalFiltered;
        }

        return response()->json($response);
    }

    public function show($id)
    {
        $data = Publisher::find($id);
        if ($data->organization_id && $data->organization_id != 0) {
            $organization = $data->organization->id;
        } else {
            $organization = '';
        }

        return response()->json([
            'province'       => $data->province_id ? $data->province->name : null,
            'city'           => $data->city_id ? $data->city->name : null,
            'district'       => $data->district_id ? $data->district->name : null,
            'village'        => $data->village_id ? $data->village->name : null,
            'organization'   => $organization,
            'photo'          => $data->photo(),
            'contact'        => $data->contact,
            'fax'            => $data->fax,
            'name'           => $data->name,
            'email'          => $data->user ? $data->user->email : "",
            'username'       => $data->user ? $data->user->username : "",
            'phone'          => $data->phone,
            'website'        => $data->website,
            'address'        => $data->address,
            'type'           => $data->type,
            'status'         => $data->status,
            'created_at'     => date('d-m-Y', strtotime($data->created_at))
        ]);
    }

    public function review(Request $request, $id)
    {
        $user = User::where('userable_type', 'publishers')->where('userable_id', $id)->first();
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'email'    => $user ? 'required|email|unique:users,email,' . $user->id : 'required|email|unique:users,email',
            'username' => $user ? 'required|unique:users,username, ' . $user->id . '|min:6|regex:/^\S*$/u' : 'required|unique:users,username|min:6|regex:/^\S*$/u',
        ], [
            'name.required'     => 'Nama Pelaksana wajib di isi!',
            'username.required' => 'Username wajib di isi!',
            'username.unique'   => 'Username telah ada!',
            'username.regex'    => 'Username tidak boleh menggunakan spasi',
            'username.min'      => 'Username minimal 6 Karakter!',
            'email.required'    => 'Email wajib di isi!',
            'email.email'       => 'Email tidak valid!',
            'email.unique'      => 'Email Telah Terdaftar!'
        ]);


        if ($validator->fails()) {
            $response = [
                'status' => 422,
                'error'  => $validator->errors()
            ];
        } else {
            $publisher = Publisher::find($id);
            if ($request->status == 3) {
                User::where('userable_id', $id)->where('userable_type', 'publishers')->delete();
                Publisher::where('id', $id)->delete();

                Mail::send([], [], function ($message) use ($publisher, $request) {
                    $header      = Setting::where('slug', 'template-email-header')->first();
                    $footer      = Setting::where('slug', 'template-email-footer')->first();
                    $link_header = public_path('storage/' . str_replace('public/', '', $header->content));
                    $link_footer = public_path('storage/' . str_replace('public/', '', $footer->content));

                    $data = [
                        'header'    => '<img src="' . $message->embed($link_header) . '" style="width:100%;">',
                        'footer'    => '<img src="' . $message->embed($link_footer) . '" style="width:100%;">',
                        'publisher' => $publisher->name,
                        'problem'   => $request->problem
                    ];

                    $template = Setting::where('slug', 'template-email-publisher-rejected')->first();
                    $message->to($publisher->user->email, 'edeposit@perpusnas.go.id')
                        ->subject('Pendaftaran eDeposit Bermasalah')
                        ->from('edeposit@perpusnas.go.id', 'Info pendaftaran eDeposit')
                        ->setBody($template->parse($data), 'text/html');
                });

                activity('publishers')
                    ->performedOn(new Publisher())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'nama' => $publisher->name
                    ])
                    ->log('Menolak penerbit (' . $publisher->name . ')');

                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil diproses!'
                ];
            } else if ($request->status == 2) {
                Publisher::where('id', $id)->update([
                    'status' => 2
                ]);

                $password = Str::random(6);
                if ($publisher->user) {

                    $user = $publisher->user;
                    $user->update([
                        'userable_id'   => $id,
                        'userable_type' => 'publishers',
                        'verified_at'   => date('Y m d H:i:s'),
                        'role_id'       => 2,
                        //'password'      => Hash::make($password)
                    ]);
                } else {
                    $user = User::create([
                        'userable_id'   => $id,
                        'userable_type' => 'publishers',
                        'verified_at'   => date('Y m d H:i:s'),
                        'role_id'       => 2,
                        'lang'          => 'id',
                        'enable'        => 1,
                        'username'      => $request->username,
                        'email'         => $request->email,
                        'password'      => Hash::make($password)
                    ]);
                }

                Mail::send([], [], function ($message) use ($publisher, $request, $password, $user) {
                    $header      = Setting::where('slug', 'template-email-header')->first();
                    $footer      = Setting::where('slug', 'template-email-footer')->first();
                    $link_header = public_path('storage/' . str_replace('public/', '', $header->content));
                    $link_footer = public_path('storage/' . str_replace('public/', '', $footer->content));

                    $data = [
                        'header'    => '<img src="' . $message->embed($link_header) . '" style="width:100%;">',
                        'footer'    => '<img src="' . $message->embed($link_footer) . '" style="width:100%;">',
                        'publisher' => $publisher->name,
                        'username'  => $request->username,
                        'password'  => $publisher->system_type == 'edep' ? 'Password yang telah Anda masukan saat daftar' : $password
                    ];

                    $template = Setting::where('slug', 'template-email-publisher-success-manual')->first();
                    $message->to($user->email, 'edeposit@perpusnas.go.id')
                        ->subject('Pendaftaran eDeposit Diterima')
                        ->from('edeposit@perpusnas.go.id', 'Info pendaftaran eDeposit')
                        ->setBody($template->parse($data), 'text/html');
                });

                activity('publishers')
                    ->performedOn(new Publisher())
                    ->causedBy(session('id'))
                    ->withProperties([
                        'nama' => $publisher->name
                    ])
                    ->log('Menyetujui penerbit (' . $publisher->name . ')');

                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil diproses!'
                ];
            } else {
                $password = Str::random(6);
                $user = User::createOrUpdate([
                    'userable_id'   => $id,
                    'userable_type' => 'publishers'
                ], [
                    'verified_at'   => date('Y m d H:i:s'),
                    'role_id'       => 2,
                    'username'      => $request->username,
                    'email'         => $request->email,
                ]);
                $response = [
                    'status'  => 200,
                    'message' => 'Berhasil diupdate!'
                ];
            }
        }

        return response()->json($response);
    }

    public function destroy($id)
    {
        $check = Publisher::find($id)->collection->count();
        if ($check > 0) {
            return response()->json([
                'status' => 500,
                'message' => 'Penerbit tidak bisa dihapus karena sudah memiliki koleksi'
            ]);
        }

        User::where('userable_id', $id)->where('userable_type', 'publishers')->forceDelete();
        $destroy = Publisher::where('id', $id)->delete();
        $data    = Publisher::withTrashed()->find($id);

        if ($destroy) {
            $response = [
                'status'  => 200,
                'message' => 'Berhasil dihapus!'
            ];

            activity('publishers')
                ->performedOn(new Publisher())
                ->causedBy(session('id'))
                ->withProperties([
                    'nama' => $data->name
                ])
                ->log('Menghapus data penerbit');
        } else {
            $response = [
                'status'  => 500,
                'message' => 'Gagal dihapus'
            ];
        }

        return response()->json($response);
    }

    public function streamFile($id, $type)
    {
        $data = Publisher::find($id);

        if ($type == 'birth_certificate') {
            $file = asset(Storage::disk($data->birth_certificate_location->location)->url($data->birth_certificate));
        } else if ($type == 'statement_letter') {
            $file = asset(Storage::disk($data->statement_letter_location->location)->url($data->statement_letter));
        }

        header('Content-type: application/octet-stream');
        header('Content-disposition: attachment;filename=' . Str::random(40) . '.pdf');

        readfile($file);
    }
}
