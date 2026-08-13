<?php

namespace App\Http\Controllers\DigitalStorageHandover;

use Carbon\Carbon;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    private $worksheetCategory;

    // Sama seperti bucket "% KCKR" di halaman Kepatuhan (ComplianceV3Controller),
    // supaya operator melihat pengelompokan yang konsisten di kedua tempat.
    private const PERSENTASE_BUCKETS = [
        '0-20'   => [0, 20],
        '21-40'  => [21, 40],
        '41-60'  => [41, 60],
        '61-80'  => [61, 80],
        '81-100' => [81, 100],
    ];

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
            'e_collections.jilid',
            'e_collections.series',
            'e_collections.created_at',
            'e_collections.updated_at',
            'penerbit.is_lock',
        ];

        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 10);

        $data = [];
        $search = strtoupper($request->search['value'] ?? '');

        // 1. SET DEFAULT ORDER BY
        $orderBy = 'order by e_collections.id desc';
        $order = $request->order;

        $whereClause = '';
        $whereCondition = [];
        $whereCondition[] = "(e_collections.status = '1' and e_collections.deleted_at is null)";
        $whereCondition[] = "e_collections.worksheet_id = 20";

        if ($request->title) {
            $title = strtoupper(str_replace("'", "''", $request->title));
            $whereCondition[] = "(upper(e_collections.title_ori) like '%$title%' or upper(e_collections.title) like '%$title%')";
        }

        if ($request->isbn) {
            // e_collections.code itu CHAR (dipadding spasi di belakang) dan nilainya
            // masih pakai tanda hubung (mis. "978-634-7497-82-6"). Tanda hubung &
            // spasi di kolom harus ikut dibuang juga, bukan cuma dari input user —
            // kalau tidak, exact match ini tidak akan pernah cocok.
            $isbn = str_replace(['-', "'"], ['', "''"], $request->isbn);
            $whereCondition[] = "REPLACE(TRIM(e_collections.code), '-', '') = '$isbn'";
        }

        if ($request->qrcbn) {
            $qrcbn = str_replace("'", "''", $request->qrcbn);
            $whereCondition[] = "e_collections.qrcbn = '$qrcbn'";
        }

        if ($request->executor_id) {
            $whereCondition[] = "e_collections.penerbit_id = " . (int) $request->executor_id;
        }

        if ($request->province_id) {
            $whereCondition[] = "kabupaten.propinsiid = " . (int) $request->province_id;
        }

        if ($request->year) {
            $whereCondition[] = "e_collections.publication_year = " . (int) $request->year;
        }

        if ($request->media_id) {
            $whereCondition[] = "e_collections.collection_media_id = " . (int) $request->media_id;
        }

        if ($request->review_by) {
            $rb = strtoupper(str_replace("'", "''", $request->review_by));
            $whereCondition[] = "upper(e_collections.review_by) like '%$rb%'";
        }

        if ($request->status_penerbit !== null && $request->status_penerbit !== '') {
            if ($request->status_penerbit == '1') {
                $whereCondition[] = "penerbit.is_lock = 1";
            } else {
                $whereCondition[] = "(penerbit.is_lock IS NULL OR penerbit.is_lock != 1)";
            }
        }

        // Peta persentase-per-penerbit yang di-cache (10 menit) — SATU-SATUNYA
        // sumber % KCKR di halaman ini, dipakai baik buat mewarnai baris maupun
        // buat filter bucket. Tidak ada query agregat ke PENERBIT_ISBN yang
        // dijalankan live per request lagi (lihat cabang $pctBucket di bawah).
        $pctMap = $this->fetchPersentaseKckrMap();

        $pctBucket = null;
        if ($request->persentase_kckr && isset(self::PERSENTASE_BUCKETS[$request->persentase_kckr])) {
            $pctBucket = self::PERSENTASE_BUCKETS[$request->persentase_kckr];
        }

        if ($request->date) {
            $explodeDate = explode(' - ', $request->date);
            $startDate = \Carbon\Carbon::parse($explodeDate[0])->format('Y-m-d');
            $endDate = \Carbon\Carbon::parse($explodeDate[1])->format('Y-m-d');

            $whereCondition[] = "(e_collections.updated_at >= to_date('$startDate', 'YYYY-MM-DD') and e_collections.updated_at < to_date('$endDate', 'YYYY-MM-DD') + 1)";
        }

        if ($search) {
            $searchEsc = str_replace("'", "''", $search);
            $terms = [];

            foreach ($column as $c) {
                if ($c) {
                    $terms[] = "upper($c) like '%$searchEsc%'";
                }
            }

            $whereCondition[] = '(' . implode(' or ', $terms) . ')';
        }

        if ($whereCondition) {
            $whereClause = "where " . implode(' and ', $whereCondition);
        }

        // 2. TIMPA ORDER BY JIKA ADA REQUEST SORT DARI DATATABLE
        // $sortColumn/$sortDir disimpan terpisah (bukan cuma di dalam string
        // $orderBy) karena jalur filter % KCKR di bawah butuh keduanya buat
        // sorting di PHP, bukan di SQL.
        $sortColumn = 'e_collections.id';
        $sortDir = 'desc';
        if ($order && isset($column[$order[0]['column']]) && !empty($column[$order[0]['column']])) {
            $sortColumn = $column[$order[0]['column']];
            $sortDir = strtolower($order[0]['dir']) === 'asc' ? 'asc' : 'desc';
            // Gunakan e_collections.id desc di belakang sebagai tie-breaker
            $orderBy = "order by $sortColumn $sortDir, e_collections.id desc";
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

        if ($pctBucket !== null) {
            // ── Jalur filter % KCKR ──────────────────────────────────────────
            // TIDAK ada query agregat ke PENERBIT_ISBN di sini sama sekali.
            // Ambil cuma id + penerbit_id + nilai sort (ringan, ~1 detik untuk
            // puluhan ribu baris), saring & urutkan di PHP pakai $pctMap yang
            // sudah di-cache, baru ambil detail lengkap utuk halaman yang mau
            // ditampilkan saja (IN-list pendek, aman dari limit panjang URL).
            [$min, $max] = $pctBucket;

            $lightRows = QueryAPI::get("
                select
                    e_collections.id as id,
                    penerbit.id as penerbit_id,
                    $sortColumn as sort_val
                from
                    e_collections
                left join
                    penerbit on penerbit.id = e_collections.penerbit_id
                left join
                    kabupaten on kabupaten.id = e_collections.kabupaten_id
                left join
                    collectionmedias on collectionmedias.id = e_collections.collection_media_id
                $whereClause
            ") ?? [];

            $matched = array_values(array_filter($lightRows, function ($r) use ($pctMap, $min, $max) {
                $pid = $r->PENERBIT_ID ? (int) $r->PENERBIT_ID : null;
                if ($pid === null || !array_key_exists($pid, $pctMap)) {
                    return false;
                }
                $pct = $pctMap[$pid];
                return $pct >= $min && $pct <= $max;
            }));

            usort($matched, function ($a, $b) use ($sortDir) {
                $cmp = $a->SORT_VAL <=> $b->SORT_VAL;
                return $sortDir === 'asc' ? $cmp : -$cmp;
            });

            $totalFiltered = count($matched);
            $pageIds = array_map(fn($r) => (int) $r->ID, array_slice($matched, $start, $length - $start));

            $queryData = [];
            if (!empty($pageIds)) {
                $idsCsv = implode(',', $pageIds);
                $pageRows = QueryAPI::get("
                    select
                        e_collections.*,
                        penerbit.id as id_penerbit,
                        penerbit.name as name_penerbit,
                        penerbit.is_lock as penerbit_is_lock,
                        collectionmedias.name as name_media
                    from
                        e_collections
                    left join
                        penerbit on penerbit.id = e_collections.penerbit_id
                    left join
                        collectionmedias on collectionmedias.id = e_collections.collection_media_id
                    where
                        e_collections.id in ($idsCsv)
                ") ?? [];

                // IN(...) tidak menjamin urutan hasil — susun ulang sesuai $pageIds.
                $byId = [];
                foreach ($pageRows as $r) {
                    $byId[(int) $r->ID] = $r;
                }
                foreach ($pageIds as $id) {
                    if (isset($byId[$id])) {
                        $queryData[] = $byId[$id];
                    }
                }
            }
        } else {
            // ── Jalur normal (tanpa filter % KCKR) — sama seperti sebelumnya ──
            // 3. HAPUS JOIN KE WORKSHEETS
            $totalFiltered = QueryAPI::get("
                select
                    count(e_collections.id) as total
                from
                    e_collections
                left join
                    penerbit on penerbit.id = e_collections.penerbit_id
                left join
                    kabupaten on kabupaten.id = e_collections.kabupaten_id
                left join
                    collectionmedias on collectionmedias.id = e_collections.collection_media_id
                $whereClause
            ", true)->TOTAL ?? 0;

            // 4. HAPUS JOIN KE WORKSHEETS PADA QUERY UTAMA
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
                                    penerbit.is_lock as penerbit_is_lock,
                                    collectionmedias.name as name_media
                                from
                                    e_collections
                                left join
                                    penerbit on penerbit.id = e_collections.penerbit_id
                                left join
                                    kabupaten on kabupaten.id = e_collections.kabupaten_id
                                left join
                                    collectionmedias on collectionmedias.id = e_collections.collection_media_id
                                $whereClause
                                $orderBy
                            ) data
                        where
                            rownum <= $length
                    )
                where
                    rnum > $start
            ");
        }

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

                $isLock = $val->PENERBIT_IS_LOCK ?? null;
                $statusBadge = ($isLock == 1)
                    ? '<span class="badge bg-danger bg-opacity-10 text-danger">Blokir</span>'
                    : '<span class="badge bg-success bg-opacity-10 text-success">Aktif</span>';

                // % KCKR penerbit pemilik koleksi ini + warna barisnya. Penerbit
                // yang belum pernah punya kewajiban KCKR (tidak ada di peta) tidak
                // diwarnai — beda dari 0% (kewajiban ada, kepatuhan nol).
                $pct = null;
                $rowClass = '';
                $pctBadge = '<span class="text-muted">-</span>';
                if ($val->ID_PENERBIT && array_key_exists((int) $val->ID_PENERBIT, $pctMap)) {
                    $pct = $pctMap[(int) $val->ID_PENERBIT];
                    [$rowClass, $badgeColor] = $this->pctColor($pct);
                    $pctBadge = "<span class=\"badge bg-{$badgeColor} bg-opacity-10 text-{$badgeColor}\">{$pct}%</span>";
                }

                $data[] = [
                    'DT_RowClass' => $rowClass,
                    0  => $start + 1,
                    1  => $action,
                    2  => $val->REVIEW_BY,
                    3  => $val->ID_PENERBIT . ' | ' . $val->NAME_PENERBIT,
                    4  => ($val->TITLE ?? $val->TITLE_ORI),
                    5  => $val->NAME_MEDIA,
                    6  => $val->CODE,
                    7  => $val->JILID ?? '-',
                    8  => $val->SERIES ?? '-',
                    9  => \Carbon\Carbon::parse($val->CREATED_AT)->isoFormat('dddd, D MMMM Y'),
                    10 => \Carbon\Carbon::parse($val->UPDATED_AT)->isoFormat('dddd, D MMMM Y'),
                    11 => $statusBadge,
                    12 => $pctBadge,
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

    /**
     * Peta [penerbit_id => persentase_kckr], dihitung dengan rumus yang SAMA
     * dengan compliance:recompute-status / ComplianceV3Controller / ComplianceNotification
     * (SUDAH_KCKR / TOTAL_WAJIB * 100, TOTAL_WAJIB = SUDAH_KCKR + TAGIHAN).
     *
     * Di-cache 10 menit: ini agregat atas seluruh PENERBIT_ISBN, terlalu berat
     * untuk dihitung ulang di setiap keystroke/halaman DataTable pada laman
     * operasional yang sering dibuka ini. Staleness 10 menit cukup aman untuk
     * sekadar filter & pewarnaan baris (bukan keputusan blokir).
     */
    private function fetchPersentaseKckrMap(): array
    {
        return Cache::remember('review_persentase_kckr_map', 600, function () {
            ['join' => $isbnJoin, 'totalWajib' => $totalWajib, 'persentase' => $persentase]
                = $this->persentaseKckrFormula('P', 'PI');

            $rows = QueryAPI::get("
                SELECT P.ID AS PENERBIT_ID, $persentase AS PERSENTASE_KCKR
                FROM PENERBIT P
                $isbnJoin
                GROUP BY P.ID
                HAVING $totalWajib > 0
            ") ?? [];

            $map = [];
            foreach ($rows as $r) {
                $map[(int) $r->PENERBIT_ID] = (float) $r->PERSENTASE_KCKR;
            }

            return $map;
        });
    }

    /**
     * Rumus % KCKR yang SAMA dengan compliance:recompute-status / ComplianceV3Controller
     * / ComplianceNotification (SUDAH_KCKR / TOTAL_WAJIB * 100, TOTAL_WAJIB =
     * SUDAH_KCKR + TAGIHAN). Sumbernya cuma dipakai oleh fetchPersentaseKckrMap()
     * (satu-satunya tempat yang menjalankan agregat ke PENERBIT_ISBN, di-cache
     * 10 menit) — filter bucket di datatable() TIDAK menjalankan agregat ini
     * lagi secara live, cukup baca peta yang sudah di-cache.
     */
    private function persentaseKckrFormula(string $penerbitAlias, string $isbnAlias): array
    {
        $startDate = '2021-01-01';
        $endDate   = date('Y-m-d', strtotime('+1 day'));
        $cutoff    = '2026-01-01';

        $isPre26 = "$isbnAlias.CREATEDATE <  TO_DATE('$cutoff','YYYY-MM-DD')";
        $is2026  = "$isbnAlias.CREATEDATE >= TO_DATE('$cutoff','YYYY-MM-DD')";

        $tagihan = "SUM(CASE
            WHEN $isPre26 AND $isbnAlias.RECEIVED_DATE_KCKR IS NULL THEN 1
            WHEN $is2026  AND $isbnAlias.TANGGAL_TERBIT IS NOT NULL AND $isbnAlias.RECEIVED_DATE_KCKR IS NULL THEN 1
            ELSE 0 END)";
        $sudahKckr  = "SUM(CASE WHEN $isbnAlias.RECEIVED_DATE_KCKR IS NOT NULL THEN 1 ELSE 0 END)";
        $totalWajib = "($sudahKckr + $tagihan)";
        $persentase = "ROUND(CASE WHEN $totalWajib > 0 THEN $sudahKckr / $totalWajib * 100 ELSE 0 END, 1)";

        $join = "
            LEFT JOIN PENERBIT_ISBN $isbnAlias ON $penerbitAlias.ID = $isbnAlias.PENERBIT_ID
                AND $isbnAlias.CREATEDATE >= TO_DATE('$startDate', 'YYYY-MM-DD')
                AND $isbnAlias.CREATEDATE <  TO_DATE('$endDate', 'YYYY-MM-DD')
                AND ($isbnAlias.KETERANGAN IS NULL OR UPPER($isbnAlias.KETERANGAN) NOT LIKE '%LENGKAP%')
        ";

        return ['join' => $join, 'totalWajib' => $totalWajib, 'persentase' => $persentase];
    }

    /**
     * Warna baris + badge untuk satu nilai % KCKR, sesuai permintaan:
     * 0-20% merah, 21-40% oranye, 41-60% hijau, 61-80% biru, >80% polos.
     * @return array{0:string,1:string} [class CSS untuk <tr>, warna badge Bootstrap]
     */
    private function pctColor(float $pct): array
    {
        return match (true) {
            $pct <= 20 => ['row-pct-merah', 'danger'],
            $pct <= 40 => ['row-pct-oranye', 'warning'],
            $pct <= 60 => ['row-pct-hijau', 'success'],
            $pct <= 80 => ['row-pct-biru', 'primary'],
            default    => ['', 'secondary'],
        };
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
                            window.close();
                        </script>
                    ';

                    exit();
                }
            }
        } else {
            QueryAPI::update('e_collections', $collection->ID, [
                'review_by' => session('username'),
                'review_by_name' => session('username'),
            ], false);

            $collection = QueryAPI::get($sqlCollection, true);
        }

        if (!$collection) {
            abort(404);
        }

        if ($request->ajax()) {
            $param = $request->param;

            if ($request->param == 'cancel-review') {
                QueryAPI::update('e_collections', $collection->ID, [
                    'review_by' => null,
                    'review_by_name' => null,
                ], false);

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
                $rules = [
                    'worksheet_id'        => 'required',
                    'city_id'             => 'required',
                    'title'               => 'required',
                    'collection_media_id' => 'required',
                    'received_at'         => 'required',
                ];
                $messages = [
                    'worksheet_id.required'        => 'Jenis bahan tidak boleh kosong',
                    'city_id.required'             => 'Kota tidak boleh kosong',
                    'title.required'               => 'Judul tidak boleh kosong',
                    'collection_media_id.required' => 'Media tidak boleh kosong',
                    'received_at.required'         => 'Tanggal terima tidak boleh kosong',
                ];

                if ($collection->CODE_TYPE === '1') {
                    $rules['isbn_deposit_status']              = 'required|in:sesuai,tidak_sesuai';
                    $messages['isbn_deposit_status.required']  = 'Status ISBN wajib diisi';
                    $messages['isbn_deposit_status.in']        = 'Status ISBN tidak valid';
                }

                $validation = Validator::make($request->all(), $rules, $messages);
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

                    $publishTs      = $request->filled('publish_time') ? strtotime($request->publish_time) : null;
                    $editionDateVal = $request->filled('edition_date') ? date('Y-m-d H:i:s', strtotime($request->edition_date)) : null;

                    $updateData = [
                        'city_id' => $request->city_id,
                        'title_ori' => $request->title,
                        'album' => $request->album,
                        'slug' => Str::slug($request->title, '-'),
                        'series' => $request->series,
                        'serial' => $request->serial,
                        'publication_month' => $publishTs ? date('m', $publishTs) : null,
                        'publication_year' => $publishTs ? date('Y', $publishTs) : null,
                        'publication_day' => $publishTs ? date('d', $publishTs) : null,
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
                        'edition_date' => $editionDateVal,
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
                            if (empty($collection->DEPOSIT ?: null)) {
                                $updateData['deposit'] = Main::generateNumberDeposit();
                            }

                            $updateData['received_at'] = date('Y-m-d H:i:s', strtotime($request->received_at));
                            $updateData['received_by'] = $sessionId;
                            $updateData['received_by_name'] = session('username');
                            $updateData['validated_at'] = $currentDateTime;
                            $updateData['validated_by'] = $sessionId;
                            $updateData['validated_by_name'] = session('username');
                        } else if ($isStatus3) {
                            $updateData['revision_count'] = ($revisionCount ?: 0) + 1;
                            $updateData['problem'] = $request->problem;
                            $updateData['rejected_at'] = date('Y-m-d H:i:s');
                            $updateData['rejected_by'] = $sessionId;
                            $updateData['rejected_by_name'] = session('username');
                        } else if ($isStatus5) {
                            $updateData['reject'] = $request->reject;
                            $updateData['rejected_at'] = date('Y-m-d H:i:s');
                            $updateData['rejected_by'] = $sessionId;
                            $updateData['rejected_by_name'] = session('username');
                        } else {
                            $updateData['received_at'] = null;
                            $updateData['received_by'] = null;
                            $updateData['received_by_name'] = null;
                            $updateData['validated_at'] = null;
                            $updateData['validated_by'] = null;
                            $updateData['validated_by_name'] = null;
                            $updateData['rejected_by'] = null;
                            $updateData['rejected_by_name'] = null;
                        }
                    }

                    // Simpan status deposit ISBN jika koleksi ber-ISBN dan ada nilai yang dikirim
                    if ((int) $collection->CODE_TYPE === 1 && $request->filled('isbn_deposit_status')) {
                        $isbnNoSave = trim(preg_replace('/[\s\-]/', '', $collection->CODE ?? ''));
                        if ($isbnNoSave) {
                            $piRow = QueryAPI::get("
                                select id from penerbit_isbn
                                where REPLACE(TRIM(isbn_no), '-', '') = '$isbnNoSave'
                                  and rownum = 1
                            ", true);
                            if ($piRow && $piRow->ID) {
                                QueryAPI::update('penerbit_isbn', $piRow->ID, [
                                    'isbn_deposit_status' => $request->isbn_deposit_status,
                                ], false);
                            }
                        }
                    }

                    $updateCollection = QueryAPI::update('e_collections', $id, $updateData);

                    if (!$updateCollection) {
                        $response = ['code' => 500, 'message' => 'Gagal menyimpan data ke server'];
                    } else {
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
                            QueryAPI::verificationCollection($id, session('username'));
                        }

                    $response = [
                        'code' => 200,
                        'message' => 'Data telah ' . ($param == 'save-verification' ? 'diverifikasi' : 'disimpan')
                    ];
                    } // end if $updateCollection
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

        $isKdtValid        = false;
        $isbnDepositStatus = null;
        $penerbitIsbnId    = null;
        $isbnNo = preg_replace('/[\s\-]/', '', $collection->CODE ?? '');
        if ($isbnNo && $collection->CODE_TYPE === '1') {
            $penerbitIsbnRow = QueryAPI::get("
                select pi.id, pi.isbn_deposit_status, pt.is_kdt_valid
                from penerbit_isbn pi
                left join penerbit_terbitan pt on pt.id = pi.penerbit_terbitan_id
                where REPLACE(TRIM(pi.isbn_no), '-', '') = '$isbnNo'
                  and rownum = 1
            ", true);
            $isKdtValid        = ($penerbitIsbnRow->IS_KDT_VALID        ?? 0)    == 1;
            $isbnDepositStatus = $penerbitIsbnRow->ISBN_DEPOSIT_STATUS   ?? null;
            $penerbitIsbnId    = $penerbitIsbnRow->ID                    ?? null;
        }

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
                'isKdtValid' => $isKdtValid,
                'isbnDepositStatus' => $isbnDepositStatus,
                'penerbitIsbnId' => $penerbitIsbnId,
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
