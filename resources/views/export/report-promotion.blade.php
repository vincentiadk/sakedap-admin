<table>
    <thead>
        @if($request->date)
            <tr>
                <th colspan="16" style="border:1px solid black; text-align:left; vertical-align:center; background:#149848;">
                    Tanggal : {{ $request->date }}
                </th>
            </tr>
        @endif
        @if($request->promotion_id)
            @php
                $id = $request->promotion_id;
                $getRow = QueryAPI::get("select * from e_promo where id = $id", true);
            @endphp
            @if($getRow)
                <tr>
                    <th colspan="16" style="border:1px solid black; text-align:left; vertical-align:center; background:#149848;">
                        Promosi : {{ $getRow->NAME }}
                    </th>
                </tr>
            @endif
        @endif
        @if($request->executor_id)
            @php
                $id = $request->executor_id;
                $getRow = QueryAPI::get("select * from penerbit where id = $id", true);
            @endphp
            @if($getRow)
                <tr>
                    <th colspan="16" style="border:1px solid black; text-align:left; vertical-align:center; background:#149848;">
                        Pelaksana Serah : {{ $getRow->NAME }}
                    </th>
                </tr>
            @endif
        @endif
        @if($request->delivery_service_id)
            @php
                $id = $request->delivery_service_id;
                $getRow = QueryAPI::get("select * from jasa_pengiriman where id = $id", true);
            @endphp
            @if($getRow)
                <tr>
                    <th colspan="16" style="border:1px solid black; text-align:left; vertical-align:center; background:#149848;">
                        Jasa Kirim : {{ $getRow->NAME }}
                    </th>
                </tr>
            @endif
        @endif
        <tr>
            <th style="height:25px; border:1px solid black; text-align:center; color:white; vertical-align:center; background:#245DA9;">No</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Judul</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Kode</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Saldo</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Diskon</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Min Jumlah Paket</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Potongan</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Tgl Pengiriman</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Nomor Pengiriman</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Pengirim</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Pelaksana Serah</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Jasa Kirim</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Resi</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Biaya Kirim</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Berat</th>
            <th style="height:25px; border:1px solid black; text-align:left; color:white; vertical-align:center; background:#245DA9;">Jumlah Paket</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @foreach(collect($data)->chunk(500) as $group)
            @foreach($group as $val)
                <tr>
                    <td style="border:1px solid black; text-align:center; vertical-align:center; background:#DCDCDC;">{{ $no }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->JUDUL_PROMO }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->KODE_PROMO_PROMO }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ 'Rp ' . number_format($val->SALDO_PROMO) }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->DISKON_PROMO . ' %' }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->JUMLAH_PAKET_PROMO }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ 'Rp ' . number_format($val->JUMLAH_POTONGAN) }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ Carbon::parse($val->LETTER_DATE_LETTER)->format('dddd, D MMMM Y') }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->LETTER_NUMBER_LETTER }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->SENDER_LETTER }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->NAME_PENERBIT }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->NAME_JASA_PENGIRIMAN }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->RECEIPT_NO_LETTER }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ 'Rp ' . number_format($val->BIAYA_KIRIM_LETTER) }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->BERAT_LETTER . ' gram' }}</td>
                    <td style="border:1px solid black; text-align:left; vertical-align:center; background:#DCDCDC;">{{ $val->JUMLAH_PAKET_LETTER }}</td>
                </tr>

                @php $no++; @endphp
            @endforeach
        @endforeach
    </tbody>
</table>
