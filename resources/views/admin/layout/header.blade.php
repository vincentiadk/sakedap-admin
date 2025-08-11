<body class="vertical-layout vertical-menu-modern 2-columns menu-expanded fixed-navbar" data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">
	<nav class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow fixed-top navbar-semi-dark navbar-shadow">
		<div class="navbar-wrapper">
			<div class="navbar-header">
				<ul class="nav navbar-nav flex-row">
					<li class="nav-item mobile-menu d-md-none mr-auto">
						<a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#">
							<i class="ft-menu font-large-1"></i>
						</a>
					</li>
					<li class="nav-item mr-auto">
						<a class="navbar-brand" href="{{ url('admin/dashboard') }}">
							<img class="brand-logo" alt="Logo" src="{{ asset('main/logo.png') }}" style="max-width:36px; max-height:36px;">
							<h3 class="brand-text">eDeposit 5.0</h3>
						</a>
					</li>
					<li class="nav-item d-none d-md-block float-right">
						<a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
							<i class="toggle-icon ft-toggle-right font-medium-3 white" data-ticon="ft-toggle-right"></i>
						</a>
					</li>
					<li class="nav-item d-md-none">
						<a class="nav-link open-navbar-container" data-toggle="collapse" data-target="#navbar-mobile">
							<i class="la la-ellipsis-v"></i>
						</a>
					</li>
				</ul>
			</div>
			<div class="navbar-container content">
				<div class="collapse navbar-collapse" id="navbar-mobile">
					<ul class="nav navbar-nav mr-auto float-left">
						<li class="nav-item d-none d-md-block">
							<a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#">
								<i class="ft-menu"></i>
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="#">Last Login : {{ session('last_login') }}</a>
						</li>
					</ul>
					<ul class="nav navbar-nav float-right">
                        @php 
                            $totalPublisher = App\Models\Publisher::where('status', 1)->count();
                            $dataPublisher = App\Models\Publisher::where('status', 1)->limit(10)->latest('updated_at')->get();
                        @endphp
                        <li class="dropdown dropdown-notification nav-item">
                            <a class="nav-link nav-link-label" href="#" data-toggle="dropdown">
                                Pendaftaran Penerbit
                                <span class="badge badge-pill badge-danger">{{ $totalPublisher }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                                <li class="dropdown-menu-header">
                                    <h6 class="dropdown-header m-0">
                                        <span class="grey darken-2">10 Data Terbaru</span>
                                    </h6>
                                </li>
                                <li class="scrollable-container media-list w-100">
                                    @foreach($dataPublisher as $dp)
                                        <a href="javascript:void(0);" class="no-click">
                                            <div class="media">
                                                <div class="media-left align-self-center">
                                                    <i class="la la-file icon-bg-circle bg-primary mr-0"></i>
                                                </div>
                                                <div class="media-body">
                                                    <h6 class="media-heading">{{ $dp->name }}</h6>
                                                    <p class="notification-text font-small-3 text-muted">
                                                        {{ $dp->province->name ?? 'Tidak ada penerbit' }}
                                                    </p>
                                                    <small>
                                                        <time class="media-meta text-muted">
                                                            {{ $dp->updated_at->isoFormat('D MMMM Y') }}, Jam {{ $dp->updated_at->format('H:i') }} WIB
                                                        </time>
                                                    </small>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </li>
                                <li class="dropdown-menu-footer">
                                    <a class="dropdown-item text-muted text-center" href="{{ url('admin/publisher/monitoring') }}">Lihat Semua</a>
                                </li>
                            </ul>
                        </li>
                        @php 
                            $totalFile = App\Models\CollectionRequest::where('status', 1)->count();
                            $dataFile = App\Models\CollectionRequest::where('status', 1)->limit(10)->latest()->get();
                        @endphp
                        <li class="dropdown dropdown-notification nav-item">
                            <a class="nav-link nav-link-label" href="#" data-toggle="dropdown">
                                Permintaan File
                                <span class="badge badge-pill badge-danger">{{ $totalFile }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                                <li class="dropdown-menu-header">
                                    <h6 class="dropdown-header m-0">
                                        <span class="grey darken-2">10 Data Terbaru</span>
                                    </h6>
                                </li>
                                <li class="scrollable-container media-list w-100">
                                    @foreach($dataFile as $df)
                                        <a href="javascript:void(0)" class="no-click">
                                            <div class="media">
                                                <div class="media-left align-self-center">
                                                    <i class="la la-file icon-bg-circle bg-primary mr-0"></i>
                                                </div>
                                                <div class="media-body">
                                                    <h6 class="media-heading">{{ Str::limit($df->collection->title, 50) }}</h6>
                                                    <p class="notification-text font-small-3 text-muted">
                                                        {{ $df->collection->publisher->name ?? 'Tidak ada penerbit' }}
                                                    </p>
                                                    <small>
                                                        <time class="media-meta text-muted">
                                                            {{ $df->created_at->isoFormat('D MMMM Y') }}, Jam {{ $df->created_at->format('H:i') }} WIB
                                                        </time>
                                                    </small>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                </li>
                                <li class="dropdown-menu-footer">
                                    <a class="dropdown-item text-muted text-center" href="{{ url('admin/collection/request') }}">Lihat Semua</a>
                                </li>
                            </ul>
                        </li>
						<li class="dropdown dropdown-user nav-item">
							<a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
								<span class="mr-1">Hello,
									<span class="user-name text-bold-700">{{ session('fullname') }}</span>
								</span>
								<span class="avatar avatar-online">
									<img src="{{ asset('main/user.png') }}" alt="avatar">
								</span>
							</a>
							<div class="dropdown-menu dropdown-menu-right">
								<a class="dropdown-item" href="{{ url('admin/auth/profile') }}"><i class="ft-user"></i> Profile</a>
								<a class="dropdown-item" href="{{ url('admin/auth/change_password') }}"><i class="ft-lock"></i> Ganti Password</a>
								<a class="dropdown-item" href="{{ url('logout') }}"><i class="ft-power"></i> Keluar</a>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</nav>
