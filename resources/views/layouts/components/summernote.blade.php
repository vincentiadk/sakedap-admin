@push('summernote-css')
    <link href="{{ asset('plugins/summernote/summernote-lite.min.css') }}?v={{ uniqid() }}" rel="stylesheet">
@endpush
@push('summernote-js')
    <script src="{{ asset('plugins/summernote/summernote-lite.min.js') }}?v={{ uniqid() }}"></script>
    <script src="{{ asset('plugins/summernote/lang/summernote-id-ID.min.js') }}?v={{ uniqid() }}"></script>
@endpush
