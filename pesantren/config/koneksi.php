<?php
// ============================================================
// KONEKSI DATABASE PDO - SIAP CLOUD HOSTING
// ============================================================

$host = 'localhost';
$db   = 'db_pesantren_alfalah'; // Sesuaikan dengan nama DB di hosting
$user = 'root';                // Sesuaikan dengan user DB di hosting
$pass = '';                    // Sesuaikan dengan pass DB di hosting
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Jangan tampilkan detail error di production
    die("Koneksi Database Gagal. Silakan cek konfigurasi.");
}
?>
