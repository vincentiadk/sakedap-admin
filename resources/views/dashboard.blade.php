<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                <span class="fw-normal">Dashboard</span>
            </h4>
        </div>
        <div class="collapse d-lg-block my-lg-auto ms-lg-auto" id="page-header">
            <div class="d-sm-flex align-items-center mb-lg-0 ms-lg-3">
                <div class="d-inline-flex mt-3 mt-sm-0">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="ph-calendar me-1"></i>
                            Tanggal
                        </span>
                        <input type="text" class="form-control wmin-200" name="date" id="date" value="{{ date('Y/01/01') }} - {{ date('Y/m/d') }}" placeholder="Pilih Tanggal">
                    </div>
                    <button type="button" class="btn btn-light ms-2" onclick="loadAllStatistic()" title="Refresh Data">
                        <i class="ph-arrows-clockwise"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="row g-3">
        <div class="col-xl-3 col-sm-6">
            <div class="card card-body border-start border-primary border-3 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="flex-fill">
                        <div class="text-muted text-uppercase fs-sm fw-semibold mb-1">Karya Digital</div>
                        <h3 class="mb-0 fw-bold" id="summary-digital">
                            <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                        </h3>
                    </div>
                    <div class="ms-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                            <i class="ph-laptop ph-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card card-body border-start border-warning border-3 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="flex-fill">
                        <div class="text-muted text-uppercase fs-sm fw-semibold mb-1">Karya Analog</div>
                        <h3 class="mb-0 fw-bold" id="summary-analog">
                            <span class="spinner-border spinner-border-sm text-warning" role="status"></span>
                        </h3>
                    </div>
                    <div class="ms-3">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                            <i class="ph-film-strip ph-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card card-body border-start border-success border-3 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="flex-fill">
                        <div class="text-muted text-uppercase fs-sm fw-semibold mb-1">Karya Cetak</div>
                        <h3 class="mb-0 fw-bold" id="summary-printed">
                            <span class="spinner-border spinner-border-sm text-success" role="status"></span>
                        </h3>
                    </div>
                    <div class="ms-3">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                            <i class="ph-book-open ph-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card card-body border-start border-info border-3 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="flex-fill">
                        <div class="text-muted text-uppercase fs-sm fw-semibold mb-1">Total Koleksi</div>
                        <h3 class="mb-0 fw-bold" id="summary-total">
                            <span class="spinner-border spinner-border-sm text-info" role="status"></span>
                        </h3>
                    </div>
                    <div class="ms-3">
                        <div class="bg-info bg-opacity-10 text-info rounded-circle p-3">
                            <i class="ph-database ph-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="card shadow-sm" id="card-digital-work">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="flex-fill">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ph-laptop me-1 text-primary"></i>
                                Karya Digital
                            </h6>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chart-digital-work" class="position-relative" style="width:100%; height:400px;">
                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2 mb-0 fs-sm">Memuat grafik...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm" id="card-analog-work">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="flex-fill">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ph-film-strip me-1 text-warning"></i>
                                Karya Analog
                            </h6>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chart-analog-work" class="position-relative" style="width:100%; height:400px;">
                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                            <div class="spinner-border text-warning" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2 mb-0 fs-sm">Memuat grafik...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm" id="card-printed-work">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="flex-fill">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ph-book-open me-1 text-success"></i>
                                Karya Cetak
                            </h6>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chart-printed-work" class="position-relative" style="width:100%; height:400px;">
                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2 mb-0 fs-sm">Memuat grafik...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow-sm" id="card-collection-status">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="flex-fill">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ph-chart-bar me-1 text-secondary"></i>
                                Status Koleksi
                            </h6>
                        </div>
                        <span class="badge bg-secondary" id="badge-status-total">0 Item</span>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chart-collection-status" class="position-relative" style="width:100%; height:500px;">
                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                            <div class="spinner-border text-secondary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2 mb-0 fs-sm">Memuat grafik...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-sm" id="card-province">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="flex-fill">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ph-map-pin me-1 text-danger"></i>
                                Data Per Provinsi
                            </h6>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height:350px; overflow-y:auto;">
                        <table class="table table-hover table-sm mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="text-center" style="width: 40px;">No</th>
                                    <th>Provinsi</th>
                                    <th class="text-center" style="width: 80px;">Digital</th>
                                    <th class="text-center" style="width: 80px;">Analog</th>
                                    <th class="text-center" style="width: 80px;">Cetak</th>
                                    <th class="text-center" style="width: 80px;">Total</th>
                                </tr>
                            </thead>
                            <tbody id="data-province">
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="spinner-border text-muted" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="text-muted mt-2 mb-0 fs-sm">Memuat data...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm" id="card-activity">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="flex-fill">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ph-clock-counter-clockwise me-1"></i>
                                Aktivitas Terbaru
                            </h6>
                        </div>
                        <span class="badge bg-dark">10 Data Terakhir</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height:350px; overflow-y:auto;">
                        <table class="table table-hover table-xs mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="text-center" style="width: 40px;">No</th>
                                    <th style="width: 100px;">Aksi</th>
                                    <th>User</th>
                                    <th style="width: 100px;">Tanggal</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="data-activity">
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="spinner-border text-muted" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="text-muted mt-2 mb-0 fs-sm">Memuat aktivitas...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-sm" id="card-collection">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="flex-fill">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ph-chart-line me-1 text-primary"></i>
                                Koleksi
                            </h6>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chart-collection" class="position-relative" style="width:100%; height:500px;">
                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2 mb-0 fs-sm">Memuat grafik...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm" id="card-type">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="flex-fill">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ph-chart-pie me-1 text-success"></i>
                                Jenis Bahan
                            </h6>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chart-type" class="position-relative" style="width:100%; height:500px;">
                        <div class="position-absolute top-50 start-50 translate-middle text-center">
                            <div class="spinner-border text-success" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2 mb-0 fs-sm">Memuat grafik...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        datePickerBasic('#date');

        $('#date').on('apply.daterangepicker', function (e, picker) {
            picker.element.val(picker.startDate.format(picker.locale.format) + " - " + picker.endDate.format(picker.locale.format));
            loadAllStatistic();
        });

        loadAllStatistic();
    });

    function loadAllStatistic() {
        chartDigitalWork();
        chartAnalogWork();
        chartPrintedWork();
        chartCollectionStatus();
        dataProvince();
        dataActivity();
        chartCollection();
        chartType();
    }

    function dataProvince() {
        $.ajax({
            url: '{{ url("dashboard/data-province") }}',
            type: 'GET',
            dataType: 'JSON',
            data: {
                date: $('#date').val()
            },
            beforeSend: function() {
                $('#data-province').html(`
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="spinner-border text-muted" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2 mb-0 fs-sm">Memuat data...</p>
                        </td>
                    </tr>
                `);

                onLoading('show', '#card-province');
            },
            success: function(response) {
                if(response.length > 0) {
                    var html = '';

                    $.each(response, function(i, val) {
                        var nomor = i + 1;
                        var name = val.NAMAPROPINSI;
                        var totalDigital = parseInt(val.TOTAL_DIGITAL);
                        var totalAnalog = parseInt(val.TOTAL_ANALOG);
                        var totalPrinted = parseInt(val.TOTAL_PRINTED);
                        var total = totalDigital + totalAnalog + totalPrinted;

                        html += `
                            <tr>
                                <td class="text-center fw-semibold text-muted">${nomor}</td>
                                <td class="fw-semibold">${name}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary">${totalDigital}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning">${totalAnalog}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success">${totalPrinted}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">${total}</span>
                                </td>
                            </tr>
                        `;
                    });

                    $('#data-province').html(html);
                } else {
                    $('#data-province').html(`
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="ph-map-pin ph-3x opacity-25 mb-3"></i>
                                <p class="mb-0 fw-semibold">Tidak ada data</p>
                                <p class="fs-sm mb-0">Data provinsi belum tersedia</p>
                            </td>
                        </tr>
                    `);
                }

                onLoading('close', '#card-province');
            },
            error: function(response) {
                $('#data-province').html(`
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="alert alert-danger border-0 mb-0">
                                <i class="ph-warning-circle me-1"></i>
                                Gagal memuat data provinsi
                            </div>
                        </td>
                    </tr>
                `);

                onLoading('close', '#card-province');
                responseError(response);
            }
        });
    }

    function dataActivity() {
        $.ajax({
            url: '{{ url("dashboard/data-activity") }}',
            type: 'GET',
            dataType: 'JSON',
            beforeSend: function() {
                $('#data-activity').html(`
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="spinner-border text-muted" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="text-muted mt-2 mb-0 fs-sm">Memuat aktivitas...</p>
                        </td>
                    </tr>
                `);

                onLoading('show', '#card-activity');
            },
            success: function(response) {
                if(response.length > 0) {
                    var html = '';

                    $.each(response, function(i, val) {
                        var nomor = i + 1;
                        var action = val.ACTION || '-';
                        var user = val.ACTIONBY || '-';
                        var date = val.ACTIONDATE ? moment(val.ACTIONDATE).format('DD/MM/YYYY') : '-';
                        var description = val.NOTE || '-';
                        var actionBadge = '';
                        var actionLower = action.toLowerCase();

                        if(actionLower.includes('create') || actionLower.includes('tambah')) {
                            actionBadge = '<span class="badge bg-success">' + action + '</span>';
                        } else if(actionLower.includes('update') || actionLower.includes('edit')) {
                            actionBadge = '<span class="badge bg-primary">' + action + '</span>';
                        } else if(actionLower.includes('delete') || actionLower.includes('hapus')) {
                            actionBadge = '<span class="badge bg-danger">' + action + '</span>';
                        } else {
                            actionBadge = '<span class="badge bg-secondary">' + action + '</span>';
                        }

                        html += `
                            <tr>
                                <td class="text-center fw-semibold text-muted">${nomor}</td>
                                <td>${actionBadge}</td>
                                <td class="fw-semibold">${user}</td>
                                <td>
                                    <span class="text-muted fs-sm">
                                        <i class="ph-clock me-1"></i>${date}
                                    </span>
                                </td>
                                <td class="text-muted">${description}</td>
                            </tr>
                        `;
                    });

                    $('#data-activity').html(html);
                } else {
                    $('#data-activity').html(`
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="ph-clipboard-text ph-3x opacity-25 mb-3"></i>
                                <p class="mb-0 fw-semibold">Tidak ada aktivitas</p>
                                <p class="fs-sm mb-0">Belum ada aktivitas yang tercatat</p>
                            </td>
                        </tr>
                    `);
                }

                onLoading('close', '#card-activity');
            },
            error: function(response) {
                $('#data-activity').html(`
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="alert alert-danger border-0 mb-0">
                                <i class="ph-warning-circle me-1"></i>
                                Gagal memuat data aktivitas
                            </div>
                        </td>
                    </tr>
                `);

                onLoading('close', '#card-activity');
                responseError(response);
            }
        });
    }

    function chartDigitalWork() {
        $.ajax({
            url: '{{ url("dashboard/data-digital-work") }}',
            type: 'GET',
            dataType: 'JSON',
            data: {
                date: $('#date').val()
            },
            beforeSend: function() {
                onLoading('show', '#card-digital-work');

                $('#summary-digital').html('<span class="spinner-border spinner-border-sm text-primary" role="status"></span>');
            },
            success: function(response) {
                var total = 0;
                var validData = [];

                if(response && Array.isArray(response)) {
                    response.forEach(function(item) {
                        if(item && item.value && item.value > 0) {
                            total += parseInt(item.value);
                            validData.push(item);
                        }
                    });
                }

                $('#summary-digital').text(total.toLocaleString('id-ID'));

                if(validData.length === 0 || total === 0) {
                    $('#chart-digital-work').html(`
                        <div class="d-flex flex-column align-items-center justify-content-center" style="height: 400px;">
                            <i class="ph-laptop ph-3x text-muted opacity-25 mb-3"></i>
                            <p class="text-muted mb-1 fw-semibold">Tidak ada data</p>
                            <p class="text-muted fs-sm mb-0">Data karya digital belum tersedia</p>
                        </div>
                    `);

                    onLoading('close', '#card-digital-work');

                    return;
                }

                var chartSelector = document.getElementById('chart-digital-work');
                var existingChart = echarts.getInstanceByDom(chartSelector);

                if(existingChart) {
                    existingChart.dispose();
                }

                var chart = echarts.init(chartSelector, null, {
                    renderer: 'canvas'
                });

                var option = {
                    tooltip: {
                        trigger: 'item',
                        formatter: '{b}: {c} ({d}%)'
                    },
                    legend: {
                        orient: 'horizontal',
                        bottom: '5',
                        left: 'center',
                        textStyle: {
                            fontSize: 11
                        }
                    },
                    series: [
                        {
                            type: 'pie',
                            smooth: true,
                            radius: ['40%', '70%'],
                            center: ['50%', '45%'],
                            data: validData,
                            label: {
                                show: true,
                                position: 'outside',
                                formatter: '{c}',
                                fontSize: 12,
                                fontWeight: 'bold'
                            },
                            labelLine: {
                                show: true,
                                length: 15,
                                length2: 10
                            },
                            itemStyle: {
                                borderRadius: 8,
                                borderColor: '#fff',
                                borderWidth: 3
                            },
                            emphasis: {
                                itemStyle: {
                                    shadowBlur: 10,
                                    shadowOffsetX: 0,
                                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                                },
                                scale: true,
                                scaleSize: 10
                            }
                        }
                    ]
                };

                option && chart.setOption(option);

                window.addEventListener('resize', function() {
                    chart.resize();
                });

                onLoading('close', '#card-digital-work');
            },
            error: function(response) {
                $('#chart-digital-work').html(`
                    <div class="alert alert-danger border-0 mb-0 mx-3">
                        <i class="ph-warning-circle me-1"></i>
                        Gagal memuat grafik
                    </div>
                `);

                onLoading('close', '#card-digital-work');
                responseError(response);
            }
        });
    }

    function chartAnalogWork() {
        $.ajax({
            url: '{{ url("dashboard/data-analog-work") }}',
            type: 'GET',
            dataType: 'JSON',
            data: {
                date: $('#date').val()
            },
            beforeSend: function() {
                onLoading('show', '#card-analog-work');

                $('#summary-analog').html('<span class="spinner-border spinner-border-sm text-warning" role="status"></span>');
            },
            success: function(response) {
                var total = 0;
                var validData = [];

                if(response && Array.isArray(response)) {
                    response.forEach(function(item) {
                        if(item && item.value && item.value > 0) {
                            total += parseInt(item.value);
                            validData.push(item);
                        }
                    });
                }

                $('#summary-analog').text(total.toLocaleString('id-ID'));

                if(validData.length === 0 || total === 0) {
                    $('#chart-analog-work').html(`
                        <div class="d-flex flex-column align-items-center justify-content-center" style="height: 400px;">
                            <i class="ph-film-strip ph-3x text-muted opacity-25 mb-3"></i>
                            <p class="text-muted mb-1 fw-semibold">Tidak ada data</p>
                            <p class="text-muted fs-sm mb-0">Data karya analog belum tersedia</p>
                        </div>
                    `);

                    onLoading('close', '#card-analog-work');

                    return;
                }

                var chartSelector = document.getElementById('chart-analog-work');
                var existingChart = echarts.getInstanceByDom(chartSelector);

                if(existingChart) {
                    existingChart.dispose();
                }

                var chart = echarts.init(chartSelector, null, {
                    renderer: 'canvas'
                });

                var option = {
                    tooltip: {
                        trigger: 'item',
                        formatter: '{b}: {c} ({d}%)'
                    },
                    legend: {
                        orient: 'horizontal',
                        bottom: '5',
                        left: 'center',
                        textStyle: {
                            fontSize: 11
                        }
                    },
                    series: [
                        {
                            type: 'pie',
                            smooth: true,
                            radius: ['40%', '70%'],
                            center: ['50%', '45%'],
                            data: validData,
                            label: {
                                show: true,
                                position: 'outside',
                                formatter: '{c}',
                                fontSize: 12,
                                fontWeight: 'bold'
                            },
                            labelLine: {
                                show: true,
                                length: 15,
                                length2: 10
                            },
                            itemStyle: {
                                borderRadius: 8,
                                borderColor: '#fff',
                                borderWidth: 3
                            },
                            emphasis: {
                                itemStyle: {
                                    shadowBlur: 10,
                                    shadowOffsetX: 0,
                                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                                },
                                scale: true,
                                scaleSize: 10
                            }
                        }
                    ]
                };

                option && chart.setOption(option);

                window.addEventListener('resize', function() {
                    chart.resize();
                });

                onLoading('close', '#card-analog-work');
            },
            error: function(response) {
                $('#chart-analog-work').html(`
                    <div class="alert alert-danger border-0 mb-0 mx-3">
                        <i class="ph-warning-circle me-1"></i>
                        Gagal memuat grafik
                    </div>
                `);

                onLoading('close', '#card-analog-work');
                responseError(response);
            }
        });
    }

    function chartPrintedWork() {
        $.ajax({
            url: '{{ url("dashboard/data-printed-work") }}',
            type: 'GET',
            dataType: 'JSON',
            data: {
                date: $('#date').val()
            },
            beforeSend: function() {
                onLoading('show', '#card-printed-work');

                $('#summary-printed').html('<span class="spinner-border spinner-border-sm text-success" role="status"></span>');
            },
            success: function(response) {
                var total = 0;
                var validData = [];

                if(response && Array.isArray(response)) {
                    response.forEach(function(item) {
                        if(item && item.value && item.value > 0) {
                            total += parseInt(item.value);
                            validData.push(item);
                        }
                    });
                }

                $('#summary-printed').text(total.toLocaleString('id-ID'));

                if(validData.length === 0 || total === 0) {
                    $('#chart-printed-work').html(`
                        <div class="d-flex flex-column align-items-center justify-content-center" style="height: 400px;">
                            <i class="ph-book-open ph-3x text-muted opacity-25 mb-3"></i>
                            <p class="text-muted mb-1 fw-semibold">Tidak ada data</p>
                            <p class="text-muted fs-sm mb-0">Data karya cetak belum tersedia</p>
                        </div>
                    `);

                    onLoading('close', '#card-printed-work');

                    return;
                }

                var chartSelector = document.getElementById('chart-printed-work');
                var existingChart = echarts.getInstanceByDom(chartSelector);

                if(existingChart) {
                    existingChart.dispose();
                }

                var chart = echarts.init(chartSelector, null, {
                    renderer: 'canvas'
                });

                var option = {
                    tooltip: {
                        trigger: 'item',
                        formatter: '{b}: {c} ({d}%)'
                    },
                    legend: {
                        orient: 'horizontal',
                        bottom: '5',
                        left: 'center',
                        textStyle: {
                            fontSize: 11
                        }
                    },
                    series: [
                        {
                            type: 'pie',
                            smooth: true,
                            radius: ['40%', '70%'],
                            center: ['50%', '45%'],
                            data: validData,
                            label: {
                                show: true,
                                position: 'outside',
                                formatter: '{c}',
                                fontSize: 12,
                                fontWeight: 'bold'
                            },
                            labelLine: {
                                show: true,
                                length: 15,
                                length2: 10
                            },
                            itemStyle: {
                                borderRadius: 8,
                                borderColor: '#fff',
                                borderWidth: 3
                            },
                            emphasis: {
                                itemStyle: {
                                    shadowBlur: 10,
                                    shadowOffsetX: 0,
                                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                                },
                                scale: true,
                                scaleSize: 10
                            }
                        }
                    ]
                };

                option && chart.setOption(option);

                window.addEventListener('resize', function() {
                    chart.resize();
                });

                onLoading('close', '#card-printed-work');
            },
            error: function(response) {
                $('#chart-printed-work').html(`
                    <div class="alert alert-danger border-0 mb-0 mx-3">
                        <i class="ph-warning-circle me-1"></i>
                        Gagal memuat grafik
                    </div>
                `);

                onLoading('close', '#card-printed-work');
                responseError(response);
            }
        });
    }

    function chartCollection() {
        $.ajax({
            url: '{{ url("dashboard/data-collection") }}',
            type: 'GET',
            dataType: 'JSON',
            data: {
                date: $('#date').val()
            },
            beforeSend: function() {
                onLoading('show', '#card-collection');

                $('#summary-total').html('<span class="spinner-border spinner-border-sm text-primary" role="status"></span>');
            },
            success: function(response) {
                var total = 0;

                if(response && response.data && Array.isArray(response.data)) {
                    response.data.forEach(function(val) {
                        total += parseInt(val) || 0;
                    });
                }

                $('#summary-total').text(total.toLocaleString('id-ID'));

                if(!response || !response.data || total === 0) {
                    $('#chart-collection').html(`
                        <div class="d-flex flex-column align-items-center justify-content-center" style="height: 500px;">
                            <i class="ph-chart-line ph-3x text-muted opacity-25 mb-3"></i>
                            <p class="text-muted mb-1 fw-semibold">Tidak ada data</p>
                            <p class="text-muted fs-sm mb-0">Data koleksi belum tersedia</p>
                        </div>
                    `);

                    onLoading('close', '#card-collection');

                    return;
                }

                var chartSelector = document.getElementById('chart-collection');
                var existingChart = echarts.getInstanceByDom(chartSelector);

                if(existingChart) {
                    existingChart.dispose();
                }

                var chart = echarts.init(chartSelector, null, {
                    renderer: 'canvas'
                });

                var option = {
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: {
                            type: 'shadow'
                        }
                    },
                    grid: {
                        left: '3%',
                        right: '4%',
                        top: '10%',
                        bottom: '3%',
                        containLabel: true
                    },
                    xAxis: [
                        {
                            type: 'category',
                            data: response.label,
                            axisTick: {
                                alignWithLabel: true
                            },
                            axisLine: {
                                lineStyle: {
                                    color: '#9CA3AF'
                                }
                            },
                            axisLabel: {
                                fontSize: 11
                            },
                            splitLine: {
                                show: true,
                                lineStyle: {
                                    color: '#E5E7EB'
                                }
                            }
                        }
                    ],
                    yAxis: [
                        {
                            type: 'value',
                            axisLine: {
                                show: true,
                                lineStyle: {
                                    color: '#9CA3AF'
                                }
                            },
                            splitLine: {
                                lineStyle: {
                                    color: '#E5E7EB'
                                }
                            },
                            splitArea: {
                                show: true,
                                areaStyle: {
                                    color: ['rgba(255, 255, 255, .01)', 'rgba(0, 0, 0, .01)']
                                }
                            }
                        }
                    ],
                    series: [
                        {
                            name: 'Total',
                            type: 'bar',
                            smooth: true,
                            barWidth: '60%',
                            itemStyle: {
                                borderRadius: [6, 6, 0, 0],
                                color: '#0d6efd'
                            },
                            label: {
                                show: true,
                                position: 'top',
                                fontSize: 11,
                                fontWeight: 'bold'
                            },
                            emphasis: {
                                itemStyle: {
                                    shadowBlur: 10,
                                    shadowOffsetX: 0,
                                    shadowColor: 'rgba(0, 0, 0, 0.3)'
                                }
                            },
                            data: response.data
                        }
                    ]
                };

                option && chart.setOption(option);

                window.addEventListener('resize', function() {
                    chart.resize();
                });

                onLoading('close', '#card-collection');
            },
            error: function(response) {
                $('#chart-collection').html(`
                    <div class="alert alert-danger border-0 mb-0 mx-3">
                        <i class="ph-warning-circle me-1"></i>
                        Gagal memuat grafik
                    </div>
                `);

                onLoading('close', '#card-collection');
                responseError(response);
            }
        });
    }

    function chartType() {
        $.ajax({
            url: '{{ url("dashboard/data-type") }}',
            type: 'GET',
            dataType: 'JSON',
            data: {
                date: $('#date').val()
            },
            beforeSend: function() {
                onLoading('show', '#card-type');
            },
            success: function(response) {
                var total = 0;
                var validData = [];

                if(response && Array.isArray(response)) {
                    response.forEach(function(item) {
                        if(item && item.value && item.value > 0) {
                            total += parseInt(item.value);
                            validData.push(item);
                        }
                    });
                }

                if(validData.length === 0 || total === 0) {
                    $('#chart-type').html(`
                        <div class="d-flex flex-column align-items-center justify-content-center" style="height: 500px;">
                            <i class="ph-chart-pie ph-3x text-muted opacity-25 mb-3"></i>
                            <p class="text-muted mb-1 fw-semibold">Tidak ada data</p>
                            <p class="text-muted fs-sm mb-0">Data jenis bahan belum tersedia</p>
                        </div>
                    `);

                    onLoading('close', '#card-type');

                    return;
                }

                var chartSelector = document.getElementById('chart-type');
                var existingChart = echarts.getInstanceByDom(chartSelector);

                if(existingChart) {
                    existingChart.dispose();
                }

                var chart = echarts.init(chartSelector, null, {
                    renderer: 'canvas'
                });

                var option = {
                    tooltip: {
                        trigger: 'item',
                        formatter: '{b}: {c} ({d}%)'
                    },
                    legend: {
                        orient: 'horizontal',
                        bottom: '5',
                        left: 'center',
                        textStyle: {
                            fontSize: 11
                        }
                    },
                    series: [
                        {
                            type: 'pie',
                            smooth: true,
                            radius: ['40%', '70%'],
                            center: ['50%', '45%'],
                            data: validData,
                            label: {
                                show: true,
                                position: 'outside',
                                formatter: '{c}',
                                fontSize: 12,
                                fontWeight: 'bold'
                            },
                            labelLine: {
                                show: true,
                                length: 15,
                                length2: 10
                            },
                            itemStyle: {
                                borderRadius: 8,
                                borderColor: '#fff',
                                borderWidth: 3
                            },
                            emphasis: {
                                itemStyle: {
                                    shadowBlur: 10,
                                    shadowOffsetX: 0,
                                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                                },
                                scale: true,
                                scaleSize: 10
                            }
                        }
                    ]
                };

                option && chart.setOption(option);

                window.addEventListener('resize', function() {
                    chart.resize();
                });

                onLoading('close', '#card-type');
            },
            error: function(response) {
                $('#chart-type').html(`
                    <div class="alert alert-danger border-0 mb-0 mx-3">
                        <i class="ph-warning-circle me-1"></i>
                        Gagal memuat grafik
                    </div>
                `);

                onLoading('close', '#card-type');
                responseError(response);
            }
        });
    }

    function chartCollectionStatus() {
        $.ajax({
            url: '{{ url("dashboard/data-collection-status") }}',
            type: 'GET',
            dataType: 'JSON',
            data: {
                date: $('#date').val()
            },
            beforeSend: function() {
                onLoading('show', '#card-collection-status');
            },
            success: function(response) {
                var total = 0;

                if(response && response.data && Array.isArray(response.data)) {
                    response.data.forEach(function(val) {
                        total += parseInt(val) || 0;
                    });
                }

                $('#badge-status-total').text(total + ' Item');

                if(!response || !response.data || total === 0) {
                    $('#chart-collection-status').html(`
                        <div class="d-flex flex-column align-items-center justify-content-center" style="height: 500px;">
                            <i class="ph-clipboard-text ph-3x text-muted opacity-25 mb-3"></i>
                            <p class="text-muted mb-1 fw-semibold">Tidak ada data</p>
                            <p class="text-muted fs-sm mb-0">Data status koleksi belum tersedia</p>
                        </div>
                    `);

                    onLoading('close', '#card-collection-status');

                    return;
                }

                var chartSelector = document.getElementById('chart-collection-status');
                var existingChart = echarts.getInstanceByDom(chartSelector);

                if(existingChart) {
                    existingChart.dispose();
                }

                var chart = echarts.init(chartSelector, null, {
                    renderer: 'canvas'
                });

                var option = {
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: {
                            type: 'shadow'
                        },
                        formatter: function(params) {
                            var item = params[0];
                            var percent = total > 0 ? ((item.value / total) * 100).toFixed(1) : 0;
                            return item.name + '<br/>' + item.marker + ' ' + item.value + ' (' + percent + '%)';
                        }
                    },
                    grid: {
                        left: '3%',
                        right: '4%',
                        top: '10%',
                        bottom: '3%',
                        containLabel: true
                    },
                    xAxis: [{
                        type: 'category',
                        data: response.label,
                        axisTick: {
                            alignWithLabel: true
                        },
                        axisLine: {
                            lineStyle: {
                                color: '#9CA3AF'
                            }
                        },
                        axisLabel: {
                            fontSize: 11
                        },
                        splitLine: {
                            show: true,
                            lineStyle: {
                                color: '#E5E7EB'
                            }
                        }
                    }],
                    yAxis: [{
                        type: 'value',
                        axisLine: {
                            show: true,
                            lineStyle: {
                                color: '#9CA3AF'
                            }
                        },
                        axisLabel: {
                            fontSize: 11
                        },
                        splitLine: {
                            lineStyle: {
                                color: '#E5E7EB',
                                type: 'dashed'
                            }
                        },
                        splitArea: {
                            show: true,
                            areaStyle: {
                                color: ['rgba(255, 255, 255, .01)', 'rgba(0, 0, 0, .01)']
                            }
                        }
                    }],
                    series: [{
                        name: 'Total',
                        type: 'bar',
                        smooth: true,
                        barWidth: '60%',
                        itemStyle: {
                            borderRadius: [6, 6, 0, 0],
                            color: function(params) {
                                var colorList = ['#fd7e14', '#198754', '#dc3545', '#d63384'];
                                return colorList[params.dataIndex] || '#6c757d';
                            }
                        },
                        label: {
                            show: true,
                            position: 'top',
                            fontSize: 11,
                            fontWeight: 'bold'
                        },
                        emphasis: {
                            itemStyle: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.3)'
                            }
                        },
                        data: response.data
                    }]
                };

                option && chart.setOption(option);

                window.addEventListener('resize', function() {
                    chart.resize();
                });

                onLoading('close', '#card-collection-status');
            },
            error: function(response) {
                $('#chart-collection-status').html(`
                    <div class="alert alert-danger border-0 mb-0 mx-3">
                        <i class="ph-warning-circle me-1"></i>
                        Gagal memuat grafik
                    </div>
                `);

                onLoading('close', '#card-collection-status');
                responseError(response);
            }
        });
    }
</script>
