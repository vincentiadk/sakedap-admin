<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Laporan - <span class="fw-normal">Unduhan</span>
            </h4>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-hover w-100 display" id="datatable-client">
                <thead class="text-bg-light">
                    <tr>
                        <th class="text-center" nowrap>No</th>
                        <th class="text-center" nowrap>Aksi</th>
                        <th nowrap>Jenis</th>
                        <th nowrap>Tanggal</th>
                        <th nowrap>Jam</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($result as $key => $r)
                        <tr>
                            <td class="text-center">{{ $key + 1 }}</td>
                            <td class="text-center">
                                @if($r['status'] == 'completed')
                                    <a href="{{ url('report/download?downloaded=true&param=' . $r['job_id']) }}" class="btn btn-success btn-sm">
                                        <i class="ph-download me-1"></i>
                                        Unduh
                                    </a>
                                @elseif($r['status'] == 'failed')
                                    <button type="button" class="btn btn-danger btn-sm" disabled>
                                        <i class="ph-x me-1"></i>
                                        Gagal
                                    </button>
                                @else
                                    <button type="button" class="btn btn-primary btn-sm" disabled>
                                        <i class="ph-hourglass-medium me-1"></i>
                                        Proses ...
                                    </button>
                                @endif
                            </td>
                            <td>{{ $r['type'] }}</td>
                            <td>{{ $r['date'] }}</td>
                            <td>{{ $r['time'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('#datatable-client').DataTable({
            scrollX: true,
            deferRender: true
        });
    });
</script>
