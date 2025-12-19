<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pengiriman Fisik - Verifikasi Pengiriman - <span class="fw-normal">Detail</span>
            </h4>
        </div>
        <div class="collapse d-lg-block my-lg-auto ms-lg-auto" id="page-header">
            <div class="d-sm-flex align-items-center mb-3 mb-lg-0 ms-lg-3">
                <div class="d-inline-flex mt-3 mt-sm-0">
                    <a href="{{ url('physical-delivery/delivery-verification') }}" class="btn btn-primary">
                        <i class="ph-arrow-left me-1"></i>
                        Kembali ke Tabel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    <form id="form-data">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th class="table-success" width="20%">Tanggal</th>
                            <td width="30%">{{ Carbon::parse($letter->LETTER_DATE)->isoFormat('D MMM Y') }}, {{ Carbon::parse($letter->LETTER_DATE)->format('H:i') }}</td>
                            <th class="table-success" width="20%">No Surat</th>
                            <td width="30%">{{ $letter->LETTER_NUMBER }}</td>
                        </tr>
                        <tr>
                            <th class="table-success" width="20%">Pengirim</th>
                            <td width="30%">{{ $letter->SENDER }}</td>
                            <th class="table-success" width="20%">Telp</th>
                            <td width="30%">{{ $letter->PHONE }}</td>
                        </tr>
                        <tr>
                            <th class="table-success" width="20%">Jasa Kirim</th>
                            <td width="30%">{{ $letter->NAME_JASA_PENGIRIMAN }}</td>
                            <th class="table-success" width="20%">Tujuan</th>
                            <td width="30%">{{ $letter->NAME_BRANCH }}</td>
                        </tr>
                        <tr>
                            <th class="table-success" width="20%">Resi</th>
                            <td width="30%">{{ $letter->RECEIPT_NO }}</td>
                            <th class="table-success" width="20%">Biaya Kirim</th>
                            <td width="30%">Rp {{ number_format($letter->BIAYA_KIRIM ?: 0) }}</td>
                        </tr>
                        <tr>
                            <th class="table-success" width="20%">Berat</th>
                            <td width="30%">{{ number_format(($letter->BERAT ?: 0) / 1000, 2, ',', '.') }} Kg</td>
                            <th class="table-success" width="20%">Pelaksana Serah</th>
                            <td width="30%">{{ $letter->PENERBIT_ID }} | {{ $letter->NAME_PENERBIT }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered w-100 display nowrap" id="datatable-serverside">
                    <thead class="text-bg-light">
                        <tr>
                            <th class="text-center" rowspan="2">No</th>
                            <th class="text-center" rowspan="2">Check</th>
                            <th rowspan="2">Cover</th>
                            <th rowspan="2">Judul</th>
                            <th rowspan="2">ISBN</th>
                            <th rowspan="2">Jilid</th>
                            <th rowspan="2">Edisi</th>
                            <th colspan="2" class="text-center">Total</th>
                            <th colspan="2" class="text-center">Jumlah Eks</th>
                            <th rowspan="2">Alasan Ditolak</th>
                            <th rowspan="2">Catatan</th>
                            <th rowspan="2">Aksi</th>
                        </tr>
                        <tr>
                            <th class="text-center">Disistem</th>
                            <th class="text-center">Dikirim</th>
                            <th class="text-center">Diterima</th>
                            <th class="text-center">Ditolak</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="justify-content-end d-flex">
                    <div class="input-group me-2">
                        <span class="input-group-text">Status</span>
                        <select class="form-select wmin-200" name="status" id="status">
                            <option value="CEK FISIK" {{ ($letter->STATUS == 'CEK FISIK' || $letter->STATUS == 'TERKIRIM') ? 'selected' : '' }}>CEK FISIK</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-success text-nowrap" onclick="submitted()">
                        <i class="ph-check me-1"></i>
                        Simpan Hasil Verifikasi
                    </button>
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
                                            <label class="col-form-label col-lg-2">${col.title}</label>
                                            <div class="col-md-10">
                                                ${col.data}
                                            </div>
                                        </div>
                                    `
                                }
                            } else {
                                return '';
                            }
                        }).join('');

                        return '<div class="mt-2">' + data + '</div>';
                    }
                }
            },
            ajax: {
                url: '{{ url("physical-delivery/delivery-verification/datatable-collection") }}',
                dataType: 'JSON',
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
                { orderable: true, className: 'align-middle text-center', responsivePriority: 1 },
                { orderable: false, className: 'align-middle text-center', responsivePriority: 1 },
                { orderable: true, className: 'align-middle text-center', responsivePriority: 1 },
                { orderable: true, className: 'align-middle text-wrap', responsivePriority: 1 },
                { orderable: true, className: 'align-middle', responsivePriority: 1 },
                { orderable: true, className: 'align-middle text-wrap', responsivePriority: 1 },
                { orderable: true, className: 'align-middle text-wrap', responsivePriority: 1 },
                { orderable: true, className: 'align-middle', responsivePriority: 1 },
                { orderable: false, className: 'align-middle', responsivePriority: 1 },
                { orderable: false, className: 'align-middle', responsivePriority: 1 },
                { orderable: false, className: 'align-middle', responsivePriority: 1 },
                { orderable: false, className: 'align-middle none' },
                { orderable: false, className: 'align-middle none' },
                { orderable: false, className: 'align-middle none' },
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
        var selector = $(param).closest('tr');
        var total = selector.find('input[name="letter_detail_total"]').val();
        var accept = selector.find('select[name="letter_detail_qty_accept[]"]').val();
        var reject = selector.find('select[name="letter_detail_qty_reject[]"]').val();

        var accpetValue = parseInt(total) - parseInt(reject);
        var rejectValue = parseInt(total) - parseInt(accept);

        if(from == 'accept') {
            selector.find('select[name="letter_detail_qty_reject[]"]').val(rejectValue);
        } else if(from == 'reject') {
            selector.find('select[name="letter_detail_qty_accept[]"]').val(accpetValue);
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
