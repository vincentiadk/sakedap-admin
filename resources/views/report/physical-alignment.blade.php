<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Laporan - <span class="fw-normal">Penjajaran Fisik</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <span class="badge bg-info p-2 bg-opacity-10 text-info">
                    Laporan Penjajaran
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
                        <div class="col-lg-6 col-md-6">
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
                        <div class="col-lg-6 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-files me-1"></i>
                                Jenis Bahan
                            </label>
                            <select class="form-select select2-basic" name="worksheet_id" id="worksheet_id" data-placeholder="Semua Jenis Bahan">
                                <option value=""></option>
                                @foreach($worksheet as $w)
                                    <option value="{{ $w->ID }}">{{ $w->NAME }} [{{ $w->CATEGORY }}]</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-file me-1"></i>
                                Media
                            </label>
                            <select class="form-select select2-basic" name="media_id" id="media_id" data-placeholder="Semua Media">
                                <option value=""></option>
                                @foreach($media as $m)
                                    <option value="{{ $m->ID }}">{{ $m->NAME }} [{{ $m->DEPOSITFORMAT_CODE }}]</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-arrow-circle-down me-1"></i>
                                Sumber
                            </label>
                            <select class="form-select select2-basic" name="source_id" id="source_id" data-placeholder="Semua Sumber">
                                <option value=""></option>
                                @foreach($source as $s)
                                    <option value="{{ $s->ID }}">{{ $s->NAME }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-tag me-1"></i>
                                Kategori
                            </label>
                            <select class="form-select select2-basic" name="category_id" id="category_id" data-placeholder="Semua Kategori">
                                <option value=""></option>
                                @foreach($category as $c)
                                    <option value="{{ $c->ID }}">{{ $c->NAME }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-lock-key me-1"></i>
                                Akses
                            </label>
                            <select class="form-select select2-basic" name="access_id" id="access_id" data-placeholder="Semua Akses">
                                <option value=""></option>
                                @foreach($access as $a)
                                    <option value="{{ $a->ID }}">{{ $a->NAME }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-map-pin me-1"></i>
                                Lokasi
                            </label>
                            <select class="form-select select2-basic" name="location_id" id="location_id" data-placeholder="Semua Lokasi">
                                <option value=""></option>
                                @foreach($location as $l)
                                    <option value="{{ $l->ID }}">{{ $l->NAME }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-bookmarks me-1"></i>
                                Rak
                            </label>
                            <select class="form-select select2-basic" name="rack_id" id="rack_id" data-placeholder="Semua Rak">
                                <option value=""></option>
                                @foreach($rack as $r)
                                    <option value="{{ $r->ID }}">{{ $r->NAME }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-rows me-1"></i>
                                Ambal
                            </label>
                            <select class="form-select select2-basic" name="carpet_id" id="carpet_id" data-placeholder="Semua Ambal">
                                <option value=""></option>
                                @foreach($carpet as $c)
                                    <option value="{{ $c->ID }}">{{ $c->NAME }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-check-circle me-1"></i>
                                Ketersediaan
                            </label>
                            <select class="form-select select2-basic" name="availability" id="availability" data-placeholder="Semua Ketersediaan">
                                <option value=""></option>
                                @foreach($availability as $a)
                                    <option value="{{ $a->NAME }}">{{ $a->NAME }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer border-top">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ url('report/physical-alignment') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
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
                    <h6 class="mb-0 fw-semibold">Daftar Laporan Penjajaran Fisik</h6>
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
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-identification-badge me-1"></i>
                                Item ID
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-number-square-one me-1"></i>
                                No Induk
                            </th>
                            <th class="text-nowrap" style="min-width: 300px">
                                <i class="ph-book-open me-1"></i>
                                Data Bibliografis
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-number-circle-one me-1"></i>
                                No Panggil
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-files me-1"></i>
                                Jenis Bahan
                            </th>
                            <th class="text-nowrap" style="min-width: 130px">
                                <i class="ph-file me-1"></i>
                                Media
                            </th>
                            <th class="text-nowrap" style="min-width: 130px">
                                <i class="ph-tag me-1"></i>
                                Kategori
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-lock-key me-1"></i>
                                Akses
                            </th>
                            <th class="text-nowrap" style="min-width: 140px">
                                <i class="ph-check-circle me-1"></i>
                                Ketersediaan
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-map-pin me-1"></i>
                                Lokasi
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-bookmarks me-1"></i>
                                Rak
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-rows me-1"></i>
                                Ambal
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 130px">
                                <i class="ph-calendar-check me-1"></i>
                                Tgl Shelving
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
            order: [[0, 'desc']],
            ajax: {
                url: '{{ url("report/physical-alignment/datatable") }}',
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
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
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
