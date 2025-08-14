<div class="app-content content">
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-6 col-12 mb-2">
				<h3 class="content-header-title mb-1 d-inline-block">Review Pemantauan Audio</h3><br>
				<div class="row breadcrumbs-top d-inline-block">
					<div class="breadcrumb-wrapper col-12">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ url('publisher/dashboard') }}">Dashboard</a></li>
							<li class="breadcrumb-item"><a href="#">Audio</a></li>
							<li class="breadcrumb-item"><a href="{{ url('publisher/collection/monitoring/5') }}">Pemantauan</a></li>
							<li class="breadcrumb-item active">Review</li>
						</ol>
					</div>
				</div>
			</div>
			<div class="content-header-right col-md-6 col-12 mb-2 mt-1">
				<div class="float-md-right">
					<a href="{{ url('publisher/collection/monitoring/5') }}" class="btn btn-secondary">Kembali</a>
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
												<a class="nav-link active" data-toggle="tab" aria-controls="tab_general" href="#tab_general" aria-expanded="true">Meta Data</a>
											</li>
											<li class="nav-item">
												<a class="nav-link" data-toggle="tab" aria-controls="tab_contributor" href="#tab_contributor" aria-expanded="false">Kontributor</a>
											</li>
											<li class="nav-item">
												<a class="nav-link" data-toggle="tab" aria-controls="tab_cover" href="#tab_cover" aria-expanded="false">Cover</a>
											</li>
										</ul>
										<div class="tab-content px-1 pt-1">
											<div role="tabpanel" class="tab-pane active" id="tab_general" aria-expanded="true">
												<p>
													<div class="table-responsive">
														<table class="table table-bordered table-striped">
															<thead>
																<tr>
																	<th colspan="2">
																		<div class="row justify-content-center">
																			<div class="col-md-8">
																				@php $original = $collection->collectionMedia->where('type', 4)->first(); @endphp
																				@if($original && Storage::exists($original->link))
																					<div class="row justify-content-center">
																						<div class="col-md-6">
																							<div class="alert alert-warning alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
																								<span class="alert-icon"><i class="la la-info-circle"></i></span>
																								<ul>
																									<li>Ukuran: <b>{{ App\Helper\GeneralHelper::formatSize($original->size) }}</b></li>
																									<li>Ekstensi: <b>{{ $original->extension }}</b></li>
																									<li>Mime: <b>{{ $original->mimes }}</b></li>
																									<li>Hash: <b>{{ $original->hash }}</b></li>
																									<li>Durasi: <b>@isset($collection->physicalDescription()->duration) {{ $collection->physicalDescription()->duration }} @endif</b></li>
																									<li>Metode: <b>{{ $original->method() }}</b></li>
																								</ul>
																							</div>
																						</div>
																					</div>
																					<center>
																						<audio controls>
																						    <source src="{{url('publisher/get_file/'.$original->id)}}" type="{{ $original->mimes }}">
																							<small class="font-italic text-danger">Browser tidak didukung</small>
																						</audio>
																					</center>
																				@else
																					<div class="alert alert-danger text-center">Tidak ada file!</div>
																				@endif
																			</div>
																		</div>
																	</th>
																</tr>
															</thead>
															<tbody>
																<tr>
																	<td class="align-middle w-20 font-weight-bold">Label</td>
																	<td class="align-middle">{{ $collection->publisher->name }}</td>
																</tr>
																<tr>
																	<td class="align-middle w-20 font-weight-bold">Judul</td>
																	<td class="align-middle">{{ $collection->title }}</td>
																</tr>
																<tr>
																	<td class="align-middle w-20 font-weight-bold">Album</td>
																	<td class="align-middle">{{ $collection->album }}</td>
																</tr>
																<tr>
																	<td class="align-middle w-20 font-weight-bold">ISRC</td>
																	<td class="align-middle">{{ $collection->code }}</td>
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
																	<td class="align-middle">{{ isset($collection->city) ? $collection->city->name : '' }}</td>
																</tr>
																<tr>
																	<td class="align-middle w-20 font-weight-bold">Durasi</td>
																	<td class="align-middle">
																		@if($collection->physicalDescription())
																			{{ $collection->physicalDescription()->duration }}
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
																		@if($collection->received_at)
																		<p>{{ date('Y-m-d', strtotime($collection->received_at)) }}</p>
																		@endif
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
													@if($collection->status == 2)
													<form action="{{ url('publisher/collection/update/' . $collection->id . '/preview-access') }}" method="POST" enctype="multipart/form-data">
														@csrf
														<div class="alert alert-secondary w-100">
															<div class="row">
																<div class="col-md-6">
																	<label>Hak Akses</label>
																	<div class="alert alert-success mb-2 w-100 align-middle" role="alert">
																		<fieldset class="radio">
																			<label>
																				<input type="radio"  name="access" value="1" {{ $collection->access == 1 ? "checked" : "" }}> Akses full file berwatermak secara online
																			</label>
																		</fieldset>
																	</div>
																	<div class="alert alert-success mb-2 w-100 align-middle" role="alert">
																		<fieldset class="radio">
																			<label>
																				<input type="radio"  name="access" value="2" {{ $collection->access == 2 ? "checked" : "" }}> Akses hanya preview file secara online, namun tetap dapat di dayagunakan di lingkungan perpustakaan nasional RI dengan jaringan internet LAN
																			</label>
																		</fieldset>
																	</div>
																	<div class="alert alert-success mb-2 w-100 align-middle" role="alert">
																		<fieldset class="radio">
																			<label>
																				<input type="radio"  name="access" value="3" {{ $collection->access == 3 ? "checked" : "" }}> Akses hanya preview file secara online, dan tidak dilayankan di Perpusnas RI selama 5 tahun sejak di serahkan. Setelah periode habis akan dapat dilayankan oleh perpusnas.
																			</label>
																		</fieldset>
																	</div>
																	<div class="alert alert-success mb-2 w-100 align-middle" role="alert">
																		<fieldset class="radio">
																			<label>
																				<input type="radio"  name="access" value="4" {{ $collection->access == 4 ? "checked" : "" }}> Akses hanya file preview secara online selamanya dan tidak didayagunakan di mana pun.
																			</label>
																		</fieldset>
																	</div>
																</div>
																<div class="col-md-6">
																	<div class="form-group">
																		<label>Preview:</label>
																		@php $original = $collection->collectionMedia->where('type', 6)->first(); @endphp
																		@if(isset($original))
																			@if($original->status == 0)
																				<p>File Preview sedang di proses</p>
																			@else
																				<div id="slider_music"></div>
																				<input type="hidden" id="preview" name="preview">
																			@endif
																		@else
																			<p>File Preview sedang di proses</p>
																		@endif
																	</div>
																	<div class="form-group">
																		<button class="btn btn-warning" onclick="submit()">Update Koleksi</button>
																	</div>
																</div>
															</div>
														</div>
													</form>
													@endif
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
												<p>
													<div class="form-group">
														@php $cover = $collection->collectionMedia->where('type', '1')->first(); @endphp
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
												</p>
											</div>
										</div>
										<div class="form-group"><hr></div>
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
<script>

	$(document).ready(function(){

		var sliderMusic;
		var sliderMusic = document.getElementById('slider_music');

		noUiSlider.create(sliderMusic, {
				start: [30, 60],
				connect: true,
				step: 1,
				tooltips: [true, true],
				format: {
					to: function ( value ) {
						return value + '';
					},
					from: function ( value ) {
						return value.replace('', '');
					}
				},
				range: {
						'min': 0,
						'max': 360
				}
		});

		function submit()
		{
			$('#preview').val(sliderVideo.noUiSlider.get());
			$("form").submit();
		}

	})

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
	});
</script>
