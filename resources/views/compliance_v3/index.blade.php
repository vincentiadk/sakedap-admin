@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
        <i class="bi bi-check-circle me-1"></i> {!! session('success') !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Header --}}
    @php
        $isPerpusnas      = $isPerpusnas ?? true;
        $kckrMode         = $kckrMode ?? 'perpusnas';
        $userProvinceName = $userProvinceName ?? null;
        $currentParams    = request()->except('kckr_mode');
        $urlPerpusnas     = request()->fullUrlWithQuery(array_merge($currentParams, ['kckr_mode' => 'perpusnas']));
        $urlProvinsi      = request()->fullUrlWithQuery(array_merge($currentParams, ['kckr_mode' => 'provinsi']));
    @endphp
    <div class="d-flex align-items-start justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <h4 class="mb-0 fw-bold">
                Monitoring Kepatuhan Penerbit ISBN Gabungan
                @if(!$isPerpusnas && $userProvinceName)
                    <span class="badge bg-success ms-2" style="font-size:.7rem">{{ $userProvinceName }}</span>
                @endif
            </h4>
            <small class="text-muted">
                Sebelum-2026: berbasis Createdate &mdash;
                2026+: berbasis Tanggal Terbit &mdash;
                Kolom Terbit hanya terisi untuk judul 2026+
            </small>
        </div>
        @if($isPerpusnas)
        <div class="btn-group btn-group-sm" role="group">
            <a href="{{ $urlPerpusnas }}"
               class="btn {{ $kckrMode === 'perpusnas' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i class="fas fa-landmark"></i> Data Perpusnas
            </a>
            <a href="{{ $urlProvinsi }}"
               class="btn {{ $kckrMode === 'provinsi' ? 'btn-success' : 'btn-outline-success' }}">
                <i class="fas fa-map-marker-alt"></i> Data Provinsi
            </a>
        </div>
        @endif
    </div>

    @if($isPerpusnas)
    <div class="alert {{ $kckrMode === 'provinsi' ? 'alert-success' : 'alert-primary' }} py-2 px-3 mb-3" style="font-size:.85rem">
        @if($kckrMode === 'provinsi')
            <i class="fas fa-map-marker-alt me-1"></i>
            <strong>Mode: Data KCKR Provinsi</strong> — kolom KCKR dihitung dari <code>received_date_prov</code> (tanggal penerimaan di perpustakaan daerah).
        @else
            <i class="fas fa-landmark me-1"></i>
            <strong>Mode: Data KCKR Perpusnas</strong> — kolom KCKR dihitung dari <code>received_date_kckr</code> (tanggal penerimaan di Perpustakaan Nasional).
        @endif
    </div>
    @else
    <div class="alert alert-success py-2 px-3 mb-3" style="font-size:.85rem">
        <i class="fas fa-map-marker-alt me-1"></i>
        <strong>Data KCKR Provinsi {{ $userProvinceName }}</strong> — menampilkan kepatuhan berdasarkan penerimaan KCKR di perpustakaan daerah Anda.
    </div>
    @endif

    @if(isset($error))
        <div class="alert alert-danger">{{ $error }}</div>
    @endif

    {{-- Filter Panel --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body py-2">
            <div class="row g-2 align-items-end">

                <div class="col-auto">
                    <label class="form-label form-label-sm mb-1">Tipe Filter</label>
                    <select class="form-select form-select-sm" id="filterType" onchange="onFilterTypeChange()">
                        <option value="tahun">Per Tahun</option>
                        <option value="bulan">Per Bulan</option>
                        <option value="range">Rentang Tanggal</option>
                    </select>
                </div>

                <div id="filterTahunWrap" class="col-auto">
                    <label class="form-label form-label-sm mb-1">Tahun</label>
                    <select class="form-select form-select-sm" id="filterYear">
                        @for($y = 2030; $y >= 2015; $y--)
                            <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div id="filterBulanWrap" class="col-auto d-none">
                    <label class="form-label form-label-sm mb-1">Bulan</label>
                    <select class="form-select form-select-sm" id="filterMonth">
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln)
                            <option value="{{ $i+1 }}">{{ $bln }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="filterRangeWrap" class="col-auto d-none">
                    <label class="form-label form-label-sm mb-1">Dari</label>
                    <input type="date" class="form-control form-control-sm" id="startDate">
                </div>
                <div id="filterRangeWrap2" class="col-auto d-none">
                    <label class="form-label form-label-sm mb-1">Sampai</label>
                    <input type="date" class="form-control form-control-sm" id="endDate">
                </div>

                @if($isPerpusnas)
                <div class="col-md-2">
                    <label class="form-label form-label-sm mb-1">Provinsi</label>
                    <select class="form-select form-select-sm" id="provinceFilter" multiple size="1" style="height:31px">
                        @foreach($provinces as $prov)
                            <option value="{{ $prov->ID }}">{{ $prov->NAMAPROPINSI }}</option>
                        @endforeach
                    </select>
                </div>
                @else
                {{-- Sembunyikan filter, tetap ada di DOM agar JS tidak error --}}
                <select id="provinceFilter" class="d-none" multiple>
                    @foreach($provinceIds ?? [] as $pid)
                        <option value="{{ $pid }}" selected>{{ $pid }}</option>
                    @endforeach
                </select>
                @endif

                <div class="col-auto">
                    <label class="form-label form-label-sm mb-1">Kategori</label>
                    <select class="form-select form-select-sm" id="filterKategori">
                        <option value="">Semua</option>
                        <option value="1">Pemerintah</option>
                        <option value="2">Swasta</option>
                    </select>
                </div>

                <div class="col-auto">
                    <label class="form-label form-label-sm mb-1">Hutang Terbit</label>
                    <select class="form-select form-select-sm" id="filterHutang">
                        <option value="">Semua</option>
                        <option value="ya">Ada Hutang</option>
                        <option value="tidak">Tidak Ada</option>
                    </select>
                </div>

                <div class="col-auto">
                    <label class="form-label form-label-sm mb-1">Lewat Teguran</label>
                    <select class="form-select form-select-sm" id="filterTeguran">
                        <option value="">Semua</option>
                        <option value="ya">Lewat Teguran</option>
                        <option value="tidak">Tidak</option>
                    </select>
                </div>

                <div class="col-auto">
                    <label class="form-label form-label-sm mb-1">Status KCKR</label>
                    <select class="form-select form-select-sm" id="filterKckr">
                        <option value="">Semua</option>
                        <option value="sudah">Ada KCKR</option>
                        <option value="belum">Belum KCKR</option>
                    </select>
                </div>

                <div class="col-auto">
                    <label class="form-label form-label-sm mb-1">Rekomendasi</label>
                    <select class="form-select form-select-sm" id="filterRekomendasi">
                        <option value="">Semua</option>
                        <option value="blokir_terbit">Blokir Konfirmasi Terbit</option>
                        <option value="blokir_kckr">Blokir SS KCKR</option>
                        <option value="baik">Baik</option>
                    </select>
                </div>

                <div class="col-auto">
                    <label class="form-label form-label-sm mb-1">% KCKR</label>
                    <select class="form-select form-select-sm" id="filterPersentase">
                        <option value="">Semua</option>
                        <option value="0-20">0–20% (Sangat Tidak Patuh)</option>
                        <option value="21-40">21–40% (Tidak Patuh)</option>
                        <option value="41-60">41–60% (Cukup Patuh)</option>
                        <option value="61-80">61–80% (Patuh)</option>
                        <option value="81-100">81–100% (Sangat Patuh)</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label form-label-sm mb-1">Cari Penerbit</label>
                    <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Nama penerbit...">
                </div>

                <div class="col-auto">
                    <button class="btn btn-primary btn-sm mt-3" onclick="loadData(1)">Tampilkan</button>
                    <button class="btn btn-outline-secondary btn-sm mt-3" onclick="resetFilter()">Reset</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="mb-3" id="summaryCards">
        <div class="row g-2 mb-1 align-items-center">
            <div class="col-auto">
                <div class="card border-0 shadow-sm text-center py-2 px-3 h-100">
                    <div class="fs-4 fw-bold text-primary" id="sumPenerbit">-</div>
                    <small class="text-muted">Penerbit</small>
                </div>
            </div>
            <div class="col">
                <div class="d-flex flex-column gap-1">
                    {{-- Status Terbit (hanya 2026+) --}}
                    <div>
                        <div class="text-muted fw-semibold mb-1" style="font-size:.72rem;letter-spacing:.05em;text-transform:uppercase">
                            📄 Status Terbit <span class="badge bg-primary" style="font-size:.6rem">2026+</span>
                        </div>
                        <div class="row g-2">
                            <div class="col">
                                <div class="card border-0 shadow-sm text-center py-2 border-top border-2 border-secondary">
                                    <div class="fs-5 fw-bold text-secondary" id="sumJudul2026">-</div>
                                    <small class="text-muted" style="font-size:.72rem">Total Judul 2026+</small>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card border-0 shadow-sm text-center py-2 border-top border-2 border-success">
                                    <div class="fs-5 fw-bold text-success" id="sumTerbit">-</div>
                                    <small class="text-muted" style="font-size:.72rem">Sudah Terbit</small>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card border-0 shadow-sm text-center py-2 border-top border-2" style="border-color:#adb5bd!important">
                                    <div class="fs-5 fw-bold" style="color:#6c757d" id="sumBelumTerbit">-</div>
                                    <small class="text-muted" style="font-size:.72rem">Belum Terbit</small>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card border-0 shadow-sm text-center py-2 border-top border-2 border-warning">
                                    <div class="fs-5 fw-bold text-warning" id="sumHutang">-</div>
                                    <small class="text-muted" style="font-size:.72rem">Hutang Terbit</small>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card border-0 shadow-sm text-center py-2 border-top border-2 border-danger">
                                    <div class="fs-5 fw-bold text-danger" id="sumTeguran">-</div>
                                    <small class="text-muted" style="font-size:.72rem">Lewat Teguran</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Status KCKR (semua tahun) --}}
                    <div>
                        <div class="text-muted fw-semibold mb-1" style="font-size:.72rem;letter-spacing:.05em;text-transform:uppercase">
                            ✅ Status KCKR <span class="badge bg-secondary" style="font-size:.6rem">Semua Tahun</span>
                        </div>
                        <div class="row g-2">
                            <div class="col">
                                <div class="card border-0 shadow-sm text-center py-2 border-top border-2 border-info">
                                    <div class="fs-5 fw-bold text-info" id="sumKckr">-</div>
                                    <small class="text-muted" style="font-size:.72rem">Sudah KCKR</small>
                                    <div class="d-flex justify-content-center gap-2 mt-1">
                                        <small class="text-muted" style="font-size:.68rem">Cetak: <strong id="sumKckrCetak">-</strong></small>
                                        <small class="text-muted" style="font-size:.68rem">Rekam: <strong id="sumKckrRekam">-</strong></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="card border-0 shadow-sm text-center py-2 border-top border-2" style="border-color:#fd7e14!important">
                                    <div class="fs-5 fw-bold" style="color:#fd7e14" id="sumTagihanKckr">-</div>
                                    <small class="text-muted" style="font-size:.72rem">Tagihan KCKR</small>
                                    <div class="d-flex justify-content-center gap-2 mt-1">
                                        <small class="text-muted" style="font-size:.68rem">Cetak: <strong id="sumTagihanCetak">-</strong></small>
                                        <small class="text-muted" style="font-size:.68rem">Rekam: <strong id="sumTagihanRekam">-</strong></small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto" style="min-width:160px">
                                <div class="card border-0 shadow-sm text-center py-2 border-top border-2 border-primary h-100 px-2">
                                    <div class="fs-5 fw-bold text-primary" id="sumPctKckr">-</div>
                                    <small class="text-muted" style="font-size:.72rem">Rata-rata % KCKR</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Monitoring Penerbit</h5>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white text-primary" id="totalBadge"></span>
                <a id="btnBackDashboard" href="{{ route('dashboard_compliance') }}" class="btn btn-sm btn-outline-success btn-light">
                    <i class="fas fa-chart-pie"></i> Dashboard
                </a>
                <button class="btn btn-sm btn-light btn-outline-dark" onclick="doExportDatatable(0)" title="Export ringkasan penerbit saja">
                    <i class="bi bi-file-earmark-excel"></i> Export Penerbit
                </button>
                <button class="btn btn-sm btn-success" onclick="doExportDatatable(1)" title="Export ringkasan penerbit + daftar judul lengkap">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Export + Judul
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0 align-middle" style="font-size:.82rem">
                    <thead id="tableHead" class="table-light">
                        <tr>
                            <th rowspan="2" class="text-center align-middle">#</th>
                            <th rowspan="2" class="sortable align-middle" data-col="NAME">Nama Penerbit</th>
                            <th rowspan="2" class="align-middle">Kategori</th>
                            <th rowspan="2" class="align-middle">Kota</th>
                            <th rowspan="2" class="sortable text-center align-middle" data-col="TOTAL_JUDUL">Total Judul</th>
                            <th colspan="2" class="text-center border-start">
                                Status Terbit
                                <small class="badge bg-primary ms-1" style="font-size:.55rem;font-weight:normal">2026+</small>
                            </th>
                            <th colspan="2" class="text-center border-start">
                                Keterlambatan Terbit
                                <small class="badge bg-primary ms-1" style="font-size:.55rem;font-weight:normal">2026+</small>
                            </th>
                            <th colspan="3" class="text-center border-start bg-success bg-opacity-25">Sudah KCKR</th>
                            <th colspan="4" class="text-center border-start bg-warning bg-opacity-25">Belum KCKR</th>
                            <th rowspan="2" class="text-center align-middle border-start sortable" data-col="PERSENTASE_KCKR">% KCKR</th>
                            <th rowspan="2" class="text-center align-middle">Rekomendasi</th>
                            <th rowspan="2" class="text-center align-middle">Aksi</th>
                        </tr>
                        <tr>
                            <th class="text-center border-start bg-success bg-opacity-25" style="font-weight:normal;font-size:.8rem">Terbit</th>
                            <th class="text-center bg-secondary bg-opacity-25" style="font-weight:normal;font-size:.8rem">Belum</th>
                            <th class="text-center border-start bg-warning bg-opacity-25 sortable" data-col="HUTANG_TERBIT" style="font-weight:normal;font-size:.8rem">Hutang</th>
                            <th class="text-center bg-danger bg-opacity-25 sortable" data-col="LEWAT_TEGURAN" style="font-weight:normal;font-size:.8rem">Lewat Teguran</th>
                            <th class="text-center border-start bg-success bg-opacity-25 sortable" data-col="SUDAH_KCKR" style="font-weight:normal;font-size:.8rem">Total</th>
                            <th class="text-center bg-success bg-opacity-25" style="font-weight:normal;font-size:.8rem">Cetak</th>
                            <th class="text-center bg-success bg-opacity-25" style="font-weight:normal;font-size:.8rem">Rekam</th>
                            <th class="text-center border-start bg-warning bg-opacity-25" style="font-weight:normal;font-size:.8rem">Total</th>
                            <th class="text-center bg-warning bg-opacity-25" style="font-weight:normal;font-size:.8rem">Cetak</th>
                            <th class="text-center bg-warning bg-opacity-25" style="font-weight:normal;font-size:.8rem">Rekam</th>
                            <th class="text-center bg-danger bg-opacity-25 sortable" data-col="TERLAMBAT_KCKR" style="font-weight:normal;font-size:.8rem">Terlambat</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr><td colspan="20" class="text-center py-4 text-muted">Pilih filter lalu klik Tampilkan</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between align-items-center py-1">
            <small class="text-muted" id="pageInfo"></small>
            <div id="pagination"></div>
        </div>
    </div>

