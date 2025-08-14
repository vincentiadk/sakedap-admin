<div class="app-content content">
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-1 d-inline-block">Edit Pengelolaan Buku</h3><br>
        <div class="row breadcrumbs-top d-inline-block">
          <div class="breadcrumb-wrapper col-12">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="#">Buku</a></li>
              <li class="breadcrumb-item"><a href="{{ url('admin/collection/manage/1') }}">Pengelolaan</a></li>
              <li class="breadcrumb-item active">Edit</li>
            </ol>
          </div>
        </div>
      </div>
      <div class="content-header-right col-md-6 col-12 mb-2 mt-1">
        <div class="float-md-right">
          <a href="{{ url('admin/collection/manage') }}" class="btn btn-secondary">Kembali</a>
        </div>
      </div>
    </div>
    <div class="content-body">
      <section id="configuration">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title text-center">Form Edit</h4>
              </div>
              <div class="card-content collapse show">
                <div class="card-body card-dashboard">
                  <form action="{{ url('admin/collection/manage/update/' . $collection->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if($errors->any())
                      <div class="alert alert-danger">
                        <ul>
                          @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                          @endforeach
                        </ul>
                      </div>
                    @elseif(session('failed'))
                      <div class="alert bg-danger alert-icon-left alert-dismissible mb-2" role="alert">
                        <span class="alert-icon"><i class="la la-check"></i></span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                        <strong>Failed!</strong> {{ session('failed') }}
                      </div>
                    @endif
                    <ul class="nav nav-tabs nav-justified">
                      <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" aria-controls="tab_general" href="#tab_general" aria-expanded="true">General</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" aria-controls="tab_contributor" href="#tab_contributor" aria-expanded="false">Kontributor</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" aria-controls="tab_cover" href="#tab_cover" aria-expanded="false">Cover</a>
                      </li>
                      <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" aria-controls="tab_original" href="#tab_original" aria-expanded="false">Konten</a>
                      </li>
                    </ul>
                    <div class="tab-content px-1 pt-1">
                      <div role="tabpanel" class="tab-pane active" id="tab_general" aria-expanded="true">
                        <p>
                          <div class="form-group">
                            <label>Pelaksana :</label>
                            <select name="publisher_id" id="publisher_id" class="form-control" style="width:100%;">
                              <option value="{{ $collection->publisher->id }}" selected>{{ $collection->publisher->name }}</option>
                            </select>
                          </div>
                          <div class="form-group">
                            <label>Judul :</label>
                            <textarea name="title" id="title" class="form-control" placeholder="Masukan judul">{{ $collection->title }}</textarea>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label>ISBN :</label>
                                <input type="text" class="form-control" value="{{ $collection->code }}" disabled>
                              </div>
                              <div class="form-group">
                                <label>Preview :</label>
                                <input type="text" class="form-control" placeholder="Masukan preview"  value="{{ $collection->preview }}" disabled>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label>Tahun Terbit :</label>
                                <input type="text" name="publication_year" id="publication_year" class="form-control" placeholder="Masukan tahun terbit" value="{{ $collection->publication_year }}">
                              </div>
                              <div class="form-group">
                                <label>Tempat Terbit :</label>
                                <select name="city_id" id="city_id" class="form-control" style="width:100%;">
                                  <option value="{{ $collection->city->id }}">{{ $collection->city->name }}</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="form-group">
                            <label>Total Halaman :</label>
                            <div class="input-group mb-2">
                              <input type="number" name="total_page" id="total_page" class="form-control" placeholder="Masukan total halaman" value="{{ $collection->physicalDescription() ? $collection->physicalDescription()->total_page : '' }}">
                              <div class="input-group-prepend">
                                <div class="input-group-text">Halaman</div>
                              </div>
                            </div>
                          </div>
                          <div class="form-group">
                            <label>Dimensi :</label>
                            <div class="input-group mb-2">
                              <input type="number" name="dimension" id="dimension" class="form-control" placeholder="Masukan dimensi" value="{{ $collection->physicalDescription() ? $collection->physicalDescription()->dimension : '' }}">
                              <div class="input-group-prepend">
                                <div class="input-group-text">Cm</div>
                              </div>
                            </div>
                          </div>
                          <div class="form-group">
                            <label>Ilustrasi :</label>
                            <select name="ilustration" id="ilustration" class="form-control">
                              <option value="">-- Pilih Ilustrasi --</option>
                              <option value="Ya" {{ $collection->physicalDescription() && $collection->physicalDescription()->ilustration == 'Ya' ?  'selected' : '' }}>Ya</option>
                              <option value="Tidak" {{ $collection->physicalDescription() && $collection->physicalDescription()->ilustration == 'Tidak' ?  'selected' : '' }}>Tidak</option>
                            </select>
                          </div>
                          <div class="form-group">
                            <label>Kategori :</label>
                            <select name="collection_category[]" id="collection_category" class="form-control default_select2" style="width:100%;" multiple>
                              @foreach($category as $c)
                                @php $exist = $collection->collectionCategory->where('category_id', $c->id)->count() @endphp
                                <option value="{{ $c->id }}" {{ $exist > 0 ? 'selected' : '' }}>{{ $c->name }}</option>
                              @endforeach
                            </select>
                          </div>
                          <div class="form-group">
                            <label>Subjek :</label>
                            <select name="collection_subject[]" id="collection_subject" class="form-control" style="width:100%;" multiple>
                              @foreach($collection->collectionSubject as $cs)
                                <option value="{{ $cs->subject->name }}" selected>{{ $cs->subject->name }}</option>
                              @endforeach
                            </select>
                          </div>
                          <div class="form-group">
                            <label>Keterangan :</label>
                            <textarea name="description" id="description" class="form-control" style="resize:none;" placeholder="Masukan informasi lain">{{ $collection->description }}</textarea>
                          </div>
                        </p>
                      </div>
                      <div class="tab-pane" id="tab_contributor">
                        <p>
                          <div class="form-group">
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label>Nama :</label>
                                  <input type="text" name="fullname_field" id="fullname_field" class="form-control" placeholder="Nama lengkap">
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label>Kontributor :</label>
                                  <select name="contributor_id_field" id="contributor_id_field" class="form-control default_select2" style="width:100%;">
                                    @foreach($contributor as $c)
                                      <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                  </select>
                                </div>
                              </div>
                              <div class="col-md-4">
                                <div class="form-group">
                                  <label>Titel :</label>
                                  <input type="text" name="title_field" id="title_field" class="form-control" placeholder="Titel">
                                </div>
                              </div>
                              <div class="col-md-4">
                                <div class="form-group">
                                  <label>Tahun Kelahiran :</label>
                                  <input type="number" name="year_of_birth_field" id="year_of_birth_field" class="form-control" placeholder="Tahun kelahiran">
                                </div>
                              </div>
                              <div class="col-md-4">
                                <div class="form-group">
                                  <label>Tahun Kematian :</label>
                                  <input type="number" name="year_of_death_field" id="year_of_death_field" class="form-control" placeholder="Tahun kematian">
                                </div>
                              </div>
                            </div>
                            <div class="form-group">
                              <button type="button" onclick="addContributor()" class="btn btn-success col-12">Tambah</button>
                            </div>
                            <div class="form-group">
                              <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="datatable_default">
                                  <thead class="text-center">
                                    <tr>
                                      <th>Kontributor</th>
                                      <th>Nama</th>
                                      <th>Titel</th>
                                      <th>Tahun Kelahiran</th>
                                      <th>Tahun Kematian</th>
                                      <th>Hapus</th>
                                    </tr>
                                  </thead>
                                  <tbody id="contributor_element">
                                    @foreach($collection->collectionContributor as $cc)
                                      <tr class="text-center">
                                        <input type="hidden" name="contributor_contributor_id_field[]" value="{{ $cc->contributor_id }}">
                                        <input type="hidden" name="contributor_fullname_field[]" value="{{ $cc->author->fullname }}">
                                        <input type="hidden" name="contributor_title_field[]" value="{{ $cc->author->title }}">
                                        <input type="hidden" name="contributor_year_of_birth_field[]" value="{{ $cc->author->year_of_birth }}">
                                        <input type="hidden" name="contributor_year_of_death_field[]" value="{{ $cc->author->year_of_death }}">

                                        <td class="align-middle">{{ $cc->contributor->name }}</td>
                                        <td class="align-middle">{{ $cc->author->fullname }}</td>
                                        <td class="align-middle">{{ $cc->author->title }}</td>
                                        <td class="align-middle">{{ $cc->author->year_of_birth }}</td>
                                        <td class="align-middle">{{ $cc->author->year_of_death }}</td>
                                        <td class="align-middle">
                                          <button type="button" class="btn btn-danger btn-sm" id="remove_field_contributor">
                                            <i class="la la-trash"></i>
                                          </button>
                                        </td>
                                      </tr>
                                    @endforeach
                                  </tbody>
                                </table>
                              </div>
                            </div>
                          </div>
                        </p>
                      </div>
                      <div class="tab-pane" id="tab_cover">
                        <div class="form-group">
                          @php $cover = $collection->collectionMedia->where('type', 'Cover')->first(); @endphp
                          @if($cover && Storage::exists($cover->link))
                            <div class="row justify-content-center">
                              <div class="col-md-6">
                                <div class="alert alert-warning alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                                  <span class="alert-icon"><i class="la la-info-circle"></i></span>
                                  <ul>
                                    <li>Ukuran: <b>{{ App\Helper\GeneralHelper::formatSize($cover->size) }}</b></li>
                                    <li>Ekstensi: <b>{{ $cover->extension }}</b></li>
                                    <li>Mime: <b>{{ $cover->mimes }}</b></li>
                                    <li>Hash: <b>{{ $cover->hash }}</b></li>
                                    <li>Metode: <b>{{ $cover->method() }}</b></li>
                                  </ul>
                                </div>
                              </div>
                            </div>
                            <center>
                              <img src="{{ url('/collection/cover') . '/' . $cover->id }}" style="max-width:242px; max-height:280px;">
                            </center>
                          @else
                            <div class="alert alert-danger text-center">Tidak ada file!</div>
                          @endif
                          <div class="row justify-content-center mt-2">
                            <div class="col-md-6">
                              <input type="file" name="cover" class="form-control">
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="tab-pane" id="tab_original">
                        <div class="form-group">
                          @php $watermark = $collection->collectionMedia->where('type', 'Watermark')->first(); @endphp
                          @if($watermark && Storage::exists($watermark->link))
                            <div class="row justify-content-center">
                              <div class="col-md-6">
                                <div class="alert alert-warning alert-icon-left alert-arrow-left alert-dismissible mb-2" role="alert">
                                  <span class="alert-icon"><i class="la la-info-circle"></i></span>
                                  <ul>
                                    <li>Ukuran: <b>{{ App\Helper\GeneralHelper::formatSize($watermark->size) }}</b></li>
                                    <li>Ekstensi: <b>{{ $watermark->extension }}</b></li>
                                    <li>Mime: <b>{{ $watermark->mimes }}</b></li>
                                    <li>Hash: <b>{{ $watermark->hash }}</b></li>
                                    <li>Total Halaman: <b>@isset($collection->physicalDescription()->total_page) {{ $collection->physicalDescription()->total_page }} @endif</b></li>
                                    <li>Metode: <b>{{ $watermark->method() }}</b></li>
                                  </ul>
                                </div>
                              </div>
                            </div>
                            <center>
                              <div class="embed-responsive embed-responsive-16by9">
                                <object class="embed-responsive-item" type="application/pdf" data="{{ asset(Storage::url($watermark->link)) }}" width="100%" height="200" allowfullscreen webkitallowfullscreen></object>
                              </div>
                            </center>
                          @else
                            <div class="alert alert-danger text-center">Tidak ada file!</div>
                          @endif
                          <div class="row justify-content-center mt-2">
                            <div class="col-md-6">
                              <input type="file" name="original" class="form-control">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group"><hr></div>
                    <div class="form-group">
                      <div class="text-right">
                        <fieldset class="checkbox">
                          <label>
                            <input type="checkbox" name="lock" value="{{ $collection->lock }}" {{ $collection->lock ? 'checked' : '' }}> Kunci
                          </label>
                        </fieldset>
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
    $('#datatable_default tbody').on('click', '#remove_field_contributor', function () {
      $('#datatable_default').DataTable().row($(this).parents('tr')).remove().draw();
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function() {
      $('#datatable_default').DataTable().columns.adjust();
    });

    $('.default_select2').select2({
      placeholder: '-- Pilih --'
    });

    select2AutoSuggest('#publisher_id', 'load_publisher');
    select2AutoSuggest('#city_id', 'load_city');
    select2AutoSuggestTags('#collection_subject', 'load_subject');
  });

  function addContributor() {
    var contributor_id_field = $('#contributor_id_field').val();
    var fullname_field       = $('#fullname_field').val();
    var title_field          = $('#title_field').val();
    var year_of_birth_field  = $('#year_of_birth_field').val();
    var year_of_death_field  = $('#year_of_death_field').val();

    if(!contributor_id_field || !fullname_field || !title_field) {
      Swal.fire('Harap mengisi kontributor, nama, dan title!', '', 'warning');
    } else {
      $.ajax({
        url: '{{ url("admin/contributor/show") }}' + '/' + contributor_id_field,
        type: 'GET',
        dataType: 'JSON',
        beforeSend: function() {
          loadingOpen('#configuration');
          $('#validasi_element').hide();
          $('#validasi_content').html('');
        },
        success: function(response) {
          loadingClose('#configuration');
          $('#form_data').append(`
            <input type="hidden" name="contributor_contributor_id_field[]" value="` + contributor_id_field + `">
            <input type="hidden" name="contributor_fullname_field[]" value="` + fullname_field + `">
            <input type="hidden" name="contributor_title_field[]" value="` + title_field + `">
            <input type="hidden" name="contributor_year_of_birth_field[]" value="` + year_of_birth_field + `">
            <input type="hidden" name="contributor_year_of_death_field[]" value="` + year_of_death_field + `">
          `);

          $('#datatable_default').DataTable().row.add([
            response.name,
            fullname_field,
            title_field,
            year_of_birth_field,
            year_of_death_field,
            '<button type="button" class="btn btn-danger btn-sm" id="remove_field_contributor"><i class="la la-trash"></i></button>'
          ]).draw().node();

          $('#contributor_id_field').val('').trigger('change');
          $('#fullname_field').val('');
          $('#title_field').val('');
          $('#year_of_birth_field').val('');
          $('#year_of_death_field').val('');
        }
      });
    }
  }
</script>
