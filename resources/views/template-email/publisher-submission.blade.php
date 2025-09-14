<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Template Email - <span class="fw-normal">Penerbit Pengajuan</span>
            </h4>
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
    <form method="POST">
        @csrf
        <div class="card">
            <div class="card-body">
                <textarea name="content" class="form-control content" id="content">
                    {!! $template->CONTENT ?? '' !!}
                </textarea>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="text-end">
                    <button type="submit" class="btn btn-warning" onclick="onLoading('show', 'body')">
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
        CKEDITOR.replace('content',{
            enterMode : CKEDITOR.ENTER_BR,
            height: 250,
            versionCheck: false
        });
    });
</script>
