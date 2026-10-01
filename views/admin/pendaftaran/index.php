<?php $view_file = 'admin/pendaftaran/index'; ?>
<?php
$jenjang_label = [
  'diniyyah' => 'Jenjang Diniyyah',
  'tpq'      => 'TPQ',
  null        => 'Semua Jenjang',
];
?>

<div class="page-header">
  <div>
    <h1 class="page-title"><i class="bi bi-clipboard-check-fill me-2 text-primary"></i>Validasi Pendaftaran Santri Baru</h1>
    <p class="page-subtitle"><?= $jenjang_label[$jenjang] ?? 'Semua Jenjang' ?></p>
  </div>
  <a href="<?= BASE_URL ?>/pendaftaran/export?status=<?= urlencode($filter_status) ?>&q=<?= urlencode($filter_search) ?>"
     class="btn btn-outline-success">
    <i class="bi bi-file-earmark-excel-fill me-1"></i>Export Excel
  </a>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="stat-icon blue"><i class="bi bi-collection-fill"></i></div>
      <div><div class="stat-value"><?= $summary['total'] ?></div><div class="stat-label">Total</div></div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="stat-icon yellow"><i class="bi bi-hourglass-split"></i></div>
      <div><div class="stat-value"><?= $summary['pending'] ?></div><div class="stat-label">Pending</div></div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
      <div><div class="stat-value"><?= $summary['diterima'] ?></div><div class="stat-label">Diterima</div></div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-card">
      <div class="stat-icon red"><i class="bi bi-x-circle-fill"></i></div>
      <div><div class="stat-value"><?= $summary['ditolak'] ?></div><div class="stat-label">Ditolak</div></div>
    </div>
  </div>
</div>

<!-- Filter -->
<div class="card mb-4">
  <div class="card-body">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-5">
        <label class="form-label">Cari Santri</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control" name="q" placeholder="Nama atau no. HP wali..."
                 value="<?= e($filter_search) ?>" id="searchInput">
        </div>
      </div>
      <div class="col-md-3">
        <label class="form-label">Status</label>
        <select class="form-select" name="status" onchange="this.form.submit()">
          <option value="">Semua Status</option>
          <option value="pending"  <?= $filter_status==='pending'  ? 'selected' : '' ?>>Pending</option>
          <option value="diterima" <?= $filter_status==='diterima' ? 'selected' : '' ?>>Diterima</option>
          <option value="ditolak"  <?= $filter_status==='ditolak'  ? 'selected' : '' ?>>Ditolak</option>
        </select>
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">Filter</button>
      </div>
      <div class="col-md-2">
        <a href="<?= BASE_URL ?>/pendaftaran" class="btn btn-outline-secondary w-100">Reset</a>
      </div>
    </form>
  </div>
</div>

