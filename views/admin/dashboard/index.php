<?php
// Inject view file path for layout
/** @var array $stats */
/** @var array $recent_kegiatan */
/** @var array $recent_daftar */
/** @var string $role */
/** @var array $current_user */
/** @var array $pengaturan */
$view_file = 'admin/dashboard/index';
?>
<?php
// Role-based color mapping for jenjang
$roleColors = [
  'webmaster' => 'purple',
  'kegiatan'  => 'green',
  'diniyyah'  => 'yellow',
  'tpq'       => 'green',
];
$iconColor = $roleColors[$role] ?? 'blue';
?>

<!-- Page Header -->
<div class="page-header">
  <div>
    <h1 class="page-title"><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard</h1>
    <p class="page-subtitle">Selamat datang, <strong><?= e($current_user['nama']) ?></strong> — <?= date('l, d F Y') ?></p>
  </div>
  <div>
    <a href="<?= BASE_URL ?>" target="_blank" class="btn btn-outline-primary btn-sm">
      <i class="bi bi-globe me-1"></i>Lihat Website
    </a>
  </div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">

  <?php if ($role === 'webmaster'): ?>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="stat-icon purple"><i class="bi bi-people-fill"></i></div>
        <div>
          <div class="stat-value"><?= number_format($stats['total_santri']) ?></div>
          <div class="stat-label">Total Santri</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="stat-icon blue"><i class="bi bi-person-badge-fill"></i></div>
        <div>
          <div class="stat-value"><?= number_format($stats['total_users']) ?></div>
          <div class="stat-label">Admin Aktif</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-journal-text"></i></div>
        <div>
          <div class="stat-value"><?= number_format($stats['total_kegiatan']) ?></div>
          <div class="stat-label">Total Konten</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="stat-icon yellow"><i class="bi bi-hourglass-split"></i></div>
        <div>
          <div class="stat-value"><?= number_format($stats['pending_all']) ?></div>
          <div class="stat-label">Pendaftaran Pending</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="stat-icon teal"><i class="bi bi-award-fill"></i></div>
        <div>
          <div class="stat-value"><?= number_format($stats['total_kelulusan']) ?></div>
          <div class="stat-label">Lulusan</div>
        </div>
      </div>
    </div>

  <?php elseif ($role === 'kegiatan'): ?>
    <div class="col-6 col-md-4">
      <div class="stat-card">
        <div class="stat-icon blue"><i class="bi bi-newspaper"></i></div>
        <div>
          <div class="stat-value"><?= $stats['total_berita'] ?></div>
          <div class="stat-label">Total Berita</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-4">
      <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-file-text-fill"></i></div>
        <div>
          <div class="stat-value"><?= $stats['total_artikel'] ?></div>
          <div class="stat-label">Total Artikel</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-4">
      <div class="stat-card">
        <div class="stat-icon yellow"><i class="bi bi-camera-fill"></i></div>
        <div>
          <div class="stat-value"><?= $stats['total_galeri'] ?></div>
          <div class="stat-label">Total Galeri</div>
        </div>
      </div>
    </div>

  <?php else: // diniyyah, tpq ?>
    <div class="col-12 col-md-4">
      <div class="stat-card">
        <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
        <div>
          <div class="stat-value"><?= number_format($stats['total_santri']) ?></div>
          <div class="stat-label">Total Santri</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-4">
      <div class="stat-card">
        <div class="stat-icon yellow"><i class="bi bi-hourglass-split"></i></div>
        <div>
          <div class="stat-value"><?= $stats['pending'] ?></div>
          <div class="stat-label">Menunggu Validasi</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-4">
      <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
        <div>
          <div class="stat-value"><?= $stats['diterima'] ?></div>
          <div class="stat-label">Diterima</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-4">
      <div class="stat-card">
        <div class="stat-icon teal"><i class="bi bi-award-fill"></i></div>
        <div>
          <div class="stat-value"><?= $stats['lulus'] ?></div>
          <div class="stat-label">Lulus</div>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>

