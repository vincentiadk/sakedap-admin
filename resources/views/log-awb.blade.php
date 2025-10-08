<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                <span class="fw-normal">Log AWB</span>
            </h4>
        </div>
        <div class="collapse d-lg-block my-lg-auto ms-lg-auto" id="page-header">
            <div class="d-sm-flex align-items-center mb-3 mb-lg-0 ms-lg-3">
                <div class="d-inline-flex mt-3 mt-sm-0">
                    <div class="ms-sm-auto my-sm-auto">
                        <form>
                            <div class="input-group">
                                <span class="input-group-text">Bulan</span>
                                <input type="month" class="form-control wmin-200" name="month" id="month" value="{{ request()->month ?? date('Y-m') }}" oninput="onLoading('show', 'body'); this.form.submit()">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    @if(count($data->items()) > 0)
        <div class="card">
            <div class="card-body">
                @foreach($data->items() as $d)
                    @php
                        $statusCode = $d->STATUS_CODE;
                        $statusCodeFirst = Str::limit($statusCode, 1, '');

                        if($statusCodeFirst == 1) {
                            $statusCodeColor = 'primary';
                        } else if($statusCodeFirst == 2) {
                            $statusCodeColor = 'success';
                        } else if($statusCodeFirst == 3) {
                            $statusCodeColor = 'info';
                        } else if($statusCodeFirst == 4) {
                            $statusCodeColor = 'warning';
                        } else if($statusCodeFirst == 5) {
                            $statusCodeColor = 'danger';
                        }

                        $payload = $d->BODY;
                        $payloadDecode = json_decode($payload, true);
                        $payloadEncode = json_encode($payloadDecode, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

                        $response = $d->RES;
                        $responseDecode = json_decode($response, true);
                        $responseEncode = json_encode($responseDecode, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    @endphp
                    <div class="accordion" id="accordion_collapsed">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button fw-semibold collapsed link-{{ $statusCodeColor }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapsed-item-{{ $d->ID }}">
                                    <div class="bg-{{ $statusCodeColor }} rounded-pill p-1 me-2"></div>
                                    {{ $statusCode }}
                                    <span class="text-muted ms-2 fw-normal fst-italic">{{ $d->ENDPOINT }}</span>
                                    <span class="ms-3 text-dark fw-normal">
                                        {{ Carbon::parse($d->DATE_REQ)->isoFormat('dddd, D MMMM Y') }}
                                    </span>
                                </button>
                            </h2>
                            <div id="collapsed-item-{{ $d->ID }}" class="accordion-collapse collapse" data-bs-parent="#accordion_collapsed">
                                <div class="accordion-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="fw-bold border-bottom pb-2 mb-2">Payload</div>
                                            <pre>
                                                <code class="language-json">
                                                    {!! $payloadEncode !!}
                                                </code>
                                            </pre>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="fw-bold border-bottom pb-2 mb-2">Response</div>
                                            <pre>
                                                <code class="language-json">
                                                    {!! $responseEncode !!}
                                                </code>
                                            </pre>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                {{ $data->onEachSide(5)->appends(request()->query())->links('pagination.bootstrap-5') }}
            </div>
        </div>
    @else
        <div class="alert alert-info text-center">Tidak ada data</div>
    @endif
</div>
