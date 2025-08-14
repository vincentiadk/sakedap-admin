<style type="text/css">
  .text-muted {
    font-size: 10rem !important;
  }
</style>
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
              <li class="breadcrumb-item active">Unggah ISBN</li>
            </ol>
          </div>
        </div>
      </div>
      <div class="content-header-right col-md-6 col-12 mb-2 mt-1">
        <div class="float-md-right">
          <a type="button" class="btn btn-info rounded-circle" href="{{ url('main/Panduan%20Penggunaan%20Aplikasi%20eDeposit%20-%20Unggah%20Buku%20E-ISBN.pdf') }} "><i class="la la-question"></i></a>
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
                <h4 class="card-title">Unggah Koleksi ISBN</h4>
                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                
              </div>
              <div class="card-content collapse show">
                <div class="card-body card-dashboard">
                  <div class="col-md-12">
                      <form action="{{ route('isbn.upload') }}" method="post" enctype="multipart/form-data" id="image-upload" class="dropzone">
                        @csrf
                        <div class="dz-message d-flex flex-column">
                          <i class="la la-cloud text-muted"></i>
                          Drag &amp; Drop here or click
                        </div>
                      </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Daftar Unggah ISBN</h4>
                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                <div class="heading-elements">
                  <ul class="list-inline mb-0">
                    <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                    <li><a data-action="reload" onclick="loadDataTable()"><i class="ft-rotate-cw"></i></a></li>
                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                    <button type="button" class="btn btn-primary" onclick="submit()">Submit Koleksi</button>
                  </ul>
                </div>
              </div>
              <div class="card-content collapse show">
                <div class="card-body card-dashboard">
                  <table class="table table-striped table-bordered display" style="width: 1500px;" id="datatable_serverside">
                    <thead class="text-center">
                      <tr>
                        <th>Aksi</th>
                        <th width="400px">Judul</th>
                        <th width="400px">Penerbit</th>
                        <th width="200px">Kode</th>
                        <th>Cover</th>
                        <th>File Original</th>
                        <th>Tgl Unggah</th>
                        <th>Bulan Terbit</th>
                        <th>Tahun Terbir</th>
                        <th>Deskripsi</th>
                        <th>Kota Terbit</th>
                        <th>Preview</th>
                        <th>Hak Akses</th>
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
<div class="modal fade" id="modal_collection" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Ubah Koleksi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="body_collection">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="update_collection">Update</button>
            </div>
        </div>
    </div>
</div>
<script>

  var countContributor = 0;
  Dropzone.autoDiscover = false;
  var url;

  $(document).ready(function(){
      $("#image-upload").dropzone({
        init: function() {
          this.on("complete", file => {
            loadDataTable()
          });
        },
        parallelUploads : 1,
        maxFilesize     : 500,
        acceptedFiles   : ".jpeg,.jpg,.png,.pdf,.zip,.epub,.mp3",
        addRemoveLinks  : true,
      });

      $('#update_collection').click(function() {
        let form = $("#iframe_collection").contents().find("#form_collection")
        var formData = new FormData(form[0]);
        $.ajax({
          url: url + '/update',
          type: 'POST',
          dataType: 'JSON',
          data: formData,
          cache: false,
          contentType: false,
          processData: false,
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          beforeSend: function() {
            loadingOpen('#configuration');
          },
          success: function(response) {
           $('#modal_collection').modal('hide')
           loadingClose('#configuration');
           loadDataTable()
          },
          error: function(response) {
            console.log(response)
            loadingClose('#configuration');
            Toast.fire({
                icon: 'error',
                title: 'Server Error!'
            });
          }
        });
      })
  });

  $(function() {
    loadDataTable();
  });

  function loadDataTable() {
    $('#datatable_serverside').DataTable({
      processing: true,
      serverSide: true,
      destroy: true,
      scrollX: true,
      autoWidth: false,
      lengthMenu: [10, 25, 50, 75, 100],
      ajax: {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '{{ url("publisher/collection/isbn/datatable") }}',
      },
      columns: [
        {
          name: 'Edit',
          searchable: false,
        },
        {
          name: 'title',
        },
        {
          name: 'publisher',
          searchable: false,
        },
        {
          name: 'code',
          className: 'align-middle text-center'
        },
        {
          name: 'cover',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'file',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'created_at',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'publication_month',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'publication_year',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'desc',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'city',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'preview',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'acess',
          searchable: false,
        },
      ],
      "drawCallback": function( settings ) {
        $('#datatable_serverside').DataTable().columns.adjust();
      }
    });
  }

  function editCollection(id) {
    url = "{{ url('publisher/collection/isbn/') }}" + '/' + id;
    $('#body_collection').html('<iframe id="iframe_collection" src="'+url+'" style="width: 100%; height: 80vh;" ></iframe>')
    $('#modal_collection').modal('show');
  }

  function deleteCollection(id) {
    let url = "{{ url('publisher/collection/isbn/') }}" + '/' + id;
    $.ajax({
        url: url + '/delete',
        type: 'POST',
        dataType: 'JSON',
        cache: false,
        contentType: false,
        processData: false,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
          beforeSend: function() {
          loadingOpen('#configuration');
        },
        success: function(response) {
         loadingClose('#configuration');
         Toast.fire({
              icon: 'success',
              title: 'Sukses Hapus Koleksi!'
          });
         loadDataTable()
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

  function submit() {
    $.ajax({
        url: "{{ url('publisher/collection/isbn/submit') }}",
        type: 'POST',
        dataType: 'JSON',
        cache: false,
        contentType: false,
        processData: false,
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
          loadingOpen('#configuration');
        },
        success: function(response) {
         loadingClose('#configuration');
         Toast.fire({
              icon: 'success',
              title: response.message
          });
         loadDataTable()
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