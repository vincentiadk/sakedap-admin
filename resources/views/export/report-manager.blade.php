<table>
    <thead>
        @if($request->type_id)
            @php
                $id = $request->type_id;
                $getRow = QueryAPI::get("select * from penerbit_jenis where id = $id", true);
            @endphp
            @if($getRow)
                <tr>
                    <th colspan="23" style="border:1px solid black; text-align:left; vertical-align:center; background:#149848;">
                        Jenis : {{ $getRow->NAME }}
                    </th>
                </tr>
            @endif
        @endif
        @if($request->category_id)
            @php
                $id = $request->category_id;
                $getRow = QueryAPI::get("select * from penerbit_kategori where id = $id", true);
            @endphp
            @if($getRow)
                <tr>
                    <th colspan="23" style="border:1px solid black; text-align:left; vertical-align:center; background:#149848;">
                        Kategori : {{ $getRow->NAME }}
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
                    <th colspan="23" style="border:1px solid black; text-align:left; vertical-align:center; background:#149848;">
                        Provinsi : {{ $getRow->NAMAPROPINSI }}
                    </th>
                </tr>
            @endif
        @endif
        <tr>
            <th style="height:25px; border:1px solid black; text-align:center; color:white; vertical-align:center; background:#245DA9;">No</th>
            <th style="height:25px; border:1px solid black; text-align:center; color:white; vertical-align:center; background:#245DA9;">KRD</th>
            <th style="height:25px; border:1px solid black; text-align:center; color:white; vertical-align:center; background:#245DA9;">KRA</th>
            <th style="height:25px; border:1px solid black; text-align:center; color:white; vertical-align:center; background:#245DA9;">KC</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Nama</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Alias</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Alamat</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">SIUP</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Admin</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Telepon</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Fax</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Email</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Website</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Kode Pos</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Rata Terbitan</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Lembaga Penaung</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Induk</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Jenis</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Kategori</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Provinsi</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Kota</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Kecamatan</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Kelurahan</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach(collect($data)->chunk(500) as $group)
            @foreach($group as $val)
                <tr>
                    <td style="border:1px solid black; text-align:center; vertical-align:center; background:#DCDCDC;">{{ $no }}</td>
                    <td style="border:1px solid black; text-align:center; vertical-align:center; background:#DCDCDC;">{{ $val->TOTAL_DIGITAL }}</td>
                    <td style="border:1px solid black; text-align:center; vertical-align:center; background:#DCDCDC;">{{ $val->TOTAL_ANALOG }}</td>
                    <td style="border:1px solid black; text-align:center; vertical-align:center; background:#DCDCDC;">{{ $val->TOTAL_PRINTED }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->NAME }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->ALIAS }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->ALAMAT }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->NOSIUP }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->KONTAK1 }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->TELP1 }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->FAX1 }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->EMAIL1 }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->WEBSITE }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->KODEPOS }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->RATA_TERBITAN }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->LEMBAGA_PENAUNG }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->NAME_PARENT }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->NAME_PENERBIT_JENIS }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->NAME_PENERBIT_KATEGORI }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->NAMAPROPINSI }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->NAMAKAB }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->NAMAKEC }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->NAMAKEL }}</td>
                </tr>

                @php $no++; @endphp
            @endforeach
        @endforeach
    </tbody>
</table>
