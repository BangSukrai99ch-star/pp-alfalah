<?php
require_once ROOT_PATH . '/app/controllers/BaseController.php';

// ============================================================
// PENGATURAN CONTROLLER (Settings & TPQ Tingkatan)
// ============================================================
class PengaturanController extends BaseController {

    // --- Website Settings (Webmaster only) ---
    public function index(): void {
        require_login();
        require_role('webmaster');

        if ($this->is_post()) {
            if (!verify_csrf()) { set_flash('error','Token tidak valid.'); redirect('pengaturan'); }
            $this->simpan_pengaturan_web();
            return;
        }
        $settings = $this->pengaturan;
        $this->view('admin/pengaturan/web', compact('settings'));
    }

    private function simpan_pengaturan_web(): void {
        $fields = ['nama_pesantren','tagline','alamat','telepon','email','visi','misi','sejarah',
                   'pengumuman','beranda_sambutan','tentang_konten','publikasi_konten',
                   'psb_aktif','psb_tahun_ajaran','psb_keterangan',
                   'footer_text','facebook_url','instagram_url','youtube_url','maps_embed'];

        $data = [];
        $sets = [];
        foreach ($fields as $f) {
            $data[":$f"] = ($f === 'psb_aktif') ? (int)!empty($this->post($f)) : trim($this->post($f));
            $sets[] = "`$f` = :$f";
        }

        // Upload logo
        if (!empty($_FILES['logo']['name'])) {
            $up = upload_file($_FILES['logo'], 'settings');
            if ($up['success']) {
                delete_file($this->pengaturan['logo'] ?? null);
                $data[':logo'] = $up['path'];
                $sets[] = '`logo` = :logo';
            }
        }

        // Upload hero bg
        if (!empty($_FILES['hero_bg']['name'])) {
            $up = upload_file($_FILES['hero_bg'], 'settings');
            if ($up['success']) {
                $data[':hero_bg'] = $up['path'];
                $sets[] = '`hero_bg` = :hero_bg';
            }
        }

        set_flash('success', 'Pengaturan website berhasil disimpan.');
        redirect('pengaturan');
    }

    // --- Pengaturan PSB (buka/tutup) ---
    public function psb(): void {
        require_login();
        require_role('webmaster');

        if ($this->is_post() && verify_csrf()) {
            $psb_aktif = (int)!empty($this->post('psb_aktif'));
            $this->pdo->prepare("UPDATE pengaturan_web SET psb_aktif=:v")->execute([':v'=>$psb_aktif]);
            $label = $psb_aktif ? 'dibuka' : 'ditutup';
            set_flash('success', "Pendaftaran Santri Baru telah {$label}.");
        }
        redirect('pengaturan');
    }

    public function psb_jenjang(): void {
        require_login();
        require_role(['diniyyah', 'tpq']);

        $role = current_user()['role'];
        $field = 'psb_' . $role . '_aktif';
        if ($this->is_post() && verify_csrf()) {
            $value = (int)!empty($this->post('aktif'));
            $this->pdo->prepare("UPDATE pengaturan_web SET `$field`=:value")->execute([':value' => $value]);
            set_flash('success', 'Pendaftaran ' . ucfirst($role) . ' berhasil ' . ($value ? 'dibuka.' : 'ditutup.'));
            redirect('pengaturan/psb_jenjang');
        }
        $aktif = !empty($this->pengaturan[$field]);
        $this->view('admin/pengaturan/psb_jenjang', compact('role', 'aktif'));
    }

    // --- Tingkatan TPQ (TPQ Admin only) ---
    public function tpq(): void {
        require_login();
        require_role('tpq');

        $tingkatan = $this->pdo->query(
            "SELECT * FROM tingkatan_tpq ORDER BY urutan ASC"
        )->fetchAll();

        $this->view('admin/pengaturan/tpq', compact('tingkatan'));
    }

