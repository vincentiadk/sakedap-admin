<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pengiriman Fisik - Verifikasi Pengiriman - <span class="fw-normal">Detail</span>
            </h4>
        </div>
        <div class="collapse d-lg-block my-lg-auto ms-lg-auto" id="page-header">
            <div class="d-sm-flex align-items-center mb-3 mb-lg-0 ms-lg-3">
                <div class="d-inline-flex mt-3 mt-sm-0">
                    <a href="{{ url('physical-delivery/delivery-verification') }}" class="btn btn-primary">
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
                            <th class="table-success align-top" width="15%">Resi</th>
                            <td class="align-top" width="35%">{{ $letter->RECEIPT_NO }}</td>
                            <th class="table-success align-top" width="15%">Jasa Kirim</th>
                            <td class="align-top" width="35%">{{ $letter->NAME_JASA_PENGIRIMAN }}</td>
                        </tr>
                        <tr>
                            <th class="table-success align-top" width="15%">Tgl Kirim</th>
                            <td class="align-top" width="35%">{{ $letter->LETTER_DATE ? Carbon::parse($letter->LETTER_DATE)->isoFormat('dddd, D MMMM Y') : '' }}</td>
                            <th class="table-success align-top" width="15%">Tgl Sampai</th>
                            <td class="align-top" width="35%">{{ $letter->ACCEPT_DATE ? Carbon::parse($letter->ACCEPT_DATE)->isoFormat('dddd, D MMMM Y') : '' }}</td>
                        </tr>
                        <tr>
                            <th class="table-success align-top" width="15%">Jumlah Paket</th>
                            <td class="align-top" width="35%">{{ $letter->JUMLAH_PAKET }}</td>
                            <th class="table-success align-top" width="15%">Status</th>
                            <td class="align-top" width="35%">{{ $letter->STATUS }}</td>
                        </tr>
                        <tr>
                            <th class="table-success align-top" width="15%">Pelaksana Serah</th>
                            <td class="align-top" width="35%">{{ $letter->PENERBIT_ID . ' | ' . $letter->NAME_PENERBIT }}</td>
                            <th class="table-success align-top" width="15%">Kode Promo</th>
                            <td class="align-top" width="35%">{{ $letter->KODE_PROMO }}</td>
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
                            <th class="text-center" rowspan="2">Check</th>
                            <th rowspan="2">Cover</th>
                            <th rowspan="2">Judul</th>
                            <th rowspan="2">ISBN</th>
                            <th rowspan="2">Jilid</th>
                            <th rowspan="2">Edisi</th>
                            <th colspan="2" class="text-center">Total</th>
                            <th colspan="2" class="text-center">Jumlah Eks</th>
                            <th rowspan="2">Alasan Ditolak</th>
                            <th rowspan="2">Catatan</th>
                        </tr>
                        <tr>
                            <th class="text-center">Disistem</th>
                            <th class="text-center">Dikirim</th>
                            <th class="text-center">Diterima</th>
                            <th class="text-center">Ditolak</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($letterDetail ?? [] as $key => $ld)
                            @php
                                $strRand = Str::random(5);
                                $code = str_replace('-', '', $ld->ISBN);
                                $totalSystem = 0;
                                $totalSent = $ld->COPY ?? 0;
                                $totalReject = $totalSent;
                                $fileCover = asset('assets/no-file.jpg');

                                $checked = $ld->CHECKED;
                                $verifiedBy = $ld->VERIFIED_BY;
                                $currentUsername = session('username');
                                $isAdmin = !Main::isNotSuperAdmin(); 
                                $isOpen = ($checked != 1 && empty($verifiedBy));
                                $isOwner = ($verifiedBy == $currentUsername);
                                $canEdit = $isAdmin || $isOpen || $isOwner;

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

                                    $sqlLetterDetail = "select coalesce(sum(qty_accept), 0) as total_letter_detail from letter_detail where isbn = '$code'";
                                    $letterDetail = QueryAPI::get($sqlLetterDetail, true);

                                    $sqlCollection = "select count(id) AS total from collections where isbn = '$code' and source_id = 6";
                                    $collection = QueryAPI::get($sqlCollection, true);

                                    $totalLetterDetail = $letterDetail->TOTAL_LETTER_DETAIL ?? 0;
                                    $totalCollection = $collection->TOTAL ?? 0;

                                    if ($totalLetterDetail > 0) {
                                        $totalSystem += $totalLetterDetail;
                                    } else if ($totalCollection > 0) {
                                        $totalSystem += $totalCollection;
                                    }
                                }

                                $totalAccept = 0;

                                if ($totalSystem == 0 || $totalSystem == 1) {
                                    if ($totalSent == 1) {
                                        $totalAccept = 1;
                                        $totalReject -= 1;
                                    } else {
                                        if (Main::isNotSuperAdmin()) {
                                            $totalAccept = 1;
                                            $totalReject -= 1;
                                        } else {
                                            $totalAccept = 2;
                                            $totalReject -= 2;
                                        }
                                    }
                                }

                                if($totalSent >= 2) {
                                    $maxAccept = Main::isNotSuperAdmin() ? 1 : 2;
                                } else {
                                    $maxAccept = 1;
                                }
                            @endphp
                            <tr>
                                @if($canEdit)
                                    <input type="hidden" name="letter_detail_id[]" value="{{ $ld->LETTER_DETAIL_ID }}">
                                    <input type="hidden" name="letter_detail_total" value="{{ $totalSent }}">
                                @endif

                                <td class="text-center">{{ $key + 1 }}</td>
                                <td class="text-center">
                                    <center>
                                        @if($canEdit)
                                            <input type="hidden" name="letter_detail_checked[]" class="letter_detail_checked_{{ $strRand }}" value="{{ $ld->CHECKED == 1 ? 1 : 0 }}">
                                            <input type="checkbox" class="form-check-input" onchange="$(this).is(':checked') ? $('.letter_detail_checked_{{ $strRand }}').val(1) : $('.letter_detail_checked_{{ $strRand }}').val(0)" {{ $ld->CHECKED == 1 ? 'checked' : '' }}>
                                        @else
                                            {{ $verifiedBy }}
                                        @endif
                                    </center>
                                </td>
                                <td class="text-center">
                                    <a href="{{ $fileCover }}" data-lightbox="cover-{{ $code }}" data-title="{{ $ld->TITLE }}">
                                        <img src="{{ $fileCover }}" class="img img-fluid img-thumbnail">
                                    </a>
                                </td>
                                <td class="text-wrap">{{ $ld->TITLE }}</td>
                                <td class="text-wrap">{{ $ld->ISBN }}</td>
                                <td class="text-wrap">{{ $ld->NOMORPANGGILJILID }}</td>
                                <td class="text-wrap">{{ $ld->EDISI_SERIAL }}</td>
                                <td>
                                    <input type="number" class="form-control form-control-plaintext" @if($canEdit) name="letter_detail_system[]" @endif value="{{ $totalSystem }}" readonly @if(!$canEdit) disabled @endif>
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-plaintext" @if($canEdit) name="letter_detail_quantity[]" @endif value="{{ $ld->COPY }}" readonly @if(!$canEdit) disabled @endif>
                                </td>
                                <td>
                                    <select class="form-select" @if($canEdit) name="letter_detail_qty_accept[]" @endif onchange="calculateQty(this, 'accept')" @if(!$canEdit) disabled @endif>
                                        @for($i = 0; $i <= $maxAccept; $i++)
                                            <option value="{{ $i }}" {{ ($totalAccept) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </td>
                                <td>
                                    <select class="form-select" @if($canEdit) name="letter_detail_qty_reject[]" @endif onchange="calculateQty(this, 'reject')" @if(!$canEdit) disabled @endif>
                                        @for($i = 0; $i <= $totalSent; $i++)
                                            <option value="{{ $i }}" {{ ($totalReject) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </td>
                                <td>
                                    <select class="form-select" @if($canEdit) name="letter_detail_remark[][]" @endif multiple @if(!$canEdit) disabled @endif>
                                        @php
                                            $problemRejectDefault = 'Kelebihan jumlah eksempelar. Tidak sesuai aturan perundang-undangan.';
                                            $remark = [];

                                            if($ld->REMARK) {
                                                $remark = explode(';', $ld->REMARK ?? '');

                                                if($totalReject > 0) {
                                                    if(!in_array($problemRejectDefault, $remark)) {
                                                        $remark[] = $problemRejectDefault;
                                                    }
                                                }
                                            } else {
                                                if($totalReject > 0) {
                                                    $remark[] = $problemRejectDefault;
                                                }
                                            }
                                        @endphp
                                        @foreach($remark ?? [] as $r)
                                            <option value="{{ $r }}" selected>{{ $r }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control" @if($canEdit) name="letter_detail_note[]" @endif value="{{ $ld->ISBN_STATUS ?? '' }}" placeholder="...................." @if(!$canEdit) disabled @endif>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
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
                    <button type="button" class="btn btn-danger text-nowrap me-2" onclick="submitted('cancel')">
                        <i class="ph-x me-1"></i>
                        Batal Verifikasi
                    </button>
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
    </form>
</div>

<script>
    $(function() {
        select2ServersideTag('select[name="letter_detail_remark[][]"]', 'problem', {}, {
            minimumInputLength: 0
        });

        $('#datatable-client').DataTable({
            paging: false,
            lengthChange: false,
            info: false,
            scrollY: '400px',
            scrollX: false,
            scrollCollapse: true,
        });
    });

    function calculateQty(param, from) {
        var selector = $(param).closest('tr');
        var total = selector.find('input[name="letter_detail_total"]').val();
        var accept = selector.find('select[name="letter_detail_qty_accept[]"]').val();
        var reject = selector.find('select[name="letter_detail_qty_reject[]"]').val();

        var accpetValue = parseInt(total) - parseInt(reject);
        var rejectValue = parseInt(total) - parseInt(accept);

        if(from == 'accept') {
            selector.find('select[name="letter_detail_qty_reject[]"]').val(rejectValue);
        } else if(from == 'reject') {
            selector.find('select[name="letter_detail_qty_accept[]"]').val(accpetValue);
        }
    }

    function submitted(param) {
        $.ajax({
            url: '{{ url("physical-delivery/delivery-verification/detail/" . $letter->LETTER_ID) }}?param=' + param,
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

                            location.href = '{{ url("physical-delivery/delivery-verification") }}';
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
                responseError(response);
            }
        });
    }
</script>
