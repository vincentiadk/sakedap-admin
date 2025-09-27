<?php

namespace App\Http\Controllers\Executor;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class WarningController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'officer' => QueryAPI::get("select * from petugas_pembina"),
                'content' => 'executor.warning',
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
            'penerbit.name',
            'branchs.name',
            'e_publisher_warnings.warning_date',
            'e_publisher_warnings.tagihan_koleksi',
            'e_publisher_warnings.status',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = 'e_publisher_warnings.deleted_at is null';

        if (Main::isNotCenterBranch()) {
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
                                penerbit.name as name_penerbit,
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
                )
            where
                rnum > $start and rnum <= $length
        ");

        if ($queryData) {
            foreach ($queryData as $val) {
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
                    $dateEnd = Carbon::parse($warningDate1)->addDays(40)->format('Y-m-d');

                    $totalCollection = QueryAPI::get("
                        select
                            sum(letter_detail.qty_accept) as total
                        from
                            letter_detail
                        left join
                            letter on letter.letter_id = letter_detail.letter_id
                        where
                            (letter.accept_date >= to_date('$dateStart', 'YYYY-MM-DD') and letter.accept_date < to_date('$dateEnd', 'YYYY-MM-DD') + 1)
                    ", true);

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
                        <div class="fw-bold"><small>Teguran 1</small></div>
                        <div><small class="text-muted">Tanggal : ' . Carbon::parse($warningDate1)->format('d/m/Y') . '</small></div>
                        <div><small class="text-muted">Koleksi Diterima : ' . ($totalCollection->TOTAL ?? 0) . '</small></div>
                        <div><small class="text-muted">File : ' . $file . '</small></div>
                    ';
                }

                if ($warningDate2) {
                    $dateStart = Carbon::parse($warningDate2)->format('Y-m-d');
                    $dateEnd = Carbon::parse($warningDate2)->addDays(40)->format('Y-m-d');

                    $totalCollection = QueryAPI::get("
                        select
                            sum(letter_detail.qty_accept) as total
                        from
                            letter_detail
                        left join
                            letter on letter.letter_id = letter_detail.letter_id
                        where
                            (letter.accept_date >= to_date('$dateStart', 'YYYY-MM-DD') and letter.accept_date < to_date('$dateEnd', 'YYYY-MM-DD') + 1)
                    ", true);

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
                        <div class="fw-bold"><small>Teguran 2</small></div>
                        <div><small class="text-muted">Tanggal : ' . Carbon::parse($warningDate2)->format('d/m/Y') . '</small></div>
                        <div><small class="text-muted">Koleksi Diterima : ' . ($totalCollection->TOTAL ?? 0) . '</small></div>
                        <div><small class="text-muted">File : ' . $file . '</small></div>
                    ';
                }

                if ($warningDate3) {
                    $dateStart = Carbon::parse($warningDate3)->format('Y-m-d');
                    $dateEnd = Carbon::parse($warningDate3)->addDays(40)->format('Y-m-d');

                    $totalCollection = QueryAPI::get("
                        select
                            sum(letter_detail.qty_accept) as total
                        from
                            letter_detail
                        left join
                            letter on letter.letter_id = letter_detail.letter_id
                        where
                            (letter.accept_date >= to_date('$dateStart', 'YYYY-MM-DD') and letter.accept_date < to_date('$dateEnd', 'YYYY-MM-DD') + 1)
                    ", true);

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
                        <div class="fw-bold"><small>Teguran 3</small></div>
                        <div><small class="text-muted">Tanggal : ' . Carbon::parse($warningDate3)->format('d/m/Y') . '</small></div>
                        <div><small class="text-muted">Koleksi Diterima : ' . ($totalCollection->TOTAL ?? 0) . '</small></div>
                        <div><small class="text-muted">File : ' . $file . '</small></div>
                    ';
                }

                $warningDateHTML = $warningDate1HTML . $warningDate2HTML . $warningDate3HTML;

                $data[] = [
                    $start + 1,
                    $action,
                    $val->NAME_PENERBIT,
                    $val->NAME_BRANCH,
                    $warningDateHTML,
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
            'file' => 'required|mimes:jpg,jpeg,png,pdf',
            'file_2' => 'nullable|mimes:jpg,jpeg,png,pdf',
            'file_3' => 'nullable|mimes:jpg,jpeg,png,pdf',
        ], [
            'executor_id.required' => 'Pelaksana serah tidak boleh kosong',
            'branch_id.required' => 'Dari tidak boleh kosong',
            'warning_date.required' => 'Tanggal teguran tidak boleh kosong',
            'file.required' => 'File 1 tidak boleh kosong',
            'file.mimes' => 'File 1 hanya boleh jpg, jpeg, png, pdf',
            'file_2.mimes' => 'File 2 hanya boleh jpg, jpeg, png, pdf',
            'file_3.mimes' => 'File 3 hanya boleh jpg, jpeg, png, pdf',
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
                    'createby' => session('name'),
                    'updateby' => session('name'),
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
        $query = QueryAPI::get("select * from e_publisher_warnings where id = $id");

        $validation = Validator::make($request->all(), [
            'executor_id' => 'required',
            'branch_id' => 'required',
            'warning_date' => 'required',
            'file' => 'nullable|mimes:jpg,jpeg,png,pdf',
            'file_2' => 'nullable|mimes:jpg,jpeg,png,pdf',
            'file_3' => 'nullable|mimes:jpg,jpeg,png,pdf',
        ], [
            'executor_id.required' => 'Pelaksana serah tidak boleh kosong',
            'branch_id.required' => 'Dari tidak boleh kosong',
            'warning_date.required' => 'Tanggal teguran tidak boleh kosong',
            'file.mimes' => 'File 1 hanya boleh jpg, jpeg, png, pdf',
            'file_2.mimes' => 'File 2 hanya boleh jpg, jpeg, png, pdf',
            'file_3.mimes' => 'File 3 hanya boleh jpg, jpeg, png, pdf',
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
                    'updateby' => session('name'),
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

    public function destroyData(Request $request)
    {
        $id = $request->id;

        try {
            QueryAPI::update('e_publisher_warnings', $id, [
                'deleted_at' => date('Y-m-d')
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
}
