<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Laporan &rsaquo; <span class="fw-normal">Kinerja Validasi Koleksi</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <span class="badge bg-primary p-2 bg-opacity-10 text-primary">
                    <i class="ph-chart-line-up me-1"></i>
                    <span id="periodeBadge">—</span>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="content pt-0">

    {{-- ── Filter ──────────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-1">
                        <i class="ph-calendar-blank me-1 text-primary"></i> Dari
                    </label>
                    <input type="date" class="form-control" id="fStart">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-1">
                        <i class="ph-calendar-check me-1 text-primary"></i> Sampai
                    </label>
                    <input type="date" class="form-control" id="fEnd">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-1">
                        <i class="ph-disc me-1 text-primary"></i> Jenis Media
                    </label>
                    <select class="form-select" id="fMedia">
                        <option value="">Semua jenis media</option>
                        @foreach($medias as $m)
                            @php $mId = is_array($m) ? $m['id'] : $m->id; $mName = is_array($m) ? $m['name'] : $m->name; $mJml = is_array($m) ? ($m['jml'] ?? 0) : ($m->jml ?? 0); @endphp
                            <option value="{{ $mId }}" {{ (int) $mId === 141 ? 'selected' : '' }}>
                                {{ $mName }} ({{ number_format($mJml, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-1">
                        <i class="ph-user me-1 text-primary"></i> Validator
                    </label>
                    <select class="form-select" id="fUser">
                        <option value="">Semua validator</option>
                        @foreach($petugas as $pg)
                            @php
                                $uName = is_array($pg) ? ($pg['username'] ?? $pg['USERNAME'] ?? '') : ($pg->username ?? $pg->USERNAME ?? '');
                                $uFull = is_array($pg) ? ($pg['fullname'] ?? $pg['FULLNAME'] ?? '') : ($pg->fullname ?? $pg->FULLNAME ?? '');
                            @endphp
                            <option value="{{ $uName }}">{{ $uFull ? $uFull.' ('.$uName.')' : $uName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold mb-1">
                        <i class="ph-rows me-1 text-primary"></i> Tren per
                    </label>
                    <select class="form-select" id="fGranular">
                        <option value="hari">Hari</option>
                        <option value="bulan" selected>Bulan</option>
                        <option value="tahun">Tahun</option>
                    </select>
                </div>
                <div class="col-md-1 d-grid">
                    <button class="btn btn-primary" id="btnLoad" title="Muat ulang">
                        <i class="ph-arrow-clockwise"></i>
                    </button>
                </div>
                <div class="col-12">
                    <a href="#" class="btn btn-outline-success btn-sm" id="btnExport">
                        <i class="ph-microsoft-excel-logo me-1"></i> Unduh Excel
                    </a>
                    <a href="#" class="btn btn-outline-danger btn-sm" id="btnPdf">
                        <i class="ph-file-pdf me-1"></i> Unduh PDF
                    </a>
                </div>
            </div>
            <div id="pageAlert" class="mt-3"></div>
        </div>
    </div>

    {{-- ── Kartu ringkasan ─────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4" id="cards">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 text-muted mb-1">
                        <i class="ph-books"></i><small>Total Validasi</small>
                    </div>
                    <h3 class="mb-0 fw-semibold" id="cTotal">—</h3>
                    <small class="text-muted" id="cTotalSub">&nbsp;</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 text-muted mb-1">
                        <i class="ph-timer"></i><small>Waktu Kerja / Koleksi</small>
                    </div>
                    <h3 class="mb-0 fw-semibold text-primary" id="cKerja">—</h3>
                    <small class="text-muted">median, jeda &le; <span id="cAmbang">60</span> menit</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 text-muted mb-1">
                        <i class="ph-hourglass-medium"></i><small>Waktu Tunggu</small>
                    </div>
                    <h3 class="mb-0 fw-semibold text-warning" id="cTunggu">—</h3>
                    <small class="text-muted" id="cTungguSub">&nbsp;</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 text-muted mb-1">
                        <i class="ph-users-three"></i><small>Validator Aktif</small>
                    </div>
                    <h3 class="mb-0 fw-semibold" id="cValidator">—</h3>
                    <small class="text-muted" id="cValidatorSub">&nbsp;</small>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info border-0 d-flex align-items-start gap-2">
        <i class="ph-info fs-5"></i>
        <div>
            <strong>Waktu kerja</strong> diperkirakan dari jeda antar validasi berturut-turut oleh orang yang sama di hari yang sama —
            ini perkiraan, bukan pengukuran langsung. <strong>Waktu tunggu</strong> adalah jeda kalender sejak koleksi terdaftar
            sampai divalidasi, yang mencerminkan antrean, bukan kecepatan kerja.
        </div>
    </div>

    <div class="row g-4">

        {{-- ── Per validator ───────────────────────────────────────────────── --}}
        <div class="col-xl-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-bottom bg-primary bg-opacity-10">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ph-user-list text-primary fs-5"></i>
                        <h6 class="mb-0 fw-semibold text-primary">Kinerja per Validator</h6>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0" id="tblValidator">
                            <thead class="table-light">
                                <tr>
                                    <th>Validator</th>
                                    <th class="text-end">Koleksi</th>
                                    <th class="text-end">Hari</th>
                                    <th class="text-end">Per Hari</th>
                                    <th class="text-end">Median</th>
                                    <th class="text-end" title="Jeda di bawah 1 menit — indikasi validasi berkelompok">&lt;1 mnt</th>
                                </tr>
                            </thead>
                            <tbody><tr><td colspan="6" class="text-center text-muted py-4">Klik tombol muat.</td></tr></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Tren + media ────────────────────────────────────────────────── --}}
        <div class="col-xl-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header border-bottom bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ph-trend-up text-primary fs-5"></i>
                        <h6 class="mb-0 fw-semibold">Tren per <span id="granularLabel">Bulan</span></h6>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height:320px; overflow-y:auto">
                        <table class="table table-hover table-sm mb-0" id="tblTren">
                            <thead class="table-light">
                                <tr>
                                    <th>Periode</th>
                                    <th class="text-end">Koleksi</th>
                                    <th class="text-end">Validator</th>
                                    <th class="text-end">Median</th>
                                </tr>
                            </thead>
                            <tbody><tr><td colspan="4" class="text-center text-muted py-4">—</td></tr></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ph-squares-four text-primary fs-5"></i>
                        <h6 class="mb-0 fw-semibold">Per Jenis Media</h6>
                    </div>
                    <small class="text-muted">Selalu semua media, mengabaikan filter di atas.</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0" id="tblMedia">
                            <thead class="table-light">
                                <tr>
                                    <th>Media</th>
                                    <th class="text-end">Koleksi</th>
                                    <th class="text-end">Validator</th>
                                    <th class="text-end">Median</th>
                                </tr>
                            </thead>
                            <tbody><tr><td colspan="4" class="text-center text-muted py-4">—</td></tr></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
$(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    // Default: awal tahun berjalan sampai hari ini.
    var now = new Date();
    $('#fStart').val(now.getFullYear() + '-01-01');
    $('#fEnd').val(now.toISOString().slice(0, 10));

    function esc(v) { return $('<div>').text(v === null || v === undefined ? '' : v).html(); }
    function num(v) { return (v === null || v === undefined || v === '') ? '—' : Number(v).toLocaleString('id-ID'); }
    function mnt(v) { return (v === null || v === undefined || v === '') ? '—' : Number(v).toLocaleString('id-ID') + ' mnt'; }

    function alertBox(type, msg) {
        $('#pageAlert').html('<div class="alert alert-' + type + ' border-0 alert-dismissible fade show py-2 px-3 mb-0">' +
            msg + '<button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button></div>');
    }

    function load() {
        var payload = {
            start:    $('#fStart').val(),
            end:      $('#fEnd').val(),
            media_id: $('#fMedia').val(),
            granular: $('#fGranular').val(),
            user:     $('#fUser').val()
        };

        if (!payload.start || !payload.end) { alertBox('warning', 'Isi rentang tanggalnya dulu.'); return; }
        if (payload.start > payload.end)    { alertBox('warning', 'Tanggal "Dari" melewati tanggal "Sampai".'); return; }

        var $btn = $('#btnLoad').prop('disabled', true).html('<i class="ph-spinner"></i>');
        $('#pageAlert').empty();

        $.post('{{ route('validation_performance.data') }}', payload)
            .done(function (res) {
                var r = res.ringkasan || {};

                $('#cAmbang').text(res.filter.ambang);
                $('#periodeBadge').text(payload.start + ' s.d. ' + payload.end);
                $('#granularLabel').text($('#fGranular option:selected').text());

                $('#cTotal').text(num(r.total_validasi));
                $('#cTotalSub').text(r.jml_hari ? r.jml_hari + ' hari kerja' : ' ');

                $('#cKerja').text(mnt(r.median_menit));

                $('#cTunggu').text(r.median_tunggu === null || r.median_tunggu === undefined
                    ? '—' : num(r.median_tunggu) + ' hari');
                $('#cTungguSub').text(r.diatas_90_hari
                    ? num(r.diatas_90_hari) + ' koleksi menunggu >90 hari' : ' ');

                $('#cValidator').text(num(r.jml_validator));
                $('#cValidatorSub').text(r.jeda_kilat ? num(r.jeda_kilat) + ' jeda <1 menit' : ' ');

                // Per validator
                var rows = '';
                (res.validator || []).forEach(function (v) {
                    rows += '<tr>' +
                        '<td><span class="fw-semibold">' + esc(v.validator || '(tidak dikenal)') + '</span>' +
                        '<br><small class="text-muted">' + esc(v.nip) + '</small></td>' +
                        '<td class="text-end">' + num(v.n_jeda) + '</td>' +
                        '<td class="text-end">' + num(v.hari_kerja) + '</td>' +
                        '<td class="text-end">' + num(v.per_hari) + '</td>' +
                        '<td class="text-end fw-semibold">' + mnt(v.median_menit) + '</td>' +
                        '<td class="text-end text-muted">' + num(v.jeda_kilat) + '</td>' +
                        '</tr>';
                });
                $('#tblValidator tbody').html(rows ||
                    '<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data pada rentang ini.</td></tr>');

                // Tren
                rows = '';
                (res.tren || []).forEach(function (t) {
                    rows += '<tr><td class="fw-semibold">' + esc(t.periode) + '</td>' +
                        '<td class="text-end">' + num(t.n_jeda) + '</td>' +
                        '<td class="text-end">' + num(t.jml_validator) + '</td>' +
                        '<td class="text-end">' + mnt(t.median_menit) + '</td></tr>';
                });
                $('#tblTren tbody').html(rows ||
                    '<tr><td colspan="4" class="text-center text-muted py-4">—</td></tr>');

                // Per media
                rows = '';
                (res.media || []).forEach(function (m) {
                    rows += '<tr><td class="fw-semibold">' + esc(m.media) + '</td>' +
                        '<td class="text-end">' + num(m.n_jeda) + '</td>' +
                        '<td class="text-end">' + num(m.jml_validator) + '</td>' +
                        '<td class="text-end">' + mnt(m.median_menit) + '</td></tr>';
                });
                $('#tblMedia tbody').html(rows ||
                    '<tr><td colspan="4" class="text-center text-muted py-4">—</td></tr>');
            })
            .fail(function (xhr) {
                alertBox('danger', (xhr.responseJSON && xhr.responseJSON.message) || 'Gagal memuat data.');
            })
            .always(function () {
                $btn.prop('disabled', false).html('<i class="ph-arrow-clockwise"></i>');
            });
    }

    $('#btnExport').on('click', function (e) {
        e.preventDefault();
        var start = $('#fStart').val(), end = $('#fEnd').val();
        if (!start || !end) { alertBox('warning', 'Isi rentang tanggalnya dulu.'); return; }

        var qs = $.param({
            start:    start,
            end:      end,
            media_id: $('#fMedia').val(),
            granular: $('#fGranular').val(),
            user:     $('#fUser').val()
        });
        window.location = '{{ route('validation_performance.export') }}?' + qs;
    });

    $('#btnPdf').on('click', function (e) {
        e.preventDefault();
        var start = $('#fStart').val(), end = $('#fEnd').val();
        if (!start || !end) { alertBox('warning', 'Isi rentang tanggalnya dulu.'); return; }

        var qs = $.param({
            start:    start,
            end:      end,
            media_id: $('#fMedia').val(),
            granular: $('#fGranular').val(),
            user:     $('#fUser').val()
        });
        window.open('{{ route('validation_performance.pdf') }}?' + qs, '_blank');
    });

    $('#btnLoad').on('click', load);
    $('#fMedia, #fGranular').on('change', load);
    load();
});
</script>
