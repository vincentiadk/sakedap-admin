<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Laporan - <span class="fw-normal">Pengiriman</span>
            </h4>
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
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">
                                <i class="ph-user-circle me-1"></i>
                                Pelaksana Serah
                            </label>
                            <select class="form-select" name="executor_id" id="executor_id" data-placeholder="Semua Pelaksana Serah"></select>
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
                                <i class="ph-buildings me-1"></i>
                                Tujuan
                            </label>
                            <select class="form-select" name="branch_id" id="branch_id" data-placeholder="Semua Tujuan"></select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-barcode me-1"></i>
                                No Resi
                            </label>
                            <input type="text" class="form-control" name="receipt_no" id="receipt_no" placeholder="Masukkan nomor resi">
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-calendar-check me-1"></i>
                                Jenis Tanggal
                            </label>
                            <select class="form-select" name="date_type" id="date_type">
                                <option value="accept_date">Tanggal Diterima</option>
                                <option value="letter_date">Tanggal Pengiriman</option>
                                <option value="createdate">Tanggal Dibuat</option>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-calendar-blank me-1"></i>
                                Tanggal
                            </label>
                            <input type="text" class="form-control" name="date" id="date" placeholder="Pilih tanggal" readonly>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-flag me-1"></i>
                                Status
                            </label>
                            <select class="form-select" name="status" id="status">
                                <option value="">Semua Status</option>
                                <option value="DIAJUKAN">Diajukan</option>
                                <option value="DIKIRIM">Dikirim</option>
                                <option value="DALAM PENGIRIMAN">Dalam Pengiriman</option>
                                <option value="TERKIRIM">Terkirim</option>
                                <option value="CEK FISIK">Cek Fisik</option>
                                <option value="DITERIMA PENUH">Diterima Penuh</option>
                                <option value="DITERIMA PARSIAL">Diterima Parsial</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer border-top">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ url('report/delivery') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
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
                    <i class="ph-package me-1 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Data Pengiriman</h6>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered display nowrap w-100" id="datatable-serverside">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center text-nowrap" style="width: 60px" rowspan="2">
                                <i class="ph-hash"></i>
                            </th>
                            <th class="text-nowrap" style="min-width: 120px" rowspan="2">
                                <i class="ph-calendar me-1"></i>
                                Tgl Pengiriman
                            </th>
                            <th class="text-nowrap" style="min-width: 150px" rowspan="2">
                                <i class="ph-identification-card me-1"></i>
                                Nomor Pengiriman
                            </th>
                            <th class="text-nowrap" style="min-width: 120px" rowspan="2">
                                <i class="ph-calendar-check me-1"></i>
                                Tgl Terima
                            </th>
                            <th class="text-nowrap" style="min-width: 120px" rowspan="2">
                                <i class="ph-calendar-check me-1"></i>
                                Tgl Buat
                            </th>
                            <th class="text-nowrap" style="min-width: 180px" rowspan="2">
                                <i class="ph-user me-1"></i>
                                Pengirim
                            </th>
                            <th class="text-nowrap" style="min-width: 130px" rowspan="2">
                                <i class="ph-phone me-1"></i>
                                No HP
                            </th>
                            <th class="text-nowrap" style="min-width: 200px" rowspan="2">
                                <i class="ph-user-circle me-1"></i>
                                Pelaksana Serah
                            </th>
                            <th class="text-nowrap" style="min-width: 140px" rowspan="2">
                                <i class="ph-file-text me-1"></i>
                                Nomor UT
                            </th>
                            <th class="text-nowrap" style="min-width: 150px" rowspan="2">
                                <i class="ph-barcode me-1"></i>
                                Resi
                            </th>
                            <th class="text-nowrap" style="min-width: 150px" rowspan="2">
                                <i class="ph-truck me-1"></i>
                                Jasa Kirim
                            </th>
                            <th class="text-nowrap" style="min-width: 180px" rowspan="2">
                                <i class="ph-buildings me-1"></i>
                                Tujuan
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 100px" colspan="2">
                                <i class="ph-paper-plane-tilt me-1"></i>
                                Pengiriman
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 100px" colspan="2">
                                <i class="ph-check-circle me-1"></i>
                                Penerimaan
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 100px" colspan="2">
                                <i class="ph-x-circle me-1"></i>
                                Ditolak (Hibah)
                            </th>
                            <th class="text-nowrap" style="min-width: 150px" rowspan="2">
                                <i class="ph-flag me-1"></i>
                                Status
                            </th>
                            <th class="text-nowrap" style="min-width: 130px" rowspan="2">
                                <i class="ph-ticket me-1"></i>
                                Kode Promo
                            </th>
                            <th class="text-nowrap" style="min-width: 130px" rowspan="2">
                                <i class="ph-currency-circle-dollar me-1"></i>
                                Biaya Kirim
                            </th>
                            <th class="text-nowrap" style="min-width: 110px" rowspan="2">
                                <i class="ph-scales me-1"></i>
                                Berat
                            </th>
                            <th class="text-nowrap" style="min-width: 120px" rowspan="2">
                                <i class="ph-package me-1"></i>
                                Jumlah Paket
                            </th>
                        </tr>
                        <tr>
                            <th class="text-center text-nowrap" style="min-width: 80px">
                                <i class="ph-book-open me-1"></i>
                                Judul
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 100px">
                                <i class="ph-stack me-1"></i>
                                Eksemplar
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 80px">
                                <i class="ph-book-open me-1"></i>
                                Judul
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 100px">
                                <i class="ph-stack me-1"></i>
                                Eksemplar
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 80px">
                                <i class="ph-book-open me-1"></i>
                                Judul
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 100px">
                                <i class="ph-stack me-1"></i>
                                Eksemplar
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

        if(parseInt('{{ Main::isSuperAdmin() }}') == 0) {
            select2Serverside('#executor_id', 'executor', {
                province_id: '{{ session("province_id") }}',
            });

            select2Serverside('#branch_id', 'branch', {
                province_id: '{{ session("province_id") }}',
            }, {
                minimumInputLength: 0
            });
        } else {
            select2Serverside('#executor_id', 'executor');
            select2Serverside('#branch_id', 'branch');
        }

        loadData();
    });

    function loadData() {
        window.gDataTable = $('#datatable-serverside').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            scrollX: true,
            destroy: true,
            order: [[0, 'desc']],
            ajax: {
                url: '{{ url("report/delivery/datatable") }}',
                dataType: 'JSON',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    executor_id: $('#executor_id').val(),
                    delivery_service_id: $('#delivery_service_id').val(),
                    date: $('#date').val(),
                    date_type: $('#date_type').val(),
                    status: $('#status').val(),
                    receipt_no: $('#receipt_no').val(),
                    branch_id: $('#branch_id').val(),
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
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: false, className: 'align-middle text-wrap' },
                { orderable: false, className: 'align-middle text-wrap' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle text-center' },
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
            },
        }).on('draw.dt', function() {
            onLoading('close', '#datatable-serverside_wrapper');
        });

        window.gDataTable.columns.adjust().draw();
    }
</script>
