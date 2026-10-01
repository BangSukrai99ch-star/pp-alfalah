<?php
/** @var array $kelulusan */
/** @var array $pg */
/** @var string $search */
/** @var string $status */
/** @var string $tahun */
/** @var string $role */
/** @var ?string $jenjang_scope */
$view_file = 'admin/kelulusan/index';
$labels = ['lulus' => 'Lulus', 'tidak_lulus' => 'Tidak Lulus', 'ditunda' => 'Ditunda'];
?>

<div class="page-header">
  <div>
    <h1 class="page-title"><i class="bi bi-award-fill me-2 text-primary"></i>Data Kelulusan</h1>
    <p class="page-subtitle"><?= $jenjang_scope ? 'Kelulusan ' . ucfirst(e($jenjang_scope)) : 'Rekap kelulusan seluruh jenjang' ?></p>
  </div>
  <div class="d-flex gap-2">
    <a href="<?= BASE_URL ?>/kelulusan/export?status=<?= urlencode($status) ?>&q=<?= urlencode($search) ?>&tahun=<?= urlencode($tahun) ?>" class="btn btn-outline-success">
      <i class="bi bi-file-earmark-excel-fill me-1"></i>Export Excel
    </a>
    <?php if ($role !== 'webmaster'): ?>
      <a href="<?= BASE_URL ?>/kelulusan/tambah" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Catat Kelulusan</a>
    <?php endif; ?>
  </div>
</div>

<div class="card mb-4">
  <div class="card-body">
    <form method="get" class="row g-2 align-items-end">
      <div class="col-md-5">
        <label class="form-label">Cari</label>
        <input type="text" class="form-control" name="q" value="<?= e($search) ?>" placeholder="Nama, NIS, atau jenjang...">
      </div>
      <div class="col-md-2">
        <label class="form-label">Tahun Ajaran</label>
        <input type="text" class="form-control" name="tahun" value="<?= e($tahun) ?>" placeholder="2025/2026">
      </div>
      <div class="col-md-3">
        <label class="form-label">Status</label>
        <select class="form-select" name="status">
          <option value="">Semua Status</option>
          <?php foreach ($labels as $key => $label): ?>
            <option value="<?= $key ?>" <?= $status === $key ? 'selected' : '' ?>><?= $label ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2"><button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Filter</button></div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span>Daftar Kelulusan <span class="badge bg-primary ms-1"><?= $pg['total'] ?></span></span>
  </div>
  <div class="table-responsive">
    <table class="table table-custom mb-0">
      <thead><tr><th>#</th><th>Santri</th><th>Jenjang</th><th>Tahun</th><th>Tanggal Lulus</th><th>Status</th><th>Predikat</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php if (empty($kelulusan)): ?>
        <tr><td colspan="8" class="text-center py-5 text-muted"><i class="bi bi-award fs-2 d-block mb-2"></i>Belum ada data kelulusan</td></tr>
      <?php else: foreach ($kelulusan as $i => $row): ?>
        <tr>
          <td class="text-muted"><?= $pg['offset'] + $i + 1 ?></td>
          <td><div class="fw-semibold"><?= e($row['nama_lengkap']) ?></div><small class="text-muted"><?= e($row['nis_pesantren'] ?? 'NIS belum ada') ?></small></td>
          <td><span class="badge bg-secondary"><?= ucfirst(e($row['jenis_jenjang'])) ?></span><div class="small mt-1"><?= e($row['jenjang']) ?></div></td>
          <td class="text-muted"><?= e($row['tahun_ajaran']) ?></td>
          <td><?= $row['tanggal_lulus'] ? format_tanggal($row['tanggal_lulus'], 'd M Y') : '-' ?></td>
          <td><?= status_badge($row['status']) ?></td>
          <td><?= e($row['predikat'] ?? '-') ?></td>
          <td>
            <?php if ($role !== 'webmaster'): ?>
              <div class="d-flex gap-1">
                <a href="<?= BASE_URL ?>/surat/kelulusan/<?= $row['id'] ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="Cetak Surat Keterangan"><i class="bi bi-printer"></i></a>
                <a href="<?= BASE_URL ?>/kelulusan/edit/<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                <form action="<?= BASE_URL ?>/kelulusan/hapus/<?= $row['id'] ?>" method="post" data-confirm="Hapus data kelulusan ini?">
                  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                  <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                </form>
              </div>
            <?php else: ?>
              <a href="<?= BASE_URL ?>/surat/kelulusan/<?= $row['id'] ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="Cetak Surat Keterangan"><i class="bi bi-printer"></i></a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
  <?php if ($pg['total_pages'] > 1): ?>
  <div class="card-footer"><nav><ul class="pagination pagination-sm mb-0 justify-content-center">
    <?php for ($p = max(1, $pg['page'] - 2); $p <= min($pg['total_pages'], $pg['page'] + 2); $p++): ?>
      <li class="page-item <?= $p === $pg['page'] ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $p ?>&q=<?= urlencode($search) ?>&status=<?= urlencode($status) ?>&tahun=<?= urlencode($tahun) ?>"><?= $p ?></a></li>
    <?php endfor; ?>
  </ul></nav></div>
  <?php endif; ?>
</div>
