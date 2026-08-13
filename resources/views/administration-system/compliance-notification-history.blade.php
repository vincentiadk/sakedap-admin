<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Administrasi Sistem &rsaquo; <span class="fw-normal">Riwayat Notifikasi Kepatuhan</span>
            </h4>
        </div>
    </div>
</div>

<div class="content pt-0">

    <div class="alert alert-info border-0 d-flex align-items-start gap-2">
        <i class="ph-info fs-4"></i>
        <div>
            Cari satu penerbit untuk melihat semua email kepatuhan (pengingat & blokir) yang pernah
            benar-benar terkirim ke alamat aslinya, beserta waktunya — diambil dari log <code>historydata</code>.
            Pencarian sengaja dibatasi per-penerbit supaya tetap cepat.
        </div>
    </div>

    {{-- ── Ringkasan per jenis ─────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header border-bottom bg-success bg-opacity-10">
            <div class="d-flex align-items-center gap-2">
                <i class="ph-chart-bar text-success fs-5"></i>
                <h6 class="mb-0 fw-semibold text-success">Ringkasan Pengiriman per Jenis</h6>
            </div>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-end gap-2 mb-3">
                <div>
                    <label class="form-label fw-semibold small mb-1">Dari tanggal</label>
                    <input type="date" class="form-control form-control-sm" id="summaryStart">
                </div>
                <div>
                    <label class="form-label fw-semibold small mb-1">Sampai tanggal</label>
                    <input type="date" class="form-control form-control-sm" id="summaryEnd">
                </div>
                <button class="btn btn-sm btn-success" id="btnSummary">
                    <i class="ph-magnifying-glass me-1"></i> Tampilkan
                </button>
                <div class="ms-auto" id="summaryTotalBadge"></div>
            </div>

            <div id="summaryLoading" class="text-center text-muted py-4 d-none">
                <i class="ph-spinner"></i> Memuat ringkasan…
            </div>

            <div id="summaryNone" class="text-center text-muted py-4 d-none">
                Tidak ada email kepatuhan tercatat pada rentang tanggal ini.
            </div>

            <div class="table-responsive d-none" id="summaryWrap">
                <small class="text-muted d-block mb-2">
                    <i class="ph-hand-pointing"></i> Klik salah satu baris untuk lihat daftar penerbitnya.
                </small>
                <table class="table table-sm table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Jenis Email</th>
                            <th class="text-end" style="width:14%">Jumlah Penerbit</th>
                            <th class="text-end" style="width:12%">Jumlah Email</th>
                            <th style="width:16%">Paling Awal</th>
                            <th style="width:16%">Paling Akhir</th>
                        </tr>
                    </thead>
                    <tbody id="summaryBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Detail penerbit per jenis (muncul setelah klik baris ringkasan) ─── --}}
    <div class="card border-0 shadow-sm mb-4 d-none" id="detailCard">
        <div class="card-header border-bottom bg-light">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="ph-list-bullets text-primary fs-5"></i>
                    <h6 class="mb-0 fw-semibold">Daftar Penerbit — <span id="detailJenisLabel" class="text-primary"></span></h6>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="#" class="btn btn-sm btn-outline-success" id="btnDetailExport">
                        <i class="ph-file-xls me-1"></i> Unduh Excel
                    </a>
                    <button type="button" class="btn-close btn-sm" id="btnCloseDetail"></button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-2">
                    <thead class="table-light">
                        <tr>
                            <th style="width:12%">ID</th>
                            <th>Nama Penerbit</th>
                            <th style="width:20%">Waktu Kirim</th>
                        </tr>
                    </thead>
                    <tbody id="detailBody"></tbody>
                </table>
            </div>
            <div class="text-center">
                <button class="btn btn-sm btn-outline-primary d-none" id="btnDetailMore">
                    <i class="ph-arrow-down me-1"></i> Muat Lebih Banyak
                </button>
                <div id="detailLoading" class="text-muted small d-none"><i class="ph-spinner"></i> Memuat…</div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- ── Pencarian ───────────────────────────────────────────────────── --}}
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom bg-primary bg-opacity-10">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ph-buildings text-primary fs-5"></i>
                        <h6 class="mb-0 fw-semibold text-primary">Cari Penerbit</h6>
                    </div>
                </div>
                <div class="card-body">
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" id="publisherSearch"
                               placeholder="Ketik nama penerbit atau ID, lalu Enter">
                        <button class="btn btn-outline-primary" type="button" id="btnSearch">
                            <i class="ph-magnifying-glass"></i>
                        </button>
                    </div>

                    <div id="searchResults" class="list-group mb-2 d-none"
                         style="max-height:320px; overflow-y:auto"></div>

                    <div id="selectedPublisher" class="alert alert-info border-0 py-2 px-3 mb-0 d-none">
                        <div class="d-flex align-items-start gap-2">
                            <i class="ph-check-circle fs-5"></i>
                            <div class="flex-grow-1">
                                <strong id="selName"></strong>
                                <div class="small text-muted">
                                    ID <span id="selId"></span> &middot; Status: <span id="selStatus"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="penerbitId">
                </div>
            </div>
        </div>

        {{-- ── Riwayat ─────────────────────────────────────────────────────── --}}
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-bottom bg-light">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ph-clock-counter-clockwise text-primary fs-5"></i>
                            <h6 class="mb-0 fw-semibold">Riwayat Pengiriman</h6>
                        </div>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary d-none" id="totalBadge"></span>
                    </div>
                </div>
                <div class="card-body">

                    <div id="historyEmpty" class="text-center text-muted py-5">
                        <i class="ph-envelope-open" style="font-size:3rem; opacity:.3"></i>
                        <p class="mt-3 mb-0">Cari dan pilih penerbit dulu di sebelah kiri.</p>
                    </div>

                    <div id="historyLoading" class="text-center text-muted py-5 d-none">
                        <i class="ph-spinner"></i> Memuat riwayat…
                    </div>

                    <div id="historyNone" class="text-center text-muted py-5 d-none">
                        <i class="ph-tray" style="font-size:3rem; opacity:.3"></i>
                        <p class="mt-3 mb-0">Belum ada email kepatuhan yang tercatat terkirim ke penerbit ini.</p>
                    </div>

                    <div class="table-responsive d-none" id="historyWrap">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:16%">Waktu</th>
                                    <th>Detail</th>
                                    <th style="width:14%">Oleh</th>
                                </tr>
                            </thead>
                            <tbody id="historyBody"></tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<script>
