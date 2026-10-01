<?php
/** @var array $item */
/** @var array $berita_lainnya */
$page_title = e($item['judul']);
?>

<div class="py-4 bg-light border-bottom">
  <div class="container">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0 small">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>" class="text-decoration-none">Beranda</a></li>
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/berita?tipe=<?= e($item['tipe']) ?>" class="text-decoration-none text-capitalize"><?= e($item['tipe']) ?></a></li>
        <li class="breadcrumb-item active text-truncate max-w-300" aria-current="page"><?= e($item['judul']) ?></li>
      </ol>
    </nav>
  </div>
</div>

<section class="py-5">
  <div class="container">
    <div class="row g-5">
      <!-- Kolom Kiri: Isi Berita -->
      <div class="col-lg-8">
        <article class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
          <div class="mb-3">
            <span class="badge bg-primary px-3 py-2 rounded-pill text-capitalize fs-7">
              <?= e($item['tipe']) ?>
            </span>
          </div>
          
          <h1 class="fw-bold mb-3 lh-sm display-6"><?= e($item['judul']) ?></h1>
          
          <div class="d-flex align-items-center gap-3 text-muted small mb-4 pb-3 border-bottom flex-wrap">
            <span><i class="bi bi-person-circle me-1 text-primary"></i> <?= e($item['penulis'] ?? 'Admin Pesantren') ?></span>
            <span><i class="bi bi-calendar3 me-1 text-success"></i> <?= format_tanggal($item['created_at']) ?></span>
            <span><i class="bi bi-eye me-1 text-info"></i> <?= number_format($item['views'] ?? 0) ?> views</span>
          </div>

          <?php if (!empty($item['gambar'])): ?>
            <div class="mb-4 rounded-4 overflow-hidden">
              <?php if (is_video_file($item['gambar'])): ?>
                <video src="<?= UPLOAD_URL . e($item['gambar']) ?>" class="w-100" controls preload="metadata"></video>
              <?php else: ?>
                <img src="<?= UPLOAD_URL . e($item['gambar']) ?>" alt="<?= e($item['judul']) ?>" class="w-100 object-fit-cover" style="max-height: 480px;">
              <?php endif; ?>
            </div>
          <?php endif; ?>

          <!-- Konten Artikel/Berita -->
          <div class="article-body lh-lg text-secondary fs-6 mb-5">
            <?= nl2br($item['konten']) ?>
          </div>

          <!-- Tags -->
          <?php if (!empty($item['tags'])): ?>
            <div class="pt-3 border-top mb-4">
              <span class="text-muted small me-2"><i class="bi bi-tags-fill me-1"></i> Tags:</span>
              <?php foreach (explode(',', $item['tags']) as $tag): ?>
                <span class="badge bg-light text-dark border me-1 mb-1">#<?= trim(e($tag)) ?></span>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <!-- Share buttons -->
          <div class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span class="fw-bold small text-muted">Bagikan tulisan ini:</span>
            <div class="d-flex gap-2">
              <a href="https://api.whatsapp.com/send?text=<?= urlencode(e($item['judul']) . ' - ' . BASE_URL . '/berita/' . e($item['slug'])) ?>" target="_blank" class="btn btn-sm btn-success rounded-pill px-3">
                <i class="bi bi-whatsapp me-1"></i> WhatsApp
              </a>
              <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(BASE_URL . '/berita/' . e($item['slug'])) ?>" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3">
                <i class="bi bi-facebook me-1"></i> Facebook
              </a>
            </div>
          </div>
        </article>
      </div>

      <!-- Kolom Kanan: Sidebar Lainnya -->
      <div class="col-lg-4">
        <div class="sticky-top" style="top: 90px;">
          <!-- Card Berita Terkini -->
          <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h5 class="fw-bold mb-3 pb-2 border-bottom"><i class="bi bi-fire text-danger me-2"></i>Kabar Terkini</h5>
            <div class="d-flex flex-column gap-3">
              <?php if (empty($berita_lainnya)): ?>
                <p class="text-muted small mb-0">Belum ada kabar lainnya.</p>
              <?php else: ?>
                <?php foreach ($berita_lainnya as $other): ?>
                  <?php if ($other['id'] == $item['id']) continue; ?>
                  <div class="d-flex gap-3 align-items-center">
                    <?php if (!empty($other['gambar'])): ?>
                      <img src="<?= UPLOAD_URL . e($other['gambar']) ?>" alt="<?= e($other['judul']) ?>" class="rounded-3 object-fit-cover flex-shrink-0" style="width: 70px; height: 70px;">
                    <?php else: ?>
                      <div class="bg-light rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 70px; height: 70px;">
                        <i class="bi bi-image text-muted"></i>
                      </div>
                    <?php endif; ?>
                    <div class="flex-grow-1 overflow-hidden">
                      <small class="text-muted d-block"><?= format_tanggal($other['created_at']) ?></small>
                      <a href="<?= BASE_URL ?>/berita/<?= e($other['slug']) ?>" class="text-dark fw-semibold text-decoration-none small line-clamp-2">
                        <?= e($other['judul']) ?>
                      </a>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>

          <!-- Banner PSB Mini -->
          <?php if (!empty($pengaturan['psb_aktif']) && (!empty($pengaturan['psb_diniyyah_aktif']) || !empty($pengaturan['psb_tpq_aktif']))): ?>
            <div class="card border-0 shadow-sm rounded-4 p-4 text-white text-center" style="background: linear-gradient(135deg, #123d25, #0f766e);">
              <i class="bi bi-mortarboard display-4 mb-2 text-warning"></i>
              <h5 class="fw-bold mb-1">Penerimaan Santri Baru</h5>
              <p class="small text-light opacity-90 mb-3">Tahun Ajaran <?= e($pengaturan['psb_tahun_ajaran'] ?? '') ?> telah resmi dibuka!</p>
              <a href="<?= BASE_URL ?>/daftar" class="btn btn-warning text-dark fw-bold rounded-pill px-4">
                Daftar Online Sekarang
              </a>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>
