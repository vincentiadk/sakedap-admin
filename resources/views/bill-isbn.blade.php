<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">Tagihan ISBN</h4>
            <a href="#page-header" class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto" data-bs-toggle="collapse">
                <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
            </a>
        </div>
    </div>
    <div class="page-header-content d-lg-flex border-top">
        <div class="d-flex">
            <div class="breadcrumb py-2">
                <a href="{{ url('home') }}" class="breadcrumb-item"><i class="ph-house"></i></a>
                <span class="breadcrumb-item active">Tagihan ISBN</span>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="card">
        <div class="card-header">
            <h5 class="hstack gap-2 mb-0">Pilih Penerbit</h5>
        </div>
        <div class="card-body">
            <select class="form-select" name="publisher_id" id="publisher_id" data-placeholder="Pilih" data-allow-clear="true" onchange="changePublisher()"></select>
        </div>
    </div>
    <div id="content-on-change-publisher"></div>
</div>

<script>
    $(function() {
        select2Serverside('#publisher_id', 'publisher');
    });

    function changePublisher() {
        var publisherId = $('#publisher_id').val();

        $('#content-on-change-publisher').html('');

        if(publisherId) {
            $('#content-on-change-publisher').html(`
                <div class="card">
                    <div class="card-header">
                        <h5 class="hstack gap-2 mb-0">Filter Data</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Judul :</label>
                                    <input type="text" class="form-control" name="title" id="title" placeholder="....................">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Kepeng :</label>
                                    <input type="text" class="form-control" name="author" id="author" placeholder="....................">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Tahun Terbit :</label>
                                    <input type="text" class="form-control" name="year" id="year" placeholder="....................">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Tempat Terbit :</label>
                                    <input type="text" class="form-control" name="city" id="city" placeholder="....................">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Nomor ISBN :</label>
                                    <input type="text" class="form-control" name="code" id="code" placeholder="....................">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Subjek :</label>
                                    <input type="text" class="form-control" name="subject" id="subject" placeholder="....................">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Media :</label>
                                    <select class="form-select" name="media" id="media">
                                        <option value="">Semua</option>
                                        <option value="cetak">Cetak</option>
                                        <option value="digital">Digital</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <div class="text-end">
                            <a href="{{ url('bill-isbn') }}" class="btn btn-danger" onclick="onLoading('show', 'body')">
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
                                    <th nowrap>No</th>
                                    <th nowrap>Judul</th>
                                    <th nowrap>Kepeng</th>
                                    <th nowrap>Penerbit</th>
                                    <th nowrap>Tahun</th>
                                    <th nowrap>Tempat</th>
                                    <th nowrap>Provinsi</th>
                                    <th nowrap>ISBN</th>
                                    <th nowrap>Media</th>
                                    <th nowrap>Pustaka</th>
                                    <th nowrap>Tgl Terima KCKR</th>
                                    <th nowrap>Sinopsis</th>
                                    <th nowrap>Tgl Terima</th>
                                    <th nowrap>Tgl Dibuat</th>
                                    <th nowrap>Tgl Update</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            `);

            loadData();
        }
    }

    function loadData() {
        window.gDataTable = $('#datatable-serverside').DataTable({
            processing: true,
            serverSide: true,
            deferRender: true,
            scrollX: true,
            destroy: true,
            ajax: {
                url: '{{ url("bill-isbn/datatable") }}',
                dataType: 'JSON',
                data: {
                    publisher_id: $('#publisher_id').val(),
                    title: $('#title').val(),
                    author: $('#author').val(),
                    year: $('#year').val(),
                    city: $('#city').val(),
                    code: $('#code').val(),
                    subject: $('#subject').val(),
                    media: $('#media').val(),
                },
                beforeSend: function() {
                    onLoading('show', '.dataTables_wrapper');
                },
                error: function(response) {
                    onLoading('close', '.dataTables_wrapper');

                    swalInit.fire({
                        html: '<b>' + response.responseJSON.exception + '</b><br>' + response.responseJSON.message,
                        icon: 'error',
                        showCloseButton: false
                    });
                }
            },
            columns: [
                { orderable: false, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle text-wrap' },
                { orderable: false, className: 'align-middle text-wrap' },
                { orderable: false, className: 'align-middle text-wrap' },
                { orderable: false, className: 'align-middle' },
                { orderable: false, className: 'align-middle text-wrap' },
                { orderable: false, className: 'align-middle text-wrap' },
                { orderable: false, className: 'align-middle' },
                { orderable: false, className: 'align-middle' },
                { orderable: false, className: 'align-middle text-wrap' },
                { orderable: false, className: 'align-middle' },
                { orderable: false, className: 'align-middle text-wrap' },
                { orderable: false, className: 'align-middle' },
                { orderable: false, className: 'align-middle' },
                { orderable: false, className: 'align-middle' },
            ]
        }).on('draw.dt', function() {
            onLoading('close', '.dataTables_wrapper');
        });

        window.gDataTable.columns.adjust().draw();
    }
</script>
