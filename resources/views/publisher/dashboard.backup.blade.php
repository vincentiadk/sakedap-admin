<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-body">
            <section id="configuration">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card" style="height: 300px">
                            <div class="card-body">
                                <p>
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
                                </p>
                                <p>
                                    <span class="font-weight-bold">Partitur &nbsp;&nbsp;</span>
                                    <u>
                                        <a href="{{ url('publisher/collection/monitoring?tipe=2') }}"
                                            class="font-italic" style="font-size:12px;">
                                            {{ App\Model\Collection::where('type', 2)->where('status', 1)->where('publisher_id', $publisher_id)->count() }}
                                            Review
                                        </a>
                                    </u>
                                    &nbsp;|&nbsp;
                                    <u>
                                        <a href="{{ url('publisher/collection/accepted?tipe=2') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ App\Model\Collection::where('type', 2)->where('status', 2)->where('publisher_id', $publisher_id)->count() }}
                                            Diterima
                                        </a>
                                    </u>
                                    &nbsp;|&nbsp;
                                    <u>
                                        <a href="{{ url('publisher/collection/problem?tipe=2') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ App\Model\Collection::where('type', 2)->where('status', 3)->where('publisher_id', $publisher_id)->count() }}
                                            Masalah
                                        </a>
                                    </u>
                                </p>
                                <p>
                                    <span class="font-weight-bold">Peta &nbsp;&nbsp;</span>
                                    <u>
                                        <a href="{{ url('publisher/collection/monitoring?tipe=3') }}"
                                            class="font-italic" style="font-size:12px;">
                                            {{ App\Model\Collection::where('type', 3)->where('status', 1)->where('publisher_id', $publisher_id)->count() }}
                                            Review
                                        </a>
                                    </u>
                                    &nbsp;|&nbsp;
                                    <u>
                                        <a href="{{ url('publisher/collection/accepted?tipe=3') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ App\Model\Collection::where('type', 3)->where('status', 2)->where('publisher_id', $publisher_id)->count() }}
                                            Diterima
                                        </a>
                                    </u>
                                    &nbsp;|&nbsp;
                                    <u>
                                        <a href="{{ url('publisher/collection/problem?tipe=3') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ App\Model\Collection::where('type', 3)->where('status', 3)->where('publisher_id', $publisher_id)->count() }}
                                            Masalah
                                        </a>
                                    </u>
                                </p>
                                <p>
                                    <span class="font-weight-bold">Serial &nbsp;&nbsp;</span>
                                    <u>
                                        <a href="{{ url('publisher/collection/monitoring?tipe=4') }}"
                                            class="font-italic" style="font-size:12px;">
                                            {{ App\Model\Collection::where('type', 4)->where('parent_id', 0)->where('publisher_id', $publisher_id)->where('status', 1)->count() }}
                                            Review
                                        </a>
                                    </u>
                                    &nbsp;|&nbsp;
                                    <u>
                                        <a href="{{ url('publisher/collection/accepted?tipe=4') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ App\Model\Collection::where('type', 4)->where('parent_id', 0)->where('publisher_id', $publisher_id)->where('status', 2)->count() }}
                                            Diterima
                                        </a>
                                    </u>
                                    &nbsp;|&nbsp;
                                    <u>
                                        <a href="{{ url('publisher/collection/problem?tipe=4') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ App\Model\Collection::where('type', 4)->where('parent_id', 0)->where('publisher_id', $publisher_id)->where('status', 3)->count() }}
                                            Masalah
                                        </a>
                                    </u>
                                </p>
                                <p>
                                    <span class="font-weight-bold">Audio &nbsp;&nbsp;</span>
                                    <u>
                                        <a href="{{ url('publisher/collection/monitoring?tipe=5') }}"
                                            class="font-italic" style="font-size:12px;">
                                            {{ App\Model\Collection::where('type', 5)->where('publisher_id', $publisher_id)->where('status', 1)->count() }}
                                            Review
                                        </a>
                                    </u>
                                    &nbsp;|&nbsp;
                                    <u>
                                        <a href="{{ url('publisher/collection/accepted?tipe=5') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ App\Model\Collection::where('type', 5)->where('publisher_id', $publisher_id)->where('status', 2)->count() }}
                                            Diterima
                                        </a>
                                    </u>
                                    &nbsp;|&nbsp;
                                    <u>
                                        <a href="{{ url('publisher/collection/problem?tipe=5') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ App\Model\Collection::where('type', 5)->where('publisher_id', $publisher_id)->where('status', 3)->count() }}
                                            Masalah
                                        </a>
                                    </u>
                                </p>
                                <p>
                                    <span class="font-weight-bold">Film &nbsp;&nbsp;</span>
                                    <u>
                                        <a href="{{ url('publisher/collection/monitoring?tipe=6') }}"
                                            class="font-italic" style="font-size:12px;">
                                            {{ App\Model\Collection::where('type', 6)->where('publisher_id', $publisher_id)->where('status', 1)->count() }}
                                            Review
                                        </a>
                                    </u>
                                    &nbsp;|&nbsp;
                                    <u>
                                        <a href="{{ url('publisher/collection/accepted?tipe=6') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ App\Model\Collection::where('type', 6)->where('publisher_id', $publisher_id)->where('status', 2)->count() }}
                                            Diterima
                                        </a>
                                    </u>
                                    &nbsp;|&nbsp;
                                    <u>
                                        <a href="{{ url('publisher/collection/problem?tipe=6') }}" class="font-italic"
                                            style="font-size:12px;">
                                            {{ App\Model\Collection::where('type', 6)->where('publisher_id', $publisher_id)->where('status', 3)->count() }}
                                            Masalah
                                        </a>
                                    </u>
                                </p>
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
                                <div class="media-list list-group">
                                    @foreach ($collection_accept as $cm)
                                        <a href="{{ url('publisher/collection/monitoring/detail/' . $cm->id) }}"
                                            class="list-group-item list-group-item-action media">
                                            <div class="media-left">
                                                <i class="{{ $cm->icon() }}"></i>
                                            </div>
                                            <div class="media-body">
                                                @if ($cm->type == '4' && $cm->parent_id != 0)
                                                    <h6 class="list-group-item-heading" style="overflow:hidden;">
                                                        {{ $cm->parent()->title }}</h6>
                                                    <p class="text-muted m-0">Edisi: {{ $cm->title }}</p>
                                                @else
                                                    <h6 class="list-group-item-heading" style="overflow:hidden;">
                                                        {{ $cm->title }}</h6>
                                                @endif
                                            </div>
                                            <small
                                                class="text-muted">{{ date('d F Y', strtotime($cm->created_at)) }}</small>
                                        </a>
                                    @endforeach
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
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">
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
                                    </li>
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
                                <div class="media-list list-group">
                                    @foreach ($activity as $a)
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
                                                    {{ $a->description }} {{ $judul }}</h6>
                                            </div>
                                            <small class="text-muted">{{ $a->user ? $a->user->username : '-' }} -
                                                {{ date('d F Y', strtotime($a->created_at)) }}</small>
                                        </a>
                                    @endforeach
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
    $(function() {
        $('.table').DataTable();

        var selector_bar = $('#total_collection');
        var selector_pie = $('#collection_status');
        var selector_line = $('#collection_last_day');
        var selector_stacked_bar = $('#file_type');
        var selector_pie_krd = $('#collection_krd');
        var selector_pie_kc = $('#collection_kc');
        var selector_pie_kra = $('#collection_kra');
        var selector_pie_total = $('#collection_total');

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

        var optionPie = {
            responsive: true,
            maintainAspectRatio: false,
            responsiveAnimationDuration: 500
        };

        var optionPieTotal = {
            responsive: true,
            maintainAspectRatio: false,
            responsiveAnimationDuration: 500,
            tooltips: {
                enabled: false
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
                    display: true,
                    anchor: 'end',
                    align: 'top',
                    color: 'black',
                    font: {
                        weight: 'bold',
                        size: 14
                    }
                }
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


        var dataPie = {
            labels: ['Diterima', 'Review', 'Bermasalah'],
            datasets: [{
                label: 'Koleksi',
                data: [
                    "{{ $collection['collection_accept'] }}",
                    "{{ $collection['collection_review'] }}",
                    "{{ $collection['collection_problem'] }}",
                ],
                backgroundColor: ['#28A745', '#FFC107', '#DC3545'],
            }]
        };

        var dataPieKrd = {
            labels: @json(array_keys($total_collection['grouped']['KRD'])),
            datasets: [{
                label: 'Koleksi',
                data: @json(array_values($total_collection['grouped']['KRD'])),
                backgroundColor: generateColor(@json(array_values($total_collection['grouped']['KRD']))),
            }]
        };

        var dataPieKc = {
            labels: @json(array_keys($total_collection['grouped']['KC'])),
            datasets: [{
                label: 'Koleksi',
                data: @json(array_values($total_collection['grouped']['KC'])),
                backgroundColor: generateColor(@json(array_values($total_collection['grouped']['KC']))),
            }]
        };

        var dataPieKra = {
            labels: @json(array_keys($total_collection['grouped']['KRA'])),
            datasets: [{
                label: 'Koleksi',
                data: @json(array_values($total_collection['grouped']['KRA'])),
                backgroundColor: generateColor(@json(array_values($total_collection['grouped']['KRA']))),
            }]
        };

        var dataPieTotal = {
            labels: @json(array_keys($total_collection['total'])),
            datasets: [{
                label: 'Koleksi',
                data: @json(array_values($total_collection['total'])),
                backgroundColor: generateColor(@json(array_values($total_collection['total']))),
            }]
        };

        var dataLine = {
            labels: [
                'Buku',
                'Partitur',
                'Peta',
                'Serial',
                'Audio',
                'Film'
            ],
            datasets: [{
                label: 'Total',
                data: [
                    '{{ $collection_last_day['book'] }}',
                    '{{ $collection_last_day['partitur'] }}',
                    '{{ $collection_last_day['peta'] }}',
                    '{{ $collection_last_day['serial'] }}',
                    '{{ $collection_last_day['audio'] }}',
                    '{{ $collection_last_day['film'] }}'
                ],
                backgroundColor: '#17A2B8',
                hoverBackgroundColor: '#17A2B8',
                borderColor: 'transparent'
            }]
        };

        var dataStackedBar = {
            labels: [
                'PDF',
                'WAV',
                'MPEG'
            ],
            datasets: [{
                label: 'Total',
                data: [
                    '{{ $file_type['pdf'] }}',
                    '{{ $file_type['wav'] }}',
                    '{{ $file_type['mpeg'] }}'
                ],
                backgroundColor: '#17A2B8',
                hoverBackgroundColor: '#17A2B8',
                borderColor: 'transparent'
            }]
        };

        var configPie = {
            type: 'pie',
            options: optionPie,
            data: dataPie
        };

        // var configBar = {
        //     type: 'bar',
        //     options: optionBar,
        //     data: dataBar
        // };

        var configLine = {
            type: 'line',
            options: optionLine,
            data: dataLine
        };

        var configStackedBar = {
            type: 'horizontalBar',
            options: optionStackedBar,
            data: dataStackedBar
        };

        var configPieKrd = {
            type: 'pie',
            options: optionPieTotal,
            data: dataPieKrd
        };

        var configPieKc = {
            type: 'pie',
            options: optionPieTotal,
            data: dataPieKc
        };

        var configPieKra = {
            type: 'pie',
            options: optionPieTotal,
            data: dataPieKra
        };

        var configPieTotal = {
            type: 'pie',
            options: optionPieTotal,
            data: dataPieTotal
        };


        // new Chart(selector_bar, configBar);
        new Chart(selector_pie, configPie);
        new Chart(selector_line, configLine);
        new Chart(selector_stacked_bar, configStackedBar);
        new Chart(selector_pie_krd, configPieKrd);
        new Chart(selector_pie_kc, configPieKc);
        new Chart(selector_pie_kra, configPieKra);
        new Chart(selector_pie_total, configPieTotal);
    });

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
