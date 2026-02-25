<?php

namespace App\Http\Controllers\DigitalStorageHandover;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    private $worksheetCategory;

    public function __construct()
    {
        $this->worksheetCategory = Main::COLLECTION_DIGITAL;
    }

    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'media' => QueryAPI::get("select * from collectionmedias where (isdelete = 0 or isdelete is null) and worksheet_id in (20,142)") ?? [],
                'content' => 'digital-storage-handover.review',
                'plugins' => [
                    'datatable',
                    'daterangepicker',
                    'select2',
                ]
            ]
        ]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'e_collections.id',
            null,
            'e_collections.review_by',
            'penerbit.name',
            'e_collections.title',
            'collectionmedias.name',
            'e_collections.code',
            'e_collections.created_at',
            'e_collections.updated_at',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 10);

        $data = [];
        $search = strtoupper($request->search['value']);

        $orderBy = '';
        $order = $request->order;

        $whereClause = '';
        $whereCondition[] = "(e_collections.status = '1' and e_collections.deleted_at is null)";
        $whereCondition[] = "e_collections.worksheet_id = 20";

        if ($request->title) {
            $title = strtoupper($request->title);
            $whereCondition[] = "(upper(e_collections.title_ori) like '%$title%' or upper(e_collections.title) like '%$title%')";
        }

        if ($request->isbn) {
            $isbn = str_replace('-', '', $request->isbn);
            $whereCondition[] = "e_collections.code = '$isbn'";
        }

        if ($request->qrcbn) {
            $whereCondition[] = "e_collections.qrcbn = '$request->qrcbn'";
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

        if ($request->media_id) {
            $whereCondition[] = "e_collections.collection_media_id = $request->media_id";
        }

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $whereCondition[] = "(e_collections.updated_at >= to_date('$startDate', 'YYYY-MM-DD') and e_collections.updated_at < to_date('$endDate', 'YYYY-MM-DD') + 1)";
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
                e_collections
            where
                (status = '1' and deleted_at is null) and
                worksheet_id = 20
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                e_collections
            left join
                penerbit on penerbit.id = e_collections.penerbit_id
            left join
                kabupaten on kabupaten.id = e_collections.kabupaten_id
            left join
                collectionmedias on collectionmedias.id = e_collections.collection_media_id
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
                                penerbit.id as id_penerbit,
                                penerbit.name as name_penerbit,
                                collectionmedias.name as name_media
                            from
                                e_collections
                            left join
                                penerbit on penerbit.id = e_collections.penerbit_id
                            left join
                                kabupaten on kabupaten.id = e_collections.kabupaten_id
                            left join
                                collectionmedias on collectionmedias.id = e_collections.collection_media_id
                            left join
                                worksheets on worksheets.id = e_collections.worksheet_id
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
                $disabled = '';

                if (!Main::isSuperAdmin()) {
                    if (!empty($val->REVIEW_BY)) {
                        if ($val->REVIEW_BY !== session('username')) {
                            $disabled = 'disabled';
                        }
                    }
                }

                $action = '
                    <a href="' . url('digital-storage-handover/review/detail/' . $val->ID) . '" class="btn btn-primary btn-sm ' . $disabled . '">
                        <i class="ph-check-square-offset me-1"></i>
                        Tinjau
                    </a>
                ';

                $data[] = [
                    $start + 1,
                    $action,
                    $val->REVIEW_BY,
                    $val->ID_PENERBIT . ' | ' . $val->NAME_PENERBIT,
                    ($val->TITLE ?? $val->TITLE_ORI),
                    $val->NAME_MEDIA,
                    $val->CODE,
                    Carbon::parse($val->CREATED_AT)->isoFormat('dddd, D MMMM Y'),
                    Carbon::parse($val->UPDATED_AT)->isoFormat('dddd, D MMMM Y'),
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
        $sqlCollection = "
            select
                ec.*,
                penerbit.id as id_penerbit,
                penerbit.name as name_penerbit,
                kabupaten.namakab as namakab,
                propinsi.namapropinsi as namapropinsi,
                parents.title as title_parent,
                ccr.id as id_catalogcovers,
                ccr.fileurl as fileurl_catalogcovers,
                ccr.hash as hash_catalogcovers,
                ccr.mime as mime_catalogcovers,
                ccr.file_size as file_size_catalogcovers,
                ccr.method as method_catalogcovers,
                cfr.id as id_catalogfiles,
                cfr.fileurl as fileurl_catalogfiles,
                cfr.hash as hash_catalogfiles,
                cfr.mime as mime_catalogfiles,
                cfr.file_size as file_size_catalogfiles,
                cfr.method as method_catalogfiles
            from
                e_collections ec
            left join
                penerbit on penerbit.id = ec.penerbit_id
            left join
                kabupaten on kabupaten.id = ec.kabupaten_id
            left join
                propinsi on propinsi.id = kabupaten.propinsiid
            left join
                e_collections parents on parents.id = ec.parent_id
            left join
                (
                    select
                        cf.e_col_id, cf.id, cf.fileurl, cf.hash, cf.mime, cf.file_size, cf.method,
                        row_number() over (partition by cf.e_col_id order by cf.id desc) as rn
                    from
                        catalogfiles cf
                ) cfr on cfr.e_col_id = ec.id and cfr.rn = 1
            left join
                (
                    select
                        cc.e_col_id, cc.id, cc.fileurl, cc.hash, cc.mime, cc.file_size, cc.method,
                        row_number() over (partition by cc.e_col_id order by cc.id desc) as rn
                    from
                        catalogcovers cc
                ) ccr on ccr.e_col_id = ec.id and ccr.rn = 1
            where
                ec.id = $id and
                ec.deleted_at is null and
                ec.status = '1' and
                ec.worksheet_id = 20
        ";

        $collection = QueryAPI::get($sqlCollection, true);

        if (!$collection) {
            abort(404);
        }

        $reviewBy = $collection->REVIEW_BY ?? null;

        if (!empty($reviewBy)) {
            if (!Main::isSuperAdmin()) {
                if ($reviewBy != session('username')) {
                    echo '
                        <script>
                            alert("Koleksi sedang di tinjau oleh ' . $reviewBy . '");
                            window.location.href = "' . url('digital-storage-handover/review') . '";
                        </script>
                    ';

                    exit();
                }
            }
        } else {
            QueryAPI::update('e_collections', $collection->ID, [
                'review_by' => session('username')
            ]);

            $collection = QueryAPI::get($sqlCollection, true);
        }

        if (!$collection) {
            abort(404);
        }

        if ($request->ajax()) {
            $param = $request->param;

            if ($request->param == 'cancel-review') {
                QueryAPI::update('e_collections', $collection->ID, [
                    'review_by' => null
                ]);

                return response()->json([
                    'code' => 200,
                    'message' => 'Peninjauan berhasil dibatalkan'
                ]);
            }

            if (in_array($request->status, [3, 5])) {
                $validation = Validator::make($request->all(), [
                    'status' => 'required',
                ], [
                    'status.required' => 'Status tidak boleh kosong',
                ]);
            } else {
                $validation = Validator::make($request->all(), [
                    'worksheet_id' => 'required',
                    'city_id' => 'required',
                    'title' => 'required',
                    'collection_media_id' => 'required',
                    'received_at' => 'required',
                ], [
                    'worksheet_id.required' => 'Jenis bahan tidak boleh kosong',
                    'city_id.required' => 'Kota tidak boleh kosong',
                    'title.required' => 'Judul tidak boleh kosong',
                    'collection_media_id.required' => 'Media tidak boleh kosong',
                    'received_at.required' => 'Tanggal terima tidak boleh kosong',
                ]);
            }

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
                        'title_ori' => $request->title,
                        'album' => $request->album,
                        'slug' => Str::slug($request->title, '-'),
                        'series' => $request->series,
                        'serial' => $request->serial,
                        'publication_month' => date('m', strtotime($request->publish_time)),
                        'publication_year' => date('Y', strtotime($request->publish_time)),
                        'publication_day' => date('d', strtotime($request->publish_time)),
                        'preview' => $request->preview,
                        'physical_description' => json_encode($request->physical_description),
                        'price' => str_replace([',', '.'], '', $request->price),
                        'worksheet_id' => $request->worksheet_id,
                        'collection_media_id' => $request->collection_media_id,
                        'kabupaten_id' => $request->city_id,
                        'title' => $request->title,
                        'jilid' => $request->binding,
                        'currency' => $request->currency,
                        'jenis_isi' => $request->content_type,
                        'jenis_wadah' => $request->container_type,
                        'jenis_media' => $request->media_type,
                        'description' => $request->description,
                        'author' => implode(';', ($request->author ?? [])),
                        'kelas_besar_id' => $request->big_class_id,
                        'edition' => $request->edition,
                        'edition_date' => date('Y-m-d H:i:s', strtotime($request->edition_date)),
                        'qrcbn' => $request->qrcbn,
                    ];

                    if ($request->category && is_array($request->category)) {
                        $categoryData = [];

                        foreach ($request->category as $categoryId) {
                            $categoryData[] = [
                                'collection_id' => $id,
                                'category_id' => $categoryId
                            ];
                        }

                        foreach ($categoryData as $data) {
                            QueryAPI::create('e_collection_categories', $data);
                        }
                    }

                    if ($param == 'save-verification') {
                        $updateData['status'] = $status;

                        if ($isStatus2) {
                            $updateData['deposit'] = Main::generateNumberDeposit(
                                $request->worksheet_id,
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
                            $updateData['revision_count'] = ($revisionCount ?: 0) + 1;
                            $updateData['problem'] = $request->problem;
                            $updateData['rejected_at'] = date('Y-m-d H:i:S');
                        }

                        if ($isStatus5) {
                            $updateData['reject'] = $request->reject;
                            $updateData['rejected_at'] = date('Y-m-d H:i:S');
                        }
                    }

                    $updateCollection = QueryAPI::update('e_collections', $id, $updateData);

                    if ($updateCollection) {
                        if (($isStatus3 && $request->collection_problem) || $param == 'save') {
                            $problemsToCreate = [];

                            if ($request->collection_problem && is_array($request->collection_problem)) {
                                foreach ($request->collection_problem as $cp) {
                                    $problemsToCreate[] = [
                                        'problem_id' => $cp,
                                        'collection_id' => $id,
                                        'solved' => 0
                                    ];
                                }
                            }

                            foreach ($problemsToCreate as $problemData) {
                                QueryAPI::create('e_collection_problems', $problemData);
                            }
                        }

                        if ($isStatus2 && $param == 'save-verification') {
                            QueryAPI::verificationCollection($id);
                        }
                    }

                    $response = [
                        'code' => 200,
                        'message' => 'Data telah ' . ($param == 'save-verification' ? 'diverifikasi' : 'disimpan')
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

        $collectionProblemHistory = QueryAPI::get("
            select
                e_collection_problems.*,
                e_problems.name as name_problem
            from
                e_collection_problems
            left join
                e_problems on e_problems.id = e_collection_problems.problem_id
            where
                e_collection_problems.collection_id = $id
        ");

        return view('layouts.index', [
            'data' => [
                'worksheet' => QueryAPI::get("select * from worksheets where category = '$this->worksheetCategory'") ?? [],
                'media' => QueryAPI::get("select * from collectionmedias where (isdelete = 0 or isdelete is null) and worksheet_id in (20,142)") ?? [],
                'category' => QueryAPI::get("select * from e_categories where deleted_at is null") ?? [],
                'problem' => QueryAPI::get("select * from e_problems where deleted_at is null") ?? [],
                'contentType' => QueryAPI::get("select * from fieldrefs where tag = '336'") ?? [],
                'containerType' => QueryAPI::get("select * from fieldrefs where tag = '337'") ?? [],
                'mediaType' => QueryAPI::get("select * from fieldrefs where tag = '338'") ?? [],
                'bigClass' => QueryAPI::get("select * from master_kelas_besar") ?? [],
                'collection' => $collection,
                'collectionCategory' => $collectionCategory,
                'collectionContributor' => explode(';', ($collection->AUTHOR ?? '')),
                'collectionCopy' => $collectionCopy,
                'collectionProblemHistory' => $collectionProblemHistory,
                'physicalDescription' => json_decode($collection->PHYSICAL_DESCRIPTION ?? ''),
                'content' => 'digital-storage-handover.review-detail',
                'plugins' => [
                    'select2',
                    'daterangepicker',
                    'datatable',
                    'epubjs',
                    'videojs',
                    'pdfjs',
                    'howlerjs',
                ]
            ]
        ]);
    }
}
