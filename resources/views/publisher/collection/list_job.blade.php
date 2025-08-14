
<div class="content-body">
  <section id="configuration">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h4 class="card-title">Daftar Pemrosesan Upload</h4>
            <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
            <div class="heading-elements">
              <ul class="list-inline mb-0">
                <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                <li><a data-action="reload" onclick="loadDataTableJob()"><i class="ft-rotate-cw"></i></a></li>
                <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
              </ul>
            </div>
          </div>
          <div class="card-content collapse show">
            <div class="card-body card-dashboard">
              <table class="table table-striped table-bordered display nowrap" id="datatable_serversideJob">
                <thead class="text-center">
                  <tr>
                    <th>Type</th>
                    <th>Job Id</th>
                    <th>Progress</th>
                    <th>Status</th>
                    <th>Output</th>
                    <th>Error</th>
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
    loadDataTableJob();
  });

  function resetJob() {
    $('#periode_start').val('');
    $('#periode_end').val('');
    loadDataTable();
  }

  function loadDataTableJob() {
    $('#datatable_serversideJob').DataTable({
      processing: true,
      serverSide: true,
      destroy: true,
      scrollX: true,
      lengthMenu: [10, 25, 50, 75, 100],
      ajax: {
        method: 'GET',
        url: '{{ url("publisher/collection/import/jobs") }}',
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
          name: 'Error',
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