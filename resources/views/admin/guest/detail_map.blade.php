<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <h3 class="content-header-title mb-1 d-inline-block">Detail Peta</h3><br>
                <div class="row breadcrumbs-top d-inline-block">
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('admin/guest') }}">Guest</a></li>
                            <li class="breadcrumb-item active">Detail</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-header-right col-md-6 col-12 mb-2 mt-1">
                <div class="float-md-right">
                    <a href="{{ url('admin/guest') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="configuration">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title text-center">Detail Data</h4>
                            </div>
                            <div class="card-content collapse show">
                                <div class="card-body card-dashboard">
                                    <ul class="nav nav-tabs nav-justified">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-toggle="tab" aria-controls="tab_general"
                                                href="#tab_general" aria-expanded="true">Meta Data</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" aria-controls="tab_contributor"
                                                href="#tab_contributor" aria-expanded="false">Kontributor</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" aria-controls="tab_cover"
                                                href="#tab_cover" aria-expanded="false">Cover</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content px-1 pt-1">
                                        <div role="tabpanel" class="tab-pane active" id="tab_general"
                                            aria-expanded="true">
                                            <p>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="table-responsive">
                                                            <table class="table table-striped table-bordered">
                                                                <tbody>
                                                                    <tr>
                                                                        <td
                                                                            class="align-middle w-20 font-weight-bold">
                                                                            Penerbit</td>
                                                                        <td class="align-middle">
                                                                            {{ $collection->publisher->name }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td
                                                                            class="align-middle w-20 font-weight-bold">
                                                                            Judul</td>
                                                                        <td class="align-middle">
                                                                            {{ $collection->title }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td
                                                                            class="align-middle w-20 font-weight-bold">
                                                                            ISBN</td>
                                                                        <td class="align-middle">
                                                                            {{ $collection->code }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td
                                                                            class="align-middle w-20 font-weight-bold">
                                                                            Preview</td>
                                                                        <td class="align-middle">
                                                                            {{ $collection->preview }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td
                                                                            class="align-middle w-20 font-weight-bold">
                                                                            Tahun Terbit</td>
                                                                        <td class="align-middle">
                                                                            {{ $collection->publication_year }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td
                                                                            class="align-middle w-20 font-weight-bold">
                                                                            Tempat Terbit</td>
                                                                        <td class="align-middle">
                                                                            {{ isset($collection->publisher->city) ? $collection->publisher->city->name : '' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td
                                                                            class="align-middle w-20 font-weight-bold">
                                                                            Total Halaman</td>
                                                                        <td class="align-middle">
                                                                            @if($collection->physicalDescription())
                                                                            {{ $collection->physicalDescription()->total_page }}
                                                                            Hal
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td
                                                                            class="align-middle w-20 font-weight-bold">
                                                                            Skala</td>
                                                                        <td class="align-middle">
                                                                            @if($collection->physicalDescription())
                                                                            {{ $collection->physicalDescription()->scale }}
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td
                                                                            class="align-middle w-20 font-weight-bold">
                                                                            Dimensi</td>
                                                                        <td class="align-middle">
                                                                            @if($collection->physicalDescription())
                                                                            {{ $collection->physicalDescription()->dimension }}
                                                                            Cm
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td
                                                                            class="align-middle w-20 font-weight-bold">
                                                                            Kategori</td>
                                                                        <td class="align-middle">
                                                                            @foreach($collection->collectionCategory
                                                                            as $cc)
                                                                            <span
                                                                                class="badge bg-info">{{ $cc->category->name }}</span>
                                                                            @endforeach
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td
                                                                            class="align-middle w-20 font-weight-bold">
                                                                            Subjek</td>
                                                                        <td class="align-middle">
                                                                            @foreach($collection->collectionSubject
                                                                            as $cs)
                                                                            <span
                                                                                class="badge bg-info">{{ $cs->subject->name }}</span>
                                                                            @endforeach
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="align-middle font-weight-bold">
                                                                            Keterangan</td>
                                                                        <td class="align-middle">
                                                                            {{ $collection->description }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="align-middle font-weight-bold">
                                                                            Tanggal Terima</td>
                                                                        <td class="align-middle">
                                                                            <input type="date" name="received_at"
                                                                                class="form-control"
                                                                                value="{{ date('Y-m-d') }}">
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        @php $watermark = $collection->collectionMedia->where('type', 3)->first(); @endphp
                                                        @if($watermark && count($watermark->jsonParse()) > 0)
                                                            <center>
                                                                <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
                                                                    <div class="carousel-inner">
                                                                    @foreach($watermark->jsonParse() as $key => $w)
                                                                        @if($key == 0)
                                                                        <div class="carousel-item active">
                                                                            <img class="d-block w-100 ezoom" src="{{ $w }}" alt="First slide" style="height:903px;">
                                                                        </div>
                                                                        @else
                                                                        <div class="carousel-item">
                                                                            <img class="d-block w-100 ezoom" src="{{ $w }}" alt="First slide" style="height:903px;">
                                                                        </div>
                                                                        @endif
                                                                    @endforeach
                                                                    </div>
                                                                    <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                                                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                                    <span class="sr-only">Previous</span>
                                                                    </a>
                                                                    <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                                                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                                    <span class="sr-only">Next</span>
                                                                    </a>
                                                                </div>
                                                            </center>
                                                        @else
                                                            <div class="alert alert-danger text-center font-weight-bold"
                                                                style="height:650px;">
                                                                <span style="line-height:650px;">Tidak ada file!</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </p>
                                        </div>
                                        <div class="tab-pane" id="tab_contributor">
                                            <p>
                                                <div class="form-group">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered table-striped"
                                                            id="datatable_default">
                                                            <thead class="text-center">
                                                                <tr>
                                                                    <th>Kontributor</th>
                                                                    <th>Nama</th>
                                                                    <th>Gelar</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($collection->collectionContributor as $cc)
                                                                <tr class="text-center">
                                                                    <td class="align-middle">
                                                                        {{ $cc->contributor->name }}</td>
                                                                    <td class="align-middle">
                                                                        {{ $cc->author->fullname }}</td>
                                                                    <td class="align-middle">
                                                                        {{ $cc->author->title }}</td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </p>
                                        </div>
                                        <div class="tab-pane" id="tab_cover">
                                            <div class="form-group">
                                                @php $cover = $collection->collectionMedia->where('type',
                                                1)->first(); @endphp
                                                @if($cover && Storage::exists($cover->link))
                                                <div class="row justify-content-center">
                                                    <div class="col-md-6">
                                                        <div class="alert alert-warning alert-icon-left alert-arrow-left alert-dismissible mb-2"
                                                            role="alert">
                                                            <span class="alert-icon"><i
                                                                    class="la la-info-circle"></i></span>
                                                            <ul>
                                                                <li>Ukuran:
                                                                    <b>{{ App\Helper\GeneralHelper::formatSize($cover->size) }}</b>
                                                                </li>
                                                                <li>Ekstensi: <b>{{ $cover->extension }}</b></li>
                                                                <li>Mime: <b>{{ $cover->mimes }}</b></li>
                                                                <li>Hash: <b>{{ $cover->hash }}</b></li>
                                                                <li>Metode: <b>{{ $cover->method() }}</b></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <center>
                                                    <img src="{{ asset(Storage::url($cover->link)) }}" class="ezoom" style="max-width:242px; max-height:280px;">
                                                </center>
                                                @else
                                                <div class="alert alert-danger text-center">Tidak ada file!</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
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
    $(function () {
        $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
            $('#datatable_default').DataTable().columns.adjust();
        });
    });
</script>
