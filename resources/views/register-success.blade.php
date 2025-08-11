<!DOCTYPE html>
<html class="loading" lang="{{ config('app.locale') }}" data-textdirection="ltr">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Register Berhasil - eDeposit</title>
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
  <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/plugins/forms/wizard.css') }}">
  <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/vendors/css/forms/selects/select2.min.css') }}">
  <link rel="stylesheet" href="{{ asset('theme_admin/plugins/waitMe/waitMe.min.css') }}">
  <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/plugins/extensions/toastr.css') }}">
  <style type="text/css">
    .error {
      color: red;
      margin-top: 10px;
    }
  </style>
</head>
<body >
  <div class="app-content content">
    <div class="content-wrapper">
      <div class="content-header row">
      </div>
      <div class="content-body">
        <section class="flexbox-container" id="configuration">
          <div class="col-12 d-flex align-items-center justify-content-center">
            <div class="col-10 box-shadow-2 p-0">
              <div class="card border-grey border-lighten-3 m-0">
                <div class="card-header border-0">
                  <div class="card-title text-center">
                    <div class="p-1">
                      <img src="{{ asset('main/logo.png') }}" alt="branding logo">
                    </div>
                  </div>
                  <h6 class="card-subtitle line-on-side text-muted text-center font-small-3 pt-2">
                    <span>eDeposit V3</span>
                  </h6>
                </div>
                <div class="card-body">
                  <div class="alert alert-success border-0 alert-dismissible mb-2" role="alert">
                    <h4 class="alert-heading mb-2 text-center">Terimakasih</h4>
                    <p class=" text-center">Anda telah terdaftar sebagai Penerbit di eDeposit. Data sedang proses verifikasi oleh tim perpusnas</p>
                  </div>
                  <div class="text-center">
                    <a href="{{ url('/') }}" class="btn btn-default">Beranda</a>
                  </div>
                </div>
            </div>
          </div>
        </section>
      </div>
    </div>
  </div>
  <script src="{{ asset('theme_admin/app-assets/vendors/js/vendors.min.js') }}"></script>
  <script src="{{ asset('theme_admin/app-assets/vendors/js/extensions/toastr.min.js') }}"></script>
  <script src="{{ asset('theme_admin/app-assets/vendors/js/forms/icheck/icheck.min.js') }}"></script>
  <script src="{{ asset('theme_admin/app-assets/vendors/js/forms/validation/jqBootstrapValidation.js') }}"></script>
  <script src="{{ asset('theme_admin/app-assets/js/core/app-menu.js') }}"></script>
  <script src="{{ asset('theme_admin/app-assets/js/core/app.js') }}"></script>
  <script src="{{ asset('theme_admin/plugins/waitMe/waitMe.min.js') }}"></script>
  <script src="{{ asset('theme_admin/app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}"></script>
  <script src="{{ asset('theme_admin/app-assets/vendors/js/extensions/jquery.steps.min.js') }}"></script>
  <script src="{{ asset('theme_admin/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
  <script src="{{ asset('theme_admin/plugins/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
  <script type="text/javascript">

    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      onOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
      }
    });

    $(document).ready(function() {
      $('input[type=radio][name=category]').change(function() {
        if($(this).val() == 1) {
          $('#col-akta').html('<label id="label-akta">Akta / Perusahaan</label><div class="custom-file  border border-light"><label class="file center-block align-middle"><input type="file" id="file_akta" name="akta" class="w-100" id="inputGroupFileP" accept=".pdf" required="" style="margin-top: 5px;margin-left: 10px;"><span class="file-custom"></span></label></div>');
        } else if($(this).val() == 3) {
          $('#col-akta').html('');
        } else if($(this).val() == 2) {
          $('#col-akta').html('<label id="label-akta">KTP</label><div class="custom-file  border border-light"><label class="file center-block align-middle"><input type="file" id="file_akta" name="akta" class="w-100" id="inputGroupFileP" accept=".pdf" required="" style="margin-top: 5px;margin-left: 10px;"><span class="file-custom"></span></label></div>');
        }
      });
    });

    var form = $(".steps-validation").show();
    $(".steps-validation").steps({
        headerTag: "h6",
        bodyTag: "fieldset",
        transitionEffect: "fade",
        titleTemplate: '<span class="step">#index#</span> #title#',
        labels: {
            finish: 'Submit'
        },
        onStepChanging: function (event, currentIndex, newIndex)
        {
          $('#category-error').remove()
          $('#collection-error').remove()
          $('#password-confirm-error').remove()

          form.validate().settings.ignore = ":disabled,:hidden";
          let valid = form.valid();

          if(currentIndex == 0) {
            if ($("input[name='category']:checked").val() == undefined)
            {
              $('.category-element').append('<label id="category-error" class="error"  for="category">This field is required.</label>');
              valid = false;
            }
          }

          if(currentIndex == 1) {
            if($("input[name='password']").val() != $("input[name='password_confirm']").val()) {
              $('.password_confirm').append('<label id="password-confirm-error" class="error" for="category">Password Confirm is not valid!</label>');
              valid = false;
            }
          }

          return valid;
        },
        onStepChanged: function (event, currentIndex, priorIndex) {
          if(currentIndex == 2) {
            select2Nested('#publisher_province', 'load_province', null);
            select2Nested('#publisher_city', 'load_city', $('#publisher_province'));
            select2Nested('#publisher_district', 'load_district', $('#publisher_city'));
            select2Nested('#publisher_village', 'load_village', $('#publisher_district'));
          }
        },
        onFinishing: function (event, currentIndex)
        {
          create();
        }
    })

    function select2Nested(selector, endpoint, nestedId) {
      $(selector).select2({
          placeholder: '-- Pilih --',
          allowClear: true,
          cache: true,
          dropdowntParent: $('#modal_element'),
          ajax: {
              url: '{{ url("publisher/select2_serverside") }}' + '/' + endpoint,
              type: 'POST',
              dataType: 'JSON',
              delay: 250,
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              data: function(params) {
                  return {
                      search: params.term,
                      nested_id: nestedId != null ? nestedId.val() : null
                  };
              },
              processResults: function(data) {
                  return {
                      results: data.items
                  }
              }
          }
      });
    }

    function create() {
      $.ajax({
          url: '{{ url("register") }}',
          type: 'POST',
          dataType: 'JSON',
          data: new FormData($('#form_data')[0]),
          cache: false,
          contentType: false,
          processData: false,
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          beforeSend: function() {
              loadingOpen('#configuration');
              $('#validasi_element').hide();
              $('#validasi_content').html('');
          },
          success: function(response) {
              loadingClose('#configuration');
              if(response.status == 200) {
                  Toast.fire({
                      icon: 'success',
                      title: response.message
                  });
                  location.reload(true);
              } else if(response.status == 422) {
                  $('#validasi_element').show();

                  document.body.scrollTop            = 0;
                  document.documentElement.scrollTop = 0;

                  Toast.fire({
                      icon: 'info',
                      title: 'Validasi'
                  });

                  $.each(response.error, function(i, val) {
                      console.log(val)
                      $('#validasi_content').append('<li>' + val + '</li>');
                  });
              } else {
                  Toast.fire({
                      icon: 'warning',
                      title: response.message
                  });
              }
          },
          error: function() {
              loadingClose('#configuration');
              Toast.fire({
                  icon: 'error',
                  title: 'Server Error!'
              });
          }
      });
  }
  function loadingOpen(selector) {
      $(selector).waitMe({
        effect : 'progressBar',
        text : 'Mohon Tunggu ...',
        bg : 'rgba(255,255,255,0.7)',
        color : '#000'
      });
    }

    function loadingClose(selector) {
      $(selector).waitMe('hide');
    }
  </script>
</body>
</html>
