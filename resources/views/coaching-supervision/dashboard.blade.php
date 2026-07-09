@extends('layouts.app')

@section('content')
<style>
    #dashboardContent .card { border: none; border-radius: 12px; }
    .stat-card { transition: transform .15s; }
    .stat-card:hover { transform: translateY(-3px); }
    .badge-patuh-0  { background-color: #dc3545; }
    .badge-patuh-1  { background-color: #fd7e14; }
    .badge-patuh-2  { background-color: #ffc107; color: #000; }
    .badge-patuh-3  { background-color: #0dcaf0; color: #000; }
    .badge-patuh-4  { background-color: #198754; }
    .progress-bar-striped { animation: progress-bar-stripes 1s linear infinite; }

    @media print {
        @page { size: A4 landscape; margin: 10mm; }
        nav, footer, .btn, form { display: none !important; }
        body { background: white !important; }
        #dashboardContent .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; }
        .container-fluid { padding: 0 !important; }
        canvas { max-width: 100% !important; }
    }
</style>

@php
    $bulanNames = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $ft         = $dateFilter['type'] ?? 'tahun';
    $filterYear = request('filter_year', date('Y'));
    $filterMonth= (int) request('filter_month', date('n'));
    if ($ft === 'tahun') {
        $periodeLabel = 'Tahun ' . $filterYear;
    } elseif ($ft === 'bulan') {
        $periodeLabel = $bulanNames[$filterMonth] . ' ' . $filterYear;
    } else {
        // end_date di controller sudah +1 hari (exclusive), tampilkan -1 hari untuk label
        $endDisplay   = isset($dateFilter['end'])
            ? \Carbon\Carbon::parse($dateFilter['end'])->subDay()->format('Y-m-d')
            : '-';
        $periodeLabel = ($dateFilter['start'] ?? '-') . ' s.d. ' . $endDisplay;
    }
    $selectedProvinces = $provinceIds ?? [];
    $provinceLabel = '';
    if (!empty($selectedProvinces) && !empty($provinces)) {
        $provNames = array_map(fn($p) => $p->NAME ?? $p->NAMA ?? '', array_filter((array)$provinces, fn($p) => in_array($p->ID ?? $p->id ?? 0, $selectedProvinces)));
        $provinceLabel = implode(', ', $provNames);
    }
@endphp

<div class="container-fluid mt-4 px-4" id="dashboardContent" style="max-width:1650px">

{{-- Judul PDF (hanya tampil saat print) --}}
<div id="pdfHeader" style="display:none" class="mb-3 border-bottom pb-2">
    <h4 class="fw-bold mb-1">Dashboard Kepatuhan Penerbit KCKR</h4>
    <div class="text-muted" style="font-size:.9rem">
        <strong>Periode:</strong> {{ $periodeLabel }}
        @if($provinceLabel)
            &nbsp;|&nbsp; <strong>Provinsi:</strong> {{ $provinceLabel }}
        @endif
        &nbsp;|&nbsp; <strong>Dicetak:</strong> {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB
    </div>
</div>
    <div class="d-flex justify-content-between align-items-center mb-4" >
        <div>
            <h2 class="mb-0">📊 Dashboard Kepatuhan Penerbit</h2>
            @if($isMixed ?? false)
                <span class="badge bg-warning text-dark ms-1">Mode Campuran — pra-2026 + 2026+</span>
            @elseif($hasV2 ?? false)
                <span class="badge bg-primary ms-1">Mode 2026+ — berbasis Tanggal Terbit</span>
            @endif
        </div>
        @php
            $currentKckrMode = $kckrMode ?? 'perpusnas';
            $toCompliance = array_filter([
                'filter_type'  => $dateFilter['type'] ?? 'tahun',
                'filter_year'  => request('filter_year'),
                'filter_month' => request('filter_month'),
                'start_date'   => request('start_date'),
                'end_date'     => request('end_date'),
                'kckr_mode'    => $currentKckrMode,
            ]);
            if (!empty($provinceIds)) {
                $toCompliance['province_ids'] = $provinceIds;
            }
            $detailRoute = route('compliance_v3.index');

            // URL toggle mode untuk Perpusnas
            $currentParams = array_filter(request()->except('kckr_mode'));
            $urlPerpusnas  = request()->fullUrlWithQuery(array_merge($currentParams, ['kckr_mode' => 'perpusnas']));
            $urlProvinsi   = request()->fullUrlWithQuery(array_merge($currentParams, ['kckr_mode' => 'provinsi']));
        @endphp
        <div class="d-flex gap-2 flex-wrap align-items-center">
            @if($isPerpusnas ?? true)
            <div class="btn-group btn-group-sm" role="group" title="Pilih data KCKR yang ditampilkan">
                <a href="{{ $urlPerpusnas }}"
                   class="btn {{ $currentKckrMode === 'perpusnas' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fas fa-landmark"></i> Data Perpusnas
                </a>
                <a href="{{ $urlProvinsi }}"
                   class="btn {{ $currentKckrMode === 'provinsi' ? 'btn-success' : 'btn-outline-success' }}">
                    <i class="fas fa-map-marker-alt"></i> Data Provinsi
                </a>
            </div>
            @endif
            <button onclick="downloadPDF()" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf"></i> Download PDF
            </button>
            <a href="{{ $detailRoute }}?{{ http_build_query($toCompliance) }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-table"></i> Lihat Detail
            </a>
        </div>
    </div>

    @if($isPerpusnas ?? true)
    <div class="alert {{ ($currentKckrMode === 'provinsi') ? 'alert-success' : 'alert-primary' }} py-2 px-3 mb-3" style="font-size:.85rem">
        @if($currentKckrMode === 'provinsi')
            <i class="fas fa-map-marker-alt me-1"></i>
            <strong>Mode: Data KCKR Provinsi</strong> — menampilkan kepatuhan berdasarkan tanggal penerimaan KCKR di perpustakaan daerah (<code>received_date_prov</code>).
        @else
            <i class="fas fa-landmark me-1"></i>
            <strong>Mode: Data KCKR Perpusnas</strong> — menampilkan kepatuhan berdasarkan tanggal penerimaan KCKR di Perpustakaan Nasional (<code>received_date_kckr</code>).
        @endif
    </div>
    @endif

    @if(isset($error))
        <div class="alert alert-danger">{{ $error }}</div>
    @endif

    {{-- Filter --}}
    <div class="card shadow-sm mb-3" id="dashFilterCard">
        <div class="card-body py-2">
            @php
                $ft          = $dateFilter['type'] ?? 'tahun';
                $filterYear  = request('filter_year', date('Y'));
                $filterMonth = request('filter_month', date('n'));
            @endphp
            <form method="GET" action="{{ route('dashboard_compliance') }}">
                <div class="row g-2 align-items-end">

                    <div class="col-auto">
                        <label class="form-label form-label-sm mb-1">Tipe Filter</label>
                        <select class="form-select form-select-sm" name="filter_type" id="dashFilterType" onchange="toggleDashFilter()">
                            <option value="tahun" {{ $ft == 'tahun' ? 'selected' : '' }}>Per Tahun</option>
                            <option value="bulan" {{ $ft == 'bulan' ? 'selected' : '' }}>Per Bulan</option>
                            <option value="range" {{ $ft == 'range' ? 'selected' : '' }}>Rentang Tanggal</option>
                        </select>
                    </div>

                    <div id="dash_wrap_tahun" class="col-auto {{ $ft != 'tahun' ? 'd-none' : '' }}">
                        <label class="form-label form-label-sm mb-1">Tahun</label>
                        <select name="filter_year" class="form-select form-select-sm" id="dash_year_tahun" {{ $ft != 'tahun' ? 'disabled' : '' }}>
                            @for($y = 2030; $y >= 2015; $y--)
                                <option value="{{ $y }}" {{ $filterYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <div id="dash_wrap_bulan" class="col-auto {{ $ft != 'bulan' ? 'd-none' : '' }}">
                        <label class="form-label form-label-sm mb-1">Bulan</label>
                        <select name="filter_month" class="form-select form-select-sm" {{ $ft != 'bulan' ? 'disabled' : '' }}>
                            @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $bln)
                                <option value="{{ $i+1 }}" {{ $filterMonth == ($i+1) ? 'selected' : '' }}>{{ $bln }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="dash_wrap_bulan_year" class="col-auto {{ $ft != 'bulan' ? 'd-none' : '' }}">
                        <label class="form-label form-label-sm mb-1">Tahun</label>
                        <select name="filter_year" class="form-select form-select-sm" id="dash_year_bulan" {{ $ft != 'bulan' ? 'disabled' : '' }}>
                            @for($y = 2030; $y >= 2015; $y--)
                                <option value="{{ $y }}" {{ $filterYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <div id="dash_wrap_start" class="col-auto {{ $ft != 'range' ? 'd-none' : '' }}">
                        <label class="form-label form-label-sm mb-1">Dari</label>
                        <input type="date" name="start_date" class="form-control form-control-sm"
                            value="{{ request('start_date', '2026-01-01') }}" {{ $ft != 'range' ? 'disabled' : '' }}>
                    </div>
                    <div id="dash_wrap_end" class="col-auto {{ $ft != 'range' ? 'd-none' : '' }}">
                        <label class="form-label form-label-sm mb-1">Sampai</label>
                        <input type="date" name="end_date" class="form-control form-control-sm"
                            value="{{ request('end_date', '2026-12-31') }}" {{ $ft != 'range' ? 'disabled' : '' }}>
                    </div>

                    @if($isPerpusnas ?? true)
                    <div class="col-auto">
                        <label class="form-label form-label-sm mb-1">Provinsi</label>
                        <select name="province_ids[]" class="form-select form-select-sm" multiple size="1" style="height:31px">
                            @foreach($provinces ?? [] as $prov)
                                <option value="{{ $prov->ID }}" {{ in_array($prov->ID, $provinceIds ?? []) ? 'selected' : '' }}>
                                    {{ $prov->NAMAPROPINSI }}
                                </option>
                            @endforeach
                        </select>
                        <div><small class="text-muted" style="font-size:.7rem">Kosong = semua provinsi</small></div>
                    </div>
                    @else
                    {{-- Non-Perpusnas: kirim province_id tersembunyi agar filter tetap teraplikasi --}}
                    @foreach($provinceIds ?? [] as $pid)
                        <input type="hidden" name="province_ids[]" value="{{ $pid }}">
                    @endforeach
                    @endif

                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary btn-sm mt-3">
                            <i class="fas fa-sync-alt"></i> Tampilkan
                        </button>
                        <a href="{{ route('dashboard_compliance') }}?filter_type=tahun&filter_year={{ date('Y') }}"
                           class="btn btn-outline-secondary btn-sm mt-3">Reset</a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>
    function toggleDashFilter() {
        const t = document.getElementById('dashFilterType').value;
        const show = id => document.getElementById(id)?.classList.remove('d-none');
        const hide = id => document.getElementById(id)?.classList.add('d-none');
        ['dash_wrap_tahun','dash_wrap_bulan','dash_wrap_bulan_year','dash_wrap_start','dash_wrap_end'].forEach(hide);
        const tahunEl  = document.getElementById('dash_year_tahun');
        const bulanEl  = document.getElementById('dash_year_bulan');
        if (t === 'tahun') {
            show('dash_wrap_tahun');
            tahunEl.disabled = false; bulanEl.disabled = true;
        } else if (t === 'bulan') {
            show('dash_wrap_bulan'); show('dash_wrap_bulan_year');
            tahunEl.disabled = true; bulanEl.disabled = false;
        } else {
            show('dash_wrap_start'); show('dash_wrap_end');
            tahunEl.disabled = true; bulanEl.disabled = true;
        }
        // disable input range saat tidak aktif agar tidak ikut submit
        document.querySelectorAll('#dash_wrap_start input, #dash_wrap_end input').forEach(el => {
            el.disabled = t !== 'range';
        });
    }
    document.addEventListener('DOMContentLoaded', toggleDashFilter);
    </script>

    @if(isset($total) && $total)
    @php
        $belumKckrTop = isset($total->TOTAL_BELUM_KCKR)
            ? $total->TOTAL_BELUM_KCKR
            : (($total->TOTAL_JUDUL ?? 0) - ($total->TOTAL_KCKR ?? 0));
    @endphp
    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col">
            <div class="card shadow-sm stat-card h-100">
                <div class="card-body text-center">
                    <div class="text-muted mb-1"><i class="fas fa-building fa-lg"></i></div>
                    <h6 class="text-muted">Total Penerbit</h6>
                    <h2 class="text-primary fw-bold">{{ number_format($total->TOTAL_PENERBIT) }}</h2>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card shadow-sm stat-card h-100">
                <div class="card-body text-center">
                    <div class="text-muted mb-1"><i class="fas fa-book fa-lg"></i></div>
                    <h6 class="text-muted">Total Judul ISBN</h6>
                    <h2 class="text-info fw-bold">{{ number_format($total->TOTAL_JUDUL) }}</h2>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card shadow-sm stat-card h-100">
                <div class="card-body text-center">
                    <div class="text-muted mb-1"><i class="fas fa-check-circle fa-lg"></i></div>
                    <h6 class="text-muted">Total Sudah KCKR</h6>
                    <h2 class="text-success fw-bold">{{ number_format($total->TOTAL_KCKR) }}</h2>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card shadow-sm stat-card h-100">
                <div class="card-body text-center">
                    <div class="mb-1" style="color:#fd7e14"><i class="fas fa-file-invoice fa-lg"></i></div>
                    <h6 class="text-muted">Belum KCKR</h6>
                    <h2 class="fw-bold" style="color:#fd7e14">{{ number_format($belumKckrTop) }}</h2>
                    <small class="text-muted" style="font-size:.72rem">judul belum setor KCKR</small>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card shadow-sm stat-card h-100">
                <div class="card-body text-center">
                    <div class="text-muted mb-1"><i class="fas fa-chart-pie fa-lg"></i></div>
                    <h6 class="text-muted">Rata-rata Kepatuhan</h6>
                    <h2 class="fw-bold {{ ($total->RATA_RATA_KEPATUHAN ?? 0) >= 61 ? 'text-success' : (($total->RATA_RATA_KEPATUHAN ?? 0) >= 41 ? 'text-warning' : 'text-danger') }}">
                        {{ number_format($total->RATA_RATA_KEPATUHAN ?? 0, 1) }}%
                    </h2>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart Perbandingan ISBN vs KCKR --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header fw-bold d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span>📈 Perbandingan ISBN Terbit vs Sudah KCKR</span>
        </div>
        <div class="card-body">
            <div class="d-flex gap-3 mb-2" style="font-size:.8rem">
                <span class="d-flex align-items-center gap-1">
                    <span style="width:10px;height:10px;border-radius:2px;background:#2a78d6;display:inline-block"></span>
                    ISBN Terbit
                </span>
                <span class="d-flex align-items-center gap-1">
                    <span style="width:10px;height:10px;border-radius:2px;background:#1baf7a;display:inline-block"></span>
                    Sudah KCKR
                </span>
                <span class="ms-auto text-muted" id="chartSubtitle" style="font-size:.75rem"></span>
            </div>
            <div id="chartLoading" class="text-center py-4 text-muted d-none">
                <div class="spinner-border spinner-border-sm me-1"></div> Memuat...
            </div>
            <div id="chartError" class="alert alert-danger d-none"></div>
            <div style="position:relative;width:100%;height:320px">
                <canvas id="isbnKckrChart" role="img" aria-label="Grouped bar chart perbandingan ISBN terbit vs sudah KCKR"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart + Distribusi --}}
    <div class="row g-3 mb-4">
        {{-- Donut Chart --}}
        <div class="col-md-5">
            <div class="card shadow-sm h-100">
                <div class="card-header fw-bold">🍩 Distribusi Kepatuhan</div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <canvas id="donutChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Bar Chart --}}
        <div class="col-md-7">
            <div class="card shadow-sm h-100">
                <div class="card-header fw-bold">📊 Jumlah Penerbit per Kategori Kepatuhan</div>
                <div class="card-body">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Distribusi Cards --}}
    @php
        $levels = [
            'Sangat Tidak Patuh' => ['color' => 'danger',  'icon' => 'fa-times-circle',     'range' => '0% – 20%'],
            'Tidak Patuh'        => ['color' => 'orange',  'icon' => 'fa-exclamation-circle','range' => '21% – 40%'],
            'Cukup Patuh'        => ['color' => 'warning', 'icon' => 'fa-minus-circle',      'range' => '41% – 60%'],
            'Patuh'              => ['color' => 'info',    'icon' => 'fa-check-circle',      'range' => '61% – 80%'],
            'Sangat Patuh'       => ['color' => 'success', 'icon' => 'fa-star',              'range' => '81% – 100%'],
        ];
        $distribusiMap = collect($distribusi)->keyBy('KATEGORI_PATUH');
    @endphp

    <div class="row g-3">
        @foreach($levels as $nama => $level)
            @php
                $d      = $distribusiMap[$nama] ?? null;
                $jumlah = $d ? $d->JUMLAH : 0;
                $pct    = $total->TOTAL_PENERBIT > 0 ? round($jumlah / $total->TOTAL_PENERBIT * 100, 1) : 0;
                // hex warna per level — tidak bergantung Bootstrap class
                $hex = match($level['color']) {
                    'danger'  => '#dc3545',
                    'orange'  => '#fd7e14',
                    'warning' => '#ffc107',
                    'info'    => '#0dcaf0',
                    'success' => '#198754',
                    default   => '#6c757d',
                };
                $textDark = in_array($level['color'], ['warning']) ? 'color:#000' : '';
            @endphp
            <div class="col-md">
                <div class="card shadow-sm stat-card h-100" style="border-top:3px solid {{ $hex }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge" style="background:{{ $hex }};{{ $textDark }}">{{ $level['range'] }}</span>
                            <i class="fas {{ $level['icon'] }} fa-lg" style="color:{{ $hex }}"></i>
                        </div>
                        <h6 class="fw-bold">{{ $nama }}</h6>
                        <h3 class="fw-bold mb-1" style="color:{{ $hex }}">{{ number_format($jumlah) }}</h3>
                        <small class="text-muted">penerbit ({{ $pct }}%)</small>
                        <div class="progress mt-2" style="height:6px">
                            <div class="progress-bar" style="width:{{ $pct }}%;background:{{ $hex }}"></div>
                        </div>
                        @if($d)
                            <hr class="my-2">
                            <small class="text-muted">
                                📚 {{ number_format($d->TOTAL_JUDUL) }} judul &nbsp;|&nbsp;
                                ✅ {{ number_format($d->TOTAL_KCKR) }} KCKR &nbsp;|&nbsp;
                                📈 avg {{ $d->RATA_RATA_PCT }}%
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── Section Status Terbit (tampil jika ada data 2026+) ── --}}
    @if(($hasV2 ?? false) && isset($total) && $total)
    <div class="row g-3 mt-2">
        <div class="col-12">
            <h5 class="fw-bold text-primary border-bottom pb-2 mb-3">
                📋 Status Terbit
                @if($isMixed ?? false)
                    <small class="text-muted fw-normal">(data 2026+ saja)</small>
                @endif
            </h5>
        </div>

        {{-- Sudah Terbit --}}
        <div class="col-md">
            <div class="card shadow-sm stat-card h-100 border-top border-3 border-success">
                <div class="card-body text-center">
                    <div class="text-success mb-1"><i class="fas fa-check-circle fa-lg"></i></div>
                    <h6 class="text-muted">Sudah Terbit</h6>
                    <h3 class="text-success fw-bold">{{ number_format($total->TOTAL_TERBIT ?? 0) }}</h3>
                    <small class="text-muted">judul</small>
                </div>
            </div>
        </div>

        {{-- Belum Terbit --}}
        <div class="col-md">
            <div class="card shadow-sm stat-card h-100 border-top border-3 border-secondary">
                <div class="card-body text-center">
                    <div class="text-secondary mb-1"><i class="fas fa-hourglass-half fa-lg"></i></div>
                    <h6 class="text-muted">Belum Terbit</h6>
                    <h3 class="text-secondary fw-bold">{{ number_format($total->TOTAL_BELUM_TERBIT ?? 0) }}</h3>
                    <small class="text-muted">judul</small>
                </div>
            </div>
        </div>

        {{-- Hutang Terbit --}}
        <div class="col-md">
            <div class="card shadow-sm stat-card h-100 border-top border-3 border-warning">
                <div class="card-body text-center">
                    <div class="text-warning mb-1"><i class="fas fa-exclamation-triangle fa-lg"></i></div>
                    <h6 class="text-muted">Hutang Terbit</h6>
                    <h3 class="text-warning fw-bold">{{ number_format($total->TOTAL_HUTANG_TERBIT ?? 0) }}</h3>
                    <small class="text-muted">judul melewati deadline terbit</small>
                </div>
            </div>
        </div>

        {{-- Lewat Teguran --}}
        <div class="col-md">
            <div class="card shadow-sm stat-card h-100 border-top border-3 border-danger">
                <div class="card-body text-center">
                    <div class="text-danger mb-1"><i class="fas fa-bell fa-lg"></i></div>
                    <h6 class="text-muted">Lewat Batas Teguran</h6>
                    <h3 class="text-danger fw-bold">{{ number_format($total->TOTAL_LEWAT_TEGURAN ?? 0) }}</h3>
                    <small class="text-muted">judul melewati +30 hari teguran</small>
                </div>
            </div>
        </div>

        {{-- Rekomendasi Sistem – Pie Chart + Keterangan --}}
        @php
            $blokirTerbit = $total->PENERBIT_LEWAT_TEGURAN ?? 0;
            $blokirKckr   = $total->PENERBIT_BLOKIR_KCKR   ?? 0;
            $baik         = $total->PENERBIT_BAIK           ?? 0;
            $rekTotal     = $blokirTerbit + $blokirKckr + $baik;
        @endphp
        <div class="col-12 mt-3">
            <div class="card shadow-sm border-top border-3 border-secondary">
                <div class="card-header bg-transparent py-2 d-flex justify-content-between align-items-center">
                    <span class="fw-semibold text-muted text-uppercase" style="font-size:.75rem;letter-spacing:.05em">🎯 Rekomendasi Sistem</span>
                    <small class="text-muted">{{ number_format($rekTotal) }} penerbit</small>
                </div>
                <div class="card-body">
                    <div class="row g-4 align-items-center">
                        {{-- Pie Chart --}}
                        <div class="col-md-5 d-flex justify-content-center">
                            <canvas id="rekChart" style="max-height:300px;max-width:380px"></canvas>
                        </div>
                        {{-- Keterangan --}}
                        <div class="col-md-7">
                            <h6 class="fw-semibold mb-3 text-secondary text-uppercase" style="font-size:.75rem;letter-spacing:.05em">Keterangan Status Rekomendasi</h6>
                            <div class="mb-3 p-3 rounded" style="background:#fff5f5;border-left:4px solid #dc3545">
                                <div class="d-flex align-items-start gap-2">
                                    <span style="color:#dc3545;font-size:1.1rem">🔴</span>
                                    <div>
                                        <strong class="text-danger">Blokir Konfirmasi Terbit</strong>
                                        <span class="badge bg-danger ms-1">{{ number_format($blokirTerbit) }} penerbit</span>
                                        <p class="mb-1 mt-1 small text-muted">Penerbit yang memiliki judul ISBN melewati <strong>+30 hari</strong> setelah batas konfirmasi terbit tanpa melakukan konfirmasi.</p>
                                        <p class="mb-0 small"><strong>Indikator:</strong> <code>Lewat Teguran &gt; 0</code></p>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 p-3 rounded" style="background:#fff9f0;border-left:4px solid #fd7e14">
                                <div class="d-flex align-items-start gap-2">
                                    <span style="font-size:1.1rem">🟠</span>
                                    <div>
                                        <strong style="color:#d96000">Blokir SS KCKR</strong>
                                        <span class="badge ms-1" style="background:#fd7e14">{{ number_format($blokirKckr) }} penerbit</span>
                                        <p class="mb-1 mt-1 small text-muted">Penerbit yang terlambat menyerahkan KCKR dengan tingkat kepatuhan rendah (≤ {{ $minPct ?? 20 }}%), namun belum masuk kategori Blokir Konfirmasi Terbit.</p>
                                        <p class="mb-0 small"><strong>Indikator:</strong> <code>Lewat Teguran = 0 AND Terlambat KCKR &gt; 0 AND % KCKR ≤ {{ $minPct ?? 20 }}%</code></p>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 p-3 rounded" style="background:#f0fff4;border-left:4px solid #198754">
                                <div class="d-flex align-items-start gap-2">
                                    <span style="font-size:1.1rem">🟢</span>
                                    <div>
                                        <strong class="text-success">Baik</strong>
                                        <span class="badge bg-success ms-1">{{ number_format($baik) }} penerbit</span>
                                        <p class="mb-1 mt-1 small text-muted">Penerbit yang patuh: tidak ada judul melewati batas teguran, dan tidak terlambat KCKR atau kepatuhan sudah di atas {{ $minPct ?? 20 }}%.</p>
                                        <p class="mb-0 small"><strong>Indikator:</strong> <code>Lewat Teguran = 0 AND (Terlambat KCKR = 0 ATAU % KCKR &gt; {{ $minPct ?? 20 }}%)</code></p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-2 rounded" style="background:#f8f9fa;border:1px dashed #dee2e6">
                                <small class="text-muted"><i class="fas fa-info-circle me-1"></i>
                                    <strong>Belum KCKR:</strong>
                                    @if($isMixed ?? false)
                                        Pra-2026 = semua judul tanpa KCKR; 2026+ = judul yang sudah konfirmasi terbit namun belum setor KCKR.
                                    @else
                                        Judul yang sudah konfirmasi terbit (2026+) namun belum menyerahkan KCKR.
                                    @endif
                                    Total: <strong style="color:#fd7e14">{{ number_format($total->TOTAL_BELUM_KCKR ?? 0) }} judul</strong>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ── Rekomendasi V1 (hanya mode murni s.d 2025) ── --}}
    @if(!($hasV2 ?? false) && isset($total) && $total)
    @php
        $blokirKckrV1 = $total->PENERBIT_BLOKIR_KCKR ?? 0;
        $baikV1       = $total->PENERBIT_BAIK         ?? 0;
        $rekTotalV1   = $blokirKckrV1 + $baikV1;
    @endphp
    <div class="row g-3 mt-2">
        <div class="col-12">
            <div class="card shadow-sm border-top border-3 border-secondary">
                <div class="card-header bg-transparent py-2 d-flex justify-content-between align-items-center">
                    <span class="fw-semibold text-muted text-uppercase" style="font-size:.75rem;letter-spacing:.05em">🎯 Rekomendasi Sistem</span>
                    <small class="text-muted">{{ number_format($rekTotalV1) }} penerbit</small>
                </div>
                <div class="card-body">
                    <div class="row g-4 align-items-center">
                        {{-- Pie Chart --}}
                        <div class="col-md-5 d-flex justify-content-center">
                            <canvas id="rekChart" style="max-height:300px;max-width:380px"></canvas>
                        </div>
                        {{-- Keterangan V1 --}}
                        <div class="col-md-7">
                            <h6 class="fw-semibold mb-3 text-secondary text-uppercase" style="font-size:.75rem;letter-spacing:.05em">Keterangan Status Rekomendasi (Pra-2026)</h6>
                            <div class="mb-3 p-3 rounded" style="background:#fff9f0;border-left:4px solid #fd7e14">
                                <div class="d-flex align-items-start gap-2">
                                    <span style="font-size:1.1rem">🟠</span>
                                    <div>
                                        <strong style="color:#d96000">Blokir SS KCKR</strong>
                                        <span class="badge ms-1" style="background:#fd7e14">{{ number_format($blokirKckrV1) }} penerbit</span>
                                        <p class="mb-1 mt-1 small text-muted">Penerbit yang terlambat menyerahkan KCKR (berdasarkan 3 bulan dari tanggal ISBN diterima) dengan kepatuhan ≤ {{ $minPct ?? 20 }}%.</p>
                                        <p class="mb-0 small"><strong>Indikator:</strong> <code>Terlambat KCKR &gt; 0 AND % KCKR ≤ {{ $minPct ?? 20 }}%</code></p>
                                        <p class="mb-0 small text-muted">Deadline KCKR = tanggal ISBN + 3 bulan (berdasarkan kategori/jenis media)</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 p-3 rounded" style="background:#f0fff4;border-left:4px solid #198754">
                                <div class="d-flex align-items-start gap-2">
                                    <span style="font-size:1.1rem">🟢</span>
                                    <div>
                                        <strong class="text-success">Baik</strong>
                                        <span class="badge bg-success ms-1">{{ number_format($baikV1) }} penerbit</span>
                                        <p class="mb-1 mt-1 small text-muted">Penerbit yang patuh terhadap kewajiban KCKR: tidak terlambat atau kepatuhan sudah di atas {{ $minPct ?? 20 }}%.</p>
                                        <p class="mb-0 small"><strong>Indikator:</strong> <code>Terlambat KCKR = 0 ATAU % KCKR &gt; {{ $minPct ?? 20 }}%</code></p>
                                    </div>
                                </div>
                            </div>
                            <div class="p-2 rounded" style="background:#f8f9fa;border:1px dashed #dee2e6">
                                <small class="text-muted"><i class="fas fa-info-circle me-1"></i>
                                    <strong>Belum KCKR (Pra-2026):</strong> Semua judul ISBN yang belum menyerahkan KCKR sejak terbit.
                                    Total: <strong style="color:#fd7e14">{{ number_format(($total->TOTAL_JUDUL ?? 0) - ($total->TOTAL_KCKR ?? 0)) }} judul</strong>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
// Daftarkan plugin datalabels global
Chart.register(ChartDataLabels);

@if(isset($distribusi) && count($distribusi) > 0)
    const labels  = ['Sangat Tidak Patuh', 'Tidak Patuh', 'Cukup Patuh', 'Patuh', 'Sangat Patuh'];
    const colors  = ['#dc3545', '#fd7e14', '#ffc107', '#0dcaf0', '#198754'];
    const distMap = {};
    @foreach($distribusi as $d)
        distMap['{{ $d->KATEGORI_PATUH }}'] = {{ $d->JUMLAH }};
    @endforeach
    const counts    = labels.map(l => distMap[l] ?? 0);
    const distTotal = counts.reduce((a,b) => a+b, 0);

    // Donut — label langsung di segmen
    new Chart(document.getElementById('donutChart'), {
        type: 'doughnut',
        data: { labels, datasets: [{ data: counts, backgroundColor: colors, borderWidth: 2 }] },
        options: {
            cutout: '55%',
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 10 } },
                datalabels: {
                    color: '#fff',
                    font: { size: 11, weight: 'bold' },
                    formatter: (val, ctx) => {
                        if (val === 0) return '';
                        const pct = distTotal > 0 ? ((val / distTotal) * 100).toFixed(1) : 0;
                        return val.toLocaleString('id') + '\n' + pct + '%';
                    },
                    textAlign: 'center',
                    display: ctx => ctx.dataset.data[ctx.dataIndex] > 0,
                }
            }
        }
    });

    // Bar — angka di atas setiap batang
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Jumlah Penerbit',
                data: counts,
                backgroundColor: colors,
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            layout: { padding: { top: 28 } },
            plugins: {
                legend: { display: false },
                datalabels: {
                    anchor: 'end',
                    align: 'top',
                    clip: false,
                    color: '#333',
                    font: { size: 12, weight: 'bold' },
                    formatter: val => val > 0 ? val.toLocaleString('id') : '',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grace: '15%',
                }
            },
        }
    });
