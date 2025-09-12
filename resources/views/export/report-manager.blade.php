<table>
    <thead>
        <tr>
            <th colspan="23" style="font-size:15px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center; background:#149848;">
                LAPORAN PENGELOLA
            </th>
        </tr>
        @if($request->type_id)
            @php
                $id = $request->type_id;
                $getRow = QueryAPI::get("select * from penerbit_jenis where id = $id", true);
            @endphp
            @if($getRow)
                <tr>
                    <th colspan="23" style="font-size:15px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center; background:#149848;">
                        {{ $getRow->NAME }}
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
                    <th colspan="23" style="font-size:15px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center; background:#149848;">
                        {{ $getRow->NAME }}
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
                    <th colspan="23" style="font-size:15px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center; background:#149848;">
                        {{ $getRow->NAMAPROPINSI }}
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
        @if($data)
            @foreach($data as $key => $d)
                <tr>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $key + 1 }}</td>
                    <td style="border:1px solid black; text-align:center; vertical-align:center; background:#DCDCDC;">{{ $d->TOTAL_DIGITAL }}</td>
                    <td style="border:1px solid black; text-align:center; vertical-align:center; background:#DCDCDC;">{{ $d->TOTAL_ANALOG }}</td>
                    <td style="border:1px solid black; text-align:center; vertical-align:center; background:#DCDCDC;">{{ $d->TOTAL_PRINTED }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $d->NAME }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $d->ALIAS }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $d->ALAMAT }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $d->NOSIUP }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $d->KONTAK1 }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $d->TELP1 }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $d->FAX1 }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $d->EMAIL1 }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $d->WEBSITE }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $d->KODEPOS }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $d->RATA_TERBITAN }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $d->LEMBAGA_PENAUNG }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $d->NAME_PARENT }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $d->NAME_PENERBIT_JENIS }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $d->NAME_PENERBIT_KATEGORI }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $d->NAMAPROPINSI }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $d->NAMAKAB }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $d->NAMAKEC }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $d->NAMAKEL }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
