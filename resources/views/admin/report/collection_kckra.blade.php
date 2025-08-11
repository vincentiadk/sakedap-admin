<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <h3 class="content-header-title mb-1 d-inline-block">Laporan Koleksi KCKRA </h3><br>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Laporan</a></li>
                            <li class="breadcrumb-item active">Koleksi KCKRA</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-header-right col-md-6 col-12 mb-2 mt-1">
                <div class="float-md-right">
                    <button type="button" class="btn btn-danger btn-sm" onclick="filter()"><i
                            class="la la-refresh"></i> Reset Filter</button>
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                        data-target="#modal_filter"><i class="la la-search"></i> Filter</button>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <section id="configuration">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="card-title">
                                        Daftar Koleksi
                                    </h4>
                                </div>
                                <div class="col-md-6 text-right">
                                    <h4 class="card-title">
                                        <a href="#" class="btn btn-success btn-sm" id="download_excel"
                                            onclick="downloadExcel()"><i class="la la-folder"></i> Download Excel</a>
                                    </h4>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-justified">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" aria-controls="tab_detail"
                                        href="#tab_detail" aria-expanded="true">Detail</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" aria-controls="tab_summary"
                                        href="#tab_summary" aria-expanded="false">Summary</a>
                                </li>
                            </ul>
                            <div class="tab-content px-1 pt-1">
                                <div role="tabpanel" class="tab-pane active" id="tab_detail" aria-expanded="true">
                                    <p>
                                    <table class="table table-striped table-bordered display nowrap"
                                        id="datatable_serverside_detail">
                                        <thead class="text-center">
                                            <tr>
                                                <th>No</th>
                                                <th>Aksi</th>
                                                <th>Penerbit</th>
                                                <th>Periode</th>
                                                <th>Provinsi</th>
                                                <th>Kota</th>
                                                <th>Judul</th>
                                                <th>Jenis</th>
                                                <th>Album</th>
                                                <th>Seri</th>
                                                <th>Edisi</th>
                                                <th>Serial</th>
                                                <th>Kode</th>
                                                <th>Deposit</th>
                                                <th>Mark</th>
                                                <th>Tahun Terbit</th>
                                                <th>Lokasi</th>
                                                <th>Availability</th>
                                                <th>Kondisi</th>
                                                <th>Kunci</th>
                                                <th>Manual</th>
                                                <th>Tanggal Diserahkan</th>
                                                <th>Tanggal Terima</th>
                                                <th>Tanda Terima</th>
                                            </tr>
                                        </thead>
                                    </table>
                                    </p>
                                </div>
                                <div class="tab-pane" id="tab_summary">
                                    <p>
                                    <table class="table table-striped table-bordered display nowrap"
                                        id="datatable_serverside_summary">
                                        <thead class="text-center">
                                            <tr>
                                                <th>No</th>
                                                <th>Jenis</th>
                                                <th>Total Diserahkan</th>
                                                <th>Total Diterima</th>
                                                <th>Total Ditolak</th>
                                                <th>Total Data</th>
                                            </tr>
                                        </thead>
                                    </table>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
</div>

