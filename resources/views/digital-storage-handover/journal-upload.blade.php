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
        <div class="card-header">Riwayat Upload</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama ZIP</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Processed</th>
                        <th>Success</th>
                        <th>Failed</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories ?? [] as $row)
                    <tr>
                        <td>{{ $row->ID }}</td>
                        <td>{{ $row->ZIP_NAME }}</td>
                        <td>{{ $row->STATUS }}</td>
                        <td>{{ $row->TOTAL_ROWS }}</td>
                        <td>{{ $row->PROCESSED_ROWS }}</td>
                        <td>{{ $row->SUCCESS_ROWS }}</td>
                        <td>{{ $row->FAILED_ROWS }}</td>
                        <td>
                            <a href="{{ route('journal.zip.show', $row->ID) }}" class="btn btn-sm btn-info" target="_blank">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Belum ada histori upload</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
$(document).ready(function () {
    function formatBytes(bytes) {
        if (!bytes || bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    let progressInterval = null;

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
    if(parseInt('{{ Main::isPerpusnas() }}') == 0) {
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
        resetFilePreview();
    });

    $('#dropArea').on('dragover', function (e) {
        e.preventDefault();
        e.stopPropagation();
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
        $(this).removeClass('bg-light');

        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            $('#zip_file')[0].files = files;
            showFilePreview(files[0]);
        }
    });
    $('#zipUploadForm').on('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);
        $('.invalid-feedback').remove();
        $('.is-invalid').removeClass('is-invalid')
        $.ajax({
            url: "{{ route('journal.zip.store') }}",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                alert(res.message);
                $('#progressCard').show();
                $('#detailLink').attr('href', res.detail_url);
                startProgressPolling(res.history_id);
                resetZipForm();
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;

                    if (errors.pelaksana_serah_id) {
                        $('#pelaksana_serah_id').addClass('is-invalid');
                        $('#pelaksana_serah_id').after(
                            '<div class="invalid-feedback d-block">' + errors.pelaksana_serah_id[0] + '</div>'
                        );
                    }

                    if (errors.zip_file) {
                        $('#dropArea').after(
                            '<div class="invalid-feedback d-block">' + errors.zip_file[0] + '</div>'
                        );
                    }

                    return;
                }

                alert(xhr.responseJSON?.message || 'Terjadi kesalahan');
            }
        });
    });

    function startProgressPolling(historyId) {
        if (progressInterval) clearInterval(progressInterval);
        progressInterval = setInterval(function () {
            $.get("{{ url('digital-storage-handover/journal/zip-upload/progress') }}" + '/'+ historyId, function (res) {
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
                }
            });
        }, 2000);
    }
    function resetZipForm() {
        $('#zipUploadForm')[0].reset();
        // reset preview file
        resetFilePreview();
        // hide area upload zip lagi
        //$('#zipArea').hide();
        // disable tombol upload
        //$('#btnUpload').prop('disabled', true);
    }
});
</script>
