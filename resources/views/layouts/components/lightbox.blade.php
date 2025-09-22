@push('lightbox-css')
    <link href="{{ asset('plugins/lightbox/dist/css/lightbox.min.css') }}?v={{ uniqid() }}" rel="stylesheet">
@endpush
@push('lightbox-js')
    <script src="{{ asset('plugins/lightbox/dist/js/lightbox.min.js') }}?v={{ uniqid() }}"></script>
@endpush
