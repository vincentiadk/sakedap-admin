<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Penerimaan Fisik - <span class="fw-normal">Upload Resi via Excel</span>
            </h4>
        </div>
    </div>
</div>

<div class="content pt-0">
    <div class="card mb-4">
        <form id="receiptUploadForm" enctype="multipart/form-data">
            @csrf
            <div class="card-body">

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="ph-user-circle me-2 text-primary"></i>
                            <h6 class="mb-0 fw-semibold">
                                Pelaksana Serah <span class="text-danger">*</span>
                            </h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <select class="form-select" name="pelaksana_serah_id" id="pelaksana_serah_id" data-placeholder="Pilih Pelaksana Serah"></select>
                    </div>
                </div>

                <div class="row g-3 mb-3" id="receiptArea" style="display:none;">
                    <div class="col-md-4">
                        <label class="form-label">Nomor Resi <span class="text-danger">*</span></label>
                        <input type="text" name="receipt_no" id="receipt_no" class="form-control" placeholder="Masukkan nomor resi">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tipe Pengiriman</label>
                        <select name="type_of_delivery" id="type_of_delivery" class="form-select">
                            <option value="EKSPEDISI">Ekspedisi</option>
                            <option value="DATANG_LANGSUNG">Datang Langsung</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Jasa Pengiriman ID</label>
                        <input type="number" name="jasa_pengiriman_id" id="jasa_pengiriman_id" class="form-control" placeholder="Opsional">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Catatan</label>
                        <textarea name="note" id="note" class="form-control" rows="2" placeholder="Opsional"></textarea>
                    </div>
                </div>

                <div class="mb-3" id="excelArea" style="display:none;">
                    <label class="form-label">Upload File Excel</label>

                    <div id="dropArea"
                        class="border rounded text-center position-relative p-4"
                        style="border-style:dashed; cursor:pointer; min-height:220px; background:#fafafa;">

                        <div id="dropPlaceholder" class="d-flex flex-column justify-content-center align-items-center h-100">
                            <div style="font-size:18px; font-weight:500;">Drag & drop file Excel di sini</div>
                            <div class="text-muted mt-2">atau klik untuk pilih file</div>
                            <div class="text-muted small mt-2">Format: ISBN, Judul, Jumlah Pengiriman</div>
                        </div>

                        <div id="filePreview" class="d-none">
                            <div class="d-flex justify-content-center">
                                <div class="border rounded px-3 py-2 bg-white d-inline-flex align-items-center shadow-sm"
                                    style="gap:10px; max-width:420px;">
                                    <div style="font-size:24px;">📄</div>
                                    <div class="text-start">
                                        <div id="fileName" class="fw-bold text-success"></div>
                                        <div id="fileSize" class="text-muted small"></div>
                                    </div>
                                    <button type="button" id="removeFile" class="btn btn-sm btn-light border ms-2" style="z-index:99">✕</button>
                                </div>
                            </div>
                        </div>

                        <input type="file"
                            name="excel_file"
                            id="excel_file"
                            accept=".xls,.xlsx"
                            style="position:absolute; inset:0; width:100%; height:100%; opacity:0; cursor:pointer;">
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary" id="btnUpload" disabled>Upload Excel</button>
            </div>
        </form>
    </div>

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
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-semibold">Tanggal</label>
                            <input type="text" class="form-control" name="date" id="date" placeholder="Pilih tanggal">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-semibold">Pelaksana Serah</label>
                            <select class="form-select" name="executor_id" id="executor_id" data-placeholder="Semua Pelaksana"></select>
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-semibold">Nomor Resi</label>
                            <input type="text" class="form-control" name="receipt_no" id="filter_receipt_no" placeholder="Cari nomor resi">
                        </div>

                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" name="status" id="status">
                                <option value="">Semua</option>
                                <option>queued</option>
                                <option>processing</option>
                                <option>success</option>
                                <option>partial_success</option>
                                <option>failed</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-footer border-top">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ url('physical-delivery/upload-receipt') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
                        Reset Filter
                    </a>
                    <button type="button" class="btn btn-primary" onclick="loadData()">
                        Cari Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4" id="progressCard" style="display:none;">
        <div class="card-header">Progress Upload Resi</div>
        <div class="card-body">
            <div class="progress mb-3" style="height: 25px;">
                <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated"
                     role="progressbar" style="width:0%">0%</div>
            </div>

            <div class="row">
                <div class="col-md-3"><strong>Status:</strong> <span id="statusText">-</span></div>
                <div class="col-md-3"><strong>Total:</strong> <span id="totalRows">0</span></div>
                <div class="col-md-3"><strong>Diproses:</strong> <span id="processedRows">0</span></div>
                <div class="col-md-3"><strong>Berhasil:</strong> <span id="successRows">0</span></div>
            </div>

            <div class="row mt-2">
                <div class="col-md-3"><strong>Gagal:</strong> <span id="failedRows">0</span></div>
                <div class="col-md-9"><strong>Catatan:</strong> <span id="notesText">-</span></div>
            </div>

            <div class="mt-3">
                <a href="#" id="detailLink" class="btn btn-outline-secondary btn-sm" target="_blank">Lihat Detail Histori</a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ph-clipboard-text me-1 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Riwayat Upload Resi</h6>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary">
                    <span id="record-count">0</span> Data
                </span>
            </div>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-hover table-bordered display nowrap w-100" id="datatable-serverside">
                <thead>
                    <tr>
                        <th>Aksi</th>
                        <th>ID</th>
                        <th>Pelaksana Serah</th>
                        <th>Nomor Resi</th>
                        <th>File Excel</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Success</th>
                        <th>Failed</th>
                        <th>Tanggal Unggah</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    datePickerBasic('#date');

    if(parseInt('{{ Main::isPerpusnas() }}') == 0) {
        select2Serverside('#executor_id', 'executor', {
            province_id: '{{ session("province_id") }}',
        });

        select2Serverside('#pelaksana_serah_id', 'executor', {
            province_id: '{{ session("province_id") }}',
        });
    } else {
        select2Serverside('#executor_id', 'executor');
        select2Serverside('#pelaksana_serah_id', 'executor');
    }

    loadData();

    let progressInterval = null;
    let isSubmitting = false;

    function formatBytes(bytes) {
        if (!bytes || bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function setUploadState(loading = false) {
        isSubmitting = loading;

        $('#btnUpload')
            .prop('disabled', loading)
            .html(loading
                ? '<i class="ph-spinner spinner me-1"></i> Sedang upload...'
                : 'Upload Excel');

        $('#pelaksana_serah_id').prop('disabled', loading);
        $('#receipt_no').prop('disabled', loading);
        $('#type_of_delivery').prop('disabled', loading);
        $('#jasa_pengiriman_id').prop('disabled', loading);
        $('#note').prop('disabled', loading);
        $('#excel_file').prop('disabled', loading);
        $('#removeFile').prop('disabled', loading);

        $('#dropArea').css({
            'pointer-events': loading ? 'none' : '',
            'opacity': loading ? '0.7' : ''
        });
    }

    $('#pelaksana_serah_id').on('change', function () {
        const val = $(this).val();

        if (val) {
            $('#receiptArea').show();
            $('#excelArea').show();
            $('#btnUpload').prop('disabled', false);
        } else {
            $('#receiptArea').hide();
            $('#excelArea').hide();
            $('#btnUpload').prop('disabled', true);
        }
    });

    function showFilePreview(file) {
        $('#fileName').text(file.name);
        $('#fileSize').text(formatBytes(file.size));
        $('#dropPlaceholder').addClass('d-none');
        $('#filePreview').removeClass('d-none');
    }

    function resetFilePreview() {
        $('#excel_file').val('');
        $('#fileName').text('');
        $('#fileSize').text('');
        $('#filePreview').addClass('d-none');
        $('#dropPlaceholder').removeClass('d-none');
    }

    function resetReceiptForm() {
        $('#receiptUploadForm')[0].reset();
        $('#pelaksana_serah_id').val(null).trigger('change');
        resetFilePreview();
    }

    $('#excel_file').on('change', function () {
        if (this.files && this.files.length > 0) {
            showFilePreview(this.files[0]);
        }
    });

    $('#removeFile').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (isSubmitting) return;
        resetFilePreview();
    });

    $('#dropArea').on('dragover', function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (isSubmitting) return;
        $(this).addClass('bg-light');
    });

    $('#dropArea').on('dragleave', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('bg-light');
    });

    $('#dropArea').on('drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (isSubmitting) return;

        $(this).removeClass('bg-light');

        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            $('#excel_file')[0].files = files;
            showFilePreview(files[0]);
        }
    });

    $('#receiptUploadForm').on('submit', function (e) {
        e.preventDefault();

        if (isSubmitting) return;

        let formData = new FormData(this);

        $('.invalid-feedback').remove();
        $('.is-invalid').removeClass('is-invalid');

        setUploadState(true);

        Swal.fire({
            title: 'Sedang upload...',
            text: 'Mohon tunggu, file sedang dikirim.',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: "{{ url('physical-delivery/upload-receipt/store') }}",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                Swal.close();

                $('#progressCard').show();
                $('#detailLink').attr('href', res.detail_url);

                resetReceiptForm();
                setUploadState(false);

                Swal.fire({
                    icon: 'success',
                    title: 'Upload berhasil',
                    text: 'File berhasil diunggah dan sedang diproses di background.'
                });

                startProgressPolling(res.history_id);
                loadData();
            },
            error: function (xhr) {
                Swal.close();
                setUploadState(false);

                let response = xhr.responseJSON || {};
                let errors = response.errors || {};
                let hasFieldError = false;

                if (xhr.status === 422) {
                    if (errors.pelaksana_serah_id) {
                        $('#pelaksana_serah_id').addClass('is-invalid');
                        $('#pelaksana_serah_id').after('<div class="invalid-feedback d-block">' + errors.pelaksana_serah_id[0] + '</div>');
                        hasFieldError = true;
                    }

                    if (errors.receipt_no) {
                        $('#receipt_no').addClass('is-invalid');
                        $('#receipt_no').after('<div class="invalid-feedback d-block">' + errors.receipt_no[0] + '</div>');
                        hasFieldError = true;
                    }

                    if (errors.excel_file) {
                        $('#dropArea').addClass('is-invalid');
                        $('#dropArea').after('<div class="invalid-feedback d-block">' + errors.excel_file[0] + '</div>');
                        hasFieldError = true;
                    }

                    if (!hasFieldError) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Validasi gagal',
                            text: response.message || 'Periksa kembali data yang diinput.'
                        });
                    }

                    return;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: response.message || 'Terjadi kesalahan'
                });
            }
        });
    });

    function startProgressPolling(historyId) {
        if (progressInterval) clearInterval(progressInterval);

        progressInterval = setInterval(function () {
            $.get("{{ url('physical-delivery/upload-receipt/progress') }}/" + historyId, function (res) {
                $('#statusText').text(res.status);
                $('#totalRows').text(res.total_rows);
                $('#processedRows').text(res.processed_rows);
                $('#successRows').text(res.success_rows);
                $('#failedRows').text(res.failed_rows);
                $('#notesText').text(res.notes ?? '-');

                let percent = res.percent ?? 0;
                $('#progressBar').css('width', percent + '%').text(percent + '%');

                if (['success', 'partial_success', 'failed'].includes(res.status)) {
                    clearInterval(progressInterval);

                    Swal.fire({
                        icon: res.status === 'success' ? 'success' : 'warning',
                        title: 'Proses selesai',
                        text: res.status === 'success'
                            ? 'Semua data selesai diproses.'
                            : 'Proses selesai, tetapi ada data yang perlu dicek.'
                    });

                    loadData();
                }
            });
        }, 2000);
    }
});

