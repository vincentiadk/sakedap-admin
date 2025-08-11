<table>
    <thead>
        <tr>
            <th rowspan="2" colspan="8" style="font-size:18px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">DATA ISRC</th>
        </tr>
    </thead>
</table><br>

<table>
    <thead>
        <tr>
            <th style="">No</th>
            <th style="">Judul</th>
            <th style="">Pelaksana Serah</th>
            <th style="">Komposer</th>
            <th style="">ISRC</th>
            <th style="">Tahun Publikasi</th>
            <th style="">Tipe</th>
            <th style="">Tanggal Validasi</th>
        </tr>
    </thead>
    <tbody>
        @if(count($data) > 0)
            @foreach($data as $key => $d)
                <tr>
                    <td style="">
                        {{ $key + 1 }}
                    </td>
                    <td style="">
                        {{ $d->title }}
                    </td>
                    <td style="">
                        {{ $d->producer_name }}
                    </td>
                    <td style="">
                        {{ $d->composer_name }}
                    </td>
                    <td style="">
                        @if(trim($d->isrc_number) != "") {{ $d->isrc_number }} @else - @endif
                    </td>
                    <td style="">
                        {{ $d->year }}
                    </td>
                    <td style="">
                        {{ $d->asset_type }}
                    </td>
                    <td style="">
                        {{ date('Y-m-d', strtotime($d->validation_date)) }}
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="8" style="font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">Tidak Ada Data</td>
            </tr>
        @endif
    </tbody>
</table>
