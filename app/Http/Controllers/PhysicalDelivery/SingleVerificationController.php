<?php

namespace App\Http\Controllers\PhysicalDelivery;

use App\Helpers\Main;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Helpers\QueryAPI;
use Illuminate\Support\Facades\Log;

class SingleVerificationController extends Controller
{
    /**
     * Batas waktu pemanggilan QueryAPI. Default QueryAPI = 0 (tanpa batas),
     * sehingga request bisa menggantung selamanya saat Oracle sedang berat.
     */
    private const CONNECT_TIMEOUT = 5;
    private const QUERY_TIMEOUT = 30;

    /**
     * Ekspresi SQL untuk menormalkan kolom ISBN.
     *
     * JANGAN diubah tanpa mengukur ulang. Bentuk ini dipilih karena sudah ada
     * function-based index untuk REPLACE(isbn, '-', '') di LETTER_DETAIL dan
     * COLLECTIONS. Hasil pengukuran pada 935 ribu baris LETTER_DETAIL:
     *
     *   REPLACE(isbn, '-', '')                        = 0,04 detik  (pakai index)
     *   UPPER(isbn)                                   = 0,04 detik  (pakai index)
     *   TRIM(isbn)                                    = 0,44 detik
     *   REGEXP_REPLACE(UPPER(TRIM(isbn)), '[^0-9X]')  = 2,20 detik  (full scan)
     *
     * Membungkus kolom dengan fungsi lain -- termasuk menambah UPPER atau TRIM
     * di sekelilingnya -- membuat index tidak terpakai dan query kembali lambat.
     * Untuk menangani ISBN-10 berakhiran huruf kecil "x", varian huruf besar dan
     * kecil dicari lewat IN (...) supaya tetap memakai index.
     */
    private static function isbnNorm(string $column): string
    {
        return "REPLACE($column, '-', '')";
    }

    public function index()
    {
        $status_isbn = QueryAPI::get("SELECT * FROM MASTER_STATUS_ISBN");
        return view('layouts.index', [
            'data' => [
                'content' => 'physical-delivery.single-verification',
                'plugins' => [
                    'select2',
                    'howlerjs',
                ],
                'status_isbn' => $status_isbn
            ]
        ]);
    }

