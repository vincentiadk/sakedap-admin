<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Laporan - Koleksi - <span class="fw-normal">Detail</span>
            </h4>
        </div>
        <div class="collapse d-lg-block my-lg-auto ms-lg-auto" id="page-header">
            <div class="d-sm-flex align-items-center mb-3 mb-lg-0 ms-lg-3">
                <div class="d-inline-flex mt-3 mt-sm-0">
                    <a href="{{ url('report/collection') }}" class="btn btn-primary">
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
            <div class="card-header">
                <h5 class="hstack gap-2 mb-0">Pelaksana Serah</h5>
            </div>
            <div class="card-body">
                {{ $collection->PENERBIT_ID . ' | ' . $collection->NAME_PENERBIT }}
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h5 class="hstack gap-2 mb-0">Meta Data</h5>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Jenis Bahan</label>
                    <div class="col-md-10">
                        <select class="form-select select2-basic" name="worksheet_id" id="worksheet_id" disabled>
                            <option value=""></option>
                            @foreach($worksheet as $w)
                                <option value="{{ $w->ID }}" {{ $collection->WORKSHEET_ID == $w->ID ? 'selected' : '' }}>{{ $w->NAME }} [{{ $w->DEPOSITFORMAT_CODE }}]</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Judul</label>
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="title" id="title" value="{{ $collection->TITLE }}" placeholder="...................." disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Kode</label>
                    <div class="col-md-10">
                        <div class="input-group">
                            <select class="form-select w-auto flex-grow-0" name="code_type" id="code_type" disabled>
                                <option value="">Tidak Ada</option>
                                <option value="1" {{ $collection->CODE_TYPE_E_COLLECTION == 1 ? 'selected' : ''  }}>ISBN</option>
                                <option value="2" {{ $collection->CODE_TYPE_E_COLLECTION == 2 ? 'selected' : ''  }}>ISMN</option>
                                <option value="3" {{ $collection->CODE_TYPE_E_COLLECTION == 3 ? 'selected' : ''  }}>ISRC</option>
                                <option value="4" {{ $collection->CODE_TYPE_E_COLLECTION == 4 ? 'selected' : ''  }}>ISSN</option>
                                <option value="5" {{ $collection->CODE_TYPE_E_COLLECTION == 5 ? 'selected' : ''  }}>ISAN</option>
                            </select>
                            <input type="text" class="form-control" name="code" id="code" value="{{ empty($collection->ISBN) ? '-' : $collection->ISBN }}" placeholder="...................." disabled>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Kota</label>
                    <div class="col-md-10">
                        <select class="form-select" name="city_id" id="city_id" disabled>
                            <option value="{{ $collection->CITY_ID }}" selected>
                                {{ $collection->NAMAPROPINSI }} -> {{ $collection->NAMAKAB }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Media</label>
                    <div class="col-md-10">
                        <select class="form-select select2-basic" name="collection_media_id" id="collection_media_id" disabled>
                            <option value=""></option>
                            @foreach($media as $m)
                                <option value="{{ $m->ID }}" {{ $collection->COLLECTIONMEDIA_ID == $m->ID ? 'selected' : '' }}>{{ $m->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Seri</label>
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text">
                                <label>
                                    <input type="checkbox" class="form-check-input mt-0 me-1" onchange="$(this).is(':checked') ? $('#series').attr('disabled', true) : $('#series').attr('disabled', false)" @if(empty($collection->SERIES)) checked @endif disabled>
                                    Tidak Ada
                                </label>
                            </span>
                            <input type="text" class="form-control" name="series" id="series" value="{{ $collection->SERIES }}" placeholder="...................." @if(empty($collection->SERIES)) disabled @endif>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">DDC</label>
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text">
                                <label>
                                    <input type="checkbox" class="form-check-input mt-0 me-1" onchange="$(this).is(':checked') ? $('#ddc').attr('disabled', true) : $('#ddc').attr('disabled', false)"  @if(empty($collection->DEWEYNO)) checked @endif disabled>
                                    Tidak Ada
                                </label>
                            </span>
                            <input type="text" class="form-control" name="ddc" id="ddc" value="{{ $collection->DEWEYNO }}" placeholder="...................."  @if(empty($collection->DEWEYNO)) disabled @endif>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Kala Terbit</label>
                    <div class="col-md-10">
                        <select class="form-select select2-basic" name="serial" id="serial" data-placeholder="Tidak Ada" disabled>
                            <option value=""></option>
                            <option value="1" {{ $collection->SERIAL_E_COLLECTION == 1 ? 'selected' : '' }}>Harian</option>
                            <option value="2" {{ $collection->SERIAL_E_COLLECTION == 2 ? 'selected' : '' }}>Mingguan</option>
                            <option value="3" {{ $collection->SERIAL_E_COLLECTION == 3 ? 'selected' : '' }}>Bulanan</option>
                            <option value="4" {{ $collection->SERIAL_E_COLLECTION == 4 ? 'selected' : '' }}>3 Bulan Sekali</option>
                            <option value="5" {{ $collection->SERIAL_E_COLLECTION == 5 ? 'selected' : '' }}>4 Bulan Sekali</option>
                            <option value="6" {{ $collection->SERIAL_E_COLLECTION == 6 ? 'selected' : '' }}>6 Bulan Sekali</option>
                            <option value="7" {{ $collection->SERIAL_E_COLLECTION == 7 ? 'selected' : '' }}>Tahunan</option>
                            <option value="8" {{ $collection->SERIAL_E_COLLECTION == 8 ? 'selected' : '' }}>2 Tahun Sekali</option>
                            <option value="9" {{ $collection->SERIAL_E_COLLECTION == 9 ? 'selected' : '' }}>3 Tahun Sekali</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Waktu Publish</label>
                    <div class="col-md-10">
                        <input type="month" class="form-control" name="publish_time" id="publish_time" value="{{ $collection->PUBLISHYEAR . '-' . $collection->PUBLISH_MONTH }}" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Tanggal Terima</label>
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="received_at" id="received_at" value="{{ Carbon::parse($collection->RECEIVED_AT_E_COLLECTION)->format('Y/m/d') }}" placeholder="Pilih Tanggal" readonly disabled>
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
                        <select class="form-select" name="currency" id="currency" disabled>
                            @if($collection->CURRENCY_E_COLLECTION)
                                <option value="{{ $collection->CURRENCY_E_COLLECTION }}" selected>{{ $collection->CURRENCY_E_COLLECTION }}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Harga</label>
                    <div class="col-md-10">
                        <input type="number" class="form-control" name="price" id="price" value="{{ $collection->PRICE_E_COLLECTION }}" placeholder="...................." disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Jilid</label>
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="binding" id="binding" value="{{ $collection->JILID_E_COLLECTION }}" placeholder="...................." disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Jenis Isi</label>
                    <div class="col-md-10">
                        <select class="form-select" name="content_type" id="content_type" disabled>
                            <option value=""></option>
                            @foreach($contentType as $ct)
                                <option value="{{ $ct->NAME }}" {{ $collection->JENIS_ISI_E_COLLECTION == $ct->NAME ? 'selected' : '' }}>{{ $ct->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Jenis Wadah</label>
                    <div class="col-md-10">
                        <select class="form-select" name="container_type" id="container_type" disabled>
                            <option value=""></option>
                            @foreach($containerType as $ct)
                                <option value="{{ $ct->NAME }}" {{ $collection->JENIS_WADAH_E_COLLECTION == $ct->NAME ? 'selected' : '' }}>{{ $ct->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Jenis Media</label>
                    <div class="col-md-10">
                        <select class="form-select" name="media_type" id="media_type" disabled>
                            <option value=""></option>
                            @foreach($mediaType as $mt)
                                <option value="{{ $mt->NAME }}" {{ $collection->JENIS_MEDIA_E_COLLECTION == $ct->NAME ? 'selected' : '' }}>{{ $mt->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Keterangan Fisik</label>
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text">Total Halaman / Durasi</span>
                            <input type="text" class="form-control" name="physical_description[paging]" id="physical_description[paging]" value="{{ isset($collection->PAGING) ? $collection->PAGING : '' }}" placeholder="...................." disabled>
                            <span class="input-group-text">Ilustrasi</span>
                            <input type="text" class="form-control" name="physical_description[ill]" id="physical_description[ill]" value="{{ isset($collection->ILL) ? $collection->ILL : '' }}" placeholder="...................." disabled>
                            <span class="input-group-text">Ukuran / Dimensi</span>
                            <input type="text" class="form-control" name="physical_description[sizes]" id="physical_description[sizes]" value="{{ isset($collection->SIZES) ? $collection->SIZES : '' }}" placeholder="...................." disabled>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Sinopsis</label>
                    <div class="col-md-10">
                        <textarea name="sinopsis" class="form-control" id="sinopsis" rows="5" placeholder="...................." disabled>{{ $collection->DESCRIPTION_E_COLLECTION }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h5 class="hstack gap-2 mb-0">Kategori</h5>
            </div>
            <div class="card-body">
                <select class="form-select select2-basic" name="category[]" id="category" data-placeholder="Tidak ada" multiple disabled>
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
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <tbody>
                            @if($collectionContributor)
                                @foreach($collectionContributor as $cc)
                                    <tr>
                                        <td>{{ empty($cc) ? 'Format tidak valid' : $cc }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td>Tidak ada data</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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
                                        <td>{{ $cc->DATE ? Carbon::parse($cc->DATE)->isoFormat('dddd, D MMMM Y') : '' }}</td>
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
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="hstack gap-2 mb-0">File Cover</h5>
                    </div>
                    <div class="card-body">
                        <a href="{{ url('stream-file') }}?type=cover&id={{ $collectionCover->ID ?? '' }}&filename={{ $collectionCover->FILEURL ?? '' }}" class="ratio ratio-16x9" data-lightbox="Cover" data-title="{{ $collectionCover->FILEURL ?? '' }}">
                            <img src="{{ url('stream-file') }}?type=cover&id={{ $collectionCover->ID ?? '' }}&filename={{ $collectionCover->FILEURL ?? '' }}" class="img-fluid ratio ratio-16x9">
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="hstack gap-2 mb-0">File Original</h5>
                    </div>
                    <div class="card-body">
                        <div class="ratio ratio-16x9">
                            <iframe src="{{ url('stream-file') }}?type=konten_digital&id={{ $collectionOriginal->ID ?? '' }}&filename={{ $collectionOriginal->FILEURL ?? '' }}" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="hstack gap-2 mb-0">File Preview</h5>
                    </div>
                    <div class="card-body">
                        <div class="ratio ratio-16x9">
                            <iframe src="{{ url('stream-file') }}?type=file_preview&id={{ $collectionPreview->ID ?? '' }}&filename={{ $collectionPreview->FILEURL ?? '' }}" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="hstack gap-2 mb-0">File Watermark</h5>
                    </div>
                    <div class="card-body">
                        <div class="ratio ratio-16x9">
                            <iframe src="{{ url('stream-file') }}?type=file_access&id={{ $collectionWatermark->ID ?? '' }}&filename={{ $collectionWatermark->FILEURL ?? '' }}" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(function() {
        $('#price').number(true);
    });
</script>
