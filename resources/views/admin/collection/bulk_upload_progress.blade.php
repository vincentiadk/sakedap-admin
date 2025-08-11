<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <h3 class="content-header-title mb-1 d-inline-block">Progress Bulk Upload</h3><br>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('admin/collection/bulk_upload') }}">Bulk Upload</a></li>
                            <li class="breadcrumb-item active">Progress</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-header-right col-md-6 col-12 mb-2 mt-1">
                <div class="float-md-right">
                    <div class="form-group">
                        <a href="{{ url('admin/collection/bulk_upload') }}" class="btn btn-secondary"><i class="la la-arrow-left"></i> Kembali</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="configuration">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-content collapse show">
                                <div class="card-body card-dashboard">
                                    <table class="table table-bordered table-striped" id="datatable_serverside">
                                        <thead>
                                            <tr class="text-center">
                                                <th>No</th>
                                                <th>File</th>
                                                <th>Start</th>
                                                <th>Finish</th>
                                                <th>Status</th>
                                                <th>Lihat</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<div class="modal animated bounceInRight text-left" id="modal_element" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel49" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel49">Detail Progress File</h4>
                <button type="button" class="btn btn-success btn-sm" id="btn_load_progress" onclick="loadDetailProgress()"><i class="la la-refresh"></i></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-light">
                    <ul style="max-height:400px; overflow-y:auto;" id="data_detail_progress"></ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn grey btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        loadDataTable();
    });

    function loadDataTable() {
        $('#datatable_serverside').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            scrollX: true,
            order: [[0, 'desc']],
            iDisplayInLength: 10,
            ajax: {
                url: '{{ url("admin/collection/bulk_upload/progress/datatable") }}'
            },
            columns: [
                {
                    name: 'id',
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'name',
                    className: 'align-middle text-center'
                },
                {
                    name: 'process_start_at',
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'process_finish_at',
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'status',
                    searchable: false,
                    className: 'align-middle text-center'
                },
                {
                    name: 'action',
                    searchable: false,
                    orderable: false,
                    className: 'align-middle text-center'
                }
            ]
        });
    }

    function loadDetailProgress(param) {
        $.ajax({
            url: '{{ url("admin/collection/bulk_upload/progress/show") }}',
            type: 'GET',
            dataType: 'JSON',
            data: {
                id: param
            },
            beforeSend: function() {
                loadingOpen('.modal-content');
                $('#data_detail_progress').html('');
            },
            success: function(response) {
                loadingClose('.modal-content');
                $.each(response.bulk_detail, function(i, val) {
                    $('#data_detail_progress').append(`
                        <div class="font-italic">` + val.title + `</div>
                        <div class="font-weight-bold">Description : ` + val.description + `</div>
                        <div class="font-weight-bold">Status : ` + val.status + `</div>
                    `);
                });
            },
            error: function() {
                loadingClose('.modal-content');
                Toast.fire({
                    icon: 'error',
                    title: 'Server Error!'
                });
            }
        });
    }

    function show(id) {
        $('#modal_element').modal('show');
        $('#btn_load_progress').attr('onclick', 'loadDetailProgress(' + id + ')');
        loadDetailProgress(id);
    }
</script>
