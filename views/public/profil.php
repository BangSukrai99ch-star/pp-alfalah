<?php
$page_title = 'Profil Pesantren';
?>

<!-- Header Banner -->
<section class="py-5 bg-primary-gradient text-white position-relative">
  <div class="container py-4 text-center">
    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-semibold mb-3">
      <i class="bi bi-shield-check me-1"></i> Mengenal Lebih Dekat
    </span>
    <h1 class="display-5 fw-bold mb-3">Profil <?= e($pengaturan['nama_pesantren'] ?? 'Pondok Pesantren') ?></h1>
    <p class="lead max-w-700 mx-auto text-light opacity-90">
      <?= e($pengaturan['tagline'] ?? 'Mencetak Generasi Qur\'ani yang Berakhlak Mulia') ?>
    </p>
  </div>
</section>

<!-- Konten Utama -->
<section class="py-5">
  <div class="container">
    <div class="row g-5">
      <!-- Kolom Kiri: Navigasi Anchor -->
      <div class="col-lg-3 d-none d-lg-block">
        <div class="sticky-top" style="top: 90px;">
          <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
              <h6 class="fw-bold text-uppercase text-muted small mb-0">Daftar Menu Profil</h6>
            </div>
            <div class="list-group list-group-flush p-2">
              <a href="#sejarah" class="list-group-item list-group-item-action border-0 rounded-3 py-2 text-dark">
                <i class="bi bi-hourglass-split text-primary me-2"></i> Sejarah Pesantren
              </a>
              <a href="#visi-misi" class="list-group-item list-group-item-action border-0 rounded-3 py-2 text-dark">
                <i class="bi bi-compass-fill text-success me-2"></i> Visi & Misi
              </a>
              <a href="#program" class="list-group-item list-group-item-action border-0 rounded-3 py-2 text-dark">
                <i class="bi bi-mortarboard-fill text-warning me-2"></i> Program Pendidikan
              </a>
              <a href="#fasilitas" class="list-group-item list-group-item-action border-0 rounded-3 py-2 text-dark">
                <i class="bi bi-building text-info me-2"></i> Fasilitas Pesantren
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Kolom Kanan: Isi Profil -->
      <div class="col-lg-9">
        <div id="tentang" class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-5">
          <div class="d-flex align-items-center gap-3 mb-4">
            <div class="bg-primary-subtle text-primary p-3 rounded-4">
              <i class="bi bi-house-heart-fill fs-3"></i>
            </div>
            <div>
              <span class="badge bg-primary-subtle text-primary mb-1">Tentang Pesantren</span>
              <h3 class="fw-bold mb-0">Mengenal <?= e($pengaturan['nama_pesantren'] ?? 'Pesantren') ?></h3>
            </div>
          </div>
          <div class="text-secondary lh-lg">
            <?php if (!empty($pengaturan['tentang_konten'])): ?>
              <?= nl2br(e($pengaturan['tentang_konten'])) ?>
            <?php else: ?>
              <p><?= e($pengaturan['nama_pesantren'] ?? 'Pesantren') ?> adalah pesantren salaf yang berkhidmat dalam pembelajaran Al-Qur'an, kitab kuning, pembinaan akhlak, dan penguatan tradisi keilmuan Islam.</p>
            <?php endif; ?>
          </div>
        </div>
        <!-- Sejarah -->
        <div id="sejarah" class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-5">
          <div class="d-flex align-items-center gap-3 mb-4">
            <div class="bg-primary-subtle text-primary p-3 rounded-4">
              <i class="bi bi-clock-history fs-3"></i>
            </div>
            <div>
              <span class="badge bg-primary-subtle text-primary mb-1">Pondasi & Perjalanan</span>
              <h3 class="fw-bold mb-0">Sejarah Pesantren</h3>
            </div>
          </div>
          <div class="text-secondary lh-lg fs-6">
            <?php if (!empty($pengaturan['sejarah'])): ?>
              <?= nl2br(e($pengaturan['sejarah'])) ?>
            <?php else: ?>
              <p><?= e($pengaturan['nama_pesantren'] ?? 'Pondok Pesantren') ?> didirikan dengan tekad mulia untuk mewadahi generasi muslim dalam mendalami Al-Qur'an dan khazanah keilmuan Islam (Turats / Kitab Kuning), pembinaan akhlak, serta penguatan ilmu-ilmu keislaman.</p>
              <p>Berawal dari majelis taklim dan pembelajaran tahsin Al-Qur'an yang sederhana, kini pesantren telah berkembang menjadi pusat pendidikan terpadu yang membina santri dari berbagai pelosok Nusantara dengan ribuan santri dan alumni yang berkiprah di masyarakat.</p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Visi & Misi -->
        <div id="visi-misi" class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-5">
          <div class="d-flex align-items-center gap-3 mb-4">
            <div class="bg-success-subtle text-success p-3 rounded-4">
              <i class="bi bi-flag-fill fs-3"></i>
            </div>
            <div>
              <span class="badge bg-success-subtle text-success mb-1">Arah & Cita-Cita</span>
              <h3 class="fw-bold mb-0">Visi & Misi</h3>
            </div>
          </div>

          <!-- Visi Card -->
          <div class="bg-light-subtle border-start border-4 border-success p-4 rounded-3 mb-4 shadow-sm">
            <h5 class="fw-bold text-success mb-2"><i class="bi bi-eye-fill me-2"></i>Visi</h5>
            <p class="mb-0 fs-6 fst-italic text-dark">
              "<?= e($pengaturan['visi'] ?? 'Mencetak generasi muslim yang bertaqwa, berilmu, dan berakhlak mulia.') ?>"
            </p>
          </div>

          <!-- Misi Card -->
          <div class="bg-light-subtle border-start border-4 border-primary p-4 rounded-3 shadow-sm">
            <h5 class="fw-bold text-primary mb-3"><i class="bi bi-bullseye me-2"></i>Misi</h5>
            <div class="text-secondary lh-lg">
              <?php if (!empty($pengaturan['misi'])): ?>
                <?= nl2br(e($pengaturan['misi'])) ?>
              <?php else: ?>
                <ul class="mb-0 ps-3">
                  <li class="mb-2">Menyelenggarakan pendidikan Islam terpadu yang berkualitas tinggi dan relevan dengan zaman.</li>
                  <li class="mb-2">Membina generasi penghafal Al-Qur'an yang mutqin dan berkarakter akhlaqul karimah.</li>
                  <li class="mb-2">Mengembangkan penguasaan bahasa Arab dan Inggris secara aktif.</li>
                  <li>Mempersiapkan lulusan yang cakap dalam memimpin dan berkhidmat untuk umat dan bangsa.</li>
                </ul>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Program Pendidikan -->
        <div id="program" class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-5">
          <div class="d-flex align-items-center gap-3 mb-4">
            <div class="bg-warning-subtle text-warning p-3 rounded-4">
              <i class="bi bi-mortarboard-fill fs-3"></i>
            </div>
            <div>
              <span class="badge bg-warning-subtle text-warning mb-1">Kurikulum Terpadu</span>
              <h3 class="fw-bold mb-0">Program Pendidikan</h3>
            </div>
          </div>
          
          <div class="row g-4">
            <div class="col-md-4">
              <div class="card h-100 border-0 bg-light p-3 rounded-4">
                <div class="badge bg-success align-self-start mb-2">Madrasah Diniyyah</div>
                <h5 class="fw-bold">Khazanah Kitab Kuning</h5>
                <p class="small text-muted mb-3">Pendalaman ilmu syar'i, fiqih, nahwu, shorof, dan hadits.</p>
                <ul class="list-unstyled small text-secondary mb-0">
                  <li><i class="bi bi-check-circle-fill text-success me-2"></i>Diniyyah Awaliyah (Dasar)</li>
                  <li><i class="bi bi-check-circle-fill text-success me-2"></i>Diniyyah Wustho (Menengah)</li>
                  <li><i class="bi bi-check-circle-fill text-success me-2"></i>Diniyyah Ulya (Lanjutan)</li>
                </ul>
              </div>
            </div>

            <!-- Jenjang TPQ & Tahfidz -->
            <div class="col-md-4">
              <div class="card h-100 border-0 bg-light p-3 rounded-4">
                <div class="badge bg-warning text-dark align-self-start mb-2">Al-Qur'an & TPQ</div>
                <h5 class="fw-bold">Tahsin & Tahfidz</h5>
                <p class="small text-muted mb-3">Bimbingan membaca dan menghafal Al-Qur'an bersanad.</p>
                <ul class="list-unstyled small text-secondary mb-0">
                  <li><i class="bi bi-check-circle-fill text-success me-2"></i>TPQ Jilid 1 s/d 5</li>
                  <li><i class="bi bi-check-circle-fill text-success me-2"></i>Tahfidzul Qur'an 30 Juz</li>
                  <li><i class="bi bi-check-circle-fill text-success me-2"></i>Kajian Tajwid & Makharij</li>
                </ul>
              </div>

              <div class="col-md-4">
                <div class="card h-100 border-0 bg-light p-3 rounded-4">
                  <div class="badge bg-info align-self-start mb-2">Majlis & PSQ</div>
                  <h5 class="fw-bold">Kajian Al-Qur'an</h5>
                  <p class="small text-muted mb-3">Majlis ilmu, pembinaan umat, dan penguatan pemahaman Al-Qur'an.</p>
                  <ul class="list-unstyled small text-secondary mb-0">
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>Kajian rutin</li>
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>Pembinaan akhlak</li>
                    <li><i class="bi bi-check-circle-fill text-success me-2"></i>Tahsin dan tahfidz</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Fasilitas -->
        <div id="fasilitas" class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
          <div class="d-flex align-items-center gap-3 mb-4">
            <div class="bg-info-subtle text-info p-3 rounded-4">
              <i class="bi bi-building fs-3"></i>
            </div>
            <div>
              <span class="badge bg-info-subtle text-info mb-1">Sarana Penunjang</span>
              <h3 class="fw-bold mb-0">Fasilitas Pesantren</h3>
            </div>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
                <i class="bi bi-moon-stars-fill text-primary fs-4"></i>
                <div>
                  <h6 class="fw-bold mb-0">Masjid Jami' Pesantren</h6>
                  <small class="text-muted">Pusat ibadah dan kajian kitab harian santri</small>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
                <i class="bi bi-house-door-fill text-success fs-4"></i>
                <div>
                  <h6 class="fw-bold mb-0">Asrama Santri Putra & Putri</h6>
                  <small class="text-muted">Gedung terpisah dengan pengawasan ustadz 24 jam</small>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
                <i class="bi bi-pc-display text-warning fs-4"></i>
                <div>
                  <h6 class="fw-bold mb-0">Laboratorium Komputer</h6>
                  <small class="text-muted">Dilengkapi koneksi internet edukatif berkecepatan tinggi</small>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
                <i class="bi bi-book-half text-danger fs-4"></i>
                <div>
                  <h6 class="fw-bold mb-0">Perpustakaan Kitab & Buku</h6>
                  <small class="text-muted">Ribuan koleksi kitab klasik dan buku literatur modern</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
