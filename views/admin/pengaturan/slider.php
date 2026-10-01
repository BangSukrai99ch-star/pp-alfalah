<?php
/** @var array $sliders */
$view_file = 'admin/pengaturan/slider';
?>

<div class="page-header">
  <div>
    <h1 class="page-title"><i class="bi bi-images me-2 text-primary"></i>Kelola Slider & Banner</h1>
    <p class="page-subtitle">Banner carousel halaman depan website pesantren</p>
  </div>
  <div>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSlider">
      <i class="bi bi-plus-circle me-1"></i>Tambah Slider
    </button>
  </div>
</div>

<div class="card mb-4">
  <div class="card-header bg-white d-flex justify-content-between align-items-center">
    <span><i class="bi bi-card-image me-2 text-info"></i>Daftar Banner Slider Aktif</span>
    <span class="badge bg-secondary"><?= count($sliders) ?> Banner</span>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width: 80px;">Urutan</th>
            <th style="width: 160px;">Banner</th>
            <th>Judul & Deskripsi</th>
            <th>Tautan / Link</th>
            <th>Status</th>
            <th style="width: 100px;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($sliders)): ?>
            <tr>
              <td colspan="6" class="text-center py-4 text-muted">
                Belum ada banner slider. Klik tombol <strong>Tambah Slider</strong> untuk mengunggah banner depan.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($sliders as $s): ?>
              <tr>
                <td class="text-center fw-bold fs-6"><?= $s['urutan'] ?></td>
                <td>
                  <?php if (!empty($s['gambar'])): ?>
                    <img src="<?= UPLOAD_URL . e($s['gambar']) ?>" alt="Banner" class="rounded object-fit-cover shadow-sm" style="width: 140px; height: 75px;">
                  <?php else: ?>
                    <span class="text-muted small">No Image</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="fw-bold mb-1"><?= e($s['judul'] ?? '-') ?></div>
                  <small class="text-muted"><?= e($s['deskripsi'] ?? '-') ?></small>
                </td>
                <td>
                  <?php if (!empty($s['link'])): ?>
                    <a href="<?= e($s['link']) ?>" target="_blank" class="small text-truncate d-inline-block max-w-200">
                      <i class="bi bi-box-arrow-up-right me-1"></i> <?= e($s['link']) ?>
                    </a>
                  <?php else: ?>
                    <span class="text-muted small">-</span>
                  <?php endif; ?>
                </td>
                <td>
                  <span class="badge <?= $s['aktif'] ? 'bg-success' : 'bg-secondary' ?>">
                    <?= $s['aktif'] ? 'Tampil' : 'Nonaktif' ?>
                  </span>
                </td>
                <td>
                  <form action="<?= BASE_URL ?>/pengaturan/slider_hapus/<?= $s['id'] ?>" method="post" onsubmit="return confirm('Yakin ingin menghapus banner slider ini?')">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Tambah Slider -->
<div class="modal fade" id="modalTambahSlider" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-primary me-2"></i>Tambah Banner Slider</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= BASE_URL ?>/pengaturan/slider_simpan" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-bold">Gambar Banner <span class="text-danger">*</span></label>
            <input type="file" name="gambar" class="form-control" accept="image/*" data-compress-image="true" required>
            <small class="text-muted">Ukuran ideal: 1920x800 px (Rasio 21:9 atau 16:9), Maks 5MB.</small>
          </div>
          <div class="mb-3">
            <label class="form-label">Judul Banner</label>
            <input type="text" name="judul" class="form-control" placeholder="Contoh: Selamat Datang di Pondok Pesantren">
          </div>
          <div class="mb-3">
            <label class="form-label">Deskripsi Singkat</label>
            <textarea name="deskripsi" class="form-control" rows="2" placeholder="Teks keterangan yang muncul di atas banner..."></textarea>
          </div>
          <div class="row g-3">
            <div class="col-8">
              <label class="form-label">Link Tujuan (Opsional)</label>
              <input type="text" name="link" class="form-control" placeholder="Contoh: /daftar atau https://...">
            </div>
            <div class="col-4">
              <label class="form-label">Urutan</label>
              <input type="number" name="urutan" class="form-control" value="0">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1"></i> Upload Banner</button>
        </div>
      </form>
    </div>
  </div>
</div>
