<div class="app-content content">
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-6 col-12 mb-2">
				<h3 class="content-header-title mb-1 d-inline-block">Pengaturan Hak Akses</h3><br>
				<div class="row breadcrumbs-top d-inline-block">
					<div class="breadcrumb-wrapper col-12">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></li>
							<li class="breadcrumb-item"><a href="#">Pengaturan</a></li>
							<li class="breadcrumb-item"><a href="{{ url('admin/setting/role') }}">Role</a></li>
							<li class="breadcrumb-item active">Hak Akses</li>
						</ol>
					</div>
				</div>
			</div>
			<div class="content-header-right col-md-6 col-12 mb-2 mt-1">
				<div class="float-md-right">
					<a href="{{ url('admin/setting/role') }}" class="btn btn-secondary">Kembali</a>
				</div>
			</div>
		</div>
		<div class="content-body">
			<section id="configuration">
				<div class="row">
					<div class="col-12">
						<div class="card">
							<div class="card-header">
								<h4 class="card-title">Daftar Hak Akses <b class="font-italic">{{ $role->name }}</b></h4>
							</div>
							<div class="card-content collapse show">
								<div class="card-body card-dashboard">
									<div class="table-responsive">
										<table class="table table-striped table-bordered">
											<thead>
												<tr>
													<th>Menu</th>
													<th>Link</th>
													<th class="text-center">Perizinan</th>
												</tr>
											</thead>
											<tbody>
												@foreach($menu as $m)
													<tr>
														<td>
															<i class="{{ $m->icon }}"></i>
															{{ $m->name }}
														</td>
														<td>
														{{ $m->url }}
														</td>
														<td class="text-center">
															@if($m->url)
																<input type="checkbox" id="permission_{{ $m->id }}" value="{{ $m->id }}" onclick="permission({{ $m->id }}, 'menu')" {{ $m->checkPermission($role->id) ? 'checked' : '' }}>
															@else
																<span class="badge bg-info">Is Parent</span>
															@endif
														</td>
													</tr>
													<tr>
														@foreach($m->child() as $c)
															<tr>
																<td>
																	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&rarr; {{ $c->name }}
																</td>
																<td>
																	{{ $c->url }}
																</td>
																<td class="text-center">
																	<input type="checkbox" id="permission_{{ $c->id }}" value="{{ $c->id }}" onclick="permission({{ $c->id }}, 'menu')" {{ $c->checkPermission($role->id) ? 'checked' : '' }}>
																</td>
															</tr>
														@endforeach
													</tr>
												@endforeach
											</tbody>
											<tfoot>
												<tr>
													<th colspan="3" class="text-center">Lain - Lain</th>
												</tr>
												<tr>
													<td colspan="2">Hapus Koleksi</td>
													<td class="text-center">
														<input type="checkbox" id="permission_other_1" value="1" onclick="permission(1, 'other')" {{ App\Helper\GeneralHelper::checkPermissionCertain($role->id, 1) ? 'checked' : '' }}>
													</td>
												</tr>
												<tr>
													<td colspan="2">Kunci Koleksi</td>
													<td class="text-center">
														<input type="checkbox" id="permission_other_2" value="2" onclick="permission(2, 'other')" {{ App\Helper\GeneralHelper::checkPermissionCertain($role->id, 2) ? 'checked' : '' }}>
													</td>
												</tr>
                                                <tr>
													<td colspan="2">Melihat Kinerja Semua User</td>
													<td class="text-center">
														<input type="checkbox" id="permission_other_3" value="3" onclick="permission(3, 'other')" {{ App\Helper\GeneralHelper::checkPermissionCertain($role->id, 3) ? 'checked' : '' }}>
													</td>
												</tr>
											</tfoot>
										</table>
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
	function permission(id, type) {
		$.ajax({
			url: '{{ url("admin/setting/role/user_access/checkbox_permission") }}',
			type: 'POST',
			dataType: 'JSON',
			data: {
				role_id: '{{ $role->id }}',
				menu_id: $('#permission_' + id).val(),
				access: $('#permission_other_' + id).val(),
				type: type
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
	}
</script>
