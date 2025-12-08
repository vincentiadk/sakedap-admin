<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Serah Simpan Digital - Peninjauan Serial Elektronik - <span class="fw-normal">Detail</span>
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
        <div class="card">
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
        <div class="row">
            <div class="col-md-7">
                @if($collection->TITLE_PARENT)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="hstack gap-2 mb-0">Parent</h5>
                        </div>
                        <div class="card-body">
                            {{ $collection->TITLE_PARENT }}
                        </div>
                    </div>
                @endif
                <div class="card">
                    <div class="card-header">
                        <h5 class="hstack gap-2 mb-0">Pelaksana Serah</h5>
                    </div>
                    <div class="card-body">
                        {{ $collection->ID_PENERBIT . ' | ' . $collection->NAME_PENERBIT }}
                    </div>
                </div>
                <div class="card">
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
                                <label class="col-form-label col-md-2">Tgl Terbit Edisi <span class="text-danger fw-bold">*</span></label>
                                <div class="col-md-10">
                                    <input type="text" class="form-control date-picker-single" name="edition_date" id="edition_date" value="{{ Carbon::parse($collection->EDITION_DATE)->format('Y/m/d') }}" placeholder="Pilih Tanggal" readonly>
                                </div>
                            </div>
                        @endif
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Jenis Bahan <span class="text-danger fw-bold">*</span></label>
                            <div class="col-md-10">
                                <select class="form-select" name="worksheet_id" id="worksheet_id" readonly>
                                    @if($collection->WORKSHEET_ID)
                                        @foreach($worksheet as $w)
                                            @if($collection->WORKSHEET_ID == $w->ID)
                                                <option value="{{ $w->ID }}" selected>{{ $w->NAME }} [{{ $w->CATEGORY }}]</option>
                                            @endif
                                        @endforeach
                                    @else
                                        <option value="">Tidak Ada</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Judul <span class="text-danger fw-bold">*</span></label>
                            <div class="col-md-10">
                                <textarea name="title" class="form-control" id="title" rows="5" placeholder="....................">{{ $collection->TITLE ?? $collection->TITLE_ORI }}</textarea>
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
                                <input type="text" class="form-control" name="publish_time" id="publish_time" value="{{ ($collection->PUBLICATION_DAY && $collection->PUBLICATION_MONTH && $collection->PUBLICATION_YEAR) ? $collection->PUBLICATION_YEAR . '/' . $collection->PUBLICATION_MONTH . '/' . $collection->PUBLICATION_DAY : '' }}" placeholder="Pilih Tanggal" readonly>
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
                                <input type="text" class="form-control" name="preview" id="preview" value="{{ $collection->PREVIEW }}" placeholder="cth : 1-5 / 00:01-00:20" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Akses</label>
                            <div class="col-md-10">
                                <select class="form-select select2-basic" name="access" id="access" data-placeholder="Pilih" readonly disabled>
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
                                <select class="form-select" name="currency" id="currency" disabled>
                                    @if($collection->CURRENCY)
                                        <option value="{{ $collection->CURRENCY }}" selected>{{ $collection->CURRENCY }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-2">Harga</label>
                            <div class="col-md-10">
                                <input type="number" class="form-control" name="price" id="price" value="{{ $collection->PRICE }}" placeholder="...................." disabled>
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
                                </div>
                                <div class="input-group mt-1">
                                    <span class="input-group-text">Ilustrasi</span>
                                    <input type="text" class="form-control" name="physical_description[ill]" list="suggestion-physical-description-ill" id="physical_description[ill]" value="{{ isset($physicalDescription->ill) ? $physicalDescription->ill : '' }}" placeholder="...................." autocomplete="off">
                                    <datalist id="suggestion-physical-description-ill">
                                        <option value="Tidak Ada">Tidak Ada</option>
                                        <option value="Ada (Berwarna)">Ada (Berwarna)</option>
                                        <option value="Ada (Tidak Berwarna)">Ada (Tidak Berwarna)</option>
                                    </datalist>
                                </div>
                                <div class="input-group mt-1">
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
                <div class="card">
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
                <div class="card">
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
                    <div class="card">
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
            </div>
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="hstack gap-2 mb-0">File Konten</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-2">
                            <div><b>Hash :</b> {{ $collection->HASH_CATALOGFILES ?? '' }}</div>
                            <div><b>Mime Type :</b> {{ $collection->MIME_CATALOGFILES ?? '' }}</div>
                            <div><b>Ukuran :</b> {{ Main::formatFileSize($collection->FILE_SIZE_CATALOGFILES ?? 0) }}</div>
                            <div><b>Metode :</b> {{ Main::method($collection->METHOD_CATALOGFILES ?? 0) }}</div>
                        </div>
                        <div id="viewer-wrapper" style="position: relative; width: 100%; min-height: 400px; background: #f5f5f5; border: 1px solid #ddd;">
                            <div id="viewer-content" style="width: 100%; height: 100%;">
                                <div id="pdf-viewer-container" style="overflow: auto; max-height: 800px; background: #525659; padding: 20px 0; display: none;"></div>
                                <video id="video-player" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" style="display: none; width: 100%; height: 450px;"></video>
                                <div id="epub-container" style="display: none; height: 800px; width: 100%; background: white;"></div>
                                <div id="epub-controls" style="display: none; position: absolute; top: 50%; left: 0; width: 100%; justify-content: space-between; padding: 0 20px; pointer-events: none; z-index: 10000; transform: translateY(-50%);">
                                    <button type="button" id="prev-btn" class="btn btn-dark btn-sm rounded-circle shadow" style="width: 40px; height: 40px; pointer-events: auto; display: flex; align-items: center; justify-content: center;">
                                        <i class="ph-caret-left" style="font-size: 20px;"></i>
                                    </button>
                                    <button type="button" id="next-btn" class="btn btn-dark btn-sm rounded-circle shadow" style="width: 40px; height: 40px; pointer-events: auto; display: flex; align-items: center; justify-content: center;">
                                        <i class="ph-caret-right" style="font-size: 20px;"></i>
                                    </button>
                                </div>
                                <div id="audio-wrapper" style="display: none; height: 800px; width: 100%; position: relative; overflow: hidden; border-radius: 8px; background: #000;">
                                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, #023BAD 0%, #06732A 100%); z-index: 1;"></div>
                                    <canvas id="wave-canvas" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 2; opacity: 0.6;"></canvas>
                                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10; display: flex; flex-direction: column; padding: 30px;">
                                        <div class="d-flex justify-content-between text-white align-items-center" style="font-family: sans-serif;">
                                            <span id="timer-current" style="font-variant-numeric: tabular-nums;">0:00</span>
                                            <span class="fw-bold text-uppercase" style="opacity: 0.8; font-size: 14px;">Now Playing</span>
                                            <span id="timer-duration" style="font-variant-numeric: tabular-nums;">--:--</span>
                                        </div>
                                        <div class="flex-grow-1 d-flex align-items-center justify-content-center text-center">
                                            <h3 id="audio-title-display" class="text-white fw-light" style="text-shadow: 0 2px 10px rgba(0,0,0,0.2);">
                                                Menyiapkan Audio...
                                            </h3>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-center gap-4 pb-4" style="position: relative; z-index: 20;">
                                            <button type="button" class="btn btn-link text-white p-0" id="btn-rewind" title="-10 Detik">
                                                <i class="ph-rewind" style="font-size: 32px;"></i>
                                            </button>
                                            <div id="play-pause-wrapper" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,0.2); border-radius: 50%; backdrop-filter: blur(5px); border: 2px solid rgba(255,255,255,0.5); cursor: pointer;transition: transform 0.2s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.2);" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                                                <i id="icon-play" class="ph-play text-white" style="font-size: 40px; margin-left: 4px;"></i>
                                                <i id="icon-pause" class="ph-pause text-white" style="font-size: 40px; display: none;"></i>
                                            </div>
                                            <button type="button" class="btn btn-link text-white p-0" id="btn-forward" title="+10 Detik">
                                                <i class="ph-fast-forward" style="font-size: 32px;"></i>
                                            </button>
                                        </div>
                                        <div style="position: absolute; bottom: 30px; right: 30px;">
                                            <button type="button" id="btn-mute" class="btn btn-link text-white p-0" style="opacity: 0.6;">
                                                <i class="ph-speaker-high" id="icon-vol" style="font-size: 24px;"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div id="default-message" style="display: flex; height: 800px; align-items: center; justify-content: center;">
                                    <span class="text-muted">Memuat file...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="hstack gap-2 mb-0">File Cover</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-2">
                            <div><b>Hash :</b> {{ $collection->HASH_CATALOGCOVERS ?? '' }}</div>
                            <div><b>Mime Type :</b> {{ $collection->MIME_CATALOGCOVERS ?? '' }}</div>
                            <div><b>Ukuran :</b> {{ Main::formatFileSize($collection->FILE_SIZE_CATALOGCOVERS ?? 0) }}</div>
                            <div><b>Metode :</b> {{ Main::method($collection->METHOD_CATALOGCOVERS ?? 0) }}</div>
                        </div>
                        <img src="{{ url('stream-file') }}?type=cover&id={{ $collection->ID_CATALOGCOVERS ?? '' }}&filename={{ $collection->FILEURL_CATALOGCOVERS ?? '' }}" class="img-fluid w-100" style="object-fit: contain; max-height: 600px;" alt="Cover Catalog">
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
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
            <div class="card">
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
                            Simpan & Verifikasi
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </form>
</div>

