<?php
/** @var array $akun */
$view_file = 'admin/pengaturan/akun';
?>

<div class="page-header">
  <div>
    <h1 class="page-title"><i class="bi bi-person-gear me-2 text-primary"></i>Akun Saya</h1>
    <p class="page-subtitle">Atur identitas login dan keamanan akun admin Anda</p>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="card">
      <div class="card-header"><i class="bi bi-shield-lock-fill me-2 text-primary"></i>Informasi Akun</div>
      <div class="card-body">
        <div class="d-flex align-items-center gap-3 p-3 mb-4 rounded-3 bg-light">
          <div class="user-avatar">
            <div class="avatar-initials" style="width:48px;height:48px;background:linear-gradient(135deg,#166534,#0f766e);">
              <?= strtoupper(substr($akun['nama'], 0, 2)) ?>
            </div>
          </div>
          <div>
            <div class="fw-bold"><?= e($akun['nama']) ?></div>
            <div class="text-muted small"><?= $akun['role'] === 'webmaster' ? 'Admin Ponpes' : ucfirst(e($akun['role'])) ?></div>
          </div>
        </div>

        <form action="<?= BASE_URL ?>/pengaturan/akun_simpan" method="post">
          <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
          <div class="mb-3">
            <label class="form-label" for="nama">Nama Lengkap</label>
            <input type="text" class="form-control" id="nama" name="nama" value="<?= e($akun['nama']) ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label" for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" value="<?= e($akun['username']) ?>" minlength="3" maxlength="50" pattern="[A-Za-z0-9._-]+" required>
            <div class="form-text">3-50 karakter: huruf, angka, titik, garis bawah, atau tanda hubung.</div>
          </div>
          <hr class="my-4">
          <h6 class="fw-bold mb-3">Ganti Password <span class="text-muted fw-normal">(opsional)</span></h6>
          <div class="mb-3">
            <label class="form-label" for="password_baru">Password Baru</label>
            <input type="password" class="form-control" id="password_baru" name="password_baru" minlength="8" autocomplete="new-password">
          </div>
          <div class="mb-3">
            <label class="form-label" for="password_konfirmasi">Konfirmasi Password Baru</label>
            <input type="password" class="form-control" id="password_konfirmasi" name="password_konfirmasi" minlength="8" autocomplete="new-password">
          </div>
          <div class="mb-4">
            <label class="form-label" for="password_lama">Password Saat Ini <span class="text-danger">*</span></label>
            <input type="password" class="form-control" id="password_lama" name="password_lama" required autocomplete="current-password">
            <div class="form-text">Diperlukan untuk mengonfirmasi perubahan akun.</div>
          </div>
          <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle me-1"></i>Simpan Perubahan</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card border-danger-subtle">
      <div class="card-header text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Hapus Akun</div>
      <div class="card-body">
        <p class="text-muted small">Penghapusan akun bersifat permanen. Setelah berhasil, Anda akan logout dan tidak dapat masuk kembali dengan akun ini.</p>
        <form action="<?= BASE_URL ?>/pengaturan/akun_hapus" method="post" data-confirm="Akun Anda akan dihapus permanen dan sesi akan berakhir. Lanjutkan?">
          <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
          <label class="form-label" for="password_hapus">Password Saat Ini</label>
          <input type="password" class="form-control mb-3" id="password_hapus" name="password_hapus" required autocomplete="current-password">
          <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash3 me-1"></i>Hapus Akun Saya</button>
        </form>
      </div>
    </div>
  </div>
</div>
