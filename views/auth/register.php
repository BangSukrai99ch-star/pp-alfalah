<?php /** @var ?string $error */ ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Akun - <?= e($pengaturan['nama_pesantren'] ?? 'Pesantren') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root { --primary:#166534; --secondary:#0f766e; }
    body { min-height:100vh; margin:0; padding:24px; display:flex; align-items:center; justify-content:center; font-family:'Inter',sans-serif; background:linear-gradient(135deg,#13251b,#14532d,#0f766e); }
    .register-card { width:100%; max-width:460px; padding:36px; border-radius:20px; background:rgba(255,255,255,.96); box-shadow:0 20px 60px rgba(0,0,0,.28); }
    .brand-mark { width:64px; height:64px; margin:0 auto 16px; border-radius:16px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.6rem; background:linear-gradient(135deg,var(--primary),var(--secondary)); }
    .form-label { color:#374151; font-size:.85rem; font-weight:600; }
    .form-control { padding:10px 14px; border:2px solid #e5e7eb; border-radius:10px; }
    .form-control:focus { border-color:var(--primary); box-shadow:0 0 0 4px rgba(22,101,52,.1); }
    .btn-register { width:100%; padding:11px; border:0; border-radius:10px; color:#fff; font-weight:700; background:linear-gradient(135deg,var(--primary),var(--secondary)); }
    .notice { font-size:.78rem; color:#64748b; }
  </style>
</head>
<body>
  <div class="register-card">
    <div class="text-center mb-4">
      <div class="brand-mark"><i class="bi bi-person-plus-fill"></i></div>
      <h1 class="h4 fw-bold mb-1">Daftar Akun Pengguna</h1>
      <p class="text-muted small mb-0">Buat akun untuk mengakses layanan pengguna Pesantren Al Falah.</p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger d-flex gap-2 align-items-center small"><i class="bi bi-exclamation-triangle-fill"></i><span><?= e($error) ?></span></div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/register" method="post" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <div class="mb-3">
        <label class="form-label" for="nama">Nama Lengkap</label>
        <input class="form-control" id="nama" name="nama" value="<?= e($_POST['nama'] ?? '') ?>" required autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label" for="email">Email</label>
        <input class="form-control" type="email" id="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" autocomplete="email" required>
        <div class="form-text">Email digunakan untuk masuk ke akun Anda.</div>
      </div>
      <div class="mb-3">
        <label class="form-label" for="password">Password</label>
        <input class="form-control" type="password" id="password" name="password" minlength="8" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="password_konfirmasi">Konfirmasi Password</label>
        <input class="form-control" type="password" id="password_konfirmasi" name="password_konfirmasi" minlength="8" required>
      </div>
      <p class="notice mb-3"><i class="bi bi-shield-check-fill text-success me-1"></i>Pendaftaran ini hanya membuat akun pengguna biasa, bukan akun admin.</p>
      <button class="btn-register" type="submit"><i class="bi bi-person-check-fill me-2"></i>Buat Akun</button>
    </form>

    <p class="text-center small mt-4 mb-0">Sudah memiliki akun? <a class="fw-semibold text-success" href="<?= BASE_URL ?>/login">Login di sini</a></p>
    <p class="text-center small mt-2 mb-0"><a class="text-muted text-decoration-none" href="<?= BASE_URL ?>"><i class="bi bi-arrow-left me-1"></i>Kembali ke website</a></p>
  </div>
</body>
</html>
