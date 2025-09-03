<body>
	<div class="navbar navbar-expand-xl navbar-static shadow">
		<div class="container-fluid">
			<div class="navbar-brand flex-1">
				<a href="{{ url('home') }}" class="d-inline-flex align-items-center">
					<img src="{{ asset('assets/icon.png') }}" alt="Logo">
                    <span class="ms-2 fs-4 pt-1 text-dark fw-bold">E-DEPOSIT 5.0</span>
				</a>
			</div>
			<div class="d-flex w-100 w-xl-auto overflow-auto overflow-xl-visible scrollbar-hidden border-top border-top-xl-0 order-1 order-xl-0 pt-2 pt-xl-0 mt-2 mt-xl-0">
				<ul class="nav gap-1 justify-content-center flex-nowrap flex-xl-wrap mx-auto">
					<li class="nav-item nav-item-dropdown">
						<a href="#" class="navbar-nav-link dropdown-toggle rounded" data-bs-toggle="dropdown" data-bs-auto-close="outside">
							<i class="ph-list me-2"></i>
							Menu
						</a>
						<div class="dropdown-menu p-0">
							<div class="d-flex">
								<div class="d-flex flex-row flex-xl-column bg-light overflow-auto overflow-xl-visible rounded-top rounded-top-xl-0 rounded-start-xl">
									<div class="flex-1 border-bottom border-bottom-xl-0 p-2 p-xl-3">
                                        <div class="fw-bold border-bottom d-none d-xl-block pb-2 mb-2">Main Menu</div>
                                        <div style="max-height:50vh; overflow-y:auto; overflow-x:hidden;">
                                            <ul class="nav nav-pills flex-xl-column flex-nowrap text-nowrap justify-content-center wmin-xl-300" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <a href="{{ url('dashboard') }}" class="nav-link rounded {{ Request::segment(1) == 'dashboard' ? 'active' : '' }}">
                                                        <i class="ph-house me-2"></i>
                                                        Dasboard
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="#menu-master-data" class="nav-link rounded {{ Request::segment(1) == 'master-data' ? 'active' : '' }}" data-bs-toggle="tab" aria-selected="{{ Request::segment(1) == 'master-data' ? 'true' : 'false' }}" role="tab">
                                                        <i class="ph-archive me-2"></i>
                                                        Master Data
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="#menu-location" class="nav-link rounded {{ Request::segment(1) == 'location' ? 'active' : '' }}" data-bs-toggle="tab" aria-selected="{{ Request::segment(1) == 'location' ? 'true' : 'false' }}" role="tab">
                                                        <i class="ph-buildings me-2"></i>
                                                        Lokasi
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="#menu-library" class="nav-link rounded {{ Request::segment(1) == 'library' ? 'active' : '' }}" data-bs-toggle="tab" aria-selected="{{ Request::segment(1) == 'library' ? 'true' : 'false' }}" role="tab">
                                                        <i class="ph-book-open me-2"></i>
                                                        Perpustakaan
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="#menu-manager" class="nav-link rounded {{ Request::segment(1) == 'manager' ? 'active' : '' }}" data-bs-toggle="tab" aria-selected="{{ Request::segment(1) == 'manager' ? 'true' : 'false' }}" role="tab">
                                                        <i class="ph-users-four me-2"></i>
                                                        Pengelola
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="#menu-collection" class="nav-link rounded {{ Request::segment(1) == 'collection' ? 'active' : '' }}" data-bs-toggle="tab" aria-selected="{{ Request::segment(1) == 'collection' ? 'true' : 'false' }}" role="tab">
                                                        <i class="ph-books me-2"></i>
                                                        Koleksi
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="#menu-delivery" class="nav-link rounded {{ Request::segment(1) == 'delivery' ? 'active' : '' }}" data-bs-toggle="tab" aria-selected="{{ Request::segment(1) == 'delivery' ? 'true' : 'false' }}" role="tab">
                                                        <i class="ph-truck me-2"></i>
                                                        Pengiriman
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="{{ url('bill-isbn') }}" class="nav-link rounded {{ Request::segment(1) == 'bill-isbn' ? 'active' : '' }}">
                                                        <i class="ph-cardholder me-2"></i>
                                                        Tagihan ISBN
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="{{ url('request-file') }}" class="nav-link rounded {{ Request::segment(1) == 'request-file' ? 'active' : '' }}">
                                                        <i class="ph-file-plus me-2"></i>
                                                        Permintaan File
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="#menu-template-email" class="nav-link rounded {{ Request::segment(1) == 'template-email' ? 'active' : '' }}" data-bs-toggle="tab" aria-selected="{{ Request::segment(1) == 'template-email' ? 'true' : 'false' }}" role="tab">
                                                        <i class="ph-envelope me-2"></i>
                                                        Template Email
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="#menu-report" class="nav-link rounded {{ Request::segment(1) == 'report' ? 'active' : '' }}" data-bs-toggle="tab" aria-selected="{{ Request::segment(1) == 'report' ? 'true' : 'false' }}" role="tab">
                                                        <i class="ph-notebook me-2"></i>
                                                        Laporan
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="#menu-setting" class="nav-link rounded {{ Request::segment(1) == 'setting' ? 'active' : '' }}" data-bs-toggle="tab" aria-selected="{{ Request::segment(1) == 'setting' ? 'true' : 'false' }}" role="tab">
                                                        <i class="ph-gear me-2"></i>
                                                        Pengaturan
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
									</div>
								</div>
								<div class="tab-content flex-xl-1 main-menu-sub">
                                    <div class="tab-pane dropdown-scrollable-xl fade p-3 {{ Request::segment(1) == 'master-data' ? 'show active' : '' }}" id="menu-master-data" role="tabpanel">
                                        <div class="row" style="max-height:55vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-lg-4 mb-3 mb-lg-0">
                                                <div class="fw-bold border-bottom pb-2 mb-2">Sub Menu</div>
                                                <a href="{{ url('master-data/visit') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'master-data' && Request::segment(2) == 'visit' ? 'active' : '' }}">Kunjungan</a>
                                                <a href="{{ url('master-data/problem') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'master-data' && Request::segment(2) == 'problem' ? 'active' : '' }}">Masalah</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane dropdown-scrollable-xl fade p-3 {{ Request::segment(1) == 'location' ? 'show active' : '' }}" id="menu-location" role="tabpanel">
                                        <div class="row" style="max-height:55vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-lg-4 mb-3 mb-lg-0">
                                                <div class="fw-bold border-bottom pb-2 mb-2">Sub Menu</div>
                                                <a href="{{ url('location/province') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'location' && Request::segment(2) == 'province' ? 'active' : '' }}">Provinsi</a>
                                                <a href="{{ url('location/city') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'location' && Request::segment(2) == 'city' ? 'active' : '' }}">Kota / Kabupaten</a>
                                                <a href="{{ url('location/district') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'location' && Request::segment(2) == 'district' ? 'active' : '' }}">Kecamatan</a>
                                                <a href="{{ url('location/village') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'location' && Request::segment(2) == 'village' ? 'active' : '' }}">Kelurahan / Desa</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane dropdown-scrollable-xl fade p-3 {{ Request::segment(1) == 'library' ? 'show active' : '' }}" id="menu-library" role="tabpanel">
                                        <div class="row" style="max-height:55vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-lg-4 mb-3 mb-lg-0">
                                                <div class="fw-bold border-bottom pb-2 mb-2">Sub Menu</div>
                                                <a href="{{ url('library/data') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'library' && Request::segment(2) == 'data' ? 'active' : '' }}">Data</a>
                                                <a href="{{ url('library/location') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'library' && Request::segment(2) == 'location' ? 'active' : '' }}">Lokasi</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane dropdown-scrollable-xl fade p-3 {{ Request::segment(1) == 'manager' ? 'show active' : '' }}" id="menu-manager" role="tabpanel">
                                        <div class="row" style="max-height:55vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-lg-4 mb-3 mb-lg-0">
                                                <div class="fw-bold border-bottom pb-2 mb-2">Sub Menu</div>
                                                <a href="{{ url('manager/create-data') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'manager' && Request::segment(2) == 'create-data' ? 'active' : '' }}">Tambah Data</a>
                                                <a href="{{ url('manager/review') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'manager' && Request::segment(2) == 'review' ? 'active' : '' }}">Peninjauan</a>
                                                <a href="{{ url('manager/manage') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'manager' && Request::segment(2) == 'manage' ? 'active' : '' }}">Pengelolaan</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane dropdown-scrollable-xl fade p-3 {{ Request::segment(1) == 'collection' ? 'show active' : '' }}" id="menu-collection" role="tabpanel">
                                        <div class="row" style="max-height:55vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-lg-4 mb-3 mb-lg-0">
                                                <div class="fw-bold border-bottom pb-2 mb-2">Sub Menu</div>
                                                <a href="{{ url('collection/create-single') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'collection' && Request::segment(2) == 'create-single' ? 'active' : '' }}">Tambah Tunggal</a>
                                                <a href="{{ url('collection/create-more') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'collection' && Request::segment(2) == 'create-more' ? 'active' : '' }}">Tambah Banyak</a>
                                                <a href="{{ url('collection/reject') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'collection' && Request::segment(2) == 'reject' ? 'active' : '' }}">Ditolak</a>
                                                <a href="{{ url('collection/problem') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'collection' && Request::segment(2) == 'problem' ? 'active' : '' }}">Bermasalah</a>
                                                <a href="{{ url('collection/review') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'collection' && Request::segment(2) == 'review' ? 'active' : '' }}">Peninjauan</a>
                                                <a href="{{ url('collection/digital-work') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'collection' && Request::segment(2) == 'digital-work' ? 'active' : '' }}">Karya Digital</a>
                                                <a href="{{ url('collection/printed-work') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'collection' && Request::segment(2) == 'printed-work' ? 'active' : '' }}">Karya Cetak</a>
                                                <a href="{{ url('collection/analog-work') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'collection' && Request::segment(2) == 'analog-work' ? 'active' : '' }}">Karya Analog</a>
                                                <a href="{{ url('collection/label') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'collection' && Request::segment(2) == 'label' ? 'active' : '' }}">Label</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane dropdown-scrollable-xl fade p-3 {{ Request::segment(1) == 'delivery' ? 'show active' : '' }}" id="menu-delivery" role="tabpanel">
                                        <div class="row" style="max-height:55vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-lg-4 mb-3 mb-lg-0">
                                                <div class="fw-bold border-bottom pb-2 mb-2">Sub Menu</div>
                                                <a href="{{ url('delivery/delivered') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'delivery' && Request::segment(2) == 'delivered' ? 'active' : '' }}">Diantar</a>
                                                <a href="{{ url('delivery/independent') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'delivery' && Request::segment(2) == 'independent' ? 'active' : '' }}">Mandiri</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane dropdown-scrollable-xl fade p-3 {{ Request::segment(1) == 'template-email' ? 'show active' : '' }}" id="menu-template-email" role="tabpanel">
                                        <div class="row" style="max-height:55vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-lg-4 mb-3 mb-lg-0">
                                                <div class="fw-bold border-bottom pb-2 mb-2">Sub Menu</div>
                                                <a href="{{ url('template-email/header') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'template-email' && Request::segment(2) == 'header' ? 'active' : '' }}">Header</a>
                                                <a href="{{ url('template-email/footer') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'template-email' && Request::segment(2) == 'footer' ? 'active' : '' }}">Footer</a>
                                                <a href="{{ url('template-email/receipt') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'template-email' && Request::segment(2) == 'receipt' ? 'active' : '' }}">Tanda Terima</a>
                                                <a href="{{ url('template-email/activation') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'template-email' && Request::segment(2) == 'activation' ? 'active' : '' }}">Aktivasi</a>
                                                <a href="{{ url('template-email/change-password') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'template-email' && Request::segment(2) == 'change-password' ? 'active' : '' }}">Ganti Password</a>
                                                <a href="{{ url('template-email/manager-reject') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'template-email' && Request::segment(2) == 'manager-reject' ? 'active' : '' }}">Pengelola Ditolak</a>
                                                <a href="{{ url('template-email/manager-submission') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'template-email' && Request::segment(2) == 'manager-submission' ? 'active' : '' }}">Pengelola Pengajuan</a>
                                                <a href="{{ url('template-email/manager-accept') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'template-email' && Request::segment(2) == 'manager-accept' ? 'active' : '' }}">Pengelola Diterima</a>
                                                <a href="{{ url('template-email/collection-problem') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'template-email' && Request::segment(2) == 'collection-problem' ? 'active' : '' }}">Koleksi Bermasalah</a>
                                                <a href="{{ url('template-email/collection-submitted') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'template-email' && Request::segment(2) == 'collection-submitted' ? 'active' : '' }}">Koleksi Diserahkan</a>
                                                <a href="{{ url('template-email/collection-accept') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'template-email' && Request::segment(2) == 'collection-accept' ? 'active' : '' }}">Koleksi Diterima</a>
                                            </div>
                                        </div>
                                    </div>
									<div class="tab-pane dropdown-scrollable-xl fade p-3 {{ Request::segment(1) == 'report' ? 'show active' : '' }}" id="menu-report" role="tabpanel">
                                        <div class="row" style="max-height:55vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-lg-4 mb-3 mb-lg-0">
                                                <div class="fw-bold border-bottom pb-2 mb-2">Sub Menu</div>
                                                <a href="{{ url('report/periodic') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'periodic' ? 'active' : '' }}">Periodik</a>
                                                <a href="{{ url('report/manager') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'manager' ? 'active' : '' }}">Pengelola</a>
                                                <a href="{{ url('report/collection') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'collection' ? 'active' : '' }}">Koleksi</a>
                                                <a href="{{ url('report/perfomance-user') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'perfomance-user' ? 'active' : '' }}">Performa User</a>
                                                <a href="{{ url('report/download') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'download' ? 'active' : '' }}">Unduhan</a>
                                                <a href="{{ url('report/log') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'log' ? 'active' : '' }}">Log</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane dropdown-scrollable-xl fade p-3 {{ Request::segment(1) == 'setting' ? 'show active' : '' }}" id="menu-setting" role="tabpanel">
                                        <div class="row" style="max-height:55vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-lg-4 mb-3 mb-lg-0">
                                                <div class="fw-bold border-bottom pb-2 mb-2">Sub Menu</div>
                                                <a href="{{ url('setting/leader') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'setting' && Request::segment(2) == 'leader' ? 'active' : '' }}">Pimpinan</a>
                                                <a href="{{ url('setting/banner') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'setting' && Request::segment(2) == 'banner' ? 'active' : '' }}">Banner</a>
                                                <a href="{{ url('setting/faq') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'setting' && Request::segment(2) == 'faq' ? 'active' : '' }}">FAQ</a>
                                                <a href="{{ url('setting/terms_conditions') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'setting' && Request::segment(2) == 'terms_conditions' ? 'active' : '' }}">Syarat & Ketentuan</a>
                                                <a href="{{ url('setting/about-us') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'setting' && Request::segment(2) == 'about-us' ? 'active' : '' }}">Tentang Kami</a>
                                            </div>
                                        </div>
                                    </div>
								</div>
							</div>
						</div>
					</li>
                    <li class="nav-item">
						<a href="javascript:void(0);" class="navbar-nav-link text-danger rounded" onclick="logout()">
							<i class="ph-sign-out me-2"></i>
							Keluar
						</a>
					</li>
				</ul>
			</div>
			<ul class="nav gap-1 flex-xl-1 justify-content-end order-0 order-xl-1">
                <li class="nav-item nav-item-dropdown-lg dropdown me-lg-1">
                    <a href="#" class="navbar-nav-link navbar-nav-link-icon rounded-pill" data-bs-toggle="dropdown" data-bs-auto-close="outside">
						<i class="ph-users-four"></i>
						<span class="badge bg-yellow text-black position-absolute top-0 end-0 translate-middle-top zindex-1 rounded-pill mt-1 me-1">0</span>
					</a>
					<div class="dropdown-menu dropdown-menu-end wmin-lg-400 p-0">
						<div class="d-flex align-items-center p-3">
							<h6 class="mb-0">Notifikasi Pendaftaran Pengelola</h6>
						</div>
						<div class="dropdown-menu-scrollable pb-2">
							<a href="#" class="dropdown-item align-items-start text-wrap py-2">
								<div class="me-3">
                                    <img src="{{ asset('assets/team.png') }}" class="w-40px h-40px" alt="">
								</div>
								<div class="flex-1">
									<span class="fw-semibold">James Alexander</span>
									<span class="text-muted float-end fs-sm">04:58</span>
									<div class="text-muted">who knows, maybe that would be the best thing for me...</div>
								</div>
							</a>
						</div>
						<div class="d-flex border-top py-2 px-3">
							<a href="{{ url('manager/review') }}" class="text-body mx-auto">
								Lihat Selengkapnya
								<i class="ph-arrow-circle-right ms-1"></i>
							</a>
						</div>
					</div>
                </li>
                <li class="nav-item nav-item-dropdown-lg dropdown me-lg-3">
                    <a href="#" class="navbar-nav-link navbar-nav-link-icon rounded-pill" data-bs-toggle="dropdown" data-bs-auto-close="outside">
						<i class="ph-file-plus"></i>
						<span class="badge bg-yellow text-black position-absolute top-0 end-0 translate-middle-top zindex-1 rounded-pill mt-1 me-1">0</span>
					</a>
					<div class="dropdown-menu dropdown-menu-end wmin-lg-400 p-0">
						<div class="d-flex align-items-center p-3">
							<h6 class="mb-0">Notifikasi Permintaan File</h6>
						</div>
						<div class="dropdown-menu-scrollable pb-2">
							<a href="#" class="dropdown-item align-items-start text-wrap py-2">
								<div class="me-3">
                                    <img src="{{ asset('assets/demand.png') }}" class="w-40px h-40px" alt="">
								</div>
								<div class="flex-1">
									<span class="fw-semibold">James Alexander</span>
									<span class="text-muted float-end fs-sm">04:58</span>
									<div class="text-muted">who knows, maybe that would be the best thing for me...</div>
								</div>
							</a>
						</div>
						<div class="d-flex border-top py-2 px-3">
							<a href="{{ url('request-file') }}" class="text-body mx-auto">
								Lihat Selengkapnya
								<i class="ph-arrow-circle-right ms-1"></i>
							</a>
						</div>
					</div>
                </li>
				<li class="nav-item">
					<a href="javascript:void(0);" class="navbar-nav-link align-items-center rounded-pill p-1 bg-transparent no-click">
                        <img src="{{ asset('assets/user.png') }}" class="w-32px h-32px rounded-pill" alt="">
						<span class="d-none d-md-inline-block mx-md-2">{{ session('name') }}</span>
					</a>
				</li>
			</ul>
		</div>
	</div>
    <div class="page-content">
        <div class="content-wrapper">
            <div class="content-inner">
