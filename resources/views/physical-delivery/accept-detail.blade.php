<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pengiriman Fisik - Diterima - <span class="fw-normal">Detail</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <a href="{{ url('physical-delivery/accept') }}" class="btn btn-primary">
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
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="ph-package me-1 text-primary"></i>
                        <h6 class="mb-0 fw-semibold">Daftar Koleksi yang Diterima</h6>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success">
                        <i class="ph-check-circle me-1"></i>
                        Status: Diterima
                    </span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered w-100 display" id="datatable-client">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center text-nowrap" rowspan="2" style="width: 60px">
                                    <i class="ph-hash"></i>
                                </th>
                                <th class="text-center text-nowrap" rowspan="2" style="width: 80px">
                                    <i class="ph-check-square"></i>
                                    Check
                                </th>
                                <th class="text-center text-nowrap" rowspan="2" style="width: 100px">
                                    <i class="ph-image"></i>
                                    Cover
                                </th>
                                <th class="text-nowrap" rowspan="2" style="min-width: 200px">
                                    <i class="ph-book-open me-1"></i>
                                    Judul
                                </th>
                                <th class="text-nowrap" rowspan="2" style="min-width: 200px">
                                    <i class="ph-identification-card me-1"></i>
                                    ISBN
                                </th>
                                <th class="text-nowrap" rowspan="2" style="min-width: 100px">
                                    <i class="ph-books me-1"></i>
                                    Jilid
                                </th>
                                <th class="text-nowrap" rowspan="2" style="min-width: 100px">
                                    <i class="ph-note me-1"></i>
                                    Edisi
                                </th>
                                <th class="text-center text-nowrap" colspan="2">
                                    <i class="ph-package me-1"></i>
                                    Jumlah Eks
                                </th>
                                <th class="text-nowrap" rowspan="2" style="min-width: 150px">
                                    <i class="ph-x-circle me-1"></i>
                                    Alasan Ditolak
                                </th>
                                <th class="text-nowrap" rowspan="2" style="min-width: 150px">
                                    <i class="ph-note-pencil me-1"></i>
                                    Catatan
                                </th>
                            </tr>
                            <tr>
                                <th class="text-center text-nowrap" style="min-width: 100px">
                                    <i class="ph-check-circle me-1"></i>
                                    Diterima
                                </th>
                                <th class="text-center text-nowrap" style="min-width: 100px">
                                    <i class="ph-x-circle me-1"></i>
                                    Ditolak
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($letterDetail ?? [] as $key => $ld)
                                @php
                                    $code = str_replace('-', '', $ld->ISBN);
                                    $getDataISBN = null;

                                    if ($code) {
                                        $getDataISBN = ISBN::get('search', [
                                            'code' => $code
                                        ], true);
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center fw-semibold">{{ $key + 1 }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                            {{ $ld->RECEIVED_BY ?: '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ Main::getCoverISBN($getDataISBN->cover_file_name ?? null) }}" data-lightbox="cover-{{ $code }}" data-title="{{ $ld->TITLE }}">
                                            <img src="{{ Main::getCoverISBN($getDataISBN->cover_file_name ?? null) }}" class="img-fluid img-thumbnail shadow-sm" style="max-width: 70px; max-height: 100px; object-fit: cover;">
                                        </a>
                                    </td>
                                    <td class="align-middle text-wrap">
                                        <div class="fw-semibold">{{ $ld->TITLE }}</div>
                                    </td>
                                    <td class="align-middle text-nowrap" style="min-width: 250px;">
                                        <div class="input-group flex-nowrap">
                                            <input type="text" class="form-control form-control-sm" id="field-isbn-{{ $ld->LETTER_DETAIL_ID }}" value="{{ $ld->ISBN ?: '' }}" style="width:150px; flex:none;">
                                            <button type="button" class="btn btn-sm btn-warning" onclick="isbnNumbering({{ $ld->LETTER_DETAIL_ID }})">Simpan</button>
                                        </div>
                                    </td>
                                    <td class="align-middle">{{ $ld->NOMORPANGGILJILID ?: '-' }}</td>
                                    <td class="align-middle">{{ $ld->EDISI_SERIAL ?: '-' }}</td>
                                    <td class="text-center align-middle">
                                        <span class="badge bg-success">{{ $ld->QTY_ACCEPT ?: 0 }}</span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge bg-danger">{{ $ld->QTY_REJECT ?: 0 }}</span>
                                    </td>
                                    <td class="align-middle">
                                        @php $remark = explode(';', $ld->REMARK ?? ''); @endphp
                                        @if($remark && count(array_filter($remark)) > 0)
                                            <ul class="m-0 ps-3">
                                                @foreach($remark as $r)
                                                    @if(!empty($r))
                                                        <li class="text-danger small">{{ $r }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if($ld->ISBN_STATUS)
                                            <span class="badge bg-info bg-opacity-10 text-info">{{ $ld->ISBN_STATUS }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
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
            paging: true,
            lengthChange: true,
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
            info: true,
            searching: true,
            scrollY: false,
            scrollX: true,
            scrollCollapse: true,
            autoWidth: false,
            order: [[0, 'asc']],
            columnDefs: [
                {
                    targets: [2],
                    orderable: false
                }
            ],
        });
    });

    function isbnNumbering(id) {
        swalInit.fire({
            title: 'Penomoran ISBN',
            text: 'Apakah anda yakin menambah/mengganti nomor ISBN pada data penerimaan ini? Menambahkan ISBN pada data penerimaan akan menghapus tagihan ISBN pelaksana serah ke {{ $letter->NAME_BRANCH }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, simpan!',
            cancelButtonText: 'Tidak, batalkan!',
        }).then(function(result) {
            if(result.value) {
                $.ajax({
                    url: '{{ url("physical-delivery/accept/isbn-numbering") }}',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        isbn: $('#field-isbn-' + id).val(),
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    beforeSend: function() {
                        onLoading('show', 'body');
                    },
                    success: function(response) {
                        onLoading('close', 'body');

                        if(response.code == 200) {
                            notification('success', response.message);
                        } else if(response.code == 400) {
                            $.each(response.error, function(i, val) {
                                notification('error', val);
                            });
                        } else {
                            swalInit.fire({
                                title: 'Oops ...',
                                text: response.message,
                                icon: 'warning',
                                showCloseButton: false
                            });
                        }
                    },
                    error: function(response) {
                        onLoading('close', 'body');
                        responseError(response);
                    }
                });
            }
        });
    }
</script>
