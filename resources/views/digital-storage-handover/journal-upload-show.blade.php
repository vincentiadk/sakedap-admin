<div class="container">
    <h4 class="mb-3">Detail Histori Upload</h4>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <!-- KIRI: INFO UTAMA -->
                <div class="col-md-6 border-end">
                    <div><strong>ID:</strong> {{ $history->ID }}</div>
                    <div><strong>Nama ZIP:</strong> {{ $history->ZIP_NAME }}</div>
                    <div><strong>Status:</strong> <span id="main-status">{{ $history->STATUS }}</span></div>
                    <div><strong>Total:</strong> <span id="main-total">{{ $history->TOTAL_ROWS ?? 0 }}</span></div>
                    <div><strong>Diproses:</strong> <span id="main-processed">{{ $history->PROCESSED_ROWS ?? 0 }}</span></div>
                    <div><strong>Berhasil:</strong> <span id="main-success">{{ $history->SUCCESS_ROWS ?? 0 }}</span></div>
                    <div><strong>Gagal:</strong> <span id="main-failed">{{ $history->FAILED_ROWS ?? 0 }}</span></div>
                    <div><strong>Catatan:</strong> <span id="main-notes">{{ $history->NOTES ?? '-' }}</span></div>
                </div>

                <!-- KANAN: REALTIME REDIS -->
                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Progress Realtime</strong>
                        <small id="rt-updated" class="text-muted">-</small>
                    </div>

                    <div class="row text-center mb-2">
                        <div class="col-4">
                            <div class="small text-muted">Processed</div>
                            <div id="rt-processed" class="fw-bold">0</div>
                        </div>
                        <div class="col-4">
                            <div class="small text-muted">Success</div>
                            <div id="rt-success" class="fw-bold text-success">0</div>
                        </div>
                        <div class="col-4">
                            <div class="small text-muted">Failed</div>
                            <div id="rt-failed" class="fw-bold text-danger">0</div>
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
                        <div><strong>File:</strong> <span id="rt-latest-file">-</span></div>
                        <div><strong>Pesan:</strong> <span id="rt-latest-message">-</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">Detail per Row</div>
        <div class="card-body table-responsive">
            <table class="table table-hover table-bordered display nowrap w-100" id="datatable-serverside">
                <thead>
                    <tr>
                        <th>Row</th>
                        <th>Judul</th>
                        <th>File PDF</th>
                        <th>Status</th>
                        <th>Pesan</th>
                        <th>ID Koleksi</th>
                        <th>Tanggal Upload</th>
                    </tr>
                </thead>

            </table>
        </div>
    </div>
</div>
<script>
    const historyId = "{{ $id }}";
    const realtimeUrl = "{{ route('journal.zip.progress-realtime', ['id' => '__ID__']) }}".replace('__ID__', historyId);

    function badgeClass(status) {
        switch (status) {
            case 'success': return 'bg-success';
            case 'failed': return 'bg-danger';
            case 'running': return 'bg-primary';
            case 'pending': return 'bg-warning text-dark';
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
            const rows = res.rows || [];

            $('#rt-total').text(summary.total_rows_in_redis ?? 0);
            $('#rt-processed').text(summary.processed_rows ?? 0);
            $('#rt-success').text(summary.success_rows ?? 0);
            $('#rt-failed').text(summary.failed_rows ?? 0);
            $('#rt-running').text(summary.running_rows ?? 0);
            $('#rt-pending').text(summary.pending_rows ?? 0);

            const percent = summary.percent ?? 0;
            $('#rt-progress-bar')
                .css('width', percent + '%')
                .attr('aria-valuenow', percent)
                .text(percent + '%');

            const latest = summary.latest_row || {};
            $('#rt-latest-row').text(latest.row_number_upload ?? '-');
            $('#rt-latest-status').html(
                latest.status
                    ? `<span class="badge ${badgeClass(latest.status)}">${latest.status}</span>`
                    : '-'
            );
            $('#rt-latest-file').text(latest.file_name ?? '-');
            $('#rt-latest-message').text(latest.message ?? '-');
            $('#rt-latest-updated').text(latest.updated_at ?? '-');

            const activeRows = rows.filter(r => ['running', 'pending'].includes(r.status));

            if (activeRows.length === 0) {
                $('#rt-active-rows').html('<em>Tidak ada row yang sedang diproses</em>');
            } else {
                let html = '<div class="table-responsive"><table class="table table-sm table-bordered">';
                html += `
                    <thead>
                        <tr>
                            <th>Row</th>
                            <th>Status</th>
                            <th>File</th>
                            <th>Pesan</th>
                            <th>Update</th>
                        </tr>
                    </thead>
                    <tbody>
                `;

                activeRows.forEach(function(row) {
                    html += `
                        <tr>
                            <td>${row.row_number_upload ?? '-'}</td>
                            <td><span class="badge ${badgeClass(row.status)}">${row.status ?? '-'}</span></td>
                            <td>${row.file_name ?? '-'}</td>
                            <td>${row.message ?? '-'}</td>
                            <td>${row.updated_at ?? '-'}</td>
                        </tr>
                    `;
                });

                html += '</tbody></table></div>';
                $('#rt-active-rows').html(html);
            }
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
            order: [[1, 'desc']],
            ajax: {
                url: '{{ url("digital-storage-handover/journal/zip-upload/history/datatable/". $id ) }}',
                dataType: 'JSON',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (d) {
                    $('#form-filter').serializeArray().forEach(function(item) {
                        d[item.name] = item.value;
                    });

                    return d;
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
                { orderable: true, className: 'align-middle text-center fw-semibold' },
                { orderable: false, className: 'align-middle text-center' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle text-wrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle text-wrap' },
            ],
            initComplete: function (settings, json) {
                var table = this.api();
                const searchInput = $('div.dataTables_filter input');

                searchInput.off().unbind();

                searchInput.on('keyup', debounce(function () {
                    table.search(this.value).draw();
                }, 500));
            },
            drawCallback: function(settings) {
                var api = this.api();
                //updateRecordCount(api.page.info().recordsDisplay);
            }
        }).on('draw.dt', function() {
            onLoading('close', '#datatable-serverside_wrapper');
        });
    }

</script>