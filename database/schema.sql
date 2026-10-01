-- ============================================================
-- SISTEM INFORMASI PESANTREN
-- Database Schema v1.0
-- ============================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
CREATE DATABASE IF NOT EXISTS `db_pp_alfalah` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_pp_alfalah`;
-- ============================================================
-- 1. TABEL USERS (Admin dengan RBAC)
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(191) DEFAULT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('webmaster', 'kegiatan', 'diniyyah', 'tpq', 'admin_kelas', 'user') NOT NULL,
  `foto` VARCHAR(255) DEFAULT NULL,
  `aktif` TINYINT(1) DEFAULT 1,
  `last_login` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- ============================================================
-- 2. TABEL PENGATURAN WEB (Website Config)
-- ============================================================
CREATE TABLE IF NOT EXISTS `pengaturan_web` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nama_pesantren` VARCHAR(150) NOT NULL DEFAULT 'Pesantren',
  `tagline` VARCHAR(255) DEFAULT NULL,
  `alamat` TEXT DEFAULT NULL,
  `telepon` VARCHAR(20) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `logo` VARCHAR(255) DEFAULT NULL,
  `favicon` VARCHAR(255) DEFAULT NULL,
  `hero_bg` VARCHAR(255) DEFAULT NULL,
  `visi` TEXT DEFAULT NULL,
  `misi` TEXT DEFAULT NULL,
  `sejarah` TEXT DEFAULT NULL,
  `pengumuman` TEXT DEFAULT NULL,
  `beranda_sambutan` TEXT DEFAULT NULL,
  `tentang_konten` TEXT DEFAULT NULL,
  `publikasi_konten` TEXT DEFAULT NULL,
  `psb_aktif` TINYINT(1) DEFAULT 0 COMMENT '1=Buka, 0=Tutup',
  `psb_diniyyah_aktif` TINYINT(1) DEFAULT 1 COMMENT 'PSB jenjang diniyyah',
  `psb_tpq_aktif` TINYINT(1) DEFAULT 1 COMMENT 'PSB jenjang TPQ',
  `psb_tahun_ajaran` VARCHAR(20) DEFAULT NULL,
  `psb_keterangan` TEXT DEFAULT NULL,
  `footer_text` VARCHAR(255) DEFAULT NULL,
  `facebook_url` VARCHAR(255) DEFAULT NULL,
  `instagram_url` VARCHAR(255) DEFAULT NULL,
  `youtube_url` VARCHAR(255) DEFAULT NULL,
  `maps_embed` TEXT DEFAULT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- ============================================================
