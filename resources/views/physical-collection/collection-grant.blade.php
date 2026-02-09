<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Koleksi Fisik - <span class="fw-normal">Koleksi Dihibahkan</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <span class="badge bg-success p-2 bg-opacity-10 text-success">
                    <i class="ph-gift me-1"></i>
                    Manajemen Hibah
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
                    <i class="ph-list-checks me-2 text-success"></i>
                    <h6 class="mb-0 fw-semibold">Daftar Koleksi Yang Akan Masuk Grup</h6>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="ph-hand-pointing me-1"></i>
                        Aksi
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="javascript:void(0);" class="dropdown-item" onclick="createGroup()">
                            <i class="ph-arrows-in-line-horizontal me-2"></i>
                            Buat Grup
                        </a>
                        {{-- <a href="javascript:void(0);" class="dropdown-item" onclick="outGroup()">
                            <i class="ph-arrows-out-line-horizontal me-2"></i>
                            Keluar Grup
                        </a> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered display nowrap w-100" id="datatable-action">
                    <thead class="table-light">
                        <tr>
                            <th class="text-nowrap">
                                <i class="ph-book-open me-1"></i>
                                Judul
                            </th>
                            <th class="text-nowrap">
                                <i class="ph-user-circle me-1"></i>
                                Pelaksana Serah
                            </th>
                            <th class="text-nowrap text-center">
                                <i class="ph-stack me-1"></i>
                                Jumlah
                            </th>
                            <th class="text-nowrap">
                                <i class="ph-barcode me-1"></i>
                                Resi
                            </th>
                            <th class="text-nowrap text-center">
                                <i class="ph-circles-three me-1"></i>
                                Grup
                            </th>
                            <th class="text-nowrap text-center" style="width: 80px">
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
                                <option value="letter.accept_date">Tanggal Diterima</option>
                                <option value="letter.letter_date">Tanggal Pengiriman</option>
                                <option value="letter.createdate">Tanggal Dibuat</option>
                                <option value="hibah_detail.createdate">Tanggal Hibah</option>
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
            </div>
            <div class="card-footer border-top">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ url('physical-collection/collection-grant') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
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
                    <i class="ph-gift me-2 text-success"></i>
                    <h6 class="mb-0 fw-semibold">Daftar Koleksi Hibah</h6>
                </div>
                <button type="button" class="btn btn-success" onclick="addListAction()">
                    <i class="ph-list-plus me-1"></i>
                    Tambahkan ke Daftar
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered display nowrap w-100" id="datatable-serverside">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center text-nowrap" style="width: 60px">
                                <i class="ph-check-square"></i>
                            </th>
                            <th class="text-center text-nowrap" style="width: 80px">
                                <i class="ph-hash"></i>
                                No
                            </th>
                            <th class="text-nowrap" style="min-width: 130px">
                                <i class="ph-calendar me-1"></i>
                                Tgl Hibah
                            </th>
                            <th class="text-nowrap" style="min-width: 250px">
                                <i class="ph-book-open me-1"></i>
                                Judul
                            </th>
                            <th class="text-nowrap" style="min-width: 180px">
                                <i class="ph-user-circle me-1"></i>
                                Pelaksana Serah
                            </th>
                            <th class="text-nowrap" style="min-width: 180px">
                                <i class="ph-map-pin me-1"></i>
                                Tujuan
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-truck me-1"></i>
                                Jasa Kirim
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-barcode me-1"></i>
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
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-arrow-bend-down-left me-1"></i>
                                Sumber
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 100px">
                                <i class="ph-circles-three me-1"></i>
                                Grup
                            </th>
                            <th class="text-nowrap" style="min-width: 200px">
                                <i class="ph-warning me-1"></i>
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
                    text: '<i class="ph-x me-1"></i> Hilangkan Centang'
                },
            ],
            ajax: {
                url: '{{ url("physical-collection/collection-grant/datatable") }}',
                dataType: 'JSON',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    executor_id: $('#executor_id').val(),
                    delivery_service_id: $('#delivery_service_id').val(),
                    date: $('#date').val(),
                    date_type: $('#date_type').val(),
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
                { orderable: true, className: 'align-middle text-center allow-select' },
                { orderable: false, className: 'align-middle text-wrap allow-select' },
                { orderable: true, className: 'align-middle text-wrap allow-select' },
                { orderable: true, className: 'align-middle text-wrap allow-select' },
                { orderable: true, className: 'align-middle text-wrap allow-select' },
                { orderable: true, className: 'align-middle allow-select' },
                { orderable: true, className: 'align-middle text-center allow-select' },
                { orderable: true, className: 'align-middle text-wrap allow-select' },
                { orderable: true, className: 'align-middle text-wrap allow-select' },
                { orderable: true, className: 'align-middle text-center allow-select' },
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
                { className: 'align-middle text-center' },
            ]
        });

        $('#datatable-action').DataTable().clear().draw();

        var localStorageData = localStorage.getItem('datatable-action-grant');
        var data = localStorageData ? JSON.parse(localStorageData) : [];

        $.each(data, function(i, val) {
            var btnRemove = `
                <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeListAction(${ val[0] })" title="Hapus dari daftar">
                    <i class="ph-trash"></i>
                </button>
            `;

            $('#datatable-action').DataTable().row.add([
                val[1],
                val[2],
                val[3],
                val[4],
                val[5],
                btnRemove
            ]).draw().node();
        });
    }

    function addListAction() {
        var addedCount = 0;

        window.gDataTable.rows({ selected: true }).every(function() {
            var row = this.node();
            var data = $(row).find('input[name="data"]');
            var id = data.data('id');
            var title = data.data('title');
            var executor = data.data('executor');
            var qty = data.data('qty-grant');
            var receipt = data.data('receipt');
            var group = data.data('group');

            var dataStorage = localStorage.getItem('datatable-action-grant');
            var currentDataStorage = dataStorage ? JSON.parse(dataStorage) : [];
            var isDuplicate = false;

            var payload = [
                id,
                title,
                executor,
                qty,
                receipt,
                group,
            ];

            for (var i = 0; i < currentDataStorage.length; i++) {
                if (currentDataStorage[i][0] === id) {
                    isDuplicate = true;

                    break;
                }
            }

            if (!isDuplicate) {
                currentDataStorage.push(payload);

                addedCount++;
            }

            localStorage.setItem('datatable-action-grant', JSON.stringify(currentDataStorage));
        });

        $('.buttons-select-none').click();

        loadDataAction();

        if(addedCount > 0) {
            new Noty({
                text: '<div class="d-flex align-items-center"><i class="ph-check-circle ph-2x me-3 text-success"></i><div><strong>Berhasil!</strong><br>' + addedCount + ' data telah ditambahkan ke daftar</div></div>',
                type: 'success',
                timeout: 3000,
                layout: 'topRight',
                theme: 'limitless'
            }).show();
        } else {
            new Noty({
                text: '<div class="d-flex align-items-center"><i class="ph-info ph-2x me-3 text-info"></i><div><strong>Info</strong><br>Tidak ada data baru yang ditambahkan</div></div>',
                type: 'info',
                timeout: 3000,
                layout: 'topRight',
                theme: 'limitless'
            }).show();
        }
    }

    function removeListAction(id) {
        var dataStorage = localStorage.getItem('datatable-action-grant');
        var currentDataStorage = dataStorage ? JSON.parse(dataStorage) : [];

        var updatedDataStorage = currentDataStorage.filter(function(item) {
            return item[0] !== id;
        });

        localStorage.setItem('datatable-action-grant', JSON.stringify(updatedDataStorage));

        loadDataAction();

        new Noty({
            text: '<div class="d-flex align-items-center"><i class="ph-trash ph-2x me-3 text-warning"></i><div><strong>Dihapus</strong><br>Data telah dihapus dari daftar</div></div>',
            type: 'warning',
            timeout: 2000,
            layout: 'topRight',
            theme: 'limitless'
        }).show();
    }

    function createGroup() {
        var id = [];
        var dataStorage = localStorage.getItem('datatable-action-grant');
        var responseDataStorage = dataStorage ? JSON.parse(dataStorage) : [];

        if(responseDataStorage.length === 0) {
            swalInit.fire({
                title: 'Peringatan',
                text: 'Belum ada data yang dipilih untuk dibuat grup',
                icon: 'warning',
                showCloseButton: true
            });

            return;
        }

        $.each(responseDataStorage, function(i, val) {
            id.push(val[0]);
        });

        var notyConfirm = new Noty({
            text: '<div class="mb-3"><h5 class="text-dark"><i class="ph-arrows-in-line-horizontal me-2"></i>Buat Grup Koleksi?</h5><div class="alert alert-info border-0 mb-0 mt-2"><i class="ph-info me-2"></i>Anda akan membuat grup untuk <strong>' + responseDataStorage.length + ' koleksi</strong> yang ada dalam daftar</div></div>',
            timeout: false,
            modal: true,
            layout: 'center',
            closeWith: 'button',
            type: 'confirm',
            buttons: [
                Noty.button('Batal', 'btn btn-light', function () {
                    notyConfirm.close();
                }),
                Noty.button('<i class="ph-check me-1"></i> Ya, Buat Grup', 'btn btn-success ms-2', function () {
                    $.ajax({
                        url: '{{ url("physical-collection/collection-grant/create-group") }}',
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
                                localStorage.removeItem('datatable-action-grant');

                                $('#datatable-action').DataTable().clear().draw();

                                swalInit.fire({
                                    title: 'Berhasil!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 3000,
                                    showCloseButton: false
                                });
                            } else {
                                swalInit.fire({
                                    title: 'Error',
                                    text: response.message,
                                    icon: 'error',
                                    showCloseButton: true
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

    function outGroup() {
        var id = [];
        var dataStorage = localStorage.getItem('datatable-action-grant');
        var responseDataStorage = dataStorage ? JSON.parse(dataStorage) : [];

        if(responseDataStorage.length === 0) {
            swalInit.fire({
                title: 'Peringatan',
                text: 'Belum ada data yang dipilih untuk dikeluarkan dari grup',
                icon: 'warning',
                showCloseButton: true
            });

            return;
        }

        $.each(responseDataStorage, function(i, val) {
            id.push(val[0]);
        });

        var notyConfirm = new Noty({
            text: '<div class="mb-3"><h5 class="text-dark"><i class="ph-arrows-out-line-horizontal me-2"></i>Keluarkan dari Grup?</h5><div class="alert alert-warning border-0 mb-0 mt-2"><i class="ph-warning me-2"></i>Anda akan mengeluarkan <strong>' + responseDataStorage.length + ' koleksi</strong> dari grup</div></div>',
            timeout: false,
            modal: true,
            layout: 'center',
            closeWith: 'button',
            type: 'confirm',
            buttons: [
                Noty.button('Batal', 'btn btn-light', function () {
                    notyConfirm.close();
                }),
                Noty.button('<i class="ph-check me-1"></i> Ya, Keluarkan', 'btn btn-warning ms-2', function () {
                    $.ajax({
                        url: '{{ url("physical-collection/collection-grant/out-group") }}',
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
                                localStorage.removeItem('datatable-action-grant');

                                $('#datatable-action').DataTable().clear().draw();

                                swalInit.fire({
                                    title: 'Berhasil!',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 3000,
                                    showCloseButton: false
                                });
                            } else {
                                swalInit.fire({
                                    title: 'Error',
                                    text: response.message,
                                    icon: 'error',
                                    showCloseButton: true
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
