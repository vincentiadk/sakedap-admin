<div class="app-content content">
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-1 d-inline-block">{{$title}}</h3><br>
        <div class="row breadcrumbs-top d-inline-block">
          <div class="breadcrumb-wrapper col-12">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="#">Penerbit</a></li>
              <li class="breadcrumb-item active">Teguran</li>
            </ol>
          </div>
        </div>
      </div>
      <div class="content-header-right col-md-6 col-12 mb-2 mt-1">
        <div class="float-md-right">
          <button type="button" class="btn btn-danger" onclick="reset()"><i class="la la-refresh"></i> Reset Filter</button>
          <a href="{{ url('admin/publisher-warning/create') }}" class="btn btn-primary" style="border-radius:0 !important;"><i class="la la-plus"></i> Tambah Teguran</a>              
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
								<div class="heading-elements">
									<ul class="list-inline mb-0">
										<li><a data-action="collapse"><i class="ft-plus"></i></a></li>
									</ul>
								</div>
							</div>
							<div class="card-content collapse">
								<div class="card-body">
									<div class="row">
										<div class="col-md-12">
											<div class="form-group row">
												<label class="col-md-2">Penerbit</label>
												<div class="col-md-10">
													<select name="publisher_id" id="publisher_id" class="form-control" style="width:100%;">
													@if(!empty(session('filter.collection.manage.1.publisher_id')))
														<option value="{{session('filter.collection.manage.1.publisher_id')}}" selected="selected">{{  App\Models\Publisher::select('name')->where('id',session('filter.collection.manage.1.publisher_id'))->first()->name }}</option>
													@endif
													</select>
												</div>
											</div>
										</div>
                    <div class="col-md-12">
                      <div class="form-group row">
                        <label class="col-md-2">Provinsi</label>
                        <div class="col-md-10">
                          <select name="province_id" id="province_id" class="form-control" style="width:100%;">
                          @if(!empty(session('filter.collection.problem.1.province_id')))
                            <option value="{{session('filter.collection.problem.1.province_id')}}" selected="selected">{{ App\Models\Province::where('id', session('filter.collection.problem.1.province_id'))->select('name')->first()->name }}</option>
                          @endif
                          </select>
                        </div>
                      </div>
                    </div>
										<div class="col-md-12">
											<div class="form-group row">
												<label class="col-md-2">Jenis Teguran</label>
												<div class="col-md-7">
                          <select name="warning_count" id="warning_count" class="form-control" style="width:100%;">
                              <option value="">Semua</option>
                              <option value="1">Teguran ke-1</option>
                              <option value="2">Teguran ke-2</option>
                              <option value="3">Teguran ke-3</option>
                          </select>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <button type="button" class="btn btn-primary col-12" onclick="loadDataTable()"><i class="la la-search"></i> Cari</button>
                          </div>
                        </div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
            <div class="card">
							<div class="card-header">
								<h4 class="card-title">Daftar Teguran</h4>
							</div>
							<div class="card-content collapse show">
								<div class="card-body card-dashboard">
									<table class="table table-striped table-bordered display nowrap" id="datatable_list_serverside">
										<thead class="text-center">
											<tr>
                        <th>No</th>
                        <th>Publisher</th>
                        <th>Tgl Teguran</th>
                        <th>Jenis</th>
                        <th>Asal Teguran</th>
                        <th>Alasan</th>
                        <th>Aksi</th>
											</tr>
										</thead>
									</table>
								</div>
							</div>
						</div>
						<div class="card">
							<div class="card-header">
								<h4 class="card-title">Rekap Teguran</h4>
							</div>
							<div class="card-content collapse show">
								<div class="card-body card-dashboard">
									<table class="table table-striped table-bordered display nowrap" id="datatable_serverside">
										<thead class="text-center">
											<tr>
                        <th>No</th>
                        <th>Penerbit</th>
                        <th>Jumlah Teguran</th>
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
	<div class="modal-dialog modal-md" role="document">
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
        <form id="form_data" method="POST" enctype="multipart/form-data">
          <div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label>Penerbit :</label>
								<input type="hidden" name="id" id="id" class="form-control">
								<input type="text" name="name" id="name" class="form-control" disabled>
							</div>
							<div class="form-group">
								<label>Teguran :</label>
								<input type="text" name="warning" id="warning" class="form-control" disabled>
							</div>
							<div class="form-group">
                <label>Tanggal :</label>
								<input type="date" name="warning_date" id="warning_date" class="form-control">
							</div>
							<div class="form-group">
								<label>Alasan :</label>
								<input type="text" name="reason" id="reason" class="form-control">
							</div>
						</div>
					</div>
					<div class="form-group"><hr></div>
					<div class="form-group">
						<div class="row text-center">
							<div class="col-md-12">
								<div class="form-group">
									<label>Lampiran :</label>
                  <div>
                    <a href="" target="_blank" id="attachment_link" class="text-primary"><i class="la la-file"></i> Lihat File</a>
                    <input type="file" name="attachment" id="attachment" class="form-control">
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" onclick="cancel()" id="btn_cancel">Batal</button>
				<button type="button" class="btn btn-warning" onclick="update()" id="btn_update">Simpan Perubahan</button>
			</div>
		</div>
	</div>
