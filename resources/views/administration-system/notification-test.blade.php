<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Administrasi Sistem &rsaquo; <span class="fw-normal">Uji Notifikasi Kepatuhan</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <span class="badge bg-warning p-2 bg-opacity-10 text-warning">
                    <i class="ph-flask me-1"></i>
                    Mode Uji Coba
                </span>
            </div>
        </div>
    </div>
</div>

<div class="content pt-0">

    <div class="alert alert-warning border-0 d-flex align-items-start gap-2">
        <i class="ph-shield-check fs-4"></i>
        <div>
            <strong>Halaman ini tidak bisa mengirim ke penerbit.</strong>
            Semua email diarahkan ke alamat tester yang Anda ketik, dan penanda
            <code>IS_NOTIF_*</code> / <code>TGL_NOTIF_*</code> di tabel <code>PENERBIT</code> tidak pernah ditulis dari sini —
            jadi penerbit tidak akan tercatat "sudah dinotifikasi" gara-gara pengujian.
            Pengiriman sesungguhnya tetap lewat <code>compliance:send-notifications</code>.
        </div>
    </div>

    <div class="row g-4">

        {{-- ── Form ────────────────────────────────────────────────────────── --}}
        <div class="col-xl-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom bg-primary bg-opacity-10">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ph-sliders text-primary fs-5"></i>
                        <h6 class="mb-0 fw-semibold text-primary">Parameter Uji</h6>
                    </div>
                </div>
                <div class="card-body">

                    {{-- Jenis notifikasi --}}
                    <label class="form-label fw-semibold mb-2">
                        <i class="ph-envelope-simple me-1 text-primary"></i> Jenis Notifikasi
                    </label>

                    <div class="d-grid gap-2 mb-4">
                        @foreach($jenis as $key => $def)
                            <label class="border rounded p-2 d-flex align-items-start gap-2 jenis-option
                                          {{ $def['terisi'] ? '' : 'opacity-50' }}"
                                   style="cursor:{{ $def['terisi'] ? 'pointer' : 'not-allowed' }}">
                                <input type="radio" name="jenis" value="{{ $key }}"
                                       class="form-check-input mt-1 flex-shrink-0"
                                       {{ $def['terisi'] ? '' : 'disabled' }}
                                       {{ $loop->first && $def['terisi'] ? 'checked' : '' }}>
                                <span class="flex-grow-1">
                                    <span class="d-block fw-semibold">
                                        {{ $def['label'] }}
                                        @if($def['reminder'])
                                            <span class="badge bg-warning bg-opacity-10 text-warning ms-1">Pengingat</span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger ms-1">Blokir</span>
                                        @endif
                                    </span>
                                    <small class="text-muted d-block" style="font-size:.78rem">
                                        Penanda: <code>{{ $def['flag'] }}</code>
                                    </small>
                                    @unless($def['terisi'])
                                        <small class="text-danger d-block" style="font-size:.78rem">
                                            <i class="ph-warning-circle"></i>
                                            Redaksi <code>{{ $def['param'] }}</code> belum diisi
                                        </small>
                                    @endunless
                                </span>
                            </label>
                        @endforeach
                    </div>

                    {{-- Penerbit --}}
                    <label class="form-label fw-semibold">
                        <i class="ph-buildings me-1 text-primary"></i> Penerbit
                    </label>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" id="publisherSearch"
                               placeholder="Ketik nama penerbit atau ID, lalu Enter">
                        <button class="btn btn-outline-primary" type="button" id="btnSearch">
                            <i class="ph-magnifying-glass"></i>
                        </button>
                    </div>

                    <div id="searchResults" class="list-group mb-2 d-none"
                         style="max-height:220px; overflow-y:auto"></div>

                    <div id="selectedPublisher" class="alert alert-info border-0 py-2 px-3 mb-4 d-none">
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

                    {{-- Email tester --}}
                    <label class="form-label fw-semibold">
                        <i class="ph-paper-plane-tilt me-1 text-primary"></i> Email Tester
                    </label>
                    <input type="email" class="form-control mb-1" id="testerEmail"
                           placeholder="nama@domain.com">
                    <small class="text-muted d-block mb-4">
                        Email uji akan dikirim ke alamat ini, bukan ke email penerbit.
                    </small>

                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary flex-grow-1" id="btnPreview">
                            <i class="ph-eye me-1"></i> Pratinjau
                        </button>
                        <button class="btn btn-primary flex-grow-1" id="btnSend">
                            <i class="ph-paper-plane-right me-1"></i> Kirim ke Tester
                        </button>
                    </div>

                    <div id="formAlert" class="mt-3"></div>
                </div>
            </div>
        </div>

        {{-- ── Pratinjau ───────────────────────────────────────────────────── --}}
        <div class="col-xl-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-bottom bg-light">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ph-eye text-primary fs-5"></i>
                            <h6 class="mb-0 fw-semibold">Pratinjau Email</h6>
                        </div>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary d-none" id="previewSubjectBadge"></span>
                    </div>
                </div>
                <div class="card-body">

                    <div id="previewEmpty" class="text-center text-muted py-5">
                        <i class="ph-envelope-open" style="font-size:3rem; opacity:.3"></i>
                        <p class="mt-3 mb-0">Pilih jenis notifikasi dan penerbit, lalu klik <strong>Pratinjau</strong>.</p>
                    </div>

                    <div id="previewWrap" class="d-none">
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Subject</small>
                            <div class="border rounded px-3 py-2 bg-light" id="previewSubject"></div>
                        </div>

                        <iframe id="previewFrame" class="w-100 border rounded"
                                style="height:520px; background:#f4f4f4"></iframe>

                        <div class="mt-3">
                            <small class="text-muted d-block mb-1">Nilai placeholder yang disubstitusi</small>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0" id="varsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:45%">Placeholder</th>
                                            <th>Nilai</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<script>