<div class="modal fade" id="modal_filter" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Filter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-md-2">Penerbit</label>
                            <div class="col-md-10">
                                <select name="publisher_id" id="publisher_id" class="form-control"
                                    style="width:100%;"></select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-md-2">Jenis</label>
                            <div class="col-md-10">
                                <select name="type" id="type" class="form-control select2" style="width:100%;"
                                    multiple>
                                    <option value="">Semua</option>
                                    @foreach ($deposit_head as $category => $items)
                                        <optgroup label="{{ $category_dh[$category] }}">
                                            @foreach ($items as $item)
                                                <option value="{{ $item->id }}">{{ $item->shape }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-md-2">Lokasi :</label>
                            <div class="col-md-10">
                                <select name="lib_loc_id" id="lib_loc_id" class="form-control select2"
                                    style="width:100%;">
                                    <option></option>
                                    @foreach ($lib_loc as $key => $value)
                                        <option value="{{ $value->id }}">
                                            {{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-md-2">Kondisi</label>
                            <div class="col-md-10">
                                <select name="condition" id="condition" class="form-control select2"
                                    style="width:100%;">
                                    <option value="">Semua</option>
                                    @foreach ($condition as $key => $value)
                                        <option value="{{ $key }}">
                                            {{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-md-2">Availability</label>
                            <div class="col-md-10">
                                <select name="availability" id="availability" class="form-control select2"
                                    style="width:100%;">
                                    <option value="">Semua</option>
                                    @foreach ($availability as $key => $value)
                                        <option value="{{ $key }}">
                                            {{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-md-2">Provinsi</label>
                            <div class="col-md-10">
                                <select name="province_id" id="province_id" class="form-control"
                                    style="width:100%;"></select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-md-2">Jenis Periode</label>
                            <div class="col-md-10">
                                <select name="type_date" id="type_date" class="form-control" style="width:100%;">
                                    <option value="created_at">Tanggal diunggah / dibuat</option>
                                    <option value="received_at">Tanggal diterima</option>
                                    <option value="updated_at">Tanggal ditolak / bermasalah</option>
                                    <option value="validated_at">Tanggal divalidasi / dilock</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-md-2">Status</label>
                            <div class="col-md-10">
                                <select name="status" id="status" class="form-control" style="width:100%;">
                                    <option value="">Semua</option>
                                    <option value="1">Review</option>
                                    <option value="2">Diterima</option>
                                    <option value="3">Masalah</option>
                                    <option value="4">Per Proses</option>
                                    <option value="5">Ditolak</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-md-2">Tahunan</label>
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-md-4">
                                        <select name="year_start" id="year_start" class="form-control">
                                            <option value="">-- Pilih --</option>
                                            @for ($i = 2018; $i <= date('Y'); $i++)
                                                <option value="{{ $i }}"
                                                    {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        <p style="line-height:40px;" class="text-center">s/d</p>
                                    </div>
                                    <div class="col-md-4">
                                        <select name="year_end" id="year_end" class="form-control">
                                            <option value="">-- Pilih --</option>
                                            @for ($i = 2018; $i <= date('Y'); $i++)
                                                <option value="{{ $i }}"
                                                    {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-primary col-12"
                                                onclick="filter('annual')"><i class="la la-search"></i> Cari</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-md-2">Bulanan</label>
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <select name="month_start" id="month_start" class="form-control">
                                                    <option value="">-- Pilih --</option>
                                                    <option value="01">
                                                        {{ App\Helper\GeneralHelper::getMonth('01') }}</option>
                                                    <option value="02">
                                                        {{ App\Helper\GeneralHelper::getMonth('02') }}</option>
                                                    <option value="03">
                                                        {{ App\Helper\GeneralHelper::getMonth('03') }}</option>
                                                    <option value="04">
                                                        {{ App\Helper\GeneralHelper::getMonth('04') }}</option>
                                                    <option value="05">
                                                        {{ App\Helper\GeneralHelper::getMonth('05') }}</option>
                                                    <option value="06">
                                                        {{ App\Helper\GeneralHelper::getMonth('06') }}</option>
                                                    <option value="07">
                                                        {{ App\Helper\GeneralHelper::getMonth('07') }}</option>
                                                    <option value="08">
                                                        {{ App\Helper\GeneralHelper::getMonth('08') }}</option>
                                                    <option value="09">
                                                        {{ App\Helper\GeneralHelper::getMonth('09') }}</option>
                                                    <option value="10">
                                                        {{ App\Helper\GeneralHelper::getMonth('10') }}</option>
                                                    <option value="11">
                                                        {{ App\Helper\GeneralHelper::getMonth('11') }}</option>
                                                    <option value="12">
                                                        {{ App\Helper\GeneralHelper::getMonth('12') }}</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <select name="month_year_start" id="month_year_start"
                                                    class="form-control">
                                                    <option value="">-- Pilih --</option>
                                                    @for ($i = 2018; $i <= date('Y'); $i++)
                                                        <option value="{{ $i }}"
                                                            {{ $i == date('Y') ? 'selected' : '' }}>
                                                            {{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <p style="line-height:40px;" class="text-center">s/d</p>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <select name="month_end" id="month_end" class="form-control">
                                                    <option value="">-- Pilih --</option>
                                                    <option value="01">
                                                        {{ App\Helper\GeneralHelper::getMonth('01') }}</option>
                                                    <option value="02">
                                                        {{ App\Helper\GeneralHelper::getMonth('02') }}</option>
                                                    <option value="03">
                                                        {{ App\Helper\GeneralHelper::getMonth('03') }}</option>
                                                    <option value="04">
                                                        {{ App\Helper\GeneralHelper::getMonth('04') }}</option>
                                                    <option value="05">
                                                        {{ App\Helper\GeneralHelper::getMonth('05') }}</option>
                                                    <option value="06">
                                                        {{ App\Helper\GeneralHelper::getMonth('06') }}</option>
                                                    <option value="07">
                                                        {{ App\Helper\GeneralHelper::getMonth('07') }}</option>
                                                    <option value="08">
                                                        {{ App\Helper\GeneralHelper::getMonth('08') }}</option>
                                                    <option value="09">
                                                        {{ App\Helper\GeneralHelper::getMonth('09') }}</option>
                                                    <option value="10">
                                                        {{ App\Helper\GeneralHelper::getMonth('10') }}</option>
                                                    <option value="11">
                                                        {{ App\Helper\GeneralHelper::getMonth('11') }}</option>
                                                    <option value="12">
                                                        {{ App\Helper\GeneralHelper::getMonth('12') }}</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <select name="month_year_end" id="month_year_end"
                                                    class="form-control">
                                                    <option value="">-- Pilih --</option>
                                                    @for ($i = 2018; $i <= date('Y'); $i++)
                                                        <option value="{{ $i }}"
                                                            {{ $i == date('Y') ? 'selected' : '' }}>
                                                            {{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-primary col-12"
                                                onclick="filter('monthly')"><i class="la la-search"></i> Cari</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-md-2">Harian</label>
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="date" name="day_start" id="day_start" class="form-control"
                                            max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-1">
                                        <p style="line-height:40px;" class="text-center">s/d</p>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="date" name="day_end" id="day_end" class="form-control"
                                            max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-primary col-12"
                                                onclick="filter('daily')"><i class="la la-search"></i> Cari</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        select2AutoSuggest('#extension', 'load_extension');
        select2AutoSuggest('#publisher_id', 'load_publisher');
        select2AutoSuggest('#province_id', 'load_province');
        filter();

        $('a[data-toggle="tab"]').on('shown.bs.tab', function() {
            $('#datatable_serverside_detail').DataTable().columns.adjust();
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function() {
            $('#datatable_serverside_summary').DataTable().columns.adjust();
        });
    });

    function filter(param = '') {
        $('#download_excel').attr('onclick', 'downloadExcel("' + param + '")');

        var year_start = $('#year_start');
        var year_end = $('#year_end');
        var month_start = $('#month_start');
        var month_end = $('#month_end');
        var month_year_start = $('#month_year_start');
        var month_year_end = $('#month_year_end');
        var day_start = $('#day_start');
        var day_end = $('#day_end');

        if (param == 'annual') {
            month_start.val('');
            month_end.val('');
            month_year_start.val('');
            month_year_end.val('');
            day_start.val('');
            day_end.val('');

            if (year_start.val() && year_end.val()) {
                loadDataTableSummary(param);
                loadDataTableDetail(param);
                $('#modal_filter').modal('hide');
            } else {
                Swal.fire('Ooopss!!', 'Harap mengisi tahun awal dan tahun akhir.', 'warning');
            }
        } else if (param == 'monthly') {
            year_start.val('');
            year_end.val('');
            day_start.val('');
            day_end.val('');

            if (month_start.val() && month_year_start.val() && month_end.val() && month_year_end.val()) {
                loadDataTableSummary(param);
                loadDataTableDetail(param);
                $('#modal_filter').modal('hide');
            } else {
                Swal.fire('Ooopss!!', 'Harap mengisi bulan tahun awal dan bulan tahun akhir.', 'warning');
            }
        } else if (param == 'daily') {
            year_start.val('');
            year_end.val('');
            month_start.val('');
            month_end.val('');
            month_year_start.val('');
            month_year_end.val('');

            if (day_start.val() && day_end.val()) {
                $('#modal_filter').modal('hide');
                loadDataTableSummary(param);
                loadDataTableDetail(param);
            } else {
                Swal.fire('Ooopss!!', 'Harap mengisi harian awal dan harian akhir.', 'warning');
            }
        } else {
            loadDataTableSummary(param);
            loadDataTableDetail(param);
        }
    }

    function reset() {
        $('#publisher_id').val('').trigger('change');
        $('#type').val('');
        $('#status').val('');
        $('#method').val('');
        $('#province_id').val('').trigger('change');
        $('#year_start').val('');
        $('#year_end').val('');
        $('#month_start').val('');
        $('#month_end').val('');
        $('#month_year_start').val('');
        $('#month_year_end').val('');
        $('#day_start').val('');
        $('#day_end').val('');
        $('#extension').val('').trigger('change');
        $('#lib_loc_id').val('').trigger('change');
        $('#condition').val('').trigger('change');
        $('#availability').val('').trigger('change');
        filter();
    }

    function loadDataTableSummary(param = '') {
        $('#datatable_serverside_summary').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            scrollX: true,
            order: [
                [0, 'desc']
            ],
            iDisplayInLength: 10,
            ajax: {
                url: '{{ url('admin/report/collection/kckra/datatable_summary') }}',
                type: 'post',
                data: {
                    param: param,
                    publisher_id: $('#publisher_id').val(),
                    type: $('#type').val(),
                    province_id: $('#province_id').val(),
                    method: $('#method').val(),
                    year_start: $('#year_start').val(),
                    year_end: $('#year_end').val(),
                    month_start: $('#month_start').val(),
                    month_end: $('#month_end').val(),
                    month_year_start: $('#month_year_start').val(),
                    month_year_end: $('#month_year_end').val(),
                    day_start: $('#day_start').val(),
                    day_end: $('#day_end').val(),
                    status: $('#status').val(),
                    type_date: $('#type_date').val(),
                    extension: $('#extension').val()
                }
            },
            columns: [{
                    name: 'id',
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'type',
                    className: 'align-middle text-center'
                },
                {
                    name: 'total_submitted',
                    searchable: false,
                    orderable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'total_accept',
                    searchable: false,
                    orderable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'total_rejected',
                    searchable: false,
                    orderable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'total_data',
                    searchable: false,
                    orderable: false,
                    className: 'align-middle text-center'
                }
            ]
        });
    }

    function loadDataTableDetail(param = '') {
        $('#datatable_serverside_detail').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            scrollX: true,
            order: [
                [0, 'desc']
            ],
            iDisplayInLength: 10,
            pagingType: 'input',
            statesave: true,
            ajax: {
                url: '{{ url('admin/report/collection/kckra/datatable_detail') }}',
                type: 'post',
                data: {
                    param: param,
                    publisher_id: $('#publisher_id').val(),
                    type: $('#type').val(),
                    province_id: $('#province_id').val(),
                    method: $('#method').val(),
                    year_start: $('#year_start').val(),
                    year_end: $('#year_end').val(),
                    month_start: $('#month_start').val(),
                    month_end: $('#month_end').val(),
                    month_year_start: $('#month_year_start').val(),
                    month_year_end: $('#month_year_end').val(),
                    day_start: $('#day_start').val(),
                    day_end: $('#day_end').val(),
                    status: $('#status').val(),
                    type_date: $('#type_date').val(),
                    extension: $('#extension').val(),
                    lib_loc_id: $('#lib_loc_id').val(),
                    condition: $('#condition').val(),
                    availability: $('#availability').val()
                }
            },
            columns: [{
                    name: 'id',
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'action',
                    searchable: false,
                    orderable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'publisher_id',
                    className: 'align-middle text-center'
                },
                {
                    name: 'period',
                    searchable: false,
                    orderable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'province_id',
                    orderable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'city_id',
                    className: 'align-middle text-center'
                },
                {
                    name: 'title',
                    className: 'align-middle text-center'
                },
                {
                    name: 'type',
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'album',
                    className: 'align-middle text-center'
                },
                {
                    name: 'series',
                    className: 'align-middle text-center'
                },
                {
                    name: 'edition',
                    className: 'align-middle text-center'
                },
                {
                    name: 'serial',
                    className: 'align-middle text-center'
                },
                {
                    name: 'code',
                    className: 'align-middle text-center'
                },
                {
                    name: 'deposit',
                    className: 'align-middle text-center'
                },
                {
                    name: 'mark',
                    className: 'align-middle text-center'
                },
                {
                    name: 'publication_year',
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'lib_loc_id',
                    searchable: false,
                    orderable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'availability',
                    orderable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'condition',
                    orderable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'lock',
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'manual',
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'created_at',
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'received_at',
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'created_at',
                    searchable: false,
                    orderable: false,
                    className: 'align-middle text-center'
                }
            ]
        });
    }

    function downloadExcel(param = '') {
        $.ajax({
            url: '{{ url('admin/report/file_download/kckra/processing') }}',
            type: 'POST',
            dataType: 'JSON',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                loadingOpen('body');
            },
            data: {
                param: param,
                publisher_id: $('#publisher_id').val(),
                type: $('#type').val(),
                province_id: $('#province_id').val(),
                method: $('#method').val(),
                year_start: $('#year_start').val(),
                year_end: $('#year_end').val(),
                month_start: $('#month_start').val(),
                month_end: $('#month_end').val(),
                month_year_start: $('#month_year_start').val(),
                month_year_end: $('#month_year_end').val(),
                day_start: $('#day_start').val(),
                day_end: $('#day_end').val(),
                status: $('#status').val(),
                type_date: $('#type_date').val(),
                lib_loc_id: $('#lib_loc_id').val(),
                condition: $('#condition').val(),
                availability: $('#availability').val(),
                slug: 'collection'
            },
            success: function(response) {
                loadingClose('body');
                Swal.fire('Sukses!!', 'Sedang diproses.', 'success');
            }
        });
    }
</script>
