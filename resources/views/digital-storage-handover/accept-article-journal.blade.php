<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Serah Simpan Digital - <span class="fw-normal">Artikel Jurnal</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <span class="badge bg-success p-2 bg-opacity-10 text-success">
                    Data Artikel Jurnal Diterima
                </span>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="card border-0 shadow-sm">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ph-funnel me-1 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Filter Pencarian</h6>
                </div>
                <button type="button" class="btn btn-sm btn-light" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="ph-caret-down"></i>
                </button>
            </div>
        </div>
        <div class="collapse" id="filterCollapse">
            <div class="card-body">
                <form id="form-filter">
                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-calendar me-1"></i>
                                Tanggal
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ph-calendar-blank"></i>
                                </span>
                                <input type="text" class="form-control" name="date" id="date" placeholder="Pilih tanggal">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-user-circle me-1"></i>
                                Pelaksana Serah
                            </label>
                            <select class="form-select" name="executor_id" id="executor_id" data-placeholder="Semua Pelaksana"></select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-book me-1"></i>
                            </label>
                            <input type="text" class="form-control" name="title" id="title" placeholder="Cari judul">
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-map-pin me-1"></i>
                                Provinsi
                            </label>
                            <select class="form-select" name="province_id" id="province_id" data-placeholder="Pilih Provinsi">
                                @if(!Main::isPerpusnas())
                                    <option value="{{ session('province_id') }}" selected>{{ session('province_name') }}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-calendar-blank me-1"></i>
                                Tahun
                            </label>
                            <input type="number" class="form-control" name="year" id="year" placeholder="Cari tahun">
                        </div>

                    </div>
                </form>
            </div>
            <div class="card-footer border-top">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ url('digital-storage-handover/accept') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
                        <i class="ph-arrow-counter-clockwise me-1"></i>
                        Reset Filter
                    </a>
                    <button type="button" class="btn btn-primary" onclick="loadData()">
                        <i class="ph-magnifying-glass me-1"></i>
                        Cari Data
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ph-clipboard-text me-1 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Daftar Data Diterima</h6>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary" id="total-records">
                    <i class="ph-list-checks me-1"></i>
                    <span id="record-count">0</span> Data
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered display nowrap w-100" id="datatable-serverside">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center text-nowrap" style="width: 60px">
                                <i class="ph-hash"></i>
                            </th>
                            <th class="text-center text-nowrap" style="width: 100px">
                                <i class="ph-gear"></i>
                                Aksi
                            </th>
                            <th class="text-nowrap" style="min-width: 180px">
                                <i class="ph-user-circle me-1"></i>
                                Pelaksana Serah
                            </th>
                            <th class="text-nowrap" style="min-width: 250px">
                                <i class="ph-book me-1"></i>
                                Katalog ID
                            </th>
                            <th class="text-nowrap" style="min-width: 250px">
                                <i class="ph-book me-1"></i>
                                Judul Artikel
                            </th>
                            <th class="text-nowrap" style="min-width: 250px">
                                <i class="ph-book me-1"></i>
                                Kontributor Artikel
                            </th>
                            <th class="text-nowrap" style="min-width: 250px">
                                <i class="ph-book me-1"></i>
                                Subyek Artikel
                            </th>
                            <th class="text-nowrap" style="min-width: 100px">
                                <i class="ph-code me-1"></i>
                                Tanggal Edisi
                            </th>
                            <th class="text-nowrap" style="min-width: 250px">
                                <i class="ph-book me-1"></i>
                                Judul Jurnal
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-file me-1"></i>
                                Jenis Media
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-file me-1"></i>
                                ISSN
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-file me-1"></i>
                                Volume / Edisi
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 130px">
                                <i class="ph-calendar-check me-1"></i>
                                Tgl Terima
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        datePickerBasic('#date');

        if(parseInt('{{ Main::isPerpusnas() }}') == 0) {
            select2Serverside('#province_id', 'location', {
                for: 'province',
                province_id: '{{ session("province_id") }}',
            }, {
                minimumInputLength: 0
            });

            select2Serverside('#executor_id', 'executor', {
                province_id: '{{ session("province_id") }}',
            });
        } else {
            select2Serverside('#executor_id', 'executor');

            select2Serverside('#province_id', 'location', {
                for: 'province'
            }, {
                minimumInputLength: 0
            });
        }

        loadData();
    });

    function loadData() {
        if ($.fn.DataTable.isDataTable('#datatable-serverside')) {
            window.gDataTable.ajax.reload();
            return;
        }
        window.gDataTable = $('#datatable-serverside').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            scrollX: true,
            destroy: true,
            order: [[6, 'desc']],
            ajax: {
                url: '{{ url("digital-storage-handover/accept-article-journal/datatable") }}',
                dataType: 'JSON',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (d) {
                    $('#form-filter').serializeArray().forEach(function(item) {
                        d[item.name] = item.value;
                    });

                    return d;
                },
                beforeSend: function() {
                    onLoading('show', '#datatable-serverside_wrapper');
                },
                error: function(response) {
                    onLoading('close', '#datatable-serverside_wrapper');
                    responseError(response);
                }
            },
            columns: [
                { orderable: true, className: 'align-middle text-center fw-semibold' },
                { orderable: false, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-center' },
                 { orderable: true, className: 'align-middle text-center' },
            ],
            initComplete: function (settings, json) {
                var table = this.api();
                const searchInput = $('div.dataTables_filter input');

                searchInput.off().unbind();

                searchInput.on('keyup', debounce(function () {
                    table.search(this.value).draw();
                }, 500));

                updateRecordCount(json ? json.recordsFiltered : 0);
            },
            drawCallback: function(settings) {
                var api = this.api();
                updateRecordCount(api.page.info().recordsDisplay);
            }
        }).on('draw.dt', function() {
            onLoading('close', '#datatable-serverside_wrapper');
        });
    }

    function verifikasi(id) {
        var notyConfirm = new Noty({
            text: '<div class="mb-3"><h5 class="text-dark">Verifikasi ulang?</h5><span class="text-muted">Anda yakin ingin melakukan verifikasi ulang?</span></div>',
            timeout: false,
            modal: true,
            layout: 'center',
            closeWith: 'button',
            type: 'confirm',
            buttons: [
                Noty.button('Tidak', 'btn btn-light', function () {
                    notyConfirm.close();
                }),
                Noty.button('Kirim', 'btn btn-teal ms-2', function () {
                    $.ajax({
                        url: '{{ url("digital-storage-handover/accept-article-journal/verification") }}',
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            id: id
                        },
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        beforeSend: function() {
                            onLoading('show', '.noty_bar');
                        },
                        success: function(response) {
                            onLoading('close', '.noty_bar');

                            if(response.code == 200) {
                                notyConfirm.close();

                                swalInit.fire({
                                    title: 'Berhasil',
                                    text: response.message,
                                    icon: 'success',
                                    showCloseButton: false
                                });
                            } else {
                                swalInit.fire({
                                    title: 'Error',
                                    text: response.message,
                                    icon: 'error',
                                    showCloseButton: false
                                });
                            }
                        },
                        error: function(response) {
                            onLoading('close', '.noty_bar');
                            responseError(response);
                        }
                    });
                })
            ]
        }).show();
    }

    function destroy(id) {
        var notyConfirm = new Noty({
            text: '<div class="mb-3"><h5 class="text-dark">Hapus data?</h5><span class="text-muted">Anda yakin ingin menghapus data ini? Seluruh data dan file yang berkaitan juga akan terhapus.</span></div>',
            timeout: false,
            modal: true,
            layout: 'center',
            closeWith: 'button',
            type: 'confirm',
            buttons: [
                Noty.button('Tidak', 'btn btn-light', function () {
                    notyConfirm.close();
                }),
                Noty.button('Kirim', 'btn btn-teal ms-2', function () {
                    $.ajax({
                        url: '{{ url("digital-storage-handover/accept-article-journal/destroy-data") }}',
                        type: 'DELETE',
                        dataType: 'JSON',
                        data: {
                            id: id
                        },
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        beforeSend: function() {
                            onLoading('show', '.noty_bar');
                        },
                        success: function(response) {
                            onLoading('close', '.noty_bar');

                            if(response.code == 200) {
                                notyConfirm.close();

                                swalInit.fire({
                                    title: 'Berhasil',
                                    text: response.message,
                                    icon: 'success',
                                    showCloseButton: false
                                });
                            } else {
                                swalInit.fire({
                                    title: 'Error',
                                    text: response.message,
                                    icon: 'error',
                                    showCloseButton: false
                                });
                            }
                            loadData();
                        },
                        error: function(response) {
                            onLoading('close', '.noty_bar');
                            responseError(response);
                        }
                    });
                })
            ]
        }).show();
    }

    function showDetail(id) {
        $.ajax({
            url: '{{ url("digital-storage-handover/accept-article-journal/detail") }}',
            type: 'GET',
            dataType: 'JSON',
            data: {
                id: id
            },
            beforeSend: function() {
                swalInit.fire({
                    title: 'Loading...',
                    text: 'Sedang mengambil data detail',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                if (response.code == 200) {
                    let data = response.data;

                    const safe = (value) => value ? value : '-';

                    const doi = data.ARTICLE_DOI
                        ? `<a href="${data.ARTICLE_DOI}" target="_blank" class="meta-link">${data.ARTICLE_DOI}</a>`
                        : '-';

                    const articleLink = data.ARTICLE_ORIGINAL_LINK
                        ? `<a href="${data.ARTICLE_ORIGINAL_LINK}" target="_blank" class="meta-link">${data.ARTICLE_ORIGINAL_LINK}</a>`
                        : '-';

                    const fileLink = data.ARTICLE_FILE_LINK
                        ? `<a href="${data.ARTICLE_FILE_LINK}" target="_blank" class="meta-link">Download File</a>`
                        : '-';

                    const katalog = data.CATALOG_ID
                        ? `<iframe 
                                src="https://inlis-backup.perpusnas.go.id/inlisnew/KatalogDetailView.aspx?id=${data.CATALOG_ID}" 
                                width="100%" 
                                height="500" 
                                style="border:1px solid #dee2e6; border-radius:10px; background:#fff;">
                        </iframe>`
                        : `<div class="empty-box">Data katalog tidak tersedia</div>`;

                    let html = `
                        <style>
                            .swal-detail-wrap {
                                text-align: left;
                                font-family: inherit;
                            }
                            .swal-detail-grid {
                                display: grid;
                                grid-template-columns: 2fr 1fr;
                                gap: 20px;
                                align-items: start;
                            }
                            .swal-detail-main,
                            .swal-detail-side {
                                display: flex;
                                flex-direction: column;
                                gap: 16px;
                            }
                            .detail-card {
                                background: #fff;
                                border: 1px solid #e9ecef;
                                border-radius: 12px;
                                padding: 18px;
                                box-shadow: 0 2px 8px rgba(0,0,0,0.04);
                            }
                            .detail-title {
                                font-size: 24px;
                                font-weight: 700;
                                line-height: 1.4;
                                color: #212529;
                                margin-bottom: 8px;
                            }
                            .detail-subtitle {
                                font-size: 14px;
                                color: #6c757d;
                                margin-bottom: 14px;
                            }
                            .section-title {
                                font-size: 16px;
                                font-weight: 700;
                                color: #212529;
                                margin-bottom: 12px;
                                border-bottom: 1px solid #e9ecef;
                                padding-bottom: 8px;
                            }
                            .meta-list {
                                display: grid;
                                grid-template-columns: 180px 1fr;
                                row-gap: 10px;
                                column-gap: 14px;
                                font-size: 14px;
                            }
                            .meta-label {
                                color: #6c757d;
                                font-weight: 600;
                            }
                            .meta-value {
                                color: #212529;
                                word-break: break-word;
                            }
                            .meta-link {
                                color: #0d6efd;
                                text-decoration: none;
                                word-break: break-all;
                            }
                            .meta-link:hover {
                                text-decoration: underline;
                            }
                            .abstract-box {
                                background: #f8f9fa;
                                border-radius: 10px;
                                padding: 14px;
                                font-size: 14px;
                                color: #212529;
                                line-height: 1.7;
                                text-align: justify;
                                min-height: 100px;
                            }
                            .empty-box {
                                background: #f8f9fa;
                                border: 1px dashed #ced4da;
                                border-radius: 10px;
                                padding: 20px;
                                text-align: center;
                                color: #6c757d;
                                font-size: 14px;
                            }
                            .side-info-item {
                                margin-bottom: 12px;
                            }
                            .side-info-label {
                                display: block;
                                font-size: 12px;
                                color: #6c757d;
                                margin-bottom: 4px;
                                text-transform: uppercase;
                                letter-spacing: .3px;
                            }
                            .side-info-value {
                                font-size: 14px;
                                color: #212529;
                                word-break: break-word;
                            }
                            @media (max-width: 991px) {
                                .swal-detail-grid {
                                    grid-template-columns: 1fr;
                                }
                                .meta-list {
                                    grid-template-columns: 1fr;
                                }
                            }
                        </style>

                        <div class="swal-detail-wrap">
                            <div class="swal-detail-grid">

                                <div class="swal-detail-main">
                                    <div class="detail-card">
                                        <div class="detail-title">${safe(data.ARTICLE_TITLE)}</div>
                                        <div class="detail-subtitle">
                                            ${safe(data.ARTICLE_CONTRIBUTOR)}
                                        </div>

                                        <div class="section-title">Metadata Artikel</div>
                                        <div class="meta-list">
                                        
                                            <div class="meta-label">DOI</div>
                                            <div class="meta-value">${doi}</div>

                                            <div class="meta-label">Link Artikel</div>
                                            <div class="meta-value">${articleLink}</div>

                                            <div class="meta-label">Link File</div>
                                            <div class="meta-value">${fileLink}</div>

                                            <div class="meta-label">Tanggal Terima</div>
                                            <div class="meta-value">${safe(data.RECEIVED_AT)}</div>
                                            <div class="meta-label">Tanggal Publikasi</div>
                                            <div class="meta-value">${safe(data.EDITION_DATE)}</div>
                                            
                                            <div class="meta-label">Catalog ID</div>
                                            <div class="meta-value">${safe(data.CATALOG_ID)}</div>
                                            
                                            <div class="meta-label">Diunggah Oleh</div>
                                            <div class="meta-value">${safe(data.CREATEBYNAME)}</div>
                                        </div>
                                    </div>

                                    <div class="detail-card">
                                        <div class="section-title">Abstrak</div>
                                        <div class="abstract-box">
                                            ${safe(data.ARTICLE_ABSTRACT)}
                                        </div>
                                    </div>

                                    <div class="detail-card">
                                        <div class="section-title">Katalog</div>
                                        ${katalog}
                                    </div>
                                </div>

                                <div class="swal-detail-side">
                                    <div class="detail-card">
                                        <div class="section-title">Info Jurnal</div>
                                        <div class="side-info-item">
                                            <span class="side-info-label">Judul Jurnal</span>
                                            <div class="side-info-value">${safe(data.TITLE)}</div>
                                        </div>
                                    

                                        <div class="side-info-item">
                                            <span class="side-info-label">ISSN / EISSN</span>
                                            <div class="side-info-value">${safe(data.CODE)}</div>
                                        </div>
                                        <div class="side-info-item">
                                            <span class="side-info-label">Volume</span>
                                            <div class="side-info-value">${safe(data.VOLUME)}</div>
                                        </div>

                                        <div class="side-info-item">
                                            <span class="side-info-label">Pelaksana Serah</span>
                                            <div class="side-info-value">${safe(data.PENERBITNAME)}</div>
                                        </div>
                                    </div>

                                    <div class="detail-card">
                                        <div class="section-title">Akses Cepat</div>
                                        <div class="side-info-item">
                                            <a href="${data.ARTICLE_ORIGINAL_LINK ?? '#'}" target="_blank" class="meta-link">
                                                Buka Halaman Artikel
                                            </a>
                                        </div>
                                        <div class="side-info-item">
                                            <a href="${data.ARTICLE_FILE_LINK ?? '#'}" target="_blank" class="meta-link">
                                                Download File
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    `;

                    swalInit.fire({
                        title: '',
                        html: html,
                        width: '95%',
                        confirmButtonText: 'Tutup',
                        showCloseButton: true,
                        showConfirmButton: true,
                        customClass: {
                            popup: 'p-2'
                        }
                    });
                } else {
                    swalInit.fire({
                        title: 'Error',
                        text: response.message,
                        icon: 'error',
                        showCloseButton: true
                    });
                }
            },
            error: function(response) {
                responseError(response);
            }
        });
    }
    function buildViewerHtml() {
        return `
            <div id="swal-viewer-wrapper" style="position: relative; width: 100%; min-height: 400px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 12px; overflow: hidden;">
                <div id="swal-viewer-content" style="width: 100%; height: 100%; position: relative;">
                    
                    <div id="swal-pdf-controls" style="display: none; position: absolute; top: 15px; right: 25px; z-index: 1000; background: rgba(0,0,0,0.8); padding: 8px 15px; border-radius: 8px; gap: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.5); align-items: center;">
                        <span id="swal-pdf-info" style="color: white; font-size: 13px; font-weight: 500; border-right: 1px solid #555; padding-right: 12px;">
                            Halaman: <span id="swal-current-page-num">-</span> / <span id="swal-total-pages-num">-</span>
                        </span>
                        <div style="display: flex; gap: 8px;">
                            <button type="button" class="btn btn-sm btn-light" id="swal-btn-pdf-zoom-out" title="Zoom Out">
                                <i class="ph-magnifying-glass-minus text-dark"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-light" id="swal-btn-pdf-fit" title="Fit Layout">
                                <i class="ph-corners-out text-dark"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-light" id="swal-btn-pdf-zoom-in" title="Zoom In">
                                <i class="ph-magnifying-glass-plus text-dark"></i>
                            </button>
                        </div>
                    </div>

                    <div id="swal-pdf-viewer-container" style="overflow: auto; max-height: 700px; background: #525659; padding: 20px 0; display: none; scroll-snap-type: y mandatory; scroll-behavior: smooth;"></div>

                    <video id="swal-video-player" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" style="display: none; width: 100%; height: 450px;"></video>

                    <div id="swal-epub-container" style="display: none; height: 800px; width: 100%; background: white;"></div>

                    <div id="swal-epub-controls" style="display: none; position: absolute; top: 50%; left: 0; width: 100%; justify-content: space-between; padding: 0 20px; pointer-events: none; z-index: 10000; transform: translateY(-50%);">
                        <button type="button" id="swal-prev-btn" class="btn btn-dark btn-sm rounded-circle shadow" style="width: 40px; height: 40px; pointer-events: auto; display: flex; align-items: center; justify-content: center;">
                            <i class="ph-caret-left" style="font-size: 20px;"></i>
                        </button>
                        <button type="button" id="swal-next-btn" class="btn btn-dark btn-sm rounded-circle shadow" style="width: 40px; height: 40px; pointer-events: auto; display: flex; align-items: center; justify-content: center;">
                            <i class="ph-caret-right" style="font-size: 20px;"></i>
                        </button>
                    </div>

                    <div id="swal-audio-wrapper" style="display: none; height: 800px; width: 100%; position: relative; overflow: hidden; border-radius: 8px; background: #000;">
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, #023BAD 0%, #06732A 100%); z-index: 1;"></div>
                        <canvas id="swal-wave-canvas" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 2; opacity: 0.6;"></canvas>
                        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10; display: flex; flex-direction: column; padding: 30px;">
                            <div class="d-flex justify-content-between text-white align-items-center" style="font-family: sans-serif;">
                                <span id="swal-timer-current" style="font-variant-numeric: tabular-nums;">0:00</span>
                                <span class="fw-bold text-uppercase" style="opacity: 0.8; font-size: 14px;">Now Playing</span>
                                <span id="swal-timer-duration" style="font-variant-numeric: tabular-nums;">--:--</span>
                            </div>
                            <div class="flex-grow-1 d-flex align-items-center justify-content-center text-center">
                                <h3 id="swal-audio-title-display" class="text-white fw-light" style="text-shadow: 0 2px 10px rgba(0,0,0,0.2);">
                                    Menyiapkan Audio...
                                </h3>
                            </div>
                            <div class="d-flex align-items-center justify-content-center gap-4 pb-4" style="position: relative; z-index: 20;">
                                <button type="button" class="btn btn-link text-white p-0" id="swal-btn-rewind" title="-10 Detik">
                                    <i class="ph-rewind" style="font-size: 32px;"></i>
                                </button>
                                <div id="swal-play-pause-wrapper" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.2); border-radius: 50%; backdrop-filter: blur(5px); border: 2px solid rgba(255,255,255,0.5); cursor: pointer; transition: transform 0.2s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                                    <i id="swal-icon-play" class="ph-play text-white" style="font-size: 40px; margin-left: 4px;"></i>
                                    <i id="swal-icon-pause" class="ph-pause text-white" style="font-size: 40px; display: none;"></i>
                                </div>
                                <button type="button" class="btn btn-link text-white p-0" id="swal-btn-forward" title="+10 Detik">
                                    <i class="ph-fast-forward" style="font-size: 32px;"></i>
                                </button>
                            </div>
                            <div style="position: absolute; bottom: 30px; right: 30px;">
                                <button type="button" id="swal-btn-mute" class="btn btn-link text-white p-0" style="opacity: 0.6;">
                                    <i class="ph-speaker-high" id="swal-icon-vol" style="font-size: 24px;"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="swal-default-message" style="display: flex; height: 800px; align-items: center; justify-content: center;">
                        <span class="text-muted">Memuat file...</span>
                    </div>
                </div>
            </div>
        `;
    }
    function updateRecordCount(count) {
        $('#record-count').text(count || 0);
    }
</script>
