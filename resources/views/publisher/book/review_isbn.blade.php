<!DOCTYPE html>
<html class="loading" lang="{{ config('app.locale') }}" data-textdirection="ltr">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<title>Update Koleksi</title>
	<link rel="apple-touch-icon" href="{{ asset(Storage::url('public/main/favicon.png')) }}">
	<link rel="shortcut icon" type="image/png" href="{{ asset(Storage::url('public/main/favicon.png')) }}">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Quicksand:300,400,500,700" rel="stylesheet">
	<link href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css" rel="stylesheet">
	<link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/vendors.css') }}">
	<link rel="stylesheet" href="{{ asset('theme_admin/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
	<link rel="stylesheet" href="{{ asset('theme_admin/app-assets/vendors/css/tables/extensions/buttons.dataTables.min.css') }}">
	<link rel="stylesheet" href="{{ asset('theme_admin/app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css') }}">
	<link rel="stylesheet" href="{{ asset('theme_admin/app-assets/vendors/css/forms/selects/select2.min.css') }}">
	<link rel="stylesheet" href="{{ asset('theme_admin/app-assets/vendors/css/editors/summernote.css') }}">
	<link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/plugins/extensions/toastr.css') }}">
	<link rel="stylesheet" href="{{ asset('theme_admin/plugins/lightbox/dist/css/lightbox.min.css') }}">
	<link rel="stylesheet" href="{{ asset('theme_admin/plugins/waitMe/waitMe.min.css') }}">
	<link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/app.css') }}">
	<link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/core/menu/menu-types/vertical-menu.css') }}">
	<link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/core/colors/palette-gradient.css') }}">
	<link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/plugins/forms/wizard.css') }}">
	<link rel="stylesheet" href="{{ asset('theme_admin/app-assets/vendors/css/extensions/nouislider.min.css') }}">

	<link rel="stylesheet" href="{{ asset('theme_admin/assets/css/style.css') }}">
	<link rel="stylesheet" href="{{ asset('theme_admin/plugins/dropzone/dropzone.min.css') }}">
	<link href="https://transloadit.edgly.net/releases/uppy/v1.21.2/uppy.min.css" rel="stylesheet">

	<script src="{{ asset('theme_admin/app-assets/vendors/js/vendors.min.js') }}"></script>
	<script src="{{ asset('theme_admin/app-assets/vendors/js/extensions/toastr.min.js') }}"></script>
	<script src="{{ asset('theme_admin/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
	<script src="{{ asset('theme_admin/app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js') }}"></script>
	<script src="{{ asset('theme_admin/app-assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js') }}"></script>
	<script src="{{ asset('theme_admin/app-assets/vendors/js/tables/jszip.min.js') }}"></script>
	<script src="{{ asset('theme_admin/app-assets/vendors/js/tables/pdfmake.min.js') }}"></script>
	<script src="{{ asset('theme_admin/app-assets/vendors/js/tables/vfs_fonts.js') }}"></script>
	<script src="{{ asset('theme_admin/app-assets/vendors/js/tables/buttons.html5.min.js') }}"></script>
	<script src="{{ asset('theme_admin/app-assets/vendors/js/tables/buttons.print.min.js') }}"></script>
	<script src="{{ asset('theme_admin/app-assets/vendors/js/tables/buttons.colVis.min.js') }}"></script>
	<script src="{{ asset('theme_admin/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
	<script src="{{ asset('theme_admin/app-assets/vendors/js/editors/summernote/summernote.js') }}"></script>
	<script src="{{ asset('theme_admin/app-assets/vendors/js/charts/chart.min.js') }}"></script>
	<script src="{{ asset('theme_admin/app-assets/vendors/js/editors/ckeditor/ckeditor.js') }}"></script>
	<script src="{{ asset('theme_admin/plugins/lightbox/dist/js/lightbox.min.js') }}"></script>
	<script src="{{ asset('theme_admin/plugins/waitMe/waitMe.min.js') }}"></script>
	<script src="{{ asset('theme_admin/plugins/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
	<script src="{{ asset('theme_admin/plugins/viewerjs/pdf.js') }}"></script>
	<script src="{{ asset('theme_admin/plugins/viewerjs/pdf.worker.js') }}"></script>
	<script src="{{ asset('theme_admin/app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}"></script>
	<script src="{{ asset('theme_admin/app-assets/vendors/js/extensions/jquery.steps.min.js') }}"></script>
	<script src="{{ asset('theme_admin/app-assets/vendors/js/extensions/nouislider.min.js') }}"></script>
	<script src="{{ asset('theme_admin/plugins/dropzone/dropzone.min.js') }}"></script>

	<style>
		.nowrap {
			white-space: nowrap;
		}

		.hover-link:hover {
				text-decoration: underline;
		}

		.table {
				width: 100% !important;
		}

		#datatable_default > tbody > tr > td {
				text-align: center;
				vertical-align: middle;
		}
	</style>

	<script>
		$(function() {
				$('body').tooltip({ selector: '[data-toggle=tooltip]' });

				$('#datatable_default').DataTable({
						scrollX: true
				});

				$('.summernote').summernote({
						height: 200
				});

				$('.select2').select2({
						placeholder: '-- Pilih --',
						dropdownParent: $('#modal_element')
				});

		});

		function select2LoadAll(selector, endpoint) {
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
								}
						}
				});
		}

		function select2Nested(selector, endpoint, nestedId) {
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
												search: params.term,
												nested_id: nestedId != '' ? nestedId.val() : ''
										};
								},
								processResults: function(data) {
										return {
												results: data.items
										}
								}
						}
				});
		}

		function select2AutoSuggest(selector, endpoint) {
				$(selector).select2({
						placeholder: '-- Pilih --',
						minimumInputLength: 3,
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
								}
						}
				});
		}

		function select2AutoSuggestMultiple(selector, endpoint) {
				$(selector).select2({
						placeholder: '-- Pilih --',
						minimumInputLength: 3,
						allowClear: true,
						multiple: true,
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
						}
				});
		}

		function select2AutoSuggestTags(selector, endpoint) {
			console.log('select2AutoSuggestTags.selector: ' + selector)
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

		const Toast = Swal.mixin({
			toast: true,
			position: 'top-end',
			showConfirmButton: false,
			timer: 3000,
			timerProgressBar: true,
			onOpen: (toast) => {
				toast.addEventListener('mouseenter', Swal.stopTimer)
				toast.addEventListener('mouseleave', Swal.resumeTimer)
			}
		});

		lightbox.option({
			'resizeDuration': 200,
			'wrapAround': true
		});

		function loadingOpen(selector) {
			$(selector).waitMe({
				effect : 'progressBar',
				text : 'Mohon Tunggu ...',
				bg : 'rgba(255,255,255,0.7)',
				color : '#000'
			});
		}

		function loadingClose(selector) {
			$(selector).waitMe('hide');
		}

	</script>
</head>
<body>
<div class="content-body">
<section id="configuration">
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
			</div>
			<div class="card-content collapse show">
				<div class="card-body card-dashboard">
					 <form action="{{ url('publisher/collection/update/' . $collection->id) }}" method="POST" enctype="multipart/form-data" id="form_collection">
				 		<div class="alert alert-danger">
							Untuk ubah cover dan file original, silakan gunakan drag and drop pada form unggah ISBN
						</div>
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
								<a class="nav-link" data-toggle="tab" aria-controls="tab_publisher" href="#tab_publisher" aria-expanded="true">Publisher</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-toggle="tab" aria-controls="tab_contributor" href="#tab_contributor" aria-expanded="false">Kontributor</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-toggle="tab" aria-controls="tab_cover" href="#tab_cover" aria-expanded="false">Cover</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" data-toggle="tab" aria-controls="tab_orginal" href="#tab_orginal" aria-expanded="false">File Original</a>
							</li>
						</ul>
						<div class="tab-content px-1 pt-1">
							<div role="tabpanel" class="tab-pane active" id="tab_general" aria-expanded="true">
								<p>
									<div class="row">
										<div class="col-md-12">
										<div class="form-group">
												<label>Judul :</label>
												<textarea name="title_ori" id="title_ori" class="form-control" placeholder="Masukan judul" readonly="">{{ $collection->title_ori }}</textarea>
											</div>
											<div class="form-group">
												<label>Perubahan Judul :</label>
												<textarea name="title" id="title" class="form-control" placeholder="Masukan judul" >{{ $collection->title }}</textarea>
											</div>
											<div class="form-group">
												<label>ISBN :</label>
												<input type="text" class="form-control" value="{{ $collection->code }}" disabled>
											</div>
											<div class="form-group">
												<label>Bulan Terbit :</label>
												<select name="publication_month" id="publication_month" class="form-control" >
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
											<div class="form-group">
												<label>Tahun Terbit :</label>
												<input type="text" name="publication_year" id="publication_year" class="form-control" placeholder="Masukan tahun terbit" value="{{ $collection->publication_year }}" >
											</div>
											<div class="form-group">
												<label>Tempat Terbit :</label>
												<select name="city_id" id="city_id" class="form-control" style="width:100%;" >
													<option value="{{ $collection->city? $collection->city->id : 0 }}">{{ $collection->city ? $collection->city->name : "Lengkapi Profil Anda" }}</option>
												</select>
											</div>
											<div class="form-group">
												<label>Total Halaman :</label>
												<div class="input-group mb-2">
													<input type="number" name="total_page" id="total_page" class="form-control" placeholder="Masukan total halaman" value="{{ $collection->physicalDescription() ? $collection->physicalDescription()->total_page : '' }}" >
													<div class="input-group-prepend">
														<div class="input-group-text">Halaman</div>
													</div>
												</div>
											</div>
											@if($collection->physicalDescription() && isset($collection->physicalDescription()->ilustration))
											<div class="form-group">
												<label>Ilustrasi :</label>
												<select name="ilustration" id="ilustration" class="form-control" >
													<option value="">-- Pilih Ilustrasi --</option>
													<option value="Ya" {{ $collection->physicalDescription() && $collection->physicalDescription()->ilustration == 'Ya' ?  'selected' : '' }}>Ya</option>
													<option value="Tidak" {{ $collection->physicalDescription() && $collection->physicalDescription()->ilustration == 'Tidak' ?  'selected' : '' }}>Tidak</option>
												</select>
											</div>
											@endif
											<div class="form-group">
												<label>Kategori :</label>
												<div class="row no-gutters">
													@foreach($category as $c)
													@php $exist = $collection->collectionCategory->where('category_id', $c->id)->count() @endphp
													<div class="col-md-3 custom-control custom-checkbox mr-1 category-checkbox"><input type="checkbox" id="checkbox-{{ $c->id }}" class="custom-control-input" name="category[]" data-name="{{ $c->name }}" value="{{ $c->id }}" {{ $exist > 0 ? 'checked' : '' }}><label class="custom-control-label" style="margin-left: 20px;" for="checkbox-{{ $c->id }}">{{ $c->name }}</label></div>
													@endforeach
												</div>
												</div>
											<div class="form-group">
												<label>Subjek :</label>
												<select name="collection_subject[]" id="collection_subject" class="form-control" style="width:100%;" multiple >
													@foreach($collection->collectionSubject as $cs)
														<option value="{{ $cs->subject->name }}" selected>{{ $cs->subject->name }}</option>
													@endforeach
												</select>
											</div>
											<div class="form-group">
												<label>Keterangan :</label>
												<textarea name="description" id="description" class="form-control" style="resize:none;" placeholder="Masukan informasi lain" >{{ $collection->description }}</textarea>
											</div>
										</div>
									</div>
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
															<label>Preview :</label>
															<input type="text" name="preview" id="preview" class="form-control" placeholder="Ex: 1-3" value="{{ $collection->preview }}">
													</div>
												</div>
											</div>
										</div>
								</p>
							</div>
							<div class="tab-pane" id="tab_publisher">
								<div class="form-group">
										<label>Nama Pelaksana</label>
										<input type="text" value="{{ $publisher->name }}" name="publisher_name" id="publisher_name" class="form-control required" placeholder="Nama Pelaksana" value="{{ $publisher->name }}" readonly="">
								</div>
								<div class="form-group">
										<label>Alamat Pelaksana</label>
										<input type="text" value="{{ $publisher->address }}"  name="publisher_address" id="publisher_address" class="form-control required" placeholder="Alamat Pelaksana" value="{{ $publisher->address }}">
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
							<div class="tab-pane" id="tab_contributor">
								<p>
									<table class="table table-bordered table-striped">
										<tbody id="data_contributor">
											@foreach($collection->collectionContributor as $cc)
												<tr>
													<td class="align-middle">
														<select name="contributor_contributor_id_field[]" class="form-control">
															@foreach($contributor as $c)
																<option value="{{ $c->id }}" {{ $c->id == $cc->contributor_id ? 'selected' : '' }}>{{ $c->name }}</option>
															@endforeach
														</select>
													</td>
													<td class="align-middle">
														<input type="text" name="contributor_fullname_field[]" class="form-control" value="{{ $cc->author->fullname }}" placeholder="Nama">
													</td>
													<td class="align-middle">
														<input type="text" name="contributor_title_field[]" class="form-control" value="{{ $cc->author->title }}" placeholder="Titel">
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
							</div>
							<div class="tab-pane" id="tab_cover">
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
											<a href="{{ url('/collection/cover') . '/' . $cover->id }}" data-lightbox="{{ $collection->title }}" data-title="{{ $collection->title }}"><img src="{{ url('/collection/cover') . '/' . $cover->id }}" style="max-width:242px; max-height:280px;"></a>
										</center>
									@else
										<div class="alert alert-danger text-center">Tidak ada file!</div>
									@endif
								</div>
							</div>
							<div class="tab-pane" id="tab_orginal">
								<div class="form-group">
									@php $original = $collection->collectionMedia->where('type', 2)->first(); @endphp
									@if($original)
										<div class="row justify-content-center">
											<div class="col-md-6">
												<div class="alert alert-warning alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
													<span class="alert-icon"><i class="la la-info-circle"></i></span>
													<ul>
														<li>Ukuran: <b>{{ App\Helper\GeneralHelper::formatSize($original->size) }}</b></li>
														<li>Ekstensi: <b>{{ $original->extension }}</b></li>
														<li>Mime: <b>{{ $original->mimes }}</b></li>
														<li>Hash: <b>{{ $original->hash }}</b></li>
														<li>Metode: <b>{{ $original->method() }}</b></li>
													</ul>
												</div>
											</div>
										</div>
									@else
										<div class="alert alert-danger text-center">Tidak ada file!</div>
									@endif
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

		$('#data_contributor').on('click', '#remove_row_contributor', function() {
			$(this).closest('tr').remove();
		});

		$('input:radio[name="status"]').click(function() {
			if($(this).is(':checked') && $(this).val() == 3) {
				$('#form_problem').fadeIn(200);
			} else {
				$('#form_problem').fadeOut(200);
			}
		});

		$('.carousel').each(function(){
				$(this).carousel({
						interval: false
				});
		});

		select2AutoSuggestTags('#collection_subject', 'load_subject');
		select2Nested('#publisher_province', 'load_province', '');
		select2Nested('#publisher_city', 'load_city', $('#publisher_province'));
		select2Nested('#publisher_district', 'load_district', $('#publisher_city'));
		select2Nested('#publisher_village', 'load_village', $('#publisher_district'));

		var provinceId = "{{ $publisher->province_id }}"

		if(provinceId != "") {
			var province = {
					id: "{{ $publisher->province_id }}",
					text: "{{ $publisher->province != '' ? $publisher->province->name : '' }}"
			}

			var newOption = new Option(province.text, province.id, false, false);
			$('#publisher_province').append(newOption).trigger('change');
		}

		var cityId = "{{ $publisher->city_id }}"

		if(cityId != "") {
			var city = {
					id: "{{ $publisher->city_id }}",
					text: "{{ $publisher->city != '' ? $publisher->city->name : '' }}"
			}

			var newOption = new Option(city.text, city.id, false, false);
			$('#publisher_city').append(newOption).trigger('change');
		}

		var districtId = "{{ $publisher->district_id }}"

		if(districtId != "") {
			var district = {
					id: "{{ $publisher->district_id }}",
					text: "{{ $publisher->district != '' ? $publisher->district->name : '' }}"
			}

			var newOption = new Option(district.text, district.id, false, false);
			$('#publisher_district').append(newOption).trigger('change');
		}

		var villageId = "{{ $publisher->village_id }}"

		if(villageId != "") {
			var village = {
					id: "{{ $publisher->village_id }}",
					text: "{{ $publisher->village != '' ? $publisher->village->name : '' }}"
			}

			var newOption = new Option(village.text, village.id, false, false);
			$('#publisher_village').append(newOption).trigger('change');
		}
	});

	var total_image = 1;

	$(function() {
			loadPdfImage();
		});

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
					$('#lblHal').html(page);
					$('#lblTotal').html(response.total_data);
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

	function addElementContributor() {
		$('#data_contributor').append(`
			<tr>
				<td class="align-middle">
					<select name="contributor_contributor_id_field[]" class="form-control">
						@foreach($contributor as $c)
							<option value="{{ $c->id }}">{{ $c->name }}</option>
						@endforeach
					</select>
				</td>
				<td class="align-middle">
					<input type="text" name="contributor_fullname_field[]" class="form-control" placeholder="Nama">
				</td>
				<td class="align-middle">
					<input type="text" name="contributor_title_field[]" class="form-control" placeholder="Titel">
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
	}
</script>
<script src="{{ asset('theme_admin/app-assets/js/core/app-menu.js') }}"></script>
	<script src="{{ asset('theme_admin/app-assets/js/core/app.js') }}"></script>
	<script src="{{ asset('theme_admin/app-assets/js/scripts/customizer.js') }}"></script>
</body>
</html>
