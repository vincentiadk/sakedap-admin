<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <h3 class="content-header-title mb-1 d-inline-block">Entri Koleksi</h3><br>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Koleksi</a></li>
                            <li class="breadcrumb-item active">Entri</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="configuration_krd">
                <div class="row">
                    <div class="col-12 mt-1 mb-1">
                        <h4>Karya Rekam Digital</h4>
                    </div>
                </div>
                @php
                    $arr_color = ['primary', 'danger', 'success', 'warning', 'info'];
                    $total_color = count($arr_color);
                    $numberOfColumns = 3;
                    $bootstrapColWidth = 12 / $numberOfColumns;
                    $arrayChunks = array_chunk($deposit_head['KRD'], $numberOfColumns);
                    $key = 0;
                @endphp
                @foreach ($arrayChunks as $items)
                    <div class="row match-height">
                        @foreach ($items as $item)
                            <div class="col-md-{{ $bootstrapColWidth }}">
                                <a href="{{ url('admin/collection/create_manual/' . $item['id']) }}">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="text-center">
                                                <i class="{{ $item->icon() }} text-{{ $arr_color[$key % sizeof($arr_color)] }} {{ $key % sizeof($arr_color) }}"
                                                    style="font-size:100px;"></i>
                                                <h4
                                                    class="text-{{ $arr_color[$key % sizeof($arr_color)] }} font-weight-bold">
                                                    {{ $item['shape'] }}
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            @php $key++; @endphp
                        @endforeach
                    </div>
                @endforeach
                {{-- <div class="row">
                    <div class="col-md-4">
                        <a href="{{ url('admin/collection/create_manual/1') }}">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <i class="la la-book text-primary" style="font-size:100px;"></i>
                                        <h4 class="text-primary font-weight-bold">Entri Buku</h4>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('admin/collection/create_manual/2') }}">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <i class="la la-file-audio-o text-danger" style="font-size:100px;"></i>
                                        <h4 class="text-danger font-weight-bold">Entri Partitur</h4>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('admin/collection/create_manual/3') }}">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <i class="la la-map text-success" style="font-size:100px;"></i>
                                        <h4 class="text-success font-weight-bold">Entri Peta</h4>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('admin/collection/create_manual/4') }}">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <i class="la la-list-alt text-warning" style="font-size:100px;"></i>
                                        <h4 class="text-warning font-weight-bold">Entri Serial</h4>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('admin/collection/create_manual/5') }}">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <i class="la la-music text-info" style="font-size:100px;"></i>
                                        <h4 class="text-info font-weight-bold">Entri Audio</h4>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('admin/collection/create_manual/6') }}">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <i class="la la-film text-primary" style="font-size:100px;"></i>
                                        <h4 class="text-primary font-weight-bold">Entri Film</h4>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div> --}}
            </section>

            <section id="configuration_kc">
                <div class="row">
                    <div class="col-12 mt-1 mb-1">
                        <h4>Karya Cetak</h4>
                    </div>
                </div>
                @php
                    $arrayChunks = array_chunk($deposit_head['KC'], $numberOfColumns);
                    $key = 0;
                @endphp
                @foreach ($arrayChunks as $items)
                    <div class="row match-height">
                        @foreach ($items as $item)
                            <div class="col-md-{{ $bootstrapColWidth }}">
                                <a href="{{ url('admin/collection/kckra/create_manual/' . $item['id']) }}">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="text-center">
                                                <i class="{{ $item->icon() }} text-{{ $arr_color[$key % sizeof($arr_color)] }}"
                                                    style="font-size:100px;"></i>
                                                <h4
                                                    class="text-{{ $arr_color[$key % sizeof($arr_color)] }} font-weight-bold">
                                                    {{ $item['shape'] }}
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            @php $key++; @endphp
                        @endforeach
                    </div>
                @endforeach
            </section>

            <section id="configuration_kra">
                <div class="row">
                    <div class="col-12 mt-1 mb-1">
                        <h4>Karya Rekam Analog</h4>
                    </div>
                </div>
                @php
                    $arrayChunks = array_chunk($deposit_head['KRA'], $numberOfColumns);
                    $key = 0;
                @endphp
                @foreach ($arrayChunks as $items)
                    <div class="row match-height">
                        @foreach ($items as $item)
                            <div class="col-md-{{ $bootstrapColWidth }}">
                                <a href="{{ url('admin/collection/kckra/create_manual/' . $item['id']) }}">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="text-center">
                                                <i class="{{ $item->icon() }} text-{{ $arr_color[$key % sizeof($arr_color)] }}"
                                                    style="font-size:100px;"></i>
                                                <h4
                                                    class="text-{{ $arr_color[$key % sizeof($arr_color)] }} font-weight-bold">
                                                    {{ $item['shape'] }}
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            @php $key++; @endphp
                        @endforeach
                    </div>
                @endforeach
            </section>
        </div>
    </div>
</div>
