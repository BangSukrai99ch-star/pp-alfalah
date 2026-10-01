<?php
// ============================================================
// GENERAL HELPER FUNCTIONS
// ============================================================

/**
 * Generate URL slug dari string
 */
function make_slug(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-') . '-' . substr(uniqid(), -5);
}

/**
 * Format tanggal Indonesia
 */
function format_tanggal(string $date, string $format = 'd F Y'): string {
    if (empty($date)) return '-';
    $bulan = [
        1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',
        7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
    ];
    $d = date_create($date);
    if (!$d) return '-';
    $result = date_format($d, $format);
    foreach ($bulan as $num => $nama) {
        $result = str_replace(date('F', mktime(0,0,0,$num,1)), $nama, $result);
    }
    return $result;
}

/**
 * Sanitize string input (XSS prevention)
 */
function sanitize(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verify_csrf(): bool {
    $token = $_POST['csrf_token'] ?? '';
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Upload file dengan validasi
 * @return array ['success'=>bool, 'path'=>string, 'error'=>string]
 */
function upload_file(array $file, string $subfolder = 'general', array $allowed_types = []): array {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'File upload error: ' . $file['error']];
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    $allowed = !empty($allowed_types) ? $allowed_types : ALLOWED_IMAGE_TYPES;
    if (!in_array($mime, $allowed)) {
        return ['success' => false, 'error' => 'Tipe file tidak diizinkan.'];
    }
    // Batas 5 MB hanya berlaku untuk gambar. Video konten mengikuti batas server PHP.
    $is_video = str_starts_with($mime, 'video/');
    if (!$is_video && $file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => 'Gambar masih lebih besar dari 5MB. Kompres gambar sebelum mengunggah.'];
    }
    $ext       = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename  = uniqid('', true) . '.' . strtolower($ext);
    $dest_dir  = UPLOAD_PATH . $subfolder . '/';
    if (!is_dir($dest_dir)) {
        mkdir($dest_dir, 0755, true);
    }
    $dest_path = $dest_dir . $filename;
    if (!move_uploaded_file($file['tmp_name'], $dest_path)) {
        return ['success' => false, 'error' => 'Gagal menyimpan file.'];
    }
    return ['success' => true, 'path' => $subfolder . '/' . $filename, 'filename' => $filename];
}

function is_video_file(?string $path): bool {
    if (!$path) return false;
    return in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['mp4', 'webm', 'ogg', 'ogv', 'mov', 'avi', 'mkv'], true);
}

/**
 * Hapus file dari uploads
 */
function delete_file(?string $path): void {
    if ($path && file_exists(UPLOAD_PATH . $path)) {
        unlink(UPLOAD_PATH . $path);
    }
}

/**
 * Pagination helper
 */
function paginate(int $total, int $page, int $per_page = ITEMS_PER_PAGE): array {
    $total_pages = max(1, ceil($total / $per_page));
    $page        = max(1, min($page, $total_pages));
    $offset      = ($page - 1) * $per_page;
    return [
        'total'       => $total,
        'per_page'    => $per_page,
        'page'        => $page,
        'total_pages' => $total_pages,
        'offset'      => $offset,
        'has_prev'    => $page > 1,
        'has_next'    => $page < $total_pages,
    ];
}

/**
 * Redirect helper
 */
function redirect(string $path): never {
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}

/**
 * Escape HTML output
 */
function e(mixed $val): string {
    return htmlspecialchars((string)($val ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Truncate text
 */
function truncate(string $text, int $length = 150): string {
    $text = strip_tags($text);
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '...';
}

/**
 * Badge HTML untuk status pendaftaran
 */
function status_badge(string $status): string {
    $map = [
        'pending'  => ['warning', 'Menunggu'],
        'diterima' => ['success', 'Diterima'],
        'ditolak'  => ['danger',  'Ditolak'],
        'lulus'    => ['success', 'Lulus'],
        'tidak_lulus' => ['danger', 'Tidak Lulus'],
        'ditunda'  => ['warning', 'Ditunda'],
        'aktif'    => ['success', 'Aktif'],
        'alumni'   => ['info',    'Alumni'],
        'keluar'   => ['secondary','Keluar'],
    ];
    [$color, $label] = $map[$status] ?? ['secondary', $status];
    return "<span class=\"badge bg-{$color}\">{$label}</span>";
}

/**
 * Get pengaturan web dari database (cached)
 */
function get_pengaturan(PDO $pdo): array {
    static $settings = null;
    if ($settings === null) {
        $stmt = $pdo->query("SELECT * FROM pengaturan_web LIMIT 1");
        $settings = $stmt->fetch() ?: [];
    }
    return $settings;
}

/**
 * Render view file dengan data
 */
function render_view(string $view_path, array $data = []): void {
    extract($data);
    require ROOT_PATH . '/views/' . $view_path . '.php';
}

/**
 * Export data ke file Excel (CSV UTF-8 dengan pemisah titik-koma
 * agar terbaca rapi oleh Microsoft Excel versi Indonesia).
 * Menghentikan eksekusi setelah file dikirim ke browser.
 */
function export_excel(string $filename, array $headers, array $rows): never {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8 agar karakter Indonesia tampil benar di Excel
    fputcsv($out, $headers, ';');
    foreach ($rows as $row) {
        fputcsv($out, $row, ';');
    }
    fclose($out);
    exit;
}

/**
 * Konversi angka bulan (1-12) menjadi angka romawi untuk penomoran surat.
 */
function bulan_romawi(int $bulan): string {
    $map = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
    return $map[max(0, min(11, $bulan - 1))];
}

/**
 * Generate nomor surat resmi pesantren, contoh: 007/PSB/PSAF/X/2025
 */
function nomor_surat(string $kode, int $id, ?string $tanggal = null): string {
    $ts    = $tanggal ? strtotime($tanggal) : time();
    $ts    = $ts ?: time();
    $bulan = bulan_romawi((int)date('n', $ts));
    $tahun = date('Y', $ts);
    return sprintf('%03d/%s/PSAF/%s/%s', $id, $kode, $bulan, $tahun);
}
