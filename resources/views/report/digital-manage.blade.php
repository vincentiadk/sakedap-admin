<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Laporan <span class="fw-normal">Pengelolaan Digital</span>
            </h4>
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
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Tabel :</label>
                        <select class="form-select select2-basic" name="table_name" id="table_name" data-placeholder="Semua">
                            <option value=""></option>
                            @foreach($tableName as $t)
                                <option value="{{ $t->NAME }}">{{ $t->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Aksi :</label>
                        <select class="form-select select2-basic" name="action" id="action" data-placeholder="Semua">
                            <option value=""></option>
                            @foreach($action as $a)
                                <option value="{{ $a->NAME }}">{{ $a->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Tanggal :</label>
                        <input type="text" class="form-control" name="date" id="date" placeholder="Semua Tanggal" readonly>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">User :</label>
                        <select class="form-select select2-basic" name="action_by" id="action_by" data-placeholder="Semua">
                            @if(Main::isNotSuperAdmin())
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
                <a href="{{ url('report/digital-manage') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
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
        <div class="card-body">
            <table class="table table-bordered table-hover w-100 display" id="datatable-serverside">
                <thead class="text-bg-light">
                    <tr>
                        <th class="text-nowrap">No</th>
                        <th class="text-nowrap">Aksi</th>
                        <th class="text-nowrap">Tabel</th>
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

<script>
    $(function() {
        datePickerBasic('#date');

        datePickerBasic('#de_date', {
            dateLimit: {
                months: 1
            }
        });

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
                url: '{{ url("report/digital-manage/datatable") }}',
                dataType: 'JSON',
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
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
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
            },
        }).on('draw.dt', function() {
            onLoading('close', '#datatable-serverside_wrapper');
        });

        window.gDataTable.columns.adjust().draw();
    }
</script>
