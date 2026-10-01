<?php
require_once ROOT_PATH . '/app/controllers/BaseController.php';

class KelulusanController extends BaseController {

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
    }

    public function index(): void {
        require_login();
        require_role(['diniyyah', 'tpq', 'webmaster']);

        $role = current_user()['role'];
        $jenjang_scope = $role === 'webmaster' ? null : ROLE_JENJANG_MAP[$role];
        $search = trim($this->get('q', ''));
        $status = $this->get('status', '');
        $tahun = trim($this->get('tahun', ''));
        $page = (int)$this->get('page', 1);

        $params = [];
        $where = ['1=1'];
        if ($jenjang_scope) {
            $where[] = 'k.jenis_jenjang = :jenis_jenjang';
            $params[':jenis_jenjang'] = $jenjang_scope;
        }
        if ($search !== '') {
            $where[] = '(s.nama_lengkap LIKE :q1 OR s.nis_pesantren LIKE :q2 OR k.jenjang LIKE :q3)';
            $params[':q1'] = $params[':q2'] = $params[':q3'] = '%' . $search . '%';
        }
        if ($status !== '') {
            $where[] = 'k.status = :status';
            $params[':status'] = $status;
        }
        if ($tahun !== '') {
            $where[] = 'k.tahun_ajaran = :tahun';
            $params[':tahun'] = $tahun;
        }
        $where_sql = implode(' AND ', $where);

        $count = $this->pdo->prepare("SELECT COUNT(*) FROM kelulusan k JOIN santri s ON s.id=k.santri_id WHERE {$where_sql}");
        $count->execute($params);
        $pg = paginate((int)$count->fetchColumn(), $page);

        $stmt = $this->pdo->prepare("SELECT k.*, s.nama_lengkap, s.nis_pesantren, u.nama AS penetap_nama
            FROM kelulusan k
            JOIN santri s ON s.id=k.santri_id
            LEFT JOIN users u ON u.id=k.ditetapkan_oleh
            WHERE {$where_sql}
            ORDER BY k.tahun_ajaran DESC, k.created_at DESC
            LIMIT :limit OFFSET :offset");
        foreach ($params as $key => $value) $stmt->bindValue($key, $value);
        $stmt->bindValue(':limit', $pg['per_page'], PDO::PARAM_INT);
        $stmt->bindValue(':offset', $pg['offset'], PDO::PARAM_INT);
        $stmt->execute();
        $kelulusan = $stmt->fetchAll();

        $this->view('admin/kelulusan/index', compact(
            'kelulusan', 'pg', 'search', 'status', 'tahun', 'role', 'jenjang_scope'
        ));
    }

    public function export(): void {
        require_login();
        require_role(['diniyyah', 'tpq', 'webmaster']);

        $role = current_user()['role'];
        $jenjang_scope = $role === 'webmaster' ? null : ROLE_JENJANG_MAP[$role];
        $search = trim($this->get('q', ''));
        $status = $this->get('status', '');
        $tahun = trim($this->get('tahun', ''));

        $params = [];
        $where = ['1=1'];
        if ($jenjang_scope) {
            $where[] = 'k.jenis_jenjang = :jenis_jenjang';
            $params[':jenis_jenjang'] = $jenjang_scope;
        }
        if ($search !== '') {
            $where[] = '(s.nama_lengkap LIKE :q1 OR s.nis_pesantren LIKE :q2 OR k.jenjang LIKE :q3)';
            $params[':q1'] = $params[':q2'] = $params[':q3'] = '%' . $search . '%';
        }
        if ($status !== '') {
            $where[] = 'k.status = :status';
            $params[':status'] = $status;
        }
        if ($tahun !== '') {
            $where[] = 'k.tahun_ajaran = :tahun';
            $params[':tahun'] = $tahun;
        }
        $where_sql = implode(' AND ', $where);

        $stmt = $this->pdo->prepare("SELECT k.*, s.nama_lengkap, s.nis_pesantren, u.nama AS penetap_nama
            FROM kelulusan k
            JOIN santri s ON s.id=k.santri_id
            LEFT JOIN users u ON u.id=k.ditetapkan_oleh
            WHERE {$where_sql}
            ORDER BY k.tahun_ajaran DESC, k.created_at DESC");
        $stmt->execute($params);
        $data = $stmt->fetchAll();

        $status_labels = ['lulus' => 'Lulus', 'tidak_lulus' => 'Tidak Lulus', 'ditunda' => 'Ditunda'];
        $headers = [
            'No', 'Nama Santri', 'NIS Pesantren', 'Program', 'Jenjang', 'Tahun Ajaran',
            'Tanggal Lulus', 'Nomor Ijazah', 'Predikat', 'Status', 'Catatan',
            'Ditetapkan Oleh', 'Tanggal Dicatat',
        ];
        $rows = [];
        foreach ($data as $i => $row) {
            $rows[] = [
                $i + 1,
                $row['nama_lengkap'],
                $row['nis_pesantren'],
                ucfirst($row['jenis_jenjang']),
                $row['jenjang'],
                $row['tahun_ajaran'],
                $row['tanggal_lulus'],
                $row['nomor_ijazah'],
                $row['predikat'],
                $status_labels[$row['status']] ?? $row['status'],
                $row['catatan'],
                $row['penetap_nama'],
                $row['created_at'],
            ];
        }

        $nama_file = 'kelulusan_' . ($jenjang_scope ?: 'semua') . '_' . date('Ymd_His') . '.xls';
        export_excel($nama_file, $headers, $rows);
    }

    public function tambah(): void {
        require_login();
        require_role(['diniyyah', 'tpq']);

        $role = current_user()['role'];
        $jenjang_scope = ROLE_JENJANG_MAP[$role];
        $santri_list = $this->eligible_santri($jenjang_scope);
        $options = $this->jenjang_options($jenjang_scope);
        $item = null;
        $selected_santri_id = (int)$this->get('santri_id', 0);
        $this->view('admin/kelulusan/form', compact('santri_list', 'options', 'item', 'jenjang_scope', 'selected_santri_id'));
    }

    public function edit(?string $id = null): void {
        require_login();
        require_role(['diniyyah', 'tpq']);
        if (!$id) redirect('kelulusan');

        $role = current_user()['role'];
        $jenjang_scope = ROLE_JENJANG_MAP[$role];
        $stmt = $this->pdo->prepare('SELECT * FROM kelulusan WHERE id=:id AND jenis_jenjang=:jenis LIMIT 1');
        $stmt->execute([':id' => $id, ':jenis' => $jenjang_scope]);
        $item = $stmt->fetch();
        if (!$item) {
            set_flash('error', 'Data kelulusan tidak ditemukan atau bukan milik jenjang Anda.');
            redirect('kelulusan');
        }

        $santri_list = $this->eligible_santri($jenjang_scope);
        $options = $this->jenjang_options($jenjang_scope);
        $selected_santri_id = (int)$item['santri_id'];
        $this->view('admin/kelulusan/form', compact('santri_list', 'options', 'item', 'jenjang_scope', 'selected_santri_id'));
    }

    public function simpan(?string $id = null): void {
        require_login();
        require_role(['diniyyah', 'tpq']);
        if (!$this->is_post() || !verify_csrf()) {
            set_flash('error', 'Permintaan tidak valid.');
            redirect('kelulusan');
        }

        $role = current_user()['role'];
        $jenis = ROLE_JENJANG_MAP[$role];
        $santri_id = (int)$this->post('santri_id');
        $jenjang = trim($this->post('jenjang'));
        $tahun_ajaran = trim($this->post('tahun_ajaran'));
        $tanggal_lulus = $this->post('tanggal_lulus') ?: null;
        $nomor_ijazah = trim($this->post('nomor_ijazah')) ?: null;
        $predikat = trim($this->post('predikat')) ?: null;
        $status = $this->post('status', 'lulus');
        $catatan = trim($this->post('catatan')) ?: null;

        $options = $this->jenjang_options($jenis);
        if (!$santri_id || !in_array($jenjang, $options, true) || $tahun_ajaran === '') {
            set_flash('error', 'Santri, jenjang, dan tahun ajaran wajib diisi dengan benar.');
            redirect($id ? 'kelulusan/edit/' . $id : 'kelulusan/tambah');
        }
        if (!in_array($status, ['lulus', 'tidak_lulus', 'ditunda'], true)) {
            set_flash('error', 'Status kelulusan tidak valid.');
            redirect($id ? 'kelulusan/edit/' . $id : 'kelulusan/tambah');
        }
        if (!$this->santri_in_scope($santri_id, $jenis)) {
            set_flash('error', 'Santri tidak berada dalam jenjang yang dapat Anda kelola.');
            redirect($id ? 'kelulusan/edit/' . $id : 'kelulusan/tambah');
        }

        $data = [
            ':santri_id' => $santri_id,
            ':jenis_jenjang' => $jenis,
            ':jenjang' => $jenjang,
            ':tahun_ajaran' => $tahun_ajaran,
            ':tanggal_lulus' => $tanggal_lulus,
            ':nomor_ijazah' => $nomor_ijazah,
            ':predikat' => $predikat,
            ':status' => $status,
            ':catatan' => $catatan,
            ':ditetapkan_oleh' => current_user()['id'],
        ];

        try {
            if ($id) {
                $data[':id'] = $id;
                $stmt = $this->pdo->prepare('UPDATE kelulusan SET santri_id=:santri_id, jenjang=:jenjang, tahun_ajaran=:tahun_ajaran, tanggal_lulus=:tanggal_lulus, nomor_ijazah=:nomor_ijazah, predikat=:predikat, status=:status, catatan=:catatan, ditetapkan_oleh=:ditetapkan_oleh WHERE id=:id AND jenis_jenjang=:jenis_jenjang');
                $stmt->execute($data);
                set_flash('success', 'Data kelulusan berhasil diperbarui.');
            } else {
                $stmt = $this->pdo->prepare('INSERT INTO kelulusan (santri_id, jenis_jenjang, jenjang, tahun_ajaran, tanggal_lulus, nomor_ijazah, predikat, status, catatan, ditetapkan_oleh) VALUES (:santri_id, :jenis_jenjang, :jenjang, :tahun_ajaran, :tanggal_lulus, :nomor_ijazah, :predikat, :status, :catatan, :ditetapkan_oleh)');
                $stmt->execute($data);
                set_flash('success', 'Data kelulusan berhasil ditambahkan.');
            }
        } catch (PDOException $error) {
            if ((int)$error->errorInfo[1] === 1062) {
                set_flash('error', 'Data kelulusan untuk santri, jenjang, dan tahun ajaran tersebut sudah ada.');
            } else {
                set_flash('error', 'Data kelulusan gagal disimpan.');
            }
        }
        redirect('kelulusan');
    }

    public function hapus(?string $id = null): void {
        require_login();
        require_role(['diniyyah', 'tpq']);
        if (!$this->is_post() || !verify_csrf() || !$id) redirect('kelulusan');

        $stmt = $this->pdo->prepare('DELETE FROM kelulusan WHERE id=:id AND jenis_jenjang=:jenis');
        $stmt->execute([':id' => $id, ':jenis' => ROLE_JENJANG_MAP[current_user()['role']]]);
        set_flash('success', 'Data kelulusan berhasil dihapus.');
        redirect('kelulusan');
    }

    private function jenjang_options(string $jenis): array {
        if ($jenis === 'diniyyah') {
            return array_column(
                $this->pdo->query("SELECT nama FROM diniyyah_jenjang WHERE aktif=1 ORDER BY urutan ASC, nama ASC")->fetchAll(),
                'nama'
            );
        }
        return array_column($this->pdo->query("SELECT nama FROM tingkatan_tpq WHERE aktif=1 ORDER BY urutan ASC")->fetchAll(), 'nama');
    }

    private function eligible_santri(string $jenis): array {
        $stmt = $this->pdo->prepare("SELECT DISTINCT s.id, s.nama_lengkap, s.nis_pesantren
            FROM santri s JOIN pendaftaran_jenjang pj ON pj.santri_id=s.id
            WHERE pj.jenis_jenjang=:jenis AND pj.status='diterima'
            ORDER BY s.nama_lengkap ASC");
        $stmt->execute([':jenis' => $jenis]);
        return $stmt->fetchAll();
    }

    private function santri_in_scope(int $santri_id, string $jenis): bool {
        $stmt = $this->pdo->prepare("SELECT 1 FROM pendaftaran_jenjang WHERE santri_id=:santri_id AND jenis_jenjang=:jenis AND status='diterima' LIMIT 1");
        $stmt->execute([':santri_id' => $santri_id, ':jenis' => $jenis]);
        return (bool)$stmt->fetchColumn();
    }
}
