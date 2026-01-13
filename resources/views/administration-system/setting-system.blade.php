<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Administrasi Sistem - <span class="fw-normal">Pengaturan Sistem</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <span class="badge bg-primary p-2 bg-opacity-10 text-primary">
                    Konfigurasi Sistem
                </span>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <div class="d-flex align-items-center">
                <i class="ph-warning-circle me-2 fs-4"></i>
                <div>
                    <h6 class="mb-1">Terdapat Kesalahan!</h6>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{!! $error !!}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @elseif(session('success'))
        <div class="alert bg-success text-white alert-dismissible fade show border-0">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <div class="d-flex align-items-center">
                <i class="ph-check-circle me-2 fs-4"></i>
                <div>{{ session('success') }}</div>
            </div>
        </div>
    @elseif(session('failed'))
        <div class="alert bg-danger text-white alert-dismissible fade show border-0">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <div class="d-flex align-items-center">
                <i class="ph-x-circle me-2 fs-4"></i>
                <div>{{ session('failed') }}</div>
            </div>
        </div>
    @endif
    <form action="{{ url('administration-system/setting-system/submitted') }}" method="POST" onsubmit="onLoading('show', 'body')">
        @csrf
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <ul class="nav nav-tabs nav-tabs-highlight nav-justified mb-0">
                    <li class="nav-item">
                        <a href="#nav-tabs-system" class="nav-link active" data-bs-toggle="tab">
                            <i class="ph-gear me-2"></i>
                            Sistem
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#nav-tabs-email" class="nav-link" data-bs-toggle="tab">
                            <i class="ph-envelope me-2"></i>
                            Email
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#nav-tabs-collection" class="nav-link" data-bs-toggle="tab">
                            <i class="ph-books me-2"></i>
                            Koleksi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#nav-tabs-captcha" class="nav-link" data-bs-toggle="tab">
                            <i class="ph-shield-check me-2"></i>
                            Captcha
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#nav-tabs-whatsapp" class="nav-link" data-bs-toggle="tab">
                            <i class="ph-whatsapp-logo me-2"></i>
                            WhatsApp
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#nav-tabs-api-isbn" class="nav-link" data-bs-toggle="tab">
                            <i class="ph-barcode me-2"></i>
                            API ISBN
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#nav-tabs-api-ro" class="nav-link" data-bs-toggle="tab">
                            <i class="ph-truck me-2"></i>
                            API Pengiriman
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="nav-tabs-system">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="ph-gear-six me-2 text-primary"></i>
                            <h6 class="mb-0 fw-semibold">Pengaturan Umum</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-lock-key me-1"></i>
                                Percobaan Login
                            </label>
                            <div class="col-lg-9">
                                <div class="input-group">
                                    <span class="input-group-text">Attempt</span>
                                    <input type="number" class="form-control" name="system_rate_limiter" id="system_rate_limiter" value="{{ $settingParameter->firstWhere('NAME', 'EPercobaanLogin')->VALUE ?? '' }}" placeholder="Jumlah percobaan">
                                    <span class="input-group-text">Interval (Jam)</span>
                                    <input type="number" class="form-control" name="system_rate_limiter_interval" id="system_rate_limiter_interval" value="{{ $settingParameter->firstWhere('NAME', 'EPercobaanLoginInterval')->VALUE ?? '' }}" placeholder="Interval waktu">
                                </div>
                                <div class="form-text">Batasan jumlah percobaan login dan interval waktu pemblokiran</div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-key me-1"></i>
                                AES Encryption
                            </label>
                            <div class="col-lg-9">
                                <div class="input-group">
                                    <span class="input-group-text">Key</span>
                                    <input type="text" class="form-control" name="system_aes_key" id="system_aes_key" value="{{ $settingParameter->firstWhere('NAME', 'EAesKey')->VALUE ?? '' }}" placeholder="AES Key">
                                    <span class="input-group-text">IV</span>
                                    <input type="text" class="form-control" name="system_aes_iv" id="system_aes_iv" value="{{ $settingParameter->firstWhere('NAME', 'EAesIV')->VALUE ?? '' }}" placeholder="AES IV">
                                </div>
                                <div class="form-text">Kunci enkripsi AES untuk keamanan data</div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-key me-1"></i>
                                AES INLIS
                            </label>
                            <div class="col-lg-9">
                                <div class="input-group">
                                    <span class="input-group-text">Key</span>
                                    <input type="text" class="form-control" name="system_aes_key_inlis" id="system_aes_key_inlis" value="{{ $settingParameter->firstWhere('NAME', 'EAesInlisKey')->VALUE ?? '' }}" placeholder="AES INLIS Key">
                                    <span class="input-group-text">IV</span>
                                    <input type="text" class="form-control" name="system_aes_iv_inlis" id="system_aes_iv_inlis" value="{{ $settingParameter->firstWhere('NAME', 'EAesInlisIV')->VALUE ?? '' }}" placeholder="AES INLIS IV">
                                </div>
                                <div class="form-text">Kunci enkripsi AES khusus untuk sistem INLIS</div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-browser me-1"></i>
                                Allow IFrame Domain
                            </label>
                            <div class="col-lg-9">
                                <input type="url" class="form-control" name="system_allow_iframe_domain" id="system_allow_iframe_domain" value="{{ $settingParameter->firstWhere('NAME', 'EIFrameDomain')->VALUE ?? '' }}" placeholder="https://example.com">
                                <div class="form-text">Domain yang diizinkan untuk menampilkan aplikasi dalam iframe</div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-arrow-counter-clockwise me-1"></i>
                                Batas Berlaku Reset Password
                            </label>
                            <div class="col-lg-9">
                                <div class="input-group">
                                    <input type="number" class="form-control" name="system_limit_reset_password" id="system_limit_reset_password" value="{{ $settingParameter->firstWhere('NAME', 'EBatasResetPassword')->VALUE ?? '' }}" placeholder="Durasi dalam jam">
                                    <span class="input-group-text">Jam</span>
                                </div>
                                <div class="form-text">Masa berlaku link reset password</div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-file-cloud me-1"></i>
                                Batas Berlaku File Original
                            </label>
                            <div class="col-lg-9">
                                <div class="input-group">
                                    <input type="number" class="form-control" name="system_limit_file_original" id="system_limit_file_original" value="{{ $settingParameter->firstWhere('NAME', 'EBatasFileOriginal')->VALUE ?? '' }}" placeholder="Durasi dalam hari">
                                    <span class="input-group-text">Hari</span>
                                </div>
                                <div class="form-text">Masa penyimpanan file original di server</div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-calendar-check me-1"></i>
                                Tgl Mulai Kepatuhan Penerbit
                            </label>
                            <div class="col-lg-9">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ph-calendar-blank"></i>
                                    </span>
                                    <input type="text" class="form-control datepicker-single" name="executor_start_date" id="executor_start_date" value="{{ $settingParameter->firstWhere('NAME', 'ETglKepatuhanPenerbit')->VALUE ?? '' }}" placeholder="Pilih Tanggal" readonly>
                                </div>
                                <div class="form-text">Tanggal mulai perhitungan kepatuhan penerbit</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="ph-database me-2 text-primary"></i>
                            <h6 class="mb-0 fw-semibold">Konfigurasi Redis</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-plug me-1"></i>
                                Client
                            </label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="system_redis_client" id="system_redis_client" value="{{ $settingParameter->firstWhere('NAME', 'ERedisClient')->VALUE ?? '' }}" placeholder="Redis client type">
                                <div class="form-text">Tipe client Redis yang digunakan</div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-globe me-1"></i>
                                Host
                            </label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="system_redis_host" id="system_redis_host" value="{{ $settingParameter->firstWhere('NAME', 'ERedisHost')->VALUE ?? '' }}" placeholder="127.0.0.1">
                                <div class="form-text">Alamat host server Redis</div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-user me-1"></i>
                                Username
                            </label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="system_redis_username" id="system_redis_username" value="{{ $settingParameter->firstWhere('NAME', 'ERedisUsername')->VALUE ?? '' }}" placeholder="Redis username">
                                <div class="form-text">Username untuk autentikasi Redis (opsional)</div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-lock me-1"></i>
                                Password
                            </label>
                            <div class="col-lg-9">
                                <input type="password" class="form-control" name="system_redis_password" id="system_redis_password" value="{{ $settingParameter->firstWhere('NAME', 'ERedisPassword')->VALUE ?? '' }}" placeholder="Redis password">
                                <div class="form-text">Password untuk autentikasi Redis (opsional)</div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-plugs me-1"></i>
                                Port
                            </label>
                            <div class="col-lg-9">
                                <input type="number" class="form-control" name="system_redis_port" id="system_redis_port" value="{{ $settingParameter->firstWhere('NAME', 'ERedisPort')->VALUE ?? '' }}" placeholder="6379">
                                <div class="form-text">Port server Redis (default: 6379)</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="ph-cookie me-2 text-primary"></i>
                            <h6 class="mb-0 fw-semibold">Konfigurasi Session</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-hard-drives me-1"></i>
                                Driver
                            </label>
                            <div class="col-lg-9">
                                <select class="form-select" name="system_session_driver" id="system_session_driver">
                                    <option value="file" {{ ($settingParameter->firstWhere('NAME', 'ESessionDriver')->VALUE ?? '') == 'file' ? 'selected' : '' }}>File</option>
                                    <option value="cookie" {{ ($settingParameter->firstWhere('NAME', 'ESessionDriver')->VALUE ?? '') == 'cookie' ? 'selected' : '' }}>Cookie</option>
                                    <option value="database" {{ ($settingParameter->firstWhere('NAME', 'ESessionDriver')->VALUE ?? '') == 'database' ? 'selected' : '' }}>Database</option>
                                    <option value="memcached" {{ ($settingParameter->firstWhere('NAME', 'ESessionDriver')->VALUE ?? '') == 'memcached' ? 'selected' : '' }}>Memcached</option>
                                    <option value="redis" {{ ($settingParameter->firstWhere('NAME', 'ESessionDriver')->VALUE ?? '') == 'redis' ? 'selected' : '' }}>Redis</option>
                                    <option value="array" {{ ($settingParameter->firstWhere('NAME', 'ESessionDriver')->VALUE ?? '') == 'array' ? 'selected' : '' }}>Array</option>
                                    <option value="dynamodb" {{ ($settingParameter->firstWhere('NAME', 'ESessionDriver')->VALUE ?? '') == 'dynamodb' ? 'selected' : '' }}>Dynamo DB</option>
                                </select>
                                <div class="form-text">Driver penyimpanan session</div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-timer me-1"></i>
                                Batas Waktu
                            </label>
                            <div class="col-lg-9">
                                <div class="input-group">
                                    <input type="number" class="form-control" name="system_session_lifetime" id="system_session_lifetime" value="{{ $settingParameter->firstWhere('NAME', 'ESessionLifeTime')->VALUE ?? '' }}" placeholder="Durasi session">
                                    <span class="input-group-text">Menit</span>
                                </div>
                                <div class="form-text">Durasi maksimal session sebelum expired</div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-shield-checkered me-1"></i>
                                Enkripsi Session
                            </label>
                            <div class="col-lg-9">
                                <select class="form-select" name="system_encryption" id="system_encryption">
                                    <option value="1" {{ ($settingParameter->firstWhere('NAME', 'ESessionEncrypt')->VALUE ?? '') == '1' ? 'selected' : '' }}>Ya</option>
                                    <option value="0" {{ ($settingParameter->firstWhere('NAME', 'ESessionEncrypt')->VALUE ?? '') == '0' ? 'selected' : '' }}>Tidak</option>
                                </select>
                                <div class="form-text">Aktifkan enkripsi untuk data session</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="nav-tabs-email">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="ph-envelope-simple me-2 text-primary"></i>
                            <h6 class="mb-0 fw-semibold">Konfigurasi Email SMTP</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-globe me-1"></i>
                                Host
                            </label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="mail_host" id="mail_host" value="{{ $mail->HOST ?? '' }}" placeholder="smtp.gmail.com">
                                <div class="form-text">Alamat server SMTP</div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-plugs me-1"></i>
                                Port
                            </label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="mail_port" id="mail_port" value="{{ $mail->PORT ?? '' }}" placeholder="587">
                                <div class="form-text">Port SMTP (587 untuk TLS, 465 untuk SSL)</div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-user me-1"></i>
                                Username
                            </label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="mail_username" id="mail_username" value="{{ $mail->CREDENTIALMAIL ?? '' }}" placeholder="email@example.com">
                                <div class="form-text">Username atau email untuk autentikasi SMTP</div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-lock me-1"></i>
                                Password
                            </label>
                            <div class="col-lg-9">
                                <input type="password" class="form-control" name="mail_password" id="mail_password" value="{{ $mail->CREDENTIALPASSWORD ?? '' }}" placeholder="••••••••">
                                <div class="form-text">Password untuk autentikasi SMTP</div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-envelope me-1"></i>
                                Dari Email
                            </label>
                            <div class="col-lg-9">
                                <input type="email" class="form-control" name="mail_from" id="mail_from" value="{{ $mail->MAILFROM ?? '' }}" placeholder="noreply@example.com">
                                <div class="form-text">Email pengirim default</div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-identification-card me-1"></i>
                                Atas Nama
                            </label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="mail_name" id="mail_name" value="{{ $mail->MAILDISPLAYNAME ?? '' }}" placeholder="Nama Pengirim">
                                <div class="form-text">Nama yang akan ditampilkan sebagai pengirim</div>
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="row">
                            <div class="col-lg-3"></div>
                            <div class="col-lg-9">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="form-group">
                                            <i class="ph-test-tube me-1"></i>
                                            Tes Pengiriman Email
                                        </h6>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="ph-envelope"></i>
                                            </span>
                                            <input type="email" class="form-control" name="dummy_email" id="dummy_email" value="admin@gmail.com" placeholder="Email tujuan tes">
                                            <button type="button" class="btn btn-primary" onclick="testSendEmail()">
                                                <i class="ph-paper-plane-right me-1"></i>
                                                Kirim Tes
                                            </button>
                                        </div>
                                        <div class="form-text mt-2">Kirim email percobaan untuk memastikan konfigurasi bekerja dengan baik</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="nav-tabs-collection">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="ph-book-open me-2 text-primary"></i>
                            <h6 class="mb-0 fw-semibold">Pengaturan Koleksi</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-upload-simple me-1"></i>
                                Batas Upload
                            </label>
                            <div class="col-lg-9">
                                <div class="input-group">
                                    <span class="input-group-text">Cover (MB)</span>
                                    <input type="number" class="form-control" name="catalog_cover" id="catalog_cover" value="{{ $settingParameter->firstWhere('NAME', 'EKatalogCoverMaxUpload')->VALUE ?? '' }}" placeholder="Ukuran maks">
                                    <span class="input-group-text">Konten (MB)</span>
                                    <input type="number" class="form-control" name="catalog_content" id="catalog_content" value="{{ $settingParameter->firstWhere('NAME', 'EKatalogContentMaxUpload')->VALUE ?? '' }}" placeholder="Ukuran maks">
                                </div>
                                <div class="form-text">Batas maksimal ukuran file upload untuk cover dan konten katalog</div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-chart-line me-1"></i>
                                Tingkat Kepatuhan
                            </label>
                            <div class="col-lg-9">
                                <div class="input-group mb-2">
                                    <span class="input-group-text">Sangat Patuh (%)</span>
                                    <input type="color" class="form-control form-control-color" name="catalog_very_obedient_color" id="catalog_very_obedient_color" value="{{ $obedient->firstWhere('NAME', 'Sangat Patuh')->WARNA ?? '#28a745' }}">
                                    <input type="number" class="form-control" name="catalog_very_obedient" id="catalog_very_obedient" max="100" value="{{ $obedient->firstWhere('NAME', 'Sangat Patuh')->PERSEN ?? '' }}" placeholder="Persentase">
                                </div>
                                <div class="input-group mb-2">
                                    <span class="input-group-text">Patuh (%)</span>
                                    <input type="color" class="form-control form-control-color" name="catalog_obedient_color" id="catalog_obedient_color" value="{{ $obedient->firstWhere('NAME', 'Patuh')->WARNA ?? '#17a2b8' }}">
                                    <input type="number" class="form-control" name="catalog_obedient" id="catalog_obedient" max="100" value="{{ $obedient->firstWhere('NAME', 'Patuh')->PERSEN ?? '' }}" placeholder="Persentase">
                                </div>
                                <div class="input-group mb-2">
                                    <span class="input-group-text">Cukup Patuh (%)</span>
                                    <input type="color" class="form-control form-control-color" name="catalog_quite_obidient_color" id="catalog_quite_obidient_color" value="{{ $obedient->firstWhere('NAME', 'Cukup Patuh')->WARNA ?? '#ffc107' }}">
                                    <input type="number" class="form-control" name="catalog_quite_obidient" id="catalog_quite_obidient" max="100" value="{{ $obedient->firstWhere('NAME', 'Cukup Patuh')->PERSEN ?? '' }}" placeholder="Persentase">
                                </div>
                                <div class="input-group mb-2">
                                    <span class="input-group-text">Kurang Patuh (%)</span>
                                    <input type="color" class="form-control form-control-color" name="catalog_less_obidient_color" id="catalog_less_obidient_color" value="{{ $obedient->firstWhere('NAME', 'Kurang Patuh')->WARNA ?? '#fd7e14' }}">
                                    <input type="number" class="form-control" name="catalog_less_obidient" id="catalog_less_obidient" max="100" value="{{ $obedient->firstWhere('NAME', 'Kurang Patuh')->PERSEN ?? '' }}" placeholder="Persentase">
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text">Tidak Patuh (%)</span>
                                    <input type="color" class="form-control form-control-color" name="catalog_not_obey_color" id="catalog_not_obey_color" value="{{ $obedient->firstWhere('NAME', 'Tidak Patuh')->WARNA ?? '#dc3545' }}">
                                    <input type="number" class="form-control" name="catalog_not_obey" id="catalog_not_obey" max="100" value="{{ $obedient->firstWhere('NAME', 'Tidak Patuh')->PERSEN ?? '' }}" placeholder="Persentase">
                                </div>
                                <div class="form-text mt-2">Persentase dan warna untuk setiap tingkat kepatuhan penerbit</div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-clock-afternoon me-1"></i>
                                Batas Waktu Serah KCKR
                            </label>
                            <div class="col-lg-9">
                                <div class="input-group">
                                    <input type="number" class="form-control" name="catalog_submission_kckr" id="catalog_submission_kckr" value="{{ $settingParameter->firstWhere('NAME', 'EBatasSerahKCKR')->VALUE ?? '' }}" placeholder="Jumlah hari">
                                    <span class="input-group-text">Hari</span>
                                </div>
                                <div class="form-text">Batas waktu penyerahan Karya Cetak dan Karya Rekam</div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-gift me-1"></i>
                                Batas Waktu Hibah
                            </label>
                            <div class="col-lg-9">
                                <div class="input-group">
                                    <input type="number" class="form-control" name="catalog_limit_grant" id="catalog_limit_grant" value="{{ $settingParameter->firstWhere('NAME', 'EBatasHibah')->VALUE ?? '' }}" placeholder="Jumlah hari">
                                    <span class="input-group-text">Hari</span>
                                </div>
                                <div class="form-text">Batas waktu untuk hibah koleksi</div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-package me-1"></i>
                                Batas Waktu Pengambilan
                            </label>
                            <div class="col-lg-9">
                                <div class="input-group">
                                    <input type="number" class="form-control" name="catalog_limit_retur" id="catalog_limit_retur" value="{{ $settingParameter->firstWhere('NAME', 'EBatasPengambilan')->VALUE ?? '' }}" placeholder="Jumlah hari">
                                    <span class="input-group-text">Hari</span>
                                </div>
                                <div class="form-text">Batas waktu pengambilan koleksi</div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-hourglass me-1"></i>
                                Waktu Wajib KCKR
                            </label>
                            <div class="col-lg-9">
                                <div class="input-group">
                                    <span class="input-group-text">Karya Cetak (Hari)</span>
                                    <input type="number" class="form-control" name="printed_work" id="printed_work" value="{{ $settingParameter->firstWhere('NAME', 'EWaktuWajibKaryaCetak')->VALUE ?? '' }}" placeholder="Hari">
                                    <span class="input-group-text">Karya Rekam (Hari)</span>
                                    <input type="number" class="form-control" name="recording_work" id="recording_work" value="{{ $settingParameter->firstWhere('NAME', 'EWaktuWajibKaryaRekam')->VALUE ?? '' }}" placeholder="Hari">
                                </div>
                                <div class="form-text">Waktu wajib penyerahan untuk karya cetak dan karya rekam</div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-chalkboard-teacher me-1"></i>
                                Maks Jumlah Pembinaan
                            </label>
                            <div class="col-lg-9">
                                <input type="number" class="form-control" name="max_coaching" id="max_coaching" value="{{ $settingParameter->firstWhere('NAME', 'EMaksJumlahPembinaan')->VALUE ?? '' }}" placeholder="Jumlah maksimal">
                                <div class="form-text">Batas maksimal jumlah pembinaan yang dapat dilakukan</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="nav-tabs-captcha">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="ph-shield-check me-2 text-primary"></i>
                            <h6 class="mb-0 fw-semibold">Konfigurasi Google reCAPTCHA</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info border-0">
                            <div class="d-flex align-items-start">
                                <i class="ph-info me-2 fs-4"></i>
                                <div>
                                    <h6 class="mb-1">Informasi</h6>
                                    Dapatkan kunci reCAPTCHA dari <a href="https://www.google.com/recaptcha/admin" target="_blank" class="alert-link">Google reCAPTCHA Admin</a>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-key me-1"></i>
                                Secret Key
                            </label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="captcha_secret_key" id="captcha_secret_key" value="{{ $settingParameter->firstWhere('NAME', 'ECaptchaSecret')->VALUE ?? '' }}" placeholder="Secret key dari Google reCAPTCHA">
                                <div class="form-text">Kunci rahasia untuk validasi server-side</div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-globe me-1"></i>
                                Site Key
                            </label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="captcha_site_key" id="captcha_site_key" value="{{ $settingParameter->firstWhere('NAME', 'ECaptchaSite')->VALUE ?? '' }}" placeholder="Site key dari Google reCAPTCHA">
                                <div class="form-text">Kunci publik untuk ditampilkan di halaman web</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="nav-tabs-whatsapp">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="ph-whatsapp-logo me-2 text-primary"></i>
                            <h6 class="mb-0 fw-semibold">Tes Pengiriman WhatsApp</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label fw-semibold">
                                        <i class="ph-phone me-1"></i>
                                        Nomor Tujuan
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">+62</span>
                                        <input type="text" class="form-control" name="whatsapp_target" id="whatsapp_target" placeholder="8123456789">
                                    </div>
                                    <div class="form-text">Masukkan nomor WhatsApp tanpa +62 atau 0</div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label fw-semibold">
                                        <i class="ph-chat-text me-1"></i>
                                        Isi Pesan
                                    </label>
                                    <textarea name="whatsapp_body" id="whatsapp_body" class="form-control" rows="5" placeholder="Tulis pesan yang akan dikirim..."></textarea>
                                    <div class="form-text">Pesan yang akan dikirim ke nomor tujuan</div>
                                </div>
                                <button type="button" class="btn btn-success" onclick="testSendWhatsapp()">
                                    <i class="ph-paper-plane-right me-1"></i>
                                    Kirim Tes WhatsApp
                                </button>
                            </div>
                            <div class="col-lg-6">
                                <div class="card bg-light border-0">
                                    <div class="card-header border-bottom">
                                        <h6 class="mb-0">
                                            <i class="ph-code me-1"></i>
                                            Response API
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <pre class="mb-0"><code class="language-json" id="response-whatsapp">Belum ada response...</code></pre>
                                    </div>
                                </div>
                                <div class="alert alert-warning border-0 mt-3">
                                    <div class="d-flex align-items-start">
                                        <i class="ph-warning me-2 fs-4"></i>
                                        <div>
                                            <h6 class="mb-1">Perhatian</h6>
                                            Pastikan API WhatsApp sudah dikonfigurasi dengan benar sebelum melakukan pengiriman.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="nav-tabs-api-isbn">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="ph-barcode me-2 text-primary"></i>
                            <h6 class="mb-0 fw-semibold">Konfigurasi API ISBN</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-key me-1"></i>
                                Token
                            </label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="isbn_token" id="isbn_token" value="{{ $settingParameter->firstWhere('NAME', 'EAPIISBNToken')->VALUE ?? '' }}" placeholder="Token API ISBN">
                                <div class="form-text">Token autentikasi untuk mengakses API ISBN</div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-link me-1"></i>
                                Base URL
                            </label>
                            <div class="col-lg-9">
                                <input type="url" class="form-control" name="isbn_base_url" id="isbn_base_url" value="{{ $settingParameter->firstWhere('NAME', 'EAPIISBNBaseUrl')->VALUE ?? '' }}" placeholder="https://api.isbn.example.com">
                                <div class="form-text">URL dasar endpoint API ISBN</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="nav-tabs-api-ro">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="ph-truck me-2 text-primary"></i>
                            <h6 class="mb-0 fw-semibold">Konfigurasi Raja Ongkir</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-key me-1"></i>
                                Token
                            </label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="ro_token" id="ro_token" value="{{ $settingParameter->firstWhere('NAME', 'EAPIRajaOngkirToken')->VALUE ?? '' }}" placeholder="Token API Raja Ongkir">
                                <div class="form-text">Token autentikasi untuk mengakses API Raja Ongkir</div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-link me-1"></i>
                                Base URL
                            </label>
                            <div class="col-lg-9">
                                <input type="url" class="form-control" name="ro_base_url" id="ro_base_url" value="{{ $settingParameter->firstWhere('NAME', 'EAPIRajaOngkirBaseUrl')->VALUE ?? '' }}" placeholder="https://api.rajaongkir.com">
                                <div class="form-text">URL dasar endpoint API Raja Ongkir</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="ph-package me-2 text-primary"></i>
                            <h6 class="mb-0 fw-semibold">Konfigurasi Komship</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row form-group">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-key me-1"></i>
                                Token
                            </label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="komship_token" id="komship_token" value="{{ $settingParameter->firstWhere('NAME', 'EAPIKomshipToken')->VALUE ?? '' }}" placeholder="Token API Komship">
                                <div class="form-text">Token autentikasi untuk mengakses API Komship</div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-link me-1"></i>
                                Base URL
                            </label>
                            <div class="col-lg-9">
                                <input type="url" class="form-control" name="komship_base_url" id="komship_base_url" value="{{ $settingParameter->firstWhere('NAME', 'EAPIKomshipBaseUrl')->VALUE ?? '' }}" placeholder="https://api.komship.com">
                                <div class="form-text">URL dasar endpoint API Komship</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="ph-gear me-2 text-primary"></i>
                            <h6 class="mb-0 fw-semibold">Metode Pengiriman</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <label class="col-lg-3 col-form-label fw-semibold">
                                <i class="ph-swap me-1"></i>
                                Metode Pengiriman Default
                            </label>
                            <div class="col-lg-9">
                                <select class="form-select" name="delivery_method" id="delivery_method">
                                    <option value="manual" {{ ($settingParameter->firstWhere('NAME', 'EDeliveryMethod')->VALUE ?? '') == 'manual' ? 'selected' : '' }}>Manual</option>
                                    <option value="expedition" {{ ($settingParameter->firstWhere('NAME', 'EDeliveryMethod')->VALUE ?? '') == 'expedition' ? 'selected' : '' }}>Ekspedisi</option>
                                </select>
                                <div class="form-text">Pilih metode pengiriman default yang akan digunakan sistem</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        <i class="ph-info me-1"></i>
                        Pastikan semua konfigurasi sudah benar sebelum menyimpan
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="ph-floppy-disk me-1"></i>
                        Simpan Pengaturan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(function() {
        datePickerSingle('.datepicker-single');
    });

    function testSendEmail() {
        var email = $('#dummy_email').val();

        if(!email) {
            swalInit.fire({
                title: 'Oops...',
                text: 'Mohon masukkan email tujuan',
                icon: 'warning',
                showCloseButton: false
            });
            return;
        }

        $.ajax({
            url: '{{ url("administration-system/setting-system/test-send-email") }}',
            type: 'POST',
            dataType: 'JSON',
            data: {
                email: email
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            beforeSend: function() {
                onLoading('show', 'body');
            },
            success: function(response) {
                onLoading('close', 'body');

                if(response.code == 200) {
                    swalInit.fire({
                        title: 'Berhasil',
                        text: response.message,
                        icon: 'success',
                        showCloseButton: false
                    });
                } else {
                    swalInit.fire({
                        title: 'Error',
                        text: response.message,
                        icon: 'error',
                        showCloseButton: false
                    });
                }
            },
            error: function(response) {
                onLoading('close', 'body');
                responseError(response);
            }
        });
    }

    function testSendWhatsapp() {
        var target = $('#whatsapp_target').val();
        var body = $('#whatsapp_body').val();

        if(target == '' || body == '') {
            swalInit.fire({
                title: 'Oops...',
                text: 'Mohon mengisi nomor tujuan dan pesan',
                icon: 'warning',
                showCloseButton: false
            });
            return;
        }

        $.ajax({
            url: '{{ url("administration-system/setting-system/test-send-whatsapp") }}',
            type: 'POST',
            dataType: 'JSON',
            data: {
                target: target,
                body: body
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            beforeSend: function() {
                onLoading('show', 'body');
                $('#response-whatsapp').text('Mengirim pesan...');
            },
            success: function(response) {
                onLoading('close', 'body');
                $('#response-whatsapp').text(JSON.stringify(response, null, 2));

                if(response.code == 200 || response.success) {
                    swalInit.fire({
                        title: 'Berhasil',
                        text: 'Pesan WhatsApp berhasil dikirim',
                        icon: 'success',
                        showCloseButton: false
                    });
                }
            },
            error: function(response) {
                onLoading('close', 'body');
                $('#response-whatsapp').text(JSON.stringify(response.responseJSON || {error: 'Terjadi kesalahan'}, null, 2));
                responseError(response);
            }
        });
    }
</script>
