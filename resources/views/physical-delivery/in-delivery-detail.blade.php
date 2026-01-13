<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pengiriman Fisik - Dalam Pengiriman - <span class="fw-normal">Detail</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <a href="{{ url('physical-delivery/in-delivery') }}" class="btn btn-primary">
                    <i class="ph-arrow-left me-1"></i>
                    Kembali ke Tabel
                </a>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    <form id="form-data">
        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center">
                    <i class="ph-info me-1 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Informasi Pengiriman</h6>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="text-muted fw-semibold small mb-1">
                                        <i class="ph-calendar-blank me-1"></i>
                                        Tanggal
                                    </label>
                                    <div class="fw-semibold text-dark">
                                        {{ Carbon::parse($letter->LETTER_DATE)->isoFormat('D MMMM Y') }}, {{ Carbon::parse($letter->LETTER_DATE)->format('H:i') }} WIB
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="text-muted fw-semibold small mb-1">
                                        <i class="ph-user me-1"></i>
                                        Pengirim
                                    </label>
                                    <div class="fw-semibold text-dark">{{ $letter->SENDER ?: 'Tidak ada' }}</div>
                                </div>
                                <div class="form-group">
                                    <label class="text-muted fw-semibold small mb-1">
                                        <i class="ph-truck me-1"></i>
                                        Jasa Kirim
                                    </label>
                                    <div class="fw-semibold text-dark">{{ $letter->NAME_JASA_PENGIRIMAN ?: 'Tidak ada' }}</div>
                                </div>
                                <div class="form-group">
                                    <label class="text-muted fw-semibold small mb-1">
                                        <i class="ph-barcode me-1"></i>
                                        No Resi
                                    </label>
                                    <div class="fw-semibold text-dark">{{ $letter->RECEIPT_NO ?: 'Tidak ada' }}</div>
                                </div>
                                <div class="form-group">
                                    <label class="text-muted fw-semibold small mb-1">
                                        <i class="ph-scales me-1"></i>
                                        Berat
                                    </label>
                                    <div class="fw-semibold text-dark">{{ number_format(($letter->BERAT ?: 0) / 1000, 2, ',', '.') }} Kg</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="text-muted fw-semibold small mb-1">
                                        <i class="ph-file-text me-1"></i>
                                        No Surat
                                    </label>
                                    <div class="fw-semibold text-dark">{{ $letter->LETTER_NUMBER ?: 'Tidak ada' }}</div>
                                </div>
                                <div class="form-group">
                                    <label class="text-muted fw-semibold small mb-1">
                                        <i class="ph-phone me-1"></i>
                                        Telepon
                                    </label>
                                    <div class="fw-semibold text-dark">{{ $letter->PHONE ?: 'Tidak ada' }}</div>
                                </div>
                                <div class="form-group">
                                    <label class="text-muted fw-semibold small mb-1">
                                        <i class="ph-map-pin me-1"></i>
                                        Tujuan
                                    </label>
                                    <div class="fw-semibold text-dark">{{ $letter->NAME_BRANCH ?: 'Tidak ada' }}</div>
                                </div>
                                <div class="form-group">
                                    <label class="text-muted fw-semibold small mb-1">
                                        <i class="ph-currency-circle-dollar me-1"></i>
                                        Biaya Kirim
                                    </label>
                                    <div class="fw-semibold text-dark">Rp {{ number_format($letter->BIAYA_KIRIM ?: 0, 0, ',', '.') }}</div>
                                </div>
                                <div class="form-group">
                                    <label class="text-muted fw-semibold small mb-1">
                                        <i class="ph-buildings me-1"></i>
                                        Pelaksana Serah
                                    </label>
                                    <div class="fw-semibold text-dark">{{ $letter->PENERBIT_ID }} | {{ $letter->NAME_PENERBIT }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center">
                    <i class="ph-map-trifold me-1 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Tracking Pengiriman</h6>
                </div>
            </div>
            <div class="card-body">
                @if($receipt)
                    <div class="border p-3 rounded bg-light">
                        <div class="list-feed list-feed-solid">
                            @foreach($receipt->manifest as $key => $m)
                                <div class="list-feed-item {{ $key == 0 ? 'border-success' : 'border-primary' }}">
                                    <div class="fw-semibold">
                                        <i class="ph-check-circle me-1 {{ $key == 0 ? 'text-success' : 'text-primary' }}"></i>
                                        {{ $m->manifest_code }}
                                    </div>
                                    <div class="text-muted">
                                        <small>
                                            <i class="ph-calendar-blank me-1"></i>
                                            {{ $m->manifest_date }} {{ $m->manifest_time }}
                                        </small>
                                    </div>
                                    <div class="mt-1">{{ $m->manifest_description }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="alert alert-info border-0 d-flex align-items-center">
                        <i class="ph-info me-2" style="font-size: 1.5rem;"></i>
                        <div>
                            <div class="fw-semibold">Tidak ada data tracking</div>
                            <small>Data tracking pengiriman belum tersedia</small>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="ph-package me-1 text-primary"></i>
                        <h6 class="mb-0 fw-semibold">Daftar Koleksi Pengiriman</h6>
                    </div>
                    <span class="badge bg-info bg-opacity-10 text-info" id="total-records">
                        <i class="ph-list-checks me-1"></i>
                        <span id="record-count">{{ count($letterDetail ?? []) }}</span> Item
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered display nowrap w-100" id="datatable-client">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center text-nowrap" style="width: 60px">
                                    <i class="ph-hash"></i>
                                </th>
                                <th class="text-center text-nowrap" style="width: 100px">
                                    <i class="ph-image"></i>
                                    Cover
                                </th>
                                <th class="text-nowrap" style="min-width: 200px">
                                    <i class="ph-book-open me-1"></i>
                                    Judul
                                </th>
                                <th class="text-nowrap" style="min-width: 130px">
                                    <i class="ph-identification-card me-1"></i>
                                    ISBN
                                </th>
                                <th class="text-nowrap" style="min-width: 100px">
                                    <i class="ph-books me-1"></i>
                                    Jilid
                                </th>
                                <th class="text-nowrap" style="min-width: 100px">
                                    <i class="ph-note me-1"></i>
                                    Edisi
                                </th>
                                <th class="text-center text-nowrap" style="min-width: 100px">
                                    <i class="ph-stack me-1"></i>
                                    Jumlah
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($letterDetail ?? [] as $key => $ld)
                                @php
                                    $code = str_replace('-', '', $ld->ISBN);
                                    $fileCover = asset('assets/no-file.jpg');

                                    if ($code) {
                                        $getDataISBN = ISBN::get('search', [
                                            'code' => $code
                                        ], true);

                                        if($getDataISBN) {
                                            if(isset($getDataISBN->cover_file_name)) {
                                                if($getDataISBN->cover_file_name) {
                                                    $fileCover = $getDataISBN->cover_file_name;
                                                }
                                            }
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center fw-semibold">{{ $key + 1 }}</td>
                                    <td class="text-center">
                                        <a href="{{ $fileCover }}" data-lightbox="cover-{{ $code }}" data-title="{{ $ld->TITLE }}">
                                            <img src="{{ $fileCover }}" class="img-fluid img-thumbnail rounded shadow-sm" style="max-width: 70px; max-height: 100px; object-fit: cover;">
                                        </a>
                                    </td>
                                    <td class="text-wrap">{{ $ld->TITLE }}</td>
                                    <td class="text-wrap">{{ $ld->ISBN ?: '-' }}</td>
                                    <td class="text-wrap">{{ $ld->NOMORPANGGILJILID ?: '-' }}</td>
                                    <td class="text-wrap">{{ $ld->EDISI_SERIAL ?: '-' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-primary bg-opacity-10 text-primary">
                                            {{ $ld->COPY }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(function() {
        $('#datatable-client').DataTable({
            paging: false,
            lengthChange: false,
            info: false,
            scrollY: '400px',
            scrollX: true,
            scrollCollapse: true,
            language: {
                search: '<i class="ph-magnifying-glass me-1"></i>',
                searchPlaceholder: 'Cari data...',
                zeroRecords: '<div class="text-center py-4"><i class="ph-file-x text-muted" style="font-size: 3rem;"></i><div class="mt-2 text-muted">Tidak ada data yang ditemukan</div></div>',
                emptyTable: '<div class="text-center py-4"><i class="ph-file-x text-muted" style="font-size: 3rem;"></i><div class="mt-2 text-muted">Tidak ada data tersedia</div></div>'
            },
            initComplete: function (settings, json) {
                var table = this.api();
                const searchInput = $('div.dataTables_filter input');

                searchInput.addClass('form-control');

                searchInput.off().unbind();

                searchInput.on('keyup', debounce(function () {
                    table.search(this.value).draw();
                }, 500));
            }
        });
    });
</script>
