<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <h3 class="content-header-title mb-1 d-inline-block">Edit {{ $shape }}</h3><br>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">{{ $shape }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('admin/collection/manage/1') }}">Pengelolaan</a>
                            </li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-header-right col-md-6 col-12 mb-2 mt-1">
                <div class="float-md-right">
                    <a href="{{ url("admin/collection/kckra/create_manual/$type") }}" class="btn btn-primary">Tambah
                        Data Baru</a>
                    <a href="{{ url("admin/collection/kckra/manage/$type") }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="configuration">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-content collapse show">
                                <div class="card-body card-dashboard">
                                    <form method="POST" action="{{ $collection->lock ? $locked_url : '' }}"
                                        enctype="multipart/form-data" class="form">
                                        @csrf
                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <ul>
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @elseif(session('failed'))
                                            <div class="alert bg-danger alert-icon-left alert-dismissible mb-2"
                                                role="alert">
                                                <span class="alert-icon"><i class="la la-check"></i></span>
                                                <button type="button" class="close" data-dismiss="alert"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                                <strong>Failed!</strong> {{ session('failed') }}
                                            </div>
                                        @endif

                                        <ul class="nav nav-tabs nav-underline no-hover-bg" id="pills-tab"
                                            role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link active" id="pills-meta-tab" data-toggle="pill"
                                                    href="#pills-meta" data-target="#pills-meta" role="tab">Metadata
                                                </a>
                                            </li>
                                            @if (array_key_exists('cover', $fields))
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link" id="pills-cover-tab" data-toggle="pill"
                                                        href="#pills-cover" data-target="#pills-cover" type="button"
                                                        role="tab">Cover</a>
                                                </li>
                                            @endif
                                            @if (array_key_exists('collection_edition', $fields))
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link" id="pills-edition-tab" data-toggle="pill"
                                                        href="#pills-edition" data-target="#pills-edition"
                                                        role="tab">Edisi
                                                        Serial</a>
                                                </li>
                                            @endif
                                            @if (array_key_exists('collection_copy', $fields))
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link" id="pills-exemplar-tab" data-toggle="pill"
                                                        href="#pills-exemplar" data-target="#pills-exemplar"
                                                        role="tab">Eksemplar</a>
                                                </li>
                                            @endif
                                            @if (array_key_exists('collection_media', $fields))
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link" id="pills-media-tab" data-toggle="pill"
                                                        href="#pills-media" data-target="#pills-media"
                                                        role="tab">Konten Digital</a>
                                                </li>
                                            @endif
                                        </ul>
                                        <div class="tab-content mt-3" id="pills-tabContent">
                                            <div class="tab-pane fade show active" id="pills-meta"
                                                aria-labelledby="pills-meta-tab">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        @if (array_key_exists('publisher_id', $fields))
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Penerbit :</label>
                                                                <div class="col-md-10">
                                                                    <select name="publisher_id" id="publisher_id"
                                                                        class="form-control" style="width:100%;"
                                                                        {{ $collection->lock ? 'disabled' : '' }}>
                                                                        <option
                                                                            value="{{ $collection->publisher->id }}"
                                                                            selected>
                                                                            {{ $collection->publisher->name }}</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Provinsi Terbit :</label>
                                                                <div class="col-md-10">
                                                                    <div class="input-group">
                                                                        <input name="province" type="text"
                                                                            class="form-control" id="province"
                                                                            placeholder="Provinsi Terbit"
                                                                            value="{{ $collection->publisher->province->name }}"
                                                                            readonly>
                                                                        <div class="input-group-append">
                                                                            <span class="input-group-text"
                                                                                id="text_code_province">{{ $collection->publisher->province->id }}</span>
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
                                                                            value="{{ $collection->publisher->city->name }}"
                                                                            readonly>
                                                                        <div class="input-group-append">
                                                                            <span class="input-group-text"
                                                                                id="text_code_city">{{ $collection->publisher->city->id }}</span>
                                                                            <input id="city_id" type="hidden"
                                                                                name="city_id">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Mark National :</label>
                                                                <div class="col-md-10">
                                                                    <input type="text" class="form-control"
                                                                        value="{{ $collection->mark_national }}"
                                                                        disabled>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Mark Province :</label>
                                                                <div class="col-md-10">
                                                                    <input type="text" class="form-control"
                                                                        value="{{ $collection->mark_province }}"
                                                                        disabled>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (array_key_exists('title', $fields))
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Judul Asli :</label>
                                                                <div class="col-md-10">
                                                                    <textarea class="form-control" disabled>{{ $collection->title_ori }}</textarea>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (array_key_exists('title', $fields))
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Judul Perubahan :</label>
                                                                <div class="col-md-10">
                                                                    <textarea name="title" id="title" class="form-control" placeholder="Masukan judul"
                                                                        {{ $collection->lock ? 'disabled' : '' }}>{{ $collection->title }}</textarea>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (array_key_exists('code', $fields))
                                                            <div class="form-group row">
                                                                <label class="col-md-2">ISBN/ISSN :</label>
                                                                <div class="col-md-10">
                                                                    <input type="text" class="form-control"
                                                                        value="{{ $collection->code }}" disabled>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (array_key_exists('series', $fields))
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Seri :</label>
                                                                <div class="col-md-10">
                                                                    <input type="text" class="form-control"
                                                                        name="series"
                                                                        value="{{ $collection->series }}"
                                                                        placeholder="Masukan seri"
                                                                        {{ $collection->lock ? 'disabled' : '' }}>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (array_key_exists('edition', $fields))
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Edisi :</label>
                                                                <div class="col-md-10">
                                                                    <input type="text" class="form-control"
                                                                        name="edition"
                                                                        value="{{ $collection->edition }}"
                                                                        placeholder="Masukan edisi"
                                                                        {{ $collection->lock ? 'disabled' : '' }}>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (array_key_exists('publication_month', $fields))
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Bulan Terbit :</label>
                                                                <div class="col-md-5">
                                                                    <select name="publication_month"
                                                                        id="publication_month" class="form-control"
                                                                        {{ $collection->lock ? 'disabled' : '' }}>
                                                                        <option value="">-- Pilih --
                                                                        </option>
                                                                        <option value="01"
                                                                            {{ $collection->publication_month == '01' ? 'selected' : '' }}>
                                                                            {{ App\Helper\GeneralHelper::getMonth('01') }}
                                                                        </option>
                                                                        <option value="02"
                                                                            {{ $collection->publication_month == '02' ? 'selected' : '' }}>
                                                                            {{ App\Helper\GeneralHelper::getMonth('02') }}
                                                                        </option>
                                                                        <option value="03"
                                                                            {{ $collection->publication_month == '03' ? 'selected' : '' }}>
                                                                            {{ App\Helper\GeneralHelper::getMonth('03') }}
                                                                        </option>
                                                                        <option value="04"
                                                                            {{ $collection->publication_month == '04' ? 'selected' : '' }}>
                                                                            {{ App\Helper\GeneralHelper::getMonth('04') }}
                                                                        </option>
                                                                        <option value="05"
                                                                            {{ $collection->publication_month == '05' ? 'selected' : '' }}>
                                                                            {{ App\Helper\GeneralHelper::getMonth('05') }}
                                                                        </option>
                                                                        <option value="06"
                                                                            {{ $collection->publication_month == '06' ? 'selected' : '' }}>
                                                                            {{ App\Helper\GeneralHelper::getMonth('06') }}
                                                                        </option>
                                                                        <option value="07"
                                                                            {{ $collection->publication_month == '07' ? 'selected' : '' }}>
                                                                            {{ App\Helper\GeneralHelper::getMonth('07') }}
                                                                        </option>
                                                                        <option value="08"
                                                                            {{ $collection->publication_month == '08' ? 'selected' : '' }}>
                                                                            {{ App\Helper\GeneralHelper::getMonth('08') }}
                                                                        </option>
                                                                        <option value="09"
                                                                            {{ $collection->publication_month == '09' ? 'selected' : '' }}>
                                                                            {{ App\Helper\GeneralHelper::getMonth('09') }}
                                                                        </option>
                                                                        <option value="10"
                                                                            {{ $collection->publication_month == '10' ? 'selected' : '' }}>
                                                                            {{ App\Helper\GeneralHelper::getMonth('10') }}
                                                                        </option>
                                                                        <option value="11"
                                                                            {{ $collection->publication_month == '11' ? 'selected' : '' }}>
                                                                            {{ App\Helper\GeneralHelper::getMonth('11') }}
                                                                        </option>
                                                                        <option value="12"
                                                                            {{ $collection->publication_month == '12' ? 'selected' : '' }}>
                                                                            {{ App\Helper\GeneralHelper::getMonth('12') }}
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                @if (array_key_exists('publication_year', $fields))
                                                                    <label class="col-md-1">Tahun Terbit :</label>
                                                                    <div class="col-md-4">
                                                                        <input type="text" name="publication_year"
                                                                            id="publication_year" class="form-control"
                                                                            placeholder="Masukan tahun terbit"
                                                                            value="{{ $collection->publication_year }}"
                                                                            {{ $collection->lock ? 'disabled' : '' }}>
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
                                                                        <option
                                                                            {{ $collection->serial == '1' ? 'selected' : '' }}
                                                                            value="1">Harian</option>
                                                                        <option
                                                                            {{ $collection->serial == '2' ? 'selected' : '' }}
                                                                            value="2">Mingguan</option>
                                                                        <option
                                                                            {{ $collection->serial == '3' ? 'selected' : '' }}
                                                                            value="3">Bulanan</option>
                                                                        <option
                                                                            {{ $collection->serial == '4' ? 'selected' : '' }}
                                                                            value="4">3 Bulan Sekali</option>
                                                                        <option
                                                                            {{ $collection->serial == '5' ? 'selected' : '' }}
                                                                            value="5">4 Bulan Sekali</option>
                                                                        <option
                                                                            {{ $collection->serial == '6' ? 'selected' : '' }}
                                                                            value="6">6 Bulan Sekali</option>
                                                                        <option
                                                                            {{ $collection->serial == '7' ? 'selected' : '' }}
                                                                            value="7">Tahunan</option>
                                                                        <option
                                                                            {{ $collection->serial == '8' ? 'selected' : '' }}
                                                                            value="8">2 Tahun Sekali</option>
                                                                        <option
                                                                            {{ $collection->serial == '9' ? 'selected' : '' }}
                                                                            value="9">3 Tahun Sekali</option>
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
                                                                        value="{{ date('Y-m-d', strtotime($collection->received_at)) }}"
                                                                        max="{{ date('Y-m-d') }}"
                                                                        {{ $collection->lock ? 'disabled' : '' }}>
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
                                                                            placeholder="Masukan total halaman"
                                                                            value="{{ isset($collection->physicalDescription()->total_page) ? $collection->physicalDescription()->total_page : '' }}"
                                                                            {{ $collection->lock ? 'disabled' : '' }}>
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
                                                                            placeholder="Masukan dimensi"
                                                                            value="{{ isset($collection->physicalDescription()->dimension) ? $collection->physicalDescription()->dimension : '' }}"
                                                                            {{ $collection->lock ? 'disabled' : '' }}>
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
                                                                            value="{{ $collection->price }}"
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
                                                                        style="width:100%;" multiple
                                                                        {{ $collection->lock ? 'disabled' : '' }}>
                                                                        @foreach ($category as $c)
                                                                            @php $exist = $collection->collectionCategory->where('category_id', $c->id)->count() @endphp
                                                                            <option value="{{ $c->id }}"
                                                                                {{ $exist > 0 ? 'selected' : '' }}>
                                                                                {{ $c->name }}</option>
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
                                                                        style="width:100%;" multiple
                                                                        {{ $collection->lock ? 'disabled' : '' }}>
                                                                        @foreach ($collection->collectionSubject as $cs)
                                                                            <option value="{{ $cs->subject->name }}"
                                                                                selected>
                                                                                {{ $cs->subject->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (array_key_exists('description', $fields))
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Keterangan :</label>
                                                                <div class="col-md-10">
                                                                    <textarea name="description" id="description" class="form-control" style="resize:true;"
                                                                        placeholder="Masukan informasi lain" {{ $collection->lock ? 'disabled' : '' }}>{{ $collection->description }}</textarea>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        @if (array_key_exists('collection_contributor', $fields))
                                                            <h4 class="form-section">Kontributor</h4>
                                                            <p>
                                                            <table class="table table-bordered table-striped">
                                                                <tbody id="data_contributor">
                                                                    @if (sizeof($collection->collectionContributor) > 0)
                                                                        @foreach ($collection->collectionContributor as $cc)
                                                                            <tr>
                                                                                <td class="align-middle">
                                                                                    <select
                                                                                        name="contributor_contributor_id_field[]"
                                                                                        class="form-control select2">
                                                                                        @foreach ($contributor as $c)
                                                                                            <option
                                                                                                value="{{ $c->id }}"
                                                                                                {{ $c->id == $cc->contributor_id ? 'selected' : '' }}>
                                                                                                {{ $c->name }}
                                                                                            </option>
                                                                                        @endforeach
                                                                                    </select>
                                                                                </td>
                                                                                <td class="align-middle">
                                                                                    <select
                                                                                        onchange="validationContributor()"
                                                                                        name="contributor_fullname_field[]"
                                                                                        class="form-control author"
                                                                                        style="width:100%;">
                                                                                        <option selected
                                                                                            value="{{ $cc->author->fullname }}">
                                                                                            {{ $cc->author->fullname }}
                                                                                        </option>
                                                                                    </select>
                                                                                </td>
                                                                                <td class="align-middle">
                                                                                    <input type="text"
                                                                                        name="contributor_title_field[]"
                                                                                        class="form-control"
                                                                                        value="{{ $cc->author->title }}"
                                                                                        oninput="validationContributor()"
                                                                                        placeholder="Gelar">
                                                                                </td>
                                                                                <td class="align-middle">
                                                                                    <button type="button"
                                                                                        class="btn btn-danger btn-sm col-12"
                                                                                        id="remove_row_contributor"><i
                                                                                            class="la la-trash"></i></button>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr>
                                                                            <td class="align-middle">
                                                                                <select
                                                                                    name="contributor_contributor_id_field[]"
                                                                                    class="form-control select2">
                                                                                    @foreach ($contributor as $c)
                                                                                        <option
                                                                                            value="{{ $c->id }}">
                                                                                            {{ $c->name }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </td>
                                                                            <td class="align-middle">
                                                                                <select
                                                                                    onchange="validationContributor()"
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
                                                                                <button type="button"
                                                                                    class="btn btn-danger btn-sm col-12"
                                                                                    id="remove_row_contributor"><i
                                                                                        class="la la-trash"></i></button>
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                            <div class="form-group">
                                                                <button type="button"
                                                                    class="btn btn-success btn-sm col-12"
                                                                    onclick="addElementContributor()"><i
                                                                        class="la la-plus"></i></button>
                                                            </div>
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @if (array_key_exists('cover', $fields))
                                                <div class="tab-pane fade" id="pills-cover" role="tabpanel"
                                                    aria-labelledby="pills-cover-tab">
                                                    <h5>
                                                        Cover untuk Collection Utama
                                                    </h5>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            @if (array_key_exists('cover', $fields))
                                                                <div class="form-group">
                                                                    @php $cover = $collection->collectionMedia->where('type', 1)->first(); @endphp
                                                                    @if ($cover && Storage::disk($cover->location->location)->exists($cover->link))
                                                                        <div class="row justify-content-center">
                                                                            <div class="col-md-6">
                                                                                <div class="alert alert-warning alert-icon-left alert-arrow-left alert-dismissible mb-2"
                                                                                    role="alert">
                                                                                    <span class="alert-icon"><i
                                                                                            class="la la-info-circle"></i></span>
                                                                                    <ul>
                                                                                        <li>Ukuran:
                                                                                            <b>{{ App\Helper\GeneralHelper::formatSize($cover->size) }}</b>
                                                                                        </li>
                                                                                        <li>Ekstensi:
                                                                                            <b>{{ $cover->extension }}</b>
                                                                                        </li>
                                                                                        <li>Mime:
                                                                                            <b>{{ $cover->mimes }}</b>
                                                                                        </li>
                                                                                        <li>Hash:
                                                                                            <b>{{ $cover->hash }}</b>
                                                                                        </li>
                                                                                        <li>Metode:
                                                                                            <b>{{ $cover->method() }}</b>
                                                                                        </li>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <center>
                                                                            <a href="{{ url('collection/cover') . '/' . $cover->id }}"
                                                                                data-lightbox="Cover Collection"
                                                                                data-title="{{ $collection->title }}">
                                                                                <img src="{{ url('collection/cover') . '/' . $cover->id }}"
                                                                                    style="max-height:280px; max-width:242px;">
                                                                            </a>
                                                                        </center>
                                                                    @else
                                                                        <div class="alert alert-danger text-center">
                                                                            Tidak ada file!</div>
                                                                    @endif
                                                                    <div class="row justify-content-center mt-2">
                                                                        <div class="col-md-6">
                                                                            <input type="file" name="cover"
                                                                                class="form-control"
                                                                                {{ $collection->lock ? 'disabled' : '' }}>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
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
                                                                            <th>No</th>
                                                                            <th>Edisi</th>
                                                                            <th>Publication Date</th>
                                                                            <th>Total Copy</th>
                                                                            <th>Cover</th>
                                                                            <th>Karantina</th>
                                                                            <th>Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    </p>
                                                </div>
                                            @endif
                                            @if (array_key_exists('collection_copy', $fields))
                                                <div class="tab-pane fade" id="pills-exemplar"
                                                    aria-labelledby="pills-exemplar-tab">
                                                    <p>
                                                        @if (array_key_exists('collection_edition', $fields))
                                                            <div class="form-group row">
                                                                <label class="col-md-2">Edisi Serial :</label>
                                                                <div class="col-md-10">
                                                                    <select name="copy_edition_id"
                                                                        id="copy_edition_id"
                                                                        class="form-control select2"
                                                                        style="width:100%;">
                                                                        <option></option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    <div class="form-group row">
                                                        <label class="col-md-2">Jumlah Eksemplar :</label>
                                                        <div class="col-md-10">
                                                            <input type="number" name="copy_total" id="copy_total"
                                                                class="form-control"
                                                                placeholder="Masukan Jumlah Eksemplar"
                                                                value="{{ session('library_id') == 1 && $kategori_deposit == 'KC' ? 2 : 1 }}"
                                                                style="width:100%;">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-md-2">Tanggal Terima :</label>
                                                        <div class="col-md-10">
                                                            <input type="date" name="copy_received_date"
                                                                id="copy_received_date" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-md-2">Kondisi :</label>
                                                        <div class="col-md-10">
                                                            <select name="copy_condition" id="copy_condition"
                                                                class="form-control" style="width:100%;">
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
                                                            <select name="copy_availability" id="copy_availability"
                                                                class="form-control select2" style="width:100%;">
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
                                                            <select name="copy_lib_loc_id" id="copy_lib_loc_id"
                                                                class="form-control select2" style="width:100%;">
                                                                <option></option>
                                                                @foreach ($lib_loc as $key => $value)
                                                                    <option value="{{ $value->id }}">
                                                                        {{ $value->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="text-right row">
                                                            <button type="button"
                                                                class="btn btn-success btn-md col-12"
                                                                onclick="addElementCopies()">Tambahkan
                                                                Eksemplar</button>
                                                        </div>
                                                    </div>


                                                    <table class="table table-bordered table-striped"
                                                        id="datatable_copies">
                                                        <thead>
                                                            <tr>
                                                                <th style="width:5%">No</th>
                                                                <th style="width:10%">Code</th>
                                                                <th style="width:10%">Tgl Terima</th>
                                                                <th style="width:5%">Kondisi</th>
                                                                @if (array_key_exists('collection_edition', $fields))
                                                                    <th style="width:20%">Edisi Serial</th>
                                                                @endif
                                                                <th style="width:10%">Harga</th>
                                                                <th style="width:10%">Ketersediaan</th>
                                                                <th style="width:15%">Lokasi Perpustakaan</th>
                                                                <th style="width:15%">Lokasi Penyimpanan</th>
                                                                <th style="width:5%">Karantina</th>
                                                                <th style="width:5%">Action</th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                    </p>
                                                    <div class="form-group row">
                                                        <label class="col-md-2">Total Exemplar/Copy :</label>
                                                        <div class="col-md-10">
                                                            <input readonly type="text" name="total_copy"
                                                                id="total_copy" class="form-control"
                                                                placeholder="Total Copy">
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="copies_field" id="copies_field">
                                                </div>
                                            @endif
                                            @if (array_key_exists('collection_media', $fields))
                                                <div class="tab-pane fade" id="pills-media"
                                                    aria-labelledby="pills-media-tab">
                                                    <p>
                                                        @php
                                                            $original = $collection->collectionMedia->where('type', 2)->first();
                                                        @endphp
                                                        @if ($original)
                                                            @if ($original->extension == 'pdf')
                                                                <center>
                                                                    <div id="carouselExampleControls"
                                                                        class="carousel slide" data-ride="carousel">
                                                                        <p>Halaman <span id="lblHal"></span> dari
                                                                            <span id="lblTotal"></span>
                                                                        <div class="form-group">
                                                                            <a class="btn btn-primary btn-sm"
                                                                                href="#" onclick="prev()">
                                                                                << </a>
                                                                                    <input type="number"
                                                                                        name="key_carousel"
                                                                                        onchange="loadPdfImage()"
                                                                                        min="0" value="1"
                                                                                        id="key_carousel"> / <sub
                                                                                        id="total_data_image_pdf"></sub>
                                                                                    <a href="#"
                                                                                        class="btn btn-success btn-sm"
                                                                                        onclick="next()">>></a>
                                                                        </div>
                                                                        <div class="carousel-inner">
                                                                            <div id="carouselExampleControls"
                                                                                class="carousel slide"
                                                                                data-ride="carousel">
                                                                                <div class="carousel-inner">
                                                                                    <div class="carousel-item active">
                                                                                        <a href=""
                                                                                            id="lightbox_image_pdf"
                                                                                            data-lightbox="PDF"
                                                                                            data-title="Preview PDF"><img
                                                                                                class="d-block w-100"
                                                                                                src=""
                                                                                                id="data_image_pdf"
                                                                                                style="max-height:903px; width:100%;"></a>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <a class="btn btn-primary btn-sm"
                                                                            href="#" onclick="prev()">
                                                                            << </a>
                                                                                <a href="#"
                                                                                    class="btn btn-success btn-sm"
                                                                                    onclick="next()">>></a>
                                                                    </div>
                                                                </center>
                                                            @elseif($original->extension == 'epub')
                                                                <script src="/theme_admin/assets/js/jszip.min.js"></script>
                                                                <script src="/theme_admin/assets/js/epub.min.js"></script>
                                                                <select id="toc"></select>
                                                                <div id="epub-area" class="spreads"
                                                                    style="height:903px;"></div>
                                                                <a id="prev" href="#prev"
                                                                    class="arrow btn btn-success">‹</a>
                                                                <a id="next" href="#next"
                                                                    class="arrow btn btn-info">›</a>
                                                            @elseif($original->extension == 'mp3')
                                                                <div class="alert alert-danger text-center font-weight-bold" style="height:903px;">
                                                                    <span style="line-height:903px;">File MP3 belum di support</span>
                                                                </div>
                                                            @else
                                                                <div class="alert alert-danger text-center font-weight-bold"
                                                                    style="height:903px;">
                                                                    <span style="line-height:903px;">Tidak ada
                                                                        file!</span>
                                                                </div>
                                                            @endif

                                                            <div class="alert alert-warning alert-icon-left alert-arrow-left alert-dismissible mb-2"
                                                                role="alert">
                                                                <span class="alert-icon"><i
                                                                        class="la la-info-circle"></i></span>
                                                                <ul>
                                                                    <li>Ukuran:
                                                                        <b>{{ App\Helper\GeneralHelper::formatSize($original->size) }}</b>
                                                                    </li>
                                                                    <li>Ekstensi: <b>{{ $original->extension }}</b>
                                                                    </li>
                                                                    <li>Mime: <b>{{ $original->mimes }}</b></li>
                                                                    <li>Hash: <b>{{ $original->hash }}</b></li>
                                                                    <li>Metode: <b>{{ $original->method() }}</b></li>
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    <div class="alert alert-warning">
                                                        <small>
                                                            Jenis File Yang di Dukung <b>: PDF</b><br>
                                                            Maksimal Ukuran File <b>: 500 MB</b>
                                                        </small>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="file" class="file-content form-control-lg"
                                                            name="original" id="original" data-theme="fa5">
                                                    </div>
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                        @php
                                            $showBtnCancelUpdate = true;

                                            if($collection->edit_by) {
                                                if($collection->edit_by == session('id')) {
                                                    $showBtnCancelUpdate = false;
                                                }
                                            }
                                        @endphp

                                        <div class="form-group">
                                            <hr>
                                        </div>
                                        <div class="form-group">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <ul id="validation_contributor"
                                                            class="text-danger font-italic"></ul>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="text-right">
                                                            @if ($access_lock > 0)
                                                                <fieldset class="checkbox">
                                                                    <label>
                                                                        <input type="checkbox" name="lock"
                                                                            onchange="formUrl()"
                                                                            value="{{ $collection->lock }}"
                                                                            {{ $collection->lock ? 'checked' : '' }}>
                                                                        Kunci
                                                                    </label>
                                                                </fieldset>
                                                            @endif
                                                            @if($showBtnCancelUpdate)
                                                                <button type="submit" name="cancel" value="cancel" class="btn btn-secondary">Batal Edit</button>
                                                            @endif
                                                            <button type="reset" class="btn btn-danger" {{ $collection->lock ? 'disabled' : '' }}>Reset</button>
                                                            <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
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

<div class="modal animated bounceInRight text-left" id="modal_copies" data-backdrop="static" role="dialog"
    aria-labelledby="myModalLabel49" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel49">Form</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" id="validasi_element_copies" style="display:none;">
                    <ul id="validasi_content_copies"></ul>
                </div>
                <form action="" id="form_data_copies">
                    @if (array_key_exists('collection_edition', $fields))
                        <div class="form-group row">
                            <label class="col-md-2">Edisi Serial :</label>
                            <div class="col-md-10">
                                <select name="collection_id" id="modal_copy_edition_id" class="form-control select2"
                                    style="width:100%;"></select>
                            </div>
                        </div>
                    @endif
                    <div class="form-group row">
                        <label class="col-md-2">Kondisi :</label>
                        <div class="col-md-10">
                            <select name="condition" id="modal_copy_condition" class="form-control"
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
                            <select name="availability" id="modal_copy_availability" class="form-control select2"
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
                            <select name="lib_loc_id" id="modal_copy_lib_loc_id" class="form-control select2"
                                style="width:100%;">
                                <option></option>
                                @foreach ($lib_loc as $key => $value)
                                    <option value="{{ $value->id }}">
                                        {{ $value->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn grey btn-secondary" data-dismiss="modal">Tutup</button>
                {{-- <button type="button" class="btn btn-danger" onclick="cancel()" id="btn_cancel_copies"
                    style="diplay:none;">Batal</button> --}}
                <button type="button" class="btn btn-warning" onclick="updateCopies()"
                    id="btn_update_copies">Simpan
                    Perubahan</button>
            </div>
        </div>
    </div>
</div>

@if (array_key_exists('collection_edition', $fields))
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
                        <input id="id_field" type="hidden" value="">
                        <div class="form-group">
                            <label>Edisi / Volume :</label>
                            <input type="text" name="edition_field" id="edition_field" class="form-control"
                                placeholder="Masukan Edisi">
                        </div>
                        <div class="form-group">
                            <label>Tanggal Terbit Edisi / Volume :</label>
                            <input type="text" name="publication_date_field" id="publication_date_field"
                                class="form-control" />
                        </div>
                        <div class="form-group" id="cover_field_wrapper">
                            <label>Cover :</label>
                            <div class="input-group">
                                <input type="file" name="cover_field" id="cover_field" class="form-control">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-danger remove_cover" type="button"
                                        onclick="removeCoverEdition()">Remove Cover</button>
                                    <input id="temp_cover" type="hidden" name="temp_cover">
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="media_field_wrapper">
                            <label>Konten Digital :</label>
                            <div class="input-group">
                                <input type="file" name="media_field" id="media_field" class="form-control">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-danger remove_file" type="button"
                                        onclick="removeFileEdition()">Remove File</button>
                                    <input id="temp_file" type="hidden" name="temp_file">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <hr>
                        </div>
                        <div id="eksemplar_edition">
                            <div class="form-group ">
                                <label>Jumlah Eksemplar :</label>
                                <input type="number" name="copy_total" id="copy_total" class="form-control"
                                    placeholder="Masukan Jumlah Eksemplar"
                                    value="{{ session('library_id') == 1 && $kategori_deposit == 'KC' ? 2 : 1 }}"
                                    style="width:100%;">
                            </div>
                            <div class="form-group">
                                <label>Tanggal Terima :</label>
                                <input type="date" name="copy_received_date" id="copy_received_date"
                                    class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Kondisi :</label>
                                <select name="copy_condition" id="copy_condition" class="form-control"
                                    style="width:100%;">
                                    @foreach ($col_conditions as $id => $name)
                                        <option value="{{ $id }}">
                                            {{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Ketersediaan :</label>
                                <select name="copy_availability" id="copy_availability" class="form-control select2"
                                    style="width:100%;">
                                    <option></option>
                                    @foreach ($availability as $key => $value)
                                        <option value="{{ $key }}">
                                            {{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Lokasi :</label>
                                <select name="copy_lib_loc_id" id="copy_lib_loc_id" class="form-control select2"
                                    style="width:100%;">
                                    <option></option>
                                    @foreach ($lib_loc as $key => $value)
                                        <option value="{{ $value->id }}">
                                            {{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="resetEditionModal()"
                        data-dismiss="modal">Close</button>
                    <button id="btn_create_editions" type="button" style="display:none" class="btn btn-primary"
                        onclick="addEdition()">Tambah</button>
                    <button id="btn_update_editions" type="button" style="display:none" class="btn btn-primary"
                        onclick="updateEditions()">Ubah</button>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
    $(function() {
        @if (!array_key_exists('serial', $fields))
            loadDataTableCopies('{{ $collection_id }}');
        @else
            loadDataTableEditions('{{ $collection_id }}');
            loadDataTableCopies('{{ $collection_id }}', 'all');
            select2AutoSuggest('#copy_edition_id', 'load_edition/{{ $collection_id }}');
        @endif

        $('#data_contributor').on('click', '#remove_row_contributor', function() {
            $(this).closest('tr').remove();
        });

        select2AutoSuggest('#publisher_id', 'load_publisher');
        select2AutoSuggestTags('#collection_subject', 'load_subject');
        select2AutoSuggest('.author', 'load_author_manage');

        $('#publication_date_field').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD',
                showDropdowns: true,
                minYear: 1901,
                maxYear: parseInt(moment().format('YYYY'), 10)
            }
        });
    });


    //resize the datatable on change tabs
    $('a[data-toggle="pill"]').on('shown.bs.tab', function(e) {
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    });

    //onchange edition serial
    $(document).on("change", "#copy_edition_id", function() {
        var edition_id = $(this).val();
        if (edition_id == '') {
            loadDataTableCopies('{{ $collection_id }}', 'all');
        } else {
            loadDataTableCopies(edition_id);
        }

        // validationCopy();
    });

    function formUrl() {
        var locked = '{{ $collection->lock }}';
        var locked_url = '{{ $locked_url }}';

        if (locked == 1) {
            $('.form').attr('action', locked_url);
        } else {
            if ($('input[name="lock"]').prop('checked')) {
                $('.form').attr('action', locked_url);
            } else {
                $('.form').removeAttr('action');
            }
        }
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


    function loadDataTableCopies(collection_id, type = 'notall') {
        $('#datatable_copies').DataTable({
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
                url: '{{ url('admin/collection/kckra/datatable_copies') }}' + '/' + collection_id + '/' +
                    type,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataSrc: function(json) {
                    if (json.recordsValid) {
                        $("#total_copy").val(json.recordsValid);
                    } else {
                        $("#total_copy").val(0);
                    }
                    return json.data;
                }
            },
        });

    }

    function loadDataTableEditions(collection_id) {
        $('#datatable_edition').DataTable({
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
                url: '{{ url('admin/collection/kckra/datatable_editions') }}' + '/' + collection_id,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
        });

    }

    function toUpdateCopies() {
        $('#modal_copies').modal('show');
        // $('#btn_cancel_copies').show();
        $('#btn_update_copies').show();
    }

    function showCopies(id) {
        toUpdateCopies();
        $.ajax({
            url: '{{ url('admin/collection/kckra/show_copies') }}' + '/' + id,
            type: 'GET',
            dataType: 'JSON',
            beforeSend: function() {
                loadingOpen('.modal-content');
                $('#validasi_element_copies').hide();
                $('#validasi_content_copies').html('');
            },
            success: function(response) {
                loadingClose('.modal-content');
                @if (array_key_exists('collection_edition', $fields))
                    $('#modal_copy_edition_id').val(response.collection_id).trigger('change');
                @endif
                $('#modal_copy_condition').val(response.condition);
                $('#modal_copy_availability').val(response.availability).trigger('change');
                $('#modal_copy_lib_loc_id').val(response.lib_loc_id).trigger('change');

                $('#btn_update_copies').attr('onclick', 'updateCopies(' + id + ')');
            },
            error: function() {
                loadingClose('.modal-content');
                // cancel();
                Toast.fire({
                    icon: 'error',
                    title: 'Server Error!'
                });
            }
        })
    }

    function updateCopies(id) {
        $.ajax({
            url: '{{ url('admin/collection/kckra/update_copies') }}' + '/' + id,
            type: 'POST',
            dataType: 'JSON',
            data: $('#form_data_copies').serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                loadingOpen('.modal-content');
                $('#validasi_element_copies').hide();
                $('#validasi_content_copies').html('');
            },
            success: function(response) {
                loadingClose('.modal-content');
                if (response.status == 200) {
                    $('#datatable_copies').DataTable().ajax.reload(null, false);
                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });
                    $('#modal_copies').modal('hide');
                } else if (response.status == 422) {
                    $('#validasi_element_copies').show();
                    Toast.fire({
                        icon: 'info',
                        title: 'Validasi'
                    });

                    $.each(response.error, function(i, val) {
                        $('#validasi_content_copies').append('<li>' + val + '</li>');
                    })
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

    function destroyCopies(id) {
        $.ajax({
            url: '{{ url('admin/collection/kckra/karantina_copies') }}' + '/' + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                loadingOpen('#configuration');
                $('#validasi_element_copies').hide();
                $('#validasi_content_copies').html('');
            },
            success: function(response) {
                loadingClose('#configuration');
                $('#datatable_copies').DataTable().ajax.reload(null, false);

                @if (array_key_exists('serial', $fields))
                    $('#datatable_edition').DataTable().ajax.reload(null, false);
                @endif

                Toast.fire({
                    icon: 'success',
                    title: response.message
                });
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

    function addElementCopies() {
        var edition_id = null;
        var edition = null;
        var copy_total = $("#copy_total").val();
        var received_date = $("#copy_received_date").val();
        var condition_id = $("#copy_condition").val();
        var condition = $('#copy_condition').find('option:selected').text().replace(/\s+/g, ' ').trim();
        if ($('#copy_edition_id').length) {
            if ($("#copy_edition_id").val()) {
                edition_id = $("#copy_edition_id").select2('data')[0].id;
                edition = $("#copy_edition_id").find(':selected').text().replace(/\s+/g, ' ').trim();
            } else {
                alert('Mohon pastikan anda sudah memilih edisi sebelum menambahkan eksemplar!');
                return false;
            }
        }

        var price = $("#copy_price").val();
        var availability_id = $("#copy_availability").select2('data')[0].id;
        var availability = $("#copy_availability").find(':selected').text().replace(/\s+/g, ' ').trim();
        var library_id = `{{ $library->id }}`;
        var library = `{{ $library->name }}`;
        var lib_loc_id = $("#copy_lib_loc_id").select2('data')[0].id;
        var lib_loc = $("#copy_lib_loc_id").find(':selected').text().replace(/\s+/g, ' ').trim();
        var quarantine = null;
        var data = [];
        var datatable = [];
        loadingOpen('#configuration');
        for (let i = 0; i < copy_total; i++) {
            var row = parseInt($("#total_copy").val());
            var no = row + 1;
            data.push({
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
            });

        }

        createCopies(data);

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


    function createCopies(data) {
        // Perform the AJAX POST request
        $.ajax({
            url: '{{ url('admin/collection/kckra/create_copies') . '/' . $collection_id }}',
            method: "POST",
            data: {
                data: data
            },
            dataType: "json",
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
                    //reset the exemplar fields
                    $('#datatable_copies').DataTable().ajax.reload(null, false);
                    @if (array_key_exists('serial', $fields))
                        $('#datatable_edition').DataTable().ajax.reload(null, false);
                    @endif
                } else {
                    Toast.fire({
                        icon: 'warning',
                        title: response.message
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            }
        });
    }

    function showEditions(id) {
        openEditionModal();
        resetEditionModal();
        $.ajax({
            url: '{{ url('admin/collection/kckra/show_editions') }}' + '/' + id,
            type: 'GET',
            dataType: 'JSON',
            beforeSend: function() {
                loadingOpen('.modal-content');
                $('#validasi_element_editions').hide();
                $('#validasi_content_editions').html('');
            },
            success: function(response) {
                loadingClose('.modal-content');
                $('#edition_field').val(response.edition);
                $('#publication_date_field').data('daterangepicker').setStartDate(
                    response.start_publication_date
                );
                $('#publication_date_field').data('daterangepicker').setEndDate(
                    response.end_publication_date
                );

                $(".remove_cover").hide()
                $(".remove_file").hide()

                if (response.cover) {
                    $(".remove_cover").show();
                    $("#temp_cover").val(response.cover);
                }

                if (response.file) {
                    $(".remove_file").show();
                    $("#temp_file").val(response.file);
                }


                $('#btn_update_editions').attr('onclick', 'updateEditions(' + id + ')');
            },
            error: function() {
                loadingClose('.modal-content');
                // cancel();
                Toast.fire({
                    icon: 'error',
                    title: 'Server Error!'
                });
            }
        })
    }

    function resetEditionModal() {
        $('#modal_edition').modal('hide');
        $('#edition_field').val('');
        $('#publication_date_field').val('');
        $('#media_field').val('');
        $('#cover_field').val('');
        $('#old_data').remove();
        $('#temp_cover').val('');
        $('#temp_file').val('');
    }

    function removeCoverEdition() {
        $("#old_data").remove();
        $("#temp_cover").val('');
        $(".remove_cover").hide();
    }

    function removeFileEdition() {
        $("#old_data").remove();
        $("#temp_file").val('');
        $(".remove_file").hide();
    }

    function openEditionModal(param) {
        if (param == 'add') {
            $(".remove_cover").hide()
            $(".remove_file").hide()
            $("#edition_id_temp").val('new');
            $('#btn_create_editions').show();
            $('#btn_update_editions').hide();
            $('#eksemplar_edition').show();
            $('#modal_edition').modal('show');
        } else {
            $("#edition_id_temp").val(param);
            $('#btn_update_editions').show();
            $('#btn_create_editions').hide();
            $('#eksemplar_edition').hide();
            $('#modal_edition').modal('show');
        }
    }

    function addEdition() {
        var edition_field = $('#edition_field').val();
        var date_field = $('#publication_date_field').val();
        var cover_field = $('#cover_field').val();

        if (!edition_field || !date_field) {
            Swal.fire('Harap mengisi field edition dan publication date!', '', 'warning');
        } else {
            console.log(new FormData($('#form_edition')[0]));
            $.ajax({
                url: '{{ url('admin/collection/kckra/create_editions') . '/' . $collection_id }}',
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
                    $('#datatable_edition').DataTable().ajax.reload(null, false);
                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });
                    resetEditionModal();
                }
            });
        }
    }

    function updateEditions(id) {
        $.ajax({
            url: '{{ url('admin/collection/kckra/update_editions') }}' + '/' + id,
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
                loadingOpen('.modal-content');
                $('#validasi_element_editions').hide();
                $('#validasi_content_editions').html('');
            },
            success: function(response) {
                loadingClose('.modal-content');
                if (response.status == 200) {
                    $('#datatable_edition').DataTable().ajax.reload(null, false);
                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });
                    $('#modal_edition').modal('hide');
                } else if (response.status == 422) {
                    $('#validasi_element_editions').show();
                    Toast.fire({
                        icon: 'info',
                        title: 'Validasi'
                    });

                    $.each(response.error, function(i, val) {
                        $('#validasi_content_editions').append('<li>' + val + '</li>');
                    })
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

    function destroyEditions(id) {
        $.ajax({
            url: '{{ url('admin/collection/kckra/karantina_editions') }}' + '/' + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                loadingOpen('#configuration');
                $('#validasi_element_editions').hide();
                $('#validasi_content_editions').html('');
            },
            success: function(response) {
                loadingClose('#configuration');
                $('#datatable_edition').DataTable().ajax.reload(null, false);

                Toast.fire({
                    icon: 'success',
                    title: response.message
                });
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
