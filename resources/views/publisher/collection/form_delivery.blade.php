<div class="app-content content">
		<div class="content-wrapper">
				<div class="content-header row">
						<div class="content-header-left col-md-6 col-12 mb-2">
								<h3 class="content-header-title mb-1 d-inline-block">Pengiriman KC dan KR Analog</h3><br>
								<div class="row breadcrumbs-top d-inline-block">
										<div class="breadcrumb-wrapper col-12">
												<ol class="breadcrumb">
														<li class="breadcrumb-item"><a href="{{ url('publisher/dashboard') }}">Dashboard</a></li>
														<li class="breadcrumb-item"><a href="#">Koleksi</a></li>
														<li class="breadcrumb-item active">Tambah Data</li>
												</ol>
										</div>
								</div>
						</div>
						<div class="content-header-right col-md-6 col-12 mb-2 mt-1">
								<!-- <div class="float-md-right">
										<button type="button" class="btn btn-success" data-toggle="modal" id="modal_element_button" data-target="#modal_element">Import / Bulk Upload</button>
								</div> -->
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
					@elseif(session('failed'))
						<div class="alert bg-danger alert-icon-left alert-dismissible mb-2" role="alert">
								<span class="alert-icon"><i class="la la-times"></i></span>
								<button type="button" class="close" data-dismiss="alert" aria-label="Close">
										<span aria-hidden="true">&times;</span>
								</button>
								<strong>Failed!</strong> {{ session('failed') }}
						</div>
					@endif
					<div class="row">
						<div class="col-12">
							<div class="card">
								<div class="card-header">
									<h4 class="card-title text-center">Form Pengiriman KC dan KR Analog</h4>
									<a class="heading-elements-toggle"><i class="la la-ellipsis-h font-medium-3"></i></a>
									<div class="heading-elements">
										<ul class="list-inline mb-0">
											<li><a data-action="collapse"><i class="ft-minus"></i></a></li>
											<li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
											<li><a data-action="expand"><i class="ft-maximize"></i></a></li>
										</ul>
									</div>
									<div class="form-group">
											<div class="alert alert-danger" id="validasi_element" style="display:none;">
													<ul id="validasi_content"></ul>
											</div>
									</div>
								</div>
								<div class="card-content collapse show">
									<div class="card-body">
										<form id="form_data">
											<!-- Step 1 -->
											<h6>Informasi Pengiriman</h6>
											<div class="row">
												<div class="col-md-6">
													<div class="form-group">
														<label>Nama Penerbit</label>
														<input type="text" name="publisher_name" id="publisher_name" class="form-control required" placeholder="Nama Pelaksana" value="{{ $data['publisher']->name }}" readonly="">
														<span class="error"></span>
													</div>
													<div class="form-group">
														<label>Ekspedisi</label>
														<select name="expedition_id" id="expedition" class="form-control required" style="width:100%;" required>
															<option value="">
																-- Pilih Ekspedisi --
															</option>
															@foreach ($expedition as $e)
																<option value="{{ $e->id }}">
																	{{ $e->name }}
																</option>
															@endforeach
														</select>
														<span class="error"></span>
													</div>
													<div class="form-group">
														<label id="label_receipt_no">Nomor Resi Pengiriman</label>
														<input type="text" name="receipt_no" id="receipt_no" class="form-control required" placeholder="No Resi" value="" required>
														<span class="error"></span>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label>Tanggal Pengiriman</label>
														<input type="date" name="delivery_date" id="delivery_date" class="form-control" required>
														<span class="error"></span>
													</div>
													<div class="form-group">
														<label>Dikirim Ke</label>
														<fieldset class="radio">
															<label>
																<input type="radio"  name="library_id" value="1" required> Perpusnas
															</label>
														</fieldset>
														@if(!empty($data['library']))
														<fieldset class="radio">
															<label>
																<input type="radio"  name="library_id" value="{{ $data['library']->id }}"> Provinsi {{ $data['publisher']->province->name }}
															</label>
														</fieldset>
														@endif
														<span class="error"></span>
													</div>
												</div>
											</div>
											<hr>
											<h6>Data Koleksi</h6>
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<input type="text" id="isbnCode" class="form-control" placeholder="Kode ISBN">
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group text-left">
														<button type="button" class="btn btn-warning" onclick="checkCodeIsbn()"><i class="la la-search"></i> Cari ISBN</button>
														<button type="button" class="btn btn-primary" onclick="showModalCreate()"><i class="la la-plus"></i> Tambah Baru</button>
													</div>
												</div>
												<div class="col-md-3">
												</div>
											</div>
											<table class="table table-striped table-bordered" id="datatable_form">
												<thead class="text-center">
												<tr>
													<th>Jenis KCKR</th>
													<th>Cover</th>
													<th>Judul</th>
													<th>Kepengarangan</th>
													<th>Bulan/Tahun Terbit</th>
													<th>Deskripsi Fisik</th>
													<th>Ringkasan</th>
													<th>Jumlah</th>
													<th>Aksi</th>
												</tr>
												</thead>
											</table>
											<div class="col-md-12">
												<hr>
												<div class="form-group text-right">
													{{-- <button type="button" class="btn btn-danger" onclick="reset()"><i class="la la-times"></i> Download PDF</button> --}}
													<button type="submit" class="btn btn-success"><i class="la la-save"></i> Simpan</button>
												</div>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>
			</div>
	</div>
</div>

