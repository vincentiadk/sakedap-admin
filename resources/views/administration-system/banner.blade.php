<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Administrasi Sistem - <span class="fw-normal">Banner</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <button type="button" class="btn btn-primary" onclick="onCreate()">
                    <i class="ph-plus-circle me-1"></i>
                    Tambah Banner
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
                    <i class="ph-image me-1 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Daftar Banner</h6>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary" id="total-records">
                    <i class="ph-list-checks me-1"></i>
                    <span id="record-count">0</span> Data
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
                            <th class="text-center text-nowrap" style="min-width: 150px">
                                <i class="ph-image me-1"></i>
                                Gambar
                            </th>
                            <th class="text-nowrap" style="min-width: 180px">
                                <i class="ph-megaphone me-1"></i>
                                Promosi
                            </th>
                            <th class="text-nowrap" style="min-width: 200px">
                                <i class="ph-text-aa me-1"></i>
                                Judul
                            </th>
                            <th class="text-nowrap" style="min-width: 250px">
                                <i class="ph-note me-1"></i>
                                Keterangan
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 120px">
                                <i class="ph-tag me-1"></i>
                                Jenis
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
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">
                    <i class="ph-image me-2"></i>
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
                    <div class="row">
                        <div class="col-12">
                        <h6 class="fw-semibold form-group">
                            <i class="ph-image me-1 text-primary"></i>
                            Gambar Banner
                        </h6>
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                Upload Gambar
                                <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control" name="image" id="image" accept="image/*">
                        </div>
                        <div id="image-preview-container" class="d-none">
                            <label class="form-label fw-semibold">Preview Gambar Saat Ini</label>
                            <div class="border rounded p-2 bg-white">
                                <a href="" data-lightbox="banner-form" data-title="Preview Banner" id="image-preview">
                                    <img src="" class="img-fluid rounded" id="image-preview-img" style="max-height: 200px;">
                                </a>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                <i class="ph-text-aa me-1"></i>
                                Judul Banner
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="title" id="title" placeholder="Masukkan judul banner">
                        </div>
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                <i class="ph-megaphone me-1"></i>
                                Promosi Terkait
                            </label>
                            <select class="form-select" name="promotion_id" id="promotion_id" data-dropdown-parent="#modal-form" data-placeholder="Pilih promosi (opsional)"></select>
                        </div>
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                <i class="ph-tag me-1"></i>
                                Jenis Banner
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" name="type" id="type">
                                <option value="">Pilih jenis banner</option>
                                <option value="slider">Slider - Banner bergerak otomatis</option>
                                <option value="overlay">Overlay - Banner popup</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                <i class="ph-flag me-1"></i>
                                Status Banner
                            </label>
                            <select class="form-select" name="status" id="status">
                                <option value="1">Aktif - Banner akan ditampilkan</option>
                                <option value="2">Tidak Aktif - Banner disembunyikan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                <i class="ph-note me-1"></i>
                                Keterangan / Deskripsi
                            </label>
                            <textarea class="form-control" name="description" id="description" rows="4" placeholder="Masukkan keterangan atau deskripsi banner (opsional)"></textarea>
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

        if(parseInt('{{ Main::isSuperAdmin() }}') == 0) {
            select2Serverside('#promotion_id', 'promotion', {
                province_id: '{{ session("province_id") }}',
            });
        } else {
            select2Serverside('#promotion_id', 'promotion');
        }

        $('#image').on('change', function(e) {
            const file = e.target.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    $('#image-preview-img').attr('src', e.target.result);
                    $('#image-preview').attr('href', e.target.result);
                    $('#image-preview-container').removeClass('d-none');
                };
                reader.readAsDataURL(file);
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
        $('#image-preview').attr('href', 'javascript:void(0);');
        $('#image-preview-container').addClass('d-none');
        $('#status').val(1);
        $('#promotion_id').val('').change();
    }

    function onCreate() {
        onReset();

        $('#modal-title-text').text('Tambah Data Banner');
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
        $('#modal-title-text').text('Edit Data Banner');
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
                url: '{{ url("administration-system/banner/datatable") }}',
                dataType: 'JSON',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-center' },
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
            url: '{{ url("administration-system/banner/create-data") }}',
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
            url: '{{ url("administration-system/banner/show-data") }}',
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
                $('#description').val(response.DESCRIPTION);
                $('#type').val(response.TYPE);
                $('#status').val(response.STATUS);
                $('#promotion_id').html('<option value="' + response.PROMO_ID + '" selected>' + response.JUDUL_E_PROMO + '</option>');

                if(response.IMAGE) {
                    var paramFile = {
                        id: response.ID,
                        type: 'gambar_banner',
                        filename: response.IMAGE,
                        v: '{{ Str::random(40) }}'
                    };

                    var imageUrl = `{{ url("stream-file") }}?${ $.param(paramFile) }`;
                    $('#image-preview').attr('href', imageUrl);
                    $('#image-preview-img').attr('src', imageUrl);
                    $('#image-preview-container').removeClass('d-none');
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
            url: '{{ url("administration-system/banner/update-data") }}',
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
            text: '<div class="mb-3"><h5 class="text-dark">Hapus Data Banner?</h5><span class="text-muted">Data yang telah dihapus tidak bisa dikembalikan lagi</span></div>',
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
                        url: '{{ url("administration-system/banner/destroy-data") }}',
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
