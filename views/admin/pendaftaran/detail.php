<?php
/** @var array $item */
/** @var array $all_daftar */
$view_file = 'admin/pendaftaran/detail';
?>

<div class="page-header">
  <div>
    <h1 class="page-title"><i class="bi bi-person-vcard-fill me-2 text-primary"></i>Detail Pendaftaran</h1>
    <p class="page-subtitle"><?= e($item['nama_lengkap']) ?> — <?= ucfirst(e($item['jenis_jenjang'])) ?></p>
  </div>
  <div class="d-flex gap-2">
    <a href="<?= BASE_URL ?>/pendaftaran" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
    <?php if ($item['status'] === 'pending'): ?>
      <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalTerima">
        <i class="bi bi-check-circle me-1"></i>Terima
      </button>
      <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalTolak">
        <i class="bi bi-x-circle me-1"></i>Tolak
      </button>
    <?php elseif ($item['status'] === 'diterima'): ?>
      <a href="<?= BASE_URL ?>/surat/penerimaan/<?= $item['id'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-printer me-1"></i>Cetak Surat Penerimaan
      </a>
    <?php endif; ?>
  </div>
</div>

<!-- Status Banner -->
<div class="alert <?= $item['status']==='diterima' ? 'alert-success' : ($item['status']==='ditolak' ? 'alert-danger' : 'alert-warning') ?> mb-4">
  <strong>Status Pendaftaran (<?= e($item['jenjang_dipilih']) ?><?= !empty($item['tingkat_dipilih']) ? ' — Kelas ' . e($item['tingkat_dipilih']) : '' ?>):</strong>
  <?= status_badge($item['status']) ?>
  <?php if ($item['status'] !== 'pending' && $item['validator_nama']): ?>
    — Divalidasi oleh <strong><?= e($item['validator_nama']) ?></strong>
    pada <?= format_tanggal($item['validated_at'], 'd F Y H:i') ?>
  <?php endif; ?>
  <?php if ($item['catatan_admin']): ?>
    <div class="mt-2"><strong>Catatan Admin:</strong> <?= e($item['catatan_admin']) ?></div>
  <?php endif; ?>
</div>

