<?php

namespace App\Http\Controllers\CoachingSupervision;

use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CreateExecutorController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'category' => QueryAPI::get("select * from penerbit_kategori") ?? [],
                'type' => QueryAPI::get("select * from penerbit_jenis") ?? [],
                'content' => 'coaching-supervision.create-executor',
                'plugins' => [
                    'fileinput',
                    'select2',
                ]
            ]
        ]);
    }

    public function submitted(Request $request)
    {
        $response = [];

        if ($request->ajax()) {
            $validation = Validator::make($request->all(), [
                'name' => 'required',
                'location_id' => 'required',
                'email' => 'nullable|email',
                'email_alternative' => 'nullable|email',
                'postal_code' => 'nullable|digits:5|numeric',
                'phone' => 'nullable|min_digits:8|max_digits:13|numeric',
                'phone_alternative' => 'nullable|min_digits:8|max_digits:13|numeric',
                'fax' => 'nullable|min_digits:8|max_digits:13|numeric',
                'fax_alternative' => 'nullable|min_digits:8|max_digits:13|numeric',
                'file_deed' => 'required|file|mimes:pdf|max:512',
                'file_statement' => 'required|file|mimes:pdf|max:512',
            ], [
                'name.required' => 'Nama tidak boleh kosong',
                'location_id.required' => 'Lokasi tidak boleh kosong',
                'email.email' => 'Email tidak valid',
                'email_alternative.email' => 'Email alternatif tidak valid',
                'postal_code.digits' => 'Kode pos harus 5 digit',
                'postal_code.numeric' => 'Kode pos harus angka',
                'phone.min_digits' => 'Telepon minimal 8 digit',
                'phone.max_digits' => 'Telepon maksimal 13 digit',
                'phone.numeric' => 'Telepon harus angka',
                'phone_alternative.min_digits' => 'Telepon alternatif minimal 8 digit',
                'phone_alternative.max_digits' => 'Telepon alternatif maksimal 13 digit',
                'phone_alternative.numeric' => 'Telepon alternatif harus angka',
                'fax.min_digits' => 'Fax minimal 8 digit',
                'fax.max_digits' => 'Fax maksimal 13 digit',
                'fax.numeric' => 'Fax harus angka',
                'fax_alternative.min_digits' => 'Fax alternatif minimal 8 digit',
                'fax_alternative.max_digits' => 'Fax alternatif maksimal 13 digit',
                'fax_alternative.numeric' => 'Fax alternatif harus angka',
                'file_deed.required' => 'File akta tidak boleh kosong',
                'file_deed.file' => 'File akta tidak valid',
                'file_deed.file' => 'File akta harus pdf',
                'file_deed.file' => 'File akta maksimal 512 KB',
                'file_statement.required' => 'File pernyataan tidak boleh kosong',
                'file_statement.file' => 'File pernyataan tidak valid',
                'file_statement.file' => 'File pernyataan harus pdf',
                'file_statement.file' => 'File pernyataan maksimal 512 KB',
            ]);

            if ($validation->fails()) {
                $response = [
                    'code' => 400,
                    'error' => $validation->errors()->all(),
                ];
            } else {
                try {
                    $location = Main::locationById($request->location_id, 'village');

                    $createPublisher = QueryAPI::create('penerbit', [
                        'kategori_id' => $request->category_id,
                        'jenis_id' => $request->type_id,
                        'name' => $request->name,
                        'parent_id' => $request->parent_id,
                        'lembaga_penaung' => $request->shelter_institution,
                        'nama_gedung' => $request->building,
                        'kontak1' => $request->admin,
                        'kontak2' => $request->admin_alternative,
                        'province_id' => $location->PROPINSIID ?? null,
                        'provinsi' => $location->NAMAPROPINSI ?? null,
                        'city_id' => $location->KABUPATENID ?? null,
                        'city' => $location->NAMAKAB ?? null,
                        'district_id' => $location->KECAMATANID ?? null,
                        'village_id' => $location->ID ?? null,
                        'email1' => $request->email,
                        'email2' => $request->email_alternative,
                        'kodepos' => $request->postal_code,
                        'telp1' => $request->phone,
                        'telp2' => $request->phone_alternative,
                        'alamat' => $request->address,
                        'fax1' => $request->fax,
                        'fax2' => $request->fax_alternative,
                        'website' => $request->website,
                        'rata_terbitan' => $request->publication_average,
                        'createby' => session('username'),
                        'createdate' => date('Y-m-d H:i:s'),
                        'updateby' => session('username'),
                        'updatedate' => date('Y-m-d H:i:s'),
                        'validateby' => session('username'),
                        'validatedate' => date('Y-m-d H:i:s'),
                        'registerdate' => date('Y-m-d H:i:s'),
                        'source_db' => 'EDEPOSIT',
                        'is_validasi' => 1,
                        'is_disable' => 0,
                        'is_lock' => 1,
                        'is_anggota_ikapi' => 0,
                        'is_shareprefix' => 0,
                        'is_single' => 0,
                        'single_count' => 0,
                        'kuota_permohonan' => 0,
                        'status' => 3,
                        'tree_level' => $request->parent_id ? 2 : 1,
                    ], false);

                    if ($createPublisher) {
                        $fileDeed = $request->file('file_deed');
                        $fileStatement = $request->file('file_statement');

                        QueryAPI::uploadFile([
                            'type' => 'penerbit_akte_notaris',
                            'id' => $createPublisher->ID,
                            'iszip' => false,
                            'file' => $fileDeed,
                        ]);

                        QueryAPI::uploadFile([
                            'type' => 'penerbit_surat_pernyataan',
                            'id' => $createPublisher->ID,
                            'iszip' => false,
                            'file' => $fileStatement,
                        ]);
                    }

                    $response = [
                        'code' => 200,
                        'message' => 'Data telah ditambahkan'
                    ];
                } catch (\Exception $e) {
                    $response = [
                        'code' => $e->getCode(),
                        'message' => $e->getMessage()
                    ];
                }
            }
        }

        return response()->json($response);
    }
}
