<?php
/** @var int $santri_diterima */
$view_file = 'public/home';
?>

<!-- PSB Alert Banner -->
<?php if (!empty($pengaturan['psb_aktif']) && (!empty($pengaturan['psb_diniyyah_aktif']) || !empty($pengaturan['psb_tpq_aktif']))): ?>
<div class="psb-banner">
  <div class="container d-flex justify-content-center align-items-center gap-3 flex-wrap">
    <h5 class="mb-0">🎓 Pendaftaran Santri Baru Tahun Ajaran <?= e($pengaturan['psb_tahun_ajaran'] ?? '') ?> DIBUKA!</h5>
    <a href="<?= BASE_URL ?>/daftar" class="btn btn-sm">Daftar Sekarang →</a>
  </div>
</div>
<?php endif; ?>

<!-- Hero Section -->
<section class="hero-section">
  <?php if (!empty($pengaturan['hero_bg'])): ?>
    <div class="hero-bg" style="background-image:url('<?= UPLOAD_URL . e($pengaturan['hero_bg']) ?>')"></div>
  <?php endif; ?>

  <div class="container py-5">
    <div class="row align-items-center min-vh-75">
      <div class="col-lg-7 hero-copy">
        <div class="hero-badge">
          <i class="bi bi-moon-stars-fill"></i>
          Lembaga Pendidikan Islam Terpadu
        </div>
        <p class="hero-kicker mb-2">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ</p>
        <h1><?= e($pengaturan['nama_pesantren'] ?? 'Pesantren') ?></h1>
        <?php if (!empty($pengaturan['tagline'])): ?>
          <p class="hero-tagline mt-2"><?= e($pengaturan['tagline']) ?></p>
        <?php endif; ?>
        <p class="mt-3"><?= nl2br(e($pengaturan['beranda_sambutan'] ?? ($pengaturan['visi'] ?? 'Mencetak generasi muslimin yang bertaqwa, berilmu, dan berakhlak mulia.'))) ?></p>

        <div class="d-flex gap-3 flex-wrap mt-4">
          <?php if (!empty($pengaturan['psb_aktif']) && (!empty($pengaturan['psb_diniyyah_aktif']) || !empty($pengaturan['psb_tpq_aktif']))): ?>
            <a href="<?= BASE_URL ?>/daftar" class="btn btn-warning btn-lg fw-bold">
              <i class="bi bi-pen-fill me-2"></i>Daftar Sekarang
            </a>
          <?php endif; ?>
          <a href="<?= BASE_URL ?>/profil" class="btn btn-outline-light btn-lg">
            <i class="bi bi-info-circle me-2"></i>Profil Pesantren
          </a>
        </div>

        <div class="hero-stats">
          <div>
            <div class="hero-stat-num" data-counter="<?= $santri_diterima ?>"><?= $santri_diterima ?>+</div>
            <div class="hero-stat-label">Santri Diterima</div>
          </div>
          <div>
            <div class="hero-stat-num" data-counter="3">3+</div>
            <div class="hero-stat-label">Program Pendidikan</div>
          </div>
          <div>
            <div class="hero-stat-num" data-counter="20"></div>
            <div class="hero-stat-label">Berdiri Sejak Tahun </div>
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="hero-identity ms-lg-4">
          <?php if (!empty($pengaturan['logo'])): ?>
            <img class="hero-logo" src="<?= UPLOAD_URL . e($pengaturan['logo']) ?>" alt="Logo <?= e($pengaturan['nama_pesantren'] ?? 'Pesantren') ?>">
          <?php else: ?>
            <div class="hero-logo-fallback"><i class="bi bi-moon-stars-fill"></i></div>
          <?php endif; ?>
          <div class="hero-identity-caption">
            <span class="hero-identity-line"></span>
            <span>Menuntut ilmu, membangun peradaban</span>
            <span class="hero-identity-line"></span>
          </div>
        </div>
        <div class="hero-card-float ms-lg-4">
          <div class="d-flex align-items-center gap-3 mb-3">
              <div class="hero-card-icon"><i class="bi bi-moon-stars-fill"></i></div>
            <div>
              <div class="fw-bold">Program Unggul Dengan Pendidikan Terpadu</div>
              <div class="small">Dalam Kependidikan Agama Berbasis</div>
            </div>
          </div>
          <div class="row g-2">
            <?php $programs = [
              ['📖','Diniyyah','Awaliyah, Wustho, Majlis'],
              ['🕌','TPQ & PSQ','Kajian dan pembinaan Al-Qur’an'],
            ]; ?>
            <?php foreach ($programs as $p): ?>
            <div class="col-12">
              <div class="hero-program-item">
                <span style="font-size:1.3rem"><?= $p[0] ?></span>
                <div>
                  <div class="fw-semibold"><?= $p[1] ?></div>
                  <div><?= $p[2] ?></div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="hero-scroll">
    <i class="bi bi-chevron-double-down d-block"></i>Scroll
  </div>
</section>