</div>

{{-- Loading Modal --}}
<div class="modal fade" id="exportModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Menyiapkan File Excel</h6>
            </div>
            <div class="modal-body">
                <div class="progress mb-2" style="height:8px">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                         id="exportProgress" style="width:0%"></div>
                </div>
                <small class="text-muted" id="exportStatus">Memproses data...</small>
            </div>
        </div>
    </div>
</div>

<style>
.sortable { cursor: pointer; user-select: none; }
.sortable:hover { background: rgba(0,0,0,.05); }
th.sorted-asc::after  { content: ' ▲'; font-size:.65rem; }
th.sorted-desc::after { content: ' ▼'; font-size:.65rem; }
</style>

<script>
let currentPage = 1, currentSort = 'NAME', currentDir = 'ASC';

function onFilterTypeChange() {
    const t = document.getElementById('filterType').value;
    document.getElementById('filterTahunWrap').classList.toggle('d-none', t !== 'tahun');
    document.getElementById('filterBulanWrap').classList.toggle('d-none', t !== 'bulan');
    ['filterRangeWrap','filterRangeWrap2'].forEach(id =>
        document.getElementById(id).classList.toggle('d-none', t !== 'range'));
}

function buildParams(page = 1) {
    const type = document.getElementById('filterType').value;
    const params = new URLSearchParams({
        filter_type: type,
        page, sort_col: currentSort, sort_dir: currentDir,
    });
    if (type === 'tahun') params.set('filter_year', document.getElementById('filterYear').value);
    if (type === 'bulan') {
        params.set('filter_year',  document.getElementById('filterYear').value);
        params.set('filter_month', document.getElementById('filterMonth').value);
    }
    if (type === 'range') {
        params.set('start_date', document.getElementById('startDate').value);
        params.set('end_date',   document.getElementById('endDate').value);
    }
    const prov = [...document.getElementById('provinceFilter').selectedOptions].map(o => o.value);
    prov.forEach(v => params.append('province_ids[]', v));
    const v = (id) => document.getElementById(id).value;
    if (v('filterKategori'))    params.set('kategori',            v('filterKategori'));
    if (v('filterHutang'))      params.set('filter_hutang',       v('filterHutang'));
    if (v('filterTeguran'))     params.set('filter_teguran',      v('filterTeguran'));
    if (v('filterKckr'))        params.set('filter_kckr',         v('filterKckr'));
    if (v('filterRekomendasi')) params.set('filter_rekomendasi',  v('filterRekomendasi'));
    if (v('filterPersentase'))  params.set('persentase',          v('filterPersentase'));
    if (v('searchInput'))       params.set('search',              v('searchInput'));
    // kckr_mode: ambil dari URL saat ini agar toggle tetap aktif saat reload data
    const kckrMode = new URLSearchParams(window.location.search).get('kckr_mode');
    if (kckrMode) params.set('kckr_mode', kckrMode);
    return params;
}

