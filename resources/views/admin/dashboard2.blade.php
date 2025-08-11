<div class="app-content content ">
	<div class="content-wrapper">
		<div class="content-body">
			<section id="configuration">
				<div class="row">
					<div class="col-md-5">
						<div class="card lazy">
							<div class="card-body" id="collection_type_status">
							
							</div>
						</div>
						<div class="card lazy">
							<div class="card-header">
								<h4 class="card-title text-center">Status Koleksi</h4>
							</div>
							<div class="card-body">
								<canvas id="collection_status" height="300"></canvas>
							</div>
						</div>
					</div>
					<div class="col-md-7">
						<div class="card lazy">
							<div class="card-header">
								<h4 class="card-title text-center">Total Koleksi</h4>
							</div>
							<div class="card-body">
								<canvas id="total_collection" height="571"></canvas>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="card lazy">
							<div class="card-header">
								<h4 class="card-title text-center">Koleksi Pemantauan</h4>
							</div>
							<div class="card-body">
								<div class="media-list list-group" id="div-pemantauan"></div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="card lazy">
							<div class="card-header">
								<h4 class="card-title text-center">Penambahan Koleksi 10 Hari Terakhir</h4>
							</div>
							<div class="card-body">
								<canvas id="collection_last_day" height="288"></canvas>
							</div>
						</div>
						<div class="card lazy">
							<div class="card-header">
								<h4 class="card-title text-center">Tipe File</h4>
							</div>
							<div class="card-body">
								<canvas id="file_type" height="288"></canvas>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="card lazy">
							<div class="card-header">
								<h4 class="card-title text-center">Aktivitas User</h4>
							</div>
							<div class="card-body">
								<div class="media-list list-group" id="div-activity">
									
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
	function getCollectionTypeStatus(){
		$.ajax({
			url: '/admin/dashboard/collection_type_status',
			method : 'GET',
			success : function(response){
				var txt = '<p><span class="font-weight-bold">Buku &nbsp;&nbsp;</span>'
				+ '<a href="/admin/collection/monitoring?type=1" class="font-italic" style="font-size:12px;"> ' + response['book']['review'] + ' Pemantauan</a>'
				+ ' &nbsp;|&nbsp;<a href="/admin/collection/manage/1" class="font-italic" style="font-size:12px;">'+ response['book']['manage'] + ' Diterima</a>'
				+ '&nbsp;|&nbsp;<a href="/admin/collection/problem/1" class="font-italic" style="font-size:12px;">'+ response['book']['problem'] + ' Masalah</a></p>';
				txt +='<p><span class="font-weight-bold">Partitur &nbsp;&nbsp;</span>'
				+ '<a href="/admin/collection/monitoring?type=2" class="font-italic" style="font-size:12px;"> ' + response['partitur']['review'] + ' Pemantauan</a>'
				+ ' &nbsp;|&nbsp;<a href="/admin/collection/manage/2" class="font-italic" style="font-size:12px;">'+ response['partitur']['manage'] + ' Diterima</a>'
				+ '&nbsp;|&nbsp;<a href="/admin/collection/problem/2" class="font-italic" style="font-size:12px;">'+ response['partitur']['problem'] + ' Masalah</a></p>';
				txt +='<p><span class="font-weight-bold">Peta &nbsp;&nbsp;</span>'
				+ '<a href="/admin/collection/monitoring?type=3" class="font-italic" style="font-size:12px;"> ' + response['map']['review'] + ' Pemantauan</a>'
				+ ' &nbsp;|&nbsp;<a href="/admin/collection/manage/3" class="font-italic" style="font-size:12px;">'+ response['map']['manage'] + ' Diterima</a>'
				+ '&nbsp;|&nbsp;<a href="/admin/collection/problem/3" class="font-italic" style="font-size:12px;">'+ response['map']['problem'] + ' Masalah</a></p>';
				txt +='<p><span class="font-weight-bold">Serial &nbsp;&nbsp;</span>'
				+ '<a href="/admin/collection/monitoring?type=4" class="font-italic" style="font-size:12px;"> ' + response['serial']['review'] + ' Pemantauan</a>'
				+ ' &nbsp;|&nbsp;<a href="/admin/collection/manage/4" class="font-italic" style="font-size:12px;">'+ response['serial']['manage'] + ' Diterima</a>'
				+ '&nbsp;|&nbsp;<a href="/admin/collection/problem/4" class="font-italic" style="font-size:12px;">'+ response['serial']['problem'] + ' Masalah</a></p>';
				txt +='<p><span class="font-weight-bold">Audio &nbsp;&nbsp;</span>'
				+ '<a href="/admin/collection/monitoring?type=5" class="font-italic" style="font-size:12px;"> ' + response['audio']['review'] + ' Pemantauan</a>'
				+ ' &nbsp;|&nbsp;<a href="/admin/collection/manage/5" class="font-italic" style="font-size:12px;">'+ response['audio']['manage'] + ' Diterima</a>'
				+ '&nbsp;|&nbsp;<a href="/admin/collection/problem/5" class="font-italic" style="font-size:12px;">'+ response['audio']['problem'] + ' Masalah</a></p>';
				txt +='<p><span class="font-weight-bold">Film &nbsp;&nbsp;</span>'
				+ '<a href="/admin/collection/monitoring?type=6" class="font-italic" style="font-size:12px;"> ' + response['film']['review'] + ' Pemantauan</a>'
				+ ' &nbsp;|&nbsp;<a href="/admin/collection/manage/6" class="font-italic" style="font-size:12px;">'+ response['film']['manage'] + ' Diterima</a>'
				+ '&nbsp;|&nbsp;<a href="/admin/collection/problem/6" class="font-italic" style="font-size:12px;">'+ response['film']['problem'] + ' Masalah</a></p>';
				$('#collection_type_status').html(txt)
			}
		})
	}
	function getCollectionStatus(){
		$.ajax({
			url: '/admin/dashboard/collection_status',
			method : 'GET',
			success : function(response){
				
				dataPie = {	
					labels: ['Diterima', 'Review', 'Bermasalah', 'PreProcess', 'Ditolak'],
					datasets: [{
						label: 'Koleksi',
						data: [
							response['collection_accept'],
							response['collection_review'],
							response['collection_problem'],
							response['collection_preprocess'],
							response['collection_reject'],
						],
						backgroundColor: ['#28A745', '#FFC107', '#DC3545', '#CCC', '#000'],
					}]
				};
				var optionPie = {
					responsive: true,
					maintainAspectRatio: false,
					responsiveAnimationDuration: 500
				};
				var selector_pie         = $('#collection_status');
				var configPie = {
					type: 'pie',
					options: optionPie,
					data: dataPie
				};
				new Chart(selector_pie, configPie);
			}
		})
	}
	function getTotalCollection(){
		$.ajax({
			url: '/admin/dashboard/total_collection',
			method : 'GET',
			success : function(response){
				var selector_bar         = $('#total_collection');
				var optionBar = {
					responsive: true,
					maintainAspectRatio: false,
					responsiveAnimationDuration: 500,
					elements: {
						rectangle: {
							borderWidth: 2,
							borderColor: 'rgb(0, 255, 0)',
							borderSkipped: 'bottom'
						}
					},
					legend: {
						position: 'top',
					},
					scales: {
						xAxes: [{
							display: true,
							gridLines: {
								color: "#f3f3f3",
								drawTicks: false,
							},
							scaleLabel: {
								display: true,
							}
						}],
						yAxes: [{
							display: true,
							gridLines: {
								color: "#f3f3f3",
								drawTicks: false,
							},
							scaleLabel: {
								display: true,
							}
						}]
					}
				};
				
				var dataBar = {
					labels: [
						'Buku',
						'Partitur',
						'Peta',
						'Serial',
						'Audio',
						'Film'
					],
					datasets: [
						{
							label: 'Total',
							data: [
								response["book"],
								response["partitur"],
								response["map"],
								response["serial"],
								response["audio"],
								response["film"],
							],
							backgroundColor: '#17A2B8',
							hoverBackgroundColor: '#17A2B8',
							borderColor: 'transparent'
						}
					]
				};
				var configBar = {
					type: 'bar',
					options: optionBar,
					data: dataBar
				};
				new Chart(selector_bar, configBar);
			}
		})
	}
	function getCollectionLastDay(){
		$.ajax({
			url: '/admin/dashboard/collection_last_day',
			method : 'GET',
			success : function(response){
				var dataChart = {
					labels: [
						'Buku',
						'Partitur',
						'Peta',
						'Serial',
						'Audio',
						'Film'
					],
					datasets: [
						{
							label: 'Total',
							data: [
								response["book"],
								response["partitur"],
								response["map"],
								response["serial"],
								response["audio"],
								response["film"],
							],
							backgroundColor: '#17A2B8',
							hoverBackgroundColor: '#17A2B8',
							borderColor: 'transparent'
						}
					]
				};
				var option = {
					responsive: true,
					maintainAspectRatio: false,
					responsiveAnimationDuration: 500
				};
				var selector        = $('#collection_last_day');

				var config = {
					type: 'line',
					options: option,
					data: dataChart
				};
				new Chart(selector, config);
			}
		})
	}
	function getFileType(){
		$.ajax({
			url: '/admin/dashboard/file_type',
			method : 'GET',
			success : function(response){
				var selector_stacked_bar = $('#file_type');
				var optionStackedBar = {
					responsive: true,
					maintainAspectRatio: false,
					responsiveAnimationDuration: 500
				};
				var dataStackedBar = {
					labels: [
						'PDF',
						'WAV',
						'EPUB',
						'JFIF',
						'MP3',
						'PNG',
						'JPEG/JPG'
					],
					datasets: [
						{
							label: 'Total',
							data: [
								response["pdf"],
								response["wav"],
								response["epub"],
								response["jfif"],
								response["mp3"],
								response["png"],
								parseInt(response["jpeg"]) + parseInt(response["jpg"])
							],
							backgroundColor: '#17A2B8',
							hoverBackgroundColor: '#17A2B8',
							borderColor: 'transparent'
						}
					]
				};
				var configStackedBar = {
					type: 'horizontalBar',
					options: optionStackedBar,
					data: dataStackedBar
				};
				new Chart(selector_stacked_bar, configStackedBar);
			}
		})
	}
	function getCollectionMonitoring(){
		$.ajax({
			url: '/admin/dashboard/collection_monitoring',
			method : 'GET',
			success : function(response){
				
				$.each(response, function(i, val){
					var txt = '<a href="/admin/collection/monitoring/review/' + val['id'] + '" class="list-group-item list-group-item-action media">'
							+'<div class="media-left"><i class="'+ val['icon'] +'"></i></div><div class="media-body">'
							+'<h6 class="list-group-item-heading" style="overflow:hidden;">' + val['title'] + '</h6></div>'
							+ '<small class="text-muted">' + val['created_at'] +'</small></a>';
					$('#div-pemantauan').append(txt);
				})
			}
		});
	}
	function getActivity(){
		$.ajax({
			url: '/admin/dashboard/activity',
			method : 'GET',
			success : function(response){
				
				$.each(response, function(i, val){
					var txt = '<a href="javascript:void(0);" style="pointer-events:none;" class="list-group-item list-group-item-action media">'
							+ '<div class="media-body">'
							+'<h6 class="list-group-item-heading" style="overflow:hidden;">' + val['description'] + '</h6>'
							+'</div><small class="text-muted">' + val['user']['admin']['fullname'] + '-' + val['created_at'] + '</small></a>';
					$('#div-activity').append(txt);
				})
			}
		});	
	}
	getCollectionTypeStatus();
	getCollectionStatus();
	getCollectionLastDay();
	getTotalCollection();
	getFileType();
	getCollectionMonitoring();
	getActivity();
	$(function() {
		$('.table').DataTable();
	});
</script>
