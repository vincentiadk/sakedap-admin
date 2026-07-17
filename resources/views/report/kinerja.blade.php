<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Laporan - <span class="fw-normal">Kinerja Saya</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <span class="badge bg-primary p-2 bg-opacity-10 text-primary">
                    <i class="ph-user me-1"></i>{{ session('username') }}
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
                    <h6 class="mb-0 fw-semibold">Filter</h6>
                </div>
                <button type="button" class="btn btn-sm btn-light" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="ph-caret-down"></i>
                </button>
            </div>
        </div>
        <div class="collapse show" id="filterCollapse">
            <div class="card-body">
                <form id="form-filter">
                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-calendar me-1"></i>Tanggal Aksi
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ph-calendar-blank"></i></span>
                                <input type="text" class="form-control" name="date" id="date" placeholder="Pilih rentang tanggal">
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-lightning me-1"></i>Jenis Aksi
                            </label>
                            <input type="text" class="form-control" name="action" id="action" placeholder="cth: update, insert, delete">
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-table me-1"></i>Nama Tabel
                            </label>
                            <input type="text" class="form-control" name="table_name" id="table_name" placeholder="cth: E_COLLECTIONS">
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer border-top">
                <div class="d-flex justify-content-between gap-2">
                    <button type="button" class="btn btn-success" onclick="doExport()">
                        <i class="ph-file-xls me-1"></i> Unduh Excel
                    </button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-danger" onclick="resetFilter()">
                            <i class="ph-arrow-counter-clockwise me-1"></i> Reset
                        </button>
                        <button type="button" class="btn btn-primary" onclick="loadData()">
                            <i class="ph-magnifying-glass me-1"></i> Cari
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ph-chart-bar me-1 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Riwayat Aktivitas</h6>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary" id="total-badge">
                    <span id="record-count">0</span> Data
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered display nowrap w-100" id="datatable-serverside">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width:50px">#</th>
                            <th>Aksi</th>
                            <th>Tabel</th>
                            <th>Username</th>
                            <th>Tanggal Aksi</th>
                            <th>Keterangan</th>
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
            scrollX: true,
            destroy: true,
            order: [[4, 'desc']],
            ajax: {
                url: '{{ url("report/kinerja/datatable") }}',
                type: 'POST',
                dataType: 'JSON',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: function(d) {
                    $('#form-filter').serializeArray().forEach(i => d[i.name] = i.value);
                    return d;
                },
                beforeSend: () => onLoading('show', '#datatable-serverside_wrapper'),
                complete: () => onLoading('close', '#datatable-serverside_wrapper'),
                error: (r) => { responseError(r); }
            },
            columns: [
                { orderable: false, className: 'align-middle text-center fw-semibold' },
                { orderable: true,  className: 'align-middle' },
                { orderable: true,  className: 'align-middle' },
                { orderable: true,  className: 'align-middle' },
                { orderable: true,  className: 'align-middle text-nowrap' },
                { orderable: false, className: 'align-middle text-wrap' },
            ],
            initComplete: function(settings, json) {
                updateCount(json ? json.recordsFiltered : 0);
                const inp = $('div.dataTables_filter input');
                inp.off().unbind();
                inp.on('keyup', debounce(function() { this.api && this.api().search(this.value).draw(); }, 500));
            },
            drawCallback: function(settings) {
                updateCount(this.api().page.info().recordsDisplay);
            }
        });
    }

    function updateCount(n) { $('#record-count').text(n || 0); }

    function resetFilter() {
        $('#form-filter')[0].reset();
        loadData();
    }

    function doExport() {
        const params = new URLSearchParams($('#form-filter').serialize());
        window.location.href = '{{ url("report/kinerja/export") }}?' + params.toString();
    }
</script>
