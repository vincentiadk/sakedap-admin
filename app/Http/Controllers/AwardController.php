<?php

namespace App\Http\Controllers;

use App\Helpers\QueryAPI;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AwardController extends Controller
{
    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'content' => 'award',
                'plugins' => [
                    'datatable',
                ]
            ]
        ]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'e_awards.id',
            null,
            null,
            'e_awards.year',
            'e_awards.theme',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 10);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = 'e_awards.deleted_at is null';

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
                e_awards
            where
                deleted_at is null
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                e_awards
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
                                e_awards.id,
                                e_awards.year,
                                e_awards.theme,
                                count(e_collection_award.id) as total_collection
                            from
                                e_awards
                            left join
                                e_collection_award on e_collection_award.award_id = e_awards.id
                            $whereClause
                            group by
                                e_awards.id,
                                e_awards.year,
                                e_awards.theme
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
                            <a href="' . url('award/nomination/' . $val->ID) . '" class="dropdown-item">
                                <i class="ph-trend-up me-1"></i>
                                Nominasi
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
                    $val->TOTAL_COLLECTION,
                    $val->YEAR,
                    $val->THEME,
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
            'year' => 'required',
            'theme' => 'required',
        ], [
            'year.required' => 'Tahun tidak boleh kosong',
            'theme.required' => 'Tema tidak boleh kosong',
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                QueryAPI::create('e_awards', [
                    'year' => $request->year,
                    'theme' => $request->theme,
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
                e_awards
            where
                id = $id
        ", true);

        return response()->json($data);
    }

    public function updateData(Request $request)
    {
        $id = $request->table_id;
        $validation = Validator::make($request->all(), [
            'year' => 'required',
            'theme' => 'required',
        ], [
            'year.required' => 'Tahun tidak boleh kosong',
            'theme.required' => 'Tema tidak boleh kosong',
        ]);

        if ($validation->fails()) {
            $response = [
                'code' => 400,
                'error' => $validation->errors()->all(),
            ];
        } else {
            try {
                QueryAPI::update('e_awards', $id, [
                    'year' => $request->year,
                    'theme' => $request->theme,
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
            QueryAPI::update('e_awards', $id, [
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

    public function nomination($id)
    {
        $award = QueryAPI::get("
            select
                *
            from
                e_awards
            where
                id = $id
        ", true);

        if (!$award) {
            abort(404);
        }

        $awardCatalog = QueryAPI::get("
            select
                e_collection_award.*,
                catalogs.title as title_catalog,
                catalogs.penerbit_id as penerbit_id_catalog,
                penerbit.name as name_penerbit
            from
                e_collection_award
            join
                catalogs on catalogs.id = e_collection_award.collection_id
            join
                penerbit on penerbit.id = catalogs.penerbit_id
            where
                award_id = $id
        ");

        return view('layouts.index', [
            'data' => [
                'worksheet' => QueryAPI::get("select * from worksheets where category is not null") ?? [],
                'award' => $award,
                'catalog' => $awardCatalog,
                'content' => 'award-nomination',
                'plugins' => [
                    'datatable',
                    'select2',
                ]
            ]
        ]);
    }

    public function nominationDatatable(Request $request)
    {
        $column = [
            null,
            'catalogs.id',
            null,
            'penerbit.name',
            'catalogs.title',
            'worksheets.name',
            'catalogs.isbn',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 10);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "
            (
                catalogs.isdelete = 0 or
                catalogs.isdelete is null
            )
        ";

        if ($request->title) {
            $title = strtoupper($request->title);
            $whereCondition[] = "upper(catalogs.title) like '%$title%'";
        }

        if ($request->executor_id) {
            $whereCondition[] = "catalogs.penerbit_id = $request->executor_id";
        }

        if ($request->province_id) {
            $whereCondition[] = "kabupaten.propinsiid = $request->province_id";
        }

        if ($request->year) {
            $whereCondition[] = "catalogs.publishyear = $request->year";
        }

        if ($request->worksheet_id) {
            $whereCondition[] = "catalogs.worksheet_id = $request->worksheet_id";
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
                catalogs
            join
                worksheets on worksheets.id = catalogs.worksheet_id
            where
                (
                    catalogs.isdelete = 0 or
                    catalogs.isdelete is null
                )
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                catalogs
            join
                penerbit on penerbit.id = catalogs.penerbit_id
            join
                e_collections on e_collections.id = catalogs.edeposit_col_id
            join
                kabupaten on kabupaten.id = e_collections.kabupaten_id
            join
                worksheets on worksheets.id = catalogs.worksheet_id
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
                                catalogs.id,
                                catalogs.title,
                                catalogs.isbn,
                                catalogs.penerbit_id,
                                penerbit.name as name_penerbit,
                                worksheets.name as name_worksheet
                            from
                                catalogs
                            join
                                penerbit on penerbit.id = catalogs.penerbit_id
                            join
                                e_collections on e_collections.id = catalogs.edeposit_col_id
                            join
                                kabupaten on kabupaten.id = e_collections.kabupaten_id
                            join
                                worksheets on worksheets.id = catalogs.worksheet_id
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
                $inputHidden = '
                    <input type="hidden" name="data" data-id="' . $val->ID . '" data-title="' . $val->TITLE . '" data-executor="' . $val->NAME_PENERBIT . '">
                ';

                $data[] = [
                    $inputHidden,
                    $start + 1,
                    $val->PENERBIT_ID . ' | ' . $val->NAME_PENERBIT,
                    $val->TITLE,
                    $val->NAME_WORKSHEET,
                    $val->ISBN,
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

    public function nominationAdd(Request $request, $id)
    {
        $catalog = $request->catalog ?? [];

        try {
            foreach ($catalog as $c) {
                QueryAPI::create('e_collection_award', [
                    'collection_id' => $c['id'],
                    'award_id' => $id,
                    'token_access' => Str::random(40),
                ]);
            }

            $response = [
                'code' => 200,
                'message' => 'Data telah dimasukan nominasi'
            ];
        } catch (\Exception $e) {
            $response = [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }

        return response()->json($response);
    }

    public function nominationRemove(Request $request, $id)
    {
        $eCollectionAwardId = $request->id;

        try {
            QueryAPI::delete('e_collection_award', $eCollectionAwardId);

            $response = [
                'code' => 200,
                'message' => 'Data telah dihapus dari nominasi'
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
