<div class="app-content content">
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-6 col-12 mb-2">
				<h3 class="content-header-title mb-1 d-inline-block">Edit Pengelolaan Serial</h3><br>
				<div class="row breadcrumbs-top d-inline-block">
					<div class="breadcrumb-wrapper col-12">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
							<li class="breadcrumb-item"><a href="#">Serial</a></li>
							<li class="breadcrumb-item"><a href="{{ url('admin/collection/manage/4') }}">Pengelolaan</a></li>
							<li class="breadcrumb-item active">Edit</li>
						</ol>
					</div>
				</div>
			</div>
			<div class="content-header-right col-md-6 col-12 mb-2 mt-1">
				<div class="float-md-right">
					<a href="{{ url('admin/collection/create_manual/4') }}" class="btn btn-primary">Tambah Data Baru</a>
					<a href="{{ url('admin/collection/manage/4') }}" class="btn btn-secondary">Kembali</a>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="configuration">
				<div class="row">
					<div class="col-12">
						<div class="card">
							<div class="card-content collapse show">
								<div class="card-body card-dashboard">
									<form method="POST" action="{{ $collection->lock ? $locked_url : '' }}" enctype="multipart/form-data" class="form">
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
										<h4 class="form-section">Meta Data</h4>
										<p>
											<div class="form-group row">
												<label class="col-md-2">Penerbit :</label>
												<div class="col-md-10">
														<select name="publisher_id" id="publisher_id" class="form-control" style="width:100%;" {{ $collection->lock ? 'disabled' : '' }}>
														<option value="{{ $collection->publisher->id }}" selected>{{ $collection->publisher->name }}</option>
														</select>
												</div>
											</div>
											<div class="form-group row">
												<label class="col-md-2">Judul Asli :</label>
												<div class="col-md-10">
                                                    <textarea class="form-control" disabled>{{ $collection->title_ori }}</textarea>
												</div>
											</div>
                                            <div class="form-group row">
												<label class="col-md-2">Judul Perubahan :</label>
												<div class="col-md-10">
                                                    <textarea name="title" id="title" class="form-control" placeholder="Masukan judul" {{ $collection->lock ? 'disabled' : '' }}>{{ $collection->title }}</textarea>
												</div>
											</div>
											<div class="form-group row">
												<label class="col-md-2">ISSN :</label>
												<div class="col-md-10">
														<input type="text" class="form-control" value="{{ $collection->code }}" disabled>
												</div>
											</div>
											<div class="form-group row">
												<label class="col-md-2">Preview :</label>
												<div class="col-md-10">
														<input type="text" class="form-control" placeholder="Masukan preview"  value="{{ $collection->preview }}" disabled>
												</div>
											</div>
											<div class="form-group row">
												<label class="col-md-2">Bulan Terbit Pertama Kali :</label>
												<div class="col-md-10">
														<select name="publication_month" id="publication_month" class="form-control" {{ $collection->lock ? 'disabled' : '' }}>
														<option value="">-- Pilih --</option>
														<option value="01" {{ $collection->publication_month == '01' ? 'selected' : '' }}>{{ App\Helper\GeneralHelper::getMonth('01') }}</option>
														<option value="02" {{ $collection->publication_month == '02' ? 'selected' : '' }}>{{ App\Helper\GeneralHelper::getMonth('02') }}</option>
														<option value="03" {{ $collection->publication_month == '03' ? 'selected' : '' }}>{{ App\Helper\GeneralHelper::getMonth('03') }}</option>
														<option value="04" {{ $collection->publication_month == '04' ? 'selected' : '' }}>{{ App\Helper\GeneralHelper::getMonth('04') }}</option>
														<option value="05" {{ $collection->publication_month == '05' ? 'selected' : '' }}>{{ App\Helper\GeneralHelper::getMonth('05') }}</option>
														<option value="06" {{ $collection->publication_month == '06' ? 'selected' : '' }}>{{ App\Helper\GeneralHelper::getMonth('06') }}</option>
														<option value="07" {{ $collection->publication_month == '07' ? 'selected' : '' }}>{{ App\Helper\GeneralHelper::getMonth('07') }}</option>
														<option value="08" {{ $collection->publication_month == '08' ? 'selected' : '' }}>{{ App\Helper\GeneralHelper::getMonth('08') }}</option>
														<option value="09" {{ $collection->publication_month == '09' ? 'selected' : '' }}>{{ App\Helper\GeneralHelper::getMonth('09') }}</option>
														<option value="10" {{ $collection->publication_month == '10' ? 'selected' : '' }}>{{ App\Helper\GeneralHelper::getMonth('10') }}</option>
														<option value="11" {{ $collection->publication_month == '11' ? 'selected' : '' }}>{{ App\Helper\GeneralHelper::getMonth('11') }}</option>
														<option value="12" {{ $collection->publication_month == '12' ? 'selected' : '' }}>{{ App\Helper\GeneralHelper::getMonth('12') }}</option>
														</select>
												</div>
											</div>
											<div class="form-group row">
												<label class="col-md-2">Tahun Terbit Pertama Kali :</label>
												<div class="col-md-10">
														<input type="text" name="publication_year" id="publication_year" class="form-control" placeholder="Masukan tahun terbit" value="{{ $collection->publication_year }}" {{ $collection->lock ? 'disabled' : '' }}>
												</div>
											</div>
											<div class="form-group row">
												<label class="col-md-2">DDC :</label>
												<div class="col-md-10">
														<input type="text" name="ddc" id="ddc" class="form-control" placeholder="Masukan DDC" value="{{ $collection->ddc }}" {{ $collection->lock ? 'disabled' : '' }}>
												</div>
											</div>
											<div class="form-group row">
												<label class="col-md-2">Serial :</label>
												<div class="col-md-10">
														<select name="serial" id="serial" class="form-control" {{ $collection->lock ? 'disabled' : '' }}>
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
											<div class="form-group row">
												<label class="col-md-2">Dimensi :</label>
												<div class="col-md-10">
														<div class="input-group mb-2">
														<input type="number" name="dimension" id="dimension" class="form-control" placeholder="Masukan dimensi" value="{{ ($collection->physicalDescription()) ? $collection->physicalDescription()->dimension : '' }}" {{ $collection->lock ? 'disabled' : '' }}>
														<div class="input-group-prepend">
																<div class="input-group-text">Cm</div>
														</div>
														</div>
												</div>
											</div>
											<div class="form-group row">
												<label class="col-md-2">Kategori :</label>
												<div class="col-md-10">
														<select name="collection_category[]" id="collection_category" class="form-control select2" style="width:100%;" multiple {{ $collection->lock ? 'disabled' : '' }}>
														@foreach($category as $c)
																@php $exist = $collection->collectionCategory->where('category_id', $c->id)->count() @endphp
																<option value="{{ $c->id }}" {{ $exist > 0 ? 'selected' : '' }}>{{ $c->name }}</option>
														@endforeach
														</select>
												</div>
											</div>
											<div class="form-group row">
												<label class="col-md-2">Subjek :</label>
												<div class="col-md-10">
														<select name="collection_subject[]" id="collection_subject" class="form-control" style="width:100%;" multiple {{ $collection->lock ? 'disabled' : '' }}>
														@foreach($collection->collectionSubject as $cs)
																<option value="{{ $cs->subject->name }}" selected>{{ $cs->subject->name }}</option>
														@endforeach
														</select>
												</div>
											</div>
											<div class="form-group row">
												<label class="col-md-2">Keterangan :</label>
												<div class="col-md-10">
														<textarea name="description" id="description" class="form-control" style="resize:none;" placeholder="Masukan informasi lain" {{ $collection->lock ? 'disabled' : '' }}>{{ $collection->description }}</textarea>
												</div>
											</div>
										</p>
										<h4 class="form-section">Hak Akses</h4>
										<p>
												<div class="alert alert-light">
														<div class="form-check">
																<input type="radio" class="form-check-input" name="access" id="access_1" value="1" {{ $collection->access == 1 ? 'checked' : '' }} {{ $collection->createdBy->userable_type == 'publishers' || $collection->lock ? 'disabled' : '' }}>
																<label class="form-check-label" for="access_1">
																		Akses full file berwatermak secara online
																</label>
														</div>
												</div>
												<div class="alert alert-light">
														<div class="form-check">
																<input type="radio" class="form-check-input" name="access" id="access_2" value="2" {{ $collection->access == 2 ? 'checked' : '' }} {{ $collection->createdBy->userable_type == 'publishers' || $collection->lock ? 'disabled' : '' }}>
																<label class="form-check-label" for="access_2">
																		Akses hanya preview file secara online, namun tetap dapat di dayagunakan di lingkungan perpustakaan nasional RI dengan jaringan internet LAN
																</label>
														</div>
												</div>
												<div class="alert alert-light">
														<div class="form-check">
																<input type="radio" class="form-check-input" name="access" id="access_3" value="3" {{ $collection->access == 3 ? 'checked' : '' }} {{ $collection->createdBy->userable_type == 'publishers' || $collection->lock ? 'disabled' : '' }}>
																<label class="form-check-label" for="access_3">
																		Akses hanya file preview secara online, dan tidak didayagunakan di lingkungan Perpustakaan Nasional RI selama 5 tahun sejak diserahkan. Setelah 5 tahun, akan didayagunakan oleh Perpustakaan Nasional RI di jaringan internet LAN
																</label>
														</div>
												</div>
												<div class="alert alert-light">
														<div class="form-check">
																<input type="radio" class="form-check-input" name="access" id="access_4" value="4" {{ $collection->access == 4 ? 'checked' : '' }} {{ $collection->createdBy->userable_type == 'publishers' || $collection->lock ? 'disabled' : '' }}>
																<label class="form-check-label" for="access_4">
																		Akses hanya file preview secara online selamanya dan tidak didayagunakan di mana pun
																</label>
														</div>
												</div>
										</p>
										<h4 class="form-section">Kontributor</h4>
										<p>
											<table class="table table-bordered table-striped">
												<tbody id="data_contributor">
													@foreach($collection->collectionContributor as $cc)
														<tr>
															<td class="align-middle">
																<select name="contributor_contributor_id_field[]" class="form-control select2">
																	@foreach($contributor as $c)
																		<option value="{{ $c->id }}" {{ $c->id == $cc->contributor_id ? 'selected' : '' }}>{{ $c->name }}</option>
																	@endforeach
																</select>
															</td>
															<td class="align-middle">
																<input type="text" name="contributor_fullname_field[]" class="form-control" value="{{ $cc->author->fullname }}" oninput="validationContributor()" placeholder="Nama">
															</td>
															<td class="align-middle">
																<input type="text" name="contributor_title_field[]" class="form-control" value="{{ $cc->author->title }}" oninput="validationContributor()" placeholder="Gelar">
															</td>
															<td class="align-middle">
																<input type="number" name="contributor_year_of_birth_field[]" class="form-control" value="{{ $cc->author->year_of_birth }}" placeholder="Thn. Lahir">
															</td>
															<td class="align-middle">
																<input type="number" name="contributor_year_of_death_field[]" class="form-control" value="{{ $cc->author->year_of_death }}" placeholder="Thn. Mati">
															</td>
															<td class="align-middle">
																<button type="button" class="btn btn-danger btn-sm col-12" id="remove_row_contributor"><i class="la la-trash"></i></button>
															</td>
														</tr>
													@endforeach
												</tbody>
											</table>
											<div class="form-group">
												<button type="button" class="btn btn-success btn-sm col-12" onclick="addElementContributor()"><i class="la la-plus"></i></button>
											</div>
										</p>
										<h4 class="form-section">Cover</h4>
										<div class="form-group">
											@php $cover = $collection->collectionMedia->where('type', 1)->first(); @endphp
											@if($cover)
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
													<a href="{{ url('collection/cover') . '/' . $cover->id }}" data-lightbox="Cover Collection" data-title="{{ $collection->title }}">
														<img src="{{ url('collection/cover') . '/' . $cover->id }}" style="max-height:280px; max-width:242px;">
													</a>
												</center>
											@else
												<div class="alert alert-danger text-center">Tidak ada file!</div>
											@endif
											<div class="row justify-content-center mt-2">
												<div class="col-md-6">
													<input type="file" name="cover" class="form-control" {{ $collection->lock ? 'disabled' : '' }}>
												</div>
											</div>
										</div>
										<h4 class="form-section">Edisi</h4>
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
																	<th>Manual</th>
																	<th>Hapus</th>
																</tr>
															</thead>
															<tbody id="edition_element">
																@foreach($edition as $e)
																	<tr class="text-center">
																		<td class="align-middle">{{ $e->edition }}</td>
																		<td class="align-middle">{{ $e->date ?? '' }}</td>
																		<td class="align-middle">
																			@php $cover = $e->collectionMedia->where('type', 1)->first(); @endphp
																			@if($cover)
																			<a href="{{ url('collection/cover') . '/' . $cover->id }}" data-lightbox="Cover Collection" data-title="{{ $collection->title }}">
																				<img src="{{ url('collection/cover') . '/' . $cover->id }}" style="max-height:280px; max-width:242px;">
																			</a>
																			@endif
																		</td>
																		<td class="align-middle">
																			<button type="button" class="btn btn-success btn-sm" data-toggle="modal" onclick="showModalSlide('{{ $e->id }}')">Lihat File</button>
																		</td>
																		<td class="align-middle">
																			@if($e->manual) Manual @else Mandiri @endif
																		</td>
																		<td class="align-middle">
																			<button type="button" class="btn btn-danger btn-sm" onclick="destroyEdition({{ $e->id }})" id="remove_field_edition"><i class="la la-trash"></i></button>
																		</td>
																	</tr>
																@endforeach
															</tbody>
														</table>
													</div>
												</div>
											</div>
										</p>
										<div class="form-group"><hr></div>
										<div class="form-group">
											<div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <ul id="validation_contributor" class="text-danger font-italic"></ul>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="text-right">
                                                            @if($access_lock > 0)
                                                                <fieldset class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" name="lock" onchange="formUrl()" value="{{ $collection->lock }}" {{ $collection->lock ? 'checked' : '' }}> Kunci
                                                                    </label>
                                                                </fieldset>
                                                            @endif
                                                            <button type="submit" name="cancel" value="cancel" class="btn btn-secondary">Batal Edit</button>
                                                            <button type="reset" class="btn btn-danger" {{ $collection->lock ? 'disabled' : '' }}>Reset</button>
                                                            <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
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
			</section>
		</div>
	</div>
