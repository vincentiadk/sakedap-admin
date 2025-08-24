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
                                        <div style="max-height:35vh; overflow-y:auto; overflow-x:hidden;">
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
                                                    <a href="#menu-publisher" class="nav-link rounded {{ Request::segment(1) == 'publisher' ? 'active' : '' }}" data-bs-toggle="tab" aria-selected="{{ Request::segment(1) == 'publisher' ? 'true' : 'false' }}" role="tab">
                                                        <i class="ph-users-four me-2"></i>
                                                        Penerbit
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="#menu-collection" class="nav-link rounded {{ Request::segment(1) == 'collection' ? 'active' : '' }}" data-bs-toggle="tab" aria-selected="{{ Request::segment(1) == 'collection' ? 'true' : 'false' }}" role="tab">
                                                        <i class="ph-books me-2"></i>
                                                        Koleksi
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="#menu-kckra" class="nav-link rounded {{ Request::segment(1) == 'kckra' ? 'active' : '' }}" data-bs-toggle="tab" aria-selected="{{ Request::segment(1) == 'kckra' ? 'true' : 'false' }}" role="tab">
                                                        <i class="ph-book me-2"></i>
                                                        KCKRA
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="{{ url('article') }}" class="nav-link rounded {{ Request::segment(1) == 'article' ? 'active' : '' }}">
                                                        <i class="ph-newspaper me-2"></i>
                                                        Artikel
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="{{ url('bill-isbn') }}" class="nav-link rounded {{ Request::segment(1) == 'bill-isbn' ? 'active' : '' }}">
                                                        <i class="ph-cardholder me-2"></i>
                                                        Tagihan ISBN
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="{{ url('isrc') }}" class="nav-link rounded {{ Request::segment(1) == 'isrc' ? 'active' : '' }}">
                                                        <i class="ph-music-note me-2"></i>
                                                        ISRC
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="{{ url('award') }}" class="nav-link rounded {{ Request::segment(1) == 'award' ? 'active' : '' }}">
                                                        <i class="ph-trophy me-2"></i>
                                                        Penghargaan
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
                                        <div class="row" style="max-height:40vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-lg-4 mb-3 mb-lg-0">
                                                <div class="fw-bold border-bottom pb-2 mb-2">Sub Menu</div>
                                                <a href="{{ url('master-data/visit') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'master-data' && Request::segment(2) == 'visit' ? 'active' : '' }}">Kunjungan</a>
                                                <a href="{{ url('master-data/contributor') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'master-data' && Request::segment(2) == 'contributor' ? 'active' : '' }}">Kontributor</a>
                                                <a href="{{ url('master-data/category') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'master-data' && Request::segment(2) == 'category' ? 'active' : '' }}">Kategori</a>
                                                <a href="{{ url('master-data/subject') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'master-data' && Request::segment(2) == 'subject' ? 'active' : '' }}">Subjek</a>
                                                <a href="{{ url('master-data/problem') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'master-data' && Request::segment(2) == 'problem' ? 'active' : '' }}">Masalah</a>
                                                <a href="{{ url('master-data/author') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'master-data' && Request::segment(2) == 'author' ? 'active' : '' }}">Pengarang</a>
                                                <a href="{{ url('master-data/organization') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'master-data' && Request::segment(2) == 'organization' ? 'active' : '' }}">Organisasi</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane dropdown-scrollable-xl fade p-3 {{ Request::segment(1) == 'location' ? 'show active' : '' }}" id="menu-location" role="tabpanel">
                                        <div class="row" style="max-height:40vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-lg-4 mb-3 mb-lg-0">
                                                <div class="fw-bold border-bottom pb-2 mb-2">Sub Menu</div>
                                                <a href="{{ url('location/province') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'location' && Request::segment(2) == 'province' ? 'active' : '' }}">Provinsi</a>
                                                <a href="{{ url('location/city') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'location' && Request::segment(2) == 'city' ? 'active' : '' }}">Kota / Kabupaten</a>
                                                <a href="{{ url('location/district') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'location' && Request::segment(2) == 'district' ? 'active' : '' }}">Kecamatan</a>
                                                <a href="{{ url('location/village') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'location' && Request::segment(2) == 'village' ? 'active' : '' }}">Kelurahan / Desa</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane dropdown-scrollable-xl fade p-3 {{ Request::segment(1) == 'publisher' ? 'show active' : '' }}" id="menu-publisher" role="tabpanel">
                                        <div class="row" style="max-height:40vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-lg-4 mb-3 mb-lg-0">
                                                <div class="fw-bold border-bottom pb-2 mb-2">Sub Menu</div>
                                                <a href="{{ url('publisher/group') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'publisher' && Request::segment(2) == 'group' ? 'active' : '' }}">Grup</a>
                                                <a href="{{ url('publisher/access') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'publisher' && Request::segment(2) == 'access' ? 'active' : '' }}">Akses</a>
                                                <a href="{{ url('publisher/warning') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'publisher' && Request::segment(2) == 'warning' ? 'active' : '' }}">Teguran</a>
                                                <a href="{{ url('publisher/create-data') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'publisher' && Request::segment(2) == 'create-data' ? 'active' : '' }}">Tambah Data</a>
                                                <a href="{{ url('publisher/review') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'publisher' && Request::segment(2) == 'review' ? 'active' : '' }}">Peninjauan</a>
                                                <a href="{{ url('publisher/manage') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'publisher' && Request::segment(2) == 'manage' ? 'active' : '' }}">Pengelolaan</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane dropdown-scrollable-xl fade p-3 {{ Request::segment(1) == 'collection' ? 'show active' : '' }}" id="menu-collection" role="tabpanel">
                                        <div class="row" style="max-height:40vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-lg-4 mb-3 mb-lg-0">
                                                <div class="fw-bold border-bottom pb-2 mb-2">Sub Menu</div>
                                                <a href="{{ url('collection/create-single') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'collection' && Request::segment(2) == 'create-single' ? 'active' : '' }}">Tambah Tunggal</a>
                                                <a href="{{ url('collection/create-more') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'collection' && Request::segment(2) == 'create-more' ? 'active' : '' }}">Tambah Banyak</a>
                                                <a href="{{ url('collection/reject') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'collection' && Request::segment(2) == 'reject' ? 'active' : '' }}">Ditolak</a>
                                                <a href="{{ url('collection/problem') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'collection' && Request::segment(2) == 'problem' ? 'active' : '' }}">Bermasalah</a>
                                                <a href="{{ url('collection/review') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'collection' && Request::segment(2) == 'review' ? 'active' : '' }}">Peninjauan</a>
                                                <a href="{{ url('collection/accept') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'collection' && Request::segment(2) == 'accept' ? 'active' : '' }}">Diterima</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane dropdown-scrollable-xl fade p-3 {{ Request::segment(1) == 'kckra' ? 'show active' : '' }}" id="menu-kckra" role="tabpanel">
                                        <div class="row" style="max-height:40vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-lg-4 mb-3 mb-lg-0">
                                                <div class="fw-bold border-bottom pb-2 mb-2">Sub Menu</div>
                                                <a href="{{ url('kckra/depo') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'kckra' && Request::segment(2) == 'depo' ? 'active' : '' }}">Depo</a>
                                                <a href="{{ url('kckra/label') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'kckra' && Request::segment(2) == 'label' ? 'active' : '' }}">Label</a>
                                                <a href="{{ url('kckra/create-single') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'kckra' && Request::segment(2) == 'create-single' ? 'active' : '' }}">Tambah Tunggal</a>
                                                <a href="{{ url('kckra/create-more') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'kckra' && Request::segment(2) == 'create-more' ? 'active' : '' }}">Tambah Banyak</a>
                                                <a href="{{ url('kckra/reject') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'kckra' && Request::segment(2) == 'reject' ? 'active' : '' }}">Ditolak</a>
                                                <a href="{{ url('kckra/accept') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'kckra' && Request::segment(2) == 'accept' ? 'active' : '' }}">Diterima</a>
                                                <a href="{{ url('kckra/internal') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'kckra' && Request::segment(2) == 'internal' ? 'active' : '' }}">Internal</a>
                                                <a href="{{ url('kckra/external') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'kckra' && Request::segment(2) == 'external' ? 'active' : '' }}">Eksternal</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane dropdown-scrollable-xl fade p-3 {{ Request::segment(1) == 'template-email' ? 'show active' : '' }}" id="menu-template-email" role="tabpanel">
                                        <div class="row" style="max-height:40vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-lg-4 mb-3 mb-lg-0">
                                                <div class="fw-bold border-bottom pb-2 mb-2">Sub Menu</div>
                                                <a href="{{ url('template-email/header') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'template-email' && Request::segment(2) == 'header' ? 'active' : '' }}">Header</a>
                                                <a href="{{ url('template-email/footer') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'template-email' && Request::segment(2) == 'footer' ? 'active' : '' }}">Footer</a>
                                                <a href="{{ url('template-email/receipt') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'template-email' && Request::segment(2) == 'receipt' ? 'active' : '' }}">Tanda Terima</a>
                                                <a href="{{ url('template-email/activation') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'template-email' && Request::segment(2) == 'activation' ? 'active' : '' }}">Aktivasi</a>
                                                <a href="{{ url('template-email/change-password') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'template-email' && Request::segment(2) == 'change-password' ? 'active' : '' }}">Ganti Password</a>
                                                <a href="{{ url('template-email/publisher-reject') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'template-email' && Request::segment(2) == 'publisher-reject' ? 'active' : '' }}">Penerbit Ditolak</a>
                                                <a href="{{ url('template-email/publisher-submission') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'template-email' && Request::segment(2) == 'publisher-submission' ? 'active' : '' }}">Penerbit Pengajuan</a>
                                                <a href="{{ url('template-email/publisher-accept') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'template-email' && Request::segment(2) == 'publisher-accept' ? 'active' : '' }}">Penerbit Diterima</a>
                                                <a href="{{ url('template-email/collection-reject') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'template-email' && Request::segment(2) == 'collection-reject' ? 'active' : '' }}">Koleksi Ditolak</a>
                                                <a href="{{ url('template-email/collection-submitted') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'template-email' && Request::segment(2) == 'collection-submitted' ? 'active' : '' }}">Koleksi Diserahkan</a>
                                                <a href="{{ url('template-email/collection-accept') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'template-email' && Request::segment(2) == 'collection-accept' ? 'active' : '' }}">Koleksi Diterima</a>
                                            </div>
                                        </div>
                                    </div>
									<div class="tab-pane dropdown-scrollable-xl fade p-3 {{ Request::segment(1) == 'report' ? 'show active' : '' }}" id="menu-report" role="tabpanel">
                                        <div class="row" style="max-height:40vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-lg-4 mb-3 mb-lg-0">
                                                <div class="fw-bold border-bottom pb-2 mb-2">Sub Menu</div>
                                                <a href="{{ url('report/periodic') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'periodic' ? 'active' : '' }}">Periodik</a>
                                                <a href="{{ url('report/publisher') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'publisher' ? 'active' : '' }}">Penerbit</a>
                                                <a href="{{ url('report/publisher-isbn') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'publisher-isbn' ? 'active' : '' }}">Penerbit ISBN</a>
                                                <a href="{{ url('report/collection') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'collection' ? 'active' : '' }}">Koleksi</a>
                                                <a href="{{ url('report/kckra') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'kckra' ? 'active' : '' }}">KCKRA</a>
                                                <a href="{{ url('report/delivery') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'delivery' ? 'active' : '' }}">Pengiriman</a>
                                                <a href="{{ url('report/perfomance-user') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'perfomance-user' ? 'active' : '' }}">Performa User</a>
                                                <a href="{{ url('report/download') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'download' ? 'active' : '' }}">Unduhan</a>
                                                <a href="{{ url('report/log') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'log' ? 'active' : '' }}">Log</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane dropdown-scrollable-xl fade p-3 {{ Request::segment(1) == 'setting' ? 'show active' : '' }}" id="menu-setting" role="tabpanel">
                                        <div class="row" style="max-height:40vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-lg-4 mb-3 mb-lg-0">
                                                <div class="fw-bold border-bottom pb-2 mb-2">Sub Menu</div>
                                                <a href="{{ url('setting/user') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'setting' && Request::segment(2) == 'user' ? 'active' : '' }}">Pengguna</a>
                                                <a href="{{ url('setting/leader') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'setting' && Request::segment(2) == 'leader' ? 'active' : '' }}">Pimpinan</a>
                                                <a href="{{ url('setting/library') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'setting' && Request::segment(2) == 'library' ? 'active' : '' }}">Perpustakaan</a>
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
						<a href="{{ url('auth/change-password') }}" class="navbar-nav-link rounded">
							<i class="ph-key me-2"></i>
							Ganti Password
						</a>
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
				<li class="nav-item">
					<a href="javascript:void(0);" class="navbar-nav-link align-items-center rounded-pill p-1 bg-transparent no-click">
                        <img src="{{ asset('assets/user.png') }}" class="w-32px h-32px rounded-pill" alt="">
						<span class="d-none d-md-inline-block mx-md-2">{{ session('fullname') }}</span>
					</a>
				</li>
			</ul>
		</div>
	</div>
    <div class="page-content">
        <div class="content-wrapper">
            <div class="content-inner">
