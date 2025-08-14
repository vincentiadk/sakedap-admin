<div class="app-content content">
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-1 d-inline-block">Request File Original</h3><br>
        <div class="row breadcrumbs-top d-inline-block">
          <div class="breadcrumb-wrapper col-12">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="#">Request File Original</a></li>
            </ol>
          </div>
        </div>
      </div>
      <div class="content-header-right col-md-6 col-12 mb-2 mt-1">
        <div class="float-md-right">
          <button type="button" class="btn btn-secondary" onclick="loadDataTable()">Refresh</button>
        </div>
      </div>
    </div>
    <div class="content-body">
      <section id="configuration">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Filter</h4>
              </div>
              <div class="card-content collapse show">
                <div class="card-body card-dashboard">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Dari Tanggal :</label>
                        <input type="date" name="periode_start" id="periode_start" class="form-control">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Sampai Tanggal :</label>
                        <input type="date" name="periode_end" id="periode_end" class="form-control">
                      </div>
                    </div>
                    <div class="col-md-12">
                      <hr>
                      <div class="form-group text-right">
                        <button type="button" class="btn btn-danger btn-sm" onclick="reset()"><i class="la la-times"></i> Reset</button>
                        <button type="button" class="btn btn-success btn-sm" onclick="loadDataTable()"><i class="la la-search"></i> Cari</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Daftar Request File Original</h4>
                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                <div class="heading-elements">
                  <ul class="list-inline mb-0">
                    <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                    <li><a onclick="loadDataTable()" data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                  </ul>
                </div>
              </div>
              <div class="card-content collapse show">
                <div class="card-body card-dashboard">
                  <table class="table table-striped table-bordered display nowrap" id="datatable_serverside">
                    <thead class="text-center">
                      <tr>
                        <th>Judul</th>
                        <th>Status</th>
                        <th>Jumlah Download</th>
                        <th>Surat Permintaan</th>
                        <th>Tanggal Request</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</div>

<script>
  $(function() {
    loadDataTable();
  });

  function reset() {
    $('#periode_start').val('');
    $('#periode_end').val('');
    loadDataTable();
  }

  function loadDataTable() {
    $('#datatable_serverside').DataTable({
      processing: true,
      serverSide: true,
      destroy: true,
      scrollX: true,
      lengthMenu: [10, 25, 50, 75, 100],
      ajax: {
        url: '{{ url("publisher/collection/request/monitor/datatable") }}',
        data: {
          periode_start: $('#periode_start').val(),
          periode_end: $('#periode_end').val()
        }
      },
      columns: [
        {
          name: 'title',
          className: 'align-middle text-center'
        },
        {
          name: 'status',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'count_download',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'request_file',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'created_at',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'action',
          searchable: false,
          className: 'align-middle text-center'
        }
      ]
    });
  }

  function updateStatus(collection_request_id, status) {

      var fd = new FormData();
      fd.append("collection_request_id", collection_request_id);// getting value from form feleds 
      fd.append("status", status);

      $.ajax({
          url: '{{ url("/admin/collection/request/update_status") }}' ,
          type: 'POST',
          dataType: 'JSON',
          data: fd,
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