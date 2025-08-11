<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <h3 class="content-header-title mb-1 d-inline-block">Edit Pengelolaan Buku</h3><br>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Buku</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('admin/collection/manage/1') }}">Pengelolaan</a>
                            </li>
                            <li class="breadcrumb-item active">Edit</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-header-right col-md-6 col-12 mb-2 mt-1">
                <div class="float-md-right">
                    <a href="{{ url('admin/collection/create_manual/1') }}" class="btn btn-primary">Tambah Data Baru</a>
                    <a href="{{ url('admin/collection/manage/1') }}" class="btn btn-secondary">Kembali</a>
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
                                        <h4 class="form-section">Meta Data</h4>
                                        <p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <label class="col-md-2">Penerbit :</label>
                                                    <div class="col-md-10">
                                                        <select name="publisher_id" id="publisher_id"
                                                            class="form-control" style="width:100%;"
                                                            {{ $collection->lock ? 'disabled' : '' }}>
                                                            <option value="{{ $collection->publisher->id }}" selected>
                                                                {{ $collection->publisher->name }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Judul Asli :</label>
                                                    <div class="col-md-10">
                                                        <textarea class="form-control" disabled>{{ $collection->title_ori }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Judul Perubahan :</label>
                                                    <div class="col-md-10">
                                                        <textarea name="title" id="title" class="form-control" placeholder="Masukan judul"
                                                            {{ $collection->lock ? 'disabled' : '' }}>{{ $collection->title }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">ISBN :</label>
                                                    <div class="col-md-10">
                                                        <input type="text" class="form-control"
                                                            value="{{ $collection->code }}" disabled>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Seri :</label>
                                                    <div class="col-md-10">
                                                        <input type="text" class="form-control" name="series"
                                                            value="{{ $collection->series }}"
                                                            placeholder="Masukan seri"
                                                            {{ $collection->lock ? 'disabled' : '' }}>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Edisi :</label>
                                                    <div class="col-md-10">
                                                        <input type="text" class="form-control" name="edition"
                                                            value="{{ $collection->edition }}"
                                                            placeholder="Masukan edisi"
                                                            {{ $collection->lock ? 'disabled' : '' }}>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Preview :</label>
                                                    <div class="col-md-10">
                                                        <input type="text" class="form-control"
                                                            placeholder="Masukan preview"
                                                            value="{{ $collection->preview }}" disabled>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Bulan Terbit :</label>
                                                    <div class="col-md-10">
                                                        <select name="publication_month" id="publication_month"
                                                            class="form-control"
                                                            {{ $collection->lock ? 'disabled' : '' }}>
                                                            <option value="">-- Pilih --</option>
                                                            <option value="01"
                                                                {{ $collection->publication_month == '01' ? 'selected' : '' }}>
                                                                {{ App\Helper\GeneralHelper::getMonth('01') }}</option>
                                                            <option value="02"
                                                                {{ $collection->publication_month == '02' ? 'selected' : '' }}>
                                                                {{ App\Helper\GeneralHelper::getMonth('02') }}</option>
                                                            <option value="03"
                                                                {{ $collection->publication_month == '03' ? 'selected' : '' }}>
                                                                {{ App\Helper\GeneralHelper::getMonth('03') }}</option>
                                                            <option value="04"
                                                                {{ $collection->publication_month == '04' ? 'selected' : '' }}>
                                                                {{ App\Helper\GeneralHelper::getMonth('04') }}</option>
                                                            <option value="05"
                                                                {{ $collection->publication_month == '05' ? 'selected' : '' }}>
                                                                {{ App\Helper\GeneralHelper::getMonth('05') }}</option>
                                                            <option value="06"
                                                                {{ $collection->publication_month == '06' ? 'selected' : '' }}>
                                                                {{ App\Helper\GeneralHelper::getMonth('06') }}</option>
                                                            <option value="07"
                                                                {{ $collection->publication_month == '07' ? 'selected' : '' }}>
                                                                {{ App\Helper\GeneralHelper::getMonth('07') }}</option>
                                                            <option value="08"
                                                                {{ $collection->publication_month == '08' ? 'selected' : '' }}>
                                                                {{ App\Helper\GeneralHelper::getMonth('08') }}</option>
                                                            <option value="09"
                                                                {{ $collection->publication_month == '09' ? 'selected' : '' }}>
                                                                {{ App\Helper\GeneralHelper::getMonth('09') }}</option>
                                                            <option value="10"
                                                                {{ $collection->publication_month == '10' ? 'selected' : '' }}>
                                                                {{ App\Helper\GeneralHelper::getMonth('10') }}</option>
                                                            <option value="11"
                                                                {{ $collection->publication_month == '11' ? 'selected' : '' }}>
                                                                {{ App\Helper\GeneralHelper::getMonth('11') }}</option>
                                                            <option value="12"
                                                                {{ $collection->publication_month == '12' ? 'selected' : '' }}>
                                                                {{ App\Helper\GeneralHelper::getMonth('12') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Tahun Terbit :</label>
                                                    <div class="col-md-10">
                                                        <input type="text" name="publication_year"
                                                            id="publication_year" class="form-control"
                                                            placeholder="Masukan tahun terbit"
                                                            value="{{ $collection->publication_year }}"
                                                            {{ $collection->lock ? 'disabled' : '' }}>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Tanggal Terima :</label>
                                                    <div class="col-md-10">
                                                        <input type="date" name="received_at" id="received_at"
                                                            class="form-control"
                                                            value="{{ date('Y-m-d', strtotime($collection->received_at)) }}"
                                                            max="{{ date('Y-m-d') }}"
                                                            {{ $collection->lock ? 'disabled' : '' }}>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Total Halaman :</label>
                                                    <div class="col-md-10">
                                                        <div class="input-group mb-2">
                                                            <input type="number" name="total_page" id="total_page"
                                                                class="form-control"
                                                                placeholder="Masukan total halaman"
                                                                value="{{ $collection->physicalDescription() ? $collection->physicalDescription()->total_page : '' }}"
                                                                {{ $collection->lock ? 'disabled' : '' }}>
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
                                                            <input type="number" name="dimension" id="dimension"
                                                                class="form-control" placeholder="Masukan dimensi"
                                                                value="{{ isset($collection->physicalDescription()->dimension) ? $collection->physicalDescription()->dimension : '' }}"
                                                                {{ $collection->lock ? 'disabled' : '' }}>
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text">Cm</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Ilustrasi :</label>
                                                    <div class="col-md-10">
                                                        <select name="ilustration" id="ilustration"
                                                            class="form-control"
                                                            {{ $collection->lock ? 'disabled' : '' }}>
                                                            <option value="">-- Pilih Ilustrasi --</option>
                                                            <option value="Ya"
                                                                {{ isset($collection->physicalDescription()->ilustration) && $collection->physicalDescription()->ilustration == 'Ya' ? 'selected' : '' }}>
                                                                Ya</option>
                                                            <option value="Tidak"
                                                                {{ isset($collection->physicalDescription()->ilustration) && $collection->physicalDescription()->ilustration == 'Tidak' ? 'selected' : '' }}>
                                                                Tidak</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Kategori :</label>
                                                    <div class="col-md-10">
                                                        <select name="collection_category[]" id="collection_category"
                                                            class="form-control select2" style="width:100%;" multiple
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
                                                <div class="form-group row">
                                                    <label class="col-md-2">Subjek :</label>
                                                    <div class="col-md-10">
                                                        <select name="collection_subject[]" id="collection_subject"
                                                            class="form-control" style="width:100%;" multiple
                                                            {{ $collection->lock ? 'disabled' : '' }}>
                                                            @foreach ($collection->collectionSubject as $cs)
                                                                <option value="{{ $cs->subject->name }}" selected>
                                                                    {{ $cs->subject->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-md-2">Keterangan :</label>
                                                    <div class="col-md-10">
                                                        <textarea name="description" id="description" class="form-control" style="resize:true;"
                                                            placeholder="Masukan informasi lain" {{ $collection->lock ? 'disabled' : '' }}>{{ $collection->description }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                @php
                                                    $original = $collection->collectionMedia->where('type', 2)->first();
                                                    $watermark = $collection->collectionMedia->where('type', 3)->first();
                                                @endphp
                                                @if (isset($original))
                                                    @if ($original->extension == 'pdf' && $watermark && count($watermark->jsonParse()) > 0)
                                                        <center>
                                                            <div id="carouselExampleControls" class="carousel slide"
                                                                data-ride="carousel">
                                                                <p>Halaman <span id="lblHal"></span> dari <span
                                                                        id="lblTotal"></span>
                                                                <div class="form-group">
                                                                    <a class="btn btn-primary btn-sm" href="#"
                                                                        onclick="prev()">
                                                                        <<< /a><input type="number"
                                                                                name="key_carousel"
                                                                                onchange="loadPdfImage()"
                                                                                min="0" value="1"
                                                                                id="key_carousel"> / <sub
                                                                                id="total_data_image_pdf"></sub> <a
                                                                                href="#"
                                                                                class="btn btn-success btn-sm"
                                                                                onclick="next()">>></a>
                                                                </div>
                                                                <div class="carousel-inner">
                                                                    <div id="carouselExampleControls"
                                                                        class="carousel slide" data-ride="carousel">
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
                                                                <a class="btn btn-primary btn-sm" href="#"
                                                                    onclick="prev()">
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
                                                        <div id="epub-area" class="spreads" style="height:903px;">
                                                        </div>
                                                        <a id="prev" href="#prev"
                                                            class="arrow btn btn-success">‹</a>
                                                        <a id="next" href="#next"
                                                            class="arrow btn btn-info">›</a>
                                                    @elseif($original->extension == 'mp3')
                                                        <div class="alert alert-danger text-center font-weight-bold"
                                                            style="height:903px;">
                                                            <span style="line-height:903px;">File MP3 belum di
                                                                support</span>
                                                        </div>
                                                    @else
                                                        <div class="alert alert-warning alert-icon-left alert-arrow-left alert-dismissible mb-2"
                                                            role="alert">
                                                            <span class="alert-icon"><i
                                                                    class="la la-info-circle"></i></span>
                                                            <ul>
                                                                <li>Ukuran:
                                                                    <b>{{ App\Helper\GeneralHelper::formatSize($original->size) }}</b>
                                                                </li>
                                                                <li>Ekstensi: <b>{{ $original->extension }}</b></li>
                                                                <li>Mime: <b>{{ $original->mimes }}</b></li>
                                                                <li>Hash: <b>{{ $original->hash }}</b></li>
                                                                <li>Metode: <b>{{ $original->method() }}</b></li>
                                                            </ul>
                                                        </div>
                                                    @endif
                                                @else
                                                    <div class="alert alert-danger text-center font-weight-bold"
                                                        style="height:903px;">
                                                        <span style="line-height:903px;">Tidak ada file!</span>
                                                    </div>
                                                @endif


                                            </div>
                                        </div>
                                        </p>
                                        <h4 class="form-section">Hak Akses</h4>
                                        <p>
                                        <div class="alert alert-light">
                                            <div class="form-check">
                                                <input type="radio" class="form-check-input" name="access"
                                                    id="access_1" value="1"
                                                    {{ $collection->access == 1 ? 'checked' : '' }}
                                                    {{ $collection->createdBy->userable_type == 'publishers' || $collection->lock ? 'disabled' : '' }}>
                                                <label class="form-check-label" for="access_1">
                                                    Akses full file berwatermak secara online
                                                </label>
                                            </div>
                                        </div>
                                        <div class="alert alert-light">
                                            <div class="form-check">
                                                <input type="radio" class="form-check-input" name="access"
                                                    id="access_2" value="2"
                                                    {{ $collection->access == 2 ? 'checked' : '' }}
                                                    {{ $collection->createdBy->userable_type == 'publishers' || $collection->lock ? 'disabled' : '' }}>
                                                <label class="form-check-label" for="access_2">
                                                    Akses hanya preview file secara online, namun tetap dapat di
                                                    dayagunakan di lingkungan perpustakaan nasional RI dengan jaringan
                                                    internet LAN
                                                </label>
                                            </div>
                                        </div>
                                        <div class="alert alert-light">
                                            <div class="form-check">
                                                <input type="radio" class="form-check-input" name="access"
                                                    id="access_3" value="3"
                                                    {{ $collection->access == 3 ? 'checked' : '' }}
                                                    {{ $collection->createdBy->userable_type == 'publishers' || $collection->lock ? 'disabled' : '' }}>
                                                <label class="form-check-label" for="access_3">
                                                    Akses hanya file preview secara online, dan tidak didayagunakan di
                                                    lingkungan Perpustakaan Nasional RI selama 5 tahun sejak diserahkan.
                                                    Setelah 5 tahun, akan didayagunakan oleh Perpustakaan Nasional RI di
                                                    jaringan internet LAN
                                                </label>
                                            </div>
                                        </div>
                                        <div class="alert alert-light">
                                            <div class="form-check">
                                                <input type="radio" class="form-check-input" name="access"
                                                    id="access_4" value="4"
                                                    {{ $collection->access == 4 ? 'checked' : '' }}
                                                    {{ $collection->createdBy->userable_type == 'publishers' || $collection->lock ? 'disabled' : '' }}>
                                                <label class="form-check-label" for="access_4">
                                                    Akses hanya file preview secara online selamanya dan tidak
                                                    didayagunakan di mana pun
                                                </label>
                                            </div>
                                        </div>
                                        </p>
                                        <h4 class="form-section">Kontributor</h4>
                                        <p>
                                        <table class="table table-bordered table-striped">
                                            <tbody id="data_contributor">
                                                @foreach ($collection->collectionContributor as $cc)
                                                    <tr>
                                                        <td class="align-middle">
                                                            <select name="contributor_contributor_id_field[]"
                                                                class="form-control select2">
                                                                @foreach ($contributor as $c)
                                                                    <option value="{{ $c->id }}"
                                                                        {{ $c->id == $cc->contributor_id ? 'selected' : '' }}>
                                                                        {{ $c->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td class="align-middle">
                                                            <input type="text" name="contributor_fullname_field[]"
                                                                class="form-control"
                                                                value="{{ $cc->author->fullname }}"
                                                                oninput="validationContributor()" placeholder="Nama">
                                                        </td>
                                                        <td class="align-middle">
                                                            <input type="text" name="contributor_title_field[]"
                                                                class="form-control" value="{{ $cc->author->title }}"
                                                                oninput="validationContributor()" placeholder="Gelar">
                                                        </td>
                                                        <td class="align-middle">
                                                            <input type="number"
                                                                name="contributor_year_of_birth_field[]"
                                                                class="form-control"
                                                                value="{{ $cc->author->year_of_birth }}"
                                                                placeholder="Thn. Lahir">
                                                        </td>
                                                        <td class="align-middle">
                                                            <input type="number"
                                                                name="contributor_year_of_death_field[]"
                                                                class="form-control"
                                                                value="{{ $cc->author->year_of_death }}"
                                                                placeholder="Thn. Mati">
                                                        </td>
                                                        <td class="align-middle">
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm col-12"
                                                                id="remove_row_contributor"><i
                                                                    class="la la-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="form-group">
                                            <button type="button" class="btn btn-success btn-sm col-12"
                                                onclick="addElementContributor()"><i class="la la-plus"></i></button>
                                        </div>
                                        </p>
                                        <h4 class="form-section">Cover</h4>
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
                                                                <li>Ekstensi: <b>{{ $cover->extension }}</b></li>
                                                                <li>Mime: <b>{{ $cover->mimes }}</b></li>
                                                                <li>Hash: <b>{{ $cover->hash }}</b></li>
                                                                <li>Metode: <b>{{ $cover->method() }}</b></li>
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
                                                <div class="alert alert-danger text-center">Tidak ada file!</div>
                                            @endif
                                            <div class="row justify-content-center mt-2">
                                                <div class="col-md-6">
                                                    <input type="file" name="cover" class="form-control"
                                                        {{ $collection->lock ? 'disabled' : '' }}>
                                                </div>
                                            </div>
                                        </div>
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
                                                            <button type="submit" name="cancel" value="cancel"
                                                                class="btn btn-secondary">Batal Edit</button>
                                                            <button type="reset" class="btn btn-danger"
                                                                {{ $collection->lock ? 'disabled' : '' }}>Reset</button>
                                                            <button type="submit" class="btn btn-warning">Simpan
                                                                Perubahan</button>
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

@if ($original && $original->extension == 'epub')
    <script>
        $(function() {
            var params = URLSearchParams && new URLSearchParams(document.location.search.substring(1));
            var url = params && params.get("url") && decodeURIComponent(params.get("url"));
            var currentSectionIndex = (params && params.get("loc")) ? params.get("loc") : undefined;
            var book = ePub(url || "{{ url('collection/file.epub') . '?id=' . $original->id }}");
            var rendition = book.renderTo("epub-area", {
                method: "default",
                width: "100%",
                height: "100%"
            });
            var displayed = rendition.display();
            book.ready.then(function() {

                var next = document.getElementById("next");

                next.addEventListener("click", function(e) {
                    book.package.metadata.direction === "rtl" ? rendition.prev() : rendition.next();
                    e.preventDefault();
                }, false);

                var prev = document.getElementById("prev");
                prev.addEventListener("click", function(e) {
                    book.package.metadata.direction === "rtl" ? rendition.next() : rendition.prev();
                    e.preventDefault();
                }, false);

                var keyListener = function(e) {

                    // Left Key
                    if ((e.keyCode || e.which) == 37) {
                        book.package.metadata.direction === "rtl" ? rendition.next() : rendition.prev();
                    }

                    // Right Key
                    if ((e.keyCode || e.which) == 39) {
                        book.package.metadata.direction === "rtl" ? rendition.prev() : rendition.next();
                    }

                };

                rendition.on("keyup", keyListener);
                document.addEventListener("keyup", keyListener, false);

            })

            var title = document.getElementById("title");

            rendition.on("rendered", function(section) {
                var current = book.navigation && book.navigation.get(section.href);

                if (current) {
                    var $select = document.getElementById("toc");
                    var $selected = $select.querySelector("option[selected]");
                    if ($selected) {
                        $selected.removeAttribute("selected");
                    }

                    var $options = $select.querySelectorAll("option");
                    for (var i = 0; i < $options.length; ++i) {
                        let selected = $options[i].getAttribute("ref") === current.href;
                        if (selected) {
                            $options[i].setAttribute("selected", "");
                        }
                    }
                }

            });

            rendition.on("relocated", function(location) {
                console.log(location);

                var next = book.package.metadata.direction === "rtl" ? document.getElementById("prev") :
                    document.getElementById("next");
                var prev = book.package.metadata.direction === "rtl" ? document.getElementById("next") :
                    document.getElementById("prev");

                if (location.atEnd) {
                    next.style.visibility = "hidden";
                } else {
                    next.style.visibility = "visible";
                }

                if (location.atStart) {
                    prev.style.visibility = "hidden";
                } else {
                    prev.style.visibility = "visible";
                }

            });

            rendition.on("layout", function(layout) {
                let viewer = document.getElementById("epub-area");

                if (layout.spread) {
                    viewer.classList.remove('single');
                } else {
                    viewer.classList.add('single');
                }
            });

            window.addEventListener("unload", function() {
                console.log("unloading");
                this.book.destroy();
            });

            book.loaded.navigation.then(function(toc) {
                var $select = document.getElementById("toc"),
                    docfrag = document.createDocumentFragment();

                toc.forEach(function(chapter) {
                    var option = document.createElement("option");
                    option.textContent = chapter.label;
                    option.setAttribute("ref", chapter.href);

                    docfrag.appendChild(option);
                });

                $select.appendChild(docfrag);

                $select.onchange = function() {
                    var index = $select.selectedIndex,
                        url = $select.options[index].getAttribute("ref");
                    rendition.display(url);
                    return false;
                };

            });
        });
    </script>
@endif
<script>
    $(function() {
        $('#data_contributor').on('click', '#remove_row_contributor', function() {
            $(this).closest('tr').remove();
        });

        $('#key_carousel').on('keypress', function(e) {
            if (e.which == 13) {
                loadPdfImage();
            }
        });

        select2AutoSuggest('#publisher_id', 'load_publisher');
        select2AutoSuggestTags('#collection_subject', 'load_subject');
        loadPdfImage();
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

        validationContributor();

        $('.select2').select2({
            placeholder: '-- Pilih --'
        });
    }

    function loadPdfImage(page = 1, nextprev = false) {
        if (!nextprev) {
            page = $('#key_carousel').val();
        }
        $.ajax({
            url: '{{ url('admin/collection/load_image_pdf') }}',
            type: 'GET',
            dataType: 'JSON',
            data: {
                collection_id: '{{ $collection->id }}',
                key: page
            },
            success: function(response) {
                $('#lightbox_image_pdf').attr('src', response.image);
                $('#data_image_pdf').attr('src', response.image);
                $('#total_data_image_pdf').html(response.total_data);
                $('#key_carousel').val(page);
                $('#lblHal').html(page);
                $('#lblTotal').html(response.total_data);
            },
            error: function() {
                false;
            }
        });
    }

    function next() {
        page = parseInt($('#key_carousel').val()) + 1;
        loadPdfImage(page, true);
    }

    function prev() {
        page = parseInt($('#key_carousel').val()) - 1;
        loadPdfImage(page, true);
    }
</script>
