<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pengiriman Fisik - Verifikasi Pengiriman - <span class="fw-normal">Detail</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <a href="{{ url('physical-delivery/delivery-verification') }}" class="btn btn-primary">
                    <i class="ph-arrow-left me-1"></i>
                    Kembali ke Tabel
                </a>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    <form id="form-data">
        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center">
                    <i class="ph-info me-1 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Informasi Pengiriman</h6>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="text-muted fw-semibold small mb-1">
                                        <i class="ph-calendar-blank me-1"></i>
                                        Tanggal
                                    </label>
                                    <div class="fw-semibold text-dark">
                                        {{ Carbon::parse($letter->LETTER_DATE)->isoFormat('D MMMM Y') }}, {{ Carbon::parse($letter->LETTER_DATE)->format('H:i') }} WIB
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="text-muted fw-semibold small mb-1">
                                        <i class="ph-user me-1"></i>
                                        Pengirim
                                    </label>
                                    <div class="fw-semibold text-dark">{{ $letter->SENDER ?: 'Tidak ada' }}</div>
                                </div>
                                <div class="form-group">
                                    <label class="text-muted fw-semibold small mb-1">
                                        <i class="ph-truck me-1"></i>
                                        Jasa Kirim
                                    </label>
                                    <div class="fw-semibold text-dark">{{ $letter->NAME_JASA_PENGIRIMAN ?: 'Tidak ada' }}</div>
                                </div>
                                <div class="form-group">
                                    <label class="text-muted fw-semibold small mb-1">
                                        <i class="ph-barcode me-1"></i>
                                        No Resi
                                    </label>
                                    <div class="fw-semibold text-dark">{{ $letter->RECEIPT_NO ?: 'Tidak ada' }}</div>
                                </div>
                                <div class="form-group">
                                    <label class="text-muted fw-semibold small mb-1">
                                        <i class="ph-scales me-1"></i>
                                        Berat
                                    </label>
                                    <div class="fw-semibold text-dark">{{ number_format(($letter->BERAT ?: 0) / 1000, 2, ',', '.') }} Kg</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="text-muted fw-semibold small mb-1">
                                        <i class="ph-file-text me-1"></i>
                                        No Surat
                                    </label>
                                    <div class="fw-semibold text-dark">{{ $letter->LETTER_NUMBER ?: 'Tidak ada' }}</div>
                                </div>
                                <div class="form-group">
                                    <label class="text-muted fw-semibold small mb-1">
                                        <i class="ph-phone me-1"></i>
                                        Telepon
                                    </label>
                                    <div class="fw-semibold text-dark">{{ $letter->PHONE ?: 'Tidak ada' }}</div>
                                </div>
                                <div class="form-group">
                                    <label class="text-muted fw-semibold small mb-1">
                                        <i class="ph-map-pin me-1"></i>
                                        Tujuan
                                    </label>
                                    <div class="fw-semibold text-dark">{{ $letter->NAME_BRANCH ?: 'Tidak ada' }}</div>
                                </div>
                                <div class="form-group">
                                    <label class="text-muted fw-semibold small mb-1">
                                        <i class="ph-currency-circle-dollar me-1"></i>
                                        Biaya Kirim
                                    </label>
                                    <div class="fw-semibold text-dark">Rp {{ number_format($letter->BIAYA_KIRIM ?: 0, 0, ',', '.') }}</div>
                                </div>
                                <div class="form-group">
                                    <label class="text-muted fw-semibold small mb-1">
                                        <i class="ph-buildings me-1"></i>
                                        Pelaksana Serah
                                    </label>
                                    <div class="fw-semibold text-dark">{{ $letter->PENERBIT_ID }} | {{ $letter->NAME_PENERBIT }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="ph-package me-1 text-primary"></i>
                        <h6 class="mb-0 fw-semibold">Daftar Koleksi Pengiriman</h6>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary">
                        <i class="ph-list-checks me-1"></i>
                        Verifikasi Item
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
                                <th class="text-center text-nowrap" rowspan="2" style="width: 80px">
                                    <i class="ph-check-square"></i>
                                    Check
                                </th>
                                <th class="text-center text-nowrap" rowspan="2" style="width: 100px">
                                    <i class="ph-image"></i>
                                    Cover
                                </th>
                                <th class="text-nowrap" rowspan="2" style="min-width: 200px">
                                    <i class="ph-book-open me-1"></i>
                                    Judul
                                </th>
                                <th class="text-nowrap" rowspan="2" style="min-width: 130px">
                                    <i class="ph-identification-card me-1"></i>
                                    ISBN
                                </th>
                                <th class="text-nowrap" rowspan="2" style="min-width: 100px">
                                    <i class="ph-books me-1"></i>
                                    Jilid
                                </th>
                                <th class="text-nowrap" rowspan="2" style="min-width: 100px">
                                    <i class="ph-note me-1"></i>
                                    Edisi
                                </th>
                                <th class="text-center text-nowrap" colspan="2">
                                    <i class="ph-stack me-1"></i>
                                    Total
                                </th>
                                <th class="text-center text-nowrap" colspan="2">
                                    <i class="ph-package me-1"></i>
                                    Jumlah Eks
                                </th>
                                <th class="text-nowrap" rowspan="2" style="min-width: 150px">
                                    <i class="ph-x-circle me-1"></i>
                                    Alasan Ditolak
                                </th>
                                <th class="text-nowrap" rowspan="2" style="min-width: 150px">
                                    <i class="ph-note-pencil me-1"></i>
                                    Catatan
                                </th>
                                <th class="text-center text-nowrap" rowspan="2" style="width: 100px">
                                    <i class="ph-gear me-1"></i>
                                    Aksi
                                </th>
                            </tr>
                            <tr>
                                <th class="text-center text-nowrap" style="min-width: 100px">
                                    <i class="ph-database me-1"></i>
                                    Disistem
                                </th>
                                <th class="text-center text-nowrap" style="min-width: 100px">
                                    <i class="ph-paper-plane-tilt me-1"></i>
                                    Dikirim
                                </th>
                                <th class="text-center text-nowrap" style="min-width: 100px">
                                    <i class="ph-check-circle me-1"></i>
                                    Diterima
                                </th>
                                <th class="text-center text-nowrap" style="min-width: 100px">
                                    <i class="ph-x-circle me-1"></i>
                                    Ditolak
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div class="d-flex align-items-center">
                        <i class="ph-info text-primary me-1" style="font-size: 1.5rem;"></i>
                        <div>
                            <div class="fw-semibold">Simpan Hasil Verifikasi</div>
                            <small class="text-muted">Pastikan semua data sudah dicek dengan benar</small>
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-md-row gap-2 align-items-stretch align-items-md-center">
                        <div class="input-group" style="min-width: 250px;">
                            <span class="input-group-text">
                                <i class="ph-flag me-1"></i>
                                Status
                            </span>
                            <select class="form-select" name="status" id="status">
                                <option value="CEK FISIK" {{ ($letter->STATUS == 'CEK FISIK' || $letter->STATUS == 'TERKIRIM') ? 'selected' : '' }}>Cek Fisik</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-success text-nowrap" onclick="submitted()">
                            <i class="ph-check-circle me-1"></i>
                            Simpan Hasil Verifikasi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(function() {
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
            destroy: true,
            order: [0, 'asc'],
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.childRowImmediate,
                    renderer: function (api, rowIdx, columns) {
                        let data = columns.map((col, i) => {
                            let isLast = (i === columns.length - 1);

                            if (col.hidden) {
                                if(isLast) {
                                    return `
                                        <div class="form-group">
                                            <div class="text-end">${col.data}</div>
                                        </div>
                                    `;
                                } else {
                                    return `
                                        <div class="form-group row">
                                            <label class="col-form-label col-lg-3 fw-semibold">${col.title}</label>
                                            <div class="col-lg-9">
                                                ${col.data}
                                            </div>
                                        </div>
                                    `
                                }
                            } else {
                                return '';
                            }
                        }).join('');

                        return '<div class="mt-2 p-3 bg-light rounded">' + data + '</div>';
                    }
                }
            },
            ajax: {
                url: '{{ url("physical-delivery/delivery-verification/datatable-collection") }}',
                dataType: 'JSON',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    letter_id: '{{ $letter->LETTER_ID }}'
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
                { orderable: true, className: 'align-middle text-center fw-semibold', responsivePriority: 1 },
                { orderable: false, className: 'align-middle text-center', responsivePriority: 1 },
                { orderable: true, className: 'align-middle text-center', responsivePriority: 1 },
                { orderable: true, className: 'align-middle text-wrap', responsivePriority: 1 },
                { orderable: true, className: 'align-middle', responsivePriority: 1 },
                { orderable: true, className: 'align-middle text-wrap', responsivePriority: 1 },
                { orderable: true, className: 'align-middle text-wrap', responsivePriority: 1 },
                { orderable: true, className: 'align-middle text-center', responsivePriority: 2 },
                { orderable: false, className: 'align-middle text-center', responsivePriority: 2 },
                { orderable: false, className: 'align-middle text-center', responsivePriority: 2 },
                { orderable: false, className: 'align-middle text-center', responsivePriority: 2 },
                { orderable: false, className: 'align-middle none' },
                { orderable: false, className: 'align-middle none' },
                { orderable: false, className: 'align-middle text-center none' },
            ],
            initComplete: function (settings, json) {
                var table = this.api();
                const searchInput = $('div.dataTables_filter input');

                searchInput.off().unbind();

                searchInput.on('keyup', debounce(function () {
                    table.search(this.value).draw();
                }, 500));
            },
            drawCallback: function() {
                select2ServersideTag('.remark-field', 'problem', {}, {
                    minimumInputLength: 0,
                });

                onLoading('close', '#datatable-serverside_wrapper');
            },
        }).on('draw.dt', function() {
            onLoading('close', '#datatable-serverside_wrapper');
        }).on('responsive-display', function (e, datatable, row, showHide, update) {
            if (showHide) {
                select2ServersideTag('.remark-field', 'problem', {}, {
                    minimumInputLength: 0,
                });
            }
        });

        window.gDataTable.columns.adjust().draw();
    }

    function checkedAction(id, param, verif = 1) {
        var checkChecked = $('.checkbox-' + param).is(':checked');

        $.ajax({
            url: '{{ url("physical-delivery/delivery-verification/checked-action") }}',
            type: 'POST',
            dataType: 'JSON',
            data: {
                letter_detail_id: id,
                qty_accept: $('.total-accept-' + param).val(),
                qty_reject: $('.total-reject-' + param).val(),
                isbn_status: $('.note-' + param).val(),
                checked: checkChecked ? 1 : 0,
                verif: verif,
                remark: $('.remark-' + param).find(':selected').map(function() {
                    return $(this).val();
                }).get(),
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            beforeSend: function() {
                onLoading('show', '#datatable-serverside_wrapper');
            },
            success: function(response) {
                if(response.code == 200) {
                    if(verif == 1) {
                        onReloadTable();
                    } else {
                        onLoading('close', '#datatable-serverside_wrapper');
                    }

                    notification('success', response.message);
                } else {
                    onLoading('close', '#datatable-serverside_wrapper');

                    if(checkChecked) {
                        $('.checkbox-' + param).prop('checked', false);
                    } else {
                        $('.checkbox-' + param).prop('checked', true);
                    }

                    swalInit.fire({
                        title: response.code == 500 ? 'Error' : 'Oops ...',
                        text: response.message,
                        icon: response.code == 500 ? 'error' : 'info',
                        showCloseButton: true
                    });

                    if(response.code == 403) {
                        onReloadTable();
                    }
                }
            },
            error: function(response) {
                onLoading('close', '#datatable-serverside_wrapper');
                responseError(response);

                var checkChecked = $('.checkbox-' + param).is(':checked');

                if(checkChecked) {
                    $('.checkbox-' + param).prop('checked', false);
                } else {
                    $('.checkbox-' + param).prop('checked', true);
                }
            }
        });
    }

    function calculateQty(param, from) {
        var total = $('.total-copy-' + param).val();
        var accept = $('.total-accept-' + param).val();
        var reject = $('.total-reject-' + param).val();

        var accpetValue = parseInt(total) - parseInt(reject);
        var rejectValue = parseInt(total) - parseInt(accept);

        if(from == 'accept') {
            $('.total-reject-' + param).val(rejectValue ?? 0);
        } else if(from == 'reject') {
            $('.total-accept-' + param).val(accpetValue ?? 0);
        }
    }

    function submitted() {
        $.ajax({
            url: '{{ url("physical-delivery/delivery-verification/detail/" . $letter->LETTER_ID) }}',
            type: 'POST',
            dataType: 'JSON',
            data: $('#form-data').serialize(),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            beforeSend: function() {
                onLoading('show', 'body');
            },
            success: function(response) {
                onLoading('close', 'body');

                if(response.code == 200) {
                    swalInit.fire({
                        title: 'Berhasil',
                        text: response.message,
                        icon: 'success',
                        showDenyButton: false,
                        showCancelButton: false,
                        confirmButtonText: 'Oke',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            onLoading('show', 'body');

                            location.href = '{{ url("physical-delivery/delivery-verification") }}';
                        }
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
                onLoading('close', 'body');
                responseError(response);
            }
        });
    }
</script>
