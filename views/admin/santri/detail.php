<?php
/** @var array $santri */
/** @var array $riwayat_daftar */
/** @var array $riwayat_kelas */
/** @var array $riwayat_kelulusan */
/** @var array $riwayat_kenaikan */
$view_file = 'admin/santri/detail';
?>

<div class="page-header">
  <div>
    <h1 class="page-title"><i class="bi bi-mortarboard-fill me-2 text-primary"></i>Detail Data Santri</h1>
    <p class="page-subtitle"><?= e($santri['nama_lengkap']) ?> (NIS: <?= e($santri['nis_pesantren'] ?? '-') ?>)</p>
  </div>
  <div class="d-flex gap-2">
    <a href="<?= BASE_URL ?>/santri" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
    <a href="<?= BASE_URL ?>/santri/edit/<?= $santri['id'] ?>" class="btn btn-primary btn-sm">
      <i class="bi bi-pencil-square me-1"></i>Edit Santri
    </a>
  </div>
</div>

<div class="row g-4">
  <!-- Kolom Kiri: Profil & Biodata -->
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header bg-white"><i class="bi bi-person-badge-fill me-2 text-primary"></i>Biodata Santri</div>
      <div class="card-body">
        <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-3">
          <?php if (!empty($santri['foto'])): ?>
            <img src="<?= UPLOAD_URL . e($santri['foto']) ?>" class="rounded-circle object-fit-cover shadow-sm" style="width:75px;height:75px;" alt="Foto">
          <?php else: ?>
            <div style="width:75px;height:75px;background:#e2f0e4;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2rem;color:#4f9560">
              <i class="bi bi-person-fill"></i>
            </div>
          <?php endif; ?>
          <div>
            <h5 class="fw-bold mb-1"><?= e($santri['nama_lengkap']) ?></h5>
            <div class="d-flex gap-2 flex-wrap">
              <span class="badge <?= $santri['jenis_kelamin']==='L'?'bg-primary':'bg-danger' ?>">
                <?= $santri['jenis_kelamin']==='L'?'Laki-laki':'Perempuan' ?>
              </span>
              <?= status_badge($santri['status']) ?>
            </div>
          </div>
        </div>

        <table class="table table-sm table-striped">
          <tr><th class="text-muted" style="width:38%">NIS Pesantren</th><td><?= e($santri['nis_pesantren'] ?? '-') ?></td></tr>
          <tr><th class="text-muted">NISN</th><td><?= e($santri['nisn'] ?? '-') ?></td></tr>
          <tr><th class="text-muted">Tempat, Tanggal Lahir</th><td><?= e($santri['tempat_lahir'] ?? '-') ?>, <?= format_tanggal($santri['tanggal_lahir'] ?? '') ?></td></tr>
          <tr><th class="text-muted">Agama</th><td><?= e($santri['agama'] ?? 'Islam') ?></td></tr>
          <tr><th class="text-muted">Anak ke-</th><td><?= e($santri['anak_ke'] ?? '-') ?></td></tr>
          <tr><th class="text-muted">Alamat</th><td><?= nl2br(e($santri['alamat'] ?? '-')) ?></td></tr>
          <tr><th class="text-muted">Kel / Kec</th><td><?= e($santri['kelurahan'] ?? '-') ?> / <?= e($santri['kecamatan'] ?? '-') ?></td></tr>
          <tr><th class="text-muted">Kab / Prov</th><td><?= e($santri['kabupaten'] ?? '-') ?> / <?= e($santri['provinsi'] ?? '-') ?></td></tr>
          <tr><th class="text-muted">Kode Pos</th><td><?= e($santri['kode_pos'] ?? '-') ?></td></tr>
        </table>
      </div>
    </div>
  </div>

  <!-- Kolom Kanan: Data Orang Tua / Wali -->
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header bg-white"><i class="bi bi-people-fill me-2 text-success"></i>Data Orang Tua / Wali</div>
      <div class="card-body">
        <h6 class="fw-bold text-primary mb-3">Orang Tua Kandung</h6>
        <table class="table table-sm table-borderless mb-4">
          <tr><td class="text-muted" style="width:38%">Nama Ayah</td><td><strong><?= e($santri['nama_ayah'] ?? '-') ?></strong></td></tr>
          <tr><td class="text-muted">Pekerjaan Ayah</td><td><?= e($santri['pekerjaan_ayah'] ?? '-') ?></td></tr>
          <tr><td class="text-muted">Nama Ibu</td><td><strong><?= e($santri['nama_ibu'] ?? '-') ?></strong></td></tr>
          <tr><td class="text-muted">Pekerjaan Ibu</td><td><?= e($santri['pekerjaan_ibu'] ?? '-') ?></td></tr>
        </table>

        <h6 class="fw-bold text-success mb-3 border-top pt-3">Kontak Wali / Darurat</h6>
        <table class="table table-sm table-borderless">
          <tr><td class="text-muted" style="width:38%">Nama Wali</td><td><?= e($santri['nama_wali'] ?? '-') ?></td></tr>
          <tr><td class="text-muted">Hubungan</td><td><?= e($santri['hubungan_wali'] ?? '-') ?></td></tr>
          <tr><td class="text-muted">No. HP / WhatsApp</td><td>
            <?php if (!empty($santri['no_hp_wali'])): ?>
              <a href="https://wa.me/<?= preg_replace('/[^0-9]/','',$santri['no_hp_wali']) ?>" target="_blank" class="btn btn-sm btn-outline-success">
                <i class="bi bi-whatsapp me-1"></i> <?= e($santri['no_hp_wali']) ?>
              </a>
            <?php else: ?>-<?php endif; ?>
          </td></tr>
          <tr><td class="text-muted">Email Wali</td><td><?= e($santri['email_wali'] ?? '-') ?></td></tr>
        </table>
      </div>
    </div>
  </div>

  <!-- Riwayat Pendaftaran & Riwayat Kelas -->
  <div class="col-12">
    <div class="card">
      <div class="card-header bg-white"><i class="bi bi-journal-bookmark-fill me-2 text-warning"></i>Riwayat Pendaftaran Jenjang</div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>No</th>
                <th>Jenis Jenjang</th>
                <th>Pilihan Jenjang / Kelas</th>
                <th>Status Validasi</th>
                <th>Divalidasi Oleh</th>
                <th>Tanggal Validasi</th>
                <th>Catatan</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($riwayat_daftar)): ?>
                <tr><td colspan="7" class="text-center py-3 text-muted">Belum ada data pendaftaran jenjang.</td></tr>
              <?php else: ?>
                <?php foreach ($riwayat_daftar as $i => $rd): ?>
                  <tr>
                    <td><?= $i + 1 ?></td>
                    <td><span class="badge bg-secondary text-uppercase"><?= e($rd['jenis_jenjang']) ?></span></td>
                    <td>
                      <strong><?= e($rd['jenjang_dipilih']) ?></strong>
                      <?php if (!empty($rd['tingkat_dipilih'])): ?><div class="small text-muted">Kelas: <?= e($rd['tingkat_dipilih']) ?></div><?php endif; ?>
                    </td>
                    <td><?= status_badge($rd['status']) ?></td>
                    <td><?= e($rd['validator_nama'] ?? '-') ?></td>
                    <td><?= !empty($rd['validated_at']) ? format_tanggal($rd['validated_at'], 'd M Y H:i') : '-' ?></td>
                    <td><small class="text-muted"><?= e($rd['catatan_admin'] ?? '-') ?></small></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="card">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span><i class="bi bi-arrow-up-right-circle-fill me-2 text-success"></i>Riwayat Kenaikan Kelas</span>
        <?php if (in_array(current_user()['role'], ['diniyyah', 'tpq'], true)): ?>
          <a href="<?= BASE_URL ?>/kenaikan/tambah?santri_id=<?= $santri['id'] ?>" class="btn btn-sm btn-outline-success"><i class="bi bi-plus-lg me-1"></i>Catat</a>
        <?php endif; ?>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-custom mb-0">
          <thead><tr><th>Program</th><th>Dari</th><th>Menjadi</th><th>Tahun Ajaran</th><th>Status</th><th>Diproses Oleh</th></tr></thead>
          <tbody>
          <?php if (empty($riwayat_kenaikan)): ?>
            <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada catatan kenaikan kelas.</td></tr>
          <?php else: foreach ($riwayat_kenaikan as $item): ?>
            <tr>
              <td><span class="badge bg-secondary text-uppercase"><?= e($item['jenis_jenjang']) ?></span></td>
              <td><strong><?= e($item['jenjang_asal']) ?></strong><?= $item['kelas_asal'] ? '<div class="small text-muted">' . e($item['kelas_asal']) . '</div>' : '' ?></td>
              <td>
                <?php if ($item['status'] === 'lulus'): ?><span class="text-success fw-semibold">Selesai program</span>
                <?php else: ?><strong><?= e($item['jenjang_tujuan']) ?></strong><?= $item['kelas_tujuan'] ? '<div class="small text-muted">' . e($item['kelas_tujuan']) . '</div>' : '' ?><?php endif; ?>
              </td>
              <td><?= e($item['tahun_ajaran']) ?></td>
              <td><?= e(ucfirst($item['status'])) ?></td>
              <td><?= e($item['pemroses_nama'] ?? '-') ?></td>
            </tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-12">
    <div class="card">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span><i class="bi bi-award-fill me-2 text-primary"></i>Riwayat Kelulusan Per Jenjang</span>
        <?php if (in_array(current_user()['role'], ['diniyyah', 'tpq'], true)): ?>
          <a href="<?= BASE_URL ?>/kelulusan/tambah?santri_id=<?= $santri['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-plus-lg me-1"></i>Catat</a>
        <?php endif; ?>
      </div>
      <div class="table-responsive">
        <table class="table table-hover table-custom mb-0">
          <thead><tr><th>Jenjang</th><th>Tahun Ajaran</th><th>Tanggal Lulus</th><th>Status</th><th>Predikat</th><th>Nomor Ijazah</th></tr></thead>
          <tbody>
          <?php if (empty($riwayat_kelulusan)): ?>
            <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada catatan kelulusan.</td></tr>
          <?php else: foreach ($riwayat_kelulusan as $lulus): ?>
            <tr>
              <td><span class="badge bg-secondary"><?= ucfirst(e($lulus['jenis_jenjang'])) ?></span><div class="small mt-1"><?= e($lulus['jenjang']) ?></div></td>
              <td><?= e($lulus['tahun_ajaran']) ?></td>
              <td><?= $lulus['tanggal_lulus'] ? format_tanggal($lulus['tanggal_lulus'], 'd M Y') : '-' ?></td>
              <td><?= status_badge($lulus['status']) ?></td>
              <td><?= e($lulus['predikat'] ?? '-') ?></td>
              <td><?= e($lulus['nomor_ijazah'] ?? '-') ?></td>
            </tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