    public function tpq_simpan(): void {
        require_login();
        require_role('tpq');
        if (!$this->is_post() || !verify_csrf()) { redirect('pengaturan/tpq'); }

        $nama       = $this->post('nama', []);
        $urutan     = $this->post('urutan', []);
        $keterangan = $this->post('keterangan', []);
        $aktif      = $this->post('aktif', []);
        $ids        = $this->post('id', []);

        foreach ($ids as $i => $id) {
            if ($id === 'new') {
                if (!empty($nama[$i])) {
                    $this->pdo->prepare("INSERT INTO tingkatan_tpq (nama,urutan,keterangan,aktif) VALUES (:n,:u,:k,:a)")
                              ->execute([':n'=>$nama[$i],':u'=>(int)($urutan[$i]??0),':k'=>$keterangan[$i]??'',':a'=>isset($aktif[$i])?1:0]);
                }
            } else {
                $this->pdo->prepare("UPDATE tingkatan_tpq SET nama=:n, urutan=:u, keterangan=:k, aktif=:a WHERE id=:id")
                          ->execute([':n'=>$nama[$i],':u'=>(int)($urutan[$i]??0),':k'=>$keterangan[$i]??'',':a'=>isset($aktif[$i])?1:0,':id'=>$id]);
            }
        }
        set_flash('success', 'Tingkatan TPQ berhasil disimpan.');
        redirect('pengaturan/tpq');
    }

    public function tpq_hapus(?string $id = null): void {
        require_login();
        require_role('tpq');
        if ($this->is_post() && verify_csrf() && $id) {
            $this->pdo->prepare("DELETE FROM tingkatan_tpq WHERE id=:id")->execute([':id'=>$id]);
            set_flash('success', 'Tingkatan TPQ berhasil dihapus.');
        }
        redirect('pengaturan/tpq');
    }

    // --- Struktur Diniyyah (Diniyyah Admin dan Webmaster) ---
    public function diniyyah(): void {
        require_login();
        require_role(['diniyyah', 'webmaster']);

        $jenjang = $this->pdo->query(
            "SELECT * FROM diniyyah_jenjang ORDER BY urutan ASC, nama ASC"
        )->fetchAll();
        $kelas = $this->pdo->query(
            "SELECT dk.*, dj.nama AS nama_jenjang
             FROM diniyyah_kelas dk
             JOIN diniyyah_jenjang dj ON dj.id = dk.jenjang_id
             ORDER BY dj.urutan ASC, dj.nama ASC, dk.urutan ASC, dk.nama ASC"
        )->fetchAll();

        $this->view('admin/pengaturan/diniyyah', compact('jenjang', 'kelas'));
    }

