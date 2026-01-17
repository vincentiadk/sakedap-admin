<?php

namespace App\Http\Controllers\CoachingSupervision;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ExecutorListController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'category' => QueryAPI::get("select * from penerbit_kategori") ?? [],
                'type' => QueryAPI::get("select * from penerbit_jenis") ?? [],
                'content' => 'coaching-supervision.executor-list',
                'plugins' => [
                    'daterangepicker',
                    'datatable',
                    'select2',
                ]
            ]
        ]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'penerbit.id',
            null,
            null,
            'penerbit.id',
            'penerbit.name',
            'penerbit.email1',
            'penerbit_kategori.name',
            'penerbit_jenis.name',
            'penerbit.telp1',
            'penerbit.registerdate',
            'penerbit.createdate',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "penerbit.status = '3'";

        if (!Main::isSuperAdmin()) {
            $whereCondition[] = 'penerbit.province_id = ' . session('province_id');
        }

        if ($request->status) {
            if ($request->status == 1) {
                $whereCondition[] = '(penerbit.is_lock = 0 or penerbit.is_lock is null)';
            } else if ($request->status == 2) {
                $whereCondition[] = '(penerbit.is_lock = 1)';
            } else if ($request->status == 3) {
                $whereCondition[] = '(penerbit.is_lock = 2)';
            }
        }

        if ($search) {
            $terms = [];

            foreach ($column as $c) {
                if ($c) {
                    $terms[] = "upper($c) like '%$search%'";
                }
            }

            $whereCondition[] = '(' . implode(' or ', $terms) . ')';
        }

        if ($whereCondition) {
            $whereClause = "where " . implode(' and ', $whereCondition);
        }

        if ($order) {
            $orderColumnIndex = $order[0]['column'];
            $orderDir = $order[0]['dir'];
            $orderBy = "order by " . $column[$orderColumnIndex] . " $orderDir";
        }

        $totalData = QueryAPI::get("
            select
                count(*) as total
            from
                penerbit
            where
                status = '3'
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                penerbit
            left join
                penerbit_kategori on penerbit_kategori.id = penerbit.kategori_id
            left join
                penerbit_jenis on penerbit_jenis.id = penerbit.jenis_id
            $whereClause
        ", true)->TOTAL ?? 0;

        $queryData = QueryAPI::get("
            select
                *
            from (
                    select
                        rownum as rnum,
                        data.*
                    from
                        (
                            select
                                penerbit.*,
                                penerbit_kategori.name as name_penerbit_kategori,
                                penerbit_jenis.name as name_penerbit_jenis
                            from
                                penerbit
                            left join
                                penerbit_kategori on penerbit_kategori.id = penerbit.kategori_id
                            left join
                                penerbit_jenis on penerbit_jenis.id = penerbit.jenis_id
                            $whereClause
                            $orderBy
                        ) data
                    where
                        rownum <= $length
                )
            where
                rnum > $start
        ");

        if ($queryData) {
            foreach ($queryData as $val) {
                $action = '<div class="btn-group"><button type="button" class="btn btn-flat-primary w-100 btn-sm fw-semibold dropdown-toggle" data-bs-toggle="dropdown"><i class="ph-hand-pointing me-1"></i>Aksi</button><div class="dropdown-menu"><a href="javascript:void(0);" class="dropdown-item" onclick="showDataUpdate(' . $val->ID . ')"><i class="ph-pen me-1"></i>Edit Data</a>';

                if ($val->API_STATUS == 'PENDING') {
                    $action .= '<div class="dropdown-divider"></div><a href="javascript:void(0);" class="dropdown-item text-success" onclick="approveAPIAccess(' . $val->ID . ')"><i class="ph-check-circle me-1"></i>Setujui Akses API</a><a href="javascript:void(0);" class="dropdown-item text-danger" onclick="rejectAPIAccess(' . $val->ID . ')"><i class="ph-x-circle me-1"></i>Tolak Akses API</a>';
                } else if ($val->API_STATUS == 'APPROVED') {
                    $action .= '<div class="dropdown-divider"></div><a href="javascript:void(0);" class="dropdown-item text-warning" onclick="revokeAPIAccess(' . $val->ID . ')"><i class="ph-prohibit me-1"></i>Cabut Akses API</a>';
                }

                $action .= '<div class="dropdown-divider"></div><a href="javascript:void(0);" class="dropdown-item" onclick="sendEmailResetPassword(' . $val->ID . ')"><i class="ph-envelope-open me-1"></i>Kirim Reset Password</a><a href="javascript:void(0);" class="dropdown-item" onclick="destroyData(' . $val->ID . ')"><i class="ph-trash-simple me-1"></i>Hapus Data</a></div></div>';

                $email = '
                    <div>Utama : ' . $val->EMAIL1 . '</div>
                    <div>Alternatif : ' . $val->EMAIL2 . '</div>
                ';

                $phone = '
                    <div>Utama : ' . $val->TELP1 . '</div>
                    <div>Alternatif : ' . $val->TELP2 . '</div>
                ';

                $lock = '';

                if (is_null($val->IS_LOCK) || $val->IS_LOCK == 0) {
                    $lock = 'Aktif';
                } else if ($val->IS_LOCK == 1) {
                    $lock = 'Blokir';
                } else if ($val->IS_LOCK == 2) {
                    $lock = 'Usulan Blokir';
                }

                $dataWarning = QueryAPI::get("select * from e_publisher_warnings where publisher_id = $val->ID and status = 'DALAM TEGURAN'") ?? [];

                if (count($dataWarning) > 0) {
                    $warning = 'Dalam Teguran';
                } else {
                    $warning = 'Tidak Ada';
                }

                $mark = '
                    <div>Status : ' . $lock . '</div>
                    <div>Teguran : ' . $warning . '</div>
                    <div>Status API : ' . ucwords(strtolower(($val->API_STATUS ?: 'Belum Mengajukan'))) . '</div>
                ';

                $data[] = [
                    $start + 1,
                    $action,
                    $mark,
                    $val->ID,
                    $val->NAME,
                    $email,
                    $val->NAME_PENERBIT_KATEGORI,
                    $val->NAME_PENERBIT_JENIS,
                    $phone,
                    Carbon::parse($val->REGISTERDATE)->isoFormat('dddd, D MMMM Y'),
                    Carbon::parse($val->CREATEDATE)->isoFormat('dddd, D MMMM Y'),
                ];

                $start++;
            }
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    }

    public function showData(Request $request)
    {
        $id = $request->id;
        $data = QueryAPI::get("
            select
                penerbit.*,
                penerbit_kategori.name as name_penerbit_kategori,
                penerbit_jenis.name as name_penerbit_jenis,
                parent.name as name_parent,
                propinsi.namapropinsi as namapropinsi,
                kabupaten.namakab as namakab,
                kecamatan.namakec as namakec,
                kelurahan.namakel as namakel
            from
                penerbit
            left join
                penerbit_kategori on penerbit_kategori.id = penerbit.kategori_id
            left join
                penerbit_jenis on penerbit_jenis.id = penerbit.jenis_id
            left join
                penerbit parent on parent.id = penerbit.parent_id
            left join
                propinsi on propinsi.id = penerbit.province_id
            left join
                kabupaten on kabupaten.id = penerbit.city_id
            left join
                kecamatan on kecamatan.id = penerbit.district_id
            left join
                kelurahan on kelurahan.id = penerbit.village_id
            where
                penerbit.id = $id
        ", true);

        return response()->json($data);
    }

    public function updateData(Request $request)
    {
        $id = $request->table_id;
        $validation = Validator::make($request->all(), [
            'location_id' => 'required',
            'email' => 'nullable|email',
            'email_alternative' => 'nullable|email',
            'postal_code' => 'nullable|digits:5|numeric',
            'phone' => 'nullable|min_digits:8|max_digits:13|numeric',
            'phone_alternative' => 'nullable|min_digits:8|max_digits:13|numeric',
            'fax' => 'nullable|min_digits:8|max_digits:13|numeric',
            'fax_alternative' => 'nullable|min_digits:8|max_digits:13|numeric',
        ], [
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
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                $location = Main::locationById($request->location_id, 'village');

                QueryAPI::update('penerbit', $id, [
                    'kategori_id' => $request->category_id,
                    'jenis_id' => $request->type_id,
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
                    'fax1' => $request->fax,
                    'fax2' => $request->fax_alternative,
                    'website' => $request->website,
                    'rata_terbitan' => $request->publication_average,
                    'updateby' => session('username'),
                    'updatedate' => date('Y-m-d H:i:s'),
                    'updateterminal' => $request->ip(),
                    'jwt' => $request->jwt ?? null,
                    'x_api_key' => $request->x_api_key ?? null,
                    'jwt_expired' => $request->jwt_expired ?? null,
                ], false);

                $response = [
                    'code' => 200,
                    'message' => 'Data telah diubah'
                ];
            } catch (\Exception $e) {
                $response = [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage()
                ];
            }
        }

        return response()->json($response);
    }

    public function approveAPIAccess(Request $request)
    {
        $id = $request->id;

        try {
            $apiKey = Str::random(64);

            QueryAPI::update('penerbit', $id, [
                'x_api_key' => $apiKey,
                'api_status' => 'APPROVED',
                'is_api_enable' => 1,
                'updateby' => session('username'),
                'updatedate' => date('Y-m-d H:i:s'),
                'updateterminal' => $request->ip(),
            ], false);

            $data = QueryAPI::get("select * from penerbit where id = $id", true);

            if ($data && $data->EMAIL1) {
                try {
                    $payloadEmail = [
                        'name' => $data->NAME,
                        'email' => $data->EMAIL1,
                        'api_key' => $apiKey,
                        'message' => 'Permintaan akses API Anda telah disetujui. Berikut adalah API Key Anda: ' . $apiKey
                    ];

                    Mail::send([], [], function ($message) use ($payloadEmail) {
                        $message->to($payloadEmail['email'], $payloadEmail['name'])
                            ->subject('Akses API SAKEDAP Disetujui')
                            ->from(config('mail.from.address'), config('mail.from.name'))
                            ->html('<p>Yth. ' . $payloadEmail['name'] . ',</p><p>' . $payloadEmail['message'] . '</p>', 'text/html');
                    });
                } catch (\Exception $e) {
                    return response()->json([
                        'code' => $e->getCode() ?? 500,
                        'message' => $e->getMessage()
                    ]);
                }
            }

            return response()->json([
                'code' => 200,
                'message' => 'Akses API telah disetujui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function rejectAPIAccess(Request $request)
    {
        $id = $request->id;
        $reason = $request->reason ?? 'Tidak memenuhi persyaratan';

        try {
            QueryAPI::update('penerbit', $id, [
                'api_status' => 'REJECTED',
                'api_reject_reason' => $reason,
                'updateby' => session('username'),
                'updatedate' => date('Y-m-d H:i:s'),
                'updateterminal' => $request->ip(),
            ], false);

            $data = QueryAPI::get("select * from penerbit where id = $id", true);

            if ($data && $data->EMAIL1) {
                try {
                    $payloadEmail = [
                        'name' => $data->NAME,
                        'email' => $data->EMAIL1,
                        'reason' => $reason,
                        'message' => 'Permintaan akses API Anda ditolak. Alasan: ' . $reason
                    ];

                    Mail::send([], [], function ($message) use ($payloadEmail) {
                        $message->to($payloadEmail['email'], $payloadEmail['name'])
                            ->subject('Akses API SAKEDAP Ditolak')
                            ->from(config('mail.from.address'), config('mail.from.name'))
                            ->html('<p>Yth. ' . $payloadEmail['name'] . ',</p><p>' . $payloadEmail['message'] . '</p>', 'text/html');
                    });
                } catch (\Exception $e) {
                    return response()->json([
                        'code' => $e->getCode() ?? 500,
                        'message' => $e->getMessage()
                    ]);
                }
            }

            return response()->json([
                'code' => 200,
                'message' => 'Akses API telah ditolak'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function revokeAPIAccess(Request $request)
    {
        $id = $request->id;

        try {
            QueryAPI::update('penerbit', $id, [
                'x_api_key' => null,
                'jwt' => null,
                'api_status' => 'REVOKED',
                'is_api_enable' => 0,
                'updateby' => session('username'),
                'updatedate' => date('Y-m-d H:i:s'),
                'updateterminal' => $request->ip(),
            ], false);

            return response()->json([
                'code' => 200,
                'message' => 'Akses API telah dicabut'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function sendEmailResetPassword(Request $request)
    {
        $id = $request->id;
        $data = QueryAPI::get("select * from penerbit where id = $id", true);

        if ($data) {
            $email = $data->EMAIL1 ?: null;

            if ($email) {
                try {
                    $createRequest = QueryAPI::create('e_password_resets', [
                        'email' => $email,
                        'token' => Str::random(40),
                        'created_at' => date('Y-m-d H:i:s'),
                        'expired_at' => date('Y-m-d H:i:s', strtotime('+' . config('system.limit_reset_password') . ' hours')),
                    ], false);

                    if ($createRequest) {
                        try {
                            $tokenUrl = url('reset-password-action?token=' . $createRequest->TOKEN . '&email=' . urlencode($email));
                            $templateEmail = QueryAPI::get("select * from e_settings where slug = 'ResetPassword'", true);

                            $payloadEmail = [
                                'name' => $data->NAME,
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

                            $response = [
                                'code' => 200,
                                'message' => 'Reset password telah terkirim'
                            ];
                        } catch (\Exception $e) {
                            $response = [
                                'code' => $e->getCode(),
                                'message' => $e->getMessage()
                            ];
                        }
                    } else {
                        $response = [
                            'code' => 500,
                            'message' => 'Gagal membuat permintaan'
                        ];
                    }
                } catch (\Exception $e) {
                    $response = [
                        'code' => $e->getCode(),
                        'message' => $e->getMessage()
                    ];
                }
            } else {
                $response = [
                    'code' => 404,
                    'message' => 'Pelaksana serah tidak memiliki email'
                ];
            }
        } else {
            $response = [
                'code' => 404,
                'message' => 'Data tidak ditemukan'
            ];
        }

        return response()->json($response);
    }

    public function destroyData(Request $request)
    {
        $id = $request->id;

        try {
            QueryAPI::delete('penerbit', $id);

            $response = [
                'code' => 200,
                'message' => 'Data telah dihapus'
            ];
        } catch (\Exception $e) {
            $response = [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }

        return response()->json($response);
    }
}
