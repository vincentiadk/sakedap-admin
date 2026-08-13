<?php

namespace App\Helpers;

use App\Traits\OracleHelper;
use Illuminate\Support\Facades\Mail;

/**
 * Sumber tunggal untuk isi email kepatuhan penerbit.
 *
 * Dipakai bersama oleh:
 *   - Console\Commands\ComplianceSendNotifications  (pengiriman massal terjadwal)
 *   - AdministrationSystem\NotificationTestController (halaman uji redaksi)
 *
 * Kelas ini sengaja tidak memutuskan SIAPA yang layak dikirimi email — itu
 * tetap milik command. Di sini hanya: ambil data penerbit, susun HTML, kirim.
 */
class ComplianceNotification
{
    use OracleHelper;

    public const CUTOFF     = '2026-01-01';
    public const START_DATE = '2021-01-01';

    public const STATUS_BLOKIR_TERBIT   = 'Blokir Konfirmasi Terbit';
    public const STATUS_BLOKIR_KCKR     = 'Blokir SSKCKR';
    public const STATUS_BLOKIR_KEDUANYA = 'Blokir Konfirmasi Terbit dan SSKCKR';

    /**
     * Definisi keempat jenis email.
     *
     * flag/date  = kolom penanda di PENERBIT.
     * param      = nama settingparameter berisi redaksi HTML (dikelola admin).
     * reminder   = true  -> dedup pakai cooldown TGL_NOTIF_*
     *              false -> dedup pakai IS_NOTIF_* (sekali per transisi status)
     */
    public const JENIS = [
        'pengingat-kt' => [
            'label'    => 'Pengingat Blokir Konfirmasi Terbit',
            'param'    => 'EmailRedaksiPengingatBlokirKonfirmasiTerbit',
            'flag'     => 'IS_NOTIF_TANGGAL_TERBIT_EMAIL',
            'date'     => 'TGL_NOTIF_TANGGAL_TERBIT_EMAIL',
            'subject'  => 'Pengingat: Rencana Pemblokiran Layanan ISBN akibat Konfirmasi Tanggal Terbit',
            'reminder' => true,
        ],
        'blokir-kt' => [
            'label'    => 'Blokir Konfirmasi Terbit',
            'param'    => 'EmailRedaksiBlokirKonfirmasiTerbit',
            'flag'     => 'IS_NOTIF_BLOKIR_KT_EMAIL',
            'date'     => 'TGL_NOTIF_BLOKIR_KT_EMAIL',
            'subject'  => 'Pemberitahuan: Pemblokiran Layanan ISBN akibat Konfirmasi Tanggal Terbit',
            'reminder' => false,
        ],
        'pengingat-kckr' => [
            'label'    => 'Pengingat Blokir SSKCKR',
            'param'    => 'EmailRedaksiPengingatBlokirSSKCKR',
            'flag'     => 'IS_NOTIF_KCKR_EMAIL',
            'date'     => 'TGL_NOTIF_KCKR_EMAIL',
            'subject'  => 'Pengingat: Rencana Pemblokiran Layanan ISBN akibat Kewajiban Serah Simpan KCKR',
            'reminder' => true,
        ],
        'blokir-kckr' => [
            'label'    => 'Blokir SSKCKR',
            'param'    => 'EmailRedaksiBlokirSSKCKR',
            'flag'     => 'IS_NOTIF_BLOKIR_KCKR_EMAIL',
            'date'     => 'TGL_NOTIF_BLOKIR_KCKR_EMAIL',
            'subject'  => 'Pemberitahuan: Pemblokiran Layanan ISBN akibat Kewajiban Serah Simpan KCKR',
            'reminder' => false,
        ],
    ];

    public static function jenisValid(string $jenis): bool
    {
        return isset(self::JENIS[$jenis]);
    }

    // ── Redaksi ──────────────────────────────────────────────────────────────

    /**
     * Ambil redaksi HTML dari settingparameters. Hanya yang terisi yang
     * dikembalikan, sehingga pemanggil bisa membedakan "belum diisi" dari "kosong".
     */
    public function loadRedaksi(): array
    {
        $names = implode("','", array_column(self::JENIS, 'param'));
        $rows  = QueryAPI::get("SELECT name, value FROM settingparameters WHERE name IN ('$names')") ?? [];

        $map = [];
        foreach ($rows as $row) {
            $name  = $row->NAME  ?? $row->name  ?? null;
            $value = $row->VALUE ?? $row->value ?? null;
            if ($name !== null && trim((string) $value) !== '') {
                $map[$name] = $value;
            }
        }

        return $map;
    }

