<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Ganti Password - <span class="fw-normal">Keamanan Akun</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <a href="{{ url('profile') }}" class="btn btn-light shadow-sm">
                    <i class="ph-arrow-left me-2"></i>
                    Kembali ke Profil
                </a>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-xl-5">
            <form method="POST" id="form-password" autocomplete="off">
                @csrf
                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="ph-check-circle me-2"></i>
                            <div class="flex-fill">
                                <strong>Berhasil!</strong> {{ session('success') }}
                            </div>
                        </div>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger border-0 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="ph-x-circle me-2"></i>
                            <div class="flex-fill">
                                <strong>Gagal!</strong> {{ session('error') }}
                            </div>
                        </div>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm">
                        <div class="d-flex align-items-start">
                            <i class="ph-warning-circle me-2 mt-1"></i>
                            <div class="flex-fill">
                                <strong>Terdapat kesalahan:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <div class="bg-info bg-opacity-10 rounded p-2 me-3">
                                <i class="ph-info text-info"></i>
                            </div>
                            <div class="flex-fill">
                                <h6 class="mb-1 fw-semibold">Keamanan Password</h6>
                                <p class="text-muted mb-0 small">
                                    Gunakan password yang kuat untuk melindungi akun Anda. Pastikan password memenuhi semua kriteria keamanan yang tercantum di bawah.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="ph-lock-key me-2 text-primary"></i>
                            <h6 class="mb-0 fw-semibold">Form Ganti Password</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label fw-semibold" for="new_password">
                                <i class="ph-key me-1"></i>
                                Password Baru
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ph-lock"></i>
                                </span>
                                <input type="password" class="form-control" name="new_password" id="new_password" placeholder="Masukkan password baru" autocomplete="new-password">
                                <button class="btn btn-light" type="button" id="toggle-new-password">
                                    <i class="ph-eye" id="icon-new-password"></i>
                                </button>
                            </div>
                        </div>
                        <div class="password-rule">
                            <div class="card border-0">
                                <div class="card-body p-3">
                                    <h6 class="mb-2 fw-semibold">
                                        <i class="ph-shield-check me-1"></i>
                                        Kriteria Password
                                    </h6>
                                    <div class="d-grid gap-2">
                                        <div class="form-text text-danger d-flex align-items-center">
                                            <span class="icon-char">
                                                <i class="ph-x text-danger me-2"></i>
                                            </span>
                                            Minimal 8 karakter
                                        </div>
                                        <div class="form-text text-danger d-flex align-items-center">
                                            <span class="icon-case">
                                                <i class="ph-x text-danger me-2"></i>
                                            </span>
                                            1 huruf besar dan 1 huruf kecil
                                        </div>
                                        <div class="form-text text-danger d-flex align-items-center">
                                            <span class="icon-number">
                                                <i class="ph-x text-danger me-2"></i>
                                            </span>
                                            1 angka
                                        </div>
                                        <div class="form-text text-danger d-flex align-items-center">
                                            <span class="icon-symbol">
                                                <i class="ph-x text-danger me-2"></i>
                                            </span>
                                            1 simbol (Misalnya: !@#$%^&*)
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="form-group">
                            <label class="form-label fw-semibold" for="confirm_password">
                                <i class="ph-check-circle me-1"></i>
                                Konfirmasi Password
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ph-lock"></i>
                                </span>
                                <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Konfirmasi password baru" autocomplete="new-password">
                                <button class="btn btn-light" type="button" id="toggle-confirm-password">
                                    <i class="ph-eye" id="icon-confirm-password"></i>
                                </button>
                            </div>
                        </div>
                        <div class="confirm-password-rule">
                            <div class="card border-0">
                                <div class="card-body p-3">
                                    <div class="form-text text-danger d-flex align-items-center">
                                        <span class="icon-match">
                                            <i class="ph-x text-danger me-2"></i>
                                        </span>
                                        Password harus cocok
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer border-top">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ url('profile') }}" class="btn btn-light">
                                <i class="ph-x me-1"></i>
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary" id="btn-submit" disabled>
                                <i class="ph-floppy-disk me-1"></i>
                                Simpan Password
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="ph-lightbulb me-2 text-warning"></i>
                            <h6 class="mb-0 fw-semibold">Tips Keamanan</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0 ps-3">
                            <li class="mb-2">
                                <small class="text-muted">Jangan gunakan password yang sama dengan akun lain</small>
                            </li>
                            <li class="mb-2">
                                <small class="text-muted">Hindari menggunakan informasi pribadi yang mudah ditebak</small>
                            </li>
                            <li class="mb-2">
                                <small class="text-muted">Ganti password secara berkala untuk keamanan maksimal</small>
                            </li>
                            <li>
                                <small class="text-muted">Jangan bagikan password Anda kepada siapapun</small>
                            </li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(function() {
        const requirements = {
            length: { regex: /^.{8,20}$/, selector: '.icon-char' },
            case: { regex: /(?=.*[a-z])(?=.*[A-Z])/, selector: '.icon-case' },
            digit: { regex: /\d/, selector: '.icon-number' },
            special: { regex: /[!@#$%^&*()_+={}[\]:;'"<,>.?/\\|~`]/, selector: '.icon-symbol' }
        };

        const $submitButton = $('#btn-submit');
        const $newPasswordInput = $("#new_password");
        const $confirmPasswordInput = $("#confirm_password");

        // Toggle Password Visibility
        $('#toggle-new-password').on('click', function() {
            const type = $newPasswordInput.attr('type') === 'password' ? 'text' : 'password';
            $newPasswordInput.attr('type', type);
            $('#icon-new-password').toggleClass('ph-eye ph-eye-slash');
        });

        $('#toggle-confirm-password').on('click', function() {
            const type = $confirmPasswordInput.attr('type') === 'password' ? 'text' : 'password';
            $confirmPasswordInput.attr('type', type);
            $('#icon-confirm-password').toggleClass('ph-eye ph-eye-slash');
        });

        function updateNewPasswordRules(passwordValue) {
            let allValid = true;

            $.each(requirements, function(key, req) {
                const $iconSpan = $('.password-rule ' + req.selector);
                const $iconElement = $iconSpan.find('i');
                const $ruleText = $iconSpan.closest('.form-text');

                if (req.regex.test(passwordValue)) {
                    $iconElement.removeClass('ph-x text-danger').addClass('ph-check-circle text-success');
                    $ruleText.addClass('text-success').removeClass('text-danger');
                } else {
                    $iconElement.removeClass('ph-check-circle text-success').addClass('ph-x text-danger');
                    $ruleText.addClass('text-danger').removeClass('text-success');
                    allValid = false;
                }
            });

            return allValid;
        }

        function checkConfirmPassword(newPass, confirmPass) {
            const $matchIconSpan = $('.confirm-password-rule .icon-match');
            const $matchIconElement = $matchIconSpan.find('i');
            const $matchRuleText = $matchIconSpan.closest('.form-text');
            const isMatch = (newPass.length > 0 && newPass === confirmPass);

            if (isMatch) {
                $matchIconElement.removeClass('ph-x text-danger').addClass('ph-check-circle text-success');
                $matchRuleText.addClass('text-success').removeClass('text-danger');
            } else {
                $matchIconElement.removeClass('ph-check-circle text-success').addClass('ph-x text-danger');
                $matchRuleText.addClass('text-danger').removeClass('text-success');
            }

            return isMatch;
        }

        function checkAllValidation() {
            const newPassValue = $newPasswordInput.val();
            const confirmPassValue = $confirmPasswordInput.val();
            const isNewPassValid = updateNewPasswordRules(newPassValue);
            const isConfirmValid = checkConfirmPassword(newPassValue, confirmPassValue);
            const finalValidation = isNewPassValid && isConfirmValid;

            $submitButton.prop('disabled', !finalValidation);

            return finalValidation;
        }

        $newPasswordInput.on("keyup blur", function() {
            checkAllValidation();
        });

        $confirmPasswordInput.on("keyup blur", function() {
            checkAllValidation();
        });

        $('#form-password').on('submit', function(e) {
            const isValid = checkAllValidation();

            if (typeof onLoading === 'function') {
                 onLoading('show', 'body');
            }

            if (!isValid) {
                e.preventDefault();

                if (typeof onLoading === 'function') {
                    onLoading('close', 'body');
                }

                if (typeof swalInit !== 'undefined') {
                    swalInit.fire({
                        title: 'Oops ...',
                        text: 'Harap perbaiki semua aturan password yang belum terpenuhi.',
                        icon: 'warning',
                        showCloseButton: false
                    });
                } else {
                    alert('Harap perbaiki semua aturan password yang belum terpenuhi.');
                }
            }
        });

        checkAllValidation();
    });
</script>
