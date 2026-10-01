<?php
/** @var string $role */
/** @var ?string $jenis */
/** @var string $tahun */
/** @var string $search */
/** @var array $pg */
/** @var array $kenaikan */
$view_file = 'admin/kenaikan/index';
$status_labels = ['naik' => 'Naik Kelas', 'tetap' => 'Tetap Kelas', 'lulus' => 'Lulus Program'];
?>

<div class="page-header">
  <div>
    <h1 class="page-title"><i class="bi bi-arrow-up-right-circle-fill me-2 text-success"></i>Kenaikan Kelas</h1>
    <p class="page-subtitle"><?= $jenis ? 'Kelola kenaikan kelas program ' . strtoupper(e($jenis)) : 'Rekap kenaikan kelas seluruh program' ?></p>
  </div>
  <div class="d-flex gap-2">
    <a href="<?= BASE_URL ?>/kenaikan/export?tahun=<?= urlencode($tahun) ?>&q=<?= urlencode($search) ?>" class="btn btn-outline-success">
      <i class="bi bi-file-earmark-excel-fill me-1"></i>Export Excel
    </a>
    <?php if ($role !== 'webmaster'): ?>
      <a href="<?= BASE_URL ?>/kenaikan/tambah" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Catat Kenaikan</a>
    <?php endif; ?>
  </div>
</div>

<div class="card mb-4">
  <div class="card-body">
    <form method="get" class="row g-2 align-items-end">
      <div class="col-md-5">
        <label class="form-label">Cari Santri</label>
        <input class="form-control" name="q" value="<?= e($search) ?>" placeholder="Nama atau NIS pesantren">
      </div>
      <div class="col-md-3">
        <label class="form-label">Tahun Ajaran</label>
        <input class="form-control" name="tahun" value="<?= e($tahun) ?>" placeholder="2026/2027">
      </div>
      <div class="col-md-2"><button class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Filter</button></div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header">Riwayat Kenaikan <span class="badge bg-primary ms-1"><?= $pg['total'] ?></span></div>
  <div class="table-responsive">
    <table class="table table-custom mb-0">
      <thead><tr><th>#</th><th>Santri</th><th>Program</th><th>Dari</th><th>Menjadi</th><th>Tahun</th><th>Status</th><th>Diproses Oleh</th><th>Surat</th><?php if ($role !== 'webmaster'): ?><th>Aksi</th><?php endif; ?></tr></thead>
      <tbody>
      <?php if (empty($kenaikan)): ?>
        <tr><td colspan="<?= $role === 'webmaster' ? 9 : 10 ?>" class="text-center py-5 text-muted"><i class="bi bi-arrow-up-right-circle fs-2 d-block mb-2"></i>Belum ada data kenaikan kelas.</td></tr>
      <?php else: foreach ($kenaikan as $index => $item): ?>
        <tr>
          <td><?= $pg['offset'] + $index + 1 ?></td>
          <td><div class="fw-semibold"><?= e($item['nama_lengkap']) ?></div><small class="text-muted"><?= e($item['nis_pesantren'] ?: 'NIS belum ada') ?></small></td>
          <td><span class="badge bg-secondary text-uppercase"><?= e($item['jenis_jenjang']) ?></span></td>
          <td><strong><?= e($item['jenjang_asal']) ?></strong><?= $item['kelas_asal'] ? '<div class="small text-muted">' . e($item['kelas_asal']) . '</div>' : '' ?></td>
          <td>
            <?php if ($item['status'] === 'lulus'): ?><span class="text-success fw-semibold">Selesai program</span>
            <?php else: ?><strong><?= e($item['jenjang_tujuan']) ?></strong><?= $item['kelas_tujuan'] ? '<div class="small text-muted">' . e($item['kelas_tujuan']) . '</div>' : '' ?><?php endif; ?>
          </td>
          <td><?= e($item['tahun_ajaran']) ?></td>
          <td><span class="badge <?= $item['status'] === 'naik' ? 'bg-success' : ($item['status'] === 'tetap' ? 'bg-warning text-dark' : 'bg-primary') ?>"><?= e($status_labels[$item['status']]) ?></span></td>
          <td><?= e($item['pemroses_nama'] ?? '-') ?></td>
          <td>
            <a href="<?= BASE_URL ?>/surat/kenaikan/<?= $item['id'] ?>" target="_blank" class="btn btn-sm btn-outline-primary" title="Cetak Surat Keterangan">
              <i class="bi bi-printer"></i>
            </a>
          </td>
          <?php if ($role !== 'webmaster'): ?>
          <td>
            <form action="<?= BASE_URL ?>/kenaikan/hapus/<?= $item['id'] ?>" method="post" data-confirm="Hapus catatan kenaikan kelas terbaru ini?">
              <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
              <button class="btn btn-sm btn-outline-danger" title="Hapus catatan terbaru"><i class="bi bi-trash"></i></button>
            </form>
          </td>
          <?php endif; ?>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