function loadData(page = 1) {
    currentPage = page;
    const tbody = document.getElementById('tableBody');
    tbody.innerHTML = '<tr><td colspan="20" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div> Memuat...</td></tr>';

    fetch('{{ route("compliance_v3.data") }}?' + buildParams(page))
        .then(r => r.json())
        .then(res => {
            if (res.error) { tbody.innerHTML = `<tr><td colspan="20" class="text-center text-danger">${res.error}</td></tr>`; return; }
            renderTable(res);
            renderPagination(res);
            updateSummary(res);
        })
        .catch(() => tbody.innerHTML = '<tr><td colspan="20" class="text-center text-danger">Gagal memuat data.</td></tr>');
}

function dash(v) { return (v === undefined || v === null || v === '') ? '-' : v; }

const THRESHOLD_PCT = {{ $minPct ?? 20 }};

function renderTable(res) {
    const tbody = document.getElementById('tableBody');
    if (!res.data.length) {
        tbody.innerHTML = '<tr><td colspan="20" class="text-center py-4 text-muted">Tidak ada data.</td></tr>';
        return;
    }
    let no = (res.current_page - 1) * res.per_page + 1;
    tbody.innerHTML = res.data.map(r => {
        const has2026      = parseInt(r.JUDUL_2026_PLUS || 0) > 0;
        const terbitCell   = has2026 ? `<span class="text-success fw-semibold">${r.JUDUL_TERBIT}</span>` : `<span class="text-muted">-</span>`;
        const belumTerbit  = has2026 ? `<span class="text-muted">${r.JUDUL_BELUM_TERBIT}</span>` : `<span class="text-muted">-</span>`;
        const hutangBadge  = has2026
            ? (r.HUTANG_TERBIT  > 0 ? `<span class="badge bg-warning text-dark">${r.HUTANG_TERBIT}</span>` : `<span class="text-muted">0</span>`)
            : `<span class="text-muted">-</span>`;
        const teguranBadge = has2026
            ? (r.LEWAT_TEGURAN  > 0 ? `<span class="badge bg-danger">${r.LEWAT_TEGURAN}</span>` : `<span class="text-muted">0</span>`)
            : `<span class="text-muted">-</span>`;

        const pct          = parseFloat(r.PERSENTASE_KCKR || 0);
        const pctColor     = pct >= THRESHOLD_PCT ? 'success' : pct >= 50 ? 'warning' : 'danger';
        const pctBadge     = `<span class="badge bg-${pctColor}">${pct}%</span>`;

        const teguran       = parseInt(r.LEWAT_TEGURAN || 0);
        const terlambatKckr = parseInt(r.TERLAMBAT_KCKR || 0);
        const rekBadge      = (terlambatKckr > 0 && pct <= THRESHOLD_PCT)
            ? `<span class="badge" style="background:#fd7e14">Blokir SS KCKR</span>`
            : teguran > 0
            ? `<span class="badge bg-danger">Blokir Konfirmasi Terbit</span>`
            : `<span class="badge bg-success">Baik</span>`;

        const detailUrl = `{{ url('/coaching-supervision/compliance-v3/detail') }}/${r.ID}?` + buildParams().toString();
        return `<tr>
            <td class="text-muted text-center">${no++}</td>
            <td><a href="${detailUrl}" class="text-decoration-none fw-semibold">${r.NAME}</a></td>
            <td><span class="badge bg-secondary">${r.KATEGORI}</span></td>
            <td>${r.CITY || '-'}</td>
            <td class="text-center">${r.TOTAL_JUDUL}</td>
            <td class="text-center">${terbitCell}</td>
            <td class="text-center">${belumTerbit}</td>
            <td class="text-center">${hutangBadge}</td>
            <td class="text-center">${teguranBadge}</td>
            <td class="text-center text-info fw-semibold">${r.SUDAH_KCKR}</td>
            <td class="text-center text-muted">${r.SUDAH_KCKR_CETAK}</td>
            <td class="text-center text-muted">${r.SUDAH_KCKR_REKAM}</td>
            <td class="text-center">${r.BELUM_KCKR}</td>
            <td class="text-center text-muted">${r.BELUM_KCKR_CETAK}</td>
            <td class="text-center text-muted">${r.BELUM_KCKR_REKAM}</td>
            <td class="text-center">${terlambatKckr > 0 ? `<span class="badge bg-danger">${terlambatKckr}</span>` : `<span class="text-muted">0</span>`}</td>
            <td class="text-center">${pctBadge}</td>
            <td class="text-center">${rekBadge}</td>
            <td class="text-center"><a href="${detailUrl}" class="btn btn-xs btn-outline-primary" style="font-size:.7rem;padding:1px 6px">Detail</a></td>
        </tr>`;
    }).join('');
}

