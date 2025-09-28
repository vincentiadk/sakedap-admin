<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pengiriman - <span class="fw-normal">Dalam Pengiriman</span>
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
                        <label class="form-label">Tujuan :</label>
                        <select class="form-select" name="branch_id" id="branch_id" data-placeholder="Semua"></select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="text-end">
                <a href="{{ url('delivery/sent') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
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
                        <th class="text-nowrap"><i class="ph-gear"></i></th>
                        <th class="text-nowrap">Judul</th>
                        <th class="text-nowrap">ISBN / ISSN</th>
                        <th class="text-nowrap">Pelaksana Serah</th>
                        <th class="text-nowrap">Tujuan</th>
                        <th class="text-nowrap">Jasa Kirim</th>
                        <th class="text-nowrap">Resi</th>
                        <th class="text-nowrap">Jumlah Eks</th>
                        <th class="text-nowrap">Jenis Media</th>
                        <th class="text-nowrap">Status</th>
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

        if(parseInt('{{ Main::isNotCenterBranch() }}') === 1) {
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
                url: '{{ url("delivery/sent/datatable") }}',
                dataType: 'JSON',
                data: {
                    executor_id: $('#executor_id').val(),
                    delivery_service_id: $('#delivery_service_id').val(),
                    date: $('#date').val(),
                    date_type: $('#date_type').val(),
                    branch_id: $('#branch_id').val(),
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
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
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

    function detail(id) {
        $.ajax({
            url: '{{ url("delivery/sent/detail") }}',
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
                }

                if (itemAWB) {
                    htmlItemAWB = `
                        <div class="timeline timeline-start">
                            <div class="timeline-container">
                                ${ itemAWB }
                            </div>
                        </div>
                    `;
                }

                $('.modal-body').html(`
                    <table class="table table-bordered mb-4">
                        <tbody>
                            <tr>
                                <th class="table-success" width="20%">Pelaksana Serah</th>
                                <td width="80%">${ response.data?.NAME_PENERBIT }</td>
                            </tr>
                            <tr>
                                <th class="table-success" width="20%">Judul</th>
                                <td width="80%">${ response.data?.TITLE }</td>
                            </tr>
                            <tr>
                                <th class="table-success" width="20%">Tujuan</th>
                                <td width="80%">${ response.data?.NAME_BRANCH }</td>
                            </tr>
                            <tr>
                                <th class="table-success" width="20%">Resi</th>
                                <td width="80%">${ response.data?.RECEIPT_NO_LETTER }</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="fw-bold border-bottom pb-2 mb-3">Histori Data</div>
                            ${ htmlItemHistory }
                        </div>
                        <div class="col-md-6">
                            <div class="fw-bold border-bottom pb-2 mb-3">Histori Pengiriman</div>
                            ${ htmlItemAWB }
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
