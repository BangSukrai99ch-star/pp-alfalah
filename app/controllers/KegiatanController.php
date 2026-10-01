<?php
require_once ROOT_PATH . '/app/controllers/BaseController.php';

// ============================================================
// KEGIATAN CONTROLLER (Berita, Artikel, Galeri)
// ============================================================
class KegiatanController extends BaseController {

    public function index(): void {
        require_login();
        require_role(['kegiatan', 'webmaster']);

        $tipe   = $this->get('tipe', '');
        $status = $this->get('status', '');
        $search = trim($this->get('q', ''));
        $page   = (int)$this->get('page', 1);

        $params = [];
        $where  = ['1=1'];
        if (!empty($tipe))   { $where[] = 'k.tipe = :tipe';     $params[':tipe']   = $tipe; }
        if (!empty($status)) { $where[] = 'k.status = :status'; $params[':status'] = $status; }
        if (!empty($search)) { $where[] = 'k.judul LIKE :q';    $params[':q']      = "%$search%"; }

        $where_sql = implode(' AND ', $where);
        $cnt = $this->pdo->prepare("SELECT COUNT(*) FROM kegiatan k WHERE $where_sql");
        $cnt->execute($params);
        $pg = paginate((int)$cnt->fetchColumn(), $page);

        $stmt = $this->pdo->prepare("
            SELECT k.*, u.nama as penulis FROM kegiatan k
            LEFT JOIN users u ON k.dibuat_oleh = u.id
            WHERE $where_sql ORDER BY k.created_at DESC LIMIT :lim OFFSET :off
        ");
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':lim', $pg['per_page'], PDO::PARAM_INT);
        $stmt->bindValue(':off', $pg['offset'],   PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();

        $this->view('admin/kegiatan/index', compact('items', 'pg', 'tipe', 'status', 'search'));
    }

    public function tambah(): void {
        require_login();
        require_role(['kegiatan', 'webmaster']);

        if ($this->is_post()) {
            if (!verify_csrf()) { set_flash('error','Token tidak valid.'); redirect('kegiatan/tambah'); }
            $this->simpan_kegiatan(null);
            return;
        }
        $item = null;
        $this->view('admin/kegiatan/form', compact('item'));
    }

    public function edit(?string $id = null): void {
        require_login();
        require_role(['kegiatan', 'webmaster']);
        if (!$id) { redirect('kegiatan'); }

        $item = $this->find($id);
        if (!$item) { redirect('kegiatan'); }

        if ($this->is_post()) {
            if (!verify_csrf()) { set_flash('error','Token tidak valid.'); $this->view('admin/kegiatan/form', compact('item')); return; }
            $this->simpan_kegiatan($id, $item);
            return;
        }
        $this->view('admin/kegiatan/form', compact('item'));
    }

    public function delete(?string $id = null): void {
        require_login();
        require_role(['kegiatan', 'webmaster']);
        if (!$this->is_post() || !verify_csrf() || !$id) { redirect('kegiatan'); }

        $item = $this->find($id);
        if ($item) {
            delete_file($item['gambar']);
            // Hapus galeri_foto jika ada
            if ($item['galeri_foto']) {
                $fotos = json_decode($item['galeri_foto'], true) ?? [];
                foreach ($fotos as $f) delete_file($f);
            }
            $this->pdo->prepare("DELETE FROM kegiatan WHERE id=:id")->execute([':id'=>$id]);
            set_flash('success', 'Konten berhasil dihapus.');
        }
        redirect('kegiatan');
    }

    public function toggle_status(?string $id = null): void {
        require_login();
        require_role(['kegiatan', 'webmaster']);
        if (!$id) { redirect('kegiatan'); }

        $item = $this->find($id);
        if ($item) {
            $new_status = ($item['status'] === 'publish') ? 'draft' : 'publish';
            $this->pdo->prepare("UPDATE kegiatan SET status=:s WHERE id=:id")
                      ->execute([':s'=>$new_status,':id'=>$id]);
            set_flash('success', "Status diubah ke: {$new_status}.");
        }
        redirect('kegiatan');
    }

    private function simpan_kegiatan(?string $id, ?array $old = null): void {
        $judul    = trim($this->post('judul'));
        $tipe     = $this->post('tipe');
        $konten   = $this->post('konten');
        $tags     = trim($this->post('tags'));
        $status   = $this->post('status', 'draft');
        $slug     = make_slug($judul);

        if (empty($judul)) { set_flash('error','Judul wajib diisi.'); redirect($id ? "kegiatan/edit/$id" : 'kegiatan/tambah'); }

        $gambar_path = $old['gambar'] ?? null;
        if (!empty($_FILES['gambar']['name'])) {
            $up = upload_file($_FILES['gambar'], 'kegiatan', array_merge(ALLOWED_IMAGE_TYPES, ALLOWED_VIDEO_TYPES));
            if (!$up['success']) { set_flash('error', $up['error']); redirect($id ? "kegiatan/edit/$id" : 'kegiatan/tambah'); }
            if ($old) delete_file($old['gambar']);
            $gambar_path = $up['path'];
        }

        // Multi-upload untuk galeri
        $galeri_paths = $old ? (json_decode($old['galeri_foto'] ?? '[]', true) ?? []) : [];
        if (!empty($_FILES['galeri_foto']['name'][0])) {
            $files = $_FILES['galeri_foto'];
            foreach (array_keys($files['name']) as $i) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $single = ['name'=>$files['name'][$i],'tmp_name'=>$files['tmp_name'][$i],
                               'size'=>$files['size'][$i],'error'=>$files['error'][$i],'type'=>$files['type'][$i]];
                    $up = upload_file($single, 'galeri', ALLOWED_IMAGE_TYPES);
                    if ($up['success']) $galeri_paths[] = $up['path'];
                }
            }
        }

        if ($id) {
            $this->pdo->prepare("
                UPDATE kegiatan SET judul=:j, tipe=:t, konten=:k, gambar=:g,
                galeri_foto=:gf, tags=:ta, status=:s, slug=:sl WHERE id=:id
            ")->execute([':j'=>$judul,':t'=>$tipe,':k'=>$konten,':g'=>$gambar_path,
                         ':gf'=>json_encode($galeri_paths),':ta'=>$tags,':s'=>$status,':sl'=>$slug,':id'=>$id]);
            set_flash('success','Konten berhasil diperbarui.');
        } else {
            $this->pdo->prepare("
                INSERT INTO kegiatan (judul, tipe, slug, konten, gambar, galeri_foto, tags, status, dibuat_oleh)
                VALUES (:j,:t,:sl,:k,:g,:gf,:ta,:s,:uid)
            ")->execute([':j'=>$judul,':t'=>$tipe,':sl'=>$slug,':k'=>$konten,':g'=>$gambar_path,
                         ':gf'=>json_encode($galeri_paths),':ta'=>$tags,':s'=>$status,':uid'=>current_user()['id']]);
            set_flash('success','Konten berhasil ditambahkan.');
        }
        redirect('kegiatan');
    }

    private function find(string $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM kegiatan WHERE id=:id LIMIT 1");
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch() ?: null;
    }
}