</div>
<div class="modal" id="modal-slide-content" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-body">
				<center>
			<a class="btn btn-primary btn-sm" href="#" onclick="prevModal()"><<</a><input type="number" name="key_carousel"  onchange="loadPdfImageModal($('#collection_edition_id').val())" min="0" value="1" id="key_carousel_modal"> / <sub id="total_data_image_pdf_modal"></sub> <a href="#" class="btn btn-success btn-sm" onclick="nextModal()">>></a>
			<p></p>
			<input type="hidden" name="collection-edition-id" id="collection_edition_id">
			<div class="thumbs_gall_slider_con content_thumbs_gall clearfix">

				<div class="thumbs_gall_slider_larg owl-carousel" data-transition="fadeUp">
						<div class="item">
							<img class="d-block w-100" src="" id="data_image_pdf_modal" style="max-height:903px; width:100%;">
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
<div class="modal animated bounceInRight text-left" id="modal_element" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel49" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel49">Histori Koleksi</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				 <div class="card-body card-dashboard">
						<table class="table table-striped table-bordered display nowrap" id="datatable_serverside">
							<thead class="text-center">
								<tr>
									<th>No</th>
									<th>Aksi</th>
									<th>Tanggal</th>
								</tr>
							</thead>
							<tbody id="list-history"></tbody>
						</table>
					</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn grey btn-secondary" data-dismiss="modal">Tutup</button>
			</div>
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
						<label>Edisi :</label>
						<input type="text" name="edition_field" id="edition_field" class="form-control" placeholder="Masukan Edisi">
					</div>
					<div class="form-group">
						<label>Tanggal :</label>
						<input type="date" name="date_field" id="date_field" class="form-control">
					</div>
					<div class="form-group">
						<label>Cover :</label>
						<input type="file" name="cover_field" id="cover_field" class="form-control">
					</div>
					<div class="form-group">
						<label>Konten :</label>
						<input type="file" name="original_field" id="original_field" class="form-control">
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

