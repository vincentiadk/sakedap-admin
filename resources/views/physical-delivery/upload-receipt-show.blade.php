<div class="content pt-0">
    <h4 class="mb-3">Detail Histori Upload Resi</h4>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 border-end">
                    <div><strong>ID:</strong> {{ $history->ID }}</div>
                    <div><strong>Pelaksana Serah:</strong> {{ $history->PENERBIT_NAME ?? '-' }}</div>
                    <div><strong>Nomor Resi:</strong> {{ $history->RECEIPT_NO ?? '-' }}</div>
                    <div><strong>File Excel:</strong> {{ $history->FILE_NAME ?? '-' }}</div>
                    <div><strong>Status:</strong> <span id="main-status">{{ $history->STATUS }}</span></div>
                    <div><strong>Total:</strong> <span id="main-total">{{ $history->TOTAL_ROWS ?? 0 }}</span></div>
                    <div><strong>Diproses:</strong> <span id="main-processed">{{ $history->PROCESSED_ROWS ?? 0 }}</span></div>
                    <div><strong>Berhasil:</strong> <span id="main-success">{{ $history->SUCCESS_ROWS ?? 0 }}</span></div>
                    <div><strong>Gagal:</strong> <span id="main-failed">{{ $history->FAILED_ROWS ?? 0 }}</span></div>
                    <div><strong>Letter ID:</strong> {{ $history->LETTER_ID ?? '-' }}</div>
                    <div><strong>Catatan:</strong> <span id="main-notes">{{ $history->NOTES ?? '-' }}</span></div>
                </div>

                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Progress Realtime</strong>
                        <small id="rt-updated" class="text-muted">-</small>
                    </div>

                    <div class="row text-center mb-2">
                        <div class="col-3">
                            <div class="small text-muted">Processed</div>
                            <div id="rt-processed" class="fw-bold">0</div>
                        </div>
                        <div class="col-3">
                            <div class="small text-muted">Success</div>
                            <div id="rt-success" class="fw-bold text-success">0</div>
                        </div>
                        <div class="col-3">
                            <div class="small text-muted">Failed</div>
                            <div id="rt-failed" class="fw-bold text-danger">0</div>
                        </div>
                        <div class="col-3">
                            <div class="small text-muted">Pending</div>
                            <div id="rt-pending" class="fw-bold text-warning">0</div>
                        </div>
                    </div>

                    <div class="progress mb-3" style="height: 20px;">
                        <div
                            id="rt-progress-bar"
                            class="progress-bar progress-bar-striped progress-bar-animated"
                            role="progressbar"
                            style="width: 0%;"
                            aria-valuenow="0"
                            aria-valuemin="0"
                            aria-valuemax="100"
                        >
                            0%
                        </div>
                    </div>

                    <div class="small">
                        <div><strong>Row terakhir:</strong> <span id="rt-latest-row">-</span></div>
                        <div><strong>Status:</strong> <span id="rt-latest-status">-</span></div>
                        <div><strong>ISBN:</strong> <span id="rt-latest-isbn">-</span></div>
                        <div><strong>Judul:</strong> <span id="rt-latest-title">-</span></div>
                        <div><strong>Pesan:</strong> <span id="rt-latest-message">-</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Detail per Row</div>
        <div class="card-body table-responsive">
            <table class="table table-hover table-bordered display w-100" id="datatable-serverside">
                <thead>
                    <tr>
                        <th>Row</th>
                        <th>ISBN</th>
                        <th>Judul</th>
                        <th>Jumlah Kirim</th>
                        <th>Seharusnya Diterima</th>
                        <th>Diterima</th>
                        <th>Ditolak</th>
                        <th>Status</th>
                        <th>Pesan</th>
                        <th>Tanggal Upload</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
const historyId = "{{ $id }}";
const realtimeUrl = "{{ url('physical-delivery/upload-receipt/history/progress-realtime') }}/" + historyId;

function badgeClass(status) {
    switch (status) {
        case 'success': return 'bg-success';
        case 'failed': return 'bg-danger';
        case 'running': return 'bg-primary';
        case 'pending': return 'bg-warning text-dark';
        case 'rejected': return 'bg-danger';
        case 'not_found': return 'bg-secondary';
        default: return 'bg-secondary';
    }
}

$(function() {
    setInterval(loadRealtimeProgress, 3000);
    loadRealtimeProgress();
    loadData();

    function loadRealtimeProgress() {
        $.get(realtimeUrl, function(res) {
            const summary = res.summary || {};
            const latest = summary.latest_row || {};

            $('#rt-processed').text(summary.processed_rows ?? 0);
            $('#rt-success').text(summary.success_rows ?? 0);
            $('#rt-failed').text(summary.failed_rows ?? 0);
            $('#rt-pending').text(summary.pending_rows ?? 0);

            const percent = summary.percent ?? 0;

            $('#rt-progress-bar')
                .css('width', percent + '%')
                .attr('aria-valuenow', percent)
                .text(percent + '%');

            $('#rt-latest-row').text(latest.row_number_upload ?? '-');

            $('#rt-latest-status').html(
                latest.status
                    ? `<span class="badge ${badgeClass(latest.status)}">${latest.status}</span>`
                    : '-'
            );

            $('#rt-latest-isbn').text(latest.isbn ?? '-');
            $('#rt-latest-title').text(latest.title ?? '-');
            $('#rt-latest-message').text(latest.message ?? '-');
            $('#rt-updated').text(latest.updated_at ?? '-');

        }).fail(function() {
            console.log('Gagal ambil progress realtime');
        });
    }
});

function loadData() {
    if ($.fn.DataTable.isDataTable('#datatable-serverside')) {
        window.gDataTable.ajax.reload();
        return;
    }

    window.gDataTable = $('#datatable-serverside').DataTable({
        processing: true,
        serverSide: true,
        deferRender: true,
        scrollX: true,
        destroy: true,
        order: [[0, 'asc']],
        ajax: {
            url: '{{ url("physical-delivery/upload-receipt/history/datatable/" . $id) }}',
            dataType: 'JSON',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
            { orderable: true, className: 'align-middle text-wrap fw-semibold' },
            { orderable: true, className: 'align-middle text-wrap' },
            { orderable: true, className: 'align-middle text-wrap' },
            { orderable: true, className: 'align-middle text-center' },
            { orderable: true, className: 'align-middle text-center' },
            { orderable: true, className: 'align-middle text-center' },
            { orderable: true, className: 'align-middle text-center' },
            { orderable: true, className: 'align-middle text-wrap' },
            { orderable: true, className: 'align-middle text-wrap' },
            { orderable: true, className: 'align-middle text-wrap' },
        ],
        initComplete: function () {
            var table = this.api();
            const searchInput = $('div.dataTables_filter input');

            searchInput.off().unbind();

            searchInput.on('keyup', debounce(function () {
                table.search(this.value).draw();
            }, 500));
        }
    }).on('draw.dt', function() {
        onLoading('close', '#datatable-serverside_wrapper');
    });
}
</script>