    // ── Data penerbit ────────────────────────────────────────────────────────

    /**
     * Agregat kepatuhan per penerbit, memakai definisi yang sama dengan
     * compliance:recompute-status, ditambah hitungan khusus notifikasi:
     * KT_MENDEKATI / KCKR_MENDEKATI (jendela "akan diblokir") dan
     * TGL_BLOKIR_KT / TGL_BLOKIR_KCKR (tanggal blokir terdekat).
     *
     * @param bool $skipHaving Lepas filter "punya kewajiban" — dipakai saat
     *                         merender preview untuk penerbit sembarang.
     */
    public function fetchCandidates(
        $conn,
        int $dlKc,
        int $dlKr,
        int $teguran,
        int $reminderDays,
        ?int $penerbitId = null,
        bool $skipHaving = false
    ): array {
        $startDate = self::START_DATE;
        $cutoff    = self::CUTOFF;
        $endDate   = date('Y-m-d', strtotime('+1 day'));

        $isPre26 = "PI.CREATEDATE <  TO_DATE('$cutoff','YYYY-MM-DD')";
        $is2026  = "PI.CREATEDATE >= TO_DATE('$cutoff','YYYY-MM-DD')";

        $dlTerbit = "CASE WHEN PT.JENIS_MEDIA = '1' THEN (PI.CREATEDATE + $dlKc) ELSE (PI.CREATEDATE + $dlKr) END";
        $dlKckrV1 = "CASE WHEN P.KATEGORI_ID = 1 THEN ADD_MONTHS(PI.CREATEDATE, 3)
                          ELSE CASE WHEN PT.JENIS_MEDIA = '1' THEN ADD_MONTHS(PI.CREATEDATE, 3)
                                    ELSE ADD_MONTHS(PI.CREATEDATE, 12) END END";
        $dlKckrV2 = "CASE WHEN P.KATEGORI_ID = 1 THEN ADD_MONTHS(PI.TANGGAL_TERBIT, 3)
                          ELSE CASE WHEN PT.JENIS_MEDIA = '1' THEN ADD_MONTHS(PI.TANGGAL_TERBIT, 3)
                                    ELSE ADD_MONTHS(PI.TANGGAL_TERBIT, 12) END END";

        $belumTerbit = "(PI.TANGGAL_TERBIT IS NULL AND PI.RECEIVED_DATE_KCKR IS NULL)";

        $tagihan    = "SUM(CASE
            WHEN $isPre26 AND PI.RECEIVED_DATE_KCKR IS NULL THEN 1
            WHEN $is2026  AND PI.TANGGAL_TERBIT IS NOT NULL AND PI.RECEIVED_DATE_KCKR IS NULL THEN 1
            ELSE 0 END)";
        $sudahKckr  = "SUM(CASE WHEN PI.RECEIVED_DATE_KCKR IS NOT NULL THEN 1 ELSE 0 END)";
        $totalWajib = "($sudahKckr + $tagihan)";