    public function diniyyah_simpan(): void {
        require_login();
        require_role(['diniyyah', 'webmaster']);

        if (!$this->is_post() || !verify_csrf()) {
            set_flash('error', 'Permintaan tidak valid.');
            redirect('pengaturan/diniyyah');
        }

        $jenjang_ids = $this->post('jenjang_id', []);
        $jenjang_nama = $this->post('jenjang_nama', []);
        $jenjang_urutan = $this->post('jenjang_urutan', []);
        $jenjang_aktif = $this->post('jenjang_aktif', []);
        $kelas_ids = $this->post('kelas_id', []);
        $kelas_jenjang = $this->post('kelas_jenjang', []);
        $kelas_nama = $this->post('kelas_nama', []);
        $kelas_urutan = $this->post('kelas_urutan', []);
        $kelas_aktif = $this->post('kelas_aktif', []);

        if (!is_array($jenjang_ids) || !is_array($kelas_ids)) {
            set_flash('error', 'Format data tidak valid.');
            redirect('pengaturan/diniyyah');
        }

        $known_level_ids = array_flip(array_map(
            'strval',
            $this->pdo->query("SELECT id FROM diniyyah_jenjang")->fetchAll(PDO::FETCH_COLUMN)
        ));
        $level_names = [];
        $level_map = [];

        try {
            $this->pdo->beginTransaction();
            $insert_level = $this->pdo->prepare(
                'INSERT INTO diniyyah_jenjang (nama, urutan, aktif) VALUES (:nama, :urutan, :aktif)'
            );
            $update_level = $this->pdo->prepare(
                'UPDATE diniyyah_jenjang SET nama=:nama, urutan=:urutan, aktif=:aktif WHERE id=:id'
            );

            foreach ($jenjang_ids as $key) {
                $key = (string)$key;
                $nama = trim((string)($jenjang_nama[$key] ?? ''));
                if ($nama === '') {
                    if (isset($known_level_ids[$key])) {
                        throw new RuntimeException('Nama jenjang Diniyyah wajib diisi.');
                    }
                    continue;
                }
                $normalized_name = strtolower($nama);
                if (isset($level_names[$normalized_name])) {
                    throw new RuntimeException('Nama jenjang Diniyyah tidak boleh duplikat.');
                }
                $level_names[$normalized_name] = true;
                $data = [
                    ':nama' => $nama,
                    ':urutan' => max(0, (int)($jenjang_urutan[$key] ?? 0)),
                    ':aktif' => !empty($jenjang_aktif[$key]) ? 1 : 0,
                ];

                if (isset($known_level_ids[$key])) {
                    $data[':id'] = (int)$key;
                    $update_level->execute($data);
                    $level_map[$key] = (int)$key;
                } elseif (str_starts_with($key, 'new-')) {
                    $insert_level->execute($data);
                    $level_map[$key] = (int)$this->pdo->lastInsertId();
                } else {
                    throw new RuntimeException('Jenjang Diniyyah tidak valid.');
                }
            }

            $insert_class = $this->pdo->prepare(
                'INSERT INTO diniyyah_kelas (jenjang_id, nama, urutan, aktif) VALUES (:jenjang_id, :nama, :urutan, :aktif)'
            );
            $update_class = $this->pdo->prepare(
                'UPDATE diniyyah_kelas SET jenjang_id=:jenjang_id, nama=:nama, urutan=:urutan, aktif=:aktif WHERE id=:id'
            );
            $known_class_ids = array_flip(array_map(
                'strval',
                $this->pdo->query("SELECT id FROM diniyyah_kelas")->fetchAll(PDO::FETCH_COLUMN)
            ));
            $class_names = [];

            foreach ($kelas_ids as $key) {
                $key = (string)$key;
                $nama = trim((string)($kelas_nama[$key] ?? ''));
                if ($nama === '') {
                    if (isset($known_class_ids[$key])) {
                        throw new RuntimeException('Nama kelas Diniyyah wajib diisi.');
                    }
                    continue;
                }
                $level_key = (string)($kelas_jenjang[$key] ?? '');
                if (!isset($level_map[$level_key])) {
                    throw new RuntimeException('Pilih jenjang yang valid untuk setiap kelas Diniyyah.');
                }
                $class_name_key = $level_map[$level_key] . ':' . strtolower($nama);
                if (isset($class_names[$class_name_key])) {
                    throw new RuntimeException('Nama kelas tidak boleh duplikat dalam jenjang yang sama.');
                }
                $class_names[$class_name_key] = true;
                $data = [
                    ':jenjang_id' => $level_map[$level_key],
                    ':nama' => $nama,
                    ':urutan' => max(0, (int)($kelas_urutan[$key] ?? 0)),
                    ':aktif' => !empty($kelas_aktif[$key]) ? 1 : 0,
                ];

                if (isset($known_class_ids[$key])) {
                    $data[':id'] = (int)$key;
                    $update_class->execute($data);
                } elseif (str_starts_with($key, 'new-')) {
                    $insert_class->execute($data);
                } else {
                    throw new RuntimeException('Kelas Diniyyah tidak valid.');
                }
            }

            $this->pdo->commit();
            set_flash('success', 'Struktur jenjang dan kelas Diniyyah berhasil disimpan.');
        } catch (Throwable $error) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            set_flash('error', $error instanceof RuntimeException
                ? $error->getMessage()
                : 'Struktur Diniyyah gagal disimpan. Pastikan nama jenjang dan kelas tidak duplikat.');
        }

