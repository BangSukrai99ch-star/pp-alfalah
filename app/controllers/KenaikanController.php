<?php
require_once ROOT_PATH . '/app/controllers/BaseController.php';

class KenaikanController extends BaseController {
    public function index(): void {
        require_login();
        require_role(['diniyyah', 'tpq', 'webmaster', 'admin_kelas']);

        $role = current_user()['role'];
        $assignment = $role === 'admin_kelas' ? $this->class_assignment() : null;
        if ($role === 'admin_kelas' && !$assignment) {
            set_flash('error', 'Akun Anda belum ditugaskan ke kelas.');
            redirect('santri');
        }
        $jenis = $role === 'webmaster' ? null : ($assignment['jenis_jenjang'] ?? ROLE_JENJANG_MAP[$role]);
        $tahun = trim($this->get('tahun', ''));
        $search = trim($this->get('q', ''));
        $page = max(1, (int)$this->get('page', 1));

        $where = ['1=1'];
        $params = [];
        if ($jenis !== null) {
            $where[] = 'kk.jenis_jenjang = :jenis';
            $params[':jenis'] = $jenis;
        }
        if ($assignment) {
            $where[] = 'kk.jenjang_asal = :kelas_jenjang';
            $params[':kelas_jenjang'] = $assignment['jenjang'];
            if ($assignment['kelas'] !== null) {
                $where[] = 'kk.kelas_asal = :kelas_nama';
                $params[':kelas_nama'] = $assignment['kelas'];
            }
        }
        if ($tahun !== '') {
            $where[] = 'kk.tahun_ajaran = :tahun';
            $params[':tahun'] = $tahun;
        }
        if ($search !== '') {
            $where[] = '(s.nama_lengkap LIKE :q1 OR s.nis_pesantren LIKE :q2)';
            $params[':q1'] = $params[':q2'] = '%' . $search . '%';
        }
        $whereSql = implode(' AND ', $where);

        $count = $this->pdo->prepare(
            "SELECT COUNT(*) FROM kenaikan_kelas kk
             JOIN santri s ON s.id = kk.santri_id
             WHERE {$whereSql}"
        );
        $count->execute($params);
        $pg = paginate((int)$count->fetchColumn(), $page);

        $stmt = $this->pdo->prepare(
            "SELECT kk.*, s.nama_lengkap, s.nis_pesantren, u.nama AS pemroses_nama
             FROM kenaikan_kelas kk
             JOIN santri s ON s.id = kk.santri_id
             LEFT JOIN users u ON u.id = kk.diproses_oleh
             WHERE {$whereSql}
             ORDER BY kk.tahun_ajaran DESC, kk.created_at DESC
             LIMIT :limit OFFSET :offset"
        );
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $pg['per_page'], PDO::PARAM_INT);
        $stmt->bindValue(':offset', $pg['offset'], PDO::PARAM_INT);
        $stmt->execute();
        $kenaikan = $stmt->fetchAll();

