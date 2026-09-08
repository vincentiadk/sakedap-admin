<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pengiriman Fisik - <span class="fw-normal">Daftar Antrian</span>
            </h4>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="card border-0 shadow-sm">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ph-queue me-2 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Antrian Penerimaan Fisik</h6>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary">
                    <i class="ph-package me-1"></i>
                    Paket Masuk
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label">Rentang Tanggal Terima</label>
                    <input type="text" class="form-control" id="filter_date" placeholder="Semua tanggal" autocomplete="off">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Lokasi</label>
                    <select class="form-select" id="filter_lokasi">
                        <option value="">Semua lokasi</option>
                        @foreach ($lokasi as $l)
                            <option value="{{ $l->ID }}">{{ $l->NAMA }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" id="filter_status">
                        <option value="">Semua status</option>
                        <option value="diterima_satpam">Diterima satpam</option>
                        <option value="diproses">Diproses (belum ditutup)</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="button" class="btn btn-primary flex-fill" onclick="onReloadTable()">
                        <i class="ph-funnel me-1"></i> Terapkan
                    </button>
                    <button type="button" class="btn btn-light" onclick="onResetFilter()" title="Reset filter">
                        <i class="ph-arrow-counter-clockwise"></i>
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered display nowrap w-100" id="datatable-serverside">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center text-nowrap" style="width: 60px"><i class="ph-hash"></i></th>
                            <th class="text-center text-nowrap" style="width: 110px"><i class="ph-gear"></i> Aksi</th>
                            <th class="text-nowrap" style="min-width: 150px"><i class="ph-ticket me-1"></i>Nomor Antrian</th>
                            <th class="text-center text-nowrap" style="min-width: 140px"><i class="ph-clock me-1"></i>Waktu Terima</th>
                            <th class="text-center text-nowrap" style="min-width: 120px"><i class="ph-truck me-1"></i>Cara Datang</th>
                            <th class="text-nowrap" style="min-width: 160px"><i class="ph-user me-1"></i>Ekspedisi / Pengirim</th>
                            <th class="text-nowrap" style="min-width: 160px"><i class="ph-barcode me-1"></i>No. Resi</th>
                            <th class="text-nowrap" style="min-width: 180px"><i class="ph-buildings me-1"></i>Penerbit</th>
                            <th class="text-center text-nowrap" style="min-width: 110px"><i class="ph-package me-1"></i>Jumlah</th>
                            <th class="text-nowrap" style="min-width: 150px"><i class="ph-map-pin me-1"></i>Lokasi</th>
                            <th class="text-nowrap" style="min-width: 130px"><i class="ph-user-circle me-1"></i>Petugas</th>
                            <th class="text-center text-nowrap" style="min-width: 110px"><i class="ph-database me-1"></i>Sumber</th>
                            <th class="text-center text-nowrap" style="min-width: 110px"><i class="ph-flag me-1"></i>Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('#filter_date').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Hapus',
                applyLabel: 'Terapkan',
                format: 'DD/MM/YYYY',
            }
        });

        $('#filter_date').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            onReloadTable();
        });

        $('#filter_date').on('cancel.daterangepicker', function() {
            $(this).val('');
            onReloadTable();
        });

        $('#filter_lokasi, #filter_status').on('change', onReloadTable);

        loadData();
    });

    function onReloadTable() {
        window.gDataTable.ajax.reload(null, false);
    }

    /** Menutup dus yang tertinggal di "diproses" karena lupa ditekan Selesai. */
    function tandaiSelesai(id, nomor) {
        Swal.fire({
            title: 'Tandai selesai?',
            html: `Dus <b>${nomor}</b> akan ditandai selesai dikerjakan.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Selesai',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-primary me-2',
                cancelButton: 'btn btn-outline-secondary'
            },
        }).then(function (hasil) {
            if (!hasil.isConfirmed) return;

            $.ajax({
                url: '{{ url("physical-delivery/queue/selesaikan") }}',
                type: 'POST',
                dataType: 'JSON',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: { id: id },
                success: function (res) {
                    notification('success', res.message);
                    onReloadTable();
                },
                error: function (xhr) {
                    const r = xhr.responseJSON || {};
                    Swal.fire({
                        title: 'Gagal',
                        text: r.message || 'Gagal menandai selesai.',
                        icon: 'error',
                        customClass: { confirmButton: 'btn btn-primary' },
                    });
                }
            });
        });
    }

    function onResetFilter() {
        $('#filter_date').val('');
        $('#filter_lokasi').val('');
        $('#filter_status').val('');
        onReloadTable();
    }

    function loadData() {
        window.gDataTable = $('#datatable-serverside').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            scrollX: true,
            destroy: true,
            order: [],
            ajax: {
                url: '{{ url("physical-delivery/queue/datatable") }}',
                dataType: 'JSON',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(d) {
                    d.date = $('#filter_date').val();
                    d.lokasi_id = $('#filter_lokasi').val();
                    d.status = $('#filter_status').val();
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
                { orderable: false, className: 'align-middle text-center fw-semibold' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: true,  className: 'align-middle' },
                { orderable: true,  className: 'align-middle text-center' },
                { orderable: true,  className: 'align-middle text-center' },
                { orderable: true,  className: 'align-middle text-wrap' },
                { orderable: true,  className: 'align-middle' },
                { orderable: true,  className: 'align-middle text-wrap' },
                { orderable: true,  className: 'align-middle text-center' },
                { orderable: true,  className: 'align-middle text-wrap' },
                { orderable: true,  className: 'align-middle' },
                { orderable: true,  className: 'align-middle text-center' },
                { orderable: true,  className: 'align-middle text-center' },
            ],
        }).on('draw.dt', function() {
            // Wajib: onLoading('show') di beforeSend hanya ditutup di sini.
            // Tanpa ini spinner-nya berputar terus walau data sudah masuk.
            onLoading('close', '#datatable-serverside_wrapper');
        });

        window.gDataTable.columns.adjust().draw();
    }
</script>
