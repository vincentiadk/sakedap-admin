<?php

namespace App\Http\Controllers\DigitalStorageHandover;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class AcceptArticleJournalController extends Controller
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
                'content' => 'digital-storage-handover.accept-article-journal',
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
            'e.id',
            null,
            'penerbit.name',
            'catalogs.title',
            'collectionmedias.name',
            'catalogs.isbn',
            'e.created_at',
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
                e.deleted_at is null
            ) and
            worksheets.category = '$this->worksheetCategory' 
            and article_title is not null
        ";

        if ($request->title) {
            $title = strtoupper($request->title);
            $whereCondition[] = "upper(e.article_title) like '%$title%'";
        }

        if ($request->executor_id) {
            $whereCondition[] = "e.penerbit_id = $request->executor_id";
        }

        if ($request->province_id) {
            $whereCondition[] = "kabupaten.propinsiid = $request->province_id";
        }

        if ($request->year) {
            $whereCondition[] = "e.publishyear = $request->year";
        }
        $whereCondition[] = "e.collection_media_id = 203";
        /*if ($request->media_id) {
            $whereCondition[] = "e.collection_media_id = $request->media_id";
        }*/

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = Carbon::parse($explodeDate[1])->format('Y-m-d');

            $whereCondition[] = "(e.received_at >= to_date('$startDate', 'YYYY-MM-DD') and e.received_at < to_date('$endDate', 'YYYY-MM-DD') + 1)";
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
                e_collections e
            left join
                catalogs on catalogs.edeposit_col_id = e.id
            left join
                worksheets on worksheets.id = e.worksheet_id
            where
                (
                   e.deleted_at is null
                ) and
                worksheets.category = '$this->worksheetCategory'
        ", true)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select
                count(*) as total
            from
                e_collections e
            left join
                catalogs on catalogs.edeposit_col_id = e.id
            left join
                penerbit on penerbit.id = e.penerbit_id
            left join
                kabupaten on kabupaten.id = e.kabupaten_id
            left join
                worksheets on worksheets.id = e.worksheet_id
            left join
                collectionmedias on collectionmedias.id = e.collection_media_id
            $whereClause
        ", true)->TOTAL ?? 0;
        $sql = "
            select
                *
            from (
                    select
                        rownum as rnum,
                        data.*
                    from
                        (
                            select 
                                e.id,
                                catalogs.id as cat_id,
                                catalogs.title,
                                catalogs.isbn,
                                e.created_at, e.title as judul_jurnal,
                                e.penerbit_id, e.code,
                                penerbit.name as name_penerbit,
                                collectionmedias.name as name_media,e.edition, e.serial,
                                e.received_at as received_at_e_collection,
                                e.article_title, e.article_contributor, e.article_subject, 
                                e.article_original_link, e.article_doi, e.description,
                                e.article_publish_date, e.deposit
                            from
                                e_collections e
                            left join
                                catalogs on e.id = catalogs.edeposit_col_id
                            left join
                                penerbit on penerbit.id = e.penerbit_id
                            left join
                                kabupaten on kabupaten.id = e.kabupaten_id
                            left join
                                worksheets on worksheets.id = e.worksheet_id
                            left join
                                collectionmedias on collectionmedias.id = e.collection_media_id
                            $whereClause
                            $orderBy
                        ) data
                    where
                        rownum <= $length
                )
            where
                rnum > $start
        ";
        
        $queryData = QueryAPI::get($sql);
        
        if ($queryData) {
            foreach ($queryData as $val) {
                $action = '
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm" onclick="showDetail(' . $val->ID . ')">
                        <i class="ph-info me-1"></i>
                        Detail
                    </a>
                    <a href="javascript:void(0);" class="btn btn-danger btn-sm mt-1 text-nowrap" onclick="destroy(' . $val->ID . ')">
                        <i class="ph-trash me-1"></i>
                        Hapus
                    </a>
                    <a href="javascript:void(0);" class="btn btn-success btn-sm mt-1 text-nowrap" onclick="verifikasi(' . $val->ID . ')">
                        <i class="ph-check me-1"></i>
                        Verifikasi
                    </a>
                ';

                $data[] = [
                    $start + 1,
                    $action,
                    $val->PENERBIT_ID . ' | ' . $val->NAME_PENERBIT,
                    $val->CAT_ID,
                    $val->ARTICLE_TITLE,
                    $val->ARTICLE_CONTRIBUTOR,
                    $val->ARTICLE_SUBJECT,
                    $val->ARTICLE_PUBLISH_DATE,
                    $val->JUDUL_JURNAL,
                    $val->NAME_MEDIA,
                    $val->ISBN,
                    $val->EDITION,
                    Carbon::parse($val->RECEIVED_AT_E_COLLECTION)->isoFormat('dddd, D MMMM Y'),
                ];

                $start++;
            }
        }
        //Log::info($data);
        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ]);
    }

    public function detail(Request $request)
    {
        $id = $request->id;
        $data = QueryAPI::get("
        select ec.*, u.fullname as createbyname,
        p.name as penerbitname 
        from e_collections ec 
        left join users u on u.id = ec.created_by 
        left join penerbit p on p.id = ec.penerbit_id
        where ec.id = {$id}", true);
        if($data) {
            return response()->json([
                'code' => 200,
                'data' => $data
            ]);
        } else {
            return response()->json([
                'code' => 400,
                'message' => "error mengambil data"
            ]);
        }
       
    }

    public function verification(Request $request)
    {
        $id = $request->id;
        $verifikasi  = QueryAPI::verificationCollection($id, session('username'));
        if($verifikasi) {
            return response()->json([
                    'code' => 200,
                    'message' => 'Sukses diverifikasi'
                ], 200);
        } else {
            return response()->json([
                'code' => 404,
                'message' => 'Gagal diverifikasi'
            ], 404);
        }
    }

    public function destroyData(Request $request)
    {
        $id = $request->id;
        
        $idCat = QueryAPI::get("
            SELECT id FROM catalogs WHERE edeposit_col_id = {$id}
        ", true);
        
        $idFile = QueryAPI::get("
            SELECT id FROM catalogfiles WHERE e_col_id = {$id}
        ", true);

        try {
            QueryAPI::delete('e_collections', $id);
            if($idCat) {
                QueryAPI::delete('catalogs', $idCat->ID);
            }
            if($idFile) {
                QueryAPI::removeFile([
                    'type' => 'konten_digital',
                    'id' => $idFile->ID,
                ]);
                QueryAPI::delete('catalogfiles', $idFile->ID);
            }
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