function updateRecordCount(count) {
    $('#record-count').text(count || 0);
}

function loadData() {
    if ($.fn.DataTable.isDataTable('#datatable-serverside')) {
        window.gDataTable.ajax.reload();
        return;
    }

    window.gDataTable = $('#datatable-serverside').DataTable({
        processing: true,
        serverSide: true,
        deferRender: true,
        scrollX: true,
        destroy: true,
        order: [[1, 'desc']],
        ajax: {
            url: '{{ url("physical-delivery/upload-receipt/datatable") }}',
            dataType: 'JSON',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
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
            { orderable: false, className: 'align-middle' },
            { orderable: true, className: 'align-middle text-center fw-semibold' },
            { orderable: true, className: 'align-middle text-wrap' },
            { orderable: true, className: 'align-middle text-wrap' },
            { orderable: true, className: 'align-middle text-wrap' },
            { orderable: true, className: 'align-middle text-wrap' },
            { orderable: true, className: 'align-middle text-center' },
            { orderable: true, className: 'align-middle text-center' },
            { orderable: true, className: 'align-middle text-center' },
            { orderable: true, className: 'align-middle text-wrap' },
        ],
        initComplete: function (settings, json) {
            var table = this.api();
            const searchInput = $('div.dataTables_filter input');

            searchInput.off().unbind();

            searchInput.on('keyup', debounce(function () {
                table.search(this.value).draw();
            }, 500));

            updateRecordCount(json ? json.recordsFiltered : 0);
        },
        drawCallback: function(settings) {
            var api = this.api();
            updateRecordCount(api.page.info().recordsDisplay);
        }
    }).on('draw.dt', function() {
        onLoading('close', '#datatable-serverside_wrapper');
    });
}
</script>