<script>
    let currentSound = null;
    let watermarkSound = null;
    let animationFrameId = null;
    let audioAnimFrame = null;

    $(function() {
        datePickerSingle('#received_at, #publish_time, #edition_date');

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

    $(document).ready(function() {
        const fileUrl = "{{ url('stream-file') }}?type=konten_digital&id={{ $collection->ID_CATALOGFILES ?? '' }}&filename={{ $collection->FILEURL_CATALOGFILES ?? '' }}";
        const rawFilename = "{{ $collection->FILEURL_CATALOGFILES ?? '' }}";
        const fileExtension = rawFilename.split('.').pop().toLowerCase();

        if(rawFilename) {
            loadUniversalViewer(fileUrl, fileExtension);
        } else {
            $('#default-message span').text('File konten tidak tersedia.');
        }
    });

    function loadUniversalViewer(url, ext) {
        if (typeof audioAnimFrame !== 'undefined' && audioAnimFrame) {
            cancelAnimationFrame(audioAnimFrame);
        }

        if (animationFrameId) {
            cancelAnimationFrame(animationFrameId);
        }

        if (currentSound) {
            currentSound.stop();
            currentSound.unload();

            currentSound = null;
        }

        if (watermarkSound) {
            watermarkSound.unload();

            watermarkSound = null;
        }

        $('#pdf-canvas, #epub-container, #audio-wrapper, #default-message').hide();
        $('#video-player').hide();
        $('#watermark-overlay').css('display', 'flex');

        switch (ext) {
            case 'pdf':
                renderPdf(url);

                break;
            case 'mp4':
                renderVideo(url, ext);

                break;
            case 'webm':
                renderVideo(url, ext);

                break;
            case 'ogg':
                renderAudio(url, ext);

                break;
            case 'epub':
                renderEpub(url);

                break;
            case 'mp3':
                renderAudio(url, ext);

                break;
            case 'wav':
                renderAudio(url, ext);

                break;
            default:
                $('#default-message').show().find('span').text('Preview tidak didukung untuk format: .' + ext);
                $('#watermark-overlay').hide();

                break;
        }
    }

    function renderPdf(url) {
        const $container = $('#pdf-viewer-container');

        $container.css({
            'display': 'block',
            'width': '100%',
            'height': '800px',
            'max-height': '800px',
            'overflow-y': 'auto',
            'overflow-x': 'hidden',
            'padding': '20px 0',
            'background': '#525659',
            'box-sizing': 'border-box'
        }).empty();

        $('#video-player, #epub-container, #audio-wrapper, #default-message').hide();
        $('#watermark-overlay').hide();

        const checkPdfLib = setInterval(function() {
            if (window.pdfjsLib) {
                clearInterval(checkPdfLib);

                window.pdfjsLib.getDocument(url).promise.then(function(pdf) {
                    for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                        renderPage(pdf, pageNum, $container);
                    }
                }).catch(function(error) {
                    $('#default-message').show().find('span').text('Gagal memuat PDF.');

                    $container.hide();
                });
            }
        }, 100);
    }

    function renderPage(pdf, pageNumber, $container) {
        pdf.getPage(pageNumber).then(function(page) {
            let availableWidth = ($container[0].clientWidth || 800) - 40;

            if (availableWidth < 300) availableWidth = 300;

            var unscaledViewport = page.getViewport({ scale: 1 });
            var scale = availableWidth / unscaledViewport.width;
            const viewport = page.getViewport({ scale: scale });

            const $pageWrapper = $('<div/>', {
                class: 'pdf-page-wrapper',
                style: `
                    position: relative;
                    margin: 0 auto 20px auto;
                    display: block;
                    overflow: hidden;
                    box-shadow: 0 4px 8px rgba(0,0,0,0.5);
                    width: ${viewport.width}px;
                    height: ${viewport.height}px;
                    background-color: white;
                `
            });

            const canvasId = 'pdf-page-' + pageNumber;
            const $canvas = $('<canvas/>', { id: canvasId });
            const canvas = $canvas[0];
            const context = canvas.getContext('2d');

            canvas.height = viewport.height;
            canvas.width = viewport.width;

            const watermarkHtml = `
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 10;">
                    <div style="position: absolute; bottom: 10px; left: 63px;  transform: rotate(-90deg); transform-origin: bottom left; width: ${viewport.height - 40}px; text-align: left; white-space: nowrap; opacity: 0.4; color: #333; font-family: 'Arial', sans-serif; font-size: 13px;  line-height: 1.5;">
                        <div style="font-weight: bold; text-transform: uppercase;">
                            Pelaksanaan Undang - Undang Nomor 13 Tahun 2018.
                        </div>
                        <div style="font-weight: bold; text-transform: uppercase;">
                            Tentang Serah Simpan Karya Cetak dan Karya Rekam
                        </div>
                        <div style="color: #d9534f; font-weight: bold; display: inline-block;">
                            Peringatan : Dilarang menggandakan, mencetak, mengunduh, atau mendistribusikan kembali tanpa izin.
                        </div>
                    </div>
                </div>
            `;

            $pageWrapper.append($canvas);
            $pageWrapper.append(watermarkHtml);
            $container.append($pageWrapper);

            const renderContext = {
                canvasContext: context,
                viewport: viewport
            };
            page.render(renderContext);
        });
    }

    function renderVideo(url, ext) {
        Howler.unload();

        if (typeof audioAnimFrame !== 'undefined' && audioAnimFrame)  {
            cancelAnimationFrame(audioAnimFrame);
        }

        if (animationFrameId) {
            cancelAnimationFrame(animationFrameId);
        }

        const watermarkSrc = "{{ asset('assets/audio-wm.mp3') }}";

        $('#pdf-canvas, #epub-container, #audio-wrapper, #default-message').hide();

        const $videoEl = $('#video-player');

        $videoEl.show().css({ 'width': '100%', 'height': '800px' });

        $('#video-watermark-layer').remove();

        watermarkSound = new Howl({
            src: [watermarkSrc],
            html5: true,
            loop: true,
            volume: 0.2,
            preload: true
        });

        const syncVideoWatermark = () => {
            if (!watermarkSound || !window.player) return;

            const wmDur = watermarkSound.duration();

            if (wmDur > 0) {
                const currentTime = window.player.currentTime();
                const targetPos = currentTime % wmDur;

                if (Math.abs(watermarkSound.seek() - targetPos) > 0.5) {
                    watermarkSound.seek(targetPos);
                }

                if (!window.player.paused() && !watermarkSound.playing()) {
                    watermarkSound.play();
                }
            }
        };

        if (window.player) {
            window.player.dispose();
        }

        window.player = videojs('video-player', {
            controls: true,
            preload: 'auto',
            fluid: false,
            height: 600,
            width: '100%',
            sources: [
                {
                    src: url,
                    type: 'video/' + (ext === 'mov' ? 'mp4' : ext)
                }
            ]
        });

        window.player.ready(function() {
            window.player.addClass('vjs-matrix');

            const watermarkHtml = `
                <div id="video-watermark-layer" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 20; overflow: hidden;">
                    <div style="position: absolute; bottom: 30px; left: 65px; width: 800px; transform: rotate(-90deg); transform-origin: bottom left; text-align: left;">
                        <div style="font-family: 'Arial', sans-serif; font-size: 11px; line-height: 1.5; color: #fff; opacity: 0.7; text-shadow: 2px 2px 4px rgba(0,0,0,0.9); white-space: nowrap;">
                            <span style="font-weight: bold; text-transform: uppercase;">
                                Pelaksanaan Undang - Undang Nomor 13 Tahun 2018
                            </span><br>
                            <span style="font-weight: bold; text-transform: uppercase;">
                                Tentang Serah Simpan Karya Cetak dan Karya Rekam
                            </span><br>
                            <span style="color: #ff6b6b; font-weight: bold; text-shadow: 1px 1px 2px #000;">
                                Peringatan : Dilarang menggandakan, mencetak, mengunduh, atau mendistribusikan kembali tanpa izin.
                            </span>
                        </div>
                    </div>
                </div>
            `;

            $(window.player.el()).append(watermarkHtml);
        });

        window.player.on('play', function() {
            watermarkSound.play();

            syncVideoWatermark();
        });

        window.player.on('pause', function() { watermarkSound.pause(); });
        window.player.on('ended', function() { watermarkSound.stop(); });
        window.player.on('seeking', function() { syncVideoWatermark(); });

        $('#video-player').on('contextmenu', () => false);
    }

    function renderEpub(url) {
        const $container = $('#epub-container');

        $container
            .show()
            .css({
                'display': 'block',
                'overflow-y': 'hidden',
                'position': 'relative'
            })
            .empty();

        $('#watermark-overlay').css('display', 'flex');
        $('#epub-controls').css('display', 'none');
        $('#epub-watermark-layer').remove();

        if (window.ePub) {
            const book = window.ePub(url);

            const rendition = book.renderTo("epub-container", {
                width: '100%',
                height: '100%',
                flow: 'scrolled-doc',
                allowScriptedContent: true
            });

            rendition.display().then(() => {
                $('#epub-controls').css('display', 'flex');
                $('#watermark-overlay').hide();

                const containerHeight = $container.height();

                const watermarkHtml = `
                    <div id="epub-watermark-layer" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 999;">
                        <div style="position: absolute; bottom: 10px; left: 60px; transform: rotate(-90deg); transform-origin: bottom left; width: ${containerHeight - 40}px; text-align: left; white-space: nowrap; opacity: 0.4; color: #333; font-family: 'Arial', sans-serif; font-size: 13px; line-height: 1.5;">
                            <div style="font-weight: bold; text-transform: uppercase;">
                                Pelaksanaan Undang - Undang Nomor 13 Tahun 2018
                            </div>
                            <div style="font-weight: bold; text-transform: uppercase;">
                                Tentang Serah Simpan Karya Cetak dan Karya Rekam
                            </div>
                            <div style="color: #d9534f; font-weight: bold; display: inline-block;">
                                Peringatan : Dilarang menggandakan, mencetak, mengunduh, atau mendistribusikan kembali tanpa izin.
                            </div>
                        </div>
                    </div>
                `;

                $container.append(watermarkHtml);

            });

            rendition.themes.default({
                'img': { 'max-width': '100% !important', 'height': 'auto !important' },
                'table': { 'max-width': '100% !important' },
                'p': {
                    'font-family': 'Helvetica, sans-serif',
                    'font-size': '1.1em',
                    'line-height': '1.6',
                    'text-align': 'justify'
                },
                'body': { 'padding': '0 15px !important', 'box-sizing': 'border-box' }
            });

            $('#next-btn').off('click').on('click', function(e) {
                e.preventDefault();
                rendition.next();
            });

            $('#prev-btn').off('click').on('click', function(e) {
                e.preventDefault();
                rendition.prev();
            });

            $(document).off('keyup').on('keyup', function(e) {
                if (e.keyCode == 37) rendition.prev();
                if (e.keyCode == 39) rendition.next();
            });
        }
    }

    function renderAudio(url, formatExt) {
        const watermarkSrc = "{{ asset('assets/audio-wm.mp3') }}";

        Howler.unload();

        if (typeof audioAnimFrame !== 'undefined' && audioAnimFrame) {
            cancelAnimationFrame(audioAnimFrame);
        }

        let cleanExt = (formatExt || 'mp3').toString().replace('.', '').toLowerCase().trim();
        const $wrapper = $('#audio-wrapper');
        const $playWrapper = $('#play-pause-wrapper');
        const $iconPlay = $('#icon-play');
        const $iconPause = $('#icon-pause');
        const $title = $('#audio-title-display');
        const canvas = document.getElementById('wave-canvas');
        const ctx = canvas.getContext('2d');

        $wrapper.fadeIn();

        $('#watermark-overlay').hide();

        $title.text('Menyiapkan...');
        $iconPlay.show(); $iconPause.hide();

        const syncWatermark = () => {
            if (!watermarkSound || !currentSound) return;
            if (!currentSound.playing()) return;

            const wmDur = watermarkSound.duration();

            if (wmDur > 0) {
                const targetPos = currentSound.seek() % wmDur;
                const currentWmPos = watermarkSound.seek();

                if (Math.abs(currentWmPos - targetPos) > 0.3) {
                    watermarkSound.seek(targetPos);
                }

                if (!watermarkSound.playing()) {
                    watermarkSound.play();
                }
            }
        };

        watermarkSound = new Howl({
            src: [watermarkSrc],
            html5: true,
            loop: true,
            preload: true,
            volume: 0.3,
        });

        currentSound = new Howl({
            src: [url],
            html5: true,
            format: [cleanExt, 'mp3'],
            xhr: { method: 'GET', withCredentials: true },

            onload: function() {
                $title.text('Audio Siap');

                $('#timer-duration').text(formatTime(currentSound.duration()));

                if(!audioAnimFrame) loopAnimation();
            },
            onloaderror: function(id, err) {
                $title.text('Gagal Memuat');
            },
            onplay: function() {
                watermarkSound.play();

                syncWatermark();

                $iconPlay.hide(); $iconPause.show(); $title.text('Sedang Memutar');
            },
            onpause: function() {
                watermarkSound.pause();
                $iconPlay.show(); $iconPause.hide(); $title.text('Dijeda');
            },
            onseek: function() {
                syncWatermark();
            },
            onend: function() {
                watermarkSound.stop();
                $iconPlay.show(); $iconPause.hide(); $title.text('Selesai');
            }
        });

        let wavePhase = 0;

        const loopAnimation = () => {
            if (currentSound && currentSound.playing()) {
                $('#timer-current').text(formatTime(currentSound.seek() || 0));
            }

            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
            const w = canvas.width, h = canvas.height, mid = h / 2;

            ctx.clearRect(0, 0, w, h);

            const speed = (currentSound && currentSound.playing()) ? 0.1 : 0.02;

            wavePhase += speed;

            const drawSine = (color, offset, amp) => {
                ctx.beginPath(); ctx.strokeStyle = color; ctx.lineWidth = 2;
                for (let x = 0; x < w; x++) {
                    const y = mid + Math.sin(x * 0.01 + wavePhase + offset) * (50 * amp) * Math.sin(wavePhase * 0.5);
                    ctx.lineTo(x, y);
                }
                ctx.stroke();
            };

            drawSine('rgba(255,255,255,0.3)', 0, 1);
            drawSine('rgba(255,255,255,0.8)', 2, 0.8);

            audioAnimFrame = requestAnimationFrame(loopAnimation);
        };

        const formatTime = (s) => {
            if(isNaN(s)) return "0:00";

            let m = Math.floor(s/60), sec = Math.floor(s%60);

            return m + ':' + (sec<10?'0':'')+sec;
        }

        $playWrapper.off().on('click', function() {
            if (!currentSound) return;

            if (currentSound.playing()) {
                currentSound.pause();
            } else {
                if (Howler.ctx && Howler.ctx.state !== 'running') Howler.ctx.resume();

                currentSound.play();
            }
        });

        $('#btn-mute').off().on('click', function() {
            const muted = !currentSound.mute();

            currentSound.mute(muted);
            watermarkSound.mute(muted);

            $('#icon-vol').attr('class', muted ? 'ph-speaker-slash' : 'ph-speaker-high');
        });

        loopAnimation();
    }

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
