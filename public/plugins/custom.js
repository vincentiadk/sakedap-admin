window.gBaseUrl = $('meta[name="url"]').attr('content') + '/';
window.gDataTable = '';
window.gLookupDialogDataTable = '';

let swalInit;

$(function () {
    initSweetAlert();
    initNoty();
    initLightBox();
    configDataTable();
    disableEnterFormAjax();
    select2Basic();
    iframeable();
    notificationHeader();

    $(document).on('select2:opening', function (e) {
        if ($(e.target).attr('readonly')) {
            e.preventDefault();

            return false;
        }
    });

    $('select[readonly]').each(function () {
        $(this).next('.select2-container').addClass('is-readonly');
    });

    $(document).on('init.dt', function (e, settings) {
        if (!settings.oInit.scrollX) {
            return;
        }

        var $wrapper = $(settings.nTableWrapper);
        var $topWrapper = $wrapper.find('.dt-top-scroll-wrapper');
        var $scrollBody = $wrapper.find('.dataTables_scrollBody');

        if ($topWrapper.children().length === 0) {
            $topWrapper.append('<div class="top-scroll-content"></div>');
        }

        var $topContent = $topWrapper.find('.top-scroll-content');

        $topWrapper.hide();

        function adjustWidthAndSync() {
            var scrollBodyEl = $scrollBody.get(0);
            var isScrollNeeded = scrollBodyEl.scrollWidth > scrollBodyEl.clientWidth;

            if (isScrollNeeded) {
                $topWrapper.show();

                var tableWidth = $scrollBody.find('table').width();

                $topContent.width(tableWidth);
                $topWrapper.scrollLeft($scrollBody.scrollLeft());
            } else {
                $topWrapper.hide();
            }
        }

        setTimeout(adjustWidthAndSync, 100);

        $topWrapper.off('scroll.topscroll').on('scroll.topscroll', function () {
            $scrollBody.scrollLeft($topWrapper.scrollLeft());
        });

        $scrollBody.off('scroll.topscroll').on('scroll.topscroll', function () {
            $topWrapper.scrollLeft($scrollBody.scrollLeft());
        });

        $wrapper.on('draw.dt column-sizing.dt', function () {
            setTimeout(adjustWidthAndSync, 50);
        });

        $(window).on('resize', adjustWidthAndSync);
    });
});

function debounce(callback, delay) {
    let timeout;

    return function () {
        const context = this;
        const args = arguments;

        clearTimeout(timeout);

        timeout = setTimeout(() => {
            callback.apply(context, args);
        }, delay);
    };
}

function initSweetAlert() {
    if (typeof Swal !== 'undefined') {
        swalInit = Swal.mixin({
            buttonsStyling: false,
            showCloseButton: false,
            customClass: {
                confirmButton: 'btn btn-primary mx-1',
                cancelButton: 'btn btn-danger mx-1',
                denyButton: 'btn btn-light mx-1',
                input: 'form-control',
            },
        });
    }
}

function initNoty() {
    if (typeof Noty !== 'undefined') {
        Noty.overrideDefaults({
            theme: 'limitless',
            timeout: 2500
        });
    }
}

function initLightBox() {
    if (typeof lightbox !== 'undefined') {
        lightbox.option({
            resizeDuration: 200,
            wrapAround: true
        });
    }
}

function iframeable() {
    try {
        if (window.self !== window.top) {
            $('.iframeable').hide();
        }
    } catch (e) {
        $('.iframeable').hide();
    }
}

function select2Basic() {
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2-basic').select2({
            placeholder: 'Pilih',
            language: 'id',
        });
    }
}

