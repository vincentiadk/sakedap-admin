<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Koleksi Fisik - <span class="fw-normal">Koleksi Dalam Pengiriman</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <span class="badge bg-success p-2 bg-opacity-10 text-success">
                    Status Pengiriman
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
                    <i class="ph-funnel me-1 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Filter Pencarian</h6>
                </div>
                <button type="button" class="btn btn-sm btn-light" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="ph-caret-down"></i>
                </button>
            </div>
        </div>
        <div class="collapse" id="filterCollapse">
            <div class="card-body">
                <form id="form-filter">
                    <div class="row g-3">
                        <div class="col-lg-12 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-user-circle me-1"></i>
                                Pelaksana Serah
                            </label>
                            <select class="form-select" name="executor_id" id="executor_id" data-placeholder="Semua Pelaksana"></select>
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
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-calendar-blank me-1"></i>
                                Tanggal
                            </label>
                            <div class="input-group">
                                <select class="form-select w-auto flex-grow-0" name="date_type" id="date_type">
                                    <option value="letter_date">Tanggal Pengiriman</option>
                                    <option value="create_date">Tanggal Dibuat</option>
                                </select>
                                <input type="text" class="form-control" name="date" id="date" placeholder="Pilih tanggal" readonly>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-map-pin me-1"></i>
                                Tujuan
                            </label>
                            <select class="form-select" name="branch_id" id="branch_id" data-placeholder="Semua Tujuan"></select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer border-top">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ url('physical-collection/collection-on-delivery') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
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
                    <h6 class="mb-0 fw-semibold">Daftar Koleksi Dalam Pengiriman</h6>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary" id="total-records">
                    <i class="ph-list-checks me-1"></i>
                    <span id="record-count">0</span> Data
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered display nowrap w-100" id="datatable-serverside">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center text-nowrap" style="width: 60px">
                                <i class="ph-hash"></i>
                            </th>
                            <th class="text-center text-nowrap" style="width: 100px">
                                <i class="ph-gear"></i>
                                Aksi
                            </th>
                            <th class="text-nowrap" style="min-width: 250px">
                                <i class="ph-book me-1"></i>
                                Judul
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-barcode me-1"></i>
                                ISBN / ISSN
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
                                Jumlah Eks
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-file me-1"></i>
                                Jenis Media
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 120px">
                                <i class="ph-flag me-1"></i>
                                Status
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="modal-detail" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ph-info me-2"></i>
                    Detail Koleksi Dalam Pengiriman
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        datePickerBasic('#date');

        if(parseInt('{{ Main::isPerpusnas() }}') == 0) {
            select2Serverside('#branch_id', 'branch', {
                province_id: '{{ session("province_id") }}'
            }, {
                minimumInputLength: 0
            });

            select2Serverside('#executor_id', 'executor', {
                province_id: '{{ session("province_id") }}',
            });
        } else {
            select2Serverside('#executor_id', 'executor');
            select2Serverside('#branch_id', 'branch');
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
                url: '{{ url("physical-collection/collection-on-delivery/datatable") }}',
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
                { orderable: true, className: 'align-middle text-center fw-semibold' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-center' },
            ],
            initComplete: function (settings, json) {
                var table = this.api();
                const searchInput = $('div.dataTables_filter input');

                searchInput.off().unbind();

                searchInput.on('keyup', debounce(function () {
                    table.search(this.value).draw();
                }, 500));

                updateRecordCount(json.recordsFiltered);
            },
            drawCallback: function(settings) {
                var api = this.api();

                updateRecordCount(api.page.info().recordsFiltered);
            }
        }).on('draw.dt', function() {
            onLoading('close', '#datatable-serverside_wrapper');
        });

        window.gDataTable.columns.adjust().draw();
    }

    function updateRecordCount(count) {
        $('#record-count').text(count || 0);
    }

    function detail(id) {
        $.ajax({
            url: '{{ url("physical-collection/collection-on-delivery/detail") }}',
            type: 'GET',
            dataType: 'JSON',
            data: {
                id: id
            },
            beforeSend: function() {
                $('.modal-body').html('');
                $('#modal-detail').modal('show');

                onLoading('show', '.modal-content');
            },
            success: function(response) {
                let itemHistory = '';
                let itemAWB = '';

                if (response.awb) {
                    for (const val of response.awb.manifest) {
                        let icon = '';

                        if (val.manifest_code === 'DELIVERED') {
                            icon = `
                                <div class="bg-success text-white">
                                    <i class="ph-check-circle"></i>
                                </div>
                            `;
                        } else {
                            icon = `
                                <div class="bg-light text-muted">
                                    <i class="ph-truck"></i>
                                </div>
                            `;
                        }

                        itemAWB += `
                            <div class="timeline-row">
                                <div class="timeline-icon">${ icon }</div>
                                <div class="timeline-time">
                                    ${ val.manifest_date }
                                    <div class="text-muted">${ val.manifest_time }</div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="mb-1">${ val.city_name }</h6>
                                        ${ val.manifest_description }
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                }

                if (response.history) {
                    for (const val of response.history) {
                        itemHistory += `
                            <div class="timeline-row">
                                <div class="timeline-icon">
                                    <div class="bg-primary text-white">
                                        <i class="ph-arrow-counter-clockwise"></i>
                                    </div>
                                </div>
                                <div class="timeline-time">
                                    ${ moment(val.ACTION_DATE).format('YYYY-MM-DD') }
                                    <div class="text-muted">${ moment(val.ACTION_DATE).format('H:mm:ss') }</div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="mb-1">${ val.ACTION_BY }</h6>
                                        ${ val.NOTE }
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                }

                let htmlItemHistory = '';
                let htmlItemAWB = '';

                if (itemHistory) {
                    htmlItemHistory = `
                        <div class="timeline timeline-start">
                            <div class="timeline-container">
                                ${ itemHistory }
                            </div>
                        </div>
                    `;
                } else {
                    htmlItemHistory = `
                        <div class="alert alert-light border-0 alert-dismissible">
                            <i class="ph-info me-2"></i>
                            Belum ada histori data
                        </div>
                    `;
                }

                if (itemAWB) {
                    htmlItemAWB = `
                        <div class="timeline timeline-start">
                            <div class="timeline-container">
                                ${ itemAWB }
                            </div>
                        </div>
                    `;
                } else {
                    htmlItemAWB = `
                        <div class="alert alert-light border-0 alert-dismissible">
                            <i class="ph-info me-2"></i>
                            Belum ada histori pengiriman
                        </div>
                    `;
                }

                $('.modal-body').html(`
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light border-bottom">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ph-info me-1"></i>
                                Informasi Koleksi
                            </h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <th class="table-light fw-semibold" width="25%">
                                            <i class="ph-user-circle me-1"></i>
                                            Pelaksana Serah
                                        </th>
                                        <td width="75%">${ response.data?.ID_PENERBIT } | ${ response.data?.NAME_PENERBIT }</td>
                                    </tr>
                                    <tr>
                                        <th class="table-light fw-semibold">
                                            <i class="ph-book me-1"></i>
                                            Judul
                                        </th>
                                        <td>${ response.data?.TITLE }</td>
                                    </tr>
                                    <tr>
                                        <th class="table-light fw-semibold">
                                            <i class="ph-map-pin me-1"></i>
                                            Tujuan
                                        </th>
                                        <td>${ response.data?.NAME_BRANCH }</td>
                                    </tr>
                                    <tr>
                                        <th class="table-light fw-semibold">
                                            <i class="ph-receipt me-1"></i>
                                            Resi
                                        </th>
                                        <td>${ response.data?.RECEIPT_NO_LETTER }</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-light border-bottom">
                                    <h6 class="mb-0 fw-semibold">
                                        <i class="ph-clock-counter-clockwise me-1"></i>
                                        Histori Data
                                    </h6>
                                </div>
                                <div class="card-body">
                                    ${ htmlItemHistory }
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-light border-bottom">
                                    <h6 class="mb-0 fw-semibold">
                                        <i class="ph-truck me-1"></i>
                                        Histori Pengiriman
                                    </h6>
                                </div>
                                <div class="card-body">
                                    ${ htmlItemAWB }
                                </div>
                            </div>
                        </div>
                    </div>
                `);

                onLoading('close', '.modal-content');
            },
            error: function(response) {
                onLoading('close', '.modal-content');
                responseError(response);
            }
        });
    }
</script>
