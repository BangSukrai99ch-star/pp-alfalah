<?php
/** @var array $item */
/** @var array $pengaturan */
$nomor = nomor_surat('PSB', (int)$item['id'], $item['validated_at'] ?? $item['created_at']);
$nama_program = $item['jenis_jenjang'] === 'diniyyah'
    ? 'Diniyyah (Kajian Keagamaan Ula/Wustho/Majlis)'
    : 'TPQ & PSQ (Taman Pendidikan Al-Qur\'an dan Program Sorogan Qur\'an)';
$nama_wali = $item['nama_wali'] ?: ($item['nama_ayah'] ?: '-');
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
  <h5 class="text-decoration-underline fw-bold text-uppercase mb-1">Surat Keterangan Penerimaan Santri Baru</h5>
  <p class="mb-0">Nomor: <?= e($nomor) ?></p>
</div>

<p>
  Pimpinan <?= e($pengaturan['nama_pesantren'] ?? 'Pondok Pesantren Al-Falah') ?> dengan ini
  menerangkan bahwa santri berikut telah dinyatakan <strong>DITERIMA</strong> sebagai santri baru
  di lingkungan pondok pesantren:
</p>

<table class="table table-borderless table-sm mb-4" style="max-width:640px">
  <tr><td style="width:230px">Nama Santri</td><td>: <strong><?= e($item['nama_lengkap']) ?></strong></td></tr>
  <tr><td>Jenis Kelamin</td><td>: <?= $item['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td></tr>
  <tr><td>Tempat, Tanggal Lahir</td><td>: <?= e($item['tempat_lahir'] ?: '-') ?><?= $item['tanggal_lahir'] ? ', ' . format_tanggal($item['tanggal_lahir']) : '' ?></td></tr>
  <tr><td>Nama Wali / Orang Tua</td><td>: <?= e($nama_wali) ?></td></tr>
  <tr><td>Program</td><td>: <?= $nama_program ?></td></tr>
  <tr><td>Jenjang / Tingkat</td><td>: <?= e($item['jenjang_dipilih']) ?><?= $item['tingkat_dipilih'] ? ' &mdash; Kelas ' . e($item['tingkat_dipilih']) : '' ?></td></tr>
  <tr><td>Tahun Ajaran</td><td>: <?= e($item['tahun_ajaran'] ?: '-') ?></td></tr>
</table>

<p>
  Santri yang bersangkutan resmi menjadi bagian dari keluarga besar pondok pesantren dan wajib
  mengikuti seluruh tata tertib, adab, serta kegiatan pendidikan salaf yang berlaku, terhitung
  sejak tanggal ditetapkannya surat ini.
</p>

<p>Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>

<div class="row mt-5">
  <div class="col-7"></div>
  <div class="col-5 text-center">
    <p class="mb-0">
      <?= format_tanggal($item['validated_at'] ?? $item['created_at'] ?? date('Y-m-d')) ?>
    </p>
    <p class="mb-0">Pengasuh / Pengelola Pondok</p>
    <div style="height:80px"></div>
    <p class="fw-bold text-decoration-underline mb-0"><?= e($item['validator_nama'] ?? '-') ?></p>
  </div>
</div>