function updateSummary(res) {
    const a = res.agg || {};
    const fmt = v => (parseInt(v) || 0).toLocaleString('id');
    document.getElementById('sumPenerbit').textContent    = res.total;
    document.getElementById('totalBadge').textContent     = res.total + ' penerbit ditemukan';
    // Total judul 2026+ = sudah terbit + belum terbit (exclude semua tahun lama)
    document.getElementById('sumJudul2026').textContent   = fmt((parseInt(a.SUM_TERBIT)||0) + (parseInt(a.SUM_BELUM_TERBIT)||0));
    document.getElementById('sumTerbit').textContent      = fmt(a.SUM_TERBIT);
    document.getElementById('sumBelumTerbit').textContent = fmt(a.SUM_BELUM_TERBIT);
    document.getElementById('sumHutang').textContent      = fmt(a.SUM_HUTANG);
    document.getElementById('sumTeguran').textContent     = fmt(a.SUM_TEGURAN);
    document.getElementById('sumKckr').textContent        = fmt(a.SUM_SUDAH_KCKR);
    document.getElementById('sumKckrCetak').textContent   = fmt(a.SUM_SUDAH_KCKR_CETAK);
    document.getElementById('sumKckrRekam').textContent   = fmt(a.SUM_SUDAH_KCKR_REKAM);
    document.getElementById('sumTagihanKckr').textContent = fmt(a.SUM_BELUM_KCKR);
    document.getElementById('sumTagihanCetak').textContent = fmt(a.SUM_BELUM_KCKR_CETAK);
    document.getElementById('sumTagihanRekam').textContent = fmt(a.SUM_BELUM_KCKR_REKAM);
    document.getElementById('sumPctKckr').textContent     = (parseFloat(a.AVG_PCT) || 0).toFixed(1) + '%';
    document.getElementById('pageInfo').textContent       =
        `Menampilkan ${res.data.length} dari ${res.total} penerbit (hal. ${res.current_page}/${res.last_page})`;
}

