<table>
    <thead>
        <tr>
            <th rowspan="2" colspan="28" style="text-align:center; font-size:30px"> LAPORAN KOLEKSI</th>
        </tr>
    </thead>
</table><br>

<table>
    <thead>
        <tr>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                No</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Penerbit</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Periode</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Metode</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Provinsi</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Kota</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Judul</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Jenis</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Album</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Seri</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Edisi</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Serial</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                DDC</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Volume</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Kode</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Deposit</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Tahun Terbit</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Copyright</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Preview</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Kunci</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Manual</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Akses</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Kontributor</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Subjek</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Size File</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Jenis File</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Tanggal Diserahkan</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Tanggal Terima</th>
            <th style="height:25px; font-size:10px; font-weight:bold; border:1px solid black; text-align:center; vertical-align:center;">
                Status</th>
        </tr>
    </thead>
    <tbody>
        @if($data->count() > 0)
            @foreach($data as $key => $d)
                <tr>
                    <td >
                        {{ $key + 1 }}
                    </td>
                    <td >
                        @if($d->publisher){{ $d->publisher->name }}@endif
                    </td>
                    <td >
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
                    <td >
                        @if($d->collectionMedia->where('type', 2)->count() > 0)
                            {{ $d->collectionMedia()->where('type', 2)->first()->method() }}
                        @else
                            Invalid
                        @endif
                    </td>
                    <td >
                        {{ $d->city_id ? $d->city->province->name : '-' }}
                    </td>
                    <td >
                        {{ $d->city_id ? $d->city->name : '-' }}
                    </td>
                    <td >
                        {!! $d->title !!}
                    </td>
                    <td >
                        {{ $d->type() }}
                    </td>
                    <td >
                        {{ $d->album }}
                    </td>
                    <td >
                        {{ $d->series }}
                    </td>
                    <td >
                        {{ $d->edition }}
                    </td>
                    <td >
                        {{ $d->serial }}
                    </td>
                    <td >
                        {{ $d->ddc }}
                    </td>
                    <td >
                        {{ $d->volume }}
                    </td>
                    <td >
                        {{ $d->code }}
                    </td>
                    <td >
                        {{ $d->deposit }}
                    </td>
                    <td >
                        {{ $d->publication_year }}
                    </td>
                    <td >
                        {{ $d->copyright }}
                    </td>
                    <td >
                        {{  $d->preview  }}
                    </td>
                    <td >
                        {{ $d->lock ? 'Ya' : 'Tidak' }}
                    </td>
                    <td >
                        {{ $d->manual ? 'Ya' : 'Tidak' }}
                    </td>
                    <td >
                        @if($d->access != "")
                        {{ $d->access() }}
                        @endif
                    </td>
                    <td >
                        @if($d->collectionContributor->count() > 0)
                            @foreach($d->collectionContributor as $cc)
                                {{ $cc->author->fullname }} ({{ $cc->contributor->name }});
                            @endforeach
                        @else
                            Tidak Ada Kontributor
                        @endif
                    </td>
                    <td >
                        @if($d->collectionSubject->count() > 0)
                            @foreach($d->collectionSubject as $cs)
                                {{ $cs->subject->name }};
                            @endforeach
                        @else
                            Tidak Ada Subjek
                        @endif
                    </td>
                    <td >
                        @php
                            if($d->type == 1 || $d->type == 2 || $d->type == 3 || $d->type == 4) {
                                $media = App\Models\CollectionMedia::where('collection_id', $d->id)->where('type', 2)->first();
                            } else if($d->type == 5) {
                                $media = App\Models\CollectionMedia::where('collection_id', $d->id)->where('type', 2)->first();
                            } else {
                                $media = App\Models\CollectionMedia::where('collection_id', $d->id)->where('type', 2)->first();
                            }
                        @endphp
                        @if($media)
                            {{ $media->size }} KB
                        @else
                            0 KB
                        @endif
                    </td>
                    <td>
                        @if($media)
                        {{ strtoupper(str_replace('.', '', $media->extension)) }}
                        @endif
                    </td>
                    <td >
                        {{ date('d-m-Y, H:i', strtotime($d->created_at)) }}
                    </td>
                    <td >
                        {{ $d->received_at ? date('d-m-Y, H:i', strtotime($d->received_at)) : '-' }}
                    </td>
                    <td >
                        {{ $d->status() }}
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="28">
                    Tidak Ada Data</td>
            </tr>
        @endif
    </tbody>
</table>
