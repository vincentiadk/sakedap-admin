<?php

namespace App\Http\Controllers\PhysicalDelivery;

use App\Helpers\AntrianFisik;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Menautkan antrian fisik (LETTER_ANTRIAN) ke sebuah resi (LETTER).
 *
 * Dibutuhkan karena satpam mengisi antrian secara manual: nomor resi bisa
 * salah ketik dan nama ekspedisi bisa ditulis berbeda, sehingga pencocokan
 * otomatis gagal walau dusnya jelas-jelas sudah tiba. Petugas verifikasi
 * memegang dusnya dan bisa membaca nomor antrian di stiker -- itu bukti yang
 * lebih kuat daripada kecocokan teks.
 *
 * Penautan selalu dikenakan ke SATU KIRIMAN UTUH (semua dus dengan
 * KIRIMAN_ID yang sama), bukan per baris. Kalau hanya sebagian dus yang
 * tertaut, tampilannya akan berbunyi "1/2 dus" seolah ada dus yang hilang.
 */
class QueueLinkController extends Controller
{
    private const CONNECT_TIMEOUT = 5;
    private const QUERY_TIMEOUT = 30;

    /**
     * Cari kiriman di antrian berdasarkan nomor antrian pada stiker, atau
     * nomor resi. Dikelompokkan per kiriman, bukan per dus.
     */
    public function search(Request $request)
    {
        $keyword = strtoupper(trim((string) $request->keyword));

        if ($keyword === '') {
            return response()->json(['code' => 422, 'message' => 'Kata kunci tidak boleh kosong'], 422);
        }

        $aman = str_replace("'", "''", $keyword);

        $rows = QueryAPI::get("
            SELECT
                a.kiriman_id,
                MIN(a.nomor_antrian) AS nomor_pertama,
                COUNT(*) AS jml_dus,
                MAX(a.jumlah_dus) AS total_dus,
                MAX(a.receipt_no) AS receipt_no,
                MAX(a.ekspedisi) AS ekspedisi,
                MAX(a.cara_datang) AS cara_datang,
                MAX(a.nama_petugas) AS nama_petugas,
                MAX(a.waktu_terima) AS waktu_terima,
                MAX(a.status) KEEP (DENSE_RANK LAST ORDER BY a.waktu_terima, a.id) AS status,
                MAX(l.nama) KEEP (DENSE_RANK LAST ORDER BY a.waktu_terima, a.id) AS lokasi_nama,
                MAX(a.letter_id) AS letter_id,
                MAX(lt.receipt_no) AS letter_receipt_no
            FROM letter_antrian a
            LEFT JOIN letter_antrian_lokasi l ON l.id = a.lokasi_id
            LEFT JOIN letter lt ON lt.letter_id = a.letter_id
            WHERE a.delete_date IS NULL
              AND (UPPER(a.nomor_antrian) LIKE '%{$aman}%' OR UPPER(a.receipt_no) LIKE '%{$aman}%')
            GROUP BY a.kiriman_id
            ORDER BY MAX(a.waktu_terima) DESC
        ", false, self::CONNECT_TIMEOUT, self::QUERY_TIMEOUT) ?? [];

        // Untuk memilih dus aktif, petugas butuh baris per dus -- bukan
        // ringkasan per kiriman seperti pada penautan.
        $dus = [];

        if ($request->per_dus) {
            $dus = QueryAPI::get("
                SELECT id, nomor_antrian, nomor_dus, jumlah_dus, kiriman_id, letter_id
                FROM letter_antrian
                WHERE delete_date IS NULL
                  AND (UPPER(nomor_antrian) LIKE '%{$aman}%'
                       OR UPPER(receipt_no) LIKE '%{$aman}%'
                       OR UPPER(kiriman_id) LIKE '%{$aman}%')
                ORDER BY kiriman_id, nomor_dus, id
            ", false, self::CONNECT_TIMEOUT, self::QUERY_TIMEOUT) ?? [];
        }

        return response()->json([
            'code' => 200,
            'count' => count($rows),
            'data' => $rows,
            'dus' => $dus,
        ]);
    }

    /**
     * Cari resi untuk ditautkan ke sebuah kiriman.
     *
     * Dipakai saat petugas memegang dus yang belum tertaut: nomor resi yang
     * diketik satpam dipakai sebagai kata kunci awal, tapi tetap bisa diubah
     * karena ketikan satpam bisa salah.
     */
    public function searchLetter(Request $request)
    {
        $keyword = strtoupper(trim((string) $request->keyword));

        if ($keyword === '') {
            return response()->json(['code' => 422, 'message' => 'Kata kunci tidak boleh kosong'], 422);
        }

        if (mb_strlen($keyword) < 3) {
            return response()->json([
                'code' => 422,
                'message' => 'Ketik minimal 3 karakter.'
            ], 422);
        }

        $aman = str_replace("'", "''", $keyword);

        // Dicari dari nomor resi ATAU nama penerbit. Nomor resi sering kosong
        // atau salah ketik -- yang selalu bisa dibaca petugas dari dus adalah
        // nama pengirimnya.
        $where = "(upper(l.receipt_no) like '%{$aman}%' or upper(p.name) like '%{$aman}%')";

        // Resi yang sudah diterima tidak perlu ditawarkan -- dusnya tidak
        // mungkin baru tiba sekarang.
        $where .= " and l.status not in ('DITERIMA', 'DITERIMA PENUH', 'DITERIMA PARSIAL')";

        // Petugas provinsi hanya boleh melihat resi yang ditujukan ke
        // provinsinya -- sama seperti aturan penerimaan.
        if (!Main::isPerpusnas()) {
            $where .= ' and b.province_id = ' . (int) session('province_id');
        }

        $rows = QueryAPI::get("
            SELECT * FROM (
                SELECT
                    l.letter_id, l.receipt_no, l.status, l.sent_date,
                    p.name AS penerbit, b.name AS tujuan, jp.name AS ekspedisi,
                    (SELECT COUNT(*) FROM letter_antrian a
                      WHERE a.letter_id = l.letter_id AND a.delete_date IS NULL) AS dus_tertaut
                FROM letter l
                LEFT JOIN penerbit p ON p.id = l.penerbit_id
                LEFT JOIN jasa_pengiriman jp ON jp.id = l.jasa_pengiriman_id
                JOIN branchs b ON b.id = l.branch_id
                WHERE {$where}
                ORDER BY l.letter_id DESC
            ) WHERE ROWNUM <= 10
        ", false, self::CONNECT_TIMEOUT, self::QUERY_TIMEOUT) ?? [];

        return response()->json(['code' => 200, 'count' => count($rows), 'data' => $rows]);
    }

    /**
     * Tautkan satu kiriman ke sebuah resi.
     *
     * Menautkan ulang ke resi lain diperbolehkan -- itu jalur perbaikan kalau
     * sebelumnya salah taut.
     */
    public function link(Request $request)
    {
        $letterId = (int) $request->letter_id;
        $kirimanId = trim((string) $request->kiriman_id);

        if ($letterId <= 0 || $kirimanId === '') {
            return response()->json(['code' => 422, 'message' => 'Resi dan kiriman wajib diisi.'], 422);
        }

        $letter = $this->letter($letterId);

        if (!$letter) {
            return response()->json(['code' => 404, 'message' => 'Resi tidak ditemukan.'], 404);
        }

        // Aturan sama dengan penerimaan: Perpusnas bebas, provinsi hanya
        // kiriman yang ditujukan ke provinsinya sendiri.
        if (!Main::isPerpusnas() && (int) $letter->PROVINCE_ID !== (int) session('province_id')) {
            return response()->json([
                'code' => 403,
                'message' => 'Anda hanya dapat menautkan antrian untuk kiriman ke provinsi Anda sendiri.'
            ], 403);
        }

        $aman = str_replace("'", "''", $kirimanId);

        $dusList = QueryAPI::get("
            SELECT id, nomor_antrian, letter_id
            FROM letter_antrian
            WHERE delete_date IS NULL AND kiriman_id = '{$aman}'
            ORDER BY nomor_dus, id
        ", false, self::CONNECT_TIMEOUT, self::QUERY_TIMEOUT) ?? [];

        if (!$dusList) {
            return response()->json(['code' => 404, 'message' => 'Kiriman tidak ditemukan di antrian.'], 404);
        }

        $diubah = 0;

        try {
            foreach ($dusList as $dus) {
                if ((int) $dus->LETTER_ID === $letterId) {
                    continue;
                }

                QueryAPI::update('letter_antrian', (int) $dus->ID, [
                    'letter_id' => $letterId,
                    'update_date' => date('Y-m-d H:i:s'),
                ], false);

                $diubah++;
            }
        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Gagal menautkan: ' . $e->getMessage()
            ], 500);
        }

        Log::info('Tautkan antrian ke resi', [
            'kiriman_id' => $kirimanId,
            'letter_id' => $letterId,
            'receipt_no' => $letter->RECEIPT_NO,
            'dus_diubah' => $diubah,
            'oleh' => session('username'),
        ]);

        return response()->json([
            'code' => 200,
            'message' => $diubah > 0
                ? "Kiriman berhasil ditautkan ke resi {$letter->RECEIPT_NO} ({$diubah} dus)."
                : 'Kiriman ini memang sudah tertaut ke resi tersebut.',
        ]);
    }

    /**
     * Tetapkan dus yang sedang dibuka petugas. Disimpan di session server,
     * bukan di browser, karena penulisan letter_antrian_id terjadi di server
     * saat penerimaan disimpan.
     */
    public function setActive(Request $request)
    {
        $id = (int) $request->letter_antrian_id;

        // LEFT JOIN, bukan INNER: dus yang dicatat manual satpam belum punya
        // letter_id sama sekali. Justru dus itulah yang paling sering perlu
        // dipilih -- resinya akan ketahuan sendiri saat petugas menyimpan
        // penerimaan pertama dari dus tersebut.
        $dus = QueryAPI::get("
            SELECT a.id, a.nomor_antrian, a.nomor_dus, a.jumlah_dus, a.kiriman_id,
                   a.letter_id, l.receipt_no, b.province_id
            FROM letter_antrian a
            LEFT JOIN letter l ON l.letter_id = a.letter_id
            LEFT JOIN branchs b ON b.id = l.branch_id
            WHERE a.id = {$id} AND a.delete_date IS NULL
        ", true, self::CONNECT_TIMEOUT, self::QUERY_TIMEOUT);

        if (!$dus) {
            return response()->json(['code' => 404, 'message' => 'Dus tidak ditemukan.'], 404);
        }

        // Kalau dusnya sudah tertaut, batas provinsi berlaku. Kalau belum,
        // belum ada provinsi untuk diperiksa -- pemeriksaannya terjadi saat
        // menyimpan penerimaan, lewat aturan yang sama.
        if ($dus->LETTER_ID && !Main::isPerpusnas()
            && (int) $dus->PROVINCE_ID !== (int) session('province_id')) {
            return response()->json([
                'code' => 403,
                'message' => 'Dus ini milik kiriman ke provinsi lain.'
            ], 403);
        }

        session([AntrianFisik::SESSION_DUS => [
            'id' => (int) $dus->ID,
            'nomor' => $dus->NOMOR_ANTRIAN,
            'nomor_dus' => $dus->NOMOR_DUS,
            'jumlah_dus' => $dus->JUMLAH_DUS,
            'kiriman_id' => $dus->KIRIMAN_ID,
            'letter_id' => $dus->LETTER_ID ? (int) $dus->LETTER_ID : null,
            'receipt_no' => $dus->RECEIPT_NO,
            'dipilih_pada' => time(),
        ]]);

        return response()->json([
            'code' => 200,
            'message' => 'Dus ' . $dus->NOMOR_ANTRIAN . ' sedang dibuka.',
            'data' => session(AntrianFisik::SESSION_DUS),
        ]);
    }

    /**
     * Petugas menyatakan dus sudah habis dikerjakan.
     *
     * Dipisahkan dari "Ganti dus": berganti dus tidak berarti dus lama selesai
     * -- petugas bisa kembali lagi ke dus itu. Hanya tombol Selesai yang
     * menutupnya.
     */
    public function clearActive(Request $request)
    {
        $aktif = session(AntrianFisik::SESSION_DUS);
        $ditandai = false;

        if ($aktif && $request->selesai) {
            $ditandai = AntrianFisik::tandaiSelesai((int) $aktif['id']);

            Log::info('Dus dinyatakan selesai', [
                'letter_antrian_id' => $aktif['id'],
                'nomor_antrian' => $aktif['nomor'],
                'letter_id' => $aktif['letter_id'] ?? null,
                'status_berubah' => $ditandai,
                'oleh' => session('username'),
            ]);
        }

        session()->forget(AntrianFisik::SESSION_DUS);

        return response()->json([
            'code' => 200,
            'message' => $ditandai
                ? 'Dus ' . $aktif['nomor'] . ' ditandai selesai.'
                : 'Selesai membuka dus.',
        ]);
    }

    public function active()
    {
        return response()->json([
            'code' => 200,
            'data' => session(AntrianFisik::SESSION_DUS),
        ]);
    }

    private function letter(int $letterId): ?object
    {
        return QueryAPI::get("
            SELECT l.letter_id, l.receipt_no, b.province_id, b.name AS destination_library
            FROM letter l
            JOIN branchs b ON b.id = l.branch_id
            WHERE l.letter_id = {$letterId}
        ", true, self::CONNECT_TIMEOUT, self::QUERY_TIMEOUT);
    }
}
