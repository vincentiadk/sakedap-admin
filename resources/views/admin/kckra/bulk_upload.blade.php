<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <h3 class="content-header-title mb-1 d-inline-block">Bulk Upload KCKRA</h3><br>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Bulk Upload KCKRA</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-header-right col-md-6 col-12 mb-2 mt-1">
                <div class="float-md-right">
                    <div class="form-group">
                        <div class="btn-group">
                            <button type="button" class="btn btn-success" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false"><i class="la la-download"></i> Download Template</button>
                            <div class="dropdown-menu arrow">
                                <a href="{{ url('admin/collection/kckra/bulk_upload/download_template?type=serial') }}"
                                    class="dropdown-item">Bulk Serial</a>
                                <a href="{{ url('admin/collection/kckra/bulk_upload/download_template?type=non_serial') }}"
                                    class="dropdown-item">Bulk Non Serial</a>
                            </div>
                        </div>
                        <a href="{{ url('admin/collection/kckra/bulk_upload/progress') }}" class="btn btn-primary"><i
                                class="la la-eye"></i> Lihat Progress</a>
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
                                    <div class="alert alert-danger" id="validasi_element" style="display:none;">
                                        <ul id="validasi_content"></ul>
                                    </div>
                                    <form action="" id="form_data" class="steps-validation wizard-notification">
                                        <h6>Tipe Bulk Upload KCKRA</h6>
                                        <fieldset>
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="alert round bg-secondary alert-dismissible"
                                                                role="alert">
                                                                <h5
                                                                    class="font-weight-bold text-white text-center text-uppercase">
                                                                    Serial</h5>
                                                            </div>
                                                            <center>
                                                                <fieldset>
                                                                    <input type="radio" name="flag" id="flag"
                                                                        value="serial" checked>
                                                                </fieldset>
                                                            </center>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="alert round bg-secondary alert-dismissible"
                                                                role="alert">
                                                                <h5
                                                                    class="font-weight-bold text-white text-center text-uppercase">
                                                                    Non Serial</h5>
                                                            </div>
                                                            <center>
                                                                <fieldset>
                                                                    <input type="radio" name="flag" id="flag"
                                                                        value="non_serial">
                                                                </fieldset>
                                                            </center>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <h6>Form</h6>
                                        <fieldset>
                                            <div class="card">
                                                <div class="card-body">
                                                    <div id="step2_serial" style="display:none;">
                                                        <label>Tipe :</label>
                                                        <select onchange="datatableSerial(this.value)"
                                                            name="type_serial" id="type_serial" class="custom-select">
                                                            <option value="">-- Pilih Tipe Koleksi --</option>
                                                            @foreach ($deposit_head as $item)
                                                                @if ($item->is_serial == '1')
                                                                    <option value="{{ $item->id }}">
                                                                        {{ $item->shape }}
                                                                    </option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                        <br>
                                                        <table style="display: none"
                                                            class="table table-bordered table-striped"
                                                            id="datatable_serial">
                                                            <thead>
                                                                <tr class="text-center">
                                                                    <th>No</th>
                                                                    <th>Judul</th>
                                                                    <th>Penerbit</th>
                                                                    <th>Pilih</th>
                                                                </tr>
                                                            </thead>
                                                        </table>
                                                    </div>
                                                    <div id="step2_non_serial" style="display:none;">
                                                        <div class="form-group">
                                                            <label>Tipe :</label>
                                                            <select name="type_non_serial" id="type_non_serial"
                                                                class="custom-select">
                                                                <option value="">-- Pilih Tipe Koleksi --
                                                                </option>
                                                                @foreach ($deposit_head as $item)
                                                                    @if ($item->is_serial == '0')
                                                                        <option value="{{ $item->id }}">
                                                                            {{ $item->shape }}
                                                                        </option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Penerbit :</label>
                                                            <select name="publisher_id" id="publisher_id"
                                                                class="form-control" style="width:100%;"
                                                                required></select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <h6>File Upload</h6>
                                        <fieldset>
                                            <div class="form-group">
                                                <input type="file" class="file-upload form-control-lg"
                                                    name="file_upload" id="file_upload" data-theme="fa5">
                                            </div>
                                        </fieldset>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('a[href="#previous"]').hide();
        select2AutoSuggest('#publisher_id', 'load_publisher');

        $('input[type="radio"]').iCheck({
            checkboxClass: 'icheckbox_square-green',
            radioClass: 'iradio_square-green',
            increaseArea: '20%'
        });

        dragFile('.file-upload', ['zip']);
    });

    function datatableSerial(type) {
        if (type !== '') {
            $("#datatable_serial").show();
            $('#datatable_serial').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                scrollX: true,
                order: [
                    [0, 'desc']
                ],
                iDisplayInLength: 10,
                ajax: {
                    url: '{{ url('admin/collection/kckra/bulk_upload/datatable_serial') }}' + '/' + type
                },
                columns: [{
                        name: 'id',
                        searchable: false,
                        className: 'align-middle text-center'
                    },
                    {
                        name: 'title',
                        className: 'align-middle text-center'
                    },
                    {
                        name: 'publisher_id',
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
        } else {
            $("#datatable_serial").hide();
            $('#datatable_serial').DataTable().clear().draw();
        }
    }

    var form = $('.steps-validation').show();
    $('.steps-validation').steps({
            headerTag: 'h6',
            bodyTag: 'fieldset',
            transitionEffect: 'fade',
            titleTemplate: '<span class="step">#index#</span> #title#',
            labels: {
                finish: 'Submit'
            },
            onStepChanging: function(e, t, i) {
                var flag = $('#flag:checked').val();
                console.log(flag, 'flag');
                $('a[href="#previous"]').show();

                if (i == 0) {
                    $('a[href="#previous"]').hide();
                } else if (i == 1) {
                    if (flag == 'serial') {
                        $('#step2_non_serial').hide();
                        $('#step2_serial').show();
                    } else {
                        $('#step2_non_serial').show();
                        $('#step2_serial').hide();
                    }
                }

                return t > i || !(3 === i && Number($('#age-2').val()) < 18) && (t < i && (form.find(
                            '.body:eq("+i+") label.error').remove(), form.find('.body:eq("+i+") .error')
                        .removeClass('error')), form.validate().settings.ignore = ':disabled,:hidden', form
                    .valid());
            },
            onFinishing: function(e, t) {
                $.ajax({
                    url: '{{ url('admin/collection/kckra/bulk_upload/action_upload') }}',
                    type: 'POST',
                    dataType: 'JSON',
                    data: new FormData($('#form_data')[0]),
                    cache: false,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        loadingOpen('#configuration');
                        $('#validasi_element').hide();
                        $('#validasi_content').html('');
                    },
                    success: function(response) {
                        loadingClose('#configuration');
                        if (response.status == 200) {
                            Swal.fire({
                                title: 'Berhasil Upload',
                                text: 'File telah diproses',
                                icon: 'success',
                                showCancelButton: true,
                                allowOutsideClick: false,
                                confirmButtonColor: '#28d094',
                                cancelButtonColor: '#3085d6',
                                confirmButtonText: 'Upload Lagi',
                                cancelButtonText: 'Lihat Progress',
                                confirmButtonClass: 'btn btn-success',
                                cancelButtonClass: 'btn btn-primary ml-1',
                                buttonsStyling: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                } else {
                                    window.location.href =
                                        '{{ url('admin/collection/kckra/bulk_upload/progress') }}';
                                }
                            });
                        } else if (response.status == 422) {
                            $('#validasi_element').show();

                            document.body.scrollTop = 0;
                            document.documentElement.scrollTop = 0;

                            Toast.fire({
                                icon: 'info',
                                title: 'Validasi'
                            });

                            $.each(response.error, function(i, val) {
                                $('#validasi_content').append('<li>' + val + '</li>');
                            });
                        } else {
                            Toast.fire({
                                icon: 'warning',
                                title: response.message
                            });
                        }
                    },
                    error: function() {
                        loadingClose('#configuration');
                        Toast.fire({
                            icon: 'error',
                            title: 'Server Error!'
                        });
                    }
                });
            },
            onFinished: function(e, t) {
                return true;
            }
        }),
        $('.steps-validation').validate({
            ignore: 'input[type=hidden]',
            errorClass: 'danger',
            successClass: 'success',
            highlight: function(e, t) {
                $(e).removeClass(t);
            },
            unhighlight: function(e, t) {
                $(e).removeClass(t);
            },
            errorPlacement: function(e, t) {
                e.insertAfter(t);
            },
            rules: {
                email: {
                    email: !0
                }
            }
        });
</script>