        $lewatTeguran = "SUM(CASE WHEN $is2026 AND $belumTerbit AND SYSDATE > ($dlTerbit + $teguran)
                               THEN 1 ELSE 0 END)";

        // Jumlah item yang sudah terlambat KCKR — kriteria mentah blokir SSKCKR,
        // dipakai untuk memastikan pengingat tidak dikirim ke penerbit yang
        // sebenarnya sudah lewat jatuh tempo.
        $terlambat = "SUM(CASE
            WHEN $isPre26 AND (
                (PI.RECEIVED_DATE_KCKR IS NOT NULL AND PI.RECEIVED_DATE_KCKR > ($dlKckrV1))
             OR (PI.RECEIVED_DATE_KCKR IS NULL    AND SYSDATE > ($dlKckrV1))
            ) THEN 1
            WHEN $is2026 AND PI.TANGGAL_TERBIT IS NOT NULL AND (
                (PI.RECEIVED_DATE_KCKR IS NOT NULL AND PI.RECEIVED_DATE_KCKR > ($dlKckrV2))
             OR (PI.RECEIVED_DATE_KCKR IS NULL    AND SYSDATE > ($dlKckrV2))
            ) THEN 1
            ELSE 0 END)";

        // ── Jendela "N hari sebelum diblokir" ────────────────────────────────
        // Keduanya memakai lead time yang sama supaya {{jumlah_hari_pengingat}}
        // di redaksi selalu <= N.

        $ktMendekati = "SUM(CASE WHEN $is2026 AND $belumTerbit
                                 AND SYSDATE <= ($dlTerbit + $teguran)
                                 AND SYSDATE >= ($dlTerbit + $teguran - $reminderDays)
                               THEN 1 ELSE 0 END)";

        $kckrMendekati = "SUM(CASE
            WHEN $isPre26 AND PI.RECEIVED_DATE_KCKR IS NULL
                 AND SYSDATE <= ($dlKckrV1) AND SYSDATE >= ($dlKckrV1 - $reminderDays) THEN 1
            WHEN $is2026 AND PI.TANGGAL_TERBIT IS NOT NULL AND PI.RECEIVED_DATE_KCKR IS NULL
                 AND SYSDATE <= ($dlKckrV2) AND SYSDATE >= ($dlKckrV2 - $reminderDays) THEN 1
            ELSE 0 END)";

        // Angka untuk placeholder redaksi ({{jumlah_konfirmasi}}, {{jumlah_lewat_batas}}).
        $belumKonfirmasi = "SUM(CASE WHEN $is2026 AND $belumTerbit THEN 1 ELSE 0 END)";
        $lewatBatas      = "SUM(CASE WHEN $is2026 AND $belumTerbit AND SYSDATE > ($dlTerbit) THEN 1 ELSE 0 END)";

        // Tanggal blokir terdekat yang AKAN datang — dipakai {{tanggal_blokir}} pada email pengingat.
        $tglBlokirKt = "MIN(CASE WHEN $is2026 AND $belumTerbit AND SYSDATE <= ($dlTerbit + $teguran)
                                 THEN ($dlTerbit + $teguran) END)";
        $tglBlokirKckr = "MIN(CASE
            WHEN $isPre26 AND PI.RECEIVED_DATE_KCKR IS NULL AND SYSDATE <= ($dlKckrV1) THEN ($dlKckrV1)
            WHEN $is2026 AND PI.TANGGAL_TERBIT IS NOT NULL AND PI.RECEIVED_DATE_KCKR IS NULL
                 AND SYSDATE <= ($dlKckrV2) THEN ($dlKckrV2)
            END)";

        $persentase = "ROUND(CASE WHEN $totalWajib > 0 THEN $sudahKckr / $totalWajib * 100 ELSE 0 END, 1)";

        $whereId = $penerbitId ? 'WHERE P.ID = ' . (int) $penerbitId : '';
        $having  = $skipHaving ? '' : "HAVING $totalWajib > 0 OR $lewatTeguran > 0";

        $sql = "
            SELECT
                P.ID, P.NAME, P.EMAIL1, P.EMAIL2, P.STATUS_AKHIR, P.IS_LOCK,
                P.IS_IMUN_AUTO_BLOKIR,
                P.IS_NOTIF_TANGGAL_TERBIT_EMAIL, P.TGL_NOTIF_TANGGAL_TERBIT_EMAIL,
                P.IS_NOTIF_KCKR_EMAIL,           P.TGL_NOTIF_KCKR_EMAIL,
                P.IS_NOTIF_BLOKIR_KT_EMAIL,      P.TGL_NOTIF_BLOKIR_KT_EMAIL,
                P.IS_NOTIF_BLOKIR_KCKR_EMAIL,    P.TGL_NOTIF_BLOKIR_KCKR_EMAIL,
                PS.TGL_BLOKIR     AS TGL_BLOKIR_TERCATAT,
                $tagihan          AS TAGIHAN,
                $lewatTeguran     AS LEWAT_TEGURAN,
                $terlambat        AS TERLAMBAT_KCKR,
                $ktMendekati      AS KT_MENDEKATI,
                $kckrMendekati    AS KCKR_MENDEKATI,
                $belumKonfirmasi  AS BELUM_KONFIRMASI,
                $lewatBatas       AS LEWAT_BATAS_KONFIRMASI,
                $tglBlokirKt      AS TGL_BLOKIR_KT,
                $tglBlokirKckr    AS TGL_BLOKIR_KCKR,
                $persentase       AS PERSENTASE_KCKR
            FROM PENERBIT P
            LEFT JOIN PENERBIT_ISBN PI ON P.ID = PI.PENERBIT_ID
                AND PI.CREATEDATE >= TO_DATE('$startDate', 'YYYY-MM-DD')
                AND PI.CREATEDATE <  TO_DATE('$endDate', 'YYYY-MM-DD')
                AND (PI.KETERANGAN IS NULL OR UPPER(PI.KETERANGAN) NOT LIKE '%LENGKAP%')
            LEFT JOIN PENERBIT_TERBITAN PT ON PI.PENERBIT_TERBITAN_ID = PT.ID
            LEFT JOIN (
                SELECT PENERBIT_ID, MAX(CREATEDATE) AS TGL_BLOKIR
                FROM PENERBIT_STATUS
                WHERE STATUSAKHIR LIKE 'Blokir%'
                GROUP BY PENERBIT_ID
            ) PS ON PS.PENERBIT_ID = P.ID
            $whereId
            GROUP BY
                P.ID, P.NAME, P.EMAIL1, P.EMAIL2, P.STATUS_AKHIR, P.IS_LOCK,
                P.IS_IMUN_AUTO_BLOKIR,
                P.IS_NOTIF_TANGGAL_TERBIT_EMAIL, P.TGL_NOTIF_TANGGAL_TERBIT_EMAIL,
                P.IS_NOTIF_KCKR_EMAIL,           P.TGL_NOTIF_KCKR_EMAIL,
                P.IS_NOTIF_BLOKIR_KT_EMAIL,      P.TGL_NOTIF_BLOKIR_KT_EMAIL,
                P.IS_NOTIF_BLOKIR_KCKR_EMAIL,    P.TGL_NOTIF_BLOKIR_KCKR_EMAIL,
                PS.TGL_BLOKIR
            $having
        ";

        $result = odbc_exec($conn, $sql);
        if (!$result) {
            throw new \RuntimeException('Query kandidat gagal: ' . odbc_errormsg($conn));
        }

        $rows = [];
        while ($row = odbc_fetch_object($result)) {
            $rows[] = $row;
        }
        odbc_free_result($result);

        return $rows;
    }

    /**
     * Ambil satu penerbit lengkap dengan agregat kepatuhannya.
     * Mengembalikan null bila ID tidak ditemukan.
     */
    public function fetchOne(int $penerbitId, int $reminderDays = 7): ?object
    {
        $cs   = ComplianceSettings::get();
        $conn = $this->getOracleConnection();

        $rows = $this->fetchCandidates(
            $conn,
            (int) $cs['BatasWaktuKonfirmasiTerbitKaryaCetak'],
            (int) $cs['BatasWaktuKonfirmasiTerbitDigital'],
            (int) $cs['BatasWaktuTeguranKonfirmasiTerbit'],
            $reminderDays,
            $penerbitId,
            true
        );

        return $rows[0] ?? null;
    }

    // ── Penyusunan email ─────────────────────────────────────────────────────

    /**
     * Placeholder mengikuti gaya {{snake_case}} yang dipakai di redaksi
     * (settingparameters, diedit admin lewat Summernote).
     *
     * {{tanggal_blokir}} punya dua arti tergantung jenis email:
     *   - email blokir    : tanggal penerbit BENAR-BENAR diblokir, diambil dari
     *                       riwayat PENERBIT_STATUS (bukan tanggal email dikirim).
     *   - email pengingat : tanggal penerbit AKAN diblokir, yaitu jatuh tempo
     *                       terdekat yang belum terlewat.
     */
    public function buildVars(string $jenis, object $row, int $minPct, ?string $autoBlokirMulaiTanggal = null): array
    {
        // pengingat-kckr sengaja TIDAK memakai deadline per-item (TGL_BLOKIR_KCKR) —
        // penerbit yang masuk jenis ini sudah di bawah ambang KCKR SEKARANG (lihat
        // ComplianceSendNotifications::isEligible), jadi tanggal yang relevan buat
        // mereka adalah kapan fitur auto blokir mulai berlaku, bukan jatuh tempo
        // item yang mungkin sudah lama lewat.
        $tglBlokir = match ($jenis) {
            'blokir-kt', 'blokir-kckr' => $row->TGL_BLOKIR_TERCATAT ?? null,
            'pengingat-kt'             => $row->TGL_BLOKIR_KT      ?? null,
            'pengingat-kckr'           => (trim((string) $autoBlokirMulaiTanggal) !== '' ? $autoBlokirMulaiTanggal : ($row->TGL_BLOKIR_KCKR ?? null)),
        };

        $ts = $tglBlokir ? strtotime((string) $tglBlokir) : false;

        // Email blokir tidak boleh memuat tanggal kosong. Kalau riwayat
        // PENERBIT_STATUS belum punya catatannya (penerbit lama, atau saat
        // preview), pakai AutoBlokir_MulaiTanggal (tanggal resmi mulai berlakunya
        // auto blokir) — fallback ke hari ini kalau setting itu pun kosong.
        if (!$ts && !self::JENIS[$jenis]['reminder']) {
            $mulai = trim((string) $autoBlokirMulaiTanggal);
            $ts    = ($mulai !== '' ? strtotime($mulai) : false) ?: strtotime('today');
        }

        // Sisa hari dihitung antar tengah malam, bukan dari jam berapa proses
        // kebetulan berjalan. Jatuh tempo membawa komponen jam dari
        // CREATEDATE/TANGGAL_TERBIT, sehingga tanpa normalisasi ini selisih 7,4
        // hari akan dibulatkan menjadi "8 hari" di email.
        $sisaHari = $ts
            ? max(0, (int) round((strtotime(date('Y-m-d', $ts)) - strtotime('today')) / 86400))
            : 0;

        return [
            '{{nama_penerbit}}'         => (string) ($row->NAME ?? '-'),
            '{{tanggal_blokir}}'        => $ts ? date('d/m/Y', $ts) : '-',
            '{{jumlah_hari_pengingat}}' => (string) $sisaHari,
            '{{jumlah_konfirmasi}}'     => (string) (int) ($row->BELUM_KONFIRMASI ?? 0),
            '{{jumlah_lewat_batas}}'    => (string) (int) ($row->LEWAT_BATAS_KONFIRMASI ?? 0),
            '{{jumlah_tagihan_sskckr}}' => (string) (int) ($row->TAGIHAN ?? 0),
            '{{persentase_kepatuhan}}'  => (string) (float) ($row->PERSENTASE_KCKR ?? 0),
            '{{threshold_kepatuhan}}'   => (string) $minPct,
        ];
    }

    public function buildHtml(string $redaksi, string $jenis, object $row, int $minPct, ?string $autoBlokirMulaiTanggal = null): string
    {
        $def = self::JENIS[$jenis];

        // Redaksi sudah memuat sapaan, rekap, tautan portal, dan penutup —
        // pembungkus ini hanya menambah kop, badge, dan catatan kaki.
        $isi = strtr($redaksi, $this->buildVars($jenis, $row, $minPct, $autoBlokirMulaiTanggal));

        $judul = htmlspecialchars($def['subject'], ENT_QUOTES, 'UTF-8');
        $badge = $def['reminder'] ? 'badge-warning' : 'badge-danger';
        $label = $def['reminder'] ? 'Pengingat' : 'Pemberitahuan Blokir';
        $tgl   = date('d/m/Y H:i');

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Helvetica, Arial, sans-serif; background-color:#f4f4f4; margin:0; padding:0; color:#333; }
        .container { max-width:600px; margin:30px auto; background:#fff; padding:30px; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,.1); }
        .header { text-align:center; border-bottom:2px solid #0056b3; padding-bottom:20px; margin-bottom:25px; }
        .header h2 { margin:0; color:#0056b3; font-size:22px; }
        .badge { display:inline-block; padding:8px 12px; margin-top:10px; border-radius:4px; font-size:13px; font-weight:bold; text-transform:uppercase; letter-spacing:1px; }
        .badge-warning { background-color:#fff3cd; color:#856404; border:1px solid #ffeeba; }
        .badge-danger { background-color:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }
        .content { line-height:1.6; font-size:15px; }
        .footer { margin-top:28px; padding-top:16px; border-top:1px solid #eee; font-size:12px; color:#888; line-height:1.5; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>{$judul}</h2>
            <span class="badge {$badge}">{$label}</span>
        </div>
        <div class="content">
            {$isi}
        </div>
        <div class="footer">
            Email ini dikirim otomatis oleh sistem SAKEDAP pada {$tgl}. Mohon tidak membalas email ini.<br>
            Perpustakaan Nasional Republik Indonesia.
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Kirim satu email. Kredensial SMTP datang dari tabel MAILSERVER lewat
     * ConfigurationProvider, jadi tidak ada yang perlu dikonfigurasi di sini.
     */
    public function send(array $recipients, string $jenis, string $html): void
    {
        $subject = self::JENIS[$jenis]['subject'];

        Mail::send([], [], function ($message) use ($recipients, $subject, $html) {
            $message->to($recipients)
                ->subject($subject)
                ->from(config('mail.from.address'), config('mail.from.name'))
                ->html($html, 'text/html');
        });
    }
}
