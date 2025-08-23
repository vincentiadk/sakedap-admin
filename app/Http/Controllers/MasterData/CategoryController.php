<?php

namespace App\Http\Controllers\MasterData;

use App\Helpers\QueryAPI;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $data = [
            'worksheet' => QueryAPI::get("select * from worksheets"),
            'content' => 'master-data.category'
        ];

        return view('layouts.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'e_categories.id',
            null,
            'e_categories.name',
            'worksheets.name',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = $request->search['value'];

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = 'e_categories.deleted_at is null';

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
                e_categories
            where
                deleted_at is null
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                e_categories
            left join
                worksheets on worksheets.id = e_categories.type
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
                                e_categories.*,
                                worksheets.name as name_worksheet
                            from
                                e_categories
                            left join
                                worksheets on worksheets.id = e_categories.type
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
                    $val->NAME,
                    $val->NAME_WORKSHEET,
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
            'type' => 'required',
        ], [
            'name.required' => 'Nama tidak boleh kosong',
            'type.required' => 'Jenis tidak boleh kosong',
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                QueryAPI::create('e_categories', [
                    'name' => $request->name,
                    'slug' => Str::slug($request->name, '-'),
                    'type' => $request->type,
                ]);

                QueryAPI::activityLog([
                    'log_name' => 'default',
                    'description' => 'Membuat data kategori',
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
                e_categories.*,
                worksheets.name as name_worksheet
            from
                e_categories
            left join
                worksheets on worksheets.id = e_categories.type
            where
                e_categories.id = $id
        ", true);

        return response()->json($data);
    }

    public function updateData(Request $request)
    {
        $id = $request->table_id;
        $validation = Validator::make($request->all(), [
            'name' => 'required',
            'type' => 'required',
        ], [
            'name.required' => 'Nama tidak boleh kosong',
            'type.required' => 'Jenis tidak boleh kosong',
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                QueryAPI::update('e_categories', $id, [
                    'name' => $request->name,
                    'slug' => Str::slug($request->name, '-'),
                    'type' => $request->type,
                ]);

                QueryAPI::activityLog([
                    'log_name' => 'default',
                    'description' => 'Mengubah data kategori',
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
                e_categories
            where
                id = $id
        ", true);

        try {
            QueryAPI::update('e_categories', $id, [
                'deleted_at' => date('Y-m-d')
            ]);

            QueryAPI::activityLog([
                'log_name' => 'default',
                'description' => 'Menghapus data kategori',
                'causer_id' => session('id'),
                'properties' => json_encode(['nama' => $data->NAME ?? null]),
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
