<div class="page-header page-header-light shadow-sm mb-4">
    <div class="page-header-content d-lg-flex">
        <div class="d-flex">
            <h4 class="page-title mb-0">
                Administrasi Sistem - <span class="fw-normal">Pengaturan Antrian</span>
            </h4>
        </div>
    </div>
</div>

<div class="content pt-0">
    <div class="row g-3">
        {{-- LOKASI --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="ph-map-pin me-2 text-primary"></i>
                        <h6 class="mb-0 fw-semibold">Lokasi</h6>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" onclick="onCreateLokasi()">
                        <i class="ph-plus-circle me-1"></i> Tambah
                    </button>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Tempat paket diterima atau disinggahkan, misalnya lobi penerimaan dan ruang transit.
                    </p>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered w-100" id="table-lokasi">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 50px"><i class="ph-hash"></i></th>
                                    <th class="text-center text-nowrap" style="width: 100px"><i class="ph-gear"></i> Aksi</th>
                                    <th class="text-nowrap">Nama</th>
                                    <th class="text-nowrap">Keterangan</th>
                                    <th class="text-center text-nowrap">PC</th>
                                    <th class="text-center text-nowrap">Status</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- PC --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="ph-desktop me-2 text-primary"></i>
                        <h6 class="mb-0 fw-semibold">PC</h6>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" onclick="onCreatePc()">
                        <i class="ph-plus-circle me-1"></i> Tambah
                    </button>
                </div>
                <div class="card-body">
                    <p class="text-muted small">
                        Komputer petugas, dikenali dari alamat IP dan terikat pada satu lokasi.
                    </p>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered w-100" id="table-pc">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 50px"><i class="ph-hash"></i></th>
                                    <th class="text-center text-nowrap" style="width: 100px"><i class="ph-gear"></i> Aksi</th>
                                    <th class="text-nowrap">Alamat IP</th>
                                    <th class="text-nowrap">Lokasi</th>
                                    <th class="text-nowrap">Keterangan</th>
                                    <th class="text-nowrap">Terakhir Aktif</th>
                                    <th class="text-center text-nowrap">Status</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL LOKASI --}}
<div class="modal fade" id="modal-lokasi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-lokasi-title">Tambah Lokasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-lokasi">
                <div class="modal-body">
                    <input type="hidden" name="id" id="lokasi_id">
                    <div class="mb-3">
                        <label class="form-label">Nama Lokasi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama" id="lokasi_nama" maxlength="100" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <input type="text" class="form-control" name="keterangan" id="lokasi_keterangan" maxlength="255">
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="aktif" id="lokasi_aktif" value="1" checked>
                        <label class="form-check-label" for="lokasi_aktif">Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL PC --}}
