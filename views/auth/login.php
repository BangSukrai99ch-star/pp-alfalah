<?php /** @var ?string $error */ ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Akun - <?= e($pengaturan['nama_pesantren'] ?? 'Pesantren') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root { --primary:#166534; --accent:#879b32; }
    * { box-sizing: border-box; }
    body {
      font-family:'Inter',sans-serif;
      min-height:100vh;
      background: linear-gradient(135deg, #13251b 0%, #14532d 50%, #0f766e 100%);
      display: flex; align-items: center; justify-content: center;
      padding: 20px;
      position: relative; overflow: hidden;
    }
    body::before {
      content:'';
      position:absolute; inset:0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .login-card {
      width: 100%; max-width: 420px;
      background: rgba(255,255,255,0.95);
      backdrop-filter: blur(20px);
      border-radius: 20px;
      padding: 40px 36px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
      position: relative; z-index: 1;
    }
    .login-header { text-align: center; margin-bottom: 32px; }
    .login-logo {
      width: 70px; height: 70px;
      background: linear-gradient(135deg, var(--primary), #0f766e);
      border-radius: 18px;
      display: inline-flex; align-items: center; justify-content: center;
      font-size: 1.8rem; color: #fff;
      margin-bottom: 16px;
      box-shadow: 0 8px 20px rgba(22,101,52,0.3);
    }
    .login-title { font-size: 1.3rem; font-weight: 800; color: #1e293b; margin: 0; }
    .login-sub { font-size: 0.8rem; color: #64748b; margin-top: 4px; }
    .form-label { font-weight: 600; font-size: 0.82rem; color: #374151; }
    .form-control {
      border: 2px solid #e5e7eb;
      border-radius: 10px;
      padding: 10px 14px;
      font-size: 0.9rem;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(22,101,52,0.10); outline:none; }
    .input-group .btn { border-radius: 0 10px 10px 0 !important; border: 2px solid #e5e7eb; border-left:none; background:#f9fafb; color:#6b7280; }
    .input-group .btn:hover { background:#f3f4f6; color:#374151; }
    .input-group .form-control { border-radius: 10px 0 0 10px !important; }
    .btn-login {
      background: linear-gradient(135deg, var(--primary), #0f766e);
      border: none;
      border-radius: 10px;
      padding: 11px;
      font-weight: 700;
      font-size: 0.95rem;
      color: #fff;
      width: 100%;
      transition: all 0.2s;
      box-shadow: 0 4px 14px rgba(22,101,52,0.3);
    }
    .btn-login:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(22,101,52,0.4); background: linear-gradient(135deg, #14532d, #0f766e); }
    .btn-login:active { transform: translateY(0); }
    .alert-danger { background: #f0e1d9; border: 1px solid #d8b8a8; color: #6b3a2a; border-radius: 10px; font-size: 0.875rem; }
    .login-roles {
      display: flex; flex-wrap: wrap; gap: 6px;
      margin-top: 20px; justify-content: center;
    }
    .login-role-badge {
      font-size: 0.65rem; font-weight: 600;
      padding: 3px 10px;
      border-radius: 20px;
    }
    .back-link { text-align: center; margin-top: 20px; }
    .back-link a { font-size: 0.82rem; color: #64748b; text-decoration: none; }
    .back-link a:hover { color: var(--primary); }
    .divider { text-align:center; margin: 20px 0; position:relative; }
    .divider::before { content:''; position:absolute; top:50%; left:0; right:0; height:1px; background:#e5e7eb; }
    .divider span { background:#fff; padding: 0 10px; font-size: 0.75rem; color:#9ca3af; position:relative; }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="login-header">
      <?php if (!empty($pengaturan['logo'])): ?>
        <img src="<?= UPLOAD_URL . e($pengaturan['logo']) ?>" alt="Logo" style="height:70px;border-radius:14px;margin-bottom:12px;">
      <?php else: ?>
        <div class="login-logo"><i class="bi bi-building"></i></div>
      <?php endif; ?>
      <h1 class="login-title"><?= e($pengaturan['nama_pesantren'] ?? 'Pesantren') ?></h1>
      <p class="login-sub">Halaman Masuk Akun Anda</p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger d-flex align-items-center gap-2 mb-3">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span><?= e($error) ?></span>
      </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/login" method="post" id="loginForm" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

      <div class="mb-3">
        <label class="form-label" for="identity"><i class="bi bi-person-fill me-1"></i>Email atau Username Admin</label>
        <input type="text" class="form-control" id="identity" name="identity"
               placeholder="nama@email.com atau username admin" required autofocus
               value="<?= e($_POST['identity'] ?? '') ?>">
      </div>

      <div class="mb-4">
        <label class="form-label" for="password"><i class="bi bi-lock-fill me-1"></i>Password</label>
        <div class="input-group">
          <input type="password" class="form-control" id="password" name="password"
                 placeholder="Masukkan password" required>
          <button type="button" class="btn" id="togglePassword">
            <i class="bi bi-eye"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn-login">
        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
      </button>
    </form>

    <div class="divider"><span>Belum memiliki akun?</span></div>
    <a href="<?= BASE_URL ?>/register" class="btn btn-outline-success w-100">Daftar Akun Pengguna</a>

    <div class="back-link mt-3">
      <a href="<?= BASE_URL ?>"><i class="bi bi-arrow-left me-1"></i>Kembali ke Website</a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('togglePassword').addEventListener('click', function() {
      const pw = document.getElementById('password');
      const icon = this.querySelector('i');
      if (pw.type === 'password') {
        pw.type = 'text';
        icon.className = 'bi bi-eye-slash';
      } else {
        pw.type = 'password';
        icon.className = 'bi bi-eye';
      }
    });
  </script>
</body>
</html>
