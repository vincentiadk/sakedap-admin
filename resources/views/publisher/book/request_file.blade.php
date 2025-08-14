<div class="app-content content">
  <div class="content-wrapper">
      <div class="content-header row">
          <div class="content-header-left col-md-6 col-12 mb-2">
              <h3 class="content-header-title mb-1 d-inline-block">{{ $data['title'] }}</h3><br>
              <div class="row breadcrumbs-top d-inline-block">
                  <div class="breadcrumb-wrapper col-12">
                      <ol class="breadcrumb">
                          <li class="breadcrumb-item"><a href="{{ url('publisher/dashboard') }}">Dashboard</a></li>
                          <li class="breadcrumb-item"><a href="#">Buku</a></li>
                          <li class="breadcrumb-item active">Request File Original</li>
                      </ol>
                  </div>
              </div>
          </div>
      </div>
    <div class="content-body">
      <section id="configuration">
        @if(session('success'))
          <div class="alert bg-success alert-icon-left alert-dismissible mb-2" role="alert">
              <span class="alert-icon"><i class="la la-check"></i></span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
              <strong>Success!</strong> {{ session('success') }}
          </div>
        @elseif(session('failed'))
          <div class="alert bg-danger alert-icon-left alert-dismissible mb-2" role="alert">
              <span class="alert-icon"><i class="la la-times"></i></span>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
              <strong>Success!</strong> {{ session('failed') }}
          </div>
        @endif 
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title text-center">Form Request File Original Koleksi</h4>
                <a class="heading-elements-toggle"><i class="la la-ellipsis-h font-medium-3"></i></a>
                <div class="heading-elements">
                  <ul class="list-inline mb-0">
                    <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                    <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                    <li><a data-action="close"><i class="ft-x"></i></a></li>
                  </ul>
                </div>
              </div>
              <div class="card-content collapse show">
                <div class="card-body">
                  <form id="form_data">
                  <div class="form-group file-upload">
                    <label>Surat Permintaan File Original : <span class="danger">*</span></label>
                    <input type="file" name="file_request_letter" id="file_request_letter" class="form-control " accept=".pdf">
                  </div>
                   <div class="form-group file-upload">
                    <label>Kode Verifikasi : <span class="danger">*</span></label>
                    <div class="alert alert-info mb-2" role="alert">
                      <strong>Kode Verifikasi telah dikirim melalui Email!</strong>
                    </div>
                    <input type="text" name="verification_code" id="verification_code" class="form-control ">
                  </div>
                  <button type="button" class="btn btn-info"><i class="la la-send" onclick="submit()"></i> Kirim</button>
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
<script type="text/javascript">
  function submit() {

      if($('#file_request_letter').val() == "" || $('#verification_code').val() == "") {
        Swal.fire({
            position: 'center',
            icon: 'warning',
            title: 'Harap memilih Tipe Koleksi',
            showConfirmButton: true
        });
        return;
      }

      $.ajax({
          url: '{{ url("/publisher/collection/request/original/" . $data["collectionId"]) }}',
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
</script>
