<?php
require_once ROOT_PATH . '/app/controllers/BaseController.php';

// ============================================================
// SURAT CONTROLLER
// Cetak teks/surat resmi: penerimaan santri, kenaikan kelas, kelulusan
// ============================================================
class SuratController extends BaseController {

    public function penerimaan(?string $id = null): void {
        require_login();
        require_role(['diniyyah', 'tpq', 'webmaster']);
        if (!$id) { redirect('pendaftaran'); }

        $stmt = $this->pdo->prepare("
            SELECT pj.*, s.nama_lengkap, s.jenis_kelamin, s.tempat_lahir, s.tanggal_lahir,
                   s.nama_wali, s.nama_ayah, s.hubungan_wali, s.no_hp_wali,
                   u.nama as validator_nama
            FROM pendaftaran_jenjang pj
            JOIN santri s ON pj.santri_id = s.id
            LEFT JOIN users u ON pj.divalidasi_oleh = u.id
            WHERE pj.id = :id LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $item = $stmt->fetch();
        if (!$item) { redirect('pendaftaran'); }

        $role = current_user()['role'];
        if ($role !== 'webmaster' && $item['jenis_jenjang'] !== ROLE_JENJANG_MAP[$role]) {
            set_flash('error', 'Anda tidak berhak mencetak surat ini.');
            redirect('pendaftaran');
        }
        if ($item['status'] !== 'diterima') {
            set_flash('error', 'Surat penerimaan hanya dapat dicetak untuk pendaftaran yang berstatus Diterima.');
            redirect('pendaftaran/detail/' . $id);
        }

        $this->view('surat/penerimaan', compact('item'), 'print');
    }

    public function kenaikan(?string $id = null): void {
        require_login();
        require_role(['diniyyah', 'tpq', 'webmaster', 'admin_kelas']);
        if (!$id) { redirect('kenaikan'); }

        $stmt = $this->pdo->prepare("
            SELECT kk.*, s.nama_lengkap, s.jenis_kelamin, s.nis_pesantren, s.nama_wali, s.nama_ayah,
                   u.nama as pemroses_nama
            FROM kenaikan_kelas kk
            JOIN santri s ON s.id = kk.santri_id
            LEFT JOIN users u ON u.id = kk.diproses_oleh
            WHERE kk.id = :id LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $item = $stmt->fetch();
        if (!$item) { redirect('kenaikan'); }

        $role = current_user()['role'];
        if ($role === 'admin_kelas') {
            $assignment = $this->class_assignment();
            if (!$assignment
                || $item['jenis_jenjang'] !== $assignment['jenis_jenjang']
                || $item['jenjang_asal'] !== $assignment['jenjang']
                || ($assignment['kelas'] !== null && $item['kelas_asal'] !== $assignment['kelas'])
            ) {
                set_flash('error', 'Anda tidak berhak mencetak surat ini.');
                redirect('kenaikan');
            }
        } elseif ($role !== 'webmaster' && $item['jenis_jenjang'] !== ROLE_JENJANG_MAP[$role]) {
            set_flash('error', 'Anda tidak berhak mencetak surat ini.');
            redirect('kenaikan');
        }

        $this->view('surat/kenaikan', compact('item'), 'print');
    }

    public function kelulusan(?string $id = null): void {
        require_login();
        require_role(['diniyyah', 'tpq', 'webmaster']);
        if (!$id) { redirect('kelulusan'); }

        $stmt = $this->pdo->prepare("
            SELECT k.*, s.nama_lengkap, s.jenis_kelamin, s.nis_pesantren, s.tempat_lahir, s.tanggal_lahir,
                   s.nama_wali, s.nama_ayah,
                   u.nama as penetap_nama
            FROM kelulusan k
            JOIN santri s ON s.id = k.santri_id
            LEFT JOIN users u ON u.id = k.ditetapkan_oleh
            WHERE k.id = :id LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $item = $stmt->fetch();
        if (!$item) { redirect('kelulusan'); }

        $role = current_user()['role'];
        if ($role !== 'webmaster' && $item['jenis_jenjang'] !== ROLE_JENJANG_MAP[$role]) {
            set_flash('error', 'Anda tidak berhak mencetak surat ini.');
            redirect('kelulusan');
        }

        $this->view('surat/kelulusan', compact('item'), 'print');
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
}
