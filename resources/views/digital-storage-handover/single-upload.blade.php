<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Serah Simpan Digital - <span class="fw-normal">Unggah Tunggal</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <span class="badge bg-primary bg-opacity-10 text-primary">
                    <i class="ph-upload me-1"></i>
                    Form Upload
                </span>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="alert alert-danger border-0 shadow-sm d-none" id="validation-element">
        <div class="d-flex align-items-start">
            <i class="ph-warning-circle me-2 mt-1"></i>
            <div class="flex-fill">
                <strong>Terdapat kesalahan validasi:</strong>
                <ul class="mb-0 mt-2" id="validation-data"></ul>
            </div>
        </div>
    </div>
    <form id="form-data">
        <input type="hidden" name="upload_id_cover" id="upload_id_cover" value="{{ $uploadIDCover }}">
        <input type="hidden" name="upload_id_content" id="upload_id_content" value="{{ $uploadIDContent }}">
        <input type="hidden" name="upload_id" id="upload_id" value="{{ $uploadID }}">
        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center">
                    <i class="ph-files me-2 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">
                        Jenis Bahan
                        <span class="text-danger">*</span>
                    </h6>
                </div>
            </div>
            <div class="card-body">
                <select class="form-select select2-basic" name="worksheet_id" id="worksheet_id" onchange="chooseWorksheet()" data-placeholder="Pilih Jenis Bahan">
                    <option value=""></option>
                    @foreach($worksheet as $w)
                        <option value="{{ $w->ID }}">{{ $w->NAME }} [{{ $w->CATEGORY }}]</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="card border-0 shadow-sm d-none" id="form-parent">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center">
                    <i class="ph-folder-open me-2 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Parent Catalog</h6>
                </div>
            </div>
            <div class="card-body">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="ph-magnifying-glass"></i>
                    </span>
                    <input type="hidden" name="catalog_id" id="catalog_id">
                    <input type="text" class="form-control" name="catalog_title" id="catalog_title" placeholder="Cari catalog parent" onchange="catalogParent()" readonly>
                    <button type="button" class="btn btn-danger d-none" onclick="onLoading('show', 'body'); location.reload(true);" id="btn-cancel-parent">
                        <i class="ph-x me-1"></i>
                        Batalkan
                    </button>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center">
                    <i class="ph-user-circle me-2 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">
                        Pelaksana Serah
                        <span class="text-danger">*</span>
                    </h6>
                </div>
            </div>
            <div class="card-body">
                <select class="form-select" name="executor_id" id="executor_id" data-placeholder="Pilih Pelaksana Serah"></select>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center">
                    <i class="ph-info me-2 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Meta Data Induk</h6>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-form-label col-md-2 fw-semibold">
                        <i class="ph-video-camera me-1"></i>
                        Media
                        <span class="text-danger">*</span>
                    </label>
                    <div class="col-md-10">
                        <select class="form-select select2-basic" name="collection_media_id" id="collection_media_id" data-placeholder="Pilih Media">
                            <option value=""></option>
                            @foreach($media as $m)
                                <option value="{{ $m->ID }}">{{ $m->NAME }} [{{ $m->DEPOSITFORMAT_CODE }}]</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2 fw-semibold">
                        <i class="ph-book me-1"></i>
                        Judul
                        <span class="text-danger">*</span>
                    </label>
                    <div class="col-md-10">
                        <textarea name="title" class="form-control" id="title" rows="3" placeholder="Masukkan judul lengkap"></textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2 fw-semibold">
                        <i class="ph-barcode me-1"></i>
                        Kode
                    </label>
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
                            <input type="text" class="form-control" name="code" id="code" placeholder="Masukkan kode">
                            <button type="button" class="btn btn-success" id="btn-check-isbn" onclick="checkISBNCode()">
                                <i class="ph-magnifying-glass me-1"></i>
                                Cek Kode
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2 fw-semibold">
                        <i class="ph-map-pin me-1"></i>
                        Kota
                        <span class="text-danger">*</span>
                    </label>
                    <div class="col-md-10">
                        <select class="form-select" name="city_id" id="city_id" data-placeholder="Pilih Kota"></select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2 fw-semibold">
                        <i class="ph-qr-code me-1"></i>
                        QRCBN
                    </label>
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text">
                                <label class="mb-0">
                                    <input type="checkbox" class="form-check-input mt-0 me-1" onchange="$(this).is(':checked') ? $('#qrcbn').attr('readonly', true) : $('#qrcbn').attr('readonly', false)" checked>
                                    Tidak Ada
                                </label>
                            </span>
                            <input type="text" class="form-control" name="qrcbn" id="qrcbn" placeholder="Masukkan QRCBN" readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2 fw-semibold">
                        <i class="ph-list-bullets me-1"></i>
                        Seri
                    </label>
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text">
                                <label class="mb-0">
                                    <input type="checkbox" class="form-check-input mt-0 me-1" id="series_checkbox" onchange="$(this).is(':checked') ? $('#series').attr('readonly', true) : $('#series').attr('readonly', false)" checked>
                                    Tidak Ada
                                </label>
                            </span>
                            <input type="text" class="form-control" name="series" id="series" placeholder="Masukkan seri" readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2 fw-semibold">
                        <i class="ph-clock me-1"></i>
                        Kala Terbit
                    </label>
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
                    <label class="col-form-label col-md-2 fw-semibold">
                        <i class="ph-calendar-check me-1"></i>
                        Waktu Terbit
                        <span class="text-danger">*</span>
                    </label>
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="ph-calendar-blank"></i>
                            </span>
                            <input type="text" class="form-control date-picker-single" name="publish_time" id="publish_time" placeholder="Pilih Tanggal">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2 fw-semibold">
                        <i class="ph-calendar-plus me-1"></i>
                        Tanggal Terima
                        <span class="text-danger">*</span>
                    </label>
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="ph-calendar-blank"></i>
                            </span>
                            <input type="text" class="form-control date-picker-single" name="received_at" id="received_at" placeholder="Pilih Tanggal">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2 fw-semibold">
                        <i class="ph-eye me-1"></i>
                        Preview
                        <span class="text-danger">*</span>
                    </label>
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="preview" id="preview" placeholder="cth : 1-5 / 00:01-00:20">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2 fw-semibold">
                        <i class="ph-lock-key me-1"></i>
                        Akses
                        <span class="text-danger">*</span>
                    </label>
                    <div class="col-md-10">
                        <select class="form-select select2-basic" name="access" id="access" data-placeholder="Pilih Jenis Akses">
                            <option value=""></option>
                            <option value="1">Akses full file berwatermak secara online</option>
                            <option value="2">Akses hanya preview file secara online, namun tetap dapat di dayagunakan di lingkungan perpustakaan nasional RI dengan jaringan internet LAN</option>
                            <option value="3">Akses hanya file preview secara online, dan tidak didayagunakan di lingkungan Perpustakaan Nasional RI selama 5 tahun sejak diserahkan. Setelah 5 tahun, akan didayagunakan oleh Perpustakaan Nasional RI di jaringan internet LAN</option>
                            <option value="4">Akses hanya file preview secara online selamanya dan tidak didayagunakan di mana pun</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2 fw-semibold">
                        <i class="ph-currency-circle-dollar me-1"></i>
                        Mata Uang
                    </label>
                    <div class="col-md-10">
                        <select class="form-select" name="currency" id="currency">
                            <option value="IDR" selected>IDR</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2 fw-semibold">
                        <i class="ph-tag me-1"></i>
                        Harga
                        <span class="text-danger">*</span>
                    </label>
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" name="price" id="price" placeholder="0">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2 fw-semibold">
                        <i class="ph-book-bookmark me-1"></i>
                        Jilid
                    </label>
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="binding" id="binding" placeholder="Masukkan jilid">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2 fw-semibold">
                        <i class="ph-file-text me-1"></i>
                        Jenis Isi
                    </label>
                    <div class="col-md-10">
                        <select class="form-select select2-basic" name="content_type" id="content_type" data-placeholder="Pilih Jenis Isi">
                            <option value=""></option>
                            @foreach($contentType as $ct)
                                <option value="{{ $ct->NAME }}" {{ $ct->NAME == 'teks' ? 'selected' : '' }}>{{ $ct->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2 fw-semibold">
                        <i class="ph-package me-1"></i>
                        Jenis Wadah
                    </label>
                    <div class="col-md-10">
                        <select class="form-select select2-basic" name="container_type" id="container_type" data-placeholder="Pilih Jenis Wadah">
                            <option value=""></option>
                            @foreach($containerType as $ct)
                                <option value="{{ $ct->NAME }}" {{ $ct->NAME == 'komputer' ? 'selected' : '' }}>{{ $ct->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2 fw-semibold">
                        <i class="ph-disc me-1"></i>
                        Jenis Media
                    </label>
                    <div class="col-md-10">
                        <select class="form-select select2-basic" name="media_type" id="media_type" data-placeholder="Pilih Jenis Media">
                            <option value=""></option>
                            @foreach($mediaType as $mt)
                                <option value="{{ $mt->NAME }}" {{ $mt->NAME == 'sumber daya sambung jaring' ? 'selected' : '' }}>{{ $mt->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2 fw-semibold">
                        <i class="ph-folders me-1"></i>
                        Kelas Besar
                    </label>
                    <div class="col-md-10">
                        <select class="form-select select2-basic" name="big_class_id" id="big_class_id" data-placeholder="Pilih Kelas Besar">
                            <option value=""></option>
                            @foreach($bigClass as $bc)
                                <option value="{{ $bc->ID }}">{{ $bc->CLASS }} - {{ $bc->DESCRIPTION }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2 fw-semibold">
                        <i class="ph-ruler me-1"></i>
                        Keterangan Fisik
                    </label>
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text">Total Halaman / Durasi</span>
                            <input type="number" class="form-control" name="physical_description[paging]" id="physical_description[paging]" placeholder="0">
                            <select class="form-select flex-grow-0 w-auto" name="physical_description[paging_flag]" id="physical_description[paging_flag]">
                                <option value="Halaman" selected>Halaman</option>
                                <option value="Menit">Menit</option>
                                <option value="Jam">Jam</option>
                            </select>
                        </div>
                        <div class="input-group mt-2">
                            <span class="input-group-text">Ilustrasi</span>
                            <input type="text" class="form-control" name="physical_description[ill]" list="suggestion-physical-description-ill" id="physical_description[ill]" placeholder="Pilih atau ketik" autocomplete="off">
                            <datalist id="suggestion-physical-description-ill">
                                <option value="Tidak Ada">Tidak Ada</option>
                                <option value="Ada (Berwarna)">Ada (Berwarna)</option>
                                <option value="Ada (Tidak Berwarna)">Ada (Tidak Berwarna)</option>
                            </datalist>
                        </div>
                        <div class="input-group mt-2">
                            <span class="input-group-text">Ukuran / Dimensi</span>
                            <input type="text" class="form-control" name="physical_description[sizes]" id="physical_description[sizes]" placeholder="Masukkan ukuran">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2 fw-semibold">
                        <i class="ph-note me-1"></i>
                        Sinopsis
                        <span class="text-danger">*</span>
                    </label>
                    <div class="col-md-10">
                        <textarea name="description" class="form-control" id="description" rows="4" placeholder="Masukkan sinopsis atau deskripsi"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center">
                    <i class="ph-tag me-2 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Kategori</h6>
                </div>
            </div>
            <div class="card-body">
                <select class="form-select select2-basic" name="category[]" id="category" data-placeholder="Pilih Kategori (bisa lebih dari satu)" multiple>
                    <option value=""></option>
                    @foreach($category as $c)
                        <option value="{{ $c->ID }}">{{ $c->NAME }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center">
                    <i class="ph-users me-2 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Kontributor <span class="text-danger">*</span></h6>
                </div>
            </div>
            <div class="card-body">
                <select class="form-select" name="author[]" id="author" data-placeholder="Ketik nama kontributor (pisahkan dengan titik koma)" multiple></select>
                <small class="form-text text-muted">
                    <i class="ph-info me-1"></i>
                    Ketik dan tekan Enter atau gunakan titik koma (;) untuk memisahkan kontributor (cth: Pengarang, Budi Santoso. S)
                </small>
            </div>
        </div>
        <div class="card border-0 shadow-sm" id="card-edition">
            <div class="card-header border-bottom">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="ph-books me-2 text-primary"></i>
                        <h6 class="mb-0 fw-semibold">Edisi Serial</h6>
                    </div>
                    <label class="mb-0">
                        <input type="checkbox" class="form-check-input mt-0 me-1" name="has_edition" onchange="toggleEditionSection()">
                        <span class="fw-semibold">Centang jika ada edisi baru</span>
                    </label>
                </div>
            </div>
            <div id="existing-editions" style="display:none;">
                <div class="card-body border-bottom">
                    <h6 class="fw-semibold text-muted">
                        <i class="ph-clock-counter-clockwise me-1"></i>
                        Edisi Serial yang Sudah Ada
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover w-100 display nowrap" id="table-existing-editions">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%">No</th>
                                    <th style="width: 15%">Edisi/Volume</th>
                                    <th style="width: 12%">Tgl Terbit</th>
                                    <th style="width: 20%">Judul Artikel</th>
                                    <th style="width: 15%">Kontributor</th>
                                    <th style="width: 10%">DOI</th>
                                    <th style="width: 10%">Files</th>
                                    <th style="width: 13%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div id="content-edition-copy" style="display:none;">
                <div class="card-body">
                    <div id="data-edition"></div>
                    <div class="alert alert-warning border-0 d-flex align-items-center" id="empty-edition-message">
                        <i class="ph-info me-2"></i>
                        <span>Belum ada edisi baru. Klik tombol "Tambah Edisi Serial" untuk menambahkan.</span>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row g-2 align-items-center">
                        <div class="col-auto">
                            <button type="button" class="btn btn-success" onclick="addEdition()">
                                <i class="ph-plus-circle me-1"></i>
                                Tambah Edisi Serial
                            </button>
                        </div>
                        <div class="col-auto">
                            <input type="number" class="form-control" id="add-number-edition" min="1" max="10" value="1" style="width: 80px;">
                        </div>
                        <div class="col-auto">
                            <span class="text-muted">
                                <i class="ph-info me-1"></i>
                                Maksimal 10 edisi sekaligus
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalEditionDetail" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="ph-newspaper me-2"></i>
                            Detail Edisi Serial
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="modalEditionDetailContent"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="ph-x me-1"></i>
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @if(!$uploadIDCover && !$uploadIDContent && !$uploadID)
            <div class="row g-3">
                <div class="col-md-6" id="section-file-cover">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header border-bottom">
                            <div class="d-flex align-items-center">
                                <i class="ph-image me-2 text-primary"></i>
                                <h6 class="mb-0 fw-semibold">
                                    File Cover
                                    <span class="text-danger">*</span>
                                </h6>
                            </div>
                        </div>
                        <div class="card-body">
                            <input type="file" name="file_cover" id="file_cover">
                        </div>
                    </div>
                </div>
                <div class="col-md-6" id="section-file-content">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header border-bottom">
                            <div class="d-flex align-items-center">
                                <i class="ph-file-pdf me-2 text-primary"></i>
                                <h6 class="mb-0 fw-semibold">
                                    File Konten
                                    <span class="text-danger">*</span>
                                </h6>
                            </div>
                        </div>
                        <div class="card-body">
                            <input type="file" name="file_content" id="file_content">
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </form>
    <div class="card border-0 shadow-sm mt-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    <i class="ph-info me-1"></i>
                    <small>Field bertanda <span class="text-danger">*</span> wajib diisi</small>
                </div>
                <button type="button" class="btn btn-primary btn-lg" onclick="submitted()">
                    <i class="ph-cloud-arrow-up me-2"></i>
                    Simpan & Upload Data
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .edition-item input.is-invalid {
        border-color: #dc3545;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(.375em + .1875rem) center;
        background-size: calc(.75em + .375rem) calc(.75em + .375rem);
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
    }
    .edition-item input[type="file"].is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
        background-image: none;
    }
    .edition-item .invalid-label {
        color: #dc3545;
        font-size: 0.8rem;
        margin-top: 4px;
        display: block;
    }
</style>

<script>
    let existingEditionsTable = null;

    $(function() {
        datePickerSingle('.date-picker-single');

        $(document).on('change', '#data-edition input[name="cc_edition_date[]"]', function() {
            $(this).removeClass('is-invalid');
        });

        $(document).on('change', '#data-edition input[name="cc_edition_content[]"]', function() {
            $(this).removeClass('is-invalid');
        });

        if(parseInt('{{ Main::isPerpusnas() }}') == 0) {
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
            select2Serverside('#executor_id', 'executor');

            select2Serverside('#city_id', 'location', {
                for: 'city'
            }, {
                minimumInputLength: 0
            });
        }

        select2Serverside('#currency', 'currency');

        dragAndDropFile('#file_cover', {
            maxFileCount: 1,
            autoReplace: true,
            allowedFileExtensions: ['jpg', 'jpeg', 'png'],
            maxFileSize: 2048,
        });

        dragAndDropFile('#file_content', {
            maxFileCount: 1,
            autoReplace: true,
            allowedFileExtensions: ['pdf', 'epub', 'mp3', 'mp4', 'wav'],
            maxFileSize: 204800,
        });

        $('#author').select2({
            multiple: true,
            tags: true,
            tokenSeparators: [';']
        });

        lookupCatalogParent('#catalog_title', '#catalog_id');
        codeType();
        chooseWorksheet();
    });

    function chooseWorksheet() {
        var worksheetId = $('#worksheet_id').val();

        if(worksheetId == 142) {
            $('#form-parent').removeClass('d-none');
            $('#column-edition').removeClass('d-none');
            $('#card-edition').removeClass('d-none');
            $('#section-file-cover').addClass('d-none');
            $('#section-file-content').addClass('d-none');
            $('#has_edition').prop('checked', true);

            toggleEditionSection();
        } else {
            $('#form-parent').addClass('d-none');
            $('#column-edition').addClass('d-none');
            $('#btn-cancel-parent').addClass('d-none');
            $('#card-edition').addClass('d-none');
            $('#section-file-cover').removeClass('d-none');
            $('#section-file-content').removeClass('d-none');
            $('#has_edition').prop('checked', false);
            $('#content-edition-copy').hide();
            $('#data-edition').empty();
            $('#empty-edition-message').show();
        }

        $('#card-edition #data-edition').html('');
    }

    function catalogParent() {
        $('#btn-cancel-parent').removeClass('d-none');

        if($('#catalog_id').val()) {
            $.ajax({
                url: '{{ url("digital-storage-handover/single-upload/catalog-parent") }}',
                type: 'GET',
                dataType: 'JSON',
                data: {
                    id: $('#catalog_id').val()
                },
                beforeSend: function() {
                    onLoading('show', 'body');
                },
                success: function(response) {
                    onLoading('close', 'body');

                    const data = response.data;
                    const copy = response.copy;

                    $('#executor_id').html(`
                        <option value="${data.PENERBIT_ID}" selected>
                            ${data.PENERBIT_ID} | ${data.NAME_PENERBIT}
                        </option>
                    `);

                    $('#worksheet_id').val(data.WORKSHEET_ID).change();
                    $('#collection_media_id').val(data.CM_ID_E_COL).change();
                    $('#title').val(data.TITLE);
                    $('#code_type').val(data.CODE_TYPE_E_COLLECTION).change();
                    $('#code').val(data.ISBN);
                    $('#series_checkbox').prop('checked', data.SERIES ? false : true).change();
                    $('#series').val(data.SERIES);
                    $('#serial').val(data.SERIAL_E_COLLECTION).change();
                    $('#publish_time').val(data.PUBLISHYEAR + '-' + data.PUBLISH_MONTH);
                    $('#preview').val(data.PREVIEW);
                    $('#currency').html('<option value="' + data.CURRENCY_E_COLLECTION + '" selected>' + data.CURRENCY_E_COLLECTION + '</option>');
                    $('#price').val(data.PRICE_E_COLLECTION);
                    $('#binding').val(data.JILID_E_COLLECTION);
                    $('#content_type').val(data.JENIS_ISI).change();
                    $('#container_type').val(data.JENIS_WADAH).change();
                    $('#media_type').val(data.JENIS_MEDIA).change();
                    $('#big_class_id').val(data.KELAS_BESAR_ID).change();
                    $('input[name="physical_description[paging]"]').val(data.PAGING);
                    $('input[name="physical_description[ill]"]').val(data.ILL);
                    $('input[name="physical_description[sizes]"]').val(data.SIZES);
                    $('#description').val(data.DESCRIPTION_E_COLLECTION).change();

                    if (data.CITY_ID) {
                        const label = (data.NAMAPROPINSI && data.NAMAKAB) ? `${data.NAMAPROPINSI} -> ${data.NAMAKAB}` : data.CITY_ID;

                        $('#city_id').html(`<option value="${data.CITY_ID}" selected>${label}</option>`);
                    }

                    if(copy && copy.length > 0) {
                        loadExistingEditionsDataTable(copy);

                        $('#existing-editions').show();
                    } else {
                        $('#existing-editions').hide();

                        if(existingEditionsTable) {
                            existingEditionsTable.destroy();

                            existingEditionsTable = null;
                        }
                    }

                    $('#data-edition').empty();
                    $('#empty-edition-message').show();
                    $('#content-edition-copy').hide();
                },
                error: function(response) {
                    onLoading('close', 'body');
                    responseError(response);
                }
            });
        }
    }

    function loadExistingEditionsDataTable(editions) {
        if(existingEditionsTable) {
            existingEditionsTable.destroy();
        }

        const tableData = editions.map(function(edition, index) {
            const editionDate = edition.EDITION_DATE ? moment(edition.EDITION_DATE).format('DD/MM/YYYY') : '-';
            const editionTitle = edition.EDITION || '-';
            const articleTitle = edition.ARTICLE_TITLE || '-';
            const articleContributor = edition.ARTICLE_CONTRIBUTOR || '-';
            const articleDoi = edition.ARTICLE_DOI || '-';

            const files = [];
            if(edition.COVER_FILEURL) {
                files.push(`
                    <a href="{{ url('stream-file') }}?type=cover&id=${edition.COVER_ID}&filename=${edition.COVER_FILEURL}" target="_blank" class="btn btn-sm btn-outline-primary me-1" title="Lihat Cover">
                        <i class="ph-image"></i>
                    </a>
                `);
            }
            if(edition.CONTENT_FILEURL) {
                files.push(`
                    <a href="{{ url('stream-file') }}?type=konten_digital&id=${edition.CONTENT_ID}&filename=${edition.CONTENT_FILEURL}" target="_blank" class="btn btn-sm btn-outline-success" title="Lihat Konten">
                        <i class="ph-file"></i>
                    </a>
                `);
            }

            const filesHtml = files.length > 0 ? files.join(' ') : '<span class="text-muted">-</span>';

            const actionBtn = `
                <button type="button" class="btn btn-sm btn-info" onclick='viewEditionDetail(${JSON.stringify(edition)})'>
                    <i class="ph-eye me-1"></i>
                    Detail
                </button>
            `;

            return [
                index + 1,
                editionTitle,
                editionDate,
                articleTitle,
                articleContributor,
                articleDoi,
                filesHtml,
                actionBtn
            ];
        });

        existingEditionsTable = $('#table-existing-editions').DataTable({
            data: tableData,
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Semua"]],
            order: [[2, 'desc']],
            columnDefs: [
                { orderable: false, targets: [6, 7] },
                { className: 'text-center', targets: [0, 5, 6, 7] }
            ],
            responsive: true
        });
    }

    function viewEditionDetail(edition) {
        const editionDate = edition.EDITION_DATE ? moment(edition.EDITION_DATE).format('dddd, DD MMMM YYYY') : '-';
        const articlePublishDate = edition.ARTICLE_PUBLISH_DATE ? moment(edition.ARTICLE_PUBLISH_DATE).format('dddd, DD MMMM YYYY') : '-';

        const detailHtml = `
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary bg-opacity-10">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ph-book-open me-1"></i>
                                Informasi Edisi
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-form-label col-md-4 fw-semibold">Edisi/Volume</label>
                                <div class="col-md-8">
                                    <p class="form-control-plaintext">${edition.EDITION || '-'}</p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-md-4 fw-semibold">Tgl Terbit</label>
                                <div class="col-md-8">
                                    <p class="form-control-plaintext">${editionDate}</p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-md-4 fw-semibold">Cover</label>
                                <div class="col-md-8">
                                    ${edition.COVER_FILEURL ? `
                                        <a href="{{ url('stream-file') }}?type=cover&id=${edition.COVER_ID}&filename=${edition.COVER_FILEURL}" target="_blank" class="btn btn-sm btn-primary">
                                            <i class="ph-image me-1"></i>
                                            Lihat Cover
                                        </a>
                                    ` : '<span class="text-muted">Tidak ada</span>'}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-md-4 fw-semibold">Konten</label>
                                <div class="col-md-8">
                                    ${edition.CONTENT_FILEURL ? `
                                        <a href="{{ url('stream-file') }}?type=konten_digital&id=${edition.CONTENT_ID}&filename=${edition.CONTENT_FILEURL}" target="_blank" class="btn btn-sm btn-success">
                                            <i class="ph-file me-1"></i>
                                            Lihat Konten
                                        </a>
                                    ` : '<span class="text-muted">Tidak ada</span>'}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-md-4 fw-semibold">Status</label>
                                <div class="col-md-8">
                                    <span class="badge bg-success">
                                        <i class="ph-check-circle me-1"></i>
                                        Terverifikasi
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-success bg-opacity-10">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ph-article me-1"></i>
                                Informasi Artikel
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-form-label col-md-4 fw-semibold">Judul</label>
                                <div class="col-md-8">
                                    <p class="form-control-plaintext">${edition.ARTICLE_TITLE || '-'}</p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-md-4 fw-semibold">Kontributor</label>
                                <div class="col-md-8">
                                    <p class="form-control-plaintext">${edition.ARTICLE_CONTRIBUTOR || '-'}</p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-md-4 fw-semibold">Abstrak</label>
                                <div class="col-md-8">
                                    <p class="form-control-plaintext">${edition.ARTICLE_ABSTRACT || '-'}</p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-md-4 fw-semibold">Subyek</label>
                                <div class="col-md-8">
                                    <p class="form-control-plaintext">${edition.ARTICLE_SUBJECT || '-'}</p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-md-4 fw-semibold">Link Original</label>
                                <div class="col-md-8">
                                    ${edition.ARTICLE_ORIGINAL_LINK ? `
                                        <a href="${edition.ARTICLE_ORIGINAL_LINK}" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="ph-link me-1"></i>
                                            Buka Link
                                        </a>
                                    ` : '<p class="form-control-plaintext">-</p>'}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-md-4 fw-semibold">Tgl Publikasi</label>
                                <div class="col-md-8">
                                    <p class="form-control-plaintext">${articlePublishDate}</p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-md-4 fw-semibold">DOI</label>
                                <div class="col-md-8">
                                    <p class="form-control-plaintext">${edition.ARTICLE_DOI || '-'}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('#modalEditionDetailContent').html(detailHtml);

        const modal = new bootstrap.Modal(document.getElementById('modalEditionDetail'));

        modal.show();
    }

    function toggleEditionSection() {
        if($('input[name="has_edition"]').is(':checked')) {
            $('#content-edition-copy').fadeIn(500);
        } else {
            $('#content-edition-copy').hide();
            $('#data-edition').empty();
            $('#empty-edition-message').show();
        }
    }

    function checkISBNCode() {
        $.ajax({
            url: '{{ url("digital-storage-handover/single-upload/check-isbn-code") }}',
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
                        text: 'Kode ditemukan di database',
                        icon: 'success',
                        showDenyButton: true,
                        confirmButtonText: '<i class="ph-check me-1"></i> Otomatis Isi Data',
                        denyButtonText: '<i class="ph-x me-1"></i> Hanya Cek Kode'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#title').val(response.data.title);
                            $('#executor_id').html(`<option value="` + response.data.penerbit_id + `" selected>` + response.data.penerbit_id + ` | ` + response.data.nama_penerbit + `</option>`);

                            notification('success', 'Data berhasil diisi otomatis');
                        }
                    });
                } else {
                    swalInit.fire({
                        title: 'Oops',
                        text: 'Data tidak ditemukan',
                        icon: 'error',
                        showCloseButton: false
                    });
                }
            },
            error: function(response) {
                onLoading('close', 'body');
                responseError(response);
            }
        });
    }

    function addEdition() {
        var total = parseInt($('#add-number-edition').val(), 10);

        if(isNaN(total) || total < 1 || total > 10) {
            swalInit.fire({
                title: 'Peringatan',
                text: 'Jumlah baris harus antara 1-10',
                icon: 'warning',
                showCloseButton: false
            });

            return;
        }

        for(var i = 1; i <= total; i++) {
            const editionIndex = $('#data-edition .edition-item').length + 1;

            $('#data-edition').append(`
                <div class="edition-item mb-4 p-3 border rounded bg-light">
                    <input type="hidden" name="cc_edition[]" value="1">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <h6 class="mb-0 fw-bold text-primary">
                            <i class="ph-newspaper me-1"></i>
                            Edisi Serial #${editionIndex}
                        </h6>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeEditionItem(this)">
                            <i class="ph-trash me-1"></i>
                            Hapus Edisi
                        </button>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-primary bg-opacity-10 border-bottom">
                                    <h6 class="mb-0 fw-semibold">
                                        <i class="ph-book-open me-1"></i>
                                        Informasi Edisi
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 fw-semibold">
                                            <i class="ph-tag me-1"></i>
                                            Edisi / Volume
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-md-12">
                                            <input type="text" class="form-control" name="cc_edition_title[]" placeholder="Contoh: Vol 1 No 1" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 fw-semibold">
                                            <i class="ph-calendar me-1"></i>
                                            Tanggal Terbit Edisi
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-md-12">
                                            <input type="text" class="form-control date-picker-edition" name="cc_edition_date[]" placeholder="Pilih Tanggal" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 fw-semibold">
                                            <i class="ph-image me-1"></i>
                                            File Cover
                                        </label>
                                        <div class="col-md-12">
                                            <input type="file" class="form-control" name="cc_edition_cover[]" accept=".jpg,.jpeg,.png">
                                            <small class="text-muted">Format: JPG, JPEG, PNG</small>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 fw-semibold">
                                            <i class="ph-file me-1"></i>
                                            File Konten
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="col-md-12">
                                            <input type="file" class="form-control" name="cc_edition_content[]" accept=".pdf,.epub,.mp3,.mp4,.wav" required>
                                            <small class="text-muted">Format: PDF, EPUB, MP3, MP4, WAV</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-success bg-opacity-10 border-bottom">
                                    <h6 class="mb-0 fw-semibold">
                                        <i class="ph-article me-1"></i>
                                        Informasi Artikel
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 fw-semibold">
                                            <i class="ph-file-text me-1"></i>
                                            Judul Artikel
                                        </label>
                                        <div class="col-md-12">
                                            <input type="text" class="form-control" name="cc_edition_article_title[]" placeholder="Masukkan judul artikel">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 fw-semibold">
                                            <i class="ph-user me-1"></i>
                                            Kontributor Artikel
                                        </label>
                                        <div class="col-md-12">
                                            <input type="text" class="form-control" name="cc_edition_article_contributor[]" placeholder="Nama kontributor">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 fw-semibold">
                                            <i class="ph-note-pencil me-1"></i>
                                            Abstrak Artikel
                                        </label>
                                        <div class="col-md-12">
                                            <textarea class="form-control" name="cc_edition_article_abstract[]" rows="3" placeholder="Ringkasan artikel"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 fw-semibold">
                                            <i class="ph-hash me-1"></i>
                                            Subyek Artikel
                                        </label>
                                        <div class="col-md-12">
                                            <input type="text" class="form-control" name="cc_edition_article_subject[]" placeholder="Kata kunci / subyek">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 fw-semibold">
                                            <i class="ph-link me-1"></i>
                                            Link Original Artikel
                                        </label>
                                        <div class="col-md-12">
                                            <input type="url" class="form-control" name="cc_edition_article_original_link[]" placeholder="https://...">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 fw-semibold">
                                            <i class="ph-calendar-check me-1"></i>
                                            Tanggal Publikasi Artikel
                                        </label>
                                        <div class="col-md-12">
                                            <input type="text" class="form-control date-picker-edition-article" name="cc_edition_article_publish_date[]" placeholder="Pilih Tanggal">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-12 fw-semibold">
                                            <i class="ph-identification-badge me-1"></i>
                                            DOI Artikel
                                        </label>
                                        <div class="col-md-12">
                                            <input type="text" class="form-control" name="cc_edition_article_doi[]" placeholder="10.xxxx/xxxxx">
                                            <small class="text-muted">Digital Object Identifier</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `);
        }

        $('#empty-edition-message').addClass('d-none');

        datePickerSingle('.date-picker-edition');
        datePickerSingle('.date-picker-edition-article');
    }

    function removeEditionItem(button) {
        $(button).closest('.edition-item').remove();

        $('#data-edition .edition-item').each(function(index) {
            $(this).find('h6.fw-bold').html(`
                <i class="ph-newspaper me-1"></i>
                Edisi Serial #${index + 1}
            `);
        });

        if ($('#data-edition .edition-item').length === 0) {
            $('#empty-edition-message').removeClass('d-none');
        }
    }

    function codeType() {
        var codeType = $('#code_type').val();

        $('#code').val('');
        $('#btn-check-isbn').hide();
        $('#code').attr('readonly', false);

        if(codeType == 1) {
            $('#btn-check-isbn').show();
        } else if(codeType == '') {
            $('#code').attr('readonly', true);
        }
    }

    function clearValidation() {
        $('#validation-element').addClass('d-none');
        $('#validation-data').html('');
        $('html, body').animate({scrollTop: 0}, 300);
    }

    function showValidation(data) {
        $('#validation-element').removeClass('d-none');
        $('#validation-data').html('');

        $.each(data, function(index, value) {
            $('#validation-data').append('<li>' + value + '</li>');
        });

        $('html, body').animate({scrollTop: 0}, 500);
    }

    function submitted() {
        const form = $('#form-data')[0];

        if (!form.checkValidity()) {
            form.reportValidity();

            return;
        }

        if (!$('input[name="has_edition"]').is(':checked')) {
            $('input[name="has_edition"]').val(0);
        }

        if ($('input[name="has_edition"]').is(':checked')) {
            const editionItems = $('#data-edition .edition-item');

            if (editionItems.length === 0) {
                swalInit.fire({
                    title: 'Edisi Kosong',
                    text: 'Anda mengaktifkan edisi serial, namun belum menambahkan edisi apapun.',
                    icon: 'warning',
                    confirmButtonText: '<i class="ph-check me-1"></i> Mengerti',
                });

                return;
            }

            let editionErrors = [];

            editionItems.each(function(index) {
                const editionNumber = index + 1;
                const $item = $(this);

                const titleVal = $item.find('input[name="cc_edition_title[]"]').val();
                const dateVal = $item.find('input[name="cc_edition_date[]"]').val();
                const fileInput = $item.find('input[name="cc_edition_content[]"]')[0];
                const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;

                $item.find('input[name="cc_edition_title[]"]').removeClass('is-invalid');
                $item.find('input[name="cc_edition_date[]"]').removeClass('is-invalid');
                $item.find('input[name="cc_edition_content[]"]').removeClass('is-invalid');

                if (!titleVal) {
                    $item.find('input[name="cc_edition_title[]"]').addClass('is-invalid');
                    editionErrors.push(`Edisi #${editionNumber}: Judul edisi wajib diisi`);
                }

                if (!dateVal) {
                    $item.find('input[name="cc_edition_date[]"]').addClass('is-invalid');
                    editionErrors.push(`Edisi #${editionNumber}: Tanggal terbit wajib diisi`);
                }

                if (!hasFile) {
                    $item.find('input[name="cc_edition_content[]"]').addClass('is-invalid');
                    editionErrors.push(`Edisi #${editionNumber}: File konten wajib diisi`);
                }
            });

            if (editionErrors.length > 0) {
                const errorListHtml = editionErrors.map(e => `<li class="text-start">${e}</li>`).join('');

                swalInit.fire({
                    title: 'Edisi Belum Lengkap',
                    html: `<ul class="mb-0 ps-3">${errorListHtml}</ul>`,
                    icon: 'warning',
                    confirmButtonText: '<i class="ph-check me-1"></i> Oke, Saya Perbaiki',
                });

                const firstError = $('#data-edition .edition-item .is-invalid').first();

                if (firstError.length) {
                    $('html, body').animate({ scrollTop: firstError.closest('.edition-item').offset().top - 100 }, 400);
                }

                return;
            }
        }

        $.ajax({
            url: '{{ url("digital-storage-handover/single-upload/submitted") }}',
            type: 'POST',
            dataType: 'JSON',
            data: new FormData(form),
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
                        confirmButtonText: '<i class="ph-check me-1"></i> Oke',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            onLoading('show', 'body');
                            location.href = '{{ url("digital-storage-handover/single-upload") }}';
                        }
                    });
                } else if(response.code == 400) {
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
