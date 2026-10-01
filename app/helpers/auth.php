<?php
// ============================================================
// AUTH HELPER - Session Management & RBAC
// ============================================================

/**
 * Cek apakah user sudah login
 */
function is_logged_in(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Redirect jika belum login
 */
function require_login(): void {
    if (!is_logged_in()) {
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}

/**
 * Cek apakah user memiliki role tertentu
 */
function has_role(string|array $roles): bool {
    if (!is_logged_in()) return false;
    $user_role = $_SESSION['user_role'] ?? '';
    if ($user_role === 'master_admin') {
        $user_role = 'webmaster';
    }
    if ($user_role === 'admin_publikasi') {
        $user_role = 'kegiatan';
    }
    if ($user_role === 'admin_awaliyah' || $user_role === 'admin_wustho') {
        $user_role = 'diniyyah';
    }
    if ($user_role === 'admin_tpq') {
        $user_role = 'tpq';
    }
    if (is_array($roles)) {
        $roles = array_map(static function ($role) {
            return match ($role) {
                'master_admin' => 'webmaster',
                'admin_publikasi' => 'kegiatan',
                'admin_awaliyah', 'admin_wustho' => 'diniyyah',
                'admin_tpq' => 'tpq',
                default => $role,
            };
        }, $roles);
        return in_array($user_role, $roles);
    }
    return $user_role === match ($roles) {
        'master_admin' => 'webmaster',
        'admin_publikasi' => 'kegiatan',
        'admin_awaliyah', 'admin_wustho' => 'diniyyah',
        'admin_tpq' => 'tpq',
        default => $roles,
    };
}

/**
 * Require specific role, redirect if not authorized
 */
function require_role(string|array $roles): void {
    require_login();
    if (!has_role($roles)) {
        $_SESSION['flash_error'] = 'Anda tidak memiliki akses ke halaman ini.';
        header('Location: ' . BASE_URL . '/admin');
        exit;
    }
}

/**
 * Set flash message
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash_' . $type] = $message;
}

/**
 * Get & clear flash message
 */
function get_flash(string $type): ?string {
    if (isset($_SESSION['flash_' . $type])) {
        $msg = $_SESSION['flash_' . $type];
        unset($_SESSION['flash_' . $type]);
        return $msg;
    }
    return null;
}

/**
 * Get logged-in user data from session
 */
function current_user(): array {
    $role = $_SESSION['user_role'] ?? '';
    $role = match ($role) {
        'master_admin' => 'webmaster',
        'admin_publikasi' => 'kegiatan',
        'admin_awaliyah', 'admin_wustho' => 'diniyyah',
        'admin_tpq' => 'tpq',
        default => $role,
    };
    return [
        'id'       => $_SESSION['user_id']   ?? null,
        'nama'     => $_SESSION['user_nama']  ?? '',
        'username' => $_SESSION['user_username'] ?? '',
        'role'     => $role,
        'foto'     => $_SESSION['user_foto']  ?? null,
    ];
}

/**
 * Jenjang yang boleh diakses berdasarkan role
 */
function get_allowed_jenjang(): string {
    $role = $_SESSION['user_role'] ?? '';
    return ROLE_JENJANG_MAP[$role] ?? '';
}
