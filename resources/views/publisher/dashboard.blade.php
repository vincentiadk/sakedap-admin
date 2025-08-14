<div class="app-content content">
    <div class="content-wrapper">
    <div class="alert alert-icon-left alert-danger mb-2" role="alert"><span class="alert-icon"><i class="la la-bell-o"></i>
        </span><p><strong>Sehubungan dengan proses optimalisasi data Serah Simpan Karya Cetak dan Karya Rekam dengan data ISBN yang masih berlangsung, akan terjadi ketidakonsistenan data hingga 31 Agustus 2025.<br/>Mohon maaf atas ketidaknyamanannya.<br/><br/>Tim Edeposit</p>
        </strong>
        </div>
        <div class="content-body">
            <section id="configuration">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body total-koleksi" style="height: 400px; overflow-y: scroll">
                                {{-- <p>
                                    <span class="font-weight-bold">Buku &nbsp;&nbsp;</span>
                                    <u>
                                        <a href="{{ url('publisher/collection/monitoring?tipe=1') }}"
                                            class="font-italic" style="font-size:12px;">
                                            {{ App\Model\Collection::where('type', 1)->where('parent_id', 0)->where('status', 1)->where('publisher_id', $publisher_id)->count() }}
                                            Review
                                        </a>
                                    </u>
                                    &nbsp;|&nbsp;
                                    <u>
                                        <a href="{{ url('publisher/collection/accepted?tipe=1') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ App\Model\Collection::where('type', 1)->where('parent_id', 0)->where('status', 2)->where('publisher_id', $publisher_id)->count() }}
                                            Diterima
                                        </a>
                                    </u>
                                    &nbsp;|&nbsp;
                                    <u>
                                        <a href="{{ url('publisher/collection/problem?tipe=1') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ App\Model\Collection::where('type', 1)->where('parent_id', 0)->where('status', 3)->where('publisher_id', $publisher_id)->count() }}
                                            Masalah
                                        </a>
                                    </u>
                                </p> --}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title text-center">Status Koleksi</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="collection_status" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title text-center">Koleksi Terakhir Diterima</h4>
                            </div>
                            <div class="card-body">
                                <div class="media-list list-group list-collection-accept">
                                    {{-- @foreach ($collection_accept as $cm)
                                        <a href="{{ url('publisher/collection/monitoring/detail/' . $cm->id) }}"
                                            class="list-group-item list-group-item-action media">
                                            <div class="media-left">
                                                <i class="{{ $cm->icon() }}"></i>
                                            </div>
                                            <div class="media-body">
                                                @if ($cm->type == '4' && $cm->parent_id != 0)
                                                    <h6 class="list-group-item-heading" style="overflow:hidden;">
                                                        {{ $cm->parent()->title }}
                                                    </h6>
                                                    <p class="text-muted m-0">Edisi: {{ $cm->title }}</p>
                                                @else
                                                    <h6 class="list-group-item-heading" style="overflow:hidden;">
                                                        {{ $cm->title }}
                                                    </h6>
                                                @endif
                                            </div>
                                            <small
                                                class="text-muted">{{ date('d F Y', strtotime($cm->created_at)) }}</small>
                                        </a>
                                    @endforeach --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title text-center">Penambahan Koleksi 10 Hari Terakhir</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="collection_last_day" height="288"></canvas>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title text-center">Tipe File</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="file_type" height="288"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="col-md-12">
                            <div class="card pull-up">
                                <div class="card-header">
                                    <h4 class="card-title text-center">Koleksi Karya Rekam Digital</h4>
                                </div>
                                <div class="card-body">
                                    {{-- <canvas id="collection_status" height="300"></canvas> --}}
                                    <canvas id="collection_krd" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
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
                </div>
                <div class="row">
                    <div class="col-md-4">

                        {{-- <div class="card">
                        <div class="card-header">
                          <h4 class="card-title text-center">Total Koleksi</h4>
                        </div>
                        <div class="card-body">
                          <canvas id="total_collection" height="150"></canvas>
                        </div> --}}
                        <div class="card pull-up">
                            <div class="card-header">
                                <h4 class="card-title text-center">Total Karya</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="collection_total" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card pull-up">
                            <div class="card-content">
                                <div class="card-body">
                                    <h4 class="card-title">Jumlah Belum Dikirim</h4>
                                </div>
                                <ul class="list-group list-group-flush list-not-delivered">
                                    {{-- <li class="list-group-item">
                                        <span
                                            class="badge badge-pill bg-success float-right">{{ $not_delivered['total_national'] }}
                                            Koleksi
                                        </span>
                                        Perpusnas
                                    </li>
                                    <li class="list-group-item">
                                        <span
                                            class="badge badge-pill bg-success float-right">{{ $not_delivered['total_province'] }}
                                            Koleksi
                                        </span>
                                        Perpustakaan Provinsi
                                    </li> --}}
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title text-center">Aktivitas User</h4>
                            </div>
                            <div class="card-body">
                                <div class="media-list list-group list-activity">
                                    {{-- @foreach ($activity as $a)
                                        <a href="javascript:void(0);" style="cursor:none;"
                                            class="list-group-item list-group-item-action media">
                                            <div class="media-body">
                                                @php
                                                    $judul = '';
                                                    if (isset(json_decode($a->properties, true)['title'])) {
                                                        $judul = json_decode($a->properties, true)['title'];
                                                    } elseif (isset(json_decode($a->properties, true)['judul'])) {
                                                        $judul = json_decode($a->properties, true)['judul'];
                                                    }
                                                    
                                                @endphp
                                                <h6 class="list-group-item-heading" style=" overflow:hidden;">
                                                    {{ $a->description }} {{ $judul }}
                                                </h6>
                                            </div>
                                            <small class="text-muted">{{ $a->user ? $a->user->username : '-' }} -
                                                {{ date('d F Y', strtotime($a->created_at)) }}</small>
                                        </a>
                                    @endforeach --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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

        var optionLine = {
            responsive: true,
            maintainAspectRatio: false,
            responsiveAnimationDuration: 500
        };

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
            "{{ url('publisher/load_dashboard/file_type') }}"
        );
        renderChart(
            selector_pie,
            'pie',
            optionPie,
            "{{ url('publisher/load_dashboard/collection_status') }}"
        );
        renderChart(
            selector_line,
            'line',
            optionLine,
            "{{ url('publisher/load_dashboard/collection_last_day') }}"
        );

        getChartGrouped();
        getActivity();
        getTotalKoleksi();
        getCollectionAccept();
        getNotDelivered();
    });

    function getTotalKoleksi() {
        $.ajax({
            url: "{{ url('publisher/load_dashboard/total_koleksi') }}", // The URL defined in your route
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var html = '';
                // console.log(data, 'data');
                $.each(data, function(key, element) {
                    html += `<p>
                                <span class="font-weight-bold"> ` + element.shape + ` &nbsp;&nbsp;</span>
                                <u>
                                    <a href="{{ url('publisher/collection/monitoring?tipe= `+element.deposit_head_id+`') }}"
                                        class="font-italic" style="font-size:12px;">
                                        ` + element.review + `
                                        Review
                                    </a>
                                </u>
                                &nbsp;|&nbsp;
                                <u>
                                    <a href="{{ url('publisher/collection/accepted?tipe= `+element.deposit_head_id+`') }}" class="font-italic"
                                        style="font-size:12px;">
                                        ` + element.diterima + `
                                        Diterima
                                    </a>
                                </u>
                                &nbsp;|&nbsp;
                                <u>
                                    <a href="{{ url('publisher/collection/problem?tipe= `+element.deposit_head_id+`') }}" class="font-italic"
                                        style="font-size:12px;">
                                        ` + element.masalah + `
                                        Masalah
                                    </a>
                                </u>
                            </p>`;
                });
                $(".total-koleksi").html(html);
            },
            error: function(xhr, status, error) {
                $(".total-koleksi").html(`
                            <p>
                                <span class="font-weight-bold">Data Kosong</span>
                            </p>
                `)
            }
        });
    }

    function getCollectionAccept() {
        $.ajax({
            url: "{{ url('publisher/load_dashboard/collection_accept') }}", // The URL defined in your route
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var html = '';
                // console.log(data, 'collection_accept');
                $.each(data, function(key, element) {
                    var title = '';
                    if (element.is_serial && element.parent_id != 0) {
                        title = `<h6 class="list-group-item-heading" style="overflow:hidden;">
                                    ` + element.parent_title + `
                                </h6>
                                <p class="text-muted m-0">Edisi: ` + element.title + `</p>`;
                    } else {
                        title = `<h6 class="list-group-item-heading" style="overflow:hidden;">
                                    ` + element.title + `
                                </h6>`;
                    }
                    html += `<a href="{{ url('publisher/collection/monitoring/detail/`+element.id+`') }}"
                                class="list-group-item list-group-item-action media">
                                <div class="media-left">
                                    <i class="` + element.icon + `"></i>
                                </div>
                                <div class="media-body">
                                    ` + element.title + `
                                </div>
                                <small class="text-muted">` + element.created_at + `</small>
                            </a>`;
                });
                $(".list-collection-accept").html(html);
            },
            error: function(xhr, status, error) {
                $(".list-collection-accept").html(`
                            <a href="javascript:void(0);" style="pointer-events:none;"
                                class="list-group-item list-group-item-action media">
                                <div class="media-body"> 
                                    <h6 class="list-group-item-heading" style="overflow:hidden;"> 
                                        Data Tidak Ditemukan
                                    </h6>
                                </div>
                            </a>
                `);
            }
        });
    }

    function getNotDelivered() {
        $.ajax({
            url: "{{ url('publisher/load_dashboard/not_delivered') }}", // The URL defined in your route
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var html = '';
                $.each(data, function(key, element) {
                    html += `<li class="list-group-item">
                                <span
                                    class="badge badge-pill bg-success float-right">` + element.total + `
                                    Koleksi
                                </span>
                                ` + element.jenis + `
                            </li>`;
                });
                $(".list-not-delivered").html(html);
            },
            error: function(xhr, status, error) {
                $(".list-not-delivered").html(`
                            <li class="list-group-item">
                                Data tidak ditemukan
                            </li>
                `);
            }
        });
    }

    function getActivity() {
        $.ajax({
            url: "{{ url('publisher/load_dashboard/activity') }}", // The URL defined in your route
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

    function getChartGrouped() {
        $.ajax({
            url: "{{ url('publisher/load_dashboard/collection_grouped') }}", // The URL defined in your route
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