</div>

<script>
   $(function() {
      select2AutoSuggestMultiple('#publisher_id', 'load_publisher');
		  select2AutoSuggestMultiple('#province_id', 'load_province');
      loadDataTable();
   });

   function loadDataTable() {
    $('#datatable_list_serverside').DataTable({
         deferRender: true,
         serverSide: true,
         processing: true,
         iDisplayInLength: 10,
         destroy: true,
         order: [[0, 'desc']],
         scrollX: true,
         ajax: {
            url: '{{ url("admin/publisher-warning/datatable/list") }}',
            type: 'POST',
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
               publisher_id: $('#publisher_id').val(),
               province_id: $('#province_id').val(),
               warning_count: $('#warning_count').val(),
            }
         },
         columns: [
            { name: 'id', searchable: false, className: 'text-center align-middle', orderable: false },
            { name: 'publisher_name', searchable: false, orderable: false },
            { name: 'warning_date' },
            { name: 'warning', searchable: false, orderable: false },
            { name: 'library_name', searchable: false, orderable: false },
            { name: 'reason', searchable: false, orderable: false },
            { name: 'action', searchable: false, orderable: false },
        ],
        order: [[1, 'asc']]
      });

      $('#datatable_serverside').DataTable({
         deferRender: true,
         serverSide: true,
         processing: true,
         iDisplayInLength: 10,
         destroy: true,
         order: [[0, 'desc']],
         scrollX: true,
         ajax: {
            url: '{{ url("admin/publisher-warning/datatable") }}',
            type: 'POST',
            headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
               publisher_id: $('#publisher_id').val(),
               province_id: $('#province_id').val(),
               warning_count: $('#warning_count').val(),
            }
         },
         columns: [
            { name: 'id', searchable: false, className: 'text-center align-middle', orderable: false },
            { name: 'name' },
            { name: 'total_teguran', searchable: false, orderable: false },
        ],
        order: [[1, 'asc']]
      });
   }

  function show(id) {
		toUpdate();
		$.ajax({
			url: '{{ url("admin/publisher-warning/show") }}' + '/' + id,
			type: 'GET',
			dataType: 'JSON',
			beforeSend: function() {
				loadingOpen('.modal-content');
				$('#validasi_element').hide();
				$('#validasi_content').html('');
			},
			success: function(response) {
				loadingClose('.modal-content');
				$('#id').val(response.id);
				$('#name').val(response.name);
				$('#warning').val('Teguran ke-'+response.warning);
				$('#warning_date').val(response.warning_date);
				$('#reason').val(response.reason);
				// $('#attachment_link').val(response.attachment_link);
				$('a#attachment_link').attr('href', response.attachment_link);
				// $('a#statement_letter').attr('href', '{{ url("admin/publisher/stream_pdf") }}' + '/' + id + '/statement_letter');
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

  function update() {
	  var formData = new FormData($('#form_data')[0]);
		$.ajax({
			url: '{{ url("admin/publisher-warning/update") }}' + '/' + $('#id').val(),
			type: 'POST',
			data: formData,
      processData: false,
      contentType: false,
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
					url: '{{ url("admin/publisher-warning/destroy") }}' + '/' + id,
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

  function cancel() {
		reset();
		$('#modal_element').modal('hide');
	}

	function toUpdate() {
		$('#modal_element').modal('show');
	}

	function reset() {
		$('#province_id').val('');
		$('#publisher_id').val('');
		$('#warning_count').val('');
		loadDataTable();
	}

  function success() {
		cancel();
    
		$('#datatable_list_serverside').DataTable().ajax.reload(null, false);
		$('#datatable_serverside').DataTable().ajax.reload(null, false);
	}
</script>