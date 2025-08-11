<!DOCTYPE html>
<html class="loading" lang="{{ config('app.locale') }}" data-textdirection="ltr">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta name="description" content="Modern admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities with bitcoin dashboard.">
  <meta name="keywords" content="admin template, modern admin template, dashboard template, flat admin template, responsive admin template, web app, crypto dashboard, bitcoin dashboard">
  <meta name="author" content="PIXINVENT">
  <title>Reset Password eDeposit</title>
  <link rel="apple-touch-icon" href="{{ asset('main/favicon.png') }}">
  <link rel="shortcut icon" type="image/png" href="{{ asset('main/favicon.png') }}">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Quicksand:300,400,500,700"
  rel="stylesheet">
  <link href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css"
  rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/vendors.css') }}">
  <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/vendors/css/forms/icheck/custom.css') }}">
  <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/app.css') }}">
  <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/core/menu/menu-types/vertical-menu.css') }}">
  <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/core/colors/palette-gradient.css') }}">
  <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/pages/login-register.css') }}">
  <link rel="stylesheet" href="{{ asset('theme_admin/assets/css/style.css') }}">
</head>
<body class="vertical-layout vertical-menu 1-column   menu-expanded blank-page blank-page" data-open="click" data-menu="vertical-menu" data-col="1-column">
  <div class="app-content content">
    <div class="content-wrapper">
      <div class="content-header row">
      </div>
      <div class="content-body">
        <section class="flexbox-container">
          <div class="col-12 d-flex align-items-center justify-content-center">
            <div class="col-md-4 col-10 box-shadow-2 p-0">
              <div class="card border-grey border-lighten-3 m-0">
                <div class="card-header border-0">
                  <div class="card-title text-center">
                    <div class="p-1">
                      <img src="{{ asset('main/logo.png') }}" alt="branding logo">
                    </div>
                  </div>
                  <h6 class="card-subtitle line-on-side text-muted text-center font-small-3 pt-2">
                    <span>Reset Password</span>

                  </h6>
                  <p style="color:red">Fitur reset password <b>tidak</b> dapat digunakan untuk reset password Akun ISBN. Gunakan reset password pada <a href="https://isbn.perpusnas.go.id/">web ISBN</a></p>
                </div>
                <div class="card-content">
                  <div class="card-body">
                    @if(session('success'))
                      <div class="text-center">
                        <div class="alert bg-success">{{ session('success') }}</div>
                      </div>
                    @elseif(session('failed'))
                      <div class="text-center">
                        <div class="alert bg-danger">{{ session('failed') }}</div>
                      </div>
                    @endif
                    <form class="form-horizontal form-simple" action="{{ url('reset-password') }}" method="POST" novalidate>
                      @csrf
                      <div class="form-group">
                        <fieldset class="form-group position-relative has-icon-left mb-0">
                          <input type="text" name="email" class="form-control form-control-lg input-lg" id="user-name" placeholder="Masukan E-mail Anda" required>
                          <div class="form-control-position"><i class="ft-mail"></i></div>
                        </fieldset>
                      </div>
                      <button type="submit" class="btn btn-info btn-lg btn-block"><i class="ft-unlock"></i> Reset Password</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
  <script src="{{ asset('theme_admin/app-assets/vendors/js/vendors.min.js') }}"></script>
  <script src="{{ asset('theme_admin/app-assets/vendors/js/forms/icheck/icheck.min.js') }}"></script>
  <script src="{{ asset('theme_admin/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js') }}"></script>
  <script src="{{ asset('theme_admin/app-assets/js/core/app-menu.js') }}"></script>
  <script src="{{ asset('theme_admin/app-assets/js/core/app.js') }}"></script>
  <script src="{{ asset('theme_admin/app-assets/js/scripts/forms/form-login-register.js') }}"></script>
</body>
</html>
