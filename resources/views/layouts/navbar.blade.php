<body>
	<div class="navbar navbar-expand-xl navbar-static shadow iframeable">
		<div class="container-fluid">
			<div class="navbar-brand flex-1">
				<a href="{{ url('home') }}" class="d-inline-flex align-items-center">
					<img src="{{ asset('assets/icon.png') }}" alt="Logo">
                    <span class="ms-2 fs-4 pt-1 text-dark fw-bold">SAKEDAP</span>
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
                                        <div style="max-height:60vh; overflow-y:auto; overflow-x:hidden;">
                                            <ul class="nav nav-pills flex-xl-column flex-nowrap text-nowrap justify-content-center wmin-xl-300" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <a href="{{ url('dashboard') }}" class="nav-link rounded {{ Request::segment(1) == 'dashboard' ? 'active' : '' }}">
                                                        <i class="ph-chart-pie me-2"></i>
                                                        Dasboard
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="#menu-physical-delivery" class="nav-link rounded {{ Request::segment(1) == 'physical-delivery' ? 'active' : '' }}" data-bs-toggle="tab" aria-selected="{{ Request::segment(1) == 'physical-delivery' ? 'true' : 'false' }}" role="tab">
                                                        <i class="ph-archive-box me-2"></i>
                                                        Pengiriman Fisik
                                                    </a>
                                                </li>
                                                @if(Main::isPerpusnas())
                                                    <li class="nav-item" role="presentation">
                                                        <a href="#menu-national-management" class="nav-link rounded {{ Request::segment(1) == 'national-management' ? 'active' : '' }}" data-bs-toggle="tab" aria-selected="{{ Request::segment(1) == 'national-management' ? 'true' : 'false' }}" role="tab">
                                                            <i class="ph-user-circle-gear me-2"></i>
                                                            Pengelolaan Koleksi Perpusnas
                                                        </a>
                                                    </li>
                                                @endif
                                                <li class="nav-item" role="presentation">
                                                    <a href="#menu-physical-collection" class="nav-link rounded {{ Request::segment(1) == 'physical-collection' ? 'active' : '' }}" data-bs-toggle="tab" aria-selected="{{ Request::segment(1) == 'physical-collection' ? 'true' : 'false' }}" role="tab">
                                                        <i class="ph-books me-2"></i>
                                                        Koleksi Fisik
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="#menu-digital-storage-handover" class="nav-link rounded {{ Request::segment(1) == 'digital-storage-handover' ? 'active' : '' }}" data-bs-toggle="tab" aria-selected="{{ Request::segment(1) == 'digital-storage-handover' ? 'true' : 'false' }}" role="tab">
                                                        <i class="ph-monitor-play me-2"></i>
                                                        Serah Simpan Digital
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="{{ url('bill-isbn') }}" class="nav-link rounded {{ Request::segment(1) == 'bill-isbn' ? 'active' : '' }}">
                                                        <i class="ph-cardholder me-2"></i>
                                                        Tagihan ISBN
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="#menu-coaching-supervision" class="nav-link rounded {{ Request::segment(1) == 'coaching-supervision' ? 'active' : '' }}" data-bs-toggle="tab" aria-selected="{{ Request::segment(1) == 'coaching-supervision' ? 'true' : 'false' }}" role="tab">
                                                        <i class="ph-user-focus me-2"></i>
                                                        Pengawasan & Pembinaan
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="#menu-report" class="nav-link rounded {{ Request::segment(1) == 'report' ? 'active' : '' }}" data-bs-toggle="tab" aria-selected="{{ Request::segment(1) == 'report' ? 'true' : 'false' }}" role="tab">
                                                        <i class="ph-notebook me-2"></i>
                                                        Laporan
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="#menu-administration-system" class="nav-link rounded {{ Request::segment(1) == 'administration-system' ? 'active' : '' }}" data-bs-toggle="tab" aria-selected="{{ Request::segment(1) == 'administration-system' ? 'true' : 'false' }}" role="tab">
                                                        <i class="ph-gear me-2"></i>
                                                        Administrasi Sistem
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="{{ url('request-file') }}" class="nav-link rounded {{ Request::segment(1) == 'request-file' ? 'active' : '' }}">
                                                        <i class="ph-file-plus me-2"></i>
                                                        Permintaan File
                                                    </a>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <a href="{{ url('award') }}" class="nav-link rounded {{ Request::segment(1) == 'award' ? 'active' : '' }}">
                                                        <i class="ph-trophy me-2"></i>
                                                        Pekan Penghargaan
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
									</div>
								</div>
								<div class="tab-content flex-xl-1 main-menu-sub">
                                    <div class="tab-pane dropdown-scrollable-xl fade p-3 {{ Request::segment(1) == 'physical-delivery' ? 'show active' : '' }}" id="menu-physical-delivery" role="tabpanel">
                                        <div class="row" style="max-height:65vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-md-12">
                                                <div class="alert alert-info text-center fw-semibold">Sub Menu</div>
                                            </div>
                                            <div class="col-md-7">
                                                <a href="{{ url('physical-delivery/delivery-verification') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'physical-delivery' && Request::segment(2) == 'delivery-verification' ? 'active' : '' }}">Verifikasi Pengiriman</a>
                                                <a href="{{ url('physical-delivery/delivery-to-destination') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'physical-delivery' && Request::segment(2) == 'delivery-to-destination' ? 'active' : '' }}">Pengiriman Sampai ke Tujuan</a>
                                                <a href="{{ url('physical-delivery/in-delivery') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'physical-delivery' && Request::segment(2) == 'in-delivery' ? 'active' : '' }}">Dalam Pengiriman</a>
                                                <a href="{{ url('physical-delivery/accept') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'physical-delivery' && Request::segment(2) == 'accept' ? 'active' : '' }}">Diterima</a>
                                                <a href="{{ url('physical-delivery/create-receipt') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'physical-delivery' && Request::segment(2) == 'create-receipt' ? 'active' : '' }}">Tambah Bukti Penerimaan</a>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="border-start-lg ps-lg-3 py-2">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="ph-chart-pie-slice text-success me-2 ph-lg"></i>
                                                        <span class="fw-semibold text-uppercase text-muted">Statistik Pengiriman</span>
                                                    </div>
                                                    <div class="mb-2">
                                                        <p class="fs-sm text-muted">
                                                            Sistem saat ini mencatat <span class="text-danger fw-bold">{{ number_format(config('system.total_delivery_verification')) }}</span> data verifikasi,
                                                            <span class="text-primary fw-bold">{{ number_format(config('system.total_in_delivery')) }}</span> paket sedang dikirim, dan
                                                            <span class="text-success fw-bold">{{ number_format(config('system.total_delivery_sent')) }}</span> pengiriman telah sampai di tujuan.
                                                        </p>
                                                    </div>
                                                    <div class="list-group list-group-flush border-top border-bottom">
                                                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-1 border-0">
                                                            <div class="d-flex align-items-center">
                                                                <i class="ph-clipboard-text text-warning me-2"></i>
                                                                <span class="fs-sm">Pengiriman Verifikasi</span>
                                                            </div>
                                                            <span class="badge bg-warning-alpha text-warning rounded-pill fw-bold">
                                                                {{ number_format(config('system.total_delivery_verification')) }}
                                                            </span>
                                                        </div>
                                                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-1 border-0">
                                                            <div class="d-flex align-items-center">
                                                                <i class="ph-truck text-primary me-2"></i>
                                                                <span class="fs-sm">Dalam Pengiriman</span>
                                                            </div>
                                                            <span class="badge bg-primary-alpha text-primary rounded-pill fw-bold">
                                                                {{ number_format(config('system.total_in_delivery')) }}
                                                            </span>
                                                        </div>
                                                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-1 border-0">
                                                            <div class="d-flex align-items-center">
                                                                <i class="ph-check-circle text-success me-2"></i>
                                                                <span class="fs-sm">Sampai Tujuan</span>
                                                            </div>
                                                            <span class="badge bg-success-alpha text-success rounded-pill fw-bold">
                                                                {{ number_format(config('system.total_delivery_sent')) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane dropdown-scrollable-xl fade p-3 {{ Request::segment(1) == 'national-management' ? 'show active' : '' }}" id="menu-national-management" role="tabpanel">
                                        <div class="row" style="max-height:65vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-md-6">
                                                <div class="alert alert-info text-center fw-semibold">Pengelolaan Koleksi</div>
                                                <a href="{{ url('national-management/deposit-collection-list') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'national-management' && Request::segment(2) == 'deposit-collection-list' ? 'active' : '' }}">Daftar Koleksi Deposit</a>
                                                <a href="{{ url('national-management/catalog-list') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'national-management' && Request::segment(2) == 'catalog-list' ? 'active' : '' }}">Daftar Katalog</a>
                                                <a href="{{ url('national-management/delivery-to-processing') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'national-management' && Request::segment(2) == 'delivery-to-processing' ? 'active' : '' }}">Pengiriman ke Pengolahan</a>
                                                <a href="{{ url('national-management/delivery-to-processing-list') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'national-management' && Request::segment(2) == 'delivery-to-processing-list' ? 'active' : '' }}">Daftar Pengiriman ke Pengolahan</a>
                                                <a href="{{ url('national-management/alignment-storage') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'national-management' && Request::segment(2) == 'alignment-storage' ? 'active' : '' }}">Penyimpanan & Penjajaran</a>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="alert alert-info text-center fw-semibold">Pengelolaan Serial</div>
                                                <a href="{{ url('national-management/cardex-list') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'national-management' && Request::segment(2) == 'cardex-list' ? 'active' : '' }}">Daftar Kardeks</a>
                                                <a href="{{ url('national-management/volume-by-title') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'national-management' && Request::segment(2) == 'volume-by-title' ? 'active' : '' }}">Jilid Berdasarkan Judul</a>
                                                <a href="{{ url('national-management/collection-volume') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'national-management' && Request::segment(2) == 'collection-volume' ? 'active' : '' }}">Jilid Koleksi</a>
                                                <a href="{{ url('national-management/create-volume') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'national-management' && Request::segment(2) == 'create-volume' ? 'active' : '' }}">Buat Jilid Baru</a>
                                                <a href="{{ url('national-management/import-serial-collection') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'national-management' && Request::segment(2) == 'import-serial-collection' ? 'active' : '' }}">Import Koleksi Serial</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane dropdown-scrollable-xl fade p-3 {{ Request::segment(1) == 'physical-collection' ? 'show active' : '' }}" id="menu-physical-collection" role="tabpanel">
                                        <div class="row" style="max-height:65vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-md-12">
                                                <div class="alert alert-info text-center fw-semibold">Sub Menu</div>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="{{ url('physical-collection/collection-on-delivery') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'physical-collection' && Request::segment(2) == 'collection-on-delivery' ? 'active' : '' }}">Koleksi Dalam Pengiriman</a>
                                                <a href="{{ url('physical-collection/collection-accept') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'physical-collection' && Request::segment(2) == 'collection-accept' ? 'active' : '' }}">Koleksi Diterima</a>
                                                <a href="{{ url('physical-collection/collection-reject') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'physical-collection' && Request::segment(2) == 'collection-reject' ? 'active' : '' }}">Koleksi Ditolak</a>
                                                <a href="{{ url('physical-collection/collection-grant') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'physical-collection' && Request::segment(2) == 'collection-grant' ? 'active' : '' }}">Koleksi Dihibahkan</a>
                                                <a href="{{ url('physical-collection/collection-retur') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'physical-collection' && Request::segment(2) == 'collection-retur' ? 'active' : '' }}">Koleksi Dikembalikan</a>
                                                <a href="{{ url('physical-collection/verification-collection-received') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'physical-collection' && Request::segment(2) == 'verification-collection-received' ? 'active' : '' }}">Verifikasi Koleksi Yang Diterima</a>
                                                <a href="{{ url('physical-collection/retrospective-collection-registration') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'physical-collection' && Request::segment(2) == 'retrospective-collection-registration' ? 'active' : '' }}">Registrasi Koleksi Retrospeksi</a>
                                                <a href="{{ url('physical-collection/label') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'physical-collection' && Request::segment(2) == 'label' ? 'active' : '' }}">Label</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane dropdown-scrollable-xl fade p-3 {{ Request::segment(1) == 'digital-storage-handover' ? 'show active' : '' }}" id="menu-digital-storage-handover" role="tabpanel">
                                        <div class="row" style="max-height:65vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-md-12">
                                                <div class="alert alert-info text-center fw-semibold">Sub Menu</div>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="{{ url('digital-storage-handover/review') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'digital-storage-handover' && Request::segment(2) == 'review' ? 'active' : '' }}">Peninjauan</a>
                                                <a href="{{ url('digital-storage-handover/review-edition') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'digital-storage-handover' && Request::segment(2) == 'review-edition' ? 'active' : '' }}">Peninjauan Serial Elektronik</a>
                                                <a href="{{ url('digital-storage-handover/problem') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'digital-storage-handover' && Request::segment(2) == 'problem' ? 'active' : '' }}">Bermasalah</a>
                                                <a href="{{ url('digital-storage-handover/accept') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'digital-storage-handover' && Request::segment(2) == 'accept' ? 'active' : '' }}">Diterima</a>
                                                <a href="{{ url('digital-storage-handover/reject') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'digital-storage-handover' && Request::segment(2) == 'reject' ? 'active' : '' }}">Ditolak</a>
                                                <a href="{{ url('digital-storage-handover/single-upload') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'digital-storage-handover' && Request::segment(2) == 'single-upload' ? 'active' : '' }}">Unggah Tunggal</a>
                                                <a href="{{ url('digital-storage-handover/bulk-upload') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'digital-storage-handover' && Request::segment(2) == 'bulk-upload' ? 'active' : '' }}">Unggah Banyak</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane dropdown-scrollable-xl fade p-3 {{ Request::segment(1) == 'coaching-supervision' ? 'show active' : '' }}" id="menu-coaching-supervision" role="tabpanel">
                                        <div class="row" style="max-height:65vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-md-12">
                                                <div class="alert alert-info text-center fw-semibold">Sub Menu</div>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="{{ url('coaching-supervision/create-executor') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'coaching-supervision' && Request::segment(2) == 'create-executor' ? 'active' : '' }}">Tambah Data Pelaksana Serah</a>
                                                <a href="{{ url('coaching-supervision/executor-list') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'coaching-supervision' && Request::segment(2) == 'executor-list' ? 'active' : '' }}">Daftar Pelaksana Serah</a>
                                                <a href="{{ url('coaching-supervision/problem') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'coaching-supervision' && Request::segment(2) == 'problem' ? 'active' : '' }}">Bermasalah</a>
                                                <a href="{{ url('coaching-supervision/review') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'coaching-supervision' && Request::segment(2) == 'review' ? 'active' : '' }}">Peninjauan</a>
                                                <a href="{{ url('coaching-supervision/compliance') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'coaching-supervision' && Request::segment(2) == 'compliance' ? 'active' : '' }}">Kepatuhan</a>
                                                <a href="{{ url('coaching-supervision/coaching-schedule') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'coaching-supervision' && Request::segment(2) == 'coaching-schedule' ? 'active' : '' }}">Jadwal Pembinaan</a>
                                                <a href="{{ url('coaching-supervision/monitoring') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'coaching-supervision' && Request::segment(2) == 'monitoring' ? 'active' : '' }}">Pemantauan</a>
                                                <a href="{{ url('coaching-supervision/warning') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'coaching-supervision' && Request::segment(2) == 'warning' ? 'active' : '' }}">Teguran</a>
                                                <a href="{{ url('coaching-supervision/executor-group') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'coaching-supervision' && Request::segment(2) == 'executor-group' ? 'active' : '' }}">Grup Pelaksana Serah</a>
                                                <a href="{{ url('coaching-supervision/executor-access') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'coaching-supervision' && Request::segment(2) == 'executor-access' ? 'active' : '' }}">Akses Pelaksana Serah</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane dropdown-scrollable-xl fade p-3 {{ Request::segment(1) == 'report' ? 'show active' : '' }}" id="menu-report" role="tabpanel">
                                        <div class="row" style="max-height:65vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-md-12">
                                                <div class="alert alert-info text-center fw-semibold">Sub Menu</div>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="{{ url('report/delivery') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'delivery' ? 'active' : '' }}">Pengiriman</a>
                                                <a href="{{ url('report/promotion') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'promotion' ? 'active' : '' }}">Promosi</a>
                                                <a href="{{ url('report/physical-reception') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'physical-reception' ? 'active' : '' }}">Penerimaan Fisik</a>
                                                <a href="{{ url('report/physical-recording') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'physical-recording' ? 'active' : '' }}">Pencatatan Fisik</a>
                                                <a href="{{ url('report/physical-alignment') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'physical-alignment' ? 'active' : '' }}">Penjajaran Fisik</a>
                                                <a href="{{ url('report/manage') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'manage' ? 'active' : '' }}">Pengelolaan</a>
                                                <a href="{{ url('report/digital-manage') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'digital-manage' ? 'active' : '' }}">Pengelolaan Digital</a>
                                                <a href="{{ url('report/digital-empowerment') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'digital-empowerment' ? 'active' : '' }}">Pendayagunaan Digital</a>
                                                <a href="{{ url('report/physical-empowerment') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'physical-empowerment' ? 'active' : '' }}">Pendayagunaan Fisik</a>
                                                <a href="{{ url('report/service') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'service' ? 'active' : '' }}">Layanan</a>
                                                <a href="{{ url('report/asset') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'asset' ? 'active' : '' }}">Aset</a>
                                                <a href="{{ url('report/download') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'report' && Request::segment(2) == 'download' ? 'active' : '' }}">Download</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane dropdown-scrollable-xl fade p-3 {{ Request::segment(1) == 'administration-system' ? 'show active' : '' }}" id="menu-administration-system" role="tabpanel">
                                        <div class="row" style="max-height:65vh; overflow-y:auto; overflow-x:hidden;">
                                            <div class="col-md-3">
                                                <div class="alert alert-info text-center fw-semibold">Main</div>
                                                <div class="col-md-2">
                                                    <a href="{{ url('administration-system/setting-system') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'setting-system' ? 'active' : '' }}">Pengaturan Sistem</a>
                                                    <a href="{{ url('administration-system/promotion') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'promotion' ? 'active' : '' }}">Promosi</a>
                                                    <a href="{{ url('administration-system/template-email') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'template-email' ? 'active' : '' }}">Template Email</a>
                                                    <a href="{{ url('administration-system/header-email') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'header-email' ? 'active' : '' }}">Header Email</a>
                                                    <a href="{{ url('administration-system/footer-email') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'footer-email' ? 'active' : '' }}">Footer Email</a>
                                                    <a href="{{ url('administration-system/user') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'user' ? 'active' : '' }}">User</a>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="alert alert-info text-center fw-semibold">Front Office</div>
                                                <div class="col-md-2">
                                                    <a href="{{ url('administration-system/site-setting') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'site-setting' ? 'active' : '' }}">Pengaturan Situs</a>
                                                    <a href="{{ url('administration-system/news') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'news' ? 'active' : '' }}">Berita</a>
                                                    <a href="{{ url('administration-system/banner') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'banner' ? 'active' : '' }}">Banner</a>
                                                    <a href="{{ url('administration-system/event') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'event' ? 'active' : '' }}">Event</a>
                                                    <a href="{{ url('administration-system/faq') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'faq' ? 'active' : '' }}">FAQ</a>
                                                    <a href="{{ url('administration-system/pages') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'pages' ? 'active' : '' }}">Halaman</a>
                                                    <a href="{{ url('administration-system/tutorial') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'tutorial' ? 'active' : '' }}">Tutorial</a>
                                                    <a href="{{ url('administration-system/news-category') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'news-category' ? 'active' : '' }}">Kategori Berita</a>
                                                    <a href="{{ url('administration-system/about-us') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'about-us' ? 'active' : '' }}">Tentang Kami</a>
                                                    <a href="{{ url('administration-system/terms-conditions') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'terms-conditions' ? 'active' : '' }}">Syarat & Ketentuan</a>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="alert alert-info text-center fw-semibold">Master Data</div>
                                                <div class="col-md-2">
                                                    <a href="{{ url('administration-system/media-type') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'media-type' ? 'active' : '' }}">Jenis Media</a>
                                                    <a href="{{ url('administration-system/collection-category') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'collection-category' ? 'active' : '' }}">Kategori Koleksi</a>
                                                    <a href="{{ url('administration-system/compliance') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'compliance' ? 'active' : '' }}">Kepatuhan</a>
                                                    <a href="{{ url('administration-system/problem') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'problem' ? 'active' : '' }}">Masalah</a>
                                                    <a href="{{ url('administration-system/library') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'library' ? 'active' : '' }}">Perpustakaan</a>
                                                    <a href="{{ url('administration-system/depo') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'depo' ? 'active' : '' }}">Depo</a>
                                                    <a href="{{ url('administration-system/leader') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'leader' ? 'active' : '' }}">Pimpinan</a>
                                                    <a href="{{ url('administration-system/storage-space') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'storage-space' ? 'active' : '' }}">Ruang Penyimpanan</a>
                                                    <a href="{{ url('administration-system/size-weight-book') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'size-weight-book' ? 'active' : '' }}">Ukuran & Berat Buku</a>
                                                    <a href="{{ url('administration-system/delivery-service') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'delivery-service' ? 'active' : '' }}">Jasa Pengiriman</a>
                                                    <a href="{{ url('administration-system/setting-deposit-number') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'setting-deposit-number' ? 'active' : '' }}">Setting Nomor Deposit</a>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="alert alert-info text-center fw-semibold">Lokasi</div>
                                                <div class="col-md-2">
                                                    <a href="{{ url('administration-system/province') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'province' ? 'active' : '' }}">Provinsi</a>
                                                    <a href="{{ url('administration-system/city') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'city' ? 'active' : '' }}">Kota / Kabupaten</a>
                                                    <a href="{{ url('administration-system/district') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'district' ? 'active' : '' }}">Kecamatan</a>
                                                    <a href="{{ url('administration-system/village') }}" class="dropdown-item rounded pb-0 {{ Request::segment(1) == 'administration-system' && Request::segment(2) == 'village' ? 'active' : '' }}">Kelurahan / Desa</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
								</div>
							</div>
						</div>
					</li>
                    <li class="nav-item nav-item-dropdown-xl dropdown">
						<a href="#" class="navbar-nav-link dropdown-toggle rounded" data-bs-toggle="dropdown">
							<i class="ph-file me-2"></i>
							Log
						</a>
						<div class="dropdown-menu">
							<a href="{{ url('log-viewer') }}" class="dropdown-item rounded">Sistem</a>
							<a href="{{ url('log-awb') }}" class="dropdown-item rounded">AWB</a>
							<a href="{{ url('log-activity') }}" class="dropdown-item rounded">Aktivitas</a>
						</div>
					</li>
                    <li class="nav-item">
						<a href="{{ url('auth/profile') }}" class="navbar-nav-link rounded">
							<i class="ph-user-circle me-2"></i>
							Profil
						</a>
					</li>
                    <li class="nav-item">
						<a href="{{ url('auth/change-password') }}" class="navbar-nav-link rounded">
							<i class="ph-lock me-2"></i>
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
							<a href="{{ url('coaching-supervision/review') }}" class="text-body mx-auto disabled">
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
