<table>
    <thead>
        <tr>
            <th rowspan="2" colspan="7" style="text-align:center; font-size:30px">LAPORAN PERIODIK</th>
        </tr>
    </thead>
</table><br>

<table>
    <thead>
        <tr>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Bulan</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Buku</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Partitur</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Peta</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Serial</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Audio</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Film</th>
        </tr>
    </thead>
    <tbody>
        @if($data)
            @foreach($data as $key => $d)
                <tr>
                    <td>{{ $d['data']['month'] }}</td>
                    @foreach($d['data']['item'] as $val)
                        <td>{{ $val }}</td>
                    @endforeach
                </tr>
            @endforeach
        @else
            <tr>
                <td style="border:1px solid black; text-align:center; vertical-align:center;" colspan="7">Tidak Ada Data</td>
            </tr>
        @endif
    </tbody>
</table>
