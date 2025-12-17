<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Administrasi Sistem - Tutorial - <span class="fw-normal">Preview</span>
            </h4>
        </div>
        <div class="collapse d-lg-block my-lg-auto ms-lg-auto" id="page-header">
            <div class="d-sm-flex align-items-center mb-3 mb-lg-0 ms-lg-3">
                <div class="d-inline-flex mt-3 mt-sm-0">
                    <a href="{{ url('administration-system/tutorial') }}" class="btn btn-primary">
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
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-img-top position-relative overflow-hidden" style="max-height: 450px;">
                    <img src="{{ $news->IMAGE ? url('stream-file') . '?type=gambar_artikel&id=' . $news->ID . '&filename=' . $news->IMAGE : asset('assets/no-file.jpg') }}" class="img-fluid w-100" style="object-fit: cover; height: 450px;" alt="{{ $news->TITLE }}">
                    <div class="position-absolute top-0 end-0 m-3">
                        @if($news->STATUS == 'PUBLISH')
                            <span class="badge bg-success fs-6 shadow">
                                <i class="ph-check-circle me-1"></i>{{ $news->STATUS }}
                            </span>
                        @else
                            <span class="badge bg-warning fs-6 shadow">
                                <i class="ph-clock me-1"></i>{{ $news->STATUS }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <span class="badge bg-primary bg-opacity-10 text-primary fs-6 fw-semibold px-3 py-2">
                            <i class="ph-tag me-1"></i>{{ $news->NAME_E_NEWS_KATEGORI }}
                        </span>
                        <span class="badge bg-info bg-opacity-10 text-info fs-6 fw-semibold px-3 py-2 ms-2">
                            <i class="ph-translate me-1"></i>{{ $news->LANG == 'id' ? 'Bahasa Indonesia' : 'English' }}
                        </span>
                    </div>
                    <h2 class="card-title fw-bold mb-3" style="line-height: 1.4;">
                        {{ $news->TITLE }}
                    </h2>
                    <div class="d-flex flex-wrap align-items-center text-muted mb-4 pb-4 border-bottom">
                        <div class="d-flex align-items-center me-4 mb-2">
                            <i class="ph-calendar-blank me-2 text-primary"></i>
                            <span>{{ Carbon::parse($news->CREATED_AT)->isoFormat('D MMMM Y') }}</span>
                        </div>
                        <div class="d-flex align-items-center me-4 mb-2">
                            <i class="ph-clock me-2 text-primary"></i>
                            <span>{{ Carbon::parse($news->CREATED_AT)->format('H:i') }} WIB</span>
                        </div>
                        <div class="d-flex align-items-center me-4 mb-2">
                            <i class="ph-eye me-2 text-primary"></i>
                            <span>{{ number_format($news->NUM_VIEW ?: 0) }} views</span>
                        </div>
                    </div>
                    <div class="content-article fs-6" style="line-height: 1.8;">
                        {!! $news->CONTENT !!}
                    </div>
                    @if(!empty($news->LAMPIRAN_LINK))
                        <div class="mt-4 pt-4 border-top">
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
                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="fs-4 text-primary mb-1">
                                <i class="ph-calendar-check"></i>
                            </div>
                            <div class="fw-semibold">Tanggal</div>
                            <div class="text-muted fs-sm">
                                {{ Carbon::parse($news->CREATED_AT)->isoFormat('D MMM Y') }}
                            </div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="fs-4 text-success mb-1">
                                <i class="ph-eye"></i>
                            </div>
                            <div class="fw-semibold">Total Views</div>
                            <div class="text-muted fs-sm">{{ number_format($news->NUM_VIEW ?: 0) }}</div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="fs-4 text-info mb-1">
                                <i class="ph-tag"></i>
                            </div>
                            <div class="fw-semibold">Kategori</div>
                            <div class="text-muted fs-sm">{{ $news->NAME_E_NEWS_KATEGORI }}</div>
                        </div>
                        <div class="col-md-3">
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
