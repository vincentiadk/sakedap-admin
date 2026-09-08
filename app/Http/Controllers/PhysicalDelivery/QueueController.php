<?php

namespace App\Http\Controllers\PhysicalDelivery;

use App\Helpers\AntrianFisik;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QueueController extends Controller
{
    private const CONNECT_TIMEOUT = 5;
    private const QUERY_TIMEOUT = 30;

    /**
     * Kolom yang boleh dipakai untuk urut/cari, sejajar dengan urutan kolom
     * di tabel HTML. null = kolom tampilan saja (nomor urut / aksi).
     */
    private const COLUMNS = [
        null,
        null,
        'a.nomor_antrian',
        'a.waktu_terima',
        'a.cara_datang',
        'a.ekspedisi',
        'a.receipt_no',
        'p.name',
        'a.jumlah_dus',
        'l.nama',
        'a.nama_petugas',
        'a.sumber',
        'a.status',
    ];

    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'content' => 'physical-delivery.queue',
                'lokasi' => QueryAPI::get("
                    select l.id, l.nama
                    from letter_antrian_lokasi l
                    where l.aktif = 1"
                    . (Main::isPerpusnas() ? '' : ' and l.branch_id = ' . (int) session('branch_id')) . "
                    order by l.nama
                ", false, self::CONNECT_TIMEOUT, self::QUERY_TIMEOUT) ?? [],
                'plugins' => [
                    'datatable',
                    'select2',
                    'daterangepicker',
                ],
            ]
        ]);
    }

    public function datatable(Request $request)
    {
        $draw = intval($request->draw ?? 0);
        $start = intval($request->start ?? 0);
        $length = $start + intval($request->length ?? 10);

        // Baris terhapus disembunyikan -- tabel ini memakai soft delete.
        $whereCondition = ['a.delete_date is null'];

        // Wilayah diturunkan dari lokasi dus, bukan dari resinya: dus catatan
        // manual satpam belum punya letter_id, dan justru dus itulah yang
        // paling perlu terlihat. Dus tanpa lokasi tidak bisa diketahui
        // wilayahnya -- ditampilkan ke Perpusnas supaya tidak hilang dari
        // semua orang.
        //
        // Disaring per PERPUSTAKAAN, bukan per provinsi: dus adalah barang
        // fisik yang berada di gedung tertentu, dan yang membukanya petugas
        // gedung itu. Petugas Dinas Provinsi Jawa Timur tidak akan berjalan
        // ke pos satpam Blitar.
        //
        // Berbeda dengan aturan penerimaan (mayHandle) yang berbasis provinsi
        // -- itu wewenang administratif, ini keberadaan barang.
        if (!Main::isPerpusnas()) {
            $whereCondition[] = 'l.branch_id = ' . (int) session('branch_id');
        }

        if ($request->lokasi_id) {
            $whereCondition[] = 'a.lokasi_id = ' . (int) $request->lokasi_id;
        }

        if ($request->cara_datang) {
            $whereCondition[] = "a.cara_datang = '" . $this->escape($request->cara_datang) . "'";
        }

        if ($request->status) {
            $whereCondition[] = "a.status = '" . $this->escape($request->status) . "'";
        }

        if ($request->date) {
            $rentang = explode(' - ', $request->date);

            if (count($rentang) === 2) {
                $mulai = Carbon::parse($rentang[0])->format('Y-m-d');
                $selesai = Carbon::parse($rentang[1])->format('Y-m-d');

                $whereCondition[] = "(a.waktu_terima >= to_date('$mulai', 'YYYY-MM-DD')"
                    . " and a.waktu_terima < to_date('$selesai', 'YYYY-MM-DD') + 1)";
            }
        }

        $search = strtoupper($this->escape($request->search['value'] ?? ''));

        if ($search !== '') {
            $terms = [];

            foreach (self::COLUMNS as $c) {
                // Kolom tanggal dan angka dilewati -- LIKE pada keduanya tidak
                // berguna dan memaksa konversi tipe di Oracle.
                if ($c && $c !== 'a.waktu_terima' && $c !== 'a.jumlah_dus') {
                    $terms[] = "upper($c) like '%$search%'";
                }
            }

            $whereCondition[] = '(' . implode(' or ', $terms) . ')';
        }

        $whereClause = 'where ' . implode(' and ', $whereCondition);

        $orderBy = 'order by a.waktu_terima desc, a.id desc';
        $order = $request->order;

        if ($order && isset(self::COLUMNS[$order[0]['column']]) && self::COLUMNS[$order[0]['column']]) {
            $arah = strtolower($order[0]['dir']) === 'asc' ? 'asc' : 'desc';
            $orderBy = 'order by ' . self::COLUMNS[$order[0]['column']] . ' ' . $arah;
        }

        // Penerbit hanya diketahui lewat resi: dus yang dicatat manual satpam
        // belum punya letter_id, jadi join-nya harus left dan kolomnya boleh
        // kosong -- bukan alasan untuk menyembunyikan barisnya.
        $joins = "
            from letter_antrian a
            left join letter_antrian_lokasi l on l.id = a.lokasi_id
            left join letter lt on lt.letter_id = a.letter_id
            left join penerbit p on p.id = lt.penerbit_id
        ";

        $totalData = QueryAPI::get("
            select count(*) as total from letter_antrian where delete_date is null
        ", true, self::CONNECT_TIMEOUT, self::QUERY_TIMEOUT)->TOTAL ?? 0;

        $totalFiltered = QueryAPI::get("
            select count(*) as total $joins $whereClause
        ", true, self::CONNECT_TIMEOUT, self::QUERY_TIMEOUT)->TOTAL ?? 0;

        $queryData = QueryAPI::get("
            select * from (
                select rownum as rnum, d.* from (
                    select
                        a.id,
                        a.nomor_antrian,
                        a.waktu_terima,
                        a.cara_datang,
                        a.ekspedisi,
                        a.receipt_no,
                        a.pengirim_manual,
                        a.jumlah_dus,
                        a.perkiraan_qty,
                        a.perkiraan_copy,
                        a.nama_petugas,
                        a.sumber,
                        a.status,
                        a.letter_id,
                        a.kiriman_id,
                        a.nomor_dus,
                        l.nama as lokasi_nama,
                        lt.penerbit_id,
                        p.name as penerbit_nama
                    $joins
                    $whereClause
                    $orderBy
                ) d where rownum <= $length
            ) where rnum > $start
        ", false, self::CONNECT_TIMEOUT, self::QUERY_TIMEOUT) ?? [];

        $data = [];
        $no = $start + 1;

        foreach ($queryData as $val) {
            $waktu = $val->WAKTU_TERIMA
                ? '<div>' . Carbon::parse($val->WAKTU_TERIMA)->isoFormat('D MMM Y') . '</div>'
                    . '<small class="text-muted">' . Carbon::parse($val->WAKTU_TERIMA)->format('H:i') . ' WIB</small>'
                : '-';

            $pengirim = $val->CARA_DATANG === 'ekspedisi'
                ? ($val->EKSPEDISI ?: '-')
                : ($val->PENGIRIM_MANUAL ?: '-');

            $resi = $val->RECEIPT_NO ?: '-';

            if ($val->LETTER_ID) {
                $resi .= '<br><a href="' . url('physical-delivery/accept/detail/' . $val->LETTER_ID) . '"'
                    . ' class="small" target="_blank">Lihat resi</a>';
            }

            // Dus tanpa resi belum bisa diketahui penerbitnya -- dibedakan
            // dari resi yang penerbitnya memang kosong di master.
            if (!$val->LETTER_ID) {
                $penerbit = '<span class="text-muted">Belum tertaut resi</span>';
            } elseif ($val->PENERBIT_NAMA) {
                $penerbit = '<div>' . e($val->PENERBIT_NAMA) . '</div>'
                    . '<small class="text-muted">ID ' . e($val->PENERBIT_ID) . '</small>';
            } else {
                $penerbit = '<span class="text-muted">-</span>';
            }

            $perkiraan = [];
            $val->PERKIRAAN_QTY ? $perkiraan[] = $val->PERKIRAAN_QTY . ' judul' : null;
            $val->PERKIRAAN_COPY ? $perkiraan[] = $val->PERKIRAAN_COPY . ' eks' : null;

            $dus = $val->NOMOR_DUS
                ? '<div>Dus ke-' . $val->NOMOR_DUS . ' dari ' . ($val->JUMLAH_DUS ?: '?') . '</div>'
                : '<div>' . ($val->JUMLAH_DUS ?: 0) . ' dus</div>';

            if ($perkiraan) {
                $dus .= '<small class="text-muted">~' . implode(', ', $perkiraan) . '</small>';
            }

            $lokasi = $val->LOKASI_NAMA ?: '<span class="text-muted">Belum ditentukan</span>';

            // Dus yang tertinggal di "diproses" -- petugas lupa menekan
            // Selesai. Ditutup dari sini supaya tidak perlu membuka layar
            // verifikasi hanya untuk itu.
            $aksi = strtolower(trim((string) $val->STATUS)) === AntrianFisik::STATUS_DIPROSES
                ? '<button type="button" class="btn btn-outline-success btn-sm text-nowrap"'
                    . ' onclick="tandaiSelesai(' . (int) $val->ID . ', \'' . e($val->NOMOR_ANTRIAN) . '\')">'
                    . '<i class="ph-check me-1"></i>Selesai</button>'
                : '<span class="text-muted">-</span>';

            $data[] = [
                $no++,
                $aksi,
                '<span class="fw-semibold">' . $val->NOMOR_ANTRIAN . '</span>',
                $waktu,
                '<span class="badge bg-info bg-opacity-10 text-info text-capitalize">' . ($val->CARA_DATANG ?: '-') . '</span>',
                $pengirim,
                $resi,
                $penerbit,
                $dus,
                $lokasi,
                $val->NAMA_PETUGAS ?: '-',
                '<span class="badge bg-secondary bg-opacity-10 text-secondary">' . ($val->SUMBER ?: '-') . '</span>',
                $this->badgeStatus($val->STATUS),
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    /**
     * Warna dan labelnya dipusatkan di AntrianFisik supaya layar antrian dan
     * layar verifikasi tidak pelan-pelan berbeda. Kode di luar daftar tetap
     * ditampilkan apa adanya, bukan disembunyikan.
     */
    private function badgeStatus($status): string
    {
        if (trim((string) $status) === '') {
            return '<span class="badge bg-secondary bg-opacity-10 text-secondary">-</span>';
        }

        return '<span class="badge ' . AntrianFisik::warnaStatus($status) . '">'
            . e(AntrianFisik::labelStatus($status)) . '</span>';
    }

    /**
     * Tutup dus yang tertinggal di "diproses" karena petugas lupa menekan
     * Selesai. Aturan wewenangnya sama dengan penerimaan.
     */
    public function selesaikan(Request $request)
    {
        $id = (int) $request->id;

        // Wewenangnya ditentukan lokasi dus, bukan tujuan resinya -- dus yang
        // dicatat manual satpam belum punya resi sama sekali.
        $dus = QueryAPI::get("
            SELECT a.id, a.nomor_antrian, a.status, lok.branch_id
            FROM letter_antrian a
            LEFT JOIN letter_antrian_lokasi lok ON lok.id = a.lokasi_id
            WHERE a.id = {$id} AND a.delete_date IS NULL
        ", true, self::CONNECT_TIMEOUT, self::QUERY_TIMEOUT);

        if (!$dus) {
            return response()->json(['code' => 404, 'message' => 'Dus tidak ditemukan.'], 404);
        }

        if ($dus->BRANCH_ID && !Main::isPerpusnas()
            && (int) $dus->BRANCH_ID !== (int) session('branch_id')) {
            return response()->json([
                'code' => 403,
                'message' => 'Dus ini berada di perpustakaan lain.'
            ], 403);
        }

        AntrianFisik::tandaiSelesai($id);

        Log::info('Dus ditandai selesai dari daftar antrian', [
            'letter_antrian_id' => $id,
            'nomor_antrian' => $dus->NOMOR_ANTRIAN,
            'oleh' => session('username'),
        ]);

        return response()->json([
            'code' => 200,
            'message' => 'Dus ' . $dus->NOMOR_ANTRIAN . ' ditandai selesai.',
        ]);
    }

    private function escape($value): string
    {
        return str_replace("'", "''", trim((string) $value));
    }
}
