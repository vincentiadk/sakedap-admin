<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Administrasi Sistem - <span class="fw-normal">FAQ</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <button type="button" class="btn btn-primary" onclick="onCreate()">
                    <i class="ph-question me-1"></i>
                    Tambah FAQ
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
                    <i class="ph-question me-1 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Daftar FAQ (Frequently Asked Questions)</h6>
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
                            <th class="text-center text-nowrap" style="min-width: 80px">
                                <i class="ph-translate me-1"></i>
                                Lang
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 100px">
                                <i class="ph-sort-ascending me-1"></i>
                                Urutan
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 100px">
                                <i class="ph-eye me-1"></i>
                                Publish
                            </th>
                            <th class="text-nowrap" style="min-width: 300px">
                                <i class="ph-chat-circle-text me-1"></i>
                                Pertanyaan
                            </th>
                            <th class="text-nowrap" style="min-width: 350px">
                                <i class="ph-chat-circle-dots me-1"></i>
                                Jawaban
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-tag me-1"></i>
                                Kategori
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
                <h5 class="modal-title fw-semibold">
                    <i class="ph-question me-2"></i>
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
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ph-sort-ascending me-1"></i>
                                    Urutan Tampil
                                </label>
                                <input type="number" class="form-control" name="sequence" id="sequence" min="0" placeholder="0">
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ph-eye me-1"></i>
                                    Status Publikasi
                                </label>
                                <select class="form-select" name="publish" id="publish">
                                    <option value="1">Ya - Ditampilkan</option>
                                    <option value="2">Tidak - Disembunyikan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
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
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ph-tag me-1"></i>
                                    Kategori FAQ
                                </label>
                                <input type="text" class="form-control" name="category" id="category" placeholder="Contoh: Pendaftaran, Peminjaman, Layanan, dll">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ph-chat-circle-text me-1"></i>
                                    Pertanyaan
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control text-editor" name="question" id="question" rows="5" placeholder="Tulis pertanyaan yang sering ditanyakan..."></textarea>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ph-chat-circle-dots me-1"></i>
                                    Jawaban
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" name="answer" id="answer" rows="5" placeholder="Tulis jawaban yang lengkap dan informatif..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info border-0 bg-info bg-opacity-10">
                        <div class="d-flex">
                            <i class="ph-lightbulb me-2 fs-5"></i>
                            <div>
                                <h6 class="alert-heading mb-1">Tips Menulis FAQ yang Baik:</h6>
                                <ul class="mb-0 small">
                                    <li>Gunakan bahasa yang mudah dipahami</li>
                                    <li>Pertanyaan harus spesifik dan langsung ke pokok masalah</li>
                                    <li>Jawaban harus lengkap namun tidak bertele-tele</li>
                                    <li>Sertakan contoh atau langkah-langkah jika diperlukan</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top bg-light">
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
                    Simpan FAQ
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('#question').summernote({
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });

        $('#answer').summernote({
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });

        loadData();
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
        $('#sequence').val(0);
        $('#publish').val(1);
        $('#lang').val('ID');
        $('#question').summernote('code', '');
        $('#answer').summernote('code', '');
    }

    function onCreate() {
        onReset();

        $('#modal-title-text').text('Tambah Data FAQ');
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
        $('#modal-title-text').text('Edit Data FAQ');
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
                url: '{{ url("administration-system/faq/datatable") }}',
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
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
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
            url: '{{ url("administration-system/faq/create-data") }}',
            type: 'POST',
            dataType: 'JSON',
            data: $('#form-data').serialize(),
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
            url: '{{ url("administration-system/faq/show-data") }}',
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
                $('#sequence').val(response.SEQUENCE);
                $('#publish').val(response.PUBLISH);
                $('#question').summernote('code', response.QUESTION);
                $('#answer').summernote('code', response.ANSWER);
                $('#category').val(response.CATEGORY);
                $('#lang').val(response.LANG);
            },
            error: function(response) {
                onLoading('close', '.modal-content');
                responseError(response);
            }
        });
    }

    function updateData() {
        $.ajax({
            url: '{{ url("administration-system/faq/update-data") }}',
            type: 'POST',
            dataType: 'JSON',
            data: $('#form-data').serialize(),
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
            text: '<div class="mb-3"><h5 class="text-dark">Hapus Data FAQ?</h5><span class="text-muted">Data yang telah dihapus tidak bisa dikembalikan lagi</span></div>',
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
                        url: '{{ url("administration-system/faq/destroy-data") }}',
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
