<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

/**
 * Keberadaan fisik dus dari LETTER_ANTRIAN, untuk ditempelkan ke query
 * yang berbasis tabel LETTER.
 *
 * Aturan penautannya sengaja ditaruh di satu tempat karena dipakai di dua
 * layar (Verifikasi Pengiriman dan Verifikasi Per Judul) -- kalau disalin,
 * cepat atau lambat keduanya akan berbeda.
 *
 * Cara menaut, berurutan:
 *   1. letter_antrian.letter_id = letter.letter_id
 *   2. kalau letter_id kosong: receipt_no sama DAN nama ekspedisinya sama
 *      dengan jasa_pengiriman milik letter tersebut.
 *
 * Catatan: LETTER_ANTRIAN tidak menyimpan jasa_pengiriman_id, hanya nama
 * ekspedisi sebagai teks. Pencocokan dilakukan atas nama yang sudah
 * dinormalkan (huruf besar, tanpa spasi tepi), bukan atas id.
 *
 * Satu baris LETTER_ANTRIAN = satu dus (ada unique key KIRIMAN_ID+NOMOR_DUS),
 * jadi jumlah dus yang tiba dihitung dari COUNT(*).
 */
class AntrianFisik
{
    /**
     * Kolom hasil untuk ditaruh di SELECT. Semua berawalan "fisik_".
     */
    public static function columns(): string
    {
        return "
            nvl(fa.jml_dus, fr.jml_dus) as fisik_jml_dus,
            nvl(fa.total_dus, fr.total_dus) as fisik_total_dus,
            nvl(fa.status_terakhir, fr.status_terakhir) as fisik_status,
            nvl(fa.waktu_terakhir, fr.waktu_terakhir) as fisik_waktu,
            nvl(lfa.nama, lfr.nama) as fisik_lokasi,
            case
                when fa.letter_id is not null then 'letter_id'
                when fr.receipt_no is not null then 'receipt_no'
            end as fisik_tautan
        ";
    }

    /**
     * LEFT JOIN yang perlu ditambahkan. $letter dan $jp adalah alias tabel
     * letter dan jasa_pengiriman di query pemanggil.
     */
    public static function joins(string $letter = 'l', string $jp = 'jp'): string
    {
        return "
            left join (
                select
                    letter_id,
                    count(*) as jml_dus,
                    max(jumlah_dus) as total_dus,
                    max(waktu_terima) as waktu_terakhir,
                    max(status) keep (dense_rank last order by waktu_terima, id) as status_terakhir,
                    max(lokasi_id) keep (dense_rank last order by waktu_terima, id) as lokasi_terakhir
                from letter_antrian
                where delete_date is null and letter_id is not null
                group by letter_id
            ) fa on fa.letter_id = {$letter}.letter_id
            left join letter_antrian_lokasi lfa on lfa.id = fa.lokasi_terakhir
            left join (
                select
                    receipt_no,
                    upper(trim(ekspedisi)) as ekspedisi_norm,
                    count(*) as jml_dus,
                    max(jumlah_dus) as total_dus,
                    max(waktu_terima) as waktu_terakhir,
                    max(status) keep (dense_rank last order by waktu_terima, id) as status_terakhir,
                    max(lokasi_id) keep (dense_rank last order by waktu_terima, id) as lokasi_terakhir
                from letter_antrian
                where delete_date is null and letter_id is null and receipt_no is not null
                group by receipt_no, upper(trim(ekspedisi))
            ) fr on fr.receipt_no = {$letter}.receipt_no
                and fr.ekspedisi_norm = upper(trim({$jp}.name))
            left join letter_antrian_lokasi lfr on lfr.id = fr.lokasi_terakhir
        ";
    }

    /** Kunci session tempat "dus aktif" disimpan. */
    public const SESSION_DUS = 'dus_aktif';

    /**
     * Umur maksimal pilihan dus aktif, dalam detik (8 jam -- satu hari kerja).
     * Lewat dari ini petugas diminta memilih ulang, bukan diteruskan diam-diam.
     */
    public const BATAS_DUS_AKTIF = 8 * 3600;

