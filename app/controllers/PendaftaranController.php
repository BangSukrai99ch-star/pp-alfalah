<?php
require_once ROOT_PATH . '/app/controllers/BaseController.php';

// ============================================================
// PENDAFTARAN CONTROLLER (Admin - Validasi PSB)
// ============================================================
class PendaftaranController extends BaseController {

    public function index(?string $param = null): void {
        require_login();
        require_role(['diniyyah', 'tpq', 'webmaster']);

        $role    = current_user()['role'];
        $jenjang = ($role === 'webmaster') ? null : ROLE_JENJANG_MAP[$role];

        $filter_status = $this->get('status', '');
        $filter_search = trim($this->get('q', ''));
        $page          = (int)$this->get('page', 1);

        // Build query
        $params = [];
        $where  = ['1=1'];

        if ($jenjang) {
            $where[] = 'pj.jenis_jenjang = :jenjang';
            $params[':jenjang'] = $jenjang;
        }
        if (!empty($filter_status)) {
            $where[] = 'pj.status = :status';
            $params[':status'] = $filter_status;
        }
        if (!empty($filter_search)) {
            $where[] = '(s.nama_lengkap LIKE :q1 OR s.no_hp_wali LIKE :q2)';
            $params[':q1'] = $params[':q2'] = '%' . $filter_search . '%';
        }

        $where_sql = implode(' AND ', $where);

        // Count
        $cnt = $this->pdo->prepare("
            SELECT COUNT(*) FROM pendaftaran_jenjang pj
            JOIN santri s ON pj.santri_id = s.id
            WHERE {$where_sql}
        ");
        $cnt->execute($params);
        $total = (int)$cnt->fetchColumn();

        $pg = paginate($total, $page);

        // Fetch
        $stmt = $this->pdo->prepare("
            SELECT pj.*, s.nama_lengkap, s.jenis_kelamin, s.tanggal_lahir,
                   s.no_hp_wali, s.email_wali, s.nama_ayah,
                   u.nama as validator_nama
            FROM pendaftaran_jenjang pj
            JOIN santri s ON pj.santri_id = s.id
            LEFT JOIN users u ON pj.divalidasi_oleh = u.id
            WHERE {$where_sql}
            ORDER BY FIELD(pj.status,'pending','diterima','ditolak'), pj.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit',  $pg['per_page'], PDO::PARAM_INT);
        $stmt->bindValue(':offset', $pg['offset'],   PDO::PARAM_INT);
        $stmt->execute();
        $data_daftar = $stmt->fetchAll();

        // Summary counts
        $summary = $this->get_summary_counts($jenjang);

        $this->view('admin/pendaftaran/index', compact(
            'data_daftar', 'pg', 'summary', 'filter_status', 'filter_search', 'role', 'jenjang'
        ));
    }

    public function detail(?string $id = null): void {
        require_login();
        require_role(['diniyyah', 'tpq', 'webmaster']);

        if (!$id) { redirect('pendaftaran'); }

        $stmt = $this->pdo->prepare("
            SELECT pj.*, s.*, u.nama as validator_nama
            FROM pendaftaran_jenjang pj
            JOIN santri s ON pj.santri_id = s.id
            LEFT JOIN users u ON pj.divalidasi_oleh = u.id
            WHERE pj.id = :id LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $item = $stmt->fetch();

        if (!$item) { redirect('pendaftaran'); }

        // Cek otorisasi role
        $role = current_user()['role'];
        if ($role !== 'webmaster') {
            $allowed_jenjang = ROLE_JENJANG_MAP[$role];
            if ($item['jenis_jenjang'] !== $allowed_jenjang) {
                set_flash('error', 'Anda tidak berhak mengakses data ini.');
                redirect('pendaftaran');
            }
        }

        // Semua pendaftaran santri yang sama
        $all_pendaftaran = $this->pdo->prepare("
            SELECT * FROM pendaftaran_jenjang WHERE santri_id = :sid
        ");
        $all_pendaftaran->execute([':sid' => $item['santri_id']]);
        $all_daftar = $all_pendaftaran->fetchAll();

        $this->view('admin/pendaftaran/detail', compact('item', 'all_daftar'));
    }

    public function validasi(?string $id = null): void {
        require_login();
        require_role(['diniyyah', 'tpq', 'webmaster']);

        if (!$this->is_post() || !$id) { redirect('pendaftaran'); }
        if (!verify_csrf()) {
            set_flash('error', 'Permintaan tidak valid.');
            redirect('pendaftaran');
        }

        $status  = $this->post('status');
        $catatan = trim($this->post('catatan_admin'));

        if (!in_array($status, ['diterima', 'ditolak'])) {
            set_flash('error', 'Status tidak valid.');
            redirect('pendaftaran');
        }

        // Verifikasi kepemilikan role
        $stmt = $this->pdo->prepare("SELECT * FROM pendaftaran_jenjang WHERE id=:id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $pj = $stmt->fetch();

        if (!$pj) { redirect('pendaftaran'); }

        $role = current_user()['role'];
        if ($role !== 'webmaster') {
            $allowed_jenjang = ROLE_JENJANG_MAP[$role];
            if ($pj['jenis_jenjang'] !== $allowed_jenjang) {
                set_flash('error', 'Anda tidak berhak memvalidasi data ini.');
                redirect('pendaftaran');
            }
        }

        $upd = $this->pdo->prepare("
            UPDATE pendaftaran_jenjang
            SET status=:st, catatan_admin=:ca, divalidasi_oleh=:uid, validated_at=NOW()
            WHERE id=:id
        ");
        $upd->execute([
            ':st'  => $status,
            ':ca'  => $catatan,
            ':uid' => current_user()['id'],
            ':id'  => $id,
        ]);

        $label = $status === 'diterima' ? 'Diterima' : 'Ditolak';
        set_flash('success', "Status pendaftaran berhasil diubah menjadi: {$label}.");
        redirect('pendaftaran/detail/' . $id);
    }

    public function export(): void {
        require_login();
        require_role(['diniyyah', 'tpq', 'webmaster']);

        $role    = current_user()['role'];
        $jenjang = ($role === 'webmaster') ? null : ROLE_JENJANG_MAP[$role];

        $filter_status = $this->get('status', '');
        $filter_search = trim($this->get('q', ''));

        $params = [];
        $where  = ['1=1'];

        if ($jenjang) {
            $where[] = 'pj.jenis_jenjang = :jenjang';
            $params[':jenjang'] = $jenjang;
        }
        if (!empty($filter_status)) {
            $where[] = 'pj.status = :status';
            $params[':status'] = $filter_status;
        }
        if (!empty($filter_search)) {
            $where[] = '(s.nama_lengkap LIKE :q1 OR s.no_hp_wali LIKE :q2)';
            $params[':q1'] = $params[':q2'] = '%' . $filter_search . '%';
        }
        $where_sql = implode(' AND ', $where);

        $stmt = $this->pdo->prepare("
            SELECT pj.*, s.nama_lengkap, s.jenis_kelamin, s.tanggal_lahir,
                   s.no_hp_wali, s.email_wali, s.nama_ayah, s.nama_wali,
                   u.nama as validator_nama
            FROM pendaftaran_jenjang pj
            JOIN santri s ON pj.santri_id = s.id
            LEFT JOIN users u ON pj.divalidasi_oleh = u.id
            WHERE {$where_sql}
            ORDER BY pj.created_at DESC
        ");
        $stmt->execute($params);
        $data = $stmt->fetchAll();

        $headers = [
            'No', 'Nama Santri', 'Jenis Kelamin', 'Nama Wali', 'No HP Wali', 'Email Wali',
            'Program', 'Jenjang/Tingkat Dipilih', 'Tahun Ajaran', 'Status', 'Catatan Admin',
            'Divalidasi Oleh', 'Tanggal Validasi', 'Tanggal Daftar',
        ];
        $rows = [];
        foreach ($data as $i => $row) {
            $rows[] = [
                $i + 1,
                $row['nama_lengkap'],
                $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan',
                $row['nama_wali'] ?: $row['nama_ayah'],
                $row['no_hp_wali'],
                $row['email_wali'],
                ucfirst($row['jenis_jenjang']),
                $row['jenjang_dipilih'] . ($row['tingkat_dipilih'] ? ' - ' . $row['tingkat_dipilih'] : ''),
                $row['tahun_ajaran'],
                ucfirst($row['status']),
                $row['catatan_admin'],
                $row['validator_nama'],
                $row['validated_at'],
                $row['created_at'],
            ];
        }

        $nama_file = 'pendaftaran_' . ($jenjang ?: 'semua') . '_' . date('Ymd_His') . '.xls';
        export_excel($nama_file, $headers, $rows);
    }

    private function get_summary_counts(?string $jenjang): array {
        $where = $jenjang ? "jenis_jenjang = " . $this->pdo->quote($jenjang) . " AND " : "";
        return [
            'pending'  => (int)$this->pdo->query("SELECT COUNT(*) FROM pendaftaran_jenjang WHERE {$where} status='pending'")->fetchColumn(),
            'diterima' => (int)$this->pdo->query("SELECT COUNT(*) FROM pendaftaran_jenjang WHERE {$where} status='diterima'")->fetchColumn(),
            'ditolak'  => (int)$this->pdo->query("SELECT COUNT(*) FROM pendaftaran_jenjang WHERE {$where} status='ditolak'")->fetchColumn(),
            'total'    => (int)$this->pdo->query("SELECT COUNT(*) FROM pendaftaran_jenjang" . ($jenjang ? " WHERE jenis_jenjang=" . $this->pdo->quote($jenjang) : ""))->fetchColumn(),
        ];
    }
}
