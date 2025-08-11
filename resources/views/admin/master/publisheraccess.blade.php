<div class="app-content content">
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-1 d-inline-block">Master Publisher Access</h3><br>
        <div class="row breadcrumbs-top d-inline-block">
          <div class="breadcrumb-wrapper col-12">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="#">Master</a></li>
              <li class="breadcrumb-item active">Publisher Access</li>
            </ol>
          </div>
        </div>
      </div>
      <div class="content-header-right col-md-6 col-12 mb-2 mt-1">
        <div class="float-md-right">
          <button type="button" class="btn btn-secondary" onclick="loadDataTable()">Refresh</button>
          <button type="button" class="btn btn-info" data-toggle="modal" id="modal_element_button" data-target="#modal_element">Tambah</button>
        </div>
      </div>
    </div>
    <div class="content-body">
      <section id="configuration">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Daftar Publisher Access</h4>
              </div>
              <div class="card-content collapse show">
                <div class="card-body card-dashboard">
                  <table class="table table-striped table-bordered display nowrap" id="datatable_serverside">
                    <thead class="text-center">
                      <tr>
                        <th>No</th>
                        <th>Publisher</th>
                        <th>Kode Sistem</th>
                        <th>Tipe Sistem</th>
                        <th>Tanggal</th>
                        <th>Update</th>
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

