<div class="app-content content">
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-1 d-inline-block">{{ $data['title'] }}</h3><br>
        <div class="row breadcrumbs-top d-inline-block">
          <div class="breadcrumb-wrapper col-12">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ url('publisher/dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="#">Koleksi</a></li>
              <li class="breadcrumb-item active">Diterima</li>
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
        @if(session('success'))
          <div class="alert bg-success alert-icon-left alert-dismissible mb-2" role="alert">
            <span class="alert-icon"><i class="la la-check"></i></span>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <strong>Success!</strong> {{ session('success') }}
          </div>
        @endif
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Filter</h4>
              </div>
              <div class="card-content collapse show">
                <div class="card-body card-dashboard">
                  <div class="row">
                    @if($data['groups'])
                    <div class="col-md-4">
                      <label>Publisher</label>
                      <select name="publisher_id" id="publisher_id" class="form-control select2" multiple="multiple">
                        @foreach($data['groups']->groups as $key => $item)
                          <option value="{{ $item->publisher->id }}">{{ $item->publisher->name }}</option>
                        @endforeach
                      </select>
                    </div>
                    @endif
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Tipe Koleksi</label>
                        <select class="select2 form-control" name="type[]" id="tipekoleksi" multiple="multiple">
                          <option value="1">Buku</option>
                          <option value="2">Partitur</option>
                          <option value="3">Peta</option>
                          <option value="4">Serial</option>
                          <option value="5">Audio</option>
                          <option value="6">Film / Audio Visual</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-4">
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
                <h4 class="card-title">Daftar Koleksi Diterima</h4>
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
                        <th>Tipe Koleksi</th>
                        <th>Pelaksana</th>
                        <th>Judul</th>
                        <th>Identifier</th>
                        <th>Tanggal</th>
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
@include('publisher.collection.request_file')
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
    $('#tipekoleksi').val('').trigger('change');
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
        url: '{{ url("publisher/collection/accepted/datatable/") }}',
        data: {
          periode_start: $('#periode_start').val(),
          periode_end: $('#periode_end').val(),
          type: $('#tipekoleksi').val(),
          publisher_id: $('#publisher_id').val()
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
          name: 'request_file_original',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'request_receipt',
          searchable: false,
          className: 'align-middle text-center'
        }
      ]
    });
  }
</script>
