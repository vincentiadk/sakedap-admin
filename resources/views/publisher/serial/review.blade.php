<div class="app-content content">
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-6 col-12 mb-2">
				<h3 class="content-header-title mb-1 d-inline-block">Edit Pengelolaan Serial</h3><br>
				<div class="row breadcrumbs-top d-inline-block">
					<div class="breadcrumb-wrapper col-12">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ url('publisher/dashboard') }}">Dashboard</a></li>
							<li class="breadcrumb-item"><a href="#">Serial</a></li>
							<li class="breadcrumb-item"><a href="{{ url('publisher/collection/problem/4') }}">Pengelolaan</a></li>
							<li class="breadcrumb-item active">Edit</li>
						</ol>
					</div>
				</div>
			</div>
			<div class="content-header-right col-md-6 col-12 mb-2 mt-1">
				<div class="float-md-right">
					<a href="{{ url('publisher/collection/problem/4') }}" class="btn btn-secondary">Kembali</a>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="configuration">
				<div class="row">
					<div class="col-12">
						<div class="card">
							<div class="card-header">
								<h4 class="card-title text-center">Form Edit</h4>
							</div>
							<div class="card-content collapse show">
								<div class="card-body card-dashboard">
									<form action="{{ url('publisher/collection/update/' . $collection->id) }}" method="POST" enctype="multipart/form-data">
										@csrf
										@if($errors->any())
											<div class="alert alert-danger">
												<ul>
													@foreach ($errors->all() as $error)
														<li>{{ $error }}</li>
													@endforeach
												</ul>
											</div>
										@elseif(session('failed'))
											<div class="alert bg-danger alert-icon-left alert-dismissible mb-2" role="alert">
												<span class="alert-icon"><i class="la la-check"></i></span>
												<button type="button" class="close" data-dismiss="alert" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
												<strong>Failed!</strong> {{ session('failed') }}
											</div>
										@endif
										<ul class="nav nav-tabs nav-justified">
											<li class="nav-item">
												<a class="nav-link active" data-toggle="tab" aria-controls="tab_general" href="#tab_general" aria-expanded="true">General</a>
											</li>
											<li class="nav-item">
												<a class="nav-link" data-toggle="tab" aria-controls="tab_contributor" href="#tab_contributor" aria-expanded="false">Kontributor</a>
											</li>
											<li class="nav-item">
												<a class="nav-link" data-toggle="tab" aria-controls="tab_cover" href="#tab_cover" aria-expanded="false">Cover</a>
											</li>
											<li class="nav-item">
												<a class="nav-link" data-toggle="tab" aria-controls="tab_edition" href="#tab_edition" aria-expanded="false">Edisi</a>
											</li>
										</ul>
										<div class="tab-content px-1 pt-1">
											<div role="tabpanel" class="tab-pane active" id="tab_general" aria-expanded="true">

												<p>
													<div class="form-group">
														<label>Judul :</label>
														<textarea name="title" id="title" class="form-control" placeholder="Masukan judul">{{ $collection->title }}</textarea>
													</div>
													<div class="row">
														<div class="col-md-6">
															<div class="form-group">
																<label>Identifier :</label>
																<input type="text" class="form-control" value="{{ $collection->code }}" disabled>
															</div>
															<div class="form-group">
																<label>Preview :</label>
																<input type="text" class="form-control" placeholder="Masukan preview"  value="{{ $collection->preview }}" disabled>
															</div>
															<div class="form-group">
																<label>Tahun Terbit :</label>
																<input type="text" name="publication_year" id="publication_year" class="form-control" placeholder="Masukan tahun terbit" value="{{ $collection->publication_year }}">
															</div>
															<div class="form-group">
																<label>Tempat Terbit :</label>
																<select name="city_id" id="city_id" class="form-control" style="width:100%;">
																	<option value="{{ $collection->city->id }}">{{ $collection->city->name }}</option>
																</select>
															</div>
														</div>
														<div class="col-md-6">
															<div class="form-group">
																<label>DDC :</label>
																<input type="text" name="ddc" id="ddc" class="form-control" placeholder="Masukan DDC" value="{{ $collection->ddc }}">
															</div>
															<div class="form-group">
																<label>Volume :</label>
																<input type="text" name="volume" id="volume" class="form-control" placeholder="Masukan volume" value="{{ $collection->volume }}">
															</div>
															<div class="form-group">
																<label>Seri :</label>
																<input type="text" name="series" id="series" class="form-control" placeholder="Masukan seri" value="{{ $collection->series }}">
															</div>
															<div class="form-group">
																<label>Serial :</label>
																<select name="serial" id="serial" class="form-control">
																	<option value="">-- Pilih Serial --</option>
																	<option value="1" {{ $collection->serial == 1 ? 'selected' : '' }}>Harian</option>
																	<option value="2" {{ $collection->serial == 2 ? 'selected' : '' }}>Mingguan</option>
																	<option value="3" {{ $collection->serial == 3 ? 'selected' : '' }}>Bulanan</option>
																	<option value="4" {{ $collection->serial == 4 ? 'selected' : '' }}>3 Bulan Sekali</option>
																	<option value="5" {{ $collection->serial == 5 ? 'selected' : '' }}>4 Bulan Sekali</option>
																	<option value="6" {{ $collection->serial == 6 ? 'selected' : '' }}>6 Bulan Sekali</option>
																	<option value="7" {{ $collection->serial == 7 ? 'selected' : '' }}>Tahunan</option>
																	<option value="8" {{ $collection->serial == 8 ? 'selected' : '' }}>2 Tahun Sekali</option>
																	<option value="9" {{ $collection->serial == 9 ? 'selected' : '' }}>3 Tahun Sekali</option>
																</select>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label>Total Halaman :</label>
														<div class="input-group mb-2">
															<input type="number" name="total_page" id="total_page" class="form-control" placeholder="Masukan total halaman" value="{{ ($collection->physicalDescription()) ? $collection->physicalDescription()->total_page : '' }}">
															<div class="input-group-prepend">
																<div class="input-group-text">Halaman</div>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label>Dimensi :</label>
														<div class="input-group mb-2">
															<input type="number" name="dimension" id="dimension" class="form-control" placeholder="Masukan dimensi" value="{{ ($collection->physicalDescription()) ? $collection->physicalDescription()->dimension : '' }}">
															<div class="input-group-prepend">
																<div class="input-group-text">Cm</div>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label>Kategori :</label>
														<select name="collection_category[]" id="collection_category" class="form-control default_select2" style="width:100%;" multiple>
															@foreach($category as $c)
																@php $exist = $collection->collectionCategory->where('category_id', $c->id)->count() @endphp
																<option value="{{ $c->id }}" {{ $exist > 0 ? 'selected' : '' }}>{{ $c->name }}</option>
															@endforeach
														</select>
													</div>
													<div class="form-group">
														<label>Subjek :</label>
														<select name="collection_subject[]" id="collection_subject" class="form-control" style="width:100%;" multiple>
															@foreach($collection->collectionSubject as $cs)
																<option value="{{ $cs->subject->name }}" selected>{{ $cs->subject->name }}</option>
															@endforeach
														</select>
													</div>
													<div class="form-group">
														<label>Keterangan :</label>
														<textarea name="description" id="description" class="form-control" style="resize:none;" placeholder="Masukan informasi lain">{{ $collection->description }}</textarea>
													</div>
												</p>
											</div>
											<div class="tab-pane" id="tab_contributor">
												<p>
													<div class="form-group">
														<div class="row">
															<div class="col-md-6">
																<div class="form-group">
																	<label>Nama :</label>
																	<input type="text" name="fullname_field" id="fullname_field" class="form-control" placeholder="Nama lengkap">
																</div>
															</div>
															<div class="col-md-6">
																<div class="form-group">
																	<label>Kontributor :</label>
																	<select name="contributor_id_field" id="contributor_id_field" class="form-control default_select2" style="width:100%;">
																		@foreach($contributor as $c)
																			<option value="{{ $c->id }}">{{ $c->name }}</option>
																		@endforeach
																	</select>
																</div>
															</div>
															<div class="col-md-4">
																<div class="form-group">
																	<label>Title :</label>
																	<input type="text" name="title_field" id="title_field" class="form-control" placeholder="Title">
																</div>
															</div>
															<div class="col-md-4">
																<div class="form-group">
																	<label>Tahun Kelahiran :</label>
																	<input type="number" name="year_of_birth_field" id="year_of_birth_field" class="form-control" placeholder="Tahun kelahiran">
																</div>
															</div>
															<div class="col-md-4">
																<div class="form-group">
																	<label>Tahun Kematian :</label>
																	<input type="number" name="year_of_death_field" id="year_of_death_field" class="form-control" placeholder="Tahun kematian">
																</div>
															</div>
														</div>
														<div class="form-group">
															<button type="button" onclick="addContributor()" class="btn btn-success col-12">Tambah</button>
														</div>
														<div class="form-group">
															<div class="table-responsive">
																<table class="table table-bordered table-striped" id="datatable_default">
																	<thead class="text-center">
																		<tr>
																			<th>Kontributor</th>
																			<th>Nama</th>
																			<th>Title</th>
																			<th>Tahun Kelahiran</th>
																			<th>Tahun Kematian</th>
																			<th>Hapus</th>
																		</tr>
																	</thead>
																	<tbody id="contributor_element">
																		@foreach($collection->collectionContributor as $cc)
																			<tr class="text-center">
																				<input type="hidden" name="contributor_contributor_id_field[]" value="{{ $cc->contributor_id }}">
																				<input type="hidden" name="contributor_fullname_field[]" value="{{ $cc->author->fullname }}">
																				<input type="hidden" name="contributor_title_field[]" value="{{ $cc->author->title }}">
																				<input type="hidden" name="contributor_year_of_birth_field[]" value="{{ $cc->author->year_of_birth }}">
																				<input type="hidden" name="contributor_year_of_death_field[]" value="{{ $cc->author->year_of_death }}">

																				<td class="align-middle">{{ $cc->contributor->name }}</td>
																				<td class="align-middle">{{ $cc->author->fullname }}</td>
																				<td class="align-middle">{{ $cc->author->title }}</td>
																				<td class="align-middle">{{ $cc->author->year_of_birth }}</td>
																				<td class="align-middle">{{ $cc->author->year_of_death }}</td>
																				<td class="align-middle">
																					<button type="button" class="btn btn-danger btn-sm" id="remove_field_contributor">
																						<i class="la la-trash"></i>
																					</button>
																				</td>
																			</tr>
																		@endforeach
																	</tbody>
																</table>
															</div>
														</div>
													</div>
												</p>
											</div>
											<div class="tab-pane" id="tab_cover">
												<div class="form-group">
													@php $cover = $collection->collectionMedia->where('type', 1)->first(); @endphp
													@if($cover && Storage::exists($cover->link))
														<div class="row justify-content-center">
															<div class="col-md-6">
																<div class="alert alert-warning alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
																	<span class="alert-icon"><i class="la la-info-circle"></i></span>
																	<ul>
																		<li>Ukuran: <b>{{ App\Helper\GeneralHelper::formatSize($cover->size) }}</b></li>
																		<li>Ekstensi: <b>{{ $cover->extension }}</b></li>
																		<li>Mime: <b>{{ $cover->mimes }}</b></li>
																		<li>Hash: <b>{{ $cover->hash }}</b></li>
																		<li>Metode: <b>{{ $cover->method() }}</b></li>
																	</ul>
																</div>
															</div>
														</div>
														<center>
															<a href="{{ url('/collection/cover') . '/' . $cover->id }}" data-lightbox="{{ $collection->title }}" data-title="{{ $collection->title }}"><img src="{{ url('/collection/cover') . '/' . $cover->id }}" style="max-width:242px; max-height:280px;"></a>
														</center>
													@else
														<div class="alert alert-danger text-center">Tidak ada file!</div>
													@endif
													<div class="row justify-content-center mt-2">
														<div class="col-md-6">
															<input type="file" name="cover" class="form-control">
														</div>
													</div>
												</div>
											</div>
											<div class="tab-pane" id="tab_edition">
												<p>
													<div class="form-group">
														<div class="form-group text-right">
															<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal_edition">Tambah</button>
														</div>
														<div class="form-group">
															<div class="table-responsive">
																<table class="table table-bordered table-striped" id="datatable_edition">
																	<thead class="text-center">
																		<tr>
																			<th>Edisi</th>
																			<th>Tanggal</th>
																			<th>Cover</th>
																			<th>Konten</th>
																			<th>Hapus</th>
																		</tr>
																	</thead>
																	<tbody id="edition_element">
																		@foreach($edition as $e)
																			<tr class="text-center">
																				<td class="align-middle">{{ $e->edition }}</td>
																				<td class="align-middle">{{ date('d-m-Y', strtotime($e->date)) }}</td>
																				<td class="align-middle">
																					@php $cover = $e->collectionMedia->where('type', 1)->first(); @endphp
																					@if($cover && Storage::disk('local')->exists($cover->link))
																						<a href="{{ asset(Storage::disk('local')->url($cover->link)) }}" data-lightbox="{{ $cover->link }}" data-title="{{ $cover->link }}"><img src="{{ asset(Storage::disk('local')->url($cover->link)) }}" style="max-height:30px; max-width:30px;"></a>
																					@endif
																				</td>
																				<td class="align-middle">
																					<button type="button" class="btn btn-success btn-sm" data-toggle="modal" onclick="showModalSlide('{{ $e->id }} ')">Lihat File</button>
																				</td>
																				<td> <button type="button" class="btn btn-danger btn-sm" onclick="destroyEdition({{ $e->id }})" id="remove_field_edition"><i class="la la-trash"></td>
																			</tr>
																		@endforeach
																	</tbody>
																</table>
															</div>
														</div>
													</div>
												</p>
											</div>
										</div>
										<div class="form-group"><hr></div>
										<div class="form-group">
											<div class="text-right">
												<button type="reset" class="btn btn-danger">Reset</button>
												<button type="submit" class="btn btn-warning">Simpan Perubahan</button>
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

