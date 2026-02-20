<?php

namespace App\Http\Controllers\CoachingSupervision;

use App\Helpers\Barantum;
use App\Helpers\ISBN;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class WarningController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'officer' => QueryAPI::get("select * from petugas_pembina") ?? [],
                'content' => 'coaching-supervision.warning',
                'plugins' => [
                    'datatable',
                    'select2',
                    'daterangepicker',
                ]
            ]
        ]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'e_publisher_warnings.id',
            null,
            'penerbit.id',
            'penerbit.name',
            'branchs.name',
            'e_publisher_warnings.tagihan_koleksi',
            'e_publisher_warnings.status',
            'e_publisher_warnings.warning_date',
            'e_publisher_warnings.warning_date_2',
            'e_publisher_warnings.warning_date_3',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 10);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = 'e_publisher_warnings.deleted_at is null';

        if (!Main::isSuperAdmin() && !Main::isPerpusnas()) {
            $whereCondition[] = 'penerbit.province_id = ' . session('province_id');
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

        if ($request->executor_id) {
            $whereCondition[] = "e_publisher_warnings.publisher_id = $request->executor_id";
        }

        if ($request->branch_id) {
            $whereCondition[] = "e_publisher_warnings.branch_id = $request->branch_id";
        }

        if ($request->date) {
            $date = Carbon::parse($request->date)->format('Y-m-d');
            $whereCondition[] = "e_publisher_warnings.warning_date = to_date('$date', 'YYYY-MM-DD')";
        }

        if ($whereCondition) {
            $whereClause = "where " . implode(' and ', $whereCondition);
        }

        if ($order) {
            $orderColumnIndex = $order[0]['column'];
            $orderDir = $order[0]['dir'];
            $orderBy = "order by nvl(" . $column[$orderColumnIndex] . ", 0) $orderDir";
        }

        $totalData = QueryAPI::get("
            select
                count(*) as total
            from
                e_publisher_warnings
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                e_publisher_warnings
            left join
                penerbit on penerbit.id = e_publisher_warnings.publisher_id
            left join
                branchs on branchs.id = e_publisher_warnings.branch_id
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
                                e_publisher_warnings.*,
                                penerbit.id as id_penerbit,
                                penerbit.name as name_penerbit,
                                penerbit.is_lock as is_lock_penerbit,
                                branchs.name as name_branch
                            from
                                e_publisher_warnings
                            left join
                                penerbit on penerbit.id = e_publisher_warnings.publisher_id
                            left join
                                branchs on branchs.id = e_publisher_warnings.branch_id
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
                if ($val->IS_LOCK_PENERBIT == 2) {
                    $btnLock = '
                        <a href="javascript:void(0);" class="dropdown-item" onclick="lockable(' . $val->ID_PENERBIT . ', 0)">
                            <i class="ph-lock-simple-open me-1"></i>
                            Buka Blokir
                        </a>
                    ';
                } else {
                    $btnLock = '
                        <a href="javascript:void(0);" class="dropdown-item" onclick="lockable(' . $val->ID_PENERBIT . ', 2)">
                            <i class="ph-lock-simple me-1"></i>
                            Usulan Blokir
                        </a>
                    ';
                }

                $action = '
                    <div class="btn-group">
                        <button type="button" class="btn btn-flat-primary w-100 btn-sm fw-semibold dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="ph-hand-pointing me-1"></i>
                            Aksi
                        </button>
                        <div class="dropdown-menu">
                            <a href="javascript:void(0);" class="dropdown-item" onclick="showDataUpdate(' . $val->ID . ')">
                                <i class="ph-pen me-1"></i>
                                Ubah Data
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item" onclick="destroyData(' . $val->ID . ')">
                                <i class="ph-trash-simple me-1"></i>
                                Hapus Data
                            </a>
                            ' . $btnLock . '
                        </div>
                    </div>
                ';

                $warningDate1HTML = '';
                $warningDate2HTML = '';
                $warningDate3HTML = '';

                $warningDate1 = $val->WARNING_DATE;
                $warningDate2 = $val->WARNING_DATE_2;
                $warningDate3 = $val->WARNING_DATE_3;

                if ($warningDate1) {
                    $dateStart = Carbon::parse($warningDate1)->format('Y-m-d');
                    $dateLimitEnd = Carbon::parse($warningDate2 ?? now())->format('Y-m-d');
                    $dateLimit = Carbon::parse($dateStart)->diffInDays($dateLimitEnd);
                    $dateEnd = Carbon::parse($warningDate1)->addDays(40)->format('Y-m-d');
                    $dateNext = Carbon::parse($warningDate1)->addDays(40)->isoFormat('D MMM Y');

                    if ($dateLimit >= 40) {
                        $mark = '<span class="text-danger">' . $dateLimit . ' Hari</span>';
                    } else {
                        $mark = '<span class="text-success">' . $dateLimit . ' Hari</span>';
                    }

                    $totalCollection = ISBN::get('search', [
                        'received_date_kckr_from' => $dateStart,
                        'received_date_kckr_to' => $dateEnd,
                    ])->recordsFiltered ?? 0;

                    $file = '
                        <a href="javascript:void(0);" class="text-warning">
                            Tidak Ada
                        </a>
                    ';

                    if ($val->LINK_FILE) {
                        $file = '
                            <a href="' . url('stream-file') . '?type=publisher_warning&id=' . $val->ID . '&filename=' . $val->LINK_FILE . '" class="text-primary" target="_blank">
                                Lihat
                            </a>
                        ';
                    }

                    $warningDate1HTML = '
                        <div class="fw-bold"><small>' . $mark . '</small></div>
                        <div><small class="text-muted">Tgl : ' . Carbon::parse($warningDate1)->isoFormat('D MMM Y') . '</small></div>
                        <div><small class="text-muted">Diterima : ' . $totalCollection . '</small></div>
                        <div><small class="text-muted">File : ' . $file . '</small></div>
                        <div><small class="text-muted">Berikutnya : ' . $dateNext . '</small></div>
                        <div>
                            <small class="text-muted">
                                Kirim Pesan :
                                <a href="javascript:void(0);" class="text-danger" onclick="sendEmail(' . $val->ID . ', 1)">
                                    <i class="ph-envelope-open"></i>
                                </a>
                                <a href="javascript:void(0);" class="text-teal" onclick="sendWhatsapp(' . $val->ID . ', 1)">
                                    <i class="ph-whatsapp-logo"></i>
                                </a>
                            </small>
                        </div>
                    ';
                }

                if ($warningDate2) {
                    $dateStart = Carbon::parse($warningDate2)->format('Y-m-d');
                    $dateLimitEnd = Carbon::parse($warningDate3 ?? now())->format('Y-m-d');
                    $dateLimit = Carbon::parse($dateStart)->diffInDays($dateLimitEnd);
                    $dateEnd = Carbon::parse($warningDate2)->addDays(40)->format('Y-m-d');
                    $dateNext = Carbon::parse($warningDate2)->addDays(40)->isoFormat('D MMM Y');

                    if ($dateLimit >= 40) {
                        $mark = '<span class="text-danger">' . $dateLimit . ' Hari</span>';
                    } else {
                        $mark = '<span class="text-success">' . $dateLimit . ' Hari</span>';
                    }

                    $totalCollection = ISBN::get('search', [
                        'received_date_kckr_from' => $dateStart,
                        'received_date_kckr_to' => $dateEnd,
                    ])->recordsFiltered ?? 0;

                    $file = '
                        <a href="javascript:void(0);" class="text-warning">
                            Tidak Ada
                        </a>
                    ';

                    if ($val->LINK_FILE_2) {
                        $file = '
                            <a href="' . url('stream-file') . '?type=publisher_warning_2&id=' . $val->ID . '&filename=' . $val->LINK_FILE_2 . '" class="text-primary" target="_blank">
                                Lihat
                            </a>
                        ';
                    }

                    $warningDate2HTML = '
                        <div class="fw-bold"><small>' . $mark . '</small></div>
                        <div><small class="text-muted">Tgl : ' . Carbon::parse($warningDate2)->isoFormat('D MMM Y') . '</small></div>
                        <div><small class="text-muted">Diterima : ' . $totalCollection . '</small></div>
                        <div><small class="text-muted">File : ' . $file . '</small></div>
                        <div><small class="text-muted">Berikutnya : ' . $dateNext . '</small></div>
                        <div>
                            <small class="text-muted">
                                Kirim Pesan :
                                <a href="javascript:void(0);" class="text-danger" onclick="sendEmail(' . $val->ID . ', 2)">
                                    <i class="ph-envelope-open"></i>
                                </a>
                                <a href="javascript:void(0);" class="text-teal" onclick="sendWhatsapp(' . $val->ID . ', 2)">
                                    <i class="ph-whatsapp-logo"></i>
                                </a>
                            </small>
                        </div>
                    ';
                }

                if ($warningDate3) {
                    $dateStart = Carbon::parse($warningDate3)->format('Y-m-d');
                    $dateLimit = Carbon::parse($dateStart)->diffInDays(now()->format('Y-m-d'));
                    $dateEnd = Carbon::parse($warningDate3)->addDays(40)->format('Y-m-d');

                    if ($dateLimit >= 40) {
                        $mark = '<span class="text-danger">' . $dateLimit . ' Hari</span>';
                    } else {
                        $mark = '<span class="text-success">' . $dateLimit . ' Hari</span>';
                    }

                    $totalCollection = ISBN::get('search', [
                        'received_date_kckr_from' => $dateStart,
                        'received_date_kckr_to' => $dateEnd,
                    ])->recordsFiltered ?? 0;

                    $file = '
                        <a href="javascript:void(0);" class="text-warning">
                            Tidak Ada
                        </a>
                    ';

                    if ($val->LINK_FILE_3) {
                        $file = '
                            <a href="' . url('stream-file') . '?type=publisher_warning_3&id=' . $val->ID . '&filename=' . $val->LINK_FILE_3 . '" class="text-primary" target="_blank">
                                Lihat
                            </a>
                        ';
                    }

                    $warningDate3HTML = '
                        <div class="fw-bold"><small>' . $mark . '</small></div>
                        <div><small class="text-muted">Tgl : ' . Carbon::parse($warningDate3)->isoFormat('D MMM Y') . '</small></div>
                        <div><small class="text-muted">Diterima : ' . $totalCollection . '</small></div>
                        <div><small class="text-muted">File : ' . $file . '</small></div>
                        <div>
                            <small class="text-muted">
                                Kirim Pesan :
                                <a href="javascript:void(0);" class="text-danger" onclick="sendEmail(' . $val->ID . ', 3)">
                                    <i class="ph-envelope-open"></i>
                                </a>
                                <a href="javascript:void(0);" class="text-teal" onclick="sendWhatsapp(' . $val->ID . ', 3)">
                                    <i class="ph-whatsapp-logo"></i>
                                </a>
                            </small>
                        </div>
                    ';
                }

                $data[] = [
                    $start + 1,
                    $action,
                    $val->ID_PENERBIT,
                    $val->NAME_PENERBIT,
                    $val->NAME_BRANCH,
                    $warningDate1HTML,
                    $warningDate2HTML,
                    $warningDate3HTML,
                    $val->TAGIHAN_KOLEKSI ?? 0,
                    $val->STATUS,
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

    public function createData(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'executor_id' => 'required',
            'branch_id' => 'required',
            'warning_date' => 'required',
            'file' => 'required|mimes:pdf|max:5120',
            'file_2' => 'nullable|mimes:pdf|max:5120',
            'file_3' => 'nullable|mimes:pdf|max:5120',
        ], [
            'executor_id.required' => 'Pelaksana serah tidak boleh kosong',
            'branch_id.required' => 'Dari tidak boleh kosong',
            'warning_date.required' => 'Tanggal teguran tidak boleh kosong',
            'file.required' => 'File 1 tidak boleh kosong',
            'file.mimes' => 'File 1 hanya boleh pdf',
            'file.max' => 'File 2 maksimal 5MB',
            'file_2.mimes' => 'File 2 hanya boleh pdf',
            'file_2.max' => 'File 2 maksimal 5MB',
            'file_3.mimes' => 'File 3 hanya boleh pdf',
            'file_3.max' => 'File 2 maksimal 5MB',
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                $createData = QueryAPI::create('e_publisher_warnings', [
                    'publisher_id' => $request->executor_id,
                    'branch_id' => $request->branch_id,
                    'warning_date' => $request->warning_date,
                    'warning_date_2' => $request->warning_date_2,
                    'warning_date_3' => $request->warning_date_3,
                    'tagihan_koleksi' => $request->bill_collection,
                    'status' => $request->status,
                    'createby' => session('username'),
                    'updateby' => session('username'),
                ]);

                if ($createData) {
                    $uploadFile = QueryAPI::uploadFile([
                        'type' => 'publisher_warning',
                        'id' => $createData->ID,
                        'iszip' => 0,
                        'file' => $request->file('file'),
                    ]);

                    if ($uploadFile) {
                        QueryAPI::update('e_publisher_warnings', $createData->ID, [
                            'link_file' => $uploadFile->FileName
                        ], false);
                    }

                    if ($request->hasFile('file_2')) {
                        $uploadFile = QueryAPI::uploadFile([
                            'type' => 'publisher_warning_2',
                            'id' => $createData->ID,
                            'iszip' => 0,
                            'file' => $request->file('file_2'),
                        ]);

                        if ($uploadFile) {
                            QueryAPI::update('e_publisher_warnings', $createData->ID, [
                                'link_file_2' => $uploadFile->FileName
                            ], false);
                        }
                    }

                    if ($request->hasFile('file_3')) {
                        $uploadFile = QueryAPI::uploadFile([
                            'type' => 'publisher_warning_3',
                            'id' => $createData->ID,
                            'iszip' => 0,
                            'file' => $request->file('file_3'),
                        ]);

                        if ($uploadFile) {
                            QueryAPI::update('e_publisher_warnings', $createData->ID, [
                                'link_file_3' => $uploadFile->FileName
                            ], false);
                        }
                    }
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

        return response()->json($response);
    }

    public function showData(Request $request)
    {
        $id = $request->id;
        $data = QueryAPI::get("
            select
                e_publisher_warnings.*,
                penerbit.name as name_penerbit,
                branchs.name as name_branch
            from
                e_publisher_warnings
            left join
                penerbit on penerbit.id = e_publisher_warnings.publisher_id
            left join
                branchs on branchs.id = e_publisher_warnings.branch_id
            where
                e_publisher_warnings.id = $id
        ", true);

        return response()->json($data);
    }

    public function updateData(Request $request)
    {
        $id = $request->table_id;
        $query = QueryAPI::get("select * from e_publisher_warnings where id = $id", true);

        $validation = Validator::make($request->all(), [
            'executor_id' => 'required',
            'branch_id' => 'required',
            'warning_date' => 'required',
            'file' => 'nullable|mimes:pdf|max:5120',
            'file_2' => 'nullable|mimes:pdf|max:5120',
            'file_3' => 'nullable|mimes:pdf|max:5120',
        ], [
            'executor_id.required' => 'Pelaksana serah tidak boleh kosong',
            'branch_id.required' => 'Dari tidak boleh kosong',
            'warning_date.required' => 'Tanggal teguran tidak boleh kosong',
            'file.mimes' => 'File 1 hanya boleh pdf',
            'file.max' => 'File 2 maksimal 5MB',
            'file_2.mimes' => 'File 2 hanya boleh pdf',
            'file_2.max' => 'File 2 maksimal 5MB',
            'file_3.mimes' => 'File 3 hanya boleh pdf',
            'file_3.max' => 'File 2 maksimal 5MB',
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                $updateData = QueryAPI::update('e_publisher_warnings', $id, [
                    'publisher_id' => $request->executor_id,
                    'branch_id' => $request->branch_id,
                    'warning_date' => $request->warning_date,
                    'warning_date_2' => $request->warning_date_2,
                    'warning_date_3' => $request->warning_date_3,
                    'tagihan_koleksi' => (int) $request->bill_collection ?? 0,
                    'status' => $request->status,
                    'updateby' => session('username'),
                ]);

                if ($updateData) {
                    if ($request->hasFile('file')) {
                        QueryAPI::removeFile([
                            'type' => 'publisher_warning',
                            'id' => $id,
                            'filename' => $query->LINK_FILE ?? ''
                        ]);

                        $uploadFile = QueryAPI::uploadFile([
                            'type' => 'publisher_warning',
                            'id' => $id,
                            'iszip' => 0,
                            'file' => $request->file('file'),
                        ]);

                        if ($uploadFile) {
                            QueryAPI::update('e_publisher_warnings', $id, [
                                'link_file' => $uploadFile->FileName
                            ], false);
                        }
                    }

                    if ($request->hasFile('file_2')) {
                        QueryAPI::removeFile([
                            'type' => 'publisher_warning_2',
                            'id' => $id,
                            'filename' => $query->LINK_FILE_2 ?? ''
                        ]);

                        $uploadFile = QueryAPI::uploadFile([
                            'type' => 'publisher_warning_2',
                            'id' => $id,
                            'iszip' => 0,
                            'file' => $request->file('file_2'),
                        ]);

                        if ($uploadFile) {
                            QueryAPI::update('e_publisher_warnings', $id, [
                                'link_file_2' => $uploadFile->FileName
                            ], false);
                        }
                    }

                    if ($request->hasFile('file_3')) {
                        QueryAPI::removeFile([
                            'type' => 'publisher_warning_3',
                            'id' => $id,
                            'filename' => $query->LINK_FILE_3 ?? ''
                        ]);

                        $uploadFile = QueryAPI::uploadFile([
                            'type' => 'publisher_warning_3',
                            'id' => $id,
                            'iszip' => 0,
                            'file' => $request->file('file_3'),
                        ]);

                        if ($uploadFile) {
                            QueryAPI::update('e_publisher_warnings', $id, [
                                'link_file_3' => $uploadFile->FileName
                            ], false);
                        }
                    }
                }

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

    public function lockable(Request $request)
    {
        QueryAPI::update('penerbit', $request->id, [
            'is_lock' => $request->is_lock
        ], false);

        $response = [
            'code' => 200,
            'message' => 'Pemblokiran pelaksana serah telah disesuaikan'
        ];

        return response()->json($response);
    }

    public function destroyData(Request $request)
    {
        $id = $request->id;

        try {
            QueryAPI::update('e_publisher_warnings', $id, [
                'deleted_at' => date('Y-m-d H:i:s')
            ]);

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

    public function sendEmail(Request $request)
    {
        $id = $request->id;
        $warningTarget = $request->target;
        $warning = QueryAPI::get("
            select
                e_publisher_warnings.*,
                penerbit.name as name_penerbit,
                penerbit.email1 as email_penerbit
            from
                e_publisher_warnings
            left join
                penerbit on penerbit.id = e_publisher_warnings.publisher_id
            where
                e_publisher_warnings.id = $id
        ", true);

        if ($warning) {
            $email = $warning->EMAIL_PENERBIT;
            $totalCollection = 0;
            $linkFile = null;
            $fileType = '';

            if ($email) {
                if ($warningTarget == 3) {
                    $linkFile = $warning->LINK_FILE_3;
                    $fileType = 'publisher_warning_3';
                    $dateStart = Carbon::parse($warning->WARNING_DATE_3)->format('Y-m-d');
                    $dateEnd = Carbon::parse($warning->WARNING_DATE_3)->addDays(40)->format('Y-m-d');

                    $totalCollection = ISBN::get('search', [
                        'received_date_kckr_from' => $dateStart,
                        'received_date_kckr_to' => $dateEnd,
                    ])->recordsFiltered ?? 0;
                } else if ($warningTarget == 2) {
                    $linkFile = $warning->LINK_FILE_2;
                    $fileType = 'publisher_warning_2';
                    $dateStart = Carbon::parse($warning->WARNING_DATE_2)->format('Y-m-d');
                    $dateEnd = Carbon::parse($warning->WARNING_DATE_2)->addDays(40)->format('Y-m-d');

                    $totalCollection = ISBN::get('search', [
                        'received_date_kckr_from' => $dateStart,
                        'received_date_kckr_to' => $dateEnd,
                    ])->recordsFiltered ?? 0;
                } else if ($warningTarget == 1) {
                    $linkFile = $warning->LINK_FILE;
                    $fileType = 'publisher_warning';
                    $dateStart = Carbon::parse($warning->WARNING_DATE_2)->format('Y-m-d');
                    $dateEnd = Carbon::parse($warning->WARNING_DATE_2)->addDays(40)->format('Y-m-d');

                    $totalCollection = ISBN::get('search', [
                        'received_date_kckr_from' => $dateStart,
                        'received_date_kckr_to' => $dateEnd,
                    ])->recordsFiltered ?? 0;
                }

                $arrears = $warning->TAGIHAN_KOLEKSI - $totalCollection;
                $dataSend = $this->buildSendData('email', $warning, $warningTarget, $totalCollection, $arrears);

                try {
                    Mail::send([], [], function ($message) use ($warning, $dataSend, $linkFile, $fileType) {
                        $message->to($warning->EMAIL_PENERBIT ?? '', $warning->NAME_PENERBIT)
                            ->subject('Surat Teguran')
                            ->from(config('mail.from.address'), config('mail.from.name'))
                            ->html($dataSend, 'text/html');

                        if ($linkFile) {
                            $file = url('stream-file?type=' . $fileType . '&id=' . ($warning->ID ?? '') . '&filename=' . $linkFile);
                            $response = Http::get($file);

                            if ($response->successful()) {
                                $fileContent = $response->body();

                                $message->attachData($fileContent, 'Surat Teguran.pdf');
                            }
                        }
                    });

                    $response = [
                        'code' => 200,
                        'message' => 'Berhasil dikirim ke email pelaksana serah'
                    ];
                } catch (\Exception $e) {
                    $response = [
                        'code' => 500,
                        'message' => $e->getMessage()
                    ];
                }
            } else {
                $response = [
                    'code' => 500,
                    'message' => 'Email pelaksana serah kosong'
                ];
            }
        } else {
            $response = [
                'code' => 500,
                'message' => 'Data teguran tidak ditemukan'
            ];
        }

        return response()->json($response);
    }

    public function sendWhatsapp(Request $request)
    {
        $id = $request->id;
        $warningTarget = 1;
        $warning = QueryAPI::get("
            select
                e_publisher_warnings.*,
                penerbit.name as name_penerbit,
                penerbit.kontak1 as kontak_penerbit
            from
                e_publisher_warnings
            left join
                penerbit on penerbit.id = e_publisher_warnings.publisher_id
            where
                e_publisher_warnings.id = $id
        ", true);

        if ($warning) {
            $noTelp = $warning->KONTAK_PENERBIT;
            $totalCollection = 0;
            $linkFile = null;
            $fileType = '';

            if ($noTelp) {
                if ($warningTarget == 3) {
                    $linkFile = $warning->LINK_FILE_3;
                    $fileType = 'publisher_warning_3';
                    $dateStart = Carbon::parse($warning->WARNING_DATE_3)->format('Y-m-d');
                    $dateEnd = Carbon::parse($warning->WARNING_DATE_3)->addDays(40)->format('Y-m-d');

                    $totalCollection = ISBN::get('search', [
                        'received_date_kckr_from' => $dateStart,
                        'received_date_kckr_to' => $dateEnd,
                    ])->recordsFiltered ?? 0;
                } else if ($warningTarget == 2) {
                    $linkFile = $warning->LINK_FILE_2;
                    $fileType = 'publisher_warning_2';
                    $dateStart = Carbon::parse($warning->WARNING_DATE_2)->format('Y-m-d');
                    $dateEnd = Carbon::parse($warning->WARNING_DATE_2)->addDays(40)->format('Y-m-d');

                    $totalCollection = ISBN::get('search', [
                        'received_date_kckr_from' => $dateStart,
                        'received_date_kckr_to' => $dateEnd,
                    ])->recordsFiltered ?? 0;
                } else if ($warningTarget == 1) {
                    $linkFile = $warning->LINK_FILE;
                    $fileType = 'publisher_warning';
                    $dateStart = Carbon::parse($warning->WARNING_DATE_2)->format('Y-m-d');
                    $dateEnd = Carbon::parse($warning->WARNING_DATE_2)->addDays(40)->format('Y-m-d');

                    $totalCollection = ISBN::get('search', [
                        'received_date_kckr_from' => $dateStart,
                        'received_date_kckr_to' => $dateEnd,
                    ])->recordsFiltered ?? 0;
                }

                $arrears = $warning->TAGIHAN_KOLEKSI - $totalCollection;
                $dataSend = $this->buildSendData('whatsapp', $warning, $warningTarget, $totalCollection, $arrears);
                $fileData = null;

                if ($linkFile) {
                    try {
                        $streamUrl = url('stream-file?type=' . $fileType . '&id=' . ($warning->ID ?? '') . '&filename=' . $linkFile);
                        $responseStream = Http::withCookies($request->cookies->all(), config('session.cookie'))
                            ->timeout(60)
                            ->get($streamUrl);

                        if ($responseStream->successful()) {
                            $binaryContent = $responseStream->body();
                            $filename = $linkFile ?? 'Surat_Teguran_' . time() . '.pdf';
                            $path = 'public/temp/' . $filename;

                            Storage::put($path, $binaryContent);

                            $fileData = asset(Storage::url($path));
                        } else {
                            Log::channel('barantum')->error("Gagal mengambil stream file. Status: " . $responseStream->status());
                        }
                    } catch (\Exception $e) {
                        Log::channel('barantum')->error("Error saat fetch stream: " . $e->getMessage());
                    }
                }

                $send = Barantum::send($noTelp, $warning->NAME_PENERBIT ?? 'Penerbit', [$dataSend], $fileData);
                $response = $send;
            } else {
                $response = [
                    'code' => 500,
                    'message' => 'No Telp pelaksana serah kosong'
                ];
            }
        } else {
            $response = [
                'code' => 500,
                'message' => 'Data teguran tidak ditemukan'
            ];
        }

        return response()->json($response);
    }

    private function buildSendData($type, $data, $warningTarget, $totalCollection, $arrears)
    {
        $result = '';

        if ($type == 'whatsapp') {
            switch ($warningTarget) {
                case '1':
                    $result = "";
                    $result .= "*PEMBERITAHUAN – TEGURAN 1*\n\n";
                    $result .= "Kepada Yth. Pelaksana Serah:\n";
                    $result .= "*$data->NAME_PENERBIT*:\n\n";
                    $result .= "Sistem mencatat masih terdapat kewajiban serah simpan karya yang belum dipenuhi sesuai *UU No. 13 Tahun 2018*.\n\n";
                    $result .= "📊 *Rekap Tagihan Saat Ini:*\n";
                    $result .= "📚 Total Tertagih: *$data->TAGIHAN_KOLEKSI*\n";
                    $result .= "📚 Total Diterima: *$totalCollection*\n";
                    $result .= "📚 Total Tunggakan: *$arrears*\n\n";
                    $result .= "Mohon segera selesaikan kewajiban Anda melalui portal SAKEDAP:\n";
                    $result .= "🌐 https://sakedap.perpusnas.go.id\n\n";
                    $result .= "📄 *Dokumen Surat Teguran 1 resmi telah kami lampirkan bersama pesan ini.*\n\n";

                    break;
                case '2':
                    $result = "";
                    $result .= "*PERINGATAN – TEGURAN 2*\n\n";
                    $result .= "Kepada Yth. Pelaksana Serah:\n";
                    $result .= "*$data->NAME_PENERBIT*:\n\n";
                    $result .= "Menindaklanjuti surat sebelumnya, kami belum menerima penyelesaian kewajiban serah simpan karya sesuai *UU No. 13 Tahun 2018*.\n\n";
                    $result .= "📊 *Status Tunggakan:*\n";
                    $result .= "📚 Total Tertagih: *$data->TAGIHAN_KOLEKSI*\n";
                    $result .= "📚 Total Diterima: *$totalCollection*\n";
                    $result .= "📚 Total Tunggakan: *$arrears*\n\n";
                    $result .= "Kami mohon kerja sama Saudara untuk segera memenuhi kewajiban ini guna menghindari sanksi administratif.\n\n";
                    $result .= "🌐 Akses Portal: https://sakedap.perpusnas.go.id\n\n";
                    $result .= "📄 *Dokumen Surat Teguran 2 resmi telah kami lampirkan bersama pesan ini.*\n\n";
                    $result .= "Terima kasih atas kerja samanya.\n\n";

                    break;
                case '3':
                    $result = "";
                    $result .= "*PERINGATAN – TEGURAN 3 (TERAKHIR)*\n\n";
                    $result .= "Kepada Yth. Pelaksana Serah:\n";
                    $result .= "*$data->NAME_PENERBIT*:\n\n";
                    $result .= "Ini adalah *Peringatan Terakhir*. Sampai saat ini kewajiban serah simpan karya Saudara belum terpenuhi.\n\n";
                    $result .= "📊 *Total Kewajiban Tertunggak:*\n";
                    $result .= "📚 Total Tertagih: *$data->TAGIHAN_KOLEKSI*\n";
                    $result .= "📚 Total Diterima: *$totalCollection*\n";
                    $result .= "📚 Total Tunggakan: *$arrears*\n\n";
                    $result .= "Sesuai *UU No. 13 Tahun 2018*, mohon segera melakukan serah simpan sebelum dilakukan tindakan lebih lanjut sesuai ketentuan perundang-undangan.\n\n";
                    $result .= "👉 Segera proses di: https://sakedap.perpusnas.go.id\n\n";
                    $result .= "📄 *Dokumen Surat Teguran 3 resmi telah kami lampirkan bersama pesan ini.*\n\n";
                    $result .= "Harap menjadi perhatian serius.\n\n";

                    break;
                default:
                    $result = '';

                    break;
            }
        } else {
            $result = '
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <style>
                        body {
                            font-family: `Helvetica Neue`, Helvetica, Arial, sans-serif;
                            background-color: #f4f4f4;
                            margin: 0;
                            padding: 0;
                            color: #333;
                        }

                        .container {
                            max-width: 600px;
                            margin: 30px auto;
                            background: #ffffff;
                            padding: 30px;
                            border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                        }

                        .header {
                            text-align: center;
                            border-bottom: 2px solid #0056b3;
                            padding-bottom: 20px; margin-bottom: 25px;
                        }

                        .header h2 {
                            margin: 0; color: #0056b3;
                            font-size: 24px;
                        }

                        .badge {
                            display: inline-block;
                            padding: 8px 12px;
                            margin-top: 10px;
                            border-radius: 4px;
                            font-size: 14px;
                            font-weight: bold;
                            text-transform: uppercase;
                            letter-spacing: 1px;
                        }

                        .badge-warning {
                            background-color: #fff3cd;
                            color: #856404;
                            border: 1px solid #ffeeba;
                        }

                        .badge-danger {
                            background-color: #f8d7da;
                            color: #721c24;
                            border: 1px solid #f5c6cb;
                        }

                        .content {
                            line-height: 1.6;
                            font-size: 16px;
                        }

                        .table-data {
                            width: 100%;
                            border-collapse: collapse;
                            margin: 20px 0;
                            background-color: #fafafa;
                        }

                        .table-data th, .table-data td {
                            border: 1px solid #ddd;
                            padding: 12px;
                            text-align: left;
                        }

                        .table-data th {
                            background-color: #e9ecef;
                            color: #495057;
                        }

                        .btn-action {
                            display: block;
                            width: fit-content;
                            margin: 25px auto;
                            background-color: #0056b3;
                            color: #ffffff !important;
                            text-decoration: none;
                            padding: 12px 30px;
                            border-radius: 50px;
                            font-weight: bold;
                            text-align: center;
                        }

                        .footer {
                            margin-top: 30px;
                            border-top: 1px solid #eee;
                            padding-top: 20px;
                            font-size: 12px;
                            color: #777;
                            text-align: center;
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="header">
                            <h2>Perpustakaan Nasional Republik Indonesia</h2>
                            <span class="badge badge-warning">PEMBERITAHUAN TEGURAN KE-' . $warningTarget . '</span>
                        </div>
                        <div class="content">
                            <p>Yth. Pelaksana Serah,<br><strong>' . $data->NAME_PENERBIT . '</strong></p>
                            <p>Sistem mencatat masih terdapat kewajiban serah simpan karya yang belum dipenuhi sesuai <b>UU No. 13 Tahun 2018</b>.</p>
                            <p>Berikut adalah rekapitulasi kewajiban serah simpan yang tertunggak:</p>
                            <table class="table-data">
                                <tr>
                                    <td>Total Tertagih</td>
                                    <td><strong>' . $data->TAGIHAN_KOLEKSI . '</strong></td>
                                </tr>
                                <tr>
                                    <td>Total Diterima</td>
                                    <td><strong>' . $totalCollection . '</strong></td>
                                </tr>
                                <tr>
                                    <td>Total Tertunggak</td>
                                    <td><strong>' . $arrears . '</strong></td>
                                </tr>
                            </table>
                            <p>Mohon segera melakukan pengiriman koleksi fisik atau unggah koleksi digital melalui portal resmi kami.</p>
                            <a href="https://sakedap.perpusnas.go.id" class="btn-action">Akses Portal SAKEDAP</a>
                            <p style="font-size: 0.9em; color: #555; text-align: center;">
                                <em>*Surat Teguran Resmi (PDF) telah kami lampirkan pada email ini.</em>
                            </p>
                        </div>
                        <div class="footer">
                            <p>Terima kasih atas perhatian dan kerja sama Anda.</p>
                            <p><strong>Perpustakaan Nasional RI</strong><br>"Perpustakaan Hadir Demi Martabat Bangsa"</p>
                        </div>
                    </div>
                </body>
                </html>
            ';
        }

        return $result;
    }
}
