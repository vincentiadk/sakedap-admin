<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">Tanda Terima</h4>
            <a href="#page-header" class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto" data-bs-toggle="collapse">
                <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
            </a>
        </div>
        <div class="collapse d-lg-block my-lg-auto ms-lg-auto" id="page-header">
            <div class="d-sm-flex align-items-center mb-3 mb-lg-0 ms-lg-3">
                <div class="d-inline-flex mt-3 mt-sm-0">
                    <button type="button" class="btn btn-primary" onclick="onCreate()">
                        <i class="ph-plus-circle me-1"></i>
                        Tambah Data
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="page-header-content d-lg-flex border-top">
        <div class="d-flex">
            <div class="breadcrumb py-2">
                <a href="{{ url('home') }}" class="breadcrumb-item"><i class="ph-house"></i></a>
                <a href="javascript:void(0);" class="breadcrumb-item">Template Email</a>
                <span class="breadcrumb-item active">Tanda Terima</span>
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
