<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <h3 class="content-header-title mb-1 d-inline-block">Pemantauan Koleksi</h3><br>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Koleksi</a></li>
                            <li class="breadcrumb-item active">Pemantauan</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="configuration">
                @if(session('success'))
					<div class="alert bg-success alert-icon-left alert-dismissible mb-2" role="alert">
						<span class="alert-icon"><i class="la la-check"></i></span>
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<strong>Success!</strong> {{ session('success') }}
					</div>
				@endif
                <div class="row">
                    <div class="col-md-4">
                        <a href="{{ url('admin/collection/monitoring?type=1') }}">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <i class="la la-book text-primary" style="font-size:100px;"></i>
                                        <h4 class="text-primary font-weight-bold">Pemantauan Buku</h4>
                                        <div class="text-muted">{{ $total_book }} koleksi</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('admin/collection/monitoring?type=2') }}">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <i class="la la-file-audio-o text-danger" style="font-size:100px;"></i>
                                        <h4 class="text-danger font-weight-bold">Pemantauan Partitur</h4>
                                        <div class="text-muted">{{ $total_partitur }} koleksi</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('admin/collection/monitoring?type=3') }}">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <i class="la la-map text-success" style="font-size:100px;"></i>
                                        <h4 class="text-success font-weight-bold">Pemantauan Peta</h4>
                                        <div class="text-muted">{{ $total_map }} koleksi</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('admin/collection/monitoring?type=4') }}">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <i class="la la-list-alt text-warning" style="font-size:100px;"></i>
                                        <h4 class="text-warning font-weight-bold">Pemantauan Serial</h4>
                                        <div class="text-muted">{{ $total_serial }} koleksi</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('admin/collection/monitoring?type=5') }}">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <i class="la la-music text-info" style="font-size:100px;"></i>
                                        <h4 class="text-info font-weight-bold">Pemantauan Audio</h4>
                                        <div class="text-muted">{{ $total_audio }} koleksi</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ url('admin/collection/monitoring?type=6') }}">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <i class="la la-film text-primary" style="font-size:100px;"></i>
                                        <h4 class="text-primary font-weight-bold">Pemantauan Film</h4>
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
