{{-- @php
    dd($fields);
@endphp --}}

<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <h3 class="content-header-title mb-1 d-inline-block">Tambah Data {{ $shape }} </h3><br>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">{{ $shape }} </a></li>
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
                @if (session('success'))
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
                                    @if (array_key_exists('collection_edition', $fields))
                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                            data-target="#modal_serial_parent">Pilih Judul Serial</button>
                                    @endif
                                    <form id="form_data" class="form">
                                        {{-- @php
                                            dd($fields);
                                        @endphp --}}

                                        @if ($deposit_head_by_id['id'] == '7')
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <select name="form_type" id="form_type" class="form-control"
                                                            onchange="formType()">
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
                                                                <input type="text" class="form-control"
                                                                    name="code" id="code"
                                                                    placeholder="Masukan kode ISBN (xxx-xxx-xxxx-xx-x)">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button type="button" class="btn btn-warning col-12"
                                                                id="btn_check_code_isbn"
                                                                onclick="checkCodeIsbn()">Cari</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div id="form_success_check_isbn"
                                            @if ($deposit_head_by_id['id'] == '7') style="display:none" @endif>
                                            <div class="form-group">
                                                <hr>
                                            </div>
                                            <div class="form-group">
                                                <div class="alert alert-danger" id="validasi_element"
                                                    style="display:none;">
                                                    <ul id="validasi_content"></ul>
                                                </div>
                                            </div>
                                            <ul class="nav nav-tabs nav-underline no-hover-bg" id="pills-tab"
                                                role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link active" id="pills-meta-tab" data-toggle="pill"
                                                        href="#pills-meta" data-target="#pills-meta"
                                                        role="tab">Metadata
                                                    </a>
                                                </li>
                                                {{-- @if (array_key_exists('collection_edition', $fields))
                                                    <li class="nav-item" role="presentation">
                                                        <a class="nav-link" id="pills-edition-tab" data-toggle="pill"
                                                            href="#pills-edition" data-target="#pills-edition"
                                                            role="tab">Edisi
                                                            Serial</a>
                                                    </li>s
                                                @endif
                                                @if (array_key_exists('collection_copy', $fields))
                                                    <li class="nav-item" role="presentation">
                                                        <a class="nav-link" id="pills-exemplar-tab"
                                                            data-toggle="pill" href="#pills-exemplar"
                                                            data-target="#pills-exemplar" role="tab">Eksemplar</a>
                                                    </li>
                                                @endif --}}
                                                @if (array_key_exists('cover', $fields))
                                                    <li class="nav-item" role="presentation">
                                                        <a class="nav-link" id="pills-cover-tab" data-toggle="pill"
                                                            href="#pills-cover" data-target="#pills-cover"
                                                            type="button" role="tab">Cover</a>
                                                    </li>
                                                @endif
                                                {{-- @if (array_key_exists('collection_contributor', $fields))
                                                    <li class="nav-item" role="presentation">
                                                        <a class="nav-link" id="pills-contributor-tab"
                                                            data-toggle="pill" href="#pills-contributor"
                                                            data-target="#pills-contributor" role="tab">
                                                            Kontributor
                                                        </a>
                                                    </li>
                                                @endif --}}
                                            </ul>

                                            <div class="tab-content px-1 pt-1" id="pills-tabContent">
                                                <div class="tab-pane fade show active" id="pills-meta"
                                                    aria-labelledby="pills-meta-tab">
                                                    <p>
                                                        @if (array_key_exists('publisher_id', $fields))
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Penerbit :</label>
                                                                <div class="col-md-10">
                                                                    <select name="publisher_id" id="publisher_id"
                                                                        class="form-control"
                                                                        style="width:100%;"></select>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Provinsi Terbit :</label>
                                                                <div class="col-md-10">
                                                                    <div class="input-group">
                                                                        <input name="province" type="text"
                                                                            class="form-control" id="province"
                                                                            placeholder="Provinsi Terbit" readonly>
                                                                        <div class="input-group-append">
                                                                            <span class="input-group-text"
                                                                                id="text_code_province">.</span>
                                                                            <input id="province_id" type="hidden"
                                                                                name="province_id">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Kabupaten/Kota Terbit :</label>
                                                                <div class="col-md-10">
                                                                    <div class="input-group">
                                                                        <input name="city" type="text"
                                                                            class="form-control" id="city"
                                                                            placeholder="Kabupaten/Kota Terbit"
                                                                            readonly>
                                                                        <div class="input-group-append">
                                                                            <span class="input-group-text"
                                                                                id="text_code_city">.</span>
                                                                            <input id="city_id" type="hidden"
                                                                                name="city_id">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (array_key_exists('title', $fields))
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Judul :</label>
                                                                <div class="col-md-10">
                                                                    <textarea name="title" id="title" class="form-control" placeholder="Masukan judul"></textarea>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (array_key_exists('code', $fields) && $deposit_head_by_id['id'] != '7')
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Kode :</label>
                                                                <div class="col-md-10">
                                                                    <input type="text" class="form-control"
                                                                        name="code" id="code"
                                                                        placeholder="Masukan kode (ex: ISBN, ISSN, dll)">
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (array_key_exists('series', $fields))
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Seri :</label>
                                                                <div class="col-md-10">
                                                                    <input type="text" class="form-control"
                                                                        name="series" id="series"
                                                                        placeholder="Masukan seri">
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (array_key_exists('edition', $fields))
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Edisi :</label>
                                                                <div class="col-md-10">
                                                                    <input type="text" class="form-control"
                                                                        name="edition" id="edition"
                                                                        placeholder="Masukan edisi">
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (array_key_exists('publication_month', $fields))
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Bulan Terbit :</label>
                                                                <div class="col-md-5">
                                                                    <select name="publication_month"
                                                                        id="publication_month" class="form-control">
                                                                        <option value="">-- Pilih --</option>
                                                                        <option value="01">
                                                                            {{ App\Helper\GeneralHelper::getMonth('01') }}
                                                                        </option>
                                                                        <option value="02">
                                                                            {{ App\Helper\GeneralHelper::getMonth('02') }}
                                                                        </option>
                                                                        <option value="03">
                                                                            {{ App\Helper\GeneralHelper::getMonth('03') }}
                                                                        </option>
                                                                        <option value="04">
                                                                            {{ App\Helper\GeneralHelper::getMonth('04') }}
                                                                        </option>
                                                                        <option value="05">
                                                                            {{ App\Helper\GeneralHelper::getMonth('05') }}
                                                                        </option>
                                                                        <option value="06">
                                                                            {{ App\Helper\GeneralHelper::getMonth('06') }}
                                                                        </option>
                                                                        <option value="07">
                                                                            {{ App\Helper\GeneralHelper::getMonth('07') }}
                                                                        </option>
                                                                        <option value="08">
                                                                            {{ App\Helper\GeneralHelper::getMonth('08') }}
                                                                        </option>
                                                                        <option value="09">
                                                                            {{ App\Helper\GeneralHelper::getMonth('09') }}
                                                                        </option>
                                                                        <option value="10">
                                                                            {{ App\Helper\GeneralHelper::getMonth('10') }}
                                                                        </option>
                                                                        <option value="11">
                                                                            {{ App\Helper\GeneralHelper::getMonth('11') }}
                                                                        </option>
                                                                        <option value="12">
                                                                            {{ App\Helper\GeneralHelper::getMonth('12') }}
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                @if (array_key_exists('publication_year', $fields))
                                                                    <label class="col-md-1">Tahun Terbit :</label>
                                                                    <div class="col-md-4">
                                                                        <input type="text" name="publication_year"
                                                                            id="publication_year" class="form-control"
                                                                            placeholder="Masukan tahun terbit">
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                        @if (array_key_exists('serial', $fields))
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Serial :</label>
                                                                <div class="col-md-10">
                                                                    <select name="serial" id="serial"
                                                                        class="form-control">
                                                                        <option value="">-- Pilih Serial --
                                                                        </option>
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
                                                        @endif
                                                        @if (array_key_exists('received_at', $fields))
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Tanggal Terima :</label>
                                                                <div class="col-md-10">
                                                                    <input type="date" name="received_at"
                                                                        id="received_at" class="form-control"
                                                                        value="{{ date('Y-m-d') }}"
                                                                        max="{{ date('Y-m-d') }}">
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (array_key_exists('total_page', $fields))
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Total Halaman :</label>
                                                                <div class="col-md-10">
                                                                    <div class="input-group">
                                                                        <input type="number" name="total_page"
                                                                            id="total_page" class="form-control"
                                                                            placeholder="Masukan total halaman">
                                                                        <div class="input-group-prepend">
                                                                            <div class="input-group-text">Halaman</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (array_key_exists('dimension', $fields))
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Dimensi :</label>
                                                                <div class="col-md-10">
                                                                    <div class="input-group">
                                                                        <input type="number" name="dimension"
                                                                            id="dimension" class="form-control"
                                                                            placeholder="Masukan dimensi">
                                                                        <div class="input-group-prepend">
                                                                            <div class="input-group-text">Cm</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (array_key_exists('price', $fields))
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Harga :</label>
                                                                <div class="col-md-10">
                                                                    <div class="input-group">
                                                                        <div class="input-group-prepend">
                                                                            <select name="currency"
                                                                                class="form-control" id="currency">
                                                                                <option value="IDR">IDR</option>
                                                                                <option value="USD">USD</option>
                                                                                <option value="EUR">EUR</option>
                                                                            </select>
                                                                        </div>
                                                                        <input type="number" name="price"
                                                                            id="price" class="form-control"
                                                                            placeholder="Masukan Harga Koleksi">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (array_key_exists('collection_category', $fields))
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Kategori :</label>
                                                                <div class="col-md-10">
                                                                    <select name="collection_category[]"
                                                                        id="collection_category"
                                                                        class="form-control select2"
                                                                        style="width:100%;" multiple>
                                                                        @foreach ($category as $c)
                                                                            <option value="{{ $c->id }}">
                                                                                {{ $c->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (array_key_exists('collection_subject', $fields))
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Subjek :</label>
                                                                <div class="col-md-10">
                                                                    <select name="collection_subject[]"
                                                                        id="collection_subject" class="form-control"
                                                                        style="width:100%;" multiple></select>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (array_key_exists('kepeng', $fields))
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Kepeng :</label>
                                                                <div class="col-md-10">
                                                                    <textarea name="kepeng" id="kepeng" class="form-control" style="resize:none;" placeholder="Masukan kepeng"></textarea>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (array_key_exists('description', $fields))
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Keterangan :</label>
                                                                <div class="col-md-10">
                                                                    <textarea name="description" id="description" class="form-control" style="resize:none;"
                                                                        placeholder="Masukan informasi lain"></textarea>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if (!$is_serial)

                                                            <div class="form-group">
                                                                <hr>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Jumlah Eksemplar :</label>
                                                                <div class="col-md-10">
                                                                    <input type="number" name="copy_total"
                                                                        id="copy_total" class="form-control"
                                                                        placeholder="Masukan Jumlah Eksemplar" required
                                                                        value="{{ session('library_id') == 1 && $kategori_deposit == 'KC' ? 2 : 1 }}"
                                                                        style="width:100%;">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Tanggal Terima :</label>
                                                                <div class="col-md-10">
                                                                    <input type="date" name="copy_received_date"
                                                                        required id="copy_received_date"
                                                                        class="form-control">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Kondisi :</label>
                                                                <div class="col-md-10">
                                                                    <select name="copy_condition" id="copy_condition"
                                                                        required class="form-control"
                                                                        style="width:100%;">
                                                                        @foreach ($col_conditions as $id => $name)
                                                                            <option value="{{ $id }}">
                                                                                {{ $name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Ketersediaan :</label>
                                                                <div class="col-md-10">
                                                                    <select name="copy_availability"
                                                                        id="copy_availability"
                                                                        class="form-control select2" required
                                                                        style="width:100%;">
                                                                        <option></option>
                                                                        @foreach ($availability as $key => $value)
                                                                            <option value="{{ $key }}">
                                                                                {{ $value }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Lokasi :</label>
                                                                <div class="col-md-10">
                                                                    <select name="copy_lib_loc_id"
                                                                        id="copy_lib_loc_id"
                                                                        class="form-control select2" required
                                                                        style="width:100%;">
                                                                        <option></option>
                                                                        @foreach ($lib_loc as $key => $value)
                                                                            <option value="{{ $value->id }}">
                                                                                {{ $value->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </p>

                                                    {{-- @if (array_key_exists('collection_contributor', $fields))
                                                        <h4 class="form-section">Kontributor</h4>
                                                        <p>
                                                        <table class="table table-bordered table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th>Kontributor</th>
                                                                    <th>Nama</th>
                                                                    <th>Jabatan</th>
                                                                    <th>Tahun Kelahiran</th>
                                                                    <th>Tahun Kematian</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="data_contributor">
                                                                <tr>
                                                                    <td class="align-middle">
                                                                        <select
                                                                            name="contributor_contributor_id_field[]"
                                                                            class="form-control">
                                                                            @foreach ($contributor as $c)
                                                                                <option value="{{ $c->id }}">
                                                                                    {{ $c->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </td>
                                                                    <td class="align-middle">
                                                                        <select onchange="validationContributor()"
                                                                            name="contributor_fullname_field[]"
                                                                            class="form-control author"
                                                                            style="width:100%;">
                                                                            <option></option>
                                                                        </select>
                                                                    </td>
                                                                    <td class="align-middle">
                                                                        <input type="text"
                                                                            name="contributor_title_field[]"
                                                                            class="form-control"
                                                                            oninput="validationContributor()"
                                                                            placeholder="Gelar">
                                                                    </td>
                                                                    <td class="align-middle">
                                                                        <input type="number"
                                                                            name="contributor_year_of_birth_field[]"
                                                                            class="form-control"
                                                                            placeholder="Thn. Lahir">
                                                                    </td>
                                                                    <td class="align-middle">
                                                                        <input type="number"
                                                                            name="contributor_year_of_death_field[]"
                                                                            class="form-control"
                                                                            placeholder="Thn. Mati">
                                                                    </td>
                                                                    <td class="align-middle">
                                                                        <button type="button"
                                                                            class="btn btn-danger btn-sm col-12"
                                                                            id="remove_row_contributor"><i
                                                                                class="la la-trash"></i></button>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        <div class="form-group">
                                                            <button type="button"
                                                                class="btn btn-success btn-sm col-12"
                                                                onclick="addElementContributor()"><i
                                                                    class="la la-plus"></i></button>
                                                        </div>
                                                        </p>
                                                    @endif --}}

                                                </div>
                                                @if (array_key_exists('collection_edition', $fields))
                                                    <div class="tab-pane fade" id="pills-edition"
                                                        aria-labelledby="pills-edition-tab">
                                                        <p>
                                                        <div class="form-group">
                                                            <div class="form-group text-right">
                                                                <button type="button" class="btn btn-info btn-sm"
                                                                    data-toggle="modal" data-target="#modal_edition"
                                                                    onclick="openEditionModal('add')">Tambah</button>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="table-responsive">
                                                                    <table class="table table-bordered table-striped"
                                                                        id="datatable_edition">
                                                                        <thead class="text-center">
                                                                            <tr>
                                                                                <th>Edisi</th>
                                                                                <th>Tanggal Terbit</th>
                                                                                <th>Cover</th>
                                                                                <th>Total Copy</th>
                                                                                <th>Hapus</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody id="edition_element"></tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        </p>
                                                    </div>
                                                @endif
                                                @if (array_key_exists('cover', $fields))
                                                    <div class="tab-pane fade" id="pills-cover"
                                                        aria-labelledby="pills-cover-tab">
                                                        <div class="alert alert-warning">
                                                            <small>
                                                                Jenis File Yang di Dukung <b>: JPG, JPEG, PNG</b><br>
                                                                Maksimal Ukuran File <b>: 1 MB</b>
                                                            </small>
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="file" class="file-cover form-control-lg"
                                                                name="cover" id="cover" data-theme="fa5">
                                                        </div>
                                                        <div class="form-group">
                                                            <hr>
                                                        </div>
                                                    </div>
                                                @endif

                                            </div>
                                            <div class="form-group">
                                                <div class="row">
                                                    @if (array_key_exists('collection_contributor', $fields))
                                                        <div class="col-md-6">
                                                            <div class="col-md-6">
                                                                <ul id="validation_contributor"
                                                                    class="text-danger font-italic"></ul>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <div class="col-md-6">
                                                        <div class="text-right">
                                                            <button type="button" class="btn btn-danger"
                                                                onclick="document.location.reload(true)">Reset
                                                                Semua</button>
                                                            <button type="button" class="btn btn-primary"
                                                                onclick="create()">Tambahkan</button>
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

@if (array_key_exists('collection_edition', $fields))
    {{-- <div class="modal fade" id="modal_serial_parent" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
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
                                @foreach ($serial as $key => $s)
                                    <tr class="text-center">
                                        <td class="align-middle">{{ $key + 1 }}</td>
                                        <td class="align-middle">{{ $s->title }}</td>
                                        <td class="align-middle">
                                            {{ $s->collectionEdition() ? $s->collectionEdition()->count() : 0 }}</td>
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
    </div> --}}

    <div class="modal fade" id="modal_serial_parent" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Judul Serial Parent</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped" id="datatable_serial_parent">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Judul</th>
                                    <th>Total Edisi</th>
                                    <th>Pilih</th>
                                </tr>
                            </thead>
                            <tbody>

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

    <div class="modal fade" id="modal_edition" data-backdrop="static" data-keyboard="false"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content" id="modal_edition_content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Edisi Serial</h5>
                    <button onclick="resetEditionModal()" type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" id="form_edition">
                        <input id="edition_id_temp" type="hidden" value="new">
                        <div class="form-group">
                            <label>Edisi / Volume :</label>
                            <input type="text" name="edition_field" id="edition_field" class="form-control"
                                placeholder="Masukan Edisi">
                        </div>
                        <div class="form-group">
                            <label>Tanggal Terbit Edisi / Volume :</label>
                            <input type="date" name="date_field" id="date_field" class="form-control">
                        </div>
                        <div class="form-group" id="cover_field_wrapper">
                            <label>Cover :</label>
                            <input type="file" name="cover_field" id="cover_field" class="form-control">
                        </div>
                        {{-- <h4 class="form-section">Lokasi</h4>
                        <p>
                        <table class="table table-bordered table-striped">
                            <tbody id="data_copies">
                                <tr row="0">
                                    <td class="align-middle">
                                        <select id="location_lib_loc_id_field_0" row="0"
                                            name="location_lib_loc_id_field[]"
                                            class="form-control lib_loc_id edition_location location_lib_loc_id_field select2"
                                            style="width:100%;">
                                            <option></option>
                                            @foreach ($lib_loc as $key => $value)
                                                <option value="{{ $value->id }}">
                                                    {{ $value->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="align-middle">
                                        <input type="number" id="location_copy_field_0" row="0"
                                            name="location_copy_field[]"
                                            class="form-control edition_location location_copy_field"
                                            oninput="validationCopy()"
                                            value="{{ session('library_id') == 1 && $kategori_deposit == 'KC' ? 2 : 1 }}"
                                            placeholder="Eksemplar/Copy">
                                    </td>
                                    <td class="align-middle">
                                        <select id="location_condition_field_0" row="0"
                                            name="location_condition_field[]"
                                            class="form-control edition_location location_condition_field">
                                            @foreach ($col_conditions as $id => $name)
                                                <option value="{{ $id }}">
                                                    {{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="align-middle">
                                        <button type="button" class="btn btn-danger btn-sm col-12"
                                            id="remove_row_location"><i class="la la-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group">
                            <button type="button" class="btn btn-success btn-sm col-12"
                                onclick="addElementCopies()"><i class="la la-plus"></i></button>
                        </div>
                        </p>
                        <div class="form-group row">
                            <label class="col-md-2">Total Exemplar/Copy :</label>
                            <div class="col-md-10">
                                <input readonly type="text" name="total_copy" id="total_copy"
                                    class="form-control" placeholder="Total Copy">
                            </div>
                        </div> --}}
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="resetEditionModal()"
                        data-dismiss="modal">Close</button>
                    <button id="btn_create" type="button" style="display:none" class="btn btn-primary"
                        onclick="addEdition()">Tambah</button>
                    <button id="btn_update" type="button" style="display:none" class="btn btn-primary"
                        onclick="editEdition()">Ubah</button>
                </div>
            </div>
        </div>
    </div>
@endif


<script>
    $(function() {
        @if (array_key_exists('serial', $fields))
            loadDataTableSerialParent();
            // Attach a function to the show.bs.modal event
            $('#modal_serial_parent').on('show.bs.modal', function() {
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
            });
        @endif

        $('#datatable_copies').DataTable();
        sessionStorage.setItem('temp_copies', JSON.stringify([]));
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;

        select2AutoSuggest('#publisher_id', 'load_publisher');
        // select2AutoSuggest('.lib_loc_id', 'load_lib_loc');
        select2AutoSuggestTags('#collection_subject', 'load_subject');
        select2AutoSuggestTags('.author', 'load_author');

        $('#data_contributor').on('click', '#remove_row_contributor', function() {
            $(this).closest('tr').remove();
        });

        $('#data_copies').on('click', '#remove_row_copy', function() {
            var row = $(this).closest('tr');
            var rowId = $(this).attr('row');
            var edition_id = $("#copy_edition_id").select2('data')[0].id;
            $('#datatable_copies').DataTable().row(row).remove().draw();
            updateStoredData('temp_copies', null, edition_id, rowId);
            validationCopy();
            console.log(JSON.parse(sessionStorage.getItem('temp_copies')));
        });

        $('#datatable_edition tbody').on('click', '#remove_field_edition', function() {
            $('#datatable_edition').DataTable().row($(this).parents('tr')).remove().draw();
        });

        $('#datatable_edition tbody').on('click', '#edit_field_edition', function() {

            var row = $(this).attr('row');
            var detailArray = $("#json_detail" + row).val();
            detailArray = JSON.parse(detailArray);
            resetEditionModal();
            openEditionModal(row);
            // $('#modal_edition').modal('show');
            $('#edition_field').val(detailArray.edition);
            $('#date_field').val(detailArray.date);
            //detailArray.cover_file
            if (detailArray.cover !== '' && detailArray.cover !== null) {
                var cover_html =
                    '<input type="file" name="cover_field" id="cover_field" class="form-control"> <textarea style="display:none" type"hidden" id="old_data" name="old_data">' +
                    $("#json_detail" + row).val() + '</textarea>';
                $('#cover_field_wrapper').html('');
                $('#cover_field_wrapper').html(
                    '<label>Cover :</label>' +
                    '<div class="input-group">' + cover_html +
                    '<div class="input-group-append" id="cover_file_temp">' + detailArray
                    .cover_file +
                    '<button class="btn btn-outline-danger" type="button" onclick="removeCoverEdition()">Remove Cover</button>' +
                    '</div>'
                )
            }
            // $('#data_copies').html('');
            // $.each(detailArray.location, function(i, val) {
            //     addElementCopies();
            //     $('#location_lib_loc_id_field_' + i).val(val.lib_loc_id).trigger('change');
            //     $('#location_copy_field_' + i).val(val.copy);
            //     $('#location_condition_field_' + i).val(val.condition);
            // });
        });

        $(document).on("change", "#publisher_id", function() {
            var publisher_id = $("#publisher_id").val();
            var selectedOptions = $(this).val();
            if (selectedOptions && selectedOptions.length > 0) {
                var params = {
                    publisher_id: publisher_id
                };
                $.getJSON('{{ url('admin/collection/kckra/get_publisher') }}', params, function(data) {
                    if (typeof data.province.id !== 'undefined' && data.province.id !== null) {
                        $("#province").val(data.province.name);
                        $("#city").val(data.city.name);
                        $("#text_code_province").html(data.province.id);
                        $("#province_id").val(data.province.id);
                        $("#text_code_city").html(data.city.id);
                        $("#city_id").val(data.city.id);
                    } else {
                        $("#province").val('');
                        $("#city").val('');
                        $("#text_code_province").html('');
                        $("#province_id").val('');
                        $("#text_code_city").html('');
                        $("#city_id").val('');
                    }
                });
            } else {
                $("#province").val('');
                $("#city").val('');
                $("#text_code_province").html('');
                $("#province_id").val('');
                $("#text_code_city").html('');
                $("#city_id").val('');
            }
        });

        //onchange edition exemplar
        $(document).on("change", "#copy_edition_id", function() {
            var edition_id = $(this).val();
            if ($("#copy_edition_id").val() != edition_id) {
                $("#block_name").select2('val', id);
            }
            //reset field
            resetCopy();
            //reset datatable
            $('#datatable_copies').DataTable().clear().draw();

            //initialize datas
            var temp_copies = JSON.parse(sessionStorage.getItem('temp_copies'));
            var edition_id = $("#copy_edition_id").val();
            if (temp_copies.hasOwnProperty(edition_id)) {
                $.each(temp_copies[edition_id], function(key, value) {
                    $('#datatable_copies').DataTable().row.add([
                        value.no,
                        value.received_date,
                        value.condition,
                        value.edition,
                        value.price,
                        value.availability,
                        value.library,
                        value.lib_loc,
                        value.quarantine,
                        `<button type="button" row="` + value.row +
                        `" class="btn btn-danger btn-sm col-12" id="remove_row_copy"><i class="la la-trash"></i></button>`
                    ]).draw().node();
                });
            }

            validationCopy();
        });

        dragFile('.file-cover', ['jpg', 'jpeg', 'png']);
        dragFile('.file-content', ['pdf', 'epub', 'mobi']);
        validationCopy();
    });

    function getParentSerial(parent_id) {
        window.location.href = '{{ url('admin/collection/kckra/manage/update') }}' + '/' + parent_id;
    }

    function removeCoverEdition() {
        $("#old_data").remove();
        $("#cover_file_temp").remove();
    }

    function openEditionModal(param) {
        if (param == 'add') {
            $("#edition_id_temp").val('new');
            $('#btn_create').show();
            $('#btn_update').hide();
            $('#modal_edition').modal('show');
        } else {
            $("#edition_id_temp").val(param);
            $('#btn_update').show();
            $('#btn_create').hide();
            $('#modal_edition').modal('show');
        }
    }

    function resetEditionModal() {
        $('#modal_edition').modal('hide');
        $('#edition_field').val('');
        $('#date_field').val('');
        $('#cover_field').val('');
    }

    function addElementContributor() {
        $('#data_contributor').append(`
            <tr>
                <td class="align-middle">
                    <select name="contributor_contributor_id_field[]" class="form-control select2">
                        @foreach ($contributor as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="align-middle">
                    <select onchange="validationContributor()" name="contributor_fullname_field[]" class="form-control author" style="width:100%;"> <option></option> </select>
                </td>
                <td class="align-middle">
                    <input type="text" name="contributor_title_field[]" class="form-control" oninput="validationContributor()" placeholder="Gelar">
                </td>
                <td class="align-middle">
                    <input type="number" name="contributor_year_of_birth_field[]" class="form-control" placeholder="Thn. Lahir">
                </td>
                <td class="align-middle">
                    <input type="number" name="contributor_year_of_death_field[]" class="form-control" placeholder="Thn. Mati">
                </td>
                <td class="align-middle">
                    <button type="button" class="btn btn-danger btn-sm col-12" id="remove_row_contributor"><i class="la la-trash"></i></button>
                </td>
            </tr>
        `);

        select2AutoSuggestTags('.author', 'load_author');
        validationContributor();

        $('.select2').select2({
            placeholder: '-- Pilih --'
        });
    }

    function addElementCopies() {

        var copy_total = $("#copy_total").val();
        var received_date = $("#copy_received_date").val();
        var condition_id = $("#copy_condition").val();
        var condition = $('#copy_condition').find('option:selected').text().replace(/\s+/g, ' ').trim();
        var edition_id = $("#copy_edition_id").select2('data')[0].id;
        var edition = $("#copy_edition_id").find(':selected').text().replace(/\s+/g, ' ').trim();
        var price = $("#copy_price").val();
        var availability_id = $("#copy_availability").select2('data')[0].id;
        var availability = $("#copy_availability").find(':selected').text().replace(/\s+/g, ' ').trim();
        var library_id = `{{ $library->id }}`;
        var library = `{{ $library->name }}`;
        var lib_loc_id = $("#copy_lib_loc_id").select2('data')[0].id;
        var lib_loc = $("#copy_lib_loc_id").find(':selected').text().replace(/\s+/g, ' ').trim();
        var quarantine = null;

        for (let i = 0; i < copy_total; i++) {
            var row = parseInt($("#total_copy").val());
            var no = row + 1;

            var data = {
                row: row,
                no: no,
                received_date: received_date,
                condition_id: condition_id,
                condition: condition,
                edition_id: edition_id,
                edition: edition,
                price: price,
                availability: availability,
                availability_id: availability_id,
                library_id: library_id,
                library: library,
                lib_loc_id: lib_loc_id,
                lib_loc: lib_loc,
                quarantine: quarantine
            };

            $('#datatable_copies').DataTable().row.add([
                no,
                received_date,
                condition,
                edition,
                price,
                availability,
                library,
                lib_loc,
                quarantine,
                `<button type="button" row="` + row +
                `" class="btn btn-danger btn-sm col-12" id="remove_row_copy"><i class="la la-trash"></i></button>`
            ]).draw().node();

            //update the data to sessionStorage
            updateStoredData('temp_copies', data, edition_id, row);

            //reset the exemplar fields
            // resetCopy();
            validationCopy();
        }

        // select2AutoSuggest('.lib_loc_id', 'load_lib_loc');
        $('.select2').select2({
            placeholder: '-- Pilih --'
        });

        console.log(JSON.parse(sessionStorage.getItem('temp_copies')));
        resetCopy();

    }

    function resetCopy() {
        $("#copy_total").val({{ session('library_id') == 1 && $kategori_deposit == 'KC' ? 2 : 1 }});
        $("#copy_received_date").val('');
        $("#copy_condition").prop('selectedIndex', -1);
        $("#copy_price").val('');
        $("#copy_availability").val(null).trigger('change');
        $("#copy_lib_loc_id").val(null).trigger('change');
    }

    function updateStoredData(key, data, parent_id = null, child_id = null) {
        //get initial data from sessionStorage
        var storedData = JSON.parse(sessionStorage.getItem(key));
        //check if updated data is exist, if exist update.
        if (data !== null) {
            if (parent_id != null) {
                if (child_id != null) {
                    if (!storedData.hasOwnProperty(parent_id)) {
                        storedData[parent_id] = [];
                    } else {
                        if (!storedData[parent_id].hasOwnProperty(child_id)) {
                            storedData[parent_id][child_id] = [];
                        }
                    }
                    storedData[parent_id][child_id] = data;
                } else {
                    if (!storedData.hasOwnProperty(parent_id)) {
                        storedData[parent_id] = [];
                    }
                    storedData[parent_id].push(data);
                }
            } else {
                storedData.push(data);
            }
        } else {
            //if updated data is not exist delete
            if (parent_id != null) {
                if (child_id != null) {
                    if (storedData.hasOwnProperty(parent_id)) {
                        if (storedData[parent_id].hasOwnProperty(child_id)) {
                            delete storedData[parent_id][child_id];
                        }
                    }
                } else {
                    if (storedData.hasOwnProperty(parent_id)) {
                        delete storedData[parent_id];
                    }
                }
            } else {
                storedData = [];
            }
        }


        // Store the updated array back into sessionStorage
        sessionStorage.setItem(key, JSON.stringify(storedData));
    }

    function addEdition() {
        var edition_field = $('#edition_field').val();
        var date_field = $('#date_field').val();
        var cover_field = $('#cover_field').val();
        var location_fields = true;
        // $('.location_lib_loc_id_field, .location_copy_field, .location_condition_field').each(function() {
        //     if ($(this).val() == '') {
        //         location_fields = false;
        //     }
        // });
        // console.log(location_fields);

        if (!edition_field || !date_field) {
            Swal.fire('Harap mengisi field edition, date, dan lokasi koleksi!', '', 'warning');
        } else {
            console.log(new FormData($('#form_edition')[0]));
            $.ajax({
                url: '{{ url('admin/collection/kckra/save_temporary') }}',
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
                    var current_row = $('#datatable_edition').DataTable().data().length;
                    var arrDetailLocation = [];
                    var file_name = ($('#cover_field')[0]?.files?.[0]?.name) ?? null;
                    var html_append = `
                        <div class="detail` + current_row + `edition detail_edition" row="` + current_row + `">
                        <input type="hidden" name="edition_edition_field[` + current_row + `]" value="` +
                        edition_field + `">
                        <input type="hidden" name="edition_date_field[` + current_row + `]" value="` + date_field + `">
                        <input type="hidden" name="edition_cover_field[` + current_row + `]" value="` + file_name + `">
                    `;

                    // $('.location_lib_loc_id_field').each(
                    //     function(index, value) {
                    //         if ($(this).val() != '') {
                    //             if (!Array.isArray(arrDetailLocation[index])) {
                    //                 arrDetailLocation[index] = {};
                    //             }
                    //             arrDetailLocation[index].lib_loc_id = $(this).val();
                    //             html_append +=
                    //                 `<input type="hidden" name="edition_location_field[location_lib_loc_id_field][` +
                    //                 current_row + `][` + index + `]" value="` + $(this).val() + `">`;
                    //         }
                    //     }
                    // );
                    // $('.location_copy_field').each(
                    //     function(index, value) {
                    //         if ($(this).val() != '') {
                    //             arrDetailLocation[index].copy = $(this).val();
                    //             html_append +=
                    //                 `<input type="hidden" name="edition_location_field[location_copy_field][` +
                    //                 current_row + `][` + index + `]" value="` + $(this).val() + `">`;
                    //         }
                    //     }
                    // );
                    // $('.location_condition_field').each(
                    //     function(index, value) {
                    //         if ($(this).val() != '') {
                    //             arrDetailLocation[index].condition = $(this).val();
                    //             html_append +=
                    //                 `<input type="hidden" name="edition_location_field[location_condition_field][` +
                    //                 current_row + `][` + index + `]" value="` + $(this).val() + `">`;
                    //         }
                    //     }
                    // );

                    html_append += `</div>`;

                    var arrDetail = {
                        edition: edition_field,
                        date: date_field,
                        cover: file_name,
                        cover_file: response.cover_field,
                        // location: arrDetailLocation
                    };

                    // console.log(arrDetail);
                    // $('#form_data').append(html_append);

                    $('#datatable_edition').DataTable().row.add([
                        edition_field,
                        response.date_field,
                        response.cover_field,
                        response.total_copy,
                        '<button row="' + current_row +
                        '" type="button" class="btn btn-primary btn-sm action-button" id="edit_field_edition"><i class="la la-pencil-square"></i></button>' +
                        '&nbsp;' +
                        '<button row="' + current_row +
                        '" type="button" class="btn btn-danger btn-sm" id="remove_field_edition"><i class="la la-trash"></i></button>' +
                        html_append +
                        '<textarea style="display:none" id="json_detail' + current_row + '">' + JSON
                        .stringify(arrDetail) + '</textarea>'
                    ]).draw().node();

                    //update selection edition
                    updateSelectEdition();

                    //reset modal
                    resetEditionModal();
                }
            });
        }
    }

    function editEdition() {
        var current_row = $('#edition_id_temp').val();
        var edition_field = $('#edition_field').val();
        var date_field = $('#date_field').val();
        var cover_field = $('#cover_field').val();
        // var location_fields = true;
        // $('.location_lib_loc_id_field, .location_copy_field, .location_condition_field').each(function() {
        //     if ($(this).val() == '') {
        //         location_fields = false;
        //     }
        // });

        if (!edition_field || !date_field) {
            Swal.fire('Harap mengisi field edition, date, dan lokasi koleksi!', '', 'warning');
        } else {
            // console.log(new FormData($('#form_edition')[0]));
            $.ajax({
                url: '{{ url('admin/collection/kckra/save_temporary') }}',
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
                    var arrDetailLocation = [];
                    var file_name = ($('#cover_field')[0]?.files?.[0]?.name) ?? null;
                    var html_append = `
                        <div class="detail` + current_row + `edition detail_edition" row="` + current_row + `">
                        <input type="hidden" name="edition_edition_field[` + current_row + `]" value="` +
                        edition_field + `">
                        <input type="hidden" name="edition_date_field[` + current_row + `]" value="` + date_field + `">
                        <input type="hidden" name="edition_cover_field[` + current_row + `]" value="` + file_name + `">
                    `;

                    html_append += `</div>`;

                    var arrDetail = {
                        edition: edition_field,
                        date: date_field,
                        cover: file_name,
                        cover_file: response.cover_field,
                    };


                    $('#datatable_edition').DataTable().row($('button[row="' + current_row + '"]').parent()
                        .parent()).data(
                        [
                            edition_field,
                            response.date_field,
                            response.cover_field,
                            response.total_copy,
                            '<button row="' + current_row +
                            '" type="button" class="btn btn-primary btn-sm action-button" id="edit_field_edition"><i class="la la-pencil-square"></i></button>' +
                            '&nbsp;' +
                            '<button row="' + current_row +
                            '" type="button" class="btn btn-danger btn-sm" id="remove_field_edition"><i class="la la-trash"></i></button>' +
                            html_append +
                            '<textarea style="display:none" id="json_detail' + current_row + '">' + JSON
                            .stringify(arrDetail) + '</textarea>'
                        ]
                    ).draw();

                    //update selection edition
                    updateSelectEdition();

                    //reset modal
                    resetEditionModal();
                }
            });
        }
    }

    function updateSelectEdition() {
        //update data to the exemplar tab's edition select
        $("#copy_edition_id").html('<option></option>');
        $('#datatable_edition').DataTable().rows().every(function(rowIdx, tableLoop, rowLoop) {
            var rowData = this.data(); // Get the data for the current row
            $("#copy_edition_id").append('<option value="' + rowIdx + '">' + rowData[0] +
                '</option>');
        });
    }

    function validationCopy() {
        var total = $('#datatable_copies').DataTable().rows().count();
        $("#total_copy").val(total);
    }

    function formType() {
        var form_type = $('#form_type').val();
        $('#datatable_default').DataTable().columns.adjust();

        if (form_type == 'isbn') {
            $('#form_check_isbn').fadeIn(200);
            $('#form_success_check_isbn').hide();
            $('#kepeng').attr('disabled', false);
        } else if (form_type == 'non') {
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
        validationCopy();
    }

    function checkCodeIsbn() {
        if ($('#code').val() != '') {
            $.ajax({
                url: '{{ url('admin/collection/kckra/check_code_isbn') }}',
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
                    if (response.status == 201) {
                        window.location.href = response.data;
                    } else if (response.status == 200) {
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });

                        response = response.data;
                        // console.log(response);

                        $('#code').val(response.code);
                        $('#title').val(response.title);
                        $('#publication_year').val(response.tahun_terbit);
                        $('#kepeng').val(response.kepeng);
                        $('#description').val(response.sinopsis);
                        $('#edition').val(response.edisi);
                        $('#total_page').val(response.jml_hlm);
                        $('#series').val(response.seri);

                        if (response.subjek) {
                            $('#collection_subject').html('<option value="' + response.subjek +
                                '" selected>' + response.subjek + '</option>');
                        }

                        if (response.publisher_id) {
                            $('#publisher_id').html('<option value="' + response.publisher_id +
                                '" selected>' + response.publisher_name + '</option>');
                            $("#province").val(response.publisher_province);
                            $("#city").val(response.publisher_city);
                            $("#text_code_province").html(response.publisher_province_id);
                            $("#province_id").val(response.publisher_province_id);
                            $("#text_code_city").html(response.publisher_city_id);
                            $("#city_id").val(response.publisher_city_id);
                        }

                        $('#code').attr('readonly', true);
                        $('#btn_check_code_isbn').attr('disabled', true);
                        $('#form_success_check_isbn').fadeIn(200);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response.message,
                            showConfirmButton: true,
                            allowOutsideClick: true, // Allow dismissing by clicking outside the alert
                            allowEscapeKey: true // Allow dismissing by pressing the Escape key
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

    function success(collection_id = null) {
        if (collection_id !== null) {
            window.location.href = '{{ url('admin/collection/kckra/manage/update') }}' + '/' + collection_id;
        } else {
            location.reload(true);
        }
    }

    function create() {

        var status = true;
        if ($("#copy_total").val() != '' && $("#copy_total").val() != 0) {
            if (
                $("#copy_received_date").val() == '' ||
                $("#copy_condition").val() == '' ||
                $("#copy_availability").val() == '' ||
                $("#copy_lib_loc_id").val() == ''
            ) {
                loadingClose('#configuration');
                Toast.fire({
                    icon: 'warning',
                    title: 'Mohon Pastikan tgl terima, kondisi, ketersediaan, dan lokasi eksemplar koleksi terisi!'
                });
                status = false;
            }
        }

        if ($("#province").val() == '' && $("#city").val() == '') {
            loadingClose('#configuration');
            Toast.fire({
                icon: 'warning',
                title: 'Mohon Pastikan Tempat Terbit Penerbit Sudah Terisi Pada Master Penerbit!'
            });
            status = false;
        }

        if (status) {
            $.ajax({
                url: '{{ url('admin/collection/kckra/create_manual/' . $deposit_head_by_id['id']) }}' +
                    '/' + $('#form_type').val(),
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
                    if (response.status == 200) {
                        Toast.fire({
                            icon: 'success',
                            title: 'Berhasil Menambahkan Data Koleksi KCKRA!'
                        });
                        success(response.collection_id);
                        // sessionStorage.removeItem('temp_copies');
                    } else if (response.status == 422) {
                        $('#validasi_element').show();

                        document.body.scrollTop = 0;
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
                error: function(xhr, status, error) {
                    loadingClose('#configuration');
                    Toast.fire({
                        icon: 'error',
                        title: 'Server Error!'
                    });
                    console.log(xhr)
                    console.log(status)
                    console.log(error)
                }
            });
        }

    }

    function createImport() {
        $.ajax({
            url: '{{ url('admin/collection/create_import/') }}' + '/' + 1,
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
                if (response.status == 200) {
                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });
                    location.reload(true);
                } else if (response.status == 422) {
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

    function loadDataTableSerialParent() {
        $('#datatable_serial_parent').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            scrollX: true,
            order: [
                [0, 'asc']
            ],
            iDisplayInLength: 10,
            pagingType: 'input',
            ajax: {
                url: '{{ url('admin/collection/kckra/datatable_serial_parent') }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(d) {
                    // Add custom parameters to the data object
                    d.type = '{{ $deposit_head_by_id['id'] }}';
                },
            },
        });

    }
</script>
