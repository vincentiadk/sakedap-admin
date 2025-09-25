<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pelaksana Serah - <span class="fw-normal">Pengelolaan</span>
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
                        <th class="text-nowrap">No</th>
                        <th class="text-nowrap"><i class="ph-gear"></i></th>
                        <th class="text-nowrap">Tanda</th>
                        <th class="text-nowrap">Nama</th>
                        <th class="text-nowrap">Email</th>
                        <th class="text-nowrap">Kategori</th>
                        <th class="text-nowrap">Jenis</th>
                        <th class="text-nowrap">Telp</th>
                        <th class="text-nowrap">Tgl Daftar</th>
                        <th class="text-nowrap">Tgl Diterima</th>
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
                <h5 class="modal-title">Edit Data</h5>
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">
                    <i class="ph-x"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-data" class="form-ajax">
                    <input type="hidden" name="table_id" id="table_id">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Kategori :</label>
                                <select class="form-select select2-basic" name="category_id" id="category_id" data-placeholder="Tidak Ada" data-dropdown-parent="#modal-form">
                                    <option value="">Tidak Ada</option>
                                    @foreach($category as $c)
                                        <option value="{{ $c->ID }}">{{ $c->NAME }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nama : <span class="text-danger fw-bold">*</span></label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="...................." disabled>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Lembaga Penaung :</label>
                                <input type="text" class="form-control" name="shelter_institution" id="shelter_institution" placeholder="....................">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Admin :</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="admin" id="admin" placeholder="....................">
                                    <span class="input-group-text">Alternatif</span>
                                    <input type="text" class="form-control" name="admin_alternative" id="admin_alternative" placeholder="....................">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email :</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="email" id="email" placeholder="....................">
                                    <span class="input-group-text">Alternatif</span>
                                    <input type="text" class="form-control" name="email_alternative" id="email_alternative" placeholder="....................">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Telepon :</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="phone" id="phone" placeholder="....................">
                                    <span class="input-group-text">Alternatif</span>
                                    <input type="text" class="form-control" name="phone_alternative" id="phone_alternative" placeholder="....................">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Fax :</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="fax" id="fax" placeholder="....................">
                                    <span class="input-group-text">Alternatif</span>
                                    <input type="text" class="form-control" name="fax_alternative" id="fax_alternative" placeholder="....................">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Rata - Rata Terbitan :</label>
                                <input type="number" class="form-control" name="publication_average" id="publication_average" placeholder="....................">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Jenis :</label>
                                <select class="form-select select2-basic" name="type_id" id="type_id" data-placeholder="Tidak Ada" data-dropdown-parent="#modal-form">
                                    <option value=""></option>
                                    @foreach($type as $t)
                                        <option value="{{ $t->ID }}">{{ $t->NAME }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Induk :</label>
                                <select class="form-select" name="parent_id" id="parent_id" data-placeholder="Tidak Ada" data-dropdown-parent="#modal-form"></select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Gedung :</label>
                                <input type="text" class="form-control" name="building" id="building" placeholder="....................">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Lokasi : <span class="text-danger fw-bold">*</span></label>
                                <select class="form-select" name="location_id" id="location_id" data-placeholder="Tidak Ada" data-dropdown-parent="#modal-form"></select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Kode Pos :</label>
                                <input type="text" class="form-control" name="postal_code" id="postal_code" placeholder="....................">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Alamat :</label>
                                <input type="text" class="form-control" name="address" id="address" placeholder="...................." disabled>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Website :</label>
                                <input type="text" class="form-control" name="website" id="website" placeholder="....................">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Kunci :</label>
                                <select class="form-select" name="is_lock" id="is_lock">
                                    <option value="1">Ya</option>
                                    <option value="0">Tidak</option>
                                </select>
                            </div>
                        </div>
                    </div>
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
                </form>
            </div>
            <div class="modal-footer justify-content-end">
                <button class="btn btn-danger" id="btn-cancel" onclick="onCancel()">
                    <i class="ph-x me-1"></i>
                    Batalkan Perubahan
                </button>
                <button class="btn btn-warning" id="btn-update" onclick="updateData()">
                    <i class="ph-floppy-disk me-1"></i>
                    Simpan Perubahan Data
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        loadData();

        select2Serverside('#parent_id', 'executor');
        select2Serverside('#location_id', 'location');
    });

    function onReloadTable() {
        window.gDataTable.ajax.reload(null, false);
    }

    function onReset() {
        clearValidation();

        $('#modal-form').modal('hide');
        $('#form-data').trigger('reset');
        $('#category_id').val('').change();
        $('#type_id').val('').change();
        $('#parent_id').val('').change();
        $('#location_id').val('').change();
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
                url: '{{ url("executor/manage/datatable") }}',
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
                { orderable: false, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
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
            url: '{{ url("executor/manage/show-data") }}',
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
                $('#category_id').val(response.KATEGORI_ID).change();
                $('#type_id').val(response.JENIS_ID).change();
                $('#name').val(response.NAME);
                $('#parent_id').html('<option value="' + response.PARENT_ID + '" selected>' + response.NAME_PARENT + '</option>');
                $('#shelter_institution').val(response.LEMBAGA_PENAUNG);
                $('#building').val(response.NAMA_GEDUNG);
                $('#admin').val(response.KONTAK1);
                $('#admin_alternative').val(response.KONTAK2);
                $('#location_id').html('<option value="' + response.VILLAGE_ID + '" selected>' + response.NAMAPROPINSI + ' -> ' + response.NAMAKAB + ' -> ' + response.NAMAKEC + ' -> ' + response.NAMAKEL + '</option>');
                $('#email').val(response.EMAIL1);
                $('#email_alternative').val(response.EMAIL2);
                $('#postal_code').val(response.KODEPOS);
                $('#phone').val(response.TELP1);
                $('#phone_alternative').val(response.TELP2);
                $('#address').val(response.ALAMAT);
                $('#fax').val(response.FAX1);
                $('#fax_alternative').val(response.FAX2);
                $('#website').val(response.WEBSITE);
                $('#publication_average').val(response.RATA_TERBITAN);
                $('#is_lock').val(response.IS_LOCK == 1 ? 1 : 0);

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
            url: '{{ url("executor/manage/update-data") }}',
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
                        url: '{{ url("executor/manage/destroy-data") }}',
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
