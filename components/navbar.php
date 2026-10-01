<?php
/** @var array $pengaturan */
$logoPath = $pengaturan['logo'] ?? 'settings/logo_alfalah.png';
$logoFile = ROOT_PATH . '/public/uploads/' . ltrim((string)$logoPath, '/');
if (!is_file($logoFile)) {
    $logoPath = 'settings/logo_alfalah.png';
}
$navBase = defined('BASE_URL') ? BASE_URL : '';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary-gradient sticky-top" aria-label="Navigasi utama">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?= $navBase ?>">
      <img src="<?= UPLOAD_URL . e($logoPath) ?>" alt="Logo Pesantren Al Falah" class="brand-logo">
      <span class="brand-name-text">
        <strong><?= e($pengaturan['nama_pesantren'] ?? 'Pesantren Salaf Al Falah') ?></strong>
        <small class="d-block opacity-75">Pesantren Salaf</small>
      </span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"
            aria-controls="navbarMain" aria-expanded="false" aria-label="Buka menu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarMain">
      <ul class="navbar-nav ms-auto gap-lg-1">
        <li class="nav-item"><a class="nav-link" href="<?= $navBase ?>">Beranda</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Profil</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= $navBase ?>/profil#tentang">Tentang</a></li>
            <li><a class="dropdown-item" href="<?= $navBase ?>/berita?tipe=kegiatan">Kegiatan</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Program</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= $navBase ?>/profil#awaliyah">Diniyyah Awaliyah</a></li>
            <li><a class="dropdown-item" href="<?= $navBase ?>/profil#wustho">Diniyyah Wustho</a></li>
            <li><a class="dropdown-item" href="<?= $navBase ?>/profil#majlis">Majlis</a></li>
            <li><a class="dropdown-item" href="<?= $navBase ?>/profil#tpq">TPQ</a></li>
            <li><a class="dropdown-item" href="<?= $navBase ?>/profil#psq">PSQ</a></li>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Info Publik</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= $navBase ?>/berita?tipe=pemberitahuan">Pemberitahuan</a></li>
            <li><a class="dropdown-item" href="<?= $navBase ?>/berita?tipe=fatwa_ulama">Fatwa Ulama</a></li>
          </ul>
        </li>
        <?php if (!is_logged_in()): ?>
          <li class="nav-item"><a class="nav-link" href="<?= $navBase ?>/login"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a></li>
        <?php elseif (has_role(['webmaster', 'kegiatan', 'diniyyah', 'tpq'])): ?>
          <li class="nav-item"><a class="nav-link" href="<?= $navBase ?>/admin"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= $navBase ?>/logout">Keluar</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?= $navBase ?>/logout">Keluar</a></li>
        <?php endif; ?>
        <li class="nav-item">
          <form class="d-flex ms-lg-2" action="<?= $navBase ?>/berita" method="get" role="search">
            <label class="visually-hidden" for="navSearch">Cari Publikasi</label>
            <input class="form-control form-control-sm" id="navSearch" name="q" type="search" placeholder="Cari publikasi">
          </form>
        </li>
      </ul>
    </div>
  </div>
</nav>
