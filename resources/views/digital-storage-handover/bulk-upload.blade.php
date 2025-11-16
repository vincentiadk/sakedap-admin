<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Serah Simpan Digital - <span class="fw-normal">Unggah Banyak</span>
            </h4>
        </div>
        <div class="collapse d-lg-block my-lg-auto ms-lg-auto" id="page-header">
            <div class="d-sm-flex align-items-center mb-3 mb-lg-0 ms-lg-3">
                <div class="d-inline-flex mt-3 mt-sm-0">
                    <a href="{{ url('download/from-public') }}?path=assets/bulk-example.zip" target="_blank" class="btn btn-success me-2">
                        <i class="ph-file-zip me-1"></i>
                        Contoh Upload
                    </a>
                    <a href="{{ url('download/from-public') }}?path=assets/PANDUAN BULK UPLOAD SAKEDAP.pdf" target="_blank" class="btn btn-info me-2">
                        <i class="ph-file-pdf me-1"></i>
                        Panduan Bulk Upload
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="alert alert-danger d-none" id="validation-element">
        <ul class="mb-0" id="validation-data"></ul>
    </div>
    <form id="form-data">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-highlight nav-justified">
                    <li class="nav-item">
                        <a href="#nav-tabs-upload" class="nav-link active" data-bs-toggle="tab">Upload</a>
                    </li>
                    <li class="nav-item">
                        <a href="#nav-tabs-progress" class="nav-link" data-bs-toggle="tab" onclick="loadData()">Progress</a>
                    </li>
                </ul>
                <div class="tab-content flex-lg-fill mt-4">
                    <div class="tab-pane fade show active" id="nav-tabs-upload">
                        <div class="form-group">
                            <div class="input-group">
                                <span class="input-group-text">Jenis</span>
                                <select class="form-select" name="type" id="type" onchange="changeType()">
                                    <option value="">Pilih</option>
                                    <option value="bulk_non_serial">Non Serial</option>
                                    <option value="bulk_serial">Serial</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="param-id"></div>
                        <div class="form-group">
                            <input type="file" name="file" id="file">
                        </div>
                        <div><hr></div>
                        <div class="text-end">
                            <button type="button" class="btn btn-primary" onclick="submitted()">
                                <i class="ph-check me-1"></i>
                                Submit Data
                            </button>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-tabs-progress">
                        <table class="table table-bordered table-hover w-100 display" id="datatable-serverside">
                            <thead class="text-bg-light">
                                <tr>
                                    <th class="text-nowrap">No</th>
                                    <th class="text-nowrap">Detail</th>
                                    <th class="text-nowrap">File</th>
                                    <th class="text-nowrap">Mulai Proses</th>
                                    <th class="text-nowrap">Selesai Proses</th>
                                    <th class="text-nowrap">Status</th>
                                    <th class="text-nowrap">Tanggal</th>
                                </tr>
                            </thead>
                        </table>
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
                <h5 class="modal-title">Detail Bulk</h5>
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">
                    <i class="ph-x"></i>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <thead class="text-bg-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-nowrap">Judul</th>
                            <th class="text-nowrap">Keterangan</th>
                            <th class="text-nowrap">Status</th>
                        </tr>
                    </thead>
                    <tbody id="data-detail-bulk"></tbody>
                </table>
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
                { orderable: true, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
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
                                <td class="text-center">${ i + 1 }</td>
                                <td class="text-wrap">${ val.TITLE }</td>
                                <td class="text-wrap">${ val.DESCRIPTION }</td>
                                <td class="text-nowrap">${ val.STATUS }</td>
                            </tr>
                        `);
                    });
                } else {
                    $('#data-detail-bulk').html('<tr><td class="text-center" colspan="4">Tidak ada data</td></tr>');
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
                <input type="hidden" name="id" id="id">
                <input type="text" class="form-control" name="text" id="text" placeholder="Pilih Katalog" readonly>
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
