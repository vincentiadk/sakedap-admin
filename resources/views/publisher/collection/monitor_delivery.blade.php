<div class="app-content content">
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-1 d-inline-block">Pengiriman KC dan KR Analog</h3><br>
        <div class="row breadcrumbs-top d-inline-block">
          <div class="breadcrumb-wrapper col-12">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ url('publisher/dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="#">Koleksi</a></li>
              <li class="breadcrumb-item active">Pengiriman</li>
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
                        <label>Ekspedisi</label>
                        <select class="form-control" name="expedition" id="tipeekspedisi">
                          <option value="" selected>Semua</option>
                          @foreach ($expedition as $e)
														<option value="{{ $e->id }}">
															{{ $e->name }}
														</option>
													@endforeach
                        </select>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>No Resi :</label>
                        <input type="text" name="receipt_no" id="receipt_no" placeholder="No Resi" class="form-control">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Status :</label>
                        <select class="form-control" name="status" id="status">
                          <option value="" selected>Semua</option>
                          <option value="DRAFT" >DRAFT</option>
                          <option value="DELIVERED" >DELIVERED</option>
                          <option value="ACCEPTED" >ACCEPTED</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Tanggal Pengiriman :</label>
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
                <h4 class="card-title">Daftar Pengiriman KC dan KR Analog</h4>
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
                        <th>Nama Penerbit</th>
                        <th>Jumlah Judul</th>
                        <th>Jumlah Eksemplar</th>
                        <th>Status</th>
                        <th>Ekspedisi</th>
                        <th>No Resi</th>
                        <th>Tanggal Kirim</th>
                        <th>Tujuan</th>
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

    // $('#tipeekspedisi').select2({
    //     placeholder: '-- Pilih Tipe Ekpedisi --',
    //     allowClear: true,
    //     multiple: false,
    //     cache: true,
    // });

    $('#publisher_id').select2({
        placeholder: '-- Pilih Publisher --',
        allowClear: true,
        multiple: false,
        cache: true,
    });

    // $("#tipeekspedisi").select2("val", "{{ Request::input('expedition') }}");
    $("#publisher_id").select2("val", "{{ Request::input('publisher_id') }}");

    loadDataTable();
  });

  function reset() {
    $('#periode_start').val('');
    $('#periode_end').val('');
    $('#tipeekspedisi').val('');
    $('#receipt_no').val('');
    $('#status').val('');
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
        url: '{{ url("publisher/collection/delivery/datatable") }}',
        data: {
          periode_start: $('#periode_start').val(),
          periode_end: $('#periode_end').val(),
          expedition_id: $('#tipeekspedisi').val(),
          receipt_no: $('#receipt_no').val(),
          status: $('#status').val(),
        }
      },
      columns: [
        {
          name: 'publisher',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'count_title',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'count_exemplar',
          className: 'align-middle text-center'
        },
        {
          name: 'status',
          className: 'align-middle text-center'
        },  
        {
          name: 'expedition',
          className: 'align-middle text-center'
        },  
        {
          name: 'receipt_no',
          className: 'align-middle text-center'
        },
        {
          name: 'delivery_date',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'library_id',
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
					url: '{{ url("publisher/collection/delivery/destroy") }}' + '/' + id,
					type: 'GET',
					dataType: 'JSON',
					success: function(response) {
						if(response.status == 200) {
							$('#datatable_serverside').DataTable().ajax.reload(null, false);
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