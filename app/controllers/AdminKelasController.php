<?php
require_once ROOT_PATH . '/app/controllers/BaseController.php';

class AdminKelasController extends BaseController {
    public function index(): void {
        require_login();
        require_role(['diniyyah', 'tpq']);

        $jenis = ROLE_JENJANG_MAP[current_user()['role']];
        $kelas = $this->kelas_program($jenis);
        $this->view('admin/admin_kelas/index', compact('jenis', 'kelas'));
    }

    public function simpan(): void {
        require_login();
        require_role(['diniyyah', 'tpq']);
        if (!$this->is_post() || !verify_csrf()) {
            redirect('admin-kelas');
        }

        $jenis = ROLE_JENJANG_MAP[current_user()['role']];
        $kelas_id = (int)$this->post('kelas_id');
        $nama = trim($this->post('nama'));
        $email = strtolower(trim($this->post('email')));
        $password = $this->post('password');

        if (!$this->kelas_valid($jenis, $kelas_id) || $nama === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
            set_flash('error', 'Data admin kelas tidak valid. Nama, email valid, dan password minimal 8 karakter wajib diisi.');
            redirect('admin-kelas');
        }
        if ($this->kelas_sudah_memiliki_admin($jenis, $kelas_id)) {
            set_flash('error', 'Kelas ini sudah memiliki Admin Kelas.');
            redirect('admin-kelas');
        }

        try {
            $this->pdo->beginTransaction();
            $username = 'kelas_' . bin2hex(random_bytes(8));
            $user = $this->pdo->prepare(
                "INSERT INTO users (nama, username, email, password, role, aktif)
                 VALUES (:nama, :username, :email, :password, 'admin_kelas', 1)"
            );
            $user->execute([
                ':nama' => $nama,
                ':username' => $username,
                ':email' => $email,
                ':password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]),
            ]);
            $user_id = (int)$this->pdo->lastInsertId();

            $assignment = $this->pdo->prepare(
                "INSERT INTO admin_kelas (user_id, jenis_jenjang, diniyyah_kelas_id, tingkatan_tpq_id)
                 VALUES (:user_id, :jenis, :diniyyah_kelas_id, :tingkatan_tpq_id)"
            );
            $assignment->execute([
                ':user_id' => $user_id,
                ':jenis' => $jenis,
                ':diniyyah_kelas_id' => $jenis === 'diniyyah' ? $kelas_id : null,
                ':tingkatan_tpq_id' => $jenis === 'tpq' ? $kelas_id : null,
            ]);
            $this->pdo->commit();
            set_flash('success', 'Akun Admin Kelas berhasil dibuat dan ditugaskan.');
        } catch (PDOException $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            set_flash('error', (int)($exception->errorInfo[1] ?? 0) === 1062
                ? 'Email atau penugasan kelas sudah digunakan.'
                : 'Akun Admin Kelas gagal dibuat.');
        }
        redirect('admin-kelas');
    }

    public function hapus(?string $id = null): void {
        require_login();
        require_role(['diniyyah', 'tpq']);
        if (!$this->is_post() || !verify_csrf() || !$id) {
            redirect('admin-kelas');
        }

        $jenis = ROLE_JENJANG_MAP[current_user()['role']];
        $stmt = $this->pdo->prepare(
            'SELECT user_id FROM admin_kelas WHERE id = :id AND jenis_jenjang = :jenis LIMIT 1'
        );
        $stmt->execute([':id' => $id, ':jenis' => $jenis]);
        $user_id = $stmt->fetchColumn();
        if (!$user_id) {
            set_flash('error', 'Admin Kelas tidak ditemukan.');
            redirect('admin-kelas');
        }

        $this->pdo->prepare("DELETE FROM users WHERE id = :id AND role = 'admin_kelas'")->execute([':id' => $user_id]);
        set_flash('success', 'Admin Kelas berhasil dihapus dari penugasan.');
        redirect('admin-kelas');
    }

    private function kelas_program(string $jenis): array {
        if ($jenis === 'diniyyah') {
            return $this->pdo->query(
                "SELECT dk.id, dj.nama AS jenjang, dk.nama AS kelas, ak.id AS assignment_id,
                        u.nama AS admin_nama, u.email AS admin_email
                 FROM diniyyah_kelas dk
                 JOIN diniyyah_jenjang dj ON dj.id = dk.jenjang_id
                 LEFT JOIN admin_kelas ak ON ak.diniyyah_kelas_id = dk.id
                 LEFT JOIN users u ON u.id = ak.user_id
                 ORDER BY dj.urutan, dj.nama, dk.urutan, dk.nama"
            )->fetchAll();
        }

        return $this->pdo->query(
            "SELECT tt.id, tt.nama AS jenjang, NULL AS kelas, ak.id AS assignment_id,
                    u.nama AS admin_nama, u.email AS admin_email
             FROM tingkatan_tpq tt
             LEFT JOIN admin_kelas ak ON ak.tingkatan_tpq_id = tt.id
             LEFT JOIN users u ON u.id = ak.user_id
             ORDER BY tt.urutan, tt.nama"
        )->fetchAll();
    }

    private function kelas_valid(string $jenis, int $kelas_id): bool {
        $sql = $jenis === 'diniyyah'
            ? 'SELECT 1 FROM diniyyah_kelas WHERE id = :id LIMIT 1'
            : 'SELECT 1 FROM tingkatan_tpq WHERE id = :id LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $kelas_id]);
        return (bool)$stmt->fetchColumn();
    }

    private function kelas_sudah_memiliki_admin(string $jenis, int $kelas_id): bool {
        $field = $jenis === 'diniyyah' ? 'diniyyah_kelas_id' : 'tingkatan_tpq_id';
        $stmt = $this->pdo->prepare("SELECT 1 FROM admin_kelas WHERE `$field` = :id LIMIT 1");
        $stmt->execute([':id' => $kelas_id]);
        return (bool)$stmt->fetchColumn();
    }
}