$(function () {

    function esc(s) { return $('<div>').text(s == null ? '' : s).html(); }

    // ── Ringkasan per jenis ────────────────────────────────────────────────
    function todayStr() {
        var d = new Date();
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }

    $('#summaryStart, #summaryEnd').val(todayStr());

    function loadSummary() {
        $('#summaryNone, #summaryWrap').addClass('d-none');
        $('#summaryLoading').removeClass('d-none');
        $('#summaryTotalBadge').empty();

        $.get('{{ route('notification_history.summary') }}', {
            start_date: $('#summaryStart').val(),
            end_date: $('#summaryEnd').val()
        })
            .done(function (res) {
                $('#summaryLoading').addClass('d-none');

                if (!res.data || !res.data.length) {
                    $('#summaryNone').removeClass('d-none');
                    return;
                }

                $('#summaryTotalBadge').html(
                    '<span class="badge bg-success bg-opacity-10 text-success">' +
                    res.total_penerbit + ' penerbit &middot; ' + res.total_email + ' email' +
                    '</span>'
                );

                var rows = '';
                res.data.forEach(function (r) {
                    rows += '<tr class="summary-row" style="cursor:pointer" data-jenis-key="' + esc(r.jenis_key) + '" data-jenis-label="' + esc(r.jenis) + '">' +
                            '<td>' + esc(r.jenis) + '</td>' +
                            '<td class="text-end fw-semibold">' + r.jumlah_penerbit.toLocaleString('id-ID') + '</td>' +
                            '<td class="text-end">' + r.jumlah_email.toLocaleString('id-ID') + '</td>' +
                            '<td class="small text-nowrap">' + esc(r.paling_awal) + '</td>' +
                            '<td class="small text-nowrap">' + esc(r.paling_akhir) + '</td>' +
                            '</tr>';
                });
                $('#summaryBody').html(rows);
                $('#summaryWrap').removeClass('d-none');
            })
            .fail(function () {
                $('#summaryLoading').addClass('d-none');
                $('#summaryNone').removeClass('d-none').text('Gagal memuat ringkasan.');
            });
    }

    $('#btnSummary').on('click', loadSummary);
    loadSummary(); // muat ringkasan hari ini otomatis saat halaman dibuka

    // ── Detail penerbit per jenis (klik baris ringkasan) ───────────────────
    var detailJenisKey = null;
    var detailOffset = 0;

    $(document).on('click', '.summary-row', function () {
        $('.summary-row').removeClass('table-active');
        $(this).addClass('table-active');

        detailJenisKey = $(this).data('jenis-key');
        detailOffset = 0;
        $('#detailJenisLabel').text($(this).data('jenis-label'));
        $('#detailBody').empty();
        $('#detailCard').removeClass('d-none');
        loadDetail(true);
        updateExportLink();

        $('html, body').animate({ scrollTop: $('#detailCard').offset().top - 80 }, 250);
    });

    function updateExportLink() {
        var qs = $.param({
            jenis: detailJenisKey,
            start_date: $('#summaryStart').val(),
            end_date: $('#summaryEnd').val()
        });
        $('#btnDetailExport').attr('href', '{{ route('notification_history.export') }}?' + qs);
    }

    $('#btnCloseDetail').on('click', function () {
        $('#detailCard').addClass('d-none');
        $('.summary-row').removeClass('table-active');
        detailJenisKey = null;
    });

    function loadDetail(reset) {
        if (!detailJenisKey) return;

        $('#btnDetailMore').addClass('d-none');
        $('#detailLoading').removeClass('d-none');

        $.get('{{ route('notification_history.detail') }}', {
            jenis: detailJenisKey,
            start_date: $('#summaryStart').val(),
            end_date: $('#summaryEnd').val(),
            offset: detailOffset
        })
            .done(function (res) {
                $('#detailLoading').addClass('d-none');

                if (reset && (!res.data || !res.data.length)) {
                    $('#detailBody').html('<tr><td colspan="3" class="text-center text-muted py-3">Tidak ada data.</td></tr>');
                    return;
                }

                var rows = '';
                (res.data || []).forEach(function (d) {
                    rows += '<tr>' +
                            '<td>' + esc(d.penerbit_id) + '</td>' +
                            '<td>' + esc(d.penerbit_nama) + '</td>' +
                            '<td class="small text-nowrap">' + esc(d.waktu) + '</td>' +
                            '</tr>';
                });
                $('#detailBody').append(rows);

                detailOffset += (res.data || []).length;
                $('#btnDetailMore').toggleClass('d-none', !res.has_more);
            })
            .fail(function () {
                $('#detailLoading').addClass('d-none');
                $('#detailBody').append('<tr><td colspan="3" class="text-center text-danger py-3">Gagal memuat data.</td></tr>');
            });
    }

    $('#btnDetailMore').on('click', function () { loadDetail(false); });

    // ── Pencarian penerbit (endpoint sama dengan Uji Notifikasi) ──────────
    function doSearch() {
        var q = $('#publisherSearch').val().trim();
        if (!q) return;

        $('#searchResults').removeClass('d-none').html(
            '<div class="list-group-item text-muted small">Mencari…</div>'
        );

        $.get('{{ route('notification_test.search') }}', { q: q })
            .done(function (res) {
                if (!res.results || !res.results.length) {
                    $('#searchResults').html(
                        '<div class="list-group-item text-muted small">Tidak ada penerbit yang cocok.</div>'
                    );
                    return;
                }

                var html = '';
                res.results.forEach(function (r) {
                    html += '<button type="button" class="list-group-item list-group-item-action py-2 result-item"' +
                            ' data-id="' + r.id + '"' +
                            ' data-nama="' + esc(r.text) + '"' +
                            ' data-status="' + esc(r.status) + '">' +
                            '<div class="fw-semibold small">' + esc(r.text) + '</div>' +
                            '<div class="text-muted" style="font-size:.75rem">' + esc(r.status) + '</div>' +
                            '</button>';
                });
                $('#searchResults').html(html);
            })
            .fail(function () {
                $('#searchResults').html(
                    '<div class="list-group-item text-danger small">Pencarian gagal.</div>'
                );
            });
    }

    $('#btnSearch').on('click', doSearch);
    $('#publisherSearch').on('keypress', function (e) {
        if (e.which === 13) { e.preventDefault(); doSearch(); }
    });

    $(document).on('click', '.result-item', function () {
        var $b = $(this);
        $('#penerbitId').val($b.data('id'));
        $('#selName').text($b.data('nama'));
        $('#selId').text($b.data('id'));
        $('#selStatus').text($b.data('status'));
        $('#selectedPublisher').removeClass('d-none');
        $('#searchResults').addClass('d-none').empty();

        loadHistory($b.data('id'));
    });

    // ── Riwayat ─────────────────────────────────────────────────────────
    function loadHistory(id) {
        $('#historyEmpty, #historyNone, #historyWrap').addClass('d-none');
        $('#historyLoading').removeClass('d-none');
        $('#totalBadge').addClass('d-none');

        $.get('{{ route('notification_history.history') }}', { penerbit: id })
            .done(function (res) {
                $('#historyLoading').addClass('d-none');

                if (!res.data || !res.data.length) {
                    $('#historyNone').removeClass('d-none');
                    return;
                }

                $('#totalBadge').removeClass('d-none').text(res.total + ' email');

                var rows = '';
                res.data.forEach(function (h) {
                    rows += '<tr>' +
                            '<td class="text-nowrap small">' + esc(h.waktu) + '</td>' +
                            '<td class="small">' + esc(h.catatan) + '</td>' +
                            '<td class="small text-muted">' + esc(h.oleh) + '</td>' +
                            '</tr>';
                });
                $('#historyBody').html(rows);
                $('#historyWrap').removeClass('d-none');
            })
            .fail(function () {
                $('#historyLoading').addClass('d-none');
                $('#historyNone').removeClass('d-none').html(
                    '<i class="ph-warning-circle text-danger" style="font-size:3rem"></i>' +
                    '<p class="mt-3 mb-0 text-danger">Gagal memuat riwayat.</p>'
                );
            });
    }

});
</script>
