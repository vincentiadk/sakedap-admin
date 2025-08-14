<div class="app-content content">
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-6 col-12 mb-2">
				<h3 class="content-header-title mb-1 d-inline-block">Laporan Pengiriman KCKR Analog</h3><br>
				<div class="row breadcrumbs-top d-inline-block">
					<div class="breadcrumb-wrapper col-12">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
							<li class="breadcrumb-item"><a href="#">Laporan</a></li>
							<li class="breadcrumb-item active">Penerbit</li>
						</ol>
					</div>
				</div>
			</div>
			<div class="content-header-right col-md-6 col-12 mb-2 mt-1">
				<div class="float-md-right">
					<button type="button" class="btn btn-danger btn-sm" onclick="reset()"><i class="la la-refresh"></i> Reset Filter</button>
					<button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal_filter"><i class="la la-search"></i> Filter</button>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="configuration">
				<div class="row">
					<div class="col-12">
						<div class="card">
							<div class="card-header">
								<div class="row">
									<div class="col-md-6">
										<h4 class="card-title">
											Daftar Laporan KCKR Analog
										</h4>
									</div>
									<div class="col-md-6 text-right">
										<h4 class="card-title">
											<a href="#" class="btn btn-success btn-sm" id="download_excel" onclick="downloadExcel()"><i class="la la-folder"></i> Download Excel</a>
										</h4>
									</div>
								</div>
							</div>
							<div class="card-content collapse show">
								<div class="card-body card-dashboard">
									<table class="table table-striped table-bordered display nowrap" id="datatable_serverside_summary">
										<thead class="text-center">
											<tr>
												<th>No</th>
												<th>Penerbit</th>
												<th>Ekspedisi</th>
												<th>Jenis</th>
												<th>ISBN/ISSN</th>
												<th>Judul</th>
												<th>Pengarang</th>
												<th>Tanda Registrasi Karya</th>
												<th>Bulan / Tahun Terbit</th>
												<th>Jumlah Perpusnas</th>
												<th>Jumlah Provinsi</th>
												<th>Tanggal Kirim Perpusnas</th>
												<th>Tanggal Terima Perpusnas</th>
												<th>Tanggal Kirim Provinsi</th>
												<th>Tanggal Terima Provinsi</th>
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

<div class="modal fade" id="modal_filter" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Filter</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">

            		<div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-md-2">Perpustakaan</label>
                            <div class="col-md-10">
                                <select name="library_id" id="library_id" class="form-control" style="width:100%;"></select>
                            </div>
                        </div>
                    </div>
					<div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-md-2">Ekspedisi</label>
							<div class="col-md-10">
                                <select name="expedition_id" id="expedition_id" class="form-control">
									<option value="" selected>Semua</option>
									@foreach ($expedition as $e)
										<option value="{{ $e->id }}">
											{{ $e->name }}
										</option>
									@endforeach
                                </select>
							</div>
                        </div>
                    </div>
					<div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-md-2">Penerbit</label>
                            <div class="col-md-10">
                                <select name="publisher_id" id="publisher_id" class="form-control" style="width:100%;"></select>
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
                                                <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
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
                                                <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
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
                                                <select name="month_start" id="month_start" class="form-control">
                                                    <option value="">-- Pilih --</option>
                                                    <option value="01">{{ App\Helper\GeneralHelper::getMonth('01') }}</option>
                                                    <option value="02">{{ App\Helper\GeneralHelper::getMonth('02') }}</option>
                                                    <option value="03">{{ App\Helper\GeneralHelper::getMonth('03') }}</option>
                                                    <option value="04">{{ App\Helper\GeneralHelper::getMonth('04') }}</option>
                                                    <option value="05">{{ App\Helper\GeneralHelper::getMonth('05') }}</option>
                                                    <option value="06">{{ App\Helper\GeneralHelper::getMonth('06') }}</option>
                                                    <option value="07">{{ App\Helper\GeneralHelper::getMonth('07') }}</option>
                                                    <option value="08">{{ App\Helper\GeneralHelper::getMonth('08') }}</option>
                                                    <option value="09">{{ App\Helper\GeneralHelper::getMonth('09') }}</option>
                                                    <option value="10">{{ App\Helper\GeneralHelper::getMonth('10') }}</option>
                                                    <option value="11">{{ App\Helper\GeneralHelper::getMonth('11') }}</option>
                                                    <option value="12">{{ App\Helper\GeneralHelper::getMonth('12') }}</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <select name="month_year_start" id="month_year_start" class="form-control">
                                                    <option value="">-- Pilih --</option>
                                                    @for($i = 2018; $i <= date('Y'); $i++)
                                                        <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
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
                                                    <option value="01">{{ App\Helper\GeneralHelper::getMonth('01') }}</option>
                                                    <option value="02">{{ App\Helper\GeneralHelper::getMonth('02') }}</option>
                                                    <option value="03">{{ App\Helper\GeneralHelper::getMonth('03') }}</option>
                                                    <option value="04">{{ App\Helper\GeneralHelper::getMonth('04') }}</option>
                                                    <option value="05">{{ App\Helper\GeneralHelper::getMonth('05') }}</option>
                                                    <option value="06">{{ App\Helper\GeneralHelper::getMonth('06') }}</option>
                                                    <option value="07">{{ App\Helper\GeneralHelper::getMonth('07') }}</option>
                                                    <option value="08">{{ App\Helper\GeneralHelper::getMonth('08') }}</option>
                                                    <option value="09">{{ App\Helper\GeneralHelper::getMonth('09') }}</option>
                                                    <option value="10">{{ App\Helper\GeneralHelper::getMonth('10') }}</option>
                                                    <option value="11">{{ App\Helper\GeneralHelper::getMonth('11') }}</option>
                                                    <option value="12">{{ App\Helper\GeneralHelper::getMonth('12') }}</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <select name="month_year_end" id="month_year_end" class="form-control">
                                                    <option value="">-- Pilih --</option>
                                                    @for($i = 2018; $i <= date('Y'); $i++)
                                                        <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
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
                                        <input type="date" name="day_start" id="day_start" class="form-control" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-1">
                                        <p style="line-height:40px;" class="text-center">s/d</p>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="date" name="day_end" id="day_end" class="form-control" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
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
</div>

