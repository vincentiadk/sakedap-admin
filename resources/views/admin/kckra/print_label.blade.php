<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <h3 class="content-header-title mb-1 d-inline-block">{{ $title }}</h3><br>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">KCKRA</a></li>
                            <li class="breadcrumb-item active">Print Label</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-header-right col-md-6 col-12 mb-2 mt-1">
                <div class="float-md-right">
                    <button type="button" class="btn btn-danger btn-sm" onclick="reset()"><i class="la la-refresh"></i>
                        Reset Filter</button>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="configuration">
                @if (session('success'))
                    <div class="alert bg-success alert-icon-left alert-dismissible mb-2" role="alert">
                        <span class="alert-icon"><i class="la la-check"></i></span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>Success!</strong> {{ session('success') }}
                    </div>
                @endif
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Filter</h4>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li><a data-action="collapse"><i class="ft-plus"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="col-md-2">Type Collection</label>
                                                <div class="col-md-10">
                                                    <select name="type" id="type" class="form-control"
                                                        style="width:100%;">
                                                        <option value="">-- Pilih Tipe Koleksi KCKRA --</option>
                                                        @foreach ($types as $id_type => $type)
                                                            <option
                                                                @if (!empty(session('filter.collection.kckra.print.type')) && $id_type == session('filter.collection.kckra.print.type')) selected="selected" @endif
                                                                value="{{ $id_type }}">
                                                                {{ $type }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="col-md-2">Judul</label>
                                                <div class="col-md-10">
                                                    <textarea name="title" id="title" class="form-control" style="resize:none;">{{ session('filter.collection.kckra.print.title') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="col-md-2">Penerbit</label>
                                                <div class="col-md-10">
                                                    <select name="publisher_id" id="publisher_id" class="form-control"
                                                        style="width:100%;">
                                                        @if (!empty(session('filter.collection.kckra.print.publisher_id')))
                                                            <option
                                                                value="{{ session('filter.collection.kckra.print.publisher_id') }}"
                                                                selected="selected">
                                                                {{ App\Models\Publisher::select('name')->where('id', session('filter.collection.kckra.print.publisher_id'))->first()->name }}
                                                            </option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="col-md-2">Provinsi</label>
                                                <div class="col-md-10">
                                                    <select name="province_id" id="province_id" class="form-control"
                                                        style="width:100%;">
                                                        @if (!empty(session('filter.collection.kckra.print.province_id')))
                                                            <option
                                                                value="{{ session('filter.collection.kckra.print.province_id') }}"
                                                                selected="selected">
                                                                {{ App\Models\Province::where('id', session('filter.collection.kckra.print.province_id'))->select('name')->first()->name }}
                                                            </option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="col-md-2">Tempat Terbit</label>
                                                <div class="col-md-10">
                                                    <select name="city" id="city" class="form-control"
                                                        style="width:100%;">
                                                        @if (!empty(session('filter.collection.kckra.print.city')))
                                                            <option
                                                                value="{{ session('filter.collection.kckra.print.city') }}"
                                                                selected="selected">
                                                                {{ App\Models\City::select('name')->where('id', session('filter.collection.kckra.print.city'))->first()->name }}
                                                            </option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="col-md-2">Dikelola</label>
                                                <div class="col-md-10">
                                                    @php $manages = array(''=>'Semua','1'=>'Sudah','2'=>'Belum')  @endphp
                                                    <select name="manage" id="manage" class="form-control">
                                                        @foreach ($manages as $key => $value)
                                                            <option value="{{ $key }}"
                                                                {{ $key == session('filter.collection.kckra.print.manage') ? 'selected' : '' }}>
                                                                {{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="col-md-2">Validasi</label>
                                                <div class="col-md-10">
                                                    @php $validated = array(''=>'Semua','1'=>'Sudah','2'=>'Belum')  @endphp
                                                    <select name="validated" id="validated" class="form-control">
                                                        @foreach ($validated as $key => $value)
                                                            <option value="{{ $key }}"
                                                                {{ $key == session('filter.collection.kckra.print.validated') ? 'selected' : '' }}>
                                                                {{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="col-md-2">Edited</label>
                                                <div class="col-md-10">
                                                    @php $edited = array(''=>'Semua','1'=>'Sudah','2'=>'Belum')  @endphp
                                                    <select name="edited" id="edited" class="form-control">
                                                        @foreach ($validated as $key => $value)
                                                            <option value="{{ $key }}"
                                                                {{ $key == session('filter.collection.kckra.print.edited') ? 'selected' : '' }}>
                                                                {{ $value }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="col-md-2">Tahun Terbit</label>
                                                <div class="col-md-10">
                                                    <input type="number" name="publication_year"
                                                        id="publication_year" class="form-control"
                                                        value="{{ session('filter.collection.kckra.print.publication_year') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="col-md-2">ISBN</label>
                                                <div class="col-md-10">
                                                    <input type="text" name="code" id="code"
                                                        class="form-control"
                                                        value="{{ session('filter.collection.kckra.print.code') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group row">
                                                <label class="col-md-2">Tahunan</label>
                                                <div class="col-md-10">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <select name="year_start" id="year_start"
                                                                class="form-control">
                                                                <option value="">-- Pilih --</option>
                                                                @for ($i = 2018; $i <= date('Y'); $i++)
                                                                    <option value="{{ $i }}"
                                                                        {{ $i == session('filter.collection.kckra.print.year_start') ? 'selected' : '' }}>
                                                                        {{ $i }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <p style="line-height:40px;" class="text-center">s/d</p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <select name="year_end" id="year_end"
                                                                class="form-control">
                                                                <option value="">-- Pilih --</option>
                                                                @for ($i = 2018; $i <= date('Y'); $i++)
                                                                    <option value="{{ $i }}"
                                                                        {{ $i == session('filter.collection.kckra.print.year_end') ? 'selected' : '' }}>
                                                                        {{ $i }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <button type="button" class="btn btn-primary col-12"
                                                                    onclick="filter('annual')"><i
                                                                        class="la la-search"></i> Cari</button>
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
                                                                    @php $month = array('01','02','03','04','05','06','07','08','09','10','11','12') @endphp
                                                                    <select name="month_start" id="month_start"
                                                                        class="form-control">
                                                                        <option value="">-- Pilih --</option>
                                                                        @foreach ($month as $key => $value)
                                                                            <option value="{{ $value }}"
                                                                                {{ $value == session('filter.collection.kckra.print.month_start') ? 'selected' : '' }}>
                                                                                {{ App\Helper\GeneralHelper::getMonth($value) }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <select name="month_year_start"
                                                                        id="month_year_start" class="form-control">
                                                                        <option value="">-- Pilih --</option>
                                                                        @for ($i = 2018; $i <= date('Y'); $i++)
                                                                            <option value="{{ $i }}"
                                                                                {{ $i == session('filter.collection.kckra.print.month_year_start') ? 'selected' : '' }}>
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
                                                                    <select name="month_end" id="month_end"
                                                                        class="form-control">
                                                                        <option value="">-- Pilih --</option>
                                                                        @foreach ($month as $key => $value)
                                                                            <option value="{{ $value }}"
                                                                                {{ $value == session('filter.collection.kckra.print.month_end') ? 'selected' : '' }}>
                                                                                {{ App\Helper\GeneralHelper::getMonth($value) }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <select name="month_year_end" id="month_year_end"
                                                                        class="form-control">
                                                                        <option value="">-- Pilih --</option>
                                                                        @for ($i = 2018; $i <= date('Y'); $i++)
                                                                            <option value="{{ $i }}"
                                                                                {{ $i == session('filter.collection.kckra.print.month_year_end') ? 'selected' : '' }}>
                                                                                {{ $i }}</option>
                                                                        @endfor
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <button type="button" class="btn btn-primary col-12"
                                                                    onclick="filter('monthly')"><i
                                                                        class="la la-search"></i> Cari</button>
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
                                                            <input type="date" name="day_start" id="day_start"
                                                                class="form-control" max="{{ date('Y-m-d') }}"
                                                                value="{{ empty(session('filter.collection.kckra.print.day_start')) ? date('Y-m-d') : session('filter.collection.kckra.print.day_start') }}">
                                                        </div>
                                                        <div class="col-md-1">
                                                            <p style="line-height:40px;" class="text-center">s/d</p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <input type="date" name="day_end" id="day_end"
                                                                class="form-control" max="{{ date('Y-m-d') }}"
                                                                value="{{ empty(session('filter.collection.kckra.print.day_end')) ? date('Y-m-d') : session('filter.collection.kckra.print.day_end') }}">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <button type="button" class="btn btn-primary col-12"
                                                                    onclick="filter('daily')"><i
                                                                        class="la la-search"></i> Cari</button>
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

                        <div class="card" id="cart_prints">
                            <div class="card-header mb-2">
                                <h4 class="card-title" id="heading-icon">Daftar Keranjang</h4>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-success" data-toggle="dropdown"
                                                    aria-haspopup="true" aria-expanded="false"><i
                                                        class="la la-print"></i> Print </button>
                                                <div class="dropdown-menu arrow">
                                                    <a href="javascript:void(0);" onclick="printCart('barcode')"
                                                        class="dropdown-item">Barcode</a>
                                                    <a href="javascript:void(0);" onclick="printCart('qrcode')"
                                                        class="dropdown-item">QR Code</a>
                                                </div>
                                            </div>
                                        </li>
                                        <li><a data-action="collapse"><i class="ft-plus"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse">
                                <div class="card-body card-dashboard">
                                    <table class="table table-striped table-bordered display nowrap"
                                        id="datatable_cart">
                                        <thead class="text-center">
                                            <tr>
                                                <th>Judul</th>
                                                <th>Barcode</th>
                                                <th>Mark Province</th>
                                                <th>Mark National</th>
                                                <th>Hapus</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title" id="heading-icon">Daftar Koleksi KCKRA</h4>
                                <div class="heading-elements">
                                    <ul class="list-inline mb-0">
                                        <li>
                                            <button type="button" class="btn btn-md btn-success"
                                                onclick="saveCart()"><i class="la la-cart-plus"></i></button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body card-dashboard">
                                    <table class="table table-striped table-bordered display nowrap"
                                        id="datatable_serverside">
                                        <thead class="text-center">
                                            <tr>
                                                <th></th>
                                                <th>No</th>
                                                <th>Type Collection</th>
                                                <th>Kode Barcode</th>
                                                <th>Mark National</th>
                                                <th>Mark Province</th>
                                                <th>Penerbit</th>
                                                <th>Judul</th>
                                                <th>ISBN</th>
                                                <th>Kondisi</th>
                                                <th>Ketersediaan</th>
                                                <th>Perpustakaan</th>
                                                <th>Lokasi</th>
                                                <th>Update</th>
                                                <th>Penerima</th>
                                                <th>Tanggal Terima</th>
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

<script>
    $(function() {
        let param = "{{ session('filter.collection.kckra.print.param') }}"
        select2AutoSuggest('#publisher_id', 'load_publisher');
        select2AutoSuggest('#province_id', 'load_province');
        filter(param);
        $("#datatable_cart").DataTable({
            "columns": [{
                    "data": "title",
                    "title": "Judul"
                },
                {
                    "data": "code",
                    "title": "Barcode"
                },
                {
                    "data": "mark_province",
                    "title": "Mark Province"
                },
                {
                    "data": "mark_national",
                    "title": "Mark National"
                },
                {
                    "data": "remove",
                    "title": "Hapus"
                },
            ],
            data: []
        });
        updateTableCart();
        $('#province_id').change(function() {
            if ($('#province_id').val() == '') {
                $('#city').html('');
                $('#city').val('');
            } else {
                select2AutoSuggest('#city', 'load_city/' + $('#province_id').val());
            }
        });
    });

    function filter(param = '') {
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
            // reset();
            loadDataTable();
        }
    }

    function reset() {
        $.ajax({
            url: '{{ url('admin/collection/reset_filed/manage/1') }}',
            type: 'GET',
            contentType: false,
            processData: false,
            success: function(response) {
                $('#title').val('');
                $('#publisher_id').val('').trigger('change');
                $('#province_id').val('').trigger('change');
                $('#city').val('').trigger('change');
                $('#publication_year').val('');
                $('#code').val('');
                $('#manage').val('');
                $('#validated').val('');
                $('#edited').val('');
                $('#year_start').val('');
                $('#year_end').val('');
                $('#month_start').val('');
                $('#month_end').val('');
                $('#month_year_start').val('');
                $('#month_year_end').val('');
                $('#day_start').val('');
                $('#day_end').val('');
                loadDataTable();
            },
            error: function() {
                Toast.fire({
                    icon: 'error',
                    title: 'Server Error!'
                });
            }
        });
    }

    function loadDataTable(param = '') {
        $('#datatable_serverside').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            order: [
                [1, 'desc']
            ],
            scrollX: true,
            iDisplayInLength: 10,
            ajax: {
                url: '{{ url('admin/collection/kckra/print/datatable') }}',
                data: {
                    param: param,
                    title: $('#title').val(),
                    type: $('#type').val(),
                    publisher_id: $('#publisher_id').val(),
                    province_id: $('#province_id').val(),
                    city: $('#city').val(),
                    publication_year: $('#publication_year').val(),
                    code: $('#code').val(),
                    manage: $('#manage').val(),
                    validated: $('#validated').val(),
                    edited: $('#edited').val(),
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
            columnDefs: [{
                targets: 0,
                checkboxes: {
                    selectRow: true
                }
            }],
            select: {
                style: 'multi'
            },
            rowId: '0',
            columns: [{
                    name: 'id',
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'number',
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'type',
                    searchable: false,
                    orderable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'barcode',
                    className: 'align-middle text-center'
                },
                {
                    name: 'mark_national',
                    className: 'align-middle text-center'
                },
                {
                    name: 'mark_province',
                    className: 'align-middle text-center'
                },
                {
                    name: 'publisher_id',
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'title',
                    className: 'align-middle text-center'
                },
                {
                    name: 'code',
                    className: 'align-middle text-center'
                },
                {
                    name: 'condition',
                    className: 'align-middle text-center'
                },
                {
                    name: 'availability',
                    className: 'align-middle text-center'
                },
                {
                    name: 'perpustakaan',
                    className: 'align-middle text-center'
                },
                {
                    name: 'lokasi',
                    className: 'align-middle text-center'
                },
                {
                    name: 'updated_by',
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'validated_by',
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'received_at',
                    searchable: false,
                    className: 'align-middle text-center'
                }
            ],
            pagingType: 'input',
        });
    }

    function saveCart() {
        // console.log($('#datatable_serverside').DataTable().column(0).checkboxes.selected());
        var rows_selected = $('#datatable_serverside').DataTable().column(0).checkboxes.selected();
        // Iterate over all selected checkboxes
        $.each(rows_selected, function(index, rowId) {
            var row = $('#datatable_serverside').DataTable().row('#' + rowId);
            if (row && row.length > 0) {
                var rowData = row.data();
                var data = {
                    'title': rowData[7],
                    'code': rowData[3],
                    'mark_province': rowData[4],
                    'mark_national': rowData[5],
                    'id': rowData[0],
                }
                console.log(data);
                updateStoredData('cart_prints', data, rowId, null, 'replace', true, 'local');
            } else {
                console.log("Row with ID " + rowId + " not found.");
            }
        });
        updateTableCart();
        Swal.fire('Success', 'Koleksi berhasil ditambahkan dalam keranjang!', 'success');
    }

    function printCart(param) {
        var data = JSON.parse(localStorage.getItem('cart_prints'));
        var idsArray = Object.keys(data);
        if (Object.keys(data).length === 0) {
            Swal.fire('Ooopss!!', 'Keranjang Kosong, Mohon untuk memilih koleksi terlebih dahulu', 'warning');
        } else {
            window.open('{{ url('admin/collection/kckra/print/') }}' + '/' + param + '?ids=' + idsArray.join(','),
                '_blank');
        }
    }

    function updateTableCart() {
        $('#datatable_cart').DataTable().clear().draw();
        var carts = JSON.parse(localStorage.getItem('cart_prints'));
        $.each(carts, function(index, data) {
            console.log(data, 'data');
            data.remove =
                `<button type="button" class="btn btn-danger btn-sm col-12" onclick="removeCart(` + data.id +
                `)"><i class="la la-trash"></i></button>`;
            $('#datatable_cart').DataTable().row.add(data).draw().node();
        });
    }

    function removeCart(id) {
        updateStoredData('cart_prints', null, id, null, 'delete', true, 'local');
        updateTableCart();
    }

    function updateStoredData(key, data, parent_id = null, child_id = null, type = 'add', unique = false, storage =
        'session') {

        // console.log(parent_id);
        //get initial data from sessionStorage / localStorage
        if (storage == 'session') {
            var storedData = JSON.parse(sessionStorage.getItem(key));
        } else {
            var storedData = JSON.parse(localStorage.getItem(key));
        }

        if (storedData == null || storedData.length == 0) {
            storedData = {};
        }

        if (type == 'add') {
            //check if updated data is exist, if exist update.
            if (data !== null) {
                if (parent_id != null) {
                    if (child_id != null) {
                        if (!storedData.hasOwnProperty(parent_id)) {
                            storedData[parent_id] = [];
                        } else {
                            if (!storedData[parent_id].hasOwnProperty(child_id)) {
                                storedData[parent_id][child_id] = [];
                            }
                        }
                        storedData[parent_id][child_id] = data;
                    } else {
                        if (!storedData.hasOwnProperty(parent_id)) {
                            storedData[parent_id] = [];
                        }
                        storedData[parent_id].push(data);
                    }
                } else {
                    if (unique) {
                        if (!storedData.includes(data)) {
                            storedData.push(data);
                        }
                    } else {
                        storedData.push(data);
                    }
                }
            }
        } else if (type == 'replace') {
            //replace data in session
            if (data !== null) {
                if (parent_id != null) {
                    if (child_id != null) {
                        storedData[parent_id][child_id] = data;
                    } else {
                        storedData[parent_id] = data;
                    }
                } else {
                    storedData = data;
                }
            }
        } else {
            //if updated data is not exist delete
            if (parent_id != null) {
                if (child_id != null) {
                    if (storedData.hasOwnProperty(parent_id)) {
                        if (storedData[parent_id].hasOwnProperty(child_id)) {
                            delete storedData[parent_id][child_id];
                        }
                    }
                } else {
                    if (storedData.hasOwnProperty(parent_id)) {
                        delete storedData[parent_id];
                    }
                }
            } else {
                storedData = {};
            }
        }

        // Store the updated array back into sessionStorage / localstorage
        if (storage == 'session') {
            sessionStorage.setItem(key, JSON.stringify(storedData));
        } else {
            localStorage.setItem(key, JSON.stringify(storedData));
        }
    }

    function destroy(id) {
        Swal.fire({
            title: 'Anda yakin menghapus?',
            text: '',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: '{{ url('admin/collection/destroy') }}' + '/' + id,
                    type: 'GET',
                    dataType: 'JSON',
                    success: function(response) {
                        if (response.status == 200) {
                            $('#datatable_serverside').DataTable().ajax.reload(null, false);
                            Toast.fire({
                                icon: 'success',
                                title: response.message
                            });
                        } else {
                            Toast.fire({
                                icon: 'warning',
                                title: response.message
                            });
                        }
                    },
                    error: function() {
                        Toast.fire({
                            icon: 'error',
                            title: 'Server Error!'
                        });
                    }
                });
            }
        });
    }
</script>
