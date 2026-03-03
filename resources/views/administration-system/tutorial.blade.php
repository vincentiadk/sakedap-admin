<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Administrasi Sistem - <span class="fw-normal">Tutorial</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <button type="button" class="btn btn-primary" onclick="onCreate()">
                    <i class="ph-file-video me-1"></i>
                    Tambah Tutorial
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
                    <i class="ph-file-video me-1 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Daftar Tutorial</h6>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="input-group" style="width: auto;">
                        <span class="input-group-text">
                            <i class="ph-funnel"></i>
                        </span>
                        <select class="form-select" name="filter_category" id="filter_category" onchange="loadData()" style="min-width: 200px;">
                            <option value="">Semua Kategori</option>
                            @foreach($category as $c)
                                <option value="{{ $c->ID }}">{{ $c->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary" id="total-records">
                        <i class="ph-list-checks me-1"></i>
                        <span id="record-count">0</span> Data
                    </span>
                </div>
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
                            <th class="text-center text-nowrap" style="min-width: 150px">
                                <i class="ph-file-video me-1"></i>
                                Media
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-tag me-1"></i>
                                Kategori
                            </th>
                            <th class="text-nowrap" style="min-width: 250px">
                                <i class="ph-text-aa me-1"></i>
                                Judul
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 80px">
                                <i class="ph-translate me-1"></i>
                                Lang
                            </th>
                            <th class="text-nowrap" style="min-width: 200px">
                                <i class="ph-link me-1"></i>
                                Lampiran Link
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 120px">
                                <i class="ph-flag me-1"></i>
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
                <h5 class="modal-title fw-semibold">
                    <i class="ph-file-video me-2"></i>
                    <span id="modal-title-text"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger border-0 d-none" id="validation-element">
                    <div class="d-flex align-items-center">
                        <i class="ph-warning-circle me-2 fs-4"></i>
                        <div class="flex-fill">
                            <h6 class="alert-heading mb-2">Terdapat Kesalahan Input</h6>
                            <ul class="mb-0" id="validation-data"></ul>
                        </div>
                    </div>
                </div>
                <form id="form-data" class="form-ajax">
                    <input type="hidden" name="table_id" id="table_id">
                    <div class="form-group">
                        <label class="form-label fw-semibold">
                            Upload Media
                        </label>
                        <input type="file" class="form-control" name="media" id="media" accept="video/*,audio/*,.pdf,.png,.jpg,.jpeg">
                    </div>
                    <div id="media-preview-container" class="d-none">
                        <label class="form-label fw-semibold">Media Saat Ini</label>
                        <div class="border rounded p-3 bg-white">
                            <a href="" class="btn btn-success" id="media-preview" target="_blank">
                                <i class="ph-file me-1"></i>
                                Lihat / Download Media
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ph-tag me-1"></i>
                                    Kategori Tutorial
                                </label>
                                <select class="form-select select2-basic" name="category_id" id="category_id" data-dropdown-parent="#modal-form" data-placeholder="Pilih kategori tutorial">
                                    <option value=""></option>
                                    @foreach($category as $c)
                                        <option value="{{ $c->ID }}">{{ $c->NAME }} | Halaman Statis : {{ $c->PAGES == 1 ? 'Ya' : 'Tidak' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ph-link me-1"></i>
                                    Lampiran Link
                                </label>
                                <input type="url" class="form-control" name="attachment_link" id="attachment_link" placeholder="https://example.com">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ph-translate me-1"></i>
                                    Bahasa
                                </label>
                                <select class="form-select" name="lang" id="lang">
                                    <option value="ID">Indonesia</option>
                                    <option value="EN">English</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ph-flag me-1"></i>
                                    Status
                                </label>
                                <select class="form-select" name="status" id="status">
                                    <option value="PUBLISH">PUBLISH - Ditampilkan</option>
                                    <option value="HIDE">HIDE - Disembunyikan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ph-text-aa me-1"></i>
                                    Judul Tutorial
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" name="title" id="title" rows="3" placeholder="Masukkan judul tutorial yang jelas dan deskriptif"></textarea>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ph-note me-1"></i>
                                    Ringkasan
                                </label>
                                <textarea class="form-control" name="summary" id="summary" rows="3" placeholder="Ringkasan singkat tentang isi tutorial"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label fw-semibold">
                            <i class="ph-article me-1"></i>
                            Konten Lengkap
                        </label>
                        <textarea class="form-control" name="content" id="content" placeholder="Tulis konten lengkap tutorial di sini..."></textarea>
                    </div>
                    <div class="alert alert-info border-0 bg-info bg-opacity-10">
                        <div class="d-flex">
                            <i class="ph-lightbulb me-2 fs-5"></i>
                            <div>
                                <h6 class="alert-heading mb-1">Tips Membuat Tutorial yang Baik:</h6>
                                <ul class="mb-0 small">
                                    <li>Gunakan judul yang jelas dan spesifik</li>
                                    <li>Sertakan langkah-langkah yang mudah diikuti</li>
                                    <li>Tambahkan screenshot atau video untuk memudahkan pemahaman</li>
                                    <li>Gunakan bahasa yang sederhana dan mudah dipahami</li>
                                    <li>Sertakan contoh praktis jika memungkinkan</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">
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
                    <i class="ph-video me-1"></i>
                    Simpan Tutorial
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        loadData();

        $('#content').summernote({
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });

        $('#media').on('change', function(e) {
            const fileName = e.target.files[0]?.name;

            if (fileName) {
                const fileSize = (e.target.files[0].size / 1024 / 1024).toFixed(2);

                if (!$('#media-info').length) {
                    $(this).parent().after(`
                        <div id="media-info" class="alert alert-success border-0 mt-2 mb-0">
                            <div class="d-flex align-items-center">
                                <i class="ph-check-circle me-2"></i>
                                <div>
                                    <strong>File dipilih:</strong> ${fileName} (${fileSize} MB)
                                </div>
                            </div>
                        </div>
                    `);
                } else {
                    $('#media-info').html(`
                        <div class="d-flex align-items-center">
                            <i class="ph-check-circle me-2"></i>
                            <div>
                                <strong>File dipilih:</strong> ${fileName} (${fileSize} MB)
                            </div>
                        </div>
                    `);
                }
            }
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
        $('#media-preview').attr('href', 'javascript:void(0);');
        $('#media-preview-container').addClass('d-none');
        $('#media-info').remove();
        $('#status').val('PUBLISH');
        $('#lang').val('ID');
        $('#category_id').val('').change();
        $('#content').summernote('code', '');
    }

    function onCreate() {
        onReset();

        $('#modal-title-text').text('Tambah Data Tutorial');
        $('#modal-form').modal('show');
    }

    function onCancel() {
        onReset();
    }

    function onUpdate() {
        onReset();

        $('#btn-create').addClass('d-none');
        $('#btn-update').removeClass('d-none');
        $('#btn-cancel').removeClass('d-none');
        $('#modal-title-text').text('Edit Data Tutorial');
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

    function updateRecordCount(count) {
        $('#record-count').text(count || 0);
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
                url: '{{ url("administration-system/tutorial/datatable") }}',
                dataType: 'JSON',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
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
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-center' },
            ],
            initComplete: function (settings, json) {
                var table = this.api();
                const searchInput = $('div.dataTables_filter input');

                searchInput.off().unbind();

                searchInput.on('keyup', debounce(function () {
                    table.search(this.value).draw();
                }, 500));

                updateRecordCount(json.recordsFiltered);
            },
            drawCallback: function(settings) {
                var api = this.api();
                updateRecordCount(api.page.info().recordsFiltered);
            }
        }).on('draw.dt', function() {
            onLoading('close', '#datatable-serverside_wrapper');
        });

        window.gDataTable.columns.adjust().draw();
    }

    function createData() {
        $.ajax({
            url: '{{ url("administration-system/tutorial/create-data") }}',
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
            url: '{{ url("administration-system/tutorial/show-data") }}',
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
                $('#category_id').val(response.KATEGORI_ID).change();
                $('#attachment_link').val(response.LAMPIRAN_LINK);
                $('#summary').val(response.RINGKASAN);

                if(response.FILE_NAME) {
                    var paramFile = {
                        id: response.ID,
                        type: 'file_artikel',
                        filename: response.FILE_NAME,
                        v: '{{ Str::random(40) }}'
                    };

                    $('#media-preview').attr('href', `{{ url("stream-file") }}?${ $.param(paramFile) }`);
                    $('#media-preview-container').removeClass('d-none');
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
            url: '{{ url("administration-system/tutorial/update-data") }}',
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
            text: '<div class="mb-3"><h5 class="text-dark">Hapus Data Tutorial?</h5><span class="text-muted">Data yang telah dihapus tidak bisa dikembalikan lagi</span></div>',
            timeout: false,
            modal: true,
            layout: 'center',
            closeWith: 'button',
            type: 'confirm',
            buttons: [
                Noty.button('Tidak', 'btn btn-light', function () {
                    notyConfirm.close();
                }),
                Noty.button('Hapus', 'btn btn-danger ms-2', function () {
                    $.ajax({
                        url: '{{ url("administration-system/tutorial/destroy-data") }}',
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
