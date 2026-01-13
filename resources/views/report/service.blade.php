<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Laporan - <span class="fw-normal">Pelayanan</span>
            </h4>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="card border-0 shadow-sm" id="card-data">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <i class="ph-chart-bar me-1 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Data Pelayanan</h6>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <label class="form-label mb-0 me-2 fw-semibold text-nowrap">
                        <i class="ph-calendar me-1"></i>
                        Tahun:
                    </label>
                    <select class="form-select" style="min-width: 120px;" name="year" id="year" onchange="loadData()">
                        @for($i = 2019; $i <= date('Y'); $i++)
                            <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                    <button type="button" class="btn btn-success text-nowrap" onclick="downloadExcel()">
                        <i class="ph-microsoft-excel-logo me-1"></i>
                        Download Excel
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered w-100">
                    <thead class="table-light">
                        <tr>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-calendar-blank me-1"></i>
                                Bulan
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 150px">
                                <i class="ph-user-focus me-1"></i>
                                Datang Langsung
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 150px">
                                <i class="ph-upload me-1"></i>
                                Unggah Mandiri
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 150px">
                                <i class="ph-truck me-1"></i>
                                Via Pengiriman
                            </th>
                        </tr>
                    </thead>
                    <tbody id="table-data">
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="ph-spinner spinner me-2"></i>
                                Memuat data...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer border-top bg-light">
            <div class="d-flex align-items-center text-muted">
                <i class="ph-info me-2"></i>
                <small>Data pelayanan berdasarkan metode penerimaan per bulan</small>
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

                $('#table-data').html(`
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            <i class="ph-spinner spinner me-2"></i>
                            Memuat data...
                        </td>
                    </tr>
                `);
            },
            success: function(response) {
                $('#table-data').html('');

                if(response.length > 0) {
                    $.each(response, function(i, val) {
                        var dataTD = '';

                        $.each(val.data, function(index, value) {
                            dataTD += `
                                <td class="text-center text-nowrap fw-semibold">${ value }</td>
                            `;
                        });

                        $('#table-data').append(`
                            <tr>
                                <td class="text-nowrap">${ val.name }</td>
                                ${ dataTD }
                            </tr>
                        `);
                    });
                } else {
                    $('#table-data').html(`
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="ph-database me-2"></i>
                                Tidak ada data untuk tahun ini
                            </td>
                        </tr>
                    `);
                }

                onLoading('close', '#card-data');
            },
            error: function(response) {
                onLoading('close', '#card-data');

                $('#table-data').html(`
                    <tr>
                        <td colspan="4" class="text-center text-danger py-4">
                            <i class="ph-warning-circle me-2"></i>
                            Gagal memuat data
                        </td>
                    </tr>
                `);

                responseError(response);
            }
        });
    }
</script>
