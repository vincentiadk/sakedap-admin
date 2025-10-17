<div class="content mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-4">
            <form method="POST" id="form-password" autocomplete="off">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Ganti Password</h5>
                    </div>
                    <div class="card-body border-top">
                        @if(session('success'))
                            <div class="alert bg-success text-white fade show border-0" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="alert bg-danger text-white fade show border-0" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-group">
                            <label class="form-label" for="new_password">Password Baru : <span class="text-danger fw-bold">*</span></label>
                            <input type="password" class="form-control" name="new_password" id="new_password" placeholder="Masukkan password baru" autocomplete="new-password">
                        </div>
                        <div class="password-rule mt-2">
                            <h6 class="fs-sm fw-medium mb-1">Password harus memenuhi:</h6>
                            <div class="form-text text-danger d-flex align-items-center">
                                <span class="icon-char">
                                    <i class="ph-x text-danger me-1"></i>
                                </span>
                                Minimal 8 karakter
                            </div>
                            <div class="form-text text-danger d-flex align-items-center">
                                <span class="icon-case">
                                    <i class="ph-x text-danger me-1"></i>
                                </span>
                                1 huruf besar dan 1 huruf kecil
                            </div>
                            <div class="form-text text-danger d-flex align-items-center">
                                <span class="icon-number">
                                    <i class="ph-x text-danger me-1"></i>
                                </span>
                                1 angka
                            </div>
                            <div class="form-text text-danger d-flex align-items-center">
                                <span class="icon-symbol">
                                    <i class="ph-x text-danger me-1"></i>
                                </span>
                                1 simbol (Misalnya: !@#$%^&*)
                            </div>
                        </div>
                        <hr class="my-3">
                        <div class="form-group">
                            <label class="form-label" for="confirm_password">Konfirmasi Password : <span class="text-danger fw-bold">*</span></label>
                            <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Konfirmasi password baru" autocomplete="new-password">
                        </div>
                        <div class="confirm-password-rule mt-2">
                             <div class="form-text text-danger d-flex align-items-center">
                                <span class="icon-match">
                                    <i class="ph-x text-danger me-1"></i>
                                </span>
                                Password harus cocok
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="form-group mb-0">
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary" id="btn-submit" disabled>
                                    <i class="ph-floppy-disk me-1"></i>
                                    Simpan Perubahan Password
                                </button>
                            </div>
                        </div>
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
                    swalInit.fire('Oops ...', 'Harap perbaiki semua aturan password yang belum terpenuhi.', 'warning');
                } else {
                    alert('Harap perbaiki semua aturan password yang belum terpenuhi.');
                }
            }
        });

        checkAllValidation();
    });
</script>
