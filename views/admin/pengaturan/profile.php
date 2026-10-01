<?php
/** @var array $user */
/** @var array $role_label */
$view_file = 'admin/pengaturan/profile';
$roleName = $role_label[$user['role']] ?? ucfirst($user['role']);
$avatar = strtoupper(substr($user['nama'], 0, 2));
$lastLogin = !empty($user['last_login']) ? date('d M Y, H:i', strtotime($user['last_login'])) : 'Belum pernah login';
$createdAt = !empty($user['created_at']) ? date('d M Y', strtotime($user['created_at'])) : '-';
?>

<div class="page-header">
  <div>
    <h1 class="page-title"><i class="bi bi-person-badge me-2 text-primary"></i>Profil Saya</h1>
    <p class="page-subtitle">Ringkasan identitas, akses, dan status akun administrator</p>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-4">
    <div class="card h-100 shadow-sm border-0">
      <div class="card-body text-center p-4">
        <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle text-white fw-bold fs-4" style="width:96px;height:96px;background:linear-gradient(135deg,#166534,#0f766e);">
          <?= e($avatar) ?>
        </div>
        <h4 class="mb-1"><?= e($user['nama']) ?></h4>
        <div class="text-primary fw-semibold"><?= e($roleName) ?></div>
        <div class="text-muted small mt-2">@<?= e($user['username']) ?></div>

        <div class="d-grid gap-2 mt-4">
          <a href="<?= BASE_URL ?>/pengaturan/akun" class="btn btn-primary">
            <i class="bi bi-pencil-square me-2"></i>Ubah Akun
          </a>
          <a href="<?= BASE_URL ?>" target="_blank" class="btn btn-outline-secondary">
            <i class="bi bi-globe me-2"></i>Lihat Website
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="row g-4">
      <div class="col-md-6">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-header bg-white border-0 fw-bold">
            <i class="bi bi-person-lines-fill me-2 text-success"></i>Informasi Akun
          </div>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span class="text-muted">Nama lengkap</span>
                <strong><?= e($user['nama']) ?></strong>
              </li>
              <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span class="text-muted">Username</span>
                <strong><?= e($user['username']) ?></strong>
              </li>
              <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span class="text-muted">Role</span>
                <strong><?= e($roleName) ?></strong>
              </li>
              <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span class="text-muted">Status</span>
                <span class="badge <?= !empty($user['aktif']) ? 'bg-success' : 'bg-secondary' ?> rounded-pill">
                  <?= !empty($user['aktif']) ? 'Aktif' : 'Non-Aktif' ?>
                </span>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-header bg-white border-0 fw-bold">
            <i class="bi bi-clock-history me-2 text-info"></i>Aktivitas
          </div>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span class="text-muted">Login terakhir</span>
                <strong><?= e($lastLogin) ?></strong>
              </li>
              <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span class="text-muted">Bergabung</span>
                <strong><?= e($createdAt) ?></strong>
              </li>
              <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                <span class="text-muted">Hak akses</span>
                <strong><?= e($roleName) ?></strong>
              </li>
              <li class="list-group-item px-0">
                <small class="text-muted">Akun ini dipakai untuk mengelola data operasional dan konten website pesantren.</small>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
