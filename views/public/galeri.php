<?php
$page_title = 'Galeri Kegiatan Pesantren';
?>

<!-- Header Banner -->
<section class="py-5 bg-primary-gradient text-white text-center">
  <div class="container py-3">
    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-semibold mb-2">
      <i class="bi bi-camera me-1"></i> Dokumentasi Visual
    </span>
    <h1 class="display-6 fw-bold mb-2">Galeri <?= e($pengaturan['nama_pesantren'] ?? 'Pesantren') ?></h1>
    <p class="text-light opacity-90 mb-0">Merekam jejak kebersamaan, ibadah, pembelajaran, dan aktivitas santri.</p>
  </div>
</section>

<!-- Galeri Grid -->
<section class="py-5">
  <div class="container">
    <?php if (empty($items)): ?>
      <div class="text-center py-5">
        <div class="display-1 text-muted mb-3"><i class="bi bi-images"></i></div>
        <h4 class="fw-bold">Belum Ada Foto Galeri</h4>
        <p class="text-muted">Dokumentasi kegiatan santri akan segera diupload oleh pengurus.</p>
        <a href="<?= BASE_URL ?>" class="btn btn-outline-primary"><i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda</a>
      </div>
    <?php else: ?>
      <div class="row g-4">
        <?php foreach ($items as $item): ?>
          <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden card-hover position-relative group-gallery">
              <?php if (!empty($item['gambar'])): ?>
                <img src="<?= UPLOAD_URL . e($item['gambar']) ?>" alt="<?= e($item['judul']) ?>" class="card-img-top" style="height: 220px; object-fit: cover;">
              <?php else: ?>
                <div class="bg-secondary-subtle d-flex align-items-center justify-content-center" style="height: 220px;">
                  <i class="bi bi-image text-muted fs-1"></i>
                </div>
              <?php endif; ?>
              <div class="card-body p-3">
                <small class="text-muted d-block mb-1"><i class="bi bi-calendar3 me-1"></i> <?= format_tanggal($item['created_at']) ?></small>
                <h6 class="fw-bold mb-0 text-dark"><?= e($item['judul']) ?></h6>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
