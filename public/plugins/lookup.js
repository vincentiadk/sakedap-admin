window.gLookupDialogDataTable = '';

function lookupDialog(options) {
    const { title, dtAjaxUrl, dtColumns, dtAjaxData, onSelect } = options;
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
            order: [[0, 'desc']],
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
            columns: dtColumns
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
