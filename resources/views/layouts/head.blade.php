<!DOCTYPE html>
<html lang="id" dir="ltr">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="url" content="{{ url('/') }}">
    <meta name="user-id" content="{{ session('id') }}">
	<title>E-Deposit 5.0 | Admin Panel</title>
    <link rel="shortcut icon" href="{{ asset('assets/icon.png') }}?v={{ uniqid() }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/icon.png') }}?v={{ uniqid() }}">
	<link href="{{ asset('themes/fonts/inter/inter.css') }}?v={{ uniqid() }}" rel="stylesheet">
	<link href="{{ asset('themes/icons/phosphor/styles.min.css') }}?v={{ uniqid() }}" rel="stylesheet">
	<link href="{{ asset('themes/css/ltr/all.min.css') }}?v={{ uniqid() }}" id="stylesheet" rel="stylesheet">
	<link href="{{ asset('plugins/lightbox/dist/css/lightbox.min.css') }}?v={{ uniqid() }}" rel="stylesheet">
	<link href="{{ asset('plugins/waitMe/waitMe.min.css') }}?v={{ uniqid() }}" rel="stylesheet">
	<link href="{{ asset('plugins/custom.css') }}?v={{ uniqid() }}" rel="stylesheet">
	<script src="{{ asset('themes/js/bootstrap/bootstrap.bundle.min.js') }}?v={{ uniqid() }}"></script>
	<script src="{{ asset('themes/js/jquery/jquery.min.js') }}?v={{ uniqid() }}"></script>
	<script src="{{ asset('plugins/moment.js') }}?v={{ uniqid() }}"></script>
	<script src="{{ asset('themes/js/vendor/ui/prism.min.js') }}?v={{ uniqid() }}"></script>
	<script src="{{ asset('themes/js/vendor/tables/datatables/datatables.min.js') }}?v={{ uniqid() }}"></script>
	<script src="{{ asset('themes/js/vendor/notifications/sweet_alert.min.js') }}?v={{ uniqid() }}"></script>
	<script src="{{ asset('themes/js/vendor/forms/selects/select2.min.js') }}?v={{ uniqid() }}"></script>
	<script src="{{ asset('themes/js/vendor/forms/selects/select2-lang/id.js') }}?v={{ uniqid() }}"></script>
    <script src="{{ asset('themes/js/vendor/notifications/noty.min.js') }}?v={{ uniqid() }}"></script>
    <script src="{{ asset('themes/js/vendor/uploaders/fileinput/fileinput.min.js') }}?v={{ uniqid() }}"></script>
    <script src="{{ asset('themes/js/vendor/pickers/daterangepicker.js') }}?v={{ uniqid() }}"></script>
	<script src="{{ asset('themes/js/app.js') }}?v={{ uniqid() }}"></script>
	<script src="{{ asset('plugins/lightbox/dist/js/lightbox.min.js') }}?v={{ uniqid() }}"></script>
	<script src="{{ asset('plugins/waitMe/waitMe.min.js') }}?v={{ uniqid() }}"></script>
	<script src="{{ asset('plugins/ckeditor/ckeditor.js') }}?v={{ uniqid() }}"></script>
	<script src="{{ asset('plugins/ckeditor/lang/id.js') }}?v={{ uniqid() }}"></script>
	<script src="{{ asset('plugins/number/jquery.number.min.js') }}?v={{ uniqid() }}"></script>
	<script src="{{ asset('plugins/custom.js') }}?v={{ uniqid() }}"></script>
</head>