    public function search(Request $request)
    {
        $keyword = trim((string) $request->get('keyword', ''));
        $mode = strtolower(trim((string) $request->get('mode', 'auto')));
        $limit = (int) $request->get('limit', 20);

        if ($limit < 1 || $limit > 100) {
            $limit = 20;
        }

        if ($keyword === '') {
            return response()->json([
                'code' => 200,
                'message' => 'Keyword kosong',
                'data' => [],
            ]);
        }

        $keywordSafe = $this->escapeSql($keyword);
        $keywordUpper = trim(strtoupper($keywordSafe));
        $branchId = (int) session('branch_id');

        if ($mode === 'auto') {
            $isbnOnly = preg_replace('/[^0-9Xx]/', '', $keyword);

            if (
                preg_match('/^[0-9Xx\-\s]+$/', $keyword) &&
                in_array(strlen($isbnOnly), [10, 13])
            ) {
                $mode = 'isbn';
            } else {
                $mode = 'title';
            }
        }

        // Ekspresi normalisasi ISBN, dipakai seragam di base/hist/col.
        $isbnLd = self::isbnNorm('ld.isbn');
        $isbnLd2 = self::isbnNorm('ld2.isbn');
        $isbnCol = self::isbnNorm('c.isbn');

        // Hak edit: Perpusnas boleh semua, provinsi hanya kiriman ke provinsinya.
        // Ini hanya untuk menata tampilan -- penegakan sebenarnya ada di
        // updateReceivedDate(), yang memeriksa ulang ke database.
        $canEditExpr = Main::isPerpusnas()
            ? '1 = 1'
            : 'b.province_id = ' . (int) session('province_id');

        $where = " l.status <> 'DRAFT' ";

        if ($mode === 'isbn') {
            // Whitelist: ISBN hanya digit dan huruf X, sekaligus aman dari injeksi.
            $isbnKey = preg_replace('/[^0-9X]/', '', $keywordUpper);

            if ($isbnKey === '') {
                return response()->json([
                    'code' => 200,
                    'message' => 'ISBN tidak valid',
                    'mode' => $mode,
                    'count' => 0,
                    'data' => [],
                ]);
            }

            // IN dengan varian huruf besar/kecil, bukan UPPER(kolom) -- lihat isbnNorm().
            $isbnKeys = array_unique([$isbnKey, strtolower($isbnKey)]);
            $isbnList = "'" . implode("', '", $isbnKeys) . "'";

            $where .= " AND {$isbnLd} IN ({$isbnList})";
        } else {
            $where .= " AND upper(ld.title) like '%{$keywordUpper}%' ";
        }
        //$where .= " AND l.branch_id = {$branchId} ";

        // Korelasi per baris dihilangkan: riwayat ISBN dihitung sekali (hist/col),
        // lalu kontribusi baris itu sendiri dikurangi -- setara dengan
        // "ld2.letter_detail_id != ld.letter_detail_id" pada versi lama.
        $sql = "
            WITH base AS (
                SELECT /*+ MATERIALIZE */ * FROM (
                    SELECT
                        ROW_NUMBER() OVER (ORDER BY l.create_date DESC, ld.letter_detail_id DESC) AS rn,
                        ld.letter_detail_id, ld.title, ld.copy, ld.quantity,
                        ld.qty_accept, ld.qty_reject, ld.qty_hibah, ld.isbn,
                        ld.author, ld.publisher, ld.publish_year, p.name AS pub_name,
                        ld.remark, ld.letter_id, l.status, l.branch_id,
                        l.type_of_delivery, ld.isbn_status,
                        jp.name AS jasa_pengiriman_name,
                        CASE WHEN l.status = 'DITERIMA' THEN l.accept_date
                             ELSE ld.received_date END AS received_date,
                        CASE WHEN l.status = 'DITERIMA' THEN u_l.fullname
                             ELSE u.fullname END AS received_by_name,
                        l.accept_date,
                        b.name AS destination_library,
                        b.province_id AS destination_province_id,
                        CASE WHEN {$canEditExpr} THEN 1 ELSE 0 END AS boleh_wilayah,
                        {$isbnLd} AS isbn_norm,
                        CASE WHEN l.status IN ('DITERIMA PENUH', 'DITERIMA PARSIAL', 'CEK FISIK', 'DITERIMA')
                             THEN 1 ELSE 0 END AS in_hist
                    FROM letter_detail ld
                    JOIN penerbit p ON p.id = ld.penerbit_id
                    JOIN letter l ON l.letter_id = ld.letter_id
                    LEFT JOIN users u ON u.username = ld.received_by
                    LEFT JOIN users u_l ON u_l.username = l.create_by
                    LEFT JOIN jasa_pengiriman jp ON jp.id = l.jasa_pengiriman_id
                    JOIN branchs b ON b.id = l.branch_id
                    WHERE {$where}
                ) WHERE rn <= {$limit}
            ),
            hist AS (
                SELECT
                    {$isbnLd2} AS isbn_norm,
                    SUM(CASE WHEN l2.branch_id <> 37 THEN ld2.copy END) AS copy_prov,
                    SUM(CASE WHEN l2.branch_id  = 37 THEN ld2.copy END) AS copy_sistem,
                    SUM(CASE WHEN l2.branch_id <> 37 THEN ld2.qty_accept END) AS accept_prov,
                    SUM(CASE WHEN l2.branch_id  = 37 THEN ld2.qty_accept END) AS accept_sistem
                FROM letter_detail ld2
                JOIN letter l2 ON l2.letter_id = ld2.letter_id
                WHERE l2.status IN ('DITERIMA PENUH', 'DITERIMA PARSIAL', 'CEK FISIK', 'DITERIMA')
                  AND {$isbnLd2} IN (SELECT isbn_norm FROM base)
                GROUP BY {$isbnLd2}
            ),
            col AS (
                SELECT
                    {$isbnCol} AS isbn_norm,
                    COUNT(c.id) AS total_collection_sistem
                FROM collections c
                WHERE c.source_id = 6
                  AND c.branch_id = {$branchId}
                  AND {$isbnCol} IN (SELECT isbn_norm FROM base)
                GROUP BY {$isbnCol}
            ),
            media AS (
                -- Jenis media dari pengajuan ISBN penerbit. Verifikasi fisik
                -- hanya untuk karya cetak (jenis_media = 1).
                -- isbn_no di PENERBIT_ISBN selalu tanpa tanda hubung, sama
                -- bentuknya dengan isbn_norm, jadi bisa dibandingkan langsung.
                SELECT
                    pi.isbn_no AS isbn_norm,
                    MAX(pt.jenis_media) AS jenis_media
                FROM penerbit_isbn pi
                JOIN penerbit_terbitan pt ON pt.id = pi.penerbit_terbitan_id
                WHERE pi.isbn_no IN (SELECT isbn_norm FROM base)
                GROUP BY pi.isbn_no
            )
            SELECT
                t.*,
                t.total_copy_prov + t.total_copy_sistem AS total_copy_all,
                CASE WHEN t.received_date IS NULL THEN 'verification' ELSE 'received' END AS status_code
            FROM (
                SELECT
                    b.*,
                    NVL(h.copy_prov, 0)     - CASE WHEN b.in_hist = 1 AND b.branch_id <> 37 THEN NVL(b.copy, 0)       ELSE 0 END AS total_copy_prov,
                    NVL(h.copy_sistem, 0)   - CASE WHEN b.in_hist = 1 AND b.branch_id  = 37 THEN NVL(b.copy, 0)       ELSE 0 END AS total_copy_sistem,
                    NVL(h.accept_prov, 0)   - CASE WHEN b.in_hist = 1 AND b.branch_id <> 37 THEN NVL(b.qty_accept, 0) ELSE 0 END AS total_accept_prov,
                    NVL(h.accept_sistem, 0) - CASE WHEN b.in_hist = 1 AND b.branch_id  = 37 THEN NVL(b.qty_accept, 0) ELSE 0 END AS total_accept_sistem,
                    NVL(c.total_collection_sistem, 0) AS total_collection_sistem,
                    m.jenis_media,
                    jm.name AS jenis_media_name,
                    -- Boleh diproses bila: wilayahnya cocok DAN jenis medianya
                    -- karya cetak. ISBN yang tidak ada di pengajuan ISBN
                    -- (jenis_media NULL) tetap diizinkan -- kirimannya sudah
                    -- terlanjur ada, tidak ada bukti bahwa itu non-cetak.
                    CASE
                        WHEN b.boleh_wilayah = 1 AND (m.jenis_media IS NULL OR m.jenis_media = '1')
                        THEN 1 ELSE 0
                    END AS can_edit
                FROM base b
                LEFT JOIN hist h ON h.isbn_norm = b.isbn_norm
                LEFT JOIN col c ON c.isbn_norm = b.isbn_norm
                LEFT JOIN media m ON m.isbn_norm = b.isbn_norm
                LEFT JOIN jenis_media jm ON TO_CHAR(jm.id) = m.jenis_media
            ) t
            ORDER BY t.rn
        ";
        //Log::info($sql);
        $data = QueryAPI::get($sql, false, self::CONNECT_TIMEOUT, self::QUERY_TIMEOUT) ?? [];

        // Tidak ketemu di penerimaan? Cari di data ISBN penerbit, supaya petugas
        // tahu bukunya memang terdaftar tapi belum pernah masuk pengiriman --
        // bukan sekadar "tidak ditemukan".
        $registry = [];

        if (!$data && $mode === 'isbn') {
            $registry = $this->searchPenerbitIsbn($isbnKeys ?? []);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Berhasil',
            'mode' => $mode,
            'count' => count($data),
            'data' => $data,
            'registry' => $registry,
        ]);
    }

    /**
     * Cari ISBN di PENERBIT_ISBN (data pengajuan ISBN penerbit).
     *
     * Kolom isbn_no di sana selalu tanpa tanda hubung, jadi dibandingkan
     * mentah -- 0,16 detik, sedangkan dibungkus REPLACE() jadi 0,40 detik
     * karena index tidak terpakai.
     */
    private function searchPenerbitIsbn(array $isbnKeys): array
    {
        if (!$isbnKeys) {
            return [];
        }

        $list = "'" . implode("', '", $isbnKeys) . "'";

        // ROWNUM, bukan FETCH FIRST -- databasenya Oracle 11g.
        $rows = QueryAPI::get("
            SELECT * FROM (
                SELECT
                    pi.id AS penerbit_isbn_id,
                    pi.isbn_no,
                    pt.title,
                    pt.author,
                    pt.tahun_terbit,
                    p.name AS pub_name,
                    pi.status,
                    pi.tanggal_terbit,
                    pi.received_date_kckr,
                    pi.received_date_prov,
                    pt.jenis_media,
                    jm.name AS jenis_media_name
                FROM penerbit_isbn pi
                LEFT JOIN penerbit_terbitan pt ON pt.id = pi.penerbit_terbitan_id
                LEFT JOIN penerbit p ON p.id = pi.penerbit_id
                LEFT JOIN jenis_media jm ON TO_CHAR(jm.id) = pt.jenis_media
                WHERE pi.isbn_no IN ({$list})
                ORDER BY pi.id DESC
            ) WHERE ROWNUM <= 10
        ", false, self::CONNECT_TIMEOUT, self::QUERY_TIMEOUT) ?? [];

        return is_array($rows) ? $rows : [];
    }

    /**
     * Hapus satu judul yang BELUM diterima.
     *
     * Tiga syarat, semuanya diperiksa ulang di server:
     * 1. Datanya ada.
     * 2. Penggunanya berhak atas provinsi tujuan kiriman itu.
     * 3. Belum ada tanggal terima -- yang sudah diterima tidak boleh dihapus.
     */
    public function destroy(Request $request)
    {
        $id = (int) $request->letter_detail_id;
        $row = $this->findForEdit($id);

        if (!$row) {
            return response()->json([
                'code' => 404,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        if (!$this->mayHandle($row)) {
            return response()->json([
                'code' => 403,
                'message' => 'Anda hanya dapat menghapus kiriman ke provinsi Anda sendiri.'
            ], 403);
        }

        if (!empty($row->RECEIVED_DATE)) {
            return response()->json([
                'code' => 422,
                'message' => 'Judul ini sudah diterima, tidak dapat dihapus.'
            ], 422);
        }

        try {
            if (!QueryAPI::delete('letter_detail', $id)) {
                return response()->json([
                    'code' => 500,
                    'message' => 'Gagal menghapus data.'
                ], 500);
            }

            Log::info('Hapus judul belum diterima', [
                'letter_detail_id' => $id,
                'letter_id' => $row->LETTER_ID,
                'title' => $row->TITLE,
                'oleh' => session('username'),
            ]);

            return response()->json([
                'code' => 200,
                'message' => 'Judul berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ambil data letter_detail berikut provinsi tujuannya, langsung dari
     * database. Sengaja tidak memakai apa pun dari request -- branch_id yang
     * dikirim browser bisa diubah pengguna, jadi tidak boleh dipercaya.
     */
    private function findForEdit($letterDetailId): ?object
    {
        $id = (int) $letterDetailId;

        if ($id <= 0) {
            return null;
        }

        return QueryAPI::get("
            SELECT
                ld.letter_detail_id,
                ld.received_date,
                ld.title,
                l.letter_id,
                l.status,
                l.branch_id,
                b.province_id,
                b.name AS destination_library,
                (
                    SELECT MAX(pt.jenis_media)
                    FROM penerbit_isbn pi
                    JOIN penerbit_terbitan pt ON pt.id = pi.penerbit_terbitan_id
                    WHERE pi.isbn_no = REPLACE(ld.isbn, '-', '')
                ) AS jenis_media
            FROM letter_detail ld
            JOIN letter l ON l.letter_id = ld.letter_id
            JOIN branchs b ON b.id = l.branch_id
            WHERE ld.letter_detail_id = {$id}
        ", true, self::CONNECT_TIMEOUT, self::QUERY_TIMEOUT);
    }

    /**
     * Verifikasi fisik hanya untuk karya cetak (penerbit_terbitan.jenis_media = 1).
     * ISBN yang tidak terdaftar di pengajuan ISBN (NULL) tetap diizinkan --
     * kirimannya sudah ada dan tidak ada bukti bahwa itu bukan karya cetak.
     */
    private function mediaBolehDiterima(object $row): bool
    {
        $jenis = $row->JENIS_MEDIA ?? null;

        return $jenis === null || trim((string) $jenis) === '' || trim((string) $jenis) === '1';
    }

    /**
     * Nama jenis media untuk pesan ke pengguna. Tabel JENIS_MEDIA baru berisi
     * id 1; selama sisanya belum diisi, kodenya yang ditampilkan.
     */
    private function namaJenisMedia($kode): string
    {
        $kode = trim((string) $kode);

        if ($kode === '') {
            return 'tidak diketahui';
        }

        $nama = QueryAPI::get(
            "SELECT name FROM jenis_media WHERE TO_CHAR(id) = '" . preg_replace('/[^0-9]/', '', $kode) . "'",
            true,
            self::CONNECT_TIMEOUT,
            self::QUERY_TIMEOUT
        );

        return $nama->NAME ?? ('jenis media kode ' . $kode);
    }

    /**
     * Perpusnas boleh menangani semua kiriman; petugas provinsi hanya kiriman
     * yang ditujukan ke provinsinya sendiri.
     */
    private function mayHandle(object $row): bool
    {
        if (Main::isPerpusnas()) {
            return true;
        }

        return (int) $row->PROVINCE_ID === (int) session('province_id');
    }

    private function escapeSql(string $value): string
    {
        $value = str_replace('\\', '\\\\', $value);
        $value = str_replace("'", "''", $value);
        $value = str_replace('%', '\%', $value);
        $value = str_replace('_', '\_', $value);

        return $value;
    }

    public function updateReceivedDate(Request $request)
    {
        $id = $request->letter_detail_id;

        $row = $this->findForEdit($id);

        if (!$row) {
            return response()->json([
                'code' => 404,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        if (!$this->mayHandle($row)) {
            return response()->json([
                'code' => 403,
                'message' => 'Anda hanya dapat memproses penerimaan untuk kiriman ke provinsi Anda sendiri.'
            ], 403);
        }

        if (!$this->mediaBolehDiterima($row)) {
            return response()->json([
                'code' => 422,
                'message' => 'ISBN ini adalah ISBN ' . $this->namaJenisMedia($row->JENIS_MEDIA)
                    . '. Verifikasi fisik hanya untuk karya cetak.'
            ], 422);
        }

        // Tanggal terima tidak boleh di masa depan. Atribut "max" di browser
        // gampang dilewati, jadi diperiksa lagi di sini.
        $tanggalTerima = trim((string) $request->received_date);

        if ($tanggalTerima === '') {
            return response()->json([
                'code' => 422,
                'message' => 'Tanggal terima wajib diisi.'
            ], 422);
        }

        try {
            $tanggal = Carbon::parse($tanggalTerima)->startOfDay();
        } catch (\Exception $e) {
            return response()->json([
                'code' => 422,
                'message' => 'Format tanggal terima tidak valid.'
            ], 422);
        }

        if ($tanggal->greaterThan(Carbon::today())) {
            return response()->json([
                'code' => 422,
                'message' => 'Tanggal terima tidak boleh melebihi hari ini (' . Carbon::today()->format('d/m/Y') . ').'
            ], 422);
        }

        try {
            if ($request->ISBN ?: null && (int) $request->detail_qty_accept > 0) {
                QueryAPI::setReceiveDate([
                    'LetterDetailId' => $id,
                    'NomorISBN' => $request->detail_isbn,
                    'IsPerpusnas' => ((int) $request->branch_id) == 37 ? 1 : 0,
                    'IsProvinsi' => ((int) $request->branch_id) != 37 ? 1 : 0,
                    'TanggalTerima' => $request->received_date,
                ]);
            }
            $params = [
                'received_date' => $request->received_date,
                'qty_accept' => $request->detail_qty_accept,
                'qty_reject' => $request->detail_qty_reject,
                'copy' => $request->detail_copy,
                'remark' => $request->detail_reject_reason,
                'checked' => 1
            ];
            if ($request->received_by_name == "no_name") {
                $params = array_merge($params, [
                    'received_by' => session('username'),
                ]);
            }
            QueryAPI::update('letter_detail', $id, $params, false);
            $letterDetail = QueryAPI::get("
                select
                    sum(copy) as total_data,
                    sum(qty_accept) as total_accept,
                    sum(nvl(qty_reject, 0)) as total_reject
                from
                    letter_detail
                where
                    letter_id = '$request->letter_id'
                ", true);
            if ($letterDetail) {
                if ($letterDetail->TOTAL_DATA == $letterDetail->TOTAL_ACCEPT) {
                    $status = 'DITERIMA PENUH';
                } else {
                    $status = 'DITERIMA PARSIAL';
                }
                if ($request->letter_status != 'DITERIMA') {
                    QueryAPI::update('letter', $request->letter_id, [
                        'status' => $status,
                        'accept_date' => $request->received_date,
                        'update_by' => session('username'),
                        'update_terminal' => $request->ip(),
                        'update_date' => date('Y-m-d')
                    ], false);
                } else {
                    QueryAPI::update('letter', $request->letter_id, [
                        //'status' => 'DITERIMA',
                        //'accept_date' => $request->received_date,
                        'update_by' => session('username'),
                        'update_terminal' => $request->ip(),
                        'update_date' => date('Y-m-d')
                    ], false);
                }
            }
            return response()->json([
                'code' => 200,
                'message' => 'Berhasil menyimpan data penerimaan.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