<!-- Table -->
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span>Daftar Pendaftaran <span class="badge bg-primary ms-1"><?= $pg['total'] ?></span></span>
  </div>
  <div class="table-responsive">
    <table class="table table-custom mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>Nama Santri</th>
          <th>Jenjang</th>
          <th>Program Dipilih</th>
          <th>Thn. Ajaran</th>
          <th>Status</th>
          <th>Tgl. Daftar</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($data_daftar)): ?>
        <tr>
          <td colspan="8" class="text-center py-5 text-muted">
            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
            Tidak ada data pendaftaran
          </td>
        </tr>
        <?php else: ?>
        <?php foreach ($data_daftar as $i => $d): ?>
        <tr class="status-<?= $d['status'] ?>">
          <td class="text-muted"><?= ($pg['offset'] + $i + 1) ?></td>
          <td>
            <div class="fw-semibold"><?= e($d['nama_lengkap']) ?></div>
            <small class="text-muted"><i class="bi bi-telephone-fill me-1"></i><?= e($d['no_hp_wali']) ?></small>
            <?php if ($d['jenis_kelamin'] === 'L'): ?>
              <span class="badge bg-info bg-opacity-10 text-info ms-1" style="font-size:.65rem">L</span>
            <?php else: ?>
              <span class="badge bg-danger bg-opacity-10 text-danger ms-1" style="font-size:.65rem">P</span>
            <?php endif; ?>
          </td>
          <td><span class="badge bg-secondary"><?= ucfirst(e($d['jenis_jenjang'])) ?></span></td>
          <td class="fw-medium">
            <?= e($d['jenjang_dipilih']) ?>
            <?php if (!empty($d['tingkat_dipilih'])): ?>
              <div class="small text-muted">Kelas: <?= e($d['tingkat_dipilih']) ?></div>
            <?php endif; ?>
          </td>
          <td class="text-muted small"><?= e($d['tahun_ajaran'] ?? '-') ?></td>
          <td><?= status_badge($d['status']) ?></td>
          <td class="text-muted small"><?= format_tanggal($d['created_at'], 'd M Y') ?></td>
          <td>
            <div class="d-flex gap-1">
              <a href="<?= BASE_URL ?>/pendaftaran/detail/<?= $d['id'] ?>" class="btn btn-sm btn-outline-primary" title="Detail">
                <i class="bi bi-eye"></i>
              </a>
              <?php if ($d['status'] === 'pending'): ?>
              <button type="button" class="btn btn-sm btn-success btn-validasi"
                      data-status="diterima" data-nama="<?= e($d['nama_lengkap']) ?>"
                      data-bs-toggle="modal" data-bs-target="#modalValidasi"
                      data-id="<?= $d['id'] ?>" data-action="diterima" title="Terima">
                <i class="bi bi-check-lg"></i>
              </button>
              <button type="button" class="btn btn-sm btn-danger btn-validasi"
                      data-status="ditolak" data-nama="<?= e($d['nama_lengkap']) ?>"
                      data-bs-toggle="modal" data-bs-target="#modalValidasi"
                      data-id="<?= $d['id'] ?>" data-action="ditolak" title="Tolak">
                <i class="bi bi-x-lg"></i>
              </button>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($pg['total_pages'] > 1): ?>
  <div class="card-footer">
    <nav>
      <ul class="pagination pagination-sm mb-0 justify-content-center">
        <?php if ($pg['has_prev']): ?>
          <li class="page-item"><a class="page-link" href="?page=<?= $pg['page']-1 ?>&status=<?= $filter_status ?>&q=<?= urlencode($filter_search) ?>">‹ Prev</a></li>
        <?php endif; ?>
        <?php for ($p = max(1,$pg['page']-2); $p <= min($pg['total_pages'],$pg['page']+2); $p++): ?>
          <li class="page-item <?= $p==$pg['page']?'active':'' ?>">
            <a class="page-link" href="?page=<?= $p ?>&status=<?= $filter_status ?>&q=<?= urlencode($filter_search) ?>"><?= $p ?></a>
          </li>
        <?php endfor; ?>
        <?php if ($pg['has_next']): ?>
          <li class="page-item"><a class="page-link" href="?page=<?= $pg['page']+1 ?>&status=<?= $filter_status ?>&q=<?= urlencode($filter_search) ?>">Next ›</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
  <?php endif; ?>
</div>

<!-- Modal Validasi -->
<div class="modal fade" id="modalValidasi" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalValidasiTitle">Validasi Pendaftaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="" method="post" id="formValidasi">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="status" id="inputStatus" value="">
        <div class="modal-body">
          <p id="modalValidasiText"></p>
          <div class="mb-3">
            <label class="form-label">Catatan Admin (opsional)</label>
            <textarea class="form-control" name="catatan_admin" rows="3"
                      placeholder="Misal: Silakan hadir untuk verifikasi berkas..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn" id="btnSubmitValidasi">Konfirmasi</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Handle modal validasi
document.getElementById('modalValidasi').addEventListener('show.bs.modal', function(event) {
  const btn      = event.relatedTarget;
  const id       = btn.getAttribute('data-id');
  const action   = btn.getAttribute('data-action');
  const nama     = btn.getAttribute('data-nama');
  const form     = document.getElementById('formValidasi');
  const inputStatus = document.getElementById('inputStatus');
  const btnSubmit   = document.getElementById('btnSubmitValidasi');
  const modalText   = document.getElementById('modalValidasiText');
  const modalTitle  = document.getElementById('modalValidasiTitle');

  form.action = '<?= BASE_URL ?>/pendaftaran/validasi/' + id;
  inputStatus.value = action;

  if (action === 'diterima') {
    modalTitle.textContent = 'Terima Pendaftaran';
    modalText.innerHTML = `Apakah Anda yakin ingin <strong class="text-success">MENERIMA</strong> pendaftaran <strong>${nama}</strong>?`;
    btnSubmit.className = 'btn btn-success';
    btnSubmit.textContent = 'Ya, Terima';
  } else {
    modalTitle.textContent = 'Tolak Pendaftaran';
    modalText.innerHTML = `Apakah Anda yakin ingin <strong class="text-danger">MENOLAK</strong> pendaftaran <strong>${nama}</strong>?`;
    btnSubmit.className = 'btn btn-danger';
    btnSubmit.textContent = 'Ya, Tolak';
  }
});
</script>