<div class="modal fade" id="modal_edition" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content" id="modal_edition_content">
			<div class="modal-header">
				<h5 class="modal-title" id="staticBackdropLabel">Edisi Serial</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form action="" id="form_edition">
					<div class="form-group">
							<label>Edisi / Volume :</label>
							<input type="text" name="edition_field" id="edition_field" class="form-control" placeholder="Masukan Edisi">
					</div>
					<div class="form-group">
							<label>Tanggal Terbit Edisi / Volume :</label>
							<input type="date" name="date_field" id="date_field" class="form-control">
					</div>
					<div class="form-group">
							<label>Cover :</label>
							<input type="file" name="cover_field" accept=".jpg,.png,.jpeg" id="cover_field" class="form-control">
					</div>
					<div class="form-group">
							<label>Konten :</label>
							<input type="file" name="original_field" accept=".pdf" id="original_field" class="form-control">
					</div>
					<div class="form-group">
							<label>Nomor Deposit:</label>
							<input type="text" name="number_deposit_field" id="number_deposit_field" class="form-control" readonly="">
					</div>
					<div class="form-group">
							<label>Jumlah Halaman:</label>
							<input type="text" name="total_page_field" id="total_page_field" class="form-control">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onclick="addEdition()">Tambah</button>
			</div>
		</div>
	</div>
