<?php
// ============================================================
// UTILITY: Generate Password Hash
// Jalankan sekali untuk generate hash, lalu hapus file ini!
// Akses: http://localhost/pesantren/al%20falah/generate_password.php
// ============================================================

if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') {
    die('Hanya bisa diakses dari localhost!');
}

$password = 'Admin@12345'; // Ganti sesuai kebutuhan

$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

echo "<pre>";
echo "Password   : " . $password . "\n";
echo "Hash (BCrypt cost=12):\n" . $hash . "\n\n";
echo "Gunakan hash ini di kolom 'password' tabel users.\n\n";
echo "UPDATE users SET password='" . $hash . "' WHERE username='webmaster';\n";
echo "</pre>";

echo "<br><strong style='color:red'>⚠️ HAPUS FILE INI SETELAH DIGUNAKAN!</strong>";
