<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pengaturan - <span class="fw-normal">Footer Email</span>
            </h4>
        </div>
    </div>
</div>
<div class="content pt-0">
    @elseif(session('success'))
        <div class="alert bg-success text-white fade show border-0">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert bg-danger text-white fade show border-0">
            {{ session('error') }}
        </div>
    @endif
    <form method="POST" enctype="multipart/form-data" onsubmit="onLoading('show', 'body')">
        @csrf
        <div class="card">
            <div class="card-body">
                <input type="file" name="file" id="file" required>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="text-end">
                    <button type="submit" class="btn btn-warning">
                        <i class="ph-floppy-disk me-1"></i>
                        Simpan Data
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(function() {
        var hasImage = '{{ $template->CONTENT }}';
        var templateId = '{{ $template->ID ?? "" }}';

        if(hasImage) {
            var imageUrl = '{{ url("stream-file") }}?type=gambar_template&id=' + templateId + '&filename={{ $template->CONTENT }}';
            var previewImage = [imageUrl];
            var previewConfig = [
                {
                    caption: '',
                    size: '',
                    key: 1,
                    url: imageUrl
                }
            ]
        } else {
            var previewImage = '';
            var previewConfig = '';
        }

        dragAndDropFile('#file', {
            allowedFileExtensions: ['jpg', 'jpeg', 'png'],
            maxFileCount: 1,
            initialPreview: previewImage,
            initialPreviewConfig: previewConfig,
            overwriteInitial: true
        });
    });
</script>