<div class="modal fade" id="modal_create" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content" id="modal_edition_content">
			<div class="modal-header">
					<h3>Tambah Koleksi</h3>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
					</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="alert alert-danger" id="validasi_element" style="display:none;">
							<ul id="validasi_content"></ul>
						</div>
						<form id="form_collection_new" class="form">
							<div>
								<div class="form-group">
									<div class="alert alert-danger" id="validasi_element"
										style="display:none;">
										<ul id="validasi_content"></ul>
									</div>
								</div>
								<p>
									<div class="form-group row">
										<label class="col-md-2">Penerbit :</label>
										<div class="col-md-10">
											<input type="text" name="publisher_name" id="publisher_name" class="form-control exclude-clear required" placeholder="Nama Pelaksana" value="{{ $data['publisher']->name }}" readonly="">
										</div>
									</div>
									<div class="form-group row">
										<label class="col-md-2">Provinsi Terbit :</label>
										<div class="col-md-10">
											<div class="input-group">
												<input name="province" type="text"
													class="form-control exclude-clear" id="province"
													placeholder="Provinsi Terbit" value="{{ $data['publisher']->province->name }}" readonly>
												<div class="input-group-append">
													<span class="input-group-text"
														id="text_code_province">.</span>
													<input id="province_id" type="hidden"
														name="province_id" class="exclude-clear" value="{{ $data['publisher']->province->id }}" >
												</div>
											</div>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-md-2">Kabupaten/Kota Terbit :</label>
										<div class="col-md-10">
											<div class="input-group">
												<input name="city" type="text"
													class="form-control exclude-clear" id="city"
													placeholder="Kabupaten/Kota Terbit" value="{{ $data['publisher']->city->name }}" readonly>
												<div class="input-group-append">
													<span class="input-group-text"
														id="text_code_city">.</span>
													<input id="city_id" type="hidden" class="exclude-clear" value="{{ $data['publisher']->city->id }}" 
														name="city_id">
												</div>
											</div>
										</div>
									</div>

									<div class="form-group row">
										<label class="col-md-2">Tipe :</label>
										<div class="col-md-10">
											<select name="deposit_head_id"
												id="deposit_head_id" class="form-control deposit-head-id-select"
												style="width:100%;" multiple>
											
												@foreach ($data['deposit_head'] as $k => $v)
													<option value="{{ $k }}">
														{{ $v}}
													</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="form-group row serial_container">
										<label class="col-md-2">Nama Serial :</label>
										<div class="col-md-10">
											<select name="title_serial" id="title_serial" class="form-control" placeholder="Masukan judul" style="width:100%;"> </select>
											<span class="error"></span>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-md-2">Judul :</label>
										<div class="col-md-10">
											<input type="text" name="title" id="title" class="form-control" placeholder="Masukan judul" style="width:100%;"> 
											<span class="error"></span>
										</div>
									</div>
									<input type="hidden" name="collection_id" id="collection_id">
									<input type="hidden" name="parent_id" id="parent_id">
									<div class="form-group row">
										<label class="col-md-2">Kode :</label>
										<div class="col-md-10">
											<input type="text" class="form-control" name="code"
												id="code"
												placeholder="Masukan kode (ex: ISBN, ISSN, dll)" required>
											<span class="error"></span>
										</div>
									</div>
									<div class="form-group row serial_container">
										<label class="col-md-2">Seri :</label>
										<div class="col-md-10">
											<input type="text" class="form-control" name="series"
												id="series" placeholder="Masukan seri">
											<span class="error"></span>
										</div>
									</div>
									<div class="form-group row serial_container">
										<label class="col-md-2">Edisi :</label>
										<div class="col-md-10">
											<input type="text" class="form-control" name="edition"
												id="edition" placeholder="Masukan edisi">
											<span class="error"></span>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-md-2">Bulan Terbit :</label>
										<div class="col-md-10">
											<select name="publication_month" id="publication_month"
												class="form-control" required>
												<option value="">-- Pilih --</option>
												<option value="01">
													{{ App\Helper\GeneralHelper::getMonth('01') }}
												</option>
												<option value="02">
													{{ App\Helper\GeneralHelper::getMonth('02') }}
												</option>
												<option value="03">
													{{ App\Helper\GeneralHelper::getMonth('03') }}
												</option>
												<option value="04">
													{{ App\Helper\GeneralHelper::getMonth('04') }}
												</option>
												<option value="05">
													{{ App\Helper\GeneralHelper::getMonth('05') }}
												</option>
												<option value="06">
													{{ App\Helper\GeneralHelper::getMonth('06') }}
												</option>
												<option value="07">
													{{ App\Helper\GeneralHelper::getMonth('07') }}
												</option>
												<option value="08">
													{{ App\Helper\GeneralHelper::getMonth('08') }}
												</option>
												<option value="09">
													{{ App\Helper\GeneralHelper::getMonth('09') }}
												</option>
												<option value="10">
													{{ App\Helper\GeneralHelper::getMonth('10') }}
												</option>
												<option value="11">
													{{ App\Helper\GeneralHelper::getMonth('11') }}
												</option>
												<option value="12">
													{{ App\Helper\GeneralHelper::getMonth('12') }}
												</option>
											</select>

											<span class="error"></span>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-md-2">Tahun Terbit :</label>
										<div class="col-md-10">
											<input type="number" name="publication_year"
												id="publication_year" class="form-control"
												placeholder="Masukan tahun terbit" required>

											<span class="error"></span>
										</div>
									</div>
									<div class="form-group row serial_container">
										<label class="col-md-2">Serial :</label>
										<div class="col-md-10">
											<select name="serial" id="serial"
												class="form-control">
												<option value="">-- Pilih Serial --</option>
												<option value="1">Harian</option>
												<option value="2">Mingguan</option>
												<option value="3">Bulanan</option>
												<option value="4">3 Bulan Sekali</option>
												<option value="5">4 Bulan Sekali</option>
												<option value="6">6 Bulan Sekali</option>
												<option value="7">Tahunan</option>
												<option value="8">2 Tahun Sekali</option>
												<option value="9">3 Tahun Sekali</option>
											</select>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-md-2">Total Halaman :</label>
										<div class="col-md-10">
											<div class="input-group">
												<input type="number" name="total_page"
													id="total_page" class="form-control"
													placeholder="Masukan total halaman">
												<div class="input-group-prepend">
													<div class="input-group-text">Halaman</div>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-md-2">Dimensi :</label>
										<div class="col-md-10">
											<div class="input-group">
												<input type="number" name="dimension" id="dimension"
													class="form-control"
													placeholder="Masukan dimensi">
												<div class="input-group-prepend">
													<div class="input-group-text">Cm</div>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-md-2">Harga :</label>
										<div class="col-md-10">
											<div class="input-group">
												<div class="input-group-prepend">
													<div class="input-group-text">Rp.</div>
												</div>
												<input type="number" name="price" id="price"
													class="form-control"
													placeholder="Masukan Harga Koleksi">
											</div>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-md-2">Kategori :</label>
										<div class="col-md-10">
											<select name="collection_category[]"
												id="collection_category" class="form-control select2"
												style="width:100%;" multiple>
												@foreach ($category as $c)
													<option value="{{ $c->id }}">
														{{ $c->name }}
													</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-md-2">Keterangan :</label>
										<div class="col-md-10">
											<textarea name="description" id="description" class="form-control" 
												placeholder="Masukan informasi lain"></textarea>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-md-2">Jumlah Eksemplar :</label>
										<div class="col-md-10">
											<input type="number" name="exemplar" id="exemplar"
														class="form-control collection-copy"
														placeholder="Maximal 1" min="1" >
											<span class="error"></span>
										</div>
									</div>
								</p>
								<h4 class="form-section">Kontributor</h4>
								<p>
									<div class="table-responsive">
										<table class="table table-bordered table-striped">
											<tbody id="data_contributor">
												
											</tbody>
										</table>
									</div>
									<div class="form-group">
										<button type="button" class="btn btn-success btn-sm col-12"
											onclick="addElementContributor()"><i
												class="la la-plus"></i></button>
									</div>
								</p>
								<h4 class="form-section">Cover</h4>
								<div class="alert alert-warning">
									<small>
										Jenis File Yang di Dukung <b>: JPG, JPEG, PNG</b><br>
										Maksimal Ukuran File <b>: 1 MB</b>
									</small>
								</div>
								<div class="form-group">
									<input type="file" class="file-cover form-control-lg"
										name="cover" id="cover" data-theme="fa5">
								</div>
								<div class="form-group">
									<hr>
								</div>

								<div class="form-group">
									<div class="row">
										<div class="col-md-6">
											<div class="col-md-6">
												<ul id="validation_contributor"
													class="text-danger font-italic"></ul>
											</div>
										</div>
										<div class="col-md-6">
											<div class="text-right">
												{{-- <button type="button" id="btnCreateCollection" class="btn btn-primary"
													onclick="createCollection()">Tambah Data</button> --}}
												<button type="submit" id="btnCreateCollection" class="btn btn-primary">Simpan</button>
												{{-- <button type="button" id="btnUpdateCollection" class="btn btn-primary"
													onclick="updateCollection()">
													Ubah Data</button> --}}
											</div>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_terms" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
				<div class="modal-content" id="modal_edition_content">
						<div class="modal-header">
								<h3>Syarat dan Ketentuan</h3>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
								</button>
						</div>
						<div class="modal-body">
								<ul>
									<li>File digital yang diserahkan merupakan file lengkap dari halaman cover depan, halaman verso, halaman daftar isi (jika ada), halaman isi sampai ke halaman cover belakang.</li>
									<li>Terdapat cantuman ISBN sumber elektronis (e-ISBN) pada halaman verso</li>
									<li>File tidak bertanda air (watermark)</li>
									<li>File tidak dienkripsi</li>
									<li>File tidak rusak dan dapat diakses</li>
									<li>Kualitas karya rekam digital yang diserahkan sama dengan kualitas karya rekam yang dipublikasikan</li>
									<li>Produsen Karya Rekam menyetujui karya digital yang diserahkan disimpan di Repository Serah Simpan KCKR milik Perpustakaan Nasional</li>
									<li>Produsen Karya Rekam menerima tanda terima penyerahan karya digital melalui eDeposit Perpustakaan Nasional</li>
									<li>Penyerahan karya rekam digital melalui e-Deposit oleh Produsen Karya Rekam merupakan pemenuhan kewajiban peraturan perundang-undangan Serah Simpan Karya Cetak dan Karya Rekam</li>
								</ul>
						</div>
				</div>
		</div>
