<?php $view_file = 'admin/pengaturan/web'; ?>

<div class="page-header">
  <div>
    <h1 class="page-title"><i class="bi bi-gear-fill me-2 text-primary"></i>Pengaturan Website</h1>
    <p class="page-subtitle">Konfigurasi tampilan dan informasi website pesantren</p>
  </div>
</div>

<form action="<?= BASE_URL ?>/pengaturan" method="post" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

  <div class="row g-4">
    <!-- Informasi Dasar -->
    <div class="col-lg-8">
      <div class="card mb-4">
        <div class="card-header">📋 Informasi Dasar Pesantren</div>
        <div class="card-body row g-3">
          <div class="col-12">
            <label class="form-label">Nama Pesantren <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="nama_pesantren" value="<?= e($settings['nama_pesantren'] ?? '') ?>" required>
          </div>
          <div class="col-12">
            <label class="form-label">Tagline / Slogan</label>
            <input type="text" class="form-control" name="tagline" value="<?= e($settings['tagline'] ?? '') ?>" placeholder="Slogan pesantren...">
          </div>
          <div class="col-12">
            <label class="form-label">Alamat Lengkap</label>
            <textarea class="form-control" name="alamat" rows="2"><?= e($settings['alamat'] ?? '') ?></textarea>
          </div>
        </div>
      </div>

      <div class="card mb-4" id="kontak-pesantren">
        <div class="card-header"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Kontak & Media Sosial</div>
        <div class="card-body row g-3">
          <div class="col-md-6">
            <label class="form-label" for="telepon">Nomor Telepon / WhatsApp</label>
            <input type="tel" class="form-control" id="telepon" name="telepon" value="<?= e($settings['telepon'] ?? '') ?>" placeholder="08xxxxxxxxxx">
            <div class="form-text">Ditampilkan sebagai kontak utama di halaman Hubungi Kami.</div>
          </div>
          <div class="col-md-6">
            <label class="form-label" for="email">Email Resmi</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= e($settings['email'] ?? '') ?>" placeholder="sekretariat@pesantren.id">
          </div>
          <div class="col-md-4">
            <label class="form-label" for="facebook_url"><i class="bi bi-facebook me-1"></i>Facebook</label>
            <input type="url" class="form-control" id="facebook_url" name="facebook_url" value="<?= e($settings['facebook_url'] ?? '') ?>" placeholder="https://facebook.com/...">
          </div>
          <div class="col-md-4">
            <label class="form-label" for="instagram_url"><i class="bi bi-instagram me-1"></i>Instagram</label>
            <input type="url" class="form-control" id="instagram_url" name="instagram_url" value="<?= e($settings['instagram_url'] ?? '') ?>" placeholder="https://instagram.com/...">
          </div>
          <div class="col-md-4">
            <label class="form-label" for="youtube_url"><i class="bi bi-youtube me-1"></i>YouTube</label>
            <input type="url" class="form-control" id="youtube_url" name="youtube_url" value="<?= e($settings['youtube_url'] ?? '') ?>" placeholder="https://youtube.com/@...">
          </div>
          <div class="col-12">
            <label class="form-label" for="maps_embed"><i class="bi bi-map me-1"></i>Lokasi Google Maps</label>
            <textarea class="form-control" id="maps_embed" name="maps_embed" rows="4" placeholder="Tempel kode Embed dari Google Maps di sini..."><?= e($settings['maps_embed'] ?? '') ?></textarea>
            <div class="form-text">Google Maps → Bagikan → Sematkan peta → Salin HTML. Peta akan tampil di halaman Hubungi Kami.</div>
          </div>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header">📖 Visi, Misi & Sejarah</div>
        <div class="card-body row g-3">
          <div class="col-12">
            <label class="form-label">Visi</label>
            <textarea class="form-control" name="visi" rows="3"><?= e($settings['visi'] ?? '') ?></textarea>
          </div>
          <div class="col-12">
            <label class="form-label">Misi</label>
            <textarea class="form-control" name="misi" rows="4"><?= e($settings['misi'] ?? '') ?></textarea>
          </div>
          <div class="col-12">
            <label class="form-label">Sejarah Singkat</label>
            <textarea class="form-control" name="sejarah" rows="4"><?= e($settings['sejarah'] ?? '') ?></textarea>
          </div>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header">📢 Pengumuman Website</div>
        <div class="card-body">
          <label class="form-label">Teks Pengumuman (tampil di homepage)</label>
          <textarea class="form-control" name="pengumuman" rows="4"
                    placeholder="Pengumuman penting untuk calon santri..."><?= e($settings['pengumuman'] ?? '') ?></textarea>
        </div>

        <div class="card mb-4">
          <div class="card-header"><i class="bi bi-layout-text-window-reverse me-2 text-primary"></i>Konten Halaman Publik</div>
          <div class="card-body row g-3">
            <div class="col-12">
              <label class="form-label">Sambutan Beranda</label>
              <textarea class="form-control" name="beranda_sambutan" rows="4"
                        placeholder="Teks sambutan yang tampil pada bagian utama Beranda..."><?= e($settings['beranda_sambutan'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
              <label class="form-label">Tentang Pesantren</label>
              <textarea class="form-control" name="tentang_konten" rows="6"
                        placeholder="Isi pengenalan pesantren yang tampil pada Profil > Tentang..."><?= e($settings['tentang_konten'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
              <label class="form-label">Pengantar Publikasi</label>
              <textarea class="form-control" name="publikasi_konten" rows="4"
                        placeholder="Pengantar yang tampil di halaman Publikasi..."><?= e($settings['publikasi_konten'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
              <div class="form-text">Konten ini dikelola Master Admin dan tampil langsung di website utama. Artikel, kegiatan, dan galeri tetap dikelola dari menu Publikasi.</div>
            </div>
          </div>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header">🔗 Footer & Peta</div>
        <div class="card-body row g-3">
          <div class="col-md-6">
            <label class="form-label">Footer Text</label>
            <input type="text" class="form-control" name="footer_text" value="<?= e($settings['footer_text'] ?? '') ?>">
          </div>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header">🎓 Pengaturan PSB Online</div>
        <div class="card-body row g-3">
          <div class="col-md-4">
            <label class="form-label">Status PSB</label>
            <div class="form-check form-switch mt-2">
              <input class="form-check-input" type="checkbox" name="psb_aktif" value="1"
                     id="psbSwitch" <?= !empty($settings['psb_aktif']) ? 'checked' : '' ?>>
              <label class="form-check-label" for="psbSwitch">
                <span id="psbLabel"><?= !empty($settings['psb_aktif']) ? 'PSB DIBUKA' : 'PSB DITUTUP' ?></span>
              </label>
            </div>
          </div>
          <div class="col-md-4">
            <label class="form-label">Tahun Ajaran</label>
            <input type="text" class="form-control" name="psb_tahun_ajaran"
                   value="<?= e($settings['psb_tahun_ajaran'] ?? '') ?>" placeholder="2025/2026">
          </div>
          <div class="col-12">
            <label class="form-label">Keterangan PSB</label>
            <textarea class="form-control" name="psb_keterangan" rows="2"
                      placeholder="Info tambahan tentang PSB..."><?= e($settings['psb_keterangan'] ?? '') ?></textarea>
          </div>
        </div>
      </div>
    </div>

    <!-- Sidebar: Logo & Gambar -->
    <div class="col-lg-4">
      <div class="card mb-4 sticky-top" style="top:80px">
        <div class="card-header">🖼️ Logo & Gambar</div>
        <div class="card-body">
          <div class="mb-4">
            <label class="form-label fw-semibold">Logo Pesantren</label>
            <?php if (!empty($settings['logo'])): ?>
              <div class="mb-2 text-center">
                <img src="<?= UPLOAD_URL . e($settings['logo']) ?>" style="max-height:80px;" id="previewLogo" alt="Logo">
              </div>
            <?php else: ?>
              <img src="" class="img-fluid mb-2 d-none" id="previewLogo" alt="Logo">
            <?php endif; ?>
            <input type="file" class="form-control" name="logo" accept="image/*" data-preview="previewLogo" data-compress-image="true">
            <div class="form-text">PNG transparan direkomendasikan. Maks 2MB.</div>
          </div>

          <div class="mb-4">
            <label class="form-label fw-semibold">Background Hero</label>
            <?php if (!empty($settings['hero_bg'])): ?>
              <div class="mb-2">
                <img src="<?= UPLOAD_URL . e($settings['hero_bg']) ?>" class="img-fluid rounded" id="previewHeroBg" alt="Hero BG">
              </div>
            <?php else: ?>
              <img src="" class="img-fluid rounded mb-2 d-none" id="previewHeroBg" alt="Hero BG">
            <?php endif; ?>
            <input type="file" class="form-control" name="hero_bg" accept="image/*" data-preview="previewHeroBg" data-compress-image="true">
            <div class="form-text">Gambar latar halaman utama. Maks 5MB.</div>
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save me-1"></i>Simpan Pengaturan
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>

<script>
document.getElementById('psbSwitch').addEventListener('change', function() {
  document.getElementById('psbLabel').textContent = this.checked ? 'PSB DIBUKA' : 'PSB DITUTUP';
  document.getElementById('psbLabel').style.color  = this.checked ? '#16835b' : '#7c4a35';
  document.getElementById('psbLabel').style.fontWeight = '700';
});
</script>
