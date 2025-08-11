<table>
    <thead>
        <tr>
            <th rowspan="2" colspan="10" style="text-align:center; font-size:30px">LAPORAN KINERJA USER</th>
        </tr>
    </thead>
</table><br>

<table>
    <thead>
        <tr>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Tanggal</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                User</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Koleksi</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Deposit</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                ISBN/ISMN/ISRC/ISSN/ISAN</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Bulan Terbit</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Deskripsi</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Data</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Sebelum</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Sesudah</th>
        </tr>
    </thead>
    <tbody>
        @if($data->count() > 0)
            @foreach($data as $d)
                @php
                    $properties = json_decode($d->properties, true);
                    if(array_key_exists('data_lama', $properties)) {
                        $field_data = $properties['data_lama'];
                        $rowspan    = count($properties['data_lama']) + 1;
                    } else {
                        if(is_array($properties)) {
                            $field_data = $properties;
                            $rowspan    = count($properties) + 1;
                        } else {
                            $field_data = [];
                            $rowspan    = 0;
                        }
                    }
                @endphp
                <tr>
                    <td style="border:1px solid black; text-align:center; vertical-align:center;" rowspan="{{ $rowspan }}">{{ $d->created_at->format('Y-m-d H:i:s') }}</td>
                    <td style="border:1px solid black; text-align:center; vertical-align:center;" rowspan="{{ $rowspan }}">
                        {{ $d->user ? $d->user->username : '-' }}
                    </td>
                    <td style="border:1px solid black; text-align:center; vertical-align:center;" rowspan="{{ $rowspan }}">{{ $d->collection ? $d->collection->title : '-' }}</td>
                    <td style="border:1px solid black; text-align:center; vertical-align:center;" rowspan="{{ $rowspan }}">{{ $d->collection ? $d->collection->deposit : '-' }}</td>
                    <td style="border:1px solid black; text-align:center; vertical-align:center;" rowspan="{{ $rowspan }}">{{ $d->collection ? $d->collection->code : '-' }}</td>
                    <td style="border:1px solid black; text-align:center; vertical-align:center;" rowspan="{{ $rowspan }}">{{ $d->collection ? $d->collection->publicationMonth() : '-' }}</td>
                    <td style="border:1px solid black; text-align:center; vertical-align:center;" rowspan="{{ $rowspan }}">{{ $d->description }}</td>
                </tr>
                @if($rowspan > 0)
                    @if(array_key_exists('data_lama', $properties))
                        @foreach($field_data as $kfd => $fd)
                            @php
                                $value_old = '';
                                $value_new = '';

                                if(is_array($properties['data_lama'][$kfd])) {
                                    foreach($properties['data_lama'][$kfd] as $key_detail => $val_detail) {
                                        if(is_array($val_detail)) {
                                            $value_old = json_encode($properties['data_lama'][$kfd], JSON_UNESCAPED_SLASHES);
                                        } else {
                                            $separator  = $key_detail + 1 == count($properties['data_lama'][$kfd]) ? '' : ', ';
                                            $value_old .= $val_detail . $separator;
                                        }
                                    }
                                } else {
                                    $value_old = $properties['data_lama'][$kfd];
                                }

                                if(is_array($properties['data_baru'][$kfd])) {
                                    foreach($properties['data_baru'][$kfd] as $key_detail => $val_detail) {
                                        if(is_array($val_detail)) {
                                            $value_new = json_encode($properties['data_baru'][$kfd], JSON_UNESCAPED_SLASHES);
                                        } else {
                                            $separator  = $key_detail + 1 == count($properties['data_baru'][$kfd]) ? '' : ', ';
                                            $value_new .= $val_detail . $separator;
                                        }
                                    }
                                } else {
                                    $value_new = $properties['data_baru'][$kfd];
                                }
                            @endphp
                            <tr>
                                <td style="border:1px solid black; text-align:center; vertical-align:center;">{{ ucwords(str_replace('_', ' ', $kfd)) }}</td>
                                <td style="border:1px solid black; text-align:center; vertical-align:center;">{{ $value_old }}</td>
                                <td style="border:1px solid black; text-align:center; vertical-align:center;">{{ $value_new }}</td>
                            </tr>
                        @endforeach
                    @else
                        @foreach($field_data as $kfd => $fd)
                            @php
                                $value = '';

                                if(is_array($properties[$kfd])) {
                                    foreach($properties[$kfd] as $key_detail => $val_detail) {
                                        if(is_array($val_detail)) {
                                            $value = json_encode($properties[$kfd], JSON_UNESCAPED_SLASHES);
                                        } else {
                                            $separator  = $key_detail + 1 == count($properties[$kfd]) ? '' : ', ';
                                            $value     .= $val_detail . $separator;
                                        }
                                    }
                                } else {
                                    $value = $properties[$kfd];
                                }
                            @endphp
                            <tr>
                                <td style="border:1px solid black; text-align:center; vertical-align:center;">{{ ucwords(str_replace('_', ' ', $kfd)) }}</td>
                                <td style="border:1px solid black; text-align:center; vertical-align:center;"></td>
                                <td style="border:1px solid black; text-align:center; vertical-align:center;">{{ $value }}</td>
                            </tr>
                        @endforeach
                    @endif
                @else
                    <tr>
                        <td colspan="3" style="border:1px solid black; text-align:center; vertical-align:center;">-</td>
                    </tr>
                @endif
            @endforeach
        @else
            <tr>
                <td style="border:1px solid black; text-align:center; vertical-align:center;" colspan="10">Tidak Ada Data</td>
            </tr>
        @endif
    </tbody>
</table>
