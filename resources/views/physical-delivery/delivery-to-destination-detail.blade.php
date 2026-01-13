<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pengiriman Fisik - Pengiriman Sampai ke Tujuan - <span class="fw-normal">Detail</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <a href="{{ url('physical-delivery/delivery-to-destination') }}" class="btn btn-primary">
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
                    <i class="ph-info me-1 text-success"></i>
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
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="ph-package me-1 text-success"></i>
                        <h6 class="mb-0 fw-semibold">Daftar Koleksi Pengiriman</h6>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success">
                        <i class="ph-stack me-1"></i>
                        {{ count($letterDetail ?? []) }} Item
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
                                <th class="text-nowrap" style="min-width: 120px">
                                    <i class="ph-books me-1"></i>
                                    Jilid
                                </th>
                                <th class="text-nowrap" style="min-width: 120px">
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
                                    <td class="text-center align-middle fw-semibold">{{ $key + 1 }}</td>
                                    <td class="text-center align-middle">
                                        <a href="{{ $fileCover }}" data-lightbox="cover-{{ $code }}" data-title="{{ $ld->TITLE }}">
                                            <img src="{{ $fileCover }}" class="img-fluid img-thumbnail rounded" style="max-width: 70px; height: auto;">
                                        </a>
                                    </td>
                                    <td class="align-middle text-wrap">
                                        <div class="fw-semibold text-dark">{{ $ld->TITLE }}</div>
                                    </td>
                                    <td class="align-middle">{{ $ld->ISBN }}</td>
                                    <td class="align-middle text-wrap">{{ $ld->NOMORPANGGILJILID ?: '-' }}</td>
                                    <td class="align-middle text-wrap">{{ $ld->EDISI_SERIAL ?: '-' }}</td>
                                    <td class="text-center align-middle">
                                        <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold">
                                            {{ $ld->COPY }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach

                            @if(count($letterDetail ?? []) == 0)
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="ph-package text-muted mb-2" style="font-size: 3rem;"></i>
                                            <span class="text-muted">Tidak ada data koleksi</span>
                                        </div>
                                    </td>
                                </tr>
                            @endif
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
            scrollY: '450px',
            scrollX: true,
            scrollCollapse: true,
            searching: true,
            ordering: true,
            order: [[0, 'asc']],
        });
    });
</script>
