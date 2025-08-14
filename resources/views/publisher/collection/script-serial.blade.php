<script type="text/javascript">
	function loadDataTableSerial() {
	    tableSerial = $('#datatable_serverside_serial').DataTable({
	      ajax: {
	        	url: '{{ url("publisher/collection/serial") }}',
	        	type: 'POST',
	          	dataType: 'JSON',
	          	async : false,
	          	headers: {
	            	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	          	},
	      },
	      processing: true,
	      serverSide: true,
	      scrollX: true,
	      lengthMenu: [10, 25, 50, 75, 100],
	      columns: [
	        {
	          name : 'no',
	          searchable: false
	        },
	        { 
	          name :'deposit',
	          searchable: false
	        },
	        { 
	          name :'code',
	        },
	        {
	          name : 'title',
	        },
	        {
	          name : 'action',
	          searchable: false
	        }
	      ]
	    });
  	}

  	function selectSerial(id) {
      	$.ajax({
          	url: '{{ url("publisher/collection/serial") }}' + '/' + id,
          	type: 'POST',
          	async: false,

	      	headers: {
	        	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	      	},
          	success: function(data) {
          		console.log(data)
          		$('#from_detail').fadeIn(500) 
          		showFromSerial()
          		$('#id_serial').val(data.id)
            	$('#code_serial').val(data.code)
		        $('#title_serial').val(data.title)
		        $('#ddc_serial').val(data.ddc)
		        $('#preview_serial').val(data.preview)
		        $('#publication_month_serial').val(data.publication_month)
		        $('#publication_year_serial').val(data.publication_year)
		        $('#description_serial').val(data.description)

		        data.collection_contributor.forEach(function(item, index) {
		        	if(index != 0) {
		        		addContributor('#form-contributor_serial')
		        	}

		            let number = (countContributor - 1);

		            $('#contributor_name_field_' + number).val(item.contributor.name)
		            $('#author_fullname_field_' + number).val(item.author.fullname)
		            $('#author_title_field_' + number).val(item.author.title)
		            $('#author_year_of_birth_field' + number).val(item.author.year_of_birth)
		            $('#author_year_of_death_field' + number).val(item.author.year_of_death)


		            var newOption = new Option(item.contributor.name.trim(), item.contributor.id, true, true);
		            $('#contributor_id_field_'+number).append(newOption).trigger('change');

		            var newOption = new Option(item.author.name.trim(), item.author.id, true, true);
		            $('#author_id_field_'+number).append(newOption).trigger('change');
		        });

          	}
        })

    //});
    }
</script>