<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Administrasi Sistem - <span class="fw-normal">Halaman</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <span class="badge bg-primary bg-opacity-10 text-primary">
                    <i class="ph-file-text me-1"></i>
                    Kelola Konten Halaman
                </span>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    @if($errors->any())
        <div class="alert alert-danger border-0 alert-dismissible fade show">
            <div class="d-flex align-items-center">
                <i class="ph-warning-circle me-2 fs-4"></i>
                <div class="flex-fill">
                    <h6 class="alert-heading mb-2">Terdapat Kesalahan</h6>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @elseif(session('success'))
        <div class="alert alert-success border-0 alert-dismissible fade show">
            <div class="d-flex align-items-center">
                <i class="ph-check-circle me-2 fs-4"></i>
                <div class="flex-fill">
                    {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger border-0 alert-dismissible fade show">
            <div class="d-flex align-items-center">
                <i class="ph-x-circle me-2 fs-4"></i>
                <div class="flex-fill">
                    {{ session('error') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($category)
        <div class="card border-0 bg-primary bg-opacity-10 mb-3">
            <div class="card-body">
                <div class="d-flex align-items-start">
                    <i class="ph-info me-3 fs-3 text-primary"></i>
                    <div>
                        <h6 class="mb-2 fw-semibold">Panduan Penggunaan</h6>
                        <ul class="mb-0 small">
                            <li>Klik tombol <strong>"Tambah Konten"</strong> untuk menambahkan konten ke kategori</li>
                            <li>Seret icon <i class="ph-dots-six"></i> untuk mengubah urutan konten</li>
                            <li>Klik icon <i class="ph-trash text-danger"></i> untuk menghapus konten</li>
                            <li>Jangan lupa klik tombol <strong>"Simpan Data"</strong> setelah melakukan perubahan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <form action="{{ url('administration-system/pages/submitted') }}" method="POST" onsubmit="onLoading('show', 'body')">
            @csrf
            @foreach($category as $c)
                <input type="hidden" name="category[]" value="{{ $c->ID }}">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header border-bottom">
                        <div class="d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ph-folder-open me-2 text-primary"></i>
                                {{ $c->TREE_PATH }}
                            </h6>
                            <span class="badge bg-primary bg-opacity-10 text-primary" id="count-{{ $c->ID }}">
                                <i class="ph-files me-1"></i>
                                <span class="content-count">0</span> Konten
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        @php
                            $content = [];

                            if ($c->CONTENT) {
                                $contentList = "'" . str_replace(',', "','", $c->CONTENT) . "'";

                                $content = QueryAPI::get("
                                    select
                                        id,
                                        title,
                                        lang
                                    from
                                        e_news
                                    where
                                        id in ($contentList)
                                    order by
                                        instr(',' || '$c->CONTENT' || ',', ',' || ID || ',')
                                ");
                            }
                        @endphp
                        <div id="content-news-{{ $c->ID }}" class="draggable-container">
                            @if($content && count($content) > 0)
                                <ul class="list-group">
                                    @foreach($content as $cc)
                                        <li class="list-group-item border-start border-3 border-primary mb-2">
                                            <div class="d-flex align-items-center">
                                                <a href="javascript:void(0);" class="d-flex align-items-center me-3 text-muted dragula-handle" style="cursor: grab;">
                                                    <i class="ph-dots-six-vertical fs-3"></i>
                                                </a>
                                                <div class="flex-fill">
                                                    <select class="form-select category-content" name="category_content[{{ $c->ID }}][]">
                                                        <option value="{{ $cc->ID }}" selected>{{ $cc->LANG }} | {{ $cc->TITLE }}</option>
                                                    </select>
                                                </div>
                                                <div class="ms-3">
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeContent(this, {{ $c->ID }})" title="Hapus konten">
                                                        <i class="ph-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="text-center py-4 text-muted empty-state">
                                    <i class="ph-files fs-1 mb-2 d-block opacity-50"></i>
                                    <p class="mb-0">Belum ada konten. Klik tombol "Tambah Konten" untuk memulai.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer border-top">
                        <button type="button" class="btn btn-success" onclick="addContent('#content-news-{{ $c->ID }}', {{ $c->ID }})">
                            <i class="ph-plus-circle me-1"></i>
                            Tambah Konten
                        </button>
                    </div>
                </div>
            @endforeach
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            <i class="ph-info me-1"></i>
                            Pastikan semua perubahan sudah sesuai sebelum menyimpan
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="ph-floppy-disk me-1"></i>
                            Simpan Semua Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="ph-folder-open fs-1 text-muted mb-3 d-block opacity-50"></i>
                <h5 class="text-muted mb-2">Tidak Ada Data</h5>
                <p class="text-muted mb-0">Belum ada kategori halaman yang tersedia</p>
            </div>
        </div>
    @endif
</div>

<style>
    .select2-results__option {
        white-space: normal !important;
        word-wrap: break-word;
        line-height: 1.5;
    }

    .dragula-handle {
        cursor: grab;
        transition: all 0.2s;
    }

    .dragula-handle:hover {
        color: var(--bs-primary) !important;
    }

    .dragula-handle:active {
        cursor: grabbing;
    }

    .gu-mirror {
        position: fixed !important;
        margin: 0 !important;
        z-index: 9999 !important;
        opacity: 0.8;
        list-style-type: none;
    }

    .gu-hide {
        display: none !important;
    }

    .gu-unselectable {
        -webkit-user-select: none !important;
        -moz-user-select: none !important;
        -ms-user-select: none !important;
        user-select: none !important;
    }

    .gu-transit {
        opacity: 0.5;
        transform: scale(0.98);
    }

    .list-group-item {
        transition: all 0.3s ease;
    }

    .list-group-item:hover {
        background-color: rgba(var(--bs-primary-rgb), 0.05);
    }

    .empty-state {
        animation: fadeIn 0.5s;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    $(function() {
        const containers = document.querySelectorAll('.draggable-container');

        containers.forEach(function(container) {
            const dragulaInstance = dragula([container], {
                moves: function (el, container, handle) {
                    return handle.classList.contains('dragula-handle') || handle.closest('.dragula-handle');
                }
            });

            dragulaInstance.on('drop', function(el, target, source, sibling) {
                updateContentOrder($(target));
                updateContentCount($(target));
            });

            updateContentOrder($(container));
            updateContentCount($(container));
        });

        select2Serverside('.category-content', 'news', {}, {
            minimumInputLength: 0
        });
    });

    function updateContentOrder(containerElement) {
        const categoryId = containerElement.attr('id').replace('content-news-', '');

        containerElement.find('li').each(function(index, item) {
            const selectElement = $(item).find('select.category-content');
            selectElement.attr('name', `category_content[${categoryId}][${index}]`);
        });
    }

    function updateContentCount(containerElement) {
        const categoryId = containerElement.attr('id').replace('content-news-', '');
        const count = containerElement.find('li.list-group-item').length;

        $(`#count-${categoryId} .content-count`).text(count);
    }

    function addContent(param, id) {
        const container = $(param);

        container.find('.empty-state').parent().remove();

        if (container.find('ul.list-group').length === 0) {
            container.html('<ul class="list-group"></ul>');
        }

        container.find('ul.list-group').append(`
            <li class="list-group-item border-start border-3 border-primary mb-2">
                <div class="d-flex align-items-center">
                    <a href="javascript:void(0);" class="d-flex align-items-center me-3 text-muted dragula-handle" style="cursor: grab;">
                        <i class="ph-dots-six-vertical fs-3"></i>
                    </a>
                    <div class="flex-fill">
                        <select class="form-select category-content" name="category_content[${ id }][]"></select>
                    </div>
                    <div class="ms-3">
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeContent(this, ${ id })" title="Hapus konten">
                            <i class="ph-trash"></i>
                        </button>
                    </div>
                </div>
            </li>
        `);

        select2Serverside('.category-content', 'news', {}, {
            minimumInputLength: 0
        });

        updateContentOrder(container);
        updateContentCount(container);

        const newItem = container.find('li.list-group-item').last();
        newItem[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function removeContent(param, categoryId) {
        const listItem = $(param).closest('.list-group-item');
        const container = listItem.closest('.draggable-container');

        const notyConfirm = new Noty({
            text: '<div class="mb-3"><h6 class="text-dark">Hapus Konten?</h6><span class="text-muted">Konten akan dihapus dari halaman ini</span></div>',
            timeout: false,
            modal: true,
            layout: 'center',
            closeWith: 'button',
            type: 'confirm',
            buttons: [
                Noty.button('Batal', 'btn btn-light', function () {
                    notyConfirm.close();
                }),
                Noty.button('Hapus', 'btn btn-danger ms-2', function () {
                    listItem.fadeOut(300, function() {
                        $(this).remove();

                        updateContentOrder(container);
                        updateContentCount(container);

                        if (container.find('li.list-group-item').length === 0) {
                            container.html(`
                                <div class="text-center py-4 text-muted empty-state">
                                    <i class="ph-files fs-1 mb-2 d-block opacity-50"></i>
                                    <p class="mb-0">Belum ada konten. Klik tombol "Tambah Konten" untuk memulai.</p>
                                </div>
                            `);
                        }
                    });
                    notyConfirm.close();
                })
            ]
        }).show();
    }
</script>
