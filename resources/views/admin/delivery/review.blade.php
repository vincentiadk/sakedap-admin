<div class="app-content content">
    <div class="content-wrapper">
            <div class="content-header row">
                    <div class="content-header-left col-md-6 col-12 mb-2">
                            <h3 class="content-header-title mb-1 d-inline-block">Pengiriman KC dan KR Analog</h3><br>
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
                                <h4 class="card-title text-center">Pengiriman KC dan KR Analog</h4>
                                <a class="heading-elements-toggle"><i class="la la-ellipsis-h font-medium-3"></i></a>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                        <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
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
                                    <form id="form_data">
                                        <!-- Step 1 -->
                                        <h6>Informasi Pengiriman</h6>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-bordered">
                                                        <tbody>
                                                            <tr>
                                                                <td class="align-middle w-20 font-weight-bold">Penerbit</td>
                                                                <td class="align-middle">{{ $delivery->publisher->name }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-middle w-20 font-weight-bold">Tgl Kirim</td>
                                                                <td class="align-middle">{{ $delivery->delivery_date }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-middle w-20 font-weight-bold">Ekspedisi</td>
                                                                <td class="align-middle">{{ $delivery->expedition->name }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-middle w-20 font-weight-bold">No Resi</td>
                                                                <td class="align-middle">{{ $delivery->receipt_no }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="align-middle w-20 font-weight-bold">Tgl Terima</td>
                                                                <td class="align-middle">
                                                                    <input type="hidden" name="delivery_id" id="delivery_id" class="form-control" value="{{$delivery->id}}">
                                                                    <input type="date" name="accepted_date" id="accepted_date" class="form-control" value="">
                                                                </td>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                {{-- <div class="form-group">
                                                    <label>Tanggal Terima</label>
                                                    <input type="date" name="delivery_date" id="delivery_date" class="form-control" value="">
                                                </div> --}}
                                            </div>
                                        </div>
                                        <hr>
                                        <h6>Data Koleksi</h6>
                                        <div class="table-responsive" style="overflow-x: auto;">
                                        <table class="table table-striped table-bordered" id="datatable_form">
                                            <thead class="text-center">
                                            <tr>
                                                <th style="min-width: 300px">Aksi</th>
                                                <th>Cover</th>
                                                <th>ISBN</th>
                                                <th>Jenis KCKR</th>
                                                <th style="min-width: 300px">Judul</th>
                                                <th>Pengarang</th>
                                                <th>Tahun Terbit</th>
                                                <th>Deskripsi Fisik</th>
                                                <th style="min-width: 300px">Ringkasan</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($delivery->collectionCopy as $key =>$value)
                                               <tr>
                                                    <input type="hidden" name="collection_copy[{{$key}}][id]" value="{{$value->id}}">
                                                    <td>
                                                        <div>
                                                            <input type="radio" name="collection_copy[{{$key}}][status]" class="status_{{$key}}" id="accept{{$key}}" value="accept" onchange="handleRadioStatus({{$key}})">
                                                            <label for="accept{{$key}}">Diterima</label>

                                                            <input type="radio" name="collection_copy[{{$key}}][status]" class="status_{{$key}}" id="reject{{$key}}" value="reject" onchange="handleRadioStatus({{$key}})">
                                                            <label for="reject{{$key}}">Ditolak</label>
                                                        </div>
                                                        <div id="status_accept{{$key}}" class="form-control mt-1" style="display: none">
                                                            <input type="radio" name="collection_copy[{{$key}}][status_accept]" class="status_accept_{{$key}}" id="status_accept_1{{$key}}" value="1">
                                                            <label for="status_accept_1{{$key}}">Sangat Baik</label>

                                                            <input type="radio" name="collection_copy[{{$key}}][status_accept]" class="status_accept_{{$key}}" id="status_accept_2{{$key}}" value="2">
                                                            <label for="status_accept_2{{$key}}">Baik</label>

                                                            <input type="radio" name="collection_copy[{{$key}}][status_accept]" class="status_accept_{{$key}}" id="status_accept_3{{$key}}" value="3">
                                                            <label for="status_accept_3{{$key}}">Cukup</label>

                                                            <input type="radio" name="collection_copy[{{$key}}][status_accept]" class="status_accept_{{$key}}" id="status_accept_4{{$key}}" value="4">
                                                            <label for="status_accept_4{{$key}}">Rusak</label>
                                                        </div>
                                                        {{-- <select name="collection_copy[{{$key}}][status_reject]" id="status_reject{{$key}}" class="form-control" style="display: none">
                                                            @foreach($problem as $p)
                                                                <option value="{{$p->id}}">{{$p->name}}</option>
                                                            @endforeach
                                                        </select> --}}
                                                        <div id="status_reject{{$key}}" class="form-control mt-1" style="display: none">
                                                            @foreach($problem as $p)
                                                                <div class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" class="status_reject_{{$key}}" id="status_reject_{{$p->id}}_{{$key}}" name="collection_copy[{{$key}}][status_reject][]" value="{{$p->id}}">
                                                                        {{$p->name}}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        @php $cover = $value->collection->collectionMedia->where('type', 1)->first(); @endphp
                                                        @if($cover)
                                                            <center>
                                                                <a href="{{ url('collection/cover') . '/' . $cover->id }}" data-lightbox="Cover Collection" data-title="{{ $cover->collection->title }}"><img src="{{ url('collection/cover') . '/' . $cover->id }}" style="max-height:280px; max-width:242px;"></a>
                                                            </center>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{$value->collection->code}}
                                                        @php $cover = $value->collection->collectionMedia->where('type', 1)->first();
                                                        @endphp
                                                        @if(!empty($cover) && !empty($cover->link))
                                                            <center>
                                                                <img src="{{ asset(Storage::url($cover->link)) }}"  style="max-width:242px; max-height:280px;">
                                                            </center>
                                                        @endif
													</td>
                                                    <td>{{$value->collection->depositHead->shape}}</td>
                                                    <td>{{$value->collection->title}}</td>
                                                    <td>
                                                        @foreach($value->collection->collectionContributor as $c)
                                                            {{$c->author->fullname}}</br>
                                                        @endforeach
                                                    </td>
                                                    <td>{{$value->collection->publication_month . ' - '. $value->collection->publication_year}}</td>
                                                    <td>
                                                        {{ $value->collection->physicalDescription()->total_page ?? '-' }}
                                                        Hal,
                                                        {{ $value->collection->physicalDescription()->dimension ?? '-' }}
                                                        Cm
                                                    </td>
                                                    <td>
                                                        <p class="short-description">
                                                            {{ \Illuminate\Support\Str::limit($value->collection->description, $limit = 200, $end = '...') }}
                                                        </p>
                                                        <p class="full-description" style="display: none;">
                                                            {{ $value->collection->description }}
                                                        </p>
                                                        <a href="#" class="toggle-description">Show More</a>
                                                    </td>

                                                </tr>
												@endforeach
                                            </tbody>
                                        </table>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                            <div class="form-group text-right">
                                                <button type="submit" class="btn btn-success"><i class="la la-save"></i> Simpan</button>
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

<script type="text/javascript">

var tableForm;
var tableIdx = 0;

$(document).ready(function() {

    $('#form_data').validate({
		errorClass: 'is-invalid',
		errorPlacement: function(error, element) {
			error.appendTo(element.next('.error'));
			error.attr('style', 'color: red; font-size: 12px; margin-top: 5px;');
		},
		submitHandler: function(form) {
			submit();
		}
    });

    $('#datatable_form').DataTable({
        paging: false
    });

    $('.toggle-description').click(function(event) {
        event.preventDefault();
        var shortDescription = $(this).siblings('.short-description');
        var fullDescription = $(this).siblings('.full-description');
        if (shortDescription.is(':visible')) {
            shortDescription.hide();
            fullDescription.show();
            $(this).text('Show Less');
        } else {
            shortDescription.show();
            fullDescription.hide();
            $(this).text('Show More');
        }
    });

});

function handleRadioStatus(index) {
    var selectedValue = $("input[name='collection_copy[" + index + "][status]']:checked").val();

    if (selectedValue == "accept") {
        $("#status_accept" + index).show();
        $("#status_reject" + index).hide();
    } else if (selectedValue == "reject") {
        $("#status_accept" + index).hide();
        $("#status_reject" + index).show();
    } else {
        $("#status_accept" + index).hide();
        $("#status_reject" + index).hide();
    }
}

function submit() {
    let isValid = true;

    for (let index = 0; index < {{count($delivery->collectionCopy)}}; index++) {

        const isChecked = $('.status_'+index+':checked').length > 0;

        if (!isChecked) {
            isValid = false;
        }

        const value = $('.status_'+index+':checked').val();
        if (value == "accept") {
            if ($('.status_accept_'+index+':checked').length === 0) {
                isValid = false;
            }
        } else {
            if ($('.status_reject_'+index+':checked').length === 0) {
                isValid = false;
            }
        }

    };

    if (!isValid) {
        Toast.fire({
            icon: 'warning',
            title: "Harus memverifikasi semua exemplar!"
        });
        return false;
    }


	var formData = new FormData($('#form_data')[0]);
	$.ajax({
			url: '{{ url("admin/collection/delivery/accept") }}' + '/' + $("#delivery_id").val(),
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
							window.location.href = "{{ url('admin/collection/delivery') }}";
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

</script>

@include('publisher.collection.script-serial')