<script>
	$(function() {
		$('#data_contributor').on('click', '#remove_row_contributor', function() {
			$(this).closest('tr').remove();
		});

		$('#datatable_edition tbody').on('click', '#remove_field_edition', function () {
			$('#datatable_edition').DataTable().row($(this).parents('tr')).remove().draw();
		});

		select2AutoSuggest('#publisher_id', 'load_publisher');
		select2AutoSuggestTags('#collection_subject', 'load_subject');
	});

    function formUrl() {
        var locked     = '{{ $collection->lock }}';
        var locked_url = '{{ $locked_url }}';

        if(locked == 1) {
            $('.form').attr('action', locked_url);
        } else {
            if($('input[name="lock"]').prop('checked')) {
                $('.form').attr('action', locked_url);
            } else {
                $('.form').removeAttr('action');
            }
        }
    }

	function destroyEdition(id) {
		$.ajax({
			url: '{{ url("admin/collection/edition/destroy") }}',
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

	function addElementContributor() {
		$('#data_contributor').append(`
			<tr>
				<td class="align-middle">
					<select name="contributor_contributor_id_field[]" class="form-control select2">
						@foreach($contributor as $c)
							<option value="{{ $c->id }}">{{ $c->name }}</option>
						@endforeach
					</select>
				</td>
				<td class="align-middle">
					<input type="text" name="contributor_fullname_field[]" class="form-control" oninput="validationContributor()" placeholder="Nama">
				</td>
				<td class="align-middle">
					<input type="text" name="contributor_title_field[]" class="form-control" oninput="validationContributor()" placeholder="Gelar">
				</td>
				<td class="align-middle">
					<input type="number" name="contributor_year_of_birth_field[]" class="form-control" placeholder="Thn. Lahir">
				</td>
				<td class="align-middle">
					<input type="number" name="contributor_year_of_death_field[]" class="form-control" placeholder="Thn. Mati">
				</td>
				<td class="align-middle">
					<button type="button" class="btn btn-danger btn-sm col-12" id="remove_row_contributor"><i class="la la-trash"></i></button>
				</td>
			</tr>
		`);

        validationContributor();

        $('.select2').select2({
            placeholder: '-- Pilih --'
        });
	}

	function addEdition() {
		var edition_field  = $('#edition_field').val();
		var date_field     = $('#date_field').val();
		var cover_field    = $('#cover_field').val();
		var original_field = $('#original_field').val();

		if(!edition_field || !date_field || !cover_field || !original_field) {
			Swal.fire('Harap mengisi semua field!', '', 'warning');
		} else {
			$.ajax({
				url: '{{ url("admin/collection/edition/create") }}' + '/' + '{{ $collection->id }}',
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
					$('#datatable_edition').DataTable().row.add([
						edition_field,
						response.date_field,
						response.cover_field,
						response.original_field,
						'Manual',
						'<button type="button" class="btn btn-danger btn-sm" onclick="destroyEdition(' + response.id + ')" id="remove_field_edition"><i class="la la-trash"></i></button>'
					]).draw().node();

					$('#modal_edition').modal('hide');
					$('#edition_field').val('');
					$('#date_field').val('');
					$('#cover_field').val('');
					$('#original_field').val('');
				}
			});
		}
	}
	function showModalSlide(collectionId) {
		$('#modal-slide-content').modal('show');
		$('#collection_edition_id').val(collectionId);
		total_image_modal = 1;
		pageModal = 1;
		collModalId = collectionId;
		$('#data_image_pdf_modal').attr('src', '');
		$('#total_data_image_pdf_modal').html('');
		$('#key_carousel_modal').val(pageModal);
		loadPdfImageModal(pageModal, false, collModalId);
	}

	function loadPdfImageModal(pageModal = 1, nextprev = false, collectionId) {
		console.log(total_image_modal)
		console.log(pageModal)
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
				total_image_modal = response.total_data;
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
		loadPdfImageModal(pageModal, true, collModalId);
	}
	function prevModal() {
		pageModal = parseInt($('#key_carousel_modal').val()) - 1;
		loadPdfImageModal(pageModal, true, collModalId);
	}
</script>
