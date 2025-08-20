<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <h3 class="content-header-title mb-1 d-inline-block">Tambah Data Serial</h3><br>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Serial</a></li>
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
                                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal_serial_parent">Pilih Judul Serial</button>
                                    <form id="form_data" class="form">
                                        <div class="form-group">
                                            <div class="alert alert-danger" id="validasi_element" style="display:none;">
                                                <ul id="validasi_content"></ul>
                                            </div>
                                        </div>
                                        <h4 class="form-section">Meta Data</h4>
                                        <p>
                                            <div class="form-group row">
                                                <label class="col-md-2">Produser :</label>
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
                                                <label class="col-md-2">ISSN :</label>
                                                <div class="col-md-10">
                                                    <input type="text" class="form-control" name="code" id="code" placeholder="Masukan kode ISSN">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-2">Preview :</label>
                                                <div class="col-md-10">
                                                    <input type="text" name="preview" id="preview" class="form-control" placeholder="Ex: 1-3">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-2">Bulan Terbit Pertama Kali :</label>
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
                                                <label class="col-md-2">Tahun Terbit Pertama Kali:</label>
                                                <div class="col-md-10">
                                                    <input type="number" name="publication_year" id="publication_year" class="form-control" placeholder="Masukan tahun terbit">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-2">DDC :</label>
                                                <div class="col-md-10">
                                                    <input type="text" name="ddc" id="ddc" class="form-control" placeholder="Masukan DDC">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-2">Serial :</label>
                                                <div class="col-md-10">
                                                    <select name="serial" id="serial" class="form-control">
                                                        <option value="">-- Pilih Serial --</option>
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
                                            <input type="file" name="cover" id="cover" class="form-control">
                                        </div>
                                        <h4 class="form-section">Edisi</h4>
                                        <p>
                                            <div class="form-group">
                                                <div class="form-group text-right">
                                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal_edition">Tambah</button>
                                                </div>
                                                <div class="form-group">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped" id="datatable_edition">
                                                            <thead class="text-center">
                                                                <tr>
                                                                    <th>Edisi</th>
                                                                    <th>Tanggal Terbit</th>
                                                                    <th>Cover</th>
                                                                    <th>Konten</th>
                                                                    <th>Hapus</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="edition_element"></tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </p>
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

<div class="modal fade" id="modal_serial_parent" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped" id="datatable_default">
                        <thead>
                            <tr class="text-center">
                                <th>No</th>
                                <th>Judul</th>
                                <th>Total Edisi</th>
                                <th>Pilih</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($serial as $key => $s)
                                <tr class="text-center">
                                    <td class="align-middle">{{ $key + 1 }}</td>
                                    <td class="align-middle">{{ $s->title }}</td>
                                    <td class="align-middle">{{ $s->collectionEdition() ? $s->collectionEdition()->count() : 0 }}</td>
                                    <td class="align-middle">
                                        <input type="radio" onclick="getParentSerial({{ $s->id }})">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_edition" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="modal_edition_content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Edisi Serial</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" id="form_edition">
                    <div class="form-group">
                        <label>Edisi / Volume :</label>
                        <input type="text" name="edition_field" id="edition_field" class="form-control" placeholder="Masukan Edisi">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Terbit Edisi / Volume :</label>
                        <input type="date" name="date_field" id="date_field" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Cover :</label>
                        <input type="file" name="cover_field" id="cover_field" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Konten :</label>
                        <input type="file" name="original_field" id="original_field" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="addEdition()">Tambah</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        document.body.scrollTop            = 0;
        document.documentElement.scrollTop = 0;

        $('#datatable_edition tbody').on('click', '#remove_field_edition', function () {
            $('#datatable_edition').DataTable().row($(this).parents('tr')).remove().draw();
        });

        $('#data_contributor').on('click', '#remove_row_contributor', function() {
            $(this).closest('tr').remove();
        });

        select2AutoSuggest('#publisher_id', 'load_publisher');
        select2AutoSuggestTags('#collection_subject', 'load_subject');
    });

    function getParentSerial(parent_id) {
        window.location.href = '{{ url("admin/collection/manage/update") }}' + '/' + parent_id;
    }

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

    function addEdition() {
        var edition_field  = $('#edition_field').val();
        var date_field     = $('#date_field').val();
        var cover_field    = $('#cover_field').val();
        var original_field = $('#original_field').val();

        if(!edition_field || !date_field || !cover_field || !original_field) {
            Swal.fire('Harap mengisi semua field!', '', 'warning');
        } else {
            $.ajax({
                url: '{{ url("admin/collection/save_temporary") }}',
                type: 'POST',
                dataType: 'JSON',
                data: new FormData($('#form_edition')[0]),
                cache: false,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    loadingOpen('#modal_edition_content');
                },
                success: function(response) {
                    loadingClose('#modal_edition_content');
                    $('#form_data').append(`
                        <input type="hidden" name="edition_edition_field[]" value="` + edition_field + `">
                        <input type="hidden" name="edition_date_field[]" value="` + date_field + `">
                        <input type="hidden" name="edition_cover_field[]" value="` + $('#cover_field')[0].files[0].name + `">
                        <input type="hidden" name="edition_original_field[]" value="` + $('#original_field')[0].files[0].name + `">
                    `);

                    $('#datatable_edition').DataTable().row.add([
                        edition_field,
                        response.date_field,
                        response.cover_field,
                        response.original_field,
                        '<button type="button" class="btn btn-danger btn-sm" id="remove_field_edition"><i class="la la-trash"></i></button>'
                    ]).draw().node();

                    $('#modal_edition').modal('hide');
                    $('#edition_field').val('');
                    $('#date_field').val('');
                    $('#cover_field').val('');
                    $('#original_field').val('');
                }
            });
        }
    }

    function success() {
        location.reload(true);
    }

    function create() {
        $.ajax({
            url: '{{ url("admin/collection/create_manual/4") }}',
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
</script>
