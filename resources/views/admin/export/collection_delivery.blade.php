<table>
    <thead>
        <tr>
            <th rowspan="2" colspan="16"
                style="font-size:18px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                LAPORAN PENGIRIMAN KCKR ANALOG</th>
        </tr>
    </thead>
</table><br>

<table>
    <thead>
        <tr>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                No</th>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Jenis</th>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                ISBN/ISSN</th>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Judul</th>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Pengarang</th>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Bulan / Tahun Terbit</th>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Deskripsi Fisik</th>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Ringkasan</th>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Jumlah Perpusnas</th>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Jumlah Provinsi</th>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Tanggal Kirim Perpusnas</th>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Tanggal Terima Perpusnas</th>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Tanggal Kirim Provinsi</th>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Tanggal Terima Provinsi</th>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                TRK Perpusnas</th>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                TRK Provinsi</th>
        </tr>
    </thead>
    <tbody>
        @if($data->count() > 0)
        @foreach($data as $key => $val)
        <tr>
            <td style="font-size:9px; border:1px solid black; text-align:center; vertical-align:center;">
                {{ $key + 1 }}
            </td>
            <td >
                {{ $val->collection->depositHead->shape }}
            </td>
            <td >
                {{ $val->collection->code }}
            </td>
            <td >
                {{ $val->collection->title }}
            </td>
            <td >
                @foreach($val->collection->collectionContributor as $c)
                    {{$c->author->fullname}}</br>
                @endforeach
            </td>
            <td >
                {{ $val->collection->publicationMonth() . '/' . $val->collection->publication_year }}
            </td>
            <td >
                {{ $val->collection->physicalDescription()->total_page ?? '-' }}
                Hal, 
                {{ $val->collection->physicalDescription()->dimension ?? '-' }}
                Cm
            </td>
            <td >
                {{ $val->collection->description }}
            </td>
            <td >
                {{ $val->perpusnas_count }}
            </td>
            <td >
                {{ $val->province_count }}
            </td>
            <td >
                {{ $val->perpusnas_delivery_date }}
            </td>
            <td >
                {{ $val->perpusnas_accepted_date }}
            </td>
            <td >
                {{ $val->province_delivery_date }}
            </td>
            <td >
                {{ $val->province_accepted_date }}
            </td>
            <td >
                {{ $val->collection->mark_national }}
            </td>
            <td >
                {{ $val->collection->mark_province }}
            </td>
           
        </tr>
        @endforeach
        @else
        <tr>
            <td colspan="16"
                style="font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Tidak Ada Data</td>
        </tr>
        @endif
    </tbody>
</table>
