<?php
/** @var array $item */
/** @var array $pengaturan */
$nomor = nomor_surat('KN', (int)$item['id'], $item['created_at']);
$nama_program = $item['jenis_jenjang'] === 'diniyyah' ? 'Diniyyah' : 'TPQ & PSQ';
$status_labels = [
    'naik'  => 'DINYATAKAN NAIK KE JENJANG/KELAS BERIKUTNYA',
    'tetap' => 'DINYATAKAN TETAP PADA JENJANG/KELAS SEMULA',
    'lulus' => 'DINYATAKAN TELAH MENYELESAIKAN SELURUH PROGRAM',
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
  <h5 class="text-decoration-underline fw-bold text-uppercase mb-1">Surat Keterangan Kenaikan Kelas</h5>
  <p class="mb-0">Nomor: <?= e($nomor) ?></p>
</div>

<p>
  Pimpinan <?= e($pengaturan['nama_pesantren'] ?? 'Pondok Pesantren Al-Falah') ?> dengan ini
  menerangkan bahwa berdasarkan hasil evaluasi pembelajaran tahun ajaran <strong><?= e($item['tahun_ajaran']) ?></strong>,
  santri berikut:
</p>

<table class="table table-borderless table-sm mb-4" style="max-width:640px">
  <tr><td style="width:230px">Nama Santri</td><td>: <strong><?= e($item['nama_lengkap']) ?></strong></td></tr>
  <tr><td>NIS Pesantren</td><td>: <?= e($item['nis_pesantren'] ?: '-') ?></td></tr>
  <tr><td>Program</td><td>: <?= e($nama_program) ?></td></tr>
  <tr><td>Jenjang / Kelas Asal</td><td>: <?= e($item['jenjang_asal']) ?><?= $item['kelas_asal'] ? ' &mdash; ' . e($item['kelas_asal']) : '' ?></td></tr>
  <?php if ($item['status'] !== 'lulus'): ?>
  <tr><td>Jenjang / Kelas Tujuan</td><td>: <?= e($item['jenjang_tujuan'] ?: '-') ?><?= $item['kelas_tujuan'] ? ' &mdash; ' . e($item['kelas_tujuan']) : '' ?></td></tr>
  <?php endif; ?>
  <tr><td>Tahun Ajaran</td><td>: <?= e($item['tahun_ajaran']) ?></td></tr>
</table>

<p class="fw-bold text-center text-uppercase"><?= e($status_labels[$item['status']] ?? '') ?></p>

<?php if (!empty($item['catatan'])): ?>
  <p><strong>Catatan:</strong> <?= nl2br(e($item['catatan'])) ?></p>
<?php endif; ?>

<p>Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>

<div class="row mt-5">
  <div class="col-7"></div>
  <div class="col-5 text-center">
    <p class="mb-0"><?= format_tanggal($item['created_at'] ?? date('Y-m-d')) ?></p>
    <p class="mb-0">Wali Kelas / Admin Program</p>
    <div style="height:80px"></div>
    <p class="fw-bold text-decoration-underline mb-0"><?= e($item['pemroses_nama'] ?? '-') ?></p>
  </div>
</div>
