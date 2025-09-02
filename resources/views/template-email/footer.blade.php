<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">Footer</h4>
            <a href="#page-header" class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto" data-bs-toggle="collapse">
                <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
            </a>
        </div>
    </div>
    <div class="page-header-content d-lg-flex border-top">
        <div class="d-flex">
            <div class="breadcrumb py-2">
                <a href="{{ url('home') }}" class="breadcrumb-item"><i class="ph-house"></i></a>
                <a href="javascript:void(0);" class="breadcrumb-item">Template Email</a>
                <span class="breadcrumb-item active">Footer</span>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    @if(session('success'))
        <div class="alert bg-success text-white alert-icon-start fade show border-0">
            <span class="alert-icon bg-black bg-opacity-20">
                <i class="ph-check-circle"></i>
            </span>
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert bg-danger text-white alert-icon-start fade show border-0">
            <span class="alert-icon bg-black bg-opacity-20">
                <i class="ph-x-circle"></i>
            </span>
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
