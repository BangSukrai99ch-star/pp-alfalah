<?php
require_once ROOT_PATH . '/app/controllers/BaseController.php';

// ============================================================
// PUBLIC CONTROLLER (Frontend Website)
// ============================================================
class PublicController extends BaseController {

    public function home(): void {
        $berita = $this->pdo->query(
            "SELECT * FROM kegiatan WHERE tipe='berita' AND status='publish' ORDER BY created_at DESC LIMIT 6"
        )->fetchAll();

        $galeri = $this->pdo->query(
            "SELECT * FROM kegiatan WHERE tipe='galeri' AND status='publish' ORDER BY created_at DESC LIMIT 8"
        )->fetchAll();

        $slider = $this->pdo->query(
            "SELECT * FROM slider WHERE aktif=1 ORDER BY urutan ASC LIMIT 5"
        )->fetchAll();

        $santri_diterima = (int)$this->pdo->query(
            "SELECT COUNT(DISTINCT santri_id) FROM pendaftaran_jenjang WHERE status='diterima'"
        )->fetchColumn();

        $this->view('public/home', compact('berita', 'galeri', 'slider', 'santri_diterima'), 'public');
    }

    public function berita(?string $slug = null): void {
        if ($slug) {
            $stmt = $this->pdo->prepare(
                "SELECT k.*, u.nama as penulis FROM kegiatan k 
                 LEFT JOIN users u ON k.dibuat_oleh = u.id
                 WHERE k.slug = :slug AND k.status='publish' LIMIT 1"
            );
            $stmt->execute([':slug' => $slug]);
            $item = $stmt->fetch();
            if (!$item) { $this->error404(); return; }
            // Update views
            $this->pdo->prepare("UPDATE kegiatan SET views = views+1 WHERE slug = :s")
                      ->execute([':s' => $slug]);
            $berita_lainnya = $this->pdo->query(
                "SELECT * FROM kegiatan WHERE tipe IN ('berita','artikel') AND status='publish'
                 ORDER BY created_at DESC LIMIT 5"
            )->fetchAll();
            $this->view('public/berita_detail', compact('item', 'berita_lainnya'), 'public');
        } else {
            $tipe = $this->get('tipe', 'berita');
            $page = (int)$this->get('page', 1);

            $total_stmt = $this->pdo->prepare("SELECT COUNT(*) FROM kegiatan WHERE tipe = :tipe AND status = 'publish'");
            $total_stmt->execute([':tipe' => $tipe]);
            $total = (int)$total_stmt->fetchColumn();

            $stmt = $this->pdo->prepare(
                "SELECT * FROM kegiatan WHERE tipe = :tipe AND status = 'publish' ORDER BY created_at DESC LIMIT :limit OFFSET :offset"
            );
            $pg = paginate($total, $page);
            $stmt->bindValue(':tipe', $tipe, PDO::PARAM_STR);
            $stmt->bindValue(':limit', $pg['per_page'], PDO::PARAM_INT);
            $stmt->bindValue(':offset', $pg['offset'], PDO::PARAM_INT);
            $stmt->execute();
            $items = $stmt->fetchAll();
            $this->view('public/berita_list', compact('items', 'pg', 'tipe'), 'public');
        }
    }

