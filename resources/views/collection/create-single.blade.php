<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Koleksi - <span class="fw-normal">Tambah Tunggal</span>
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
                <h5 class="hstack gap-2 mb-0">Pelaksana Serah <span class="text-danger fw-bold">*</span></h5>
            </div>
            <div class="card-body">
                <select class="form-select" name="executor_id" id="executor_id"></select>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h5 class="hstack gap-2 mb-0">Meta Data</h5>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Jenis Bahan <span class="text-danger fw-bold">*</span></label>
                    <div class="col-md-10">
                        <select class="form-select select2-basic" name="worksheet_id" id="worksheet_id">
                            <option value=""></option>
                            @foreach($worksheet as $w)
                                <option value="{{ $w->ID }}">{{ $w->NAME }} [{{ $w->DEPOSITFORMAT_CODE }}]</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Media <span class="text-danger fw-bold">*</span></label>
                    <div class="col-md-10">
                        <select class="form-select select2-basic" name="collection_media_id" id="collection_media_id">
                            <option value=""></option>
                            @foreach($media as $m)
                                <option value="{{ $m->ID }}">{{ $m->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Judul <span class="text-danger fw-bold">*</span></label>
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="title" id="title" placeholder="....................">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Kode</label>
                    <div class="col-md-10">
                        <div class="input-group">
                            <select class="form-select w-auto flex-grow-0" name="code_type" id="code_type" onchange="codeType()">
                                <option value="">Tidak Ada</option>
                                <option value="1">ISBN</option>
                                <option value="2">ISMN</option>
                                <option value="3">ISRC</option>
                                <option value="4">ISSN</option>
                                <option value="5">ISAN</option>
                            </select>
                            <input type="text" class="form-control" name="code" id="code" placeholder="....................">
                            <button type="button" class="btn btn-success" id="btn-check-isbn" onclick="checkISBNCode()">Cek Kode</button>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Kota <span class="text-danger fw-bold">*</span></label>
                    <div class="col-md-10">
                        <select class="form-select" name="city_id" id="city_id"></select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Seri</label>
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text">
                                <label>
                                    <input type="checkbox" class="form-check-input mt-0 me-1" onchange="$(this).is(':checked') ? $('#series').attr('disabled', true) : $('#series').attr('disabled', false)" checked>
                                    Tidak Ada
                                </label>
                            </span>
                            <input type="text" class="form-control" name="series" id="series" placeholder="...................." disabled>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">DDC</label>
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text">
                                <label>
                                    <input type="checkbox" class="form-check-input mt-0 me-1" onchange="$(this).is(':checked') ? $('#ddc').attr('disabled', true) : $('#ddc').attr('disabled', false)" checked>
                                    Tidak Ada
                                </label>
                            </span>
                            <input type="text" class="form-control" name="ddc" id="ddc" placeholder="...................." disabled>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Kala Terbit</label>
                    <div class="col-md-10">
                        <select class="form-select select2-basic" name="serial" id="serial" data-placeholder="Tidak Ada">
                            <option value=""></option>
                            <option value="1">Harian</option>
                            <option value="2">Mingguan</option>
                            <option value="3">Bulanan</option>
                            <option value="4">3 Bulan Sekali</option>
                            <option value="5">4 Bulan Sekali</option>
                            <option value="6">6 Bulan Sekali</option>
                            <option value="7">Tahunan</option>
                            <option value="8">2 Tahun Sekali</option>
                            <option value="9">3 Tahun Sekali</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Waktu Publish</label>
                    <div class="col-md-10">
                        <input type="month" class="form-control" name="publish_time" id="publish_time">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Tanggal Terima <span class="text-danger fw-bold">*</span></label>
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="received_at" id="received_at" placeholder="Pilih Tanggal" readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Preview</label>
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="preview" id="preview" placeholder="cth : 1-5 / 00:01-00:20">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Akses <span class="text-danger fw-bold">*</span></label>
                    <div class="col-md-10">
                        <select class="form-select select2-basic" name="access" id="access" data-placeholder="Pilih">
                            <option value=""></option>
                            <option value="1">Akses full file berwatermak secara online</option>
                            <option value="2">Akses hanya preview file secara online, namun tetap dapat di dayagunakan di lingkungan perpustakaan nasional RI dengan jaringan internet LAN</option>
                            <option value="3">Akses hanya file preview secara online, dan tidak didayagunakan di lingkungan Perpustakaan Nasional RI selama 5 tahun sejak diserahkan. Setelah 5 tahun, akan didayagunakan oleh Perpustakaan Nasional RI di jaringan internet LAN</option>
                            <option value="4">Akses hanya file preview secara online selamanya dan tidak didayagunakan di mana pun</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Mata Uang</label>
                    <div class="col-md-10">
                        <select class="form-select" name="currency" id="currency">
                            <option value="IDR" selected>IDR</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Harga</label>
                    <div class="col-md-10">
                        <input type="number" class="form-control" name="price" id="price" placeholder="....................">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Jilid</label>
                    <div class="col-md-10">
                        <input type="number" class="form-control" name="binding" id="binding" placeholder="....................">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Jenis Isi</label>
                    <div class="col-md-10">
                        <select class="form-select select2-basic" name="content_type" id="content_type">
                            <option value=""></option>
                            @foreach($contentType as $ct)
                                <option value="{{ $ct->NAME }}">{{ $ct->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Jenis Wadah</label>
                    <div class="col-md-10">
                        <select class="form-select select2-basic" name="container_type" id="container_type">
                            <option value=""></option>
                            @foreach($containerType as $ct)
                                <option value="{{ $ct->NAME }}">{{ $ct->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Jenis Media</label>
                    <div class="col-md-10">
                        <select class="form-select select2-basic" name="media_type" id="media_type">
                            <option value=""></option>
                            @foreach($mediaType as $mt)
                                <option value="{{ $mt->NAME }}">{{ $mt->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Keterangan Fisik</label>
                    <div class="col-md-10">
                        <textarea name="description" class="form-control" id="decription" rows="5" placeholder="...................."></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h5 class="hstack gap-2 mb-0">Kategori</h5>
            </div>
            <div class="card-body">
                <select class="form-select select2-basic" name="category[]" id="category" data-placeholder="Pilih" multiple>
                    <option value=""></option>
                    @foreach($category as $c)
                        <option value="{{ $c->ID }}">{{ $c->NAME }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h5 class="hstack gap-2 mb-0">Kontributor</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <tbody id="data-contributor"></tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">
                <div class="row">
                    <div class="col-md-2">
                        <div class="input-group">
                            <button type="button" class="btn btn-success" onclick="addContributor()">Tambah</button>
                            <input type="number" class="form-control text-center" id="add-number-contributor" min="1" value="1" placeholder="....................">
                            <span class="input-group-text">Baris</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="hstack gap-2 mb-0">Terbitan</h5>
                <span class="ms-auto">
                    <label>
                        <input type="checkbox" class="form-check-input mt-0 me-1" name="has_edition" onchange="$(this).is(':checked') ? $('#content-edition-copy').fadeIn(500) : $('#content-edition-copy').hide()">
                        Centang jika ada
                    </label>
                </span>
            </div>
            <div id="content-edition-copy" style="display:none;">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Edisi / Volume</th>
                                    <th>Tgl Terbit</th>
                                    <th>Cover</th>
                                    <th>Konten</th>
                                    <th>Hapus</th>
                                </tr>
                            </thead>
                            <tbody id="data-edition"></tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="input-group">
                                <button type="button" class="btn btn-success" onclick="addEdition()">Tambah</button>
                                <input type="number" class="form-control text-center" id="add-number-edition" min="1" value="1" placeholder="....................">
                                <span class="input-group-text">Baris</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="hstack gap-2 mb-0">File Cover <span class="text-danger fw-bold">*</span></h5>
                    </div>
                    <div class="card-body">
                        <input type="file" name="file_cover" id="file_cover">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="hstack gap-2 mb-0">File Konten <span class="text-danger fw-bold">*</span></h5>
                    </div>
                    <div class="card-body">
                        <input type="file" name="file_content" id="file_content">
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="card">
        <div class="card-body">
            <div class="text-end">
                <button type="button" class="btn btn-primary" onclick="submitted()">
                    <i class="ph-plus me-1"></i>
                    Tambah Data
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        datePickerSingle('#received_at');

        if(parseInt('{{ Main::isNotCenterBranch() }}') === 1) {
            select2Serverside('#branch_id', 'branch', {
                province_id: '{{ session("province_id") }}'
            }, {
                minimumInputLength: 0
            });

            select2Serverside('#city_id', 'location', {
                for: 'city',
                province_id: '{{ session("province_id") }}',
            }, {
                minimumInputLength: 0
            });

            select2Serverside('#executor_id', 'executor', {
                province_id: '{{ session("province_id") }}',
            });
        } else {
            select2Serverside('#branch_id', 'branch');
            select2Serverside('#executor_id', 'executor');

            select2Serverside('#city_id', 'city', {
                for: 'city'
            });
        }

        select2Serverside('#currency', 'currency');

        dragAndDropFile('#file_cover', {
            maxFileCount: 1,
            autoReplace: true,
            allowedFileExtensions: ['jpg', 'jpeg', 'png']
        });

        dragAndDropFile('#file_content', {
            maxFileCount: 1,
            autoReplace: true,
            allowedFileExtensions: ['pdf',, 'epub', 'mp3', 'mp4', 'wav']
        });

        codeType();
    });

    function checkISBNCode() {
        $.ajax({
            url: '{{ url("collection/create-single/check-isbn-code") }}',
            type: 'GET',
            dataType: 'JSON',
            data: {
                code: $('#code').val()
            },
            beforeSend: function() {
                onLoading('show', 'body');
            },
            success: function(response) {
                onLoading('close', 'body');

                if(response.code == 200) {
                    swalInit.fire({
                        title: 'Data ditemukan',
                        icon: 'success',
                        showDenyButton: true,
                        confirmButtonText: 'Otomatis Isi Judul & Pelaksana Serah',
                        denyButtonText: 'Hanya Cek Kode'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#title').val(response.data.title);
                            $('#executor_id').html(`<option value="` + response.data.penerbit_id + `">` + response.data.nama_penerbit + `</option>`);
                        }
                    });
                } else {
                    swalInit.fire('Oops', 'Data tidak ditemukan', 'error');
                }
            },
            error: function(response) {
                onLoading('close', 'body');

                swalInit.fire({
                    html: '<b>' + response.responseJSON.exception + '</b><br>' + response.responseJSON.message,
                    icon: 'error',
                    showCloseButton: false
                });
            }
        });
    }

    function addContributor() {
        var total = $('#add-number-contributor').val();

        for(var i = 1; i <= total; i++) {
            $('#data-contributor').append(`
                <tr>
                    <input type="hidden" name="cc_contributor[]" value="1">
                    <td>
                        <select class="form-select select2-basic" name="cc_contributor_id[]">
                            @foreach($contributor as $key => $c)
                                <option value="{{ $c->ID }}" {{ $key == 0 ? 'selected' : '' }}>{{ $c->NAME }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="cc_contributor_name[]" placeholder="Nama">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger col-12" onclick="removeRow(this)"><i class="ph-trash"></i></button>
                    </td>
                </tr>
            `);

            $('select[name="cc_contributor_id[]"]').select2({
                placeholder: 'Pilih'
            });
        }
    }

    function addEdition() {
        var total = $('#add-number-edition').val();

        for(var i = 1; i <= total; i++) {
            $('#data-edition').append(`
                <tr>
                    <input type="hidden" name="cc_edition[]" value="1">
                    <td>
                        <input type="text" class="form-control" name="cc_edition_title[]" placeholder="....................">
                    </td>
                    <td>
                        <input type="text" class="form-control" name="cc_edition_date[]" placeholder="Pilih Tanggal">
                    </td>
                    <td>
                        <input type="file" class="form-control" name="cc_edition_cover[]">
                    </td>
                    <td>
                        <input type="file" class="form-control" name="cc_edition_content[]">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger col-12" onclick="removeRow(this)"><i class="ph-trash"></i></button>
                    </td>
                </tr>
            `);

            datePickerSingle('input[name="cc_edition_date[]"]');
        }
    }

    function removeRow(param) {
        $(param).closest('tr').remove();
    }

    function codeType() {
        var codeType = $('#code_type').val();

        $('#code').val('');
        $('#btn-check-isbn').hide();
        $('#code').attr('disabled', false);

        if(codeType == 1) {
            $('#btn-check-isbn').show();
        } else if(codeType == '') {
            $('#code').attr('disabled', true);
        }
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

    function submitted() {
        $.ajax({
            url: '{{ url("collection/create-single/submitted") }}',
            type: 'POST',
            dataType: 'JSON',
            data: new FormData($('#form-data')[0]),
            contentType: false,
            processData: false,
            cache: false,
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

                            location.href = '{{ url("collection/create-single") }}';
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

                swalInit.fire({
                    html: '<b>' + response.responseJSON.exception + '</b><br>' + response.responseJSON.message,
                    icon: 'error',
                    showCloseButton: true
                });
            }
        });
    }
</script>
