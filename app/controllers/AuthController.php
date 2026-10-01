<?php
require_once ROOT_PATH . '/app/controllers/BaseController.php';

class AuthController extends BaseController {
    public function login(): void {
        if (is_logged_in()) {
            $this->redirect(has_role('admin_kelas') ? 'santri' : (has_role(['webmaster', 'kegiatan', 'diniyyah', 'tpq']) ? 'admin' : ''));
        }

        $error = null;
        if ($this->is_post()) {
            if (!verify_csrf()) {
                $error = 'Permintaan tidak valid. Coba lagi.';
            } else {
                $identity = trim($this->post('identity'));
                $password = $this->post('password');

                if ($identity === '' || empty($password)) {
                    $error = 'Email atau username admin dan password wajib diisi.';
                } else {
                    $stmt = $this->pdo->prepare(
                        "SELECT * FROM users
                         WHERE (email = :identity1 OR username = :identity2) AND aktif = 1
                         LIMIT 1"
                    );
                    $stmt->execute([':identity1' => $identity, ':identity2' => $identity]);
                    $user = $stmt->fetch();

                    if ($user && password_verify($password, $user['password'])) {
                        session_regenerate_id(true);
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_nama'] = $user['nama'];
                        $_SESSION['user_username'] = $user['username'];
                        $_SESSION['user_role'] = $user['role'];
                        $_SESSION['user_foto'] = $user['foto'];

                        $this->pdo->prepare('UPDATE users SET last_login = NOW() WHERE id = :id')
                            ->execute([':id' => $user['id']]);

                        set_flash('success', 'Selamat datang, ' . $user['nama'] . '!');
                        $this->redirect($user['role'] === 'admin_kelas' ? 'santri' : ($user['role'] === 'user' ? '' : 'admin'));
                    }

                    sleep(1);
                    $error = 'Email, username admin, atau password salah.';
                }
            }
        }

        $data = ['error' => $error, 'pengaturan' => $this->pengaturan];
        extract($data);
        require ROOT_PATH . '/views/auth/login.php';
    }

    public function register(): void {
        if (is_logged_in()) {
            $this->redirect(has_role('admin_kelas') ? 'santri' : (has_role(['webmaster', 'kegiatan', 'diniyyah', 'tpq']) ? 'admin' : ''));
        }

        $error = null;
        if ($this->is_post()) {
            if (!verify_csrf()) {
                $error = 'Permintaan tidak valid. Coba lagi.';
            } else {
                $nama = trim($this->post('nama'));
                $email = strtolower(trim($this->post('email')));
                $password = $this->post('password');
                $password_konfirmasi = $this->post('password_konfirmasi');

                if ($nama === '' || $email === '' || $password === '' || $password_konfirmasi === '') {
                    $error = 'Semua kolom wajib diisi.';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Masukkan alamat email yang valid.';
                } elseif (strlen($password) < 8) {
                    $error = 'Password minimal terdiri dari 8 karakter.';
                } elseif (!hash_equals($password, $password_konfirmasi)) {
                    $error = 'Konfirmasi password tidak sama.';
                } else {
                    try {
                        $username_internal = 'user_' . bin2hex(random_bytes(8));
                        $stmt = $this->pdo->prepare(
                            "INSERT INTO users (nama, username, email, password, role, aktif)
                             VALUES (:nama, :username, :email, :password, 'user', 1)"
                        );
                        $stmt->execute([
                            ':nama' => $nama,
                            ':username' => $username_internal,
                            ':email' => $email,
                            ':password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
                        ]);

                        session_regenerate_id(true);
                        $_SESSION['user_id'] = (int)$this->pdo->lastInsertId();
                        $_SESSION['user_nama'] = $nama;
                        $_SESSION['user_username'] = $email;
                        $_SESSION['user_role'] = 'user';
                        $_SESSION['user_foto'] = null;

                        set_flash('success', 'Akun berhasil dibuat. Selamat datang, ' . $nama . '!');
                        $this->redirect('');
                    } catch (PDOException $error_exception) {
                        if ((int)($error_exception->errorInfo[1] ?? 0) === 1062) {
                            $error = 'Email tersebut sudah digunakan.';
                        } else {
                            $error = 'Akun tidak dapat dibuat. Silakan coba lagi.';
                        }
                    }
                }
            }
        }

        $data = ['error' => $error, 'pengaturan' => $this->pengaturan];
        extract($data);
        require ROOT_PATH . '/views/auth/register.php';
    }

    public function logout(): void {
        session_unset();
        session_destroy();
        session_start();
        session_regenerate_id(true);
        set_flash('success', 'Anda berhasil logout.');
        $this->redirect('login');
    }
}
