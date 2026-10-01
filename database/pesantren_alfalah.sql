-- Schema hosting/cPanel Pesantren Salaf Al Falah.
-- Import file ini setelah memilih database tujuan di phpMyAdmin.
-- File ini sengaja tidak berisi CREATE DATABASE atau USE.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `users` (
  `id_user` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `email` VARCHAR(191) DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `role` ENUM('master_admin','admin_publikasi','admin_awaliyah','admin_wustho','admin_tpq','admin_kelas','user') NOT NULL,
  `aktif` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `uq_users_username` (`username`),
  UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `publikasi` (
  `id_publikasi` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `judul` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `konten` LONGTEXT NOT NULL,
  `kategori` ENUM('kegiatan','pemberitahuan','fatwa_ulama') NOT NULL,
  `gambar` VARCHAR(255) DEFAULT NULL,
  `id_penulis` INT UNSIGNED NOT NULL,
  `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `disetujui_oleh` INT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_publikasi`),
  UNIQUE KEY `uq_publikasi_slug` (`slug`),
  KEY `idx_publikasi_status_kategori` (`status`,`kategori`),
  KEY `idx_publikasi_penulis` (`id_penulis`),
  CONSTRAINT `fk_publikasi_penulis` FOREIGN KEY (`id_penulis`) REFERENCES `users` (`id_user`),
  CONSTRAINT `fk_publikasi_penyetuju` FOREIGN KEY (`disetujui_oleh`) REFERENCES `users` (`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `pendaftaran` (
  `id_pendaftaran` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_santri` VARCHAR(150) NOT NULL,
  `jenis_kelamin` ENUM('L','P') NOT NULL,
  `nama_wali` VARCHAR(150) NOT NULL,
  `no_hp_wali` VARCHAR(30) NOT NULL,
  `alamat` TEXT NOT NULL,
  `program` ENUM('awaliyah','wustho','tpq') NOT NULL,
  `kelas_sekarang` VARCHAR(50) DEFAULT NULL,
  `status_pendaftaran` ENUM('pending','diterima','ditolak') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pendaftaran`),
  KEY `idx_pendaftaran_program_status` (`program`,`status_pendaftaran`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `diniyyah_jenjang` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(100) NOT NULL,
  `urutan` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `aktif` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_diniyyah_jenjang_nama` (`nama`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `diniyyah_kelas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `jenjang_id` INT UNSIGNED NOT NULL,
  `nama` VARCHAR(100) NOT NULL,
  `urutan` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `aktif` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_diniyyah_kelas_jenjang_nama` (`jenjang_id`,`nama`),
  KEY `idx_diniyyah_kelas_aktif` (`jenjang_id`,`aktif`,`urutan`),
  CONSTRAINT `fk_diniyyah_kelas_jenjang` FOREIGN KEY (`jenjang_id`)
    REFERENCES `diniyyah_jenjang` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `admin_kelas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `jenis_jenjang` ENUM('diniyyah','tpq') NOT NULL,
  `diniyyah_kelas_id` INT UNSIGNED DEFAULT NULL,
  `tingkatan_tpq_id` INT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_admin_kelas_user` (`user_id`),
  UNIQUE KEY `uq_admin_kelas_diniyyah` (`diniyyah_kelas_id`),
  UNIQUE KEY `uq_admin_kelas_tpq` (`tingkatan_tpq_id`),
  CONSTRAINT `fk_admin_kelas_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `fk_admin_kelas_diniyyah` FOREIGN KEY (`diniyyah_kelas_id`) REFERENCES `diniyyah_kelas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `kenaikan_kelas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_pendaftaran` INT UNSIGNED NOT NULL,
  `program` ENUM('awaliyah','wustho','tpq') NOT NULL,
  `kelas_asal` VARCHAR(100) DEFAULT NULL,
  `kelas_tujuan` VARCHAR(100) DEFAULT NULL,
  `tahun_ajaran` VARCHAR(20) NOT NULL,
  `status` ENUM('naik','tetap','lulus') NOT NULL DEFAULT 'naik',
  `catatan` TEXT DEFAULT NULL,
  `diproses_oleh` INT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_kenaikan_pendaftaran_tahun` (`id_pendaftaran`,`tahun_ajaran`),
  KEY `idx_kenaikan_program_status` (`program`,`status`,`tahun_ajaran`),
  CONSTRAINT `fk_kenaikan_pendaftaran` FOREIGN KEY (`id_pendaftaran`)
    REFERENCES `pendaftaran` (`id_pendaftaran`) ON DELETE CASCADE,
  CONSTRAINT `fk_kenaikan_pemroses` FOREIGN KEY (`diproses_oleh`)
    REFERENCES `users` (`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `program_info` (
  `id_info` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `program` ENUM('awaliyah','wustho','majlis','tpq','psq') NOT NULL,
  `judul` VARCHAR(150) NOT NULL,
  `deskripsi` TEXT NOT NULL,
  `jadwal` VARCHAR(255) DEFAULT NULL,
  `aktif` TINYINT(1) NOT NULL DEFAULT 1,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_info`),
  UNIQUE KEY `uq_program_info_program` (`program`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`username`,`password`,`nama_lengkap`,`role`)
VALUES (
  'masteradmin',
  '$2y$12$y3/COZRakuuJ5XVC.WxN/et8Nil6K3r5B4kg2sacuMCdqDxQvAJmu',
  'Master Admin Al Falah',
  'master_admin'
)
ON DUPLICATE KEY UPDATE `username` = VALUES(`username`);

INSERT INTO `program_info` (`program`,`judul`,`deskripsi`,`jadwal`)
VALUES
  ('awaliyah','Diniyyah Awaliyah','Pendidikan dasar keislaman dan pembinaan akhlak.','Menyesuaikan jadwal pesantren'),
  ('wustho','Diniyyah Wustho','Pendalaman kitab turats dan ilmu-ilmu keislaman.','Menyesuaikan jadwal pesantren'),
  ('majlis','Majlis','Majlis ilmu dan kajian rutin untuk masyarakat.','Menyesuaikan jadwal majlis'),
  ('tpq','TPQ','Pembelajaran baca Al-Quran bertahap untuk anak-anak.','Menyesuaikan jadwal TPQ'),
  ('psq','PSQ','Program pembinaan dan penguatan pemahaman Al-Quran.','Menyesuaikan jadwal PSQ')
ON DUPLICATE KEY UPDATE `judul` = VALUES(`judul`);

INSERT INTO `diniyyah_jenjang` (`nama`,`urutan`,`aktif`)
VALUES
  ('Diniyyah Ula',1,1),
  ('Diniyyah Wustho',2,1),
  ('Diniyyah Ulya',3,1)
ON DUPLICATE KEY UPDATE `urutan` = VALUES(`urutan`);

INSERT INTO `diniyyah_kelas` (`jenjang_id`,`nama`,`urutan`,`aktif`)
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

SET FOREIGN_KEY_CHECKS = 1;
