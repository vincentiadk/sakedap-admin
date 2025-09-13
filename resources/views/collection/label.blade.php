<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Koleksi - <span class="fw-normal">Label</span>
            </h4>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="card">
        <div class="card-header d-flex align-items-center py-0">
            <h5 class="py-3 mb-0">Daftar Koleksi Yang Akan di Cetak</h5>
            <div class="ms-auto my-auto">
                <div class="btn-group">
                    <button type="button" class="btn btn-teal dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="ph-printer me-1"></i>
                        Cetak Data
                    </button>
                    <div class="dropdown-menu">
                        <a href="javascript:void(0);" class="dropdown-item" onclick="printDataList('barcode')">
                            <i class="ph-barcode me-1"></i>
                            Barcode
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item" onclick="printDataList('qrcode')">
                            <i class="ph-qr-code me-1"></i>
                            QR Code
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover w-100 display" id="datatable-print">
                <thead class="text-bg-light">
                    <tr>
                        <th nowrap>Judul</th>
                        <th nowrap>Kode</th>
                        <th nowrap>Mark Nasional</th>
                        <th nowrap>Mark Provinsi</th>
                        <th nowrap>Hapus</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
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
                        <label class="form-label">Pengelola :</label>
                        <select class="form-select" name="publisher_id" id="publisher_id" data-placeholder="Semua"></select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Jenis :</label>
                        <select class="form-select select2-basic" name="worksheet_id" id="worksheet_id" data-placeholder="Semua">
                            <option value=""></option>
                            @foreach($worksheet as $w)
                                <option value="{{ $w->ID }}">{{ $w->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Judul :</label>
                        <input type="text" class="form-control" name="title" id="title" placeholder="....................">
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
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Tahun :</label>
                        <input type="number" class="form-control" name="year" id="year" placeholder="....................">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="text-end">
                <a href="{{ url('collection/label') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
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
            <h5 class="py-3 mb-0">Daftar Koleksi Yang Akan di Cetak</h5>
            <div class="ms-auto my-auto">
                <button type="button" class="btn btn-teal" onclick="addListPrint()">
                    <i class="ph-list-plus me-1"></i>
                    Tambahkan ke Daftar Cetak
                </button>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover w-100 display" id="datatable-serverside">
                <thead class="text-bg-light">
                    <tr>
                        <th nowrap>#</th>
                        <th nowrap>No</th>
                        <th nowrap>Jenis</th>
                        <th nowrap>Kode</th>
                        <th nowrap>Mark Nasional</th>
                        <th nowrap>Mark Provinsi</th>
                        <th nowrap>Pengelola</th>
                        <th nowrap>Judul</th>
                        <th nowrap>ISBN</th>
                        <th nowrap>Perpustakaan</th>
                        <th nowrap>Lokasi</th>
                        <th nowrap>Update Oleh</th>
                        <th nowrap>Terima Oleh</th>
                        <th nowrap>Tgl Terima</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    $(function() {
        datePickerBasic('#date');

        if(parseInt('{{ Main::isNotCenterBranch() }}') === 1) {
            select2Serverside('#province_id', 'location', {
                for: 'province',
                province_id: '{{ session("province_id") }}',
            }, {
                minimumInputLength: 0
            });

            select2Serverside('#publisher_id', 'publisher', {
                province_id: '{{ session("province_id") }}',
            });
        } else {
            select2Serverside('#publisher_id', 'publisher');
            select2Serverside('#province_id', 'location');
        }

        loadDataPrint();
        loadData();
    });

    function loadData() {
        window.gDataTable = $('#datatable-serverside').DataTable({
            dom: '<"dt-buttons-full"B><"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
            processing: true,
            serverSide: true,
            deferRender: true,
            scrollX: true,
            destroy: true,
            order: [[1, 'desc']],
            columnDefs: [
                {
                    orderable: false,
                    className: 'select-checkbox',
                    targets: 0
                },
            ],
            select: {
                style: 'multi',
                selector: 'td'
            },
            buttons: [
                { extend: 'selectAll', className: 'btn btn-teal', text: 'Centang Semua' },
                { extend: 'selectNone', className: 'btn btn-warning', text: 'Hilangkan Semua Centang' },
            ],
            ajax: {
                url: '{{ url("collection/label/datatable") }}',
                dataType: 'JSON',
                data: {
                    title: $('#title').val(),
                    publisher_id: $('#publisher_id').val(),
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

                    swalInit.fire({
                        html: '<b>' + response.responseJSON.exception + '</b><br>' + response.responseJSON.message,
                        icon: 'error',
                        showCloseButton: false
                    });
                }
            },
            columns: [
                { orderable: false, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
            ]
        }).on('draw.dt', function() {
            onLoading('close', '#datatable-serverside_wrapper');
        });

        window.gDataTable.columns.adjust().draw();
    }

    function loadDataPrint() {
        $('#datatable-print').DataTable({
            deferRender: true,
            scrollX: true,
            destroy: true,
            columns: [
                { className: 'align-middle text-wrap' },
                { className: 'align-middle' },
                { className: 'align-middle' },
                { className: 'align-middle' },
                { className: 'align-middle text-center' },
            ]
        });

        $('#datatable-print').DataTable().clear().draw();

        var localStorageData = localStorage.getItem('datatable-print');
        var data = localStorageData ? JSON.parse(localStorageData) : [];

        $.each(data, function(i, val) {
            var btnRemove = `
                <button type="button" class="btn btn-danger btn-sm col-12" onclick="removeListPrint(${ val[0] })">
                    <i class="ph-trash"></i>
                </button>
            `;

            $('#datatable-print').DataTable().row.add([
                val[1],
                val[2],
                val[3],
                val[4],
                btnRemove
            ]).draw().node();
        });
    }

    function addListPrint() {
        window.gDataTable.rows({ selected: true }).every(function() {
            var row = this.node();
            var data = $(row).find('input[name="data"]');
            var id = data.data('id');
            var title = data.data('title');
            var code = data.data('code');
            var markNational = data.data('mark-national');
            var markProvince = data.data('mark-province');

            var dataStorage = localStorage.getItem('datatable-print');
            var currentDataStorage = dataStorage ? JSON.parse(dataStorage) : [];
            var isDuplicate = false;

            var payload = [
                id,
                title,
                code,
                markNational,
                markProvince,
            ];

            for (var i = 0; i < currentDataStorage.length; i++) {
                if (currentDataStorage[i][0] === id) {
                    isDuplicate = true;
                    break;
                }
            }

            if (!isDuplicate) {
                currentDataStorage.push(payload);
            }

            localStorage.setItem('datatable-print', JSON.stringify(currentDataStorage));
        });

        $('.buttons-select-none').click();

        loadDataPrint();

        swalInit.fire({
            title: 'Berhasil',
            text: 'Data telah ditambahkan dalam list',
            icon: 'success'
        });
    }

    function removeListPrint(id) {
        var dataStorage = localStorage.getItem('datatable-print');
        var currentDataStorage = dataStorage ? JSON.parse(dataStorage) : [];

        var updatedDataStorage = currentDataStorage.filter(function(item) {
            return item[0] !== id;
        });

        localStorage.setItem('datatable-print', JSON.stringify(updatedDataStorage));

        loadDataPrint();
    }

    function printDataList(param) {
        var dataId = [];
        var dataStorage = localStorage.getItem('datatable-print');
        var responseDataStorage = dataStorage ? JSON.parse(dataStorage) : [];

        if(dataStorage) {
            $.each(responseDataStorage, function(i, val) {
                dataId.push(val[0]);
            });

            var queryString = {
                id: dataId
            }

            window.open('{{ url("collection/label/print") }}/' + param + '?' + $.param(queryString), '_blank');
        } else {
            swalInit.fire({
                title: 'Oops ...',
                text: 'Tidak ada data di tabel untuk di cetak',
                icon: 'info'
            });
        }

    }
</script>