function renderPagination(res) {
    const el = document.getElementById('pagination');
    if (res.last_page <= 1) { el.innerHTML = ''; return; }
    let html = '<nav><ul class="pagination pagination-sm mb-0">';
    html += `<li class="page-item ${res.current_page===1?'disabled':''}"><a class="page-link" href="#" onclick="loadData(${res.current_page-1});return false">&laquo;</a></li>`;
    const start = Math.max(1, res.current_page-2), end = Math.min(res.last_page, res.current_page+2);
    if (start > 1) html += `<li class="page-item"><a class="page-link" href="#" onclick="loadData(1);return false">1</a></li>${start>2?'<li class="page-item disabled"><span class="page-link">…</span></li>':''}`;
    for (let p=start; p<=end; p++) html += `<li class="page-item ${p===res.current_page?'active':''}"><a class="page-link" href="#" onclick="loadData(${p});return false">${p}</a></li>`;
    if (end < res.last_page) html += `${end<res.last_page-1?'<li class="page-item disabled"><span class="page-link">…</span></li>':''}<li class="page-item"><a class="page-link" href="#" onclick="loadData(${res.last_page});return false">${res.last_page}</a></li>`;
    html += `<li class="page-item ${res.current_page===res.last_page?'disabled':''}"><a class="page-link" href="#" onclick="loadData(${res.current_page+1});return false">&raquo;</a></li>`;
    html += '</ul></nav>';
    el.innerHTML = html;
}

