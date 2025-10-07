<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pengaturan - <span class="fw-normal">Tentang Kami</span>
            </h4>
        </div>
    </div>
</div>
<div class="content pt-0">
    @if(session('success'))
        <div class="alert bg-success text-white fade show border-0">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert bg-danger text-white fade show border-0">
            {{ session('error') }}
        </div>
    @endif
    <form method="POST">
        @csrf
        <div class="card">
            <div class="card-body">
                <textarea name="content" class="form-control content" id="content">
                    {!! $template->VALUE ?? '' !!}
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
