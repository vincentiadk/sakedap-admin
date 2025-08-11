<!DOCTYPE html>
<html>

<head>
    <title>Barcode PDF</title>
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
            left: 0;
            transform: translateY(-50%);
        }

        /* Define a container div for centering */
        .container {
            position: relative;
            width: 100%;
            height: 100%;
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
                        <td style="width: 75%">
                            <p>{{ Illuminate\Support\Str::limit($item->title, 25) }}</p>
                            <img style="max-width: 90%;margin-bottom: 5px; margin-top:5px"
                                src="data:image/png;base64,{{ $item->barcode_image }}" alt="Barcode Image">
                        </td>
                        <td style="width: 25%">
                            <p>{{ $item->mark_number }}</p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @endforeach
</body>

</html