        redirect('pengaturan/diniyyah');
    }

    // --- Profil & akun pribadi semua admin ---
    public function profile(): void {
        require_login();

        $stmt = $this->pdo->prepare("SELECT id, nama, username, role, foto, aktif, last_login, created_at FROM users WHERE id=:id LIMIT 1");
        $stmt->execute([':id' => current_user()['id']]);
        $user = $stmt->fetch();

        if (!$user) {
            session_unset();
            session_destroy();
            redirect('login');
        }

        $role_label = [
            'webmaster' => 'Master Admin',
            'kegiatan' => 'Admin Publikasi',
            'diniyyah' => 'Admin Diniyyah',
            'tpq' => 'Admin TPQ',
        ];

        $this->view('admin/pengaturan/profile', compact('user', 'role_label'));
    }

    public function akun(): void {
        require_login();

        $stmt = $this->pdo->prepare("SELECT id, nama, username, role, foto, last_login FROM users WHERE id=:id LIMIT 1");
        $stmt->execute([':id' => current_user()['id']]);
        $akun = $stmt->fetch();

        if (!$akun) {
            session_unset();
            session_destroy();
            redirect('login');
        }

        $this->view('admin/pengaturan/akun', compact('akun'));
    }

    public function akun_simpan(): void {
        require_login();
        if (!$this->is_post() || !verify_csrf()) {
            set_flash('error', 'Permintaan tidak valid.');
            redirect('pengaturan/akun');
        }

        $user_id = (int)current_user()['id'];
        $nama = trim($this->post('nama'));
        $username = trim($this->post('username'));
        $password_lama = $this->post('password_lama');
        $password_baru = $this->post('password_baru');
        $password_konfirmasi = $this->post('password_konfirmasi');

        if (empty($nama) || empty($username) || empty($password_lama)) {
            set_flash('error', 'Nama, username, dan password saat ini wajib diisi.');
            redirect('pengaturan/akun');
        }
        if (!preg_match('/^[A-Za-z0-9._-]{3,50}$/', $username)) {
            set_flash('error', 'Username harus 3-50 karakter dan hanya boleh berisi huruf, angka, titik, garis bawah, atau tanda hubung.');
            redirect('pengaturan/akun');
        }

        $stmt = $this->pdo->prepare("SELECT password FROM users WHERE id=:id LIMIT 1");
        $stmt->execute([':id' => $user_id]);
        $akun = $stmt->fetch();
        if (!$akun || !password_verify($password_lama, $akun['password'])) {
            set_flash('error', 'Password saat ini tidak sesuai.');
            redirect('pengaturan/akun');
        }

        $cek_username = $this->pdo->prepare("SELECT id FROM users WHERE username=:username AND id<>:id LIMIT 1");
        $cek_username->execute([':username' => $username, ':id' => $user_id]);
        if ($cek_username->fetch()) {
            set_flash('error', 'Username tersebut sudah digunakan admin lain.');
            redirect('pengaturan/akun');
        }

        $sets = ['nama=:nama', 'username=:username'];
        $data = [':nama' => $nama, ':username' => $username, ':id' => $user_id];
        if (!empty($password_baru)) {
            if (strlen($password_baru) < 8) {
                set_flash('error', 'Password baru minimal 8 karakter.');
                redirect('pengaturan/akun');
            }
            if ($password_baru !== $password_konfirmasi) {
                set_flash('error', 'Konfirmasi password baru tidak cocok.');
                redirect('pengaturan/akun');
            }
            $sets[] = 'password=:password';
            $data[':password'] = password_hash($password_baru, PASSWORD_BCRYPT, ['cost' => 12]);
        }

        $_SESSION['user_nama'] = $nama;
        $_SESSION['user_username'] = $username;
        set_flash('success', 'Akun berhasil diperbarui.');
        redirect('pengaturan/akun');
    }

    public function akun_hapus(): void {
        require_login();
        if (!$this->is_post() || !verify_csrf()) {
            set_flash('error', 'Permintaan tidak valid.');
            redirect('pengaturan/akun');
        }

        $user_id = (int)current_user()['id'];
        $password = $this->post('password_hapus');
        $stmt = $this->pdo->prepare("SELECT password FROM users WHERE id=:id LIMIT 1");
        $stmt->execute([':id' => $user_id]);
        $akun = $stmt->fetch();
        if (!$akun || !password_verify($password, $akun['password'])) {
            set_flash('error', 'Password tidak sesuai. Akun tidak dihapus.');
            redirect('pengaturan/akun');
        }

        $this->pdo->prepare("DELETE FROM users WHERE id=:id")->execute([':id' => $user_id]);
        session_unset();
        session_destroy();
        session_start();
        session_regenerate_id(true);
        set_flash('success', 'Akun Anda berhasil dihapus.');
        redirect('login');
    }

    // --- Manajemen Users (Webmaster only) ---
    public function users(): void {
        require_login();
        require_role('webmaster');

        $users = $this->pdo->query("SELECT * FROM users WHERE role NOT IN ('user', 'admin_kelas') ORDER BY role ASC, nama ASC")->fetchAll();
        $this->view('admin/pengaturan/users', compact('users'));
    }

    public function user_simpan(): void {
        require_login();
        require_role('webmaster');
        if (!$this->is_post() || !verify_csrf()) { redirect('pengaturan/users'); }

        $id       = $this->post('id');
        $nama     = trim($this->post('nama'));
        $username = trim($this->post('username'));
        $role     = $this->post('role');
        $aktif    = (int)!empty($this->post('aktif'));
        $password = $this->post('password');

        if (empty($nama) || empty($username) || empty($role)) {
            set_flash('error','Data tidak lengkap.'); redirect('pengaturan/users');
        }
        if (!in_array($role, ['webmaster', 'kegiatan', 'diniyyah', 'tpq'], true)) {
            set_flash('error', 'Role admin tidak valid.'); redirect('pengaturan/users');
        }

        if ($id) {
            // Update
            $data = [':nama'=>$nama,':username'=>$username,':role'=>$role,':aktif'=>$aktif,':id'=>$id];
            $sets = ['nama=:nama','username=:username','role=:role','aktif=:aktif'];
            if (!empty($password)) {
                $sets[] = 'password=:password';
                $data[':password'] = password_hash($password, PASSWORD_BCRYPT, ['cost'=>12]);
            }
            $this->pdo->prepare("UPDATE users SET ".implode(',', $sets)." WHERE id=:id")->execute($data);
            set_flash('success','User berhasil diperbarui.');
        } else {
            // Create
            if (empty($password)) { set_flash('error','Password wajib diisi untuk user baru.'); redirect('pengaturan/users'); }
            $this->pdo->prepare("INSERT INTO users (nama,username,password,role,aktif) VALUES (:n,:u,:p,:r,:a)")
                      ->execute([':n'=>$nama,':u'=>$username,':p'=>password_hash($password,PASSWORD_BCRYPT,['cost'=>12]),':r'=>$role,':a'=>$aktif]);
            set_flash('success','User baru berhasil ditambahkan.');
        }
        redirect('pengaturan/users');
    }

    public function user_hapus(?string $id = null): void {
        require_login();
        require_role('webmaster');
        if ($this->is_post() && verify_csrf() && $id) {
            if ($id == current_user()['id']) { set_flash('error','Tidak bisa menghapus akun sendiri.'); redirect('pengaturan/users'); }
            $this->pdo->prepare("DELETE FROM users WHERE id=:id AND role NOT IN ('user', 'admin_kelas')")->execute([':id'=>$id]);
            set_flash('success','User berhasil dihapus.');
        }
        redirect('pengaturan/users');
    }

    // --- Slider ---
    public function slider(): void {
        require_login();
        require_role('webmaster');
        $sliders = $this->pdo->query("SELECT * FROM slider ORDER BY urutan ASC")->fetchAll();
        $this->view('admin/pengaturan/slider', compact('sliders'));
    }

    public function slider_simpan(): void {
        require_login();
        require_role('webmaster');
        if (!$this->is_post() || !verify_csrf()) { redirect('pengaturan/slider'); }

        $judul    = trim($this->post('judul'));
        $deskripsi= trim($this->post('deskripsi'));
        $link     = trim($this->post('link'));
        $urutan   = (int)$this->post('urutan', 0);

        if (empty($_FILES['gambar']['name'])) { set_flash('error','Gambar wajib diupload.'); redirect('pengaturan/slider'); }
        $up = upload_file($_FILES['gambar'], 'slider');
        if (!$up['success']) { set_flash('error',$up['error']); redirect('pengaturan/slider'); }

        $this->pdo->prepare("INSERT INTO slider (judul,deskripsi,gambar,link,urutan) VALUES (:j,:d,:g,:l,:u)")
                  ->execute([':j'=>$judul,':d'=>$deskripsi,':g'=>$up['path'],':l'=>$link,':u'=>$urutan]);
        set_flash('success','Slider berhasil ditambahkan.');
        redirect('pengaturan/slider');
    }

    public function slider_hapus(?string $id = null): void {
        require_login();
        require_role('webmaster');
        if ($this->is_post() && verify_csrf() && $id) {
            $s = $this->pdo->prepare("SELECT gambar FROM slider WHERE id=:id");
            $s->execute([':id'=>$id]);
            $slider = $s->fetch();
            if ($slider) delete_file($slider['gambar']);
            $this->pdo->prepare("DELETE FROM slider WHERE id=:id")->execute([':id'=>$id]);
            set_flash('success','Slider berhasil dihapus.');
        }
        redirect('pengaturan/slider');
    }
}
