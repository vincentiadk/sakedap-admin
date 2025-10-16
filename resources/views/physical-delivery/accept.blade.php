<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pengiriman Fisik - <span class="fw-normal">Diterima</span>
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
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">No Resi :</label>
                        <input type="text" class="form-control" name="receipt_no" id="receipt_no" placeholder="Semua">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Jenis Tanggal :</label>
                        <select class="form-select" name="date_type" id="date_type">
                            <option value="accept_date">Diterima</option>
                            <option value="letter_date">Pengiriman</option>
                            <option value="createdate">Dibuat</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Tanggal :</label>
                        <input type="text" class="form-control" name="date" id="date" placeholder="Semua Tanggal" readonly>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Status :</label>
                        <select class="form-select" name="status" id="status">
                            <option value="">Semua</option>
                            <option value="DITERIMA PENUH">DITERIMA PENUH</option>
                            <option value="DITERIMA PARSIAL">DITERIMA PARSIAL</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Proses By :</label>
                        <select class="form-select select2-basic" name="proses_by" id="proses_by" data-placeholder="Semua">
                            <option value="">Semua</option>
                            @if($prosesBy)
                                @foreach($prosesBy as $pb)
                                    <option value="{{ $pb->PROSES_BY }}">{{ $pb->PROSES_BY }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="text-end">
                <a href="{{ url('physical-delivery/accept') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
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
                        <th class="text-nowrap" rowspan="2">No</th>
                        <th class="text-nowrap" rowspan="2">Aksi</th>
                        <th class="text-nowrap" rowspan="2">Tgl Terima</th>
                        <th class="text-nowrap" rowspan="2">User</th>
                        <th class="text-nowrap" rowspan="2">Pelaksana Serah</th>
                        <th class="text-nowrap" rowspan="2">Resi</th>
                        <th class="text-nowrap" rowspan="2">Jasa Kirim</th>
                        <th class="text-nowrap" rowspan="2">Tujuan</th>
                        <th class="text-nowrap text-center" colspan="2">Pengiriman</th>
                        <th class="text-nowrap text-center" colspan="2">Penerimaan</th>
                        <th class="text-nowrap text-center" colspan="2">Ditolak (Hibah)</th>
                        <th class="text-nowrap" rowspan="2">Status</th>
                        <th class="text-nowrap" rowspan="2">Proses By</th>
                    </tr>
                    <tr>
                        <th class="text-nowrap text-center">Judul</th>
                        <th class="text-nowrap text-center">Eksemplar</th>
                        <th class="text-nowrap text-center">Judul</th>
                        <th class="text-nowrap text-center">Eksemplar</th>
                        <th class="text-nowrap text-center">Judul</th>
                        <th class="text-nowrap text-center">Eksemplar</th>
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

            select2Serverside('#branch_id', 'branch', {
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
            order: [[2, 'desc']],
            ajax: {
                url: '{{ url("physical-delivery/accept/datatable") }}',
                dataType: 'JSON',
                data: {
                    executor_id: $('#executor_id').val(),
                    delivery_service_id: $('#delivery_service_id').val(),
                    date: $('#date').val(),
                    date_type: $('#date_type').val(),
                    status: $('#status').val(),
                    receipt_no: $('#receipt_no').val(),
                    branch_id: $('#branch_id').val(),
                    proses_by: $('#proses_by').val(),
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
                { orderable: false, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: false, className: 'align-middle text-wrap' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle' },
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

    function sendEmail(id) {
        var notyConfirm = new Noty({
            text: '<div class="mb-3"><h5 class="text-dark">Kirim Email?</h5><span class="text-muted">Anda yakin ingin mengirim email?</span></div>',
            timeout: false,
            modal: true,
            layout: 'center',
            closeWith: 'button',
            type: 'confirm',
            buttons: [
                Noty.button('Tidak', 'btn btn-light', function () {
                    notyConfirm.close();
                }),
                Noty.button('Kirim', 'btn btn-danger ms-2', function () {
                    $.ajax({
                        url: '{{ url("physical-delivery/accept/send-email") }}',
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

                                swalInit.fire({
                                    title: 'Berhasil',
                                    text: response.message,
                                    icon: 'success',
                                    showCloseButton: false
                                });
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