document.querySelectorAll('th.sortable').forEach(th => {
    th.addEventListener('click', () => {
        const col = th.dataset.col;
        if (!col) return;
        if (currentSort === col) currentDir = currentDir === 'ASC' ? 'DESC' : 'ASC';
        else { currentSort = col; currentDir = 'ASC'; }
        document.querySelectorAll('th').forEach(t => t.classList.remove('sorted-asc','sorted-desc'));
        th.classList.add(currentDir === 'ASC' ? 'sorted-asc' : 'sorted-desc');
        loadData(1);
    });
});

function doExport(withDetail = 0) {
    const params = buildParams(1);
    params.delete('page'); params.delete('sort_col'); params.delete('sort_dir');
    params.set('with_judul', withDetail);

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("compliance_v3.queue_export") }}';

    const csrf = document.createElement('input');
    csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = csrfToken;
    form.appendChild(csrf);

    for (const [key, val] of params.entries()) {
        const inp = document.createElement('input');
        inp.type = 'hidden'; inp.name = key; inp.value = val;
        form.appendChild(inp);
    }

    document.body.appendChild(form);
    form.submit();
}

function doExportDatatable(withDetail = 0) {
    const params = buildParams(1);
    params.delete('page'); params.delete('sort_col'); params.delete('sort_dir');

    const modalEl   = document.getElementById('modal-datatable-download');
    const modalProg = new bootstrap.Modal(modalEl);
    const bar       = document.getElementById('modal-datatable-download-progress');
    const countEl   = document.getElementById('modal-datatable-download-progress-count');
    const statusEl  = document.getElementById('modal-datatable-download-status');
    const spinner   = modalEl.querySelector('.spinner-border');

    function setUI(label) {
        statusEl.textContent = label || 'Proses Download...';
        spinner.classList.add('text-success'); spinner.classList.remove('text-danger'); spinner.style.display = '';
        document.getElementById('btn-resume-download')?.classList.add('d-none');
        document.getElementById('btn-retry-download')?.classList.add('d-none');
        document.getElementById('btn-cancel-download')?.classList.remove('d-none');
        bar.style.width = '0%'; bar.textContent = '0%';
        bar.classList.add('bg-success'); bar.classList.remove('bg-info', 'bg-danger');
    }

    // ── Semua export pakai background job, poll progress di modal ──────────────
    params.set('with_judul', withDetail);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    countEl.textContent = 'Memproses di server...';
    setUI('Mengirim permintaan...');
    document.getElementById('btn-cancel-download')?.classList.add('d-none');
    modalProg.show();

    fetch('{{ route("compliance_v3.queue_export") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
        body: params.toString(),
    })
    .then(r => r.json())
    .then(({ jobID }) => {
        const statusUrl   = '{{ url("coaching-supervision/compliance-v3/export-status") }}/' + jobID;
        const downloadUrl = '{{ url("coaching-supervision/compliance-v3/export-download") }}/' + jobID;

        statusEl.textContent = 'Diproses di server...';

        const poll = setInterval(() => {
            fetch(statusUrl).then(r => r.json()).then(({ status: s, pct: p, label: l }) => {
                if (p > 0) { bar.style.width = p + '%'; bar.textContent = p + '%'; }
                statusEl.textContent = s === 'queued' ? 'Menunggu worker...' : (l || 'Diproses di server...');
                countEl.textContent = p > 0 ? (p + '%') : '';

                if (s === 'completed') {
                    clearInterval(poll);
                    bar.style.width = '100%'; bar.textContent = '100%';
                    statusEl.textContent = 'Selesai! File sedang diunduh.';
                    window.location.href = downloadUrl;
                    setTimeout(() => modalProg.hide(), 2000);
                } else if (s === 'failed') {
                    clearInterval(poll);
                    bar.classList.replace('bg-success', 'bg-danger');
                    statusEl.textContent = 'Gagal. Silakan coba lagi.';
                }
            });
        }, 2000);

        setTimeout(() => clearInterval(poll), 600000);
    })
    .catch(() => {
        statusEl.textContent = 'Gagal menghubungi server.';
        spinner.style.display = 'none';
    });
}

