window.gBaseUrl = $('meta[name="url"]').attr('content') + '/';
window.gDataTable = '';

const swalInit = Swal.mixin({
    buttonsStyling: false,
    showCloseButton: false,
    customClass: {
        confirmButton: 'btn btn-primary mx-1',
        cancelButton: 'btn btn-danger mx-1',
        denyButton: 'btn btn-light mx-1',
        input: 'form-control',
    },
});

Noty.overrideDefaults({
    theme: 'limitless',
    timeout: 2500
});

lightbox.option({
    resizeDuration: 200,
    wrapAround: true
});

$(function () {
    configDataTable();
    disableEnterFormAjax();
    select2Basic();
});

function select2Basic() {
    $('.select2-basic').select2({
        placeholder: 'Pilih',
        language: 'id',
    });
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
    $.extend($.fn.dataTable.defaults, {
        autoWidth: true,
        lengthMenu: [10, 25, 50, 75, 100],
        pageLength: 10,
        stateDuration: 60 * 60 * 24,
        dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
        language: {
            search: '<span class="me-1">Cari</span> <div class="form-control-feedback form-control-feedback-end flex-fill">_INPUT_<div class="form-control-feedback-icon"><i class="ph-magnifying-glass opacity-50"></i></div></div>',
            searchPlaceholder: 'Kata Kunci ...',
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
    });
}

function onLoading(type, selector, text = "") {
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

function notification(type, text, layout = 'topRight') {
    new Noty({
        layout: layout,
        text: text,
        type: type,
    }).show();
}

function logout() {
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

function datePickerBasic(selector, additionalConfig = {}) {
    moment.locale('id');

    var configuration = $.extend({
        parentEl: '.content-inner',
        autoUpdateInput: false,
        language: 'id',
        showDropdowns: true,
        ranges: {
            'Hari Ini': [moment(), moment()],
            'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
            '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
            '1 Bulan Sebelumnya': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
        },
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

function datePickerSingle(selector, additionalConfig = {}) {
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

function select2Serverside(selector, endpoint, payload = {}, additionalConfig = {}) {
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

function select2ServersideTag(selector, endpoint, payload = {}) {
    $(selector).select2({
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
    });
}

function dragAndDropFile(selector = '.file-input', additionalConfig = {}) {
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
