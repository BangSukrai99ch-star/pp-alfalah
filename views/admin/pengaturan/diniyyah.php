<?php
/** @var array $jenjang */
/** @var array $kelas */
$view_file = 'admin/pengaturan/diniyyah';
?>

<div class="page-header">
  <div>
    <h1 class="page-title"><i class="bi bi-diagram-3-fill me-2 text-warning"></i>Struktur Diniyyah</h1>
    <p class="page-subtitle">Atur jenjang dan kelas Diniyyah yang tersedia untuk pendaftaran.</p>
  </div>
  <a href="<?= BASE_URL ?>/admin-kelas" class="btn btn-outline-success"><i class="bi bi-person-badge-fill me-1"></i>Kelola Admin Kelas</a>
</div>

<form action="<?= BASE_URL ?>/pengaturan/diniyyah_simpan" method="post" id="formDiniyyah">
  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span><i class="bi bi-layers-fill me-2"></i>Jenjang Diniyyah</span>
      <button type="button" class="btn btn-sm btn-outline-success" id="addJenjang">
        <i class="bi bi-plus-circle me-1"></i>Tambah Jenjang
      </button>
    </div>
    <div class="card-body">
      <p class="small text-muted">Contoh jenjang: Ula, Wustho, Majlis. Nonaktifkan jenjang untuk menyembunyikan seluruh kelasnya dari formulir pendaftaran.</p>
      <div class="row fw-bold text-muted small mb-2 px-2">
        <div class="col-md-5">Nama Jenjang</div>
        <div class="col-md-2">Urutan</div>
        <div class="col-md-2 text-center">Aktif</div>
      </div>
      <div id="jenjangRows">
        <?php foreach ($jenjang as $item): ?>
          <?php $key = (string)$item['id']; ?>
          <div class="row g-2 mb-2 align-items-center jenjang-row">
            <input type="hidden" name="jenjang_id[]" value="<?= e($key) ?>">
            <div class="col-md-5">
              <input type="text" class="form-control jenjang-name" name="jenjang_nama[<?= e($key) ?>]" value="<?= e($item['nama']) ?>" required>
            </div>
            <div class="col-md-2">
              <input type="number" min="0" class="form-control" name="jenjang_urutan[<?= e($key) ?>]" value="<?= e($item['urutan']) ?>">
            </div>
            <div class="col-md-2 text-center">
              <input type="hidden" name="jenjang_aktif[<?= e($key) ?>]" value="0">
              <div class="form-check form-switch d-flex justify-content-center">
                <input class="form-check-input" type="checkbox" name="jenjang_aktif[<?= e($key) ?>]" value="1" <?= $item['aktif'] ? 'checked' : '' ?>>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span><i class="bi bi-journal-bookmark-fill me-2"></i>Kelas Diniyyah</span>
      <button type="button" class="btn btn-sm btn-outline-success" id="addKelas">
        <i class="bi bi-plus-circle me-1"></i>Tambah Kelas
      </button>
    </div>
    <div class="card-body">
      <p class="small text-muted">Setiap kelas berada di bawah satu jenjang. Kelas nonaktif tidak dapat dipilih untuk pendaftaran baru, tetapi riwayat pendaftaran tetap tersimpan.</p>
      <div class="row fw-bold text-muted small mb-2 px-2">
        <div class="col-md-4">Jenjang</div>
        <div class="col-md-4">Nama Kelas</div>
        <div class="col-md-2">Urutan</div>
        <div class="col-md-2 text-center">Aktif</div>
      </div>
      <div id="kelasRows">
        <?php foreach ($kelas as $item): ?>
          <?php $key = (string)$item['id']; ?>
          <div class="row g-2 mb-2 align-items-center kelas-row">
            <input type="hidden" name="kelas_id[]" value="<?= e($key) ?>">
            <div class="col-md-4">
              <select class="form-select kelas-jenjang" name="kelas_jenjang[<?= e($key) ?>]" required>
                <?php foreach ($jenjang as $level): ?>
                  <option value="<?= $level['id'] ?>" <?= (int)$item['jenjang_id'] === (int)$level['id'] ? 'selected' : '' ?>><?= e($level['nama']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <input type="text" class="form-control" name="kelas_nama[<?= e($key) ?>]" value="<?= e($item['nama']) ?>" required>
            </div>
            <div class="col-md-2">
              <input type="number" min="0" class="form-control" name="kelas_urutan[<?= e($key) ?>]" value="<?= e($item['urutan']) ?>">
            </div>
            <div class="col-md-2 text-center">
              <input type="hidden" name="kelas_aktif[<?= e($key) ?>]" value="0">
              <div class="form-check form-switch d-flex justify-content-center">
                <input class="form-check-input" type="checkbox" name="kelas_aktif[<?= e($key) ?>]" value="1" <?= $item['aktif'] ? 'checked' : '' ?>>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="d-flex justify-content-end mt-3">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-save me-1"></i>Simpan Struktur Diniyyah
        </button>
      </div>
    </div>
  </div>
</form>

<div class="alert alert-info mt-4 mb-0">
  <i class="bi bi-info-circle-fill me-2"></i>Gunakan status <strong>aktif</strong> untuk mengatur pilihan pendaftaran. Setelah menambah kelas, buatkan Admin Kelas melalui menu <strong>Kelola Admin Kelas</strong>.
</div>

<script>
(() => {
  const jenjangRows = document.getElementById('jenjangRows');
  const kelasRows = document.getElementById('kelasRows');
  let nextJenjang = 1;
  let nextKelas = 1;

  const optionMarkup = (value, label, selected = false) =>
    `<option value="${value}"${selected ? ' selected' : ''}>${label}</option>`;

  const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (char) => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
  }[char]));

  function currentOptions(selected = '') {
    return [...document.querySelectorAll('.jenjang-row')].map((row) => {
      const key = row.querySelector('input[name="jenjang_id[]"]').value;
      const name = row.querySelector('.jenjang-name').value.trim() || 'Jenjang baru';
      return optionMarkup(escapeHtml(key), escapeHtml(name), key === selected);
    }).join('');
  }

  document.getElementById('addJenjang').addEventListener('click', () => {
    const key = `new-${nextJenjang++}`;
    jenjangRows.insertAdjacentHTML('beforeend', `
      <div class="row g-2 mb-2 align-items-center jenjang-row">
        <input type="hidden" name="jenjang_id[]" value="${key}">
        <div class="col-md-5"><input type="text" class="form-control jenjang-name" name="jenjang_nama[${key}]" placeholder="Contoh: Majlis" required></div>
        <div class="col-md-2"><input type="number" min="0" class="form-control" name="jenjang_urutan[${key}]" value="0"></div>
        <div class="col-md-2 text-center">
          <input type="hidden" name="jenjang_aktif[${key}]" value="0">
          <div class="form-check form-switch d-flex justify-content-center"><input class="form-check-input" type="checkbox" name="jenjang_aktif[${key}]" value="1" checked></div>
        </div>
      </div>`);
  });

  document.getElementById('addKelas').addEventListener('click', () => {
    const key = `new-${nextKelas++}`;
    kelasRows.insertAdjacentHTML('beforeend', `
      <div class="row g-2 mb-2 align-items-center kelas-row">
        <input type="hidden" name="kelas_id[]" value="${key}">
        <div class="col-md-4"><select class="form-select kelas-jenjang" name="kelas_jenjang[${key}]" required>${currentOptions()}</select></div>
        <div class="col-md-4"><input type="text" class="form-control" name="kelas_nama[${key}]" placeholder="Contoh: Kelas 1" required></div>
        <div class="col-md-2"><input type="number" min="0" class="form-control" name="kelas_urutan[${key}]" value="0"></div>
        <div class="col-md-2 text-center">
          <input type="hidden" name="kelas_aktif[${key}]" value="0">
          <div class="form-check form-switch d-flex justify-content-center"><input class="form-check-input" type="checkbox" name="kelas_aktif[${key}]" value="1" checked></div>
        </div>
      </div>`);
  });
})();
</script>
