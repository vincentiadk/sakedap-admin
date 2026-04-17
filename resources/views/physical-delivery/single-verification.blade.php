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
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label class="form-label text-muted mb-1">Judul</label>
                                                <div class="fw-semibold" id="detail_title">-</div>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label text-muted mb-1">ISBN</label>
                                                <div id="detail_isbn">-</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-muted mb-1">Filter Status</label>
                                                <select id="detail_isbn_status" class="form-select"  name="detail_isbn_status">
                                                    <option value="" selected="true">Pilih Status ISBN</option>
                                                    <option value="berISBN">berISBN</option>
                                                    @foreach($status_isbn as $si)
                                                    <option value="{{$si->KODE}}"> {{ $si->KODE }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label text-muted mb-1">Penerbit</label>
                                                <div id="detail_publisher">-</div>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label text-muted mb-1">Tahun Terbit</label>
                                                <div id="detail_year">-</div>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label text-muted mb-1">Status Resi</label>
                                                <div id="detail_status">-</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-muted mb-1">Status Item</label>
                                                <div id="detail_item_status">-</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-muted mb-1">Tanggal Terima</label>
                                                <div id="detail_received_date">-</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-muted mb-1">User Penerima</label>
                                                <div id="detail_received_by">-</div>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label text-muted mb-1">Tujuan Perpustakaan</label>
                                                <div id="detail_destination_library">-</div>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label text-muted mb-1">Jenis Pengiriman</label>
                                                <div id="detail_type_of_delivery">-</div>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label text-muted mb-1">Jasa Pengiriman</label>
                                                <div id="detail_jasa_pengiriman">-</div>
                                            </div>
                                            <div class="col-md-12 d-none" id="systemSummaryWrapper">
                                                <div class="border rounded p-3 bg-white" id="systemSummaryBox">
                                                    <div class="fw-semibold mb-2">Ringkasan Data di Sistem</div>

                                                    <div class="row g-3 small">
                                                        <div class="col-md-3">
                                                            <div class="text-muted">Riwayat Eks Dikirim ke Perpusnas</div>
                                                            <div class="fw-semibold" id="info_total_copy_sistem">0</div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="text-muted">Riwayat Eks Diterima ke Perpusnas</div>
                                                            <div class="fw-semibold" id="info_total_accept_sistem">0</div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="text-muted">Riwayat Eks Dikirim ke Provinsi</div>
                                                            <div class="fw-semibold" id="info_total_copy_prov">0</div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="text-muted">Riwayat Eks Diterima di Provinsi</div>
                                                            <div class="fw-semibold" id="info_total_accept_prov">0</div>
                                                        </div>
                                                         <div class="col-md-3">
                                                            <div class="text-muted">Total Eks Dikirim ke Perpusnas dan Provinsi</div>
                                                            <div class="fw-semibold" id="info_total_copy_all">0</div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="text-muted">Eks yang dikirim saat ini</div>
                                                            <div class="fw-semibold" id="info_collection_current">0</div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="text-muted">Koleksi sudah dicatat</div>
                                                            <div class="fw-semibold" id="info_total_collection_sistem">0</div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="text-muted">Eks Lebih Perpusnas</div>
                                                            <div class="fw-semibold" id="info_collection_other">0</div>
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
</style>
<script>
    // Dummy data sementara untuk simulasi tampilan
    const dummyBooks = [];
    let currentMode = null;
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

    function renderResults(data) {
        let i = 0;
        const list = document.getElementById('searchResultList');
        const empty = document.getElementById('emptyResult');
        const info = document.getElementById('searchResultInfo');

        list.innerHTML = '';

        if (!data.length) {
            list.classList.add('d-none');
            empty.classList.remove('d-none');
            info.innerText = '0 data';
            return;
        }

        empty.classList.add('d-none');
        list.classList.remove('d-none');
        info.innerText = `${data.length} data`;

        data.forEach(item => {
            const html = `
                <div class="result-item" data-id="${item.LETTER_DETAIL_ID}" data-index="${i}">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <div class="result-title">${item.TITLE}</div>
                            <div class="result-meta mb-1">
                                ISBN: ${item.ISBN || '-'} <br>
                                ${item.AUTHOR} • ${item.PUBLISHER} • ${item.PUBLISH_YEAR}
                            </div>
                        </div>
                        <div>
                            <div class="d-flex flex-column align-items-end gap-1">
                                ${getStatusBadge(item)}
                                <span class="badge bg-primary-subtle text-primary">Resi: ${item.STATUS}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            list.insertAdjacentHTML('beforeend', html);
            i+=1;
        });

        document.querySelectorAll('.result-item').forEach(el => {
            el.addEventListener('click', function () {
                document.querySelectorAll('.result-item').forEach(x => x.classList.remove('active'));
                this.classList.add('active');

                const index = Number(this.dataset.index);
                const selected = data[index];

                fillDetail(selected);
            });
        });
    }

    function fillDetail(item) {
        if (!item) return;

        document.getElementById('emptyDetail').classList.add('d-none');
        document.getElementById('detailPanel').classList.remove('d-none');
        document.getElementById('letter_id').value = item.LETTER_ID || '';
        document.getElementById('letter_detail_id').value = item.LETTER_DETAIL_ID || '';
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
        document.getElementById('detail_received_by').innerText = item.RECEIVED_BY_NAME ?? '-';
        document.getElementById('detail_received_date').innerText = item.RECEIVED_DATE ?? '-';
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

        const collectionCurrent = qtyAccept;
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

        const totalCopyAll = totalCopySistem + totalCopyProv;
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
                $('#searchResultInfo').text('Mencari...');
            },
            success: function (response) {
                if (response.code === 200) {
                    renderResults(response.data || []);
                    clearDetail();
                } else {
                    renderResults([]);
                    clearDetail();
                }
            },
            error: function () {
                $('#searchResultInfo').text('Gagal mengambil data');
                renderResults([]);
                clearDetail();
            }
        });
    }

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