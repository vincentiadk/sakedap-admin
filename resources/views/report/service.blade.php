<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Laporan - <span class="fw-normal">Pelayanan</span>
            </h4>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="card" id="card-data">
        <div class="card-body">
            <div class="input-group">
                <span class="input-group-text">Tahun</span>
                <select class="form-select" name="year" id="year" onchange="loadData()">
                    @for($i = 2019; $i <= date('Y'); $i++)
                        <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
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
                            <th class="text-nowrap">Bulan</th>
                            <th class="text-nowrap text-center">Datang Langsung</th>
                            <th class="text-nowrap text-center">Unggah Mandiri</th>
                            <th class="text-nowrap text-center">Via Pengiriman</th>
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
            year: $('#year').val()
        }

        onLoading('show', 'body');

        location.href = '{{ url("report/service?") }}' + $.param(queryString);
    }

    function loadData() {
        $.ajax({
            url: '{{ url("report/service/load-data") }}',
            type: 'GET',
            dataType: 'JSON',
            data: {
                year: $('#year').val()
            },
            beforeSend: function() {
                onLoading('show', '#card-data');

                $('#table-data').html('');
            },
            success: function(response) {
                $.each(response, function(i, val) {
                    var dataTD = '';

                    $.each(val.data, function(index, value) {
                        dataTD += `
                            <td class="text-center text-nowrap">${ value }</td>
                        `;
                    });

                    $('#table-data').append(`
                        <tr>
                            <td class="text-nowrap">${ val.name }</td>
                            ${ dataTD }
                        </tr>
                    `);
                });

                onLoading('close', '#card-data');
            },
            error: function(response) {
                onLoading('close', '#card-data');

                swalInit.fire({
                    html: '<b>' + response.responseJSON.exception + '</b><br>' + response.responseJSON.message,
                    icon: 'error',
                    showCloseButton: false
                });
            }
        });
    }
</script>
