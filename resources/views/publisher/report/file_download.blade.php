<div class="app-content content">
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-1 d-inline-block">File Download</h3><br>
        <div class="row breadcrumbs-top d-inline-block">
          <div class="breadcrumb-wrapper col-12">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ url('publisher/dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="#">Laporan</a></li>
              <li class="breadcrumb-item active">File Download</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="content-body">
      <section id="configuration">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Daftar File Download</h4>
                <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                <div class="heading-elements">
                  <ul class="list-inline mb-0">
                    <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                    <li><a onclick="loadDataTable()" data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                    <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                  </ul>
                </div>
              </div>
              <div class="card-content collapse show">
                <div class="card-body card-dashboard">
                  <table class="table table-striped table-bordered display nowrap" id="datatable_serverside">
                    <thead class="text-center">
                      <tr>
                        <th>Jenis</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>File</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
    
<div class="content-body">
  <section id="configuration">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Daftar Pemrosesan Download File</h4>
            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
            <div class="heading-elements">
              <ul class="list-inline mb-0">
                <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                <li><a data-action="reload" onclick="loadDataTableJObs()"><i class="ft-rotate-cw"></i></a></li>
                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              </ul>
            </div>
          </div>
          <div class="card-content collapse show">
            <div class="card-body card-dashboard">
              <table class="table table-striped table-bordered display nowrap" id="datatable_serverside_jobs">
                <thead class="text-center">
                  <tr>
                    <th>Type</th>
                    <th>Job Id</th>
                    <th>Progress</th>
                    <th>Status</th>
                    <th>Output</th>
                    <th>Waktu Submit</th>
                    <th>Waktu Mulai</th>
                    <th>Waktu Selesai</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<script type="text/javascript">
  $(function() {
    loadDataTableJObs();
  });

  function reset() {
    loadDataTableJObs();
  }

  function loadDataTableJObs() {
    $('#datatable_serverside_jobs').DataTable({
      processing: true,
      serverSide: true,
      destroy: true,
      scrollX: true,
      lengthMenu: [10, 25, 50, 75, 100],
      ajax: {
        method: 'GET',
        url: '{{ url("publisher/report/file_download/jobs") }}',
      },
      columns: [
        {
          name: 'Type',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'Progress',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'Job Id',
          className: 'align-middle text-center'
        },
        {
          name: 'Status',
          className: 'align-middle text-center'
        },
        {
          name: 'Output',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'created_at',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'started_at',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'finished_at',
          searchable: false,
          orderable: false,
          className: 'align-middle text-center'
        }
      ]
    });
  }
</script>
  </div>
</div>

<script>
  $(function() {
    loadDataTable();
  });

  function loadDataTable() {
    $('#datatable_serverside').DataTable({
      processing: true,
      serverSide: true,
      destroy: true,
      scrollX: true,
      lengthMenu: [10, 25, 50, 75, 100],
      ajax: {
        url: '{{ url("publisher/report/file_download/datatable") }}'
      },
      columns: [
        {
          name: 'slug',
          className: 'align-middle text-center'
        },
        {
          name: 'status',
          className: 'align-middle text-center'
        },
        {
          name: 'date',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'time',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'link',
          searchable: false,
          className: 'align-middle text-center'
        }
      ]
    });
  }
</script>