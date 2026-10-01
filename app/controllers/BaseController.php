<?php
// ============================================================
// BASE CONTROLLER
// ============================================================
abstract class BaseController {
    protected PDO $pdo;
    protected array $pengaturan = [];

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->ensure_psb_columns();
        $this->ensure_diniyyah_tables();
        $this->ensure_public_user_role();
        $this->ensure_user_email_column();
        $this->ensure_class_admin_structure();
        $this->pengaturan = get_pengaturan($pdo);
        $this->ensure_kelulusan_table();
        $this->ensure_kenaikan_kelas_table();
    }

    protected function view(string $view, array $data = [], string $layout = 'admin'): void {
        $data['pengaturan']   = $this->pengaturan;
        $data['current_user'] = current_user();
        $data['flash_success'] = get_flash('success');
        $data['flash_error']   = get_flash('error');
        $data['flash_warning'] = get_flash('warning');

        $view_content_file = ROOT_PATH . '/views/' . $view . '.php';
        if (!file_exists($view_content_file)) {
            http_response_code(404);
            echo "<h2>View not found: {$view}</h2>";
            return;
        }
        $data['view_content_file'] = $view_content_file;
        $data['view_name'] = $view;

        extract($data);
        $layout_file = ROOT_PATH . '/views/layouts/' . $layout . '.php';
        require $layout_file;
    }

    protected function json(mixed $data, int $code = 200): never {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $path): never {
        header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
        exit;
    }

    protected function is_post(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function post(string $key, mixed $default = ''): mixed {
        return $_POST[$key] ?? $default;
    }

    protected function get(string $key, mixed $default = ''): mixed {
        return $_GET[$key] ?? $default;
    }

    private function ensure_kelulusan_table(): void {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS kelulusan (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            santri_id INT UNSIGNED NOT NULL,
            jenis_jenjang ENUM('diniyyah','tpq') NOT NULL,
            jenjang VARCHAR(100) NOT NULL,
            tahun_ajaran VARCHAR(20) NOT NULL,
            tanggal_lulus DATE DEFAULT NULL,
            nomor_ijazah VARCHAR(100) DEFAULT NULL,
            predikat VARCHAR(100) DEFAULT NULL,
            status ENUM('lulus','tidak_lulus','ditunda') DEFAULT 'lulus',
            catatan TEXT DEFAULT NULL,
            ditetapkan_oleh INT UNSIGNED DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (santri_id) REFERENCES santri(id) ON DELETE CASCADE,
            FOREIGN KEY (ditetapkan_oleh) REFERENCES users(id) ON DELETE SET NULL,
            UNIQUE KEY uq_kelulusan_santri_jenjang_tahun (santri_id, jenis_jenjang, jenjang, tahun_ajaran)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    private function ensure_kenaikan_kelas_table(): void {
        $this->pdo->exec("CREATE TABLE IF NOT EXISTS kenaikan_kelas (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            santri_id INT UNSIGNED NOT NULL,
            jenis_jenjang ENUM('diniyyah','tpq') NOT NULL,
            jenjang_asal VARCHAR(100) NOT NULL,
            kelas_asal VARCHAR(100) DEFAULT NULL,
            jenjang_tujuan VARCHAR(100) DEFAULT NULL,
            kelas_tujuan VARCHAR(100) DEFAULT NULL,
            tahun_ajaran VARCHAR(20) NOT NULL,
            status ENUM('naik','tetap','lulus') NOT NULL DEFAULT 'naik',
            catatan TEXT DEFAULT NULL,
            diproses_oleh INT UNSIGNED DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (santri_id) REFERENCES santri(id) ON DELETE CASCADE,
            FOREIGN KEY (diproses_oleh) REFERENCES users(id) ON DELETE SET NULL,
            UNIQUE KEY uq_kenaikan_santri_program_tahun (santri_id, jenis_jenjang, tahun_ajaran),
            KEY idx_kenaikan_program_status (jenis_jenjang, status, tahun_ajaran)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    private function ensure_public_user_role(): void {
        $stmt = $this->pdo->prepare(
            "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'role'"
        );
        $stmt->execute();
        $column_type = strtolower((string)$stmt->fetchColumn());

        if ($column_type !== '' && !str_contains($column_type, "'user'")) {
            $this->pdo->exec(
                "ALTER TABLE users MODIFY COLUMN role
                 ENUM('webmaster','kegiatan','diniyyah','tpq','user') NOT NULL"
            );
        }
    }

    private function ensure_user_email_column(): void {
        $column = $this->pdo->prepare(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'email'"
        );
        $column->execute();
        if (!(int)$column->fetchColumn()) {
            $this->pdo->exec('ALTER TABLE users ADD COLUMN email VARCHAR(191) DEFAULT NULL AFTER username');
        }

        $index = $this->pdo->prepare(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND INDEX_NAME = 'uq_users_email'"
        );
        $index->execute();
        if (!(int)$index->fetchColumn()) {
            $this->pdo->exec('ALTER TABLE users ADD UNIQUE KEY uq_users_email (email)');
        }
    }

    private function ensure_class_admin_structure(): void {
        $role = $this->pdo->prepare(
            "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'role'"
        );
        $role->execute();
        $column_type = strtolower((string)$role->fetchColumn());
        if ($column_type !== '' && !str_contains($column_type, "'admin_kelas'")) {
            $this->pdo->exec(
                "ALTER TABLE users MODIFY COLUMN role
                 ENUM('webmaster','kegiatan','diniyyah','tpq','admin_kelas','user') NOT NULL"
            );
        }

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS admin_kelas (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED NOT NULL,
            jenis_jenjang ENUM('diniyyah','tpq') NOT NULL,
            diniyyah_kelas_id INT UNSIGNED DEFAULT NULL,
            tingkatan_tpq_id INT UNSIGNED DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uq_admin_kelas_user (user_id),
            UNIQUE KEY uq_admin_kelas_diniyyah (diniyyah_kelas_id),
            UNIQUE KEY uq_admin_kelas_tpq (tingkatan_tpq_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (diniyyah_kelas_id) REFERENCES diniyyah_kelas(id) ON DELETE CASCADE,
            FOREIGN KEY (tingkatan_tpq_id) REFERENCES tingkatan_tpq(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    private function ensure_psb_columns(): void {
        $columns = [
            'psb_diniyyah_aktif' => 'TINYINT(1) NOT NULL DEFAULT 1',
            'psb_tpq_aktif' => 'TINYINT(1) NOT NULL DEFAULT 1',
            'beranda_sambutan' => 'TEXT DEFAULT NULL',
            'tentang_konten' => 'TEXT DEFAULT NULL',
            'publikasi_konten' => 'TEXT DEFAULT NULL',
        ];
        foreach ($columns as $name => $definition) {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'pengaturan_web' AND COLUMN_NAME = :column_name");
            $stmt->execute([':column_name' => $name]);
            if (!(int)$stmt->fetchColumn()) {
                $this->pdo->exec("ALTER TABLE pengaturan_web ADD COLUMN `$name` $definition AFTER psb_aktif");
            }
        }
    }

    private function ensure_diniyyah_tables(): void {
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS diniyyah_jenjang (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                nama VARCHAR(100) NOT NULL,
                urutan TINYINT UNSIGNED NOT NULL DEFAULT 0,
                aktif TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uq_diniyyah_jenjang_nama (nama)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            $this->pdo->exec("CREATE TABLE IF NOT EXISTS diniyyah_kelas (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                jenjang_id INT UNSIGNED NOT NULL,
                nama VARCHAR(100) NOT NULL,
                urutan TINYINT UNSIGNED NOT NULL DEFAULT 0,
                aktif TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uq_diniyyah_kelas_jenjang_nama (jenjang_id, nama),
                KEY idx_diniyyah_kelas_aktif (jenjang_id, aktif, urutan),
                CONSTRAINT fk_diniyyah_kelas_jenjang FOREIGN KEY (jenjang_id)
                    REFERENCES diniyyah_jenjang(id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            if ((int)$this->pdo->query("SELECT COUNT(*) FROM diniyyah_jenjang")->fetchColumn() === 0) {
                $insert_level = $this->pdo->prepare(
                    'INSERT INTO diniyyah_jenjang (nama, urutan, aktif) VALUES (:nama, :urutan, 1)'
                );
                $insert_class = $this->pdo->prepare(
                    'INSERT INTO diniyyah_kelas (jenjang_id, nama, urutan, aktif) VALUES (:jenjang_id, :nama, :urutan, 1)'
                );
                $defaults = ['Diniyyah Ula', 'Diniyyah Wustho', 'Diniyyah Ulya'];

                foreach ($defaults as $level_order => $level_name) {
                    $insert_level->execute([':nama' => $level_name, ':urutan' => $level_order + 1]);
                    $level_id = (int)$this->pdo->lastInsertId();
                    foreach (['Kelas 1', 'Kelas 2', 'Kelas 3'] as $class_order => $class_name) {
                        $insert_class->execute([
                            ':jenjang_id' => $level_id,
                            ':nama' => $class_name,
                            ':urutan' => $class_order + 1,
                        ]);
                    }
                }
        }
    }
}
