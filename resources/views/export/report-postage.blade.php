<table>
    <thead>
        <tr>
            <th colspan="7" style="border:1px solid black; text-align:left; vertical-align:center; background:#149848;">
                Tanggal : {{ $request->date }}
            </th>
        </tr>
        <tr>
            <th style="height:25px; border:1px solid black; text-align:center; color:white; vertical-align:center; background:#245DA9;">No</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Provinsi</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Total Berat Paket</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Ongkir Min</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Ongkir Maks</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Ongkir Rata - Rata</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Jumlah Paket</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $d)
            <tr>
                <td style="border:1px solid black; text-align:center; vertical-align:center; background:#DCDCDC;">{{ $key + 1 }}</td>
                <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $d->PROVINCE }}</td>
                <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ number_format($d->WEIGHT, 2) }} kg</td>
                <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">Rp {{ number_format($d->POSTAGE_MIN, 2) }}</td>
                <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">Rp {{ number_format($d->POSTAGE_MAX, 2) }}</td>
                <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">Rp {{ number_format($d->POSTAGE_AVG, 2) }}</td>
                <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $d->PACKAGE }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