-- 3. TABEL SANTRI (Data Induk Terpusat)
-- ============================================================
CREATE TABLE IF NOT EXISTS `santri` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nisn` VARCHAR(20) DEFAULT NULL UNIQUE COMMENT 'Nomor Induk Siswa Nasional',
  `nis_pesantren` VARCHAR(20) DEFAULT NULL UNIQUE COMMENT 'Nomor Induk Pesantren',
  `nama_lengkap` VARCHAR(150) NOT NULL,
  `jenis_kelamin` ENUM('L', 'P') NOT NULL,
  `tempat_lahir` VARCHAR(100) DEFAULT NULL,
  `tanggal_lahir` DATE DEFAULT NULL,
  `agama` VARCHAR(30) DEFAULT 'Islam',
  `anak_ke` TINYINT DEFAULT NULL,
  `alamat` TEXT DEFAULT NULL,
  `kelurahan` VARCHAR(100) DEFAULT NULL,
  `kecamatan` VARCHAR(100) DEFAULT NULL,
  `kabupaten` VARCHAR(100) DEFAULT NULL,
  `provinsi` VARCHAR(100) DEFAULT NULL,
  `kode_pos` VARCHAR(10) DEFAULT NULL,
  -- Wali
  `nama_ayah` VARCHAR(100) DEFAULT NULL,
  `pekerjaan_ayah` VARCHAR(100) DEFAULT NULL,
  `nama_ibu` VARCHAR(100) DEFAULT NULL,
  `pekerjaan_ibu` VARCHAR(100) DEFAULT NULL,
  `nama_wali` VARCHAR(100) DEFAULT NULL,
  `hubungan_wali` VARCHAR(50) DEFAULT NULL,
  `no_hp_wali` VARCHAR(20) DEFAULT NULL,
  `email_wali` VARCHAR(100) DEFAULT NULL,
  -- Berkas
  `foto` VARCHAR(255) DEFAULT NULL,
  `dokumen_kk` VARCHAR(255) DEFAULT NULL,
  `dokumen_akta` VARCHAR(255) DEFAULT NULL,
  -- Status
  `status` ENUM('aktif', 'alumni', 'keluar') DEFAULT 'aktif',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- ============================================================
-- 4. TABEL TINGKATAN TPQ (Dinamis)
-- ============================================================
CREATE TABLE IF NOT EXISTS `tingkatan_tpq` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `urutan` TINYINT DEFAULT 0,
  `keterangan` TEXT DEFAULT NULL,
  `aktif` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- ============================================================
-- 5. TABEL STRUKTUR DINIYYAH (Dinamis)
-- ============================================================
CREATE TABLE IF NOT EXISTS `diniyyah_jenjang` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `urutan` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `aktif` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_diniyyah_jenjang_nama` (`nama`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS `diniyyah_kelas` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `jenjang_id` INT UNSIGNED NOT NULL,
  `nama` VARCHAR(100) NOT NULL,
  `urutan` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `aktif` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_diniyyah_kelas_jenjang_nama` (`jenjang_id`, `nama`),
  KEY `idx_diniyyah_kelas_aktif` (`jenjang_id`, `aktif`, `urutan`),
  CONSTRAINT `fk_diniyyah_kelas_jenjang` FOREIGN KEY (`jenjang_id`)
    REFERENCES `diniyyah_jenjang` (`id`) ON DELETE RESTRICT
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
CREATE TABLE IF NOT EXISTS `admin_kelas` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `jenis_jenjang` ENUM('diniyyah','tpq') NOT NULL,
  `diniyyah_kelas_id` INT UNSIGNED DEFAULT NULL,
  `tingkatan_tpq_id` INT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_admin_kelas_user` (`user_id`),
  UNIQUE KEY `uq_admin_kelas_diniyyah` (`diniyyah_kelas_id`),
  UNIQUE KEY `uq_admin_kelas_tpq` (`tingkatan_tpq_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`diniyyah_kelas_id`) REFERENCES `diniyyah_kelas`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`tingkatan_tpq_id`) REFERENCES `tingkatan_tpq`(`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- ============================================================
-- 6. TABEL KELAS (Riwayat kelas umum/legacy)
-- ============================================================
CREATE TABLE IF NOT EXISTS `kelas` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nama_kelas` VARCHAR(100) NOT NULL,
  `jenjang` ENUM(
    'TK',
    'SD',
    'SMP',
    'SMA',
    'S1',
    'Diniyyah Ula',
    'Diniyyah Wustho',
    'Diniyyah Ulya',
    'TPQ'
  ) NOT NULL,
  `tingkat` VARCHAR(20) DEFAULT NULL COMMENT 'Kelas 1, 2, 3, dst',
  `wali_kelas` VARCHAR(100) DEFAULT NULL,
  `kapasitas` SMALLINT DEFAULT 30,
  `aktif` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- ============================================================
-- 7. TABEL PENDAFTARAN JENJANG (PSB Multi-Jenjang / Junction)
-- ============================================================
CREATE TABLE IF NOT EXISTS `pendaftaran_jenjang` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `santri_id` INT UNSIGNED NOT NULL,
  `jenis_jenjang` ENUM('diniyyah', 'tpq') NOT NULL,
  `jenjang_dipilih` VARCHAR(100) NOT NULL COMMENT 'Contoh: SMA, Diniyyah Wustho, Jilid 1',
  `tingkat_dipilih` VARCHAR(50) DEFAULT NULL COMMENT 'Kelas 1, 2, dst',
  `tahun_ajaran` VARCHAR(20) DEFAULT NULL,
  `status` ENUM('pending', 'diterima', 'ditolak') DEFAULT 'pending',
  `catatan_admin` TEXT DEFAULT NULL,
  `divalidasi_oleh` INT UNSIGNED DEFAULT NULL,
  `validated_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`santri_id`) REFERENCES `santri`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`divalidasi_oleh`) REFERENCES `users`(`id`) ON DELETE
  SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- ============================================================
