<style>
    .th-kckra {
        height: 25px;
        font-size: 10px;
        font-weight: bold;
        border: 1px solid black;
        text-align: center;
        vertical-align: center;
    }
</style>
<table>
    <thead>
        <tr>
            <th rowspan="2" colspan="22" style="text-align:center; font-size:30px"> LAPORAN KOLEKSI KCRA </th>
        </tr>
    </thead>
</table><br>

<table>
    <thead>
        <tr>
            <th class="th-kckra">No</th>
            <th class="th-kckra">Penerbit</th>
            <th class="th-kckra">Periode</th>
            <th class="th-kckra">Provinsi</th>
            <th class="th-kckra">Kota</th>
            <th class="th-kckra">Judul</th>
            <th class="th-kckra">Jenis</th>
            <th class="th-kckra">Album</th>
            <th class="th-kckra">Seri</th>
            <th class="th-kckra">Edisi</th>
            <th class="th-kckra">Serial</th>
            <th class="th-kckra">Kode</th>
            <th class="th-kckra">Deposit</th>
            <th class="th-kckra">Mark</th>
            <th class="th-kckra">Tahun Terbit</th>
            <th class="th-kckra">Lokasi</th>
            <th class="th-kckra">Availability</th>
            <th class="th-kckra">Kondisi</th>
            <th class="th-kckra">Kunci</th>
            <th class="th-kckra">Manual</th>
            <th class="th-kckra">Tanggal Diserahkan</th>
            <th class="th-kckra">Tanggal Terima</th>
        </tr>
    </thead>
    <tbody>
        {{-- @php
            dd($data->count());
        @endphp --}}
        @if ($data->count() > 0)
            @foreach ($data as $key => $val)
                @php
                    if ($detail['param']) {
                        if ($detail['param'] == 'annual') {
                            $periode = date('Y', strtotime($val->collection->created_at));
                        } elseif ($detail['param'] == 'monthly') {
                            $periode = date('F Y', strtotime($val->collection->created_at));
                        } elseif ($detail['param'] == 'daily') {
                            $periode = date('d F Y', strtotime($val->collection->created_at));
                        }
                    } else {
                        $periode = 'Semua Periode';
                    }
                    
                    if ($val->collection->status == 2) {
                        $receivedBy = $val->collection->receivedBy ? $val->collection->receivedBy->username : '';
                        $updatedBy = $val->collection->updatedBy ? $val->collection->updatedBy->username : '';
                    }
                    
                    if ($val->collection->publisher) {
                        $publisher_name = $val->collection->publisher ? $val->collection->publisher->name : '';
                        $province_name = $val->collection->publisher->province ? $val->collection->publisher->province->name : '';
                        $city_name = $val->collection->publisher->city ? $val->collection->publisher->city->name : '';
                    } else {
                        $province_name = '';
                        $city_name = '';
                        $publisher_name = '';
                    }
                    
                    if (session('library_id') == '1') {
                        $mark = $val->collection->mark_national;
                    } else {
                        $mark = $val->collection->mark_province;
                    }
                @endphp
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $publisher_name }}</td>
                    <td>{{ $periode }}</td>
                    <td>{{ $province_name }}</td>
                    <td>{{ $city_name }}</td>
                    <td>{{ $val->collection->title }}</td>
                    <td>{{ $val->collection->depositHead->shape }}</td>
                    <td>{{ $val->collection->album }}</td>
                    <td>{{ $val->collection->series }}</td>
                    <td>{{ $val->collection->edition }}</td>
                    <td>{{ $val->collection->serial }}</td>
                    <td>{{ $val->collection->code }}</td>
                    <td>{{ $val->collection->deposit }}</td>
                    <td>{{ $mark }}</td>
                    <td>{{ $val->collection->publication_year }}</td>
                    <td>{{ $val->lib_location->name }}</td>
                    <td>{{ isset($availability[$val->availability]) ? $availability[$val->availability] : '-' }}</td>
                    <td>{{ isset($arrConditions[$val->condition]) ? $arrConditions[$val->condition] : '-' }}</td>
                    <td>{{ $val->collection->lock ? 'Ya' : 'Tidak' }}</td>
                    <td>{{ $val->collection->manual ? 'Ya' : 'Tidak' }}</td>
                    <td>{{ $val->created_at->format('d-m-Y, H:i') }}</td>
                    <td>{{ $val->received_at ? date('d-m-Y, H:i', strtotime($val->received_at)) : '-' }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="22">
                    Tidak Ada Data</td>
            </tr>
        @endif
    </tbody>
</table>