@endif

// Pie Chart Rekomendasi Sistem
@if(isset($total) && $total)
(function() {
    const rekEl = document.getElementById('rekChart');
    if (!rekEl) return;
    @if($hasV2 ?? false)
        const rekLabels = ['Blokir Konfirmasi Terbit', 'Blokir SS KCKR', 'Baik'];
        const rekData   = [{{ $blokirTerbit ?? 0 }}, {{ $blokirKckr ?? 0 }}, {{ $baik ?? 0 }}];
        const rekColors = ['#dc3545', '#fd7e14', '#198754'];
    @else
        const rekLabels = ['Blokir SS KCKR', 'Baik'];
        const rekData   = [{{ $total->PENERBIT_BLOKIR_KCKR ?? 0 }}, {{ $total->PENERBIT_BAIK ?? 0 }}];
        const rekColors = ['#fd7e14', '#198754'];
    @endif
    const rekTotal = rekData.reduce((a,b) => a+b, 0);
    new Chart(rekEl, {
        type: 'pie',
        data: {
            labels: rekLabels,
            datasets: [{ data: rekData, backgroundColor: rekColors, borderWidth: 2, hoverOffset: 10 }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { size: 12 }, padding: 12, boxWidth: 14 }
                },
                datalabels: {
                    color: '#fff',
                    font: { size: 13, weight: 'bold' },
                    formatter: (val, ctx) => {
                        if (val === 0) return '';
                        const pct = rekTotal > 0 ? ((val / rekTotal) * 100).toFixed(1) : 0;
                        return val.toLocaleString('id') + '\n' + pct + '%';
                    },
                    textAlign: 'center',
                    display: ctx => ctx.dataset.data[ctx.dataIndex] > 0,
                },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const v   = ctx.parsed;
                            const pct = rekTotal > 0 ? ((v / rekTotal) * 100).toFixed(1) : 0;
                            return ` ${ctx.label}: ${v.toLocaleString('id')} (${pct}%)`;
                        }
                    }
                }
            }
        }
    });
})();
@endif

