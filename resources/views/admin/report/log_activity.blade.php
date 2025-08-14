<div class="app-content content">
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-6 col-12 mb-2">
				<h3 class="content-header-title mb-1 d-inline-block">Laporan Log Aktivitas</h3><br>
				<div class="row breadcrumbs-top d-inline-block">
					<div class="breadcrumb-wrapper col-12">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
							<li class="breadcrumb-item"><a href="#">Laporan</a></li>
							<li class="breadcrumb-item active">Log Aktivitas</li>
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
								<h4 class="card-title">Daftar Laporan Log Aktivitas</h4>
							</div>
							<div class="card-content collapse show">
								<div class="card-body card-dashboard">
									<table class="table table-striped table-bordered display nowrap" id="datatable_serverside">
										<thead class="text-center">
											<tr>
												<th>No</th>
												<th>User</th>
												<th>Deskripsi</th>
												<th>Property</th>
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

	function loadDataTable() {
		$('#datatable_serverside').DataTable({
			processing: true,
			serverSide: true,
			destroy: true,
			scrollX: true,
			order: [[0, 'desc']],
			iDisplayInLength: 10,
			pagingType: 'input',
			ajax: {
				url: '{{ url("admin/report/log_activity/datatable") }}',
				type: 'post',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
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
					name: 'description',
					className: 'align-middle text-center'
				},
                {
					name: 'property',
					searchable: false,
					className: 'align-middle text-center'
				},
				{
					name: 'created_at',
					searchable: false,
					className: 'align-middle text-center'
				}
			]
		});
	}
</script>
