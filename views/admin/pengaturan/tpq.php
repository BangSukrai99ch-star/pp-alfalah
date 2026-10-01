<?php
/** @var array $tingkatan */
$view_file = 'admin/pengaturan/tpq';
?>

<div class="page-header">
  <div>
    <h1 class="page-title"><i class="bi bi-book-fill me-2 text-danger"></i>Pengaturan Tingkatan TPQ</h1>
    <p class="page-subtitle">Atur nama tingkatan TPQ secara dinamis</p>
  </div>
  <a href="<?= BASE_URL ?>/admin-kelas" class="btn btn-outline-success"><i class="bi bi-person-badge-fill me-1"></i>Kelola Admin Kelas</a>
</div>

<div class="card">
  <div class="card-header">
    <i class="bi bi-sliders me-2"></i>Daftar Tingkatan TPQ
  </div>
  <div class="card-body">
    <form action="<?= BASE_URL ?>/pengaturan/tpq_simpan" method="post" id="formTPQ">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

      <div class="row fw-bold text-muted small mb-2 px-2">
        <div class="col-md-4">Nama Tingkatan</div>
        <div class="col-md-2">Urutan</div>
        <div class="col-md-4">Keterangan</div>
        <div class="col-md-1 text-center">Aktif</div>
        <div class="col-md-1">Hapus</div>
      </div>

      <div id="tpqRowsContainer">
        <?php foreach ($tingkatan as $t): ?>
        <div class="tpq-row row g-2 mb-2 align-items-center">
          <input type="hidden" name="id[]" value="<?= $t['id'] ?>">
          <div class="col-md-4">
            <input type="text" name="nama[]" class="form-control"
                   value="<?= e($t['nama']) ?>" placeholder="Nama Tingkatan" required>
          </div>
          <div class="col-md-2">
            <input type="number" name="urutan[]" class="form-control"
                   value="<?= e($t['urutan']) ?>" placeholder="Urutan">
          </div>
          <div class="col-md-4">
            <input type="text" name="keterangan[]" class="form-control"
                   value="<?= e($t['keterangan'] ?? '') ?>" placeholder="Keterangan...">
          </div>
          <div class="col-md-1 text-center">
            <div class="form-check form-switch d-flex justify-content-center">
              <input class="form-check-input" type="checkbox" name="aktif[]" value="1"
                     <?= $t['aktif'] ? 'checked' : '' ?>>
            </div>
          </div>
          <div class="col-md-1 d-flex gap-1">
            <button type="button" class="btn btn-sm btn-outline-danger remove-tpq-row" title="Hapus baris">
              <i class="bi bi-dash-circle"></i>
            </button>
            <!-- Hard delete via separate form -->
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="d-flex gap-2 mt-3">
        <button type="button" class="btn btn-outline-success btn-sm" id="addTpqRow">
          <i class="bi bi-plus-circle me-1"></i>Tambah Tingkatan
        </button>
        <button type="submit" class="btn btn-primary btn-sm">
          <i class="bi bi-save me-1"></i>Simpan Semua Perubahan
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Info Card -->
<div class="card mt-4 border-0" style="background:#f2f7e5;">
  <div class="card-body">
    <h6 class="mb-2"><i class="bi bi-lightbulb-fill text-warning me-2"></i>Petunjuk Penggunaan</h6>
    <ul class="small text-muted mb-0">
      <li>Anda dapat <strong>mengubah nama tingkatan</strong> sesuai kurikulum yang digunakan (misal: Jilid 1, Iqra 1, Tilawah, dll).</li>
      <li>Kolom <strong>Urutan</strong> menentukan posisi tampilan dalam formulir pendaftaran.</li>
      <li>Hilangkan centang <strong>Aktif</strong> untuk menyembunyikan tingkatan dari formulir pendaftaran tanpa menghapusnya.</li>
      <li>Klik <strong>Tambah Tingkatan</strong> untuk menambah baris baru.</li>
      <li>Setelah membuat tingkatan baru, buatkan <strong>Admin Kelas</strong> melalui tombol Kelola Admin Kelas.</li>
      <li>Perubahan akan otomatis tersinkronisasi dengan formulir PSB Online.</li>
    </ul>
  </div>
</div>

<!-- Tingkatan saat ini yang digunakan di pendaftaran -->
<?php
global $pdo;
$used = $pdo->query("SELECT jenjang_dipilih, COUNT(*) as jumlah FROM pendaftaran_jenjang WHERE jenis_jenjang='tpq' GROUP BY jenjang_dipilih")->fetchAll();
?>
<?php if (!empty($used)): ?>
<div class="card mt-4">
  <div class="card-header"><i class="bi bi-bar-chart-fill me-2 text-info"></i>Statistik Pendaftaran TPQ per Tingkatan</div>
  <div class="card-body">
    <div class="row g-3">
      <?php foreach ($used as $u): ?>
      <div class="col-6 col-md-3">
        <div class="text-center p-3 border rounded">
          <div class="fw-bold fs-4"><?= $u['jumlah'] ?></div>
          <div class="text-muted small"><?= e($u['jenjang_dipilih']) ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>
