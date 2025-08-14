<div class="app-content content">
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-6 col-12 mb-2">
				<h3 class="content-header-title mb-1 d-inline-block">Review Pemantauan Buku</h3><br>
				<div class="row breadcrumbs-top d-inline-block">
					<div class="breadcrumb-wrapper col-12">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ url('publisher/dashboard') }}">Dashboard</a></li>
							<li class="breadcrumb-item"><a href="#">Buku</a></li>
							<li class="breadcrumb-item"><a href="{{ url('publisher/collection/monitoring/1') }}">Pemantauan</a></li>
							<li class="breadcrumb-item active">Review</li>
						</ol>
					</div>
				</div>
			</div>
			<div class="content-header-right col-md-6 col-12 mb-2 mt-1">
				<div class="float-md-right">
					<a href="{{ url('publisher/collection/monitoring/1') }}" class="btn btn-secondary">Kembali</a>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="configuration">
				<div class="row">
					<div class="col-12">
						<div class="card">
							<div class="card-header">
								<h4 class="card-title text-center">Form Review</h4>
							</div>
							<div class="card-content collapse show">
								<div class="card-body card-dashboard">
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
												<p>
													<div class="table-responsive">
														<table class="table table-striped table-bordered">
															<tbody>
																<tr>
																	<td class="align-middle w-20 font-weight-bold">Pelaksana</td>
																	<td class="align-middle">{{ $collection->publisher->name }}</td>
																</tr>
																<tr>
																	<td class="align-middle w-20 font-weight-bold">Judul</td>
																	<td class="align-middle">{{ $collection->title }}</td>
																</tr>
																<tr>
																	<td class="align-middle w-20 font-weight-bold">Tipe</td>
																	<td class="align-middle">{{ $collection->typeBook() }}</td>
																</tr>
																<tr>
																	<td class="align-middle w-20 font-weight-bold">Identifier</td>
																	<td class="align-middle">{{ $collection->code }}</td>
																</tr>
																<tr>
																	<td class="align-middle w-20 font-weight-bold">Preview</td>
																	<td class="align-middle">{{ $collection->preview }}</td>
																</tr>
																<tr>
																	<td class="align-middle w-20 font-weight-bold">Tahun Terbit</td>
																	<td class="align-middle">{{ $collection->publication_year }}</td>
																</tr>
																<tr>
																	<td class="align-middle w-20 font-weight-bold">Tempat Terbit</td>
																	<td class="align-middle">{{ $collection->city->name }}</td>
																</tr>
																<tr>
																	<td class="align-middle w-20 font-weight-bold">Total Halaman</td>
																	<td class="align-middle">
																		@if($collection->physicalDescription())
																			{{ $collection->physicalDescription()->total_page }} Hal
																		@endif
																	</td>
																</tr>
																<tr>
																	<td class="align-middle w-20 font-weight-bold">Dimensi</td>
																	<td class="align-middle">
																		@if($collection->physicalDescription() && isset($collection->physicalDescription()->dimension))
																			{{ $collection->physicalDescription()->dimension }} Cm
																		@endif
																	</td>
																</tr>
																<tr>
																	<td class="align-middle w-20 font-weight-bold">Ilustrasi</td>
																	<td class="align-middle">
																		@if($collection->physicalDescription())
																			{{ $collection->physicalDescription()->ilustration }}
																		@endif
																	</td>
																</tr>
																<tr>
																	<td class="align-middle w-20 font-weight-bold">Kategori</td>
																	<td class="align-middle">
																		@foreach($collection->collectionCategory as $cc)
																			<span class="badge bg-info">{{ $cc->category->name }}</span>
																		@endforeach
																	</td>
																</tr>
																<tr>
																	<td class="align-middle w-20 font-weight-bold">Subjek</td>
																	<td class="align-middle">
																		@foreach($collection->collectionSubject as $cs)
																			<span class="badge bg-info">{{ $cs->subject->name }}</span>
																		@endforeach
																	</td>
																</tr>
																<tr>
																	<td class="align-middle font-weight-bold">Keterangan</td>
																	<td class="align-middle">{{ $collection->description }}</td>
																</tr>
																<tr>
																	<td class="align-middle font-weight-bold">Status</td>
																	<td class="align-middle">{{ $collection->status() }}</td>
																</tr>
																<tr>
																	<td class="align-middle font-weight-bold">Tanggal Terima</td>
																	<td class="align-middle">
																		{{ date('d M Y', strtotime($collection->received_at) )}}
																	</td>
																</tr>
																<tr>
																	<td class="align-middle font-weight-bold">Hak Akses</td>
																	<td class="align-middle">
																		{{ $collection->access() }}
																	</td>
																</tr>
															</tbody>
														</table>
													</div>
												</p>
											</div>
											<div class="tab-pane" id="tab_contributor">
												<p>
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
																	</tr>
																</thead>
																<tbody>
																	@foreach($collection->collectionContributor as $cc)
																		<tr class="text-center">
																			<td class="align-middle">{{ $cc->contributor->name }}</td>
																			<td class="align-middle">{{ $cc->author->fullname }}</td>
																			<td class="align-middle">{{ $cc->author->title }}</td>
																			<td class="align-middle">{{ $cc->author->year_of_birth }}</td>
																			<td class="align-middle">{{ $cc->author->year_of_death }}</td>
																		</tr>
																	@endforeach
																</tbody>
															</table>
														</div>
													</div>
												</p>
											</div>
											<div class="tab-pane" id="tab_cover">
												<div class="form-group">
													@php $cover = $collection->collectionMedia->where('type', 'Cover')->first(); @endphp
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
												</div>
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
										 <div class="row">
											<div class="col-4 ml-2">
												<button class="btn btn-info" onclick="loadHistory()">Histori Koleksi</button>
											</div>
										</div>
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

	function loadHistory() {
		$.ajax({
			url: '{{ url("publisher/collection/history") }}',
			type: 'GET',
			dataType: 'JSON',
			data: {
				collection_id: '{{ $collection->id }}',
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success: function(response) {
			 $('#list-history').empty()
			 response.forEach(function(item, idx) {
				$('#list-history').append('<tr><td>'+(idx+1)+'</td><td>'+item.description+'</td><td>'+item.updated_at+'</td></tr>')
			 })
			 $('#modal_element').modal('show')
			},
			error: function() {
				false;
			}
		});
	}
	$(function() {
		$('a[data-toggle="tab"]').on('shown.bs.tab', function() {
			$('#datatable_default').DataTable().columns.adjust();
		});

		$('input:radio[name="status"]').click(function() {
			if($(this).is(':checked') && $(this).val() == 3) {
				$('#form_problem').fadeIn(200);
			} else {
				$('#form_problem').fadeOut(200);
			}
		});
	});

	var total_image = 1;
	var total_image_modal = 1;
	var page = 1;
	var pageModal = 1;
	var collId ="";

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
</script>
