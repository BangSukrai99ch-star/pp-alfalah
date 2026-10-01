<?php
require_once ROOT_PATH . '/app/controllers/BaseController.php';

// ============================================================
// SANTRI CONTROLLER (Data Induk Santri - CRUD)
// ============================================================
class SantriController extends BaseController {

    public function index(): void {
        require_login();
        require_role(['diniyyah', 'tpq', 'webmaster', 'admin_kelas']);

        $role    = current_user()['role'];
        $jenjang = ($role === 'webmaster' || $role === 'admin_kelas') ? null : ROLE_JENJANG_MAP[$role];
        $assignment = $role === 'admin_kelas' ? $this->class_assignment() : null;
        if ($role === 'admin_kelas' && !$assignment) {
            set_flash('error', 'Akun Anda belum ditugaskan ke kelas.');
            redirect('');
        }
        $search  = trim($this->get('q', ''));
        $status  = $this->get('status', 'aktif');
        $page    = (int)$this->get('page', 1);

        $params = [];
        $where  = ['1=1'];

        // Filter berdasarkan jenjang admin
        if ($assignment) {
            $where[] = "EXISTS (
                SELECT 1 FROM pendaftaran_jenjang pj2
                WHERE pj2.santri_id = s.id
                  AND pj2.jenis_jenjang = :class_jenis
                  AND pj2.status = 'diterima'
                  AND COALESCE((
                    SELECT kk.jenjang_tujuan FROM kenaikan_kelas kk
                    WHERE kk.santri_id = s.id AND kk.jenis_jenjang = pj2.jenis_jenjang
                    ORDER BY kk.created_at DESC, kk.id DESC LIMIT 1
                  ), pj2.jenjang_dipilih) = :class_jenjang
                  AND (:class_kelas IS NULL OR COALESCE((
                    SELECT kk.kelas_tujuan FROM kenaikan_kelas kk
                    WHERE kk.santri_id = s.id AND kk.jenis_jenjang = pj2.jenis_jenjang
                    ORDER BY kk.created_at DESC, kk.id DESC LIMIT 1
                  ), pj2.tingkat_dipilih) = :class_kelas)
            )";
            $params[':class_jenis'] = $assignment['jenis_jenjang'];
            $params[':class_jenjang'] = $assignment['jenjang'];
            $params[':class_kelas'] = $assignment['kelas'];
        } elseif ($jenjang) {
            $where[] = "EXISTS (
                SELECT 1 FROM pendaftaran_jenjang pj2
                WHERE pj2.santri_id = s.id
                  AND pj2.jenis_jenjang = :jenjang
                  AND pj2.status = 'diterima'
            )";
            $params[':jenjang'] = $jenjang;
        }
        if (!empty($search)) {
            $where[] = '(s.nama_lengkap LIKE :q1 OR s.nisn LIKE :q2 OR s.nis_pesantren LIKE :q3)';
            $params[':q1'] = $params[':q2'] = $params[':q3'] = '%' . $search . '%';
        }
        if (!empty($status)) {
            $where[] = 's.status = :status';
            $params[':status'] = $status;
        }

        $where_sql = implode(' AND ', $where);

        $cnt = $this->pdo->prepare("SELECT COUNT(*) FROM santri s WHERE {$where_sql}");
        $cnt->execute($params);
        $total = (int)$cnt->fetchColumn();
        $pg    = paginate($total, $page);

        $stmt = $this->pdo->prepare("
            SELECT s.*,
                   GROUP_CONCAT(DISTINCT pj.jenis_jenjang SEPARATOR ', ') as jenjang_list
            FROM santri s
            LEFT JOIN pendaftaran_jenjang pj ON pj.santri_id = s.id
            WHERE {$where_sql}
            GROUP BY s.id
            ORDER BY s.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':limit',  $pg['per_page'], PDO::PARAM_INT);
        $stmt->bindValue(':offset', $pg['offset'],   PDO::PARAM_INT);
        $stmt->execute();
        $santri_list = $stmt->fetchAll();

        $this->view('admin/santri/index', compact('santri_list', 'pg', 'search', 'status', 'role'));
    }

    public function export(): void {
        require_login();
        require_role(['diniyyah', 'tpq', 'webmaster', 'admin_kelas']);

        $role    = current_user()['role'];
        $jenjang = ($role === 'webmaster' || $role === 'admin_kelas') ? null : ROLE_JENJANG_MAP[$role];
        $assignment = $role === 'admin_kelas' ? $this->class_assignment() : null;
        if ($role === 'admin_kelas' && !$assignment) {
            set_flash('error', 'Akun Anda belum ditugaskan ke kelas.');
            redirect('santri');
        }
        $search  = trim($this->get('q', ''));
        $status  = $this->get('status', 'aktif');

        $params = [];
        $where  = ['1=1'];

        if ($assignment) {
            $where[] = "EXISTS (
                SELECT 1 FROM pendaftaran_jenjang pj2
                WHERE pj2.santri_id = s.id
                  AND pj2.jenis_jenjang = :class_jenis
                  AND pj2.status = 'diterima'
                  AND COALESCE((
                    SELECT kk.jenjang_tujuan FROM kenaikan_kelas kk
                    WHERE kk.santri_id = s.id AND kk.jenis_jenjang = pj2.jenis_jenjang
                    ORDER BY kk.created_at DESC, kk.id DESC LIMIT 1
                  ), pj2.jenjang_dipilih) = :class_jenjang
                  AND (:class_kelas IS NULL OR COALESCE((
                    SELECT kk.kelas_tujuan FROM kenaikan_kelas kk
                    WHERE kk.santri_id = s.id AND kk.jenis_jenjang = pj2.jenis_jenjang
                    ORDER BY kk.created_at DESC, kk.id DESC LIMIT 1
                  ), pj2.tingkat_dipilih) = :class_kelas)
            )";
            $params[':class_jenis'] = $assignment['jenis_jenjang'];
            $params[':class_jenjang'] = $assignment['jenjang'];
            $params[':class_kelas'] = $assignment['kelas'];
        } elseif ($jenjang) {
            $where[] = "EXISTS (
                SELECT 1 FROM pendaftaran_jenjang pj2
                WHERE pj2.santri_id = s.id
                  AND pj2.jenis_jenjang = :jenjang
                  AND pj2.status = 'diterima'
            )";
            $params[':jenjang'] = $jenjang;
        }
        if (!empty($search)) {
            $where[] = '(s.nama_lengkap LIKE :q1 OR s.nisn LIKE :q2 OR s.nis_pesantren LIKE :q3)';
            $params[':q1'] = $params[':q2'] = $params[':q3'] = '%' . $search . '%';
        }
        if (!empty($status)) {
            $where[] = 's.status = :status';
            $params[':status'] = $status;
        }
        $where_sql = implode(' AND ', $where);

        $stmt = $this->pdo->prepare("
            SELECT s.*,
                   GROUP_CONCAT(DISTINCT pj.jenis_jenjang SEPARATOR ', ') as jenjang_list
            FROM santri s
            LEFT JOIN pendaftaran_jenjang pj ON pj.santri_id = s.id
            WHERE {$where_sql}
            GROUP BY s.id
            ORDER BY s.created_at DESC
        ");
        $stmt->execute($params);
        $data = $stmt->fetchAll();

        $headers = [
            'No', 'NIS Pesantren', 'NISN', 'Nama Lengkap', 'Jenis Kelamin', 'Tempat Lahir',
            'Tanggal Lahir', 'Alamat', 'Nama Ayah', 'Nama Ibu', 'Nama Wali', 'No HP Wali',
            'Email Wali', 'Program Diikuti', 'Status', 'Terdaftar Sejak',
        ];
        $rows = [];
        foreach ($data as $i => $row) {
            $rows[] = [
                $i + 1,
                $row['nis_pesantren'],
                $row['nisn'],
                $row['nama_lengkap'],
                $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan',
                $row['tempat_lahir'],
                $row['tanggal_lahir'],
                $row['alamat'],
                $row['nama_ayah'],
                $row['nama_ibu'],
                $row['nama_wali'],
                $row['no_hp_wali'],
                $row['email_wali'],
                $row['jenjang_list'],
                ucfirst($row['status']),
                $row['created_at'],
            ];
        }

        $nama_file = 'data_santri_' . ($jenjang ?: 'semua') . '_' . date('Ymd_His') . '.xls';
        export_excel($nama_file, $headers, $rows);
    }

    public function detail(?string $id = null): void {
        require_login();
        require_role(['diniyyah', 'tpq', 'webmaster', 'admin_kelas']);

        $santri = $this->find_santri($id);
        if (!$santri || (current_user()['role'] === 'admin_kelas' && !$this->class_admin_can_access((int)$id))) { redirect('santri'); }

        $pendaftaran = $this->pdo->prepare(
            "SELECT pj.*, u.nama as validator_nama FROM pendaftaran_jenjang pj
             LEFT JOIN users u ON pj.divalidasi_oleh = u.id
             WHERE pj.santri_id = :sid ORDER BY pj.created_at DESC"
        );
        $pendaftaran->execute([':sid' => $id]);
        $riwayat_daftar = $pendaftaran->fetchAll();

        $kelas_riwayat = $this->pdo->prepare(
            "SELECT sk.*, k.nama_kelas, k.jenjang FROM santri_kelas sk
             JOIN kelas k ON sk.kelas_id = k.id
             WHERE sk.santri_id = :sid ORDER BY sk.tahun_ajaran DESC"
        );
        $kelas_riwayat->execute([':sid' => $id]);
        $riwayat_kelas = $kelas_riwayat->fetchAll();

        $kelulusan_stmt = $this->pdo->prepare(
            "SELECT k.*, u.nama AS penetap_nama FROM kelulusan k
             LEFT JOIN users u ON k.ditetapkan_oleh = u.id
             WHERE k.santri_id = :sid ORDER BY k.tahun_ajaran DESC, k.created_at DESC"
        );
        $kelulusan_stmt->execute([':sid' => $id]);
        $riwayat_kelulusan = $kelulusan_stmt->fetchAll();

        $kenaikan_stmt = $this->pdo->prepare(
            "SELECT kk.*, u.nama AS pemroses_nama FROM kenaikan_kelas kk
             LEFT JOIN users u ON u.id = kk.diproses_oleh
             WHERE kk.santri_id = :sid ORDER BY kk.tahun_ajaran DESC, kk.created_at DESC"
        );
        $kenaikan_stmt->execute([':sid' => $id]);
        $riwayat_kenaikan = $kenaikan_stmt->fetchAll();

        $this->view('admin/santri/detail', compact('santri', 'riwayat_daftar', 'riwayat_kelas', 'riwayat_kelulusan', 'riwayat_kenaikan'));
    }

    public function edit(?string $id = null): void {
        require_login();
        require_role(['diniyyah', 'tpq', 'webmaster', 'admin_kelas']);

        $santri = $this->find_santri($id);
        if (!$santri || (current_user()['role'] === 'admin_kelas' && !$this->class_admin_can_access((int)$id))) { redirect('santri'); }

        if ($this->is_post()) {
            if (!verify_csrf()) {
                set_flash('error', 'Permintaan tidak valid.');
                $this->view('admin/santri/form', compact('santri'));
                return;
            }
            $this->update_santri($id, $santri);
            return;
        }

        $this->view('admin/santri/form', compact('santri'));
    }

    private function update_santri(string $id, array $santri): void {
        $fields = ['nama_lengkap','jenis_kelamin','tempat_lahir','tanggal_lahir',
                   'agama','anak_ke','alamat','kelurahan','kecamatan','kabupaten',
                   'provinsi','kode_pos','nama_ayah','pekerjaan_ayah','nama_ibu',
                   'pekerjaan_ibu','nama_wali','hubungan_wali','no_hp_wali',
                   'email_wali','nisn','nis_pesantren','status'];

        $data  = [];
        $sets  = [];
        foreach ($fields as $f) {
            $val      = trim($this->post($f, $santri[$f] ?? ''));
            $data[":$f"] = $val;
            $sets[]   = "`$f` = :$f";
        }

        // Upload foto
        if (!empty($_FILES['foto']['name'])) {
            $upload = upload_file($_FILES['foto'], 'santri');
            if ($upload['success']) {
                delete_file($santri['foto']);
                $data[':foto'] = $upload['path'];
                $sets[]        = '`foto` = :foto';
            } else {
                set_flash('error', $upload['error']);
                $this->view('admin/santri/form', compact('santri'));
                return;
            }
        }

        $data[':id'] = $id;
        $stmt = $this->pdo->prepare("UPDATE santri SET " . implode(', ', $sets) . " WHERE id = :id");
        $stmt->execute($data);
        set_flash('success', 'Data santri berhasil diperbarui.');
        redirect('santri/detail/' . $id);
    }

    public function delete(?string $id = null): void {
        require_login();
        require_role(['webmaster']);

        if (!$this->is_post() || !verify_csrf()) {
            redirect('santri');
        }
        $santri = $this->find_santri($id);
        if ($santri) {
            delete_file($santri['foto']);
            $this->pdo->prepare("DELETE FROM santri WHERE id=:id")->execute([':id' => $id]);
            set_flash('success', 'Data santri berhasil dihapus.');
        }
        redirect('santri');
    }

    private function find_santri(?string $id): ?array {
        if (!$id) return null;
        $stmt = $this->pdo->prepare("SELECT * FROM santri WHERE id=:id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    private function class_assignment(): ?array {
        $stmt = $this->pdo->prepare(
            "SELECT ak.jenis_jenjang, dj.nama AS diniyyah_jenjang, dk.nama AS diniyyah_kelas, tt.nama AS tpq_tingkatan
             FROM admin_kelas ak
             LEFT JOIN diniyyah_kelas dk ON dk.id = ak.diniyyah_kelas_id
             LEFT JOIN diniyyah_jenjang dj ON dj.id = dk.jenjang_id
             LEFT JOIN tingkatan_tpq tt ON tt.id = ak.tingkatan_tpq_id
             WHERE ak.user_id = :user_id LIMIT 1"
        );
        $stmt->execute([':user_id' => current_user()['id']]);
        $assignment = $stmt->fetch();
        if (!$assignment) {
            return null;
        }
        return [
            'jenis_jenjang' => $assignment['jenis_jenjang'],
            'jenjang' => $assignment['jenis_jenjang'] === 'diniyyah' ? $assignment['diniyyah_jenjang'] : $assignment['tpq_tingkatan'],
            'kelas' => $assignment['jenis_jenjang'] === 'diniyyah' ? $assignment['diniyyah_kelas'] : null,
        ];
    }

    private function class_admin_can_access(int $santri_id): bool {
        $assignment = $this->class_assignment();
        if (!$assignment) {
            return false;
        }
        $stmt = $this->pdo->prepare(
            "SELECT 1 FROM pendaftaran_jenjang pj
             WHERE pj.santri_id = :santri_id
               AND pj.jenis_jenjang = :jenis
               AND pj.status = 'diterima'
               AND COALESCE((
                 SELECT kk.jenjang_tujuan FROM kenaikan_kelas kk
                 WHERE kk.santri_id = pj.santri_id AND kk.jenis_jenjang = pj.jenis_jenjang
                 ORDER BY kk.created_at DESC, kk.id DESC LIMIT 1
               ), pj.jenjang_dipilih) = :jenjang
               AND (:kelas1 IS NULL OR COALESCE((
                 SELECT kk.kelas_tujuan FROM kenaikan_kelas kk
                 WHERE kk.santri_id = pj.santri_id AND kk.jenis_jenjang = pj.jenis_jenjang
                 ORDER BY kk.created_at DESC, kk.id DESC LIMIT 1
               ), pj.tingkat_dipilih) = :kelas2)
             LIMIT 1"
        );
        $stmt->execute([
            ':santri_id' => $santri_id,
            ':jenis' => $assignment['jenis_jenjang'],
            ':jenjang' => $assignment['jenjang'],
            ':kelas1' => $assignment['kelas'],
            ':kelas2' => $assignment['kelas'],
        ]);
        return (bool)$stmt->fetchColumn();
    }
}
