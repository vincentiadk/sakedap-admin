<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <h3 class="content-header-title mb-1 d-inline-block">Pengiriman KCKRA</h3><br>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">KCKRA</a></li>
                            <li class="breadcrumb-item active">Pengiriman</li>
                        </ol>
                    </div>
                </div>
            </div>
            {{-- <div class="content-header-right col-md-6 col-12 mb-2 mt-1">
                <div class="float-md-right">
                    <a href="{{ url("admin/collection/kckra/create_manual/$type") }}" class="btn btn-primary">Tambah
                        Data Baru</a>
                    <a href="{{ url("admin/collection/kckra/manage/$type") }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div> --}}
        </div>
        <div class="content-body">
            <section id="configuration">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-content collapse show">
                                <div class="card-body card-dashboard">
                                    <form method="POST" enctype="multipart/form-data" class="form">
                                        @csrf
                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <ul>
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @elseif(session('failed'))
                                            <div class="alert bg-danger alert-icon-left alert-dismissible mb-2"
                                                role="alert">
                                                <span class="alert-icon"><i class="la la-check"></i></span>
                                                <button type="button" class="close" data-dismiss="alert"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                                <strong>Failed!</strong> {{ session('failed') }}
                                            </div>
                                        @endif

                                        <ul class="nav nav-tabs nav-underline no-hover-bg" id="pills-tab"
                                            role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link active" id="pills-input-tab" data-toggle="pill"
                                                    href="#pills-input" data-target="#pills-input" role="tab">Input
                                                    Data Pengiriman
                                                </a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link" id="pills-print-tab" data-toggle="pill"
                                                    href="#pills-print" data-target="#pills-print" role="tab">Cetak
                                                    Data Pengiriman
                                                </a>
                                            </li>

                                        </ul>
                                        <div class="tab-content mt-3" id="pills-tabContent">
                                            <div class="tab-pane fade show active" id="pills-input"
                                                aria-labelledby="pills-input-tab">
                                                <div class="row">
                                                    <div class="col-md-10">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" name="code"
                                                                id="code"
                                                                placeholder="Masukan kode barcode (ex: C23100400001)">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-warning col-12"
                                                            id="btn_check_code_deposit"
                                                            onclick="checkCodeDeposit()">Cari</button>
                                                    </div>
                                                </div>
                                                <div id="show_collection" style="display: none">
                                                    <div class="row justify-content-center">
                                                        <div class="col-md-6">
                                                            <div class="alert alert-success alert-icon-left alert-arrow-left alert-dismissible mb-2"
                                                                role="alert">
                                                                <span class="alert-icon"><i
                                                                        class="la la-info-circle"></i></span>
                                                                <table>
                                                                    <tr>
                                                                        <th style="padding-right: 20px;"> Tanda
                                                                            Registrasi Karya </th>
                                                                        <td id="mark"> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th style="padding-right: 20px;"> Kode Barcode
                                                                        </th>
                                                                        <td id="barcode"> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th style="padding-right: 20px;"> Judul </th>
                                                                        <td id="title"> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th style="padding-right: 20px;"> Penerbit </th>
                                                                        <td id="publisher_name"> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th style="padding-right: 20px;"> Tahun Terbit
                                                                        </th>
                                                                        <td id="publication_year"> </td>
                                                                    </tr>
                                                                    <tr style="display: none">
                                                                        <th style="padding-right: 20px;"> Copy </th>
                                                                        <td id="total_copy"> </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th style="padding-right: 20px;"> Jenis Koleksi
                                                                        </th>
                                                                        <td id="type_collection"> </td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <button class=" col-md-12 btn btn-md btn-success" type="button"
                                                            onclick="addShipping()">Tambahkan Koleksi</button>

                                                    </div>
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-md-12">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-striped"
                                                                id="datatable_shipping">
                                                                <thead class="text-center">
                                                                    <tr>
                                                                        <th>TRK</th>
                                                                        <th>Kode Barcode</th>
                                                                        <th>Judul</th>
                                                                        <th>Tahun Terbit</th>
                                                                        <th>Eksemplar</th>
                                                                        <th>Tgl Kirim</th>
                                                                        <th>Hapus</th>
                                                                    </tr>
                                                                </thead>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <hr>
                                                </div>
                                                <div class="form-group">
                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="text-right">
                                                                    <button type="button"
                                                                        onclick="insertCopyShipping()"
                                                                        class="btn btn-warning">Simpan
                                                                        Perubahan</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="pills-print"
                                                aria-labelledby="pills-print-tab">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group row">
                                                            <label class="col-md-2">Judul Cetak :</label>
                                                            <div class="col-md-10">
                                                                <textarea name="print_title" id="print_title" class="form-control">DAFTAR PENGIRIMAN BAHAN PUSTAKA () KE BAGIAN PENGOLAHAN TAHUN {{ date('Y') }} APLIKASI INLIS ENTERPRISE</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-md-2">Nama Koordinator :</label>
                                                            <div class="col-md-10">
                                                                <input type="text" name="print_coordinator"
                                                                    id="print_coordinator" class="form-control"
                                                                    value="Emyati Tangke Lembang, S.Sos."
                                                                    placeholder="Masukan Nama Koordinator"
                                                                    style="width:100%;">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-md-2">NIP Koordinator :</label>
                                                            <div class="col-md-10">
                                                                <input type="text" name="print_nip_coordinator"
                                                                    id="print_nip_coordinator" class="form-control"
                                                                    value="19650902 199103 2 001"
                                                                    placeholder="Masukan NIP Koordinator"
                                                                    style="width:100%;">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-md-2">Periode Tanggal :</label>
                                                            <div class="col-md-10">
                                                                <input type="text" name="print_period"
                                                                    id="print_period" class="form-control"
                                                                    style="width:100%;">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-md-2">Pengirim :</label>
                                                            <div class="col-md-10">
                                                                <select style="width: 100%" name="print_sender"
                                                                    id="print_sender" class="form-control">
                                                                    <option value=""> </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <button class="btn btn-success btn-md col-md-12" type="button"
                                                        onclick="loadDataTablePrint()">
                                                        Search
                                                    </button>
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-md-12">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-striped"
                                                                id="datatable_prints">
                                                                <thead class="text-center">
                                                                    <tr>
                                                                        <th>TRK</th>
                                                                        <th>Kode Barcode</th>
                                                                        <th>Judul</th>
                                                                        <th>Tahun Terbit</th>
                                                                        <th>Eksemplar</th>
                                                                        <th>Tgl Kirim</th>
                                                                        <th>Hapus</th>
                                                                    </tr>
                                                                </thead>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <hr>
                                                </div>
                                                <div class="form-group">
                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="text-right">
                                                                    <button type="button" onclick="printPdf()"
                                                                        class="btn btn-warning">Print PDF</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
        $('#datatable_shipping').DataTable({
            columns: [{
                    data: 'trk',
                    title: 'TRK'
                },
                {
                    data: 'code',
                    title: 'Kode Barcode'
                },
                {
                    data: 'judul',
                    title: 'Judul'
                },
                {
                    data: 'tahun_terbit',
                    title: 'Tahun Terbit',
                },
                {
                    data: 'eksemplar',
                    title: 'Eksemplar',
                },
                {
                    data: 'tgl_kirim',
                    title: 'Tgl Kirim',
                },
                {
                    data: 'hapus',
                    title: 'Hapus',
                },
                {
                    data: 'collection_id',
                    title: 'Collection ID',
                    visible: false
                },
            ],
        });

        updateStoredData('selected_copies', {}, null, null, 'delete');
        select2AutoSuggest('#print_sender', 'load_user');

        $('#print_period').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD',
                showDropdowns: true,
                minYear: 1901,
                maxYear: parseInt(moment().format('YYYY'), 10)
            }
        });
    });

    function resetAlert() {
        $('#mark').html('');
        $('#title').html('');
        $('#barcode').html('');
        $('#publisher_name').html('');
        $('#publication_year').html('');
        $('#total_copy').html('');
        $('#type_collection').html('');
        $("#show_collection").hide();
    }

    function checkCodeDeposit() {
        if ($('#code').val() != '') {
            $.ajax({
                url: '{{ url('admin/collection/kckra/check_code_deposit') }}',
                type: 'POST',
                data: {
                    code: $('#code').val()
                },
                dataType: 'JSON',
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
                    if (response.status == 201) {
                        window.location.href = response.data;
                    } else if (response.status == 200) {
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });

                        //reset alert first
                        resetAlert();

                        //fill alert
                        response = response.data;
                        $('#mark').html(response.mark_national + ' , ' + response.mark_province);
                        $('#title').html(response.title);
                        $('#publisher_name').html(response.publisher_name);
                        $('#publication_year').html(response.publication_year);
                        $('#total_copy').html(response.total_copy);
                        $('#barcode').html(response.code);
                        $('#type_collection').html(response.type_collection);



                        //show alert
                        console.log(response);
                        $("#show_collection").show();
                        @if ($library_id == '1')
                            updateStoredData('collection', response, null, null, 'replace');
                        @else
                            updateStoredData('collection', response, null, null, 'replace');
                        @endif
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: response.message,
                            showConfirmButton: true,
                            allowOutsideClick: true, // Allow dismissing by clicking outside the alert
                            allowEscapeKey: true // Allow dismissing by pressing the Escape key
                        });

                        $("#show_collection").hide();
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
        } else {
            Swal.fire({
                position: 'top-end',
                icon: 'warning',
                title: 'Harap mengisi kode',
                showConfirmButton: false,
                timer: 1500
            });
        }
    }

    function addShipping() {
        var collection = JSON.parse(sessionStorage.getItem('collection'));
        // console.log(collection.length);
        var message = false;
        if (Object.keys(collection).length > 0) {
            if (collection.total_copy > 0) {
                var valid_copies = true;
                var code = collection.code;
                @if ($library_id == '1')
                    var mark = collection.mark_national;
                @else
                    var mark = collection.mark_province;
                @endif

                var selected_copies = JSON.parse(sessionStorage.getItem('selected_copies'));
                if (Object.keys(selected_copies).length > 0) {
                    if (selected_copies.hasOwnProperty(code)) {
                        if (selected_copies[code] >= collection.total_copy) {
                            valid_copies = false;
                        }
                    }
                }

                if (valid_copies) {
                    $('#datatable_shipping').DataTable().row.add({
                        "trk": mark,
                        "code": code,
                        "judul": collection.title,
                        "tahun_terbit": collection.publication_year,
                        "eksemplar": 1,
                        "tgl_kirim": moment().format("YYYY-MM-DD"),
                        "hapus": `<button type="button" class="btn btn-danger btn-sm col-12" id="remove_row_shipping"><i class="la la-trash"></i></button>`,
                        "collection_id": collection.id
                    }).draw().node();

                    if (Object.keys(selected_copies).length > 0) {
                        if (selected_copies.hasOwnProperty(code)) {
                            updateStoredData('selected_copies', selected_copies[code] + 1, code, null, 'replace');
                        } else {
                            updateStoredData('selected_copies', 1, code, null, 'replace');
                        }
                    } else {
                        updateStoredData('selected_copies', 1, code, null, 'replace');
                    }

                } else {
                    message = 'Eksemplar yang dipilih melebihi total eksemplar yang tersedia!';
                }
            } else {
                message = 'Total Eksemplar (Copy) pada Koleksi Tersebut Kosong!';
            }
        } else {
            message = 'Mohon Cari Koleksi Terlebih Dahulu Agar Bisa Ditambahkan!';
        }

        if (message) {
            Swal.fire({
                icon: 'error',
                title: message,
                showConfirmButton: true,
                allowOutsideClick: true, // Allow dismissing by clicking outside the alert
                allowEscapeKey: true // Allow dismissing by pressing the Escape key
            });

            return false;
        }
    }

    function updateStoredData(key, data, parent_id = null, child_id = null, type = 'add') {
        //get initial data from sessionStorage
        // console.log(parent_id);
        var storedData = JSON.parse(sessionStorage.getItem(key));
        if (type == 'add') {
            //check if updated data is exist, if exist update.
            if (data !== null) {
                if (parent_id != null) {
                    if (child_id != null) {
                        if (!storedData.hasOwnProperty(parent_id)) {
                            storedData[parent_id] = [];
                        } else {
                            if (!storedData[parent_id].hasOwnProperty(child_id)) {
                                storedData[parent_id][child_id] = [];
                            }
                        }
                        storedData[parent_id][child_id] = data;
                    } else {
                        if (!storedData.hasOwnProperty(parent_id)) {
                            storedData[parent_id] = [];
                        }
                        storedData[parent_id].push(data);
                    }
                } else {
                    storedData.push(data);
                }
            }
        } else if (type == 'replace') {
            //replace data in session
            if (data !== null) {
                if (parent_id != null) {
                    if (child_id != null) {
                        storedData[parent_id][child_id] = data;
                    } else {
                        storedData[parent_id] = data;
                    }
                } else {
                    storedData = data;
                }
            }
        } else {
            //if updated data is not exist delete
            if (parent_id != null) {
                if (child_id != null) {
                    if (storedData.hasOwnProperty(parent_id)) {
                        if (storedData[parent_id].hasOwnProperty(child_id)) {
                            delete storedData[parent_id][child_id];
                        }
                    }
                } else {
                    if (storedData.hasOwnProperty(parent_id)) {
                        delete storedData[parent_id];
                    }
                }
            } else {
                storedData = {};
            }
        }

        // Store the updated array back into sessionStorage

        sessionStorage.setItem(key, JSON.stringify(storedData));
    }

    function insertCopyShipping() {
        var dataTables = $('#datatable_shipping').DataTable().data().toArray();
        // console.log(dataTables);
        $.ajax({
            url: '{{ url('admin/collection/kckra/shipping/create') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}', // Laravel CSRF token
                data: JSON.stringify(dataTables) // Your JSON data
            },
            beforeSend: function() {
                loadingOpen('#configuration');
            },
            success: function(response) {
                loadingClose('#configuration');
                Toast.fire({
                    icon: 'success',
                    title: response.message
                });
                //reset datatable
                resetAlert();
                $('#datatable_shipping').DataTable().clear().draw();
            },
            error: function() {
                loadingClose('#configuration');
                Toast.fire({
                    icon: 'error',
                    title: 'Server Error!'
                });
            }
        });
    }

    function loadDataTablePrint() {
        if ($('#print_period').val() != '' && $('#print_sender').val() != '') {
            $('#datatable_prints').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                scrollX: true,
                order: [
                    [5, 'asc']
                ],
                iDisplayInLength: 10,
                pagingType: 'input',
                ajax: {
                    url: '{{ url('admin/collection/kckra/shipping/datatable') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(d) {
                        // Add custom parameters to the data object
                        d.period = $('#print_period').val();
                        d.user_id = $('#print_sender').val();
                    },
                    dataSrc: function(json) {
                        if (json.recordsValid) {
                            $("#total_copy").val(json.recordsValid);
                        } else {
                            $("#total_copy").val(0);
                        }
                        return json.data;
                    }
                },
                columns: [{
                        data: 'trk',
                        title: 'TRK',
                        orderable: false,
                    },
                    {
                        data: 'code',
                        title: 'Kode Barcode',
                        orderable: false,
                    },
                    {
                        data: 'judul',
                        title: 'Judul',
                        orderable: false,
                    },
                    {
                        data: 'tahun_terbit',
                        title: 'Tahun Terbit',
                        orderable: false,
                    },
                    {
                        data: 'eksemplar',
                        title: 'Eksemplar',
                        orderable: false,
                    },
                    {
                        data: 'delivery_internal_date',
                        name: 'delivery_internal_date',
                        title: 'Tgl Kirim',
                    },
                    {
                        data: 'hapus',
                        title: 'Hapus',
                        orderable: false,
                    },
                    {
                        data: 'collection_id',
                        title: 'Collection ID',
                        orderable: false,
                        visible: false
                    },
                ],
            });
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Harap Pilih Periode dan Pengirim Terlebih dahulu!',
                showConfirmButton: false,
                timer: 1500
            });
        }
    }

    function printPdf() {
        var title = $("#print_title").val();
        var coordinator = $("#print_coordinator").val();
        var nip = $("#print_nip_coordinator").val();
        var period = $("#print_period").val();
        var sender = $("#print_sender").val();
        if (period != '' && sender != '') {
            window.open('{{ url('admin/collection/kckra/shipping/print') }}' + '?title=' + title + '&coordinator=' +
                coordinator + '&nip=' +
                nip + '&period=' + period + '&sender=' + sender, '_blank');
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Harap Pilih Periode dan Pengirim Terlebih dahulu!',
                showConfirmButton: false,
                timer: 1500
            });
        }
    }

    function destroyCopyDelivery(id) {
        $.ajax({
            url: '{{ url('admin/collection/kckra/shipping/karantina') }}' + '/' + id,
            type: 'DELETE',
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
                $('#datatable_prints').DataTable().ajax.reload(null, false);

                Toast.fire({
                    icon: 'success',
                    title: response.message
                });
            },
            error: function() {
                loadingClose('#configuration');
                Toast.fire({
                    icon: 'error',
                    title: 'Server Error!'
                });
            }
        });
    }
</script>
