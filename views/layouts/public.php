<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($page_title) ? e($page_title) . ' - ' : '' ?><?= e($pengaturan['nama_pesantren'] ?? 'Pesantren') ?></title>
  <meta name="description" content="<?= e($pengaturan['tagline'] ?? '') ?>">
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
  <!-- Public CSS -->
  <link href="<?= BASE_URL ?>/public/css/public.css?v=<?= filemtime(ROOT_PATH . '/public/css/public.css') ?>" rel="stylesheet">
  <?php if (!empty($pengaturan['favicon'])): ?>
    <link rel="icon" href="<?= UPLOAD_URL . e($pengaturan['favicon']) ?>" type="image/png">
  <?php endif; ?>
</head>
<body>

<?php require ROOT_PATH . '/components/navbar.php'; ?>

<!-- Main Content -->
<main>
  <?php
  if (isset($view_content_file) && file_exists($view_content_file)) {
      require $view_content_file;
  } elseif (isset($view_file) && file_exists(ROOT_PATH . '/views/' . $view_file . '.php')) {
      require ROOT_PATH . '/views/' . $view_file . '.php';
  }
  ?>
</main>

<!-- Footer -->
<footer class="site-footer mt-5">
  <div class="footer-top">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-4">
          <h5 class="footer-brand"><?= e($pengaturan['nama_pesantren'] ?? '') ?></h5>
          <p class="text-secondary small"><?= e($pengaturan['tagline'] ?? '') ?></p>
          <div class="d-flex gap-2 mt-3">
            <?php if (!empty($pengaturan['facebook_url'])): ?><a href="<?= e($pengaturan['facebook_url']) ?>" target="_blank" class="footer-social"><i class="bi bi-facebook"></i></a><?php endif; ?>
            <?php if (!empty($pengaturan['instagram_url'])): ?><a href="<?= e($pengaturan['instagram_url']) ?>" target="_blank" class="footer-social"><i class="bi bi-instagram"></i></a><?php endif; ?>
            <?php if (!empty($pengaturan['youtube_url'])): ?><a href="<?= e($pengaturan['youtube_url']) ?>" target="_blank" class="footer-social"><i class="bi bi-youtube"></i></a><?php endif; ?>
          </div>
        </div>
        <div class="col-lg-3">
          <h6 class="footer-heading">Menu</h6>
          <ul class="footer-links">
            <li><a href="<?= BASE_URL ?>">Beranda</a></li>
            <li><a href="<?= BASE_URL ?>/profil">Profil Pesantren</a></li>
            <li><a href="<?= BASE_URL ?>/berita">Berita & Artikel</a></li>
            <li><a href="<?= BASE_URL ?>/galeri">Galeri</a></li>
            <li><a href="<?= BASE_URL ?>/kontak">Kontak</a></li>
            <?php if (!empty($pengaturan['psb_aktif']) && (!empty($pengaturan['psb_diniyyah_aktif']) || !empty($pengaturan['psb_tpq_aktif']))): ?><li><a href="<?= BASE_URL ?>/daftar" class="text-warning">Pendaftaran Santri</a></li><?php endif; ?>
          </ul>
        </div>
        <div class="col-lg-5">
          <h6 class="footer-heading">Hubungi Kami</h6>
          <ul class="footer-contact">
            <?php if (!empty($pengaturan['alamat'])): ?>
              <li><i class="bi bi-geo-alt-fill text-warning"></i> <?= nl2br(e($pengaturan['alamat'])) ?></li>
            <?php endif; ?>
            <?php if (!empty($pengaturan['telepon'])): ?>
              <li><i class="bi bi-telephone-fill text-warning"></i> <?= e($pengaturan['telepon']) ?></li>
            <?php endif; ?>
            <?php if (!empty($pengaturan['email'])): ?>
              <li><i class="bi bi-envelope-fill text-warning"></i> <?= e($pengaturan['email']) ?></li>
            <?php endif; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container">
      <p class="mb-0 text-center small"><?= e($pengaturan['footer_text'] ?? '') ?></p>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>/public/js/public.js"></script>
</body>
</html>
