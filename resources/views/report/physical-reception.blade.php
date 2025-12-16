<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Koleksi Fisik - <span class="fw-normal">Koleksi Diterima</span>
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
                        <label class="form-label">Pelaksana Serah :</label>
                        <select class="form-select" name="executor_id" id="executor_id" data-placeholder="Semua"></select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Tanggal :</label>
                        <div class="input-group">
                            <select class="form-select w-auto flex-grow-0" name="date_type" id="date_type">
                                <option value="accept_date">Diterima</option>
                                <option value="letter_date">Pengiriman</option>
                                <option value="createdate">Dibuat</option>
                            </select>
                            <input type="text" class="form-control" name="date" id="date" placeholder="Semua Tanggal" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Jasa Kirim :</label>
                        <select class="form-select select2-basic" name="delivery_service_id" id="delivery_service_id" data-placeholder="Semua">
                            <option value=""></option>
                            @foreach($deliveryService as $ds)
                                <option value="{{ $ds->ID }}">{{ $ds->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Status :</label>
                        <select class="form-select" name="status" id="status">
                            <option value="">Semua</option>
                            <option value="DITERIMA PARSIAL">DITERIMA PARSIAL</option>
                            <option value="DITERIMA PENUH">DITERIMA PENUH</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="text-end">
                <a href="{{ url('report/physical-reception') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
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
                        <th class="text-nowrap">Judul</th>
                        <th class="text-nowrap">Pelaksana Serah</th>
                        <th class="text-nowrap">Tujuan</th>
                        <th class="text-nowrap">Jasa Kirim</th>
                        <th class="text-nowrap">Resi</th>
                        <th class="text-nowrap">Jumlah Eks</th>
                        <th class="text-nowrap">Jumlah Diterim</th>
                        <th class="text-nowrap">Jumlah Ditolak</th>
                        <th class="text-nowrap">Jumlah Hibah</th>
                        <th class="text-nowrap">Jumlah Dikembalikan</th>
                        <th class="text-nowrap">Jumlah Verif</th>
                        <th class="text-nowrap">Jenis Media</th>
                        <th class="text-nowrap">Status</th>
                        <th class="text-nowrap">Harga</th>
                        <th class="text-nowrap">ISBN</th>
                        <th class="text-nowrap">Tahun Terbit</th>
                        <th class="text-nowrap">ISBN Status</th>
                        <th class="text-nowrap">Edisi Serial</th>
                        <th class="text-nowrap">TTES Awal</th>
                        <th class="text-nowrap">TTES Akhir</th>
                        <th class="text-nowrap">Kala Terbit</th>
                        <th class="text-nowrap">Katalog ID</th>
                        <th class="text-nowrap">Jilid</th>
                        <th class="text-nowrap">QRCBN</th>
                        <th class="text-nowrap">ISBD</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div id="modal-detail" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Data</h5>
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">
                    <i class="ph-x"></i>
                </button>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        datePickerBasic('#date');

        if(parseInt('{{ Main::isNotSuperAdmin() }}') === 1) {
            select2Serverside('#executor_id', 'executor', {
                province_id: '{{ session("province_id") }}',
            });
        } else {
            select2Serverside('#executor_id', 'executor');
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
                url: '{{ url("report/physical-reception/datatable") }}',
                dataType: 'JSON',
                data: {
                    executor_id: $('#executor_id').val(),
                    delivery_service_id: $('#delivery_service_id').val(),
                    date: $('#date').val(),
                    date_type: $('#date_type').val(),
                    status: $('#status').val(),
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
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
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
