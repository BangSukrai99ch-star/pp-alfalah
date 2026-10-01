<?php
/** @var array $current_user */
/** @var ?string $flash_success */
/** @var ?string $flash_error */
/** @var ?string $flash_warning */
/** @var array $pengaturan */
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pengaturan['nama_pesantren'] ?? 'Pesantren') ?> - Admin Panel</title>
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
  <!-- Admin Custom CSS -->
  <link href="<?= BASE_URL ?>/public/css/admin.css" rel="stylesheet">
</head>
<body class="admin-body">

<!-- Sidebar -->
<div class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-brand">
    <?php if (!empty($pengaturan['logo'])): ?>
      <img src="<?= UPLOAD_URL . e($pengaturan['logo']) ?>" alt="Logo" class="sidebar-logo">
    <?php endif; ?>
    <div class="sidebar-brand-text">
      <span class="brand-name"><?= e($pengaturan['nama_pesantren'] ?? 'Pesantren') ?></span>
      <span class="brand-sub">Admin Panel</span>
    </div>
  </div>

  <div class="sidebar-user">
    <div class="user-avatar">
      <?php $u = $current_user; ?>
      <?php if (!empty($u['foto'])): ?>
        <img src="<?= UPLOAD_URL . e($u['foto']) ?>" alt="Foto">
      <?php else: ?>
        <div class="avatar-initials"><?= strtoupper(substr($u['nama'], 0, 2)) ?></div>
      <?php endif; ?>
    </div>
    <div class="user-info">
      <div class="user-name"><?= e($u['nama']) ?></div>
      <div class="user-role badge-role-<?= $u['role'] ?>"><?= $u['role'] === 'webmaster' ? 'Admin Ponpes' : ucfirst($u['role']) ?></div>
    </div>
  </div>

  <nav class="sidebar-nav">
    <!-- Dashboard -->
    <div class="nav-item">
      <a href="<?= BASE_URL ?>/admin" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'/admin') && !str_contains($_SERVER['REQUEST_URI'],'/santri') && !str_contains($_SERVER['REQUEST_URI'],'/pendaftaran') && !str_contains($_SERVER['REQUEST_URI'],'/kegiatan') && !str_contains($_SERVER['REQUEST_URI'],'/pengaturan') ? 'active' : '' ?>">
        <i class="bi bi-speedometer2"></i> Dashboard
      </a>
    </div>

    <?php $role = $u['role']; ?>

    <!-- Webmaster Menu -->
    <?php if ($role === 'webmaster'): ?>
    <div class="nav-section-title">WEBSITE</div>
    <div class="nav-item">
      <a href="<?= BASE_URL ?>/pengaturan" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'/pengaturan') && !str_contains($_SERVER['REQUEST_URI'],'tpq') && !str_contains($_SERVER['REQUEST_URI'],'diniyyah') && !str_contains($_SERVER['REQUEST_URI'],'users') && !str_contains($_SERVER['REQUEST_URI'],'slider') ? 'active' : '' ?>">
        <i class="bi bi-gear-fill"></i> Pengaturan Web
      </a>
    </div>
    <div class="nav-item">
      <a href="<?= BASE_URL ?>/pengaturan/slider" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'/slider') ? 'active' : '' ?>">
        <i class="bi bi-images"></i> Slider / Banner
      </a>
    </div>
    <div class="nav-section-title">MANAJEMEN</div>
    <div class="nav-item">
      <a href="<?= BASE_URL ?>/pengaturan/users" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'/users') ? 'active' : '' ?>">
        <i class="bi bi-people-fill"></i> Kelola Admin
      </a>
    </div>
    <?php endif; ?>

    <!-- Kegiatan Menu -->
    <?php if (in_array($role, ['kegiatan','webmaster'])): ?>
    <div class="nav-section-title">KONTEN</div>
    <div class="nav-item">
      <a href="<?= BASE_URL ?>/kegiatan?tipe=berita" class="nav-link">
        <i class="bi bi-newspaper"></i> Berita
      </a>
    </div>
    <div class="nav-item">
      <a href="<?= BASE_URL ?>/kegiatan?tipe=artikel" class="nav-link">
        <i class="bi bi-file-text-fill"></i> Artikel
      </a>
    </div>
    <div class="nav-item">
      <a href="<?= BASE_URL ?>/kegiatan?tipe=galeri" class="nav-link">
        <i class="bi bi-camera-fill"></i> Galeri
      </a>
    </div>
    <?php endif; ?>

    <!-- Jenjang Admin Menu -->
    <?php if (in_array($role, ['diniyyah','tpq','webmaster','admin_kelas'])): ?>
    <div class="nav-section-title">SANTRI</div>
    <div class="nav-item">
      <a href="<?= BASE_URL ?>/santri" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'/santri') ? 'active' : '' ?>">
        <i class="bi bi-mortarboard-fill"></i> Data Santri
      </a>
    </div>
    <?php if ($role !== 'admin_kelas'): ?>
    <div class="nav-item">
      <a href="<?= BASE_URL ?>/pendaftaran" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'/pendaftaran') ? 'active' : '' ?>">
        <i class="bi bi-clipboard-check-fill"></i> Pendaftaran Santri
        <?php
          $jenjang_f = ($role === 'webmaster') ? null : ROLE_JENJANG_MAP[$role];
          $pending_sql = "SELECT COUNT(*) FROM pendaftaran_jenjang WHERE status='pending'";
          if ($jenjang_f) $pending_sql .= " AND jenis_jenjang='" . $jenjang_f . "'";
          global $pdo;
          $pending_cnt = $pdo->query($pending_sql)->fetchColumn();
        ?>
        <?php if ($pending_cnt > 0): ?>
          <span class="badge bg-danger ms-auto"><?= $pending_cnt ?></span>
        <?php endif; ?>
      </a>
    </div>
    <?php endif; ?>
    <?php if ($role !== 'admin_kelas'): ?>
    <div class="nav-item">
      <a href="<?= BASE_URL ?>/kelulusan" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'/kelulusan') ? 'active' : '' ?>">
        <i class="bi bi-award-fill"></i> Data Kelulusan
      </a>
    </div>
    <?php endif; ?>
    <div class="nav-item">
      <a href="<?= BASE_URL ?>/kenaikan" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'/kenaikan') ? 'active' : '' ?>">
        <i class="bi bi-arrow-up-right-circle-fill"></i> Kenaikan Kelas
      </a>
    </div>
    <?php if (in_array($role, ['diniyyah', 'tpq'], true)): ?>
    <div class="nav-item">
      <a href="<?= BASE_URL ?>/pengaturan/psb_jenjang" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'/psb_jenjang') ? 'active' : '' ?>">
        <i class="bi bi-calendar2-check-fill"></i> Jadwal Pendaftaran
      </a>
    </div>
    <?php endif; ?>

    <?php if (in_array($role, ['diniyyah', 'tpq'], true)): ?>
    <div class="nav-item">
      <a href="<?= BASE_URL ?>/admin-kelas" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'/admin-kelas') ? 'active' : '' ?>">
        <i class="bi bi-person-badge-fill"></i> Admin Kelas
      </a>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <!-- Diniyyah Khusus -->
    <?php if (in_array($role, ['diniyyah', 'webmaster'], true)): ?>
    <div class="nav-section-title">PENGATURAN DINIYYAH</div>
    <div class="nav-item">
      <a href="<?= BASE_URL ?>/pengaturan/diniyyah" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'/pengaturan/diniyyah') ? 'active' : '' ?>">
        <i class="bi bi-diagram-3-fill"></i> Jenjang & Kelas Diniyyah
      </a>
    </div>
    <?php endif; ?>

    <!-- TPQ Khusus -->
    <?php if ($role === 'tpq'): ?>
    <div class="nav-section-title">PENGATURAN</div>
    <div class="nav-item">
      <a href="<?= BASE_URL ?>/pengaturan/tpq" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'/tpq') ? 'active' : '' ?>">
        <i class="bi bi-book-fill"></i> Tingkatan TPQ
      </a>
    </div>
    <?php endif; ?>

  </nav><!-- end nav -->
