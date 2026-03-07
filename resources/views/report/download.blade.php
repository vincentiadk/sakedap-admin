<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Laporan - <span class="fw-normal">Unduhan</span>
            </h4>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="card border-0 shadow-sm">
        <div class="card-header border-bottom">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ph-download-simple me-1 text-primary"></i>
                    <h6 class="mb-0 fw-semibold">Riwayat Unduhan</h6>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary">
                        <i class="ph-files me-1"></i>
                        {{ count($result) }} File
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered display nowrap w-100" id="datatable-client">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center text-nowrap" style="width: 60px">
                                <i class="ph-hash"></i>
                            </th>
                            <th class="text-center text-nowrap" style="width: 120px">
                                <i class="ph-gear"></i>
                                Aksi
                            </th>
                            <th class="text-nowrap" style="min-width: 200px">
                                <i class="ph-file-text me-1"></i>
                                Jenis Laporan
                            </th>
                            <th class="text-nowrap" style="min-width: 130px">
                                <i class="ph-calendar me-1"></i>
                                Tanggal
                            </th>
                            <th class="text-nowrap" style="min-width: 100px">
                                <i class="ph-clock me-1"></i>
                                Jam
                            </th>
                            <th class="text-center text-nowrap" style="min-width: 130px">
                                <i class="ph-flag me-1"></i>
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($result as $key => $r)
                            <tr>
                                <td class="text-center fw-semibold">{{ $key + 1 }}</td>
                                <td class="text-center">
                                    @if($r['status'] == 'completed')
                                        <a href="{{ url('report/download?downloaded=true&param=' . $r['job_id']) }}" class="btn btn-success btn-sm">
                                            <i class="ph-download me-1"></i>
                                            Unduh
                                        </a>
                                    @elseif($r['status'] == 'failed')
                                        <button type="button" class="btn btn-danger btn-sm" readonly>
                                            <i class="ph-x me-1"></i>
                                            Gagal
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-primary btn-sm" readonly>
                                            <i class="ph-hourglass-medium me-1"></i>
                                            Proses
                                        </button>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="ph-file-arrow-down me-2 text-primary"></i>
                                        <span>{{ $r['type'] }}</span>
                                    </div>
                                </td>
                                <td>
                                    <i class="ph-calendar-blank me-1 text-muted"></i>
                                    {{ $r['date'] }}
                                </td>
                                <td>
                                    <i class="ph-clock-afternoon me-1 text-muted"></i>
                                    {{ $r['time'] }}
                                </td>
                                <td class="text-center">
                                    @if($r['status'] == 'completed')
                                        <span class="badge bg-success">
                                            <i class="ph-check-circle me-1"></i>
                                            Selesai
                                        </span>
                                    @elseif($r['status'] == 'failed')
                                        <span class="badge bg-danger">
                                            <i class="ph-warning-circle me-1"></i>
                                            Gagal
                                        </span>
                                    @else
                                        <span class="badge bg-primary">
                                            <i class="ph-spinner spinner me-1"></i>
                                            Proses
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="ph-folder-open ph-2x mb-2 d-block"></i>
                                    <span>Belum ada riwayat unduhan</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(count($result) > 0)
        <div class="card-footer border-top bg-light">
            <div class="d-flex align-items-center text-muted">
                <i class="ph-info me-2"></i>
                <small>File yang telah selesai diproses dapat diunduh dengan menekan tombol unduh</small>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    $(function() {
        $('#datatable-client').DataTable({
            scrollX: true,
            deferRender: true,
            order: [[0, 'desc']],
        });
    });
</script>
