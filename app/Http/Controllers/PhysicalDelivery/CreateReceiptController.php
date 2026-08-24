<?php

namespace App\Http\Controllers\PhysicalDelivery;

use App\Helpers\ISBN;
use App\Helpers\Main;
use App\Helpers\QueryAPI;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CreateReceiptController extends Controller
{
    public function index()
    {
        $worksheetAnalog = Main::COLLECTION_ANALOG;
        $worksheetPrinted = Main::COLLECTION_PRINTED;

        $media = QueryAPI::get("
            select
                collectionmedias.*
            from
                collectionmedias
            join
                worksheets on worksheets.id = collectionmedias.worksheet_id
            where
                worksheets.category in ('$worksheetAnalog','$worksheetPrinted') and
                collectionmedias.depositformat_code is not null
        ");

        return view('layouts.index', [
            'data' => [
                'deliveryService' => QueryAPI::get("select * from jasa_pengiriman") ?? [],
                'content' => 'physical-delivery.create-receipt',
                'acceptDefault' => 2,
                'media' => $media ?? [],
                'plugins' => [
                    'select2',
                    'datatable',
                    'daterangepicker',
                    'lightbox',
                ]
            ]
        ]);
    }

    public function searchISBN(Request $request)
    {
        $code = str_replace('-', '', $request->code);
        $branchId = session('branch_id');

        $data = ISBN::get('search', [
            'code' => $code
        ], true);

        $letterDetail = QueryAPI::get(
            "
            select
                nvl(sum(letter_detail.qty_accept), 0) as total_letter_detail
            from
                letter_detail
            left join
                letter on letter.letter_id = letter_detail.letter_id
            where
                letter.branch_id = $branchId and
                replace(letter_detail.isbn, '-', '') = '$code'",
            true
        );

        $collection = QueryAPI::get(
            "
            select
                count(collections.id) as total
            from
                collections
            left join
                letter_detail on letter_detail.letter_detail_id = collections.letter_detail_id
            left join
                letter on letter.letter_id = letter_detail.letter_id
            where
                letter.branch_id = $branchId and
                replace(collections.isbn, '-', '') = '$code' and
                collections.source_id = 6",
            true
        );

        $totalLetterDetail = $letterDetail->TOTAL_LETTER_DETAIL ?? 0;
        $totalCollection = $collection->TOTAL ?? 0;
        $totalSystem = 0;
        $maxAccept = (!Main::isSuperAdmin() && !Main::isPerpusnas()) ? 1 : 2;
        $optionAccept = [];

        if ($totalLetterDetail > 0) {
            $totalSystem = $totalLetterDetail;
        } else if ($totalCollection > 0) {
            $totalSystem = $totalCollection;
        }

        if ($totalSystem == 0) {
            $totalAccept = $maxAccept;
            $totalReject = 0;
        } else if ($totalSystem == 1) {
            $totalAccept = min(1, $maxAccept);
            $totalReject = $maxAccept - $totalAccept;
        } else {
            $totalAccept = 0;
            $totalReject = $maxAccept;
        }

        for ($i = 0; $i <= $maxAccept; $i++) {
            $optionSelected = $i == $totalAccept ? 'selected' : '';
            $optionAccept[] = '<option value="' . $i . '" ' . $optionSelected . '>' . $i . '</option>';
        }

        $linkCover = Main::getCoverISBN($data->cover_file_name ?? null);
        $title = $data->title ?? '';

        $fileCover = '
            <a href="' . $linkCover . '" data-lightbox="cover-' . $code . '" data-title="' . $title . '">
                <img src="' . $linkCover . '" class="img img-fluid img-thumbnail" style="max-width:70px;">
            </a>
        ';

        return response()->json([
            'data' => $data,
            'totalAccept' => $totalAccept,
            'optionAccept' => $optionAccept,
            'totalReject' => $totalReject,
            'totalSystem' => $totalSystem,
            'fileCover' => $fileCover,
        ]);
    }

    public function selectCatalog(Request $request)
    {
        $id = $request->id;
        $data = QueryAPI::get("
            select
                catalogs.*,
                worksheets.name as name_worksheet,
                penerbit.name as name_penerbit
            from
                catalogs
            left join
                worksheets on worksheets.id = catalogs.worksheet_id
            left join
                penerbit on penerbit.id = catalogs.penerbit_id
            where
                catalogs.id = $id
        ", true);

        return response()->json($data);
    }

    public function submitted(Request $request)
    {
        if ($request->ajax()) {
            $validation = Validator::make($request->all(), [
                'delivery_service_id' => 'required',
                'accept_date' => 'required',
                'phone' => 'required|min_digits:8|max_digits:13|numeric',
                'executor_id' => 'required',
                'branch_id' => 'required',
                'sender_name' => 'required',
            ], [
                'delivery_service_id.required' => 'Jasa kirim tidak boleh kosong',
                'accept_date.required' => 'Tanggal terima tidak boleh kosong',
                'phone.required' => 'Telepon tidak boleh kosong',
                'phone.min_digits' => 'Telepon minimal 8 digit',
                'phone.max_digits' => 'Telepon maksimal 13 digit',
                'phone.numeric' => 'Telepon harus angka',
                'executor_id.required' => 'Pelaksana serah tidak boleh kosong',
                'branch_id.required' => 'Tujuan tidak boleh kosong',
                'sender_name.required' => 'Nama pengirim tidak boleh kosong',
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'code' => 400,
                    'error' => $validation->errors()->all(),
                ]);
            } else {
                try {
                    $deliveryServiceId = $request->delivery_service_id;
                    $receiptNumber = $request->receipt;

                    if ($deliveryServiceId == 1) {
                        $receiptNumber = 'LSG' . date('YmdHis');
                    }

                    // Dicek kombinasi resi + jasa kirim, bukan resi doang — nomor resi
                    // dari kurir berbeda bisa kebetulan sama (mis. dua-duanya "123456").
                    $receiptNumberSafe = str_replace("'", "''", $receiptNumber);
                    if (QueryAPI::get("select LETTER_ID from letter where receipt_no = '$receiptNumberSafe' and jasa_pengiriman_id = " . (int) $deliveryServiceId, true)) {
                        return response()->json([
                            'code' => 400,
                            'error' => ['Resi ini sudah terdaftar untuk jasa kirim yang sama']
                        ]);
                    }

                    $deliveryService = QueryAPI::get("select * from jasa_pengiriman where id = $deliveryServiceId", true);

                    if (!$deliveryService) {
                        return response()->json([
                            'code' => 500,
                            'message' => 'Jasa kirim tidak ditemukan di database'
                        ]);
                    }

                    $now = date('Y-m-d H:i:s');
                    $currentUser = session('username');
                    $currentIp = $request->ip();
                    $cacheDuration = 60;

                    $weight = 0;
                    // Tanggal resi (waybill_date) TIDAK dicek real-time ke RajaOngkir lagi
                    // di sini — API-nya bisa lambat/timeout (10 detik) dan bikin proses
                    // simpan kerasa ngehang. Nilainya diisi tanggal sekarang dulu, lalu
                    // dikoreksi belakangan oleh job terjadwal (lihat SyncWaybillDate).
                    $letterDate = $now;
                    $status = 'DITERIMA PENUH';
                    $totalPackage = 0;

                    $auditData = [
                        'create_date' => $now,
                        'create_by' => $currentUser,
                        'create_terminal' => $currentIp,
                        'update_date' => $now,
                        'update_by' => $currentUser,
                        'update_terminal' => $currentIp,
                    ];

                    $letterData = array_merge([
                        'type_of_delivery' => $deliveryServiceId == 1 ? 'Datang Langsung' : 'Pos',
                        'letter_date' => $letterDate,
                        'letter_number' => $request->cover_letter_number,
                        'accept_date' => $request->accept_date . ' ' . date('H:i:s', strtotime($now)),
                        'sender' => $request->sender_name,
                        'phone' => $request->phone,
                        'is_printed' => $request->param == 'save-print' ? 1 : 0,
                        'publisher_id' => $request->executor_id,
                        'is_sendedemail' => $request->param == 'save-send-email' ? 1 : 0,
                        'lang' => 'id',
                        'penerbit_id' => $request->executor_id,
                        'jasa_pengiriman_id' => $deliveryServiceId,
                        'branch_id' => $request->branch_id,
                        'receipt_no' => $receiptNumber,
                        'berat' => $weight,
                        'proses_by' => session('username'),
                        'sent_date' => $now,
                        'check_date' => $now,
                    ], $auditData);

                    $letter = QueryAPI::create('letter', $letterData, false);
                    $executorId = (int) $request->executor_id;
                    // Join kabupaten sekalian — dipakai buat isi publisher_city/publisher_address
                    // koleksi terbitan berkala yang tidak pakai katalog (lihat blok cp di bawah).
                    $letterExecutor = QueryAPI::get("
                        select
                            penerbit.*,
                            kabupaten.namakab as namakab
                        from
                            penerbit
                        left join
                            kabupaten on kabupaten.id = penerbit.city_id
                        where
                            penerbit.id = $executorId
                    ", true);

                    if (!$letter) {
                        return response()->json([
                            'code' => 500,
                            'message' => 'Gagal membuat surat'
                        ]);
                    }

                    $letter = QueryAPI::get("select * from letter where letter_id = " . ($letter->LETTER_ID ?? 0), true);

                    if (!$letter) {
                        return response()->json([
                            'code' => 500,
                            'message' => 'Gagal mengambil data surat setelah dibuat'
                        ]);
                    }

                    if ($request->ci && is_array($request->ci)) {
                        foreach ($request->ci as $key => $ci) {
                            $totalPackage++;

                            $code = $request->ci_code[$key] ?? null;
                            if (!$code) continue;

                            $isbnCacheKey = "isbn:{$code}";
                            $isbn = Cache::remember($isbnCacheKey, $cacheDuration, function () use ($code) {
                                return ISBN::get('search', ['code' => $code], true);
                            });

                            if (!$isbn) {
                                continue;
                            }

                            $qtyAccept = (int) ($request->ci_qty_accept[$key] ?? 0);
                            $qtyReject = (int) ($request->ci_qty_reject[$key] ?? 0);

                            if ($qtyAccept === 0 && $qtyReject === 0) continue;

                            $qrcbn = ($request->ci_qrcbn[$key] ?? null);
                            $isbd = ($request->ci_isbd[$key] ?? null);

                            if ($qtyReject > 0) $status = 'DITERIMA PARSIAL';

                            $catalog = null;

                            if ($isbn->is_kdt_valid == 1) {
                                $catalogId = $isbn->catalog_id;
                                $catalogCacheKey = "catalog:{$catalogId}";

                                $catalog = Cache::remember($catalogCacheKey, $cacheDuration, function () use ($catalogId) {
                                    return QueryAPI::get("select * from catalogs where id = {$catalogId}", true);
                                });
                            }

                            $description = '';

                            if (isset($request->ci_description[$key]) && is_array($request->ci_description[$key])) {
                                $description = implode(';', $request->ci_description[$key]);
                            }

                            $letterDetailData = [
                                'title' => $isbn->title,
                                'copy' => $qtyAccept + $qtyReject,
                                'quantity' => 1,
                                'letter_id' => $letter->LETTER_ID ?? null,
                                'remark' => $description,
                                'author' => $isbn->kepeng,
                                'publisher' => $isbn->nama_penerbit,
                                'isbn' => $code,
                                'publish_year' => $isbn->tahun_terbit,
                                'isbn_status' => 'berISBN',
                                'is_receivedate' => 1,
                                'penerbit_isbn_id' => $isbn->id ?? null,
                                'catalog_id' => $isbn->is_kdt_valid == 1 ? $isbn->catalog_id : null,
                                'qty_accept' => $qtyAccept,
                                'qty_reject' => $qtyReject,
                                'province_id' => $isbn->province_id,
                                'kab_id' => $catalog->CITY_ID ?? null,
                                'deskripsifisik' => $catalog->DESCRIPTION ?? null,
                                'sinopsis' => $isbn->sinopsis,
                                'cleaning_note' => $isbn->keterangan,
                                'jenis_media' => $isbn->jenis_media,
                                'collection_type_id' => 2,
                                'penerbit_terbitan_id' => $isbn->ptid,
                                'penerbit_id' => $isbn->penerbit_id ?? null,
                                'nomorpanggiljilid' => $isbn->keterangan,
                                'qrcbn' => $qrcbn,
                                'isbd' => $isbd,
                                'received_by' => $currentUser,
                                'received_date' => $now,
                                'checked' => 1,
                            ];
                            
                            $letterDetail = QueryAPI::create('letter_detail', $letterDetailData, false);

                            if ($qtyAccept > 0) {
                                QueryAPI::setReceiveDate([
                                    'LetterDetailId' => $letterDetail->ID ?? '',
                                    'NomorISBN' => $letterDetail->ISBN ?? '',
                                    'IsPerpusnas' => ((int) $request->branch_id) == 37 ? 1 : 0,
                                    'IsProvinsi' => ((int) $request->branch_id) != 37 ? 1 : 0,
                                    'TanggalTerima' => date('Y-m-d'),
                                ]);
                            }
                        }
                    }

                    if ($request->cni && is_array($request->cni)) {
                        foreach ($request->cni as $key => $cni) {
                            $totalPackage++;

                            $qtyReject = (int) ($request->cni_qty_reject[$key] ?? 0);
                            if ($qtyReject > 0) $status = 'DITERIMA PARSIAL';

                            $catalogId = $request->cni_catalog_id[$key] ?? null;
                            $catalog = null;

                            if ($catalogId) {
                                $catalogCacheKey = "catalog:detail:{$catalogId}";

                                $catalog = Cache::remember($catalogCacheKey, $cacheDuration, function () use ($catalogId) {
                                    $catalogQuery = "
                                        select
                                            catalogs.*,
                                            penerbit.name as name_penerbit,
                                            penerbit.alamat as alamat_penerbit,
                                            kabupaten.namakab as namakab,
                                            kabupaten.propinsiid as propinsiid
                                        from
                                            catalogs
                                        left join
                                            penerbit on penerbit.id = catalogs.penerbit_id
                                        left join
                                            kabupaten on kabupaten.id = penerbit.city_id
                                        where
                                            catalogs.id = $catalogId
                                    ";

                                    return QueryAPI::get($catalogQuery, true);
                                });
                            }

                            $qtyAccept = (int) ($request->cni_qty_accept[$key] ?? 0);
                            $title = $request->cni_title[$key] ?? null;
                            $author = $request->cni_author[$key] ?? null;
                            $year = $request->cni_year[$key] ?? null;
                            $physicalDescription = $request->cni_physical_description[$key] ?? null;
                            $executor = $request->cni_executor[$key] ?? ($letterExecutor->NAME ?? null);
                            $binding = $request->cni_binding[$key] ?? null;
                            $qrcbn = $request->cni_qrcbn[$key] ?? null;
                            $isbd = $request->cni_isbd[$key] ?? null;
                            $media = strtoupper($request->cni_type[$key] ?? '');
                            $getCollectionMedia = null;

                            if ($qtyAccept === 0 && $qtyReject === 0) continue;

                            if ($media) {
                                $getCollectionMedia = Cache::remember('collectionmedias:' . $media, $cacheDuration, function () use ($media) {
                                    return QueryAPI::get("select * from collectionmedias where upper(name) = '$media'", true);
                                });
                            }

                            $description = '';

                            if (isset($request->cni_description[$key]) && is_array($request->cni_description[$key])) {
                                $description = implode(';', $request->cni_description[$key]);
                            }

                            $price = 0;

                            if (isset($request->cni_price[$key])) {
                                $price = str_replace([',', '.'], '', $request->cni_price[$key]);
                            }

                            $letterDetailData = [
                                'title' => $title,
                                'copy' => $qtyAccept + $qtyReject,
                                'quantity' => 1,
                                'price' => $price,
                                'letter_id' => $letter->LETTER_ID ?? null,
                                'remark' => $description,
                                'author' => $author,
                                'publisher' => $catalog->NAME_PENERBIT ?? $executor,
                                'publisher_address' => $catalog->ALAMAT_PENERBIT ?? null,
                                'publish_year' => $year,
                                'publisher_city' => $catalog->NAMAKAB ?? null,
                                'is_receivedate' => 1,
                                'catalog_id' => $catalogId,
                                'qty_accept' => $qtyAccept,
                                'qty_reject' => $qtyReject,
                                'province_id' => $catalog->PROPINSIID ?? ($letterExecutor->PROVINCE_ID ?? null),
                                'kab_id' => $catalog->CITY_ID ?? ($letterExecutor->CITY_ID ?? null),
                                'collection_type_id' => $catalog->COLLECTIONMEDIA_ID ?? ($getCollectionMedia->ID ?? null),
                                'deskripsifisik' => $physicalDescription,
                                'jenis_media' => $getCollectionMedia->NAME ?? null,
                                'penerbit_id' => $catalog->PENERBIT_ID ?? $executorId,
                                'nomorpanggiljilid' => $binding,
                                'qrcbn' => $qrcbn,
                                'isbd' => $isbd,
                                'received_by' => $currentUser,
                                'received_date' => $now,
                                'checked' => 1,
                            ];

                            QueryAPI::create('letter_detail', $letterDetailData, false);
                        }
                    }

                    if ($request->cp && is_array($request->cp)) {
                        foreach ($request->cp as $key => $cp) {
                            $totalPackage++;

                            $catalogId = isset($request->cp_catalog_id[$key]) ? $request->cp_catalog_id[$key] : null;
                            $manualTitle = isset($request->cp_manual_title[$key]) ? $request->cp_manual_title[$key] : null;

                            // ISSN & Jenis Media diisi SEKALI per judul (bukan per edisi) —
                            // cuma relevan waktu tidak pakai katalog dari database, karena
                            // katalog sudah punya data itu sendiri. Diterapkan ke SEMUA baris
                            // edisi (cpe) di bawah judul ini.
                            //
                            // PENTING: simpan ke kolom ISSN, JANGAN ke kolom ISBN — kolom ISBN
                            // dipakai DeliveryVerificationController::index() buat memutuskan
                            // baris ini masuk tab "Koleksi ISBN" atau "Terbitan Berkala" waktu
                            // tanda terima ini dibuka lagi buat diedit (isbn diisi -> selalu
                            // dianggap ISBN, edisi_serial/TTES tidak pernah dicek).
                            $issn = isset($request->cp_issn[$key]) ? trim((string) $request->cp_issn[$key]) : null;
                            $kalaTerbit = isset($request->cp_kala_terbit[$key]) ? trim((string) $request->cp_kala_terbit[$key]) : null;
                            $mediaName = strtoupper(trim((string) ($request->cp_media_type[$key] ?? '')));
                            $manualMedia = null;

                            if ($mediaName) {
                                $manualMedia = Cache::remember('collectionmedias:' . $mediaName, $cacheDuration, function () use ($mediaName) {
                                    return QueryAPI::get("select * from collectionmedias where upper(name) = '$mediaName'", true);
                                });
                            }

                            if (!isset($request->cpe[$key]) || !is_array($request->cpe[$key])) {
                                continue;
                            }

                            foreach ($request->cpe[$key] as $keys => $cpe) {
                                $totalPackage++;

                                $qtyReject = (int) ($request->cpe_qty_reject[$key][$keys] ?? 0);
                                $catalog = null;

                                if ($qtyReject > 0) $status = 'DITERIMA PARSIAL';

                                if ($catalogId) {
                                    $catalogCacheKey = "catalog:detail:{$catalogId}";

                                    $catalog = Cache::remember($catalogCacheKey, $cacheDuration, function () use ($catalogId) {
                                        $catalogQuery = "
                                            select
                                                catalogs.*,
                                                penerbit.name as name_penerbit,
                                                penerbit.alamat as alamat_penerbit,
                                                kabupaten.namakab as namakab,
                                                kabupaten.propinsiid as propinsiid
                                            from
                                                catalogs
                                            left join
                                                penerbit on penerbit.id = catalogs.penerbit_id
                                            left join
                                                kabupaten on kabupaten.id = penerbit.city_id
                                            where
                                                catalogs.id = $catalogId
                                        ";

                                        return QueryAPI::get($catalogQuery, true);
                                    });
                                }

                                $qtyAccept = (int) ($request->cpe_qty_accept[$key][$keys] ?? 0);
                                $edition = $request->cpe_edition[$key][$keys] ?? null;
                                $firstTTES = $request->cpe_first_ttes[$key][$keys] ?? null;
                                $endTTES = $request->cpe_end_ttes[$key][$keys] ?? null;
                                $description = '';

                                if ($qtyAccept === 0 && $qtyReject === 0) continue;

                                if (isset($request->cpe_description[$key][$keys]) && is_array($request->cpe_description[$key][$keys])) {
                                    $description = implode(';', $request->cpe_description[$key][$keys]);
                                }

                                $letterDetailData = [
                                    'title' => $catalog->TITLE ?? ($manualTitle ?? ''),
                                    'remark' => $description,
                                    'copy' => $qtyAccept + $qtyReject,
                                    'quantity' => 1,
                                    'price' => $catalog->PRICE ?? null,
                                    'letter_id' => $letter->LETTER_ID ?? null,
                                    'author' => $catalog->AUTHOR ?? null,
                                    'publisher' => $catalog->NAME_PENERBIT ?? ($letterExecutor->NAME ?? null),
                                    // Kalau tidak pakai katalog, alamat/kota diambil dari data
                                    // Pelaksana Serah (penerbit yang dipilih di form) — bukan
                                    // dibiarkan kosong.
                                    'publisher_address' => $catalog->ALAMAT_PENERBIT ?? ($letterExecutor->ALAMAT ?? null),
                                    // Tahun terbit diambil dari TTES Awal edisi ini (per-edisi,
                                    // karena tiap edisi terbitan berkala bisa beda tahun) kalau
                                    // tidak ada data katalog.
                                    'publish_year' => $catalog->PUBLISHYEAR ?? ($firstTTES ? date('Y', strtotime($firstTTES)) : null),
                                    'publisher_city' => $catalog->NAMAKAB ?? ($letterExecutor->NAMAKAB ?? null),
                                    'is_receivedate' => 1,
                                    'edisi_serial' => $edition,
                                    'ttes_awal' => $firstTTES,
                                    'ttes_akhir' => $endTTES,
                                    'catalog_id' => $catalogId,
                                    'qty_accept' => $qtyAccept,
                                    'qty_reject' => $qtyReject,
                                    'province_id' => $catalog->PROPINSIID ?? ($letterExecutor->PROVINCE_ID ?? null),
                                    'kab_id' => $catalog->CITY_ID ?? ($letterExecutor->CITY_ID ?? null),
                                    'collection_type_id' => $catalog->COLLECTIONMEDIA_ID ?? ($manualMedia->ID ?? null),
                                    'jenis_media' => $manualMedia->NAME ?? null,
                                    'issn' => $issn ?: null,
                                    // Kala terbit — sama seperti ISSN/Jenis Media, diisi sekali per
                                    // judul dan berlaku ke semua edisi di bawahnya.
                                    'kala_terbit' => $kalaTerbit ?: null,
                                    'penerbit_id' => $catalog->PENERBIT_ID ?? $executorId,
                                    'received_by' => $currentUser,
                                    'received_date' => $now,
                                    'checked' => 1,
                                ];

                                QueryAPI::create('letter_detail', $letterDetailData, false);
                            }
                        }
                    }

                    if ($totalPackage === 0) {
                        QueryAPI::delete('letter', $letter->LETTER_ID);

                        return response()->json([
                            'code' => 400,
                            'error' => ['Tidak ada item valid yang dapat disimpan']
                        ]);
                    }

                    QueryAPI::update('letter', $letter->LETTER_ID, [
                        'status' => $status,
                        'jumlah_paket' => $totalPackage
                    ], false);

                    $url = '';
                    $param = $request->param;

                    if ($param == 'save-print') {
                        $url = url('physical-delivery/accept/print/' . ($letter->LETTER_ID ?? 0));
                    } elseif ($param == 'save-send-email') {
                        $executorId = $letter->PENERBIT_ID ?? 0;
                        $executor = QueryAPI::get("select * from penerbit where id = $executorId", true);

                        $settings = QueryAPI::get("
                            select
                                *
                            from
                                e_settings
                            where
                                slug in ('Header','Footer','ResiPenerimaan')
                        ");

                        $templateEmailContent = null;
                        $templateEmailHeader = null;
                        $templateEmailFooter = null;

                        if ($settings) {
                            foreach ($settings as $setting) {
                                if ($setting->SLUG == 'ResiPenerimaan') $templateEmailContent = $setting;
                                elseif ($setting->SLUG == 'Header') $templateEmailHeader = $setting;
                                elseif ($setting->SLUG == 'Footer') $templateEmailFooter = $setting;
                            }
                        }

                        $dateNow = date('Y-m-d');
                        $branchId = $letter->BRANCH_ID ?? 0;

                        $leader = QueryAPI::get("
                            select
                                *
                            from
                                penanggung_jawab
                            where
                                branch_id = $branchId and
                                (tanggal_awal <= to_date('$dateNow', 'YYYY-MM-DD') and tanggal_akhir >= to_date('$dateNow', 'YYYY-MM-DD') + 1)
                        ", true);

                        $signature = '';

                        if ($leader) {
                            $signatureUrl = url('stream-file') . '?type=gambar_ttd&id=' . ($leader->ID ?? 0) . '&filename=' . ($leader->TTD_FILE_NAME ?? 0);
                            $signature = $leader->JABATAN . '<br><br>' . '<img src="' . $signatureUrl . '" style="max-width:40px !important;"><br><br>' . $leader->NAMA . '<br>' . '<span style="font-weight:bold;">' . $leader->NIP . '</span>';
                        }

                        $bodyParamEmail = [
                            'accepted_date' => date('d/m/Y', strtotime($letter->ACCEPT_DATE ?? '')),
                            'letter_no' => $letter->LETTER_NUMBER_UT ?? '',
                            'publisher_name' => $executor->NAME ?? '',
                            'director' => $signature,
                            'header' => '<img src="' . (string) Main::base64File(url('stream-file?type=gambar_template&id=' . ($templateEmailHeader->ID ?? '') . '&filename=' . ($templateEmailHeader->CONTENT ?? ''))) . '" style="max-width:100%;">',
                            'footer' => '<img src="' . (string) Main::base64File(url('stream-file?type=gambar_template&id=' . ($templateEmailFooter->ID ?? '') . '&filename=' . ($templateEmailFooter->CONTENT ?? ''))) . '" style="max-width:100%; margin-bottom:10px">',
                            'qr' => 'https://image-charts.com/chart?chs=150x150&cht=qr&chl=' . $letter->LETTER_NUMBER_UT,
                            'source' => QueryAPI::get("select name from branchs where id = " . ($letter->BRANCH_ID ?? 0), true)->NAME ?? '-',
                        ];

                        Mail::send([], [], function ($message) use ($bodyParamEmail, $templateEmailContent, $executor) {
                            $message->to($executor->EMAIL1 ?? '', $bodyParamEmail['publisher_name'])
                                ->subject('Resi Penerimaan')
                                ->from(config('mail.from.address'), config('mail.from.name'))
                                ->html(Main::parseTemplateEmail($bodyParamEmail, $templateEmailContent), 'text/html');
                        });
                    }

                    return response()->json([
                        'code' => 200,
                        'message' => 'Data berhasil disimpan',
                        'url' => $url
                    ]);
                } catch (\Exception $e) {
                    Log::error('Exception in submitted', [
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);

                    return response()->json([
                        'code' => 500,
                        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                    ]);
                }
            }
        }
    }
}
