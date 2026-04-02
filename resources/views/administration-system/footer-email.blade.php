<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Administrasi Sistem - <span class="fw-normal">Footer Email</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <span class="badge bg-primary p-2 bg-opacity-10 text-primary">
                    <i class="ph-image me-1"></i>
                    Footer Template
                </span>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    @if(session('success'))
        <div class="alert bg-success text-white alert-dismissible fade show border-0">
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            <div class="d-flex align-items-center">
                <i class="ph-check-circle me-2 fs-4"></i>
                <div>{{ session('success') }}</div>
            </div>
        </div>
    @elseif(session('error'))
        <div class="alert bg-danger text-white alert-dismissible fade show border-0">
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            <div class="d-flex align-items-center">
                <i class="ph-x-circle me-2 fs-4"></i>
                <div>{{ session('error') }}</div>
            </div>
        </div>
    @endif
    <form method="POST" enctype="multipart/form-data" onsubmit="onLoading('show', 'body')">
        @csrf
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="alert alert-info border-0 mb-0">
                    <div class="d-flex align-items-start">
                        <i class="ph-info me-2 fs-4"></i>
                        <div>
                            <h6 class="mb-1">Informasi Footer Email</h6>
                            <p class="mb-1">Footer email adalah gambar yang akan ditampilkan di bagian bawah setiap email yang dikirim sistem.</p>
                            <ul class="mb-0 mt-2">
                                <li>Format file: JPG, JPEG, atau PNG</li>
                                <li>Ukuran yang disarankan: 600px x 100px</li>
                                <li>Maksimal 1 file</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center">
                    <i class="ph-upload-simple me-2 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Upload Footer Email</h6>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @if(!Main::isSuperAdmin() && !Main::isPerpusnas())
                        <div class="col-lg-12 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="ph-map-pin me-1"></i>
                                Provinsi
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ph-buildings"></i>
                                </span>
                                <select class="form-select" name="province_id" id="province_id" required>
                                    <option value="{{ session('province_id') }}" selected>{{ session('province_name') }}</option>
                                </select>
                            </div>
                        </div>
                    @endif
                    <div class="col-lg-12">
                        <label class="form-label fw-semibold">
                            <i class="ph-image me-1"></i>
                            File Footer
                            <span class="text-danger">*</span>
                        </label>
                        <input type="file" name="file" id="file" required>
                    </div>
                </div>
            </div>
        </div>
        @if(isset($template) && $template->CONTENT)
            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom">
                    <div class="d-flex align-items-center">
                        <i class="ph-eye me-2 text-primary"></i>
                        <h6 class="mb-0 fw-semibold">Preview Footer Saat Ini</h6>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center p-3 bg-light rounded">
                        <img src="{{ url('stream-file') }}?type=gambar_template&id={{ $template->ID }}&filename={{ $template->CONTENT }}"
                            class="img-fluid rounded shadow-sm"
                            alt="Footer Email Preview"
                            style="max-height: 150px;">
                    </div>
                    <div class="text-muted text-center mt-2">
                        <small>
                            <i class="ph-info me-1"></i>
                            Ini adalah preview footer email yang sedang digunakan
                        </small>
                    </div>
                </div>
            </div>
        @endif
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        <i class="ph-warning me-1"></i>
                        Pastikan file yang diupload sesuai dengan ketentuan
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="ph-floppy-disk me-1"></i>
                        Simpan Footer Email
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(function() {
        var hasImage = '{{ $template->CONTENT ?? '' }}';
        var templateId = '{{ $template->ID ?? "" }}';

        if(hasImage) {
            var imageUrl = '{{ url("stream-file") }}?type=gambar_template&id=' + templateId + '&filename=' + hasImage;
            var previewImage = [imageUrl];
            var previewConfig = [
                {
                    caption: 'Footer Email Saat Ini',
                    size: '',
                    key: 1,
                    url: imageUrl
                }
            ];
        } else {
            var previewImage = '';
            var previewConfig = '';
        }

        dragAndDropFile('#file', {
            allowedFileExtensions: ['jpg', 'jpeg', 'png'],
            maxFileCount: 1,
            maxFileSize: 2048,
            initialPreview: previewImage,
            initialPreviewConfig: previewConfig,
            overwriteInitial: true,
            showUpload: false,
            showRemove: true,
            showCancel: false,
            browseLabel: 'Pilih File',
            removeLabel: 'Hapus',
            browseClass: 'btn btn-primary',
            removeClass: 'btn btn-danger',
            fileActionSettings: {
                showRemove: true,
                showUpload: false,
                showZoom: true,
                showDrag: false
            },
            layoutTemplates: {
                actionDelete: '<button type="button" class="btn btn-sm btn-icon btn-danger" title="Hapus file" {dataKey}><i class="ph-trash"></i></button>',
                actionZoom: '<button type="button" class="btn btn-sm btn-icon btn-info" title="Lihat preview" {dataKey}><i class="ph-magnifying-glass-plus"></i></button>',
            },
            msgPlaceholder: 'Pilih file atau drag & drop di sini...',
            msgInvalidFileExtension: 'Format file "{name}" tidak valid. Hanya file {extensions} yang diperbolehkan.',
            msgSizeTooLarge: 'File "{name}" terlalu besar ({size} KB). Maksimal ukuran file adalah {maxSize} KB.',
            msgFilesTooMany: 'Jumlah file yang dipilih ({n}) melebihi batas maksimal {m}.',
        });
    });
</script>
