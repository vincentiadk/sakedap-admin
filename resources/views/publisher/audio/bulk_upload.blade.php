<div class="app-content content">
	<div class="content-wrapper">
			<div class="content-header row">
					<div class="content-header-left col-md-6 col-12 mb-2">
							<h3 class="content-header-title mb-1 d-inline-block">{{ $data['title'] }}</h3><br>
							<div class="row breadcrumbs-top d-inline-block">
									<div class="breadcrumb-wrapper col-12">
											<ol class="breadcrumb">
													<li class="breadcrumb-item"><a href="{{ url('publisher/dashboard') }}">Dashboard</a></li>
													<li class="breadcrumb-item"><a href="#">Audio</a></li>
													<li class="breadcrumb-item active">Upload Bulk</li>
											</ol>
									</div>
							</div>
					</div>
					<div class="content-header-right col-md-6 col-12 mb-2 mt-1">
							<div class="float-md-right">
									<div class="form-group">
										<p><a type="button" class="btn btn-info rounded-circle" href="{{ url('main/panduan-bulk.pdf') }} "><i class="la la-question"></i></a>
										<a href="{{ url('template/audio/audio_bulk.zip') }}" class="btn btn-success">Download Template</a></p>
									</div>
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
				@elseif(session('failed'))
					<div class="alert bg-danger alert-icon-left alert-dismissible mb-2" role="alert">
							<span class="alert-icon"><i class="la la-times"></i></span>
							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
									<span aria-hidden="true">&times;</span>
							</button>
							<strong>Success!</strong> {{ session('failed') }}
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
							</div>
							<div class="card-content collapse show">
								<div class="card-body">
									<form id="form_data" class="steps-validation wizard-circle">
										<!-- Step 1 -->
										<h6>Data Publisher</h6>
										<fieldset>
											<div class="col-md-12">
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
										</fieldset>
										<h6>Upload File</h6>
										<fieldset>
											<input type="hidden" name="file_zip" id="file_upload">
											<div class="form-group">
												<div id="drag-drop-area"></div>
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
		@include('publisher.collection.list_job')
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {

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
					// Allways allow previous action even if the current form is not valid!
					if (currentIndex > newIndex)
					{
						if(newIndex == 1) {
							return false;
						}
						return true;
					}

					var valid = true;

					if(currentIndex == 0) {
						if($('#publisher_address').val() == "") {
							console.log('#publisher_address', '')
							valid = false;
						}

						console.log("$('#publisher_province').val()", $('#publisher_province').val())
						if($('#publisher_province').val() == '') {
							console.log('#publisher_province', '')
							valid = false;
						}

						console.log("$('#publisher_city').val()", $('#publisher_city').val())
						if($('#publisher_city').val() == '') {
							console.log('#publisher_city', '')
							valid = false;
						}

						console.log("$('#publisher_district').val()", $('#publisher_district').val())
						if($('#publisher_district').val() == '') {
							console.log('#publisher_district', '')
							valid = false;
						}

						console.log("$('#publisher_village').val()", $('#publisher_village').val())
						if($('#publisher_village').val() == '') {
							console.log('#publisher_village', '')
							valid = false;
						}

						if(!valid) {
							Swal.fire({
										position: 'center',
										icon: 'warning',
										title: 'Harap mengisi semua data',
										showConfirmButton: true
								});
						}

						return valid;
					}

					return true;
			},
			onStepChanged: function (event, currentIndex, priorIndex) {

			},
			onFinishing: function (event, currentIndex)
			{
				if($('#file_upload').val() == "") {
					Swal.fire({
								position: 'center',
								icon: 'warning',
								title: 'Harap mengisi semua data',
								showConfirmButton: true
					});
					return false
				} else {
					create()
				}
			}
		});


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
	});

	function create() {
		$.ajax({
				url: '{{ url("publisher/collection/import/") }}' + '/' + '{{ $data["typeId"] }}',
				type: 'POST',
				dataType: 'JSON',
				data: new FormData($('#form_data')[0]),
				cache: false,
				contentType: false,
				processData: false,
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
						if(response.status == 200) {
								Toast.fire({
										icon: 'success',
										title: response.message
								});
								location.reload(true);
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
						} else {
								Toast.fire({
										icon: 'warning',
										title: response.message
								});
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
</script>
<script src="https://transloadit.edgly.net/releases/uppy/v3.17.0/uppy.min.js"></script>
<script>
	$(document).ready(function() {
		const date = Date.now();
		const fileName = 'audio_collection_' + "{{ $data['publisher']->id }}_" + date + '.zip'
		const uppy = Uppy.Core(
			{
				restrictions: {
					maxNumberOfFiles: 1,
					allowedFileTypes: ['.zip'],
				},
				onBeforeFileAdded: (currentFile, files) => {
					currentFile.name = fileName
					return currentFile
				}
			})
			.use(Uppy.Dashboard, {
				inline: true,
				width: '100%',
				target: '#drag-drop-area'
			})
			.use(Uppy.Tus, {
				endpoint: "{{ url('tus/') }}",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			})

			uppy.on('complete', (result) => {
				$('#file_upload').val(result.successful[0].name)
			})
	})
</script>
