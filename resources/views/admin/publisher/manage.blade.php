<div class="app-content content">
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-6 col-12 mb-2">
				<h3 class="content-header-title mb-1 d-inline-block">Penerbit Pengelolaan</h3><br>
				<div class="row breadcrumbs-top d-inline-block">
					<div class="breadcrumb-wrapper col-12">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
							<li class="breadcrumb-item"><a href="#">Penerbit</a></li>
							<li class="breadcrumb-item active">Pengelolaan</li>
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
								<h4 class="card-title">Daftar Penerbit Pengelolaan</h4>
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
								<input type="text" name="name" id="name" class="form-control" disabled>
							</div>
							<div class="form-group">
								<label>Nama Perubahan :</label>
								<input type="text" name="name_change" id="name_change" class="form-control">
							</div>
							<div class="form-group">
								<label>Username :</label>
								<input type="text" name="username" id="username" class="form-control" disabled>
							</div>
							<div class="form-group">
								<label>Email :</label>
								<input type="text" name="email" id="email" class="form-control">
							</div>
							<div class="form-group">
								<label>Telepon :</label>
								<input type="text" name="phone" id="phone" class="form-control">
							</div>
							<div class="form-group">
								<label>Fax :</label>
								<input type="text" name="fax" id="fax" class="form-control">
							</div>
							<div class="form-group">
								<label>Kontak :</label>
								<input type="text" name="contact" id="contact" class="form-control">
							</div>
                            <div class="form-group">
                                <label>Tipe :</label>
                                <select name="type" id="type" class="form-control" style="width:100%;">
                                    <option value="1">Swasta</option>
                                    <option value="2">Perorangan</option>
                                    <option value="3">Pemerintah</option>
                                </select>
                            </div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
                                <label>Asal Registrasi :</label>
								<input type="text" name="system_type" id="system_type" class="form-control" disabled>
							</div>
							<div class="form-group">
								<label>Kode sistem lain :</label>
								<input type="text" name="code_system" id="code_system" class="form-control" disabled>
							</div>
							<div class="form-group">
                                <label>Provinsi :</label>
								<select name="province_id" id="province_id" style="width:100%;" onchange="getCity()"></select>
							</div>
							<div class="form-group">
                                <label>Kota :</label>
								<select name="city_id" id="city_id" style="width:100%;" onchange="getDistrict()"></select>
							</div>
							<div class="form-group">
                                <label>Kecamatan :</label>
								<select name="district_id" id="district_id" style="width:100%;" onchange="getVillage()"></select>
							</div>
							<div class="form-group">
                                <label>Kelurahan :</label>
								<select name="village_id" id="village_id" style="width:100%;"></select>
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
                                <select name="organization_id" id="organization_id" class="form-control">
                                    <option value="">-- Pilih --</option>
                                    @foreach($organization as $o)
                                        <option value="{{ $o->id }}">{{ $o->name }}</option>
                                    @endforeach
                                </select>
                            </div>
						</div>
					</div>
					<div class="form-group"><hr></div>
					<div class="form-group"><hr></div>
					<div class="form-group">
						<div class="row text-center">
							<div class="col-md-6">
								<div class="form-group">
									<label>Akta Perusahaan :</label>
									<div>
										<a href="" target="_blank" id="birth_certificate" class="text-primary"><i class="la la-file"></i> Lihat File</a>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Surat Keterangan :</label>
									<div>
										<a href="" target="_blank" id="statement_letter" class="text-primary"><i class="la la-file"></i> Lihat File</a>
									</div>
								</div>
							</div>
						</div>
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

<div class="modal animated bounceInRight text-left" id="modal_confirmation" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel50" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel50">Konfirmasi</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<h4>Anda yakin akan <span id="spanLock"></span>? </h4>
				<p id="pLock" style="color:orange"></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-info" id="btn_save_lock">Ya</button>
				<button type="button" class="btn btn-danger" onclick="cancel()" id="btn_cancel_lock">Batal</button>
			</div>
		</div>
	</div>
</div>

