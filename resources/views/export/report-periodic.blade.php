<table>
    <thead>
        <tr>
            <th colspan="26" style="border:1px solid black; text-align:left; vertical-align:center; background:#149848;">
                Tahun : {{ $request->year }}
            </th>
        </tr>
        <tr>
            <th style="height:25px; border:1px solid black; text-align:center; color:white; vertical-align:center; background:#245DA9;" rowspan="2">No</th>
            <th style="height:25px; border:1px solid black; text-align:center; color:white; vertical-align:center; background:#245DA9;" rowspan="2">Jenis</th>
            @for($i = 1; $i <= 12; $i++)
                <th style="height:25px; border:1px solid black; text-align:center; color:white; vertical-align:center; background:#245DA9;" colspan="2">
                    {{ Carbon::parse(date('Y') . '-' . sprintf('%02s', $i))->isoFormat('MMMM') }}
                </th>
            @endfor
        </tr>
        <tr>
            @for($i = 1; $i <= 12; $i++)
                <th style="height:25px; border:1px solid black; text-align:center; color:white; vertical-align:center; background:#245DA9;">
                    Katalog
                </th>
                <th style="height:25px; border:1px solid black; text-align:center; color:white; vertical-align:center; background:#245DA9;">
                    Collection
                </th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $d)
            <tr>
                <td style="border:1px solid black; text-align:center; vertical-align:center; background:#DCDCDC;">{{ $key + 1 }}</td>
                @foreach($d as $val)
                    <td style="border:1px solid black; text-align:center; vertical-align:center; background:#DCDCDC;">{{ $val }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