</div><!-- end sidebar -->

<!-- Main Content -->
<div class="admin-main" id="adminMain">

  <!-- Top Navbar -->
  <nav class="admin-topbar">
    <div class="topbar-left">
      <button class="sidebar-toggle" id="sidebarToggle">
        <i class="bi bi-list"></i>
      </button>
      <nav aria-label="breadcrumb" class="d-none d-md-flex">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/admin">Dashboard</a></li>
          <?php if (isset($breadcrumb)): foreach ($breadcrumb as $bc): ?>
            <li class="breadcrumb-item <?= $bc['active'] ?? false ? 'active' : '' ?>">
              <?= isset($bc['url']) ? '<a href="'.$bc['url'].'">'.e($bc['label']).'</a>' : e($bc['label']) ?>
            </li>
          <?php endforeach; endif; ?>
        </ol>
      </nav>
    </div>
    <div class="topbar-right">
      <a href="<?= BASE_URL ?>" target="_blank" class="btn btn-sm btn-outline-secondary me-2">
        <i class="bi bi-globe"></i> Lihat Website
      </a>
      <div class="dropdown">
        <button class="btn btn-sm btn-light dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
          <i class="bi bi-person-circle"></i> <?= e($u['nama']) ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><span class="dropdown-item-text text-muted small"><?= $u['role'] === 'webmaster' ? 'Admin Ponpes' : ucfirst($u['role']) ?></span></li>
          <li><a class="dropdown-item" href="<?= BASE_URL ?>/profile"><i class="bi bi-person-badge me-2"></i>Profil Saya</a></li>
          <li><a class="dropdown-item" href="<?= BASE_URL ?>/pengaturan/akun"><i class="bi bi-person-gear me-2"></i>Akun Saya</a></li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <form action="<?= BASE_URL ?>/logout" method="post" class="d-inline">
              <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
              <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i> Logout</button>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Page Content -->
  <div class="admin-content">
    <!-- Flash Messages -->
    <?php if ($flash_success): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= e($flash_success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
    <?php if ($flash_error): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= e($flash_error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
    <?php if ($flash_warning): ?>
      <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2"></i><?= e($flash_warning) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <!-- View Content Injected Here -->
    <?php
    if (isset($view_content_file) && file_exists($view_content_file)) {
        require $view_content_file;
    } elseif (isset($view_file) && file_exists(ROOT_PATH . '/views/' . $view_file . '.php')) {
        require ROOT_PATH . '/views/' . $view_file . '.php';
    }
    ?>
  </div>

  <footer class="admin-footer">
    <small class="text-muted">&copy; <?= date('Y') ?> <?= e($pengaturan['nama_pesantren'] ?? 'Pesantren') ?> - Admin Panel</small>
  </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/public/js/admin.js"></script>
</body>
</html>
