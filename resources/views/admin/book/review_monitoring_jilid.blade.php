<div class="app-content content">
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-6 col-12 mb-2">
				<h3 class="content-header-title mb-1 d-inline-block">Review Pemantauan Buku</h3><br>
				<div class="row breadcrumbs-top d-inline-block">
					<div class="breadcrumb-wrapper col-12">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
							<li class="breadcrumb-item"><a href="#">Buku</a></li>
							<li class="breadcrumb-item"><a href="{{ url('admin/collection/monitoring/1') }}">Pemantauan</a></li>
							<li class="breadcrumb-item active">Review</li>
						</ol>
					</div>
				</div>
			</div>
			<div class="content-header-right col-md-6 col-12 mb-2 mt-1">
				<div class="float-md-right">
                    @if($collection->collectionProblem->count() > 0)
                        <a href="javascript:void(0);" class="btn btn-info">REUPLOAD KOLEKSI</a>
                    @else
                        <a href="javascript:void(0);" class="btn btn-success">NEW KOLEKSI</a>
                    @endif
                </div>
			</div>
		</div>
		<div class="content-body">
            @if(session('success'))
                <div class="alert bg-success alert-icon-left alert-dismissible" role="alert">
                    <span class="alert-icon"><i class="la la-check"></i></span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>Success!</strong> {{ session('success') }}
                </div>
            @elseif(session('failed'))
                <div class="alert bg-danger alert-icon-left alert-dismissible" role="alert">
                    <span class="alert-icon"><i class="la la-times"></i></span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>Oooppss!</strong> {{ session('failed') }}
                </div>
            @endif
			<section id="configuration">
				<div class="row">
					<div class="col-12">
						<div class="card">
							<div class="card-header">
								<h4 class="card-title text-center">Form Review</h4>
							</div>
							<div class="card-content collapse show">
								<div class="card-body card-dashboard">
                                    <h4 class="form-section">Histori Permasalahan</h4>
                                    <p>
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">No</th>
                                                    <th>Masalah</th>
                                                    <th>Status</th>
                                                    <th>Tanggal</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if($collection->collectionProblem->count() > 0)
                                                    @foreach($collection->collectionProblem as $key => $cp)
                                                        <tr>
                                                            <td class="text-center">{{ $key + 1 }}</td>
                                                            <td>{{ $cp->problem->name ?? '' }}</td>
                                                            <td>{{ $cp->solved == 1 ? 'Selesai' : 'Belum Terselesaikan' }}</td>
                                                            <td nowrap>{{ $cp->created_at }}</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td class="text-center" colspan="4">Tidak ada data</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </p>
									<form action="{{ url('admin/collection/monitoring/review/' . $collection->id) }}" method="POST" class="form-horizontal">
                                        @csrf
										<ul class="nav nav-tabs nav-justified">
											<li class="nav-item">
												<a class="nav-link active" data-toggle="tab" aria-controls="tab_general" href="#tab_general" aria-expanded="true">Meta Data</a>
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
																	<td class="align-middle w-20 font-weight-bold">Penerbit</td>
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
																	<td class="align-middle w-20 font-weight-bold">ISBN</td>
																	<td class="align-middle">{{ $collection->code }}</td>
																</tr>
																<tr>
																	<td class="align-middle w-20 font-weight-bold">Preview</td>
																	<td class="align-middle">{{ $collection->preview }}</td>
																</tr>
                                                                <tr>
                                                                    <td class="align-middle w-20 font-weight-bold">Bulan Terbit</td>
                                                                    <td class="align-middle">
                                                                        @if($collection->publication_month)
                                                                            {{ App\Helper\GeneralHelper::getMonth($collection->publication_month) }}
                                                                        @endif
                                                                    </td>
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
																		@if(isset($collection->physicalDescription()->total_page))
																			{{ $collection->physicalDescription()->total_page }} Hal
																		@endif
																	</td>
																</tr>
																<tr>
																	<td class="align-middle w-20 font-weight-bold">Dimensi</td>
																	<td class="align-middle">
																		@if(isset($collection->physicalDescription()->dimension))
																			{{ $collection->physicalDescription()->dimension }} Cm
																		@endif
																	</td>
																</tr>
																<tr>
																	<td class="align-middle w-20 font-weight-bold">Ilustrasi</td>
																	<td class="align-middle">
																		@if(isset($collection->physicalDescription()->ilustration))
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
																		<th>Gelar</th>
																	</tr>
																</thead>
																<tbody>
																	@foreach($collection->collectionContributor as $cc)
																		<tr class="text-center">
																			<td class="align-middle">{{ $cc->contributor->name }}</td>
																			<td class="align-middle">{{ $cc->author->fullname }}</td>
																			<td class="align-middle">{{ $cc->author->title }}</td>
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
															<img src="{{ asset(Storage::url($cover->link)) }}" class="ezoom" style="max-width:242px; max-height:280px;">
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
																			<th>Metode</th>
																		</tr>
																	</thead>
																	<tbody id="edition_element">
																		@foreach($collection->edition()->get() as $key => $e)
																			<tr class="text-center">
																				<td class="align-middle">{{ $e->code }}</td>
																				<td class="align-middle">{{ date('d-m-Y', strtotime($e->created_at)) }}</td>
																				<td class="align-middle">
																					@php $cover = $e->collectionMedia->where('type', 1)->first(); @endphp
																					@if($cover)
																						<a href="{{ asset(Storage::url($cover->link)) }}" data-lightbox="{{ $cover->link }}" data-title="{{ $cover->link }}"><img src="{{ asset(Storage::url($cover->link)) }}" class="ezoom" style="max-height:30px; max-width:30px;"></a>
																					@endif
																				</td>
																				@php
																						$preview = $e->collectionMedia->where('type', '3')->first();
																					@endphp
																				<td class="align-middle">
																					<button type="button" class="btn btn-success btn-sm" data-toggle="modal" onclick="showModalSlide({{ $e->id }} )">Lihat File</button>
																				</td>
																				<td>
																					@if($preview)
																					{{ $preview->method() }}
																					@endif
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
										<div class="form-group text-center">
                                            <label>Status :</label>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <fieldset class="radio">
                                                        <label>
                                                            <input type="radio" name="status" value="1"
                                                                {{ $collection->status == 1 ? 'checked' : '' }}> Review
                                                        </label>
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-3">
                                                    <fieldset class="radio">
                                                        <label>
                                                            <input type="radio" name="status" value="2"
                                                                {{ $collection->status == 2 ? 'checked' : '' }}>
                                                            Diterima
                                                        </label>
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-3">
                                                    <fieldset class="radio">
                                                        <label>
                                                            <input type="radio" name="status" value="3"
                                                                {{ $collection->status == 3 ? 'checked' : '' }}>
                                                            Bermasalah
                                                        </label>
                                                    </fieldset>
                                                </div>
                                                <div class="col-md-3">
                                                    <fieldset class="radio">
                                                        <label>
                                                            <input type="radio" name="status" value="5"
                                                                {{ $collection->status == 5 ? 'checked' : '' }}> Ditolak
                                                        </label>
                                                    </fieldset>
                                                </div>
                                            </div>
                                        </div>
										<div id="form_problem" style="display:none;">
											<div class="form-group"><hr></div>
											<h4 class="card-title text-center mb-3">Form Bermasalah</h4>
											<div class="form-group">
												<div class="row">
													@foreach($problem as $p)
														<div class="col-md-6">
															<fieldset class="checkboxsas">
																<label>
																	<input type="checkbox" name="collection_problem[]" value="{{ $p->id }}"> {{ $p->name }}
																</label>
															</fieldset>
														</div>
													@endforeach
												</div>
											</div>
											<div class="form-group">
												<textarea name="problem" class="form-control" placeholder="Masalah Lainnya" rows="7" style="resize:none;"></textarea>
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
<div class="modal" id="modal-slide-content" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-body">
				<center>
			<a class="btn btn-primary btn-sm prev-modal" href="#" onclick="prevModal($('#collection_edition_id').val())"><<</a><input type="number" name="key_carousel"  onchange="loadPdfImageModal($('#collection_edition_id').val())" min="0" value="1" id="key_carousel_modal"> / <sub id="total_data_image_pdf_modal"></sub> <a href="#" class="btn btn-success btn-sm next-modal" onclick="nextModal($('#collection_edition_id').val())">>></a>
			<p></p>
			<input type="hidden" name="collection-edition-id" id="collection_edition_id">
			<div class="thumbs_gall_slider_con content_thumbs_gall clearfix">

				<div class="thumbs_gall_slider_larg owl-carousel" data-transition="fadeUp">
						<div class="item">
							<img class="d-block w-100 ezoom" src="" id="data_image_pdf_modal" style="max-height:903px; width:100%;">
						</div>
				</div>
			</div>
			<div class="form-group">
								<a class="btn btn-primary btn-sm prev-modal" href="#" onclick="prevModal($('#collection_edition_id').val())"><<</a> <a href="#" class="btn btn-success btn-sm next-modal" onclick="nextModal($('#collection_edition_id').val())">>></a>
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

	var total_image_modal = 1;
	var page              = 1;
	var pageModal         = 1;
	var collId            = "";

	function showModalSlide(collectionId) {
		$('#modal-slide-content').modal('show');
		$('#collection_edition_id').val(collectionId);

		total_image_modal = 1;
		pageModal         = 1;
	    collId            = collectionId;

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
				total_image_modal = response.total_data;

                $('.next-modal').attr('onclick', 'nextModal(' + collectionId + ')');
                $('.prev-modal').attr('onclick', 'prevModal(' + collectionId + ')');
                $('#data_image_pdf_modal').attr('src', response.image);
				$('#total_data_image_pdf_modal').html(response.total_data);
				$('#key_carousel_modal').val(pageModal);
			},
			error: function() {
				false;
			}
		});
	}
	function nextModal(collection_Id) {
		pageModal = parseInt($('#key_carousel_modal').val()) + 1;
		return loadPdfImageModal(pageModal, true, collection_Id);
	}
	function prevModal(collection_Id) {
		pageModal = parseInt($('#key_carousel_modal').val()) - 1;
		return loadPdfImageModal(pageModal, true, collection_Id);
	}
</script>
