<?php
/** @var array $santri_list */
/** @var array $pg */
/** @var string $search */
/** @var string $status */
/** @var string $role */
$view_file = 'admin/santri/index';
?>

<div class="page-header">
  <div>
    <h1 class="page-title"><i class="bi bi-mortarboard-fill me-2 text-primary"></i>Data Santri</h1>
    <p class="page-subtitle">Kelola data induk santri pesantren</p>
  </div>
  <a href="<?= BASE_URL ?>/santri/export?status=<?= urlencode($status) ?>&q=<?= urlencode($search) ?>"
     class="btn btn-outline-success">
    <i class="bi bi-file-earmark-excel-fill me-1"></i>Export Excel
  </a>
</div>

<!-- Filter -->
<div class="card mb-4">
  <div class="card-body">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-5">
        <label class="form-label">Cari</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control" name="q" value="<?= e($search) ?>"
                 placeholder="Nama, NISN, atau NIS Pesantren..." id="searchInput">
        </div>
      </div>
      <div class="col-md-3">
        <label class="form-label">Status</label>
        <select class="form-select" name="status" onchange="this.form.submit()">
          <option value="">Semua Status</option>
          <option value="aktif"  <?= $status==='aktif'  ? 'selected' : '' ?>>Aktif</option>
          <option value="alumni" <?= $status==='alumni' ? 'selected' : '' ?>>Alumni</option>
          <option value="keluar" <?= $status==='keluar' ? 'selected' : '' ?>>Keluar</option>
        </select>
      </div>
      <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Cari</button></div>
      <div class="col-md-2"><a href="<?= BASE_URL ?>/santri" class="btn btn-outline-secondary w-100">Reset</a></div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span>Daftar Santri <span class="badge bg-primary ms-1"><?= $pg['total'] ?></span></span>
  </div>
  <div class="table-responsive">
    <table class="table table-custom mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>Santri</th>
          <th>JK</th>
          <th>Jenjang</th>
          <th>Status</th>
          <th>Tgl. Masuk</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($santri_list)): ?>
        <tr><td colspan="7" class="text-center py-5 text-muted">
          <i class="bi bi-inbox fs-2 d-block mb-2"></i>Tidak ada data santri
        </td></tr>
        <?php else: ?>
        <?php foreach ($santri_list as $i => $s): ?>
        <tr>
          <td class="text-muted"><?= ($pg['offset'] + $i + 1) ?></td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <?php if (!empty($s['foto'])): ?>
                <img src="<?= UPLOAD_URL . e($s['foto']) ?>" style="width:36px;height:36px;object-fit:cover;border-radius:50%;" alt="">
              <?php else: ?>
                <div style="width:36px;height:36px;background:#e2f0e4;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;color:#4f9560">
                  <?= strtoupper(substr($s['nama_lengkap'],0,2)) ?>
                </div>
              <?php endif; ?>
              <div>
                <div class="fw-semibold"><?= e($s['nama_lengkap']) ?></div>
                <small class="text-muted"><?= e($s['nis_pesantren'] ?? 'NIS belum ada') ?></small>
              </div>
            </div>
          </td>
          <td><span class="badge <?= $s['jenis_kelamin']==='L'?'bg-info':'bg-danger' ?> bg-opacity-75"><?= $s['jenis_kelamin']==='L'?'L':'P' ?></span></td>
          <td>
            <?php if ($s['jenjang_list']): ?>
              <?php foreach (explode(', ', $s['jenjang_list']) as $jl): ?>
                <span class="badge bg-light text-dark border me-1"><?= ucfirst(e(trim($jl))) ?></span>
              <?php endforeach; ?>
            <?php else: ?>
              <span class="text-muted">-</span>
            <?php endif; ?>
          </td>
          <td><?= status_badge($s['status']) ?></td>
          <td class="text-muted small"><?= format_tanggal($s['created_at'], 'd M Y') ?></td>
          <td>
            <div class="d-flex gap-1">
              <a href="<?= BASE_URL ?>/santri/detail/<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary" title="Detail">
                <i class="bi bi-eye"></i>
              </a>
              <a href="<?= BASE_URL ?>/santri/edit/<?= $s['id'] ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
              <?php if ($role === 'webmaster'): ?>
              <form action="<?= BASE_URL ?>/santri/delete/<?= $s['id'] ?>" method="post"
                    data-confirm="Hapus data santri <?= e($s['nama_lengkap']) ?>? Semua riwayat pendaftaran juga akan terhapus.">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
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
        <?php if ($pg['has_prev']): ?><li class="page-item"><a class="page-link" href="?page=<?= $pg['page']-1 ?>&q=<?= urlencode($search) ?>&status=<?= $status ?>">‹</a></li><?php endif; ?>
        <?php for ($p=max(1,$pg['page']-2); $p<=min($pg['total_pages'],$pg['page']+2); $p++): ?>
          <li class="page-item <?= $p==$pg['page']?'active':'' ?>"><a class="page-link" href="?page=<?= $p ?>&q=<?= urlencode($search) ?>&status=<?= $status ?>"><?= $p ?></a></li>
        <?php endfor; ?>
        <?php if ($pg['has_next']): ?><li class="page-item"><a class="page-link" href="?page=<?= $pg['page']+1 ?>&q=<?= urlencode($search) ?>&status=<?= $status ?>">›</a></li><?php endif; ?>
      </ul>
    </nav>
  </div>
  <?php endif; ?>
</div>
