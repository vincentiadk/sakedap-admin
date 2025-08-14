<div class="app-content content">
		<div class="content-wrapper">
				<div class="content-header row">
						<div class="content-header-left col-md-6 col-12 mb-2">
								<h3 class="content-header-title mb-1 d-inline-block">Tambah Data Koleksi</h3><br>
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
									<h4 class="card-title text-center">Form Penyerahan Koleksi</h4>
									<a class="heading-elements-toggle"><i class="la la-ellipsis-h font-medium-3"></i></a>
									<div class="heading-elements">
										<ul class="list-inline mb-0">
											<li><a data-action="collapse"><i class="ft-minus"></i></a></li>
											<li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
											<li><a data-action="expand"><i class="ft-maximize"></i></a></li>
											<li><a data-action="close"><i class="ft-x"></i></a></li>
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
										<form id="form_data" class="steps-validation wizard-circle">
											<!-- Step 1 -->
											<h6>Tipe Publikasi</h6>
											<fieldset>
												<div class="row">
													<div class="col-xl-6 col-lg-6">
														<div class="border border-light p-2">
														<h3>Monograf</h3>
															<fieldset class="radio">
																<label>
																	<input type="radio"  name="type" value="1"> Buku / Buku Berjilid / Buku Audio
																</label>
															</fieldset>
															<fieldset class="radio">
																<label>
																	<input type="radio"  name="type" value="3"> Peta
																</label>
															</fieldset>
															<fieldset class="radio">
																<label>
																	<input type="radio"  name="type" value="2"> Partitur Musik
																</label>
															</fieldset>
															<fieldset class="radio">
																<label>
																	<input type="radio"  name="type" value="4"> Serial
																</label>
															</fieldset>
														</div>
													</div>
													<div class="col-xl-6 col-lg-6">
														<div class="border border-light p-2">
														<h3>Audio Visual</h3>
															<fieldset class="radio">
																<label>
																	<input type="radio"   name="type" value="5"> Audio
																</label>
															</fieldset>
															<fieldset class="radio">
																<label>
																	<input type="radio" name="type" value="6"> Video
																</label>
															</fieldset>
														</div>
													</div>
												</div>
												<!-- <div class="input-file">
												</div> -->
											</fieldset>
											<!-- Step 2 -->
											<h6>Detail Publikasi</h6>
											<fieldset>
												<div class="row">
													<input type="hidden" name="type_of_collection" id="type_of_collection" />
													<div class="col-md-12" id="type_book" style="display:none;">
														<div class="row">
															<div class="col-md-6 m-auto text-center">
																<button class="btn btn-primary btn-min-width mr-1 mb-1 select_type_book" type="button" value="isbn">E-ISBN</button>
															</div>
															<div class="col-md-6 m-auto text-center">
																<button class="btn btn-primary btn-min-width mr-1 mb-1 select_type_book" type="button" value="non-isbn">NON E-ISBN</button>
															</div>
														</div>
													</div>
													<div class="col-md-12" id="type_partitur" style="display:none;">
														<div class="row">
															<div class="col-md-6 m-auto text-center">
																<button class="btn btn-primary btn-min-width mr-1 mb-1 select_type_partitur" type="button" value="ismn">ISMN</button>
															</div>
															<div class="col-md-6 m-auto text-center">
																<button class="btn btn-primary btn-min-width mr-1 mb-1 select_type_partitur" type="button" value="non-ismn">NON ISMN</button>
															</div>
														</div>
													</div>
													<div class="col-md-12" id="type_map" style="display:none;">
														<div class="row">
															<div class="col-md-6 m-auto text-center">
																<button class="btn btn-primary btn-min-width mr-1 mb-1 select_type_map" type="button" value="isbn">ISBN</button>
															</div>
															<div class="col-md-6 m-auto text-center">
																<button class="btn btn-primary btn-min-width mr-1 mb-1 select_type_map" type="button" value="non-isbn">NON ISBN</button>
															</div>
														</div>
													</div>
													<div class="col-md-12" id="type_audio" style="display:none;">
														 <div class="row">
															<div class="col-md-6 m-auto text-center">
																<button class="btn btn-primary btn-min-width mr-1 mb-1 select_type_audio" type="button" value="isrc">ISRC</button>
															</div>
															<div class="col-md-6 m-auto text-center">
																<button class="btn btn-primary btn-min-width mr-1 mb-1 select_type_audio" type="button" value="non-isrc">NON ISRC</button>
															</div>
														</div>
													</div>

													<div class="col-md-12" id="type_video" style="display:none;">
														 <div class="row">
															<div class="col-md-6 m-auto text-center">
																<button class="btn btn-primary btn-min-width mr-1 mb-1 select_type_video" type="button" value="isan">ISAN</button>
															</div>
															<div class="col-md-6 m-auto text-center">
																<button class="btn btn-primary btn-min-width mr-1 mb-1 select_type_video" type="button" value="non-isan">NON ISAN</button>
															</div>
														</div>
													</div>

													<div class="col-md-12" id="type_serial" style="display:none;">
														<div class="row">
															<div class="col-md-12 m-auto text-center">
																<button class="btn btn-primary btn-min-width mr-1 mb-1 select_type_serial" type="button" value="issn">Buat Metadata Baru</button>
															</div>
															<!-- <div class="col-md-12 m-auto text-center">
																<button class="btn btn-primary btn-min-width mr-1 mb-1 select_type_serial" type="button" value="non-issn">Buat </button>
															</div> -->
														</div>

														<h4 class="card-title">Daftar Koleksi Serial</h4>
														<table class="table table-bordered" id="datatable_serverside_serial">
															<thead class="text-center">
																<tr>
																	<th>#</th>
																	<th width="20%">Deposit</th>
																	<th width="20%">ISSN</th>
																	<th>Judul</th>
																	<th>Action</th>
																</tr>
															</thead>
														</table>

													</div>

													<div class="col-md-12" id="select_isbn" style="display:none;">
													<div class="border border-light p-5 m-5">
														<h4 class="card-title">Filter Data</h4>
															@if($data['publisher_groups'])
															<div class="form-group row">
																	<label class="col-md-2">Publisher</label>
																	<div class="col-md-10">
																		<select id="publisher_id" class="form-control" onchange="loadDataTableISBN();">
																			@foreach($data['publisher_groups'] as $key => $item)
																				<option value="{{ $item->publisher->id }}" {{ $key == 0 ? 'selected' : '' }}>{{ $item->publisher->name }}</option>
																			@endforeach
																		</select>
																	</div>
															</div>
															@endif
													</div>
														<h4 class="card-title">Daftar Tagihan E-ISBN</h4>
														<table class="table table-bordered" id="datatable_serverside_isbn">
															<thead class="text-center">
																<tr>
																	<th>#</th>
																	<th width="20%">ISBN</th>
																	<th width="20%">Publisher</th>
																	<th>Judul</th>
																	<th>Action</th>
																</tr>
															</thead>
														</table>
													</div>
												</div>
											</fieldset>
											<h6>Detail Publikasi</h6>
											<fieldset>
												<div class="col-md-12" id="from_detail" style="display:none;">

													<ul class="nav nav-tabs nav-justified">
															<li class="nav-item">
																	<a class="nav-link active" data-toggle="tab" aria-controls="tab_general" href="#tab_general" aria-expanded="true">General</a>
															</li>
															<li class="nav-item">
																	<a class="nav-link" data-toggle="tab" aria-controls="tab_publisher" href="#tab_publisher" aria-expanded="false">Publisher</a>
															</li>
															<li class="nav-item">
																	<a class="nav-link" data-toggle="tab" aria-controls="tab_cover" href="#tab_cover" aria-expanded="false">Cover</a>
															</li>
															<li class="nav-item">
																	<a class="nav-link" data-toggle="tab" aria-controls="tab_konten" href="#tab_konten" aria-expanded="false">Konten</a>
															</li>
													</ul>
													<div class="tab-content px-1 pt-1">
														<div role="tabpanel" class="tab-pane active" id="tab_general" aria-expanded="true">
															<div id="form_detail_book" style="display:none;">
																<input type="hidden" name="isbn_book" id="isbn_book" class="form-control required" placeholder="Judul Buku">
																<div class="form-group" id="form_title_book_ori">
																		<label>Judul :</label>
																		<input type="text" name="title_ori_book" id="title_ori_book" class="form-control required" placeholder="Judul Buku" readonly="">
																</div>
																<div class="form-group" id="form_title_book">
																		<label id="label_title_book">Perubahan Judul :</label>
																		<input type="text" name="title_book" id="title_book" class="form-control required" placeholder="Judul Buku">
																</div>
																<div id="form-contributor_book">

																</div>
																<div class="row">
																	<div class="col-md-3">
																			<button type="button" onclick="addContributor('#form-contributor_book')" class="btn btn-success col-12">Tambah Kontributor</button>
																	</div>
																</div>
																<br/>
																<div class="form-group">
																	<label>Bulan Terbit :</label>
																	<select name="publication_month_book" id="publication_month_book" class="form-control">
																			<option value="">-- Pilih --</option>
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
																</div>
																<div class="form-group">
																		<label>Tahun Terbit :</label>
																		<input type="number" name="publication_year_book" id="publication_year_book" class="form-control" placeholder="Masukan tahun terbit">
																</div>
																<div class="row form-group">
																	 <div class="col-md-6">
																			<label>Seri :</label>
																			<input type="text" name="series_book" id="series_book" class="form-control required" placeholder="Masukan Seri Buku">
																	</div>
																	<div class="col-md-6">
																			<label>Edisi :</label>
																			<input type="text" name="edition_book" id="edition_book" class="form-control required" placeholder="Masukan Edisi Buku">
																	</div>
																</div>
																<div class="row form-group">
																	<div class="col-md-6">
																			<label>Jumlah Halaman :</label>
																			<input type="text" name="page_book" id="page_book" class="form-control required" placeholder="Masukan Jumlah Halaman Buku">
																	</div>
																	<div class="col-md-6">
																			<label>Ketebalan :</label>
																			<input type="text" name="thickness_book" id="thickness_book" class="form-control required" placeholder="Masukan Ketebalan Buku">
																	</div>
																</div>
																<div class="row form-group">
																	<div class="col-md-6">
																			<label>Preview :</label>
																			<input type="text" name="preview_book" id="preview_book" class="form-control required" placeholder="Ex: 1 - 2">
																	</div>
																</div>
																<div class="form-group">
																	<label>Abstrak atau Deskripsi (Minimal 200 Karakter) :</label>
																	<textarea name="description" id="description_book" class="form-control" placeholder="Abstrak atau Deskripsi"></textarea>
																</div>
																<br/>
																<h4 id="subject_book_parent">Subjek</h4>
																<div class="form-group">
																		<input type="hidden" name="subject[]" id="subject_1" class="form-control" placeholder="Subjek Buku 1" readonly="">
																		<input type="hidden" name="subject[]" id="subject_2" class="form-control" placeholder="Subjek Buku 2" readonly="">
																		<input type="hidden" name="subject[]" id="subject_3" class="form-control" placeholder="Subjek Buku 3" readonly="">
																		<input type="hidden" name="subject[]" id="subject_4" class="form-control" placeholder="Subjek Buku 4" readonly="">
																		<input type="hidden" name="subject[]" id="subject_5" class="form-control" placeholder="Subjek Buku 5" readonly="">
																		<ul class="list-group" id="list_subject">

																		</ul>
																	</div>
																<br/>
																<h4>Kategori</h4>
																<div class="form-group">
																	<div class="row ml-0 mr-0" id="category_book">
																	</div>
																</div>
																<br />
															</div>
															<div id="form_detail_partitur"  style="display:none;">
																	<div class="form-group">
																			<label>Judul :</label>
																			<textarea name="title_partitur" id="title_partitur" class="form-control" placeholder="Masukan judul"></textarea>
																	</div>
																	<div id="form-contributor_partitur">

																	</div>
																	<div class="row">
																		<div class="col-md-3">
																				<button type="button" onclick="addContributor('#form-contributor_partitur')" class="btn btn-success col-12">Tambah Kontributor</button>
																		</div>
																	</div>
																	<br/>
																	<div class="form-group" id="form_ismn">
																			<label>ISMN :</label>
																			<input type="text" class="form-control" name="code_partitur" id="code_partitur" placeholder="Masukan kode ISMN">
																	</div>
																	<div class="form-group">
																		<label>Bulan Terbit :</label>
																		<select name="publication_month_partitur" id="publication_month_partitur" class="form-control">
																				<option value="">-- Pilih --</option>
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
																	</div>
																	<div class="row">
																			<div class="col-md-6">
																					<div class="form-group">
																							<label>Tahun Terbit :</label>
																							<input type="number" name="publication_year_partitur" id="publication_year_partitur" class="form-control" placeholder="Masukan tahun terbit">
																					</div>
																			</div>
																			<div class="col-md-6">
																					<div class="form-group">
																						<label>Preview :</label>
																						<input type="text" name="preview_partitur" id="preview_partitur" class="form-control" placeholder="Ex: 1-3">
																				</div>
																			</div>
																	</div>
																	<div class="form-group">
																			<label>Keterangan (Minimal 200 Karakter) :</label>
																			<textarea name="description_partitur" id="description_partitur" class="form-control" style="resize:none;" placeholder="Masukan informasi lain"></textarea>
																	</div>
																	<br />
																	<h4>Kategori</h4>
																	<div class="form-group">
																		<div class="row" id="category_partitur">
																		</div>
																	</div>
																	<br />
															</div>
															<div id="form_detail_map" style="display: none;">
																<div class="form-group">
																		<label>Judul :</label>
																		<textarea name="title_map" id="title_map" class="form-control" placeholder="Masukan judul"></textarea>
																</div>
																<div id="form-contributor_map">

																</div>
																<div class="row">
																	<div class="col-md-3">
																			<button type="button" onclick="addContributor('#form-contributor_map')" class="btn btn-success col-12">Tambah Kontributor</button>
																	</div>
																</div>
																<br/>
																<div class="form-group" id="form_map">
																		<label>ISBN :</label>
																		<input type="text" class="form-control" name="code_map" id="code_map" placeholder="Masukan kode ISBN">
																</div>
																<div class="form-group">
																		<label>Bulan Terbit :</label>
																		<select name="publication_month_map" id="publication_month_map" class="form-control">
																				<option value="">-- Pilih --</option>
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
																</div>
																<div class="row">
																		<div class="col-md-6">
																				<div class="form-group">
																						<label>Tahun Terbit :</label>
																						<input type="number" name="publication_year_map" id="publication_year_map" class="form-control" placeholder="Masukan tahun terbit">
																				</div>
																		</div>
																		<div class="col-md-6">
																			<div class="form-group">
																					<label>Preview :</label>
																					<input type="text" name="preview_map" id="preview_map" class="form-control" placeholder="Ex: 1-3">
																			</div>
																		</div>
																</div>
																<div class="form-group">
																		<label>Skala :</label>
																		<input type="number" name="scala_map" id="scala_map" class="form-control" placeholder="Skala">
																</div>
																<div class="form-group">
																		<label>Keterangan (Minimal 200 Karakter): </label>
																		<textarea name="description_map" id="description_map" class="form-control" style="resize:none;" placeholder="Masukan informasi lain"></textarea>
																</div>
																<br />
																<h4>Kategori</h4>
																<div id="category_map">
																</div>
																<br />
															</div>
															<div id="form_detail_music" style="display: none;">
																<div class="form-group">
																		<label>Judul :</label>
																		<textarea name="title_music" id="title_music" class="form-control" placeholder="Masukan judul"></textarea>
																</div>
																<div id="form-contributor_music">

																</div>
																<div class="row">
																	<div class="col-md-3">
																			<button type="button" onclick="addContributor('#form-contributor_music')" class="btn btn-success col-12">Tambah Kontributor</button>
																	</div>
																</div>
																<br/>
																<div class="form-group">
																		<label>Album :</label>
																		<input type="text" class="form-control" name="album_music" id="album_music" placeholder="Masukan album">
																</div>
																<div class="form-group" id="form_music">
																		<label>ISRC :</label>
																		<input type="text" class="form-control" name="code_music" id="code_music" placeholder="Masukan kode ISRC">
																</div>
																<!-- <div class="row">
																		<div class="col-md-6">
																				<div class="form-group">
																						<label>Preview Start :</label>
																						<input type="text" name="preview_start_music" id="preview_start_music" class="form-control" placeholder="Ex: 00:30">
																				</div>
																		</div>
																		<div class="col-md-6">
																				<div class="form-group">
																						<label>Preview End :</label>
																						<input type="text" name="preview_end_music" id="preview_end_music" class="form-control" placeholder="Ex: 01:02">
																				</div>
																		</div>
																</div> -->
																<div class="form-group">
																	<label>Preview:</label>
																	<div id="slider_music"></div>
																</div>
																<div class="form-group">
																		<label>Durasi :</label>
																		<input type="number" name="duration_music" id="duration_music" class="form-control" placeholder="Durasi dalam detik">
																</div>
																<div class="form-group">
																		<label>Bulan Terbit :</label>
																		<select name="publication_month_music" id="publication_month_music" class="form-control">
																				<option value="">-- Pilih --</option>
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
																</div>
																<div class="form-group">
																		<label>Tahun Terbit :</label>
																		<input type="number" name="publication_year_music" id="publication_year_music" class="form-control" placeholder="Masukan tahun terbit">
																</div>
																<div class="form-group">
																		<label>Keterangan (Minimal 200 Karakter):</label>
																		<textarea name="description_music" id="description_music" class="form-control" style="resize:none;" placeholder="Masukan informasi lain"></textarea>
																</div>
																<br />
																<h4>Kategori</h4>
																<div id="category_music">
																</div>
																<br />
															</div>
															<div id="form_detail_video" style="display: none;">
																<div class="form-group">
																		<label>Judul :</label>
																		<textarea name="title_video" id="title_video" class="form-control" placeholder="Masukan judul"></textarea>
																</div>
																<div id="form-contributor_video">

																</div>
																<div class="row">
																	<div class="col-md-3">
																			<button type="button" onclick="addContributor('#form-contributor_video')" class="btn btn-success col-12">Tambah Kontributor</button>
																	</div>
																</div>
																<br/>
																<div class="form-group" id="form_video">
																		<label>ISAN :</label>
																		<input type="text" class="form-control" name="code_video" id="code_video" placeholder="Masukan kode ISAN">
																</div>
																<!-- <div class="row">
																		<div class="col-md-6">
																				<div class="form-group">
																						<label>Preview Start :</label>
																						<input type="text" name="preview_start_video" id="preview_start_video" class="form-control" placeholder="Ex: 00:30">
																				</div>
																		</div>
																		<div class="col-md-6">
																				<div class="form-group">
																						<label>Preview End :</label>
																						<input type="text" name="preview_end_video" id="preview_end_video" class="form-control" placeholder="Ex: 01:02">
																				</div>
																		</div>
																</div> -->
																<div class="form-group">
																	<label>Preview:</label>
																	<div id="slider_video"></div>
																</div>
																<div class="form-group">
																		<label>Durasi :</label>
																		<input type="number" name="duration_video" id="duration_video" class="form-control" placeholder="Durasi dalam detik">
																</div>
																<div class="form-group">
																		<label>Bulan Terbit :</label>
																		<select name="publication_month_video" id="publication_month_video" class="form-control">
																				<option value="">-- Pilih --</option>
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
																</div>
																<div class="form-group">
																		<label>Tahun Terbit :</label>
																		<input type="number" name="publication_year_video" id="publication_year_video" class="form-control" placeholder="Masukan tahun terbit">
																</div>
																<div class="form-group">
																		<label>Keterangan (Minimal 200 Karakter):</label>
																		<textarea name="description_video" id="description_video" class="form-control" style="resize:none;" placeholder="Masukan informasi lain"></textarea>
																</div>
																<br />
																<h4>Kategori</h4>
																<div id="category_music">
																</div>
																<br />
															</div>
															<div id="form_detail_serial" style="display: none;">
																<input type="hidden" class="form-control" name="id_serial" id="id_serial">
																<div class="form-group">
																		<label>Judul :</label>
																		<textarea name="title_serial" id="title_serial" class="form-control" placeholder="Masukan judul"></textarea>
																</div>
																<div class="form-group" id="form_serial">
																		<label>ISSN :</label>
																		<input type="text" class="form-control" name="code_serial" id="code_serial" placeholder="Masukan kode ISSN">
																</div>
																<div id="form-contributor_serial">

																</div>
																<div class="row">
																	<div class="col-md-3">
																			<button type="button" onclick="addContributor('#form-contributor_serial')" class="btn btn-success col-12">Tambah Kontributor</button>
																	</div>
																</div>
																<br/>
																<div class="form-group">
																	<label>Bulan Terbit Pertama Kali:</label>
																	<select name="publication_month_serial" id="publication_month_serial" class="form-control">
																			<option value="">-- Pilih --</option>
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
																</div>
																<div class="row">
																		<div class="col-md-6">
																				<div class="form-group">
																						<label>Preview :</label>
																						<input type="text" name="preview_serial" id="preview_serial" class="form-control" placeholder="Ex: 1-3">
																				</div>
																		</div>
																		<div class="col-md-6">
																				<div class="form-group">
																						<label>Tahun Terbit Pertama Kali :</label>
																						<input type="text" name="publication_year_serial" id="publication_year_serial" class="form-control" placeholder="Masukan tahun terbit">
																				</div>
																		</div>
																		<div class="col-md-6">
																				<div class="form-group">
																						<label>DDC :</label>
																						<input type="text" name="ddc_serial" id="ddc_serial" class="form-control" placeholder="Masukan DDC">
																				</div>
																		</div>
																		<div class="col-md-6">
																				<div class="form-group">
																						<label>Serial :</label>
																						<select name="serial" id="serial" class="form-control">
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
																</div>
																<div class="form-group">
																		<label>Keterangan : (Minimal 200 Karakter)</label>
																		<textarea name="description_serial" id="description_serial" class="form-control" style="resize:none;" placeholder="Masukan informasi lain"></textarea>
																</div>
																<h4>Kategori</h4>
																<div id="category_music">
																</div>
															</div>
														</div>
														<div class="tab-pane" id="tab_publisher">
															<div class="form-group">
																	<label>Nama Pelaksana</label>
																	<input type="text" name="publisher_name" id="publisher_name" class="form-control required" placeholder="Nama Pelaksana" value="{{ $data['publisher']->name }}" readonly="">
															</div>
															<div class="form-group">
																	<label>Alamat Pelaksana</label>
																	<input type="text" name="publisher_address" id="publisher_address" class="form-control required" placeholder="Alamat Pelaksana" value="{{ $data['publisher']->address }}">
															</div>
															<div class="form-group">
																	<label>Provinsi</label>
																	<select name="publisher_province" id="publisher_province" class="form-control required" style="width:100%;"></select>
															</div>
															<div class="form-group">
																	<label>Kota/Kab</label>
																	<select name="publisher_city" id="publisher_city" class="form-control required" style="width:100%;"></select>
															</div>
															<div class="form-group">
																	<label>Kecamatan</label>

																	<select name="publisher_district" id="publisher_district" class="form-control required" style="width:100%;"></select>
															</div>
															<div class="form-group">
																	<label>Kelurahan</label>
																	<select name="publisher_village" id="publisher_village" class="form-control required" style="width:100%;"></select>
															</div>
														</div>
														<div class="tab-pane" id="tab_cover">
														</div>
														<div class="tab-pane" id="tab_konten">
															<div id="content_serial">
																<div class="form-group">
																		<div class="form-group text-right">
																				<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal_edition">Tambah</button>
																		</div>
																		<div class="form-group">
																				<div class="table-responsive">
																						<table class="table table-bordered table-striped" id="datatable_edition">
																								<thead class="text-center">
																										<tr>
																												<th>Edisi / Volume</th>
																												<th>Tanggal Terbit</th>
																												<th>Cover</th>
																												<th>Konten</th>
																												<th>Hapus</th>
																										</tr>
																								</thead>
																								<tbody id="edition_element"></tbody>
																						</table>
																				</div>
																		</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</fieldset>
											<!-- Step 4 -->
											<h6>Hak Akses</h6>
											<fieldset>
												<div class="row">
													<div class="alert alert-success mb-2 w-100 align-middle" role="alert">
														<fieldset class="radio">
															<label>
																<input type="radio"  name="access" value="1"> Akses full file watermak secara online
															</label>
														</fieldset>
													</div>
													<div class="alert alert-success mb-2 w-100 align-middle" role="alert">
														<fieldset class="radio">
															<label>
																<input type="radio"  name="access" value="2" checked> Akses hanya preview file secara online, namun tetap dapat di dayagunakan di lingkungan perpustakaan nasional RI dengan jaringan internet LAN
															</label>
														</fieldset>
													</div>
													<div class="alert alert-success mb-2 w-100 align-middle" role="alert">
														<fieldset class="radio">
															<label>
																<input type="radio"  name="access" value="3"> Akses hanya preview file secara online, dan tidak dilayankan di Perpusnas RI selama 5 tahun sejak di serahkan. Setelah periode habis akan dapat dilayankan oleh perpusnas.
															</label>
														</fieldset>
													</div>
													<div class="alert alert-success mb-2 w-100 align-middle" role="alert">
														<fieldset class="radio">
															<label>
																<input type="radio"  name="access" value="4"> Akses hanya file preview secara online selamanya dan tidak didayagunakan di mana pun.
															</label>
														</fieldset>
													</div>
												</div>
											</fieldset>
											<!-- Step 4 -->
											<h6>Review dan Submit</h6>
											<fieldset>
												<h4>Review Penyerahan Koleksi</h4>
												<div class="row">
													<table class="table table-bordered table-striped">
														<tbody id="review-body">

														</tbody>
													</table>
													<div class="alert alert-success mb-2 w-100 align-middle" role="alert">
														<fieldset class="checkbox" style="width: fit-content; display: contents;">
															<label>
																<input type="checkbox"  name="agree-terms" value="1"> Saya menyetujui
															</label>
														</fieldset>
														<a onclick="showModalTerms()" style="color: blue;">syarat dan ketentuan berlaku</a>
													</div>
												</div>
											</fieldset>
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

