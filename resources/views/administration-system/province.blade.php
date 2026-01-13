<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Administrasi Sistem - <span class="fw-normal">Provinsi</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <button type="button" class="btn btn-primary" onclick="onCreate()">
                    <i class="ph-plus-circle me-1"></i>
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
                    <i class="ph-map-pin me-2 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Daftar Provinsi</h6>
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
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-code me-1"></i>
                                Kode
                            </th>
                            <th class="text-nowrap" style="min-width: 250px">
                                <i class="ph-flag me-1"></i>
                                Nama
                            </th>
                            <th class="text-nowrap text-center" style="min-width: 150px">
                                <i class="ph-globe-hemisphere-east me-1"></i>
                                Latitude
                            </th>
                            <th class="text-nowrap text-center" style="min-width: 150px">
                                <i class="ph-globe-hemisphere-west me-1"></i>
                                Longitude
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="modal-form" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ph-map-trifold me-2"></i>
                    <span id="modal-title-text"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert bg-danger text-white border-0 alert-dismissible fade show d-none" id="validation-element">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                    <div class="d-flex align-items-start">
                        <i class="ph-warning-circle me-2 fs-5"></i>
                        <div class="flex-fill">
                            <strong>Validasi Error!</strong>
                            <ul class="mb-0 mt-2" id="validation-data"></ul>
                        </div>
                    </div>
                </div>
                <form id="form-data" class="form-ajax">
                    <input type="hidden" name="table_id" id="table_id">
                    <div class="form-group">
                        <label class="form-label fw-semibold">
                            <i class="ph-code me-1"></i>
                            Kode Provinsi
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="ph-identification-badge"></i>
                            </span>
                            <input type="text" class="form-control" name="code" id="code" placeholder="Masukkan kode provinsi">
                        </div>
                        <small class="form-text text-muted">
                            <i class="ph-info me-1"></i>
                            Kode unik provinsi (contoh: 11, 12, 31)
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="form-label fw-semibold">
                            <i class="ph-flag me-1"></i>
                            Nama Provinsi
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="ph-text-aa"></i>
                            </span>
                            <input type="text" class="form-control" name="name" id="name" placeholder="Masukkan nama provinsi">
                        </div>
                        <small class="form-text text-muted">
                            <i class="ph-info me-1"></i>
                            Nama lengkap provinsi (contoh: DKI Jakarta, Jawa Barat)
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="form-label fw-semibold">
                            <i class="ph-map-trifold me-1"></i>
                            Koordinat Geografis
                        </label>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ph-globe-hemisphere-east"></i>
                                        Lat
                                    </span>
                                    <input type="number" class="form-control" name="latitude" id="latitude" placeholder="-6.2088" step="any">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ph-globe-hemisphere-west"></i>
                                        Long
                                    </span>
                                    <input type="number" class="form-control" name="longitude" id="longitude" placeholder="106.8456" step="any">
                                </div>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            <i class="ph-info me-1"></i>
                            Koordinat geografis provinsi (opsional, untuk pemetaan)
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ph-x me-1"></i>
                    Tutup
                </button>
                <button class="btn btn-danger d-none" id="btn-cancel" onclick="onCancel()">
                    <i class="ph-arrow-counter-clockwise me-1"></i>
                    Batalkan Perubahan
                </button>
                <button class="btn btn-primary d-none" id="btn-update" onclick="updateData()">
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
    }

    function onCreate() {
        onReset();

        $('#modal-title-text').text('Tambah Data');
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
        $('#modal-title-text').text('Edit Data');
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
                url: '{{ url("administration-system/province/datatable") }}',
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
                { orderable: true, className: 'align-middle text-center fw-semibold' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle' },
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
            url: '{{ url("administration-system/province/create-data") }}',
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
            url: '{{ url("administration-system/province/show-data") }}',
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
                $('#code').val(response.CODE);
                $('#name').val(response.NAMAPROPINSI);
                $('#latitude').val(response.LATITUDE);
                $('#longitude').val(response.LONGITUDE);
            },
            error: function(response) {
                onLoading('close', '.modal-content');
                responseError(response);
            }
        });
    }

    function updateData() {
        $.ajax({
            url: '{{ url("administration-system/province/update-data") }}',
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
                        url: '{{ url("administration-system/province/destroy-data") }}',
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
