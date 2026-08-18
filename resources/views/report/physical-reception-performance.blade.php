<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Laporan &rsaquo; <span class="fw-normal">Kinerja Penerimaan Karya Fisik</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success p-2 bg-opacity-10 text-success">
                    <i class="ph-books me-1"></i>
                    <span id="periodeBadge">—</span>
                </span>
                <a href="{{ route('physical_reception_performance.export') }}" id="btnExport"
                   class="btn btn-sm btn-outline-primary d-none">
                    <i class="ph-microsoft-excel-logo me-1"></i> Unduh Excel
                </a>
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
                        <i class="ph-calendar-blank me-1 text-primary"></i> Dari Tanggal
                    </label>
                    <input type="date" class="form-control" id="fStart">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-1">
                        <i class="ph-calendar-check me-1 text-primary"></i> Sampai Tanggal
                    </label>
                    <input type="date" class="form-control" id="fEnd">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold mb-1">
                        <i class="ph-disc me-1 text-primary"></i> Jenis Media
                    </label>
                    <select class="form-select" id="fMedia">
                        <option value="">Semua Media</option>
                        @foreach($medias as $m)
                            <option value="{{ $m->ID ?? $m['id'] ?? '' }}">{{ $m->NAME ?? $m['name'] ?? '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold mb-1">
                        <i class="ph-chart-bar me-1 text-primary"></i> Granularitas
                    </label>
                    <select class="form-select" id="fGranular">
                        <option value="bulan" selected>Per Bulan</option>
                        <option value="hari">Per Hari</option>
                        <option value="tahun">Per Tahun</option>
                    </select>
                </div>
                @if(Main::isPerpusnas())
                <div class="col-md-2">
                    <label class="form-label fw-semibold mb-1">
                        <i class="ph-building-office me-1 text-primary"></i> Tujuan Pengiriman
                    </label>
                    <select class="form-select" id="fTujuan">
                        <option value="">Semua Tujuan</option>
                        <option value="perpusnas">Perpusnas</option>
                        <option value="provinsi">Provinsi</option>
                    </select>
                </div>
                @endif
                @if(Main::isPerpusnas())
                <div class="col-md-2">
                    <label class="form-label fw-semibold mb-1">
                        <i class="ph-map-pin me-1 text-primary"></i> Provinsi
                    </label>
                    <select class="form-select" id="fProvince" data-placeholder="Semua Provinsi"></select>
                </div>
                @endif
                <div class="col-md-3">
                    <label class="form-label fw-semibold mb-1">
                        <i class="ph-user me-1 text-primary"></i> Petugas
                    </label>
                    <select class="form-select" id="fUser">
                        <option value="">Semua petugas</option>
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
                    <button class="btn btn-primary w-100" id="btnLoad">
                        <i class="ph-magnifying-glass me-1"></i> Tampilkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Kartu Ringkasan ─────────────────────────────────────────────────── --}}
    <div class="row g-3 mb-4 d-none" id="cardsSection">
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-3">
                    <div class="text-primary mb-1"><i class="ph-envelope-simple ph-2x"></i></div>
                    <div class="fw-bold fs-4" id="cSurat">—</div>
                    <div class="text-muted small">Surat Masuk</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-3">
                    <div class="text-info mb-1"><i class="ph-books ph-2x"></i></div>
                    <div class="fw-bold fs-4" id="cJudul">—</div>
                    <div class="text-muted small">Judul</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-3">
                    <div class="text-success mb-1"><i class="ph-check-circle ph-2x"></i></div>
                    <div class="fw-bold fs-4" id="cAccept">—</div>
                    <div class="text-muted small">Copy Diterima</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-3">
                    <div class="text-danger mb-1"><i class="ph-x-circle ph-2x"></i></div>
                    <div class="fw-bold fs-4" id="cReject">—</div>
                    <div class="text-muted small">Copy Ditolak</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-3">
                    <div class="text-warning mb-1"><i class="ph-gift ph-2x"></i></div>
                    <div class="fw-bold fs-4" id="cHibah">—</div>
                    <div class="text-muted small">Hibah</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-3">
                    <div class="text-secondary mb-1"><i class="ph-arrow-u-up-left ph-2x"></i></div>
                    <div class="fw-bold fs-4" id="cRetur">—</div>
                    <div class="text-muted small">Retur</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Tren ────────────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4 d-none" id="chartSection">
        <div class="card-header border-bottom d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-semibold"><i class="ph-chart-line me-1 text-primary"></i> Tren Penerimaan</h6>
            <div class="d-flex gap-2 align-items-center">
                <select class="form-select form-select-sm" id="chartToggle" style="width:auto">
                    <option value="total_accept">Copy Diterima</option>
                    <option value="total_judul">Judul</option>
                    <option value="total_surat">Surat</option>
                    <option value="total_reject">Ditolak</option>
                </select>
            </div>
        </div>
        <div class="card-body p-0">
            <div id="trenChart" style="height:300px;width:100%"></div>
        </div>
    </div>

    {{-- ── Per Jenis Media ─────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4 d-none" id="mediaSection">
        <div class="card-header border-bottom">
            <h6 class="mb-0 fw-semibold"><i class="ph-squares-four me-1 text-primary"></i> Per Jenis Media <small class="text-muted fw-normal">(selalu semua media, abaikan filter Jenis Media di atas)</small></h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Jenis Media</th>
                            <th class="text-center">Judul</th>
                            <th class="text-center text-success">Diterima</th>
                            <th class="text-center text-danger">Ditolak</th>
                            <th class="text-center text-warning">Hibah</th>
                            <th class="text-center text-secondary">Retur</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyMedia">
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Per Cabang (Perpusnas only) + Per Petugas ─────────────────────── --}}
    <div class="row g-3 mb-4 d-none" id="tablesSection">
        @if(Main::isPerpusnas())
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-bottom">
                    <h6 class="mb-0 fw-semibold"><i class="ph-buildings me-1 text-primary"></i> Per Cabang</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0" id="tblCabang">
                            <thead class="table-light">
                                <tr>
                                    <th>Cabang</th>
                                    <th>Tujuan</th>
                                    <th class="text-center">Surat</th>
                                    <th class="text-center">Judul</th>
                                    <th class="text-center text-success">Diterima</th>
                                    <th class="text-center text-danger">Ditolak</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyCabang">
                                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="col-lg-{{ Main::isPerpusnas() ? 6 : 12 }}">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-bottom">
                    <h6 class="mb-0 fw-semibold"><i class="ph-user-circle me-1 text-primary"></i> Per Petugas</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0" id="tblPetugas">
                            <thead class="table-light">
                                <tr>
                                    <th>Petugas</th>
                                    <th class="text-center">Judul</th>
                                    <th class="text-center text-success">Diterima</th>
                                    <th class="text-center text-danger">Ditolak</th>
                                    <th class="text-center text-warning">Hibah</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyPetugas">
                                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada data</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Empty state ─────────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm" id="emptyState">
        <div class="card-body text-center py-5">
            <i class="ph-chart-bar text-muted" style="font-size:3rem"></i>
            <p class="text-muted mt-3 mb-0">Pilih rentang tanggal lalu klik <strong>Tampilkan</strong></p>
        </div>
    </div>

