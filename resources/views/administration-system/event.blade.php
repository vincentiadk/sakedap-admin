<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Administrasi Sistem - <span class="fw-normal">Event</span>
            </h4>
        </div>
        <div class="collapse d-lg-block my-lg-auto ms-lg-auto" id="page-header">
            <div class="d-sm-flex align-items-center mb-3 mb-lg-0 ms-lg-3">
                <div class="d-inline-flex mt-3 mt-sm-0">
                    <button type="button" class="btn btn-primary" onclick="onCreate()">
                        <i class="ph-plus-circle me-1"></i>
                        Tambah Data
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover w-100 display" id="datatable-serverside">
                <thead class="text-bg-light">
                    <tr>
                        <th class="text-nowrap">No</th>
                        <th class="text-nowrap"><i class="ph-gear"></i></th>
                        <th class="text-nowrap">Gambar</th>
                        <th class="text-nowrap">Total Katalog</th>
                        <th class="text-nowrap">Kategori</th>
                        <th class="text-nowrap">Judul</th>
                        <th class="text-nowrap">Lang</th>
                        <th class="text-nowrap">Lampiran Link</th>
                        <th class="text-nowrap">Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div id="modal-form" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">
                    <i class="ph-x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger d-none" id="validation-element">
                    <ul class="mb-0" id="validation-data"></ul>
                </div>
                <form id="form-data" class="form-ajax">
                    <input type="hidden" name="table_id" id="table_id">
                    <div class="form-group">
                        <label class="form-label">Gambar :</label>
                        <div class="input-group">
                            <input type="file" class="form-control" name="image" id="image">
                            <a href="" data-lightbox="news-form" data-title="Preview Gambar" class="btn btn-success" id="image-preview">
                                <i class="ph-image me-1"></i>
                                Lihat Gambar Saat Ini
                            </a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Katalog :</label>
                        <select class="form-select" name="catalog[]" id="catalog" data-placeholder="Pilih Beberapa" data-dropdown-parent="#modal-form" multiple></select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kategori : <span class="text-danger fw-bold">*</span></label>
                        <select class="form-select select2-basic" name="category_id" id="category_id" data-dropdown-parent="#modal-form">
                            <option value=""></option>
                            @foreach($category as $c)
                                <option value="{{ $c->ID }}">{{ $c->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Judul : <span class="text-danger fw-bold">*</span></label>
                        <input type="text" class="form-control" name="title" id="title" placeholder="....................">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status :</label>
                        <select class="form-select" name="status" id="status">
                            <option value="PUBLISH">PUBLISH</option>
                            <option value="HIDE">HIDE</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Lang :</label>
                        <select class="form-select" name="lang" id="lang">
                            <option value="ID">ID</option>
                            <option value="EN">EN</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Lampiran Link :</label>
                        <input type="text" class="form-control" name="attachment_link" id="attachment_link" placeholder="....................">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ringkasan :</label>
                        <textarea class="form-control" name="summary" id="summary" rows="5" placeholder="...................."></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konten :</label>
                        <textarea class="form-control" name="content" id="content" placeholder="...................."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-end">
                <button class="btn btn-danger d-none" id="btn-cancel" onclick="onCancel()">
                    <i class="ph-x me-1"></i>
                    Batalkan Perubahan
                </button>
                <button class="btn btn-warning d-none" id="btn-update" onclick="updateData()">
                    <i class="ph-floppy-disk me-1"></i>
                    Simpan Perubahan Data
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
        select2Serverside('#catalog', 'catalog', {}, {
            minimumInputLength: 0
        });

        $('#content').summernote({
            height: 300
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
        $('#image-preview').hide();
        $('#status').val('PUBLISH');
        $('#lang').val('ID');
        $('#category_id').val('').change();
        $('#content').summernote('code', '');
        $('#catalog').html('');
        $('#catalog').val('').change();
    }

    function onCreate() {
        onReset();

        $('#modal-form .modal-title').text('Tambah Data');
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
        $('#modal-form .modal-title').text('Edit Data');
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
                url: '{{ url("administration-system/event/datatable") }}',
                dataType: 'JSON',
                beforeSend: function() {
                    onLoading('show', '#datatable-serverside_wrapper');
                },
                error: function(response) {
                    onLoading('close', '#datatable-serverside_wrapper');
                    responseError(response);
                }
            },
            columns: [
                { orderable: true, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
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
            url: '{{ url("administration-system/event/create-data") }}',
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
            url: '{{ url("administration-system/event/show-data") }}',
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

                if(response.CATALOG) {
                    var catalog = JSON.parse(response.CATALOG);

                    if(catalog) {
                        $.each(catalog, function(i, val) {
                            $('#catalog').append(`
                                <option value="${ val.id }" selected>${ val.title }</option>
                            `);
                        });
                    }
                }

                if(response.IMAGE) {
                    var paramFile = {
                        id: response.ID,
                        type: 'gambar_artikel',
                        filename: response.IMAGE,
                        v: '{{ Str::random(40) }}'
                    };

                    $('#image-preview').attr('href', `{{ url("stream-file") }}?${ $.param(paramFile) }`);
                    $('#image-preview').fadeIn(500);
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
            url: '{{ url("administration-system/event/update-data") }}',
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
            text: '<div class="mb-3"><h5 class="text-dark">Hapus Data?</h5><span class="text-muted">Data yang telah dihapus tidak bisa dikembalikan lagi</span></div>',
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
                        url: '{{ url("administration-system/event/destroy-data") }}',
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
