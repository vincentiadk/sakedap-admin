
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
                                    <div class="alert bg-success text-white alert-icon-start fade show border-0">
                                        <span class="alert-icon bg-black bg-opacity-20">
                                            <i class="ph-check-circle"></i>
                                        </span>
                                        {{ session('success') }}
                                    </div>
                                @elseif(session('failed'))
                                    <div class="alert bg-danger text-white alert-icon-start fade show border-0">
                                        <span class="alert-icon bg-black bg-opacity-20">
                                            <i class="ph-x-circle"></i>
                                        </span>
                                        {{ session('failed') }}
                                    </div>
                                @endif
								<div class="mb-3">
									<label class="form-label">Password Baru :</label>
									<div class="form-control-feedback form-control-feedback-start">
										<input type="password" class="form-control" name="new_password" id="new_password" placeholder="...................." required>
										<div class="form-control-feedback-icon">
											<i class="ph-key text-muted"></i>
										</div>
									</div>
								</div>
                                <div class="mb-3">
									<label class="form-label">Konfirmasi Password :</label>
									<div class="form-control-feedback form-control-feedback-start">
										<input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="...................." required>
										<div class="form-control-feedback-icon">
											<i class="ph-key text-muted"></i>
										</div>
									</div>
								</div>
								<div class="mb-3">
									<button type="submit" class="btn btn-primary w-100">Reset Password</button>
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
