<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Administrasi Sistem - <span class="fw-normal">Syarat & Ketentuan</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <span class="badge bg-warning p-2 bg-opacity-10 text-warning">
                    <i class="ph-shield-warning me-1"></i>
                    Kebijakan Sistem
                </span>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    @if(session('success'))
        <div class="alert bg-success text-white fade show border-0 alert-dismissible">
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            <div class="d-flex align-items-center">
                <i class="ph-check-circle me-2 fs-5"></i>
                <div>
                    <strong>Berhasil!</strong>
                    <div>{{ session('success') }}</div>
                </div>
            </div>
        </div>
    @elseif(session('error'))
        <div class="alert bg-danger text-white fade show border-0 alert-dismissible">
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            <div class="d-flex align-items-center">
                <i class="ph-x-circle me-2 fs-5"></i>
                <div>
                    <strong>Gagal!</strong>
                    <div>{{ session('error') }}</div>
                </div>
            </div>
        </div>
    @endif
    <form method="POST">
        @csrf
        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="ph-file-doc me-2 text-primary"></i>
                        <h6 class="mb-0 fw-semibold">Konten Syarat & Ketentuan</h6>
                    </div>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary">
                        <i class="ph-pencil-simple me-1"></i>
                        Editor Mode
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label fw-semibold">
                        <i class="ph-article me-1"></i>
                        Konten Halaman
                    </label>
                    <textarea name="content" class="form-control content" id="content">{!! $template->VALUE_LOB ?? '' !!}</textarea>
                    <small class="form-text text-muted">
                        <i class="ph-info me-1"></i>
                        Gunakan editor untuk mengatur format konten halaman Syarat & Ketentuan
                    </small>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        <i class="ph-warning me-1"></i>
                        <small>Pastikan konten sudah sesuai sebelum menyimpan</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" onclick="onLoading('show', 'body')">
                            <i class="ph-floppy-disk me-1"></i>
                            Simpan Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(function() {
        CKEDITOR.replace('content', {
            enterMode: CKEDITOR.ENTER_BR,
            height: 400,
            versionCheck: false,
            toolbar: [
                { name: 'document', items: ['Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates'] },
                { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
                { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll', '-', 'Scayt'] },
                '/',
                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'] },
                { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl'] },
                { name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
                { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar', 'PageBreak', 'Iframe'] },
                '/',
                { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
                { name: 'colors', items: ['TextColor', 'BGColor'] },
                { name: 'tools', items: ['Maximize', 'ShowBlocks'] }
            ],
            removePlugins: 'elementspath',
            resize_enabled: false
        });

        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    });
</script>
