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
    <link rel="apple-touch-icon" href="{{ asset(Storage::url('public/main/favicon.png')) }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset(Storage::url('public/main/favicon.png')) }}">
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Quicksand:300,400,500,700"
        rel="stylesheet">
    <link href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/vendors.css') }}">
    <link rel="stylesheet"
        href="{{ asset('theme_admin/app-assets/vendors/css/tables/datatable/jquery.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/vendors/css/tables/datatable/datatables.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('theme_admin/app-assets/vendors/css/tables/extensions/buttons.dataTables.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('theme_admin/app-assets/vendors/css/tables/datatable/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/vendors/css/forms/selects/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/vendors/css/editors/summernote.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/plugins/extensions/toastr.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/plugins/lightbox/dist/css/lightbox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/plugins/waitMe/waitMe.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/core/menu/menu-types/vertical-menu.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/core/colors/palette-gradient.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/css/plugins/forms/wizard.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/app-assets/vendors/css/extensions/nouislider.min.css') }}">

    <link rel="stylesheet" href="{{ asset('theme_admin/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_admin/plugins/dropzone/dropzone.min.css') }}">
    <link href="https://transloadit.edgly.net/releases/uppy/v1.21.2/uppy.min.css" rel="stylesheet">

    <script src="{{ asset('theme_admin/app-assets/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('theme_admin/app-assets/vendors/js/extensions/toastr.min.js') }}"></script>
    <script src="{{ asset('theme_admin/app-assets/vendors/js/tables/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('theme_admin/app-assets/vendors/js/tables/datatable/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('theme_admin/app-assets/vendors/js/tables/datatable/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('theme_admin/plugins/dataTables/input.js') }}"></script>
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
    <script src="{{ asset('theme_admin/plugins/lightbox/dist/js/lightbox.min.js') }}"></script>
    <script src="{{ asset('theme_admin/plugins/waitMe/waitMe.min.js') }}"></script>
    <script src="{{ asset('theme_admin/plugins/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('theme_admin/plugins/viewerjs/pdf.js') }}"></script>
    <script src="{{ asset('theme_admin/plugins/viewerjs/pdf.worker.js') }}"></script>
    <script src="{{ asset('theme_admin/app-assets/vendors/js/forms/validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('theme_admin/app-assets/vendors/js/extensions/jquery.steps.min.js') }}"></script>
    <script src="{{ asset('theme_admin/app-assets/vendors/js/extensions/nouislider.min.js') }}"></script>
    <script src="{{ asset('theme_admin/plugins/dropzone/dropzone.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>



    <style>
        .nowrap {
            white-space: nowrap;
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
    </style>

    <script>
        $(function() {
            $('body').tooltip({
                selector: '[data-toggle=tooltip]'
            });

            $('#datatable_default').DataTable({
                scrollX: true
            });

            $('.summernote').summernote({
                height: 200
            });

            $('.select2').select2({
                placeholder: '-- Pilih --',
                dropdownParent: $('#modal_element')
            });

            getAccountLocked();
            getTotalIsbn();
            getPublisherWarning();
        });

        function select2LoadAll(selector, endpoint) {
            $(selector).select2({
                placeholder: '-- Pilih --',
                allowClear: true,
                cache: true,
                dropdowntParent: $('#modal_element'),
                ajax: {
                    url: '{{ url('publisher/select2_serverside') }}' + '/' + endpoint,
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

        function select2Nested(selector, endpoint, nestedId) {
            $(selector).select2({
                placeholder: '-- Pilih --',
                allowClear: true,
                cache: true,
                dropdowntParent: $('#modal_element'),
                ajax: {
                    url: '{{ url('publisher/select2_serverside') }}' + '/' + endpoint,
                    type: 'POST',
                    dataType: 'JSON',
                    delay: 250,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(params) {
                        return {
                            search: params.term,
                            nested_id: nestedId != '' ? nestedId.val() : ''
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

        function select2AutoSuggest(selector, endpoint) {
            $(selector).select2({
                placeholder: '-- Pilih --',
                minimumInputLength: 3,
                allowClear: true,
                cache: true,
                dropdowntParent: $('#modal_element'),
                ajax: {
                    url: '{{ url('publisher/select2_serverside') }}' + '/' + endpoint,
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
                    url: '{{ url('publisher/select2_serverside') }}' + '/' + endpoint,
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
            console.log('select2AutoSuggestTags.selector: ' + selector)
            $(selector).select2({
                placeholder: '-- Pilih --',
                minimumInputLength: 3,
                allowClear: true,
                tags: true,
                cache: true,
                ajax: {
                    url: '{{ url('publisher/select2_serverside') }}' + '/' + endpoint,
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
                        return '';
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

        function getTotalIsbn() {
            $.ajax({
                url: '{{ url('publisher/bill_isbn/total') }}',
                type: 'GET',
                dataType: 'JSON',
                beforeSend: function() {

                },
                success: function(response) {
                    //if(response.total > 0) {
                    let html =
                        '<div class="alert alert-icon-left alert-info mb-2" role="alert"><span class="alert-icon"><i class="la la-bell-o"></i></span><p><strong>Tagihan ISBN Cetak Anda adalah: ' +
                        response.total_cetak + '</p></strong><p><strong>Tagihan Elektronik: ' + response
                        .total_elek + '</p></strong><p>Review: ' + response.total_review +
                        '</p><p>Bermasalah:' + response.total_problem + '</p></div>';
                    $('.content-wrapper').prepend(html)
                    //}
                }
            });
        }

        function getPublisherWarning() {
            $.ajax({
                url: '{{ url('publisher/bill_isbn/warning') }}',
                type: 'GET',
                dataType: 'JSON',
                beforeSend: function() {

            },
            success: function(response) {
                if(response.total > 0) {
                    let html = '<div class="alert alert-icon-left alert-warning mb-2" role="alert"><span class="alert-icon"><i class="la la-bell-o"></i></span><p><strong>Anda mendapat teguran ke-'+response.warning+'</p></strong><p>'+response.reason+'</p><p>Lampiran : <a href="'+response.attachment_link+'"> Link Lampiran </a></p></div>';
                    $('.content-wrapper').prepend(html)
                }
            }
        });
        }

        function getAccountLocked() {
            $.ajax({
                url: '{{ url('publisher/bill_isbn/locked') }}',
                type: 'GET',
                dataType: 'JSON',
                beforeSend: function() {

                },
                success: function(response) {
                    if (response.locked > 0) {
                        let html =
                            '<div class="alert alert-icon-left alert-danger mb-2" role="alert"><span class="alert-icon"><i class="la la-warning"></i></span><p><strong>Akun ISBN Anda diblokir! Segera serahkan tagihan ISBN Anda untuk membuka blokir.</p></strong></div>';
                        $('.content-wrapper').prepend(html)
                    }
                }
            });
        }
    </script>
</head>
