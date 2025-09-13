<table>
    <thead>
        <tr>
            <th colspan="14" style="border:1px solid black; text-align:left; vertical-align:center; background:#149848;">
                Tahun : {{ $request->year }}
            </th>
        </tr>
        <tr>
            <th style="height:25px; border:1px solid black; text-align:center; color:white; vertical-align:center; background:#245DA9;">No</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Jenis</th>
            @for($i = 1; $i <= 12; $i++)
                <th style="height:25px; border:1px solid black; text-align:center; color:white; vertical-align:center; background:#245DA9;">
                    {{ \Carbon\Carbon::parse(date('Y') . '-' . sprintf('%02s', $i))->isoFormat('MMMM') }}
                </th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $d)
            <tr>
                <td style="border:1px solid black; text-align:center; vertical-align:center; background:#DCDCDC;">{{ $key + 1 }}</td>
                <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $d['name'] }}</td>
                @foreach($d['data'] as $val)
                    <td style="border:1px solid black; text-align:center; vertical-align:center; background:#DCDCDC;">{{ $val }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
