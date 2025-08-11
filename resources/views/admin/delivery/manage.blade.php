<div class="app-content content">
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-6 col-12 mb-2">
				<h3 class="content-header-title mb-1 d-inline-block">{{$title}}</h3><br>
				<div class="row breadcrumbs-top d-inline-block">
					<div class="breadcrumb-wrapper col-12">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
							<li class="breadcrumb-item"><a href="#">Koleksi</a></li>
							<li class="breadcrumb-item active">Daftar Kirim</li>
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
												<label class="col-md-2">No Resi</label>
												<div class="col-md-10">
													<input type="text" name="receipt_no" id="receipt_no" class="form-control" />
												</div>
											</div>
										</div>
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
												<label class="col-md-2">Tanggal Kirim</label>
												<div class="col-md-10">
													<div class="row">
														<div class="col-md-4">
															<input type="date" name="day_start" id="day_start" class="form-control" max="{{ date('Y-m-d') }}">
														</div>
														<div class="col-md-1">
															<p style="line-height:40px;" class="text-center">s/d</p>
														</div>
														<div class="col-md-4">
															<input type="date" name="day_end" id="day_end" class="form-control" max="{{ date('Y-m-d') }}">
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
								<h4 class="card-title">Daftar Kirim KC dan KR Analog</h4>
							</div>
							<div class="card-content collapse show">
								<div class="card-body card-dashboard">
									<table class="table table-striped table-bordered display nowrap" id="datatable_serverside">
										<thead class="text-center">
											<tr>
												<th>No</th>
												<th>Aksi</th>
												<th>Penerbit</th>
												<th>Jumlah</br>Judul</th>
												<th>Jumlah</br>Eksemplar</th>
												<th>Status</th>
												<th>Ekspedisi</th>
												<th>Nomor Resi /</br> Nama Pengirim</th>
												<th>Tanggal Kirim</th>
												<th>Tujuan</th>
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
		let param = "{{session('filter.collection.manage.1.param')}}"
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
		loadDataTable();
	}

	function reset() {
		$('#receipt_no').val('');
		$('#publisher_id').val('').trigger('change');
		$('#expedition_id').val('');
		$('#day_start').val('');
		$('#day_end').val('');
		loadDataTable();
	}

	function loadDataTable(param = '') {
		$('#datatable_serverside').DataTable({
			processing: true,
			serverSide: true,
			destroy: true,
			scrollX: true,
			order: [[0, 'desc']],
			iDisplayInLength: 10,
			ajax: {
				url: '{{ url("admin/collection/delivery/datatable") }}',
				data: {
					param: param,
					receipt_no: $('#receipt_no').val(),
					publisher_id: $('#publisher_id').val(),
					periode_start: $('#day_start').val(),
					periode_end: $('#day_start').val(),
					expedition_id: $('#expedition_id').val(),
				}
			},
			columns: [
                {
					name: 'id',
					searchable: false,
					orderable: true,
					className: 'align-middle text-center'
				},
				{
					name: 'action',
					searchable: false,
					orderable: false,
					className: 'align-middle text-center'
				},
                {
					name: 'publisher',
					searchable: false,
                    orderable: false,
					className: 'align-middle text-center'
				},
				{
					name: 'count_title',
					searchable: false,
                    orderable: false,
					className: 'align-middle text-center'
				},
				{
					name: 'count_exemplar',
                    searchable: false,
                    orderable: false,
					className: 'align-middle text-center'
				},
				{
					name: 'status',
                    searchable: false,
                    orderable: false,
					className: 'align-middle text-center'
				},
				{
					name: 'expedition',
                    searchable: false,
                    orderable: false,
					className: 'align-middle text-center'
				},
				{
					name: 'receipt_no',
                    searchable: false,
                    orderable: false,
					className: 'align-middle text-center'
				},
				{
					name: 'delivery_date',
					searchable: false,
                    orderable: false,
					className: 'align-middle text-center'
				},
				{
					name: 'library_id',
					searchable: false,
                    orderable: false,
					className: 'align-middle text-center'
				},
			],
			pagingType: 'input',
		});
	}

</script>