        $this->view('admin/kenaikan/index', compact('role', 'jenis', 'tahun', 'search', 'pg', 'kenaikan'));
    }

    public function export(): void {
        require_login();
        require_role(['diniyyah', 'tpq', 'webmaster', 'admin_kelas']);

        $role = current_user()['role'];
        $assignment = $role === 'admin_kelas' ? $this->class_assignment() : null;
        if ($role === 'admin_kelas' && !$assignment) {
            set_flash('error', 'Akun Anda belum ditugaskan ke kelas.');
            redirect('santri');
        }
        $jenis = $role === 'webmaster' ? null : ($assignment['jenis_jenjang'] ?? ROLE_JENJANG_MAP[$role]);
        $tahun = trim($this->get('tahun', ''));
        $search = trim($this->get('q', ''));

        $where = ['1=1'];
        $params = [];
        if ($jenis !== null) {
            $where[] = 'kk.jenis_jenjang = :jenis';
            $params[':jenis'] = $jenis;
        }
        if ($assignment) {
            $where[] = 'kk.jenjang_asal = :kelas_jenjang';
            $params[':kelas_jenjang'] = $assignment['jenjang'];
            if ($assignment['kelas'] !== null) {
                $where[] = 'kk.kelas_asal = :kelas_nama';
                $params[':kelas_nama'] = $assignment['kelas'];
            }
        }
        if ($tahun !== '') {
            $where[] = 'kk.tahun_ajaran = :tahun';
            $params[':tahun'] = $tahun;
        }
        if ($search !== '') {
            $where[] = '(s.nama_lengkap LIKE :q1 OR s.nis_pesantren LIKE :q2)';
            $params[':q1'] = $params[':q2'] = '%' . $search . '%';
        }
        $whereSql = implode(' AND ', $where);

        $stmt = $this->pdo->prepare(
            "SELECT kk.*, s.nama_lengkap, s.nis_pesantren, u.nama AS pemroses_nama
             FROM kenaikan_kelas kk
             JOIN santri s ON s.id = kk.santri_id
             LEFT JOIN users u ON u.id = kk.diproses_oleh
             WHERE {$whereSql}
             ORDER BY kk.tahun_ajaran DESC, kk.created_at DESC"
        );
        $stmt->execute($params);
        $data = $stmt->fetchAll();

        $status_labels = ['naik' => 'Naik Kelas', 'tetap' => 'Tetap Kelas', 'lulus' => 'Lulus Program'];
        $headers = [
            'No', 'Nama Santri', 'NIS Pesantren', 'Program', 'Jenjang Asal', 'Kelas Asal',
            'Jenjang Tujuan', 'Kelas Tujuan', 'Tahun Ajaran', 'Status', 'Catatan',
            'Diproses Oleh', 'Tanggal Diproses',
        ];
        $rows = [];
        foreach ($data as $i => $row) {
            $rows[] = [
                $i + 1,
                $row['nama_lengkap'],
                $row['nis_pesantren'],
                ucfirst($row['jenis_jenjang']),
                $row['jenjang_asal'],
                $row['kelas_asal'],
                $row['jenjang_tujuan'],
                $row['kelas_tujuan'],
                $row['tahun_ajaran'],
                $status_labels[$row['status']] ?? $row['status'],
                $row['catatan'],
                $row['pemroses_nama'],
                $row['created_at'],
            ];
        }

        $nama_file = 'kenaikan_kelas_' . ($jenis ?: 'semua') . '_' . date('Ymd_His') . '.xls';
        export_excel($nama_file, $headers, $rows);
    }

    public function tambah(): void {
        require_login();
        require_role(['diniyyah', 'tpq', 'admin_kelas']);

        $role = current_user()['role'];
        $assignment = $role === 'admin_kelas' ? $this->class_assignment() : null;
        if ($role === 'admin_kelas' && !$assignment) {
            set_flash('error', 'Akun Anda belum ditugaskan ke kelas.');
            redirect('santri');
        }
        $jenis = $assignment['jenis_jenjang'] ?? ROLE_JENJANG_MAP[$role];
        $penempatan = $this->current_placements($jenis, $assignment);
        $pilihan_tujuan = $this->target_options($jenis);
        $selected_santri_id = (int)$this->get('santri_id', 0);

        $this->view('admin/kenaikan/form', compact('jenis', 'penempatan', 'pilihan_tujuan', 'selected_santri_id'));
    }

    public function simpan(): void {
        require_login();
        require_role(['diniyyah', 'tpq', 'admin_kelas']);

        if (!$this->is_post() || !verify_csrf()) {
            set_flash('error', 'Permintaan tidak valid.');
            redirect('kenaikan');
        }

        $role = current_user()['role'];
        $assignment = $role === 'admin_kelas' ? $this->class_assignment() : null;
        if ($role === 'admin_kelas' && !$assignment) {
            set_flash('error', 'Akun Anda belum ditugaskan ke kelas.');
            redirect('santri');
        }
        $jenis = $assignment['jenis_jenjang'] ?? ROLE_JENJANG_MAP[$role];
        $santri_id = (int)$this->post('santri_id');
        $tahun_ajaran = trim($this->post('tahun_ajaran'));
        $status = $this->post('status');
        $catatan = trim($this->post('catatan')) ?: null;
        $asal = $this->current_placement($santri_id, $jenis, $assignment);

        if (!$asal || $tahun_ajaran === '') {
            set_flash('error', 'Santri dan tahun ajaran wajib diisi. Santri harus sudah diterima pada program Anda.');
            redirect('kenaikan/tambah');
        }
        if (!in_array($status, ['naik', 'tetap', 'lulus'], true)) {
            set_flash('error', 'Status kenaikan kelas tidak valid.');
            redirect('kenaikan/tambah');
        }

        $tujuan = null;
        if ($status === 'naik') {
            $tujuan = $this->selected_target($jenis);
            if ($tujuan === null) {
                set_flash('error', 'Pilih tujuan kenaikan kelas yang tersedia.');
                redirect('kenaikan/tambah');
            }
        } elseif ($status === 'tetap') {
            $tujuan = ['jenjang' => $asal['jenjang'], 'kelas' => $asal['kelas']];
        }

        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO kenaikan_kelas
                    (santri_id, jenis_jenjang, jenjang_asal, kelas_asal, jenjang_tujuan, kelas_tujuan,
                     tahun_ajaran, status, catatan, diproses_oleh)
                 VALUES
                    (:santri_id, :jenis, :jenjang_asal, :kelas_asal, :jenjang_tujuan, :kelas_tujuan,
                     :tahun_ajaran, :status, :catatan, :diproses_oleh)'
            );
            $stmt->execute([
                ':santri_id' => $santri_id,
                ':jenis' => $jenis,
                ':jenjang_asal' => $asal['jenjang'],
                ':kelas_asal' => $asal['kelas'],
                ':jenjang_tujuan' => $tujuan['jenjang'] ?? null,
                ':kelas_tujuan' => $tujuan['kelas'] ?? null,
                ':tahun_ajaran' => $tahun_ajaran,
                ':status' => $status,
                ':catatan' => $catatan,
                ':diproses_oleh' => current_user()['id'],
            ]);
            set_flash('success', 'Kenaikan kelas berhasil dicatat.');
        } catch (PDOException $error) {
            if ((int)($error->errorInfo[1] ?? 0) === 1062) {
                set_flash('error', 'Kenaikan kelas santri ini untuk tahun ajaran tersebut sudah tercatat.');
            } else {
                set_flash('error', 'Kenaikan kelas gagal disimpan.');
            }
        }
        redirect('kenaikan');
    }

    public function hapus(?string $id = null): void {
        require_login();
        require_role(['diniyyah', 'tpq', 'admin_kelas']);

        if (!$this->is_post() || !verify_csrf() || !$id) {
            redirect('kenaikan');
        }

        $role = current_user()['role'];
        $assignment = $role === 'admin_kelas' ? $this->class_assignment() : null;
        if ($role === 'admin_kelas' && !$assignment) {
            redirect('santri');
        }
        $jenis = $assignment['jenis_jenjang'] ?? ROLE_JENJANG_MAP[$role];
        $stmt = $this->pdo->prepare(
            'SELECT id, santri_id FROM kenaikan_kelas WHERE id = :id AND jenis_jenjang = :jenis
             AND (:kelas_jenjang_check IS NULL OR jenjang_asal = :kelas_jenjang)
             AND (:kelas_nama_check IS NULL OR kelas_asal = :kelas_nama) LIMIT 1'
        );
        $stmt->execute([
            ':id' => $id,
            ':jenis' => $jenis,
            ':kelas_jenjang_check' => $assignment['jenjang'] ?? null,
            ':kelas_jenjang' => $assignment['jenjang'] ?? null,
            ':kelas_nama_check' => $assignment['kelas'] ?? null,
            ':kelas_nama' => $assignment['kelas'] ?? null,
        ]);
        $record = $stmt->fetch();
        if (!$record) {
            set_flash('error', 'Catatan kenaikan kelas tidak ditemukan atau bukan milik program Anda.');
            redirect('kenaikan');
        }

        $latest = $this->pdo->prepare(
            'SELECT id FROM kenaikan_kelas
             WHERE santri_id = :santri_id AND jenis_jenjang = :jenis
             ORDER BY created_at DESC, id DESC LIMIT 1'
        );
        $latest->execute([':santri_id' => $record['santri_id'], ':jenis' => $jenis]);
        if ((int)$latest->fetchColumn() !== (int)$record['id']) {
            set_flash('error', 'Hanya catatan kenaikan kelas terbaru yang dapat dihapus agar riwayat santri tetap konsisten.');
            redirect('kenaikan');
        }

        $this->pdo->prepare('DELETE FROM kenaikan_kelas WHERE id = :id AND jenis_jenjang = :jenis')
            ->execute([':id' => $record['id'], ':jenis' => $jenis]);
        set_flash('success', 'Catatan kenaikan kelas berhasil dihapus.');
        redirect('kenaikan');
    }

    private function current_placements(string $jenis, ?array $assignment = null): array {
        $stmt = $this->pdo->prepare(
            "SELECT s.id AS santri_id, s.nama_lengkap, s.nis_pesantren,
                    pj.jenjang_dipilih, pj.tingkat_dipilih,
                    kk.jenjang_tujuan, kk.kelas_tujuan, kk.status AS kenaikan_status
             FROM pendaftaran_jenjang pj
             JOIN santri s ON s.id = pj.santri_id
             LEFT JOIN kenaikan_kelas kk ON kk.id = (
                 SELECT history.id
                 FROM kenaikan_kelas history
                 WHERE history.santri_id = pj.santri_id
                   AND history.jenis_jenjang = pj.jenis_jenjang
                 ORDER BY history.created_at DESC, history.id DESC
                 LIMIT 1
             )
             WHERE pj.jenis_jenjang = :jenis
               AND pj.status = 'diterima'
               AND (kk.status IS NULL OR kk.status <> 'lulus')
             ORDER BY s.nama_lengkap ASC"
        );
        $stmt->execute([':jenis' => $jenis]);

        $placements = array_map(static function (array $row): array {
            $row['jenjang'] = $row['jenjang_tujuan'] ?? $row['jenjang_dipilih'];
            $row['kelas'] = $row['kelas_tujuan'] ?? $row['tingkat_dipilih'];
            return $row;
        }, $stmt->fetchAll());
        if ($assignment === null) {
            return $placements;
        }
        return array_values(array_filter($placements, static function (array $placement) use ($assignment): bool {
            return $placement['jenjang'] === $assignment['jenjang']
                && ($assignment['kelas'] === null || $placement['kelas'] === $assignment['kelas']);
        }));
    }

    private function current_placement(int $santri_id, string $jenis, ?array $assignment = null): ?array {
        foreach ($this->current_placements($jenis, $assignment) as $placement) {
            if ((int)$placement['santri_id'] === $santri_id) {
                return ['jenjang' => $placement['jenjang'], 'kelas' => $placement['kelas']];
            }
        }
        return null;
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

    private function target_options(string $jenis): array {
        if ($jenis === 'diniyyah') {
            $rows = $this->pdo->query(
                "SELECT dk.id, dj.nama AS jenjang, dk.nama AS kelas
                 FROM diniyyah_kelas dk
                 JOIN diniyyah_jenjang dj ON dj.id = dk.jenjang_id
                 WHERE dk.aktif = 1 AND dj.aktif = 1
                 ORDER BY dj.urutan ASC, dj.nama ASC, dk.urutan ASC, dk.nama ASC"
            )->fetchAll();
            foreach ($rows as &$row) {
                $row['label'] = $row['jenjang'] . ' — ' . $row['kelas'];
            }
            unset($row);
            return $rows;
        }

        $rows = $this->pdo->query(
            "SELECT nama FROM tingkatan_tpq WHERE aktif = 1 ORDER BY urutan ASC, nama ASC"
        )->fetchAll();
        foreach ($rows as &$row) {
            $row['jenjang'] = $row['nama'];
            $row['kelas'] = null;
            $row['label'] = $row['nama'];
        }
        unset($row);
        return $rows;
    }

    private function selected_target(string $jenis): ?array {
        if ($jenis === 'diniyyah') {
            $kelas_id = (int)$this->post('kelas_tujuan_id');
            if ($kelas_id < 1) {
                return null;
            }
            $stmt = $this->pdo->prepare(
                "SELECT dj.nama AS jenjang, dk.nama AS kelas
                 FROM diniyyah_kelas dk
                 JOIN diniyyah_jenjang dj ON dj.id = dk.jenjang_id
                 WHERE dk.id = :id AND dk.aktif = 1 AND dj.aktif = 1
                 LIMIT 1"
            );
            $stmt->execute([':id' => $kelas_id]);
            return $stmt->fetch() ?: null;
        }

        $tingkatan = trim($this->post('tingkatan_tujuan'));
        if ($tingkatan === '') {
            return null;
        }
        $stmt = $this->pdo->prepare(
            'SELECT nama FROM tingkatan_tpq WHERE nama = :nama AND aktif = 1 LIMIT 1'
        );
        $stmt->execute([':nama' => $tingkatan]);
        if (!$stmt->fetchColumn()) {
            return null;
        }
        return ['jenjang' => $tingkatan, 'kelas' => null];
    }
}
