<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Koleksi Fisik - <span class="fw-normal">Koleksi Dikembalikan</span>
            </h4>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="card">
        <div class="card-header d-flex align-items-center py-0">
            <h5 class="py-3 mb-0">Daftar Koleksi Yang Akan Dihibahkan</h5>
            <div class="ms-auto my-auto">
                <div class="btn-group">
                    <button type="button" class="btn btn-teal dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="ph-hand-pointing me-1"></i>
                        Aksi
                    </button>
                    <div class="dropdown-menu">
                        <a href="javascript:void(0);" class="dropdown-item" onclick="grant()">
                            <i class="ph-gift me-1"></i>
                            Hibahkan
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item" onclick="taken(null, 1)">
                            <i class="ph-handshake me-1"></i>
                            Tandai Sudah Diambil
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item" onclick="taken(null, 0)">
                            <i class="ph-minus-circle me-1"></i>
                            Tandai Belum Diambil
                        </a>
                        <a href="javascript:void(0);" class="dropdown-item" onclick="taken(null, -1)">
                            <i class="ph-x me-1"></i>
                            Tandai Batal Diambil
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover w-100 display" id="datatable-action">
                <thead class="text-bg-light">
                    <tr>
                        <th class="text-nowrap">Judul</th>
                        <th class="text-nowrap">Pelaksana Serah</th>
                        <th class="text-nowrap">Jumlah</th>
                        <th class="text-nowrap">Resi</th>
                        <th class="text-nowrap">Hapus</th>
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
                        <label class="form-label">Pelaksana Serah :</label>
                        <select class="form-select" name="executor_id" id="executor_id" data-placeholder="Semua"></select>
                    </div>
                </div>
                <div class="col-md-4">
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
                <div class="col-md-4">
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
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="text-end">
                <a href="{{ url('physical-collection/collection-retur') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
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
            <h5 class="py-3 mb-0">Daftar Koleksi Dikembalikan</h5>
            <div class="ms-auto my-auto">
                <button type="button" class="btn btn-teal" onclick="addListAction()">
                    <i class="ph-list-plus me-1"></i>
                    Tambahkan ke Daftar Atas
                </button>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover w-100 display" id="datatable-serverside">
                <thead class="text-bg-light">
                    <tr>
                        <th class="text-nowrap">#</th>
                        <th class="text-nowrap">No</th>
                        <th class="text-nowrap"><i class="ph-gear"></i></th>
                        <th class="text-nowrap">Auto Hibah</th>
                        <th class="text-nowrap">Status</th>
                        <th class="text-nowrap">Judul</th>
                        <th class="text-nowrap">Pelaksana Serah</th>
                        <th class="text-nowrap">Tujuan</th>
                        <th class="text-nowrap">Jasa Kirim</th>
                        <th class="text-nowrap">Resi</th>
                        <th class="text-nowrap">Jumlah</th>
                        <th class="text-nowrap">Jenis Media</th>
                        <th class="text-nowrap">Alasan Ditolak</th>
                        <th class="text-nowrap">Proses By</th>
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
                selector: 'td.allow-select'
            },
            buttons: [
                { extend: 'selectAll', className: 'btn btn-teal', text: 'Centang Semua' },
                { extend: 'selectNone', className: 'btn btn-warning', text: 'Hilangkan Semua Centang' },
            ],
            ajax: {
                url: '{{ url("physical-collection/collection-retur/datatable") }}',
                dataType: 'JSON',
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
                { orderable: true, className: 'align-middle text-center allow-select' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle' },
                { orderable: false, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap allow-select' },
                { orderable: true, className: 'align-middle text-wrap allow-select' },
                { orderable: true, className: 'align-middle text-wrap allow-select' },
                { orderable: true, className: 'align-middle allow-select' },
                { orderable: true, className: 'align-middle allow-select' },
                { orderable: true, className: 'align-middle allow-select' },
                { orderable: true, className: 'align-middle text-wrap allow-select' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-wrap allow-select' },
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
                { className: 'align-middle' },
                { className: 'align-middle' },
                { className: 'align-middle text-center' },
            ]
        });

        $('#datatable-action').DataTable().clear().draw();

        var localStorageData = localStorage.getItem('datatable-action-retur');
        var data = localStorageData ? JSON.parse(localStorageData) : [];

        $.each(data, function(i, val) {
            var btnRemove = `
                <button type="button" class="btn btn-danger btn-sm col-12" onclick="removeListAction(${ val[0] })">
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
            text: 'Data telah ditambahkan dalam list',
            icon: 'success'
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
    }

    function grant(param = null) {
        if(param) {
            var id = [param];
        } else {
            var id = [];
            var dataStorage = localStorage.getItem('datatable-action-retur');
            var responseDataStorage = dataStorage ? JSON.parse(dataStorage) : [];

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
                Noty.button('Ya', 'btn btn-success ms-2', function () {
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

            $.each(responseDataStorage, function(i, val) {
                id.push(val[0]);
            });
        }

        var notyConfirm = new Noty({
            text: '<div class="mb-3"><h5 class="text-dark">Pengambilan Koleksi?</h5><span class="text-muted">Anda yakin ingin menandai pengambilan koleksi dalam daftar ini?</span></div>',
            timeout: false,
            modal: true,
            layout: 'center',
            closeWith: 'button',
            type: 'confirm',
            buttons: [
                Noty.button('Tidak', 'btn btn-light', function () {
                    notyConfirm.close();
                }),
                Noty.button('Ya', 'btn btn-teal ms-2', function () {
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
