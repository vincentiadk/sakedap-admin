<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">Tambah Data</h4>
            <a href="#page-header" class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto" data-bs-toggle="collapse">
                <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
            </a>
        </div>
    </div>
    <div class="page-header-content d-lg-flex border-top">
        <div class="d-flex">
            <div class="breadcrumb py-2">
                <a href="{{ url('home') }}" class="breadcrumb-item"><i class="ph-house"></i></a>
                <a href="javascript:void(0);" class="breadcrumb-item">Pengelola</a>
                <span class="breadcrumb-item active">Tambah Data</span>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="alert alert-danger d-none" id="validation-element">
        <ul class="mb-0" id="validation-data"></ul>
    </div>
    <form id="form-data">
        <div class="card">
            <div class="card-header">
                <h5 class="hstack gap-2 mb-0">Informasi</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Kategori :</label>
                            <select class="form-select select2-basic" name="category_id" id="category_id" data-placeholder="Tidak Ada">
                                <option value="">Tidak Ada</option>
                                @foreach($category as $c)
                                    <option value="{{ $c->ID }}">{{ $c->NAME }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nama : <span class="text-danger fw-bold">*</span></label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="....................">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Lembaga Penaung :</label>
                            <input type="text" class="form-control" name="shelter_institution" id="shelter_institution" placeholder="....................">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Admin :</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="admin" id="admin" placeholder="....................">
                                <span class="input-group-text">Alternatif</span>
                                <input type="text" class="form-control" name="admin_alternative" id="admin_alternative" placeholder="....................">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email :</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="email" id="email" placeholder="....................">
                                <span class="input-group-text">Alternatif</span>
                                <input type="text" class="form-control" name="email_alternative" id="email_alternative" placeholder="....................">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Telepon :</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="phone" id="phone" placeholder="....................">
                                <span class="input-group-text">Alternatif</span>
                                <input type="text" class="form-control" name="phone_alternative" id="phone_alternative" placeholder="....................">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Fax :</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="fax" id="fax" placeholder="....................">
                                <span class="input-group-text">Alternatif</span>
                                <input type="text" class="form-control" name="fax_alternative" id="fax_alternative" placeholder="....................">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Jenis :</label>
                            <select class="form-select select2-basic" name="type_id" id="type_id" data-placeholder="Tidak Ada">
                                <option value=""></option>
                                @foreach($type as $t)
                                    <option value="{{ $t->ID }}">{{ $t->NAME }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Induk :</label>
                            <select class="form-select select2-basic" name="parent_id" id="parent_id" data-placeholder="Tidak Ada"></select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Gedung :</label>
                            <input type="text" class="form-control" name="building" id="building" placeholder="....................">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Lokasi : <span class="text-danger fw-bold">*</span></label>
                            <select class="form-select select2-basic" name="location_id" id="location_id" data-placeholder="Tidak Ada"></select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Kode Pos :</label>
                            <input type="text" class="form-control" name="postal_code" id="postal_code" placeholder="....................">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Alamat :</label>
                            <input type="text" class="form-control" name="address" id="address" placeholder="....................">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Website :</label>
                                    <input type="text" class="form-control" name="website" id="website" placeholder="....................">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Rata - Rata Terbitan :</label>
                                    <input type="number" class="form-control" name="publication_average" id="publication_average" placeholder="....................">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="hstack gap-2 mb-0">File Akta <span class="text-danger fw-bold">*</span></h5>
                    </div>
                    <div class="card-body">
                        <input type="file" name="file_deed" id="file_deed">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="hstack gap-2 mb-0">File Pernyataan <span class="text-danger fw-bold">*</span></h5>
                    </div>
                    <div class="card-body">
                        <input type="file" name="file_statement" id="file_statement">
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="card">
        <div class="card-body">
            <div class="text-end">
                <button type="button" class="btn btn-primary" onclick="submitted()">
                    <i class="ph-plus me-1"></i>
                    Tambah Data
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        select2Serverside('#parent_id', 'publisher');
        select2Serverside('#location_id', 'location');

        dragAndDropFile('#file_deed', {
            maxFileCount: 1,
            autoReplace: true,
            allowedFileExtensions: ['pdf']
        });

        dragAndDropFile('#file_statement', {
            maxFileCount: 1,
            autoReplace: true,
            allowedFileExtensions: ['pdf']
        });
    });

    function clearValidation() {
        $('#validation-element').addClass('d-none');
        $('#validation-data').html('');
    }

    function showValidation(data) {
        $('#validation-element').removeClass('d-none');
        $('#validation-data').html('');

        $.each(data, function(index, value) {
            $('#validation-data').append('<li>' + value + '</li>');
        });
    }

    function submitted() {
        $.ajax({
            url: '{{ url("manager/create-data/submitted") }}',
            type: 'POST',
            dataType: 'JSON',
            data: new FormData($('#form-data')[0]),
            contentType: false,
            processData: false,
            cache: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            beforeSend: function() {
                onLoading('show', 'body');
                clearValidation();
            },
            success: function(response) {
                onLoading('close', 'body');

                if(response.code == 200) {
                    swalInit.fire({
                        title: 'Berhasil',
                        text: response.message,
                        icon: 'success',
                        showDenyButton: false,
                        showCancelButton: false,
                        confirmButtonText: 'Oke',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            onLoading('show', 'body');

                            location.href = '{{ url("manager/create-data") }}';
                        }
                    });
                } else if(response.code == 400) {
                    onLoading('close', 'body');
                    $('.btn-to-top button').click();
                    showValidation(response.error);
                } else {
                    swalInit.fire({
                        title: 'Oops ...',
                        text: response.message,
                        icon: 'info',
                        showCloseButton: true
                    });
                }
            },
            error: function(response) {
                onLoading('close', 'body');

                swalInit.fire({
                    html: '<b>' + response.responseJSON.exception + '</b><br>' + response.responseJSON.message,
                    icon: 'error',
                    showCloseButton: true
                });
            }
        });
    }
</script>
