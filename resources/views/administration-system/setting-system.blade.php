<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Administrasi Sistem - <span class="fw-normal">Pengaturan Sistem</span>
            </h4>
        </div>
    </div>
</div>
<div class="content pt-0">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{!! $error !!}</li>
                @endforeach
            </ul>
        </div>
    @elseif(session('success'))
        <div class="alert bg-success text-white fade show border-0">
            {{ session('success') }}
        </div>
    @elseif(session('failed'))
        <div class="alert bg-danger text-white fade show border-0">
            {{ session('failed') }}
        </div>
    @endif
    <form action="{{ url('administration-system/setting-system/submitted') }}" method="POST" onsubmit="onLoading('show', 'body')">
        @csrf
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-highlight nav-justified">
                    <li class="nav-item">
                        <a href="#nav-tabs-system" class="nav-link active" data-bs-toggle="tab">Sistem</a>
                    </li>
                    <li class="nav-item">
                        <a href="#nav-tabs-email" class="nav-link" data-bs-toggle="tab">Email</a>
                    </li>
                    <li class="nav-item">
                        <a href="#nav-tabs-collection" class="nav-link" data-bs-toggle="tab">Koleksi</a>
                    </li>
                    <li class="nav-item">
                        <a href="#nav-tabs-captcha" class="nav-link" data-bs-toggle="tab">Captcha</a>
                    </li>
                    <li class="nav-item">
                        <a href="#nav-tabs-whatsapp" class="nav-link" data-bs-toggle="tab">Whatsapp</a>
                    </li>
                    <li class="nav-item">
                        <a href="#nav-tabs-api-isbn" class="nav-link" data-bs-toggle="tab">API ISBN</a>
                    </li>
                    <li class="nav-item">
                        <a href="#nav-tabs-api-ro" class="nav-link" data-bs-toggle="tab">API Raja Ongkir</a>
                    </li>
                </ul>
                <div class="tab-content flex-lg-fill mt-4">
                    <div class="tab-pane fade show active" id="nav-tabs-system">
                        <div class="fw-bold border-bottom pb-2 mb-3">Umum</div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Percobaan Login</label>
                            <div class="col-md-10">
                                <div class="input-group">
                                    <span class="input-group-text">Attempt</span>
                                    <input type="number" class="form-control" name="system_rate_limiter" id="system_rate_limiter" value="{{ $settingParameter->firstWhere('NAME', 'EPercobaanLogin')->VALUE ?? '' }}" placeholder="....................">
                                    <span class="input-group-text">Interval (Jam)</span>
                                    <input type="number" class="form-control" name="system_rate_limiter_interval" id="system_rate_limiter_interval" value="{{ $settingParameter->firstWhere('NAME', 'EPercobaanLoginInterval')->VALUE ?? '' }}" placeholder="....................">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">AES</label>
                            <div class="col-md-10">
                                <div class="input-group">
                                    <span class="input-group-text">Key</span>
                                    <input type="text" class="form-control" name="system_aes_key" id="system_aes_key" value="{{ $settingParameter->firstWhere('NAME', 'EAesKey')->VALUE ?? '' }}" placeholder="....................">
                                    <span class="input-group-text">IV</span>
                                    <input type="text" class="form-control" name="system_aes_iv" id="system_aes_iv" value="{{ $settingParameter->firstWhere('NAME', 'EAesIV')->VALUE ?? '' }}" placeholder="....................">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">AES INLIS</label>
                            <div class="col-md-10">
                                <div class="input-group">
                                    <span class="input-group-text">Key</span>
                                    <input type="text" class="form-control" name="system_aes_key_inlis" id="system_aes_key_inlis" value="{{ $settingParameter->firstWhere('NAME', 'EAesInlisKey')->VALUE ?? '' }}" placeholder="....................">
                                    <span class="input-group-text">IV</span>
                                    <input type="text" class="form-control" name="system_aes_iv_inlis" id="system_aes_iv_inlis" value="{{ $settingParameter->firstWhere('NAME', 'EAesInlisIV')->VALUE ?? '' }}" placeholder="....................">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Allow IFrame Domain</label>
                            <div class="col-md-10">
                                <input type="url" class="form-control" name="system_allow_iframe_domain" id="system_allow_iframe_domain" value="{{ $settingParameter->firstWhere('NAME', 'EIFrameDomain')->VALUE ?? '' }}" placeholder="....................">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Batas Berlaku Reset Password</label>
                            <div class="col-md-10">
                                <div class="input-group">
                                    <input type="number" class="form-control" name="system_limit_reset_password" id="system_limit_reset_password" value="{{ $settingParameter->firstWhere('NAME', 'EBatasResetPassword')->VALUE ?? '' }}" placeholder="....................">
                                    <span class="input-group-text">Jam</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Batas Berlaku File Original</label>
                            <div class="col-md-10">
                                <div class="input-group">
                                    <input type="number" class="form-control" name="system_limit_file_original" id="system_limit_file_original" value="{{ $settingParameter->firstWhere('NAME', 'EBatasFileOriginal')->VALUE ?? '' }}" placeholder="....................">
                                    <span class="input-group-text">Hari</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Tgl Mulai Kepatuhan Penerbit</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control datepicker-single" name="executor_start_date" id="executor_start_date" value="{{ $settingParameter->firstWhere('NAME', 'ETglKepatuhanPenerbit')->VALUE ?? '' }}" placeholder="Pilih Tanggal" readonly>
                            </div>
                        </div>
                        <div class="fw-bold border-bottom pb-2 mb-3">Redis</div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Client</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="system_redis_client" id="system_redis_client" value="{{ $settingParameter->firstWhere('NAME', 'ERedisClient')->VALUE ?? '' }}" placeholder="....................">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Host</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="system_redis_host" id="system_redis_host" value="{{ $settingParameter->firstWhere('NAME', 'ERedisHost')->VALUE ?? '' }}" placeholder="....................">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Username</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="system_redis_username" id="system_redis_username" value="{{ $settingParameter->firstWhere('NAME', 'ERedisUsername')->VALUE ?? '' }}" placeholder="....................">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Password</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="system_redis_password" id="system_redis_password" value="{{ $settingParameter->firstWhere('NAME', 'ERedisPassword')->VALUE ?? '' }}" placeholder="....................">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Port</label>
                            <div class="col-md-10">
                                <input type="number" class="form-control" name="system_redis_port" id="system_redis_port" value="{{ $settingParameter->firstWhere('NAME', 'ERedisPort')->VALUE ?? '' }}" placeholder="....................">
                            </div>
                        </div>
                        <div class="fw-bold border-bottom pb-2 mb-3">Session</div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Driver</label>
                            <div class="col-md-10">
                                <select class="form-select" name="system_session_driver" id="system_session_driver">
                                    <option value="file" {{ ($settingParameter->firstWhere('NAME', 'ESessionDriver')->VALUE ?? '') == 'file' ? 'selected' : '' }}>File</option>
                                    <option value="cookie" {{ ($settingParameter->firstWhere('NAME', 'ESessionDriver')->VALUE ?? '') == 'cookie' ? 'selected' : '' }}>Cookie</option>
                                    <option value="database" {{ ($settingParameter->firstWhere('NAME', 'ESessionDriver')->VALUE ?? '') == 'database' ? 'selected' : '' }}>Database</option>
                                    <option value="memcached" {{ ($settingParameter->firstWhere('NAME', 'ESessionDriver')->VALUE ?? '') == 'memcached' ? 'selected' : '' }}>Memcached</option>
                                    <option value="redis" {{ ($settingParameter->firstWhere('NAME', 'ESessionDriver')->VALUE ?? '') == 'redis' ? 'selected' : '' }}>Redis</option>
                                    <option value="array" {{ ($settingParameter->firstWhere('NAME', 'ESessionDriver')->VALUE ?? '') == 'array' ? 'selected' : '' }}>Array</option>
                                    <option value="dynamodb" {{ ($settingParameter->firstWhere('NAME', 'ESessionDriver')->VALUE ?? '') == 'dynamodb' ? 'selected' : '' }}>Dynamo DB</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Batas</label>
                            <div class="col-md-10">
                                <div class="input-group">
                                    <input type="number" class="form-control" name="system_session_lifetime" id="system_session_lifetime" value="{{ $settingParameter->firstWhere('NAME', 'ESessionLifeTime')->VALUE ?? '' }}" placeholder="....................">
                                    <span class="input-group-text">Menit</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Terenkripsi</label>
                            <div class="col-md-10">
                                <select class="form-select" name="system_encryption" id="system_encryption">
                                    <option value="1" {{ ($settingParameter->firstWhere('NAME', 'ESessionEncrypt')->VALUE ?? '') == '1' ? 'selected' : '' }}>Ya</option>
                                    <option value="0" {{ ($settingParameter->firstWhere('NAME', 'ESessionEncrypt')->VALUE ?? '') == '0' ? 'selected' : '' }}>Tidak</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-tabs-email">
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Host</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="mail_host" id="mail_host" value="{{ $mail->HOST ?? '' }}" placeholder="....................">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Port</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="mail_port" id="mail_port" value="{{ $mail->PORT ?? '' }}" placeholder="....................">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Username</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="mail_username" id="mail_username" value="{{ $mail->CREDENTIALMAIL ?? '' }}" placeholder="....................">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Password</label>
                            <div class="col-md-10">
                                <input type="password" class="form-control" name="mail_password" id="mail_password" value="{{ $mail->CREDENTIALPASSWORD ?? '' }}" placeholder="....................">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Dari Email</label>
                            <div class="col-md-10">
                                <input type="email" class="form-control" name="mail_from" id="mail_from" value="{{ $mail->MAILFROM ?? '' }}" placeholder="....................">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Atas Nama</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="mail_name" id="mail_name" value="{{ $mail->MAILDISPLAYNAME ?? '' }}" placeholder="....................">
                            </div>
                        </div>
                        <div class="row justify-content-end">
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="dummy_email" id="dummy_email" value="admin@gmail.com" placeholder="Email yang akan di tes">
                                    <button type="button" class="btn btn-light btn-sm" onclick="testSendEmail()">
                                        <i class="ph-paper-plane-right me-1"></i>
                                        Tes Kirim
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-tabs-collection">
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Batas Upload</label>
                            <div class="col-md-10">
                                <div class="input-group">
                                    <span class="input-group-text">Cover (MB)</span>
                                    <input type="number" class="form-control" name="catalog_cover" id="catalog_cover" value="{{ $settingParameter->firstWhere('NAME', 'EKatalogCoverMaxUpload')->VALUE ?? '' }}" placeholder="....................">
                                    <span class="input-group-text">Konten (MB)</span>
                                    <input type="number" class="form-control" name="catalog_content" id="catalog_content" value="{{ $settingParameter->firstWhere('NAME', 'EKatalogContentMaxUpload')->VALUE ?? '' }}" placeholder="....................">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Kepatuhan</label>
                            <div class="col-md-10">
                                <div class="input-group">
                                    <span class="input-group-text">Patuh (%)</span>
                                    <input type="number" class="form-control" name="catalog_obedient" id="catalog_obedient" max="100" value="{{ $obedient->firstWhere('NAME', 'Patuh')->PERSEN ?? '' }}" placeholder="....................">
                                    <span class="input-group-text">Sebagian Patuh (%)</span>
                                    <input type="number" class="form-control" name="catalog_some_obey" id="catalog_some_obey" max="100" value="{{ $obedient->firstWhere('NAME', 'Sebagian Patuh')->PERSEN ?? '' }}" placeholder="....................">
                                    <span class="input-group-text">Tidak Patuh (%)</span>
                                    <input type="number" class="form-control" name="catalog_not_obey" id="catalog_not_obey" max="100" value="{{ $obedient->firstWhere('NAME', 'Tidak Patuh')->PERSEN ?? '' }}" placeholder="....................">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Batas Waktu Serah KCKR</label>
                            <div class="col-md-10">
                                <div class="input-group">
                                    <input type="number" class="form-control" name="catalog_submission_kckr" id="catalog_submission_kckr" value="{{ $settingParameter->firstWhere('NAME', 'EBatasSerahKCKR')->VALUE ?? '' }}" placeholder="....................">
                                    <span class="input-group-text">Hari</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Batas Waktu Hibah</label>
                            <div class="col-md-10">
                                <div class="input-group">
                                    <input type="number" class="form-control" name="catalog_limit_grant" id="catalog_limit_grant" value="{{ $settingParameter->firstWhere('NAME', 'EBatasHibah')->VALUE ?? '' }}" placeholder="....................">
                                    <span class="input-group-text">Hari</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Batas Waktu Pengambilan</label>
                            <div class="col-md-10">
                                <div class="input-group">
                                    <input type="number" class="form-control" name="catalog_limit_retur" id="catalog_limit_retur" value="{{ $settingParameter->firstWhere('NAME', 'EBatasPengambilan')->VALUE ?? '' }}" placeholder="....................">
                                    <span class="input-group-text">Hari</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Waktu Wajib KCKR</label>
                            <div class="col-md-10">
                                <div class="input-group">
                                    <span class="input-group-text">Karya Cetak (Hari)</span>
                                    <input type="number" class="form-control" name="printed_work" id="printed_work" value="{{ $settingParameter->firstWhere('NAME', 'EWaktuWajibKaryaCetak')->VALUE ?? '' }}" placeholder="....................">
                                    <span class="input-group-text">Karya Rekam (Hari)</span>
                                    <input type="number" class="form-control" name="recording_work" id="recording_work" value="{{ $settingParameter->firstWhere('NAME', 'EWaktuWajibKaryaRekam')->VALUE ?? '' }}" placeholder="....................">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Maks Jumlah Pembinaan</label>
                            <div class="col-md-10">
                                <input type="number" class="form-control" name="max_coaching" id="max_coaching" value="{{ $settingParameter->firstWhere('NAME', 'EMaksJumlahPembinaan')->VALUE ?? '' }}" placeholder="....................">
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-tabs-captcha">
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Secret Key</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="captcha_secret_key" id="captcha_secret_key" value="{{ $settingParameter->firstWhere('NAME', 'ECaptchaSecret')->VALUE ?? '' }}" placeholder="....................">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Site Key</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="captcha_site_key" id="captcha_site_key" value="{{ $settingParameter->firstWhere('NAME', 'ECaptchaSite')->VALUE ?? '' }}" placeholder="....................">
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-tabs-whatsapp">
                        Belum Tersedia
                    </div>
                    <div class="tab-pane fade" id="nav-tabs-api-isbn">
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Token</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="isbn_token" id="isbn_token" value="{{ $settingParameter->firstWhere('NAME', 'EAPIISBNToken')->VALUE ?? '' }}" placeholder="....................">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Base Url</label>
                            <div class="col-md-10">
                                <input type="url" class="form-control" name="isbn_base_url" id="isbn_base_url" value="{{ $settingParameter->firstWhere('NAME', 'EAPIISBNBaseUrl')->VALUE ?? '' }}" placeholder="....................">
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-tabs-api-ro">
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Token</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="ro_token" id="ro_token" value="{{ $settingParameter->firstWhere('NAME', 'EAPIRajaOngkirToken')->VALUE ?? '' }}" placeholder="....................">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Base Url</label>
                            <div class="col-md-10">
                                <input type="url" class="form-control" name="ro_base_url" id="ro_base_url" value="{{ $settingParameter->firstWhere('NAME', 'EAPIRajaOngkirBaseUrl')->VALUE ?? '' }}" placeholder="....................">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="ph-floppy-disk me-1"></i>
                        Simpan Data
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
        $.ajax({
            url: '{{ url("administration-system/setting-system/test-send-email") }}',
            type: 'POST',
            dataType: 'JSON',
            data: {
                email: $('#dummy_email').val()
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
</script>