// ── Chart ISBN vs KCKR ───────────────────────────────────────────────────────
let isbnKckrChartInstance = null;

function loadChart() {
    // Baca dari filter utama dashboard (URL params saat ini)
    const p = new URLSearchParams(window.location.search);
    const params = new URLSearchParams();

    const ft = p.get('filter_type') || '{{ $dateFilter["type"] ?? "tahun" }}';
    params.set('filter_type', ft);

    if (ft === 'tahun') {
        params.set('filter_year', p.get('filter_year') || '{{ request("filter_year", date("Y")) }}');
    } else if (ft === 'bulan') {
        params.set('filter_year',  p.get('filter_year')  || '{{ request("filter_year", date("Y")) }}');
        params.set('filter_month', p.get('filter_month') || '{{ request("filter_month", date("n")) }}');
    } else {
        params.set('start_date', p.get('start_date') || '{{ request("start_date") }}');
        params.set('end_date',   p.get('end_date')   || '{{ request("end_date") }}');
    }

    @if(!empty($provinceIds))
        @foreach($provinceIds as $pid)
            params.append('province_ids[]', '{{ $pid }}');
        @endforeach
    @endif

    document.getElementById('chartLoading').classList.remove('d-none');
    document.getElementById('chartError').classList.add('d-none');

    fetch('{{ route("dashboard_compliance.chart") }}?' + params.toString())
        .then(r => r.json())
        .then(res => {
            document.getElementById('chartLoading').classList.add('d-none');
            if (res.error) {
                document.getElementById('chartError').textContent = res.error;
                document.getElementById('chartError').classList.remove('d-none');
                return;
            }

            const granLabel = { hari: 'per hari', bulan: 'per bulan', tahun: 'per tahun' };
            let subtitle = '';
            if (ft === 'tahun') subtitle = 'Tahun ' + params.get('filter_year') + ' — breakdown per bulan';
            else if (ft === 'bulan') subtitle = 'Bulan ' + params.get('filter_month') + '/' + params.get('filter_year') + ' — breakdown per hari';
            else subtitle = (params.get('start_date') || '') + ' s.d. ' + (params.get('end_date') || '') + ' — ' + (granLabel[res.granularity] || '');
            document.getElementById('chartSubtitle').textContent = subtitle;

            if (isbnKckrChartInstance) isbnKckrChartInstance.destroy();

            isbnKckrChartInstance = new Chart(document.getElementById('isbnKckrChart'), {
                type: 'bar',
                data: {
                    labels: res.labels,
                    datasets: [
                        {
                            label: 'ISBN Terbit',
                            data: res.isbn,
                            backgroundColor: '#2a78d6',
                            borderRadius: { topLeft: 4, topRight: 4 },
                            borderSkipped: false,
                            barPercentage: 0.75,
                            categoryPercentage: 0.8,
                            datalabels: {
                                anchor: 'end',
                                align: 'end',
                                clip: false,
                                color: '#1a1a1a',
                                font: { size: 10, weight: 'bold' },
                                formatter: val => val > 0 ? val.toLocaleString('id') : '',
                            }
                        },
                        {
                            label: 'Sudah KCKR',
                            data: res.kckr,
                            backgroundColor: '#1baf7a',
                            borderRadius: { topLeft: 4, topRight: 4 },
                            borderSkipped: false,
                            barPercentage: 0.75,
                            categoryPercentage: 0.8,
                            datalabels: {
                                anchor: 'end',
                                align: 'end',
                                clip: false,
                                color: '#1a1a1a',
                                font: { size: 10, weight: 'bold' },
                                formatter: (val, ctx) => {
                                    const i   = ctx.dataIndex;
                                    const pct = res.isbn[i] > 0 ? (val / res.isbn[i] * 100).toFixed(1) : '0.0';
                                    return val > 0 ? val.toLocaleString('id') + ' (' + pct + '%)' : '';
                                },
                            }
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: { padding: { top: 24 } },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                afterBody: (items) => {
                                    const i   = items[0].dataIndex;
                                    const pct = res.isbn[i] > 0 ? Math.round(res.kckr[i] / res.isbn[i] * 100) : 0;
                                    const gap = (res.isbn[i] - res.kckr[i]).toLocaleString('id');
                                    return ['Kepatuhan: ' + pct + '%', 'Belum KCKR: ' + gap];
                                }
                            }
                        },
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#333', font: { size: 12 } },
                        },
                        y: {
                            grid: { color: '#e1e0d9' },
                            border: { display: false },
                            grace: '15%',
                            ticks: {
                                color: '#333',
                                font: { size: 11 },
                                callback: v => v >= 1000 ? (v/1000).toFixed(1).replace('.0','')+'rb' : v
                            }
                        }
                    }
                }
            });
        })
        .catch(err => {
            document.getElementById('chartLoading').classList.add('d-none');
            document.getElementById('chartError').textContent = 'Gagal memuat data chart.';
            document.getElementById('chartError').classList.remove('d-none');
        });
}