function disableEnterFormAjax() {
    $('.form-ajax').keydown(function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
}

function configDataTable() {
    if (typeof $.fn.dataTable !== 'undefined') {
        $.extend($.fn.dataTable.defaults, {
            autoWidth: true,
            lengthMenu: [10, 25, 50, 75, 100],
            pageLength: 10,
            stateDuration: 60 * 60 * 24,
            searchDelay: 500,
            dom: '<"datatable-header justify-content-start"f<"ms-sm-auto"l><"ms-sm-3"B>><"dt-top-scroll-wrapper"><"datatable-scroll-wrap"t><"datatable-footer"ip>',
            language: {
                search: '<div class="form-control-feedback form-control-feedback-end flex-fill">_INPUT_<div class="form-control-feedback-icon"><i class="ph-magnifying-glass opacity-50"></i></div></div>',
                searchPlaceholder: 'Cari ...',
                lengthMenu: '<span class="me-1">Tampilkan</span> _MENU_',
                paginate: {
                    first: 'Halawan Awal',
                    last: 'Halaman Akhir',
                    next: document.dir == 'rtl' ? 'Sebelumnya' : 'Selanjutnya',
                    previous: document.dir == 'rtl' ? 'Selanjutnya' : 'Sebelumnya',
                },
                emptyTable: 'Tidak ada data',
                info: 'Menampilkan _START_ hingga _END_ dari _TOTAL_ data',
                infoEmpty: 'Menampilkan 0 hingga 0 dari 0 data',
                infoFiltered: '',
                loadingRecords: 'Memuat ...',
                zeroRecords: 'Tidak ada data',
                pageButton: 'btn btn-primary',
            },
            buttons: {
                dom: {
                    button: {
                        className: 'btn btn-secondary'
                    },
                },
                buttons: [
                    {
                        extend: 'collection',
                        text: '<i class="ph-microsoft-excel-logo me-1"></i> Download Excel',
                        buttons: [
                            {
                                text: 'Semua Data',
                                action: function (e, dt, button, config) {
                                    var self = this;
                                    var info = dt.page.info();
                                    var totalRecords = info.recordsDisplay;
                                    var chunkSize = 50;
                                    var allData = [];
                                    var start = 0;
                                    var currentRequest = null;
                                    var isCancelled = false;
                                    var isPaused = false;

                                    var modalEl = document.getElementById('modal-datatable-download');
                                    var modalProg = new bootstrap.Modal(modalEl);

                                    function setUIProcessing() {
                                        isPaused = false;
                                        isCancelled = false;

                                        $('#modal-datatable-download-status').text('Proses Download...');
                                        $('.spinner-border').addClass('text-success').removeClass('text-danger').show();
                                        $('#btn-resume-download, #btn-retry-download').addClass('d-none');
                                        $('#btn-cancel-download').removeClass('d-none');
                                    }

                                    function setUIError(message) {
                                        isPaused = true;

                                        $('#modal-datatable-download-status').text(message);
                                        $('.spinner-border').hide();
                                        $('#btn-resume-download, #btn-retry-download').removeClass('d-none');

                                        swalInit.fire({
                                            title: 'Koneksi Terputus',
                                            text: 'Gagal mengambil data. Silakan klik "Lanjutkan" untuk mencoba lagi.',
                                            icon: 'error',
                                            confirmButtonText: 'Siap'
                                        });
                                    }

                                    $('#modal-datatable-download-progress').css('width', '0%').text('0%').addClass('bg-success').removeClass('bg-info');
                                    $('#modal-datatable-download-progress-count').text('0 / ' + totalRecords + ' Data');

                                    setUIProcessing();
                                    modalProg.show();

                                    $('#btn-cancel-download').off('click').on('click', function () {
                                        isCancelled = true;

                                        if (currentRequest) currentRequest.abort();
                                        modalProg.hide();

                                        swalInit.fire({
                                            title: 'Dibatalkan',
                                            text: 'Proses download telah dihentikan.',
                                            icon: 'info',
                                            timer: 2000,
                                            showConfirmButton: false
                                        });
                                    });

                                    $('#btn-resume-download').off('click').on('click', function () {
                                        setUIProcessing();
                                        fetchNextBatch();
                                    });

                                    $('#btn-retry-download').off('click').on('click', function () {
                                        allData = [];
                                        start = 0;

                                        $('#modal-datatable-download-progress').css('width', '0%').text('0%');

                                        setUIProcessing();
                                        fetchNextBatch();
                                    });

                                    function fetchNextBatch() {
                                        if (isCancelled || isPaused) return;
                                        var params = dt.ajax.params();

                                        params.start = start;
                                        params.length = chunkSize;

                                        currentRequest = $.ajax({
                                            url: dt.ajax.url(),
                                            dataType: 'JSON',
                                            type: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                            },
                                            data: params,
                                            success: function (json) {
                                                if (isCancelled) return;

                                                allData = allData.concat(json.data);
                                                var percentage = Math.round((allData.length / totalRecords) * 100);

                                                $('#modal-datatable-download-progress').css('width', percentage + '%').text(percentage + '%');
                                                $('#modal-datatable-download-progress-percent').text(percentage + '%');
                                                $('#modal-datatable-download-progress-count').text(allData.length + ' / ' + totalRecords + ' Data');

                                                if (allData.length < totalRecords && json.data.length > 0) {
                                                    start += chunkSize;

                                                    setTimeout(fetchNextBatch, 100);
                                                } else {
                                                    generateExcelFile(allData, modalProg);
                                                }
                                            },
                                            error: function (xhr, status) {
                                                if (status !== 'abort') {
                                                    setUIError('Koneksi Terputus atau Macet!');
                                                }
                                            }
                                        });
                                    }

                                    function generateExcelFile(combinedData, modalInstance) {
                                        if (isCancelled) return;

                                        $('#modal-datatable-download-status').text('Menyusun File Excel...');
                                        $('.spinner-border').removeClass('text-success').addClass('text-info').show();
                                        $('#modal-datatable-download-progress').addClass('bg-info').removeClass('bg-success');
                                        $('#btn-cancel-download, #btn-resume-download, #btn-retry-download').addClass('d-none');

                                        setTimeout(function () {
                                            try {
                                                var reportData = combinedData.map(function (item, index) {
                                                    var rowObject = {
                                                        "No": index + 1
                                                    };

                                                    dt.columns(':visible').every(function (idx) {
                                                        var colDef = dt.column(idx).settings()[0].aoColumns[idx];

                                                        if (colDef.export !== false) {
                                                            var headerText = $(this.header()).text().trim();
                                                            var dataKey = this.dataSrc();

                                                            if (dataKey) {
                                                                rowObject[headerText] = item[dataKey];
                                                            }
                                                        }
                                                    });

                                                    return rowObject;
                                                });

                                                var ws = XLSX.utils.json_to_sheet(reportData);
                                                var wb = XLSX.utils.book_new();

                                                XLSX.utils.book_append_sheet(wb, ws, 'Data Export');
                                                XLSX.writeFile(wb, 'Download_Excel_Sakedap_' + new Date().getTime() + ".xlsx");

                                                modalInstance.hide();

                                                swalInit.fire({
                                                    title: 'Berhasil!',
                                                    text: 'Sebanyak ' + combinedData.length + ' data berhasil diunduh.',
                                                    icon: 'success',
                                                    confirmButtonText: 'Oke'
                                                });

                                            } catch (err) {
                                                modalInstance.hide();

                                                swalInit.fire({
                                                    title: 'Error',
                                                    text: 'Gagal menyusun file Excel: ' + err.message,
                                                    icon: 'error'
                                                });
                                            }
                                        }, 500);
                                    }

                                    fetchNextBatch();
                                }
                            },
                            {
                                extend: 'excelHtml5',
                                text: 'Halaman Ini Saja',
                                exportOptions: {
                                    modifier: {
                                        page: 'current',
                                    }
                                }
                            },
                        ]
                    }
                ]
            },
        });
    }
}