    /**
     * Status dus setelah petugas verifikasi mulai membukanya.
     *
     * Penamaannya mengikuti gaya yang sudah dipakai satpam ("diterima_satpam").
     * Daftar status belum ditetapkan bersama, jadi nilai ini perlu disepakati
     * dengan pembuat aplikasi satpam supaya keduanya membaca kode yang sama.
     */
    public const STATUS_DIPROSES = 'diproses';

    /**
     * Status dus setelah petugas menyatakan isinya habis dikerjakan.
     *
     * Tidak bisa disimpulkan otomatis: sistem tidak pernah tahu berapa judul
     * yang ada di dalam satu dus. Judul baru tertaut saat diverifikasi, jadi
     * "semua judul yang tertaut sudah diproses" selalu benar dan tidak
     * membuktikan apa pun. Hanya petugas yang memegang dus kosong yang tahu.
     */
    public const STATUS_SELESAI = 'selesai';

    /**
     * Tandai dus sedang diproses. Dipanggil setelah penerimaan tersimpan.
     *
     * Sengaja tidak melempar exception: penerimaannya sudah tersimpan, dan
     * kegagalan mengubah status dus tidak boleh membatalkan pekerjaan petugas.
     * Ada check constraint pada kolom STATUS yang isinya tidak terbaca, jadi
     * kode baru bisa saja ditolak -- kalau itu terjadi, dicatat ke log untuk
     * ditindaklanjuti, bukan dilemparkan ke layar petugas.
     */
    public static function tandaiDiproses(int $letterAntrianId): bool
    {
        return self::ubahStatus($letterAntrianId, self::STATUS_DIPROSES);
    }

    public static function tandaiSelesai(int $letterAntrianId): bool
    {
        return self::ubahStatus($letterAntrianId, self::STATUS_SELESAI);
    }

