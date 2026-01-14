<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Pekan Penghargaan - <span class="fw-normal">Nominasi</span>
            </h4>
        </div>
        <div class="d-lg-flex ms-lg-auto">
            <div class="d-flex align-items-center">
                <a href="{{ url('award') }}" class="btn btn-light shadow-sm">
                    <i class="ph-arrow-left me-2"></i>
                    Kembali ke Tabel
                </a>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="card border-0 shadow-sm">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center">
                <i class="ph-trophy me-2 text-primary"></i>
                <h6 class="mb-0 fw-semibold">Data Penghargaan</h6>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <tbody>
                        <tr>
                            <th class="bg-light" width="20%">
                                <i class="ph-calendar-blank me-2 text-primary"></i>
                                Tahun
                            </th>
                            <td width="80%">
                                <span class="fw-semibold">{{ $award->YEAR }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light" width="20%">
                                <i class="ph-text-aa me-2 text-primary"></i>
                                Tema
                            </th>
                            <td width="80%">{{ $award->THEME }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light" width="20%">
                                <i class="ph-calendar-plus me-2 text-primary"></i>
                                Tgl Dibuat
                            </th>
                            <td width="80%">{{ Carbon::parse($award->CREATED_AT)->isoFormat('dddd, D MMMM Y') }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light" width="20%">
                                <i class="ph-clock-clockwise me-2 text-primary"></i>
                                Tgl Perubahan
                            </th>
                            <td width="80%">{{ Carbon::parse($award->UPDATED_AT)->isoFormat('dddd, D MMMM Y') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ph-star me-2 text-warning"></i>
                    <h6 class="mb-0 fw-semibold">Katalog Yang Masuk Nominasi</h6>
                </div>
                <span class="badge bg-warning bg-opacity-10 text-warning" id="nomination-count">
                    <i class="ph-list-checks me-1"></i>
                    <span id="nomination-total">{{ $catalog ? count($catalog) : 0 }}</span> Katalog
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered display nowrap w-100" id="datatable-nomination">
                    <thead class="table-light">
                        <tr>
                            <th class="text-nowrap" style="min-width: 250px">
                                <i class="ph-book me-1"></i>
                                Judul
                            </th>
                            <th class="text-nowrap" style="min-width: 200px">
                                <i class="ph-user-circle me-1"></i>
                                Pelaksana Serah
                            </th>
                            <th class="text-center text-nowrap" style="width: 100px">
                                <i class="ph-trash me-1"></i>
                                Hapus
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($catalog)
                            @foreach($catalog as $c)
                                <tr>
                                    <td class="align-middle text-wrap">{{ $c->TITLE_CATALOG }}</td>
                                    <td class="align-middle text-wrap">
                                        <div class="d-flex flex-column">
                                            <span class="fw-semibold">{{ $c->PENERBIT_ID_CATALOG }}</span>
                                            <small class="text-muted">{{ $c->NAME_PENERBIT }}</small>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeListNomination({{ $c->ID }}, this)">
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
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ph-funnel me-2 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Filter Pencarian</h6>
                </div>
                <button type="button" class="btn btn-sm btn-light" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="ph-caret-down"></i>
                </button>
            </div>
        </div>
        <div class="collapse" id="filterCollapse">
            <div class="card-body">
                <form id="form-filter">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ph-user-circle me-1"></i>
                                    Pelaksana Serah
                                </label>
                                <select class="form-select" name="executor_id" id="executor_id" data-placeholder="Semua Pelaksana"></select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ph-files me-1"></i>
                                    Jenis Bahan
                                </label>
                                <select class="form-select select2-basic" name="worksheet_id" id="worksheet_id" data-placeholder="Semua Jenis Bahan">
                                    <option value=""></option>
                                    @foreach($worksheet as $w)
                                        <option value="{{ $w->ID }}">{{ $w->NAME }} [{{ $w->CATEGORY }}]</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ph-book-open me-1"></i>
                                    Judul
                                </label>
                                <input type="text" class="form-control" name="title" id="title" placeholder="Cari berdasarkan judul">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ph-map-pin me-1"></i>
                                    Provinsi
                                </label>
                                <select class="form-select" name="province_id" id="province_id">
                                    @if(!Main::isSuperAdmin())
                                        <option value="{{ session('province_id') }}" selected>{{ session('province_name') }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label fw-semibold">
                                    <i class="ph-calendar-blank me-1"></i>
                                    Tahun
                                </label>
                                <input type="number" class="form-control" name="year" id="year" placeholder="Masukkan tahun">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer border-top">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ url('award/nomination/' . $award->ID) }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
                        <i class="ph-arrow-counter-clockwise me-1"></i>
                        Reset Filter
                    </a>
                    <button type="button" class="btn btn-primary" onclick="loadData()">
                        <i class="ph-magnifying-glass me-1"></i>
                        Cari Data
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ph-books me-2 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Daftar Katalog</h6>
                </div>
                <button type="button" class="btn btn-success" onclick="addListNomination()">
                    <i class="ph-list-plus me-2"></i>
                    Tambahkan ke Nominasi
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered display nowrap w-100" id="datatable-serverside">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center text-nowrap" style="width: 50px">
                                <i class="ph-check-square"></i>
                            </th>
                            <th class="text-center text-nowrap" style="width: 60px">
                                <i class="ph-hash"></i>
                            </th>
                            <th class="text-nowrap" style="min-width: 200px">
                                <i class="ph-user-circle me-1"></i>
                                Pelaksana Serah
                            </th>
                            <th class="text-nowrap" style="min-width: 250px">
                                <i class="ph-book me-1"></i>
                                Judul
                            </th>
                            <th class="text-nowrap" style="min-width: 150px">
                                <i class="ph-files me-1"></i>
                                Jenis Bahan
                            </th>
                            <th class="text-nowrap" style="min-width: 120px">
                                <i class="ph-barcode me-1"></i>
                                Kode
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        if(parseInt('{{ Main::isSuperAdmin() }}') == 0) {
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
            scrollX: true,
            language: {
                emptyTable: "Belum ada katalog yang masuk nominasi"
            }
        });

        loadData();
    });

    function onReloadTable() {
        window.gDataTable.ajax.reload(null, false);
    }

    function updateNominationCount() {
        var count = $('#datatable-nomination').DataTable().rows().count();
        $('#nomination-total').text(count);
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
                    text: '<i class="ph-microsoft-excel-logo me-2"></i> Download Excel',
                    className: 'btn btn-success',
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: '<i class="ph-download me-2"></i> Semua Data Keseluruhan',
                            exportOptions: {
                                modifier: {
                                    page: 'all',
                                    search: 'none',
                                }
                            }
                        },
                        {
                            extend: 'excelHtml5',
                            text: '<i class="ph-funnel me-2"></i> Semua Data Dengan Pencarian',
                            exportOptions: {
                                modifier: {
                                    page: 'all',
                                    search: 'applied',
                                }
                            }
                        },
                        {
                            extend: 'excelHtml5',
                            text: '<i class="ph-file me-2"></i> Halaman Ini Saja',
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
                    className: 'btn btn-primary',
                    text: '<i class="ph-checks me-2"></i> Centang Semua'
                },
                {
                    extend: 'selectNone',
                    className: 'btn btn-warning',
                    text: '<i class="ph-x me-2"></i> Hilangkan Centang'
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
                { orderable: true, className: 'align-middle text-center fw-semibold allow-select' },
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

        if(catalog.length === 0) {
            swalInit.fire({
                title: 'Peringatan',
                text: 'Silakan pilih katalog terlebih dahulu',
                icon: 'warning',
                showCloseButton: false
            });
            return;
        }

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
                        var executorDisplay = `
                            <div class="d-flex flex-column">
                                <span class="fw-semibold">${val.executor.split('|')[0].trim()}</span>
                                <small class="text-muted">${val.executor.split('|')[1] ? val.executor.split('|')[1].trim() : ''}</small>
                            </div>
                        `;

                        var btnRemove = `
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeListNomination(${ val.id }, this)">
                                <i class="ph-trash"></i>
                            </button>
                        `;

                        $('#datatable-nomination').DataTable().row.add([
                            val.title,
                            executorDisplay,
                            btnRemove
                        ]).draw().node();
                    });

                    updateNominationCount();
                    $('.buttons-select-none').click();

                    swalInit.fire({
                        title: 'Berhasil',
                        text: response.message,
                        icon: 'success'
                    });
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
                Noty.button('Ya, Hapus', 'btn btn-danger ms-2', function () {
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

                                updateNominationCount();

                                notification('success', 'Katalog berhasil dihapus dari nominasi');
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
