<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Serah Simpan Digital - <span class="fw-normal">Unggah Banyak</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary p-2 bg-opacity-10 text-primary">
                    <i class="ph-upload me-1"></i>
                    Bulk Upload
                </span>
                <a href="{{ url('download/from-public') }}?path=assets/bulk-example.zip" target="_blank" class="btn btn-success btn-sm">
                    <i class="ph-file-zip me-1"></i>
                    Contoh Upload
                </a>
                <a href="{{ url('download/from-public') }}?path=assets/PANDUAN BULK UPLOAD SAKEDAP.pdf" target="_blank" class="btn btn-info btn-sm">
                    <i class="ph-file-pdf me-1"></i>
                    Panduan
                </a>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="alert alert-danger border-0 d-none" id="validation-element">
        <div class="d-flex align-items-center mb-2">
            <i class="ph-warning-circle me-2 fs-4"></i>
            <h6 class="mb-0 fw-semibold">Terdapat Kesalahan Validasi</h6>
        </div>
        <ul class="mb-0" id="validation-data"></ul>
    </div>
    <form id="form-data">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <ul class="nav nav-tabs nav-tabs-highlight card-header-tabs mb-0">
                    <li class="nav-item">
                        <a href="#nav-tabs-upload" class="nav-link active" data-bs-toggle="tab">
                            <i class="ph-upload-simple me-2"></i>
                            Upload
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#nav-tabs-progress" class="nav-link" data-bs-toggle="tab" onclick="loadData()">
                            <i class="ph-clock-clockwise me-2"></i>
                            Progress
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="nav-tabs-upload">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="ph-list me-1"></i>
                                    Jenis Upload
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" name="type" id="type" onchange="changeType()">
                                    <option value="">Pilih Jenis Upload</option>
                                    <option value="bulk_non_serial">Non Serial</option>
                                    <option value="bulk_serial">Serial</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <div id="param-id"></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="ph-file-zip me-1"></i>
                                    File ZIP
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="file" name="file" id="file">
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light" onclick="location.reload()">
                                <i class="ph-arrow-counter-clockwise me-1"></i>
                                Reset
                            </button>
                            <button type="button" class="btn btn-primary" onclick="submitted()">
                                <i class="ph-check-circle me-1"></i>
                                Submit Data
                            </button>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-tabs-progress">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered display nowrap w-100" id="datatable-serverside">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center text-nowrap" style="width: 60px">
                                            <i class="ph-hash"></i>
                                        </th>
                                        <th class="text-center text-nowrap" style="width: 100px">
                                            <i class="ph-info"></i>
                                            Detail
                                        </th>
                                        <th class="text-nowrap" style="min-width: 200px">
                                            <i class="ph-file-zip me-1"></i>
                                            File
                                        </th>
                                        <th class="text-center text-nowrap" style="min-width: 150px">
                                            <i class="ph-play-circle me-1"></i>
                                            Mulai Proses
                                        </th>
                                        <th class="text-center text-nowrap" style="min-width: 150px">
                                            <i class="ph-check-circle me-1"></i>
                                            Selesai Proses
                                        </th>
                                        <th class="text-center text-nowrap" style="min-width: 120px">
                                            <i class="ph-flag me-1"></i>
                                            Status
                                        </th>
                                        <th class="text-center text-nowrap" style="min-width: 130px">
                                            <i class="ph-calendar me-1"></i>
                                            Tanggal
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div id="modal-bulk" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ph-info-circle me-2"></i>
                    Detail Bulk Upload
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center text-nowrap" style="width: 60px">
                                    <i class="ph-hash"></i>
                                </th>
                                <th class="text-nowrap" style="min-width: 250px">
                                    <i class="ph-book me-1"></i>
                                    Judul
                                </th>
                                <th class="text-nowrap" style="min-width: 200px">
                                    <i class="ph-note me-1"></i>
                                    Keterangan
                                </th>
                                <th class="text-center text-nowrap" style="min-width: 120px">
                                    <i class="ph-flag me-1"></i>
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody id="data-detail-bulk"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        dragAndDropFile('#file', {
            maxFileCount: 1,
            autoReplace: true,
            allowedFileExtensions: ['zip']
        });
    });

    function loadData() {
        window.gDataTable = $('#datatable-serverside').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            scrollX: true,
            destroy: true,
            order: [[0, 'desc']],
            ajax: {
                url: '{{ url("digital-storage-handover/bulk-upload/datatable-bulk") }}',
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
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-center' },
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

    function showData(id) {
        $.ajax({
            url: '{{ url("digital-storage-handover/bulk-upload/detail-bulk") }}',
            type: 'GET',
            dataType: 'JSON',
            data: {
                id: id
            },
            beforeSend: function() {
                onLoading('show', '.modal-content');

                $('#modal-bulk').modal('show');
                $('#data-detail-bulk').html('');
            },
            success: function(response) {
                onLoading('close', '.modal-content');

                if(response.length > 0 && response) {
                    $.each(response, function(i, val) {
                        $('#data-detail-bulk').append(`
                            <tr>
                                <td class="text-center fw-semibold">${ i + 1 }</td>
                                <td class="text-wrap">${ val.TITLE }</td>
                                <td class="text-wrap">${ val.DESCRIPTION }</td>
                                <td class="text-center text-nowrap">${ val.STATUS }</td>
                            </tr>
                        `);
                    });
                } else {
                    $('#data-detail-bulk').html(`
                        <tr>
                            <td class="text-center" colspan="4">
                                <div class="py-3">
                                    <i class="ph-file-x fs-2 text-muted mb-2 d-block"></i>
                                    <span class="text-muted">Tidak ada data</span>
                                </div>
                            </td>
                        </tr>
                    `);
                }
            },
            error: function(response) {
                onLoading('close', '.modal-content');
                responseError(response);
            }
        });
    }

    function changeType() {
        var type = $('#type').val();

        $('#btn-template').html('');
        $('#param-id').html('');

        if(type == 'bulk_non_serial') {
            $('#param-id').html(`
                <label class="form-label fw-semibold">
                    <i class="ph-user-circle me-1"></i>
                    Pelaksana Serah
                    <span class="text-danger">*</span>
                </label>
                <select class="form-select" name="id" id="id" data-placeholder="Pilih Pelaksana Serah"></select>
            `);

            if(parseInt('{{ Main::isNotSuperAdmin() }}') === 1) {
                select2Serverside('#id', 'executor');
            } else {
                select2Serverside('#id', 'executor', {
                    province_id: '{{ session("province_id") }}'
                });
            }
        } else if(type == 'bulk_serial') {
            $('#param-id').html(`
                <label class="form-label fw-semibold">
                    <i class="ph-book-open me-1"></i>
                    Katalog Induk
                    <span class="text-danger">*</span>
                </label>
                <input type="hidden" name="id" id="id">
                <input type="text" class="form-control" name="text" id="text" placeholder="Klik untuk memilih katalog induk" readonly>
            `);

            lookupCatalogParent('#text', '#id');
        }
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

    function submitted() {
        $.ajax({
            url: '{{ url("digital-storage-handover/bulk-upload/submitted") }}',
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
                onLoading('show', 'body');
                clearValidation();
            },
            success: function(response) {
                onLoading('close', 'body');

                if(response.code == 200) {
                    swalInit.fire({
                        title: 'Berhasil',
                        text: response.message,
                        icon: 'success',
                        showDenyButton: false,
                        showCancelButton: false,
                        confirmButtonText: 'Oke',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            onLoading('show', 'body');

                            location.href = '{{ url("digital-storage-handover/bulk-upload") }}';
                        }
                    });
                } else if(response.code == 400) {
                    onLoading('close', 'body');
                    $('.btn-to-top button').click();
                    showValidation(response.error);
                } else {
                    swalInit.fire({
                        title: 'Oops ...',
                        text: response.message,
                        icon: 'info',
                        showCloseButton: true
                    });
                }
            },
            error: function(response) {
                onLoading('close', 'body');
                responseError(response);
            }
        });
    }
</script>
