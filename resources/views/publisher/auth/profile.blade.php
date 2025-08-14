<div class="app-content content">
  <div class="content-wrapper">
    <div class="content-body">
      <section id="configuration">
        <div class="row justify-content-center">
          <div class="col-6">
            <div class="card">
              <div class="card-content collapse show">
                <div class="card-header">
                  <h4 class="card-title text-center">Profile</h4>
                </div>
                <div class="card-body card-dashboard">
                  @if(session('success'))
                    <div class="alert bg-success alert-icon-left alert-dismissible mb-2" role="alert">
                      <span class="alert-icon"><i class="la la-check"></i></span>
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                      <strong>Success!</strong> {{ session('success') }}
                    </div>
                  @elseif(session('failed'))
                    <div class="alert bg-danger alert-icon-left alert-dismissible mb-2" role="alert">
                      <span class="alert-icon"><i class="la la-check"></i></span>
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                      <strong>Ooppsss!</strong> {{ session('failed') }}
                    </div>
                  @elseif($errors->any())
                    <div class="alert alert-danger">
                      <ul>
                        @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                        @endforeach
                      </ul>
                    </div>
                  @endif
                  <div class="form-group">
                                    <div class="alert alert-danger" id="validasi_element" style="display:none;">
                                        <ul id="validasi_content"></ul>
                                    </div>
                                </div>
                  @if($data['publisher']->system_type == 'edep')
                    <a href="#" onclick="showForm()" class="btn btn-warning" id="aFormIsbn">Anda belum terkoneksi dengan ISBN, klik di sini untuk melakukan koneksi</a>
                      <form action="{{ url('publisher/auth/connect') }}" method="POST" style="display:none" id="formIsbn">
                              @csrf
                              <div class="form-group">
                                <label>Username :</label>
                                <input type="text" name="username_isbn" class="form-control"placeholder="Masukan username pada akun ISBN Anda">
                              </div>
                              <div class="form-group">
                                <label>Password :</label>
                                <input type="password" name="password_isbn" class="form-control"placeholder="Masukan password pada akun ISBN Anda">
                              </div>
                              <div class="form-group">
                                <div class="text-center">
                                  <a href="#" class="btn btn-warning" onclick="saveIsbn()">Simpan Koneksi ISBN</a>
                                </div>
                              </div>
                      </form>
                  @elseif($data['publisher']->system_type == 'isbn')
                        <a href="#" class="btn btn-success">Anda terkoneksi dengan ISBN</a>

                  @endif
                  <form action="{{ url('publisher/auth/profile') }}" method="POST">
                    @csrf
                    <div class="form-group">
                      <label>Nama :</label>
                      <input type="text" name="fullname" class="form-control" value="{{ $data['publisher']->name }}" placeholder="Masukan nama lengkap">
                    </div>
                    <div class="form-group">
                      <label>Email :</label>
                      <input type="email" name="email" class="form-control" value="{{ $data['publisher']->user->email }}" placeholder="Masukan email" readonly="">
                    </div>
                    <div class="form-group">
                      <label>Alamat :</label>
                      <textarea name="address" class="form-control" style="resize:none;" placeholder="Masukan alamat">{{ $data['publisher']->address }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Provinsi</label>
                        <select name="province_id" id="publisher_province" class="form-control required" style="width:100%;"></select>
                    </div>
                    <div class="form-group">
                        <label>Kota/Kab</label>
                        <select name="city_id" id="publisher_city" class="form-control required" style="width:100%;"></select>
                    </div>
                    <div class="form-group">
                        <label>Kecamatan</label>
                        <select name="district_id" id="publisher_district" class="form-control required" style="width:100%;"></select>
                    </div>
                    <div class="form-group">
                        <label>Kelurahan</label>
                        <select name="village_id" id="publisher_village" class="form-control required" style="width:100%;"></select>
                    </div>
                    <div class="form-group">
                      <label>Username :</label>
                      <input type="text" name="username" class="form-control" value="{{ session('username') }}" placeholder="Masukan username" disabled>
                    </div>
                    <div class="form-group">
                      <div class="text-center">
                        <button type="reset" class="btn btn-danger">Reset</button>
                        <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
          @if($data['groups'])
          <div class="col-6">
            <div class="card">
              <div class="card-content collapse show">
                <div class="card-header">
                  <h4 class="card-title text-center">Publisher Group</h4>
                  <h6 class="text-center">{{ $data['groups']->name }}</h6>
                </div>
                <div class="card-body card-dashboard">
                  @foreach($data['groups']->groups as $a)
                    <a href="javascript:void(0);" style="cursor:none;" class="list-group-item list-group-item-action media">
                      <div class="media-body">
                        <h6 class="list-group-item-heading" style=" overflow:hidden;">{{ $a->publisher->name }}</h6>
                      </div>
                      <small class="text-muted"></small>
                    </a>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
          @endif
        </div>

      </section>
    </div>
  </div>
</div>
<script type="text/javascript">

  $(document).ready(function() {

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
      showForm = function(){
        $('#formIsbn').show();
        $('#aFormIsbn').hide();
      }
      saveIsbn = function(){
        $.ajax({
            url: "{{ url('publisher/auth/connect') }}",
            contentType: 'multipart/form-data',
            cache: false,
            // contentType: 'text/html',
            contentType: 'application/x-www-form-urlencoded',
            // processData: false,
            type: 'POST',
            data : $('#formIsbn').serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            success: function(response){
              $('#validasi_element').hide();
              $('#validasi_element').html('');
              if(response.status == 200) {
                  Toast.fire({
                      icon: 'success',
                      title: response.message
                  });
                  $('#aFormIsbn').text('Anda terkoneksi dengan ISBN').show();
                  $('#aFormIsbn').removeClass('btn-warning').addClass('btn-success').show();
                  $('#formIsbn').hide();

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
            error:function(data){
                Toast.fire({
                      icon: 'warning',
                      title: 'Form Error'
                  });
            },
            failure: function(data){
              Toast.fire({
                      icon: 'warning',
                      title: 'Form Submit Failed'
                  });
            }
        });
      }
  });

</script>