<div class="modal animated bounceInRight text-left" id="modal_element" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel49" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel49">Form</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-danger" id="validasi_element" style="display:none;">
          <ul id="validasi_content"></ul>
        </div>
        <form action="" id="form_data">
          <div class="form-group">
            <label>Publisher</label>
            <select name="publisher_id" id="publisher_id" class="form-control select2" style="width:100%;">
              <option value="">-- Pilih Publisher --</option>
              @foreach($publisher as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label>Tipe Sistem :</label>
            <input type="text" name="system_type" id="system_type" class="form-control">
          </div>
          <div class="form-group">
            <label>Kode Sistem :</label>
            <input type="text" name="code_system" id="code_system" class="form-control">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn grey btn-secondary" data-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-danger" onclick="cancel()" id="btn_cancel" style="diplay:none;">Batal</button>
        <button type="button" class="btn btn-warning" onclick="update()" id="btn_update" style="diplay:none;">Simpan Perubahan</button>
        <button type="button" class="btn btn-primary" onclick="create()" id="btn_create">Tambah</button>
      </div>
    </div>
  </div>
</div>

<script>
  $(function() {
    loadDataTable();

    $('#modal_element_button').click(function() {
      reset();
      $('#btn_cancel').hide();
      $('#btn_update').hide();
      $('#btn_create').show();
    });
  });

  function cancel() {
    reset();
    $('#modal_element').modal('hide');
    $('#btn_cancel').hide();
    $('#btn_update').hide();
    $('#btn_create').show();
  }

  function toUpdate() {
    $('#modal_element').modal('show');
    $('#btn_cancel').show();
    $('#btn_update').show();
    $('#btn_create').hide();
  }

  function reset() {
    $('#validasi_element').hide();
    $('#validasi_content').html('');
    $('#form_data').trigger('reset');
  }

  function success() {
    cancel();
    $('#datatable_serverside').DataTable().ajax.reload(null, false);
  }

  function loadDataTable() {
    $('#datatable_serverside').DataTable({
      processing: true,
      serverSide: true,
      destroy: true,
      scrollX: true,
      order: [[0, 'desc']],
      iDisplayInLength: 10,
      pagingType : 'input',
      ajax: {
        url: "{{ url('admin/publisher_group/' .$group_id. '/access/datatable') }}"
      },
      columns: [
        {
          name: 'id',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'publisher_id',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'code_system',
          className: 'align-middle text-center'
        },
        {
          name: 'system_type',
          className: 'align-middle text-center'
        },
        {
          name: 'created_at',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'updated_at',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'action',
          searchable: false,
          orderable: false,
          className: 'align-middle text-center'
        }
      ]
    });
  }
  $('#publisher_id').on('change', function(){
    $.ajax({
      url: "{{ url('admin/publisher_group/get-publisher') }}" + "/" + $(this).val(),
      success: function(response) {
        $('#system_type').val(response.system_type);
        $('#code_system').val(response.code_system);
      },
      error: function() {
        loadingClose('.modal-content');
        Toast.fire({
          icon: 'error',
          title: 'Server Error!'
        });
      }
    });
  })
  function create() {
    $.ajax({
      url: "{{ url('admin/publisher_group/'.$group_id.'/access/create') }}",
      type: 'POST',
      dataType: 'JSON',
      data: $('#form_data').serialize(),
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      beforeSend: function() {
        loadingOpen('.modal-content');
        $('#validasi_element').hide();
        $('#validasi_content').html('');
      },
      success: function(response) {
        loadingClose('.modal-content');
        if(response.status == 200) {
          success();
          Toast.fire({
            icon: 'success',
            title: response.message
          });
        } else if(response.status == 422) {
          $('#validasi_element').show();
          Toast.fire({
            icon: 'info',
            title: 'Validasi'
          });
          
          $.each(response.error, function(i, val) {
            $('#validasi_content').append('<li>' + val + '</li>');
          })
        } else {
          Toast.fire({
            icon: 'warning',
            title: response.message
          });
        }
      },
      error: function() {
        loadingClose('.modal-content');
        Toast.fire({
          icon: 'error',
          title: 'Server Error!'
        });
      }
    });
  }

  function show(id) {
    toUpdate();
    $.ajax({
      url: "{{ url('admin/publisher_group/'.$group_id.'/access/show') }}" + '/' + id,
      type: 'GET',
      dataType: 'JSON',
      beforeSend: function() {
        loadingOpen('.modal-content');
        $('#validasi_element').hide();
        $('#validasi_content').html('');
      },
      success: function(response) {
        loadingClose('.modal-content');
        $('#publisher_id').val(response.publisher_id).trigger('change');
        $('#code_system').val(response.code_system);
        $('#system_type').val(response.system_type);
        $('#btn_update').attr('onclick', 'update(' + id + ')');
      },
      error: function() {
        loadingClose('.modal-content');
        cancel();
        Toast.fire({
          icon: 'error',
          title: 'Server Error!'
        });
      }
    })
  }

  function update(id) {
    $.ajax({
      url: "{{ url('admin/publisher_group/'.$group_id.'/access/update') }}" + '/' + id,
      type: 'POST',
      dataType: 'JSON',
      data: $('#form_data').serialize(),
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      beforeSend: function() {
        loadingOpen('.modal-content');
        $('#validasi_element').hide();
        $('#validasi_content').html('');
      },
      success: function(response) {
        loadingClose('.modal-content');
        if(response.status == 200) {
          success();
          Toast.fire({
            icon: 'success',
            title: response.message
          });
        } else if(response.status == 422) {
          $('#validasi_element').show();
          Toast.fire({
            icon: 'info',
            title: 'Validasi'
          });
          
          $.each(response.error, function(i, val) {
            $('#validasi_content').append('<li>' + val + '</li>');
          })
        } else {
          Toast.fire({
            icon: 'warning',
            title: response.message
          });
        }
      },
      error: function() {
        loadingClose('.modal-content');
        Toast.fire({
          icon: 'error',
          title: 'Server Error!'
        });
      }
    });
  }

  function destroy(id) {
    Swal.fire({
      title: 'Anda yakin menghapus?',
      text: '',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if(result.value) {
        $.ajax({
          url: "{{ url('admin/publisher_group/'.$group_id.'/access/destroy') }}" + '/' + id,
          type: 'GET',
          dataType: 'JSON',
          success: function(response) {
            if(response.status == 200) {
              success();
              Toast.fire({
                icon: 'success',
                title: response.message
              });
            } else {
              Toast.fire({
                icon: 'warning',
                title: response.message
              });
            }
          },
          error: function() {
            Toast.fire({
              icon: 'error',
              title: 'Server Error!'
            });
          }
        });
      }
    });
  }
</script>