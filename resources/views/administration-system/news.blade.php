<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Administrasi Sistem - <span class="fw-normal">Berita</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <button type="button" class="btn btn-primary" onclick="onCreate()">
                    <i class="ph-plus-circle me-2"></i>
                    Tambah Data
                </button>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="card border-0 shadow-sm">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ph-funnel me-2 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Filter Data</h6>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-lg-6">
                    <label class="form-label fw-semibold">
                        <i class="ph-folder-open me-1"></i>
                        Kategori
                    </label>
                    <select class="form-select" name="filter_category" id="filter_category" onchange="loadData()">
                        <option value="">Semua Kategori</option>
                        @foreach($category as $c)
                            <option value="{{ $c->ID }}">{{ $c->NAME }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-6">
                    <label class="form-label fw-semibold">
                        <i class="ph-file-text me-1"></i>
                        Peruntukan
                    </label>
                    <select class="form-select" name="filter_ownership" id="filter_ownership" onchange="loadData()">
                        <option value="">Semua Peruntukan</option>
                        <option value="1">Halaman Berita</option>
                        <option value="2">Halaman Statis</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ph-newspaper me-2 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Daftar Berita</h6>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary">
                    <i class="ph-article me-1"></i>
                    Manajemen Konten
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered display nowrap w-100" id="datatable-serverside">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center text-nowrap" style="width: 60px">
                                <i class="ph-hash"></i>
                            </th>
                            <th class="text-center text-nowrap" style="width: 100px">
                                <i class="ph-gear"></i>
                                Aksi
                            </th>
                            <th class="text-center text-nowrap" style="width: 100px">
                                <i class="ph-image me-1"></i>
                                Gambar
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-folder-open me-1"></i>
                                Kategori
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 120px">
                                <i class="ph-file-text me-1"></i>
                                Halaman
                            </th>
                            <th class="text-nowrap" style="min-width: 250px">
                                <i class="ph-article me-1"></i>
                                Judul
                            </th>
                            <th class="text-center text-nowrap" style="width: 80px">
                                <i class="ph-globe me-1"></i>
                                Lang
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-link me-1"></i>
                                Lampiran Link
                            </th>
                            <th class="text-center text-nowrap" style="width: 100px">
                                <i class="ph-toggle-right me-1"></i>
                                Status
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="modal-form" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ph-newspaper me-2"></i>
                    <span id="modal-title-text"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger border-0 d-none" id="validation-element">
                    <div class="d-flex align-items-start">
                        <i class="ph-warning-circle me-2 fs-4"></i>
                        <div class="flex-fill">
                            <h6 class="mb-2">Terdapat Kesalahan Validasi!</h6>
                            <ul class="mb-0" id="validation-data"></ul>
                        </div>
                    </div>
                </div>
                <form id="form-data" class="form-ajax">
                    <input type="hidden" name="table_id" id="table_id">
                    <div class="card bg-light border-0 form-group">
                        <div class="card-body">
                            <label class="form-label fw-semibold">
                                <i class="ph-image me-1"></i>
                                Gambar Berita
                            </label>
                            <div class="input-group">
                                <input type="file" class="form-control" name="image" id="image" accept="image/*">
                                <a href="javascript:void(0);" data-lightbox="news-form" data-title="Preview Gambar" class="btn btn-success d-none" id="image-preview">
                                    <i class="ph-eye me-1"></i>
                                    Lihat Gambar Saat Ini
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="form-label fw-semibold">
                                <i class="ph-folder-open me-1"></i>
                                Kategori
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-select select2-basic" name="category_id" id="category_id" data-dropdown-parent="#modal-form" data-placeholder="Pilih Kategori">
                                <option value=""></option>
                                @foreach($category as $c)
                                    <option value="{{ $c->ID }}">{{ $c->NAME }} | Halaman Statis: {{ $c->PAGES == 1 ? 'Ya' : 'Tidak' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label fw-semibold">
                                <i class="ph-link me-1"></i>
                                Lampiran Link
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ph-link-simple"></i>
                                </span>
                                <input type="url" class="form-control" name="attachment_link" id="attachment_link" placeholder="https://example.com">
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label fw-semibold">
                                <i class="ph-globe me-1"></i>
                                Bahasa
                            </label>
                            <select class="form-select" name="lang" id="lang">
                                <option value="ID">Indonesia (ID)</option>
                                <option value="EN">English (EN)</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label fw-semibold">
                                <i class="ph-toggle-right me-1"></i>
                                Status Publikasi
                            </label>
                            <select class="form-select" name="status" id="status">
                                <option value="PUBLISH">Publish (Tampilkan)</option>
                                <option value="HIDE">Hide (Sembunyikan)</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label fw-semibold">
                                <i class="ph-article me-1"></i>
                                Judul Berita
                                <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" name="title" id="title" rows="3" placeholder="Masukkan judul berita yang menarik"></textarea>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label fw-semibold">
                                <i class="ph-note me-1"></i>
                                Ringkasan
                            </label>
                            <textarea class="form-control" name="summary" id="summary" rows="3" placeholder="Ringkasan singkat berita (maks 200 karakter)"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label fw-semibold">
                            <i class="ph-text-align-left me-1"></i>
                            Konten Berita
                        </label>
                        <textarea class="form-control" name="content" id="content" placeholder="Tulis konten berita lengkap di sini..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ph-x me-1"></i>
                    Tutup
                </button>
                <button class="btn btn-danger d-none" id="btn-cancel" onclick="onCancel()">
                    <i class="ph-arrow-counter-clockwise me-1"></i>
                    Batalkan Perubahan
                </button>
                <button class="btn btn-warning d-none" id="btn-update" onclick="updateData()">
                    <i class="ph-floppy-disk me-1"></i>
                    Simpan Perubahan
                </button>
                <button class="btn btn-primary d-none" id="btn-create" onclick="createData()">
                    <i class="ph-plus-circle me-1"></i>
                    Simpan Data
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        loadData();

        $('#content').summernote({
            height: 400,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            placeholder: 'Tulis konten berita lengkap di sini...'
        });
    });

    function onReloadTable() {
        window.gDataTable.ajax.reload(null, false);
    }

    function onReset() {
        clearValidation();

        $('#modal-form').modal('hide');
        $('#form-data').trigger('reset');
        $('#btn-create').removeClass('d-none');
        $('#btn-update').addClass('d-none');
        $('#btn-cancel').addClass('d-none');
        $('#image-preview').attr('href', 'javascript:void(0);');
        $('#image-preview').addClass('d-none');
        $('#status').val('PUBLISH');
        $('#lang').val('ID');
        $('#category_id').val('').trigger('change');
        $('#content').summernote('code', '');
    }

    function onCreate() {
        onReset();

        $('#modal-title-text').text('Tambah Data Berita');
        $('#modal-form').modal('show');
    }

    function onCancel() {
        swalInit.fire({
            title: 'Batalkan Perubahan?',
            text: 'Perubahan yang belum disimpan akan hilang',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Tidak',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                onReset();
            }
        });
    }

    function onUpdate() {
        onReset();

        $('#btn-create').addClass('d-none');
        $('#btn-update').removeClass('d-none');
        $('#btn-cancel').removeClass('d-none');
        $('#modal-title-text').text('Edit Data Berita');
        $('#modal-form').modal('show');
    }

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

    function formSuccess() {
        onReset();
        onReloadTable();
    }

    function loadData() {
        window.gDataTable = $('#datatable-serverside').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            scrollX: true,
            destroy: true,
            order: [[0, 'desc']],
            ajax: {
                url: '{{ url("administration-system/news/datatable") }}',
                dataType: 'JSON',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    ownership: $('#filter_ownership').val(),
                    category: $('#filter_category').val(),
                },
                beforeSend: function() {
                    onLoading('show', '#datatable-serverside_wrapper');
                },
                error: function(response) {
                    onLoading('close', '#datatable-serverside_wrapper');
                    responseError(response);
                }
            },
            columns: [
                { orderable: true, className: 'align-middle text-center fw-semibold' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-center' },
            ],
            initComplete: function (settings, json) {
                var table = this.api();
                const searchInput = $('div.dataTables_filter input');

                searchInput.off().unbind();

                searchInput.on('keyup', debounce(function () {
                    table.search(this.value).draw();
                }, 500));
            },
        }).on('draw.dt', function() {
            onLoading('close', '#datatable-serverside_wrapper');
        });

        window.gDataTable.columns.adjust().draw();
    }

    function createData() {
        $.ajax({
            url: '{{ url("administration-system/news/create-data") }}',
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
                onLoading('show', '.modal-content');
                clearValidation();
            },
            success: function(response) {
                onLoading('close', '.modal-content');

                if(response.code == 200) {
                    formSuccess();
                    notification('success', response.message);
                } else if(response.code == 400) {
                    $('#modal-form .modal-body').scrollTop(0);
                    showValidation(response.error);
                } else {
                    swalInit.fire({
                        title: 'Error',
                        text: response.message,
                        icon: 'error',
                        showCloseButton: false
                    });
                }
            },
            error: function(response) {
                onLoading('close', '.modal-content');
                responseError(response);
            }
        });
    }

    function showDataUpdate(id) {
        $.ajax({
            url: '{{ url("administration-system/news/show-data") }}',
            type: 'GET',
            dataType: 'JSON',
            data: {
                id: id
            },
            beforeSend: function() {
                onLoading('show', '.modal-content');
                onUpdate();
            },
            success: function(response) {
                onLoading('close', '.modal-content');

                $('#table_id').val(response.ID);
                $('#title').val(response.TITLE);
                $('#content').summernote('code', response.CONTENT);
                $('#status').val(response.STATUS);
                $('#lang').val(response.LANG);
                $('#category_id').val(response.KATEGORI_ID).trigger('change');
                $('#attachment_link').val(response.LAMPIRAN_LINK);
                $('#summary').val(response.RINGKASAN);

                if(response.IMAGE) {
                    var paramFile = {
                        id: response.ID,
                        type: 'gambar_artikel',
                        filename: response.IMAGE,
                        v: '{{ Str::random(40) }}'
                    };

                    $('#image-preview').attr('href', `{{ url("stream-file") }}?${ $.param(paramFile) }`);
                    $('#image-preview').removeClass('d-none');
                }
            },
            error: function(response) {
                onLoading('close', '.modal-content');
                responseError(response);
            }
        });
    }

    function updateData() {
        $.ajax({
            url: '{{ url("administration-system/news/update-data") }}',
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
                onLoading('show', '.modal-content');
                clearValidation();
            },
            success: function(response) {
                onLoading('close', '.modal-content');

                if(response.code == 200) {
                    formSuccess();
                    notification('success', response.message);
                } else if(response.code == 400) {
                    $('#modal-form .modal-body').scrollTop(0);
                    showValidation(response.error);
                } else {
                    swalInit.fire({
                        title: 'Error',
                        text: response.message,
                        icon: 'error',
                        showCloseButton: false
                    });
                }
            },
            error: function(response) {
                onLoading('close', '.modal-content');
                responseError(response);
            }
        });
    }

    function destroyData(id) {
        var notyConfirm = new Noty({
            text: '<div class="mb-3"><h5 class="text-dark">Hapus Data Berita?</h5><span class="text-muted">Data yang telah dihapus tidak dapat dikembalikan lagi</span></div>',
            timeout: false,
            modal: true,
            layout: 'center',
            closeWith: 'button',
            type: 'confirm',
            buttons: [
                Noty.button('Batal', 'btn btn-light', function () {
                    notyConfirm.close();
                }),
                Noty.button('Hapus', 'btn btn-danger ms-2', function () {
                    $.ajax({
                        url: '{{ url("administration-system/news/destroy-data") }}',
                        type: 'DELETE',
                        dataType: 'JSON',
                        data: {
                            id: id
                        },
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        beforeSend: function() {
                            onLoading('show', '.noty_bar');
                        },
                        success: function(response) {
                            onLoading('close', '.noty_bar');

                            if(response.code == 200) {
                                notyConfirm.close();
                                onReloadTable();
                                notification('success', response.message);
                            } else {
                                swalInit.fire({
                                    title: 'Error',
                                    text: response.message,
                                    icon: 'error',
                                    showCloseButton: false
                                });
                            }
                        },
                        error: function(response) {
                            onLoading('close', '.noty_bar');
                            responseError(response);
                        }
                    });
                })
            ]
        }).show();
    }
</script>
