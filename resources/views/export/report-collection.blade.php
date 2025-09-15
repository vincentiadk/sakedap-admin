<table>
    <thead>
        @if($request->date)
            <tr>
                <th colspan="15" style="border:1px solid black; text-align:left; vertical-align:center; background:#149848;">
                    Tanggal : {{ $request->date }}
                </th>
            </tr>
        @endif
        @if($request->title)
            <tr>
                <th colspan="15" style="border:1px solid black; text-align:left; vertical-align:center; background:#149848;">
                    Judul : {{ $request->title }}
                </th>
            </tr>
        @endif
        @if($request->executor_id)
            @php
                $id = $request->executor_id;
                $getRow = QueryAPI::get("select * from penerbit where id = $id", true);
            @endphp
            @if($getRow)
                <tr>
                    <th colspan="15" style="border:1px solid black; text-align:left; vertical-align:center; background:#149848;">
                        Pelaksana Serah : {{ $getRow->NAME }}
                    </th>
                </tr>
            @endif
        @endif
        @if($request->worksheet_id)
            @php
                $id = $request->worksheet_id;
                $getRow = QueryAPI::get("select * from worksheets where id = $id", true);
            @endphp
            @if($getRow)
                <tr>
                    <th colspan="15" style="border:1px solid black; text-align:left; vertical-align:center; background:#149848;">
                        Provinsi : {{ $getRow->NAME }}
                    </th>
                </tr>
            @endif
        @endif
        @if($request->province_id)
            @php
                $id = $request->province_id;
                $getRow = QueryAPI::get("select * from propinsi where id = $id", true);
            @endphp
            @if($getRow)
                <tr>
                    <th colspan="15" style="border:1px solid black; text-align:left; vertical-align:center; background:#149848;">
                        Provinsi : {{ $getRow->NAMAPROPINSI }}
                    </th>
                </tr>
            @endif
        @endif
        @if($request->year)
            <tr>
                <th colspan="15" style="border:1px solid black; text-align:left; vertical-align:center; background:#149848;">
                    Tahun : {{ $request->year }}
                </th>
            </tr>
        @endif
        <tr>
            <th style="height:25px; border:1px solid black; text-align:center; color:white; vertical-align:center; background:#245DA9;">No</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Judul</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Album</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Seri</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Edisi</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">DDC</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Volume</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Kode</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Tahun</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Preview</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Tgl Validasi</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Pelaksana Serah</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Provinsi</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Kabupaten</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Jenis</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach(collect($data)->chunk(500) as $group)
            @foreach($group as $val)
                <tr>
                    <td style="border:1px solid black; text-align:center; vertical-align:center; background:#DCDCDC;">{{ $no }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->TITLE }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->ALBUM }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->SERIES }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->EDITION }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->DEWEYNO }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->VOLUME }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->ISBN }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->PUBLISHYEAR }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->PREVIEW }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->VALIDATEDATE }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->NAME_PENERBIT }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->NAMAPROPINSI }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->NAMAKAB }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->NAME_WORKSHEET }}</td>
                </tr>

                @php $no++; @endphp
            @endforeach
        @endforeach
    </tbody>
</table>