</div>

<div class="modal" id="modal-slide-content" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-body">
				<center>
			<a class="btn btn-primary btn-sm" href="#" onclick="prevModal()"><<</a><input type="number" name="key_carousel"  onchange="loadPdfImageModal($('#collection_edition_id').val())" min="0" value="1" id="key_carousel_modal"> / <sub id="total_data_image_pdf_modal"></sub> <a href="#" class="btn btn-success btn-sm" onclick="nextModal()">>></a>
			<p></p>
			<input type="hidden" name="collection-edition-id" id="collection_edition_id">
			<div class="thumbs_gall_slider_con content_thumbs_gall clearfix">

				<div class="thumbs_gall_slider_larg owl-carousel" data-transition="fadeUp">
						<div class="item">
							<img class="d-block w-100" src="" id="data_image_pdf_modal" style="height:903px;">
						</div>
				</div>
			</div>
			<div class="form-group">
								<a class="btn btn-primary btn-sm" href="#" onclick="prevModal()"><<</a> <a href="#" class="btn btn-success btn-sm" onclick="nextModal()">>></a>
						</div>
				</center>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<script>
	$(function() {
		$('#datatable_default tbody').on('click', '#remove_field_contributor', function () {
			$('#datatable_default').DataTable().row($(this).parents('tr')).remove().draw();
		});

		$('#datatable_edition tbody').on('click', '#remove_field_edition', function () {
			$('#datatable_edition').DataTable().row($(this).parents('tr')).remove().draw();
		});

		$('a[data-toggle="tab"]').on('shown.bs.tab', function() {
			$('#datatable_edition').DataTable().columns.adjust();
		});

		$('a[data-toggle="tab"]').on('shown.bs.tab', function() {
			$('#datatable_default').DataTable().columns.adjust();
		});

		$('.default_select2').select2({
			placeholder: '-- Pilih --'
		});

		select2AutoSuggest('#publisher_id', 'load_publisher');
		select2AutoSuggest('#city_id', 'load_city');
		select2AutoSuggestTags('#collection_subject', 'load_subject');
	});

	function destroyEdition(id) {
		$.ajax({
			url: '{{ url("publisher/collection/edition/destroy") }}',
			type: 'POST',
			dataType: 'JSON',
			data: {
				id: id
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
	}

	function addContributor() {
		var contributor_id_field = $('#contributor_id_field').val();
		var fullname_field       = $('#fullname_field').val();
		var title_field          = $('#title_field').val();
		var year_of_birth_field  = $('#year_of_birth_field').val();
		var year_of_death_field  = $('#year_of_death_field').val();

		if(!contributor_id_field || !fullname_field || !title_field) {
			Swal.fire('Harap mengisi kontributor, nama, dan title!', '', 'warning');
		} else {
			$.ajax({
				url: '{{ url("publisher/contributor/show") }}' + '/' + contributor_id_field,
				type: 'GET',
				dataType: 'JSON',
				beforeSend: function() {
					loadingOpen('#configuration');
					$('#validasi_element').hide();
					$('#validasi_content').html('');
				},
				success: function(response) {
					loadingClose('#configuration');
					$('#form_data').append(`
						<input type="hidden" name="contributor_contributor_id_field[]" value="` + contributor_id_field + `">
						<input type="hidden" name="contributor_fullname_field[]" value="` + fullname_field + `">
						<input type="hidden" name="contributor_title_field[]" value="` + title_field + `">
						<input type="hidden" name="contributor_year_of_birth_field[]" value="` + year_of_birth_field + `">
						<input type="hidden" name="contributor_year_of_death_field[]" value="` + year_of_death_field + `">
					`);

					$('#datatable_default').DataTable().row.add([
						response.name,
						fullname_field,
						title_field,
						year_of_birth_field,
						year_of_death_field,
						'<button type="button" class="btn btn-danger btn-sm" id="remove_field_contributor"><i class="la la-trash"></i></button>'
					]).draw().node();

					$('#contributor_id_field').val('').trigger('change');
					$('#fullname_field').val('');
					$('#title_field').val('');
					$('#year_of_birth_field').val('');
					$('#year_of_death_field').val('');
				}
			});
		}
	}

	function addEdition() {
		var edition_field  = $('#edition_field').val();
		var date_field     = $('#date_field').val();
		var cover_field    = $('#cover_field').val();
		var original_field = $('#original_field').val();
		var number_deposit_field = $('#number_deposit_field').val();
		var total_page_field = $('#total_page_field').val();

		if(!edition_field || !date_field || !cover_field || !original_field || !total_page_field) {
				Swal.fire('Harap mengisi semua field!', '', 'warning');
		} else {
			$.ajax({
				url: '{{ url("publisher/collection/edition/create") }}' + '/' + '{{ $collection->id }}',
				type: 'POST',
				dataType: 'JSON',
				data: new FormData($('#form_edition')[0]),
				cache: false,
				contentType: false,
				processData: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				beforeSend: function() {
					loadingOpen('#modal_edition_content');
				},
				success: function(response) {
					loadingClose('#modal_edition_content');
					location.reload();
					$('#datatable_edition').DataTable().row.add([
						edition_field,
						response.date_field,
						response.cover_field,
						response.original_field,
						'<button type="button" class="btn btn-danger btn-sm" onclick="destroyEdition(' + response.id + ')" id="remove_field_edition"><i class="la la-trash"></i></button>'
					]).draw().node();

					$('#modal_edition').modal('hide');
					$('#edition_field').val('');
					$('#date_field').val('');
					$('#cover_field').val('');
					$('#original_field').val('');

				},
				error: function() {
					$('modal_edition').modal('hide')
					Toast.fire({
						icon: 'error',
						title: 'Server Error!'
					});
				}
			});
		}
	}

	var total_image = 1;
	var total_image_modal = 1;
	var page = 1;
	var pageModal = 1;
	var collId ="";

	$(function() {
			loadPdfImage();
		});

		function showModalSlide(collectionId) {
			$('#modal-slide-content').modal('show');
			$('#collection_edition_id').val(collectionId);
			total_image_modal = 1;
			pageModal = 1;
		collId = collectionId;
			$('#data_image_pdf_modal').attr('src', '');
			$('#total_data_image_pdf_modal').html('');
			$('#key_carousel_modal').val(pageModal);
			loadPdfImageModal(pageModal, false, collId);
		}

		function loadPdfImageModal(pageModal = 1, nextprev = false, collectionId) {
			if(pageModal > total_image_modal) {
				return;
			}
			if(! nextprev){
				pageModal =  $('#key_carousel_modal').val();
			}
			$.ajax({
				url: '{{ url("collection/load_image_pdf") }}',
				type: 'GET',
				dataType: 'JSON',
				data: {
					collection_id: collectionId,
					key: pageModal
				},
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				success: function(response) {
					total_image = response.total_data;
					if(response.image) {
						$('#data_image_pdf_modal').attr('src', response.image);
					} else {
						$('#data_image_pdf_modal').attr('src', "{{ url('main/file-not-found.jpg')  }}");
					}

					$('#total_data_image_pdf_modal').html(response.total_data);
					$('#key_carousel_modal').val(pageModal);
				},
				error: function() {
					false;
				}
			});
		}
		function nextModal() {
			pageModal = parseInt($('#key_carousel_modal').val()) + 1;
			loadPdfImageModal(pageModal, true, collId);
		}
		function prevModal() {
			pageModal = parseInt($('#key_carousel_modal').val()) - 1;
			loadPdfImageModal(pageModal, true, collId);
		}

		function loadPdfImage(page = 1, nextprev = false) {
			if(page > total_image) {
				return;
			}
			if(! nextprev){
				page =  $('#key_carousel').val();
			}
			$.ajax({
				url: '{{ url("collection/load_image_pdf") }}',
				type: 'GET',
				dataType: 'JSON',
				data: {
					collection_id: '{{ $collection->id }}',
					key: page
				},
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				success: function(response) {
					total_image = response.total_data;
					if(response.image) {
						$('#data_image_pdf').attr('src', response.image);
					} else {
						$('#data_image_pdf').attr('src', "{{ url('main/file-not-found.jpg')  }}");
					}

					$('#total_data_image_pdf').html(response.total_data);
					$('#key_carousel').val(page);
				},
				error: function() {
					false;
				}
			});
		}
		function next() {
			page = parseInt($('#key_carousel').val()) + 1;
			loadPdfImage(page, true);
		}
		function prev() {
			page = parseInt($('#key_carousel').val()) - 1;
			loadPdfImage(page, true);
		}
</script>
