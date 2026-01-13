<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Administrasi Sistem - <span class="fw-normal">Promosi</span>
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
                    <i class="ph-tag me-2 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Daftar Promosi</h6>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary">
                    <i class="ph-percent me-1"></i>
                    Manajemen Promo
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
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-map-pin me-1"></i>
                                Provinsi
                            </th>
                            <th class="text-nowrap" style="min-width: 200px">
                                <i class="ph-notebook me-1"></i>
                                Judul
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-barcode me-1"></i>
                                Kode
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 140px">
                                <i class="ph-calendar-check me-1"></i>
                                Tgl Mulai
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 140px">
                                <i class="ph-calendar-x me-1"></i>
                                Tgl Berakhir
                            </th>
                            <th class="text-nowrap" style="min-width: 130px">
                                <i class="ph-wallet me-1"></i>
                                Saldo
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 100px">
                                <i class="ph-percent me-1"></i>
                                Diskon
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 120px">
                                <i class="ph-package me-1"></i>
                                Jumlah Paket
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
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ph-tag me-2"></i>
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
                    <div class="form-group">
                        <label class="form-label fw-semibold">
                            <i class="ph-notebook me-1"></i>
                            Judul Promosi
                            <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" name="title" id="title" placeholder="Masukkan judul promosi">
                        <div class="form-text">Nama atau judul promosi yang akan ditampilkan</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="form-label fw-semibold">
                                <i class="ph-barcode me-1"></i>
                                Kode Promo
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control text-uppercase" name="code" id="code" placeholder="KODEPROMO">
                            <div class="form-text">Kode unik untuk promosi</div>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label fw-semibold">
                                <i class="ph-calendar-check me-1"></i>
                                Tanggal Mulai
                                <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" class="form-control" name="start_date" id="start_date">
                            <div class="form-text">Tanggal mulai berlaku</div>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label fw-semibold">
                                <i class="ph-calendar-x me-1"></i>
                                Tanggal Berakhir
                                <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" class="form-control" name="end_date" id="end_date">
                            <div class="form-text">Tanggal berakhir promo</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="form-label fw-semibold">
                                <i class="ph-wallet me-1"></i>
                                Saldo
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" name="balance" id="balance" placeholder="0">
                            </div>
                            <div class="form-text">Nilai saldo promosi (opsional)</div>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label fw-semibold">
                                <i class="ph-percent me-1"></i>
                                Diskon
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control" name="discount" id="discount" placeholder="0" min="0" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="form-text">Persentase diskon (0-100)</div>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="form-label fw-semibold">
                                <i class="ph-package me-1"></i>
                                Jumlah Paket
                            </label>
                            <input type="number" class="form-control" name="package" id="package" placeholder="0" min="0">
                            <div class="form-text">Jumlah paket yang tersedia</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label fw-semibold">
                            <i class="ph-map-pin me-1"></i>
                            Provinsi
                        </label>
                        <select class="form-select" name="province_id[]" id="province_id" data-dropdown-parent="#modal-form" data-placeholder="Pilih satu atau beberapa provinsi" multiple></select>
                        <div class="form-text">Pilih provinsi yang berlaku untuk promosi ini (kosongkan untuk semua provinsi)</div>
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
        $('#balance').number(true);

        if(parseInt('{{ Main::isNotSuperAdmin() }}') === 1) {
            select2Serverside('#province_id', 'location', {
                for: 'province',
                province_id: '{{ session("province_id") }}',
            }, {
                minimumInputLength: 0
            });
        } else {
            select2Serverside('#province_id', 'location', {
                for: 'province'
            }, {
                minimumInputLength: 0
            });
        }

        $('#code').on('input', function() {
            $(this).val($(this).val().toUpperCase());
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
        $('#province_id').val(null).trigger('change');
    }

    function onCreate() {
        onReset();

        $('#modal-title-text').text('Tambah Data Promosi');
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
        $('#modal-title-text').text('Edit Data Promosi');
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
                url: '{{ url("administration-system/promotion/datatable") }}',
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
                { orderable: false, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-end' },
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
            url: '{{ url("administration-system/promotion/create-data") }}',
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
            url: '{{ url("administration-system/promotion/show-data") }}',
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

                $('#table_id').val(response.data.ID);
                $('#title').val(response.data.JUDUL);
                $('#code').val(response.data.KODE_PROMO);
                $('#start_date').val(moment(response.data.TANGGAL_MULAI).format('YYYY-MM-DDTHH:mm'));
                $('#end_date').val(moment(response.data.TANGGAL_SELESAI).format('YYYY-MM-DDTHH:mm'));
                $('#balance').val(response.data.SALDO);
                $('#discount').val(response.data.DISKON);
                $('#package').val(response.data.JUMLAH_PAKET);

                if(response.province) {
                    $.each(response.province, function(i, val) {
                        $('#province_id').append('<option value="' + val.ID + '" selected>' + val.NAMAPROPINSI + '</option>')
                    });
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
            url: '{{ url("administration-system/promotion/update-data") }}',
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
            text: '<div class="mb-3"><h5 class="text-dark">Hapus Data Promosi?</h5><span class="text-muted">Data yang telah dihapus tidak dapat dikembalikan lagi</span></div>',
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
                        url: '{{ url("administration-system/promotion/destroy-data") }}',
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