</div>

@push('echart-js')
    <script src="{{ asset('themes/js/vendor/visualization/echarts/echarts.min.js') }}"></script>
@endpush

<script>
(function () {
    // ── init filter default (tahun berjalan) ────────────────────────────────
    const now   = new Date();
    const y     = now.getFullYear();
    const pad   = n => String(n).padStart(2, '0');
    document.getElementById('fStart').value = `${y}-01-01`;
    document.getElementById('fEnd').value   = `${y}-${pad(now.getMonth()+1)}-${pad(now.getDate())}`;

    @if(Main::isPerpusnas())
    select2Serverside('#fProvince', 'location', { for: 'province' }, { minimumInputLength: 0 });
    @endif

    let trenChart = null;
    let lastPayload = null;

    document.getElementById('btnLoad').addEventListener('click', loadData);
    document.getElementById('chartToggle').addEventListener('change', function() {
        if (lastPayload) {
            const field = this.value;
            const labels = { total_accept:'Copy Diterima', total_judul:'Judul', total_surat:'Surat', total_reject:'Ditolak' };
            renderChart(lastPayload.tren, field, labels[field] || field);
        }
    });

    function loadData() {
        const start   = document.getElementById('fStart').value;
        const end     = document.getElementById('fEnd').value;
        const mediaId = document.getElementById('fMedia').value;
        const granular= document.getElementById('fGranular').value;
        const tujuan  = document.getElementById('fTujuan')?.value ?? '';
        const prov    = document.getElementById('fProvince')?.value ?? '';
        const user    = document.getElementById('fUser')?.value ?? '';

        if (!start || !end) {
            Swal.fire('Perhatian', 'Isi rentang tanggal terlebih dahulu.', 'warning');
            return;
        }

        const btn = document.getElementById('btnLoad');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memuat...';

        onLoading('show', 'body');

        const params = new URLSearchParams({ start, end, granular });
        if (mediaId)  params.set('media_id', mediaId);
        if (tujuan)   params.set('tujuan', tujuan);
        if (prov)     params.set('province_id', prov);
        if (user)     params.set('user', user);

        fetch('{{ route("physical_reception_performance.data") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: params.toString(),
        })
        .then(r => r.json())
        .then(function(res) {
            if (!res.success) throw new Error(res.message || 'Gagal memuat data');
            lastPayload = res;

            // Badge periode
            document.getElementById('periodeBadge').textContent =
                new Date(start).toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' })
                + ' – '
                + new Date(end).toLocaleDateString('id-ID', { day:'numeric', month:'short', year:'numeric' });

            renderCards(res.ringkasan);
            renderChart(res.tren, 'total_accept', 'Copy Diterima');
            renderMedia(res.per_media);
            renderCabang(res.per_cabang);
            renderPetugas(res.per_petugas);

            // Show sections
            document.getElementById('emptyState').classList.add('d-none');
            document.getElementById('cardsSection').classList.remove('d-none');
            document.getElementById('chartSection').classList.remove('d-none');
            document.getElementById('mediaSection').classList.remove('d-none');
            document.getElementById('tablesSection').classList.remove('d-none');

            // Resize chart setelah container visible (d-none → display block butuh 1 tick)
            setTimeout(() => trenChart && trenChart.resize(), 50);

            // Build export URL
            const exportUrl = '{{ route("physical_reception_performance.export") }}?' + params.toString();
            const exportBtn = document.getElementById('btnExport');
            exportBtn.href  = exportUrl;
            exportBtn.classList.remove('d-none');
        })
        .catch(function(err) {
            Swal.fire('Gagal', err.message || 'Terjadi kesalahan saat memuat data.', 'error');
        })
        .finally(function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="ph-magnifying-glass me-1"></i> Tampilkan';
            onLoading('close', 'body');
        });
    }

    function fmt(n) {
        return Number(n || 0).toLocaleString('id-ID');
    }

    function renderCards(r) {
        document.getElementById('cSurat').textContent  = fmt(r.total_surat);
        document.getElementById('cJudul').textContent  = fmt(r.total_judul);
        document.getElementById('cAccept').textContent = fmt(r.total_accept);
        document.getElementById('cReject').textContent = fmt(r.total_reject);
        document.getElementById('cHibah').textContent  = fmt(r.total_hibah);
        document.getElementById('cRetur').textContent  = fmt(r.total_retur);
    }

    function renderChart(tren, field, label) {
        const chartDom = document.getElementById('trenChart');
        if (trenChart) { trenChart.dispose(); }
        trenChart = echarts.init(chartDom, null, { renderer: 'canvas' });

        const labels = tren.map(r => r.periode);
        const values = tren.map(r => r[field] || 0);

        trenChart.setOption({
            tooltip: {
                trigger: 'axis',
                formatter: function(params) {
                    return params[0].name + '<br>' + label + ': <b>' + params[0].value.toLocaleString('id-ID') + '</b>';
                }
            },
            grid: { left: 60, right: 20, top: 20, bottom: 40 },
            xAxis: { type: 'category', data: labels, axisLabel: { rotate: labels.length > 12 ? 30 : 0 } },
            yAxis: { type: 'value', minInterval: 1 },
            series: [{
                name: label,
                type: 'bar',
                data: values,
                itemStyle: { color: '#1565C0', borderRadius: [4,4,0,0] },
                label: { show: values.length <= 18, position: 'top', fontSize: 11,
                         formatter: p => p.value > 0 ? p.value.toLocaleString('id-ID') : '' },
            }],
        });

        window.addEventListener('resize', () => trenChart && trenChart.resize());
    }

    function renderMedia(rows) {
        const tbody = document.getElementById('tbodyMedia');
        if (!rows || !rows.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-3">Tidak ada data</td></tr>';
            return;
        }
        // Hitung total untuk persentase bar
        const maxJudul = Math.max(...rows.map(r => r.total_judul || 0), 1);
        tbody.innerHTML = rows.map(function(r) {
            const pct = Math.round((r.total_judul / maxJudul) * 100);
            return `<tr>
                <td>
                    <div class="fw-semibold mb-1">${escHtml(r.media)}</div>
                    <div class="progress" style="height:4px">
                        <div class="progress-bar bg-primary" style="width:${pct}%"></div>
                    </div>
                </td>
                <td class="text-center fw-semibold">${fmt(r.total_judul)}</td>
                <td class="text-center text-success">${fmt(r.total_accept)}</td>
                <td class="text-center text-danger">${fmt(r.total_reject)}</td>
                <td class="text-center text-warning">${fmt(r.total_hibah)}</td>
                <td class="text-center text-secondary">${fmt(r.total_retur)}</td>
            </tr>`;
        }).join('');
    }

    function renderCabang(rows) {
        const tbody = document.getElementById('tbodyCabang');
        if (!rows || !rows.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-3">Tidak ada data</td></tr>';
            return;
        }
        tbody.innerHTML = rows.map(function(r) {
            const isPerpusnas = r.tujuan && r.tujuan.toLowerCase().includes('nasional');
            const badge = isPerpusnas
                ? '<span class="badge bg-primary bg-opacity-10 text-primary small">Perpusnas</span>'
                : '<span class="badge bg-success bg-opacity-10 text-success small">Provinsi</span>';
            return `<tr>
                <td>
                    <div class="fw-semibold">${escHtml(r.cabang)}</div>
                    <small class="text-muted">${escHtml(r.provinsi)}</small>
                </td>
                <td>${badge}</td>
                <td class="text-center">${fmt(r.total_surat)}</td>
                <td class="text-center">${fmt(r.total_judul)}</td>
                <td class="text-center text-success fw-semibold">${fmt(r.total_accept)}</td>
                <td class="text-center text-danger">${fmt(r.total_reject)}</td>
            </tr>`;
        }).join('');
    }

    function renderPetugas(rows) {
        const tbody = document.getElementById('tbodyPetugas');
        if (!rows || !rows.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">Tidak ada data</td></tr>';
            return;
        }
        tbody.innerHTML = rows.map(function(r) {
            return `<tr>
                <td>
                    <div class="fw-semibold">${escHtml(r.petugas)}</div>
                    <small class="text-muted">${escHtml(r.username)}</small>
                </td>
                <td class="text-center">${fmt(r.total_judul)}</td>
                <td class="text-center text-success fw-semibold">${fmt(r.total_accept)}</td>
                <td class="text-center text-danger">${fmt(r.total_reject)}</td>
                <td class="text-center text-warning">${fmt(r.total_hibah)}</td>
            </tr>`;
        }).join('');
    }

    function escHtml(str) {
        return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
})();
</script>
