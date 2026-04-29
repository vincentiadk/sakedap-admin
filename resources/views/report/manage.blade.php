<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Laporan - <span class="fw-normal">Penerimaan Digital</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <span class="badge bg-info p-2 bg-opacity-10 text-info">
                    Laporan Penerimaan Digital
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
                                <i class="ph-calendar-blank me-1"></i>
                                Tanggal
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ph-calendar"></i>
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
                                <i class="ph-file me-1"></i>
                                Jenis Media
                            </label>
                            <select class="form-select select2-basic" name="media_id" id="media_id" data-placeholder="Semua Jenis Media">
                                <option value=""></option>
                                @foreach($media as $m)
                                    <option value="{{ $m->ID }}">{{ $m->NAME }} [{{ $m->DEPOSITFORMAT_CODE }}]</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-book-open me-1"></i>
                                Judul
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ph-magnifying-glass"></i>
                                </span>
                                <input type="text" class="form-control" name="title" id="title" placeholder="Cari berdasarkan judul">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-map-pin me-1"></i>
                                Provinsi
                            </label>
                            <select class="form-select" name="province_id" id="province_id" data-placeholder="Semua Provinsi">
                                @if(!Main::isSuperAdmin() && !Main::isPerpusnas())
                                    <option value="{{ session('province_id') }}" selected>{{ session('province_name') }}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-calendar-blank me-1"></i>
                                Tahun
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ph-hash"></i>
                                </span>
                                <input type="number" class="form-control" name="year" id="year" placeholder="Cari berdasarkan tahun">
                            </div>
                        </div>
                         <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-calendar-blank me-1"></i>
                                Diterima Oleh
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ph-hash"></i>
                                </span>
                                <input type="text" class="form-control" name="fullname" id="fullname" placeholder="Cari nama user">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer border-top">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ url('report/manage') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
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
                    <h6 class="mb-0 fw-semibold">Daftar Laporan Pengelolaan</h6>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-info bg-opacity-10 text-info" id="total-records">
                        <i class="ph-list-checks me-1"></i>
                        <span id="record-count">0</span> Data
                    </span>
                    {{-- <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modal-download-excel">
                        <i class="ph-microsoft-excel-logo me-1"></i>
                        Download Excel
                    </button> --}}
                </div>
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
                            <th class="text-nowrap" style="min-width: 100px">
                                <i class="ph-archive-box me-1"></i>
                                ID Katalog
                            </th>
                            <th class="text-nowrap" style="min-width: 100px">
                                <i class="ph-identification-card me-1"></i>
                                ID Pelaksana Serah
                            </th>
                            <th class="text-nowrap" style="min-width: 180px">
                                <i class="ph-user-circle me-1"></i>
                                Nama Pelaksana Serah
                            </th>
                            <th class="text-nowrap" style="min-width: 180px">
                                <i class="ph-calendar me-1"></i>
                                Periode
                            </th>
                            <th class="text-nowrap" style="min-width: 180px">
                                <i class="ph-flow-arrow me-1"></i>
                                Metode
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-map-pin me-1"></i>
                                Provinsi
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-buildings me-1"></i>
                                Kota
                            </th>
                            <th class="text-nowrap" style="min-width: 250px">
                                <i class="ph-book-open me-1"></i>
                                Judul
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-file-text me-1"></i>
                                Jenis
                            </th>
                            <th class="text-nowrap" style="min-width: 130px">
                                <i class="ph-music-notes me-1"></i>
                                Album
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-list-numbers me-1"></i>
                                Seri
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-newspaper me-1"></i>
                                Edisi
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-barcode me-1"></i>
                                Serial
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-tag me-1"></i>
                                DDC
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-books me-1"></i>
                                Volume
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-qr-code me-1"></i>
                                Kode
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-identification-badge me-1"></i>
                                Deposit
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-archive me-1"></i>
                                INLIS
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 100px">
                                <i class="ph-calendar me-1"></i>
                                Tahun Terbit
                            </th>
                            <th class="text-nowrap" style="min-width: 100px">
                                <i class="ph-copyright me-1"></i>
                                Copyright
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 100px">
                                <i class="ph-eye me-1"></i>
                                Preview
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 100px">
                                <i class="ph-key me-1"></i>
                                Kunci
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 100px">
                                <i class="ph-book me-1"></i>
                                Manual
                            </th>
                            <th class="text-nowrap" style="min-width: 100px">
                                <i class="ph-lock-open me-1"></i>
                                Akses
                            </th>
                            <th class="text-nowrap" style="min-width: 100px">
                                <i class="ph-users me-1"></i>
                                Kontributor
                            </th>
                            <th class="text-nowrap" style="min-width: 100px">
                                <i class="ph-tag-simple me-1"></i>
                                Subjek
                            </th>
                            <th class="text-nowrap" style="min-width: 100px">
                                <i class="ph-file-arrow-down me-1"></i>
                                Size File
                            </th>
                            <th class="text-nowrap" style="min-width: 100px">
                                <i class="ph-file-doc me-1"></i>
                                Jenis File
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 130px">
                                <i class="ph-calendar-check me-1"></i>
                                Tgl Diserahkan
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 130px">
                                <i class="ph-calendar-check me-1"></i>
                                Tgl Diterima
                            </th>
                            <th class="text-nowrap" style="min-width: 100px">
                                <i class="ph-money me-1"></i>
                                Harga
                            </th>
                            <th class="text-nowrap" style="min-width: 100px">
                                <i class="ph-money me-1"></i>
                                Diterima Oleh
                            </th>
                            <th class="text-nowrap" style="min-width: 100px">
                                <i class="ph-money me-1"></i>
                                DOI
                            </th>
                            <th class="text-nowrap" style="min-width: 100px">
                                <i class="ph-phone-call me-1"></i>
                                NPL
                            </th>
                            <th class="text-nowrap" style="min-width: 100px">
                                <i class="ph-anchor me-1"></i>
                                NID
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="modal-download-excel" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ph-microsoft-excel-logo me-1"></i>
                    Download Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="ph-calendar-blank me-1"></i>
                            Tanggal
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="ph-calendar"></i>
                            </span>
                            <input type="text" class="form-control" name="de_date" id="de_date" placeholder="Pilih tanggal">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="ph-user-circle me-1"></i>
                            Pelaksana Serah
                        </label>
                        <select class="form-select" name="de_executor_id" id="de_executor_id" data-placeholder="Semua Pelaksana" data-dropdown-parent="#modal-download-excel"></select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="ph-file me-1"></i>
                            Jenis Media
                        </label>
                        <select class="form-select select2-basic" name="de_media_id" id="de_media_id" data-placeholder="Semua Jenis Media" data-dropdown-parent="#modal-download-excel">
                            <option value=""></option>
                            @foreach($media as $m)
                                <option value="{{ $m->ID }}">{{ $m->NAME }} [{{ $m->DEPOSITFORMAT_CODE }}]</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="ph-book-open me-1"></i>
                            Judul
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="ph-magnifying-glass"></i>
                            </span>
                            <input type="text" class="form-control" name="de_title" id="de_title" placeholder="Cari berdasarkan judul">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="ph-map-pin me-1"></i>
                            Provinsi
                        </label>
                        <select class="form-select" name="de_province_id" id="de_province_id" data-placeholder="Semua Provinsi" data-dropdown-parent="#modal-download-excel">
                            @if(!Main::isSuperAdmin() && !Main::isPerpusnas())
                                <option value="{{ session('province_id') }}" selected>{{ session('province_name') }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            <i class="ph-calendar-blank me-1"></i>
                            Tahun
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="ph-hash"></i>
                            </span>
                            <input type="number" class="form-control" name="de_year" id="de_year" placeholder="Cari berdasarkan tahun">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ph-x me-1"></i>
                    Batal
                </button>
                <button type="button" class="btn btn-success" onclick="downloadExcel()">
                    <i class="ph-download me-1"></i>
                    Download Excel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        datePickerBasic('#date');

        datePickerBasic('#de_date', {
            dateLimit: {
                months: 1
            }
        });

        if(parseInt('{{ Main::isSuperAdmin() }}') == 0 && parseInt('{{ Main::isPerpusnas() }}') == 0) {
            select2Serverside('#province_id, #de_province_id', 'location', {
                for: 'province',
                province_id: '{{ session("province_id") }}',
            }, {
                minimumInputLength: 0
            });

            select2Serverside('#executor_id, #de_executor_id', 'executor', {
                province_id: '{{ session("province_id") }}',
            });
        } else {
            select2Serverside('#executor_id, #de_executor_id', 'executor');

            select2Serverside('#province_id, #de_province_id', 'location', {
                for: 'province'
            }, {
                minimumInputLength: 0
            });
        }

        loadData();
        notifSuccessFromSession();
    });

    function notifSuccessFromSession() {
        var notif = '{{ session("success") }}';

        if(notif) {
            swalInit.fire('Berhasil', notif, 'success');
        }
    }

    function downloadExcel() {
        var queryString = {
            exported: true,
            title: $('#de_title').val(),
            executor_id: $('#de_executor_id').val(),
            province_id: $('#de_province_id').val(),
            year: $('#de_year').val(),
            media_id: $('#de_media_id').val(),
            date: $('#de_date').val(),
        }

        onLoading('show', 'body');

        location.href = '{{ url("report/manage?") }}' + $.param(queryString);
    }

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
            order: [[0, 'desc']],
            ajax: {
                url: '{{ url("report/manage/datatable") }}',
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
                { orderable: false, className: 'align-middle text-center', export: false },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
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

    function updateRecordCount(count) {
        $('#record-count').text(count || 0);
    }
</script>