$(function () {
    var token = $('meta[name="csrf-token"]').attr('content');

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': token } });

    function alertBox(type, message) {
        $('#formAlert').html(
            '<div class="alert alert-' + type + ' border-0 alert-dismissible fade show py-2 px-3 mb-0">' +
            message +
            '<button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button></div>'
        );
    }

    function currentPayload() {
        var jenis = $('input[name="jenis"]:checked').val();
        var penerbit = $('#penerbitId').val();

        if (!jenis) { alertBox('warning', 'Pilih jenis notifikasi dulu.'); return null; }
        if (!penerbit) { alertBox('warning', 'Pilih penerbit dulu lewat kotak pencarian.'); return null; }

        return { jenis: jenis, penerbit: penerbit };
    }

    // ── Pencarian penerbit ───────────────────────────────────────────────
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
                            ' data-nama="' + $('<div>').text(r.text).html() + '"' +
                            ' data-status="' + $('<div>').text(r.status).html() + '">' +
                            '<div class="fw-semibold small">' + $('<div>').text(r.text).html() + '</div>' +
                            '<div class="text-muted" style="font-size:.75rem">' +
                            $('<div>').text(r.status).html() +
                            (r.email ? ' &middot; ' + $('<div>').text(r.email).html() : ' &middot; email kosong') +
                            '</div></button>';
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
    });

    // ── Pratinjau ────────────────────────────────────────────────────────
    $('#btnPreview').on('click', function () {
        var payload = currentPayload();
        if (!payload) return;

        var $btn = $(this).prop('disabled', true);
        $btn.html('<i class="ph-spinner me-1"></i> Merender…');

        $.post('{{ route('notification_test.preview') }}', payload)
            .done(function (res) {
                $('#previewEmpty').addClass('d-none');
                $('#previewWrap').removeClass('d-none');
                $('#previewSubject').text(res.subject);
                $('#previewFrame')[0].srcdoc = res.html;

                var rows = '';
                $.each(res.vars, function (k, v) {
                    rows += '<tr><td><code>' + $('<div>').text(k).html() + '</code></td>' +
                            '<td>' + $('<div>').text(v).html() + '</td></tr>';
                });
                $('#varsTable tbody').html(rows);

                alertBox('success', 'Pratinjau dirender memakai data asli penerbit tersebut.');
            })
            .fail(function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Gagal merender pratinjau.';
                alertBox('danger', msg);
            })
            .always(function () {
                $btn.prop('disabled', false).html('<i class="ph-eye me-1"></i> Pratinjau');
            });
    });

    // ── Kirim ────────────────────────────────────────────────────────────
    $('#btnSend').on('click', function () {
        var payload = currentPayload();
        if (!payload) return;

        var email = $('#testerEmail').val().trim();
        if (!email) { alertBox('warning', 'Isi alamat email tester dulu.'); return; }

        payload.email = email;

        var $btn = $(this).prop('disabled', true);
        $btn.html('<i class="ph-spinner me-1"></i> Mengirim…');

        $.post('{{ route('notification_test.send') }}', payload)
            .done(function (res) {
                alertBox('success', res.message);
            })
            .fail(function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Gagal mengirim email.';
                alertBox('danger', msg);
            })
            .always(function () {
                $btn.prop('disabled', false).html('<i class="ph-paper-plane-right me-1"></i> Kirim ke Tester');
            });
    });
});
</script>
