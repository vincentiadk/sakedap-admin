<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 10px; color: #222; margin: 0; }
        h1 { font-size: 15px; color: #0d47a1; margin: 0 0 2px; }
        .meta { font-size: 9px; color: #555; margin-bottom: 10px; }
        .meta span { margin-right: 14px; }
        h2 { font-size: 11px; color: #1565c0; margin: 14px 0 4px; border-bottom: 1px solid #1565c0; padding-bottom: 2px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        th, td { border: 1px solid #cfcfcf; padding: 3px 5px; text-align: left; }
        th { background: #1565c0; color: #fff; font-size: 9px; }
        td { font-size: 9px; }
        .num { text-align: right; }
        .kv td:first-child { width: 55%; color: #555; }
        .kv td:last-child { font-weight: bold; }
        .empty { color: #999; font-style: italic; padding: 6px; }
        .foot { margin-top: 14px; font-size: 8px; color: #999; border-top: 1px solid #eee; padding-top: 4px; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <div class="meta">
        @foreach($meta as $label => $val)
            <span><strong>{{ $label }}:</strong> {{ $val }}</span>
        @endforeach
    </div>

    @foreach($sections as $sec)
        <h2>{{ $sec['title'] }}</h2>
        @php $rows = $sec['rows'] ?? []; @endphp
        @if(empty($rows))
            <div class="empty">Tidak ada data.</div>
        @elseif(($sec['type'] ?? 'table') === 'kv')
            <table class="kv">
                @foreach($rows as $r)
                    <tr><td>{{ $r[0] }}</td><td>{{ $r[1] }}</td></tr>
                @endforeach
            </table>
        @else
            <table>
                <thead>
                    <tr>
                        @foreach($sec['headers'] as $i => $h)
                            <th class="{{ in_array($i, $sec['num'] ?? []) ? 'num' : '' }}">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $r)
                        <tr>
                            @foreach($r as $i => $cell)
                                <td class="{{ in_array($i, $sec['num'] ?? []) ? 'num' : '' }}">{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach

    <div class="foot">
        Diunduh dari SAKEDAP pada {{ date('d/m/Y H:i') }}. Grafik tidak disertakan dalam versi PDF.
    </div>
</body>
</html>
