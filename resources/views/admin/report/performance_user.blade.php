<div class="app-content content">
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-6 col-12 mb-2">
				<h3 class="content-header-title mb-1 d-inline-block">Laporan Kinerja User</h3><br>
				<div class="row breadcrumbs-top d-inline-block">
					<div class="breadcrumb-wrapper col-12">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
							<li class="breadcrumb-item"><a href="#">Laporan</a></li>
							<li class="breadcrumb-item active">Kinerja User</li>
						</ol>
					</div>
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
                                        @if($access_all_user > 0)
                                            <div class="col-md-12">
                                                <div class="form-group row">
                                                    <label class="col-md-2">User</label>
                                                    <div class="col-md-10">
                                                        <select name="filter_causer_id" id="filter_causer_id" class="select2" style="width:100%;">
                                                            @if($access_all_user < 1)
                                                                <option value="{{ session('id') }}" selected>{{ session('username') }} | {{ session('fullname') }}</option>
                                                            @else
                                                                <option value="">Semua</option>
                                                                @foreach($user as $u)
                                                                    <option value="{{ $u->id }}">{{ $u->username }} | {{ $u->admin->fullname }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
										<div class="col-md-12">
											<div class="form-group row">
												<label class="col-md-2">Tanggal</label>
                                                <div class="col-md-10">
                                                    <div class="input-group">
                                                        <input type="date" name="filter_start_date" id="filter_start_date" max="{{ date('Y-m-d') }}" style="height:40px;" class="form-control">
                                                        <div class="input-group-prepend" style="height:40px;">
                                                            <span class="input-group-text">s/d</span>
                                                        </div>
                                                        <input type="date" name="filter_finish_date" id="filter_finish_date" max="{{ date('Y-m-d') }}" style="height:40px;" class="form-control">
                                                    </div>
                                                </div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group row">
                                                <label class="col-md-2">Kriteria</label>
                                                <div class="col-md-10">
                                                    <select name="filter_type" id="filter_type" class="form-control">
                                                        <option value="">Semua</option>
                                                        <option value="1">Tolak</option>
                                                        <option value="2">Terima</option>
                                                        <option value="3">Kelola (Before After)</option>
                                                        <option value="4">Validasi</option>
                                                        <option value="5">Masalah</option>
                                                    </select>
                                                </div>
											</div>
											<div class="form-group"><hr></div>
											<div class="form-group">
												<div class="text-right">
													<button type="button" class="btn btn-secondary" onclick="filter('reset')">Reset</button>
													<button type="button" class="btn btn-success" onclick="filter()">Cari</button>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="card">
							<div class="card-header">
								<div class="row">
                                    <div class="col-md-6">
                                        <h4 class="card-title">
                                            Daftar Kinerja User
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
								<div class="card-body card-dashboard table-responsive">
									<table class="table table-striped table-bordered display" id="datatable_serverside">
										<thead class="text-center">
											<tr>
												<th>No</th>
												<th>User</th>
												<th>Koleksi</th>
												<th>Description</th>
												<th>Properti</th>
												<th>Tanggal</th>
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
		loadDataTable();
	});

	function filter(param = '') {
		if(param) {
			$('#filter_causer_id').val('').change();
			$('#filter_start_date').val('');
			$('#filter_finish_date').val('');
			$('#filter_type').val('');
		}

		loadDataTable();
	}

	function loadDataTable() {
		$('#datatable_serverside').DataTable({
			processing: true,
			serverSide: true,
			destroy: true,
			order: [[0, 'desc']],
			iDisplayInLength: 10,
			pagingType : 'input',
			ajax: {
				url: '{{ url("admin/report/performance_user/datatable") }}',
				type: 'get',
				data: {
					causer_id: $('#filter_causer_id').val(),
					start_date: $('#filter_start_date').val(),
					finish_date: $('#filter_finish_date').val(),
					type: $('#filter_type').val()
				}
			},
			columns: [
				{
					name: 'id',
					searchable: false,
					className: 'align-middle text-center'
				},
				{
					name: 'causer_id',
					className: 'align-middle text-center'
				},
				{
					name: 'subject_id',
					className: 'align-middle text-center'
				},
				{
					name: 'description',
					className: 'align-middle text-center'
				},
				{
					name: 'properties',
					searchable: false,
					className: 'align-middle text-center'
				},
				{
					name: 'created_at',
					searchable: false,
					className: 'align-middle text-center nowrap'
				}
			]
		});
	}

    function downloadExcel() {
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
                causer_id: $('#filter_causer_id').val(),
                start_date: $('#filter_start_date').val(),
                finish_date: $('#filter_finish_date').val(),
                type: $('#filter_type').val(),
                slug: 'performance_user'
            },
            success: function(response) {
                loadingClose('body');
                Swal.fire('Sukses!!', 'Sedang diproses.', 'success');
            }
        });
    }
</script>
