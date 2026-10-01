<?php
$page_title = 'Hubungi Kami';
?>

<!-- Header Banner -->
<section class="py-5 bg-primary-gradient text-white text-center">
  <div class="container py-3">
    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-semibold mb-2">
      <i class="bi bi-chat-dots me-1"></i> Layanan Informasi
    </span>
    <h1 class="display-6 fw-bold mb-2">Kontak & Lokasi Pesantren</h1>
    <p class="text-light opacity-90 mb-0">Silakan hubungi kami untuk informasi pendaftaran, program pendidikan, atau kunjungan.</p>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <div class="row g-5">
      <!-- Kolom Kiri: Informasi Kontak -->
      <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 h-100 bg-light-subtle">
          <h4 class="fw-bold mb-4 text-primary">Informasi Kontak</h4>

          <div class="d-flex flex-column gap-4">
            <div class="d-flex gap-3">
              <div class="bg-primary text-white p-3 rounded-4 flex-shrink-0 d-flex align-items-center justify-content-center" style="width:50px; height:50px;">
                <i class="bi bi-geo-alt-fill fs-5"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-1">Alamat Pesantren</h6>
                <p class="text-secondary small mb-0"><?= e($pengaturan['alamat'] ?? 'Jl. Pesantren No. 1') ?></p>
              </div>
            </div>

            <div class="d-flex gap-3">
              <div class="bg-success text-white p-3 rounded-4 flex-shrink-0 d-flex align-items-center justify-content-center" style="width:50px; height:50px;">
                <i class="bi bi-telephone-fill fs-5"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-1">Telepon / WhatsApp</h6>
                <?php if (!empty($pengaturan['telepon'])): ?>
                  <a class="text-secondary small text-decoration-none" href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $pengaturan['telepon']) ?>" target="_blank" rel="noopener"><?= e($pengaturan['telepon']) ?> <i class="bi bi-box-arrow-up-right ms-1"></i></a>
                <?php else: ?><p class="text-secondary small mb-0">-</p><?php endif; ?>
              </div>
            </div>

            <div class="d-flex gap-3">
              <div class="bg-warning text-dark p-3 rounded-4 flex-shrink-0 d-flex align-items-center justify-content-center" style="width:50px; height:50px;">
                <i class="bi bi-envelope-fill fs-5"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-1">Email Resmi</h6>
                <?php if (!empty($pengaturan['email'])): ?>
                  <a class="text-secondary small text-decoration-none" href="mailto:<?= e($pengaturan['email']) ?>"><?= e($pengaturan['email']) ?></a>
                <?php else: ?><p class="text-secondary small mb-0">-</p><?php endif; ?>
              </div>
            </div>

            <div class="d-flex gap-3">
              <div class="bg-info text-white p-3 rounded-4 flex-shrink-0 d-flex align-items-center justify-content-center" style="width:50px; height:50px;">
                <i class="bi bi-clock-fill fs-5"></i>
              </div>
              <div>
                <h6 class="fw-bold mb-1">Jam Pelayanan Sekretariat</h6>
                <p class="text-secondary small mb-0">Setiap Hari: 08.00 - 17.00 WIB</p>
              </div>
            </div>
          </div>

          <div class="mt-4 pt-4 border-top">
            <h6 class="fw-bold mb-3">Media Sosial Kami:</h6>
            <div class="d-flex gap-2">
              <?php if (!empty($pengaturan['facebook_url'])): ?>
                <a href="<?= e($pengaturan['facebook_url']) ?>" target="_blank" rel="noopener" aria-label="Facebook" class="btn btn-outline-primary rounded-circle"><i class="bi bi-facebook"></i></a>
              <?php endif; ?>
              <?php if (!empty($pengaturan['instagram_url'])): ?>
                <a href="<?= e($pengaturan['instagram_url']) ?>" target="_blank" rel="noopener" aria-label="Instagram" class="btn btn-outline-danger rounded-circle"><i class="bi bi-instagram"></i></a>
              <?php endif; ?>
              <?php if (!empty($pengaturan['youtube_url'])): ?>
                <a href="<?= e($pengaturan['youtube_url']) ?>" target="_blank" rel="noopener" aria-label="YouTube" class="btn btn-outline-danger rounded-circle"><i class="bi bi-youtube"></i></a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Kolom Kanan: Peta Embed & Pesan -->
      <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-4">
          <h4 class="fw-bold mb-2">Peta Lokasi Pesantren</h4>
          <p class="text-secondary small mb-4">Temukan petunjuk arah menuju kampus Pondok Pesantren.</p>
          
          <?php if (!empty($pengaturan['maps_embed'])): ?>
            <div class="maps-embed rounded-4 overflow-hidden shadow-sm">
              <?= $pengaturan['maps_embed'] ?>
            </div>
          <?php else: ?>
            <div class="rounded-4 bg-light border p-5 text-center d-flex flex-column align-items-center justify-content-center" style="min-height: 380px;">
              <i class="bi bi-map text-primary display-3 mb-3"></i>
              <h5 class="fw-bold">Peta Belum Dikonfigurasi</h5>
              <p class="text-muted small max-w-400">Admin web master dapat memasukkan kode embed Google Maps di menu Pengaturan Web.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>
