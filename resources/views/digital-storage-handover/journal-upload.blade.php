<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Serah Simpan Digital - <span class="fw-normal">Upload Jurnal Digital via ZIP</span>
            </h4>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="card mb-4">
        <div class="card-body">
            <form id="zipUploadForm" enctype="multipart/form-data">
                @csrf
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
                <div class="mb-3" id="uploadAreaWrap" style="display:none;">
                    <label class="form-label">Upload File ZIP</label>
                    <div id="dropArea" class="border rounded p-5 text-center" style="border-style:dashed !important; cursor:pointer;">
                        <p class="mb-2">Drag & drop file ZIP di sini</p>
                        <p class="text-muted mb-2">atau klik untuk pilih file</p>
                        <input type="file" 
                            name="zip_file" 
                            id="zip_file" 
                            accept=".zip" 
                            style="inset:0; width:100%; height:100%; opacity:0; cursor:pointer;">
                    </div>
                    <div id="fileName" class="mt-2 text-success fw-bold"></div>
                </div>

                <button type="submit" class="btn btn-primary" id="btnUpload">Upload ZIP</button>
            </form>
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
    $('#zip_file').on('change', function () {
        if (this.files && this.files.length > 0) {
            $('#fileName').text(this.files[0].name);
        }
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
            $('#fileName').text(files[0].name);
        }
    });
    $('#zipUploadForm').on('submit', function (e) {
        e.preventDefault();

        let formData = new FormData(this);

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
            },
            error: function (xhr) {
                let msg = 'Terjadi kesalahan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                alert(msg);
            }
        });
    });

function startProgressPolling(historyId) {
    if (progressInterval) clearInterval(progressInterval);

    progressInterval = setInterval(function () {
        $.get("{{ url('e-collections/zip-upload/progress') }}/" + historyId, function (res) {
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
});
</script>
<script>
let progressInterval = null;

$('#pelaksana_serah_id').on('change', function () {
    const val = $(this).val();
    if (val) {
        $('#uploadAreaWrap').show();
        $('#btnUpload').prop('disabled', false);
    } else {
        $('#uploadAreaWrap').hide();
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
/*$('#dropArea').on('click', function () {
    $('#zip_file').trigger('click');
});

$('#zip_file').on('change', function () {
    if (this.files.length > 0) {
        $('#selectedFile').text(this.files[0].name);
    }
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
        $('#selectedFile').text(files[0].name);
    }
});
*/

</script>