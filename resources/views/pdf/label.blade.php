<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 10px 10px 10px 10px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table td, .table th {
            border: 1px solid gray;
            padding: 0.5rem;
            font-size: 12px;
            vertical-align: middle;
            text-align: left;
        }

        .table th {
            font-weight: bold;
            font-size: 12px;
            text-align: left;
        }

        .table tbody td:first-child::after {
            content: leader(". ");
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    @foreach ($data as $key => $item)
        <div style="display:grid; place-items:center;">
            <table class="table">
                <tr>
                    <th colspan="2" style="text-align:center; text-transform:uppercase; font-size:10px;">
                        {{ empty($item->NAME_LOCATION_LIBRARY) ? 'Perpustakaan Nasional RI' : $item->NAME_LOCATION_LIBRARY }}
                    </th>
                </tr>
                <tr>
                    <td width="65%">
                        @if($param == 'barcode')
                            <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG("$item->NOMORBARCODE", 'C39', 2, 180) }}">
                            <div style="text-align:center;">{{ $item->NOMORBARCODE }}</div>
                        @else
                            <span style="transform:rotate(270deg); transform-origin:right; position:fixed; top:40; left:120px;">
                                {{ $item->NOMORBARCODE }}
                            </span>
                            <center>
                                <img src="data:image/png;base64,{{ DNS2D::getBarcodePNG("$item->NOMORBARCODE", 'QRCODE', 5.2, 5.2) }}">
                            </center>
                        @endif
                    </td>
                    <td width="35%">{{ $item->MARK_PROVINCE }}</td>
                </tr>
            </table>
        </div>
        @if(count($data) != $key + 1)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>