function onLoading(type, selector, text = '') {
    if (typeof $.fn.waitMe !== 'undefined') {
        if (type == 'show') {
            $(selector).waitMe({
                effect: 'ios',
                text: text,
                bg: 'rgba(255,255,255,0.7)',
                color: '#004096',
                waitTime: -1,
                textPos: 'vertical',
            });
        } else if (type == 'close') {
            $(selector).waitMe('hide');
        }
    }
}

function notification(type, text, layout = 'topRight') {
    if (typeof Noty !== 'undefined') {
        new Noty({
            layout: layout,
            text: text,
            type: type,
        }).show();
    }
}

function logout() {
    if (typeof Swal !== 'undefined') {
        swalInit.fire({
            title: 'Anda yakin ingin keluar?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, keluarkan',
            cancelButtonText: 'Tidak, batalkan',
        }).then((result) => {
            if (result.isConfirmed) {
                onLoading('show', 'body');
                document.location.href = window.gBaseUrl + 'auth/logout';
            }
        });
    }
}

function datePickerBasic(selector, additionalConfig = {}) {
    if (typeof $.fn.daterangepicker !== 'undefined') {
        moment.locale('id');

        var configuration = $.extend({
            parentEl: '.content-inner',
            autoUpdateInput: false,
            language: 'id',
            showDropdowns: true,
            locale: {
                applyLabel: 'Terapkan',
                cancelLabel: 'Batal',
                startLabel: 'Dari Tanggal',
                endLabel: 'Sampai Tanggal',
                customRangeLabel: 'Pilih Sendiri',
                daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                firstDay: 1,
                format: 'YYYY/MM/DD',
            },
        }, additionalConfig);

        $(selector).daterangepicker(configuration).on('apply.daterangepicker', function (e, picker) {
            picker.element.val(picker.startDate.format(picker.locale.format) + " - " + picker.endDate.format(picker.locale.format));
        });
    }
}

