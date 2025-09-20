<?php

namespace App\Http\Controllers\Collection;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function index()
    {
        $data = [
            'worksheet' => QueryAPI::get("select * from worksheets where category is not null"),
            'content' => 'collection.review'
        ];

        return view('layouts.index', ['data' => $data]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'e_collections.id',
            null,
            'penerbit.name',
            'e_collections.title',
            'worksheets.name',
            'e_collections.code',
            'e_collections.updated_at',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 0);

        $data = [];
        $search = $request->search['value'];

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = '(e_collections.status = 1 and e_collections.deleted_at is null) and (e_collections.parent_id = 0 or e_collections.parent_id is null)';

        if ($request->title) {
            $whereCondition[] = "(e_collections.title_ori like '%$search%' or e_collections.title like '%$search%')";
        }

        if ($request->executor_id) {
            $whereCondition[] = "e_collections.penerbit_id = $request->executor_id";
        }

        if ($request->province_id) {
            $whereCondition[] = "kabupaten.propinsiid = $request->province_id";
        }

        if ($request->year) {
            $whereCondition[] = "e_collections.publication_year = $request->year";
        }

        if ($request->worksheet_id) {
            $whereCondition[] = "e_collections.worksheet_id = $request->worksheet_id";
        }

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $whereCondition[] = "(e_collections.updated_at >= date '$startDate' and e_collections.updated_at <= date '$endDate')";
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
                e_collections
            where
                (
                    parent_id = 0 or
                    parent_id is null
                ) and
                (
                    status = 1 and
                    deleted_at is null
                )
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                e_collections
            join
                penerbit on penerbit.id = e_collections.penerbit_id
            join
                kabupaten on kabupaten.id = e_collections.kabupaten_id
            left join
                worksheets on worksheets.id = e_collections.worksheet_id
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
                                e_collections.*,
                                penerbit.name as name_penerbit,
                                worksheets.name as name_worksheet
                            from
                                e_collections
                            join
                                penerbit on penerbit.id = e_collections.penerbit_id
                            join
                                kabupaten on kabupaten.id = e_collections.kabupaten_id
                            left join
                                worksheets on worksheets.id = e_collections.worksheet_id
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
                    <a href="' . url('collection/review/detail/' . $val->ID) . '" class="btn btn-primary btn-sm">
                        <i class="ph-check-square-offset me-1"></i>
                        Tinjau
                    </a>
                ';

                $data[] = [
                    $start + 1,
                    $action,
                    $val->NAME_PENERBIT,
                    ($val->TITLE ?? $val->TITLE_ORI),
                    $val->NAME_WORKSHEET,
                    $val->CODE,
                    Carbon::parse($val->UPDATED_AT)->format('d/m/Y'),
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

    public function detail(Request $request, $id)
    {
        $collection = QueryAPI::get("
            select
                e_collections.*,
                penerbit.name as name_penerbit,
                kabupaten.namakab as namakab,
                propinsi.namapropinsi as namapropinsi,
                branchs.name as name_branch
            from
                e_collections
            join
                penerbit on penerbit.id = e_collections.penerbit_id
            join
                kabupaten on kabupaten.id = e_collections.kabupaten_id
            join
                propinsi on propinsi.id = kabupaten.propinsiid
            left join
                branchs on branchs.id = e_collections.branch_id
            where
                (
                    e_collections.parent_id = 0 or
                    e_collections.parent_id is null
                ) and
                e_collections.id = $id and
                e_collections.deleted_at is null and
                e_collections.status = 1
        ", true);

        if ($request->ajax()) {
            $validation = Validator::make($request->all(), [
                'worksheet_id' => 'required',
                'city_id' => 'required',
                'branch_id' => 'required',
                'title' => 'required',
                'collection_media_id' => 'required',
                'received_at' => 'required',
                'access' => 'required',
            ], [
                'worksheet_id.required' => 'Jenis tidak boleh kosong',
                'city_id.required' => 'Kota tidak boleh kosong',
                'branch_id.required' => 'Perpustakaan tidak boleh kosong',
                'title.required' => 'Judul tidak boleh kosong',
                'collection_media_id.required' => 'Media tidak boleh kosong',
                'received_at.required' => 'Tanggal terima tidak boleh kosong',
                'access.required' => 'Akses tidak boleh kosong',
            ]);

            if ($validation->fails()) {
                $response = [
                    'code' => 400,
                    'error' => $validation->errors()->all(),
                ];
            } else {
                try {
                    $status = $request->status;
                    $isStatus2 = $status == 2;
                    $isStatus3 = $status == 3;
                    $isStatus5 = $status == 5;
                    $sessionId = session('id');
                    $currentDateTime = date('Y-m-d H:i:s');
                    $revisionCount = $collection->REVISION_COUNT ?? 0;

                    $updateData = [
                        'city_id' => $request->city_id,
                        'branch_id' => $request->branch_id,
                        'title_ori' => $request->title,
                        'album' => $request->album,
                        'slug' => Str::slug($request->title, '-'),
                        'series' => $request->series,
                        'serial' => $request->serial,
                        'ddc' => $request->ddc,
                        'publication_month' => date('m', strtotime($request->publish_time)),
                        'publication_year' => date('Y', strtotime($request->publish_time)),
                        'preview' => $request->preview,
                        'description' => $request->description,
                        'akses' => $request->access,
                        'status' => $status,
                        'price' => str_replace([',', '.'], '', $request->price),
                        'worksheet_id' => $request->worksheet_id,
                        'collection_media_id' => $request->collection_media_id,
                        'kabupaten_id' => $request->city_id,
                        'title' => $request->title,
                    ];

                    if ($isStatus2) {
                        $updateData['deposit'] = Main::generateNumberDeposit(
                            $request->worksheet_id,
                            $request->branch_id,
                            $request->year ?? date('Y'),
                            $request->city_id
                        );

                        $updateData['received_at'] = date('Y-m-d H:i:s', strtotime($request->received_at));
                        $updateData['received_by'] = $sessionId;
                        $updateData['validated_at'] = $currentDateTime;
                        $updateData['validated_by'] = $sessionId;
                    } else {
                        $updateData['deposit'] = null;
                        $updateData['received_at'] = null;
                        $updateData['received_by'] = null;
                        $updateData['validated_at'] = null;
                        $updateData['validated_by'] = null;
                    }

                    if ($isStatus3) {
                        $updateData['revision_count'] = $revisionCount + 1;
                        $updateData['problem'] = $request->problem;
                    }

                    if ($isStatus5) {
                        $updateData['reject'] = $request->reject;
                    }

                    $updateCollection = QueryAPI::update('e_collections', $id, $updateData);

                    if ($updateCollection) {
                        if ($isStatus3 && $request->collection_problem) {
                            $problemsToCreate = [];

                            foreach ($request->collection_problem as $cp) {
                                $problemsToCreate[] = [
                                    'problem_id' => $cp,
                                    'collection_id' => $id,
                                    'solved' => 0
                                ];
                            }

                            foreach ($problemsToCreate as $problemData) {
                                QueryAPI::create('e_collection_problems', $problemData);
                            }
                        }

                        if ($isStatus2) {
                            QueryAPI::verificationCollection($id);
                        }
                    }

                    $response = [
                        'code' => 200,
                        'message' => 'Data telah disimpan'
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

        $collectionCategory = [];
        $dataCollectionCategory = QueryAPI::get("
            select
                *
            from
                e_collection_categories
            where
                collection_id = $id
        ");

        if ($dataCollectionCategory) {
            foreach ($dataCollectionCategory as $dcc) {
                $collectionCategory[] = $dcc->CATEGORY_ID;
            }
        }

        $collectionCopy = QueryAPI::get("
            select
                *
            from
                e_collections
            where
                parent_id = $id and
                deleted_at is null
        ");

        $collectionCover = QueryAPI::get("
            select
                *
            from
                catalogcovers
            where
                e_col_id = $id
        ", true);

        $collectionContent = QueryAPI::get("
            select
                *
            from
                catalogfiles
            where
                e_col_id = $id
        ", true);

        $collectionProblemHistory = QueryAPI::get("
            select
                e_collection_problems.*,
                e_problems.name as name_problem
            from
                e_collection_problems
            join
                e_problems on e_problems.id = e_collection_problems.problem_id
            where
                e_collection_problems.collection_id = $id
        ");

        $data = [
            'worksheet' => QueryAPI::get("select * from worksheets where category is not null"),
            'media' => QueryAPI::get("select * from collectionmedias where isdelete = 0 or isdelete is null"),
            'category' => QueryAPI::get("select * from e_categories where deleted_at is null"),
            'contributor' => QueryAPI::get("select * from e_contributors where show = 1 and deleted_at is null"),
            'problem' => QueryAPI::get("select * from e_problems where deleted_at is null"),
            'collection' => $collection,
            'collectionCategory' => $collectionCategory,
            'collectionContributor' => explode(';', ($collection->DESCRIPTION ?? '')),
            'collectionCopy' => $collectionCopy,
            'collectionCover' => $collectionCover,
            'collectionContent' => $collectionContent,
            'collectionProblemHistory' => $collectionProblemHistory,
            'content' => 'collection.review-detail'
        ];

        return view('layouts.index', ['data' => $data]);
    }
}
