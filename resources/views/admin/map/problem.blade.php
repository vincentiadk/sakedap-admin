<div class="app-content content">
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-6 col-12 mb-2">
				<h3 class="content-header-title mb-1 d-inline-block">Masalah Peta</h3><br>
				<div class="row breadcrumbs-top d-inline-block">
					<div class="breadcrumb-wrapper col-12">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
							<li class="breadcrumb-item"><a href="#">Peta</a></li>
							<li class="breadcrumb-item active">Masalah</li>
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
													<textarea name="title" id="title" class="form-control" style="resize:none;">{{ session('filter.collection.problem.3.title') }}</textarea>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group row">
												<label class="col-md-2">Penerbit</label>
												<div class="col-md-10">
													<select name="publisher_id" id="publisher_id" class="form-control" style="width:100%;">
													@if(!empty(session('filter.collection.problem.3.publisher_id')))
														<option value="{{session('filter.collection.problem.3.publisher_id')}}" selected="selected">{{  App\Models\Publisher::select('name')->where('id',session('filter.collection.problem.3.publisher_id'))->first()->name }}</option>
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
													@if(!empty(session('filter.collection.problem.3.province_id')))
														<option value="{{session('filter.collection.problem.3.province_id')}}" selected="selected">{{ App\Models\Province::where('id', session('filter.collection.problem.3.province_id'))->select('name')->first()->name }}</option>
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
													@if(!empty(session('filter.collection.problem.3.city')))
														<option value="{{session('filter.collection.problem.3.city')}}" selected="selected">{{ App\Models\City::select('name')->where('id',session('filter.collection.problem.3.city'))->first()->name }}</option>
													@endif
													</select>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group row">
												<label class="col-md-2">Tahun Terbit</label>
												<div class="col-md-10">
													<input type="number" name="publication_year" id="publication_year" class="form-control" value="{{ session('filter.collection.problem.3.publication_year') }}" >
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group row">
												<label class="col-md-2">ISBN</label>
												<div class="col-md-10">
													<input type="text" name="code" id="code" class="form-control" value="{{ session('filter.collection.problem.3.code') }}">
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
																	<option value="{{ $i }}" {{ $i == session('filter.collection.problem.3.year_start') ? 'selected' : '' }}>{{ $i }}</option>
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
																	<option value="{{ $i }}" {{ $i == session('filter.collection.problem.3.year_end') ? 'selected' : '' }}>{{ $i }}</option>
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
																			<option value="{{$value}}" {{ $value == session('filter.collection.problem.3.month_start') ? 'selected' : '' }}>{{App\Helper\GeneralHelper::getMonth($value)}}</option>
																		@endforeach
																	</select>
																</div>
																<div class="col-md-6">
																	<select name="month_year_start" id="month_year_start" class="form-control">
																		<option value="">-- Pilih --</option>
																		@for($i = 2018; $i <= date('Y'); $i++)
																			<option value="{{ $i }}" {{ $i == session('filter.collection.problem.3.month_year_start') ? 'selected' : '' }}>{{ $i }}</option>
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
																			<option value="{{$value}}" {{ $value == session('filter.collection.problem.3.month_end') ? 'selected' : '' }}>{{App\Helper\GeneralHelper::getMonth($value)}}</option>
																		@endforeach
																	</select>
																</div>
																<div class="col-md-6">
																	<select name="month_year_end" id="month_year_end" class="form-control">
																		<option value="">-- Pilih --</option>
																		@for($i = 2018; $i <= date('Y'); $i++)
																			<option value="{{ $i }}" {{ $i == session('filter.collection.problem.3.month_year_end') ? 'selected' : '' }}>{{ $i }}</option>
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
															<input type="date" name="day_start" id="day_start" class="form-control" max="{{ date('Y-m-d') }}" value="{{ empty(session('filter.collection.problem.3.day_start'))? date('Y-m-d') : session('filter.collection.problem.3.day_start') }}">
														</div>
														<div class="col-md-1">
															<p style="line-height:40px;" class="text-center">s/d</p>
														</div>
														<div class="col-md-4">
															<input type="date" name="day_end" id="day_end" class="form-control" max="{{ date('Y-m-d') }}" value="{{ empty(session('filter.collection.problem.3.day_end'))? date('Y-m-d') : session('filter.collection.problem.3.day_end') }}">
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
								<h4 class="card-title">Daftar Masalah Peta</h4>
							</div>
							<div class="card-content collapse show">
								<div class="card-body card-dashboard">
									<table class="table table-striped table-bordered display nowrap" id="datatable_serverside">
										<thead class="text-center">
											<tr>
												<th>No</th>
												<th>Penerbit</th>
												<th>Judul</th>
												<th>ISBN</th>
												<th>Status</th>
												<th>Masalah</th>
												<th>Lainnya</th>
												<th>Tanggal</th>
												<th>User</th>
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
		let param = "{{session('filter.collection.problem.3.param')}}"
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
          url: '{{ url("admin/collection/reset_filed/problem/3") }}',
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
			order: [[0, 'desc']],
			iDisplayInLength: 10,
			pagingType: 'input',
			ajax: {
				url: '{{ url("admin/collection/problem/datatable/3") }}',
				data: {
					param: param,
					title: $('#title').val(),
					publisher_id: $('#publisher_id').val(),
					province_id: $('#province_id').val(),
					city: $('#city').val(),
					publication_year: $('#publication_year').val(),
					code: $('#code').val(),
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
					name: 'title',
					className: 'align-middle text-center'
				},
				{
					name: 'code',
					className: 'align-middle text-center'
				},
                {
					name: 'status',
                    searchable: false,
					className: 'align-middle text-center'
				},
				{
					name: 'collection_problem',
					orderable: false,
					searchable: false,
					className: 'align-middle text-center no-nowrap'
				},
				{
					name: 'problem',
					className: 'align-middle text-center no-nowrap'
				},
				{
					name: 'validated_at',
					searchable: false,
					className: 'align-middle text-center'
				},
				{
					name: 'validated_by',
					searchable: false,
					className: 'align-middle text-center'
				}
			]
		});
	}
</script>
