<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pengiriman Fisik - <span class="fw-normal">Tambah Bukti Penerimaan</span>
            </h4>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="alert alert-danger border-0 alert-dismissible fade d-none" id="validation-element">
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        <div class="d-flex align-items-start">
            <i class="ph-warning-circle ph-2x me-3 mt-1"></i>
            <div class="flex-fill">
                <h6 class="alert-heading fw-semibold mb-2">Terdapat Kesalahan Validasi</h6>
                <p class="mb-2">Mohon periksa dan perbaiki kesalahan berikut:</p>
                <ul class="mb-0" id="validation-data"></ul>
            </div>
        </div>
    </div>
    <form id="form-data">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ph-info me-1"></i>
                    Informasi Umum Pengiriman
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">
                            Jasa Kirim
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ph-truck"></i></span>
                            <select class="form-select select2-basic" name="delivery_service_id" id="delivery_service_id" onchange="receiptable()" data-width="1%">
                                <option value="">-- Pilih Jasa Kirim --</option>
                                @foreach($deliveryService as $ds)
                                    <option value="{{ $ds->ID }}">{{ $ds->NAME }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">Nomor Resi</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ph-barcode"></i></span>
                            <input type="text" class="form-control" name="receipt" id="receipt" placeholder="Masukkan nomor resi">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">
                            Tanggal Terima
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ph-calendar"></i></span>
                            <input type="text" class="form-control date-single" name="accept_date" id="accept_date" value="{{ date('Y/m/d') }}" placeholder="Pilih Tanggal" readonly>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">
                            Nomor Telepon
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ph-phone"></i></span>
                            <input type="text" class="form-control" name="phone" id="phone" placeholder="081234567890">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">
                            Pelaksana Serah
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ph-user-circle"></i></span>
                            <select class="form-select" name="executor_id" id="executor_id" data-width="1%"></select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">
                            Tujuan
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ph-map-pin"></i></span>
                            <select class="form-select" name="branch_id" id="branch_id" data-width="1%">
                                <option value="{{ session('branch_id') }}" selected>{{ session('branch_name') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">
                            Nama Pengirim
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ph-user"></i></span>
                            <input type="text" class="form-control" name="sender_name" id="sender_name" placeholder="Nama lengkap pengirim">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">Nomor Surat Pengantar</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ph-file-text"></i></span>
                            <input type="text" class="form-control" name="cover_letter_number" id="cover_letter_number" placeholder="001/SP/2025">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header d-sm-flex align-items-sm-center py-3">
                <h5 class="mb-sm-0">
                    <i class="ph-barcode me-1"></i>
                    Koleksi ISBN
                </h5>
                <div class="ms-sm-auto my-sm-auto">
                    <div class="input-group">
                        <span class="input-group-text"><i class="ph-magnifying-glass"></i></span>
                        <input type="text" class="form-control" name="search_isbn" id="search_isbn" placeholder="Masukkan Nomor ISBN" onkeypress="if(event.keyCode == 13) { searchISBN(); return false; }">
                        <button type="button" class="btn btn-primary" onclick="searchISBN()">
                            Cari
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info border-0" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="ph-lightbulb ph-2x me-3"></i>
                        <div>
                            <h6 class="alert-heading fw-semibold mb-1">Cara Menambahkan Koleksi ISBN</h6>
                            <ol class="mb-0 ps-3">
                                <li>Pastikan <strong>Pelaksana Serah</strong> sudah dipilih</li>
                                <li>Masukkan nomor ISBN pada kolom pencarian</li>
                                <li>Klik tombol <strong>"Cari"</strong> atau tekan Enter</li>
                                <li>Data akan otomatis ditambahkan ke tabel</li>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center text-nowrap" rowspan="2" width="80">Cover</th>
                                <th class="text-nowrap" rowspan="2" width="150">ISBN</th>
                                <th class="text-nowrap" rowspan="2">Judul</th>
                                <th class="text-nowrap" rowspan="2" width="100">Edisi</th>
                                <th class="text-nowrap" rowspan="2" width="100">Jilid</th>
                                <th class="text-center text-nowrap" colspan="2">Total Eks</th>
                                <th class="text-center text-nowrap" colspan="2">Jumlah Eks</th>
                                <th class="text-nowrap" rowspan="2" width="200">Keterangan</th>
                                <th class="text-nowrap" rowspan="2" width="120">QRCBN</th>
                                <th class="text-nowrap" rowspan="2" width="120">ISBD</th>
                                <th class="text-center text-nowrap" rowspan="2" width="80">Aksi</th>
                            </tr>
                            <tr>
                                <th class="text-center" width="80">Sistem</th>
                                <th class="text-center" width="80">Dikirim</th>
                                <th class="text-center" width="100">Diterima</th>
                                <th class="text-center" width="100">Ditolak</th>
                            </tr>
                        </thead>
                        <tbody id="data-collection-isbn">
                            <tr id="empty-isbn-row">
                                <td colspan="13" class="text-center text-muted py-5">
                                    <i class="ph-book-open ph-4x d-block mb-3 opacity-50"></i>
                                    <h6 class="fw-semibold">Belum Ada Data Koleksi ISBN</h6>
                                    <p class="mb-0">Gunakan fitur pencarian di atas untuk menambahkan koleksi</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ph-book-open me-1"></i>
                    Koleksi Non ISBN
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning border-0" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="ph-lightbulb ph-2x me-3"></i>
                        <div>
                            <h6 class="alert-heading fw-semibold mb-1">Informasi Koleksi Non ISBN</h6>
                            <p class="mb-0">Bagian ini untuk koleksi yang tidak memiliki ISBN. Klik tombol <strong>"Tambah"</strong> di bawah untuk menambahkan data koleksi secara manual.</p>
                        </div>
                    </div>
                </div>
                <div class="table-responsive" id="non-isbn-container">
                    <table class="table">
                        <tbody id="data-collection-non-isbn">
                            <tr id="empty-non-isbn-row">
                                <td class="text-center text-muted py-5">
                                    <i class="ph-books ph-4x d-block mb-3 opacity-50"></i>
                                    <h6 class="fw-semibold">Belum Ada Data Koleksi Non ISBN</h6>
                                    <p class="mb-0">Klik tombol "Tambah" di bawah untuk menambahkan koleksi</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-md-3 mb-2 mb-md-0">
                        <div class="input-group">
                            <button type="button" class="btn btn-success" onclick="addCollectionNonISBN()">
                                <i class="ph-plus-circle me-1"></i>
                                Tambah
                            </button>
                            <input type="number" class="form-control text-center" id="add-number-collection-non-isbn" min="1" max="10" value="1">
                            <span class="input-group-text">Baris</span>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <small class="text-muted">
                            <i class="ph-info me-1"></i>
                            Maksimal menambahkan 10 baris sekaligus
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="ph-newspaper me-1"></i>
                    Koleksi Terbitan Berkala
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info border-0" role="alert">
                    <div class="d-flex align-items-start">
                        <i class="ph-lightbulb ph-2x me-3"></i>
                        <div>
                            <h6 class="alert-heading fw-semibold mb-1">Informasi Terbitan Berkala</h6>
                            <p class="mb-0">Bagian ini untuk terbitan berkala seperti majalah dan jurnal. Pilih katalog dari database atau masukkan judul manual. Setiap terbitan dapat memiliki beberapa edisi.</p>
                        </div>
                    </div>
                </div>
                <div class="table-responsive" id="periodicals-container">
                    <table class="table">
                        <tbody id="data-collection-periodicals">
                            <tr id="empty-periodicals-row">
                                <td class="text-center text-muted py-5">
                                    <i class="ph-newspaper-clipping ph-4x d-block mb-3 opacity-50"></i>
                                    <h6 class="fw-semibold">Belum Ada Data Terbitan Berkala</h6>
                                    <p class="mb-0">Klik tombol "Tambah" di bawah untuk menambahkan terbitan berkala</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-md-3 mb-2 mb-md-0">
                        <div class="input-group">
                            <button type="button" class="btn btn-info" onclick="addPeriodicals()">
                                <i class="ph-plus-circle me-1"></i>
                                Tambah
                            </button>
                            <input type="number" class="form-control text-center" id="add-number-collection-periodicals" min="1" max="10" value="1">
                            <span class="input-group-text">Baris</span>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <small class="text-muted">
                            <i class="ph-info me-1"></i>
                            Maksimal menambahkan 10 baris sekaligus
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="card shadow-sm border-0">
        <div class="card-body py-3">
            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                <div class="text-muted">
                    <i class="ph-info me-1"></i>
                    Pastikan semua data sudah benar sebelum menyimpan
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-info" onclick="submitted('save-send-email')">
                        <i class="ph-envelope-open me-1"></i>
                        Simpan & Kirim Email
                    </button>
                    <button type="button" class="btn btn-success" onclick="submitted('save-print')">
                        <i class="ph-printer me-1"></i>
                        Simpan & Cetak
                    </button>
                    <button type="button" class="btn btn-primary" onclick="submitted('save')">
                        <i class="ph-floppy-disk me-1"></i>
                        Simpan Data
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .cursor-pointer {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    #data-collection-isbn tr:not(#empty-isbn-row):hover,
    #data-collection-non-isbn tr:not(#empty-non-isbn-row):hover,
    #data-collection-periodicals tr:not(#empty-periodicals-row):hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .animate__animated {
        animation-duration: 0.5s;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate__fadeIn {
        animation-name: fadeIn;
    }
</style>

<script>
    let collectionNonISBNCounter = 0;
    let periodicalsCounter = 0;

    $(function() {
        datePickerSingle('.date-single');

        if(parseInt('{{ Main::isNotSuperAdmin() }}') === 1) {
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

        $('#search_isbn').on('keypress', function(e) {
            if(e.which === 13) {
                e.preventDefault();

                searchISBN();
            }
        });
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
            $('#receipt').attr('placeholder', 'Masukkan nomor resi');
        }
    }

    function searchISBN() {
        var executorId = $('#executor_id').val();

        if(!executorId) {
            swalInit.fire({
                title: 'Peringatan',
                text: 'Silakan pilih Pelaksana Serah terlebih dahulu',
                icon: 'warning',
            });

            return;
        }

        var searchValue = $('#search_isbn').val();

        if(!searchValue) {
            swalInit.fire({
                title: 'Peringatan',
                text: 'Silakan masukkan nomor ISBN',
                icon: 'warning',
            });

            return;
        }

        $.ajax({
            url: '{{ url("physical-delivery/create-receipt/search-isbn") }}',
            type: 'GET',
            dataType: 'JSON',
            data: {
                code: searchValue,
                accept_default: '{{ $acceptDefault }}',
            },
            beforeSend: function() {
                onLoading('show', 'body');
            },
            success: function(response) {
                onLoading('close', 'body');

                var randStr = randomString(10);

                if (response.data && typeof response.data === 'object' && Object.keys(response.data).length > 0) {
                    if((response.data?.jenis_media ?? '').toLowerCase() == 'cetak') {
                        if(response.data?.penerbit_id == executorId) {
                            if($('#data-collection-isbn tr td[colspan]').length > 0) {
                                $('#empty-isbn-row').remove();
                            }

                            $('#data-collection-isbn').append(`
                                <tr class="animate__animated animate__fadeIn">
                                    <input type="hidden" name="ci[]" value="1">
                                    <input type="hidden" name="ci_code[]" value="${ response.data.isbn }">

                                    <td class="text-center align-middle">${ response.fileCover }</td>
                                    <td class="align-middle">
                                        <span class="badge bg-secondary">${ response.data.isbn }</span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="fw-semibold">${ response.data.title }</div>
                                    </td>
                                    <td class="align-middle">${ response.data.edisi ?? '-' }</td>
                                    <td class="align-middle">${ response.data.keterangan ?? '-' }</td>
                                    <td class="text-center align-middle">
                                        <span class="badge bg-primary">${ response.totalSystem }</span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input type="number" class="form-control form-control-sm text-center ci-total-${ randStr }" readonly>
                                    </td>
                                    <td class="align-middle">
                                        <select class="form-select form-select-sm ci-accept-${ randStr }" name="ci_qty_accept[]" onchange="calculateQtyTotal('.ci-total-${ randStr }', '.ci-accept-${ randStr }', '.ci-reject-${ randStr }', '.ci-description-${ randStr }')">
                                            ${ response.optionAccept }
                                        </select>
                                    </td>
                                    <td class="align-middle">
                                        <input type="number" class="form-control form-control-sm ci-reject-${ randStr }" name="ci_qty_reject[]" value="${ response.totalReject }" oninput="calculateQtyTotal('.ci-total-${ randStr }', '.ci-accept-${ randStr }', '.ci-reject-${ randStr }', '.ci-description-${ randStr }')">
                                    </td>
                                    <td class="align-middle">
                                        <select class="form-select form-select-sm ci-description-${ randStr }" name="ci_description[]" multiple></select>
                                    </td>
                                    <td class="align-middle">
                                        <input type="text" class="form-control form-control-sm" name="ci_qrcbn[]" placeholder="QRCBN">
                                    </td>
                                    <td class="align-middle">
                                        <input type="text" class="form-control form-control-sm" name="ci_isbd[]" placeholder="ISBD">
                                    </td>
                                    <td class="text-center align-middle">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)" data-bs-toggle="tooltip" title="Hapus item">
                                            <i class="ph-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `);

                            calculateQtyTotal(`.ci-total-${ randStr }`, `.ci-accept-${ randStr }`, `.ci-reject-${ randStr }`, `.ci-description-${ randStr }`);

                            $('#search_isbn').val('').focus();

                            if(response.data.is_kdt_valid == 1) {
                                swalInit.fire({
                                    title: 'Berhasil!',
                                    html: 'ISBN telah tervalidasi dengan KDT.<br>Katalog ID: <strong>' + response.data.catalog_id + '</strong>',
                                    icon: 'success',
                                });
                            } else {
                                notification('success', '<i class="ph-check-circle me-2"></i> ISBN berhasil ditambahkan');
                            }

                            select2ServersideTag('select[name="ci_description[]"]', 'problem', {}, {
                                minimumInputLength: 0
                            });

                            if(response.totalReject > 0) {
                                $('.ci-description-' + randStr).html(`
                                    <option value="Kelebihan jumlah eksempelar. Tidak sesuai aturan perundang-undangan." selected>Kelebihan jumlah eksempelar. Tidak sesuai aturan perundang-undangan.</option>
                                `);
                            }

                            $('[data-bs-toggle="tooltip"]').tooltip();
                        } else {
                            swalInit.fire({
                                title: 'Oops...',
                                text: 'Mohon pilih pelaksana serah atas nama ' + response.data?.nama_penerbit,
                                icon: 'warning',
                            });
                        }
                    } else {
                        swalInit.fire({
                            title: 'Oops...',
                            text: 'ISBN bukan merupakan ISBN cetak. Silakan unggah karya digital pada web SAKEDAP',
                            icon: 'warning',
                        });
                    }
                } else {
                    swalInit.fire({
                        title: 'Tidak Ditemukan',
                        text: 'Data ISBN tidak ditemukan di sistem',
                        icon: 'info',
                    });
                }
            },
            error: function(response) {
                onLoading('close', 'body');
                responseError(response);
            }
        });
    }

    function removeItem(param) {
        swalInit.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus item ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
        }).then((result) => {
            if (result.isConfirmed) {
                var $tbody = $(param).closest('tbody');
                var tbodyId = $tbody.attr('id');

                $(param).closest('tr').fadeOut(300, function() {
                    $(this).remove();

                    if($tbody.find('tr').length === 0) {
                        var emptyMessage = '';

                        if(tbodyId === 'data-collection-isbn') {
                            emptyMessage = '<tr id="empty-isbn-row"><td colspan="13" class="text-center text-muted py-5"><i class="ph-book-open ph-4x d-block mb-3 opacity-50"></i><h6 class="fw-semibold">Belum Ada Data Koleksi ISBN</h6><p class="mb-0">Gunakan fitur pencarian di atas untuk menambahkan koleksi</p></td></tr>';
                        } else if(tbodyId === 'data-collection-non-isbn') {
                            emptyMessage = '<tr id="empty-non-isbn-row"><td class="text-center text-muted py-5"><i class="ph-books ph-4x d-block mb-3 opacity-50"></i><h6 class="fw-semibold">Belum Ada Data Koleksi Non ISBN</h6><p class="mb-0">Klik tombol "Tambah" di bawah untuk menambahkan koleksi</p></td></tr>';
                        } else if(tbodyId === 'data-collection-periodicals') {
                            emptyMessage = '<tr id="empty-periodicals-row"><td class="text-center text-muted py-5"><i class="ph-newspaper-clipping ph-4x d-block mb-3 opacity-50"></i><h6 class="fw-semibold">Belum Ada Data Terbitan Berkala</h6><p class="mb-0">Klik tombol "Tambah" di bawah untuk menambahkan terbitan berkala</p></td></tr>';
                        }

                        $tbody.html(emptyMessage);
                    }
                });

                notification('success', '<i class="ph-check-circle me-2"></i> Item berhasil dihapus');
            }
        });
    }

    function addCollectionNonISBN() {
        var total = parseInt($('#add-number-collection-non-isbn').val());

        if(total < 1 || isNaN(total)) {
            swalInit.fire({
                title: 'Peringatan',
                text: 'Jumlah baris minimal 1',
                icon: 'warning',
            });

            return;
        }

        if(total > 10) {
            swalInit.fire({
                title: 'Peringatan',
                text: 'Maksimal 10 baris per sekali tambah',
                icon: 'warning',
            });

            return;
        }

        if($('#data-collection-non-isbn tr td[colspan]').length > 0 || $('#data-collection-non-isbn tr td').length === 1) {
            $('#empty-non-isbn-row').remove();
        }

        for(var i = 1; i <= total; i++) {
            collectionNonISBNCounter++;

            var currentNumber = collectionNonISBNCounter;
            var randStr = randomString(10);

            $('#data-collection-non-isbn').append(`
                <tr class="animate__animated animate__fadeIn">
                    <input type="hidden" name="cni[]" value="1">
                    <td width="5%" class="align-top pt-3">
                        <button type="button" class="btn btn-danger" onclick="removeItem(this)" data-bs-toggle="tooltip" title="Hapus koleksi">
                            <i class="ph-trash"></i>
                        </button>
                    </td>
                    <td width="95%">
                        <div class="card border shadow-sm mb-2">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                <h6 class="mb-0">
                                    <i class="ph-book-open me-2"></i>
                                    Koleksi #${currentNumber}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-lg-6">
                                        <label class="form-label">
                                            <i class="ph-hash me-1"></i>ID Katalog
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ph-database"></i></span>
                                            <input type="text" class="form-control cni_catalog_id_${ randStr }" name="cni_catalog_id[]" placeholder="Klik untuk pilih katalog" onchange="selectCollectionNonISBN(this)" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">
                                            <i class="ph-buildings me-1"></i>Pelaksana Serah
                                        </label>
                                        <input type="text" class="form-control" name="cni_executor[]" placeholder="Nama pelaksana">
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">
                                            <i class="ph-book-bookmark me-1"></i>Judul
                                        </label>
                                        <input type="text" class="form-control" name="cni_title[]" placeholder="Judul koleksi">
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">
                                            <i class="ph-user-circle me-1"></i>Kepengarangan
                                        </label>
                                        <input type="text" class="form-control" name="cni_author[]" placeholder="Nama pengarang/penulis">
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">
                                            <i class="ph-note me-1"></i>Deskripsi Fisik
                                        </label>
                                        <input type="text" class="form-control" name="cni_physical_description[]" placeholder="Contoh: 200 hlm ; 21 cm">
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="form-label">
                                            <i class="ph-calendar-blank me-1"></i>Tahun Terbit
                                        </label>
                                        <input type="text" class="form-control" name="cni_year[]" placeholder="2025">
                                    </div>
                                    <div class="col-lg-3">
                                        <label class="form-label">
                                            <i class="ph-books me-1"></i>No Jilid
                                        </label>
                                        <input type="text" class="form-control" name="cni_binding[]" placeholder="No. jilid">
                                    </div>
                                    <div class="col-lg-12">
                                        <label class="form-label">
                                            <i class="ph-note-pencil me-1"></i>Keterangan
                                        </label>
                                        <select class="form-select cni-description-${ randStr }" name="cni_description[]" multiple></select>
                                    </div>
                                </div>
                                <hr class="my-3">
                                <div class="row g-3">
                                    <div class="col-lg-4">
                                        <label class="form-label">
                                            <i class="ph-stack me-1"></i>Jumlah Eksemplar
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">Total</span>
                                            <input type="number" class="form-control cni-total-${ randStr }" disabled>
                                            <span class="input-group-text">Terima</span>
                                            <select class="form-select cni-accept-${ randStr }" name="cni_qty_accept[]" style="max-width: 100px;" onchange="calculateQtyTotal('.cni-total-${ randStr }', '.cni-accept-${ randStr }', '.cni-reject-${ randStr }', '.cni-description-${ randStr }')">
                                                <option value="0" selected>0</option>
                                                @for($j = 1; $j <= $acceptDefault; $j++)
                                                    <option value="{{ $j }}">{{ $j }}</option>
                                                @endfor
                                            </select>
                                            <span class="input-group-text">Tolak</span>
                                            <input type="number" class="form-control cni-reject-${ randStr }" name="cni_qty_reject[]" value="0" style="max-width: 80px;" oninput="calculateQtyTotal('.cni-total-${ randStr }', '.cni-accept-${ randStr }', '.cni-reject-${ randStr }', '.cni-description-${ randStr }')">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <label class="form-label">
                                            <i class="ph-currency-circle-dollar me-1"></i>Harga
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control" name="cni_price[]" placeholder="0">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <label class="form-label">
                                            <i class="ph-file me-1"></i>Jenis Media
                                        </label>
                                        <select class="form-select select2-basic" name="cni_type[]">
                                            <option value="">-- Pilih Jenis --</option>
                                            @foreach($media as $m)
                                                <option value="{{ $m->NAME }}">{{ $m->NAME }} [{{ $m->DEPOSITFORMAT_CODE }}]</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">QRCBN</label>
                                        <input type="text" class="form-control" name="cni_qrcbn[]" placeholder="Kode QRCBN">
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">ISBD</label>
                                        <input type="text" class="form-control" name="cni_isbd[]" placeholder="Kode ISBD">
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

        $('[data-bs-toggle="tooltip"]').tooltip();

        notification('success', '<i class="ph-check-circle me-2"></i> ' + total + ' koleksi berhasil ditambahkan');

        $('html, body').animate({
            scrollTop: $('#data-collection-non-isbn tr').last().offset().top - 100
        }, 500);
    }

    function selectCollectionNonISBN(param) {
        $.ajax({
            url: '{{ url("physical-delivery/create-receipt/select-catalog") }}',
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
                selector.find('select[name="cni_type[]"]').val(response?.NAME_WORKSHEET).trigger('change');
                selector.find('input[name="cni_price[]"]').val(response?.PRICE);

                notification('success', '<i class="ph-check-circle me-2"></i> Data katalog berhasil dimuat');
            },
            error: function(response) {
                onLoading('close', '#data-collection-non-isbn');
                responseError(response);
            }
        });
    }

    function addPeriodicals() {
        var total = parseInt($('#add-number-collection-periodicals').val());

        if(total < 1 || isNaN(total)) {
            swalInit.fire({
                title: 'Peringatan',
                text: 'Jumlah baris minimal 1',
                icon: 'warning',
            });

            return;
        }

        if(total > 10) {
            swalInit.fire({
                title: 'Peringatan',
                text: 'Maksimal 10 baris per sekali tambah',
                icon: 'warning',
            });

            return;
        }

        if($('#data-collection-periodicals tr td[colspan]').length > 0 || $('#data-collection-periodicals tr td').length === 1) {
            $('#empty-periodicals-row').remove();
        }

        for(var i = 1; i <= total; i++) {
            periodicalsCounter++;

            var currentNumber = periodicalsCounter;
            var randStr = randomString(10);

            $('#data-collection-periodicals').append(`
                <tr class="periodical-row-${ randStr } animate__animated animate__fadeIn">
                    <input type="hidden" name="cp[]" value="1">
                    <td width="5%" rowspan="2" class="align-top pt-3">
                        <button type="button" class="btn btn-danger" onclick="removeItemPeriodicals('${ randStr }')" data-bs-toggle="tooltip" title="Hapus terbitan berkala">
                            <i class="ph-trash"></i>
                        </button>
                    </td>
                    <td width="95%">
                        <div class="card border shadow-sm mb-2">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                <h6 class="mb-0">
                                    <i class="ph-newspaper-clipping me-2"></i>
                                    Terbitan Berkala #${currentNumber}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input use-catalog-${ randStr }" type="checkbox" id="use_catalog_${ randStr }" checked onchange="toggleCatalogInput('${ randStr }')">
                                    <label class="form-check-label" for="use_catalog_${ randStr }">
                                        <i class="ph-database me-1"></i>
                                        Gunakan Katalog dari Database
                                    </label>
                                </div>
                                <div class="catalog-input-${ randStr }">
                                    <input type="hidden" class="cp_catalog_id_${ randStr }" name="cp_catalog_id[]">
                                    <label class="form-label">
                                        Pilih Katalog <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="ph-database"></i></span>
                                        <input type="text" class="form-control cp_catalog_text_${ randStr }" placeholder="Klik untuk mencari katalog terbitan berkala" readonly>
                                    </div>
                                </div>
                                <div class="manual-input-${ randStr }" style="display: none;">
                                    <label class="form-label">
                                        Judul Terbitan Berkala <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control cp_manual_title_${ randStr }" name="cp_manual_title[]" placeholder="Masukkan judul terbitan berkala">
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="periodical-row-${ randStr }">
                    <td>
                        <div class="card border shadow-sm">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                <h6 class="mb-0">
                                    <i class="ph-notebook me-1"></i>
                                    Daftar Edisi
                                </h6>
                                <button type="button" class="btn btn-success btn-sm" onclick="addCollectionPeriodicalsEdition('${ randStr }')">
                                    <i class="ph-plus me-1"></i>
                                    Tambah Edisi
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="data-collection-periodicals-edition-${ randStr }">
                                    <div class="text-center text-muted py-4">
                                        <i class="ph-notebook ph-3x d-block mb-2 opacity-50"></i>
                                        <p class="mb-0">Belum ada edisi. Klik tombol "Tambah Edisi" untuk menambahkan.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            `);

            lookupCatalog(`.cp_catalog_text_${ randStr }`, `.cp_catalog_id_${ randStr }`, false, {
                worksheet_id: [13, 142]
            });
        }

        $('[data-bs-toggle="tooltip"]').tooltip();

        notification('success', '<i class="ph-check-circle me-2"></i> ' + total + ' terbitan berkala berhasil ditambahkan');

        $('html, body').animate({
            scrollTop: $('.periodical-row-' + randStr).offset().top - 100
        }, 500);
    }

    function toggleCatalogInput(randStr) {
        if($('.use-catalog-' + randStr).is(':checked')) {
            $('.catalog-input-' + randStr).slideDown();
            $('.manual-input-' + randStr).slideUp();
            $('.cp_manual_title_' + randStr).val('');
        } else {
            $('.catalog-input-' + randStr).slideUp();
            $('.manual-input-' + randStr).slideDown();
            $('.cp_catalog_id_' + randStr).val('');
            $('.cp_catalog_text_' + randStr).val('');
        }
    }

    function removeItemPeriodicals(randStr) {
        swalInit.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus terbitan berkala ini beserta semua edisinya?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
        }).then((result) => {
            if (result.isConfirmed) {
                $('.periodical-row-' + randStr).fadeOut(300, function() {
                    $(this).remove();

                    if($('#data-collection-periodicals tr').length === 0) {
                        $('#data-collection-periodicals').html('<tr id="empty-periodicals-row"><td class="text-center text-muted py-5"><i class="ph-newspaper-clipping ph-4x d-block mb-3 opacity-50"></i><h6 class="fw-semibold">Belum Ada Data Terbitan Berkala</h6><p class="mb-0">Klik tombol "Tambah" di bawah untuk menambahkan terbitan berkala</p></td></tr>');
                    }
                });

                notification('success', '<i class="ph-check-circle me-2"></i> Terbitan berkala berhasil dihapus');
            }
        });
    }

    function addCollectionPeriodicalsEdition(parentRandStr) {
        var randStr = randomString(10);
        var editionContainer = $('#data-collection-periodicals-edition-' + parentRandStr);

        if(editionContainer.find('.text-center.text-muted').length > 0) {
            editionContainer.empty();
        }

        editionContainer.append(`
            <div class="edition-row-${ randStr } border rounded p-3 mb-3 bg-light animate__animated animate__fadeIn">
                <input type="hidden" name="cpe[][]" value="${ parentRandStr }">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-2">
                        <label class="form-label small">
                            <i class="ph-hash me-1"></i>Edisi Serial
                        </label>
                        <input type="text" class="form-control form-control-sm" name="cpe_edition[][]" placeholder="No. Edisi">
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label small">
                            <i class="ph-calendar me-1"></i>TTES Awal
                        </label>
                        <input type="text" class="form-control form-control-sm date-single" name="cpe_first_ttes[][]" placeholder="Pilih tanggal" readonly>
                    </div>
                    <div class="col-lg-2">
                        <label class="form-label small">
                            <i class="ph-calendar-check me-1"></i>TTES Akhir
                        </label>
                        <input type="text" class="form-control form-control-sm date-single" name="cpe_end_ttes[][]" placeholder="Pilih tanggal" readonly>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label small">
                            <i class="ph-stack me-1"></i>Eksemplar
                        </label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Total</span>
                            <input type="number" class="form-control cpe-total-${ randStr }" disabled>
                            <span class="input-group-text">Terima</span>
                            <select class="form-select cpe-accept-${ randStr }" name="cpe_qty_accept[][]" onchange="calculateQtyTotal('.cpe-total-${ randStr }', '.cpe-accept-${ randStr }', '.cpe-reject-${ randStr }', '.cpe-description-${ randStr }')">
                                <option value="0" selected>0</option>
                                @for($k = 1; $k <= $acceptDefault; $k++)
                                    <option value="{{ $k }}">{{ $k }}</option>
                                @endfor
                            </select>
                            <span class="input-group-text">Tolak</span>
                            <input type="number" class="form-control cpe-reject-${ randStr }" name="cpe_qty_reject[][]" value="0" oninput="calculateQtyTotal('.cpe-total-${ randStr }', '.cpe-accept-${ randStr }', '.cpe-reject-${ randStr }', '.cpe-description-${ randStr }')">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeItemEdition('${ randStr }')">
                            <i class="ph-trash me-1"></i>
                            Hapus
                        </button>
                    </div>
                    <div class="col-12">
                        <label class="form-label small">
                            <i class="ph-note-pencil me-1"></i>Keterangan
                        </label>
                        <select class="form-select form-select-sm cpe-description-${ randStr }" name="cpe_description[][]" multiple></select>
                    </div>
                </div>
            </div>
        `);

        calculateQtyTotal(`.cpe-total-${ randStr }`, `.cpe-accept-${ randStr }`, `.cpe-reject-${ randStr }`, `.cpe-description-${ randStr }`);

        select2ServersideTag('select[name="cpe_description[][]"]', 'problem', {}, {
            minimumInputLength: 0
        });

        datePickerSingle('.date-single');
        notification('success', '<i class="ph-check-circle me-2"></i> Edisi berhasil ditambahkan');
    }

    function removeItemEdition(randStr) {
        swalInit.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus edisi ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
        }).then((result) => {
            if (result.isConfirmed) {
                $('.edition-row-' + randStr).fadeOut(300, function() {
                    $(this).remove();
                });

                notification('success', '<i class="ph-check-circle me-2"></i> Edisi berhasil dihapus');
            }
        });
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
        $('#validation-element').removeClass('show').addClass('d-none');
        $('#validation-data').html('');
    }

    function showValidation(data) {
        $('#validation-element').removeClass('d-none').addClass('show');
        $('#validation-data').html('');

        $.each(data, function(index, value) {
            $('#validation-data').append('<li class="mb-1">' + value + '</li>');
        });

        $('html, body').animate({ scrollTop: 0 }, 'smooth');
    }

    function submitted(param) {
        var confirmText = '';
        var buttonText = '';
        var icon = 'question';

        if(param === 'save-send-email') {
            confirmText = 'Data akan disimpan dan email notifikasi akan dikirim ke pelaksana serah.';
            buttonText = 'Ya, Simpan & Kirim Email';
            icon = 'info';
        } else if(param === 'save-print') {
            confirmText = 'Data akan disimpan dan dokumen akan dicetak.';
            buttonText = 'Ya, Simpan & Cetak';
            icon = 'info';
        } else {
            confirmText = 'Data akan disimpan ke sistem.';
            buttonText = 'Ya, Simpan';
            icon = 'question';
        }

        swalInit.fire({
            title: 'Konfirmasi Penyimpanan',
            html: confirmText + '<br><strong>Pastikan semua data sudah benar.</strong>',
            icon: icon,
            showCancelButton: true,
            confirmButtonText: buttonText,
            cancelButtonText: 'Batal',
            reverseButtons: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
        }).then((result) => {
            if (result.isConfirmed) {
                processSubmit(param);
            }
        });
    }

    function processSubmit(param) {
        $.ajax({
            url: '{{ url("physical-delivery/create-receipt/submitted") }}?param=' + param,
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
                        title: 'Berhasil!',
                        html: response.message,
                        icon: 'success',
                        showDenyButton: false,
                        showCancelButton: false,
                        confirmButtonText: 'OK',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            onLoading('show', 'body');

                            if(response.url) {
                                window.open(response.url, '_blank', 'width=750,height=450');
                            }

                            location.href = '{{ url("physical-delivery/create-receipt") }}';
                        }
                    });
                } else if(response.code == 400) {
                    onLoading('close', 'body');
                    showValidation(response.error);

                    swalInit.fire({
                        title: 'Validasi Gagal',
                        html: 'Terdapat kesalahan pada pengisian form.<br><strong>Silakan periksa kembali data yang Anda masukkan.</strong>',
                        icon: 'warning',
                    });
                } else {
                    swalInit.fire({
                        title: 'Oops...',
                        text: response.message,
                        icon: 'info',
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
