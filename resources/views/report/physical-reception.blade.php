<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Laporan - <span class="fw-normal">Penerimaan Fisik</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <span class="badge bg-info p-2 bg-opacity-10 text-info">
                    <i class="ph-chart-line me-1"></i>
                    Laporan Penerimaan
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
                        <div class="col-lg-4 col-md-12">
                            <label class="form-label fw-semibold">
                                <i class="ph-user-circle me-1"></i>
                                Pelaksana Serah
                            </label>
                            <select class="form-select" name="executor_id" id="executor_id" data-placeholder="Semua Pelaksana"></select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-calendar-blank me-1"></i>
                                Tanggal
                            </label>
                            <div class="input-group">
                                <select class="form-select w-auto flex-grow-0" name="date_type" id="date_type" style="max-width: 130px;">
                                    <option value="accept_date">Diterima</option>
                                    <option value="letter_date">Pengiriman</option>
                                    <option value="createdate">Dibuat</option>
                                </select>
                                <input type="text" class="form-control" name="date" id="date" placeholder="Pilih tanggal">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-truck me-1"></i>
                                Jasa Kirim
                            </label>
                            <select class="form-select select2-basic" name="delivery_service_id" id="delivery_service_id" data-placeholder="Semua Jasa Kirim">
                                <option value=""></option>
                                @foreach($deliveryService as $ds)
                                    <option value="{{ $ds->ID }}">{{ $ds->NAME }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-flag me-1"></i>
                                Status
                            </label>
                            <select class="form-select" name="status" id="status">
                                <option value="">Semua Status</option>
                                <option value="DITERIMA PARSIAL">Diterima Parsial</option>
                                <option value="DITERIMA PENUH">Diterima Penuh</option>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-user-plus me-1"></i>
                                Nama Pengguna
                            </label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="Cari nama pengguna">
                        </div>
                        
                    </div>
                </form>
            </div>
            </div> <!-- card filter selesai -->

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header border-bottom">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="ph-chart-bar me-1 text-success"></i>
                <h6 class="mb-0 fw-semibold">Summary Penerimaan Fisik</h6>
            </div>
            <div class="d-flex align-items-center gap-2">
                <select class="form-select form-select-sm" id="summary_period" style="width: 160px;">
                    <option value="daily">Harian</option>
                    <option value="monthly">Bulanan</option>
                    <option value="yearly">Tahunan</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered w-100" id="datatable-summary">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width:60px">No</th>
                        <th>Nama Orang</th>
                        <th>Periode</th>
                        <th class="text-center">Total Judul</th>
                        <th class="text-center">Total Eks</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header border-bottom">
        ...
            <div class="card-footer border-top">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ url('report/physical-reception') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
                        <i class="ph-arrow-counter-clockwise me-1"></i>
                        Reset Filter
                    </a>
                    <button type="button" class="btn btn-primary" onclick="reloadReport()">
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
                    <h6 class="mb-0 fw-semibold">Daftar Laporan Penerimaan Fisik</h6>
                </div>
                <span class="badge bg-info bg-opacity-10 text-info" id="total-records">
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
                            <th class="text-nowrap" style="min-width: 250px">
                                <i class="ph-book-open me-1"></i>
                                Judul
                            </th>
                            <th class="text-nowrap" style="min-width: 250px">
                                <i class="ph-calendar-check me-1"></i>
                                Tgl Terima
                            </th>
                            <th class="text-nowrap" style="min-width: 250px">
                                <i class="ph-calendar-plus me-1"></i>
                                Tgl Buat
                            </th>
                            <th class="text-nowrap" style="min-width: 180px">
                                <i class="ph-user-circle me-1"></i>
                                Pelaksana Serah
                            </th>
                            <th class="text-nowrap" style="min-width: 200px">
                                <i class="ph-map-pin me-1"></i>
                                Tujuan
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-truck me-1"></i>
                                Jasa Kirim
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-barcode me-1"></i>
                                Resi
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 100px">
                                <i class="ph-stack me-1"></i>
                                Jumlah Eks
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 120px">
                                <i class="ph-check-circle me-1"></i>
                                Jumlah Diterima
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 120px">
                                <i class="ph-x-circle me-1"></i>
                                Jumlah Ditolak
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 120px">
                                <i class="ph-gift me-1"></i>
                                Jumlah Hibah
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 140px">
                                <i class="ph-arrow-u-up-left me-1"></i>
                                Jumlah Dikembalikan
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 120px">
                                <i class="ph-check-square me-1"></i>
                                Jumlah Verif
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-file me-1"></i>
                                Jenis Media
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 120px">
                                <i class="ph-flag me-1"></i>
                                Status
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-currency-circle-dollar me-1"></i>
                                Harga
                            </th>
                            <th class="text-nowrap" style="min-width: 130px">
                                <i class="ph-identification-card me-1"></i>
                                ISBN
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 110px">
                                <i class="ph-calendar me-1"></i>
                                Tahun Terbit
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-check-circle me-1"></i>
                                ISBN Status
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-newspaper me-1"></i>
                                Edisi Serial
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 110px">
                                <i class="ph-arrow-right me-1"></i>
                                TTES Awal
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 110px">
                                <i class="ph-arrow-left me-1"></i>
                                TTES Akhir
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-clock me-1"></i>
                                Kala Terbit
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-database me-1"></i>
                                Katalog ID
                            </th>
                            <th class="text-nowrap" style="min-width: 100px">
                                <i class="ph-books me-1"></i>
                                Jilid
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-qr-code me-1"></i>
                                QRCBN
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-list me-1"></i>
                                ISBD
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-user-plus me-1"></i>
                                Create By
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-package me-1"></i>
                                Received By
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-check me-1"></i>
                                Verified By
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="modal-detail" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ph-info me-1"></i>
                    Detail Data
                </h5>
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">
                    <i class="ph-x"></i>
                </button>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        datePickerBasic('#date');

        if(parseInt('{{ Main::isSuperAdmin() }}') == 0 && parseInt('{{ Main::isPerpusnas() }}') == 0) {
            select2Serverside('#executor_id', 'executor', {
                province_id: '{{ session("province_id") }}',
            });
        } else {
            select2Serverside('#executor_id', 'executor');
        }

        loadData();
        loadSummary();
    });
    $(document).on('change', '#summary_period', function () {
        loadSummary();
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
            order: [[0, 'desc']],
            ajax: {
                url: '{{ url("report/physical-reception/datatable") }}',
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
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-end' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
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

                updateRecordCount(json.recordsFiltered);
            },
            drawCallback: function(settings) {
                var api = this.api();

                updateRecordCount(api.page.info().recordsFiltered);
            }
        }).on('draw.dt', function() {
            onLoading('close', '#datatable-serverside_wrapper');
        });
    }

    function updateRecordCount(count) {
        $('#record-count').text(count || 0);
    }
    function loadSummary() {
        if ($.fn.DataTable.isDataTable('#datatable-summary')) {
            window.gSummaryTable.ajax.reload();
            return;
        }

        window.gSummaryTable = $('#datatable-summary').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            scrollX: true,
            destroy: true,
            order: [[2, 'desc'], [1, 'asc']],
            ajax: {
                url: '{{ url("report/physical-reception/datatable-summary") }}',
                dataType: 'JSON',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (d) {
                    $('#form-filter').serializeArray().forEach(function(item) {
                        d[item.name] = item.value;
                    });

                    d.period = $('#summary_period').val();

                    return d;
                },
                beforeSend: function() {
                    onLoading('show', '#datatable-summary_wrapper');
                },
                error: function(response) {
                    onLoading('close', '#datatable-summary_wrapper');
                    responseError(response);
                }
            },
            columns: [
                { orderable: false, className: 'align-middle text-center fw-semibold' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-nowrap' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-center' },
            ]
        }).on('draw.dt', function() {
            onLoading('close', '#datatable-summary_wrapper');
        });
    }

    function reloadReport() {
        loadSummary();
        loadData();
    }
</script>
