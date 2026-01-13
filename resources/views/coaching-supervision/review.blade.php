<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pengawasan & Pembinaan - <span class="fw-normal">Peninjauan</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <span class="badge bg-info p-2 bg-opacity-10 text-info">
                    <i class="ph-eye me-1"></i>
                    Peninjauan Data
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
                    <i class="ph-clipboard-text me-1 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Daftar Pelaksana Serah Perlu Ditinjau</h6>
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
                            <th class="text-nowrap" style="min-width: 100px">
                                <i class="ph-identification-card me-1"></i>
                                ID
                            </th>
                            <th class="text-nowrap" style="min-width: 200px">
                                <i class="ph-buildings me-1"></i>
                                Nama
                            </th>
                            <th class="text-nowrap" style="min-width: 200px">
                                <i class="ph-envelope me-1"></i>
                                Email
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-tag me-1"></i>
                                Kategori
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-list me-1"></i>
                                Jenis
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-phone me-1"></i>
                                Telp
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 130px">
                                <i class="ph-calendar-plus me-1"></i>
                                Tgl Daftar
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
                    <i class="ph-magnifying-glass me-2"></i>
                    Tinjau Data Pelaksana
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-data" class="form-ajax">
                    <input type="hidden" name="table_id" id="table_id">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ph-info me-1"></i>
                                Informasi Detail
                            </h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered mb-0" id="info-detail">
                                <tbody>
                                    <tr>
                                        <th class="table-light fw-semibold align-top" width="20%">
                                            <i class="ph-tag me-1"></i>
                                            Kategori
                                        </th>
                                        <td class="align-top" width="80%" id="category" colspan="3"></td>
                                    </tr>
                                    <tr>
                                        <th class="table-light fw-semibold align-top" width="20%">
                                            <i class="ph-buildings me-1"></i>
                                            Nama
                                        </th>
                                        <td class="align-top" width="80%" id="name" colspan="3"></td>
                                    </tr>
                                    <tr>
                                        <th class="table-light fw-semibold align-top" width="20%">
                                            <i class="ph-tree-structure me-1"></i>
                                            Induk
                                        </th>
                                        <td class="align-top" width="80%" id="parent" colspan="3"></td>
                                    </tr>
                                    <tr>
                                        <th class="table-light fw-semibold align-top" width="20%">
                                            <i class="ph-buildings me-1"></i>
                                            Lembaga Penaung
                                        </th>
                                        <td class="align-top" width="80%" id="shelter_institution" colspan="3"></td>
                                    </tr>
                                    <tr>
                                        <th class="table-light fw-semibold align-top" width="20%">
                                            <i class="ph-buildings me-1"></i>
                                            Gedung
                                        </th>
                                        <td class="align-top" width="80%" id="building" colspan="3"></td>
                                    </tr>
                                    <tr>
                                        <th class="table-light fw-semibold align-top" width="20%">
                                            <i class="ph-map-pin me-1"></i>
                                            Lokasi
                                        </th>
                                        <td class="align-top" width="80%" id="location" colspan="3"></td>
                                    </tr>
                                    <tr>
                                        <th class="table-light fw-semibold align-top" width="20%">
                                            <i class="ph-envelope me-1"></i>
                                            Kode Pos
                                        </th>
                                        <td class="align-top" width="80%" id="postal_code" colspan="3"></td>
                                    </tr>
                                    <tr>
                                        <th class="table-light fw-semibold align-top" width="20%">
                                            <i class="ph-map-trifold me-1"></i>
                                            Alamat
                                        </th>
                                        <td class="align-top" width="80%" id="address" colspan="3"></td>
                                    </tr>
                                    <tr>
                                        <th class="table-light fw-semibold align-top" width="20%">
                                            <i class="ph-user me-1"></i>
                                            Admin
                                        </th>
                                        <td class="align-top" width="30%" id="admin"></td>
                                        <th class="table-light fw-semibold align-top" width="20%">Alternatif</th>
                                        <td class="align-top" width="30%" id="admin_alternative"></td>
                                    </tr>
                                    <tr>
                                        <th class="table-light fw-semibold align-top" width="20%">
                                            <i class="ph-envelope me-1"></i>
                                            Email
                                        </th>
                                        <td class="align-top" width="30%" id="email"></td>
                                        <th class="table-light fw-semibold align-top" width="20%">Alternatif</th>
                                        <td class="align-top" width="30%" id="email_alternative"></td>
                                    </tr>
                                    <tr>
                                        <th class="table-light fw-semibold align-top" width="20%">
                                            <i class="ph-phone me-1"></i>
                                            Telepon
                                        </th>
                                        <td class="align-top" width="30%" id="phone"></td>
                                        <th class="table-light fw-semibold align-top" width="20%">Alternatif</th>
                                        <td class="align-top" width="30%" id="phone_alternative"></td>
                                    </tr>
                                    <tr>
                                        <th class="table-light fw-semibold align-top" width="20%">
                                            <i class="ph-printer me-1"></i>
                                            Fax
                                        </th>
                                        <td class="align-top" width="30%" id="fax"></td>
                                        <th class="table-light fw-semibold align-top" width="20%">Alternatif</th>
                                        <td class="align-top" width="30%" id="fax_alternative"></td>
                                    </tr>
                                    <tr>
                                        <th class="table-light fw-semibold align-top" width="20%">
                                            <i class="ph-globe me-1"></i>
                                            Website
                                        </th>
                                        <td class="align-top" width="80%" id="website" colspan="3"></td>
                                    </tr>
                                    <tr>
                                        <th class="table-light fw-semibold align-top" width="20%">
                                            <i class="ph-book me-1"></i>
                                            Rata Penerbitan
                                        </th>
                                        <td class="align-top" width="80%" id="publication_average" colspan="3"></td>
                                    </tr>
                                    <tr>
                                        <th class="table-light fw-semibold align-top" width="20%">
                                            <i class="ph-calendar-plus me-1"></i>
                                            Tanggal Daftar
                                        </th>
                                        <td class="align-top" width="80%" id="registration_date" colspan="3"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header border-bottom">
                                    <h6 class="mb-0 fw-semibold">
                                        <i class="ph-file-pdf me-1 text-danger"></i>
                                        File Akta
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="ratio ratio-16x9">
                                        <iframe src="" id="file_deed" frameborder="0"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header border-bottom">
                                    <h6 class="mb-0 fw-semibold">
                                        <i class="ph-file-pdf me-1 text-danger"></i>
                                        File Pernyataan
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="ratio ratio-16x9">
                                        <iframe src="" id="file_statement" frameborder="0"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card border-0 shadow-sm">
                        <div class="card-header border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ph-check-circle me-1"></i>
                                Status Peninjauan
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="btn-group d-flex" role="group">
                                <input type="radio" class="btn-check" name="status" id="status-2" autocomplete="off" value="2" onchange="changeStatus()">
                                <label class="btn btn-outline-danger" for="status-2">
                                    <i class="ph-x-circle me-1"></i>
                                    Bermasalah
                                </label>
                                <input type="radio" class="btn-check" name="status" id="status-3" autocomplete="off" value="3" onchange="changeStatus()">
                                <label class="btn btn-outline-success" for="status-3">
                                    <i class="ph-check-circle me-1"></i>
                                    Terima
                                </label>
                            </div>
                            <div class="mt-3 d-none" id="description-wrapper">
                                <label class="form-label fw-semibold">
                                    <i class="ph-note me-1"></i>
                                    Keterangan Masalah
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" name="description" id="description" rows="4" placeholder="Jelaskan masalah yang ditemukan pada data pelaksana serah ini"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" id="btn-cancel" onclick="onCancel()">
                    <i class="ph-x me-1"></i>
                    Batal
                </button>
                <button class="btn btn-primary" id="btn-update" onclick="updateData()">
                    <i class="ph-check-circle me-1"></i>
                    Konfirmasi Peninjauan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        loadData();
    });

    function changeStatus() {
        var status = $('input[name="status"]:checked').val();

        if(status == 2) {
            $('#description-wrapper').removeClass('d-none');
        } else {
            $('#description-wrapper').addClass('d-none');
        }
    }

    function onReloadTable() {
        window.gDataTable.ajax.reload(null, false);
    }

    function onReset() {
        clearValidation();

        $('#modal-form').modal('hide');
        $('#form-data').trigger('reset');
        $('#file_deed').attr('src', '');
        $('#file_statement').attr('src', '');
        $('#description-wrapper').addClass('d-none');
    }

    function onCancel() {
        onReset();
    }

    function onUpdate() {
        onReset();

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
                url: '{{ url("coaching-supervision/review/datatable") }}',
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
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
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

    function updateRecordCount(count) {
        $('#record-count').text(count || 0);
    }

    function showDataUpdate(id) {
        $.ajax({
            url: '{{ url("coaching-supervision/review/show-data") }}',
            type: 'GET',
            dataType: 'JSON',
            data: {
                id: id
            },
            beforeSend: function() {
                onLoading('show', '.modal-content');
                onUpdate();

                $('input[name="status"]').prop('checked', false);
                $('#description-wrapper').addClass('d-none');
                $('#info-detail tbody tr td').text('');
            },
            success: function(response) {
                onLoading('close', '.modal-content');

                $('#table_id').val(response.ID);
                $('#category').text(response.NAME_PENERBIT_KATEGORI);
                $('#type').text(response.NAME_PENERBIT_JENIS);
                $('#name').text(response.NAME);
                $('#parent').text(response.NAME_PARENT);
                $('#shelter_institution').text(response.LEMBAGA_PENAUNG);
                $('#building').text(response.NAMA_GEDUNG);
                $('#admin').text(response.KONTAK1);
                $('#admin_alternative').text(response.KONTAK2);
                $('#location').text(response.NAMAPROPINSI + ' -> ' + response.NAMAKAB + ' -> ' + response.NAMAKEC + ' -> ' + response.NAMAKEL);
                $('#email').text(response.EMAIL1);
                $('#email_alternative').text(response.EMAIL2);
                $('#postal_code').text(response.KODEPOS);
                $('#phone').text(response.TELP1);
                $('#phone_alternative').text(response.TELP2);
                $('#address').text(response.ALAMAT);
                $('#fax').text(response.FAX1);
                $('#fax_alternative').text(response.FAX2);
                $('#website').text(response.WEBSITE);
                $('#publication_average').text(response.RATA_TERBITAN);
                $('#registration_date').text(moment(response.CREATEDATE).format('DD/MM/YYYY'));

                if(response.FILE_AKTE_NOTARIS) {
                    var paramFile = {
                        id: response.ID,
                        type: 'penerbit_akte_notaris',
                        filename: response.FILE_AKTE_NOTARIS,
                        v: '{{ Str::random(40) }}'
                    };

                    $('#file_deed').attr('src', `{{ url("stream-file") }}?${ $.param(paramFile) }`);
                }

                if(response.FILE_SP) {
                    var paramFile = {
                        id: response.ID,
                        type: 'penerbit_surat_pernyataan',
                        filename: response.FILE_SP,
                        v: '{{ Str::random(40) }}'
                    };

                    $('#file_statement').attr('src', `{{ url("stream-file") }}?${ $.param(paramFile) }`);
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
            url: '{{ url("coaching-supervision/review/update-data") }}',
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
                        url: '{{ url("coaching-supervision/review/destroy-data") }}',
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
