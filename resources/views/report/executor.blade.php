<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Laporan - <span class="fw-normal">Pelaksana Serah</span>
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
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Jenis :</label>
                        <select class="form-select select2-basic" name="type_id" id="type_id" data-placeholder="Semua">
                            <option value=""></option>
                            @foreach($type as $t)
                                <option value="{{ $t->ID }}">{{ $t->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Kategori :</label>
                        <select class="form-select select2-basic" name="category_id" id="category_id" data-placeholder="Semua">
                            <option value=""></option>
                            @foreach($category as $c)
                                <option value="{{ $c->ID }}">{{ $c->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Provinsi :</label>
                        <select class="form-select" name="province_id" id="province_id">
                            @if(Main::isNotCenterBranch())
                                <option value="{{ session('province_id') }}" selected>{{ session('province_name') }}</option>
                            @endif
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="text-end">
                <a href="{{ url('report/executor') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
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
                <button type="button" class="btn btn-success" onclick="downloadExcel()">
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
                        <th class="text-nowrap">Pelaksana Serah</th>
                        <th class="text-nowrap">Jenis</th>
                        <th class="text-nowrap">Kategori</th>
                        <th class="text-nowrap">Provinsi</th>
                        <th class="text-nowrap">KRD</th>
                        <th class="text-nowrap">KRA</th>
                        <th class="text-nowrap">KC</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    $(function() {
        if(parseInt('{{ Main::isNotCenterBranch() }}') === 1) {
            select2Serverside('#province_id', 'location', {
                for: 'province',
                province_id: '{{ session("province_id") }}',
            }, {
                minimumInputLength: 0
            });
        } else {
            select2Serverside('#province_id', 'location');
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
            type_id: $('#type_id').val(),
            category_id: $('#category_id').val(),
            province_id: $('#province_id').val()
        }

        onLoading('show', 'body');

        location.href = '{{ url("report/executor?") }}' + $.param(queryString);
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
                url: '{{ url("report/executor/datatable") }}',
                dataType: 'JSON',
                data: {
                    type_id: $('#type_id').val(),
                    category_id: $('#category_id').val(),
                    province_id: $('#province_id').val(),
                },
                beforeSend: function() {
                    onLoading('show', '.dataTables_wrapper');
                },
                error: function(response) {
                    onLoading('close', '.dataTables_wrapper');

                    swalInit.fire({
                        html: '<b>' + response.responseJSON.exception + '</b><br>' + response.responseJSON.message,
                        icon: 'error',
                        showCloseButton: false
                    });
                }
            },
            columns: [
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle text-center' },
            ]
        }).on('draw.dt', function() {
            onLoading('close', '.dataTables_wrapper');
        });

        window.gDataTable.columns.adjust().draw();
    }
</script>