-- 8. TABEL SANTRI KELAS (Riwayat Kelas Santri)
-- ============================================================
CREATE TABLE IF NOT EXISTS `santri_kelas` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `santri_id` INT UNSIGNED NOT NULL,
  `kelas_id` INT UNSIGNED NOT NULL,
  `tahun_ajaran` VARCHAR(20) DEFAULT NULL,
  `semester` TINYINT DEFAULT 1,
  `status` ENUM('aktif', 'naik', 'tinggal', 'lulus') DEFAULT 'aktif',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`santri_id`) REFERENCES `santri`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`kelas_id`) REFERENCES `kelas`(`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- ============================================================
-- 9. TABEL KELULUSAN PER JENJANG
-- ============================================================
CREATE TABLE IF NOT EXISTS `kelulusan` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `santri_id` INT UNSIGNED NOT NULL,
  `jenis_jenjang` ENUM('diniyyah', 'tpq') NOT NULL,
  `jenjang` VARCHAR(100) NOT NULL,
  `tahun_ajaran` VARCHAR(20) NOT NULL,
  `tanggal_lulus` DATE DEFAULT NULL,
  `nomor_ijazah` VARCHAR(100) DEFAULT NULL,
  `predikat` VARCHAR(100) DEFAULT NULL,
  `status` ENUM('lulus', 'tidak_lulus', 'ditunda') DEFAULT 'lulus',
  `catatan` TEXT DEFAULT NULL,
  `ditetapkan_oleh` INT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`santri_id`) REFERENCES `santri`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`ditetapkan_oleh`) REFERENCES `users`(`id`) ON DELETE
  SET NULL,
    UNIQUE KEY `uq_kelulusan_santri_jenjang_tahun` (
      `santri_id`,
      `jenis_jenjang`,
      `jenjang`,
      `tahun_ajaran`
    )
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- ============================================================
-- 10. TABEL KENAIKAN KELAS PER PROGRAM
-- ============================================================
CREATE TABLE IF NOT EXISTS `kenaikan_kelas` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `santri_id` INT UNSIGNED NOT NULL,
  `jenis_jenjang` ENUM('diniyyah','tpq') NOT NULL,
  `jenjang_asal` VARCHAR(100) NOT NULL,
  `kelas_asal` VARCHAR(100) DEFAULT NULL,
  `jenjang_tujuan` VARCHAR(100) DEFAULT NULL,
  `kelas_tujuan` VARCHAR(100) DEFAULT NULL,
  `tahun_ajaran` VARCHAR(20) NOT NULL,
  `status` ENUM('naik','tetap','lulus') NOT NULL DEFAULT 'naik',
  `catatan` TEXT DEFAULT NULL,
  `diproses_oleh` INT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`santri_id`) REFERENCES `santri`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`diproses_oleh`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  UNIQUE KEY `uq_kenaikan_santri_program_tahun` (`santri_id`,`jenis_jenjang`,`tahun_ajaran`),
  KEY `idx_kenaikan_program_status` (`jenis_jenjang`,`status`,`tahun_ajaran`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- ============================================================
-- 11. TABEL KEGIATAN (Berita, Artikel, Galeri)
-- ============================================================
CREATE TABLE IF NOT EXISTS `kegiatan` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `judul` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `tipe` ENUM('berita', 'artikel', 'galeri') NOT NULL DEFAULT 'berita',
  `konten` LONGTEXT DEFAULT NULL,
  `gambar` VARCHAR(255) DEFAULT NULL,
  `galeri_foto` JSON DEFAULT NULL COMMENT 'Array path foto untuk galeri',
  `tags` VARCHAR(255) DEFAULT NULL,
  `status` ENUM('draft', 'publish') DEFAULT 'draft',
  `views` INT DEFAULT 0,
  `dibuat_oleh` INT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`dibuat_oleh`) REFERENCES `users`(`id`) ON DELETE
  SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- ============================================================
-- 11. TABEL PENGUMUMAN SLIDER (Hero Slider Website)
-- ============================================================
CREATE TABLE IF NOT EXISTS `slider` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `judul` VARCHAR(150) DEFAULT NULL,
  `deskripsi` TEXT DEFAULT NULL,
  `gambar` VARCHAR(255) NOT NULL,
  `link` VARCHAR(255) DEFAULT NULL,
  `urutan` TINYINT DEFAULT 0,
  `aktif` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
-- ============================================================
-- DEFAULT SEED DATA
-- ============================================================
-- Webmaster default (password: Admin@12345)
INSERT INTO `users` (`nama`, `username`, `password`, `role`)
VALUES (
    'Web Master',
    'webmaster',
    '$2y$12$y3/COZRakuuJ5XVC.WxN/et8Nil6K3r5B4kg2sacuMCdqDxQvAJmu',
    'webmaster'
  ),
  (
    'Admin Kegiatan',
    'kegiatan',
    '$2y$12$y3/COZRakuuJ5XVC.WxN/et8Nil6K3r5B4kg2sacuMCdqDxQvAJmu',
    'kegiatan'
  ),
  (
    'Admin Diniyyah',
    'diniyyah',
    '$2y$12$y3/COZRakuuJ5XVC.WxN/et8Nil6K3r5B4kg2sacuMCdqDxQvAJmu',
    'diniyyah'
  ),
  (
    'Admin TPQ',
    'tpq',
    '$2y$12$y3/COZRakuuJ5XVC.WxN/et8Nil6K3r5B4kg2sacuMCdqDxQvAJmu',
    'tpq'
  );
-- Pengaturan web default
INSERT INTO `pengaturan_web` (
    `nama_pesantren`,
    `tagline`,
    `alamat`,
    `telepon`,
    `email`,
    `logo`,
    `visi`,
    `misi`,
    `psb_aktif`,
    `psb_tahun_ajaran`,
    `footer_text`
  )
VALUES (
    'Pondok Pesantren Al-Falah',
    'Mencetak Generasi Qur\'ani yang Berakhlak Mulia, Berilmu, dan Berwawasan Luas',
    'Jl. Raya Pesantren No. 45, Kompleks Ponpes Al-Falah',
    '(021) 78901234',
    'info@ponpes-alfalah.sch.id',
    'settings/logo_alfalah.png',
    'Mencetak generasi muslim yang bertaqwa, berakhlak mulia, menguasai ilmu agama dan umum, serta berkhidmat untuk umat dan bangsa.',
    '1. Menyelenggarakan pendidikan pesantren terpadu berkualitas\n2. Membina akhlaqul karimah santri berlandaskan Al-Qur\'an dan Sunnah\n3. Mengembangkan bakat akademik, non-akademik, dan kemandirian santri',
    1,
    '2025/2026',
    '© 2025 Pondok Pesantren Al-Falah. All Rights Reserved.'
  );
-- Tingkatan TPQ default
INSERT INTO `tingkatan_tpq` (`nama`, `urutan`)
VALUES ('Jilid 1', 1),
  ('Jilid 2', 2),
  ('Jilid 3', 3),
  ('Jilid 4', 4),
  ('Jilid 5', 5),
  ('Al-Qur\'an', 6);
-- Struktur Diniyyah default dapat diubah oleh admin Diniyyah dari panel.
INSERT INTO `diniyyah_jenjang` (`nama`, `urutan`, `aktif`)
VALUES
  ('Diniyyah Ula', 1, 1),
  ('Diniyyah Wustho', 2, 1),
  ('Diniyyah Ulya', 3, 1)
ON DUPLICATE KEY UPDATE `urutan` = VALUES(`urutan`);

INSERT INTO `diniyyah_kelas` (`jenjang_id`, `nama`, `urutan`, `aktif`)
SELECT dj.id, kelas.nama, kelas.urutan, 1
FROM `diniyyah_jenjang` dj
JOIN (
  SELECT 'Diniyyah Ula' AS jenjang_nama, 'Kelas 1' AS nama, 1 AS urutan
  UNION ALL SELECT 'Diniyyah Ula', 'Kelas 2', 2
  UNION ALL SELECT 'Diniyyah Ula', 'Kelas 3', 3
  UNION ALL SELECT 'Diniyyah Wustho', 'Kelas 1', 1
  UNION ALL SELECT 'Diniyyah Wustho', 'Kelas 2', 2
  UNION ALL SELECT 'Diniyyah Wustho', 'Kelas 3', 3
  UNION ALL SELECT 'Diniyyah Ulya', 'Kelas 1', 1
  UNION ALL SELECT 'Diniyyah Ulya', 'Kelas 2', 2
  UNION ALL SELECT 'Diniyyah Ulya', 'Kelas 3', 3
) kelas ON kelas.jenjang_nama = dj.nama
ON DUPLICATE KEY UPDATE `urutan` = VALUES(`urutan`);
-- Kelas default
INSERT INTO `kelas` (`nama_kelas`, `jenjang`, `tingkat`)
VALUES -- Diniyyah
  ('TK A', 'TK', 'A'),
  ('TK B', 'TK', 'B'),
  ('SD Kelas 1', 'SD', '1'),
('SD Kelas 2', 'SD', '2'),
('SD Kelas 3', 'SD', '3'),
('SD Kelas 4', 'SD', '4'),
('SD Kelas 5', 'SD', '5'),
('SD Kelas 6', 'SD', '6'),
  ('SMP Kelas 7', 'SMP', '7'),
('SMP Kelas 8', 'SMP', '8'),
('SMP Kelas 9', 'SMP', '9'),
  ('SMA Kelas 10', 'SMA', '10'),
('SMA Kelas 11', 'SMA', '11'),
('SMA Kelas 12', 'SMA', '12'),
  ('S1 Semester 1', 'S1', '1'),
('S1 Semester 2', 'S1', '2'),
  -- Diniyyah Ula
  ('Diniyyah Ula Kelas 1', 'Diniyyah Ula', '1'),
('Diniyyah Ula Kelas 2', 'Diniyyah Ula', '2'),
('Diniyyah Ula Kelas 3', 'Diniyyah Ula', '3'),
  -- Diniyyah Wustho
  (
    'Diniyyah Wustho Kelas 1',
    'Diniyyah Wustho',
    '1'
  ),
(
    'Diniyyah Wustho Kelas 2',
    'Diniyyah Wustho',
    '2'
  ),
(
    'Diniyyah Wustho Kelas 3',
    'Diniyyah Wustho',
    '3'
  ),
  -- Diniyyah Ulya
  ('Diniyyah Ulya Kelas 1', 'Diniyyah Ulya', '1'),
('Diniyyah Ulya Kelas 2', 'Diniyyah Ulya', '2'),
('Diniyyah Ulya Kelas 3', 'Diniyyah Ulya', '3');
SET FOREIGN_KEY_CHECKS = 1;