<div class="container">
    <h4 class="mb-3">Detail Histori Upload</h4>

    <div class="card mb-4">
        <div class="card-body">
            <div><strong>ID:</strong> {{ $history->ID }}</div>
            <div><strong>Nama ZIP:</strong> {{ $history->ZIP_NAME }}</div>
            <div><strong>Status:</strong> {{ $history->STATUS }}</div>
            <div><strong>Total:</strong> {{ $history->TOTAL_ROWS }}</div>
            <div><strong>Diproses:</strong> {{ $history->PROCESSED_ROWS }}</div>
            <div><strong>Berhasil:</strong> {{ $history->SUCCESS_ROWS }}</div>
            <div><strong>Gagal:</strong> {{ $history->FAILED_ROWS }}</div>
            <div><strong>Catatan:</strong> {{ $history->NOTES }}</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Detail per Row</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th>Row</th>
                        <th>Judul</th>
                        <th>File PDF</th>
                        <th>Status</th>
                        <th>Pesan</th>
                        <th>ID Koleksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($details as $row)
                    <tr>
                        <td>{{ $row->ROW_NUMBER_UPLOAD }}</td>
                        <td>{{ $row->TITLE }}</td>
                        <td>{{ $row->FILE_NAME }}</td>
                        <td>{{ $row->STATUS }}</td>
                        <td>{{ $row->MESSAGE }}</td>
                        <td>{{ $row->E_COLLECTION_ID }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Belum ada detail</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>