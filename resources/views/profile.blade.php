<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                <span class="fw-normal">Profil</span>
            </h4>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-xl-5">
            <form method="POST">
                @csrf
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-4">
                        <div class="mb-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                <i class="ph-user ph-4x text-primary"></i>
                            </div>
                        </div>
                        <h5 class="mb-1 fw-semibold">{{ session('name') }}</h5>
                        <p class="text-muted mb-0">{{ session('email') }}</p>
                        <div class="mt-3">
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                <i class="ph-identification-badge me-1"></i>
                                {{ session('username') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="ph-user-circle me-2 text-primary"></i>
                            <h6 class="mb-0 fw-semibold">Edit Profil</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger border-0 mb-4">
                                <div class="d-flex align-items-start">
                                    <i class="ph-warning-circle me-2 mt-1"></i>
                                    <div class="flex-fill">
                                        <strong>Terdapat kesalahan:</strong>
                                        <ul class="mb-0 mt-2">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @elseif(session('success'))
                            <div class="alert alert-success border-0 mb-4">
                                <div class="d-flex align-items-center">
                                    <i class="ph-check-circle me-2"></i>
                                    <div class="flex-fill">
                                        <strong>Berhasil!</strong> {{ session('success') }}
                                    </div>
                                </div>
                            </div>
                        @elseif(session('error'))
                            <div class="alert alert-danger border-0 mb-4">
                                <div class="d-flex align-items-center">
                                    <i class="ph-x-circle me-2"></i>
                                    <div class="flex-fill">
                                        <strong>Gagal!</strong> {{ session('error') }}
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                <i class="ph-user me-1"></i>
                                Nama Lengkap
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="name" id="name" value="{{ session('name') }}" placeholder="Masukkan nama lengkap">
                            <small class="form-text text-muted">
                                <i class="ph-info me-1"></i>
                                Nama akan ditampilkan di sistem
                            </small>
                        </div>
                        <div class="form-group">
                            <label class="form-label fw-semibold">
                                <i class="ph-envelope me-1"></i>
                                Email
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ph-at"></i>
                                </span>
                                <input type="email" class="form-control" name="email" id="email" value="{{ session('email') }}" placeholder="nama@example.com">
                            </div>
                            <small class="form-text text-muted">
                                <i class="ph-info me-1"></i>
                                Email digunakan untuk notifikasi sistem
                            </small>
                        </div>
                    </div>
                    <div class="card-footer border-top">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-light">
                                <i class="ph-arrow-counter-clockwise me-1"></i>
                                Reset
                            </button>
                            <button type="submit" class="btn btn-primary" onclick="onLoading('show', 'body')">
                                <i class="ph-floppy-disk me-1"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="ph-info me-2 text-primary"></i>
                            <h6 class="mb-0 fw-semibold">Informasi Akun</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                                <i class="ph-identification-badge text-primary"></i>
                            </div>
                            <div class="flex-fill">
                                <div class="text-muted small">Username</div>
                                <div class="fw-semibold">{{ session('username') }}</div>
                            </div>
                        </div>
                        @if(session('province_name'))
                            <div class="d-flex align-items-start mb-3">
                                <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                                    <i class="ph-map-pin text-primary"></i>
                                </div>
                                <div class="flex-fill">
                                    <div class="text-muted small">Provinsi</div>
                                    <div class="fw-semibold">{{ session('province_name') }}</div>
                                </div>
                            </div>
                        @endif
                        @if(session('role_name'))
                            <div class="d-flex align-items-start">
                                <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                                    <i class="ph-shield-check text-primary"></i>
                                </div>
                                <div class="flex-fill">
                                    <div class="text-muted small">Role</div>
                                    <div class="fw-semibold">{{ session('role_name') }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-header border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="ph-lock me-2 text-primary"></i>
                            <h6 class="mb-0 fw-semibold">Keamanan</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning bg-opacity-10 rounded p-2 me-3">
                                    <i class="ph-key text-warning"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">Password</div>
                                    <small class="text-muted">Ubah password secara berkala</small>
                                </div>
                            </div>
                            <a href="{{ url('auth/change-password') }}" class="btn btn-light btn-sm">
                                <i class="ph-pencil-simple me-1"></i>
                                Ubah
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
