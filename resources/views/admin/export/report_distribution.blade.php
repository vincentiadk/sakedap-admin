<table>
    <thead>
        <tr>
            <th rowspan="2" colspan="8" style="font-size:18px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center; background:#28A745;">LAPORAN DISTRIBUSI</th>
        </tr>
    </thead>
</table><br>

<table>
    <thead>
        <tr>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">No</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">Ekspedisi</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">Penerbit</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">Perpustakaan</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">Tgl Kirim</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">Tgl Diterima</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">Status</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">No Surat</th>
        </tr>
    </thead>
    <tbody>
        @if($data->count() > 0)
            @foreach($data as $key => $val)
                <tr>
                    <td style="font-size:9px; border:1px solid black; text-align:center; vertical-align:center;">{{ $key + 1 }}</td>
                    <td>{{ $val->expedition->name ?? '' }}</td>
                    <td>{{ $val->publisher->name ?? '' }}</td>
                    <td>{{ $val->library->name ?? '' }}</td>
                    <td>{{ $val->delivery_date }}</td>
                    <td>{{ $val->accepted_date }}</td>
                    <td>{{ $val->status }}</td>
                    <td>{{ $val->letter_no }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="8" style="font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">Tidak Ada Data</td>
            </tr>
        @endif
    </tbody>
</table>
