<!DOCTYPE html>
<html>

<head>
    <title>QRCODE PDF</title>
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        @page {
            margin: 0;
            padding: 0;
        }

        th,
        td {
            border: 1px solid black;
        }

        table {
            border-collapse: collapse;
            text-align: center;
            vertical-align: middle;
            width: 95%;
            min-width: 95%;
            margin-left: auto;
            margin-right: auto;
            padding: 0px;
            border-spacing: 0px;
        }

        /* Center the table vertically within the container */
        .table-wrapper {
            position: absolute;
            top: 50%;
            width: 100mm;
            transform: translateY(-50%);
        }

        /* Define a container div for centering */
        .container {
            position: relative;
            height: 50mm;
            width: 100mm;
        }
    </style>
</head>

<body>
    @foreach ($data as $item)
        <div class="container">
            <div class="table-wrapper">
                <table>
                    <tr>
                        <td colspan="2">
                            {{ $library->name }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 60%">
                        <div style="font-size:11pt; transform: rotate(270deg); transform-origin:right; position:fixed; top:150px; left:120px">{{ $item->code }}</div>   
                            <img style="max-width: 95%;margin-bottom: 5px; margin-top: 5px;"
                                src="data:image/png;base64,{{ $item->barcode_image }}" alt="Barcode Image">
                           
                        </td>
                        <td style="width: 40%">
                            <p>{{ $item->mark_number }}</p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @endforeach
</body>

</html