<div class="modal fade" id="modal-pc" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-pc-title">Tambah PC</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-pc">
                <div class="modal-body">
                    <input type="hidden" name="id" id="pc_id">
                    <div class="mb-3">
                        <label class="form-label">Alamat IP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control font-monospace" name="ip_address" id="pc_ip"
                               placeholder="192.168.1.10" maxlength="45" required>
                        <small class="text-muted">IPv4 atau IPv6. Satu alamat hanya boleh terdaftar sekali.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lokasi <span class="text-danger">*</span></label>
                        <select class="form-select" name="lokasi_id" id="pc_lokasi" required>
                            <option value="">Pilih lokasi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Keterangan</label>
                        <input type="text" class="form-control" name="keterangan" id="pc_keterangan" maxlength="255">
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="aktif" id="pc_aktif" value="1" checked>
                        <label class="form-check-label" for="pc_aktif">Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const URL_LOKASI = '{{ url("administration-system/queue-setting/lokasi") }}';
    const URL_PC = '{{ url("administration-system/queue-setting/pc") }}';
    const CSRF = $('meta[name="csrf-token"]').attr('content');

    $(function() {
        window.tableLokasi = buatTabel('#table-lokasi', URL_LOKASI + '/datatable', 6);
        window.tablePc = buatTabel('#table-pc', URL_PC + '/datatable', 7);

        muatPilihanLokasi();

        $('#form-lokasi').on('submit', function(e) {
            e.preventDefault();
            simpan(URL_LOKASI + '/save', $(this).serialize(), '#modal-lokasi', function() {
                window.tableLokasi.ajax.reload(null, false);
                muatPilihanLokasi();
            });
        });

        $('#form-pc').on('submit', function(e) {
            e.preventDefault();
            simpan(URL_PC + '/save', $(this).serialize(), '#modal-pc', function() {
                window.tablePc.ajax.reload(null, false);
            });
        });
    });

    function buatTabel(selector, url, jumlahKolom) {
        const kolom = [];

        for (let i = 0; i < jumlahKolom; i++) {
            kolom.push({ orderable: false, className: 'align-middle' + (i === 0 ? ' text-center' : '') });
        }

        return $(selector).DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            searching: false,
            lengthChange: false,
            pageLength: 10,
            order: [],
            ajax: {
                url: url,
                type: 'POST',
                dataType: 'JSON',
                headers: { 'X-CSRF-TOKEN': CSRF },
                error: function(response) { responseError(response); }
            },
            columns: kolom,
        });
    }

    function muatPilihanLokasi() {
        $.get(URL_LOKASI + '/options', function(res) {
            const pilihan = $('#pc_lokasi');
            const terpilih = pilihan.val();

            pilihan.html('<option value="">Pilih lokasi</option>');

            (res.data || []).forEach(function(l) {
                pilihan.append(new Option(l.NAMA, l.ID));
            });

            if (terpilih) {
                pilihan.val(terpilih);
            }
        });
    }

    function simpan(url, data, modal, sesudah) {
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'JSON',
            headers: { 'X-CSRF-TOKEN': CSRF },
            data: data,
            success: function(res) {
                $(modal).modal('hide');
                Swal.fire({
                    title: 'Berhasil',
                    text: res.message,
                    icon: 'success',
                    customClass: { confirmButton: 'btn btn-primary' },
                });
                sesudah();
            },
            error: function(xhr) {
                const r = xhr.responseJSON || {};
                Swal.fire({
                    title: 'Gagal',
                    html: r.error ? r.error.join('<br>') : (r.message || 'Terjadi kesalahan.'),
                    icon: 'error',
                    customClass: { confirmButton: 'btn btn-primary' },
                });
            }
        });
    }

    function hapus(url, id, sesudah) {
        Swal.fire({
            title: 'Anda yakin?',
            text: 'Data yang dihapus tidak dapat dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-outline-secondary'
            },
        }).then(function(hasil) {
            if (!hasil.isConfirmed) return;

            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'JSON',
                headers: { 'X-CSRF-TOKEN': CSRF },
                data: { id: id },
                success: function(res) {
                    Swal.fire({
                        title: 'Berhasil',
                        text: res.message,
                        icon: 'success',
                        customClass: { confirmButton: 'btn btn-primary' },
                    });
                    sesudah();
                },
                error: function(xhr) {
                    const r = xhr.responseJSON || {};
                    Swal.fire({
                        title: 'Tidak dapat dihapus',
                        text: r.message || 'Terjadi kesalahan.',
                        icon: 'error',
                        customClass: { confirmButton: 'btn btn-primary' },
                    });
                }
            });
        });
    }

    // ---- Lokasi ----
    function onCreateLokasi() {
        $('#form-lokasi')[0].reset();
        $('#lokasi_id').val('');
        $('#lokasi_aktif').prop('checked', true);
        $('#modal-lokasi-title').text('Tambah Lokasi');
        $('#modal-lokasi').modal('show');
    }

    function onUpdateLokasi(id) {
        $.get(URL_LOKASI + '/show', { id: id }, function(res) {
            const d = res.data;
            $('#lokasi_id').val(d.ID);
            $('#lokasi_nama').val(d.NAMA);
            $('#lokasi_keterangan').val(d.KETERANGAN);
            $('#lokasi_aktif').prop('checked', String(d.AKTIF) === '1');
            $('#modal-lokasi-title').text('Edit Lokasi');
            $('#modal-lokasi').modal('show');
        });
    }

    function onDestroyLokasi(id) {
        hapus(URL_LOKASI + '/destroy', id, function() {
            window.tableLokasi.ajax.reload(null, false);
            muatPilihanLokasi();
        });
    }

    // ---- PC ----
    function onCreatePc() {
        $('#form-pc')[0].reset();
        $('#pc_id').val('');
        $('#pc_aktif').prop('checked', true);
        $('#modal-pc-title').text('Tambah PC');
        $('#modal-pc').modal('show');
    }

    function onUpdatePc(id) {
        $.get(URL_PC + '/show', { id: id }, function(res) {
            const d = res.data;
            $('#pc_id').val(d.ID);
            $('#pc_ip').val(d.IP_ADDRESS);
            $('#pc_lokasi').val(d.LOKASI_ID);
            $('#pc_keterangan').val(d.KETERANGAN);
            $('#pc_aktif').prop('checked', String(d.AKTIF) === '1');
            $('#modal-pc-title').text('Edit PC');
            $('#modal-pc').modal('show');
        });
    }

    function onDestroyPc(id) {
        hapus(URL_PC + '/destroy', id, function() {
            window.tablePc.ajax.reload(null, false);
        });
    }
</script>
