<div class="content mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-4">
            <form method="POST">
                @csrf
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Ganti Password</h5>
                    </div>
                    <div class="card-body border-top">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @elseif(session('success'))
                            <div class="alert bg-success text-white fade show border-0">
                                {{ session('success') }}
                            </div>
                        @elseif(session('error'))
                            <div class="alert bg-danger text-white fade show border-0">
                                {{ session('error') }}
                            </div>
                        @endif
                        <div class="form-group">
                            <label class="form-label">Password Baru : <span class="text-danger fw-bold">*</span></label>
                            <input type="password" class="form-control" name="new_password" id="new_password" placeholder="....................">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Konfirmasi Password : <span class="text-danger fw-bold">*</span></label>
                            <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="....................">
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="form-group mb-0">
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary" onclick="onLoading('show', 'body')">
                                    <i class="ph-floppy-disk me-1"></i>
                                    Simpan Perubahan Password
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