</div>

<script type="text/javascript">

	var countContributor = 0;
	var countFile = 0;
	var sliderVideo;
	var sliderMusic;
	var tempCollection = [];
	var table;

	var tableSerial;
	var dataSerial = [];
	var dataDepositHead = @php echo json_encode($data['deposit_head']); @endphp;
	var dataContributor = @php echo json_encode($data['contributor']); @endphp;
	var listDepositHeadSerial = @php echo json_encode($data['deposit_head_serial']); @endphp;

	var tableForm;
	var tableIdx = 0;
	var currentEditingIdx;

	$(document).ready(function() {
		
		// $('#expedition').select2({
		// 	placeholder: '-- Pilih Tipe Ekspedisi --',
		// 	allowClear: true,
		// 	multiple: false,
		// 	cache: true,
		// });

		$('#collection_category').select2({
			placeholder: '-- Pilih Kategori --',
			allowClear: true,
			multiple: true,
			cache: true,
		});

		$('#deposit_head_id').select2({
			placeholder: '-- Pilih Tipe --',
			allowClear: true,
			multiple: false,
			cache: true,
		});


		$('#deposit_head_id').on('change', function () {
			// Get the selected value(s)
			const selectedValues = $(this).val();
			if (listDepositHeadSerial.includes(Number(selectedValues))) {
				$(".serial_container").show();
			} else {
				$(".serial_container").hide();
			}
		});
		

		$('#form_collection_new').validate({
			errorClass: 'is-invalid',
			errorPlacement: function(error, element) {
				error.appendTo(element.next('.error'));
				error.attr('style', 'color: red; font-size: 12px; margin-top: 5px;');
			},
			submitHandler: function(form) {
				if (currentEditingIdx != null) {
					updateCollection(currentEditingIdx);
				} else {
					createCollection();
				}
			}
		});

		$('#form_data').validate({
			errorClass: 'is-invalid',
			errorPlacement: function(error, element) {
				error.appendTo(element.next('.error'));
				error.attr('style', 'color: red; font-size: 12px; margin-top: 5px;');
			},
			submitHandler: function(form) {
				var isValid = true;
				$('input[name^="collection_copy"], select[name^="collection_copy"], textarea[name^="collection_copy"]').each(function() {
					var inputValue = $(this).val();
					var nameValue = $(this).attr('name');
					// Perform validation or other actions on inputValue
					if (!inputValue) {
						// Handle validation error
						Swal.fire({
							icon: 'warning',
							title: 'Mohon lengkapi data koleksi',
							showConfirmButton: false,
							timer: 1500
						});
						isValid = false;
						return false; // Prevent form submission
					}

				});

				if (isValid) {
					create();
				}
			}
		});

		tableForm = $('#datatable_form').DataTable({
			processing: true,
			destroy: true,
			scrollX: true,
			info: false,
			ordering: false,
			paging: false,
			searching: false
		}).draw(false);

		$('#datatable_form').on('click', '.remove-row', function(event) {
			const indx = $(this).data('index');
			delete tempCollection[indx];
			// tempCollection.splice(indx, 1);

			const row = $(this).closest('tr');
            tableForm.row(row).remove().draw();
		});

		$('#data_contributor').on('click', '#remove_row_contributor', function() {
            $(this).closest('tr').remove();
        });
		
		$('input[name="library_id"]').change(function() {
            const selectedOption = $(this).val();
            // Update the estimation field in the table

			if (selectedOption == '1') {
				// $(this).val(2); 
				$('#exemplar').attr("placeholder", "Maximal 2");
			} else {
				// $(this).val(1); 
				$('#exemplar').attr("placeholder", "Maximal 1");
			}
        });

		$('#expedition').change(function() {
            const selectedOption = $(this).val();
            // Update the estimation field in the table

			if (selectedOption == '1') {
				$("#label_receipt_no").html('Nama Pengirim');
			} else {
				$("#label_receipt_no").html('Nomor Resi Pengiriman');
			}
        });

		

		$('#modal_create').on('hidden.bs.modal', function() {
			currentEditingIdx = null;
			$('#data_contributor').html('');
			$('#form_collection_new input:not(.exclude-clear), #form_collection_new textarea:not(.exclude-clear)').val('');
		});
	});

	function checkCodeIsbn() {
		if (!$('input[name="library_id"]').is(':checked')) {
			Swal.fire({
				icon: 'warning',
				title: 'Silakan pilih tujuan pengiriman terlebih dahulu',
				showConfirmButton: false,
				timer: 1500
			});

			return;
		}

        if ($('#isbnCode').val() != '') {
			var existingNames = $('.collection-code').map(function() {
				return $(this).val().replace(/-/g, '');
			}).get();

			if (existingNames.includes($('#isbnCode').val().replace(/-/g, ''))) {
				Swal.fire({
					icon: 'warning',
					title: 'ISBN Sudah diinput sebelumnya',
					showConfirmButton: false,
					timer: 1500
				});
			} else {
				$.ajax({
					url: '{{ url('publisher/collection/check_code_isbn') }}',
					type: 'POST',
					data: {
						code: $('#isbnCode').val(),
						library_id : $('input[name="library_id"]:checked').val(),
					},
					dataType: 'JSON',
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					beforeSend: function() {
						loadingOpen('#configuration');
						$('#validasi_element').hide();
						$('#validasi_content').html('');
					},
					success: function(response) {
						loadingClose('#configuration');
						if (response.status == 201) {
							window.location.href = response.data;
						} else if (response.status == 200) {
							Swal.fire({
								icon: 'success',
								title: response.message,
								showConfirmButton: false,
								timer: 1500
							});

							response = response.data;

							addRowCollection(response);

							$('#isbnCode').val('');
							$('#form_success_check_isbn').fadeIn(200);
						} else {
							Swal.fire({
								icon: 'error',
								title: response.message,
								showConfirmButton: true,
								allowOutsideClick: true, // Allow dismissing by clicking outside the alert
								allowEscapeKey: true // Allow dismissing by pressing the Escape key
							});

							$('#form_success_check_isbn').hide();
						}
					},
					error: function() {
						loadingClose('#configuration');
						Toast.fire({
							icon: 'error',
							title: 'Server Error!'
						});
					}
				});
			}
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Harap mengisi kode',
                showConfirmButton: false,
                timer: 1500
            });
        }
    }

	function create() {

		var formData = new FormData($('#form_data')[0]);

		for (var i = 0; i < tempCollection.length; i++) {
			var obj = tempCollection[i];
			for (var key in obj) {
				if (obj.hasOwnProperty(key)) {
					if (key == 'contributor') {
						for (var j = 0; j < obj[key].length; j++) {
							var contributor = obj[key][j];
							for (var contributorKey in contributor) {
								if (contributor.hasOwnProperty(contributorKey)) {
									var contributorValue = contributor[contributorKey] === null ? '' : contributor[contributorKey];
									formData.append('collections[' + i + '][' + key + '][' + j + '][' + contributorKey + ']', contributorValue);
								}
							}
						}
					}else if (key == 'category') {
						for (var l = 0; l < obj.category.length; l++) {
							formData.append('collections[' + i + '][category][]', obj.category[l]);
						}
					} else {
						var objValue = obj[key] === null ? '' : obj[key];
						formData.append('collections[' + i + '][' + key + ']', objValue);
					}
				}
			}
		}

		$.ajax({
				url: '{{ url("publisher/collection/delivery_form") }}',
				type: 'POST',
				dataType: 'JSON',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				xhr: function() {
					var xhr = new window.XMLHttpRequest();

					xhr.upload.addEventListener("progress", function(evt) {
						if (evt.lengthComputable) {
							var percentComplete = evt.loaded / evt.total;
							percentComplete = parseInt(percentComplete * 100);

							$('#progressValueUpload').empty();
							$('#percentComplete').remove();
							$('#progressUpload').append('<span id="percentComplete">'+percentComplete + '%</span>');
							$('#progressValueUpload').attr('aria-valuenow', percentComplete);
							$('#progressValueUpload').css('width', '' + percentComplete + '%');

							if (percentComplete === 100) {

							}

						}
					}, false);

					return xhr;
				},
				beforeSend: function() {
						loadingOpen('#configuration');
						$('.waitMe_content').append('<br/><div id="progressUpload" class="progress"><div id="progressValueUpload" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div></div>')
						$('#validasi_element').hide();
						$('#validasi_content').html('');
				},
				success: function(response) {
						loadingClose('#configuration');
						$('#progressUpload').remove();
						if(response.status == 200) {
								Toast.fire({
										icon: 'success',
										title: response.message
								});
								window.location.href = "{{ url('publisher/collection/delivery') }}";
						} else if(response.status == 422) {
								$('#validasi_element').show();

								document.body.scrollTop            = 0;
								document.documentElement.scrollTop = 0;

								Toast.fire({
										icon: 'info',
										title: 'Validasi'
								});

								$.each(response.error, function(i, val) {
										$('#validasi_content').append('<li>' + val + '</li>');
								});
						} else if(response.status == 302 && response) {
							window.location.href = "{{ url('publisher/collection/update/') }}" + "/" + response.data.id;
						} else {
								Toast.fire({
										icon: 'warning',
										title: response.message
								});
						}
				},
				error: function(e) {
					loadingClose('#configuration');
					$('#progressUpload').remove();
					Toast.fire({
							icon: 'error',
							title: 'Server Error!'
					});
				}
		});
	}

	function showModalCreate() {
		if (!$('input[name="library_id"]').is(':checked')) {
			Swal.fire({
				icon: 'warning',
				title: 'Silakan pilih tujuan pengiriman terlebih dahulu',
				showConfirmButton: false,
				timer: 1500
			});

			return;
		}

		$('#modal_create').modal('show');
		$(".serial_container").hide();
		addElementContributor();

		select2DepositHead();
		select2Author();
		select2Title();
	}

	function closeModalCreate() {
		$('#modal_create').modal('hide');
	}

	function createCollection() {

		try {
			var existingNames = $('.collection-code').map(function() {
				return $(this).val().replace(/-/g, '');
			}).get();

			if (existingNames.includes($('#code').val().replace(/-/g, ''))) {
				Swal.fire({
					icon: 'warning',
					title: 'ISBN Sudah diinput sebelumnya',
					showConfirmButton: false,
					timer: 1500
				});
			} else {
				const contributorIdInputs = $('select[name="contributor_contributor_id_field[]"]');
				// const authorNameInputs = $('select[name="author_id_field[]"]').select2('data');
				const authorTitleInputs = $('input[name="contributor_title_field[]"]');
				const authorBirthInputs = $('input[name="contributor_year_of_birth_field[]"]');
				const authorDeathInputs = $('input[name="contributor_year_of_death_field[]"]');

				var contributorArr = [];
				contributorIdInputs.each(function(index) {
					var authorNameInputs = $('#author_id_field-'+index).select2('data');
					const contributor = {
						"id_kontributor": contributorIdInputs.eq(index).val(),
						"kontributor": null,
						"id_author": authorNameInputs[0]?.id,
						"author": authorNameInputs[0]?.text,
						"author_title": authorTitleInputs.eq(index).val(),
						"author_birth": authorBirthInputs.eq(index).val(),
						"author_death": authorDeathInputs.eq(index).val()
					}
					contributorArr.push(contributor);
				});
				
				const formData = {
					"collection_id": null,
					"parent_id": $("#parent_id").val(),
					"deposit_head_id": $("#deposit_head_id").val(),
					"code": $("#code").val(),
					"title": $("#title").val(),
					"tahun_terbit": $("#publication_year").val(),
					"bulan_terbit": $("#publication_month").val(),
					"kepeng": "",
					"sinopsis": $("#description").val(),
					"edisi": $("#edition").val(),
					"jml_hlm": $("#total_page").val(),
					"subjek": "",
					"seri": $("#series").val(),
					"dimension": $("#dimension").val(),
					"price": $("#price").val(),
					"publisher_id": 33,
					"publisher_name": $("#publisher_name").val(),
					"publisher_province_id": $("#province_id").val(),
					"publisher_province": "",
					"publisher_city_id": $("#city_id").val(),
					"publisher_city":"",
					"contributor": contributorArr,
					"category": $("#collection_category").val(),
					"exemplar": $("#exemplar").val(),
					"cover":$('#cover').prop('files')[0],
				};

				$('#form_collection_new input:not(.exclude-clear)').val('');
				addRowCollection(formData);
				closeModalCreate();
			}

		} catch (error) {
				// Code to handle the exception or error
				// The error parameter contains information about the error
				console.error('An error occurred:', error.message);
		}
	}

	function addRowCollection(collection) {

		try {
			tempCollection.push(collection);

			var htmlInput = [];

			const selectOptions = Object.keys(dataDepositHead).map(key => `<option value="${key}">${dataDepositHead[key]}</option>`).join('');
			htmlInput.push(`<input type="hidden" name="collection_copy[${tableIdx}][collection_id]" value="${collection.tahun_terbit}" class="form-control" style="width: 75px">
							<select name="collection_copy[${tableIdx}][deposit_head_id]" class="form-control" disabled>${selectOptions}</select>
							<input type="text" id="collection_copy-${tableIdx}-code" name="collection_copy[${tableIdx}][code]" value="${collection.code}" class="form-control collection-code" style="width: 200px" disabled>`);
			htmlInput.push(`<img id="collection_copy-${tableIdx}-cover" class="form-control collection-cover" style="width: 200px">`);
			htmlInput.push(`<textarea name="collection_copy[${tableIdx}][title]" class="form-control" style="width: 250px" rows="4" disabled>${collection.title}</textarea>`);
			
			var contributorElements = '';
			var contributorSelectOptions = ';'
			contributorSelectOptions = Object.keys(dataContributor).map(key => `<option value="${dataContributor[key].id}">${dataContributor[key].name}</option>`).join('');
				
			if (collection.contributor.length > 0) {
				collection.contributor.forEach((element, index) => {
					contributorElements += `
						<div class="row mx-0" style="width: 500px">
							<select name="collection_copy[${tableIdx}][contributor_id]"  id="collection_copy-${tableIdx}-contributor_id-${index}" class="form-control" style="width: 40%" disabled>${contributorSelectOptions}</select>	
							<input type="text" name="collection_copy[${tableIdx}][author_name]" value="${element.author}" class="form-control" style="width: 60%" disabled>			
						</div>`;
				});
			} else {
				contributorElements += `
					<div class="row mx-0" style="width: 500px">
						<select name="collection_copy[${tableIdx}][contributor_id]"  id="collection_copy-${tableIdx}-contributor_id-0" class="form-control" style="width: 40%" disabled>${contributorSelectOptions}</select>	
						<input type="text" name="collection_copy[${tableIdx}][author_name]" value="" class="form-control" style="width: 60%" disabled>			
					</div>`;
			}
			

			htmlInput.push(contributorElements);
			
			htmlInput.push(`<div class="row mx-0" style="width: 300px">
							<select name="collection_copy[${tableIdx}][publication_month]" id="collection_copy-${tableIdx}-publication_month" class="form-control" style="width: 60%" disabled>
									<option value="">-- Pilih Bulan --</option>
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
							<input type="text" name="collection_copy[${tableIdx}][publication_year]" value="${collection.tahun_terbit}" class="form-control"  style="width: 40%" disabled>
							</div>`);
			htmlInput.push(`Ketebalan : <input type="number" name="collection_copy[${tableIdx}][dimension]" class="form-control" placeholder="0" value="${collection.dimension}" disabled>
							Jumlah Halaman : <input type="number" name="collection_copy[${tableIdx}][total_page]" class="form-control" placeholder="0" value="${collection.jml_hlm}" disabled>`);
			htmlInput.push(`<textarea name="collection_copy[${tableIdx}][description]" class="form-control" style="width: 350px" placeholder="Sinopsis/Deskripsi" disabled>${collection.sinopsis}</textarea>`);
			
			// if ($('input[name="library_id"]').val() == 1) {
			// 	htmlInput.push(`<input type="text" name="collection_copy[${tableIdx}][copy]" class="form-control collection-copy" value="2" style="width: 75px" disabled>`);
			// } else {
			// 	htmlInput.push(`<input type="text" name="collection_copy[${tableIdx}][copy]" class="form-control collection-copy" value="1" style="width: 75px" disabled>`);	
			// }

			htmlInput.push(`<input type="text" name="collection_copy[${tableIdx}][copy]" class="form-control collection-copy" value="${collection.exemplar}" style="width: 75px" disabled>`);
			
			htmlInput.push(`<button type="button" class="btn btn-warning edit-row" onclick="viewCollection(${tableIdx})">Edit</button>
							<button type="button" class="btn btn-danger remove-row" data-index="${tableIdx}">Remove</button>`);
			
			tableForm.row.add(htmlInput).draw(false);

			$('#collection_copy-'+tableIdx+'-publication_month').val(collection.bulan_terbit);
			collection.contributor.forEach((element, index) => {
				$("#collection_copy-"+tableIdx+"-contributor_id-"+index).select2({
					placeholder: '-- Pilih Peran --',
					allowClear: true,
					multiple: false,
					cache: true,
				}).val(element.id_kontributor).trigger("change");
			});

			displayImagePreview(tableIdx, collection);

			tableIdx++;

		} catch (error) {
				// Code to handle the exception or error
				// The error parameter contains information about the error
				console.error('An error occurred:', error.message);
		}
	}

	function displayImagePreview(idx, collection) {
		if (collection.cover) {
			if (collection.cover instanceof File) {
				const reader = new FileReader();

				reader.onload = function (event) {
					// Update the src attribute of the image preview
					$('#collection_copy-'+idx+'-cover').attr('src', event.target.result);
					$('#collection_copy-'+idx+'-cover').css('display', 'block');
				};
				// Read the file as a data URL
				reader.readAsDataURL(collection.cover);
			} else {
				$('#collection_copy-'+idx+'-cover').attr('src', collection.cover_url);
			}
		} else {
			$('#collection_copy-'+idx+'-cover').attr('src', collection.cover_url);
		}
    }

	function viewCollection(rowIdx) {
		console.log(tempCollection);
		var collection = tempCollection[rowIdx];

		$("#collection_id").val(collection.collection_id),
		$("#code").val(collection.code);
		$("#title").val(collection.title);
		$("#publication_year").val(collection.tahun_terbit);
		$("#publication_month").val(collection.bulan_terbit);
		$("#description").val(collection.sinopsis);
		$("#total_page").val(collection.jml_hlm);
		$("#dimension").val(collection.dimension);
		$("#price").val(collection.price);
		$("#exemplar").val(collection.exemplar);
		$("#collection_category").val(collection.category);
		$('#collection_category').trigger('change');
		if (listDepositHeadSerial.includes(collection.deposit_head_id)) {
			$(".serial_container").show();
		} else {
			$(".serial_container").hide();
		}


		collection.contributor.forEach((element,index) => {
			$('#data_contributor').append(`
				<tr class="row-contributor">
					<td class="align-middle">
						<select name="contributor_contributor_id_field[]" data-index="${index}" id="contributor_id_field-${index}"  class="form-control">
							@foreach ($contributor as $c)
								<option value="{{ $c->id }}">{{ $c->name }}</option>
							@endforeach
						</select>
					</td>
					<td class="align-middle">
						<select name="author_id_field[]" id="author_id_field-${index}" class="form-control author-select"></select>
					</td>
					<td class="align-middle">
						<input type="text" name="contributor_title_field[]" id="contributor_title_field-${index}" class="form-control" oninput="validationContributor()" placeholder="Gelar">
					</td>
					<td class="align-middle">
						<input type="number" name="contributor_year_of_birth_field[]" id="contributor_year_of_birth_field-${index}" class="form-control" placeholder="Thn. Lahir">
					</td>
					<td class="align-middle">
						<input type="number" name="contributor_year_of_death_field[]" id="contributor_year_of_death_field-${index}" class="form-control" placeholder="Thn. Mati">
					</td>
					<td class="align-middle">
						<button type="button" class="btn btn-danger btn-sm col-12" id="remove_row_contributor"><i class="la la-trash"></i></button>
					</td>
				</tr>
			`);

			$('#author_id_field-'+index).select2({
				ajax: {
					url: '{{ url("publisher/select2_serverside") }}' + '/' + 'load_author',
					processResults: function(data) {
						// Process the data from the server response
						// and format it into Select2-compatible format
						return {
							results: data
						};
					}
				},
				data: [{ id: element.id_author ?? null, text: element.author }]
			});

			if (element.id_kontributor) {
				$('#contributor_id_field-'+index).val(element.id_kontributor);
			}
			$('#contributor_title_field-'+index).val(element.author_title);
			$('#contributor_year_of_birth_field-'+index).val(element.author_birth);
			$('#contributor_year_of_death_field-'+index).val(element.author_death);
		});

		$('#modal_create').modal('show');
		currentEditingIdx = rowIdx;
	}

	function updateCollection(rowIdx) {

		// const contributorIdInputs = $('select[name="contributor_contributor_id_field[]"]');
		// const authorNameInputs = $('select[name="author_id_field[]"]');

		// var contributorArr = [];
		// contributorIdInputs.each(function(index) {
		// 	const contributor = {
		// 		"id_kontributor": contributorIdInputs.eq(index).val(),
		// 		"kontributor": null,
		// 		"id_author": authorNameInputs.eq(index).val(),
		// 		"author": authorNameInputs.eq(index).text(),
		// 	}
		// 	contributorArr.push(contributor);
		// });

		try {
		
			const contributorIdInputs = $('select[name="contributor_contributor_id_field[]"]');
			const authorTitleInputs = $('input[name="contributor_title_field[]"]');
			const authorBirthInputs = $('input[name="contributor_year_of_birth_field[]"]');
			const authorDeathInputs = $('input[name="contributor_year_of_death_field[]"]');
			
			var contributorArr = [];
			contributorIdInputs.each(function(index) {
				var indx = $(this).data('index');
				var authorNameInputs = $('#author_id_field-'+indx).select2('data');
				const contributor = {
					"id_kontributor": contributorIdInputs.eq(index).val(),
					"kontributor": null,
					"id_author": authorNameInputs[0]?.id,
					"author": authorNameInputs[0]?.text,
					"author_title": authorTitleInputs.eq(index).val(),
					"author_birth": authorBirthInputs.eq(index).val(),
					"author_death": authorDeathInputs.eq(index).val()
				}
				contributorArr.push(contributor);
			});
			
			var file = $('#cover').prop('files')[0];
			if (!file) {
				file = tempCollection[rowIdx].cover;
			}
			const formData = {
					"collection_id": $("#collection_id").val(),
					"deposit_head_id": $("#deposit_head_id").val(),
					"code": $("#code").val(),
					"title": $("#title").val(),
					"tahun_terbit": $("#publication_year").val(),
					"bulan_terbit": $("#publication_month").val(),
					"kepeng": "",
					"sinopsis": $("#description").val(),
					"edisi": $("#edition").val(),
					"jml_hlm": $("#total_page").val(),
					"subjek": "",
					"seri": $("#series").val(),
					"dimension": $("#dimension").val(),
					"price": $("#price").val(),
					"publisher_id": 33,
					"publisher_name": $("#publisher_name").val(),
					"publisher_province_id": $("#province_id").val(),
					"publisher_province": "",
					"publisher_city_id": $("#city_id").val(),
					"publisher_city":"",
					"category":$("#collection_category").val(),
					"contributor": contributorArr,
					"exemplar": $("#exemplar").val(),
					"cover":file,
					"cover_url":tempCollection[rowIdx].cover_url,
			};

			updateRowCollection(formData, rowIdx);
			closeModalCreate();

		} catch (error) {
			console.error('An error occurred:', error.message);
		}
		
	}

	function updateRowCollection(collection, rowIdx) {
		try {
			tempCollection[rowIdx] = collection;
			var htmlInput = [];

			const selectOptions = Object.keys(dataDepositHead).map(key => `<option value="${key}">${dataDepositHead[key]}</option>`).join('');
			htmlInput.push(`<input type="hidden" name="collection_copy[${rowIdx}][collection_id]" value="${collection.tahun_terbit}" class="form-control" style="width: 75px">
							<select name="collection_copy[${rowIdx}][deposit_head_id]" class="form-control" disabled>${selectOptions}</select>
							<input type="text" id="collection_copy-${rowIdx}-code" name="collection_copy[${rowIdx}][code]" value="${collection.code}" class="form-control collection-code" style="width: 200px" disabled>`);
			htmlInput.push(`<img id="collection_copy-${rowIdx}-cover" class="form-control collection-cover" style="width: 200px">`);
			htmlInput.push(`<textarea name="collection_copy[${rowIdx}][title]" class="form-control" style="width: 250px" rows="4" disabled>${collection.title}</textarea>`);
			
			var contributorElements = '';
			var contributorSelectOptions = '';
			contributorSelectOptions = Object.keys(dataContributor).map(key => `<option value="${dataContributor[key].id}">${dataContributor[key].name}</option>`).join('');
				
			if (collection.contributor.length > 0) {
				collection.contributor.forEach((element, index) => {
					contributorElements += `
						<div class="row mx-0" style="width: 500px">
							<select name="collection_copy[${rowIdx}][contributor_id]"  id="collection_copy-${rowIdx}-contributor_id-${index}" class="form-control" style="width: 40%" disabled>${contributorSelectOptions}</select>	
							<input type="text" name="collection_copy[${rowIdx}][author_name]" value="${element.author}" class="form-control" style="width: 60%" disabled>			
						</div>`;
				});
			} else {
				contributorElements += `
					<div class="row mx-0" style="width: 500px">
						<select name="collection_copy[${rowIdx}][contributor_id]"  id="collection_copy-${rowIdx}-contributor_id-0" class="form-control" style="width: 40%" disabled>${contributorSelectOptions}</select>	
						<input type="text" name="collection_copy[${rowIdx}][author_name]" value="" class="form-control" style="width: 60%" disabled>			
					</div>`;
			}
			

			htmlInput.push(contributorElements);
			
			htmlInput.push(`<div class="row mx-0" style="width: 300px">
							<select name="collection_copy[${rowIdx}][publication_month]" id="collection_copy-${rowIdx}-publication_month" class="form-control" style="width: 60%" disabled>
									<option value="">-- Pilih Bulan --</option>
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
							<input type="text" name="collection_copy[${rowIdx}][publication_year]" value="${collection.tahun_terbit}" class="form-control"  style="width: 40%" disabled>
							</div>`);
			htmlInput.push(`Ketebalan : <input type="number" name="collection_copy[${rowIdx}][dimension]" class="form-control" placeholder="0" value="${collection.dimension}" disabled>
							Jumlah Halaman : <input type="number" name="collection_copy[${rowIdx}][total_page]" class="form-control" placeholder="0" value="${collection.jml_hlm}" disabled>`);
			htmlInput.push(`<textarea name="collection_copy[${rowIdx}][description]" class="form-control" style="width: 350px" placeholder="Sinopsis/Deskripsi" disabled>${collection.sinopsis}</textarea>`);
			
			// if ($('input[name="library_id"]').val() == 1) {
			// 	htmlInput.push(`<input type="text" name="collection_copy[${rowIdx}][copy]" class="form-control collection-copy" value="2" style="width: 75px" disabled>`);
			// } else {
			// 	htmlInput.push(`<input type="text" name="collection_copy[${rowIdx}][copy]" class="form-control collection-copy" value="1" style="width: 75px" disabled>`);	
			// }
			
			htmlInput.push(`<input type="text" name="collection_copy[${rowIdx}][copy]" class="form-control collection-copy" value="${collection.exemplar}" style="width: 75px" disabled>`);	

			htmlInput.push(`<button type="button" class="btn btn-warning edit-row" onclick="viewCollection(${rowIdx})">Edit</button>
							<button type="button" class="btn btn-danger remove-row" data-index="${rowIdx}">Remove</button>`);
			
			
			for (let i = 0; i < tableForm.row.length; i++) {
				var tempCode = $('#collection_copy-'+i+'-code').val();
				if (tempCode == collection.code) {
					// rowIdx = i;
					tableForm.row(i).data(htmlInput).draw(false);
					break;
				}
			};

			$('.remove-row').each(function(index, element) {
				const indx = $(this).data('index');
				var tempCode = $('#collection_copy-'+indx+'-code').val();
				if (tempCode == collection.code) {
					// rowIdx = i;
					tableForm.row(index).data(htmlInput).draw(false);
					return;
				}
			});


			$('#collection_copy-'+rowIdx+'-publication_month').val(collection.bulan_terbit);
			collection.contributor.forEach((element, index) => {
				$("#collection_copy-"+rowIdx+"-contributor_id-"+index).select2({
					placeholder: '-- Pilih Peran --',
					allowClear: true,
					multiple: false,
					cache: true,
				}).val(element.id_kontributor).trigger("change");
			});

			displayImagePreview(rowIdx, collection);

		} catch (error) {
			// Code to handle the exception or error
			// The error parameter contains information about the error
			console.error('An error occurred:', error.message);
		}
	}

	function addElementContributor() {
		var index = $('.row-contributor').length;

        $('#data_contributor').append(`
            <tr class="row-contributor">
                <td class="align-middle">
                    <select name="contributor_contributor_id_field[]" id="contributor_id_field-${index}" class="form-control" data-index="${index}" style="width : 300px">
                        @foreach ($contributor as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="align-middle">
                   <select name="author_id_field[]" id="author_id_field-${index}" class="form-control author-select" style="width : 300px"></select>
                </td>
                <td class="align-middle">
                    <input type="text" name="contributor_title_field[]" id="contributor_title_field-${index}" class="form-control" oninput="validationContributor()" placeholder="Gelar" style="width : 200px">
                </td>
                <td class="align-middle">
                    <input type="number" name="contributor_year_of_birth_field[]" id="contributor_year_of_birth_field-${index}" class="form-control" placeholder="Thn. Lahir" style="width : 200px">
                </td>
                <td class="align-middle">
                    <input type="number" name="contributor_year_of_death_field[]" id="contributor_year_of_death_field-${index}" class="form-control" placeholder="Thn. Mati" style="width : 200px">
                </td>
                <td class="align-middle">
                    <button type="button" class="btn btn-danger btn-sm col-12" id="remove_row_contributor"><i class="la la-trash"></i></button>
                </td>
            </tr>
        `);
		select2Author();
        validationContributor();
    }

	function select2DepositHead() {
		$('.deposit-head-id-select').select2({
        	dropdownParent: $('#modal_create'),
			placeholder: '-- Pilih Tipe --',
			allowClear: true,
			multiple: false,
			cache: true,
		});
	}

	function select2Author() {
		$(".author-select").select2({
			placeholder: '-- Nama Kontributor --',
        	dropdownParent: $('#data_contributor'),
			minimumInputLength: 3,
			allowClear: true,
			tags: true,
			cache: true,
			ajax: {
				url: '{{ url("publisher/select2_serverside") }}' + '/' + 'load_author',
				type: 'POST',
				dataType: 'JSON',
				delay: 250,
				headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data: function(params) {
						return {
								search: params.term
						};
				},
				processResults: function(data) {
						return {
								results: data.items
						}
				}
			},
			templateSelection: function (data, container) {
				// $('#author_fullname_field_' + count).val(data.text)
				// $('#author_title_field_' + count).val(data.title)
				// $('#author_year_of_birth_field_' + count).val(data.yob)
				// $('#author_year_of_death_field_' + count).val(data.yod)

				return data.text;
			},
			createTag: function (params) {
							var term = $.trim(params.term);
							if (term === '') {
									return '';
							} else {
									return {
											id: term,
											text: term,
											newTag: true
									}
							}
			}
		});
	}

	function select2Title() {
		$("#title_serial").select2({
			placeholder: 'Masukkan Judul',
        	dropdownParent: $('#modal_create'),
			minimumInputLength: 5,
			allowClear: true,
			tags: true,
			cache: true,
			ajax: {
				url: '{{ url("publisher/select2_serverside") }}' + '/' + 'load_collection',
				type: 'POST',
				dataType: 'JSON',
				delay: 250,
				headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data: function(params) {
						return {
								search: params.term
						};
				},
				processResults: function(data) {
						return {
								results: data.items
						}
				}
			},
			templateSelection: function (data, container) {
				return data.text;
			},
			createTag: function (params) {
				var term = $.trim(params.term);
				if (term === '') {
						return '';
				} else {
						return {
								id: term,
								text: term,
								newTag: true
						}
				}
			}
		});

		$('#title_serial').on('select2:select', function (e) {
			const collection = e.params.data.collection; 
			const physicalDescription = JSON.parse(collection.physical_description);
			$("#parent_id").val(collection.id);
			$("#publication_year").val(collection.publication_year);
			$("#publication_month").val(collection.publication_month);
			$("#description").val(collection.description);
			$("#total_page").val(physicalDescription.total_page);
			$("#dimension").val(physicalDescription.dimension);
			$("#price").val(collection.price);
			
		});
	}

	function validationContributor() {
	}

	function showModalTerms() {
		$('#modal_terms').modal('show');
	}
</script>

@include('publisher.collection.script-serial')
