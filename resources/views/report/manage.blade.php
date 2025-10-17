<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Laporan - <span class="fw-normal">Pengelolaan</span>
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
                        <label class="form-label">Tanggal :</label>
                        <input type="text" class="form-control" name="date" id="date" placeholder="Semua Tanggal" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Pelaksana Serah :</label>
                        <select class="form-select" name="executor_id" id="executor_id" data-placeholder="Semua"></select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Jenis Bahan :</label>
                        <select class="form-select select2-basic" name="worksheet_id" id="worksheet_id" data-placeholder="Semua">
                            <option value=""></option>
                            @foreach($worksheet as $w)
                                <option value="{{ $w->ID }}">{{ $w->NAME }} [{{ $w->CATEGORY }}]</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Judul :</label>
                        <input type="text" class="form-control" name="title" id="title" placeholder="Semua">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Provinsi :</label>
                        <select class="form-select" name="province_id" id="province_id" data-placeholder="Semua">
                            @if(Main::isNotSuperAdmin())
                                <option value="{{ session('province_id') }}" selected>{{ session('province_name') }}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Tahun :</label>
                        <input type="number" class="form-control" name="year" id="year" placeholder="Semua">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="text-end">
                <a href="{{ url('report/manage') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
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
            {{-- <div class="ms-auto my-auto">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal-download-excel">
                    <i class="ph-microsoft-excel-logo me-1"></i>
                    Download
                </button>
            </div> --}}
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover w-100 display" id="datatable-serverside">
                <thead class="text-bg-light">
                    <tr>
                        <th class="text-nowrap">No</th>
                        <th class="text-nowrap"><i class="ph-gear"></i></th>
                        <th class="text-nowrap">Pelaksana Serah</th>
                        <th class="text-nowrap">Provinsi</th>
                        <th class="text-nowrap">Kota</th>
                        <th class="text-nowrap">Judul</th>
                        <th class="text-nowrap">Jenis Bahan</th>
                        <th class="text-nowrap">Album</th>
                        <th class="text-nowrap">Seri</th>
                        <th class="text-nowrap">Edisi</th>
                        <th class="text-nowrap">Volume</th>
                        <th class="text-nowrap">Kode</th>
                        <th class="text-nowrap">Tahun</th>
                        <th class="text-nowrap">Preview</th>
                        <th class="text-nowrap">Tgl Terima</th>
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
                            <label class="form-label">Tanggal :</label>
                            <input type="text" class="form-control" name="de_date" id="de_date" placeholder="Semua Tanggal" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Pelaksana Serah :</label>
                            <select class="form-select" name="de_executor_id" id="de_executor_id" data-placeholder="Semua" data-dropdown-parent="#modal-download-excel"></select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Jenis Bahan :</label>
                            <select class="form-select select2-basic" name="de_worksheet_id" id="de_worksheet_id" data-placeholder="Semua" data-dropdown-parent="#modal-download-excel">
                                <option value=""></option>
                                @foreach($worksheet as $w)
                                    <option value="{{ $w->ID }}">{{ $w->NAME }} [{{ $w->CATEGORY }}]</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Judul :</label>
                            <input type="text" class="form-control" name="de_title" id="de_title" placeholder="Semua">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Provinsi :</label>
                            <select class="form-select" name="de_province_id" id="de_province_id" data-placeholder="Semua" data-dropdown-parent="#modal-download-excel">
                                @if(Main::isNotSuperAdmin())
                                    <option value="{{ session('province_id') }}" selected>{{ session('province_name') }}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Tahun :</label>
                            <input type="number" class="form-control" name="de_year" id="de_year" placeholder="Semua">
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

        if(parseInt('{{ Main::isNotSuperAdmin() }}') === 1) {
            select2Serverside('#province_id, #de_province_id', 'location', {
                for: 'province',
                province_id: '{{ session("province_id") }}',
            }, {
                minimumInputLength: 0
            });

            select2Serverside('#executor_id, #de_executor_id', 'executor', {
                province_id: '{{ session("province_id") }}',
            });
        } else {
            select2Serverside('#executor_id, #de_executor_id', 'executor');
            select2Serverside('#province_id, #de_province_id', 'location');
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
            title: $('#de_title').val(),
            executor_id: $('#de_executor_id').val(),
            province_id: $('#de_province_id').val(),
            year: $('#de_year').val(),
            worksheet_id: $('#de_worksheet_id').val(),
            date: $('#de_date').val(),
        }

        onLoading('show', 'body');

        location.href = '{{ url("report/manage?") }}' + $.param(queryString);
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
                url: '{{ url("report/manage/datatable") }}',
                dataType: 'JSON',
                data: {
                    title: $('#title').val(),
                    executor_id: $('#executor_id').val(),
                    province_id: $('#province_id').val(),
                    year: $('#year').val(),
                    worksheet_id: $('#worksheet_id').val(),
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
                { orderable: false, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
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
