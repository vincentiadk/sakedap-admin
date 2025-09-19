<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pengiriman - Daftar Pengiriman - <span class="fw-normal">Verifikasi</span>
            </h4>
        </div>
        <div class="collapse d-lg-block my-lg-auto ms-lg-auto" id="page-header">
            <div class="d-sm-flex align-items-center mb-3 mb-lg-0 ms-lg-3">
                <div class="d-inline-flex mt-3 mt-sm-0">
                    <a href="{{ url('delivery/list') }}" class="btn btn-primary">
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
                            <th class="table-primary align-top" width="20%">Resi</th>
                            <td class="align-top" width="30%">{{ $letter->RECEIPT_NO }}</td>
                            <th class="table-primary align-top" width="20%">Jasa Kirim</th>
                            <td class="align-top" width="30%">{{ $letter->NAME_JASA_PENGIRIMAN }}</td>
                        </tr>
                        <tr>
                            <th class="table-primary align-top" width="20%">Tgl Kirim</th>
                            <td class="align-top" width="30%">{{ $letter->LETTER_DATE ? Carbon::parse($letter->LETTER_DATE)->format('d/m/Y') : '' }}</td>
                            <th class="table-primary align-top" width="20%">Tgl Sampai</th>
                            <td class="align-top" width="30%">{{ $letter->ACCEPT_DATE ? Carbon::parse($letter->ACCEPT_DATE)->format('d/m/Y') : '' }}</td>
                        </tr>
                        <tr>
                            <th class="table-primary align-top" width="20%">Jumlah Paket</th>
                            <td class="align-top" width="30%">{{ $letter->JUMLAH_PAKET }}</td>
                            <th class="table-primary align-top" width="20%">Status</th>
                            <td class="align-top" width="30%">{{ $letter->STATUS }}</td>
                        </tr>
                        <tr>
                            <th class="table-primary align-top" width="20%">Pelaksana Serah</th>
                            <td class="align-top" width="30%">{{ $letter->NAME_PENERBIT }}</td>
                            <th class="table-primary align-top" width="20%">Kode Promo</th>
                            <td class="align-top" width="30%">{{ $letter->KODE_PROMO }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered w-100 display" id="datatable-client">
                    <thead class="text-bg-light">
                        <tr>
                            <th class="text-center" rowspan="2">No</th>
                            <th rowspan="2">Judul</th>
                            <th rowspan="2">Edisi</th>
                            <th rowspan="2">Jenis</th>
                            <th colspan="3" class="text-center">Jumlah Eksemplar</th>
                            <th rowspan="2">Alasan Ditolak</th>
                        </tr>
                        <tr>
                            <th class="text-center">Total</th>
                            <th class="text-center">Diterima</th>
                            <th class="text-center">Ditolak</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($letterDetail as $key => $ld)
                            <tr>
                                <input type="hidden" name="letter_detail_id[]" value="{{ $ld->LETTER_DETAIL_ID }}">
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td class="text-wrap">{{ $ld->TITLE }}</td>
                                <td class="text-wrap">{{ $ld->EDISI_SERIAL }}</td>
                                <td class="text-wrap">{{ $ld->NAME_WORKSHEET }}</td>
                                <td>
                                    <input type="number" class="form-control form-control-plaintext" name="letter_detail_quantity[]" value="{{ $ld->QUANTITY }}">
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="letter_detail_qty_accept[]" oninput="calculateQty(this, 'accept')" value="{{ $ld->QTY_ACCEPT ?: $ld->QUANTITY }}" {{ $disabled }}>
                                </td>
                                <td>
                                    <input type="number" class="form-control" name="letter_detail_qty_reject[]" oninput="calculateQty(this, 'reject')" value="{{ $ld->QTY_REJECT ?: 0 }}" {{ $disabled }}>
                                </td>
                                <td>
                                    <select class="form-select" name="letter_detail_remark[][]" multiple {{ $disabled }}>
                                        @if($ld->REMARK)
                                            @php $remark = explode(';', $ld->REMARK ?? ''); @endphp

                                            @foreach($remark as $r)
                                                <option value="{{ $r }}" selected>{{ $r }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if(!$disabled)
            <div class="card">
                <div class="card-body">
                    <div class="justify-content-end d-flex">
                        <div class="input-group me-2">
                            <span class="input-group-text">Status</span>
                            <select class="form-select wmin-200" name="status" id="status">
                                <option value="DALAM PENGIRIMAN" {{ $letter->STATUS == 'DALAM PENGIRIMAN' ? 'selected' : '' }}>DALAM PENGIRIMAN</option>
                                <option value="TERKIRIM" {{ $letter->STATUS == 'TERKIRIM' ? 'selected' : '' }}>TERKIRIM</option>
                                <option value="CEK FISIK" {{ $letter->STATUS == 'CEK FISIK' ? 'selected' : '' }}>CEK FISIK</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-warning text-nowrap me-2" onclick="submitted('save')">
                            <i class="ph-floppy-disk me-1"></i>
                            Simpan
                        </button>
                        <button type="button" class="btn btn-success text-nowrap" onclick="submitted('save-verification')">
                            <i class="ph-check me-1"></i>
                            Simpan & Verifikasi
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </form>
</div>

<script>
    $(function() {
        select2ServersideTag('select[name="letter_detail_remark[][]"]', 'problem', {}, {
            minimumInputLength: 0
        });

        $('#datatable-client').DataTable({
            scrollX: true
        });
    });

    function calculateQty(param, from) {
        var selector = $(param).closest('tr');
        var total = selector.find('input[name="letter_detail_quantity[]"]').val();
        var accept = selector.find('input[name="letter_detail_qty_accept[]"]').val();
        var reject = selector.find('input[name="letter_detail_qty_reject[]"]').val();

        var accpetValue = parseInt(total) - parseInt(reject);
        var rejectValue = parseInt(total) - parseInt(accept);

        if(from == 'accept') {
            selector.find('input[name="letter_detail_qty_reject[]"]').val(rejectValue);
        } else if(from == 'reject') {
            selector.find('input[name="letter_detail_qty_accept[]"]').val(accpetValue);
        }
    }

    function submitted(param) {
        $.ajax({
            url: '{{ url("delivery/list/verification/" . $letter->LETTER_ID) }}?param=' + param,
            type: 'POST',
            dataType: 'JSON',
            data: $('#form-data').serialize(),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            beforeSend: function() {
                onLoading('show', 'body');
            },
            success: function(response) {
                onLoading('close', 'body');

                if(response.code == 200) {
                    swalInit.fire({
                        title: 'Berhasil',
                        text: response.message,
                        icon: 'success',
                        showDenyButton: false,
                        showCancelButton: false,
                        confirmButtonText: 'Oke',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            onLoading('show', 'body');

                            location.href = '{{ url("delivery/list") }}';
                        }
                    });
                } else {
                    swalInit.fire({
                        title: 'Error',
                        text: response.message,
                        icon: 'error',
                        showCloseButton: true
                    });
                }
            },
            error: function(response) {
                onLoading('close', 'body');

                swalInit.fire({
                    html: '<b>' + response.responseJSON.exception + '</b><br>' + response.responseJSON.message,
                    icon: 'error',
                    showCloseButton: true
                });
            }
        });
    }
</script>
