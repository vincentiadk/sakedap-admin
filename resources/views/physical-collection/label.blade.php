<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Koleksi Fisik - <span class="fw-normal">Label</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <span class="badge bg-info p-2 bg-opacity-10 text-info">
                    Cetak Label Koleksi
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
                    <i class="ph-printer me-2 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Daftar Koleksi Yang Akan di Cetak</h6>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="ph-printer me-1"></i>
                        Cetak Data
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="javascript:void(0);" class="dropdown-item" onclick="printDataList('barcode')">
                            <i class="ph-barcode me-2"></i>
                            Barcode
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item" onclick="printDataList('qrcode')">
                            <i class="ph-qr-code me-2"></i>
                            QR Code
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered display nowrap w-100" id="datatable-print">
                    <thead class="table-light">
                        <tr>
                            <th class="text-nowrap" style="min-width: 250px">
                                <i class="ph-book me-1"></i>
                                Judul
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-fingerprint me-1"></i>
                                Kode
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-flag me-1"></i>
                                Mark Nasional
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-map-pin me-1"></i>
                                Mark Provinsi
                            </th>
                            <th class="text-center text-nowrap" style="width: 80px">
                                <i class="ph-trash me-1"></i>
                                Hapus
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
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
        <div class="collapse show" id="filterCollapse">
            <div class="card-body">
                <form id="form-filter">
                    <div class="row g-3">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-calendar-blank me-1"></i>
                                Tanggal
                            </label>
                            <input type="text" class="form-control" name="date" id="date" placeholder="Pilih tanggal">
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-user-circle me-1"></i>
                                Pelaksana Serah
                            </label>
                            <select class="form-select" name="executor_id" id="executor_id" data-placeholder="Semua Pelaksana"></select>
                        </div>
                        <div class="col-lg-4 col-md-6">
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
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-book-open me-1"></i>
                                Judul
                            </label>
                            <input type="text" class="form-control" name="title" id="title" placeholder="Cari berdasarkan judul">
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-map-trifold me-1"></i>
                                Provinsi
                            </label>
                            <select class="form-select" name="province_id" id="province_id">
                                @if(!Main::isPerpusnas())
                                    <option value="{{ session('province_id') }}" selected>{{ session('province_name') }}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-calendar-check me-1"></i>
                                Tahun
                            </label>
                            <input type="number" class="form-control" name="year" id="year" placeholder="Contoh: 2024">
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer border-top">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ url('physical-collection/label') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
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
                    <h6 class="mb-0 fw-semibold">Daftar Koleksi</h6>
                </div>
                <button type="button" class="btn btn-success btn-sm" onclick="addListPrint()">
                    <i class="ph-list-plus me-1"></i>
                    Tambahkan ke Daftar Cetak
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered display nowrap w-100" id="datatable-serverside">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center text-nowrap" style="width: 40px">
                                <i class="ph-check-square"></i>
                            </th>
                            <th class="text-center text-nowrap" style="width: 60px">
                                <i class="ph-hash"></i>
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-files me-1"></i>
                                Jenis Bahan
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-fingerprint me-1"></i>
                                Kode
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-flag me-1"></i>
                                Mark Nasional
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-map-pin me-1"></i>
                                Mark Provinsi
                            </th>
                            <th class="text-nowrap" style="min-width: 180px">
                                <i class="ph-user-circle me-1"></i>
                                Pelaksana Serah
                            </th>
                            <th class="text-nowrap" style="min-width: 250px">
                                <i class="ph-book me-1"></i>
                                Judul
                            </th>
                            <th class="text-nowrap" style="min-width: 130px">
                                <i class="ph-barcode me-1"></i>
                                ISBN
                            </th>
                            <th class="text-nowrap" style="min-width: 180px">
                                <i class="ph-buildings me-1"></i>
                                Lokasi
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-user-gear me-1"></i>
                                Update Oleh
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-check me-1"></i>
                                Terima Oleh
                            </th>
                            <th class="text-nowrap" style="min-width: 130px">
                                <i class="ph-calendar-check me-1"></i>
                                Tgl Terima
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

        if(parseInt('{{ Main::isPerpusnas() }}') == 0) {
            select2Serverside('#province_id', 'location', {
                for: 'province',
                province_id: '{{ session("province_id") }}',
            }, {
                minimumInputLength: 0
            });

            select2Serverside('#executor_id', 'executor', {
                province_id: '{{ session("province_id") }}',
            });
        } else {
            select2Serverside('#executor_id', 'executor');

            select2Serverside('#province_id', 'location', {
                for: 'province'
            }, {
                minimumInputLength: 0
            });
        }

        loadDataPrint();
        loadData();
    });

    function loadData() {
        window.gDataTable = $('#datatable-serverside').DataTable({
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
                {
                    extend: 'collection',
                    text: '<i class="ph-microsoft-excel-logo me-1"></i> Download Excel',
                    className: 'btn btn-success',
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: 'Semua Data Keseluruhan',
                            exportOptions: {
                                modifier: {
                                    page: 'all',
                                    search: 'none',
                                }
                            }
                        },
                        {
                            extend: 'excelHtml5',
                            text: 'Semua Data Dengan Pencarian',
                            exportOptions: {
                                modifier: {
                                    page: 'all',
                                    search: 'applied',
                                }
                            }
                        },
                        {
                            extend: 'excelHtml5',
                            text: 'Halaman Ini Saja',
                            exportOptions: {
                                modifier: {
                                    page: 'current',
                                }
                            }
                        },
                    ]
                },
                {
                    extend: 'selectAll',
                    className: 'btn btn-primary',
                    text: '<i class="ph-checks me-1"></i> Centang Semua'
                },
                {
                    extend: 'selectNone',
                    className: 'btn btn-warning',
                    text: '<i class="ph-x me-1"></i> Hilangkan Semua Centang'
                },
            ],
            ajax: {
                url: '{{ url("physical-collection/label/datatable") }}',
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
                { orderable: false, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-center fw-semibold' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-center' },
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
                <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeListPrint(${ val[0] })">
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
        var selectedRows = window.gDataTable.rows({ selected: true }).count();

        if (selectedRows === 0) {
            swalInit.fire({
                title: 'Peringatan',
                text: 'Silakan pilih data terlebih dahulu',
                icon: 'warning'
            });
            return;
        }

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
            text: 'Data telah ditambahkan dalam daftar cetak',
            icon: 'success',
            timer: 2000,
            timerProgressBar: true
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

        notification('success', 'Data berhasil dihapus dari daftar cetak');
    }

    function printDataList(param) {
        var dataId = [];
        var dataStorage = localStorage.getItem('datatable-print');
        var responseDataStorage = dataStorage ? JSON.parse(dataStorage) : [];

        if (responseDataStorage.length === 0) {
            swalInit.fire({
                title: 'Peringatan',
                text: 'Daftar cetak masih kosong. Silakan tambahkan data terlebih dahulu',
                icon: 'warning'
            });
            return;
        }

        $.each(responseDataStorage, function(i, val) {
            dataId.push(val[0]);
        });

        var queryString = {
            id: dataId
        }

        window.open('{{ url("physical-collection/label/print") }}/' + param + '?' + $.param(queryString), '_blank');
    }
</script>
