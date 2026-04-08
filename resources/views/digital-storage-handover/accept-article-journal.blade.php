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
                                Judul Serial
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
                { orderable: false, className: 'align-middle text-center' },
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

                let html = `
                    <div class="text-start">
                        <table class="table table-bordered table-sm">
                            <tr>
                                <th width="35%">Judul Artikel</th>
                                <td>${data.ARTICLE_TITLE ?? '-'}</td>
                            </tr>
                            <tr>
                                <th>Judul Jurnal</th>
                                <td>${data.TITLE ?? '-'}</td>
                            </tr>
                            <tr>
                                <th>DOI</th>
                                <td>${data.ARTICLE_DOI ?? '-'}</td>
                            </tr>
                            <tr>
                                <th>Link Artikel</th>
                                <td>${data.ARTICLE_ORIGINAL_LINK ?? '-'}</td>
                            </tr>
                             <tr>
                                <th>Link File</th>
                                <td>${data.ARTICLE_FILE_LINK ?? '-'}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Terima</th>
                                <td>${data.RECEIVED_AT ?? '-'}</td>
                            </tr>
                            <tr>
                                <th>Penulis</th>
                                <td>${data.ARTICLE_CONTRIBUTOR ?? '-'}</td>
                            </tr>
                            <tr>
                                <th>Abstrak</th>
                                <td>${data.ARTICLE_ABSTRACT ?? '-'}</td>
                            </tr>
                            <tr>
                                <th>Katalog</th>
                                <td><iframe src='https://inlis-backup.perpusnas.go.id/inlisnew/KatalogDetailView.aspx?id=${data.CATALOG_ID}' width="400px" height="600px"></iframe></td>
                            </tr>
                        </table>
                    </div>
                `;

                swalInit.fire({
                    title: 'Detail Metadata',
                    html: html,
                    icon: 'info',
                    width: 800,
                    confirmButtonText: 'Tutup',
                    showCloseButton: true
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

    function updateRecordCount(count) {
        $('#record-count').text(count || 0);
    }
</script>