<!-- Content Grid -->
<div class="row g-4">

  <!-- Pendaftaran Terbaru -->
  <?php if (!empty($recent_daftar)): ?>
  <div class="col-lg-7">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-clipboard-check me-2 text-primary"></i>Pendaftaran Terbaru</span>
        <a href="<?= BASE_URL ?>/pendaftaran" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-custom mb-0">
            <thead>
              <tr>
                <th>Nama Santri</th>
                <th>Jenjang</th>
                <th>Status</th>
                <th>Tanggal</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recent_daftar as $d): ?>
              <tr class="status-<?= $d['status'] ?>">
                <td>
                  <div class="fw-semibold"><?= e($d['nama_lengkap']) ?></div>
                  <small class="text-muted"><?= e($d['no_hp_wali']) ?></small>
                </td>
                <td>
                  <span class="badge bg-secondary"><?= e($d['jenis_jenjang']) ?></span>
                  <div class="small text-muted mt-1"><?= e($d['jenjang_dipilih']) ?></div>
                  <?php if (!empty($d['tingkat_dipilih'])): ?><div class="small text-muted">Kelas: <?= e($d['tingkat_dipilih']) ?></div><?php endif; ?>
                </td>
                <td><?= status_badge($d['status']) ?></td>
                <td class="text-muted small"><?= format_tanggal($d['created_at'], 'd M Y') ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Konten Terbaru -->
  <div class="col-lg-<?= !empty($recent_daftar) ? '5' : '12' ?>">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-journal-text me-2 text-success"></i>Konten Terbaru</span>
        <?php if (in_array($role, ['kegiatan','webmaster'])): ?>
          <a href="<?= BASE_URL ?>/kegiatan" class="btn btn-sm btn-outline-success">Kelola Konten</a>
        <?php endif; ?>
      </div>
      <div class="card-body p-0">
        <?php if (empty($recent_kegiatan)): ?>
          <div class="text-center py-4 text-muted">
            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
            Belum ada konten
          </div>
        <?php else: ?>
        <div class="list-group list-group-flush">
          <?php foreach ($recent_kegiatan as $k): ?>
          <a href="<?= BASE_URL ?>/berita/<?= e($k['slug']) ?>" target="_blank" class="list-group-item list-group-item-action py-3">
            <div class="d-flex gap-3 align-items-start">
              <?php if (!empty($k['gambar'])): ?>
                <?php if (is_video_file($k['gambar'])): ?>
                  <video src="<?= UPLOAD_URL . e($k['gambar']) ?>" style="width:50px;height:50px;object-fit:cover;border-radius:8px;" muted preload="metadata"></video>
                <?php else: ?>
                  <img src="<?= UPLOAD_URL . e($k['gambar']) ?>" style="width:50px;height:50px;object-fit:cover;border-radius:8px;" alt="">
                <?php endif; ?>
              <?php else: ?>
                <div style="width:50px;height:50px;background:#edf4ed;border-radius:8px;display:flex;align-items:center;justify-content:center;"><i class="bi bi-image text-muted"></i></div>
              <?php endif; ?>
              <div class="flex-1 min-w-0">
                <div class="fw-semibold text-truncate" style="max-width:200px"><?= e($k['judul']) ?></div>
                <div class="small text-muted">
                  <span class="badge bg-light text-dark me-1"><?= e($k['tipe']) ?></span>
                  <?= format_tanggal($k['created_at'], 'd M Y') ?>
                </div>
              </div>
              <span class="badge <?= $k['status']==='publish' ? 'bg-success' : 'bg-secondary' ?>"><?= $k['status'] ?></span>
            </div>
          </a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

</div>

<!-- PSB Status Banner (Webmaster only) -->
<?php if ($role === 'webmaster'): ?>
<div class="mt-4">
  <div class="card border-0" style="background: linear-gradient(135deg, <?= $pengaturan['psb_aktif'] ? '#16835b' : '#68776d' ?>, <?= $pengaturan['psb_aktif'] ? '#0f766e' : '#4b5d52' ?>); color:#fff;">
    <div class="card-body d-flex justify-content-between align-items-center">
      <div>
        <h6 class="mb-1 fw-bold"><i class="bi bi-door-<?= $pengaturan['psb_aktif'] ? 'open' : 'closed' ?>-fill me-2"></i>
          Pendaftaran Santri Baru (PSB) saat ini: <strong><?= $pengaturan['psb_aktif'] ? 'DIBUKA' : 'DITUTUP' ?></strong>
        </h6>
        <small class="opacity-75">Tahun Ajaran: <?= e($pengaturan['psb_tahun_ajaran'] ?? '-') ?></small>
      </div>
      <form action="<?= BASE_URL ?>/pengaturan/psb" method="post">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="psb_aktif" value="<?= $pengaturan['psb_aktif'] ? '0' : '1' ?>">
        <button type="submit" class="btn btn-light fw-bold" style="color:<?= $pengaturan['psb_aktif'] ? '#7c4a35' : '#16835b' ?>">
          <i class="bi bi-toggle-<?= $pengaturan['psb_aktif'] ? 'on' : 'off' ?>-fill me-1"></i>
          <?= $pengaturan['psb_aktif'] ? 'Tutup PSB' : 'Buka PSB' ?>
        </button>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>
