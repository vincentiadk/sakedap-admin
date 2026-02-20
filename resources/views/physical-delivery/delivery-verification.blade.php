<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pengiriman Fisik - <span class="fw-normal">Verifikasi Pengiriman</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <span class="badge bg-primary p-2 bg-opacity-10 text-primary">
                    Status Verifikasi
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
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-user-circle me-1"></i>
                                Pelaksana Serah
                            </label>
                            <select class="form-select" name="executor_id" id="executor_id" data-placeholder="Semua Pelaksana"></select>
                        </div>
                        <div class="col-lg-3 col-md-6">
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
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-map-pin me-1"></i>
                                Tujuan
                            </label>
                            <select class="form-select" name="branch_id" id="branch_id" data-placeholder="Semua Tujuan"></select>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-barcode me-1"></i>
                                No Resi
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ph-hash"></i>
                                </span>
                                <input type="text" class="form-control" name="receipt_no" id="receipt_no" placeholder="Cari berdasarkan nomor resi">
                            </div>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-calendar-blank me-1"></i>
                                Jenis Tanggal
                            </label>
                            <select class="form-select" name="date_type" id="date_type">
                                <option value="accept_date">Tanggal Diterima</option>
                                <option value="letter_date">Tanggal Pengiriman</option>
                                <option value="create_date">Tanggal Dibuat</option>
                                <option value="arrival_date">Tanggal Sampai</option>
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-calendar me-1"></i>
                                Tanggal
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ph-calendar-blank"></i>
                                </span>
                                <input type="text" class="form-control" name="date" id="date" placeholder="Pilih tanggal" readonly>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-flag me-1"></i>
                                Status
                            </label>
                            <select class="form-select" name="status" id="status">
                                <option value="">Semua Status</option>
                                <option value="TERKIRIM">Terkirim</option>
                                <option value="CEK FISIK">Cek Fisik</option>
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="ph-check-circle me-1"></i>
                                Received By
                            </label>
                            <select class="form-select select2-basic" name="received_by" id="received_by" data-placeholder="Semua Penerima">
                                <option value="">Semua</option>
                                @if($receivedBy)
                                    @foreach($receivedBy as $pb)
                                        <option value="{{ $pb->RECEIVED_BY }}">{{ $pb->RECEIVED_BY }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="#advance-search" class="fw-semibold text-primary" data-bs-toggle="collapse">
                            <i class="ph-plus-circle me-1"></i>
                            Pencarian Lanjutan
                        </a>
                    </div>
                    <div class="collapse mt-3" id="advance-search">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-lg-3 col-md-6">
                                        <label class="form-label">
                                            <i class="ph-book-open me-1"></i>
                                            Judul
                                        </label>
                                        <input type="text" class="form-control" name="title" id="title" placeholder="Cari judul">
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <label class="form-label">
                                            <i class="ph-pen me-1"></i>
                                            Kepengarangan
                                        </label>
                                        <input type="text" class="form-control" name="author" id="author" placeholder="Cari pengarang">
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <label class="form-label">
                                            <i class="ph-identification-card me-1"></i>
                                            ISBN
                                        </label>
                                        <input type="text" class="form-control" name="isbn" id="isbn" placeholder="Cari ISBN">
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <label class="form-label">
                                            <i class="ph-calendar-blank me-1"></i>
                                            Tahun Terbit
                                        </label>
                                        <input type="number" class="form-control" name="publish_year" id="publish_year" placeholder="Cari tahun terbit">
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <label class="form-label">
                                            <i class="ph-books me-1"></i>
                                            Edisi Serial
                                        </label>
                                        <input type="text" class="form-control" name="edition_serial" id="edition_serial" placeholder="Cari edisi serial">
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <label class="form-label">
                                            <i class="ph-clock me-1"></i>
                                            Kala Terbit
                                        </label>
                                        <input type="text" class="form-control" name="periodicals" id="periodicals" placeholder="Cari kala terbit">
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <label class="form-label">
                                            <i class="ph-note me-1"></i>
                                            Ket Fisik
                                        </label>
                                        <input type="text" class="form-control" name="physical_description" id="physical_description" placeholder="Cari keterangan fisik">
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <label class="form-label">
                                            <i class="ph-file-text me-1"></i>
                                            Sinopsis
                                        </label>
                                        <input type="text" class="form-control" name="sinopsis" id="sinopsis" placeholder="Cari sinopsis">
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <label class="form-label">
                                            <i class="ph-file me-1"></i>
                                            Jenis Media
                                        </label>
                                        <input type="text" class="form-control" name="media_type" id="media_type" placeholder="Cari jenis media">
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <label class="form-label">
                                            <i class="ph-tag me-1"></i>
                                            No Panggil Jilid
                                        </label>
                                        <input type="text" class="form-control" name="binding" id="binding" placeholder="Cari no panggil jilid">
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <label class="form-label">
                                            <i class="ph-qr-code me-1"></i>
                                            QRCBN
                                        </label>
                                        <input type="text" class="form-control" name="qrcbn" id="qrcbn" placeholder="Cari QRCBN">
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <label class="form-label">
                                            <i class="ph-list me-1"></i>
                                            ISBD
                                        </label>
                                        <input type="text" class="form-control" name="isbd" id="isbd" placeholder="Cari ISBD">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer border-top">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ url('physical-delivery/delivery-verification') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
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
                    <h6 class="mb-0 fw-semibold">Daftar Verifikasi Pengiriman</h6>
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
                            <th class="text-center text-nowrap" rowspan="2" style="width: 60px">
                                <i class="ph-hash"></i>
                            </th>
                            <th class="text-center text-nowrap" rowspan="2" style="width: 100px">
                                <i class="ph-gear"></i>
                                Aksi
                            </th>
                            <th class="text-nowrap" rowspan="2" style="min-width: 180px">
                                <i class="ph-user-circle me-1"></i>
                                Pelaksana Serah
                            </th>
                            <th class="text-center text-nowrap" rowspan="2" style="min-width: 120px">
                                <i class="ph-flag me-1"></i>
                                Status
                            </th>
                            <th class="text-center text-nowrap" rowspan="2" style="min-width: 130px">
                                <i class="ph-calendar-check me-1"></i>
                                Tgl Sampai
                            </th>
                            <th class="text-center text-nowrap" rowspan="2" style="min-width: 100px">
                                <i class="ph-clock me-1"></i>
                                Aging
                            </th>
                            <th class="text-nowrap" rowspan="2" style="min-width: 150px">
                                <i class="ph-barcode me-1"></i>
                                Resi
                            </th>
                            <th class="text-nowrap" rowspan="2" style="min-width: 150px">
                                <i class="ph-truck me-1"></i>
                                Jasa Kirim
                            </th>
                            <th class="text-nowrap" rowspan="2" style="min-width: 200px">
                                <i class="ph-map-pin me-1"></i>
                                Tujuan
                            </th>
                            <th class="text-center" colspan="2">
                                <i class="ph-package me-1"></i>
                                Pengiriman
                            </th>
                        </tr>
                        <tr>
                            <th class="text-center text-nowrap" style="min-width: 100px">
                                <i class="ph-book me-1"></i>
                                Judul
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 100px">
                                <i class="ph-stack me-1"></i>
                                Eksemplar
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
            select2Serverside('#executor_id', 'executor', {
                province_id: '{{ session("province_id") }}',
            });

            select2Serverside('#branch_id', 'branch', {
                province_id: '{{ session("province_id") }}',
            }, {
                minimumInputLength: 0
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
            order: [[4, 'asc'], [3, 'desc']],
            ajax: {
                url: '{{ url("physical-delivery/delivery-verification/datatable") }}',
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
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: false, className: 'align-middle text-wrap' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle text-center' },
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
                        url: '{{ url("physical-delivery/delivery-verification/send-email") }}',
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
