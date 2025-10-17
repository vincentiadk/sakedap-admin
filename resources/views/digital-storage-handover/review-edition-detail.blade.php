<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Serah Simpan Digital - Peninjauan Edisi - <span class="fw-normal">Detail</span>
            </h4>
        </div>
        <div class="collapse d-lg-block my-lg-auto ms-lg-auto" id="page-header">
            <div class="d-sm-flex align-items-center mb-3 mb-lg-0 ms-lg-3">
                <div class="d-inline-flex mt-3 mt-sm-0">
                    <button type="button" class="btn btn-secondary me-2" onclick="lookupCatalogHistory('E_COLLECTIONS', {{ $collection->ID }})">
                        <i class="ph-books me-1"></i>
                        Histori E-Collection
                    </button>
                    <button type="button" class="btn btn-info me-2" onclick="lookupCatalogHistory('CATALOGS', {{ $collection->ID }})">
                        <i class="ph-book-open me-1"></i>
                        Histori Katalog
                    </button>
                    <a href="{{ url('digital-storage-handover/review-edition') }}" class="btn btn-primary">
                        <i class="ph-arrow-left me-1"></i>
                        Kembali ke Tabel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="alert alert-danger d-none" id="validation-element">
        <ul class="mb-0" id="validation-data"></ul>
    </div>
    <form id="form-data">
        <div class="d-flex align-items-stretch align-items-lg-start flex-column flex-lg-row">
            <div class="flex-1 order-2 order-lg-1">
                <div class="card" id="scrollspy-history-problem">
                    <div class="card-header d-flex align-items-center">
                        <h6 class="mb-0">Histori Masalah</h6>
                        <div class="ms-auto">
                            @if($collection->REVISION_COUNT)
                                <span class="badge bg-danger">{{ $collection->REVISION_COUNT }} Kali Dilakukan Revisi</span>
                            @else
                                <span class="badge bg-info">Belum Ada Revisi</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover">
                            <tbody>
                                @if($collectionProblemHistory)
                                    @foreach($collectionProblemHistory as $cph)
                                        <tr>
                                            <td>{{ $cph->NAME_PROBLEM }}</td>
                                            <td>{{ $cph->CREATED_AT ? Carbon::parse($cph->CREATED_AT)->isoFormat('dddd, D MMMM Y') : null }}</td>
                                            <td>{{ $cph->SOLVED == 1 ? 'Telah Diperbaiki' : 'Belum Diperbaiki' }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3">Tidak ada data</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($collection->TITLE_PARENT)
                    <div class="card" id="scrollspy-parent">
                        <div class="card-header">
                            <h5 class="hstack gap-2 mb-0">Parent</h5>
                        </div>
                        <div class="card-body">
                            {{ $collection->TITLE_PARENT }}
                        </div>
                    </div>
                @endif
                <div class="card" id="scrollspy-executor">
                    <div class="card-header">
                        <h5 class="hstack gap-2 mb-0">Pelaksana Serah</h5>
                    </div>
                    <div class="card-body">
                        {{ $collection->ID_PENERBIT . ' | ' . $collection->NAME_PENERBIT }}
                    </div>
                </div>
                <div class="card" id="scrollspy-meta-data">
                    <div class="card-header">
                        <h5 class="hstack gap-2 mb-0">Meta Data</h5>
                    </div>
                    <div class="card-body">
                        @if($collection->TITLE_PARENT)
                            <div class="form-group row">
                                <label class="col-form-label col-md-2">Edisi</label>
                                <div class="col-md-10">
                                    <input type="text" class="form-control" name="edition" id="edition" value="{{ $collection->EDITION ?? $collection->EDITION }}" placeholder="....................">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-md-2">Tanggal Terbit Edisi <span class="text-danger fw-bold">*</span></label>
                                <div class="col-md-10">
                                    <input type="text" class="form-control date-picker-single" name="edition_date" id="edition_date" value="{{ Carbon::parse($collection->EDITION_DATE)->format('Y/m/d') }}" placeholder="Pilih Tanggal" readonly>
                                </div>
                            </div>
                        @endif
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Jenis Bahan <span class="text-danger fw-bold">*</span></label>
                            <div class="col-md-10">
                                <select class="form-select select2-basic" name="worksheet_id" id="worksheet_id" disabled>
                                    <option value=""></option>
                                    @foreach($worksheet as $w)
                                        <option value="{{ $w->ID }}" {{ $collection->WORKSHEET_ID == $w->ID ? 'selected' : '' }}>{{ $w->NAME }} [{{ $w->CATEGORY }}]</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Judul <span class="text-danger fw-bold">*</span></label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="title" id="title" value="{{ $collection->TITLE ?? $collection->TITLE_ORI }}" placeholder="....................">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Kode</label>
                            <div class="col-md-10">
                                <div class="input-group">
                                    <select class="form-select w-auto flex-grow-0" name="code_type" id="code_type" disabled>
                                        <option value="">Tidak Ada</option>
                                        <option value="1" {{ $collection->CODE_TYPE == 1 ? 'selected' : ''  }}>ISBN</option>
                                        <option value="2" {{ $collection->CODE_TYPE == 2 ? 'selected' : ''  }}>ISMN</option>
                                        <option value="3" {{ $collection->CODE_TYPE == 3 ? 'selected' : ''  }}>ISRC</option>
                                        <option value="4" {{ $collection->CODE_TYPE == 4 ? 'selected' : ''  }}>ISSN</option>
                                        <option value="5" {{ $collection->CODE_TYPE == 5 ? 'selected' : ''  }}>ISAN</option>
                                    </select>
                                    <input type="text" class="form-control" name="code" id="code" value="{{ empty($collection->CODE) ? '-' : $collection->CODE }}" placeholder="...................." disabled>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Kota <span class="text-danger fw-bold">*</span></label>
                            <div class="col-md-10">
                                <select class="form-select" name="city_id" id="city_id">
                                    <option value="{{ $collection->KABUPATEN_ID }}" selected>
                                        {{ $collection->NAMAPROPINSI }} -> {{ $collection->NAMAKAB }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Media <span class="text-danger fw-bold">*</span></label>
                            <div class="col-md-10">
                                <select class="form-select select2-basic" name="collection_media_id" id="collection_media_id">
                                    <option value=""></option>
                                    @foreach($media as $m)
                                        <option value="{{ $m->ID }}" {{ $collection->COLLECTION_MEDIA_ID == $m->ID ? 'selected' : '' }}>{{ $m->NAME }} [{{ $m->DEPOSITFORMAT_CODE }}]</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">QRCBN</label>
                            <div class="col-md-10">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <label>
                                            <input type="checkbox" class="form-check-input mt-0 me-1" onchange="$(this).is(':checked') ? $('#qrcbn').attr('disabled', true) : $('#qrcbn').attr('disabled', false)" @if(empty($collection->QRCBN)) checked @endif>
                                            Tidak Ada
                                        </label>
                                    </span>
                                    <input type="text" class="form-control" name="qrcbn" id="qrcbn" value="{{ $collection->QRCBN }}" placeholder="...................." @if(empty($collection->QRCBN)) disabled @endif>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Seri</label>
                            <div class="col-md-10">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <label>
                                            <input type="checkbox" class="form-check-input mt-0 me-1" onchange="$(this).is(':checked') ? $('#series').attr('disabled', true) : $('#series').attr('disabled', false)" @if(empty($collection->SERIES)) checked @endif>
                                            Tidak Ada
                                        </label>
                                    </span>
                                    <input type="text" class="form-control" name="series" id="series" value="{{ $collection->SERIES }}" placeholder="...................." @if(empty($collection->SERIES)) disabled @endif>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Kala Terbit</label>
                            <div class="col-md-10">
                                <select class="form-select select2-basic" name="serial" id="serial" data-placeholder="Tidak Ada">
                                    <option value=""></option>
                                    <option value="1" {{ $collection->SERIAL == 1 ? 'selected' : '' }}>Harian</option>
                                    <option value="2" {{ $collection->SERIAL == 2 ? 'selected' : '' }}>Mingguan</option>
                                    <option value="3" {{ $collection->SERIAL == 3 ? 'selected' : '' }}>Bulanan</option>
                                    <option value="4" {{ $collection->SERIAL == 4 ? 'selected' : '' }}>3 Bulan Sekali</option>
                                    <option value="5" {{ $collection->SERIAL == 5 ? 'selected' : '' }}>4 Bulan Sekali</option>
                                    <option value="6" {{ $collection->SERIAL == 6 ? 'selected' : '' }}>6 Bulan Sekali</option>
                                    <option value="7" {{ $collection->SERIAL == 7 ? 'selected' : '' }}>Tahunan</option>
                                    <option value="8" {{ $collection->SERIAL == 8 ? 'selected' : '' }}>2 Tahun Sekali</option>
                                    <option value="9" {{ $collection->SERIAL == 9 ? 'selected' : '' }}>3 Tahun Sekali</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Waktu Terbit</label>
                            <div class="col-md-10">
                                <input type="month" class="form-control" name="publish_time" id="publish_time" value="{{ $collection->PUBLICATION_YEAR . '-' . $collection->PUBLICATION_MONTH }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Tanggal Terima <span class="text-danger fw-bold">*</span></label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="received_at" id="received_at" value="{{ Carbon::parse($collection->RECEIVED_AT)->format('Y/m/d') }}" placeholder="Pilih Tanggal" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Preview</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="preview" id="preview" value="{{ $collection->PREVIEW }}" placeholder="cth : 1-5 / 00:01-00:20">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Akses</label>
                            <div class="col-md-10">
                                <select class="form-select select2-basic" name="access" id="access" data-placeholder="Pilih" disabled>
                                    <option value=""></option>
                                    <option value="1" {{ $collection->AKSES == 1 ? 'selected' : '' }}>Akses full file berwatermak secara online</option>
                                    <option value="2" {{ $collection->AKSES == 2 ? 'selected' : '' }}>Akses hanya preview file secara online, namun tetap dapat di dayagunakan di lingkungan perpustakaan nasional RI dengan jaringan internet LAN</option>
                                    <option value="3" {{ $collection->AKSES == 3 ? 'selected' : '' }}>Akses hanya file preview secara online, dan tidak didayagunakan di lingkungan Perpustakaan Nasional RI selama 5 tahun sejak diserahkan. Setelah 5 tahun, akan didayagunakan oleh Perpustakaan Nasional RI di jaringan internet LAN</option>
                                    <option value="4" {{ $collection->AKSES == 4 ? 'selected' : '' }}>Akses hanya file preview secara online selamanya dan tidak didayagunakan di mana pun</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Mata Uang</label>
                            <div class="col-md-10">
                                <select class="form-select" name="currency" id="currency">
                                    @if($collection->CURRENCY)
                                        <option value="{{ $collection->CURRENCY }}" selected>{{ $collection->CURRENCY }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Harga</label>
                            <div class="col-md-10">
                                <input type="number" class="form-control" name="price" id="price" value="{{ $collection->PRICE }}" placeholder="....................">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Jilid</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="binding" id="binding" value="{{ $collection->JILID }}" placeholder="....................">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Jenis Isi</label>
                            <div class="col-md-10">
                                <select class="form-select select2-basic" name="content_type" id="content_type">
                                    <option value=""></option>
                                    @foreach($contentType as $ct)
                                        <option value="{{ $ct->NAME }}" {{ $collection->JENIS_ISI == $ct->NAME ? 'selected' : '' }}>{{ $ct->NAME }}</option>
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
                                        <option value="{{ $ct->NAME }}" {{ $collection->JENIS_WADAH == $ct->NAME ? 'selected' : '' }}>{{ $ct->NAME }}</option>
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
                                        <option value="{{ $mt->NAME }}" {{ $collection->JENIS_MEDIA == $mt->NAME ? 'selected' : '' }}>{{ $mt->NAME }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Kelas Besar</label>
                            <div class="col-md-10">
                                <select class="form-select select2-basic" name="big_class_id" id="big_class_id">
                                    <option value=""></option>
                                    @foreach($bigClass as $bc)
                                        <option value="{{ $bc->ID }}" {{ $collection->KELAS_BESAR_ID == $bc->ID ? 'selected' : '' }}>{{ $bc->CLASS }} - {{ $bc->DESCRIPTION }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Keterangan Fisik</label>
                            <div class="col-md-10">
                                <div class="input-group">
                                    <span class="input-group-text">Total Halaman / Durasi</span>
                                    <input type="number" class="form-control" name="physical_description[paging]" id="physical_description[paging]" value="{{ isset($physicalDescription->paging) ? $physicalDescription->paging : '' }}" placeholder="....................">
                                    <select class="form-select flex-grow-0 w-auto" name="physical_description[paging_flag]" id="physical_description[paging_flag]">
                                        <option value="Halaman" {{ isset($physicalDescription->paging_flag) ? ($physicalDescription->paging_flag == 'Halaman' ? 'selected' : '') : '' }}>Halaman</option>
                                        <option value="Menit" {{ isset($physicalDescription->paging_flag) ? ($physicalDescription->paging_flag == 'Menit' ? 'selected' : '') : '' }}>Menit</option>
                                        <option value="Jam" {{ isset($physicalDescription->paging_flag) ? ($physicalDescription->paging_flag == 'Jam' ? 'selected' : '') : '' }}>Jam</option>
                                    </select>
                                    <span class="input-group-text">Ilustrasi</span>
                                    <input type="text" class="form-control" name="physical_description[ill]" list="suggestion-physical-description-ill" id="physical_description[ill]" value="{{ isset($physicalDescription->ill) ? $physicalDescription->ill : '' }}" placeholder="...................." autocomplete="off">
                                    <datalist id="suggestion-physical-description-ill">
                                        <option value="Tidak Ada">Tidak Ada</option>
                                        <option value="Ada (Berwarna)">Ada (Berwarna)</option>
                                        <option value="Ada (Tidak Berwarna)">Ada (Tidak Berwarna)</option>
                                    </datalist>
                                    <span class="input-group-text">Ukuran / Dimensi</span>
                                    <input type="text" class="form-control" name="physical_description[sizes]" id="physical_description[sizes]" value="{{ isset($physicalDescription->sizes) ? $physicalDescription->sizes : '' }}" placeholder="....................">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Sinopsis</label>
                            <div class="col-md-10">
                                <textarea name="description" class="form-control" id="description" rows="5" placeholder="....................">{{ $collection->DESCRIPTION }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card" id="scrollspy-category">
                    <div class="card-header">
                        <h5 class="hstack gap-2 mb-0">Kategori</h5>
                    </div>
                    <div class="card-body">
                        <select class="form-select select2-basic" name="category[]" id="category" data-placeholder="Tidak ada" multiple>
                            <option value=""></option>
                            @foreach($category as $c)
                                <option value="{{ $c->ID }}" {{ in_array($c->ID, $collectionCategory) ? 'selected' : '' }}>{{ $c->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card" id="scrollspy-author">
                    <div class="card-header">
                        <h5 class="hstack gap-2 mb-0">Kontributor</h5>
                    </div>
                    <div class="card-body">
                        <select class="form-select" name="author[]" id="author" data-placeholder="Tulis beberapa" multiple>
                            @foreach(explode(';', ($collection->AUTHOR ?? '')) as $c)
                                <option value="{{ $c }}" selected>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if(!$collection->TITLE_PARENT)
                    <div class="card" id="scrollspy-edition-serial">
                        <div class="card-header">
                            <h5 class="hstack gap-2 mb-0">Edisi Serial</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Edisi / Volume</th>
                                            <th>Tgl Terbit</th>
                                            <th>Cover</th>
                                            <th>Konten</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($collectionCopy)
                                            @foreach($collectionCopy as $key => $cc)
                                                @php
                                                    $cover = QueryAPI::get("
                                                        select
                                                            *
                                                        from
                                                            catalogcovers
                                                        where
                                                            e_col_id = $cc->ID
                                                    ", true);

                                                    $content = QueryAPI::get("
                                                        select
                                                            *
                                                        from
                                                            catalogfiles
                                                        where
                                                            e_col_id = $cc->ID
                                                    ", true);
                                                @endphp
                                                <tr>
                                                    <td>{{ $cc->EDITION }}</td>
                                                    <td>{{ $cc->EDITION_DATE ? Carbon::parse($cc->EDITION_DATE)->isoFormat('dddd, D MMMM Y') : '' }}</td>
                                                    <td>
                                                        @if($cover)
                                                            <a href="{{ url('stream-file') }}?type=cover&id={{ $cover->ID }}&filename={{ $cover->FILEURL}}" class="text-primary" data-lightbox="Cover-Edisi-{{ $key + 1 }}" data-title="{{ $cover->FILEURL }}">
                                                                <i class="ph-image me-1"></i>
                                                                Lihat
                                                            </a>
                                                        @else
                                                            Tidak ada cover
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($content)
                                                            <a href="{{ url('stream-file') }}?type=konten_digital&id={{ $content->ID }}&filename={{ $content->FILEURL}}" class="text-primary" target="_blank">
                                                                <i class="ph-file me-1"></i>
                                                                Lihat
                                                            </a>
                                                        @else
                                                            Tidak ada konten
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4">Tidak ada data</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="card" id="scrollspy-file">
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-tabs-highlight nav-justified">
                            <li class="nav-item">
                                <a href="#nav-tabs-cover" class="nav-link active" data-bs-toggle="tab">File Cover</a>
                            </li>
                            <li class="nav-item">
                                <a href="#nav-tabs-watermark" class="nav-link" data-bs-toggle="tab">File Konten</a>
                            </li>
                        </ul>
                        <div class="tab-content flex-lg-fill mt-4">
                            <div class="tab-pane fade show active" id="nav-tabs-cover">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="ratio ratio-16x9">
                                            <img src="{{ url('stream-file') }}?type=cover&id={{ $collectionCover->ID ?? '' }}&filename={{ $collectionCover->FILEURL ?? '' }}" class="img-fluid object-fit-cover">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="alert alert-info">
                                            <div><b>Hash :</b> {{ $collectionCover->HASH ?? '' }}</div>
                                            <div><b>Mime Type :</b> {{ $collectionCover->MIME ?? '' }}</div>
                                            <div><b>Ukuran :</b> {{ Main::formatFileSize($collectionCover->FILE_SIZE ?? 0) }}</div>
                                            <div><b>Metode :</b> {{ Main::method($collectionCover->METHOD ?? 0) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-tabs-watermark">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="ratio ratio-16x9">
                                            <iframe src="{{ url('stream-file') }}?type=file_access&id={{ $collectionContent->ID ?? '' }}&filename={{ $collectionContent->FILEURL ?? '' }}" frameborder="0"></iframe>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="alert alert-info">
                                            <div><b>Hash :</b> {{ $collectionContent->HASH ?? '' }}</div>
                                            <div><b>Mime Type :</b> {{ $collectionContent->MIME ?? '' }}</div>
                                            <div><b>Ukuran :</b> {{ Main::formatFileSize($collectionContent->FILE_SIZE ?? 0) }}</div>
                                            <div><b>Metode :</b> {{ Main::method($collectionContent->METHOD ?? 0) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card" id="scrollspy-status">
                    <div class="card-header">
                        <h5 class="hstack gap-2 mb-0">Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="btn-group d-flex">
                                <input type="radio" class="btn-check" name="status" id="status-1" autocomplete="off" value="1" onchange="changeStatus()" checked>
                                <label class="btn btn-outline-primary" for="status-1">Tinjau</label>
                                <input type="radio" class="btn-check" name="status" id="status-2" autocomplete="off" value="2" onchange="changeStatus()">
                                <label class="btn btn-outline-success" for="status-2">Terima</label>
                                <input type="radio" class="btn-check" name="status" id="status-3" autocomplete="off" value="3" onchange="changeStatus()">
                                <label class="btn btn-outline-warning" for="status-3">Bermasalah</label>
                                <input type="radio" class="btn-check" name="status" id="status-5" autocomplete="off" value="5" onchange="changeStatus()">
                                <label class="btn btn-outline-danger" for="status-5">Tolak</label>
                            </div>
                        </div>
                        <div id="content-change-status"></div>
                    </div>
                </div>
                @if($collection->REVIEW_BY == session('username'))
                    <div class="card" id="scrollspy-submit">
                        <div class="card-body">
                            <div class="text-end">
                                <button type="button" class="btn btn-danger" onclick="submitted('cancel-review')">
                                    <i class="ph-x me-1"></i>
                                    Batal Tinjau
                                </button>
                                <button type="button" class="btn btn-warning" onclick="submitted('save')">
                                    <i class="ph-floppy-disk me-1"></i>
                                    Simpan
                                </button>
                                <button type="button" class="btn btn-success" onclick="submitted('save-verification')">
                                    <i class="ph-check me-1"></i>
                                    Simpan & Verifikas
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="sticky-lg-top order-1 order-lg-2 wmin-lg-200 ms-lg-3 mb-3" id="page_nav">
                <h6 class="fw-semibold mt-lg-3 mb-3">Anchor Link</h6>
                <ul class="nav nav-scrollspy flex-column">
                    <li class="nav-item">
                        <a href="#scrollspy-history-problem" class="nav-link">Histori Masalah</a>
                    </li>
                    @if($collection->TITLE_PARENT)
                        <li class="nav-item">
                            <a href="#scrollspy-parent" class="nav-link">Parent</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a href="#scrollspy-executor" class="nav-link">Pelaksana Serah</a>
                    </li>
                    <li class="nav-item">
                        <a href="#scrollspy-meta-data" class="nav-link">Meta Data</a>
                    </li>
                    <li class="nav-item">
                        <a href="#scrollspy-category" class="nav-link">Kategori</a>
                    </li>
                    <li class="nav-item">
                        <a href="#scrollspy-author" class="nav-link">Kontributor</a>
                    </li>
                    @if($collection->TITLE_PARENT)
                        <li class="nav-item">
                            <a href="#scrollspy-edition-serial" class="nav-link">Edisi Serial</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a href="#scrollspy-file" class="nav-link">File</a>
                    </li>
                    <li class="nav-item">
                        <a href="#scrollspy-status" class="nav-link">Status</a>
                    </li>
                    @if($collection->REVIEW_BY == session('username'))
                        <li class="nav-item">
                            <a href="#scrollspy-submit" class="nav-link">Submit</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </form>
</div>

<script>
    $(function() {
        datePickerSingle('#received_at');

        if(parseInt('{{ Main::isNotSuperAdmin() }}') === 1) {
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
        } else {
            select2Serverside('#branch_id', 'branch');

            select2Serverside('#city_id', 'city', {
                for: 'city'
            });
        }

        select2Serverside('#currency', 'currency');

        $('#author').select2({
            multiple: true,
            tags: true,
            tokenSeparators: [';']
        });
    });

    function removeRow(param) {
        $(param).closest('tr').remove();
    }

    function changeStatus() {
        var status = $('input[name="status"]:checked').val();

        $('#content-change-status').html('');

        if(status == 3) {
            $('#content-change-status').html(`
                <div class="form-group">
                    <select class="form-select select2-basic" name="collection_problem[]" id="collection_problem" data-placeholder="Pilih" multiple>
                        @foreach($problem as $p)
                            <option value="{{ $p->ID }}">{{ $p->NAME }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <textarea class="form-control" name="problem" id="problem" rows="5" placeholder="Keterangan lain masalah"></textarea>
                </div>
            `);
        } else if(status == 5) {
            $('#content-change-status').html(`
                <div class="form-group">
                    <textarea class="form-control" name="reject" id="reject" rows="5" placeholder="Keterangan penolakan"></textarea>
                </div>
            `);
        }

        select2Basic();
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
            url: '{{ url("digital-storage-handover/review-edition/detail/" . $collection->ID) }}?param=' + param,
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

                            location.href = '{{ url("digital-storage-handover/review-edition") }}';
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
