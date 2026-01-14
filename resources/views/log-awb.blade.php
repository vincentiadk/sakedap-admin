<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                <span class="fw-normal">Log AWB</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <form method="GET" id="filter-form">
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="ph-calendar-blank me-1"></i>
                            Bulan
                        </span>
                        <input type="month" class="form-control wmin-200" name="month" id="month" value="{{ request()->month ?? date('Y-m') }}" oninput="onLoading('show', 'body'); this.form.submit()">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    @if(count($data->items()) > 0)
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <h6 class="mb-0 text-muted">Total Request</h6>
                                <h3 class="mb-0 mt-2 fw-semibold">{{ $data->total() }}</h3>
                            </div>
                            <div class="ms-3">
                                <i class="ph-list-bullets ph-3x text-primary opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <h6 class="mb-0 text-muted">Success (2xx)</h6>
                                <h3 class="mb-0 mt-2 fw-semibold text-success">
                                    {{ collect($data->items())->filter(function($item) {
                                        return Str::startsWith($item->STATUS_CODE, '2');
                                    })->count() }}
                                </h3>
                            </div>
                            <div class="ms-3">
                                <i class="ph-check-circle ph-3x text-success opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <h6 class="mb-0 text-muted">Client Error (4xx)</h6>
                                <h3 class="mb-0 mt-2 fw-semibold text-warning">
                                    {{ collect($data->items())->filter(function($item) {
                                        return Str::startsWith($item->STATUS_CODE, '4');
                                    })->count() }}
                                </h3>
                            </div>
                            <div class="ms-3">
                                <i class="ph-warning ph-3x text-warning opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-fill">
                                <h6 class="mb-0 text-muted">Server Error (5xx)</h6>
                                <h3 class="mb-0 mt-2 fw-semibold text-danger">
                                    {{ collect($data->items())->filter(function($item) {
                                        return Str::startsWith($item->STATUS_CODE, '5');
                                    })->count() }}
                                </h3>
                            </div>
                            <div class="ms-3">
                                <i class="ph-x-circle ph-3x text-danger opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="ph-file-text me-2 text-primary"></i>
                        <h6 class="mb-0 fw-semibold">Detail Log Request</h6>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary">
                        <i class="ph-calendar me-1"></i>
                        {{ Carbon::parse(request()->month ?? date('Y-m'))->isoFormat('MMMM YYYY') }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                @foreach($data->items() as $d)
                    @php
                        $statusCode = $d->STATUS_CODE;
                        $statusCodeFirst = Str::limit($statusCode, 1, '');

                        if($statusCodeFirst == 1) {
                            $statusCodeColor = 'primary';
                            $statusIcon = 'info';
                        } else if($statusCodeFirst == 2) {
                            $statusCodeColor = 'success';
                            $statusIcon = 'check-circle';
                        } else if($statusCodeFirst == 3) {
                            $statusCodeColor = 'info';
                            $statusIcon = 'arrow-bend-double-up-right';
                        } else if($statusCodeFirst == 4) {
                            $statusCodeColor = 'warning';
                            $statusIcon = 'warning';
                        } else if($statusCodeFirst == 5) {
                            $statusCodeColor = 'danger';
                            $statusIcon = 'x-circle';
                        }

                        $payload = $d->BODY;
                        $payloadDecode = json_decode($payload, true);
                        $payloadEncode = json_encode($payloadDecode, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

                        $response = $d->RES;
                        $responseDecode = json_decode($response, true);
                        $responseEncode = json_encode($responseDecode, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    @endphp
                    <div class="accordion" id="accordion-{{ $d->ID }}">
                        <div class="accordion-item border shadow-sm">
                            <h2 class="accordion-header">
                                <button class="accordion-button fw-semibold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsed-item-{{ $d->ID }}">
                                    <div class="d-flex align-items-center w-100">
                                        <div class="d-flex align-items-center">
                                            <i class="ph-{{ $statusIcon }} me-2 text-{{ $statusCodeColor }}"></i>
                                            <span class="badge bg-{{ $statusCodeColor }} bg-opacity-10 text-{{ $statusCodeColor }} me-3">
                                                {{ $statusCode }}
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column flex-fill">
                                            <span class="text-body">{{ $d->ENDPOINT }}</span>
                                            <small class="text-muted">
                                                <i class="ph-calendar-blank me-1"></i>
                                                {{ Carbon::parse($d->DATE_REQ)->isoFormat('dddd, D MMMM Y - HH:mm:ss') }}
                                            </small>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapsed-item-{{ $d->ID }}" class="accordion-collapse collapse" data-bs-parent="#accordion-{{ $d->ID }}">
                                <div class="accordion-body bg-light">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="card border-0 shadow-sm h-100">
                                                <div class="card-header bg-white border-bottom">
                                                    <div class="d-flex align-items-center">
                                                        <i class="ph-arrow-up me-2 text-primary"></i>
                                                        <h6 class="mb-0 fw-semibold">Request Payload</h6>
                                                    </div>
                                                </div>
                                                <div class="card-body p-0">
                                                    <pre class="mb-0 p-3 bg-white rounded-bottom" style="max-height: 400px; overflow-y: auto;"><code class="language-json">{!! $payloadEncode !!}</code></pre>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border-0 shadow-sm h-100">
                                                <div class="card-header bg-white border-bottom">
                                                    <div class="d-flex align-items-center">
                                                        <i class="ph-arrow-down me-2 text-{{ $statusCodeColor }}"></i>
                                                        <h6 class="mb-0 fw-semibold">Response</h6>
                                                        <span class="badge bg-{{ $statusCodeColor }} bg-opacity-10 text-{{ $statusCodeColor }} ms-auto">
                                                            {{ $statusCode }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="card-body p-0">
                                                    <pre class="mb-0 p-3 bg-white rounded-bottom" style="max-height: 400px; overflow-y: auto;"><code class="language-json">{!! $responseEncode !!}</code></pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="card-footer border-top">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="text-muted">
                        <i class="ph-info me-1"></i>
                        Menampilkan {{ $data->firstItem() ?? 0 }} - {{ $data->lastItem() ?? 0 }} dari {{ $data->total() }} data
                    </div>
                    <div>
                        {{ $data->onEachSide(2)->appends(request()->query())->links('pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <div class="mb-3">
                    <i class="ph-database ph-4x text-muted opacity-50"></i>
                </div>
                <h5 class="fw-semibold mb-2">Tidak Ada Data</h5>
                <p class="text-muted mb-0">
                    Tidak ada log request untuk bulan <strong>{{ Carbon::parse(request()->month ?? date('Y-m'))->isoFormat('MMMM YYYY') }}</strong>
                </p>
            </div>
        </div>
    @endif
</div>

<style>
    pre {
        background-color: #f8f9fa;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        line-height: 1.5;
    }

    pre code {
        color: #212529;
        font-family: 'Courier New', monospace;
    }

    .accordion-button:not(.collapsed) {
        background-color: #f8f9fa;
        box-shadow: none;
    }

    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(0,0,0,.125);
    }

    .accordion-button::after {
        margin-left: auto;
    }
</style>
