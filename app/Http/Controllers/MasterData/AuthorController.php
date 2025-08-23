<?php

namespace App\Http\Controllers\MasterData;

use App\Helpers\QueryAPI;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AuthorController extends Controller
{
    public function index()
    {
        $data = [
            'content' => 'master-data.author'
        ];

        return view('layouts.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'e_authors.id',
            null,
            'e_authors.fullname',
            'e_authors.title',
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
                e_authors
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                e_authors
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
                                e_authors
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
                    $val->FULLNAME,
                    $val->TITLE,
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
            'fullname' => 'required',
        ], [
            'fullname.required' => 'Nama tidak boleh kosong'
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                QueryAPI::create('e_authors', [
                    'fullname' => $request->fullname,
                    'slug' => Str::slug($request->fullname, '-'),
                    'title' => $request->title,
                ]);

                QueryAPI::activityLog([
                    'log_name' => 'default',
                    'description' => 'Membuat data pengarang',
                    'causer_id' => session('id'),
                    'properties' => json_encode(['nama' => $request->fullname]),
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
                e_authors
            where
                id = $id
        ", true);

        return response()->json($data);
    }

    public function updateData(Request $request)
    {
        $id = $request->table_id;
        $validation = Validator::make($request->all(), [
            'fullname' => 'required',
        ], [
            'fullname.required' => 'Nama tidak boleh kosong'
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                QueryAPI::update('e_authors', $id, [
                    'fullname' => $request->fullname,
                    'slug' => Str::slug($request->fullname, '-'),
                    'title' => $request->title,
                ]);

                QueryAPI::activityLog([
                    'log_name' => 'default',
                    'description' => 'Mengubah data pengarang',
                    'causer_id' => session('id'),
                    'properties' => json_encode(['nama' => $request->fullname]),
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
                e_authors
            where
                id = $id
        ", true);

        try {
            QueryAPI::delete('e_authors', $id);

            QueryAPI::activityLog([
                'log_name' => 'default',
                'description' => 'Menghapus data pengarang',
                'causer_id' => session('id'),
                'properties' => json_encode(['nama' => $data->FULLNAME ?? null]),
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
