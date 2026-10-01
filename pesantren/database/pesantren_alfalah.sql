-- ============================================================
-- SISTEM INFORMASI PESANTREN AL FALAH
-- Database Schema for Cloud Hosting (Compatible with Shared Hosting)
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. TABEL USERS
CREATE TABLE IF NOT EXISTS `users` (
  `id_user` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nama_lengkap` VARCHAR(100) NOT NULL,
  `role` ENUM('master_admin', 'admin_publikasi', 'admin_awaliyah', 'admin_wustho', 'admin_tpq') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. TABEL PUBLIKASI
CREATE TABLE IF NOT EXISTS `publikasi` (
  `id_publikasi` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `judul` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `konten` LONGTEXT NOT NULL,
  `kategori` ENUM('kegiatan', 'pemberitahuan', 'fatwa_ulama') NOT NULL,
  `gambar` VARCHAR(255) DEFAULT NULL,
  `id_penulis` INT UNSIGNED NOT NULL,
  `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
  `disetujui_oleh` INT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`id_penulis`) REFERENCES `users`(`id_user`) ON DELETE CASCADE,
  FOREIGN KEY (`disetujui_oleh`) REFERENCES `users`(`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. TABEL PENDAFTARAN
CREATE TABLE IF NOT EXISTS `pendaftaran` (
  `id_pendaftaran` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nama_santri` VARCHAR(150) NOT NULL,
  `jenis_kelamin` ENUM('L', 'P') NOT NULL,
  `nama_wali` VARCHAR(150) NOT NULL,
  `no_hp_wali` VARCHAR(20) NOT NULL,
  `alamat` TEXT NOT NULL,
  `program` ENUM('awaliyah', 'wustho', 'tpq') NOT NULL,
  `kelas_sekarang` VARCHAR(50) DEFAULT NULL,
  `status_pendaftaran` ENUM('baru', 'diproses', 'diterima', 'ditolak') DEFAULT 'baru',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- DATA AWAL (SEEDER)
-- Password: Admin@123 (Sudah di-hash menggunakan password_hash DEFAULT)
INSERT INTO `users` (`username`, `password`, `nama_lengkap`, `role`) VALUES
('masteradmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Master Admin Al Falah', 'master_admin'),
('adminpub', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Publikasi', 'admin_publikasi'),
('adminawa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Awaliyah', 'admin_awaliyah'),
('adminwus', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Wustho', 'admin_wustho'),
('admintpq', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin TPQ', 'admin_tpq');

SET FOREIGN_KEY_CHECKS = 1;
