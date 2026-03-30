
<!DOCTYPE html>
<html lang="id" dir="ltr">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>@yield('title')</title>
    <link rel="shortcut icon" href="{{ asset('assets/icon.png') }}?v={{ uniqid() }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/icon.png') }}?v={{ uniqid() }}">
	<link href="{{ asset('themes/fonts/inter/inter.css') }}?v={{ uniqid() }}" rel="stylesheet">
	<link href="{{ asset('themes/icons/phosphor/styles.min.css') }}?v={{ uniqid() }}" rel="stylesheet">
	<link href="{{ asset('themes/css/ltr/all.min.css') }}?v={{ uniqid() }}" id="stylesheet" rel="stylesheet">
	<script src="{{ asset('themes/js/bootstrap/bootstrap.bundle.min.js') }}?v={{ uniqid() }}"></script>
	<script src="{{ asset('themes/js/app.js') }}?v={{ uniqid() }}"></script>
</head>
<body>
	<div class="page-content">
		<div class="content-wrapper">
			<div class="content-inner">
				<div class="content d-flex justify-content-center align-items-center">
					<div class="flex-fill">
						<div class="text-center mb-4">
							<img src="{{ asset('themes/images/error_bg.svg') }}" class="img-fluid mb-3" height="230" alt="">
							<h1 class="display-3 fw-semibold lh-1 mb-3">@yield('code')</h1>
							<h4 class="mx-md-auto">@yield('message')</h4>
							<p style="color: #6c757d; font-size: 14px;">
								Terima kasih atas kesabaran Anda 🙏
							</p>
						</div>
						<div class="text-center">
							<a href="{{ url('/') }}" class="btn btn-primary">
                                <i class="ph-arrow-left me-1"></i>
								Kembali
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
