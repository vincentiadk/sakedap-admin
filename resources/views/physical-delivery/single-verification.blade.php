<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pengiriman Fisik - <span class="fw-normal">Verifikasi Per Judul </span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <a href="{{ url('physical-delivery/accept') }}" class="btn btn-primary">
                    <i class="ph-arrow-left me-1"></i>
                    Kembali ke Tabel
                </a>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Penerimaan Buku dari Pengiriman</h5>
            <span class="badge bg-info text-white">Cari berdasarkan Judul / ISBN</span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                {{-- PANEL KIRI --}}
                <div class="col-lg-5">
                    <div class="border rounded p-3 h-100">
                        <h6 class="mb-3">Pencarian Buku</h6>

                        <div class="mb-3">
                            <label class="form-label">Kata Kunci</label>
                            <div class="input-group">
                                <input type="text" id="keyword" class="form-control"
                                    placeholder="Masukkan judul atau ISBN">
                                <button type="button" class="btn btn-primary" id="btnSearch">
                                    <i class="ph-magnifying-glass"></i> Cari
                                </button>
                                <button type="button" class="btn btn-light" id="btnResetSearch">
                                    Reset
                                </button>
                            </div>
                            <small class="text-muted">
                                Sistem dapat mencari berdasarkan ISBN exact match atau judul yang mirip.
                            </small>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Mode Pencarian</label>
                                <select id="search_mode" class="form-select">
                                    <option value="auto">Otomatis</option>
                                    <option value="isbn">ISBN</option>
                                    <option value="title">Judul</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status ISBN</label>
                                <select id="status_filter" class="form-select">
                                    <option value="">Semua</option>
                                    <option value="not_received">Belum Diterima</option>
                                    <option value="received">Sudah Diterima</option>
                                    <option value="verification">Perlu Verifikasi</option>
                                </select>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Hasil Pencarian</h6>
                            <small class="text-muted" id="searchResultInfo">0 data</small>
                        </div>

                        <div id="searchResultWrapper" style="max-height: 520px; overflow-y: auto;">
                            <div id="searchLoading" class="d-none py-4">
                                <div class="text-center text-muted mb-3">
                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    Mencari data...
                                </div>
                                <div class="skeleton-item"></div>
                                <div class="skeleton-item"></div>
                                <div class="skeleton-item"></div>
                            </div>

                            <div id="emptyResult" class="text-center text-muted py-5">
                                <i class="ph-books fs-1 d-block mb-2"></i>
                                Belum ada hasil pencarian
                            </div>

                            <div id="searchResultList" class="list-group d-none">
                                {{-- item hasil pencarian akan diisi JS / loop backend --}}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PANEL KANAN --}}
                <div class="col-lg-7">
                    <div class="border rounded p-3 h-100">
                        <h6 class="mb-3">Detail Karya & Verifikasi Penerimaan</h6>

                        <div id="emptyDetail" class="text-center text-muted py-5">
                            <i class="ph-book-open fs-1 d-block mb-2"></i>
                            Pilih salah satu karya dari hasil pencarian untuk melihat detail
                        </div>
                        <form action="{{ url('physical-delivery/single-verification/update-received-date') }}" method="POST" id="receiveForm">
                                    @csrf
                            <div id="detailPanel" class="d-none">
                                <div class="card mb-3 bg-light">
                                    <div class="card-body">
                                        <div class="row g-1">
                                            <div class="col-md-12">
                                                <div class="field-inline">
                                                    <label class="field-label">Judul</label>
                                                    <div class="field-value fw-semibold" id="detail_title">-</div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="field-inline">
                                                    <label class="field-label">ISBN</label>
                                                    <div class="field-value" id="detail_isbn">-</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="field-inline">
                                                    <label class="field-label" for="detail_isbn_status">Status ISBN</label>
                                                    <select id="detail_isbn_status" class="form-select form-select-sm field-value" name="detail_isbn_status">
                                                        <option value="" selected="true">Pilih Status ISBN</option>
                                                        <option value="berISBN">berISBN</option>
                                                        @foreach($status_isbn as $si)
                                                        <option value="{{$si->KODE}}"> {{ $si->KODE }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="field-inline">
                                                    <label class="field-label">Penerbit</label>
                                                    <div class="field-value" id="detail_publisher">-</div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="field-inline">
                                                    <label class="field-label">Tahun Terbit</label>
                                                    <div class="field-value" id="detail_year">-</div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="field-inline">
                                                    <label class="field-label">Status Resi</label>
                                                    <div class="field-value" id="detail_status">-</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="field-inline">
                                                    <label class="field-label">Status Item</label>
                                                    <div class="field-value" id="detail_item_status">-</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="field-inline">
                                                    <label class="field-label">Tanggal Terima</label>
                                                    <div class="field-value" id="detail_received_date">-</div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="field-inline">
                                                    <label class="field-label">User Penerima</label>
                                                    <div class="field-value" id="detail_received_by">-</div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="field-inline">
                                                    <label class="field-label">Tujuan Perpustakaan</label>
                                                    <div class="field-value" id="detail_destination_library">-</div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="field-inline">
                                                    <label class="field-label">Jenis Pengiriman</label>
                                                    <div class="field-value" id="detail_type_of_delivery">-</div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="field-inline">
                                                    <label class="field-label">Jasa Pengiriman</label>
                                                    <div class="field-value" id="detail_jasa_pengiriman">-</div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 d-none" id="systemSummaryWrapper">
                                                <div class="border rounded p-3 bg-white" id="systemSummaryBox">
                                                    <div class="fw-semibold mb-2">Ringkasan Data di Sistem</div>

                                                    <div class="row g-1 gx-4 small">
                                                        <div class="col-md-6">
                                                            <div class="stat-inline">
                                                                <span class="stat-label">Riwayat Eks Dikirim ke Perpusnas</span>
                                                                <span class="stat-value" id="info_total_copy_sistem">0</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="stat-inline">
                                                                <span class="stat-label">Riwayat Eks Diterima ke Perpusnas</span>
                                                                <span class="stat-value" id="info_total_accept_sistem">0</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="stat-inline">
                                                                <span class="stat-label">Riwayat Eks Dikirim ke Provinsi</span>
                                                                <span class="stat-value" id="info_total_copy_prov">0</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="stat-inline">
                                                                <span class="stat-label">Riwayat Eks Diterima di Provinsi</span>
                                                                <span class="stat-value" id="info_total_accept_prov">0</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="stat-inline">
                                                                <span class="stat-label">Total Eks Dikirim ke Perpusnas dan Provinsi</span>
                                                                <span class="stat-value" id="info_total_copy_all">0</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="stat-inline">
                                                                <span class="stat-label">Eks yang dikirim saat ini</span>
                                                                <span class="stat-value" id="info_collection_current">0</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="stat-inline">
                                                                <span class="stat-label">Koleksi sudah dicatat</span>
                                                                <span class="stat-value" id="info_total_collection_sistem">0</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="stat-inline">
                                                                <span class="stat-label">Eks Lebih Perpusnas</span>
                                                                <span class="stat-value" id="info_collection_other">0</span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mt-3 small text-muted" id="info_recommendation">
                                                        -
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr class="my-3">
                                        <div class="row g-3 align-items-end">
                                            <div class="col-md-3">
                                                <label class="form-label text-muted mb-1">Jumlah Judul Dikirim</label>
                                                <div id="detail_quantity">-</div>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label text-muted mb-1">Jumlah Eks Dikirim</label>
                                                <div class="input-group">
                                                    <button type="button" class="btn btn-outline-secondary minus">-</button>
                                                    <input type="text" class="form-control text-center" id="detail_copy" name="detail_copy" value="0" readonly>
                                                    <button type="button" class="btn btn-outline-secondary plus">+</button>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <label class="form-label text-muted mb-1">Diterima</label>
                                                <div class="input-group">
                                                    <button type="button" class="btn btn-outline-secondary minus">-</button>
                                                    <input type="text" class="form-control text-center" id="detail_qty_accept" name="detail_qty_accept" value="0" readonly>
                                                    <button type="button" class="btn btn-outline-secondary plus">+</button>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label text-muted mb-1">Ditolak</label>
                                                <div class="input-group">
                                                    <button type="button" class="btn btn-outline-secondary minus">-</button>
                                                    <input type="text" class="form-control text-center" id="detail_qty_reject" name="detail_qty_reject" value="0" readonly>
                                                    <button type="button" class="btn btn-outline-secondary plus">+</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row g-3 mt-1">
                                            <div class="col-md-12 d-none" id="rejectReasonWrapper">
                                                <label class="form-label text-muted mb-1">Alasan Ditolak</label>
                                                <input type="text" class="form-control" id="detail_reject_reason" name="detail_reject_reason" value="-"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    <input type="hidden" name="letter_detail_id" id="letter_detail_id">
                                    <input type="hidden" name="letter_id" id="letter_id">
                                    <input type="hidden" name="status_code" id="status_code">
                                    <input type="hidden" name="branch_id" id="branch_id">
                                    <input type="hidden" name="received_by_name" id="received_by_name">
                                    <input type="hidden" name="action_type" id="action_type" value="receive">
                                    <input type="hidden" name="letter_status" id="letter_status">

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Tanggal Terima</label>
                                            <input type="date" name="received_date" id="received_date" class="form-control">
                                            <small class="text-muted" id="received_date_help">
                                                Tanggal terima hanya dapat diubah untuk data yang masih perlu verifikasi.
                                            </small>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Keterangan</label>
                                            <div id="verification_alert" class="alert alert-secondary py-2 px-3 mb-0">
                                                <i class="ph-info me-1"></i>
                                                <span id="verification_note">Data belum dipilih</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2 mt-4">
                                        <button type="button" class="btn btn-light" id="btnClearSelection">
                                            Batal Pilih
                                        </button>

                                        <button type="submit" class="btn btn-success d-none" id="btnReceive">
                                            Simpan Data
                                        </button>

                                        <button type="submit" class="btn btn-warning d-none" id="btnReceiveAgain">
                                            Terima Ulang
                                        </button>
                                    </div>
                            </div>
                        </form>
                    </div>
                </div>
                {{-- END PANEL KANAN --}}
            </div>
        </div>
    </div>
</div>
<style>
    .result-item {
        cursor: pointer;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 12px 14px;
        transition: all .2s ease-in-out;
        margin-bottom: 10px;
        background: #fff;
    }

    .result-item:hover {
        border-color: #0d6efd;
        background: #f8fbff;
    }

    .result-item.active {
        border-color: #0d6efd;
        background: #eef5ff;
    }

    .result-title {
        font-weight: 600;
        margin-bottom: 4px;
    }

    .result-meta {
        font-size: 12px;
        color: #6b7280;
    }

    .badge-status {
        font-size: 11px;
        padding: 4px 8px;
    }
    .input-group input {
        background-color: #fff !important;
    }

    .result-destination {
        font-size: 12px;
        color: #0d6efd;
        margin-top: 2px;
    }

    /* Kiriman di luar wewenang: tetap terbaca, tapi jelas tidak bisa diproses */
    .result-item-locked {
        background: #fbfbfc;
        border-style: dashed;
    }

    .result-item-locked .result-title {
        color: #6b7280;
    }

    /* Data dari pengajuan ISBN penerbit, bukan dari penerimaan */
    .result-item-registry {
        cursor: default;
        border-color: #ffc107;
        background: #fffdf5;
    }

    .result-item-registry:hover {
        border-color: #ffc107;
        background: #fffdf5;
    }

    /* Rangka abu-abu saat menunggu hasil pencarian */
    .skeleton-item {
        height: 62px;
        border-radius: 10px;
        margin-bottom: 10px;
        background: linear-gradient(90deg, #f1f3f5 25%, #e9ecef 37%, #f1f3f5 63%);
        background-size: 400% 100%;
        animation: skeleton-loading 1.2s ease-in-out infinite;
    }

    @keyframes skeleton-loading {
        0% { background-position: 100% 50%; }
        100% { background-position: 0 50%; }
    }

    @media (prefers-reduced-motion: reduce) {
        .skeleton-item { animation: none; }
    }

    /* Field tampilan: label dan nilai sebaris, bukan bertumpuk */
    .field-inline {
        display: flex;
        align-items: baseline;
        gap: 8px;
        min-height: 26px;
        padding: 2px 0;
    }

    .field-inline .field-label {
        flex: 0 0 138px;
        margin: 0;
        color: #6b7280;
        font-size: 12px;
        line-height: 1.4;
    }

    .field-inline .field-value {
        flex: 1 1 auto;
        min-width: 0;
        font-size: 13px;
        word-break: break-word;
    }

    /* Angka ringkasan: label kiri, nilai rata kanan */
    .stat-inline {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 10px;
        padding: 3px 0;
        border-bottom: 1px dashed #e9ecef;
    }

    .stat-inline .stat-label {
        color: #6b7280;
        min-width: 0;
    }

    .stat-inline .stat-value {
        flex: 0 0 auto;
        font-weight: 600;
        font-variant-numeric: tabular-nums;
    }

    @media (max-width: 767.98px) {
        .field-inline .field-label {
            flex-basis: 110px;
        }
    }
</style>
<script>
    // Dummy data sementara untuk simulasi tampilan
    const dummyBooks = [];
    let currentMode = null;

    // Nama yang akan tercatat sebagai penerima saat data disimpan.
    // Server mengisi received_by dari session('username'), lalu ditampilkan
    // sebagai fullname -- jadi yang dipratinjau di sini fullname-nya.
    const NAMA_PENGGUNA = @json(session('name') ?: (session('username') ?: '-'));
    select2Serverside('#detail_status_isbn', 'status-isbn');
    $('.plus').click(function() {
        let input = $(this).siblings('input');
        input.val(parseInt(input.val()) + 1);
        toggleRejectReason();
    });

    $('.minus').click(function() {
        let input = $(this).siblings('input');
        let val = parseInt(input.val());
        if (val > 0) input.val(val - 1);
        toggleRejectReason();
    });
    function toggleRejectReason() {
        let qtyReject = parseInt($('#detail_qty_reject').val() || 0, 10);

        if (qtyReject > 0) {
            $('#rejectReasonWrapper').removeClass('d-none');
            $('#detail_reject_reason').prop('required', true);
        } else {
            $('#rejectReasonWrapper').addClass('d-none');
            $('#detail_reject_reason').prop('required', false).val('');
        }
    }
    function getStatusResiBadge(status) {
        let badgeClass = 'bg-secondary';

        if (status.trim() === 'DITERIMA') badgeClass = 'bg-success';
        if (status.trim()  === 'DITERIMA PENUH') badgeClass = 'bg-success';
        if (status.trim()  === 'DITERIMA PARSIAL') badgeClass = 'bg-primary';
        if (status.trim()  === 'DALAM PENGIRIMAN') badgeClass = 'bg-warning text-dark';
        return `<span class="badge ${badgeClass} badge-status">${status}</span>`;
    }
    function getStatusBadge(item) {
        let badgeClass = 'bg-secondary';
        let text = 'Belum Diverifikasi';

        const accept = parseInt(item.QTY_ACCEPT || 0);
        const reject = parseInt(item.QTY_REJECT || 0);

        if (accept > 0 && reject === 0) {
            badgeClass = 'bg-success';
            text = 'Diterima';
        } else if (accept === 0 && reject > 0) {
            badgeClass = 'bg-danger';
            text = 'Ditolak';
        } else if (accept > 0 && reject > 0) {
            badgeClass = 'bg-warning text-dark';
            text = 'Parsial';
        } else {
            badgeClass = 'bg-secondary';
            text = 'Belum Verifikasi';
        }

        return `<span class="badge ${badgeClass} badge-status">${text}</span>`;
    }

    function renderResults(data, registry) {
        let i = 0;
        const list = document.getElementById('searchResultList');
        const empty = document.getElementById('emptyResult');
        const info = document.getElementById('searchResultInfo');

        list.innerHTML = '';

        if (!data.length) {
            // Belum ada di penerimaan, tapi ISBN-nya terdaftar di data penerbit.
            if (registry && registry.length) {
                renderRegistry(registry);
                return;
            }

            list.classList.add('d-none');
            empty.classList.remove('d-none');
            info.innerText = '0 data';
            return;
        }

        empty.classList.add('d-none');
        list.classList.remove('d-none');
        info.innerText = `${data.length} data`;

        data.forEach(item => {
            const meta = [item.AUTHOR, item.PUBLISHER, item.PUBLISH_YEAR]
                .filter(v => v !== null && v !== undefined && String(v).trim() !== '')
                .join(' • ');

            // Hapus hanya untuk yang belum diterima dan memang jadi wewenang pengguna.
            const belumDiterima = (item.STATUS_CODE || '').toLowerCase() === 'verification';
            const bolehUbah = String(item.CAN_EDIT) === '1';

            const tombolHapus = (belumDiterima && bolehUbah)
                ? `<button type="button" class="btn btn-outline-danger btn-sm btn-hapus-judul mt-1"
                        data-id="${item.LETTER_DETAIL_ID}" title="Hapus judul ini">
                        <i class="ph-trash"></i>
                   </button>`
                : '';

            let tandaKunci = '';

            if (!bolehUbah) {
                const alasan = alasanTidakBisaProses(item);

                tandaKunci = bukanKaryaCetak(item)
                    ? `<span class="badge bg-warning-subtle text-warning-emphasis mt-1" title="${alasan}">
                            <i class="ph-disc me-1"></i>Bukan karya cetak
                       </span>`
                    : `<span class="badge bg-secondary-subtle text-secondary mt-1" title="${alasan}">
                            <i class="ph-lock-simple me-1"></i>Hanya lihat
                       </span>`;
            }

            const html = `
                <div class="result-item ${bolehUbah ? '' : 'result-item-locked'}" data-id="${item.LETTER_DETAIL_ID}" data-index="${i}">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div class="flex-grow-1 min-w-0">
                            <div class="result-title">${item.TITLE}</div>
                            <div class="result-meta mb-1">
                                ISBN: ${item.ISBN || '-'}${meta ? '<br>' + meta : ''}
                            </div>
                            <div class="result-destination">
                                <i class="ph-map-pin me-1"></i>${item.DESTINATION_LIBRARY || '-'}
                            </div>
                        </div>
                        <div>
                            <div class="d-flex flex-column align-items-end gap-1">
                                ${getStatusBadge(item)}
                                <span class="badge bg-primary-subtle text-primary">Resi: ${item.STATUS}</span>
                                ${tandaKunci}
                                ${tombolHapus}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            list.insertAdjacentHTML('beforeend', html);
            i+=1;
        });

        document.querySelectorAll('.result-item').forEach(el => {
            el.addEventListener('click', function (e) {
                // Klik tombol hapus tidak boleh ikut memilih baris.
                if (e.target.closest('.btn-hapus-judul')) {
                    return;
                }

                document.querySelectorAll('.result-item').forEach(x => x.classList.remove('active'));
                this.classList.add('active');

                const index = Number(this.dataset.index);
                const selected = data[index];

                fillDetail(selected);
            });
        });

        document.querySelectorAll('.btn-hapus-judul').forEach(el => {
            el.addEventListener('click', function (e) {
                e.stopPropagation();

                const id = this.dataset.id;
                const index = Number(this.closest('.result-item').dataset.index);
                const judul = data[index] ? data[index].TITLE : '';

                konfirmasiHapusJudul(id, judul);
            });
        });
    }

    /**
     * ISBN tidak ada di LETTER_DETAIL, tapi terdaftar di PENERBIT_ISBN.
     * Ditampilkan sebagai keterangan saja -- tidak bisa diproses penerimaannya
     * karena belum ada data pengirimannya.
     */
    function renderRegistry(rows) {
        const list = document.getElementById('searchResultList');
        const empty = document.getElementById('emptyResult');
        const info = document.getElementById('searchResultInfo');

        empty.classList.add('d-none');
        list.classList.remove('d-none');
        info.innerText = `${rows.length} data ISBN penerbit`;

        let html = `
            <div class="alert alert-warning d-flex gap-2 mb-3">
                <i class="ph-warning-circle fs-5"></i>
                <div>
                    <div class="fw-semibold">Belum ada di data pengiriman</div>
                    <div class="small">
                        Data di bawah ini <b>bukan berasal dari data pengiriman</b>, melainkan
                        dari <b>data pengajuan ISBN penerbit</b> &mdash; karena penerbitnya
                        belum mengisi data pengiriman untuk ISBN ini.
                        Penerimaan baru dapat diproses setelah data pengirimannya masuk.
                    </div>
                </div>
            </div>
        `;

        rows.forEach(row => {
            const meta = [row.AUTHOR, row.PUB_NAME, row.TAHUN_TERBIT]
                .filter(v => v !== null && v !== undefined && String(v).trim() !== '')
                .join(' • ');

            const catatanMedia = bukanKaryaCetak(row)
                ? `<div class="alert alert-secondary py-2 px-3 small mb-2">
                        <i class="ph-disc me-1"></i>ISBN ini adalah ISBN <b>${namaJenisMedia(row)}</b>.
                        Verifikasi fisik hanya untuk karya cetak.
                   </div>`
                : '';

            html += `
                <div class="result-item result-item-registry">
                    <div class="result-title">${row.TITLE || '(judul belum terdata)'}</div>
                    <div class="result-meta mb-2">
                        ISBN: ${row.ISBN_NO || '-'}${meta ? '<br>' + meta : ''}
                    </div>
                    ${catatanMedia}
                    <div class="row g-1 small">
                        <div class="col-12">
                            <div class="stat-inline">
                                <span class="stat-label">Status pengajuan ISBN</span>
                                <span class="stat-value">${row.STATUS || '-'}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="stat-inline">
                                <span class="stat-label">Diterima Perpusnas</span>
                                <span class="stat-value ${row.RECEIVED_DATE_KCKR ? '' : 'text-danger'}">
                                    ${row.RECEIVED_DATE_KCKR ? formatTanggalTampil(row.RECEIVED_DATE_KCKR) : 'Belum'}
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="stat-inline">
                                <span class="stat-label">Diterima Provinsi</span>
                                <span class="stat-value ${row.RECEIVED_DATE_PROV ? '' : 'text-danger'}">
                                    ${row.RECEIVED_DATE_PROV ? formatTanggalTampil(row.RECEIVED_DATE_PROV) : 'Belum'}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        list.innerHTML = html;
    }

    /**
     * Nama jenis media untuk ditampilkan. Tabel JENIS_MEDIA baru berisi id 1,
     * jadi kode dipakai sebagai cadangan selama sisanya belum diisi.
     */
    function namaJenisMedia(item) {
        const nama = (item.JENIS_MEDIA_NAME || '').trim();

        if (nama) {
            return nama;
        }

        const kode = kodeJenisMedia(item);

        return kode ? `jenis media kode ${kode}` : 'tidak diketahui';
    }

    function kodeJenisMedia(item) {
        const kode = item.JENIS_MEDIA;

        return (kode === null || kode === undefined) ? '' : String(kode).trim();
    }

    function bukanKaryaCetak(item) {
        const kode = kodeJenisMedia(item);

        return kode !== '' && kode !== '1';
    }

    /**
     * Kenapa satu baris tidak bisa diproses. Dua sebab yang berbeda, jadi
     * pesannya juga harus berbeda -- jangan sampai petugas mengira ini soal
     * wilayah padahal soal jenis media.
     */
    function alasanTidakBisaProses(item) {
        if (bukanKaryaCetak(item)) {
            return `ISBN ini adalah ISBN ${namaJenisMedia(item)}. Verifikasi fisik hanya untuk karya cetak.`;
        }

        return 'Kiriman ini ditujukan ke ' + (item.DESTINATION_LIBRARY || 'perpustakaan lain') +
            '. Anda hanya dapat melihat datanya.';
    }

    function konfirmasiHapusJudul(id, judul) {
        Swal.fire({
            title: 'Anda yakin?',
            html: `Judul berikut akan dihapus permanen:<br><b>${judul}</b>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-outline-secondary'
            },
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            Swal.fire({
                title: 'Menghapus...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
            });

            $.ajax({
                url: '{{ url("physical-delivery/single-verification/destroy") }}',
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                dataType: 'JSON',
                data: { letter_detail_id: id },
                success: function (response) {
                    Swal.fire({
                        title: 'Berhasil',
                        text: response.message || 'Judul berhasil dihapus.',
                        icon: 'success',
                        customClass: { confirmButton: 'btn btn-primary' },
                    });
                    clearDetail();
                    doSearch();
                },
                error: function (xhr) {
                    const pesan = (xhr.responseJSON && xhr.responseJSON.message)
                        ? xhr.responseJSON.message
                        : 'Gagal menghapus data.';

                    Swal.fire({
                        title: 'Gagal',
                        text: pesan,
                        icon: 'error',
                        customClass: { confirmButton: 'btn btn-primary' },
                    });
                }
            });
        });
    }

    function fillDetail(item) {
        if (!item) return;

        document.getElementById('emptyDetail').classList.add('d-none');
        document.getElementById('detailPanel').classList.remove('d-none');
        document.getElementById('letter_id').value = item.LETTER_ID || '';
        document.getElementById('letter_detail_id').value = item.LETTER_DETAIL_ID || '';
        document.getElementById('letter_status').value = item.STATUS || '';
        document.getElementById('received_by_name').value = item.RECEIVED_BY_NAME || 'no_name';
        document.getElementById('branch_id').value = item.BRANCH_ID || '';
        document.getElementById('status_code').value = item.STATUS_CODE || '';

        document.getElementById('detail_title').innerText = item.TITLE || '-';
        document.getElementById('detail_isbn').innerText = item.ISBN || '-';
        if(item.ISBN_STATUS == '' && item.ISBN != ''){
            if(![...document.getElementById('detail_isbn_status').options].some(opt => opt.value === 'berISBN')){
                document.getElementById('detail_isbn_status').add(new Option('berISBN', 'berISBN', true, true));
            }
            document.getElementById('detail_isbn_status').value = 'berISBN';
        } else {
            if(item.ISBN_STATUS && ![...document.getElementById('detail_isbn_status').options].some(opt => opt.value === item.ISBN_STATUS)){
                document.getElementById('detail_isbn_status').add(new Option(item.ISBN_STATUS, item.ISBN_STATUS, true, true));
            }
            document.getElementById('detail_isbn_status').value = item.ISBN_STATUS || '';
        }

        document.getElementById('detail_publisher').innerText = item.PUB_NAME || item.PUBLISHER || '-';
        document.getElementById('detail_year').innerText = item.PUBLISH_YEAR || '-';
        document.getElementById('detail_status').innerHTML = getStatusResiBadge(item.STATUS);
        document.getElementById('detail_item_status').innerHTML = getStatusBadge(item);
        document.getElementById('detail_destination_library').innerText = item.DESTINATION_LIBRARY || item.LIBRARY_NAME || '-';
        setInfoPenerimaan(item);
        document.getElementById('detail_type_of_delivery').innerText = item.TYPE_OF_DELIVERY || '-';
        document.getElementById('detail_jasa_pengiriman').innerText = item.JASA_PENGIRIMAN_NAME || '-';
        document.getElementById('detail_copy').value = item.COPY || '0';
        document.getElementById('detail_quantity').innerText = item.QUANTITY || '0';

        document.getElementById('detail_qty_accept').value = item.QTY_ACCEPT || '0';
        document.getElementById('detail_qty_reject').value = item.QTY_REJECT || '0';
        //document.getElementById('detail_reject_reason').value = item.REMARK || '-';
        const qtyReject = parseInt(item.QTY_REJECT || 0, 10);
        const rejectReason = item.REMARK || '';

        document.getElementById('detail_reject_reason').value = rejectReason;

        if (qtyReject > 0) {
            document.getElementById('rejectReasonWrapper').classList.remove('d-none');
        } else {
            document.getElementById('rejectReasonWrapper').classList.add('d-none');
        }
        toggleSystemSummary(item);
        const receivedDateInput = document.getElementById('received_date');
        const verificationNote = document.getElementById('verification_note');
        const btnReceive = document.getElementById('btnReceive');
        const btnReceiveAgain = document.getElementById('btnReceiveAgain');

        receivedDateInput.value = formatDateForInput(item.RECEIVED_DATE);
        btnReceive.classList.add('d-none');
        btnReceiveAgain.classList.add('d-none');

        // Petugas provinsi hanya boleh memproses kiriman ke provinsinya sendiri.
        // Server memeriksa ulang hal yang sama saat menyimpan.
        if (String(item.CAN_EDIT) !== '1') {
            receivedDateInput.setAttribute('readonly', true);
            receivedDateInput.required = false;
            verificationNote.innerText = alasanTidakBisaProses(item);
            $('.plus, .minus').hide();
            $('#detail_isbn_status').prop('disabled', true);
            $('#detail_reject_reason').prop('readonly', true);
            return;
        }

        $('#detail_isbn_status').prop('disabled', false);
        $('#detail_reject_reason').prop('readonly', false);

        if ((item.STATUS_CODE || '').toLowerCase() === 'verification') {
            receivedDateInput.removeAttribute('readonly');
            receivedDateInput.required = true;
            verificationNote.innerText = 'Data perlu verifikasi. Silakan isi tanggal terima.';
            btnReceive.classList.remove('d-none');
            document.getElementById('action_type').value = 'receive';
            $('.plus, .minus').show();
            $('#detail_quantity').prop('readonly', true);
            $('#detail_qty_accept').prop('readonly', true);
            $('#detail_qty_reject').prop('readonly', true);
           
        } else {
            if(item.RECEIVED_DATE != '') {
                receivedDateInput.setAttribute('readonly', true);
            } else {
                receivedDateInput.removeAttribute('readonly');
            }
            receivedDateInput.required = true; //aslinya false
            verificationNote.innerText = 'Data sudah diterima sebelumnya. Hanya tersedia aksi terima ulang.';
            btnReceiveAgain.classList.remove('d-none');
            document.getElementById('action_type').value = 'receive_again';
            $('.plus, .minus').show(); //aslinye hide
            $('#detail_quantity').prop('readonly', true);
            $('#detail_qty_accept').prop('readonly', true);
            $('#detail_qty_reject').prop('readonly', true);
        }

        // Isi otomatis tanggal hari ini kalau kolomnya masih bisa diubah dan
        // belum ada isinya, supaya petugas tidak perlu memilih tanggal tiap kali.
        // Yang sudah diterima tidak tersentuh -- kolomnya sudah readonly di atas.
        if (!receivedDateInput.hasAttribute('readonly') && !receivedDateInput.value) {
            receivedDateInput.value = todayForInput();
        }

        // Tidak boleh memilih tanggal ke depan. Server memeriksa hal yang sama.
        receivedDateInput.setAttribute('max', todayForInput());

        // Dipanggil lagi di sini karena setInfoPenerimaan() berjalan sebelum
        // kolom tanggal di atas terisi.
        perbaruiPratinjauTanggal();
    }

    /**
     * Tanggal Terima & User Penerima.
     * Sudah diterima  -> tampil biasa.
     * Belum diterima  -> merah, disertai nilai yang akan tersimpan dalam kurung.
     *                    Untuk kiriman di luar wewenang, kurungnya tidak
     *                    ditampilkan karena pengguna itu memang tidak akan
     *                    menjadi penerimanya.
     */
    function setInfoPenerimaan(item) {
        const elTanggal = document.getElementById('detail_received_date');
        const elUser = document.getElementById('detail_received_by');

        const sudahDiterima = !!(item.RECEIVED_DATE && String(item.RECEIVED_DATE).trim() !== '');
        const bolehUbah = String(item.CAN_EDIT) === '1';

        [elTanggal, elUser].forEach(el => el.classList.remove('text-danger', 'fw-semibold'));

        if (sudahDiterima) {
            elTanggal.innerText = formatTanggalTampil(item.RECEIVED_DATE);
            elUser.innerText = item.RECEIVED_BY_NAME || '-';
            return;
        }

        [elTanggal, elUser].forEach(el => el.classList.add('text-danger', 'fw-semibold'));

        if (!bolehUbah) {
            elTanggal.innerText = 'Belum diterima';
            elUser.innerText = 'Belum diterima';
            return;
        }

        elUser.innerText = `Belum diterima (akan diisi: ${NAMA_PENGGUNA})`;
        perbaruiPratinjauTanggal();
    }

    /**
     * Isi dalam kurung pada "Tanggal Terima" mengikuti kolom isian di bawah,
     * jadi petugas langsung melihat tanggal apa yang akan tersimpan.
     * Hanya berlaku selama data belum diterima -- ditandai kelas text-danger
     * yang dipasang setInfoPenerimaan().
     */
    function perbaruiPratinjauTanggal() {
        const elTanggal = document.getElementById('detail_received_date');

        if (!elTanggal.classList.contains('text-danger')) {
            return;
        }

        const diisi = document.getElementById('received_date').value;

        elTanggal.innerText = diisi
            ? `Belum diterima (akan diisi: ${formatTanggalIndo(diisi)})`
            : 'Belum diterima (tanggal belum diisi)';
    }

    // "2026-07-08 12:00:00 AM" -> "08/07/2026"
    function formatTanggalTampil(nilai) {
        if (!nilai) return '-';

        return formatTanggalIndo(String(nilai).split(' ')[0]);
    }

    // "2026-09-04" -> "04/09/2026"
    function formatTanggalIndo(iso) {
        if (!iso) return '-';

        const bagian = iso.split('-');

        return bagian.length === 3 ? `${bagian[2]}/${bagian[1]}/${bagian[0]}` : iso;
    }

    function todayForInput() {
        // Dibentuk dari komponen tanggal lokal, bukan toISOString(), supaya
        // tidak meleset sehari akibat konversi ke UTC.
        const d = new Date();
        const bulan = String(d.getMonth() + 1).padStart(2, '0');
        const tanggal = String(d.getDate()).padStart(2, '0');

        return `${d.getFullYear()}-${bulan}-${tanggal}`;
    }

    function toggleSystemSummary(item) {
        const rawIsbn = (item.ISBN || '').trim();
        const normalizedIsbn = rawIsbn.replace(/-/g, '').trim();
        const hasIsbn = rawIsbn !== '' && rawIsbn !== '-' && normalizedIsbn !== '';

        if (!hasIsbn) {
            $('#systemSummaryWrapper').addClass('d-none');
            $('#info_total_copy_sistem').text('0');
            $('#info_total_accept_sistem').text('0');
            $('#info_total_collection_sistem').text('0');
            $('#info_collection_current').text('0');
            $('#info_collection_other').text('0');
            $('#info_recommendation').text('-');
            return;
        }

        $('#systemSummaryWrapper').removeClass('d-none');

        const totalCopySistem = parseInt(item.TOTAL_COPY_SISTEM || 0, 10);
        const totalAcceptSistem = parseInt(item.TOTAL_ACCEPT_SISTEM || 0, 10);
        const totalCollectionSistem = parseInt(item.TOTAL_COLLECTION_SISTEM || 0, 10);
        const qtyAccept = parseInt(item.QTY_ACCEPT || 0, 10);

        const collectionCurrent = item.COPY;
        const collectionOther = Math.max(totalCollectionSistem - collectionCurrent, 0);

        $('#info_total_copy_sistem').text(totalCopySistem);
        $('#info_total_accept_sistem').text(totalAcceptSistem);
        $('#info_total_collection_sistem').text(totalCollectionSistem);
        $('#info_collection_current').text(collectionCurrent);
        $('#info_collection_other').text(collectionOther);
        
        const totalCopyProv = parseInt(item.TOTAL_COPY_PROV || 0, 10);
        const totalAcceptProv = parseInt(item.TOTAL_ACCEPT_PROV || 0, 10);

        $('#info_total_prov').text(totalCopyProv);
        $('#info_total_accept_copy_prov').text(totalAcceptProv);

        const totalCopyAll = item.TOTAL_COPY_ALL;
        $('#info_total_copy_all').text(totalCopyAll);
        let note = 'Data ini bersifat referensi berdasarkan ISBN.';

        if (totalCollectionSistem > 0) {
            note = `Saat ini terdapat ${totalCollectionSistem} koleksi di sistem, terdiri dari ${collectionCurrent} dari item ini dan ${collectionOther} dari data lain.`;
        }

        if (totalAcceptSistem > 0) {
            note += ` Riwayat penerimaan sebelumnya tercatat sebanyak ${totalAcceptSistem} eks.`;
        }

        $('#info_recommendation').text(note);
    }
    function formatDateForInput(dateString) {
        if (!dateString) return '';

        return dateString.split(' ')[0]; // ambil "2025-09-29"
    }
    function clearDetail() {
        document.getElementById('detailPanel').classList.add('d-none');
        document.getElementById('emptyDetail').classList.remove('d-none');
        document.getElementById('letter_detail_id').value = '';
        document.getElementById('letter_id').value = '';

        document.querySelectorAll('.result-item').forEach(x => x.classList.remove('active'));
        document.getElementById('receiveForm').reset();
        $('#btnReceive, #btnReceiveAgain').addClass('d-none');
        $('#systemSummaryWrapper').addClass('d-none');
        $('#info_total_copy_sistem').text('0');
        $('#info_total_accept_sistem').text('0');
        $('#info_total_collection_sistem').text('0');
        $('#info_collection_current').text('0');
        $('#info_collection_other').text('0');
        $('#info_recommendation').text('-');
    }

    function doSearch() {
        const keyword = $('#keyword').val().trim();
        const mode = $('#search_mode').val()
        renderResults([]);
        clearDetail();
        if (!keyword) {
            return;
        }

        $.ajax({
            url: '{{ url("physical-delivery/single-verification/search") }}',
            type: 'POST',
            headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
            dataType: 'JSON',
            data: {
                keyword: keyword,
                mode: mode,
                limit: 20
            },
            beforeSend: function () {
                tampilkanLoadingPencarian(true);
            },
            success: function (response) {
                tampilkanLoadingPencarian(false);

                if (response.code === 200) {
                    renderResults(response.data || [], response.registry || []);
                    clearDetail();
                } else {
                    renderResults([]);
                    clearDetail();
                }
            },
            error: function () {
                tampilkanLoadingPencarian(false);
                $('#searchResultInfo').text('Gagal mengambil data');
                renderResults([]);
                clearDetail();
            }
        });
    }

    function tampilkanLoadingPencarian(sedangMencari) {
        const loading = document.getElementById('searchLoading');
        const list = document.getElementById('searchResultList');
        const empty = document.getElementById('emptyResult');
        const btn = document.getElementById('btnSearch');

        if (sedangMencari) {
            $('#searchResultInfo').text('Mencari...');
            loading.classList.remove('d-none');
            list.classList.add('d-none');
            empty.classList.add('d-none');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Mencari';
        } else {
            loading.classList.add('d-none');
            btn.disabled = false;
            btn.innerHTML = '<i class="ph-magnifying-glass"></i> Cari';
        }
    }

    // Pratinjau dalam kurung mengikuti tanggal yang diketik/dipilih petugas.
    document.getElementById('received_date').addEventListener('input', function () {
        if (this.value && this.value > todayForInput()) {
            Swal.fire({
                title: 'Tanggal tidak valid',
                text: 'Tanggal terima tidak boleh melebihi hari ini (' + formatTanggalIndo(todayForInput()) + ').',
                icon: 'warning',
                customClass: { confirmButton: 'btn btn-primary' },
            });

            this.value = todayForInput();
        }

        perbaruiPratinjauTanggal();
    });

    document.getElementById('btnSearch').addEventListener('click', doSearch);

    document.getElementById('keyword').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearDetail();
            doSearch();
        }
    });

    document.getElementById('btnResetSearch').addEventListener('click', function() {
        document.getElementById('keyword').value = '';
        document.getElementById('search_mode').value = 'auto';
        document.getElementById('status_filter').value = '';
        renderResults([]);
        clearDetail();
    });

    document.getElementById('btnClearSelection').addEventListener('click', function() {
        currentMode = null;
        clearDetail();
    });
    // TERIMA
    $('#btnReceive').on('click', function (e) {
        e.preventDefault();
        currentMode = 'receive';
        const errors = validateReceiveForm();

        if (errors.length > 0) {
            showValidationAlert(errors);
            return;
        }
        Swal.fire({
            title: 'Simpan data?',
            text: 'Data akan disimpan dan ditandai sudah diperiksa.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-outline-secondary'
            },
        }).then((result) => {
            if (result.isConfirmed) {
                submitForm();
            }
        });
    });

    // TERIMA ULANG
    $('#btnReceiveAgain').on('click', function (e) {
        e.preventDefault();
        currentMode = 'receive_again';
        const errors = validateReceiveForm();

        if (errors.length > 0) {
            showValidationAlert(errors);
            return;
        }
        Swal.fire({
            title: 'Terima ulang?',
            text: 'Data sebelumnya akan diperbarui.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, lanjutkan',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-outline-secondary'
            },
        }).then((result) => {
            if (result.isConfirmed) {
                
                submitForm();
            }
        });
    });

    function submitForm() {
        let formData = $('#receiveForm').serializeArray();

        // tambahin mode ke payload
        formData.push({
            name: 'mode',
            value: currentMode
        });

        Swal.fire({
            title: 'Menyimpan...',
            text: 'Mohon tunggu',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "{{ url('physical-delivery/single-verification/update-received-date') }}",
            method: 'POST',
            data: formData,
            success: function (res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data berhasil disimpan',
                    timer: 1500
                });

                $('#btnClearSelection').click();
                $('#btnResetSearch').click();
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                });
            }
        });
    }

    function validateReceiveForm() {
        let errors = [];

        const receivedDate = $('#received_date').val();
        const copy = parseInt($('#detail_copy').val() || 0, 10);
        const qtyAccept = parseInt($('#detail_qty_accept').val() || 0, 10);
        const qtyReject = parseInt($('#detail_qty_reject').val() || 0, 10);

        if (!receivedDate) {
            errors.push('Tanggal terima wajib diisi.');
        } else if (receivedDate > todayForInput()) {
            // Format YYYY-MM-DD bisa dibandingkan langsung sebagai teks.
            errors.push('Tanggal terima tidak boleh melebihi hari ini (' + formatTanggalIndo(todayForInput()) + ').');
        }
        if (copy <= 0) {
            errors.push('Jumlah eksemplar dikirim harus lebih dari 0.');
        }
        if (qtyReject < 0) {
            errors.push('Jumlah ditolak tidak boleh kurang dari 0');
        }
        if (qtyAccept < 0) {
            errors.push('Jumlah ditolak tidak boleh kurang dari 0');
        }

        if (qtyAccept > copy ) {
            errors.push('Jumlah diterima tidak boleh lebih dari jumlah eksemplar dikirim.');
        }

        if (qtyReject > copy) {
            errors.push('Jumlah ditolak tidak boleh lebih dari jumlah eksemplar dikirim.');
        }
        const rejectReason = $('#detail_reject_reason').val();
        if (qtyReject > 0 && !rejectReason) {
            errors.push('Alasan penolakan wajib diisi jika ada eksemplar yang ditolak.');
        }
        if ((qtyAccept + qtyReject) != copy) {
            errors.push('Total jumlah diterima dan jumlah ditolak harus sama dengan jumlah eksemplar dikirimkan.');
        }

        return errors;
    }
    function showValidationAlert(errors) {
        Swal.fire({
            icon: 'warning',
            title: 'Data belum lengkap',
            html: `
                <div style="text-align:left;">
                    <ul style="margin:0; padding-left:20px;">
                        ${errors.map(err => `<li>${err}</li>`).join('')}
                    </ul>
                </div>
            `,
            confirmButtonText: 'OK'
        });
    }
</script>