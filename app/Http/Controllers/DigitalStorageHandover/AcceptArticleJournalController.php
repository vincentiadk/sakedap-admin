<?php

namespace App\Http\Controllers\DigitalStorageHandover;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

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
                    'epubjs',
                    'videojs',
                    'pdfjs',
                    'howlerjs',
                ]
            ]
        ]);
    }

    public function datatable(Request $request)
    {
        $column = [
            'e.id', null, 'penerbit.name', 'catalogs.title',
            'collectionmedias.name', 'catalogs.isbn', 'e.created_at',
        ];

        $draw   = intval($request->draw ?? 0);
        $offset = intval($request->start ?? 0);
        $limit  = intval($request->length ?? 10);
        $rowEnd = $offset + $limit;

        // ─── Build WHERE ─────────────────────────────────────────────────────
        $conditions = [
            "e.deleted_at IS NULL",
            "worksheets.category = '" . $this->escStr($this->worksheetCategory) . "'",
            "e.article_title IS NOT NULL",
            "e.collection_media_id = 203",
        ];

        if ($request->filled('title')) {
            $conditions[] = "upper(e.article_title) LIKE '%" . $this->escStr(strtoupper($request->title)) . "%'";
        }

        if ($request->filled('executor_id') && is_numeric($request->executor_id)) {
            $conditions[] = "e.penerbit_id = " . $this->escInt($request->executor_id);
        }

        if ($request->filled('province_id') && is_numeric($request->province_id)) {
            $conditions[] = "kabupaten.propinsiid = " . $this->escInt($request->province_id);
        }

        if ($request->filled('year') && is_numeric($request->year)) {
            $conditions[] = "e.publication_year = " . $this->escInt($request->year);
        }

        if ($request->filled('received_by') && is_numeric($request->received_by)) {
            $conditions[] = "e.received_by = " . $this->escInt($request->received_by);
        }

        if ($request->filled('is_need_verify')) {
            $val = $request->is_need_verify;
            if ($val === '1') {
                $conditions[] = "(e.is_need_verify = 1 OR e.catalog_id IS NULL)";
            } elseif (is_numeric($val)) {
                $conditions[] = "e.is_need_verify = " . $this->escInt($val);
            }
        }

        $allowedDateTypes = ['e.created_at', 'e.received_at', 'e.article_publish_date'];
        if ($request->filled('date') && $request->filled('date_type')
            && in_array($request->date_type, $allowedDateTypes, true)) {

            [$startDate, $endDate] = explode(' - ', $request->date);
            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate   = Carbon::parse($endDate)->format('Y-m-d');

            $conditions[] = "({$request->date_type} >= TO_DATE('$startDate','YYYY-MM-DD')
                            AND {$request->date_type} < TO_DATE('$endDate','YYYY-MM-DD') + 1)";
        }

        $search = strtoupper($request->input('search.value', ''));
        if ($search) {
            $safesearch = $this->escStr($search);
            $terms = [];
            foreach ($column as $c) {
                if ($c) $terms[] = "upper($c) LIKE '%$safesearch%'";
            }
            $conditions[] = '(' . implode(' OR ', $terms) . ')';
        }

        $whereClause = 'WHERE ' . implode(' AND ', $conditions);

        // ─── ORDER BY ────────────────────────────────────────────────────────
        $orderBy = 'ORDER BY e.created_at DESC';
        if ($request->filled('order')) {
            $idx = (int) $request->input('order.0.column');
            $dir = strtoupper($request->input('order.0.dir')) === 'ASC' ? 'ASC' : 'DESC';
            if (isset($column[$idx]) && $column[$idx]) {
                $orderBy = "ORDER BY {$column[$idx]} $dir";
            }
        }

        // ─── JOIN ────────────────────────────────────────────────────────────
        $joins = "
            FROM e_collections e
            LEFT JOIN catalogs         ON e.id = catalogs.edeposit_col_id
            LEFT JOIN penerbit         ON penerbit.id = e.penerbit_id
            LEFT JOIN kabupaten        ON kabupaten.id = e.kabupaten_id
            LEFT JOIN worksheets       ON worksheets.id = e.worksheet_id
            LEFT JOIN collectionmedias ON collectionmedias.id = e.collection_media_id
            LEFT JOIN users u          ON u.id = e.received_by
        ";

        // ─── totalData ───────────────────────────────────────────────────────
        $wsCategory = $this->worksheetCategory;
        $totalData = Cache::remember("datatable_total_{$wsCategory}", 300, function () use ($wsCategory) {
            return QueryAPI::get("
                SELECT COUNT(*) AS total
                FROM e_collections e
                LEFT JOIN worksheets ON worksheets.id = e.worksheet_id
                WHERE e.deleted_at IS NULL
                AND worksheets.category = '$wsCategory'
            ", true)->TOTAL ?? 0;
        });

        // ─── totalFiltered ───────────────────────────────────────────────────
        $totalFiltered = QueryAPI::get("
            SELECT COUNT(*) AS total $joins $whereClause
        ", true)->TOTAL ?? 0;

        // ─── Data aktual ─────────────────────────────────────────────────────
        $sql = "
            SELECT * FROM (
                SELECT rownum AS rnum, data.*
                FROM (
                    SELECT
                        e.id, e.is_need_verify,
                        catalogs.id AS cat_id, catalogs.title, catalogs.isbn,
                        e.created_at, e.title AS judul_jurnal,
                        e.penerbit_id, e.code,
                        penerbit.name AS name_penerbit,
                        collectionmedias.name AS name_media,
                        e.edition, e.serial,
                        e.received_at AS received_at_e_collection,
                        e.article_title, e.article_contributor, e.article_subject,
                        e.article_original_link, e.article_doi, e.description,
                        e.article_publish_date, e.deposit, e.edition_date,
                        u.fullname AS received_by_name, e.volume
                    $joins
                    $whereClause
                    $orderBy
                ) data
                WHERE rownum <= $rowEnd
            )
            WHERE rnum > $offset
        ";

        $queryData = QueryAPI::get($sql);

        // ─── Format output ───────────────────────────────────────────────────
        $data  = [];
        $rowNo = $offset;

        foreach ($queryData ?? [] as $val) {
            $rowNo++;
            $canDelete = Carbon::parse($val->CREATED_AT)->diffInDays(now()) <= 7;
            $needVerif = $val->IS_NEED_VERIFY === '1' || trim($val->CAT_ID ?? '') === '';

            $action = '<a href="javascript:void(0);" class="btn btn-primary btn-sm"
                        onclick="showDetail(' . $val->ID . ')">
                        <i class="ph-info me-1"></i> Detail</a>';

            if ($canDelete) {
                $action .= '<a href="javascript:void(0);" class="btn btn-danger btn-sm mt-1 text-nowrap"
                            onclick="destroy(' . $val->ID . ')">
                            <i class="ph-trash me-1"></i> Hapus</a>';
            }

            $action .= $needVerif
                ? '<a href="javascript:void(0);" class="btn btn-warning btn-sm mt-1 text-nowrap"
                    onclick="verifikasi(' . $val->ID . ')">
                    <i class="ph-warning me-1"></i> Perlu Verifikasi Ulang</a>'
                : '<a href="javascript:void(0);" class="btn btn-success btn-sm mt-1 text-nowrap"
                    onclick="verifikasi(' . $val->ID . ')">
                    <i class="ph-check me-1"></i> Verifikasi</a>';

            $data[] = [
                $rowNo,
                $action,
                $val->PENERBIT_ID . ' | ' . $val->NAME_PENERBIT,
                $val->CAT_ID,
                $val->ARTICLE_TITLE,
                $val->ARTICLE_CONTRIBUTOR,
                $val->ARTICLE_SUBJECT,
                $val->EDITION_DATE,
                $val->JUDUL_JURNAL,
                $val->NAME_MEDIA,
                $val->ISBN,
                $val->VOLUME,
                Carbon::parse($val->RECEIVED_AT_E_COLLECTION)->isoFormat('dddd, D MMMM Y'),
                $val->RECEIVED_BY_NAME,
            ];
        }

        return response()->json([
            'draw'            => $draw,
            'recordsTotal'    => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data'            => $data,
        ]);
    }

    private function escStr($val): string
    {
        return str_replace("'", "''", (string) $val);
    }

    private function escInt($val): int
    {
        return (int) $val;
    }
    public function detail(Request $request)
    {
        $id = $request->id;
        $sql = "select ec.*, 
                TO_CHAR(ec.edition_date, 'YYYY-MM-DD') as edition_date_formatted,
                TO_CHAR(ec.received_at, 'YYYY-MM-DD') as received_at_formatted,
                TO_CHAR(ec.created_at, 'YYYY-MM-DD') as created_at_formatted,
                u.fullname as createbyname,
                p.name as penerbitname,
                k.namakab as kotaterbit,
                pro.namapropinsi as propinsiterbit,
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
                cfr.method as method_catalogfiles,
                co.noinduk_deposit     
                from e_collections ec 
                left join users u on u.id = ec.created_by 
                left join penerbit p on p.id = ec.penerbit_id
                left join kabupaten k on ec.kabupaten_id = k.id
                left join propinsi pro on pro.id = k.propinsiid
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
                left join collections co on co.edeposit_col_id = ec.id
                where ec.id = '{$id}'";

        $data = QueryAPI::get($sql,true);
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
            $update = QueryAPI::update('E_COLLECTIONS', $id, [
                        'is_need_verify' => 0,
                        'updated_by' => session('id')
            ], true);
            if($update) {
                return response()->json([
                        'code' => 200,
                        'message' => 'Sukses diverifikasi'
                ], 200);
            }
        } 
        return response()->json([
                'code' => 404,
                'message' => 'Gagal diverifikasi'
            ], 404);
       
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
                $idCols = QueryAPI::get("
                    SELECT id FROM collections WHERE catalog_id = " . 
                    $idCat->ID);
                if($idCols) {
                    foreach($idCols as $idCol){
                        QueryAPI::delete('collections', $idCol->ID);
                    }
                }
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
            Log::error('Hapus artikel jurnal gagal', [
                'message' => $e->getMessage(),
            ]);
            $response = [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
        }

        return response()->json($response);
    }

    public function updateInlineField(Request $request)
    {
        try {
            $id = $request->id;
            $field = $request->field;
            $value = $request->value;

            if (!$id || !$field) {
                throw new \Exception('Parameter tidak lengkap');
            }
            $allowed = [
                'article_title',
                'article_contributor',
                'article_subject',
                'article_doi',
                'article_file_link',
                'article_original_link',
                'article_abstract',
                'edition_date',
                'title',
                'code',
                'volume',
                'city_id'
            ];

            if (!in_array($field, $allowed)) {
                throw new \Exception('Field tidak diizinkan');
            }
            if ($field === 'edition_date' && $value) {
                $value = date('Y-m-d', strtotime($value));
            }
            $ip = $request->ip();

            // update data dengan params
            $params = [
                $field => $value,
                'is_need_verify' => 1,
                'updated_by' => session('id')
            ];

            if($field == 'city_id'){
                $params = array_merge($params, [
                    'KABUPATEN_ID' => $value,
                ]);
            }
            QueryAPI::update('E_COLLECTIONS', $id, $params , true);

            return response()->json([
                'code' => 200,
                'message' => 'Berhasil disimpan'
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'code' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }
}
