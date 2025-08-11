<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <h3 class="content-header-title mb-1 d-inline-block">ISRC</h3><br>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">ISRC</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-header-right col-md-6 col-12 mb-2 mt-1">
                <div class="float-md-right">
                    <button type="button" class="btn btn-secondary" onclick="loadDataTable()">Refresh</button>
                    <button type="button" class="btn btn-primary" data-toggle="modal"
                        data-target="#modal_filter">Filter</button>
                    <button type="button" class="btn btn-success" id='download_excel'
                        onclick="downloadExcel()">Download Excel</button>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="configuration">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Data ISRC</h4>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body card-dashboard">
                                    <table class="table table-striped table-bordered display nowrap"
                                        id="datatable_serverside">
                                        <thead class="text-center">
                                            <tr>
                                                <th>No</th>
                                                <th>Judul</th>
                                                <th>Pelaksana Serah</th>
                                                <th>Komposer</th>
                                                <th>ISRC</th>
                                                <th>Th Publikasi</th>
                                                <th>Tipe</th>
                                                <th>Tanggal Validasi</th>
                                            </tr>
                                        </thead>
                                    </table>
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
                            <label class="col-md-2">Judul</label>
                            <div class="col-md-10">
                                <textarea name="title" id="title" class="form-control" style="resize:none;"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-md-2">Pelaksana Serah</label>
                            <div class="col-md-10">
                                <select name="publisher_id" id="publisher_id" class="form-control" style="width:100%;">
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-md-2">Tahun Publikasi</label>
                            <div class="col-md-10">
                                <input type="number" name="publication_year" id="publication_year"
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-md-2">ISRC</label>
                            <div class="col-md-10">
                                <input type="text" name="code" id="code" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label class="col-md-2">Tipe Media</label>
                            <div class="col-md-10">
                                <select name="file_type" id="file_type" class="form-control">
                                    <option value="">-- Pilih --</option>
                                    <option value="VIDEO">Video</option>
                                    <option value="AUDIO">Audio</option>
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
                                                    {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
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
                                                    {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
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
                                                        {{ App\Helper\GeneralHelper::getMonth('01') }}
                                                    </option>
                                                    <option value="02">
                                                        {{ App\Helper\GeneralHelper::getMonth('02') }}
                                                    </option>
                                                    <option value="03">
                                                        {{ App\Helper\GeneralHelper::getMonth('03') }}
                                                    </option>
                                                    <option value="04">
                                                        {{ App\Helper\GeneralHelper::getMonth('04') }}
                                                    </option>
                                                    <option value="05">
                                                        {{ App\Helper\GeneralHelper::getMonth('05') }}
                                                    </option>
                                                    <option value="06">
                                                        {{ App\Helper\GeneralHelper::getMonth('06') }}
                                                    </option>
                                                    <option value="07">
                                                        {{ App\Helper\GeneralHelper::getMonth('07') }}
                                                    </option>
                                                    <option value="08">
                                                        {{ App\Helper\GeneralHelper::getMonth('08') }}
                                                    </option>
                                                    <option value="09">
                                                        {{ App\Helper\GeneralHelper::getMonth('09') }}
                                                    </option>
                                                    <option value="10">
                                                        {{ App\Helper\GeneralHelper::getMonth('10') }}
                                                    </option>
                                                    <option value="11">
                                                        {{ App\Helper\GeneralHelper::getMonth('11') }}
                                                    </option>
                                                    <option value="12">
                                                        {{ App\Helper\GeneralHelper::getMonth('12') }}
                                                    </option>
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
                                                        {{ App\Helper\GeneralHelper::getMonth('01') }}
                                                    </option>
                                                    <option value="02">
                                                        {{ App\Helper\GeneralHelper::getMonth('02') }}
                                                    </option>
                                                    <option value="03">
                                                        {{ App\Helper\GeneralHelper::getMonth('03') }}
                                                    </option>
                                                    <option value="04">
                                                        {{ App\Helper\GeneralHelper::getMonth('04') }}
                                                    </option>
                                                    <option value="05">
                                                        {{ App\Helper\GeneralHelper::getMonth('05') }}
                                                    </option>
                                                    <option value="06">
                                                        {{ App\Helper\GeneralHelper::getMonth('06') }}
                                                    </option>
                                                    <option value="07">
                                                        {{ App\Helper\GeneralHelper::getMonth('07') }}
                                                    </option>
                                                    <option value="08">
                                                        {{ App\Helper\GeneralHelper::getMonth('08') }}
                                                    </option>
                                                    <option value="09">
                                                        {{ App\Helper\GeneralHelper::getMonth('09') }}
                                                    </option>
                                                    <option value="10">
                                                        {{ App\Helper\GeneralHelper::getMonth('10') }}
                                                    </option>
                                                    <option value="11">
                                                        {{ App\Helper\GeneralHelper::getMonth('11') }}
                                                    </option>
                                                    <option value="12">
                                                        {{ App\Helper\GeneralHelper::getMonth('12') }}
                                                    </option>
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

<div class="modal fade" id="modal_detail" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Detail ISRC</h5>
                <button type="button" id="btnClose" class="close" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <img id="imgCover" class="ezoom" width="100%">
                    </div>
                    <div class="col-md-8">
                        <div id="center" style="height:200px">
                            <audio id="audioPlayer" src="" controls="controls" controlsList="nodownload"
                                autobuffer="autobuffer" oncontextmenu="return false;"></audio>
                            <video id="videoPlayer" src="" controls="controls" controlsList="nodownload"
                                autobuffer="autobuffer" oncontextmenu="return false;"></video>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        select2AutoSuggest('#publisher_id', 'load_publisher_isrc');
        filter();

        $('#download_excel').off('click').on('click', function() {
            let param = $(this).data('param') || ''; // Ambil nilai data-param
            downloadExcel(param);
        });

    });

    function loadDataTable(param = '') {
        $('#datatable_serverside').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            scrollX: true,
            order: [
                [0, 'desc']
            ],
            iDisplayInLength: 10,
            ajax: {
                url: '{{ url('admin/isrc/datatable') }}',
                data: {
                    param: param,
                    title: $('#title').val(),
                    publisher_id: $('#publisher_id').val(),
                    code: $('#code').val(),
                    publication_year: $('#publication_year').val(),
                    year_start: $('#year_start').val(),
                    year_end: $('#year_end').val(),
                    month_start: $('#month_start').val(),
                    month_end: $('#month_end').val(),
                    month_year_start: $('#month_year_start').val(),
                    month_year_end: $('#month_year_end').val(),
                    day_start: $('#day_start').val(),
                    day_end: $('#day_end').val(),
                    file_type: $('#file_type').val()
                },
            },
            columns: [{
                    name: 'id',
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'title',
                    className: 'align-middle text-center'
                },
                {
                    name: 'producer_name',
                    className: 'align-middle text-center'
                },
                {
                    name: 'composer_name',
                    className: 'align-middle text-center'
                },
                {
                    name: 'isrc_number',
                    className: 'align-middle text-center'
                },
                {
                    name: 'year',
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'asset_type',
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'publication_date',
                    searchable: false,
                    className: 'align-middle text-center'
                }
            ]
        });
    }

    function filter(param = '') {
        $('#download_excel').data('param', param);

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
                loadDataTable(param);
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
                loadDataTable(param);
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
                loadDataTable(param);
            } else {
                Swal.fire('Ooopss!!', 'Harap mengisi harian awal dan harian akhir.', 'warning');
            }
        } else {
            loadDataTable(param);
        }
    }

    function openDetail(id) {
        $.ajax({
            url: '{{ url('admin/isrc/show') }}',
            type: 'GET',
            dataType: 'JSON',
            data: {
                id: id
            },
            beforeSend: function() {
                $('#audioPlayer').attr('src', '');
                $('#videoPlayer').attr('src', '');
            },
            success: function(response) {
                if (response.type) {
                    $('#imgCover').attr('src', response.cover);
                    if (response.file) {
                        if (response.type == 'AUDIO') {
                            $('#audioPlayer').show();
                            $('#videoPlayer').hide();
                            $('#audioPlayer').attr('src', response.file);
                        } else {
                            $('#audioPlayer').hide();
                            $('#videoPlayer').show();
                            $('#videoPlayer').attr('src', response.file);
                        }
                    } else {
                        $('#audioPlayer').hide();
                        $('#videoPlayer').hide();
                    }

                    $('#modal_detail').modal('show');
                } else {
                    alert('File tidak ditemukan');
                }
            },
            error: function() {
                alert('Server error');
            }
        });
    }

    function downloadExcel(param = '') {
        $.ajax({
            url: '{{ url('admin/report/file_download/processing') }}',
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
                title: $('#title').val(),
                publisher_id: $('#publisher_id').val(),
                code: $('#code').val(),
                publication_year: $('#publication_year').val(),
                year_start: $('#year_start').val(),
                year_end: $('#year_end').val(),
                month_start: $('#month_start').val(),
                month_end: $('#month_end').val(),
                month_year_start: $('#month_year_start').val(),
                month_year_end: $('#month_year_end').val(),
                day_start: $('#day_start').val(),
                day_end: $('#day_end').val(),
                file_type: $('#file_type').val(),
                slug: 'data_isrc'
            },
            success: function(response) {
                loadingClose('body');
                Swal.fire('Sukses!!', 'Sedang diproses.', 'success');
            }
        });
    }
</script>
