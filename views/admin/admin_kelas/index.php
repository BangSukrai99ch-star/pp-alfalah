<?php
/** @var string $jenis */
/** @var array $kelas */
$view_file = 'admin/admin_kelas/index';
?>
<div class="page-header">
  <div>
    <h1 class="page-title"><i class="bi bi-person-badge-fill me-2 text-success"></i>Admin Kelas</h1>
    <p class="page-subtitle">Tetapkan satu wali kelas untuk setiap <?= $jenis === 'diniyyah' ? 'kelas Diniyyah' : 'tingkatan TPQ/PSQ' ?>.</p>
  </div>
</div>

<div class="alert alert-info"><i class="bi bi-info-circle-fill me-2"></i>Kelas baru akan muncul di daftar ini sebagai <strong>Belum ada Admin Kelas</strong>. Buatkan akun sebelum wali kelas mengelola santrinya.</div>
<div class="card">
  <div class="table-responsive">
    <table class="table table-custom mb-0">
      <thead><tr><th>Kelas</th><th>Admin Kelas</th><th>Email</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($kelas as $item): ?>
        <tr>
          <td><strong><?= e($item['jenjang']) ?></strong><?= $item['kelas'] ? '<div class="small text-muted">' . e($item['kelas']) . '</div>' : '' ?></td>
          <?php if ($item['assignment_id']): ?>
            <td><?= e($item['admin_nama']) ?></td>
            <td><?= e($item['admin_email']) ?></td>
            <td><form method="post" action="<?= BASE_URL ?>/admin-kelas/hapus/<?= $item['assignment_id'] ?>" data-confirm="Hapus akun Admin Kelas ini?"><input type="hidden" name="csrf_token" value="<?= csrf_token() ?>"><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td>
          <?php else: ?>
            <td colspan="3">
              <form method="post" action="<?= BASE_URL ?>/admin-kelas/simpan" class="row g-2 align-items-end">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="kelas_id" value="<?= $item['id'] ?>">
                <div class="col-md-3"><input class="form-control form-control-sm" name="nama" placeholder="Nama wali kelas" required></div>
                <div class="col-md-3"><input class="form-control form-control-sm" type="email" name="email" placeholder="Email login" required></div>
                <div class="col-md-3"><input class="form-control form-control-sm" type="password" name="password" placeholder="Password minimal 8" minlength="8" required></div>
                <div class="col-md-3"><button class="btn btn-sm btn-success w-100"><i class="bi bi-person-plus-fill me-1"></i>Buat Admin Kelas</button></div>
              </form>
            </td>
          <?php endif; ?>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
