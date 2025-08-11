<div class="app-content content">
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-6 col-12 mb-2">
				<h3 class="content-header-title mb-1 d-inline-block">Laporan Distribusi</h3><br>
				<div class="row breadcrumbs-top d-inline-block">
					<div class="breadcrumb-wrapper col-12">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
							<li class="breadcrumb-item"><a href="#">Laporan</a></li>
							<li class="breadcrumb-item active">Distribusi</li>
						</ol>
					</div>
				</div>
			</div>
			<div class="content-header-right col-md-6 col-12 mb-2 mt-1">
				<div class="float-md-right">
					<button type="button" class="btn btn-success btn-sm" onclick="downloadExcel()"><i class="la la-download"></i> Download Excel</button>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="configuration">
				<div class="row">
					<div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Filtering Data</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Penerbit :</label>
                                            <select class="form-control" name="publisher_id" id="publisher_id"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Perpustakaan :</label>
                                            <select class="select2 form-control" name="library_id" id="library_id">
                                                <option value="">Semua</option>
                                                @foreach($library as $l)
                                                    <option value="{{ $l->id }}">{{ $l->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Ekspedisi :</label>
                                            <select class="select2 form-control" name="expedition_id" id="expedition_id">
                                                <option value="">Semua</option>
                                                @foreach($expedition as $e)
                                                    <option value="{{ $e->id }}">{{ $e->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Status :</label>
                                            <select class="select2 form-control" name="status" id="status">
                                                <option value="">Semua</option>
                                                @foreach($status as $s)
                                                    <option value="{{ $s }}">{{ $s }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Tgl Kirim :</label>
                                            <input type="date" name="delivery_date" id="delivery_date" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Tgl Diterima :</label>
                                            <input type="date" name="accepted_date" id="accepted_date" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-0"><hr></div>
                                <div class="text-right">
                                    <button type="button" class="btn btn-danger" onclick="location.reload(true)">Reset</button>
                                    <button type="button" class="btn btn-primary" onclick="loadDataTable()">Cari Data</button>
                                </div>
                            </div>
                        </div>
						<div class="card">
                            <div class="card-body">
                                <table class="table table-bordered display w-100" id="datatable_serverside">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Ekspedisi</th>
                                            <th>Penerbit</th>
                                            <th>Perpustakaan</th>
                                            <th>Tgl Kirim</th>
                                            <th>Tgl Diterima</th>
                                            <th>Status</th>
                                            <th>No Surat</th>
                                        </tr>
                                    </thead>
                                </table>
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

		select2AutoSuggest('#publisher_id', 'load_publisher');
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
				url: '{{ url("admin/report/distribution/datatable") }}',
				type: 'post',
				data: {
					expedition_id: $('#expedition_id').val(),
					publisher_id: $('#publisher_id').val(),
					library_id: $('#library_id').val(),
					delivery_date: $('#delivery_date').val(),
					accepted_date: $('#accepted_date').val(),
					status: $('#status').val(),
				}
			},
			columns: [
				{ name: 'id', orderable: true, searchable: false, className: 'align-middle text-center' },
				{ name: 'expedition_id', orderable: true, searchable: true, className: 'align-middle text-wrap' },
				{ name: 'publisher_id', orderable: true, searchable: true, className: 'align-middle text-wrap' },
				{ name: 'library_id', orderable: true, searchable: true, className: 'align-middle text-wrap' },
				{ name: 'delivery_date', orderable: true, searchable: false, className: 'align-middle' },
				{ name: 'accepted_date', orderable: true, searchable: false, className: 'align-middle' },
				{ name: 'status', orderable: true, searchable: true, className: 'align-middle' },
				{ name: 'letter_no', orderable: true, searchable: true, className: 'align-middle' },
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
				expedition_id: $('#expedition_id').val(),
                publisher_id: $('#publisher_id').val(),
                library_id: $('#library_id').val(),
                delivery_date: $('#delivery_date').val(),
                accepted_date: $('#accepted_date').val(),
                status: $('#status').val(),
				slug: 'report_distribution'
			},
			success: function(response) {
				loadingClose('body');
				Swal.fire('Sukses!!', 'Sedang diproses.', 'success');
			}
		});
	}
</script>
