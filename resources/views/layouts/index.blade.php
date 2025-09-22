@extends('layouts.app')

@section('content')
    @include($data['content'], $data)
@endsection

@isset($data['plugins'])
    @if(is_array($data['plugins']))
        @foreach($data['plugins'] as $p)
            @include('layouts.components.' . $p)
        @endforeach
    @endif
@endif
