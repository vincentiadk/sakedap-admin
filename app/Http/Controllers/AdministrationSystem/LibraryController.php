<?php

namespace App\Http\Controllers\AdministrationSystem;

use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LibraryController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'content' => 'administration-system.library',
                'plugins' => [
                    'datatable',
                    'select2',
                ]
            ]
        ]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'branchs.id',
            null,
            'propinsi.namapropinsi',
            'branchs.code',
            'branchs.name',
            'branchs.phone',
            'branchs.kode_pos',
            'branchs.alamat',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = '(branchs.isdelete = 0 or branchs.isdelete is null)';

        if (Main::isNotSuperAdmin()) {
            $whereCondition[] = 'branchs.province_id = ' . session('province_id');
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
                branchs
            where
                isdelete = 0 or
                isdelete is null
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                branchs
            left join
                propinsi on propinsi.id = branchs.province_id
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
                                branchs.*,
                                propinsi.namapropinsi as namapropinsi
                            from
                                branchs
                            left join
                                propinsi on propinsi.id = branchs.province_id
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

                $data[] = [
                    $start + 1,
                    $action,
                    $val->NAMAPROPINSI,
                    $val->CODE,
                    $val->NAME,
                    $val->PHONE,
                    $val->KODE_POS,
                    $val->ALAMAT,
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
            'name' => 'required',
            'code' => 'required',
            'postal_code' => 'required|digits:5|numeric',
            'phone' => 'required|min_digits:8|max_digits:13|numeric',
            'province_id' => 'required',
        ], [
            'name.required' => 'Nama tidak boleh kosong',
            'code.required' => 'Kode tidak boleh kosong',
            'postal_code.required' => 'Kode pos tidak boleh kosong',
            'postal_code.digits' => 'Kode pos harus 5 digit',
            'postal_code.numeric' => 'Kode pos harus angka',
            'phone.required' => 'Telepon tidak boleh kosong',
            'phone.min_digits' => 'Telepon minimal 8 digit',
            'phone.max_digits' => 'Telepon maksimal 13 digit',
            'phone.numeric' => 'Telepon harus angka',
            'province_id.required' => 'Provinsi tidak boleh kosong',
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                QueryAPI::create('branchs', [
                    'code' => $request->code,
                    'name' => $request->name,
                    'isdelete' => 0,
                    'createby' => session('username'),
                    'createdate' => date('Y-m-d H:i:s'),
                    'createterminal' => $request->ip(),
                    'updateby' => session('username'),
                    'updatedate' => date('Y-m-d H:i:s'),
                    'updateterminal' => $request->ip(),
                    'alamat' => $request->address,
                    'province_id' => $request->province_id,
                    'kode_pos' => $request->postal_code,
                    'phone' => $request->phone,
                ], false);

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
                branchs.*,
                propinsi.namapropinsi as namapropinsi
            from
                branchs
            left join
                propinsi on propinsi.id = branchs.province_id
            where
                branchs.id = $id
        ", true);

        return response()->json($data);
    }

    public function updateData(Request $request)
    {
        $id = $request->table_id;
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'code' => 'required',
            'postal_code' => 'required|digits:5|numeric',
            'phone' => 'required|min_digits:8|max_digits:13|numeric',
            'province_id' => 'required',
        ], [
            'name.required' => 'Nama tidak boleh kosong',
            'code.required' => 'Kode tidak boleh kosong',
            'postal_code.required' => 'Kode pos tidak boleh kosong',
            'postal_code.digits' => 'Kode pos harus 5 digit',
            'postal_code.numeric' => 'Kode pos harus angka',
            'phone.required' => 'Telepon tidak boleh kosong',
            'phone.min_digits' => 'Telepon minimal 8 digit',
            'phone.max_digits' => 'Telepon maksimal 13 digit',
            'phone.numeric' => 'Telepon harus angka',
            'province_id.required' => 'Provinsi tidak boleh kosong',
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                QueryAPI::update('branchs', $id, [
                    'code' => $request->code,
                    'name' => $request->name,
                    'updateby' => session('username'),
                    'updatedate' => date('Y-m-d H:i:s'),
                    'updateterminal' => $request->ip(),
                    'alamat' => $request->address,
                    'province_id' => $request->province_id,
                    'kode_pos' => $request->postal_code,
                    'phone' => $request->phone,
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

    public function destroyData(Request $request)
    {
        $id = $request->id;

        try {
            QueryAPI::update('branchs', $id, [
                'isdelete' => 1,
                'updateby' => session('username'),
                'updatedate' => date('Y-m-d H:i:s'),
                'updateterminal' => $request->ip(),
            ], false);

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
