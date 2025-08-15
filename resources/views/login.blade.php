{{-- @if(\App::environment('production')) --}}
    <!DOCTYPE html>
    <html class="loading" lang="{{ config('app.locale') }}" data-textdirection="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <title>Login eDeposit</title>
        <link rel="apple-touch-icon" href="{{ asset('main/favicon.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('main/favicon.png') }}">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Quicksand:300,400,500,700" rel="stylesheet">
        <link href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/vendors.css') }}">
        <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/vendors/css/forms/icheck/icheck.css') }}">
        <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/vendors/css/forms/icheck/custom.css') }}">
        <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/core/menu/menu-types/vertical-menu.css') }}">
        <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/core/colors/palette-gradient.css') }}">
        <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/pages/login-register.css') }}">
        <link rel="stylesheet" href="{{ asset('theme_admin/assets/css/style.css') }}">
        <script async src="https://www.google.com/recaptcha/api.js"></script>
    </head>
    <body class="vertical-layout vertical-menu 1-column menu-expanded blank-page blank-page" data-open="click" data-menu="vertical-menu" data-col="1-column">
        <div class="app-content content">
            <div class="content-wrapper">
                <div class="content-header row"></div>
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
                                            <span>eDeposit 5.0</span>
                                        </h6>
                                        <p>Silahkan masukkan username dan password Anda. Anda dapat menggunakan username dan password akun ISBN.</p>
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
                                            <form class="form-horizontal form-simple" id="form-login" action="{{ url('login') }}" method="POST" novalidate autocomplete="off">
                                                @csrf
                                                <div class="form-group">
                                                    <fieldset class="form-group position-relative has-icon-left mb-0">
                                                        <input type="text" autocomplete="off" name="username" class="form-control form-control-lg input-lg" id="user-name" placeholder="Masukan username / email" required >
                                                        <div class="form-control-position"><i class="ft-user"></i></div>
                                                    </fieldset>
                                                </div>
                                                <div class="form-group">
                                                    <fieldset class="form-group position-relative has-icon-left">
                                                        <input type="password" autocomplete="off" name="password" class="form-control form-control-lg input-lg" id="user-password" placeholder="Enter Password" required >
                                                        <div class="form-control-position"><i class="la la-key"></i></div>
                                                    </fieldset>
                                                </div>
                                                @if(App::environment('production'))
                                                    <div class="g-recaptcha pb-1" data-sitekey="6Lem1QQqAAAAABvlZDDWlreHM6zqxjjAZrDkrGsK"></div>
                                                @endif
                                                <div class="form-group row">
                                                    <div class="col-md-12 col-12 text-center text-md-left">
                                                        <div class="form-check">
                                                            <input type="checkbox" id="remember-me" class="form-check-input">
                                                            <label for="remember-me"> Remember Me</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-info btn-lg btn-block"
                                               ><i class="ft-unlock"></i> Login</button><br>
                                                <div class="form-group row">
                                                    <div class="col-md-12 col-12 text-center text-md-right">
                                                        <a href="{{ url('reset-password') }}">  Lupa Password ?</a>
                                                    </div>
                                                </div>
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
        @if(App::environment('production'))
            <script>
                $(document).ready(function() {
                    $('#form-login').submit(function(event) {
                        event.preventDefault();
                        if(window.location.href.toString().includes('edeposit.perpusnas.go.id')){
                            var recaptchaResponse = grecaptcha.getResponse();
                            if(recaptchaResponse.length == 0) {
                                alert("Please complete the reCAPTCHA.");
                            } else {
                                event.currentTarget.submit();
                            }
                        } else {
                            event.currentTarget.submit();
                        }
                    });
                });
            </script>
        @endif
    </body>
    </html>
{{-- @else
    <script>
    var message  = '{{ $message }}';
    var redirect = '{{ $redirect }}';

    if(message) {
        alert(message);
    }

    window.location.href=redirect;
    </script>


@endif --}}
