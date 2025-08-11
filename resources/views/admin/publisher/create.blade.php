<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <h3 class="content-header-title mb-1 d-inline-block">Tambah Data Penerbit</h3><br>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Penerbit</a></li>
                            <li class="breadcrumb-item active">Tambah Data</li>
                        </ol>
                    </div>
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
                                    <form action="" id="form_data" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label>Foto :</label>
                                            <input type="file" class="form-control" name="photo" id="photo">
                                        </div>
                                        <div class="form-group">
                                            <label>Nama :</label>
                                            <input type="text" class="form-control" name="name" id="name" placeholder="Masukan nama lengkap">
                                        </div>
                                        <div class="form-group">
                                            <label>Email :</label>
                                            <input type="text" class="form-control" name="email" id="email" placeholder="Masukan email aktif">
                                        </div>
                                        <div class="form-group">
                                            <label>Website :</label>
                                            <input type="text" class="form-control" name="website" id="website" placeholder="Masukan website">
                                        </div>
                                        <div class="form-group">
                                            <label>Organisasi :</label>
                                            <select name="organization_id" id="organization_id" class="form-control select2">
                                                <option value="">-- Pilih --</option>
                                                @foreach($organization as $o)
                                                    <option value="{{ $o->id }}">{{ $o->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Jenis :</label>
                                            <select name="type" id="type" class="form-control">
                                                <option value="">-- Pilih --</option>
                                                <option value="1">Swasta</option>
                                                <option value="2">Perorangan</option>
                                                <option value="3">Pemerintahan</option>
                                            </select>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Fax :</label>
                                                    <input type="text" class="form-control" name="fax" id="fax" placeholder="Masukan nomor fax">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Kontak :</label>
                                                    <input type="text" name="contact" id="contact" class="form-control" placeholder="Masukan nomor kontak">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Telepon :</label>
                                                    <input type="text" name="phone" id="phone" class="form-control" placeholder="Masukan nomor telepon">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Provinsi :</label>
                                                    <select name="province_id" id="province_id" style="width:100%;" onchange="getCity()"></select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Kota :</label>
                                                    <select name="city_id" id="city_id" style="width:100%;" onchange="getDistrict()"></select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Kecamatan :</label>
                                                    <select name="district_id" id="district_id" style="width:100%;" onchange="getVillage()"></select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Kelurahan :</label>
                                                    <select name="village_id" id="village_id" style="width:100%;"></select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Kode Pos :</label>
                                            <input type="text" class="form-control" name="postal_code" id="postal_code" placeholder="Masukan kode pos">
                                        </div>
                                        <div class="form-group">
                                            <label>Alamat :</label>
                                            <textarea name="address" id="address" class="form-control" style="resize:none;" placeholder="Masukan alamat"></textarea>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>File Pernyataan :</label>
                                                    <input type="file" class="form-control" name="statement_letter" id="statement_letter">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>File Akta :</label>
                                                    <input type="file" class="form-control" name="birth_certificate" id="birth_certificate">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group"><hr></div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Username :</label>
                                                    <input type="text" name="username" id="username" class="form-control" placeholder="Masukan username">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Password :</label>
                                                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukan password">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Konfirmasi Password :</label>
                                                    <input type="password" name="c_password" id="c_password" class="form-control" placeholder="Masukan konfirmasi password">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group"><hr></div>
                                        <div class="form-group">
                                            <div class="text-right">
                                                <button type="button" class="btn btn-danger" onclick="document.location.reload(true)">Reset Semua</button>
                                                <button type="button" class="btn btn-primary" onclick="create()">Tambahkan</button>
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

        $('#datatable_default tbody').on('click', '#remove_field_contributor', function () {
            $('#datatable_default').DataTable().row($(this).parents('tr')).remove().draw();
        });

        select2AutoSuggest('#province_id', 'load_province');
        $('#city_id').select2();
        $('#district_id').select2();
        $('#village_id').select2();
    });

    function success() {
        location.reload(true);
    }

    function getCity() {
        var province_id = $('#province_id').val();
        if(province_id !== '') {
            select2AutoSuggest('#city_id', 'load_city/' + province_id);
        } else {
            $('#city_id').val('').trigger('change');
        }
    }

    function getDistrict() {
        var city_id = $('#city_id').val();
        if(city_id !== '') {
            select2AutoSuggest('#district_id', 'load_district/' + city_id);
        } else {
            $('#district_id').val('').trigger('change');
        }
    }

    function getVillage() {
        var district_id = $('#district_id').val();
        if(district_id !== '') {
            select2AutoSuggest('#village_id', 'load_village/' + district_id);
        } else {
            $('#village_id').val('').trigger('change');
        }
    }

    function create() {
        $.ajax({
            url: '{{ url("admin/publisher/create") }}',
            type: 'POST',
            dataType: 'JSON',
            data: new FormData($('#form_data')[0]),
            contentType: false,
            processData: false,
            cache: true,
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
