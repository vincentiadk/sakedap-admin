<div class="modal animated bounceInRight text-left" id="modal_element" data-backdrop="static" role="dialog" aria-labelledby="myModalLabel49" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel49">Form Request File Original</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="form_data">
          <div class="form-group file-upload">
            <label>Surat Permintaan File Original : <span class="danger">*</span> <a href="{{url('/main/surat-permohonan-file.doc')}}" target="_blank" class="btn btn-info btn-sm"><i class="la la-file"></i>Unduh Form</a></label>
            <div class="alert alert-info mb-2" role="alert">
              <strong>Mohon upload file surat permintaan file original</strong>
            </div>
            <input type="hidden" name="collection_id" id="collection_id" class="form-control ">
            <input type="file" name="file_request_letter" id="file_request_letter" class="form-control " accept=".pdf">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn grey btn-secondary" data-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-warning" onclick="submit()" id="btn_update">Kirim</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  function requestFile(collectionId) {
    $('#modal_element').modal('show');
    $('#collection_id').val(collectionId);
  }

  function reset() {
    console.log('reset')
    console.log($('#file_request_letter').val())
    console.log($('#file_request_letter').val())
  }

  function submit() {
    if($('#file_request_letter').val() == "") {
        Swal.fire({
            position: 'center',
            icon: 'warning',
            title: 'Harap mengupload file permintaan file original',
            showConfirmButton: true
        });
        return;
      }

      $.ajax({
          url: '{{ url("/publisher/collection/request/original/") }}/' + $('#collection_id').val() ,
          type: 'POST',
          dataType: 'JSON',
          data: new FormData($('#form_data')[0]),
          cache: false,
          contentType: false,
          processData: false,
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          beforeSend: function() {
              loadingOpen('#configuration');
              $('#validasi_element').hide();
              $('#validasi_content').html('');
          },
          success: function(response) {
              $('#collection_id').val('');
              $('#file_request_letter').val('').change();
              loadingClose('#configuration');
              $('#modal_element').modal('hide');
              if(response.status == 200) {
                  Toast.fire({
                      icon: 'success',
                      title: response.message
                  });
                  location.reload(true);
              } else if(response.status == 422) {
                  $('#validasi_element').show();

                  document.body.scrollTop            = 0;
                  document.documentElement.scrollTop = 0;

                  Toast.fire({
                      icon: 'info',
                      title: 'Validasi'
                  });

                  $.each(response.error, function(i, val) {
                      $('#validasi_content').append('<li>' + val + '</li>');
                  });
              } else {
                  Toast.fire({
                      icon: 'warning',
                      title: response.message
                  });
              }
          },
          error: function() {
              $('#collection_id').val('');
              $('#file_request_letter').val('').change();
              loadingClose('#configuration');
              $('#modal_element').modal('hide');
              Toast.fire({
                  icon: 'error',
                  title: 'Server Error!'
              });
          }
      });
  }
</script>