function datePickerSingle(selector, additionalConfig = {}) {
    if (typeof $.fn.daterangepicker !== 'undefined') {
        moment.locale('id');

        var configuration = $.extend({
            parentEl: '.content-inner',
            autoUpdateInput: false,
            singleDatePicker: true,
            showDropdowns: true,
            language: 'id',
            locale: {
                applyLabel: 'Terapkan',
                cancelLabel: 'Batal',
                daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                firstDay: 1,
                format: 'YYYY/MM/DD',
            },
        }, additionalConfig);

        $(selector).daterangepicker(configuration).on('apply.daterangepicker', function (e, picker) {
            picker.element.val(picker.startDate.format(picker.locale.format));
        });
    }
}

function select2Serverside(selector, endpoint, payload = {}, additionalConfig = {}) {
    if (typeof $.fn.select2 !== 'undefined') {
        var configuration = $.extend({
            placeholder: 'Pilih',
            minimumInputLength: 3,
            cache: true,
            ajax: {
                url: window.gBaseUrl + 'select2-serverside/' + endpoint,
                type: 'GET',
                dataType: 'JSON',
                delay: 250,
                language: 'id',
                transport: function (params, success, failure) {
                    if ($(this).attr('readonly')) {
                        return null;
                    }

                    return $.ajax(params).done(success).fail(failure);
                },
                data: function (params) {
                    return $.extend({
                        search: params.term,
                    }, payload);
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
            },
            templateResult: function (data) {
                if (data.loading) {
                    return data.text;
                }

                var $container = $(data.html);

                return $container;
            },
            templateSelection: function (data) {
                return data.text;
            }
        }, additionalConfig);

        $(selector).select2(configuration);
    }
}

function select2ServersideTag(selector, endpoint, payload = {}, additionalConfig = {}) {
    if (typeof $.fn.select2 !== 'undefined') {
        var configuration = $.extend({
            placeholder: 'Pilih',
            minimumInputLength: 1,
            cache: true,
            tags: true,
            multiple: true,
            ajax: {
                url: window.gBaseUrl + 'select2-serverside/' + endpoint,
                type: 'GET',
                dataType: 'JSON',
                delay: 250,
                language: 'id',
                transport: function (params, success, failure) {
                    if ($(this).attr('readonly')) {
                        return null;
                    }

                    return $.ajax(params).done(success).fail(failure);
                },
                data: function (params) {
                    return $.extend({
                        search: params.term,
                        return_value: true,
                    }, payload);
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
            },
            createTag: function (params) {
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
        }, additionalConfig);

        $(selector).select2(configuration);
    }
}

function dragAndDropFile(selector = '.file-input', additionalConfig = {}) {
    if (typeof $.fn.fileinput !== 'undefined') {
        const previewZoomButtonClasses = {
            rotate: 'btn btn-light btn-icon btn-sm',
            toggleheader: 'btn btn-light btn-icon btn-header-toggle btn-sm',
            fullscreen: 'btn btn-light btn-icon btn-sm',
            borderless: 'btn btn-light btn-icon btn-sm',
            close: 'btn btn-light btn-icon btn-sm',
        };

        const previewZoomButtonIcons = {
            prev: document.dir == 'rtl' ? '<i class="ph-arrow-right"></i>' : '<i class="ph-arrow-left"></i>',
            next: document.dir == 'rtl' ? '<i class="ph-arrow-left"></i>' : '<i class="ph-arrow-right"></i>',
            rotate: '<i class="ph-arrow-clockwise"></i>',
            toggleheader: '<i class="ph-arrows-down-up"></i>',
            fullscreen: '<i class="ph-corners-out"></i>',
            borderless: '<i class="ph-frame-corners"></i>',
            close: '<i class="ph-x"></i>',
        };

        const fileActionSettings = {
            zoomClass: '',
            zoomIcon: '<i class="ph-magnifying-glass-plus"></i>',
            dragClass: "p-2",
            dragIcon: '<i class="ph-dots-six"></i>',
            removeClass: "",
            removeErrorClass: "text-danger",
            removeIcon: '<i class="ph-trash"></i>',
            indicatorNew: '<i class="ph-file-plus text-success"></i>',
            indicatorSuccess: '<i class="ph-check file-icon-large text-success"></i>',
            indicatorError: '<i class="ph-x text-danger"></i>',
            indicatorLoading: '<i class="ph-spinner spinner text-muted"></i>',
        };

        var configuration = $.extend({
            showUpload: false,
            browseLabel: 'Telusuri',
            browseOnZoneClick: true,
            autoReplace: true,
            browseIcon: '<i class="ph-file-plus me-2"></i>',
            uploadIcon: '<i class="ph-file-arrow-up me-2"></i>',
            removeIcon: '<i class="ph-x fs-base me-2"></i>',
            layoutTemplates: {
                icon: '<i class="ph-check"></i>',
            },
            browseClass: 'btn btn-light',
            uploadClass: 'btn btn-light',
            removeClass: 'btn btn-light',
            initialCaption: 'Tidak ada file',
            initialPreviewAsData: true,
            previewZoomButtonClasses: previewZoomButtonClasses,
            previewZoomButtonIcons: previewZoomButtonIcons,
            fileActionSettings: fileActionSettings,
        }, additionalConfig);

        $(selector).fileinput(configuration);
    }
}

function onPopover(selector, content, title = '') {
    if ($('.popover').length == 0) {
        var myPopover = new bootstrap.Popover($(selector), {
            container: 'body',
            trigger: 'focus',
            html: true,
            content: content,
            title: title,
            placement: 'auto',
        });

        myPopover.enable();
        myPopover.show();
    }
}

function randomString(length) {
    let result = '';
    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    const charactersLength = characters.length;

    for (let i = 0; i < length; i++) {
        result += characters.charAt(Math.floor(Math.random() * charactersLength));
    }

    return result;
}

function notificationHeader() {
    $.ajax({
        url: gBaseUrl + 'notification',
        type: 'GET',
        dataType: 'JSON',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        beforeSend: function () {
            onLoading('show', '#notification-header-executor-list');
            onLoading('show', '#notification-header-file-list');

            $('#notification-header-executor-total').text(0);
            $('#notification-header-executor-list').html('');
            $('#notification-header-file-total').text(0);
            $('#notification-header-file-list').html('');
        },
        success: function (response) {
            onLoading('close', '#notification-header-executor-list');
            onLoading('close', '#notification-header-file-list');

            if (response.executor.length > 0) {
                $.each(response.executor, function (i, val) {
                    $('#notification-header-executor-total').text(response.executor.length);
                    $('#notification-header-executor-list').append(`
                        <a href="javascript:void(0);" class="dropdown-item align-items-start text-wrap py-2 no-click">
                            <div class="me-3">
                                <img src="${gBaseUrl}assets/team.png" class="w-40px h-40px" alt="">
                            </div>
                            <div class="flex-1">
                                <span class="fw-semibold">${val.NAME}</span>
                                <span class="text-muted float-end fs-sm">${val.UPDATEDATE}</span>
                                <div class="text-muted">${val.NAMAPROPINSI}</div>
                            </div>
                        </a>
                    `);

                    if (i + 1 == 10) {
                        return false;
                    }
                });
            } else {
                $('#notification-header-executor-list').html(`<div class="p-3 text-muted text-center">Tidak ada data</div>`);
            }

            if (response.file.length > 0) {
                $.each(response.file, function (i, val) {
                    $('#notification-header-file-total').text(response.file.length);
                    $('#notification-header-file-list').append(`
                        <a href="javascript:void(0);" class="dropdown-item align-items-start text-wrap py-2 no-click">
                            <div class="me-3">
                                <img src="${gBaseUrl}assets/demand.png" class="w-40px h-40px" alt="">
                            </div>
                            <div class="flex-1">
                                <span class="fw-semibold">${val.TITLE}</span>
                                <span class="text-muted float-end fs-sm">${val.CREATED_AT}</span>
                                <div class="text-muted">${val.NAME}</div>
                            </div>
                        </a>
                    `);

                    if (i + 1 == 10) {
                        return false;
                    }
                });
            } else {
                $('#notification-header-file-list').html(`<div class="p-3 text-muted text-center">Tidak ada data</div>`);
            }
        },
        error: function (response) {
            notificationHeader();
        }
    });
}

function lookup(options) {
    const { title, dtAjaxUrl, dtColumns, dtAjaxData, dtOrder, onSelect } = options;
    const $modal = $('#lookup-dialog-modal');

    if ($.fn.DataTable.isDataTable('#lookup-dialog-datatable')) {
        $('#lookup-dialog-datatable').DataTable().destroy();
        $('#lookup-dialog-datatable tbody').off('click', '.select-btn');
    }

    $('#lookup-dialog-title').text(title);

    $modal.modal('show');

    $modal.off('shown.bs.modal').on('shown.bs.modal', function () {
        window.gLookupDialogDataTable = $('#lookup-dialog-datatable').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            deferRender: true,
            destroy: true,
            order: dtOrder,
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.childRowImmediate,
                    renderer: function (api, rowIdx, columns) {
                        let data = columns.map((col, i) => {
                            if (col.hidden) {
                                return `
                                    <div class="col-md-2 fw-semibold">
                                        ${col.title}
                                        <span class="float-end pe-2">:</span>
                                    </div>
                                    <div class="col-md-10">
                                        <span class="overflow-hidden text-wrap">${col.data}</span>
                                    </div>
                                `
                            } else {
                                return '';
                            }
                        }).join('');

                        return '<div class="row g-0 py-1">' + data + '</div>';
                    }
                }
            },
            ajax: {
                url: dtAjaxUrl,
                dataType: 'JSON',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function (d) {
                    if (typeof options.dtAjaxData === 'function') {
                        $.extend(d, options.dtAjaxData());
                    }

                    return d;
                },
                beforeSend: function () {
                    onLoading('show', '#lookup-dialog-datatable_wrapper');
                },
                error: function (response) {
                    onLoading('close', '#lookup-dialog-datatable_wrapper');
                    responseError(response);
                }
            },
            columns: dtColumns,
        }).on('draw.dt', function () {
            onLoading('close', '#lookup-dialog-datatable_wrapper');
        });

        window.gLookupDialogDataTable.columns.adjust().draw();

        $('#lookup-dialog-datatable tbody').off('click', '.select-btn').on('click', '.select-btn', function () {
            const $row = $(this).closest('tr');
            const $data = $row.find('.data');
            let data = $data;

            onSelect(data);

            $modal.modal('hide');
        });

        var $scrollWrapper = $('#lookup-dialog-datatable').closest('.dataTables_wrapper').find('.dataTables_scrollBody');
        $scrollWrapper.attr('tabindex', '0');
    }).off('hidden.bs.modal').on('hidden.bs.modal', function () {
        if ($.fn.DataTable.isDataTable('#lookup-dialog-datatable')) {
            $('#lookup-dialog-datatable').DataTable().destroy();
        }
    });
}

