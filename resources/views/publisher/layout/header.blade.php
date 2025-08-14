<body class="vertical-layout vertical-menu 2-columns menu-expanded fixed-navbar" data-open="click" data-menu="vertical-menu" data-col="2-columns">
	<nav class="header-navbar navbar-expand-md navbar navbar-with-menu navbar-without-dd-arrow fixed-top navbar-semi-light bg-info navbar-shadow">
		<div class="navbar-wrapper">
			<div class="navbar-header">
				<ul class="nav navbar-nav flex-row">
					<li class="nav-item mobile-menu d-md-none mr-auto">
						<a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#">
							<i class="ft-menu font-large-1"></i>
						</a>
					</li>
					<li class="nav-item mr-auto">
						<a class="navbar-brand" href="{{ url('publisher/dashboard') }}">
							<img class="brand-logo" alt="Logo" src="{{ asset('/main/logo.png') }}" style="max-width:36px; max-height:36px;">
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
							<a class="nav-link" href="#">{{ date('d F Y') }}</a>
						</li>
					</ul>
					<ul class="nav navbar-nav float-right">
						<li class="dropdown dropdown-user nav-item">
							<a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
								<span class="mr-1">Hello,
									<span class="user-name text-bold-700">{{ session('fullname') }}</span>
								</span>
								<span class="avatar avatar-online">
									<img src="{{ asset('main/user.png') }}" alt="avatar">
								</span>
							</a>
							@php
								$countNotifUnread = \App\Model\Notification::where('user_id', session('id'))->whereNull('read_at')->count();
							@endphp
							<div class="dropdown-menu dropdown-menu-right">
								<a class="dropdown-item" href="{{ url('publisher/auth/profile') }}"><i class="ft-user"></i> Profile</a>
								<a class="dropdown-item" href="{{ url('publisher/notification') }}"><i class="ft-bell"></i> Notifikasi
								@if($countNotifUnread > 0)
								<div class="badge badge-pill badge-danger">{{ $countNotifUnread }}</div>
								@endif
								</a>
								<a class="dropdown-item" href="https://api-interoperabilitas.perpusnas.go.id/sso/change-password"><i class="ft-lock"></i> Ganti Password</a>
								<a class="dropdown-item" href="{{ url('logout') }}"><i class="ft-power"></i> Keluar</a>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</nav>
