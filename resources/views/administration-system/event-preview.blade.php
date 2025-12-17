<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Administrasi Sistem - Event - <span class="fw-normal">Preview</span>
            </h4>
        </div>
        <div class="collapse d-lg-block my-lg-auto ms-lg-auto" id="page-header">
            <div class="d-sm-flex align-items-center mb-3 mb-lg-0 ms-lg-3">
                <div class="d-inline-flex mt-3 mt-sm-0">
                    <a href="{{ url('administration-system/event') }}" class="btn btn-primary">
                        <i class="ph-arrow-left me-1"></i>
                        Kembali ke Tabel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow-sm">
                <div class="card-img-top position-relative overflow-hidden" style="max-height: 450px;">
                    <img src="{{ $news->IMAGE ? url('stream-file') . '?type=gambar_artikel&id=' . $news->ID . '&filename=' . $news->IMAGE : asset('assets/no-file.jpg') }}" class="img-fluid w-100" style="object-fit: cover; height: 450px;" alt="{{ $news->TITLE }}">
                    <div class="position-absolute top-0 end-0 m-3">
                        @if($news->STATUS == 'PUBLISH')
                            <span class="badge bg-success fs-6 shadow">
                                <i class="ph-check-circle me-1"></i>
                                {{ $news->STATUS }}
                            </span>
                        @else
                            <span class="badge bg-warning fs-6 shadow">
                                <i class="ph-clock me-1"></i>
                                {{ $news->STATUS }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="mb-3">
                        <span class="badge bg-primary bg-opacity-10 text-primary fs-6 fw-semibold px-3 py-2">
                            <i class="ph-tag me-1"></i>
                            {{ $news->NAME_E_NEWS_KATEGORI }}
                        </span>
                        <span class="badge bg-info bg-opacity-10 text-info fs-6 fw-semibold px-3 py-2 ms-2">
                            <i class="ph-translate me-1"></i>
                            {{ $news->LANG == 'id' ? 'Bahasa Indonesia' : 'English' }}
                        </span>
                    </div>
                    <h2 class="card-title fw-bold mb-3" style="line-height: 1.4;">
                        {{ $news->TITLE }}
                    </h2>
                    <div class="d-flex flex-wrap align-items-center text-muted mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center me-3 mb-2">
                            <i class="ph-calendar-blank me-1 text-primary"></i>
                            <span>{{ Carbon::parse($news->CREATED_AT)->isoFormat('D MMMM Y') }}</span>
                        </div>
                        <div class="d-flex align-items-center me-3 mb-2">
                            <i class="ph-clock me-1 text-primary"></i>
                            <span>{{ Carbon::parse($news->CREATED_AT)->format('H:i') }} WIB</span>
                        </div>
                        <div class="d-flex align-items-center me-3 mb-2">
                            <i class="ph-eye me-1 text-primary"></i>
                            <span>{{ number_format($news->NUM_VIEW ?: 0) }} views</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="fw-semibold mb-3">
                                        <i class="ph-calendar-check text-primary me-1"></i>
                                        Waktu Pelaksanaan
                                    </h6>
                                    <div class="mb-2">
                                        <small class="text-muted d-block mb-1">Tanggal Mulai</small>
                                        <div class="fw-semibold">
                                            <i class="ph-calendar me-1 text-success"></i>
                                            {{ $news->TANGGAL_MULAI ? Carbon::parse($news->TANGGAL_MULAI)->isoFormat('dddd, D MMMM Y') : '-' }}
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <small class="text-muted d-block mb-1">Tanggal Selesai</small>
                                        <div class="fw-semibold">
                                            <i class="ph-calendar-x me-1 text-danger"></i>
                                            {{ $news->TANGGAL_SELESAI ? Carbon::parse($news->TANGGAL_SELESAI)->isoFormat('dddd, D MMMM Y') : '-' }}
                                        </div>
                                    </div>
                                    @if($news->TANGGAL_MULAI && $news->TANGGAL_SELESAI)
                                        <div class="mt-3 pt-3 border-top">
                                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                                <i class="ph-hourglass me-1"></i>
                                                Durasi: {{ Carbon::parse($news->TANGGAL_MULAI)->diffInDays(Carbon::parse($news->TANGGAL_SELESAI)) + 1 }} hari
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <h6 class="fw-semibold mb-3">
                                        <i class="ph-map-pin text-danger me-1"></i>
                                        Tempat
                                    </h6>
                                    @if(!empty($news->LOKASI))
                                        <div class="d-flex align-items-start">
                                            <i class="ph-buildings text-primary me-1 mt-1"></i>
                                            <div class="fw-semibold">{{ $news->LOKASI }}</div>
                                        </div>
                                    @else
                                        <div class="text-muted">
                                            <i class="ph-warning-circle me-1"></i>
                                            Tempat belum ditentukan
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <h5 class="fw-semibold mb-3">
                        <i class="ph-microphone-stage text-primary me-1"></i>
                        Narasumber
                    </h5>
                    <div class="card border">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center me-3" style="width: 20px; height: 20px; flex-shrink: 0;"><i class="ph-user fs-2"></i></div>
                                <div class="flex-fill">
                                    <p class="mb-0">{{ $news->NARASUMBER ?: 'Tidak ada' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @php
                        $catalogs = $news->CATALOG ? json_decode($news->CATALOG) : [];
                    @endphp
                    <div class="mb-3">
                        <h5 class="fw-semibold mb-3">
                            <i class="ph-list-bullets text-primary me-1"></i>
                            Katalog Event
                        </h5>
                        <div class="list-group list-group-flush border rounded p-3">
                            @if($catalogs && count($catalogs) > 0)
                                @foreach($catalogs as $index => $catalog)
                                    <div class="list-group-item px-0">
                                        <div class="d-flex align-items-start">
                                            <div class="me-3">
                                                <span class="badge bg-primary rounded-circle" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">{{ $index + 1 }}</span>
                                            </div>
                                            <div class="flex-fill">
                                                <div class="fw-semibold mb-1">{{ $catalog->title ?? 'Item ' . ($index + 1) }}</div>
                                                @if(!empty($catalog->id))
                                                    <small class="text-muted">
                                                        <i class="ph-hash me-1"></i>
                                                        ID: {{ $catalog->id }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="list-group-item px-0">
                                    <div class="d-flex align-items-start">
                                        Tidak ada katalog
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="content-article fs-6 mb-3" style="line-height: 1.8;">
                        {!! $news->CONTENT !!}
                    </div>
                    @if(!empty($news->LAMPIRAN_LINK))
                        <div class="mt-3 pt-3 border-top">
                            <h5 class="mb-3">
                                <i class="ph-link me-1 text-primary"></i>
                                Tautan Terkait
                            </h5>
                            <a href="{{ $news->LAMPIRAN_LINK }}" class="list-group-item list-group-item-action d-flex align-items-center" target="_blank" rel="noopener noreferrer">
                                <div class="me-3">
                                    @if(str_contains($news->LAMPIRAN_LINK, 'youtube.com') || str_contains($news->LAMPIRAN_LINK, 'youtu.be'))
                                        <i class="ph-youtube-logo text-danger fs-2"></i>
                                    @elseif(str_contains($news->LAMPIRAN_LINK, 'instagram.com'))
                                        <i class="ph-instagram-logo text-pink fs-2"></i>
                                    @elseif(str_contains($news->LAMPIRAN_LINK, 'facebook.com'))
                                        <i class="ph-facebook-logo text-primary fs-2"></i>
                                    @elseif(str_contains($news->LAMPIRAN_LINK, 'twitter.com') || str_contains($news->LAMPIRAN_LINK, 'x.com'))
                                        <i class="ph-twitter-logo text-info fs-2"></i>
                                    @elseif(str_contains($news->LAMPIRAN_LINK, 'linkedin.com'))
                                        <i class="ph-linkedin-logo text-primary fs-2"></i>
                                    @else
                                        <i class="ph-globe text-success fs-2"></i>
                                    @endif
                                </div>
                                <div class="flex-fill">
                                    <div class="text-muted fs-sm text-break">
                                        {{ $news->LAMPIRAN_LINK }}
                                    </div>
                                </div>
                                <div>
                                    <i class="ph-arrow-square-out text-primary"></i>
                                </div>
                            </a>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        <small>
                            <i class="ph-clock-counter-clockwise me-1"></i>
                            Terakhir diperbarui: {{ Carbon::parse($news->UPDATED_AT)->diffForHumans() }}
                        </small>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 col-md-3 mb-3 mb-md-0">
                            <div class="fs-4 text-primary mb-1">
                                <i class="ph-calendar-check"></i>
                            </div>
                            <div class="fw-semibold">Tanggal</div>
                            <div class="text-muted fs-sm">
                                {{ Carbon::parse($news->CREATED_AT)->isoFormat('D MMM Y') }}
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3 mb-md-0">
                            <div class="fs-4 text-success mb-1">
                                <i class="ph-eye"></i>
                            </div>
                            <div class="fw-semibold">Total Views</div>
                            <div class="text-muted fs-sm">{{ number_format($news->NUM_VIEW ?: 0) }}</div>
                        </div>
                        <div class="col-6 col-md-3 mb-3 mb-md-0">
                            <div class="fs-4 text-info mb-1">
                                <i class="ph-tag"></i>
                            </div>
                            <div class="fw-semibold">Kategori</div>
                            <div class="text-muted fs-sm">{{ $news->NAME_E_NEWS_KATEGORI }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="fs-4 text-warning mb-1">
                                <i class="ph-flag"></i>
                            </div>
                            <div class="fw-semibold">Status</div>
                            <div class="text-muted fs-sm">{{ $news->STATUS }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .content-article img {
        max-width: 100%;
        height: auto;
        border-radius: 0.375rem;
        margin: 1rem 0;
    }

    .content-article p {
        margin-bottom: 1rem;
        text-align: justify;
    }

    .content-article h1,
    .content-article h2,
    .content-article h3,
    .content-article h4,
    .content-article h5,
    .content-article h6 {
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }

    .content-article ul,
    .content-article ol {
        margin-bottom: 1rem;
        padding-left: 1.5rem;
    }

    .content-article blockquote {
        border-left: 4px solid #2196F3;
        padding-left: 1rem;
        margin: 1.5rem 0;
        font-style: italic;
        color: #666;
    }

    .content-article a {
        color: #2196F3;
        text-decoration: underline;
    }

    .content-article a:hover {
        color: #1976D2;
    }

    .list-group-item:hover {
        background-color: #f8f9fa;
    }
</style>
