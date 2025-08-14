<table>
    <thead>
        <tr>
            <th rowspan="2" colspan="10" style="font-size:18px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">TAGIHAN ISBN</th>
        </tr>
    </thead>
</table><br>

<table>
    <thead>
        <tr>
            <th style="">No</th>
            <th style="">Nama Penerbit</th>
            <th style="">Judul</th>
            <th style="">Jenis</th>
            <th style="">Penyerahan</th>
            <th style="">ISBN</th>
            <th style="">Status ISBN</th>
            <th style="">Status KCKR</th>
            <th style="">Deposit</th>
            <th style="">Kepeng</th>
        </tr>
    </thead>
    <tbody>
        @if(count($data) > 0)
            @foreach($data as $key => $d)
                @php
                    $prefix_element    = $d['prefix_element'];
                    $publisher_element = $d['publisher_element'];
                    $item_element      = $d['item_element'];
                    $check_digit       = $d['check_digit'];
                    $code              = $prefix_element . '-' . $publisher_element . '-' . $item_element . '-' . $check_digit;
                    $collection        = App\Model\Collection::where('code', $code)->where('parent_id', 0)->first();
                @endphp

                <tr>
                    <td style="">
                        {{ $key + 1 }}
                    </td>
                    <td style="">
                        {{ $d['nama_penerbit'] }}
                    </td>
                    <td style="">
                        {{ $d['title'] }}
                    </td>
                    <td style="">
                        {{ ucwords($d['jenis']) }}
                    </td>
                    <td style="">
                        @isset($d['received_at']) {{ date('Y-m-d', strtotime($d['received_at'])) }} @else - @endif
                    </td>
                    <td style="">
                        {{ $code }}
                    </td>
                    <td style="">
                        @isset($d['received_at']) Telah Diserahkan @else Belum Diserahkan @endif
                    </td>
                    <td style="">
                        @if(isset($d['received_date']) && $collection)
                            {{ $collection->status() }}
                        @elseif(isset($d['received_date']) && !$collection)
                            Belum Terunggah
                        @elseif(!isset($d['received_date']) && $collection)
                            Belum Disinkronkan
                        @elseif(!isset($d['received_date']) && !$collection)
                            Menunggu Diunggah
                        @else
                            Invalid
                        @endif
                    </td>
                    <td style="">
                        @if($collection) {{ $collection->deposit }} @else - @endif
                    </td>
                    <td style="">
                        {{ $d['kepeng'] }}
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="10" style="font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">Tidak Ada Data</td>
            </tr>
        @endif
    </tbody>
</table>
