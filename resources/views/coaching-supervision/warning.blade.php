<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pengawasan & Pembinaan - <span class="fw-normal">Teguran</span>
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
        <div class="card-header">
            <h5 class="hstack gap-2 mb-0">Filter Data</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Tanggal :</label>
                        <input type="text" class="form-control" name="filter_date" id="filter_date" placeholder="Semua Tanggal" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Pelaksana Serah :</label>
                        <select class="form-select" name="filter_executor_id" id="filter_executor_id" data-placeholder="Semua"></select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Dari :</label>
                        <select class="form-select" name="filter_branch_id" id="filter_branch_id" data-placeholder="Semua"></select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="text-end">
                <a href="{{ url('coaching-supervision/warning') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
                    <i class="ph-arrows-clockwise me-1"></i>
                    Reset Filter
                </a>
                <a href="javascript:void(0);" class="btn btn-success" onclick="loadData()">
                    <i class="ph-magnifying-glass me-1"></i>
                    Cari Data
                </a>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover w-100 display" id="datatable-serverside">
                <thead class="text-bg-light">
                    <tr>
                        <th class="text-nowrap">No</th>
                        <th class="text-nowrap"><i class="ph-gear"></i></th>
                        <th class="text-nowrap">ID</th>
                        <th class="text-nowrap">Pelaksana Serah</th>
                        <th class="text-nowrap">Dari</th>
                        <th class="text-nowrap">Tgl Teguran</th>
                        <th class="text-nowrap">Tagihan Koleksi</th>
                        <th class="text-nowrap">Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div id="modal-form" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Pelaksana Serah : <span class="text-danger fw-bold">*</span></label>
                                <select class="form-select" name="executor_id" id="executor_id" data-dropdown-parent="#modal-form"></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Dari : <span class="text-danger fw-bold">*</span></label>
                                <select class="form-select" name="branch_id" id="branch_id" data-dropdown-parent="#modal-form"></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Tagihan Koleksi :</label>
                                <input type="number" class="form-control" name="bill_collection" id="bill_collection" placeholder="....................">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Status :</label>
                                <select class="form-select" name="status" id="status">
                                    <option value="DALAM TEGURAN">DALAM TEGURAN</option>
                                    <option value="SELESAI">SELESAI</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Teguran 1 :</label>
                        <div class="input-group">
                            <span class="input-group-text">File</span>
                            <input type="file" class="form-control" name="file" id="file">
                            <a href="" data-title="Preview File 1" class="btn btn-success" id="file-preview" target="_blank">
                                <i class="ph-file me-1"></i>
                                Lihat
                            </a>
                            <span class="input-group-text">Tanggal</span>
                            <input type="text" class="form-control date-single" name="warning_date" id="warning_date" placeholder="Pilih Tanggal" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Teguran 2 :</label>
                        <div class="input-group">
                            <span class="input-group-text">File</span>
                            <input type="file" class="form-control" name="file_2" id="file_2">
                            <a href="" data-title="Preview File 2" class="btn btn-success" id="file-preview-2" target="_blank">
                                <i class="ph-file me-1"></i>
                                Lihat
                            </a>
                            <span class="input-group-text">Tanggal</span>
                            <input type="text" class="form-control date-single" name="warning_date_2" id="warning_date_2" placeholder="Pilih Tanggal" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Teguran 3 :</label>
                        <div class="input-group">
                            <span class="input-group-text">File</span>
                            <input type="file" class="form-control" name="file_3" id="file_3">
                            <a href="" data-title="Preview File 3" class="btn btn-success" id="file-preview-3" target="_blank">
                                <i class="ph-file me-1"></i>
                                Lihat
                            </a>
                            <span class="input-group-text">Tanggal</span>
                            <input type="text" class="form-control date-single" name="warning_date_3" id="warning_date_3" placeholder="Pilih Tanggal" readonly>
                        </div>
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
        datePickerSingle('.date-single');

        if(parseInt('{{ Main::isNotSuperAdmin() }}') === 1) {
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
        $('#file-preview').hide();
        $('#file-preview-2').attr('href', 'javascript:void(0);');
        $('#file-preview-2').hide();
        $('#file-preview-3').attr('href', 'javascript:void(0);');
        $('#file-preview-3').hide();
        $('#executor_id').val('').change();
        $('#branch_id').html(`<option value="{{ session('branch_id') }}" selected>{{ session('branch_name') }}</option>`);
        $('#executor_id').val('DALAM TEGURAN');
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
                url: '{{ url("coaching-supervision/warning/datatable") }}',
                dataType: 'JSON',
                data: {
                    branch_id: $('#filter_branch_id').val(),
                    executor_id: $('#filter_executor_id').val(),
                    date: $('#filter_date').val(),
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
                { orderable: true, className: 'align-top text-center' },
                { orderable: false, className: 'align-top text-center' },
                { orderable: true, className: 'align-top' },
                { orderable: true, className: 'align-top text-wrap' },
                { orderable: true, className: 'align-top text-wrap' },
                { orderable: true, className: 'align-top' },
                { orderable: true, className: 'align-top' },
                { orderable: true, className: 'align-top text-wrap' },
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
                    $('#file-preview').fadeIn(500);
                }

                if(response.LINK_FILE_2) {
                    var paramFile = {
                        id: response.ID,
                        type: 'publisher_warning_2',
                        filename: response.LINK_FILE_2,
                        v: '{{ Str::random(40) }}'
                    };

                    $('#file-preview-2').attr('href', `{{ url("stream-file") }}?${ $.param(paramFile) }`);
                    $('#file-preview-2').fadeIn(500);
                }

                if(response.LINK_FILE_3) {
                    var paramFile = {
                        id: response.ID,
                        type: 'publisher_warning_3',
                        filename: response.LINK_FILE_3,
                        v: '{{ Str::random(40) }}'
                    };

                    $('#file-preview-3').attr('href', `{{ url("stream-file") }}?${ $.param(paramFile) }`);
                    $('#file-preview-3').fadeIn(500);
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
        if(value == 2) {
            var statusText = 'melakukan usulan blokir';
        } else {
            var statusText = 'buka blokir';
        }

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
                Noty.button('Ya', 'btn btn-danger ms-2', function () {
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
</script>
