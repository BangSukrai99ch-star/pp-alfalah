<?php
/** @var array $santri */
$view_file = 'admin/santri/form';
?>

<div class="page-header">
  <div>
    <h1 class="page-title"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Data Santri</h1>
    <p class="page-subtitle">Perbarui biodata santri dan kontak wali</p>
  </div>
  <div>
    <a href="<?= BASE_URL ?>/santri/detail/<?= $santri['id'] ?>" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-1"></i>Batal
    </a>
  </div>
</div>

<form action="<?= BASE_URL ?>/santri/edit/<?= $santri['id'] ?>" method="post" enctype="multipart/form-data">
  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

  <div class="row g-4">
    <!-- Biodata Santri -->
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header bg-white"><i class="bi bi-person-fill me-2 text-primary"></i>Biodata Santri</div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="nama_lengkap" class="form-control" value="<?= e($santri['nama_lengkap'] ?? '') ?>" required>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-bold">NIS Pesantren</label>
              <input type="text" name="nis_pesantren" class="form-control" value="<?= e($santri['nis_pesantren'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">NISN</label>
              <input type="text" name="nisn" class="form-control" value="<?= e($santri['nisn'] ?? '') ?>">
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-bold">Jenis Kelamin</label>
              <select name="jenis_kelamin" class="form-select">
                <option value="L" <?= ($santri['jenis_kelamin'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                <option value="P" <?= ($santri['jenis_kelamin'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Status Santri</label>
              <select name="status" class="form-select">
                <option value="aktif" <?= ($santri['status'] ?? '') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                <option value="alumni" <?= ($santri['status'] ?? '') === 'alumni' ? 'selected' : '' ?>>Alumni / Lulus</option>
                <option value="keluar" <?= ($santri['status'] ?? '') === 'keluar' ? 'selected' : '' ?>>Keluar / Pindah</option>
              </select>
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">Tempat Lahir</label>
              <input type="text" name="tempat_lahir" class="form-control" value="<?= e($santri['tempat_lahir'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Tanggal Lahir</label>
              <input type="date" name="tanggal_lahir" class="form-control" value="<?= e($santri['tanggal_lahir'] ?? '') ?>">
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">Agama</label>
              <input type="text" name="agama" class="form-control" value="<?= e($santri['agama'] ?? 'Islam') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Anak ke-</label>
              <input type="number" name="anak_ke" class="form-control" value="<?= e($santri['anak_ke'] ?? '1') ?>">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Foto Santri</label>
            <?php if (!empty($santri['foto'])): ?>
              <div class="mb-2">
                <img src="<?= UPLOAD_URL . e($santri['foto']) ?>" class="rounded" style="width:60px;height:60px;object-fit:cover;" alt="Foto saat ini">
                <small class="text-muted ms-2">Foto saat ini</small>
              </div>
            <?php endif; ?>
            <input type="file" name="foto" class="form-control" accept="image/*" data-compress-image="true">
            <small class="text-muted">Format: JPG/PNG/WEBP, Maks. 5MB</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Alamat & Data Orang Tua -->
    <div class="col-lg-6">
      <div class="card mb-4">
        <div class="card-header bg-white"><i class="bi bi-geo-alt-fill me-2 text-danger"></i>Alamat Lengkap</div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Alamat / Jalan / RT RW</label>
            <textarea name="alamat" class="form-control" rows="2"><?= e($santri['alamat'] ?? '') ?></textarea>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">Kelurahan / Desa</label>
              <input type="text" name="kelurahan" class="form-control" value="<?= e($santri['kelurahan'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Kecamatan</label>
              <input type="text" name="kecamatan" class="form-control" value="<?= e($santri['kecamatan'] ?? '') ?>">
            </div>
          </div>

          <div class="row g-3">
            <div class="col-md-5">
              <label class="form-label">Kabupaten / Kota</label>
              <input type="text" name="kabupaten" class="form-control" value="<?= e($santri['kabupaten'] ?? '') ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label">Provinsi</label>
              <input type="text" name="provinsi" class="form-control" value="<?= e($santri['provinsi'] ?? '') ?>">
            </div>
            <div class="col-md-3">
              <label class="form-label">Kode Pos</label>
              <input type="text" name="kode_pos" class="form-control" value="<?= e($santri['kode_pos'] ?? '') ?>">
            </div>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header bg-white"><i class="bi bi-people-fill me-2 text-success"></i>Data Orang Tua & Wali</div>
        <div class="card-body">
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">Nama Ayah</label>
              <input type="text" name="nama_ayah" class="form-control" value="<?= e($santri['nama_ayah'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Pekerjaan Ayah</label>
              <input type="text" name="pekerjaan_ayah" class="form-control" value="<?= e($santri['pekerjaan_ayah'] ?? '') ?>">
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">Nama Ibu</label>
              <input type="text" name="nama_ibu" class="form-control" value="<?= e($santri['nama_ibu'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Pekerjaan Ibu</label>
              <input type="text" name="pekerjaan_ibu" class="form-control" value="<?= e($santri['pekerjaan_ibu'] ?? '') ?>">
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">Nama Wali</label>
              <input type="text" name="nama_wali" class="form-control" value="<?= e($santri['nama_wali'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Hubungan Wali</label>
              <input type="text" name="hubungan_wali" class="form-control" value="<?= e($santri['hubungan_wali'] ?? '') ?>">
            </div>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">No. HP / WhatsApp Wali</label>
              <input type="text" name="no_hp_wali" class="form-control" value="<?= e($santri['no_hp_wali'] ?? '') ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Email Wali</label>
              <input type="email" name="email_wali" class="form-control" value="<?= e($santri['email_wali'] ?? '') ?>">
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tombol Simpan -->
    <div class="col-12">
      <div class="card p-3 d-flex flex-row justify-content-between align-items-center bg-light">
        <span class="text-muted small">Pastikan data yang dimasukkan sudah benar.</span>
        <button type="submit" class="btn btn-primary px-4">
          <i class="bi bi-save me-1"></i> Simpan Perubahan
        </button>
      </div>
    </div>
  </div>
</form>
