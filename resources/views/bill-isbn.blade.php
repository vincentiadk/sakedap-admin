<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                <span class="fw-normal">Tagihan ISBN</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <span class="badge bg-warning p-2 bg-opacity-10 text-warning">
                    <i class="ph-file-text me-1"></i>
                    Tagihan ISBN
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
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                <i class="ph-book me-1"></i>
                                Judul
                            </label>
                            <input type="text" class="form-control" name="title" id="title" placeholder="Cari judul">
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-user-circle me-1"></i>
                                Pelaksana Serah
                            </label>
                            <input type="text" class="form-control" name="executor" id="executor" placeholder="Cari pelaksana serah">
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-pen me-1"></i>
                                Kepengarangan
                            </label>
                            <input type="text" class="form-control" name="author" id="author" placeholder="Cari kepengarangan">
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-calendar-blank me-1"></i>
                                Tahun Terbit
                            </label>
                            <select class="form-select select2-basic" name="year" id="year" data-placeholder="Semua Tahun">
                                <option value=""></option>
                                @for($i = date('Y'); $i >= 1998; $i--)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-map-pin me-1"></i>
                                Tempat Terbit
                            </label>
                            <input type="text" class="form-control" name="city" id="city" placeholder="Cari tempat terbit">
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-barcode me-1"></i>
                                Nomor ISBN
                            </label>
                            <input type="text" class="form-control" name="code" id="code" placeholder="Cari nomor ISBN">
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-tag me-1"></i>
                                Subjek
                            </label>
                            <input type="text" class="form-control" name="subject" id="subject" placeholder="Cari subjek">
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-list me-1"></i>
                                Sinopsis Class
                            </label>
                            <input type="text" class="form-control" name="sinopsis_class" id="sinopsis_class" placeholder="Cari sinopsis class">
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-phone me-1"></i>
                                Nomor Panggil
                            </label>
                            <input type="text" class="form-control" name="call_number" id="call_number" placeholder="Cari nomor panggil">
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-file me-1"></i>
                                Media
                            </label>
                            <select class="form-select" name="media" id="media">
                                <option value="">Semua Media</option>
                                <option value="cetak">Cetak</option>
                                <option value="digital pdf">Digital PDF</option>
                                <option value="digital epub">Digital EPUB</option>
                                <option value="audio book">Audio Book</option>
                                <option value="audio visual book">Audio Visual Book</option>
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-map-trifold me-1"></i>
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
                                <i class="ph-calendar-check me-1"></i>
                                Tgl Terima KCKR
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ph-calendar-blank"></i>
                                </span>
                                <input type="text" class="form-control date-range-picker" name="received_date_kckr" id="received_date_kckr" placeholder="Semua tanggal" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-calendar-check me-1"></i>
                                Tgl Terima Provinsi
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ph-calendar-blank"></i>
                                </span>
                                <input type="text" class="form-control date-range-picker" name="received_date_province" id="received_date_province" placeholder="Semua tanggal" readonly>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer border-top">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ url('bill-isbn') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
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
                    <h6 class="mb-0 fw-semibold">Daftar Tagihan ISBN</h6>
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
                            <th class="text-center text-nowrap" style="min-width: 120px">
                                <i class="ph-flag me-1"></i>
                                Status
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 120px">
                                <i class="ph-image me-1"></i>
                                Cover
                            </th>
                            <th class="text-nowrap" style="min-width: 250px">
                                <i class="ph-book me-1"></i>
                                Judul
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-pen me-1"></i>
                                Kepengarangan
                            </th>
                            <th class="text-nowrap" style="min-width: 180px">
                                <i class="ph-user-circle me-1"></i>
                                Pelaksana Serah
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 80px">
                                <i class="ph-calendar-blank me-1"></i>
                                Tahun
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-map-pin me-1"></i>
                                Tempat
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-map-trifold me-1"></i>
                                Provinsi
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-barcode me-1"></i>
                                ISBN
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-file me-1"></i>
                                Media
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-books me-1"></i>
                                Pustaka
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 130px">
                                <i class="ph-calendar-check me-1"></i>
                                Tgl Terima KCKR
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 150px">
                                <i class="ph-calendar-check me-1"></i>
                                Tgl Terima Provinsi
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 100px">
                                <i class="ph-note me-1"></i>
                                Sinopsis
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 130px">
                                <i class="ph-calendar me-1"></i>
                                Tgl Terima
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 130px">
                                <i class="ph-calendar-plus me-1"></i>
                                Tgl Dibuat
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 130px">
                                <i class="ph-clock-clockwise me-1"></i>
                                Tgl Update
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
        datePickerBasic('.date-range-picker');

        if(parseInt('{{ Main::isSuperAdmin() }}') == 1 || parseInt('{{ Main::isPerpusnas() }}') == 1) {
            select2Serverside('#province_id', 'location', {
                for: 'province'
            }, {
                minimumInputLength: 0
            });
        } else {
            select2Serverside('#province_id', 'location', {
                for: 'province',
                province_id: '{{ session("province_id") }}',
            }, {
                minimumInputLength: 0
            });
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
            ajax: {
                url: '{{ url("bill-isbn/datatable") }}',
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
                { orderable: false, className: 'align-middle text-center fw-semibold' },
                { orderable: false, className: 'align-middle text-center', export: false },
                { orderable: false, className: 'align-middle text-center', export: false },
                { orderable: false, className: 'align-middle text-wrap' },
                { orderable: false, className: 'align-middle text-center', export: false },
                { orderable: false, className: 'align-middle text-wrap' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle text-wrap' },
                { orderable: false, className: 'align-middle text-wrap' },
                { orderable: false, className: 'align-middle' },
                { orderable: false, className: 'align-middle' },
                { orderable: false, className: 'align-middle text-wrap' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle text-center', export: false },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle text-center' },
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

        window.gDataTable.columns.adjust().draw();
    }

    function updateRecordCount(count) {
        $('#record-count').text(count || 0);
    }
</script>
