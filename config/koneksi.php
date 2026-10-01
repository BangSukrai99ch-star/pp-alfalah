<?php
// ============================================================
// KONEKSI DATABASE - PDO
// ============================================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'db_pp_alfalah');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

function ensure_database_ready(PDO $pdo): void {
    $dbName = DB_NAME;
    $exists = $pdo->query(
        "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = " . $pdo->quote($dbName)
    )->fetchColumn();

    if (!$exists) {
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . str_replace('`', '', $dbName) . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }
}

function import_schema_if_needed(PDO $pdo): void {
    $schemaPath = dirname(__DIR__) . '/database/schema.sql';
    if (!is_file($schemaPath)) {
        return;
    }

    $hasUsersTable = $pdo->query(
        "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users'"
    )->fetchColumn();

    if ((int)$hasUsersTable > 0) {
        return;
    }

    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS);
    if ($mysqli->connect_errno) {
        return;
    }

    $dbName = str_replace('`', '', DB_NAME);
    $mysqli->query("CREATE DATABASE IF NOT EXISTS `" . $dbName . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $mysqli->select_db($dbName);

    $sql = @file_get_contents($schemaPath);
    if ($sql === false || trim($sql) === '') {
        $mysqli->close();
        return;
    }

    if ($mysqli->multi_query($sql)) {
        do {
            if ($result = $mysqli->store_result()) {
                $result->free();
            }
        } while ($mysqli->more_results() && $mysqli->next_result());
    }

    $mysqli->close();
}

try {
    $baseOptions = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
    ];

    $baseDsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
    $pdo = new PDO($baseDsn, DB_USER, DB_PASS, $baseOptions);

    ensure_database_ready($pdo);

    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $baseOptions);

    import_schema_if_needed($pdo);
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode([
        'error' => true,
        'message' => 'Database connection failed. Please contact administrator.'
    ]));
}
