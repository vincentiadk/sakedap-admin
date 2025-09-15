<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                <span class="fw-normal">Tagihan ISBN</span>
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
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label">Judul :</label>
                        <input type="text" class="form-control" name="title" id="title" placeholder="....................">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Penerbit :</label>
                        <input type="text" class="form-control" name="publisher" id="publisher" placeholder="....................">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Kepeng :</label>
                        <input type="text" class="form-control" name="author" id="author" placeholder="....................">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Tahun Terbit :</label>
                        <select class="form-select" name="year" id="year">
                            <option value="">Semua</option>
                            @for($i = 2019; $i <= date('Y'); $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Tempat Terbit :</label>
                        <input type="text" class="form-control" name="city" id="city" placeholder="....................">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Nomor ISBN :</label>
                        <input type="text" class="form-control" name="code" id="code" placeholder="....................">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Subjek :</label>
                        <input type="text" class="form-control" name="subject" id="subject" placeholder="....................">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Media :</label>
                        <select class="form-select" name="media" id="media">
                            <option value="">Semua</option>
                            <option value="cetak">Cetak</option>
                            <option value="digital pdf">Digital PDF</option>
                            <option value="digital epub">Digital EPUB</option>
                            <option value="audio book">Audio Book</option>
                            <option value="audio visual book">Audio Visual Book</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Provinsi :</label>
                        <select class="form-select" name="province_id" id="province_id">
                            @if(Main::isNotCenterBranch())
                                <option value="{{ session('province_id') }}" selected>{{ session('province_name') }}</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Tgl Terima KCKR :</label>
                        <input type="text" class="form-control date-range-picker" name="received_date_kckr" id="received_date_kckr" placeholder="Semua Tanggal" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Tgl Terima Provinsi :</label>
                        <input type="text" class="form-control date-range-picker" name="received_date_province" id="received_date_province" placeholder="Semua Tanggal" readonly>
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
                        <th nowrap>Tgl Terima Provinsi</th>
                        <th nowrap>Sinopsis</th>
                        <th nowrap>Tgl Terima</th>
                        <th nowrap>Tgl Dibuat</th>
                        <th nowrap>Tgl Update</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    $(function() {
        datePickerBasic('.date-range-picker');

        if(parseInt('{{ Main::isNotCenterBranch() }}') === 1) {
            select2Serverside('#province_id', 'location', {
                for: 'province',
                province_id: '{{ session("province_id") }}',
            }, {
                minimumInputLength: 0
            });
        } else {
            select2Serverside('#province_id', 'location');
        }

        loadData();
    });

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
                    publisher: $('#publisher').val(),
                    title: $('#title').val(),
                    author: $('#author').val(),
                    year: $('#year').val(),
                    city: $('#city').val(),
                    code: $('#code').val(),
                    subject: $('#subject').val(),
                    media: $('#media').val(),
                    province_id: $('#province_id').val(),
                    received_date_kckr: $('#received_date_kckr').val(),
                    received_date_province: $('#received_date_province').val(),
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
                { orderable: false, className: 'align-middle text-center' },
                { orderable: false, className: 'align-middle text-wrap' },
                { orderable: false, className: 'align-middle' },
                { orderable: false, className: 'align-middle text-wrap' },
                { orderable: false, className: 'align-middle text-wrap' },
                { orderable: false, className: 'align-middle' },
                { orderable: false, className: 'align-middle' },
                { orderable: false, className: 'align-middle text-wrap' },
                { orderable: false, className: 'align-middle' },
                { orderable: false, className: 'align-middle' },
                { orderable: false, className: 'align-middle text-center' },
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
