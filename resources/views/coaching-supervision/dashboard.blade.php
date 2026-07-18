@extends('layouts.app')

@section('content')
<style>
    body { background: #f4f6fb; }

    #dashboardContent .card {
        border: 1px solid rgba(15, 40, 80, .06);
        border-radius: 16px;
        box-shadow: 0 1px 2px rgba(15, 40, 80, .04), 0 4px 16px rgba(15, 40, 80, .05);
    }
    #dashboardContent .card-header {
        background: transparent;
        border-bottom: 1px solid rgba(15, 40, 80, .07);
        font-size: .88rem;
        padding-top: .8rem;
        padding-bottom: .8rem;
    }
    #dashboardContent h2 { letter-spacing: -.02em; }

    .stat-card {
        transition: transform .18s ease, box-shadow .18s ease;
        position: relative;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(15, 40, 80, .10) !important;
    }
    .stat-card .card-body { padding: 1.1rem .9rem; }
    .stat-card h6 {
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        font-weight: 600;
    }
    .stat-card h2, .stat-card h3 { letter-spacing: -.02em; margin-bottom: .1rem; }

    /* icon bubble */
    .icon-bubble {
        width: 42px; height: 42px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: .45rem;
        font-size: 1.05rem;
    }

    /* header section */
    #dashboardContent h5.fw-bold {
        font-size: 1rem;
        letter-spacing: -.01em;
    }

    /* tombol lebih halus */
    #dashboardContent .btn { border-radius: 10px; }
    #dashboardContent .btn-group .btn { border-radius: 10px; }
    #dashboardContent .btn-group .btn:first-child { border-top-right-radius: 0; border-bottom-right-radius: 0; }
    #dashboardContent .btn-group .btn:last-child  { border-top-left-radius: 0; border-bottom-left-radius: 0; }

    /* filter card */
    #dashFilterCard { background: #ffffff; }
    #dashFilterCard .form-select, #dashFilterCard .form-control { border-radius: 8px; }

    /* alert info mode */
    #dashboardContent .alert { border: none; border-radius: 12px; }

    .badge-patuh-0  { background-color: #dc3545; }
    .badge-patuh-1  { background-color: #fd7e14; }
    .badge-patuh-2  { background-color: #ffc107; color: #000; }
    .badge-patuh-3  { background-color: #0dcaf0; color: #000; }
    .badge-patuh-4  { background-color: #198754; }
    .progress-bar-striped { animation: progress-bar-stripes 1s linear infinite; }
    #dashboardContent .progress { border-radius: 99px; background: #eef1f7; }
    #dashboardContent .progress-bar { border-radius: 99px; }

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
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 rounded-4"
         style="background:linear-gradient(120deg,#eaf1fd 0%,#f6f9ff 55%,#eef8f3 100%);border:1px solid rgba(42,120,214,.12)">
        <div>
            <h2 class="mb-1 fw-bold" style="color:#173a63">📊 Dashboard Kepatuhan Penerbit</h2>
            <div class="text-muted" style="font-size:.82rem">Pemantauan kepatuhan KCKR & konfirmasi terbit — {{ $periodeLabel }}</div>
            @if($isMixed ?? false)
                <span class="badge bg-warning text-dark mt-1">Mode Campuran — pra-2026 + 2026+</span>
            @elseif($hasV2 ?? false)
                <span class="badge bg-primary mt-1">Mode 2026+ — berbasis Tanggal Terbit</span>
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
                <button id="btnDashPerpusnas" onclick="setDashKckrMode('perpusnas')"
                   class="btn {{ $currentKckrMode === 'perpusnas' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="ph-bank"></i> Data Perpusnas
                </button>
                <button id="btnDashProvinsi" onclick="setDashKckrMode('provinsi')"
                   class="btn {{ $currentKckrMode === 'provinsi' ? 'btn-success' : 'btn-outline-success' }}">
                    <i class="ph-map-pin"></i> Data Provinsi
                </button>
            </div>
            @endif
            <button onclick="downloadPDF()" class="btn btn-danger btn-sm">
                <i class="ph-file-pdf"></i> Download PDF
            </button>
            <a href="{{ $detailRoute }}?{{ http_build_query($toCompliance) }}" class="btn btn-outline-primary btn-sm">
                <i class="ph-table"></i> Lihat Detail
            </a>
        </div>
    </div>

    @if($isPerpusnas ?? true)
    <div class="alert {{ ($currentKckrMode === 'provinsi') ? 'alert-success' : 'alert-primary' }} py-2 px-3 mb-3" style="font-size:.85rem">
        @if($currentKckrMode === 'provinsi')
            <i class="ph-map-pin me-1"></i>
            <strong>Mode: Data KCKR Provinsi</strong> — menampilkan kepatuhan berdasarkan tanggal penerimaan KCKR di perpustakaan daerah (<code>received_date_prov</code>).
        @else
            <i class="ph-bank me-1"></i>
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
                $ft          = $dateFilter['type'] ?? 'range';
                $filterYear  = request('filter_year', date('Y'));
                $filterMonth = request('filter_month', date('n'));
            @endphp
            <form method="GET" action="{{ route('dashboard_compliance') }}">
                <div class="row g-2 align-items-end">

                    <div class="col-auto">
                        <label class="form-label form-label-sm mb-1">Tipe Filter</label>
                        <select class="form-select form-select-sm" name="filter_type" id="dashFilterType" onchange="toggleDashFilter()">
                            <option value="range"  {{ $ft == 'range'  ? 'selected' : '' }}>Kumulatif</option>
                            <option value="tahun"  {{ $ft == 'tahun'  ? 'selected' : '' }}>Per Tahun</option>
                            <option value="bulan"  {{ $ft == 'bulan'  ? 'selected' : '' }}>Per Bulan</option>
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
                            value="{{ request('start_date', '2021-01-01') }}" {{ $ft != 'range' ? 'disabled' : '' }}>
                    </div>
                    <div id="dash_wrap_end" class="col-auto {{ $ft != 'range' ? 'd-none' : '' }}">
                        <label class="form-label form-label-sm mb-1">Sampai</label>
                        <input type="date" name="end_date" class="form-control form-control-sm"
                            value="{{ request('end_date', date('Y-m-d')) }}" {{ $ft != 'range' ? 'disabled' : '' }}>
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
                            <i class="ph-arrows-clockwise"></i> Tampilkan
                        </button>
                        <a href="{{ route('dashboard_compliance') }}?filter_type=range&start_date=2021-01-01&end_date={{ date('Y-m-d') }}"
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
            <div class="card stat-card h-100" style="border-top:4px solid #2a78d6">
                <div class="card-body text-center">
                    <div class="icon-bubble" style="background:#e8f0fc;color:#2a78d6"><i class="ph-buildings"></i></div>
                    <h6 class="text-muted">Total Penerbit</h6>
                    <h2 class="fw-bold" style="color:#2a78d6">{{ number_format($total->TOTAL_PENERBIT) }}</h2>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100" style="border-top:4px solid #0dcaf0">
                <div class="card-body text-center">
                    <div class="icon-bubble" style="background:#e3f8fd;color:#0aa2c0"><i class="ph-book"></i></div>
                    <h6 class="text-muted">Total Judul ISBN</h6>
                    <h2 class="fw-bold" style="color:#0aa2c0">{{ number_format($total->TOTAL_JUDUL) }}</h2>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100" style="border-top:4px solid #198754">
                <div class="card-body text-center">
                    <div class="icon-bubble" style="background:#e6f6ee;color:#198754"><i class="ph-check-circle"></i></div>
                    <h6 class="text-muted">Total Sudah KCKR</h6>
                    <h2 class="fw-bold text-success">{{ number_format($total->TOTAL_KCKR) }}</h2>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card stat-card h-100" style="border-top:4px solid #fd7e14">
                <div class="card-body text-center">
                    <div class="icon-bubble" style="background:#fff2e5;color:#fd7e14"><i class="ph-file-text"></i></div>
                    <h6 class="text-muted">Belum KCKR</h6>
                    <h2 class="fw-bold" style="color:#fd7e14">{{ number_format($belumKckrTop) }}</h2>
                    <small class="text-muted" style="font-size:.72rem">judul belum setor KCKR</small>
                </div>
            </div>
        </div>
        <div class="col">
            @php
                $avgK = $total->RATA_RATA_KEPATUHAN ?? 0;
                $avgColor = $avgK >= 61 ? '#198754' : ($avgK >= 41 ? '#d9a406' : '#dc3545');
                $avgBg    = $avgK >= 61 ? '#e6f6ee' : ($avgK >= 41 ? '#fdf6e0' : '#fdeaec');
            @endphp
            <div class="card stat-card h-100" style="border-top:4px solid {{ $avgColor }}">
                <div class="card-body text-center">
                    <div class="icon-bubble" style="background:{{ $avgBg }};color:{{ $avgColor }}"><i class="ph-chart-pie"></i></div>
                    <h6 class="text-muted">Rata-rata Kepatuhan</h6>
                    <h2 class="fw-bold" style="color:{{ $avgColor }}">{{ number_format($avgK, 1) }}%</h2>
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
            'Sangat Tidak Patuh' => ['color' => 'danger',  'icon' => 'ph-x-circle',     'range' => '0% – 20%'],
            'Tidak Patuh'        => ['color' => 'orange',  'icon' => 'ph-warning-circle','range' => '21% – 40%'],
            'Cukup Patuh'        => ['color' => 'warning', 'icon' => 'ph-minus-circle',      'range' => '41% – 60%'],
            'Patuh'              => ['color' => 'info',    'icon' => 'ph-check-circle',      'range' => '61% – 80%'],
            'Sangat Patuh'       => ['color' => 'success', 'icon' => 'ph-star',              'range' => '81% – 100%'],
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
                            <i class="{{ $level['icon'] }} ph-lg" style="color:{{ $hex }}"></i>
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
                    <div class="text-success mb-1"><i class="ph-check-circle ph-lg"></i></div>
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
                    <div class="text-secondary mb-1"><i class="ph-hourglass-medium ph-lg"></i></div>
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
                    <div class="text-warning mb-1"><i class="ph-warning ph-lg"></i></div>
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
                    <div class="text-danger mb-1"><i class="ph-bell ph-lg"></i></div>
                    <h6 class="text-muted">Lewat Batas Teguran</h6>
                    <h3 class="text-danger fw-bold">{{ number_format($total->TOTAL_LEWAT_TEGURAN ?? 0) }}</h3>
                    <small class="text-muted">judul melewati +30 hari teguran</small>
                </div>
            </div>
        </div>

        {{-- Rekomendasi Sistem – Pie Chart + Keterangan --}}
        @php
            $blokirTerbit   = $total->PENERBIT_BLOKIR_TERBIT   ?? 0;
            $blokirKckr     = $total->PENERBIT_BLOKIR_KCKR     ?? 0;
            $blokirKeduanya = $total->PENERBIT_BLOKIR_KEDUANYA ?? 0;
            $baik           = $total->PENERBIT_BAIK             ?? 0;
            $rekTotal       = $blokirTerbit + $blokirKckr + $blokirKeduanya + $baik;
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
                                        <p class="mb-1 mt-1 small text-muted">Penerbit yang melewati batas teguran konfirmasi terbit, namun KCKR-nya masih baik.</p>
                                        <p class="mb-0 small"><strong>Indikator:</strong> <code>Lewat Teguran &gt; 0 AND KCKR baik</code></p>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 p-3 rounded" style="background:#fff9f0;border-left:4px solid #fd7e14">
                                <div class="d-flex align-items-start gap-2">
                                    <span style="font-size:1.1rem">🟠</span>
                                    <div>
                                        <strong style="color:#d96000">Blokir SS KCKR</strong>
                                        <span class="badge ms-1" style="background:#fd7e14">{{ number_format($blokirKckr) }} penerbit</span>
                                        <p class="mb-1 mt-1 small text-muted">Penerbit terlambat KCKR (≤ {{ $minPct ?? 20 }}%), namun konfirmasi terbit masih baik.</p>
                                        <p class="mb-0 small"><strong>Indikator:</strong> <code>Terlambat KCKR &gt; 0 AND % KCKR ≤ {{ $minPct ?? 20 }}% AND Lewat Teguran = 0</code></p>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 p-3 rounded" style="background:#fdf0ff;border-left:4px solid #6f42c1">
                                <div class="d-flex align-items-start gap-2">
                                    <span style="font-size:1.1rem">🟣</span>
                                    <div>
                                        <strong style="color:#6f42c1">Blokir Konfirm + SSKCKR</strong>
                                        <span class="badge ms-1" style="background:#6f42c1">{{ number_format($blokirKeduanya) }} penerbit</span>
                                        <p class="mb-1 mt-1 small text-muted">Penerbit yang terkena kedua blokir sekaligus: lewat teguran konfirmasi terbit DAN terlambat KCKR.</p>
                                        <p class="mb-0 small"><strong>Indikator:</strong> <code>Lewat Teguran &gt; 0 AND Terlambat KCKR &gt; 0 AND % KCKR ≤ {{ $minPct ?? 20 }}%</code></p>
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
                                <small class="text-muted"><i class="ph-info me-1"></i>
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
                                <small class="text-muted"><i class="ph-info me-1"></i>
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

    {{-- ── Prediksi Blokir (hanya tampil jika ada data 2026+) ── --}}
    @if(($hasV2 ?? false) || ($isMixed ?? false))
    <div class="mt-4" id="prediksiSection">
        <h5 class="fw-bold text-danger border-bottom pb-2 mb-3">
            ⚠️ Prediksi Penerbit Akan Blokir Konfirmasi Terbit
            <small class="text-muted fw-normal" style="font-size:.85rem">(dalam 90 hari ke depan)</small>
        </h5>
        <div id="prediksiLoading" class="text-center py-3 text-muted">
            <div class="spinner-border spinner-border-sm me-1"></div> Memuat prediksi...
        </div>
        <div id="prediksiContent" class="d-none">
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="card shadow-sm h-100 border-top border-3 border-danger">
                        <div class="card-body text-center">
                            <div class="text-danger mb-1"><i class="ph-warning-circle ph-lg"></i></div>
                            <h6 class="text-muted">Dalam 30 Hari</h6>
                            <h2 class="text-danger fw-bold" id="predD30">-</h2>
                            <small class="text-muted">penerbit akan kena blokir</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm h-100 border-top border-3 border-warning">
                        <div class="card-body text-center">
                            <div class="text-warning mb-1"><i class="ph-clock ph-lg"></i></div>
                            <h6 class="text-muted">31–60 Hari</h6>
                            <h2 class="text-warning fw-bold" id="predD60">-</h2>
                            <small class="text-muted">penerbit akan kena blokir</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm h-100 border-top border-3" style="border-color:#0dcaf0!important">
                        <div class="card-body text-center">
                            <div style="color:#0dcaf0" class="mb-1"><i class="ph-calendar ph-lg"></i></div>
                            <h6 class="text-muted">61–90 Hari</h6>
                            <h2 class="fw-bold" style="color:#0dcaf0" id="predD90">-</h2>
                            <small class="text-muted">penerbit akan kena blokir</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-header fw-semibold" style="font-size:.9rem">
                    📅 Timeline — penerbit yang akan masuk blokir per hari (30 hari ke depan)
                </div>
                <div class="card-body">
                    <div style="position:relative;width:100%;height:200px">
                        <canvas id="prediksiChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div id="prediksiError" class="alert alert-danger d-none"></div>
    </div>
    @endif

    {{-- ── Breakdown Kategori & Media + Top 10 Hutang ── --}}
    <div class="mt-4" id="breakdownSection">
        <h5 class="fw-bold text-primary border-bottom pb-2 mb-3">🧩 Breakdown Kepatuhan</h5>
        <div id="breakdownLoading" class="text-center py-3 text-muted">
            <div class="spinner-border spinner-border-sm me-1"></div> Memuat breakdown...
        </div>
        <div id="breakdownContent" class="d-none">
            <div class="row g-3 mb-3">
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header fw-semibold">🏛️ Pemerintah vs Swasta</div>
                        <div class="card-body">
                            <div style="position:relative;width:100%;height:170px">
                                <canvas id="kategoriChart"></canvas>
                            </div>
                            <div id="kategoriLegend" class="mt-2 d-flex flex-wrap gap-3" style="font-size:.78rem"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header fw-semibold">💿 Karya Cetak vs Karya Rekam</div>
                        <div class="card-body">
                            <div style="position:relative;width:100%;height:170px">
                                <canvas id="mediaChart"></canvas>
                            </div>
                            <div id="mediaLegend" class="mt-2 d-flex flex-wrap gap-3" style="font-size:.78rem"></div>
                            <div id="ecolStrip" class="mt-2 p-2 rounded d-none" style="background:#f6f8fc;border:1px dashed #d8e0ee;font-size:.78rem">
                                <div class="fw-semibold text-muted mb-1" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.04em">
                                    Status Setoran Karya Rekam di e-Deposit
                                </div>
                                <div class="d-flex flex-wrap gap-3">
                                    <span>🔵 In Review: <strong id="ecolReview">-</strong> judul</span>
                                    <span>🟢 Diterima: <strong id="ecolDiterima">-</strong> judul</span>
                                    <span>🔴 Bermasalah: <strong id="ecolBermasalah">-</strong> judul</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                    <span>🔥 Top 10 Penerbit Penyumbang Hutang KCKR Terbesar</span>
                    <small class="text-muted fw-normal">judul wajib KCKR yang belum disetor</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0 align-middle" style="font-size:.85rem">
                            <thead style="background:#f6f8fc">
                                <tr>
                                    <th class="ps-3" style="width:40px">#</th>
                                    <th>Penerbit</th>
                                    <th class="text-end">Total Judul</th>
                                    <th class="text-end">Sudah KCKR</th>
                                    <th class="text-end">Hutang KCKR</th>
                                    <th class="text-end pe-3" style="width:180px">% Kepatuhan</th>
                                </tr>
                            </thead>
                            <tbody id="top10Body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div id="breakdownError" class="alert alert-danger d-none"></div>
    </div>

    {{-- ── Perbandingan Periode + Aging Hutang + Lama Konfirmasi ── --}}
    <div class="mt-4" id="insightSection">
        <h5 class="fw-bold text-primary border-bottom pb-2 mb-3">📈 Tren & Kedalaman Data</h5>
        <div id="insightLoading" class="text-center py-3 text-muted">
            <div class="spinner-border spinner-border-sm me-1"></div> Memuat insight...
        </div>
        <div id="insightContent" class="d-none">
            <div class="row g-3">
                {{-- Perbandingan Periode --}}
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header fw-semibold">⚖️ vs Periode Sebelumnya</div>
                        <div class="card-body">
                            <div class="text-muted mb-2" style="font-size:.72rem" id="prevPeriodLabel"></div>
                            <div id="periodCompare"></div>
                        </div>
                    </div>
                </div>
                {{-- Aging Hutang --}}
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header fw-semibold">⏳ Aging Hutang KCKR</div>
                        <div class="card-body">
                            <div style="position:relative;width:100%;height:190px">
                                <canvas id="agingChart"></canvas>
                            </div>
                            <div class="text-muted mt-2" style="font-size:.72rem">
                                Usia kewajiban KCKR yang belum disetor — pra-2026 sejak ISBN terbit, 2026+ sejak konfirmasi terbit.
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Lama Konfirmasi Terbit --}}
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header fw-semibold">⏱️ Lama Konfirmasi Terbit <small class="text-muted fw-normal">(2026+)</small></div>
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="p-3 rounded" style="background:#e8f0fc">
                                        <h3 class="fw-bold mb-0" style="color:#2a78d6" id="konfirmMedian">-</h3>
                                        <small class="text-muted">hari (median)</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 rounded" style="background:#f0f9f4">
                                        <h3 class="fw-bold mb-0 text-success" id="konfirmAvg">-</h3>
                                        <small class="text-muted">hari (rata-rata)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="text-muted mt-3" style="font-size:.72rem">
                                Dihitung dari tanggal ISBN terbit sampai penerbit melakukan konfirmasi terbit.
                                Sampel: <span id="konfirmN">-</span> judul.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="insightError" class="alert alert-danger d-none"></div>
    </div>

    {{-- ── Heatmap Provinsi ── --}}
    @if($isPerpusnas ?? true)
    <div class="mt-4" id="heatmapSection">
        <h5 class="fw-bold text-primary border-bottom pb-2 mb-3">
            🗺️ Heatmap Kepatuhan per Provinsi
        </h5>
        <div id="heatmapLoading" class="text-center py-3 text-muted">
            <div class="spinner-border spinner-border-sm me-1"></div> Memuat data provinsi...
        </div>
        <div id="heatmapContent" class="d-none">
            <div class="row g-3">
                <div class="col-lg-7">
                    <div class="card shadow-sm h-100">
                        <div class="card-header fw-semibold" style="font-size:.9rem">Ranking Kepatuhan KCKR per Provinsi</div>
                        <div class="card-body" style="overflow-y:auto;max-height:460px">
                            <canvas id="provinsiChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card shadow-sm h-100">
                        <div class="card-header fw-semibold" style="font-size:.9rem">Tile Map Provinsi</div>
                        <div class="card-body" style="overflow-y:auto;max-height:460px">
                            <div id="provinsiTiles" class="d-flex flex-wrap gap-2"></div>
                            <div class="mt-3 d-flex flex-wrap gap-2" style="font-size:.75rem">
                                <span><span style="display:inline-block;width:12px;height:12px;background:#dc3545;border-radius:2px"></span> 0–20%</span>
                                <span><span style="display:inline-block;width:12px;height:12px;background:#fd7e14;border-radius:2px"></span> 21–40%</span>
                                <span><span style="display:inline-block;width:12px;height:12px;background:#ffc107;border-radius:2px"></span> 41–60%</span>
                                <span><span style="display:inline-block;width:12px;height:12px;background:#0dcaf0;border-radius:2px"></span> 61–80%</span>
                                <span><span style="display:inline-block;width:12px;height:12px;background:#198754;border-radius:2px"></span> 81–100%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="heatmapError" class="alert alert-danger d-none"></div>
    </div>
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
        const rekLabels = ['Blokir Konfirmasi Terbit', 'Blokir SS KCKR', 'Blokir Konfirm + SSKCKR', 'Baik'];
        const rekData   = [{{ $blokirTerbit ?? 0 }}, {{ $blokirKckr ?? 0 }}, {{ $blokirKeduanya ?? 0 }}, {{ $baik ?? 0 }}];
        const rekColors = ['#dc3545', '#fd7e14', '#6f42c1', '#198754'];
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
        params.set('start_date', p.get('start_date') || '{{ request("start_date", "2021-01-01") }}');
        params.set('end_date',   p.get('end_date')   || '{{ request("end_date",   date("Y-m-d")) }}');
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

document.addEventListener('DOMContentLoaded', () => {
    loadChart();
    loadPrediksi();
    loadBreakdown();
    loadInsight();
    loadHeatmap();
});

// ── Prediksi Blokir ──────────────────────────────────────────────────────────
let prediksiChartInstance = null;
function loadPrediksi() {
    const predSec = document.getElementById('prediksiSection');
    if (!predSec) return;

    const p = new URLSearchParams(window.location.search);
    const params = new URLSearchParams();
    @if(!empty($provinceIds))
        @foreach($provinceIds as $pid)
            params.append('province_ids[]', '{{ $pid }}');
        @endforeach
    @endif
    params.set('kckr_mode', p.get('kckr_mode') || 'perpusnas');

    fetch('{{ route("dashboard_compliance.prediksi") }}?' + params.toString())
        .then(r => r.json())
        .then(res => {
            document.getElementById('prediksiLoading').classList.add('d-none');
            if (res.error) {
                document.getElementById('prediksiError').textContent = res.error;
                document.getElementById('prediksiError').classList.remove('d-none');
                return;
            }
            document.getElementById('predD30').textContent = res.d30.toLocaleString('id');
            document.getElementById('predD60').textContent = res.d60.toLocaleString('id');
            document.getElementById('predD90').textContent = res.d90.toLocaleString('id');
            document.getElementById('prediksiContent').classList.remove('d-none');

            if (prediksiChartInstance) prediksiChartInstance.destroy();
            prediksiChartInstance = new Chart(document.getElementById('prediksiChart'), {
                type: 'bar',
                data: {
                    labels: res.timelineLabels,
                    datasets: [{
                        label: 'Penerbit masuk blokir',
                        data: res.timelineData,
                        backgroundColor: ctx => {
                            const v = ctx.raw;
                            if (v >= 10) return '#dc3545';
                            if (v >= 5)  return '#fd7e14';
                            if (v >= 1)  return '#ffc107';
                            return '#e9ecef';
                        },
                        borderRadius: 3,
                        datalabels: {
                            display: ctx => ctx.raw > 0,
                            anchor: 'end', align: 'top', clip: false,
                            color: '#333', font: { size: 9, weight: 'bold' },
                            formatter: v => v,
                        }
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    layout: { padding: { top: 18 } },
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 10 }, maxRotation: 0 } },
                        y: { beginAtZero: true, grace: '20%', ticks: { stepSize: 1, font: { size: 10 } } }
                    }
                }
            });
        })
        .catch(() => {
            document.getElementById('prediksiLoading').classList.add('d-none');
            document.getElementById('prediksiError').textContent = 'Gagal memuat data prediksi.';
            document.getElementById('prediksiError').classList.remove('d-none');
        });
}

// ── Breakdown Kategori/Media + Top 10 Hutang ─────────────────────────────────
const bdCharts = {};
function buildFilterParams() {
    const p = new URLSearchParams(window.location.search);
    const params = new URLSearchParams();
    const ft = p.get('filter_type') || '{{ $dateFilter["type"] ?? "tahun" }}';
    params.set('filter_type', ft);
    if (ft === 'tahun') {
        params.set('filter_year', p.get('filter_year') || '{{ request("filter_year", date("Y")) }}');
    } else if (ft === 'bulan') {
        params.set('filter_year',  p.get('filter_year')  || '{{ request("filter_year",  date("Y")) }}');
        params.set('filter_month', p.get('filter_month') || '{{ request("filter_month", date("n")) }}');
    } else {
        params.set('start_date', p.get('start_date') || '{{ request("start_date", "2021-01-01") }}');
        params.set('end_date',   p.get('end_date')   || '{{ request("end_date",   date("Y-m-d")) }}');
    }
    @if(!empty($provinceIds))
        @foreach($provinceIds as $pid)
            params.append('province_ids[]', '{{ $pid }}');
        @endforeach
    @endif
    params.set('kckr_mode', p.get('kckr_mode') || 'perpusnas');
    return params;
}

function renderGroupChart(canvasId, legendId, rows) {
    const labels = rows.map(r => r.grp);
    if (bdCharts[canvasId]) bdCharts[canvasId].destroy();

    // 100% stacked: gambar proporsi persen agar grup kecil tetap terbaca,
    // angka absolut ditampilkan di label & tooltip
    const totals   = rows.map(r => r.sudah + r.belum);
    const sudahPct = rows.map((r, i) => totals[i] > 0 ? r.sudah / totals[i] * 100 : 0);
    const belumPct = rows.map((r, i) => totals[i] > 0 ? r.belum / totals[i] * 100 : 0);

    bdCharts[canvasId] = new Chart(document.getElementById(canvasId), {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Sudah KCKR', data: sudahPct,
                    backgroundColor: '#1baf7a', borderRadius: 4,
                    datalabels: {
                        color: '#fff', font: { size: 11, weight: 'bold' },
                        formatter: (v, ctx) => v >= 8
                            ? rows[ctx.dataIndex].sudah.toLocaleString('id') + ' (' + v.toFixed(1) + '%)' : '',
                    }
                },
                {
                    label: 'Belum KCKR', data: belumPct,
                    backgroundColor: '#fd7e14', borderRadius: 4,
                    datalabels: {
                        color: '#fff', font: { size: 11, weight: 'bold' },
                        formatter: (v, ctx) => v >= 8
                            ? rows[ctx.dataIndex].belum.toLocaleString('id') + ' (' + v.toFixed(1) + '%)' : '',
                    }
                },
            ]
        },
        options: {
            indexAxis: 'y',
            responsive: true, maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12 } },
                tooltip: {
                    callbacks: {
                        label: ctx => {
                            const r   = rows[ctx.dataIndex];
                            const abs = ctx.datasetIndex === 0 ? r.sudah : r.belum;
                            return ` ${ctx.dataset.label}: ${abs.toLocaleString('id')} judul (${ctx.parsed.x.toFixed(1)}%)`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    stacked: true, min: 0, max: 100,
                    ticks: { font: { size: 10 }, callback: v => v + '%' }
                },
                y: { stacked: true, ticks: { font: { size: 12 } } }
            }
        }
    });
    document.getElementById(legendId).innerHTML = rows.map(r =>
        `<span><strong>${r.grp}</strong>: ${r.penerbit.toLocaleString('id')} penerbit · ${r.judul.toLocaleString('id')} judul ·
         <span style="color:${r.pct >= 61 ? '#198754' : r.pct >= 41 ? '#d9a406' : '#dc3545'};font-weight:600">${r.pct}% patuh</span></span>`
    ).join('');
}

