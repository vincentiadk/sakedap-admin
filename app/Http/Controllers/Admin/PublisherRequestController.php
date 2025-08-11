<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Location;
use App\Models\Publisher;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Helper\GeneralHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PublisherRequestController extends Controller
{
    protected $location;

    public function __construct()
    {
        $this->location = Location::where('active', 1)->first();
    }
    public function create(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'province_id'       => 'required',
                'city_id'           => 'required',
                'district_id'       => 'required',
                'village_id'        => 'required',
                'organization_id'   => 'required',
                'photo'             => 'image|max:1024|mimes:jpg,jpeg,png',
                'contact'           => 'max:12',
                'fax'               => 'max:12',
                'name'              => 'required',
                'email'             => 'required|email',
                'phone'             => 'required|max:12',
                'address'           => 'required',
                'postal_code'       => 'required',
                'type'              => 'required',
                'birth_certificate' => 'required|file|max:50000|mimes:pdf',
                'statement_letter'  => 'required|file|max:50000|mimes:pdf',
                'username'          => 'required|unique:mysql.users',
                'password'          => 'required',
                'c_password'        => 'required|same:password'
            ], [
                'province_id.required'       => 'Harap memilih provinsi!',
                'city_id.required'           => 'Harap memilih kota!',
                'district_id.required'       => 'Harap memilih kecamatan!',
                'village_id.required'        => 'Harap memilih kelurahan!',
                'organization_id.required'   => 'Harap memilih organisasi!',
                'photo.image'                => 'Foto harus file gambar!',
                'photo.max'                  => 'Foto maksimal 1MB!',
                'photo.mimes'                => 'Foto harus berformat jpg/jpeg/png!',
                'contact.max'                => 'Kontak maksimal 12 karakter!',
                'fax.max'                    => 'Fax maksimal 12 karakter!',
                'name.required'              => 'Nama wajib di isi!',
                'email.required'             => 'Email wajib di isi!',
                'email.email'                => 'Email tidak valid!',
                'phone.required'             => 'Telepon wajib di isi!',
                'phone.max'                  => 'Telepon maksimal 12 karakter!',
                'address.required'           => 'Alamat wajib di isi!',
                'postal_code.required'       => 'Kode pos wajib di isi!',
                'type.required'              => 'Harap memilih jenis!',
                'birth_certificate.required' => 'Akta kelahiran konten wajib di isi!',
                'birth_certificate.file'     => 'Akta kelahiran harus berupa file!',
                'birth_certificate.max'      => 'Akta kelahiran maksimal 50MB!',
                'birth_certificate.mimes'    => 'Akta kelahiran harus bertipe pdf!',
                'statement_letter.required'  => 'Surat pernyataan konten wajib di isi!',
                'statement_letter.file'      => 'Surat pernyataan harus berupa file!',
                'statement_letter.max'       => 'Surat pernyataan maksimal 50MB!',
                'statement_letter.mimes'     => 'Surat pernyataan harus bertipe pdf!',
                'username.required'          => 'Username wajib di isi!',
                'username.unique'            => 'Username telah digunakan!',
                'password.required'          => 'Password wajib di isi!',
                'c_password.required'        => 'Konfirmasi password wajib di isi!',
                'c_password.same'            => 'Konfirmasi password tidak sama!'
            ]);

            if ($validator->fails()) {
                $response = [
                    'status' => 422,
                    'error'  => $validator->errors()
                ];
            } else {
                $file_name = "";
                $birth_certificate = "";
                $statement_letter = "";
                if ($request->hasFile('photo')) {
                    $file_name = Storage::disk($this->location->location)->put('public/publisher/photo', $request->file('photo'));
                }
                if ($request->hasFile('birth_certificate')) {
                    $birth_certificate = Storage::disk($this->location->location)->put('public/publisher/birth_certificate', $request->file('birth_certificate'));
                }
                if ($request->hasFile('statement_letter')) {
                    $statement_letter = Storage::disk($this->location->location)->put('public/publisher/statement_letter', $request->file('statement_letter'));
                }
                $create = Publisher::create([
                    'organization_id'   => $request->organization_id,
                    'province_id'       => $request->province_id,
                    'city_id'           => $request->city_id,
                    'district_id'       => $request->district_id,
                    'village_id'        => $request->village_id,
                    'photo'             => $request->hasFile('photo') ? $file_name : null,
                    'publisher_code'    => GeneralHelper::publisherCode(),
                    'contact'           => $request->contact,
                    'name'              => $request->name,
                    'fax'               => $request->fax,
                    'phone'             => $request->phone,
                    'website'           => $request->website,
                    'address'           => $request->address,
                    'postal_code'       => $request->postal_code,
                    'type'              => $request->type,
                    'code_system'       => $request->code_system,
                    'birth_certificate' => $birth_certificate,
                    'statement_letter'  => $statement_letter,
                    'status'            => 2,
                    'birth_certificate_location' => $this->location->id,
                    'bc_location'       => $this->location->id
                ]);

                if ($create) {
                    $user = User::create([
                        'userable_type' => 'publishers',
                        'userable_id'   => $create->id,
                        'role_id'       => null,
                        'username'      => $request->username,
                        'email'         => $request->email,
                        'password'      => Hash::make($request->password),
                        'lang'          => 'id',
                        'last_login'    => date('Y-m-d H:i:s'),
                        'enable'        => true
                    ]);

                    // Mail::send([], [], function ($message) use ($request, $user, $create) {
                    //     $header      = Setting::where('slug', 'template-email-header')->first();
                    //     $footer      = Setting::where('slug', 'template-email-footer')->first();
                    //     $link_header = public_path('storage/' . str_replace('public/', '', $header->content));
                    //     $link_footer = public_path('storage/' . str_replace('public/', '', $footer->content));
                    //     $publisher   = Publisher::find($create->id);

                    //     $data = [
                    //         'header'    => '<img src="' . $message->embed($link_header) . '" style="width:100%;">',
                    //         'footer'    => '<img src="' . $message->embed($link_footer) . '" style="width:100%;">',
                    //         'publisher' => $publisher->name,
                    //         'username'  => $request->username
                    //     ];

                    //     $template = Setting::where('slug', 'template-email-publisher-success-manual')->first();
                    //     $message->to($user->email, 'edeposit@perpusnas.go.id')
                    //         ->subject('Pendaftaran eDeposit Diterima')
                    //         ->from('edeposit@perpusnas.go.id', 'Info pendaftaran eDeposit')
                    //         ->setBody($template->parse($data), 'text/html');
                    // });

                    $publisher = Publisher::find($create->id);
                    activity('publishers')
                        ->performedOn(new Publisher())
                        ->causedBy(session('id'))
                        ->withProperties([
                            'organisasi'       => $publisher->organization->name,
                            'provinsi'         => $publisher->province->name,
                            'kota'             => $publisher->city->name,
                            'kecamatan'        => $publisher->district->name,
                            'kelurahan'        => $publisher->village->name,
                            'foto'             => asset(Storage::url($publisher->photo)),
                            'deposit'          => $publisher->code,
                            'kontak'           => $publisher->contact,
                            'nama'             => $publisher->name,
                            'fax'              => $publisher->fax,
                            'phone'            => $publisher->phone,
                            'website'          => $publisher->website,
                            'alamat'           => $publisher->address,
                            'kodepos'          => $publisher->postal_code,
                            'tipe'             => $publisher->type(),
                            'kode_sistem'      => $publisher->code_system,
                            'akta_kelahiran'   => asset(Storage::disk($publisher->birth_certificate_location->location)->url($publisher->birth_certificate)),
                            'surat_pernyataan' => asset(Storage::disk($publisher->statement_letter_location->location)->url($publisher->statement_letter)),
                            'status'           => $publisher->status(),
                            'username'         => $publisher->user->username,
                            'email'            => $publisher->user->email,

                        ])
                        ->log('Menambah data penerbit');

                    session()->flash('success', 'Berhasil ditambahkan!');
                    $response = ['status'  => 200];
                } else {
                    session()->flash('failed', 'Gagal ditambahkan!');
                    $response = [
                        'status'  => 500,
                        'message' => 'Gagal ditambahkan'
                    ];
                }
            }

            return response()->json($response);
        } else {
            $data = [
                'title'        => 'Tambah Penerbit',
                'organization' => Organization::all(),
                'content'      => 'admin.publisher.create'
            ];

            return view('admin.layout.index', ['data' => $data]);
        }
    }

    public function streamPdf($id, $type)
    {
        $data = Publisher::find($id);
        $location = Location::all();
        foreach ($location as $loc) {
            if ($type == 'birth_certificate' && $data->birth_certificate != '' && Storage::disk(Location::find($data->birth_certificate_location)->location)->exists($data->birth_certificate)) {
                $file = Storage::disk(Location::find($data->birth_certificate_location)->location)->path($data->birth_certificate);
            } else if ($type == 'statement_letter' && $data->statement_letter_location != '' && Storage::disk(Location::find($data->statement_letter_location)->location)->exists($data->statement_letter)) {
                $file = Storage::disk(Location::find($data->statement_letter_location)->location)->path($data->statement_letter);
            } else {
                return response()->json($type . ' ' . 'tidak ditemukan!'); //redirect()->back()->with(['not_found' => 'Data tidak ditemukan!']);
            }
            return response()->file($file);
        }
    }
}
