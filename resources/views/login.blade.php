
<!DOCTYPE html>
<html lang="id" dir="ltr">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>E-Deposit 5.0 | Admin Panel</title>
    <link rel="shortcut icon" href="{{ asset('assets/icon.png') }}?v={{ uniqid() }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/icon.png') }}?v={{ uniqid() }}">
	<link href="{{ asset('themes/fonts/inter/inter.css') }}?v={{ uniqid() }}" rel="stylesheet">
	<link href="{{ asset('themes/icons/phosphor/styles.min.css') }}?v={{ uniqid() }}" rel="stylesheet">
	<link href="{{ asset('themes/css/ltr/all.min.css') }}?v={{ uniqid() }}" id="stylesheet" rel="stylesheet">
	<script src="{{ asset('themes/js/bootstrap/bootstrap.bundle.min.js') }}?v={{ uniqid() }}"></script>
	<script src="{{ asset('themes/js/app.js') }}?v={{ uniqid() }}"></script>
    {!! NoCaptcha::renderJs() !!}

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
					<form class="login-form" method="POST">
                        @csrf
						<div class="card mb-0">
							<div class="card-body">
								<div class="text-center mb-2">
									<div class="d-inline-flex align-items-center justify-content-center mb-3 mt-2">
										<img src="{{ asset('assets/icon.png') }}" style="max-width:125px" alt="">
									</div>
									<h5 class="mb-0">Masuk ke Akun Anda</h5>
									<span class="d-block text-muted">Masukan Kredensial</span>
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
								<div class="mb-3">
									<label class="form-label">Username :</label>
									<div class="form-control-feedback form-control-feedback-start">
										<input type="text" class="form-control" name="username" id="username" placeholder="...................." required>
										<div class="form-control-feedback-icon">
											<i class="ph-user-circle text-muted"></i>
										</div>
									</div>
								</div>
								<div class="mb-3">
									<label class="form-label">Password :</label>
									<div class="form-control-feedback form-control-feedback-start">
										<input type="password" class="form-control" name="password" id="password" placeholder="...................." required>
										<div class="form-control-feedback-icon">
											<i class="ph-lock text-muted"></i>
										</div>
									</div>
								</div>
                                <div class="d-flex align-items-center mb-3">
									<a href="{{ url('reset-password-request') }}" class="ms-auto">Reset Password?</a>
								</div>
                                <div class="mb-3 d-flex justify-content-center w-100">
                                    {!! NoCaptcha::display() !!}
                                </div>
								<div class="mb-3">
									<button type="submit" class="btn btn-primary w-100">Masuk</button>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
