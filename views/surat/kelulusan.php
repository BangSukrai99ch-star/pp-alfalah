<?php
/** @var array $item */
/** @var array $pengaturan */
$nomor = $item['nomor_ijazah'] ?: nomor_surat('KL', (int)$item['id'], $item['tanggal_lulus'] ?? $item['created_at']);
$nama_program = $item['jenis_jenjang'] === 'diniyyah' ? 'Diniyyah' : 'TPQ & PSQ';
$status_labels = [
    'lulus'       => 'DINYATAKAN LULUS',
    'tidak_lulus' => 'DINYATAKAN TIDAK LULUS',
    'ditunda'     => 'KELULUSAN DITUNDA',
];
?>
<div class="surat-kop">
  <?php if (!empty($pengaturan['logo'])): ?>
    <img src="<?= UPLOAD_URL . e($pengaturan['logo']) ?>" alt="Logo">
  <?php endif; ?>
  <div>
    <h4 class="text-uppercase fw-bold"><?= e($pengaturan['nama_pesantren'] ?? 'Pondok Pesantren Al-Falah') ?></h4>
    <p class="fst-italic mb-1">Pesantren Salaf</p>
    <p><?= e($pengaturan['alamat'] ?? '') ?></p>
    <p>
      <?= !empty($pengaturan['telepon']) ? 'Telp/WA: ' . e($pengaturan['telepon']) : '' ?>
      <?= !empty($pengaturan['email']) ? ' &bull; Email: ' . e($pengaturan['email']) : '' ?>
    </p>
  </div>
</div>

<div class="surat-judul text-center mb-4">
  <h5 class="text-decoration-underline fw-bold text-uppercase mb-1">Surat Keterangan Kelulusan Jenjang</h5>
  <p class="mb-0">Nomor: <?= e($nomor) ?></p>
</div>

<p>
  Pimpinan <?= e($pengaturan['nama_pesantren'] ?? 'Pondok Pesantren Al-Falah') ?> dengan ini
  menerangkan bahwa santri berikut:
</p>

<table class="table table-borderless table-sm mb-4" style="max-width:640px">
  <tr><td style="width:230px">Nama Santri</td><td>: <strong><?= e($item['nama_lengkap']) ?></strong></td></tr>
  <tr><td>NIS Pesantren</td><td>: <?= e($item['nis_pesantren'] ?: '-') ?></td></tr>
  <tr><td>Tempat, Tanggal Lahir</td><td>: <?= e($item['tempat_lahir'] ?: '-') ?><?= $item['tanggal_lahir'] ? ', ' . format_tanggal($item['tanggal_lahir']) : '' ?></td></tr>
  <tr><td>Program</td><td>: <?= e($nama_program) ?></td></tr>
  <tr><td>Jenjang</td><td>: <?= e($item['jenjang']) ?></td></tr>
  <tr><td>Tahun Ajaran</td><td>: <?= e($item['tahun_ajaran']) ?></td></tr>
  <?php if (!empty($item['predikat'])): ?>
  <tr><td>Predikat</td><td>: <?= e($item['predikat']) ?></td></tr>
  <?php endif; ?>
  <?php if (!empty($item['tanggal_lulus'])): ?>
  <tr><td>Tanggal Penetapan</td><td>: <?= format_tanggal($item['tanggal_lulus']) ?></td></tr>
  <?php endif; ?>
</table>

<p class="fw-bold text-center text-uppercase"><?= e($status_labels[$item['status']] ?? '') ?> dari jenjang <?= e($item['jenjang']) ?> program <?= e($nama_program) ?></p>

<?php if (!empty($item['catatan'])): ?>
  <p><strong>Catatan:</strong> <?= nl2br(e($item['catatan'])) ?></p>
<?php endif; ?>

<p>
  Surat keterangan ini menjadi bukti sah bahwa santri yang bersangkutan telah menyelesaikan
  proses pembelajaran pada jenjang tersebut di lingkungan pondok pesantren salaf, dan dapat
  dipergunakan sebagaimana mestinya.
</p>

<div class="row mt-5">
  <div class="col-7"></div>
  <div class="col-5 text-center">
    <p class="mb-0"><?= format_tanggal($item['tanggal_lulus'] ?? $item['created_at'] ?? date('Y-m-d')) ?></p>
    <p class="mb-0">Pengasuh / Pengelola Pondok</p>
    <div style="height:80px"></div>
    <p class="fw-bold text-decoration-underline mb-0"><?= e($item['penetap_nama'] ?? '-') ?></p>
  </div>
</div>
