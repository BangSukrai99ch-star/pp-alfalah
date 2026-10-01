<?php
require_once ROOT_PATH . '/app/controllers/BaseController.php';

// ============================================================
// DASHBOARD CONTROLLER
// ============================================================
class DashboardController extends BaseController
{

    public function index(): void
    {
        require_login();
        $user = current_user();
        $role = $user['role'];
        if ($role === 'admin_kelas') {
            $this->redirect('santri');
        }
        if (!in_array($role, ['webmaster', 'kegiatan', 'diniyyah', 'tpq'], true)) {
            set_flash('error', 'Akun pengguna tidak memiliki akses ke dashboard admin.');
            $this->redirect('');
        }

        // Stats
        $stats = [];
        // Total santri
        $stats['total_santri'] = $this->pdo->query("SELECT COUNT(*) FROM santri")->fetchColumn();

        if ($role === 'webmaster') {
            $stats['total_users'] = $this->pdo->query("SELECT COUNT(*) FROM users WHERE role <> 'user'")->fetchColumn();
            $stats['total_kegiatan'] = $this->pdo->query("SELECT COUNT(*) FROM kegiatan")->fetchColumn();
            $stats['pending_all'] = $this->pdo->query(
                "SELECT COUNT(*) FROM pendaftaran_jenjang WHERE status='pending'"
            )->fetchColumn();
            $stats['total_kelulusan'] = $this->pdo->query(
                "SELECT COUNT(*) FROM kelulusan WHERE status='lulus'"
            )->fetchColumn();
        } elseif ($role === 'kegiatan') {
            $stats['total_berita'] = $this->pdo->query(
                "SELECT COUNT(*) FROM kegiatan WHERE tipe='berita'"
            )->fetchColumn();
            $stats['total_artikel'] = $this->pdo->query(
                "SELECT COUNT(*) FROM kegiatan WHERE tipe='artikel'"
            )->fetchColumn();
            $stats['total_galeri'] = $this->pdo->query(
                "SELECT COUNT(*) FROM kegiatan WHERE tipe='galeri'"
            )->fetchColumn();
        } elseif (in_array($role, ['diniyyah', 'tpq'])) {
            $jenjang = ROLE_JENJANG_MAP[$role];
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM pendaftaran_jenjang WHERE jenis_jenjang = :j AND status = 'pending'"
            );
            $stmt->execute([':j' => $jenjang]);
            $stats['pending'] = $stmt->fetchColumn();

            $stmt2 = $this->pdo->prepare(
                "SELECT COUNT(*) FROM pendaftaran_jenjang WHERE jenis_jenjang = :j AND status = 'diterima'"
            );
            $stmt2->execute([':j' => $jenjang]);
            $stats['diterima'] = $stmt2->fetchColumn();

            $stmt3 = $this->pdo->prepare(
                "SELECT COUNT(*) FROM pendaftaran_jenjang WHERE jenis_jenjang = :j AND status = 'ditolak'"
            );
            $stmt3->execute([':j' => $jenjang]);
            $stats['ditolak'] = $stmt3->fetchColumn();

            $stmt4 = $this->pdo->prepare(
                "SELECT COUNT(*) FROM kelulusan WHERE jenis_jenjang = :j AND status = 'lulus'"
            );
            $stmt4->execute([':j' => $jenjang]);
            $stats['lulus'] = $stmt4->fetchColumn();
        }

        // Berita terbaru (untuk semua)
        $recent_kegiatan = $this->pdo->query(
            "SELECT k.*, u.nama as admin_nama FROM kegiatan k 
             LEFT JOIN users u ON k.dibuat_oleh = u.id
             ORDER BY k.created_at DESC LIMIT 5"
        )->fetchAll();

        // Pendaftaran terbaru (untuk jenjang admin)
        $recent_daftar = [];
        if (in_array($role, ['diniyyah', 'tpq', 'webmaster'])) {
            $jenjang_filter = ($role === 'webmaster') ? null : ROLE_JENJANG_MAP[$role];
            $sql = "SELECT pj.*, s.nama_lengkap, s.jenis_kelamin, s.no_hp_wali
                    FROM pendaftaran_jenjang pj
                    JOIN santri s ON pj.santri_id = s.id";
            if ($jenjang_filter) {
                $sql .= " WHERE pj.jenis_jenjang = '{$jenjang_filter}'";
            }
            $sql .= " ORDER BY pj.created_at DESC LIMIT 5";
            $recent_daftar = $this->pdo->query($sql)->fetchAll();
        }

        $this->view('admin/dashboard/index', compact('stats', 'recent_kegiatan', 'recent_daftar', 'role'));
    }

    // Alias
    public function home(): void
    {
        $this->index();
    }
}
