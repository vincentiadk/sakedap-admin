<div class="app-content content">
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-6 col-12 mb-2">
				<h3 class="content-header-title mb-1 d-inline-block">Penerbit Pemantauan</h3><br>
				<div class="row breadcrumbs-top d-inline-block">
					<div class="breadcrumb-wrapper col-12">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
							<li class="breadcrumb-item"><a href="#">Penerbit</a></li>
							<li class="breadcrumb-item active">Pemantauan</li>
						</ol>
					</div>
				</div>
			</div>
			<div class="content-header-right col-md-6 col-12 mb-2 mt-1">
				<div class="float-md-right">
					<button type="button" class="btn btn-secondary" onclick="loadDataTable()">Refresh</button>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="configuration">
				<div class="row">
					<div class="col-12">
						<div class="card">
							<div class="card-header">
								<h4 class="card-title">Daftar Penerbit Pemantauan</h4>
							</div>
							<div class="card-content collapse show">
								<div class="card-body card-dashboard">
									<table class="table table-striped table-bordered display nowrap" id="datatable_serverside">
										<thead class="text-center">
											<tr>
												<th>No</th>
												<th>Foto</th>
												<th>Nama</th>
												<th>Email</th>
												<th>Telepon</th>
												<th>Organisasi</th>
												<th>Registrasi</th>
												<th>Aksi</th>
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
	<div class="modal-dialog modal-lg" role="document">
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
				<form action="" id="form_data">
					<div class="form-group">
						<center>
							<img src="" class="rounded-circle height-100 ezoom" id="photo">
						</center>
					</div>
					<div class="form-group"><hr></div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Nama :</label>
								<input type="text" name="name" id="name" class="form-control" readonly>
							</div>
							<div class="form-group">
								<label>Username :</label>
								<input type="text" name="username" id="username" class="form-control">
							</div>
							<div class="form-group">
								<label>Email :</label>
								<input type="text" name="email" id="email" class="form-control">
							</div>
							<div class="form-group">
								<label>Telepon :</label>
								<input type="text" name="phone" id="phone" class="form-control" disabled>
							</div>
							<div class="form-group">
								<label>Fax :</label>
								<input type="text" name="fax" id="fax" class="form-control" disabled>
							</div>
							<div class="form-group">
								<label>Kontak :</label>
								<input type="text" name="contact" id="contact" class="form-control" disabled>
							</div>
                            <div class="form-group">
                                <label>Tipe :</label>
                                <select name="type" id="type" class="form-control" style="width:100%;" disabled>
                                    <option value="1">Swasta</option>
                                    <option value="2">Perorangan</option>
                                    <option value="3">Pemerintah</option>
                                </select>
                            </div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Provinsi :</label>
								<input type="text" name="province" id="province" class="form-control" disabled>
							</div>
							<div class="form-group">
								<label>Kota :</label>
								<input type="text" name="city" id="city" class="form-control" disabled>
							</div>
							<div class="form-group">
								<label>Kecamatan :</label>
								<input type="text" name="district" id="district" class="form-control" disabled>
							</div>
							<div class="form-group">
								<label>Kelurahan :</label>
								<input type="text" name="village" id="village" class="form-control" disabled>
							</div>
							<div class="form-group">
								<label>Alamat :</label>
								<input type="text" name="address" id="address" class="form-control" disabled>
							</div>
							<div class="form-group">
								<label>Registrasi :</label>
								<input type="text" name="created_at" id="created_at" class="form-control" disabled>
							</div>
                            <div class="form-group">
                                <label>Organisasi :</label>
                                <select name="organization_id" id="organization_id" class="form-control" disabled>
                                    <option value="">-- Pilih --</option>
                                    @foreach($organization as $o)
                                        <option value="{{ $o->id }}">{{ $o->name }}</option>
                                    @endforeach
                                </select>
                            </div>
						</div>
					</div>
					<div class="form-group"><hr></div>
					<div class="form-group">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<label>Akta Perusahaan :</label>
									<!--div>
										<a href="" target="_blank" onclick="previewStreamPdf(this.href); return false" id="birth_certificate" class="text-primary"><i class="la la-file"></i> Lihat File</a>
									</div-->
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>Surat Keterangan :</label>
									<!--div>
										<a href="" target="_blank" onclick="previewStreamPdf(this.href); return false" id="statement_letter" class="text-primary"><i class="la la-file"></i> Lihat File</a>
									</div-->
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Status :</label>
									<div class="row">
										<div class="col-md-4">
											<fieldset class="radio">
												<label>
													<input type="radio" name="status" value="1"> Review
												</label>
											</fieldset>
										</div>
										<div class="col-md-4">
											<fieldset class="radio">
												<label>
													<input type="radio" name="status" value="2"> Diterima
												</label>
											</fieldset>
										</div>
										<div class="col-md-4">
											<fieldset class="radio">
												<label>
													<input type="radio" name="status" value="3"> Bermasalah
												</label>
											</fieldset>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div id="form_problem" style="display:none;">
						<div class="form-group"><hr></div>
						<div class="form-group">
							<textarea name="problem" id="problem" class="form-control" placeholder="Masalah" style="resize:none;"></textarea>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12"><iframe id="birth_certificate" src="" width="100%" height="500px"></iframe></div>
						<div class="col-md-12"><iframe id="statement_letter" src="" width="100%" height="500px"></iframe></div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn grey btn-secondary" data-dismiss="modal">Tutup</button>
				<button type="button" class="btn btn-danger" onclick="cancel()" id="btn_cancel">Batal</button>
				<button type="button" class="btn btn-warning" onclick="update()" id="btn_update">Simpan Perubahan</button>
			</div>
		</div>
	</div>
