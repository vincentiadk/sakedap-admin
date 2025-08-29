<div class="page-header page-header-light shadow mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">Tambah Banyak</h4>
            <a href="#page-header" class="btn btn-light align-self-center collapsed d-lg-none border-transparent rounded-pill p-0 ms-auto" data-bs-toggle="collapse">
                <i class="ph-caret-down collapsible-indicator ph-sm m-1"></i>
            </a>
        </div>
    </div>
    <div class="page-header-content d-lg-flex border-top">
        <div class="d-flex">
            <div class="breadcrumb py-2">
                <a href="{{ url('home') }}" class="breadcrumb-item"><i class="ph-house"></i></a>
                <a href="javascript:void(0);" class="breadcrumb-item">Koleksi</a>
                <span class="breadcrumb-item active">Tambah Banyak</span>
            </div>
        </div>
    </div>
</div>
<div class="content pt-0">
    <div class="alert alert-danger d-none" id="validation-element">
        <ul class="mb-0" id="validation-data"></ul>
    </div>
    <form id="form-data">
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-text">Jenis</span>
                        <select class="form-select" name="type" id="type" onchange="changeType()">
                            <option value="">Pilih</option>
                            <option value="bulk_non_serial">Non Serial</option>
                            <option value="bulk_serial">Serial</option>
                        </select>
                        <span id="btn-template"></span>
                    </div>
                </div>
                <div class="form-group" id="param-id"></div>
                <div class="form-group">
                    <input type="file" name="file" id="file">
                </div>
            </div>
        </div>
    </form>
    <div class="card">
        <div class="card-body">
            <div class="text-end">
                <button type="button" class="btn btn-primary" onclick="submitted()">
                    <i class="ph-check me-1"></i>
                    Submit Data
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        dragAndDropFile('#file', {
            maxFileCount: 1,
            autoReplace: true,
            allowedFileExtensions: ['zip']
        });
    });

    function changeType() {
        var type = $('#type').val();

        $('#btn-template').html('');
        $('#param-id').html('');

        if(type == 'bulk_non_serial') {
            $('#btn-template').html(`
                <a href="{{ url('download/from-public') }}?path=assets/template-excel/bulk_non_serial.zip" class="btn btn-success rounded-start-0" target="_blank">
                    <i class="ph-download me-1"></i>
                    Download Template
                </a>
            `);

            $('#param-id').html(`
                <select class="form-select" name="id" id="id" data-placeholder="Pilih Penerbit"></select>
            `);

            select2Serverside('#id', 'publisher');
        } else if(type == 'bulk_serial') {
            $('#btn-template').html(`
                <a href="{{ url('download/from-public') }}?path=assets/template-excel/bulk_serial.zip" class="btn btn-success rounded-start-0" target="_blank">
                    <i class="ph-download me-1"></i>
                    Download Template
                </a>
            `);

            $('#param-id').html(`
                <select class="form-select" name="id" id="id" data-placeholder="Pilih Koleksi"></select>
            `);

            select2Serverside('#id', 'collection-parent');
        }
    }

    function clearValidation() {
        $('#validation-element').addClass('d-none');
        $('#validation-data').html('');
    }

    function showValidation(data) {
        $('#validation-element').removeClass('d-none');
        $('#validation-data').html('');

        $.each(data, function(index, value) {
            $('#validation-data').append('<li>' + value + '</li>');
        });
    }

    function submitted() {
        $.ajax({
            url: '{{ url("collection/create-more/submitted") }}',
            type: 'POST',
            dataType: 'JSON',
            data: new FormData($('#form-data')[0]),
            contentType: false,
            processData: false,
            cache: false,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            beforeSend: function() {
                onLoading('show', 'body');
                clearValidation();
            },
            success: function(response) {
                onLoading('close', 'body');

                if(response.code == 200) {
                    swalInit.fire({
                        title: 'Berhasil',
                        text: response.message,
                        icon: 'success',
                        showDenyButton: false,
                        showCancelButton: false,
                        confirmButtonText: 'Oke',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            onLoading('show', 'body');

                            location.href = '{{ url("collection/create-more") }}';
                        }
                    });
                } else if(response.code == 400) {
                    onLoading('close', 'body');
                    $('.btn-to-top button').click();
                    showValidation(response.error);
                } else {
                    swalInit.fire({
                        title: 'Oops ...',
                        text: response.message,
                        icon: 'info',
                        showCloseButton: true
                    });
                }
            },
            error: function(response) {
                onLoading('close', 'body');

                swalInit.fire({
                    html: '<b>' + response.responseJSON.exception + '</b><br>' + response.responseJSON.message,
                    icon: 'error',
                    showCloseButton: true
                });
            }
        });
    }
</script>
