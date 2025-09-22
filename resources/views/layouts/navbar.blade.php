<body>
	<div class="navbar navbar-expand-xl navbar-static shadow iframeable">
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
                                                        <i class="ph-chart-bar me-2"></i>
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
                                                    <a href="#menu-executor" class="nav-link rounded {{ Request::segment(1) == 'executor' ? 'active' : '' }}" data-bs-toggle="tab" aria-selected="{{ Request::segment(1) == 'executor' ? 'true' : 'false' }}" role="tab">
                                                        <i class="ph-users-four me-2"></i>
                                                        Pelaksana Serah
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="{{ url('bill-isbn') }}" class="nav-link rounded {{ Request::segment(1) == 'bill-isbn' ? 'active' : '' }}">
                                                        <i class="ph-cardholder me-2"></i>
                                                        Tagihan ISBN
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="#menu-collection" class="nav-link rounded {{ Request::segment(1) == 'collection' ? 'active' : '' }}" data-bs-toggle="tab" aria-selected="{{ Request::segment(1) == 'collection' ? 'true' : 'false' }}" role="tab">
                                                        <i class="ph-books me-2"></i>
                                                        Koleksi
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="{{ url('promotion') }}" class="nav-link rounded {{ Request::segment(1) == 'promotion' ? 'active' : '' }}">
                                                        <i class="ph-ticket me-2"></i>
                                                        Promosi
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="#menu-delivery" class="nav-link rounded {{ Request::segment(1) == 'delivery' ? 'active' : '' }}" data-bs-toggle="tab" aria-selected="{{ Request::segment(1) == 'delivery' ? 'true' : 'false' }}" role="tab">
                                                        <i class="ph-truck me-2"></i>
                                                        Pengiriman
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="#menu-supervision" class="nav-link rounded {{ Request::segment(1) == 'supervision' ? 'active' : '' }}" data-bs-toggle="tab" aria-selected="{{ Request::segment(1) == 'supervision' ? 'true' : 'false' }}" role="tab">
                                                        <i class="ph-eye me-2"></i>
                                                        Pengawasan
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="{{ url('request-file') }}" class="nav-link rounded {{ Request::segment(1) == 'request-file' ? 'active' : '' }}">
                                                        <i class="ph-file-plus me-2"></i>
                                                        Permintaan File
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="{{ url('template-email') }}" class="nav-link rounded {{ Request::segment(1) == 'template-email' ? 'active' : '' }}">
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
                                                <a href="{{ url('master-data/category') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'master-data' && Request::segment(2) == 'category' ? 'active' : '' }}">Kategori</a>
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
                                                <a href="{{ url('library/depo') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'library' && Request::segment(2) == 'depo' ? 'active' : '' }}">Depo</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane dropdown-scrollable-xl fade p-3 {{ Request::segment(1) == 'executor' ? 'show active' : '' }}" id="menu-executor" role="tabpanel">
                                        <div class="row" style="max-height:55vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-lg-4 mb-3 mb-lg-0">
                                                <div class="fw-bold border-bottom pb-2 mb-2">Sub Menu</div>
                                                <a href="{{ url('executor/create-data') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'executor' && Request::segment(2) == 'create-data' ? 'active' : '' }}">Tambah Data</a>
                                                <a href="{{ url('executor/review') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'executor' && Request::segment(2) == 'review' ? 'active' : '' }}">Peninjauan</a>
                                                <a href="{{ url('executor/manage') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'executor' && Request::segment(2) == 'manage' ? 'active' : '' }}">Pengelolaan</a>
                                                <a href="{{ url('executor/warning') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'executor' && Request::segment(2) == 'warning' ? 'active' : '' }}">Teguran</a>
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
                                                <a href="{{ url('delivery/list') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'delivery' && Request::segment(2) == 'list' ? 'active' : '' }}">Daftar Pengiriman</a>
                                                <a href="{{ url('delivery/sent') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'delivery' && Request::segment(2) == 'sent' ? 'active' : '' }}">Dalam Pengiriman</a>
                                                <a href="{{ url('delivery/accepted') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'delivery' && Request::segment(2) == 'accepted' ? 'active' : '' }}">Koleksi Diterima</a>
                                                <a href="{{ url('delivery/receipt') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'delivery' && Request::segment(2) == 'receipt' ? 'active' : '' }}">Bukti Penerimaan</a>
                                                <a href="{{ url('delivery/reject') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'delivery' && Request::segment(2) == 'reject' ? 'active' : '' }}">Koleksi Ditolak</a>
                                                <a href="{{ url('delivery/grant') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'delivery' && Request::segment(2) == 'grant' ? 'active' : '' }}">Koleksi Dihibahkan</a>
                                                <a href="{{ url('delivery/retur') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'delivery' && Request::segment(2) == 'retur' ? 'active' : '' }}">Koleksi Dikembalikan</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane dropdown-scrollable-xl fade p-3 {{ Request::segment(1) == 'supervision' ? 'show active' : '' }}" id="menu-supervision" role="tabpanel">
                                        <div class="row" style="max-height:55vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-lg-4 mb-3 mb-lg-0">
                                                <div class="fw-bold border-bottom pb-2 mb-2">Sub Menu</div>
                                                <a href="{{ url('supervision/compliance') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'supervision' && Request::segment(2) == 'compliance' ? 'active' : '' }}">Kepatuhan</a>
                                                <a href="{{ url('supervision/coaching') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'supervision' && Request::segment(2) == 'coaching' ? 'active' : '' }}">Pembinaan</a>
                                                <a href="{{ url('supervision/monitoring') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'supervision' && Request::segment(2) == 'monitoring' ? 'active' : '' }}">Pemantauan</a>
                                            </div>
                                        </div>
                                    </div>
									<div class="tab-pane dropdown-scrollable-xl fade p-3 {{ Request::segment(1) == 'report' ? 'show active' : '' }}" id="menu-report" role="tabpanel">
                                        <div class="row" style="max-height:55vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-lg-4 mb-3 mb-lg-0">
                                                <div class="fw-bold border-bottom pb-2 mb-2">Sub Menu</div>
                                                <a href="{{ url('report/periodic') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'periodic' ? 'active' : '' }}">Periodik</a>
                                                <a href="{{ url('report/executor') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'executor' ? 'active' : '' }}">Pelaksana Serah</a>
                                                <a href="{{ url('report/collection') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'collection' ? 'active' : '' }}">Koleksi</a>
                                                <a href="{{ url('report/performance-user') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'performance-user' ? 'active' : '' }}">Performa User</a>
                                                <a href="{{ url('report/log') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'log' ? 'active' : '' }}">Log</a>
                                                <a href="{{ url('report/promotion') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'promotion' ? 'active' : '' }}">Promosi</a>
                                                <a href="{{ url('report/postage') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'postage' ? 'active' : '' }}">Ongkir</a>
                                                <a href="{{ url('report/warning') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'warning' ? 'active' : '' }}">Teguran</a>
                                                <a href="{{ url('report/download') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'download' ? 'active' : '' }}">Unduhan</a>
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
                                                <a href="{{ url('setting/terms-conditions') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'setting' && Request::segment(2) == 'terms-conditions' ? 'active' : '' }}">Syarat & Ketentuan</a>
                                                <a href="{{ url('setting/about-us') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'setting' && Request::segment(2) == 'about-us' ? 'active' : '' }}">Tentang Kami</a>
                                                <a href="{{ url('setting/header-email') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'setting' && Request::segment(2) == 'header-email' ? 'active' : '' }}">Header Email</a>
                                                <a href="{{ url('setting/footer-email') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'setting' && Request::segment(2) == 'footer-email' ? 'active' : '' }}">Footer Email</a>
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
						<span class="badge bg-yellow text-black position-absolute top-0 end-0 translate-middle-top zindex-1 rounded-pill mt-1 me-1" id="notification-header-executor-total"></span>
					</a>
					<div class="dropdown-menu dropdown-menu-end wmin-lg-400 p-0">
						<div class="d-flex align-items-center p-3">
							<h6 class="mb-0">Notifikasi Pendaftaran Pelaksana Serah</h6>
						</div>
						<div class="dropdown-menu-scrollable pb-2" id="notification-header-executor-list"></div>
						<div class="d-flex border-top py-2 px-3">
							<a href="{{ url('executor/review') }}" class="text-body mx-auto">
								Lihat Selengkapnya
								<i class="ph-arrow-circle-right ms-1"></i>
							</a>
						</div>
					</div>
                </li>
                <li class="nav-item nav-item-dropdown-lg dropdown me-lg-3">
                    <a href="#" class="navbar-nav-link navbar-nav-link-icon rounded-pill" data-bs-toggle="dropdown" data-bs-auto-close="outside">
						<i class="ph-file-plus"></i>
						<span class="badge bg-yellow text-black position-absolute top-0 end-0 translate-middle-top zindex-1 rounded-pill mt-1 me-1" id="notification-header-file-total"></span>
					</a>
					<div class="dropdown-menu dropdown-menu-end wmin-lg-400 p-0">
						<div class="d-flex align-items-center p-3">
							<h6 class="mb-0">Notifikasi Permintaan File</h6>
						</div>
						<div class="dropdown-menu-scrollable pb-2" id="notification-header-file-list"></div>
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
