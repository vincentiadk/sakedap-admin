<div class="app-content content">
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-1 d-inline-block">{{ $data['title'] }}</h3><br>
        <div class="row breadcrumbs-top d-inline-block">
          <div class="breadcrumb-wrapper col-12">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ url('publisher/dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="#">Koleksi</a></li>
              <li class="breadcrumb-item active">Masalah</li>
            </ol>
          </div>
        </div>
      </div>
      <div class="content-header-right col-md-6 col-12 mb-2 mt-1">
        <div class="float-md-right">
          <button type="button" class="btn btn-secondary" onclick="loadDataTable()">Refresh</button>
        </div>
      </div>
    </div>
    <div class="content-body">
      <section id="configuration">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Filter</h4>
              </div>
              <div class="card-content collapse show">
                <div class="card-body card-dashboard">
                  <div class="row">
                   @if($data['groups'])
                    <div class="col-md-4">
                      <label>Publisher</label>
                      <select name="publisher_id" id="publisher_id" class="form-control select2" multiple="multiple">
                        @foreach($data['groups']->groups as $key => $item)
                          <option value="{{ $item->publisher->id }}">{{ $item->publisher->name }}</option>
                        @endforeach
                      </select>
                    </div>
                    @endif
                  </div>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>ISBN/ISSN</label>
                        <input type="text" name="code" id="code" placeholder="Code" class="form-control">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Judul</label>
                        <input type="text" name="title" id="title" placeholder="Judul" class="form-control">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    {{-- <div class="col-md-4">
                      <div class="form-group">
                        <label>Dari Tanggal :</label>
                        <input type="date" name="periode_start" id="periode_start" class="form-control">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Sampai Tanggal :</label>
                        <input type="date" name="periode_end" id="periode_end" class="form-control">
                      </div>
                    </div> --}}
                    <div class="col-md-12">
                      <hr>
                      <div class="form-group text-right">
                        <button type="button" class="btn btn-danger btn-sm" onclick="reset()"><i class="la la-times"></i> Reset</button>
                        <button type="button" class="btn btn-success btn-sm" onclick="loadDataTable()"><i class="la la-search"></i> Cari</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h4 class="card-title">Daftar Koleksi KCKRA</h4>
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
                        <th>Tipe Koleksi</th>
                        <th>Pelaksana</th>
                        <th>Judul</th>
                        <th>Identifier</th>
                        <th>Kirim Perpusnas</th>
                        <th>Diterima Perpusnas</th>
                        <th>Kirim Provinsi</th>
                        <th>Diterima Provinsi</th>
                        <th>Aksi</th>
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
  </div>
</div>


<div class="modal animated bounceInRight text-left" id="modal_element" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel49" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel49">Form</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="alert alert-danger" id="validasi_element" style="display:none;">
					<ul id="validasi_content"></ul>
				</div>
        <form id="form_data" method="POST" enctype="multipart/form-data">
          <div class="row">
            <table class="table table-striped table-bordered table-responsive" id="table_detail">
              <thead class="text-center">
                <tr>
                  <th style="white-space: normal;">No Resi</th>
                  <th style="white-space: normal;">Tanggal Kirim</th>
                  <th style="white-space: normal;">Tanggal Terima</th>
                  <th style="white-space: normal;">Status Pengiriman</th>
                  <th style="white-space: normal;">Status Koleksi</th>
                  <th style="white-space: normal;">Note</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" onclick="cancel()" id="btn_cancel">Tutup</button>
			</div>
		</div>
	</div>
</div>

<script>
  $(function() {
    $('#tipekoleksi').select2({
        placeholder: '-- Pilih Tipe Koleksi --',
        allowClear: true,
        multiple: true,
        cache: true,
    });

     $('#publisher_id').select2({
        placeholder: '-- Pilih Publisher --',
        allowClear: true,
        multiple: true,
        cache: true,
    });

    $("#tipekoleksi").select2("val", "{{ Request::input('tipe') }}");
    $("#publisher_id").select2("val", "{{ Request::input('publisher_id') }}");

    loadDataTable();
  });

  function reset() {
    $('#periode_start').val('');
    $('#periode_end').val('');
    loadDataTable();
  }

  function cancel() {
		$('#modal_element').modal('hide');
	}

  function loadDataTable() {
    $('#datatable_serverside').DataTable({
      processing: true,
      serverSide: true,
      destroy: true,
      scrollX: true,
      lengthMenu: [10, 25, 50, 75, 100],
      order: [[2,"asc"]],
      ajax: {
        url: '{{ url("publisher/collection/monitoring_kckra/datatable") }}',
        data: {
          title: $('#title').val(),
          code: $('#code').val()
        }
      },
      rowCallback: function(row, data) {
				// Customize row color based on data
				$(row).find('td:eq(5)').css('color', 'white');
				if (data[5] >= 2) {
					$(row).find('td:eq(5)').css('background-color', 'green');
				} else {
					$(row).find('td:eq(5)').css('background-color', 'grey');
				}

        $(row).find('td:eq(7)').css('color', 'white');
				if (data[7] >= 1) {
					$(row).find('td:eq(7)').css('background-color', 'green');
				} else {
					$(row).find('td:eq(7)').css('background-color', 'grey');
				}
			},
      columns: [
        {
          name: 'type',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'publisher_id',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'title',
          className: 'align-middle text-center'
        },
        {
          name: 'code',
          className: 'align-middle text-center'
        },
        {
          name: 'perpusnas_sent_count',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'perpusnas_accept_count',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'province_sent_count',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'province_accept_count',
          searchable: false,
          className: 'align-middle text-center'
        },
        {
          name: 'action',
          searchable: false,
          className: 'align-middle text-center'
        },
      ]
    });
  }

  function show(id) {
		$('#modal_element').modal('show');
		$.ajax({
			url: '{{ url("publisher/collection/monitoring_kckra/show") }}' + '/' + id,
			type: 'GET',
			dataType: 'JSON',
			beforeSend: function() {
				loadingOpen('.modal-content');
				$('#validasi_element').hide();
				$('#validasi_content').html('');
			},
			success: function(response) {
				loadingClose('.modal-content');
				
        var tableHtml = '';
        response.forEach(element => {
          tableHtml += '<tr>' +
              '<td>' + element.receipt_no + '</td>' +
              '<td>' + element.delivery_date + '</td>' +
              '<td>' + (element.accepted_date === '0000-00-00' ? '' : element.accepted_date) + '</td>' +
              '<td>' + element.status_delivery + '</td>' +
              '<td>' + (element.status_collection === null ? '' : element.status_collection) + '</td>' +
              '<td>' + element.problem + '</td>' +
              '</tr>';
        });

        $('#table_detail tbody').html(tableHtml);
			},
			error: function() {
				loadingClose('.modal-content');
				cancel();
				Toast.fire({
					icon: 'error',
					title: 'Server Error!'
				});
			}
		})
	}
</script>