<div class="app-content content">
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-6 col-12 mb-2">
				<h3 class="content-header-title mb-1 d-inline-block">File Download</h3><br>
				<div class="row breadcrumbs-top d-inline-block">
					<div class="breadcrumb-wrapper col-12">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
							<li class="breadcrumb-item"><a href="#">Laporan</a></li>
							<li class="breadcrumb-item active">File Download</li>
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
								<h4 class="card-title">Daftar File Download</h4>
							</div>
							<div class="card-content collapse show">
								<div class="card-body card-dashboard">
									<table class="table table-striped table-bordered display nowrap" id="datatable_serverside">
										<thead class="text-center">
											<tr>
												<th>No</th>
												<th>Jenis</th>
												<th>Tanggal</th>
												<th>Jam</th>
												<th>Keterangan</th>
												<th>File</th>
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
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel49">Keterangan</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="alert alert-danger" id="validasi_element" style="display:none;">
					<ul id="validasi_content"></ul>
				</div>
				<form action="" id="form_data">
                    <div id="data_periodic">
                        <div class="form-group">
                            <label>Tahun :</label>
                            <input type="text" name="yearly" id="yearly" class="form-control-plaintext" disabled>
                        </div>
                        <div class="form-group">
                            <label>Status :</label>
                            <input type="text" name="status" id="status" class="form-control-plaintext" disabled>
                        </div>
                        <div class="form-group">
                            <label>Tanggal :</label>
                            <input type="text" name="date" id="date" class="form-control-plaintext" disabled>
                        </div>
                    </div>
                    <div id="data_collection">
                        <div class="form-group">
                            <label>Periode :</label>
                            <input type="text" name="periode" id="periode" class="form-control-plaintext" disabled>
                        </div>
                        <div class="form-group">
                            <label>Provinsi :</label>
                            <input type="text" name="province" id="province" class="form-control-plaintext" disabled>
                        </div>
                        <div class="form-group">
                            <label>Metode :</label>
                            <input type="text" name="method" id="method" class="form-control-plaintext" disabled>
                        </div>
                        <div class="form-group">
                            <label>Tipe :</label>
                            <input type="text" name="type" id="type" class="form-control-plaintext" disabled>
                        </div>
                        <div class="form-group">
                            <label>Penerbit :</label>
                            <input type="text" name="publisher" id="publisher" class="form-control-plaintext" disabled>
                        </div>
                        <div class="form-group">
                            <label>User :</label>
                            <input type="text" name="user" id="user" class="form-control-plaintext" disabled>
                        </div>
                    </div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn grey btn-secondary" data-dismiss="modal">Tutup</button>
			</div>
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
			pagingType : 'input',
			ajax: {
				url: '{{ url("admin/report/file_download/datatable") }}',
				type: 'post'
			},
			columns: [
				{
					name: 'id',
					searchable: false,
					className: 'align-middle text-center'
				},
				{
					name: 'slug',
					className: 'align-middle text-center'
				},
				{
					name: 'date',
					searchable: false,
					className: 'align-middle text-center'
				},
				{
					name: 'time',
					searchable: false,
					className: 'align-middle text-center'
				},
				{
					name: 'description',
					searchable: false,
					className: 'align-middle text-center'
				},
				{
					name: 'link',
					searchable: false,
					className: 'align-middle text-center'
				}
			]
		});
	}

	function showDescription(id) {
		$('#modal_element').modal('show');
		$.ajax({
			url: '{{ url("admin/report/file_download/show_description") }}' + '/' + id,
			type: 'GET',
			dataType: 'JSON',
			beforeSend: function() {
				loadingOpen('.modal-content');
				$('#validasi_element').hide();
				$('#validasi_content').html('');
			},
			success: function(response) {
				loadingClose('.modal-content');
                if(response.slug == 'periodic') {
                    $('#data_periodic').show();
                    $('#data_collection').hide();
                    $('#yearly').val(response.yearly);
                    $('#status').val(response.status);
                    $('#date').val(response.date);
                } else {
                    $('#data_periodic').hide();
                    $('#data_collection').show();
                    $('#periode').val(response.periode);
                    $('#province').val(response.province);
                    $('#method').val(response.method);
                    $('#type').val(response.type);
                    $('#publisher').val(response.publisher);
                    $('#user').val(response.user);
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
</script>
