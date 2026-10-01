<?php
/** @var string $jenis */
/** @var array $penempatan */
/** @var array $pilihan_tujuan */
/** @var int $selected_santri_id */
$view_file = 'admin/kenaikan/form';
?>

<div class="page-header">
  <div>
    <h1 class="page-title"><i class="bi bi-arrow-up-right-circle-fill me-2 text-success"></i>Catat Kenaikan Kelas</h1>
    <p class="page-subtitle">Kenaikan kelas <?= strtoupper(e($jenis)) ?> hanya untuk santri yang telah diterima pada program ini.</p>
  </div>
  <a href="<?= BASE_URL ?>/kenaikan" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>

<?php if (empty($penempatan)): ?>
  <div class="alert alert-info"><i class="bi bi-info-circle-fill me-2"></i>Belum ada santri aktif pada program ini yang dapat diproses kenaikan kelasnya.</div>
<?php else: ?>
<form action="<?= BASE_URL ?>/kenaikan/simpan" method="post">
  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header"><i class="bi bi-person-vcard me-2"></i>Data Kenaikan Kelas</div>
        <div class="card-body row g-3">
          <div class="col-12">
            <label class="form-label">Santri <span class="text-danger">*</span></label>
            <select class="form-select" name="santri_id" required>
              <option value="">Pilih santri</option>
              <?php foreach ($penempatan as $item): ?>
                <option value="<?= $item['santri_id'] ?>" <?= $selected_santri_id === (int)$item['santri_id'] ? 'selected' : '' ?>>
                  <?= e($item['nama_lengkap']) ?> — <?= e($item['jenjang']) ?><?= $item['kelas'] ? ' / ' . e($item['kelas']) : '' ?>
                </option>
              <?php endforeach; ?>
            </select>
            <div class="form-text">Kelas asal diambil otomatis dari pendaftaran diterima atau catatan kenaikan terakhir.</div>
          </div>
          <div class="col-md-6">
            <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
            <input class="form-control" name="tahun_ajaran" placeholder="2026/2027" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Hasil Evaluasi <span class="text-danger">*</span></label>
            <select class="form-select" name="status" id="statusKenaikan" required>
              <option value="naik">Naik Kelas / Tingkatan</option>
              <option value="tetap">Tetap di Kelas Saat Ini</option>
              <option value="lulus">Lulus Program</option>
            </select>
          </div>
          <div class="col-12" id="tujuanSection">
            <label class="form-label">Tujuan Kenaikan <span class="text-danger">*</span></label>
            <?php if ($jenis === 'diniyyah'): ?>
              <select class="form-select" name="kelas_tujuan_id" id="tujuanKenaikan">
                <option value="">Pilih jenjang dan kelas tujuan</option>
                <?php foreach ($pilihan_tujuan as $pilihan): ?>
                  <option value="<?= $pilihan['id'] ?>"><?= e($pilihan['label']) ?></option>
                <?php endforeach; ?>
              </select>
            <?php else: ?>
              <select class="form-select" name="tingkatan_tujuan" id="tujuanKenaikan">
                <option value="">Pilih tingkatan TPQ/PSQ tujuan</option>
                <?php foreach ($pilihan_tujuan as $pilihan): ?>
                  <option value="<?= e($pilihan['jenjang']) ?>"><?= e($pilihan['label']) ?></option>
                <?php endforeach; ?>
              </select>
            <?php endif; ?>
          </div>
          <div class="col-12">
            <label class="form-label">Catatan</label>
            <textarea class="form-control" name="catatan" rows="4" placeholder="Catatan evaluasi atau pertimbangan kenaikan kelas..."></textarea>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card border-success-subtle">
        <div class="card-body">
          <h5 class="fw-bold"><i class="bi bi-shield-check text-success me-2"></i>Kontrol Program</h5>
          <p class="text-muted small mb-3">Catatan tidak dapat dipakai oleh admin program lain. Satu santri hanya dapat diproses sekali untuk setiap tahun ajaran.</p>
          <button class="btn btn-primary w-100"><i class="bi bi-save me-1"></i>Simpan Kenaikan</button>
        </div>
      </div>
    </div>
  </div>
</form>
<script>
(() => {
  const status = document.getElementById('statusKenaikan');
  const tujuan = document.getElementById('tujuanSection');
  const pilihan = document.getElementById('tujuanKenaikan');
  const syncTujuan = () => {
    const wajibTujuan = status.value === 'naik';
    tujuan.classList.toggle('d-none', !wajibTujuan);
    pilihan.required = wajibTujuan;
    if (!wajibTujuan) pilihan.value = '';
  };
  status.addEventListener('change', syncTujuan);
  syncTujuan();
})();
</script>
<?php endif; ?>
