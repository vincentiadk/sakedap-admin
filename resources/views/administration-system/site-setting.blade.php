<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Administrasi Sistem - <span class="fw-normal">Pengaturan Situs</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <span class="badge bg-primary p-2 bg-opacity-10 text-primary">
                    <i class="ph-globe me-1"></i>
                    Konfigurasi Website
                </span>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    @if($errors->any())
        <div class="alert alert-danger border-0 alert-dismissible fade show">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <div class="d-flex align-items-start">
                <i class="ph-warning-circle me-2 fs-4"></i>
                <div>
                    <h6 class="mb-2">Terdapat Kesalahan Validasi!</h6>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{!! $error !!}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @elseif(session('success'))
        <div class="alert bg-success text-white alert-dismissible fade show border-0">
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            <div class="d-flex align-items-center">
                <i class="ph-check-circle me-2 fs-4"></i>
                <div>{{ session('success') }}</div>
            </div>
        </div>
    @elseif(session('failed'))
        <div class="alert bg-danger text-white alert-dismissible fade show border-0">
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
            <div class="d-flex align-items-center">
                <i class="ph-x-circle me-2 fs-4"></i>
                <div>{{ session('failed') }}</div>
            </div>
        </div>
    @endif
    <form action="{{ url('administration-system/site-setting/submitted') }}" method="POST" onsubmit="onLoading('show', 'body')">
        @csrf
        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center">
                    <i class="ph-image-square me-2 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Branding & Logo</h6>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="form-label fw-semibold">
                            <i class="ph-diamonds-four me-1"></i>
                            File Ikon
                        </label>
                        <input type="file" class="form-control" name="file_icon" id="file_icon">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label fw-semibold">
                            <i class="ph-image me-1"></i>
                            File Logo
                        </label>
                        <input type="file" class="form-control" name="file_logo" id="file_logo">
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center">
                    <i class="ph-address-book me-2 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Informasi Kontak</h6>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 form-group">
                        <label class="form-label fw-semibold">
                            <i class="ph-map-pin me-1"></i>
                            Alamat
                        </label>
                        <textarea name="address" id="address" class="form-control" rows="4" placeholder="Masukkan alamat lengkap kantor">{{ $settingParameter->firstWhere('NAME', 'EFOAlamat')->VALUE ?? '' }}</textarea>
                    </div>
                    <div class="col-md-12 form-group">
                        <label class="form-label fw-semibold">
                            <i class="ph-phone me-1"></i>
                            Nomor Telepon
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">Utama</span>
                            <input type="text" class="form-control" name="phone_office" id="phone_office" value="{{ $settingParameter->firstWhere('NAME', 'EFOTelpKantor')->VALUE ?? '' }}" placeholder="(021) 1234567">
                            <span class="input-group-text">Karya Cetak</span>
                            <input type="text" class="form-control" name="phone_printed" id="phone_printed" value="{{ $settingParameter->firstWhere('NAME', 'EFOTelpKC')->VALUE ?? '' }}" placeholder="(021) 2345678">
                            <span class="input-group-text">Karya Rekam</span>
                            <input type="text" class="form-control" name="phone_recorded" id="phone_recorded" value="{{ $settingParameter->firstWhere('NAME', 'EFOTelpKR')->VALUE ?? '' }}" placeholder="(021) 3456789">
                        </div>
                    </div>
                    <div class="col-md-12 form-group">
                        <label class="form-label fw-semibold">
                            <i class="ph-envelope me-1"></i>
                            Email
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="ph-at"></i>
                            </span>
                            <input type="email" class="form-control" name="email" id="email" value="{{ $settingParameter->firstWhere('NAME', 'EFOEmail')->VALUE ?? '' }}" placeholder="contact@example.com">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center">
                    <i class="ph-share-network me-2 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Media Sosial</h6>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 form-group">
                        <label class="form-label fw-semibold">
                            <i class="ph-youtube-logo me-1"></i>
                            Link YouTube
                        </label>
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-danger text-white">
                                <i class="ph-youtube-logo me-1"></i>
                                Nasional
                            </span>
                            <input type="url" class="form-control" name="national_youtube" id="national_youtube" value="{{ $settingParameter->firstWhere('NAME', 'EFOYoutubeNasional')->VALUE ?? '' }}" placeholder="https://youtube.com/@channel">
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-danger text-white">
                                <i class="ph-youtube-logo me-1"></i>
                                Sakedap
                            </span>
                            <input type="url" class="form-control" name="youtube" id="youtube" value="{{ $settingParameter->firstWhere('NAME', 'EFOYoutube')->VALUE ?? '' }}" placeholder="https://youtube.com/@channel">
                        </div>
                    </div>
                    <div class="col-md-12 form-group">
                        <label class="form-label fw-semibold">
                            <i class="ph-instagram-logo me-1"></i>
                            Link Instagram
                        </label>
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-gradient" style="background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%) !important; color: white;">
                                <i class="ph-instagram-logo me-1"></i>
                                Nasional
                            </span>
                            <input type="url" class="form-control" name="national_instagram" id="national_instagram" value="{{ $settingParameter->firstWhere('NAME', 'EFOInstagramNasional')->VALUE ?? '' }}" placeholder="https://instagram.com/username">
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-gradient" style="background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%) !important; color: white;">
                                <i class="ph-instagram-logo me-1"></i>
                                Sakedap
                            </span>
                            <input type="url" class="form-control" name="instagram" id="instagram" value="{{ $settingParameter->firstWhere('NAME', 'EFOInstagram')->VALUE ?? '' }}" placeholder="https://instagram.com/username">
                        </div>
                    </div>
                    <div class="col-md-12 form-group">
                        <label class="form-label fw-semibold">
                            <i class="ph-globe me-1"></i>
                            Link Website Nasional
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="ph-link"></i>
                            </span>
                            <input type="url" class="form-control" name="national_website" id="national_website" value="{{ $settingParameter->firstWhere('NAME', 'EFOWebsiteNasional')->VALUE ?? '' }}" placeholder="https://example.com">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        <i class="ph-info me-1"></i>
                        Pastikan semua informasi yang dimasukkan sudah benar
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="ph-floppy-disk me-1"></i>
                        Simpan Pengaturan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(function() {
        const settings = {
            icon: {
                value: @json($settingParameter->firstWhere('NAME', 'EFOIcon')?->VALUE ?? ''),
                id: @json($settingParameter->firstWhere('NAME', 'EFOIcon')?->ID ?? '')
            },
            logo: {
                value: @json($settingParameter->firstWhere('NAME', 'EFOLogo')?->VALUE ?? ''),
                id: @json($settingParameter->firstWhere('NAME', 'EFOLogo')?->ID ?? '')
            }
        };

        const commonFileConfig = {
            allowedFileExtensions: ['jpg', 'jpeg', 'png'],
            maxFileCount: 1,
            maxFileSize: 2048,
            overwriteInitial: true,
            showUpload: false,
            showRemove: true,
            showCancel: false,
            browseLabel: 'Pilih File',
            removeLabel: 'Hapus',
            browseClass: 'btn btn-primary',
            removeClass: 'btn btn-danger',
            fileActionSettings: {
                showRemove: true,
                showUpload: false,
                showZoom: true,
                showDrag: false
            },
            layoutTemplates: {
                actionDelete: '<button type="button" class="btn btn-sm btn-icon btn-danger" title="Hapus file" {dataKey}><i class="ph-trash"></i></button>',
                actionZoom: '<button type="button" class="btn btn-sm btn-icon btn-info" title="Lihat preview" {dataKey}><i class="ph-magnifying-glass-plus"></i></button>',
            },
            msgPlaceholder: 'Pilih file atau drag & drop di sini...',
            msgSizeTooLarge: 'File "{name}" ({size} KB) melebihi ukuran maksimal {maxSize} KB.',
            msgInvalidFileExtension: 'Ekstensi file "{name}" tidak valid. Hanya file {extensions} yang diperbolehkan.',
            msgFilesTooMany: 'Jumlah file yang dipilih ({n}) melebihi batas maksimal {m}.',
        };

        function buildFilePreview(filename, id) {
            if (!filename || !id) {
                return {
                    preview: [],
                    config: []
                };
            }

            const imageUrl = '{{ url("stream-file") }}?type=settingparameters&id=' + id + '&filename=' + encodeURIComponent(filename);

            return {
                preview: [imageUrl],
                config: [{
                    caption: filename,
                    size: '',
                    key: 1,
                    url: imageUrl,
                    downloadUrl: imageUrl
                }]
            };
        }

        const iconPreview = buildFilePreview(settings.icon.value, settings.icon.id);
        const logoPreview = buildFilePreview(settings.logo.value, settings.logo.id);

        dragAndDropFile('#file_icon', {
            ...commonFileConfig,
            initialPreview: iconPreview.preview,
            initialPreviewConfig: iconPreview.config
        });

        dragAndDropFile('#file_logo', {
            ...commonFileConfig,
            initialPreview: logoPreview.preview,
            initialPreviewConfig: logoPreview.config
        });
    });
</script>
