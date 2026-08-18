@php use App\Helpers\Main; @endphp

<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Laporan &rsaquo; <span class="fw-normal">Kinerja Pencatatan Koleksi Fisik</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <span class="badge bg-success p-2 bg-opacity-10 text-success">
                    <i class="ph-notebook me-1"></i>
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
                        <i class="ph-calendar-blank me-1 text-success"></i> Dari
                    </label>
                    <input type="date" class="form-control" id="fStart">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-1">
                        <i class="ph-calendar-check me-1 text-success"></i> Sampai
                    </label>
                    <input type="date" class="form-control" id="fEnd">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold mb-1">
                        <i class="ph-disc me-1 text-success"></i> Jenis Media
                    </label>
                    <select class="form-select" id="fMedia">
                        <option value="">Semua jenis media</option>
                        @foreach($medias as $m)
                            @php $mId = is_array($m) ? ($m['id'] ?? $m['ID'] ?? '') : ($m->id ?? $m->ID ?? ''); $mName = is_array($m) ? ($m['name'] ?? $m['NAME'] ?? '') : ($m->name ?? $m->NAME ?? ''); @endphp
                            <option value="{{ $mId }}">{{ $mName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold mb-1">
                        <i class="ph-rows me-1 text-success"></i> Tren per
                    </label>
                    <select class="form-select" id="fGranular">
                        <option value="hari">Hari</option>
                        <option value="bulan" selected>Bulan</option>
                        <option value="tahun">Tahun</option>
                    </select>
                </div>
                @if(Main::isPerpusnas())
                <div class="col-md-2">
                    <label class="form-label fw-semibold mb-1">
                        <i class="ph-map-pin me-1 text-success"></i> Provinsi
                    </label>
                    <select class="form-select" id="fProvince">
                        <option value="">Semua provinsi</option>
                        @foreach($provinces as $prov)
                            @php $pId = is_array($prov) ? ($prov['id'] ?? $prov['ID'] ?? '') : ($prov->id ?? $prov->ID ?? ''); $pName = is_array($prov) ? ($prov['name'] ?? $prov['NAME'] ?? '') : ($prov->name ?? $prov->NAME ?? ''); @endphp
                            <option value="{{ $pId }}">{{ $pName }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-md-{{ Main::isPerpusnas() ? 12 : 2 }} d-flex gap-2 align-items-end">
                    <button class="btn btn-success px-4" id="btnLoad">
                        <i class="ph-magnifying-glass me-1"></i> Tampilkan
                    </button>
                    <a href="#" class="btn btn-outline-success btn-sm" id="btnExport" style="height:38px;line-height:26px;">
                        <i class="ph-microsoft-excel-logo me-1"></i> Ekspor
                    </a>
                </div>
            </div>
            <div id="pageAlert" class="mt-3"></div>
        </div>
    </div>

    {{-- ── Loading ──────────────────────────────────────────────────────────── --}}
    <div id="loadingState" class="text-center py-5 d-none">
        <div class="spinner-border text-success mb-2" role="status"></div>
        <div class="text-muted">Mengambil data…</div>
    </div>

    {{-- ── Empty state ──────────────────────────────────────────────────────── --}}
    <div id="emptyState" class="text-center py-5 d-none">
        <i class="ph-magnifying-glass fs-1 text-muted opacity-50"></i>
        <div class="text-muted mt-2">Tidak ada data pada periode ini.</div>
    </div>

    {{-- ── Kartu ringkasan ─────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4 d-none" id="cardsSection">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 text-muted mb-1">
                        <i class="ph-books"></i><small>Total Judul Dicatat</small>
                    </div>
                    <h3 class="mb-0 fw-semibold text-success" id="cTotal">—</h3>
                    <small class="text-muted" id="cTotalSub">&nbsp;</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 text-muted mb-1">
                        <i class="ph-calendar"></i><small>Hari Aktif</small>
                    </div>
                    <h3 class="mb-0 fw-semibold" id="cHari">—</h3>
                    <small class="text-muted">hari dengan pencatatan</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 text-muted mb-1">
                        <i class="ph-chart-line-up"></i><small>Rata-rata / Hari</small>
                    </div>
                    <h3 class="mb-0 fw-semibold text-primary" id="cRata2">—</h3>
                    <small class="text-muted">judul per hari aktif</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 text-muted mb-1">
                        <i class="ph-buildings"></i><small>Cabang Aktif</small>
                    </div>
                    <h3 class="mb-0 fw-semibold" id="cCabang">—</h3>
                    <small class="text-muted">cabang yang mencatat</small>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Grafik tren ──────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4 d-none" id="chartSection">
        <div class="card-header border-bottom bg-light">
            <div class="d-flex align-items-center gap-2">
                <i class="ph-trend-up text-success fs-5"></i>
                <h6 class="mb-0 fw-semibold">Tren Pencatatan per <span id="granularLabel">Bulan</span></h6>
            </div>
        </div>
        <div class="card-body">
            <div id="trenChart" style="height:300px;"></div>
        </div>
    </div>

    {{-- ── Per Petugas ──────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4 d-none" id="petugasSection">
        <div class="card-header border-bottom bg-light">
            <div class="d-flex align-items-center gap-2">
                <i class="ph-user-list text-success fs-5"></i>
                <h6 class="mb-0 fw-semibold">Per Petugas Pencatat</h6>
            </div>
            <small class="text-muted">Berdasarkan historydata INSERT pada tabel collections.</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0" id="tblPetugas">
                    <thead class="table-light">
                        <tr>
                            <th>Petugas</th>
                            <th class="text-end">Total Judul</th>
                            <th class="text-end">Hari Aktif</th>
                            <th class="text-end">Rata-rata / Hari</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Per Jenis Media + Per Cabang ────────────────────────────────────── --}}
    <div class="row g-4 d-none" id="tablesSection">

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-bottom bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ph-squares-four text-success fs-5"></i>
                        <h6 class="mb-0 fw-semibold">Per Jenis Media</h6>
                    </div>
                    <small class="text-muted">Selalu semua jenis media, mengabaikan filter di atas.</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0" id="tblMedia">
                            <thead class="table-light">
                                <tr>
                                    <th>Jenis Media</th>
                                    <th class="text-end">Judul</th>
                                    <th style="width:120px">Proporsi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @if(Main::isPerpusnas())
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-bottom bg-light">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ph-buildings text-success fs-5"></i>
                        <h6 class="mb-0 fw-semibold">Per Cabang</h6>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height:400px;overflow-y:auto;">
                        <table class="table table-hover table-sm mb-0" id="tblCabang">
                            <thead class="table-light">
                                <tr>
                                    <th>Cabang</th>
                                    <th>Provinsi</th>
                                    <th class="text-end">Judul</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @else
        {{-- Non-perpusnas: tampilkan per cabang full-width tapi disembunyikan karena hanya 1 cabang --}}
        @endif

    </div>
</div>

@push('echart-js')
<script src="{{ asset('themes/js/vendor/visualization/echarts/echarts.min.js') }}"></script>
@endpush

<script>
(function () {
    'use strict';

    // ── Setup AJAX ───────────────────────────────────────────────────────────
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // ── Default filter ───────────────────────────────────────────────────────
    var now = new Date();
    document.getElementById('fStart').value = now.getFullYear() + '-01-01';
    document.getElementById('fEnd').value   = now.toISOString().slice(0, 10);

    // ── Chart ────────────────────────────────────────────────────────────────
    var trenChart = null;

    // ── Helpers ──────────────────────────────────────────────────────────────
    function num(v) {
        if (v === null || v === undefined || v === '') return '—';
        return Number(v).toLocaleString('id-ID');
    }
    function esc(v) {
        var d = document.createElement('div');
        d.textContent = (v === null || v === undefined) ? '' : String(v);
        return d.innerHTML;
    }
    function alertBox(type, msg) {
        document.getElementById('pageAlert').innerHTML =
            '<div class="alert alert-' + type + ' border-0 alert-dismissible fade show py-2 px-3 mb-0">' +
            msg + '<button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button></div>';
    }
    function show(id)  { document.getElementById(id).classList.remove('d-none'); }
    function hide(id)  { document.getElementById(id).classList.add('d-none'); }

    function hideResults() {
        ['cardsSection','chartSection','petugasSection','tablesSection','emptyState'].forEach(hide);
    }

    // ── Load ─────────────────────────────────────────────────────────────────
    function load() {
        var start    = document.getElementById('fStart').value;
        var end      = document.getElementById('fEnd').value;
        var mediaId  = document.getElementById('fMedia').value;
        var granular = document.getElementById('fGranular').value;
        var province = document.getElementById('fProvince') ? document.getElementById('fProvince').value : '';

        if (!start || !end) { alertBox('warning', 'Isi rentang tanggalnya dulu.'); return; }
        if (start > end)    { alertBox('warning', 'Tanggal "Dari" melewati "Sampai".'); return; }

        document.getElementById('pageAlert').innerHTML = '';
        hideResults();
        show('loadingState');

        var btn = document.getElementById('btnLoad');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memuat…';

        // Build form data
        var body = new URLSearchParams({
            _token:      csrfToken,
            start:       start,
            end:         end,
            media_id:    mediaId,
            granular:    granular,
            province_id: province,
        });

        fetch('{{ route("recording_performance.data") }}', {
            method:  'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': csrfToken },
            body:    body.toString(),
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            hide('loadingState');

            if (!res.success) {
                alertBox('danger', res.message || 'Gagal memuat data.');
                return;
            }

            renderCards(res.ringkasan || {});
            renderChart(res.tren || []);
            renderPetugas(res.per_petugas || []);
            renderMedia(res.per_media || []);
            renderCabang(res.per_cabang || []);

            document.getElementById('periodeBadge').textContent = start + ' s.d. ' + end;
            document.getElementById('granularLabel').textContent =
                document.getElementById('fGranular').options[document.getElementById('fGranular').selectedIndex].text;

            var total = (res.ringkasan || {}).total_judul || 0;
            if (!total) {
                show('emptyState');
            } else {
                show('cardsSection');
                show('chartSection');
                show('petugasSection');
                show('tablesSection');
                setTimeout(function () { trenChart && trenChart.resize(); }, 50);
            }
        })
        .catch(function (err) {
            hide('loadingState');
            alertBox('danger', 'Terjadi kesalahan: ' + err.message);
        })
        .finally(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="ph-magnifying-glass me-1"></i> Tampilkan';
        });
    }

    // ── Render cards ─────────────────────────────────────────────────────────
    function renderCards(r) {
        document.getElementById('cTotal').textContent   = num(r.total_judul);
        document.getElementById('cTotalSub').textContent = (r.jml_hari ? r.jml_hari + ' hari aktif' : ' ');
        document.getElementById('cHari').textContent    = num(r.jml_hari);
        document.getElementById('cRata2').textContent   = num(r.rata2_hari);
        document.getElementById('cCabang').textContent  = num(r.jml_cabang);
    }

    // ── Render chart ─────────────────────────────────────────────────────────
    function renderChart(rows) {
        var labels = rows.map(function (r) { return r.PERIODE || ''; });
        var values = rows.map(function (r) { return r.TOTAL_JUDUL || 0; });

        var el = document.getElementById('trenChart');
        if (!trenChart) {
            trenChart = echarts.init(el);
        }
        trenChart.setOption({
            tooltip: { trigger: 'axis' },
            grid:    { left: 60, right: 20, top: 20, bottom: 60 },
            xAxis: {
                type: 'category',
                data: labels,
                axisLabel: { rotate: labels.length > 12 ? 45 : 0, fontSize: 11 },
            },
            yAxis: { type: 'value', name: 'Judul' },
            series: [{
                name: 'Total Judul',
                type: 'bar',
                data: values,
                itemStyle: { color: '#28a745' },
                label: { show: values.length <= 24, position: 'top', fontSize: 10,
                    formatter: function (p) { return p.value > 0 ? p.value.toLocaleString('id-ID') : ''; } },
            }],
        });
    }

    // ── Render per petugas ───────────────────────────────────────────────────
    function renderPetugas(rows) {
        var html = '';
        rows.forEach(function (r) {
            html += '<tr>' +
                '<td class="fw-semibold"><i class="ph-user me-1 text-muted"></i>' + esc(r.PETUGAS) + '</td>' +
                '<td class="text-end">' + num(r.TOTAL_JUDUL) + '</td>' +
                '<td class="text-end">' + num(r.JML_HARI) + '</td>' +
                '<td class="text-end text-primary fw-semibold">' + num(r.RATA2_HARI) + '</td>' +
                '</tr>';
        });
        document.querySelector('#tblPetugas tbody').innerHTML = html ||
            '<tr><td colspan="4" class="text-center text-muted py-4">Tidak ada data histori pencatatan.</td></tr>';
    }

    // ── Render per media ─────────────────────────────────────────────────────
    function renderMedia(rows) {
        var maxJudul = Math.max.apply(null, rows.map(function (r) { return r.TOTAL_JUDUL || 0; }).concat([1]));
        var html = '';
        rows.forEach(function (r) {
            var judul = r.TOTAL_JUDUL || 0;
            var pct   = Math.round(judul / maxJudul * 100);
            html += '<tr>' +
                '<td class="fw-semibold">' + esc(r.NAMA_MEDIA) + '</td>' +
                '<td class="text-end">' + num(judul) + '</td>' +
                '<td><div class="progress" style="height:8px;"><div class="progress-bar bg-success" style="width:' + pct + '%"></div></div></td>' +
                '</tr>';
        });
        document.querySelector('#tblMedia tbody').innerHTML = html ||
            '<tr><td colspan="3" class="text-center text-muted py-4">Tidak ada data.</td></tr>';
    }

    // ── Render per cabang ────────────────────────────────────────────────────
    function renderCabang(rows) {
        var el = document.querySelector('#tblCabang tbody');
        if (!el) return;
        var html = '';
        rows.forEach(function (r) {
            html += '<tr>' +
                '<td class="fw-semibold">' + esc(r.NAMA_CABANG) + '</td>' +
                '<td class="text-muted">' + esc(r.NAMA_PROPINSI) + '</td>' +
                '<td class="text-end">' + num(r.TOTAL_JUDUL) + '</td>' +
                '</tr>';
        });
        el.innerHTML = html ||
            '<tr><td colspan="3" class="text-center text-muted py-4">Tidak ada data.</td></tr>';
    }

    // ── Export ───────────────────────────────────────────────────────────────
    document.getElementById('btnExport').addEventListener('click', function (e) {
        e.preventDefault();
        var start = document.getElementById('fStart').value;
        var end   = document.getElementById('fEnd').value;
        if (!start || !end) { alertBox('warning', 'Isi rentang tanggalnya dulu.'); return; }

        var params = new URLSearchParams({
            start:       start,
            end:         end,
            media_id:    document.getElementById('fMedia').value,
            granular:    document.getElementById('fGranular').value,
            province_id: document.getElementById('fProvince') ? document.getElementById('fProvince').value : '',
        });
        window.location = '{{ route("recording_performance.export") }}?' + params.toString();
    });

    // ── Events ───────────────────────────────────────────────────────────────
    document.getElementById('btnLoad').addEventListener('click', load);
    document.getElementById('fGranular').addEventListener('change', function () {
        if (!document.getElementById('cardsSection').classList.contains('d-none')) load();
    });

    window.addEventListener('resize', function () { trenChart && trenChart.resize(); });

})();
</script>
