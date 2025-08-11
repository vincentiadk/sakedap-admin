<div class="app-content content">
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-6 col-12 mb-2">
				<h3 class="content-header-title mb-1 d-inline-block">Laporan Periodik</h3><br>
				<div class="row breadcrumbs-top d-inline-block">
					<div class="breadcrumb-wrapper col-12">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
							<li class="breadcrumb-item"><a href="#">Laporan</a></li>
							<li class="breadcrumb-item active">Periodik</li>
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
                                <div class="card-title">
								    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group row no-gutters">
                                                <label class="col-form-label col-md-3">Tahun</label>
                                                <div class="col-md-9">
                                                    <select name="yearly" id="yearly" class="custom-select">
                                                        @for($i = date('Y'); $i >= 2020; $i--)
                                                            <option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group row no-gutters">
                                                <label class="col-form-label col-md-3">Status</label>
                                                <div class="col-md-9">
                                                    <select name="status" id="status" class="custom-select">
                                                        <option value="1">Review</option>
                                                        <option value="2" selected>Diterima</option>
                                                        <option value="3">Masalah</option>
                                                        <option value="4">Pre Proses</option>
                                                        <option value="5">Ditolak</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group row no-gutters">
                                                <label class="col-form-label col-md-3">Tanggal</label>
                                                <div class="col-md-9">
                                                    <select name="date" id="date" class="custom-select">
                                                        <option value="rejected_at">Ditolak</option>
                                                        <option value="received_at" selected>Diterima</option>
                                                        <option value="validated_at">Divalidasi</option>
                                                        <option value="created_at">Dibuat</option>
                                                        <option value="updated_at">Diedit</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <button type="button" class="btn btn-success col-12" onclick="downloadExcel()"><i class="la la-folder"></i> Download Excel</button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <button type="button" class="btn btn-primary col-12" onclick="loadData()"><i class="la la-search"></i> Filter</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
							</div>
							<div class="card-content collapse show">
								<div class="card-body card-dashboard table-responsive">
									<table class="table table-striped table-bordered">
										<thead class="text-center">
											<tr>
												<th class="table-secondary font-italic text-left">Bulan</th>
												<th>Buku</th>
												<th>Partitur</th>
												<th>Peta</th>
												<th>Serial</th>
												<th>Audio</th>
												<th>Film</th>
											</tr>
										</thead>
                                        <tbody id="list_item"></tbody>
                                        <tfoot id="list_total"></tfoot>
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
    function loadData() {
        $.ajax({
            url: '{{ url("admin/report/periodic/load_data") }}',
            type: 'GET',
            dataType: 'JSON',
            data: {
                yearly: $('#yearly').val(),
                status: $('#status').val(),
                date: $('#date').val()
            },
            beforeSend: function() {
                loadingOpen('#configuration');
                $('#list_item').html('');
                $('#list_total').html('');
            },
            success: function(response) {
                $.each(response.item, function(i, val) {
                    var rand_str = randStr(10);

                    $('#list_item').append(`
                        <tr class="` + rand_str + ` text-center">
                            <td class="text-left table-secondary font-weight-bold font-italic">` + val.data.month + `</td>
                        </tr>
                    `);

                    $.each(val.data.item, function(index, value) {
                        $('.' + rand_str).append(`<td>` + value + `</td>`);
                    });
                });

                $('#list_total').html(`
                    <tr class="table-dark font-weight-bold text-center">
                        <th class="text-left">TOTAL</th>
                        <th>` + response.total[0] + `</th>
                        <th>` + response.total[1] + `</th>
                        <th>` + response.total[2] + `</th>
                        <th>` + response.total[3] + `</th>
                        <th>` + response.total[4] + `</th>
                        <th>` + response.total[5] + `</th>
                    </tr>
                `);

                loadingClose('#configuration');
            },
            error: function() {
                loadingClose('#configuration');
                alert('Server Error!!');
            }
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
                yearly: $('#yearly').val(),
                status: $('#status').val(),
                date: $('#date').val(),
                slug: 'periodic'
            },
            success: function(response) {
                loadingClose('body');
                Swal.fire('Sukses!!', 'Sedang diproses.', 'success');
            }
        });
    }
</script>
