<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <h3 class="content-header-title mb-1 d-inline-block">Guest</h3><br>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Guest</li>
                        </ol>
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
                                <h4 class="card-title">Filter</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <label class="col-md-2">Penerbit</label>
                                            <div class="col-md-10">
                                                <select name="publisher_id" id="publisher_id" class="form-control" style="width:100%;"></select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <label class="col-md-2">Jenis</label>
                                            <div class="col-md-10">
                                                <select name="type" id="type" class="form-control">
                                                    <option value="">Semua</option>
                                                    <option value="1">Buku</option>
                                                    <option value="2">Partitur</option>
                                                    <option value="3">Peta</option>
                                                    <option value="4">Serial</option>
                                                    <option value="5">Audio</option>
                                                    <option value="6">Film</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <label class="col-md-2">Kategori</label>
                                            <div class="col-md-10">
                                                <select name="category_id" id="category_id" class="form-control" style="width:100%;">
                                                <option value="">Semua</option>
                                                    @foreach($category as $c)
                                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <label class="col-md-2">Subjek</label>
                                            <div class="col-md-10">
                                                <select name="subject_id[]" id="subject_id" class="form-control" style="width:100%;" multiple="true"></select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <label class="col-md-2">Provinsi</label>
                                            <div class="col-md-10">
                                                <select name="province_id" id="province_id" class="form-control" style="width:100%;"></select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group row">
                                            <label class="col-md-2">Tahunan</label>
                                            <div class="col-md-10">
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <select name="year_start" id="year_start" class="form-control">
                                                            <option value="">-- Pilih --</option>
                                                            @for($i = 2018; $i <= date('Y'); $i++)
                                                                <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <select name="year_end" id="year_end" class="form-control">
                                                            <option value="">-- Pilih --</option>
                                                            @for($i = 2018; $i <= date('Y'); $i++)
                                                                <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <button type="button" class="btn btn-primary col-12" onclick="filter('annual')"><i class="la la-search"></i> Cari</button>
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
                                                    <div class="col-md-5">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <select name="month_start" id="month_start" class="form-control">
                                                                    <option value="">-- Pilih --</option>
                                                                    <option value="01">{{ App\Helper\GeneralHelper::getMonth('01') }}</option>
                                                                    <option value="02">{{ App\Helper\GeneralHelper::getMonth('02') }}</option>
                                                                    <option value="03">{{ App\Helper\GeneralHelper::getMonth('03') }}</option>
                                                                    <option value="04">{{ App\Helper\GeneralHelper::getMonth('04') }}</option>
                                                                    <option value="05">{{ App\Helper\GeneralHelper::getMonth('05') }}</option>
                                                                    <option value="06">{{ App\Helper\GeneralHelper::getMonth('06') }}</option>
                                                                    <option value="07">{{ App\Helper\GeneralHelper::getMonth('07') }}</option>
                                                                    <option value="08">{{ App\Helper\GeneralHelper::getMonth('08') }}</option>
                                                                    <option value="09">{{ App\Helper\GeneralHelper::getMonth('09') }}</option>
                                                                    <option value="10">{{ App\Helper\GeneralHelper::getMonth('10') }}</option>
                                                                    <option value="11">{{ App\Helper\GeneralHelper::getMonth('11') }}</option>
                                                                    <option value="12">{{ App\Helper\GeneralHelper::getMonth('12') }}</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <select name="month_year_start" id="month_year_start" class="form-control">
                                                                    <option value="">-- Pilih --</option>
                                                                    @for($i = 2018; $i <= date('Y'); $i++)
                                                                        <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                                                                    @endfor
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <select name="month_end" id="month_end" class="form-control">
                                                                    <option value="">-- Pilih --</option>
                                                                    <option value="01">{{ App\Helper\GeneralHelper::getMonth('01') }}</option>
                                                                    <option value="02">{{ App\Helper\GeneralHelper::getMonth('02') }}</option>
                                                                    <option value="03">{{ App\Helper\GeneralHelper::getMonth('03') }}</option>
                                                                    <option value="04">{{ App\Helper\GeneralHelper::getMonth('04') }}</option>
                                                                    <option value="05">{{ App\Helper\GeneralHelper::getMonth('05') }}</option>
                                                                    <option value="06">{{ App\Helper\GeneralHelper::getMonth('06') }}</option>
                                                                    <option value="07">{{ App\Helper\GeneralHelper::getMonth('07') }}</option>
                                                                    <option value="08">{{ App\Helper\GeneralHelper::getMonth('08') }}</option>
                                                                    <option value="09">{{ App\Helper\GeneralHelper::getMonth('09') }}</option>
                                                                    <option value="10">{{ App\Helper\GeneralHelper::getMonth('10') }}</option>
                                                                    <option value="11">{{ App\Helper\GeneralHelper::getMonth('11') }}</option>
                                                                    <option value="12">{{ App\Helper\GeneralHelper::getMonth('12') }}</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <select name="month_year_end" id="month_year_end" class="form-control">
                                                                    <option value="">-- Pilih --</option>
                                                                    @for($i = 2018; $i <= date('Y'); $i++)
                                                                        <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                                                                    @endfor
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <button type="button" class="btn btn-primary col-12" onclick="filter('monthly')"><i class="la la-search"></i> Cari</button>
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
                                                    <div class="col-md-5">
                                                        <input type="date" name="day_start" id="day_start" class="form-control" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
                                                    </div>
                                                    <div class="col-md-5">
                                                        <input type="date" name="day_end" id="day_end" class="form-control" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <button type="button" class="btn btn-primary col-12" onclick="filter('daily')"><i class="la la-search"></i> Cari</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group text-center">
                                            <hr>
                                            <button class="btn btn-danger" onclick="reset()"><i class="la la-refresh"></i> Reset Semua Filter</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="card-title">
                                            Daftar Koleksi
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped table-bordered display"
                                    id="datatable_serverside">
                                    <thead class="text-center">
                                        <tr>
                                            <th>No</th>
                                            <th>Judul</th>
                                            <th>Jenis</th>
                                            <th>Pelaksana Serah</th>
                                            <th>Kontributor</th>
                                            <th>Subyek</th>
                                            <th>Periode Unggah</th>
                                            <th>Tgl Terima</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
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
        select2AutoSuggest('#publisher_id', 'load_publisher');
        select2AutoSuggest('#province_id', 'load_province');
        select2AutoSuggestTags('#subject_id', 'load_subject');
        filter();
    });

    function filter(param = '') {
        var year_start       = $('#year_start');
        var year_end         = $('#year_end');
        var month_start      = $('#month_start');
        var month_end        = $('#month_end');
        var month_year_start = $('#month_year_start');
        var month_year_end   = $('#month_year_end');
        var day_start        = $('#day_start');
        var day_end          = $('#day_end');

        if(param == 'annual') {
            month_start.val('');
            month_end.val('');
            month_year_start.val('');
            month_year_end.val('');
            day_start.val('');
            day_end.val('');

            if(year_start.val() && year_end.val()) {
                loadDataTable(param);
            } else {
                Swal.fire('Ooopss!!', 'Harap mengisi tahun awal dan tahun akhir.', 'warning');
            }
        } else if(param == 'monthly') {
            year_start.val('');
            year_end.val('');
            day_start.val('');
            day_end.val('');

            if(month_start.val() && month_year_start.val() && month_end.val() && month_year_end.val()) {
                loadDataTable(param);
            } else {
                Swal.fire('Ooopss!!', 'Harap mengisi bulan tahun awal dan bulan tahun akhir.', 'warning');
            }
        } else if(param == 'daily') {
            year_start.val('');
            year_end.val('');
            month_start.val('');
            month_end.val('');
            month_year_start.val('');
            month_year_end.val('');

            if(day_start.val() && day_end.val()) {
                loadDataTable(param);
            } else {
                Swal.fire('Ooopss!!', 'Harap mengisi harian awal dan harian akhir.', 'warning');
            }
        } else {
            loadDataTable(param);
        }
    }

    function reset() {
        $('#publisher_id').val('').trigger('change');
        $('#type').val('');
        $('#province_id').val('').trigger('change');
        $('#category_id').val('').trigger('change');
        $('#subject_id').val('').trigger('change');
        $('#year_start').val('');
        $('#year_end').val('');
        $('#month_start').val('');
        $('#month_end').val('');
        $('#month_year_start').val('');
        $('#month_year_end').val('');
        $('#day_start').val('');
        $('#day_end').val('');
        filter();
    }

    function loadDataTable(param = '') {
        $('#datatable_serverside').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            scrollX: true,
            order: [[0, 'desc']],
            iDisplayInLength: 10,
            ajax: {
                url: '{{ url("admin/guest/datatable") }}',
                data: {
                    param: param,
                    publisher_id: $('#publisher_id').val(),
                    type: $('#type').val(),
                    category_id: $('#category_id').val(),
                    subject_id: $('#subject_id').val(),
                    province_id: $('#province_id').val(),
                    year_start: $('#year_start').val(),
                    year_end: $('#year_end').val(),
                    month_start: $('#month_start').val(),
                    month_end: $('#month_end').val(),
                    month_year_start: $('#month_year_start').val(),
                    month_year_end: $('#month_year_end').val(),
                    day_start: $('#day_start').val(),
                    day_end: $('#day_end').val()
                }
            },
            columns: [
                {
                    name: 'id',
                    searchable: false,
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
                    name: 'publisher',
                    searchable: false,
                    orderable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'kontributor',
                    searchable: false,
                    orderable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'subyek',
                    searchable: false,
                    orderable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'periode',
                    orderable: false,
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'received_at',
                    orderable: false,
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'action',
                    searchable: false,
                    orderable: false,
                    className: 'align-middle text-center'
                }
            ]
        });
    }
</script>
