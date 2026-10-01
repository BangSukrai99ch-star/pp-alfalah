<?php
/** @var array $items */
/** @var array $pg */
/** @var string $tipe */
$tipe_label = ($tipe === 'artikel') ? 'Artikel' : 'Berita';
$page_title = $tipe_label . ' Pesantren';
?>

<!-- Header Banner -->
<section class="py-5 bg-primary-gradient text-white">
  <div class="container py-3 text-center">
    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-semibold mb-2">
      <i class="bi bi-newspaper me-1"></i> Informasi Terkini
    </span>
    <h1 class="display-6 fw-bold mb-2"><?= $tipe_label ?> <?= e($pengaturan['nama_pesantren'] ?? 'Pesantren') ?></h1>
    <p class="text-light opacity-90 mb-0"><?= nl2br(e($pengaturan['publikasi_konten'] ?? 'Seputar kabar, kegiatan, dan tulisan edukatif santri serta asatidz.')) ?></p>
  </div>
</section>

<!-- Filter Tipe Tab & Konten -->
<section class="py-5">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 pb-2 border-bottom">
      <div class="btn-group" role="group">
        <a href="<?= BASE_URL ?>/berita?tipe=berita" class="btn <?= $tipe === 'berita' ? 'btn-primary' : 'btn-outline-secondary' ?>">
          <i class="bi bi-newspaper me-1"></i> Berita Harian
        </a>
        <a href="<?= BASE_URL ?>/berita?tipe=artikel" class="btn <?= $tipe === 'artikel' ? 'btn-primary' : 'btn-outline-secondary' ?>">
          <i class="bi bi-file-text me-1"></i> Artikel & Kajian
        </a>
      </div>
      <span class="text-muted small">Total: <?= $pg['total'] ?? count($items) ?> postingan</span>
    </div>

    <?php if (empty($items)): ?>
      <div class="text-center py-5">
        <div class="display-1 text-muted mb-3"><i class="bi bi-journal-x"></i></div>
        <h4 class="fw-bold">Belum Ada <?= $tipe_label ?></h4>
        <p class="text-muted">Konten pada kategori ini belum dipublikasikan.</p>
        <a href="<?= BASE_URL ?>" class="btn btn-outline-primary"><i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda</a>
      </div>
    <?php else: ?>
      <div class="row g-4 mb-5">
        <?php foreach ($items as $item): ?>
          <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden card-hover transition">
              <div class="position-relative">
                <?php if (!empty($item['gambar'])): ?>
                  <?php if (is_video_file($item['gambar'])): ?>
                    <video src="<?= UPLOAD_URL . e($item['gambar']) ?>" class="card-img-top" controls preload="metadata" style="height: 220px; object-fit: cover;"></video>
                  <?php else: ?>
                    <img src="<?= UPLOAD_URL . e($item['gambar']) ?>" class="card-img-top" alt="<?= e($item['judul']) ?>" style="height: 220px; object-fit: cover;">
                  <?php endif; ?>
                <?php else: ?>
                  <div class="bg-secondary-subtle d-flex align-items-center justify-content-center" style="height: 220px;">
                    <i class="bi bi-image text-muted display-4"></i>
                  </div>
                <?php endif; ?>
                <span class="badge bg-primary position-absolute top-0 start-0 m-3 px-3 py-1 rounded-pill text-capitalize">
                  <?= e($item['tipe']) ?>
                </span>
              </div>
              <div class="card-body p-4 d-flex flex-column">
                <div class="text-muted small mb-2 d-flex align-items-center gap-2">
                  <i class="bi bi-calendar3"></i> <?= format_tanggal($item['created_at']) ?>
                  <span>•</span>
                  <i class="bi bi-eye"></i> <?= number_format($item['views'] ?? 0) ?>
                </div>
                <h5 class="card-title fw-bold mb-3">
                  <a href="<?= BASE_URL ?>/berita/<?= e($item['slug']) ?>" class="text-dark text-decoration-none text-hover-primary">
                    <?= e($item['judul']) ?>
                  </a>
                </h5>
                <p class="card-text text-secondary small flex-grow-1">
                  <?= truncate($item['konten'] ?? '', 120) ?>
                </p>
                <div class="pt-3 border-top mt-auto">
                  <a href="<?= BASE_URL ?>/berita/<?= e($item['slug']) ?>" class="btn btn-sm btn-outline-primary rounded-pill w-100">
                    Baca Selengkapnya <i class="bi bi-arrow-right ms-1"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Pagination -->
      <?php if (!empty($pg['total_pages']) && $pg['total_pages'] > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
          <ul class="pagination justify-content-center">
            <?php if ($pg['has_prev']): ?>
              <li class="page-item">
                <a class="page-link" href="<?= BASE_URL ?>/berita?tipe=<?= $tipe ?>&page=<?= $pg['page'] - 1 ?>">
                  <i class="bi bi-chevron-left"></i>
                </a>
              </li>
            <?php endif; ?>

            <?php for ($p = 1; $p <= $pg['total_pages']; $p++): ?>
              <li class="page-item <?= $p === $pg['page'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= BASE_URL ?>/berita?tipe=<?= $tipe ?>&page=<?= $p ?>"><?= $p ?></a>
              </li>
            <?php endfor; ?>

            <?php if ($pg['has_next']): ?>
              <li class="page-item">
                <a class="page-link" href="<?= BASE_URL ?>/berita?tipe=<?= $tipe ?>&page=<?= $pg['page'] + 1 ?>">
                  <i class="bi bi-chevron-right"></i>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </nav>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>
