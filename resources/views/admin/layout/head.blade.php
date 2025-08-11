<!DOCTYPE html>
<html class="loading" lang="{{ config('app.locale') }}" data-textdirection="ltr">

<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-F4VZCGM4KE"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-F4VZCGM4KE');
    </script>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>{{ $title }}</title>
    <link rel="apple-touch-icon" href="{{ asset('main/favicon.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('main/favicon.png') }}">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Quicksand:300,400,500,700" rel="stylesheet">
    <link href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/vendors.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/vendors/css/tables/datatable/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/vendors/css/tables/extensions/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/vendors/css/editors/summernote.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/vendors/css/forms/icheck/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/vendors/css/ui/prism.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/bootstrap-extended.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/colors.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/components.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/plugins/extensions/toastr.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/plugins/forms/wizard.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/vendors/css/extensions/toastr.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/plugins/extensions/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/plugins/lightbox/dist/css/lightbox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/plugins/waitMe/waitMe.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/plugins/krajee/css/fileinput.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/plugins/krajee/themes/explorer-fa5/theme.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/core/menu/menu-types/vertical-menu-modern.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/core/colors/palette-gradient.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" type="text/css" href="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.7/css/dataTables.checkboxes.css" />
    <script src="{{ asset('theme_admin/app-assets/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('theme_admin/app-assets/vendors/js/extensions/toastr.min.js') }}"></script>
    <script src="{{ asset('theme_admin/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('theme_admin/app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('theme_admin/app-assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('theme_admin/app-assets/vendors/js/tables/jszip.min.js') }}"></script>
    <script src="{{ asset('theme_admin/app-assets/vendors/js/tables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('theme_admin/app-assets/vendors/js/tables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('theme_admin/app-assets/vendors/js/tables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('theme_admin/app-assets/vendors/js/tables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('theme_admin/app-assets/vendors/js/tables/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('theme_admin/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
    <script src="{{ asset('theme_admin/app-assets/vendors/js/editors/summernote/summernote.js') }}"></script>
    <script src="{{ asset('theme_admin/app-assets/vendors/js/charts/chart.min.js') }}"></script>
    <script src="{{ asset('theme_admin/app-assets/vendors/js/editors/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('theme_admin/app-assets/vendors/js/extensions/jquery.steps.min.js') }}"></script>
    <script src="{{ asset('theme_admin/app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('theme_admin/app-assets/vendors/js/forms/icheck/icheck.min.js') }}"></script>
    <script src="{{ asset('theme_admin/app-assets/vendors/js/ui/prism.min.js') }}"></script>
    <script src="{{ asset('theme_admin/app-assets/vendors/js/extensions/toastr.min.js') }}"></script>
    <script src="{{ asset('theme_admin/plugins/lightbox/dist/js/lightbox.min.js') }}"></script>
    <script src="{{ asset('theme_admin/plugins/krajee/js/fileinput.min.js') }}"></script>
    <script src="{{ asset('theme_admin/plugins/krajee/js/plugins/buffer.min.js') }}"></script>
    <script src="{{ asset('theme_admin/plugins/krajee/js/plugins/filetype.min.js') }}"></script>
    <script src="{{ asset('theme_admin/plugins/krajee/js/plugins/piexif.min.js') }}"></script>
    <script src="{{ asset('theme_admin/plugins/krajee/js/plugins/sortable.min.js') }}"></script>
    <script src="{{ asset('theme_admin/plugins/krajee/themes/fa5/theme.min.js') }}"></script>
    <script src="{{ asset('theme_admin/plugins/krajee/themes/explorer-fa5/theme.min.js') }}"></script>
    <script src="{{ asset('theme_admin/plugins/waitMe/waitMe.min.js') }}"></script>
    <script src="{{ asset('theme_admin/plugins/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('theme_admin/plugins/viewerjs/pdf.js') }}"></script>
    <script src="{{ asset('theme_admin/plugins/viewerjs/pdf.worker.js') }}"></script>
    <script src="{{ asset('theme_admin/plugins/jquery.lazy/jquery.lazy.min.js') }}"></script>
    <script src="{{ asset('theme_admin/plugins/dataTables/input.js') }}"></script>
    <script src="{{ asset('theme_admin/assets/js/jszip.min.js') }}"></script>
    <script src="{{ asset('theme_admin/assets/js/epub.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.7/js/dataTables.checkboxes.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
    <script src="https://example.com/fontawesome/v5.15.4/js/fontawesome.js" data-auto-replace-svg="nest"></script>
    <script src="https://example.com/fontawesome/v5.15.4/js/solid.js"></script>
    <script src="https://example.com/fontawesome/v5.15.4/js/brands.js"></script>

    <style>
        .files input {
            outline: 2px dashed #92b0b3;
            outline-offset: -10px;
            -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
            transition: outline-offset .15s ease-in-out, background-color .15s linear;
            padding: 120px 0px 85px 35%;
            text-align: center !important;
            margin: 0;
            width: 100% !important;
        }

        .files input:focus {
            outline: 2px dashed #92b0b3;
            outline-offset: -10px;
            -webkit-transition: outline-offset .15s ease-in-out, background-color .15s linear;
            transition: outline-offset .15s ease-in-out, background-color .15s linear;
            border: 1px solid #92b0b3;
        }

        .files {
            position: relative;
        }

        .files:after {
            pointer-events: none;
            position: absolute;
            top: 60px;
            left: 0;
            width: 50px;
            right: 0;
            height: 56px;
            content: "";
            background-image: url(https://image.flaticon.com/icons/png/128/109/109612.png);
            display: block;
            margin: 0 auto;
            background-size: 100%;
            background-repeat: no-repeat;
        }

        .color input {
            background-color: #f1f1f1;
        }

        .files:before {
            position: absolute;
            bottom: 10px;
            left: 0;
            pointer-events: none;
            width: 100%;
            right: 0;
            height: 57px;
            content: " or drag it here. ";
            display: block;
            margin: 0 auto;
            color: #2ea591;
            font-weight: 600;
            text-transform: capitalize;
            text-align: center;
        }

        .btn {
            border-radius: 0 !important;
        }

        .form-group label {
            font-weight: bold;
            color: #343a40;
        }

        .nowrap {
            white-space: nowrap;
        }

        .no-nowrap {
            white-space: normal !important;
        }

        .hover-link:hover {
            text-decoration: underline;
        }

        .table {
            width: 100% !important;
        }

        #datatable_default>tbody>tr>td {
            text-align: center;
            vertical-align: middle;
        }

        #datatable_edition>tbody>tr>td {
            text-align: center;
            vertical-align: middle;
        }

        .no-click {
            pointer-events: none !important;
        }
    </style>

    <script>
        $(function() {
            $('#datatable_default').DataTable();

            $('body').tooltip({
                selector: '[data-toggle=tooltip]'
            });

            $('#datatable_edition').DataTable({
                scrollX: true
            });

            $('.summernote').summernote({
                height: 200
            });

            $('.select2').select2({
                placeholder: '-- Pilih --'
            });
        });

        function dragFile(selector, allowed_ext = []) {
            $(selector).fileinput({
                allowedFileExtensions: allowed_ext,
                showUpload: false
            });
        }

        function randStr(length) {
            var result = '';
            var char = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
            var char_length = char.length;

            for (var i = 0; i < length; i++) {
                result += char.charAt(Math.floor(Math.random() * char_length));
            }

            return result;
        }

        function validationContributor(param = '', type = '') {
            $('button[type="submit"]').attr('disabled', false);
            $('#validation_contributor').html('');

            var name = $('input[name="contributor_fullname_field[]"]').map(function() {
                return $(this).val();
            }).get();

            var title = $('input[name="contributor_title_field[]"]').map(function() {
                return $(this).val();
            }).get();

            var empty_name = 0;
            var empty_title = 0;

            $.each(name, function(i, val) {
                if (val == '') {
                    empty_name += 1;
                }
            });

            $.each(title, function(i, val) {
                if (val == '') {
                    empty_title += 1;
                }
            });

            if (empty_name > 0) {
                $('#validation_contributor').append('<li>Mohon mengisi semua nama di kontributor</li>');
            }

            if (empty_title > 0) {
                $('#validation_contributor').append('<li>Mohon mengisi semua gelar di kontributor</li>');
            }

            if (empty_name > 0 || empty_title > 0) {
                $('button[type="submit"]').attr('disabled', true);
            }
        }

        function select2AutoSuggest(selector, endpoint, modal = '') {
            $(selector).select2({
                placeholder: '-- Pilih --',
                dropdownParent: modal,
                minimumInputLength: 2,
                allowClear: true,
                cache: true,
                dropdowntParent: $('#modal_element'),
                ajax: {
                    url: '{{ url('admin/select2_serverside') }}' + '/' + endpoint,
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 250,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.items
                        }
                    }
                }
            });
        }

        function select2AutoSuggestMultiple(selector, endpoint) {
            $(selector).select2({
                placeholder: '-- Pilih --',
                minimumInputLength: 3,
                allowClear: true,
                multiple: true,
                cache: true,
                ajax: {
                    url: '{{ url('admin/select2_serverside') }}' + '/' + endpoint,
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 250,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.items
                        }
                    }
                }
            });
        }

        function select2AutoSuggestTags(selector, endpoint) {
            $(selector).select2({
                placeholder: '-- Pilih --',
                minimumInputLength: 3,
                allowClear: true,
                tags: true,
                cache: true,
                ajax: {
                    url: '{{ url('admin/select2_serverside') }}' + '/' + endpoint,
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 250,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.items
                        }
                    }
                },
                createTag: function(params) {
                    var term = $.trim(params.term);
                    if (term === '') {
                        return null;
                    } else {
                        return {
                            id: term,
                            text: term,
                            newTag: true
                        }
                    }
                }
            });
        }

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            onOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true
        });

        function loadingOpen(selector) {
            $(selector).waitMe({
                effect: 'progressBar',
                text: 'Mohon Tunggu ...',
                bg: 'rgba(255,255,255,0.7)',
                color: '#000'
            });
        }

        function loadingClose(selector) {
            $(selector).waitMe('hide');
        }
    </script>
</head>
