<!DOCTYPE html>
<html lang="id" dir="ltr">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="url" content="{{ url('/') }}">
    <meta name="user-id" content="{{ session('id') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
	<title>SAKEDAP | Admin Panel</title>
    <link rel="shortcut icon" href="{{ asset('assets/icon.png') }}?v={{ uniqid() }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/icon.png') }}?v={{ uniqid() }}">
	<link href="{{ asset('themes/fonts/inter/inter.css') }}?v={{ uniqid() }}" rel="stylesheet">
	<link href="{{ asset('themes/icons/phosphor/styles.min.css') }}?v={{ uniqid() }}" rel="stylesheet">
	<link href="{{ asset('themes/css/ltr/all.min.css') }}?v={{ uniqid() }}" id="stylesheet" rel="stylesheet">
	@stack('lightbox-css')
	<link href="{{ asset('plugins/waitMe/waitMe.min.css') }}?v={{ uniqid() }}" rel="stylesheet">
	@stack('summernote-css')
	@stack('videojs-css')
	<link href="{{ asset('plugins/custom.css') }}?v={{ uniqid() }}" rel="stylesheet">
	<script src="{{ asset('themes/js/bootstrap/bootstrap.bundle.min.js') }}?v={{ uniqid() }}"></script>
	<script src="{{ asset('themes/js/jquery/jquery.min.js') }}?v={{ uniqid() }}"></script>
	<script src="{{ asset('plugins/moment.js') }}?v={{ uniqid() }}"></script>
	<script src="{{ asset('themes/js/vendor/ui/prism.min.js') }}?v={{ uniqid() }}"></script>
	@stack('datatable-js')
	<script src="{{ asset('themes/js/vendor/notifications/sweet_alert.min.js') }}?v={{ uniqid() }}"></script>
    @stack('select2-js')
    <script src="{{ asset('themes/js/vendor/notifications/noty.min.js') }}?v={{ uniqid() }}"></script>
    @stack('fileinput-js')
    @stack('daterangepicker-js')
    @stack('echart-js')
	<script src="{{ asset('themes/js/app.js') }}?v={{ uniqid() }}"></script>
    @stack('lightbox-js')
	<script src="{{ asset('plugins/waitMe/waitMe.min.js') }}?v={{ uniqid() }}"></script>
    @stack('ckeditor-js')
    @stack('dragula-js')
	<script src="{{ asset('plugins/number/jquery.number.min.js') }}?v={{ uniqid() }}"></script>
    @stack('summernote-js')
    @stack('pdfjs-js')
    @stack('howlerjs-js')
    @stack('epubjs-js')
    @stack('videojs-js')
    @stack('lookup-js')
	<script src="{{ asset('plugins/custom.js') }}?v={{ uniqid() }}"></script>
</head>
