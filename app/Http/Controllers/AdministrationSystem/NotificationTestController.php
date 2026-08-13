<?php

namespace App\Http\Controllers\AdministrationSystem;

use App\Helpers\ComplianceNotification;
use App\Helpers\ComplianceSettings;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Halaman uji redaksi notifikasi kepatuhan.
 *
 * Halaman ini SENGAJA tidak bisa mengirim ke alamat penerbit. Semua pengiriman
 * dipaksa ke alamat tester yang diketik operator. Pengiriman massal ke penerbit
 * asli tetap hanya lewat command compliance:send-notifications, yang punya
 * pemeriksaan status, cooldown, dan pencatatan penanda.
 */
class NotificationTestController extends Controller
{
    public function __construct()
    {
        if (!Main::isSuperAdmin() && !Main::isPerpusnas()) {
            abort(403);
        }
    }

    public function index()
    {
        $notif   = new ComplianceNotification();
        $redaksi = $notif->loadRedaksi();

        $jenis = [];
        foreach (ComplianceNotification::JENIS as $key => $def) {
            $jenis[$key] = [
                'label'    => $def['label'],
                'subject'  => $def['subject'],
                'param'    => $def['param'],
                'flag'     => $def['flag'],
                'date'     => $def['date'],
                'reminder' => $def['reminder'],
                'terisi'   => isset($redaksi[$def['param']]),
            ];
        }

        return view('layouts.index', [
            'data' => [
                'content' => 'administration-system.notification-test',
                'jenis'   => $jenis,
            ]
        ]);
    }

    /**
     * Cari penerbit untuk autocomplete. Dibatasi 20 hasil.
     */
    public function searchPublisher(Request $request)
    {
        $term = trim((string) $request->get('q', ''));

        if (mb_strlen($term) < 3 && !ctype_digit($term)) {
            return response()->json(['results' => []]);
        }

        $safe = str_replace("'", "''", mb_strtoupper($term));

        $cari = ctype_digit($term)
            ? "P.ID = " . (int) $term
            : "UPPER(P.NAME) LIKE '%{$safe}%'";

        // Notifikasi kepatuhan hanya berlaku untuk penerbit ber-ISBN, jadi
        // pencarian dibatasi ke penerbit yang sumber datanya dari DB ISBN.
        $rows = QueryAPI::get("
            SELECT * FROM (
                SELECT P.ID, P.NAME, P.EMAIL1, P.EMAIL2, P.STATUS_AKHIR
                FROM PENERBIT P
                WHERE UPPER(P.SOURCE_DB) = 'ISBN'
                  AND ({$cari})
                ORDER BY P.NAME
            ) WHERE ROWNUM <= 20
        ") ?? [];

        $results = array_map(fn($r) => [
            'id'     => (int) $r->ID,
            'text'   => $r->ID . ' — ' . $r->NAME,
            'email'  => $r->EMAIL1 ?: ($r->EMAIL2 ?: null),
            'status' => trim((string) ($r->STATUS_AKHIR ?? '')) ?: 'Belum ada status',
        ], $rows);

        return response()->json(['results' => $results]);
    }

    /**
     * Render email tanpa mengirim. Dikembalikan sebagai HTML mentah untuk
     * ditampilkan di dalam iframe, plus ringkasan data yang dipakai.
     */
    public function preview(Request $request)
    {
        $request->validate([
            'jenis'    => 'required|string',
            'penerbit' => 'required|integer|min:1',
        ]);

        return $this->render($request->jenis, (int) $request->penerbit, function ($html, $row, $vars, $def) {
            return response()->json([
                'success' => true,
                'html'    => $html,
                'subject' => $def['subject'],
                'penerbit' => [
                    'id'     => (int) $row->ID,
                    'nama'   => $row->NAME,
                    'status' => trim((string) ($row->STATUS_AKHIR ?? '')) ?: 'Belum ada status',
                    'email1' => $row->EMAIL1 ?: null,
                    'email2' => $row->EMAIL2 ?: null,
                ],
                'vars' => $vars,
            ]);
        });
    }

    /**
     * Kirim ke alamat tester. Penanda IS_NOTIF_ dan TGL_NOTIF_ tidak pernah
     * ditulis dari sini — penerbit asli tidak boleh tercatat "sudah
     * dinotifikasi" gara-gara operator menguji redaksi.
     */
    public function send(Request $request)
    {
        $request->validate([
            'jenis'    => 'required|string',
            'penerbit' => 'required|integer|min:1',
            'email'    => 'required|email',
        ]);

        $email = trim($request->email);

        return $this->render($request->jenis, (int) $request->penerbit, function ($html, $row, $vars, $def) use ($request, $email) {
            $notif = new ComplianceNotification();

            try {
                $notif->send([$email], $request->jenis, $html);
            } catch (\Throwable $e) {
                Log::channel('daily')->error('NotificationTest: gagal kirim', [
                    'jenis'    => $request->jenis,
                    'penerbit' => (int) $row->ID,
                    'tujuan'   => $email,
                    'error'    => $e->getMessage(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengirim: ' . $e->getMessage(),
                ], 500);
            }

            Log::channel('daily')->info('NotificationTest: email uji terkirim', [
                'jenis'    => $request->jenis,
                'penerbit' => (int) $row->ID,
                'tujuan'   => $email,
                'oleh'     => session('username', 'unknown'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email uji "' . $def['label'] . '" terkirim ke ' . $email,
            ]);
        });
    }

    /**
     * Bagian yang sama antara preview dan send: validasi jenis, ambil penerbit,
     * pastikan redaksinya ada, lalu susun HTML.
     */
    private function render(string $jenis, int $penerbitId, callable $then)
    {
        if (!ComplianceNotification::jenisValid($jenis)) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis notifikasi tidak dikenal.',
            ], 422);
        }

        $notif = new ComplianceNotification();
        $def   = ComplianceNotification::JENIS[$jenis];

        $redaksi = $notif->loadRedaksi();
        if (!isset($redaksi[$def['param']])) {
            return response()->json([
                'success' => false,
                'message' => "Redaksi '{$def['param']}' belum diisi. Lengkapi dulu di Pengaturan Kepatuhan.",
            ], 422);
        }

        try {
            $row = $notif->fetchOne($penerbitId);
        } catch (\Throwable $e) {
            Log::channel('daily')->error('NotificationTest: query penerbit gagal', [
                'penerbit' => $penerbitId,
                'error'    => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data penerbit: ' . $e->getMessage(),
            ], 500);
        }

        if (!$row) {
            return response()->json([
                'success' => false,
                'message' => "Penerbit dengan ID {$penerbitId} tidak ditemukan.",
            ], 404);
        }

        $cs        = ComplianceSettings::get();
        $minPct    = (int) $cs['BatasMinimumKepatuhanKCKR'];
        $mulaiAuto = (string) ($cs['AutoBlokir_MulaiTanggal'] ?? '');
        $vars      = $notif->buildVars($jenis, $row, $minPct, $mulaiAuto);
        $html      = $notif->buildHtml($redaksi[$def['param']], $jenis, $row, $minPct, $mulaiAuto);

        return $then($html, $row, $vars, $def);
    }
}