document.addEventListener('DOMContentLoaded', () => loadChart());
// ─────────────────────────────────────────────────────────────────────────────

function downloadPDF() {
    const btn = document.querySelector('button[onclick="downloadPDF()"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';

    const el = document.getElementById('dashboardContent');

    const pdfHeader = document.getElementById('pdfHeader');
    pdfHeader.style.display = 'block';
    const hidden = el.querySelectorAll('.btn, form, #dashFilterCard');
    hidden.forEach(e => e.style.display = 'none');

    const SCALE = 2;

    html2canvas(el, {
        scale: SCALE,
        useCORS: true,
        allowTaint: true,
        logging: false,
        backgroundColor: '#ffffff',
        windowWidth: 1700,
        onclone: (doc) => {
            doc.querySelectorAll('canvas').forEach(c => {
                const original = document.getElementById(c.id);
                if (original) {
                    const img = doc.createElement('img');
                    img.src = original.toDataURL('image/png');
                    img.style.width  = original.offsetWidth  + 'px';
                    img.style.height = original.offsetHeight + 'px';
                    c.parentNode.replaceChild(img, c);
                }
            });
        }
    }).then(canvas => {
        pdfHeader.style.display = 'none';
        hidden.forEach(e => e.style.display = '');

        const { jsPDF } = window.jspdf;
        const imgData = canvas.toDataURL('image/jpeg', 0.92);

        // Buat PDF dengan ukuran persis mengikuti konten (1px = 0.264583mm @96dpi)
        const MM_PER_PX = 25.4 / 96;
        const pdfW = (canvas.width  / SCALE) * MM_PER_PX;
        const pdfH = (canvas.height / SCALE) * MM_PER_PX;

        const pdf = new jsPDF({
            orientation: pdfW > pdfH ? 'l' : 'p',
            unit: 'mm',
            format: [pdfW, pdfH]
        });
        pdf.addImage(imgData, 'JPEG', 0, 0, pdfW, pdfH, '', 'FAST');

        const now = new Date();
        const filename = `Dashboard-Kepatuhan-${now.getFullYear()}${String(now.getMonth()+1).padStart(2,'0')}${String(now.getDate()).padStart(2,'0')}.pdf`;
        pdf.save(filename);

        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-file-pdf"></i> Download PDF';
    }).catch(err => {
        pdfHeader.style.display = 'none';
        hidden.forEach(e => e.style.display = '');
        console.error(err);
        alert('Gagal: ' + err.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-file-pdf"></i> Download PDF';
    });
}
</script>
@endsection
