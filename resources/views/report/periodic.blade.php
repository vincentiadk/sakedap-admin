<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">Periodik</h4>
            <a href="#page-header" class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto" data-bs-toggle="collapse">
                <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
            </a>
        </div>
    </div>
    <div class="page-header-content d-lg-flex border-top">
        <div class="d-flex">
            <div class="breadcrumb py-2">
                <a href="{{ url('home') }}" class="breadcrumb-item"><i class="ph-house"></i></a>
                <a href="javascript:void(0);" class="breadcrumb-item">Laporan</a>
                <span class="breadcrumb-item active">Periodik</span>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="card">
        <div class="card-body">
            <div class="input-group">
                <span class="input-group-text">Tahun</span>
                <select class="form-select" name="year" id="year" onchange="loadData()">
                    @for($i = 2019; $i <= date('Y'); $i++)
                        <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
                <button type="button" class="btn btn-success" onclick="downloadExcel()">
                    <i class="ph-microsoft-excel-logo me-1"></i>
                    Download
                </button>
            </div>
            <div><hr></div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover w-100">
                    <thead class="text-bg-light">
                        <tr>
                            <th nowrap>Jenis</th>
                            @for($i = 1; $i <= 12; $i++)
                                <th class="text-center" nowrap>{{ \Carbon\Carbon::parse(date('Y') . '-' . sprintf('%02s', $i))->isoFormat('MMMM') }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody id="table-data"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        loadData();
    });

    function loadData() {
        $.ajax({
            url: '{{ url("report/periodic/load-data") }}',
            type: 'GET',
            dataType: 'JSON',
            data: {
                year: $('#year').val()
            },
            beforeSend: function() {
                onLoading('show', 'body');

                $('#table-data').html('');
            },
            success: function(response) {
                $.each(response, function(i, val) {
                    var dataTD = '';

                    $.each(val.data, function(index, value) {
                        dataTD += `
                            <td class="text-center" nowrap>${ value }</td>
                        `;
                    });

                    $('#table-data').append(`
                        <tr>
                            <td nowrap>${ val.name }</td>
                            ${ dataTD }
                        </tr>
                    `);
                });

                onLoading('close', 'body');
            },
            error: function(response) {
                onLoading('close', 'body');

                swalInit.fire({
                    html: '<b>' + response.responseJSON.exception + '</b><br>' + response.responseJSON.message,
                    icon: 'error',
                    showCloseButton: false
                });
            }
        });
    }
</script>
