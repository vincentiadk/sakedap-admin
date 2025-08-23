@include('layouts.head', $data)
@include('layouts.navbar', $data)
@include($data['content'], $data)
@include('layouts.footer', $data)
