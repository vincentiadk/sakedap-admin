<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-6 col-12 mb-2">
                <h3 class="content-header-title mb-1 d-inline-block">Dokumentasi API</h3><br>
            </div>
        </div>
        <div class="content-body">
            <section id="configuration">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">API Buat Koleksi Buku ber ISBN</h4>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="exampleInputEmail1">URL</label>
                                    <input type="email" class="form-control" value="{{ 'POST: ' . env('APP_URL') . '/api/publisher/collection' }}" readonly>
                                </div>
                                <div class="form-group">
                                    <h5>Authorization Header</h5>
                                    <div class="bs-callout-info callout-border-right ">
                                        <div class="card" style="background:#ECEFF1;">
                                            <div class="card-body">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Name</th>
                                                            <th>Value</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Client-ID</td>
                                                            <td>: {{ $credentials->client_id }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Client-Secret</td>
                                                            <td>: {{ $credentials->client_secret }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Authable-ID</td>
                                                            <td>: {{ $credentials->authable_id }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <h5>Parameter</h5>
                                    <div class="bs-callout-info callout-border-right ">
                                        <pre>
							                <code>
                                                {
                                                    "code": "978-623-200-314-9" <span style='color:blue'>wajib diisi</span>,
                                                    "type":"buku/audio/film/peta/partitur"
                                                    "preview": "2-5,30,40" <span style='color:blue'>wajib diisi</span>,
                                                    "description": "diisi dengan abstrak atau ringkasan isi buku" <span style='color:blue'>wajib diisi</span>,
                                                    "publication_year": "2021",
                                                    "publication_month": "09" isi bulan terbit  dengan 2 digit angka,
                                                    "title": "Judul Buku",
                                                    "category": [
                                                        "category 1",
                                                        "category 2",
                                                        "category 3"
                                                    ],
                                                    "contributor": [
                                                        {
                                                            "name" 					: "Contibutor Name",
                                                            "author_fullname"			: "Author Name",
                                                            "author_title"				: "dr.",
                                                            "author_year_of_birth"	: "2020",
                                                            "author_year_of_death"	: ""
                                                        },
                                                        {
                                                            "name" 					: "Contibutor Name 2",
                                                            "author_fullname"			: "Author Name 2",
                                                            "author_title"				: "dr.",
                                                            "author_year_of_birth"	: "2020",
                                                            "author_year_of_death"	: ""
                                                        }
                                                    ],
                                                    "file_original": "url file original",
                                                    "file_cover": "url cover",
                                                    "price" : "25000" wajib diisi dengan integer tanpa tanda koma,
                                                    "access": "1/2/3/4",
                                                }
							                </code>
						                </pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">API GET Collection</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">URL</label>
                                            <input type="email" class="form-control" value="{{ 'GET: ' . env('APP_URL') . '/api/publisher/collection/{type}/list' }}" readonly>
                                        </div>
                                        <div class="form-group">
                                            <h5>Authorization Header</h5>
                                            <div class="bs-callout-info callout-border-right ">
                                                <div class="card" style="background:#ECEFF1;">
                                                    <div class="card-body">
                                                        <table class="table table-sm">
                                                            <thead>
                                                                <tr>
                                                                    <th>Name</th>
                                                                    <th>Value</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td>Client-ID</td>
                                                                    <td>: {{ $credentials->client_id }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Client-Secret</td>
                                                                    <td>: {{ $credentials->client_secret }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Authable-ID</td>
                                                                    <td>: {{ $credentials->authable_id }}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <h5>Parameter</h5>
                                            <div class="bs-callout-info callout-border-right">
                                                <pre>
                                                    <code>
                                                        {
                                                            "type" :"buku/partitur/peta/audio/film" <span style='color:blue'>pilih salah satu</span>
                                                            "code": "978-623-200-314-9",
                                                            "received": "0",   <span style='color:blue'>optional, isi jika Anda ingin get data yang belum diterima</span>
                                                            "received_day": "19",   <span style='color:blue'>optional</span>
                                                            "received_month": "01",    <span style='color:blue'>optional</span>
                                                            "received_year": "2021",    <span style='color:blue'>optional</span>
                                                            "page": "2",    <span style='color:blue'>untuk halaman selanjutnya</span>
                                                        }
                                                    </code>
                                                </pre>
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
