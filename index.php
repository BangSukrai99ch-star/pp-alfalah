<?php
// ============================================================
// FRONT CONTROLLER / ROUTER
// ============================================================

define('ROOT_PATH', __DIR__);
require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/config/koneksi.php';
require_once ROOT_PATH . '/app/helpers/auth.php';
require_once ROOT_PATH . '/app/helpers/functions.php';

// Start secure session
session_name(SESSION_NAME);
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'secure'   => false, // Set true di production dengan HTTPS
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// Parse URL
$request_uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$script_name = rawurldecode(dirname($_SERVER['SCRIPT_NAME']));
$path = str_replace($script_name, '', $request_uri);
$path = ltrim($path, '/');
$segments = explode('/', $path);

$controller_name = !empty($segments[0]) ? $segments[0] : 'public';
$action          = !empty($segments[1]) ? $segments[1] : 'index';
$param1          = $segments[2] ?? null;
$param2          = $segments[3] ?? null;

// Route mapping
$routes = [
    ''          => ['PublicController', 'home'],
    'public'    => ['PublicController', 'home'],
    'home'      => ['PublicController', 'home'],
    'berita'    => ['PublicController', 'berita'],
    'galeri'    => ['PublicController', 'galeri'],
    'profil'    => ['PublicController', 'profil'],
    'kontak'    => ['PublicController', 'kontak'],
    'daftar'    => ['PublicController', 'pendaftaran'],
    'login'     => ['AuthController', 'login'],
    'register'  => ['AuthController', 'register'],
    'logout'    => ['AuthController', 'logout'],
    'admin'     => ['DashboardController', $action],
    'dashboard' => ['DashboardController', $action],
    'santri'    => ['SantriController', $action],
    'kelulusan' => ['KelulusanController', $action],
    'kenaikan'  => ['KenaikanController', $action],
    'surat'     => ['SuratController', $action],
    'admin-kelas' => ['AdminKelasController', $action],
    'pendaftaran' => ['PendaftaranController', $action],
    'kegiatan'  => ['KegiatanController', $action],
    'pengaturan' => ['PengaturanController', $action],
    'profile'   => ['PengaturanController', 'profile'],
    'users'     => ['PengaturanController', $action],
];

// Resolve controller
if (isset($routes[$controller_name])) {
    [$ctrl_class, $ctrl_action] = $routes[$controller_name];
} else {
    [$ctrl_class, $ctrl_action] = ['PublicController', 'error404'];
}

// Load and dispatch
$controller_file = ROOT_PATH . '/app/controllers/' . $ctrl_class . '.php';
if (file_exists($controller_file)) {
    require_once $controller_file;
    $controller = new $ctrl_class($pdo);
    $method = sanitize_method($ctrl_action);
    if (method_exists($controller, $method)) {
        $controller->$method($param1, $param2);
    } else {
        require_once ROOT_PATH . '/app/controllers/PublicController.php';
        $c = new PublicController($pdo);
        $c->error404();
    }
} else {
    require_once ROOT_PATH . '/app/controllers/PublicController.php';
    $c = new PublicController($pdo);
    $c->error404();
}

function sanitize_method(string $method): string
{
    return preg_replace('/[^a-zA-Z0-9_]/', '', $method);
}