<!-- Program Pendidikan Section -->
<section class="py-5 bg-light">
  <div class="container">
    <div class="section-header">
      <div class="section-badge">Program Kami</div>
      <h2 class="section-title">Jenjang Pendidikan</h2>
      <div class="section-divider"></div>
    </div>
    <div class="row g-3 justify-content-center">
      <?php $jenjang_data = [
        ['📚','Diniyyah Awaliyah Wustho & Majlis','Awaliyah Wustho & Majlis','Pengajian ilmu agama Islam secara terstruktur','green','#d1fae5'],
        ['🤲','TPQ','Jilid 1 hingga Al-Qur\'an','Belajar membaca Al-Qur\'an dengan metode terbaik','olive','#e9efd3'],
      ]; ?>
      <?php foreach ($jenjang_data as [$icon,$nama,$sub,$desc,$color,$bg]): ?>
      <div class="col-md-4">
        <div class="jenjang-card">
          <div class="jenjang-icon" style="background:<?= $bg ?>">
            <span style="font-size:1.6rem"><?= $icon ?></span>
          </div>
          <h5><?= $nama ?></h5>
          <div class="badge bg-light text-dark border mb-2"><?= $sub ?></div>
          <p><?= $desc ?></p>
          <?php if (!empty($pengaturan['psb_aktif']) && (!empty($pengaturan['psb_diniyyah_aktif']) || !empty($pengaturan['psb_tpq_aktif']))): ?>
            <a href="<?= BASE_URL ?>/daftar" class="btn btn-sm btn-outline-primary mt-2">Daftar Program Ini</a>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Berita Terbaru Section -->
<?php if (!empty($berita)): ?>
<section class="py-5">
  <div class="container">
    <div class="section-header">
      <div class="section-badge">Informasi</div>
      <h2 class="section-title">Berita & Kegiatan Terbaru</h2>
      <div class="section-divider"></div>
    </div>
    <div class="row g-4">
      <?php foreach ($berita as $b): ?>
      <div class="col-md-6 col-lg-4">
        <div class="news-card">
          <?php if (!empty($b['gambar'])): ?>
            <?php if (is_video_file($b['gambar'])): ?>
              <video src="<?= UPLOAD_URL . e($b['gambar']) ?>" class="news-card-img" controls preload="metadata"></video>
            <?php else: ?>
              <img src="<?= UPLOAD_URL . e($b['gambar']) ?>" class="news-card-img" alt="<?= e($b['judul']) ?>">
            <?php endif; ?>
          <?php else: ?>
            <div class="news-card-img-placeholder">📰</div>
          <?php endif; ?>
          <div class="news-card-body">
            <div class="news-card-type"><?= ucfirst(e($b['tipe'])) ?></div>
            <h3 class="news-card-title">
              <a href="<?= BASE_URL ?>/berita/<?= e($b['slug']) ?>"><?= e($b['judul']) ?></a>
            </h3>
            <p class="news-card-meta mb-2"><?= e(truncate($b['konten'] ?? '', 100)) ?></p>
            <div class="news-card-meta d-flex justify-content-between">
              <span><i class="bi bi-calendar3 me-1"></i><?= format_tanggal($b['created_at'], 'd M Y') ?></span>
              <span><i class="bi bi-eye me-1"></i><?= number_format($b['views']) ?></span>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-4">
      <a href="<?= BASE_URL ?>/berita" class="btn btn-outline-primary">
        Lihat Semua Berita <i class="bi bi-arrow-right ms-1"></i>
      </a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Galeri Section -->
<?php if (!empty($galeri)): ?>
<section class="py-5 bg-light">
  <div class="container">
    <div class="section-header">
      <div class="section-badge">Galeri</div>
      <h2 class="section-title">Foto Kegiatan</h2>
      <div class="section-divider"></div>
    </div>
    <div class="galeri-grid">
      <?php foreach ($galeri as $g): ?>
      <?php if (!empty($g['gambar'])): ?>
      <div class="galeri-item" data-src="<?= UPLOAD_URL . e($g['gambar']) ?>">
        <img src="<?= UPLOAD_URL . e($g['gambar']) ?>" alt="<?= e($g['judul']) ?>">
        <div class="galeri-item-overlay">
          <span><?= e(truncate($g['judul'], 40)) ?></span>
        </div>
      </div>
      <?php endif; ?>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-4">
      <a href="<?= BASE_URL ?>/galeri" class="btn btn-outline-primary">
        Lihat Semua Galeri <i class="bi bi-arrow-right ms-1"></i>
      </a>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- Pengumuman Section -->
<?php if (!empty($pengaturan['pengumuman'])): ?>
<section class="py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card border-warning" style="border-width:2px !important;">
          <div class="card-header bg-warning text-dark fw-bold">
            <i class="bi bi-megaphone-fill me-2"></i>Pengumuman Pesantren
          </div>
          <div class="card-body">
            <?= nl2br(e($pengaturan['pengumuman'])) ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<?php if (!empty($pengaturan['psb_aktif']) && (!empty($pengaturan['psb_diniyyah_aktif']) || !empty($pengaturan['psb_tpq_aktif']))): ?>
<section class="py-5" style="background:linear-gradient(135deg,#123d25,#0f766e)">
  <div class="container text-center text-white">
    <h2 class="fw-bold mb-3">Bergabunglah Bersama Kami!</h2>
    <p class="mb-4 opacity-75">Pendaftaran Santri Baru Tahun Ajaran <?= e($pengaturan['psb_tahun_ajaran'] ?? '') ?> Terbuka.<br>Daftarkan putra putri Anda sekarang.</p>
    <a href="<?= BASE_URL ?>/daftar" class="btn btn-warning btn-lg fw-bold px-5">
      <i class="bi bi-pen-fill me-2"></i>Daftar Sekarang !!!
    </a>
  </div>
</section>
<?php endif; ?>
