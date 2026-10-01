<?php
/** @var array $items */
/** @var array $pg */
/** @var string $tipe */
/** @var string $status */
/** @var string $search */
$view_file = 'admin/kegiatan/index';
?>

<div class="page-header">
  <div>
    <h1 class="page-title"><i class="bi bi-journal-richtext me-2 text-success"></i>Manajemen Konten</h1>
    <p class="page-subtitle">Kelola berita, artikel, dan galeri kegiatan pesantren</p>
  </div>
  <a href="<?= BASE_URL ?>/kegiatan/tambah" class="btn btn-success">
    <i class="bi bi-plus-lg me-1"></i>Tambah Konten
  </a>
</div>

<!-- Filter Tabs -->
<div class="card mb-4">
  <div class="card-body">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label">Cari Judul</label>
        <input type="text" class="form-control" name="q" value="<?= e($search) ?>" placeholder="Cari judul konten..." id="searchInput">
      </div>
      <div class="col-md-3">
        <label class="form-label">Tipe Konten</label>
        <select class="form-select" name="tipe" onchange="this.form.submit()">
          <option value="">Semua Tipe</option>
          <option value="berita"  <?= $tipe==='berita'  ? 'selected' : '' ?>>📰 Berita</option>
          <option value="artikel" <?= $tipe==='artikel' ? 'selected' : '' ?>>📄 Artikel</option>
          <option value="galeri"  <?= $tipe==='galeri'  ? 'selected' : '' ?>>📷 Galeri</option>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Status</label>
        <select class="form-select" name="status" onchange="this.form.submit()">
          <option value="">Semua</option>
          <option value="publish" <?= $status==='publish' ? 'selected' : '' ?>>Publish</option>
          <option value="draft"   <?= $status==='draft'   ? 'selected' : '' ?>>Draft</option>
        </select>
      </div>
      <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Cari</button></div>
      <div class="col-md-1"><a href="<?= BASE_URL ?>/kegiatan" class="btn btn-outline-secondary w-100">Reset</a></div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <span>Daftar Konten <span class="badge bg-success ms-1"><?= $pg['total'] ?></span></span>
  </div>
  <div class="table-responsive">
    <table class="table table-custom mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>Konten</th>
          <th>Tipe</th>
          <th>Status</th>
          <th>Views</th>
          <th>Oleh</th>
          <th>Tanggal</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($items)): ?>
        <tr><td colspan="8" class="text-center py-5 text-muted">
          <i class="bi bi-inbox fs-2 d-block mb-2"></i>Belum ada konten
        </td></tr>
        <?php else: ?>
        <?php foreach ($items as $i => $k): ?>
        <tr>
          <td class="text-muted"><?= ($pg['offset']+$i+1) ?></td>
          <td>
            <div class="d-flex gap-2 align-items-center">
              <?php if (!empty($k['gambar'])): ?>
                <?php if (is_video_file($k['gambar'])): ?>
                  <video src="<?= UPLOAD_URL . e($k['gambar']) ?>" style="width:45px;height:45px;object-fit:cover;border-radius:8px;" muted preload="metadata"></video>
                <?php else: ?>
                  <img src="<?= UPLOAD_URL . e($k['gambar']) ?>" style="width:45px;height:45px;object-fit:cover;border-radius:8px;" alt="">
                <?php endif; ?>
              <?php else: ?>
                <div style="width:45px;height:45px;background:#edf4ed;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#78917d;font-size:1.2rem"><i class="bi bi-image"></i></div>
              <?php endif; ?>
              <div>
                <div class="fw-semibold"><?= e(truncate($k['judul'], 60)) ?></div>
                <?php if ($k['tags']): ?>
                  <div class="text-muted small"><?= e($k['tags']) ?></div>
                <?php endif; ?>
              </div>
            </div>
          </td>
          <td>
            <?php $tipe_icons = ['berita'=>'📰','artikel'=>'📄','galeri'=>'📷']; ?>
            <span class="badge bg-light text-dark border"><?= ($tipe_icons[$k['tipe']] ?? '') . ' ' . ucfirst(e($k['tipe'])) ?></span>
          </td>
          <td>
            <form action="<?= BASE_URL ?>/kegiatan/toggle_status/<?= $k['id'] ?>" method="post" class="d-inline">
              <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
              <button type="submit" class="badge border-0 <?= $k['status']==='publish' ? 'bg-success' : 'bg-secondary' ?>"
                      title="Klik untuk ubah status" style="cursor:pointer">
                <?= $k['status']==='publish' ? '● Publish' : '○ Draft' ?>
              </button>
            </form>
          </td>
          <td class="text-muted small"><i class="bi bi-eye me-1"></i><?= number_format($k['views']) ?></td>
          <td class="text-muted small"><?= e($k['penulis'] ?? '-') ?></td>
          <td class="text-muted small"><?= format_tanggal($k['created_at'],'d M Y') ?></td>
          <td>
            <div class="d-flex gap-1">
              <a href="<?= BASE_URL ?>/berita/<?= e($k['slug']) ?>" target="_blank"
                 class="btn btn-sm btn-outline-info" title="Lihat">
                <i class="bi bi-eye"></i>
              </a>
              <a href="<?= BASE_URL ?>/kegiatan/edit/<?= $k['id'] ?>"
                 class="btn btn-sm btn-outline-warning" title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
              <form action="<?= BASE_URL ?>/kegiatan/delete/<?= $k['id'] ?>" method="post"
                    data-confirm="Hapus konten '<?= e($k['judul']) ?>'?">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($pg['total_pages'] > 1): ?>
  <div class="card-footer">
    <nav>
      <ul class="pagination pagination-sm mb-0 justify-content-center">
        <?php if ($pg['has_prev']): ?><li class="page-item"><a class="page-link" href="?page=<?= $pg['page']-1 ?>&tipe=<?= $tipe ?>&status=<?= $status ?>&q=<?= urlencode($search) ?>">‹</a></li><?php endif; ?>
        <?php for ($p=max(1,$pg['page']-2); $p<=min($pg['total_pages'],$pg['page']+2); $p++): ?>
          <li class="page-item <?= $p==$pg['page']?'active':'' ?>"><a class="page-link" href="?page=<?= $p ?>&tipe=<?= $tipe ?>&status=<?= $status ?>&q=<?= urlencode($search) ?>"><?= $p ?></a></li>
        <?php endfor; ?>
        <?php if ($pg['has_next']): ?><li class="page-item"><a class="page-link" href="?page=<?= $pg['page']+1 ?>&tipe=<?= $tipe ?>&status=<?= $status ?>&q=<?= urlencode($search) ?>">›</a></li><?php endif; ?>
      </ul>
    </nav>
  </div>
  <?php endif; ?>
</div>
