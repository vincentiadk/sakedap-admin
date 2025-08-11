<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <h3 class="content-header-title mb-1 d-inline-block">Masalah Koleksi</h3><br>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Koleksi</a></li>
                            <li class="breadcrumb-item active">Masalah</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="configuration">
                <div class="row">
                    <div class="col-md-4">
                        <a href="{{ url('admin/collection/problem/1') }}">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <i class="la la-book text-primary" style="font-size:100px;"></i>
                                        <h4 class="text-primary font-weight-bold">Masalah Buku</h4>
                                        <div class="text-muted">{{ $total_book }} koleksi</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('admin/collection/problem/2') }}">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <i class="la la-file-audio-o text-danger" style="font-size:100px;"></i>
                                        <h4 class="text-danger font-weight-bold">Masalah Partitur</h4>
                                        <div class="text-muted">{{ $total_partitur }} koleksi</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('admin/collection/problem/3') }}">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <i class="la la-map text-success" style="font-size:100px;"></i>
                                        <h4 class="text-success font-weight-bold">Masalah Peta</h4>
                                        <div class="text-muted">{{ $total_map }} koleksi</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('admin/collection/problem/4') }}">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <i class="la la-list-alt text-warning" style="font-size:100px;"></i>
                                        <h4 class="text-warning font-weight-bold">Masalah Serial</h4>
                                        <div class="text-muted">{{ $total_serial }} koleksi</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('admin/collection/problem/5') }}">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <i class="la la-music text-info" style="font-size:100px;"></i>
                                        <h4 class="text-info font-weight-bold">Masalah Audio</h4>
                                        <div class="text-muted">{{ $total_audio }} koleksi</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('admin/collection/problem/6') }}">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <i class="la la-film text-primary" style="font-size:100px;"></i>
                                        <h4 class="text-primary font-weight-bold">Masalah Film</h4>
                                        <div class="text-muted">{{ $total_film }} koleksi</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
               </div>
            </section>
        </div>
    </div>
</div>
