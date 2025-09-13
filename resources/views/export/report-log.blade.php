<table>
    <thead>
        @if($request->date)
            <tr>
                <th colspan="7" style="border:1px solid black; text-align:left; vertical-align:center; background:#149848;">
                    Tanggal : {{ $request->date }}
                </th>
            </tr>
        @endif
        @if($request->action)
            <tr>
                <th colspan="7" style="border:1px solid black; text-align:left; vertical-align:center; background:#149848;">
                    Aksi : {{ ucwords($request->action) }}
                </th>
            </tr>
        @endif
        @if($request->action_by)
            <tr>
                <th colspan="7" style="border:1px solid black; text-align:left; vertical-align:center; background:#149848;">
                    User : {{ ucwords($request->action_by) }}
                </th>
            </tr>
        @endif
        <tr>
            <th style="height:25px; border:1px solid black; text-align:center; color:white; vertical-align:center; background:#245DA9;">No</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Aksi</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Tabel</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">User</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Tanggal</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">IP</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach(collect($data)->chunk(500) as $group)
            @foreach($group as $val)
                <tr>
                    <td style="border:1px solid black; text-align:center; vertical-align:center; background:#DCDCDC;">{{ $no }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ ucwords($val->ACTION) }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->TABLENAME }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->ACTIONBY }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->ACTIONDATE }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->ACTIONTERMINAL }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ strip_tags($val->NOTE) }}</td>
                </tr>

                @php $no++; @endphp
            @endforeach
        @endforeach
    </tbody>
</table>
