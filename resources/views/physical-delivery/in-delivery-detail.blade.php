<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pengiriman Fisik - Dalam Pengiriman - <span class="fw-normal">Detail</span>
            </h4>
        </div>
        <div class="collapse d-lg-block my-lg-auto ms-lg-auto" id="page-header">
            <div class="d-sm-flex align-items-center mb-3 mb-lg-0 ms-lg-3">
                <div class="d-inline-flex mt-3 mt-sm-0">
                    <a href="{{ url('physical-delivery/in-delivery') }}" class="btn btn-primary">
                        <i class="ph-arrow-left me-1"></i>
                        Kembali ke Tabel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    <form id="form-data">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th class="table-success" width="20%">Tanggal</th>
                            <td width="30%">{{ Carbon::parse($letter->LETTER_DATE)->isoFormat('D MMM Y') }}, {{ Carbon::parse($letter->LETTER_DATE)->format('H:i') }}</td>
                            <th class="table-success" width="20%">No Surat</th>
                            <td width="30%">{{ $letter->LETTER_NUMBER }}</td>
                        </tr>
                        <tr>
                            <th class="table-success" width="20%">Pengirim</th>
                            <td width="30%">{{ $letter->SENDER }}</td>
                            <th class="table-success" width="20%">Telp</th>
                            <td width="30%">{{ $letter->PHONE }}</td>
                        </tr>
                        <tr>
                            <th class="table-success" width="20%">Jasa Kirim</th>
                            <td width="30%">{{ $letter->NAME_JASA_PENGIRIMAN }}</td>
                            <th class="table-success" width="20%">Tujuan</th>
                            <td width="30%">{{ $letter->NAME_BRANCH }}</td>
                        </tr>
                        <tr>
                            <th class="table-success" width="20%">Resi</th>
                            <td width="30%">{{ $letter->RECEIPT_NO }}</td>
                            <th class="table-success" width="20%">Biaya Kirim</th>
                            <td width="30%">Rp {{ number_format($letter->BIAYA_KIRIM) }}</td>
                        </tr>
                        <tr>
                            <th class="table-success" width="20%">Berat</th>
                            <td width="30%">{{ number_format(($letter->BERAT ?? 0) / 1000, 2, ',', '.') }} Kg</td>
                            <th class="table-success" width="20%">Pelaksana Serah</th>
                            <td width="30%">{{ $letter->PENERBIT_ID }} | {{ $letter->NAME_PENERBIT }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                @if($receipt)
                    <div class="border p-3 rounded">
                        <div class="list-feed list-feed-solid">
                            @foreach($receipt->manifest as $key => $m)
                                <div class="list-feed-item {{ $key == 0 ? 'border-success' : 'border-primary' }}">
                                    <div class="fw-semibold">{{ $m->manifest_code }}</div>
                                    <div class="text-muted"><small>{{ $m->manifest_date }} {{ $m->manifest_time }}</small></div>
                                    <div>{{ $m->manifest_description }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="alert alert-info">
                        Tidak ada data tracking pengiriman
                    </div>
                @endif
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered w-100 display" id="datatable-client">
                    <thead class="text-bg-light">
                        <tr>
                            <th class="text-center text-nowrap">No</th>
                            <th class="text-nowrap">Cover</th>
                            <th class="text-nowrap">Judul</th>
                            <th class="text-nowrap">ISBN</th>
                            <th class="text-nowrap">Jilid</th>
                            <th class="text-nowrap">Edisi</th>
                            <th class="text-nowrap">Jumlah</th>
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
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td class="text-center">
                                    <a href="{{ $fileCover }}" data-lightbox="cover-{{ $code }}" data-title="{{ $ld->TITLE }}">
                                        <img src="{{ $fileCover }}" class="img img-fluid img-thumbnail" style="max-width:70px;">
                                    </a>
                                </td>
                                <td class="text-wrap">{{ $ld->TITLE }}</td>
                                <td class="text-wrap">{{ $ld->ISBN }}</td>
                                <td class="text-wrap">{{ $ld->NOMORPANGGILJILID }}</td>
                                <td class="text-wrap">{{ $ld->EDISI_SERIAL }}</td>
                                <td class="text-wrap">{{ $ld->COPY }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
            scrollX: false,
            scrollCollapse: true,
        });
    });
</script>
