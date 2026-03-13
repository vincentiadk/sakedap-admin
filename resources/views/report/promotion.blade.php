<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Laporan - <span class="fw-normal">Promosi</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <span class="badge bg-info p-2 bg-opacity-10 text-info">
                    <i class="ph-chart-line me-1"></i>
                    Laporan Promosi
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
                        <div class="col-lg-3 col-md-6">
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
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-user-circle me-1"></i>
                                Pelaksana Serah
                            </label>
                            <select class="form-select" name="executor_id" id="executor_id" data-placeholder="Semua Pelaksana"></select>
                        </div>
                        <div class="col-lg-3 col-md-6">
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
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-tag me-1"></i>
                                Promo
                            </label>
                            <select class="form-select" name="promotion_id" id="promotion_id" data-placeholder="Semua Promo"></select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer border-top">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ url('report/promotion') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
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
                    <h6 class="mb-0 fw-semibold">Daftar Laporan Promosi</h6>
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
                            <th class="text-nowrap" style="min-width: 200px">
                                <i class="ph-book-open me-1"></i>
                                Judul
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-qr-code me-1"></i>
                                Kode
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-wallet me-1"></i>
                                Saldo
                            </th>
                            <th class="text-nowrap" style="min-width: 100px">
                                <i class="ph-percent me-1"></i>
                                Diskon
                            </th>
                            <th class="text-nowrap" style="min-width: 140px">
                                <i class="ph-package me-1"></i>
                                Min Jumlah Paket
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-scissors me-1"></i>
                                Potongan
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 130px">
                                <i class="ph-calendar-check me-1"></i>
                                Tgl Pengiriman
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-file-text me-1"></i>
                                Nomor Pengiriman
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-user me-1"></i>
                                Pengirim
                            </th>
                            <th class="text-nowrap" style="min-width: 180px">
                                <i class="ph-user-circle me-1"></i>
                                Pelaksana Serah
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-truck me-1"></i>
                                Jasa Kirim
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-barcode me-1"></i>
                                Resi
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-currency-circle-dollar me-1"></i>
                                Biaya Kirim
                            </th>
                            <th class="text-nowrap" style="min-width: 100px">
                                <i class="ph-scales me-1"></i>
                                Berat
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-stack me-1"></i>
                                Jumlah Paket
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
                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="ph-user-circle me-1"></i>
                            Pelaksana Serah
                        </label>
                        <select class="form-select" name="de_executor_id" id="de_executor_id" data-placeholder="Semua Pelaksana" data-dropdown-parent="#modal-download-excel"></select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="ph-truck me-1"></i>
                            Jasa Kirim
                        </label>
                        <select class="form-select select2-basic" name="de_delivery_service_id" id="de_delivery_service_id" data-placeholder="Semua Jasa Kirim" data-dropdown-parent="#modal-download-excel">
                            <option value=""></option>
                            @foreach($deliveryService as $ds)
                                <option value="{{ $ds->ID }}">{{ $ds->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="ph-tag me-1"></i>
                            Promo
                        </label>
                        <select class="form-select" name="de_promotion_id" id="de_promotion_id" data-placeholder="Semua Promo" data-dropdown-parent="#modal-download-excel"></select>
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
            select2Serverside('#executor_id, #de_executor_id', 'executor', {
                province_id: '{{ session("province_id") }}',
            });

            select2Serverside('#promotion_id, #de_promotion_id', 'promotion', {
                province_id: '{{ session("province_id") }}',
            });
        } else {
            select2Serverside('#executor_id, #de_executor_id', 'executor');
            select2Serverside('#promotion_id, #de_promotion_id', 'promotion');
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
            executor_id: $('#de_executor_id').val(),
            date: $('#de_date').val(),
            delivery_service_id: $('#de_delivery_service_id').val(),
            promotion_id: $('#de_promotion_id').val(),
        }

        onLoading('show', 'body');

        location.href = '{{ url("report/promotion?") }}' + $.param(queryString);
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
            destroy: true,
            order: [[0, 'desc']],
            ajax: {
                url: '{{ url("report/promotion/datatable") }}',
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
                { orderable: true, className: 'align-middle text-end' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-end' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-end' },
                { orderable: true, className: 'align-middle text-end' },
                { orderable: true, className: 'align-middle text-center' },
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
</script>
