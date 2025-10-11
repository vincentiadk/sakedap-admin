<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pengiriman - <span class="fw-normal">Bukti Penerimaan</span>
            </h4>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="alert alert-danger d-none" id="validation-element">
        <ul class="mb-0" id="validation-data"></ul>
    </div>
    <form id="form-data">
        <div class="card">
            <div class="card-header">
                <h5 class="hstack gap-2 mb-0">Form</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Jasa Kirim : <span class="text-danger fw-bold">*</span></label>
                            <select class="form-select select2-basic" name="delivery_service_id" id="delivery_service_id" onchange="receiptable()">
                                <option value=""></option>
                                @foreach($deliveryService as $ds)
                                    <option value="{{ $ds->ID }}">{{ $ds->NAME }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Resi :</label>
                            <input type="text" class="form-control" name="receipt" id="receipt" placeholder="....................">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Tanggal Terima : <span class="text-danger fw-bold">*</span></label>
                            <input type="text" class="form-control date-single" name="accept_date" id="accept_date" value="{{ date('Y/m/d') }}" placeholder="Pilih Tanggal" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Telepon : <span class="text-danger fw-bold">*</span></label>
                            <input type="text" class="form-control" name="phone" id="phone" placeholder="....................">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Pelaksana Serah : <span class="text-danger fw-bold">*</span></label>
                            <select class="form-select" name="executor_id" id="executor_id"></select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Tujuan : <span class="text-danger fw-bold">*</span></label>
                            <select class="form-select" name="branch_id" id="branch_id">
                                <option value="{{ session('branch_id') }}" selected>{{ session('branch_name') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Nama Pengirim : <span class="text-danger fw-bold">*</span></label>
                            <input type="text" class="form-control" name="sender_name" id="sender_name" placeholder="....................">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-label">Nomor Surat Pengantar :</label>
                            <input type="text" class="form-control" name="cover_letter_number" id="cover_letter_number" placeholder="....................">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header d-sm-flex align-items-sm-center py-sm-0">
                <h5 class="py-sm-3 mb-sm-0">Koleksi ISBN</h5>
                <div class="ms-sm-auto my-sm-auto">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search_isbn" id="search_isbn" placeholder="Nomor ISBN">
                        <button type="button" class="btn btn-primary" onclick="searchISBN()">
                            <i class="ph-magnifying-glass me-1"></i>
                            Cari
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="text-bg-light">
                            <tr>
                                <th class="text-nowrap" rowspan="2">Kode</th>
                                <th class="text-nowrap" rowspan="2">Judul</th>
                                <th class="text-nowrap" rowspan="2">Edisi</th>
                                <th class="text-nowrap" rowspan="2">Jilid</th>
                                <th class="text-nowrap text-center" colspan="2">Total</th>
                                <th class="text-nowrap text-center" colspan="2">Jumlah Eks</th>
                                <th class="text-nowrap" rowspan="2">Keterangan</th>
                                <th class="text-nowrap" rowspan="2">QRCBN</th>
                                <th class="text-nowrap" rowspan="2">ISBD</th>
                                <th class="text-nowrap text-center" rowspan="2">Hapus</th>
                            </tr>
                            <tr>
                                <th class="text-nowrap text-center">Disistem</th>
                                <th class="text-nowrap text-center">Dikirim</th>
                                <th class="text-nowrap text-center">Diterima</th>
                                <th class="text-nowrap text-center">Ditolak</th>
                            </tr>
                        </thead>
                        <tbody id="data-collection-isbn"></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h5 class="hstack gap-2 mb-0">Koleksi Non ISBN</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody id="data-collection-non-isbn"></tbody>
                </table>
                <div class="card-footer bg-white border-top-0">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="input-group">
                                <button type="button" class="btn btn-success" onclick="addCollectionNonISBN()">Tambah</button>
                                <input type="number" class="form-control text-center" id="add-number-collection-non-isbn" min="1" value="1" placeholder="....................">
                                <span class="input-group-text">Baris</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h5 class="hstack gap-2 mb-0">Koleksi Terbitan Berkala</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody id="data-collection-periodicals"></tbody>
                </table>
                <div class="card-footer bg-white border-top-0">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="input-group">
                                <button type="button" class="btn btn-success" onclick="addPeriodicals()">Tambah</button>
                                <input type="number" class="form-control text-center" id="add-number-collection-periodicals" min="1" value="1" placeholder="....................">
                                <span class="input-group-text">Baris</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="card">
        <div class="card-body">
            <div class="text-end">
                <button type="button" class="btn btn-info" onclick="submitted('save-send-email')">
                    <i class="ph-envelope-open me-1"></i>
                    Simpan & Kirim Email
                </button>
                <button type="button" class="btn btn-success" onclick="submitted('save-print')">
                    <i class="ph-printer me-1"></i>
                    Simpan & Cetak
                </button>
                <button type="button" class="btn btn-warning" onclick="submitted('save')">
                    <i class="ph-floppy-disk me-1"></i>
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        datePickerSingle('.date-single');

        if(parseInt('{{ Main::isNotCenterBranch() }}') === 1) {
            select2Serverside('#branch_id', 'branch', {
                province_id: '{{ session("province_id") }}'
            }, {
                minimumInputLength: 0
            });

            select2Serverside('#executor_id', 'executor', {
                province_id: '{{ session("province_id") }}',
            });
        } else {
            select2Serverside('#branch_id', 'branch');
            select2Serverside('#executor_id', 'executor');
        }
    });

    function receiptable() {
        var deliveryServiceId = $('#delivery_service_id').val();

        if(deliveryServiceId == 1) {
            $('#receipt').val('');
            $('#receipt').attr('disabled', true);
            $('#receipt').attr('placeholder', 'Auto Generate');
        } else {
            $('#receipt').val('');
            $('#receipt').attr('disabled', false);
            $('#receipt').attr('placeholder', '....................');
        }
    }

    function searchISBN() {
        $.ajax({
            url: '{{ url("delivery/receipt/search-isbn") }}',
            type: 'GET',
            dataType: 'JSON',
            data: {
                code: $('#search_isbn').val(),
                executor_id: $('#executor_id').val(),
                accept_default: '{{ $acceptDefault }}',
            },
            beforeSend: function() {
                onLoading('show', 'body');
            },
            success: function(response) {
                onLoading('close', 'body');

                var executorId = $('#executor_id').val();
                var randStr = randomString(10);

                if (response.data && typeof response.data === 'object' && Object.keys(response.data).length > 0 ) {
                    if((response.data?.jenis_media ?? '').toLowerCase() == 'cetak') {
                        if(response.data?.penerbit_id == executorId) {
                            $('#data-collection-isbn').append(`
                                <tr>
                                    <input type="hidden" name="ci[]" value="1">
                                    <input type="hidden" name="ci_code[]" value="${ response.data.isbn }">

                                    <td class="text-wrap">${ response.data.isbn }</td>
                                    <td class="text-wrap">${ response.data.title }</td>
                                    <td class="text-wrap">${ response.data.edisi ?? '' }</td>
                                    <td class="text-wrap">${ response.data.keterangan ?? '' }</td>
                                    <td>
                                        <input type="number" class="form-control form-control-plaintext" value="${ response.totalSystem }" readonly>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-plaintext ci-total-${ randStr }" readonly>
                                    </td>
                                    <td>
                                        <select class="form-select w-auto flex-grow-0 ci-accept-${ randStr }" name="ci_qty_accept[]" onchange="calculateQtyTotal('.ci-total-${ randStr }', '.ci-accept-${ randStr }', '.ci-reject-${ randStr }', '.ci-description-${ randStr }')">
                                            ${ response.optionAccept }
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control ci-reject-${ randStr }" name="ci_qty_reject[]" value="${ response.totalReject }" oninput="calculateQtyTotal('.ci-total-${ randStr }', '.ci-accept-${ randStr }', '.ci-reject-${ randStr }', '.ci-description-${ randStr }')">
                                    </td>
                                    <td>
                                        <select class="form-select ci-description-${ randStr }" name="ci_description[]" multiple></select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="ci_qrcbn[]">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="ci_isbd[]">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)">
                                            <i class="ph-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `);

                            calculateQtyTotal(`.ci-total-${ randStr }`, `.ci-accept-${ randStr }`, `.ci-reject-${ randStr }`, `.ci-description-${ randStr }`);

                            $('#search_isbn').val('');

                            if(response.data.is_kdt_valid == 1) {
                                swalInit.fire('Berhasil', 'ISBN telah tervalidasi dengan KDT. koleksi otomatis di kaitkan dengan Katalog ID : ' + response.data.catalog_id, 'info');
                            } else {
                                swalInit.fire('Berhasil', 'ISBN ditemukan', 'success');
                            }

                            select2ServersideTag('select[name="ci_description[]"]', 'problem', {}, {
                                minimumInputLength: 0
                            });

                            if(response.totalReject > 0) {
                                $('.ci-description-' + randStr ).html(`
                                    <option value="Kelebihan jumlah eksempelar. Tidak sesuai aturan perundang-undangan." selected>Kelebihan jumlah eksempelar. Tidak sesuai aturan perundang-undangan.</option>
                                `);
                            }
                        } else {
                            swalInit.fire('Oops ...', 'Mohon pilih pelaksana serah atas nama ' + response.data?.nama_penerbit, 'warning');
                        }
                    } else {
                        swalInit.fire('Oops ...', 'ISBN bukan merupakan ISBN cetak, silahkan silahkan unggah karya digital pada web E-Deposit', 'warning');
                    }
                } else {
                    swalInit.fire('Oops ...', 'Data isbn tidak ditemukan', 'info');
                }
            },
            error: function(response) {
                onLoading('close', 'body');
                responseError(response);
            }
        });
    }

    function removeItem(param) {
        $(param).closest('tr').remove();
    }

    function addCollectionNonISBN() {
        var total = $('#add-number-collection-non-isbn').val();

        for(var i = 1; i <= total; i++) {
            var randStr = randomString(10);

            $('#data-collection-non-isbn').append(`
                <tr>
                    <input type="hidden" name="cni[]" value="1">
                    <td width="5%">
                        <button type="button" class="btn btn-danger" onclick="removeItem(this)">
                            <i class="ph-trash"></i>
                        </button>
                    </td>
                    <td width="95%">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mb-3">
                                    <label class="col-form-label col-lg-3">ID Catalog</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control cni_catalog_id_${ randStr }" name="cni_catalog_id[]" placeholder="Pilih Katalog" onchange="selectCollectionNonISBN(this)" readonly>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-form-label col-lg-3">Pelaksana Serah</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" name="cni_executor[]" placeholder="Kosongi jika pelaksana serah sama dengan form">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-form-label col-lg-3">Judul</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" name="cni_title[]">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-form-label col-lg-3">Kepengarangan</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" name="cni_author[]">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-form-label col-lg-3">Deskripsi Fisik</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" name="cni_physical_description[]">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-form-label col-lg-3">Tahun Terbit</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" name="cni_year[]">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row mb-3">
                                    <label class="col-form-label col-lg-3">Jenis</label>
                                    <div class="col-lg-9">
                                        <select class="form-select select2-basic" name="cni_type[]">
                                            <option value=""></option>
                                            @foreach($media as $m)
                                                <option value="{{ $m->NAME }}">{{ $m->NAME }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-form-label col-lg-3">Jumlah Eks</label>
                                    <div class="col-lg-9">
                                        <span class="input-group">
                                            <span class="input-group-text">Total</span>
                                            <input type="number" class="form-control cni-total-${ randStr }" disabled>
                                            <span class="input-group-text">Terima</span>
                                            <select class="form-select w-auto flex-grow-0 cni-accept-${ randStr }" name="cni_qty_accept[]" onchange="calculateQtyTotal('.cni-total-${ randStr }', '.cni-accept-${ randStr }', '.cni-reject-${ randStr }', '.cni-description-${ randStr }')">
                                                <option value="0" selected>0</option>
                                                <option value="{{ $acceptDefault }}">{{ $acceptDefault }}</option>
                                            </select>
                                            <span class="input-group-text">Tolak</span>
                                            <input type="number" class="form-control cni-reject-${ randStr }" name="cni_qty_reject[]" value="0" oninput="calculateQtyTotal('.cni-total-${ randStr }', '.cni-accept-${ randStr }', '.cni-reject-${ randStr }', '.cni-description-${ randStr }')">
                                        </span>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-form-label col-lg-3">No Jilid</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" name="cni_binding[]">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-form-label col-lg-3">Harga</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" name="cni_price[]">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label class="col-form-label col-lg-3">Keterangan</label>
                                    <div class="col-lg-9">
                                        <select class="form-select cni-description-${ randStr }" name="cni_description[]" multiple></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            `);

            calculateQtyTotal(`.cni-total-${ randStr }`, `.cni-accept-${ randStr }`, `.cni-reject-${ randStr }`, `.cni-description-${ randStr }`);

            select2ServersideTag('select[name="cni_description[]"]', 'problem', {}, {
                minimumInputLength: 0
            });

            $('input[name="cni_price[]"]').number(true);

            lookupCatalog(`.cni_catalog_id_${ randStr }`, `.cni_catalog_id_${ randStr }`, true);

            select2Basic();
        }
    }

    function selectCollectionNonISBN(param) {
        $.ajax({
            url: '{{ url("delivery/receipt/select-catalog") }}',
            type: 'GET',
            dataType: 'JSON',
            data: {
                id: $(param).val()
            },
            beforeSend: function() {
                onLoading('show', '#data-collection-non-isbn');
            },
            success: function(response) {
                onLoading('close', '#data-collection-non-isbn');

                let selector = $(param).closest('tr');

                selector.find('input[name="cni_title[]"]').val(response?.TITLE);
                selector.find('input[name="cni_author[]"]').val(response?.AUTHOR);
                selector.find('input[name="cni_physical_description[]"]').val(response?.DESCRIPTION);
                selector.find('input[name="cni_year[]"]').val(response?.PUBLISHYEAR);
                selector.find('input[name="cni_type[]"]').val(response?.NAME_WORKSHEET);
                selector.find('input[name="cni_price[]"]').val(response?.PRICE);
            },
            error: function(response) {
                onLoading('close', '#data-collection-non-isbn');
                responseError(response);
            }
        });
    }

    function addPeriodicals() {
        var total = $('#add-number-collection-periodicals').val();

        for(var i = 1; i <= total; i++) {
            var randStr = randomString(10);

            $('#data-collection-periodicals').append(`
                <tr class="${ randStr }">
                    <input type="hidden" name="cp[]" value="1">
                    <td width="5%" rowspan="2">
                        <button type="button" class="btn btn-danger" onclick="removeItemPeriodicals('${ randStr }')">
                            <i class="ph-trash"></i>
                        </button>
                    </td>
                    <td width="95%">
                        <input type="hidden" class="cp_catalog_id_${ randStr }" name="cp_catalog_id[]">
                        <input type="text" class="form-control cp_catalog_text_${ randStr }" placeholder="Pilih Katalog" readonly>
                    </td>
                </tr>
                <tr class="${ randStr }">
                    <td>
                        <div id="data-collection-periodicals-edition-${ randStr }"></div>
                        <button type="button" class="btn btn-teal btn-sm" onclick="addCollectionPeriodicalsEdition('${ randStr }')">Tambah Edisi</button>
                    </td>
                </tr>
            `);

            lookupCatalog(`.cp_catalog_text_${ randStr }`, `.cp_catalog_id_${ randStr }`);
        }
    }

    function removeItemPeriodicals(param) {
        $('.' + param).remove();
    }

    function addCollectionPeriodicalsEdition(param) {
        var randStr = randomString(10);

        $('#data-collection-periodicals-edition-' + param).append(`
            <div class="row mb-2">
                <input type="hidden" name="cpe[][]">
                <div class="col-md-2">
                    <label class="form-label">Edisi Serial :</label>
                    <input type="text" class="form-control form-control-sm" name="cpe_edition[][]">
                </div>
                <div class="col-md-2">
                    <label class="form-label">TTES Awal :</label>
                    <input type="text" class="form-control form-control-sm date-single" name="cpe_first_ttes[][]" readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label">TTES Akhir :</label>
                    <input type="text" class="form-control form-control-sm date-single" name="cpe_end_ttes[][]" readonly>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Eksemplar :</label>
                    <div class="input-group">
                        <span class="input-group-text">Total</span>
                        <input type="number" class="form-control form-control-sm cpe-total-${ randStr }" disabled>
                        <span class="input-group-text">Terima</span>
                        <select class="form-select form-select-sm w-auto flex-grow-0 cpe-accept-${ randStr }" name="cpe_qty_accept[][]" onchange="calculateQtyTotal('.cpe-total-${ randStr }', '.cpe-accept-${ randStr }', '.cpe-reject-${ randStr }', '.cpe-description-${ randStr }')">
                            <option value="0" selected>0</option>
                            <option value="{{ $acceptDefault }}">{{ $acceptDefault }}</option>
                        </select>
                        <span class="input-group-text">Tolak</span>
                        <input type="number" class="form-control form-control-sm cpe-reject-${ randStr }" name="cpe_qty_reject[][]" value="0" oninput="calculateQtyTotal('.cpe-total-${ randStr }', '.cpe-accept-${ randStr }', '.cpe-reject-${ randStr }', '.cpe-description-${ randStr }')">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Keterangan :</label>
                    <select class="form-select cpe-description-${ randStr }" name="cpe_description[][]" multiple></select>
                </div>
                <div class="col-md-12 text-end mt-1">
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeItemEdition(this)">Hapus Edisi</button>
                </div>
            </div>
        `);

        calculateQtyTotal(`.cpe-total-${ randStr }`, `.cpe-accept-${ randStr }`, `.cpe-reject-${ randStr }`, `.cpe-description-${ randStr }`);

        select2ServersideTag('select[name="cpe_description[][]"]', 'problem', {}, {
            minimumInputLength: 0
        });

        datePickerSingle('.date-single');
    }

    function removeItemEdition(param) {
        $(param).closest('.row').remove();
    }

    function calculateQtyTotal(selectorTotal, selectorAccept, selectorReject, selectorDescription) {
        let accept = parseInt($(selectorAccept).val() ?? 0);
        let reject = parseInt($(selectorReject).val() ?? 0);

        if(reject < 0) {
           reject = 0;
           $(selectorReject).val(0);
        } else if(accept < 0) {
            accept = 0;
            $(selectorAccept).val(0);
        }

        let total = parseInt(accept + reject);

        if(reject > 0) {
            $(selectorDescription).prop('disabled', false);
        } else {
            $(selectorDescription).val('').change();
            $(selectorDescription).prop('disabled', true);
        }

        $(selectorTotal).val(total);
    }

    function clearValidation() {
        $('#validation-element').addClass('d-none');
        $('#validation-data').html('');
    }

    function showValidation(data) {
        $('#validation-element').removeClass('d-none');
        $('#validation-data').html('');

        $.each(data, function(index, value) {
            $('#validation-data').append('<li>' + value + '</li>');
        });
    }

    function submitted(param) {
        $.ajax({
            url: '{{ url("delivery/receipt/submitted") }}?param=' + param,
            type: 'POST',
            dataType: 'JSON',
            data: $('#form-data').serialize(),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            beforeSend: function() {
                onLoading('show', 'body');
                clearValidation();
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

                            if(response.url) {
                                window.open(response.url, '_blank', 'width=750,height=450');
                            }

                            location.href = '{{ url("delivery/receipt") }}';
                        }
                    });
                } else if(response.code == 400) {
                    onLoading('close', 'body');
                    $('.btn-to-top button').click();
                    showValidation(response.error);
                } else {
                    swalInit.fire({
                        title: 'Oops ...',
                        text: response.message,
                        icon: 'info',
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
