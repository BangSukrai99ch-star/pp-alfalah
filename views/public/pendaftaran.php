<?php
/** @var bool $psb_aktif */
/** @var array $psb_jenjang */
/** @var array|null $success */
/** @var array $errors */
/** @var array $tingkatan_tpq */
/** @var array $kelas_diniyyah */
$view_file = 'public/pendaftaran';
?>
<?php $page_title = 'Pendaftaran Santri Baru'; ?>

<!-- Hero Mini -->
<div class="psb-hero">
  <div class="container">
    <div class="psb-hero-copy">
      <span class="eyebrow"><i class="bi bi-stars me-2"></i>Gerbang masa depan</span>
      <h1>Pendaftaran Santri Baru</h1>
      <p class="mb-0">Tahun Ajaran <?= e($pengaturan['psb_tahun_ajaran'] ?? '-') ?></p>
    </div>
    <div class="psb-hero-mark"><i class="bi bi-pen-fill"></i></div>
  </div>
</div>

<section class="psb-section py-5">
  <div class="container">

    <?php if (!$psb_aktif || !in_array(true, $psb_jenjang, true)): ?>
    <!-- PSB Ditutup -->
    <div class="row justify-content-center">
      <div class="col-md-6 text-center py-5">
        <div style="font-size:4rem">🔒</div>
        <h3 class="mt-3 fw-bold">Pendaftaran Saat Ini Ditutup</h3>
        <p class="text-muted">Pendaftaran Santri Baru belum dibuka. Silakan pantau website kami untuk informasi lebih lanjut.</p>
        <?php if (!empty($pengaturan['psb_keterangan']) || !empty($pengaturan['pengumuman'])): ?>
          <div class="alert alert-info text-start mt-3"><?= nl2br(e($pengaturan['psb_keterangan'] ?? $pengaturan['pengumuman'])) ?></div>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>" class="btn btn-primary mt-3"><i class="bi bi-house me-1"></i>Kembali ke Beranda</a>
      </div>
    </div>

    <?php elseif ($success): ?>
    <!-- Sukses -->
    <div class="row justify-content-center">
      <div class="col-md-7 text-center py-5">
        <div style="width:90px;height:90px;background:#d1fae5;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2.5rem;margin:0 auto 20px">✅</div>
        <h3 class="fw-bold text-success">Pendaftaran Berhasil Dikirim!</h3>
        <p class="text-muted mt-2">Terima kasih, <strong><?= e($success['nama']) ?></strong>. Formulir pendaftaran Anda telah kami terima.</p>
        <div class="alert alert-info text-start mt-3">
          <h6 class="fw-bold mb-2">📋 Langkah Selanjutnya:</h6>
          <ol class="mb-0">
            <li>Admin masing-masing jenjang akan memvalidasi pendaftaran Anda.</li>
            <li>Anda akan dihubungi melalui nomor HP wali yang telah didaftarkan.</li>
            <li>Hadir ke pesantren untuk verifikasi berkas jika diperlukan.</li>
          </ol>
        </div>
        <?php if (!is_logged_in()): ?>
        <div class="alert alert-success text-start mt-3">
          <h6 class="fw-bold mb-2"><i class="bi bi-bell-fill me-1"></i>Jangan Lewatkan Informasi Terbaru</h6>
          <p class="mb-2">Agar tidak ketinggalan pengumuman perkembangan pendaftaran, jadwal, dan publikasi terbaru dari pesantren, kami sarankan Anda membuat akun pengguna. Notifikasi publikasi terbaru akan tampil langsung di akun Anda.</p>
          <a href="<?= BASE_URL ?>/register" class="btn btn-success btn-sm"><i class="bi bi-person-plus-fill me-1"></i>Buat Akun Pengguna</a>
        </div>
        <?php endif; ?>
        <div class="d-flex gap-2 justify-content-center mt-4">
          <a href="<?= BASE_URL ?>" class="btn btn-primary">Kembali ke Beranda</a>
          <a href="<?= BASE_URL ?>/daftar" class="btn btn-outline-success">Daftar Lagi</a>
        </div>
      </div>
    </div>

    <?php else: ?>
    <!-- Formulir PSB -->
    <div class="row justify-content-center">
      <div class="col-lg-9">

        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger mb-4">
            <h6 class="fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Mohon perbaiki kesalahan berikut:</h6>
            <ul class="mb-0">
              <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <div class="psb-intro">
          <div>
            <span class="section-kicker">Mulai langkah pertama</span>
            <h2 class="mb-2">Data calon santri</h2>
            <p class="mb-0 text-muted">Lengkapi formulir berikut. Tim kami akan meninjau data dan menghubungi wali melalui nomor WhatsApp yang dicantumkan.</p>
          </div>
          <div class="psb-step"><span>01</span><small>Formulir online</small></div>
        </div>
        <div class="psb-form-card">
          <div class="text-center mb-4">
            <h3 class="fw-bold">📋 Formulir Pendaftaran Santri Baru</h3>
            <p class="text-muted">Isi semua data dengan benar dan lengkap</p>
          </div>

          <form action="<?= BASE_URL ?>/daftar" method="post" id="psbForm" novalidate>
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <!-- ===== BIODATA SANTRI ===== -->
            <div class="form-section-title">👤 Data Calon Santri</div>
            <div class="row g-3">
              <div class="col-md-8">
                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="nama_lengkap"
                       value="<?= e($_POST['nama_lengkap'] ?? '') ?>" required placeholder="Nama sesuai akta kelahiran">
              </div>
              <div class="col-md-4">
                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                <select class="form-select" name="jenis_kelamin" required>
                  <option value="">Pilih</option>
                  <option value="L" <?= ($_POST['jenis_kelamin']??'')==='L'?'selected':'' ?>>Laki-laki</option>
                  <option value="P" <?= ($_POST['jenis_kelamin']??'')==='P'?'selected':'' ?>>Perempuan</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Tempat Lahir</label>
                <input type="text" class="form-control" name="tempat_lahir"
                       value="<?= e($_POST['tempat_lahir'] ?? '') ?>" placeholder="Kota lahir">
              </div>
              <div class="col-md-4">
                <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="tanggal_lahir"
                       value="<?= e($_POST['tanggal_lahir'] ?? '') ?>" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Anak ke-</label>
                <input type="number" class="form-control" name="anak_ke" min="1" max="20"
                       value="<?= e($_POST['anak_ke'] ?? '') ?>" placeholder="Contoh: 1">
              </div>
              <div class="col-12">
                <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                <textarea class="form-control" name="alamat" rows="2" required
                          placeholder="Jl. ... RT/RW ... Desa ... Kecamatan ..."><?= e($_POST['alamat'] ?? '') ?></textarea>
              </div>
            </div>

            <!-- ===== DATA ORANG TUA ===== -->
            <div class="form-section-title">👨‍👩‍👦 Data Orang Tua / Wali</div>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Nama Ayah</label>
                <input type="text" class="form-control" name="nama_ayah" value="<?= e($_POST['nama_ayah'] ?? '') ?>" placeholder="Nama lengkap ayah">
              </div>
              <div class="col-md-6">
                <label class="form-label">Nama Ibu</label>
                <input type="text" class="form-control" name="nama_ibu" value="<?= e($_POST['nama_ibu'] ?? '') ?>" placeholder="Nama lengkap ibu">
              </div>
              <div class="col-md-6">
                <label class="form-label">No. HP / WhatsApp Wali <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                  <input type="tel" class="form-control" name="no_hp_wali"
                         value="<?= e($_POST['no_hp_wali'] ?? '') ?>" required placeholder="08xx-xxxx-xxxx">
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Email Wali (opsional)</label>
                <input type="email" class="form-control" name="email_wali"
                       value="<?= e($_POST['email_wali'] ?? '') ?>" placeholder="email@contoh.com">
              </div>
            </div>

            <!-- ===== PILIHAN JENJANG ===== -->
            <div class="form-section-title">🎓 Program Pendidikan yang Dipilih</div>
            <p class="text-muted small mb-3">Calon santri dapat mendaftar di <strong>lebih dari satu program</strong> sekaligus.</p>

            <div id="jenjangAlert" class="alert alert-danger d-none mb-3">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>Pilih minimal satu program pendidikan.
            </div>

            <!-- Diniyyah -->
            <?php if ($psb_jenjang['diniyyah']): ?>
            <div class="mb-4">
              <div class="jenjang-check-item d-flex align-items-center gap-3" style="max-width:320px">
                <div class="form-check mb-0">
                  <input class="form-check-input" type="checkbox" name="daftar_diniyyah" value="1"
                         id="daftarDiniyyahCheck" <?= !empty($_POST['daftar_diniyyah']) ? 'checked' : '' ?>>
                  <label class="form-check-label fw-semibold" for="daftarDiniyyahCheck">
                    📚 Diniyyah (Pendidikan Keagamaan)
                  </label>
                </div>
              </div>

              <div id="diniyyahClassSection" class="mt-3 p-3 bg-light rounded" style="display:none">
                <label class="form-label fw-semibold">Pilih Jenjang dan Kelas Diniyyah:</label>
                <?php if (empty($kelas_diniyyah)): ?>
                  <div class="alert alert-warning mb-0 small">Pilihan kelas Diniyyah belum tersedia. Silakan hubungi admin Diniyyah.</div>
                <?php else: ?>
                  <div class="jenjang-checkbox-group">
                    <?php foreach ($kelas_diniyyah as $kelas): ?>
                    <div class="jenjang-check-item">
                      <div class="form-check mb-0">
                        <input class="form-check-input" type="radio" name="kelas_diniyyah_id"
                               value="<?= $kelas['id'] ?>"
                               id="din_<?= $kelas['id'] ?>"
                               <?= (string)($_POST['kelas_diniyyah_id'] ?? '') === (string)$kelas['id'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="din_<?= $kelas['id'] ?>">
                          <strong><?= e($kelas['nama_jenjang']) ?></strong> — <?= e($kelas['nama_kelas']) ?>
                        </label>
                      </div>
                    </div>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>
            <?php endif; ?>

            <!-- TPQ -->
            <?php if ($psb_jenjang['tpq']): ?>
            <div class="mb-4">
              <div class="jenjang-check-item d-flex align-items-center gap-3" style="max-width:280px">
                <div class="form-check mb-0">
                  <input class="form-check-input" type="checkbox" name="daftar_tpq" value="1"
                         id="daftarTpqCheck" <?= !empty($_POST['daftar_tpq']) ? 'checked' : '' ?>>
                  <label class="form-check-label fw-semibold" for="daftarTpqCheck">
                    🤲 TPQ (Taman Pendidikan Al-Qur'an)
                  </label>
                </div>
              </div>

              <div id="tpqLevelSection" class="mt-3 p-3 bg-light rounded" style="display:none">
                <label class="form-label fw-semibold">Pilih Tingkatan TPQ:</label>
                <div class="jenjang-checkbox-group">
                  <?php foreach ($tingkatan_tpq as $tt): ?>
                  <div class="jenjang-check-item">
                    <div class="form-check mb-0">
                      <input class="form-check-input" type="radio" name="tingkat_tpq"
                             value="<?= e($tt['nama']) ?>" id="tpq_<?= $tt['id'] ?>"
                             <?= ($_POST['tingkat_tpq'] ?? '') === $tt['nama'] ? 'checked' : '' ?>>
                      <label class="form-check-label" for="tpq_<?= $tt['id'] ?>"><?= e($tt['nama']) ?></label>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
            <?php endif; ?>

            <!-- Submit -->
            <div class="d-grid">
              <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-send-fill me-2"></i>Kirim Formulir Pendaftaran
              </button>
              <p class="text-muted text-center small mt-2">
                <i class="bi bi-shield-check-fill text-success me-1"></i>
                Data Anda aman dan akan diproses oleh admin pesantren.
              </p>
            </div>
          </form>
        </div>

      </div>
    </div>
    <?php endif; ?>

  </div>
</section>
