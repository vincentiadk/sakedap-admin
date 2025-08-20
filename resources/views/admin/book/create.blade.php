<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <h3 class="content-header-title mb-1 d-inline-block">Tambah Data Buku</h3><br>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Buku</a></li>
                            <li class="breadcrumb-item active">Tambah Data</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-header-right col-md-6 col-12 mb-2 mt-1">
                <div class="float-md-right">
                    <a href="{{ url('admin/collection/bulk_upload') }}" class="btn btn-success">Bulk Upload</a>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="configuration">
                @if(session('success'))
                    <div class="alert bg-success alert-icon-left alert-dismissible mb-2" role="alert">
                        <span class="alert-icon"><i class="la la-check"></i></span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>Success!</strong> {{ session('success') }}
                    </div>
                @elseif(session('failed'))
                    <div class="alert bg-danger alert-icon-left alert-dismissible mb-2" role="alert">
                        <span class="alert-icon"><i class="la la-times"></i></span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>Success!</strong> {{ session('failed') }}
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-danger" id="validasi_element" style="display:none;">
                            <ul id="validasi_content"></ul>
                        </div>
                        <div class="card">
                            <div class="card-content collapse show">
                                <div class="card-body card-dashboard">
                                    <form id="form_data" class="form">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <select name="form_type" id="form_type" class="form-control" onchange="formType()">
                                                        <option value="">-- Pilih Form --</option>
                                                        <option value="isbn">ISBN</option>
                                                        <option value="non">Non ISBN</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12" id="form_check_isbn" style="display:none;">
                                                <div class="row">
                                                    <div class="col-md-10">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" name="code" id="code" placeholder="Masukan kode ISBN (xxx-xxx-xxxx-xx-x)">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-warning col-12" id="btn_check_code_isbn" onclick="checkCodeIsbn()">Cari</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="form_success_check_isbn" style="display:none;">
                                            <div class="form-group"><hr></div>
                                            <div class="form-group">
                                                <div class="alert alert-danger" id="validasi_element" style="display:none;">
                                                    <ul id="validasi_content"></ul>
                                                </div>
                                            </div>
                                            <h4 class="form-section">Meta Data</h4>
                                            <p>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Penerbit :</label>
                                                    <div class="col-md-10">
                                                        <select name="publisher_id" id="publisher_id" class="form-control" style="width:100%;"></select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Judul :</label>
                                                    <div class="col-md-10">
                                                        <textarea name="title" id="title" class="form-control" placeholder="Masukan judul"></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Seri :</label>
                                                    <div class="col-md-10">
                                                        <input type="text" class="form-control" name="series" id="series" placeholder="Masukan seri">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Edisi :</label>
                                                    <div class="col-md-10">
                                                        <input type="text" class="form-control" name="edition" id="edition" placeholder="Masukan edisi">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Preview :</label>
                                                    <div class="col-md-10">
                                                        <input type="text" name="preview" id="preview" class="form-control" placeholder="Ex: 1-2">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Bulan Terbit :</label>
                                                    <div class="col-md-10">
                                                        <select name="publication_month" id="publication_month" class="form-control">
                                                            <option value="">-- Pilih --</option>
                                                            <option value="01">{{ App\Helper\GeneralHelper::getMonth('01') }}</option>
                                                            <option value="02">{{ App\Helper\GeneralHelper::getMonth('02') }}</option>
                                                            <option value="03">{{ App\Helper\GeneralHelper::getMonth('03') }}</option>
                                                            <option value="04">{{ App\Helper\GeneralHelper::getMonth('04') }}</option>
                                                            <option value="05">{{ App\Helper\GeneralHelper::getMonth('05') }}</option>
                                                            <option value="06">{{ App\Helper\GeneralHelper::getMonth('06') }}</option>
                                                            <option value="07">{{ App\Helper\GeneralHelper::getMonth('07') }}</option>
                                                            <option value="08">{{ App\Helper\GeneralHelper::getMonth('08') }}</option>
                                                            <option value="09">{{ App\Helper\GeneralHelper::getMonth('09') }}</option>
                                                            <option value="10">{{ App\Helper\GeneralHelper::getMonth('10') }}</option>
                                                            <option value="11">{{ App\Helper\GeneralHelper::getMonth('11') }}</option>
                                                            <option value="12">{{ App\Helper\GeneralHelper::getMonth('12') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Tahun Terbit :</label>
                                                    <div class="col-md-10">
                                                        <input type="number" name="publication_year" id="publication_year" class="form-control" placeholder="Masukan tahun terbit">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Tanggal Terima :</label>
                                                    <div class="col-md-10">
                                                        <input type="date" name="received_at" id="received_at" class="form-control" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Total Halaman :</label>
                                                    <div class="col-md-10">
                                                        <div class="input-group mb-2">
                                                            <input type="number" name="total_page" id="total_page" class="form-control" placeholder="Masukan total halaman">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text">Halaman</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Dimensi :</label>
                                                    <div class="col-md-10">
                                                        <div class="input-group mb-2">
                                                            <input type="number" name="dimension" id="dimension" class="form-control" placeholder="Masukan dimensi">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text">Cm</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Ilustrasi :</label>
                                                    <div class="col-md-10">
                                                        <select name="ilustration" id="ilustration" class="form-control">
                                                            <option value="">-- Pilih Ilustrasi --</option>
                                                            <option value="Ya">Ya</option>
                                                            <option value="Tidak">Tidak</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Kategori :</label>
                                                    <div class="col-md-10">
                                                        <select name="collection_category[]" id="collection_category" class="form-control select2" style="width:100%;" multiple>
                                                            @foreach($category as $c)
                                                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Subjek :</label>
                                                    <div class="col-md-10">
                                                        <select name="collection_subject[]" id="collection_subject" class="form-control" style="width:100%;" multiple></select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Kepeng :</label>
                                                    <div class="col-md-10">
                                                        <textarea name="kepeng" id="kepeng" class="form-control" style="resize:none;" placeholder="Masukan kepeng"></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Keterangan :</label>
                                                    <div class="col-md-10">
                                                        <textarea name="description" id="description" class="form-control" style="resize:none;" placeholder="Masukan informasi lain"></textarea>
                                                    </div>
                                                </div>
                                            </p>
                                            <h4 class="form-section">Hak Akses</h4>
                                            <p>
                                                <div class="alert alert-light">
                                                    <div class="form-check">
                                                        <input type="radio" class="form-check-input" name="access" id="access_1" value="1" checked>
                                                        <label class="form-check-label" for="access_1">
                                                            Akses full file berwatermak secara online
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="alert alert-light">
                                                    <div class="form-check">
                                                        <input type="radio" class="form-check-input" name="access" id="access_2" value="2">
                                                        <label class="form-check-label" for="access_2">
                                                            Akses hanya preview file secara online, namun tetap dapat di dayagunakan di lingkungan perpustakaan nasional RI dengan jaringan internet LAN
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="alert alert-light">
                                                    <div class="form-check">
                                                        <input type="radio" class="form-check-input" name="access" id="access_3" value="3">
                                                        <label class="form-check-label" for="access_3">
                                                            Akses hanya file preview secara online, dan tidak didayagunakan di lingkungan Perpustakaan Nasional RI selama 5 tahun sejak diserahkan. Setelah 5 tahun, akan didayagunakan oleh Perpustakaan Nasional RI di jaringan internet LAN
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="alert alert-light">
                                                    <div class="form-check">
                                                        <input type="radio" class="form-check-input" name="access" id="access_4" value="4">
                                                        <label class="form-check-label" for="access_4">
                                                            Akses hanya file preview secara online selamanya dan tidak didayagunakan di mana pun
                                                        </label>
                                                    </div>
                                                </div>
                                            </p>
                                            <h4 class="form-section">Kontributor</h4>
                                            <p>
                                                <table class="table table-bordered table-striped">
                                                    <tbody id="data_contributor">
                                                        <tr>
                                                            <td class="align-middle">
                                                                <select name="contributor_contributor_id_field[]" class="form-control select2">
                                                                    @foreach($contributor as $c)
                                                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td class="align-middle">
                                                                <input type="text" name="contributor_fullname_field[]" class="form-control" oninput="validationContributor()" placeholder="Nama">
                                                            </td>
                                                            <td class="align-middle">
                                                                <input type="text" name="contributor_title_field[]" class="form-control" oninput="validationContributor()" placeholder="Gelar">
                                                            </td>
                                                            <td class="align-middle">
                                                                <button type="button" class="btn btn-danger btn-sm col-12" id="remove_row_contributor"><i class="la la-trash"></i></button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-success btn-sm col-12" onclick="addElementContributor()"><i class="la la-plus"></i></button>
                                                </div>
                                            </p>
                                            <h4 class="form-section">Cover</h4>
                                            <div class="alert alert-warning">
                                                <small>
                                                    Jenis File Yang di Dukung <b>: JPG, JPEG, PNG</b><br>
                                                    Maksimal Ukuran File <b>: 1 MB</b>
                                                </small>
                                            </div>
                                            <div class="form-group">
                                                <input type="file" class="file-cover form-control-lg" name="cover" id="cover" data-theme="fa5">
                                            </div>
                                            <h4 class="form-section">Konten</h4>
                                            <div class="alert alert-warning">
                                                <small>
                                                    Jenis File Yang di Dukung <b>: PDF, EPUB, MOBI</b><br>
                                                    Maksimal Ukuran File <b>: 500 MB</b>
                                                </small>
                                            </div>
                                            <div class="form-group">
                                                <input type="file" class="file-content form-control-lg" name="original" id="original" data-theme="fa5">
                                            </div>
                                            <div class="form-group"><hr></div>
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="col-md-6">
                                                            <ul id="validation_contributor" class="text-danger font-italic"></ul>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="text-right">
                                                            <button type="button" class="btn btn-danger" onclick="document.location.reload(true)">Reset Semua</button>
                                                            <button type="button" class="btn btn-primary" onclick="create()">Tambahkan</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    $(function() {
        document.body.scrollTop            = 0;
        document.documentElement.scrollTop = 0;

        select2AutoSuggest('#publisher_id', 'load_publisher');
        select2AutoSuggestTags('#collection_subject', 'load_subject');

        $('#data_contributor').on('click', '#remove_row_contributor', function() {
            $(this).closest('tr').remove();
        });

        dragFile('.file-cover', ['jpg', 'jpeg', 'png']);
        dragFile('.file-content', ['pdf', 'epub', 'mobi']);
    });

    function addElementContributor() {
        $('#data_contributor').append(`
            <tr>
                <td class="align-middle">
                    <select name="contributor_contributor_id_field[]" class="form-control select2">
                        @foreach($contributor as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="align-middle">
                    <input type="text" name="contributor_fullname_field[]" class="form-control" oninput="validationContributor()" placeholder="Nama">
                </td>
                <td class="align-middle">
                    <input type="text" name="contributor_title_field[]" class="form-control" oninput="validationContributor()" placeholder="Gelar">
                </td>
                <td class="align-middle">
                    <button type="button" class="btn btn-danger btn-sm col-12" id="remove_row_contributor"><i class="la la-trash"></i></button>
                </td>
            </tr>
        `);

        validationContributor();

        $('.select2').select2({
            placeholder: '-- Pilih --'
        });
    }

    function formType() {
        var form_type = $('#form_type').val();
        $('#datatable_default').DataTable().columns.adjust();

        if(form_type == 'isbn') {
            $('#form_check_isbn').fadeIn(200);
            $('#form_success_check_isbn').hide();
            $('#kepeng').attr('disabled', false);
        } else if(form_type == 'non') {
            $('#form_check_isbn').hide();
            $('#form_success_check_isbn').fadeIn(200);
            $('#kepeng').attr('disabled', true);
            $('#kepeng').val('');
        } else {
            $('#form_check_isbn').hide();
            $('#form_success_check_isbn').hide();
            $('#kepeng').attr('disabled', true);
            $('#kepeng').val('');
        }

        reset();
        $('#form_type').val(form_type);
    }

    function reset() {
        $('#form_data').trigger('reset');
        $('#publisher_id').val('').trigger('change');
        $('#contributor_element').html('');
        $('#code').attr('readonly', false);
        $('#btn_check_code_isbn').attr('disabled', false);
    }

    function checkCodeIsbn() {
        if($('#code').val() != '') {
            $.ajax({
                url: '{{ url("admin/collection/check_code_isbn") }}',
                type: 'POST',
                data: {
                    code: $('#code').val()
                },
                dataType: 'JSON',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    loadingOpen('#configuration');
                    $('#validasi_element').hide();
                    $('#validasi_content').html('');
                },
                success: function(response) {
                    loadingClose('#configuration');
                    if(response.status == 201) {
                        window.location.href = response.data;
                    } else if(response.status == 200) {
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });

                        $('#code').val(response.code);
                        $('#title').val(response.title);
                        $('#publication_year').val(response.tahun_terbit);
                        $('#kepeng').val(response.kepeng);
                        $('#description').val(response.sinopsis);
                        $('#edition').val(response.edisi);
                        $('#total_page').val(response.jml_hlm);
                        $('#series').val(response.seri);

                        if(response.subjek) {
                            $('#collection_subject').html('<option value="' + response.subjek + '" selected>' + response.subjek + '</option>');
                        }

                        if(response.publisher_id) {
                            $('#publisher_id').html('<option value="' + response.publisher_id + '" selected>' + response.publisher_name + '</option>');
                        }

                        $('#code').attr('readonly', true);
                        $('#btn_check_code_isbn').attr('disabled', true);
                        $('#form_success_check_isbn').fadeIn(200);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });

                        $('#code').attr('readonly', false);
                        $('#btn_check_code_isbn').attr('disabled', false);
                        $('#form_success_check_isbn').hide();
                    }
                },
                error: function() {
                    loadingClose('#configuration');
                    Toast.fire({
                        icon: 'error',
                        title: 'Server Error!'
                    });
                }
            });
        } else {
            Swal.fire({
                position: 'top-end',
                icon: 'warning',
                title: 'Harap mengisi kode',
                showConfirmButton: false,
                timer: 1500
            });
        }
    }

    function success() {
        location.reload(true);
    }

    function create() {
        $.ajax({
            url: '{{ url("admin/collection/create_manual/1") }}' + '/' + $('#form_type').val(),
            type: 'POST',
            dataType: 'JSON',
            data: new FormData($('#form_data')[0]),
            cache: false,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                loadingOpen('#configuration');
                $('#validasi_element').hide();
                $('#validasi_content').html('');
            },
            success: function(response) {
                loadingClose('#configuration');
                if(response.status == 200) {
                    success();
                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });
                } else if(response.status == 422) {
                    $('#validasi_element').show();

                    document.body.scrollTop            = 0;
                    document.documentElement.scrollTop = 0;

                    Toast.fire({
                        icon: 'info',
                        title: 'Validasi'
                    });

                    $.each(response.error, function(i, val) {
                        $('#validasi_content').append('<li>' + val + '</li>');
                    });
                } else {
                    Toast.fire({
                        icon: 'warning',
                        title: response.message
                    });
                }
            },
            error: function() {
                loadingClose('#configuration');
                Toast.fire({
                    icon: 'error',
                    title: 'Server Error!'
                });
            }
        });
    }

    function createImport() {
        $.ajax({
            url: '{{ url("admin/collection/create_import/") }}' + '/' + 1,
            type: 'POST',
            dataType: 'JSON',
            data: new FormData($('#form_data_import')[0]),
            cache: false,
            contentType: false,
            processData: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                loadingOpen('.modal-content');
            },
            success: function(response) {
                loadingClose('.modal-content');
                if(response.status == 200) {
                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });
                    location.reload(true);
                } else if(response.status == 422) {
                    $.each(response.error, function(i, val) {
                        Toast.fire({
                            icon: 'danger',
                            title: 'Validasi',
                            text: val
                        });
                    });
                } else {
                    Toast.fire({
                        icon: 'warning',
                        title: response.message
                    });
                }
            },
            error: function() {
                loadingClose('.modal-content');
                Toast.fire({
                    icon: 'error',
                    title: 'Server Error!'
                });
            }
        });
    }
</script>
