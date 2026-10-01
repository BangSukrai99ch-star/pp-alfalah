<?php
/** @var ?array $item */
$view_file = 'admin/kegiatan/form';
$is_edit = !empty($item);
?>

<div class="page-header">
  <div>
    <h1 class="page-title">
      <i class="bi bi-<?= $is_edit ? 'pencil-square' : 'plus-circle' ?> me-2 text-success"></i>
      <?= $is_edit ? 'Edit Konten' : 'Tambah Konten Baru' ?>
    </h1>
  </div>
  <a href="<?= BASE_URL ?>/kegiatan" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>

<form action="<?= BASE_URL ?>/kegiatan/<?= $is_edit ? 'edit/'.$item['id'] : 'tambah' ?>" method="post"
      enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

  <div class="row g-4">
    <!-- Main Content -->
    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Judul Konten <span class="text-danger">*</span></label>
            <input type="text" class="form-control form-control-lg" name="judul"
                   value="<?= e($item['judul'] ?? '') ?>" placeholder="Masukkan judul..." required>
          </div>

          <div class="mb-3">
            <label class="form-label">Konten / Isi</label>
            <textarea class="form-control" name="konten" rows="14" id="editorKonten"
                      placeholder="Tulis konten di sini..."><?= e($item['konten'] ?? '') ?></textarea>
            <div class="form-text">Mendukung format HTML dasar (&lt;p&gt;, &lt;b&gt;, &lt;ul&gt;, &lt;a&gt;, dll.)</div>
          </div>

          <div class="mb-3">
            <label class="form-label">Tags</label>
            <input type="text" class="form-control" name="tags"
                   value="<?= e($item['tags'] ?? '') ?>" placeholder="Contoh: pesantren, kegiatan, ramadhan">
            <div class="form-text">Pisahkan dengan koma.</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
      <!-- Publish Settings -->
      <div class="card mb-3">
        <div class="card-header">⚙️ Pengaturan Publikasi</div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Tipe Konten <span class="text-danger">*</span></label>
            <select class="form-select" name="tipe" required>
              <option value="berita"  <?= ($item['tipe']??'berita')==='berita'  ? 'selected' : '' ?>>📰 Berita</option>
              <option value="artikel" <?= ($item['tipe']??'')==='artikel' ? 'selected' : '' ?>>📄 Artikel</option>
              <option value="galeri"  <?= ($item['tipe']??'')==='galeri'  ? 'selected' : '' ?>>📷 Galeri</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" name="status">
              <option value="draft"   <?= ($item['status']??'')==='draft'   ? 'selected' : '' ?>>○ Draft</option>
              <option value="publish" <?= ($item['status']??'')==='publish' ? 'selected' : '' ?>>● Publish</option>
            </select>
          </div>
          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-success">
              <i class="bi bi-save me-1"></i><?= $is_edit ? 'Simpan Perubahan' : 'Publikasikan Konten' ?>
            </button>
            <a href="<?= BASE_URL ?>/kegiatan" class="btn btn-outline-secondary">Batal</a>
          </div>
        </div>
      </div>

      <!-- Gambar Utama -->
      <div class="card mb-3">
        <div class="card-header">🖼️ Gambar Utama</div>
        <div class="card-body">
          <?php if ($is_edit && !empty($item['gambar'])): ?>
            <?php if (is_video_file($item['gambar'])): ?>
              <video src="<?= UPLOAD_URL . e($item['gambar']) ?>" class="img-fluid rounded mb-2" id="previewGambar" controls preload="metadata"></video>
            <?php else: ?>
              <img src="<?= UPLOAD_URL . e($item['gambar']) ?>" class="img-fluid rounded mb-2" id="previewGambar" alt="Preview">
            <?php endif; ?>
          <?php else: ?>
            <img src="" class="img-fluid rounded mb-2 d-none" id="previewGambar" alt="Preview">
          <?php endif; ?>
          <input type="file" class="form-control" name="gambar" accept="image/*,video/*" data-preview="previewGambar" data-compress-image="true">
          <div class="form-text">Gambar otomatis dikompresi hingga maksimal 5MB. Video konten tidak dibatasi oleh batas gambar.</div>
        </div>
      </div>

      <!-- Galeri Multi-Foto -->
      <div class="card" id="galeriSection" style="display: <?= ($item['tipe']??'berita')==='galeri' ? 'block' : 'none' ?>">
        <div class="card-header">📷 Foto Galeri (Multi)</div>
        <div class="card-body">
          <?php if ($is_edit && !empty($item['galeri_foto'])): ?>
            <?php $fotos = json_decode($item['galeri_foto'], true) ?? []; ?>
            <?php if ($fotos): ?>
              <div class="row g-1 mb-2">
                <?php foreach ($fotos as $f): ?>
                  <div class="col-4">
                    <img src="<?= UPLOAD_URL . e($f) ?>" class="img-fluid rounded" style="aspect-ratio:1;object-fit:cover" alt="">
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          <?php endif; ?>
          <input type="file" class="form-control" name="galeri_foto[]" accept="image/*" multiple data-compress-image="true">
          <div class="form-text">Pilih banyak foto sekaligus. Foto lama akan tetap disimpan.</div>
        </div>
      </div>
    </div>
  </div>
</form>

<script>
// Show/hide galeri section based on tipe
document.querySelector('select[name="tipe"]').addEventListener('change', function() {
  document.getElementById('galeriSection').style.display = this.value === 'galeri' ? 'block' : 'none';
});
</script>
