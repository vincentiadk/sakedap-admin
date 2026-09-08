<?php

namespace App\Http\Controllers\AdministrationSystem;

use App\Helpers\Main;
use App\Helpers\QueryAPI;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Pengaturan antrian penerimaan fisik: master Lokasi dan master PC.
 * Keduanya sengaja disatukan dalam satu halaman karena PC selalu terikat
 * ke sebuah lokasi -- memisahkannya membuat petugas bolak-balik menu.
 */
class QueueSettingController extends Controller
{
    private const CONNECT_TIMEOUT = 5;
    private const QUERY_TIMEOUT = 30;

    public function index()
    {
        return view('layouts.index', [
            'data' => [
                'content' => 'administration-system.queue-setting',
                'plugins' => [
                    'datatable',
                ],
            ]
        ]);
    }

    // ----------------------------------------------------------------
    // LOKASI
    // ----------------------------------------------------------------

    public function lokasiDatatable(Request $request)
    {
        $rows = QueryAPI::get("
            select
                l.id, l.nama, l.keterangan, l.aktif,
                (select count(*) from letter_antrian_pc pc where pc.lokasi_id = l.id) as jml_pc
            from letter_antrian_lokasi l
            " . $this->batasWilayah("l") . "
            order by l.nama
        ", false, self::CONNECT_TIMEOUT, self::QUERY_TIMEOUT) ?? [];

        $data = [];
        $no = 1;

        foreach ($rows as $val) {
            $aksi = '
                <button type="button" class="btn btn-warning btn-sm" onclick="onUpdateLokasi(' . $val->ID . ')" title="Edit">
                    <i class="ph-pencil"></i>
                </button>
                <button type="button" class="btn btn-danger btn-sm ms-1" onclick="onDestroyLokasi(' . $val->ID . ')" title="Hapus">
                    <i class="ph-trash"></i>
                </button>
            ';

            $data[] = [
                $no++,
                $aksi,
                $val->NAMA,
                $val->KETERANGAN ?: '-',
                (int) $val->JML_PC . ' PC',
                $this->badgeAktif($val->AKTIF),
            ];
        }

        return response()->json([
            'draw' => intval($request->draw ?? 0),
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),
            'data' => $data,
        ]);
    }

    public function lokasiShow(Request $request)
    {
        $row = QueryAPI::get("
            select id, nama, keterangan, aktif
            from letter_antrian_lokasi where id = " . (int) $request->id
        , true, self::CONNECT_TIMEOUT, self::QUERY_TIMEOUT);

        return response()->json($row ? ['code' => 200, 'data' => $row] : ['code' => 404, 'message' => 'Data tidak ditemukan'], $row ? 200 : 404);
    }

