<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Laporan - <span class="fw-normal">Ongkir</span>
            </h4>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="card" id="card-data">
        <div class="card-body">
            <div class="input-group">
                <span class="input-group-text">Tanggal</span>
                <input type="text" class="form-control" name="date" id="date" value="{{ date('Y/m/01') }} - {{ date('Y/m/t') }}" placeholder="Pilih Tanggal" readonly>
                <button type="button" class="btn btn-success" onclick="downloadExcel()">
                    <i class="ph-microsoft-excel-logo me-1"></i>
                    Download
                </button>
            </div>
            <div><hr></div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100">
                    <thead class="text-bg-light">
                        <tr>
                            <th class="text-nowrap">Provinsi</th>
                            <th class="text-nowrap">Total Berat Paket</th>
                            <th class="text-nowrap">Ongkir Min</th>
                            <th class="text-nowrap">Ongkir Maks</th>
                            <th class="text-nowrap">Ongkir Rata - Rata</th>
                            <th class="text-nowrap">Jumlah Paket</th>
                        </tr>
                    </thead>
                    <tbody id="table-data"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        datePickerBasic('#date');

        $('#date').on('apply.daterangepicker', function (e, picker) {
            loadData();
        });

        loadData();
        notifSuccessFromSession();
    });

    function notifSuccessFromSession() {
        var notif = '{{ session("success") }}';

        if(notif) {
            swalInit.fire('Berhasil', notif, 'success');
        }
    }

    function downloadExcel() {
        var queryString = {
            exported: true,
            date: $('#date').val()
        }

        onLoading('show', 'body');

        location.href = '{{ url("report/postage?") }}' + $.param(queryString);
    }

    function loadData() {
        $.ajax({
            url: '{{ url("report/postage/load-data") }}',
            type: 'GET',
            dataType: 'JSON',
            data: {
                date: $('#date').val()
            },
            beforeSend: function() {
                onLoading('show', '#card-data');

                $('#table-data').html('');
            },
            success: function(response) {
                $.each(response, function(i, val) {
                    $('#table-data').append(`
                        <tr>
                            <td>${val.PROVINCE}</td>
                            <td>${$.number(val.WEIGHT, 2)} kg</td>
                            <td>Rp ${$.number(val.POSTAGE_MIN, 2)}</td>
                            <td>Rp ${$.number(val.POSTAGE_MAX, 2)}</td>
                            <td>Rp ${$.number(val.POSTAGE_AVG, 2)}</td>
                            <td>${val.PACKAGE}</td>
                        </tr>
                    `);
                });

                onLoading('close', '#card-data');
            },
            error: function(response) {
                onLoading('close', '#card-data');
                responseError(response);
            }
        });
    }
</script>
