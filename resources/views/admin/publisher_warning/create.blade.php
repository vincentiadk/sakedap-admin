<div class="app-content content">
   <div class="content-wrapper">
       <div class="content-header row">
           <div class="content-header-left col-md-6 col-12 mb-2">
               <h3 class="content-header-title mb-1 d-inline-block">Tambah Teguran </h3><br>
               <div class="row breadcrumbs-top d-inline-block">
                   <div class="breadcrumb-wrapper col-12">
                       <ol class="breadcrumb">
                           <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
                           <li class="breadcrumb-item"><a href="#">Penerbit </a></li>
                           <li class="breadcrumb-item active">Teguran</li>
                       </ol>
                   </div>
               </div>
           </div>
       </div>
       <div class="content-body">
           <section id="configuration">
               @if (session('success'))
                   <div class="alert bg-success alert-icon-left alert-dismissible mb-2" role="alert">
                       <span class="alert-icon"><i class="la la-check"></i></span>
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                       <strong>Success!</strong> {{ session('success') }}
                   </div>
               @elseif(session('failed'))
                   <div class="alert bg-danger alert-icon-left alert-dismissible mb-2" role="alert">
                       <span class="alert-icon"><i class="la la-times"></i></span>
                       <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                       <strong>Success!</strong> {{ session('failed') }}
                   </div>
               @endif
               <div class="row">
                   <div class="col-md-12">
                       <div class="alert alert-danger" id="validasi_element" style="display:none;">
                           <ul id="validasi_content"></ul>
                       </div>
                       <div class="card">
                           <div class="card-content collapse show">
                               <div class="card-body card-dashboard">
                                 <form id="form_data" action="{{ url('admin/publisher_warning/create') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                       <label>Penerbit :</label>
                                       <select name="publisher_id" id="publisher_id" class="form-control" style="width:100%;"></select>
                                   </div>
                                    <div class="form-group">
                                       <label>Pilih Teguran :</label>
                                       <select name="warning" id="warning" class="form-control">
                                          <option value="">-- Pilih Teguran --</option>
                                          <option value="1">Teguran Ke-1</option>
                                          <option value="2">Teguran Ke-2</option>
                                          <option value="3">Teguran Ke-3</option>
                                       </select>
                                    </div>
                                    <div class="form-group">
                                       <label>Asal Teguran :</label>
                                       <select name="category" id="category" class="form-control">
                                          <option value="1">Pusat</option>
                                          <option value="2">Provinsi</option>
                                       </select>
                                    </div>
                                    <div class="form-group" id="province_select">
                                       <label>Provinsi</label>
                                       <select name="province_id" id="province_id" class="form-control" style="width:100%;"></select>
                                    </div>
                                    <div class="form-group">
                                       <label>Tanggal Teguran :</label>
                                       <input type="date" name="warning_date" id="warning_date" class="form-control" max="{{ date('Y-m-d') }}">
                                    </div>
                                    <div class="form-group">
                                      <label>Alasan :</label>
                                      <textarea  name="reason" id="reason" class="form-control" placeholder="Isi alasan"></textarea>
                                   </div>
                                    <h4 class="form-section">Lampiran</h4>
                                    <div class="alert alert-warning">
                                        <small>
                                            Jenis File Yang di Dukung <b>: JPG, JPEG, PNG, PDF</b><br>
                                            Maksimal Ukuran File <b>: 5 MB</b>
                                        </small>
                                    </div>
                                    <div class="form-group">
                                        <input type="file" class="file-cover form-control-lg" name="attachment" id="attachment" data-theme="fa5">
                                    </div>
                                    <div class="form-group"><hr></div>
                                    <div class="form-group">
                                       <div class="text-right">
                                          <div class="mt-2">
                                             <button type="button" class="btn btn-primary submit" onclick="create()"><i class="fas fa-plus"></i> Submit</button>
                                          </div>
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

function selectWarning(e) {
}

var rows_selected = [];
var table;
function updateDataTableSelectAllCtrl(table){
   var $table             = table.table().node();
   var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
   var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
   var chkbox_select_all  = $('thead input[name="select_all"]', $table).get(0);

   if($chkbox_checked.length === 0){
      chkbox_select_all.checked = false;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = false;
      }

   } else if ($chkbox_checked.length === $chkbox_all.length){
      chkbox_select_all.checked = true;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = false;
      }
   } else {
      chkbox_select_all.checked = true;
      if('indeterminate' in chkbox_select_all){
         chkbox_select_all.indeterminate = true;
      }
   }
}

$(document).ready(function (){

   $('#province_select').fadeOut()
   $('#warning').change(function() {
      rows_selected = [];
   })

  $('#category').change(function() {
       if($(this).val() == 1) {
         $('#province_select').fadeOut()
      } else $('#province_select').fadeIn()
  })
  

  select2AutoSuggest('#province_id', 'load_province');
  select2AutoSuggest('#publisher_id', 'load_publisher');
        
  $('#publisher_id').on('change', function() {
    var selectedPublisherId = $(this).val(); // Get the selected publisher_id.

    if (selectedPublisherId) {
      // Make the AJAX request with the selected publisher_id.
      $.ajax({
          type: 'GET',
          url: '{{ url("admin/publisher-warning/count") }}' + '/' + selectedPublisherId,
          success: function(response) {
            if (response.count == 3) {
              Swal.fire('Error!', 'Publisher tersebut sudah 3 kali mendapat teguran', 'error');
            } else {
              if (response.last) {
                var lastWarning = response.last.warning_date;
                var lastWarningDate = new Date(lastWarning);
                var currentDate = new Date();
                var timeDifference = lastWarningDate - currentDate;

                var daysDifference = Math.ceil(timeDifference / (1000 * 60 * 60 * 24));
                if (daysDifference < 40) {
                  Swal.fire('Error!', 'Jarak dari teguran sebelumnya kurang dari 40 hari', 'error');
                } else {
                  $('#warning option').prop('disabled', false);
                  $("#warning").val(response.count + 1);
                  $('#warning option:not(:selected)').prop('disabled', true);
                }
              }
              
            }
          },
          error: function() {
              console.error('Error while counting warnings.');
          }
      });
    }
  });

});




function create() {

	var formData = new FormData($('#form_data')[0]);
  $.ajax({
      url: '{{ url("admin/publisher-warning/create") }}',
      type: 'POST',
			data: formData,
      cache: false,
			contentType: false,
			processData: false,
			headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
      beforeSend: function() {
        $('#validasi_element').hide();
        $('#validasi_content').html('');
        loadingOpen('.card-body');
      },
      success: function(response) {
        loadingClose('.card-body');
        if(response.status == 200) {
          Swal.fire('Berhasil!', response.message, 'success');
          window.location.href = "{{url('admin/publisher-warning')}}";
        } else if(response.status == 422) {
          $('#validasi_element').show();
          $('.card-body').scrollTop(0);
          
          $.each(response.error, function(i, val) {
            $.each(val, function(i, val) {
              $('#validasi_content').append(`
                <li>` + val + `</li>
              `);
            });
          });
        } else {
          Swal.fire('Error!', response.message, 'error');
          loadingClose('.card-body');
        }
      },
      error: function() {
        $('.card-body').scrollTop(0);
        loadingClose('.card-body');
        Swal.fire('Server Error!', '', 'error');
      }
    });
  }
</script>