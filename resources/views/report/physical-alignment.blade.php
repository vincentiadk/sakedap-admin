<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Laporan - <span class="fw-normal">Penjajaran Fisik</span>
            </h4>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="card">
        <div class="card-header">
            <h5 class="hstack gap-2 mb-0">Filter Data</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Tanggal :</label>
                        <input type="text" class="form-control" name="date" id="date" placeholder="Semua Tanggal" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Jenis Bahan :</label>
                        <select class="form-select select2-basic" name="worksheet_id" id="worksheet_id" data-placeholder="Semua">
                            <option value=""></option>
                            @foreach($worksheet as $w)
                                <option value="{{ $w->ID }}">{{ $w->NAME }} [{{ $w->CATEGORY }}]</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Media :</label>
                        <select class="form-select select2-basic" name="media_id" id="media_id" data-placeholder="Semua">
                            <option value=""></option>
                            @foreach($media as $m)
                                <option value="{{ $m->ID }}">{{ $m->NAME }} [{{ $m->DEPOSITFORMAT_CODE }}]</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Sumber :</label>
                        <select class="form-select select2-basic" name="source_id" id="source_id" data-placeholder="Semua">
                            <option value=""></option>
                            @foreach($source as $s)
                                <option value="{{ $s->ID }}">{{ $s->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Kategori :</label>
                        <select class="form-select select2-basic" name="category_id" id="category_id" data-placeholder="Semua">
                            <option value=""></option>
                            @foreach($category as $c)
                                <option value="{{ $c->ID }}">{{ $c->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Akses :</label>
                        <select class="form-select select2-basic" name="access_id" id="access_id" data-placeholder="Semua">
                            <option value=""></option>
                            @foreach($access as $a)
                                <option value="{{ $a->ID }}">{{ $a->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Lokasi :</label>
                        <select class="form-select select2-basic" name="location_id" id="location_id" data-placeholder="Semua">
                            <option value=""></option>
                            @foreach($location as $l)
                                <option value="{{ $l->ID }}">{{ $l->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Rak :</label>
                        <select class="form-select select2-basic" name="rack_id" id="rack_id" data-placeholder="Semua">
                            <option value=""></option>
                            @foreach($rack as $r)
                                <option value="{{ $r->ID }}">{{ $r->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Ambal :</label>
                        <select class="form-select select2-basic" name="carpet_id" id="carpet_id" data-placeholder="Semua">
                            <option value=""></option>
                            @foreach($carpet as $c)
                                <option value="{{ $c->ID }}">{{ $c->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Ketersediaan :</label>
                        <select class="form-select select2-basic" name="availability" id="availability" data-placeholder="Semua">
                            <option value=""></option>
                            @foreach($availability as $a)
                                <option value="{{ $a->NAME }}">{{ $a->NAME }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="text-end">
                <a href="{{ url('report/physical-alignment') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
                    <i class="ph-arrows-clockwise me-1"></i>
                    Reset Filter
                </a>
                <a href="javascript:void(0);" class="btn btn-success" onclick="loadData()">
                    <i class="ph-magnifying-glass me-1"></i>
                    Cari Data
                </a>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover w-100 display" id="datatable-serverside">
                <thead class="text-bg-light">
                    <tr>
                        <th class="text-nowrap">No</th>
                        <th class="text-nowrap">Item ID</th>
                        <th class="text-nowrap">No Induk</th>
                        <th class="text-nowrap">Data Bibliografis</th>
                        <th class="text-nowrap">No Panggil</th>
                        <th class="text-nowrap">Jenis Bahan</th>
                        <th class="text-nowrap">Media</th>
                        <th class="text-nowrap">Kategori</th>
                        <th class="text-nowrap">Akses</th>
                        <th class="text-nowrap">Ketersediaan</th>
                        <th class="text-nowrap">Lokasi</th>
                        <th class="text-nowrap">Rak</th>
                        <th class="text-nowrap">Ambal</th>
                        <th class="text-nowrap">Tgl Shelving</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    $(function() {
        datePickerBasic('#date');

        loadData();
    });

    function loadData() {
        window.gDataTable = $('#datatable-serverside').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            scrollX: true,
            destroy: true,
            order: [[0, 'desc']],
            ajax: {
                url: '{{ url("report/physical-alignment/datatable") }}',
                dataType: 'JSON',
                data: {
                    worksheet_id: $('#worksheet_id').val(),
                    source_id: $('#source_id').val(),
                    media_id: $('#media_id').val(),
                    category_id: $('#category_id').val(),
                    access_id: $('#access_id').val(),
                    availability: $('#availability').val(),
                    location_id: $('#location_id').val(),
                    rack_id: $('#rack_id').val(),
                    carpet_id: $('#carpet_id').val(),
                    date: $('#date').val(),
                },
                beforeSend: function() {
                    onLoading('show', '#datatable-serverside_wrapper');
                },
                error: function(response) {
                    onLoading('close', '#datatable-serverside_wrapper');
                    responseError(response);
                }
            },
            columns: [
                { orderable: true, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
            ],
            initComplete: function (settings, json) {
                var table = this.api();
                const searchInput = $('div.dataTables_filter input');

                searchInput.off().unbind();

                searchInput.on('keyup', debounce(function () {
                    table.search(this.value).draw();
                }, 500));
            },
        }).on('draw.dt', function() {
            onLoading('close', '#datatable-serverside_wrapper');
        });

        window.gDataTable.columns.adjust().draw();
    }
</script>
