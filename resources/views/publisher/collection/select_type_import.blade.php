<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <h3 class="content-header-title mb-1 d-inline-block">Tambah Data Koleksi</h3><br>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('publisher/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="#">Koleksi</a></li>
                            <li class="breadcrumb-item active">Tambah Data</li>
                        </ol>

                    </div>
                </div>
            </div>
            <div class="content-header-right col-md-3 col-12 mb-2 mt-1">
              <a type="button" class="btn btn-info rounded-circle" href="{{ url('main/panduan-bulk.pdf') }} "><i class="la la-question"></i></a>
            </div>
        </div>
        <div class="content-body">
            <section id="configuration">
                <div class="row" id="select_type">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Pilih Tipe Koleksi</h4>
                              </div>
                            <div class="card-content collapse show">
                                <div class="card-body card-dashboard">
                                    <div class="row">
                                        <div class="col-md-4 text-center">
                                            <a href="{{ url('publisher/collection/import/1') }}" class="btn btn-primary btn-min-width mr-1 mb-1" ><span class="text-white">Buku</span></a>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <a href="{{ url('publisher/collection/import/2') }}" class="btn btn-primary btn-min-width mr-1 mb-1" ><span class="text-white">Partitur</span></a>
                                        </div>
                                        <div class="col-md-4  text-center">
                                            <a href="{{ url('publisher/collection/import/3') }}" class="btn btn-primary btn-min-width mr-1 mb-1" ><span class="text-white">Peta</span></a>
                                        </div>
                                        <div class="col-md-4  text-center">
                                            <a href="{{ url('publisher/collection/import/5') }}" class="btn btn-primary btn-min-width mr-1 mb-1" ><span class="text-white">Audio</span></a>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <a href="{{ url('publisher/collection/import/6') }}" class="btn btn-primary btn-min-width mr-1 mb-1" ><span class="text-white">Video</span></a>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <a href="#" onclick="selectDataSerial()" class="btn btn-primary btn-min-width mr-1 mb-1" ><span class="text-white">Serial</span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" id="type_serial" style="display:none;">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Pilih Data Serial</h4>
                              </div>
                            <div class="card-content collapse show">
                                <div class="card-body card-dashboard">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                              <div class="col-md-12 m-auto text-center">
                                                <button class="btn btn-primary btn-min-width mr-1 mb-1 select_type_serial" onclick="showFormSerial()" type="button" value="issn">Buat Metadata Baru</button>
                                              </div>
                                              <!-- <div class="col-md-12 m-auto text-center">
                                                <button class="btn btn-primary btn-min-width mr-1 mb-1 select_type_serial" type="button" value="non-issn">Buat </button>
                                              </div> -->
                                            </div>

                                            <h4 class="card-title">Daftar Koleksi Serial</h4>
                                            <table class="table table-bordered" id="datatable_serverside_serial">
                                              <thead class="text-center">
                                                <tr>
                                                  <th>#</th>
                                                  <th width="20%">Deposit</th>
                                                  <th width="20%">ISSN</th>
                                                  <th>Judul</th>
                                                  <th>Action</th>
                                                </tr>
                                              </thead>
                                            </table>

                                          </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" id="form_detail_serial" style="display:none;">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Tambah Metadata</h4>
                            </div>
                            <div class="card-content collapse show">
                              <div class="card-body">
                                <div class="form-group">
                                    <div class="alert alert-danger" id="validasi_element" style="display:none;">
                                        <ul id="validasi_content"></ul>
                                    </div>
                                </div>
                                <form id="form_data" class="steps-validation wizard-circle">
                                  <h6>Detail Publikasi</h6>
                                  <fieldset>
                                    <div class="col-md-12" id="from_detail">

                                      <ul class="nav nav-tabs nav-justified">
                                          <li class="nav-item">
                                              <a class="nav-link active" data-toggle="tab" aria-controls="tab_general" href="#tab_general" aria-expanded="true">General</a>
                                          </li>
                                          <li class="nav-item">
                                              <a class="nav-link" data-toggle="tab" aria-controls="tab_publisher" href="#tab_publisher" aria-expanded="false">Publisher</a>
                                          </li>
                                          <li class="nav-item">
                                              <a class="nav-link" data-toggle="tab" aria-controls="tab_cover" href="#tab_cover" aria-expanded="false">Cover</a>
                                          </li>
                                      </ul>
                                      <div class="tab-content px-1 pt-1">
                                        <div role="tabpanel" class="tab-pane active" id="tab_general" aria-expanded="true">

                                            <div id="form_detail_serial">
                                                <input type="hidden" class="form-control" name="type" value="4">
                                                <div class="form-group">
                                                    <label>Judul :</label>
                                                    <textarea name="title_serial" id="title_serial" class="form-control" placeholder="Masukan judul"></textarea>
                                                </div>
                                                <div class="form-group" id="form_serial">
                                                    <label>ISSN :</label>
                                                    <input type="text" class="form-control" name="code_serial" id="code_serial" placeholder="Masukan kode ISSN">
                                                </div>
                                                <div id="form-contributor_serial">

                                                </div>
                                                <div class="row">
                                                  <div class="col-md-3">
                                                      <button type="button" onclick="addContributor('#form-contributor_serial')" class="btn btn-success col-12">Tambah Kontributor</button>
                                                  </div>
                                                </div>
                                                <br/>
                                                <div class="form-group">
                                                  <label>Bulan Terbit Pertama Kali:</label>
                                                  <select name="publication_month_serial" id="publication_month_serial" class="form-control">
                                                      <option value="">-- Pilih --</option>
                                                      <option value="01">{{ App\Helper\GeneralHelper::getMonth('01') }}</option>
                                                      <option value="02">{{ App\Helper\GeneralHelper::getMonth('02') }}</option>
                                                      <option value="03">{{ App\Helper\GeneralHelper::getMonth('03') }}</option>
                                                      <option value="04">{{ App\Helper\GeneralHelper::getMonth('04') }}</option>
                                                      <option value="05">{{ App\Helper\GeneralHelper::getMonth('05') }}</option>
                                                      <option value="06">{{ App\Helper\GeneralHelper::getMonth('06') }}</option>
                                                      <option value="07">{{ App\Helper\GeneralHelper::getMonth('07') }}</option>
                                                      <option value="08">{{ App\Helper\GeneralHelper::getMonth('08') }}</option>
                                                      <option value="09">{{ App\Helper\GeneralHelper::getMonth('09') }}</option>
                                                      <option value="10">{{ App\Helper\GeneralHelper::getMonth('10') }}</option>
                                                      <option value="11">{{ App\Helper\GeneralHelper::getMonth('11') }}</option>
                                                      <option value="12">{{ App\Helper\GeneralHelper::getMonth('12') }}</option>
                                                  </select>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Preview :</label>
                                                            <input type="text" name="preview_serial" id="preview_serial" class="form-control" placeholder="Ex: 1-3">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Tahun Terbit Pertama Kali :</label>
                                                            <input type="text" name="publication_year_serial" id="publication_year_serial" class="form-control" placeholder="Masukan tahun terbit">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>DDC :</label>
                                                            <input type="text" name="ddc_serial" id="ddc_serial" class="form-control" placeholder="Masukan DDC">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Serial :</label>
                                                            <select name="serial" id="serial" class="form-control">
                                                                <option value="">-- Pilih Serial --</option>
                                                                <option value="1">Harian</option>
                                                                <option value="2">Mingguan</option>
                                                                <option value="3">Bulanan</option>
                                                                <option value="4">3 Bulan Sekali</option>
                                                                <option value="5">4 Bulan Sekali</option>
                                                                <option value="6">6 Bulan Sekali</option>
                                                                <option value="7">Tahunan</option>
                                                                <option value="8">2 Tahun Sekali</option>
                                                                <option value="9">3 Tahun Sekali</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Keterangan : (Minimal 200 Karakter)</label>
                                                    <textarea name="description_serial" id="description_serial" class="form-control" style="resize:none;" placeholder="Masukan informasi lain"></textarea>
                                                </div>
                                                <h4>Kategori</h4>
                                                <div id="category_music">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab_publisher">
                                          <div class="form-group">
                                              <label>Nama Pelaksana</label>
                                              <input type="text" name="publisher_name" id="publisher_name" class="form-control required" placeholder="Nama Pelaksana" value="{{ $data['publisher']->name }}" readonly="">
                                          </div>
                                          <div class="form-group">
                                              <label>Alamat Pelaksana</label>
                                              <input type="text" name="publisher_address" id="publisher_address" class="form-control required" placeholder="Alamat Pelaksana" value="{{ $data['publisher']->address }}">
                                          </div>
                                          <div class="form-group">
                                              <label>Provinsi</label>
                                              <select name="publisher_province" id="publisher_province" class="form-control required" style="width:100%;"></select>
                                          </div>
                                          <div class="form-group">
                                              <label>Kota/Kab</label>
                                              <select name="publisher_city" id="publisher_city" class="form-control required" style="width:100%;"></select>
                                          </div>
                                          <div class="form-group">
                                              <label>Kecamatan</label>

                                              <select name="publisher_district" id="publisher_district" class="form-control required" style="width:100%;"></select>
                                          </div>
                                          <div class="form-group">
                                              <label>Kelurahan</label>
                                              <select name="publisher_village" id="publisher_village" class="form-control required" style="width:100%;"></select>
                                          </div>
                                        </div>
                                        <div class="tab-pane" id="tab_cover">
                                        </div>
                                      </div>
                                    </div>
                                  </fieldset>
                                  <!-- Step 4 -->
                                  <h6>Hak Akses</h6>
                                  <fieldset>
                                    <div class="row">
                                      <div class="alert alert-success mb-2 w-100 align-middle" role="alert">
                                        <fieldset class="radio">
                                          <label>
                                            <input type="radio"  name="access" value="1"> Akses full file watermak secara online
                                          </label>
                                        </fieldset>
                                      </div>
                                      <div class="alert alert-success mb-2 w-100 align-middle" role="alert">
                                        <fieldset class="radio">
                                          <label>
                                            <input type="radio"  name="access" value="2" checked> Akses hanya preview file secara online, namun tetap dapat di dayagunakan di lingkungan perpustakaan nasional RI dengan jaringan internet LAN
                                          </label>
                                        </fieldset>
                                      </div>
                                      <div class="alert alert-success mb-2 w-100 align-middle" role="alert">
                                        <fieldset class="radio">
                                          <label>
                                            <input type="radio"  name="access" value="3"> Akses hanya preview file secara online, dan tidak dilayankan di Perpusnas RI selama 5 tahun sejak di serahkan. Setelah periode habis akan dapat dilayankan oleh perpusnas.
                                          </label>
                                        </fieldset>
                                      </div>
                                      <div class="alert alert-success mb-2 w-100 align-middle" role="alert">
                                        <fieldset class="radio">
                                          <label>
                                            <input type="radio"  name="access" value="4"> Akses hanya file preview secara online selamanya dan tidak didayagunakan di mana pun.
                                          </label>
                                        </fieldset>
                                      </div>
                                    </div>
                                  </fieldset>
                                  <!-- Step 4 -->
                                  <h6>Review dan Submit</h6>
                                  <fieldset>
                                    <h4>Review Penyerahan Koleksi</h4>
                                    <div class="row">
                                      <table class="table table-bordered table-striped">
                                        <tbody id="review-body">

                                        </tbody>
                                      </table>
                                      <div class="alert alert-success mb-2 w-100 align-middle" role="alert">
                                        <fieldset class="checkbox">
                                          <label>
                                            <input type="checkbox"  name="agree-terms" value="1"> saya menyetujui syarat dan ketentuan berlaku
                                          </label>
                                        </fieldset>
                                      </div>
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
        @include('publisher.collection.list_job')
    </div>
</div>

<script type="text/javascript">

    function selectDataSerial() {
        $('#select_type').hide();
        $('#type_serial').fadeIn(500);
        loadDataTableSerial()
    }

    function loadDataTableSerial() {
        tableSerial = $('#datatable_serverside_serial').DataTable({
          ajax: {
                url: '{{ url("publisher/collection/serial") }}',
                type: 'POST',
                dataType: 'JSON',
                async : false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
          },
          processing: true,
          serverSide: true,
          scrollX: true,
          lengthMenu: [10, 25, 50, 75, 100],
          columns: [
            {
              name : 'no',
              searchable: false
            },
            {
              name :'deposit',
              searchable: false
            },
            {
              name :'code',
            },
            {
              name : 'title',
            },
            {
              name : 'action',
              searchable: false
            }
          ]
        });
    }

    function selectSerial(id) {
        window.location.href = "{{ url('publisher/collection/import/4') }}?id=" + id
    }

    function initReview() {

        let container = $('#review-body')
        $('.review-value').remove()


        let type = $("input[name='type']:checked").val();
        let typeElement = 'serial'

        container.append('<tr class="review-value"><td>Judul</td><td>'+$('#title_' + typeElement).val()+'</td></tr>')

        container.append('<tr class="review-value"><td>Deskripsi</td><td>'+$('#description_' + typeElement).val()+'</td></tr>')

        container.append('<tr class="review-value"><td>Publisher</td><td>'+$('#publisher_name').val()+'</td></tr>')

        var kontributor = [];

        $('input[name^="author_fullname_field"]').each( function( key, value ) {
          console.log($(this).val())
          kontributor.push($(this).val())
        })

        container.append('<tr class="review-value"><td>Kontributor</td><td>'+kontributor.join(",")+'</td></tr>')

        var category = [];
        $('input[name^="category"]').each( function( key, value ) {
          if($(this).filter(":checked").val()) {
            category.push($(this).attr("data-name"))
          }
        })

        container.append('<tr class="review-value"><td>Kategori</td><td>'+category.join(",")+'</td></tr>')

        let access = $("input[name='access']:checked").val();
        var desc = '';

        if(access == 1) {
          desc = 'Akses full file watermak secara online';
        } else if(access == 2) {
          desc = 'Akses hanya preview file secara online, namun tetap dapat di dayagunakan di lingkungan perpustakaan nasional RI dengan jaringan internet LAN';
        } else if(access == 3) {
          desc = 'Akses hanya preview file secara online, dan tidak dilayankan di Perpusnas RI selama 1 tahun sejak di serahkan. Setelah periode habis akan dapat dilayankan oleh perpusnas.';
        } else if(access == 4) {
          desc = 'Akses hanya file preview secara online selamanya dan tidak didayagunakan di mana pun.'
        }
        container.append('<tr class="review-value"><td>Hak Akses</td><td>'+desc+'</td></tr>')


    }

    var form = $(".steps-validation").show();

    $(".steps-validation").steps({
        headerTag: "h6",
        bodyTag: "fieldset",
        transitionEffect: "fade",
        titleTemplate: '<span class="step">#index#</span> #title#',
        labels: {
            finish: 'Submit'
        },
        onStepChanging: function (event, currentIndex, newIndex)
        {
            console.log(currentIndex)

            $('#type-collection-error').hide()
            $('#validasi_element').hide();
            $('#validasi_content').html('');

            if(currentIndex == 0) {
              var valid = true;

              if($('#title_serial').val() == "") {
                $('#validasi_content').append('<li>Judul wajib diisi!</li>');
                valid = false;
              }

              if($('#publication_year_serial').val() == "") {
                $('#validasi_content').append('<li>Tahun Terbit wajib diisi!</li>');
                valid = false;
              }

              if($('#publication_month_serial').val() == "") {
                $('#validasi_content').append('<li>Bulan Terbit wajib diisi!</li>');
                valid = false;
              }

              if($('#ddc_serial').val() == "") {
                $('#validasi_content').append('<li>DDC wajib diisi!</li>');
                valid = false;
              }

              if($('#description_serial').val() == "") {
                $('#validasi_content').append('<li>Deskripsi wajib diisi!</li>');
                valid = false;
              } else if($('#description_serial').val().length < 200) {
                $('#validasi_content').append('<li>Deskripsi kurang 200 karakter</li>');
                valid = false;
              }

              if($('#publisher_address').val() == "") {
                $('#validasi_content').append('<li>Alamat Publisher wajib diisi!</li>');
                valid = false;
              }

              if($('#publisher_province').val() == '') {
                $('#validasi_content').append('<li>Provinsi Publisher wajib diisi!</li>');
                valid = false;
              }

              if($('#publisher_city').val() == '') {
                $('#validasi_content').append('<li>Kota Publisher wajib diisi!</li>');
                valid = false;
              }

              if($('#publisher_district').val() == '') {
                $('#validasi_content').append('<li>Kecamatan Publisher wajib diisi!</li>');
                valid = false;
              }

              if($('#publisher_village').val() == '') {
                $('#validasi_content').append('<li>Desa Publisher wajib diisi!</li>');
                valid = false;
              }

              var x = 0;
              var falseContributor = 0;
              for (x = 0; x < countContributor; x++) {
                  if($('#contributor_id_field_' + x).val() == '') {
                    falseContributor++;
                    valid = false;
                  }

                  if($('#author_fullname_field_' + x).val() == '') {
                    falseContributor++;
                    valid = false;
                  }

                  if($('#author_title_field_' + x).val() == '') {
                    falseContributor++;
                    valid = false;
                  }
              }
              if(falseContributor > 0) {
                $('#validasi_content').append('<li>Mohon lengkapi data kontributor!</li>');
              }

              var i = 0;
              var falseCover = 0;
              for (i = 1; i <= countFile; i++) {
                  if($('#file_cover_' + i).val() == "") {
                    valid = false;
                    falseCover++;
                  }
              }

              if(falseCover > 0) {
                $('#validasi_content').append('<li>Mohon isi data cover!</li>');
              }

              if(!valid) {
                Swal.fire({
                      position: 'center',
                      icon: 'warning',
                      title: 'Harap mengisi semua data',
                      showConfirmButton: true
                  });
                $('#validasi_element').show();
              }

              return valid;
            }

            if(currentIndex == 1) {
              if ($("input[name='access']:checked").val() == undefined)
              {
                  Swal.fire({
                      position: 'center',
                      icon: 'warning',
                      title: 'Harap memilih hak Akses',
                      showConfirmButton: true
                  });
                  return false;
              }
            }

            return true;
        },
        onStepChanged: function (event, currentIndex, priorIndex) {
          if(currentIndex == 2) {
            initReview()
          }
        },
        onFinishing: function (event, currentIndex)
        {
            if ($("input[name='agree-terms']:checked").val() == undefined)
            {
                Swal.fire({
                    position: 'center',
                    icon: 'warning',
                    title: 'Mohon check syarat dan ketentuan yang berlaku',
                    showConfirmButton: true
                });
                return false;
            } else {
              create();
            }
        }
    });

    var countContributor = 0;
    var countFile = 0;

    function showFormSerial() {
        countFile = 1;
        countContributor = 0;

        $('.category-checkbox').remove()
        $('.contributor').remove()

        let type = $("input[name='type']:checked").val();
        let item = ""

        let inputFileCover = '<div class="file-upload"><div class="alert alert-warning"><small>Jenis File Yang di Dukung <b>: JPG, JPEG, PNG</b><br>Maksimal Ukuran File <b>: 2 MB</b></small></div><div class="form-group"><label>Cover '+item+' : <span class="danger">*</span></label><input type="file" name="file_upload[cover]['+item.trim()+'][]" class="form-control " accept=".jpg,.png" id="file_cover_'+countFile+'"></div></div>'

        $('#tab_cover').append(inputFileCover)
        $('#content_serial').show()

        // $('#form-contributor').append(fromContributor(countContributor));
        addContributor('#form-contributor_serial')

        getCategoryByType($('#category_serial'))
        select2LoadAll('#contributor_id_field_' + countContributor, 'load_contributor/' + $("input[name='type']:checked").val());
        select2Author('#author_id_field_' + countContributor, 'load_author', countContributor);

        initializePublisher()

        $('#type_serial').hide();
        $('#form_detail_serial').fadeIn(500);

    }

    function initializePublisher() {

        select2Nested('#publisher_province', 'load_province', '');
        select2Nested('#publisher_city', 'load_city', $('#publisher_province'));
        select2Nested('#publisher_district', 'load_district', $('#publisher_city'));
        select2Nested('#publisher_village', 'load_village', $('#publisher_district'));

        var provinceId = "{{ $data['publisher']->province_id }}"

        if(provinceId != "") {
          var province = {
              id: "{{ $data['publisher']->province_id }}",
              text: "{{ $data['publisher']->province != '' ? $data['publisher']->province->name : '' }}"
          }

          var newOption = new Option(province.text, province.id, false, false);
          $('#publisher_province').append(newOption).trigger('change');
        }

        var cityId = "{{ $data['publisher']->city_id }}"

        if(cityId != "") {
          var city = {
              id: "{{ $data['publisher']->city_id }}",
              text: "{{ $data['publisher']->city != '' ? $data['publisher']->city->name : '' }}"
          }

          var newOption = new Option(city.text, city.id, false, false);
          $('#publisher_city').append(newOption).trigger('change');
        }

        var districtId = "{{ $data['publisher']->district_id }}"

        if(districtId != "") {
          var district = {
              id: "{{ $data['publisher']->district_id }}",
              text: "{{ $data['publisher']->district != '' ? $data['publisher']->district->name : '' }}"
          }

          var newOption = new Option(district.text, district.id, false, false);
          $('#publisher_district').append(newOption).trigger('change');
        }

        var villageId = "{{ $data['publisher']->village_id }}"

        if(villageId != "") {
          var village = {
              id: "{{ $data['publisher']->village_id }}",
              text: "{{ $data['publisher']->village != '' ? $data['publisher']->village->name : '' }}"
          }

          var newOption = new Option(village.text, village.id, false, false);
          $('#publisher_village').append(newOption).trigger('change');
        }
      }

    function getCategoryByType(container) {
        $.ajax({
          url: '{{ url("publisher/select2_serverside/load_category") }}' + '/4',
          type: 'POST',
          dataType: 'JSON',headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function(response) {
            response.items.forEach(function(item, index) {

              let html = '<div class="col-md-3 custom-control custom-checkbox mr-1 category-checkbox"><input type="checkbox" id="checkbox-'+item.id+'" class="custom-control-input" name="category[]" data-name="'+item.text+'" value="'+item.id+'"><label class="custom-control-label" for="checkbox-'+item.id+'">'+item.text+'</label></div>'

              container.append(html);
            })
          }
        })

    }

    function getSubjectByType(container) {
        $.ajax({
          url: '{{ url("publisher/select2_serverside/load_subject") }}',
          type: 'POST',
          dataType: 'JSON',
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function(response) {
            response.items.forEach(function(item, index) {

              let html = '<div class="d-inline-block custom-control custom-checkbox mr-1 category-checkbox"><input type="checkbox" id="checkbox-'+item.id+'" class="custom-control-input" name="subject[]" value="'+item.id+'"><label class="custom-control-label" for="checkbox-'+item.id+'">'+item.name+'</label></div>'

              container.append(html);
            })
          }
        })

    }

    function addContributor(element) {

        $(element).append(fromContributor(countContributor))

        $('#remove_field_contributor_' + countContributor).click(function() {
          countContributor--;
          let parent = $(this).closest('.contributor').remove();
        })

        select2LoadContributor('#contributor_id_field_' + countContributor, 'load_contributor/4', countContributor);
        select2Author('#author_id_field_' + countContributor, 'load_author', countContributor);
        countContributor++;

      }

    function fromContributor(count) {
          let html = '<div class="row contributor"><div class="col-md-3"><div class="form-group"><label>Role Kontributor : </label><input type="hidden" name="contributor_name_field[]" id="contributor_name_field_'+count+'" /><select name="contributor_id_field[]" id="contributor_id_field_'+count+'" class="form-control" style="width:100%;"></select><p>ex: Penulis, Penyanyi, Pengisi Suara, dll</p></div></div><div class="col-md-3"><div class="form-group"><label>Nama Lengkap:</label><input type="hidden" name="author_fullname_field[]" id="author_fullname_field_'+count+'" /><select name="author_id_field[]" id="author_id_field_'+count+'" class="form-control" style="width:100%;"></select></div></div><div class="col-md-2"><div class="form-group"><label>Titel :</label><input type="text" name="author_title_field[]" id="author_title_field_'+count+'" class="form-control" placeholder="Titel"><p>ex: Ir.,S.komp., S.pd, dll</p></div></div><div class="col-md-2"><div class="form-group"><label>Tahun Kelahiran :</label><input type="number" name="author_year_of_birth_field[]" id="author_year_of_birth_field_'+count+'" class="form-control" placeholder="Tahun kelahiran"></div></div><div class="col-md-2"><div class="form-group"><label>Tahun Kematian :</label><input type="number" name="author_year_of_death_field[]" id="author_year_of_death_field_'+count+'" class="form-control" placeholder="Tahun kematian"></div></div>';

          if(count > 0) {
            html += '<div class="col-md-1"><div class="form-group"><label>Hapus</label><button type="button" class="btn btn-icon btn-secondary mr-1" id="remove_field_contributor_'+count+'"><i class="la la-trash"></i></button></div></div>';
          }

          html += '</div>';

          return html
    }

    function select2Author(selector, endpoint, count) {
          $(selector).select2({
              placeholder: '-- Pilih --',
              minimumInputLength: 3,
              allowClear: true,
              tags: true,
              cache: true,
              ajax: {
                  url: '{{ url("publisher/select2_serverside") }}' + '/' + endpoint,
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
              templateSelection: function (data, container) {
                $('#author_fullname_field_' + count).val(data.text)
                $('#author_title_field_' + count).val(data.title)
                $('#author_year_of_birth_field_' + count).val(data.yob)
                $('#author_year_of_death_field_' + count).val(data.yod)

                return data.text;
              },
              createTag: function (params) {
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

      function select2LoadContributor(selector, endpoint, count) {
            $(selector).select2({
                placeholder: '-- Pilih --',
                allowClear: true,
                cache: true,
                dropdowntParent: $('#modal_element'),
                ajax: {
                    url: '{{ url("publisher/select2_serverside") }}' + '/' + endpoint,
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
                    },
                    templateSelection: function (data) {
                      console.log('templateSelection.data: ', data);
                      $('#contributor_name_field_' + count).val(data.text)
                      return data.text;
                    }
                }
            });

            $(selector).on("select2:select", function (e) {
              $('#contributor_name_field_' + count).val(e.params.data.text)
            });
        }

        function create() {
      var formData = new FormData($('#form_data')[0]);

      if($("input[name='type']:checked").val() == 6) {
        //video
        formData.append('preview_video', sliderVideo.noUiSlider.get());

      } else if($("input[name='type']:checked").val() == 5) {
        formData.append('preview_music', sliderMusic.noUiSlider.get());
        //audio
      }

      $.ajax({
          url: '{{ url("publisher/collection/create_manual") }}',
          type: 'POST',
          dataType: 'JSON',
          data: formData,
          cache: false,
          contentType: false,
          processData: false,
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          xhr: function() {
            var xhr = new window.XMLHttpRequest();

            xhr.upload.addEventListener("progress", function(evt) {
              if (evt.lengthComputable) {
                var percentComplete = evt.loaded / evt.total;
                percentComplete = parseInt(percentComplete * 100);

                $('#progressValueUpload').empty();
                $('#percentComplete').remove();
                $('#progressUpload').append('<span id="percentComplete">'+percentComplete + '%</span>');
                $('#progressValueUpload').attr('aria-valuenow', percentComplete);
                $('#progressValueUpload').css('width', '' + percentComplete + '%');

                if (percentComplete === 100) {

                }

              }
            }, false);

            return xhr;
          },
          beforeSend: function() {
              loadingOpen('#configuration');
              $('.waitMe_content').append('<br/><div id="progressUpload" class="progress"><div id="progressValueUpload" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div></div>')
              $('#validasi_element').hide();
              $('#validasi_content').html('');
          },
          success: function(response) {
              loadingClose('#configuration');
              $('#progressUpload').remove();
              if(response.status == 200) {
                  Toast.fire({
                      icon: 'success',
                      title: response.message
                  });
                  window.location.href = "{{ url('publisher/collection/import/4') }}?id=" + response.id
              } else if(response.status == 422) {
                  $('#validasi_element').show();

                  document.body.scrollTop            = 0;
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
          error: function(e) {
            loadingClose('#configuration');
            $('#progressUpload').remove();
            Toast.fire({
                icon: 'error',
                title: 'Server Error!'
            });
          }
      });
  }

</script>

