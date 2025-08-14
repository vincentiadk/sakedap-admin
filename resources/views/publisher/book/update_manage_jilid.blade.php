<div class="app-content content">
  <div class="content-wrapper">
	<div class="content-header row">
	  <div class="content-header-left col-md-6 col-12 mb-2">
		<h3 class="content-header-title mb-1 d-inline-block">Edit Pengelolaan Buku</h3><br>
		<div class="row breadcrumbs-top d-inline-block">
		  <div class="breadcrumb-wrapper col-12">
			<ol class="breadcrumb">
			  <li class="breadcrumb-item"><a href="{{ url('publisher/dashboard') }}">Dashboard</a></li>
			  <li class="breadcrumb-item"><a href="#">Buku</a></li>
			  <li class="breadcrumb-item"><a href="{{ url('publisher/collection/problem/1') }}">Buku Bermasalah</a></li>
			  <li class="breadcrumb-item active">Edit</li>
			</ol>
		  </div>
		</div>
	  </div>
	  <div class="content-header-right col-md-6 col-12 mb-2 mt-1">
		<div class="float-md-right">
		  <a href="{{ url('publisher/collection/problem/1') }}" class="btn btn-secondary">Kembali</a>
		</div>
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
		@endif
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
						<a class="nav-link" data-toggle="tab" aria-controls="tab_original" href="#tab_original" aria-expanded="false">Jilid</a>
					  </li>
					</ul>
					<div class="tab-content px-1 pt-1">
					  <div role="tabpanel" class="tab-pane active" id="tab_general" aria-expanded="true">
						<div class="row">
						  <div class="col-md-12">
							  <div class="alert alert-danger text-center font-weight-bold">
								@php
								  $problem = "<p>";
								  if($collection->collectionProblem) {
									foreach($collection->collectionProblem as $p) {
									  if($p->problem){
										$problem .= $p->problem->name . "</br>";
									  }
									}
								  }
								  $problem .= $collection->problem ."</p>";
								@endphp
								  <span>Masalah : </span>
								  {!! $problem !!}
								  </span>
							  </div>
							</div>
						</div>
						<p>
						  <div class="form-group">
							<label>Judul :</label>
							<textarea name="title" id="title" class="form-control" placeholder="Masukan judul">{{ $collection->title }}</textarea>
						  </div>
						  <div class="form-group">
							<label>Identifier :</label>
							<input type="text" class="form-control" value="{{ $collection->code }}" disabled>
						  </div>
						  <div class="form-group">
							<label>Tahun Terbit :</label>
							<input type="text" name="publication_year" id="publication_year" class="form-control" placeholder="Masukan tahun terbit" value="{{ $collection->publication_year }}">
						  </div>
						  <div class="form-group">
							<label>Preview :</label>
							<input type="text" class="form-control" placeholder="Masukan preview"  value="{{ $collection->preview }}">
						  </div>
						  <div class="form-group">
							<label>Total Halaman :</label>
							<div class="input-group mb-2">
							  <input type="number" name="total_page" id="total_page" class="form-control" placeholder="Masukan total halaman" value="{{ $collection->physicalDescription() ? $collection->physicalDescription()->total_page : '' }}">
							  <div class="input-group-prepend">
								<div class="input-group-text">Halaman</div>
							  </div>
							</div>
						  </div>
						  <div class="form-group">
							<label>Dimensi :</label>
							<div class="input-group mb-2">
							  <input type="number" name="dimension" id="dimension" class="form-control" placeholder="Masukan dimensi" value="{{ $collection->physicalDescription() ? $collection->physicalDescription()->dimension : '' }}">
							  <div class="input-group-prepend">
								<div class="input-group-text">Cm</div>
							  </div>
							</div>
						  </div>
						  <div class="form-group">
							<label>Ilustrasi :</label>
							<select name="ilustration" id="ilustration" class="form-control">
							  <option value="">-- Pilih Ilustrasi --</option>
							  <option value="Ya" {{ $collection->physicalDescription() && $collection->physicalDescription()->ilustration == 'Ya' ?  'selected' : '' }}>Ya</option>
							  <option value="Tidak" {{ $collection->physicalDescription() && $collection->physicalDescription()->ilustration == 'Tidak' ?  'selected' : '' }}>Tidak</option>
							</select>
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
								  <label>Titel :</label>
								  <input type="text" name="title_field" id="title_field" class="form-control" placeholder="Titel">
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
									  <th>Titel</th>
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
					  <div class="tab-pane" id="tab_original">
						 <p>
						  <div class="form-group">
							<div class="form-group">
							  <div class="table-responsive">
								<table class="table table-bordered table-striped">
								  <thead class="text-center">
									<tr>
									  <th>ISBN</th>
									  <th>Tanggal</th>
									  <th>Cover</th>
									  <th>Konten</th>
									  <th>Action</th>
									</tr>
								  </thead>
								  <tbody id="edition_element">
									@foreach($collection->edition()->get() as $key => $e)
									  <tr class="text-center">
										<td class="align-middle">{{ $e->code }}</td>
										<td class="align-middle">{{ date('d-m-Y', strtotime($e->created_at)) }}</td>
										<td class="align-middle">
										  @php $cover = $e->collectionMedia->where('type', '1')->first(); @endphp
										  @if($cover)
											<a href="{{ asset(Storage::url($cover->link)) }}" data-lightbox="{{ $cover->link }}" data-title="{{ $cover->link }}"><img src="{{ asset(Storage::url($cover->link)) }}" style="max-height:30px; max-width:30px;"></a>
										  @endif
										</td>
										<td class="align-middle">
										  <button type="button" class="btn btn-success btn-sm" data-toggle="modal" onclick="showModalSlide('{{ $e->id }} ')">Lihat File</button>
										</td>
										<td>
										  <a href="{{ url('publisher/collection/update/' . $e->id) }}" class="btn btn-warning btn-sm"><i class="la la-edit"></i> Edit</a>
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
					</div>
					<div class="form-group"><hr></div>
					<div class="form-group">
					  <div class="text-right">
						<button type="reset" class="btn btn-danger">Reset</button>
						<button type="submit" class="btn btn-warning">Update</button>
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
<script>
  $(function() {
	$('#datatable_default tbody').on('click', '#remove_field_contributor', function () {
	  $('#datatable_default').DataTable().row($(this).parents('tr')).remove().draw();
	});

	$('a[data-toggle="tab"]').on('shown.bs.tab', function() {
	  $('#datatable_default').DataTable().columns.adjust();
	});

	$('.default_select2').select2({
	  placeholder: '-- Pilih --'
	});

	select2AutoSuggestTags('#collection_subject', 'load_subject');
  });

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

  var total_image = 1;
  var total_image_modal = 1;
  var page = 1;
  var pageModal = 1;
  var collId ="";

  function showModalSlide(collectionId) {
	console.log(collectionId)
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
</script>
