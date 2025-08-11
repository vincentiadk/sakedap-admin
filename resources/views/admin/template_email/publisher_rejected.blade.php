<div class="app-content content">
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-1 d-inline-block">Template Email Penerbit Ditolak</h3><br>
        <div class="row breadcrumbs-top d-inline-block">
          <div class="breadcrumb-wrapper col-12">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="#">Template Email</a></li>
              <li class="breadcrumb-item active">Penerbit Ditolak</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="content-body">
      <section id="configuration">
        <div class="row">
          @if(session('success'))
            <div class="col-12">
              <div class="alert bg-success alert-icon-left alert-dismissible mb-2" role="alert">
                <span class="alert-icon"><i class="la la-check"></i></span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
                <strong>Success!</strong> {{ session('success') }}
              </div>
            </div>
          @endif
          <div class="col-12">
            <div class="card">
              <div class="card-content collapse show">
                <div class="card-body card-dashboard">
                  <form action="{{ url('admin/template_email/create_update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="slug" class="form-control" value="template-email-publisher-rejected">
                    <div class="form-group">
                      <textarea name="content" id="ckeditor" class="ckeditor">
                        @if($data)
                          {!! $data->content !!}
                        @endif
                      </textarea>
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

<script>
  $(function() {
    'use strict';
    var editor = CKEDITOR.instances['ckeditor'];
    if (editor) { editor.destroy(true); }
    CKEDITOR.replace('ckeditor', {
      height: '350px',
      extraPlugins: 'forms'
    });
  });
</script>