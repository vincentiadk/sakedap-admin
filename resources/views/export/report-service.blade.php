<table>
    <thead>
        <tr>
            <th colspan="5" style="border:1px solid black; text-align:left; vertical-align:center; background:#149848;">
                Tahun : {{ $request->year }}
            </th>
        </tr>
        <tr>
            <th style="height:25px; border:1px solid black; text-align:center; color:white; vertical-align:center; background:#245DA9;">No</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Bulan</th>
            <th style="height:25px; border:1px solid black; text-align:center; color:white; vertical-align:center; background:#245DA9;">Datang Langsung</th>
            <th style="height:25px; border:1px solid black; text-align:center; color:white; vertical-align:center; background:#245DA9;">Unggah Mandiri</th>
            <th style="height:25px; border:1px solid black; text-align:center; color:white; vertical-align:center; background:#245DA9;">Via Pengiriman</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $d)
            <tr>
                <td style="border:1px solid black; text-align:center; vertical-align:center; background:#DCDCDC;">{{ $key + 1 }}</td>
                <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $d['name'] }}</td>
                @foreach($d['data'] as $keys => $val)
                    <td style="border:1px solid black; text-align:center; vertical-align:center; background:#DCDCDC;">{{ $val }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