function loadBreakdown() {
    const sec = document.getElementById('breakdownSection');
    if (!sec) return;

    fetch('{{ route("dashboard_compliance.breakdown") }}?' + buildFilterParams().toString())
        .then(r => r.json())
        .then(res => {
            document.getElementById('breakdownLoading').classList.add('d-none');
            if (res.error) {
                document.getElementById('breakdownError').textContent = res.error;
                document.getElementById('breakdownError').classList.remove('d-none');
                return;
            }
            document.getElementById('breakdownContent').classList.remove('d-none');

            renderGroupChart('kategoriChart', 'kategoriLegend', res.kategori);
            renderGroupChart('mediaChart', 'mediaLegend', res.media);

            if (res.ecol) {
                document.getElementById('ecolReview').textContent     = res.ecol.in_review.toLocaleString('id');
                document.getElementById('ecolDiterima').textContent   = res.ecol.diterima.toLocaleString('id');
                document.getElementById('ecolBermasalah').textContent = res.ecol.bermasalah.toLocaleString('id');
                document.getElementById('ecolStrip').classList.remove('d-none');
            }

            const tbody = document.getElementById('top10Body');
            if (!res.top10.length) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-3">Tidak ada hutang KCKR pada periode ini 🎉</td></tr>';
                return;
            }
            const detailBase = '{{ url("coaching-supervision/compliance-v3/detail") }}/';
            tbody.innerHTML = res.top10.map((r, i) => {
                const pctColor = r.pct >= 61 ? '#198754' : r.pct >= 41 ? '#d9a406' : '#dc3545';
                return `<tr>
                    <td class="ps-3 text-muted">${i + 1}</td>
                    <td><a href="${detailBase}${r.id}" class="text-decoration-none fw-semibold">${r.nama ?? '-'}</a></td>
                    <td class="text-end">${r.judul.toLocaleString('id')}</td>
                    <td class="text-end text-success">${r.sudah.toLocaleString('id')}</td>
                    <td class="text-end fw-bold" style="color:#fd7e14">${r.hutang.toLocaleString('id')}</td>
                    <td class="text-end pe-3">
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            <div class="progress flex-grow-1" style="height:6px;max-width:90px">
                                <div class="progress-bar" style="width:${r.pct}%;background:${pctColor}"></div>
                            </div>
                            <span style="color:${pctColor};font-weight:600;min-width:44px;text-align:right">${r.pct}%</span>
                        </div>
                    </td>
                </tr>`;
            }).join('');
        })
        .catch(() => {
            document.getElementById('breakdownLoading').classList.add('d-none');
            document.getElementById('breakdownError').textContent = 'Gagal memuat data breakdown.';
            document.getElementById('breakdownError').classList.remove('d-none');
        });
}