    public function galeri(): void {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM kegiatan WHERE tipe='galeri' AND status='publish' ORDER BY created_at DESC LIMIT 30"
        );
        $stmt->execute();
        $items = $stmt->fetchAll();
        $this->view('public/galeri', compact('items'), 'public');
    }

    public function profil(): void {
        $this->view('public/profil', [], 'public');
    }

    public function kontak(): void {
        $sent = false;
        $this->view('public/kontak', compact('sent'), 'public');
    }

    public function pendaftaran(): void {
        // Cek apakah PSB aktif
        $psb_aktif = (bool)($this->pengaturan['psb_aktif'] ?? false);
        $psb_jenjang = [
            'diniyyah' => $psb_aktif && (bool)($this->pengaturan['psb_diniyyah_aktif'] ?? true),
            'tpq' => $psb_aktif && (bool)($this->pengaturan['psb_tpq_aktif'] ?? true),
        ];

        $tingkatan_tpq = $this->pdo->query(
            "SELECT * FROM tingkatan_tpq WHERE aktif=1 ORDER BY urutan ASC"
        )->fetchAll();
        $kelas_diniyyah = $this->pdo->query(
            "SELECT dk.id, dk.nama AS nama_kelas, dj.nama AS nama_jenjang
             FROM diniyyah_kelas dk
             JOIN diniyyah_jenjang dj ON dj.id = dk.jenjang_id
             WHERE dk.aktif=1 AND dj.aktif=1
             ORDER BY dj.urutan ASC, dj.nama ASC, dk.urutan ASC, dk.nama ASC"
        )->fetchAll();

        $success = null;
        $errors  = [];

        if ($this->is_post() && $psb_aktif) {
            [$success, $errors] = $this->proses_pendaftaran($tingkatan_tpq, $kelas_diniyyah, $psb_jenjang);
        }

        $this->view('public/pendaftaran', compact('psb_aktif', 'psb_jenjang', 'tingkatan_tpq', 'kelas_diniyyah', 'success', 'errors'), 'public');
    }

    private function proses_pendaftaran(array $tingkatan_tpq, array $kelas_diniyyah, array $psb_jenjang): array {
        $errors = [];

        // Validasi CSRF
        if (!verify_csrf()) {
            return [null, ['Permintaan tidak valid. Silakan muat ulang halaman.']];
        }

        // Ambil data
        $nama          = trim($this->post('nama_lengkap'));
        $jk            = $this->post('jenis_kelamin');
        $ttl_tempat    = trim($this->post('tempat_lahir'));
        $ttl_tanggal   = $this->post('tanggal_lahir');
        $alamat        = trim($this->post('alamat'));
        $nama_ayah     = trim($this->post('nama_ayah'));
        $nama_ibu      = trim($this->post('nama_ibu'));
        $no_hp         = trim($this->post('no_hp_wali'));
        $email_wali    = trim($this->post('email_wali'));
        $daftar_diniyyah   = !empty($this->post('daftar_diniyyah'));
        $kelas_diniyyah_id = (int)$this->post('kelas_diniyyah_id', 0);
        $daftar_tpq        = !empty($this->post('daftar_tpq'));
        $tingkat_tpq       = $this->post('tingkat_tpq');

        // Validasi wajib
        if (empty($nama))     $errors[] = 'Nama lengkap wajib diisi.';
        if (empty($jk))       $errors[] = 'Jenis kelamin wajib dipilih.';
        if (empty($ttl_tanggal)) $errors[] = 'Tanggal lahir wajib diisi.';
        if (empty($alamat))   $errors[] = 'Alamat wajib diisi.';
        if (empty($no_hp))    $errors[] = 'Nomor HP wali wajib diisi.';

        $tingkatan_resmi = array_column($tingkatan_tpq, 'nama');
        $kelas_diniyyah_dipilih = null;
        foreach ($kelas_diniyyah as $kelas) {
            if ((int)$kelas['id'] === $kelas_diniyyah_id) {
                $kelas_diniyyah_dipilih = $kelas;
                break;
            }
        }

        if (!$psb_jenjang['diniyyah']) {
            $daftar_diniyyah = false;
            $kelas_diniyyah_id = 0;
            $kelas_diniyyah_dipilih = null;
        } elseif (!$daftar_diniyyah) {
            $kelas_diniyyah_id = 0;
            $kelas_diniyyah_dipilih = null;
        }
        if (!$psb_jenjang['tpq']) {
            $daftar_tpq = false;
            $tingkat_tpq = null;
        }

        // Minimal satu pilihan yang valid harus benar-benar dibuat.
        $ada_jenjang = $daftar_diniyyah || $daftar_tpq;
        if (!$ada_jenjang) $errors[] = 'Pilih minimal satu program pendidikan.';
        if ($daftar_diniyyah && !$kelas_diniyyah_dipilih) {
            $errors[] = 'Pilih kelas Diniyyah yang tersedia.';
        }
        if ($daftar_tpq && !in_array($tingkat_tpq, $tingkatan_resmi, true)) {
            $errors[] = 'Pilih tingkatan TPQ yang tersedia.';
        }

        if (!empty($errors)) return [null, $errors];

        try {
            $this->pdo->beginTransaction();

            // Insert santri induk
            $stmt = $this->pdo->prepare("
                INSERT INTO santri (nama_lengkap, jenis_kelamin, tempat_lahir, tanggal_lahir,
                    alamat, nama_ayah, nama_ibu, no_hp_wali, email_wali, status)
                VALUES (:nl, :jk, :ttl, :tgl, :al, :ay, :ib, :hp, :em, 'aktif')
            ");
            $stmt->execute([
                ':nl'  => $nama,
                ':jk'  => $jk,
                ':ttl' => $ttl_tempat,
                ':tgl' => $ttl_tanggal,
                ':al'  => $alamat,
                ':ay'  => $nama_ayah,
                ':ib'  => $nama_ibu,
                ':hp'  => $no_hp,
                ':em'  => $email_wali,
            ]);
            $santri_id = $this->pdo->lastInsertId();
            $tahun_ajaran = $this->pengaturan['psb_tahun_ajaran'] ?? date('Y') . '/' . (date('Y') + 1);

            $ins_pj = $this->pdo->prepare("
                INSERT INTO pendaftaran_jenjang (santri_id, jenis_jenjang, jenjang_dipilih, tingkat_dipilih, tahun_ajaran, status)
                VALUES (:sid, :jj, :jd, :td, :ta, 'pending')
            ");

            // Simpan nama jenjang dan kelas agar riwayat pendaftaran tetap utuh saat struktur diubah.
            if ($kelas_diniyyah_dipilih) {
                $ins_pj->execute([
                    ':sid' => $santri_id,
                    ':jj' => 'diniyyah',
                    ':jd' => $kelas_diniyyah_dipilih['nama_jenjang'],
                    ':td' => $kelas_diniyyah_dipilih['nama_kelas'],
                    ':ta' => $tahun_ajaran,
                ]);
            }

            // Insert TPQ
            if ($daftar_tpq && !empty($tingkat_tpq)) {
                $ins_pj->execute([':sid'=>$santri_id,':jj'=>'tpq',':jd'=>$tingkat_tpq,':td'=>null,':ta'=>$tahun_ajaran]);
            }

            $this->pdo->commit();
            return [['santri_id' => $santri_id, 'nama' => $nama], []];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return [null, ['Terjadi kesalahan sistem. Silakan coba lagi.']];
        }
    }

    public function error404(): void {
        http_response_code(404);
        $this->view('public/404', [], 'public');
    }
}
