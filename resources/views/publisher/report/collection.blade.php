<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <h3 class="content-header-title mb-1 d-inline-block">Laporan Koleksi</h3><br>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('publisher/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Laporan</a></li>
                            <li class="breadcrumb-item active">Koleksi</li>
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
                                            <label class="col-md-2">Tahunan</label>
                                            <div class="col-md-10">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <select name="year_start" id="year_start" class="form-control">
                                                            <option value="">-- Pilih --</option>
                                                            @for($i = 2018; $i <= date('Y'); $i++)
                                                                <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <p style="line-height:40px;" class="text-center">s/d</p>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select name="year_end" id="year_end" class="form-control">
                                                            <option value="">-- Pilih --</option>
                                                            @for($i = 2018; $i <= date('Y'); $i++)
                                                                <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
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
                                                    <div class="col-md-4">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <select name="month_start" id="month_start" class="form-control">
                                                                    <option value="">-- Pilih --</option>
                                                                    <option value="01" {{ 1 == date('m') ? 'selected' : '' }}>Januari</option>
                                                                    <option value="02" {{ 2 == date('m') ? 'selected' : '' }}>Februari</option>
                                                                    <option value="03" {{ 3 == date('m') ? 'selected' : '' }}>Maret</option>
                                                                    <option value="04" {{ 4 == date('m') ? 'selected' : '' }}>April</option>
                                                                    <option value="05" {{ 5 == date('m') ? 'selected' : '' }}>Mei</option>
                                                                    <option value="06" {{ 6 == date('m') ? 'selected' : '' }}>Juni</option>
                                                                    <option value="07" {{ 7 == date('m') ? 'selected' : '' }}>Juli</option>
                                                                    <option value="08" {{ 8 == date('m') ? 'selected' : '' }}>Agustus</option>
                                                                    <option value="09" {{ 9 == date('m') ? 'selected' : '' }}>September</option>
                                                                    <option value="10" {{ 10 == date('m') ? 'selected' : '' }}>Oktober</option>
                                                                    <option value="11" {{ 11 == date('m') ? 'selected' : '' }}>November</option>
                                                                    <option value="12" {{ 12 == date('m') ? 'selected' : '' }}>Desember</option>
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
                                                    <div class="col-md-1">
                                                        <p style="line-height:40px;" class="text-center">s/d</p>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <select name="month_end" id="month_end" class="form-control">
                                                                    <option value="">-- Pilih --</option>
                                                                    <option value="01" {{ 1 == date('m') ? 'selected' : '' }}>Januari</option>
                                                                    <option value="02" {{ 2 == date('m') ? 'selected' : '' }}>Februari</option>
                                                                    <option value="03" {{ 3 == date('m') ? 'selected' : '' }}>Maret</option>
                                                                    <option value="04" {{ 4 == date('m') ? 'selected' : '' }}>April</option>
                                                                    <option value="05" {{ 5 == date('m') ? 'selected' : '' }}>Mei</option>
                                                                    <option value="06" {{ 6 == date('m') ? 'selected' : '' }}>Juni</option>
                                                                    <option value="07" {{ 7 == date('m') ? 'selected' : '' }}>Juli</option>
                                                                    <option value="08" {{ 8 == date('m') ? 'selected' : '' }}>Agustus</option>
                                                                    <option value="09" {{ 9 == date('m') ? 'selected' : '' }}>September</option>
                                                                    <option value="10" {{ 10 == date('m') ? 'selected' : '' }}>Oktober</option>
                                                                    <option value="11" {{ 11 == date('m') ? 'selected' : '' }}>November</option>
                                                                    <option value="12" {{ 12 == date('m') ? 'selected' : '' }}>Desember</option>
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
                                                    <div class="col-md-3">
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
                                                    <div class="col-md-4">
                                                        <input type="date" name="day_start" id="day_start" class="form-control" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
                                                    </div>
                                                    <div class="col-md-1">
                                                        <p style="line-height:40px;" class="text-center">s/d</p>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="date" name="day_end" id="day_end" class="form-control" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
                                                    </div>
                                                    <div class="col-md-3">
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
                                    <div class="col-md-6 text-right">
                                        <h4 class="card-title">
                                            <a href="#" class="btn btn-success btn-sm" id="download_excel" onclick="downloadExcel()"><i class="la la-folder"></i> Download Excel</a>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <ul class="nav nav-tabs nav-justified">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" aria-controls="tab_detail" href="#tab_detail" aria-expanded="true">Detail</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" aria-controls="tab_summary" href="#tab_summary" aria-expanded="false">Summary</a>
                                    </li>
                                </ul>
                                <div class="tab-content px-1 pt-1">
                                    <div role="tabpanel" class="tab-pane active" id="tab_detail" aria-expanded="true">
                                        <p>
                                            <table class="table table-striped table-bordered display nowrap"
                                                id="datatable_serverside_detail">
                                                <thead class="text-center">
                                                    <tr>
                                                        <th>Pelaksana</th>
                                                        <th>Periode</th>
                                                        <th>Deposit</th>
                                                        <th>Judul</th>
                                                        <th>Edisi</th>
                                                        <th>Seri</th>
                                                        <th>Tempat Terbit</th>
                                                        <th>Penyerahan</th>
                                                        <th>Diterima</th>
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

<script>
    $(function() {
        filter();

        $('a[data-toggle="tab"]').on('shown.bs.tab', function() {
            $('#datatable_serverside_detail').DataTable().columns.adjust();
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function() {
            $('#datatable_serverside_summary').DataTable().columns.adjust();
        });
    });

    function filter(param = '') {
        $('#download_excel').attr('onclick', "downloadExcel('" + param + "')");

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
                loadDataTableSummary(param);
                loadDataTableDetail(param);
            } else {
                Swal.fire('Ooopss!!', 'Harap mengisi tahun awal dan tahun akhir.', 'warning');
            }
        } else if(param == 'monthly') {
            year_start.val('');
            year_end.val('');
            day_start.val('');
            day_end.val('');

            if(month_start.val() && month_year_start.val() && month_end.val() && month_year_end.val()) {
                loadDataTableSummary(param);
                loadDataTableDetail(param);
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
        $('#type').val('');
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

    function loadDataTableSummary(param = '') {
        $('#datatable_serverside_summary').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            scrollX: true,
            lengthMenu: [10, 25, 50, 75, 100],
            ajax: {
                url: '{{ url("publisher/report/collection/datatable_summary") }}',
                data: {
                    param: param,
                    type: $('#type').val(),
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
                    name: 'type',
                    className: 'align-middle text-center'
                },
                {
                    name: 'total_submitted',
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'total_accept',
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'total_rejected',
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'total_data',
                    searchable: false,
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
            lengthMenu: [10, 25, 50, 75, 100],
            ajax: {
                url: '{{ url("publisher/report/collection/datatable_detail") }}',
                data: {
                    param: param,
                    publisher_id: $('#publisher_id').val(),
                    type: $('#type').val(),
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
                    name: 'publisher_id',
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'periode',
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'deposit',
                    className: 'align-middle text-center'
                },
                {
                    name: 'title',
                    className: 'align-middle text-center'
                },
                {
                    name: 'edition',
                    className: 'align-middle text-center'
                },
                {
                    name: 'series',
                    className: 'align-middle text-center'
                },
                {
                    name: 'city_id',
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
                    name: 'receipt',
                    searchable: false,
                    className: 'align-middle text-center'
                }
            ]
        });
    }

    function downloadExcel(param = '') {
        $.ajax({
            url: '{{ url("publisher/report/file_download/processing") }}',
            type: 'POST',
            dataType: 'JSON',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                param: param,
                publisher_id: $('#publisher_id').val(),
                type: $('#type').val(),
                province_id: $('#province_id').val(),
                year_start: $('#year_start').val(),
                year_end: $('#year_end').val(),
                month_start: $('#month_start').val(),
                month_end: $('#month_end').val(),
                month_year_start: $('#month_year_start').val(),
                month_year_end: $('#month_year_end').val(),
                day_start: $('#day_start').val(),
                day_end: $('#day_end').val(),
                slug: 'collection'
            },
            success: function(response) {
                Swal.fire('Sukses!!', 'Sedang diproses.', 'success');
            }
        });
    }
</script>