    private static function ubahStatus(int $letterAntrianId, string $status): bool
    {
        try {
            $dus = QueryAPI::get(
                "select status from letter_antrian where id = {$letterAntrianId}",
                true,
                5,
                30
            );

            if (!$dus || strtolower(trim((string) $dus->STATUS)) === $status) {
                return false;
            }

            QueryAPI::update('letter_antrian', $letterAntrianId, [
                'status' => $status,
                'update_date' => date('Y-m-d H:i:s'),
            ], false);

            return true;
        } catch (\Exception $e) {
            Log::warning('Gagal mengubah status dus jadi ' . $status, [
                'letter_antrian_id' => $letterAntrianId,
                'pesan' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Tentukan dus mana yang harus dicatat untuk satu resi.
     *
     * Mengembalikan [letter_antrian_id, pesan_error]. Kalau pesan_error terisi,
     * penyimpanan harus dihentikan -- artinya resinya berdus banyak dan
     * sistem tidak boleh menebak dus mana yang sedang dibuka.
     *
     * Resi yang belum tercatat di antrian sama sekali mengembalikan
     * [null, null]: penerimaan tetap boleh disimpan, hanya tanpa tautan dus.
     * Memblokirnya akan menghentikan pekerjaan hanya karena satpam belum
     * sempat mendata -- itu bukan kesalahan petugas verifikasi.
     */
    public static function tentukanDus(int $letterId): array
    {
        $hasil = ['id' => null, 'error' => null, 'taut_kiriman' => null];

        $aktif = session(self::SESSION_DUS);

        // Dus aktif yang ditinggal terlalu lama hampir pasti basi -- petugas
        // pulang tanpa menekan Selesai, lalu esoknya mengerjakan dus lain.
        // Memakainya diam-diam akan menautkan judul ke dus yang salah.
        if ($aktif && !empty($aktif['dipilih_pada'])
            && (time() - (int) $aktif['dipilih_pada']) > self::BATAS_DUS_AKTIF) {
            $jam = round((time() - (int) $aktif['dipilih_pada']) / 3600);

            $hasil['error'] = 'Dus aktif ' . $aktif['nomor'] . ' dipilih ' . $jam
                . ' jam lalu dan kemungkinan sudah tidak relevan. '
                . 'Pilih ulang dus yang sedang Anda buka.';

            return $hasil;
        }

        if ($aktif) {
            // Dus yang dicatat manual oleh satpam belum tahu resinya -- resi
            // yang dicari penerbit tidak ketemu, atau salah ketik. Petugas
            // verifikasi menemukannya lewat ISBN, jadi resinya ketahuan dari
            // pekerjaan normal. Tautkan sekalian saat menyimpan.
            if (empty($aktif['letter_id'])) {
                $hasil['id'] = (int) $aktif['id'];
                $hasil['taut_kiriman'] = $aktif['kiriman_id'];

                return $hasil;
            }

            // Pengaman utama terhadap lupa ganti dus: berpindah dus hampir
            // selalu berarti berpindah resi juga.
            if ((int) $aktif['letter_id'] !== $letterId) {
                $hasil['error'] = 'Dus aktif ' . $aktif['nomor'] . ' milik resi ' . $aktif['receipt_no']
                    . ', bukan resi ini. Ganti dus aktif dulu.';

                return $hasil;
            }

            $hasil['id'] = (int) $aktif['id'];

            return $hasil;
        }

        // Tanpa dus aktif: hanya bisa ditentukan kalau resinya berdus tunggal.
        $dusList = QueryAPI::get("
            select id, nomor_antrian, nomor_dus
            from letter_antrian
            where delete_date is null and letter_id = {$letterId}
            order by nomor_dus, id
        ", false, 5, 30) ?? [];

        if (!$dusList) {
            return $hasil;
        }

        if (count($dusList) === 1) {
            $hasil['id'] = (int) $dusList[0]->ID;

            return $hasil;
        }

        $hasil['error'] = 'Resi ini terdiri dari ' . count($dusList) . ' dus. '
            . 'Pilih dulu dus yang sedang Anda buka lewat tombol "Buka Dus".';

        return $hasil;
    }

    /**
     * Tautkan seluruh dus satu kiriman ke sebuah resi.
     * Dipakai saat dus yang dicatat manual satpam akhirnya ketahuan resinya
     * lewat penerimaan pertama yang disimpan dari dus itu.
     */
    public static function tautkanKiriman(string $kirimanId, int $letterId): int
    {
        $aman = str_replace("'", "''", $kirimanId);

        $dusList = QueryAPI::get("
            select id from letter_antrian
            where delete_date is null and kiriman_id = '{$aman}' and letter_id is null
        ", false, 5, 30) ?? [];

        foreach ($dusList as $dus) {
            QueryAPI::update('letter_antrian', (int) $dus->ID, [
                'letter_id' => $letterId,
                'update_date' => date('Y-m-d H:i:s'),
            ], false);
        }

        return count($dusList);
    }

    /**
     * Ringkasan siap tampil untuk satu baris hasil query.
     */
    public static function badge($row): string
    {
        $status = trim((string) ($row->FISIK_STATUS ?? ''));

        if ($status === '') {
            return '<span class="badge bg-secondary bg-opacity-10 text-secondary">Belum tiba</span>';
        }

        $warna = [
            'diterima_satpam' => 'bg-warning bg-opacity-10 text-warning',
            self::STATUS_DIPROSES => 'bg-info bg-opacity-10 text-info',
            'diterima' => 'bg-success bg-opacity-10 text-success',
            'selesai' => 'bg-success bg-opacity-10 text-success',
            'batal' => 'bg-danger bg-opacity-10 text-danger',
        ];

        $kelas = $warna[strtolower($status)] ?? 'bg-info bg-opacity-10 text-info';
        $label = ucfirst(str_replace('_', ' ', $status));

        $html = '<span class="badge ' . $kelas . '">' . e($label) . '</span>';

        $dus = (int) ($row->FISIK_JML_DUS ?? 0);
        $total = (int) ($row->FISIK_TOTAL_DUS ?? 0);

        if ($dus > 0) {
            $html .= '<div class="small mt-1">' . $dus . ($total > 0 ? '/' . $total : '') . ' dus</div>';
        }

        if (!empty($row->FISIK_LOKASI)) {
            $html .= '<small class="text-muted d-block">' . e($row->FISIK_LOKASI) . '</small>';
        }

        // Ditaut lewat resi + ekspedisi, bukan letter_id -- petugas perlu tahu
        // ini kecocokan tidak langsung supaya bisa dicek kalau meragukan.
        if (($row->FISIK_TAUTAN ?? '') === 'receipt_no') {
            $html .= '<small class="text-warning d-block" title="Dicocokkan lewat nomor resi dan ekspedisi, belum tertaut ke resi">'
                . '<i class="ph-link-break"></i> cocok via resi</small>';
        }

        return $html;
    }
}