<script>
	$(function() {
		filter();
		select2AutoSuggest('#library_id', 'load_library');
		select2AutoSuggest('#publisher_id', 'load_publisher');
	});

	function filter(param = '') {
		$('#download_excel').attr('onclick', 'downloadExcel("' + param + '")');

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
				$('#modal_filter').modal('hide');
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
				$('#modal_filter').modal('hide');
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
				$('#modal_filter').modal('hide');
			} else {
				Swal.fire('Ooopss!!', 'Harap mengisi harian awal dan harian akhir.', 'warning');
			}
		} else {
			loadDataTable(param);
		}
	}

	function reset() {
		$('#publisher_id').val('').trigger('change');
		$('#library_id').val('').trigger('change');
		$('#expedition_id').val('');
		$('#year_start').val('');
		$('#year_end').val('');
		$('#month_start').val('');
		$('#month_end').val('');
		$('#month_year_start').val('');
		$('#month_year_end').val('');
		$('#day_start').val('');
		$('#day_end').val('');
		filter();
	}

	function loadDataTable(param = '') {
		$('#datatable_serverside_summary').DataTable({
			processing: true,
			serverSide: true,
			destroy: true,
			scrollX: true,
			order: [[0, 'desc']],
			iDisplayInLength: 10,
			pagingType: 'input',
			ajax: {
				url: '{{ url("admin/report/collection_delivery/datatable") }}',
				type: 'post',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
				data: {
					param: param,
					type: $('#type').val(),
					collection: $('#collection').val(),
					publisher_id: $('#publisher_id').val(),
					library_id: $('#library_id').val(),
					expedition_id: $('#expedition_id').val(),
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
					name: 'No',
					searchable: false,
					className: 'align-middle text-center'
				},
				{
					name: 'publisher',
					searchable: true,
					className: 'align-middle text-center'
				},
				{
					name: 'expedition',
					searchable: true,
					className: 'align-middle text-center'
				},
				{
					name: 'Jenis',
					searchable: true,
					className: 'align-middle text-center'
				},
				{
					name: 'ISBN/ISSN',
					searchable: true,
					className: 'align-middle text-center'
				},
				{
					name: 'Judul',
					searchable: true,
					className: 'align-middle text-center'
				},
				{
					name: 'Pengarang',
					searchable: true,
					className: 'align-middle text-center'
				},
				{
					name: 'Tanda Registrasi Karya',
					searchable: true,
					className: 'align-middle text-center'
				},
				{
					name: 'Bulan / Tahun Terbit',
					searchable: true,
					className: 'align-middle text-center'
				},
				{
					name: 'Jumlah Perpusnas',
					searchable: true,
					className: 'align-middle text-center'
				},
				{
					name: 'Jumlah Provinsi',
					searchable: true,
					className: 'align-middle text-center'
				},
				{
					name: 'Tanggal Kirim Perpusnas',
					searchable: true,
					className: 'align-middle text-center'
				},
				{
					name: 'Tanggal Terima Perpusnas',
					searchable: true,
					className: 'align-middle text-center'
				},
				{
					name: 'Tanggal Kirim Provinsi',
					searchable: true,
					className: 'align-middle text-center'
				},
				{
					name: 'Tanggal Terima Provinsi',
					searchable: true,
					className: 'align-middle text-center'
				},
			]
		});
	}

	function downloadExcel(param = '') {
		$.ajax({
			url: '{{ url("admin/report/file_download/processing") }}',
			type: 'POST',
			dataType: 'JSON',
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			beforeSend: function() {
				loadingOpen('body');
			},
			data: {
				param: param,
				type: $('#type').val(),
				collection: $('#collection').val(),
				province_id: $('#province_id').val(),
				year_start: $('#year_start').val(),
				year_end: $('#year_end').val(),
				month_start: $('#month_start').val(),
				month_end: $('#month_end').val(),month_year_start: $('#month_year_start').val(),
				month_year_end: $('#month_year_end').val(),
				day_start: $('#day_start').val(),
				day_end: $('#day_end').val(),
				slug: 'collection_delivery'
			},
			success: function(response) {
				loadingClose('body');
				Swal.fire('Sukses!!', 'Sedang diproses.', 'success');
			}
		});
	}
</script>
