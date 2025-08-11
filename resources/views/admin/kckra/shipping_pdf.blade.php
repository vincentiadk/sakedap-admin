<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>

    <style type="text/css">
        * {
            font-family: Verdana, Arial, sans-serif;
        }

        table {
            font-size: x-small;
        }

        tfoot tr td {
            font-weight: bold;
            font-size: x-small;
        }

        .gray {
            background-color: lightgray
        }
    </style>
</head>

<body>

    <table width="100%">
        <tr>
            <td>
                <h3>{{ $title }}</h3>
                <pre>
Nama Koordinator : {{ $coordinator }}
NIP Koordinator : {{ $nip }}
Periode Pengiriman : {{ $period }}
Pengirim : {{ $name_sender }}
                </pre>
            </td>
        </tr>


    </table>

    <table width="100%">
        <thead style="background-color: lightgray;">
            <tr>
                <th>#</th>
                <th>TRK</th>
                <th>Kode Barcode</th>
                <th>Judul</th>
                <th>Tahun Terbit</th>
                <th>Eksemplar</th>
                <th>Tgl Kirim</th>
            </tr>
        </thead>
        <tbody>
            {{-- @for ($i = 0; $i < 100; $i++) --}}
            @foreach ($data as $idx => $item)
                <tr>
                    <th scope="row">{{ $idx + 1 }}</th>
                    <td>{{ $library_id == '1' ? $item->mark_national : $item->mark_province }}</td>
                    <td>{{ $item->barcode }}</td>
                    <td>{{ !empty($item->edition_title) ? $item->parent_title . ' ' . $item->edition_title : $item->title }}
                    </td>
                    <td>{{ !empty($item->edition_title) ? date('Y', strtotime($item->start_publication_date)) . ' - ' . date('Y', strtotime($item->end_publication_date)) : $item->publication_year }}
                    </td>
                    <td>1</td>
                    <td>{{ $item->delivery_internal_date }}</td>
                </tr>
            @endforeach
            {{-- @endfor --}}
        </tbody>
    </table>

</body>

</html>
