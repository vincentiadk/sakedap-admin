<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Administrasi Sistem - <span class="fw-normal">Template Email</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <span class="badge bg-primary p-2 bg-opacity-10 text-primary">
                    <i class="ph-envelope-simple me-1"></i>
                    Manajemen Template
                </span>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="card border-0 shadow-sm">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ph-file-html me-2 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Daftar Template Email</h6>
                </div>
                <span class="badge bg-info bg-opacity-10 text-info">
                    <i class="ph-code me-1"></i>
                    HTML Template
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
                            <th class="text-nowrap" style="min-width: 200px">
                                <i class="ph-tag me-1"></i>
                                Slug
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 150px">
                                <i class="ph-calendar-plus me-1"></i>
                                Tgl Dibuat
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 150px">
                                <i class="ph-calendar-check me-1"></i>
                                Tgl Diperbarui
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="modal-form" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ph-pencil-simple me-2"></i>
                    Edit Template Email
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning border-0 mb-3">
                    <div class="d-flex align-items-start">
                        <i class="ph-warning me-2 fs-4"></i>
                        <div>
                            <h6 class="mb-1">Perhatian</h6>
                            Pastikan Anda memahami struktur HTML sebelum melakukan perubahan. Perubahan yang salah dapat menyebabkan tampilan email tidak sesuai.
                        </div>
                    </div>
                </div>
                <form id="form-data" class="form-ajax">
                    <input type="hidden" name="table_id" id="table_id">
                    <div class="form-group">
                        <label class="form-label fw-semibold">
                            <i class="ph-code me-1"></i>
                            Konten Template
                        </label>
                        <textarea name="contents" class="form-control contents" id="contents" rows="10"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ph-x me-1"></i>
                    Tutup
                </button>
                <button class="btn btn-danger" id="btn-cancel" onclick="onCancel()">
                    <i class="ph-arrow-counter-clockwise me-1"></i>
                    Batalkan Perubahan
                </button>
                <button class="btn btn-primary" id="btn-update" onclick="updateData()">
                    <i class="ph-floppy-disk me-1"></i>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        loadData();

        CKEDITOR.replace('contents',{
            enterMode : CKEDITOR.ENTER_BR,
            height: 400,
            versionCheck: false,
            enforceFocus: false,
            toolbar: [
                { name: 'document', items: [ 'Source', '-', 'Preview' ] },
                { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
                { name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll' ] },
                '/',
                { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
                { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
                { name: 'links', items: [ 'Link', 'Unlink' ] },
                { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule' ] },
                '/',
                { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
                { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
                { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] }
            ]
        });
    });

    function onReloadTable() {
        window.gDataTable.ajax.reload(null, false);
    }

    function onReset() {
        $('#modal-form').modal('hide');
        $('#form-data').trigger('reset');

        if(CKEDITOR.instances.contents) {
            CKEDITOR.instances.contents.setData('');
        }
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
        $('#modal-form').modal('show');
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
                url: '{{ url("administration-system/template-email/datatable") }}',
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
                { orderable: true, className: 'align-middle' },
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
            },
        }).on('draw.dt', function() {
            onLoading('close', '#datatable-serverside_wrapper');
        });

        window.gDataTable.columns.adjust().draw();
    }

    function showDataUpdate(id) {
        $.ajax({
            url: '{{ url("administration-system/template-email/show-data") }}',
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

                if(CKEDITOR.instances.contents) {
                    CKEDITOR.instances.contents.setData(response.CONTENT);
                }
            },
            error: function(response) {
                onLoading('close', '.modal-content');
                responseError(response);
            }
        });
    }

    function updateData() {
        var content = '';

        if(CKEDITOR.instances.contents) {
            content = CKEDITOR.instances.contents.getData();
        }

        if(!content || content.trim() === '') {
            swalInit.fire({
                title: 'Oops...',
                text: 'Konten template tidak boleh kosong',
                icon: 'warning',
                showCloseButton: false
            });
            return;
        }

        $.ajax({
            url: '{{ url("administration-system/template-email/update-data") }}',
            type: 'POST',
            dataType: 'JSON',
            data: {
                table_id: $('#table_id').val(),
                content: content
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            beforeSend: function() {
                onLoading('show', '.modal-content');
            },
            success: function(response) {
                onLoading('close', '.modal-content');

                if(response.code == 200) {
                    formSuccess();
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
                onLoading('close', '.modal-content');
                responseError(response);
            }
        });
    }
</script>
