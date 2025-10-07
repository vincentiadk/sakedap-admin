<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Koleksi - Peninjauan - <span class="fw-normal">Detail</span>
            </h4>
        </div>
        <div class="collapse d-lg-block my-lg-auto ms-lg-auto" id="page-header">
            <div class="d-sm-flex align-items-center mb-3 mb-lg-0 ms-lg-3">
                <div class="d-inline-flex mt-3 mt-sm-0">
                    <a href="{{ url('collection/review') }}" class="btn btn-primary">
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
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Jenis Bahan <span class="text-danger fw-bold">*</span></label>
                    <div class="col-md-10">
                        <select class="form-select select2-basic" name="worksheet_id" id="worksheet_id">
                            <option value=""></option>
                            @foreach($worksheet as $w)
                                <option value="{{ $w->ID }}" {{ $collection->WORKSHEET_ID == $w->ID ? 'selected' : '' }}>{{ $w->NAME }} [{{ $w->DEPOSITFORMAT_CODE }}]</option>
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
                                <option value="{{ $m->ID }}" {{ $collection->COLLECTION_MEDIA_ID == $m->ID ? 'selected' : '' }}>{{ $m->NAME }}</option>
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
                                    <input type="checkbox" class="form-check-input mt-0 me-1" onchange="$(this).is(':checked') ? $('#series').attr('disabled', true) : $('#series').attr('disabled', false)" @if(empty($collection->SERIES)) checked @endif>
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
                                    <input type="checkbox" class="form-check-input mt-0 me-1" onchange="$(this).is(':checked') ? $('#ddc').attr('disabled', true) : $('#ddc').attr('disabled', false)"  @if(empty($collection->DDC)) checked @endif>
                                    Tidak Ada
                                </label>
                            </span>
                            <input type="text" class="form-control" name="ddc" id="ddc" value="{{ $collection->DDC }}" placeholder="...................."  @if(empty($collection->DDC)) disabled @endif>
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
                    <label class="col-form-label col-md-2">Waktu Publish</label>
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
                                <option value="{{ $mt->NAME }}" {{ $collection->JENIS_MEDIA == $ct->NAME ? 'selected' : '' }}>{{ $mt->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-form-label col-md-2">Keterangan Fisik</label>
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text">Total Halaman / Durasi</span>
                            <input type="text" class="form-control" name="physical_description[paging]" id="physical_description[paging]" value="{{ isset($physicalDescription->paging) ? $physicalDescription->paging : '' }}" placeholder="....................">
                            <span class="input-group-text">Ilustrasi</span>
                            <input type="text" class="form-control" name="physical_description[ill]" id="physical_description[ill]" value="{{ isset($physicalDescription->ill) ? $physicalDescription->ill : '' }}" placeholder="....................">
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
                        <h5 class="hstack gap-2 mb-0">File Konten</h5>
                    </div>
                    <div class="card-body">
                        <div class="ratio ratio-16x9">
                            <iframe src="{{ url('stream-file') }}?type=konten_digital&id={{ $collectionContent->ID ?? '' }}&filename={{ $collectionContent->FILEURL ?? '' }}" frameborder="0"></iframe>
                        </div>
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
    </form>
    <div class="card">
        <div class="card-body">
            <div class="text-end">
                <button type="button" class="btn btn-primary" onclick="submitted()">
                    <i class="ph-floppy-disk me-1"></i>
                    Simpan Data
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        datePickerSingle('#received_at');

        if(parseInt('{{ Main::isNotCenterBranch() }}') === 1) {
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

            select2Serverside('#branch_id', 'city', {
                for: 'city'
            });
        }

        select2Serverside('#currency', 'currency');

        $('#author').select2({
            multiple: true,
            tags: true,
            tokenSeparators: [';', ' ']
        });
    });

    function addContributor() {
        var total = $('#add-number-contributor').val();

        for(var i = 1; i <= total; i++) {
            $('#data-contributor').append(`
                <tr>
                    <input type="hidden" name="cc_contributor[]" value="1">
                    <td>
                        <select class="form-select select2-basic" name="cc_contributor_role[]">
                            @foreach($contributor as $key => $c)
                                <option value="{{ $c->NAME }}" {{ $key == 0 ? 'selected' : '' }}>{{ $c->NAME }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="text" class="form-control" name="cc_contributor_name[]" placeholder="Nama">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger col-12" onclick="removeRow(this)"><i class="ph-trash"></i></button>
                    </td>
                </tr>
            `);

            $('select[name="cc_contributor_id[]"]').select2({
                placeholder: 'Pilih'
            });
        }
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

    function submitted() {
        $.ajax({
            url: '{{ url("collection/review/detail/" . $collection->ID) }}',
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

                            location.href = '{{ url("collection/review") }}';
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