// ── Insight: Perbandingan Periode + Aging + Lama Konfirmasi ──────────────────
let agingChartInstance = null;
function loadInsight() {
    const sec = document.getElementById('insightSection');
    if (!sec) return;

    fetch('{{ route("dashboard_compliance.insight") }}?' + buildFilterParams().toString())
        .then(r => r.json())
        .then(res => {
            document.getElementById('insightLoading').classList.add('d-none');
            if (res.error) {
                document.getElementById('insightError').textContent = res.error;
                document.getElementById('insightError').classList.remove('d-none');
                return;
            }
            document.getElementById('insightContent').classList.remove('d-none');

            // ── Perbandingan periode ──
            document.getElementById('prevPeriodLabel').textContent = 'Dibandingkan: ' + res.prev_label;
            const rowsCompare = [
                { label: 'Penerbit Aktif', cur: res.current.penerbit, prev: res.previous.penerbit },
                { label: 'Judul ISBN',     cur: res.current.judul,    prev: res.previous.judul },
                { label: 'Sudah KCKR',     cur: res.current.sudah,    prev: res.previous.sudah },
                { label: '% Kepatuhan',    cur: res.current.pct,      prev: res.previous.pct, pct: true },
            ];
            document.getElementById('periodCompare').innerHTML = rowsCompare.map(r => {
                const diff = r.cur - r.prev;
                const diffPct = r.prev > 0 ? (diff / r.prev * 100) : (r.cur > 0 ? 100 : 0);
                const up   = diff > 0, flat = diff === 0;
                const arrow = flat ? '▬' : (up ? '▲' : '▼');
                const color = flat ? '#6c757d' : (up ? '#198754' : '#dc3545');
                const fmt   = v => r.pct ? v.toFixed(1) + '%' : v.toLocaleString('id');
                const diffTxt = r.pct
                    ? (diff >= 0 ? '+' : '') + diff.toFixed(1) + ' poin'
                    : (diff >= 0 ? '+' : '') + diff.toLocaleString('id') + ' (' + (diffPct >= 0 ? '+' : '') + diffPct.toFixed(1) + '%)';
                return `<div class="d-flex justify-content-between align-items-center py-2 border-bottom" style="font-size:.85rem">
                    <div>
                        <div class="fw-semibold">${r.label}</div>
                        <small class="text-muted">sebelumnya: ${fmt(r.prev)}</small>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold" style="font-size:1.05rem">${fmt(r.cur)}</div>
                        <small style="color:${color};font-weight:600">${arrow} ${diffTxt}</small>
                    </div>
                </div>`;
            }).join('');

            // ── Aging chart ──
            const agingColors = ['#ffc107', '#fd7e14', '#e35d6a', '#dc3545'];
            if (agingChartInstance) agingChartInstance.destroy();
            agingChartInstance = new Chart(document.getElementById('agingChart'), {
                type: 'bar',
                data: {
                    labels: res.aging.map(a => a.label),
                    datasets: [{
                        data: res.aging.map(a => a.count),
                        backgroundColor: agingColors,
                        borderRadius: 5,
                        datalabels: {
                            anchor: 'end', align: 'top', clip: false,
                            color: '#333', font: { size: 11, weight: 'bold' },
                            formatter: v => v > 0 ? v.toLocaleString('id') : '',
                        }
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    layout: { padding: { top: 20 } },
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                        y: {
                            beginAtZero: true, grace: '15%',
                            ticks: {
                                font: { size: 10 },
                                callback: v => v >= 1000 ? (v/1000).toFixed(1).replace('.0','') + 'rb' : v
                            }
                        }
                    }
                }
            });

            // ── Lama konfirmasi terbit ──
            document.getElementById('konfirmMedian').textContent = res.konfirmasi.n > 0 ? res.konfirmasi.median.toLocaleString('id') : '—';
            document.getElementById('konfirmAvg').textContent    = res.konfirmasi.n > 0 ? res.konfirmasi.avg.toLocaleString('id')    : '—';
            document.getElementById('konfirmN').textContent      = res.konfirmasi.n.toLocaleString('id');
        })
        .catch(() => {
            document.getElementById('insightLoading').classList.add('d-none');
            document.getElementById('insightError').textContent = 'Gagal memuat insight.';
            document.getElementById('insightError').classList.remove('d-none');
        });
}

// ── Heatmap Provinsi ─────────────────────────────────────────────────────────
let provinsiChartInstance = null;
function loadHeatmap() {
    const hmSec = document.getElementById('heatmapSection');
    if (!hmSec) return;

    const p = new URLSearchParams(window.location.search);
    const params = new URLSearchParams();
    const ft = p.get('filter_type') || '{{ $dateFilter["type"] ?? "tahun" }}';
    params.set('filter_type', ft);
    if (ft === 'tahun') {
        params.set('filter_year', p.get('filter_year') || '{{ request("filter_year", date("Y")) }}');
    } else if (ft === 'bulan') {
        params.set('filter_year',  p.get('filter_year')  || '{{ request("filter_year",  date("Y")) }}');
        params.set('filter_month', p.get('filter_month') || '{{ request("filter_month", date("n")) }}');
    } else {
        params.set('start_date', p.get('start_date') || '{{ request("start_date", "2021-01-01") }}');
        params.set('end_date',   p.get('end_date')   || '{{ request("end_date",   date("Y-m-d")) }}');
    }
    params.set('kckr_mode', p.get('kckr_mode') || 'perpusnas');
    @if(!empty($provinceIds))
        @foreach($provinceIds as $pid)
            params.append('province_ids[]', '{{ $pid }}');
        @endforeach
    @endif

    fetch('{{ route("dashboard_compliance.provinsi") }}?' + params.toString())
        .then(r => r.json())
        .then(res => {
            document.getElementById('heatmapLoading').classList.add('d-none');
            if (res.error) {
                document.getElementById('heatmapError').textContent = res.error;
                document.getElementById('heatmapError').classList.remove('d-none');
                return;
            }
            const rows   = res.rows || res;
            const isKota = res.mode === 'kota';
            const unit   = isKota ? 'Kabupaten/Kota' : 'Provinsi';
            const hmTitle = document.querySelector('#heatmapSection h5');
            if (hmTitle) hmTitle.innerHTML = '🗺️ Heatmap Kepatuhan per ' + unit;
            const hdrs = document.querySelectorAll('#heatmapContent .card-header');
            if (hdrs[0]) hdrs[0].textContent = 'Ranking Kepatuhan KCKR per ' + unit;
            if (hdrs[1]) hdrs[1].textContent = 'Tile Map ' + unit;
            document.getElementById('heatmapContent').classList.remove('d-none');

            const pctColor = pct => {
                if (pct <= 20) return '#dc3545';
                if (pct <= 40) return '#fd7e14';
                if (pct <= 60) return '#ffc107';
                if (pct <= 80) return '#0dcaf0';
                return '#198754';
            };
            const textColor = pct => (pct > 20 && pct <= 60) ? '#000' : '#fff';

            // Tile map
            const tilesEl = document.getElementById('provinsiTiles');
            tilesEl.innerHTML = '';
            [...rows].sort((a, b) => b.avg_kckr - a.avg_kckr).forEach(row => {
                const bg  = pctColor(row.avg_kckr);
                const fg  = textColor(row.avg_kckr);
                const blokir = row.blokir_terbit + row.blokir_kckr + row.blokir_keduanya;
                const tile = document.createElement('div');
                tile.title = `${row.nama}\n${row.total_penerbit} penerbit | ${row.avg_kckr}% KCKR\nBlokir: ${blokir} | Baik: ${row.baik}`;
                tile.style.cssText = `background:${bg};color:${fg};border-radius:8px;padding:6px 10px;font-size:.75rem;cursor:default;min-width:110px;text-align:center`;
                tile.innerHTML = `<div style="font-weight:600;font-size:.7rem;line-height:1.2">${row.nama}</div>
                    <div style="font-size:1rem;font-weight:bold">${row.avg_kckr}%</div>
                    <div style="font-size:.65rem;opacity:.85">${row.total_penerbit} penerbit</div>`;
                tilesEl.appendChild(tile);
            });

            // Horizontal bar chart (sorted ascending by KCKR)
            const labels  = rows.map(r => r.nama);
            const values  = rows.map(r => r.avg_kckr);
            const bgColors = values.map(v => pctColor(v));

            if (provinsiChartInstance) provinsiChartInstance.destroy();

            const chartEl = document.getElementById('provinsiChart');
            chartEl.height = Math.max(300, rows.length * 22);

            provinsiChartInstance = new Chart(chartEl, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Rata-rata Kepatuhan KCKR (%)',
                        data: values,
                        backgroundColor: bgColors,
                        borderRadius: 4,
                        datalabels: {
                            anchor: 'end', align: 'right', clip: false,
                            color: '#333', font: { size: 10, weight: 'bold' },
                            formatter: v => v + '%',
                        }
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true, maintainAspectRatio: false,
                    layout: { padding: { right: 40 } },
                    plugins: { legend: { display: false } },
                    scales: {
                        x: {
                            beginAtZero: true, max: 110,
                            ticks: { callback: v => v + '%', font: { size: 10 } }
                        },
                        y: { ticks: { font: { size: 10 } } }
                    }
                }
            });
        })
        .catch(() => {
            document.getElementById('heatmapLoading').classList.add('d-none');
            document.getElementById('heatmapError').textContent = 'Gagal memuat data provinsi.';
            document.getElementById('heatmapError').classList.remove('d-none');
        });
}
// ─────────────────────────────────────────────────────────────────────────────

