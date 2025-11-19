<?php

namespace App\Http\Controllers\CoachingSupervision;

use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ExecutorAccessController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'group' => QueryAPI::get("select * from e_publisher_groups where deleted_at is null") ?? [],
                'content' => 'coaching-supervision.executor-access',
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
            'e_publisher_access.id',
            null,
            'e_publisher_groups.name',
            'penerbit.nama',
            'penerbit.kd_penerbit',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = 'e_publisher_access.deleted_at is null';

        if ($request->publisher_group_id) {
            $whereCondition[] = 'e_publisher_access.publisher_group_id = ' . $request->publisher_group_id;
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
                e_publisher_access
            where
                deleted_at is null
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                e_publisher_access
            left join
                e_publisher_groups on e_publisher_access.publisher_group_id = e_publisher_groups.id
            left join
                penerbit on penerbit.id = e_publisher_access.publisher_id
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
                                e_publisher_access.*,
                                penerbit.name as name_penerbit,
                                penerbit.kd_penerbit as kd_penerbit,
                                e_publisher_groups.name as name_publisher_group
                            from
                                e_publisher_access
                            left join
                                e_publisher_groups on e_publisher_access.publisher_group_id = e_publisher_groups.id
                            left join
                                penerbit on penerbit.id = e_publisher_access.publisher_id
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
                    $val->NAME_PUBLISHER_GROUP,
                    $val->PUBLISHER_ID . ' | ' . $val->NAME_PENERBIT,
                    $val->KD_PENERBIT,
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
            'publisher_group_id' => 'required',
        ], [
            'executor_id.required' => 'Penerbit tidak boleh kosong',
            'publisher_group_id.required' => 'Grup tidak boleh kosong',
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                QueryAPI::create('e_publisher_access', [
                    'publisher_id' => $request->executor_id,
                    'publisher_group_id' => $request->publisher_group_id,
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
                e_publisher_access.*,
                penerbit.name as name_penerbit
            from
                e_publisher_access
            left join
                penerbit on penerbit.id = e_publisher_access.publisher_id
            where
                e_publisher_access.id = $id
        ", true);

        return response()->json($data);
    }

    public function updateData(Request $request)
    {
        $id = $request->table_id;
        $validation = Validator::make($request->all(), [
            'executor_id' => 'required',
            'publisher_group_id' => 'required',
        ], [
            'executor_id.required' => 'Penerbit tidak boleh kosong',
            'publisher_group_id.required' => 'Grup tidak boleh kosong',
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                QueryAPI::update('e_publisher_access', $id, [
                    'publisher_id' => $request->executor_id,
                    'publisher_group_id' => $request->publisher_group_id,
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

        try {
            QueryAPI::update('e_publisher_access', $id, [
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
}
