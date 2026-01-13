<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pengawasan & Pembinaan - <span class="fw-normal">Daftar Pelaksana Serah</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <span class="badge bg-info p-2 bg-opacity-10 text-info">
                    <i class="ph-users me-1"></i>
                    Daftar Pelaksana
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
                    <h6 class="mb-0 fw-semibold">Daftar Pelaksana Serah</h6>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary" id="total-records">
                        <i class="ph-list-checks me-1"></i>
                        <span id="record-count">0</span> Data
                    </span>
                    <div class="input-group" style="width: auto;">
                        <span class="input-group-text">
                            <i class="ph-funnel"></i>
                        </span>
                        <select class="form-select" name="filter_status" id="filter_status" onchange="loadData()" style="min-width: 150px;">
                            <option value="">Semua Status</option>
                            <option value="1">Aktif</option>
                            <option value="2">Blokir</option>
                            <option value="3">Usulan Blokir</option>
                        </select>
                    </div>
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
                            <th class="text-center text-nowrap" style="min-width: 100px">
                                <i class="ph-flag me-1"></i>
                                Tanda
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
                            <th class="text-center text-nowrap" style="min-width: 130px">
                                <i class="ph-calendar-check me-1"></i>
                                Tgl Diterima
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
                    <i class="ph-note-pencil me-2"></i>
                    Edit Data Pelaksana
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-data" class="form-ajax">
                    <input type="hidden" name="table_id" id="table_id">
                    <ul class="nav nav-tabs nav-tabs-highlight mb-3">
                        <li class="nav-item">
                            <a href="#nav-tabs-data" class="nav-link active" data-bs-toggle="tab">
                                <i class="ph-info me-2"></i>
                                Data
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#nav-tabs-file" class="nav-link" data-bs-toggle="tab">
                                <i class="ph-file-pdf me-2"></i>
                                File
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#nav-tabs-api-key" class="nav-link" data-bs-toggle="tab">
                                <i class="ph-key me-2"></i>
                                API Key
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="nav-tabs-data">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <label class="form-label fw-semibold">
                                        <i class="ph-tag me-1"></i>
                                        Kategori
                                    </label>
                                    <select class="form-select select2-basic" name="category_id" id="category_id" data-placeholder="Pilih Kategori" data-dropdown-parent="#modal-form">
                                        <option value="">Tidak Ada</option>
                                        @foreach($category as $c)
                                            <option value="{{ $c->ID }}">{{ $c->NAME }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label fw-semibold">
                                        <i class="ph-list me-1"></i>
                                        Jenis
                                    </label>
                                    <select class="form-select select2-basic" name="type_id" id="type_id" data-placeholder="Pilih Jenis" data-dropdown-parent="#modal-form">
                                        <option value="">Tidak Ada</option>
                                        @foreach($type as $t)
                                            <option value="{{ $t->ID }}">{{ $t->NAME }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label fw-semibold">
                                        <i class="ph-buildings me-1"></i>
                                        Nama
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="name" id="name" placeholder="Nama pelaksana serah" disabled>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label fw-semibold">
                                        <i class="ph-tree-structure me-1"></i>
                                        Induk
                                    </label>
                                    <select class="form-select" name="parent_id" id="parent_id" data-placeholder="Pilih Induk" data-dropdown-parent="#modal-form"></select>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label fw-semibold">
                                        <i class="ph-buildings me-1"></i>
                                        Lembaga Penaung
                                    </label>
                                    <input type="text" class="form-control" name="shelter_institution" id="shelter_institution" placeholder="Lembaga penaung">
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label fw-semibold">
                                        <i class="ph-buildings me-1"></i>
                                        Gedung
                                    </label>
                                    <input type="text" class="form-control" name="building" id="building" placeholder="Nama gedung">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        <i class="ph-user me-1"></i>
                                        Admin
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="admin" id="admin" placeholder="Admin utama">
                                        <span class="input-group-text">
                                            <i class="ph-swap me-1"></i>
                                        </span>
                                        <input type="text" class="form-control" name="admin_alternative" id="admin_alternative" placeholder="Admin alternatif">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        <i class="ph-envelope me-1"></i>
                                        Email
                                    </label>
                                    <div class="input-group">
                                        <input type="email" class="form-control" name="email" id="email" placeholder="Email utama">
                                        <span class="input-group-text">
                                            <i class="ph-swap me-1"></i>
                                        </span>
                                        <input type="email" class="form-control" name="email_alternative" id="email_alternative" placeholder="Email alternatif">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        <i class="ph-phone me-1"></i>
                                        Telepon
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="phone" id="phone" placeholder="Telepon utama">
                                        <span class="input-group-text">
                                            <i class="ph-swap me-1"></i>
                                        </span>
                                        <input type="text" class="form-control" name="phone_alternative" id="phone_alternative" placeholder="Telepon alternatif">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        <i class="ph-printer me-1"></i>
                                        Fax
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="fax" id="fax" placeholder="Fax utama">
                                        <span class="input-group-text">
                                            <i class="ph-swap me-1"></i>
                                        </span>
                                        <input type="text" class="form-control" name="fax_alternative" id="fax_alternative" placeholder="Fax alternatif">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label fw-semibold">
                                        <i class="ph-map-pin me-1"></i>
                                        Lokasi
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" name="location_id" id="location_id" data-placeholder="Pilih Lokasi" data-dropdown-parent="#modal-form"></select>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label fw-semibold">
                                        <i class="ph-envelope me-1"></i>
                                        Kode Pos
                                    </label>
                                    <input type="text" class="form-control" name="postal_code" id="postal_code" placeholder="Kode pos">
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label fw-semibold">
                                        <i class="ph-map-trifold me-1"></i>
                                        Alamat
                                    </label>
                                    <input type="text" class="form-control" name="address" id="address" placeholder="Alamat lengkap" disabled>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label fw-semibold">
                                        <i class="ph-globe me-1"></i>
                                        Website
                                    </label>
                                    <input type="url" class="form-control" name="website" id="website" placeholder="https://example.com">
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label fw-semibold">
                                        <i class="ph-book me-1"></i>
                                        Rata - Rata Terbitan
                                    </label>
                                    <input type="number" class="form-control" name="publication_average" id="publication_average" placeholder="Jumlah terbitan">
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label fw-semibold">
                                        <i class="ph-lock me-1"></i>
                                        Kunci
                                    </label>
                                    <select class="form-select" name="is_lock" id="is_lock">
                                        <option value="1">Ya</option>
                                        <option value="0">Tidak</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="nav-tabs-file">
                            <div class="row g-3">
                                <div class="col-lg-6">
                                    <div class="card border-0 bg-light">
                                        <div class="card-header border-bottom bg-white">
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
                                    <div class="card border-0 bg-light">
                                        <div class="card-header border-bottom bg-white">
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
                        </div>
                        <div class="tab-pane fade" id="nav-tabs-api-key">
                            <div class="card border-0 bg-light mb-3">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" name="is_api_enable" id="is_api_enable" onchange="tokenable('status')">
                                            <label class="form-check-label fw-semibold" for="is_api_enable">
                                                <i class="ph-power me-1"></i>
                                                Status API Key
                                            </label>
                                        </div>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="tokenable('generate')">
                                            <i class="ph-arrows-clockwise me-1"></i>
                                            Generate Baru
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        <i class="ph-calendar-x me-1"></i>
                                        Tanggal Expired
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ph-calendar-blank"></i>
                                        </span>
                                        <input type="text" class="form-control" name="jwt_expired" id="jwt_expired" placeholder="Pilih tanggal expired" readonly>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        <i class="ph-key me-1"></i>
                                        JWT Token
                                    </label>
                                    <textarea class="form-control" name="jwt" id="jwt" rows="3" style="resize:none;" placeholder="JWT Token" readonly></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        <i class="ph-key me-1"></i>
                                        X-API-Key
                                    </label>
                                    <textarea class="form-control" name="x_api_key" id="x_api_key" rows="3" style="resize:none;" placeholder="X-API-Key" readonly></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" id="btn-cancel" onclick="onCancel()">
                    <i class="ph-x me-1"></i>
                    Batal
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

        datePickerSingle('#jwt_expired');
        select2Serverside('#parent_id', 'executor');
        select2Serverside('#location_id', 'location');
    });

    function tokenable(type) {
        if(type == 'generate') {
            swalInit.fire({
                title: 'Generate Token Baru',
                text: 'Anda yakin ingin generate token baru?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, generate',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#jwt').val(randomString(64));
                    $('#x_api_key').val(randomString(64));
                }
            });
        } else if(type == 'status') {
            var isAPIEnable = $('#is_api_enable').is(':checked');

            if(isAPIEnable) {
                var titleText = 'mengaktifkan';
                var confirmValue = true;
                var cancelValue = false;
            } else {
                var titleText = 'menonaktifkan';
                var confirmValue = false;
                var cancelValue = true;
            }

            swalInit.fire({
                title: 'Anda yakin ingin ' + titleText + ' api key?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                allowOutsideClick: false,
                allowEscapeKey: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#is_api_enable').prop('checked', confirmValue);
                } else {
                    $('#is_api_enable').prop('checked', cancelValue);
                }
            });

            if($('#is_api_enable').is(':checked') && $('#jwt').val() == '' & $('#x_api_key').val() == '') {
                tokenable('generate');
            }
        }
    }

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

        $('a[href="#nav-tabs-data"]').removeClass('active');
        $('a[href="#nav-tabs-file"]').removeClass('active');
        $('a[href="#nav-tabs-api-key"]').removeClass('active');

        $('#nav-tabs-data').removeClass('show active');
        $('#nav-tabs-file').removeClass('show active');
        $('#nav-tabs-api-key').removeClass('show active');

        $('a[href="#nav-tabs-data"]').addClass('active');
        $('#nav-tabs-data').addClass('show active');
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
                url: '{{ url("coaching-supervision/executor-list/datatable") }}',
                dataType: 'JSON',
                data: {
                    status: $('#filter_status').val()
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
                { orderable: false, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
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
            url: '{{ url("coaching-supervision/executor-list/show-data") }}',
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
                $('#jwt').val(response.JWT);
                $('#x_api_key').val(response.X_API_KEY);
                $('#jwt_expired').val(response.JWT_EXPIRED ? moment(response.JWT_EXPIRED).format('YYYY/MM/DD') : '');

                if(response.IS_API_ENABLE == 1) {
                    $('#is_api_enable').prop('checked', true);
                } else {
                    $('#is_api_enable').prop('checked', false);
                }

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
            url: '{{ url("coaching-supervision/executor-list/update-data") }}',
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

    function sendEmailResetPassword(id) {
        var notyConfirm = new Noty({
            text: '<div class="mb-3"><h5 class="text-dark">Kirim Email Reset Password?</h5><span class="text-muted">Pelaksana akan mendapatkan link dari email untuk melakukan reset password</span></div>',
            timeout: false,
            modal: true,
            layout: 'center',
            closeWith: 'button',
            type: 'confirm',
            buttons: [
                Noty.button('Tidak', 'btn btn-light', function () {
                    notyConfirm.close();
                }),
                Noty.button('Kirim', 'btn btn-success ms-2', function () {
                    $.ajax({
                        url: '{{ url("coaching-supervision/executor-list/send-email-reset-password") }}',
                        type: 'POST',
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
                        url: '{{ url("coaching-supervision/executor-list/destroy-data") }}',
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
