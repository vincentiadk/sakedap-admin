<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                <span class="fw-normal">Log Aktivitas</span>
            </h4>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="card border-0 shadow-sm">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ph-funnel me-2 text-primary"></i>
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ph-table me-1"></i>
                                    Tabel
                                </label>
                                <select class="form-select select2-basic" name="table_name" id="table_name" data-placeholder="Semua Tabel">
                                    <option value=""></option>
                                    @foreach($tableName as $t)
                                        <option value="{{ $t->NAME }}">{{ $t->NAME }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ph-lightning me-1"></i>
                                    Aksi
                                </label>
                                <select class="form-select select2-basic" name="action" id="action" data-placeholder="Semua Aksi">
                                    <option value=""></option>
                                    @foreach($action as $a)
                                        <option value="{{ $a->NAME }}">{{ $a->NAME }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
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
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ph-user me-1"></i>
                                    User
                                </label>
                                <select class="form-select select2-basic" name="action_by" id="action_by" data-placeholder="Semua User">
                                    @if(!Main::isSuperAdmin() && !Main::isPerpusnas())
                                        <option value="{{ session('username') }}" selected>{{ session('username') }}</option>
                                    @else
                                        <option value=""></option>
                                        @foreach($actionBy as $ab)
                                            <option value="{{ $ab->NAME }}">{{ $ab->NAME }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer border-top">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ url('log-activity') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
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
                    <i class="ph-list-bullets me-2 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Daftar Log Aktivitas</h6>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary" id="total-records">
                        <i class="ph-list-checks me-1"></i>
                        <span id="record-count">0</span> Data
                    </span>
                    {{-- <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modal-download-excel">
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
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-lightning me-1"></i>
                                Aksi
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-table me-1"></i>
                                Tabel
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-user me-1"></i>
                                User
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 130px">
                                <i class="ph-calendar-check me-1"></i>
                                Tanggal
                            </th>
                            <th class="text-nowrap" style="min-width: 130px">
                                <i class="ph-broadcast me-1"></i>
                                IP Address
                            </th>
                            <th class="text-nowrap" style="min-width: 250px">
                                <i class="ph-info me-1"></i>
                                Keterangan
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="modal-download-excel" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-semibold">
                    <i class="ph-microsoft-excel-logo me-2 text-success"></i>
                    Download Excel Log Aktivitas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info border-0 mb-4">
                    <div class="d-flex align-items-center">
                        <i class="ph-info me-2"></i>
                        <div>
                            <strong>Informasi:</strong> Pilih filter untuk mengunduh data log aktivitas sesuai kriteria yang diinginkan.
                        </div>
                    </div>
                </div>
                <form id="form-download">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ph-lightning me-1"></i>
                                    Aksi
                                </label>
                                <select class="form-select select2-basic" name="de_action" id="de_action" data-placeholder="Semua Aksi" data-dropdown-parent="#modal-download-excel">
                                    <option value=""></option>
                                    @foreach($action as $a)
                                        <option value="{{ $a->NAME }}">{{ $a->NAME }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ph-calendar me-1"></i>
                                    Tanggal
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ph-calendar-blank"></i>
                                    </span>
                                    <input type="text" class="form-control" name="de_date" id="de_date" placeholder="Pilih tanggal">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ph-user me-1"></i>
                                    User
                                </label>
                                <select class="form-select select2-basic" name="de_action_by" id="de_action_by" data-placeholder="Semua User" data-dropdown-parent="#modal-download-excel">
                                    @if(!Main::isSuperAdmin() && !Main::isPerpusnas())
                                        <option value="{{ session('username') }}" selected>{{ session('username') }}</option>
                                    @else
                                        <option value=""></option>
                                        @foreach($actionBy as $ab)
                                            <option value="{{ $ab->NAME }}">{{ $ab->NAME }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ph-x me-1"></i>
                    Tutup
                </button>
                <button type="button" class="btn btn-success" onclick="downloadExcel()">
                    <i class="ph-download-simple me-1"></i>
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

        loadData();
        notifSuccessFromSession();
    });

    function notifSuccessFromSession() {
        var notif = '{{ session("success") }}';

        if(notif) {
            swalInit.fire({
                title: 'Berhasil',
                text: notif,
                icon: 'success',
                showCloseButton: false
            });
        }
    }

    function updateRecordCount(count) {
        $('#record-count').text(count || 0);
    }

    function downloadExcel() {
        var queryString = {
            exported: true,
            action: $('#de_action').val(),
            action_by: $('#de_action_by').val(),
            date: $('#de_date').val(),
        }

        onLoading('show', '.modal-content');

        setTimeout(function() {
            location.href = '{{ url("report/log?") }}' + $.param(queryString);
            onLoading('close', '.modal-content');
            $('#modal-download-excel').modal('hide');
        }, 500);
    }

    function loadData() {
        window.gDataTable = $('#datatable-serverside').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            scrollX: true,
            destroy: true,
            order: [[0, 'desc']],
            ajax: {
                url: '{{ url("log-activity/datatable") }}',
                dataType: 'JSON',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    action: $('#action').val(),
                    action_by: $('#action_by').val(),
                    date: $('#date').val(),
                    table_name: $('#table_name').val(),
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
                { orderable: true, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle' },
                { orderable: false, className: 'align-middle text-wrap' },
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
</script>
