<div class="app-content content">
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-6 col-12 mb-2">
				<h3 class="content-header-title mb-1 d-inline-block">Pengelolaan Audio</h3><br>
				<div class="row breadcrumbs-top d-inline-block">
					<div class="breadcrumb-wrapper col-12">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
							<li class="breadcrumb-item"><a href="#">Audio</a></li>
							<li class="breadcrumb-item active">Pengelolaan</li>
						</ol>
					</div>
				</div>
			</div>
			<div class="content-header-right col-md-6 col-12 mb-2 mt-1">
				<div class="float-md-right">
					<button type="button" class="btn btn-danger btn-sm" onclick="reset()"><i class="la la-refresh"></i> Reset Filter</button>
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
												<label class="col-md-2">Judul</label>
												<div class="col-md-10">
													<textarea name="title" id="title" class="form-control" style="resize:none;">{{ session('filter.collection.manage.5.title') }}</textarea>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group row">
												<label class="col-md-2">Penerbit</label>
												<div class="col-md-10">
													<select name="publisher_id" id="publisher_id" class="form-control" style="width:100%;">
													@if(!empty(session('filter.collection.manage.5.publisher_id')))
														<option value="{{session('filter.collection.manage.5.publisher_id')}}" selected="selected">{{  App\Models\Publisher::select('name')->where('id',session('filter.collection.manage.5.publisher_id'))->first() ? App\Models\Publisher::select('name')->where('id',session('filter.collection.manage.5.publisher_id'))->first()->name : '' }}</option>
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
													@if(!empty(session('filter.collection.manage.5.province_id')))
														<option value="{{session('filter.collection.manage.5.province_id')}}" selected="selected">{{ App\Models\Province::where('id', session('filter.collection.manage.5.province_id'))->select('name')->first()->name }}</option>
													@endif
													</select>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group row">
												<label class="col-md-2">Tempat Terbit</label>
												<div class="col-md-10">
                                                    <select name="city" id="city" class="form-control" style="width:100%;">
													@if(!empty(session('filter.collection.manage.5.city')))
														<option value="{{session('filter.collection.manage.5.city')}}" selected="selected">{{ App\Models\ City::select('name')->where('id',session('filter.collection.manage.5.city'))->first()->name }}</option>
													@endif
													</select>
												</div>
											</div>
										</div>
                                        <div class="col-md-12">
											<div class="form-group row">
												<label class="col-md-2">Dikelola</label>
												<div class="col-md-10">
												@php $manages = array(''=>'Semua','1'=>'Sudah','2'=>'Belum')  @endphp
                                                    <select name="manage" id="manage" class="form-control">
														@foreach($manages as $key =>$value)
															<option value="{{$key}}" {{ $key == session('filter.collection.manage.5.manage') ? 'selected' : '' }}>{{$value}}</option>
														@endforeach
                                                    </select>
												</div>
											</div>
										</div>
                                        <div class="col-md-12">
											<div class="form-group row">
												<label class="col-md-2">Validasi</label>
												<div class="col-md-10">
												@php $validated = array(''=>'Semua','1'=>'Sudah','2'=>'Belum')  @endphp
                                                    <select name="validated" id="validated" class="form-control">
														@foreach($validated as $key =>$value)
															<option value="{{$key}}" {{ $key == session('filter.collection.manage.5.validated') ? 'selected' : '' }}>{{$value}}</option>
														@endforeach
                                                    </select>
												</div>
											</div>
										</div>
                                        <div class="col-md-12">
											<div class="form-group row">
												<label class="col-md-2">Edited</label>
												<div class="col-md-10">
												@php $edited = array(''=>'Semua','1'=>'Sudah','2'=>'Belum')  @endphp
                                                    <select name="edited" id="edited" class="form-control">
														@foreach($validated as $key =>$value)
															<option value="{{$key}}" {{ $key == session('filter.collection.manage.5.edited') ? 'selected' : '' }}>{{$value}}</option>
														@endforeach
                                                    </select>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group row">
												<label class="col-md-2">Tahun Terbit</label>
												<div class="col-md-10">
													<input type="number" name="publication_year" id="publication_year" class="form-control" value="{{ session('filter.collection.manage.5.publication_year') }}" > 
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group row">
												<label class="col-md-2">ISRC</label>
												<div class="col-md-10">
													<input type="text" name="code" id="code" class="form-control" value="{{ session('filter.collection.manage.5.code') }}">
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group row">
												<label class="col-md-2">Tahunan</label>
												<div class="col-md-10">
													<div class="row">
														<div class="col-md-4">
															<select name="year_start" id="year_start" class="form-control">
																<option value="">-- Pilih --</option>
																@for($i = 2018; $i <= date('Y'); $i++)
																	<option value="{{ $i }}" {{ $i == session('filter.collection.manage.5.year_start') ? 'selected' : '' }}>{{ $i }}</option>
																@endfor
															</select>
														</div>
														<div class="col-md-1">
															<p style="line-height:40px;" class="text-center">s/d</p>
														</div>
														<div class="col-md-4">
															<select name="year_end" id="year_end" class="form-control">
																<option value="">-- Pilih --</option>
																@for($i = 2018; $i <= date('Y'); $i++)
																	<option value="{{ $i }}" {{ $i == session('filter.collection.manage.5.year_end') ? 'selected' : '' }}>{{ $i }}</option>
																@endfor
															</select>
														</div>
														<div class="col-md-3">
															<div class="form-group">
																<button type="button" class="btn btn-primary col-12" onclick="filter('annual')"><i class="la la-search"></i> Cari</button>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group row">
												<label class="col-md-2">Bulanan</label>
												<div class="col-md-10">
													<div class="row">
														<div class="col-md-4">
															<div class="row">
																<div class="col-md-6">
																	@php $month = array('01','02','03','04','05','06','07','08','09','10','11','12') @endphp
																	<select name="month_start" id="month_start" class="form-control">
																		<option value="">-- Pilih --</option>
																		@foreach($month as $key =>$value)
																			<option value="{{$value}}" {{ $value == session('filter.collection.manage.5.month_start') ? 'selected' : '' }}>{{App\Helper\GeneralHelper::getMonth($value)}}</option>
																		@endforeach
																	</select>
																</div>
																<div class="col-md-6">
																	<select name="month_year_start" id="month_year_start" class="form-control">
																		<option value="">-- Pilih --</option>
																		@for($i = 2018; $i <= date('Y'); $i++)
																			<option value="{{ $i }}" {{ $i == session('filter.collection.manage.5.month_year_start') ? 'selected' : '' }}>{{ $i }}</option>
																		@endfor
																	</select>
																</div>
															</div>
														</div>
														<div class="col-md-1">
															<p style="line-height:40px;" class="text-center">s/d</p>
														</div>
														<div class="col-md-4">
															<div class="row">
																<div class="col-md-6">
																	<select name="month_end" id="month_end" class="form-control">
																		<option value="">-- Pilih --</option>
																		@foreach($month as $key =>$value)
																			<option value="{{$value}}" {{ $value == session('filter.collection.manage.5.month_end') ? 'selected' : '' }}>{{App\Helper\GeneralHelper::getMonth($value)}}</option>
																		@endforeach
																	</select>
																</div>
																<div class="col-md-6">
																	<select name="month_year_end" id="month_year_end" class="form-control">
																		<option value="">-- Pilih --</option>
																		@for($i = 2018; $i <= date('Y'); $i++)
																			<option value="{{ $i }}" {{ $i == session('filter.collection.manage.5.month_year_end') ? 'selected' : '' }}>{{ $i }}</option>
																		@endfor
																	</select>
																</div>
															</div>
														</div>
														<div class="col-md-3">
															<div class="form-group">
																<button type="button" class="btn btn-primary col-12" onclick="filter('monthly')"><i class="la la-search"></i> Cari</button>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group row">
												<label class="col-md-2">Harian</label>
												<div class="col-md-10">
													<div class="row">
														<div class="col-md-4">
															<input type="date" name="day_start" id="day_start" class="form-control" max="{{ date('Y-m-d') }}" value="{{ empty(session('filter.collection.manage.5.day_start'))? date('Y-m-d') : session('filter.collection.manage.5.day_start') }}">
														</div>
														<div class="col-md-1">
															<p style="line-height:40px;" class="text-center">s/d</p>
														</div>
														<div class="col-md-4">
															<input type="date" name="day_end" id="day_end" class="form-control" max="{{ date('Y-m-d') }}" value="{{ empty(session('filter.collection.manage.5.day_end'))? date('Y-m-d') : session('filter.collection.manage.5.day_end') }}">
														</div>
														<div class="col-md-3">
															<div class="form-group">
																<button type="button" class="btn btn-primary col-12" onclick="filter('daily')"><i class="la la-search"></i> Cari</button>
															</div>
														</div>
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
								<h4 class="card-title">Daftar Pengelolaan Audio</h4>
							</div>
							<div class="card-content collapse show">
								<div class="card-body card-dashboard">
									<table class="table table-striped table-bordered display nowrap" id="datatable_serverside">
										<thead class="text-center">
											<tr>
												<th>Edit</th>
												<th>No</th>
												<th>Pengelolaan</th>
												<th>Terkunci</th>
												<th>Deposit</th>
												<th>Label</th>
												<th>Judul</th>
												<th>ISRC</th>
												<th>Update</th>
												<th>Validator</th>
												<th>Tanggal</th>
												<th>Hapus</th>
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
		let param = "{{session('filter.collection.manage.5.param')}}"
		select2AutoSuggest('#publisher_id', 'load_publisher');
		select2AutoSuggest('#province_id', 'load_province');
        filter(param);

        $('#province_id').change(function() {
            if($('#province_id').val() == '') {
                $('#city').html('');
                $('#city').val('');
            } else {
                select2AutoSuggest('#city', 'load_city/' + $('#province_id').val());
            }
        });
	});

	function filter(param = '') {
		var year_start       = $('#year_start');
		var year_end         = $('#year_end');
		var month_start      = $('#month_start');
		var month_end        = $('#month_end');
		var month_year_start = $('#month_year_start');
		var month_year_end   = $('#month_year_end');
		var day_start        = $('#day_start');
		var day_end          = $('#day_end');

		if(param == 'annual') {
			month_start.val('');
			month_end.val('');
			month_year_start.val('');
			month_year_end.val('');
			day_start.val('');
			day_end.val('');

			if(year_start.val() && year_end.val()) {
				loadDataTable(param);
			} else {
				Swal.fire('Ooopss!!', 'Harap mengisi tahun awal dan tahun akhir.', 'warning');
			}
		} else if(param == 'monthly') {
			year_start.val('');
			year_end.val('');
			day_start.val('');
			day_end.val('');

			if(month_start.val() && month_year_start.val() && month_end.val() && month_year_end.val()) {
				loadDataTable(param);
			} else {
				Swal.fire('Ooopss!!', 'Harap mengisi bulan tahun awal dan bulan tahun akhir.', 'warning');
			}
		} else if(param == 'daily') {
			year_start.val('');
			year_end.val('');
			month_start.val('');
			month_end.val('');
			month_year_start.val('');
			month_year_end.val('');

			if(day_start.val() && day_end.val()) {
				loadDataTable(param);
			} else {
				Swal.fire('Ooopss!!', 'Harap mengisi harian awal dan harian akhir.', 'warning');
			}
		} else {
			// reset();
			loadDataTable();
		}
	}

	function reset() {
		$.ajax({
          url: '{{ url("admin/collection/reset_filed/manage/5") }}',
          type: 'GET',
          contentType: false,
          processData: false,
          success: function(response) {
			$('#title').val('');
			$('#publisher_id').val('').trigger('change');
			$('#province_id').val('').trigger('change');
			$('#city').val('').trigger('change');
			$('#publication_year').val('');
			$('#code').val('');
			$('#manage').val('');
			$('#validated').val('');
			$('#edited').val('');
			$('#year_start').val('');
			$('#year_end').val('');
			$('#month_start').val('');
			$('#month_end').val('');
			$('#month_year_start').val('');
			$('#month_year_end').val('');
			$('#day_start').val('');
			$('#day_end').val('');
			loadDataTable();
          },
          error: function() {
              Toast.fire({
                  icon: 'error',
                  title: 'Server Error!'
              });
          }
      });
	}

	function loadDataTable(param = '') {
		$('#datatable_serverside').DataTable({
			processing: true,
			serverSide: true,
			destroy: true,
			scrollX: true,
			order: [[1, 'desc']],
			iDisplayInLength: 10,
			pagingType: 'input',
			ajax: {
				url: '{{ url("admin/collection/manage/datatable/5") }}',
				data: {
					param: param,
					title: $('#title').val(),
					publisher_id: $('#publisher_id').val(),
					province_id: $('#province_id').val(),
					city: $('#city').val(),
					publication_year: $('#publication_year').val(),
					code: $('#code').val(),
					manage: $('#manage').val(),
					validated: $('#validated').val(),
					edited: $('#edited').val(),
					year_start: $('#year_start').val(),
					year_end: $('#year_end').val(),
					month_start: $('#month_start').val(),
					month_end: $('#month_end').val(),
					month_year_start: $('#month_year_start').val(),
					month_year_end: $('#month_year_end').val(),
					day_start: $('#day_start').val(),
					day_end: $('#day_end').val()
				}
			},
			columns: [
                {
					name: 'edit',
					searchable: false,
					orderable: false,
					className: 'align-middle text-center'
				},
				{
					name: 'id',
					searchable: false,
					className: 'align-middle text-center'
				},
                {
					name: 'manage_by',
                    searchable: false,
					className: 'align-middle text-center'
				},
                {
					name: 'lock',
                    searchable: false,
					className: 'align-middle text-center'
				},
				{
					name: 'deposit',
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
					name: 'updated_by',
					searchable: false,
					className: 'align-middle text-center'
				},
				{
					name: 'validated_by',
					searchable: false,
					className: 'align-middle text-center'
				},
				{
					name: 'received_at',
					searchable: false,
					className: 'align-middle text-center'
				},
				{
					name: 'delete',
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
					url: '{{ url("admin/collection/destroy") }}' + '/' + id,
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
