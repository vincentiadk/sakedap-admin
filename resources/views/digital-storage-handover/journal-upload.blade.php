<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Serah Simpan Digital - <span class="fw-normal">Upload Jurnal Digital via ZIP</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ url('download/from-public') }}?path=assets/contoh_upload_jurnal.zip" target="_blank" class="btn btn-success btn-sm">
                    <i class="ph-file-zip me-1"></i>
                    Contoh Upload Jurnal
                </a>
                <a href="{{ url('download/from-public') }}?path=assets/PANDUAN_UPLOAD_JURNAL_SAKEDAP.pdf" target="_blank" class="btn btn-info btn-sm">
                    <i class="ph-file-pdf me-1"></i>
                    Panduan
                </a>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="card mb-4">
        <form id="zipUploadForm" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="ph-user-circle me-2 text-primary"></i>
                            <h6 class="mb-0 fw-semibold">
                                Pelaksana Serah
                                <span class="text-danger">*</span>
                            </h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <select class="form-select" name="pelaksana_serah_id" id="pelaksana_serah_id" data-placeholder="Pilih Pelaksana Serah"></select>
                    </div>
                </div>
                <div class="mb-3" id="zipArea" style="display:none;">
                    <label class="form-label">Upload File ZIP</label>

                    <div id="dropArea"
                        class="border rounded text-center position-relative p-4"
                        style="border-style:dashed; cursor:pointer; min-height:220px; background:#fafafa;">

                        <div id="dropPlaceholder" class="d-flex flex-column justify-content-center align-items-center h-100">
                            <div style="font-size:18px; font-weight:500;">Drag & drop file ZIP di sini</div>
                            <div class="text-muted mt-2">atau klik untuk pilih file</div>
                        </div>

                        <div id="filePreview" class="d-none">
                            <div class="d-flex justify-content-center">
                                <div class="border rounded px-3 py-2 bg-white d-inline-flex align-items-center shadow-sm"
                                    style="gap:10px; max-width:420px;">
                                    <div style="font-size:24px;">📦</div>
                                    <div class="text-start">
                                        <div id="fileName" class="fw-bold text-success"></div>
                                        <div id="fileSize" class="text-muted small"></div>
                                    </div>
                                    <button type="button" id="removeFile" class="btn btn-sm btn-light border ms-2" style="z-index:99">✕</button>
                                </div>
                            </div>
                        </div>

                        <input type="file"
                            name="zip_file"
                            id="zip_file"
                            accept=".zip"
                            style="position:absolute; inset:0; width:100%; height:100%; opacity:0; cursor:pointer;">
                    </div>
                </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary" id="btnUpload" disabled>Upload ZIP</button>
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
                            <label class="form-label fw-semibold">
                                <i class="ph-calendar me-1"></i>
                                Tanggal
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ph-calendar-blank"></i>
                                </span>
                                <input type="text" class="form-control" name="date" id="date" placeholder="Pilih tanggal">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-user-circle me-1"></i>
                                Pelaksana Serah
                            </label>
                            <select class="form-select" name="executor_id" id="executor_id" data-placeholder="Semua Pelaksana"></select>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-book me-1"></i> Nama Zip
                            </label>
                            <input type="text" class="form-control" name="zip_name" id="zip_name" placeholder="Cari Zip">
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-user-circle me-1"></i>
                                Status
                            </label>
                            <select class="form-select" name="status" id="status" data-placeholder="Semua Status">
                                <option value="">semua</option>
                                <option>queued</option>
                                <option>processing</option>
                                <option>done_with_error</option>
                                <option>done</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer border-top">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ url('digital-storage-handover/accept') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
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
    <div class="card mb-4" id="progressCard" style="display:none;">
        <div class="card-header">Progress Upload</div>
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
                    <h6 class="mb-0 fw-semibold">Riwayat Upload</h6>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary" id="total-records">
                    <i class="ph-list-checks me-1"></i>
                    <span id="record-count">0</span> Data
                </span>
            </div>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover table-bordered display nowrap w-100" id = datatable-serverside>
                <thead>
                    <tr>
                        <th>Aksi</th>
                        <th>ID</th>
                        <th>Pelaksana Serah</th>
                        <th>Nama ZIP</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Processed</th>
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
        select2Serverside('#province_id', 'location', {
                for: 'province',
                province_id: '{{ session("province_id") }}',
            }, {
                minimumInputLength: 0
        });
        select2Serverside('#executor_id', 'executor', {
                province_id: '{{ session("province_id") }}',
        });
    } else {
        select2Serverside('#executor_id', 'executor');
        select2Serverside('#province_id', 'location', {
                for: 'province'
            }, {
                minimumInputLength: 0
        });
    }
    loadData();
    function formatBytes(bytes) {
        if (!bytes || bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    let progressInterval = null;
    let isSubmitting = false;

    function setUploadState(loading = false) {
        isSubmitting = loading;

        $('#btnUpload')
            .prop('disabled', loading)
            .html(loading
                ? '<i class="ph-spinner spinner me-1"></i> Sedang upload...'
                : 'Upload ZIP');

        $('#pelaksana_serah_id').prop('disabled', loading);
        $('#zip_file').prop('disabled', loading);
        $('#removeFile').prop('disabled', loading);

        if (loading) {
            $('#dropArea').css({
                'pointer-events': 'none',
                'opacity': '0.7'
            });
        } else {
            $('#dropArea').css({
                'pointer-events': '',
                'opacity': ''
            });
        }
    }

    function showProcessingAlert() {
        Swal.fire({
            icon: 'success',
            title: 'Upload berhasil',
            text: 'File berhasil diunggah dan sedang diproses di background.',
            confirmButtonText: 'OK'
        });
    }

    $('#pelaksana_serah_id').on('change', function () {
        const val = $(this).val();
        if (val) {
            $('#zipArea').show();
            $('#btnUpload').prop('disabled', false);
        } else {
            $('#zipArea').hide();
            $('#btnUpload').prop('disabled', true);
        }
    });

    if (parseInt('{{ Main::isPerpusnas() }}') == 0) {
        select2Serverside('#pelaksana_serah_id', 'executor', {
            province_id: '{{ session("province_id") }}',
        });
    } else {
        select2Serverside('#pelaksana_serah_id', 'executor');
    }

    function showFilePreview(file) {
        $('#fileName').text(file.name);
        $('#fileSize').text(formatBytes(file.size));
        $('#dropPlaceholder').addClass('d-none');
        $('#filePreview').removeClass('d-none');
    }

    function resetFilePreview() {
        $('#zip_file').val('');
        $('#fileName').text('');
        $('#fileSize').text('');
        $('#filePreview').addClass('d-none');
        $('#dropPlaceholder').removeClass('d-none');
    }

    $('#zip_file').on('change', function () {
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
            $('#zip_file')[0].files = files;
            showFilePreview(files[0]);
        }
    });

    $('#zipUploadForm').on('submit', function (e) {
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
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "{{ route('journal.zip.store') }}",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                Swal.close();

                $('#progressCard').show();
                $('#detailLink').attr('href', res.detail_url);

                resetZipForm();
                setUploadState(false);

                showProcessingAlert();
                startProgressPolling(res.history_id);
            },
            error: function (xhr) {
                Swal.close();
                setUploadState(false);
                $('.invalid-feedback').remove();
                $('.is-invalid').removeClass('is-invalid');
                let response = xhr.responseJSON || {};
                let errors = response.errors || {};
                let hasFieldError = false;

                if (xhr.status === 422) {
                    if (errors.pelaksana_serah_id && errors.pelaksana_serah_id.length > 0) {
                        $('#pelaksana_serah_id').addClass('is-invalid');
                        $('#pelaksana_serah_id').after(
                            '<div class="invalid-feedback d-block">' + errors.pelaksana_serah_id[0] + '</div>'
                        );
                        hasFieldError = true;
                    }

                    if (errors.zip_file && errors.zip_file.length > 0) {
                        $('#dropArea').addClass('is-invalid');    
                        $('#dropArea').after(
                                '<div class="invalid-feedback d-block">' + errors.zip_file[0] + '</div>'
                            );
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
            $.get("{{ url('digital-storage-handover/journal/zip-upload/progress') }}/" + historyId, function (res) {
                $('#statusText').text(res.status);
                $('#totalRows').text(res.total_rows);
                $('#processedRows').text(res.processed_rows);
                $('#successRows').text(res.success_rows);
                $('#failedRows').text(res.failed_rows);
                $('#notesText').text(res.notes ?? '-');

                let percent = res.percent ?? 0;
                $('#progressBar').css('width', percent + '%').text(percent + '%');

                if (['done', 'done_with_error', 'failed'].includes(res.status)) {
                    clearInterval(progressInterval);

                    let icon = res.status === 'done' ? 'success' : 'warning';
                    let text = res.status === 'done'
                        ? 'Semua data selesai diproses.'
                        : 'Proses selesai, tetapi ada beberapa data yang gagal.';

                    Swal.fire({
                        icon: icon,
                        title: 'Proses selesai',
                        text: text
                    });
                }
            });
        }, 2000);
    }

    function resetZipForm() {
        $('#zipUploadForm')[0].reset();
        resetFilePreview();
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
                url: '{{ url("digital-storage-handover/journal/zip-upload/datatable") }}',
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
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-wrap' },
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