<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Administrasi Sistem &rsaquo; <span class="fw-normal">Pengaturan Kepatuhan KCKR</span>
            </h4>
        </div>
    </div>
</div>

<div class="content pt-0">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
        <i class="ph-check-circle fs-5"></i>
        {{ session('success') }}
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form method="POST" action="{{ route('compliance_setting.save') }}">
        @csrf

        <div class="row g-4">

            {{-- ── Konfirmasi Terbit ───────────────────────────────────────── --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom bg-primary bg-opacity-10">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ph-calendar-check text-primary fs-5"></i>
                            <h6 class="mb-0 fw-semibold text-primary">Tenggat Waktu Konfirmasi Terbit</h6>
                        </div>
                        <small class="text-muted">
                            Jumlah hari setelah ISBN didaftarkan — penerbit wajib mengonfirmasi terbit sebelum batas ini.
                        </small>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="ph-book me-1 text-primary"></i>
                                    Karya Cetak
                                    <span class="text-muted fw-normal ms-1" style="font-size:.8rem">(BatasWaktuKonfirmasiTerbitKaryaCetak)</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('BatasWaktuKonfirmasiTerbitKaryaCetak') is-invalid @enderror"
                                           name="BatasWaktuKonfirmasiTerbitKaryaCetak"
                                           value="{{ old('BatasWaktuKonfirmasiTerbitKaryaCetak', $params['BatasWaktuKonfirmasiTerbitKaryaCetak']) }}"
                                           min="1" max="3650">
                                    <span class="input-group-text">hari</span>
                                </div>
                                @error('BatasWaktuKonfirmasiTerbitKaryaCetak')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    Saat ini: <strong>{{ $params['BatasWaktuKonfirmasiTerbitKaryaCetak'] }} hari</strong>
                                    (≈ {{ round($params['BatasWaktuKonfirmasiTerbitKaryaCetak'] / 30, 1) }} bulan)
                                </small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="ph-monitor-play me-1 text-info"></i>
                                    Karya Rekam / Digital
                                    <span class="text-muted fw-normal ms-1" style="font-size:.8rem">(BatasWaktuKonfirmasiTerbitDigital)</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('BatasWaktuKonfirmasiTerbitDigital') is-invalid @enderror"
                                           name="BatasWaktuKonfirmasiTerbitDigital"
                                           value="{{ old('BatasWaktuKonfirmasiTerbitDigital', $params['BatasWaktuKonfirmasiTerbitDigital']) }}"
                                           min="1" max="3650">
                                    <span class="input-group-text">hari</span>
                                </div>
                                @error('BatasWaktuKonfirmasiTerbitDigital')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    Saat ini: <strong>{{ $params['BatasWaktuKonfirmasiTerbitDigital'] }} hari</strong>
                                    (≈ {{ round($params['BatasWaktuKonfirmasiTerbitDigital'] / 30, 1) }} bulan)
                                </small>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Teguran ─────────────────────────────────────────────────── --}}
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header border-bottom bg-warning bg-opacity-10">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ph-warning text-warning fs-5"></i>
                            <h6 class="mb-0 fw-semibold text-warning">Batas Waktu Teguran Konfirmasi Terbit</h6>
                        </div>
                        <small class="text-muted">
                            Tambahan hari setelah tenggat konfirmasi terbit — penerbit masuk status "Lewat Teguran".
                        </small>
                    </div>
                    <div class="card-body">
                        <label class="form-label fw-semibold">
                            <i class="ph-bell-ringing me-1 text-warning"></i>
                            Toleransi Teguran
                            <span class="text-muted fw-normal ms-1" style="font-size:.8rem">(BatasWaktuTeguranKonfirmasiTerbit)</span>
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control @error('BatasWaktuTeguranKonfirmasiTerbit') is-invalid @enderror"
                                   name="BatasWaktuTeguranKonfirmasiTerbit"
                                   value="{{ old('BatasWaktuTeguranKonfirmasiTerbit', $params['BatasWaktuTeguranKonfirmasiTerbit']) }}"
                                   min="1" max="365">
                            <span class="input-group-text">hari</span>
                        </div>
                        @error('BatasWaktuTeguranKonfirmasiTerbit')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            Saat ini: <strong>{{ $params['BatasWaktuTeguranKonfirmasiTerbit'] }} hari</strong> setelah tenggat
                        </small>

                        <div class="alert alert-warning border-0 mt-3 py-2 px-3" style="font-size:.82rem">
                            <i class="ph-info me-1"></i>
                            Penerbit lewat teguran akan direkomendasikan <strong>Blokir Konfirmasi Terbit</strong>.
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Threshold Blokir KCKR ───────────────────────────────────── --}}
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header border-bottom bg-danger bg-opacity-10">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ph-shield-warning text-danger fs-5"></i>
                            <h6 class="mb-0 fw-semibold text-danger">Threshold Auto Blokir KCKR</h6>
                        </div>
                        <small class="text-muted">
                            Penerbit dengan % kepatuhan KCKR di bawah nilai ini dan ada keterlambatan
                            akan direkomendasikan <strong>Blokir SS KCKR</strong>.
                        </small>
                    </div>
                    <div class="card-body">
                        <label class="form-label fw-semibold">
                            <i class="ph-percent me-1 text-danger"></i>
                            Minimum % Kepatuhan KCKR
                            <span class="text-muted fw-normal ms-1" style="font-size:.8rem">(BatasMinimumKepatuhanKCKR)</span>
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control @error('BatasMinimumKepatuhanKCKR') is-invalid @enderror"
                                   name="BatasMinimumKepatuhanKCKR" id="inputThreshold"
                                   value="{{ old('BatasMinimumKepatuhanKCKR', $params['BatasMinimumKepatuhanKCKR']) }}"
                                   min="1" max="100" oninput="updateThresholdBar(this.value)">
                            <span class="input-group-text">%</span>
                        </div>
                        @error('BatasMinimumKepatuhanKCKR')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-1" style="font-size:.78rem">
                                <span class="text-danger fw-semibold">Blokir</span>
                                <span id="thresholdLabel" class="fw-bold">{{ $params['BatasMinimumKepatuhanKCKR'] }}%</span>
                                <span class="text-success fw-semibold">Aman</span>
                            </div>
                            <div class="progress" style="height:12px">
                                <div class="progress-bar bg-danger" id="thresholdBar"
                                     style="width:{{ $params['BatasMinimumKepatuhanKCKR'] }}%"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-1" style="font-size:.72rem;color:#aaa">
                                <span>0%</span><span>50%</span><span>100%</span>
                            </div>
                        </div>

                        <div class="alert alert-danger border-0 mt-3 py-2 px-3" style="font-size:.82rem">
                            <i class="ph-info me-1"></i>
                            Penerbit dengan kepatuhan KCKR &lt; <strong id="thresholdHint">{{ $params['BatasMinimumKepatuhanKCKR'] }}%</strong>
                            dan ada keterlambatan akan direkomendasikan <strong>Blokir SS KCKR</strong>.
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Ringkasan Konfigurasi ────────────────────────────────────── --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-light">
                    <div class="card-body py-3">
                        <h6 class="fw-semibold mb-3"><i class="ph-info me-1 text-primary"></i> Ringkasan Konfigurasi Aktif</h6>
                        <div class="row g-3 text-center">
                            <div class="col-md-3">
                                <div class="bg-white rounded border p-3">
                                    <div class="fs-4 fw-bold text-primary">{{ $params['BatasWaktuKonfirmasiTerbitKaryaCetak'] }}</div>
                                    <small class="text-muted">Hari — Tenggat KC</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-white rounded border p-3">
                                    <div class="fs-4 fw-bold text-info">{{ $params['BatasWaktuKonfirmasiTerbitDigital'] }}</div>
                                    <small class="text-muted">Hari — Tenggat KR</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-white rounded border p-3">
                                    <div class="fs-4 fw-bold text-warning">{{ $params['BatasWaktuTeguranKonfirmasiTerbit'] }}</div>
                                    <small class="text-muted">Hari — Toleransi Teguran</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="bg-white rounded border p-3">
                                    <div class="fs-4 fw-bold text-danger">{{ $params['BatasMinimumKepatuhanKCKR'] }}%</div>
                                    <small class="text-muted">Threshold Blokir KCKR</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Tombol Simpan ────────────────────────────────────────────── --}}
            <div class="col-12">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                        <i class="ph-arrow-counter-clockwise me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="ph-floppy-disk me-1"></i> Simpan Pengaturan
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
function updateThresholdBar(val) {
    val = Math.min(100, Math.max(0, parseInt(val) || 0));
    document.getElementById('thresholdBar').style.width  = val + '%';
    document.getElementById('thresholdLabel').textContent = val + '%';
    document.getElementById('thresholdHint').textContent  = val + '%';
}
</script>
