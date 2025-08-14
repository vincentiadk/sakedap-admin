<div class="app-content content">
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-6 col-12 mb-2">
				<h3 class="content-header-title mb-1 d-inline-block">Review Audio</h3><br>
				<div class="row breadcrumbs-top d-inline-block">
					<div class="breadcrumb-wrapper col-12">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
							<li class="breadcrumb-item"><a href="#">Audio</a></li>
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
							<div class="card-content collapse show">
								<div class="card-body card-dashboard">
									<form method="POST" class="form">
										@csrf
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
										<h4 class="form-section">Meta Data</h4>
										<p>
											<div class="table-responsive">
												<table class="table table-bordered table-striped">
													<thead>
														<tr>
															<th colspan="2">
																<div class="row justify-content-center">
																	<div class="col-md-8">
																		@php $original = $collection->collectionMedia->where('type', 2)->first(); @endphp
																		@if($original && Storage::disk($original->location->location)->exists($original->link))
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
																				<audio controls controlsList="nodownload">
																					<source src="{{url('admin/get_file/'.$original->id)}}" type="{{ $original->mimes }}">
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
															<td class="align-middle">{{ isset($collection->publisher->city) ? $collection->publisher->city->name : '' }}</td>
														</tr>
														<tr>
															<td class="align-middle w-20 font-weight-bold">Durasi</td>
															<td class="align-middle">
																@if(isset($collection->physicalDescription()->duration))
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
																<input type="date" name="received_at" class="form-control" style="width:40%;" value="{{ date('Y-m-d') }}">
															</td>
														</tr>
														<tr>
															<td class="align-middle font-weight-bold">Hak Akses</td>
															<td class="align-middle">{{ $collection->access() }}</td>
														</tr>
													</tbody>
												</table>
											</div>
										</p>
										<h4 class="form-section">Kontributor</h4>
										<p>
											<div class="form-group">
												<div class="table-responsive">
													<table class="table table-bordered table-striped" id="datatable_default">
														<thead class="text-center">
															<tr>
																<th>Kontributor</th>
																<th>Nama</th>
																<th>Gelar</th>
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
										<h4 class="form-section">Cover</h4>
										<p>
											<div class="form-group">
												@php $cover = $collection->collectionMedia->where('type', 1)->first(); @endphp
												@if($cover && Storage::disk($cover->location->location)->exists($cover->link))
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
											</div>
										</p>
										<div class="form-group"><hr></div>
										<div class="form-group text-center">
											<label>Status :</label>
											<div class="row">
												<div class="col-md-3">
													<fieldset class="radio">
														<label>
															<input type="radio" name="status" value="1" {{ $collection->status == 1 ? 'checked' : '' }}> Review
														</label>
													</fieldset>
												</div>
												<div class="col-md-3">
													<fieldset class="radio">
														<label>
															<input type="radio" name="status" value="2" {{ $collection->status == 2 ? 'checked' : '' }}> Diterima
														</label>
													</fieldset>
												</div>
												<div class="col-md-3">
													<fieldset class="radio">
														<label>
															<input type="radio" name="status" value="3" {{ $collection->status == 3 ? 'checked' : '' }}> Bermasalah
														</label>
													</fieldset>
												</div>
                                                <div class="col-md-3">
													<fieldset class="radio">
														<label>
															<input type="radio" name="status" value="5" {{ $collection->status == 5 ? 'checked' : '' }}> Ditolak
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

<script>
	$(function() {
		$('input:radio[name="status"]').click(function() {
			if($(this).is(':checked') && $(this).val() == 3) {
				$('#form_problem').fadeIn(200);
			} else {
				$('#form_problem').fadeOut(200);
			}
		});
	});
</script>