<div class="modal fade" id="modal_slide" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
				<div class="modal-content" id="modal_edition_content">
						<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
								</button>
						</div>
						<div class="modal-body">
								<div class="row">
									<div class="col">
										<div id="carousel-example-caption" class="carousel slide" data-ride="carousel">
											<div class="carousel-inner" role="listbox">

											</div>
											<a class="carousel-control-prev" href="#carousel-example-caption" role="button" data-slide="prev">
												<span class="carousel-control-prev-icon  bg-danger" aria-hidden="true"></span>
												<span class="sr-only">Previous</span>
											</a>
											<a class="carousel-control-next" href="#carousel-example-caption" role="button" data-slide="next">
												<span class="carousel-control-next-icon bg-danger" aria-hidden="true"></span>
												<span class="sr-only">Next</span>
											</a>
										</div>
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
	var dataIsbn = [];
	var table;

	var tableSerial;
	var dataSerial = [];

	$(document).ready(function() {



		$('#type-collection-error').hide()
		$('#type_book').hide()
		$('#type_partitur').hide()
		$('#type_audio').hide()


		// Show form
		var form = $(".steps-validation").show();

		$(".steps-validation").steps({
				headerTag: "h6",
				bodyTag: "fieldset",
				transitionEffect: "fade",
				titleTemplate: '<span class="step">#index#</span> #title#',
				labels: {
						finish: 'Submit'
				},
				onStepChanging: function (event, currentIndex, newIndex)
				{
						$('#type-collection-error').hide()

						// Allways allow previous action even if the current form is not valid!
						if (currentIndex > newIndex)
						{
							if(newIndex == 1) {
								return false;
							}
							return true;
						}


						if ($("input[name='type']:checked").val() == undefined && currentIndex == 0)
						{
								Swal.fire({
										position: 'center',
										icon: 'warning',
										title: 'Harap memilih Tipe Publikasi',
										showConfirmButton: true
								});
								return false;
						}


						$('#validasi_element').hide();
						$('#validasi_content').html('');

						if(currentIndex == 1) {
							if ($("input[name='type_of_collection']").val() == "")
							{
									Swal.fire({
											position: 'center',
											icon: 'warning',
											title: 'Harap memilih Tipe Koleksi',
											showConfirmButton: true
									});
									return false;
							}
						}

						if(currentIndex == 2) {
							var valid = true;

							if($("input[name='type']:checked").val() == 1) {
									if($('#title_book').val() == "") {
										$('#validasi_content').append('<li>Judul wajib diisi!</li>');
										valid = false;
									}

									if($('#publication_year_book').val() == "") {
										$('#validasi_content').append('<li>Tahun Terbit wajib diisi!</li>');
										valid = false;
									}

									if($('#publication_month_book').val() == "") {
										$('#validasi_content').append('<li>Bulan Terbit wajib diisi!</li>');
										valid = false;
									}

									if($('#series_book').val() == "" && $("input[name='type_of_collection']").val() == 'isbn') {
										$('#validasi_content').append('<li>Seri wajib diisi!</li>');
										valid = false;
									}

									if($('#edition_book').val() == "" && $("input[name='type_of_collection']").val() == 'isbn') {
										$('#validasi_content').append('<li>Edisi wajib diisi!</li>');
										valid = false;
									}

									if($('#page_book').val() == "") {
										$('#validasi_content').append('<li>Halaman wajib diisi!</li>');
										valid = false;
									}

									if($('#thickness_book').val() == "") {
										$('#validasi_content').append('<li>Ketebalan wajib diisi!</li>');
										valid = false;
									}

									if($('#description_book').val() == "") {
										$('#validasi_content').append('<li>Deskripsi wajib diisi!</li>');
										valid = false;
									}  else if($('#description_book').val().length < 200) {
										$('#validasi_content').append('<li>Halaman kurang 200 karakter</li>');
										valid = false;
									}

							} else if($("input[name='type']:checked").val() == 2) {
									if($('#title_partitur').val() == "") {
										$('#validasi_content').append('<li>Judul wajib diisi!</li>');
										valid = false;
									}

									if($('#publication_year_partitur').val() == "") {
										$('#validasi_content').append('<li>Tahun Terbit wajib diisi!</li>');
										valid = false;
									}

									if($('#publication_month_partitur').val() == "") {
										$('#validasi_content').append('<li>Bulan Terbit wajib diisi!</li>');
										valid = false;
									}

									if($('#preview_partitur').val() == "") {
										$('#validasi_content').append('<li>Preview wajib diisi!</li>');
										valid = false;
									}

									if($('#description_partitur').val() == "") {
										$('#validasi_content').append('<li>Deskripsi wajib diisi!</li>');
										valid = false;
									} else if($('#description_partitur').val().length < 200) {
										$('#validasi_content').append('<li>Deskripsi kurang 200 karakter</li>');
										valid = false;
									}

									// if($('#code_partitur').val() == "" &&  $("input[name='type_of_collection']").val('ismn')) {
									//   $('#validasi_content').append('<li>ISMN wajib diisi!</li>');
									//   valid = false;
									// }
							} else if($("input[name='type']:checked").val() == 3) {
									if($('#title_map').val() == "") {
										$('#validasi_content').append('<li>Judul wajib diisi!</li>');
										valid = false;
									}

									if($('#publication_year_map').val() == "") {
										$('#validasi_content').append('<li>Tahun Terbit wajib diisi!</li>');
										valid = false;
									}

									if($('#publication_month_map').val() == "") {
										$('#validasi_content').append('<li>Bulan Terbit wajib diisi!</li>');
										valid = false;
									}

									if($('#preview_map').val() == "") {
										$('#validasi_content').append('<li>Preview wajib diisi!</li>');
										valid = false;
									}

									if($('#description_map').val() == "") {
										$('#validasi_content').append('<li>Deskripsi wajib diisi!</li>');
										valid = false;
									} else if($('#description_map').val().length < 200) {
										$('#validasi_content').append('<li>Deskripsi kurang 200 karakter</li>');
										valid = false;
									}

									if($('#code_map').val() == "" &&  $("input[name='type_of_collection']").val() == 'isbn') {
										$('#validasi_content').append('<li>ISBN wajib diisi!</li>');
										valid = false;
									}

							} else if($("input[name='type']:checked").val() == 5) {
									if($('#title_music').val() == "") {
										$('#validasi_content').append('<li>Judul wajib diisi!</li>');
										valid = false;
									}

									if($('#publication_year_music').val() == "") {
										$('#validasi_content').append('<li>Tahun Terbit wajib diisi!</li>');
										valid = false;
									}

									if($('#publication_month_music').val() == "") {
										$('#validasi_content').append('<li>Bulan Terbit wajib diisi!</li>');
										valid = false;
									}

									// if($('#preview_start_music').val() == "") {
									//   $('#validasi_content').append('<li>Preview mulai wajib diisi!</li>');
									//   valid = false;
									// }

									// if($('#preview_end_music').val() == "") {
									//   $('#validasi_content').append('<li>Preview Akhir wajib diisi!</li>');
									//   valid = false;
									// }

									if($('#description_music').val() == "") {
										$('#validasi_content').append('<li>Deskripsi wajib diisi!</li>');
										valid = false;
									} else if($('#description_music').val().length < 200) {
										$('#validasi_content').append('<li>Deskripsi kurang 200 karakter</li>');
										valid = false;
									}

									if($('#code_music').val() == "" &&  $("input[name='type_of_collection']").val() == 'isrc') {
										$('#validasi_content').append('<li>ISRC wajib diisi!</li>');
										valid = false;
									}
							} else if($("input[name='type']:checked").val() == 6) {
									if($('#title_video').val() == "") {
										$('#validasi_content').append('<li>Judul wajib diisi!</li>');
										valid = false;
									}

									if($('#publication_year_video').val() == "") {
										$('#validasi_content').append('<li>Publikasi wajib diisi!</li>');
										valid = false;
									}

									if($('#publication_month_video').val() == "") {
										$('#validasi_content').append('<li>Bulan Terbit wajib diisi!</li>');
										valid = false;
									}

									// if($('#preview_start_video').val() == "") {
									//   $('#validasi_content').append('<li>Preview mulai wajib diisi!</li>');
									//   valid = false;
									// }

									// if($('#preview_end_video').val() == "") {
									//   $('#validasi_content').append('<li>Preview akhir wajib diisi!</li>');
									//   valid = false;
									// }

									if($('#description_video').val() == "") {
										$('#validasi_content').append('<li>Deskripsi wajib diisi!</li>');
										valid = false;
									} else if($('#description_video').val().length < 200) {
										$('#validasi_content').append('<li>Deskripsi kurang 200 karakter</li>');
										valid = false;
									}

									if($('#code_video').val() == "" &&  $("input[name='type_of_collection']").val() == 'isan') {
										$('#validasi_content').append('<li>ISAN wajib diisi!</li>');
										valid = false;
									}
							} else if($("input[name='type']:checked").val() == 4) {

									if($('#title_serial').val() == "") {
										$('#validasi_content').append('<li>Judul wajib diisi!</li>');
										valid = false;
									}

									if($('#publication_year_serial').val() == "") {
										$('#validasi_content').append('<li>Tahun Terbit wajib diisi!</li>');
										valid = false;
									}

									if($('#publication_month_serial').val() == "") {
										$('#validasi_content').append('<li>Bulan Terbit wajib diisi!</li>');
										valid = false;
									}

									if($('#ddc_serial').val() == "") {
										$('#validasi_content').append('<li>DDC wajib diisi!</li>');
										valid = false;
									}

									if($('#description_serial').val() == "") {
										$('#validasi_content').append('<li>Deskripsi wajib diisi!</li>');
										valid = false;
									} else if($('#description_serial').val().length < 200) {
										$('#validasi_content').append('<li>Deskripsi kurang 200 karakter</li>');
										valid = false;
									}

									if($('input[name^="edition_cover_field"]').length < 1) {
										$('#validasi_content').append('<li>Edisi Konten wajib diisi minimal 1</li>');
										valid = false;
									}
							}

							if($('#publisher_address').val() == "") {
								$('#validasi_content').append('<li>Alamat Publisher wajib diisi!</li>');
								valid = false;
							}

							if($('#publisher_province').val() == '') {
								$('#validasi_content').append('<li>Provinsi Publisher wajib diisi!</li>');
								valid = false;
							}

							if($('#publisher_city').val() == '') {
								$('#validasi_content').append('<li>Kota Publisher wajib diisi!</li>');
								valid = false;
							}

							if($('#publisher_district').val() == '') {
								$('#validasi_content').append('<li>Kecamatan Publisher wajib diisi!</li>');
								valid = false;
							}

							if($('#publisher_village').val() == '') {
								$('#validasi_content').append('<li>Desa Publisher wajib diisi!</li>');
								valid = false;
							}

							var x = 0;
							var falseContributor = 0;
							for (x = 0; x < countContributor; x++) {
									if($('#contributor_id_field_' + x).val() == '') {
										falseContributor++;
										valid = false;
									}

									if($('#author_fullname_field_' + x).val() == '') {
										falseContributor++;
										valid = false;
									}

									if($('#author_title_field_' + x).val() == '') {
										falseContributor++;
										valid = false;
									}
							}
							if(falseContributor > 0) {
								$('#validasi_content').append('<li>Mohon lengkapi data kontributor!</li>');
							}

							var i = 0;
							var falseContent = 0;
							var falseCover = 0;
							for (i = 1; i <= countFile; i++) {
									if($('#file_cover_' + i).val() == "") {
										valid = false;
										falseCover++;
									}

									if($("input[name='type']:checked").val() != 4) {
										if($('#file_konten_' + i).val() == "") {
											valid = false;
											falseContent++;
										}
									}
							}

							if(falseContent > 0) {
								$('#validasi_content').append('<li>Mohon isi data konten!</li>');
							}

							if(falseCover > 0) {
								$('#validasi_content').append('<li>Mohon isi data cover!</li>');
							}

							if(!valid) {
								Swal.fire({
											position: 'center',
											icon: 'warning',
											title: 'Harap mengisi semua data',
											showConfirmButton: true
									});
								$('#validasi_element').show();
							}

							return valid;
						}

						if(currentIndex == 3) {
							if ($("input[name='access']:checked").val() == undefined)
							{
									Swal.fire({
											position: 'center',
											icon: 'warning',
											title: 'Harap memilih hak Akses',
											showConfirmButton: true
									});
									return false;
							}
						}

						return true;
				},
				onStepChanged: function (event, currentIndex, priorIndex) {
					if(currentIndex == 1) {

						$('#type_book').hide()
						$('#type_partitur').hide()
						$('#type_audio').hide()
						$('#type_map').hide()
						$('#type_serial').hide()
						$('#type_video').hide()

						if($("input[name='type']:checked").val() == "1") {
							$('#type_book').show()
						} else if($("input[name='type']:checked").val() == "2") {
							$('#from_detail').fadeIn(500)
							resetForm()
							$("input[name='type_of_collection']").val('non_ismn')
							$('#form_ismn').show()
							showFromPartitur();
						} else if($("input[name='type']:checked").val() == "5") {
							$('#from_detail').fadeIn(500)
							resetForm()
							$("input[name='type_of_collection']").val('non_isrc')
							$('#form_music').hide()
							showFromMusic();
						} else if($("input[name='type']:checked").val() == "3") {
							$('#from_detail').fadeIn(500)
							resetForm()
							$("input[name='type_of_collection']").val('non_isbn')
							$('#form_map').hide()
							showFromMap();
						} else if($("input[name='type']:checked").val() == "4") {
							//$('#from_detail').fadeIn(500)
							resetForm()
							$("input[name='type_of_collection']").val('issn')
							$('#type_serial').fadeIn(500)
							//showFromSerial()
							if(tableSerial == '') {
								loadDataTableSerial()
							}
						} else if($("input[name='type']:checked").val() == "6") {
							$('#from_detail').fadeIn(500)
							resetForm()
							$("input[name='type_of_collection']").val('non_isan')
							$('#form_video').hide()
							showFromVideo()
						}
					} else if(currentIndex == 4) {
						initReview()
					}


				},
				onFinishing: function (event, currentIndex)
				{
						if ($("input[name='agree-terms']:checked").val() == undefined)
						{
								Swal.fire({
										position: 'center',
										icon: 'warning',
										title: 'Mohon check syarat dan ketentuan yang berlaku',
										showConfirmButton: true
								});
								return false;
						} else {
							create();
						}
				}
		});

		$('.select_type_book').click(function(){
			resetForm()
			if($(this).val() == 'isbn') {
				$('#select_isbn').fadeIn(500);
				$('#from_detail').hide()
				$("input[name='type_of_collection']").val('isbn')
				loadDataTableISBN()
			} else {
				$('#from_detail').fadeIn(500)
				$('#select_isbn').hide()
				$("input[name='type_of_collection']").val('non_isbn')
				showFromBookNonIsbn()
			}
		});
		$('.select_type_partitur').click(function(){
			$('#from_detail').fadeIn(500)
			resetForm()
			if($(this).val() == 'ismn') {
				$("input[name='type_of_collection']").val('ismn')
				$('#form_ismn').show()
			} else {
				$("input[name='type_of_collection']").val('non_ismn')
				$('#form_ismn').hide()
			}
			showFromPartitur();
		});

		$('.select_type_map').click(function(){
			$('#from_detail').fadeIn(500)
			resetForm()
			if($(this).val() == 'isbn') {
				$("input[name='type_of_collection']").val('isbn')
				$('#form_map').show()
			} else {
				$("input[name='type_of_collection']").val('non_isbn')
				$('#form_map').hide()
			}
			showFromMap();
		});

		$('.select_type_audio').click(function(){
			$('#from_detail').fadeIn(500)
			resetForm()
			if($(this).val() == 'isrc') {
				$("input[name='type_of_collection']").val('isrc')
				$('#form_music').show()
			} else {
				$("input[name='type_of_collection']").val('non_isrc')
				$('#form_music').hide()
			}
			showFromMusic();
		});

		$('.select_type_video').click(function(){
			$('#from_detail').fadeIn(500)
			resetForm()
			if($(this).val() == 'isan') {
				$("input[name='type_of_collection']").val('isan')
				$('#form_video').show()
			} else {
				$("input[name='type_of_collection']").val('non_isan')
				$('#form_video').hide()
			}
			showFromVideo();
		});

		$('.select_type_serial').click(function(){
			$('#from_detail').fadeIn(500)
			resetForm()
			if($(this).val() == 'issn') {
				$("input[name='type_of_collection']").val('issn')
				$('#form_serial').show()
			} else {
				$("input[name='type_of_collection']").val('non_issn')
				$('#form_serial').hide()
			}
			showFromSerial();
		});

	});

	function resetForm() {
		$('#form_detail_partitur').hide()
		$('#form_detail_map').hide()
		$('#form_detail_music').hide()
		$('#form_detail_video').hide()
		$('#form_detail_book').hide()
		$('#form_detail_serial').hide()
		$('#content_serial').hide()
	}

	function initReview() {

		let container = $('#review-body')
		$('.review-value').remove()


		let type = $("input[name='type']:checked").val();
		let typeElement = ''
		if(type == "1") {
			typeElement = 'book'
		} else if(type == "2") {
			typeElement = 'partitur'
		} else if(type == "3") {
			typeElement = 'map'
		} else if(type == "5") {
			typeElement = 'music'
		} else if(type == "6") {
			typeElement = 'video'
		} else if(type == "4") {
			typeElement = 'serial'
		}

		container.append('<tr class="review-value"><td>Judul</td><td>'+$('#title_' + typeElement).val()+'</td></tr>')

		if(typeElement == 'book') {
			container.append('<tr class="review-value"><td>Seri</td><td>'+$('#series_' + typeElement).val()+'</td></tr>')
			container.append('<tr class="review-value"><td>Jumlah Halaman</td><td>'+$('#page_' + typeElement).val()+'</td></tr>')
			container.append('<tr class="review-value"><td>Edisi</td><td>'+$('#edition_' + typeElement).val()+'</td></tr>')
			container.append('<tr class="review-value"><td>Ketebalan</td><td>'+$('#thickness_' + typeElement).val()+'</td></tr>')
		} else if(typeElement == 'serial') {
		} else {
			container.append('<tr class="review-value"><td>Tahun Terbit</td><td>'+$('#publication_year_' + typeElement).val()+'</td></tr>')
		}

		container.append('<tr class="review-value"><td>Deskripsi</td><td>'+$('#description_' + typeElement).val()+'</td></tr>')

		container.append('<tr class="review-value"><td>Publisher</td><td>'+$('#publisher_name').val()+'</td></tr>')

		var kontributor = [];

		$('input[name^="author_fullname_field"]').each( function( key, value ) {
			console.log($(this).val())
			kontributor.push($(this).val())
		})

		container.append('<tr class="review-value"><td>Kontributor</td><td>'+kontributor.join(",")+'</td></tr>')

		var category = [];
		$('input[name^="category"]').each( function( key, value ) {
			if($(this).filter(":checked").val()) {
				category.push($(this).attr("data-name"))
			}
		})

		container.append('<tr class="review-value"><td>Kategori</td><td>'+category.join(",")+'</td></tr>')

		let access = $("input[name='access']:checked").val();
		var desc = '';

		if(access == 1) {
			desc = 'Akses full file watermak secara online';
		} else if(access == 2) {
			desc = 'Akses hanya preview file secara online, namun tetap dapat di dayagunakan di lingkungan perpustakaan nasional RI dengan jaringan internet LAN';
		} else if(access == 3) {
			desc = 'Akses hanya preview file secara online, dan tidak dilayankan di Perpusnas RI selama 5 tahun sejak di serahkan. Setelah periode habis akan dapat dilayankan oleh perpusnas.';
		} else if(access == 4) {
			desc = 'Akses hanya file preview secara online selamanya dan tidak didayagunakan di mana pun.'
		}
		container.append('<tr class="review-value"><td>Hak Akses</td><td>'+desc+'</td></tr>')


	}

	function showFromPartitur() {
		$('#form_detail_partitur').show()
		countFile = 1;
		countContributor = 0;

		$('.file-upload').remove()
		$('.category-checkbox').remove()
		$('.contributor').remove()

		let type = $("input[name='type']:checked").val();
		let item = ""

		let typeFile = ".pdf"
		let descFile = "PDF"

		let inputFileCover = '<div class="file-upload"><div class="alert alert-warning"><small>Jenis File Yang di Dukung <b>: JPG, JPEG, PNG</b><br>Maksimal Ukuran File <b>: 2 MB</b></small></div><div class="form-group"><label>Cover '+item+' : <span class="danger">*</span></label><input type="file" name="file_upload[cover]['+item.trim()+'][]" class="form-control " accept=".jpg,.png" id="file_cover_'+countFile+'"></div></div>'
		let inputFileKonten = '<div class="file-upload"><div class="alert alert-warning"><small>Jenis File Yang di Dukung <b>: '+descFile+'</b><br>Maksimal Ukuran File <b>: 500 MB</b></small></div><div class="form-group"><label>Konten '+item+' : <span class="danger">*</span></label><input type="file" name="file_upload[content]['+item.trim()+'][]" class="form-control" accept="'+ typeFile +'" id="file_konten_'+countFile+'"></div></div>'

		$('#tab_cover').append(inputFileCover)
		$('#tab_konten').append(inputFileKonten)

		//$('#form-contributor_partitur').append(fromContributor(countContributor));
		addContributor('#form-contributor_partitur')


		getCategoryByType($('#category_partitur'))
		select2LoadAll('#contributor_id_field_' + countContributor, 'load_contributor/' + $("input[name='type']:checked").val());
		select2Author('#author_id_field_' + countContributor, 'load_author', countContributor);

		initializePublisher()

		$(".steps-validation").steps("next")
	}

	function showFromMap() {
		$('#form_detail_map').show()
		countFile = 1;
		countContributor = 0;

		$('.file-upload').remove()
		$('.category-checkbox').remove()
		$('.contributor').remove()

		let type = $("input[name='type']:checked").val();
		let item = ""


		let typeFile = ".pdf"
		let descFile = "PDF"


		let inputFileCover = '<div class="file-upload"><div class="alert alert-warning"><small>Jenis File Yang di Dukung <b>: JPG, JPEG, PNG</b><br>Maksimal Ukuran File <b>: 2 MB</b></small></div><div class="form-group"><label>Cover '+item+' : <span class="danger">*</span></label><input type="file" name="file_upload[cover]['+item.trim()+'][]" class="form-control " accept=".jpg,.png" id="file_cover_'+countFile+'"></div></div>'
		let inputFileKonten = '<div class="file-upload"><div class="alert alert-warning"><small>Jenis File Yang di Dukung <b>: '+descFile+'</b><br>Maksimal Ukuran File <b>: 500 MB</b></small></div><div class="form-group"><label>Konten '+item+' : <span class="danger">*</span></label><input type="file" name="file_upload[content]['+item.trim()+'][]" class="form-control" accept="'+ typeFile +'" id="file_konten_'+countFile+'"></div></div>'

		$('#tab_cover').append(inputFileCover)
		$('#tab_konten').append(inputFileKonten)

		//$('#form-contributor').append(fromContributor(countContributor));
		addContributor('#form-contributor_map')


		getCategoryByType($('#category_map'))
		select2LoadAll('#contributor_id_field_' + countContributor, 'load_contributor/' + $("input[name='type']:checked").val());
		select2Author('#author_id_field_' + countContributor, 'load_author', countContributor);

		initializePublisher()

		$(".steps-validation").steps("next")
	}

	function showFromMusic() {
		$('#form_detail_music').show()

		sliderMusic = document.getElementById('slider_music');

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

		countFile = 1;
		countContributor = 0;

		$('.file-upload').remove()
		$('.category-checkbox').remove()
		$('.contributor').remove()

		let type = $("input[name='type']:checked").val();
		let item = ""


		let typeFile = ".wav"
		let descFile = "WAV"


		let inputFileCover = '<div class="file-upload"><div class="alert alert-warning"><small>Jenis File Yang di Dukung <b>: JPG, JPEG, PNG</b><br>Maksimal Ukuran File <b>: 2 MB</b></small></div><div class="form-group"><label>Cover '+item+' : <span class="danger">*</span></label><input type="file" name="file_upload[cover]['+item.trim()+'][]" class="form-control " accept=".jpg,.png" id="file_cover_'+countFile+'"></div></div>'
		let inputFileKonten = '<div class="file-upload"><div class="alert alert-warning"><small>Jenis File Yang di Dukung <b>: '+descFile+'</b><br>Maksimal Ukuran File <b>: 500 MB</b></small></div><div class="form-group"><label>Konten '+item+' : <span class="danger">*</span></label><input type="file" name="file_upload[content]['+item.trim()+'][]" class="form-control" accept="'+ typeFile +'" id="file_konten_'+countFile+'"></div></div>'

		$('#tab_cover').append(inputFileCover)
		$('#tab_konten').append(inputFileKonten)

		//$('#form-contributor').append(fromContributor(countContributor));
		addContributor('#form-contributor_music')


		getCategoryByType($('#category_music'))
		select2LoadAll('#contributor_id_field_' + countContributor, 'load_contributor/' + $("input[name='type']:checked").val());
		select2Author('#author_id_field_' + countContributor, 'load_author', countContributor);

		initializePublisher()

		$(".steps-validation").steps("next")
	}

	function showFromSerial() {
		$('#form_detail_serial').show()
		countFile = 1;
		countContributor = 0;

		$('.file-upload').remove()
		$('.category-checkbox').remove()
		$('.contributor').remove()

		let type = $("input[name='type']:checked").val();
		let item = ""

		let inputFileCover = '<div class="file-upload"><div class="alert alert-warning"><small>Jenis File Yang di Dukung <b>: JPG, JPEG, PNG</b><br>Maksimal Ukuran File <b>: 2 MB</b></small></div><div class="form-group"><label>Cover '+item+' : <span class="danger">*</span></label><input type="file" name="file_upload[cover]['+item.trim()+'][]" class="form-control " accept=".jpg,.png" id="file_cover_'+countFile+'"></div></div>'

		$('#tab_cover').append(inputFileCover)
		$('#content_serial').show()

		// $('#form-contributor').append(fromContributor(countContributor));
		addContributor('#form-contributor_serial')

		getCategoryByType($('#category_serial'))
		select2LoadAll('#contributor_id_field_' + countContributor, 'load_contributor/' + $("input[name='type']:checked").val());
		select2Author('#author_id_field_' + countContributor, 'load_author', countContributor);

		initializePublisher()

		$(".steps-validation").steps("next")
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
						url: '{{ url("publisher/collection/save_temporary") }}',
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
								$('#form_data').append(`
										<input type="hidden" class="serial_input" name="edition_edition_field[]" value="` + edition_field + `">
										<input type="hidden" class="serial_input" name="edition_date_field[]" value="` + date_field + `">
										<input type="hidden" class="serial_input" name="edition_total_page_field[]" value="` + total_page_field + `">
										<input type="hidden" class="serial_input" name="edition_cover_field[]" value="` + response.cover_path + `">
										<input type="hidden" class="serial_input" name="edition_original_field[]" value="` + response.original_path + `">
								`);

								$('#datatable_edition').DataTable().row.add([
										edition_field,
										response.date_field,
										response.cover_field,
										response.original_field,
										'<button type="button" class="btn btn-danger btn-sm" id="remove_field_edition"><i class="la la-trash"></i></button>'
								]).draw().node();

								$('#datatable_edition tbody').on('click', '#remove_field_edition', function () {
									console.log('remove edition')
										$('#datatable_edition').DataTable().row($(this).parents('tr')).remove().draw();
								});

								$('#modal_edition').modal('hide');
								$('#edition_field').val('');
								$('#date_field').val('');
								$('#cover_field').val('');
								$('#original_field').val('');
								$('#total_page_field').val('');
						}
				});
		}
	}

	function showFromVideo() {
		$('#form_detail_video').show()

		sliderVideo = document.getElementById('slider_video');

		noUiSlider.create(sliderVideo, {
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

		countFile = 1;
		countContributor = 0;

		$('.file-upload').remove()
		$('.category-checkbox').remove()
		$('.contributor').remove()

		let type = $("input[name='type']:checked").val();
		let item = ""


		let typeFile = ".mp4"
		let descFile = "MP4"

		let inputFileCover = '<div class="file-upload"><div class="alert alert-warning"><small>Jenis File Yang di Dukung <b>: JPG, JPEG, PNG</b><br>Maksimal Ukuran File <b>: 2 MB</b></small></div><div class="form-group"><label>Cover '+item+' : <span class="danger">*</span></label><input type="file" name="file_upload[cover]['+item.trim()+'][]" class="form-control " accept=".jpg,.png" id="file_cover_'+countFile+'"></div></div>'
		let inputFileKonten = '<div class="file-upload"><div class="alert alert-warning"><small>Jenis File Yang di Dukung <b>: '+descFile+'</b><br>Maksimal Ukuran File <b>: 500 MB</b></small></div><div class="form-group"><label>Konten '+item+' : <span class="danger">*</span></label><input type="file" name="file_upload[content]['+item.trim()+'][]" class="form-control" accept="'+ typeFile +'" id="file_konten_'+countFile+'"></div></div>'

		$('#tab_cover').append(inputFileCover)
		$('#tab_konten').append(inputFileKonten)

		// $('#form-contributor').append(fromContributor(countContributor));
		addContributor('#form-contributor_video')

		getCategoryByType($('#category_video'))
		select2LoadAll('#contributor_id_field_' + countContributor, 'load_contributor/' + $("input[name='type']:checked").val());
		select2Author('#author_id_field_' + countContributor, 'load_author', countContributor);

		initializePublisher()

		$(".steps-validation").steps("next")
	}

	function showFromBookNonIsbn() {

		$('#form_detail_book').show()
		countFile = 1;
		countContributor = 0;

		$('.file-upload').remove()
		$('.category-checkbox').remove()
		$('.contributor').remove()

		$('#call_number_book').hide()
		$('#form_title_book_ori').hide()
		$('#form_title_book').show()
		$('#label_title_book').html("Judul Buku")

		let type = $("input[type='radio'][name='type']").val();
		let item = ""

		let typeFile = ".pdf"
		let descFile = "PDF"


		let inputFileCover = '<div class="file-upload"><div class="alert alert-warning"><small>Jenis File Yang di Dukung <b>: JPG, JPEG, PNG</b><br>Maksimal Ukuran File <b>: 2 MB</b></small></div><div class="form-group"><label>Cover '+item+' : <span class="danger">*</span></label><input type="file" name="file_upload[cover]['+item.trim()+'][]" class="form-control " accept=".jpg,.png" id="file_cover_'+countFile+'"></div></div>'
		let inputFileKonten = '<div class="file-upload"><div class="alert alert-warning"><small>Jenis File Yang di Dukung <b>: '+descFile+'</b><br>Maksimal Ukuran File <b>: 500 MB</b></small></div><div class="form-group"><label>Konten '+item+' : <span class="danger">*</span></label><input type="file" name="file_upload[content]['+item.trim()+'][]" class="form-control" accept="'+ typeFile +'" id="file_konten_'+countFile+'"></div></div>'

		$('#tab_cover').append(inputFileCover)
		$('#tab_konten').append(inputFileKonten)

		//$('#form-contributor').append(fromContributor(countContributor));
		addContributor('#form-contributor_book')

		$('#subject_book_parent').hide()

		getCategoryByType($('#category_book'))
		select2LoadAll('#contributor_id_field_' + countContributor, 'load_contributor/' + $("input[name='type']:checked").val());
		select2Author('#author_id_field_' + countContributor, 'load_author', countContributor);

		initializePublisher()


		$(".steps-validation").steps("next")

	}

	function loadDataTableISBN() {
		table = $('#datatable_serverside_isbn').DataTable({
			processing: true,
			serverSide: true,
			destroy: true,
			scrollX: true,
			order: [[0, 'desc']],
			pagingType: 'input',
			iDisplayInLength: 10,
            ajax: {
				url: '{{ url("publisher/collection/isbn-by-publisher-all") }}',
				data: {
					publisher_id: $('#publisher_id').val()
				}
			},
			columns: [
				{ name: 'kd_penerbit', searchable: false, className: 'align-middle text-center' },
                { name: 'code', className: 'align-middle text-center' },
                { name: 'nama_penerbit', className: 'align-middle text-center' },
                { name: 'title', className: 'align-middle text-center' },
                { name: 'action', orderable: false, searchable: false, className: 'align-middle text-center' }
			]
		});
	}

	function selectisbn(selector) {
			var isbnno          = [];
			var keterangan      = [];
            var kd_penerbit_dtl = $('#' + selector).attr('kd_penerbit_dtl');

			if(kd_penerbit_dtl != '') {
				$.ajax({
					url: '{{ url("publisher/collection/isbn-jilid") }}' + '/' + kd_penerbit_dtl,
					type: 'GET',
                    dataType: 'JSON',
					success: function(data) {
                        isbnno.push(data.code);
                        keterangan.push('-');

                        $('#from_detail').fadeIn(300);
				        $('#form_detail_book').show();
                        $('#call_number_book').show();
                        $('#form_title_book_ori').show();
                        $('#form_title_book').show();
                        $('#label_title_book').html("Perubahan Judul Buku <small style='color:blue'>(Ubah hanya jika ada perubahan pada judul buku yang Anda terbitkan)</small>");
                        $('#isbn_book').val(data.code);
                        $('#title_book').val(data.title);
                        $('#title_ori_book').val(data.title);
                        $('#series_book').val(data.seri);
                        $('#edition_book').val(data.edisi);
                        $('#page_book').val(data.jml_hlm);
                        $('#description').val(data.sinopsis);
                        $('#subject_1').val(data.subjek);
                        $('#ddc_serial').val(data.call_number);
                        $('.file-upload').remove();
                        $('.category-checkbox').remove();
                        $('.contributor').remove();
                        $('.list-subject').remove();

                        if(data.subjek) {
                        	$('#subject_book_parent').show();
                        } else  {
                            $('#subject_book_parent').hide();
                        }

                        if(data.subjek) {
                        	$('#list_subject').append('<li class="list-group-item list-subject">'+data.subjek+'</li>');
                        }

                        countFile        = 0;
                        countContributor = 0;

                        countFile++;
                        countFile++;

                        let typeFile = '.pdf, .epub, .mp3';
                        let descFile = 'PDF, EPUB, MP3';

                        let inputFileCover = '<div class="file-upload"><div class="alert alert-warning"><small>Jenis File Yang di Dukung <b>: JPG, JPEG, PNG</b><br>Maksimal Ukuran File <b>: 2 MB</b></small></div><div class="form-group"><label>Cover '+data.code+' : <span class="danger">*</span></label><input type="file" name="file_upload[cover]['+data.code+'][]" class="form-control " accept=".jpg,.png" id="file_cover_'+countFile+'"></div></div>'
                        let inputFileKonten = '<div class="file-upload"><div class="alert alert-warning"><small>Jenis File Yang di Dukung <b>: '+descFile+'</b><br>Maksimal Ukuran File <b>: 500 MB</b></small></div><div class="form-group"><label>Konten '+data.code+' : <span class="danger">*</span></label><input type="file" name="file_upload[content]['+data.code+'][]" class="form-control" accept="'+ typeFile +'" id="file_konten_'+countFile+'"></div></div>';

                        $('#tab_cover').append(inputFileCover);
                        $('#tab_konten').append(inputFileKonten);

                        if(isbnno.length > 1) {
                        	if(isbnno[1].trim() != "") {
                        		i = 0;
                        		isbnno.forEach(function(item, index) {
                        			var code = item.trim()
                        			if(code || code.length > 0) {
                        				countFile++;

                        				let type = $("input[type='radio'][name='type']").val();
                        				let typeFile = ".pdf, .epub, .mp3";
                        				let descFile = "PDF, EPUB, MP3";

                        				let inputFileCover = '<div class="file-upload"><div class="alert alert-warning"><small>Jenis File Yang di Dukung <b>: JPG, JPEG, PNG</b><br>Maksimal Ukuran File <b>: 2 MB</b></small></div><div class="form-group"><label>Cover '+code+  ' ' + keterangan[i]+' : <span class="danger">*</span></label><input type="file" name="file_upload[cover]['+code.trim()+'][]" class="form-control " accept=".jpg,.png" id="file_cover_'+countFile+'"></div></div>'
                        				let inputFileKonten = '<div class="file-upload"><div class="alert alert-warning"><small>Jenis File Yang di Dukung <b>: '+descFile+'</b><br>Maksimal Ukuran File <b>: 500 MB</b></small></div><div class="form-group"><label>Konten '+code+ ' ' + keterangan[i]+' : <span class="danger">*</span></label><input type="file" name="file_upload[content]['+code.trim()+'][]" class="form-control" accept="'+ typeFile +'" id="file_konten_'+countFile+'"></div></div>'

                        				$('#tab_cover').append(inputFileCover);
                        				$('#tab_konten').append(inputFileKonten);
                        				i++;
                        			}
                        		});
                        	}
                        }

                        var kepeng = data.kepeng.split(";");
                        if(kepeng.length > 0 ) {
                        	kepeng.forEach(function(item, index) {
                        		addContributor('#form-contributor_book');

                        		let number = (countContributor - 1);
                        		$('#contributor_name_field_' + number).val('penulis');
                        		$('#author_fullname_field_' + number).val(item.trim());

                        		var newOption = new Option('penulis', 0, true, true);
                        		$('#contributor_id_field_'+number).append(newOption).trigger('change');

                        		var newOption = new Option(item.trim(), item.trim(), true, true);
                        		$('#author_id_field_'+number).append(newOption).trigger('change');

                        	});
                        } else{
                        	addContributor('#form-contributor_book');
                        }

                        getCategoryByType($('#category_book'));
                        initializePublisher();

                        $(".steps-validation").steps("next");
					}
				});
			}
		}

	function initializePublisher() {

		select2Nested('#publisher_province', 'load_province', '');
		select2Nested('#publisher_city', 'load_city', $('#publisher_province'));
		select2Nested('#publisher_district', 'load_district', $('#publisher_city'));
		select2Nested('#publisher_village', 'load_village', $('#publisher_district'));

		var provinceId = "{{ $data['publisher']->province_id }}"

		if(provinceId != "") {
			var province = {
					id: "{{ $data['publisher']->province_id }}",
					text: "{{ $data['publisher']->province != '' ? $data['publisher']->province->name : '' }}"
			}

			var newOption = new Option(province.text, province.id, false, false);
			$('#publisher_province').append(newOption).trigger('change');
		}

		var cityId = "{{ $data['publisher']->city_id }}"

		if(cityId != "") {
			var city = {
					id: "{{ $data['publisher']->city_id }}",
					text: "{{ $data['publisher']->city != '' ? $data['publisher']->city->name : '' }}"
			}

			var newOption = new Option(city.text, city.id, false, false);
			$('#publisher_city').append(newOption).trigger('change');
		}

		var districtId = "{{ $data['publisher']->district_id }}"

		if(districtId != "") {
			var district = {
					id: "{{ $data['publisher']->district_id }}",
					text: "{{ $data['publisher']->district != '' ? $data['publisher']->district->name : '' }}"
			}

			var newOption = new Option(district.text, district.id, false, false);
			$('#publisher_district').append(newOption).trigger('change');
		}

		var villageId = "{{ $data['publisher']->village_id }}"

		if(villageId != "") {
			var village = {
					id: "{{ $data['publisher']->village_id }}",
					text: "{{ $data['publisher']->village != '' ? $data['publisher']->village->name : '' }}"
			}

			var newOption = new Option(village.text, village.id, false, false);
			$('#publisher_village').append(newOption).trigger('change');
		}
	}

	function formatDetailRow ( d ) {

			var isbnno = d[4].isbnno.split("~");

			var html = "";
			isbnno.forEach(function(item, index) {
				var code = item.trim()
				if(code || code.length > 0) {
					html += "<tr><td>"+code+"</td><td>"+d[4].title+"</td></tr>"
				}
			});

			return html
	}

	function getCategoryByType(container) {
		$.ajax({
			url: '{{ url("publisher/select2_serverside/load_category") }}' + '/' + $("input[name='type']:checked").val(),
			type: 'POST',
			dataType: 'JSON',headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success: function(response) {
				response.items.forEach(function(item, index) {

					let html = '<div class="col-md-3 custom-control custom-checkbox mr-1 category-checkbox"><input type="checkbox" id="checkbox-'+item.id+'" class="custom-control-input" name="category[]" data-name="'+item.text+'" value="'+item.id+'"><label class="custom-control-label" for="checkbox-'+item.id+'">'+item.text+'</label></div>'

					container.append(html);
				})
			}
		})

	}

	function getSubjectByType(container) {
		$.ajax({
			url: '{{ url("publisher/select2_serverside/load_subject") }}',
			type: 'POST',
			dataType: 'JSON',
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success: function(response) {
				response.items.forEach(function(item, index) {

					let html = '<div class="d-inline-block custom-control custom-checkbox mr-1 category-checkbox"><input type="checkbox" id="checkbox-'+item.id+'" class="custom-control-input" name="subject[]" value="'+item.id+'"><label class="custom-control-label" for="checkbox-'+item.id+'">'+item.name+'</label></div>'

					container.append(html);
				})
			}
		})

	}

	function addContributor(element) {

		$(element).append(fromContributor(countContributor))

		$('#remove_field_contributor_' + countContributor).click(function() {
			countContributor--;
			let parent = $(this).closest('.contributor').remove();
		})

		select2LoadContributor('#contributor_id_field_' + countContributor, 'load_contributor/' + $("input[name='type']:checked").val(), countContributor);
		select2Author('#author_id_field_' + countContributor, 'load_author', countContributor);
		countContributor++;

	}

	function fromContributor(count) {
			let html = '<div class="row contributor"><div class="col-md-3"><div class="form-group"><label>Role Kontributor : </label><input type="hidden" name="contributor_name_field[]" id="contributor_name_field_'+count+'" /><select name="contributor_id_field[]" id="contributor_id_field_'+count+'" class="form-control" style="width:100%;"></select><p>ex: Penulis, Penyanyi, Pengisi Suara, dll</p></div></div><div class="col-md-3"><div class="form-group"><label>Nama Lengkap:</label><input type="hidden" name="author_fullname_field[]" id="author_fullname_field_'+count+'" /><select name="author_id_field[]" id="author_id_field_'+count+'" class="form-control" style="width:100%;"></select></div></div><div class="col-md-2"><div class="form-group"><label>Titel :</label><input type="text" name="author_title_field[]" id="author_title_field_'+count+'" class="form-control" placeholder="Titel"><p>ex: Ir.,S.komp., S.pd, dll</p></div></div><div class="col-md-2"><div class="form-group"><label>Tahun Kelahiran :</label><input type="number" name="author_year_of_birth_field[]" id="author_year_of_birth_field_'+count+'" class="form-control" placeholder="Tahun kelahiran"></div></div><div class="col-md-2"><div class="form-group"><label>Tahun Kematian :</label><input type="number" name="author_year_of_death_field[]" id="author_year_of_death_field_'+count+'" class="form-control" placeholder="Tahun kematian"></div></div>';

			if(count > 0) {
				html += '<div class="col-md-1"><div class="form-group"><label>Hapus</label><button type="button" class="btn btn-icon btn-secondary mr-1" id="remove_field_contributor_'+count+'"><i class="la la-trash"></i></button></div></div>';
			}

			html += '</div>';

			return html
	}

	function select2Author(selector, endpoint, count) {
			$(selector).select2({
					placeholder: '-- Pilih --',
					minimumInputLength: 3,
					allowClear: true,
					tags: true,
					cache: true,
					ajax: {
							url: '{{ url("publisher/select2_serverside") }}' + '/' + endpoint,
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
						$('#author_fullname_field_' + count).val(data.text)
						$('#author_title_field_' + count).val(data.title)
						$('#author_year_of_birth_field_' + count).val(data.yob)
						$('#author_year_of_death_field_' + count).val(data.yod)

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

	function select2LoadContributor(selector, endpoint, count) {
				$(selector).select2({
						placeholder: '-- Pilih --',
						allowClear: true,
						cache: true,
						dropdowntParent: $('#modal_element'),
						ajax: {
								url: '{{ url("publisher/select2_serverside") }}' + '/' + endpoint,
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
								},
								templateSelection: function (data) {
									console.log('templateSelection.data: ', data);
									$('#contributor_name_field_' + count).val(data.text)
									return data.text;
								}
						}
				});

				$(selector).on("select2:select", function (e) {
					$('#contributor_name_field_' + count).val(e.params.data.text)
				});
		}

	function create() {
			var formData = new FormData($('#form_data')[0]);

			if($("input[name='type']:checked").val() == 6) {
				//video
				formData.append('preview_video', sliderVideo.noUiSlider.get());

			} else if($("input[name='type']:checked").val() == 5) {
				formData.append('preview_music', sliderMusic.noUiSlider.get());
				//audio
			}

			$.ajax({
					url: '{{ url("publisher/collection/create_manual") }}',
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
									window.location.href = "{{ url('publisher/collection/monitoring/detail/') }}" + "/" + response.id;
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

	function showModalTerms() {
		$('#modal_terms').modal('show')
	}
</script>

@include('publisher.collection.script-serial')