function setDashKckrMode(mode) {
    document.getElementById('btnDashPerpusnas').className = 'btn ' + (mode === 'perpusnas' ? 'btn-primary' : 'btn-outline-primary');
    document.getElementById('btnDashProvinsi').className  = 'btn ' + (mode === 'provinsi'  ? 'btn-success' : 'btn-outline-success');
    const url = new URL(window.location.href);
    url.searchParams.set('kckr_mode', mode);
    window.location.href = url.toString();
}
// ─────────────────────────────────────────────────────────────────────────────

function downloadPDF() {
    const btn = document.querySelector('button[onclick="downloadPDF()"]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memproses...';

    const el = document.getElementById('dashboardContent');

    const pdfHeader = document.getElementById('pdfHeader');
    pdfHeader.style.display = 'block';
    const hidden = el.querySelectorAll('.btn, form, #dashFilterCard');
    hidden.forEach(e => e.style.display = 'none');

    // Buka semua scrollable container agar html2canvas capture full content
    const scrollables = el.querySelectorAll('[style*="overflow"][style*="max-height"], [style*="max-height"][style*="overflow"]');
    const scrollableStates = [];
    scrollables.forEach(s => {
        scrollableStates.push({ el: s, maxHeight: s.style.maxHeight, overflow: s.style.overflow, overflowY: s.style.overflowY });
        s.style.maxHeight = 'none';
        s.style.overflow  = 'visible';
        s.style.overflowY = 'visible';
    });

    const SCALE = 2;

    html2canvas(el, {
        scale: SCALE,
        useCORS: true,
        allowTaint: true,
        logging: false,
        backgroundColor: '#ffffff',
        windowWidth: 1700,
        onclone: (doc) => {
            doc.querySelectorAll('[style]').forEach(s => {
                s.style.maxHeight = 'none';
                s.style.overflow  = 'visible';
                s.style.overflowY = 'visible';
            });
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
        scrollableStates.forEach(s => {
            s.el.style.maxHeight = s.maxHeight;
            s.el.style.overflow  = s.overflow;
            s.el.style.overflowY = s.overflowY;
        });

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
        btn.innerHTML = '<i class="ph-file-pdf"></i> Download PDF';
    }).catch(err => {
        pdfHeader.style.display = 'none';
        hidden.forEach(e => e.style.display = '');
        scrollableStates.forEach(s => {
            s.el.style.maxHeight = s.maxHeight;
            s.el.style.overflow  = s.overflow;
            s.el.style.overflowY = s.overflowY;
        });
        console.error(err);
        alert('Gagal: ' + err.message);
        btn.disabled = false;
        btn.innerHTML = '<i class="ph-file-pdf"></i> Download PDF';
    });
}
</script>
@endsection