    public function lokasiSave(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'nama' => 'required|max:100',
            'keterangan' => 'nullable|max:255',
        ], [
            'nama.required' => 'Nama lokasi tidak boleh kosong',
            'nama.max' => 'Nama lokasi maksimal 100 karakter',
            'keterangan.max' => 'Keterangan maksimal 255 karakter',
        ]);

        if ($validation->fails()) {
            return response()->json(['code' => 400, 'error' => $validation->errors()->all()], 400);
        }

        if ($request->id && !$this->lokasiMilikSendiri((int) $request->id)) {
            return $this->tolakBukanMilikSendiri();
        }

        $payload = [
            'nama' => trim($request->nama),
            'keterangan' => trim((string) $request->keterangan) ?: null,
            'aktif' => $request->aktif ? 1 : 0,
            'update_date' => date('Y-m-d H:i:s'),
        ];

        try {
            // Sama seperti pcSave(): penolakan datang lewat nilai kembalian,
            // bukan exception, jadi harus diperiksa.
            if ($request->id) {
                $berhasil = (bool) QueryAPI::update('letter_antrian_lokasi', (int) $request->id, $payload, false);
                $pesan = 'Lokasi berhasil diperbarui';
            } else {
                // Lokasi baru selalu milik perpustakaan pembuatnya.
                $payload['branch_id'] = (int) session('branch_id');
                $payload['create_date'] = date('Y-m-d H:i:s');
                $berhasil = (bool) QueryAPI::create('letter_antrian_lokasi', $payload, false);
                $pesan = 'Lokasi berhasil ditambahkan';
            }

            if (!$berhasil) {
                return response()->json([
                    'code' => 500,
                    'message' => 'Data lokasi ditolak database dan tidak tersimpan. '
                        . 'Pesan lengkapnya ada di storage/logs/sakedap-api-*.log.'
                ], 500);
            }

            return response()->json(['code' => 200, 'message' => $pesan]);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
        }
    }

    public function lokasiDestroy(Request $request)
    {
        $id = (int) $request->id;

        if (!$this->lokasiMilikSendiri($id)) {
            return $this->tolakBukanMilikSendiri();
        }

        // Lokasi yang masih dipakai tidak boleh hilang -- PC dan riwayat
        // mutasi akan menggantung tanpa acuan.
        $dipakai = QueryAPI::get("
            select
                (select count(*) from letter_antrian_pc where lokasi_id = $id) as jml_pc,
                (select count(*) from letter_antrian where lokasi_id = $id) as jml_antrian
            from dual
        ", true, self::CONNECT_TIMEOUT, self::QUERY_TIMEOUT);

        if ($dipakai && ((int) $dipakai->JML_PC > 0 || (int) $dipakai->JML_ANTRIAN > 0)) {
            return response()->json([
                'code' => 422,
                'message' => 'Lokasi ini masih dipakai oleh ' . (int) $dipakai->JML_PC . ' PC dan '
                    . (int) $dipakai->JML_ANTRIAN . ' antrian. Nonaktifkan saja, jangan dihapus.'
            ], 422);
        }

        try {
            QueryAPI::delete('letter_antrian_lokasi', $id);

            return response()->json(['code' => 200, 'message' => 'Lokasi telah dihapus']);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => 'Gagal menghapus: ' . $e->getMessage()], 500);
        }
    }

    // ----------------------------------------------------------------
    // PC
    // ----------------------------------------------------------------

    public function pcDatatable(Request $request)
    {
        $rows = QueryAPI::get("
            select pc.id, pc.ip_address, pc.keterangan, pc.aktif, pc.last_seen,
                   pc.lokasi_id, l.nama as lokasi_nama
            from letter_antrian_pc pc
            join letter_antrian_lokasi l on l.id = pc.lokasi_id
            " . $this->batasWilayah("l") . "
            order by l.nama, pc.ip_address
        ", false, self::CONNECT_TIMEOUT, self::QUERY_TIMEOUT) ?? [];

        $data = [];
        $no = 1;

        foreach ($rows as $val) {
            $aksi = '
                <button type="button" class="btn btn-warning btn-sm" onclick="onUpdatePc(' . $val->ID . ')" title="Edit">
                    <i class="ph-pencil"></i>
                </button>
                <button type="button" class="btn btn-danger btn-sm ms-1" onclick="onDestroyPc(' . $val->ID . ')" title="Hapus">
                    <i class="ph-trash"></i>
                </button>
            ';

            $data[] = [
                $no++,
                $aksi,
                '<span class="fw-semibold font-monospace">' . $val->IP_ADDRESS . '</span>',
                $val->LOKASI_NAMA ?: '<span class="text-danger">Lokasi terhapus</span>',
                $val->KETERANGAN ?: '-',
                $val->LAST_SEEN ?: '<span class="text-muted">Belum pernah</span>',
                $this->badgeAktif($val->AKTIF),
            ];
        }

        return response()->json([
            'draw' => intval($request->draw ?? 0),
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data),
            'data' => $data,
        ]);
    }

    public function pcShow(Request $request)
    {
        $row = QueryAPI::get("
            select id, ip_address, lokasi_id, keterangan, aktif
            from letter_antrian_pc where id = " . (int) $request->id
        , true, self::CONNECT_TIMEOUT, self::QUERY_TIMEOUT);

        return response()->json($row ? ['code' => 200, 'data' => $row] : ['code' => 404, 'message' => 'Data tidak ditemukan'], $row ? 200 : 404);
    }

    public function pcSave(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'ip_address' => 'required|ip',
            'lokasi_id' => 'required|integer',
            'keterangan' => 'nullable|max:255',
        ], [
            'ip_address.required' => 'Alamat IP tidak boleh kosong',
            'ip_address.ip' => 'Alamat IP tidak valid',
            'lokasi_id.required' => 'Lokasi wajib dipilih',
            'keterangan.max' => 'Keterangan maksimal 255 karakter',
        ]);

        if ($validation->fails()) {
            return response()->json(['code' => 400, 'error' => $validation->errors()->all()], 400);
        }

        // PC hanya boleh ditempelkan ke lokasi milik sendiri.
        //
        // Baris lokasinya sekalian diambil karena BRANCH_ID pada
        // LETTER_ANTRIAN_PC wajib diisi dan terikat FK gabungan
        // (BRANCH_ID, LOKASI_ID) ke LETTER_ANTRIAN_LOKASI -- jadi nilainya
        // harus mengikuti lokasi yang dipilih, bukan branch pengguna. Untuk
        // pengguna Perpusnas keduanya bisa berbeda.
        $lokasi = QueryAPI::get(
            "select id, branch_id from letter_antrian_lokasi where id = " . (int) $request->lokasi_id,
            true,
            self::CONNECT_TIMEOUT,
            self::QUERY_TIMEOUT
        );

        if (!$lokasi) {
            return response()->json(['code' => 422, 'message' => 'Lokasi tidak ditemukan.'], 422);
        }

        if (!Main::isPerpusnas() && (int) $lokasi->BRANCH_ID !== (int) session('branch_id')) {
            return $this->tolakBukanMilikSendiri();
        }

        $branchId = (int) $lokasi->BRANCH_ID;

        // Satu IP hanya boleh terdaftar sekali, kalau tidak lokasi PC jadi
        // ambigu. Dibatasi per perpustakaan mengikuti UQ_LA_PC_IP
        // (BRANCH_ID, IP_ADDRESS): IP yang sama di jaringan perpustakaan lain
        // bukan bentrok, dan menolaknya akan memblokir pendaftaran yang sah.
        $ip = str_replace("'", "''", $request->ip_address);
        $kecuali = $request->id ? ' and id <> ' . (int) $request->id : '';

        $kembar = QueryAPI::get(
            "select count(*) as total from letter_antrian_pc
             where branch_id = {$branchId} and ip_address = '$ip'$kecuali",
            true,
            self::CONNECT_TIMEOUT,
            self::QUERY_TIMEOUT
        );

        if ($kembar && (int) $kembar->TOTAL > 0) {
            return response()->json([
                'code' => 422,
                'message' => 'Alamat IP ' . $request->ip_address . ' sudah terdaftar.'
            ], 422);
        }

        $payload = [
            'branch_id' => $branchId,
            'ip_address' => $request->ip_address,
            'lokasi_id' => (int) $request->lokasi_id,
            'keterangan' => trim((string) $request->keterangan) ?: null,
            'aktif' => $request->aktif ? 1 : 0,
            'update_date' => date('Y-m-d H:i:s'),
        ];

        try {
            // Hasil simpan wajib dibaca: QueryAPI menolak lewat nilai
            // kembalian (false / array kosong), bukan exception. Tanpa ini
            // penolakan Oracle tetap dilaporkan "berhasil" ke petugas.
            if ($request->id) {
                $berhasil = (bool) QueryAPI::update('letter_antrian_pc', (int) $request->id, $payload, false);
                $pesan = 'PC berhasil diperbarui';
            } else {
                $payload['create_date'] = date('Y-m-d H:i:s');
                $berhasil = (bool) QueryAPI::create('letter_antrian_pc', $payload, false);
                $pesan = 'PC berhasil ditambahkan';
            }

            if (!$berhasil) {
                return response()->json([
                    'code' => 500,
                    'message' => 'Data PC ditolak database dan tidak tersimpan. '
                        . 'Pesan lengkapnya ada di storage/logs/sakedap-api-*.log.'
                ], 500);
            }

            return response()->json(['code' => 200, 'message' => $pesan]);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
        }
    }

    public function pcDestroy(Request $request)
    {
        $pc = QueryAPI::get(
            "select lokasi_id from letter_antrian_pc where id = " . (int) $request->id,
            true,
            self::CONNECT_TIMEOUT,
            self::QUERY_TIMEOUT
        );

        if ($pc && !$this->lokasiMilikSendiri((int) $pc->LOKASI_ID)) {
            return $this->tolakBukanMilikSendiri();
        }

        try {
            QueryAPI::delete('letter_antrian_pc', (int) $request->id);

            return response()->json(['code' => 200, 'message' => 'PC telah dihapus']);
        } catch (\Exception $e) {
            return response()->json(['code' => 500, 'message' => 'Gagal menghapus: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Daftar lokasi aktif untuk isian dropdown PC.
     */
    public function lokasiOptions()
    {
        return response()->json([
            'code' => 200,
            'data' => QueryAPI::get("
                select l.id, l.nama
                from letter_antrian_lokasi l
                " . $this->batasWilayah('l') . "
                order by l.nama
            ", false, self::CONNECT_TIMEOUT, self::QUERY_TIMEOUT) ?? [],
        ]);
    }

    /**
     * Batas wilayah untuk query lokasi: tiap perpustakaan mengelola lokasi
     * dan PC-nya sendiri.
     *
     * Sengaja per PERPUSTAKAAN, bukan per provinsi. Lokasi adalah tempat
     * fisik di satu gedung -- pos satpam Blitar bukan urusan Dinas Provinsi
     * Jawa Timur, walaupun keduanya seprovinsi. Ini memang lebih ketat
     * daripada aturan penerimaan yang berbasis provinsi.
     */
    private function batasWilayah(string $alias): string
    {
        return Main::isPerpusnas()
            ? ''
            : "where {$alias}.branch_id = " . (int) session('branch_id');
    }

    /**
     * Pastikan sebuah lokasi memang milik perpustakaan pengguna sebelum boleh
     * diubah atau dihapus. Dipanggil sebelum setiap penulisan -- id yang
     * dikirim browser tidak boleh dipercaya.
     */
    private function lokasiMilikSendiri(int $lokasiId): bool
    {
        if (Main::isPerpusnas()) {
            return true;
        }

        $row = QueryAPI::get(
            "select branch_id from letter_antrian_lokasi where id = {$lokasiId}",
            true,
            self::CONNECT_TIMEOUT,
            self::QUERY_TIMEOUT
        );

        return $row && (int) $row->BRANCH_ID === (int) session('branch_id');
    }

    private function tolakBukanMilikSendiri()
    {
        return response()->json([
            'code' => 403,
            'message' => 'Lokasi ini milik perpustakaan lain.'
        ], 403);
    }

    private function badgeAktif($aktif): string
    {
        return (int) $aktif === 1
            ? '<span class="badge bg-success bg-opacity-10 text-success">Aktif</span>'
            : '<span class="badge bg-secondary bg-opacity-10 text-secondary">Nonaktif</span>';
    }
}
