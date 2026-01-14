<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pengawasan & Pembinaan - <span class="fw-normal">Tambah Data Pelaksana Serah</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <span class="badge bg-primary p-2 bg-opacity-10 text-primary">
                    <i class="ph-user-plus me-1"></i>
                    Tambah Pelaksana
                </span>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="alert alert-danger border-0 d-none" id="validation-element">
        <div class="d-flex align-items-center mb-2">
            <i class="ph-warning-circle me-2 fs-4"></i>
            <h6 class="mb-0 fw-semibold">Terdapat Kesalahan Validasi</h6>
        </div>
        <ul class="mb-0" id="validation-data"></ul>
    </div>
    <form id="form-data">
        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center">
                    <i class="ph-info me-2 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Informasi Pelaksana Serah</h6>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="ph-tag me-1"></i>
                                    Kategori
                                </label>
                                <select class="form-select select2-basic" name="category_id" id="category_id" data-placeholder="Pilih Kategori">
                                    <option value="">Tidak Ada</option>
                                    @foreach($category as $c)
                                        <option value="{{ $c->ID }}">{{ $c->NAME }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="ph-buildings me-1"></i>
                                    Nama
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Masukkan nama pelaksana serah">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="ph-buildings me-1"></i>
                                    Lembaga Penaung
                                </label>
                                <input type="text" class="form-control" name="shelter_institution" id="shelter_institution" placeholder="Masukkan lembaga penaung">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="ph-user me-1"></i>
                                    Admin
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="admin" id="admin" placeholder="Admin utama">
                                    <span class="input-group-text">
                                        <i class="ph-swap me-1"></i>
                                        Alternatif
                                    </span>
                                    <input type="text" class="form-control" name="admin_alternative" id="admin_alternative" placeholder="Admin alternatif">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="ph-envelope me-1"></i>
                                    Email
                                </label>
                                <div class="input-group">
                                    <input type="email" class="form-control" name="email" id="email" placeholder="email@domain.com">
                                    <span class="input-group-text">
                                        <i class="ph-swap me-1"></i>
                                        Alternatif
                                    </span>
                                    <input type="email" class="form-control" name="email_alternative" id="email_alternative" placeholder="email-alt@domain.com">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="ph-phone me-1"></i>
                                    Telepon
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="phone" id="phone" placeholder="Nomor telepon">
                                    <span class="input-group-text">
                                        <i class="ph-swap me-1"></i>
                                        Alternatif
                                    </span>
                                    <input type="text" class="form-control" name="phone_alternative" id="phone_alternative" placeholder="Nomor telepon alternatif">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="ph-printer me-1"></i>
                                    Fax
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="fax" id="fax" placeholder="Nomor fax">
                                    <span class="input-group-text">
                                        <i class="ph-swap me-1"></i>
                                        Alternatif
                                    </span>
                                    <input type="text" class="form-control" name="fax_alternative" id="fax_alternative" placeholder="Nomor fax alternatif">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="ph-list me-1"></i>
                                    Jenis
                                </label>
                                <select class="form-select select2-basic" name="type_id" id="type_id" data-placeholder="Pilih Jenis">
                                    <option value="">Tidak Ada</option>
                                    @foreach($type as $t)
                                        <option value="{{ $t->ID }}">{{ $t->NAME }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="ph-tree-structure me-1"></i>
                                    Induk
                                </label>
                                <select class="form-select select2-basic" name="parent_id" id="parent_id" data-placeholder="Pilih Induk"></select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="ph-buildings me-1"></i>
                                    Gedung
                                </label>
                                <input type="text" class="form-control" name="building" id="building" placeholder="Masukkan nama gedung">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="ph-map-pin me-1"></i>
                                    Lokasi
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select select2-basic" name="location_id" id="location_id" data-placeholder="Pilih Lokasi"></select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="ph-envelope me-1"></i>
                                    Kode Pos
                                </label>
                                <input type="text" class="form-control" name="postal_code" id="postal_code" placeholder="Masukkan kode pos">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="ph-map-trifold me-1"></i>
                                    Alamat
                                </label>
                                <textarea class="form-control" name="address" id="address" rows="3" placeholder="Masukkan alamat lengkap"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="ph-globe me-1"></i>
                                    Website
                                </label>
                                <input type="url" class="form-control" name="website" id="website" placeholder="https://example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="ph-book me-1"></i>
                                    Rata - Rata Terbitan
                                </label>
                                <input type="number" class="form-control" name="publication_average" id="publication_average" placeholder="Jumlah terbitan">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="ph-file-pdf me-2 text-danger"></i>
                            <h6 class="mb-0 fw-semibold">
                                File Akta
                                <span class="text-danger">*</span>
                            </h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <input type="file" name="file_deed" id="file_deed">
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="ph-file-pdf me-2 text-danger"></i>
                            <h6 class="mb-0 fw-semibold">
                                File Pernyataan
                                <span class="text-danger">*</span>
                            </h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <input type="file" name="file_statement" id="file_statement">
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ url('coaching-supervision/create-executor') }}" class="btn btn-danger">
                    <i class="ph-x me-1"></i>
                    Batal
                </a>
                <button type="button" class="btn btn-primary" onclick="submitted()">
                    <i class="ph-check-circle me-1"></i>
                    Tambah Data
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        if(parseInt('{{ Main::isSuperAdmin() }}') == 0) {
            select2Serverside('#location_id', 'location', {
                province_id: '{{ session("province_id") }}'
            });

            select2Serverside('#parent_id', 'executor', {
                province_id: '{{ session("province_id") }}'
            });
        } else {
            select2Serverside('#parent_id', 'executor');
            select2Serverside('#location_id', 'location');
        }

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
            url: '{{ url("coaching-supervision/create-executor/submitted") }}',
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

                            location.href = '{{ url("coaching-supervision/create-executor") }}';
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
                responseError(response);
            }
        });
    }
</script>
