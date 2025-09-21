<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Laporan - <span class="fw-normal">Teguran</span>
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
                            <th class="text-nowrap">Pelaksana Serah</th>
                            @for($i = 1; $i <= 12; $i++)
                                <th class="text-center text-nowrap">{{ Carbon::parse(date('Y') . '-' . sprintf('%02s', $i))->isoFormat('MMMM') }}</th>
                            @endfor
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

        location.href = '{{ url("report/warning?") }}' + $.param(queryString);
    }

    function loadData() {
        $.ajax({
            url: '{{ url("report/warning/load-data") }}',
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
                if(response.length > 0) {
                    $.each(response, function(i, val) {
                        $('#table-data').append(`
                            <tr>
                                <td class="text-wrap">${val.NAME}</td>
                                <td class="text-center">${val.MONTH_1}</td>
                                <td class="text-center">${val.MONTH_2}</td>
                                <td class="text-center">${val.MONTH_3}</td>
                                <td class="text-center">${val.MONTH_4}</td>
                                <td class="text-center">${val.MONTH_5}</td>
                                <td class="text-center">${val.MONTH_6}</td>
                                <td class="text-center">${val.MONTH_7}</td>
                                <td class="text-center">${val.MONTH_8}</td>
                                <td class="text-center">${val.MONTH_9}</td>
                                <td class="text-center">${val.MONTH_10}</td>
                                <td class="text-center">${val.MONTH_11}</td>
                                <td class="text-center">${val.MONTH_12}</td>
                            </tr>
                        `);
                    });
                } else {
                    $('#table-data').html('<tr><td colspan="13" class="text-center">Tidak ada data</td></tr>');
                }

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
