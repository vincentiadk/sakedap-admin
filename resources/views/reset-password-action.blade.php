
<!DOCTYPE html>
<html lang="id" dir="ltr">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>SAKEDAP | Admin Panel</title>
    <link rel="shortcut icon" href="{{ asset('assets/icon.png') }}?v={{ uniqid() }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/icon.png') }}?v={{ uniqid() }}">
	<link href="{{ asset('themes/fonts/inter/inter.css') }}?v={{ uniqid() }}" rel="stylesheet">
	<link href="{{ asset('themes/icons/phosphor/styles.min.css') }}?v={{ uniqid() }}" rel="stylesheet">
	<link href="{{ asset('themes/css/ltr/all.min.css') }}?v={{ uniqid() }}" id="stylesheet" rel="stylesheet">
	<script src="{{ asset('themes/js/bootstrap/bootstrap.bundle.min.js') }}?v={{ uniqid() }}"></script>
    <script src="{{ asset('themes/js/jquery/jquery.min.js') }}?v={{ uniqid() }}"></script>
	<script src="{{ asset('themes/js/app.js') }}?v={{ uniqid() }}"></script>

    <style>
        .page-content {
            background: url('{{ asset("assets/bg-login.jpg") }}') no-repeat center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
        }
    </style>

</head>

<body class="bg-light">
	<div class="page-content">
		<div class="content-wrapper">
			<div class="content-inner">
				<div class="content d-flex justify-content-center align-items-center">
					<form class="login-form" method="POST" id="form-password" autocomplete="off">
                        @csrf
						<div class="card mb-0">
							<div class="card-body">
								<div class="text-center mb-4">
									<div class="d-inline-flex align-items-center justify-content-center mb-3 mt-2">
										<img src="{{ asset('assets/icon.png') }}" style="max-width:125px" alt="">
									</div>
									<h5 class="mb-0">Reset Password</h5>
									<span class="d-block text-muted">Silahkan melakukan pergantian password</span>
								</div>
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
								<div class="form-group">
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
								<div class="mt-3">
                                    <button type="submit" class="btn btn-primary w-100" id="btn-submit" readonly>
                                        Reset Pasword
                                    </button>
								</div>
							</div>
						</div>
					</form>
				</div>
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

                $submitButton.prop('readonly', !finalValidation);

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

</body>
</html>