function lookupCatalog(selectorInput, selectorId, replaceID = false, payload = {}) {
    $(selectorInput).click(function () {
        var currentSearchableValue = $('#lookup-dialog-filter-searchable').val();

        var dataAjax = $.extend({
            searchable: currentSearchableValue
        }, payload);

        $('#lookup-dialog-filter').html(`
            <div class="input-group">
                <span class="input-group-text">Cari Berdasarkan</span>
                <select class="form-select select2-basic" id="lookup-dialog-filter-searchable" data-width="1%" data-dropdown-parent="#lookup-dialog-modal" data-placeholder="Global" multiple>
                    <option value="c.bibid">BIB ID</option>
                    <option value="c.title">Judul</option>
                    <option value="c.author">Kepeng</option>
                    <option value="p.name">Pelaksana Serah</option>
                    <option value="c.publishyear">Tahun Terbit</option>
                    <option value="c.subject">Subjek</option>
                    <option value="c.isbn">ISBN</option>
                    <option value="c.callnumber">Nomor Panggil</option>
                    <option value="w.name">Jenis Bahan</option>
                </select>
            </div>
        `);

        $('#lookup-dialog-datatable thead').html(`
            <tr>
                <th class="text-nowrap text-center">No</th>
                <th class="text-nowrap text-center">#</th>
                <th class="text-nowrap">BIB ID</th>
                <th class="text-nowrap">ISBN</th>
                <th class="text-nowrap">Nomor Panggil</th>
                <th class="text-nowrap">Jumlah Koleksi</th>
                <th class="text-nowrap">Tahun Terbit</th>
                <th class="text-nowrap">Judul</th>
                <th class="text-nowrap">Pelaksana Serah</th>
                <th class="text-nowrap">Kepengarangan</th>
                <th class="text-nowrap">Detail</th>
            </tr>
        `);

        lookup({
            title: 'Pilih Data Katalog',
            dtAjaxUrl: window.gBaseUrl + 'datatable-serverside/catalog',
            dtAjaxData: function () {
                dataAjax.searchable = $('#lookup-dialog-filter-searchable').val();
                return dataAjax;
            },
            dtOrder: [],
            dtColumns: [
                { orderable: true, className: 'align-middle text-nowrap text-center' },
                { orderable: false, className: 'align-middle text-nowrap text-center' },
                { orderable: true, className: 'align-middle text-nowrap' },
                { orderable: true, className: 'align-middle text-nowrap' },
                { orderable: true, className: 'align-middle text-nowrap' },
                { orderable: true, className: 'align-middle text-nowrap' },
                { orderable: true, className: 'align-middle text-nowrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
            ],
            onSelect: function (data) {
                $(selectorId).val(data.data('id'));

                if (replaceID == true) {
                    $(selectorInput).val(data.data('id'));
                } else {
                    $(selectorInput).val(data.data('title'));
                }

                $(selectorInput).change();
            }
        });

        select2Basic();

        if (currentSearchableValue && currentSearchableValue.length > 0) {
            $('#lookup-dialog-filter-searchable').val(currentSearchableValue).trigger('change');
        }

        $('#lookup-dialog-filter-searchable').change(function (e) {
            if (window.gLookupDialogDataTable) {
                window.gLookupDialogDataTable.ajax.reload(null, false);
            }
        });
    });
}

function lookupCatalogParent(selectorInput, selectorId) {
    $(selectorInput).click(function () {
        var currentSearchableValue = $('#lookup-dialog-filter-searchable').val();

        $('#lookup-dialog-filter').html(`
            <div class="input-group">
                <span class="input-group-text">Cari Berdasarkan</span>
                <select class="form-select select2-basic" id="lookup-dialog-filter-searchable" data-width="1%" data-dropdown-parent="#lookup-dialog-modal" data-placeholder="Global" multiple>
                    <option value="c.bibid">BIB ID</option>
                    <option value="c.title">Judul</option>
                    <option value="c.author">Kepeng</option>
                    <option value="p.name">Pelaksana Serah</option>
                    <option value="c.publishyear">Tahun Terbit</option>
                    <option value="c.subject">Subjek</option>
                    <option value="c.isbn">ISBN</option>
                    <option value="c.callnumber">Nomor Panggil</option>
                    <option value="w.name">Jenis Bahan</option>
                </select>
            </div>
        `);

        $('#lookup-dialog-datatable thead').html(`
            <tr>
                <th class="text-nowrap text-center">No</th>
                <th class="text-nowrap text-center">#</th>
                <th class="text-nowrap">BIB ID</th>
                <th class="text-nowrap">ISBN</th>
                <th class="text-nowrap">Nomor Panggil</th>
                <th class="text-nowrap">Jumlah Koleksi</th>
                <th class="text-nowrap">Tahun Terbit</th>
                <th class="text-nowrap">Judul</th>
                <th class="text-nowrap">Pelaksana Serah</th>
                <th class="text-nowrap">Kepengarangan</th>
                <th class="text-nowrap">Detail</th>
            </tr>
        `);

        lookup({
            title: 'Pilih Data Katalog Parent',
            dtAjaxUrl: window.gBaseUrl + 'datatable-serverside/catalog-parent',
            dtAjaxData: function () {
                return {
                    searchable: $('#lookup-dialog-filter-searchable').val()
                };
            },
            dtOrder: [],
            dtColumns: [
                { orderable: true, className: 'align-middle text-nowrap text-center' },
                { orderable: false, className: 'align-middle text-nowrap text-center' },
                { orderable: true, className: 'align-middle text-nowrap' },
                { orderable: true, className: 'align-middle text-nowrap' },
                { orderable: true, className: 'align-middle text-nowrap' },
                { orderable: true, className: 'align-middle text-nowrap' },
                { orderable: true, className: 'align-middle text-nowrap' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
                { orderable: true, className: 'align-middle' },
            ],
            onSelect: function (data) {
                $(selectorId).val(data.data('id'));
                $(selectorInput).val(data.data('title'));
                $(selectorInput).change();
            }
        });

        select2Basic();

        if (currentSearchableValue && currentSearchableValue.length > 0) {
            $('#lookup-dialog-filter-searchable').val(currentSearchableValue).trigger('change');
        }

        $('#lookup-dialog-filter-searchable').change(function (e) {
            if (window.gLookupDialogDataTable) {
                window.gLookupDialogDataTable.ajax.reload(null, false);
            }
        });
    });
}

function lookupCatalogHistory(table, id) {
    $('#lookup-dialog-filter').html('');

    $('#lookup-dialog-datatable thead').html(`
        <tr>
            <th class="text-nowrap text-center">No</th>
            <th class="text-nowrap">Judul</th>
            <th class="text-nowrap">Aksi</th>
            <th class="text-nowrap">User</th>
            <th class="text-nowrap">Tgl</th>
            <th class="text-nowrap">Ket</th>
        </tr>
    `);

    lookup({
        title: 'Histori ' + table + ' ' + id,
        dtAjaxUrl: window.gBaseUrl + 'datatable-serverside/catalog-history',
        dtAjaxData: function () {
            return {
                table: table,
                id: id,
            };
        },
        dtOrder: [[0, 'desc']],
        dtColumns: [
            { orderable: true, className: 'align-middle text-nowrap text-center' },
            { orderable: true, className: 'align-middle text-wrap' },
            { orderable: true, className: 'align-middle' },
            { orderable: true, className: 'align-middle text-wrap' },
            { orderable: true, className: 'align-middle text-nowrap' },
            { orderable: true, className: 'align-middle text-wrap' },
        ],
    });
}

function responseError(response) {
    let errorException = 'Error ...';
    let errorMessage = 'Refresh ulang browser';

    if (response?.responseJSON?.exception || response?.responseJSON?.message) {
        errorException = response?.responseJSON?.exception ?? 'Error ...';
        errorMessage = response?.responseJSON?.message ?? 'Refresh ulang browser';
    }

    swalInit.fire({
        html: `<b>${errorException}</b><br>${errorMessage}`,
        icon: 'error',
        showCloseButton: false
    });
}
