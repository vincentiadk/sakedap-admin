<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-body">
            <section id="configuration">
                <div class="row match-height">
                    <div class="col-md-4">
                        {{-- <div class="card pull-up">
                            <div class="card-body">
                                <p>
                                    <span class="font-weight-bold">Buku &nbsp;&nbsp;</span>
                                    <u>
                                        <a href="{{ url('admin/collection/monitoring?type=1') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ $collection_type_status['book']['review'] }} Pemantauan
                                        </a>
                                    </u>
                                    &nbsp;|&nbsp;
                                    <u>
                                        <a href="{{ url('admin/collection/manage/1') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ $collection_type_status['book']['manage'] }} Diterima
                                        </a>
                                    </u>
                                    &nbsp;|&nbsp;
                                    <u>
                                        <a href="{{ url('admin/collection/problem/1') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ $collection_type_status['book']['problem'] }} Masalah
                                        </a>
                                    </u>
                                </p>
                                <p>
                                    <span class="font-weight-bold">Partitur &nbsp;&nbsp;</span>
                                    <u>
                                        <a href="{{ url('admin/collection/monitoring?type=2') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ $collection_type_status['partitur']['review'] }} Pemantauan
                                        </a>
                                    </u>
                                    &nbsp;|&nbsp;
                                    <u>
                                        <a href="{{ url('admin/collection/manage/2') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ $collection_type_status['partitur']['manage'] }} Diterima
                                        </a>
                                    </u>
                                    &nbsp;|&nbsp;
                                    <u>
                                        <a href="{{ url('admin/collection/problem/2') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ $collection_type_status['partitur']['problem'] }} Masalah
                                        </a>
                                    </u>
                                </p>
                                <p>
                                    <span class="font-weight-bold">Peta &nbsp;&nbsp;</span>
                                    <u>
                                        <a href="{{ url('admin/collection/monitoring?type=3') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ $collection_type_status['map']['review'] }} Pemantauan
                                        </a>
                                    </u>
                                    &nbsp;|&nbsp;
                                    <u>
                                        <a href="{{ url('admin/collection/manage/3') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ $collection_type_status['map']['manage'] }} Diterima
                                        </a>
                                    </u>
                                    &nbsp;|&nbsp;
                                    <u>
                                        <a href="{{ url('admin/collection/problem/3') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ $collection_type_status['map']['problem'] }} Masalah
                                        </a>
                                    </u>
                                </p>
                                <p>
                                    <span class="font-weight-bold">Serial &nbsp;&nbsp;</span>
                                    <u>
                                        <a href="{{ url('admin/collection/monitoring?type=4') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ $collection_type_status['serial']['review'] }} Pemantauan
                                        </a>
                                    </u>
                                    &nbsp;|&nbsp;
                                    <u>
                                        <a href="{{ url('admin/collection/manage/4') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ $collection_type_status['serial']['manage'] }} Diterima
                                        </a>
                                    </u>
                                    &nbsp;|&nbsp;
                                    <u>
                                        <a href="{{ url('admin/collection/problem/4') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ $collection_type_status['serial']['problem'] }} Masalah
                                        </a>
                                    </u>
                                </p>
                                <p>
                                    <span class="font-weight-bold">Audio &nbsp;&nbsp;</span>
                                    <u>
                                        <a href="{{ url('admin/collection/monitoring?type=5') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ $collection_type_status['audio']['review'] }} Pemantauan
                                        </a>
                                    </u>
                                    &nbsp;|&nbsp;
                                    <u>
                                        <a href="{{ url('admin/collection/manage/5') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ $collection_type_status['audio']['manage'] }} Diterima
                                        </a>
                                    </u>
                                    &nbsp;|&nbsp;
                                    <u>
                                        <a href="{{ url('admin/collection/problem/5') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ $collection_type_status['audio']['problem'] }} Masalah
                                        </a>
                                    </u>
                                </p>
                                <p>
                                    <span class="font-weight-bold">Film &nbsp;&nbsp;</span>
                                    <u>
                                        <a href="{{ url('admin/collection/monitoring?type=6') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ $collection_type_status['film']['review'] }} Pemantauan
                                        </a>
                                    </u>
                                    &nbsp;|&nbsp;
                                    <u>
                                        <a href="{{ url('admin/collection/manage/6') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ $collection_type_status['film']['manage'] }} Diterima
                                        </a>
                                    </u>
                                    &nbsp;|&nbsp;
                                    <u>
                                        <a href="{{ url('admin/collection/problem/1') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ $collection_type_status['film']['problem'] }} Masalah
                                        </a>
                                    </u>
                                </p>
                            </div>
                        </div> --}}
                        <div class="card pull-up">
                            <div class="card-content">
                                <div class="card-body">
                                    <h4 class="card-title">Provinsi Terbanyak</h4>
                                </div>
                                <ul class="list-group list-group-flush list-location">
                                    {{-- @foreach ($collection_location as $value)
                                        <li class="list-group-item">
                                            <span
                                                class="badge badge-pill bg-success float-right">{{ $value->total_exemplar }}
                                                Eksemplar
                                            </span>
                                            <span
                                                class="badge badge-pill bg-info float-right">{{ $value->total_collection }}
                                                Judul
                                            </span>
                                            {{ $value->name }}
                                        </li>
                                    @endforeach --}}
                                </ul>
                            </div>
                        </div>
                        <div class="card pull-up">
                            <div class="card-header">
                                <h4 class="card-title text-center">Koleksi Karya Rekam Digital</h4>
                            </div>
                            <div class="card-body">
                                {{-- <canvas id="collection_status" height="300"></canvas> --}}
                                <canvas id="collection_krd" height="320"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        {{-- <div class="card pull-up">
							<div class="card-header">
								<h4 class="card-title text-center">Total Koleksi</h4>
							</div>
							<div class="card-body">
								<canvas id="total_collection" height="571"></canvas>
							</div>
						</div> --}}
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card pull-up">
                                    <div class="card-header">
                                        <h4 class="card-title text-center">Koleksi Karya Cetak</h4>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="collection_kc" height="300"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card pull-up">
                                    <div class="card-header">
                                        <h4 class="card-title text-center">Koleksi Karya Rekam Analog</h4>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="collection_kra" height="300"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card pull-up">
                                    <div class="card-header">
                                        <h4 class="card-title text-center">Total Kaya</h4>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="collection_total" height="300"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        {{-- <div class="card pull-up">
							<div class="card-header">
								<h4 class="card-title text-center">Total Koleksi</h4>
							</div>
							<div class="card-body">
								<canvas id="total_collection" height="571"></canvas>
							</div>
						</div> --}}
                        <div class="row collection-list">
                            {{-- @php
                                $arr_color = ['primary', 'info', 'success', 'warning', 'danger'];
                            @endphp
                            @foreach ($collection_list as $index => $value)
                                @php
                                    $percentage = ($value['total'] / $collection_list[0]['total']) * 100;
                                @endphp
                                <div class="col-lg-12">
                                    <div class="card pull-up">
                                        <div class="card-content">
                                            <div class="card-body">
                                                <div class="media d-flex">
                                                    <div class="media-body text-left">
                                                        <h3 class="{{ $arr_color[$index] }}">{{ $value['value'] }}</h3>
                                                        <h6>{{ $value['code'] . ' - ' . $value['shape'] }}</h6>
                                                    </div>
                                                    <div>
                                                        <i
                                                            class="icon-basket-loaded {{ $arr_color[$index] }} font-large-2 float-right"></i>
                                                    </div>
                                                </div>
                                                <div class="progress progress-sm mt-1 mb-0 box-shadow-2">
                                                    <div class="progress-bar bg-gradient-x-{{ $arr_color[$index] }}"
                                                        role="progressbar" style="width: {{ $percentage }}%"
                                                        aria-valuenow="{{ $percentage }}" aria-valuemin="0"
                                                        aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach --}}
                        </div>

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card pull-up">
                                    <div class="card-header">
                                        <h4 class="card-title text-center">Aktivitas User</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="media-list list-group list-activity">
                                            {{-- @foreach ($activity as $a)
                                                <a href="javascript:void(0);" style="pointer-events:none;"
                                                    class="list-group-item list-group-item-action media">
                                                    <div class="media-body">
                                                        <h6 class="list-group-item-heading" style="overflow:hidden;">
                                                            {{ $a->description }}</h6>
                                                    </div>
                                                    <small class="text-muted">{{ $a->user ? $a->user->username : '-' }}
                                                        {{ $a->created_at->format('d-m-Y, H:i') }}</small>
                                                </a>
                                            @endforeach --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-md-3">
                        <div class="card pull-up">
                            <div class="card-header">
                                <h4 class="card-title text-center">Koleksi Pemantauan</h4>
                            </div>
                            <div class="card-body">
                                <div class="media-list list-group">
                                    @foreach ($collection_monitoring as $cm)
                                        <a href="{{ url('admin/collection/monitoring/review/' . $cm->id) }}"
                                            class="list-group-item list-group-item-action media">
                                            <div class="media-left">
                                                <i class="{{ $cm->icon() }}"></i>
                                            </div>
                                            <div class="media-body">
                                                <h6 class="list-group-item-heading" style="overflow:hidden;">
                                                    {{ $cm->title }}</h6>
                                            </div>
                                            <small
                                                class="text-muted">{{ date('d F Y', strtotime($cm->created_at)) }}</small>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    <div class="col-md-6">
                        {{-- <div class="card pull-up">
                            <div class="card-header">
                                <h4 class="card-title text-center">Penambahan Koleksi 10 Hari Terakhir</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="collection_last_day" height="288"></canvas>
                            </div>
                        </div> --}}
                        <div class="card pull-up">
                            <div class="card-header">
                                <h4 class="card-title text-center">Tipe File</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="file_type" height="288"></canvas>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-md-3">
                        <div class="card pull-up">
                            <div class="card-header">
                                <h4 class="card-title text-center">Aktivitas User</h4>
                            </div>
                            <div class="card-body">
                                <div class="media-list list-group">
                                    @foreach ($activity as $a)
                                        <a href="javascript:void(0);" style="pointer-events:none;"
                                            class="list-group-item list-group-item-action media">
                                            <div class="media-body">
                                                <h6 class="list-group-item-heading" style="overflow:hidden;">
                                                    {{ $a->description }}</h6>
                                            </div>
                                            <small class="text-muted">{{ $a->user ? $a->user->username : '-' }}
                                                {{ $a->created_at->format('d-m-Y, H:i') }}</small>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    var selector_pie_krd = $('#collection_krd');
    var selector_pie_kc = $('#collection_kc');
    var selector_pie_kra = $('#collection_kra');
    var selector_pie_total = $('#collection_total');
    var optionPie = {
        responsive: true,
        maintainAspectRatio: false,
        responsiveAnimationDuration: 500,
        tooltips: {
            enabled: false
        },
        legend: {
            display: true,
            position: 'bottom', // Adjust legend position if needed
            labels: {
                padding: 20, // Add margin here to control the space between legend and chart
            },
        },
        plugins: {
            datalabels: {
                formatter: (value, ctx) => {
                    let sum = 0;
                    let dataArr = ctx.chart.data.datasets[0].data;
                    dataArr.map(data => {
                        sum += data;
                    });
                    let percentage = (value * 100 / sum).toFixed(2);
                    return percentage > 0 ? percentage + '%' : '';
                },
                backgroundColor: function(context) {
                    return context.dataset.backgroundColor;
                },
                borderColor: 'white',
                borderRadius: 25,
                borderWidth: 2,
                color: 'white',
                display: function(context) {
                    var dataset = context.dataset;
                    var count = dataset.data.length;
                    var value = dataset.data[context.dataIndex];
                    return value > count * 1.5;
                },
                font: {
                    weight: 'bold'
                },
                padding: 6
            },
        }
    };

    $(function() {
        $('.table').DataTable();

        var selector_bar = $('#total_collection');
        var selector_pie = $('#collection_status');
        var selector_line = $('#collection_last_day');
        var selector_stacked_bar = $('#file_type');

        var optionBar = {
            responsive: true,
            maintainAspectRatio: false,
            responsiveAnimationDuration: 500,
            elements: {
                rectangle: {
                    borderWidth: 2,
                    borderColor: 'rgb(0, 255, 0)',
                    borderSkipped: 'bottom'
                }
            },
            legend: {
                position: 'top',
            },
            scales: {
                xAxes: [{
                    display: true,
                    gridLines: {
                        color: "#f3f3f3",
                        drawTicks: false,
                    },
                    scaleLabel: {
                        display: true,
                    }
                }],
                yAxes: [{
                    display: true,
                    gridLines: {
                        color: "#f3f3f3",
                        drawTicks: false,
                    },
                    scaleLabel: {
                        display: true,
                    }
                }]
            }
        };

        var optionLine = {
            responsive: true,
            maintainAspectRatio: false,
            responsiveAnimationDuration: 500
        };

        var optionStackedBar = {
            responsive: true,
            maintainAspectRatio: false,
            responsiveAnimationDuration: 500
        };

        renderChart(
            selector_stacked_bar,
            'horizontalBar',
            optionStackedBar,
            "{{ url('admin/load_dashboard/file_type') }}"
        );
        getActivity();
        getListLocation();
        getCollectionList();
        getChartGrouped();
    });

    function getActivity() {
        $.ajax({
            url: "{{ url('admin/load_dashboard/activity') }}", // The URL defined in your route
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var html = '';
                $.each(data, function(key, element) {
                    if (typeof element.user !== 'undefined') {
                        var username = element.user.username;
                    } else {
                        var username = '-';
                    }
                    html += `<a href="javascript:void(0);" style="pointer-events:none;"
                                class="list-group-item list-group-item-action media">
                                <div class="media-body"> 
                                    <h6 class="list-group-item-heading" style="overflow:hidden;"> 
                                        ` + element.description + `
                                    </h6>
                                </div>
                                <small class="text-muted">
                                    ` + username + `
                                    ` + element.created_at + `
                                </small>
                            </a>`;
                });
                $(".list-activity").html(html);
            },
            error: function(xhr, status, error) {
                $(".list-activity").html(`
                            <a href="javascript:void(0);" style="pointer-events:none;"
                                class="list-group-item list-group-item-action media">
                                <div class="media-body"> 
                                    <h6 class="list-group-item-heading" style="overflow:hidden;"> 
                                        Data Tidak Ditemukan
                                    </h6>
                                </div>
                            </a>
                `)
            }
        });
    }

    function getListLocation() {
        $.ajax({
            url: "{{ url('admin/load_dashboard/collection_location') }}", // The URL defined in your route
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var html = '';
                data.forEach(element => {
                    html += `<li class="list-group-item">
                                <span
                                    class="badge badge-pill bg-success float-right">` + element.total_exemplar + `
                                    KC-KRA
                                </span>
                                <span
                                    class="badge badge-pill bg-info float-right">` + element.total_collection + `
                                    KRD
                                </span>
                                ` + element.name + `
                            </li>`;
                });
                $(".list-location").html(html);
            },
            error: function(xhr, status, error) {
                $(".list-location").html(`<li class="list-group-item"> Data Kosong </li>`)
            }
        });
    }

    function getCollectionList() {
        $.ajax({
            url: "{{ url('admin/load_dashboard/collection_list') }}", // The URL defined in your route
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var html = '';
                var arr_color = ['primary', 'info', 'success', 'warning', 'danger'];
                data.forEach(function(value, index) {
                    var percentage = (value.total / data[0].total) * 100;
                    html += `<div class="col-lg-12">
                                <div class="card pull-up">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <div class="media d-flex">
                                                <div class="media-body text-left">
                                                    <h3 class="` + arr_color[index] + `">` + value.value + `</h3>
                                                    <h6>` + value.code + ` - ` + value.shape + `</h6>
                                                </div>
                                                <div>
                                                    <i
                                                        class="icon-basket-loaded ` + arr_color[index] + ` font-large-2 float-right"></i>
                                                </div>
                                            </div>
                                            <div class="progress progress-sm mt-1 mb-0 box-shadow-2">
                                                <div class="progress-bar bg-gradient-x-` + arr_color[index] + `"
                                                    role="progressbar" style="width: ` + percentage + `%"
                                                    aria-valuenow="` + percentage + `" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                });
                $(".collection-list").html(html);
            },
            error: function(xhr, status, error) {
                $(".collection-list").html(`
                    <div class="col-lg-12">
                        <div class="card pull-up">
                            <div class="card-content">
                                <div class="card-body">
                                    <h5>Data Tidak Ditemukan</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                `)
            }
        });
    }

    function getChartGrouped() {
        $.ajax({
            url: "{{ url('admin/load_dashboard/collection_grouped') }}", // The URL defined in your route
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                new Chart(selector_pie_krd, {
                    type: 'pie',
                    options: optionPie,
                    data: data.KRD
                });
                new Chart(selector_pie_kc, {
                    type: 'pie',
                    options: optionPie,
                    data: data.KC
                });
                new Chart(selector_pie_kra, {
                    type: 'pie',
                    options: optionPie,
                    data: data.KRA
                });
                new Chart(selector_pie_total, {
                    type: 'pie',
                    options: optionPie,
                    data: data.total
                });
            }
        });
    }

    function renderChart(selector, type, options, url) {
        // Fetch data via Ajax
        $.ajax({
            url: url, // URL to your Laravel route
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Create a new pie chart
                new Chart(selector, {
                    type: type,
                    options: options,
                    data: data
                });
            },
            error: function(xhr, status, error) {
                // Handle errors, if any
                console.error(error);
            }
        });
    }

    function generateColor(data) {
        var colors = [];
        for (let index = 1; index <= data.length; index++) {
            colors.push(selectColor(index));
        }

        return colors;
    }

    function selectColor(number) {
        const hue = number * 137.508 + 60; // use golden angle approximation
        return `hsl(${hue},50%,75%)`;
    }
</script>
