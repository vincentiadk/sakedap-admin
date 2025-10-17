<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Administrasi Sistem - <span class="fw-normal">Halaman</span>
            </h4>
        </div>
    </div>
</div>
<div class="content pt-0">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @elseif(session('success'))
        <div class="alert bg-success text-white fade show border-0">
            {{ session('success') }}
        </div>
    @elseif(session('error'))
        <div class="alert bg-danger text-white fade show border-0">
            {{ session('error') }}
        </div>
    @endif
    @if($category)
        <form action="{{ url('administration-system/pages/submitted') }}" method="POST" onsubmit="onLoading('show', 'body')">
            @csrf
            @foreach($category as $c)
                <input type="hidden" name="category[]" value="{{ $c->ID }}">
                <div class="card">
                    <div class="card-header">
                        <h5 class="hstack gap-2 mb-0">{{ $c->TREE_PATH }}</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-borderless media-list-container py-2">
                            <div id="content-news-{{ $c->ID }}" class="draggable-container">
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

                                @if($content)
                                    @foreach($content as $cc)
                                        <li class="list-group-item d-flex align-items-start">
                                            <a href="javascript:void(0);" class="d-inline-flex align-items-center me-3">
                                                <i class="ph-dots-six dragula-handle ph-2x"></i>
                                            </a>
                                            <div class="flex-fill">
                                                <select class="form-select category-content" name="category_content[{{ $c->ID }}][]">
                                                    <option value="{{ $cc->ID }}" selected>{{ $cc->LANG }} | {{ $cc->TITLE }}</option>
                                                </select>
                                            </div>
                                            <div class="ms-3">
                                                <a href="javascript:void(0);" class="list-icons-item" onclick="removeContent(this)">
                                                    <i class="ph-trash text-danger pt-2 ph-1x"></i>
                                                </a>
                                            </div>
                                        </li>
                                    @endforeach
                                @endif
                            </div>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent">
                        <button type="button" class="btn btn-success btn-sm" onclick="addContent('#content-news-{{ $c->ID }}', {{ $c->ID }})">
                            <i class="ph-plus me-1"></i>
                            Tambah Konten
                        </button>
                    </div>
                </div>
            @endforeach
            <div class="card">
                <div class="card-body">
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="ph-floppy-disk me-1"></i>
                            Simpan Data
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @else
        <div class="alert alert-info text-center">Tidak ada data</div>
    @endif
</div>

<style>
    .select2-results__option {
        white-space: normal !important;
        word-wrap: break-word; 
        line-height: 1.5; 
    }
</style>

<script>
    $(function() {
        const containers = document.querySelectorAll('.draggable-container');

        containers.forEach(function(container) {
            const dragulaInstance = dragula([container], {
                mirrorContainer: document.querySelector('.media-list-container'),
                moves: function (el, container, handle) {
                    return handle.classList.contains('dragula-handle');
                }
            });

            dragulaInstance.on('drop', function(el, target, source, sibling) {
                updateContentOrder($(target)); 
            });
            
            updateContentOrder($(container));
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

    function addContent(param, id) {
        $(param).append(`
            <li class="list-group-item d-flex align-items-start">
                <a href="javascript:void(0);" class="d-inline-flex align-items-center me-3">
                    <i class="ph-dots-six dragula-handle ph-2x"></i>
                </a>
                <div class="flex-fill">
                    <select class="form-select category-content" name="category_content[${ id }][]"></select>
                </div>
                <div class="ms-3">
                    <a href="javascript:void(0);" class="list-icons-item" onclick="removeContent(this)">
                        <i class="ph-trash text-danger pt-2 ph-1x"></i>
                    </a>
                </div>
            </li>
        `);

        select2Serverside('.category-content', 'news', {}, {
            minimumInputLength: 0
        });
    }

    function removeContent(param) {
        $(param).parents('.list-group-item').remove();
    }
</script>
