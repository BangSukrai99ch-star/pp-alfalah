<?php
/** @var array $pengaturan */
/** @var ?string $flash_error */
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pengaturan['nama_pesantren'] ?? 'Pesantren') ?> - Cetak Surat</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
  body { background:#e9ecef; font-family:"Times New Roman", Times, serif; color:#1a1a1a; }
  .surat-toolbar { max-width:820px; margin:20px auto 0; }
  .surat-sheet { background:#fff; max-width:820px; margin:20px auto 60px; padding:45px 55px; box-shadow:0 0 12px rgba(0,0,0,.18); min-height:29.7cm; }
  .surat-kop { display:flex; align-items:center; gap:18px; border-bottom:4px double #222; padding-bottom:14px; margin-bottom:24px; }
  .surat-kop img { height:72px; width:72px; object-fit:contain; }
  .surat-kop h4 { margin:0; letter-spacing:.5px; }
  .surat-kop p { margin:0; font-size:.85rem; }
  .surat-judul h5 { letter-spacing:.5px; }
  @media print {
    body { background:#fff; }
    .no-print { display:none !important; }
    .surat-sheet { box-shadow:none; margin:0; padding:0; min-height:0; max-width:100%; }
  }
</style>
</head>
<body>
  <div class="surat-toolbar no-print d-flex justify-content-between mb-2">
    <a href="javascript:history.back()" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    <button type="button" onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer me-1"></i>Cetak / Simpan PDF</button>
  </div>
  <?php if (!empty($flash_error)): ?>
    <div class="alert alert-danger surat-toolbar no-print"><?= e($flash_error) ?></div>
  <?php endif; ?>
  <div class="surat-sheet">
    <?php require $view_content_file; ?>
  </div>
</body>
</html>
