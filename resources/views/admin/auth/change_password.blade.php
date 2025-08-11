<div class="app-content content">
  <div class="content-wrapper">
    <div class="content-body">
      <section id="configuration">
        <div class="row justify-content-center">
          <div class="col-6">
            <div class="card">
              <div class="card-content collapse show">
                <div class="card-header">
                  <h4 class="card-title text-center">Ganti Password</h4>
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
                  <form action="{{ url('admin/auth/change_password') }}" method="POST">
                    @csrf
                    <div class="form-group">
                      <label>Password Baru :</label>
                      <input type="password" name="password" class="form-control" placeholder="Masukan password baru">
                    </div>
                    <div class="form-group">
                      <label>Konfirmasi Password :</label>
                      <input type="password" name="password_confirm" class="form-control" placeholder="Masukan konfirmasi password">
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