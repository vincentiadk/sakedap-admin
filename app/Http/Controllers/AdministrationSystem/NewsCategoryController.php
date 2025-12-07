<?php

namespace App\Http\Controllers\AdministrationSystem;

use App\Helpers\QueryAPI;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class NewsCategoryController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'content' => 'administration-system.news-category',
                'plugins' => [
                    'select2',
                    'datatable',
                ]
            ]
        ]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'id',
            null,
            'pages',
            'name',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition = [];

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
                e_news_kategori
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                e_news_kategori
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
                                id,
                                parent_id,
                                name,
                                pages,
                                level,
                                rpad(' ', (level - 1) * 2) || name as tree_view,
                                ltrim(sys_connect_by_path(name, ' > '), ' > ') as tree_path
                            from
                                e_news_kategori
                            $whereClause
                            start with
                                parent_id is null
                            connect by prior
                                id = parent_id
                            order siblings by
                                name
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
                    $val->PAGES == 1 ? '<i class="ph-check text-success"></i>' : '<i class="ph-x text-danger"></i>',
                    $val->TREE_PATH,
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
        ], [
            'name.required' => 'Nama tidak boleh kosong',
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                QueryAPI::create('e_news_kategori', [
                    'name' => $request->name,
                    'ringkasan' => $request->summary,
                    'slug' => Str::slug($request->name, '-'),
                    'pages' => $request->pages,
                    'parent_id' => $request->parent_id,
                    'createby' => session('username'),
                    'createdate' => date('Y-m-d H:i:s'),
                    'createterminal' => $request->ip(),
                    'updateby' => session('username'),
                    'updatedate' => date('Y-m-d H:i:s'),
                    'updateterminal' => $request->ip(),
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
                id,
                parent_id,
                name,
                pages,
                level,
                ringkasan,
                rpad(' ', (level - 1) * 2) || name as tree_view,
                ltrim(sys_connect_by_path(name, ' > '), ' > ') as tree_path
            from
                e_news_kategori
            where
                id = $id
            start with
                parent_id is null
            connect by prior
                id = parent_id
            order siblings by
                name
        ", true);

        if ($data) {
            $data->TREE_PATH = str_replace(' > ' . $data->NAME, '', $data->TREE_PATH);
        }

        return response()->json($data);
    }

    public function updateData(Request $request)
    {
        $id = $request->table_id;
        $validation = Validator::make($request->all(), [
            'name' => 'required',
        ], [
            'name.required' => 'Nama tidak boleh kosong',
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                QueryAPI::update('e_news_kategori', $id, [
                    'name' => $request->name,
                    'ringkasan' => $request->summary,
                    'slug' => Str::slug($request->name, '-'),
                    'pages' => $request->pages,
                    'parent_id' => $request->parent_id,
                    'updateby' => session('username'),
                    'updatedate' => date('Y-m-d H:i:s'),
                    'updateterminal' => $request->ip(),
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
            QueryAPI::delete('e_news_kategori', $id);

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