function _doExportLegacy(withDetail = 0) {
    // kept for reference only — not called
    const params = buildParams(1);
    params.delete('page'); params.delete('sort_col'); params.delete('sort_dir');
    params.set('with_judul', withDetail);

    const modal  = new bootstrap.Modal(document.getElementById('exportModal'));
    const bar    = document.getElementById('exportProgress');
    const status = document.getElementById('exportStatus');
    let pct = 5;
    bar.style.width = pct + '%';
    status.textContent = 'Mengirim permintaan...';
    modal.show();

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    fetch('{{ route("compliance_v3.queue_export") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': csrfToken },
        body: params.toString(),
    })
    .then(r => r.json())
    .then(({ jobID }) => {
        const statusUrl   = '{{ route("compliance_v3.export_status", ["jobID" => "__JOB__"]) }}'.replace('__JOB__', jobID);
        const downloadUrl = '{{ route("compliance_v3.export_download", ["jobID" => "__JOB__"]) }}'.replace('__JOB__', jobID);

        const pollInterval = setInterval(() => {
            fetch(statusUrl).then(r => r.json()).then(({ status: s, pct: p, label: l }) => {
                if (p > 0) {
                    bar.style.width = p + '%';
                    bar.textContent = p + '%';
                }
                if (s === 'queued') {
                    status.textContent = 'Menunggu antrian worker...';
                } else if (s === 'processing' && p === 0) {
                    status.textContent = 'Memulai proses export...';
                } else {
                    status.textContent = l || 'Sedang diproses...';
                }

                if (s === 'completed') {
                    clearInterval(pollInterval);
                    bar.style.width = '100%';
                    bar.textContent = '100%';
                    status.textContent = 'Selesai! File sedang diunduh.';
                    window.location.href = downloadUrl;
                    setTimeout(() => modal.hide(), 2000);
                } else if (s === 'failed') {
                    clearInterval(pollInterval);
                    bar.classList.add('bg-danger');
                    bar.classList.remove('bg-primary');
                    status.textContent = 'Gagal membuat file. Silakan coba lagi.';
                    setTimeout(() => modal.hide(), 3000);
                }
            });
        }, 2000);

        setTimeout(() => { clearInterval(pollInterval); modal.hide(); }, 600000);
    })
    .catch(() => {
        bar.classList.replace('bg-primary', 'bg-danger');
        status.textContent = 'Gagal menghubungi server. Silakan coba lagi.';
        setTimeout(() => modal.hide(), 3000);
    });
}