<script>
	$(function() {
		loadDataTable();

		var status = '{{ session("not_found") }}';
		if(status) {
			Swal.fire('Oooppsss!!', status, 'info');
		}

		select2AutoSuggest('#province_id', 'load_province');
        $('#city_id').select2();
        $('#district_id').select2();
        $('#village_id').select2();	
	});

	function getCity() {
        var province_id = $('#province_id').val();
        if(province_id !== '') {
            select2AutoSuggest('#city_id', 'load_city/' + province_id);
        } else {
            $('#city_id').val('').trigger('change');
        }
    }

    function getDistrict() {
        var city_id = $('#city_id').val();
        if(city_id !== '') {
            select2AutoSuggest('#district_id', 'load_district/' + city_id);
        } else {
            $('#district_id').val('').trigger('change');
        }
    }

    function getVillage() {
        var district_id = $('#district_id').val();
        if(district_id !== '') {
            select2AutoSuggest('#village_id', 'load_village/' + district_id);
        } else {
            $('#village_id').val('').trigger('change');
        }
    }

	function lock(id) {
		getPublisherLock(id, "1");
		$('#modal_confirmation').modal('show');
	}
	function unlock(id) {
		getPublisherLock(id, "0");
		$('#modal_confirmation').modal('show');
	}

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
	function sync(id) {
		$.ajax({
			url: '{{ url("admin/publisher/manage/sync-isbn") }}' + '/' + id,
			type: 'GET',
			dataType: 'JSON',
			beforeSend: function() {
				loadingOpen('.modal-content');
				$('#validasi_element').hide();
				$('#validasi_content').html('');
			},
			success: function(response) {
				if(response.status == 200) {
					success();
					Toast.fire({
						icon: 'success',
						timer: 10000,
						position: 'middle',
						title: response.message
					});
				} else {
					Toast.fire({
						icon: 'warning',
						timer: 10000,
						position: 'middle',
						title: response.message
					});
				}
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
	function loadDataTable() {
		$('#datatable_serverside').DataTable({
			processing: true,
			serverSide: true,
			destroy: true,
			scrollX: true,
			order: [[0, 'desc']],
			iDisplayInLength: 10,
			ajax: {
				url: '{{ url("admin/publisher/manage/datatable") }}'
			},
			rowCallback: function(row, data) {
				// Customize row color based on data
				if (data[8] == 3) {
					$(row).css('background-color', '#ffabab');	
				} else if(data[8] == 2 || data[8] == 1) {
					$(row).css('background-color', '#fffcc7');	
				}

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
					orderable: false,
					searchable: false,
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
	function getPublisherLock(id, type) {
		$.ajax({
			url: '{{ url("admin/publisher/manage/show") }}' + '/' + id,
			type: 'GET',
			dataType: 'JSON',
			beforeSend: function() {
				loadingOpen('.modal-content');
				$('#validasi_element').hide();
				$('#validasi_content').html('');
			},
			success: function(response) {
				loadingClose('.modal-content');
				if(type == "0") {
					$('#spanLock').html('<b>membuka blokir</b> Penerbit : ' + response.name);
					$('#pLock').html('*) Setelah dibuka blokir, penerbit dapat kembali mengajukan ISBN pada isbn.perpusnas.go.id');
					$('#btn_save_lock').attr('onclick', 'doBlockUnblock(' + id + ', 0)');
				} else {
					$('#spanLock').html('<b>memblokir</b> Penerbit : ' + response.name);
					$('#pLock').html('*) Setelah diblokir, penerbit <b>tidak dapat</b> mengajukan ISBN pada isbn.perpusnas.go.id');
					$('#btn_save_lock').attr('onclick', 'doBlockUnblock(' + id + ', 1)');
				}
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

	function doLockUnlock(id, type) {
		$.ajax({
			url: '{{ url("admin/publisher/manage/lock-unlock") }}' + '/' + id + '?type=' + type,
			type: 'GET',
			dataType: 'JSON',
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
					Toast.fire({
						icon: 'info',
						title: 'Validasi'
					});
				} else {
					Toast.fire({
						icon: 'warning',
						title: response.message
					});
				}
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
	function show(id) {
		toUpdate();
		$.ajax({
			url: '{{ url("admin/publisher/manage/show") }}' + '/' + id,
			type: 'GET',
			dataType: 'JSON',
			beforeSend: function() {
				loadingOpen('.modal-content');
				$("#province_id").html('').trigger('change')
				$("#city_id").html('').trigger('change')
				$("#district_id").html('').trigger('change')
				$("#village_id").html('').trigger('change')
				$('#validasi_element').hide();
				$('#validasi_content').html('');
			},
			success: function(response) {
				console.log(response)
				loadingClose('.modal-content');
				$('#photo').attr('src', response.photo);
				$('#name').val(response.name);
				$('#name_change').val(response.name_change);
				$('#username').val(response.username);
				$('#email').val(response.email);
				$('#phone').val(response.phone);
				$('#fax').val(response.fax);
				$('#contact').val(response.contact);
				$('#type').val(response.type);
				$('#organization_id').val(response.organization);
				$('#system_type').val(response.system_type);
				$('#code_system').val(response.code_system);
				$('#created_at').val(response.created_at);
				$('a#birth_certificate').attr('href', '{{ url("admin/publisher/stream_pdf") }}' + '/' + id + '/birth_certificate');
				$('a#statement_letter').attr('href', '{{ url("admin/publisher/stream_pdf") }}' + '/' + id + '/statement_letter');
				$('input[name="status"][value="' + response.status + '"]').prop('checked', true);
				$('#btn_update').attr('onclick', 'update(' + id + ')');

				var provinceId = response.province_id
				
				if(provinceId != null) {
					var newOption = new Option(response.province, response.province_id, false, false);
					$('#province_id').append(newOption).trigger('change');
				}

				var cityId = response.city_id
				if(cityId != null) {
					var newOption = new Option(response.city, response.city_id, false, false);
					$('#city_id').append(newOption).trigger('change');
				}

				var districtId = response.district_id

				if(districtId != null) {
					var newOption = new Option(response.district, response.district_id, false, false);
					$('#district_id').append(newOption).trigger('change');
				}

				var villageId = response.village

				if(villageId != null) {
					var newOption = new Option(response.village, response.village_id, false, false);
					$('#village_id').append(newOption).trigger('change');
				}
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
			url: '{{ url("admin/publisher/manage/update") }}' + '/' + id,
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
					url: '{{ url("admin/publisher/manage/destroy") }}' + '/' + id,
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
