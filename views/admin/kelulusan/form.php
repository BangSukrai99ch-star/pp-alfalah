<?php
/** @var array $santri_list */
/** @var array $options */
/** @var ?array $item */
/** @var string $jenjang_scope */
/** @var int $selected_santri_id */
$view_file = 'admin/kelulusan/form';
$is_edit = !empty($item);
?>

<div class="page-header">
  <div>
    <h1 class="page-title"><i class="bi bi-award-fill me-2 text-primary"></i><?= $is_edit ? 'Edit' : 'Catat' ?> Kelulusan</h1>
    <p class="page-subtitle">Kelulusan jenjang <?= ucfirst(e($jenjang_scope)) ?></p>
  </div>
  <a href="<?= BASE_URL ?>/kelulusan" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>

<form action="<?= BASE_URL ?>/kelulusan/simpan<?= $is_edit ? '/' . $item['id'] : '' ?>" method="post">
  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header"><i class="bi bi-person-vcard me-2"></i>Data Kelulusan</div>
        <div class="card-body row g-3">
          <div class="col-12">
            <label class="form-label">Santri <span class="text-danger">*</span></label>
            <select class="form-select" name="santri_id" required <?= $is_edit ? 'disabled' : '' ?>>
              <option value="">Pilih santri yang diterima</option>
              <?php foreach ($santri_list as $santri): ?>
                <option value="<?= $santri['id'] ?>" <?= (string)($item['santri_id'] ?? $selected_santri_id) === (string)$santri['id'] ? 'selected' : '' ?>>
                  <?= e($santri['nama_lengkap']) ?><?= !empty($santri['nis_pesantren']) ? ' - ' . e($santri['nis_pesantren']) : '' ?>
                </option>
              <?php endforeach; ?>
            </select>
            <?php if ($is_edit): ?><input type="hidden" name="santri_id" value="<?= $item['santri_id'] ?>"><?php endif; ?>
            <div class="form-text">Hanya santri dengan status pendaftaran diterima yang tersedia.</div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Jenjang <span class="text-danger">*</span></label>
            <select class="form-select" name="jenjang" required>
              <option value="">Pilih jenjang</option>
              <?php foreach ($options as $option): ?><option value="<?= e($option) ?>" <?= ($item['jenjang'] ?? '') === $option ? 'selected' : '' ?>><?= e($option) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="tahun_ajaran" value="<?= e($item['tahun_ajaran'] ?? '') ?>" placeholder="2025/2026" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Tanggal Lulus</label>
            <input type="date" class="form-control" name="tanggal_lulus" value="<?= e($item['tanggal_lulus'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Nomor Ijazah / Sertifikat</label>
            <input type="text" class="form-control" name="nomor_ijazah" value="<?= e($item['nomor_ijazah'] ?? '') ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Predikat</label>
            <input type="text" class="form-control" name="predikat" value="<?= e($item['predikat'] ?? '') ?>" placeholder="Sangat Baik">
          </div>
          <div class="col-md-6">
            <label class="form-label">Status <span class="text-danger">*</span></label>
            <select class="form-select" name="status" required>
              <?php foreach (['lulus' => 'Lulus', 'tidak_lulus' => 'Tidak Lulus', 'ditunda' => 'Ditunda'] as $key => $label): ?><option value="<?= $key ?>" <?= ($item['status'] ?? 'lulus') === $key ? 'selected' : '' ?>><?= $label ?></option><?php endforeach; ?>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Catatan</label>
            <textarea class="form-control" name="catatan" rows="4" placeholder="Catatan tambahan kelulusan..."><?= e($item['catatan'] ?? '') ?></textarea>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card border-success-subtle">
        <div class="card-body">
          <h5 class="fw-bold"><i class="bi bi-shield-check text-success me-2"></i>Konfirmasi Data</h5>
          <p class="text-muted small">Pastikan jenjang, tahun ajaran, dan status kelulusan sudah sesuai sebelum disimpan.</p>
          <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-1"></i>Simpan Kelulusan</button>
        </div>
      </div>
    </div>
  </div>
</form>
