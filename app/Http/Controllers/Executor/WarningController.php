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
            'e_publisher_warnings.link_file',
            'penerbit.name',
            'branchs.name',
            'e_publisher_warnings.warning_date',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = $request->search['value'];

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
                    $terms[] = "$c like '%$search%'";
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

                $file = '
                    <button type="button" class="btn btn-danger btn-sm" disabled>Tidak Ada</button>
                ';

                if ($val->LINK_FILE) {
                    $file = '
                        <a href="' . url('stream-file') . '?type=publisher_warning&id=' . $val->ID . '&filename=' . $val->LINK_FILE . '" class="btn btn-success btn-sm" target="_blank">Lihat File</a>
                    ';
                }

                $data[] = [
                    $start + 1,
                    $action,
                    $file,
                    $val->NAME_PENERBIT,
                    $val->NAME_BRANCH,
                    Carbon::parse($val->WARNING_DATE)->format('d/m/Y'),
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
        ], [
            'executor_id.required' => 'Pelaksana serah tidak boleh kosong',
            'branch_id.required' => 'Dari tidak boleh kosong',
            'warning_date.required' => 'Tanggal teguran tidak boleh kosong',
            'file.required' => 'File tidak boleh kosong',
            'file.mimes' => 'File hanya boleh jpg, jpeg, png, pdf',
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
        ], [
            'executor_id.required' => 'Pelaksana serah tidak boleh kosong',
            'branch_id.required' => 'Dari tidak boleh kosong',
            'warning_date.required' => 'Tanggal teguran tidak boleh kosong',
            'file.mimes' => 'File hanya boleh jpg, jpeg, png, pdf',
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
