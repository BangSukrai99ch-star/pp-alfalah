<?php
/** @var string $role */
/** @var bool $aktif */
$view_file = 'admin/pengaturan/psb_jenjang';
$labels = [
    'diniyyah' => ['Diniyyah', 'Diniyyah Ula, Wustho, dan Ulya', 'bi-book-half'],
    'tpq' => ['TPQ', 'Taman Pendidikan Al-Qur\'an', 'bi-journal-bookmark-fill'],
];
[$label, $description, $icon] = $labels[$role];
?>

<div class="page-header">
  <div>
    <h1 class="page-title"><i class="bi bi-calendar2-check-fill me-2 text-primary"></i>Jadwal Pendaftaran</h1>
    <p class="page-subtitle">Atur masa pendaftaran khusus <?= e($label) ?></p>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header"><i class="<?= $icon ?> me-2 text-primary"></i>Pendaftaran <?= e($label) ?></div>
      <div class="card-body">
        <div class="d-flex align-items-center gap-3 p-3 mb-4 rounded-3 <?= $aktif ? 'bg-success-subtle' : 'bg-light' ?>">
          <div class="stat-icon <?= $aktif ? 'green' : 'yellow' ?>"><i class="bi bi-<?= $aktif ? 'unlock-fill' : 'lock-fill' ?>"></i></div>
          <div>
            <div class="fw-bold">Pendaftaran <?= $aktif ? 'Sedang Dibuka' : 'Sedang Ditutup' ?></div>
            <div class="text-muted small"><?= e($description) ?></div>
          </div>
        </div>
        <p class="text-muted">Pengaturan ini hanya memengaruhi pilihan jenjang <?= e($label) ?>. Pendaftaran jenjang lain tetap mengikuti jadwal admin masing-masing.</p>
        <form action="<?= BASE_URL ?>/pengaturan/psb_jenjang" method="post">
          <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
          <input type="hidden" name="aktif" value="<?= $aktif ? '0' : '1' ?>">
          <button type="submit" class="btn <?= $aktif ? 'btn-outline-danger' : 'btn-primary' ?>">
            <i class="bi bi-toggle-<?= $aktif ? 'off' : 'on' ?>-fill me-1"></i>
            <?= $aktif ? 'Tutup Pendaftaran ' . e($label) : 'Buka Pendaftaran ' . e($label) ?>
          </button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card border-info-subtle">
      <div class="card-body">
        <h5 class="fw-bold"><i class="bi bi-info-circle-fill text-info me-2"></i>Catatan</h5>
        <ul class="text-muted small mb-0 ps-3">
          <li class="mb-2">Webmaster tetap memiliki sakelar PSB utama sebagai pengendali global.</li>
          <li class="mb-2">Jika PSB global ditutup, semua jenjang ikut tertutup.</li>
          <li>Jika PSB global dibuka, jenjang ini mengikuti status yang Anda atur di sini.</li>
        </ul>
      </div>
    </div>
  </div>
</div>
