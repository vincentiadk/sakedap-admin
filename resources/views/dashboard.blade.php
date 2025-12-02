<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                <span class="fw-normal">Dashboard</span>
            </h4>
        </div>
        <div class="collapse d-lg-block my-lg-auto ms-lg-auto" id="page-header">
            <div class="d-sm-flex align-items-center mb-3 mb-lg-0 ms-lg-3">
                <div class="d-inline-flex mt-3 mt-sm-0">
                    <div class="input-group">
                        <span class="input-group-text">Tanggal</span>
                        <input type="text" class="form-control wmin-200" name="date" id="date" value="{{ date('Y/m/01') }} - {{ date('Y/m/d') }}" placeholder="Pilih Tanggal" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="row">
        <div class="col-md-4">
            <div class="card" id="card-digital-work">
                <div class="card-header">
                    <h5 class="hstack gap-2 mb-0">Karya Digital</h5>
                </div>
                <div class="card-body">
                    <div id="chart-digital-work" style="width:100%; height:400px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card" id="card-analog-work">
                <div class="card-header">
                    <h5 class="hstack gap-2 mb-0">Karya Analog</h5>
                </div>
                <div class="card-body">
                    <div id="chart-analog-work" style="width:100%; height:400px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card" id="card-printed-work">
                <div class="card-header">
                    <h5 class="hstack gap-2 mb-0">Karya Cetak</h5>
                </div>
                <div class="card-body">
                    <div id="chart-printed-work" style="width:100%; height:400px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card" id="card-collection-status">
                <div class="card-header">
                    <h5 class="hstack gap-2 mb-0">Status Koleksi</h5>
                </div>
                <div class="card-body">
                    <div id="chart-collection-status" style="width:100%; height:500px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card" id="card-province">
                <div class="card-header">
                    <h5 class="hstack gap-2 mb-0">Provinsi</h5>
                </div>
                <div class="card-body">
                    <div class="table-fix-header" style="height:300px;">
                        <table class="table table-sm">
                            <thead class="text-bg-light">
                                <tr>
                                    <th class="text-nowrap">No</th>
                                    <th class="text-nowrap">Provinsi</th>
                                    <th class="text-nowrap">Digital</th>
                                    <th class="text-nowrap">Analog</th>
                                    <th class="text-nowrap">Cetak</th>
                                    <th class="text-nowrap">Total</th>
                                </tr>
                            </thead>
                            <tbody id="data-province"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card" id="card-activity">
                <div class="card-header">
                    <h5 class="hstack gap-2 mb-0">10 Data Aktivitas User Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-fix-header" style="height:300px;">
                        <table class="table">
                            <thead class="text-bg-light">
                                <tr>
                                    <th class="text-nowrap">No</th>
                                    <th class="text-nowrap">Aksi</th>
                                    <th class="text-nowrap">User</th>
                                    <th class="text-nowrap">Tanggal</th>
                                    <th class="text-nowrap">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="data-activity"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card" id="card-collection">
                <div class="card-header">
                    <h5 class="hstack gap-2 mb-0">Koleksi</h5>
                </div>
                <div class="card-body">
                    <div id="chart-collection" style="width:100%; height:500px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card" id="card-type">
                <div class="card-header">
                    <h5 class="hstack gap-2 mb-0">Jenis Bahan</h5>
                </div>
                <div class="card-body">
                    <div id="chart-type" style="width:100%; height:500px;"></div>
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
                $('#data-province').html('');

                onLoading('show', '#card-province');
            },
            success: function(response) {
                if(response.length > 0) {
                    $.each(response, function(i, val) {
                        var nomor = i + 1;
                        var name = val.NAMAPROPINSI;
                        var totalDigital = parseInt(val.TOTAL_DIGITAL);
                        var totalAnalog = parseInt(val.TOTAL_ANALOG);
                        var totalPrinted = parseInt(val.TOTAL_PRINTED);
                        var total = totalDigital + totalAnalog + totalPrinted;


                        $('#data-province').append(`
                            <tr>
                                <td class="text-nowrap">${ nomor }</td>
                                <td class="text-nowrap">${ name }</td>
                                <td class="text-nowrap">
                                    <span class="badge bg-primary">${ totalDigital }</span>
                                </td>
                                <td class="text-nowrap">
                                    <span class="badge bg-primary">${ totalAnalog }</span>
                                </td>
                                <td class="text-nowrap">
                                    <span class="badge bg-primary">${ totalPrinted }</span>
                                </td>
                                <td class="text-nowrap">
                                    <span class="badge bg-success">${ total }</span>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    $('#data-province').html('<tr><td class="text-center" colspan="6">Tidak ada data</td></tr>');
                }

                onLoading('close', '#card-province');
            },
            error: function(response) {
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
                $('#data-activity').html('');

                onLoading('show', '#card-activity');
            },
            success: function(response) {
                if(response.length > 0) {
                    $.each(response, function(i, val) {
                        var nomor = i + 1;
                        var action = val.ACTION;
                        var user = val.ACTIONBY;
                        var date = moment(val.ACTIONDATE).format('DD/MM/YYYY');
                        var description = val.NOTE;


                        $('#data-activity').append(`
                            <tr>
                                <td class="align-top text-nowrap">${ nomor }</td>
                                <td class="align-top text-nowrap">${ action }</td>
                                <td class="align-top text-wrap">${ user }</td>
                                <td class="align-top">${ date }</td>
                                <td class="align-top text-wrap">${ description }</td>
                            </tr>
                        `);
                    });
                } else {
                    $('#data-activity').html('<tr><td class="text-center" colspan="5">Tidak ada data</td></tr>');
                }

                onLoading('close', '#card-activity');
            },
            error: function(response) {
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
            },
            success: function(response) {
                var chartSelector = document.getElementById('chart-digital-work');
                var chart = echarts.init(chartSelector, null, {
                    renderer: 'canvas'
                });

                var option = {
                    tooltip: {
                        trigger: 'item',
                    },
                    legend: {
                        orient: 'horizontal',
                        left: 'center'
                    },
                    series: [
                        {
                            type: 'pie',
                            smooth: true,
                            radius: '95%',
                            top: '10%',
                            data: response,
                            label: {
                                show: true,
                                position: 'inside',
                                formatter: '{c}',
                                textStyle: {
                                    color: '#fff',
                                    fontWeight: 'bold'
                                }
                            },
                            labelLine: {
                                show: true
                            },
                            emphasis: {
                                itemStyle: {
                                    shadowBlur: 10,
                                    shadowOffsetX: 0,
                                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                                }
                            }
                        }
                    ]
                };

                option && chart.setOption(option);

                onLoading('close', '#card-digital-work');
            },
            error: function(response) {
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
            },
            success: function(response) {
                var chartSelector = document.getElementById('chart-analog-work');
                var chart = echarts.init(chartSelector, null, {
                    renderer: 'canvas'
                });

                var option = {
                    tooltip: {
                        trigger: 'item',
                    },
                    legend: {
                        orient: 'horizontal',
                        left: 'center'
                    },
                    series: [
                        {
                            type: 'pie',
                            smooth: true,
                            radius: '95%',
                            top: '15%',
                            data: response,
                            label: {
                                show: true,
                                position: 'inside',
                                formatter: '{c}',
                                textStyle: {
                                    color: '#fff',
                                    fontWeight: 'bold'
                                }
                            },
                            labelLine: {
                                show: true
                            },
                            emphasis: {
                                itemStyle: {
                                    shadowBlur: 10,
                                    shadowOffsetX: 0,
                                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                                }
                            }
                        }
                    ]
                };

                option && chart.setOption(option);

                onLoading('close', '#card-analog-work');
            },
            error: function(response) {
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
            },
            success: function(response) {
                var chartSelector = document.getElementById('chart-printed-work');
                var chart = echarts.init(chartSelector, null, {
                    renderer: 'canvas'
                });

                var option = {
                    tooltip: {
                        trigger: 'item',
                    },
                    legend: {
                        orient: 'horizontal',
                        left: 'center'
                    },
                    series: [
                        {
                            type: 'pie',
                            smooth: true,
                            radius: '95%',
                            top: '20%',
                            data: response,
                            label: {
                                show: true,
                                position: 'inside',
                                formatter: '{c}',
                                textStyle: {
                                    color: '#fff',
                                    fontWeight: 'bold'
                                }
                            },
                            labelLine: {
                                show: true
                            },
                            emphasis: {
                                itemStyle: {
                                    shadowBlur: 10,
                                    shadowOffsetX: 0,
                                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                                }
                            }
                        }
                    ]
                };

                option && chart.setOption(option);

                onLoading('close', '#card-printed-work');
            },
            error: function(response) {
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
            },
            success: function(response) {
                var chartSelector = document.getElementById('chart-collection');
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
                        left: 2,
                        right: 2,
                        top: 10,
                        bottom: 0,
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
                    axisPointer: [
                        {
                            lineStyle: {
                                color: '#6B7280'
                            }
                        }
                    ],
                    series: [
                        {
                            name: 'Total',
                            type: 'bar',
                            smooth: true,
                            barWidth: '60%',
                            data: response.data
                        }
                    ]
                };

                option && chart.setOption(option);

                onLoading('close', '#card-collection');
            },
            error: function(response) {
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
                var chartSelector = document.getElementById('chart-type');
                var chart = echarts.init(chartSelector, null, {
                    renderer: 'canvas'
                });

                var option = {
                    tooltip: {
                        trigger: 'item',
                    },
                    legend: {
                        orient: 'horizontal',
                        left: 'center'
                    },
                    series: [
                        {
                            type: 'pie',
                            smooth: true,
                            radius: '95%',
                            top: '20%',
                            data: response,
                            label: {
                                show: true,
                                position: 'inside',
                                formatter: '{c}',
                                textStyle: {
                                    color: '#fff',
                                    fontWeight: 'bold'
                                }
                            },
                            labelLine: {
                                show: true
                            },
                            emphasis: {
                                itemStyle: {
                                    shadowBlur: 10,
                                    shadowOffsetX: 0,
                                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                                }
                            }
                        }
                    ]
                };

                option && chart.setOption(option);

                onLoading('close', '#card-type');
            },
            error: function(response) {
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
                var chartSelector = document.getElementById('chart-collection-status');
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
                        left: 2,
                        right: 2,
                        top: 10,
                        bottom: 0,
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
                    axisPointer: [
                        {
                            lineStyle: {
                                color: '#6B7280'
                            }
                        }
                    ],
                    series: [
                        {
                            name: 'Total',
                            type: 'bar',
                            smooth: true,
                            barWidth: '60%',
                            data: response.data
                        }
                    ]
                };

                option && chart.setOption(option);

                onLoading('close', '#card-collection-status');
            },
            error: function(response) {
                onLoading('close', '#card-collection-status');
                responseError(response);
            }
        });
    }
</script>