function resetFilter() {
    document.getElementById('filterType').value        = 'tahun';
    document.getElementById('filterYear').value        = '{{ date("Y") }}';
    document.getElementById('filterKategori').value    = '';
    document.getElementById('filterHutang').value      = '';
    document.getElementById('filterTeguran').value     = '';
    document.getElementById('filterKckr').value        = '';
    document.getElementById('filterRekomendasi').value = '';
    document.getElementById('filterPersentase').value  = '';
    document.getElementById('searchInput').value       = '';
    [...document.getElementById('provinceFilter').options].forEach(o => o.selected = false);
    onFilterTypeChange();
}

document.getElementById('searchInput').addEventListener('keydown', e => { if(e.key==='Enter') loadData(1); });

function updateDashboardLink() {
    const p = buildParams(1);
    p.delete('page'); p.delete('sort_col'); p.delete('sort_dir');
    // hapus filter tambahan yang tidak dikenal dashboard
    ['kategori','filter_hutang','filter_teguran','filter_kckr','filter_rekomendasi','persentase','search'].forEach(k => p.delete(k));
    document.getElementById('btnBackDashboard').href = '{{ route("dashboard_compliance") }}?' + p.toString();
}

// patch loadData agar selalu update link dashboard
const _origLoadData = loadData;
loadData = function(page) { updateDashboardLink(); _origLoadData(page); };

document.addEventListener('DOMContentLoaded', function () {
    const p = new URLSearchParams(window.location.search);
    if (!p.has('filter_type')) return; // tidak ada params → biarkan default

    const ft = p.get('filter_type');
    document.getElementById('filterType').value = ft;
    onFilterTypeChange();

    if (ft === 'tahun' && p.get('filter_year'))
        document.getElementById('filterYear').value = p.get('filter_year');

    if (ft === 'bulan') {
        if (p.get('filter_year'))  document.getElementById('filterYear').value  = p.get('filter_year');
        if (p.get('filter_month')) document.getElementById('filterMonth').value = p.get('filter_month');
    }

    if (ft === 'range') {
        if (p.get('start_date')) document.getElementById('startDate').value = p.get('start_date');
        if (p.get('end_date'))   document.getElementById('endDate').value   = p.get('end_date');
    }

    // Provinsi — support province_ids[] (fetch) dan province_ids[0] (http_build_query)
    const ids = p.getAll('province_ids[]');
    if (!ids.length) {
        // format PHP http_build_query: province_ids[0]=x, province_ids[1]=y, ...
        for (const [key, val] of p.entries()) {
            if (/^province_ids\[\d+\]$/.test(key)) ids.push(val);
        }
    }
    if (ids.length) {
        [...document.getElementById('provinceFilter').options].forEach(o => {
            o.selected = ids.includes(o.value);
        });
    }

    updateDashboardLink();
    loadData(1);
});
</script>
@endsection