<div class="row g-4">
  <!-- Biodata Santri -->
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header"><i class="bi bi-person-fill me-2"></i>Biodata Santri</div>
      <div class="card-body">
        <div class="d-flex align-items-start gap-3 mb-3">
          <?php if (!empty($item['foto'])): ?>
            <img src="<?= UPLOAD_URL . e($item['foto']) ?>" class="rounded" style="width:80px;height:80px;object-fit:cover;" alt="Foto">
          <?php else: ?>
            <div style="width:80px;height:80px;background:#e2f0e4;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:2rem;color:#4f9560">
              <i class="bi bi-person-fill"></i>
            </div>
          <?php endif; ?>
          <div>
            <h5 class="mb-1"><?= e($item['nama_lengkap']) ?></h5>
            <span class="badge <?= $item['jenis_kelamin']==='L'?'bg-info':'bg-danger' ?>"><?= $item['jenis_kelamin']==='L'?'Laki-laki':'Perempuan' ?></span>
          </div>
        </div>
        <table class="table table-sm table-borderless">
          <tr><td class="text-muted" style="width:40%">Tempat / Tgl Lahir</td>
              <td><?= e($item['tempat_lahir'] ?? '-') ?>, <?= format_tanggal($item['tanggal_lahir'] ?? '') ?></td></tr>
          <tr><td class="text-muted">Agama</td><td><?= e($item['agama'] ?? 'Islam') ?></td></tr>
          <tr><td class="text-muted">Anak ke-</td><td><?= e($item['anak_ke'] ?? '-') ?></td></tr>
          <tr><td class="text-muted">Alamat</td><td><?= nl2br(e($item['alamat'] ?? '-')) ?></td></tr>
          <?php if ($item['kelurahan']): ?>
            <tr><td class="text-muted">Kel/Kec</td><td><?= e($item['kelurahan']) ?> / <?= e($item['kecamatan'] ?? '') ?></td></tr>
            <tr><td class="text-muted">Kab/Prov</td><td><?= e($item['kabupaten'] ?? '') ?> / <?= e($item['provinsi'] ?? '') ?></td></tr>
          <?php endif; ?>
          <tr><td class="text-muted">NISN</td><td><?= e($item['nisn'] ?? '-') ?></td></tr>
          <tr><td class="text-muted">NIS Pesantren</td><td><?= e($item['nis_pesantren'] ?? '<em class="text-muted">Belum ditetapkan</em>') ?></td></tr>
        </table>
      </div>
    </div>
  </div>

  <!-- Data Orang Tua & Semua Pendaftaran -->
  <div class="col-lg-6">
    <div class="card mb-3">
      <div class="card-header"><i class="bi bi-people-fill me-2"></i>Data Orang Tua / Wali</div>
      <div class="card-body">
        <table class="table table-sm table-borderless">
          <tr><td class="text-muted" style="width:40%">Nama Ayah</td><td><?= e($item['nama_ayah'] ?? '-') ?> <small class="text-muted">(<?= e($item['pekerjaan_ayah'] ?? '') ?>)</small></td></tr>
          <tr><td class="text-muted">Nama Ibu</td><td><?= e($item['nama_ibu'] ?? '-') ?> <small class="text-muted">(<?= e($item['pekerjaan_ibu'] ?? '') ?>)</small></td></tr>
          <?php if ($item['nama_wali']): ?>
            <tr><td class="text-muted">Wali</td><td><?= e($item['nama_wali']) ?> <small class="text-muted">(<?= e($item['hubungan_wali'] ?? '') ?>)</small></td></tr>
          <?php endif; ?>
          <tr><td class="text-muted">No. HP Wali</td><td><a href="tel:<?= e($item['no_hp_wali']) ?>"><?= e($item['no_hp_wali'] ?? '-') ?></a></td></tr>
          <tr><td class="text-muted">Email Wali</td><td><?= e($item['email_wali'] ?? '-') ?></td></tr>
        </table>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><i class="bi bi-stack me-2"></i>Semua Pendaftaran Santri Ini</div>
      <div class="list-group list-group-flush">
        <?php foreach ($all_daftar as $ad): ?>
        <div class="list-group-item py-2">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <span class="badge bg-secondary me-1"><?= ucfirst(e($ad['jenis_jenjang'])) ?></span>
              <strong><?= e($ad['jenjang_dipilih']) ?></strong>
              <?php if (!empty($ad['tingkat_dipilih'])): ?><span class="text-muted">— Kelas <?= e($ad['tingkat_dipilih']) ?></span><?php endif; ?>
              <small class="text-muted ms-1"><?= e($ad['tahun_ajaran'] ?? '') ?></small>
            </div>
            <?= status_badge($ad['status']) ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<!-- Tgl Daftar info -->
<div class="mt-3 text-muted small text-end">
  Mendaftar pada: <?= format_tanggal($item['created_at'], 'd F Y H:i') ?>
</div>

<?php if ($item['status'] === 'pending'): ?>
<!-- Modal Terima -->
<div class="modal fade" id="modalTerima" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title"><i class="bi bi-check-circle-fill me-2"></i>Terima Pendaftaran</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= BASE_URL ?>/pendaftaran/validasi/<?= $item['id'] ?>" method="post">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="status" value="diterima">
        <div class="modal-body">
          <p>Konfirmasi penerimaan pendaftaran <strong><?= e($item['nama_lengkap']) ?></strong> untuk jenjang <strong><?= e($item['jenjang_dipilih']) ?><?= !empty($item['tingkat_dipilih']) ? ' — Kelas ' . e($item['tingkat_dipilih']) : '' ?></strong>.</p>
          <div class="mb-3">
            <label class="form-label">Catatan (opsional)</label>
            <textarea class="form-control" name="catatan_admin" rows="3"
                      placeholder="Misal: Silakan melunasi biaya pendaftaran sebelum 30 Juli 2025..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Ya, Terima</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Tolak -->
<div class="modal fade" id="modalTolak" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title"><i class="bi bi-x-circle-fill me-2"></i>Tolak Pendaftaran</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= BASE_URL ?>/pendaftaran/validasi/<?= $item['id'] ?>" method="post">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="status" value="ditolak">
        <div class="modal-body">
          <p>Konfirmasi penolakan pendaftaran <strong><?= e($item['nama_lengkap']) ?></strong> untuk jenjang <strong><?= e($item['jenjang_dipilih']) ?><?= !empty($item['tingkat_dipilih']) ? ' — Kelas ' . e($item['tingkat_dipilih']) : '' ?></strong>.</p>
          <div class="mb-3">
            <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
            <textarea class="form-control" name="catatan_admin" rows="3" required
                      placeholder="Misal: Kuota sudah penuh / Berkas tidak lengkap..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger"><i class="bi bi-x-lg me-1"></i>Ya, Tolak</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>
