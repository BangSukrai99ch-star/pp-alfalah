<?php
// ============================================================
// KONFIGURASI APLIKASI GLOBAL
// ============================================================

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Base URL mengikuti folder aplikasi agar tetap benar saat dipindahkan.
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
$basePath = $basePath === '/' ? '' : implode('/', array_map('rawurlencode', explode('/', $basePath)));
$baseUrl = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $basePath;
define('BASE_URL', $baseUrl);
define('BASE_PATH', dirname(__DIR__));
define('UPLOAD_PATH', BASE_PATH . '/public/uploads/');
define('UPLOAD_URL', BASE_URL . '/public/uploads/');

// Upload limits
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);
define('ALLOWED_VIDEO_TYPES', ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime', 'video/x-msvideo', 'video/x-matroska']);
define('ALLOWED_DOC_TYPES', ['application/pdf', 'image/jpeg', 'image/png']);

// Pagination
define('ITEMS_PER_PAGE', 15);

// Session name
define('SESSION_NAME', 'pesantren_session');

// RBAC: Role -> allowed jenjang untuk filter pendaftaran
define('ROLE_JENJANG_MAP', [
    'admin_awaliyah' => 'diniyyah',
    'admin_wustho'   => 'diniyyah',
    'diniyyah'       => 'diniyyah',
    'admin_tpq'      => 'tpq',
    'tpq'            => 'tpq',
]);
