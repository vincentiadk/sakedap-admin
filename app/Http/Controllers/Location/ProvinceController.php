<?php

namespace App\Http\Controllers\Location;

use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ProvinceController extends Controller
{
    public function index()
    {
        $data = [
            'content' => 'location.province'
        ];

        return view('layouts.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'propinsi.id',
            null,
            'propinsi.code',
            'propinsi.name',
            'propinsi.latitude',
            'propinsi.longitude',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = $request->search['value'];

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition = [];

        if ($search) {
            $terms = [];

            foreach ($column as $c) {
                if ($c) {
                    $terms[] = "$c like '%$search%'";
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
                propinsi
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                propinsi
            $whereClause
        ", true)->TOTAL ?? 0;

        $queryData = QueryAPI::get("
            select
                *
            from (
                    select
                        ROWNUM as rnum,
                        data.*
                    from
                        (
                            select
                                *
                            from
                                propinsi
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
                            <a href="javascript:void(0);" class="dropdown-item fs-13" onclick="showDataUpdate(' . $val->ID . ')">
                                <i class="ph-pen me-1"></i>
                                Ubah Data
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item fs-13" onclick="destroyData(' . $val->ID . ')">
                                <i class="ph-trash-simple me-1"></i>
                                Hapus Data
                            </a>
                        </div>
                    </div>
                ';
                $data[] = [
                    $start + 1,
                    $action,
                    $val->CODE,
                    $val->NAMAPROPINSI,
                    $val->LATITUDE,
                    $val->LONGITUDE,
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
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ], [
            'name.required' => 'Nama tidak boleh kosong',
            'code.required' => 'Kode tidak boleh kosong',
            'latitude.required' => 'Latitude harus angka',
            'longitude.required' => 'Longitude harus angka',
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                QueryAPI::create('propinsi', [
                    'namapropinsi' => $request->name,
                    'createby' => session('fullname'),
                    'createdate' => date('Y-m-d H:i:s'),
                    'updateby' => session('fullname'),
                    'updatedate' => date('Y-m-d H:i:s'),
                    'code' => $request->code,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ], false);

                QueryAPI::activityLog([
                    'log_name' => 'default',
                    'description' => 'Membuat data provinsi',
                    'causer_id' => session('id'),
                    'properties' => json_encode(['nama' => $request->name]),
                ]);

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
                *
            from
                propinsi
            where
                id = $id
        ", true);

        return response()->json($data);
    }

    public function updateData(Request $request)
    {
        $id = $request->table_id;
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'code' => 'required',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ], [
            'name.required' => 'Nama tidak boleh kosong',
            'code.required' => 'Kode tidak boleh kosong',
            'latitude.required' => 'Latitude harus angka',
            'longitude.required' => 'Longitude harus angka',
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                QueryAPI::update('propinsi', $id, [
                    'namapropinsi' => $request->name,
                    'updateby' => session('fullname'),
                    'updatedate' => date('Y-m-d H:i:s'),
                    'code' => $request->code,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ], false);

                QueryAPI::activityLog([
                    'log_name' => 'default',
                    'description' => 'Mengubah data provinsi',
                    'causer_id' => session('id'),
                    'properties' => json_encode(['nama' => $request->name]),
                ]);

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
        $data = QueryAPI::get("
            select
                *
            from
                propinsi
            where
                id = $id
        ", true);

        try {
            QueryAPI::delete('propinsi', $id);

            QueryAPI::activityLog([
                'log_name' => 'default',
                'description' => 'Menghapus data provinsi',
                'causer_id' => session('id'),
                'properties' => json_encode(['nama' => $data->NAMAPROPINSI ?? null]),
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
