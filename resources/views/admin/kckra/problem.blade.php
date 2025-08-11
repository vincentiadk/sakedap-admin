<div class="app-content content">
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-1 d-inline-block">{{ $data['title'] }}</h3><br>
        <div class="row breadcrumbs-top d-inline-block">
          <div class="breadcrumb-wrapper col-12">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ url('publisher/dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="#">Koleksi KCKR Analog</a></li>
              <li class="breadcrumb-item active">Ditolak</li>
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
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>ISBN/ISSN</label>
                        <input type="text" name="code" id="code" placeholder="Code" class="form-control">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Judul</label>
                        <input type="text" name="title" id="title" placeholder="Judul" class="form-control">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    {{-- <div class="col-md-4">
                      <div class="form-group">
                        <label>Dari Tanggal :</label>
                        <input type="date" name="periode_start" id="periode_start" class="form-control">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Sampai Tanggal :</label>
                        <input type="date" name="periode_end" id="periode_end" class="form-control">
                      </div>
                    </div> --}}
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
                <h4 class="card-title">Daftar Koleksi Ditolak KCKRA</h4>
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
                        <th>Tipe</th>
                        <th>Pelaksana</th>
                        <th>Judul</th>
                        <th>Identifier</th>
                        <th>Alasan</th>
                        <th>Status</th>
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
    $('#tipekoleksi').select2({
        placeholder: '-- Pilih Tipe Koleksi --',
        allowClear: true,
        multiple: true,
        cache: true,
    });

     $('#publisher_id').select2({
        placeholder: '-- Pilih Publisher --',
        allowClear: true,
        multiple: true,
        cache: true,
    });

    $("#tipekoleksi").select2("val", "{{ Request::input('tipe') }}");
    $("#publisher_id").select2("val", "{{ Request::input('publisher_id') }}");

    loadDataTable();
  });

  function reset() {
    $('#periode_start').val('');
    $('#periode_end').val('');
    loadDataTable();
  }

  function cancel() {
		$('#modal_element').modal('hide');
	}

  function loadDataTable() {
    $('#datatable_serverside').DataTable({
      processing: true,
      serverSide: true,
      destroy: true,
      scrollX: true,
      lengthMenu: [10, 25, 50, 75, 100],
      order: [[2,"asc"]],
      ajax: {
        url: '{{ url("admin/collection/kckra/problem/datatable") }}',
        data: {
          title: $('#title').val(),
          code: $('#code').val()
        }
      },
      columns: [
        {
          name: 'type',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'publisher_id',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'title',
          className: 'align-middle text-center'
        },
        {
          name: 'code',
          className: 'align-middle text-center'
        },
        {
          name: 'problem',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'status',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'action',
          searchable: false,
          className: 'align-middle text-center'
        },
      ]
    });
  }

  function handleCopy(id, handling) {
    var message = '';
    var handle = '';
    if (handling == 1) {
      message = 'Anda yakin untuk mendonasikan koleksi tersebut?';
      handle = 'donation';
    } else {
      message = 'Anda yakin untuk mengambil koleksi tersebut?'
      handle = 'pick up';
    }
    Swal.fire({
			title: message,
			text: '',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes!'
		}).then((result) => {
			if(result.value) {
        $.ajax({
          url: '{{ url("publisher/collection/problem_kckra/handling") }}' + '/' + id,
          type: 'GET',
          dataType: 'JSON',
          data: {
            handling: handle,
          },
          beforeSend: function() {
          },
          success: function(response) {
						$('#datatable_serverside').DataTable().ajax.reload(null, false);
            Toast.fire({
                icon: 'success',
                title: response.message
              });
          },
          error: function() {
            Toast.fire({
              icon: 'error',
              title: 'Server Error!'
            });
          }
        })
      }
		});
	}

  function handleReset(id) {
    var message = 'Anda yakin untuk mereset tindakan?';
    var handle = null;
   
    Swal.fire({
			title: message,
			text: '',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes!'
		}).then((result) => {
			if(result.value) {
        $.ajax({
          url: '{{ url("publisher/collection/problem_kckra/handling") }}' + '/' + id,
          type: 'GET',
          dataType: 'JSON',
          data: {
            handling: handle,
          },
          beforeSend: function() {
          },
          success: function(response) {
						$('#datatable_serverside').DataTable().ajax.reload(null, false);
            Toast.fire({
                icon: 'success',
                title: response.message
              });
          },
          error: function() {
            Toast.fire({
              icon: 'error',
              title: 'Server Error!'
            });
          }
        })
      }
		});
	}
</script>