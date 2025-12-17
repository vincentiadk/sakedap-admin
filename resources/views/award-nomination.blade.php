<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pekan Penghargaan - <span class="fw-normal">Nominasi</span>
            </h4>
        </div>
        <div class="collapse d-lg-block my-lg-auto ms-lg-auto" id="page-header">
            <div class="d-sm-flex align-items-center mb-3 mb-lg-0 ms-lg-3">
                <div class="d-inline-flex mt-3 mt-sm-0">
                    <a href="{{ url('award') }}" class="btn btn-primary">
                        <i class="ph-arrow-left me-1"></i>
                        Kembali ke Tabel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="card">
        <div class="card-header">
            <h5 class="hstack gap-2 mb-0">Data Penghargaan</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th class="table-success" width="20%">Tahun</th>
                        <td width="80%">{{ $award->YEAR }}</td>
                    </tr>
                    <tr>
                        <th class="table-success" width="20%">Tema</th>
                        <td width="80%">{{ $award->THEME }}</th>
                    </tr>
                    <tr>
                        <th class="table-success" width="20%">Tgl Dibuat</th>
                        <td width="80%">{{ Carbon::parse($award->CREATED_AT)->isoFormat('dddd, D MMMM Y') }}</th>
                    </tr>
                    <tr>
                        <th class="table-success" width="20%">Tgl Perubahan</th>
                        <td width="80%">{{ Carbon::parse($award->UPDATED_AT)->isoFormat('dddd, D MMMM Y') }}</th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="hstack gap-2 mb-0">Katalog Yang Masuk Nominasi</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover w-100 display" id="datatable-nomination">
                <thead class="text-bg-light">
                    <tr>
                        <th class="text-nowrap">Judul</th>
                        <th class="text-nowrap">Pelaksana Serah</th>
                        <th class="text-center">Hapus</th>
                    </tr>
                </thead>
                <tbody>
                    @if($catalog)
                        @foreach($catalog as $c)
                            <tr>
                                <td class="text-wrap">{{ $c->TITLE_CATALOG }}</td>
                                <td class="text-wrap">{{ $c->PENERBIT_ID_CATALOG }} | {{ $c->NAME_PENERBIT }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm col-12" id="btn-remove-nomination" onclick="removeListNomination({{ $c->ID }}, this)">
                                        <i class="ph-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="hstack gap-2 mb-0">Filter Data</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">Pelaksana Serah :</label>
                        <select class="form-select" name="executor_id" id="executor_id" data-placeholder="Semua"></select>
                    </div>
                </div>
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Judul :</label>
                        <input type="text" class="form-control" name="title" id="title" placeholder="....................">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Provinsi :</label>
                        <select class="form-select" name="province_id" id="province_id">
                            @if(Main::isNotSuperAdmin())
                                <option value="{{ session('province_id') }}" selected>{{ session('province_name') }}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">Tahun :</label>
                        <input type="number" class="form-control" name="year" id="year" placeholder="....................">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="text-end">
                <a href="{{ url('award/nomination/' . $award->ID) }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
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
        <div class="card-header d-flex align-items-center py-0">
            <h5 class="py-3 mb-0">Daftar Katalog</h5>
            <div class="ms-auto my-auto">
                <button type="button" class="btn btn-teal" onclick="addListNomination()">
                    <i class="ph-list-plus me-1"></i>
                    Tambahkan ke Nominasi
                </button>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover w-100 display" id="datatable-serverside">
                <thead class="text-bg-light">
                    <tr>
                        <th class="text-nowrap">#</th>
                        <th class="text-nowrap">No</th>
                        <th class="text-nowrap">Pelaksana Serah</th>
                        <th class="text-nowrap">Judul</th>
                        <th class="text-nowrap">Jenis Bahan</th>
                        <th class="text-nowrap">Kode</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    $(function() {
        if(parseInt('{{ Main::isNotSuperAdmin() }}') === 1) {
            select2Serverside('#province_id', 'location', {
                for: 'province',
                province_id: '{{ session("province_id") }}',
            }, {
                minimumInputLength: 0
            });

            select2Serverside('#executor_id', 'executor', {
                province_id: '{{ session("province_id") }}',
            });
        } else {
            select2Serverside('#executor_id', 'executor');

            select2Serverside('#province_id', 'location', {
                for: 'province'
            }, {
                minimumInputLength: 0
            });
        }

        $('#datatable-nomination').DataTable({
            scrollX: true
        });

        loadData();
    });

    function onReloadTable() {
        window.gDataTable.ajax.reload(null, false);
    }

    function loadData() {
        window.gDataTable = $('#datatable-serverside').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            scrollX: true,
            destroy: true,
            order: [[1, 'desc']],
            columnDefs: [
                {
                    orderable: false,
                    className: 'select-checkbox',
                    targets: 0
                },
            ],
            select: {
                style: 'multi',
                selector: 'td.allow-select'
            },
            buttons: [
                {
                    extend: 'collection',
                    text: '<i class="ph-microsoft-excel-logo me-1"></i> Download Excel',
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: 'Semua Data Keseluruhan',
                            exportOptions: {
                                modifier: {
                                    page: 'all',
                                    search: 'none',
                                }
                            }
                        },
                        {
                            extend: 'excelHtml5',
                            text: 'Semua Data Dengan Pencarian',
                            exportOptions: {
                                modifier: {
                                    page: 'all',
                                    search: 'applied',
                                }
                            }
                        },
                        {
                            extend: 'excelHtml5',
                            text: 'Halaman Ini Saja',
                            exportOptions: {
                                modifier: {
                                    page: 'current',
                                }
                            }
                        },
                    ]
                },
                {
                    extend: 'selectAll',
                    className: 'btn btn-success',
                    text: '<i class="ph-checks me-1"></i> Centang Semua'
                },
                {
                    extend: 'selectNone',
                    className: 'btn btn-warning',
                    text: '<i class="ph-x me-1"></i> Hilangkan Semua Centang'
                },
            ],
            ajax: {
                url: '{{ url("award/nomination/$award->ID/datatable") }}',
                dataType: 'JSON',
                data: {
                    title: $('#title').val(),
                    executor_id: $('#executor_id').val(),
                    province_id: $('#province_id').val(),
                    year: $('#year').val(),
                    worksheet_id: $('#worksheet_id').val(),
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
                { orderable: false, className: 'align-middle text-center allow-select' },
                { orderable: true, className: 'align-middle text-center allow-select' },
                { orderable: true, className: 'align-middle text-wrap allow-select' },
                { orderable: true, className: 'align-middle text-wrap allow-select' },
                { orderable: true, className: 'align-middle text-wrap allow-select' },
                { orderable: true, className: 'align-middle allow-select' },
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

    function addListNomination() {
        let catalog = [];

        window.gDataTable.rows({ selected: true }).every(function() {
            var row = this.node();
            var data = $(row).find('input[name="data"]');
            var id = data.data('id');
            var title = data.data('title');
            var executor = data.data('executor');

            catalog.push({
                id: id,
                executor: executor,
                title: title,
            });
        });

        $.ajax({
            url: '{{ url("award/nomination/$award->ID/add") }}',
            type: 'POST',
            dataType: 'JSON',
            data: {
                catalog: catalog
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            beforeSend: function() {
                onLoading('show', 'body');
            },
            success: function(response) {
                onLoading('close', 'body');

                if(response.code == 200) {
                    $.each(catalog, function(i, val) {
                        var btnRemove = `
                            <button type="button" class="btn btn-danger btn-sm col-12" onclick="removeListNomination(${ val.id }, this)">
                                <i class="ph-trash"></i>
                            </button>
                        `;

                        $('#datatable-nomination').DataTable().row.add([
                            val.title,
                            val.executor,
                            btnRemove
                        ]).draw().node();
                    });

                    $('.buttons-select-none').click();

                    swalInit.fire({
                        title: 'Berhasil',
                        text: response.message,
                        icon: 'success'
                    });
                }else {
                    swalInit.fire({
                        title: 'Error',
                        text: response.message,
                        icon: 'error',
                        showCloseButton: false
                    });
                }
            },
            error: function(response) {
                onLoading('close', 'body');
                responseError(response);
            }
        });
    }

    function removeListNomination(id, param) {
        var notyConfirm = new Noty({
            text: '<div class="mb-3"><h5 class="text-dark">Hapus Nominasi?</h5><span class="text-muted">Anda yakin ingin menghapus nominasi katalog ini?</span></div>',
            timeout: false,
            modal: true,
            layout: 'center',
            closeWith: 'button',
            type: 'confirm',
            buttons: [
                Noty.button('Tidak', 'btn btn-light', function () {
                    notyConfirm.close();
                }),
                Noty.button('Ya', 'btn btn-danger ms-2', function () {
                    $.ajax({
                        url: '{{ url("award/nomination/$award->ID/remove") }}',
                        type: 'DELETE',
                        dataType: 'JSON',
                        data: {
                            id: id
                        },
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        beforeSend: function() {
                            onLoading('show', '.noty_bar');
                        },
                        success: function(response) {
                            onLoading('close', '.noty_bar');

                            if(response.code == 200) {
                                notyConfirm.close();

                                var $row = $(param).closest('tr');

                                $('#datatable-nomination').DataTable().row($row).remove().draw(false);
                            } else {
                                swalInit.fire({
                                    title: 'Error',
                                    text: response.message,
                                    icon: 'error',
                                    showCloseButton: false
                                });
                            }
                        },
                        error: function(response) {
                            onLoading('close', '.noty_bar');
                            responseError(response);
                        }
                    });
                })
            ]
        }).show();
    }
</script>
