<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pengawasan & Pembinaan - <span class="fw-normal">Teguran</span>
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
                    <i class="ph-funnel me-1 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Filter Pencarian</h6>
                </div>
                <button type="button" class="btn btn-sm btn-light" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="ph-caret-down"></i>
                </button>
            </div>
        </div>
        <div class="collapse" id="filterCollapse">
            <div class="card-body">
                <form id="form-filter">
                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-calendar-blank me-1"></i>
                                Tanggal
                            </label>
                            <input type="text" class="form-control" name="filter_date" id="filter_date" placeholder="Pilih tanggal" readonly>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-user-circle me-1"></i>
                                Pelaksana Serah
                            </label>
                            <select class="form-select" name="filter_executor_id" id="filter_executor_id" data-placeholder="Semua Pelaksana"></select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-buildings me-1"></i>
                                Dari
                            </label>
                            <select class="form-select" name="filter_branch_id" id="filter_branch_id" data-placeholder="Semua Cabang"></select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer border-top">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ url('coaching-supervision/warning') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
                        <i class="ph-arrow-counter-clockwise me-1"></i>
                        Reset Filter
                    </a>
                    <button type="button" class="btn btn-primary" onclick="loadData()">
                        <i class="ph-magnifying-glass me-1"></i>
                        Cari Data
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ph-warning-circle me-1 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Daftar Teguran</h6>
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
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-fingerprint me-1"></i>
                                ID
                            </th>
                            <th class="text-nowrap" style="min-width: 200px">
                                <i class="ph-user-circle me-1"></i>
                                Pelaksana Serah
                            </th>
                            <th class="text-nowrap" style="min-width: 180px">
                                <i class="ph-buildings me-1"></i>
                                Dari
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-warning me-1"></i>
                                Teguran 1
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-warning me-1"></i>
                                Teguran 2
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-warning me-1"></i>
                                Teguran 3
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 120px">
                                <i class="ph-receipt me-1"></i>
                                Tagihan Koleksi
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 130px">
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
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title">
                    <i class="ph-note-pencil me-2"></i>
                    <span id="modal-title-text"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger border-0 d-none" id="validation-element">
                    <div class="d-flex align-items-center mb-2">
                        <i class="ph-warning-circle me-2"></i>
                        <strong>Terdapat kesalahan:</strong>
                    </div>
                    <ul class="mb-0" id="validation-data"></ul>
                </div>
                <form id="form-data" class="form-ajax">
                    <input type="hidden" name="table_id" id="table_id">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-user-circle me-1"></i>
                                Pelaksana Serah
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" name="executor_id" id="executor_id" data-dropdown-parent="#modal-form"></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-buildings me-1"></i>
                                Dari
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" name="branch_id" id="branch_id" data-dropdown-parent="#modal-form"></select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-receipt me-1"></i>
                                Tagihan Koleksi
                            </label>
                            <input type="number" class="form-control" name="bill_collection" id="bill_collection" placeholder="Masukkan jumlah tagihan">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-flag me-1"></i>
                                Status
                            </label>
                            <select class="form-select" name="status" id="status">
                                <option value="DALAM TEGURAN">Dalam Teguran</option>
                                <option value="SELESAI">Selesai</option>
                            </select>
                        </div>
                    </div>
                    <div class="border-top my-3"></div>
                    <div class="form-group">
                        <label class="form-label fw-semibold">
                            <i class="ph-warning me-1 text-warning"></i>
                            Teguran 1
                        </label>
                        <div class="row g-2">
                            <div class="col-md-7">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ph-file"></i>
                                    </span>
                                    <input type="file" class="form-control" name="file" id="file">
                                    <a href="" data-title="Preview File 1" class="btn btn-success d-none" id="file-preview" target="_blank">
                                        <i class="ph-eye me-1"></i>
                                        Lihat
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ph-calendar"></i>
                                    </span>
                                    <input type="text" class="form-control date-single" name="warning_date" id="warning_date" placeholder="Pilih tanggal" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label fw-semibold">
                            <i class="ph-warning me-1 text-warning"></i>
                            Teguran 2
                        </label>
                        <div class="row g-2">
                            <div class="col-md-7">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ph-file"></i>
                                    </span>
                                    <input type="file" class="form-control" name="file_2" id="file_2">
                                    <a href="" data-title="Preview File 2" class="btn btn-success d-none" id="file-preview-2" target="_blank">
                                        <i class="ph-eye me-1"></i>
                                        Lihat
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ph-calendar"></i>
                                    </span>
                                    <input type="text" class="form-control date-single" name="warning_date_2" id="warning_date_2" placeholder="Pilih tanggal" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label fw-semibold">
                            <i class="ph-warning me-1 text-danger"></i>
                            Teguran 3
                        </label>
                        <div class="row g-2">
                            <div class="col-md-7">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ph-file"></i>
                                    </span>
                                    <input type="file" class="form-control" name="file_3" id="file_3">
                                    <a href="" data-title="Preview File 3" class="btn btn-success d-none" id="file-preview-3" target="_blank">
                                        <i class="ph-eye me-1"></i>
                                        Lihat
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ph-calendar"></i>
                                    </span>
                                    <input type="text" class="form-control date-single" name="warning_date_3" id="warning_date_3" placeholder="Pilih tanggal" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top">
                <button class="btn btn-danger d-none" id="btn-cancel" onclick="onCancel()">
                    <i class="ph-x me-1"></i>
                    Batalkan
                </button>
                <button class="btn btn-warning d-none" id="btn-update" onclick="updateData()">
                    <i class="ph-floppy-disk me-1"></i>
                    Simpan Perubahan
                </button>
                <button class="btn btn-primary d-none" id="btn-create" onclick="createData()">
                    <i class="ph-check-circle me-1"></i>
                    Simpan Data
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        datePickerSingle('.date-single');

        if(parseInt('{{ Main::isSuperAdmin() }}') == 0) {
            select2Serverside('#branch_id, #filter_branch_id', 'branch', {
                province_id: '{{ session("province_id") }}'
            }, {
                minimumInputLength: 0
            });

            select2Serverside('#executor_id, #filter_executor_id', 'executor', {
                province_id: '{{ session("province_id") }}',
            });
        } else {
            select2Serverside('#branch_id, #filter_branch_id', 'branch');
            select2Serverside('#executor_id, #filter_executor_id', 'executor');
        }

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
        $('#file-preview').attr('href', 'javascript:void(0);');
        $('#file-preview').addClass('d-none');
        $('#file-preview-2').attr('href', 'javascript:void(0);');
        $('#file-preview-2').addClass('d-none');
        $('#file-preview-3').attr('href', 'javascript:void(0);');
        $('#file-preview-3').addClass('d-none');
        $('#executor_id').val('').change();
        $('#branch_id').html(`<option value="{{ session('branch_id') }}" selected>{{ session('branch_name') }}</option>`);
        $('#status').val('DALAM TEGURAN');
    }

    function onCreate() {
        onReset();

        $('#modal-title-text').text('Tambah Data Teguran');
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
        $('#modal-title-text').text('Edit Data Teguran');
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
                url: '{{ url("coaching-supervision/warning/datatable") }}',
                dataType: 'JSON',
                data: function (d) {
                    $('#form-filter').serializeArray().forEach(function(item) {
                        d[item.name] = item.value;
                    });

                    return d;
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
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
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

    function createData() {
        $.ajax({
            url: '{{ url("coaching-supervision/warning/create-data") }}',
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
            url: '{{ url("coaching-supervision/warning/show-data") }}',
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
                $('#executor_id').html('<option value="' + response.PUBLISHER_ID + '" selected>' + response.NAME_PENERBIT + '</option>');
                $('#branch_id').html('<option value="' + response.BRANCH_ID + '" selected>' + response.NAME_BRANCH + '</option>');
                $('#warning_date').val(response.WARNING_DATE ? moment(response.WARNING_DATE).format('YYYY/MM/DD') : '');
                $('#warning_date_2').val(response.WARNING_DATE_2 ? moment(response.WARNING_DATE_2).format('YYYY/MM/DD') : '');
                $('#warning_date_3').val(response.WARNING_DATE_3 ? moment(response.WARNING_DATE_3).format('YYYY/MM/DD') : '');
                $('#bill_collection').val(response.TAGIHAN_KOLEKSI);
                $('#status').val(response.STATUS);

                if(response.LINK_FILE) {
                    var paramFile = {
                        id: response.ID,
                        type: 'publisher_warning',
                        filename: response.LINK_FILE,
                        v: '{{ Str::random(40) }}'
                    };

                    $('#file-preview').attr('href', `{{ url("stream-file") }}?${ $.param(paramFile) }`);
                    $('#file-preview').removeClass('d-none');
                }

                if(response.LINK_FILE_2) {
                    var paramFile = {
                        id: response.ID,
                        type: 'publisher_warning_2',
                        filename: response.LINK_FILE_2,
                        v: '{{ Str::random(40) }}'
                    };

                    $('#file-preview-2').attr('href', `{{ url("stream-file") }}?${ $.param(paramFile) }`);
                    $('#file-preview-2').removeClass('d-none');
                }

                if(response.LINK_FILE_3) {
                    var paramFile = {
                        id: response.ID,
                        type: 'publisher_warning_3',
                        filename: response.LINK_FILE_3,
                        v: '{{ Str::random(40) }}'
                    };

                    $('#file-preview-3').attr('href', `{{ url("stream-file") }}?${ $.param(paramFile) }`);
                    $('#file-preview-3').removeClass('d-none');
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
            url: '{{ url("coaching-supervision/warning/update-data") }}',
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

    function lockable(id, value) {
        var statusText = (value == 2) ? 'melakukan usulan blokir' : 'buka blokir';
        var btnText = (value == 2) ? 'Blokir' : 'Buka Blokir';
        var btnClass = (value == 2) ? 'btn-danger' : 'btn-success';

        var notyConfirm = new Noty({
            text: '<div class="mb-3"><h5 class="text-dark">Pemblokiran Pelaksana Serah?</h5><span class="text-muted">Anda yakin ingin ' + statusText + '?</span></div>',
            timeout: false,
            modal: true,
            layout: 'center',
            closeWith: 'button',
            type: 'confirm',
            buttons: [
                Noty.button('Tidak', 'btn btn-light', function () {
                    notyConfirm.close();
                }),
                Noty.button(btnText, 'btn ' + btnClass + ' ms-2', function () {
                    $.ajax({
                        url: '{{ url("coaching-supervision/warning/lockable") }}',
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            id: id,
                            is_lock: value
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

                                swalInit.fire({
                                    title: 'Berhasil',
                                    text: response.message,
                                    icon: 'success',
                                    showCloseButton: false
                                });
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

    function lockable(id, value) {
        var statusText = (value == 2) ? 'melakukan usulan blokir' : 'buka blokir';
        var btnText = (value == 2) ? 'Blokir' : 'Buka Blokir';
        var btnClass = (value == 2) ? 'btn-danger' : 'btn-success';

        var notyConfirm = new Noty({
            text: '<div class="mb-3"><h5 class="text-dark">Pemblokiran Pelaksana Serah?</h5><span class="text-muted">Anda yakin ingin ' + statusText + '?</span></div>',
            timeout: false,
            modal: true,
            layout: 'center',
            closeWith: 'button',
            type: 'confirm',
            buttons: [
                Noty.button('Tidak', 'btn btn-light', function () {
                    notyConfirm.close();
                }),
                Noty.button(btnText, 'btn ' + btnClass + ' ms-2', function () {
                    $.ajax({
                        url: '{{ url("coaching-supervision/warning/lockable") }}',
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            id: id,
                            is_lock: value
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
                        url: '{{ url("coaching-supervision/warning/destroy-data") }}',
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

    function sendEmail(id, target) {
        var notyConfirm = new Noty({
            text: '<div class="mb-3"><h5 class="text-dark">Kirim Email?</h5><span class="text-muted">Anda yakin ingin mengirim email?</span></div>',
            timeout: false,
            modal: true,
            layout: 'center',
            closeWith: 'button',
            type: 'confirm',
            buttons: [
                Noty.button('Tidak', 'btn btn-light', function () {
                    notyConfirm.close();
                }),
                Noty.button('Kirim Email', 'btn btn-primary ms-2', function () {
                    $.ajax({
                        url: '{{ url("coaching-supervision/warning/send-email") }}',
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            id: id,
                            target: target,
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

                                swalInit.fire({
                                    title: 'Berhasil',
                                    text: response.message,
                                    icon: 'success',
                                    showCloseButton: false
                                });
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

    function sendWhatsapp(id, target) {
        var notyConfirm = new Noty({
            text: '<div class="mb-3"><h5 class="text-dark">Kirim WhatsApp?</h5><span class="text-muted">Anda yakin ingin mengirim WhatsApp?</span></div>',
            timeout: false,
            modal: true,
            layout: 'center',
            closeWith: 'button',
            type: 'confirm',
            buttons: [
                Noty.button('Tidak', 'btn btn-light', function () {
                    notyConfirm.close();
                }),
                Noty.button('Kirim WhatsApp', 'btn btn-success ms-2', function () {
                    $.ajax({
                        url: '{{ url("coaching-supervision/warning/send-whatsapp") }}',
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            id: id,
                            target: target,
                        },
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        beforeSend: function() {
                            onLoading('show', '.noty_bar');
                        },
                        success: function(response) {
                            onLoading('close', '.noty_bar');

                            if(response.code == 201) {
                                notyConfirm.close();

                                swalInit.fire({
                                    title: 'Berhasil',
                                    text: response.message,
                                    icon: 'success',
                                    showCloseButton: false
                                });
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
