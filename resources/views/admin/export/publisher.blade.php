<table>
    <thead>
        <tr>
            <th rowspan="2" colspan="13"
                style="font-size:18px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                LAPORAN PENERBIT</th>
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
                Nama</th>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Email</th>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Telepon</th>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Periode</th>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Alamat</th>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Jenis</th>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Buku</th>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Partitur</th>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Peta</th>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Serial</th>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Audio</th>
            <th
                style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Film</th>
        </tr>
    </thead>
    <tbody>
        @if($data->count() > 0)
        @foreach($data as $key => $d)
        <tr>
            <td style="font-size:9px; border:1px solid black; text-align:center; vertical-align:center;">
                {{ $key + 1 }}
            </td>
            <td style="font-size:9px; border:1px solid black; text-align:center; vertical-align:center;">
                {{ $d->name }}
            </td>
            <td style="font-size:9px; border:1px solid black; text-align:center; vertical-align:center;">
                {{ $d->user ? $d->user->email : '-' }}
            </td>
            <td style="font-size:9px; border:1px solid black; text-align:center; vertical-align:center;">
                {{ $d->phone }}
            </td>
            <td style="font-size:9px; border:1px solid black; text-align:center; vertical-align:center;">
                @if($detail['param']) 
                    @if($detail['param'] == 'annual') 
                        {{ date('Y', strtotime($d->created_at)) }}
                    @elseif($detail['param'] == 'monthly') 
                        {{ date('F Y', strtotime($d->created_at)) }}
                    @elseif($detail['param'] == 'daily') 
                        {{ date('d F Y', strtotime($d->created_at)) }}
                    @else
                        {{ 'Semua Periode' }}
                    @endif
                @else
                    {{ 'Semua Periode' }}
                @endif
            </td>
            <td style="font-size:9px; border:1px solid black; text-align:center; vertical-align:center;">
                {{ $d->province_id ? $d->province->name : 'Provinsi kosong' }},
                {{ $d->city_id ? $d->city->name : 'Kota kosong' }},
                {{ $d->district_id ? $d->district->name : 'Kecamatan kosong' }},
                {{ $d->village_id ? $d->village->name : 'kelurahan kosong' }},
                {{ $d->address }}
            </td>
            <td style="font-size:9px; border:1px solid black; text-align:center; vertical-align:center;">
                {{ $d->type() }}
            </td>
            <td style="font-size:9px; border:1px solid black; text-align:center; vertical-align:center;">
                {{ $d->collection->where('type', 1)->where('status',2)->count() }}
            </td>
            <td style="font-size:9px; border:1px solid black; text-align:center; vertical-align:center;">
                {{ $d->collection->where('type', 2)->count() }}
            </td>
            <td style="font-size:9px; border:1px solid black; text-align:center; vertical-align:center;">
                {{ $d->collection->where('type', 3)->count() }}
            </td>
            <td style="font-size:9px; border:1px solid black; text-align:center; vertical-align:center;">
                {{ $d->collection->where('type', 4)->count() }}
            </td>
            <td style="font-size:9px; border:1px solid black; text-align:center; vertical-align:center;">
                {{ $d->collection->where('type', 5)->count() }}
            </td>
            <td style="font-size:9px; border:1px solid black; text-align:center; vertical-align:center;">
                {{ $d->collection->where('type', 6)->count() }}
            </td>
        </tr>
        @endforeach
        @else
        <tr>
            <td colspan="13"
                style="font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Tidak Ada Data</td>
        </tr>
        @endif
    </tbody>
</table>