</div>

<script>
	$(function() {
		loadDataTable();

		$('input:radio[name="status"]').click(function() {
			if($(this).is(':checked') && $(this).val() == 3) {
				$('#form_problem').fadeIn(200);
			} else {
				$('#form_problem').fadeOut(200);
			}
		});

		
	});

	function cancel() {
		reset();
		$('#modal_element').modal('hide');
	}

	function toUpdate() {
		$('#modal_element').modal('show');
	}

	function reset() {
		$('#validasi_element').hide();
		$('#validasi_content').html('');
		$('#form_data').trigger('reset');
	}

	function success() {
		cancel();
		$('#datatable_serverside').DataTable().ajax.reload(null, false);
	}

	function loadDataTable() {
		$('#datatable_serverside').DataTable({
			processing: true,
			serverSide: true,
			destroy: true,
			scrollX: true,
			order: [[0, 'desc']],
			iDisplayInLength: 10,
			ajax: {
				url: '{{ url("admin/publisher/monitoring/datatable") }}'
			},
			columns: [
				{
					name: 'id',
					searchable: false,
					className: 'align-middle text-center'
				},
				{
					name: 'photo',
					searchable: false,
					className: 'align-middle text-center'
				},
				{
					name: 'name',
					className: 'align-middle text-center'
				},
				{
					name: 'email',
					className: 'align-middle text-center'
				},
				{
					name: 'phone',
					className: 'align-middle text-center'
				},
				{
					name: 'organization_id',
					searchable: false,
					className: 'align-middle text-center'
				},
				{
					name: 'created_at',
					searchable: false,
					className: 'align-middle text-center'
				},
				{
					name: 'action',
					searchable: false,
					orderable: false,
					className: 'align-middle text-center'
				}
			]
		});
	}

	function show(id) {
		toUpdate();
		$.ajax({
			url: '{{ url("admin/publisher/monitoring/show") }}' + '/' + id,
			type: 'GET',
			dataType: 'JSON',
			beforeSend: function() {
				loadingOpen('.modal-content');
				$('#validasi_element').hide();
				$('#validasi_content').html('');
			},
			success: function(response) {
				loadingClose('.modal-content');
				$('#photo').attr('src', response.photo);
				$('#name').val(response.name);
				$('#username').val(response.username);
				$('#email').val(response.email);
				if(response.username == "") {
					$('#username').removeAttr('readonly');
				} else {
					$('#username').attr('readonly','');
				}
				if(response.email == "") {
					$('#email').removeAttr('readonly');
				} else {
					$('#email').attr('readonly', '');
				}

				$('#phone').val(response.phone);
				$('#fax').val(response.fax);
				$('#contact').val(response.contact);
				$('#type').val(response.type);
				$('#organization_id').val(response.organization);
				$('#province').val(response.province);
				$('#city').val(response.city);
				$('#district').val(response.district);
				$('#village').val(response.village);
				$('#address').val(response.address);
				$('#created_at').val(response.created_at);
				$('#birth_certificate').attr('src', '{{ url("admin/publisher/stream_pdf") }}' + '/' + id + '/birth_certificate');
				$('#statement_letter').attr('src', '{{ url("admin/publisher/stream_pdf") }}' + '/' + id + '/statement_letter');
				//$('a#birth_certificate').attr('href', '{{ url("admin/publisher/stream_pdf") }}' + '/' + id + '/birth_certificate');
				//$('a#statement_letter').attr('href', '{{ url("admin/publisher/stream_pdf") }}' + '/' + id + '/statement_letter');
				$('input[name="status"][value="' + response.status + '"]').prop('checked', true);
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

	function update(id) {
		$.ajax({
			url: '{{ url("admin/publisher/monitoring/review") }}' + '/' + id,
			type: 'POST',
			dataType: 'JSON',
			data: $('#form_data').serialize(),
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
					url: '{{ url("admin/publisher/monitoring/destroy") }}' + '/' + id,
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
</script>
