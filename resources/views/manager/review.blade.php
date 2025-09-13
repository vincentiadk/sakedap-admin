<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pengelola - <span class="fw-normal">Peninjauan</span>
            </h4>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover w-100 display" id="datatable-serverside">
                <thead class="text-bg-light">
                    <tr>
                        <th nowrap>No</th>
                        <th nowrap><i class="ph-gear"></i></th>
                        <th nowrap>Nama</th>
                        <th nowrap>Email</th>
                        <th nowrap>Kategori</th>
                        <th nowrap>Jenis</th>
                        <th nowrap>Telp</th>
                        <th nowrap>Tgl Daftar</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div id="modal-form" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tinjau Data</h5>
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">
                    <i class="ph-x"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-data" class="form-ajax">
                    <input type="hidden" name="table_id" id="table_id">
                    <table class="table table-bordered mb-4" id="info-detail">
                        <tbody>
                            <tr>
                                <th class="table-primary align-top" width="20%">Kategori</th>
                                <td class="align-top" width="80%" id="category" colspan="3"></td>
                            </tr>
                            <tr>
                                <th class="table-primary align-top" width="20%">Nama</th>
                                <td class="align-top" width="80%" id="name" colspan="3"></td>
                            </tr>
                            <tr>
                                <th class="table-primary align-top" width="20%">Induk</th>
                                <td class="align-top" width="80%" id="parent" colspan="3"></td>
                            </tr>
                            <tr>
                                <th class="table-primary align-top" width="20%">Lembaga Penaung</th>
                                <td class="align-top" width="80%" id="shelter_institution" colspan="3"></td>
                            </tr>
                            <tr>
                                <th class="table-primary align-top" width="20%">Gedung</th>
                                <td class="align-top" width="80%" id="building" colspan="3"></td>
                            </tr>
                            <tr>
                                <th class="table-primary align-top" width="20%">Lokasi</th>
                                <td class="align-top" width="80%" id="location" colspan="3"></td>
                            </tr>
                            <tr>
                                <th class="table-primary align-top" width="20%">Kode Pos</th>
                                <td class="align-top" width="80%" id="postal_code" colspan="3"></td>
                            </tr>
                            <tr>
                                <th class="table-primary align-top" width="20%">Alamat</th>
                                <td class="align-top" width="80%" id="address" colspan="3"></td>
                            </tr>
                            <tr>
                                <th class="table-primary align-top" width="20%">Admin</th>
                                <td class="align-top" width="30%" id="admin"></td>
                                <th class="table-primary align-top" width="20%">Alternatif</th>
                                <td class="align-top" width="30%" id="admin_alternative"></td>
                            </tr>
                            <tr>
                                <th class="table-primary align-top" width="20%">Email</th>
                                <td class="align-top" width="30%" id="email"></td>
                                <th class="table-primary align-top" width="20%">Alternatif</th>
                                <td class="align-top" width="30%" id="email_alternative"></td>
                            </tr>
                            <tr>
                                <th class="table-primary align-top" width="20%">Telepon</th>
                                <td class="align-top" width="30%" id="phone"></td>
                                <th class="table-primary align-top" width="20%">Alternatif</th>
                                <td class="align-top" width="30%" id="phone_alternative"></td>
                            </tr>
                            <tr>
                                <th class="table-primary align-top" width="20%">Fax</th>
                                <td class="align-top" width="30%" id="fax"></td>
                                <th class="table-primary align-top" width="20%">Alternatif</th>
                                <td class="align-top" width="30%" id="fax_alternative"></td>
                            </tr>
                            <tr>
                                <th class="table-primary align-top" width="20%">Website</th>
                                <td class="align-top" width="80%" id="website" colspan="3"></td>
                            </tr>
                            <tr>
                                <th class="table-primary align-top" width="20%">Rata Penerbitan</th>
                                <td class="align-top" width="80%" id="publication_average" colspan="3"></td>
                            </tr>
                            <tr>
                                <th class="table-primary align-top" width="20%">Tanggal Daftar</th>
                                <td class="align-top" width="80%" id="registration_date" colspan="3"></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="form-group mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="fw-bold border-bottom pb-2 mb-2">File Akta</div>
                                <div class="ratio ratio-16x9">
                                    <iframe src="" id="file_deed" frameborder="0"></iframe>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fw-bold border-bottom pb-2 mb-2">File Pernyataan</div>
                                <div class="ratio ratio-16x9">
                                    <iframe src="" id="file_statement" frameborder="0"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="btn-group d-flex">
                            <input type="radio" class="btn-check" name="status" id="status-2" autocomplete="off" value="2" onchange="changeStatus()">
                            <label class="btn btn-outline-danger" for="status-2">Bermasalah</label>
                            <input type="radio" class="btn-check" name="status" id="status-3" autocomplete="off" value="3" onchange="changeStatus()">
                            <label class="btn btn-outline-success" for="status-3">Terima</label>
                        </div>
                    </div>
                    <textarea class="form-control d-none" name="description" id="description" placeholder="Keterangan masalah"></textarea>
                </form>
            </div>
            <div class="modal-footer justify-content-end">
                <button class="btn btn-danger" id="btn-cancel" onclick="onCancel()">
                    <i class="ph-x me-1"></i>
                    Batal
                </button>
                <button class="btn btn-success" id="btn-update" onclick="updateData()">
                    <i class="ph-check me-1"></i>
                    Konfirmasi
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
            $('#description').removeClass('d-none');
        } else {
            $('#description').addClass('d-none');
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
                url: '{{ url("manager/review/datatable") }}',
                dataType: 'JSON',
                beforeSend: function() {
                    onLoading('show', '.dataTables_wrapper');
                },
                error: function(response) {
                    onLoading('close', '.dataTables_wrapper');

                    swalInit.fire({
                        html: '<b>' + response.responseJSON.exception + '</b><br>' + response.responseJSON.message,
                        icon: 'error',
                        showCloseButton: false
                    });
                }
            },
            columns: [
                { orderable: true, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
            ]
        }).on('draw.dt', function() {
            onLoading('close', '.dataTables_wrapper');
        });

        window.gDataTable.columns.adjust().draw();
    }

    function showDataUpdate(id) {
        $.ajax({
            url: '{{ url("manager/review/show-data") }}',
            type: 'GET',
            dataType: 'JSON',
            data: {
                id: id
            },
            beforeSend: function() {
                onLoading('show', '.modal-content');
                onUpdate();

                $('#status').prop('checked', false);
                $('#description').addClass('d-none');
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

                swalInit.fire({
                    html: '<b>' + response.responseJSON.exception + '</b><br>' + response.responseJSON.message,
                    icon: 'error',
                    showCloseButton: false
                });
            }
        });
    }

    function updateData() {
        $.ajax({
            url: '{{ url("manager/review/update-data") }}',
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

                swalInit.fire({
                    html: '<b>' + response.responseJSON.exception + '</b><br>' + response.responseJSON.message,
                    icon: 'error',
                    showCloseButton: false
                });
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
                        url: '{{ url("manager/review/destroy-data") }}',
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

                            swalInit.fire({
                                html: '<b>' + response.responseJSON.exception + '</b><br>' + response.responseJSON.message,
                                icon: 'error',
                                showCloseButton: false
                            });
                        }
                    });
                })
            ]
        }).show();
    }
</script>
