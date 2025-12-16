<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Administrasi Sistem - <span class="fw-normal">Pengaturan Situs</span>
            </h4>
        </div>
    </div>
</div>
<div class="content pt-0">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{!! $error !!}</li>
                @endforeach
            </ul>
        </div>
    @elseif(session('success'))
        <div class="alert bg-success text-white fade show border-0">
            {{ session('success') }}
        </div>
    @elseif(session('failed'))
        <div class="alert bg-danger text-white fade show border-0">
            {{ session('failed') }}
        </div>
    @endif
    <form action="{{ url('administration-system/site-setting/submitted') }}" method="POST" onsubmit="onLoading('show', 'body')">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">File Ikon :</label>
                            <input type="file" class="form-control" name="file_icon" id="file_icon">
                        </div>
                        <div class="form-group">
                            <label class="form-label">File Logo :</label>
                            <input type="file" class="form-control" name="file_logo" id="file_logo">
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="col-form-label">Alamat :</label>
                            <textarea name="address" id="address" class="form-control" rows="7" placeholder="...................." style="resize:none;">{{ $settingParameter->firstWhere('NAME', 'EFOAlamat')->VALUE ?? '' }}</textarea>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">Telp :</label>
                            <div class="input-group">
                                <span class="input-group-text">Utama</span>
                                <input type="text" class="form-control" name="phone_office" id="phone_office" value="{{ $settingParameter->firstWhere('NAME', 'EFOTelpKantor')->VALUE ?? '' }}" placeholder="....................">
                                <span class="input-group-text">Kaya Cetak</span>
                                <input type="text" class="form-control" name="phone_printed" id="phone_printed" value="{{ $settingParameter->firstWhere('NAME', 'EFOTelpKC')->VALUE ?? '' }}" placeholder="....................">
                                <span class="input-group-text">Kaya Rekam</span>
                                <input type="text" class="form-control" name="phone_recorded" id="phone_recorded" value="{{ $settingParameter->firstWhere('NAME', 'EFOTelpKR')->VALUE ?? '' }}" placeholder="....................">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">Email :</label>
                            <input type="text" class="form-control" name="email" id="email" value="{{ $settingParameter->firstWhere('NAME', 'EFOEmail')->VALUE ?? '' }}" placeholder="....................">
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">Link Youtube :</label>
                            <div class="input-group">
                                <span class="input-group-text">Nasional</span>
                                <input type="text" class="form-control" name="national_youtube" id="national_youtube" value="{{ $settingParameter->firstWhere('NAME', 'EFOYoutubeNasional')->VALUE ?? '' }}" placeholder="....................">
                                <span class="input-group-text">Sakedap</span>
                                <input type="text" class="form-control" name="youtube" id="youtube" value="{{ $settingParameter->firstWhere('NAME', 'EFOYoutube')->VALUE ?? '' }}" placeholder="....................">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">Link Instagram :</label>
                            <div class="input-group">
                                <span class="input-group-text">Nasional</span>
                                <input type="text" class="form-control" name="national_instagram" id="national_instagram" value="{{ $settingParameter->firstWhere('NAME', 'EFOInstagramNasional')->VALUE ?? '' }}" placeholder="....................">
                                <span class="input-group-text">Sakedap</span>
                                <input type="text" class="form-control" name="instagram" id="instagram" value="{{ $settingParameter->firstWhere('NAME', 'EFOInstagram')->VALUE ?? '' }}" placeholder="....................">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label">Link Website Nasional :</label>
                            <input type="text" class="form-control" name="national_website" id="national_website" value="{{ $settingParameter->firstWhere('NAME', 'EFOWebsiteNasional')->VALUE ?? '' }}" placeholder="....................">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="ph-floppy-disk me-1"></i>
                        Simpan Data
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
            msgSizeTooLarge: 'File "{name}" ({size} KB) melebihi ukuran maksimal {maxSize} KB.',
            msgInvalidFileExtension: 'Ekstensi file "{name}" tidak valid. Hanya file {extensions} yang diperbolehkan.',
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
