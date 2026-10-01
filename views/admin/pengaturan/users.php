<?php
/** @var array $users */
/** @var array $current_user */
$view_file = 'admin/pengaturan/users';
?>

<div class="page-header">
  <div>
    <h1 class="page-title"><i class="bi bi-people-fill me-2 text-primary"></i>Kelola Admin</h1>
    <p class="page-subtitle">Manajemen pengguna dan hak akses</p>
  </div>
  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUser">
    <i class="bi bi-person-plus-fill me-1"></i>Tambah Admin
  </button>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-custom mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>Nama Admin</th>
          <th>Username</th>
          <th>Role</th>
          <th>Status</th>
          <th>Last Login</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $i => $u): ?>
        <tr>
          <td class="text-muted"><?= $i+1 ?></td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <?php if (!empty($u['foto'])): ?>
                <img src="<?= UPLOAD_URL . e($u['foto']) ?>" style="width:34px;height:34px;object-fit:cover;border-radius:50%;" alt="">
              <?php else: ?>
                <div style="width:34px;height:34px;background:#e2f0e4;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:#4f9560">
                  <?= strtoupper(substr($u['nama'],0,2)) ?>
                </div>
              <?php endif; ?>
              <span class="fw-semibold"><?= e($u['nama']) ?></span>
            </div>
          </td>
          <td><code><?= e($u['username']) ?></code></td>
          <td>
            <?php $role_colors = ['webmaster'=>'warning','kegiatan'=>'success','diniyyah'=>'warning','tpq'=>'success']; ?>
            <span class="badge bg-<?= $role_colors[$u['role']] ?? 'secondary' ?>"><?= ucfirst($u['role']) ?></span>
          </td>
          <td><?= $u['aktif'] ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Nonaktif</span>' ?></td>
          <td class="text-muted small"><?= $u['last_login'] ? format_tanggal($u['last_login'], 'd M Y H:i') : 'Belum pernah' ?></td>
          <td>
            <div class="d-flex gap-1">
              <button class="btn btn-sm btn-outline-warning btn-edit-user"
                      data-bs-toggle="modal" data-bs-target="#modalUser"
                      data-id="<?= $u['id'] ?>"
                      data-nama="<?= e($u['nama']) ?>"
                      data-username="<?= e($u['username']) ?>"
                      data-role="<?= $u['role'] ?>"
                      data-aktif="<?= $u['aktif'] ?>"
                      title="Edit">
                <i class="bi bi-pencil"></i>
              </button>
              <?php if ($u['id'] != $current_user['id']): ?>
              <form action="<?= BASE_URL ?>/pengaturan/user_hapus/<?= $u['id'] ?>" method="post"
                    data-confirm="Hapus admin <?= e($u['nama']) ?>?">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
              <?php else: ?>
                <span class="btn btn-sm btn-light disabled" title="Akun Anda sendiri"><i class="bi bi-shield-fill text-muted"></i></span>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Role Explanation -->
<div class="row g-3 mt-2">
  <?php $roles = [
    ['webmaster','purple','bi-globe2','Master Admin','Upload konten utama, mengatur website, dan mengelola semua admin'],
    ['kegiatan','success','bi-camera-fill','Admin Publikasi','Upload dan kelola berita, artikel, dan galeri kegiatan pesantren'],
    ['diniyyah','warning','bi-book-half','Admin Diniyyah','Kelola santri dan validasi PSB Awaliyah dan Wustho'],
    ['tpq','success','bi-journals','Admin TPQ','Kelola santri dan validasi PSB untuk TPQ, serta atur tingkatan TPQ'],
  ]; ?>
  <?php foreach ($roles as [$key, $color, $icon, $label, $desc]): ?>
  <div class="col-md-6 col-lg-4">
    <div class="d-flex gap-3 p-3 bg-white border rounded-3 h-100">
      <div style="width:40px;height:40px;border-radius:10px;background:var(--bs-<?= $color ?>-bg-subtle,#f0f0f0);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="bi <?= $icon ?> text-<?= $color ?> fs-5"></i>
      </div>
      <div>
        <div class="fw-bold small"><?= $label ?> <span class="badge bg-<?= $color ?>"><?= $key ?></span></div>
        <div class="text-muted" style="font-size:.78rem"><?= $desc ?></div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Modal Tambah/Edit User -->
<div class="modal fade" id="modalUser" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalUserTitle"><i class="bi bi-person-plus me-2"></i>Tambah Admin Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= BASE_URL ?>/pengaturan/user_simpan" method="post" id="formUser">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="id" id="inputUserId" value="">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="nama" id="inputNama" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Username <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="username" id="inputUsername" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password <span class="text-muted small" id="pwHint">(wajib untuk user baru)</span></label>
            <input type="password" class="form-control" name="password" id="inputPassword"
                   placeholder="Minimal 8 karakter">
            <div class="form-text" id="pwHintEdit" style="display:none;">Biarkan kosong jika tidak ingin mengubah password.</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Role <span class="text-danger">*</span></label>
            <select class="form-select" name="role" id="inputRole" required>
              <option value="">-- Pilih Role --</option>
              <option value="webmaster">Master Admin</option>
              <option value="kegiatan">Admin Publikasi</option>
              <option value="diniyyah">Admin Diniyyah</option>
              <option value="tpq">Admin TPQ</option>
            </select>
          </div>
          <div class="mb-3">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" name="aktif" value="1" id="inputAktif" checked>
              <label class="form-check-label" for="inputAktif">Akun Aktif</label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary" id="btnSimpanUser">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.querySelectorAll('.btn-edit-user').forEach(function(btn) {
  btn.addEventListener('click', function() {
    const id       = this.getAttribute('data-id');
    const nama     = this.getAttribute('data-nama');
    const username = this.getAttribute('data-username');
    const role     = this.getAttribute('data-role');
    const aktif    = this.getAttribute('data-aktif');

    document.getElementById('modalUserTitle').innerHTML = '<i class="bi bi-pencil-square me-2"></i>Edit Admin';
    document.getElementById('inputUserId').value    = id;
    document.getElementById('inputNama').value      = nama;
    document.getElementById('inputUsername').value  = username;
    document.getElementById('inputRole').value      = role;
    document.getElementById('inputAktif').checked   = aktif == '1';
    document.getElementById('inputPassword').required = false;
    document.getElementById('pwHint').style.display  = 'none';
    document.getElementById('pwHintEdit').style.display = 'block';
  });
});

document.getElementById('modalUser').addEventListener('hidden.bs.modal', function() {
  document.getElementById('formUser').reset();
  document.getElementById('inputUserId').value = '';
  document.getElementById('modalUserTitle').innerHTML = '<i class="bi bi-person-plus me-2"></i>Tambah Admin Baru';
  document.getElementById('inputPassword').required = true;
  document.getElementById('pwHint').style.display  = 'inline';
  document.getElementById('pwHintEdit').style.display = 'none';
});
</script>
