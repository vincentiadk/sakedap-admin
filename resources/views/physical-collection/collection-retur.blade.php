<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Koleksi Fisik - <span class="fw-normal">Koleksi Dikembalikan</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <span class="badge bg-warning p-2 bg-opacity-10 text-warning">
                    Koleksi Dikembalikan
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
                    <i class="ph-gift me-2 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Daftar Koleksi Yang Akan Dihibahkan</h6>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="ph-hand-pointing me-1"></i>
                        Aksi
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="javascript:void(0);" class="dropdown-item" onclick="grant()">
                            <i class="ph-gift me-2"></i>
                            Hibahkan
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0);" class="dropdown-item" onclick="taken(null, 1)">
                            <i class="ph-handshake me-2"></i>
                            Tandai Sudah Diambil
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item" onclick="taken(null, 0)">
                            <i class="ph-minus-circle me-2"></i>
                            Tandai Belum Diambil
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item text-danger" onclick="taken(null, -1)">
                            <i class="ph-x me-2"></i>
                            Tandai Batal Diambil
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered display nowrap w-100" id="datatable-action">
                    <thead class="table-light">
                        <tr>
                            <th class="text-nowrap" style="min-width: 250px">
                                <i class="ph-book me-1"></i>
                                Judul
                            </th>
                            <th class="text-nowrap" style="min-width: 180px">
                                <i class="ph-user-circle me-1"></i>
                                Pelaksana Serah
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 100px">
                                <i class="ph-stack me-1"></i>
                                Jumlah
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-receipt me-1"></i>
                                Resi
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
                                <i class="ph-user-circle me-1"></i>
                                Pelaksana Serah
                            </label>
                            <select class="form-select" name="executor_id" id="executor_id" data-placeholder="Semua Pelaksana"></select>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-calendar-blank me-1"></i>
                                Tanggal
                            </label>
                            <div class="input-group">
                                <select class="form-select w-auto flex-grow-0" name="date_type" id="date_type">
                                    <option value="accept_date">Tanggal Diterima</option>
                                    <option value="letter_date">Tanggal Pengiriman</option>
                                    <option value="createdate">Tanggal Dibuat</option>
                                </select>
                                <input type="text" class="form-control" name="date" id="date" placeholder="Pilih tanggal" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-truck me-1"></i>
                                Jasa Kirim
                            </label>
                            <select class="form-select select2-basic" name="delivery_service_id" id="delivery_service_id" data-placeholder="Semua Jasa Kirim">
                                <option value=""></option>
                                @foreach($deliveryService as $ds)
                                    <option value="{{ $ds->ID }}">{{ $ds->NAME }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer border-top">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ url('physical-collection/collection-retur') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
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
                    <h6 class="mb-0 fw-semibold">Daftar Koleksi Dikembalikan</h6>
                </div>
                <button type="button" class="btn btn-success btn-sm" onclick="addListAction()">
                    <i class="ph-list-plus me-1"></i>
                    Tambahkan ke Daftar Atas
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
                            <th class="text-center text-nowrap" style="width: 100px">
                                <i class="ph-gear"></i>
                                Aksi
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 100px">
                                <i class="ph-robot me-1"></i>
                                Auto Hibah
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 120px">
                                <i class="ph-flag me-1"></i>
                                Status
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-calendar me-1"></i>
                                Rencana Ambil
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-phone me-1"></i>
                                Kontak
                            </th>
                            <th class="text-nowrap" style="min-width: 180px">
                                <i class="ph-user me-1"></i>
                                Nama Pengambil
                            </th>
                            <th class="text-nowrap" style="min-width: 250px">
                                <i class="ph-book me-1"></i>
                                Judul
                            </th>
                            <th class="text-nowrap" style="min-width: 180px">
                                <i class="ph-user-circle me-1"></i>
                                Pelaksana Serah
                            </th>
                            <th class="text-nowrap" style="min-width: 200px">
                                <i class="ph-map-pin me-1"></i>
                                Tujuan
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-truck me-1"></i>
                                Jasa Kirim
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-receipt me-1"></i>
                                Resi
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 100px">
                                <i class="ph-stack me-1"></i>
                                Jumlah
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-file me-1"></i>
                                Jenis Media
                            </th>
                            <th class="text-nowrap" style="min-width: 200px">
                                <i class="ph-warning-circle me-1"></i>
                                Alasan Ditolak
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

        if(parseInt('{{ Main::isSuperAdmin() }}') == 0) {
            select2Serverside('#executor_id', 'executor', {
                province_id: '{{ session("province_id") }}',
            });
        } else {
            select2Serverside('#executor_id', 'executor');
        }

        loadDataAction();
        loadData();
    });

    function onReloadTable() {
        window.gDataTable.ajax.reload(null, false);
    }

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
                selector: 'td.allow-select'
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
                url: '{{ url("physical-collection/collection-retur/datatable") }}',
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
                { orderable: false, className: 'align-middle text-center allow-select' },
                { orderable: true, className: 'align-middle text-center fw-semibold allow-select' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle text-center allow-select' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-wrap allow-select' },
                { orderable: true, className: 'align-middle allow-select' },
                { orderable: true, className: 'align-middle text-wrap allow-select' },
                { orderable: true, className: 'align-middle text-wrap allow-select' },
                { orderable: true, className: 'align-middle text-wrap allow-select' },
                { orderable: true, className: 'align-middle text-wrap allow-select' },
                { orderable: true, className: 'align-middle allow-select' },
                { orderable: true, className: 'align-middle allow-select' },
                { orderable: true, className: 'align-middle text-center allow-select' },
                { orderable: true, className: 'align-middle text-wrap allow-select' },
                { orderable: true, className: 'align-middle text-wrap' },
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

    function loadDataAction() {
        $('#datatable-action').DataTable({
            deferRender: true,
            scrollX: true,
            destroy: true,
            columns: [
                { className: 'align-middle text-wrap' },
                { className: 'align-middle text-wrap' },
                { className: 'align-middle text-center' },
                { className: 'align-middle' },
                { className: 'align-middle text-center' },
            ]
        });

        $('#datatable-action').DataTable().clear().draw();

        var localStorageData = localStorage.getItem('datatable-action-retur');
        var data = localStorageData ? JSON.parse(localStorageData) : [];

        $.each(data, function(i, val) {
            var btnRemove = `
                <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeListAction(${ val[0] })">
                    <i class="ph-trash"></i>
                </button>
            `;

            $('#datatable-action').DataTable().row.add([
                val[1],
                val[2],
                val[3],
                val[4],
                btnRemove
            ]).draw().node();
        });
    }

    function addListAction() {
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
            var executor = data.data('executor');
            var qty = data.data('qty-retur');
            var receipt = data.data('receipt');

            var dataStorage = localStorage.getItem('datatable-action-retur');
            var currentDataStorage = dataStorage ? JSON.parse(dataStorage) : [];
            var isDuplicate = false;

            var payload = [
                id,
                title,
                executor,
                qty,
                receipt,
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

            localStorage.setItem('datatable-action-retur', JSON.stringify(currentDataStorage));
        });

        $('.buttons-select-none').click();

        loadDataAction();

        swalInit.fire({
            title: 'Berhasil',
            text: 'Data telah ditambahkan dalam daftar',
            icon: 'success',
            timer: 2000,
            timerProgressBar: true
        });
    }

    function removeListAction(id) {
        var dataStorage = localStorage.getItem('datatable-action-retur');
        var currentDataStorage = dataStorage ? JSON.parse(dataStorage) : [];

        var updatedDataStorage = currentDataStorage.filter(function(item) {
            return item[0] !== id;
        });

        localStorage.setItem('datatable-action-retur', JSON.stringify(updatedDataStorage));

        loadDataAction();

        notification('success', 'Data berhasil dihapus dari daftar');
    }

    function grant(param = null) {
        if(param) {
            var id = [param];
        } else {
            var id = [];
            var dataStorage = localStorage.getItem('datatable-action-retur');
            var responseDataStorage = dataStorage ? JSON.parse(dataStorage) : [];

            if (responseDataStorage.length === 0) {
                swalInit.fire({
                    title: 'Peringatan',
                    text: 'Daftar koleksi yang akan dihibahkan masih kosong',
                    icon: 'warning'
                });
                return;
            }

            $.each(responseDataStorage, function(i, val) {
                id.push(val[0]);
            });
        }

        var notyConfirm = new Noty({
            text: '<div class="mb-3"><h5 class="text-dark">Koleksi Dihibahkan?</h5><span class="text-muted">Anda yakin ingin menghibahkan koleksi dalam daftar ini?</span></div>',
            timeout: false,
            modal: true,
            layout: 'center',
            closeWith: 'button',
            type: 'confirm',
            buttons: [
                Noty.button('Tidak', 'btn btn-light', function () {
                    notyConfirm.close();
                }),
                Noty.button('Ya, Hibahkan', 'btn btn-success ms-2', function () {
                    $.ajax({
                        url: '{{ url("physical-collection/collection-retur/grant") }}',
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            id: id
                        },
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        beforeSend: function() {
                            onLoading('show', '.noty_bar');
                        },
                        success: function(response) {
                            onLoading('close', '.noty_bar');

                            if(response.code == 200) {
                                notyConfirm.close();
                                onReloadTable();
                                localStorage.removeItem('datatable-action-retur');
                                notification('success', response.message);

                                $('#datatable-action').DataTable().clear().draw();
                            } else {
                                swalInit.fire({
                                    title: 'Error',
                                    text: response.message,
                                    icon: 'error',
                                    showCloseButton: false
                                });
                            }
                        },
                        error: function(response) {
                            onLoading('close', '.noty_bar');
                            responseError(response);
                        }
                    });
                })
            ]
        }).show();
    }

    function taken(param = null, value = null) {
        if(param) {
            var id = [param];
        } else {
            var id = [];
            var dataStorage = localStorage.getItem('datatable-action-retur');
            var responseDataStorage = dataStorage ? JSON.parse(dataStorage) : [];

            if (responseDataStorage.length === 0) {
                swalInit.fire({
                    title: 'Peringatan',
                    text: 'Daftar koleksi yang akan ditandai masih kosong',
                    icon: 'warning'
                });
                return;
            }

            $.each(responseDataStorage, function(i, val) {
                id.push(val[0]);
            });
        }

        var statusText = '';
        if (value === 1) {
            statusText = 'sudah diambil';
        } else if (value === 0) {
            statusText = 'belum diambil';
        } else if (value === -1) {
            statusText = 'batal diambil';
        }

        var notyConfirm = new Noty({
            text: '<div class="mb-3"><h5 class="text-dark">Pengambilan Koleksi?</h5><span class="text-muted">Anda yakin ingin menandai koleksi dalam daftar ini sebagai <strong>' + statusText + '</strong>?</span></div>',
            timeout: false,
            modal: true,
            layout: 'center',
            closeWith: 'button',
            type: 'confirm',
            buttons: [
                Noty.button('Tidak', 'btn btn-light', function () {
                    notyConfirm.close();
                }),
                Noty.button('Ya, Tandai', 'btn btn-primary ms-2', function () {
                    $.ajax({
                        url: '{{ url("physical-collection/collection-retur/taken") }}',
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            id: id,
                            value: value
                        },
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        beforeSend: function() {
                            onLoading('show', '.noty_bar');
                        },
                        success: function(response) {
                            onLoading('close', '.noty_bar');

                            if(response.code == 200) {
                                notyConfirm.close();
                                onReloadTable();
                                localStorage.removeItem('datatable-action-retur');
                                notification('success', response.message);

                                $('#datatable-action').DataTable().clear().draw();
                            } else {
                                swalInit.fire({
                                    title: 'Error',
                                    text: response.message,
                                    icon: 'error',
                                    showCloseButton: false
                                });
                            }
                        },
                        error: function(response) {
                            onLoading('close', '.noty_bar');
                            responseError(response);
                        }
                    });
                })
            ]
        }).show();
    }
</script>
