<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pengiriman Fisik - Penerimaan dari Pengiriman - <span class="fw-normal">Detail</span>
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
            <span class="badge bg-info text-dark">Cari berdasarkan Judul / ISBN</span>
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
                                <label class="form-label">Filter Status</label>
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
                        <h6 class="mb-3">Detail Buku & Verifikasi Penerimaan</h6>

                        <div id="emptyDetail" class="text-center text-muted py-5">
                            <i class="ph-book-open fs-1 d-block mb-2"></i>
                            Pilih salah satu buku dari hasil pencarian untuk melihat detail
                        </div>

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
                                            <label class="form-label text-muted mb-1">Penerbit</label>
                                            <div id="detail_publisher">-</div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label text-muted mb-1">Tahun Terbit</label>
                                            <div id="detail_year">-</div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label text-muted mb-1">Status Penerimaan</label>
                                            <div id="detail_status">-</div>
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
                                </div>
                            </div>

                            <form action="{{ url('physical-delivery/single-verification/update-received-date') }}" method="POST" id="receiveForm">
                                @csrf

                                <input type="hidden" name="letter_detail_id" id="letter_detail_id">
                                <input type="hidden" name="status_code" id="status_code">
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
                                        <input type="text" class="form-control" id="verification_note" readonly
                                            value="Data belum dipilih">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <button type="button" class="btn btn-light" id="btnClearSelection">
                                        Batal Pilih
                                    </button>

                                    <button type="submit" class="btn btn-success d-none" id="btnReceive">
                                        Terima
                                    </button>

                                    <button type="submit" class="btn btn-warning d-none" id="btnReceiveAgain">
                                        Terima Ulang
                                    </button>
                                </div>
                            </form>
                        </div>
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
    }
    .input-group input {
        background-color: #fff !important;
    }
</style>
<script>
    // Dummy data sementara untuk simulasi tampilan
    const dummyBooks = [];
    $('.plus').click(function() {
        let input = $(this).siblings('input');
        input.val(parseInt(input.val()) + 1);
    });

    $('.minus').click(function() {
        let input = $(this).siblings('input');
        let val = parseInt(input.val());
        if (val > 0) input.val(val - 1);
    });
    function getStatusBadge(statusCode, statusText) {
        let badgeClass = 'bg-secondary';

        if (statusCode === 'not_received') badgeClass = 'bg-success';
        if (statusCode === 'received') badgeClass = 'bg-primary';
        if (statusCode === 'verification') badgeClass = 'bg-warning text-dark';

        return `<span class="badge ${badgeClass} badge-status">${statusText}</span>`;
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
                            ${getStatusBadge(item.STATUS_CODE, item.STATUS)}
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

        document.getElementById('letter_detail_id').value = item.LETTER_DETAIL_ID || '';
        document.getElementById('status_code').value = item.STATUS_CODE || '';

        document.getElementById('detail_title').innerText = item.TITLE || '-';
        document.getElementById('detail_isbn').innerText = item.ISBN || '-';
        document.getElementById('detail_publisher').innerText = item.PUB_NAME || item.PUBLISHER || '-';
        document.getElementById('detail_year').innerText = item.PUBLISH_YEAR || '-';
        document.getElementById('detail_status').innerHTML = getStatusBadge(item.STATUS_CODE, item.STATUS);
        document.getElementById('detail_destination_library').innerText = item.DESTINATION_LIBRARY || item.LIBRARY_NAME || '-';
        document.getElementById('detail_received_by').innerText = item.RECEIVED_BY_NAME ?? '-';
        document.getElementById('detail_received_date').innerText = item.RECEIVED_DATE ?? '-';
        document.getElementById('detail_type_of_delivery').innerText = item.TYPE_OF_DELIVERY || '-';
        document.getElementById('detail_jasa_pengiriman').innerText = item.JASA_PENGIRIMAN_NAME || '-';
        document.getElementById('detail_copy').value = item.COPY || '0';
        document.getElementById('detail_quantity').innerText = item.QUANTITY || '0';

        document.getElementById('detail_qty_accept').value = item.QTY_ACCEPT || '0';
        document.getElementById('detail_qty_reject').value = item.QTY_REJECT || '0';

        const receivedDateInput = document.getElementById('received_date');
        const verificationNote = document.getElementById('verification_note');
        const btnReceive = document.getElementById('btnReceive');
        const btnReceiveAgain = document.getElementById('btnReceiveAgain');

        receivedDateInput.value = formatDateForInput(item.RECEIVED_DATE);
        btnReceive.classList.add('d-none');
        btnReceiveAgain.classList.add('d-none');

        if ((item.STATUS_CODE || '').toLowerCase() === 'verification') {
            receivedDateInput.removeAttribute('readonly');
            receivedDateInput.removeAttribute('disabled');
            receivedDateInput.required = true;
            verificationNote.value = 'Data perlu verifikasi. Silakan isi tanggal terima.';
            btnReceive.classList.remove('d-none');
            document.getElementById('action_type').value = 'receive';
            $('.plus, .minus').show();
            $('#detail_quantity').prop('readonly', true);
            $('#detail_qty_accept').prop('readonly', true);
            $('#detail_qty_reject').prop('readonly', true);
           
        } else {
            receivedDateInput.setAttribute('readonly', true);
            receivedDateInput.setAttribute('disabled', true);
            receivedDateInput.required = false;
            verificationNote.value = 'Data sudah diterima sebelumnya. Hanya tersedia aksi terima ulang.';
            btnReceiveAgain.classList.remove('d-none');
            document.getElementById('action_type').value = 'receive_again';
            $('.plus, .minus').hide();
            $('#detail_quantity').prop('disabled', true);
            $('#detail_qty_accept').prop('disabled', true);
            $('#detail_qty_reject').prop('disabled', true);
        }
    }
    function formatDateForInput(dateString) {
        if (!dateString) return '';

        return dateString.split(' ')[0]; // ambil "2025-09-29"
    }
    function clearDetail() {
        document.getElementById('detailPanel').classList.add('d-none');
        document.getElementById('emptyDetail').classList.remove('d-none');
        document.getElementById('letter_detail_id').value = '';

        document.querySelectorAll('.result-item').forEach(x => x.classList.remove('active'));
        document.getElementById('receiveForm').reset();
    }

    function doSearch() {
        const keyword = $('#keyword').val().trim();
        const mode = $('#search_mode').val();

        if (!keyword) {
            renderResults([]);
            clearDetail();
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
            doSearch();
        }
    });

    document.getElementById('btnResetSearch').addEventListener('click', function() {
        document.getElementById('keyword').value = '';
        document.getElementById('search_mode').value = 'auto';
        document.getElementById('status_filter').value = '';
        document.getElementById('search_keyword_hidden').value = '';
        renderResults([]);
        clearDetail();
    });

    document.getElementById('btnClearSelection').addEventListener('click', function() {
        clearDetail();
    });
</script>