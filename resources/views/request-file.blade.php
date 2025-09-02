<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">Permintaan File</h4>
            <a href="#page-header" class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto" data-bs-toggle="collapse">
                <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
            </a>
        </div>
    </div>
    <div class="page-header-content d-lg-flex border-top">
        <div class="d-flex">
            <div class="breadcrumb py-2">
                <a href="{{ url('home') }}" class="breadcrumb-item"><i class="ph-house"></i></a>
                <span class="breadcrumb-item active">Permintaan File</span>
            </div>
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
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Tanggal :</label>
                        <input type="text" class="form-control" name="date" id="date" placeholder="Semua Tanggal" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Pengelola :</label>
                        <select class="form-select" name="publisher_id" id="publisher_id" data-placeholder="Semua"></select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="text-end">
                <a href="{{ url('collection/analog-work') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
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
                        <th nowrap>No</th>
                        <th nowrap>Pengelola</th>
                        <th nowrap>Judul</th>
                        <th nowrap>Status</th>
                        <th nowrap>Jumlah Pengunduhan</th>
                        <th nowrap>Surat Permintaan</th>
                        <th nowrap>Tgl Permintaan</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    $(function() {
        select2Serverside('#publisher_id', 'publisher');
        datePickerBasic('#date');

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
            order: [[0, 'desc']],
            ajax: {
                url: '{{ url("request-file/datatable") }}',
                dataType: 'JSON',
                data: {
                    publisher_id: $('#publisher_id').val(),
                    date: $('#date').val(),
                },
                beforeSend: function() {
                    onLoading('show', '.dataTables_wrapper');
                },
                error: function(response) {
                    onLoading('close', '.dataTables_wrapper');

                    swalInit.fire({
                        html: '<b>' + response.responseJSON.exception + '</b><br>' + response.responseJSON.message,
                        icon: 'error',
                        showCloseButton: false
                    });
                }
            },
            columns: [
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle' },
            ]
        }).on('draw.dt', function() {
            onLoading('close', '.dataTables_wrapper');
        });

        window.gDataTable.columns.adjust().draw();
    }

    function setStatus(id, status) {
        if(status == 2) {
            var statusText = 'menerima';
        } else {
            var statusText = 'menolak';
        }

        var notyConfirm = new Noty({
            text: '<div class="mb-3"><h5 class="text-dark">Verifikasi Permintaan?</h5><span class="text-muted">Anda yakin ingin ' + statusText + ' permintaan ini?</span></div>',
            timeout: false,
            modal: true,
            layout: 'center',
            closeWith: 'button',
            type: 'confirm',
            buttons: [
                Noty.button('Tidak', 'btn btn-light', function () {
                    notyConfirm.close();
                }),
                Noty.button('Ya', 'btn btn-primary ms-2', function () {
                    $.ajax({
                        url: '{{ url("request-file/set-status") }}',
                        type: 'POST',
                        dataType: 'JSON',
                        data: {
                            id: id,
                            status: status,
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
                                notification('success', response.message);
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

                            swalInit.fire({
                                html: '<b>' + response.responseJSON.exception + '</b><br>' + response.responseJSON.message,
                                icon: 'error',
                                showCloseButton: false
                            });
                        }
                    });
                })
            ]
        }).show();
    }
</script>
