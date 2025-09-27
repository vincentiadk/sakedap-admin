<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Laporan - <span class="fw-normal">Performa User</span>
            </h4>
            <a href="#page-header" class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto" data-bs-toggle="collapse">
                <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
            </a>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="card">
        <div class="card-header">
            <h5 class="hstack gap-2 mb-0">Filter Data</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Aksi :</label>
                        <select class="form-select select2-basic" name="action" id="action" data-placeholder="Semua">
                            <option value=""></option>
                            @foreach($action as $a)
                                <option value="{{ $a->NAME }}">{{ ucwords($a->NAME) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Tanggal :</label>
                        <input type="text" class="form-control" name="date" id="date" placeholder="Semua Tanggal" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">User :</label>
                        <select class="form-select select2-basic" name="action_by" id="action_by" data-placeholder="Semua">
                            @if(Main::isNotCenterBranch())
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
        </div>
        <div class="card-footer bg-white">
            <div class="text-end">
                <a href="{{ url('report/performance-user') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
                    <i class="ph-arrows-clockwise me-1"></i>
                    Reset Filter
                </a>
                <a href="javascript:void(0);" class="btn btn-success" onclick="loadData()">
                    <i class="ph-magnifying-glass me-1"></i>
                    Cari Data
                </a>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header d-flex align-items-center py-0">
            <h5 class="py-3 mb-0">Daftar</h5>
            <div class="ms-auto my-auto">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal-download-excel">
                    <i class="ph-microsoft-excel-logo me-1"></i>
                    Download
                </button>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover w-100 display" id="datatable-serverside">
                <thead class="text-bg-light">
                    <tr>
                        <th class="text-nowrap">No</th>
                        <th class="text-nowrap">Aksi</th>
                        <th class="text-nowrap">Judul</th>
                        <th class="text-nowrap">User</th>
                        <th class="text-nowrap">Tanggal</th>
                        <th class="text-nowrap">IP</th>
                        <th class="text-nowrap">Keterangan</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div id="modal-download-excel" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Download Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Aksi :</label>
                            <select class="form-select select2-basic" name="de_action" id="de_action" data-placeholder="Semua" data-dropdown-parent="#modal-download-excel">
                                <option value=""></option>
                                @foreach($action as $a)
                                    <option value="{{ $a->NAME }}">{{ ucwords($a->NAME) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Tanggal :</label>
                            <input type="text" class="form-control" name="de_date" id="de_date" placeholder="Semua Tanggal" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">User :</label>
                            <select class="form-select select2-basic" name="de_action_by" id="de_action_by" data-placeholder="Semua" data-dropdown-parent="#modal-download-excel">
                                @if(Main::isNotCenterBranch())
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="downloadExcel()">
                    <i class="ph-download me-1"></i>
                    Download
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
            swalInit.fire('Berhasil', notif, 'success');
        }
    }

    function downloadExcel() {
        var queryString = {
            exported: true,
            action: $('#de_action').val(),
            action_by: $('#de_action_by').val(),
            date: $('#de_date').val(),
        }

        onLoading('show', 'body');

        location.href = '{{ url("report/performance-user?") }}' + $.param(queryString);
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
                url: '{{ url("report/performance-user/datatable") }}',
                dataType: 'JSON',
                data: {
                    action: $('#action').val(),
                    action_by: $('#action_by').val(),
                    date: $('#date').val(),
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
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: false, className: 'align-middle' },
                { orderable: false, className: 'align-middle text-wrap' },
            ]
        }).on('draw.dt', function() {
            onLoading('close', '#datatable-serverside_wrapper');
        });

        window.gDataTable.columns.adjust().draw();
    }
</script>
