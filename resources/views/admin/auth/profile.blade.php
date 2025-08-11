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
                  <form action="{{ url('admin/auth/profile') }}" method="POST">
                    @csrf
                    <div class="form-group">
                      <label>Nama :</label>
                      <input type="text" name="fullname" class="form-control" value="{{ session('fullname') }}" placeholder="Masukan nama lengkap">
                    </div>
                    <div class="form-group">
                      <label>Email :</label>
                      <input type="email" name="email" class="form-control" value="{{ session('email') }}" placeholder="Masukan email">
                    </div>
                    <div class="form-group">
                      <label>Alamat :</label>
                      <textarea name="address" class="form-control" style="resize:none;" placeholder="Masukan alamat">{{ session('address') }}</textarea>
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
        </div>
      </section>
    </div>
  </div>
</div>